<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych przedmiotu umowy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('dos.other.injuries.set', array('updateObject', $id)) }}" method="post"  id="dialog-injury-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Nr umowy leasingowej::</label>
                    {{ Form::text('nr_contract', $object->nr_contract, array('class' => 'form-control required', 'placeholder' => 'nr umowy leasingowej'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Opis:</label>
                    {{ Form::text('description', $object->description, array('class' => 'form-control', 'placeholder' => 'opis'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Typ:</label>
                    {{ Form::select('assetType_id', $assetTypes, $object->assetType_id, array('class' => 'form-control') )}}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Nr fabryczny:</label>
                    {{ Form::text('factoryNbr', $object->factoryNbr, array('class' => 'form-control', 'placeholder' => 'numer fabryczny'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Rok produkcji:</label>
                    {{ Form::text('year_production', $object->year_production, array('class' => 'form-control', 'placeholder' => 'rok produkcji'))  }}
                </div>
            </div>

        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
</div>

