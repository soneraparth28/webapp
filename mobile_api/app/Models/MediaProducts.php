<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MediaProducts
 *
 * @property int $id
 * @property int $products_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProducts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProducts whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProducts whereProductsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProducts whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MediaProducts extends Model
{
    use HasFactory;

    protected $guarded = [];
}
