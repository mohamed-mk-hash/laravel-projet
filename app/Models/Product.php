<?php

namespace App\Models;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    public function product_images(){
        return $this->hasMany(ProductImage::class);
    }

    // public function relatedProducts()
    // {
    //     return $this->belongsToMany(Product::class, 'related_product_table', 'product_id', 'related_product_id');
    // }
}
