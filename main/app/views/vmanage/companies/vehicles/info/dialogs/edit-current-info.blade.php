<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych bierzących pojazdu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::action('VmanageVehicleInfoController@postCurrentInfo', [$vehicle->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Miejsce użytkowania:</label>
                    {{ Form::text('place_of_usage', $vehicle->place_of_usage, array('class' => 'form-control'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Przebieg deklarowany:</label>
                    {{ Form::text('declare_mileage', $vehicle->declare_mileage, array('id'=>'declare_mileage', 'class' => 'form-control'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Przebieg bieżący:</label>
                    {{ Form::text('actual_mileage', $vehicle->actual_mileage , array('id'=>'actual_mileage', 'class' => 'form-control'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Termin badania technicznego:</label>
                    {{ Form::text('technical_exam_date', $vehicle->technical_exam_date, array('id'=>'technical_exam_date', 'class' => 'form-control date'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Termin przeglądu:</label>
                    {{ Form::text('servicing_date', $vehicle->servicing_date, array('id'=>'servicing_date', 'class' => 'form-control date'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="insurance_expire_date">Termin ważności polisy:</label>
                    {{ Form::text('insurance_expire_date', $vehicle->insurance_expire_date, ['class' => 'form-control date'])}}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="insurance">Suma ubezpieczenia AC:</label>
                    {{ Form::text('insurance', $vehicle->insurance, ['class' => 'form-control currency_input'])}}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="insurance">Assistance:</label>
                    {{ Form::text('assistance', $vehicle->assistance, ['class' => 'form-control currency_input'])}}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..." id="set">Zapisz</button>
</div>

<script type="text/javascript">
    $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
</script>