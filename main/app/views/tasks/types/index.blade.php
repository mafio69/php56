@extends('layouts.main')

@section('header')
    Zestawienie grup zadań
@stop

@section('main')
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-body">
                <ul class="media-list">
                    @foreach ($types as $type_group_id => $typeGroups)
                        <li class="media">
                            <div class="media-left">

                            </div>
                            <div class="media-body">
                                <p class="bg-primary media-heading " style="padding: 10px 15px;">
                                    <span target="{{ url('tasks/types/users-type-group', [$type_group_id]) }}" class="btn btn-default btn-xs load-users off-disable">
                                        <i class="fa fa-users fa-fw"></i>
                                        <span class="badge">
                                            {{
                                                User::whereHas('taskGroups' , function($query)use($type_group_id){
                                                        $query->where('id', $type_group_id);
                                                    })->count()
                                            }}
                                        </span>
                                    </span>
                                     {{ $taskGroups[$type_group_id] }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6" id="users-list-container">

        </div>
    </div>
@stop

@section('headerJs')
    @parent
    <script>
        $(document).ready(function (){
            $('.load-users').on('click', function() {
                let target = $(this).attr('target');
                $.ajax({
                    type: "GET",
                    url: target,
                    assync: false,
                    cache: false,
                    dataType: 'text',
                    success: function (data) {
                        $('#users-list-container').html(data);
                    }
                });
            });

            let container = $("#users-list-container");
            let container_offset = container.offset().top;
            $(document).on("scroll", function(e) {
                let scroll = window.pageYOffset || document.documentElement.scrollTop;
                if (scroll > container_offset) {
                    container.addClass("fixed-position fixed-right");
                } else {
                    container.removeClass("fixed-position fixed-right");
                }

            });
        });
    </script>
@endsection

