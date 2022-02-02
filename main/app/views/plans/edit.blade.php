@extends('layouts.main')



@section('main')
<div class="row">
    <form action="{{ URL::to('plans/update', [$plan->id]) }}" method="post" role="form">
        {{ Form::token() }}
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading text-center">
                    <a href="{{ URL::to('plans') }}" class="btn btn-default btn-xs pull-left">
                        <i class="fa fa-ban fa-fw"></i>
                        anuluj
                    </a>
                    <strong>
                        Edycja programu
                    </strong>
                    <button type="submit" class="btn btn-xs btn-primary pull-right">
                        <i class="fa fa-floppy-o fa-fw"></i>
                        zapisz program
                    </button>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Nazwa:</label>
                                {{ Form::text('name', $plan->name, array('class' => 'form-control required', 'placeholder' => 'nazwa programu'))  }}
                            </div>
                        </div>
                        <div class="col-sm-6 ">
                            <div class="form-group">
                                <label>Kod programu sprzedażowego:</label>
                                {{ Form::select('sales_program', $sales_program, $plan->sales_program, ['class' => 'form-control required']) }}
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

