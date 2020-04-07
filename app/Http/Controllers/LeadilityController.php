<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeadilityController extends Controller
{
    private $user;
	private $user_id;
	//index
	public function index(){

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		
		$data['title'] = 'Plexuss Leadility';
		$data['currentPage'] = 'plex-publisher';
		//$data['ajaxtoken'] = $token['token'];
		
		return View('leadility.view', $data);
	}


	public function importUser(){
		//Build to $data array to pass to view.
		$eightAM ="06:00:00";
		$sixPM ="23:59:59";

		if (time() >= strtotime($eightAM) && time() <= strtotime($sixPM)) {
			
			$tt = TrackingTest::whereRaw('`phone` != ""')
								->whereRaw('`pixel` != ""')
								->where('pixel', '!=' ,'0')
								->where('first_name', '!=', 'test')
								->where('first_name', '!=', '""')
								->where('last_name', '!=', '""')
								->where('email', 'NOT LIKE' ,'test%')
								->whereRaw('`aos` != ""')
								->where('email_score', 'A')
								->where('flag', '=', '0')
								->take(1)
								->orderBy('id', 'ASC')
								->get();

			// if (isset($pl_arr)) {
			// 	$tt = $tt->whereNotIn('id', $pl_arr)->get();
			// }else{
			// 	$tt = $tt->get();
			// }

			// echo "<pre>";
			// print_r($tt->toArray());
			// echo "</pre>";
			// exit();

			foreach ($tt as $key) {

				$user_table = User::where('email', $key->email)->first();

				if (isset($user_table)) {
					$pl = new PlexussLead;	
					$pl->tt_id = $key->id;
					$pl->save();

					$temp_tt = DB::connection('ldy')->statement('UPDATE `tracking_test` set `flag` = "1" where `id` = '.$key->id);

					continue;
				}	

				// $rnd = $this->getRandomNum(1,45);

				// sleep($rnd);

				$this->insertToUser($key);
				$user = $this->user;

				$rnd = $this->getRandomNum(0,1);
				if ($rnd == 0) {
					$this->importAddress($key);
				}

				$rnd = $this->getRandomNum(0,1);
				if ($rnd == 0) {
					$this->importCountry($key);
				}

				$rnd = $this->getRandomNum(0,1);
				if ($rnd == 0) {
					$this->importPhone($key);
				}

				$rnd = $this->getRandomNum(0,1);
				if ($rnd == 0) {
					$this->importGradYear($key);
				}

				$rnd = $this->getRandomNum(0,1);
				if ($rnd == 0) {
					$this->importGender($key);
				}

				$rnd = $this->getRandomNum(0,1);
				if ($rnd == 0) {
					$this->importObjective($key);
				}

				$user->save();
				
				$pl = new PlexussLead;	
				$pl->tt_id = $key->id;
				$pl->save();

				$temp_tt = DB::connection('ldy')->statement('UPDATE `tracking_test` set `flag` = "1" where `id` = '.$key->id);

			}
		}
	}

	private function insertToUser($key){

		$user = new User;

		$user->fname = $key->first_name;
		$user->lname = $key->last_name;
		$user->email = $key->email;
		$user->password = Hash::make($key->fname. '**'. $key->lname);

		$year = $this->getRandomNum(1975, 2000);
		$month = $this->getRandomNum(1, 12);
		$day = $this->getRandomNum(1, 29);

		if (strlen($month) == 1) {
			$month = '0'.$month;
		}
		if (strlen($day) == 1) {
			$day = '0'.$day;
		}

		$user->birth_date = $year . '-'. $month . '-'. $day;
		$user->is_student = 1;
		$user->is_ldy = 1;
		$user->profile_percent = 2;

		$user->save();

		$confirmation = str_random( 20 );
		$token = new ConfirmToken( array( 'token' => $confirmation ) );

		$user->confirmtoken()->save( $token );

		$name = $key->first_name .' '. $key->last_name;
		$this->sendconfirmationEmail( $name, $key->email, $confirmation );

		$user_id = $user->id;
		$this->user_id = $user_id;
		$user = User::find($user_id);

		$confirmation = str_random( 60 );
		$user->remember_token = $confirmation;
		$this->user = $user;

	}

	private function getRandomNum($start, $end){
		return rand($start, $end);
	}

	private function importAddress($key){

		$user = $this->user;
		if (isset($key->address)) {
			$user->address = $key->address;
			$user->profile_percent = 5;
		}
		
		if (isset($key->state)) {
			$user->state = $key->state;
		}

		if (isset($key->city)) {
			$user->city = $key->city;
		}

		if (isset($key->zip)) {
			$user->zip = $key->zip;
		}

		$this->user = $user;
	}

	private function importCountry($key){
		$user = $this->user;
		$user->country_id = 1;

		$this->user = $user;
	}

	private function importPhone($key){
		$user = $this->user;
		$user->phone = $key->phone;

		$this->user = $user;
	}

	private function importGradYear($key){
		$user = $this->user;

		if (isset($key->highesteducationlevel)) {
			
			if ($key->highesteducationlevel == 'GED' || $key->highesteducationlevel == 'HIGHSCHOOL' ||
				$key->highesteducationlevel == 'HIGH SCHOOL DIPLOMA' || $key->highesteducationlevel == 'INHS' ||
				$key->highesteducationlevel == 'NODEGREE' || $key->highesteducationlevel == 'NOHIGHSCHOOL/GEDDIPLOMA' ||
				$key->highesteducationlevel == 'No High School/GED Diploma' || $key->highesteducationlevel == 'NOHS') {
				
				$user->in_college = 0;
				$user->hs_grad_year = $key->highschoolgradyear;
			}else{
				$user->in_college = 1;
				$user->college_grad_year = $key->highschoolgradyear;
			}

		}

		$this->user = $user;
	}

	private function importGender($key){
		$user = $this->user;
		if (isset($key->gender)) {
			
			if ($key->gender == 'MALE') {
				$user->gender = 'm';
			}else{
				$user->gender = 'f';
			}
		}
		$this->user = $user;
	}

	private function importObjective($key){

		$user = $this->user;
		
		$degree_id = $this->getRandomNum(1,5);
		$major_id = $this->getRandomNum(1,1473);
		$profession_id = $this->getRandomNum(1,529);

		$obj = new Objective;

		$obj->user_id = $this->user_id;
		$obj->degree_type = $degree_id;
		$obj->major_id = $major_id;
		$obj->profession_id = $profession_id;

		$rnd = $this->getRandomNum(1,3);

		if ($rnd == 2) {
			$obj->whocansee = 'Public';
		}

		$obj->save();

		$user->profile_percent = 10;

		$this->user = $user;

	}

	private function sendconfirmationEmail( $name, $emailaddress, $confirmation ) {

		$mac = new MandrillAutomationController;
		$mac->confirmationEmailForUsers($name, $emailaddress, $confirmation);

	}
}
