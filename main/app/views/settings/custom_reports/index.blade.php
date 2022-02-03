@extends('layouts.main')

@section('header')

    Przypisywanie raportów do użytkowników

@stop

@section('main')

    <div >
        <table class="table table-auto  table-hover " id="users-table">
            <thead>
            <th >lp.</th>
            <th >Nazwa raportu</th>
            <th></th>
            </thead>
            @foreach ($reports as $k => $report)
                <tr class="odd gradeX">
                    <td>{{++$k}}.</td>
                    <Td>{{$report->desc}}</td>
                    <td>
                        <button target="{{ URL::route('settings.custom_reports', array('edit', $report->id)) }}" class="btn btn-info btn-sm modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i> edytuj przypisania</button>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>



@stop


@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('#modal').on('click', '#set', function(){
                $.ajax({
                    type: "POST",
                    url: $('#dialog-form').prop( 'action' ),
                    data: $('#dialog-form').serialize(),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        if(data.code == '0') location.reload();
                        else if(data.code == '1') self.location = data.url;
                        else{
                            $('#modal-sm .modal-body').html( data.error);
                            if(isset(data.url) && data.url != ''){
                                $('#modal').on('hidden.bs.modal', function (e) {
                                    self.location = data.url;
                                });
                            }
                            $('#set').attr('disabled',"disabled");
                        }
                    },
                    dataType: 'json'
                });
                return false;

            });
        });
    </script>

@stop