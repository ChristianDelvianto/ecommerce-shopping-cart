<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): InertiaResponse
    {
        $orders = $request->user()->orders()
                ->with('items.product')
                ->latest()
                ->paginate(20);

        return Inertia::render('Orders/OrderList', [
            'orders' => OrderResource::collection($orders)
        ]);
    }
}
