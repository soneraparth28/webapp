<?php
namespace Database\Seeders\Builder;

use App\Models\Core\Builder\Form\CustomFieldType;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class CustomFieldTypeSeeder extends Seeder
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

        $field_types = [
            ['name' => 'text'],
            ['name' => 'textarea'],
            ['name' => 'radio'],
            ['name' => 'select'],
            ['name' => 'date'],
            ['name' => 'number'],
        ];

        CustomFieldType::query()->insert($field_types);
        $this->enableForeignKeys();

    }
}
