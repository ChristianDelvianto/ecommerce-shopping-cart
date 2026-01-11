<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = $request->user()->orders()
                ->with('items.product')
                ->latest()
                ->paginate(20);

        return Inertia::render('Orders/OrderList', [
            'orders' => $orders
        ]);
    }
}
