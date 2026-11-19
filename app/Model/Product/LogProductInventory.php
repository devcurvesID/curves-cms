<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class LogProductInventory extends Model
{
    protected $table = 'product_inventory_logs';

    protected $fillable = [
        'club_id',
        'product_id',
        'prev_qty',
        'next_qty',
        'transaction_date',
    ];
}
