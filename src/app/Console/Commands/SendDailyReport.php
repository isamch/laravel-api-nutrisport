<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyReport;

class SendDailyReport extends Command
{
    protected $signature = 'report:daily';
    protected $description = 'Send daily sales report to admin';

    public function handle()
    {
        $yesterday = now()->subDay()->toDateString();
        
        // Most/Least sold product
        $productStats = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.created_at', $yesterday)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'desc')
            ->get();

        $mostSold = $productStats->first();
        $leastSold = $productStats->last();

        // Max/Min revenue product
        $revenueStats = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.created_at', $yesterday)
            ->select('products.name', DB::raw('SUM(order_items.price * order_items.quantity) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('revenue', 'desc')
            ->get();

        $maxRevenue = $revenueStats->first();
        $minRevenue = $revenueStats->last();

        // Revenue by site
        $siteRevenue = DB::table('orders')
            ->join('sites', 'orders.site_id', '=', 'sites.id')
            ->whereDate('orders.created_at', $yesterday)
            ->select('sites.name', DB::raw('SUM(orders.total) as revenue'))
            ->groupBy('sites.id', 'sites.name')
            ->get();

        $data = [
            'date' => $yesterday,
            'most_sold' => $mostSold,
            'least_sold' => $leastSold,
            'max_revenue' => $maxRevenue,
            'min_revenue' => $minRevenue,
            'site_revenue' => $siteRevenue,
        ];

        $admin = DB::table('users')->where('id', 1)->first();
        
        Mail::to($admin->email)->send(new DailyReport($data));

        $this->info('Daily report sent successfully');
    }
}
