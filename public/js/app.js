/**
 * RH Flow - JavaScript Compilé pour Production
 * Fonctionnalités principales de l'application
 */

// Initialisation de base
document.addEventListener('DOMContentLoaded', function() {
    console.log('RH Flow - Application chargée');

    // Initialisation des tooltips Bootstrap
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Gestion du thème sombre/clair
    initializeThemeManager();

    // Gestion des formulaires de recherche
    initializeSearchForms();

    // Gestion des tableaux avec filtres
    initializeDataTables();

    // Gestion des modales
    initializeModals();
});

/**
 * Gestion du système de thèmes
 */
function initializeThemeManager() {
    const htmlElement = document.documentElement;
    const currentTheme = localStorage.getItem('rh-theme') || 'default';

    htmlElement.setAttribute('data-theme', currentTheme);

    // Écouter les changements de thème système
    if (window.matchMedia) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        mediaQuery.addEventListener('change', function(e) {
            if (!localStorage.getItem('rh-theme')) {
                htmlElement.setAttribute('data-theme', e.matches ? 'semi-dark' : 'default');
            }
        });
    }
}

/**
 * Changer le thème dynamiquement
 */
function setTheme(themeName) {
    const htmlElement = document.documentElement;
    htmlElement.setAttribute('data-theme', themeName);
    localStorage.setItem('rh-theme', themeName);
}

/**
 * Gestion des formulaires de recherche
 */
function initializeSearchForms() {
    const searchInputs = document.querySelectorAll('input[type="search"], input[placeholder*="rechercher"], input[placeholder*="chercher"]');

    searchInputs.forEach(function(input) {
        input.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const table = e.target.closest('table') || e.target.closest('.table-responsive');

            if (table) {
                filterTableRows(table, searchTerm);
            }
        });
    });
}

/**
 * Filtrer les lignes d'un tableau
 */
function filterTableRows(table, searchTerm) {
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(function(row) {
        const text = row.textContent.toLowerCase();
        const shouldShow = text.includes(searchTerm);

        row.style.display = shouldShow ? '' : 'none';
    });
}

/**
 * Gestion des tableaux avec filtres
 */
function initializeDataTables() {
    const tables = document.querySelectorAll('.table');

    tables.forEach(function(table) {
        // Ajouter les classes de tri si nécessaire
        const headers = table.querySelectorAll('th');
        headers.forEach(function(header, index) {
            if (header.textContent.trim()) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function() {
                    sortTable(table, index);
                });
            }
        });
    });
}

/**
 * Trier un tableau par colonne
 */
function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.rows);

    rows.sort(function(a, b) {
        const aText = a.cells[columnIndex].textContent.trim();
        const bText = b.cells[columnIndex].textContent.trim();

        return aText.localeCompare(bText);
    });

    // Remettre les lignes triées dans le tableau
    rows.forEach(function(row) {
        tbody.appendChild(row);
    });
}

/**
 * Gestion des modales
 */
function initializeModals() {
    const modals = document.querySelectorAll('.modal');

    modals.forEach(function(modal) {
        modal.addEventListener('show.bs.modal', function() {
            // Focus sur le premier champ du formulaire si présent
            const form = modal.querySelector('form');
            if (form) {
                const firstInput = form.querySelector('input:not([type="hidden"])');
                if (firstInput) {
                    setTimeout(function() {
                        firstInput.focus();
                    }, 100);
                }
            }
        });
    });
}

/**
 * Afficher une alerte temporaire
 */
function showAlert(message, type = 'info', duration = 5000) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="ti ti-info-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    // Insérer l'alerte au début du body ou dans un container spécifique
    const container = document.querySelector('.main-content') || document.body;
    const existingAlert = container.querySelector('.alert');

    if (existingAlert) {
        existingAlert.remove();
    }

    container.insertAdjacentHTML('afterbegin', alertHtml);

    // Auto-dismiss après la durée spécifiée
    if (duration > 0) {
        setTimeout(function() {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, duration);
    }
}

/**
 * Formater une date en français
 */
function formatDate(dateString) {
    if (!dateString) return '';

    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/**
 * Formater un montant en euros
 */
function formatCurrency(amount) {
    if (amount === null || amount === undefined) return '0,00 €';

    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

/**
 * Générer un avatar avec initiales
 */
function generateAvatarInitials(name) {
    if (!name) return '';

    return name.split(' ')
        .map(word => word.charAt(0).toUpperCase())
        .join('')
        .substring(0, 2);
}

/**
 * Copier du texte dans le presse-papiers
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showAlert('Copié dans le presse-papiers', 'success', 2000);
    } catch (err) {
        // Fallback pour les navigateurs plus anciens
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();

        try {
            document.execCommand('copy');
            showAlert('Copié dans le presse-papiers', 'success', 2000);
        } catch (err) {
            showAlert('Erreur lors de la copie', 'danger');
        }

        document.body.removeChild(textArea);
    }
}

/**
 * Exporter des données au format CSV
 */
function exportToCSV(data, filename) {
    const csvContent = "data:text/csv;charset=utf-8,"
        + data.map(row => row.join(",")).join("\n");

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Valider un email
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Valider un numéro de téléphone français
 */
function isValidPhone(phone) {
    const phoneRegex = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

/**
 * Générer un mot de passe aléatoire
 */
function generatePassword(length = 12) {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";

    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }

    return password;
}

/**
 * Calculer la force d'un mot de passe
 */
function getPasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8) strength += 1;
    if (/[a-z]/.test(password)) strength += 1;
    if (/[A-Z]/.test(password)) strength += 1;
    if (/[0-9]/.test(password)) strength += 1;
    if (/[^A-Za-z0-9]/.test(password)) strength += 1;

    return {
        score: strength,
        label: strength <= 2 ? 'Faible' : strength <= 3 ? 'Moyen' : 'Fort',
        color: strength <= 2 ? 'danger' : strength <= 3 ? 'warning' : 'success'
    };
}
