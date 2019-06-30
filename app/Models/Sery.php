<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sery extends FireModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'series';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The unique key field name used by the table.
     *
     * @var string
     */
    protected static $uniqueKey = 'name';
}
