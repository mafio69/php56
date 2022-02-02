
<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">Wybór kategorii dokumentów</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to(url('tasks/attach-files')) }}" method="post" id="dialog-form">
        {{Form::token()}}
        <input type="hidden" name="injury_id" value="{{$injury_id}}">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <h4>Wybierz typ dokumentu:</h4>
                    <div class="row">
                        @foreach($documentTypes as $documentType)
                            @if(Auth::user()->can('kartoteka_szkody#dokumentacja#dodaj_usun_dokument_'.$documentType->id))
                                <div class="col-md-4 ">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="fileType" @if($documentType->subtypes->count() == 0) class="required" @else class="has-subtypes" @endif value="{{ $documentType->id }}">
                                            {{ $documentType->name }}
                                        </label>
                                    </div>
                                </div>
                                @if($documentType->subtypes->count() > 0)
                                    <div class="col-sm-12 subtypes-container" data-subtypes="{{ $documentType->id }}" style="display: none;">
                                        <hr>
                                        <div class="row">
                                            @foreach($documentType->subtypes as $subtype)
                                                @if(Auth::user()->can('kartoteka_szkody#dokumentacja#dodaj_usun_dokument_'.$subtype->id))
                                                    <div class="col-md-4">
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" class="required" name="fileSubType" value="{{ $subtype->id }}">
                                                                {{ $subtype->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <hr>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" id="content-container">
                    <label >Opis dokumentów:</label>
                    {{ Form::text('content', '', array('class' => 'form-control ', 'placeholder' => 'opis dokumentów')) }}
                </div>
                <div class="col-sm-12 amount-container" data-id="23" style="display: none">
                    <label>Kwota zaległości:</label>
                    {{ Form::text('amount', '', array('class' => 'form-control required number currency_input', 'placeholder' => 'kwota zaległości')) }}
                </div>
                <div class="col-sm-12 amount-container" data-id="48" style="display: none">
                    <label>Kwota wypłaty:</label>
                    {{ Form::text('amount', '', array('class' => 'form-control required number currency_input', 'placeholder' => 'kwota wypłaty')) }}
                </div>
            </div>
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-files">Wprowadź dokumenty</button>
</div>
<script>
    $('input[name="fileType"]').on('change', function(){
        var fileType = $(this).val();
        console.log($('.amount-container[data-id="'+fileType+'"]').length , fileType)
        if ($('.amount-container[data-id="'+fileType+'"]').length > 0) {
            $('#content-container').hide();
            $('.amount-container').hide();
            $('.amount-container input').prop('disabled', true);
            $('.amount-container[data-id="'+fileType+'"]').show();
            $('.amount-container[data-id="'+fileType+'"] input').prop('disabled', false);
        }else{
            $('#content-container').show();
            $('.amount-container').hide();
            $('.amount-container input').prop('disabled', true);

        }

        $('.subtypes-container').hide();

        if($(this).hasClass('has-subtypes')){
            $('.subtypes-container[data-subtypes="'+fileType+'"]').show();
        }
    });
</script>
