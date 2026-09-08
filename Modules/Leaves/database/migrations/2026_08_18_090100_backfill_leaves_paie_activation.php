<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reprise des congés existants.
 *
 * Avant l'activation explicite, l'approbation d'un congé créait directement une ligne
 * "Allocation congé". Ces congés doivent apparaître comme déjà activés et être rattachés
 * à leur ligne de paie, sinon une activation depuis l'écran créerait un second versement.
 */
return new class extends Migration
{
    public function up(): void
    {
        $leaves = DB::table('leaves')
            ->whereIn('status', ['Approuvé', 'Démarré', 'Terminé'])
            ->whereNull('deleted_at')
            ->get();

        foreach ($leaves as $leave) {
            // Seules les lignes encore vivantes comptent : une allocation soft-deleted
            // signifie que le congé ne pèse plus sur la paie, donc "non activé".
            $allowance = DB::table('allowances')
                ->where('title', 'Allocation congé')
                ->where('employee_id', $leave->employee_id)
                ->where('periode_id', $leave->periode_id)
                ->whereNull('deleted_at')
                ->where(function ($query) use ($leave) {
                    // Pas déjà réclamée par un autre congé.
                    $query->where('code', 'not like', 'CONGE-%')
                          ->orWhere('code', 'CONGE-' . $leave->id);
                })
                ->orderBy('id')
                ->first();

            if (!$allowance) {
                continue;
            }

            DB::table('allowances')
                ->where('id', $allowance->id)
                ->update(['code' => 'CONGE-' . $leave->id]);

            DB::table('leaves')
                ->where('id', $leave->id)
                ->update([
                    'is_active' => 1,
                    'allowance_id' => $allowance->id,
                    'activated_periode_id' => $leave->periode_id,
                    'activated_at' => $allowance->created_at ?? now(),
                ]);
        }
    }

    public function down(): void
    {
        // On ne remet pas les anciens codes '123' : ils n'étaient pas uniques et
        // le rattachement congé <-> allocation reste correct sans la colonne is_active.
        DB::table('leaves')->update([
            'is_active' => 0,
            'allowance_id' => null,
            'activated_periode_id' => null,
            'activated_at' => null,
        ]);
    }
};
