<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDonebyToHospitalTable extends Migration
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

            $table->unsignedBigInteger('Doneby')->nullable();

            $table
            ->foreign('Doneby')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');


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
