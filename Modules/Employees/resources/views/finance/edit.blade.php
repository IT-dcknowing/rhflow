<div class="modal-body">
    <div class="row justify-content-center">
        <div class="card-body">
            {!! Form::model($finance, ['route' => ['employee.finance.update', $finance->id], 'method' => 'PUT', 'id' => 'finance-form']) !!}
                <div class="row">
                    <div class="form-group col-md-12 mb-4">
                        {!! Form::label('account_holder_name', __('Nom du titulaire'), ['class' => 'form-label']) !!}
                        {!! Form::text('account_holder_name', null, [
                            'class' => 'form-control',
                            'placeholder' => __('Nom du titulaire'),
                        ]) !!}
                    </div>

                    <!-- Onglets pour sélectionner le type de compte -->
                    <div class="col-md-12 mb-4">
                        <ul class="nav nav-tabs" id="accountTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active"  id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank-content" type="button" role="tab" aria-controls="bank-content" aria-selected="true" style="cursor: none;">{{ __('Compte Bancaire') }}</button>
                            </li>
                        </ul>

                        <div class="tab-content mb-4">
                            <!-- Compte bancaire -->
                            <div class="tab-pane fade show active" id="bank-content">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        {!! Form::label('account_number', __('Numéro de compte'), ['class' => 'form-label']) !!}
                                        {!! Form::text('account_number', null, [
                                            'class' => 'form-control',
                                            'placeholder' => __('CXXXX 01XXX 00XXXXX 00059 XX'),
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        {!! Form::label('bank_name', __('Nom de la banque'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_name', null, [
                                            'class' => 'form-control',
                                            'placeholder' => __('Nom de la banque')
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        {!! Form::label('branch_location', __('Localisation de la succursale'), ['class' => 'form-label']) !!}
                                        {!! Form::text('branch_location', null, [
                                            'class' => 'form-control',
                                            'placeholder' => __('Localisation de la succursale'),
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        {!! Form::label('bank_identifier_code', __('Adresse de domiciliation'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_identifier_code', null, [
                                            'class' => 'form-control',
                                            'placeholder' => __('Adresse de domiciliation'),
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul class="nav nav-tabs" id="accountTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="mobile-tab"  onclick="initializeTabSwitching()" data-bs-toggle="tab" data-bs-target="#mobile-content" type="button" role="tab" aria-controls="mobile-content" aria-selected="false" style="cursor: none;">{{ __('Mobile Money') }}</button>
                            </li>
                        </ul>

                        <div class="tab-content mb-4">
                            <!-- Mobile Money -->
                            <div class="tab-pane fade show active" id="mobile-content">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        {!! Form::label('orange_money', __('Orange Money'), ['class' => 'form-label']) !!}
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <img src="{{ asset('assets/images/OM.png') }}" alt="Orange Money" style="height: 20px;">
                                                </span>
                                            </div>
                                            {!! Form::number('orange_money', null, [
                                                'class' => 'form-control',
                                                'placeholder' => __('Numéro Orange Money'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        {!! Form::label('mtn_money', __('MTN Money'), ['class' => 'form-label']) !!}
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <img src="{{ asset('assets/images/MTN.png') }}" alt="MTN Money" style="height: 20px;">
                                                </span>
                                            </div>
                                            {!! Form::number('mtn_money', null, [
                                                'class' => 'form-control',
                                                'placeholder' => __('Numéro MTN Money'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        {!! Form::label('moov_money', __('Moov Money'), ['class' => 'form-label']) !!}
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <img src="{{ asset('assets/images/MOOV.png') }}" alt="Moov Money" style="height: 20px;">
                                                </span>
                                            </div>
                                            {!! Form::number('moov_money', null, [
                                                'class' => 'form-control',
                                                'placeholder' => __('Numéro Moov Money'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        {!! Form::label('wave_money', __('Wave Money'), ['class' => 'form-label']) !!}
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <img src="{{ asset('assets/images/WAVE.png') }}" alt="Wave Money" style="height: 20px;">
                                                </span>
                                            </div>
                                            {!! Form::number('wave_money', null, [
                                                'class' => 'form-control',
                                                'placeholder' => __('Numéro Wave'),
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('employee.show', $employee->id) }}" class="btn btn-secondary me-2">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Mettre à jour') }}
                        </button>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    // Fonction pour initialiser la gestion des onglets et validations
    function initializeTabSwitching() {
        // Référence aux onglets
        const bankTab = document.getElementById('bank-tab');
        const mobileTab = document.getElementById('mobile-tab');

        if (!bankTab || !mobileTab) return; // Sortie si les éléments n'existent pas

        // Écouteurs d'événements pour les onglets
        bankTab.addEventListener('click', function() {
            // Activer l'onglet bank
            document.querySelectorAll('#accountTypeTabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Activer le contenu bank
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
            document.querySelector('#bank-content').classList.add('show', 'active');

            // Réinitialiser les champs mobile
            document.querySelectorAll('#mobile-content input').forEach(field => field.value = '');
        });

        mobileTab.addEventListener('click', function() {
            // Activer l'onglet mobile
            document.querySelectorAll('#accountTypeTabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Activer le contenu mobile
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
            document.querySelector('#mobile-content').classList.add('show', 'active');

            // Réinitialiser les champs bank
            document.querySelectorAll('#bank-content input').forEach(field => field.value = '');
        });

        // Validation du formulaire (reste inchangée)
        const financeForm = document.getElementById('finance-form');
        if (financeForm) {
            financeForm.addEventListener('submit', function(e) {
                const activeTabId = document.querySelector('.tab-pane.show.active').id;

                if (activeTabId === 'bank-content') {
                    const accountNumber = document.querySelector('[name="account_number"]').value;
                    if (!accountNumber.trim()) {
                        e.preventDefault();
                        alert('Veuillez saisir au moins le numéro de compte bancaire.');
                    }
                } else if (activeTabId === 'mobile-content') {
                    const orangeMoney = document.querySelector('[name="orange_money"]').value;
                    const mtnMoney = document.querySelector('[name="mtn_money"]').value;
                    const moovMoney = document.querySelector('[name="moov_money"]').value;
                    const waveMoney = document.querySelector('[name="wave_money"]').value;

                    if (!orangeMoney && !mtnMoney && !moovMoney && !waveMoney) {
                        e.preventDefault();
                        alert('Veuillez saisir au moins un numéro de mobile money.');
                    }
                }
            });
        }
    }

    // Initialisation lors du chargement du document
    document.addEventListener('DOMContentLoaded', initializeTabSwitching);

    // Pour le contenu chargé dynamiquement dans une modal
    document.addEventListener('shown.bs.modal', initializeTabSwitching);

    // Pour les cas où le contenu est chargé via AJAX sans événement modal
    // Exécuter l'initialisation après un court délai pour s'assurer que le DOM est mis à jour
    setTimeout(initializeTabSwitching, 500);
</script>
