<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiSetupBillForm extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'billguid',
        'apisetup_id',
        'isInputOutput'
    ];
}
