<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute l'activation du congé pour la paie :
     * un congé approuvé n'entre dans la paie que s'il est activé sur une période.
     */
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('leaves', 'is_active')) {
                // 0 = congé non pris en compte dans la paie, 1 = activé sur une période
                $table->boolean('is_active')->default(false)->after('leave_statut');
            }
            if (!Schema::hasColumn('leaves', 'allowance_id')) {
                // Ligne "Allocation congé" générée dans allowances (nullable : soft-deleted à la désactivation)
                $table->unsignedBigInteger('allowance_id')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('leaves', 'activated_periode_id')) {
                // Période de paie sur laquelle le congé est activé (peut différer de periode_id, la période de saisie)
                $table->unsignedBigInteger('activated_periode_id')->nullable()->after('allowance_id');
            }
            if (!Schema::hasColumn('leaves', 'activated_at')) {
                $table->timestamp('activated_at')->nullable()->after('activated_periode_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            foreach (['activated_at', 'activated_periode_id', 'allowance_id', 'is_active'] as $column) {
                if (Schema::hasColumn('leaves', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
