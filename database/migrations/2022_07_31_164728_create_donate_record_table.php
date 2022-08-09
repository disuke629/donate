<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonateRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donate_record', function (Blueprint $table) {
            $table->id();
            $table->integer('server_id');
            $table->integer('product_id');
            $table->integer('pay_method');
            $table->string('account');
            $table->string('number');
            $table->integer('amount');
            $table->json('blue_callback')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('donate_record');
    }
}
