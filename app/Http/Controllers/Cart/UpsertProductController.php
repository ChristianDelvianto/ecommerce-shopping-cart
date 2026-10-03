<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\UpsertProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UpsertProductController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpsertProductRequest $request, Product $product): RedirectResponse
    {
        $request->user()
        ->cart
        ->items()
        ->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $request->validated('count')]
        );

        Inertia::flash('success', 'Item added to cart');

        return back();
    }
}
