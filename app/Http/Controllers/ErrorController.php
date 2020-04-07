<?php

namespace App\Http\Controllers;

use Request, Response;
use App\ErrorLog;
use Jenssegers\Agent\Agent;

class ErrorController extends Controller
{
    // protected $layout = "views";

	public function error($code, $exception) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (isset($data['profile_img_loc'])) {
			$data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}

    	$this->insertLogError($exception, $code);
    	// return View('errors.404', $data);

	    switch ($code) {
	        case 404:
	        	$data['currentPage'] = 'error_404';
	        	$data['error_num'] = '404';
	            // return View('errors.404', $data);
	            return Response::make(view('errors.404', $data), 404);
	        break;

	        case 500:
	        	$data['currentPage'] = 'error_404';
	        	$data['error_num'] = '500';
	            // return View('errors.404', $data);
	            return Response::make(view('errors.404', $data), 404);
	        	break;
	        	
	        default:
	            $this->layout->content = View('errors.500');
	        break;
	    }
	}

	public function insertLogError($exception, $code){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$ip ='';

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			if (isset( $_SERVER['REMOTE_ADDR']) ) {
				$ip =  $_SERVER['REMOTE_ADDR'];
			}
		   
		}

		$url = Request::url();

		$agent = new Agent();

		$browser = $agent->browser();
		$version = $agent->version( $browser );

		$browser = $browser. " ". $version;


		$platform = $agent->platform();
		$version = $agent->version( $platform );

		$platform = $platform. " ".  $version;


		$device = $agent->isMobile();
		
		if ( $device ) {
			$device = $agent->device();
		}else {
			$device = "Desktop";
		}

		$params = Request::all();

		//Don't save the password of users!
		if(strpos($url, "signin") ){
			$params = "";
		}else{
			$params = json_encode($params);
		}


		$er = new ErrorLog;

		$er->insertError($data['user_id'],$exception, $code, $ip, $url,$device,$browser, $platform, $params);

	}
}
