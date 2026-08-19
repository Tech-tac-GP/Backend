<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        $categories = Category::orderBy('id')->get();

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Display a single category.
     */
    public function show(Category $category)
    {
        return response()->json([
            'data' => $category
        ]);
    }
}