<?php

/*
 * Laravel Blog Test
 * by Thomas
 * API Category Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    // List categories
    public function index()
    {
        return response()->json(Category::all(), 200);
    }
}
