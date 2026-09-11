@extends('layouts.app')

@section('title', 'Modifier Bulletin de Paie')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">✏️ Modifier Bulletin de Paie</h4>
                        <p class="text-muted mb-0">Modifiez les informations du bulletin de paie</p>
                        <small class="text-primary">
                            <i class="fas fa-user me-1"></i>{{ $paySlip->employee->name ?? 'Employé inconnu' }} •
                            <i
                                class="fas fa-calendar me-1"></i>{{ Carbon\Carbon::parse($paySlip->salary_month)->translatedFormat('F Y') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('company.declarations.resume.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                        <button class="btn btn-primary" onclick="saveBulletin()">
                            <i class="fas fa-save me-1"></i>Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form id="bulletinForm" method="POST" action="{{ route('company.declarations.resume.update', $paySlip->id) }}">
            @csrf
            @method('PUT')

            <!-- Informations Employé et Période -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">👤 Informations Employé</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom complet</label>
                                    <input type="text" class="form-control" value="{{ $paySlip->employee->name ?? '' }}"
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Poste</label>
                                    <input type="text" class="form-control" name="emploi"
                                        value="{{ $paySlip->emploi ?? '' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Numéro CNPS</label>
                                    <input type="text" class="form-control" name="num_cnps_emp"
                                        value="{{ $paySlip->num_cnps_emp ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" name="phone_emp"
                                        value="{{ $paySlip->phone_emp ?? '' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Situation familiale</label>
                                    <select class="form-select" name="situation_emp">
                                        <option value="Célibataire" {{ $paySlip->situation_emp == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                                        <option value="Marié(e)" {{ $paySlip->situation_emp == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                                        <option value="Divorcé(e)" {{ $paySlip->situation_emp == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                                        <option value="Veuf(ve)" {{ $paySlip->situation_emp == 'Veuf(ve)' ? 'selected' : '' }}>Veuf(ve)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre d'enfants</label>
                                    <input type="number" class="form-control" name="enfant_emp"
                                        value="{{ $paySlip->enfant_emp ?? 0 }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Adresse</label>
                                <textarea class="form-control" name="address_emp"
                                    rows="2">{{ $paySlip->address_emp ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">📅 Période et Type</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Période de paie</label>
                                    <input type="month" class="form-control" name="salary_month"
                                        value="{{ Carbon\Carbon::parse($paySlip->salary_month)->format('Y-m') }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre de jours</label>
                                    <input type="number" class="form-control" id="nbre_jour" name="nbre_jour"
                                        value="{{ $paySlip->nbre_jour ?? 30 }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Parts fiscales</label>
                                    <input type="number" class="form-control" id="parts_emp" name="parts_emp"
                                        value="{{ $paySlip->parts_emp ?? 1 }}" step="0.5" max="5">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ancienneté</label>
                                <input type="text" class="form-control" name="anciennete_emp"
                                    value="{{ $paySlip->anciennete_emp ?? '' }}" placeholder="ex: 2 ans 3 mois">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catégorie</label>
                                <input type="text" class="form-control" name="categories_emp"
                                    value="{{ $paySlip->categories_emp ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calcul Salaire -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">💰 Calcul du Salaire</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="Resultat_brut()">
                                <i class="fas fa-calculator me-1"></i>Recalculer
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary border-bottom">Les rubriques</h6>
                                    <!-- Salaire de base -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label"></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Salaire de base</span>
                                            <input type="number" class="form-control" name="basic_salary"
                                                value="{{ $paySlip->basic_salary }}" id="basicSalary" readonly>
                                        </div>
                                    </div>
                                    <!-- Les rubriques du brut -->
                                    @php
                                        $allowances = json_decode($paySlip->allowances);
                                    @endphp
                                    @foreach($allowances as $allowance)
                                        <div class="col-md-12 mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text">{{ $allowance->title }}</span>
                                                <input type="number" class="form-control" name="allowance"
                                                    value="{{ $allowance->amount }}" id="allowance">
                                                <input type="hidden" class="form-control" name="allowance_title"
                                                    value="{{ $allowance->title }}" id="allowance_title">
                                                <input type="hidden" class="form-control" name="allowance_fisc"
                                                    value="{{ $allowance->trait_fisc }}" id="allowance_fisc">
                                                <input type="hidden" class="form-control" name="allowance_cnps"
                                                    value="{{ $allowance->trait_cnps }}" id="allowance_cnps">
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($paySlip->avtg_real > 0)
                                        <!-- Avantages -->
                                        <div class="col-md-12 mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text">Avantages en nature</span>
                                                <input type="number" class="form-control" name="avtg_real"
                                                    value="{{ $paySlip->avtg_real }}" id="avtgReal">
                                            </div>
                                        </div>
                                    @endif
                                    <hr>
                                    <!-- Salaire brut -->
                                    <div class="col-md-12 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">Total brut</span>
                                            <input type="number" class="form-control" name="salary_brut"
                                                value="{{ $paySlip->salary_brut }}" id="salaryBrut">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-primary border-bottom">🏥 Les retenues</h6>
                                    <!-- Net imposable -->
                                    <div class="col-md-12 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">Net imposable</span>
                                            <input type="number" class="form-control" name="net_imposable"
                                                value="{{ $paySlip->net_imposable }}" id="netImposable">
                                        </div>
                                    </div>

                                    <!-- Net social -->
                                    <div class="col-md-12 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">Net social</span>
                                            <input type="number" class="form-control" name="net_sociale"
                                                value="{{ $paySlip->net_sociale }}" id="netSociale">
                                        </div>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Impôts bruts avant RICF
                                        </strong><strong class="text-danger text-end" id="imp_ricf">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Réduction pour Charges de
                                            Famille</strong><strong class="text-danger text-end" id="ricf">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Impôts Nets</strong> <strong
                                            class="text-danger text-end" id="imp_net">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Cotisation Retraite CNPS
                                        </strong><strong class="text-danger text-end" id="cnps_sal">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Couverture Maladie
                                            Universelle</strong> <strong class="text-danger text-end"
                                            id="cmu_sal">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Contribution Employeur
                                        </strong><strong class="text-danger text-end" id="ce_empl">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Contribution employeur (Expatrié)
                                        </strong><strong class="text-danger text-end" id="ce_expa">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Taxe d’Apprentissage
                                        </strong><strong class="text-danger text-end" id="taxe_app">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Taxe.F.P.C </strong><strong
                                            class="text-danger text-end" id="taxe_pf">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Accident de travail </strong><strong
                                            class="text-danger text-end" id="accident">0</strong>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mb-0">
                                        <strong class="text-primary border-bottom mb-3">Prestation Familiale
                                        </strong><strong class="text-danger text-end" id="pf_fam">0</strong>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-primary border-bottom">💰 Autres retenues</h6>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <!-- Total retenues -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Total retenues</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="total_retenue"
                                            value="{{ $paySlip->total_retenue }}" id="totalRetenue" readonly>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>

                                <!-- Total patronal -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Total patronal</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="total_patronale"
                                            value="{{ $paySlip->total_patronale }}" id="totalPatronale">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>

                                <!-- Net à payer -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-success">Net à payer</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control fw-bold text-success" name="net_payble"
                                            value="{{ $paySlip->net_payble }}" id="netPayble" readonly>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('company.declarations.resume.index') }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Annuler
                                </a>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-info" onclick="previewBulletin()">
                                        <i class="fas fa-eye me-1"></i>Aperçu
                                    </button>
                                    <button type="button" class="btn btn-outline-success" onclick="saveAndValidate()">
                                        <i class="fas fa-check me-1"></i>Enregistrer & Valider
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Enregistrer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Calcul automatique des totaux
            calculateTotals();

            // Écouteurs d'événements pour les champs de calcul
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('input', calculateTotals);
            });
        });

        function calculateTotals() {
            // Calcul du total des retenues salariales
            let totalRetenueSalarie = 0;
            document.querySelectorAll('.retention-salarie').forEach(input => {
                totalRetenueSalarie += parseFloat(input.value) || 0;
            });

            // Calcul du total des retenues fiscales
            let totalRetenueFiscale = 0;
            document.querySelectorAll('.retention-fiscale').forEach(input => {
                totalRetenueFiscale += parseFloat(input.value) || 0;
            });

            // Calcul des autres déductions
            let totalDeductions = 0;
            document.querySelectorAll('input[data-type="deduction"]').forEach(input => {
                totalDeductions += parseFloat(input.value) || 0;
            });

            // Total des retenues
            const totalRetenue = totalRetenueSalarie + totalRetenueFiscale + totalDeductions;
            document.getElementById('totalRetenue').value = Math.round(totalRetenue);

            // Calcul du total patronal
            let totalPatronale = 0;
            document.querySelectorAll('.retention-patronale').forEach(input => {
                totalPatronale += parseFloat(input.value) || 0;
            });
            document.getElementById('totalPatronale').value = Math.round(totalPatronale);

            // Calcul du net à payer
            const salaryBrut = parseFloat(document.getElementById('salaryBrut').value) || 0;
            const netPayble = salaryBrut - totalRetenue;
            document.getElementById('netPayble').value = Math.round(netPayble);
        }

        function Resultat_brut() {
            // Récupérer le salaire de base
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;

            // Récupérer les allocations (rubriques du brut)
            let allowances = 0;
            let exo_fisc_100 = 0;  // Exonération fiscale 100%
            let exo_fisc_10 = 0;   // Exonération fiscale 10%
            let exo_soci = 0;      // Exonération sociale

            document.querySelectorAll('input[name="allowance"]').forEach((input, index) => {
                const amount = parseFloat(input.value) || 0;
                const title = document.querySelectorAll('input[name="allowance_title"]')[index]?.value || '';
                const fisc = document.querySelectorAll('input[name="allowance_fisc"]')[index]?.value || '';
                const cnps = document.querySelectorAll('input[name="allowance_cnps"]')[index]?.value || '';

                allowances += amount;

                if (fisc === 'exo 100%') {
                    exo_fisc_100 += amount;
                }
                if (fisc === 'exo 10%') {
                    exo_fisc_10 += amount;
                }
                if (cnps === 'exo 100%') {
                    exo_soci += amount;
                }
            });

            // Récupérer les avantages en nature
            const avtgReal = parseFloat(document.getElementById('avtgReal')?.value) || 0;

            // Calcul du brut total
            const brut_total = basicSalary + allowances + avtgReal;

            // Transport brut (plafond 30000)
            let tp_brut = 0;
            document.querySelectorAll('input[name="allowance"]').forEach((input, index) => {
                const title = document.querySelectorAll('input[name="allowance_title"]')[index]?.value || '';
                if (title.toLowerCase().includes('transport')) {
                    tp_brut = parseFloat(input.value) || 0;
                }
            });

            // Calcul du brut imposable et brut CNPS
            let brut = brut_total;
            let brut_cnps = brut_total;

            // Application des exonérations
            const total_base = brut_total - (exo_fisc_100 + tp_brut);
            const exo_ten = (total_base * 10) / 100;

            const prime_exo = exo_fisc_10 > exo_ten ? exo_ten : exo_fisc_10;

            if (tp_brut > 30000) {
                brut = brut_total + (tp_brut - 30000) - (prime_exo + exo_fisc_100);
                brut_cnps = brut_total + (tp_brut - 30000) - exo_soci;
            } else {
                brut = brut_total - (tp_brut + prime_exo + exo_fisc_100);
                brut_cnps = brut_total - (tp_brut + exo_soci);
            }

            // Plafonner le brut CNPS
            if (brut_cnps > 3375000) {
                brut_cnps = 3375000;
            }

            // Calcul des cotisations sociales
            const cnps = Math.round((brut_cnps * 6.3) / 100);
            const parts_emp = parseFloat(document.getElementById('parts_emp').value) || 1;
            const cmu_personnes = Math.floor(brut_cnps / 50000); // Approximation pour CMU

            let coticmu = 0;
            let coticmuemp = 0;

            if (cmu_personnes < 7) {
                coticmu = cmu_personnes * 500;
                coticmuemp = cmu_personnes * 500;
            } else {
                coticmu = 3000 + ((cmu_personnes - 6) * 1000);
                coticmuemp = 3000;
            }

            // --- CALCUL LEGAL DE L'IMPOT (ALIGNÉ SUR LE MOTEUR CENTRAL) ---
            // 1. Détermination de la base imposable réelle : (SBI - CNPS - CMU) * 80%
            let base_imposable_reelle = (brut - cnps - coticmu) * 0.8;
            if (base_imposable_reelle < 0) base_imposable_reelle = 0;

            // 2. Gestion de la proratisation selon les jours travaillés
            const nbre_jour_input = parseInt(document.getElementById('nbre_jour')?.value) || 30;
            const nbre_jours = (nbre_jour_input == 28 || nbre_jour_input == 29 || nbre_jour_input == 31 || nbre_jour_input == 0) ? 30 : nbre_jour_input;

            // Mise à l'échelle mensuelle 30j pour application du barème
            const base_mensuelle = (nbre_jours >= 30) ? base_imposable_reelle : (base_imposable_reelle * 30 / nbre_jours);

            // 3. Application du Barème Unique Réforme 2024 sur la base mensuelle
            let irs_mensuel = 0;
            if (base_mensuelle <= 75000) {
                irs_mensuel = 0;
            } else if (base_mensuelle <= 240000) {
                irs_mensuel = Math.round((base_mensuelle - 75000) * 0.16);
            } else if (base_mensuelle <= 800000) {
                irs_mensuel = Math.round((165000 * 0.16) + ((base_mensuelle - 240000) * 0.21));
            } else if (base_mensuelle <= 2400000) {
                irs_mensuel = Math.round((165000 * 0.16) + (560000 * 0.21) + ((base_mensuelle - 800000) * 0.24));
            } else if (base_mensuelle <= 8000000) {
                irs_mensuel = Math.round((165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + ((base_mensuelle - 2400000) * 0.28));
            } else {
                irs_mensuel = Math.round((165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (5600000 * 0.28) + ((base_mensuelle - 8000000) * 0.32));
            }

            // 4. Rétablissement de l'impôt brut au prorata réel
            let resultimpricf = Math.round((nbre_jours >= 30) ? irs_mensuel : (irs_mensuel * nbre_jours / 30));

            // Application des réductions fixes pour charges de famille (Barème 2023)
            const fixedTable = {
                1: 0,
                1.5: 5500,
                2: 11000,
                2.5: 16500,
                3: 22000,
                3.5: 27500,
                4: 33000,
                4.5: 38500,
                5: 44000
            };
            const partsFloat = parseFloat((parts_emp || "").toString().replace(",", ".")) || 0;
            let baseReduction = 0;
            for (let key in fixedTable) {
                if (Math.abs(partsFloat - parseFloat(key)) < 0.01) {
                    baseReduction = fixedTable[key];
                    break;
                }
            }
            if (partsFloat > 5) {
                baseReduction = 44000;
            }
            
            let resultricf = 0;
            if (nbre_jours < 30 && nbre_jours > 0) {
                resultricf = Math.round((baseReduction / 30) * nbre_jours);
            } else {
                resultricf = baseReduction;
            }

            // Calcul des retenues totales
            const retenue2 = Math.max(0, resultimpricf - resultricf);
            const retenue1 = cnps + coticmu;
            const impots2 = retenue2 + retenue1;
            const salaireNet = brut_total - impots2;

            // Mettre à jour les champs du formulaire
            document.getElementById('salaryBrut').value = Math.round(brut);
            document.getElementById('netImposable').value = Math.round(brut);
            document.getElementById('netSociale').value = Math.round(brut_cnps);

            // Afficher les résultats dans le div
            document.getElementById('cnps_sal').textContent = cnps.toLocaleString('fr-FR');
            document.getElementById('cmu_sal').textContent = coticmu.toLocaleString('fr-FR');
            document.getElementById('imp_ricf').textContent = resultimpricf.toLocaleString('fr-FR');
            document.getElementById('ricf').textContent = resultricf.toLocaleString('fr-FR');
            document.getElementById('imp_net').textContent = retenue2.toLocaleString('fr-FR');

            // Calcul des cotisations patronales
            const cnpsEmp = Math.round((brut_cnps * 7.7) / 100);
            const pf = Math.round((brut_cnps * 5.75) / 100);
            const taxeApp = Math.round((brut_cnps * 1.2) / 100);
            const taxePF = Math.round((brut_cnps * 1.6) / 100);
            const accident = Math.round(75000 * 0.03);
            const ceEmp = Math.round(brut_cnps * 0.06);
            const ceExpa = Math.round(brut_cnps * 0.09);

            document.getElementById('pf_fam').textContent = pf.toLocaleString('fr-FR');
            document.getElementById('taxe_app').textContent = taxeApp.toLocaleString('fr-FR');
            document.getElementById('taxe_pf').textContent = taxePF.toLocaleString('fr-FR');
            document.getElementById('accident').textContent = accident.toLocaleString('fr-FR');
            document.getElementById('ce_empl').textContent = ceEmp.toLocaleString('fr-FR');
            document.getElementById('ce_expa').textContent = ceExpa.toLocaleString('fr-FR');

            // Mettre à jour les totaux
            document.getElementById('totalRetenue').value = Math.round(impots2);
            document.getElementById('totalPatronale').value = Math.round(cnpsEmp + pf + taxeApp + taxePF + accident + ceEmp);
            document.getElementById('netPayble').value = Math.round(salaireNet);

            // Recalculer les totaux
            calculateTotals();

            // Afficher une notification
            showNotification('Salaire recalculé avec succès', 'success');

            return salaireNet;
        }

        function saveBulletin() {
            document.getElementById('bulletinForm').submit();
        }

        function saveAndValidate() {
            // Mettre le statut à "Validé"
            document.querySelector('select[name="status"]').value = 1;
            saveBulletin();
        }

        function previewBulletin() {
            const form = document.getElementById('bulletinForm');
            const formData = new FormData(form);

            // Ouvrir un nouvel onglet avec l'aperçu
            const url = `/declarations/resume/{{ $paySlip->id }}/preview?` + new URLSearchParams(formData);
            window.open(url, '_blank');
        }

        function showNotification(message, type = 'info') {
            // Créer une notification temporaire
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            // Auto-suppression après 3 secondes
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Validation du formulaire
        document.getElementById('bulletinForm').addEventListener('submit', function (e) {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value);
            const netPayble = parseFloat(document.getElementById('netPayble').value);

            if (basicSalary < 0 || netPayble < 0) {
                e.preventDefault();
                showNotification('Les montants ne peuvent pas être négatifs', 'danger');
                return;
            }

            if (netPayble > basicSalary * 2) {
                if (!confirm('Le net à payer semble très élevé par rapport au salaire de base. Continuer quand même ?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endpush