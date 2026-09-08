<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Conserve les retenues retirées quand un prêt est désactivé.
 *
 * Le modèle Retenue n'utilise pas SoftDeletes (la colonne deleted_at existe mais le
 * trait est absent), et aucune requête de paie ne filtre is_active : sortir une
 * retenue de la paie impose donc de supprimer la ligne. On sauvegarde ses attributs
 * ici pour pouvoir la restaurer à l'identique — montant compris, celui-ci pouvant
 * avoir été ajusté période par période.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('loans', 'retenues_backup')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->json('retenues_backup')->nullable()->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('loans', 'retenues_backup')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn('retenues_backup');
            });
        }
    }
};
