<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
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

            if ($request->has('competences')) {
                $user->competences()->attach($request->competences);
            }
            // recuperer les competences
            // $competences = Competence::whereIn('id', $request->competences)->pluck('name');
            $competences = $user->competences->map(function ($competences) {
                return [
                    'id' => $competences->id,
                    'name' => $competences->name,
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
                    'token' => $token,
                ]
                // 'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    "status" => 'error',
                    "message" => 'Échec de l"authentification, vérifiez vos informations',
                    'data' => NULL
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => NULL
            ]);
        }

        $user = Auth::user();

        return response()->json([
            "status" => 'success',
            "message" => 'Authentification réussi',
            "data" => [
                "user" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                ],
                'token' => $token,
            ]
        ], 200);
    }

    //! methode de refresh token
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
            ], 401);
        }
    }

    //!methode pour update profile user (request_methode: put)
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    "status" => 'error',
                    "message" => "L'ancien mot de passe est incorrect",
                ], 400);
            }

            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('new_password')) {
                $user->password = Hash::make($request->new_password);
            }

            $user->update();

            // Update or create 
            $profile = $user->profile ?: new Profile(['user_id' => $user->id]);

            if ($request->has('telephone')) {
                $profile->telephone = $request->telephone;
            }

            if ($request->has('adresse')) {
                $profile->adresse = $request->adresse;
            }

            if ($request->has('date_naissance')) {
                $profile->date_naissance = $request->date_naissance;
            }

            if ($request->hasFile('image')) {
                // Delete old image if it exists
                if ($profile->image && Storage::exists($profile->image)) {
                    Storage::delete($profile->image);
                }

                // Store new image
                $path = $request->file('image')->store('public/profiles');
                $profile->image = $path;
            }

            $user->profile()->save($profile);

            return response()->json([
                "status" => 'success',
                "message" => 'Le profil a été modifié avec succès',
                'data' => [
                    "user" => $user,
                    "profile" => $profile,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Une erreur est survenue lors de la mise à jour du profil",
                "error" => $e->getMessage(),
            ], 500);
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