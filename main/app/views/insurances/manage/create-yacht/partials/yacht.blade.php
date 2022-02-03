<div class="col-sm-12 col-md-6 yacht-container">
    <div class="panel panel-default ">
        <div class="panel-heading overflow">
            <span class="btn btn-warning btn-xs pull-right delete-yacht"><i class="fa fa-trash-o"></i> usuń</span>
        </div>
        <div class="panel-body">

            <div class="form-group">
                <label>Nazwa przedmiotu:</label>
                {{ Form::text('yacht-name[]', '', array('class' => 'form-control required', 'placeholder' => 'nazwa przedmiotu umowy')) }}
            </div>
            <div class="form-group">
                <label>Kategoria</label>
                {{ Form::select('yacht-yacht_assetType_id[]', $yacht_assetTypes, '', array('class' => 'form-control  ')) }}
            </div>
            <div class="form-group">
                <label>Wart. z faktury netto przedm. umowy pożyczki</label>
                {{ Form::text('yacht-net_value[]', '', array('class' => 'form-control  number currency_input yacht-net_value', 'placeholder' => 'wartość z faktury netto')) }}
            </div>
            <div class="form-group">
                <label>Wart. brutto przedm. umowy pożyczki</label>
                {{ Form::text('yacht-gross_value[]', '', array('class' => 'form-control  number currency_input yacht-gross_value', 'placeholder' => 'wartość z faktury brutto')) }}
            </div>
            <div class="form-group">
                <label>Nr fabryczny:</label>
                {{ Form::text('fabric_number[]', '', array('class' => 'form-control ', 'placeholder' => 'numer fabryczny jachtu')) }}
            </div>
            <div class="form-group">
                <label>Nr rejestracyjny:</label>
                {{ Form::text('registration_number[]', '', array('class' => 'form-control ', 'placeholder' => 'numer rejestracyjny jachtu')) }}
            </div>
        </div>
    </div>
</div>
