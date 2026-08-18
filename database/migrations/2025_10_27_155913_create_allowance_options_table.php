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
        Schema::create('allowance_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('param_fiscal')->nullable();
            $table->string('param_social')->nullable();
            $table->enum('type', ['default', 'created'])->default('default');
            table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_options');
    }
};
