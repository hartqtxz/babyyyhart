<?php

namespace App\Http\Controllers\Mobile;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getName(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'name' => $user->name,
        ]);
    }
}
