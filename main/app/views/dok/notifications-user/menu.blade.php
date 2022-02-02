<div class="pull-left">
  <ul  class="nav nav-pills nav-injuries btn-sm">

      <li class="<?php if(Request::segment(3) == 'new') echo 'active'; ?> ">
        <a href="{{{ URL::route('dok.notifications-user.new', [Request::segment(4)] ) }}}">Nowe <span class="badge">{{ isset($counts[0]) ? $counts[0] : 0 }}</span></a>
      </li>

      <li class="<?php if(Request::segment(3) == 'inprogress') echo 'active'; ?>">
        <a href="{{{ URL::route('dok.notifications-user.inprogress', [Request::segment(4)] ) }}}" >W obsłudze <span class="badge">{{ isset($counts[5]) ? $counts[5] : 0 }}</span></a>
      </li>

      <li class="<?php if(Request::segment(3) == 'completed') echo 'active'; ?>">
      	<a href="{{{ URL::route('dok.notifications-user.completed', [Request::segment(4)] ) }}}" >Zakończone <span class="badge">
          {{ ( isset($counts[10]) ? $counts[10] : 0) }}
        </span></a>
      </li>  

      <li class="<?php if(Request::segment(4) == 'canceled') echo 'active'; ?>">
        <a href="{{{ URL::route('dok.notifications-user.canceled', [Request::segment(4)] ) }}}" >Anulowane <span class="badge">
          {{ ( isset($counts['-5']) ? $counts['-5'] : 0) }}
        </span></a>
      </li> 
    
  </ul>
</div>


<div class="pull-right">
  <label><h4>Osoba odpowiedzialna: </h4></label>
  <select class="form-control input-sm" id="change-user" >
    <option value="0" target="/dok/notifications/{{ Request::segments()[2] }}/0/" > ---- wybierz --- </option> 
      
  <?php foreach ($users as $k => $user){?>
    <option value="/dok/notifications/{{ Request::segments()[2] }}/{{ $user->id }}/"
      @if($id == $user->id )
        selected
        @endif
    ><?php echo $user->name;?></option>
  <?php }?>

  </select>
</div>

@section('headerJs')
  @parent
  <script type="text/javascript">
    $(document).ready(function() {
      $('#change-user').on('change', function(){
          window.location.replace($(this).val());
      });
    });
  </script>
@stop