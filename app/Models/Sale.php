<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'products',
        'user_name',
        'total_price',
        'commission',
    ];

    protected $casts = [
        'products' => 'string',
        'user_name'=> 'string',
    ];

}
