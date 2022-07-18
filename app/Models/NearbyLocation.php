<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

use JWTAuth;

class NearbyLocation extends Model
{
    protected $table = 'nearby_location';
    protected $primaryKey = 'location_id';

    protected $fillable = [];
    protected $hidden = [];
    protected $guarded = ['created_by', 'updated_by'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    

}
