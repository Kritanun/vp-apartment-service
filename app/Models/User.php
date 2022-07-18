<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = [];
    protected $hidden = [];
    protected $guarded = ['created_by', 'updated_by'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
