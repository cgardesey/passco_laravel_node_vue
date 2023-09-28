<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');

            $table->string('paymentid')->nullable();
            $table->string('name')->nullable();
            $table->string('mobileno')->nullable();
            $table->float('chargeamount')->nullable();
            $table->string('description')->nullable();
            $table->text('message')->nullable();
            $table->boolean('transactionsuccessful')->default(false);
            $table->bigInteger('time', false, true)->default(0);
            $table->boolean('expired')->default(false);

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
        Schema::dropIfExists('transactions');
    }
}
