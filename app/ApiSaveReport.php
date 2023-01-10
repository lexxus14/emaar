<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiSaveReport extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'status',
        'sched',
        'unitno',
        'leasecode',
        'totalTransaction',
        'total',
        'apisetup_id'
    ];
}
