@extends('layouts.super-admin')

@section('content')
<div class="row">
    <div class="col-12">      
        <!-- En-tête avec navigation -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-user me-2"></i>
                        Utilisateurs de {{ $enterprise->name }}
                    </h5>
                    <small class="text-muted">
                        Gérer les utilisateurs de l'entreprise
                        <span id="filteredCount"> • {{ $users->count() }} affichés</span>
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.users.create', $enterprise) }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Ajouter un utilisateur
                    </a>
                    <a href="{{ route('super-admin.enterprises.show', $enterprise) }}" class="btn bg-label-primary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour à l'entreprise
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Informations sur l'entreprise -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-dark text-white">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Entreprise :</strong> {{ $enterprise->name }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Email :</strong> {{ $enterprise->email }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Pack :</strong>
                                                @php
                                                    $planName = $company->companyPlan->name ?? '—';
                                                    $planColor = $planName == 'Gratuit' ? 'primary' : ($planName == 'Basic' ? 'info' : ($planName == 'Pro' ? 'success' : 'danger'));
                                                @endphp
                                                <span class="badge bg-{{ $planColor }}">{{ $planName }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Total utilisateurs :</strong> {{ $users->count()+1 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtres et recherche -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un utilisateur...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="typeFilter">
                                    <option value="">Tous les types</option>
                                    <option value="hr">RH</option>
                                    <option value="employee">Employé</option>
                                    <option value="company">Entreprise</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Tous les statuts</option>
                                    <option value="active">Actifs</option>
                                    <option value="inactive">Inactifs</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="dropdown">
                                    <button class="btn btn-outline-success w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-download me-2"></i>
                                        Exporter
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="exportToExcel()">
                                            <i class="ti ti-file-spreadsheet me-2"></i>Excel (CSV)
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="exportToPdf()">
                                            <i class="ti ti-file-text me-2"></i>PDF
                                        </a>
                                    </div>
                                </div>                                
                            </div>
                            <!-- Bouton de réinitialisation des filtres -->
                            <div class="col-md-2">
                                <button class="btn btn-outline-danger w-100" onclick="resetFilters()">
                                    <i class="ti ti-refresh me-1"></i>
                                    Réinitialiser les filtres
                                </button>
                            </div>
                        </div>

                        <!-- Tableau des utilisateurs -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Type</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Département</th>
                                        <th>Statut</th>
                                        <th>Dernière connexion</th>
                                        <th>Date création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    @if($user->avatar)
                                                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Avatar" class="rounded-circle">
                                                    @else
                                                        <span class="avatar-initial bg-primary rounded-circle">{{ substr($user->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <small class="text-muted">{{ $user->userName->username }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->type === 'hr')
                                                <span class="badge bg-success">RH</span>
                                            @elseif($user->type === 'employee')
                                                <span class="badge bg-info">Employé</span>
                                            @elseif($user->type === 'company')
                                                <span class="badge bg-primary">Entreprise</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($user->type) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($user->departments && $user->departments->count() > 0)
                                                @foreach($user->departments->take(2) as $dept)
                                                    <span class="badge bg-light text-dark">{{ $dept->name }}</span>
                                                @endforeach
                                                @if($user->departments->count() > 2)
                                                    <span class="badge bg-light text-dark">+{{ $user->departments->count() - 2 }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Aucun</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $user->last_login ? $user->last_login->diffForHumans() : 'Jamais' }}
                                        </td>
                                        <td>
                                            <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn btn-icon btn-sm btn-label-secondary" type="button" data-bs-toggle="dropdown">
                                                    <i class="ti ti-menu"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" style="position: fixed; z-index: 1050;">
                                                    <a class="dropdown-item bg-label-primary" href="{{ route('super-admin.users.show', $user->user_id ?? $user) }}">
                                                        <i class="ti ti-eye me-2"></i>
                                                        <span>Voir détails</span>dfsdfs
                                                    </a>
                                                    <a class="dropdown-item bg-label-primary" href="{{ route('super-admin.users.edit', $user->user_id ?? $user) }}">
                                                        <i class="ti ti-pencil-alt2 me-2"></i>
                                                        <span>Modifier</span>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    @if($user->is_active == true)
                                                    <a class="dropdown-item bg-label-warning" href="{{ route('super-admin.users.suspend', $user->user_id ?? $user) }}" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cette entreprise ?')">
                                                        <i class="ti ti-control-pause me-2"></i>
                                                        <span>Suspendre</span>
                                                    </a>
                                                    @else
                                                    <a class="dropdown-item bg-label-success" href="{{ route('super-admin.users.activate', $user->user_id ?? $user) }}" onclick="return confirm('Êtes-vous sûr de vouloir activer cette entreprise ?')">
                                                        <i class="ti ti-control-play me-2"></i>
                                                        <span>Activer</span>
                                                    </a>
                                                    @endif
                                                    <a class="dropdown-item bg-label-danger" href="{{ route('super-admin.users.delete', $user->user_id ?? $user) }}" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')">
                                                        <i class="ti ti-trash me-2"></i>
                                                        <span>Supprimer</span>
                                                    </a>  
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->links() }}
                        </div>
                        @endif
                    </div>
                </div>     
            </div> 
        </div>  
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    border-color: #e9ecef;
}

.badge {
    font-size: 0.75rem;
}

.avatar-initial {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 50%;
}

.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(38, 61, 136, 0.15);
    border: none;
}

/* Animation pour les notifications */
.fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@push('scripts-external')
<!-- Scripts externes pour l'export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
@endpush

@push('scripts')
<script>
// Variables globales pour les données
let usersData = @json($users->items());
let filteredUsers = [...usersData];

const routes = {
    show: "{{ route('super-admin.users.show', ':id') }}",
    edit: "{{ route('super-admin.users.edit', ':id') }}",
    suspend: "{{ route('super-admin.users.suspend', ':id') }}",
    activate: "{{ route('super-admin.users.activate', ':id') }}",
    delete: "{{ route('super-admin.users.delete', ':id') }}"
};

// Fonction de recherche et filtrage améliorée
function filterUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;

    filteredUsers = usersData.filter(user => {
        // Recherche textuelle
        const matchesSearch = searchTerm === '' ||
            user.name.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm) ||
            (user.username && user.username.toLowerCase().includes(searchTerm));

        // Filtre par type
        const matchesType = typeFilter === '' || user.type === typeFilter;

        // Filtre par statut
        const isActive = user.is_active ? 'active' : 'inactive';
        const matchesStatus = statusFilter === '' || isActive === statusFilter;

        return matchesSearch && matchesType && matchesStatus;
    });

    updateTable();
}

// Fonction de mise à jour du tableau
function updateTable() {
    const tbody = document.querySelector('#usersTable tbody');
    tbody.innerHTML = '';

    if (filteredUsers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="ti ti-user-off text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Aucun utilisateur trouvé</p>
                </td>
            </tr>
        `;
    } else {
        filteredUsers.forEach(user => {
            const row = createUserRow(user);
            tbody.appendChild(row);
        });
    }

    // Mettre à jour le compteur
    updateFilterCount();
}

// Fonction de mise à jour du compteur de filtres
function updateFilterCount() {
    const totalCount = usersData.length;
    const filteredCount = filteredUsers.length;
    const countElement = document.getElementById('filteredCount');

    if (countElement) {
        if (filteredCount === totalCount) {
            countElement.textContent = `• ${filteredCount} affichés`;
        } else {
            countElement.textContent = `• ${filteredCount} sur ${totalCount} affichés`;
        }
    }
}

// Fonction de création d'une ligne utilisateur
function createUserRow(user) {
    // Vérifie si la liste des utilisateurs est vide
    if (!user || user.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="9" class="text-center">Aucun utilisateur enregistré</td>`;
        return tr;
    }
    
    const tr = document.createElement('tr');

    const typeBadge = getTypeBadge(user.type);
    const statusBadge = user.is_active
        ? '<span class="badge bg-success">Actif</span>'
        : '<span class="badge bg-danger">Inactif</span>';

    const departments = user.departments && user.departments.length > 0
        ? user.departments.slice(0, 2).map(dept => `<span class="badge bg-light text-dark">${dept.name}</span>`).join('') +
        (user.departments.length > 2 ? `<span class="badge bg-light text-dark">+${user.departments.length - 2}</span>` : '')
        : '<span class="text-muted">Aucun</span>';
    
    // Remplace les placeholders :id dans les routes Laravel
    const showUrl = routes.show.replace(':id', user.user_id);
    const editUrl = routes.edit.replace(':id', user.user_id);
    const suspendUrl = routes.suspend.replace(':id', user.user_id);
    const activateUrl = routes.activate.replace(':id', user.user_id);
    const deleteUrl = routes.delete.replace(':id', user.user_id);

    tr.innerHTML = `
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar avatar-sm me-3">
                    ${user.avatar
                        ? `<img src="/storage/avatars/${user.avatar}" alt="Avatar" class="rounded-circle">`
                        : `<span class="avatar-initial bg-primary rounded-circle">${user.name.charAt(0)}</span>`
                    }
                </div>
                <div>
                    <h6 class="mb-0">${user.name}</h6>
                    <small class="text-muted">${user.username || ''}</small>
                </div>
            </div>
        </td>
        <td>${typeBadge}</td>
        <td>${user.email}</td>
        <td>${user.phone || 'N/A'}</td>
        <td>${departments}</td>
        <td>${statusBadge}</td>
        <td>${user.last_login ? formatDate(user.last_login) : 'Jamais'}</td>
        <td><small>${formatDate(user.created_at)}</small></td>
        <td align="center">
            <div class="dropdown">
                <button class="btn btn-icon btn-sm btn-label-secondary" type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-menu"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="position: fixed; z-index: 1050;">
                    <a class="dropdown-item bg-label-primary" href="${showUrl}">
                        <i class="ti ti-eye me-2"></i>
                        <span>Voir détails</span>
                    </a>
                    <a class="dropdown-item bg-label-primary" href="${editUrl}">
                        <i class="ti ti-pencil-alt2 me-2"></i>
                        <span>Modifier</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    ${user.is_active
                        ? `<a class="dropdown-item bg-label-warning" href="${suspendUrl}" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?')">
                            <i class="ti ti-control-pause me-2"></i>
                            <span>Suspendre</span>
                        </a>`
                        : `<a class="dropdown-item bg-label-success" href="${activateUrl}" onclick="return confirm('Êtes-vous sûr de vouloir activer cet utilisateur ?')">
                            <i class="ti ti-control-play me-2"></i>
                            <span>Activer</span>
                        </a>`
                    }
                    <a class="dropdown-item bg-label-danger" href="${deleteUrl}" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                        <i class="ti ti-trash me-2"></i>
                        <span>Supprimer</span>
                    </a>
                </div>
            </div>
        </td>
    `;
    return tr;
}

// Fonction pour obtenir le badge selon le type d'utilisateur
function getTypeBadge(type) {
    switch(type) {
        case 'hr':
            return '<span class="badge bg-success">RH</span>';
        case 'employee':
            return '<span class="badge bg-info">Employé</span>';
        case 'company':
            return '<span class="badge bg-primary">Entreprise</span>';
        default:
            return `<span class="badge bg-secondary">${type.charAt(0).toUpperCase() + type.slice(1)}</span>`;
    }
}

// Fonction de formatage des dates
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

// Fonction d'exportation améliorée avec vérification des dépendances
function exportUsers() {
    // Afficher le dropdown d'export au lieu de la confirmation
    return false; // Empêcher le comportement par défaut du lien
}

// Fonction d'exportation Excel avec vérification des dépendances
function exportToExcel() {
    if (typeof XLSX === 'undefined') {
        showNotification('Bibliothèque Excel non chargée. Veuillez réessayer.', 'danger');
        return;
    }

    try {
        const data = filteredUsers.map(user => ({
            'Nom': user.name,
            'Email': user.email,
            'Téléphone': user.phone || 'N/A',
            'Type': getTypeText(user.type),
            'Statut': user.is_active ? 'Actif' : 'Inactif',
            'Dernière connexion': user.last_login || 'Jamais',
            'Date de création': formatDate(user.created_at)
        }));

        // Créer un workbook Excel
        const ws = XLSX.utils.json_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Utilisateurs');

        // Ajuster la largeur des colonnes
        const colWidths = [
            { wch: 20 }, // Nom
            { wch: 25 }, // Email
            { wch: 15 }, // Téléphone
            { wch: 10 }, // Type
            { wch: 10 }, // Statut
            { wch: 15 }, // Dernière connexion
            { wch: 15 }  // Date de création
        ];
        ws['!cols'] = colWidths;

        // Télécharger le fichier
        const fileName = `utilisateurs_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);

        showNotification('Fichier Excel téléchargé avec succès!', 'success');
    } catch (error) {
        console.error('Erreur lors de l\'export Excel:', error);
        showNotification('Erreur lors de l\'export Excel: ' + error.message, 'danger');
    }
}

// Fonction d'exportation PDF avec vérification des dépendances
function exportToPdf() {
    if (typeof window.jspdf === 'undefined' || !window.jspdf.jsPDF) {
        showNotification('Bibliothèque PDF non chargée. Veuillez réessayer.', 'danger');
        return;
    }

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Titre
        doc.setFontSize(20);
        doc.setTextColor(41, 128, 185);
        doc.text('Liste des Utilisateurs', 20, 20);

        // Informations de l'entreprise
        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0);
        const entrepriseElement = document.querySelector('h6.card-title');
        const entrepriseNom = entrepriseElement ? entrepriseElement.textContent.replace('Utilisateurs de ', '') : 'Entreprise';
        doc.text(`Entreprise: ${entrepriseNom}`, 20, 35);
        doc.text(`Total: ${filteredUsers.length} utilisateurs`, 20, 45);
        doc.text(`Exporté le: ${new Date().toLocaleDateString('fr-FR')}`, 20, 55);

        // Préparation des données pour le tableau
        const tableData = filteredUsers.map((user, index) => [
            (index + 1).toString(),
            user.name,
            user.email,
            user.phone || 'N/A',
            getTypeText(user.type),
            user.is_active ? 'Actif' : 'Inactif'
        ]);

        // Ajouter le tableau
        doc.autoTable({
            head: [['N°', 'Nom', 'Email', 'Téléphone', 'Type', 'Statut']],
            body: tableData,
            startY: 70,
            styles: {
                fontSize: 8,
                cellPadding: 3,
            },
            headStyles: {
                fillColor: [41, 128, 185],
                textColor: 255,
                fontSize: 9,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            columnStyles: {
                0: { cellWidth: 15 },
                1: { cellWidth: 35 },
                2: { cellWidth: 45 },
                3: { cellWidth: 25 },
                4: { cellWidth: 20 },
                5: { cellWidth: 20 }
            }
        });

        // Pied de page
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(128, 128, 128);
            doc.text(`Page ${i} sur ${pageCount}`, doc.internal.pageSize.width - 30, doc.internal.pageSize.height - 10);
        }

        // Télécharger le fichier
        const fileName = `utilisateurs_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(fileName);

        showNotification('Fichier PDF téléchargé avec succès!', 'success');
    } catch (error) {
        console.error('Erreur lors de l\'export PDF:', error);
        showNotification('Erreur lors de l\'export PDF: ' + error.message, 'danger');
    }
}

// Fonction pour obtenir le texte du type
function getTypeText(type) {
    switch(type) {
        case 'hr': return 'RH';
        case 'employee': return 'Employé';
        case 'company': return 'Entreprise';
        default: return type.charAt(0).toUpperCase() + type.slice(1);
    }
}

// Fonction de notification
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed fade-in`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="ti ti-check me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Écouteurs d'événements améliorés
document.addEventListener('DOMContentLoaded', function() {
    // Attendre que les scripts externes soient chargés
    waitForLibraries().then(() => {
        // Écouteur pour la recherche
        document.getElementById('searchInput').addEventListener('input', filterUsers);

        // Écouteur pour le filtre de type
        document.getElementById('typeFilter').addEventListener('change', filterUsers);

        // Écouteur pour le filtre de statut
        document.getElementById('statusFilter').addEventListener('change', filterUsers);

        // Initialiser le tableau avec tous les utilisateurs
        updateTable();

        // Ajouter les fonctionnalités d'export à la fenêtre globale
        window.exportUsers = exportUsers;
        window.exportToExcel = exportToExcel;
        window.exportToPdf = exportToPdf;

        console.log('Fonctionnalités utilisateurs initialisées avec succès');
    }).catch((error) => {
        console.error('Erreur lors du chargement des bibliothèques:', error);
        showNotification('Erreur de chargement des fonctionnalités d\'export', 'danger');
    });
});

// Fonction pour attendre que les bibliothèques externes soient chargées
function waitForLibraries(maxAttempts = 50) {
    return new Promise((resolve, reject) => {
        let attempts = 0;

        function checkLibraries() {
            attempts++;

            // Vérifier si les bibliothèques principales sont disponibles
            if (typeof XLSX !== 'undefined' && typeof window.jspdf !== 'undefined') {
                resolve();
            } else if (attempts >= maxAttempts) {
                reject(new Error('Timeout: Les bibliothèques externes ne se sont pas chargées'));
            } else {
                setTimeout(checkLibraries, 100);
            }
        }

        checkLibraries();
    });
}

// Fonction de réinitialisation des filtres
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterUsers();
}

// Exposer la fonction de réinitialisation
window.resetFilters = resetFilters;
</script>
@endpush
