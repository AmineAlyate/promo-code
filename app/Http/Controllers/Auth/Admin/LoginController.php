<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $email = $request->get('email');
            $password = $request->get('password');
            if (!$token = auth('admins')->attempt(compact('email', 'password'))) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken($token);
        } catch (Throwable $e) {
            // TODO log error

            return response()->json(['error' => 'Error occurred'], 500);
        }
    }
}
