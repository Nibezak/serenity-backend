<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaofriskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areaofrisk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Createdby_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->string('Area_of_risk')->nullable();
            $table->string('Visibility')->nullable();
            $table->string('Status')->nullable();
            $table->string('Levelofrisk')->nullable();
            $table->string('Intenttoact')->nullable();
            $table->string('Plantoact')->nullable();
            $table->string('Meanstoact')->nullable();
            $table->string('RisksFactors')->nullable();
            $table->string('ProtectiveFactors')->nullable();
            $table->string('AdditionalDetails')->nullable();
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
        Schema::dropIfExists('areaofrisk');
    }
}
