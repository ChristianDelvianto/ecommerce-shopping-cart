<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
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
        $products = Product::query()
                    ->where('stock_quantity', '>', 0)
                    ->latest()
                    ->simplePaginate(20);

        return Inertia::render('Products/ProductList', [
            'products' => ProductResource::collection($products)
        ]);
    }
}
