<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;

class Maintenance {
    public function handle($request, Closure $next){
        if($request->is('admin*') || $request->is('maintenance')){
            return $next($request);
        }else{
            return new RedirectResponse(url('/maintenance'));
        }
    }
}