@extends('layouts.main')

@section('header')
    Dodawanie skrzynki
    <a href="{{ url('tasks/mailboxes') }}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-ban fa-fw"></i> anuluj
    </a>
@endsection

@section('main')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <form action="{{ URL::to('tasks/mailboxes/store') }}" class="page-form" method="post" id="page-form">
                        <div class="row marg-btm">
                            <div class="col-sm-12">
                                <h4 class="page-header marg-top-min">Dane skrzynki</h4>
                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Nazwa (opis):</label>
                                        {{  Form::text('name',  '', array('class' => 'form-control ', 'placeholder' => 'nazwa (opis)'))  }}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Serwer:</label>
                                        {{  Form::text('server',  '', array('class' => 'form-control required', 'required', 'placeholder' => 'serwer'))  }}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Login:</label>
                                        {{  Form::text('login', '', array('class' => 'form-control required', 'required', 'placeholder' => 'login')) }}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Hasło:</label>
                                        {{ Form::password('password', ['class'=> 'form-control required', 'required', 'placeholder' => 'hasło']) }}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Źródło w systemie</label>
                                        {{ Form::select('task_source_id', $taskSources, 1, ['class' => 'form-control required', 'required', 'readonly']) }}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Domyślna grupa zadań</label>
                                        <select name="task_group_id" class="form-control required" required>
                                            <option  selected="selected"  value="">
                                                --- wybierz ---
                                            </option>
                                            @foreach($taskGroups as $group)
                                                <option value="{{ $group->id }}">
                                                    {{ $group->name }}
                                                </option>
                                                @endforeach
                                                </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h4 class="page-header marg-top-min">Adresy mailowe</h4>
                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                <div class="row">
                                    <div class="col-md-6 col-md-offset-3">
                                        <div class="btn btn-primary btn-sm btn-block add-mail">
                                            <i class="fa fa-plus fa-fw"></i> dodaj
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="list-group marg-top" id="mails-container">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::token() }}
                        </div>
                        <div class="row marg-top">
                            <div class="col-sm-12">
                                <hr>
                            </div>
                            <div class="text-center col-md-8 col-md-offset-2" >
                                {{ Form::submit('Wprowadź',  array('id' => 'form_submit', 'class' => 'btn btn-primary btn-block off-disable', 'data-loading-text' => 'Trwa wprowadzanie...'))  }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('headerJs')
    @parent

    <script>
        $(document).ready(function(){
            $('.add-mail').on('click', function(){
                $.ajax({
                    type: "GET",
                    url: "{{ url('tasks/mailboxes/append-mail') }}",
                    data: {
                        task_group_id: $('select[name="task_group_id"]').val()
                    },
                    assync: false,
                    cache: false,
                    success: function (data) {
                        $('#mails-container').append(data);
                    },
                    dataType: 'text'
                });
            }).click();

            $( "form#page-form" ).submit(function(e) {
                let form_btn = $('#form_submit');
                form_btn.button('loading');

                if(! $('#page-form').valid()  ) {

                    setTimeout(function(){
                        form_btn.button('reset');
                    }, 100);

                    e.preventDefault();
                    return false;
                }
                return true;
            });

            $('#mails-container').on('click', '.remove', function (){
                $(this).parent().parent().remove();
            });
        });
    </script>
@endsection
