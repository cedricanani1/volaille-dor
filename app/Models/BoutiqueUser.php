<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoutiqueUser extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
}
