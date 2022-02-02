@extends('layouts.main')



@section('main')
    <div class="row">
        <form action="{{ URL::to('plan/groups/update', [$plan_group->id]) }}" method="post" role="form">
            {{ Form::token() }}
            <div class="col-sm-12 col-md-8 col-md-offset-2 ">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <a href="{{ URL::to('plan/groups/show', [$plan_group->id]) }}" class="btn btn-default btn-xs pull-left">
                            <i class="fa fa-ban fa-fw"></i>
                            anuluj
                        </a>
                        <strong>
                            Edycja grupy do {{ $plan_group->plan->name }}
                        </strong>
                        <button type="submit" class="btn btn-xs btn-primary pull-right">
                            <i class="fa fa-floppy-o fa-fw"></i>
                            zapisz grupę
                        </button>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>Nazwa:</label>
                                    {{ Form::text('name', $plan_group->name, array('class' => 'form-control required', 'placeholder' => 'nazwa programu'))  }}
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="form-group">
                                    <label>Kolejność:</label>
                                    {{ Form::number('ordering', $plan_group->ordering, array('class' => 'form-control required', 'placeholder' => 'kolejność'))  }}
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>List warunkowa</label>
                                    @foreach($companyGroups as $company_group)
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="company_group[]"
                                                       value="{{ $company_group->id }}"
                                                       @if($plan_group->company_groups->contains($company_group->id)) checked="checked" data-exist="true" @endif
                                                >
                                                {{ $company_group->name }}
                                                <span class="badge">{{ $company_group->companies->count() }}</span>
                                            </label>
                                            <label class="bg-danger marg-left remove_branches" data-group="{{ $company_group->id }}" style="display: none;">
                                                <input type="checkbox" name="remove_branches[]"
                                                       value="{{ $company_group->id }}"
                                                >
                                                usunąć serwisy z grupy
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
            $('[data-toggle="tooltip"]').tooltip();
            $('input[name="company_group[]"]').change(function() {
                var ischecked= $(this).is(':checked');
                var exist = $(this).data('exist');
                var company_group = $(this).val();
                if(!ischecked && exist) {
                    $('label.remove_branches[data-group="'+company_group+'"]').show();
                }else{
                    $('label.remove_branches[data-group="'+company_group+'"]').hide();
                    $('label.remove_branches[data-group="'+company_group+'"] input').prop("checked", false);
                }
            });
        })
    </script>
@stop

