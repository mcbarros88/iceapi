<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icecast extends Model
{
    protected $fillable = [
        'admin-user',
        'admin-password',
        'admin-mail',
        'port',
    ];
    
    public function mountpoint() {
        $this->hasMany('App\Mountpoint');
    }
}
