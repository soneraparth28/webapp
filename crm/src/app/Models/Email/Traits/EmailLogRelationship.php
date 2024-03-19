<?php


namespace App\Models\Email\Traits;


use App\Models\Campaign\Campaign;
use App\Models\Core\Builder\Template\Traits\TemplateRelationShip;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Subscriber\Subscriber;

trait EmailLogRelationship
{
    use StatusRelationship, TemplateRelationShip;

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
