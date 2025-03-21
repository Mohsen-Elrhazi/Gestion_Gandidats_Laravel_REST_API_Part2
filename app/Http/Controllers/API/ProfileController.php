<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     * Store a newly created resource in storage.
     */
    public function storeOrUpdate(ProfileRequest $request)
    {
        $user= Auth::User();
        
        if($user->profile){
           $profile= $user->profile;
        }else{
            $profile= new Profile();
            $profile->user_id=$user->id;
        }
        
        $this->authorize('update', $profile);
        
       if($request->has('name')){
        $user->name=$request->name;
       }
       if($request->has('email')){
           $user->email=$request->email;
       }
       if($request->has('password')){
           $user->password=Hash::make($request->password);
       }
            
        $user->save();
        
        $profile->telephone=$request->telephone;
        $profile->adresse=$request->adresse;
        $profile->date_naissance=$request->date_naissance;
        
        $path = $request->file('image')->store('public/profiles');
        $profile->image = $path;
        
        $profile->save();
        
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
        ],201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user= Auth::User();
        
        $profile= $user->profile;
        
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
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileRequest $request)
    {
        // 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $user=Auth::User();
        $profile= $user->profile;
        
        $this->authorize("delete", $profile);
        
        $profile->delete();
        
        return response()->json([
            "status" => "succes",
            "message" => "Profile supprimé avec succès",
        ],200);
    }
}