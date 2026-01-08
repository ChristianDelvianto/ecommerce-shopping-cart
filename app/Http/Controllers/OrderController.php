<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
                ->with('items.product')
                ->latest()
                ->paginate(20);

        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }
}
