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
        Schema::create('familys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->string('nom')->nullable();
            $table->string('prenoms')->nullable();
            $table->string('cmu')->nullable();
            $table->string('num_cmu')->nullable();
            $table->date('date_naiss_membre')->nullable();
            $table->string('genre_membre')->nullable();
            $table->string('type_membre')->nullable();
            $table->string('document')->nullable();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('familys', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('familys');
    }
};
