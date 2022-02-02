<div class="row">

  <div class="col-sm-12 marg-btm">
    @include('injuries.dialog.generate_documents_partials.branch_options')
    <label >Osoba upoważniona do reprezentrowania:</label>
    {{ Form::text('person', '', array('class' => 'form-control  ', 'id'=>'person',  'placeholder' => 'Osoba upoważniona do reprezentrowania')) }}
  </div>
</div>
<div class="row">
  <div class="col-sm-12 marg-btm">
    <label >Nr dowodu osobistego:</label>
    {{ Form::text('nr_id', '', array('class' => 'form-control  ', 'id'=>'nr_id',  'placeholder' => 'Nr dowodu osobistego')) }}
  </div>
</div>
<div class="row">
  <div class="col-sm-12 marg-btm">
    <label >Umiejscowienie samochodu:</label>
    {{ Form::text('car_location', '', array('class' => 'form-control  ', 'id'=>'car_location',  'placeholder' => 'Umiejscowienie samochodu')) }}
  </div>
</div>