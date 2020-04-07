<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use AWS, Request, Validator, DB, Session, DateTime, Hash;
use Carbon\Carbon; 
use App\Http\Controllers\ViewDataController, App\Http\Controllers\AjaxController;
use App\ExportField, App\Country, App\Degree, App\CollegeRecommendationFilters, App\ZipCodes, App\Department, App\Ethnicity;
use App\AgencyRecruitment, App\Agency, App\CollegeRecommendation, App\TrackingPage, App\Recruitment, App\Transcript, App\ServicesByAgency;
use App\OrganizationPortal, App\OrganizationBranchPermission, App\OrganizationPortalUser, App\AjaxToken, App\CollegeMessageThreadMembers;
use App\User, App\ProfanityList, App\ConfirmToken, App\Objective, App\Score, App\AgencyUserReview;
use App\AgencyProfileInfo, App\AgencyProfileInfoServices, App\AgencyBucketNames, App\AgencyUserBucketLogs,App\AgencyProfileInfoHours;
use App\UsersFinancialFirstyrAffordibilityLog, App\Profession, App\Major, App\PlexussVerificationsUser;
use App\UsersAppliedColleges, App\Religion, App\MilitaryAffiliation, App\SponsorUserContacts, App\CollegesApplicationsState;
use App\CollegesByAgency, App\AgencyPermission;

class AgencyController extends Controller{

	const CRONTIME = "+6 hours";

    private $max_cron_date='';
    private $min_cron_date='';

    private $num_of_inquiry = 0;
    private $num_of_recommended = 0;
    private $num_of_pending = 0;
    private $num_of_approved = 0;
    private $num_of_removed = 0;
    private $num_of_rejected = 0;

    private $num_of_inquiry_new = 0;
    private $num_of_recommended_new = 0;
    private $num_of_pending_new = 0;
    private $num_of_approved_new = 0;
    private $num_of_removed_new = 0;
    private $num_of_rejected_new = 0;

	//   public function __construct(){

			// $viewDataController = new ViewDataController();
			// $data = $viewDataController->buildData();

			// $this->setRecommendationExpirationDate();

			// if (isset($data['agency_collection'])) {
			// 	$this->agencyAdminPanelCnt($data);
			// }
	//   }

	/**
	 * This method populates agency's dashboard page
	 *
	 * @return view
	 */
	public function index(){

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		
		$data['title'] = 'Agency Control Panel';
		$data['currentPage'] = 'agency';
		
		$agency = $data['agency_collection'];

		if (isset($agency->logo_url)) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $agency->logo_url;
		} else if (isset($data['profile_img_loc'])) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$data['agency_name'] = $agency->name;
		$data['expiresIn'] = $this->setRecommendationExpirationDate();
		$data['inquiryCnt'] = $this->num_of_inquiry_new;
		$data['recommendCnt'] = $this->num_of_recommended;
		
		$data['inquiryCntTotal'] = $this->num_of_inquiry;
		$data['recommendCntTotal'] = $this->getRecommendCntTotal($data);

		$data['approvedCnt'] = $this->num_of_approved;
		$data['messageCnt'] = $this->getAgencyMessageCnt();

		// Get review average
		$review = AgencyUserReview::on('rds1')->where('agency_id', $agency->agency_id);

		$review_count = $review->count();
		$review_avg = $review->avg('rating');

		$data['review_count'] = $review_count;
		$data['review_avg'] = $review_avg;
		
		$export_fields = ExportField::leftjoin('export_field_exclusions as efe', function($join) use($data)
			 {
			    $join->on('export_fields.id', '=', 'efe.export_field_id');
			    $join->on('efe.type', '=', DB::raw('"agency"'));
			    $join->on('efe.type_id', '=', DB::raw($data["agency_collection"]->agency_id));
			})
			->whereNull('efe.export_field_id')
			->select('export_fields.id', 'export_fields.name');
			
		if($data['agency_collection']->is_trial_period == 1 || $data['agency_collection']->balance > 0) {
			$export_fields = $export_fields->get()->toArray();
		}else{
			$export_fields = $export_fields->where('export_fields.id', '!=', 2)
										   ->where('export_fields.id', '!=', 3)
										   ->where('export_fields.id', '!=', 4)
										   ->get()->toArray();
		}	

		$data['export_fields'] = $export_fields;
		// dd($data);

		$an = Agency::on('rds1')->where('id', $agency->agency_id)->first();
		$data['agency_signup_url'] = env('CURRENT_URL').'signup?utm_source='.$an->utm_source;

		return View('agency.index', $data);
	}

	public function messagingIndex(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$data['currentPage'] = 'agency-messaging';

        if (isset($data['agency_collection'])) {
			$agency = $data['agency_collection'];
		}
		
		if (isset($agency->logo_url)) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $agency->logo_url;
		} else if (isset($data['profile_img_loc'])) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		return View('agency.messaging.index', $data);
	}

	public function reportingIndex(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$data['currentPage'] = 'agency-reporting';

		if (isset($data['agency_collection'])) {
			$agency = $data['agency_collection'];
		}

		if (isset($agency->logo_url)) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $agency->logo_url;
		} else if (isset($data['profile_img_loc'])) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		return View('agency.reporting', $data);
	}

    public function videoTutorialIndex(){
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();
        $data['currentPage'] = 'agency-video-tutorial';

        if (isset($data['agency_collection'])) {
            $agency = $data['agency_collection'];
        }
        if (isset($agency->logo_url)) {
            $data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $agency->logo_url;
        } else if (isset($data['profile_img_loc'])) {
            $data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
        }

        return View('agency.tutorial', $data);
    }

	public function agencySearchIndex(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$data['currentPage'] = 'agency-search';

		// Get all services offered
		$data['services_list'] = AgencyProfileInfoServices::getAllServices();

		// Get all countries from agencies
		$data['countries_list'] = Agency::getAllAgencyCountries();

		return View('agency.agencyProfile.search', $data);
	}

	public function agencySearch($search_type, $search_string){
		$response = [];

		$query = DB::connection('rds1')->table('agency as a');

		// Search requires the search type ('country' or 'service') and the search string
		if (!isset($search_type) || !isset($search_string)) 
			return 'fail';

		switch ($search_type) {
			case 'service':
				$query = $query->join('agency_profile_info_services as apis', 'apis.agency_id', '=', 'a.id')
						   ->where('apis.service_name', $search_string);
				break;
			case 'country':
                if ($search_string == 'all') break;
				$query = $query->where('a.country', $search_string);
				break;
			case 'all':
				if ($search_string == 'all') break;
				return 'fail';			
			default: 
				return 'fail';
		}

		$query = $query->join('agency_permissions as ap', 'a.id', '=', 'ap.agency_id')
			           ->join('users as u', 'ap.user_id', '=', 'u.id')
					   ->select('a.*', 'ap.user_id', 'u.fname', 'u.lname', 'u.id as user_id')
					   ->where('a.active', 1)
                       ->orderBy(\DB::raw('-a.logo_url'), 'desc')
				       ->get();

		foreach ($query as $agent) {
			$tmp = [];

			$tmp['agency_id'] = $agent->id;
			$tmp['user_id'] = $agent->user_id;
			$tmp['company_name'] = $agent->name;
			$tmp['agent_full_name'] = $agent->fname . ' ' . $agent->lname;
			$tmp['agent_name'] = $agent->fname . ' ' . substr($agent->lname, 0, 1) . '.';
			$tmp['location'] = $agent->country;
			$tmp['services'] = AgencyProfileInfoServices::getServicesByAgencyId($agent->id);

			$review = AgencyUserReview::on('rds1')->where('agency_id', $agent->id);
			$review_cnt = $review->count();
			$review_avg = $review->avg('rating');

			$tmp['review_count'] = $review_cnt;
			$tmp['review_avg'] = $review_avg;

			$tmp['profile_slug'] = '/agency-profile/' . $agent->id . '/' . $agent->user_id;

			$tmp['message_slug'] = '/portal/messages/' . $agent->id . '/agency';

			if (isset($agent->logo_url)) {
				$tmp['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $agent->logo_url; 
			}else{
				$tmp['logo_url'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";
			}

			$response[] = $tmp;
		}

		return $response;
	}

	public function agentProfileIndex($agency_id, $agent_id){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$data['currentPage'] = 'agency-profile';

		$qry = DB::connection('rds1')->table('agency as a')
									 ->join('agency_permissions as ap', 'a.id', '=', 'ap.agency_id')
									 ->join('users as u', 'u.id', '=', 'ap.user_id')
									 ->where('a.active', 1)
									 ->where('a.id', $agency_id)
									 ->where('u.id', $agent_id)
									 ->select('a.*', 'u.fname', 'u.lname')
									 ->first();

		$review = AgencyUserReview::on('rds1')->where('agency_id', $agency_id);

		$review_cnt = $review->count();
		$review_avg = $review->avg('rating');

		$services_offered = AgencyProfileInfoServices::getServicesByAgencyId($agency_id);

		if (isset($qry)) {

			$ret = array();

			$ret['company']  = $qry->name;
			$ret['web_url']  = $qry->web_url;
			$ret['country'] = $qry->country;
			if (isset($qry->logo_url)) {
				$ret['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $qry->logo_url; 
			}else{
				$ret['logo_url'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";
			}
			$ret['agent_name']  = ucwords(strtolower($qry->fname . ' ' . $qry->lname ));
			$ret['agency_id'] = $agency_id;
			$ret['description'] = $qry->detail;
			$ret['review_cnt']  = $review_cnt;
			$ret['review_avg']  = $review_avg;
			$ret['services_offered'] = $services_offered;
			$ret['days_of_operation'] = AgencyProfileInfoHours::getBusinessDays($agency_id);
			$ret['message_slug'] = '/portal/messages/' . $agency_id . '/agency';
			
			$data['dt'] = $ret;
			$data['profile_slug'] = 'agency-profile/' . $agency_id . '/' . $agent_id;
		}

		return View('agency.agencyProfile.profile', $data);
	}

	public function savePlexussUserInfo(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		// echo "<pre>";
		// print_r($input);
		// echo "</pre>";
		// exit();

		$user_id = $input['user_id'];
		$user  = User::find($user_id);
		$score = Score::where('user_id', $user_id)->first();
		if (!isset($score)) {
			$score = new Score;
			$score->user_id = $user_id;

			$score->save();
		}
		// $obj   = DB::table('objectives');
		$obj_arr = array();

		$user_check = false;
		$score_check = false;
		$obj_check  = false;

		// $nBday = '';  //to store new birth_date
		// $newDate = [];	


		foreach ($input as $key => $value) {

			switch ($key) {
				case 'infoGradYear':
					if ($user->in_college == 0) {
						$user->hs_grad_year = $value;
					}else{
						$user->college_grad_year = $value;
					}

					$user_check = true;
					break;

				case 'profileStartTerm':
					$arr = explode(" ", $value);
					$user->planned_start_term = $arr[0];
					$user->planned_start_yr   = $arr[1];

					$user_check = true;
					break;

				 case 'birthday':
				 	if($value != null){
						$user->birth_date = $value;
					 	$user_check = true;

					}
				 	break;

				case 'profileFin':
					$val = str_replace("$", "", $value);
					$val = str_replace("+", "", $val);

					$user->financial_firstyr_affordibility = $val;
					$user_check = true;

					// ADD TO FINANCIAL LOGS FOR THE USER.
					$uffal = new UsersFinancialFirstyrAffordibilityLog;
					$uffal->add($user_id, $val, $data['user_id'], 'admin_savePlexussUserInfo');

					break;

				case 'objDegree':
					$obj_arr['degree_type'] = $value;

					$obj_check = true;
					break;

				case 'objProfession':
					$prof = Profession::on('rds1')->where('profession_name', $value)->first();
					if (isset($prof)) {
						$obj_arr['profession_id'] = $prof->id;
					}

					$obj_check = true;
					break;

				case 'schoolType':
					$user->interested_school_type = $value;

					$user_check = true;
					break;

				case 'email':
					$user->email = $value;

					$user_check = true;
					break;

				case 'skype':
					$user->skype_id = $value;

					$user_check = true;
					break;

				case 'phone':
					$user->phone = $value;

					$user_check = true;
					break;

				case 'country':
					$user->country_id = $value;

					$user_check = true;
					break;

				case 'address':
					$user->address = $value;

					$user_check = true;
					break;

				case 'city':
					$user->city = $value;

					$user_check = true;
					break;

				case 'state':
					$user->state = $value;

					$user_check = true;
					break;

				case 'majors':
					$this_obj   = Objective::on('rds1')->where('user_id', $user_id)->first();
					$delete_obj = Objective::where('user_id', $user_id)->delete();

					if (!isset($this_obj)) { // If not stored in DB, setup a temp obj
						$this_obj = (object) [ 'degree_type' => 6, 'profession_id' => null, 
											   'obj_text' => '', 'university_location' => null ];
					}

					foreach ($value as $k => $v) {
						$major = Major::on('rds1')->where('name', $v)->first();

						$tmp = new Objective;
						
						$tmp->user_id 	  		  = $user_id;
						$tmp->degree_type 		  = $this_obj->degree_type;
						$tmp->major_id	  		  = $major->id;
						$tmp->profession_id 	  = $this_obj->profession_id;
						$tmp->obj_text	  		  = $this_obj->obj_text;
						$tmp->university_location = $this_obj->university_location;

						$tmp->save();					
					}

					break;

				case 'Email_verified':
					if ($value == 'true') {
						$v = 1;
					}else{
						$v = 0;
					}
					$user->email_confirmed = $v;

					$user_check = true;
					break;

				case 'Phone_verified':
					if ($value == 'true') {
						$v = 1;
					}else{
						$v = 0;
					}
					
					$user->txt_opt_in = $v;

					$user_check = true;
					break;

				case 'Skype_verified':
					if ($value == 'true') {
						$v = 1;
					}else{
						$v = 0;
					}
					$attr = array('user_id' => $user_id);
					$val  = array('user_id' => $user_id, 'verified_skype' => $v);

					$tmp = PlexussVerificationsUser::updateOrCreate($attr, $val);

					break;

				case 'Phonecall_verified':
					if ($value == 'true') {
						$v = 1;
					}else{
						$v = 0;
					}
					// $attr = array('user_id' => $user_id);
					// $val  = array('user_id' => $user_id, 'phonecall_verified' => $v);

					// $tmp = PlexussVerificationsUser::updateOrCreate($attr, $val);

					$user->verified_phone = $v;

					$user_check = true;

					break;

				case 'satScore':
					$score->sat_total = $value;

					$score_check = true;
					break;

				case 'actScore':
					$score->act_composite = $value;

					$score_check = true;
					break;

				case 'toeflScore':
					$score->toefl_total = $value;

					$score_check = true;
					break;

				case 'ieltsScore':
					$score->ielts_total = $value;

					$score_check = true;
					break;

				case 'editGPA':
					if ($user->in_college == 0) {
						$score->hs_gpa = $value;
					}else{
						$score->overall_gpa = $value;
					}

					$score_check = true;
					break;

				case 'transcript_labels':
					foreach ($value as $transcript) {
						$current_transcript = Transcript::find($transcript['id']);
						$current_transcript->label = $transcript['new_label'];
						$current_transcript->save();
					}
					break;
				case 'uploadTypeChange':
					foreach ($value as $transcript) {
						$current_transcript = Transcript::find($transcript['id']);
						$current_transcript->doc_type = $transcript['new_type'];
						$current_transcript->save();
					}
					break;
				default:
					# code...
					break;
			}
		}
		
		if ($user_check == true) {
			$user->save();
		}
		if ($score_check == true) {
			$score->save();
		}
		if (!empty($obj_arr)) {
			$obj = Objective::updateOrCreate(['user_id' => $user_id], $obj_arr);
		}

		$this->CalcIndicatorPercent($user_id);
		$this->CalcProfilePercent($user_id);
		$this->CalcOneAppPercent($user_id);

		return "success";
	}

	// Helper method for signing up. May want to move to Controller.php later
	private function sendconfirmationEmail( $name, $emailaddress, $confirmation ) {

		$mac = new MandrillAutomationController;
		$mac->confirmationEmailForUsers($name, $emailaddress, $confirmation);

	}

	// Helper method for signing up. May want to move to Controller.php later
	public function setAjaxToken( $id = null ) {
		$user = User::find( $id );
		$hasToken = $user->ajaxToken()->first();
		$token = str_random( 20 );
		if ( $hasToken ) {
			$hasToken->token = $token;
			$hasToken->save();
		} else {
			$ajaxtoken = new AjaxToken( array( 'token'=>$token ) );
			$user->ajaxtoken()->save( $ajaxtoken );
		};
	}

	/**
	 * This method returns each tab of recommendation filters
	 *
	 * @param section String
	 * @return view
	 */
	public function getAjaxFilterSections( $section ){
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if ($section == null) {
			return;
		}
		
		$data['school_name'] = '';
		$data['school_logo'] = '';

		$filter_section = 'admin.recommendation_filter.ajax.'.$section;

		$crf = new CollegeRecommendationFilters;
		$filters = $crf->getFiltersAndLogs($data['agency_collection']->agency_id, null, null, $section);

		$ret = array();
		$name = '';

		if (isset($filters)) {
			$temp = array();

			foreach ($filters as $key) {
				if ($name == '') {

					$name = $key->name;

					$temp = array();
					$temp['type'] = $key->type;
					$temp['category'] = $key->category;
					$temp['filter'] = $key->name;
					if (isset($key->val)) {
						$temp[$key->name] = array();
						$temp[$key->name][] = $key->val;
					}
				}elseif($name == $key->name){

					if (isset($key->val)) {
						$temp[$key->name][] = $key->val;
					}
					$name = $key->name;
				}else{
					$ret[] = $temp;

					$name = $key->name;

					$temp = array();
					$temp['type'] = $key->type;
					$temp['category'] = $key->category;
					$temp['filter'] = $key->name;
					if (isset($key->val)) {
						$temp[$key->name] = array();
						$temp[$key->name][] = $key->val;
					}

				}
			}

			$ret[] = $temp;
		}

		$data['filters'] = $ret;

		switch ($section) {
			case 'location':
					$zipCodes = new ZipCodes;
					$states = $zipCodes->getAllUsState();
					$countries = Country::all()->toArray();
					$tmp_ctry = array();
					$tmp_ctry[''] = 'Select a country...';
					foreach ($countries as $key => $value) {
						$tmp_ctry[$value['country_name']] = $value['country_name'];
					}

					$data['countries'] = $tmp_ctry;
					$states = $states;
					$cities = array('' => 'Select state first' );
					$data['states'] = $states;
					$data['cities'] = $cities;
				break;

            case 'scores':
                $new_filters = array();
                foreach($data['filters'] as $key){
                    if(isset($key['sat_act'])){
                        foreach($key['sat_act'] as $i){
                            $val = explode(',', $i);
                            if($val[0] == 'sat'){
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'satMin_filter';
                                    $temp['satMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '2400'){
                                    $temp = array();
                                    $temp['filter'] = 'satMax_filter';
                                    $temp['satMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                            else{
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'actMin_filter';
                                    $temp['actMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '36'){
                                    $temp = array();
                                    $temp['filter'] = 'actMax_filter';
                                    $temp['actMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                        }
                    }elseif(isset($key['ielts_toefl'])){
                        foreach($key['ielts_toefl'] as $i){
                            $val = explode(',', $i);
                            if($val[0] == 'ielts'){
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'ieltsMin_filter';
                                    $temp['ieltsMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '9'){
                                    $temp = array();
                                    $temp['filter'] = 'ieltsMax_filter';
                                    $temp['ieltsMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }else{
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'toeflMin_filter';
                                    $temp['toeflMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '120'){
                                    $temp = array();
                                    $temp['filter'] = 'toeflMax_filter';
                                    $temp['toeflMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                        }
                    }elseif(isset($key['gpa_filter'])){
                        $val = explode(',', $key['gpa_filter'][0]);
                        if ($val[0] != '0'){
                            $temp = array();
                            $temp['filter'] = 'gpaMin_filter';
                            $temp['gpaMin_filter'] = array($val[0]);
                            $new_filters[] = $temp;
                        }
                        if ($val[1] != '4'){
                            $temp = array();
                            $temp['filter'] = 'gpaMax_filter';
                            $temp['gpaMax_filter'] = array($val[1]);
                            $new_filters[] = $temp;
                        }
                    }
                }
                $data['filters'] = $new_filters;
                break;

			case 'major':
			case 'majorDeptDegree':

				$dep = new Department;
				$useIds = true;
				$dep_name = $dep->getAllDepartments($useIds);

				$departments = $dep_name;

				$majors = array('' => 'Select department first' );
				$data['departments'] = $departments;

				break;

			case 'uploads':
				$tmpArr = $data['filters'];

				if(isset($tmpArr) && !empty($tmpArr[0]) && isset($tmpArr[0]['uploads'])){
					$tmp = array();
					$tmpArr = $data['filters'][0]['uploads'];

					foreach ($tmpArr as $key => $value) {
						$tmp[$value] = true;
					}

					$tmpArr = $tmp;
					$data['filters'][0]['uploads'] = $tmpArr;
				}

				break;

			case 'desiredDegree':

				$degree = new Degree;

				$degree = $degree::all();

				$data['degree'] = $degree;

				$tmp_arr = $data['filters'];

				if(isset($tmp_arr) && !empty($tmp_arr[0])){
					$tmp = array();
					$tmp_arr = $data['filters'][0]['desiredDegree'];

					foreach ($tmp_arr as $key => $value) {
						$tmp[$value] = true;
					}

					$tmp_arr = $tmp;
					$data['filters'][0]['desiredDegree'] = $tmp_arr;
				}

				break;

			case 'demographic':

				//get ethnicity
				if (Cache::has(env('ENVIRONMENT') .'_all_eth')) {
					$eth = Cache::get(env('ENVIRONMENT') .'_all_eth');
				}else{
					$eth = new Ethnicity;
					$eth = $eth->getAllUsEthnicities();
					Cache::put(env('ENVIRONMENT') .'_all_eth', $eth, 120);
				}
				$data['ethnicities'] = $eth;

				//get religion
				if (Cache::has(env('ENVIRONMENT') .'_all_religions')) {
					$religions = Cache::get(env('ENVIRONMENT') .'_all_religions');
				}else{
					$religions = new Religion;
					$religions = $religions->getAllUsReligions();
					Cache::put(env('ENVIRONMENT') .'_all_religions', $religions, 120);
				}
				$data['religions'] = $religions;

				$filters = $data['filters'];
				$arr = array();

				if( !empty($data['filters'][0]) ){
					foreach ($filters as $key) {
						$f = $key['filter'];
						$num = $key[$f];

						if ($key['filter'] == 'include_eth_filter') {
							$arr[$key['filter']] = $num;
							$arr['include_eth_filter_type'] = $key['type'];
						}elseif ($key['filter'] == 'include_rgs_filter') {
							$arr[$key['filter']] = $num;
							$arr['include_rgs_filter_type'] = $key['type'];
						}else{
							$arr[$key['filter']] = $num[0];
						}

					}
				}

				$data['filters'] = $arr;

				break;
			case 'militaryAffiliation':
				$tmpFilters = $data['filters'];
				if (Cache::has(env('ENVIRONMENT') .'_all_militaryAff')) {
					$ma = Cache::get(env('ENVIRONMENT') .'_all_militaryAff');
				}else{
					$ma = new MilitaryAffiliation;
					$ma = $ma->getAll();
					Cache::put(env('ENVIRONMENT') .'_all_militaryAff', $ma, 120);
				}
				$data['militaryAffiliation'] = $ma;
				// $data['filters'][0]['militaryAffiliation'] = $tmpFilters;
				break;
			case 'startDateTerm':
				$dates = array();
				$today = Carbon::today();
				$month = $today->month;
				$yr = $today->year;

				$fall = '';
				$spring = '';
				$dates[''] = 'Select...';
				for ($i = 0; $i < 7; $i++) {
					//if the current month is past fall season already, skip it
					if( $i == 0 ){
						if( $month < 7 ){
							$fall = 'Fall ' . ($yr + $i);
							$dates[$fall] = $fall;
						}
					}else{
						$fall = 'Fall ' . ($yr + $i);
						$dates[$fall] = $fall;
					}

					$spring = 'Spring ' . ($yr + $i);
					$dates[$spring] = $spring;
				}

				$dates = $dates;
				$data['dates'] = $dates;
				break;
			case 'financial':
				$opts = array('0.00','0 - 5,000','5,000 - 10,000','10,000 - 20,000','20,000 - 30,000','30,000 - 50,000', '50,000');
				$fin = array();

				$fin[''] = 'Select...';
				for ($i=0; $i < count($opts); $i++) {
					$tmp = explode('-', $opts[$i]);

					if( count($tmp) > 1 ){
						$fin[$opts[$i]] = '$'.trim($tmp[0]).' - $'.trim($tmp[1]);
					}else{
						if( $tmp[0] == '0.00' ){
							$fin[$opts[$i]] = '$0';
						}elseif( $i == 6 ){
							$fin[$opts[$i]] = '$'.$tmp[0].'+';
						}else{
							$fin[$opts[$i]] = '$'.trim($tmp[0]);
						}
					}
				}

				$data['financial_options'] = $fin;
				break;
			case 'typeofschool':
				break;
			default:
				# code...
				break;
		}
		
		return View($filter_section, $data);
	}


	public function leadsIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['show_filter'] = true;
		$data['currentPage'] = 'agency-leads';

		$input = Request::all();
		$org_input = $input;

		$has_filter_audience = false;

		if(isset($filter_audience)){
			$has_filter_audience = true;
		}

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_input');
			} else {
				$input = array();
			}

			$input['display'] = '15';
			$input['orderBy'] = null;
			$input['sortBy'] = null;
			$input['applied'] = '0';
			$input['enrolled'] = '0';
			$input['init'] = '1';
		} else {
			// if ajax call
			if(!empty($input['init']) && $input['init'] == '1') {
				//begin from filter audience and sorting
				Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

	    Session::put(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_input', $input);

	    if (isset($input['orderBy']) && isset($input['sortBy'])) {
	     	Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy', $input['sortBy']);
	     	Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy', $input['orderBy']);
        }

	    if (Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy') && 
	    	Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy')) {
	     	
	     	$input['sortBy'] = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy');
	     	$input['orderBy'] = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy');
	    }

	    // this runs only on load... not ajax... we need to make sure to remove any skip, and take at this point.
	    if (!isset($is_ajax) && $has_filter_audience == false) {
	    	Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_'.$data['currentPage'].'_'.$data['user_id']);
	    	Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
	    	$filter_audience = NULL;
	    }

		if (!isset($is_ajax) || isset($filter_audience)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_recommendations_'.$data['user_id']);		
		}

		if (!isset($is_ajax) && (!isset($org_input) || empty($org_input))) {
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy');
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy');

			unset($input['sortBy']);
			unset($input['orderBy']);
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		return $this->loadInquirieAdmin($data, null, $is_ajax, $input, $filter_audience);
	}

	public function opportunitiesIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['show_filter'] = true;
		$data['currentPage'] = 'agency-opportunities';

		$input = Request::all();
		$org_input = $input;

		$has_filter_audience = false;

		if(isset($filter_audience)){
			$has_filter_audience = true;
		}

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input');
			} else {
				$input = array();

				$input['display'] = '15';
				$input['orderBy'] = null;
				$input['sortBy'] = null;
				$input['applied'] = '0';
				$input['enrolled'] = '0';
				$input['init'] = '1';
			}		
		} else {
			// if ajax call
			if(!empty($input['init']) && $input['init'] == '1') {
				//begin from filter audience and sorting
				Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

	    Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input', $input);

        if (isset($input['orderBy']) && isset($input['sortBy'])) {
	     	Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy', $input['sortBy']);
	     	Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy', $input['orderBy']);
        }

	    if (Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy') && 
	    	Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy')) {
	     	
	     	$input['sortBy'] = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy');
	     	$input['orderBy'] = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy');
	    }
	    
	    // this runs only on load... not ajax... we need to make sure to remove any skip, and take at this point.
	    if (!isset($is_ajax) && $has_filter_audience == false) {
	    	Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_'.$data['currentPage'].'_'.$data['user_id']);
	    	Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
	    	$filter_audience = NULL;
	    }

		if (!isset($is_ajax) || isset($filter_audience)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_recommendations_'.$data['user_id']);
		}

		if (!isset($is_ajax) && (!isset($org_input) || empty($org_input))) {
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy');
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy');

			unset($input['sortBy']);
			unset($input['orderBy']);
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		return $this->loadInquirieAdmin($data, null, $is_ajax, $input, $filter_audience);
	}

	public function applicationsIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['show_filter'] = true;
		$data['currentPage'] = 'agency-applications';

		$input = Request::all();
		$org_input = $input;

		$has_filter_audience = false;

		if(isset($filter_audience)){
			$has_filter_audience = true;
		}

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_applications_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_applications_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AgencyController_applications_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_applications_input');
			} else {
				$input = array();
			}

			$input['display'] = '15';
			$input['orderBy'] = null;
			$input['orderBy'] = null;
			$input['applied'] = '0';
			$input['enrolled'] = '0';
			$input['init'] = '1';
		} else {
			// if ajax call
			if(!empty($input['init']) && $input['init'] == '1') {
				//begin from filter audience and sorting
				Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_applications_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_applications_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_applications_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_applications_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_applications_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

	    Session::put(env('ENVIRONMENT') .'_'.'AgencyController_applications_input', $input);

	    if (isset($input['orderBy']) && isset($input['sortBy'])) {
	     	Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy', $input['sortBy']);
	     	Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy', $input['orderBy']);
        }

	    if (Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy') && 
	    	Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy')) {
	     	
	     	$input['sortBy'] = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy');
	     	$input['orderBy'] = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy');
	    }

	    // this runs only on load... not ajax... we need to make sure to remove any skip, and take at this point.
	    if (!isset($is_ajax) && $has_filter_audience == false) {
	    	Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_'.$data['currentPage'].'_'.$data['user_id']);
	    	Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
	    	$filter_audience = NULL;
	    }

		if (!isset($is_ajax) || isset($filter_audience)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_recommendations_'.$data['user_id']);
		}

		if (!isset($is_ajax) && (!isset($org_input) || empty($org_input))) {
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy');
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy');

			unset($input['sortBy']);
			unset($input['orderBy']);
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		return $this->loadInquirieAdmin($data, null, $is_ajax, $input, $filter_audience);
	}

	public function removedIndex($is_ajax = null, $filter_audience = null){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['currentPage'] = 'agency-removed';

		$input = Request::all();
		$org_input = $input;

		$has_filter_audience = false;

		if(isset($filter_audience)){
			$has_filter_audience = true;
		}

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_removed_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_removed_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AgencyController_removed_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_removed_input');
			} else {
				$input = array();
			}

			$input['display'] = '15';
			$input['orderBy'] = null;
			$input['sortBy'] = null;
			$input['applied'] = '0';
			$input['enrolled'] = '0';
			$input['init'] = '1';
		} else {
			// if ajax call
			if(!empty($input['init']) && $input['init'] == '1') {
				//begin from filter audience and sorting
				Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_removed_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_removed_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_removed_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_removed_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

	    Session::put(env('ENVIRONMENT') .'_'.'AgencyController_removed_input', $input);

	    if (isset($input['orderBy']) && isset($input['sortBy'])) {
	     	Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy', $input['sortBy']);
	     	Session::put(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy', $input['orderBy']);
        }

	    if (Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy') && 
	    	Session::has(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy')) {
	     	
	     	$input['sortBy'] = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy');
	     	$input['orderBy'] = Session::get(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy');
	    }

	    // this runs only on load... not ajax... we need to make sure to remove any skip, and take at this point.
	    if (!isset($is_ajax) && $has_filter_audience == false) {
	    	Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_'.$data['currentPage'].'_'.$data['user_id']);
	    	Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
	    	$filter_audience = NULL;
	    }

		if (!isset($is_ajax) || isset($filter_audience)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_recommendations_'.$data['user_id']);
		}

		if (!isset($is_ajax) && (!isset($org_input) || empty($org_input))) {
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_sortBy');
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_opportunities_input_orderBy');

			unset($input['sortBy']);
			unset($input['orderBy']);
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		return $this->loadInquirieAdmin($data,'removed', $is_ajax, $input, $filter_audience);
	}

	// ----- get filter index
	public function getFilterIndex(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'agency-adv-filtering';
		$data['title'] = 'Agency Advanced Filtering';

		$data['school_name'] = '';
		$data['school_logo'] = '';

		$ac = new AjaxController();

		$data['filter_perc'] = $ac->getNumberOfUsersForFilter();

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}
		

		return View('admin.recommendation_filter.index', $data);
	}

	//get agency settings page
	public function getAgencySettingsIndex( $load = null ){
		switch ($load) {
			case 'profileInfo':
				$data = $this->getAgencyProfileInfo();
				$data['agencySettingLoadThis'] = 'profileInfo';
				break;
			case 'paymentInfo':
				$data = $this->getAgencyPaymentInfo();
				$data['agencySettingLoadThis'] = 'paymentInfo';
				break;
			default:
				$data = $this->getAgencyProfileInfo();
				$data['agencySettingLoadThis'] = 'profileInfo';
				break;
		}	

		return View('agency.settings', $data);	
	}


	public function getSettingsSection( $tab ){
		if ($tab == null) {
			return;
		}

		switch ($tab) {
			case 'profileInfo':
				$data = $this->getAgencyProfileInfo();
				break;
			case 'paymentInfo':
				$data = $this->getAgencyPaymentInfo();
				break;
			default:
				$data = $this->getAgencyProfileInfo();
				break;
		}

		$tab_section = 'agency.ajax.'.$tab;

		return View($tab_section, $data);
	}

	private function getAgencyPaymentInfo(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		
		$data['title'] = 'Agency Settings';
		$data['currentPage'] = 'agency-settings';

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		$agency = new Agency;

		$a = $agency->getAgencyProfile($data['user_id']);

		$data['agency_name'] = $a->name;

		return $data;
	}

	private function getAgencyProfileInfo(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		
		$data['title'] = 'Agency Settings';
		$data['currentPage'] = 'agency-settings';

        if (isset($data['agency_collection'])) {
			$agency = $data['agency_collection'];
		}

		if (isset($agency)) {
			$data['services_offered'] = AgencyProfileInfoServices::getServicesByAgencyId($agency->agency_id);
		} else {
			$data['services_offered'] = [];
		}
		
		if (isset($agency->logo_url)) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $agency->logo_url;
		} else if (isset($data['profile_img_loc'])) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$agency = new Agency;

		$a = $agency->getAgencyProfile($data['user_id']);

		$data['agency_name'] = $a->name;

		// -- profile info data on load
		$countries_raw = Country::all()->toArray();
		$countries = array();

		$countries[''] = 'Select a country...';
		foreach( $countries_raw as $val ){
			$countries[$val['country_name']] = $val['country_name'];
		}
		$data['countries'] = $countries;

		//get states
		$states_raw = DB::table('states')->get();
		$states = array();
		$states[''] = 'Select a state...';
		foreach( $states_raw as $val ){
			$states[$val->state_abbr] = $val->state_name;
		}
		$data['states'] = $states;

		// if ($a->college_counseling == 1) {
		// 	$data['college_counseling'] = true;
		// }else{
		// 	$data['college_counseling'] = false;
		// }

		// if ($a->tutoring_center == 1) {
		// 	$data['tutoring_center'] = true;
		// }else{
		// 	$data['tutoring_center'] = false;
		// }

		// if ($a->test_preparation == 1) {
		// 	$data['test_preparation'] = true;
		// }else{
		// 	$data['test_preparation'] = false;
		// }

		// if ($a->international_student_assistance == 1) {
		// 	$data['international_student_assistance'] = true;
		// }else{
		// 	$data['international_student_assistance'] = false;
		// }

		if (isset($a->country)) {
			$data['my_country'] = $a->country;
		}else{
			$data['my_country'] = null;
		}

		if(isset($a->state)){
			$data['my_state'] = $a->state;
		}else{
			$data['my_state'] = null;
		}
		
		$data['city'] = $a->city;

		$data['company_phone'] = $a->phone;

		$data['web_url'] = $a->web_url;

		$data['detail'] = $a->detail;

		$data['profile_pic'] = $a->logo_url;

        $data['skype_id'] = $a->skype_id;

        $data['whatsapp_id'] = $a->whatsapp_id;

		//get specialized schools
		$specialized_schools = DB::table('colleges_by_agencies as cba')
								->join('colleges as c', 'c.id', '=', 'cba.college_id')
								->select('cba.agency_id', 'cba.college_id', 'c.school_name')
								->get();
		$temp_arr = array();
		foreach ($specialized_schools as $school) {
			$tt = array();
			$tt['school_name'] = $school->school_name;
			$tt['school_id'] = $school->college_id;
			array_push($temp_arr, $tt);
		}
		$data['specialized_schools'] = $temp_arr;

		//get custom services
		$custom_services = ServicesByAgency::where('agency_id', $data['agency_collection']->agency_id)->get();
		$tmp_service_arr = array();
		foreach ($custom_services as $service) {
			$tmp_service_arr[] = $service->name;
		}
		$data['custom_services'] = $tmp_service_arr;

		$days_of_operation = AgencyProfileInfoHours::getBusinessDays($data['agency_collection']->agency_id);

		$data['days_of_operation'] = $days_of_operation;

		return $data;
	}

	public function saveProfileInfo(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$college_counseling = isset($input['CollegeCounseling']) ? $input['CollegeCounseling'] : 0;
		$tutoring_center = isset($input['TutoringCenter']) ? $input['TutoringCenter'] : 0;
		$test_preparation = isset($input['TestPreparation']) ? $input['TestPreparation'] : 0;
		$international_student_assistance = isset($input['InternationalStudentAssistance']) ? $input['InternationalStudentAssistance'] : 0;
		
		$user = User::find($data['user_id']);
		$user->fname = ucwords($input['fname']);
		$user->lname = ucwords($input['lname']);

		$user->save();

		Session::put('userinfo.session_reset', 1);

		$local_agency = $data['agency_collection'];

		// -- saving schools this agent is specialized in
		$colleges_by_agency = new CollegesByAgency();
		$specialized_in = isset($input['schools_specialized_in']) ? $input['schools_specialized_in'] : null;
		$tmp_array = array();

		if( isset($specialized_in) && !empty($specialized_in) ){
			foreach ($specialized_in as $school) {
				$colByAgency_attr = array('college_id' => $school);
				$colByAgency_val = array('agency_id' => $local_agency->agency_id, 'college_id' => $school);
				$update_colByAgency = CollegesByAgency::updateOrCreate($colByAgency_attr, $colByAgency_val);
			}
		}

		// -- saving custom services added by agent
		$services_by_agencies = new ServicesByAgency();
		$services_added = isset($input['custom_services']) ? $input['custom_services'] : null;
		$tmp_services = array();
		if( isset($services_added) && !empty($services_added) ){
			foreach ($services_added as $service) {
				$services_attr = array('name' => $service);
				$services_vals = array('agency_id' => $local_agency->agency_id, 'name' => $service);
				$update_services = ServicesByAgency::updateOrCreate($services_attr, $services_vals);
			}
			
		}

		$agency = Agency::find($local_agency->agency_id);

		if (isset($input['days_of_operation']) && $input['days_of_operation'] != '') {
				$days_of_operation = json_decode($input['days_of_operation']);
				AgencyProfileInfoHours::insertOrUpdate('profile', $local_agency->agency_id, $days_of_operation);

		}

		if (isset($input['services']) && $input['services'] != '') {
			$services = json_decode($input['services']);
			AgencyProfileInfoServices::removeOldAndAddNewServices($local_agency->agency_id, $services);
		}

		$profile_pic = isset($input['agencyProfilePic']) ? $input['agencyProfilePic'] : null;

		if( isset($profile_pic) && !empty($profile_pic) ){
			$rand_num = mt_rand(0, 10000);
			$rankImg = $profile_pic;
			$imgPath = $rankImg->getRealPath();
			$imgName = $rankImg->getClientOriginalName();
			$imgExtension = $rankImg->getClientOriginalExtension();
			$imgMimeType = $rankImg->getMimeType();
			$saveToAWS = $user->id . '_profilePic_' . date('Y-m-d') . '_' . $rand_num . "." . strtolower($imgExtension);

			// upload to aws regardless of filetype
			$s3 = AWS::get('s3');
			$s3->putObject(array(
				'ACL' => 'public-read',
				'Bucket' => 'asset.plexuss.com/agency/profilepics',
				'Key' => $saveToAWS,
				'SourceFile' => $imgPath
			));

			$agency->logo_url = $saveToAWS;
		}

		$agency->name = $input['name'];
		$agency->web_url = $input['web_url'];
		$agency->phone = $input['phone'];
		$agency->country = $input['country'];
		$agency->city = $input['city'];
		$agency->state = $input['state'];
		$agency->college_counseling = $college_counseling;
		$agency->tutoring_center = $tutoring_center;
		$agency->test_preparation = $test_preparation;
		$agency->international_student_assistance = $international_student_assistance;
		$agency->detail = $input['detail'];

        isset($input['skype_id']) ? $agency->skype_id = $input['skype_id'] : NULL;

        isset($input['whatsapp_id']) ? $agency->whatsapp_id = $input['whatsapp_id'] : NULL;

		$agency->save();

		return 'complete';
	}

	public function getMatchedCollegesForThisUser(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$user_id = $input['user_id'];

		$ret = array();

		$crc = new CollegeRecommendationController;
		$matched_colleges = $crc->findCollegesForThisUser($user_id, true);
		
		$tmp_matched_colleges = array();
		$tmp_college_id_arr   = array();

		foreach ($matched_colleges as $key) {

			if (!in_array($key['college_id'], $tmp_college_id_arr)) {
				$tmp_college_id_arr[]    = $key['college_id'];

				$this_tmp = array();
			
				$this_tmp['college_id']  = $key['college_id'];
				$this_tmp['school_name'] = $key['school_name'];
				$this_tmp['slug'] = $key['slug'];
				$this_tmp['logo_url']	 = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key['logo_url'];
				$this_tmp['has_applied'] = -1;

				$uac = UsersAppliedColleges::on('rds1')->where('user_id', $user_id)
													   ->where('college_id', $key['college_id'])
													   ->first();

				if (isset($uac)) {
					$this_tmp['has_applied'] = $uac->submitted;
				}

				$ret[] = $this_tmp;
			}
		}
			
		return json_encode($ret);
	}

	public function removeAgentsSpecializedSchool( $college_id ){

		if( $college_id == null ){
			return;
		}

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$matchThese = ['agency_id' => $data['agency_collection']->agency_id, 'college_id' => $college_id];

		$removingSchool = CollegesByAgency::where($matchThese)->delete();

		return 'success';
	}

	public function removeCustomAgentService(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Input::all();
		$matchThese = ['agency_id' => $data['agency_collection']->agency_id, 'name' => $input];

		$removingSchool = ServicesByAgency::where($matchThese)->delete();

		return 'success';
	}

	public function notFirstTimeAgentAnymore(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$agency = Agency::find($data['agency_collection']->agency_id);
		$agency->first_time_agent = 0;
		$agency->save();

		return 'complete';		
	}

	public function getAgencyMessages(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'agency-messages';
		$data['title'] = 'Agency Messages';

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		$data['stickyUsr'] = "";
		$data['hashed_uid'] = $data['remember_token'];

		return View('agency.messaging', $data);
	}

	public function loadProfileData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$page = explode('-', $input['currentPage'])[1];

		$agency_id = $data['agency_collection']->agency_id;

		$user_id = Crypt::decrypt($input['hashed_id']);

		$recruitMeList = DB::table('agency_recruitment as r')
		->join('users as u', 'u.id', '=', 'r.user_id')
		->leftjoin('college_recommendations as cre', function($join)
		 {
		   $join->on('cre.user_id', '=', 'r.user_id');
		   $join->on('cre.agency_id', '=', 'r.agency_id');

		 })
		->leftjoin('scores as s', 's.user_id', '=','r.user_id')
		->leftjoin('colleges as c', 'c.id','=', 'u.current_school_id')
		->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')
		->leftjoin('objectives as o', 'o.user_id', '=', 'r.user_id')
		->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
		->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
		->leftjoin('professions as p', 'p.id', '=', 'o.profession_id')
		->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
		->leftjoin('notification_topnavs as nt', function($join)
		 {
		    $join->on('nt.type_id', '=' , 'u.id');
		    $join->on('nt.submited_id', '=' , DB::raw(Session::get('userinfo.id')) );
		    $join->on('nt.type', '=', DB::raw("'user'"));
			$join->on('nt.command', '=', DB::raw("9"));
		 })
		->leftjoin('plexuss_users_verifications as puv', 'puv.user_id', '=', 'u.id')
		->select(
			DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
			'p.profession_name',
			'cre.reason',
			'nt.id as is_notified',
			'tpt.id as tpt_id',
			'cphl.id as is_handshake_paid',
			'puv.status as plexuss_status', 'puv.verified_skype', 'puv.phonecall_verified')
		->select('r.*',
			'u.fname', 'u.lname', 'u.in_college', 'u.id as user_id', 'u.hs_grad_year', 'u.college_grad_year', 'u.profile_img_loc', 'u.financial_firstyr_affordibility', 'u.email as user_email', 'u.phone as user_phone', 'u.interested_school_type', 

			'u.profile_percent', 'u.planned_start_term', 'u.planned_start_yr',
			'u.skype_id', 'u.address as userAddress', 'u.email as userEmail',
			'u.city as userCity', 'u.state as userState', 'u.zip as userZip', 'u.phone as userPhone', 'u.txt_opt_in as userTxt_opt_in', 'u.email_confirmed', 'u.verified_phone', 'u.country_id', 'u.birth_date',

			's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.toefl_total', 's.ielts_total',
			'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
			'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
			'co.country_code', 'co.country_name',
			'dt.display_name as degree_name',
			'm.name as major_name',
			'p.profession_name',
			'cre.reason',
			'nt.id as is_notified',
			'o.degree_type', 'o.major_id')

		->where('r.agency_id', '=', $agency_id)
		->where('u.id', '=', $user_id);
		// ->groupBy('u.id')
		// ->orderBy('r.created_at', 'desc');

		// if(isset($page)){
		// 	if($page == "pending"){
		// 		$data['currentPage'] = 'agency-pending';
		// 		$data['title'] = 'Pending';
		// 		$recruitMeList = $recruitMeList->where('r.agency_recruit', 1)
		// 									   ->where('r.user_recruit', 0)
		// 									   ->where('r.active', 1);

		// 		if (Cache::has(env('ENVIRONMENT') .'_'.'AgencyController_pending_'.$data['user_id'])) {
		// 			$obj = Cache::get(env('ENVIRONMENT') .'_'.'AgencyController_pending_'.$data['user_id']);
		// 			$obj['skip'] += $obj['take'];

		// 			Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_pending_'.$data['user_id'], $obj, 60);

		// 			$recruitMeList = $recruitMeList->take($obj['take'])
		// 										   ->skip($obj['skip']);
		// 		}else{
		// 			$obj = array();
		// 			$obj['take'] = 15;
		// 			$obj['skip'] = 0;
		// 			Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_pending_'.$data['user_id'], $obj, 60);
		// 			$recruitMeList = $recruitMeList->take($obj['take']);
		// 		}

		// 	}
		// 	if($page == "approved"){
		// 		$data['currentPage'] = 'agency-approved';
		// 		$data['title'] = 'Approved';
		// 		$recruitMeList = $recruitMeList->where('r.agency_recruit', 1)
		// 									   ->where('r.user_recruit', 1)
		// 									   ->where('r.active', 1);

		// 		if (Cache::has(env('ENVIRONMENT') .'_'.'AgencyController_approved_'.$data['user_id'])) {
		// 			$obj = Cache::get(env('ENVIRONMENT') .'_'.'AgencyController_approved_'.$data['user_id']);
		// 			$obj['skip'] += $obj['take'];

		// 			Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_approved_'.$data['user_id'], $obj, 60);

		// 			$recruitMeList = $recruitMeList->take($obj['take'])
		// 										   ->skip($obj['skip']);
		// 		}else{
		// 			$obj = array();
		// 			$obj['take'] = 15;
		// 			$obj['skip'] = 0;
		// 			Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_approved_'.$data['user_id'], $obj, 60);
		// 			$recruitMeList = $recruitMeList->take($obj['take']);
		// 		}
		// 	}
		// 	if($page == "removed"){
		// 		$data['currentPage'] = 'agency-removed';
		// 		$data['title'] = 'Removed';
		// 		$recruitMeList = $recruitMeList->where('r.active', 0)
		// 									   ->where('r.agency_recruit', 1)
		// 									   ->where('r.user_recruit', 1);

		// 		if (Cache::has(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id'])) {
		// 			$obj = Cache::get(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id']);
		// 			$obj['skip'] += $obj['take'];

		// 			Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id'], $obj, 60);

		// 			$recruitMeList = $recruitMeList->take($obj['take'])
		// 										   ->skip($obj['skip']);
		// 		}else{
		// 			$obj = array();
		// 			$obj['take'] = 15;
		// 			$obj['skip'] = 0;
		// 			Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_removed_'.$data['user_id'], $obj, 60);
		// 			$recruitMeList = $recruitMeList->take($obj['take']);
		// 		}
		// 	}
		// 	if($page == "rejected"){
		// 		$data['currentPage'] = 'agency-rejected';
		// 		$data['title'] = 'Rejected';
		// 		$recruitMeList = $recruitMeList->where('r.active', 1)
		// 									   ->where('r.agency_recruit', -1)
		// 									   ->where('r.user_recruit', 1);

		// 		if (Cache::has(env('ENVIRONMENT') .'_'.'AgencyController_rejected_'.$data['user_id'])) {
		// 			$obj = Cache::get(env('ENVIRONMENT') .'_'.'AgencyController_rejected_'.$data['user_id']);
		// 			$obj['skip'] += $obj['take'];

		// 			Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_rejected_'.$data['user_id'], $obj, 60);

		// 			$recruitMeList = $recruitMeList->take($obj['take'])
		// 										   ->skip($obj['skip']);
		// 		}else{
		// 			$obj = array();
		// 			$obj['take'] = 15;
		// 			$obj['skip'] = 0;
		// 			Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_rejected_'.$data['user_id'], $obj, 60);
		// 			$recruitMeList = $recruitMeList->take($obj['take']);
		// 		}
		// 	}
		// }else{
		// 	$data['currentPage'] = 'agency-inquiries';
		// 	$data['title'] = 'Inquiries';
		// 	$page = 'inquiries';
		// 	// $recruitMeList = $recruitMeList->where('r.agency_recruit', 0)
		// 	// 							   ->where('r.user_recruit', 1)
		// 	// 							   ->where('r.active', 1);
			

		// 	if (Cache::has(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id'])) {
		// 		$obj = Cache::get(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id']);
		// 		$obj['skip'] += $obj['take'];

		// 		Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id'], $obj, 60);

		// 		$recruitMeList = $recruitMeList->take($obj['take'])
		// 									   ->skip($obj['skip']);
		// 	}else{
		// 		$obj = array();
		// 		$obj['take'] = 15;
		// 		$obj['skip'] = 0;
		// 		Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_'.$data['user_id'], $obj, 60);
		// 		$recruitMeList = $recruitMeList->take($obj['take']);
		// 	}
		// }
		
		// Tracking pages model
		$tp = new TrackingPage;

		$key = $recruitMeList->first();

		$tmp = array();

		//paid for approved section
		if (isset($key->paid)) {
			$tmp['paid'] = $key->paid;
		}

		$tmp['rec_id'] = $key->id;

		$tmp['agency_name'] = $data['agency_collection']->name;

		$tmp['logo_url'] = $data['agency_collection']->logo_url;

		$tmp['page'] = $page;
		$tmp['currentPage'] = 'agency-' . $page;

		$tmp['name'] = ucwords($key->fname) . ' '. ucwords($key->lname);
		$tmp['skype_id'] = isset($key->skype_id) ? strtolower($key->skype_id) : $key->skype_id;
		$tmp['userPhone'] = $key->userPhone;
        $tmp['userEmail'] = isset($key->userEmail) ? strtolower($key->userEmail) : $key->userEmail;
		$tmp['userAddress'] = isset($key->userAddress) ? ucwords($key->userAddress) : $key->userAddress;
		$tmp['userCity']  = isset($key->userCity) ? ucwords($key->userCity) : $key->userCity;
		$tmp['userState'] = isset($key->userState) ? strtoupper($key->userState) : $key->userState;
		$tmp['userZip']   = $key->userZip;
		$tmp['country_id'] = isset($key->country_id) ? $key->country_id : null;

		$tmp['degree_id'] = isset($key->degree_type) ? $key->degree_type : NULL;
		$tmp['bachelor_plan'] = $data['bachelor_plan'];
		$tmp['is_sales'] = isset($data['is_sales']) ? $data['is_sales'] : NULL;
		$tmp['is_plexuss'] = isset($data['is_plexuss']) ? $data['is_plexuss'] : NULL;

		$tmp['start_term'] = ucwords($key->planned_start_term. " ". $key->planned_start_yr);
		$tmp['birth_date'] = isset($key->birth_date) ? $key->birth_date : NULL;
		$tmp['interested_school_type'] = $key->interested_school_type;

		$tmp['plexuss_status']  = isset($key->plexuss_status) ? $key->plexuss_status : 0;
		$tmp['verified_skype']  = isset($key->verified_skype) ? $key->verified_skype : 0;
		$tmp['phonecall_verified']  = isset($key->phonecall_verified) ? $key->phonecall_verified : 0;
		$tmp['email_confirmed'] = isset($key->email_confirmed) ? $key->email_confirmed : 0;
		$tmp['verified_phone']  = isset($key->verified_phone) ? $key->verified_phone : 0;

		$tmp['profile_percent'] = isset($key->profile_percent) ? $key->profile_percent : null;

		$tmp['profession_name'] = isset($key->profession_name) ? $key->profession_name : NULL;

		$in_college = $key->in_college;

		$tmp['in_college'] = $key->in_college;

		if (!isset($key->hs_grad_year) || $key->hs_grad_year == 0) {
			$tmp['hs_grad_year'] = 'N/A';
		}else{
			$tmp['hs_grad_year'] = $key->hs_grad_year;
		}

		if (!isset($key->college_grad_year) || $key->college_grad_year == 0) {
			$tmp['college_grad_year'] = 'N/A';
		}else{
			$tmp['college_grad_year'] = $key->college_grad_year;
		}
		
		$tmp['profile_img_loc'] = $key->profile_img_loc;

		//print_r('<pre>'.$key.'</pre>');exit();
		if($in_college){
			
			if(isset($key->overall_gpa)){
				$tmp['gpa'] = $key->overall_gpa;
			}else{
				$tmp['gpa'] = 'N/A';
			}

			if(isset($key->collegeName)){
				$tmp['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->collegeName)))); 

				$tmp['school_city'] = $key->collegeCity;
				$tmp['school_state'] = $key->collegeState;
				if ($tmp['current_school'] == "Home Schooled") {
					$tmp['address'] = $tmp['current_school'];
				}else{
					$tmp['address'] = $tmp['current_school'].', '.$tmp['school_city']. ', '.$tmp['school_state'];
				}
				
			}else{
				$tmp['current_school'] = 'N/A';
				$tmp['school_city'] = 'N/A';
				$tmp['school_state'] = 'N/A';

				$tmp['address'] = 'N/A';
			}

		}else{

			if(isset($key->hs_gpa)){
				$tmp['gpa'] = $key->hs_gpa;
			}else{
				$tmp['gpa'] = 'N/A';
			}
			

			if(isset($key->hsName)){
				$tmp['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->hsName)))); 
				$tmp['school_city'] = $key->hsCity;
				$tmp['school_state'] = $key->hsState;

				if ($tmp['current_school'] == "Home Schooled") {
					$tmp['address'] = $tmp['current_school'];
				}else{
					$tmp['address'] = $tmp['current_school'].', '.$tmp['school_city']. ', '.$tmp['school_state'];
				}
			}else{
				$tmp['current_school'] = 'N/A';
				$tmp['school_city'] = 'N/A';
				$tmp['school_state'] = 'N/A';

				$tmp['address'] = 'N/A';
			}
		}

		$tmp_address = substr($tmp['address'], -4, 3);
		if ($tmp_address == ', ,') {
			$tmp['address'] = substr($tmp['address'], 0, strlen($tmp['address'])-4);
		}

		if($tmp['gpa'] == "0.00"){
			$tmp['gpa'] = "N/A";
		}
		if($tmp['current_school'] !="N/A"){
		if ($tmp['current_school'] == "Home Schooled") {
				$tmp['current_school'] = $tmp['current_school'];
			}else{
				$tmp['current_school'] = $tmp['current_school'].', '.$tmp['school_city']. ', '.$tmp['school_state'];
			}
		}

		if(isset($key->sat_total)){
			$tmp['sat_score'] = $key->sat_total;
		}else{
			$tmp['sat_score'] = 'N/A';
		}

		if(isset($key->act_composite)){
			$tmp['act_composite'] = $key->act_composite;
		}else{
			$tmp['act_composite'] = 'N/A';
		}


		if($tmp['gpa'] == "0.00"){
			$tmp['gpa'] = "N/A";
		}

		if($tmp['sat_score'] == 0 ){
			$tmp['sat_score'] = 'N/A';
		}
		
		if ($tmp['act_composite'] == 0) {
			$tmp['act_composite'] = 'N/A';
		}

		if(isset($key->toefl_total)){
			$tmp['toefl_total'] = $key->toefl_total;
		}else{
			$tmp['toefl_total'] = 'N/A';
		}
		if ($tmp['toefl_total'] == 0) {
			$tmp['toefl_total'] = 'N/A';
		}

		if(isset($key->ielts_total)){
			$tmp['ielts_total'] = $key->ielts_total;
		}else{
			$tmp['ielts_total'] = 'N/A';
		}
		if ($tmp['ielts_total'] == 0) {
			$tmp['ielts_total'] = 'N/A';
		}


		if(isset($key->financial_firstyr_affordibility)){
			$tmp['financial_firstyr_affordibility'] = '$'.$key->financial_firstyr_affordibility;
		}else{
			$tmp['financial_firstyr_affordibility'] = 'N/A';
		}

		if ($tmp['financial_firstyr_affordibility'] === 0) {
			$tmp['financial_firstyr_affordibility'] = 'N/A';
		}

		if (isset($key->country_code)) {
			$tmp['country_code'] = $key->country_code;
			$tmp['country_name'] = $key->country_name;
		}else{
			$tmp['country_code'] = 'N/A';
			$tmp['country_name'] = 'N/A';			
		}
		

		$tmp['student_user_id'] = $key->user_id;

		$tmp['date'] = date_format(new DateTime($key->created_at), 'm/d/Y');

		if (isset($key->degree_name)) {

			$degree_name = $key->degree_name;
			$major_name = $key->major_name;
			$profession_name = $key->profession_name;
		}else{
			$degree_name = NULL;
			$major_name = NULL;
			$profession_name = NULL;

		}
		
		
		if ($degree_name == "") {
			$tmp['objective'] = null;
		}else{
			$tmp['objective'] = "I would like to get a/an ".$degree_name." in ".$major_name.". My dream would be to one day work as a(n) ".$profession_name;
		}
		// $tmp['major'] = $major_name;

		// Get majors
	  	$objectives = Objective::select('m.name')
	  						   ->leftJoin('majors as m', 'm.id', 'objectives.major_id')
	  						   ->where('objectives.user_id', $user_id)
	  						   ->get();

	  	if (isset($objectives)) {
	  		$tmp['major'] = '';
	  		foreach($objectives as $index => $major) {
	  			$tmp['major'] .= ( $index + 1 !== count($objectives) ) ? 
	  				( $major->name . ', ' ) : $major->name;
	  		}
	  	}

	  	// Get sponsors
	  	$sponsors = SponsorUserContacts::on('rds1')
	  								   ->select('option', 'fname', 'lname', 'phone', 'email', 'title', 'org_name', 'contact_name', 'relation')
									   ->where('user_id', $user_id)
									   ->get();

	  	$tmp['sponsors'] = $sponsors->toArray();

	  	$uac = DB::connection('rds1')->table('users_applied_colleges as uac')
									 ->join('colleges as c', 'c.id', '=', 'uac.college_id')
									 ->leftjoin('colleges_application_status as cas', 'c.id', '=', 'cas.college_id')
									 ->select('c.school_name', 'c.logo_url', 'c.slug', 'c.id as college_id', 'cas.status', 'uac.submitted')
									 ->where('uac.user_id', $user_id)
									 ->where('cas.user_id', $user_id)
									 ->groupBy('c.id')
									 ->orderBy(DB::raw('ISNULL(cas.status), cas.status'), 'ASC')
									 ->get();

		$tmp['applied_colleges'] = $uac;

		if (isset($key->type)) {
			$tmp['recommendation_type'] = $key->type;
		}else{
			$tmp['recommendation_type'] = 'not_filtered';
		}
		
		//Why Recommended block ends

		$tmp['hand_shake'] = 0;

		if(isset($key->agency_recruit) && isset($key->user_recruit) 
			&& $key->agency_recruit == 1 && $key->user_recruit == 1){
			$tmp['hand_shake'] = 1;
		}elseif (isset($key->agency_recruit) && $key->agency_recruit == -1) {
			$tmp['hand_shake'] = -1;
		}
		
		
		if( isset($key->is_notified) ){
			$tmp['is_notified'] = true;
		}else{
			$tmp['is_notified'] = false;
		}

		$tmp['hashed_id'] = Crypt::encrypt($key->user_id);
		$tmp['user_email'] = $key->user_email;
		$tmp['user_phone'] = $key->user_phone;

		$tmp['note'] = '';
		$tmp['note_updated_at'] = '';

		if(isset($key->note)){
			$tmp['note'] = $key->note;
		}
		if(isset($key->updated_at)){
			$tmp['note_updated_at'] = $this->xTimeAgo($key->updated_at ,date("Y-m-d H:i:s")); 
		}

		//applied student
		if( isset($key->applied) ){
			$tmp['applied'] = $key->applied;
		}

		$user_id = $key->user_id;

		$usr_college_info = array();
		$usr_college_info['name'] = $data['school_name'];
		$usr_college_info['slug'] = $data['school_slug'];
		$usr_college_info['logo'] = substr($data['school_logo'], strrpos($data['school_logo'], '/') + 1);
		$usr_college_info['page_views'] = $tp->getNumCollegeView($user_id, $data['school_slug']);

		$tmp['college_info'] = $usr_college_info;

		$rec = Recruitment::where('user_id', $user_id)
							->join('colleges as c', 'c.id', '=','recruitment.college_id')
							->select('c.id as college_id', 'c.school_name', 'c.slug','c.logo_url')
							->where('recruitment.user_id', $user_id)
							->where('c.id', '!=', 7916) // Do not show Plexuss as a competitor.
							->get();

		$competitor_colleges = array();

		foreach ($rec as $key) {

			$arr = array();
			$arr['name'] = $key->school_name;
			$arr['slug'] = $key->slug;
			$arr['logo'] = $key->logo_url;
			$arr['page_views'] = $tp->getNumCollegeView($user_id, $key->slug);

			$competitor_colleges[] = $arr;
		}

		$competitor_colleges = $this->record_sort($competitor_colleges, 'page_views', true);
		$tmp['competitor_colleges'] = $competitor_colleges;


		$trc = new Transcript;

		$trc = $trc->getUsersTranscript($user_id);
		$uploads_arr = array();

		$tmp['transcript'] = false;
		$tmp['resume'] = false;
		$tmp['ielts'] = false;
		$tmp['toefl'] = false;
		$tmp['financial'] = false;
		$tmp['prescreen_interview'] = false;
		$tmp['other'] 				= false;
		$tmp['essay']				= false;
		$tmp['passport']			= false;

		// set uploads with upload of application type 
		$arr = array();
		$arr['doc_type'] = 'application';
		$arr['mime_type'] = 'text/html';
		$arr['path'] = '/view-student-application/'.$tmp['hashed_id'];
		$domain = env('CURRENT_URL');

		$arr['transcript_name'] = '/generatePDF?url='.$domain.$arr['path'];

		$uploads_arr[] = $arr;

		foreach ($trc as $key) {
			$arr = array();

			$arr['doc_type'] = $key->doc_type;
			$arr['path'] = $key->transcript_path. $key->transcript_name;
			$arr['transcript_name'] = $key->transcript_name;
			$arr['transcript_label'] = $key->label;
			$arr['transcript_id'] = $key->id;

			$arr['mime_type'] = $this->getMimeTypeFromURL($arr['path']);


			if ($key->doc_type == 'transcript') {
				$tmp['transcript'] = true;
			}
			if ($key->doc_type == 'resume') {
				$tmp['resume'] = true;
			}
			if ($key->doc_type == 'ielts') {
				$tmp['ielts'] = true;
			}
			if ($key->doc_type == 'toefl') {
				$tmp['toefl'] = true;
			}
			if ($key->doc_type == 'financial') {
				$tmp['financial'] = true;
			}

			$uploads_arr[] = $arr;
		}

		$tmp['upload_docs'] = $uploads_arr;

		$data['inquiry_list'] [] = $tmp;

		if (isset($data['inquiry_list']) && count($data['inquiry_list']) == 15) {
			$data['has_searchResults'] = true;
		}else{
			$data['has_searchResults'] = false;
		}

		$ctry = new Country;
		$data['country_list'] = $ctry->getAllCountriesAndIds();

		return View('agency.profilePane', $data);
	}

	private function getBucketTotals(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$agency_id = $data['agency_collection']->agency_id;

		$tmp = [];

		$totals = AgencyRecruitment::on('rds1')
								   ->select('abn.name', DB::raw('count(*) as total'))
								   ->leftJoin('agency_bucket_names as abn', 'abn.id', '=', 'agency_recruitment.agency_bucket_id')
								   ->where('agency_id', $agency_id)
								   ->groupBy('abn.name')
								   ->get();

		foreach ($totals as $index => $bucket) {
			$tmp['num_of_' . $bucket->name] = $bucket->total;
		}

		return $tmp;
	}

	private function loadInquirieAdmin($data, $page = null, $is_ajax = null, $input = NULL, $filter_audience = NULL){
		$data['title'] = 'Agent Inquiries | College Recruitment | Plexuss.com';

		$data['inquiry_list'] = array();

		// get all students part of inquiries
		$agency_id = $data['agency_collection']->agency_id;

		$agency_name = $data['agency_collection']->name;

		isset($agency_name) ? $data['agency_name'] = $agency_name : null;	

		$page = explode('-', $data['currentPage'])[1];

		// commonly used filters
		if (isset($input) && !empty($input) && isset($input['orderBy']) && isset($input['sortBy'])) {
            $data['column_orders']['orderBy'] = $input['orderBy'];
            $data['column_orders']['sortBy']  = $input['sortBy'];
        }

        if(isset($input) && !empty($input['display'])){
            if(intval($input['display']) == 15 || intval($input['display']) == 30 || intval($input['display']) == 50 || intval($input['display']) == 100 || intval($input['display']) == 200) {
                $data['display_option'] = intval($input['display']);
            }else {
                $data['display_option'] = 15;
            }
        }

        if (isset($data['agency_collection'])) {
			$agency = $data['agency_collection'];
		}
		
		if (isset($agency->logo_url)) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $agency->logo_url;
		} else if (isset($data['profile_img_loc'])) {
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$recruitMeList = AgencyRecruitment::on('rds1')
										  ->select('agency_recruitment.user_id', 'agency_recruitment.updated_at', 'u.fname', 'u.lname', 'u.profile_percent', 'u.in_college', 'co.country_code', 'co.country_name', 'nt.id as is_notified',
										  	  DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name,
										  	  		   IF(u.in_college = 1, s.overall_gpa, s.hs_gpa) as gpa"))
										  ->leftJoin('users as u', 'u.id', '=', 'agency_recruitment.user_id')
										  
										  ->leftjoin('objectives as o', 'o.user_id', '=', 'u.id')
										  ->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
										  ->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
										  ->leftjoin('scores as s', 's.user_id', '=', 'u.id')
										  ->leftjoin('transcript as tpt', 'tpt.user_id', '=', 'u.id')

										  ->leftJoin('countries as co', 'co.id', '=' ,'u.country_id')
										  ->leftjoin('notification_topnavs as nt', function($join) {
										      $join->on('nt.type_id', '=' , 'u.id');
										      $join->on('nt.submited_id', '=' , DB::raw(Session::get('userinfo.id')) );
										      $join->on('nt.type', '=', DB::raw("'user'"));
											  $join->on('nt.command', '=', DB::raw("9"));
										  })
										  ->leftJoin('agency_bucket_names as abn', 'abn.id', '=', 'agency_recruitment.agency_bucket_id')
										  ->where('agency_id', $agency_id)
										  ->where('abn.name', $page);

		if (isset($filter_audience)) {
			//because users does not contain 'applied' or 'enrolled'
			//apply those first to recruitment table before joining the user table
			if(array_key_exists("applied", $filter_audience)){
				if(isset($filter_audience['applied'])){
					$recuitMeList = $recruitMeList->where("r.applied", "=", "1");
				}

			}
			if(array_key_exists("enrolled", $filter_audience)){
				if(isset($filter_audience['enrolled'])){
					$rectuirMeList = $recruitMeList->where("r.enrolled", "=", "1");
				}
			}


			$arrObj = $this->convertInputsForFilters($filter_audience);
			$filter_audience_crf = new CollegeRecommendationFilters;
			$filter_audience_qry = $filter_audience_crf->globalMethodGenerateFilterQry($arrObj, true);

			$filter_audience_qry = $filter_audience_qry->select('userFilter.id as filterUserId');
			$tmp_qry = $this->getRawSqlWithBindings($filter_audience_qry);
			$recruitMeList = $recruitMeList->join(DB::raw('('.$tmp_qry.')  as t1'), 't1.filterUserId' , '=', 'u.id');
										   
		}
		

		$total_results = $recruitMeList->distinct('u.id')->count('u.id');
		$data['total_results'] = $total_results;

		if (Cache::has(env('ENVIRONMENT') .'_'.'AgencyController_'.$data['currentPage'].'_'.$data['user_id'])) {
			$obj = Cache::get(env('ENVIRONMENT') .'_'.'AgencyController_'.$data['currentPage'].'_'.$data['user_id']);
			$obj['skip'] += $obj['take'];
			$obj['take'] = isset($data['display_option'])? $data['display_option'] : 15;
		}else{
			$obj = array();
			$obj['take'] = isset($data['display_option'])? $data['display_option'] : 15;
			$obj['skip'] = 0;
		}

		$data['current_viewing'] = $obj['skip'] + $obj['take'];

		if($data['current_viewing'] > $data['total_results'])
            $data['current_viewing'] = $data['total_results'];

        if ($data['total_results'] > $data['current_viewing']) {
        	$data['has_more_results'] = true;
        }else{
        	$data['has_more_results'] = false;
        }

		Cache::put(env('ENVIRONMENT') .'_'.'AgencyController_'.$data['currentPage'].'_'.$data['user_id'], $obj, 60);

		$recruitMeList = $recruitMeList->take($obj['take'])
									   ->skip($obj['skip']);

		// default order is created_at and type
		if (!isset($data['column_orders'])) {
			$recruitMeList = $recruitMeList->orderBy('agency_recruitment.updated_at', 'desc');
		} else {
			// Sort columns based on the inputs
			if ($data['column_orders']['orderBy'] == 'name') {
				$recruitMeList = $recruitMeList->orderBy(DB::raw('TRIM(u.fname)'), $data['column_orders']['sortBy'])
											   ->orderBy(DB::raw('TRIM(u.lname)'), $data['column_orders']['sortBy']);
			}
			if ($data['column_orders']['orderBy'] == 'major') {
				$recruitMeList = $recruitMeList->orderBy(DB::raw('`m`.name IS NULL'))->orderBy('m.name', $data['column_orders']['sortBy']);
			}
			if ($data['column_orders']['orderBy'] == 'country') {
				$recruitMeList = $recruitMeList->orderBy(DB::raw('`co`.country_name IS NULL'))->orderBy('co.country_name', $data['column_orders']['sortBy']);
			}
			if ($data['column_orders']['orderBy'] == 'uploads') {
				$recruitMeList = $recruitMeList->orderBy('tpt.id', $data['column_orders']['sortBy']);
			}
			if ($data['column_orders']['orderBy'] == 'date') {
				$recruitMeList = $recruitMeList->orderBy('agency_recruitment.updated_at', $data['column_orders']['sortBy']);
			}
			if ($data['column_orders']['orderBy'] == 'gpa') {
				$recruitMeList = $recruitMeList->orderBy('gpa', $data['column_orders']['sortBy']);
			}
		}

		$recruitMeList = $recruitMeList->groupBy('u.id')->get();

		$totals = $this->getBucketTotals();							

		$data = array_merge($data, $totals);

		$recruitMeList = $recruitMeList->toArray();

		foreach ($recruitMeList as $index => $user) {			
			$trc = new Transcript;
			$trc = $trc->getUsersTranscript($user['user_id']);

			$tmp = [];

			$tmp['name'] = $user['fname'] . ' ' . $user['lname'];

			$tmp['transcript'] = false;
			$tmp['resume'] = false;
			$tmp['ielts'] = false;
			$tmp['toefl'] = false;
			$tmp['financial'] = false;
			$tmp['prescreen_interview'] = false;
			$tmp['other'] 				= false;
			$tmp['essay']				= false;
			$tmp['passport']			= false;

			foreach ($trc as $key) {
				if ($key->doc_type == 'transcript') {
					$tmp['transcript'] = true;
				}
				if ($key->doc_type == 'resume') {
					$tmp['resume'] = true;
				}
				if ($key->doc_type == 'ielts') {
					$tmp['ielts'] = true;
				}
				if ($key->doc_type == 'toefl') {
					$tmp['toefl'] = true;
				}
				if ($key->doc_type == 'financial') {
					$tmp['financial'] = true;
				}
			}

		  	// Get time lapsed, date format in database ('YYYY-MM-DD hh:mm:ss';)
			$updated_at = $recruitMeList[$index]['updated_at'];
			$time_elapsed = $this->timeElapsed($updated_at);

			$tmp['time_elapsed'] = $time_elapsed;

			// Format date to MM/DD/YY
			$matches = preg_split('/\s+/', $updated_at);
			$date_matches = explode('-', $matches[0]);
			$date = $date_matches[1] . '/' . $date_matches[2] . '/' . $date_matches[0];

			$tmp['date'] = $date;

			// Get majors
		  	$objectives = Objective::select('m.name', 'd.initials', 'd.display_name')
		  						   ->leftJoin('majors as m', 'm.id', 'objectives.major_id')
		  						   ->leftJoin('degree_type as d', 'd.id', 'objectives.degree_type')
		  						   ->where('objectives.user_id', $user['user_id'])
		  						   ->get();

		  	if (isset($objectives) && isset($objectives[0]) && !empty($objectives)) {
		  		$tmp['degree'] = ['initials' => $objectives[0]->initials, 'display_name' => $objectives[0]->display_name];
		  		$tmp['major'] = '';
		  		foreach($objectives as $index => $major) {
		  			$tmp['major'] .= ( $index + 1 !== count($objectives) ) ? 
		  				( $major->name . ', ' ) : $major->name;
		  		}
		  	}

			// Send only hashed_id to frontend, remove user_id
			$tmp['hashed_id'] = Crypt::encrypt($user['user_id']);

			// Get GPA
			$tmp['gpa'] = $user['gpa'];

			$user['hand_shake'] = 0;

			$data['inquiry_list'][] = array_merge($user, $tmp);

		}


		///////////// Audience Filter Requirements ////////////

		// get all countries info
		if (Cache::has(env('ENVIRONMENT') .'_all_countries')) {
			$countries = Cache::get(env('ENVIRONMENT') .'_all_countries');
		}else{

			$countries_raw = Country::all()->toArray();
			$countries = array();
			$countries[''] = 'Select...';

			foreach( $countries_raw as $val ){
				$countries[$val['country_name']] = $val['country_name'];
			}

			Cache::put(env('ENVIRONMENT') .'_all_countries', $countries, 120);
		}

		$data['countries'] = $countries;

		//get states
        if (Cache::has(env('ENVIRONMENT') .'_all_states')) {
            $states = Cache::get(env('ENVIRONMENT') .'_all_states');
        }else{

            $states_raw = DB::table('states')->get();
            $states = array();
            $states[''] = 'Select...';

            foreach( $states_raw as $val ){
                $states[$val->state_name] = $val->state_name;
            }
            Cache::put(env('ENVIRONMENT') .'_all_states', $states, 120);
        }

        $data['states'] = $states;
        $cities = array('' => 'Select state first' );
		$data['cities'] = $cities;

        //get departments
        if (Cache::has(env('ENVIRONMENT') .'_all_dep_name')) {
            $dep_name = Cache::get(env('ENVIRONMENT') .'_all_dep_name');
        }else{
            $dep = new Department;
            $dep_name = $dep->getAllDepartments();
            Cache::put(env('ENVIRONMENT') .'_all_dep_name', $dep_name, 120);
        }
        $data['departments'] = $dep_name;
        $majors = array('' => 'Select department first' );
        $data['majors'] = $majors;
        //get ethnicity
        if (Cache::has(env('ENVIRONMENT') .'_all_eth')) {
            $eth = Cache::get(env('ENVIRONMENT') .'_all_eth');
        }else{
            $eth = new Ethnicity;
            $eth = $eth->getAllUsEthnicities();
            Cache::put(env('ENVIRONMENT') .'_all_eth', $eth, 120);
        }
        $data['ethnicities'] = $eth;

        //get religion
        if (Cache::has(env('ENVIRONMENT') .'_all_religions')) {
            $religions = Cache::get(env('ENVIRONMENT') .'_all_religions');
        }else{
            $religions = new Religion;
            $religions = $religions->getAllUsReligions();
            Cache::put(env('ENVIRONMENT') .'_all_religions', $religions, 120);
        }
        $data['religions'] = $religions;

         //get college application states
        if (Cache::has(env('ENVIRONMENT') .'college_application_states')) {
            $application_states = Cache::get(env('ENVIRONMENT') .'college_application_states');
        }else{
            $application_states = new CollegesApplicationsState;
            $application_states = $application_states->getAllApplicationState();
            Cache::put(env('ENVIRONMENT') .'college_application_states', $application_states, 420);
        }
        $data['application_states'] = $application_states;

        // - Military Affiliations
        $military_affiliation_raw = MilitaryAffiliation::all()->toArray();
        $military_affiliation_arr = array();
        foreach( $military_affiliation_raw as $mar ){
            $military_affiliation_arr[ $mar[ 'id' ] ] = $mar[ 'name' ];
        }
        $military_affiliation_arr = array( '' => 'Select...' ) + $military_affiliation_arr;

        $data['military_affiliation_arr'] = $military_affiliation_arr;
		/////// END Audience Filter Requirements //////////

        if ($is_ajax || (isset($input['sortBy']) && isset($input['orderBy']))) {
        	return View('agency.bucketResults', $data);
        } else {
			return View('agency.inquiriesView', $data);
		}
	}

	public function changeStudentAgencyBucket($input = NULL, $data = NULL) {
		if (!isset($data)) {
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
		}
		
		!isset($input) ? $input = Request::all() : NULL;
		
		if (!isset($input['hashed_id']) || !isset($input['bucket_name']) || !isset($data['agency_collection'])) {
			return 'fail';
		}

		try {
			$user_id = Crypt::decrypt($input['hashed_id']);
		} catch (\Exception $e) {
			return 'Bad user id';
		}

		$agency_id = $data['agency_collection']->agency_id;

		$abn = AgencyBucketNames::select('id', 'display_name')->where('name', $input['bucket_name'])->first();

		// Unable to find bucket info based on name.
		if (!isset($abn)) {
			return 'fail';
		}

		$attributes = [ 'agency_id' => $agency_id, 'user_id' => $user_id ];

		AgencyRecruitment::where($attributes)->update(['agency_bucket_id' => $abn->id]);

		AgencyUserBucketLogs::logCurrent($agency_id, $user_id);

		$ret = [];

		$ret['success'] = 1;
		$ret['display_name'] = $abn->display_name;

		return json_encode($ret);
	}

	public function undoStudentAgencyBucketChange() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$input = Request::all();
		$ret = [];

		if (!isset($input['hashed_id']) && !isset($data['agency_collection']->agency_id)) {
			$ret['success'] = 0;
			return json_encode($ret);
		}

		$agency_id = $data['agency_collection']->agency_id;

		try {
			$user_id = Crypt::decrypt($input['hashed_id']);
		} catch (\Exception $e) {
			$ret['success'] = 0;
			return json_encode($ret);
		}

		// Get recruitment id
		$recruitment = AgencyRecruitment::on('rds1')
										->select('id')
									    ->where('user_id', $user_id)
									    ->where('agency_id', $agency_id)
									    ->first();
		
		if (isset($recruitment)) {							    
	        // Find current log id of the active log.
			$active_log = AgencyUserBucketLogs::on('rds1')
											  ->select('id')
											  ->where('agency_recruitment_id', $recruitment->id)
											  ->where('active', 1)
											  ->first();
			
			// Couldn't find active any active logs
			if (!isset($active_log)) {
				$ret['success'] = 0;
				return json_encode($ret);
			}								  
			
			$undo_log = AgencyUserBucketLogs::on('rds1')
									        ->select('id', 'agency_bucket_id as bucket_id')
									        ->where('agency_recruitment_id', $recruitment->id)
									        ->where('active', 0)
									        ->where('id', '<', $active_log->id)
									    	->orderBy('id', 'desc')
									   		->first();

			// Couldn't find any previous non-active logs.
			if (!isset($undo_log)) {
				$ret['success'] = 0;
				return json_encode($ret);
			}

			$attributes = [ 'agency_id' => $agency_id, 'user_id' => $user_id ];

			AgencyRecruitment::where($attributes)->update(['agency_bucket_id' => $undo_log->bucket_id]);

			AgencyUserBucketLogs::logCurrent($agency_id, $user_id);

			AgencyUserBucketLogs::where('agency_recruitment_id', $recruitment->id)
					     	    ->update(['active' => 0]);

			AgencyUserBucketLogs::where('id', $undo_log->id)
		     	    			->update(['active' => 1]);

			$abn = AgencyBucketNames::select('name', 'display_name')->where('id', $undo_log->bucket_id)->first();

			$ret['success'] = 1;
			$ret['bucket_name'] = $abn->name;
			$ret['display_name'] = $abn->display_name;

			return json_encode($ret);

		} else {
			$ret['success'] = 0;
			return json_encode($ret);
		}
	}
	
	public function setRecommendationRecruit($user_id = NULL, $recruit_bool = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if($user_id == NULL || $data['agency_collection'] == NULL){
			return "Invalid school or user id";
		}

		if ($recruit_bool == 0) {
			$cr = CollegeRecommendation::where('agency_id', $data['agency_collection']->agency_id)
									   ->where('user_id', $user_id)
									   ->update(array('active' => -1));
			$ar = AgencyRecruitment::where('agency_id', $data['agency_collection']->agency_id)
									   ->where('user_id', $user_id)
									   ->update(array('active' => 0));
		}else{

			$token = Hash::make($user_id. '%'. $data['agency_collection']->agency_id);

			$token = str_replace("/", "", $token);

			$token = urlencode($token);

			$attr = array('user_id' => $user_id, 'agency_id' => $data['agency_collection']->agency_id );

			$val = array('user_id' => $user_id, 'agency_id' => $data['agency_collection']->agency_id,
						 'user_recruit' => 0, 'agency_recruit' => $recruit_bool, 'active' => 1, 
						 'token' => $token);
				
			$tmp = AgencyRecruitment::updateOrCreate($attr, $val);

			$cr = CollegeRecommendation::where('agency_id', $data['agency_collection']->agency_id )
									   ->where('user_id', $user_id)
									   ->update(array('active' => 0));

			$mda = new MandrillAutomationController;
			$mda->agencyWantsToRecuruitUser($user_id);
			   
			////*************Add Notification to the user *********************///////
			// $ntn = new NotificationController();
			// $ntn->create( $data['agency_collection']->name, 'user', 5, null, $data['user_id'] , $user_id);	

		}
	}

	public function setRecruitmentNote(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (!isset($input['note']) && !isset($input['user_id'])) {
			return 'Error! #1091';
		}

		$note = $input['note'];
		$user_id = $input['user_id'];

		$rec = AgencyRecruitment::where('user_id', $user_id)
							->where('agency_id', $data['agency_collection']->agency_id)
							->update(array('note' => $note, 'updated_at' => date( "Y-m-d H:i:s" )));
		
		$arr = $this->iplookup();
		
		if (isset($arr['time_zone'])) {
			// date_default_timezone_set($arr['time_zone']);
			$now = Carbon::now($arr['time_zone']);
			return $now->toTimeString();
		}
		
		return date('H:i');
	}


	public function setRecruit($user_id = NULL, $recruit_bool = NULL, $is_remove = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if($user_id == NULL || $data['agency_collection'] == NULL){
			return "Invalid school or user id";
		}

		$recruitment = AgencyRecruitment::where('user_id', $user_id)
						->where('agency_id', $data['agency_collection']->agency_id)
						->first();
		if ($recruitment->agency_recruit == 0 && $recruitment->user_recruit == 1 && $recruit_bool == 1) {
			//Payment section
			$this->chargeAgencyPerInquiry($data['agency_collection'], $user_id);
		}

		if (isset($is_remove)) {
			$recruitment->active = 0;
			$recruitment->save();
		}else{
			$recruitment->agency_recruit = $recruit_bool;
			$recruitment->save();
		}
		
		////*************Add Notification to the user *********************///////
		if($recruit_bool != -1 && !isset($is_remove)){
			$ntn = new NotificationController();
			$ntn->create( $data['agency_collection']->name, 'user', 8, null, $data['user_id'] , $user_id);	
		}
		
	}

	public function setRestore($user_id = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if($user_id == NULL || $data['agency_collection'] == NULL){
			return "Invalid school or user id";
		}


		$recruitment = AgencyRecruitment::where('user_id', $user_id)
						->where('agency_id', $data['agency_collection']->agency_id)
						->first();
		$recruitment->active = 1;
		$recruitment->save();
	}

	/*
	 * Returns the contact list of a user. with latest message, name, picture, 
	 * and the date latest message was sent to.
	 *
	 */
	public function getUsrTopics($receiver_id = NULL, $type =NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if(!isset($data['agency_collection'])){
			return Redirect::to('/');
		}

		$data['currentPage'] = "agency-messages";
		
		if($receiver_id != NULL){
			$data['stickyUsr'] = $receiver_id;
		}else{
			$data['stickyUsr'] = "";
		}

		$cmc = new CollegeMessageController;
		$data['topicUsr'] = $cmc->getAllThreadsAgency($data, $receiver_id);

		$data['hashed_uid'] = $data['remember_token'];
		
		if(!isset($type)){
			$type = '';
		}
		$data['sticky_thread_type'] = $type;
		
		$data['school_name'] = $data['agency_collection']->name;
		$data['school_logo']= 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/'.$data['agency_collection']->logo_url;

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
		
		//$tmp = $data['topicUsr'];

		//$data['topicUsr'] = json_encode($tmp);
		//$data['topicUsr'] = array_reverse($data['topicUsr']);
		return View( 'agency.messaging', $data);	
	}

	
	/*
	 * Returns the list of threads on heart beat.
	 * 
	 *
	 */
	public function getThreadListHeartBeat($receiver_id = NULL, $type = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$sender_user_id = Session::get('userinfo.id');

		$cmc = new CollegeMessageController;

		$data['topicUsr'] = $cmc->getAllThreadsAgency($data, $receiver_id, $type);
		$data['currentPage'] = "admin-messages";

		/*
		if (Cache::has(env('ENVIRONMENT') .'_'.$sender_user_id.'_msg_thread_ids')){	

			$thread_arr = Cache::get(env('ENVIRONMENT') .'_'.$sender_user_id.'_msg_thread_ids');
			foreach ($thread_arr as $key => $value) {
				$data['topicUsr'][] = Cache::get(env('ENVIRONMENT') .'_'.'thread_'.$value.'_data');
			}
		}else{
			$data['topicUsr'] = $this->getAllThreads($data, $receiver_id);
		}
		*/
		//$data['topicUsr'] = array_reverse($data['topicUsr']);

		return json_encode($data);
	}


	private function getRecommendCnt($data){

		$agency_id = $data['agency_collection']->agency_id;

		if (!isset($agency_id)) {
			return "You are not an admin!";
		}

		return CollegeRecommendation::where('agency_id', $agency_id)
							->where('created_at', '>=', $this->min_cron_date)
							->where('created_at', '<=', $this->max_cron_date)
							->where(function ($query) {
							    $query->orwhere('active', 1)
							          ->orwhere('active', -1);
							})
							->count();
	}

	private function getRecommendCntTotal($data){

		$agency_id = $data['agency_collection']->agency_id;

		return CollegeRecommendation::where('agency_id', $agency_id)
							->where('created_at', '>=', $this->min_cron_date)
							->where('created_at', '<=', $this->max_cron_date)
							->count();
	}	

	private function setRecommendationExpirationDate(){

		$tweleveAM ="00:00:00";
		$sixAM ="06:00:00";
		if (time() >= strtotime($tweleveAM) && time() <= strtotime($sixAM)) {
			$this->min_cron_date = Carbon::now()->yesterday();
			$this->min_cron_date = $this->min_cron_date->modify(self::CRONTIME);

			$this->max_cron_date = Carbon::now()->today();
			$this->max_cron_date = $this->max_cron_date->modify(self::CRONTIME);
		}else{
			$this->min_cron_date = Carbon::now()->today();
			$this->min_cron_date = $this->min_cron_date->modify(self::CRONTIME);

			$this->max_cron_date = Carbon::now()->tomorrow();
			$this->max_cron_date = $this->max_cron_date->modify(self::CRONTIME);
		}

		$expiresIn = $this->xTimeAgo(date("Y-m-d H:i:s"), $this->max_cron_date);

		$expiresIn  = str_replace(" ago", "", $expiresIn);

		return $expiresIn;
	}

	private function getApprovedCnt(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		return AgencyRecruitment::where('user_id', '!=', $data['user_id'])
							->where('agency_id', $data['agency_collection']->agency_id)
							->where('user_recruit', 1)
							->where('agency_recruit',1)
							->where('active', 1)
							->count();
	}

	private function getInquiryCnt(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$nt = NotificationTopNav::where('type', 'user')
									->where('name', $data['agency_collection']->name)
									->where('command', 9)
									//->where('created_at', '>=', Carbon::now()->subMonths(6))
									->select('type_id')
									->get()->toArray();

		return AgencyRecruitment::where('user_id', '!=', $data['user_id'])
							->where('agency_id', $data['agency_collection']->agency_id)
							->where('user_recruit', 1)
							->where('agency_recruit',0)
							->where('active', 1)
							->whereNotIn('user_id', $nt)
							->count();
	}

	private function getInquiryCntTotal(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		return AgencyRecruitment::where('user_id', '!=', $data['user_id'])
							->where('agency_id', $data['agency_collection']->agency_id)
							->where('user_recruit', 1)
							->where('agency_recruit',0)
							->where('active', 1)
							->count();
	}

	/**
	 * getChatCnt
	 *
	 * this method returns the number of "Active" messages on dashboard
	 *
	 * @return (int) (count)
	 */
	private function getAgencyMessageCnt(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$my_user_id = Session::get('userinfo.id');

		$cnt = DB::table('college_message_threads as cmt')
		->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
		->where('cmt.is_chat' ,0)
		->where('cmtm.user_id' , $my_user_id)
		->where('cmtm.agency_id', $data['agency_collection']->agency_id)
		->sum('num_unread_msg');

		return $cnt;
	}

	private function calculatedRemainingDate( $oldTime, $newTime ) {
		$timeCalc = strtotime( $newTime ) - strtotime( $oldTime );
		if ( $timeCalc > ( 60*60*24 ) ) {$timeCalc = round( $timeCalc/60/60/24 ) . " days ago";}
		else if ( $timeCalc > ( 60*60 ) ) {$timeCalc = round( $timeCalc/60/60 ) . " hrs ago";}
		else if ( $timeCalc > 60 ) {$timeCalc = round( $timeCalc/60 ) . " mins ago";}
		else if ( $timeCalc > 0 ) {$timeCalc .= " secs ago";}
		return $timeCalc;
	}

	private function agencyAdminPanelCnt($data){
	
		$rec = AgencyRecruitment::join('users as u', 'u.id', '=', 'agency_recruitment.user_id')
						  ->where('agency_recruitment.agency_id', $data['agency_collection']->agency_id)
						  ->leftjoin('notification_topnavs as nt', function($join) use($data)
							 {
							    $join->on('nt.type_id', '=' , 'u.id');
							    $join->where('nt.type', '=', 'user');
								$join->where('nt.command', '=', 9);
								$join->where('nt.submited_id', '=', $data['user_id'] );
							 })
						  ->select('agency_recruitment.*', 'nt.id as is_notified')
						  ->groupBy('agency_recruitment.id')
						  ->get();

		foreach ($rec as $key) {
			if ($key->active == 1 && $key->user_recruit == 1 && $key->agency_recruit == 0) {
				if ($key->is_notified == null) {
					$this->num_of_inquiry_new++;
				}
				$this->num_of_inquiry++;
			}elseif ($key->active == 1 && $key->user_recruit == 0 && $key->agency_recruit == 1) {
				if ($key->is_notified == null) {
					$this->num_of_pending_new++;
				}
				$this->num_of_pending++;
			}elseif ($key->active == 1 && $key->user_recruit == 1 && $key->agency_recruit == 1) {
				if ($key->is_notified == null) {
					$this->num_of_approved_new++;
				}
				$this->num_of_approved++;
			}elseif ($key->active == 0 && $key->user_recruit == 1 && $key->agency_recruit == 1) {
				if ($key->is_notified == null) {
					$this->num_of_removed_new++;
				}
				$this->num_of_removed++;
			}elseif ($key->active == 1 && $key->user_recruit == 1 && $key->agency_recruit == -1) {
				if ($key->is_notified == null) {
					$this->num_of_rejected_new++;
				}
				$this->num_of_rejected++;
			}
		}
		$this->num_of_recommended = $this->getRecommendCnt($data);
	}

	/*********** Dashboard Ajax Calls ******************/
	public function getDashboardReportingOne($is_overall = NULL){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$agency_id = $data['agency_collection']->agency_id;

		$firstDayofMonth = Carbon::now()->startOfMonth()->toDateTimeString();
		$lastDayofMonth  = Carbon::now()->endOfMonth()->toDateTimeString();

		if (!isset($is_overall)) {
			$queries = $this->topDashboardQueries($agency_id, $firstDayofMonth, $lastDayofMonth);
		}else{
			$queries = $this->topDashboardQueries($agency_id);
		}
		
		$totals = $queries['agency_recruitment'];						  
		$totals = $totals->get();

		$app_cnt = $queries['app_cnt'];							
		$app_cnt = $app_cnt->distinct('ucq.user_id')
						   ->count('ucq.user_id');


		$enroll_cnt = $queries['enroll_cnt'];
		$enroll_cnt = $enroll_cnt->distinct('r.user_id')
							     ->count('r.user_id');


		$ret = array();
		$ret['removed']       = 0;
		$ret['opportunities'] = 0;
		$ret['applications']  = $app_cnt;
		$ret['enrolled']      = $enroll_cnt;

		foreach ($totals as $key) {
			$ret[$key->name] = $key->total;
		}

		return json_encode($ret);
	}

	public function getDashboardReportingTwo($number_of_months = 3){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$ret = array();
		$agency_id = $data['agency_collection']->agency_id;

		$agency = Agency::on('rds1')->find($agency_id);

		for ($i = 0; $i < $number_of_months; $i++) {
			$firstDayofThisMonth = Carbon::now()->startOfMonth()->subMonth($i);
			$lastDayofThisMonth  = Carbon::now()->endOfMonth()->subMonth($i);

			$ret[$firstDayofThisMonth->format('F')] = array();
			$queries = $this->topDashboardQueries($agency_id, $firstDayofThisMonth->toDateTimeString(), $lastDayofThisMonth->toDateTimeString());

			$totals = $queries['agency_recruitment'];						  
			$totals = $totals->get();

			$app_cnt = $queries['app_cnt'];							
			$app_cnt = $app_cnt->distinct('ucq.user_id')
							   ->count('ucq.user_id');


			$enroll_cnt = $queries['enroll_cnt'];
			$enroll_cnt = $enroll_cnt->distinct('r.user_id')
								     ->count('r.user_id');

			$tmp = array();
			$tmp['removed']       = 0;
			$tmp['opportunities'] = 0;
			$tmp['applications']  = $app_cnt;
			$tmp['enrolled']      = $enroll_cnt;
			$tmp['pacing']        = 0;

			isset($agency->application_pacing) ? $tmp['pacing'] = $tmp['applications'] - $agency->application_pacing : NULL;
			foreach ($totals as $key) {
				$tmp[$key->name] = $key->total;
			}

			$ret[$firstDayofThisMonth->format('F')] = $tmp;
		}

		$ret['year'] = $firstDayofThisMonth->format('Y');
		$ret['application_pacing'] = $agency->application_pacing;
		$ret['opportunity_pacing'] = $agency->opportunity_pacing;
		$ret['enrolled_pacing']    = $agency->enrolled_pacing;

		return json_encode($ret);
	}

	public function getDashboardBoxesNumbers(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$ret = array();
		$agency_id = $data['agency_collection']->agency_id;

		$queries = $this->topDashboardQueries($agency_id);

		$tmp = array();
		$tmp['leads']		      = 0;
		$tmp['leads_new']		  = 0;
		$tmp['removed']           = 0;
		$tmp['removed_new']       = 0;
		$tmp['opportunities'] 	  = 0;
		$tmp['opportunities_new'] = 0;

		$totals = $queries['agency_recruitment'];	
		$totals = $totals->get();

		foreach ($totals as $key) {
			$tmp[$key->name] = $key->total;
		}

		$totals = $queries['agency_recruitment'];	
		$totals = $totals->leftjoin('notification_topnavs as nt', function($join)
						 {
						    $join->on('nt.type_id', '=' , 'agency_recruitment.user_id');
						    $join->on('nt.submited_id', '=' , DB::raw(Session::get('userinfo.id')) );
						    $join->on('nt.type', '=', DB::raw("'user'"));
							$join->on('nt.command', '=', DB::raw("9"));
						 })
						->whereNull('nt.id');
					  
		$totals = $totals->get();

		foreach ($totals as $key) {
			$tmp[$key->name.'_new'] = $key->total;
		}

		$app_cnt = $queries['app_cnt'];							
		$app_cnt = $app_cnt->distinct('ucq.user_id')
						   ->count('ucq.user_id');


		$tmp['applications']      = $app_cnt;

		$app_cnt_new = $queries['app_cnt'];							
		$app_cnt_new = $app_cnt_new->leftjoin('notification_topnavs as nt', function($join)
							 {
							    $join->on('nt.type_id', '=' , 'agency_recruitment.user_id');
							    $join->on('nt.submited_id', '=' , DB::raw(Session::get('userinfo.id')) );
							    $join->on('nt.type', '=', DB::raw("'user'"));
								$join->on('nt.command', '=', DB::raw("9"));
							 })
						   ->whereNull('nt.id')
						   ->distinct('ucq.user_id')
						   ->count('ucq.user_id');

		$tmp['applications_new']  = $app_cnt_new;

		return json_encode($tmp);
	}

	public function topDashboardQueries($agency_id, $start = NULL, $end = NULL){

		$ret = array();

		$agency_recruitment  = AgencyRecruitment::on('rds1')
											    ->select('abn.name', DB::raw('count(*) as total'))
											    ->leftJoin('agency_bucket_names as abn', function($qry){
											   	 	$qry->on('abn.id', '=', 'agency_recruitment.agency_bucket_id');
											    })
											    ->where('agency_id', $agency_id)
											    // ->where(function($qry){
											   	// 	$qry->orWhere('abn.name', '=', DB::raw("'opportunities'"))
											   	// 	    ->orWhere('abn.name', '=', DB::raw("'removed'"));
											    // })
						
											    ->groupBy('abn.name')
											    ->join('agency_user_bucket_logs as aubl', function($qry){
											   	 	$qry->on('aubl.agency_recruitment_id', '=', 'agency_recruitment.id')
											   			->on('aubl.active', '=', DB::raw("1"));
											    });
	    if (isset($start)) {
	    	$agency_recruitment = $agency_recruitment->where('aubl.created_at', '>=', $start);
	    }
	    if (isset($end)) {
	    	$agency_recruitment = $agency_recruitment->where('aubl.created_at', '<=', $end);
	    }

		$ret['agency_recruitment'] = $agency_recruitment;

		$app_cnt = AgencyRecruitment::on('rds1')
									->join('users_custom_questions as ucq', 'agency_recruitment.user_id', '=', 'ucq.user_id')
									->where('agency_id', $agency_id)
									->where('ucq.application_state', 'submit');

		if (isset($start)) {
			$app_cnt = $app_cnt->where('agency_recruitment.created_at', '>=', $start);
		}		
		if (isset($end)) {
			$app_cnt = $app_cnt->where('agency_recruitment.created_at', '<=', $end);
		}		

		$ret['app_cnt'] = $app_cnt;

		$enroll_cnt = AgencyRecruitment::on('rds1')
									   ->join('recruitment as r', 'agency_recruitment.user_id', '=', 'r.user_id')
									   ->where('agency_id', $agency_id)
									   ->where('r.enrolled', 1);

		if (isset($start)) {
			$enroll_cnt = $enroll_cnt->where('agency_recruitment.created_at', '>=', $start);
		}		
		if (isset($end)) {
			$enroll_cnt = $enroll_cnt->where('agency_recruitment.created_at', '<=', $end);
		}	

		$ret['enroll_cnt'] = $enroll_cnt;

		return $ret;
	}

	public function generateAgencyLeads($data = NULL){
		
		if (!isset($data)) {
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
		}
		
		$agency_id = $data['agency_collection']->agency_id;
		$response = array();

		$ar_cnt = AgencyRecruitment::on('rds1')->where('agency_id', $agency_id)
        								   ->where('agency_bucket_id', 1)
        								   ->count();

        $num_of_applications = 0;
        $num_of_leads        = 0;

        if ($ar_cnt > 0) {
        	$response['status']    = 'failed';
        	$response['error_msg'] = 'Oops! It looks like you still have '. $ar_cnt . ' leads in your account. Please make a decision on them, before receiving more leads.';
        	
        	$response['num_of_applications'] = $num_of_applications;
        	$response['num_of_leads']        = $num_of_leads;

        	return json_encode($response);
        }

		$crc = new CollegeRecommendationController();

        $ret = $crc->createAgencyRec($agency_id);

        
        $response['status']    = $ret['status'];
        $response['error_msg'] = $ret['error_msg'];
        $rec_user_ids = array();
        
        if ($ret['status'] == "success") {
        	$dt = $ret['data'][0];
   
        	foreach ($dt as $key) {

        		if($key['bucket_name'] == 'applications') { 
        			$num_of_applications++; 
        			$abn_id = 3;
        		}
        		if($key['bucket_name'] == 'leads') { 
        			$num_of_leads++; 
        			$abn_id = 1;
        		}

        		$ar = new AgencyRecruitment();
        		
        		$ar->user_id          = $key['user_id'];
        		$ar->agency_id        = $key['agency_id'];
        		$ar->agency_bucket_id = $abn_id;
        		$ar->active           = 1;
        		$ar->save();

        		$input = array();
        		$input['hashed_id']   = Crypt::encrypt($key['user_id']);
        		$input['bucket_name'] = $key['bucket_name'];

        		$this->changeStudentAgencyBucket($input);

        		$rec_user_ids[] = $key['user_id'];
        	}
        }
        
        $response['num_of_applications'] = $num_of_applications;
        $response['num_of_leads']        = $num_of_leads;
        $response['rec_user_ids']		 = $rec_user_ids;

        return json_encode($response);
	}

	public function addReview(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$dt = array();

		$dt['user_id']   = $data['user_id'];
		$dt['agency_id'] = $input['agency_id'];
		$dt['comment']   = $input['comment'];
		$dt['rating']    = $input['rating'];

		$aur = new AgencyUserReview;

		return $aur->add($dt);
	}

	public function getReviews(){
		$input = Request::all();

		$aur = new AgencyUserReview;

		return response()->json($aur->getReviews($input['agency_id']));
	}

	public function updateSearchResults($currentPage = null) {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$filter_audience = Request::all();

		// env('ENVIRONMENT') .'_'.'AgencyController_inquiries_input'
		if(Session::has(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience')) {
			Session::forget(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience');
		}

		Session::put(env('ENVIRONMENT') .'_'.'AgencyController_inquiries_filter_audience', $filter_audience);

		Cache::forget(env('ENVIRONMENT') .'_'.'AgencyController_'.$currentPage.'_'.$data['user_id']);

		if($currentPage == 'agency-leads') {
			return $this->leadsIndex(true, $filter_audience);
		}else if($currentPage == 'agency-opportunities') {
			return $this->opportunitiesIndex(true, $filter_audience);
		}else if($currentPage == 'agency-applications') {
			return $this->applicationsIndex(true, $filter_audience);
		}else if($currentPage == 'agency-removed') {
			return $this->removedIndex(true, $filter_audience);
		}
		else {
			return null;
		}
	}

	public function activateAgency($agency_profile_info_id = NULL){	

		$apf = AgencyProfileInfo::find($agency_profile_info_id);

		$user = User::find($apf->user_id);

		$country  = Country::find($user->country_id);

		$num_of_filtered_rec     = 10;
		$max_num_of_filtered_rec = 20;
		$application_pacing      = 10;
		$opportunity_pacing      = 50;
		$enrolled_pacing         = 2;

		$utm_source = strtolower($apf->company_name);
		$utm_source = str_replace("&", "", $utm_source);
		$utm_source = str_replace("'", "", $utm_source);
		$utm_source = str_replace('"', "", $utm_source);
		$utm_source = str_replace(".", "", $utm_source);
		$utm_source = str_replace(",", "", $utm_source);
		$utm_source = str_replace(" ", "-", $utm_source);

		$agency = new Agency;

		$agency->type 					 = "International Agency";
		$agency->name 					 = $apf->company_name;
		$agency->web_url 				 = $apf->website_url;
        $agency->skype_id                = $apf->skype_id;
        $agency->whatsapp_id             = $apf->whatsapp_id;

		if (isset($apf->profile_photo_url) && !empty($apf->profile_photo_url)) {
			$agency->logo_url 		= str_replace("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/", "", $apf->profile_photo_url);
		} else {
			$agency->logo_url = null;
		}
		
		$agency->phone 					 = $user->phone;
		$agency->detail 			     = $apf->about_company_text;
		$agency->country 				 = $country->country_name;
		$agency->active 				 = 1;
		$agency->num_of_filtered_rec     = $num_of_filtered_rec;
		$agency->max_num_of_filtered_rec = $max_num_of_filtered_rec;
		$agency->application_pacing      = $application_pacing;
		$agency->opportunity_pacing		 = $opportunity_pacing;
		$agency->enrolled_pacing		 = $enrolled_pacing;
		$agency->utm_source				 = $utm_source;

		$agency->save();

		AgencyProfileInfoHours::where('agency_profile_info_id', $agency_profile_info_id)->update(['agency_id' => $agency->id]);
		AgencyProfileInfoServices::where('agency_profile_info_id', $agency_profile_info_id)->update(['agency_id' => $agency->id]);

		$user->is_agency = 1;
		$user->save();

		$apf->agency_id = $agency->id;
		$apf->save();

		$ap = new AgencyPermission;
		$ap->agency_id = $agency->id;
		$ap->user_id   = $user->id;
		$ap->applied_reminder = 1;

		$ap->save();

		return "success";
	}
}
