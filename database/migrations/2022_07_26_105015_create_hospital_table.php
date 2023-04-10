<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital', function (Blueprint $table) {
            $table->id();
            $table->string('TypeOrganization');
            $table->string('PracticeName');
            $table->string('BusinessPhone');
            $table->string('BusinessEmail');
            $table->string('Province');
            $table->string('District');
            $table->string('Sector');
            $table->string('Cell');
            $table->string('Village');
            $table->string('TinNumber')->nullable();
            $table->string('logo')->nullable();;
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital');
    }
}
