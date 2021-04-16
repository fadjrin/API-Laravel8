<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
	protected $fillable = ['user_id','store_id','product_id','variant_id','qty'];

    protected $table = 'user_cart';
}
