<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends FireModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

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
        'auction_id', 'url', 'name', 'distillery_brand', 'bottler', 'age', 'bottled_for', 
        'abv', 'size', 'distilled', 'bottled', 'cask_type', 'cask_num', 'bottle_num',
        'num_bottles', 'description', 'lot_num', 'price', 'master_img', 'series',
        'country', 'region', 'type'
    ];

    /**
     * The unique key field name used by the table.
     *
     * @var string
     */
    protected static $uniqueKey = 'lot_num';

    public static function IfExistProduct($lot_num, $auction_id)
    {
        return static::where('lot_num', $lot_num)
            ->where('auction_id', $auction_id)->get()->first();
    }

    public static function CountProductOfAuction($auction_id)
    {
        return static::where('auction_id', $auction_id)->count();
    }

    public static function IfExistAuction($auction_id)
    {
        return static::where('auction_id', $auction_id)
            ->exists();
    }
}
