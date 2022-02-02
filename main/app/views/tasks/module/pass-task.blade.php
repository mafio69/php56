<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przekazanie zadania</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('tasks/change-task-group') }}" method="post"  id="dialog-form">
        {{Form::token()}}
        {{ Form::hidden('task_instance_id', $taskInstance->id) }}

        <div class="form-group">
            <label>Wskaż nowy dział:</label>
            {{ Form::select('task_group_id', $groups, null, ['class' => 'form-control', 'required']) }}
        </div>
        <div class="form-group" id="modal-users-list-container" style="display: none;">
            <label>Wybierz osobę:</label>
            <div id="task-users-list">
                {{ Form::select('user_id', [], null, ['class' => 'form-control' ]) }}
            </div>
        </div>

        <div id="identification-container" class="panel panel-default" style="display: none;">
            <div class="panel-body">
                <div class="form-group">
                    <label>Identyfikacja sprawy:</label>
                    {{ Form::text('term', null, ['class'=> 'form-control']) }}
                </div>
                <div class="form-group">
                    <label class="marg-right">Parametr:</label>
                    <label class="radio-inline">
                        <input type="radio" name="term_parameter" value="registration"> nr rej.
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="term_parameter" value="nr_contract"> nr umowy
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="term_parameter" value="injury_nr"> nr szkody
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="term_parameter" value="case_nr"> nr kartoteki
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Opis</label>
            {{ Form::text('description', null, ['class' => 'form-control']) }}
        </div>

        <div>

        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Potwierdź</button>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
<style>
    .bootstrap-select.btn-group .dropdown-menu li.disabled a {
        cursor: not-allowed;
        background: #5cb85d;
        color: white;
    }
    .bootstrap-select.btn-group .dropdown-menu li.disabled:before {
        content: 'nieobecność';
        display: block;
        text-align: center;
        background: #5cb85d;
        color: white;
        font-size: 10px;
        border-bottom: 1px solid rgba(23, 47, 24, 0.6);
    }
    .bootstrap-select.btn-group .dropdown-menu li.disabled
    {
        border: 1px solid #ccc;
        border-radius: 4px;
    }
</style>
<script>
    $(document).ready(function() {
        $('.modal-body select[name="task_group_id"]').on('change', function(){
            let task_group_id = $('.modal-body select[name="task_group_id"] option:selected').val();

            $.ajax({
                type: "GET",
                url: '{{ url('tasks/load-task-users') }}',
                data: {
                    task_group_id: task_group_id
                },
                assync: false,
                cache: false,
                dataType: 'text',
                success: function(data){
                    $('.modal-body #task-users-list').html(data);
                    $('.modal-body .selectpicker').selectpicker({width:'100%'});
                }
            });

            if(parseInt( task_group_id ) === 3 || parseInt( task_group_id ) === 5 ){
                $('.modal-body #identification-container').show();
                $('.modal-body #modal-users-list-container').show();
            }else{
                $('.modal-body #identification-container').hide();
                $('.modal-body #modal-users-list-container').hide();
            }
        }).change();
    });
</script>