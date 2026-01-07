<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado correctamente',
        ], 201);
    }
}
