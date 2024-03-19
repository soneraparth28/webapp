<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Products
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $type
 * @property string $price
 * @property int $delivery_time
 * @property string $tags
 * @property string $description
 * @property string $file
 * @property string|null $mime
 * @property string|null $extension
 * @property string|null $size
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MediaProducts[] $previews
 * @property-read int|null $previews_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Purchases[] $purchases
 * @property-read int|null $purchases_count
 * @property-read \App\Models\User|null $seller
 * @method static \Illuminate\Database\Eloquent\Builder|Products newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Products newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Products query()
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereUserId($value)
 * @mixin \Eloquent
 */
class Products extends Model
{  
    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class)->first();
    }

    /**
     * Get the seller
     */
    public function seller()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get Images Previews
     */
    public function previews()
    {
        return $this->hasMany(MediaProducts::class);
    }

    /**
     * Get Purchases
     */
    public function purchases()
    {
        return $this->hasMany(Purchases::class);
    }
}
