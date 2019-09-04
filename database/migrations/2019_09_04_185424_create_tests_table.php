<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->bigIncrements('test_id');
            $table->bigInteger('test_number');
            $table->boolean('test_boolean');
            $table->string('test_string');
            $table->enum('test_enum',['value1','value2']);
            $table->date('test_date');
            $table->time('test_time');
            $table->dateTime('test_date_time');
            $table->text('test_images');
            $table->longText('test_description');
            $table->timestamp('test_created_at')->nullable();
            $table->timestamp('test_updated_at')->nullable();
            $table->timestamp('test_deleted_at')->nullable();
            $table->unsignedBigInteger('test_created_by')->nullable();
            $table->unsignedBigInteger('test_updated_by')->nullable();
            $table->unsignedBigInteger('test_deleted_by')->nullable();
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
        Schema::dropIfExists('tests');
    }
}
