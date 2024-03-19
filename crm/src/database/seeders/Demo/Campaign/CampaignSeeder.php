<?php
namespace Database\Seeders\Demo\Campaign;

use App\Models\Campaign\Campaign;
use App\Models\Email\EmailLog;
use App\Repositories\App\StatusRepository;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\MessageHelper;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignSeeder extends Seeder
{
    /**
     * @var Generator
     */
    private $faker;

    private $statuses;
    /**
     * @var Generator
     */

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }


    use DisableForeignKeys,
        MessageHelper;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->startMessage();
        $this->disableForeignKeys();

        $this->statuses = resolve(StatusRepository::class)
            ->email()
            ->pluck('name', 'id');

        factory(Campaign::class, 20)->create()->each(function (Campaign $campaign) {
            $campaign->audiences()->createMany(
                $this->generateAudiences()
            );
            if ($campaign->status->name !== 'status_draft') {
                $this->generateEmailLogs($campaign);
            }
        });

        $this->enableForeignKeys();
        $this->endMessage();
    }

    private function generateAudiences()
    {
        return [
            [
                'audience_type' => 'list',
                'audiences' => DB::table('lists')
                    ->select('id')
                    ->inRandomOrder()
                    ->take(rand(2, 6))
                    ->pluck('id')
            ],
            [
                'audience_type' => 'subscriber',
                'audiences' => DB::table('subscribers')
                    ->select('id')
                    ->inRandomOrder()
                    ->take(rand(30, 400))
                    ->pluck('id')

            ]
        ];
    }

    private function generateEmailLogs(Campaign $campaign)
    {
        $subscribersIds = collect($campaign->subscriberAudiences());

        $subscribersIds->each(function ($id) use ($campaign) {
            $status_id = $this->faker->randomElement($this->statuses->keys());
            $open_count = 0;
            $click_count = 0;
            if ($this->statuses[$status_id] === 'status_delivered') {
                $open_count = $this->faker->randomElement([0, rand(0, 10)]);
                $click_count = $this->faker->randomElement([0, rand(0, $open_count)]);
            }
            factory(EmailLog::class, 1)->create([
                'subscriber_id' => $id,
                'campaign_id' => $campaign->id,
                'email_content' => $campaign->template_content,
                'status_id' => $status_id,
                'open_count' => $open_count,
                'click_count' => $click_count,
            ]);
        });
    }
}
