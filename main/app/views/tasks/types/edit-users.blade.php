@extends('layouts.main')

@section('header')
    Edycja pracowników obsługujących grupy {{ $type->name }}
@stop

@section('main')
    <form action="{{ url('tasks/types/update-users', [$type->id]) }}" method="post"  id="form">
        {{Form::token()}}
        <div class="row">
            <div class="col-lg-6 col-lg-offset-2">
                <div class="panel panel-default">
                    <table class="table table-condensed table-hover" id="search-table">
                        <thead>
                            <th>
                                <input type="checkbox" name="checkAll" value="1">
                            </th>
                            <th>Imię i nazwisko</th>
                            <th>Email</th>
                        </thead>
                        @foreach($users as $user)
                            <tr  @if($type->users->contains($user->id)) class="info" @endif>
                                <td>
                                    <input type="checkbox" value="{{ $user->id }}" class="row-checkbox " name="package[]"
                                           @if($type->users->contains($user->id))
                                           data-was="1" checked
                                           @else
                                           data-was="0"
                                           @endif
                                    >
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-lg-2">
                <a href="{{ url('tasks/types') }}" class="btn btn-default btn-block">
                    <i class="fa fa-ban fa-fw"></i>
                    Anuluj
                </a>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa fa-fw fa-floppy-o"></i>
                    Zapisz
                </button>
            </div>
        </div>
    </form>
@stop

@section('headerJs')
    @parent
    <script>
        $(document).ready(function(){

            $('#search-table').on('change','input[name="checkAll"]', function(){
                if($(this).is(':checked'))
                {
                    $('.row-checkbox').prop('checked', true).change();
                }else{
                    $('.row-checkbox').prop('checked', false).change();
                }
            });
            $('#search-table').on('change', '.row-checkbox', function(){
                if( ! $(this).is(':checked'))
                {
                    $('input[name="checkAll"]').prop('checked', false);
                }

                var was = $(this).data('was');

                var permission_id = $(this).val();
                $(this).parent().parent().removeClass();

                if($(this).is(":checked")){
                    if(was == '1')
                    {
                        $(this).parent().parent().addClass('info');
                    }else{
                        $(this).parent().parent().addClass('success');
                    }

                    if(!$('.hidden-permissions :input[value="'+permission_id+'"]').length) {
                        $('.hidden-permissions').append('<input type="hidden" name="permissions[]" value="' + permission_id + '"/>')
                    }
                }else{
                    if(was == '1') {
                        $(this).parent().parent().addClass('danger');
                    }

                    $('.hidden-permissions :input[value="'+permission_id+'"]').remove();
                }
            });
        });
    </script>
@endsection

