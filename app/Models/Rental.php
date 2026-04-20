<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'iphone_id',
        'start_date',
        'end_date',
        'duration_days',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'total_price'   => 'decimal:2',
        'duration_days' => 'integer',
    ];

    // Relasi ke customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke iphone
    public function iphone()
    {
        return $this->belongsTo(Iphone::class);
    }

    // Relasi ke payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
