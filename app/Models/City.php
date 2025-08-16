<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
        // Relationship: City belongs to a province
    public function province()
    {
        return $this->belongsTo(Region::class);
    }
}
