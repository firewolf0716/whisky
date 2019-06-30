<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FireModel extends Model
{
    /**
     * The unique key field name used by the table.
     *
     * @var string
     */
    protected static $uniqueKey = 'title';


    public static function getById($id)
    {
    	return static::where(static::$primaryKey, $id)->find(1);
    }

    public static function IsExist($title)
    {
        return static::where(static::$uniqueKey, $title)->exists();
    }

    public static function IsExistOrGet($title)
    {
        return static::where(static::$uniqueKey, $title)->get()->first();
    }

    public static function getSlugs()
    {
        return static::get(array(static::$uniqueKey, 'country'))->toArray();
    }
}
