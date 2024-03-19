<?php
namespace Database\Seeders\Demo;

use App\Models\Core\Builder\Form\CustomFieldValue;
use App\Models\Subscriber\Subscriber;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\MessageHelper;
use Illuminate\Database\Seeder;

class SubscriberSeeder extends Seeder
{
    use DisableForeignKeys, MessageHelper;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->startMessage();
        $this->disableForeignKeys();
        factory(Subscriber::class, 1000)->create()->each(function (Subscriber $subscriber) {
            $subscriber->customFields()
                ->save(
                    factory(CustomFieldValue::class)
                        ->make()
                );
        });
        $this->endMessage();
//        echo "\e[1;32mSubscriber seeded.\n";
      /*  $array = [
            [
                'email' => 'success@simulator.amazonses.com',
                'first_name' => 'John',
                'last_name' => 'Doe Success',
                'status_id' => resolve(\App\Repositories\App\StatusRepository::class)->subscriberActive(),
                'brand_id' => 1
            ],
            [
                'email' => 'bounce@simulator.amazonses.com',
                'first_name' => 'John',
                'last_name' => 'Doe Bounce',
                'status_id' => resolve(\App\Repositories\App\StatusRepository::class)->subscriberActive(),
                'brand_id' => 1
            ],
            [
                'email' => 'complaint@simulator.amazonses.com',
                'first_name' => 'John',
                'last_name' => 'Doe Complaint',
                'status_id' => resolve(\App\Repositories\App\StatusRepository::class)->subscriberActive(),
                'brand_id' => 1
            ],
            [
                'email' => 'suppressionlist@simulator.amazonses.com',
                'first_name' => 'John',
                'last_name' => 'Doe Suppression',
                'status_id' => resolve(\App\Repositories\App\StatusRepository::class)->subscriberActive(),
                'brand_id' => 1
            ],
        ];
        Subscriber::insert($array);*/
        $this->enableForeignKeys();
    }
}
