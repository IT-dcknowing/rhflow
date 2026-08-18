@extends('layouts.app')

@section('title', 'Signatures de contrats - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📋 Signature contrats</h4>
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

    <!-- Liste des types de contrats -->
    <div class="row">
        <form id='form_pad' method="post" enctype="multipart/form-data">
            @method('POST')
            <div class="modal-body" id="">
                <div class="row">

                    <input type="hidden" name="contract_id" id="contract_id" value="{{$contract->id}}">
                
                    
                    <div class="form-control" >
                        <canvas id="signature-pad" class="signature-pad" height=200 ></canvas>
                        <input type="hidden" @if(Auth::user()->type == 'company' || Auth::user()->type == 'hr' || Auth::user()->type == 'hr')name="company_signature" @elseif(Auth::user()->type == 'employee' ) name="employee_signature" @endif id="SignupImage1">
                    </div>
                    <div class="mt-1">
                    <button type="button" class="btn-sm btn-danger" id="clearSig">{{__('Clear')}}</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" value="{{__('Annuler')}}" class="btn btn-outline-danger" data-bs-dismiss="modal">
                <input type="button" id="addSig" value="{{__('Signer')}}" class="btn btn-primary ms-2">
            </div>
        </form>

        <form id='form_pad' method="post" enctype="multipart/form-data">
            @method('POST')
            <div class="modal-body" id="">
                <div class="row">

                    <input type="hidden" name="contract_id" id="contract_id" value="{{$contract->id}}">
                
                    
                    <div class="form-control" >
                        <canvas id="signature-pad" class="signature-pad" height=200 ></canvas>
                        <input type="hidden" @if(Auth::user()->type == 'company' || Auth::user()->type == 'hr' || Auth::user()->type == 'hr')name="company_signature" @elseif(Auth::user()->type == 'employee' ) name="employee_signature" @endif id="SignupImage1">
                    </div>
                    <div class="mt-1">
                    <button type="button" class="btn-sm btn-danger" id="clearSig">{{__('Clear')}}</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" value="{{__('Annuler')}}" class="btn btn-outline-danger" data-bs-dismiss="modal">
                <input type="button" id="addSig" value="{{__('Signer')}}" class="btn btn-primary ms-2">
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('assets/js/plugins/signature_pad/signature_pad.min.js')}}"></script>
    <script>
        var contract_id = $('#contract_id').val();
        var signature = {
            canvas: null,
            clearButton: null,

            init: function init() {

                this.canvas = document.querySelector(".signature-pad");
                this.clearButton = document.getElementById('clearSig');
                this.saveButton = document.getElementById('addSig');
                    signaturePad = new SignaturePad(this.canvas);


                    this.clearButton.addEventListener('click', function (event) {
                    
                        signaturePad.clear();
                    });

                    this.saveButton.addEventListener('click', function (event) {
                        var data = signaturePad.toDataURL('image/png');
                        $('#SignupImage1').val(data);



                        $.ajax({
                        url: '{{route("company.contracts.signature.save",":id")}}'.replace(':id', contract_id),
                        type: 'POST',
                        data: $("form").serialize(),
                        success: function (data) {
                            show_toastr('success', data.message);
                            $('#commonModal').modal('hide');
                        },
                        error: function (data) {
                        }
                    });


                    });
                
            }
        };

        signature.init();

    </script>
    <script src="{{asset('assets/js/plugins/signature_pad/signature_pad.min.js')}}"></script>
    <script>
        var contract_id = $('#contract_id').val();
        var signature = {
            canvas: null,
            clearButton: null,

            init: function init() {

                this.canvas = document.querySelector(".signature-pad");
                this.clearButton = document.getElementById('clearSig');
                this.saveButton = document.getElementById('addSig');
                    signaturePad = new SignaturePad(this.canvas);


                    this.clearButton.addEventListener('click', function (event) {
                    
                        signaturePad.clear();
                    });

                    this.saveButton.addEventListener('click', function (event) {
                        var data = signaturePad.toDataURL('image/png');
                        $('#SignupImage1').val(data);



                        $.ajax({
                        url: '{{route("company.contracts.signature.save", ":id")}}'.replace(':id', contract_id),
                        type: 'POST',
                        data: $("form").serialize(),
                        success: function (data) {
                            show_toastr('success', data.message);
                            $('#commonModal').modal('hide');
                        },
                        error: function (data) {
                        }
                    });


                    });
                
            }
        };

        signature.init();

    </script>
@endpush