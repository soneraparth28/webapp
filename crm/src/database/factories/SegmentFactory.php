<?php

use App\Models\Core\App\Brand\Brand;
use App\Models\Segment\LogicName;
use App\Models\Subscriber\Subscriber;
use Carbon\Carbon;
use Faker\Generator as Faker;
use App\Models\Segment\Segment;

$factory->define(Segment::class, function (Faker $faker) {
    return [
        'name' => $faker->jobTitle,
        'brand_id' => Brand::query()->inRandomOrder()->first()->id,
        'created_by' => 1,
        'segment_logic' => LogicName::all()->map(function (LogicName $logic) {
            return [
                logicGenerator($logic),
                logicGenerator($logic)
            ];
        })
    ];
});


function logicGenerator(LogicName $logic) {
        $operator = $logic->operator()->inRandomOrder()->first();
        $value = '';
        if ($operator->type == 'date'){
            $value = Carbon::now()
                ->addDays(10)
                ->format('Y-m-d');
        }elseif ($operator->type == 'date_range') {
            $value = [
                Carbon::now()
                    ->subYears(20)
                    ->format('Y-m-d'),

                Carbon::now()
                    ->addDays(50)
                    ->format('Y-m-d')
            ];
        }elseif ($operator->type == 'text') {
            if ($logic->name = 'first_name') {
                $value = Subscriber::query()
                    ->selectRaw('SUBSTR(first_name, 1, 2) as name')
                    ->groupByRaw('SUBSTR(first_name, 1, 2)')
                    ->inRandomOrder()
                    ->first()->name;
            }elseif ($logic->name == 'last_name'){
                $value = Subscriber::query()
                    ->selectRaw('SUBSTR(last_name, 1, 2) as name')
                    ->groupByRaw('SUBSTR(last_name, 1, 2)')
                    ->inRandomOrder()
                    ->first()->name;
            }elseif ($logic->name == 'email'){
                $value = Subscriber::query()
                    ->selectRaw('SUBSTR(SUBSTRING_INDEX(email, \'@\', 1), 1, 2) as email')
                    ->groupByRaw('SUBSTR(SUBSTRING_INDEX(email, \'@\', 1), 1, 2) as name')
                    ->inRandomOrder()
                    ->first()->email;
            }
        }

        return [
            $logic->name,
            $operator->name,
            $value
        ];
}
