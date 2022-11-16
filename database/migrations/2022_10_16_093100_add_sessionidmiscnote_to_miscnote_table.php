<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionidmiscnoteToMiscnoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('miscnote', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('Session_Id')->nullable();

            $table
            ->foreign('Session_Id')
            ->references('id')
            ->on('sessions')
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
        Schema::table('miscnote', function (Blueprint $table) {
            //
        });
    }
}
