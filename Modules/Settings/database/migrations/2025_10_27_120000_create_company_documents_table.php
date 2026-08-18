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
        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // statuts, rccm, dfe, cnps, inspection, reglement_interieur
            $table->string('document_name'); // Nom du document
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // Chemin vers le fichier uploadé
            $table->string('file_name')->nullable(); // Nom original du fichier
            $table->string('file_size')->nullable(); // Taille du fichier
            $table->string('mime_type')->nullable(); // Type MIME
            $table->date('issue_date')->nullable(); // Date d'émission du document
            $table->date('expiry_date')->nullable(); // Date d'expiration
            $table->json('metadata')->nullable(); // Métadonnées additionnelles
            $table->boolean('is_required')->default(false); // Si le document est obligatoire
            $table->boolean('is_verified')->default(false); // Si le document est vérifié
            $table->date('verification_date')->nullable(); // Date de vérification
            $table->string('verified_by')->nullable(); // Utilisateur qui a vérifié
            $table->text('verification_notes')->nullable(); // Notes de vérification
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Relations
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            // Index pour les performances
            $table->index(['company_id', 'document_type']);
            $table->index(['company_id', 'is_active']);
            $table->index(['document_type', 'is_required']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_documents');
    }
};
