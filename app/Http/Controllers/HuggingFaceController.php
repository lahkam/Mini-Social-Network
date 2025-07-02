<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HuggingFaceController extends Controller
{
    public function start()
    {
        return view('llm.start'); // Retourne une vue pour l'interface LLM
    }

    public function index(Request $request)
    {
        $promptu = $request->input('prompt'); 
        $prompt = "vous êtes un expert en IT, vous devez répondre à la question suivante," . $promptu . "  :  RÉPONDRE TOUJOURS AVEC LA MÊME LANGUE DE LA QUESTION. répondre à la question sans introduction ni conclusion"; // Ajoute un point d'interrogation à la fin

        $hfToken = env('HF_TOKEN'); 

        if (!$hfToken) {
            return response()->json(['error' => 'Le token Hugging Face (HF_TOKEN) n’est pas défini dans le fichier .env'], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $hfToken,
                'Content-Type' => 'application/json',
            ])->post('https://router.huggingface.co/nebius/v1/chat/completions', [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'model' => 'mistralai/Mistral-Nemo-Instruct-2407',
                'stream' => false,
            ]);

            // Vérifie si la requête a réussi
            if ($response->successful()) {
                $data = $response->json();
                $data2 = $data['choices'][0]['message']['content'] ?? 'Aucun contenu retourné';
                return view('llm.index', ['response' => $data2]); // Retourne une vue avec la réponse
            } else {
                // Gère une réponse non réussie
                return response()->json([
                    'error' => 'La requête à l’API Hugging Face a échoué',
                    'status' => $response->status(),
                    'body' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s’est produite : ' . $e->getMessage()], 500);
        }
    }
}

?>