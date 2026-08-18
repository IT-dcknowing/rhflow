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
        Schema::table('companies', function (Blueprint $table) {
            // Paramètres du système de présence
            $table->string('default_attendance_type', 50)->default('manual')->after('settings');
            $table->boolean('allow_multiple_attendance_types')->default(false)->after('default_attendance_type');
            $table->json('attendance_settings')->nullable()->after('allow_multiple_attendance_types');

            // Index pour les performances
            $table->index(['default_attendance_type', 'allow_multiple_attendance_types'], 'att_type_multiple_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex('att_type_multiple_index');
            $table->dropColumn([
                'default_attendance_type',
                'allow_multiple_attendance_types',
                'attendance_settings'
            ]);
        });
    }
};