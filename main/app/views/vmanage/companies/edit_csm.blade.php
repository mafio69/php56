<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja infomarcji do <i>{{ $csm_type->name }}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::action('VmanageCompaniesController@postEditCsm', [$company->id, $csm_type->id]) }}" method="post"  id="dialog-form">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    {{ Form::textarea('content', $content, array('class' => 'form-control lead required', 'id'=>'content',  'placeholder' => 'treść ')) }}
                </div>
            </div>
            {{Form::token()}}
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie" id="set">Zapisz zmiany</button>
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