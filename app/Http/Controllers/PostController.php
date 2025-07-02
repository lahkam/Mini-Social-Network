<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Récupérer les posts visibles pour l'utilisateur connecté
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $posts = Post::with(['user'])
            ->visibleTo($user)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $posts,
            'message' => 'Posts récupérés avec succès'
        ]);
    }

    /**
     * Créer un nouveau post normal
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:280',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Échec de la validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'type' => 'normal'
        ]);

        $post->load('user');

        return response()->json([
            'success' => true,
            'data' => $post,
            'message' => 'Post créé avec succès'
        ], 201);
    }

    /**
     * Obtenir les posts d'un utilisateur spécifique (si le visualiseur a accès)
     */
    public function getUserPosts(Request $request, $userId): JsonResponse
    {
        $viewer = Auth::user();
        
        if (!$viewer->canViewPostsOf(\App\Models\User::findOrFail($userId))) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès pour voir les posts de cet utilisateur'
            ], 403);
        }

        $posts = Post::with(['user'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $posts,
            'message' => 'Posts de l\'utilisateur récupérés avec succès'
        ]);
    }

    /**
     * Supprimer un post (uniquement par le propriétaire)
     */
    public function destroy($id): JsonResponse
    {
        $post = Post::findOrFail($id);
        
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé à supprimer ce post'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post supprimé avec succès'
        ]);
    }

    /**
     * Générer un post avec IA Gemini
     */
    public function generateAiPost(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'prompt' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prompt requis'
                ], 422);
            }

            $prompt = $request->prompt;
            $generatedContent = $this->callGeminiAPI($prompt);

            // Créer le post
            $post = Post::create([
                'user_id' => Auth::id(),
                'content' => $generatedContent,
                'type' => 'ai_generated',
                'ai_prompt' => $prompt
            ]);

            $post->load('user');

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Post IA généré avec succès'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Appel à l'API Gemini pour générer du contenu
     */
    private function callGeminiAPI($prompt): string
    {
        $apiKey = 'AIzaSyAX-FtB6rbzEy6H5hbJ3II1Mll7of_ojP8';
        
        // Fallback suggestions en cas d'échec de l'API
        $suggestions = [
            'motivation' => 'Restez motivé et persévérez dans vos études ! Chaque effort que vous investissez aujourd\'hui sera récompensé demain.',
            'étude' => 'Les études demandent de la discipline et de l\'organisation. Planifiez vos révisions et prenez des pauses régulières.',
            'programmation' => 'La programmation est un art qui se perfectionne avec la pratique. Commencez par maîtriser les fondamentaux.',
        ];
        
        try {
            // Préparer les données pour l'API Gemini
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Génère un post inspirant et motivant sur le thème : $prompt. Le post doit faire environ 200-250 caractères et être adapté à un réseau social étudiant."
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 100,
                    'temperature' => 0.7
                ]
            ];
            
            // URL de l'API Gemini
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=" . $apiKey;
            
            // Initialiser cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            // Exécuter la requête
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                $responseData = json_decode($response, true);
                
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $generatedText = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
                    
                    // Limiter à 280 caractères pour respecter les contraintes
                    if (strlen($generatedText) > 280) {
                        $generatedText = substr($generatedText, 0, 277) . '...';
                    }
                    
                    \Log::info('Contenu généré par l\'API Gemini : ' . $generatedText);
                    return $generatedText;
                }
            }
            
            // En cas d'échec de l'API, utiliser les suggestions prédéfinies
            \Log::warning('Échec de l\'API Gemini, utilisation du fallback pour : ' . $prompt);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'appel à l\'API Gemini : ' . $e->getMessage());
        }
        
        // Fallback intelligent basé sur le prompt
        $promptLower = strtolower(trim($prompt));
        
        if (isset($suggestions[$promptLower])) {
            return $suggestions[$promptLower];
        }
        
        // Génération contextuelle de fallback
        if (strpos($promptLower, 'motivation') !== false) {
            return "La motivation est le moteur de toute réussite. Gardez vos objectifs en tête et avancez pas à pas vers vos rêves !";
        } elseif (strpos($promptLower, 'étude') !== false || strpos($promptLower, 'étudier') !== false) {
            return "Les études sont un investissement dans votre futur. Organisez votre temps et créez un environnement propice à l'apprentissage.";
        } elseif (strpos($promptLower, 'code') !== false || strpos($promptLower, 'program') !== false) {
            return "Le code est un langage créatif. Pratiquez régulièrement et n'ayez pas peur de faire des erreurs, elles font partie de l'apprentissage.";
        } else {
            return "Voici une réflexion inspirée par « $prompt » : Votre idée mérite d'être explorée. Prenez le temps de réfléchir et d'agir pour atteindre vos objectifs !";
        }
    }
}
