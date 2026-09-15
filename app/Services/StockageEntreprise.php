<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Espace disque réellement occupé par les fichiers d'une entreprise (disque « public »).
 *
 * Les fichiers ne sont pas rangés par entreprise : on part des chemins enregistrés en base
 * (et des dossiers propres à un contrat ou à un salarié). Les PDF de bulletins générés en lot
 * portent un nom sans entreprise et ne sont pas comptés.
 */
class StockageEntreprise
{
    /** Octets utilisés, mis en cache 10 minutes, et reportés dans companies.current_storage_used (Go). */
    public static function octetsUtilises(Company $company): int
    {
        return Cache::remember('stockage_entreprise_' . $company->id, 600, function () use ($company) {
            $octets = array_sum(self::detail($company));
            $company->forceFill(['current_storage_used' => round($octets / 1073741824, 2)])->saveQuietly();

            return $octets;
        });
    }

    /** Octets par catégorie de fichiers. */
    public static function detail(Company $company): array
    {
        // Deux emplacements : le disque « public » (storage/app/public) et public/storage, où le logo,
        // la signature et le cachet sont déplacés directement. Quand public/storage est le lien
        // symbolique habituel, c'est le même dossier et rien n'est compté deux fois.
        $disque = Storage::disk('public');
        $racinePublique = public_path('storage');
        $local = fn ($chemin) => $racinePublique . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $chemin);

        $taille = function ($chemin) use ($disque, $local) {
            $chemin = ltrim(preg_replace('#^/?storage/#', '', (string) $chemin), '/');
            if ($chemin === '') {
                return 0;
            }
            // fileExists et non exists : un chemin incomplet (« signatures/ ») désigne un dossier
            if ($disque->fileExists($chemin)) {
                return $disque->size($chemin);
            }

            return is_file($local($chemin)) ? filesize($local($chemin)) : 0;
        };
        $tailleDossier = function ($dossier) use ($disque, $local) {
            $fichiers = [];
            if ($disque->directoryExists($dossier)) {
                foreach ($disque->allFiles($dossier) as $fichier) {
                    $fichiers[$fichier] = $disque->size($fichier);
                }
            }
            $dossierLocal = $local($dossier);
            if (is_dir($dossierLocal) && realpath($dossierLocal) !== realpath($disque->path($dossier))) {
                $iterateur = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dossierLocal, \FilesystemIterator::SKIP_DOTS));
                foreach ($iterateur as $fichier) {
                    $relatif = $dossier . '/' . str_replace(DIRECTORY_SEPARATOR, '/', substr($fichier->getPathname(), strlen($dossierLocal) + 1));
                    $fichiers[$relatif] ??= $fichier->getSize();
                }
            }

            return array_sum($fichiers);
        };
        $colonne = function ($table, $col) use ($company) {
            return Schema::hasTable($table) && Schema::hasColumn($table, $col)
                ? DB::table($table)->where('company_id', $company->id)->whereNotNull($col)->where($col, '!=', '')->pluck($col)
                : collect();
        };

        $detail = [];

        $detail['identite'] = ($company->logo ? $taille('logos/' . $company->logo) : 0)
            + ($company->electronic_signature ? $taille('signatures/' . $company->electronic_signature) : 0)
            + ($company->electronic_stamp ? $taille('stamps/' . $company->electronic_stamp) : 0);

        $detail['documents_entreprise'] = $colonne('company_documents', 'file_path')->sum($taille);

        $detail['contrats'] = DB::table('contracts')->where('company_id', $company->id)->pluck('id')
            ->sum(fn ($id) => $tailleDossier('contracts/' . $id));

        $detail['demandes'] = $colonne('demandes', 'file_path')->sum($taille);

        $detail['absences'] = $colonne('time_sheets', 'document')->sum($taille);

        $detail['documents_salaries'] = $colonne('employee_documents', 'document_value')
            ->sum(fn ($valeur) => $taille(json_decode($valeur, true)['path'] ?? ''));

        $utilisateurs = DB::table('employees')->where('company_id', $company->id)->whereNotNull('user_id')->pluck('user_id');
        $detail['dossiers_salaries'] = $utilisateurs->sum(fn ($id) => $tailleDossier('documents/' . $id));

        $detail['cmu_familles'] = $colonne('familys', 'document')->sum($taille);

        return $detail;
    }

    /** « 12,4 Mo », « 1,25 Go ». */
    public static function formater(int $octets): string
    {
        if ($octets >= 1073741824) {
            return number_format($octets / 1073741824, 2, ',', ' ') . ' Go';
        }
        if ($octets >= 1048576) {
            return number_format($octets / 1048576, 1, ',', ' ') . ' Mo';
        }

        return number_format($octets / 1024, 0, ',', ' ') . ' Ko';
    }
}
