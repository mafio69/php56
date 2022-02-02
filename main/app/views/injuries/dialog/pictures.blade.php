<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zdjęcia przesłane przez zgłaszającego</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <div class="row marg-top-min">
        <div class="col-sm-12">
            <?php foreach ($pictures as $k => $v) {?>
                <div class="col-sm-6 col-md-4" id="image-{{$v->id}}">
                    <div class="thumbnail">
                        <div class="image-container">
                            <a href="/file/uploads/mobile_images/{{$v->file}}/full" data-lightbox="image-before" >
                                {{ HTML::image('/file/uploads/mobile_images/'.$v->file.'/thumb') }}
                            </a>
                        </div>
                    </div>
                </div>
            <?php }?>
        </div>
        <div class="col-sm-12">
            {{ Form::open(array('url' => '/injuries/unprocessed/download-images/'.$v->mobile_injury_id)) }}
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fa fa-fw fa-floppy-o"></i>
                    pobierz zdjęcia
                </button>
            {{ Form::close() }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>