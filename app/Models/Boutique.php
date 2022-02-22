<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Boutique extends Model
{
    use SoftDeletes;
    protected $fillable = ['libelle','shipping_id','user_id'];

    public function zone()
    {
        return $this->belongsToMany(Shipping::class,'boutique_shippings', 'boutique_id','shipping_id');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class,'boutique_products')->withPivot('created_at','updated_at','quantity_init','quantity')->orderBy('pivot_created_at', 'desc');
    }
    public function user()
    {
        return $this->belongsToMany('App\User','boutique_users', 'boutique_id','user_id');
    }

    public function lieu()
    {
        return $this->belongsTo(Shipping::class, 'shipping_id');
    }
}
