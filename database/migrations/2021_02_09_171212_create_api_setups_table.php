<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_setups', function (Blueprint $table) {
            $table->bigincrements('id');
            $table->string('code');
            $table->string('apiname');
            $table->string('apihost')->nullable();
            $table->string('apikey')->nullable();
            $table->string('unitno')->nullable();
            $table->string('leasecode')->nullable();
            $table->string('startdate')->nullable();
            $table->string('enddate')->nullable();
            $table->string('schedule');
            $table->string('status')->nullable();;
            $table->string('wvat')->nullable();;
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
        Schema::dropIfExists('api_setups');
    }
}
