<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class licenseplate extends Model
{
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
        return ucfirst($query);
    }
  

}
