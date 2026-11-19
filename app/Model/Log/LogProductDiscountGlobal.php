<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogProductDiscountGlobal extends Model
{
    protected $table = 'log_product_discount_globals';

    protected $fillable = [
        'product_id',
        'discount_percent',
        'discount_value',
        'discount_category',
        'from_date',
        'thru_date',
    ];
}
