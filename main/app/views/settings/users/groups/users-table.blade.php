<table class="table table-hover table-condensed" data-sort-name="name"
       data-sort-order="asc">
    <thead>
        <th>
            <input type="checkbox" name="checkAll" value="1">
        </th>
        <th data-sortable="true" data-field="login">Login</th>
        <th data-sortable="true" data-field="email">Email</th>
        <th data-sortable="true" data-field="name">Nazwisko</th>
    </thead>
    @foreach($users as $user)
        <tr @if($group->users->contains($user->id)) class="info" @endif>
            <td>
                <input type="checkbox" value="{{ $user->id }}" class="row-checkbox" name="package[]"
                    @if($group->users->contains($user->id))
                        data-was="1"
                       {{--checked="checked"--}}
                    @else
                        data-was="0"
                    @endif
                >
            </td>
            <td>{{ $user->login }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->name }}</td>
        </tr>
    @endforeach
</table>
