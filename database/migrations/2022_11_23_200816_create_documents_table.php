<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->unsignedBigInteger('Patient_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('AssignedDoctor_Id')->nullable();
            $table->unsignedBigInteger('Createdby_Id')->nullable();
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
        Schema::dropIfExists('documents');
    }
}
