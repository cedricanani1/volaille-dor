<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoutiqueProduct extends Model
{
    use SoftDeletes;
    protected $fillable = ['boutique_id','product_id','quantity_init','quantity'];
}
