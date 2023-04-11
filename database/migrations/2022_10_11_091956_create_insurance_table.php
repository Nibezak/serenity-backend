z<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('CreatedBy_Id')->nullable();
            $table->unsignedBigInteger('Hospital_Id')->nullable();
            $table->string('Name')->nullable();
            $table->timestamps();

            $table
            ->foreign('CreatedBy_Id')
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
        Schema::dropIfExists('insurance');
    }
}
