<?php

namespace App\Http\Controllers;

use Request, Session;
use App\User, App\Webinar;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MandrillAutomationController;

class WebinarController extends Controller
{
    private $event_id = 6;

	public function index(){

		

		if ( !Auth::check() ) {
			
			//Adds the session ONLY if the input is there and ONLY if the user is sighed out
			if (Request::has('redirect')) {
				Session::put('redirect', Request::get('redirect'));
			}
			
			return redirect('/signin');
		}

		//Get user logged in info.
		$user = User::find( Auth::user()->id );

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['title'] = 'Plexuss Webinar';
		$data['currentPage'] = 'webinar';

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		//Data that will be shown on the index view of the webinar page.
		$data['webinar']['id'] = $user['id'];
		$data['webinar']['fname'] = $user['fname'];
		$data['webinar']['lname'] = $user['lname'];
		$data['webinar']['email'] = $user['email'];
		$data['webinar']['formNotSubmitted'] = "active";
		$data['webinar']['formSubmitted'] = "";

		//We check if this user has already submited for this spot.

		$is_registered_for_webinar = Webinar::where('user_id', '=', $user->id)
		->where('event_id', '=', $this->event_id)->first();


		//print_r($is_registered_for_webinar->user_id);
		//exit();

		//$spots  = $user->webinars;

		if (isset($is_registered_for_webinar->user_id)) {
			$data['webinar']['formNotSubmitted'] = "";
			$data['webinar']['formSubmitted'] = "active";
		}

		return View('webinar.webinar', array('data' => $data));
	}

	public function submit(){
		//Auth user
		if ( !Auth::check() ) {
			return redirect('/signin');
		}

		//Get user logged in info.
		$user = User::find( Auth::user()->id );


		//creating new webinar object and saving the user's id in user_id property
		$webinar = new Webinar;
		$webinar->user_id = $user->id;
		$webinar->event_id = $this->event_id; // One for now :)
		$webinar->save();

		$data = array();
		$data['email'] = Session::get('userinfo.email');
		$data['fname'] = Session::get('userinfo.fname');
		
		$mac = new MandrillAutomationController;
		$mac->webinarConfirmation($data);
		return "Completed";
	}
}
