<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/favicons/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/favicons/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/favicons/favicon-16x16.png') }}">
	<link rel="manifest" href="{{ asset('storage/favicons/site.webmanifest') }}">
	<link rel="mask-icon" href="{{ asset('storage/favicons/safari-pinned-tab.svg') }}" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <script src="https://kit.fontawesome.com/b7bc0d7397.js" crossorigin="anonymous"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/darkly/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kmlpandey77/bootnavbar/css/bootnavbar.css">

    <script src="https://cdn.jsdelivr.net/gh/kmlpandey77/bootnavbar/js/bootnavbar.js" defer></script>

    @yield('styles')

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">

        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm main-nav-menu">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('/storage/images/ccrLogo.png' ) }}" height="75"/>

                </a>
            </div>
        </nav>


                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item {{ Request::path() === '/' ? 'active' : '' }}">

                            <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item dropdown {{ Request::segment(1) === 'tournament' ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Tournaments
                            </a>
                            <ul class="dropdown-menu first-dropdown" aria-labelledby="navbarDropdownMenuLink">

                                @foreach ($sports as $sport)

                                    @if ($sport->activeTournamentCount() > 0)
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" disabled="{{ $sport->hasOngoingTournaments() === 0 ? 'true' : 'false' }}" href="#">{{ $sport->name }}</a>

                                            {{-- @if ( $sport->hasOngoingTournaments() > 0) --}}
                                                <ul class="dropdown-menu">

                                                    @foreach ($sport->tournaments as $tournament)
                                                        @if ($tournament->end_date >= now() && $tournament->status != 0)
                                                            <li><a class="dropdown-item" href="/tournament/{{ $tournament->slug }}">{{ $tournament->name }}</a></li>
                                                        @endif

                                                    @endforeach
                                                </ul>
                                            {{-- @endif --}}
                                        </li>
                                    @endif

                                @endforeach



                            </ul>

                        </li>
                        <li class="nav-item {{ Request::path() === '/about-us' ? 'active' : '' }}">
                            <a class="nav-link" href="/page/about-us">About Us</a>
                        </li>
                        <li class="nav-item {{ Request::path() === '/contact-us' ? 'active' : '' }}">
                            <a class="nav-link" href="/contact-us">Contact Us</a>
                        </li>

                    </ul>
                </div>





        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <footer>
        @include('partials.footer')
    </footer>
   {{--  @yield('scripts') --}}

</body>

@yield('scripts')

<script type="text/javascript">
// window.onload = function() {


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

    /*(function($){
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
})(jQuery)*/

/*$('#bootnavbar').bootnavbar({
    animation: false
});*/

/*
$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
  if (!$(this).next().hasClass('show')) {
    $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
  }
  var $subMenu = $(this).next('.dropdown-menu');
  $subMenu.toggleClass('show');


  $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
    $('.dropdown-submenu .show').removeClass('show');
  });


  return false;
});

$('.dropdown-submenu a.dropdown-toggle').on('click', function(e) {
    $(this).toggleClass('show');
  /*if (!$(this).hasClass('show')) {
    $(this).toggleClass('show');
  }

  $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
    $(this).removeClass('show');
  });

  $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {

    $('.dropdown-submenu .show').removeClass('show');
  });

  return false;
});

$(document).click(function (event) {
    var click = $(event.target);
    var _open = $(".navbar-collapse").hasClass("show");
    if (_open === true && !click.hasClass("navbar-toggler")) {
        $(".navbar-toggler").click();
    }
});

};*/
 </script>


</html>
