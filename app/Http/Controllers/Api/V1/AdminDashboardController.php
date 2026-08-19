<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    // The base URL where your AI repository's server is running
    private string $aiBaseUrl = 'http://127.0.0.1:5000/api'; 

    public function topProducts(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => 'Top products will appear here'
        ]);
    }

    public function stats(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => 'Dashboard stats will appear here'
        ]);
    }

    /**
     * AI Integration: Predict Cart Abandonment
     */
    public function predictCartAbandonment(Request $request)
    {
        // 1. Query your database for pending cart data
        $cartData = []; // Replace with actual Eloquent query later
        
        // 2. Send the data to the AI repository for analysis
        $response = Http::timeout(10)->post("{$this->aiBaseUrl}/predict-abandonment", [
            'carts' => $cartData
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $response->json()
        ], Response::HTTP_OK);
    }

    /**
     * AI Integration: Demand Forecasting
     */
    public function forecastDemand(Request $request)
    {
        // Machine learning calculations take time, so we cache the result for 24 hours!
        $forecast = Cache::remember('ai_demand_forecast', 60 * 24, function () {
            
            // Get historical sales from Laravel to send to the AI
            $salesHistory = []; 
            
            $response = Http::timeout(30)->post("{$this->aiBaseUrl}/forecast-demand", [
                'history' => $salesHistory
            ]);
            
            return $response->json();
        });

        return response()->json([
            'status' => 'success',
            'data' => $forecast
        ], Response::HTTP_OK);
    }

    /**
     * AI Integration: Customer Segmentation
     */
    public function segmentCustomers(Request $request)
    {
        $response = Http::timeout(15)->get("{$this->aiBaseUrl}/segment-customers");

        return response()->json([
            'status' => 'success',
            'data' => $response->json()
        ], Response::HTTP_OK);
    }

    /**
     * AI Integration: Purchase Prediction
     */
    public function predictPurchases(Request $request)
    {
        $userId = $request->query('user_id');
        
        $response = Http::timeout(10)->get("{$this->aiBaseUrl}/predict-purchases/{$userId}");

        return response()->json([
            'status' => 'success',
            'data' => $response->json()
        ], Response::HTTP_OK);
    }
}