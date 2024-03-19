<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListSubscriberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_subscriber', function (Blueprint $table) {
            $table->foreignId('list_id')
                ->references('id')
                ->on('lists')
                ->onDelete('cascade');

            $table->foreignId('subscriber_id')
                ->references('id')
                ->on('subscribers')
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
        Schema::dropIfExists('list_subscriber');
    }
}
