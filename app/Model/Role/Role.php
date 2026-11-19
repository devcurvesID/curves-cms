<?php

namespace App\Model\Role;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'mst_roles';

    protected $fillable = [
        'role'
    ];

    public function role_user() {
        return $this->hasMany('App\User', 'role_id');
    }

    public function role_accessmenu() {
        return $this->hasMany('App\Model\MasterData\AccessMenu', 'default_role');
    }

    public function role_transferhistory() {
        return $this->hasMany('App\Model\Staff\TransferHistory', 'role_id');
    }

    public function SetRoleAttribute($value) {
        $this->attributes['role'] = strtolower($value);
    }

    public function GetRoleAttribute($value) {
        return ucwords($value);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
