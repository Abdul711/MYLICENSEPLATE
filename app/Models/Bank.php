<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use CrudTrait;
     public $guarded = [];
    
}
