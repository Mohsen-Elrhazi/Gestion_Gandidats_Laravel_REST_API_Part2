<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompetenceRequest;
use App\Models\Competence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetenceController extends Controller
{
    /**
     * @OA\Get(
     *     path="http://127.0.0.1:8000/api/competences",
     *     summary="Get all competences",
     *     tags={"Competences"},
     *     @OA\Response(
     *         response=200,
     *         description="List of competences",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Liste des compétences"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $competences = Competence::all();

        return response()->json([
            "status" => "success",
            "data" => [
                "competences" => $competences,
            ],
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="http://127.0.0.1:8000/api/competences",
     *     summary="Create a new competence",
     *     tags={"Competences"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="PHP Development")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Competence created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Compétence créée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(CompetenceRequest $request)
    {
        // $user= Auth::User();

        $competence = Competence::create([
            "name" => $request->name
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Competence créée avec succès",
            "data" => [
                "competence" => $competence,
            ],
        ], 201);
    }


    /**
     * @OA\Get(
     *     path="http://127.0.0.1:8000/api/competences/{id}",
     *     summary="Get details of a specific competence",
     *     tags={"Competences"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the competence",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Competence details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Détails de la compétence"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $competence = Competence::find($id);

        return response()->json([
            "status" => "success",
            "data" => [
                "competence" => $competence,
            ],
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="http://127.0.0.1:8000/api/competences/{id}",
     *     summary="Update an existing competence",
     *     tags={"Competences"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the competence",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Updated Competence Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Competence updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Compétence mise à jour avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(CompetenceRequest $request, string $id)
    {
        $competence = Competence::find($id);
        $competence->name = $request->name;
        $competence->save();

        return response()->json([
            "status" => "success",
            "message" => "Competence modifié avec succès",

        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="http://127.0.0.1:8000/api/competences/{id}",
     *     summary="Delete a competence",
     *     tags={"Competences"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the competence",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Competence deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Compétence supprimée avec succès")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $competence = Competence::findorFail($id);
        $competence->delete();

        return response()->json([
            "status" => "success",
            "message" => "Competence supprimée avec succès",

        ], 200);
    }
}