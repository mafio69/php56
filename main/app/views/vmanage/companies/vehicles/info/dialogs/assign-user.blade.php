<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana użytkownika pojazdu</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::action('VmanageVehicleInfoController@postAssignUser', [$vehicle->id]) }}" method="post"  id="dialog-form">
        <fieldset>
            <div class="row marg-btm" id="search_user">
                <div class="col-md-12 marg-btm">
                    <label>Wyszukaj użytkownika po imieniu lub nazwisku:</label>
                    {{ Form::text('serach_user', '', array('class' => 'form-control', 'id' => 'get_search_user', 'placeholder' => 'wprowadź imię lub nazwisko użytkownika'))  }}
                </div>
                <div class="col-sm-12 col-md-8 col-md-offset-2 alert alert-danger" role="alert" id="user_alert" style="display: none;">Nie znaleziono użytkownika w bazie</div>
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <span class="btn btn-primary btn-sm btn-block" id="add_new_user">
                        <i class="fa fa-user-plus"></i> wprowadź nowego użytkownika
                    </span>
                </div>
                {{ Form::hidden('vmanage_user_id') }}
            </div>
            <div class="row" id="create_new_user" style="display:none;">
                <h4 class="inline-header"><span>Dane nowego użytkownika pojazdu:</span></h4>
                <div class="col-md-6 marg-btm">
                    <label>Imię:</label>
                    {{ Form::text('name', '', array('class' => 'form-control', 'placeholder' => 'imię'))  }}
                </div>
                <div class="col-md-6 marg-btm ">
                    <label>Nazwisko:</label>
                    {{ Form::text('surname', '', array('class' => 'form-control', 'placeholder' => 'nazwisko'))  }}
                </div>
                <div class="col-md-6 marg-btm ">
                    <label>Telefon:</label>
                    {{ Form::text('phone', '', array('class' => 'form-control', 'placeholder' => 'telefon'))  }}
                </div>
                <div class="col-md-6 marg-btm ">
                    <label>Email:</label>
                    {{ Form::text('email', '', array('class' => 'form-control email', 'placeholder' => 'email'))  }}
                </div>
            </div>
            {{Form::token()}}
        </fieldset>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="Trwa zmiana użytkownika pojazd...">Zapisz</button>
</div>
<script type="text/javascript">
    $('#add_new_user').on('click', function(){
        $('#search_user').hide();
        $('#create_new_user').show();
    });

    $('#get_search_user').autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: "<?php echo  URL::action('VmanageVehicleInfoController@postSearchUser');?>",
                data: {
                    term: request.term,
                    vmanage_user_id: "{{ $vehicle->vmanage_user_id }}",
                    vmanage_company_id: "{{ $vehicle->vmanage_company_id }}",
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                type: "POST",
                success: function( data ) {
                    if (jQuery.isEmptyObject(data))
                        $('#user_alert').show();

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
            if(ui.item.id != $('input[name="vmanage_user_id"]').val())
            {
                $('input[name="vmanage_user_id"]').val(ui.item.id);
            }
        }
    }).bind("keypress", function(e) {
        if(e.which == 13){
            setTimeout(function(){
                $('#get_search_user').focusout();
            },500);
        }else{
            $('input[name="vmanage_user_id"]').val('');
            $('#user_alert').hide();
        }
    });
</script>