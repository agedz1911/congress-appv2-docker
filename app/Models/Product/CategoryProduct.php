<?php

namespace App\Models\Product;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'date',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_product_id');
    }
}
