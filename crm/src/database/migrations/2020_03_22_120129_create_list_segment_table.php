<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListSegmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_segment', function (Blueprint $table) {

            $table->foreignId('list_id')
                ->references('id')
                ->on('lists')
                ->onDelete('cascade');

            $table->foreignId('segment_id')
                ->references('id')
                ->on('segments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_segment');
    }
}
