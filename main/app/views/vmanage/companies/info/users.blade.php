<div class="tab-pane fade in" id="users">
    <div class="row">
            <div class="col-sm-12 col-md-8 col-md-offset-2 item-m">
                <div class="alert alert-info" role="alert">
                    <b>Aktualny użytkownik pojazdu:</b>
                        {{ $vehicle->user->name }} {{ $vehicle->user->surname }}
                        @if($vehicle->user->phone)
                            , telefon: {{ $vehicle->user->phone }}
                        @endif
                        @if($vehicle->user->email)
                            , email: <a href="mailto:{{ $vehicle->user->email }}">{{ $vehicle->user->email }}</a>
                        @endif
                </div>
                <div class="panel panel-default small">
                    <div class="panel-heading overflow ">
                        <h4 class="panel-title">
                            Historia użytkowników pojazdu
                            <span class="btn btn-primary btn-xs marg-left pull-right modal-open" data-toggle="modal" data-target="#modal"
                                      target="{{ URL::action('VmanageVehicleInfoController@getAssignUser', [$vehicle->id]) }}" >
                                <span class="fa fa-exchange"></span> zmień użytkownika pojazdu
                            </span>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="col-sm-12 ">
                            <table class="table table-hover table-condensed">
                                <thead>
                                    <th>lp.</th>
                                    <th>imię</th>
                                    <th>nazwisko</th>
                                    <th>telefon</th>
                                    <th>email</th>
                                    <th>użytkował od</th>
                                </thead>
                                @foreach($vehicle->users as $k => $user)
                                    <tr>
                                        <td>{{ ++$k }}.</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->surname }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->toDateString() }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
