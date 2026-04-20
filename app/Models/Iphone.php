<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Iphone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'model',
        'storage',
        'color',
        'condition',
        'daily_price',
        'status',
    ];

    protected $hidden = ['deleted_at'];

    protected $casts = [
        'daily_price' => 'decimal:2',
    ];

    // Relasi: satu iphone bisa punya banyak rental
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
