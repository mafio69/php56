<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana klienta</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::to('/injuries/card/postAssignClient', [$injury->id]) }}" method="post"  id="dialog-form">
        {{ Form::token() }}

        <div class="row marg-btm" id="search_client">
            <div class="col-md-12 marg-btm">
                <label>Wyszukaj klienta po nazwie:</label>
                {{ Form::text('search_client', '', array('class' => 'form-control', 'id' => 'get_search_client', 'placeholder' => 'wprowadź nazwę klienta'))  }}
            </div>
            <div class="col-sm-12 col-md-8 col-md-offset-2 alert alert-danger" role="alert" id="client_alert" style="display: none;">Nie znaleziono klienta w bazie</div>
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <span class="btn btn-primary btn-sm btn-block" id="add_new_client">
                        <i class="fa fa-user-plus"></i> wprowadź nowego klienta
                    </span>
            </div>
            {{ Form::hidden('client_id') }}
        </div>

        <div class="form-group" id="create_new_client" style="display:none;">
            <h4 class="inline-header"><span>Dane nowego klienta:</span></h4>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Nazwa:</label>
                    {{ Form::text('name', null, array('class' => 'form-control  required', 'id'=>'name',  'placeholder' => 'nazwa')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >NIP:</label>
                    {{ Form::text('NIP', null, array('class' => 'form-control  required', 'id'=>'NIP',  'placeholder' => 'NIP')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >REGON:</label>
                    {{ Form::text('REGON', null, array('class' => 'form-control  ', 'id'=>'REGON',  'placeholder' => 'REGON')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod klienta:</label>
                    {{ Form::text('firmID', null, array('class' => 'form-control  ', 'id'=>'firmID',  'placeholder' => 'kod klienta')) }}
                </div>
            </div>
            <h4 class="inline-header"><span>Adres rejestrowy:</span></h4>
            <div class="row">
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod pocztowy:</label>
                    {{ Form::text('registry_post', null, array('class' => 'form-control  ', 'id'=>'registry_post',  'placeholder' => 'kod pocztowy')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Miasto:</label>
                    {{ Form::text('registry_city', null, array('class' => 'form-control  ', 'id'=>'registry_city',  'placeholder' => 'Miasto')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Ulica:</label>
                    {{ Form::text('registry_street', null, array('class' => 'form-control  ', 'id'=>'registry_street',  'placeholder' => 'ulica')) }}
                </div>
            </div>
            <h4 class="inline-header"><span>Dane kontaktowe:</span></h4>
            <div class="row">
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod pocztowy:</label>
                    {{ Form::text('correspond_post', null, array('class' => 'form-control  ', 'id'=>'correspond_post',  'placeholder' => 'kod pocztowy')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Miasto:</label>
                    {{ Form::text('correspond_city', null, array('class' => 'form-control  ', 'id'=>'correspond_city',  'placeholder' => 'Miasto')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Ulica:</label>
                    {{ Form::text('correspond_street', null, array('class' => 'form-control  ', 'id'=>'correspond_street',  'placeholder' => 'ulica')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Tefelon:</label>
                    {{ Form::text('phone', null, array('class' => 'form-control  ', 'id'=>'phone',  'placeholder' => 'telefon')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Email:</label>
                    {{ Form::text('email', null, array('class' => 'form-control email ', 'id'=>'email',  'placeholder' => 'email')) }}
                </div>
            </div>
        </div>


    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="Trwa wykonywanie...">Zapisz</button>
</div>
<script type="text/javascript">


    $('#add_new_client').on('click', function(){
        $('#search_client').remove();
        $('#create_new_client').show();
    });

    $('#get_search_client').autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: "<?php echo  URL::to('/injuries/card/postSearchClient');?>",
                data: {
                    term: request.term,
                    client_id: "{{ $vehicle->client_id }}",
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                type: "POST",
                success: function( data ) {
                    if (jQuery.isEmptyObject(data))
                        $('#client_alert').show();

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
            if(ui.item.id != $('input[name="client_id"]').val())
            {
                $('input[name="client_id"]').val(ui.item.id);
            }
        }
    }).bind("keypress", function(e) {
        if(e.which == 13){
            setTimeout(function(){
                $('#get_search_client').focusout();
            },500);
        }else{
            $('input[name="client_id"]').val('');
            $('#client_alert').hide();
        }
    });
</script>