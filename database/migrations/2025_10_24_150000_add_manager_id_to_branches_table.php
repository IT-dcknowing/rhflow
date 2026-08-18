<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Ajouter la colonne manager_id (nullable foreign key vers users)
            $table->unsignedBigInteger('manager_id')->nullable()->after('email');

            // Créer la foreign key
            $table->foreign('manager_id')->references('id')->on('users');

            // Index pour les performances
            $table->index(['manager_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Supprimer la foreign key et la colonne
            $table->dropForeign(['manager_id']);
            $table->dropIndex(['manager_id', 'company_id']);
            $table->dropColumn('manager_id');
        });
    }
};
