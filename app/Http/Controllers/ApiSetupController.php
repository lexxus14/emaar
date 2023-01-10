<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Validate;

use App\ApiSetup;
use App\ApiSetupBillForm;
use App\Http\Requests;

use DB;
class ApiSetupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

	public function index(){
        $apisetup = ApiSetup::all();
		return view('apisetup',compact('apisetup'));
	}

	public function Add(){
        $BillForm=DB::connection('sqlsrv')->select("select ROW_NUMBER() Over (Order by GUID) As Num, Type,GUID,BillType,Name,LatinName,
                                case when bIsInput = 0 then
                                    'Outgoing'
                                    else
                                    'Incoming'
                                end as Bill
                                 from bt000 where BillType <=3");
		return view('apisetupadd',compact('BillForm'));
	}
    public function Store(Request $request){
         $messages = [
                    'code.required' => 'Code is required.',     
                    'apiname.required' => 'Name is required.',
                    'apihost.required' => 'API Host is required.',
                    'apikey.required' => 'API Key Image is required.',
                    'startdate.required' => 'Default Image is required.',
                    'enddate.required' => 'Default Image is required.',
                  ];
        $this->validate($request,[
                'code' => 'required|unique:api_setups|max:30',
                'apiname' => 'required',
                'apihost' => 'required',
                'apikey' => 'required',
                'startdate' => 'required',
                'enddate' => 'required',
            ],$messages);

        $apisetup = new ApiSetup;
        $apisetup->code=$request->code;
        $apisetup->apiname=$request->apiname;
        $apisetup->apihost=$request->apihost;
        $apisetup->apikey=$request->apikey;
        $apisetup->leasecode=$request->leasecode;
        $apisetup->unitno=$request->unitno;
        $apisetup->startdate=$request->startdate;
        $apisetup->enddate=$request->enddate;
        $apisetup->schedule=$request->schedule;
        $apisetup->status=$request->status;
        $apisetup->wvat=$request->wvat;
        $apisetup->save();
        $apisetup_id = $apisetup->id;

        $ctr = 0;
        foreach ($request->BillGUID as $key => $value) {
            $apitsetupbillform = new ApiSetupBillForm;
            $apitsetupbillform->name= $request->BillName[$ctr];
            $apitsetupbillform->billguid=$value;
            $apitsetupbillform->apisetup_id=$apisetup_id;
            $apitsetupbillform->isInputOutput=$request->BillType[$ctr];
            $apitsetupbillform->save();
            $ctr++;
        }
         $apisetup = ApiSetup::all();
        return view('apisetup',compact('apisetup'));
    }

    public function Edit($id){
        $apisetups = ApiSetup::where('id','=',$id)->get();
        $apisetupbillforms = ApiSetupBillForm::where('apisetup_id','=',$id)->get();
        $BillForm=DB::connection('sqlsrv')->select("select ROW_NUMBER() Over (Order by GUID) As Num, Type,GUID,BillType,Name,LatinName,
                                case when bIsInput = 0 then
                                    'Outgoing'
                                    else
                                    'Incoming'
                                end as Bill
                                 from bt000 where BillType <=3");

        return view('apisetupedit',compact('apisetups','apisetupbillforms','BillForm'));
    }
    public function Update(Request $request,$id){
        $messages = [
                    'code.required' => 'Code is required.',     
                    'apiname.required' => 'Name is required.',
                    'apihost.required' => 'API Host is required.',
                    'apikey.required' => 'API Key Image is required.',
                    'startdate.required' => 'Default Image is required.',
                    'enddate.required' => 'Default Image is required.',
                  ];
        $this->validate($request,[
                'code' => 'required',
                'apiname' => 'required',
                'apihost' => 'required',
                'apikey' => 'required',
                'startdate' => 'required',
                'enddate' => 'required',
            ],$messages);

        $apisetup = ApiSetup::find($id);
        $apisetup->code=$request->code;
        $apisetup->apiname=$request->apiname;
        $apisetup->apihost=$request->apihost;
        $apisetup->apikey=$request->apikey;
        $apisetup->leasecode=$request->leasecode;
        $apisetup->unitno=$request->unitno;
        $apisetup->startdate=$request->startdate;
        $apisetup->enddate=$request->enddate;
        $apisetup->schedule=$request->schedule;
        $apisetup->status=$request->status;
        $apisetup->wvat=$request->wvat;
        $apisetup->save();
        $apisetup_id = $apisetup->id;

        $ApiSetupBillForm = ApiSetupBillForm::where('apisetup_id', '=', $apisetup_id)->delete();

        $ctr = 0;
        foreach ($request->BillGUID as $key => $value) {
            $apitsetupbillform = new ApiSetupBillForm;
            $apitsetupbillform->name= $request->BillName[$ctr];
            $apitsetupbillform->billguid=$value;
            $apitsetupbillform->apisetup_id=$apisetup_id;
            $apitsetupbillform->isInputOutput=$request->BillType[$ctr];
            $apitsetupbillform->save();
            $ctr++;
        }
         $apisetup = ApiSetup::all();
        return view('apisetup',compact('apisetup'));
    }
    public function Delete($id){
        $apisetup = ApiSetup::destroy($id);
         $apisetup = ApiSetup::all();
        return view('apisetup',compact('apisetup'));
    }
    public function GetApiTypeInfo($id){
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

    public function EmaarApiSendReport(Request $request){
        
        $apisetups = DB::select('select @a:=@a+1 serial_number,t.* from ( SELECT `id`,`apiname`, `schedule` FROM api_setups ) t');

        return view('emaarapi',compact('apisetups'));
    }

    public function GetBillReportDate($id,$DateFrom,$DateTo){
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
        $strSQL = $strSQL. " and convert(date,bu000.Date,101) = '".$DateFrom."'";
        }
        else{
            
        $strSQL = $strSQL. " and (convert(date,bu000.Date,101) >= '".$DateFrom."' and convert(date,bu000.Date,101) <= '".$DateTo."') ";
        }

        $strSQL = $strSQL. " ";
        $strSQL = $strSQL. "), ";
        $strSQL = $strSQL. "TotalBillOutput as ";
        if($strApiwVAT=="on"){
        $strSQL = $strSQL. "(select isnull(sum((bu000.Total-bu000.TotalDisc)+ bu000.VAT),0) as TotalOutput,isnull(count(bu000.guid),0) as OutputNumberTransaction,1 as TemCol from bu000 ";
        }else{
        $strSQL = $strSQL. "(select isnull(sum(bu000.Total-bu000.TotalDisc),0) as TotalOutput,isnull(count(bu000.guid),0) as OutputNumberTransaction,1 as TemCol from bu000 ";
        }
        $strSQL = $strSQL. "inner join bt000 on bt000.GUID = bu000.TypeGUID ";
        $strSQL = $strSQL. "where bu000.IsPosted = 1 and (".$strApiGUIDOutput.")";

        if($strApiDateSched=="Daily"){
        $strSQL = $strSQL. " and convert(date,bu000.Date,101) = '".$DateFrom."'";
        }else{
            // $dateFrom = date('Y-m-d', strtotime(date('Y-m-d')." -1 month"));
            // $dateTo = date("Y-m").'-'.$strApiDateDay;
        $strSQL = $strSQL. " and (convert(date,bu000.Date,101) >= '".$DateFrom."' and convert(date,bu000.Date,101) <= '".$DateTo."') ";
        }

        $strSQL = $strSQL. "  ";
        $strSQL = $strSQL. ") ";
        $strSQL = $strSQL. "Select TotalInput,TotalOutput,InputNumberTransaction,OutputNumberTransaction,TotalOutput-TotalInput as Total,OutputNumberTransaction-InputNumberTransaction as TotalTransaction from TotalBillInput ";
        $strSQL = $strSQL. "inner join TotalBillOutput on TotalBillOutput.TemCol = TotalBillInput.TemCol";
        $ApiSubmits=DB::connection('sqlsrv')->select($strSQL);
        // var_dump($strSQL);
        return \Response::json($ApiSubmits); 
    }

    public function GetBillReport($id){
        $apisetups = DB::select('select *,Day(startdate) as dd from api_setups where id ='.$id);
        $ApiGUIDInput =0;
        $ApiGUIDOutput =0;
        $strApiGUIDInput ='';
        $strApiGUIDOutput ='';
        $strApiDateSched='';
        $strApiDateDay='';
        foreach ($apisetups as $key) {
            # code...
           $ApiId = $key->id;
           $ctr=0;
           $ctr1=0;
           $strApiDateSched = $key->schedule;
           $strApiDateDay = $key->dd;
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
        $strSQL = $strSQL. "(select isnull(SUM(bu000.Total-bu000.TotalDisc),0) as TotalInput,isnull(count(bu000.guid),0) as InputNumberTransaction, 1 as TemCol from bu000 ";
        $strSQL = $strSQL. "inner join bt000 on bt000.GUID = bu000.TypeGUID ";
        $strSQL = $strSQL. "where bt000.bIsInput=1 and bt000.type <=3 and (".$strApiGUIDInput.")";
        
        if($strApiDateSched=="Daily"){
        $strSQL = $strSQL. " and bu000.Date = getdate()";
        }
        else{
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d')." -1 month"));
            $dateTo = date("Y-m").'-'.$strApiDateDay;
        $strSQL = $strSQL. " and (bu000.Date >= '".$dateFrom."' and bu000.Date < '".$dateTo."') ";
        }

        $strSQL = $strSQL. " group by bt000.bIsInput ";
        $strSQL = $strSQL. "), ";
        $strSQL = $strSQL. "TotalBillOutput as ";
        $strSQL = $strSQL. "(select isnull(sum(bu000.Total-bu000.TotalDisc),0) as TotalOutput,isnull(count(bu000.guid),0) as OutputNumberTransaction,1 as TemCol from bu000 ";
        $strSQL = $strSQL. "inner join bt000 on bt000.GUID = bu000.TypeGUID ";
        $strSQL = $strSQL. "where bt000.bIsOutput = 1 and bt000.type <=3 and (".$strApiGUIDOutput.")";

        if($strApiDateSched=="Daily"){
        $strSQL = $strSQL. " and bu000.Date = getdate()";
        }else{
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d')." -1 month"));
            $dateTo = date("Y-m").'-'.$strApiDateDay;
        $strSQL = $strSQL. " and (bu000.Date >= '".$dateFrom."' and bu000.Date < '".$dateTo."') ";
        }

        $strSQL = $strSQL. " group by bt000.bIsOutput ";
        $strSQL = $strSQL. ") ";
        $strSQL = $strSQL. "Select TotalInput,TotalOutput,InputNumberTransaction,OutputNumberTransaction,format(TotalOutput-TotalInput,'N2') as Total,OutputNumberTransaction-InputNumberTransaction as TotalTransaction from TotalBillInput ";
        $strSQL = $strSQL. "inner join TotalBillOutput on TotalBillOutput.TemCol = TotalBillInput.TemCol";
        $ApiSubmits=DB::connection('sqlsrv')->select($strSQL);
        return \Response::json($ApiSubmits); 
    }
}
        