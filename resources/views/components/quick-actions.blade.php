{{--
    Barre « Actions rapides » commune à tout le projet.

    Disposition de la maquette, en deux lignes :
      - ligne 1 : les actions principales (boutons pleins), slot par défaut ;
      - ligne 2 : les actions secondaires (slot « secondary ») à gauche,
                  et les actions de service (slot « end ») repoussées à droite.
    La deuxième ligne n'apparaît que si l'un des deux slots est fourni.

    Exemple :
        <x-quick-actions>
            <x-quick-action icon="fas fa-user-plus" label="Nouvel employé (mensuel)"
                            :href="route('company.employees.create')" />
            <x-slot:secondary>
                <x-quick-action icon="fas fa-calendar" label="Approuver les congés"
                                :href="route('company.leaves.index')" variant="outline" color="warning" />
            </x-slot:secondary>
            <x-slot:end>
                <x-quick-action icon="fas fa-cog" label="Paramètres"
                                :href="route('company.settings.settings')" variant="ghost" />
            </x-slot:end>
        </x-quick-actions>

    « stacked » : colonne de boutons pleine largeur, pour les panneaux
    d'actions des pages de détail (colonne latérale étroite).
    « card » : mettre à false pour poser la barre dans une carte existante.
--}}
@props([
    'title' => 'Actions rapides',
    'stacked' => false,
    'card' => true,
])

<div {{ $attributes->merge(['class' => $card ? 'card qa-card mb-4' : 'qa-block']) }}>
    <div class="{{ $card ? 'card-body' : '' }}">
        @if ($title)
            <div class="qa-title">{{ $title }}</div>
        @endif

        <div class="qa-bar{{ $stacked ? ' qa-bar-stacked' : '' }}">
            <div class="qa-row">{{ $slot }}</div>

            @if (isset($secondary) || isset($end))
                <div class="qa-row">
                    @isset($secondary)
                        {{ $secondary }}
                    @endisset

                    @isset($end)
                        <div class="qa-end">{{ $end }}</div>
                    @endisset
                </div>
            @endif
        </div>
    </div>
</div>
