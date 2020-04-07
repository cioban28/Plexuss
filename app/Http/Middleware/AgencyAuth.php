<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ViewDataController;
use App\Http\Controllers\StartController;

class AgencyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $startcontroller = new StartController();
        $startcontroller->setupcore();
        
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        if ( !Auth::user() ) {
            // if not signed in, send to /signin
            return redirect()->intended('/signin');

        }elseif( !isset($data['agency_collection']) ){
            // if user is not an agency, send home
            return redirect()->intended('/');
        }

        return $next($request);
    }
}
