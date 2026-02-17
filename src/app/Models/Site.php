<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = ['name', 'country_code', 'currency'];

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function productStock()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
