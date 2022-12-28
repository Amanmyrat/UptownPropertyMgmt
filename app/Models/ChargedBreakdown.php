<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargedBreakdown extends Model
{
    use HasFactory;

    protected $fillable = ["Total","EntitiesEntityID","CustomersCustomerDisplayID","SubEntitiesName"];

    // protected $guarded = [];
    
}
