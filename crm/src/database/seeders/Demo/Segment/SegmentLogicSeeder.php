<?php
namespace Database\Seeders\Demo\Segment;

use App\Models\Segment\LogicName;
use App\Models\Segment\LogicOperator;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\MessageHelper;
use Illuminate\Database\Seeder;

class SegmentLogicSeeder extends Seeder
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

        LogicOperator::query()->insert(
            $this->generateLogic(config('app-setting.segment_logic_operators'))
        );

        LogicName::query()->insert(
            $this->generateLogic(config('app-setting.segment_names'))
        );
        $this->enableForeignKeys();
        $this->endMessage();
    }

    private function generateLogic($operators)
    {
        $logic_operators = [];
        foreach ($operators as $key => $operator) {
            foreach ($operator as $name) {
                $logic_operators[] = [
                    'name' => $name, 'type' => $key
                ];
            }
        }
        return $logic_operators;
    }
}
