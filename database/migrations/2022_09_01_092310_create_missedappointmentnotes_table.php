<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissedappointmentnotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missedappointmentnotes', function (Blueprint $table) {
            $table->id();
            $table->string('Note_Type');
            $table->unsignedBigInteger('Appointment_Id')->nullable();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('CreatedBy_Id')->nullable();
            $table->unsignedBigInteger('Doctor_id')->nullable();
            $table->string('Visibility')->nullable();
            $table->string('Status')->nullable();
            $table->string('Reason')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();


            $table
            ->foreign('Patient_Id')
            ->references('id')
            ->on('patients')
            ->onDelete('cascade');

            $table
            ->foreign('Appointment_Id')
            ->references('id')
            ->on('appointments')
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

            $table
            ->foreign('Doctor_id')
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
        Schema::dropIfExists('missedappointmentnotes');
    }
}
