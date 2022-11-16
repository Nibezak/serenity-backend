<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Doctor_Id')->nullable();
            $table->unsignedBigInteger('AppointmentType_Id')->nullable();
            $table->string('Status')->nullable();
            $table->unsignedBigInteger('CreatedBy_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->string('link');

            $table->timestamps();

            $table
            ->foreign('AppointmentType_Id')
            ->references('id')
            ->on('typeappointments')
            ->onDelete('cascade');

            $table
            ->foreign('Doctor_Id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

            $table
                ->foreign('Patient_Id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');

                $table
                ->foreign('Hospital_Id')
                ->references('id')
                ->on('hospital')
                ->onDelete('cascade');


                $table
                ->foreign('CreatedBy_Id')
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
        Schema::dropIfExists('appointments');
    }
}
