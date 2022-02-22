<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use SoftDeletes;
    protected $fillable = ['libelle','quantite','product_id',];

    public function product()
    {
        return $this->belongsTo(product::class);
    }
}
