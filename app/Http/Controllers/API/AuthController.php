<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        try{
       $user = new User();
       
        $user->name= $request->name;
        $user->email= $request->email;
        $user->password= Hash::make($request->password);
        $user->role_id= $request->role;
        $user->save();

        if ($request->has('competences')) {
            $user->competences()->attach($request->competences);
        }
    
        $token = JWTAuth::fromUser($user);

        return response()->json([
            "status" => 'success',
            "message" => 'Utilisateur enregistré avec succès',
            "competences" => [
                $request->competences
            ],
            'token' => $token,
            // 'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ],201);

    }catch(\Exception $e){
        return response()->json([
            'status'=> 'error',
            'message'=> $e->getMessage(),
        ],401);
    }
  }

    public function login(LoginRequest $request){
        try{
        if(!$token= JWTAuth::attempt($request->only('email','password'))){
            return response()->json([
                "status" => 'error',
                "message" => 'Échec de l"authentification, vérifiez vos informations',
                'data' => NULL
            ],401);
        }
        }catch(\Exception $e){
        return response()->json([
            'status'=> 'error',
            'message'=> $e->getMessage(),
            'data'=> NULL
        ]);
    }
        
        $user= Auth::user();
        
        return response()->json([
            "status" => 'success',
            "message" => 'Authentification réussi',
             'data' => $user,
             'token' => $token,
        ],200);
    }

    //methode pour update profile user (request_methode: put)
    public function updateProfile(UpdateProfileRequest $request){
        $user= Auth::User();

        if(Hash::check($request->old_password,$user->password)){
            $user->name= $request->name;
            $user->email= $request->email;
            $user->password= Hash::make($request->new_password);

            $user->update();
        
            return response()->json([
                "status" => 'success',
                "message" => 'le profile a été modifié avec success',
                'data' => $user
            ],200);
            
         }else{
            return response()->json([
                "status" => 'error',
                "message" => "L'ancien mot de passe est incorrect",
                'data' => $user
            ],400);
         }    
    }

    
}