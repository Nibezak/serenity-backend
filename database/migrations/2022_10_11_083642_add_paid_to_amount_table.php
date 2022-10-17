<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidToAmountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('typeappointments', function (Blueprint $table) {
            //

            $table->string('Amount')->default(0)->nullable();
            $table->string('Description')->nullable();
            $table->string('Currency')->default('RWF')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('typeappointments', function (Blueprint $table) {
            //
        });
    }
}
