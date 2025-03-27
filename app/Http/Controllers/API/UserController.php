<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="http://127.0.0.1:8000/api/users",
     *     summary="Get all users except admins",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Liste des utilisateurs (hors administrateurs)"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', '!=', 'admin');
        })->get();

        // $users= User::where('role_id', '!=', 1)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Liste des utilisateurs (hors administrateurs)',
            'data' => new UserCollection($users)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="http://127.0.0.1:8000/api/users/{id}",
     *     summary="Get details of a specific user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Details de l'utilisateur"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $user = USer::find($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Details de l\'utilisateur',
            'data' => new UserResource($user),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="http://127.0.0.1:8000/api/users/destroy/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="user a ete supprime")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'user a ete supprime',
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="http://127.0.0.1:8000/api/users/changerStatus/{id}",
     *     summary="Activate or deactivate a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="user a ete activer/desactiver"),
     *             @OA\Property(property="user_status", type="string", example="active")
     *         )
     *     )
     * )
     */
    public function activerOrDesactiver(string $id)
    {

        $user = User::find($id);

        $this->authorize('update', $user);

        if ($user->status === 'active') {
            $user->status = 'inactive';
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'user a ete desactiver',
                "user_status" => $user->status
            ], 200);

        } elseif ($user->status === 'inactive') {
            $user->status = 'active';
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'user a ete activer',
                "user_status" => $user->status
            ], 200);
        }
    }
}