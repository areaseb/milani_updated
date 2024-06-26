<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderProduct extends BaseModel
{
    protected $table = 'ec_order_product';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'qty',
        'weight',
        'price',
        'tax_amount',
        'options',
        'product_options',
        'restock_quantity',
        'product_type',
    ];

    protected $casts = [
        'options' => 'json',
        'product_options' => 'json',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withDefault();
    }

    public function getAmountFormatAttribute(): string
    {
        return format_price($this->price * 1.22);
    }

    public function getTotalFormatAttribute(): string
    {
        return format_price(($this->price * 1.22) * $this->qty);
    }

    public function productFiles(): HasMany
    {
        return $this->hasMany(ProductFile::class, 'product_id');
    }
}
