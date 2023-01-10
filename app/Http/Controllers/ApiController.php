<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Validate;

use App\ApiSetup;
use App\ApiSetupBillForm;
use App\Http\Requests;
use App\ApiSaveReport;

use DB;

class ApiController extends Controller
{
    //
    public function StoreApiStatus(Request $request){

        $apisave = new ApiSaveReport;
        $apisave->status=$request->Status;
        $apisave->sched=$request->Sched;
        $apisave->unitno=$request->Unitno;
        $apisave->leasecode=$request->Leasecode;
        $apisave->totalTransaction=$request->TotalTransaction;
        $apisave->total=$request->Total;
        $apisave->remarks=$request->Remarks;
        $apisave->apisetup_id=$request->ApiSetupId;
        $apisave->save();
        return \Response::json($apisave);
    }
   	public function GetApiTypeInfo($id){
        $apisetups = DB::select('select *,Day(startdate) as dd from api_setups where enddate >= CURRENT_DATE() and id ='.$id);
        // $apisetups = ApiSetup::where('id','=',$id)->get();
        return \Response::json($apisetups);
    }
    public function GetApiMonthyTypeInfo($id){
        $apisetups = DB::select('select *,Day(startdate) as dd from api_setups where enddate >= CURRENT_DATE() and id ='.$id);
        // $apisetups = ApiSetup::where('id','=',$id)->get();
        return \Response::json($apisetups);
    }
    public function BillTypeInfo($id){
        $BillForm=DB::connection('sqlsrv')->select("select ROW_NUMBER() Over (Order by GUID) As Num, Type,GUID,BillType,Name,LatinName,
                                case when bIsInput = 0 then
                                    'Outgoing'
                                    else
                                    'Incoming'
                                end as Bill
                                 from bt000 where BillType <=3 and GUID = '$id'");
       return \Response::json($BillForm); 
    }

    public function DailyBillTypeInfo($id){
        $BillForm=DB::connection('sqlsrv')->select("select ROW_NUMBER() Over (Order by GUID) As Num, Type,GUID,BillType,Name,LatinName,
                                case when bIsInput = 0 then
                                    'Outgoing'
                                    else
                                    'Incoming'
                                end as Bill
                                 from bt000 where BillType <=3 and GUID = '$id'");
       return \Response::json($BillForm); 
    }

    public function EmaarApiSendReport(Request $request){
        
        $apisetups = DB::select('select @a:=@a+1 serial_number,t.* from ( SELECT `id`,`apiname`, `schedule` FROM api_setups ) t');

        return view('submit',compact('apisetups'));
    }

    public function DailyEmaarApiSendReport(Request $request){
        
        $apisetups = DB::select("select @a:=@a+1 serial_number,t.* from ( SELECT `id`,`apiname`, `schedule` FROM api_setups where schedule='Daily' ) t");

        return view('dailysubmit',compact('apisetups'));
    }
    public function MonthlyEmaarApiSendReport(Request $request){
        
        $apisetups = DB::select("select @a:=@a+1 serial_number,t.* from ( SELECT `id`,`apiname`, `schedule` FROM api_setups where schedule='Monthly' ) t");

        return view('monthlysubmit',compact('apisetups'));
    }
    public function GetBillReport($id){
        $apisetups = DB::select('select *,Day(startdate) as dd from api_setups where id ='.$id);
        $ApiGUIDInput =0;
        $ApiGUIDOutput =0;
        $strApiGUIDInput ='';
        $strApiGUIDOutput ='';
        $strApiDateSched='';
        $strApiDateDay='';
        $strApiwVAT='';
        foreach ($apisetups as $key) {
            # code...
           $ApiId = $key->id;
           $ctr=0;
           $ctr1=0;
           $strApiDateSched = $key->schedule;
           $strApiDateDay = $key->dd;
           $strApiwVAT = $key->wvat;
           $apisetupbillforms = ApiSetupBillForm::where('apisetup_id','=',$ApiId)->get();           
           foreach ($apisetupbillforms as $key) {
               # code...                
                if($key->isInputOutput=="Incoming"){
                    $ApiGUIDInput = $key->billguid; 
                    if($ctr>0)
                    {
                        $strApiGUIDInput = $strApiGUIDInput . " or ";
                    }
                    $strApiGUIDInput = $strApiGUIDInput . "  bt000.guid = '".$ApiGUIDInput."'";
                    $ctr=1;
                }else{
                    $ApiGUIDOutput = $key->billguid;
                    if($ctr1>0)
                    {
                        $strApiGUIDOutput = $strApiGUIDOutput . " or ";
                    }
                    $strApiGUIDOutput = $strApiGUIDOutput . " bt000.guid = '".$ApiGUIDOutput."'";
                    $ctr1=1;
                } 
           }           
        }

        // $strSQL = "with TempTotal as (select 0 as TempTotal, 1 as TemCol),";
        $strSQL = "with TotalBillInput as ";
       if($strApiwVAT=="on"){
        $strSQL = $strSQL. "(select isnull(SUM((bu000.Total-bu000.TotalDisc) + bu000.VAT),0) as TotalInput,isnull(count(bu000.guid),0) as InputNumberTransaction, 1 as TemCol from bu000 ";
        }else{
        $strSQL = $strSQL. "(select isnull(SUM(bu000.Total-bu000.TotalDisc),0) as TotalInput,isnull(count(bu000.guid),0) as InputNumberTransaction, 1 as TemCol from bu000 ";
        }
        $strSQL = $strSQL. "inner join bt000 on bt000.GUID = bu000.TypeGUID ";
        $strSQL = $strSQL. "where bu000.IsPosted = 1 and (".$strApiGUIDInput.")";
        
        if($strApiDateSched=="Daily"){
        $strSQL = $strSQL. " and convert(date,bu000.Date,101) = convert(date,DATEADD(day,-1,GETDATE()),101) ";
        }
        else{
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d')." -1 month"));
            $dateTo = date("Y-m").'-'.$strApiDateDay;
        $strSQL = $strSQL. " and (convert(date,bu000.Date,101) >= '".$dateFrom."' and convert(date,bu000.Date,101) < '".$dateTo."') ";
        }

        $strSQL = $strSQL. " ";
        $strSQL = $strSQL. "), ";
        $strSQL = $strSQL. "TotalBillOutput as ";
        if($strApiwVAT=="on"){
        $strSQL = $strSQL. "(select isnull(sum((bu000.Total-bu000.TotalDisc) + bu000.VAT),0) as TotalOutput,isnull(count(bu000.guid),0) as OutputNumberTransaction,1 as TemCol from bu000 ";
        }else{
        $strSQL = $strSQL. "(select isnull(sum(bu000.Total-bu000.TotalDisc),0) as TotalOutput,isnull(count(bu000.guid),0) as OutputNumberTransaction,1 as TemCol from bu000 ";
        }
        $strSQL = $strSQL. "inner join bt000 on bt000.GUID = bu000.TypeGUID ";
        $strSQL = $strSQL. "where bu000.IsPosted = 1 and (".$strApiGUIDOutput.")";

        if($strApiDateSched=="Daily"){
        $strSQL = $strSQL. " and convert(date,bu000.Date,101) = convert(date,DATEADD(day,-1,GETDATE()),101) ";
        }else{
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d')." -1 month"));
            $dateTo = date("Y-m").'-'.$strApiDateDay;
        $strSQL = $strSQL. " and (convert(date,bu000.Date,101) >= '".$dateFrom."' and convert(date,bu000.Date,101) < '".$dateTo."') ";
        }

        $strSQL = $strSQL. " ";
        $strSQL = $strSQL. ") ";
        $strSQL = $strSQL. "Select TotalInput,TotalOutput,InputNumberTransaction,OutputNumberTransaction,TotalOutput-TotalInput as Total,OutputNumberTransaction-InputNumberTransaction as TotalTransaction from TotalBillInput ";
        $strSQL = $strSQL. "inner join TotalBillOutput on TotalBillOutput.TemCol = TotalBillInput.TemCol";
        $ApiSubmits=DB::connection('sqlsrv')->select($strSQL);
        // var_dump($strSQL);
        return \Response::json($ApiSubmits); 
    }

    public function GetMonthlyBillReport($id){
        $apisetups = DB::select('select *,Day(startdate) as dd from api_setups where id ='.$id);
        $ApiGUIDInput =0;
        $ApiGUIDOutput =0;
        $strApiGUIDInput ='';
        $strApiGUIDOutput ='';
        $strApiDateSched='';
        $strApiDateDay='';
        $strApiwVAT='';
        foreach ($apisetups as $key) {
            # code...
           $ApiId = $key->id;
           $ctr=0;
           $ctr1=0;
           $strApiDateSched = $key->schedule;
           $strApiDateDay = $key->dd;
           $strApiwVAT = $key->wvat;
           $apisetupbillforms = ApiSetupBillForm::where('apisetup_id','=',$ApiId)->get();           
           foreach ($apisetupbillforms as $key) {
               # code...                
                if($key->isInputOutput=="Incoming"){
                    $ApiGUIDInput = $key->billguid; 
                    if($ctr>0)
                    {
                        $strApiGUIDInput = $strApiGUIDInput . " or ";
                    }
                    $strApiGUIDInput = $strApiGUIDInput . "  bt000.guid = '".$ApiGUIDInput."'";
                    $ctr=1;
                }else{
                    $ApiGUIDOutput = $key->billguid;
                    if($ctr1>0)
                    {
                        $strApiGUIDOutput = $strApiGUIDOutput . " or ";
                    }
                    $strApiGUIDOutput = $strApiGUIDOutput . " bt000.guid = '".$ApiGUIDOutput."'";
                    $ctr1=1;
                } 
           }           
        }

        // $strSQL = "with TempTotal as (select 0 as TempTotal, 1 as TemCol),";
        $strSQL = "with TotalBillInput as ";
       if($strApiwVAT=="on"){
        $strSQL = $strSQL. "(select isnull(SUM((bu000.Total-bu000.TotalDisc) + bu000.VAT),0) as TotalInput,isnull(count(bu000.guid),0) as InputNumberTransaction, 1 as TemCol from bu000 ";
        }else{
        $strSQL = $strSQL. "(select isnull(SUM(bu000.Total-bu000.TotalDisc),0) as TotalInput,isnull(count(bu000.guid),0) as InputNumberTransaction, 1 as TemCol from bu000 ";
        }
        $strSQL = $strSQL. "inner join bt000 on bt000.GUID = bu000.TypeGUID ";
        $strSQL = $strSQL. "where bu000.IsPosted = 1 and (".$strApiGUIDInput.")";
        
        if($strApiDateSched=="Daily"){
        $strSQL = $strSQL. " and convert(date,bu000.Date,101) = convert(date,DATEADD(day,-1,GETDATE()),101) ";
        }
        else{
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d')." -1 month"));
            $dateTo = date("Y-m").'-'.$strApiDateDay;
        $strSQL = $strSQL. " and (convert(date,bu000.Date,101) >= convert(date,DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE())-1, 0),101)  and convert(date,bu000.Date,101) <=convert(date, DATEADD(MONTH, DATEDIFF(MONTH, -1, GETDATE())-1, -1),101)) ";
        }

        $strSQL = $strSQL. " ";
        $strSQL = $strSQL. "), ";
        $strSQL = $strSQL. "TotalBillOutput as ";
        if($strApiwVAT=="on"){
        $strSQL = $strSQL. "(select isnull(sum((bu000.Total-bu000.TotalDisc) + bu000.VAT),0) as TotalOutput,isnull(count(bu000.guid),0) as OutputNumberTransaction,1 as TemCol from bu000 ";
        }else{
        $strSQL = $strSQL. "(select isnull(sum(bu000.Total-bu000.TotalDisc),0) as TotalOutput,isnull(count(bu000.guid),0) as OutputNumberTransaction,1 as TemCol from bu000 ";
        }
        $strSQL = $strSQL. "inner join bt000 on bt000.GUID = bu000.TypeGUID ";
        $strSQL = $strSQL. "where bu000.IsPosted = 1 and (".$strApiGUIDOutput.")";

        if($strApiDateSched=="Daily"){
        $strSQL = $strSQL. " and convert(date,bu000.Date,101) = convert(date,DATEADD(day,-1,GETDATE()),101) ";
        }else{
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d')." -1 month"));
            $dateTo = date("Y-m").'-'.$strApiDateDay;
        $strSQL = $strSQL. " and (convert(date,bu000.Date,101) >= convert(date,DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE())-1, 0),101)  and convert(date,bu000.Date,101) <=convert(date, DATEADD(MONTH, DATEDIFF(MONTH, -1, GETDATE())-1, -1),101)) ";
        }

        $strSQL = $strSQL. " ";
        $strSQL = $strSQL. ") ";
        $strSQL = $strSQL. "Select TotalInput,TotalOutput,InputNumberTransaction,OutputNumberTransaction,TotalOutput-TotalInput as Total,OutputNumberTransaction-InputNumberTransaction as TotalTransaction, convert(date,DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE())-1, 0),101) as DateFrom,convert(date, DATEADD(MONTH, DATEDIFF(MONTH, -1, GETDATE())-1, -1),101) as DateTo from TotalBillInput ";
        $strSQL = $strSQL. "inner join TotalBillOutput on TotalBillOutput.TemCol = TotalBillInput.TemCol";
        $ApiSubmits=DB::connection('sqlsrv')->select($strSQL);
        // var_dump($strSQL);
        return \Response::json($ApiSubmits); 
    }
    public function SaveReport(Request $request){
        $BillForm=DB::connection('sqlsrv')->select("select ROW_NUMBER() Over (Order by GUID) As Num, Type,GUID,BillType,Name,LatinName,
                                case when bIsInput = 0 then
                                    'Outgoing'
                                    else
                                    'Incoming'
                                end as Bill
                                 from bt000 where BillType <=3 and GUID = '$id'");
       return \Response::json($BillForm); 
    }
}
