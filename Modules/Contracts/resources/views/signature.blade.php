@extends('layouts.app')

@section('title', 'Signatures de contrats - RH Flow')

@section('content')
@php
    // Seul le salarié signe le contrat.
    $dejaSignee = $contract->employee_signature;
@endphp
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                         Signature contrats</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.contracts.index') }}">Gestion des contrats</a>
                            </li>
                            <li class="breadcrumb-item active">Signature contrats</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('company.contracts.show', $contract->id) }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Signature du salarié</h5>
                        <small class="text-muted">
                            Contrat de {{ $contract->employee?->name ?? 'employé non défini' }} ·
                            dessinez votre signature dans le cadre ci-dessous.
                        </small>
                    </div>
                    @if($dejaSignee)
                        <span class="badge bg-label-success">
                            <i class="fas fa-check me-1"></i>Déjà signé
                        </span>
                    @endif
                </div>

                <div class="card-body">
                    @if($dejaSignee)
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Une signature existe déjà pour ce contrat. En signant de nouveau, vous la remplacerez.
                        </div>
                    @endif

                    <form id="form_pad" method="POST"
                        action="{{ route('company.contracts.signature.save', $contract->id) }}">
                        @csrf

                        <input type="hidden" name="signature_type" value="employee">
                        <input type="hidden" name="signature_data" id="signature_data">

                        <div class="border rounded p-1 bg-white">
                            <canvas id="signature-pad" class="signature-pad d-block"
                                style="width: 100%; height: 220px; touch-action: none; cursor: crosshair;"></canvas>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-danger" id="clearSig">
                                <i class="fas fa-eraser me-1"></i>{{ __('Effacer') }}
                            </button>

                            <div class="d-flex gap-2">
                                <a href="{{ route('company.contracts.show', $contract->id) }}"
                                    class="btn btn-outline-secondary">{{ __('Annuler') }}</a>
                                <button type="submit" id="addSig" class="btn btn-primary">
                                    <i class="fas fa-pen-nib me-1"></i>{{ __('Signer') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/plugins/signature_pad/signature_pad.min.js') }}"></script>
    <script>
        (function () {
            var canvas = document.getElementById('signature-pad');
            var form = document.getElementById('form_pad');

            if (!canvas || !form || typeof SignaturePad === 'undefined') {
                return;
            }

            var signaturePad = new SignaturePad(canvas, {
                penColor: '#1a1a1a',
                backgroundColor: 'rgba(255, 255, 255, 0)'
            });

            // Le canevas doit avoir la même taille en pixels qu'à l'écran, sinon le
            // trait se dessine décalé par rapport au curseur.
            function ajusterCanvas() {
                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                var rect = canvas.getBoundingClientRect();

                if (!rect.width || !rect.height) {
                    return;
                }

                var trace = signaturePad.isEmpty() ? null : signaturePad.toData();

                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                canvas.getContext('2d').scale(ratio, ratio);

                signaturePad.clear();

                if (trace) {
                    signaturePad.fromData(trace);
                }
            }

            window.addEventListener('resize', ajusterCanvas);
            ajusterCanvas();

            document.getElementById('clearSig').addEventListener('click', function () {
                signaturePad.clear();
            });

            form.addEventListener('submit', function (e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Signature manquante',
                            text: 'Dessinez votre signature dans le cadre avant de valider.',
                            confirmButtonColor: '#253e87'
                        });
                    } else {
                        alert('Dessinez votre signature dans le cadre avant de valider.');
                    }

                    return;
                }

                document.getElementById('signature_data').value = signaturePad.toDataURL('image/png');
            });
        })();
    </script>
@endpush
