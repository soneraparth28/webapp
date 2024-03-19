<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->longText('template_content')->nullable();
            $table->string('time_period')->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->time('campaign_start_time')->nullable();
            $table->string('current_status')->default('active'); //active,paused,archived

            $table->foreignId('status_id')
                ->constrained();

            $table->foreignId('brand_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');

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
        Schema::dropIfExists('campaigns');
    }
}
