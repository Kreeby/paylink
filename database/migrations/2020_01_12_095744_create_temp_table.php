<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp', function (Blueprint $table) {
            $table->string('generated_code');
            $table->bigIncrements('id');
            $table->timestamps();
        $table->integer('amount');
        $table->string('merchant_name');
        $table->string('name');
        $table->string('code');
        $table->string('currency');
        $table->string('transaction_id');
        $table->string('logo_url');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp');
    }
}
