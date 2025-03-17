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
        
    
        
        return response()->json([
            "status" => 'success',
            "message" => 'Utilisateur enregistré avec succès',
            'data' => [
                'token'=> $user->createToken('myApp')->plainTextToken,
                'name' => $user->name,
                'email'=> $user->email,
            ],
        ],201);
    }catch(\Exception $e){
        return response()->json([
            'status'=> 'error',
            'message'=> $e->getMessage(),
        ],401);
    }
  }

    public function login(LoginRequest $request){
        // if(Auth::attempt(['email'=> $request->email, 'password'=> $request->password])){
        // ou
        if(Auth::attempt($request->only('email','password'))){
            $user= Auth::user();
            
            return response()->json([
                "status" => 'success',
                "message" => 'Authentification réussi',
                 'data' => $user,
                 'token' => $user->createToken('myApp')->plainTextToken,
            ],200);
        }
        
        return response()->json([
            "status" => 'error',
            "message" => 'Échec de l"authentification, vérifiez vos informations',
            'data' => NULL
        ],401);
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