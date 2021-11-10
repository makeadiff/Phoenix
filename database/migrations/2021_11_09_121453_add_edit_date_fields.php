<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEditDateFields extends Migration
{
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Student', function (Blueprint $table) {
            $table->dateTime('updated_on')->nullable()->default(null)->after('added_on');
        });
        Schema::table('UserGroup', function (Blueprint $table) {
            $table->dateTime('added_on')->nullable()->default(null)->after('main');
        });
        Schema::table('Donut_Donor', function (Blueprint $table) {
            $table->dropColumn('adhar');

            $table->enum('id_type', ['0_other','1_pan','2_aadhaar','3_taxpayer_id','4_passport','5_elector_id','6_dl','7_ration_card'])
                        ->nullable()->default(null)->after('nationality');
            $table->string('id_number', 50)->nullable()->default(null)->after('id_type');
        });
        DB::statement("UPDATE Donut_Donor SET id_type = '1_pan', id_number = pan WHERE pan IS NOT NULL AND pan !=''");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Student', function (Blueprint $table) {
            $table->dropColumn('updated_on');
        });
        Schema::table('UserGroup', function (Blueprint $table) {
            $table->dropColumn('added_on');
        });
    }
}
