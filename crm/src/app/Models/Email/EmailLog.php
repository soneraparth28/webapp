<?php


namespace App\Models\Email;


use App\Models\AppModel;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use App\Models\Email\Traits\EmailLogBoot;
use App\Models\Email\Traits\EmailLogRelationship;

class EmailLog extends AppModel
{
    protected $enableLoggingModelsEvents = false;

    use EmailLogRelationship, DescriptionGeneratorTrait,
        EmailLogBoot {
            boot as public traitBoot;
        }

    protected $fillable = [
        'subscriber_id', 'campaign_id', 'email_id', 'email_date', 'email_content', 'open_count', 'click_count',
        'delivery_server', 'status_id', 'location', 'is_marked_as_spam', 'tracker_id'
    ];

    protected $dates = [
        'email_date'
    ];

    protected $casts = [
        'is_marked_as_spam' => 'boolean'
    ];

    public static function boot()
    {
        self::traitBoot();

    }
}
