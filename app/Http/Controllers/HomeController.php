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
class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $SaveReport = DB::table('api_save_reports')
           ->select('api_save_reports.id','api_save_reports.created_at','api_save_reports.status','api_save_reports.unitno','sched','api_save_reports.leasecode','totalTransaction','total','apiname')
           ->join('api_setups','api_setups.id','=','api_save_reports.apisetup_id')
           ->orderBy('api_save_reports.created_at', 'desc')
           ->paginate();
        return view('home',compact('SaveReport'));
    }

    public function ViewReport($id){
        $SaveReport = DB::table('api_save_reports')
         ->select('api_save_reports.id',
          'api_save_reports.created_at',
          'api_save_reports.status',
          'api_save_reports.unitno',
          'sched',
          'api_save_reports.leasecode',
          'totalTransaction',
          'total',
          'remarks',
          'apiname')
         ->join('api_setups','api_setups.id','=','api_save_reports.apisetup_id')
         ->where('api_save_reports.id','=',$id)->get();
      return \Response::json($SaveReport);
    }
}
