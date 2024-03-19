<?php
namespace Database\Seeders\Template;

use App\Helpers\Core\Traits\FileHandler;
use App\Managers\Template\ThumbnailGenerate;

use App\Models\Template\Template;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TemplateTableSeeder extends Seeder
{
    use DisableForeignKeys, FileHandler;
    /**
     * @var Template
     */
    private $template;

    public function __construct(Template $template)
    {

        $this->template = $template;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        collect([
            [
                'subject' => 'Get 50% OFF!!!',
                'default_content' => file_get_contents(database_path('factories/templates/get_50_off.html'))
            ],
            [
                'subject' => 'Its Your Birthday',
                'default_content' => file_get_contents(database_path('factories/templates/its_your_birthday.html'))
            ],
            [
                'subject' => 'We miss you',
                'default_content' => file_get_contents(database_path('factories/templates/we_miss_you.html'))
            ],
            [
                'subject' => 'Photography Advertisement',
                'default_content' => file_get_contents(database_path('factories/templates/photography_advertisement.html'))
            ],
        ])->each(function ($row) {
            if (app()->runningInConsole()) {
                echo  "\e[1;33mSeeding: \t {$row['subject']}\n";
            }

            $this->template->forceFill($row)->save();

//            resolve(ThumbnailGenerate::class)
//                ->setMaxExecutionTime(120)
//                ->setModel($this->template)
//                ->setFileablePath('templates')
//                ->setContentKey('default_content')
//                ->process();

            $this->template = new Template();

            if (app()->runningInConsole()) {
                echo "\e[1;32mSeeded: \t {$row['subject']} \n";
            }
        });



        $this->enableForeignKeys();
    }
}
