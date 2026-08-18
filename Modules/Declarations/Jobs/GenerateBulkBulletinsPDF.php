<?php

namespace Modules\Declarations\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\PaieSalaries\Models\PaySlip;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;


class GenerateBulkBulletinsPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bulletinType; // 1, 2, ou 3
    protected $periodeId;
    protected $userId;
    protected $filename;
    protected $chunkSize = 50; // Traiter par lots de 50

    public function __construct($bulletinType, $periodeId, $userId, $filename)
    {
        $this->bulletinType = $bulletinType;
        $this->periodeId = $periodeId;
        $this->userId = $userId;
        $this->filename = $filename;
        $this->onQueue('default');
        $this->tries = 3;
        $this->timeout = 3600; // 1 heure max
    }

    public function handle()
    {
        try {
            \Log::info('GenerateBulkBulletinsPDF job started', [
                'bulletinType' => $this->bulletinType,
                'periodeId' => $this->periodeId,
                'userId' => $this->userId,
                'filename' => $this->filename
            ]);

            // Notifier que le traitement commence
            $this->notifyProgress(0, 'Initialisation du traitement...');

            // Récupérer tous les bulletins du type spécifié avec les relations nécessaires
            $bulletins = PaySlip::with(['employee', 'periode'])
                ->where('periode_id', $this->periodeId)
                ->get();

            $totalBulletins = $bulletins->count();

            if ($totalBulletins === 0) {
                throw new \Exception("Aucun bulletin trouvé pour cette période.");
            }

            $processedCount = 0;
            $company = \App\Models\Company::find($this->userId ?\App\Models\User::find($this->userId)->company_id : auth()->user()->company_id);
            $exercice = \App\Models\PaieExercice::find($this->periodeId ?\App\Models\PaiePeriode::find($this->periodeId)->exercice_id : null);
            $periode = \App\Models\PaiePeriode::find($this->periodeId);

            $allHtml = view('declarations::pdf.bulk_bulletins_wrapper', [
                'bulletins' => $bulletins,
                'company' => $company,
                'exercice' => $exercice,
                'periode' => $periode,
                'bulletinType' => $this->bulletinType
            ])->render();

            // Générer le PDF avec DomPDF
            $pdf = Pdf::loadHTML($allHtml);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
            $pdfContent = $pdf->output();

            // Sauvegarder le PDF
            $filePath = "bulletins/{$this->filename}.pdf";
            Storage::disk('public')->put($filePath, $pdfContent);

            // Notifier succès
            $this->notifyProgress(100, 'Traitement terminé avec succès!', 'success', $filePath);

        }
        catch (\Exception $e) {
            \Log::error("Erreur génération PDF bulk: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            $this->notifyProgress(0, "Erreur: " . $e->getMessage(), 'error');
            throw $e;
        }
    }

    private function generateBulletinHTML($bulletin)
    {
        return view('declarations::pdf.bulk_bulletins', compact('bulletin'))->render();
    }

    private function notifyProgress($progress, $message, $status = 'processing', $filePath = null)
    {
        // Sauvegarder en cache pour polling
        $cacheKey = "bulletin_generation_{$this->userId}_{$this->bulletinType}";
        cache()->put($cacheKey, [
            'progress' => $progress,
            'message' => $message,
            'status' => $status,
            'file_path' => $filePath,
            'timestamp' => now()
        ], 3600);

        // Broadcast pour WebSocket (optionnel)
        try {
        // Option: Si vous avez des WebSocket configurés, vous pourriez utiliser:
        // broadcast(new BulletinProgressEvent($this->userId, $progress, $message, $status));
        }
        catch (\Exception $e) {
        // WebSocket non disponible, on continue avec le cache
        }
    }
}
