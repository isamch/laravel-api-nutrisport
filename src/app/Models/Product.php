<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function stock()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
