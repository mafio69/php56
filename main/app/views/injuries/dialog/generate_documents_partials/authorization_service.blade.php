<div class="row">
    <div class="col-sm-12 marg-btm">
        @include('injuries.dialog.generate_documents_partials.branch_options')
        <div class="form-group">
            <label>Opis upoważnienia</label>
            {{ Form::text('description', '', ['class' => 'form-control']) }}
        </div>
    </div>
</div>
