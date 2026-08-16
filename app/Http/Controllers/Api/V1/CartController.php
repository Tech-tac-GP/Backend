<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CartItem;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $items = CartItem::with('product')->where('user_id', $user->id)->get();
        return response()->json($items, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $user = $request->user();

        $item = CartItem::firstOrCreate(
            ['user_id' => $user->id, 'product_id' => $data['product_id']],
            ['quantity' => $data['quantity'] ?? 1, 'price_at_add' => 0]
        );

        return response()->json($item, Response::HTTP_CREATED);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $item = CartItem::with('product')->where('user_id', $user->id)->where('id', $id)->firstOrFail();
        return response()->json($item, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $item = CartItem::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        $item->quantity = $data['quantity'];
        $item->save();

        return response()->json($item, Response::HTTP_OK);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $item = CartItem::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        $item->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
