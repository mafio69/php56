@if (Session::has('flash_notification.message'))
<div class="alert alert-notification alert-{{ Session::get('flash_notification.level') }} marg-top-min marg-btm" >
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ Session::get('flash_notification.message') }}
</div>
@endif