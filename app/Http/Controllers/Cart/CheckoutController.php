<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyLowStockQuantity;
use App\Models\Product;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $cartItems = $request->user()
                            ->cartItems()
                            ->with('product')
                            ->get();

                if ($cartItems->isEmpty()) {
                    throw new Exception('Your shopping cart is empty.');
                }

                $productIds = $cartItems->pluck('product_id')->toArray();
                $products = Product::whereIn('id', $productIds)
                            ->lockForUpdate()
                            ->get()
                            ->keyBy('id');

                $subtotal = 0;
                $lowStockProducts = [];

                // If you want to implement order status, you can change this value accordingly,
                // we'll just set it to 'completed' for simplicity
                $order = $request->user()
                        ->orders()
                        ->create([
                            'user_id' => $request->user()->id,
                            'status' => 'completed',
                            'subtotal_amount' => 0
                        ]);

                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);

                    // For this portfolio, we'll throw an exception if any product is out of stock
                    if ($cartItem->quantity > $product->stock_quantity) {
                        throw new Exception('Sorry, insufficient stock for product: ' . $product->name);
                    }

                    $orderItemTotal = $product->price * $cartItem->quantity;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $cartItem->quantity,
                        'unit_name' => $product->name,
                        'unit_price' => $product->price,
                        'total_price' => $orderItemTotal
                    ]);

                    $product->decrement('stock_quantity', $cartItem->quantity);

                    if ($product->stock_quantity <= config('app.stock_threshold')) {
                        $lowStockProducts[] = $product;
                    }

                    $subtotal += $orderItemTotal;
                }

                $order->update(['subtotal_amount' => $subtotal]);

                $request->user()->cartItems()->delete();

                DB::afterCommit(function () use ($lowStockProducts) {
                    foreach ($lowStockProducts as $product) {
                        NotifyLowStockQuantity::dispatch($product, $product->stock_quantity);
                    }
                });
            });

            Inertia::flash('success', 'Your order has been created');

            return back();
        } catch (Exception $e) {
            report($e);

            return back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }
}
