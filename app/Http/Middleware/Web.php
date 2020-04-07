<?php

namespace App\Http\Middleware;

use Closure, Session, Carbon\Carbon;
use App\Http\Controllers\TrackingPageController, App\Http\Controllers\Controller;
use App\UtmTracking;

class Web
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
        $tracking = new TrackingPageController();
        $tracking->setPageView();
        $user_id = Session::get('userinfo.id');

        // if user is already logged in and we  didn't have their user_id, update their user_id
        if (isset($user_id)  && Session::has('utm_tracking_id')) {
            $utm_tracking_id = Session::get('utm_tracking_id');

            $utm_tracking = UtmTracking::find($utm_tracking_id);
            $utm_tracking->user_id  = $user_id;
            $utm_tracking->save();

            Session::forget('utm_tracking_id');
        }

        if ($request->has(['utm_source', 'utm_medium'])) {
            $url = $request->url();
            $base_url = str_replace(env('CURRENT_URL'), "", $url);

            if ($base_url != "signup"  && !Session::has('first_session_utm_tracking')) {
                
                
                $arr = array();
                $input = $request->all();

                $arr['user_id'] = $user_id;
                $arr['base_url']= $base_url;

                $cont = new Controller();
                $arr['ip'] = $cont->getIp();

                isset($input['utm_source'])   ? $arr['utm_source']   = $input['utm_source']   : null;
                isset($input['utm_medium'])   ? $arr['utm_medium']   = $input['utm_medium']   : null;
                isset($input['utm_campaign']) ? $arr['utm_campaign'] = $input['utm_campaign'] : null;
                isset($input['utm_term'])     ? $arr['utm_term']     = $input['utm_term']     : null;
                isset($input['utm_content'])  ? $arr['utm_content']  = $input['utm_content']  : null;

                $arr['date'] = Carbon::today()->toDateString();

                $utm_tracking = UtmTracking::updateOrCreate($arr, $arr);

                if (!isset($user_id)) {
                    Session::put('utm_tracking_id', $utm_tracking->id);
                }

                Session::put('first_session_utm_tracking', true);

            }            
        }
        return $next($request);
    }
}