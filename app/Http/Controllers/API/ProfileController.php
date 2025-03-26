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
     * Store a newly created resource in storage.
     */
    public function storeOrUpdate(ProfileRequest $request)
    {
        $user= Auth::User();
        
        if($user->profile){
           $profile= $user->profile;
        }else{
            $profile= new Profile();
            $profile->user_id= $user->id;
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
        
        $profile->telephone= $request->telephone;
        $profile->adresse= $request->adresse;
        $profile->date_naissance= $request->date_naissance;

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($profile->image && Storage::exists($profile->image)) {
                Storage::delete($profile->image);
            }
        $image=$request->file('image');
        $path = $image->store('public/profiles');
        $profile->image = $path;
        }

        // $user->profile()->save($profile);
        $profile->save();

          // Attach competences si existent
            if ($request->has('competences')) {
                $user->competences()->attach($request->competences);
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
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $user=Auth::User();
        $profile= $user->profile;
        
        $this->authorize("delete", $profile);

     if ($profile->image && Storage::exists($profile->image)) {
        Storage::delete($profile->image);
     }
        
        $profile->delete();
        
        return response()->json([
            "status" => "succes",
            "message" => "Profile supprimé avec succès",
        ],200);
    }
}