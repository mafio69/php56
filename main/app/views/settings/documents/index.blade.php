@extends('layouts.main')

@section('header')

    Dokumenty generowane w DLS Pojazdy

@stop

@section('main')
    <style>
        .owner-block {
            border-left: 1px solid #dddddd;
        }

        th, tr {
            height: 100px;
        }

        #header_row2 {
            display: none;
            position: fixed;
            top: 55px;
            z-index: 11;
        }

        #header_row2 div:first-child {
            margin-left: 0px;
        }

        #header_row2 div {
            float: left;
            padding: 8px;
            height: 50px;
            background-color: #F0F0F0;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            border-right: 1px solid white;
        }

        .fixed-column {
            position: fixed;
            display: inline-block;
            width: auto;
            border-right: 1px solid #ddd;
            background: white;
            z-index: 10;
            margin-top: -10px;
        }
        #page-wrapper{padding: 0;}

        /* sticky columns & rows */
        table thead th {
            padding: 3px;
            position: sticky;
            top: 0;
            z-index: 1;
            width: 25vw;
            background: white;
        }
        table tbody th {
            font-weight: 100;
            font-style: italic;
            text-align: left;
            position: relative;
        }
        table thead th:first-child {
            position: sticky;
            left: 0;
            z-index: 2;
            width: 150px;
        }
        table thead th:nth-child(2) {
            position: sticky;
            left: 40px;
            z-index: 2;
            /* width: 150px; */
        }
        table thead th:nth-child(3) {
            position: sticky;
            left: 175px;
            z-index: 2;
            /* width: 150px; */
        }
        table tbody th:first-child {
            position: sticky;
            left: 0;
            background: white;
            z-index: 1;
        }
        table tbody th:nth-child(2) {
            position: sticky;
            left: 40px;
            background: white;
            z-index: 1;
        }
        table tbody th:nth-child(3) {
            position: sticky;
            left: 175px;
            background: white;
            z-index: 1;
        }
        .wrapper {
            width: 100%;
            max-height: 98vh;
            /* overflow: auto; */
        }
        .wrapper:focus {
            box-shadow: 0 0 0.5em rgba(0, 0, 0, 0.5);
            outline: 0;
        }
    </style>

    <div class="row">
        <div class="col-sm-12 table_list">
            <div class="view">
                <div class="wrapper">
                    <div id="header_row2" style="display:none;">
                        <div>lp.</div>
                        <div>Nazwa dokumentu</div>
                        <div>Obsługiwane grupy
                            <span class="btn btn-xs btn-info modal-open-lg" target="{{ URL::route('settings.documents', array('groupsInfo')) }}" data-toggle="modal" data-target="#modal-lg">
                                <i class="fa fa-info"></i>
                            </span>
                        </div>
                        <div >CFM</div>
                        @foreach($steps as $step)
                            <div>{{ $step }}</div>
                        @endforeach
                        <div></div>
                    </div>
                    <table class="table table-hover" id="table_list" style="width: {{ (count($steps) * 200) + 650 }}px;">
                        <thead id="header_row">
                            <th width="35">lp.</th>
                            <th width="300">Nazwa dokumentu</th>
                            <th width="100">Obsługiwane grupy
                                <span class="btn btn-xs btn-info modal-open-lg" target="{{ URL::route('settings.documents', array('groupsInfo')) }}" data-toggle="modal" data-target="#modal-lg">
                                    <i class="fa fa-info"></i>
                                </span>
                            </th>
                            <th width="50">CFM</th>
                            @foreach($steps as $step)
                                <th width="100">{{ $step }}</th>
                            @endforeach
                            <th width="50"></th>
                        </thead>
                        <tbody>
                        @foreach ($documentsTypes as $k => $document)
                            <tr>
                                <th width="35">{{$document->id}}.</th>
                                <th width="300" class="owner-block">
                                    {{$document->name}}
                                    <span class="btn btn-xs btn-warning modal-open" style="font-size: 10px;"
                                        target="{{ URL::route('settings.documents', array('editInjuriesDocumentName', $document->id)) }}"
                                        data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-pencil fa-fw"></i>
                                        edytuj
                                    </span>
                                    <a href="{{ URL::route('settings.documents', array('templateInjuriesDoc', $document->id)) }}"
                                    style="font-size: 10px;" target="_blank" class="btn btn-xs btn-info off-disable">
                                        <i class="fa fa-print fa-fw"></i> podgląd
                                    </a>
                                </th>
                                <th width="200" class="owner-block">
                                    @foreach($document->ownersGroups as $ownersGroup)
                                        {{ $ownersGroup->name }};
                                    @endforeach
                                </th>
                                <td width="50" class="owner-block">
                                    @if($document->cfm == 1)
                                        <i class="fa fa-check"></i>
                                    @elseif($document->cfm == 0)
                                        <i class="fa fa-minus"></i>
                                    @else
                                        <i class="fa fa-check"></i> / <i class="fa fa-minus"></i>
                                    @endif
                                </td>
                                @foreach($steps as $step_id => $step)
                                    <td width="200" class="owner-block">
                                        @if($document->steps->contains($step_id))
                                            <i class="fa fa-check"></i>
                                        @else
                                            <i class="fa fa-minus"></i>
                                        @endif
                                    </td>
                                @endforeach
                                <td width="50" class="owner-block">
                                    <span class="btn btn-xs btn-warning modal-open-lg" target="{{ URL::route('settings.documents', array('editInjuries', $document->id)) }}" data-toggle="modal" data-target="#modal-lg">
                                        <i class="fa fa-pencil fa-fw"></i>
                                        edytuj
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>





@stop


@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            // var $table = $('#table_list');
            // var $fixedColumn = $table.clone().removeAttr('id').removeAttr('style').insertBefore($table).addClass('fixed-column');
            //
            // $fixedColumn.find('th:not(:nth-child(1),:nth-child(2),:nth-child(3)),td:not(:nth-child(1),:nth-child(2),:nth-child(3))').remove();
            //
            // $fixedColumn.find('tr').each(function (i, elem) {
            //     $(this).height(100);
            // });

            // $(window).scroll(function () {
            //     var left = -Math.abs($(this).scrollLeft());
            //     $("#header_row2").css("left", left + "px");
            // });
            // var table_width = $("#table_list").width();
            // var top = $("#header_row").position().top;
            // var glued = false;
            // var top_offset = $("#table_list").offset().top;

            // $("#header_row2").width(table_width + 1);
            // $("body").width(table_width + 42);
           
            // $(window).scroll(function () {
            //     var scroll = $(this).scrollTop();

            //     $('.fixed-column').css('top', (top_offset - scroll + 10) + 'px');

            //     if (!glued && scroll > top) {
            //         glued = true;
            //         $('.fixed-column').hide();
            //         $("#header_row2").show();
            //         $("#table_list #header_row th").each(function(i, e) {
            //             var w = $(this).width();

            //             $("#header_row2 div:nth-child("+(i+1)+")").width( (w - 1) );
            //         });
            //     } else if(scroll <= top) {
            //         glued = false;
            //         $('.fixed-column').show();
            //         $("#header_row2").hide();
            //     }
            // });
        });
    </script>

@stop