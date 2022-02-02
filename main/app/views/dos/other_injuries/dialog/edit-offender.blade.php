<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych sprawcy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('dos.other.injuries.setEditInjuryOffender', array($id)) }}" method="post"  id="dialog-injury-form">

        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="col-md-6 marg-btm">
                    {{ Form::text('surname', $offender->surname, array('class' => 'form-control upper', 'placeholder' => 'nazwisko'))  }}
                </div>
                <div class="col-md-6  marg-btm">
                    {{ Form::text('name', $offender->name, array('class' => 'form-control upper',  'placeholder' => 'imię'))  }}
                </div>
            </div>
            <h4 class="inline-header"><span>Adres zameldowania:</span></h4>
            <div class="row">
                <div class="col-md-6  marg-btm">
                    {{ Form::text('post', $offender->post, array('class' => 'form-control upper',  'placeholder' => 'kod pocztowy'))  }}
                </div>
                <div class="col-md-6  marg-btm">
                    {{ Form::text('city', $offender->city, array('class' => 'form-control upper',  'placeholder' => 'miasto'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6  marg-btm">
                    {{ Form::text('street', $offender->street, array('class' => 'form-control upper',  'placeholder' => 'ulica'))  }}
                </div>
            </div>

            <h4 class="inline-header"><span>Dane pojazdu:</span></h4>
            <div class="row">
                <div class="col-md-6  marg-btm">
                    {{ Form::text('registration', $offender->registration, array('class' => 'form-control upper',  'placeholder' => 'nr rejestracyjny'))  }}
                </div>
                <div class="col-md-6  marg-btm">
                    {{ Form::text('car', $offender->car, array('class' => 'form-control upper',  'placeholder' => 'marka i model pojazdu'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6  marg-btm">
                    {{ Form::text('oc_nr', $offender->oc_nr, array('class' => 'form-control upper',  'placeholder' => 'nr polisy OC'))  }}
                </div>
                <div class="col-md-6  marg-btm">
                    {{ Form::text('zu', $offender->zu, array('class' => 'form-control upper',  'placeholder' => 'nazwa ZU'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6  marg-btm">
                    {{ Form::text('expire', $offender->expire, array('class' => 'form-control upper',  'placeholder' => 'data ważności polisy'))  }}
                </div>
                <div class="col-md-6  marg-btm">
                    <select name="owner" class="form-control" >
                        <option value="1">Sprawca jest właścicielem pojazdu:</option>
                        <option value="1"
                        @if($offender->owner == 1)
                                selected
                                @endif
                                >tak</option>
                        <option value="0"
                        @if($offender->owner == 0)
                                selected
                                @endif
                                >nie</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12  marg-btm">
                    {{ Form::textarea('remarks', $offender->remarks, array('class' => 'form-control ',  'placeholder' => 'uwagi', 'style' => 'height:50px;'))  }}
                </div>
            </div>
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('input[name=expire]').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });
    });

</script>