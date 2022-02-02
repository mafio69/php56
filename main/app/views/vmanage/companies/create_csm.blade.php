<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie nowego typu informacji</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::action('VmanageCompaniesController@postCreateCsm', [$company->id]) }}" method="post"  id="dialog-form">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>Nazwa typu</label>
                    {{ Form::text('name', '', array('class' => 'form-control required', 'placeholder' => 'nazwa typu ')) }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    {{ Form::textarea('content', '', array('class' => 'form-control lead required', 'id'=>'content',  'placeholder' => 'treść ')) }}
                </div>
            </div>
            {{Form::token()}}
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa dodawanie" id="set">Dodaj</button>
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