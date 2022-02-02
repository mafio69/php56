@extends('layouts.base')

@section('content')
<div id="wrapper">
	<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0;    background-color: white;">
	    <div class="navbar-header" style="height: 50px;">
	        <a class="navbar-brand" href="/" style="padding:10px;">
	        	{{ HTML::image('images/cas.jpg', 'Logo', array('style' => '  height: 35px;  ')) }}
	        </a>
	    </div>
	</nav>
	<!-- /.navbar-static-top -->

	@yield('left-nav')

	<!-- /.navbar-static-side -->
	<div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                @include('modules.flash_notification')

                <h2 class="page-header " style="margin: 20px 0 20px; width: 100%; position: relative; display: inline-block;">
					@yield('header')
                </h2>

                    @yield('sub-header')
                <!-- Content -->
					@yield('main')
				<!-- ./ content -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    </div>

</div>
@stop
