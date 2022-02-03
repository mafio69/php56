
<div class="panel panel-default">
    <div class="panel-heading">
        {{ $name }}
        <a href="{{ url('tasks/types/edit-users', [$type->id]) }}" class="btn btn-xs btn-warning pull-right">
            <i class="fa fa-pencil fa-fw"></i> edytuj
        </a>
    </div>
    <table class="table table-condensed table-hover">
        <thead>
            <th>#</th>
            <th>Imię i Nazwisko</th>
            <th>Email</th>
        </thead>
        @foreach($users as $k => $user)
            <tr>
                <td>{{ ++$k }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
            </tr>
        @endforeach
    </table>
</div>