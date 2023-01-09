<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Tournament;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /*public function __construct()
    {
        // Load your objects
        $tournaments = Tournament::all();

        // Make it available to all views by sharing it
        view()->share('tournaments', $tournaments);
    }*/
}
