<?php

namespace Modules\Chatbot\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model = 'gemini-2.0-flash';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Envoyer une conversation à Gemini avec support pour les outils (Function Calling)
     */
    public function chat(array $history, array $tools = [])
    {
        if (empty($this->apiKey)) {
            return ['error' => 'Clé API Gemini manquante dans le fichier .env'];
        }

        try {
            $systemInstruction = null;
            $contents = [];

            foreach ($history as $msg) {

                // -- System prompt
                if ($msg['role'] === 'system') {
                    $systemInstruction = [
                        'parts' => [['text' => $msg['content']]]
                    ];
                    continue;
                }

                // -- Message assistant contenant un appel d'outil (tool_use / function_call)
                if (isset($msg['function_call'])) {
                    $contents[] = [
                        'role' => 'model',
                        'parts' => [
                            [
                                'functionCall' => [
                                    'name' => $msg['function_call']['name'],
                                    'args' => $msg['function_call']['args'] ?? [],
                                ]
                            ]
                        ]
                    ];
                    continue;
                }

                if ($msg['role'] === 'function' && isset($msg['function_response'])) {
                    $contents[] = [
                        'role' => 'user',
                        'parts' => [
                            [
                                'functionResponse' => [
                                    'name' => $msg['function_response']['name'],
                                    'response' => $msg['function_response']['response'],
                                ]
                            ]
                        ]
                    ];
                    continue;
                }

                // -- Message texte ordinaire (user / assistant)
                $role = ($msg['role'] === 'user') ? 'user' : 'model';
                if (!empty($msg['content'])) {
                    $contents[] = [
                        'role' => $role,
                        'parts' => [['text' => $msg['content']]]
                    ];
                }
            }

            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 2000,
                ]
            ];

            if ($systemInstruction) {
                $payload['system_instruction'] = $systemInstruction;
            }

            if (!empty($tools)) {
                $payload['tools'] = [
                    ['function_declarations' => $tools]
                ];
            }

            $response = Http::post(
                "https://generativelanguage.googleapis.com/v1/models/{$this->model}:generateContent?key=" . $this->apiKey,
                $payload
            );

            if ($response->successful()) {
                $data = $response->json();
                Log::debug('Gemini API Success Response:', $data);
                $candidate = $data['candidates'][0] ?? null;

                if (!$candidate) {
                    return ['error' => 'Réponse vide de Gemini.'];
                }

                $part = $candidate['content']['parts'][0] ?? null;

                // Si c'est un appel de fonction
                if (isset($part['functionCall'])) {
                    return [
                        'role' => 'assistant',
                        'action' => 'function_call',
                        'function_call' => [
                            // Gemini ne retourne pas d'ID natif — on en génère un pour la compatibilité
                            'id' => 'toolu_' . uniqid(),
                            'name' => $part['functionCall']['name'],
                            'args' => $part['functionCall']['args'] ?? [],
                        ]
                    ];
                }

                $text = $part['text'] ?? '';

                return [
                    'role' => 'assistant',
                    'content' => $text
                ];
            }

            Log::error('Gemini API Error: ' . $response->body());
            return ['error' => 'Erreur de communication avec Gemini : ' . ($response->json()['error']['message'] ?? 'Inconnue')];

        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return ['error' => 'Une erreur est survenue lors de la connexion à Gemini.'];
        }
    }
}