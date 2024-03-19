<?php

namespace App\Models\SES;

use App\Models\Core\Traits\BrandRelationship;
use Illuminate\Database\Eloquent\Model;

class SnsSubscription extends Model
{
    protected $table = 'sns_subscriptions';

    protected $fillable = ['brand_id', 'confirm_url', 'is_confirmed'];

    use BrandRelationship;

}
