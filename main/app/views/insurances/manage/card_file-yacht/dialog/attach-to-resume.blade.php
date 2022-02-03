<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Podpinanie polisy pod wznowienie</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/info-insurances/attach-to-resume', [$insurance->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <p><b>Wybierz polisę pod którą chcesz podpiąć wznowienie:</b></p>

        <table class="table table-condensed table-hover marg-top">
            <tr>
                <th>lp</th>
                <th>Nr polisy</th>
                <th>Data polisy</th>
                <th>Polisa od</th>
                <th>Polisa do</th>
                <th>Składka leasingobiorcy</th>
                @foreach($coveragesTypes as $k => $name)
                    <th></th>
                @endforeach
                <th class="text-center"><em>wybierz</em></th>
            </tr>
            @foreach($insurance_to_attach as $k => $insurance)
                @if(!$insurance->refundInsurance)
                <tr>
                    <td>{{ ++$k }}.</td>
                    <td>{{ $insurance->insurance_number }}</td>
                    <td>{{ $insurance->insurance_date }}</td>
                    <td>{{ $insurance->date_from }}</td>
                    <td>{{ $insurance->date_to }}</td>
                    <td>{{ $insurance->contribution_lessor }} zł</td>
                    @foreach($insurance->coverages as $coverage_lp => $coverage)
                        <td>
                            {{ $coverage->type->name }} <i class="fa fa-check"></i>
                        </td>
                    @endforeach
                    @for($i = $insurance->coverages->count(); $i < count($coveragesTypes); $i++)
                        <td></td>
                    @endfor
                    <td class="text-center">
                        <label>
                            <input type="radio" name="insurance_to_attach" value="{{ $insurance->id }}">
                        </label>
                    </td>
                </tr>
                @endif
            @endforeach
        </table>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary " data-loading-text="trwa przetwarzanie..." id="set">Zapisz</button>
</div>
