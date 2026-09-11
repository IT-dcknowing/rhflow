@extends('layouts.super-admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-plus me-2"></i>
                        Créer un Nouveau Secteur
                    </h5>
                    <small class="text-muted">
                        Créer un nouveau secteur d'activité
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.sectors.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card"> 
                    <div class="card-body">
                        <form id="createSectorForm" method="POST" action="{{ route('super-admin.sectors.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Nom du secteur <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="name"
                                            name="name"
                                            value="{{ old('name') }}"
                                            required
                                            placeholder="Ex: Technologies de l'information">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                id="description"
                                                name="description"
                                                rows="4"
                                                placeholder="Décrivez brièvement ce secteur d'activité...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sort_order" class="form-label">Ordre d'affichage</label>
                                                <input type="number"
                                                    class="form-control @error('sort_order') is-invalid @enderror"
                                                    id="sort_order"
                                                    name="sort_order"
                                                    value="{{ old('sort_order', 0) }}"
                                                    min="0">
                                                @error('sort_order')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    Un nombre plus petit = affichage en premier
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        id="is_active"
                                                        name="is_active"
                                                        value="1"
                                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">
                                                        Secteur actif
                                                    </label>
                                                </div>
                                                <div class="form-text">
                                                    Les secteurs inactifs ne sont pas visibles par les utilisateurs
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border bg-dark text-white">
                                        <div class="card-body">
                                            <h6 class="section-title text-white">
                                                <i class="ti ti-info-alt me-2"></i>
                                                Informations
                                            </h6>

                                            <div class="mb-3">
                                                <strong>Statut:</strong>
                                                <span id="status-preview" class="badge bg-success ms-2">Actif</span>
                                            </div>

                                            <div class="mb-3">
                                                <strong>Ordre:</strong>
                                                <span id="order-preview" class="ms-2">0</span>
                                            </div>

                                            <div class="alert alert-info">
                                                <small>
                                                    <i class="ti ti-lightbulb me-1"></i>
                                                    <strong>Astuce:</strong> Utilisez un ordre d'affichage logique pour que les secteurs les plus importants apparaissent en premier.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between pt-3 border-top">
                                <a href="{{ route('super-admin.sectors.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x me-2"></i>
                                    Annuler
                                </a>

                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-outline-warning">
                                        <i class="ti ti-refresh me-2"></i>
                                        Réinitialiser
                                    </button>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-2"></i>
                                        Créer le secteur
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .section-title {
        color: #263d88;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .card.border {
        border: 1px solid #e9ecef !important;
    }

    .card-title {
        color: #495057;
        font-size: 1rem;
    }

    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aperçu du statut
    const activeCheckbox = document.getElementById('is_active');
    const statusPreview = document.getElementById('status-preview');
    const orderInput = document.getElementById('sort_order');
    const orderPreview = document.getElementById('order-preview');

    function updateStatusPreview() {
        if (activeCheckbox.checked) {
            statusPreview.className = 'badge bg-success ms-2';
            statusPreview.textContent = 'Actif';
        } else {
            statusPreview.className = 'badge bg-danger ms-2';
            statusPreview.textContent = 'Inactif';
        }
    }

    function updateOrderPreview() {
        orderPreview.textContent = orderInput.value || '0';
    }

    // Écouteurs d'événements
    activeCheckbox.addEventListener('change', updateStatusPreview);
    orderInput.addEventListener('input', updateOrderPreview);

    // Initialisation
    updateStatusPreview();
    updateOrderPreview();

    // Soumission du formulaire avec AJAX
    document.getElementById('createSectorForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Désactiver le bouton
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader me-2"></i>Création...';

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Succès - redirection vers la liste
                window.location.href = '{{ route("super-admin.sectors.index") }}';
            } else {
                // Erreur - réactiver le bouton et afficher les erreurs
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                if (data.errors) {
                    // Afficher les erreurs de validation
                    Object.keys(data.errors).forEach(field => {
                        const errorDiv = document.querySelector(`[name="${field}"]`).parentNode.querySelector('.invalid-feedback');
                        if (errorDiv) {
                            errorDiv.textContent = data.errors[field][0];
                            errorDiv.style.display = 'block';
                        }
                    });
                } else {
                    alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            alert('Erreur lors de la création du secteur');
        });
    });
});
</script>
@endpush
