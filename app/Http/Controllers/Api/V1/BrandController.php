<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Display a listing of all brands.
     */
    public function index()
    {
        $brands = Brand::orderBy('id')->get();

        return response()->json([
            'data' => $brands
        ]);
    }

    /**
     * Display a single brand.
     */
    public function show(Brand $brand)
    {
        return response()->json([
            'data' => $brand
        ]);
    }
}