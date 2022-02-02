
<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">Wybór kategorii zdjęć</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to(url('tasks/attach-images')) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <input type="hidden" name="injury_id" value="{{$injury_id}}">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <h4>Wybierz typ dokumentu:</h4>
                    <div class="row">
                        @foreach($documentTypes as $documentType_id => $documentType)
                            <div class="col-md-4 ">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="fileType"  value="{{ $documentType_id }}">
                                        {{ $documentType }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-files">Wprowadź zdjęcia</button>
</div>
