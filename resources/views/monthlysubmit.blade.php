@extends('layouts.sapp')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">EMAAR API Submit Report

                </div>

                <div class="panel-body">
                    This will submit the report automatically to EMAAR(<span id="timer"> 9 sec</span>). If it is not submitted succesfully. Please login and send the report manually.
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
                        <td><span id="progress{{$apisetup->id}}">In-progress</span> 
                    </tr> 
                    <?php $ctr++ ?>
                @endforeach                
                </tbody>
            </table>                       
        </div>  
</div>
<meta name="_token" content="{!! csrf_token() !!}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
 <script type="text/javascript">
    $(document).ready(function(){     
        var count=10;
        var counter=setInterval(timer, 1000);
        var UnitNo,LeaseCode,SalesDate,TransactionCount,NetSales;

        function timer()
        {
          count=count-1;
          if (count <= 0)
          {
             clearInterval(counter);
             var rowCount = $('#api-list tr').length;
            for(var ctr =1; ctr<=rowCount;ctr++){
                GetBillData($('#ApisetupID'+ ctr).val());  
            } 
             return;
          }
          document.getElementById("timer").innerHTML=count + " sec(s)"; 
        }

        function GetBillData(Id) { 
            var newdate = new Date();
            var dd = newdate.getDate();
            var mm = newdate.getMonth() + 1;
            var y = newdate.getFullYear();
            var formattedDate =  y + '-' + mm + '-' + dd;
            $.get('{{route('SMonthlyApiTypeInfo')}}' + '/' + Id, function (data) {
                //success data
                console.log(data);
                execute(Id,data[0].apihost,data[0].apikey,data[0].leasecode,data[0].unitno,data[0].schedule);
            });              
         }

        

        function execute(Id,apihost,apikey,leasecode,unitno,sched) {
            var total="0";
            var totalTransaction="0";
            var newdate = new Date();
            var dd = newdate.getDate();
            var mm = newdate.getMonth() + 1;
            var y = newdate.getFullYear();            
            var formattedDate =  y + '-' + mm + '-' + dd;
            $.get('{{route('SMonthlyBillReport')}}' + '/' + Id, function (data) {
                console.log(data);
                    if(data.length <= 0 ){
                        submitapi(sched,apikey,unitno,leasecode,0,0,apihost,Id,data[0].DateFrom,data[0].DateTo);
                    }else{
                        submitapi(sched,apikey,unitno,leasecode,data[0].TotalTransaction,data[0].Total,apihost,Id,data[0].DateFrom,data[0].DateTo);
                    }
                    
            });       
          
        }

        function submitapi(sched,apikey,unitno,leasecode,totalTransaction,total,apihost,Id,DateFrom,DateTo){
             const url = apihost;
             var options="";
             var newdate = new Date();
            var dd = newdate.getDate();
            var mm = newdate.getMonth() + 1;
            var y = newdate.getFullYear();            
            var formattedDate =  y + '-' + mm + '-' + dd;
            var strRemarks = "";

            <?php  $dateFrom = date('Y-m-d', strtotime(date('Y-m-d')." -1 month"));  ?>
            <?php $dateTo = date('Y-m-d', strtotime(date('Y-m-d')." -1 days"));?>
            <?php $dateSales =date('Y-m-d', strtotime(date('Y-m-d')." -1 days")); ?>

        if(sched=="Daily"){
          strRemarks= '<?php echo $dateSales; ?>';
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
                    "SalesDate": '<?php echo $dateSales; ?>',
                    "TransactionCount": totalTransaction,
                    "NetSales": total
                  }
                ]
              }
            }),
          };
        }else{
          strRemarks = '<?php echo $dateFrom.' to '.$dateTo; ?>';
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
                    "SalesDateFrom": DateFrom,
                    "SalesDateTo": DateTo,
                    "TransactionCount": totalTransaction,
                    "TotalSales": total,
                    "Remarks": DateFrom + ' ' + DateTo 
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
            .then(data => {
              console.log(data);
              let datas = JSON.parse(data); 
              if(datas["Code"] == "200"){    
                document.getElementById("progress"+Id).innerHTML="Sent";
                SaveReport(sched,unitno,leasecode,totalTransaction,total,Id,datas["Result"],strRemarks);
                window.close();
            }else{
              document.getElementById("progress"+Id).innerHTML="Not Sent"; 
              SaveReport(sched,unitno,leasecode,totalTransaction,total,Id,datas["ErrorMsg"],strRemarks);
            }
            })
            .catch(err => {
              console.error(err);   
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
 <script>
$(document).on("contextmenu", function (e) {
  e.preventDefault();
  });
$(document).keydown(function (event) {
  if (event.keyCode == 123) {
      return false;
    }
    else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) {
      return false; //Prevent from ctrl+shift+i
    }
  });
document.onkeydown = function (e) {
  if (e.keyCode == 123) {
    return false;
  }
  if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
    return false;
  }
  if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
    return false;
  }
  if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
    return false;
  }
  if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
    return false;
  }
}
</script>
 
@endsection
