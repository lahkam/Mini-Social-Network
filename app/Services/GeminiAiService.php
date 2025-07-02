<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        
        if (!$this->apiKey) {
            throw new Exception('Gemini API key not configured');
        }
    }

    /**
     * Generate content using Gemini AI
     */
    public function generatePost(string $prompt, int $maxLength = 280): string
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $this->buildPrompt($prompt, $maxLength)
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 150,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Ensure the generated text doesn't exceed the maximum length
                if (strlen($generatedText) > $maxLength) {
                    $generatedText = substr($generatedText, 0, $maxLength - 3) . '...';
                }
                
                return trim($generatedText);
            }

            Log::error('Gemini API error', ['response' => $response->body()]);
            throw new Exception('Failed to generate content from AI service');

        } catch (Exception $e) {
            Log::error('AI content generation failed', ['error' => $e->getMessage()]);
            throw new Exception('AI content generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Build the prompt for the AI model
     */
    private function buildPrompt(string $userPrompt, int $maxLength): string
    {
        return "Create a social media post based on this prompt: '{$userPrompt}'. 
                The post should be engaging, appropriate for social media, and no more than {$maxLength} characters. 
                Don't include hashtags unless specifically requested. 
                Make it conversational and authentic.";
    }

    /**
     * Validate that the generated content is appropriate
     */
    public function validateContent(string $content): bool
    {
        // Basic content validation
        $bannedWords = ['spam', 'inappropriate', 'offensive']; // Add more as needed
        
        foreach ($bannedWords as $word) {
            if (stripos($content, $word) !== false) {
                return false;
            }
        }
        
        return true;
    }
}
