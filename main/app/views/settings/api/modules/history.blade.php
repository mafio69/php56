@extends('layouts.main')


@section('header')
    Historia zapytań w module {{ $module->name }}
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="table-auto">
                <table class="table table-condensed table-hover" style="display: block;">
                    <thead>
                        <th >lp.</th>
                        <th >Zapytanie</th>
                        <th >Odpowiedź</th>
                        <th>Data odpytania</th>
                    </thead>
                    <?php $lp = (($histories->getCurrentPage()-1)*$histories->getPerPage()) + 1;?>
                    @foreach ($histories as $history)
                        <tr>
                            <td>{{$lp++}}. ({{ $history->id }})</td>
                            <Td>
                                <pre>
                                    {{$history->request}}
                                </pre>
                            </td>
                            <td>
                                <pre>
                                {{$history->response}}
                                </pre>
                            </td>
                            <td>
                                {{ substr($history->created_at, 0, -3) }}
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="pull-left" style="clear:both;">{{  $histories->links()  }}</div>
            </div>
        </div>
    </div>
@stop

@section('headerJs')
    @parent
    <script>
        $('pre').each(function(){
            var el = $(this);
            try {
                var json = JSON.parse(el.html());
                el.html(JSON.stringify(json, undefined, 2));
            }catch (e) {
                
            }
        });
    </script>
@endsection
