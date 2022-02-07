<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;


class Film extends Model
{

    use Sortable;
    use HasFactory;

    public $guarded = [];

    // public $sortable = ['id', 'naziv'];

    public function glumci()
    {

        return $this->belongsToMany(Glumac::class);

    }

    public function genres()
    {

        return $this->belongsToMany(Genre::class);

    }




    public function users()
    {

        return $this->belongsToMany(User::class)->withPivot(['score']);

    }

    public function comments(){
        return $this->hasMany(Comment::class)->whereNull('responseComment_id');
    }


}
