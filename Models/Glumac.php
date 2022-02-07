<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Glumac extends Model
{
    use HasFactory;
    public $guarded=[];

    public function filmovi(){
        return $this->belongsToMany(Film::class);
    }
}


