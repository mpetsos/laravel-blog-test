<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    // 9. List categories
    public function index()
    {
        return response()->json(Category::all(),200);
    }
}
