<?php

namespace Modules\Declarations\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Declarations\Jobs\GenerateBulkBulletinsPDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BulletinQueueController extends Controller
{
    /**
     * Déclencher la génération de PDF en queue   
     */
    public function generateBulletinsPDF(Request $request)
    {
        try {
            $bulletinType = $request->input('bulletin_type'); // 1, 2, ou 3
            $periodeId = $request->input('periode_id');
            $filename = $request->input('filename', 'bulletins_' . time());

            if (!in_array($bulletinType, [1, 2, 3])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de bulletin invalide'
                ], 400);
            }

            // La periode doit appartenir a l'entreprise de l'utilisateur et contenir des bulletins :
            // sinon on renvoie un message clair au lieu de laisser le job echouer en erreur 500.
            $companyId = Auth::user()->company_id;
            $periode = \App\Models\PaiePeriode::where('id', $periodeId)->where('company_id', $companyId)->first();

            if (!$periode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Période introuvable pour votre entreprise.'
                ], 404);
            }

            $nbBulletins = \Modules\PaieSalaries\Models\PaySlip::where('company_id', $companyId)
                ->where('periode_id', $periodeId)
                ->count();

            if ($nbBulletins === 0) {
                return response()->json([
                    'success' => false,
                    'empty' => true,
                    'message' => "Aucun bulletin de paie n'a été généré pour la période « {$periode->nom} ». Lancez d'abord le calcul de la paie."
                ], 200);
            }

            // Dispatcher le job de manière SYNCHRONE pour éviter les problèmes de worker
            // On augmente le temps d'exécution pour ce script
            set_time_limit(600); // 10 minutes
            ini_set('memory_limit', '512M');

            \Log::info('Dispatching GenerateBulkBulletinsPDF job SYNCHRONOUSLY', [
                'bulletinType' => $bulletinType,
                'periodeId' => $periodeId,
                'userId' => Auth::id(),
                'filename' => $filename
            ]);

            GenerateBulkBulletinsPDF::dispatchSync(
                $bulletinType,
                $periodeId,
                Auth::id(),
                $filename
            );

            return response()->json([
                'success' => true,
                'message' => 'Génération des bulletins lancée en arrière-plan',
                'job_id' => 'bulletin_' . Auth::id() . '_' . $bulletinType
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer la progression du traitement
     */
    public function getBulletinProgress(Request $request)
    {
        try {
            // Debug logging
            \Log::info('getBulletinProgress called at ' . now()->toDateTimeString(), ['request' => $request->all()]);

            $bulletinType = $request->input('bulletin_type');
            $cacheKey = "bulletin_generation_" . Auth::id() . "_" . $bulletinType;

            $progress = cache()->get($cacheKey, [
                'progress' => 0,
                'message' => 'En attente...',
                'status' => 'pending',
                'file_path' => null
            ]);

            \Log::info('getBulletinProgress returning', ['progress' => $progress]);
            return response()->json($progress);

        }
        catch (\Exception $e) {
            \Log::error('getBulletinProgress error', ['error' => $e->getMessage()]);
            return response()->json([
                'progress' => 0,
                'message' => 'Erreur: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Télécharger le PDF généré
     */
    /**
     * Télécharger le PDF/ZIP généré
     */
    public function downloadBulletinFile(Request $request)
    {
        try {
            $filename = $request->input('filename');

            // Vérifier si c'est un ZIP ou un PDF
            // Le job retourne le chemin relatif "bulletins/filename.zip" ou "bulletins/filename.pdf"
            // Mais le frontend envoie souvent juste le nom de base

            // Essayer dictionnaire de chemins possibles
            $paths = [
                $filename, // Chemin complet si envoyé
                "bulletins/{$filename}.zip",
                "bulletins/{$filename}.pdf",
                "bulletins/{$filename}",
            ];

            $filePath = null;
            foreach ($paths as $path) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    $filePath = $path;
                    break;
                }
            }

            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }

            $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($filePath);
            $finalName = basename($filePath);

            return response()->download($fullPath, $finalName, [
                'Content-Type' => (strpos($finalName, '.pdf') !== false) ? 'application/pdf' : 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $finalName . '"'
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
