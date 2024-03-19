<?php


namespace App\Helpers\Traits;


use App\Helpers\Core\Traits\FileHandler as CoreFileHandler;
use Illuminate\Http\UploadedFile;

trait FileHandler
{
    public function file(UploadedFile $file = null): FileHandlerContract
    {
        return new class($file) implements FileHandlerContract
        {
            use CoreFileHandler;

            private ?UploadedFile $file;
            private string $directory;

            public function __construct(UploadedFile $file = null)
            {
                $this->file = $file;
            }

            public function withOriginalName(): FileHandlerContract
            {
                $this->isWithOriginalName(true);
                return $this;
            }

            public function setDir(string $dir = 'avatar'): FileHandlerContract
            {
                $this->directory = $dir;
                return $this;
            }

            public function setDirectory(string $directory = 'avatar'): FileHandlerContract
            {
                $this->directory = $directory;
                return $this;
            }

            public function getDir(): string
            {
                return $this->directory;
            }

            public function getDirectory(): string
            {
                return $this->directory;
            }

            public function save(): string
            {
                return $this->storeFile(
                    $this->file,
                    $this->getDir()
                );
            }

            public function delete($paths): bool
            {
                $paths = is_array($paths) ? $paths : [$paths];
                return $this->deleteMultipleFile($paths);
            }
        };

    }
}