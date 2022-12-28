<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitAvailability extends Model
{
    use HasFactory;

    protected $fillable = ["description","unitname","marketrent","unitstatuscomment","entityid","statusId"];
}
