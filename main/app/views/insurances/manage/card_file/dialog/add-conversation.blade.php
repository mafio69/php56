<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Tworzenie tematu/zadania</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/communicator/store', [$agreement_id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Tytuł tematu/zadania:</label>
                    {{ Form::text('topic', '', array('class' => 'form-control required', 'id'=>'topic',  'placeholder' => 'temat rozmowy')) }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Wiadomość:</label>
                    {{ Form::textarea('content', '', array('class' => 'form-control lead required', 'id'=>'content',  'placeholder' => 'treść wiadomości')) }}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer" style="margin-top:0px;">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Dodaj</button>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#content').wysihtml5({
            toolbar: {
                "font-styles": false,
                "html": true,
                'link': false,
                "image": false,
                "color": true,
                "blockquote": false
            },
            parserRules:    wysihtml5ParserRules,
            useLineBreaks:  false
        });
    });

</script>