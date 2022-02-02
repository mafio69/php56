<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przekazanie zadania {{$absence_user->name}}</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ url('tasks/excludes/distribute-single', [$absence_user->id, $task_instance_id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}

        Potwierdź rozdanie zadańia pracownika {{ $absence_user->name }}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Potwierdź</button>
</div>
