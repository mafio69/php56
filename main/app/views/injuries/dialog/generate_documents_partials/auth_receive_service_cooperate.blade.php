<div class="row">
    <div class="col-sm-12 marg-btm">
      @include('injuries.dialog.generate_documents_partials.branch_options')
        <label >Opis upoważnienia:</label>
        {{ Form::text('description', '', array('class' => 'form-control  ', 'id'=>'description',  'placeholder' => 'Opis upoważnienia')) }}
    </div>
</div>
<div class="row">
  <div class="col-sm-12 marg-btm">
    Potwierdź wygenerowanie dokumentu.
  </div>
</div>