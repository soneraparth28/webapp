<?php


namespace App\Models\Campaign;


use App\Filters\FilterBuilder;
use App\Models\AppModel;
use App\Models\Campaign\Traits\CampaignAttribute;
use App\Models\Campaign\Traits\CampaignBoot;
use App\Models\Campaign\Traits\CampaignMethod;
use App\Models\Campaign\Traits\CampaignRelationship;
use App\Models\Campaign\Traits\CampaignRules;
use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\User;
use App\Models\Core\BaseModel;
use App\Models\Core\Status;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use App\Scopes\BrandScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Mews\Purifier\Casts\CleanHtmlInput;

/**
 * App\Models\Campaign\Campaign
 *
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $template_content
 * @property string $time_period
 * @property Carbon $start_at
 * @property Carbon $end_at
 * @property string $campaign_start_time
 * @property int $status_id
 * @property int $brand_id
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|CampaignAudience[] $audiences
 * @property-read int|null $audiences_count
 * @property-read Brand $brand
 * @property-read User|null $createdBy
 * @property-read Status $status
 * @method static Builder|BaseModel filters(FilterBuilder $filter)
 * @method static Builder|Campaign newModelQuery()
 * @method static Builder|Campaign newQuery()
 * @method static Builder|Campaign query()
 * @method static Builder|Campaign whereBrandId($value)
 * @method static Builder|Campaign whereCampaignStartTime($value)
 * @method static Builder|Campaign whereCreatedAt($value)
 * @method static Builder|Campaign whereCreatedBy($value)
 * @method static Builder|Campaign whereEndAt($value)
 * @method static Builder|Campaign whereId($value)
 * @method static Builder|Campaign whereName($value)
 * @method static Builder|Campaign whereStartAt($value)
 * @method static Builder|Campaign whereStatusId($value)
 * @method static Builder|Campaign whereSubject($value)
 * @method static Builder|Campaign whereTemplateContent($value)
 * @method static Builder|Campaign whereTimePeriod($value)
 * @method static Builder|Campaign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Campaign extends AppModel
{
    protected $fillable = [
        'name', 'subject', 'template_content', 'time_period', 'start_at', 'end_at', 'campaign_start_time',
        'brand_id', 'created_by', 'status_id', 'current_status'
    ];

//    protected $casts = [
//        'template_content' => CleanHtmlInput::class, // cleans when setting the value
//    ];

    protected static $logAttributes = [
        'name', 'subject', 'time_period', 'start_at', 'end_at', 'campaign_start_time',
        'brand.name', 'createdBy.full_name', 'status.name'
    ];

    protected $dates = [
        'start_at', 'end_at'
    ];

    use CampaignBoot {
        boot as traitBoot;
    }

    use CampaignRelationship,
        CampaignAttribute,
        CampaignRules,
        CampaignMethod,
        DescriptionGeneratorTrait;

    public static function boot()
    {
        self::traitBoot();
        static::addGlobalScope(new BrandScope());
    }

}
