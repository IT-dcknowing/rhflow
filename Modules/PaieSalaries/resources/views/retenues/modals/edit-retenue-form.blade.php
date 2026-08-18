<form id="editRetenueForm" action="{{ route('company.paiesalaries.retenues.update', $retenue->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="form-group">
                <label for="libelle" class="form-label">Libellé</label>
                <input type="text" class="form-control" id="libelle" name="libelle" value="{{ $retenue->libelle }}" required>
            </div>
        </div>
        <div class="row">
            <div class="mb-3">
                <label for="ordre" class="form-label">Ordre d'affichage</label>
                <input type="number" class="form-control" name="ordre" id="ordre" value="{{ $retenue->ordre ? $retenue->ordre : $lastOrder->ordre+1 }}" readonly>
            </div>
            <div class="mb-3">
                <label for="part" class="form-label">Part</label>
                <select name="part" id="part" class="form-select">
                    <option value="">Sélectionnez la part</option>
                    <option value="1" {{ $retenue->salariale == 1 ? 'selected' : '' }}>Salariale</option>
                    <option value="0" {{ $retenue->patronale == 1 ? 'selected' : '' }}>Patronale</option>
                </select>   
                <input type="hidden" name="salariale" id="salariale" value="{{ $retenue->salariale == 1 ? 1 : 0 }}">    
                <input type="hidden" name="patronale" id="patronale" value="{{ $retenue->patronale == 1 ? 1 : 0 }}">                       
            </div>
        </div>
        @if($retenue->type_retenue_id != 5)
        <div class="col-md-12 mb-4">
            <div class="form-group">
                <label for="type_retenue_id" class="form-label">Type de retenue</label>
                <select class="form-select" id="type_retenue_id" name="type_retenue_id">
                    <option value="">Sélectionnez un type</option>
                    @foreach($typesRetenues as $type)
                        <option value="{{ $type->id }}" {{ $retenue->type_retenue_id == $type->id ? 'selected' : '' }}>{{ $type->libelle }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @else
            <input type="hidden" name="type_retenue_id" value="5">
        @endif

        <div class="col-md-12 mb-4" id="montantField" style="display: {{ $retenue->amount > 0 ? 'block' : 'none' }};">
            <div class="form-group">
                <label for="montant" class="form-label">Montant (FCFA)</label>
                <input type="number" class="form-control" id="montant" name="amount" value="{{ $retenue->amount ?? '' }}" step="0.01" min="0">
            </div>
        </div>

        <div class="col-md-12 mb-4" id="tauxField" style="display: {{ $retenue->taux > 0 ? 'block' : 'none' }};">
            <div class="form-group">
                <label for="taux" class="form-label">Taux (%)</label>
                <input type="number" class="form-control" id="taux" name="taux" value="{{ $retenue->taux ?? '' }}" min="0" max="100">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="statut" name="statut" {{ $retenue->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="statut">
                    Activer cette retenue
                </label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </div>
</form>

<script>
    // Soumission du formulaire en AJAX
    document.getElementById('editRetenueForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const url = form.action;
        
        // Convertir FormData en objet simple
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        $('#part').on('change', function(){
            if ($(this).val() === '1') {
                $('#salariale').val('1');
                $('#patronale').val('0');
            }else{
                $('#salariale').val('0');
                $('#patronale').val('1');
            }
        });
        
        // Utiliser la méthode PUT directement
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                ...formDataObj,
                _method: 'PUT',
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Succès',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                Swal.fire({
                    title: 'Erreur',
                    text: xhr.responseJSON?.message || 'Une erreur est survenue lors de la mise à jour de la retenue',
                    icon: 'error'
                });
            }
        });
    });
</script>
