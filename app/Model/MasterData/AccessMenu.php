<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class AccessMenu extends Model
{
    protected $table = 'mst_access_menus';

    protected $fillable = [
        'key',
        'menu_description',
        'default_role',
    ];

    public function accessmenu_role() {
        return $this->belongsTo('App\Model\Role\Role', 'default_role');
    }

    public function accessmenu_user() {
        return $this->belongsToMany('App\User', 'user_access', 'menu_id', 'user_id')->withTimestamps();
    }
}
