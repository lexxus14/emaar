<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiSetupBillFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_setup_bill_forms', function (Blueprint $table) {
            $table->bigincrements('id');
            $table->string('name');
            $table->string('billguid');
            $table->unsignedBigInteger('apisetup_id');
            $table->foreign('apisetup_id')->references('id')->on('api_setups')->onDelete('cascade');;  
            $table->string('isInputOutput');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_setup_bill_forms');
    }
}
