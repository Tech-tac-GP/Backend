<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LuckyTimeSession;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand'])
            ->filter($request->only(['category_id', 'brand_id', 'status']))
            ->orderBy('id')
            ->get();

        $session = $this->getActiveLuckyTimeSession();

        $products->each->applyLuckyTimeDiscount($session);

        return response()->json([
            'lucky_time' => $this->formatLuckyTimeResponse($session),
            'data' => $products,
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand']);

        $session = $this->getActiveLuckyTimeSession();
        
        $product->applyLuckyTimeDiscount($session);

        return response()->json([
            'lucky_time' => $this->formatLuckyTimeResponse($session),
            'data' => $product,
        ]);
    }

    public function search(Request $request)
    {
        // search method for products
        $products = Product::query()
            ->search($request->query('q', ''))
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    /**
     * Helper to fetch the currently active flash sale session.
     */
    private function getActiveLuckyTimeSession(): ?LuckyTimeSession
    {
        return LuckyTimeSession::where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->first();
    }

    /**
     * Helper to DRY up the JSON response structure for flash sales.
     */
    private function formatLuckyTimeResponse(?LuckyTimeSession $session): array
    {
        return [
            'active' => (bool) $session,
            'discount_percentage' => $session ? (float) $session->discount_percentage : 0,
            'start_time' => $session?->start_time,
            'end_time' => $session?->end_time,
        ];
    }
}