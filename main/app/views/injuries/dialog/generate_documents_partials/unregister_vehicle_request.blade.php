<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Seria dowodu rejestracyjnego:</label>
        {{ Form::text('registration_document_series', '' , array('class' => 'form-control required',  'placeholder' => 'Seria dowodu rejestracyjnego', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Nr dowodu rejestracyjnego:</label>
        {{ Form::text('registration_document_number', '' , array('class' => 'form-control required',  'placeholder' => 'Nr dowodu rejestracyjnego', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Seria karty pojazdu:</label>
        {{ Form::text('vehicle_card_series', '' , array('class' => 'form-control required',  'placeholder' => 'Seria karty pojazdu', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Nr karty pojazdu:</label>
        {{ Form::text('vehicle_card_number', '' , array('class' => 'form-control required',  'placeholder' => 'Numer karty pojazdu', 'required')) }}
    </div>

    <div class="col-sm-12 marg-btm">
        <label >Oddział IdeaLeasing:</label>
        <select name="idea_office_id" class="form-control">
        @foreach($ideaOffices as $office)
            <option value="{{ $office->id }}">{{ $office->name }}, {{ $office->post }} {{ $office->city }} ul.{{ $office->street }}</option>
        @endforeach
        </select>
    </div>
</div>