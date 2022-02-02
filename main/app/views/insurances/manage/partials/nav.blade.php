
@section('leftNav')
    @parent
    <div class=" l-menu-content">
        <table id="l-menu-show">
            <tr>
                <td>
                    <?php $exist_filter = 0;?>
                    @if(Session::get('search.yachts_filter', '0') != 0)
                        <i class="fa fa-ship sm-ico" style="font-size: 15px;"></i>
                        <?php $exist_filter = 1;?>
                    @endif

                    @if(Session::get('search.foreign_policy', '0') != 0)
                        <i class="fa fa-code-fork sm-ico" style="font-size: 15px;"></i>
                        <?php $exist_filter = 1;?>
                    @endif

                    @if(Session::get('search.insurance_company_filter', '') != '')
                        <i class="fa fa-check-square-o sm-ico"></i>
                        <?php $exist_filter = 1;?>
                    @endif

                    @if($exist_filter == 0)
                        <i class="fa fa-angle-right"></i>
                    @endif
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
                <label>Liczba zgłoszeń na stronie:</label>
                <select class="form-control input-sm  search-el" id="s-pagin" name="pagin">
                    <option value="10"
                    @if(Session::get('search.pagin', '10') == 10)
                            selected
                            @endif
                            >10</option>
                    <option value="15"
                    @if(Session::get('search.pagin', '10') == 15)
                            selected
                            @endif
                            >15</option>
                    <option value="20"
                    @if(Session::get('search.pagin', '10') == 20)
                            selected
                            @endif
                            >20</option>
                    <option value="25"
                    @if(Session::get('search.pagin', '10') == 25)
                            selected
                            @endif
                            >25</option>
                    <option value="30"
                    @if(Session::get('search.pagin', '10') == 30)
                            selected
                            @endif
                            >30</option>
                </select>
            </div>
            <div class="cbp-search-para">
                <label>
                    <i class="fa fa-ship sm-ico marg-right"></i> filtruj tylko jachty
                    <input class="search-el" type="checkbox" name="yachts_filter" value="1"
                           @if(Session::get('search.yachts_filter', '0') == 1)
                           checked="checked"
                            @endif
                            >
                </label>
            </div>
            <div class="cbp-search-para">
                <label>
                    <i class="fa fa-code-fork sm-ico marg-right"></i> zawierające polisy obce
                    <input class="search-el" type="checkbox" name="foreign_policy" value="1"
                           @if(Session::get('search.foreign_policy', '0') == 1)
                           checked="checked"
                            @endif
                    >
                </label>
            </div>
            <div class="cbp-search-para">
                <label>Filtruj po ubezpieczycielu</label>
                {{ Form::text('insurance_company_filter', Session::get('search.insurance_company_filter', ''), ['class' => 'form-control input-sm  search-el', 'placeholder' => 'szukana nazwa ubezpieczyciela']) }}
            </div>
        </nav>
    </form>
@stop






