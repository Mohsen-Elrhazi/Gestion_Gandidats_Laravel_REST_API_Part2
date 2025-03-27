<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\postulerOffre;
use App\Models\Candidature;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CandidatureController extends Controller
{
    /**
     * @OA\Post(
     *     path="http://127.0.0.1:8000/api/postuler/offres/{id}",
     *     summary="Submit a candidature for an offer",
     *     tags={"Candidatures"},
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
     *             required={"motivation"},
     *             @OA\Property(property="motivation", type="string", example="I am very interested in this position.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Candidature submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Candidature soumise avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function postuler(Request $request, $id)
    {
        // Validation du fichier CV
        $validator = Validator::make($request->all(), [
            'cv' => 'required|mimes:pdf,doc,docx|max:2048', // Max 2MB, fichier PDF ou Word
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Validation échouée",
                "errors" => $validator->errors()
            ], 400);
        }

        $offre = Offre::find($id);
        if (!$offre) {
            return response()->json([
                "status" => "error",
                "message" => "Offre non trouvée"
            ], 404);
        }

        $recruteur = User::find($offre->user_id);
        $candidat = Auth::user();

        // Sauvegarde du CV
        $cvPath = $request->file('cv')->store('cvs', 'public');
        $cvFilePath = storage_path("app/public/{$cvPath}");

        // Verifier si le fichier a bien été téléchargé et existe
        if (!$request->file('cv') || !file_exists($cvFilePath)) {
            return response()->json([
                "status" => "error",
                "message" => "Le fichier CV n'a pas pu être téléchargé ou est introuvable."
            ], 500);
        }

        try {
            Mail::send(new postulerOffre($recruteur, $candidat, $cvFilePath, $offre));

            $candidature = Candidature::create([
                'candidat_id' => $candidat->id,
                'offre_id' => $offre->id,
                'cv_path' => $cvPath,
                'date_candidature' => now(),
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Email envoyée avec succès",
                "candidats email" => $candidat->email,
                'candidat_id' => $candidat->id,
                'offre_id' => $offre->id,
                'cv_path' => $cvPath,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Erreur lors de l'envoi de l'email : " . $e->getMessage()
            ], 500);
        }

    }
}