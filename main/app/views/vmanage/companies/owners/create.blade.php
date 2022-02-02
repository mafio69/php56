<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie właściciela pojazdu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ action('VmanageOwnersController@postStore') }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
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
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="add-owner">Dodaj</button>
</div>
