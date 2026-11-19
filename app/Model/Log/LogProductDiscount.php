<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogProductDiscount extends Model
{
    protected $table = 'log_product_discounts';

    protected $fillable = [
        'product_inventory_id',
        'discount_percent',
        'discount_value',
        'discount_category',
        'discount_type',
        'from_date',
        'thru_date',
    ];
}
