<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana właściciela pojazdu</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::action('VmanageVehicleInfoController@postOwnerInfo', [$vehicle->id]) }}" method="post"  id="dialog-form">
        <fieldset>
            <div class="row marg-btm" >
                <div class="col-md-12 marg-btm">
                    <label for="if_leasing">Pojazd w leasingu:</label>
                    {{ Form::select('if_leasing', [1 => 'nie', 2 => 'tak'], ($vehicle->owner_id == $vehicle->company->owner_id) ? 1 : 2, array('id'=>'if_leasing', 'class' => 'form-control'))  }}
                </div>
                <div class="col-md-12" id="owner_company_name">
                    <label>Właściciel:</label>
                    <p class="form-control">{{ $vehicle->company->owner->name }} <small><i>({{ $vehicle->company->name }})</i></small></p>
                </div>
            </div>
            <div class="row marg-btm" id="search_owner">
                <div class="col-md-12 marg-btm">
                    <label>Wyszukaj właściciela po nazwie:</label>
                    {{ Form::text('search_owner', '', array('class' => 'form-control', 'id' => 'get_search_owner', 'placeholder' => 'wprowadź nazwę właściciela'))  }}
                </div>
                <div class="col-sm-12 col-md-8 col-md-offset-2 alert alert-danger" role="alert" id="owner_alert" style="display: none;">Nie znaleziono właściciela w bazie</div>
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <span class="btn btn-primary btn-sm btn-block" id="add_new_owner">
                        <i class="fa fa-user-plus"></i> wprowadź nowego właściela
                    </span>
                </div>
                {{ Form::hidden('owner_id') }}
            </div>
            <div class="row" id="create_new_owner" style="display:none;">
                <h4 class="inline-header"><span>Dane nowego właściciela pojazdu:</span></h4>
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <label >Nazwa:</label>
                            {{ Form::text('name', '', array('class' => 'form-control required', 'id'=>'name',  'placeholder' => 'nazwa właściciela', 'required')) }}
                        </div>
                    </div>
                    <h4 class="inline-header"><span>Adres właściciela:</span></h4>
                    <div class="row">
                        <div class="col-md-6 marg-btm">
                            <label>Kod pocztowy:</label>
                            {{ Form::text('post', '', array('class' => 'form-control', 'placeholder' => 'Kod pocztowy'))  }}
                        </div>
                        <div class="col-md-6 marg-btm">
                            <label>Miasto:</label>
                            {{ Form::text('city', '', array('class' => 'form-control', 'placeholder' => 'Miasto'))  }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 ">
                            <label>Ulica:</label>
                            {{ Form::text('street', '', array('class' => 'form-control', 'placeholder' => 'Ulica'))  }}
                        </div>
                    </div>
                </div>
            </div>
            {{Form::token()}}
        </fieldset>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="Trwa zmiana właściciela pojazdu...">Zapisz</button>
</div>
<script type="text/javascript">

    $('#if_leasing').on('change', function(){
        if( $(this).val() == 1) {
            $('#search_owner').hide();
            $('#owner_company_name').show();
        }else {
            $('#owner_company_name').hide();
            $('#search_owner').show();
        }
    }).change();

    $('#add_new_owner').on('click', function(){
        $('#search_owner').hide();
        $('#create_new_owner').show();
    });

    $('#get_search_owner').autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: "<?php echo  URL::action('VmanageOwnersController@postSearchOwner');?>",
                data: {
                    term: request.term,
                    owner_id: "{{ $vehicle->owner_id }}",
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                type: "POST",
                success: function( data ) {
                    if (jQuery.isEmptyObject(data))
                        $('#owner_alert').show();

                    response( $.map( data, function( item ) {
                        return item;
                    }));
                }
            });
        },
        minLength: 2,
        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        },
        select: function(event, ui) {
            if(ui.item.id != $('input[name="owner_id"]').val())
            {
                $('input[name="owner_id"]').val(ui.item.id);
            }
        }
    }).bind("keypress", function(e) {
        if(e.which == 13){
            setTimeout(function(){
                $('#get_search_owner').focusout();
            },500);
        }else{
            $('input[name="owner_id"]').val('');
            $('#owner_alert').hide();
        }
    });
</script>