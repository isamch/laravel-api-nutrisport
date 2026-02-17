<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'site_id',
        'address_id',
        'status',
        'payment_method',
        'payment_status',
        'total',
        'remaining_amount',
        'tracking_number',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
