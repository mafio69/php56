@extends('layouts.main')



@section('main')
    <div class="row">
        <form action="{{ URL::to('plan/groups/store') }}" method="post" role="form">
            {{ Form::token() }}
            {{ Form::hidden('plan_id', $plan->id) }}
            <div class="col-sm-12 col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <a href="{{ URL::to('plans') }}" class="btn btn-default btn-xs pull-left">
                            <i class="fa fa-ban fa-fw"></i>
                            anuluj
                        </a>
                        <strong>
                            Dodawanie grupy do {{ $plan->name }}
                        </strong>
                        <button type="submit" class="btn btn-xs btn-primary pull-right">
                            <i class="fa fa-floppy-o fa-fw"></i>
                            zapisz grupę
                        </button>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6 col-lg-6">
                                <div class="form-group">
                                    <label>Nazwa:</label>
                                    {{ Form::text('name', '', array('class' => 'form-control required', 'placeholder' => 'nazwa programu'))  }}
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="form-group">
                                    <label>Kolejność:</label>
                                    {{ Form::number('ordering', $ordering, array('class' => 'form-control required', 'placeholder' => 'kolejność'))  }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>List warunkowa</label>
                                    @foreach($companyGroups as $company_group)
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="company_group[]" value="{{ $company_group->id }}">
                                                {{ $company_group->name }}
                                                <span class="badge">{{ $company_group->companies->count() }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('headerJs')
    @parent
    <script type="text/javascript" >
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@stop

