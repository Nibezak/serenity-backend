<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeappointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('typeappointments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->unsignedBigInteger('createdBy_Id')->nullable();
            $table->timestamps();


            $table
            ->foreign('Hospital_Id')
            ->references('id')
            ->on('hospital')
            ->onDelete('cascade');


            $table
            ->foreign('createdBy_Id')
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
        Schema::dropIfExists('typeappointments');
    }
}
