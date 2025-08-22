<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
class City extends Model
{
    // Relationship: City belongs to a province
 use CrudTrait;
 public $guarded = [];
  
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function licensePlates()
    {
        return $this->hasMany(licenseplate::class, 'city', 'city_name');
    }

    public function getplatescountAttribute(): int
    {
        return $this->licensePlates()->count();
    }
}
