<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfilawController extends Controller
{
    private $school_name = '';

	public function index(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Infilaw Survey';
		$data['currentPage'] = 'infilawSurvey';

		$user_id = Session::get('infilawSurvey_userId');

		$session_arr = array();
		$session_arr['ipae'] = InfilawPracticingAttorneyExperiences::get()->toArray();

		$zipCodes = new ZipCodes;
		$states = $zipCodes->getAllUsStateAbbreviation();

		$temp = array('default' => 'Select one...');

		$states = $temp + $states;
		unset($states['']);

		$session_arr['states'] = $states;

		Session::put('infilawSurvey_array', $session_arr);

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		   if (isset( $_SERVER['REMOTE_ADDR']) ) {
		    $ip =  $_SERVER['REMOTE_ADDR'];
		   }     
		}

		if (!isset($user_id)) {
			$isu  = new InfilawSurveyUser;
			$isu->ip = $ip;
			$isu->save();

			Session::put('infilawSurvey_userId', $isu->id);
			$user_id = $isu->id;
		}
		
		$data['hashed_infilaw_user_id'] = Crypt::encrypt($user_id);

		return View('infilawSurvey.index', $data);
	}

	public function saveInfo(){

		$input = Request::all();

		$id = Crypt::decrypt($input['hashed_infilaw_user_id']);

		$isu = InfilawSurveyUser::find($id);

		if (Session::get('infilawSurvey_array') != null) {
	
			$session_arr = Session::get('infilawSurvey_array');
		}else{
			$session_arr = array();
		}
		// dd('here');	
		if(isset($input['school_name'])){
			$isu->school_name = $input['school_name'];
			$session_arr['school_name'] = $input['school_name'];
		}
		if(isset($input['name'])){
			$isu->name = $input['name'];
			$session_arr['name'] = $input['name'];
		}
		if(isset($input['email'])){
			$isu->email = $input['email'];
			$session_arr['email'] = $input['email'];
		}
		if(isset($input['phone'])){
			$isu->phone = $input['phone'];
			$session_arr['phone'] = $input['phone'];
		}
		if(isset($input['address'])){
			$isu->address = $input['address'];
			$session_arr['address'] = $input['address'];
		}
		if(isset($input['experience_satisfy'])){
			$isu->experience_satisfy = $input['experience_satisfy'];
		}
		if(isset($input['career_satisfy'])){
			$isu->career_satisfy = $input['career_satisfy'];
		}
		if(isset($input['networking_alum'])){
			$isu->networking_alum = $input['networking_alum'];
		}
		if(isset($input['in_legal'])){
			$isu->in_legal = $input['in_legal'];
		}
		if(isset($input['income'])){
			$session_arr['income'] = $input['income'];
			$isu->income = $input['income'];
		}
		if(isset($input['interested_in_stipend'])){
			$isu->interested_in_stipend = $input['interested_in_stipend'];
		}
		if(isset($input['pass_bar'])){
			$isu->pass_bar = $input['pass_bar'];
		}
		if(isset($input['bar_state'])){
			$isu->bar_state = $input['bar_state'];
		}
		if(isset($input['licensed_attorney'])){
			$isu->licensed_attorney = $input['licensed_attorney'];
		}
		if(isset($input['jurisdiction_state'])){
			$isu->jurisdiction_state = $input['jurisdiction_state'];
		}
		if(isset($input['practicing_attorney'])){
			$isu->practicing_attorney = $input['practicing_attorney'];
		}
		if(isset($input['current_employer'])){
			$isu->current_employer = $input['current_employer'];
		}
		if(isset($input['ipae'])){
			$isu->practicing_attorney_experience = $isu->practicing_attorney_experience.$input['ipae'].",";
		}
		if(isset($input['start_date'])){
			$isu->start_date = $input['start_date'];
		}
		if(isset($input['title'])){
			$isu->title = $input['title'];
		}
		if(isset($input['exact_income'])){
			$isu->exact_income = $input['exact_income'];
		}

		// dd($session_arr);
		Session::put('infilawSurvey_array', $session_arr);

		$isu->save();

	}

	public function step( $step ){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Infilaw Survey';
		$data['currentPage'] = 'infilawSurvey';

		if ($step > 13) {
			return Redirect::to('/infilaw/survey/');
		}

		$user_id = Session::get('infilawSurvey_userId');

		$session_arr = Session::get('infilawSurvey_array');
		// dd($session_arr);

		if (!isset($user_id)) {
			$isu  = new InfilawSurveyUser;
			$isu->save();

			Session::put('infilawSurvey_userId', $isu->id);
			$user_id = $isu->id;
		}
		
		$data['hashed_infilaw_user_id'] = Crypt::encrypt($user_id);

		$data['is_interested'] = false;

		// Make sure the session arr has the email, school name , and income if not take it from db, place it in there.
		if ((int) $step > 1 && (!isset($session_arr['email']) || !isset($session_arr['school_name']))) {
			$isu = InfilawSurveyUser::find($user_id);
			$session_arr['email'] = $isu->email;
			$session_arr['school_name'] = $isu->school_name;

			if ((int) $step > 7 && !isset($session_arr['income'])) {
				$session_arr['income'] = $isu->income;
			}
			if ((int) $step == 9 && !isset($session_arr['ipae'])) {
				$session_arr['ipae'] = $session_arr['ipae'] = InfilawPracticingAttorneyExperiences::get()->toArray();
			}
			Session::put('infilawSurvey_array', $session_arr);
		}
		
		
		$data['is_qualified'] = false;

		// check to see if the user is qualified for the second part of the questions
		if ((int) $step >= 9 && (int) $step <= 13) {
			$isu  = InfilawSurveyUser::find($user_id);
			$data['is_qualified'] = $this->isQualified();
			$data['is_interested'] = $isu->interested_in_stipend == 'Yes' ? true : false;
			if(!$data['is_qualified'] || $isu->interested_in_stipend == 'No' ){
				$step = '13';
			}
		}

		if ($step == '13') {

			$check = false;

			$mac = new MandrillAutomationController();

			$isu  = InfilawSurveyUser::find($user_id);
			if ($data['is_qualified'] == true) {
				
				if (isset($isu->school_name) && isset($isu->name) && isset($isu->email) && isset($isu->phone)
					&& isset($isu->address) && isset($isu->experience_satisfy) && isset($isu->career_satisfy) 
					&& isset($isu->networking_alum) && isset($isu->in_legal) && isset($isu->income) && isset($isu->interested_in_stipend)
					&& isset($isu->pass_bar) && isset($isu->practicing_attorney)

					&& !empty($isu->school_name) && !empty($isu->name) && !empty($isu->email) && !empty($isu->phone)
					&& !empty($isu->address) && !empty($isu->experience_satisfy) && !empty($isu->career_satisfy) 
					&& !empty($isu->networking_alum) && !empty($isu->in_legal) && !empty($isu->income) && !empty($isu->interested_in_stipend)
					&& !empty($isu->pass_bar) && !empty($isu->practicing_attorney) ){

					$isu->amazon_code_status = $this->setAmazonCode();
					$isu->is_finished = 1;
					$isu->save();

					$check = true;
				}
			}else{

				if (isset($isu->school_name) && isset($isu->name) && isset($isu->email) && isset($isu->phone) ){
					$isu->amazon_code_status = $this->setAmazonCode();
					$isu->is_finished = 1;
					$isu->save();

					$check = true;
				}
				
			}

			if ($check) {
				$mac->sendInfilawCollegeEmail($isu);
			}
			
		}
		//dd($step);
		$data['ipae'] = $session_arr['ipae'];
		
		$prev = $step;
		$next = $step;
		$data['prev_step'] = (int)--$prev;
		$data['current_step'] = (int)$step;
		$data['next_step'] = (int)++$next;

		$data['income_arr'] = array('default' => 'Select one...',
									'$0 - $4,999' => '$0 - $4,999',
								    '$5,000 - $9,999' => '$5,000 - $9,999',
								    '$10,000 - $15,999' => '$10,000 - $15,999',
								    '$16,000 - $20,999' =>'$16,000 - $20,999',
								    '$21,000 - $25,999' => '$21,000 - $25,999',
								    '$26,000 - $30,999' => '$26,000 - $30,999',
								    '$31,000 - $35,999' => '$31,000 - $35,999',
								    '$36,000 - $40,999' => '$36,000 - $40,999',
								    '$41,000 - $45,999' => '$41,000 - $45,999',
								    '$46,000 - $49,999' => '$46,000 - $49,999',
								    '$50,000 - $50,999' => '$50,000 - $50,999',
								    '$51,000 - $51,999' => '$51,000 - $51,999',
								    '$52,000 - $52,999' => '$52,000 - $52,999',
								    '$53,000 - $53,999' => '$53,000 - $53,999',
								    '$54,000 - $54,999' => '$54,000 - $54,999',
								    '$55,000 - $55,999' => '$55,000 - $55,999',
								    '$56,000 - $60,999' => '$56,000 - $60,999',
								    '$61,000 - $65,999' => '$61,000 - $65,999',
								    '$66,000 - $70,999' => '$66,000 - $70,999',
								    '$71,000 - $75,999' => '$71,000 - $75,999',
								    '$76,000 - $80,999' => '$76,000 - $80,999',
								    '$81,000 - $85,999' => '$81,000 - $85,999',
								    '$86,000 - $90,999' => '$86,000 - $90,999',
								    '$91,000 - $95,999' => '$91,000 - $95,999',
								    '$100,000+' => '$100,000+',
								    );
		$data['session_arr'] = $session_arr;
		//dd($data);

		return View('infilawSurvey.index', $data);	
	}

	private function isQualified(){

		$session_arr = Session::get('infilawSurvey_array');

		$_this_income = explode(" - ", $session_arr['income']);
		//dd($session_arr);
		if (isset($_this_income) && count($_this_income) == 2) {
			$start_salary = str_replace(",", "", $_this_income[0]);
			$start_salary = str_replace("$", "", $start_salary);

			$end_salary = str_replace(",", "", $_this_income[1]);
			$end_salary = str_replace("$", "", $end_salary);

		}else{
			return false;
		}
		//dd($start_salary . ' '. $end_salary);
		if ($session_arr['school_name'] == "Florida Coastal School of Law") {
			if ($start_salary >= 40000 && $end_salary <= 53999) {
				return true;
			}
		}elseif ($session_arr['school_name'] == "Charlotte School of Law") {
			if ($start_salary >= 40000 && $end_salary <= 54999) {
				return true;
			}
		}

		return false;
	}

	private function setAmazonCode(){

		$session_arr = Session::get('infilawSurvey_array');
		$user_id     = Session::get('infilawSurvey_userId');



		$tt = TrackingTest::where(function ($query){
								$query->orWhere('pixel', '=', 'csl-survey');
								$query->orWhere('pixel', '=', 'fcsl-survey');
							})
							->where('email', $session_arr['email'])
							->whereNull('ad_num') //indicate that this person has already received amazon code
							->first();

		if (!isset($tt)) {
			return false;
		}

		$iac = InfilawAmazonCode::where('awarded_email', $session_arr['email'])->first();

		if(isset($iac)){
			return false;
		}

		$iac = InfilawAmazonCode::whereNull('awarded_email')
								->orWhere('awarded_email', '')
								->first();
			
		if (isset($iac)) {
			$iac->isu_id = $user_id;
			$iac->awarded_email = $session_arr['email'];
			$iac->save();

			//indicate that this person has already received amazon code
			$temp_tt = DB::connection('ldy')->statement('UPDATE `tracking_test` set `ad_num` = 1 where `id` = '.$tt->id);

			return true;
		}
		return false;
	}

	public function convert(){

		//$tmp = InfilawSurveyUser::where('is_finished', 1)->get();
		$tmp = InfilawSurveyUser::where('is_finished', 0)
								->where('email', '!=', "''")
								->get();

		foreach ($tmp as $key) {
			if (isset($key->practicing_attorney_experience) && !empty($key->practicing_attorney_experience)) {
				$arr =explode(",", $key->practicing_attorney_experience);
				//dd('here');
				$pae = InfilawPracticingAttorneyExperiences::whereIn('id', $arr)
					->select(DB::raw('GROUP_CONCAT(name SEPARATOR ",") as cnt'))
					->first();

				print_r($pae->cnt. "<br>");
			}else{
				print_r("NULLLLL<br>");
			}
		}
	}

	public function match(){

		$tmp = InfilawSurveyUser::where('is_finished', 1)
								->where(function($query){
									$query->orWhere('amazon_code_status', '=', 0);
									$query->orWhereNull('amazon_code_status');
								})
								->where('id', '>', 521)
								->take(80)
								->get();

		foreach ($tmp as $key) {
			$name_arr = explode(" ", $key->name);

			if (count($name_arr) != 2) {
				print_r("Could not find ". $key->name. "<br>");
				continue;
			}

			$tt = TrackingTest::where(function ($query){
								$query->orWhere('pixel', '=', 'csl-survey');
								$query->orWhere('pixel', '=', 'fcsl-survey');
							})
							->where('first_name', " ".$name_arr[0])
							->where('last_name', $name_arr[1])
							->whereNull('ad_num') //indicate that this person has already received amazon code
							->first();


			if (!isset($tt)) {
				print_r("Could not find ". $key->name. "<br>");
				continue;
			}

			

			$iac = InfilawAmazonCode::whereNull('awarded_email')
								->orWhere('awarded_email', '')
								->first();
			
			if (isset($iac)) {

				print_r("Added ". $key->id. " Email: ". $key->email. " <br>");
				$iac->isu_id = $key->id;
				$iac->awarded_email = $key->email;
				$iac->save();

				//indicate that this person has already received amazon code
				$temp_tt = DB::connection('ldy')->statement('UPDATE `tracking_test` set `ad_num` = 1 where `id` = '.$tt->id);

				$key->amazon_code_status = 1;
				$key->save();

			}

		}
		
	}

	public function dup(){

		$tmp = InfilawSurveyUser::where('is_finished', 1)
								->where(function($query){
									$query->orWhere('amazon_code_status', '=', 0);
									$query->orWhereNull('amazon_code_status');
								})

								->get();

		foreach ($tmp as $key) {
			$iac = InfilawAmazonCode::where('awarded_email', $key->email)
								->first();

			if (isset($iac)) {
				print_r("Dup ". $key->id. " Email: ". $key->email. " <br>");
				$key->amazon_code_status = 1;
				$key->save();
			}else{
				print_r("Not found ". $key->id. " Email: ". $key->email. " <br>");
			}


		}
		
	}
}
