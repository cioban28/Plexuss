<?php

namespace App\Http\Middleware;

use Closure, Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StartController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UserAuth
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
        if ( !Auth::user() ) {
            if ($request->has('hashed_user_id')) {
                $hashed_user_id = $request->input('hashed_user_id');

                try {
                    $user_id = Crypt::decrypt($hashed_user_id);
                    if (is_numeric($user_id)) {
                        
                        Auth::loginUsingId( $user_id, true );
                        Session::put('userinfo.session_reset', 1);
                    }

                } catch (DecryptException $e) {
                    // Dont do anything
                }
            }
        }
        
        $startcontroller = new StartController();
        $startcontroller->setupcore();    
        
        // Don't redirect users for now
        
        if ( !Auth::user() ) {
            // if it's an ajax call dont do anything
            // $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            // // dd($isAjax);
            // if(!$isAjax) {
            //     $url = $request->fullUrl();
            //     $url = str_replace(env('CURRENT_URL'), "", $url);
            //     Session::put('redirect_from_signin', $url);
            // }            
            return redirect()->intended('/signin');
        }

        return $next($request);
    }
}
