@extends('layouts.super-admin')

@section('content')
<div class="row">
  <div class="col-12">

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0 text-primary">
            <i class="ti ti-list me-2"></i>
            Activité: {{ $company->name }}
          </h5>
          <small class="text-muted">
            {{ $company->email ?? 'N/A' }}
          </small>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('super-admin.enterprises.show', $enterprise) }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Retour
          </a>
        </div>
      </div>
    </div>
    
    <div class="row mt-3">
      <div class="col-xl-12 col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <div class="p-3 border rounded bg-light h-100">
                  <div class="text-muted">Statut abonnement</div>
                  <div class="fs-5 fw-semibold">
                    {{ $activity['subscription_status'] ?? 'N/A' }}
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 border rounded bg-light h-100">
                  <div class="text-muted">Début abonnement</div>
                  <div class="fs-5 fw-semibold">
                    {{ optional($activity['subscription_start_date'])->format('Y-m-d') }}
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 border rounded bg-light h-100">
                  <div class="text-muted">Fin abonnement</div>
                  <div class="fs-5 fw-semibold">
                    {{ optional($activity['subscription_end_date'])->format('Y-m-d') }}
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 border rounded bg-light h-100">
                  <div class="text-muted">Actif</div>
                  <div class="fs-5 fw-semibold">
                    {{ $activity['is_active'] ? 'Oui' : 'Non' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <div class="p-3 border rounded h-100">
                  <div class="text-muted">Plan</div>
                  <div class="fs-5">
                    <span class="badge bg-{{ ($pack->name ?? '') == 'Gratuit' ? 'primary' : (($pack->name ?? '') == 'Basic' ? 'info' : (($pack->name ?? '') == 'Pro' ? 'success' : 'danger')) }}">
                      {{ $pack->name ?? 'N/A' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border rounded h-100">
                  <div class="text-muted">Créée le</div>
                  <div class="fs-6">{{ optional($activity['created_at'])->format('Y-m-d H:i') }}</div>
                  <div class="text-muted mt-2">Modifiée le</div>
                  <div class="fs-6">{{ optional($activity['updated_at'])->format('Y-m-d H:i') }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border rounded h-100">
                  <div class="text-muted">Informations</div>
                  <ul class="mb-0">
                    <li>Email: {{ $company->email }}</li>
                    <li>Téléphone: {{ $company->phone ?? 'N/A' }}</li>
                    <li>Ville: {{ $company->city ?? 'N/A' }}</li>
                  </ul>
                </div>
              </div>
            </div>

            <h6 class="section-title mb-3">
              <i class="ti ti-timeline me-2"></i>Timeline synthétique
            </h6>
            @if(!empty($timeline))
              <ul class="list-group mb-3">
                @foreach($timeline as $item)
                  <li class="list-group-item d-flex align-items-start">
                    <div class="me-3"><i class="{{ $item['icon'] ?? 'ti ti-hand-point-right' }}"></i></div>
                    <div>
                      <div class="fw-semibold">{{ $item['title'] ?? '' }}</div>
                      <div class="text-muted small">{{ isset($item['time']) ? \Carbon\Carbon::parse($item['time'])->format('Y-m-d H:i') : '' }}</div>
                      @if(!empty($item['desc']))
                        <div class="small">{{ $item['desc'] }}</div>
                      @endif
                    </div>
                  </li>
                @endforeach
              </ul>
            @else
              <div class="alert alert-info">
                <i class="ti ti-info-alt me-2"></i>
                Aucun journal d'activité détaillé n'est encore branché. 
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>
@endsection
