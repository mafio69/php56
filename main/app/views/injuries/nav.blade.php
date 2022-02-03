

	@section('leftNav')
        @parent
        <div class="l-menu-content left-nav-element">
            <table id="l-menu-show" style="width: 26px;">
                <tr>
                    <td>
                        <?php $exist_filter = 0;?>
                        @if(Session::get('search.injury_type', '0') != 0)
                            <i class="fa fa-filter sm-ico"></i>
                            <?php $exist_filter = 1;?>
                        @endif
                        @if(Session::get('search.user_id', '0') != 0)
                            <i class="fa fa-user sm-ico"></i>
                            <?php $exist_filter = 1;?>
                        @endif
						@if(Session::get('search.leader_id', '0') != 0)
							<i class="fa fa-user sm-ico"></i>
							<?php $exist_filter = 1;?>
						@endif
                        @if(Session::get('search.company_type', '0') != 0)
                            <i class="fa fa-wrench sm-ico"></i>
                            <?php $exist_filter = 1;?>
                        @endif
                        @if(Session::get('search.locked_status', '0') != 0)
                            <i class="fa fa-lock sm-ico"></i>
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
				<h3>Filtrowanie zgłoszeń</h3>
				<div class="cbp-search-para">
					<label><i class="fa fa-filter sm-ico"></i> Typ szkody:</label>
					<select class="form-control input-sm search-el" id="s-type" name="injury_type">
						<option value="0"
							@if(Session::get('search.injury_type', '0') == 0)
							selected
							@endif
						> ---- wszystkie --- </option>
					<?php foreach ($injuries_type as $k => $type){?>
						<option value="<?php echo $type->id;?>"
							@if(Session::get('search.injury_type', '0') == $type->id )
							selected
							@endif
						><?php echo $type->name;?></option>
					<?php }?>
					</select>
				</div>
				<div class="cbp-search-para">
					<label><i class="fa fa-user sm-ico"></i> Osoba zgłaszająca:</label>
					<select class="form-control input-sm  search-el" id="s-user" name="user_id">
						<option value="0"
							@if(Session::get('search.user_id', '0') == 0)
							selected
							@endif
						> ---- wszyscy --- </option>
					<?php foreach ($users as $k => $user){?>
						<option value="<?php echo $user->id;?>"
							@if(Session::get('search.user_id', '0') == $user->id )
							selected
							@endif
						><?php echo $user->name;?></option>
					<?php }?>
					</select>
				</div>
				<div class="cbp-search-para">
					<label><i class="fa fa-user sm-ico"></i> Osoba prowadząca:</label>
					<select class="form-control input-sm  search-el" id="s-leader" name="leader_id">
						<option value="0"
								@if(Session::get('search.leader_id', '0') == 0)
								selected
								@endif
						> ---- wszyscy --- </option>
						@foreach (User::where('login', '!=', 'default')->orderBy('name')->get() as $k => $user)
							<option value="<?php echo $user->id;?>"
									@if(Session::get('search.leader_id', '0') == $user->id )
									selected
									@endif
							><?php echo $user->name;?></option>
						@endforeach
					</select>
				</div>
				<div class="cbp-search-para">
					<label><i class="fa fa-wrench sm-ico"></i> Obsługujący serwis:</label>
					<select class="form-control input-sm  search-el" id="s-company" name="company_type">
						<option value="0"
							@if(Session::get('search.company_type', '0') == 0)
							selected
							@endif
						> ---- wszystkie --- </option>
						<option value="-1" @if(Session::get('search.company_type', '0') == '-1') selected @endif>nieprzypisany do grup</option>
                        @foreach($company_group_list as $company_group_id => $company_group_name)
                        	<option value="{{ $company_group_id }}"
                                @if(Session::get('search.company_type', '0') == $company_group_id)
                                selected
                                @endif
							>{{ $company_group_name }}</option>
                        @endforeach

					</select>
				</div>
				<div class="cbp-search-para">
					<label>
						<i class="fa fa-lock sm-ico"></i> pokaż blokowane
						<input class="search-el" type="checkbox" id="s-locked" name="locked_status" value="1"
						@if(Session::get('search.locked_status', '0') == 1)
						checked="checked"
						@endif
						>
					</label>
				</div>
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
						<a href="{{ url('injuries/reset-filters') }}">
							<i class="fa fa-fw fa-ban"></i>
							Zresetuj filtry
						</a>
					</label>
				</div>
			</nav>
		</form>
	@stop

	@section('leftNav')
        @parent
            <div class="notification_nav l-menu-content tips " title="zadania przedawnione">
                <table>
                    <tr>
						<td>
							<a role="button" href="{{ URL::route('injuries-search-expired', [Auth::user()->id]) }}"  class="btn btn-primary btn-xs">
								moje
							</a>
						</td>
						<td>
							<a role="button" href="{{ URL::route('injuries-search-expired') }}" class="btn btn-primary btn-xs">
								wszystkie
							</a>
						</td>
                        <td>
							<i class="btn btn-primary btn-xs
								@if(Request::segment(3) == 'expired')
									active
								@endif
								" style="cursor: auto;">
								   <i class="fa fa-bell sm-ico "></i>
							</i>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="notification_nav l-menu-content tips" title="zadania na dziś">
                <table>
                    <tr>
                        <td>
							<a role="button" href="{{ URL::route('injuries-search-today', [Auth::user()->id]) }}"  class="btn btn-primary btn-xs">
								moje
							</a>
                        </td>
						<td>
							<a role="button" href="{{ URL::route('injuries-search-today') }}"  class="btn btn-primary btn-xs">
								wszystkie
							</a>
						</td>
						<td>
							<i class="btn btn-primary btn-xs
							@if(Request::segment(3) == 'today')
								active
							@endif
							"	style="cursor: auto;">
								<i class="fa fa-bell-o sm-ico "></i>
							</i>
						</td>
                    </tr>
                </table>
            </div>
    @stop


