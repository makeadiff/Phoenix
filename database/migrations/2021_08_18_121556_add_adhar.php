<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdhar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Donut_Donor', function (Blueprint $table) {
            $table->string('adhar',20)->after('pan')->nullable()->default(null);
        });

        Schema::table('Class', function (Blueprint $table) {
            // $table->bigInteger('lesson_id')->default(0)->change();
            // $table->integer('class_satisfaction')->nullable()->default(null)->change();
            // $table->integer('cancel_reason')->nullable()->default(null)->change();
            // $table->bigInteger('updated_by_teacher')->default(0)->change();
            // $table->bigInteger('updated_by_mentor')->default(0)->change();
            DB::statement("ALTER TABLE `Class` CHANGE `lesson_id` `lesson_id` INT NULL DEFAULT 0, 
                                               CHANGE `class_satisfaction` `class_satisfaction` INT NULL DEFAULT NULL,
                                               CHANGE `cancel_reason` `cancel_reason` VARCHAR(200) NULL DEFAULT NULL,
                                               CHANGE `updated_by_teacher` `updated_by_teacher` INT NULL DEFAULT 0,
                                               CHANGE `updated_by_mentor` `updated_by_mentor` INT NULL DEFAULT 0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Donut_Donor', function (Blueprint $table) {
            $table->dropColumn('adhar');
        });
    }
}
