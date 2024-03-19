<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email_id', 150)->nullable();
            $table->foreignId('subscriber_id')->constrained();
            $table->foreignId('campaign_id')->constrained();
            $table->timestamp('email_date');
            $table->longText('email_content')->nullable();
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->string('delivery_server');
            $table->string('location')->nullable();
            $table->foreignId('status_id')->nullable()->constrained();
            $table->tinyInteger('is_marked_as_spam')->default(0);
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
        Schema::dropIfExists('email_logs');
    }
}
