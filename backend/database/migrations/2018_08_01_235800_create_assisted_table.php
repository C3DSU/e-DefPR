<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssistedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assisteds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('cpf', 11)->unique();
            $table->date('birth_date');
            $table->integer('birthplace')->unsigned();
            $table->foreign('birthplace')->references('id')->on('cities');
            $table->string('rg');
            $table->string('rg_issuer');
            $table->enum('gender', ['M', 'F']);
            $table->string('marital_status');
            $table->string('profession');
            //$table->string('counter_part');
            $table->text('note')->nullable();
            $table->json('addresses');
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
        Schema::dropIfExists('assisteds');
    }
}
