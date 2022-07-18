<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

use JWTAuth;

class OtherService extends Model
{
    protected $table = 'other_service';
    protected $primaryKey = 'other_service_id';

    protected $fillable = [];
    protected $hidden = [];
    protected $guarded = ['created_by', 'updated_by'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    

}
