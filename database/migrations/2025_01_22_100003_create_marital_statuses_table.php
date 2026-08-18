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
        Schema::create('marital_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default marital statuses
        DB::table('marital_statuses')->insert([
            ['name' => 'Célibataire', 'code' => 'SINGLE', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marié(e)', 'code' => 'MARRIED', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Divorcé(e)', 'code' => 'DIVORCED', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Veuf(ve)', 'code' => 'WIDOWED', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marital_statuses');
    }
};
