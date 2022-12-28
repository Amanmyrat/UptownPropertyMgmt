<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'UnitID';
    protected $guarded =[];

    public function unitType(){
        return $this->hasOne(UnitType::class,'UnitTypeId','UnitTypeID');
    }
}
