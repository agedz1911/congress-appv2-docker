<?php

namespace App\Models\Transaction;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'participant_id',
        'user_id',
        'order_number',
        'total_amount',
        'discount_amount',
        'status',
        'payment_status',
        'payment_method',
        'payment_date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            if (filled($order->order_number)) {
                return;
            }

            do {
                $orderNumber = 'event-' . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            } while (static::query()->where('order_number', $orderNumber)->exists());

            $order->order_number = $orderNumber;
        });
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
