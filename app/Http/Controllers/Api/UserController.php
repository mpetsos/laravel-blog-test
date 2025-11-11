<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    // 7. Posts by user
    public function posts($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->posts()->with(['tags','category'])->get(),200);
    }

    // 8. Comments by user
    public function comments($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->comments()->with('post')->get(),200);
    }
}
