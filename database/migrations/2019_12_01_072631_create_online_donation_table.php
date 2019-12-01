<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnlineDonationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Online_Donation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('donor_id');
            $table->float('amount', 8, 2);
            $table->date('added_on');
            $table->enum('payment', ['started','failed','success'])->default('started');
            $table->string('payment_method', 50)->nullable();
            $table->string('currency', 5)->default('INR');
            $table->enum('gateway', ['tech_process','simply_donor','razorpay','payu','other'])->default('other');
            $table->string('gateway_transaction_id', 100)->nullable();
            $table->mediumText('info')->nullable();
            $table->bigInteger('fundraiser_id')->nullable();
            $table->integer('repeat_count')->default(1);
            $table->float('unit_amount', 8, 2);
            $table->float('conversion_rate', 4, 2)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Online_Donation');
    }
}
