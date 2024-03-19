<?php


namespace App\Models\Campaign;


use App\Builder\Campaign\SubscriberBuilder;
use App\Models\AppModel;
use App\Models\Campaign\Traits\CampaignAudienceAttribute;
use App\Models\Campaign\Traits\CampaignAudienceRelationship;
use App\Models\Lists\Lists;
use App\Models\Subscriber\Subscriber;
use App\Repositories\App\StatusRepository;

/**
 * App\Models\Campaign\CampaignAudience
 *
 * @property int $id
 * @property int $campaign_id
 * @property string $audience_type list/subscriber
 * @property mixed $audiences Array of subscribers id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Campaign\Campaign $campaign
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience whereAudienceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience whereAudiences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign\CampaignAudience whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CampaignAudience extends AppModel
{
    protected $fillable = [
        'campaign_id', 'audience_type', 'audiences'
    ];

    use CampaignAudienceRelationship,
        CampaignAudienceAttribute;

    public function audiences()
    {
        if ($this->attributes['audience_type'] == 'list') {
            return  $this->listAudiences();
        }else if ($this->attributes['audience_type'] == 'subscriber'){
            $statuses = resolve(StatusRepository::class)
                ->subscriber('status_subscribed');

            return Subscriber::select(['id', 'email', 'first_name', 'last_name', 'status_id'])
                ->whereIn('status_id', array_keys($statuses))
                ->whereIn('id', $this->audiences)
                ->get();
        }
    }

    public function listAudiences($statuses = [])
    {
        return $this->subscriberBuilder($statuses)->get();
    }

    public function subscriberBuilder($statuses = [], $fields = [])
    {
        $statuses = is_array($statuses) ? $statuses : func_get_args();

        $builder = new SubscriberBuilder(
            $this->campaign
        );

        $builder->select($fields);

        if (count($statuses))
            $builder->whereStatus($statuses);

        return $builder->build();
    }
}
