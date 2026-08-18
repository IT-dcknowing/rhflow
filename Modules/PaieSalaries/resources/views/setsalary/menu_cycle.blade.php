@php
    use Carbon\Carbon;
    // Définir la locale en français
    setlocale(LC_TIME, 'fr_FR.utf8');
    $date = Carbon::parse($monthpaie);
@endphp
<style>

    /* Couleurs chaudes pour chaque lien */
    .step--1 { background: #ff7f50; } /* Coral */
    .step--2 { background: #ff6347; } /* Tomato */
    .step--3 { background: #ff4500; } /* Orange Red */
    .step--4 { background: #ff8c00; } /* Dark Orange */
    .step--5 { background: #ffa500; } /* Orange */
    .step--6 { background: #c4047f; } /* Gold */
    .step--7 { background: #008e5c; } /* Light Salmon */
    .step--8 { background: #f08080; } /* Light Coral */

    .disabled {
        pointer-events: none;
        opacity: 0.6; /* ou un autre style pour indiquer que le lien est désactivé */
    }
</style>
<script src="http://thecodeplayer.com/uploads/js/prefixfree-1.0.7.js" type="text/javascript" type="text/javascript"></script>
<div class="col-sm-12">
    <div class="row" style="justify-content: start;">
        <div class="col-9" align="left">
            <a href="{{ route('setsalary.cycle_paie',['monthpaie' => $monthpaie]) }}" class="btn btn-sm btn-info" style="justify-content: end;"><i class="ti ti-arrow-left"></i> {{ __('Retour') }}</a>
        </div>
        <div class="col-3" align="right">
            <a href="{{ route('payslip.payroll') }}" class="btn btn-sm btn-warning" style="justify-content: end;">{{ __('Sélectionnez un autre mois de traitement') }} <i class="ti ti-arrow-right"></i></a>
        </div>
    </div>
    <p></p>
    <div class="col-12 mb-3">
        <div class="bs-stepper wizard-numbered mt-1" style="overflow-x: auto;">
            <div class="bs-stepper-header" style="flex-wrap: nowrap; min-width: max-content;">
                <a class="step {{ request()->routeIs('timesheet.home') ? 'active' : '' }}" href="{{ route('timesheet.home', ['monthpaie' => $monthpaie]) }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle step--1">1</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-subtitle">Présences<br>et Absences</span>
                        </span>
                    </button>
                </a>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <a class="step disabled {{ request()->routeIs('heuresup.home') ? 'active' : '' }}" href="{{ route('heuresup.home', ['monthpaie' => $monthpaie]) }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle step--2">2</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-subtitle">Heures <br>supps</span>
                        </span>
                    </button>
                </a>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <a class="step disabled {{ request()->routeIs('loan.suivi') ? 'active' : '' }}" href="{{ URL::to('loan/suivi', $monthpaie) }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle step--3">3</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-subtitle">Les<br>Prêts</span>
                        </span>
                    </button>
                </a>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <a class="step disabled {{ request()->routeIs('avantages.paie') ? 'active' : '' }}" href="{{ URL::to('avantages/paie', $monthpaie) }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle step--4">4</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-subtitle">Avantages <br>en nature</span>
                        </span>
                    </button>
                </a>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <a class="step disabled {{ request()->routeIs('leave.home') ? 'active' : '' }}" href="{{ route('leave.home', ['monthpaie' => $monthpaie]) }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle step--5">5</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-subtitle">Les <br>Congés</span>
                        </span>
                    </button>
                </a>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <a class="step disabled {{ request()->routeIs('termination.home') ? 'active' : '' }}" href="{{ route('termination.home', ['monthpaie' => $monthpaie]) }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle step--6">6</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-subtitle">Sanctions <br>et ruptures</span>
                        </span>
                    </button>
                </a>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <a class="step disabled {{ request()->routeIs('setsalary.index') ? 'active' : '' }}" href="{{ route('setsalary.index', ['monthpaie' => $monthpaie]) }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle step--7">7</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-subtitle">Données <br>de paie</span>
                        </span>
                    </button>
                </a>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <a class="step disabled{{ request()->routeIs('payslip.index') ? 'active' : '' }}" href="{{ route('payslip.index', ['monthpaie' => $monthpaie]) }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle step--8">8</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-subtitle">Etats <br>de paie</span>
                        </span>
                    </button>
                </a>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
            </div>
        </div>
    </div>
    <div class="navigation-buttons">
        <div class="row">
            <div class="col-md-6" align="left">
                <button id="prevButton" class="btn btn-sm btn-primary" disabled><i class="ti ti-arrow-left"></i> Précédent</button>
            </div>
            <div class="col-md-6" align="right">
                <button id="nextButton" class="btn btn-sm btn-primary">Suivant<i class="ti ti-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>
<p></p>
<script>
     document.addEventListener("DOMContentLoaded", function() {
        var steps = document.querySelectorAll('.step');
        var currentStep = localStorage.getItem('currentStep') ? parseInt(localStorage.getItem('currentStep')) : 0;

        var prevButton = document.getElementById('prevButton');
        var nextButton = document.getElementById('nextButton');

        var urls = [
            "{{ route('timesheet.home', ['monthpaie' => $monthpaie]) }}",
            "{{ route('heuresup.home', ['monthpaie' => $monthpaie]) }}",
            "{{ URL::to('loan/suivi', $monthpaie) }}",
            "{{ URL::to('avantages/paie', $monthpaie) }}",
            "{{ route('leave.home', ['monthpaie' => $monthpaie]) }}",
            "{{ route('termination.home', ['monthpaie' => $monthpaie]) }}",
            "{{ route('setsalary.index', ['monthpaie' => $monthpaie]) }}",
            "{{ route('payslip.index', ['monthpaie' => $monthpaie]) }}"
        ];

        function updateStep() {
            steps.forEach((step, index) => {
                if (index === currentStep) {
                    step.classList.remove('disabled');
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                    if (index > currentStep) {
                        step.classList.add('disabled');
                    } else {
                        step.classList.remove('disabled');
                    }
                }
            });

            prevButton.disabled = currentStep === 0;
            nextButton.disabled = currentStep === steps.length - 1;
        }

        prevButton.addEventListener('click', function() {
            if (currentStep > 0) {
                currentStep--;
                localStorage.setItem('currentStep', currentStep);
                updateStep();
                window.location.href = urls[currentStep];
            }
        });

        nextButton.addEventListener('click', function() {
            if (currentStep < steps.length - 1) {
                currentStep++;
                localStorage.setItem('currentStep', currentStep);
                updateStep();
                window.location.href = urls[currentStep];
            }
        });

        // Initial call to set the first step
        updateStep();
    });
</script>
