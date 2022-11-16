<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewcolumnsinappointmenttbToAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            //

            $table->string('Duration')->nullable();
            $table->string('Frequency')->nullable();
            $table->string('Location')->nullable();
            $table->string('AppointmentAlert')->nullable();
            $table->string('ScheduledTime')->nullable();
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->string('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            //
        });
    }
}
