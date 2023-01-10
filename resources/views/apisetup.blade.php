@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">EMAAR API Setup

                </div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div> 
        <div class="col-md-12">
            <a href="{{route('apisetupadd')}}" class="btn btn-primary btn-xs">Add New API</a> 
        </div>       
        <div>            
               
            <!-- Table-to-load-the-data Part -->
            <table class="table">
                <thead>
                    
                    <tr>
                        <th>Code</th>
                        <th>API Name</th>  
                        <th>Status</th>                      
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="api-list" name="api-list">   
                    @foreach($apisetup as $api)                 
                    <tr id="">
                        <td>{{$api->code}}</td>
                        <td>{{$api->apiname}}</td>
                        <td>{{$api->status}}</td>
                        <td>{{$api->created_at}}</td>
                        <td>
                            <a href="{{route('apisetupedit')}}/{{$api->id}}" class="btn btn-warning btn-xs btn-detail open-modal">Edit</a>
                            <a href="{{route('apisetupdelete')}}/{{$api->id}}" class="btn btn-danger btn-xs btn-delete delete-task">Delete</a>
                        </td>
                    </tr>
                    @endforeach                    
                </tbody>
            </table>
            <!-- End of Table-to-load-the-data Part -->
            <!-- Modal (Pop up when detail button clicked) -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalLabel">API Editor</h4>
                        </div>
                        <div class="modal-body">
                            <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">

                                <div class="form-group error">
                                    <label for="inputTask" class="col-sm-3 control-label">API</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control has-error" id="task" name="task" placeholder="Task" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Description</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input_fields_wrap_img">
                                       
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="btn-save" value="add">Save changes</button>
                            <input type="hidden" id="task_id" name="task_id" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<meta name="_token" content="{!! csrf_token() !!}" />
    
@endsection
