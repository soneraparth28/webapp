<?php

namespace App\Models\Subscriber;

use App\Models\Core\Auth\User;
use App\Models\Core\Traits\BrandRelationship;
use App\Models\Core\Traits\UpdatedByRelationship;
use Illuminate\Database\Eloquent\Model;

class API extends Model
{
    protected $table = 'api_keys';

    protected $fillable = ['brand_id', 'user_id', 'updated_by', 'key'];

    use BrandRelationship, UpdatedByRelationship;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
