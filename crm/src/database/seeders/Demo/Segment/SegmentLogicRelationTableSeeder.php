<?php
namespace Database\Seeders\Demo\Segment;

use App\Models\Segment\LogicName;
use App\Models\Segment\LogicOperator;
use Database\Seeders\Traits\MessageHelper;
use Illuminate\Database\Seeder;

class SegmentLogicRelationTableSeeder extends Seeder
{
    use MessageHelper;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->startMessage();
        LogicName::all()->map(function (LogicName $logic) {
            $operator = [$logic->type];
            if ($logic->type == 'date') {
                $operator = array_merge($operator, ['date_range']);
            }
            $logic->operator()->sync(
                LogicOperator::query()->whereIn('type', $operator)->pluck('id')->toArray()
            );
        });
        $this->endMessage();
    }
}
