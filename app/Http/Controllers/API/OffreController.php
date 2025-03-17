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
class OffreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $offres = Offre::all();
    //     return response()->json([
    //         "status" => "success",
    //         "data"=> $offres
    //     ],200);
    // }

    public function index()
    {
        try{
              $user=Auth::User();
        $offres = Offre::where("user_id", $user->id)->get();
        return response()->json([
            "status" => "success",
            "user"=> $user,
            "data"=> $offres
        ],200);
        }catch(\Exception $e){
            return response()->json([
                "status" => "error",
                "message"=> "eror". $e->getMessage(),
            ],200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

       $validator= Validator::make($request->all(),[
            'title' =>'required',
            'description' =>'required',
            'location' =>'required',
            'contract_type' =>'required',
       ]);

       if($validator->fails()){
        return response()->json([
            "status" => "error",
            "message" => "Validation échouée",
         ]);
       }
       
         $user= Auth::user();
        //  $user= auth()->user();
        $offre= new Offre();
        
        $offre->title=$request->title;
        $offre->description=$request->description;
        $offre->location=$request->location;
        $offre->contract_type=$request->contract_type;
        $offre->user_id= $user->id;
        
         $offre->save();
         
         return response()->json([
            "status" => "success",
            "message" => "offre a ete enregistre",
            "data" => $offre,
         ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Offre $offre)
    {
        return response()->json([
            "status" => "successs",
            "data" => $offre,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offre $offre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offre $offre)
    {
        $validator= Validator::make($request->all(),[
            'title' =>'required',
            'description' =>'required',
            'location' =>'required',
            'contract_type' =>'required',
       ]);

       if($validator->fails()){
        return response()->json([
            "status" => "error",
            "message" => "Validation échouée",
         ],400);
       }
// $offre=Offre::find($id);
       $offre->title=$request->title;
       $offre->description=$request->description;
       $offre->location=$request->location;
      $offre->contract_type=$request->contract_type;

      $offre->update();
    
      return response()->json([
        "status" => "success",
        "message" => "offre a ete modifie",
     ],200);
   }
        
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offre $offre)
    {
        $offre->delete();
        
        return response()->json([
            "status" => "success",
            "message" => "offre a ete supprimé",
         ],200);
    }

    

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

    // Récupération des informations nécessaires
    $offre = Offre::find($id);
    if (!$offre) {
        return response()->json([
            "status" => "error",
            "message" => "Offre non trouvée"
        ], 404);
    }
    
    $recruteur = User::find($offre->user_id);
    $candidat = Auth::user();

    $recruteur->email;
    $candidat->email;
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
       
        $candidature=Candidature::create([
            'user_id' => $candidat->id,
            'offre_id' => $offre->id,
            'cv_path' => $cvPath,
            'date_candidature' =>now(),
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Email envoyée avec succès",
            "candidats email" => $candidat->email,
            'user_id' => $candidat->id,
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