@php
    use Modules\PaieSalaries\Http\Controllers\PaieSalariesController;
    
    $controller = app(PaieSalariesController::class);
    $activeExercice = $controller->getActiveExercice();
    $activePeriode = $controller->getActivePeriode();
@endphp

<!-- Sélecteur de période -->
<li class="nav-item dropdown me-3">
    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="periodeSelectorDropdown" data-guide-id="period-selector" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-calendar-alt me-1"></i>
        @if($activePeriode)
            <span class="d-none d-md-inline">{{ $activePeriode->nom }}</span>
            <span class="d-md-none">Période</span>
        @elseif($activeExercice)
            <span class="d-none d-md-inline">{{ $activeExercice->nom }}</span>
            <span class="d-md-none">Exercice</span>
        @else
            <span>Sélectionner une période</span>
        @endif
    </button>
    
    <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="periodeSelectorDropdown" style="min-width: 350px;">
        <div class="mb-3">
            <h6 class="dropdown-header px-0 mb-2">
                <i class="fas fa-calendar-check me-2 text-primary"></i>Exercice
            </h6>
            <select class="form-select form-select-sm" id="exerciceSelector">
                <option value="">Chargement...</option>
            </select>
        </div>
        
        <div class="mb-2">
            <h6 class="dropdown-header px-0 mb-2">
                <i class="fas fa-calendar-day me-2 text-primary"></i>Période
            </h6>
            <select class="form-select form-select-sm" id="periodeSelector">
                <option value="">Sélectionnez d'abord un exercice</option>
            </select>
        </div>
        
        <div class="mt-3 pt-2 border-top">
            <small class="text-muted d-block mb-2">
                <i class="fas fa-info-circle me-1"></i>
                La sélection sera conservée pendant votre session
            </small>
        </div>
    </div>
</li>

<style>
    #periodeSelectorDropdown {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-color: #253e87;
        color: #253e87;
    }
    
    #periodeSelectorDropdown:hover {
        background-color: #253e87;
        color: white;
        border-color: #253e87;
    }
    
    .dropdown-menu {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
    }
    
    .dropdown-header {
        font-weight: 600;
        color: #566a7f;
        font-size: 0.875rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exerciceSelector = document.getElementById('exerciceSelector');
    const periodeSelector = document.getElementById('periodeSelector');
    const dropdownButton = document.getElementById('periodeSelectorDropdown');
    
    // Charger les exercices au chargement de la page
    loadExercices();
    
    // Événement de changement d'exercice
    exerciceSelector.addEventListener('change', function() {
        const exerciceId = this.value;
        if (exerciceId) {
            setActiveExercice(exerciceId);
            loadPeriodes(exerciceId);
        } else {
            periodeSelector.innerHTML = '<option value="">Sélectionnez d\'abord un exercice</option>';
        }
    });
    
    // Événement de changement de période
    periodeSelector.addEventListener('change', function() {
        const periodeId = this.value;
        if (periodeId) {
            setActivePeriode(periodeId);
        }
    });
    
    /**
     * Charge la liste des exercices
     */
    function loadExercices() {
        console.log('Chargement des exercices...');
        const url = '{{ url("company/paiesalaries/exercices-dropdown") }}';
        console.log('URL:', url);
        
        // Timeout de 10 secondes
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);
        
        fetch(url, {
            signal: controller.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                clearTimeout(timeoutId);
                console.log('Réponse statut:', response.status);
                
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status + ' ' + response.statusText);
                }
                
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    console.error('Type de contenu invalide:', contentType);
                    // Si on reçoit du HTML (probablement page de login), on redirige ou on affiche une erreur
                    if (contentType && contentType.includes("text/html")) {
                        throw new Error('La session a expiré ou une erreur serveur est survenue (HTML reçu)');
                    }
                    throw new Error('Réponse invalide (pas du JSON)');
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Données reçues:', data);
                if (data.success) {
                    exerciceSelector.innerHTML = '<option value="">Sélectionnez un exercice</option>';
                    
                    if (!data.exercices || data.exercices.length === 0) {
                        exerciceSelector.innerHTML = '<option value="">Aucun exercice trouvé</option>';
                    } else {
                        data.exercices.forEach(exercice => {
                            const option = document.createElement('option');
                            option.value = exercice.id;
                            option.textContent = `${exercice.nom} `;
                            
                            if (exercice.id === data.active_exercice_id) {
                                option.selected = true;
                            }
                            
                            exerciceSelector.appendChild(option);
                        });
                    }
                    
                    // Charger les périodes de l'exercice actif
                    if (data.active_exercice_id) {
                        loadPeriodes(data.active_exercice_id);
                    }
                } else {
                    console.error('Erreur API:', data.message);
                    exerciceSelector.innerHTML = '<option value="">Erreur : ' + (data.message || 'Inconnue') + '</option>';
                }
            })
            .catch(error => {
                if (error.name === 'AbortError') {
                    console.error('Délai d\'attente dépassé');
                    exerciceSelector.innerHTML = '<option value="">Erreur : Délai d\'attente dépassé</option>';
                } else {
                    console.error('Erreur lors du chargement des exercices:', error);
                    exerciceSelector.innerHTML = '<option value="">Erreur : ' + error.message + '</option>';
                }
            });
    }
    
    /**
     * Charge les périodes d'un exercice
     */
    function loadPeriodes(exerciceId) {
        periodeSelector.innerHTML = '<option value="">Chargement...</option>';
        const url = `{{ url('company/paiesalaries/periodes-dropdown') }}/${exerciceId}`;
        
        // Timeout de 10 secondes
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);
        
        fetch(url, {
            signal: controller.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                clearTimeout(timeoutId);
                
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                     if (contentType && contentType.includes("text/html")) {
                        throw new Error('HTML reçu au lieu de JSON');
                    }
                    throw new Error('Réponse invalide (pas du JSON)');
                }
                
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    periodeSelector.innerHTML = '<option value="">Sélectionnez une période</option>';
                    
                    if (!data.periodes || data.periodes.length === 0) {
                        periodeSelector.innerHTML = '<option value="">Aucune période trouvée</option>';
                    } else {
                        data.periodes.forEach(periode => {
                            const option = document.createElement('option');
                            option.value = periode.id;
                            option.textContent = `${periode.nom} `;
                            
                            if (periode.id === data.active_periode_id) {
                                option.selected = true;
                            }
                            
                            periodeSelector.appendChild(option);
                        });
                    }
                } else {
                     periodeSelector.innerHTML = '<option value="">Erreur : ' + (data.message || 'Inconnue') + '</option>';
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des périodes:', error);
                periodeSelector.innerHTML = '<option value="">Erreur : ' + error.message + '</option>';
            });
    }
    
    /**
     * Définit l'exercice actif
     */
    function setActiveExercice(exerciceId) {
        fetch('{{ url("company/paiesalaries/set-active-exercice") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ exercice_id: exerciceId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour le bouton du dropdown
                updateDropdownButton(data.exercice.nom);
                
                // Afficher une notification de succès
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message);
                }
                
                // Recharger la page après un court délai pour appliquer les changements
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la définition de l\'exercice actif:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Erreur lors de la sélection de l\'exercice');
            }
        });
    }
    
    /**
     * Définit la période active
     */
    function setActivePeriode(periodeId) {
        fetch('{{ url("company/paiesalaries/set-active-periode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ periode_id: periodeId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour le bouton du dropdown
                updateDropdownButton(data.periode.nom);
                
                // Afficher une notification de succès
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message);
                }
                
                // Recharger ou rediriger la page pour appliquer les changements
                setTimeout(() => {
                    const currentUrl = new URL(window.location.href);
                    let urlChanged = false;
                    
                    // 1. Mettre à jour le paramètre periode_id dans l'URL s'il existe
                    if (currentUrl.searchParams.has('periode_id')) {
                        currentUrl.searchParams.set('periode_id', periodeId);
                        urlChanged = true;
                    }
                    
                    // 2. Mettre à jour l'ID de la période dans le chemin de l'URL (ex: /periodes/10 -> /periodes/11)
                    const pathRegex = /\/periodes\/(\d+)/;
                    if (pathRegex.test(currentUrl.pathname)) {
                        currentUrl.pathname = currentUrl.pathname.replace(pathRegex, '/periodes/' + periodeId);
                        urlChanged = true;
                    }
                    
                    if (urlChanged) {
                        window.location.href = currentUrl.toString();
                    } else {
                        window.location.reload();
                    }
                }, 500);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la définition de la période active:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Erreur lors de la sélection de la période');
            }
        });
    }
    
    /**
     * Met à jour le texte du bouton dropdown
     */
    function updateDropdownButton(text) {
        const buttonText = dropdownButton.querySelector('.d-none.d-md-inline');
        if (buttonText) {
            buttonText.textContent = text;
        }
    }
});
</script>
