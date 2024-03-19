<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Purchases
 *
 * @property int $id
 * @property int $transactions_id
 * @property int $user_id
 * @property int $products_id
 * @property string $delivery_status
 * @property string|null $description_custom_content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases whereDescriptionCustomContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases whereProductsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases whereTransactionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchases whereUserId($value)
 * @mixin \Eloquent
 */
class Purchases extends Model
{
    use HasFactory;

    public function user()
    {
      return $this->belongsTo(User::class)->first();
    }

    public function products()
    {
      return $this->belongsTo(Products::class)->first();
    }

    public function transactions()
    {
      return $this->belongsTo(Transactions::class)->first();
    }
}
