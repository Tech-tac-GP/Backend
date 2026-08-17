<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCartItemStockRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $items = $user->cartItems()->with('product')->get();

        $formattedItems = $items->map(function (CartItem $item) {
            return [
                'cart_item_id'   => $item->id,
                'product_id'     => $item->product_id,
                'name'           => $item->product->name,
                'quantity'       => $item->quantity,
                'price_per_unit' => $item->price_at_add,
                'total_price'    => $item->quantity * $item->price_at_add,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'cart_id'     => $user->id,
                'total_items' => $items->sum('quantity'),
                'subtotal'    => $items->sum(
                    fn ($item) => $item->quantity * $item->price_at_add
                ),
                'items'       => $formattedItems,
            ],
        ]);
    }

    public function destroy(Request $request, CartItem $item)
    {
        if ($item->user_id !== $request->user()->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $item->delete();

        $newSubtotal = $request->user()->cartItems()
            ->get()
            ->sum(fn ($i) => $i->quantity * $i->price_at_add);

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart',
            'data' => [
                'cart_subtotal' => $newSubtotal,
            ],
        ]);
    }

    public function updateStock(
        UpdateCartItemStockRequest $request,
        CartItem $item
    ) {
        if ($item->user_id !== $request->user()->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $newQuantity = $request->validated()['quantity'];

        if ($newQuantity <= 0) {
            $item->delete();

            $message = 'Item quantity reached zero, item removed from cart';
        } else {
            $item->update([
                'quantity' => $newQuantity,
            ]);

            $message = 'Cart updated';
        }

        $newSubtotal = $request->user()->cartItems()
            ->get()
            ->sum(fn ($i) => $i->quantity * $i->price_at_add);

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'cart_subtotal' => $newSubtotal,
            ],
        ]);
    }
}