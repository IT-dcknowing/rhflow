<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Seul le salarié signe le contrat : la signature de l'entreprise n'est plus
     * ni écrite ni affichée nulle part.
     */
    public function up(): void
    {
        if (Schema::hasColumn('contracts', 'company_signature')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('company_signature');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('contracts', 'company_signature')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->text('company_signature')->nullable()->after('employee_signature');
            });
        }
    }
};
