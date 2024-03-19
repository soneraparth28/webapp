<?php
namespace Database\Seeders\Demo\Segment;

use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\MessageHelper;
use Illuminate\Database\Seeder;
use App\Models\Segment\Segment;

class SegmentTableSeeder extends Seeder
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

        factory(Segment::class, 5)
            ->create();
        $this->enableForeignKeys();
        $this->endMessage();
    }
}
