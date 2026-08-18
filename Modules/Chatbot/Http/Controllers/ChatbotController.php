<?php

namespace Modules\Chatbot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Chatbot\Services\PayrollChatbot;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    protected PayrollChatbot $chatbot;

    public function __construct(PayrollChatbot $chatbot)
    {
        $this->chatbot = $chatbot;
    }

    /**
     * Traiter une session de chat (Stateless)
     */
    public function chat(Request $request)
    {
        $request->validate([
            'history' => 'required|array',      // L'historique complet envoyé par le navigateur
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Non authentifié'], 401);

        // Appel au service avec l'historique brut et le contexte de page
        $reply = $this->chatbot->processStatelessChat(
            $request->history, 
            $request->message,
            $user,
            $request->context ?? null
        );

        return response()->json([
            'reply' => $reply
        ]);
    }

    /**
     * Générer le rapport de paie (Stateless)
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'history' => 'required|array'
        ]);

        $summary = $this->chatbot->generateReportSummary($request->history);

        return response()->json([
            'summary' => $summary
        ]);
    }

    /**
     * Récupérer le contenu du guide utilisateur (Markdown)
     */
    public function getGuideMarkdown()
    {
        $path = base_path('GUIDE_UTILISATEUR_RH_FLOW.md');
        if (file_exists($path)) {
            return response()->json([
                'markdown' => file_get_contents($path)
            ]);
        }
        return response()->json(['error' => 'Guide non trouvé'], 404);
    }
}
