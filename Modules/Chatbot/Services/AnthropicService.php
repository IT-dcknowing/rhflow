<?php

namespace Modules\Chatbot\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    protected string $apiKey;
    protected string $version = '2023-06-01';
    protected string $baseUrl = 'https://api.anthropic.com';
    protected string $model = 'claude-3-5-sonnet-20241022';

    public function __construct()
    {
        $this->apiKey = env('ANTHROPIC_API_KEY');

        // Surcharge possible via .env (ex: ANTHROPIC_MODEL=anthropic/claude-3.5-sonnet)
        if ($m = env('ANTHROPIC_MODEL')) {
            $this->model = $m;
        }

        if ($url = env('ANTHROPIC_BASE_URL')) {
            $this->baseUrl = $url;
        }
    }

    /**
     * Envoyer une conversation à Anthropic Claude avec support des Tools (Function Calling)
     *
     * @param  array  $history  Tableau de messages (role: system/user/assistant/function)
     * @param  array  $tools    Définitions d'outils au format RHFlow (name, description, parameters)
     * @return array            ['role', 'content'] ou ['role', 'action', 'function_call', ...]
     */
    public function chat(array $history, array $tools = []): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'Clé API Anthropic manquante dans le fichier .env (ANTHROPIC_API_KEY)'];
        }

        try {
            /* ----------------------------------------------------------------
             * 1. Séparer le system prompt et construire les messages Anthropic
             * ---------------------------------------------------------------- */
            $systemPrompt = '';
            $messages = [];

            // -- Normalise un contenu quelconque (string, array...)
            //    Le frontend peut stocker {reply:"...", activity:[...]} dans content
            //    si la réponse serveur n'a pas été correctement dépaquetée côté JS.
            $normalizeContent = function ($c): string {
                if (is_string($c)) return $c;
                if (is_array($c)) {
                    // Cas courant : content = {reply:"...", activity:[...]} ou {content:"..."}
                    if (isset($c['reply']))   return is_string($c['reply'])   ? $c['reply']   : json_encode($c['reply'],   JSON_UNESCAPED_UNICODE);
                    if (isset($c['content'])) return is_string($c['content']) ? $c['content'] : json_encode($c['content'], JSON_UNESCAPED_UNICODE);
                    return json_encode($c, JSON_UNESCAPED_UNICODE);
                }
                return (string) $c;
            };

            // -- Sanitize: normalise + fusionne les messages consécutifs de même rôle
            $sanitizedHistory = [];
            foreach ($history as $msg) {
                $role = $msg['role'] ?? null;
                if (!$role) continue;

                // Normaliser le content en string dès maintenant
                if (isset($msg['content']) && !isset($msg['function_call'])) {
                    $msg['content'] = $normalizeContent($msg['content']);
                }

                // Skip function/tool messages that don't have function_response (malformed)
                if ($role === 'function' && !isset($msg['function_response'])) continue;

                // Skip assistant messages that have neither content nor function_call
                if ($role === 'assistant' && empty($msg['content']) && !isset($msg['function_call'])) continue;

                // Skip user/assistant messages with empty content
                if (in_array($role, ['user', 'assistant']) && !isset($msg['function_call']) && trim($msg['content'] ?? '') === '') continue;

                $last = end($sanitizedHistory);

                // Merge consecutive plain user or assistant messages (no tool calls)
                if ($last
                    && $last['role'] === $role
                    && $role !== 'function'
                    && !isset($msg['function_call'])
                    && !isset($last['function_call'])
                    && !empty($msg['content'])
                    && !empty($last['content'])
                ) {
                    $sanitizedHistory[count($sanitizedHistory) - 1]['content'] .= "\n\n" . $msg['content'];
                    continue;
                }

                $sanitizedHistory[] = $msg;
            }

            foreach ($sanitizedHistory as $msg) {

                // -- System prompt (extrait, pas dans messages[])
                if ($msg['role'] === 'system') {
                    $systemPrompt = $msg['content'];
                    continue;
                }

                // -- Message assistant contenant un appel d'outil (tool_use)
                if ($msg['role'] === 'assistant' && isset($msg['function_call'])) {
                    $inputArgs = $msg['function_call']['args'] ?? [];
                    if (empty($inputArgs)) {
                        $inputArgs = new \stdClass();
                    }

                    $messages[] = [
                        'role' => 'assistant',
                        'content' => [
                            [
                                'type' => 'tool_use',
                                'id' => $msg['function_call']['id'] ?? ('toolu_' . uniqid()),
                                'name' => $msg['function_call']['name'],
                                'input' => $inputArgs,
                            ]
                        ]
                    ];
                    continue;
                }

                // -- Résultat d'outil (tool_result) — doit être dans un message "user"
                if ($msg['role'] === 'function' && isset($msg['function_response'])) {
                    $content = $msg['function_response']['response']['content'] ?? '';
                    $messages[] = [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'tool_result',
                                'tool_use_id' => $msg['function_response']['id'] ?? ('toolu_' . uniqid()),
                                'content' => is_string($content)
                                    ? $content
                                    : json_encode($content, JSON_UNESCAPED_UNICODE),
                            ]
                        ]
                    ];
                    continue;
                }

                // -- Message texte ordinaire (user / assistant)
                $textContent = $normalizeContent($msg['content'] ?? '');
                if (!empty(trim($textContent))) {
                    $messages[] = [
                        'role'    => $msg['role'],
                        'content' => $textContent,
                    ];
                }
            }

            /* ----------------------------------------------------------------
             * 2. Convertir les tools au format Anthropic (input_schema)
             * ---------------------------------------------------------------- */
            $anthropicTools = [];
            foreach ($tools as $tool) {
                $schema = $tool['parameters'] ?? ['type' => 'object', 'properties' => (object) []];

                // S'assurer que properties n'est pas un tableau vide (Anthropic rejette [])
                if (isset($schema['properties']) && $schema['properties'] === []) {
                    $schema['properties'] = (object) [];
                }

                $anthropicTools[] = [
                    'name' => $tool['name'],
                    'description' => $tool['description'] ?? '',
                    'input_schema' => $schema,
                ];
            }

            /* ----------------------------------------------------------------
             * 3. Construire le payload et appeler l'API
             * ---------------------------------------------------------------- */
            $payload = [
                'model'      => $this->model,
                'max_tokens' => 350,
                'messages'   => $messages,
            ];

            if (!empty($systemPrompt)) {
                $payload['system'] = $systemPrompt;
            }

            if (!empty($anthropicTools)) {
                $payload['tools'] = $anthropicTools;
            }

            $headers = [
                'x-api-key' => $this->apiKey,
                'anthropic-version' => $this->version,
                'content-type' => 'application/json',
            ];

            // Si on utilise OpenRouter, il faut absolument utiliser Authorization: Bearer
            if (str_contains($this->baseUrl, 'openrouter.ai')) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
                $headers['HTTP-Referer'] = env('APP_URL', 'https://rhflow.dc-knowing.com');
                $headers['X-Title'] = env('APP_NAME', 'RHFLOW');
            }

            $response = Http::withHeaders($headers)->withoutVerifying()->timeout(60)->post($this->baseUrl . '/v1/messages', $payload);

            /* ----------------------------------------------------------------
             * 4. Parser la réponse
             * ---------------------------------------------------------------- */
            if ($response->successful()) {
                $data = $response->json();
                Log::debug('Anthropic API Success Response:', $data);
                $stopReason = $data['stop_reason'] ?? '';

                // -- L'IA veut appeler un outil
                if ($stopReason === 'tool_use') {
                    foreach ($data['content'] as $block) {
                        if ($block['type'] === 'tool_use') {
                            return [
                                'role' => 'assistant',
                                'action' => 'function_call',
                                'function_call' => [
                                    'id' => $block['id'],    // ⚠️ Indispensable pour tool_result
                                    'name' => $block['name'],
                                    'args' => $block['input'] ?? [],
                                ],
                                'content' => null,
                            ];
                        }
                    }
                }

                // -- Réponse texte normale
                $text = collect($data['content'] ?? [])
                    ->where('type', 'text')
                    ->pluck('text')
                    ->join("\n");

                return ['role' => 'assistant', 'content' => $text];
            }

            // -- Erreur HTTP
            $errorMsg = $response->json()['error']['message'] ?? $response->body();
            Log::error('Anthropic API Error: ' . $errorMsg);
            return ['error' => 'Veuillez vérifier vos quotas IA.'];

        } catch (\Exception $e) {
            Log::error('Anthropic Exception: ' . $e->getMessage());
            return ['error' => 'Erreur de connexion à Anthropic : ' . $e->getMessage()];
        }
    }
}