<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssigneddoctorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigneddoctor', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('Doctor_Id')->nullable();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('AssignedBy_Id')->nullable();
            $table->string('Date')->nullable();
            $table->string('Status')->default('InActive');
            $table->timestamps();

            $table
            ->foreign('Hospital_Id')
            ->references('id')
            ->on('hospital')
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
            ->foreign('AssignedBy_Id')
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
        Schema::dropIfExists('assigneddoctor');
    }
}
