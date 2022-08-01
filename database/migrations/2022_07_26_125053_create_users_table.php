<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Role_id')->nullable();
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('gender')->nullable();
            $table->string('ProfileImageUrl')->nullable();
            $table->string('Address')->nullable();
            $table->string('LicenseNumber')->nullable();
            $table->string('Title');
            $table->unsignedBigInteger('Hospital_Id');
            $table->string('password');
            $table->string('LastLoginDate');
            $table->string('JoinDate');
            $table->string('IsActive');
            $table->string('IsNotLocked')->nullable();
            $table->string('IsAccountNonExpired')->nullable();
            $table->string('IsCredentialsNonExpired')->nullable();
            $table->string('IsAccountNonLocked')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();


            // $table->foreign('Role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('Hospital_Id')->references('id')->on('hospital')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }


}
