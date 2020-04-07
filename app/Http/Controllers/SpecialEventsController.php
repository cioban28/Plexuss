<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpecialEventsController extends Controller
{
    public function getHappyBirthday(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		
		$data['title'] = 'Happy Birthday!';
		$data['currentPage'] = 'specialevent-happyBirdthdayToYou';
		
		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		return View('specialevents.happyBirthday', 
			$data);
	}
}
