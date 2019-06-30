<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends FireModel
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'houses';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $timestamps = false;
}
