<?php 

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt, Illuminate\Support\Facades\Auth;

use App\User, App\PlexussAdmin, App\AdPage, App\AdAffiliate, App\PlexussBannerCopy, App\CollegesInternationalTab, App\CollegeOverviewImages, App\Search, App\Scholarship, App\Scholarshipcms, App\ScholarshipProvider, App\UsersCustomQuestion, App\ScholarshipsUserApplied;
use App\CollegeRecommendationFilters;
use App\CollegeRecommendationFilterLogs;

class ScholarshipsController extends Controller{

	/************************************
	*  generate scholarships page view with relevent data
	*************************************/
	public function index ($is_api = NULL) { 
		$input = Request::all();

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['title'] = 'Plexuss Colleges | Scholarships';
		$data['currentPage'] = 'scholarships';

		$src="/images/profile/default.png";

		$user_id = null;
		$data['oneapp_status'] = null;

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;
			$user_id = $user->id;

			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}

            /* check user in email suppression list */
            $emailSuppressionList = EmailSuppressionList::where('uid', $user->id)->first();

            if (isset($emailSuppressionList)) {
                if ($emailSuppressionList['uid'] != '') {
                    array_push( $data['alerts'],
                        array(
                            'type' => 'hard',
                            'dur' => '10000',
                            'msg' => '<span class=\"pls-confirm-msg subcribe-msg\">Oops, seems like you are on our unsubscribe list. In order to get the best experience from Plexuss,</span> <span id=\"'.$emailSuppressionList['uid'].'\" class=\"subscribe-now\">Subscribe Now</span> <div class=\"loader loader-hidden\"></div>'
                        )
                    );
                }
            }

			if ( !$user->email_confirmed ) {
				array_push( $data['alerts'], 
					array(
						'img' => '/images/topAlert/envelope.png',
						'type' => 'hard',
						'dur' => '10000',
						'msg' => '<span class=\"pls-confirm-msg\">Please confirm your email to gain full access to Plexuss tools.</span> <span class=\"go-to-email\" data-email-domain=\"'.$data['email_provider'].'\"><span class=\"hide-for-small-only\">Go to</span><span class=\"show-for-small-only\">Confirm</span> Email</span> <span> | <span class=\"resend-email\">Resend <span class=\"hide-for-small-only\">Email</span></span></span> <span> | <span class=\"change-email\" data-current-email=\"'.$data['email'].'\">Change email <span class=\"hide-for-small-only\">address</span></span></span>'
					)
				);
			}
			// dd($user->id);

			//get if user OneApp step
			$appModel = new UsersCustomQuestion();
			$oneapp_status = $appModel->getUserStatus($user->id);
			$data['oneapp_status'] = $oneapp_status;

		}
		$data['profile_img_loc'] = $src;


		$schModel = new Scholarship();

		$fincount = 0;
		if($user_id){
			// $res1 = $schModel->getAllScholarshipsWithStatus($user_id, $input);
			// $fincount = $res1[0];
			// $res = $res1[1];

			$res = $schModel->getAllScholarshipsNotApplied($user_id, $input);

		}else{
			$res = $schModel->getAllScholarshipsFilters($input);
		}

		$data['fincount'] = $fincount;
		$data['scholarships'] =  $res;


		//if filters set in url query -- apply
		if(isset($input['rangeF'])){
			// dd($input['rangeF']);
			$data['rangeF'] = $input['rangeF'];
		}

		if (isset($is_api)) {
			return json_encode($data);
		}
		return View('private.scholarships.index', $data);
	}



	/****************************************
	* get scholarships ajax 
	* currently gets all -- may want to paginate
	*******************************************/
	public function getAllScholarships (){

		$schModel = new Scholarship();

		$res = $schModel->getAllScholarships();
		return $res;

	}
	
	
	
	public function getAllScholarshipsWithTargetting (){

		$schModel = new Scholarship();
		$res = $schModel->getAllScholarships();
		
		return $res;

	}

    public function getAllScholarshipsNotApplied() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $schModel = new Scholarship();

        $res = $schModel->getAllScholarshipsNotApplied($data['user_id'], []);

        return $res; 
    }

    // These scholarships have not yet been submitted yet
    public function getAllScholarshipsNotSubmitted($hashed_user_id = '') {
        if (!empty($hashed_user_id)) {
            try {
                $data = ['user_id' => Crypt::decrypt($hashed_user_id)];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);
        }

        $schModel = new Scholarship();

        $res = $schModel->getAllScholarshipsNotSubmitted($data['user_id'], []);

        return $res; 
    }


	/****************************************
	* get scholarships user is trying to submit
	* currently gets all -- may want to paginate
	*******************************************/
	public function getUserSubmitScholarships ($hashed_user_id = ''){
        if (!empty($hashed_user_id)) {
            try {
                $user_id = Crypt::decrypt($hashed_user_id);

            } catch (\Exception $e) {
                return 'bad user id';
                
            }
        } else {
    		$user_id = Request::get('user_id');

    		if(!isset($user_id)){
    			$user_id = Session::get('userinfo.id');
    		}
        }

		$schModel = new Scholarship();

		$res = $schModel->getUserSubmitScholarships($user_id);

		return $res;

	}



	/***************************************
	*	get user's scholarships
	*****************************************/
	public function getUsersScholarships(){

		$input = Request::all();

		$user_id = $input['user_id'];
		if(!isset($user_id) || $user_id == null){
			$user_id = Session::get('userinfo.id');
		}

		$schModel = new ScholarshipsUserApplied();
		$res = $schModel->getUsersScholarships($user_id);

		return $res;
	}



	/**********************************************
	* add schoalrships from Plexuss sales dash
	************************************************/
	public function addScholarshipSales (){

		$input = Request::all();
		$provId = $input['provider_id'];

		//if new provider
		if(!isset($provId)){

			//insert provider
			$provModel = new ScholarshipProvider();
			$provModel->company_name = $input['provider_name'];
			$provModel->contact_fname = $input['contact_fname'];
			$provModel->contact_lname = $input['contact_lname'];
			$provModel->phone = $input['provider_phone'];
			$provModel->email = $input['provider_email'];
			$provModel->address = $input['provider_address'];
			$provModel->city = $input['provider_city'];
			$provModel->state = $input['provider_state'];
			$provModel->zip = $input['provider_zip'];
			$provModel->country_id = $input['provider_country'];

			$provModel->save();
			$provId = $provModel->id;
		}

		//insert scholarship_verified
		//if sales knows the submission id from scholarship_submission table, that will be filled, otherwise, it will be null (no submission is associated with it)
		$schModel = new Scholarship();
		$schModel->scholarship_title = $input['scholarship_name'];
		$schModel->deadline = $input['deadline'];
		$schModel->recurring = $input['recurring'];
		$schModel->num_of_awards = $input['numberof'];
		$schModel->max_amount = $input['amount'];
		$schModel->website = $input['website'];
		$schModel->description = $input['description'];
		$schModel->submission_id = isset($input['submission_id']) ? $input['submission_id'] : null;
		$schModel->provider_id = $provId;
		
		$schModel->save();
		
		

		$res = array();
		$res['id'] = $schModel->id;
		$res['scholarship_name'] = $schModel->scholarship_title;
		$res['website'] = $schModel->website;
		$res['numberof'] = $schModel->num_of_awards;
		$res['deadline'] = $schModel->deadline;
		// dd($schModel->created_at);
		$date = Carbon::parse($schModel->created_at);
		$res['created_at'] = $date->format('m/d/Y');
		$res['amount'] = $schModel->max_amount;
		$res['description'] = $schModel->description;

		if(!isset($provModel)){
			$provMod = new ScholarshipProvider();
			$provModel = $provMod->getProvider($input['provider_id']);
		}
		if(!isset($provModel)) return 'error';

		$res['provider_name'] = $provModel->company_name;
		$res['contact_fname'] = $provModel->contact_fname;
		$res['contact_lsname'] = $provModel->contact_lname;
		$res['provider_phone'] = $provModel->phone;
		$res['provider_email'] = $provModel->email;
		$res['provider_address'] = $provModel->address;
		$res['provider_city'] = $provModel->city;
		$res['provider_state'] = $provModel->state;
		$res['provider_zip'] = $provModel->zip;
		$res['provider_country'] = $provModel->country_id;
		
		
		return $res;
		//return "Ok";
	}

	
	
	
	/**************************************************
	* edit scholarship from sales
	************************************************/
	public function editScholarshipSales(){

		$input = Request::all();
		if($input['step']=="1"){
			// //insert scholarship_verified
			// //if sales knows the submission id from scholarship_submission table, that will be filled, otherwise, it will be null (no submission is associated with it)
			$schobj = array();
			$schObj['scholarship_title'] = $input['scholarship_name'];
			
			$date = Carbon::parse($input['deadline']);
			$schObj['deadline'] = $date->format('m/d/Y');
			$input['deadline'] = $schObj['deadline'];
	
			$schObj['recurring'] = $input['recurring'];
			$schObj['num_of_awards'] = $input['numberof'];
			$schObj['max_amount'] = $input['amount'];
			$schObj['website'] = $input['website'];
			$schObj['description'] = $input['description'];
			$schObj['submission_id'] = isset($input['submission_id']) ? $input['submission_id'] : null;
						
			$schModel = new Scholarship();
			$schModel->updateOrCreate(['id'=>$input['id']], $schObj);
		}else if($input['step']=="2"){
			$tab_name = $input['tab_name'];
			
			if ($tab_name == null) {
				return;
			}
			
			$crf = new CollegeRecommendationFilters;
			$crfl = new CollegeRecommendationFilterLogs;
			$scholarship_id = $input['id'];
			
			
			switch ($tab_name) {
				case 'location':
					
					$crfl->clearFilter_scholarship("location",$scholarship_id);
					$crf->clearFilter_scholarship("location",$scholarship_id);
					
					$all_country_filter = $this->inputIsset($input, 'all_country_filter');
					$include_country_filter = $this->inputIsset($input, 'include_country_filter');
					$exclude_country_filter = $this->inputIsset($input, 'exclude_country_filter');
					$country = $input['country'];
					if(isset($country) && sizeof($country)>0){
						$this->saveFilterTemplate2($all_country_filter, $include_country_filter,'', $crf, 'location', 'country', $country, $crfl,$scholarship_id);
					}
					
					$all_state_filter = $this->inputIsset($input, 'all_state_filter');
					$include_state_filter = $this->inputIsset($input, 'include_state_filter');
					$exclude_state_filter = $this->inputIsset($input, 'exclude_state_filter');
					$state = $input['state'];
					if(isset($state) && sizeof($state)>0){
						$this->saveFilterTemplate2($all_state_filter, $include_state_filter,'', $crf, 'location', 'state', $state, $crfl,$scholarship_id);
					}
							
					$all_city_filter = $this->inputIsset($input, 'all_city_filter');
					$include_city_filter = $this->inputIsset($input, 'include_city_filter');
					$exclude_city_filter = $this->inputIsset($input, 'exclude_city_filter');
					$city = $input['city'];
					if(isset($city) && sizeof($city)>0){
						$this->saveFilterTemplate2($all_city_filter, $include_city_filter,'', $crf, 'location', 'city', $city, $crfl,$scholarship_id);
					}
					break;
				case 'startDateTerm':
					if(isset($input["startDateTerm"]) && $input["startDateTerm"]!=''){
						$crfl->clearFilter_scholarship("startDateTerm", $scholarship_id);
						$crf->clearFilter_scholarship("startDateTerm", $scholarship_id);
					
						$startDateTerm_filter = $input['startDateTerm'];
						//print_r($startDateTerm_filter);
						$this->saveFilterTemplate2('false', 'true', '', $crf,'startDateTerm', 'startDateTerm', $startDateTerm_filter, $crfl,$scholarship_id);
					}
					break;
				case 'financial':
					$crfl->clearFilter_scholarship("financial", $scholarship_id);
					$crf->clearFilter_scholarship("financial", $scholarship_id);
					if(isset($input['financial'])){
						//get filter
						$financials_filter = $input["financial"];
							
						//exclude the $interested_in_aid fiter
						$financials_filter = array_diff($financials_filter, array('interested_in_aid'));
						$this->saveFilterTemplate2('false', 'true', '', $crf, 'financial', 'financial', $financials_filter, $crfl,$scholarship_id);
					}
					if(isset($input['interested_in_aid'])) {
						$this->saveFilterTemplate1('1','', $crf, 'financial', 'interested_in_aid', $crfl,$scholarship_id);
					}
					break;
				case 'typeofschool':
					if (isset($input['both_typeofschool']) || isset($input['online_only_typeofschool']) || isset($input['campus_only_typeofschool'])) {
						$crfl->clearFilter_scholarship("typeofschool", $scholarship_id);
						$crf->clearFilter_scholarship("typeofschool", $scholarship_id);
						
						$both_typeofschool = $this->inputIsset($input, 'both_typeofschool');
						$online_only_typeofschool = $this->inputIsset($input, 'online_only_typeofschool');
						$campus_only_typeofschool = $this->inputIsset($input, 'campus_only_typeofschool');
						
						$interested_school_type = '0';
						if ($both_typeofschool == 'true') {
							$this->saveFilterTemplate1("2",'', $crf, "typeofschool", 'interested_school_type', $crfl,$scholarship_id);
						}elseif ($online_only_typeofschool == 'true') {
							$this->saveFilterTemplate1("1",'', $crf, "typeofschool", 'interested_school_type', $crfl,$scholarship_id);
						}elseif ($campus_only_typeofschool == "true") {
							$this->saveFilterTemplate1("0",'', $crf, "typeofschool", 'interested_school_type', $crfl,$scholarship_id);
						}else{
							$this->saveFilterTemplate1("0",'', $crf, "typeofschool", 'interested_school_type', $crfl,$scholarship_id);
						}
						
						
						
					}
					break;
				case 'majorDeptDegree':
					$crfl->clearFilter_scholarship("majorDeptDegree", $scholarship_id);
					$crf->clearFilter_scholarship("majorDeptDegree", $scholarship_id);
					
					/*$all_department_filter = $this->inputIsset($input, 'all_department_filter');
					$include_department_filter = $this->inputIsset($input, 'include_department_filter');
					$exclude_department_filter = $this->inputIsset($input, 'exclude_department_filter');
					$department1 = $this->inputIsset($input, 'department');
					if($department1==true){
						$department[] = $input['department'];
					}else{
						$department[] = '';
					}
					
					$this->saveFilterTemplate2($all_department_filter, $include_department_filter, '', $crf, "major", 'department', $department, $crfl,$scholarship_id);
					break;*/
					//echo "<pre>";print_r($input['major_filter_array']);echo "</pre>";
					//echo "<pre>";print_r($input['majorsub_filter_array']);echo "</pre>";
					
					$mainarr = $input['major_filter_array'];
					$subarr = $input['majorsub_filter_array'];
					$arry["data"] = array();
					
					//$key = array_search(18, array_column($mainarr, 'pid'));
					//print_r($subarr[$key]);
					
					/*
					for($i =0;$i<sizeof($subarr);$i++){
							$suba = $subarr[$i]["degreelevel"];
							for($j =0;$j<sizeof($suba);$j++){
								$arry["data"][] = array("department_id"=>$subarr[$i]["pid"],"major_id"=>$subarr[$i]["id"],"degree_id"=>$suba[$j],"in_ex"=>"");
							}
						
					}
					*/
					
					for($i =0;$i<sizeof($mainarr);$i++){
							if(sizeof($mainarr[$i]["degreelevel"])!=0){
								for($k=0;$k<sizeof($mainarr[$i]["degreelevel"]);$k++) {
									$arry["data"][] = array("department_id"=>$mainarr[$i]["id"],"major_id"=>'',"degree_id"=>$mainarr[$i]["degreelevel"][$k],"in_ex"=>$mainarr[$i]["type"]);
								}
							}else{
							for($j =0;$j<sizeof($subarr);$j++){
								if($mainarr[$i]["id"] == $subarr[$j]["pid"]){
									if(sizeof($subarr[$j]["degreelevel"])!=0){
										for($k=0;$k < sizeof($subarr[$j]["degreelevel"]); $k++) {
											$arry["data"][] = array("department_id"=>$subarr[$j]["pid"],"major_id"=>$subarr[$j]["id"],"degree_id"=>$subarr[$j]["degreelevel"][$k],"in_ex"=>$mainarr[$i]["type"]);
										}
									}
									else{
										for($k=0;$k<sizeof($mainarr[$i]["degreelevel"]);$k++) {
											$arry["data"][] = array("department_id"=>$mainarr[$i]["id"],"major_id"=>'',"degree_id"=>$mainarr[$i]["degreelevel"][$k],"in_ex"=>$mainarr[$i]["type"]);
										}
									}
								}
							}
							}
						
					}
					
					
					$this->saveFilterForMajor($data='', $arry, $crf, $crfl,$scholarship_id);
					
				case 'scores':
					$crfl->clearFilter_scholarship("scores", $scholarship_id);
					$crf->clearFilter_scholarship("scores", $scholarship_id);
					
					$gpaMin_filter = $this->inputIsset($input, 'gpaMin_filter');
					$gpaMax_filter = $this->inputIsset($input, 'gpaMax_filter');
					$satMin_filter = $this->inputIsset($input, 'satMin_filter');
					$satMax_filter = $this->inputIsset($input, 'satMax_filter');
					$actMin_filter = $this->inputIsset($input, 'actMin_filter');
					$actMax_filter = $this->inputIsset($input, 'actMax_filter');
					$toeflMin_filter = $this->inputIsset($input, 'toeflMin_filter');
					$toeflMax_filter = $this->inputIsset($input, 'toeflMax_filter');
					$ieltsMin_filter = $this->inputIsset($input, 'ieltsMin_filter');
					$ieltsMax_filter = $this->inputIsset($input, 'ieltsMax_filter');
					
					
					//gpa
					if($gpaMin_filter=="true"){
						$this->saveFilterTemplate1($input['gpaMin_filter'], '', $crf, "scores", 'gpaMin_filter', $crfl,$scholarship_id);
					}
					if($gpaMax_filter=="true"){
						$this->saveFilterTemplate1($input['gpaMax_filter'], '', $crf, "scores", 'gpaMax_filter', $crfl,$scholarship_id);
					}
					if($satMin_filter=="true"){
						$this->saveFilterTemplate1($input['satMin_filter'], '', $crf, "scores", 'satMin_filter', $crfl,$scholarship_id);
					}
					if($satMax_filter=="true"){
						$this->saveFilterTemplate1($input['satMax_filter'], '', $crf, "scores", 'satMax_filter', $crfl,$scholarship_id);
					}
					if($actMin_filter=="true"){
						$this->saveFilterTemplate1($input['actMin_filter'], '', $crf, "scores", 'actMin_filter', $crfl,$scholarship_id);
					}
					if($actMax_filter=="true"){
						$this->saveFilterTemplate1($input['actMax_filter'], '', $crf, "scores", 'actMax_filter', $crfl,$scholarship_id);
					}
					if($toeflMin_filter=="true"){
						$this->saveFilterTemplate1($input['toeflMin_filter'], '', $crf, "scores", 'toeflMin_filter', $crfl,$scholarship_id);
					}
					if($toeflMax_filter=="true"){
						$this->saveFilterTemplate1($input['toeflMax_filter'], '', $crf, "scores", 'toeflMax_filter', $crfl,$scholarship_id);
					}
					if($ieltsMin_filter=="true"){
						$this->saveFilterTemplate1($input['ieltsMin_filter'], '', $crf, "scores", 'ieltsMin_filter', $crfl,$scholarship_id);
					}
					if($ieltsMax_filter=="true"){
						$this->saveFilterTemplate1($input['ieltsMax_filter'], '', $crf, "scores", 'ieltsMax_filter', $crfl,$scholarship_id);
					}
					
					/*if($gpaMin_filter=="true" && $gpaMax_filter =="true"){
						$this->saveFilterTemplate1($input['gpaMin_filter'].','.$input['gpaMax_filter'], '', $crf, "scores", 'gpa_filter', $crfl,$scholarship_id);
					}elseif($gpaMin_filter=="true"){
						$this->saveFilterTemplate1($input['gpaMin_filter'].',4', '', $crf, "scores", 'gpa_filter', $crfl,$scholarship_id);
					}elseif($gpaMax_filter=="true"){
						$this->saveFilterTemplate1('0,'.$input['gpaMax_filter'], '', $crf, "scores", 'gpa_filter', $crfl,$scholarship_id);
					}*/
									
					//act/sat
					/*if($satMin_filter=="true" && $satMax_filter =="true"){
						$this->saveFilterTemplate1('sat,'.$input['satMin_filter'].','.$input['satMax_filter'], '', $crf, "scores", 'sat_act', $crfl,$scholarship_id);
					}elseif($satMin_filter=="true"){
						$this->saveFilterTemplate1('sat,'.$input['satMin_filter'].',2400', '', $crf, "scores", 'sat_act', $crfl,$scholarship_id);
					}elseif($satMax_filter=="true"){
						$this->saveFilterTemplate1('sat,0,'.$input['satMax_filter'], '', $crf, "scores", 'sat_act', $crfl,$scholarship_id);
					}
					
					if($actMin_filter=="true" && $actMax_filter =="true"){
						$this->saveFilterTemplate1('act,'.$input['actMin_filter'].','.$input['actMax_filter'], '', $crf, "scores", 'sat_act', $crfl,$scholarship_id);
					}elseif($actMin_filter=="true"){
						$this->saveFilterTemplate1('act,'.$input['actMin_filter'].',36', '', $crf, "scores", 'sat_act', $crfl,$scholarship_id);
					}elseif($actMax_filter=="true"){
						$this->saveFilterTemplate1('act,0,'.$input['actMax_filter'], '', $crf, "scores", 'sat_act', $crfl,$scholarship_id);
					}*/
					
					//toefl/ielts
					/*if ($toeflMin_filter=="true" && $toeflMax_filter=="true"){
						$this->saveFilterTemplate1('toefl,'.$input['toeflMin_filter'].','.$input['toeflMax_filter'],'', $crf, "scores", 'ielts_toefl', $crfl,$scholarship_id);
					}elseif($toeflMin_filter=="true"){
						$this->saveFilterTemplate1('toefl,'.$input['toeflMin_filter'].',120','', $crf, "scores", 'ielts_toefl', $crfl,$scholarship_id);
					}elseif($toeflMax_filter=="true"){
						$this->saveFilterTemplate1('toefl,0,'.$input['toeflMax_filter'],'', $crf, "scores", 'ielts_toefl', $crfl,$scholarship_id);
					}
					if ($ieltsMin_filter=="true" && $ieltsMax_filter=="true"){
						$this->saveFilterTemplate1('ielts,'.$input['ieltsMin_filter'].','.$input['ieltsMax_filter'],'', $crf, "scores", 'ielts_toefl', $crfl,$scholarship_id);
					}elseif($ieltsMin_filter=="true"){
						$this->saveFilterTemplate1('ielts,'.$input['ieltsMin_filter'].',9','', $crf, "scores", 'ielts_toefl', $crfl,$scholarship_id);
					}elseif($ieltsMax_filter=="true"){
						$this->saveFilterTemplate1('ielts,0,'.$input['ieltsMax_filter'],'', $crf, "scores", 'ielts_toefl', $crfl,$scholarship_id);
					}*/
					
					break;
				case 'uploads':	
					$transcript_filter = $this->inputIsset($input, 'transcript_filter');
					$financialInfo_filter = $this->inputIsset($input, 'financialInfo_filter');
					$ielts_fitler = $this->inputIsset($input, 'ielts_fitler');
					$toefl_filter = $this->inputIsset($input, 'toefl_filter');
					$resume_filter = $this->inputIsset($input, 'resume_filter');
					$passport_filter = $this->inputIsset($input, 'passport_filter');
					$essay_filter = $this->inputIsset($input, 'essay_filter');
					$other_filter = $this->inputIsset($input, 'other_filter');
					
					if ($transcript_filter == 'false' || $financialInfo_filter == 'false' || $ielts_fitler == 'false' || $toefl_filter == 'false' || $resume_filter == 'false') {
						$crfl->clearFilter_scholarship("uploads", $scholarship_id);
						$crf->clearFilter_scholarship("uploads", $scholarship_id);
					
						$qry_id = $this->saveAdminFilterRecommendation($crf, 'include', "uploads", 'uploads',$scholarship_id);
						$uploads = array();
			
						if ($transcript_filter == 'true') {
							$uploads[] = 'transcript_filter';
						}
						if ($financialInfo_filter == 'true') {
							$uploads[] = 'financialInfo_filter';
						}
						if ($ielts_fitler == 'true') {
							$uploads[] = 'ielts_fitler';
						}
						if ($toefl_filter == 'true') {
							$uploads[] = 'toefl_filter';
						}
						if ($resume_filter == 'true') {
							$uploads[] = 'resume_filter';
						}
						if ($passport_filter == 'true') {
							$uploads[] = 'passport_filter';
						}
						if ($essay_filter == 'true') {
							$uploads[] = 'essay_filter';
						}
						if ($other_filter == 'true') {
							$uploads[] = 'other_filter';
						}
						
						$arr = array();
						foreach ($uploads as $key => $value) {
							$tmp = array();
							$tmp['val'] = $value;
							$tmp['rec_filter_id'] = $qry_id;
							$arr[] = $tmp;
						}
			
						$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
					}
					break;
				case 'demographic':	
					$ageMin_filter = $this->inputIsset($input, 'ageMin_filter');
					$ageMax_filter = $this->inputIsset($input, 'ageMax_filter');
					$all_gender_filter = $this->inputIsset($input, 'all_gender_filter');
					$male_only_filter = $this->inputIsset($input, 'male_only_filter');
					$female_only_filter = $this->inputIsset($input, 'female_only_filter');
					$all_eth_filter = $this->inputIsset($input, 'all_eth_filter');
					$include_eth_filter = $this->inputIsset($input, 'include_eth_filter');
					$exclude_eth_filter = $this->inputIsset($input, 'exclude_eth_filter');
					$ethnicity = $input['ethnicity'];
					$all_rgs_filter = $this->inputIsset($input, 'all_rgs_filter');
					$include_rgs_filter = $this->inputIsset($input, 'include_rgs_filter');
					$exclude_rgs_filter = $this->inputIsset($input, 'exclude_rgs_filter');
					$religion = $input['religion'];
					
					$crfl->clearFilter_scholarship("demographic", $scholarship_id);
					$crf->clearFilter_scholarship("demographic", $scholarship_id);
					
					$this->saveFilterTemplate1($input['ageMin_filter'] , '', $crf, "demographic", 'ageMin_filter', $crfl,$scholarship_id);
					$this->saveFilterTemplate1($input['ageMax_filter'] , '', $crf, "demographic", 'ageMax_filter', $crfl,$scholarship_id);
					
					if ($all_gender_filter == 'false') {
						$qry_id = $this->saveAdminFilterRecommendation($crf, 'include', "demographic", 'gender',$scholarship_id);
						if ($male_only_filter == 'true') {
							$gender = 'male';
						}else{
							$gender = 'female';
						}
			
						$arr = array();
						$tmp = array();
						$tmp['val'] = $gender;
						$tmp['rec_filter_id'] = $qry_id;
						$arr[] = $tmp;
			
						$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
					}
					
					$this->saveFilterTemplate2($all_eth_filter, $include_eth_filter, '', $crf, "demographic", 'include_eth_filter', $ethnicity, $crfl,$scholarship_id);
					$this->saveFilterTemplate2($all_rgs_filter, $include_rgs_filter, '', $crf, "demographic", 'include_rgs_filter', $religion, $crfl,$scholarship_id);
					break;
				case 'educationLevel':	
					//if (isset($input['educationLevel'])) {
					
						$hsUsers_filter = $this->inputIsset($input, 'hsUsers_filter');
						$collegeUsers_filter = $this->inputIsset($input, 'collegeUsers_filter');
					
						//if ($hsUsers_filter == 'tre' || $collegeUsers_filter == 'false') {
							$crfl->clearFilter_scholarship("educationLevel", $scholarship_id);
							$crf->clearFilter_scholarship("educationLevel", $scholarship_id);
							
							$qry_id = $this->saveAdminFilterRecommendation($crf, 'include', "educationLevel", 'educationLevel',$scholarship_id);
			
							$arr = array();
							$tmp = array();
							
							if ($hsUsers_filter == 'true') {
								$tmp['val'] = 'hsUsers_filter';
								$tmp['rec_filter_id'] = $qry_id;
								$arr[] = $tmp;
							}
							
							if ($collegeUsers_filter == 'true') {
								$tmp['val'] = 'collegeUsers_filter';
								$tmp['rec_filter_id'] = $qry_id;
								$arr[] = $tmp;
							}
							
							$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
						//}
					//}
					break;
				case 'militaryAffiliation':	
					//if (isset($input['militaryAffiliation'])) {
						$crfl->clearFilter_scholarship("militaryAffiliation", $scholarship_id);
						$crf->clearFilter_scholarship("militaryAffiliation", $scholarship_id);
				
						$inMilitary_filter = $input['inMilitary'];
						$militaryAffiliation_filter = $input['militaryAffiliation'];
						
						$inMilitary_filter1 = $this->inputIsset($input, 'inMilitary');
						$militaryAffiliation_filter1 = $this->inputIsset($input, 'militaryAffiliation');
						
						$this->saveFilterTemplate1($input['inMilitary'], '', $crf, "militaryAffiliation", 'inMilitary', $crfl,$scholarship_id);
						if (!empty($militaryAffiliation_filter)) {
							$this->saveFilterTemplate2('false', 'true', '', $crf, "militaryAffiliation", 'militaryAffiliation', $militaryAffiliation_filter, $crfl,$scholarship_id);
						}
					//}
					break;
				case 'profileCompletion':	
					$crfl->clearFilter_scholarship("profileCompletion", $scholarship_id);
					$crf->clearFilter_scholarship("profileCompletion", $scholarship_id);
					
					$profileCompletion_filter = $input['profileCompletion'];
					$this->saveFilterTemplate1($profileCompletion_filter , '', $crf, "profileCompletion", 'profileCompletion', $crfl,$scholarship_id);
					break;
				default:
					# code...
					break;
			}
			
		}else if($input['step']=="3"){
			//check to see if provider exist -- if so update else create
			//provider update
			$provObj = array();
			$provObj['company_name'] = $input['provider_name'];
			$provObj['contact_fname'] = $input['contact_fname'];
			$provObj['contact_lname'] = $input['contact_lname'];
			$provObj['phone'] = $input['provider_phone'];
			$provObj['email'] = $input['provider_email'];
			$provObj['address'] = $input['provider_address'];
			$provObj['city'] = $input['provider_city'];
			$provObj['state'] = $input['provider_state'];
			$provObj['zip'] = $input['provider_zip'];
			$provObj['country_code'] = $input['provider_country'];
	
			$provModel = new ScholarshipProvider();
			$provModel->updateOrCreate(['id'=>$input['provider_id']], $provObj);
			
			$schObj['provider_id'] = $input['provider_id'];
			$schModel = new Scholarship();
			$schModel->updateOrCreate(['id'=>$input['id']], $schObj);
		}
		
		return $input;
	}


	/**********************************************
	*  delete a scholarship
	**********************************************/
	public function deleteScholarshipSales (){

		$id = Request::get('id');

		$schModel = new Scholarship();
		$res = $schModel->deleteScholarship($id);

		if($res != 1){
			return 'error';
		}

		return $id;

	}


	/********************************/
	public function searchScholarships(){

		$term = Request::get('term');

		$schModel = new Scholarship();
		$res = $schModel->searchScholarships($term);

		return $res;
	}


	/***********************
	* get list of providers
	******************************/
	public function getAllProviders (){

		$provModel = new ScholarshipProvider();

		$res = $provModel::all();

		return $res;
	}



	/**************
	* adds a scholarsship to user's list of scholarships with a status: finish, submitted, accepted, rejected
	**************************/
	public function queueScholarship($input = NULL){

		if (!isset($input)) {
			$input = Request::all();
		}

		$user_id = isset($input['user_id']) ? $input['user_id'] : null;
		if(!isset($user_id) || $user_id == null){
			$user_id = Session::get('userinfo.id');
		}

		$scholarship = $input['scholarship'];
		$status = $input['status'];

		$schModel = new ScholarshipsUserApplied();

		$sch = array();
		$sch['user_id'] = $user_id;
		$sch['scholarship_id'] = $scholarship;
		$sch['status'] = $status;
		// $res = $schUserApplied->save();
		$res = $schModel->updateOrCreate(['user_id'=>$user_id, 'scholarship_id'=>$scholarship ], $sch);

		if($res == false){
			return "error";
		}

		return $input;

	}
	
	/**
	 * This method returns if the input is set or not
	 * @ret boolean
	 */
	private function inputIsset($input, $val){

		if (isset($input[$val])) {
			return 'true';//$input[$val];
		}else{
			return 'false';
		}
	}

	private function saveFilterTemplate2($all_filter, $include_filter, $data='', $crf, $tab_name, $name, $val_arr, $crfl, $scholarship_id){
		if ($all_filter === 'false') {
			if ($include_filter === 'true') {
				$qry_id =$this->saveAdminFilterRecommendation($crf, 'include', $tab_name, $name, $scholarship_id);
			}else{
				$qry_id = $this->saveAdminFilterRecommendation($crf, 'exclude', $tab_name, $name, $scholarship_id);
			}
			
			$arr = array();
			foreach ($val_arr as $key => $value) {
				$tmp = array();
				$tmp['val'] = $value;
				$tmp['rec_filter_id'] = $qry_id;
				$arr[] = $tmp;
			}
			$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
		}
	}
	
	private function saveFilterTemplate1($cat_name , $data='', $crf, $tab_name, $name, $crfl, $scholarship_id){
		if ($cat_name != '') {
			$qry_id = $this->saveAdminFilterRecommendation($crf, 'include', $tab_name, $name, $scholarship_id);
			
			$arr = array();
			$tmp = array();
			$tmp['val'] = $cat_name;
			$tmp['rec_filter_id'] = $qry_id;
			$arr[] = $tmp;
			
			$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
		}
	}
	
	private function saveAdminFilterRecommendation($crf, $type, $category, $name, $scholarship_id){

		$agency_id = NULL;
		$org_branch_id = NULL;
		$org_portal_id = NULL;
		$aor_id = NULL;
		$org_school_id = NULL;
		
		$crf = $crf->saveFilter_scholarship($agency_id, $org_school_id, $org_branch_id, $type, $category, $name, $org_portal_id, $aor_id, $scholarship_id);
		return $crf->id;
	}
	
	
	private function saveAdminFilterLogRecommendation($crfl ,$qry_id = null, $arr = null){
		$crfl->saveFilterLog($arr, $qry_id);
	}

	private function saveFilterForMajor($data, $input, $crf, $crfl,$scholarship_id){

		$include_bool = false;
		$exclude_bool = false;
		$include_arr  = array();
		$exclude_arr  = array();

		if( isset($input['data']) && !empty($input['data']) ){
			foreach ($input['data'] as $key) {
				if ($include_bool == false && $key['in_ex'] == 'include') {
					$include_qry_id =$this->saveAdminFilterRecommendation($crf, $key['in_ex'], 'majorDeptDegree', 'majorDeptDegree',$scholarship_id);
					$include_bool = true;
				}
				
				if ($exclude_bool == false && $key['in_ex'] == 'exclude') {
					$exclude_qry_id =$this->saveAdminFilterRecommendation($crf, $key['in_ex'], 'majorDeptDegree', 'majorDeptDegree',$scholarship_id);
					$exclude_bool = true;
				}

				if ($key['in_ex'] == 'include') {
					$include_arr[] = $key;
				}

				if ($key['in_ex'] == 'exclude') {
					$exclude_arr[] = $key;
				}

			}
		}

		$arr = array();
		if (isset($include_qry_id)) {
			foreach ($include_arr as $key) {
				$tmp = array();
				$tmp['val'] = $key['department_id'].','.$key['major_id'].','.$key['degree_id'];
				$tmp['rec_filter_id'] = $include_qry_id;
				$arr[] = $tmp;
			}

			$this->saveAdminFilterLogRecommendation($crfl, $include_qry_id, $arr);
		}

		$arr = array();
		if (isset($exclude_qry_id)) {
			foreach ($exclude_arr as $key) {
				$tmp = array();
				$tmp['val'] = $key['department_id'].','.$key['major_id'].','.$key['degree_id'];
				$tmp['rec_filter_id'] = $exclude_qry_id;
				$arr[] = $tmp;
			}

			$this->saveAdminFilterLogRecommendation($crfl, $exclude_qry_id, $arr);
		}

	}
	
	function searchForId($id, $array) {
	   foreach ($array as $key => $val) {
		   if ($val['uid'] === $id) {
			   return $key;
		   }
	   }
	   return null;
	} 
	
	
	/*******************************************/
	public function getScholarshipsCms (){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$schModel = new Scholarshipcms();
		$res = $schModel->getScholarshipsCms($data["org_school_id"]);
		return $res;

	}
	
	/**********************************************
	* add schoalrships from Plexuss sales dash
	************************************************/
	public function addScholarshipCms (){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$schModel = new Scholarshipcms();
		
		$checkcount = $schModel->checkScholarshipExist($data['org_school_id']);
		
		$input = Request::all();
		if($input['step']=="1"){
			//if($checkcount ==0){
				$schModel->scholarship_title = $input['scholarship_name'];
				$schModel->scholarshipsub_title = $input['scholarshipsub_name'];
				$schModel->deadline = $input['deadline'];
				$schModel->max_amount = $input['amount'];
				$schModel->description = $input['description'];
				$schModel->submission_id = null;
				$schModel->provider_id = null;
				$schModel->college_id = $data['org_school_id'];
				$schModel->save();
				return  $schModel->id;
			/*}else{
				$schObj['scholarship_title'] = $input['scholarship_name'];
				$schObj['scholarshipsub_title'] = $input['scholarshipsub_name'];
				$schObj['deadline'] = $input['deadline'];
				$schObj['max_amount'] = $input['amount'];
				$schObj['description'] = $input['description'];
				$schModel->updateOrCreate(['college_id'=> $data['org_school_id']], $schObj);
				$checkid = $schModel->checkScholarship($data['org_school_id']);
				return $checkid->id;
			}*/
		}else if($input['step']=="2"){
			$tab_name = $input['tab_name'];
			
			if ($tab_name == null) {
				return;
			}
			//print_r($input);
			
			$crf = new CollegeRecommendationFilters;
			$crfl = new CollegeRecommendationFilterLogs;
			$scholarship_id = $input['id'];
			
			switch ($tab_name) {
				case 'location':
					
					$crfl->clearFilter_scholarship("location",$scholarship_id);
					$crf->clearFilter_scholarship("location",$scholarship_id);
					
					$all_country_filter = $this->inputIsset($input, 'all_country_filter');
					$include_country_filter = $this->inputIsset($input, 'include_country_filter');
					$exclude_country_filter = $this->inputIsset($input, 'exclude_country_filter');
					$country = $input['country'];
					if(isset($country) && sizeof($country)>0){
						$this->saveFilterTemplate2($all_country_filter, $include_country_filter,'', $crf, 'location', 'country', $country, $crfl,$scholarship_id);
					}
					
					$all_state_filter = $this->inputIsset($input, 'all_state_filter');
					$include_state_filter = $this->inputIsset($input, 'include_state_filter');
					$exclude_state_filter = $this->inputIsset($input, 'exclude_state_filter');
					$state = $input['state'];
					if(isset($state) && sizeof($state)>0){
						$this->saveFilterTemplate2($all_state_filter, $include_state_filter,'', $crf, 'location', 'state', $state, $crfl,$scholarship_id);
					}
							
					$all_city_filter = $this->inputIsset($input, 'all_city_filter');
					$include_city_filter = $this->inputIsset($input, 'include_city_filter');
					$exclude_city_filter = $this->inputIsset($input, 'exclude_city_filter');
					$city = $input['city'];
					if(isset($city) && sizeof($city)>0){
						$this->saveFilterTemplate2($all_city_filter, $include_city_filter,'', $crf, 'location', 'city', $city, $crfl,$scholarship_id);
					}
					break;
				case 'startDateTerm':
					if(isset($input["startDateTerm"]) && $input["startDateTerm"]!=''){
						$crfl->clearFilter_scholarship("startDateTerm", $scholarship_id);
						$crf->clearFilter_scholarship("startDateTerm", $scholarship_id);
					
						$startDateTerm_filter = $input['startDateTerm'];
						//print_r($startDateTerm_filter);
						$this->saveFilterTemplate2('false', 'true', '', $crf,'startDateTerm', 'startDateTerm', $startDateTerm_filter, $crfl,$scholarship_id);
					}
					break;
				case 'financial':
					$crfl->clearFilter_scholarship("financial", $scholarship_id);
					$crf->clearFilter_scholarship("financial", $scholarship_id);
					if(isset($input['financial'])){
						//get filter
						$financials_filter = $input["financial"];
							
						//exclude the $interested_in_aid fiter
						$financials_filter = array_diff($financials_filter, array('interested_in_aid'));
						$this->saveFilterTemplate2('false', 'true', '', $crf, 'financial', 'financial', $financials_filter, $crfl,$scholarship_id);
					}
					if(isset($input['interested_in_aid'])) {
						$this->saveFilterTemplate1('1','', $crf, 'financial', 'interested_in_aid', $crfl,$scholarship_id);
					}
					break;
				case 'typeofschool':
					if (isset($input['both_typeofschool']) || isset($input['online_only_typeofschool']) || isset($input['campus_only_typeofschool'])) {
						$crfl->clearFilter_scholarship("typeofschool", $scholarship_id);
						$crf->clearFilter_scholarship("typeofschool", $scholarship_id);
						
						$both_typeofschool = $this->inputIsset($input, 'both_typeofschool');
						$online_only_typeofschool = $this->inputIsset($input, 'online_only_typeofschool');
						$campus_only_typeofschool = $this->inputIsset($input, 'campus_only_typeofschool');
						
						$interested_school_type = '0';
						if ($both_typeofschool == 'true') {
							$this->saveFilterTemplate1("2",'', $crf, "typeofschool", 'interested_school_type', $crfl,$scholarship_id);
						}elseif ($online_only_typeofschool == 'true') {
							$this->saveFilterTemplate1("1",'', $crf, "typeofschool", 'interested_school_type', $crfl,$scholarship_id);
						}elseif ($campus_only_typeofschool == "true") {
							$this->saveFilterTemplate1("0",'', $crf, "typeofschool", 'interested_school_type', $crfl,$scholarship_id);
						}else{
							$this->saveFilterTemplate1("0",'', $crf, "typeofschool", 'interested_school_type', $crfl,$scholarship_id);
						}
						
						
						
					}
					break;
				case 'majorDeptDegree':
					$crfl->clearFilter_scholarship("majorDeptDegree", $scholarship_id);
					$crf->clearFilter_scholarship("majorDeptDegree", $scholarship_id);
					
					$mainarr = $input['major_filter_array'];
					$subarr = $input['majorsub_filter_array'];
					$arry["data"] = array();
				
					for($i =0;$i<sizeof($mainarr);$i++){
							if(sizeof($mainarr[$i]["degreelevel"])!=0){
								for($k=0;$k<sizeof($mainarr[$i]["degreelevel"]);$k++) {
									$arry["data"][] = array("department_id"=>$mainarr[$i]["id"],"major_id"=>'',"degree_id"=>$mainarr[$i]["degreelevel"][$k],"in_ex"=>$mainarr[$i]["type"]);
								}
							}else{
							for($j =0;$j<sizeof($subarr);$j++){
								if($mainarr[$i]["id"] == $subarr[$j]["pid"]){
									if(sizeof($subarr[$j]["degreelevel"])!=0){
										for($k=0;$k < sizeof($subarr[$j]["degreelevel"]); $k++) {
											$arry["data"][] = array("department_id"=>$subarr[$j]["pid"],"major_id"=>$subarr[$j]["id"],"degree_id"=>$subarr[$j]["degreelevel"][$k],"in_ex"=>$mainarr[$i]["type"]);
										}
									}
									else{
										for($k=0;$k<sizeof($mainarr[$i]["degreelevel"]);$k++) {
											$arry["data"][] = array("department_id"=>$mainarr[$i]["id"],"major_id"=>'',"degree_id"=>$mainarr[$i]["degreelevel"][$k],"in_ex"=>$mainarr[$i]["type"]);
										}
									}
								}
							}
							}
						
					}
					
					
					$this->saveFilterForMajor($data='', $arry, $crf, $crfl,$scholarship_id);
					
				case 'scores':
					$crfl->clearFilter_scholarship("scores", $scholarship_id);
					$crf->clearFilter_scholarship("scores", $scholarship_id);
					
					$gpaMin_filter = $this->inputIsset($input, 'gpaMin_filter');
					$gpaMax_filter = $this->inputIsset($input, 'gpaMax_filter');
					$satMin_filter = $this->inputIsset($input, 'satMin_filter');
					$satMax_filter = $this->inputIsset($input, 'satMax_filter');
					$actMin_filter = $this->inputIsset($input, 'actMin_filter');
					$actMax_filter = $this->inputIsset($input, 'actMax_filter');
					$toeflMin_filter = $this->inputIsset($input, 'toeflMin_filter');
					$toeflMax_filter = $this->inputIsset($input, 'toeflMax_filter');
					$ieltsMin_filter = $this->inputIsset($input, 'ieltsMin_filter');
					$ieltsMax_filter = $this->inputIsset($input, 'ieltsMax_filter');
					
					
					//gpa
					if($gpaMin_filter=="true"){
						$this->saveFilterTemplate1($input['gpaMin_filter'], '', $crf, "scores", 'gpaMin_filter', $crfl,$scholarship_id);
					}
					if($gpaMax_filter=="true"){
						$this->saveFilterTemplate1($input['gpaMax_filter'], '', $crf, "scores", 'gpaMax_filter', $crfl,$scholarship_id);
					}
					if($satMin_filter=="true"){
						$this->saveFilterTemplate1($input['satMin_filter'], '', $crf, "scores", 'satMin_filter', $crfl,$scholarship_id);
					}
					if($satMax_filter=="true"){
						$this->saveFilterTemplate1($input['satMax_filter'], '', $crf, "scores", 'satMax_filter', $crfl,$scholarship_id);
					}
					if($actMin_filter=="true"){
						$this->saveFilterTemplate1($input['actMin_filter'], '', $crf, "scores", 'actMin_filter', $crfl,$scholarship_id);
					}
					if($actMax_filter=="true"){
						$this->saveFilterTemplate1($input['actMax_filter'], '', $crf, "scores", 'actMax_filter', $crfl,$scholarship_id);
					}
					if($toeflMin_filter=="true"){
						$this->saveFilterTemplate1($input['toeflMin_filter'], '', $crf, "scores", 'toeflMin_filter', $crfl,$scholarship_id);
					}
					if($toeflMax_filter=="true"){
						$this->saveFilterTemplate1($input['toeflMax_filter'], '', $crf, "scores", 'toeflMax_filter', $crfl,$scholarship_id);
					}
					if($ieltsMin_filter=="true"){
						$this->saveFilterTemplate1($input['ieltsMin_filter'], '', $crf, "scores", 'ieltsMin_filter', $crfl,$scholarship_id);
					}
					if($ieltsMax_filter=="true"){
						$this->saveFilterTemplate1($input['ieltsMax_filter'], '', $crf, "scores", 'ieltsMax_filter', $crfl,$scholarship_id);
					}
					
					break;
				case 'uploads':	
					$transcript_filter = $this->inputIsset($input, 'transcript_filter');
					$financialInfo_filter = $this->inputIsset($input, 'financialInfo_filter');
					$ielts_fitler = $this->inputIsset($input, 'ielts_fitler');
					$toefl_filter = $this->inputIsset($input, 'toefl_filter');
					$resume_filter = $this->inputIsset($input, 'resume_filter');
					$passport_filter = $this->inputIsset($input, 'passport_filter');
					$essay_filter = $this->inputIsset($input, 'essay_filter');
					$other_filter = $this->inputIsset($input, 'other_filter');
					
					if ($transcript_filter == 'false' || $financialInfo_filter == 'false' || $ielts_fitler == 'false' || $toefl_filter == 'false' || $resume_filter == 'false') {
						$crfl->clearFilter_scholarship("uploads", $scholarship_id);
						$crf->clearFilter_scholarship("uploads", $scholarship_id);
					
						$qry_id = $this->saveAdminFilterRecommendation($crf, 'include', "uploads", 'uploads',$scholarship_id);
						$uploads = array();
			
						if ($transcript_filter == 'true') {
							$uploads[] = 'transcript_filter';
						}
						if ($financialInfo_filter == 'true') {
							$uploads[] = 'financialInfo_filter';
						}
						if ($ielts_fitler == 'true') {
							$uploads[] = 'ielts_fitler';
						}
						if ($toefl_filter == 'true') {
							$uploads[] = 'toefl_filter';
						}
						if ($resume_filter == 'true') {
							$uploads[] = 'resume_filter';
						}
						if ($passport_filter == 'true') {
							$uploads[] = 'passport_filter';
						}
						if ($essay_filter == 'true') {
							$uploads[] = 'essay_filter';
						}
						if ($other_filter == 'true') {
							$uploads[] = 'other_filter';
						}
						
						$arr = array();
						foreach ($uploads as $key => $value) {
							$tmp = array();
							$tmp['val'] = $value;
							$tmp['rec_filter_id'] = $qry_id;
							$arr[] = $tmp;
						}
			
						$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
					}
					break;
				case 'demographic':	
					$ageMin_filter = $this->inputIsset($input, 'ageMin_filter');
					$ageMax_filter = $this->inputIsset($input, 'ageMax_filter');
					$all_gender_filter = $this->inputIsset($input, 'all_gender_filter');
					$male_only_filter = $this->inputIsset($input, 'male_only_filter');
					$female_only_filter = $this->inputIsset($input, 'female_only_filter');
					$all_eth_filter = $this->inputIsset($input, 'all_eth_filter');
					$include_eth_filter = $this->inputIsset($input, 'include_eth_filter');
					$exclude_eth_filter = $this->inputIsset($input, 'exclude_eth_filter');
					$ethnicity = $input['ethnicity'];
					$all_rgs_filter = $this->inputIsset($input, 'all_rgs_filter');
					$include_rgs_filter = $this->inputIsset($input, 'include_rgs_filter');
					$exclude_rgs_filter = $this->inputIsset($input, 'exclude_rgs_filter');
					$religion = $input['religion'];
					
					$crfl->clearFilter_scholarship("demographic", $scholarship_id);
					$crf->clearFilter_scholarship("demographic", $scholarship_id);
					
					$this->saveFilterTemplate1($input['ageMin_filter'] , '', $crf, "demographic", 'ageMin_filter', $crfl,$scholarship_id);
					$this->saveFilterTemplate1($input['ageMax_filter'] , '', $crf, "demographic", 'ageMax_filter', $crfl,$scholarship_id);
					
					if ($all_gender_filter == 'false') {
						$qry_id = $this->saveAdminFilterRecommendation($crf, 'include', "demographic", 'gender',$scholarship_id);
						if ($male_only_filter == 'true') {
							$gender = 'male';
						}else{
							$gender = 'female';
						}
			
						$arr = array();
						$tmp = array();
						$tmp['val'] = $gender;
						$tmp['rec_filter_id'] = $qry_id;
						$arr[] = $tmp;
			
						$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
					}
					
					$this->saveFilterTemplate2($all_eth_filter, $include_eth_filter, '', $crf, "demographic", 'include_eth_filter', $ethnicity, $crfl,$scholarship_id);
					$this->saveFilterTemplate2($all_rgs_filter, $include_rgs_filter, '', $crf, "demographic", 'include_rgs_filter', $religion, $crfl,$scholarship_id);
					break;
				case 'educationLevel':	
					//if (isset($input['educationLevel'])) {
					
						$hsUsers_filter = $this->inputIsset($input, 'hsUsers_filter');
						$collegeUsers_filter = $this->inputIsset($input, 'collegeUsers_filter');
					
						//if ($hsUsers_filter == 'tre' || $collegeUsers_filter == 'false') {
							$crfl->clearFilter_scholarship("educationLevel", $scholarship_id);
							$crf->clearFilter_scholarship("educationLevel", $scholarship_id);
							
							$qry_id = $this->saveAdminFilterRecommendation($crf, 'include', "educationLevel", 'educationLevel',$scholarship_id);
			
							$arr = array();
							$tmp = array();
							
							if ($hsUsers_filter == 'true') {
								$tmp['val'] = 'hsUsers_filter';
								$tmp['rec_filter_id'] = $qry_id;
								$arr[] = $tmp;
							}
							
							if ($collegeUsers_filter == 'true') {
								$tmp['val'] = 'collegeUsers_filter';
								$tmp['rec_filter_id'] = $qry_id;
								$arr[] = $tmp;
							}
							
							$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
						//}
					//}
					break;
				case 'militaryAffiliation':	
					//if (isset($input['militaryAffiliation'])) {
						$crfl->clearFilter_scholarship("militaryAffiliation", $scholarship_id);
						$crf->clearFilter_scholarship("militaryAffiliation", $scholarship_id);
				
						$inMilitary_filter = $input['inMilitary'];
						$militaryAffiliation_filter = $input['militaryAffiliation'];
						
						$inMilitary_filter1 = $this->inputIsset($input, 'inMilitary');
						$militaryAffiliation_filter1 = $this->inputIsset($input, 'militaryAffiliation');
						
						$this->saveFilterTemplate1($input['inMilitary'], '', $crf, "militaryAffiliation", 'inMilitary', $crfl,$scholarship_id);
						if (!empty($militaryAffiliation_filter)) {
							$this->saveFilterTemplate2('false', 'true', '', $crf, "militaryAffiliation", 'militaryAffiliation', $militaryAffiliation_filter, $crfl,$scholarship_id);
						}
					//}
					break;
				case 'profileCompletion':	
					$crfl->clearFilter_scholarship("profileCompletion", $scholarship_id);
					$crf->clearFilter_scholarship("profileCompletion", $scholarship_id);
					
					$profileCompletion_filter = $input['profileCompletion'];
					$this->saveFilterTemplate1($profileCompletion_filter , '', $crf, "profileCompletion", 'profileCompletion', $crfl,$scholarship_id);
					break;
				default:
					# code...
					break;
			}
			
			return $scholarship_id;
		}
		
	}
	
	/**********************************************************/
	public function getAllScholarshipsCms (){
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$college_id = $data['org_school_id'];
		
		$schModel = new Scholarshipcms();
		$res = $schModel->getAllScholarships($college_id);
		return $res;

	}
	
	public function deleteScholarshipCms (){

		$id = Request::get('id');

		$schModel = new Scholarshipcms();
		$res = $schModel->deleteScholarship($id);

		if($res != 1){
			return 'error';
		}

		return $id;

	}
    /*************************************************
       New Schlorship Page  

    **************************************************/
      public function schlorships(){
      $input = Request::all();

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['title'] = 'Plexuss Colleges | Scholarships';
		$data['currentPage'] = 'newschlorshippage';

		$src="/images/profile/default.png";

		$user_id = null;
		$data['oneapp_status'] = null;

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;
			$user_id = $user->id;

			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}

            /* check user in email suppression list */
            $emailSuppressionList = EmailSuppressionList::where('uid', $user->id)->first();

            if (isset($emailSuppressionList)) {
                if ($emailSuppressionList['uid'] != '') {
                    array_push( $data['alerts'],
                        array(
                            'type' => 'hard',
                            'dur' => '10000',
                            'msg' => '<span class=\"pls-confirm-msg subcribe-msg\">Oops, seems like you are on our unsubscribe list. In order to get the best experience from Plexuss,</span> <span id=\"'.$emailSuppressionList['uid'].'\" class=\"subscribe-now\">Subscribe Now</span> <div class=\"loader loader-hidden\"></div>'
                        )
                    );
                }
            }

			if ( !$user->email_confirmed ) {
				array_push( $data['alerts'], 
					array(
						'img' => '/images/topAlert/envelope.png',
						'type' => 'hard',
						'dur' => '10000',
						'msg' => '<span class=\"pls-confirm-msg\">Please confirm your email to gain full access to Plexuss tools.</span> <span class=\"go-to-email\" data-email-domain=\"'.$data['email_provider'].'\"><span class=\"hide-for-small-only\">Go to</span><span class=\"show-for-small-only\">Confirm</span> Email</span> <span> | <span class=\"resend-email\">Resend <span class=\"hide-for-small-only\">Email</span></span></span> <span> | <span class=\"change-email\" data-current-email=\"'.$data['email'].'\">Change email <span class=\"hide-for-small-only\">address</span></span></span>'
					)
				);
			}
			// dd($user->id);

			//get if user OneApp step
			$appModel = new UsersCustomQuestion();
			$oneapp_status = $appModel->getUserStatus($user->id);
			$data['oneapp_status'] = $oneapp_status;

		}
		$data['profile_img_loc'] = $src;


		$schModel = new Scholarship();

		$fincount = 0;
		if($user_id){
			// $res1 = $schModel->getAllScholarshipsWithStatus($user_id, $input);
			// $fincount = $res1[0];
			// $res = $res1[1];

			$res = $schModel->getAllScholarshipsNotApplied($user_id, $input);

		}else{
			$res = $schModel->getAllScholarshipsFilters($input);
		}

		$data['fincount'] = $fincount;
		$data['scholarships'] =  $res;


		//if filters set in url query -- apply
		if(isset($input['rangeF'])){
			// dd($input['rangeF']);
			$data['rangeF'] = $input['rangeF'];
		}

		return View('private.scholarships.indexnew', $data);

      }

}
