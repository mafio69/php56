<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">Wybierz obiekt umowy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form id="select-insurance-object-form" >

        @foreach($objects as $k => $object)
            <div class="row">
                <div class="col-sm-12">
                    <div class="radio">
                        <label>
                            <input type="radio" name="selected_insurance_object" value="{{ $object['id']     }}"
                                   @if($k == 0) checked @endif
                            >

                            <div class="marg-right pull-left">
                                <strong>Opis przedmiotu: </strong>
                                <p class="form-control-static">{{ $object['description'] }}</p>
                                <input type="hidden" name="object_insurance_description" value="{{ $object['description'] }}">
                            </div>
                            <div class="marg-right pull-left">
                                <strong>Numer fabryczny: </strong>
                                <p class="form-control-static">{{ $object['factoryNbr'] }}</p>
                                <input type="hidden" name="object_insurance_factoryNbr" value="{{ $object['factoryNbr'] }}">
                            </div>
                            <div class="marg-right pull-left">
                                <strong>Kategoria: </strong>
                                <p class="form-control-static">{{ $object['assetType'] }}</p>
                                <input type="hidden" name="object_insurance_assetType" value="{{ $object['assetType_id'] }}">
                            </div>
                            <div class="pull-left">
                                <strong>Rok produkcji: </strong>
                                <p class="form-control-static">{{ $object['year_production'] }}</p>
                                <input type="hidden" name="object_insurance_year_production" value="{{ $object['year_production'] }}">
                            </div>
                        </label>
                    </div>
                </div>
            </div>

        @endforeach

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" id="select-insurance-object">Wybierz</button>
</div>

<script>
    $('#select-insurance-object').on('click', function(){
        var $object_id = $('input[name="selected_insurance_object"]:checked').val();
        var $checked_label = $('input[name="selected_insurance_object"]:checked').parent();
        var $object_description = $checked_label.find('input[name="object_insurance_description"]').val();
        var $object_factoryNbr = $checked_label.find('input[name="object_insurance_factoryNbr"]').val();
        var $object_assetType = $checked_label.find('input[name="object_insurance_assetType"]').val();
        var $object_year_production = $checked_label.find('input[name="year_production"]').val();

        $('#factoryNbr').val($object_factoryNbr);
        $('#description').val($object_description);
        $('#assetType_id option[value="'+$object_assetType+'"]').attr("selected", "selected");
        $('#year_production').val($object_year_production);

        $('#modal-lg').modal('hide');
        $('#modal-lg .modal-content').html('');
    });
</script>