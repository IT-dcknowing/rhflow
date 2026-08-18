<!-- Modal -->
<div class="row">
    <form id="elementForm" action="{{ route('company.paiesalaries.allowance.updateOption', $option->id) }}" method="POST">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="PUT">
        
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-6 mb-4">
                    <label for="name" class="col-form-label">Nom de la prime</label>
                    <input type="text" name="name" class="form-control" placeholder="Entrer le nom" value="{{ $option->name }}">
                </div>
                <div class="form-group col-md-6 mb-4">
                    <label for="code_compta" class="col-form-label">Code comptable</label>
                    <input type="text" name="code_compta" class="form-control" placeholder="Entrer le code comptable" value="{{ $option->code_compta }}">
                </div>
                <div class="form-group col-md-4 mb-4">
                    <label for="param_fiscal" class="col-form-label amount_label">Traitement fiscal - Exonérations</label>
                    <select name="param_fiscal" class="form-select" id="param_fiscal" onchange="exofisc()">
                        <option value="exo 0%" {{ $option->param_fiscal == 'exo 0%' ? 'selected' : '' }}>0%</option>
                        <option value="10% - Art 116-1" {{ $option->param_fiscal == '10% - Art 116-1' ? 'selected' : '' }}>10%-Art 116-1</option>
                        <option value="100% (Parfois limite d'exo)" {{ $option->param_fiscal == '100% (Parfois limite d\'exo)' ? 'selected' : '' }}>100% (Parfois limite d'exo)</option>
                        <option value="Remboursement de frais" {{ $option->param_fiscal == 'Remboursement de frais' ? 'selected' : '' }}>Remboursement de frais</option>
                        <option value="Autre" {{ $option->param_fiscal == 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                    <br/>
                    <span id="art_1"></span>
                    <div id="art_2" style="display: none;">
                        <input type="radio" id="alinea2" name="alinea" class="form-check-input" value="100%-Art 116-2">
                        <label class="form-check-label">&nbsp;&nbsp;Article 116 alinéas 2</label><br/>
                        <input type="radio" id="alinea10" name="alinea" class="form-check-input" value="100%-Art 116-10">
                        <label class="form-check-label">&nbsp;&nbsp;Article 116 alinéas 10</label><br/>
                        <input type="radio" id="alinea12" name="alinea" class="form-check-input" value="100%-Art 116-12">
                        <label class="form-check-label">&nbsp;&nbsp;Article 116 alinéas 12</label>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="param_social" class="col-form-label amount_label">Traitement CNPS</label>
                    <select name="param_social" class="form-select" id="param_social" onchange="montimpo()">
                        <option value="Soumis" {{ $option->param_social == 'Soumis' ? 'selected' : '' }}>Soumis</option>
                        <option value="Non Soumis" {{ $option->param_social == 'Non Soumis' ? 'selected' : '' }}>Non Soumis</option>
                        <option value="Soumis au-delà du mode de calcul" {{ $option->param_social == 'Soumis au-delà du mode de calcul' ? 'selected' : '' }}>Soumis au-delà du mode de calcul</option>
                    </select>
                    <br/>
                    <input type="number" class="form-control" id="amount_imp" name="amount_imp" placeholder="Entrer le montant Soumis" hidden value="{{ $option->amount_imp }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="type_montant" class="col-form-label amount_label">Type de montant</label>
                    <select name="type_montant" class="form-select" id="type_montant">
                        <option value="1" {{ $option->type_montant == 1 ? 'selected' : '' }}>Conventionnel</option>
                        <option value="0" {{ $option->type_montant == 0 ? 'selected' : '' }}>Libre</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>