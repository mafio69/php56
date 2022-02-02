<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Historia cesji</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <div class="panel-group" id="accordion-cessions" role="tablist" aria-multiselectable="true">
    @foreach($cessions as $cession)
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="heading{{ $cession->id }}">
                <h5 class="panel-title pointer" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $cession->id }}" aria-expanded="true" aria-controls="collapseOne">
                    {{ $cession->name }} <small>{{ substr($cession->pivot->created_at, 0, -3) }}</small>
                    <i class="fa fa-arrows-v pull-right"></i>
                </h5>
            </div>
            <div id="collapse{{ $cession->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $cession->id }}">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-hover table-condensed">
                                <tr class="active">
                                    <Td colspan="2">
                                        <span class="sm-title">Dane rejestrowe:</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Nazwa:</label></td>
                                    <td>{{ $cession->name }}</td>
                                </tr>
                                <tr>
                                    <td><label>NIP:</label></td>
                                    <td>{{ $cession->NIP }}</td>
                                </tr>
                                <tr>
                                    <td><label>REGON:</label></td>
                                    <td>{{ $cession->REGON }}</td>
                                </tr>
                                <tr class="active">
                                    <Td colspan="2"><span class="sm-title">Adres rejestrowy:</span></td>
                                </tr>
                                <tr>
                                    <td><label>Kod pocztowy:</label></td>
                                    <td>{{ $cession->registry_post }}</td>
                                </tr>
                                <tr>
                                    <td><label>Miato:</label></td>
                                    <td>{{ $cession->registry_city }}</td>
                                </tr>
                                <tr>
                                    <td><label>Ulica:</label></td>
                                    <td>{{ $cession->registry_street }}</td>
                                </tr>
                                <tr class="active">
                                    <Td colspan="2">
                                        <span class="sm-title">Adres kontaktowy:</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Kod pocztowy:</label></td>
                                    <td>{{ $cession->correspond_post }}</td>
                                </tr>
                                <tr>
                                    <td><label>Miato:</label></td>
                                    <td>{{ $cession->correspond_city }}</td>
                                </tr>
                                <tr>
                                    <td><label>Ulica:</label></td>
                                    <td>{{ $cession->correspond_street }}</td>
                                </tr>
                                <tr class="active">
                                    <Td colspan="2">
                                        <span class="sm-title">Dane kontaktowe:</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Telefon:</label></td>
                                    <td>{{ $cession->phone }}</td>
                                </tr>
                                <tr>
                                    <td><label>Email:</label></td>
                                    <td>{{ $cession->email }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>
