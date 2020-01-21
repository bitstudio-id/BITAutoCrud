<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitform', function (Blueprint $table) {
            $table->bigIncrements('bitform_id');
            $table->string('bitform_label');
            $table->string('bitform_input');
            $table->string('bitform_type');
            $table->string('bitform_url');
            $table->jsonb('bitform_rules');
            $table->jsonb('bitform_messages');
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
        Schema::dropIfExists('bitform');
    }
}
