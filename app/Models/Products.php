<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
