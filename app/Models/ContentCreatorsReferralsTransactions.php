<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentCreatorsReferralsTransactions extends Model
{
  protected $fillable = [
    'transactions_id',
    'user_id',
    'content_creator_id',
    'earnings',
    'type',
    'created_at'
  ];

//     public function user()
//     {
//       return $this->belongsTo(User::class)->first();
//     }

// 		public function referredBy()
//     {
//       return $this->belongsTo(User::class, 'referred_by')->first();
//     }

}
