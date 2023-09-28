<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('questionid')->nullable();
            $table->string('question')->nullable();
            $table->longText('picture')->nullable();
            $table->string('videopath')->nullable();
            $table->string('answer')->nullable();
            $table->integer('isfirstquestion')->nullable();
            $table->integer('islastquestion')->nullable();
            $table->integer('ismcq')->nullable();
            $table->integer('isfirstmcq')->nullable();
            $table->integer('islastmcq')->nullable();
            $table->integer('isendofquestion')->nullable();

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
        Schema::dropIfExists('questions');
    }
}
