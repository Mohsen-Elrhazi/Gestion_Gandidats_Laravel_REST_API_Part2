<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Competence;
use App\Models\Profile;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role;
            $user->save();
            
            $user->profile()->create([]);

            // Attach competences si existent
            if ($request->has('competences')) {
                $user->competences()->attach($request->competences);
            }

            $competences = $user->competences->map(function ($competence) {
                return [
                    'id' => $competence->id,
                    'name' => $competence->name,
                ];
            });

            $token = JWTAuth::fromUser($user);

            return response()->json([
                "status" => 'success',
                "message" => 'Utilisateur enregistré avec succès',
                "data" => [
                    "user" => [
                        "id" => $user->id,
                        "name" => $user->name,
                        "email" => $user->email,
                    ],
                    "competences" => $competences,
                    'access_token' => $token,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    "status" => 'error',
                    "message" => 'Échec de l\'authentification, vérifiez vos informations',
                    'data' => null
                ], 401);
            }

            $user = Auth::user();
            // $newToken = JWTAuth::refresh($token);
            $newToken = JWTAuth::fromUser($user);

            return response()->json([
                "status" => 'success',
                "message" => 'Authentification< réussie',
                "data" => [
                    "user" => [
                        "id" => $user->id,
                        "name" => $user->name,
                        "email" => $user->email,
                    ],
                    'access_token' => $newToken,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return response()->json([
                'status' => 'success',
                'message' => 'Token rafraîchi avec succès',
                'token' => $newToken,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Impossible de rafraîchir le token',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            // auth()->logout();

            return response()->json([
                'status' => 'success',
                'message' => 'Déconnexion réussie',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la déconnexion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}