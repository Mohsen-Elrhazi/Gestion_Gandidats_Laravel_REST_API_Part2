<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Auth;
use Illuminate\Support\Facades\Hash;
use Storage;

class UserController extends Controller
{
    //! Method to update user profile
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user(); // Get the authenticated user

            // Use the policy to check if the user can update their profile
            $this->authorize('update', $user);

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

            // Update or create profile
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
}