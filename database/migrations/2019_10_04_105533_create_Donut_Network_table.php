<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonutNetworkTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Donut_Network', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 100);
            $table->enum('relationship', array('parent','sibling','acquaintance','friend','relative','other'))->nullable()->default('parent');
            $table->enum('donor_status', array('lead','pledged','disagreed','donated'))->default('lead');
            $table->string('pledged_amount', 100)->nullable();
            $table->enum('pledge_type', array('nach','cash-cheque','online','other'))->nullable();
            $table->string('nach_duration', 3)->nullable()->comment('Number of Months of NACH Donations');
            $table->enum('collection_by', array('self','handover_to_mad'))->nullable();
            $table->text('address', 16777215)->nullable();
            $table->integer('added_by_user_id')->unsigned()->index('added_by_user_id');
            $table->dateTime('follow_up_on')->nullable();
            $table->dateTime('collect_on')->nullable();
            $table->dateTime('added_on');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Donut_Network');
    }
}
