<!DOCTYPE html>
<html lang="pl" xml:lang="pl" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
			Idea system 0.98
			@show
		</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="csrf-token" content="{{ csrf_token() }}" />

		<!-- CSS
		================================================== -->
		<link href="{{ asset("css/app.css") }}" rel="stylesheet">
		<link href="{{ asset("css/all.css") }}" rel="stylesheet">

        <link rel="shortcut icon" href="{{ asset('favicon.ico?v=2') }}">

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script data-pace-options='{ "document" : true, "eventLag": true, "restartOnPushState": false, "restartOnRequestAfter": false }' src="/js/pace.min.js"></script>
		@yield('styles')
	</head>
	<body>
	<style>
		.page-loading::before{
			content: " ";
			background-color: rgba(0, 0, 0, 0.5);
			z-index: 1999;
			width: 100%;
			height: 100%;
			position: fixed;
			left: 0;
			top: 0;
		}
	</style>
	@if(Session::has('download.in.the.next.request'))
        <?php
        $redirect_url = Session::get('download.in.the.next.request');
        Session::forget('download.in.the.next.request');
        ?>
		<script type="text/javascript">window.open('{{ $redirect_url }}', '_blank');</script>
	@endif

		<div class="alert alert-info response-alert" id="response-alert-info" role="alert"></div>
		<div class="alert alert-danger response-alert" id="response-alert-danger" role="alert"></div>
		<div class="alert alert-warning response-alert" id="response-alert-warning" role="alert"></div>
		<!-- Content -->
			@yield('content')
		<!-- ./ content -->


        <div class="l-menu-show" >
            @section('leftNav')
            @show
        </div>
        @section('leftNavContent')
        @show

    	@include('modules.modals')
		<!-- Javascripts	================================================== -->

		@section('headerJs')
			@if(!in_array(Request::segment(1),['tasks']) && !in_array(Request::getPathInfo(), ['/injuries/new', '/injuries/inprogress', '/injuries/refused', '/injuries/completed', '/injuries/total', '/injuries/theft', '/injuries/total-finished', '/injuries/search/global', '/', '']))
				<script type="text/javascript"
						src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDFo38Jk909jHI7munu9Z4xXz3KzbzR4E&libraries=places">
				</script>
			@endif
			<script src="{{ asset("js/main.js") }}"></script>
			<script src="{{ asset("js/tiff.min.js") }}"></script>
			<script >
			    $.fn.bsbutton = $.fn.button;
				$('body').addClass('page-loading');
			    let modals_initiated = false;

				setTimeout(function(){
					$('body').removeClass('page-loading');
				}, 2000);
			</script>

		@show

		@if(Session::has('show.modal.in.the.next.request'))
			<div class="modal fade" role="dialog" id="info-dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-body">
							<p>{{ Session::get('show.modal.in.the.next.request') }}</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				$(window).load(function()
				{
					$('#info-dialog').modal('show');
				});
			</script>
		@endif
	</body>
</html>
