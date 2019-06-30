<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends FireModel
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'auctions';

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
        'title', 'end_date', 'lots', 'img', 'url', 'house_id'
    ];

    public static function IsExistHouseId($house_id)
    {
        return static::where('house_id', $house_id)->exists();
    }

    public static function GetLastEndDate($house_id)
    {
        return static::where('house_id', $house_id)
            ->latest('id')->first();
    }

    public static function IsExistAuctionAndHouseId($auction_title, $house_id)
    {
        $result = static::where('house_id', $house_id)
            ->where('title', $auction_title)->get();
        if (empty($result->toArray()))  return false;
        return $result->first()->toArray();
    }
}
