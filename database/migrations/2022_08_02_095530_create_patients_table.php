<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table
            ->string('profileimageUrl')
            ->default('https://i.imgur.com/BKB2EQi.png');
            $table->id();
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('email')->unique();
            $table->string('MobilePhone');
            $table->string('HomePhone')->nullable();
            $table->string('WorkPhone')->nullable();
            $table->string('Dob');
            $table->string('GenderIdentity')->nullable();
            $table->string('AccountNumber')->nullable();
            $table->string('Address')->nullable();
            $table->string('BloodType')->nullable();
            $table->string('Height')->nullable();
            $table->string('Weight')->nullable();
            $table->string('MartialStatus')->nullable();
            $table->string('AdministrativeSex')->nullable();
            $table->string('SexualOrientation')->nullable();
            $table->string('Employment')->nullable();
            $table->string('Languages')->nullable();
            $table->unsignedBigInteger('Createdby_Id')->nullable();
            $table->string('Nationality')->nullable();
            $table->string('SSN')->nullable();
            $table->string('Province');
            $table->string('District');
            $table->string('Sector');
            $table->string('Cell');
            $table->string('Village');
            $table->string('Guardian_Name')->nullable();
            $table->string('Guardian_Phone')->nullable();
            $table->string('StreetCode')->nullable();
            $table->string('Status')->default('InActive');
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('AssignedDoctor_Id')->nullable();
            $table->string('gender')->nullable();
            $table->unsignedBigInteger('lastappoint')->nullable();
            $table->unsignedBigInteger('nextappoint')->nullable();
            $table->timestamps();

            
            $table
            ->foreign('nextappoint')
            ->references('id')
            ->on('appointments')
            ->onDelete('cascade');


            $table
            ->foreign('lastappoint')
            ->references('id')
            ->on('appointments')
            ->onDelete('cascade');

            $table
                ->foreign('Hospital_Id')
                ->references('id')
                ->on('hospital')
                ->onDelete('cascade');
            $table
                ->foreign('AssignedDoctor_Id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('Createdby_Id')
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
        Schema::dropIfExists('patients');
    }
}
