<div class="col-sm-12 col-md-6 object-container">
    <div class="panel panel-default ">
        <div class="panel-heading overflow">
            <span class="btn btn-warning btn-xs pull-right delete-object"><i class="fa fa-trash-o"></i> usuń</span>
        </div>
        <div class="panel-body">

            <div class="form-group">
                <label>Nazwa przedmiotu:</label>
                {{ Form::text('object-name[]', '', array('class' => 'form-control required', 'placeholder' => 'nazwa przedmiotu umowy')) }}
            </div>
            <div class="form-group">
                <label>Kategoria</label>
                {{ Form::select('object-object_assetType_id[]', $object_assetTypes, '', array('class' => 'form-control  ')) }}
            </div>
            <div class="form-group">
                <label>Wart. z faktury netto przedm. umowy pożyczki</label>
                {{ Form::text('object-net_value[]', '', array('class' => 'form-control  number currency_input object-net_value', 'placeholder' => 'wartość z faktury netto')) }}
            </div>
            <div class="form-group">
                <label>Wart. brutto przedm. umowy pożyczki</label>
                {{ Form::text('object-gross_value[]', '', array('class' => 'form-control  number currency_input object-gross_value', 'placeholder' => 'wartość z faktury brutto')) }}
            </div>
        </div>
    </div>
</div>
