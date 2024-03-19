<?php
namespace Database\Seeders\Status;

use App\Models\Core\Status;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        Status::query()->truncate();
        $statuses = [
            [
                'name' => 'status_deleted',
                'class' => '',
                'type' => ''
            ],
            [
                'name' => 'status_processing',
                'class' => 'purple',
                'type' => 'campaign'
            ],
            [
                'name' => 'status_sent',
                'class' => 'success',
                'type' => 'campaign'
            ],
            [
                'name' => 'status_draft',
                'class' => 'warning',
                'type' => 'campaign'
            ],
            [
                'name' => 'status_confirmed',
                'class' => 'primary',
                'type' => 'campaign'
            ],
            [
                'name' => 'status_dynamic',
                'class' => '',
                'type' => 'list'
            ],
            [
                'name' => 'status_imported',
                'class' => '',
                'type' => 'list'
            ],
            [
                'name' => 'status_subscribed',
                'class' => 'success',
                'type' => 'subscriber'
            ],
            [
                'name' => 'status_unsubscribed',
                'class' => 'purple',
                'type' => 'subscriber'
            ],
            [
                'name' => 'status_blacklisted',
                'class' => 'secondary',
                'type' => 'subscriber'
            ],
            [
                'name' => 'status_active',
                'type' => 'user',
                'class' => 'success'
            ],
            [
                'name' => 'status_inactive',
                'type' => 'user',
                'class' => 'danger'
            ],
            [
                'name' => 'status_invited',
                'type' => 'user',
                'class' => 'purple'
            ],
            [
                'name' => 'status_sent',
                'class' => 'success',
                'type' => 'email'
            ],
            [
                'name' => 'status_bounced',
                'class' => 'danger',
                'type' => 'email'
            ],
            [
                'name' => 'status_delivered',
                'class' => 'info',
                'type' => 'email'
            ],
            [
                'name' => 'status_active',
                'class' => 'success',
                'type' => 'brand'
            ],
            [
                'name' => 'status_inactive',
                'class' => 'danger',
                'type' => 'brand'
            ],
        ];

        Status::query()->insert($statuses);

        $this->enableForeignKeys();
    }
}
