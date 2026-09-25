@extends('layouts.app')

@section('title', 'Simulateur de paie - Interface améliorée')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête améliorée -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">

                            Simulateur de paie avancé
                        </h4>
                        <p class="text-muted mb-0">Calculez précisément le salaire brut à partir du net souhaité</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->locale('fr')->translatedFormat('l d F Y') }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ now()->format('H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-danger" id="resetBtn">
                            <i class="fas fa-undo me-1"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes informatives -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-info"></i>
                        <div>
                            <h6 class="mb-1">ℹ️ Informations importantes</h6>
                            <p class="mb-0 small">
                                • Créez au moins un employé avant d'utiliser le simulateur<br>
                                • Les calculs sont basés sur la réglementation fiscale en vigueur<br>
                                • Les résultats sont indicatifs et ne remplacent pas un calcul professionnel
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire principal -->
        <div class="row">
            <!-- Form de la paie -->
            <div id="from_paie" style="display:block;">
                <div class="card">
                    <div class="card-header d-flex justify-content-center border rounded mb-4">
                        <img src="{{ asset('img/illustrations/wizard-create-deal-girl-with-laptop-light.png') }}"
                            alt="wizard-create-deal" width="650px" class="img-fluid" />
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group mb-3 col-md-6">
                                <label for="empo_id" class="form-label">Selectionner l'employé *</label>
                                <select type="text" name="emp_id" class="form-select select2" id="id_emp">
                                    <option value="">{{ __('Selectionner l\'employé') }}</option>
                                    <option value="099">{{ __('Autre employé') }}</option>
                                    @if (!empty($employees))
                                        @foreach ($employees as $emp)
                                            <option value="{{$emp->id}}" data-salaire="{{ $emp->get_net_salary() }}"
                                                data-situation="{{$emp->martalstatu_id}}" data-enfant="{{$emp->enfant}}"
                                                data-personneinf="{{$emp->personneinf}}"
                                                data-parts="{{$emp->parts}}" data-cmu="{{$emp->cmu}}">
                                                {{ $emp->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('emp_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="net_pay" class="form-label">Salaire Net à Atteindre*</label>
                                <input type="number" name="net_pay" class="form-control" id="net_pay" required="required"
                                    placeholder="Net à Atteindre" />
                                @error('net_pay')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="situation_mat" class="form-label">Situation matrimoniale</label>
                                <select type="text" name="situation_mat" class="form-select" id="situation_mat"
                                    onchange="status()">
                                    @if (!empty($situations))
                                        @foreach ($situations as $status)
                                            <option value="{{ $status->id}}">
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('situation_mat')
                                    <span class="invalid-feedback" role="alert">
                                        <small>{{ $message }}</small>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="emp_enfts" class="form-label">Enfants à charge</label>
                                <select type="text" name="emp_enfts" class="form-select" id="emp_enfts" onchange="status()">
                                    @for ($i = 0; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="emp_personnes" class="form-label">Personnes infirmes à charge</label>
                                <select type="text" name="emp_personnes" class="form-select" id="emp_personnes"
                                    onchange="status()">
                                    @for ($i = 0; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="part_igr" class="form-label">Nombre de parts</label>
                                <input type="text" name="part_igr" class="form-control" id="part_igr"
                                    placeholder="Nombre de parts" readonly />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="cmu" class="form-label">Cotisation CMU</label>
                                <select type="text" name="cmu_brut" class="form-select" id="cmu_brut">
                                    @for ($i = 0; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label id="label_transport" class="form-label">Prime de transport légale</label>
                                <input type="number" id="tp_brut" class="form-control" name="tp_brut" required="" min="0"
                                    value="0" placeholder="Entrez la prime de transport">
                                <input type="number" id="tp_brut_libre" class="form-control d-none" name="tp_brut_libre"
                                    required="" min="0" placeholder="Entrez la prime de transport">
                            </div>

                            <div class="mb-3 col-md-4">
                                <label for="exo_fisc_100" class="form-label">Autres primes exonérées fiscales (100%)</label>
                                <input type="number" name="exo_fisc_100" class="form-control" id="exo_fisc_100"
                                    required="required" value="0" />
                                @error('exo_fisc_100')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-4">
                                <label for="exo_fisc_10" class="form-label">Autres primes exonérées fiscales (10%)</label>
                                <input type="number" name="exo_fisc_10" class="form-control" id="exo_fisc_10"
                                    required="required" value="0" />
                                @error('exo_fisc_10')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="exo_soci" class="form-label">Autres primes exonérées sociales</label>
                                <input type="number" name="exo_soci" class="form-control" id="exo_soci" required="required"
                                    value="0" />
                                @error('exo_soci')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" id="addSig" onclick="trouverBrutTotal()" disabled>
                                {{ __('Simuler') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Resultat de paie -->
            <div id="result_paie" style="display:none;">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="" style="background-color: #000;">
                                            <th colspan="4" class="text-center">
                                                <h5 class="text-white mb-0">{{ __('RESUME - SIMULATION DE PAIE') }}</h5>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="35%"><strong>Matricule :</strong></td>
                                            <td width="15%" class="text-end" style="background-color: #000;"><strong><span
                                                        id="numemp" class="text-warning"></span></strong></td>
                                            <td width="35%"><strong>CNPS</strong></td>
                                            <td width="15%" class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultatcnps" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre de parts : </strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultatpart" class="text-warning"></span></strong></td>
                                            <td><strong>Bénéficiaires CMU : </strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultatcmu" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Prime de transport :</strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultattp" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                            <td><strong>Total Retenues sociales :</strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="totalsocial" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Net à atteindre : </strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultatnet" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                            <td><strong>Autres primes exonérées fiscales :</strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="autresprimes" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Salaire Brut fiscal :</strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span id="totalsbi"
                                                        class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                            <td><strong>Salaire brut social :</strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span id="totalsbs"
                                                        class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Impôt Brut : </strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultatimp" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                            <td><strong>RICF : </strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultatricft" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Impôt Net :</strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultatnetimp" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                            <td><strong>Total retenues imposables: </strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="totalretenues" class="text-warning"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td width="85%"><strong>Brut Total :</strong></td>
                                            <td class="text-end" style="background-color: #000;"><strong><span
                                                        id="resultatbrut" class="text-danger"></span> <span
                                                        class="text-white">FCFA</span></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div>
                            <img src="{{ asset('img/illustrations/pencil-rocket.png') }}" alt="Pencil Rocket"
                                class="img-fluid" style="width: 100px; height: auto; position: relative; bottom: 0;"
                                align="right">
                        </div>
                        <button class="btn btn-primary" id="replay" onclick="affichefrom()">
                            {{ __('Résimuler') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function status() {
            var x = document.getElementById("situation_mat").value;
            var y = document.getElementById("emp_enfts").value;
            var z = document.getElementById("emp_personnes").value;
            //var i = 0.5;
            //const STATUT_MATRIMONIAL = x;
            let nombreDeParts = 0x1;

            if (x == '2' && y == '0') {
                nombreDeParts = 2;
            } else {
                if (y > 0) {
                    if (x == '1' || x == '3') {
                        nombreDeParts = 0x2 + Math.max(0x0, y - 0x1) * 0.5;
                    } else if (x == '2' || x == '4') {
                        nombreDeParts = 2.5 + Math.max(0x0, y - 0x1) * 0.5;
                    }
                }
            }
            nombreDeParts = nombreDeParts + parseFloat(z);
            document.getElementById("part_igr").value = Math.min(nombreDeParts, 0x5);
        }

        function Resultat_brut(brut_total) {
            var tp_brut = parseInt(document.getElementById("tp_brut").value);
            var input = parseInt(document.getElementById("tp_brut_libre").value);
            var exo_sco = parseInt(document.getElementById("exo_soci").value);
            var exo_fisc_10 = parseInt(document.getElementById("exo_fisc_10").value);
            var exo_fisc_100 = parseInt(document.getElementById("exo_fisc_100").value);
            var exo_ten = 0;
            var brut1 = 0;
            var brut_cnps1 = 0;
            var brut = 0;
            var brut_cnps = 0;
            var total_base = 0;
            var prime_exo = 0;
            total_base = brut_total - (exo_fisc_100 + tp_brut);
            exo_ten = (total_base * 10) / 100;

            if (exo_fisc_10 > exo_ten) {
                prime_exo = exo_ten;
            } else {
                prime_exo = exo_fisc_10;
            }

            if (tp_brut > 30000) {
                brut = brut_total + (tp_brut - 30000) - (prime_exo + exo_fisc_100)
                brut_cnps = brut_total + (tp_brut - 30000) - exo_sco;
            } else {
                brut = brut_total - (tp_brut + prime_exo + exo_fisc_100);
                brut_cnps = brut_total - (tp_brut + exo_sco);
            }


            var totoverstimes = 0; // Initialiser la variable pour les heures supplémentaires
            //calculons les charges des employés
            if (brut_cnps > 3375000) {
                brut_cnps = 3375000;
            }
            var cnps = 0;
            var cmu = document.getElementById("cmu_brut").value;
            var resultcnps = 0;
            var resultcmu = 0;

            if (cmu < 7) {
                coticmu = (cmu * 500);
                coticmuemp = (cmu * 500);
            } else {
                coticmu = 3000 + ((cmu - 6) * 1000);
                coticmuemp = 3000;
            }

            cnps = (brut_cnps * 6.3) / 100;

            tt_cnps = (brut_cnps * 6.3) / 100;
            resultcmu = Math.round(coticmu);
            resultcnps = Math.round(cnps);

            // Calculer le montant total de l'impôt sur le revenu imposable
            var resultimpricf = 0;
            if (brut >= 0 && brut <= 75000) {
                var tot = (brut * 0) / 100;
                resultimpricf = Math.round(tot);
            } else if (brut > 75000 && brut <= 240000) {
                var mt1 = brut - 75000;
                var tot1 = (((75000 * 0) / 100) + ((mt1 * 16) / 100));
                //r = (((brut*80)/100)-(its+total))*85/100;
                resultimpricf = Math.round(tot1);
            } else if (brut > 240000 && brut <= 800000) {
                var mt2 = brut - 240000;
                var tot2 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((mt2 * 21) / 100));
                //r = (((brut*80)/100)-(its+total1))*85/100;
                resultimpricf = Math.round(tot2);
            } else if (brut > 800000 && brut <= 2400000) {
                var mt3 = brut - 800000;
                var tot3 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((mt3 * 24) / 100));
                //r = (((brut*80)/100)-(its+total1))*85/100;
                resultimpricf = Math.round(tot3);
            } else if (brut > 2400000 && brut <= 8000000) {
                var mt4 = brut - 2400000;
                var tot4 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((mt4 * 28) / 100));
                //r = (((brut*80)/100)-(its+total1))*85/100;
                resultimpricf = Math.round(tot4);
            } else if (brut > 8000000) {
                var mt5 = brut - 8000000;
                var tot5 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((5600000 * 28) / 100) + ((mt5 * 32) / 100));
                //r = (((brut*80)/100)-(its+total2))*85/100;
                resultimpricf = Math.round(tot5);
            }
            //nombre de part en FCFA
            var nbre = document.getElementById("part_igr").value;
            // Fix: Table 2023 certifiée avec gestion robuste des virgules
            var rawNbre = document.getElementById("part_igr").value;
            var p = parseFloat((rawNbre || "").toString().replace(",", ".")) || 0;

            var fixedTable = {
                1: 0, 1.5: 5500, 2: 11000, 2.5: 16500,
                3: 22000, 3.5: 27500, 4: 33000, 4.5: 38500, 5: 44000
            };

            var resultricf = 0;
            for (var key in fixedTable) {
                if (Math.abs(p - parseFloat(key)) < 0.01) {
                    resultricf = fixedTable[key];
                    break;
                }
            }
            if (p > 5) {
                resultricf = 44000;
            }
            var retenue2 = (resultimpricf) - (resultricf);
            var retenue1 = (resultcnps) + (resultcmu);
            if (retenue2 > 0) {
                impots2 = (resultimpricf) - (resultricf) + (resultcnps) + (resultcmu);
            } else {
                retenue2 = 0;
                impots2 = (resultcnps) + (resultcmu);
            }

            var salaireNet = brut_total - impots2;
            var autrespr = 0;
            autrespr = Math.round(exo_fisc_10 + exo_fisc_100);
            /*if(exo_fisc === exo_sco){
                autrespr = exo_fisc;
            }else if(exo_fisc > exo_sco){
                autrespr = exo_fisc;
            }else if(exo_fisc < exo_sco){
                autrespr = exo_sco;
            }*/

            var resultatimp = document.getElementById("resultatimp");
            var resultatricft = document.getElementById("resultatricft");
            var resultatpart = document.getElementById("resultatpart");
            var resultatexo = document.getElementById("autresprimes");
            var resultatnetimp = document.getElementById("resultatnetimp");
            var resultatcnps = document.getElementById("resultatcnps");
            var totalsbi = document.getElementById("totalsbi");
            var totalsbs = document.getElementById("totalsbs");
            var totalretenues = document.getElementById("totalretenues");
            var totalcout = document.getElementById("totalcout");
            //var totalsocial = document.getElementById("totalsocial");
            var resultatcmu = document.getElementById("resultatcmu");
            resultatcmu.innerHTML = resultcmu.toLocaleString();
            resultatimp.innerHTML = resultimpricf.toLocaleString();
            resultatricft.innerHTML = resultricf.toLocaleString();
            resultatexo.innerHTML = autrespr.toLocaleString();
            resultatnetimp.innerHTML = retenue2.toLocaleString();
            resultatcnps.innerHTML = resultcnps.toLocaleString();
            totalretenues.innerHTML = impots2.toLocaleString();
            totalsocial.innerHTML = retenue1.toLocaleString();
            //totalcout.innerHTML  = impots2.toLocaleString()
            totalsbi.innerHTML = Math.round(brut).toLocaleString();
            totalsbs.innerHTML = Math.round(brut_cnps).toLocaleString();

            return salaireNet;
        }

        function trouverBrutTotal() {
            var salaireNetDesire = parseInt(document.getElementById("net_pay").value);
            var emp_id = document.getElementById("id_emp").value
            //alert(emp_id);
            if (salaireNetDesire < 75000) {
                alert("Veuillez entrer un montant supérieur ou égal à 75 000.");
                document.getElementById("net_pay").focus(); // Focus sur le champ net_pay
            } else {
                var brutTotal = salaireNetDesire * 2;
                var tp_brut = parseInt(document.getElementById("tp_brut").value);
                var input = parseInt(document.getElementById("tp_brut_libre").value);
                var tp = 0;
                var nbre = document.getElementById("part_igr").value;
                var cmu = document.getElementById("cmu_brut").value;
                var iterationsMax = 100000; // Limite le nombre d'itérations pour éviter une boucle infinie
                var iterations = 0;

                while (true) {
                    var salaireNet = Resultat_brut(brutTotal);
                    if (salaireNet === salaireNetDesire) {
                        break; // Sort de la boucle si le salaire net correspond au salaire net désiré
                    }

                    // Si le salaire net calculé est supérieur au salaire net désiré, on réduit le brut total de 100 ou de 1
                    if (salaireNet > salaireNetDesire) {
                        brutTotal -= 1000;
                    } else {
                        // Si le salaire net calculé est inférieur au salaire net désiré, on ajoute 1 au brut total
                        brutTotal += 1;
                    }

                    iterations++;
                    if (iterations >= iterationsMax) {
                        alert("Nombre maximal d'itérations atteint.");
                        break;
                    }
                }
                //alert (Resultat_brut(brutTotal));
                var resultatnet = document.getElementById("resultatnet");
                var resultattp = document.getElementById("resultattp");
                var numemp = document.getElementById("numemp");
                var resultatbrut = document.getElementById("resultatbrut");

                resultatnet.innerHTML = salaireNetDesire.toLocaleString();
                if (tp_brut > 30000) {
                    tp = (tp_brut - 30000);
                    resultattp.innerHTML = tp_brut.toLocaleString() + ' FCFA<br/> (Mt Imposable :' + tp.toLocaleString() + ' FCFA)';
                    resultatbrut.innerHTML = (brutTotal + tp_brut).toLocaleString();
                } else if (tp_brut === "30000") {
                    tp = 30000;
                    resultattp.innerHTML = tp.toLocaleString();
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                } else if (tp_brut === "24000") {
                    tp = 24000;
                    resultattp.innerHTML = tp.toLocaleString();
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                } else if (tp_brut === "22000") {
                    tp = 22000;
                    resultattp.innerHTML = tp.toLocaleString();
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                } else {
                    tp = tp_brut;
                    resultattp.innerHTML = tp.toLocaleString();
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                }
                resultatpart.innerHTML = nbre.toLocaleString();
                numemp.innerHTML = '#EMP00000' + emp_id;

            }

            document.getElementById("result_paie").style.display = 'block';
            document.getElementById("from_paie").style.display = 'none';
            document.getElementById("replay").disabled = false;
        }

        function afficherMontant() {
            var select = document.getElementById("tp_brut");
            var input = document.getElementById("tp_brut_libre");

            if (select.value === "Montant Libre") {
                input.hidden = false; // Affiche l'input
            } else {
                input.hidden = true; // Cache l'input
            }
        }

        function affichefrom() {
            document.getElementById("from_paie").style.display = 'block';
            document.getElementById("result_paie").style.display = 'none';
            document.getElementById("replay").disabled = true;
        }

        $(document).ready(function () {
            // Fonction pour réinitialiser le formulaire
            $('#resetBtn').on('click', function () {
                // Réinitialiser le select des employés
                $('#id_emp').val('').trigger('change');

                // Réinitialiser tous les champs input et select
                $('#net_pay').val('');
                $('#situation_mat').val('');
                $('#emp_enfts').val('0');
                $('#emp_personnes').val('0');
                $('#part_igr').val('');
                $('#cmu_brut').val('0');
                $('#tp_brut').val('0');
                $('#tp_brut_libre').val('0');
                $('#exo_fisc_100').val('0');
                $('#exo_fisc_10').val('0');
                $('#exo_soci').val('0');

                // Désactiver le bouton de simulation
                $('#addSig').prop('disabled', true);

                // Cacher les résultats si affichés
                $('#result_paie').hide();
                $('#from_paie').show();

                console.log('Formulaire réinitialisé');
            });

            // Gérer l'événement de changement
            $('.select2').on('change', function () {
                console.log('Select2 changed', $(this).val());

                var emp_id_element = $(this).val();
                var salaireNetDesire = document.getElementById("net_pay");
                var situation = document.getElementById("situation_mat");
                var enfant = document.getElementById("emp_enfts");
                var personne = document.getElementById("emp_personnes");
                var parts = document.getElementById("part_igr");
                var cmu = document.getElementById("cmu_brut");

                // Vérifie si l'élément existe et s'il y a une sélection
                if (emp_id_element) {
                    // Obtenir l'option sélectionnée
                    var selectedOption = $(this).find('option:selected');
                    var salairePropose = selectedOption.data('salaire'); // Utilisation de jQuery pour récupérer l'attribut de données
                    var emp_situation = selectedOption.data('situation');
                    var emp_enfant = selectedOption.data('enfant');
                    var emp_personne = selectedOption.data('personneinf');
                    var emp_parts = selectedOption.data('parts');
                    var emp_cmu = selectedOption.data('cmu');

                    console.log('Data attributes:', {
                        salaire: salairePropose,
                        situation: emp_situation,
                        enfant: emp_enfant,
                        personne: emp_personne,
                        parts: emp_parts,
                        cmu: emp_cmu
                    });

                    // Vérifie si le salaire est valide
                    if (salairePropose) {
                        salaireNetDesire.value = salairePropose;
                        situation.value = emp_situation;
                        enfant.value = emp_enfant;
                        parts.value = emp_parts;
                        personne.value = emp_personne;
                        cmu.value = emp_cmu;

                        console.log('Fields updated successfully');
                    }
                    document.getElementById("addSig").disabled = false; // Activer le bouton
                } else {
                    document.getElementById("addSig").disabled = true; // Désactiver le bouton si aucune sélection
                }
            });
        });
    </script>
@endpush