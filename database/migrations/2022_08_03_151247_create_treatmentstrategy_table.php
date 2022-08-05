<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreatmentstrategyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treatmentstrategy', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('CreatedBy_Id')->nullable();
            $table->string('Status');
            $table->unsignedBigInteger('Patient_Id')->nullable();
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
            ->foreign('CreatedBy_Id')
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
        Schema::dropIfExists('treatmentstrategy');
    }
}
