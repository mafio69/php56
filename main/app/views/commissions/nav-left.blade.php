
@section('leftNav')
    @parent
    <div class=" l-menu-content">
        <table id="l-menu-show">
            <tr>
                <td>
                    <i class="fa fa-angle-right"></i>
                </td>
            </tr>
        </table>
    </div>
@stop

@section('leftNavContent')
    @parent
    <form method="post" id="search-form" action="{{ URL::route('session.setSearch') }}" >
        {{Form::token()}}
        <nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="l-menu">
            <div class="cbp-search-para">
                <label>Liczba wierszy na stronie:</label>
                <select class="form-control input-sm  search-el" id="s-pagin" name="pagin">
                    @for($i = 10; $i <= 100; $i+=10)
                        <option value="{{ $i }}"
                                @if(Session::get('search.pagin', 10) == $i)
                                selected
                                @endif
                        >{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </nav>
    </form>
@stop






