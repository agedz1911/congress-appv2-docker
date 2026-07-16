<?php

namespace App\Models\Product;

use App\Models\Product\CategoryProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'type_product',
        'type_specialization',
        'price',
        'stock',
        'date_start',
        'date_end',
        'category_product_id',
    ];

    public function categoryProduct()
    {
        return $this->belongsTo(CategoryProduct::class, 'category_product_id');
    }
}
