<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OffreRequest;
use App\Models\Offre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OffreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user= Auth::user();
        $offres= $user->offres;

        return response()->json([
            "status" => "success",
            "message" => "liste des vos offres ",
            "data" => $offres,
         ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OffreRequest $request)
    {
        $user= Auth::user();
        //  $user= auth()->user();
        $offre= new Offre();
        
        $offre->title=$request->title;
        $offre->description=$request->description;
        $offre->location=$request->location;
        $offre->contract_type=$request->contract_type;
        // $offre->user_id= $user->id;
        
        $user->offres()->save($offre);
         
         return response()->json([
            "status" => "success",
            "message" => "offre a ete enregistre",
            "data" => $offre,
         ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $offre= Offre::find($id);
        
        $this->authorize('view', $offre);
        
        return response()->json([
            'status'=> 'success',
            'data'=> $offre
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user= Auth::user();
        $offre= Offre::find($id);
        
        $this->authorize('update', $offre);
        
        $offre->title = $request->title;
        $offre->description = $request->description;
        $offre->location = $request->location;
        $offre->contract_type = $request->contract_type;
        
        $offre->save();

        return response()->json([
            "status" => "success",
            "message" => "Offre mise à jour avec succès",
            "data" => $offre,
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user= Auth::user();
        $offre= Offre::find($id);
        
        $this->authorize("delete", $offre);

        $offre->delete();
        
        return response()->json([
            "status" => "success",
            "message" => "Offre supprimée avec succès",
            ],200);
    }

    public function activerOrDesactiver(string $id){
        $offre = Offre::find($id);
        
        if($offre->status === 'active'){
            $offre->status= 'inactive';
            $offre->save();

            return response()->json([
                'status'=> 'success',
                'message'=> 'Offre a ete desactiver',
                "offre_status" => $offre
            ],status: 200);
            
        }elseif( $offre->status === 'inactive'){
            $offre->status= 'active';
            $offre->save();

            return response()->json([
                'status'=> 'success',
                'message'=> 'Offre a ete activer',
                "offre_status" => $offre
            ],200);
        }        
    }}