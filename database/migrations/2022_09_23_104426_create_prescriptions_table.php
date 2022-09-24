<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('Doctor_Id')->nullable();
            $table->unsignedBigInteger('Drug_Id')->nullable();
            $table->unsignedBigInteger('RecordedBy_Id')->nullable();
            $table->string('Medical_Advices')->nullable();
            $table->string('Description')->nullable();
            $table->json('Diagnosis')->nullable();
            $table->timestamps();


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
            ->foreign('Doctor_Id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');


            $table
            ->foreign('RecordedBy_Id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');


            $table
            ->foreign('Drug_Id')
            ->references('id')
            ->on('drugs')
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
        Schema::dropIfExists('prescriptions');
    }
}
