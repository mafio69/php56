<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Data na dokumencie potwierdzenia naprawy:</label>
        {{ Form::text('date_document_confirmation', date('Y-m-d') , array('class' => 'form-control required', 'id'=>'date_submit',  'placeholder' => 'Data na dokumencie potwierdzenia naprawy', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Email do potwierdzenia naprawy:</label>
        {{ Form::text('email_document_confirmation','' , array('class' => 'form-control email mail required', 'id'=>'email_document_confirmation',  'placeholder' => 'Email do potwierdzenia naprawy', 'required')) }}
    </div>

</div>