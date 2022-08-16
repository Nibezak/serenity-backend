<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePtreatmentplanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ptreatmentplan', function (Blueprint $table) {
            $table->id();
            $table->string('Note_Type');
            $table->json('Diagnosis_Id')->nullable();
            $table->string('Diagnositic_Justification')->nullable();
            $table->json('Treatmentstrategy_Id')->nullable();
            $table->string('Presenting_Problem')->nullable();
            $table->string('Treatment_Goals')->nullable();
            $table->json('Objective_Id')->nullable();
            $table->json('Frequency_Treatment_Id')->nullable();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('CreatedBy_Id')->nullable();
            $table->unsignedBigInteger('Doctor_id')->nullable();
            $table->unsignedBigInteger('Signator_Id')->nullable();
            $table->string('Status');
            $table->string('Date');
            $table->string('Time');

            $table->timestamps();

            $table
            ->foreign('Patient_Id')
            ->references('id')
            ->on('patients')
            ->onDelete('cascade');

            // $table
            // ->foreign('Treatmentstrategy_Id')
            // ->references('id')
            // ->on('treatmentstrategy')
            // ->onDelete('cascade');


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


            $table
            ->foreign('Signator_Id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');


            // $table
            // ->foreign('Diagnosis_Id')
            // ->references('id')
            // ->on('diagnosis')
            // ->onDelete('cascade');


            // $table
            // ->foreign('Objective_Id')
            // ->references('id')
            // ->on('noteobjective')
            // ->onDelete('cascade');


            // $table
            // ->foreign('Frequency_Treatment_Id')
            // ->references('id')
            // ->on('frequencytreatment')
            // ->onDelete('cascade');



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ptreatmentplan');
    }
}
