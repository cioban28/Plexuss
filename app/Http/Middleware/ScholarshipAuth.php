<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ViewDataController;
use App\Http\Controllers\StartController;

class ScholarshipAuth
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

        }elseif( (isset($data['is_scholarship_admin_only']) && $data['is_scholarship_admin_only'] == false) && (!isset($data['is_organization']) || $data['is_organization'] == 0 ) ){
            // if not an organization, send to home
            return redirect()->intended('/');
        }

        return $next($request);
    }
}
