<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="http://127.0.0.1:8000/api/profile",
     *     summary="Create or update a user profile",
     *     tags={"Profile"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"telephone", "adresse", "date_naissance"},
     *             @OA\Property(property="telephone", type="string", example="123456789"),
     *             @OA\Property(property="adresse", type="string", example="123 Rue Exemple"),
     *             @OA\Property(property="date_naissance", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="image", type="string", format="binary", description="Profile image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profile created or updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Profile créé/mis à jour avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function storeOrUpdate(ProfileRequest $request)
    {
        $user = Auth::User();

        if ($user->profile) {
            $profile = $user->profile;
        } else {
            $profile = new Profile();
            $profile->user_id = $user->id;
        }

        $this->authorize('update', $profile);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $profile->telephone = $request->telephone;
        $profile->adresse = $request->adresse;
        $profile->date_naissance = $request->date_naissance;

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($profile->image && Storage::exists($profile->image)) {
                Storage::delete($profile->image);
            }
            $image = $request->file('image');
            $path = $image->store('public/profiles');
            $profile->image = $path;
        }

        // $user->profile()->save($profile);
        $profile->save();

        // Attach competences si existent
        if ($request->has('competences')) {
            $user->competences()->sync($request->competences);
        }

        return response()->json([
            "status" => "succes",
            "message" => "Profile créé/mis a jour avec succès",
            "data" => [
                "user" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                ],
                "profile" => $profile,
            ],
        ], 201);

    }

    /**
     * @OA\Get(
     *     path="http://127.0.0.1:8000/api/profile",
     *     summary="Get the authenticated user's profile",
     *     tags={"Profile"},
     *     @OA\Response(
     *         response=200,
     *         description="Profile details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show()
    {
        $user = Auth::User();

        $profile = $user->profile;

        $this->authorize('view', $profile);

        return response()->json([
            "status" => "succes",
            "data" => [
                "user" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                ],
                "profile" => $profile,
            ],
        ], 200);
    }



    /**
     * @OA\Delete(
     *     path="http://127.0.0.1:8000/api/profile",
     *     summary="Delete the authenticated user's profile",
     *     tags={"Profile"},
     *     @OA\Response(
     *         response=200,
     *         description="Profile deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Profile supprimé avec succès")
     *         )
     *     )
     * )
     */
    public function destroy()
    {
        $user = Auth::User();
        $profile = $user->profile;

        $this->authorize("delete", $profile);

        if ($profile->image && Storage::exists($profile->image)) {
            Storage::delete($profile->image);
        }

        $profile->delete();

        return response()->json([
            "status" => "succes",
            "message" => "Profile supprimé avec succès",
        ], 200);
    }
}