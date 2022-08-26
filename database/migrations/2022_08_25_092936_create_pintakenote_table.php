<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePintakenoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pintakenote', function (Blueprint $table) {
            $table->id();
            $table->string('Note_Type');
            $table->unsignedBigInteger('Appointment_Id')->nullable();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('CreatedBy_Id')->nullable();
            $table->unsignedBigInteger('Doctor_id')->nullable();
            $table->unsignedBigInteger('Signator_Id')->nullable();
            $table->string('Visibility')->nullable();
            $table->string('Status')->nullable();
            $table->string('PresentingProblem')->nullable();
            $table->string('Orientation')->nullable();
            $table->string('GeneralAppearance')->nullable();
            $table->string('Dress')->nullable();
            $table->string('MotorActivity')->nullable();
            $table->string('InterviewBehavior')->nullable();
            $table->string('Speech')->nullable();
            $table->string('Mood')->nullable();
            $table->string('Affect')->nullable();
            $table->string('Insight')->nullable();
            $table->string('Judgement')->nullable();
            $table->string('Memory')->nullable();
            $table->string('Attention')->nullable();
            $table->string('ThoughtProcess')->nullable();
            $table->string('ThoughtContent')->nullable();
            $table->string('Perception')->nullable();
            $table->string('FunctionalStatus')->nullable();
            $table->string('ObjectiveContent')->nullable();
            $table->string('Identification')->nullable();
            $table->string('HistoryOfPresentProblem')->nullable();
            $table->string('PsychiatricHistory')->nullable();
            $table->string('TraumaHistory')->nullable();
            $table->string('FamilyPsychiatricHistory')->nullable();
            $table->string('MedicalConditionsHistory')->nullable();
            $table->string('CurrentMedications')->nullable();
            $table->string('SubstanceUse')->nullable();
            $table->string('FamilyHistory')->nullable();
            $table->string('SocialHistory')->nullable();
            $table->string('SpiritualFactors')->nullable();
            $table->string('DevelopmentalHistory')->nullable();
            $table->string('EducationalVocationalHistory')->nullable();
            $table->string('LegalHistory')->nullable();
            $table->string('Snap')->nullable();
            $table->string('OtherImportantInformation')->nullable();
            $table->string('Plan')->nullable();
            $table->string('DiagnosticJustification')->nullable();
            $table->string('DateTimeScheduled')->nullable();
            $table->string('DateTimeOccured')->nullable();
            $table->json('Diagnosis')->nullable();
            $table->json('RiskAssessment')->nullable();
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


            $table
            ->foreign('Signator_Id')
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
        Schema::dropIfExists('pintakenote');
    }
}
