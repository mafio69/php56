<div class="pull-left">
    <ul  class="nav nav-pills nav-injuries btn-sm">
        <li class="<?php if(Request::segment(3) == 'theft-status') echo 'active'; ?> ">
            <a href="{{ URL::route('routes.get', array('settings', 'stages', 'theft-status')) }}">statusy </a>
        </li>
        <li class="<?php if(Request::segment(3) == 'theft-step') echo 'active'; ?> ">
            <a href="{{ URL::route('routes.get', array('settings', 'stages', 'theft-step')) }}"> etapy</a>
        </li>
    </ul>
</div>
