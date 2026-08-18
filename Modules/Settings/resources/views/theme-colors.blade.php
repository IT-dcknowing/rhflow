@extends('layouts.app')

@section('title', 'Thème & Couleurs - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🎨 Thème & Couleurs</h4>
                    <p class="text-muted mb-0">Personnalisez l'apparence de votre interface</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.settings.config') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                    <button class="btn btn-primary" onclick="previewTheme()">
                        <i class="fas fa-eye me-1"></i>Aperçu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration des Couleurs -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🎨 Personnalisation des Couleurs</h5>
                    <span class="badge bg-label-info">Interface</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('company.settings.theme.update') }}">
                        @csrf

                        <!-- Couleur Principale -->
                        <div class="mb-4">
                            <label class="form-label">Couleur Principale</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" class="form-control form-control-color" name="primary_color"
                                       value="{{ $company->getThemePrimaryColor() }}" style="width: 60px; height: 40px;">
                                <input type="text" class="form-control" name="primary_color_text"
                                       value="{{ $company->getThemePrimaryColor() }}" style="flex: 1;"
                                       pattern="^#[0-9A-Fa-f]{6}$" placeholder="#696cff">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetPrimaryColor()">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                            <small class="text-muted">Utilisée pour les boutons principaux, liens et accents</small>
                        </div>

                        <!-- Couleur Secondaire -->
                        <div class="mb-4">
                            <label class="form-label">Couleur Secondaire</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" class="form-control form-control-color" name="secondary_color"
                                       value="{{ $company->getThemeSecondaryColor() }}" style="width: 60px; height: 40px;">
                                <input type="text" class="form-control" name="secondary_color_text"
                                       value="{{ $company->getThemeSecondaryColor() }}" style="flex: 1;"
                                       pattern="^#[0-9A-Fa-f]{6}$" placeholder="#03c3ec">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetSecondaryColor()">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                            <small class="text-muted">Utilisée pour les éléments d'accent et navigation</small>
                        </div>

                        <!-- Couleur de Fond Header -->
                        <div class="mb-4">
                            <label class="form-label">Couleur de Fond Header</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" class="form-control form-control-color" name="header_bg_color"
                                       value="{{ $company->getThemeHeaderBgColor() }}" style="width: 60px; height: 40px;">
                                <input type="text" class="form-control" name="header_bg_color_text"
                                       value="{{ $company->getThemeHeaderBgColor() }}" style="flex: 1;"
                                       pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetHeaderColor()">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                            <small class="text-muted">Couleur de fond de l'en-tête et barres de navigation</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Appliquer les Couleurs
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aperçu en Temps Réel -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">👁️ Aperçu en Temps Réel</h5>
                    <span class="badge bg-label-success">Live</span>
                </div>
                <div class="card-body">
                    <!-- Aperçu Header -->
                    <div class="mb-4">
                        <h6 class="mb-2">En-tête</h6>
                        <div class="preview-header" style="background-color: {{ $company->getThemeHeaderBgColor() }}; padding: 1rem; border-radius: 8px; color: {{ $company->getThemeHeaderBgColor() == '#ffffff' ? '#000000' : '#ffffff' }};">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2" style="background-color: {{ $company->getThemePrimaryColor() }}; color: white;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $company->name ?? 'Nom de l\'entreprise' }}</span>
                                </div>
                                <div class="d-flex gap-1">
                                    <div class="badge" style="background-color: {{ $company->getThemeSecondaryColor() }}; color: white;">Premium</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aperçu Boutons -->
                    <div class="mb-4">
                        <h6 class="mb-2">Boutons</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn" style="background-color: {{ $company->getThemePrimaryColor() }}; color: white; border: none;">Principal</button>
                            <button class="btn" style="background-color: {{ $company->getThemeSecondaryColor() }}; color: white; border: none;">Secondaire</button>
                            <button class="btn btn-outline-primary" style="border-color: {{ $company->getThemePrimaryColor() }}; color: {{ $company->getThemePrimaryColor() }};">Outline</button>
                        </div>
                    </div>

                    <!-- Aperçu Cartes -->
                    <div class="mb-4">
                        <h6 class="mb-2">Cartes</h6>
                        <div class="card border-0" style="box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="badge me-2" style="background-color: {{ $company->getThemePrimaryColor() }}; color: white;">Nouveau</div>
                                    <small class="text-muted">Aujourd'hui</small>
                                </div>
                                <h6 class="mb-0">Exemple de carte</h6>
                                <p class="text-muted small mb-0">Description avec accent</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informations -->
                    <div class="text-center">
                        <div class="mb-2">
                            <div class="badge bg-label-primary me-2" style="background-color: rgba(105, 110, 255, 0.1) !important; color: #253e87 !important;">
                                Primaire: {{ $company->getThemePrimaryColor() }}
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="badge bg-label-info me-2" style="background-color: rgba(3, 195, 236, 0.1) !important; color: #03c3ec !important;">
                                Secondaire: {{ $company->getThemeSecondaryColor() }}
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="badge bg-label-secondary" style="background-color: rgba(133, 146, 163, 0.1) !important; color: #8592a3 !important;">
                                Header: {{ $company->getThemeHeaderBgColor() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thèmes Prédéfinis -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">🎯 Thèmes Prédéfinis</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <!-- Thème Bleu -->
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="applyTheme('blue')">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="d-flex gap-1 me-2">
                                        <div style="width: 12px; height: 12px; background: #696cff; border-radius: 50%;"></div>
                                        <div style="width: 12px; height: 12px; background: #03c3ec; border-radius: 50%;"></div>
                                    </div>
                                    <small>Bleu</small>
                                </div>
                            </button>
                        </div>

                        <!-- Thème Vert -->
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-success w-100" onclick="applyTheme('green')">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="d-flex gap-1 me-2">
                                        <div style="width: 12px; height: 12px; background: #28c848; border-radius: 50%;"></div>
                                        <div style="width: 12px; height: 12px; background: #03c3ec; border-radius: 50%;"></div>
                                    </div>
                                    <small>Vert</small>
                                </div>
                            </button>
                        </div>

                        <!-- Thème Rouge -->
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="applyTheme('red')">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="d-flex gap-1 me-2">
                                        <div style="width: 12px; height: 12px; background: #ff4d4f; border-radius: 50%;"></div>
                                        <div style="width: 12px; height: 12px; background: #8592a3; border-radius: 50%;"></div>
                                    </div>
                                    <small>Rouge</small>
                                </div>
                            </button>
                        </div>

                        <!-- Thème Orange -->
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-warning w-100" onclick="applyTheme('orange')">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="d-flex gap-1 me-2">
                                        <div style="width: 12px; height: 12px; background: #ffcd07; border-radius: 50%;"></div>
                                        <div style="width: 12px; height: 12px; background: #8592a3; border-radius: 50%;"></div>
                                    </div>
                                    <small>Orange</small>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="resetAllColors()">
                            <i class="fas fa-undo me-1"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for color pickers */
.form-control-color {
    width: 60px !important;
    height: 40px !important;
    border-radius: 8px;
    border: 1px solid #d4d4d8;
    cursor: pointer;
    padding: 0;
}

.form-control-color::-webkit-color-swatch-wrapper {
    padding: 0;
    border-radius: 6px;
}

.form-control-color::-webkit-color-swatch {
    border-radius: 6px;
    border: none;
}

.preview-header {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.1);
}

.preview-header:hover {
    transform: scale(1.02);
}

.theme-preset {
    transition: all 0.3s ease;
}

.theme-preset:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mettre à jour l'aperçu en temps réel
    updatePreview();

    // Écouter les changements des inputs couleur
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Mettre à jour le champ texte correspondant
            const textInput = document.querySelector(`input[name="${this.name}_text"]`);
            if (textInput) {
                textInput.value = this.value;
            }
            updatePreview();
        });
    });

    // Écouter les changements des champs texte
    const textInputs = document.querySelectorAll('input[pattern]');
    textInputs.forEach(input => {
        input.addEventListener('input', function() {
            const colorInput = document.querySelector(`input[name="${this.name.replace('_text', '')}"]`);
            if (colorInput && /^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorInput.value = this.value;
                updatePreview();
            }
        });
    });
});

function updatePreview() {
    const primaryColor = document.querySelector('input[name="primary_color"]').value;
    const secondaryColor = document.querySelector('input[name="secondary_color"]').value;
    const headerColor = document.querySelector('input[name="header_bg_color"]').value;

    // Mettre à jour l'aperçu
    const previewButtons = document.querySelectorAll('.btn[style*="background-color"]');
    previewButtons.forEach((btn, index) => {
        if (index === 0) btn.style.backgroundColor = primaryColor;
        if (index === 1) btn.style.backgroundColor = secondaryColor;
    });

    // Mettre à jour les badges
    document.querySelectorAll('.badge[style*="background-color"]').forEach(badge => {
        if (badge.textContent.includes('Primaire')) {
            badge.style.backgroundColor = `${primaryColor}20`;
            badge.style.color = primaryColor;
        } else if (badge.textContent.includes('Secondaire')) {
            badge.style.backgroundColor = `${secondaryColor}20`;
            badge.style.color = secondaryColor;
        }
    });

    // Mettre à jour les bordures
    document.querySelectorAll('.btn-outline-primary').forEach(btn => {
        btn.style.borderColor = primaryColor;
        btn.style.color = primaryColor;
    });
}

function resetPrimaryColor() {
    document.querySelector('input[name="primary_color"]').value = '#696cff';
    document.querySelector('input[name="primary_color_text"]').value = '#696cff';
    updatePreview();
}

function resetSecondaryColor() {
    document.querySelector('input[name="secondary_color"]').value = '#03c3ec';
    document.querySelector('input[name="secondary_color_text"]').value = '#03c3ec';
    updatePreview();
}

function resetHeaderColor() {
    document.querySelector('input[name="header_bg_color"]').value = '#ffffff';
    document.querySelector('input[name="header_bg_color_text"]').value = '#ffffff';
    updatePreview();
}

function resetAllColors() {
    resetPrimaryColor();
    resetSecondaryColor();
    resetHeaderColor();
}

function applyTheme(theme) {
    const themes = {
        'blue': {
            primary: '#696cff',
            secondary: '#03c3ec',
            header: '#ffffff'
        },
        'green': {
            primary: '#28c848',
            secondary: '#03c3ec',
            header: '#ffffff'
        },
        'red': {
            primary: '#ff4d4f',
            secondary: '#8592a3',
            header: '#ffffff'
        },
        'orange': {
            primary: '#ffcd07',
            secondary: '#8592a3',
            header: '#ffffff'
        }
    };

    if (themes[theme]) {
        document.querySelector('input[name="primary_color"]').value = themes[theme].primary;
        document.querySelector('input[name="primary_color_text"]').value = themes[theme].primary;
        document.querySelector('input[name="secondary_color"]').value = themes[theme].secondary;
        document.querySelector('input[name="secondary_color_text"]').value = themes[theme].secondary;
        document.querySelector('input[name="header_bg_color"]').value = themes[theme].header;
        document.querySelector('input[name="header_bg_color_text"]').value = themes[theme].header;

        updatePreview();
    }
}

function previewTheme() {
    // Sauvegarder temporairement les couleurs actuelles
    const currentPrimary = document.querySelector('input[name="primary_color"]').value;
    const currentSecondary = document.querySelector('input[name="secondary_color"]').value;
    const currentHeader = document.querySelector('input[name="header_bg_color"]').value;

    // Appliquer les couleurs du formulaire
    document.documentElement.style.setProperty('--bs-primary', currentPrimary);
    document.documentElement.style.setProperty('--bs-primary-rgb', hexToRgb(currentPrimary).join(','));
    document.documentElement.style.setProperty('--bs-secondary', currentSecondary);

    // Notification
    alert('Aperçu appliqué ! Les couleurs sont maintenant visibles sur toute l\'interface.');

    // Remettre les couleurs originales après 10 secondes
    setTimeout(() => {
        document.documentElement.style.removeProperty('--bs-primary');
        document.documentElement.style.removeProperty('--bs-primary-rgb');
        document.documentElement.style.removeProperty('--bs-secondary');
    }, 10000);
}

function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? [
        parseInt(result[1], 16),
        parseInt(result[2], 16),
        parseInt(result[3], 16)
    ] : [105, 110, 255]; // default blue
}
</script>
@endsection
