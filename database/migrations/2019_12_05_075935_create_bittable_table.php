<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBittableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bittable', function (Blueprint $table) {
            $table->bigIncrements('bittable_id');
            $table->unsignedBigInteger('bittable_parent_id')->nullable();
            $table->foreign('bittable_parent_id')
                ->references('bittable_id')->on('bittable')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('bittable_name')->unique();
            $table->string('bittable_type')->nullable();
            $table->text('bittable_length')->nullable();
//            $table->text('bittable_default')->nullable();
            $table->text('bittable_attributes')->nullable();
            $table->enum('bittable_join',['left','right','inner'])->nullable();
            $table->unsignedBigInteger('bittable_join_to_id')->nullable();
            $table->unsignedBigInteger('bittable_join_value')->nullable();
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
        Schema::dropIfExists('bittable');
    }
}
