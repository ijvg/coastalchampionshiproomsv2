<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Tournament;
use App\Sport;
use Illuminate\Support\Facades\Blade;

use Illuminate\Support\Facades\App;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('path.public', function() {
		    return base_path('../public_html');
		 });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	    if (App::environment(['staging', 'production'])) {
            URL::forceScheme('https');
        }
		
		/*if($this->app->environment('production')) {
			Log::info('hey');
		}*/

        //
        //$tournaments = Tournament::with('hotels')->with('hotelTournaments')->get();

        //$sports = Sport::with('tournaments')->get();
        $sports = Sport::all();

        view()->share('sports', $sports);
    }
}
