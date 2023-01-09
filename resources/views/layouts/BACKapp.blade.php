<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <script src="https://kit.fontawesome.com/b7bc0d7397.js" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        {{--<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <!--<ul class="navbar-nav mr-auto">

                    </ul>--> 
                --}}
    <div id="menu_area" class="menu-area">
        <div class="container">
            <div class="row">

                <nav class="navbar navbar-light navbar-expand-lg mainmenu">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <li class="active"><a href="#">Home <span class="sr-only">(current)</span></a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tournaments</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                                @foreach ($sports as $sport)

                                    <li class="dropdown">
                                        <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $sport->name }}</a>
                                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                                            @foreach($sport->tournaments as $tournament)

                                                <li><a href="#">{{ $tournament->name }}</a></li>

                                            @endforeach
                                            
                                            
                                            <!--<li class="dropdown">
                                                <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown3</a>
                                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                                    <li><a href="#">Action</a></li>
                                                    <li><a href="#">Another action</a></li>
                                                    <li><a href="#">Something else here</a></li>
                                                </ul>
                                            </li>-->

                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                        <!--<li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Tournaments
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item"> 
                                    {{-- href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();" --}}
                                                 
                                    Test 1
                                </a>
                            </div>
                        </li> -->

                        {{-- menu('sportTournaments', 'bootstrap') --}}

                        {{--<div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                Tournaments
                            </button>
                            <ul class="dropdown-menu">

                                @foreach ($sports as $sport)

                                @php //dd($sport->tournament); 
                                @endphp


                                <li class="dropdown-submenu">
                                    <a class="dropdown-item" tabindex="-1" href="#">{{ $sport->name }}<span class="icon voyager-angle-right"></span></a>
                                    <ul class="dropdown-menu">
                                        @foreach ($sport->tournaments as $tournament)
                                        <li><a class="dropdown-item" tabindex="-1" href="#">{{ $tournament->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>

                                @endforeach
                            </ul>
                        </div> --}}


                    </ul>
                        <!-- Authentication Links -->
                        {{--@guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul> --}}
                </div>
            </div>
        </nav>
    </div>
</div>
</div>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @php
        //dd($sports);
    @endphp
    @yield('scripts')
</body>

<script type="text/javascript">
window.onload = function() {
    /*$(document).ready(function(){
        $(".dropdown-toggle").on("mouseenter", function () {
            // make sure it is not shown:
            if (!$(this).parent().hasClass("show")) {
                $(this).click();
            }
        });

        $(".btn-group, .dropdown").on("mouseleave", function () {
            // make sure it is shown:
            if ($(this).hasClass("show")){
                $(this).children('.dropdown-toggle').first().click();
            }
        });
    });*/

    (function($){
	$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
	  if (!$(this).next().hasClass('show')) {
		$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
	  }
	  var $subMenu = $(this).next(".dropdown-menu");
	  $subMenu.toggleClass('show');

	  $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
		$('.dropdown-submenu .show').removeClass("show");
	  });

	  return false;
	});
})(jQuery)
}
</script>

</html>
