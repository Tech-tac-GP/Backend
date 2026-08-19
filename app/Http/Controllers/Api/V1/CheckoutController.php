<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LuckyTimeSession;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckoutController extends Controller
{
   
    public function __invoke(Request $request)
    {
        $user = $request->user();
        
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your cart is empty.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // 2. Security Check: Is the Lucky Time flash sale running at this EXACT second?
        $luckySession = LuckyTimeSession::activeNow()->first();
        $discount = $luckySession ? (float) $luckySession->discount_percentage : 0;

        $totalPrice = 0;
        $checkoutDetails = [];

        foreach ($cartItems as $item) {
            $productPrice = (float) $item->product->price;

            $finalItemPrice = $discount > 0 
                ? round($productPrice * (1 - ($discount / 100)), 2)
                : $productPrice;

            $itemTotal = $finalItemPrice * $item->quantity;
            $totalPrice += $itemTotal;

            $checkoutDetails[] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'original_price' => $productPrice,
                'final_unit_price' => $finalItemPrice,
                'subtotal' => $itemTotal,
            ];
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Checkout calculated successfully.',
            'data' => [
                'lucky_time_applied' => (bool) $luckySession,
                'discount_percentage' => $discount,
                'grand_total' => round($totalPrice, 2),
                'items' => $checkoutDetails,
            ],
        ], Response::HTTP_OK);
    }
}