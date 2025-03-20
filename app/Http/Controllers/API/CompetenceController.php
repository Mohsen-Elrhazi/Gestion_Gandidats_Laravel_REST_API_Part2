<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompetenceRequest;
use App\Models\Competence;
use Illuminate\Http\Request;

class CompetenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $competences=Competence::all();
        
        return response()->json([
            "status" => "success",
            "data"=> [
                "competences" => $competences,
            ],
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompetenceRequest $request)
    {
        $competence=Competence::create([
           "name" =>  $request->name
        ]);
        
        return response()->json([
            "status" => "success",
            "message" => "Competence créée avec succès",
            "data"=> [
                "competence" => $competence,
            ],
        ],201);
    }
        

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $competence= Competence::find($id);

        return response()->json([
            "status" => "success",
            "data"=> [
                "competence" => $competence,
            ],
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompetenceRequest $request, string $id)
    {
        $competence= Competence::find($id);
        $competence->name = $request->name;
        $competence->save();
        
        return response()->json([
            "status" => "success",
            "message" => "Competence modifié avec succès",

        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $competence= Competence::find($id);
        $competence->delete();

        return response()->json([
            "status" => "success",
            "message" => "Competence supprimée avec succès",

        ],200);
    }
}