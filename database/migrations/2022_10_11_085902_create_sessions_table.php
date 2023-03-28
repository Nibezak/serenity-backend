<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('StartedBy_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Doctor_Id')->nullable();
            $table->unsignedBigInteger('Insurance_Id')->nullable();
            $table->timestamps();

            $table
            ->foreign('StartedBy_Id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

            

            $table
            ->foreign('Hospital_Id')
            ->references('id')
            ->on('hospital')
            ->onDelete('cascade');

            
            $table
            ->foreign('Patient_Id')
            ->references('id')
            ->on('patients')
            ->onDelete('cascade');




            $table
            ->foreign('Insurance_Id')
            ->references('id')
            ->on('patientinsurance')
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
        Schema::dropIfExists('sessions');
    }
}
