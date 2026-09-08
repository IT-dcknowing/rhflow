<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rattache une retenue de prêt (code 500) au prêt qui l'a produite.
 *
 * Sans cette colonne, impossible de retrouver de façon sûre l'échéance d'un prêt
 * précis quand un employé a plusieurs prêts sur la même période : le libellé seul
 * ne suffit pas à les distinguer.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('retenues', 'loan_id')) {
            Schema::table('retenues', function (Blueprint $table) {
                $table->unsignedBigInteger('loan_id')->nullable()->after('employee_id');
                $table->index(['loan_id', 'periode_id']);
            });
        }

        // Reprise : rattacher les retenues code 500 existantes lorsque la correspondance
        // (employé + période + libellé) ne désigne qu'un seul prêt.
        $retenues = DB::table('retenues')
            ->where('code', 500)
            ->whereNull('loan_id')
            ->get();

        foreach ($retenues as $retenue) {
            $candidats = DB::table('loans')
                ->where('employee_id', $retenue->employee_id)
                ->whereNull('deleted_at')
                ->where(function ($query) use ($retenue) {
                    $query->where('title', $retenue->libelle)
                          ->orWhere('title', str_replace('Prêt : ', '', $retenue->libelle));
                })
                ->pluck('id');

            if ($candidats->count() === 1) {
                DB::table('retenues')->where('id', $retenue->id)->update(['loan_id' => $candidats->first()]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('retenues', 'loan_id')) {
            Schema::table('retenues', function (Blueprint $table) {
                $table->dropIndex(['loan_id', 'periode_id']);
                $table->dropColumn('loan_id');
            });
        }
    }
};
