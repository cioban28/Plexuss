<?php

namespace App\Http\Controllers;

use DateTime, App\PlexussConference;

class ConferenceController extends Controller
{
    public function index(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		
		$data['title'] = 'Plexuss Conferences';
		$data['currentPage'] = 'plexuss-conferences';
		
		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}
		$pc = new PlexussConference;

		$conf = $pc->getConferences();

		$ret = array();
		foreach ($conf as $key) {
			$arr = array();

			$arr['date'] = new DateTime($key->date);
			$arr['date'] = $arr['date']->format('m/d/Y');

			$arr['name'] = $key->name;
			$arr['location'] = $key->location;
			$arr['booth_num'] = $key->booth_num;

			$ret[] = $arr;
		}

		$data['conferences'] = $ret;
		
		return View('conference.index', $data);
	}
}
