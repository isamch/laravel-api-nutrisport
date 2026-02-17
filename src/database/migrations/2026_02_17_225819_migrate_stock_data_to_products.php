<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')->get()->each(function ($product) {
            $totalStock = DB::table('product_stock')
                ->where('product_id', $product->id)
                ->sum('quantity');
            
            DB::table('products')
                ->where('id', $product->id)
                ->update(['stock' => $totalStock]);
        });
    }

    public function down(): void
    {
        //
    }
};
