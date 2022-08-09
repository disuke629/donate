<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url_suffix')->unique();
            $table->string('sql_ip');
            $table->string('sql_port');
            $table->string('sql_database');
            $table->string('sql_username');
            $table->string('sql_password');
            $table->string('sql_payment_table');
            $table->integer('blue_online')->default(0);
            $table->string('blue_number');
            $table->string('blue_hash_key');
            $table->string('blue_hash_iv');
            $table->integer('sort')->default(0);
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
        Schema::dropIfExists('server');
    }
}
