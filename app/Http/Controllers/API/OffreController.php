<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OffreRequest;
use App\Http\Resources\OffreCollection;
use App\Http\Resources\OffreResource;
use App\Models\Offre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OffreController extends Controller
{
    /**
     * @OA\Get(
     *     path="http://127.0.0.1:8000/api/offres",
     *     summary="Get all offers",
     *     tags={"Offres"},
     *     @OA\Response(
     *         response=200,
     *         description="List of offers",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Liste des offres"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $offres = $user->offres;

        return response()->json([
            "status" => "success",
            "message" => "liste des vos offres ",
            'data' => new OffreCollection($offres)
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/offres",
     *     summary="Create a new offer",
     *     tags={"Offres"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "location", "contract_type"},
     *             @OA\Property(property="title", type="string", example="Software Engineer"),
     *             @OA\Property(property="description", type="string", example="Develop and maintain software."),
     *             @OA\Property(property="location", type="string", example="Paris"),
     *             @OA\Property(property="contract_type", type="string", example="CDI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Offer created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Offre créée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(OffreRequest $request)
    {
        $user = Auth::user();
        //  $user= auth()->user();
        $offre = new Offre();

        $offre->title = $request->title;
        $offre->description = $request->description;
        $offre->location = $request->location;
        $offre->contract_type = $request->contract_type;
        // $offre->user_id= $user->id;

        $user->offres()->save($offre);

        return response()->json([
            "status" => "success",
            "message" => "offre a ete enregistre",
            "data" => new OffreResource($offre),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/offres/{id}",
     *     summary="Get details of a specific offer",
     *     tags={"Offres"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the offer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Offer details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Details de l'offre"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $offre = Offre::find($id);

        $this->authorize('view', $offre);

        return response()->json([
            'status' => 'success',
            "data" => new OffreResource($offre),
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/offres/{id}",
     *     summary="Update an existing offer",
     *     tags={"Offres"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the offer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "location", "contract_type"},
     *             @OA\Property(property="title", type="string", example="Updated Software Engineer"),
     *             @OA\Property(property="description", type="string", example="Updated description."),
     *             @OA\Property(property="location", type="string", example="Lyon"),
     *             @OA\Property(property="contract_type", type="string", example="CDD")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Offer updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Offre mise à jour avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $offre = Offre::find($id);

        $this->authorize('update', $offre);

        $offre->title = $request->title;
        $offre->description = $request->description;
        $offre->location = $request->location;
        $offre->contract_type = $request->contract_type;

        $offre->save();

        return response()->json([
            "status" => "success",
            "message" => "Offre mise à jour avec succès",
            "data" => new OffreResource($offre),
        ], 200);

    }

    /**
     * @OA\Delete(
     *     path="/api/offres/{id}",
     *     summary="Delete an offer",
     *     tags={"Offres"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the offer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Offer deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Offre supprimée avec succès")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $offre = Offre::find($id);

        $this->authorize("delete", $offre);

        $offre->delete();

        return response()->json([
            "status" => "success",
            "message" => "Offre supprimée avec succès",
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/offres/changerStatus/{id}",
     *     summary="Activate or deactivate an offer",
     *     tags={"Offres"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the offer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Offer status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Offre activée/désactivée"),
     *             @OA\Property(property="offre_status", type="string", example="active")
     *         )
     *     )
     * )
     */
    public function activerOrDesactiver(string $id)
    {
        $offre = Offre::find($id);

        if ($offre->status === 'active') {
            $offre->status = 'inactive';
            $offre->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Offre a ete desactiver',
                "offre_status" => $offre->status
            ], status: 200);

        } elseif ($offre->status === 'inactive') {
            $offre->status = 'active';
            $offre->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Offre a ete activer',
                "offre_status" => $offre->status
            ], 200);
        }
    }


}