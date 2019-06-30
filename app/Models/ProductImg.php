<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImg extends FireModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_imgs';

    /**
     * The primary key field name used by the table.
     *
     * @var string
     */
    protected $primaryKey = 'product_img_id';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'product_img_id',
    ];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'sub_img'
    ];

    public static function IfExistProductThumb($product_id)
    {
        return static::where('product_id', $product_id)->exists();
    }
    
}
