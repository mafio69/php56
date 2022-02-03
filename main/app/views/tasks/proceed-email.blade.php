@extends('layouts.main')

@section('header')
    Wgrywanie zadania
    <a href="{{ URL::previous() }}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-ban fa-fw"></i> anuluj
    </a>
@endsection

@section('main')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <form action="{{ URL::to('tasks/store-email') }}" class="page-form" method="post" id="page-form">
                        {{ Form::hidden('filename', $filename) }}
                        <div class="row marg-btm">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <label>Wskaż grupę zadań</label>
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
