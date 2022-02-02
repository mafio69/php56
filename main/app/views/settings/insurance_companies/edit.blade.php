<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja ubezpieczalni</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('insurance_companies-set', array($insurance_company->id)) }}" method="post"  id="dialog-form">
            
            <fieldset>
                <div class="form-group">
                    <label>Nazwa:</label>
                    {{ Form::text('name', $insurance_company->name, array('class' => 'form-control required', 'placeholder' => 'nazwa', 'autofocuse' => ''))  }}                                
                </div>
                <div class="form-group">
                    <label>Kod pocztowy:</label>
                    {{ Form::text('post', $insurance_company->post, array('class' => 'form-control ', 'placeholder' => 'kod pocztowy', ))  }}
                </div> 
                <div class="form-group">
                    <label>Miasto:</label>
                    {{ Form::text('city', $insurance_company->city, array('class' => 'form-control ', 'placeholder' => 'miasto', ))  }}
                </div>               
                <div class="form-group">
                    <label>Ulica:</label>
                    {{ Form::text('street', $insurance_company->street, array('class' => 'form-control ', 'placeholder' => 'ulica', ))  }}
                </div>
                <div class="form-group">
                    <label>Osoba kontaktowa:</label>
                    {{ Form::text('contact_person', $insurance_company->contact_person, array('class' => 'form-control ', 'placeholder' => 'osoba kontaktowa', ))  }}                                
                </div>
                <div class="form-group">
                    <label>Adresy email (oddzielone przecinkiem):</label>
                    {{ Form::textarea('email', $insurance_company->email, array('class' => 'form-control ', 'placeholder' => 'email (oddzielone przecinkiem)', ))  }}
                </div>
                <div class="form-group">
                    <label>Telefon:</label>
                    {{ Form::text('phone', $insurance_company->phone, array('class' => 'form-control ', 'placeholder' => 'telefon', ))  }}
                </div>
                <div class="checkbox text-center">
                    <label>
                        <input type="checkbox" name="if_rounding" value="1" @if($insurance_company->if_rounding==1) checked @endif> Zaokrąglaj kwoty składek
                    </label>
                </div>
                <div class="checkbox text-center">
                    <label>
                        <input type="checkbox" name="if_full_year" value="1" @if($insurance_company->if_full_year==1) checked @endif> Zaokrąglaj składki do pełnych lat
                    </label>
                </div>
                {{Form::token()}}	
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz zmiany</button>
</div>