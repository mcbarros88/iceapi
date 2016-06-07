<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icecast extends Model
{
    protected $fillable = [
        'admin_user',
        'admin_password',
        'admin_mail',
        'port',
    ];
    
    public function mountpoint() {
        return $this->hasMany('App\Models\Mountpoint');
    }
}
