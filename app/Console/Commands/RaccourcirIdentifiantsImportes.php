<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Remplace les identifiants de connexion générés par l'import Excel
 * (« prenom.nom.complet70 ») par le format du formulaire de création (« KOUA70 »).
 *
 * Seuls les identifiants construits automatiquement à partir du nom sont visés :
 * un identifiant saisi dans le fichier d'import ne correspond pas à ce motif et reste intact.
 * Le mot de passe n'est pas touché.
 */
class RaccourcirIdentifiantsImportes extends Command
{
    protected $signature = 'employes:raccourcir-identifiants
        {--societe=* : Limiter aux comptes de ces identifiants de société}
        {--appliquer : Enregistrer les nouveaux identifiants (sans cette option : simulation, rien n\'est modifié)}';

    protected $description = 'Remplace les identifiants « prenom.nom70 » créés par l\'import Excel par le format court du formulaire (ex. KOUA70)';

    public function handle(): int
    {
        $appliquer = (bool) $this->option('appliquer');
        $societes = array_filter((array) $this->option('societe'));

        $comptes = User::query()
            ->whereIn('id', DB::table('employees')->whereNotNull('user_id')->select('user_id'))
            ->where('username', 'like', '%.%')
            ->when($societes, fn ($q) => $q->whereIn('company_id', $societes))
            ->orderBy('company_id')
            ->orderBy('name')
            ->get()
            // Motif exact de l'import : slug du nom avec des points + chiffres
            ->filter(fn ($u) => preg_match('/^' . preg_quote(Str::slug($u->name, '.'), '/') . '\d+$/', (string) $u->username))
            ->values();

        $this->info(($appliquer ? 'MODIFICATION' : 'SIMULATION (rien n\'est modifié)') . ' — ' . $comptes->count() . ' identifiant(s) issus de l\'import');
        if ($comptes->isEmpty()) {
            return self::SUCCESS;
        }

        if ($appliquer) {
            $fichier = 'corrections/identifiants-importes-' . now()->format('Ymd-His') . '.json';
            \Storage::disk('local')->put($fichier, $comptes->map->only(['id', 'company_id', 'name', 'username'])->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->line('Sauvegarde des anciens identifiants : storage/app/' . $fichier);
        }

        $lignes = [];
        DB::beginTransaction();
        try {
            foreach ($comptes as $compte) {
                $nouveau = User::genererUsername($compte->name);
                $lignes[] = [$compte->company_id, $compte->name, $compte->username, $nouveau];
                // Enregistré aussi en simulation (annulé ensuite) pour que les doublons soient évités d'un compte à l'autre
                DB::table('users')->where('id', $compte->id)->update(['username' => $nouveau, 'updated_at' => now()]);
            }
            $appliquer ? DB::commit() : DB::rollBack();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Aucun identifiant modifié : ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->table(['Société', 'Employé', 'Ancien identifiant', 'Nouvel identifiant'], $lignes);
        $this->line($appliquer
            ? 'Identifiants modifiés. Communiquez les nouveaux identifiants aux employés concernés.'
            : 'Simulation : les nouveaux identifiants seront tirés à nouveau lors de l\'exécution avec --appliquer.');

        return self::SUCCESS;
    }
}
