<table class="table table-hover table-condensed" data-sort-name="{{ $col_name }}"
       data-sort-order="asc">
    <thead>
        <th>
            <input type="checkbox" name="checkAll" value="1">
        </th>
        <th data-sortable="true" data-field="module_id">Moduł</th>
        <th data-sortable="true" data-field="path">Ścieżka</th>
        <th data-sortable="true" data-field="name">Nazwa</th>
    </thead>
    @foreach($permissions as $permission)
        <tr @if($group->permissions->contains($permission->id)) class="info" @endif>
            <td>
                <input type="checkbox" value="{{ $permission->id }}" class="row-checkbox" name="package[]"
                    @if($group->permissions->contains($permission->id))
                        data-was="1"
                       {{--checked="checked"--}}
                    @else
                        data-was="0"
                    @endif
                >
            </td>
            <td>{{ $permission->module->name }}</td>
            <td>{{ $permission->path }}</td>
            <td>{{ $permission->name }}</td>
        </tr>
    @endforeach
</table>
