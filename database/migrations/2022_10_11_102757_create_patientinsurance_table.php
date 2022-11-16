<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientinsuranceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patientinsurance', function (Blueprint $table) {
            $table->id();
            $table->string("InsuranceCode")->default(0)->nullable();
            $table->string('Name')->nullable();
            $table->string('Compliment')->nullable();
            $table->unsignedBigInteger('CreatedBy_Id')->nullable();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->timestamps();


            $table
            ->foreign('CreatedBy_Id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

            $table
            ->foreign('Patient_Id')
            ->references('id')
            ->on('patients')
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
        Schema::dropIfExists('patientinsurance');
    }
}
