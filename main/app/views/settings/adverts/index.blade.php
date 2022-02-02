@extends('layouts.main')

@section('header')

Reklamy wyświetlane w aplikacji mobilnej

@stop

@section('main')
<div class="row">
    <div class="col-sm-12">
    @foreach($resolution_types as $resolution)
        <div class="panel panel-default pull-left" style="margin-left: 10px;">
            <div class="panel-heading overflow">
                <h3 class="panel-title pull-left">{{ $resolution->x_ax }} x {{ $resolution->y_ax }}</h3>
                <a style="margin-left: 10px;" href="{{ URL::route('settings.adverts.create', array($resolution->id)) }}" class="btn btn-primary btn-xs pull-right " >
                    <i class="glyphicon glyphicon-plus-sign"></i> reklamę
                </a>
            </div>
            <div class="panel-body">
                <table class="table table-auto  table-hover" >
                    @if(isset($advertsA[$resolution->id]) )
                        @foreach ($advertsA[$resolution->id] as $k => $advert)
                            <tr class="odd gradeX">
                                <td>{{++$k}}.</td>
                                <Td>
                                    {{ HTML::image('/file/uploads/mobile_adverts/'.$advert->file.'/thumb/') }}
                                </td>
                                <td>
                                    {{ $advert->url }}
                                </td>
                                <td>
                                    <button target="{{ URL::route('settings.adverts.delete', array($advert->id)) }}" class="btn btn-danger btn-sm modal-open marg-btm" data-toggle="modal" data-target="#modal">usuń</button>
                                    <br>
                                    <button target="{{ URL::route('settings.adverts.edit', array($advert->id)) }}" class="btn btn-warning btn-sm modal-open" data-toggle="modal" data-target="#modal">ustaw przekierowanie</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
    @endforeach
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>

@stop


@section('headerJs')
@parent
<script type="text/javascript">
    $(document).ready(function() {
        $('.modal-open').on('click',  function(){
            hrf=$(this).attr('target');
            $.get( hrf, function( data ) {
                $('#modal .modal-content').html(data);
            });
        });

        $('#modal').on('click', '#submit', function(){

            btn = $(this);
            btn.attr('disabled', 'disabled');

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
                        $('#modal .modal-body').html( data.error);
                    }
                },
                dataType: 'json'
            });
            return false;
        });
    });
</script>

@stop