<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie grupy stawek</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('insurances/groups/store') }}" method="post"  id="dialog-form">
            {{Form::token()}}
            {{ Form::hidden('user_id', Auth::user()->id) }}
            {{ Form::hidden('leasing_agreement_insurance_group_id', $group_id) }}
            <fieldset>
                <div class="form-group">
                    <label>Nazwa stawki</label>
                    <div class="input-group" id="rate_select"
                         @if(count($rates) == 0)
                             style="display: none;"
                         @endif
                    >
                        {{ Form::select('leasing_agreement_insurance_group_rate_id', $rates, null, ['class' => 'form-control'])}}
                        <span class="input-group-btn tips" title="dodaj stawkę spoza listy">
                            <button class="btn btn-primary" type="button" id="add_rate"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                    {{ Form::text('rate_name', '', array('class' => 'form-control required', 'id' => 'rate_name', 'style' => (count($rates)==0)?'':'display:none;', (count($rates)==0)?'':'disabled', 'placeholder' => 'nazwa stawki')) }}
                </div>
                <div class="form-group">
                    <label>12 m-ce</label>
                    {{ Form::text('months_12', '', array('class' => 'form-control required number currency_input', 'placeholder' => '12 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>24 m-ce</label>
                    {{ Form::text('months_24', '', array('class' => 'form-control required number currency_input', 'placeholder' => '24 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>36 m-ce</label>
                    {{ Form::text('months_36', '', array('class' => 'form-control required number currency_input', 'placeholder' => '36 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>48 m-ce</label>
                    {{ Form::text('months_48', '', array('class' => 'form-control required number currency_input', 'placeholder' => '48 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>60 m-ce</label>
                    {{ Form::text('months_60', '', array('class' => 'form-control required number currency_input', 'placeholder' => '60 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>72 m-ce</label>
                    {{ Form::text('months_72', '', array('class' => 'form-control required number currency_input', 'placeholder' => '72 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>84 m-ce</label>
                    {{ Form::text('months_84', '', array('class' => 'form-control required number currency_input', 'placeholder' => '84 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>96 m-ce</label>
                    {{ Form::text('months_96', '', array('class' => 'form-control required number currency_input', 'placeholder' => '96 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>108 m-ce</label>
                    {{ Form::text('months_108', '', array('class' => 'form-control required number currency_input', 'placeholder' => '108 m-ce')) }}
                </div>
                <div class="form-group">
                    <label>120 m-ce</label>
                    {{ Form::text('months_120', '', array('class' => 'form-control required number currency_input', 'placeholder' => '120 m-ce')) }}
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..." id="set">Wprowadź</button>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#add_rate').on('click',function(){
            $('#rate_select').hide();
            $('#rate_name').removeAttr('disabled').show().focus();
        });
    });
</script>