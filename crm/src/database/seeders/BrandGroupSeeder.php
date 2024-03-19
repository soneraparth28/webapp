<?php
namespace Database\Seeders;

use App\Models\Core\App\Brand\BrandGroup;
use Illuminate\Database\Seeder;

class BrandGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BrandGroup::create([
            'name' => 'General',
            'created_by' => 1
        ]);
    }
}
