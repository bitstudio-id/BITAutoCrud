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
            $table->string('bittable_name');
            $table->string('bittable_type')->nullable();
            $table->text('bittable_length');
            $table->text('bittable_default');
            $table->enum('bittable_join',['left','right','inner'])->nullable();
            $table->unsignedBigInteger('bittable_join_to_id')->nullable();
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
