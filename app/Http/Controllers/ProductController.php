<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
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
    public function show(Request $request, Product $product)
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

    /**
     * Update a product (Admin only)
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->fill($request->validated());

        $product->save();

        Inertia::flash('success', 'Product updated');

        return back();
    }
}
