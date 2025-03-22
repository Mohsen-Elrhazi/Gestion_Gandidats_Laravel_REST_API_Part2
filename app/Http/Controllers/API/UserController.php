<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
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
            'data' => $users,
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = USer::find($id);
        return response()->json([
           'status' => 'success',
            'message' => 'Details de l\'utilisateur',
            'data'=> $user,
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user= User::find($id);
        $user->delete();
        
        return response()->json([
            'status' =>'success',
            'message' =>'user a ete supprime',
        ],200);        
    }

    public function activerOrDesactiver(string $id){
        $user = User::find($id);
        
        if($user->status === 'active'){
            $user->status= 'inactive';
            $user->save();

            return response()->json([
                'status'=> 'success',
                'message'=> 'user a ete desactiver',
                "user_status" => $user
            ],200);
            
        }elseif( $user->status === 'inactive'){
            $user->status= 'active';
            $user->save();

            return response()->json([
                'status'=> 'success',
                'message'=> 'user a ete activer',
                "user_status" => $user
            ],200);
        }        
    }
}