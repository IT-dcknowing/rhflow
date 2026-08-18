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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Type de notification (info, success, warning, error)
            $table->string('title'); // Titre de la notification
            $table->text('message'); // Message de la notification
            $table->string('icon')->nullable(); // Icône pour l'affichage
            $table->string('color')->default('primary'); // Couleur du thème
            $table->json('data')->nullable(); // Données supplémentaires (URLs, IDs, etc.)
            $table->boolean('is_read')->default(false); // Statut de lecture
            $table->timestamp('read_at')->nullable(); // Date de lecture
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Utilisateur destinataire
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null'); // Utilisateur émetteur (optionnel)
            $table->string('source_type')->nullable(); // Type de source (App\Models\User, etc.)
            $table->unsignedBigInteger('source_id')->nullable(); // ID de la source
            $table->string('action_url')->nullable(); // URL d'action (optionnelle)
            $table->timestamp('expires_at')->nullable(); // Date d'expiration (optionnelle)
            $table->timestamps();

            // Index pour les performances
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
