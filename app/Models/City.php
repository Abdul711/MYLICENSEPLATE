<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use CrudTrait;
        // Relationship: City belongs to a province
 
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
