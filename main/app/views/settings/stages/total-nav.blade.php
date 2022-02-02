<div class="pull-left">
    <ul  class="nav nav-pills nav-injuries btn-sm">
        <li class="<?php if(Request::segment(3) == 'total-status') echo 'active'; ?> ">
            <a href="{{ URL::route('routes.get', array('settings', 'stages', 'total-status')) }}">statusy </a>
        </li>
        <li class="<?php if(Request::segment(3) == 'total-step') echo 'active'; ?> ">
            <a href="{{ URL::route('routes.get', array('settings', 'stages', 'total-step')) }}"> etapy</a>
        </li>
    </ul>
</div>
