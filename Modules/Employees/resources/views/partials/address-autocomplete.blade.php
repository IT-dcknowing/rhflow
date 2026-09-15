{{--
    Suggestions sous le champ « Adresse » (textarea #address) des formulaires employé.
    Source : route company.employees.address-suggestions (adresses de la société + localités de Côte d'Ivoire).
--}}
@push('styles')
    <style>
        .adr-wrap { position: relative; }
        .adr-liste {
            position: absolute; left: 0; right: 0; z-index: 1060;
            margin-top: 4px; padding: 4px;
            max-height: 264px; overflow-y: auto;
            background: var(--surface, #fff);
            border: 1px solid var(--line, #e2e7f0);
            border-radius: 10px;
            box-shadow: var(--shadow-pop, 0 12px 40px rgba(20, 27, 43, .16));
        }
        .adr-liste[hidden] { display: none; }
        .adr-option {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 8px 10px; border-radius: 7px; cursor: pointer;
            font-size: 13px; color: var(--ink, #141b2b);
        }
        .adr-option:hover, .adr-option.actif { background: var(--navy-tint-2, #f8faff); color: var(--navy-text, #253e87); }
        .adr-detail { flex-shrink: 0; font-size: 11px; color: var(--muted, #6c7890); }
        .adr-option[data-source="societe"] .adr-detail { color: var(--ok, #1f7a4d); }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var champ = document.getElementById('address');
            if (!champ || champ.dataset.suggestions) return;
            champ.dataset.suggestions = '1';

            var url = @json(route('company.employees.address-suggestions'));
            var liste = document.createElement('div');
            liste.className = 'adr-liste';
            liste.id = 'address-suggestions';
            liste.setAttribute('role', 'listbox');
            liste.hidden = true;

            champ.setAttribute('autocomplete', 'off');
            champ.setAttribute('aria-autocomplete', 'list');
            champ.setAttribute('aria-controls', liste.id);
            champ.setAttribute('aria-expanded', 'false');
            champ.parentElement.classList.add('adr-wrap');
            champ.insertAdjacentElement('afterend', liste);

            var minuterie = null;
            var resultats = [];
            var actif = -1;

            function fermer() {
                liste.hidden = true;
                actif = -1;
                champ.setAttribute('aria-expanded', 'false');
                champ.removeAttribute('aria-activedescendant');
            }

            function surligner(index) {
                var options = liste.querySelectorAll('.adr-option');
                options.forEach(function (option, i) { option.classList.toggle('actif', i === index); });
                actif = index;
                if (options[index]) {
                    champ.setAttribute('aria-activedescendant', options[index].id);
                    options[index].scrollIntoView({ block: 'nearest' });
                }
            }

            function choisir(index) {
                if (!resultats[index]) return;
                champ.value = resultats[index].texte;
                fermer();
                champ.dispatchEvent(new Event('change', { bubbles: true }));
                champ.focus();
            }

            function afficher(items) {
                liste.innerHTML = '';
                items.forEach(function (item, i) {
                    var option = document.createElement('div');
                    option.className = 'adr-option';
                    option.id = 'address-suggestion-' + i;
                    option.setAttribute('role', 'option');
                    option.dataset.source = item.source;

                    var texte = document.createElement('span');
                    texte.textContent = item.texte;
                    var detail = document.createElement('span');
                    detail.className = 'adr-detail';
                    detail.textContent = item.detail;
                    option.appendChild(texte);
                    option.appendChild(detail);

                    // mousedown : choisir avant que le champ ne perde le focus
                    option.addEventListener('mousedown', function (e) { e.preventDefault(); choisir(i); });
                    liste.appendChild(option);
                });
                liste.hidden = false;
                champ.setAttribute('aria-expanded', 'true');
            }

            function rechercher() {
                var saisie = champ.value.trim();
                if (saisie.length < 2) { fermer(); return; }

                fetch(url + '?q=' + encodeURIComponent(saisie), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (reponse) { return reponse.ok ? reponse.json() : []; })
                    .then(function (items) {
                        if (champ.value.trim() !== saisie) return; // réponse d'une saisie dépassée
                        resultats = items;
                        items.length ? afficher(items) : fermer();
                    })
                    .catch(fermer);
            }

            champ.addEventListener('input', function () {
                clearTimeout(minuterie);
                minuterie = setTimeout(rechercher, 250);
            });

            champ.addEventListener('keydown', function (e) {
                if (liste.hidden) return;
                var total = resultats.length;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    surligner((actif + 1) % total);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    surligner((actif - 1 + total) % total);
                } else if (e.key === 'Enter' && actif >= 0) {
                    e.preventDefault(); // sans suggestion surlignée, Entrée garde son retour à la ligne
                    choisir(actif);
                } else if (e.key === 'Escape') {
                    fermer();
                }
            });

            champ.addEventListener('blur', function () { setTimeout(fermer, 150); });
        })();
    </script>
@endpush
