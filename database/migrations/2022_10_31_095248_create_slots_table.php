<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->string('day')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->default('Available')->nullable();
            $table->unsignedBigInteger('User_Id')->nullable();
            $table->unsignedBigInteger('Createdby_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();

            $table->timestamps();

            $table
            ->foreign('Createdby_Id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

            $table
            ->foreign('User_Id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

            $table
            ->foreign('Hospital_Id')
            ->references('id')
            ->on('hospital')
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
        Schema::dropIfExists('slots');
    }
}
