<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie leasingobiorcy</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('insurances/info-dialog/store-client') }}" method="post"  id="dialog-form">
            <fieldset>
                <div class="row marg-btm">
                    <div class="col-md-12 ">
                        <label>Nazwa:</label>
                        {{ Form::text('name', '', array('class' => 'form-control required', 'placeholder' => 'nazwa', 'autofocuse' => ''))  }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 ">
                        <label>NIP:</label>
                        {{ Form::text('NIP', '', array('class' => 'form-control required', 'placeholder' => 'NIP', 'id'=>'NIP_to_check', 'autofocuse' => ''))  }}
                    </div>
                    <div class="col-md-6 ">
                        <label>REGON:</label>
                        {{ Form::text('REGON', '', array('class' => 'form-control required', 'placeholder' => 'REGON', 'autofocuse' => ''))  }}
                    </div>
                    <div class="col-md-6 ">
                        <label>Kod klienta:</label>
                        {{ Form::text('firmID', '', array('class' => 'form-control', 'placeholder' => 'kod klienta'))  }}
                    </div>
                </div>
                <h4 class="inline-header"><span>Adres rejestrowy:</span></h4>
                <div class="row marg-btm">
                    <div class="col-md-6 ">
                        <label>Kod pocztowy:</label>
                        {{ Form::text('registry_post', '', array('class' => 'form-control', 'placeholder' => 'Kod pocztowy'))  }}
                    </div>
                    <div class="col-md-6 ">
                        <label>Miasto:</label>
                        {{ Form::text('registry_city', '', array('class' => 'form-control', 'placeholder' => 'Miasto'))  }}
                    </div>
                </div>
                <div class="row marg-btm">
                    <div class="col-md-6 ">
                        <label>Ulica:</label>
                        {{ Form::text('registry_street', '', array('class' => 'form-control', 'placeholder' => 'Ulica'))  }}
                    </div>
                </div>
                <h4 class="inline-header"><span>Adres kontaktowy:</span></h4>
                <div class="row marg-btm">
                    <div class="col-md-6 ">
                        <label>Kod pocztowy:</label>
                        {{ Form::text('correspond_post', '', array('class' => 'form-control', 'placeholder' => 'Kod pocztowy'))  }}
                    </div>
                    <div class="col-md-6 ">
                        <label>Miasto:</label>
                        {{ Form::text('correspond_city', '', array('class' => 'form-control', 'placeholder' => 'Miasto'))  }}
                    </div>
                </div>
                <div class="row marg-btm">
                    <div class="col-md-12 ">
                        <label>Ulica:</label>
                        {{ Form::text('correspond_street', '', array('class' => 'form-control', 'placeholder' => 'Ulica'))  }}
                    </div>
                </div>
                <div class="row marg-btm">
                    <div class="col-md-6 ">
                        <label>Telefon:</label>
                        {{ Form::text('phone', '', array('class' => 'form-control', 'placeholder' => 'Telefon'))  }}
                    </div>
                    <div class="col-md-6 ">
                        <label>Email:</label>
                        {{ Form::text('email', '', array('class' => 'form-control', 'placeholder' => 'Email'))  }}
                    </div>
                </div>
                {{Form::token()}}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="addClient" data-loading-text="Trwa tworzenie leasingobiorcy...">Dodaj</button>
</div>
<script type="text/javascript">
    $('#NIP_to_check').focusout(function(){
        if($(this).val().length > 0) {
            $.ajax({
                url: "{{ URL::to('insurances/info-dialog/check-client-NIP') }}",
                data: {
                    NIP: $('#NIP_to_check').val(),
                    _token: $('input[name="_token"]').val()
                },
                async: false,
                cache: false,
                type: "POST",
                success: function (data) {
                    if (data == '1') {
                        alert('Istnieje już klient w systemie o podanym numerze NIP.');
                        $('#NIP_to_check').val('').focus();
                    }
                }
            });
        }
    });
</script>