<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'medias';
    protected $fillable = ['category', 'mimetype', 'data', 'size', 'filename', 'created_by'];
}
