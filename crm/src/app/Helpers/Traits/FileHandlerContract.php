<?php


namespace App\Helpers\Traits;


interface FileHandlerContract
{
    public function withOriginalName(): FileHandlerContract;

    public function setDir(string $dir = 'avatar'): FileHandlerContract;

    public function setDirectory(string $directory = 'avatar'): FileHandlerContract;

    public function getDir(): string;

    public function getDirectory(): string;

    public function save(): string;

    public function delete($paths): bool;
}