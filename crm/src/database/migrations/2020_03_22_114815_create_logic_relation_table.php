<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogicRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logic_relation', function (Blueprint $table) {

            $table->foreignId('name_id')
                ->references('id')
                ->on('logic_names')
                ->onDelete('cascade');

            $table->foreignId('operator_id')
                ->references('id')
                ->on('logic_operators')
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
        Schema::dropIfExists('logic_relation');
    }
}
