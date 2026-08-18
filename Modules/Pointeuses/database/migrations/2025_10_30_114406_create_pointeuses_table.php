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
        Schema::create('pointeuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->datetime('auth_date_time');
            $table->date('auth_date');
            $table->time('auth_time');
            $table->string('type')->nullable();
            $table->string('event_out')->nullable();
            $table->string('direction')->nullable();
            $table->string('name_device')->nullable();
            $table->string('no_device')->nullable();
            $table->integer('emp_id')->nullable();
            $table->string('name_emp')->nullable();
            $table->string('card_no')->nullable();
            $table->integer('location_id')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointeuses');
    }
};
