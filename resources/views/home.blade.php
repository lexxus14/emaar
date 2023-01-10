@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    List of Report
                    
                </div>
                <table class="table">
                <thead>
                    
                    <tr>
                        <th>No</th>
                        <th>API Name</th>  
                        <th>Status</th>                      
                        <th>Date Created</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody id="api-list" name="api-list">   
                     @foreach($SaveReport as $savereport)         
                    <tr id="">
                        <td>{{$savereport->id}}</td>
                        <td>{{$savereport->apiname}}</td>
                        <td>{{$savereport->status}}</td>
                        <td>{{$savereport->created_at}}</td>
                        <td><button id="myModalLabel" class="btn btn-warning btn-xs btn-detail open-modal" value="{{$savereport->id}}">View</button></td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
           <center> <?php echo $SaveReport->render(); ?></center>
        </div>

    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Report</h4>
                </div>
                <div class="modal-body">
                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">

                        <div class="form-group error">
                            <label for="api-report-id" class="col-sm-3 control-label">No</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control has-error" id="api-report-id" name="api-report-id" placeholder="ID" value="">
                            </div>
                            <label for="date-created" class="col-sm-2 control-label">Date</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control has-error" id="date-created" name="date-created" placeholder="date-created" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sched" class="col-sm-3 control-label">Schedule</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="sched" name="sched" placeholder="Schedule" value="">
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label for="api-name" class="col-sm-3 control-label">API Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="api-name" name="api-name" placeholder="API Name" value="">
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label for="status" class="col-sm-3 control-label">Remarks</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Remarks" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="status" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="status" name="status" placeholder="Status" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="unit-no" class="col-sm-3 control-label">Unit No.</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="unit-no" name="unit-no" placeholder="Unit No" value="">
                            </div>

                            <label for="lease-code" class="col-sm-3 control-label">Lease Code</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="lease-code" name="lease-code" placeholder="Lease Code" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="total-transaction" class="col-sm-3 control-label">Total Transaction</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="total-transaction" name="total-transaction" placeholder="Total Transaction" value="">
                            </div>

                            <label for="total" class="col-sm-3 control-label">Total</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="total" name="total" placeholder="Total" value="">
                            </div>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-close" value="add">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        
        $('.open-modal').click(function(){
            var val = $(this).val(); 
            var total = 0;

            $.get('{{route('viewreport')}}' + '/' + val, function(data){
                console.log(data);
                total = parseFloat(data[0].total).toFixed(2);
                total = Number(total).toLocaleString('en');
                $('#api-report-id').val(data[0].id);
                $('#date-created').val(data[0].created_at);
                $('#status').val(data[0].status);
                $('#unit-no').val(data[0].unitno);
                $('#sched').val(data[0].sched);
                $('#lease-code').val(data[0].leasecode);
                $('#total-transaction').val(data[0].totalTransaction);
                $('#total').val(total);
                $('#api-name').val(data[0].apiname);
            });
            $('#myModal').modal('show');
        });
        $('#btn-close').click(function(){
            $('#myModal').modal('hide');
        });
        

    });
</script>
@endsection
