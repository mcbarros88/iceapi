<?php

namespace App\Models;

use App\Models\Ecommerce\Product;
use App\Models\Ecommerce\Purchase;
use App\Models\Label\PurchaseState;
use Illuminate\Database\Eloquent\Model;

class Mountpoint extends Model
{
    protected $fillable = [
        'mount_name',
        'icecast_id',
        'password',
        'max_listeners',
        'bitrate',
    ];
    
    public function icecast() {
        $this->belongsTo('App\Models\Icecast');
    }
}
