<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactnoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contactnote', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('CreatedBy_Id')->nullable();
            $table->unsignedBigInteger('Doctor_Id')->nullable();
            $table->unsignedBigInteger('Signator_Id')->nullable();
            $table->string('Note_Type');
            $table->string('DateTime');
            $table->string('Visibility');
            $table->string('Status');
            $table->string('ContactName');
            $table->string('RelationshipToPatient');
            $table->string('MethodCommunication');
            $table->string('ReasonCommunication');
            $table->string('TimeSpent');
            $table->string('CommunicationDetails');

            $table->timestamps();

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

            $table
                ->foreign('Doctor_Id')
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
        Schema::dropIfExists('contactnote');
    }
}
