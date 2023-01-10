<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiSaveReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_save_reports', function (Blueprint $table) {
            $table->bigincrements('id');
            $table->string('status');
            $table->string('sched');
            $table->string('unitno');
            $table->string('leasecode');
            $table->string('totalTransaction');
            $table->string('total');
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('apisetup_id');
            $table->foreign('apisetup_id')->references('id')->on('api_setups')->onDelete('cascade');
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
        Schema::dropIfExists('api_save_reports');
    }
}
