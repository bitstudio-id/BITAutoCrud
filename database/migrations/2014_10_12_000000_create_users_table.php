<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('user_id');
            $table->string('user_name');
            $table->string('user_avatar');
            $table->string('user_email')->unique();
            $table->string('user_phone')->unique();
            $table->timestamp('user_email_verified_at')->nullable();
            $table->string('user_password');
            $table->timestamp('user_created_at')->nullable();
            $table->timestamp('user_updated_at')->nullable();
            $table->timestamp('user_deleted_at')->nullable();
            $table->unsignedBigInteger('user_created_by')->nullable();
            $table->unsignedBigInteger('user_updated_by')->nullable();
            $table->unsignedBigInteger('user_deleted_by')->nullable();
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
