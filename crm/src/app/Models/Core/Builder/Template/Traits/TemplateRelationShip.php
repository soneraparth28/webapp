<?php


namespace App\Models\Core\Builder\Template\Traits;


use App\Models\Core\File\File;
use App\Models\Core\Traits\BrandRelationship;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Core\Traits\UpdatedByRelationship;

trait TemplateRelationShip
{
    use CreatedByRelationship, BrandRelationship, UpdatedByRelationship;

}
