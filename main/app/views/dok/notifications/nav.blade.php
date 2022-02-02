@section('leftNav')
    @parent
    <div class="l-menu-content">
        <table id="l-menu-show">
            <tr>
                <td>
                    <?php $exist_filter = 0;?>
                    @if(Session::get('search.injury_type', '0') != 0)
                        <i class="fa fa-filter sm-ico"></i>
                        <?php $exist_filter = 1;?>
                    @endif
                    @if( Session::get('search.user_id', '0') != 0 || Session::get('search.processes_user_id', '0') != 0 )
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
		   		<label><i class="fa fa-user sm-ico"></i> Osoba odpowiedzialna:</label>
		   		<select class="form-control input-sm  search-el" id="s-user" name="processes_user_id">
		   			<option value="0" 
		   				@if(Session::get('search.processes_user_id', '0') == 0)
			    		selected
			    		@endif
		   			> ---- wszyscy --- </option>
		    	<?php foreach ($users as $k => $user){?>
	        		<option value="<?php echo $user->id;?>"
	        			@if(Session::get('search.processes_user_id', '0') == $user->id )
			    		selected
			    		@endif
	        		><?php echo $user->name;?></option>
	        	<?php }?>
	        	</select>
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
	    </nav>
    </form>
@stop


