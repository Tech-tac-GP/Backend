<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LuckyTimeSession;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LuckyTimeController extends Controller
{
    public function status(Request $request)
    {
        $session = $this->getCurrentSession();

        return response()->json([
            'status' => 'success',
            'data' => [
                'active' => $session->is_active,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
                'discount_percentage' => (float) $session->discount_percentage,
            ],
        ], Response::HTTP_OK);
    }

    public function participate(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $session = $this->getCurrentSession();

        if (! $session->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lucky time is not active right now.',
            ], Response::HTTP_FORBIDDEN);
        }

        $product = Product::findOrFail($data['product_id']);

        $discountPercent = (float) $session->discount_percentage;
        $discountedPrice = round($product->price * (1 - ($discountPercent / 100)), 2);

        return response()->json([
            'status' => 'success',
            'message' => 'Lucky time discount applied.',
            'data' => [
                'product_id' => $product->id,
                'original_price' => (float) $product->price,
                'discount_percentage' => $discountPercent,
                'discounted_price' => $discountedPrice,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
            ],
        ], Response::HTTP_OK);
    }

    protected function getCurrentSession(): ?LuckyTimeSession
    {
        $session = LuckyTimeSession::activeNow()->first();

        if ($session && $session->status === 'scheduled') {
            $session->update(['status' => 'active']);
        }

        if ($session) {
            $session->is_active = true;
        }

        return $session;
    }
}
