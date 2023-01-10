@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">EMAAR API Setup

                </div>

                <div class="panel-body">
                    Fill out the fields for API Settings.
                </div>
            </div>
        </div> 
        @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
          @endif
          @foreach($apisetups as $apisetup)
        <form  action="{{route('apisetupupdate')}}/{{$apisetup->id}}" method="POST" enctype="multipart/form-data" class="needs-validation add-product-form" novalidate="">
            <input type="hidden" name ="_token" value="{!! csrf_token() !!}">
        
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control has-error" name="code" placeholder="Code" value="{{$apisetup->code}}">
            </div>             
        </div>   
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control has-error"  name="apiname" placeholder="API Name" value="{{$apisetup->apiname}}">
            </div> 
           <div class="col-md-3 form-check form-check-inline">
            @if($apisetup->status=="on")
              <input id="checkbox3" type="checkbox"  checked="checked" name ="status">
            @else
              <input id="checkbox3" type="checkbox"  name ="status">              
            @endif
            <label for="checkbox3">Active</label>
            </div>   
            <div class="col-md-3 form-check form-check-inline">
            @if($apisetup->wvat=="on")
              <input id="checkbox3" type="checkbox"  checked="checked" name ="wvat">
            @else
              <input id="checkbox3" type="checkbox"  name ="wvat">              
            @endif
            <label for="checkbox3">w/VAT</label>
            </div> 
        </div>    
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control has-error"  name="apihost" placeholder="API Post" value="{{$apisetup->apihost}}">
            </div> 
            <div class="col-md-6">
                <input type="text" class="form-control has-error"  name="apikey" placeholder="API Key" value="{{$apisetup->apikey}}">
            </div>             
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control has-error"  name="unitno" placeholder="Unit No" value="{{$apisetup->unitno}}">
            </div> 
            <div class="col-md-6">
                <input type="text" class="form-control has-error"  name="leasecode" placeholder="Lease Code" value="{{$apisetup->leasecode}}">
            </div>             
        </div>
        <br>
        <div class="row">
                <label>Schedule</label>  
        </div>
        <div class="row">
              <div class='col-sm-4'>
                 From:<input type='date' class="form-control" name ="startdate" value="{{$apisetup->startdate}}" />
              </div>
              <div class='col-sm-4'>
                 To:<input type='date' class="form-control" name="enddate" value="{{$apisetup->enddate}}" />
              </div>       
            <div class="col-sm-4">
                  <select class="form-control" name="schedule">
                    <option>Daily</option>
                    <option>Monthly</option>
                  </select>
            </div>
        </div> 
        @endforeach 
        <br>
        <div class="row">
            <div class="col-md-6">
                <span class="btn btn-primary btn-xs btn-Add-Bill" value="">Add Bill Form</span>
            </div>
            <!-- Table-to-load-the-data Part -->
            <table class="table api-list">
                <thead> 
                    
                    <tr>
                        <th>Name</th>
                        <th>Bill Type</th> 
                        <th align="right" class="RemoveBillTypeApiList"></th>
                    </tr>
                </thead>
                <tbody id="api-list" name="api-list">
                @foreach($apisetupbillforms as $apisetupbillform)                    
                   <tr id="api-list{{$apisetupbillform->billguid}}">
                        <input value="{{$apisetupbillform->billguid}}" type="hidden" name="BillGUID[]"> 
                        <td>{{$apisetupbillform->name}}<input value="{{$apisetupbillform->name}}" type="hidden" name="BillName[]"></td>
                        <td>{{$apisetupbillform->isInputOutput}}<input value="{{$apisetupbillform->isInputOutput}}" type="hidden" name="BillType[]"></td>
                        <td align="right"> <button class="btn btn-warning btn-xs btn-detail RemoveBillTypeApiList" value="{{$apisetupbillform->billguid}}">Remove</button>
                        </td>
                    </tr> 
                @endforeach                
                </tbody>
            </table>            
        </div>
        <br>
        <div class="row" align="right"><input type="submit" class="btn btn-primary" value="Save"></div>
        </form>
        <div>       
            <!-- End of Table-to-load-the-data Part -->
            <!-- Modal (Pop up when detail button clicked) -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalLabel">Al Ameen Bill Form</h4>
                        </div>
                        <div class="modal-body">
                            <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                                <table class="table">
                                <thead> 
                                    
                                    <tr>
                                        <th>Name</th>
                                        <th align="right"></th>
                                    </tr>
                                </thead>
                                <tbody id="api-list-bill"> 
                                @foreach($BillForm as $billForm)                   
                                    <tr id="billFormRow{!!$billForm->Num!!}">
                                        <td>                                            
                                            {!!$billForm->LatinName!!}
                                            <input value="{{$billForm->LatinName}}" type="hidden" id="billFormLatin{{$billForm->Num}}" >
                                            <input value="{{$billForm->GUID}}" type="hidden" id="billFormGUID{{$billForm->Num}}" >
                                            <input value="{{$billForm->Bill}}" type="hidden" id="billFormBill{{$billForm->Num}}" >
                                        </td>
                                        <td align="right">
                                            <button type="button" class="btn btn-primary ModalBillFormAdd" value="{!!$billForm->GUID!!}">Add</button>
                                        </td>
                                    </tr>
                                @endforeach                    
                                </tbody>
                            </table>    
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-detail" id="btn-modal-close">Close</button>
                            <input type="hidden" id="task_id" name="task_id" value="0">
                        </div>
                    </div>
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
        $('.btn-Add-Bill').click(function(e){
            $('#myModal').modal('show');
        });
        $('#btn-modal-close').click(function(e){
            $('#myModal').modal('hide');
        });
        $('.ModalBillFormAdd').click(function(e){  
            var billNum = $(this).val(); 
            $.get('{{route('BillTypeInfo')}}' + '/' + billNum, function (data) {
                //success data
                console.log(data);
                // alert(data[0].LatinName);
                 $('.api-list tbody').append('<tr id="api-list'+data[0].GUID+'"><input value="'+data[0].GUID+'" type="hidden" name="BillGUID[]" > <td>'+ data[0].LatinName+'<input value="'+data[0].LatinName+'" type="hidden" name="BillName[]" ></td><td>'+data[0].Bill+'<input value="'+data[0].Bill+'" type="hidden" name="BillType[]"></td><td align="right"> <button class="btn btn-warning btn-xs btn-detail RemoveBillTypeApiList" value="'+data[0].GUID+'">Remove</button></td></tr> '); 
                 
            });  
            $("#billFormRow" + $(this).val()).remove();              
        });
        $(".api-list").on('click', '.RemoveBillTypeApiList', function(){
            $(this).parent().parent().remove();
        });
     });

 </script>   
@endsection
