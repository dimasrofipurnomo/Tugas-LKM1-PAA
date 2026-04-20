<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Iphone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'model',
        'storage',
        'color',
        'kondisi',
        'price',
        'status',
    ];

    protected $hidden = ['deleted_at'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
