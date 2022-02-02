<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie ubezpieczyciela do grup</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('insurances/groups/add-insurance-company-to-groups') }}" method="post"  id="dialog-form">
            {{Form::token()}}
            <fieldset>
                <div class="form-group">
                    <label>Wybierz ubezpieczyciela z istniejących w systemie:</label>
                    {{ Form::select('insurance_company_id', $insuranceCompanies, 0, ['class' => 'form-control'])}}
                </div>
                <hr/>
                <div class="form-group marg-top">
                    <label>Wprowadź nowego ubezpieczyciela:</label>
                </div>
                <div class="form-group">
                    <label>Nazwa</label>
                    {{ Form::text('name', '', array('class' => 'form-control', 'placeholder' => 'nazwa ubezpieczyciela')) }}
                </div>
                <div class="form-group col-sm-4">
                    <label>Ulica</label>
                    {{ Form::text('street', '', array('class' => 'form-control ', 'placeholder' => 'ulica')) }}
                </div>
                <div class="form-group col-sm-4">
                    <label>Kod pocztowy</label>
                    {{ Form::text('post', '', array('class' => 'form-control ', 'placeholder' => 'kod pocztowy')) }}
                </div>
                <div class="form-group col-sm-4">
                    <label>Miasto</label>
                    {{ Form::text('city', '', array('class' => 'form-control ', 'placeholder' => 'miasto')) }}
                </div>
                <div class="form-group">
                    <label>Osoba kontaktowa</label>
                    {{ Form::text('contact_person', '', array('class' => 'form-control ', 'placeholder' => 'Osoba kontaktowa')) }}
                </div>
                <div class="form-group col-sm-6">
                    <label>Email</label>
                    {{ Form::text('email', '', array('class' => 'form-control email', 'placeholder' => 'email')) }}
                </div>
                <div class="form-group col-sm-6">
                    <label>Telefon</label>
                    {{ Form::text('phone', '', array('class' => 'form-control ', 'placeholder' => 'telefon')) }}
                </div>

            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..." id="set">Wprowadź</button>
</div>
