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
        Schema::create('employee_cmus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->foreignId('family_id')->nullable()->constrained('familys')->cascadeOnDelete();
            $table->string('name_cmu')->nullable();
            $table->string('prenom_cmu')->nullable();
            $table->date('date_naiss_cmu')->nullable();
            $table->string('genre_cmu')->nullable();
            $table->string('num_cmu')->nullable();
            $table->string('type_cmu')->nullable();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('employee_cmus', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['family_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_cmus');
    }
};
