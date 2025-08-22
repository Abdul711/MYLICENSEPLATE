<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use CrudTrait;
     public $guarded = [];
       public function cities()
    {
        return $this->hasMany(City::class);
    }
}
