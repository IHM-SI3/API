<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("session_token")->nullable();
            $table->string("firstname");
            $table->string("name");
            $table->string("email");
            $table->string("password");
            $table->string("address")->nullable();
            $table->string("additional_address")->nullable();
            $table->string("city")->nullable();
            $table->string("postal")->nullable();
            $table->date("dob")->nullable();
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
        Schema::dropIfExists('users');
    }
};
