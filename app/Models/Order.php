<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'status', 'total', 'date'];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'date' => 'date',
        ];
    }

    protected function orderNumber(): Attribute
    {
        return Attribute::get(
            fn (): string => 'ORD-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT),
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
