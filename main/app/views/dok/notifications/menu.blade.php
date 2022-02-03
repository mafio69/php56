<div class="pull-left">
  <ul  class="nav nav-pills nav-injuries btn-sm">

      <li class="<?php if(Request::segment(3) == 'new') echo 'active'; ?> ">
        <a href="{{{ URL::route('dok.notifications.new' ) }}}">Nowe <span class="badge">{{ isset($counts[0]) ? $counts[0] : 0 }}</span></a>
      </li>

      <li class="<?php if(Request::segment(3) == 'inprogress') echo 'active'; ?>">
        <a href="{{{ URL::route('dok.notifications.inprogress' ) }}}" >W obsłudze <span class="badge">{{ isset($counts[5]) ? $counts[5] : 0 }}</span></a>
      </li>

      <li class="<?php if(Request::segment(3) == 'completed') echo 'active'; ?>">
      	<a href="{{{ URL::route('dok.notifications.completed' ) }}}" >Zakończone <span class="badge">
          {{ ( isset($counts[10]) ? $counts[10] : 0) }}
        </span></a>
      </li>  

      <li class="<?php if(Request::segment(4) == 'canceled') echo 'active'; ?>">
        <a href="{{{ URL::route('dok.notifications.canceled' ) }}}" >Anulowane <span class="badge">
          {{ ( isset($counts['-5']) ? $counts['-5'] : 0) }}
        </span></a>
      </li> 
    
  </ul>
</div>