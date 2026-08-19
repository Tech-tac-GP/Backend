<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Get the current user's cart.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $items = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        return response()->json($items, Response::HTTP_OK);
    }

    /**
     * Add a product to the cart.
     *
     * If the product already exists in the cart,
     * increase its quantity by 1.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $user = $request->user();

        $product = Product::findOrFail($data['product_id']);

        // Product is out of stock
        if ($product->stock_quantity <= 0 || $product->status === 'out_of_stock') {
            return response()->json([
                'status' => 'error',
                'message' => 'Product is out of stock.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $item = CartItem::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            // Prevent quantity from exceeding available stock
            if ($item->quantity + 1 > $product->stock_quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not enough stock available.',
                    'available_stock' => $product->stock_quantity,
                    'cart_quantity' => $item->quantity,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $item->quantity += 1;
            $item->save();
        } else {
            $item = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price_at_add' => $product->price,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart.',
            'data' => $item->load('product'),
        ], Response::HTTP_CREATED);
    }

    /**
     * Show one cart item belonging to the current user.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $item = CartItem::with('product')
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($item, Response::HTTP_OK);
    }

    /**
     * Update the quantity of a cart item.
     *
     * quantity = 0 -> remove the item from the cart.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $user = $request->user();

        $item = CartItem::with('product')
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $newQuantity = $data['quantity'];

        // Quantity 0 means remove from cart
        if ($newQuantity === 0) {
            $item->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item removed from cart.',
            ], Response::HTTP_OK);
        }

        // Prevent quantity from exceeding stock
        if ($newQuantity > $item->product->stock_quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not enough stock available.',
                'available_stock' => $item->product->stock_quantity,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $item->quantity = $newQuantity;
        $item->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart updated successfully.',
            'data' => $item->load('product'),
        ], Response::HTTP_OK);
    }

    /**
     * Remove a cart item completely.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $item = CartItem::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart.',
        ], Response::HTTP_OK);
    }
}
