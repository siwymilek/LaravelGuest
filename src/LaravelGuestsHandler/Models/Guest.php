<?php

namespace Siwymilek\LaravelGuestsHandler\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'guests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_agent', 'remote_address', 'token', 'token_expiration_time'];
}
