<div class="split excluded-container" id="tasks-container" @if(! Session::get('task.visibility', false)) style="display: none;" @endif>
    <div class="row">
        <div class="col-sm-12">
            <div id="task-loader" class="spinner"></div>

            <ul class="nav nav-tabs">
                <li data-tab="new" role="presentation" class=" @if(Session::get('task.section', 'new') == 'new') active @endif tab"><a href="#">Nowe <span class="badge">{{ \Idea\Tasker\Tasker::stats()['new'] }}</span></a></li>
                <li data-tab="inprogress" role="presentation" class=" @if(Session::get('task.section', 'new') == 'inprogress') active  @endif tab"><a href="#">W realizacji <span class="badge">{{ \Idea\Tasker\Tasker::stats()['inprogress'] }}</span></a></li>
                <li data-tab="complete" role="presentation" class=" @if(Session::get('task.section', 'new') == 'complete') active @endif tab"><a href="#">Zakończone <span class="badge">{{ \Idea\Tasker\Tasker::stats()['complete'] }}</span></a></li>
                <li role="presentation" class="pull-right" id="tasks-switch-search">
                    <a href="#">
                        <i class="fa fa-search fa-fw"></i>
                    </a>
                </li>
                <li role="presentation" class="pull-right" id="tasks-switch-direction">
                    <a href="#">
                        <i class="fa fa-columns" aria-hidden="true"></i>
                    </a>
                </li>
            </ul>
            <div id="task-tab-content">
                @if(Session::get('task.section', 'new') == 'new')
                    @include('tasks.module.tab-new')
                @elseif(Session::get('task.section', 'new') == 'inprogress')
                    @include('tasks.module.tab-inprogress')
                @elseif(Session::get('task.section', 'new') == 'complete')
                    @include('tasks.module.tab-complete')
                @elseif(Session::get('task.section', 'new') == 'details')
                    @include('tasks.module.task', ['taskInstance' => \Idea\Tasker\Tasker::taskInstance(Session::get('task.id'))])
                @elseif(Session::get('task.section', 'new') == 'inside-preview')
                    @include('tasks.module.inside-preview', ['file' => TaskFile::find(Session::get('task.file'))])
                @endif
            </div>
        </div>
    </div>
</div>

@section('headerJs')
    @parent

    <script src="{{ asset("js/split.min.js")}}"></script>
    <script>
        function tasksSwitchVisibility()
        {
            $.ajax({
                type: "POST",
                url: "{{ url('tasks/switch-visibility') }}/",
                data: {
                    _token: $('input[name="_token"]').val()
                },
                assync: false,
                cache: false
            });
        }
        let split = null;

        function reloadSplit()
        {
            let sizes = localStorage.getItem('split-sizes');

            if (sizes) {
                sizes = JSON.parse(sizes)
            } else {
                sizes = [30, 70] // default sizes
            }

            let direction = localStorage.getItem('split-direction')
            if(! direction){
                direction = 'horizontal'
            }
            let cursor;

            if(direction == 'horizontal'){
                cursor = 'col-resize';
                $('.split').css('float', 'left');
                $('#split-container').removeClass('split-vertical');
                $('#tasks-switch-direction').addClass('active');
            }else{
                cursor = 'row-resize';
                $('.split').css('float', 'none');
                $('#split-container').addClass('split-vertical');
                $('#tasks-switch-direction').removeClass('active');
            }

            if(split) split.destroy();

            split = Split(['#tasks-container', '#page-wrapper'], {
                sizes: sizes,
                direction: direction,
                cursor: cursor,
                onDragEnd: function(sizes) {
                    localStorage.setItem('split-sizes', JSON.stringify(sizes))
                },
            });
        }
        $(document).ready(function () {

            if ($('#tasks-container').is(":visible")) {
                $('#tasks-container').show();

                reloadSplit();
            }

            $(document).on('click', '#show-tasks', function () {
                if (!$('#tasks-container').is(":visible")) {
                    $('#tasks-container').show();
                    reloadSplit();
                } else {
                    $('#tasks-container').hide();
                    $('.split').css('float', 'none');

                    if(split) {
                        split.destroy();
                        split = null;
                    }
                }

                $(this).toggleClass('btn-open')

                tasksSwitchVisibility();
            });

            $(document).on('click', '#task-collect', function(){
                let taskId = $(this).data('task');

                $.ajax({
                    type: "POST",
                    url: "{{ url('tasks/collect') }}",
                    data: {
                        _token: $('input[name="_token"]').val(),
                        task_id: taskId
                    },
                    beforeSend: function() {
                        $('#task-loader').show();
                    },
                    assync: false,
                    cache: false,
                    success: function (data) {
                        $('#task-loader').hide();
                        $('#task-tab-content').html(data);
                    },
                    dataType: 'text'
                });
            });

            $(document).on('click', '.task-show-details', function(){
                let taskId = $(this).data('task');

                $.ajax({
                    type: "GET",
                    url: "{{ url('tasks/show') }}/"+taskId,
                    assync: false,
                    cache: false,
                    beforeSend: function() {
                        $('#task-loader').show();
                    },
                    success: function (data) {
                        $('#task-loader').hide();
                        if (!$('#tasks-container').is(":visible")) {
                            $('#tasks-container').show();
                            reloadSplit();
                            tasksSwitchVisibility();
                        }

                        $('#task-tab-content').html(data);
                    },
                    dataType: 'text'
                });
            });

            $(document).on('click', '#tasks-container .nav li.tab, #tasks-container .switch-tab', function () {
                let tab = $(this).data('tab');
                $.ajax({
                    type: "GET",
                    url: "{{ url('tasks/switch-tab-content') }}",
                    data: {
                        tab: tab
                    },
                    beforeSend: function() {
                        $('#task-loader').show();
                    },
                    assync: false,
                    cache: false,
                    success: function (data) {
                        $('#task-loader').hide();
                        $('#task-tab-content').html(data);
                        $('#tasks-container .nav li.tab').removeClass('active');
                        $('#tasks-container .nav li.tab[data-tab="'+tab+'"]').addClass('active');
                    },
                    dataType: 'text'
                });
            });

            $('#tasks-switch-search').on('click', function (e){
                e.preventDefault();
                e.stopPropagation();

                if( window.localStorage.getItem('tasks-search-mode') == 1 ){
                    $('#tasks-switch-search').removeClass('active');
                    window.localStorage.setItem('tasks-search-mode', 0);
                    window.location.href = "{{ url('injuries/new') }}";
                }else{
                    $('#tasks-switch-search').addClass('active');
                    window.localStorage.setItem('tasks-search-mode', 1);
                    window.location.href = "{{ url('tasks/list/new') }}";
                }
            });

            if(window.localStorage.getItem('tasks-search-mode') == 1){
                $('#tasks-switch-search').addClass('active');
            }

            $('#tasks-switch-direction').on('click', function (){
                let direction = localStorage.getItem('split-direction')

                if(direction == 'horizontal'){
                    localStorage.setItem('split-direction', 'vertical')
                }else{
                    localStorage.setItem('split-direction', 'horizontal')
                }

                reloadSplit()
            });

            $(document).on('click', '.task-toggle-file-preview', function (){
                let file_id = $(this).data('file');

                if (!$('#tasks-container').is(":visible")) {

                    $('#tasks-container').show();
                    reloadSplit();
                    $('#show-tasks').addClass('btn-open');
                }

                $.ajax({
                    type: "GET",
                    url: "{{ url('tasks/inside-preview') }}/"+file_id,
                    assync: false,
                    cache: false,
                    beforeSend: function() {
                        $('#task-loader').show();
                    },
                    success: function (data) {
                        $('#task-loader').hide();
                        $('#task-tab-content').html(data);
                    },
                    dataType: 'text'
                });

                $('#modal-lg').modal('hide');
            });
        });
    </script>
@endsection