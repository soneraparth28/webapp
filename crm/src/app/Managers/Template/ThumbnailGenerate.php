<?php


namespace App\Managers\Template;


use App\Helpers\Core\Traits\FileHandler;
use Illuminate\Support\Str;
use Knp\Snappy\Image;

class ThumbnailGenerate
{
    use FileHandler;
    protected $max_execution_time = 120;
    protected $model;
    protected $contentKey = 'custom_content';
    protected $binary = 'vendor/bin/wkhtmltoimage-amd64';


    protected $fileablePath = '';
    protected $fileableRelation = 'thumbnail';
    protected $fileableType = 'template';

    protected $options = [
        'height' => '430',
        'width' => '310',
        'zoom' => '0.5',
        'encoding' => 'UTF-8'
    ];

    public function __construct()
    {
        $this->setDefaultPath();

    }

    public function process(callable $callback = null)
    {
        ini_set('max_execution_time', $this->max_execution_time);

        $destination = storage_path('app/public' . $this->fileablePath);

        $content = strtr($this->model->{$this->contentKey}, [
            'https' => 'http'
        ]);

        $snappy = new Image(base_path($this->binary));

        try {
            $snappy->generateFromHtml($content, $destination, $this->options);
        }
        catch (\RuntimeException $exception) {
            logger($exception->__toString());
        }
        $callback
            ? $callback($this->fileablePath)
        : $this->saveThumbnail();

    }

    private function saveThumbnail()
    {
        $relation = $this->fileableRelation;
        if ((bool)$this->model->$relation) {
            $this->deleteFile($this->model->$relation->path);
            $this->model->{$relation}()->delete();
        }

        $this->model->{$relation}()->create([
            'path' => '/storage' . $this->fileablePath,
            'type' => $this->fileableType
        ]);
    }


    public function setContentKey(string $contentKey)
    {
        $this->contentKey = $contentKey;
        return $this;
    }


    public function setBinary(string $binary)
    {
        if (filled($binary)) {
            $this->binary = $binary;
        }

        return $this;
    }

    public function setFileableType(string $type)
    {
        if (filled($type)) {
            $this->fileableType = $type;
        }
        return $this;
    }

    public function setFileableRelation(string $relation)
    {
        if (filled($relation)) {
            $this->fileableRelation = $relation;
        }

        return $this;
    }

    public function setFileablePath(string $path)
    {
        if (filled($path)) {
            $path = substr( $path, 0, 1 ) === '/' ? $path : '/' . $path;
            $this->fileablePath =  $path . $this->fileName();
        }

        return $this;
    }

    public function setMaxExecutionTime(int $max_execution_time)
    {
        $this->max_execution_time = $max_execution_time;
        return $this;
    }


    private function setDefaultPath()
    {
        $this->fileablePath = $this->baseFileablePath();
    }

    public function setCustomFolder($dir)
    {
        $this->fileablePath = str_replace('//', '/', $this->baseFileablePath($dir));
        return $this;
    }

    private function baseFileablePath($dir = 'templates')
    {
        return '/files/'. $dir . $this->fileName();
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    private function fileName()
    {
        return '/' . Str::random(40) . '.jpg';
    }
}
