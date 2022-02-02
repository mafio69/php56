@extends('layouts.main')

@section('header')
    Generowanie raportów
@stop

@section('main')
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <table class="table table-hover" >
            @foreach($reports as $report)
                <tr>
                    <td >
                        <strong>{{ $report->desc }}</strong>
                    </td>
                    <td>
                        <p class="btn btn-primary btn-sm modal-open generate_doc"
                           target="{{ URL::route('reports.custom.get', array('generate',$report->id)) }}" data-toggle="modal" data-target="#modal" >
                            <i class="fa fa-file-text-o"></i><span> generuj raport</span>
                        </p>
                    </td>
                </tr>
            @endforeach
            </table>
        </div>
    </div>


@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            $('#modal').on('click', '#generate-document', function(){
                var btn = $(this);
                btn.attr('disabled', 'disabled');

                $('#gen-report-form').validate();

                if($('#gen-report-form').valid()) {
                    self.location =  $('#gen-report-form').prop('action') + '?' + $('#gen-report-form').serialize();
                }else{
                    btn.removeAttr('disabled');
                }
                return false;
            });
        });
    </script>
@stop

