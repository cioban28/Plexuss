<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Request;

use App\Http\Controllers\ViewDataController;
use App\Events\UserSignedup;

class SocketController extends Controller
{
    //
    public function index($user_id = null){

    	if( isset($user_id) ){
    		$data = array();
    		$data['user_id'] = $user_id;

    	}else{
	    	$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
    	}

	    // $data = [
	    // 	'event' => 'UserSignedup',
	    // 	'data' => [
	    // 		'user_id' => $data['user_id'],
	    // 	]

	    // ];

	    Redis::publish('test-channel', json_encode($data));


	    // event(new UserSignedup($data['user_id']));
	    // return 'done';
	    return View('socket.test');
    }

    public function getRedis(){
    	dd(Redis::get('onlineUsers'));
    	// dd(Cache::get('UserSignedup'));

    }
}
