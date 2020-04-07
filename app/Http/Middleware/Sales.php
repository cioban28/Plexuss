<?php

namespace App\Http\Middleware;

use Closure;
use App\UsersSalesControl, App\User;
use App\Http\Controllers\ViewDataController;
use App\Http\Controllers\StartController;

class Sales
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

        $is_plexuss = User::on('rds1')->where('id', $data['user_id'])->where('is_plexuss', '1')->first();

        $usl =  UsersSalesControl::on('rds1')->where('user_id', $data['user_id'])->first();

        if (!isset($usl) || !isset($is_plexuss)) {

            print_r('Oops you shouldn\'t be here <br>');
            print_r('Go back to <a href="https://plexuss.com/home">'.'home page</a>');
            exit();
        }

        return $next($request);
    }
}
