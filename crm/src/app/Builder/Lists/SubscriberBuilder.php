<?php


namespace App\Builder\Lists;


use App\Builder\Builder;
use App\Models\Segment\Segment;
use Illuminate\Database\Eloquent\Collection;

class SubscriberBuilder extends Builder
{
    protected $segments = [];

    public function __construct(Collection $segments)
    {
        $this->segments = $segments;

        parent::__construct();
    }

    public function build()
    {
        $this->segments->each(function (Segment $segment) {
            $this->query->union($this->segmentBuilder($segment));
        });

        return $this->query;
    }
}
