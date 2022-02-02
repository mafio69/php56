@extends('layouts.main')

@section('header')
    Zadania nowe
@stop

@section('main')
    @include('tasks.nav')
    <div class="panel panel-default">
    <table class="table table-hover  table-condensed">
        <thead>
        <Th style="width:30px;">lp.</th>
        <th></th>
        <th>nr sprawy</th>
        <th>źródło</th>
        <th>grupa</th>
        <th>nadawca wiadomości</th>
        <th>temat wiadomości</th>
        <th>data przydzielenia</th>
        <th>data wpływu</th>
        @if(Auth::user()->can('wykaz_zadan#osoba_przypisana'))
        <th>osoba przypisana</th>
        @endif
        </thead>
        <?php $lp = (($tasks->getCurrentPage()-1)*$tasks->getPerPage()) + 1;?>
        @foreach ($tasks as $task)
            <tr class="vertical-middle"
            @if( $task->id == Session::get('tasks.card'))
                style="background-color: honeydew;"
            @endif
            >
                <td>{{$lp++}}.</td>
                <td>
                    <span class="btn btn-xs btn-block btn-primary task-collect" data-task="{{ $task->currentInstance->id }}">
                        <i class="fa fa-check fa-fw"></i> Pobierz sprawę
                    </span>
                </td>
                <td>
                    <a href="{{ url('tasks/list/show', [$task->id]) }}" class="btn btn-info btn-xs">
                        <i class="fa fa-eye fa-fw"></i>
                        {{ $task->case_nb }}
                    </a>
                </td>
                <td>{{ $task->sourceType->name }}</td>
                <td>{{ $task->group ? $task->group->name : '' }}</td>
                <td>{{ $task->from_name }} {{ $task->from_email ? '('.$task->from_email.')' : '' }}</td>
                <td>{{ $task->subject }}</td>
                <td>{{ $task->currentInstance->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $task->task_date ? $task->task_date->format('Y-m-d H:i') : '' }}</td>
                @if(Auth::user()->can('wykaz_zadan#osoba_przypisana'))
                <td>{{ $task->currentInstance->user->name }}</td>
                @endif
            </tr>
        @endforeach
    </table>
    <div class="pull-right" style="clear:both;">{{ $tasks->appends(Input::query())->links() }}</div>
    </div>
@stop

@section('headerJs')
    @parent
    <script>
        $(document).on('click', '.task-collect', function(){
            let taskId = $(this).data('task');

            $.ajax({
                type: "POST",
                url: "{{ url('tasks/collect') }}",
                data: {
                    _token: $('input[name="_token"]').val(),
                    task_id: taskId
                },
                assync: false,
                cache: false,
                success: function (data) {
                    location.reload();
                },
                dataType: 'text'
            });
        });
    </script>
@endsection
