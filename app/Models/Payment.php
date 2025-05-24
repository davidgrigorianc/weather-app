<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tariff_id',
        'user_id',
        'amount',
        'status',
        'payment_system',
        'transaction_id',
        'initial_requests',
        'remaining_requests'
    ];

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
