<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les noms de catégories intermédiaires générés (ex. "1A (SMIG)-H1") dépassent
     * les varchar(10) d'origine des grilles sectorielles -> SQLSTATE[22001] 1406.
     */
    public function up(): void
    {
        $database = DB::getDatabaseName();

        $colonnes = DB::select("
            SELECT TABLE_NAME, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME LIKE 'secteur\\_%'
              AND COLUMN_NAME = 'categorie'
              AND DATA_TYPE = 'varchar'
              AND CHARACTER_MAXIMUM_LENGTH < 100
        ", [$database]);

        foreach ($colonnes as $colonne) {
            $nullable = $colonne->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';

            DB::statement("ALTER TABLE `{$colonne->TABLE_NAME}` MODIFY `categorie` VARCHAR(100) {$nullable}");
        }
    }

    /**
     * Pas de retour en arrière : rétrécir la colonne tronquerait les catégories existantes.
     */
    public function down(): void
    {
        //
    }
};
