<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHospitalRolesCaptureToHospitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hospital', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('IsClinician')->nullable()->default(0);
            $table->unsignedBigInteger('IsReceptionist')->nullable()->default(0);
            $table->unsignedBigInteger('IsFinance')->nullable()->default(0);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hospital', function (Blueprint $table) {
            //
        });
    }
}
