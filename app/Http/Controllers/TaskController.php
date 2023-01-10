<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use DB;
use Auth;
use App\Http\Requests;
use App\Task;
use App\User;

class TaskController extends Controller
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
		$tasks = Task::all();
		return view('welcome',compact('tasks'));
	}
	
	public function store(Request $request){
		$task = Task::create($request->all());


		return \Response::json($task);
	}
	
	public function edit($task_id){
	    $task = Task::find($task_id);
	    $AllTask = Task::all();

		return response()->json($task);
	}
	
	public function update(Request $request,$task_id){
		$task = Task::find($task_id);
		$task->task = $request->task;
		$task->description = $request->description;
		$task->save();
		return \Response::json($task);
	}
	
	public function delete($task_id){
		$task = Task::destroy($task_id);

		return \Response::json($task);
	}
}
