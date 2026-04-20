<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    protected $hidden = ['deleted_at'];

    // Relasi: satu customer bisa punya banyak rental
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
