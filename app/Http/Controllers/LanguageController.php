<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;

class LanguageController extends Controller
{
    //
    public function chooser(Request $request){

    	// var_dump($request->session()->all());

		foreach ($request->locale as $key) {
			# code...
			if(!is_null($key))
			{
				// echo $key;
				Session::set('locale',$key);				
				// echo trans('label.task');
			}
		}
		return redirect ('/');
	}

}
