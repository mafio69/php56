@extends('layouts.main')


@section('header')

Szablony wiadomości SMS

<div class="pull-right">
    <button id ="create" target="{{ URL::route('settings.sms-templates.create') }}" class="btn btn-small btn-primary" data-toggle="modal" data-target="#modal"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj szablon</button>
</div>

@stop

@section('main')
<div class="row" >
    <div class="col-sm-10 col-md-8 col-lg-6">
        <div class="table-responsive">
            <table class="table  table-hover" id="table">
                <thead>
                    <th>lp.</th>
                    <th >nazwa szablonu</th>
                    <th ></th>
                </thead>
                @foreach ($templates as $lp => $template)
                    <tr >
                        <td width="20px">{{++$lp}}.</td>
                        <Td>
                            {{$template->name}}
                        </td>
                        <td>
                            <button target="{{ URL::route('settings.sms-templates.show', array($template->id)) }}" class="btn btn-primary btn-sm modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-search"></i></button>
                            <button target="{{ URL::route('settings.sms-templates.edit', array($template->id)) }}" class="btn btn-warning btn-sm modal-open" data-toggle="modal" data-target="#modal">edytuj</button>
                            <button target="{{ URL::route('settings.sms-templates.delete', array($template->id)) }}" class="btn btn-danger btn-sm modal-open" data-toggle="modal" data-target="#modal">usuń</button>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>


<!--  modal -->
<div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
        </div>
    </div>
</div>


@stop

@section('headerJs')
@parent
<script type="text/javascript">
    $(document).ready(function() {

        $('#table').on('click', '.modal-open', function(){
            var hrf=$(this).attr('target');
            $.get( hrf, function( data ) {
                $('#modal .modal-content').html(data);
            });
        });

        $('#create').on('click', function(){
            var hrf=$(this).attr('target');
            $.get( hrf, function( data ) {
                $('#modal .modal-content').html(data);
            });
        });

        $('#modal').on('click', '#submit', function(){
            $('#dialog-form').validate();
            var btn = $(this);
            btn.attr('disabled', 'disabled');

            if($('#dialog-form').valid() ) {
                $.ajax({
                    type: "POST",
                    url: $('#dialog-form').prop('action'),
                    data: $('#dialog-form').serialize(),
                    assync: false,
                    cache: false,
                    success: function (data) {
                        if (data.code == '0') location.reload();
                        else {
                            $('#modal .modal-body').html(data.error);
                            btn.attr('disabled', "disabled");
                        }
                    },
                    dataType: 'json'
                });
            }else{
                btn.removeAttr('disabled');
            }
            return false;

        });







    });

</script>

@stop

