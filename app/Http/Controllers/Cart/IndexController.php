<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
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
        // Get user cart items with product
        $items = $request->user()
                ->cartItems()
                ->with('product')
                ->latest()
                ->get();

        return Inertia::render('Cart/CartList', [
            'items' => $items
        ]);
    }
}
