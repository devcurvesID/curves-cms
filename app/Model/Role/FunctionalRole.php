<?php

namespace App\Model\Role;

use Illuminate\Database\Eloquent\Model;

class FunctionalRole extends Model
{
    protected $table = 'mst_functional_roles';

    protected $fillable = [
        'functional_role',
    ];

    public function functionalrole_staff() {
        return $this->belongsToMany('App\User', 'staff_functional_roles', 'functional_role_id', 'user_id')->withTimestamps();
    }
}
