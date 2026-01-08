<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanUpProductNotifyRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-up-product-notify-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old product_notify records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $from = Carbon::now()->subDays(3);

        DB::table('product_notify')
        ->where('created_at', '<', $from)
        ->delete();

        return 0;
    }
}
