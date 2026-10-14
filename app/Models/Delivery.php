<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    public const STATUSES = ['assigned', 'picked_up', 'in_transit', 'delivered', 'failed'];

    protected $fillable = ['order_id', 'courier_name', 'status', 'dispatched_at', 'delivered_at'];

    protected function casts(): array
    {
        return ['dispatched_at' => 'datetime', 'delivered_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
