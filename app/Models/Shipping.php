<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Shipping extends Model
{
    protected $fillable=['type','price','status'];
    public function boutique()
    {
        return $this->belongsToMany(Boutique::class,'boutique_shippings','shipping_id', 'boutique_id');
    }
}
