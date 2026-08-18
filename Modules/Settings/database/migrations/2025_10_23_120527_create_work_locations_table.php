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
        Schema::create('work_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('office'); // office, remote, client_site, warehouse, etc.
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Configuration pointeuse
            $table->boolean('has_time_clock')->default(false);
            $table->string('time_clock_ip')->nullable();
            $table->string('time_clock_mac')->nullable();
            $table->time('check_in_start')->default('08:00:00');
            $table->time('check_in_end')->default('09:30:00');
            $table->time('check_out_start')->default('17:00:00');
            $table->time('check_out_end')->default('18:30:00');
            $table->boolean('allow_remote_clock')->default(false);
            $table->integer('max_distance_meters')->default(100); // Pour géolocalisation

            // Relations
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index pour les performances
            $table->index(['company_id', 'is_active']);
            $table->index(['branch_id', 'is_active']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_locations');
    }
};
