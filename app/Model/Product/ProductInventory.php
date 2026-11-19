<?php

namespace App\Model\Product;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductInventory extends Model
{
    protected $table = 'product_inventories';

    protected $fillable = [
        'club_id',
        'product_id',
        'selling_price',
        'qty',
        'latest_update_date'
    ];

    public function productinv_productdiscount() {
        return $this->hasOne('App\Model\Product\ProductDiscount', 'product_inventory_id');
    }

    public function productinv_logproductinvprice() {
        return $this->hasMany('App\Model\Product\LogProductInventoryPrice', 'product_inventory_id');
    }

    public function productinv_logproductinvtransfer() {
        return $this->hasMany('App\Model\Log\LogProductInventoryTransfer', 'product_inventory_id');
    }

    public function productinventory_logproductinventorycorrection(){
        return $this->hasMany('App\Model\Log\LogProductInventoryCorrection','product_inventory_id');
    }

    public function productinventory_brokenproduct(){
        return $this->hasMany('App\Model\Product\BrokenProduct','product_inventory_id');
    }

    public function productinv_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function productinv_product() {
        return $this->belongsTo('App\Model\Product\Product', 'product_id');
    }

    public static function boot(){
        parent::boot();

        static::saving(function(ProductInventory $item){
            $prev_qty = empty($item->getOriginal('qty')) ? 0 : $item->getOriginal('qty');
            if($item->qty != $prev_qty){
                $log = new LogProductInventory;
                $log->club_id = $item->club_id;
                $log->product_id = $item->product_id;
                $log->prev_qty = $prev_qty;
                $log->next_qty = $item->qty;
                $log->transaction_date = Carbon::today();
                $log->save();
            }
        });
    }
}
