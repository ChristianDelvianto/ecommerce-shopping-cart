<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Requests\UpsertCartItemRequest;
use App\Jobs\NotifyLowStockQuantity;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Request $request): Response
    {
        // Get user cart items with product
        $items = $request->user()->cartItems()
                ->with('product')
                ->get();

        return Inertia::render('Cart/CartList', [
            'items' => $items
        ]);
    }

    /**
     * Checkout all cart items
     */
    public function checkoutItems(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $cartItems = $request->user()->cartItems()
                            ->with('product')
                            ->get();

                if ($cartItems->isEmpty()) {
                    throw new Exception('No items in cart');
                }

                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'status' => 'completed',
                    'subtotal_amount' => 0,
                ]);

                $subtotal = 0;

                foreach ($cartItems as $cartItem) {
                    $product = Product::where('id', $cartItem->product_id)
                                ->lockForUpdate()
                                ->first();

                    if ($cartItem->quantity > $product->stock_quantity) {
                        throw new Exception('Sorry, insufficient stock');
                    }

                    $orderItemTotal = $product->price * $cartItem->quantity;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $cartItem->quantity,
                        'unit_name' => $product->name,
                        'unit_price' => $product->price,
                        'total_price' => $orderItemTotal,
                    ]);

                    $product->decrement('stock_quantity', $cartItem->quantity);

                    $cartItem->delete();

                    if ($product->stock_quantity <= config('app.stock_threshold')) {
                        dispatch(new NotifyLowStockQuantity($product, $product->stock_quantity));
                    }

                    $subtotal += $orderItemTotal;
                }

                $order->update(['subtotal_amount' => $subtotal]);
            }, 3);

            Inertia::flash('success', 'Your order has been created');

            return back();
        } catch (Exception $e) {
            return back()->withErrors([
                'message' => 'Checkout failed, please try again.'
            ]);
        }
    }

    /**
     * Remove an item from cart
     */
    public function removeCartItem(Request $request, CartItem $cartItem): RedirectResponse
    {
        $cartItem->delete();

        Inertia::flash('success', 'Item deleted from cart');

        return back();
    }

    /**
     * Update or insert new cart item
     */
    public function upsertProductToCart(UpsertCartItemRequest $request, Product $product): RedirectResponse
    {
        $cartItem = $request->user()->cartItems()
                    ->with('product')
                    ->whereHas('product', function ($query) use ($product) {
                        $query->where('id', $product->id);
                    })
                    ->first();

        if ($cartItem) {
            $cartItem->update(['quantity' => $request->validated('count')]);

            Inertia::flash('success', 'Cart item updated');

            return back();
        }

        $request->user()->cart
            ->items()
            ->create([
                'product_id' => $product->id,
                'quantity' => $request->validated('count')
            ]);

        Inertia::flash('success', 'Product added to cart');

        return back();
    }
}
