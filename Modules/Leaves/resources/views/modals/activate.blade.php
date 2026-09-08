{{--
    Formulaire d'activation d'un congé pour la paie.
    Chargé en AJAX et injecté dans le corps d'une modale partagée (voir leaves::index).
--}}
<form action="{{ route('company.leaves.activate', $leave->id) }}" method="POST">
    @csrf

    <div class="modal-body">
        <div class="alert alert-info d-flex align-items-start" role="alert">
            <i class="fas fa-info-circle me-2 mt-1"></i>
            <div>
                Activer ce congé crée la ligne <strong>« Allocation congé »</strong> sur la période choisie.
                Elle entrera dans le brut, l'imposable et le social du bulletin.
            </div>
        </div>

        <table class="table table-sm mb-4">
            <tr>
                <th style="width: 40%;">Employé</th>
                <td>{{ $leave->employee->name ?? '—' }}</td>
            </tr>
            <tr>
                <th>Type de congé</th>
                <td>{{ $leave->leaveType->title ?? '—' }}</td>
            </tr>
            <tr>
                <th>Dates</th>
                <td>
                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}
                    <i class="fas fa-arrow-right mx-1"></i>
                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}
                    <span class="badge bg-label-info ms-1">{{ $leave->total_leave_days }} j</span>
                </td>
            </tr>
            <tr>
                <th>Période de saisie</th>
                <td>{{ $leave->periode->nom ?? 'Non rattaché' }}</td>
            </tr>
        </table>

        @if($periodes->isEmpty())
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Aucune période de paie ouverte. Créez ou rouvrez une période avant d'activer ce congé.
            </div>
        @else
            <div class="mb-3">
                <label for="activate_periode_id" class="form-label">
                    Période de paie <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="activate_periode_id" name="periode_id" required>
                    <option value="">— Choisir une période —</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}"
                            {{ (int) old('periode_id', $leave->activated_periode_id ?? $leave->periode_id) === (int) $p->id ? 'selected' : '' }}>
                            {{ $p->nom }}
                            @if($p->exercice) — {{ $p->exercice->nom }} @endif
                            ({{ ucfirst($p->statut) }})
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Seules les périodes ouvertes sont proposées : une paie validée ou payée ne peut plus être modifiée.</div>
            </div>

            <div class="mb-3">
                <label for="activate_amount_leave" class="form-label">
                    Allocation congé (brut) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="number" step="1" min="0" class="form-control"
                           id="activate_amount_leave" name="amount_leave"
                           value="{{ old('amount_leave', (int) $leave->amount_leave) }}" required>
                    <span class="input-group-text">FCFA</span>
                </div>
                <div class="form-text">Montant calculé à la création du congé. Modifiez-le si nécessaire.</div>
            </div>
        @endif
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-success" {{ $periodes->isEmpty() ? 'disabled' : '' }}>
            <i class="fas fa-toggle-on me-1"></i>Activer pour la paie
        </button>
    </div>
</form>
