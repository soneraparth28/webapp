<?php
namespace Database\Seeders\Demo\Lists;

use App\Models\Lists\Lists;
use App\Models\Segment\Segment;
use App\Models\Subscriber\Subscriber;
use App\Repositories\Lists\ListRepository;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\MessageHelper;
use Illuminate\Database\Seeder;

class ListSeeder extends Seeder
{
    use DisableForeignKeys,
        MessageHelper;
    protected  $repository;

    public function __construct(ListRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        $this->startMessage();
        $this->disableForeignKeys();

        factory(Lists::class, 20)->create()->each(function (Lists $list) {
            $subscribers = $this->generateSubscribers($list);
            $this->generateSegments($list, $subscribers);
        });

        $this->enableForeignKeys();
        $this->endMessage();
    }

    public function generateSegments(Lists $list, $existingSubscribers)
    {
        $list->segments()->attach(
            Segment::inRandomOrder()
                ->take(1)
                ->get()
                ->pluck('id')
        );

        $list = $list->refresh();
        if ($list->type == 'imported')
            $list->subscribers()->sync(collect($this->repository
                ->setModel($list)
                ->gatherSubscribers($existingSubscribers))->unique());
    }

    public function generateSubscribers(Lists $list)
    {
        return $list->subscribers()->sync(
            Subscriber::inRandomOrder()
                ->take(rand(9, 45))
                ->pluck('id')
        )['attached'];
    }
}
