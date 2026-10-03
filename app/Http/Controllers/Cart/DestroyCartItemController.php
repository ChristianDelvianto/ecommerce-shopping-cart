<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\DestroyCartItemRequest;
use App\Models\CartItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DestroyCartItemController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DestroyCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        $cartItem->delete();

        Inertia::flash('success', 'Item deleted from cart');

        return back();
    }
}
