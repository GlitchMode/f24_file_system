<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'parent_id'
    ];

    public function parent(){
        return $this->belongsTo(Folder::class);
    }
}
