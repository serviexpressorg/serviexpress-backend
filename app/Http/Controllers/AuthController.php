<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\SpecialistVerification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role === 'specialist'
                    ? 'pending_specialist'
                    : 'client',
            ]);

            if ($request->role === 'especialist') {
                SpecialistVerification::create([
                    'user_id' => $user->id,
                    'criminal_record_file_url' => $request->criminal_record_file_url,
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado correctamente',
                'data' => [
                    'user' => $user,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas',
                ], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el token',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'access_token' => $token,
                'user' => $user,
            ],
        ]);
    }

    public function me()
    {
        $user = Auth::user();

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        if ($user->role === 'specialist' || $user->role === 'pending_specialist') {
            $verification = $user->specialistVerification; // relación
            $data['specialist_verification'] = $verification ? [
                'status' => $verification->status,
                'reviewed_at' => $verification->reviewed_at,
                'rejection_reason' => $verification->rejection_reason,
            ] : [
                'status' => 'pending',
                'reviewed_at' => null,
                'rejection_reason' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Perfil obtenido correctamente',
            'data' => $data,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'avatar' => $request->avatar,
                'location' => $request->location
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'data' => $user->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil',
            ], 500);
        }
    }
}
