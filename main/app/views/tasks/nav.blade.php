<div class="row">
    @if(Auth::user()->can('wykaz_zadan#osoba_przypisana'))
    <div class="col-sm-12">
        <nav class="navbar navbar-default navbar-sm " >
            <div class="container-fluid">
                <form  role="search" id="search-form" action="{{ Request::getBasePath() }}" method="get">
                    <div class="navbar-form navbar-left">
                        <div class="form-group form-group-sm text-center  marg-top-min">
                            <a class="btn btn-xs btn-danger" href="{{ Request::getPathInfo() }}">
                                <i class="fa fa-remove fa-fw"></i> usuń filtry
                            </a>
                        </div>
                        <div class="form-group form-group-sm marg-top-min">
                            <div class="divider">|</div>
                        </div>
                        @if(Auth::user()->can('wykaz_zadan#osoba_przypisana'))
                        <div class="form-group form-group-sm marg-top-min">
                            <label class="text-center">Osoba przypisana:</label>
                        </div>
                        <div class="form-group form-group-sm marg-top-min">
                            {{ Form::select('task_user_id', $taskUsers, Input::get('task_user_id'), ['class' => 'form-control']) }}
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </nav>
    </div>
    @endif
    <div class="col-sm-10">
        <ul class="nav nav-pills marg-btm">
            <li role="presentation" @if(Request::segment(3) == 'unassigned') class="active" @endif >
                <a href="{{ url('tasks/list/unassigned') }}" >
                    Nieprzydzielone
                    <span class="badge">
                        {{ \Idea\Tasker\Tasker::globalStats()['unassigned'] }}
                    </span>
                </a>
            </li>
            <li role="presentation" @if(Request::segment(3) == 'new') class="active" @endif >
                <a href="{{ url('tasks/list/new') }}" >
                    Nowe
                    <span class="badge">
                        {{ \Idea\Tasker\Tasker::globalStats()['new'] }}
                    </span>
                </a>
            </li>
            <li role="presentation" @if(Request::segment(3) == 'inprogress') class="active" @endif >
                <a href="{{ url('tasks/list/inprogress') }}" >
                    W realizacji
                    <span class="badge">
                        {{ \Idea\Tasker\Tasker::globalStats()['inprogress'] }}
                    </span>
                </a>
            </li>
            <li role="presentation" @if(Request::segment(3) == 'complete') class="active" @endif >
                <a href="{{ url('tasks/list/complete') }}" >
                    Zakończone
                    <span class="badge">
                        {{ \Idea\Tasker\Tasker::globalStats()['complete'] }}
                    </span>
                </a>
            </li>
            <li role="presentation" @if(Request::segment(3) == 'complete-without-action') class="active" @endif >
                <a href="{{ url('tasks/list/complete-without-action') }}" >
                    Zakończone bez czynności
                    <span class="badge">
                        {{ \Idea\Tasker\Tasker::globalStats()['complete-without-action'] }}
                    </span>
                </a>
            </li>
        </ul>
    </div>
    <div class="col-sm-2">
        <div class="search-box">
            <div class="pull-right">
                @if(Input::has('term'))
                    <span class="label label-primary pull-right " style="margin-left:10px; font-size:14px;">
                      {{ Input::get('term') }}
                  </span>
                @endif
                <i class="fa fa-search show-search font-xlarge  pull-right " ></i>
            </div>

            <div class="panel panel-default search-adv">
                <div class="panel-heading">
                    <h4 class="panel-title">Filtrowanie zadań</h4>
                </div>
                <div class="panel-body">
                    <div class="form-group" style="margin-bottom:0px;">
                        {{ Form::open(array('url' => Request::url(), 'method' => 'GET', 'id' => 'search-adv-form')) }}
                        <div class="row ">
                            <div class="col-sm-12 marg-btm">
                                <input class="form-control" name="term" placeholder="wprowadź szukaną frazę"
                                       @if(Input::has('term'))
                                       value ="{{ Input::get('term') }}"
                                        @endif
                                >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 marg-btm">
                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="case_nb" value="1"
                                               @if(Input::has('case_nb'))
                                               checked
                                                @endif
                                        >nr sprawy
                                    </label>
                                </div>
                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="task_from" value="1"
                                               @if(Input::has('task_from'))
                                               checked
                                                @endif
                                        >nadawca wiadomości
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="task_subject" value="1"
                                               @if(Input::has('task_subject'))
                                               checked
                                                @endif
                                        >temat wiadomości
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="task_content" value="1"
                                               @if(Input::has('task_content'))
                                               checked
                                                @endif
                                        >treść wiadomości
                                    </label>
                                </div>


                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="global" value="1"
                                               @if(Input::has('global'))
                                               checked
                                                @endif
                                        >wyszukaj globalnie
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-10 col-sm-offset-1">
                                <button class="btn btn-primary btn-sm pull-right" type="submit" id="search-adv" style="width:100%"> <i class="fa fa-search"></i> szukaj </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('#search-form select').on('change', function () {
                if( $('#search-form').valid() ) {
                    $('#search-form').submit();
                }
            });

            $('#search-form input').on('keyup keypress', function (e) {
                if(e.which === 13){
                    if( $('#search-form').valid() ) {
                        $('#search-form').submit();
                    }
                }
            });

            $('.btn-check input').change(function(){
                if( $(this).prop('checked') ){
                    $(this).closest('.btn').addClass('active');
                }else{
                    $(this).closest('.btn').removeClass('active');
                }
            }).change();


            $('input[name="search_term"]').bind("keypress", function(e) {
                if( e.which == 13 ){
                    $('#search-adv-form').submit();
                }
            });

            $('#search-adv-form').on('submit', function (e){
                if( $('input[name="global"]').prop('checked') ){
                    $('#search-adv-form').attr('action', "{{ url('/tasks/list/global') }}");
                }

                return true;
            });

            if(window.localStorage.getItem('tasks-search-mode') == 1){
                $('.search-adv').show();
                $('input[name="global"]').prop('checked', true).parent().addClass('active');

                document.addEventListener('selectionchange', () => {
                    let selection = getSelectedTextWithin(document.getElementById('tasks-container'));
                    if(selection.length > 0){
                        $('.search-adv input[name="term"]').val(selection);
                    }
                });
            }
        });

        function getSelectedTextWithin(el) {
            var selectedText = "";
            if (typeof window.getSelection != "undefined") {
                var sel = window.getSelection(), rangeCount;
                if ( (rangeCount = sel.rangeCount) > 0 ) {
                    var range = document.createRange();
                    for (var i = 0, selRange; i < rangeCount; ++i) {
                        range.selectNodeContents(el);
                        selRange = sel.getRangeAt(i);
                        if (selRange.compareBoundaryPoints(range.START_TO_END, range) == 1 && selRange.compareBoundaryPoints(range.END_TO_START, range) == -1) {
                            if (selRange.compareBoundaryPoints(range.START_TO_START, range) == 1) {
                                range.setStart(selRange.startContainer, selRange.startOffset);
                            }
                            if (selRange.compareBoundaryPoints(range.END_TO_END, range) == -1) {
                                range.setEnd(selRange.endContainer, selRange.endOffset);
                            }
                            selectedText += range.toString();
                        }
                    }
                }
            } else if (typeof document.selection != "undefined" && document.selection.type == "Text") {
                var selTextRange = document.selection.createRange();
                var textRange = selTextRange.duplicate();
                textRange.moveToElementText(el);
                if (selTextRange.compareEndPoints("EndToStart", textRange) == 1 && selTextRange.compareEndPoints("StartToEnd", textRange) == -1) {
                    if (selTextRange.compareEndPoints("StartToStart", textRange) == 1) {
                        textRange.setEndPoint("StartToStart", selTextRange);
                    }
                    if (selTextRange.compareEndPoints("EndToEnd", textRange) == -1) {
                        textRange.setEndPoint("EndToEnd", selTextRange);
                    }
                    selectedText = textRange.text;
                }
            }
            return selectedText;
        }
    </script>
@stop

