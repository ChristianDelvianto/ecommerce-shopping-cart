<?php

namespace App\Console\Commands;

use App\Mail\DailySalesReportNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class GenerateDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-daily-sales-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily sales report and email to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $yesterday = Carbon::now()->subDay();
        $yesterday = Carbon::now();

        $grandTotal = DB::table('orders')
                    ->where('status', 'completed')
                    ->whereDate('orders.created_at', $yesterday)
                    ->sum('subtotal_amount');

        $soldProducts = DB::table('order_items')
                        ->select(
                            'products.id',
                            'products.name',
                            DB::raw('SUM(order_items.quantity) AS total_quantity'),
                            DB::raw('SUM(order_items.quantity * order_items.unit_price) AS total_sales')
                        )
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->join('products', 'order_items.product_id', '=', 'products.id')
                        ->whereDate('order_items.created_at', $yesterday)
                        ->groupBy('products.id')
                        ->orderBy('total_sales', 'desc')
                        ->get();

        $admin = User::where('role', 'admin')->first();

        Mail::to($admin->email)
        ->send(new DailySalesReportNotification($yesterday->toDateTimeString(), $grandTotal, $soldProducts));

        return 0;
    }
}
