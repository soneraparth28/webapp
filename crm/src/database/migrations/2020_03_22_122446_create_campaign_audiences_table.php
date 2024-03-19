<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignAudiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_audiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')
                ->constrained()
                ->onDelete('CASCADE');

            $table->enum('audience_type', ['list', 'subscriber'])
                ->comment('list/subscriber');

            $table->text('audiences')
                ->comment('Array of subscribers id');
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
        Schema::dropIfExists('campaign_audiences');
    }
}
