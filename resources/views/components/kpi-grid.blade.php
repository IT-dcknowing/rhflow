{{--
    Carte qui regroupe les tuiles KPI d'un écran, reprise de la liste des
    utilisateurs (Modules/Settings/resources/views/users.blade.php) : en-tête
    avec titre et badge « Vue d'ensemble », puis une rangée de <x-kpi>.

    Exemple :
        <x-kpi-grid title="Statistiques des Utilisateurs">
            <x-kpi icon="fas fa-users" color="primary" label="Total" :value="$stats['total']" />
        </x-kpi-grid>

    Le slot « actions » remplace le badge quand l'en-tête porte des boutons ;
    le slot « footer » ajoute une zone sous les tuiles (répartitions, légendes).
--}}
@props([
    'title' => 'Statistiques',
    'badge' => "Vue d'ensemble",
])

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $title }}</h5>
                @isset($actions)
                    {{ $actions }}
                @elseif ($badge)
                    <span class="badge bg-label-primary">{{ $badge }}</span>
                @endisset
            </div>
            <div class="card-body">
                <div class="row">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="row mt-3">
                        <div class="col-12">
                            {{ $footer }}
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>
