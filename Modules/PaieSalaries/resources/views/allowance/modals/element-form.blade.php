<!-- Modal -->
<div class="modal fade" id="addElementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Ajouter un élément de paie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="elementForm" action="{{ route('company.paiesalaries.allowance.storeOption') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6 mb-4">
                            <label for="allowance_option" class="col-form-label">Nom de la prime</label>
                            <input type="text" name="allowance_option" class="form-control" placeholder="Entrer le nom">
                        </div>
                        <div class="form-group col-md-6 mb-4">
                            <label for="code_compta" class="col-form-label">Code comptable</label>
                            <input type="text" 
                                   name="code_compta" 
                                   class="form-control" 
                                   placeholder="Entrer le code comptable (8 chiffres)"
                                   pattern="\d{8}" 
                                   title="Le code comptable doit contenir exactement 8 chiffres"
                                   maxlength="8"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)"
                                   required>
                        </div>
                        <div class="form-group col-md-4 mb-4">
                            <label for="trait_fisc" class="col-form-label amount_label">Traitement fiscal - Exonérations</label>
                            <select name="trait_fisc" class="form-select" id="trait_fisc" onchange="exofisc()">
                                <option value="exo 0%">0%</option>
                                <option value="exo 10%">10%-Art 116-1</option>
                                <option value="exo 100%">100% (Parfois limite d'exo)</option>
                                <option value="Remboursement de frais">Remboursement de frais</option>
                                <option value="Autre">Autre</option>
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
                            <input type="number" class="form-control" id="amount_imp_fisc" name="amount_imp_fisc" placeholder="Entrer le montant exonéré" hidden>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="trait_cnps" class="col-form-label amount_label">Traitement CNPS</label>
                            <select name="trait_cnps" class="form-select" id="trait_cnps" onchange="montimpo()">
                                <option value="Soumis">Soumis</option>
                                <option value="Non Soumis">Non Soumis</option>
                                <option value="Soumis au-delà du mode de calcul">Soumis au-delà du mode de calcul</option>
                            </select>
                            <br/>
                            <input type="number" class="form-control" id="amount_imp" name="amount_imp" placeholder="Entrer le montant Soumis" hidden>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="type_montant" class="col-form-label amount_label">Type de montant</label>
                            <select name="type_montant" class="form-select" id="type_montant">
                                <option value="1">Conventionnel</option>
                                <option value="0">Libre</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>