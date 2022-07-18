<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class FeedBack extends Model
{

    protected $table = 'feed_back';
    protected $primaryKey = 'feed_back_id';

    protected $fillable = [];
    protected $hidden = [];
    protected $guarded = ['created_by','updated_by'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
