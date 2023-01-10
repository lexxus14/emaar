<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|

Route::get('/', function () {
    return view('welcome');
});

*/

Route::get('/', 'HomeController@index');


Route::get('/tasks/{task_id?}','TaskController@edit');
Route::post('/tasks','TaskController@store');
Route::put('/tasks/{task_id?}','TaskController@update');
Route::delete('/tasks/{task_id?}','TaskController@delete');


Auth::routes();

Route::get('/home', 'HomeController@index');
Route::post('/lang','LanguageController@chooser');

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('/viewereport/{id?}', 'HomeController@ViewReport')->name('viewreport');

Route::get('/apisetup','ApiSetupController@index')->name('apisetup');
Route::get('/apisetupadd','ApiSetupController@Add')->name('apisetupadd');
Route::post('/apisetupstore','ApiSetupController@Store')->name('apisetupstore');
Route::get('/apisetupedit/{id?}','ApiSetupController@edit')->name('apisetupedit');
Route::post('/apisetupupdate/{id?}','ApiSetupController@Update')->name('apisetupupdate');
Route::get('/apisetupdelete/{id?}','ApiSetupController@Delete')->name('apisetupdelete');


Route::get('/getBillTypeInfo/{id?}','ApiSetupController@BillTypeInfo')->name('BillTypeInfo');
Route::get('/getApiTypeInfo/{id?}','ApiSetupController@GetApiTypeInfo')->name('ApiTypeInfo');
Route::get('/getBillReport/{id?}','ApiSetupController@GetBillReport')->name('BillReport');
Route::get('/getBillReport/{id?}/{datefrom?}/{dateto?}','ApiSetupController@GetBillReportDate')->name('BillReportDate');

Route::get('/apiemaarsubmit','ApiSetupController@EmaarApiSendReport')->name('apiemaarsubmit');

Route::get('/sgetBillTypeInfo/{id?}','ApiController@BillTypeInfo')->name('SBillTypeInfo');
Route::get('/sgetApiTypeInfo/{id?}','ApiController@GetApiTypeInfo')->name('SApiTypeInfo');
Route::get('/sgetBillReport/{id?}','ApiController@GetBillReport')->name('SBillReport');


Route::get('/sgetApiTypeInfo/daily/{id?}','ApiController@DailyGetApiTypeInfo')->name('SDailyApiTypeInfo');
Route::get('/sgetBillReport/daily/{id?}','ApiController@GetBillReport')->name('SDailyBillReport');

Route::get('/sgetBillTypeInfo/monthly/{id?}','ApiController@MonthlyBillTypeInfo')->name('SMonthlyBillTypeInfo');
Route::get('/sgetApiTypeInfo/monthly/{id?}','ApiController@GetApiMonthyTypeInfo')->name('SMonthlyApiTypeInfo');
Route::get('/sgetBillReport/monthly/{id?}','ApiController@GetMonthlyBillReport')->name('SMonthlyBillReport');

Route::get('/dailysubmit','ApiController@DailyEmaarApiSendReport')->name('dailysubmit');
Route::get('/monthlysubmit','ApiController@MonthlyEmaarApiSendReport')->name('monthlysubmit');

Route::get('/submit','ApiController@EmaarApiSendReport')->name('submit');
Route::post('/savesubmit','ApiController@StoreApiStatus')->name('savesubmit');