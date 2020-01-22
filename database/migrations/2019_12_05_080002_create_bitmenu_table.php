<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitmenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitmenu', function (Blueprint $table) {
            $table->bigIncrements('bitmenu_id');
            $table->unsignedBigInteger('bitmenu_parent_id')->nullable();
            $table->foreign('bitmenu_parent_id')
                ->references('bitmenu_id')->on('bitmenu')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('bitmenu_bittable_id')->nullable();
            $table->foreign('bitmenu_bittable_id')
                ->references('bittable_id')->on('bittable')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('bitmenu_index')->default(0);
            $table->string('bitmenu_icon')->default('fa fa-table')->nullable();
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
        Schema::dropIfExists('bitmenu');
    }
}
