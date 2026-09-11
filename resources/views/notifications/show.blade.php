@extends(Auth::check() && Auth::user()->type === 'super_admin' ? 'layouts.super-admin' : 'layouts.app')

@section('title', $notification->title)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <!-- Header -->
                <div class="card-header bg-{{ $notification->color }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-lg">
                                <div class="avatar-initial bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-primary">
                                    <i class="{{ $notification->icon ?? 'ti-bell' }} text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1 text-white">{{ $notification->title }}</h4>
                            <p class="mb-0 opacity-75 text-white">
                                <i class="ti ti-clock me-1 text-white"></i>
                                {{ $notification->created_at->translatedFormat('d F Y à H:i') }}
                                <span class="ms-2">•</span>
                                <span class="ms-2 text-white">{{ $notification->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                        @if($notification->is_read)
                            <div class="badge bg-white bg-opacity-25 ms-2 text-primary">
                                <i class="ti ti-check me-1 text-primary"></i>Lu
                            </div>
                        @else
                            <div class="badge bg-warning ms-2 text-primary">
                                <i class="ti ti-check-box me-1 text-primary"></i>Non lu
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body mt-2">
                    <!-- Message -->
                    <div class="mb-4">
                        <h6 class="mb-3">Message</h6>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0">{{ $notification->message }}</p>
                        </div>
                    </div>

                    <!-- Détails supplémentaires -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Informations</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Type</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-{{ $notification->color }}">
                                        {{ ucfirst($notification->type) }}
                                    </span>
                                </dd>

                                @if($notification->source)
                                    <dt class="col-sm-4">Source</dt>
                                    <dd class="col-sm-8">{{ $notification->source->name ?? 'N/A' }}</dd>
                                @endif

                                @if($notification->sender)
                                    <dt class="col-sm-4">De</dt>
                                    <dd class="col-sm-8">{{ $notification->sender->name }}</dd>
                                @endif

                                <dt class="col-sm-4">Date de création</dt>
                                <dd class="col-sm-8">{{ $notification->created_at->format('d/m/Y H:i:s') }}</dd>

                                @if($notification->read_at)
                                    <dt class="col-sm-4">Lu le</dt>
                                    <dd class="col-sm-8">{{ $notification->read_at->format('d/m/Y H:i:s') }}</dd>
                                @endif
                            </dl>
                        </div>

                        @if($notification->data)
                            <div class="col-md-6">
                                <h6 class="mb-3">Données supplémentaires</h6>
                                <div class="border rounded p-3 bg-light">
                                    <pre class="mb-0 text-sm"><code>{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                        <div class="d-flex gap-2">
                            <a href="{{ route('notifications.index') }}" class="btn bg-label-primary">
                                <i class="ti ti-arrow-left me-1"></i>Retour aux notifications
                            </a>

                            @if($notification->action_url)
                                <a href="{{ $notification->action_url }}" class="btn bg-primary text-white">
                                    <i class="ti ti-external-link me-1"></i>Voir l'action
                                </a>
                            @endif
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-outline-info btn-sm" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical me-1"></i>Actions
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if($notification->is_read)
                                    <button class="dropdown-item" onclick="markAsUnread({{ $notification->id }})">
                                        <i class="ti ti-close me-2"></i>Marquer comme non lu
                                    </button>
                                @else
                                    <button class="dropdown-item" onclick="markAsRead({{ $notification->id }})">
                                        <i class="ti ti-check me-2"></i>Marquer comme lu
                                    </button>
                                @endif
                                <button class="dropdown-item text-danger" onclick="deleteNotification({{ $notification->id }})">
                                    <i class="ti ti-trash me-2"></i>Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-initial {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .card-header {
        background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark, var(--bs-primary)) 100%);
    }

    pre {
        background-color: var(--bs-gray-100);
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        padding: 0.75rem;
    }

    .badge {
        font-size: 0.75rem;
    }
</style>

<script>
    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '_method=PATCH'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function markAsUnread(notificationId) {
        fetch(`/notifications/${notificationId}/unread`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '_method=PATCH'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function deleteNotification(notificationId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
            fetch(`/notifications/${notificationId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: '_method=DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("notifications.index") }}';
                }
            })
            .catch(error => console.error('Erreur:', error));
        }
    }
</script>
@endsection
