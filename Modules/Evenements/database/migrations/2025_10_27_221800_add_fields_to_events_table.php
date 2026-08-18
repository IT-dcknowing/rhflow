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
        // Création de la table des types d'événements
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#3b82f6');
            $table->string('icon')->default('calendar');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::table('events', function (Blueprint $table) {
            // Ajout des champs pour la gestion des types d'événements
            $table->foreignId('event_type_id')->nullable()->constrained('event_types')->onDelete('cascade');
            
            // Champs pour les rappels et notifications
            $table->boolean('send_reminder')->default(false);
            $table->integer('reminder_minutes_before')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            
            // Champs pour la synchronisation avec les calendriers externes
            $table->string('google_calendar_event_id')->nullable();
            $table->string('outlook_event_id')->nullable();
            $table->string('ical_uid')->nullable();
            
            // Statut de l'événement
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');
            
            // Emplacement et visibilité
            $table->string('location')->nullable();
            $table->boolean('is_private')->default(false);
            
            // Champs récurrents
            $table->string('recurrence_rule')->nullable();
            $table->timestamp('recurrence_until')->nullable();
            
            // Index pour les recherches
            $table->index(['company_id', 'start_date']);
            $table->index(['company_id', 'event_type_id']);
        });

        
        // Table de liaison pour les participants
        if (!Schema::hasTable('event_participants')) {
            Schema::create('event_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained()->onDelete('cascade');
                $table->morphs('participant'); // Peut être un employé, un département, etc.
                $table->string('email');
                $table->string('name');
                $table->enum('status', ['pending', 'accepted', 'declined', 'tentative'])->default('pending');
                $table->text('response_notes')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->string('calendar_event_id')->nullable(); // ID dans le calendrier externe
                $table->timestamps();
                
                $table->unique(
                    ['event_id', 'participant_type', 'participant_id'],
                    'idx_event_participant_unique'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne supprimez pas les tables si vous voulez conserver les données
        // Utilisez php artisan migrate:rollback --step=1 pour annuler la dernière migration
    }
};
