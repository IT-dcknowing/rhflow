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
        Schema::create('pay_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->integer('net_payble');
            $table->string('salary_month');
            $table->integer('status');
            $table->integer('salary_brut');
            $table->integer('net_imposable');
            $table->integer('net_sociale');
            $table->integer('basic_salary');
            $table->integer('total_retenue');
            $table->integer('total_patronale');
            $table->integer('Imp_brut');
            $table->integer('ricf');
            $table->integer('imp_net');
            $table->integer('cnps_sal');
            $table->integer('cnps_emp');
            $table->integer('cmu_sal');
            $table->integer('cmu_emp');
            $table->integer('ce_emp');
            $table->integer('ce_exp_emp');
            $table->integer('taxe_appr');
            $table->integer('taxe_fpc');
            $table->integer('acc_trav');
            $table->integer('pf_emp');
            $table->text('allowance');
            $table->text('commission');
            $table->integer('loan');
            $table->integer('saturation_deduction');
            $table->integer('other_payment');
            $table->integer('overtime');
            $table->integer('pay_leave');
            $table->integer('pay_right');
            $table->integer('avtg_real');
            $table->integer('avtg_real2');
            $table->integer('avtg_bareme');
            $table->integer('pay_type');
            $table->integer('nbre_jour');
            $table->string('address_emp');
            $table->string('situation_emp');
            $table->string('enfant_emp');
            $table->string('num_cnps_emp');
            $table->text('anciennete_emp');
            $table->text('categories_emp');
            $table->string('emploi');
            $table->string('phone_emp');
            $table->integer('parts_emp');
            $table->string('nom_etp');
            $table->string('adresse_etp');
            $table->string('phone_etp');
            $table->string('btp_etp');
            $table->integer('autre_retenue');
            $table->string('autre_retenue_type');
            $table->integer('code');
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
        Schema::dropIfExists('pay_slips');
    }
};
