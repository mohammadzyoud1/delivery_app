<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'product_option_id',
        'product_option_value_id',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function option()
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function optionValue()
    {
        return $this->belongsTo(ProductOptionValue::class, 'product_option_value_id');
    }
}
