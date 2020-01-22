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
            $table->unsignedBigInteger('bitform_bittable_id');
            $table->foreign('bitform_bittable_id')
                ->references('bittable_id')->on('bittable')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('bitform_label')->nullable();
            $table->string('bitform_input')->nullable();
            $table->string('bitform_type')->nullable();
            $table->string('bitform_url')->nullable();
            $table->jsonb('bitform_rules')->nullable();
            $table->jsonb('bitform_messages')->nullable();
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
