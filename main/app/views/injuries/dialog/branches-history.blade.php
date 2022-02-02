<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Historia serwisów</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <table class="table table-condensed table-hover">
        <thead>
            <th>#</th>
            <th>nazwa</th>
            <th>adres</th>
            <th>telefon</th>
            <th>data przypisania</th>
        </thead>
        @foreach($injury->branches as $k => $branch)
            <tr>
                <td>{{ ++$k }}.</td>
                <td>{{ $branch->branch->short_name }}</td>
                <td>{{ $branch->branch->code }} {{$branch->branch->city}}, {{$branch->branch->street}}</td>
                <td>{{ $branch->branch->phone }}</td>
                <td>{{ $branch->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>
