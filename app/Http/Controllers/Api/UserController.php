<?php

/*
 * Laravel Blog Test
 * by Thomas
 * API Users Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    // Posts by user
    public function posts($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user->posts()->with(['tags', 'category'])->get(), 200);
        } catch (ModelNotFoundException $e) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'status' => 'error',
                    'message' => 'This User does not exist '
                ], 404)
            );
        }
    }

    // Comments by user
    public function comments($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user->comments()->with('post')->get(), 200);
        } catch (ModelNotFoundException $e) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'status' => 'error',
                    'message' => 'This User does not exist '
                ], 404)
            );
        }
    }
}
