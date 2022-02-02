<div class="row marg-btm">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li role="presentation"
                @if(Request::segment(3) == 'groups' )
                class="active"
                    @endif
                    ><a href="/settings/user/groups">Grupy użytkowników</a></li>
            <li role="presentation"
                @if(Request::segment(3) != 'groups' )
                class="active"
                    @endif
                    ><a href="/settings/users">Wszyscy użytkownicy</a></li>

        </ul>
    </div>
</div>