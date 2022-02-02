<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie komentarza</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('tasks/store-comment') }}" method="post"  id="dialog-form">
        {{Form::token()}}
        {{ Form::hidden('task_id', $task->id) }}

        <div class="form-group">
            {{ Form::textarea('content', '', array('class' => 'form-control required', 'id'=>'content',  'placeholder' => 'treść wiadomości')) }}
        </div>

    </form>
</div>
<div class="modal-footer">
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