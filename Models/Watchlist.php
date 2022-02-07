<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    use HasFactory;
    public $guarded=[];


    public function movies(){

        return $this->hasMany(Film::class);

    }

    public function users(){

        return $this->hasMany(User::class);
    }
}
