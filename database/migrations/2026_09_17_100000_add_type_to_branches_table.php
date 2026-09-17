<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Distingue le siège des succursales.
     *
     * Les sites déjà enregistrés prennent « succursale », le cas le plus courant :
     * le siège se corrige ensuite à la main depuis Paramètres › Sites.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('type')->default('succursale')->after('code');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
