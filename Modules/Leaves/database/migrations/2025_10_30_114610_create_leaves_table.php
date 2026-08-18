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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->nullable()->constrained('leave_types')->cascadeOnDelete();
            $table->date('applied_on');   
            $table->date('leave_back');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('total_leave_days');
            $table->integer('amount_leave')->nullable();
            $table->integer('amount_leave_net')->nullable();
            $table->integer('leave_sit')->nullable();
            $table->string('leave_reason')->nullable();
            $table->text('month_leave')->nullable();
            $table->text('sb_leave')->nullable();
            $table->integer('days_leave')->nullable();
            $table->string('remark')->nullable();
            $table->enum('status', ['Pending', 'Approuvé', 'Rejeté','Terminé'])->default('Pending');
            $table->integer('leave_statut')->nullable();
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
        Schema::dropIfExists('leaves');
    }
};
