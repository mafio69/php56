<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie użytkownika pojazdu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
  	<form action="{{ action('VmanageUsersController@postStore') }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <input type="hidden" name="vmanage_company_id" value="{{ $company->id }}"/>
        <div class="form-group">
        <div class="row">
            <div class="col-sm-12 marg-btm">
                <label >Imię:</label>
                {{ Form::text('name', '', array('class' => 'form-control required', 'id'=>'name',  'placeholder' => 'imię', 'required')) }}
            </div>
            <div class="col-sm-12 marg-btm">
                <label >Nazwisko:</label>
                {{ Form::text('surname', '', array('class' => 'form-control required', 'id'=>'surname',  'placeholder' => 'nazwisko', 'required')) }}
            </div>
            <div class="col-sm-12 marg-btm">
                <label >Telefon:</label>
                {{ Form::text('phone', '', array('class' => 'form-control ',  'placeholder' => 'telefon kontaktowy')) }}
            </div>
            <div class="col-sm-12 marg-btm">
                <label >Email:</label>
                {{ Form::text('email', '', array('class' => 'form-control email',  'placeholder' => 'adres email')) }}
            </div>
          </div>
        </div>
  	</form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="add-user">Dodaj</button>
</div>
