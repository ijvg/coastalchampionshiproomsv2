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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/darkly/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kmlpandey77/bootnavbar/css/bootnavbar.css">

    <script src="https://cdn.jsdelivr.net/gh/kmlpandey77/bootnavbar/js/bootnavbar.js" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        {{-- <nav class="navbar navbar-expand-md navbar-light bg-primary shadow-sm" id="bootnavbar">
            <!--<div class="container">-->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar 
                    <ul class="navbar-nav mr-auto">

                    </ul>-->

                    <!-- Right Side Of Navbar -->
                    <!--<ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Home</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Dropdown</a>
                            <ul class="dropdown-menu">
                                <li class="nav-link dropdown">
                                    <a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Dropdown Item</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Another Drop Down Item</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>--> --}}

                    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm main-nav-menu">
                        <div class="container">
                        <a class="navbar-brand" href="{{ url('/') }}">
                                <img src="/storage/images/ccr_logo.png" height="75"/>

                        </a>

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
                                
                                
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" disabled="{{ $sport->hasOngoingTournaments() === 0 ? 'true' : 'false' }}" href="#">{{ $sport->name }}</a>
                                    @if ( $sport->hasOngoingTournaments() > 0)
                                    <ul class="dropdown-menu">

                                        @foreach ($sport->tournaments as $tournament)
                                            @if ($tournament->end_date >= now())
                                            <li><a class="dropdown-item" href="/tournament/{{ $tournament->slug }}">{{ $tournament->name }}</a></li>
                                            @endif

                                        @endforeach
                                    </ul>
                                    @endif
                                </li>

                                @endforeach
                                
                                
                                {{-- <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li class="dropdown-submenu">
                                  <a class="dropdown-item dropdown-toggle" href="#">Submenu</a>
                                  <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Submenu action</a></li>
                                    <li><a class="dropdown-item" href="#">Another submenu action</a></li>
                      
                      
                                    <li class="dropdown-submenu">
                                      <a class="dropdown-item dropdown-toggle" href="#">Subsubmenu</a>
                                      <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Subsubmenu action</a></li>
                                        <li><a class="dropdown-item" href="#">Another subsubmenu action</a></li>
                                      </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                      <a class="dropdown-item dropdown-toggle" href="#">Second subsubmenu</a>
                                      <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Subsubmenu action</a></li>
                                        <li><a class="dropdown-item" href="#">Another subsubmenu action</a></li>
                                      </ul>
                                    </li> 
                      
                      
                      
                                  </ul>
                                </li>--}}
                              </ul>
                            </li>
                            <li class="nav-item {{ Request::path() === '/about-us' ? 'active' : '' }}">
                                <a class="nav-link" href="/about-us">About Us</a>
                            </li>
                            <li class="nav-item {{ Request::path() === '/contact-us' ? 'active' : '' }}">
                                <a class="nav-link" href="/contact-us">Contact Us</a>
                            </li>

                          </ul>
                    
                </div>
            <!--</div>-->
            </div>
        </nav>
    
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


                    <!--</ul>-->
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
                    </ul> 
                </div>
            </div>
        </nav>
    </div>
</div>
</div>--}}

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <footer>
        @include('partials.footer')
    </footer>
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
  });*/

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

};
</script>

</html>
