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
        Schema::table('users', function (Blueprint $table) {
            // Ajout du champ username (optionnel)
            $table->string('username', 250)->nullable()->after('name');

            // Maintien des champs existants mais ajout de commentaires
            // email_verified_at existe déjà

            // Ajout du champ password_code pour la réinitialisation
            $table->string('password_code', 110)->nullable()->after('password');

            // Champ type pour les différents rôles (super_admin, company, hr, paie, employee)
            $table->string('type', 191)->default('employee')->after('password_code');

            // Avatar et préférences utilisateur
            $table->string('avatar', 191)->nullable()->after('type');
            $table->string('lang', 191)->default('fr')->after('avatar');

            // Système d'abonnement et limites
            $table->integer('plan')->nullable()->after('lang');
            $table->date('plan_expire_date')->nullable()->after('plan');
            $table->integer('plan_cpte_trait')->nullable()->after('plan_expire_date');
            $table->integer('requested_plan')->default(0)->after('plan_cpte_trait');
            $table->double('storage_limit', 8, 2)->default(0.00)->after('requested_plan');

            // Suivi de l'activité
            $table->timestamp('last_login')->nullable()->after('storage_limit');
            $table->integer('is_active')->default(1)->after('last_login');

            // Audit et traçabilité
            $table->string('created_by', 191)->after('is_active');
            $table->tinyInteger('active_status')->default(0)->after('created_by');
            $table->string('dark_mode', 100)->nullable()->after('active_status');
            $table->string('messenger_color', 191)->default('#2180f3')->after('dark_mode');

            // Couleurs personnalisables
            $table->string('colorone', 191)->nullable()->after('messenger_color');
            $table->string('colortwo', 191)->nullable()->after('colorone');

            // Configuration spécifique entreprise
            $table->integer('config_company')->nullable()->after('colortwo');
            $table->string('attendance_type', 250)->nullable()->after('config_company');
            $table->string('ip_serveur', 250)->nullable()->after('attendance_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Suppression de tous les champs ajoutés
            $table->dropColumn([
                'username',
                'password_code',
                'type',
                'avatar',
                'lang',
                'plan',
                'plan_expire_date',
                'plan_cpte_trait',
                'requested_plan',
                'storage_limit',
                'last_login',
                'is_active',
                'created_by',
                'active_status',
                'dark_mode',
                'messenger_color',
                'colorone',
                'colortwo',
                'config_company',
                'attendance_type',
                'ip_serveur',
            ]);
        });
    }
};
