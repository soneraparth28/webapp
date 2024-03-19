<?php

namespace App\Jobs\App\Template;

use App\Helpers\Core\Traits\FileHandler;
use App\Managers\Template\ThumbnailGenerate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ThumbnailGenerateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,FileHandler;

    protected $model;
    private $options = [
        'contentKey' => 'custom_content',
        'fileableType' => 'template',
        'fileableRelation' => 'thumbnail',
    ];

    public function __construct( $model, $options = [] )
    {
        $this->model = $model;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $factory = resolve(ThumbnailGenerate::class)
            ->setModel($this->model);

        foreach ($this->options as $property => $value) {
            $setter = "set" . ucfirst($property);
            if (method_exists($factory, $setter))
                $factory->{$setter}($value);
        }

        $factory->process();
    }



}
