@extends(Auth::check() && Auth::user()->type === 'super_admin' ? 'layouts.super-admin' : 'layouts.app')

@section('title', 'Notifications Non Lues')

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">Notifications Non Lues</h4>
                        <p class="text-muted mb-0">{{ $unreadCount }} notifications non lues</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-info">
                            <i class="ti ti-eye me-1"></i>Voir toutes
                        </a>
                        @if($unreadCount > 0)
                            <button class="btn bg-primary text-white" onclick="markAllAsRead()">
                                <i class="ti ti-check me-1"></i>Marquer tout comme lu
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card">
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="notification-item unread border-bottom"
                             data-notification-id="{{ $notification->id }}">
                            <div class="d-flex align-items-start p-3">
                                <!-- Icon -->
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-sm">
                                        <div class="avatar-initial bg-{{ $notification->color }} rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="{{ $notification->icon ?? 'ti-bell' }}"></i>
                                        </div>
                                    </div>
                                    <!-- Badge non lu -->
                                    <span class="badge bg-primary rounded-pill mt-1">Non lu</span>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold">
                                                {{ $notification->title }}
                                            </h6>
                                            <p class="mb-2 text-sm">
                                                {{ Str::limit($notification->message, 100) }}
                                            </p>
                                            <div class="d-flex align-items-center gap-3">
                                                <small class="text-muted">
                                                    <i class="ti ti-clock me-1"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                                @if($notification->action_url)
                                                    <a href="{{ $notification->action_url }}" class="text-primary text-decoration-none">
                                                        <i class="ti ti-arrow-right me-1"></i>Voir détails
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon bg-primary text-white" data-bs-toggle="dropdown">
                                                <i class="ti ti-menu"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button class="dropdown-item" onclick="markAsRead({{ $notification->id }})">
                                                    <i class="ti ti-check me-2"></i>Marquer comme lu
                                                </button>
                                                <button class="dropdown-item text-danger" onclick="deleteNotification({{ $notification->id }})">
                                                    <i class="ti ti-trash me-2"></i>Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <div class="avatar-initial bg-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-check text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5 class="text-success">Toutes les notifications sont lues !</h5>
                            <p class="text-muted mb-3">Vous avez lu toutes vos notifications récentes.</p>
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
                                Voir l'historique complet
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

<style>
    .notification-item {
        transition: all 0.2s ease;
        position: relative;
        background-color: rgba(var(--bs-primary-rgb), 0.05);
        border-left: 4px solid var(--bs-primary);
    }

    .notification-item:hover {
        background-color: rgba(var(--bs-light-rgb), 0.5);
    }

    .avatar-initial {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .badge {
        font-size: 0.75rem;
    }

    .dropdown-menu {
        min-width: 180px;
    }
</style>

<script>
    const_url = "{{ url('/notifications') }}";
    function markAsRead(notificationId) {
        fetch(`${const_url}/${notificationId}/read`, {
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
                // Supprimer l'élément de la liste ou le déplacer
                const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
                notification.remove();

                // Mettre à jour le compteur
                updateUnreadCount();

                // Si plus aucune notification, afficher le message de succès
                if (document.querySelectorAll('.notification-item').length === 0) {
                    location.reload();
                }
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function markAllAsRead() {
        if (confirm('Êtes-vous sûr de vouloir marquer toutes les notifications comme lues ?')) {
            fetch(`${const_url}/mark-all-read`, {
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
    }

    function deleteNotification(notificationId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
            fetch(`${const_url}/${notificationId}`, {
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
                    document.querySelector(`[data-notification-id="${notificationId}"]`).remove();
                    updateUnreadCount();

                    // Si plus aucune notification, afficher le message de succès
                    if (document.querySelectorAll('.notification-item').length === 0) {
                        location.reload();
                    }
                }
            })
            .catch(error => console.error('Erreur:', error));
        }
    }

    function updateUnreadCount() {
        fetch('{{ url("/notifications/ajax/unread?limit=1") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour le compteur dans l'interface
                document.querySelectorAll('.unread-count').forEach(element => {
                    element.textContent = data.total_unread;
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
</script>
@endsection
