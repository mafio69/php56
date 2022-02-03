@extends('layouts.main')

@section('header')
    Zlecenia (szkody) - wyszukiwanie globalne
    @include('injuries.menu-top')

@stop


@include('injuries.nav')

@section('main')
    @include('injuries.menu')

    <div id="injuries-container">
        @if(! $matchedLetters->isEmpty() )
            <button class="btn btn-info" style="margin-bottom:10px;" type="button" data-toggle="collapse"
                    data-target="#collapseSecond" aria-expanded="false" aria-controls="collapseSecond">
                <i class="fa fa-search" aria-hidden="true"></i> Wyświetl nieprzypisane pisma dopasowane do szukanej
                frazy <span class="badge">{{$matchedLetters->count()}}</span>
            </button>
            <div class="collapse" id="collapseSecond">
                <div class="panel panel-info" id="matched_letters_panel">
                    <div class="panel-heading">Nieprzypisane pisma dopasowane do szukanej frazy
                        <button type="button" class="close" data-target="#matched_letters_panel"
                                data-dismiss="alert" aria-hidden="true">&times;
                        </button>
                    </div>
                    <table class="table table-hover table-condensed">
                        <thead>
                        <th>lp.</th>
                        <th>typ dokumentu</th>
                        <th>tytuł pisma</th>
                        <th>nr szkody</th>
                        <th>nr umowy</th>
                        <th>nr rejestracyjny</th>
                        <th>data podpięcia</Th>
                        <th></Th>
                        <th></th>
                        </thead>
                        @foreach($matchedLetters as $k => $letter)
                            <tr>
                                <td>{{ ++$k }}</td>
                                <td>{{ Config::get('definition.fileCategory.'.$letter->category) }}</td>
                                <td>{{ checkIfEmpty($letter->name) }}</td>
                                <td>{{ checkIfEmpty($letter->injury_nr) }}</td>
                                <td>{{ checkIfEmpty($letter->nr_contract) }}</td>
                                <td>{{ checkIfEmpty($letter->registration) }}</td>
                                <td>{{ $letter->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if( trim($letter->description) != '')
                                        <a tabindex="0" class="btn btn-sm btn-info btn-popover" role="button"
                                           data-toggle="popover" data-trigger="focus" title="Opis pisma"
                                           data-content="{{ $letter->description }}"><i
                                                    class="fa fa-info-circle"></i> opis</a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]) }}"
                                       class="btn btn-sm btn-success " off-disable><i class="fa fa-download"></i>
                                        pobierz</a></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endif
        @if($unprocessed>0)
            <br/>
            <button class="btn btn-primary" style="margin-bottom:10px;" type="button" data-toggle="collapse"
                    data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-search"></i> Wyświetl nieprzetworzone szkody <span class="badge">{{$unprocessed}}</span>
            </button>
            <div class="collapse" id="collapseExample">
                <div class="well" id="unprocessed" style="padding-bottom:60px;position:relative; min-height:200px">
                    <div class="loader" style="position:absolute; color:#fff; background:rgba(100,100,100,0.7); left:0px;
                right:0px; padding-top:100px; text-align:center; diasplay:none; top:0; bottom:0; font-size:20px;">Trwa
                        ładowanie <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>
                </div>
            </div>
        @endif
        <table class="table table-hover table-condensed">
            <thead>
            <Th style="width:30px;">lp</th>
            <th></th>
            <th>nr sprawy</th>
            <th>samochód</th>
            <th>nr umowy</th>
            <th>rejestracja</th>
            <Th>właściciel</Th>
            <Th>nr szkody</th>
            <th>data zgłoszenia</th>
            <th>data i miejsce zdarzenia</th>
            <th>status</th>
            <th>upoważnienie</th>
            <th>przyjmujący</th>
            <th>prowadzący</th>
            <th></th>
            </thead>
            @foreach($injuries as $k => $injury)
                <tr>
                    <td>{{$k+1}}.</td>
                    <td>@include('injuries.includes.search-global_statuses')</td>
                    <td>
                        @if(Auth::user()->can('kartoteka_szkody#wejscie'))
                            <a type="button" class="btn btn-link" target="_blank" href="{{URL::route('injuries-info', array($injury->id))}}" >
                                {{$injury->case_nr}}
                            </a>
                        @else
                            {{$injury->case_nr}}
                        @endif
                    </td>
                    <td>{{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) }} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)  }}</td>
                    <td>{{ $injury->vehicle->nr_contract }}</td>
                    <Td>
                        @if(Auth::user()->can('kartoteka_szkody#wejscie'))
                            <a type="button" class="btn btn-link" href="{{ URL::route('injuries-info', array($injury->id)) }}" >{{$injury->vehicle->registration}}</a>
                        @else
                            {{$injury->vehicle->registration}}
                        @endif
                    </td>
                    <td>
                        <span class="tips"
                              title="{{ $injury->vehicle->owner->name }}">{{ $injury->vehicle->owner->short_name }}</span>
                    </td>
                    <td @if($injury->dsp_notification) class="bg-danger tips" title="zgłoszenie DSP" data-container="body" @endif>
                        @if($injury->injury_nr != null && $injury->injury_nr != '')
                            {{$injury->injury_nr}}
                        @else
                            ---
                        @endif
                    </td>
                    <td>
                        {{substr($injury->created_at, 0, -3)}}
                    </td>
                    <td>
                        {{$injury->event_city.' '.$injury->event_street.'
                        <br>
                        '.$injury->date_event}}
                    </td>
                    <td>
                        <b>{{ $injury->status->name }}</b>
                    </td>
                    <td>
                        @if ($injury->task_authorization == 0)
                            <i class="fa fa-exclamation md-ico task" orygin="fa-exclamation" task="task_authorization"
                               val="1" id_injury="{{$injury->id}}"></i>
                        @else
                            <i class="fa fa-check md-ico task" orygin="fa-check" task="task_authorization" val="0"
                               id_injury="{{$injury->id}}"></i>
                        @endif
                    </td>
                    <td>
                        {{ $injury->user->name }}
                    </td>
                    <td>
                        @if($injury->leader)
                            {{ $injury->leader->name }}
                        @endif
                    </td>

                    @include('injuries.includes.'.Config::get('definition.injuriesStepOptionsIncludes.'.$injury->step).'_options')
                </tr>
            @endforeach
        </table>
        @include('injuries.legend')
        <div class="pull-right" style="clear:both;">{{ $injuries->appends(Input::all())->links() }}</div>
    </div>


@stop

@section('headerJs')
    @parent
    <script type="text/javascript">

        $(document).ready(function () {

            $('#modal-lg').on('click', '#set-branch', function () {
                var btn = $(this);
                if ($('#id_warsztat').val() != '') {
                    btn.attr('disabled', 'disabled');
                    $.ajax({
                        type: "POST",
                        url: $('#assign-branch-form').prop('action'),
                        data: $('#assign-branch-form').serialize(),
                        assync: false,
                        cache: false,
                        success: function (data) {
                            if (data.code == '0') location.reload();
                            else if (data.code == '1') self.location = data.url;
                            else {
                                $('#modal-lg .modal-body').html(data.error);
                                $('#set-branch').attr('disabled', "disabled");
                            }
                        },
                        dataType: 'json'
                    });

                    return false;
                } else {
                    alert('Proszę przypisać serwis.');
                }

            });

            $('#modal-sm').on('click', '#set-injury', function () {
                var btn = $(this);
                btn.attr('disabled', 'disabled');
                $.ajax({
                    type: "POST",
                    url: $('#dialog-injury-form').prop('action'),
                    data: $('#dialog-injury-form').serialize(),
                    assync: false,
                    cache: false,
                    success: function (data) {
                        if (data.code == '0') location.reload();
                        else if (data.code == '1') self.location = data.url;
                        else {
                            $('#modal-sm .modal-body').html(data.error);
                            $('#set-injury').attr('disabled', "disabled");
                        }
                    },
                    dataType: 'json'
                });
                return false;
            });

            $('.task').click(function () {
                var task = $(this).attr('task');
                var val = $(this).attr('val');
                var id_injury = $(this).attr('id_injury');
                var element = $(this);

                $.ajax({
                    type: "POST",
                    url: "{{ URL::route('injuries-setTask') }}",
                    data: {task: task, val: val, '_token': $('input[name=_token]').val(), id_injury: id_injury},
                    assync: false,
                    cache: false,
                    beforeSend: function () {
                        element.hide();
                        element.after('<i class="fa fa-spinner fa-pulse loading-changes md-ico "></i>');
                    },
                    success: function () {
                        if (val == 1) {
                            element.attr('val', 0);
                            element.removeClass('fa-exclamation');
                            element.addClass('fa-check');
                        } else {
                            element.attr('val', 1);
                            element.removeClass('fa-check');
                            element.addClass('fa-exclamation');
                        }
                        element.parent().find('.loading-changes').remove();
                        element.show();
                    },
                    error: function () {
                        element.parent().find('.loading-changes').remove();
                        element.show();
                        $.notify({
                            icon: "fa fa-exclamation-triangle",
                            message: "Wystąpił błąd przy zmianie statusu zadania."
                        }, {
                            type: 'danger',
                            placement: {
                                from: 'bottom',
                                align: 'right'
                            },
                            delay: 2500,
                            timer: 500
                        });
                    },
                    dataType: 'json'
                });
            });

        });
        $('#unprocessed .loader').fadeIn();
        $.get(window.location.href.replace('global', 'global-unprocessed').replace(/page=[0-9]{1,}/i, 'page=1') + '&unprocessed=true', function (response) {
            $('#unprocessed').html(response);
            $('#unprocessed .loader').fadeOut();
        });
        $('#unprocessed').on('click', '#pagination_special a', function () {
            $('#unprocessed .loader').fadeIn();
            $.get($(this).attr('href'), function (response) {
                $('#unprocessed').html(response);
                $('#unprocessed .loader').fadeOut();
            });
            return false;
        })
    </script>

@stop
