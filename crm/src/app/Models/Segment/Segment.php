<?php


namespace App\Models\Segment;


use App\Models\AppModel;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use App\Models\Segment\Traits\SegmentMethod;
use App\Models\Segment\Traits\SegmentRelationship;
use App\Models\Segment\Traits\SegmentRules;
use App\Scopes\BrandScope;


class Segment extends AppModel
{
    protected $fillable = [
        'name', 'segment_logic', 'brand_id', 'created_by'
    ];

    protected static $logAttributes = [
        'name', 'segment_logic', 'brand.name', 'createdBy.full_name'
    ];

    use BootTrait {
        boot as public traitBoot;
    }

    use SegmentRelationship,
        SegmentRules,
        SegmentMethod,
        DescriptionGeneratorTrait;

    /**
     * @return mixed
     */
    public function getSegmentLogicAttribute()
    {
        return json_decode($this->attributes['segment_logic']);
    }

    public function setSegmentLogicAttribute($segmentLogic)
    {
        $this->attributes['segment_logic'] = json_encode($segmentLogic);
    }

    public static function boot()
    {
        self::traitBoot();
        static::addGlobalScope(new BrandScope());
        static::saved(function (Segment $segment) {
            cache()->forget('segment-subscribers-'.$segment->id);
            cache()->forget('segment-subscribers-count-'.$segment->id);
            cache()->forget('segment-subscribers-ids-'.$segment->id);
        });
        static::deleting(function (Segment $segment) {
            cache()->forget('segment-subscribers-'.$segment->id);
            cache()->forget('segment-subscribers-count-'.$segment->id);
            cache()->forget('segment-subscribers-ids-'.$segment->id);
        });
    }

}
