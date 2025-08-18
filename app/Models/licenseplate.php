<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class licenseplate extends Model
{
    use CrudTrait;
   public $guarded = [];
   public function user()
{
    return $this->belongsTo(User::class);
}
    public function getregionAttribute($query){
        return ucfirst($query);
    }
    public function getstatusAttribute($query){
        return ucfirst($query);
    }
    public function getcityAttribute($query){
        return ucwords($query);
    }
    public function city()
{
    // belongsTo(RelatedModel, foreignKeyOnThisModel, ownerKeyOnRelatedModel)
      return $this->belongsTo(City::class, 'city', 'city_name');
}
  

}
