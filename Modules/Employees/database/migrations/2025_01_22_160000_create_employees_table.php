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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality', 250)->nullable();
            $table->string('phone')->nullable();
            $table->integer('martalstatu_id')->nullable();
            $table->integer('enfant')->nullable();
            $table->integer('personneinf')->nullable();
            $table->string('parts', 5)->nullable();
            $table->integer('cmu')->nullable();
            $table->integer('contrat')->nullable();
            $table->string('charge_expat', 100)->nullable();
            $table->string('num_cnps', 50)->default('0')->nullable();
            $table->integer('num_secu_soc')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('categorie')->nullable();
            $table->date('end_leave')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('device_token', 255)->nullable();
            $table->string('platform', 255)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('password')->nullable();
            $table->string('employee_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->string('company_doj')->nullable();
            $table->string('documents')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_identifier_code')->nullable();
            $table->string('branch_location')->nullable();
            $table->string('orange_money')->nullable();
            $table->string('mtn_money')->nullable();
            $table->string('moov_money')->nullable();
            $table->string('wave_money')->nullable();
            $table->string('tax_payer_id')->nullable();
            $table->integer('salary_type')->nullable();
            $table->string('sous_categorie', 100)->nullable();
            $table->double('salary_horaire')->nullable();
            $table->double('salary')->default(0)->nullable();
            $table->integer('paytype')->nullable();
            $table->integer('id_secteur')->nullable();
            $table->integer('charge_its')->nullable();
            $table->integer('charge_cnps')->nullable();
            $table->integer('charge_cmu')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('secteur_id')->nullable();
            $table->string('statut_emp', 250)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index pour les performances
            $table->index(['company_id', 'is_active']);
            $table->index('employee_id');
            $table->index(['branch_id', 'department_id', 'designation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_months');
    }
};
