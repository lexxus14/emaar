@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">EMAAR API Submit Report

                </div>

                <div class="panel-body">
                    This will submit the report automatically to EMAAR. Click the submit button and select the specific date that you would like to submit.
                </div>
            </div>
        </div> 
    </div>
    <div class="row">
            <!-- Table-to-load-the-data Part -->
            <table class="table api-list">
                <thead>                     
                    <tr>
                        <th>Name</th>
                        <th>Schedule</th> 
                        <th align="center" class="RemoveBillTypeApiList">Status</th>
                    </tr>
                </thead>
                <tbody id="api-list" name="api-list">
                <?php $ctr=1 ?>
                @foreach($apisetups as $apisetup)                    
                   <tr id="api-list">
                        <td>{{$apisetup->apiname}}<input type="hidden" value="{{$apisetup->id}}" id="ApisetupID{{$ctr}}"></td>
                        <td>{{$apisetup->schedule}}</td>
                        <td><span id="progress{{$apisetup->id}}">In-progress</span> <button class="btn btn-warning btn-xs btn-detail btnManualSubmit{{$ctr}} btnSubmit" value="{{$apisetup->id}}">Submit</button> </td>
                    </tr> 
                    <?php $ctr++ ?>
                @endforeach                
                </tbody>
            </table>   
            <!-- <button class="btn btn-warning btn-xs btn-detail btnManualSubmit">Submit All</button>          -->
        </div>  
        <div class="modal fade" id="myModal-date" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                      <h4 class="modal-title" id="myModalLabel">Report</h4>
                  </div>
                  <div class="modal-body">
                      <form id="frmDate" name="frmDate" class="form-horizontal" novalidate="">
                          <div class="form-group error">
                              <label for="api-report-id" class="col-sm-2 control-label">Date</label>
                              <div class="col-sm-4">
                                  <input type="date" class="form-control has-error" id="DateReport" name="api-report-id" placeholder="ID" value="">
                              </div>
                          </div>                         
                      </form>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-primary" id="btn-SubmitDate" >Submit</button>
                  </div>
              </div>
          </div>
        </div>
        <div class="modal fade" id="myModal-date-range" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                      <h4 class="modal-title" id="myModalLabel">Report</h4>
                  </div>
                  <div class="modal-body">
                      <form id="frmDate" name="frmDate" class="form-horizontal" novalidate="">
                          <div class="form-group error">
                              <label for="api-report-id" class="col-sm-2 control-label">Date From</label>
                              <div class="col-sm-4">
                                  <input type="date" class="form-control has-error" id="DateReportFrom" name="api-report-id" placeholder="ID" value="">
                              </div>
                              <label for="date-created" class="col-sm-2 control-label">Date To</label>
                              <div class="col-sm-4">
                                  <input type="date" class="form-control has-error" id="DateReportTo" name="date-created" placeholder="date-created" value="">
                              </div>
                          </div>                         
                      </form>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-primary" id="btn-SubmitDateRange" >Submit</button>
                  </div>
              </div>
          </div>
        </div>
</div>
<meta name="_token" content="{!! csrf_token() !!}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
 <script type="text/javascript">
    $(document).ready(function(){     
        var count=10;
        var UnitNo,LeaseCode,SalesDate,TransactionCount,NetSales;

        function GetBillData(Id) { 
            var newdate = new Date();
            var dd = newdate.getDate();
            var mm = newdate.getMonth() + 1;
            var y = newdate.getFullYear();
            var formattedDate =  y + '-' + mm + '-' + dd;

            $.get('{{route('ApiTypeInfo')}}' + '/' + Id , function (data) {
                //success data
                console.log(data);
                // if(data[0].enddate <= formattedDate){
                    if(data[0].schedule!="Daily"){
                        if(data[0].dd==dd){
                            execute(Id,data[0].apihost,data[0].apikey,data[0].leasecode,data[0].unitno,data[0].schedule);
                        }
                    }   
                    else{
                            execute(Id,data[0].apihost,data[0].apikey,data[0].leasecode,data[0].unitno,data[0].schedule); 
                        }
            });              
         }

        $('.btnManualSubmit').click(function(e){
            var rowCount = $('#api-list tr').length;
            for(var ctr =1; ctr<=rowCount;ctr++){
                GetBillData($('.btnManualSubmit' +ctr).val());
            }            
        });
        $('.btnSubmit').click(function(e){
             $.get('{{route('ApiTypeInfo')}}' + '/' + $(this).val(), function (data) {
                  if(data[0].schedule!="Daily"){
                    $('#myModal-date-range').modal('show');
                    $('#btn-SubmitDateRange').val(data[0].id);
                  }else{
                    $('#myModal-date').modal('show');
                    $('#btn-SubmitDate').val(data[0].id);
                  }
                });
        });

        $('#btn-SubmitDate').click(function(e){
          var id = $(this).val();
           $.get('{{route('ApiTypeInfo')}}' + '/' + id, function (data) {
              execute(id,data[0].apihost,data[0].apikey,data[0].leasecode,data[0].unitno,data[0].schedule,$('#DateReport').val(),0); 
           });
           $('#myModal-date').modal('hide');
        });

        $('#btn-SubmitDateRange').click(function(e){
          var id = $(this).val();
           $.get('{{route('ApiTypeInfo')}}' + '/' + id, function (data) {
              execute(id,data[0].apihost,data[0].apikey,data[0].leasecode,data[0].unitno,data[0].schedule,$('#DateReportFrom').val(),$('#DateReportTo').val()); 
           });
           $('#myModal-date-range').modal('hide');
        });

        function execute(Id,apihost,apikey,leasecode,unitno,sched,Datefrom,DateTo) {
            var total="0";
            var totalTransaction="0";
            
            $.get('{{route('BillReportDate')}}' + '/' + Id + '/' + Datefrom + '/' + DateTo, function (data) {
                console.log(data);
                    if(data.length <= 0 ){
                        submitapi(sched,apikey,unitno,leasecode,0,0,apihost,Id);
                    }else{
                        submitapi(sched,apikey,unitno,leasecode,data[0].TotalTransaction,data[0].Total,apihost,Id);
                    }
                    
            });       
          
        }

        function submitapi(sched,apikey,unitno,leasecode,totalTransaction,total,apihost,Id){
             const url = apihost;
             var options="";
             var strRemarks = "";
        if(sched=="Daily"){
          strRemarks = $('#DateReport').val();
           options = {
            method: "POST",
            headers: {
              "x-apikey": apikey, 
              "Accept": "application/json",
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              "SalesDataCollection": {
                "SalesInfo": [
                  {
                    "UnitNo":  unitno,
                    "LeaseCode": leasecode,
                    "SalesDate": $('#DateReport').val(),
                    "TransactionCount": totalTransaction,
                    "NetSales": total
                  }
                ]
              }
            }),
          };
        }else{
          strRemarks = $('#DateReportFrom').val() + ' to ' + $('#DateReportTo').val();
         options = {
            method: "POST",
            headers: {
              "x-apikey": apikey,
              "Accept": "application/json",
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              "SalesDataCollection": {
                "SalesInfo": [
                  {
                    "UnitNo":  unitno,
                    "LeaseCode": leasecode,
                    "SalesDateFrom": $('#DateReportFrom').val(),
                    "SalesDateTo": $('#DateReportTo').val(),
                    "TransactionCount": totalTransaction,
                    "TotalSales": total,
                    "Remarks": strRemarks,
                  }
                ]
              }
            }),
          };
        }
          fetch(url, options).then(
            response => {
              if (response.ok) {
                return response.text();                
              }
              return response.text().then(err => {
                return Promise.reject({
                  status: response.status,
                  statusText: response.statusText,
                  errorMessage: err,
                });
              });
            })
            .then(function(data){
              console.log(data);  
              let datas = JSON.parse(data); 
              if(datas["Code"] == "200"){     
                document.getElementById("progress"+Id).innerHTML="Sent";
                SaveReport(sched,unitno,leasecode,totalTransaction,total,Id,datas["Result"],strRemarks);
            }else{
              document.getElementById("progress"+Id).innerHTML="Not Sent"; 
              SaveReport(sched,unitno,leasecode,totalTransaction,total,Id,datas["ErrorMsg"],strRemarks);
            }
            })
            .catch(err => {
              console.error(err);  
              let datas = JSON.parse(err);  
              document.getElementById("progress"+Id).innerHTML="Not Sent";
              SaveReport(sched,unitno,leasecode,totalTransaction,total,Id,datas["ErrorMsg"],strRemarks);            
            });
        }

        function SaveReport(sched,unitno,leasecode,totalTransaction,total,Id,Status,Remarks){
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          })
          

          var formData = {
              Sched:sched,
              Status:Status,
              Unitno:unitno,
              Leasecode:leasecode,
              TotalTransaction:totalTransaction,
              Total:total,
              Remarks:Remarks,
              ApiSetupId:Id
          }

          var type = "POST"; //for creating new resource

          console.log(formData);

          $.ajax({

              type: type,
              url: '{{route('savesubmit')}}',
              data: formData,
              dataType: 'json',
              success: function (data) {
                  console.log(data);                
              },
              error: function (data) {
                  console.log('Error:', data);
              }
          });
      }

    });
    
 </script>
 
@endsection
