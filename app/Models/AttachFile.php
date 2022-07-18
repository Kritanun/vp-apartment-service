<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

use JWTAuth;

class AttachFile extends Model
{
    protected $table = 'attach_file';
    protected $primaryKey = 'file_id';

    protected $fillable = [];
    protected $hidden = [];
    protected $guarded = ['created_by'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    

}
