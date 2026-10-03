<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ShowController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Product $product): InertiaResponse
    {
        if ($request->user()) {
            $product->load([
                'cartItems' => function ($query) use ($request) {
                    $query->whereHas('cart', function ($query) use ($request) {
                        $query->where('user_id', $request->user()->id);
                    });
                }
            ]);
        }

        $recommended = Product::query()
                        ->where('id', '!=', $product->id)
                        ->where('stock_quantity', '>', 0)
                        ->inRandomOrder()
                        ->limit(20)
                        ->get();

        return Inertia::render('Products/ProductShow', [
            'product' => $product,
            'recommended' => $recommended
        ]);
    }
}
