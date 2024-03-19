<?php


namespace App\Helpers\Traits\Contracts;


interface ShouldParseTemplate
{
    public function bypassAnchors(\Closure $to): self;

    public function removeTrackerElement(): self;

    public function restoreTrackerAnchors(): self;

    public function get(): string;
}