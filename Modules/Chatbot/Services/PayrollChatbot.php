<?php

namespace Modules\Chatbot\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Modules\Employees\Models\Employee;
use Modules\Contracts\Models\Contract;
use App\Models\PaiePeriode;
use Modules\Chatbot\Services\GlobalAgentTool;

class PayrollChatbot
{
    protected $ai;
    protected $agentTools;

    public function __construct()
    {
        $provider = env('CHATBOT_PROVIDER', 'gemini');

        if ($provider === 'anthropic') {
            $this->ai = app(AnthropicService::class);
        } else {
            $this->ai = app(GeminiService::class);
        }

        $this->agentTools = app(GlobalAgentTool::class);
    }

    /**
     * Récupère le contenu du guide utilisateur
     */
    protected function getGuideContext(): string
    {
        $path = base_path('GUIDE_UTILISATEUR_RH_FLOW.md');
        if (File::exists($path)) {
            $content = File::get($path);
            // Limiter à 500 caractères pour économiser les tokens API
            if (mb_strlen($content) > 500) {
                $content = mb_substr($content, 0, 500) . "...";
            }
            return $content;
        }
        return "";
    }

    /**
     * Récupère les statistiques et alertes techniques réelles (LE CERVEAU)
     */
    protected function getCompanyStats($user): array
    {
        $companyId = $user->company_id;
        
        $currentPeriod = PaiePeriode::where('company_id', $companyId)
            ->whereIn('statut', ['ouvert', 'en_cours'])
            ->latest()
            ->first();

        // Employés sans contrat actif
        $withoutContract = Employee::where('company_id', $companyId)
            ->where('is_active', 1)
            ->whereDoesntHave('contracts', function($q) {
                $q->where('status', 'accept');
            })
            ->select('id', 'name')
            ->get();

        return [
            'total_employees'  => Employee::where('company_id', $companyId)->where('is_active', 1)->count(),
            'active_contracts' => Contract::whereHas('employee', function($q) use ($companyId) {
                $q->where('company_id', $companyId)->where('is_active', 1);
            })->where('status', 'accept')->count(),
            'current_period'   => $currentPeriod ? [
                'id' => $currentPeriod->id,
                'nom' => $currentPeriod->nom,
                'statut' => $currentPeriod->statut,
                'bulletins' => $currentPeriod->bulletins()->count()
            ] : null,
            'alerts' => [
                'missing_contracts' => $withoutContract->toArray(),
                'pending_leaves'    => \Modules\Leaves\Models\Leave::where('company_id', $companyId)->where('status', 'Pending')->count(),
            ]
        ];
    }

    /**
     * Construit le prompt système expert complet pour RH Flow
     */
    protected function buildSystemPrompt($user, $context = null): string
    {
        $userName = $user->name ?? 'le gestionnaire';
        $companyName = $user->company->name ?? 'l\'entreprise';
        $date = now()->locale('fr')->isoFormat('dddd D MMMM YYYY');
          $stats = $this->getCompanyStats($user);

        $periodeLine = $stats['current_period']
            ? "Période active: #{$stats['current_period']['id']} {$stats['current_period']['nom']} ({$stats['current_period']['statut']}) | {$stats['current_period']['bulletins']} bulletins"
            : "⚠️ Aucune période ouverte.";

        $alertes = [];
        if (!empty($stats['alerts']['missing_contracts'])) {
            $alertes[] = count($stats['alerts']['missing_contracts']) . " employé(s) sans contrat";
        }
        if ($stats['alerts']['pending_leaves'] > 0) {
            $alertes[] = "{$stats['alerts']['pending_leaves']} congé(s) en attente";
        }
        $alerteLine = $alertes ? implode(' | ', $alertes) : "Aucune alerte";
        $guide = $this->getGuideContext();

        return <<<PROMPT
Tu es **Donalde**, assistante IA RH de **{$companyName}** (utilisateur: {$userName}). Date: {$date}.

## ENTREPRISE
- Effectif: {$stats['total_employees']} | Contrats actifs: {$stats['active_contracts']}
- {$periodeLine}
- Alertes: {$alerteLine}

## RÈGLES
1. Agis de façon autonome. N'attends pas de confirmation si tu as les données.
2. Chaîne les outils: audit -> calcul -> résumé.
3. Propose 1-2 actions pertinentes en fin de message.
4. Réponds en français, concis et précis.

## OUTILS
audit_company | search_period | hire_employee | manage_leave | prepare_period | run_payroll_calculation | validate_payslips | get_payroll_summary | generate_declarations | create_event | get_payroll_variables

{$guide}
PROMPT;
    }


    /**
     * Définitions des outils (Global Agent Edition)
     */
    protected function getAvailableTools(): array
    {
        return [
            [
                'name' => 'audit_company',
                'description' => "Analyse globale de l'état RH : contrats manquants, congés en attente, alertes de paie.",
                'parameters' => ['type' => 'object', 'properties' => (object)[]]
            ],
            [
                'name' => 'search_period',
                'description' => "Recherche une période de paie par son nom (ex: 'décembre 2025', '2025') pour obtenir son ID.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => "Le texte à rechercher (ex: 'décembre', '2025')."],
                    ],
                    'required' => ['query']
                ]
            ],
            [
                'name' => 'get_payroll_variables',
                'description' => "Récupère la liste des primes et retenues déjà enregistrées pour une période donnée. À utiliser systématiquement AVANT le calcul massif.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'periode_id' => [
                            'type' => 'integer',
                            'description' => "ID de la période à inspecter."
                        ],
                    ],
                    'required' => ['periode_id']
                ]
            ],
            [
                'name' => 'hire_employee',
                'description' => "Crée un nouvel employé et son compte utilisateur dans le système.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => "Nom complet de l'employé"],
                        'email' => ['type' => 'string', 'description' => "Email professionnel"],
                        'salary' => ['type' => 'number', 'description' => "Salaire de base proposé"],
                        'gender' => ['type' => 'string', 'enum' => ['Male', 'Female']],
                    ],
                    'required' => ['name']
                ]
            ],
            [
                'name' => 'manage_leave',
                'description' => "Valide ou rejette une demande de congé.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'leave_id' => ['type' => 'integer'],
                        'status' => ['type' => 'string', 'enum' => ['Approuvé', 'Rejeté']],
                        'reason' => ['type' => 'string', 'description' => "Motif du rejet ou commentaire."],
                    ],
                    'required' => ['leave_id', 'status']
                ]
            ],
            [
                'name' => 'prepare_period',
                'description' => "Prépare la prochaine période de paie (crée l'exercice si besoin et reporte les éléments).",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'month_name' => ['type' => 'string', 'description' => "Nom du mois (Facultatif, déduit si vide)."],
                    ]
                ]
            ],
            [
                'name' => 'run_payroll_calculation',
                'description' => "Lance le calcul massif des bulletins de paie.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'periode_id' => ['type' => 'integer'],
                    ],
                    'required' => ['periode_id']
                ]
            ],
            [
                'name' => 'validate_payslips',
                'description' => "Valide les bulletins d'une période et la marque comme payée. À appeler après confirmation de l'utilisateur que tout est correct.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'periode_id' => ['type' => 'integer', 'description' => "ID de la période à valider."],
                        'date_paiement' => ['type' => 'string', 'description' => "Date effective de paiement au format YYYY-MM-DD (facultatif, aujourd'hui par défaut)."],
                    ],
                    'required' => ['periode_id']
                ]
            ],
            [
                'name' => 'get_payroll_summary',
                'description' => "Génère le Livre de Paie pour une période : masse salariale, net à payer, charges patronales, coût total employeur.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'periode_id' => ['type' => 'integer'],
                    ],
                    'required' => ['periode_id']
                ]
            ],
            [
                'name' => 'generate_declarations',
                'description' => "Génère le résumé des déclarations sociales (CNPS, ITS) pour une période, prêt pour la télédéclaration.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'periode_id' => ['type' => 'integer'],
                    ],
                    'required' => ['periode_id']
                ]
            ],
            [
                'name' => 'create_event',
                'description' => "Ajoute un événement au calendrier d'entreprise.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => ['type' => 'string'],
                        'startDate' => ['type' => 'string', 'description' => "Date YYYY-MM-DD"],
                        'description' => ['type' => 'string'],
                    ],
                    'required' => ['title', 'startDate']
                ]
            ]
        ];
    }

    /**
     * Traiter le chat (retourne reply + activity_log)
     */
    public function processStatelessChat(array $history, string $userMessage, $user, $context = null): array
    {
        $systemPrompt = [
            'role'    => 'system',
            'content' => $this->buildSystemPrompt($user, $context)
        ];

        $history[] = ['role' => 'user', 'content' => $userMessage];
        $conversation = $history;
        array_unshift($conversation, $systemPrompt);

        $tools = $this->getAvailableTools();

        // Journal des actions de l'agent (visible par l'utilisateur)
        $activityLog = [];

        $maxTurns = 8;
        for ($i = 0; $i < $maxTurns; $i++) {
            Log::info("[AI Agent] Turn $i - Sending conversation to AI Provider");
            $aiResponse = $this->ai->chat($conversation, $tools);

            if (isset($aiResponse['error'])) {
                return ['reply' => $aiResponse['error'], 'activity' => $activityLog];
            }

            if (isset($aiResponse['action']) && $aiResponse['action'] === 'function_call') {
                $call = $aiResponse['function_call'];
                $args = $call['args'] ?? [];

                // Enregistrer AVANT d'exécuter
                $logEntry = [
                    'tool'   => $call['name'],
                    'args'   => $args,
                    'status' => 'running',
                    'label'  => $this->getToolLabel($call['name']),
                ];

                $result = $this->executeTool($call['name'], $args, $user);

                // Enregistrer le résultat
                $logEntry['status']  = isset($result['error']) ? 'error' : 'success';
                $logEntry['summary'] = $result['message'] ?? ($result['error'] ?? json_encode($result));
                $activityLog[] = $logEntry;

                Log::info("[AI Agent] Tool called: {$call['name']}", ['args' => $args, 'result' => $result]);

                $toolId = $call['id'] ?? ('toolu_' . uniqid());

                $conversation[] = [
                    'role'          => 'assistant', 
                    'function_call' => [
                        'id'   => $toolId,
                        'name' => $call['name'],
                        'args' => $args,
                    ],
                    'content'       => $aiResponse['content'] ?? null
                ];
                $conversation[] = [
                    'role' => 'function',
                    'function_response' => [
                        'id'       => $toolId,
                        'name'     => $call['name'], 
                        'response' => ['content' => $result]
                    ],
                    'content' => null
                ];
                continue;
            }

            return [
                'reply'    => $aiResponse['content'] ?: ($activityLog ? "✅ Action terminée avec succès." : "Je n'ai pas pu obtenir de réponse textuelle."),
                'activity' => $activityLog,
            ];
        }

        return [
            'reply'    => "Désolé, je n'ai pas pu finaliser l'action après plusieurs tentatives.",
            'activity' => $activityLog,
        ];
    }

    /**
     * Libellé humain pour chaque outil
     */
    protected function getToolLabel(string $toolName): string
    {
        return [
            'audit_company'          => '🔍 Audit des dossiers RH',
            'search_period'          => '🔎 Recherche de période de paie',
            'get_payroll_variables'  => '📋 Inspection des primes & retenues',
            'prepare_period'         => '📅 Création de la période de paie',
            'run_payroll_calculation'=> '⚙️ Calcul des bulletins de paie',
            'validate_payslips'      => '✅ Validation & clôture des bulletins',
            'get_payroll_summary'    => '📊 Génération du livre de paie',
            'generate_declarations'  => '🏛️ Calcul des déclarations sociales',
            'hire_employee'          => '👤 Recrutement d\'un employé',
            'manage_leave'           => '🏖️ Gestion d\'une demande de congé',
            'create_event'           => '📆 Ajout d\'un événement calendrier',
        ][$toolName] ?? "👩‍💼 Exécution : $toolName";
    }

    /**
     * Dispatcher Global
     */
    protected function executeTool(string $name, array $args, $user)
    {
        $companyId = $user->company_id;

        try {
            switch ($name) {
                case 'audit_company':
                    return $this->agentTools->audit_company($companyId);
                case 'search_period':
                    return $this->agentTools->search_period($companyId, $args['query']);
                case 'get_payroll_variables':
                    return $this->agentTools->get_payroll_variables($companyId, $args['periode_id']);
                case 'hire_employee':
                    return $this->agentTools->hire_employee($companyId, $args);
                case 'manage_leave':
                    return $this->agentTools->manage_leave($companyId, $args['leave_id'], $args['status'], $args['reason'] ?? null);
                case 'prepare_period':
                    return $this->agentTools->prepare_period($companyId, $args['month_name'] ?? null);
                case 'run_payroll_calculation':
                    return $this->agentTools->run_payroll($companyId, $args['periode_id']);
                case 'validate_payslips':
                    return $this->agentTools->validate_payslips($companyId, $args['periode_id'], $args['date_paiement'] ?? null);
                case 'get_payroll_summary':
                    return $this->agentTools->get_payroll_summary($companyId, $args['periode_id']);
                case 'generate_declarations':
                    return $this->agentTools->generate_declarations($companyId, $args['periode_id']);
                case 'create_event':
                    return $this->agentTools->create_event($companyId, $args['title'], $args['startDate'], $args['description'] ?? null);
                default:
                    return ['error' => "Outil '$name' non reconnu."];
            }
        } catch (\Exception $e) {
            Log::error("Error executing AI tool $name: " . $e->getMessage());
            return ['error' => "Erreur technique lors de l'exécution de l'outil."];
        }
    }

    public function generateReportSummary(array $history)
    {
        // ... (Logique inchangée pour le rapport)
        $reportPrompt = ['role' => 'system', 'content' => "Génère un rapport RH HTML professionnel..."];
        $aiResponse = $this->ai->chat([$reportPrompt, ['role' => 'user', 'content' => "Analyse la conversation..."]]);
        return ['html' => $aiResponse['content'] ?? 'Rapport indisponible', 'type' => 'html'];
    }
}
