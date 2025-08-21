<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class plate_challan extends Model
{
      public $guarded = [];
      protected static function booted()
      {
            static::creating(function ($challan) {
                  // If no due_date is already set
                  if (empty($challan->due_date)) {
                        if ($challan->licensePlate && $challan->licensePlate->created_at) {
                              // base due date on related LicensePlate's created_at
                              $challan->due_date = $challan->licensePlate->created_at->copy()->addMonths(2);
                        } else {
                              // fallback to challan's created_at or now
                              $createdAt = $challan->created_at ?? now();
                              $challan->due_date = $createdAt->copy()->addMonths(2);
                        }
                  }
            });
      }

      public function licensePlate()
      {
            return $this->belongsTo(LicensePlate::class);
      }
}
