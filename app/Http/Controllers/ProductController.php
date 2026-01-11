<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::where('stock_quantity', '>', 0)
                    ->latest()
                    ->paginate(20);

        return Inertia::render('Products/ProductList', [
            'products' => $products
        ]);
    }

    /**
     * Show a product and recommended products
     */
    public function show(Request $request, Product $product): Response
    {
        $product->load([
            'cartItems' => function ($query) use ($request) {
                $query->whereHas('cart', function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                });
            }
        ]);

        $recommended = Product::where('id', '!=', $product->id)
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
