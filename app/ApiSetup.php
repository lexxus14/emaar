<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiSetup extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 
        'apiname',
        'apihost', 
        'apikey',
        'unitno',
        'leasecode',
        'startdate',
        'enddate',
        'schedule',
        'status',
        'wvat'
    ];
}
