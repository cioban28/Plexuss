<?php

namespace App\Http\Controllers;

use Request, Session, DB, Validator, AWS, DateTime;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

use Jenssegers\Agent\Facades\Agent;

use App\Http\Controllers\UserRecommendationController;
use App\Http\Controllers\ElasticSearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MandrillAutomationController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\GetStartedController, App\Http\Controllers\NewPortalController;

use App\Recruitment, App\Organization, App\User, App\Major, App\Priority, App\Scholarshipcms;;
use App\CrmNotes;
use App\Score;
use App\UsersCustomQuestion;
use App\PortalNotification;
use App\College;
use App\Profession;
use App\Objective;
use App\Degree;
use App\Country;
use App\OrganizationBranch;
use App\Religion;
use App\Ethnicity;
use App\CollegeMessageThreadMembers;
use App\MilitaryAffiliation;
use App\Transcript;
use App\Subject;
use App\Highschool;
use App\schoolClass;
use App\CarePackageNotifyMe;
use App\ConfirmToken;
use App\CollegeOverviewImages;
use App\OrganizationBranchPermission;
use App\Aor;
use App\CollegePaidHandshakeLog;
use App\CovetedUser;
use App\SettingNotificationLog;
use App\CollegeRecommendation;
use App\AdvancedSearchNote;
use App\AgencyRecruitment;
use App\Agency;
use App\NotificationTopNav;
use App\ExportField;
use App\ExportFieldExclusion;
use App\MessageTemplate;
use App\NewsArticle;
use App\OrganizationPortalUser;
use App\OrganizationPortal;
use App\CollegeMessageThreads;
use App\UsersInvite;
use App\AdImpression;
use App\ApplyClick;
use App\CollegeNewsClick;
use App\CollegeRemoveStudentsFeedback;
use App\PickACollegeView;
use App\PrescreenedUser;
use App\CollegesInternationalTab;
use App\CollegesInternationalTestimonial;
use App\CollegesInternationalAdditionalNote;
use App\CollegesInternationalRequirment;
use App\Department;
use App\CollegesInternationalMajor;
use App\CollegesInternationalAlum;
use App\CollegesInternationalTuitionCost;
use App\CollegeRecommendationFilterLogs;
use App\GradeConversions;
use App\State;
use App\UsersAppliedColleges;
use App\Course;
use App\HonorAward;
use App\ClubOrg;
use App\UsersAppliedCollegesDeclaration;
use App\CollegesApplicationsState;
use App\CollegesApplicationStatus;
use App\SubjectClass;
use App\CollegeApplicaitonAllowedSection;
use App\AorPermission;
use App\PremiumUser;
use App\UsersPremiumEssay;
use App\UsersSalesControl;
use App\AorCollege;
use App\Education;
use App\CollegeList;
use App\UsersFinancialFirstyrAffordibilityLog;
use App\QuizzeResults;
use App\OmniPayModel;
use App\AdClick;
use App\CollegeSubmission;
use App\CollegeRecommendationFilters;
use App\LikesTally, App\OrgSavedAttachment;
use App\GPAConverter, App\GPAConverterHelper;
use App\SponsorUserContacts;
use App\UsersSharedSignup;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\DistributionClient;
use App\DistributionClientFieldMapping;
use App\DistributionClientValueMapping;
use App\NrccuaMajors;
use App\AdRedirectCampaign;
use App\EmailSuppressionList;
use App\PublicProfileProjectsAndPublications;
use App\PublicProfileClaimToFame;
use App\ScholarshipsUserApplied;
use App\EmailResubscribeList;
use App\PlexussCookieAgreement, App\NrccuaQueue;
use App\CollegeSubmissionClientType;
use App\Occupation;

class AjaxController extends Controller {

	private $pendingStudentsData = array();
	private $approvedStudentsData = array();
	// RecruitME Flow methods start
	/*
	Author: ASH
	This function saves the recruitme college for the user if passed the restrictions.
	*/

	public function saveUserRecruitMe($schoolId, $is_api = false, $api_input = null){
		if( $is_api ){
			$data = array();
			$id = $api_input['user_id'];
			$data['user_id'] = $id;
			$input = $api_input;
			$input['type'] = 'recruitMeModal';

		}else{
			$input = Request::all();
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);

			//get id by authcheck
			$id = Auth::id();
			$input = Request::all();

		}

		(!isset($input['is_seen'])) 		? $input['is_seen']		    = 0 : NULL;
		(!isset($input['is_seen_recruit'])) ? $input['is_seen_recruit'] = 0 : NULL;
		(!isset($input['email_sent'])) 		? $input['email_sent'] 		= 0 : NULL;

		(isset($input['user_recruit']))    ? $user_recruit    = $input['user_recruit']    : $user_recruit =1;
		(isset($input['college_recruit'])) ? $college_recruit = $input['college_recruit'] : $college_recruit =0;

		$user = User::find($id);
		//$schoolId = $input['college_id'];

        // Add college to user's list and college's inquiries bucket
        $recruitment_attributes = [
            'user_id' => $data['user_id'],
            'college_id' => $schoolId,
        ];

        $recruitment_values = [
            'user_id' => $data['user_id'],
            'college_id' => $schoolId,
            'status' => 1,
            'user_recruit' => $user_recruit,
            'college_recruit' => $college_recruit,
            'is_seen' => $input['is_seen'],
            'is_seen_recruit' => $input['is_seen_recruit'],
            'email_sent' => $input['email_sent'],
            'type' => 'inquiry',
        ];

        Recruitment::updateOrCreate($recruitment_attributes, $recruitment_values);

        // Check if this college is in nrccua
        $is_nrccua_eligible = DistributionClient::on('rds1')->where('ro_id', 1)
        													// ->where('active', 1)
        													->where('college_id', $schoolId)
        													->first();

        if (isset($is_nrccua_eligible)) {
        	$attr = array('ro_id' => 1, 'college_id' => $schoolId, 'user_id' => $data['user_id']);
			$val  = array('ro_id' => 1, 'college_id' => $schoolId, 'user_id' => $data['user_id']);

			NrccuaQueue::updateOrCreate($attr, $val);
        }

		$data['inquired_list'] = null;

		// Recruitment Modal starts here
		if($input['type'] == 'recruitMeModal'){

			// $usrRecruitMe = DB::table('recruitment')
			// ->select('recruitment.*')
			// ->where('user_id', $user->id)
			// ->get();

			// $tmpo = array();
			// foreach ($usrRecruitMe as $key) {
			// 	array_push($tmpo, $key->college_id);
			// }
			// array_push($tmpo, (int)$schoolId);
			// $data['inquired_list'] = $tmpo;

			// $usrRecruitMeCount = count($usrRecruitMe);

			$data['status'] = "pass";


			if(isset($input['aorSchool'])){
				$aorSchool = $input['aorSchool'];
			}

			// if(isset($input['address'])){
			// // check user contact info
			// 	$valInput = array(
			// 	        'address' => $input['address'],
			// 	        'city' => $input['city'],
			// 	        'state' => $input['state'],
			// 	        'phone' => $input['phone'],
			// 	    );
			// 	$valFilters =  array(
			// 	        'address' => 'regex:/^[a-zA-Z0-9\.,#\- ]+$/',
			// 	        'city' => 'regex:/^[a-zA-Z]+(?:[\s-][a-zA-Z]+)*$/',
			// 	        'state' => 'regex:/^[a-zA-Z\.\- ]+$/',
			// 	        'zip' => 'regex:/^[a-zA-Z0-9\.,\- ]+$/',
			// 	        'phone' => 'regex:/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/',
			// 	    );
			// 	$validator = Validator::make( $valInput, $valFilters );
			// 	if ($validator->fails()){
			// 		$messages = $validator->messages();
			// 		return $messages;
			// 		exit();
			// 	}
			// }

			// save user contact info
			if(isset($input['phone'])) {
				$user->phone = $input['phone'];
			}

			if(isset($input['address'])) {
				$user->address = $input['address'];
			}

			if(isset($input['city'])) {
				$user->city = $input['city'];
			}

			if(isset($input['state'])) {
				$user->state = $input['state'];
			}

			if(isset($input['zip'])) {
				$user->zip = $input['zip'];
			}

			if(isset($input['txt_opt_in'])) {
				$user->txt_opt_in = 1;
			} else {
				$user->txt_opt_in = -1;
			}

			$user->save();

			// for recruitment info
			if(isset($input['reputation'])){

				$reputation = $input['reputation'];
			}else{
				$reputation = 0;
			}
			if(isset($input['location'])){

				$location = $input['location'];
			}else{
				$location = 0;
			}

			if(isset($input['tuition'])){

				$tuition = $input['tuition'];
			}else{
				$tuition = 0;
			}
			if(isset($input['program_offered'])){

				$program_offered = $input['program_offered'];
			}else{
				$program_offered = 0;
			}
			if(isset($input['athletic'])){

				$athletic = $input['athletic'];
			}else{
				$athletic = 0;
			}
			if(isset($input['religion'])){

				$religion = $input['religion'];
			}else{
				$religion = 0;
			}
			if(isset($input['onlineCourse'])){

				$onlineCourse = $input['onlineCourse'];
			}else{
				$onlineCourse = 0;
			}
			if(isset($input['campus_life'])){

				$campus_life = $input['campus_life'];
			}else{
				$campus_life = 0;
			}
			if(isset($input['other'])){

				$other = $input['other'];
			}else{
				$other = 0;
			}

			/*
			foreach ($usrRecruitMe as $school) {
				foreach ($school as $key => $value) {
					if($key == "college_id" && $value == $schoolId &&  $school->status == 1){

						// Already recruited
						$data['status'] = "fail";
						break;
					}

				}

			}
			*/
			// Adding custom question values.

			// Predefined questions
			if (isset($input['toefl_score']) || isset($input['ielts_score']) || (isset($input['englishKnowledge']) && $input['englishKnowledge'] == 'nativeSpeaker')||
				isset($input['itep_score'])  || isset($input['pte_score'])   || isset($input['institute_name'])) {

				$attr = array('user_id' => $data['user_id']);
				$val  = array();

				if (isset($input['toefl_score'])) {
					$val['toefl_total'] = $input['toefl_score'];
				}
				if (isset($input['ielts_score'])) {
					$val['ielts_total'] = $input['ielts_score'];
				}
				if (isset($input['itep_score'])) {
					$val['itep_total'] = $input['itep_score'];
				}
				if (isset($input['pte_score'])) {
					$val['pte_total'] = $input['pte_score'];
				}
				if (isset($input['institute_name'])) {
					$val['english_institute_name'] = $input['institute_name'];
				}
				if (isset($input['englishKnowledge']) && $input['englishKnowledge'] == 'nativeSpeaker') {
					$val['native_english'] = 'yes';
				}

				if (!empty($val)) {
					$update = Score::updateOrCreate($attr, $val);
				}

			}

			// Non predefined questions
			if (isset($input['christian_interested'])) {
				$attr = array('user_id' => $data['user_id']);
				$val  = array('christian_interested' => $input['christian_interested'], 'user_id' => $data['user_id']);

				$update = UsersCustomQuestion::updateOrCreate($attr, $val);
			}

			// End of adding custom questions.

			if ($data['status'] == "pass") {


				$portal_notification_model = PortalNotification::where('user_id','=', $user->id)
															   ->where('school_id', '=', $schoolId)
															   ->first();

				if(isset($portal_notification_model)){

					PortalNotification::destroy($portal_notification_model->id);
				}


				// This block is to determine if the user has the recruitment in their list or trash
				// if trash , we only want to update the current record, if not add a new record
				$tmp = array();
				$tmp['user_id'] = $user->id;
				$tmp['fname'] = $user->fname;
				$tmp['lname'] = $user->lname;
				$tmp['email'] = $user->email;
				$tmp['college_id'] = $schoolId;

				$tmp['reputation'] = $reputation;
				$tmp['location'] = $location;
				$tmp['tuition'] = $tuition;
				$tmp['program_offered'] = $program_offered;
				$tmp['athletic'] = $athletic;
				$tmp['religion'] = $religion;
				$tmp['onlineCourse'] = $onlineCourse;
				$tmp['campus_life'] = $campus_life;
				$tmp['other'] = $other;

				$tmp['school_name'] = College::on('rds1')
											 ->where('id', $schoolId)
											 ->pluck('school_name');

				//handle AOR vs traditional, charging college & sending email

				if (isset($input['source'])) {
					$source = $input['source'];
				}else{
					$source = 'inquiry';
				}

				// $this->setRecruitModular($tmp, $source);

				// $this->CalcIndicatorPercent();
				Session::forget('RecruitCollegeId');
				//GENERATE RECOMMENDATIONS FOR USER
				$urc = new UserRecommendationController;

				if( $is_api ){
					$tmp= $urc->generateCollegeRecommendation($api_input);
				}else{
					$tmp= $urc->generateCollegeRecommendation();
				}

			}


		} else {
			//This is the second form for a new user that is shown after the First Hot modal.
			//I signed up user that has filledo out his profile should not see this.
			// Validation starts here
			$valInput = array(
			        'fname' => $input['fname'],
			        'lname' => $input['lname'],
			        'address' => $input['address'],
			        'city' => $input['city'],
			        'state' => $input['state'],
			        'zip' => $input['zip'],
			        'email' => $input['email'],
			        'phone' => $input['phone'],
			        'birthYear' => $input['birthYear'],
			        'birthMonth' => $input['birthMonth'],
			        'birthDay' => $input['birthDay'],
			        'gender' => $input['gender'],
			        'hs_gpa' => $input['hs_gpa'],
			        'weighted_gpa' => $input['weighted_gpa'],
			        'max_weighted_gpa' => $input['max_weighted_gpa'],
			        'major' => $input['major'],
			        'profession' => $input['profession'],
			        'degree' => $input['degree'],
			        'sat_total' => $input['sat_total'],
			        'act_composite' => $input['act_composite'],
			        'overall_gpa' => $input['overall_gpa'],

			    );
			$valFilters =  array(
			        'fname' => 'required|regex:/^[a-zA-Z ]*$/',
			        'lname' => 'required|regex:/^[a-zA-Z ]*$/',
			        'address' => 'required|regex:/^[a-zA-Z0-9\.,#\- ]+$/',
			        'city' => 'required|regex:/^[a-zA-Z]+(?:[\s-][a-zA-Z]+)*$/',
			        'state' => 'required|regex:/^[a-zA-Z\.\- ]+$/',
			        'zip' => 'regex:/^[a-zA-Z0-9\.,\- ]+$/',
			        'email' => 'required|email',
			        'phone' => 'required|regex:/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/',
			        'birthYear' => 'numeric',
			        'birthMonth' => 'numeric',
			        'birthDay' => 'numeric',
			        'gender' => 'alpha',
			        'hs_gpa' => 'required|regex:/^([0-9]){0,1}\.?([0-9]){0,2}$/',
			        'weighted_gpa' => 'regex:/^([0-9]){0,1}\.?([0-9]){0,2}$/',
			        //'max_weighted_gpa' => 'regex:/^([0-9]){0,1}\.?([0-9]){0,2}$/',
			        'max_weighted_gpa' => 'regex:/^\d+(\.\d{1,2})$/',
			        'major' => 'exists:majors,name',
			        'profession' => 'exists:professions,profession_name',
			        'degree' => 'exists:degree_type,id',
			        'sat_total' => 'numeric',
			        'act_composite' => 'numeric',
			        'overall_gpa' => 'regex:/^([0-9]){0,1}\.?([0-9]){0,2}$/',


			    );

			$validator = Validator::make( $valInput, $valFilters );
			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;
				exit();
			}

			// validation ends here
			// Hot modal starts here
			$user->fname = $input['fname'];
			$user->lname = $input['lname'];
			$user->address = $input['address'];
			$user->city = $input['city'];
			$user->state = $input['state'];
			$user->zip = $input['zip'];
			$user->email = $input['email'];
			$user->phone = $input['phone'];
			$user->birth_date = $input['birthYear']. '-'. $input['birthMonth']. '-'. $input['birthDay'];
			//$user->religion = $input['religion'];
			$user->gender = $input['gender'];
			//$user->married = $input['married'];
			//$user->ethnicity = $input['ethnicity'];
			//$user->children = $input['children'];
			$user->save();

			$scoreAttr = array('user_id' => $user->id);
			$scoreVal  = array('hs_gpa' => $input['hs_gpa'], 'weighted_gpa' => $input['weighted_gpa'], 'max_weighted_gpa' => $input['max_weighted_gpa']
				, 'sat_total' => $input['sat_total'], 'act_composite' => $input['act_composite'], 'overall_gpa' => $input['overall_gpa']);

			$updateScore = Score::updateOrCreate($scoreAttr, $scoreVal);

			$major_id_model = Major::where('name' ,'=', $input['major'])->get();

			$major_id = $major_id_model[0]['id'];

			$profession_id_model = Profession::where('profession_name' ,'=', $input['profession'])->get();

			$profession_id = $profession_id_model[0]['id'];


			$objectiveAttr = array('user_id' => $user->id);
			$objectiveVal  = array('degree_type' => $input['degree'], 'major_id' => $major_id, 'profession_id' => $profession_id);

			$updateobjective = Objective::updateOrCreate($objectiveAttr, $objectiveVal);

			$data['modaltype'] = 'recruitmentModal';
						// list of required fields
			$data['fname'] = $user->fname;
			$data['lname'] = $user->lname;
			$data['email'] = $user->email;
			$data['address'] = $user->address;
			$data['state'] = $user->state;
			$data['zip'] = $user->zip;
			$data['phone'] = $user->phone;
			$usrScores = DB::table('scores')
				->select('hs_gpa', 'sat_total', 'act_composite')
				->where('user_id', $user->id)
				->first();

			if ( isset($usrScores->hs_gpa) && $usrScores->hs_gpa != "" ) {
				$data['hs_gpa'] = $usrScores->hs_gpa;
			}else{
				$data['hs_gpa'] = "";
			}

			$data = $this->getRecruitmentModalData($data, $usrScores, $schoolId);

			//var_dump($data);
			//exit();
			return View( 'private.portal.ajax.recruitmentModal', $data);

		}

		return $data;

	}

	public function saveUserContactInfo() {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		//get id by authcheck
		$input = Request::all();
		$user = User::find($data['user_id']);

		if(isset($user)) {
			if(isset($input['address'])){
				// check user contact info
				$valInput = array(
				        'address' => $input['address'],
				        'city' => $input['city'],
				        'state' => $input['state'],
				        'phone' => $input['phone'],
				    );
				$valFilters =  array(
				        'address' => 'regex:/^[a-zA-Z0-9\.,#\- ]+$/',
				        'city' => 'regex:/^[a-zA-Z]+(?:[\s-][a-zA-Z]+)*$/',
				        'state' => 'regex:/^[a-zA-Z\.\- ]+$/',
				        'zip' => 'regex:/^[a-zA-Z0-9\.,\- ]+$/',
				        'phone' => 'regex:/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/',
				    );

				$validator = Validator::make( $valInput, $valFilters );
				if ($validator->fails()){
					$messages = $validator->messages();
					return $messages;
					exit();
				}
			}
			// save user contact info
			if (!isset($input['area_code'])) {
				$input['area_code'] = '';
			}
			if(isset($input['phone'])) {
				$user->phone = trim($input['area_code']) .' '. trim($input['phone']);
			}

			if(isset($input['address'])) {
				$user->address = $input['address'];
			}

			if(isset($input['city'])) {
				$user->city = $input['city'];
			}

			if(isset($input['state'])) {
				$user->state = $input['state'];
			}

			if(isset($input['zip'])) {
				$user->zip = $input['zip'];
			}

			if(isset($input['txt_opt_in'])) {
				$user->txt_opt_in = 1;
			} else {
				$user->txt_opt_in = -1;
			}

			$user->save();

		} else {
			return 'There is no user information';
		}

		return 'success';
	}

	/*
	Author: ASH

	This method return the user and college scores to be used in recruit me modal
	*/
	public function getUserRecruitMe($schoolId, $is_api = NULL){

		//$this->generateCollegeRecommendation();
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (isset($schoolId) && $data['signed_in'] == 1) {


			$recruitment_attributes = [
	            'user_id' => $data['user_id'],
	            'college_id' => $schoolId,
	        ];

	        $recruitment_values = [
	            'user_id' => $data['user_id'],
	            'college_id' => $schoolId,
	            'status' => 1,
	            'user_recruit' => 1,
	            'type' => 'inquiry',
	        ];

	        Recruitment::updateOrCreate($recruitment_attributes, $recruitment_values);
    	}else{
    		return "not signed in";
    	}

		$user = User::find($data['user_id']);
		$data['selected'] = array();

		// list of required fields
		$data['fname'] 		= $user->fname;
		$data['lname'] 		= $user->lname;
		$data['email'] 		= $user->email;
		$data['address'] 	= $user->address;
		$data['city'] 		= $user->city;
		$data['state'] 		= $user->state;
		$data['zip'] 			= $user->zip;
		$data['phone'] 		= $user->phone;

		$mystring = $data['phone'];
		$findme   = ' ';
		$pos = strpos($mystring, $findme);

		if (isset($data['phone'][0]) && $data['phone'][0] == '+' && $pos < 5) {
			$data['phone'] = substr($data['phone'], $pos);
		}
		$data['txt_opt_in'] = $user->txt_opt_in;
		$data['interested_school_type'] = $user->interested_school_type;
		$data['is_intl_student'] = isset($user->is_intl_student) ? $user->is_intl_student : 0;
		$usrScores = DB::table('scores')
			->select('hs_gpa', 'sat_total', 'act_composite')
			->where('user_id', $user->id)
			->first();

		if ( isset($usrScores->hs_gpa) && $usrScores->hs_gpa != "" ) {
			$data['hs_gpa'] = $usrScores->hs_gpa;
		}else{
			$data['hs_gpa'] = "";
		}

		$obj = DB::table('objectives')
			->select('degree_type', 'major_id', 'profession_id')
			->where('user_id', $user->id)
			->first();


		if( !isset($obj->degree_type) || !isset($obj->major_id) || !isset($obj->profession_id)){

			$data['major'] = '';
			$data['profession'] = '';
			$data['selected']['degree'] = '';

		}else{
			$data['selected']['degree'] = $obj->degree_type;
			$major_id = $obj->major_id;
			$profession_id = $obj->profession_id;
			$m = Major::find($major_id);
			$p = Profession::find($profession_id);

			if (isset($m->name)) {
				$data['major']  = $m->name;
			} else {
				$data['major']  = '';
			}

			if (isset($p->profession_name)) {
				$data['profession'] = $p->profession_name;
			} else {
				$data['profession'] = '';
			}
		}


		$degreeInitArr = Degree::get()->toArray();
		//$degreeArr[] = "";

		$degreeArr = array('' => 'Select...');

		foreach ($degreeInitArr as $k) {
			$id = $k['id'];
			$degreeArr[$id] = $k['display_name'];
		}
		$data['degree'] = $degreeArr;
		$c = new Country;
		$data['countriesAreaCode'] = $c->getAllCountriesAndAreaCodes();
		//end of list of required fields


		// check which modal we gonna populate

		// $modaltype = "";
		// if($data['fname'] != "" && $data['lname'] != "" && $data['email'] != ""
		// 	&& $data['address'] != "" && $data['city'] != "" && $data['state'] != "" && $data['zip'] != ""
		// 	&& $data['phone'] != ""  && $data['hs_gpa'] != "" && $data['selected']['degree']!="" && $data['major']!="" && $data['profession']!="" ){

		// $data['modaltype'] = 'recruitMeModal';
		// $data['schoolId'] = $schoolId;

		// }else
		// {
		// 	$modaltype = 'hotModal';
		// }

		$data = $this->getRecruitmentModalData($data, $usrScores, $schoolId);

		// Revenue Organization code begins
		if (isset($input['ro_id'])) {
			$this_input = array();
			$this_input['ro_id'] = $input['ro_id'];
			$this_input['college_id'] = $schoolId;

			$gsc = new GetStartedController();
			$tmp = $gsc->saveGetStartedThreeCollegesPins($this_input, 'portal');

			if ($tmp['status'] == 'success' && isset($tmp['url'])) {
				return $tmp;
			}
		}

		// Revenue Organization code ends

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
		//no more hot modal
		// if($modaltype == 'recruitMeModal'){
		// }else{
		// 	//HotModal Starts here
		// 	//NOTE: We DON'T need to get all of the form again, since we already have some values from 'require field' check up on top
		// 	$data = $this->getHotModalData($data, $schoolId, $user);
		// }

		if (isset($is_api)) {
			return $data;
		}

		return View( 'private.portal.ajax.recruitmentModal', $data);
	}

	public function getUserRecruitMeJson($schoolId){

		$dt = array();
		$tmp = $this->getUserRecruitMe($schoolId, true);

		$dt['degree'] = $tmp['degree'];
		$dt['countriesAreaCode'] = $tmp['countriesAreaCode'];
		$dt['in_our_network'] = $tmp['in_our_network'];
		$dt['status'] = $tmp['status'];
		$dt['schoolId'] = $tmp['schoolId'];
		$dt['usrScores'] = $tmp['usrScores'];
		$dt['collegeScores'] = $tmp['collegeScores'];
		$dt['modaltype'] = $tmp['modaltype'];
		$dt['school_name'] = $tmp['school_name'];


		return $dt;
	}

	public function saveUserRecruitMeJson($schoolId){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$input['user_id'] 	 = $data['user_id'];
		$input['college_id'] = $schoolId;

        $this->saveUserRecruitMe($schoolId, true, $input);

        return $schoolId;
	}

	public function saveUserRecruitMeJsonMultipleColleges(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$ret = array();
		if (!isset($input['college_ids'])) {
			$ret['status'] = "failed";
			return json_encode($ret);
		}

		foreach ($input['college_ids'] as $key => $value) {
			$schoolId = $value;
			$input['user_id'] 	 = $data['user_id'];
			$input['college_id'] = $schoolId;

			$this->saveUserRecruitMe($schoolId, true, $input);
		}

        $ret['status'] = "success";

        return json_encode($ret);
	}

	private function getCustomQuestions($schoolId, $data){
		// Custom questions part
		$ccqr = DB::connection('rds1')->table('colleges_custom_question_reqs as ccqr')
									  ->join('custom_questions as cq', 'cq.id', '=', 'ccqr.cq_id')
									  ->where('ccqr.college_id', $schoolId)
									  ->get();


		if (isset($ccqr)) {
			foreach ($ccqr as $key) {

				// if predefined or not check if the user already have those scores, if not we are going to show the custom questions
				// We wont show the custom question if the user already have the values.
				if ($key->predefined == 1) {
					$is_continue = false;
					switch ($key->question) {
						case 'tofel-ielts':
							$qry = Score::on('rds1')->where('user_id', $data['user_id'])
													->where(function($qry) {
														$qry->orWhereNotNull('ielts_total')
															->orWhereNotNull('toefl_total')
															->orWhereNotNull('pte_total')
															->orWhereNotNull('itep_total')
															->orWhereNotNull('native_english');
													})

													->first();
							if (isset($qry)) {
								$is_continue = true;
							}

							break;
					}
					if (isset($is_continue) && $is_continue == true) {
						continue;
					}

				}else{
					$qry = DB::connection('rds1')->table($key->table_name)
					     						 ->whereNotNull(DB::raw($key->field_name))
					     						 ->where('user_id', $data['user_id'])
					     						 ->first();
					if (isset($qry)) {
						continue;
					}
				}
				if (!isset($data['custom_questions'])) {
					$data['custom_questions'] = array();
				}

				$tmp = array();
				$tmp['predefined'] = 'no';

				$tmp['title'] = $key->title;
				$tmp['title'] = str_replace("{{school_name}}", $data['school_name'], $tmp['title']);

				if ($key->predefined == 1) {
					$tmp['predefined'] = $key->question;
				}else{
					$tmp['question']   = $key->question;
					$tmp['question']   = str_replace("{{school_name}}", $data['school_name'], $tmp['question']);
					$tmp['type']       = $key->type;
					$tmp['field_name'] = $key->field_name;
				}

				$data['custom_questions'][] = $tmp;
			}
		}

		return $data;
	}

	// We need recruitment modal data values both in get and post method of recruitme functionality, so we setup this functionality

	private function getRecruitmentModalData($data, $usrScores, $schoolId){

		/*
		Already been populated on top to check for required fields.

		////////////////////////
		$usrScores = DB::table('scores')
			->select('hs_gpa', 'sat_total', 'act_composite')
			->where('user_id', $user->id)
			->first();
		*/

		$collegeScores = DB::connection('rds1')->table('colleges_admissions')
											   ->join('colleges', 'colleges.id', '=', 'colleges_admissions.college_id')
											   ->leftjoin('colleges_gdpr as cg', 'colleges.id', '=', 'cg.college_id')
											   ->select('sat_read_75', 'sat_write_75', 'sat_math_75',
												 'act_composite_75', 'colleges.school_name as school_name', 'colleges.in_our_network', 'cg.lang as gdpr', 'cg.disclaimer as gdpr_disclaimer', 'cg.top_msg as gdpr_top_msg')
											   ->where('colleges.id', $schoolId)
											   ->first();

		$data['school_name'] = $collegeScores->school_name;
		$data['in_our_network'] = $collegeScores->in_our_network;
		isset($collegeScores->gdpr) 		  ? $data['gdpr_lang'] = $collegeScores->gdpr : NULL;
		isset($collegeScores->gdpr_disclaimer) ? $data['gdpr_disclaimer'] = $collegeScores->gdpr_disclaimer : NULL;
		isset($collegeScores->gdpr_top_msg)   ? $data['gdpr_top_msg'] = $collegeScores->gdpr_top_msg : NULL;

		// Set pass for status and if checks below fail then change.
		$data['status'] = "pass";

		if ( isset($usrScores->hs_gpa) && $usrScores->hs_gpa != "" ) {
			$hs_gpa = $usrScores->hs_gpa;
		}else{
			$hs_gpa = "N/A";
			$data['status'] = "fail";
		}

		if ( isset($usrScores->sat_total) && $usrScores->sat_total != "" ) {
			$sat_total = $usrScores->sat_total;
		} else{
			$sat_total = "N/A";
			$data['status'] = "fail";
		}

		if ( isset($usrScores->act_composite) && $usrScores->act_composite != "" ) {
			$act_composite = $usrScores->act_composite;
		} else {
			$act_composite = "N/A";
			$data['status'] = "fail";
		}

		$data['schoolId'] = $schoolId;
		$data['usrScores'] = array();

		$data['usrScores']['gpa'] = $hs_gpa;
		$data['usrScores']['sat'] = $sat_total;
		$data['usrScores']['act'] = $act_composite;

		$data['collegeScores'] = array();
		$data['collegeScores']['gpa'] = "N/A";


		$satTotal = $collegeScores->sat_read_75 + $collegeScores->sat_write_75 + $collegeScores->sat_math_75;


		if($satTotal == 0){
			$satTotal = "N/A";
		}

		$act_total = $collegeScores->act_composite_75;


		if($act_total == 0){
			$act_total = "N/A";
		}
		$data['collegeScores']['sat'] = $satTotal;
		$data['collegeScores']['act'] = $act_total;
		$data['modaltype'] = 'recruitMeModal';

		unset($data['aorSchool']);

		$aorSchool = OrganizationBranch::on('rds1')
			->where('school_id', $schoolId)
			->pluck('aor_only');

		$aorSchool = isset($aorSchool[0]) ? $aorSchool[0] : null;

		if(isset($aorSchool) && !empty($aorSchool)){
			$data['aorSchool'] = $aorSchool;
		}

		unset($data['showProfileInfo']);
		unset($data['zipRequired']);

		// Show profile more info modal
		if ($data['country_id'] == 1) {
			if ($data['txt_opt_in'] == 0 || empty($data['phone']) || empty($data['state']) || empty($data['zip']) || empty($data['address']) || empty($data['city'])) {
				$data['showProfileInfo'] = 'showProfileModal';
				$data['zipRequired'] = true;
			}
		}else{
			if ($data['txt_opt_in'] == 0 || empty($data['phone']) || empty($data['address']) || empty($data['city'])) {
				$data['showProfileInfo'] = 'showProfileModal';
				$data['zipRequired'] = false;
			}
		}

		$data = $this->getCustomQuestions($schoolId, $data);

		return $data;
	}

	private function getHotModalData($data, $schoolId, $user){


		$data['modaltype'] = 'hotModal';

		$data['gender'] = $user->gender;

		if($user->birth_date == ""){
			$data['birthMonth'] = "";
			$data['birthDay'] = "";
			$data['birthYear'] = "";

		}else{
			$birth_date =explode('-', $user->birth_date) ;

			$data['birthMonth'] = $birth_date[1];
			$data['birthDay'] = $birth_date[2];
			$data['birthYear'] = $birth_date[0];
		}



		$data['schoolId'] = $schoolId;

		$religionsInitArr = Religion::get()->toArray();

		$religionArr = array();
		$religionArr =  array('' => 'Select...');
		foreach ($religionsInitArr as $k) {
			$id= $k['id'];
			$religionArr[$id] = $k['religion'];
		}
		$data['religions'] = $religionArr;
		$data['selected']['religion'] = $user->religion;

		$data['selected']['gender'] = $user->gender;

		$ethnicityInitArr = Ethnicity::get()->toArray();
		$ethnicityArr = array();
		$ethnicityArr =  array('' => 'Select...');

		foreach ($ethnicityInitArr as $k) {
			$id= $k['id'];
			$ethnicityArr[$id] = $k['ethnicity'];
		}
		$data['ethnicity'] = $ethnicityArr;
		$data['selected']['ethnicity'] = $user->ethnicity;

		$data['selected']['maritalStatus'] = $user->married;

		$data['selected']['children'] = $user->children;

		$scoreModel = Score::where('user_id', '=', $user->id)->first();

		if(isset($scoreModel->hs_gpa)){
			$data['max_weighted_gpa'] = $scoreModel->max_weighted_gpa;
			$data['weighted_gpa'] = $scoreModel->weighted_gpa;
			$data['hs_gpa'] = $scoreModel->hs_gpa;

			$data['sat_total'] = $scoreModel->sat_total;
			$data['act_composite'] = $scoreModel->act_composite;
			$data['overall_gpa'] = $scoreModel->overall_gpa;
		}else{

			$data['max_weighted_gpa'] = '';
			$data['weighted_gpa'] = '';
			$data['hs_gpa'] = '';

			$data['sat_total'] = '';
			$data['act_composite'] = '';
			$data['overall_gpa'] = '';
		}


		return $data;
	}

	/*
		Author: ASH
		Purpose: Get extra college info on a selected college in portal
	*/
	public function getPortalCollegeInfo($schoolId){

		if (Auth::check()){

			$college_info = DB::connection('rds1')->table('colleges as cl')
			->join('colleges_admissions as ca', 'ca.college_id' , '=', 'cl.id')
			->join('colleges_athletics as cat' , 'cat.college_id', '=' , 'cl.id')
			->join('colleges_tuition as ct', 'ct.college_id', '=', 'cl.id')
			->select(DB::raw('cl.id, ca.deadline, ca.percent_admitted, cl.student_faculty_ratio, cl.student_body_total, SUM(ca.sat_read_75 + ca.sat_math_75 +ca.sat_write_75) as sat_total, ca.act_composite_75 as act,
				 cat.class_name as athletic, ct.tuition_avg_in_state_ftug as inStateTuition, ct.tuition_avg_out_state_ftug as outStateTuition'))
			->where('cl.id' , '=', $schoolId)
			->first();

			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			if(isset($college_info)){
				$data['id'] = $college_info->id;
				$data['deadline'] = $college_info->deadline;
				$data['percent_admitted'] = $college_info->percent_admitted;
				$data['student_faculty_ratio'] = $college_info->student_faculty_ratio;
				$data['student_body_total'] = $college_info->student_body_total;
				$data['sat_total'] = $college_info->sat_total;
				$data['act'] = $college_info->act;
				$data['athletic'] = $college_info->athletic;
				$data['inStateTuition'] = $college_info->inStateTuition;
				$data['outStateTuition'] = $college_info->outStateTuition;

			}else{
				$data['deadline'] = '';
				$data['percent_admitted'] = '';
				$data['student_faculty_ratio'] = '';
				$data['student_body_total'] = '';
				$data['sat_total'] = '';
				$data['act'] = '';
				$data['athletic'] = '';
				$data['inStateTuition'] = '';
				$data['outStateTuition'] = '';

			}

			return View( 'private.portal.ajax.manageschool.showcollegeinfo', $data);

		}

	}

	/*
		Author: ASH
		The following method will mark a college  as trash from your list, and recommendation under portal
	*/

	public function getTopNavMessages(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$my_id = Session::get('userinfo.id');

		$cml = CollegeMessageThreadMembers::where('user_id', $my_id );

		if (isset($data['org_branch_id'])) {
			$cml = $cml->where('org_branch_id', $data['org_branch_id'])
						->orWhere('org_branch_id', '-1');
		}

		$cml = $cml->sum('num_unread_msg');

		return $cml;

	}

	public function adduserschooltotrash($is_api = false, $user_id = null, $api_input = null){
		if (Auth::check() || $is_api){

			if( $is_api ){
				$id = $user_id;
			}else{
				$id = Auth::id();
			}

			$user = User::find($id);

			$input = Request::all();

			if( $is_api ){
				$input = $api_input;
			}

			if(isset($input['obj'])){
				$obj = json_decode($input['obj']);
				foreach ($obj as $key => $value) {

					$portal_notification_model = PortalNotification::where('user_id' , '=', $user->id)
						->where('school_id', '=', $value)
						->where('is_recommend' , '=' , 1)
						->first();

					if(isset($portal_notification_model)){
						$portal_notification_model->is_recommend_trash = 1 ;
						$portal_notification_model->save();
					}else{

						$recruitment = Recruitment::where('user_id', '=', $user->id )
						->where('college_id', '=', $value)
						->get();

						if (!empty($recruitment)) {
							foreach($recruitment as $recr){
								$recr->status = 0;
								$recr->save();
							}
						}

					}
				}
			}

		}
	}

	public function restoreSchool(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$user_total_colleges = array();

		$input = Request::all();

		if(isset($input['obj'])){
			$obj = json_decode($input['obj']);
			foreach ($obj as $key) {
				if (isset($key->type)) {
					if ($key->type == "scholarship") {
						$sua = ScholarshipsUserApplied::where('user_id', $data['user_id'])
	 										  ->where('scholarship_id', $key->id)
	 										  ->first();

	 					if (isset($sua)) {
						  	$sua->status      = $sua->last_status;
						  	$sua->last_status = NULL;

						  	$sua->save();
	 					}
					}elseif ($key->type == "college") {
						$recruitment = Recruitment::where('user_id', '=', $data['user_id'])
						->where('college_id', '=', $key->id)
						->get();

						if(!empty($recruitment)){

							$ac = new AorCollege;
							$matches = $ac->addAORCondition($key->id,$data['user_id']);

							foreach($matches as $match){

								$recruitment = Recruitment::where('user_id', '=', $data['user_id'])
									->where('college_id', $key->id)
									->where('aor_id', $match['aor_id'])
									->update(['status' => 1]);
							}
						}else{

							$portal_notification_model = PortalNotification::where('user_id', '=', $data['user_id'])
								->where('school_id', '=', $key->id)
								->first();

							$portal_notification_model->is_recommend_trash = 0;

							$portal_notification_model->save();
						}
					}
				}

			}
		}

	}

	public function clearShowFirstTimeHomepageModal(){


		$id = Auth::id();
		$user = User::find($id);
		$user->showFirstTimeHomepageModal = 0;
		$user->save();


	}

	public function getPersonalInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$data = array();

			$user = DB::table('users')
			->select('users.*', 'ethnicities.ethnicity as ethnicityVal', 'religions.religion as religionVal', 'countries.country_name as country')
			->leftJoin('ethnicities', 'users.ethnicity', '=', 'ethnicities.id')
			->leftJoin('religions', 'users.religion', '=', 'religions.id')
			->leftJoin('countries', 'users.country_id', '=', 'countries.id')
			->where('users.id',$id)
			->first();

			/* Create two arrays to hold high schools and colleges attended
			 * These arrays will hold the user's attended schools for their
			 * school type (college or highschool)
			 */
			// Join colleges and high schools on to educations, based on school_type
			$colleges_query = DB::table( 'educations' )
				->select(
					'educations.user_id',
					'educations.school_id',
					'educations.school_type',
					'colleges.school_name',
					'colleges.slug',
					'colleges.city',
					'colleges.state'
				)
				->join( 'colleges', 'educations.school_id', '=', 'colleges.id' )
				->where( 'educations.school_type', 'college' )
				->where( 'user_id', $id );
			$schools_attended = DB::table( 'educations' )
				->select(
					'educations.user_id',
					'educations.school_id',
					'educations.school_type',
					'high_schools.school_name',
					'high_schools.slug',
					'high_schools.city',
					'high_schools.state'
				)
				->join( 'high_schools', 'educations.school_id', '=', 'high_schools.id' )
				->where( 'educations.school_type', 'highschool' )
				->where( 'user_id', $id )
				->union( $colleges_query )
				->get();

			// Prepend default option to front of dropdown array
			$hs_attended = array( '' => 'Select a school...' );
			$colleges_attended = array( '' => 'Select a school...' );
			// Generate dropdown array
			foreach( $schools_attended as $school_attended ){
				if( $school_attended->school_type == 'highschool' ){
					$hs_attended["Schools you've attended:"][ $school_attended->school_id ] = $school_attended->school_name;
				}
				else{
					$colleges_attended["Schools you've attended:"][ $school_attended->school_id ] = $school_attended->school_name;
				}
			}
			// Append 'new school' option to end of dropdown
			$hs_attended['Add another school:'] = array( 'new' => 'find another school...' );
			$colleges_attended['Add another school:'] = array( 'new' => 'find another school...' );

			$schools_attended = array();
			$schools_attended['high_schools'] = $hs_attended;
			$schools_attended['colleges'] = $colleges_attended;
			// This array is added below, under the data['user'] array!

			$currentSchool = '';

			// Get Dropdown Data
			// - Countries
			$countries_raw = Country::all()->toArray();
			$countries = array();
			foreach( $countries_raw as $country ){
				$countries[ $country[ 'id' ] ] = $country[ 'country_name' ];
			}
			$countries = array( '' => 'Select a country' ) + $countries;

			// - Ethnicities
			$ethnicities_raw = Ethnicity::all()->toArray();
			$ethnicities = array();
			foreach( $ethnicities_raw as $ethnicity ){
				$ethnicities[ $ethnicity[ 'id' ] ] = $ethnicity[ 'ethnicity' ];
			}
			$ethnicities = array( '' => 'Select an ethnicity' ) + $ethnicities;

			// - Religions
			$religions_raw = Religion::all()->toArray();
			$religions = array();
			foreach( $religions_raw as $religion ){
				$religions[ $religion[ 'id' ] ] = $religion[ 'religion' ];
			}
			$religions = array( '' => 'Select a religion' ) + $religions;

			// - Military Affiliations
			$military_affiliation_raw = MilitaryAffiliation::all()->toArray();
			$military_affiliation_arr = array();
			foreach( $military_affiliation_raw as $mar ){
				$military_affiliation_arr[ $mar[ 'id' ] ] = $mar[ 'name' ];
			}
			$military_affiliation_arr = array( '' => 'Select your military affiliation' ) + $military_affiliation_arr;


			//Get the school name depending on the current school id and if in college or not from users table.
			if ($user->in_college) {
				$gradyear = $user->college_grad_year;
				$currentSchool = DB::table('colleges')->select('school_name')->where( 'id', '=', $user->current_school_id )->first();

				$user->current_school_name = "";
				if ( isset($currentSchool) ) {
					$user->current_school_name = $currentSchool->school_name;
				}

			}else{
				$gradyear = $user->hs_grad_year;
				$currentSchool = DB::table('high_schools')->select('school_name')->where( 'id', '=', $user->current_school_id )->first();
				/*
				echo "<pre>";
				var_dump( $currentSchool );
				exit;
				 */

				$user->current_school_name = "";
				if( isset($currentSchool) ){
					$user->current_school_name = $currentSchool->school_name;
				}
			}

			$is_military_id = $user->is_military;
			$is_military_name = ($user->is_military == 0) ? 'No' : 'Yes';

			if (isset($user->military_affiliation)) {
				$military_affiliation_name = MilitaryAffiliation::find($user->military_affiliation);
				$military_affiliation_name = $military_affiliation_name->name;
				$military_affiliation_id   = $user->military_affiliation;
			}else{
				$military_affiliation_name = null;
				$military_affiliation_id   = null;
			}

			$data['token'] = $token;
			$data['ajaxtoken'] = $token;

			$birthdayM="";$birthdayD="";$birthdayY="";
			if($user->birth_date!="" && $user->birth_date!="0000-00-00"){
				$splitDb=explode("-",$user->birth_date);
				if(count($splitDb)>0){
					$birthdayM=$splitDb[1];
					$birthdayD=$splitDb[2];
					$birthdayY=$splitDb[0];
				}
			}

			$src = "/images/profile/default.png";

			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}

			//Set Children for form
			if($user->children){
				$children="Yes";
			}else{
				$children="No";
			}

			//Set MartialStatus for form
			if($user->married){
				$maritalStatus="Married";
			}else{
				$maritalStatus="Single";
			}

			$edu_level = $user->in_college ? 'college' : 'high_school';
			$edu_level_text = $user->in_college ? 'College' : 'High School';

			$data = array_add( $data, 'user', array(
				'id' => $user->id,
				'fname' => $user->fname,
				'lname' => $user->lname,
				'email' => $user->email,
				'country' => $user->country,
				'country_id' => $user->country_id,
				'city' => $user->city,
				'state' => $user->state,
				'zip' => $user->zip,
				'gender' => $user->gender,
				'birth_date' => $user->birth_date,
				'birthdayM' => $birthdayM,
				'birthdayD' => $birthdayD,
				'birthdayY' => $birthdayY,
				'phone' => $user->phone,
				'address' => $user->address,
				'children' => $children,
				'currentSchool' => $user->current_school_id,
				'currentSchoolVal' =>$user->current_school_name,
				'maritalStatus' => $maritalStatus,
				'religion' => $user->religion,
				'religionVal' => $user->religionVal,
				'ethnicity' => $user->ethnicity,
				'ethnicityVal' => $user->ethnicityVal,
				'profile_img_loc' => $src,
				'grad_Year' => $gradyear,
				'edu_level' =>$edu_level,
				'edu_level_text' => $edu_level_text,
				'in_college' => $user->in_college,
				'schools_attended' => $schools_attended,
				'is_military_name' => $is_military_name,
				'is_military_id' => $is_military_id,
				'military_affiliation_name' => $military_affiliation_name,
				'military_affiliation_id'  => $military_affiliation_id,
				'skype' => $user->skype_id,
				'planned_start_term' => $user->planned_start_term,
				'planned_start_yr' => $user->planned_start_yr
			));
			// Put the Dropdown Data in the $data dawg!
			$data['countries'] = $countries;
			$data['ethnicities'] = $ethnicities;
			$data['religions'] = $religions;
			$data['military_affiliation_arr'] = $military_affiliation_arr;

			// echo "<pre>";
			// print_r($data);
			// echo "</pre>";
			// exit();

			return View( 'private.profile.ajax.personalInfo', $data);
		}
	}


	public function personalInfoPhoto( $token = null ){

		$input = Request::all();

		if (isset($input['targetted_user_id'])) {
			$data = array();
			$data['user_id'] = $input['targetted_user_id'];
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
		}

		$id = $data['user_id'];
        //check if token is good.
		// if ( !$this->checkToken( $token ) ) {
		// 	return 'Invalid Token';
		// }

		$input = Request::all();
		// Create message arrays
		$error_alert = array(
			'img' => '/images/topAlert/urgent.png',
			'bkg' => '#de4728',
			'textColor' => '#fff',
			'dur' => '7000'
		);
		$success_alert = array(
			'img' => '/images/topAlert/checkmark.png',
			'bkg' => '#a0db39',
			'textColor' => '#fff',
			'dur' => '5000'
		);

		// Validation rules
		$rules = array(
			'remove' => array(
				'regex:/^1$/'
			),
			'profile_picture' => array(
				'mimes:jpeg,png,gif'
			)
		);

		// Validate
		$validator = Validator::make( $input, $rules );
		if( $validator->fails() ){
			$error_alert['msg'] = 'An image of type: jpeg, png, or gif is required.';
			return json_encode( $error_alert );
		}

		// find User
		$user = User::find($id);

		// Control Structure: remove is set to 1 or else would fail validation
		if( isset( $input['remove'] ) ){
			$image_name = $user->profile_img_loc;
			// Remove from S3 Bucket
            $s3 = AWS::createClient('s3');
				$bucket = 'asset.plexuss.com';
				$keyname = 'users/images/' . $image_name;

			$delete = $s3->deleteObject(array(
				'Bucket' => $bucket,
				'Key'    => $keyname
			));

			// Unset image name on DB
			if( $delete ){
				$user->profile_img_loc = null;
				$user->save();

				// Return topAlert message
				$success_alert['msg'] = 'Profile picture removed!';
				return json_encode( $success_alert );
			}

			// Else return error
			$error_alert['msg'] = 'There was a problem removing that image.';
			return json_encode( $error_alert );
		}

		// Add or update a photo
		else if( isset( $input['profile_picture'] ) ){

			// Remove current image
			if( !is_null( $user->profile_img_loc ) && !empty($user->profile_img_loc) ){

				$image_name = $user->profile_img_loc;
				// Remove from S3 Bucket
				// $s3 = AWS::get('s3');
                $s3 = AWS::createClient('s3');

					$bucket = 'asset.plexuss.com';
					$keyname = 'users/images/' . $image_name;

				$delete = $s3->deleteObject(array(
					'Bucket' => $bucket,
					'Key'    => $keyname
				));

				if( !$delete ){
					// Else return error
					$error_alert['msg'] = 'There was a problem removing that image.';
					return json_encode( $error_alert );
				}
			}

			// add photo or update
			$Obj = new User();

			$profile_image_name = $Obj->ProcessResize( 'profile_picture', $user->id );

			// echo '<pre>';
			// print_r($profile_image_name);
			// echo '</pre>';
			// exit();

			// Get extension to set AWS's 'content type' field in s3
			$exploded = explode( '.', $profile_image_name );
			$extension = $exploded[ count( $exploded ) -1 ];
			// return 'jpg'

			switch( $extension ){
				case 'jpg':
				case 'jpeg':
					$content_type = 'image/jpeg';
					break;
				case 'png':
					$content_type = 'image/png';
					break;
				case 'gif':
					$content_type = 'image/gif';
					break;
			}

			// Halt upload process
			if( !isset( $content_type ) ){
				// Delete temp image
				unlink('dropzone/images/' . $profile_image_name);
				// Return topAlert object
				$error_alert['msg'] = 'The uploaded image must be in jpeg, png, or gif format.';
				return json_encode( $error_alert );
			}

			$Pic_Src = $Obj->get_profile_image_path();

			// echo '<pre>';
			// print_r('pic name: '.$profile_image_name.'<br>');
			// print_r($content_type.'<br>');
			// print_r($Pic_Src);
			// echo '</pre>';
			// exit();

			// echo '<pre>';
			// print_r($Pic_Src.'\\'.$profile_image_name);
			// echo '</pre>';
			// exit();

			// Upload to AWS bucket
            $aws = AWS::createClient('s3');

			$aws->putObject(array(
				'ACL'         => 'public-read',
				'Bucket'      => 'asset.plexuss.com',
				'Key'         => 'users/images/' . $profile_image_name,
				'ContentType' => $content_type,
				'SourceFile'  => $Obj->get_profile_image_path() .'\\'. $profile_image_name
			));

			// Delete temp image
			unlink( $Obj->get_profile_image_path() .'\\'. $profile_image_name );
			// Update user image in DB
			$user->profile_img_loc = $profile_image_name;

			// echo '<pre>';
			// print_r($user->profile_img_loc);
			// echo '</pre>';
			// exit();

			$user->save();

			$success_alert['msg'] = 'Profile picture updated!';

			// echo '<pre>';
			// print_r($success_alert['msg']);
			// echo '</pre>';
			// exit();

			Session::put('userinfo.session_reset', 1);

			return json_encode( $success_alert );
		}
	}


	public function postPersonalInfo( $token = null ) {
		if (Auth::check()){

		    //get id by authcheck
	        $id = Auth::id();

	        //check if token is good.
	  		//  if ( !$this->checkToken( $token ) ){
	  		//           return 'Invalid Token';
			// }

			$input = Request::all();
			// dd($input);
			$user = User::find($id);

			if( isset($input['mode']) && $input['mode']=="photo"){

				if(@$_FILES['profilepic']['name']!=""){

					$Obj = new User;
					$PhotoName = $Obj->ProcessResize('profilepic', $user->id);
					echo "photo name: " . $PhotoName;

					$aws = AWS::get('s3');
					$aws->putObject(array(
						'ACL'        => 'public-read',
						'Bucket'     => 'asset.plexuss.com/users/images',
						'Key'        => $PhotoName,
						'SourceFile' => '../public/dropzone/images/'.$PhotoName,
					));

					unlink('../public/dropzone/images/'.$PhotoName);
					$user->profile_img_loc =$PhotoName;
					$user->save();

					return "User Photo Updated Successfully.";

				}else if($input['photoPath']!=""){

					$aws = AWS::get('s3');

					$aws->putObject(array(
					'ACL'	=> 'public-read',
					'Bucket'     => 'asset.plexuss.com/users/images',
					'Key'        =>$input['photoPath'],
					'SourceFile' =>'../public/dropzone/images/'.$input['photoPath'],
					));
					unlink('../public/dropzone/images/'.$input['photoPath']);
					$user->profile_img_loc=$input['photoPath'];
					$user->save();

					return "User Photo Updated Successfully.";

				}else{
					return "Please choose a photo to upload.";
				}

			}else{

				$filter = array(
					'SchoolId' => 'numeric',
					'infoFName' => array(
						'required',
						'regex:/^([a-zA-Z\-a-zA-Z\-\.\' ])+$/'
					),
					'infoLName' => array(
						'required',
						'regex:/^([a-zA-Z\-a-zA-Z\-\.\' ])+$/'
					),
					'infoCountry' => 'numeric',
					'infoAddress' =>'regex:/^([,-a-zA-Z0-9_\.,#\- ])+$/i',
					'infoState' =>'regex:/^([,-a-zA-Z0-9_ ])+$/i',
					'infoCity' =>'regex:/^([,-a-zA-Z0-9_ ])+$/i',
					'infoZip' =>'regex:/^[a-zA-Z0-9\.,\- ]+$/',
					'infoEmail' =>'required|email',
					'infoSkype' => 'nullable|regex: /^[a-z][a-z0-9\.,\-_]{5,31}$/i',
					'infoPhoneNumber' =>'regex:/^([0-9\-\+\(\) ])+$/',
					'infoBirthDateM' =>'numeric',
					'infoBirthDateD' =>'numeric',
					'infoBirthDateY' =>'numeric',
					'infoReligion' =>'numeric',
					'infoGender' =>'alpha',
					'infoMaritalStatus' =>'alpha',
					'infoEthnicity' =>'nullable|numeric',
					'infoChildren' =>'alpha',
					'edu_level' => array(
						'regex:/^(college|high_school)$/'
					),
					'hs_attended' => array(
						'required_without:colleges_attended',
						'alpha_num'
					),
					'colleges_attended' => array(
						'required_without:hs_attended',
						'alpha_num'
					),
					'new_school' => 'nullable|regex:/^([\p{L}\p{M}\.\(\),\-\'"!@#& ])+$/u',

					'infoGradYear' =>array(
						'regex:/^([-a-zA-Z0-9_ ])+$/i'
					),
					'infoStartTerm' =>array(
						'regex:/^(Fall|Winter|Spring|Summer)[\h][0-9]{4}$/i'
					),
					'infoMilitaryAffiliation' => 'nullable|numeric',
					'infoInMilitary' => 'numeric',

				);

				$validator = Validator::make( $input, $filter );
				if ($validator->fails()){
					$messages = $validator->messages();
					return $messages;
					exit();
				}

				//Set Children
				$children = $input['infoChildren'] == 'Yes' ? 1 : 0;

				//Set MartialStatus for form
				$maritalStatus = $input['infoMaritalStatus'] == 'Single' ? 0 : 1;

				// Set grad year
				$grad_year = $input['edu_level'] == 'college' ? 'college_grad_year' : 'hs_grad_year';
				$in_college = $input['edu_level'] == 'college' ? 1 : 0;

				//Set intended start term -- needs to be seperated into term and year for DB
				$intended_start = explode(' ', $input['infoStartTerm']);
				$start_term = $intended_start[0];
				$start_year = $intended_start[1];

				// Update DB
				$hs_attended = $input['hs_attended'];
				$colleges_attended = $input['colleges_attended'];
				$add_school = $input['new_school'];

				// IF THE USER IS ADDING A NEW CUSTOM SCHOOL
				if( ( $hs_attended == 'new' || $colleges_attended == 'new' ) && $input['SchoolId'] == '' ){
					// Add new school in DB
					$new_school = $in_college == 1 ? new College : new Highschool;
					$new_school->school_name = Request::get( 'new_school' );
					$new_school->verified = 0;
					$new_school->user_id_submitted = $user->id;
					$new_school->save();

					// Add new education (table used to track which schools user had attended)
					$education = new Education( array(
						'school_id' => $new_school->id,
						'school_type' => $in_college == 1 ? 'college' : 'highschool'
					));
					$user->educations()->save( $education );

					// Set user's current school id
					$user->current_school_id = $new_school->id;
				}

				// IF THE USER IS SELECTING A NEW SCHOOL FROM AUTOCOMPLETE
				else if( ($hs_attended == 'new' || $colleges_attended == 'new') && $input['SchoolId'] != '' ){
					// Set user's current school id
					$user->current_school_id = $input['SchoolId'];

					// Add school to user's educations dropdown
					$education = new Education( array(
						'school_id' => $user->current_school_id,
						'school_type' => $in_college == 1 ? 'college' : 'highschool'
					));
					$user->educations()->save( $education );
				}

				// IF THE USER IS SWITCHING TO A SCHOOL ON HIS/HER DROPDOWN
				else{
					// Set user's current school id
					$user->current_school_id = $in_college ? $colleges_attended : $hs_attended;
				}

				$infoMilitaryAffiliation = (!isset($input['infoMilitaryAffiliation']) || $input['infoMilitaryAffiliation'] == 0
											 || $input['infoMilitaryAffiliation'] == '') ? NULL : $input['infoMilitaryAffiliation'];
				// Update profile personal info
				$user->fname = $input['infoFName'];
				$user->lname = $input['infoLName'];
				$user->email = $input['infoEmail'];
				if( isset($input['infoSkype']) && !empty($input['infoSkype']) ){
					$user->skype_id = $input['infoSkype'];
				}
				$user->country_id = $input[ 'infoCountry' ];
				$user->address = $input['infoAddress'];
				$user->city = $input['infoCity'];
				$user->state = $input['infoState'];
				$user->zip = $input['infoZip'];
				$user->phone = $input['infoPhoneNumber'];
				$user->gender = $input['infoGender'];
				$user->birth_date = $input['infoBirthDateY']."-".$input['infoBirthDateM']."-".$input['infoBirthDateD'];
				$user->children = $children;
				$user->married = $maritalStatus;
				$user->religion = $input['infoReligion'];
				$user->ethnicity = $input['infoEthnicity'];
				$user->in_college = $in_college;
				$user->$grad_year = $input['infoGradYear'];
				$user->planned_start_term = $start_term;
				$user->planned_start_yr = $start_year;
				$user->is_military = (!isset($input['infoInMilitary'])) ? 0 : $input['infoInMilitary'];
				$user->military_affiliation = $infoMilitaryAffiliation;
				//$user->profile_status='1';
				$user->save();
				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "User Info Updated.";
			}
		}
	}

	public function saveMeTab($input = []){
        if (empty($input)) {
    		$viewDataController = new ViewDataController();
    		$data = $viewDataController->buildData();
    		$input = Request::all();

        } else {
            try {
                $data = ['user_id' => Crypt::decrypt($input['hashed_user_id'])];
            } catch (\Exception $e) {
                $ret = [];
                $ret['status'] = 'failed';

                return $ret;
            }
        }

        $user = User::find($data['user_id']);

		isset($input['fname']) ? $user->fname = ucwords(strtolower($input['fname'])) : NULL;
		isset($input['lname']) ? $user->lname = ucwords(strtolower($input['lname'])) : NULL;

		if (isset($input['edu_level']) && $input['edu_level'] == 'college') {
			$user->in_college = 1;
		}elseif (isset($input['edu_level']) && $input['edu_level'] == 'hs') {
			$user->in_college = 0;
		}

        if (isset($input['user_type'])) {
            $user->is_alumni = 0;
            $user->is_student = 0;
            $user->is_parent = 0;
            $user->is_counselor = 0;
            $user->is_university_rep = 0;

            $user['is_'.$input['user_type']] = 1;
        }

		if (isset($input['currentSchoolName']) && $input['currentSchoolName'] == "Home Schooled") {
			$user->current_school_id = 35829;
		}elseif(isset($input['currentSchoolName'])){

			$school = ($user->in_college == 1) ? College::where('school_name', $input['currentSchoolName'])->first() : Highschool::where('school_name', $input['currentSchoolName'])->first();

			if (isset($school) && !empty($school)) {
				$user->current_school_id = $school->id;
			}else{
				$newSchool = ($user->in_college == 1) ?  new College : new Highschool;
				$newSchool->school_name = $input['currentSchoolName'];
				$newSchool->verified = 0;
				$newSchool->user_id_submitted = $user->id;
				$newSchool->save();
				$user->current_school_id = $newSchool->id;
			}

		}

		if ($user->in_college == 0 && isset($input['gradYear'])) {
			$user->hs_grad_year = $input['gradYear'];
		}elseif ($user->in_college == 1 && isset($input['gradYear'])) {
			$user->college_grad_year = $input['gradYear'];
		}

		isset($input['userCity'])  ? $user->city  = $input['userCity'] : NULL;
		isset($input['userState']) ? $user->state = $input['userState'] : NULL;
		if (isset($input['country_name'])) {
			$c = Country::where('country_name', $input['country_name'])->first();

			isset($c) ? $user->country_id = $c->id : NULL;
		}

		isset($input['planned_start_term']) ? $user->planned_start_term = $input['planned_start_term'] : NULL;
		isset($input['planned_start_yr']) 	? $user->planned_start_yr 	= $input['planned_start_yr']   : NULL;

		$user->save();

		if (isset($input['profession_name'])) {
			$profession = Profession::on('rds1')->where('profession_name', $input['profession_name'])->first();

			if (isset($profession) && !empty($profession)) {
				$profession_id = $profession->id;
			}else{
				$profession = new Profession;
				$profession->profession_name = $input['profession_name'];
				$profession->save();

				$profession_id = $profession->id;
			}
		}
		if (isset($input['majors']) && !empty($input['majors'])) {

			$obj = DB::table('objectives')->where('user_id', $data['user_id'])->get();

			//if there are currently saved objectives, remove them before adding new ones
			if( count($obj) > 0 ){
				foreach ($obj as $key) {
					$removeObjective = Objective::find($key->id);
					(!isset($obj_text)) 		   ? $obj_text 			  = $removeObjective->obj_text : null;
					(!isset($university_location)) ? $university_location = $removeObjective->university_location : null;
					$removeObjective->delete();
				}
			}

			for( $i = 0; $i < count($input['majors']); $i++ ){
				$obj = new Objective;
				$obj->user_id = $data['user_id'];
				isset($input['degree_id'])  ? $obj->degree_type = $input['degree_id'] : NULL;
				isset($input['majors'][$i]['id']) ? $obj->major_id = $input['majors'][$i]['id'] : NULL;
				isset($profession_id) 		? $obj->profession_id = $profession_id : NULL;
				isset($obj_text) 			? $obj->obj_text = $obj_text : NULL;
				isset($university_location) ? $obj->university_location = $university_location : NULL;

				$obj->save();
			}
		}
		//Occupation saving
		if (isset($input['occupation_name'])) {
			//check for profession
			$profession = Profession::on('rds1')->where('profession_name', $input['occupation_name'])->first();

			if (isset($profession) && !empty($profession)) {
				$profession_id = $profession->id;
			}else{
				$profession = new Profession;
				$profession->profession_name = $input['occupation_name'];
				$profession->save();

				$profession_id = $profession->id;
			}

			//save occupation
			$attr = array('user_id' => $data['user_id']);
			$val = array('user_id' => $data['user_id'], 'profession_id' => $profession_id);
			Occupation::updateOrCreate($attr, $val);
		}


		$this->CalcIndicatorPercent($data['user_id']);
		$this->CalcProfilePercent($data['user_id']);
		$this->CalcOneAppPercent($data['user_id']);

		Session::put('userinfo.session_reset', 1);

		$ret = array();
		$ret['status'] = 'success';

		return json_encode($ret);
	}

	public function getUserTranscript(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$trc = new Transcript;

		$ret = $trc->getUsersTranscriptFormatted($data['user_id']);

		return json_encode($ret);
	}

	public function getObjective( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			//should get all rows with user_id and distinct major_id
			$objectiveData=DB::table('objectives')
			->leftJoin('professions', 'objectives.profession_id', '=', 'professions.id')
			->leftJoin('majors', 'objectives.major_id', '=', 'majors.id')
			->leftJoin('degree_type', 'objectives.degree_type', '=', 'degree_type.id')
			->select('objectives.*', 'professions.profession_name as profession','majors.name as major','degree_type.display_name as degreename')
			->where('objectives.user_id',$user->id)->groupBy('objectives.major_id')->get();

			if(!isset($objectiveData[0]) || empty($objectiveData)){
			$data = array_add( $data, 'user', array(
					'id' => $user->id,
					'fname' => $user->fname,
					'lname' => $user->lname,
				) );
			}
			else
			{
					$majors = [];
					foreach($objectiveData as $i){
						array_push($majors, $i->major);
					}

					$data = array_add( $data, 'user', array(
					'id' => $user->id,
					'fname' => $user->fname,
					'lname' => $user->lname,
					'objId' => $objectiveData[0]->id,
					'degree_type' => $objectiveData[0]->degree_type,
					'degreename' => $objectiveData[0]->degreename,
					'major' => $majors,
					'profession' => $objectiveData[0]->profession,
					'whocansee' => $objectiveData[0]->whocansee,
					'aoc_post' => $objectiveData[0]->aoc_post,
					'obj_text' => $objectiveData[0]->obj_text
				) );
			}

			// Build the form drop down lists.

			return View( 'private.profile.ajax.objective', $data);
		}
	}


	public function postObjective( $token = null ) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		// $filter = array(
  //           'whocansee' =>'required|regex:/^([-a-z0-9_ ])+$/i',
		// 	'objDegree' =>'required|regex:/^([-a-z0-9_ ])+$/i',
		// 	'objMajor' => array(
		// 		'required',
		// 		'regex:/^([a-zA-Z0-9\.\-\/\(\)\&\ ])+$/'
		// 	),
		// 	'objProfession' => array(
		// 		'required',
		// 		'regex:/^([a-zA-Z0-9\.\-\/\(\)\&\ ])+$/'
		// 	)
  //       );

		// $validator = Validator::make($input,$filter);

		// if ($validator->fails()){
		// 	$messages = $validator->messages();
		// 	return $messages;
  //           //return 'There was an error with the form';
  //       }

		if(isset($input['majors']) && !empty($input['majors'])){

			$major_arr = DB::connection('rds1')
					   	   ->table('majors')
						   ->whereIn('name', $input['majors'])
						   ->pluck('id');

			//Lookup major and profession id by input objMajor and objProfession string
			$profession = Profession::on('rds1')->where('profession_name', '=', $input['formInput']['objProfession'])->first();
			$profession_id = $profession->id;

			$affectedRows = Objective::where('user_id', $data['user_id'])->delete();

			foreach ($major_arr as $key => $value) {
				$obj = new Objective;
				$obj->user_id   = $data['user_id'];
				$obj->whocansee = isset($input['formInput']['whocansee']) ? $input['formInput']['whocansee'] : NULL;
				$obj->degree_type = isset($input['formInput']['objDegree']) ? $input['formInput']['objDegree'] : NULL;
				$obj->major_id = $value;
				$obj->profession_id = $profession_id;
				$obj->obj_text = isset($input['formInput']['objPersonalObj']) ? $input['formInput']['objPersonalObj'] : NULL;

				$obj->save();
			}

			$this->CalcIndicatorPercent();
			$this->CalcProfilePercent();
			$this->CalcOneAppPercent();

			return "Objective Info Added Successfully.";
		}

		return "failed";
	}


	public function getScores( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			$data['OtherScoreData']=array();
			$scoreData = DB::table('scores')->where('user_id',$user->id)->first();


			if ($scoreData) {
				//take the saved Json code in the DB and convert it back to an array.
				$scoreData->other_values = json_decode($scoreData->other_values);

				//Setup GED pass or fail varaibles.
				if ($scoreData->gedfp === 'Pass') {
					$scoreData->gedfpPassStatus = 'active';
					$scoreData->gedfpFailStatus ='';
				} else if ($scoreData->gedfp === 'Fail'){
					$scoreData->gedfpPassStatus = '';
					$scoreData->gedfpFailStatus = 'active';
				} else {
					$scoreData->gedfpPassStatus = '';
					$scoreData->gedfpFailStatus = '';
				}
			} else {
				$scoreData = (object)[];
			}

			//Save the scoreData to hsscore
			$data['hsScore'] = $scoreData;

			$data = array_add( $data, 'user', array(
				'id' => $user->id,
				'fname' => $user->fname,
				'lname' => $user->lname,
			));

			return View( 'private.profile.ajax.scores', $data);
		}
	}


	public function postScores( $token = null ) {


		if (Auth::check()) {
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        /*
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			*/
			//echo "<pre>";
			//print_r($_REQUEST);
			//exit();

			$input = Request::all();

			// set all empty strings to null
			//This is needed for validation.
			//If you leave it as blank the DB will save a 0 and will cause issues in the forms.
			foreach($input as $key => $value){
				if($value == ""){
					$input[$key] = null;
				}
			}

			// We still need to save if the exams are post 2016 for SAT and PSAT
			!isset($input['is_pre_2016_psat']) ? $input['is_pre_2016_psat'] = 0 : NULL;
			!isset($input['is_pre_2016_sat']) ? $input['is_pre_2016_sat'] = 0 : NULL;

			$filter = array(
				'hs_gpa' =>'numeric|nullable',
				'weighted_gpa' =>'numeric|nullable',
				'max_weighted_gpa' => 'numeric|nullable',
				'act_english' =>'numeric|nullable',
				'act_math' =>'numeric|nullable',
				'act_composite' =>'numeric|nullable',
				'is_pre_2016_psat' => 'numeric|nullable',
				'psat_reading' =>'numeric|nullable',
				'psat_math' =>'numeric|nullable',
				'psat_writing' =>'numeric|nullable',
				'psat_reading_writing' => 'numeric|nullable',
				'psat_total' =>'numeric|nullable',
				'is_pre_2016_sat' => 'numeric|nullable',
				'sat_reading' =>'numeric|nullable',
				'sat_math' =>'numeric|nullable',
				'sat_writing' =>'numeric|nullable',
				'sat_reading_writing' => 'numeric|nullable',
				'sat_total' =>'numeric|nullable',
				'gedfp' =>'alpha|nullable',
				'ged_score' =>'numeric|nullable',
				'ap_overall' => 'numeric|nullable',
				'lsat_total' => 'numeric|nullable',
				'gmat_total' => 'numeric|nullable',
				'pte_total' => 'numeric|nullable',
				'gre_verbal' => 'numeric|nullable',
				'gre_quantitative' => 'numeric|nullable',
				'gre_analytical' => 'numeric|nullable',
				'othervalueScore' => 'array',
				'toefl_total' => 'numeric|nullable',
				'toefl_reading' => 'numeric|nullable',
				'toefl_listening' => 'numeric|nullable',
				'toefl_speaking' => 'numeric|nullable',
				'toefl_writing' => 'numeric|nullable',
				'toefl_ibt_total' => 'numeric|nullable',
				'toefl_ibt_reading' => 'numeric|nullable',
				'toefl_ibt_listening' => 'numeric|nullable',
				'toefl_ibt_speaking' => 'numeric|nullable',
				'toefl_ibt_writing' => 'numeric|nullable',
				'toefl_pbt_reading' => 'numeric|nullable',
				'toefl_pbt_listening' => 'numeric|nullable',
				'toefl_pbt_written' => 'numeric|nullable',
				'toefl_pbt_total' => 'numeric|nullable',
				'ielts_total' => 'numeric|nullable',
				'ielts_reading' => 'numeric|nullable',
				'ielts_listening' => 'numeric|nullable',
				'ielts_speaking' => 'numeric|nullable',
				'ielts_writing' => 'numeric|nullable',
	        );

			$validator = Validator::make($input,$filter);

			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;
				exit();
	            //return 'There was an error with the form';
	        }

			//Look up user data by id.
			$user = User::find($id);
			$allreadyHasScore = DB::table('scores')->where('user_id',$user->id)->first();


			if(isset( $input['othervalueScore']) ) {
				$OtherData = json_encode($input['othervalueScore']);
			} else {
				$OtherData = '';
			}

			if($input['postType'] == 'college') {

				$overall_gpa = $input['overall_gpa'];
			}


			//Check if this is a insert or upate.
			if( !$allreadyHasScore ) {
				//if this is a highschool insert.
				if($input['postType'] == 'highschool') {
					$ObjId =DB::table('scores')->insert(array(
					'user_id' =>$id,
					//'whocansee' => $input['whocansee'],
					'hs_gpa' =>$input['hs_gpa'],
					'weighted_gpa' => $input['weighted_gpa'],
					'max_weighted_gpa' => $input['max_weighted_gpa'],
					'act_english' => $input['act_english'],
					'act_math' => $input['act_math'],
					'act_composite' => $input['act_composite'],
					'is_pre_2016_psat' => $input['is_pre_2016_psat'],
					'psat_reading_writing' => $input['psat_reading_writing'],
					'is_pre_2016_sat' => $input['is_pre_2016_sat'],
					'sat_reading_writing' => $input['sat_reading_writing'],
					'psat_reading' => $input['psat_reading'],
					'psat_math' => $input['psat_math'],
					'psat_writing' => $input['psat_writing'],
					'psat_total' => $input['psat_total'],
					'sat_reading' => $input['sat_reading'],
					'sat_math' => $input['sat_math'],
					'sat_writing' => $input['sat_writing'],
					'sat_total' => $input['sat_total'],
					'gedfp' => $input['gedfp'],
					'ged_score' => $input['ged_score'],
					'ap_overall' => $input['ap_overall'],
					'lsat_total' => $input['lsat_total'],
					'gmat_total' => $input['gmat_total'],
					'pte_total' => $input['pte_total'],
					'gre_verbal' => $input['gre_verbal'],
					'gre_quantitative' => $input['gre_quantitative'],
					'gre_analytical' => $input['gre_analytical'],
					'updated_at' =>date('Y-m-d H:i:s'),
					'toefl_total' => $input['toefl_total'],
					'toefl_reading' => $input['toefl_reading'],
					'toefl_listening' => $input['toefl_listening'],
					'toefl_speaking' => $input['toefl_speaking'],
					'toefl_writing' => $input['toefl_writing'],
					'toefl_ibt_total' => $input['toefl_ibt_total'],
					'toefl_ibt_reading' => $input['toefl_ibt_reading'],
					'toefl_ibt_listening' => $input['toefl_ibt_listening'],
					'toefl_ibt_speaking' => $input['toefl_ibt_speaking'],
					'toefl_ibt_writing' => $input['toefl_ibt_writing'],
					'toefl_pbt_reading' => $input['toefl_pbt_reading'],
					'toefl_pbt_listening' => $input['toefl_pbt_listening'],
					'toefl_pbt_written' => $input['toefl_pbt_written'],
					'toefl_pbt_total' => $input['toefl_pbt_total'],
					'ielts_total' => $input['ielts_total'],
					'ielts_reading' => $input['ielts_reading'],
					'ielts_listening' => $input['ielts_listening'],
					'ielts_speaking' => $input['ielts_speaking'],
					'ielts_writing' => $input['ielts_writing']

					));
				} else {
					//if this is a college insert.
					$ObjId =DB::table('scores')->insert(array(
					'user_id' =>$id,
					//'whocansee' => $input['whocansee'],
					'overall_gpa' =>$overall_gpa,
					'updated_at' =>date('Y-m-d H:i:s')
					));
				}

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();

				return "Score Data Updates Successfully.";

			} else {

				if($input['postType'] == 'highschool') {
					$ObjId =DB::table('scores')
					->where('user_id',$id)
					->update(array(
					//'whocansee' => $input['whocansee'],
					'hs_gpa' =>$input['hs_gpa'],
					'weighted_gpa' => $input['weighted_gpa'],
					'max_weighted_gpa' => $input['max_weighted_gpa'],
					'act_english' => $input['act_english'],
					'act_math' => $input['act_math'],
					'act_composite' => $input['act_composite'],
					'is_pre_2016_psat' => $input['is_pre_2016_psat'],
					'psat_reading_writing' => $input['psat_reading_writing'],
					'is_pre_2016_sat' => $input['is_pre_2016_sat'],
					'sat_reading_writing' => $input['sat_reading_writing'],
					'psat_reading' => $input['psat_reading'],
					'psat_math' => $input['psat_math'],
					'psat_writing' => $input['psat_writing'],
					'psat_total' => $input['psat_total'],
					'sat_reading' => $input['sat_reading'],
					'sat_math' => $input['sat_math'],
					'sat_writing' => $input['sat_writing'],
					'sat_total' => $input['sat_total'],
					'gedfp' =>$input['gedfp'],
					'ged_score' => $input['ged_score'],
					'ap_overall' => $input['ap_overall'],
					'lsat_total' => $input['lsat_total'],
					'gmat_total' => $input['gmat_total'],
					'pte_total' => $input['pte_total'],
					'gre_verbal' => $input['gre_verbal'],
					'gre_quantitative' => $input['gre_quantitative'],
					'gre_analytical' => $input['gre_analytical'],
					'other_values' =>$OtherData,
					'updated_at' =>date('Y-m-d H:i:s'),
					'updated_at' =>date('Y-m-d H:i:s'),
					'toefl_total' => $input['toefl_total'],
					'toefl_reading' => $input['toefl_reading'],
					'toefl_listening' => $input['toefl_listening'],
					'toefl_speaking' => $input['toefl_speaking'],
					'toefl_writing' => $input['toefl_writing'],
					'toefl_ibt_total' => $input['toefl_ibt_total'],
					'toefl_ibt_reading' => $input['toefl_ibt_reading'],
					'toefl_ibt_listening' => $input['toefl_ibt_listening'],
					'toefl_ibt_speaking' => $input['toefl_ibt_speaking'],
					'toefl_ibt_writing' => $input['toefl_ibt_writing'],
					'toefl_pbt_reading' => $input['toefl_pbt_reading'],
					'toefl_pbt_listening' => $input['toefl_pbt_listening'],
					'toefl_pbt_written' => $input['toefl_pbt_written'],
					'toefl_pbt_total' => $input['toefl_pbt_total'],
					'ielts_total' => $input['ielts_total'],
					'ielts_reading' => $input['ielts_reading'],
					'ielts_listening' => $input['ielts_listening'],
					'ielts_speaking' => $input['ielts_speaking'],
					'ielts_writing' => $input['ielts_writing']
					));

				} else {

					$ObjId =DB::table('scores')
					->where('user_id',$id)
					->update(array(
					//'whocansee' => $input['whocansee'],
					'overall_gpa' =>$overall_gpa,
					'updated_at' =>date('Y-m-d H:i:s')
					));
				}

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();

				return "Score Data Updates Successfully";
			}
		}
	}




	/* Upload Center - start */
	public function getUploadCenter( $token = null ){

		if( Auth::check() ){
			$id = Auth::id();

			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$data['ajaxtoken'] = $token;

			//get users transcript info, if any
			$data['transcript_data'] = array();

			$transcriptData = DB::table('transcript')
				->where('user_id',$user->id)->where('school_type','highschool')
				->get();

			$data['transcript_data'] = $transcriptData;


			// include countries
			$data['user_country_id']  = $user->country_id;
			$data['prof_intl_country_chng'] = $user->prof_intl_country_chng;

			return View( 'private.profile.ajax.uploadcenter', $data);
		}

	}//end of getUploadcenter()

	public function postUploadCenter( $token = null ){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		$is_plexuss = false;

		if (isset($input['hashed_user_id'])) {
			try {
				$data['user_id'] = Crypt::decrypt($input['hashed_user_id']);
				$is_plexuss = true;
			} catch (\Exception $e) {
				return json_encode( array( 'msg' => "Invalid user id."  ) );
			}

		}

		//check if token is good.
		// if ( !$this->checkToken( $token ) ) {
		// 	return 'Invalid Token';
		// }

		if ($input['postType'] == 'transcriptremove'){
			// Get transcript from DB
			$transcript = Transcript::where( 'id', $input['TransId'] )
				->where( 'user_id', $data['user_id'] )
				->where( 'school_type', 'highschool' )
				->first();

			// Remove from S3 Bucket
			// $s3 = AWS::get('s3');
            $s3 = AWS::createClient('s3');
			$bucket = 'asset.plexuss.com';
			$keyname = 'users/transcripts/' . $transcript->transcript_name;

			$delete = $s3->deleteObject(array(
				'Bucket' => $bucket,
				'Key'    => $keyname
			));

			// Remove from DB if S3 Remove successful
			if( $delete ){
				$transcript->delete();
				return json_encode( array( 'msg' => 'Transcript deleted successfully!' ) );
			}
			else{
				return json_encode( array( 'msg' => 'There was a problem deleting that transcript.' ) );
			}
		}

		if ($input['postType'] == 'transcriptupload'){
			// If filename isn't empty
			if( Request::hasFile('profile_upload_files') ){

				// Get file info
				$transcript = Request::file('profile_upload_files');
				$path = $transcript->getRealPath();
				$filename = $transcript->getClientOriginalName();
				$ext = $transcript->getClientOriginalExtension();
				$mime = $transcript->getMimeType();
				$file_path = pathinfo($filename);

				$hashed_id = Crypt::encrypt($data['user_id']);

				$hashed_id = substr($hashed_id, 0, 10);

				$saveas = $file_path['filename'] . '_'.$input['docType'].'_' . date('Y-m-d_H-i-s') . '_' . $hashed_id . "." . strtolower($ext);

				// upload to aws regardless of filetype
                $s3 = AWS::createClient('s3');
				$s3->putObject(array(
					'ACL' => 'public-read',
					'Bucket' => 'asset.plexuss.com',
					'Key' => 'users/transcripts/' . $saveas,
					'SourceFile' => $path
				));

				// Public download path
				$public_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/";

				// Create new transcript record
				$transcript = new Transcript;
				$transcript->transcript_name = $saveas;
				$transcript->transcript_path = $public_path;
				$transcript->school_type = 'highschool';
				$transcript->doc_type = $input['docType'];
				isset($input['transcript_label']) ? $transcript->label = $input['transcript_label'] : NULL;
				$transcript->user_id = $data['user_id'];
				$transcript->save();

				if ($is_plexuss == true) {
					$this->CalcIndicatorPercent($data['user_id']);
					$this->CalcProfilePercent($data['user_id']);
					$this->CalcOneAppPercent($data['user_id']);
				}else{
					$this->CalcIndicatorPercent();
					$this->CalcProfilePercent();
					$this->CalcOneAppPercent();
				}

				$transcript_data = DB::table('transcript')
				->select('id')
				->where('user_id', $data['user_id'])
				->where('transcript_name', $saveas)
				->first();

				$arr = array();
				$arr['transcript_id'] = $transcript_data->id;
				$arr['msg'] 	 = "Transcript uploaded successfully!";
				isset($input['transcript_label']) ? $arr['transcript_label'] = $input['transcript_label'] : NULL;
				$arr['path'] 	 = $public_path. $saveas;
				$arr['doc_type'] = $input['docType'];
				$arr['file_name']= $saveas;
				$arr['user_id']  = $data['user_id'];
				$arr['mime_type'] = $mime;


				return json_encode($arr);
			}else{
				return json_encode( array( 'msg' => "There was an error uploading your transcript."  ) );
			}
		}//end of if transcriptupload

	}//end of uploadcenter post function


	//financial info start
	public function getFinancialInfo( $token = null ){

		if( Auth::check() ){
			$id = Auth::id();

			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			//get users transcript info, if any
			$data['transcript_data'] = array();

			$transcriptData = DB::table('transcript')
				->where('user_id',$user->id)->where('school_type','highschool')
				->get();

			$data['transcript_data'] = $transcriptData;

			$data['ajaxtoken'] = $token;

			$data['amt_able_to_pay'] = $user->financial_firstyr_affordibility;

			return View( 'private.profile.ajax.financialInfo', $data);
		}

	}//end of getfinancialinfo

	public function postFinancialInfo( $token = null ){
		// dd('in post financial info!');
	}//end of postfinancialinfo


	public function getDropDownData( $token = null ){

		$data = array();
		$data['user_id'] = Session::get('userinfo.id');
		$data['in_college'] = User::on('rds1')
			->where('id', $data['user_id'])
			->pluck('in_college');

		$data['in_college'] = $data['in_college'][0];

		$input = Request::all();
		$StrData = "";

		if($input['Type'] == 'subject'){
			$subjectData = DB::table('subjects')->get();
			echo '<option value="">Pick a Subject</option>';
			foreach($subjectData as $key=>$value){
				$sel = "";
				if($value->id == $input['SubjectId']){
					$sel = "selected";
				}
				echo '<option value="'.$value->id.'" '.$sel.'>'.$value->subject.'</option>';
			}
		}elseif($input['Type'] == 'classes' && isset($input['SubjectId'])){
			$classesData = DB::table('classes')
				->where('subject_id', $input['SubjectId'])
				->where('verified', 1)
				->orWhere('subject_id', $input['SubjectId'])
				->where('user_id_submitted', $data['user_id'])
				->get();
			echo '<option value="">Pick a Class</option>';
			foreach( $classesData as $key => $value ){
				$sel = "";
				if( $value->id == $input['ClassId'] ){
					$sel = "selected";
				}
				echo '<option value="' . $value->id . '" ' . $sel . '>' . $value->class_name . '</option>';
			}
			if($input['newClass']){
				echo "<optgroup label='" . "Can&#39;t find your class?" . "'>";
				echo "<option value='new'>Add your class here!</option>";
				echo "</optgroup>";
			}
		}elseif($input['Type'] == 'ethnicities'){
			$ethnicitiesData = DB::table('ethnicities')
				->orderBy('ethnicity', 'asc')
				->get();
			echo '<select name="infoEthnicity" id="infoEthnicity">';
			echo '<option value="">Choose</option>';
			foreach($ethnicitiesData as $key=>$value){
				$sel = "";
				if($value->id == $input['vSel']){
					$sel="selected";
				}
				echo '<option value="'.$value->id.'" '.$sel.'>'.$value->ethnicity.'</option>';
			}
			echo '<select>';
		}elseif($input['Type'] == 'countries'){
			$countries = Country::all();
			echo '<select name="infoEthnicity" id="infoEthnicity">';
			echo '<option value="">Select your country</option>';
			foreach($countries as $key => $country){
				$sel = "";
				if($country->id == $input['vSel']){
					$sel = "selected";
				}
				echo '<option value="' . $country->id . '" ' . $sel . '>'  .$country->country_name . '</option>';
			}
			echo '<select>';
		}elseif($input['Type'] == 'religions'){
			$religionsData = DB::table('religions')
				->orderBy('religion', 'asc')
				->get();
			echo '<select name="infoReligion" id="infoReligion">';
			echo '<option value="">Not Religious</option>';
			foreach($religionsData as $key=>$value){
				$sel = "";
				if($value->id == $input['vSel']){
					$sel="selected";
				}
				echo '<option value="'.$value->id.'" '.$sel.'>'.$value->religion.'</option>';
			}
			echo '<select>';
		}elseif($input['Type'] == 'degree'){
			if(isset($data['in_college']) && $data['in_college'] == 0){
				$optionsData = DB::table('degree_type')->whereNotIn('id',array(4,5,9))->get();
			}else{
				$optionsData = DB::table('degree_type')->get();
			}
			echo '<option value="">Degree type:</option>';
			foreach($optionsData as $key=>$value){
				$sel = "";
				if($value->id == $input['DegreeId']){
					$sel = "selected";
				}
				echo '<option value="'.$value->id.'" '.$sel.'>'.$value->display_name.'</option>';
			}
		}
		elseif($input['Type'] == 'aoc'){
			$optionsData = DB::table('aoc')->get();
			echo '<option value="">Choose AOC</option>';
			foreach($optionsData as $key=>$value){
				$sel = "";
				if($value->id == $input['aocId']){
					$sel = "selected";
				}
				echo '<option value="'.$value->id.'" '.$sel.'>'.$value->display_name.'</option>';
			}
		}elseif($input['Type']=='aos'){
			$optionsData = DB::table('professions')->get();
			echo '<option value="">Choose AOS</option>';
			foreach($optionsData as $key=>$value){
				$sel = "";
				if($value->id == $input['aosId']){
					$sel="selected";
				}
				echo '<option value="'.$value->id.'" '.$sel.'>'.$value->profession_name.'</option>';
			}
		}
	}

	public function getDropDownDataCol( $token = null ){
		if (Auth::check())
		{
			$id = Auth::id();
			/* Not using tokens anymore --MARK FOR DELETE--
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			 */

			$user = User::find($id);
			$input = Request::all();
			$StrData="";
			if($input['Type']=='subject')
			{
				$subjectData=DB::table('subjects')->get();
				echo '<select name="collegeInfoSubject" id="collegeInfoSubject" required onchange="getClassesCol('.$input['SubjectId'].');">';
				echo '<option value="">Pick a Subject</option>';
				if(count($subjectData)>0)
				{
					foreach($subjectData as $key=>$value)
					{
						$sel="";
						if($value->id==$input['SubjectId'])
						$sel="selected";
						echo '<option value="'.$value->id.'" '.$sel.'>'.$value->subject.'</option>';
					}
				}
				echo '<select>';
				echo '<small class="error">Please select a subject</small>';
			}
			if($input['Type']=='classes')
			{
				$classesData=DB::table('classes')->where('subject_id',$input['SubjectId'])->get();
				echo '<select name="collegeInfoClassName" id="collegeInfoClassName" required>';
				echo '<option value="">Pick a Class</option>';
				if(count($classesData)>0)
				{
					foreach($classesData as $key=>$value)
					{
						$sel="";
						if($value->id==$input['ClassId'])
						$sel="selected";
						echo '<option value="'.$value->id.'" '.$sel.'>'.$value->class_name.'</option>';
					}
				}
				echo '<select>';
				echo '<small class="error">Please select a class</small>';
			}
			exit();
		}
	}


	/*
	* Builds profile high school form and returns a view with data.
	*
	* @return VIEW
	*/
	public function getHighSchoolInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			//Build to $data array to pass to view.
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$data['ajaxtoken'] = $token;

			//Build The $hsinfo array to place in $data
			$hsinfo = array();
			$school_years = array();
			$currentSchool = array();

			$incollege = $user->in_college;
			$currentSchoolId = $user->current_school_id;

			/* For high schools attended dropdown
			 * Create an array to hold high school attended. This
			 * array will hold the user's attended schools in a dropdown
			 *
			 * Note: I know this isn't the most efficient or beautiful way
			 * of doing this, but we're crunched for time so I'm just going to
			 * add another query instead of worming my way inside this code.
			 */
			$schools_attended = DB::table( 'educations' )
				->select(
					'educations.user_id',
					'educations.school_id',
					'educations.school_type',
					'high_schools.school_name',
					'high_schools.slug',
					'high_schools.city',
					'high_schools.state'
				)
				->join( 'high_schools', 'educations.school_id', '=', 'high_schools.id' )
				->where( 'educations.school_type', 'highschool' )
				->where( 'user_id', $id )
				->get();

			/***********************************************************************
			 * ==============Create an array to hold dropdown options===============
			 ***********************************************************************
			 */
			// Prepend default option to front of dropdown array
			$hs_attended = array( '' => 'Select a school...' );
			// Generate dropdown array
			foreach( $schools_attended as $school_attended ){
				$hs_attended["Schools you've attended:"][ $school_attended->school_id ] = $school_attended->school_name;
			}
			// Append 'new school' option to end of dropdown
			$hs_attended['Add another school:'] = array( 'new' => 'find another school...' );

			// This array is added to $data later
			$schools_attended = array();
			$schools_attended['high_schools'] = $hs_attended;
			/***********************************************************************/

			/***********************************************************************
			 *========= Create an array to hold subject dropdown options ==========
			 ***********************************************************************
			 * Fetch subjects from DB and prepare an array for blade format
			 */
			$subjects_obj = Subject::all();
			$subjects = array( '' => 'Pick a Subject' );
			foreach( $subjects_obj as $subject ){
				$subjects[$subject->id] = $subject->subject;
			}
			/***********************************************************************/

			// Create current_hs index in user var
			$user->current_hs_id = $incollege ? null : $user->current_school_id;

			// Get all the schools for the user in DB.
			$edu = $user->educations()->where('school_type', '=', 'highschool')->get()->toArray();

			foreach ($edu as $key => $edRow ) {
				$courseCount = 0;
				$unitCount = 0;
				// For each education returned look up the high school info using id.
				$schoolInfo = Highschool::find($edRow['school_id'])->toArray();

				// THIS NEEDS TO BE REBUILT. IT DOES A QUERY FOR EVERY SCHOOL THAT A USER HAS
				// For each education get back a list of courses that user has under that school.
				$courses = $user->courses()
				->leftjoin('classes', 'courses.class_name', '=', 'classes.id')
				->leftjoin('subjects', 'courses.class_type', '=', 'subjects.id')
				->select('courses.*', 'classes.class_name as clName', 'subjects.subject')
				->where('school_id','=',$edRow['school_id'])->orderBy('semester')->get()->toArray();

				foreach ($courses as $cor) {
					$unitCount = $unitCount + $cor['units'];
					$courseCount++;
					$cor['colorNum'] = $key;
					$cor['school_name'] = $schoolInfo['school_name'];
 					$school_years[$cor['school_year']][$cor['semester']][] = $cor;
				}

				if (!$incollege && $currentSchoolId == $edRow['school_id']) {
					$latest = 1;
				} else {
					$latest = 0;
				}

				$hsinfo[$key] = array(
					'school_id' => $edRow['school_id'],
					'school_type' => $edRow['school_type'],
					'school_name' => $schoolInfo['school_name'],
					'latest' => $latest,
					'courseCount' =>$courseCount,
					'courseUnits' =>$unitCount,
					'colorNum' => $key
				);

				if($latest){
					$currentSchool['label'] = $schoolInfo['school_name'];
					$currentSchool['id'] = $edRow['school_id'];
				}

				$schoolInfo = null;
				$courses = null;
			}

			$data['hsSchoolInfo'] = $hsinfo;
			$data['hsSchoolYears'] = $school_years;
			$data['currentSchool'] = $currentSchool;
			$data['schools_attended'] = $schools_attended;
			$data['subjects'] = $subjects;
			$data['user'] = $user->toArray();

			$data['transcript_data']=array();
			$transcriptData=DB::table('transcript')
				->where('user_id',$user->id)->where('school_type','highschool')
				->get();
			$data['transcript_data']=$transcriptData;

			$data['subject_data']=array();
			$subjectData=DB::table('subjects')->get();
			$data['subject_data']=$subjectData;
			return View( 'private.profile.ajax.highschoolInfo', $data);
		}
	}


	/*
	* Handles High School form changes.
	*
	* @return Input info for now.
	*/
	public function postHighSchoolTranscriptInfo( $token = null ){
		if (Auth::check())
		{
			$id = Auth::id();
	        //check if token is good.
	  		//       if ( !$this->checkToken( $token ) )
			// {
	  		//           return 'Invalid Token';
			// }
			$input = Request::all();
			$user = User::find($id);
			$Obj=new User();
			echo $Obj->ProcessResize('transcript',$user->id);
			exit();
		}
	}


	public function postHighSchoolInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			$input = Request::all();

			$filter = array(
				'hs_info_hs_attended' => array(
					'required_if:postType,newandEditCourse',
					'alpha_num'
				),
				'hs_info_new_school' => 'nullable|regex: /^([0-9a-zA-Z\.\(\),\-\'"!@#& ])+$/',
				'postType' =>'alpha_dash',
				'hsSchoolId' =>'sometimes|numeric',
				'originalSchoolId' =>'sometimes|required|numeric',
				'hsSchoolPickedId' =>'numeric',
				'hsInfoSchoolCurrent' =>'sometimes|required|numeric',
				'hsInfoEducationlevel' =>'sometimes|required|alpha_dash',
				'hsInfoUnits' => 'sometimes|required|numeric',
				'hsclassLevel' => 'sometimes|required|numeric',
				'courseId' => 'numeric|nullable',
				//'hsclassGrade' => 'sometimes|required|regex:@^[a-zA-Z0-9/+-]+$@',
				//'hsclassGradeSub' => 'sometimes|required|regex:/^[a-zA-Z0-9+-]+$/',
				'hsInfoSubject' => 'numeric',
				'hsInfoClassName' => 'alpha_num',
				'hs_info_new_class' => 'nullable|regex: /^([0-9a-zA-Z\.\(\),\-\'"!@#& ])+$/',
				'hsInfoSemster' => 'sometimes|required|regex:/^([-a-z0-9_ ])+$/i',
				'TransId' => 'numeric',
				'transcript' => 'mimes:jpeg,png,gif,bmp,doc,docx,pdf'
			);
			$validator = Validator::make( $input, $filter );

			if ($validator->fails())
			{
				//return 'There was an error with the form';
				$messages = $validator->messages();
				return $messages;
				exit();
			}

			//Look up user data by id.
			$user = User::find($id);

			/*
			|-------------------------------------
			| Handle EDIT school items here.
			|-------------------------------------
			*/

			if ($input['postType'] == 'transcriptremove'){
				// Get transcript from DB
				$transcript = Transcript::where( 'id', $input['TransId'] )
					->where( 'user_id', $user->id )
					->where( 'school_type', 'highschool' )
					->first();

				// Remove from S3 Bucket
				$s3 = AWS::get('s3');
					$bucket = 'asset.plexuss.com/users/transcripts';
					$keyname = $transcript->transcript_name;

				$delete = $s3->deleteObject(array(
					'Bucket' => $bucket,
					'Key'    => $keyname
				));

				// Remove from DB if S3 Remove successful
				if( $delete ){
					$transcript->delete();
					return json_encode( array( 'msg' => 'Transcript deleted successfully!' ) );
				}
				else{
					return json_encode( array( 'msg' => 'There was a problem deleting that transcript.' ) );
				}
			}

			if ($input['postType'] == 'transcriptupload'){
				// If filename isn't empty
				if( Request::hasFile('transcript') ){
					// Get file info
					$transcript = Request::file('transcript');
					$path = $transcript->getRealPath();
					$filename = $transcript->getClientOriginalName();
					$ext = $transcript->getClientOriginalExtension();
					$mime = $transcript->getMimeType();
					$saveas = $user->id . '_transcript_hs_' . date('Y-m-d_H-i-s') . "." . strtolower($ext);

					// upload to aws regardless of filetype
					$s3 = AWS::get('s3');
					$s3->putObject(array(
						'ACL' => 'public-read',
						'Bucket' => 'asset.plexuss.com/users/transcripts',
						'Key' => $saveas,
						'SourceFile' => $path
					));

					// Public download path
					$public_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/";

					// Create new transcript record
					$transcript = new Transcript;
					$transcript->transcript_name = $saveas;
					$transcript->transcript_path = $public_path;
					$transcript->school_type = 'highschool';
					$transcript->user_id = $user->id;
					$transcript->save();

					return json_encode( array( 'msg' => "Transcript uploaded successfully!" ) );
				}
				else{
					return json_encode( array( 'msg' => "There was an error uploading your transcript."  ) );
				}

				/*
				if(@$_FILES['transcript']['name']!="")
				{
				$Obj=new User();
				$PhotoName=$Obj->ProcessResize('transcript',$user->id);
				$aws = AWS::get('s3');
				$aws->putObject(array(
				'ACL'	=> 'public-read',
				'Bucket'     => 'asset.plexuss.com/users/transcripts',
				'Key'        =>$PhotoName,
				'SourceFile' =>'../public/dropzone/images/'.$PhotoName,
				));
				unlink('../public/dropzone/images/'.$PhotoName);
				$theId =DB::table('transcript')->insertGetId(array(
				'user_id' =>$user->id,
				'transcript_name' =>$PhotoName,
				'transcript_path' =>$PhotoName,
				'transcript_date' =>date('Y-m-d H:i:s'),
				'status' =>'0'
				));
				return "Transcript Uploaded Successfully.";
				}
				else if($input['TranscriptPath']!="")
				{
					$aws = AWS::get('s3');
					$explode=explode(",",substr($input['TranscriptPath'],0,strlen($input['TranscriptPath'])-1));
					for($i=0;$i<count($explode);$i++)
					{
						$aws->putObject(array(
						'ACL'	=> 'public-read',
						'Bucket'     => 'asset.plexuss.com/users/transcripts',
						'Key'        =>$explode[$i],
						'SourceFile' =>'../public/dropzone/images/'.$explode[$i],
						));
						unlink('../public/dropzone/images/'.$explode[$i]);
						$theId =DB::table('transcript')->insertGetId(array(
						'user_id' =>$user->id,
						'transcript_name' =>$explode[$i],
						'transcript_path' =>$explode[$i],
						'transcript_date' =>date('Y-m-d H:i:s'),
						'status' =>'0'
						));
					}
					return "Transcript Uploaded Successfully.";
				}
				else
				{
				return "Please choose a photo to upload.";
				}
				 */
			}

			if ($input['postType'] == 'editSchool') {

				/* New code added to ADD a custom school not all ready in the Plexuss DB.
				 * This ADDS a new school for a user when they submit after
				 * selecting 'search for your school...' in the dropdown
				 * and entering a NEW, UNVERIFIED school
				 */
				$add_school = $input['hs_info_change_hs_attended'];
				if( $add_school == 'new' && $input['hsSchoolPickedId'] == '' ){
					$new_school = new Highschool;
					$new_school->school_name = $input['hs_info_change_new_school'];
					$new_school->verified = 0;
					$new_school->user_id_submitted = $user->id;
					$new_school->save();
				}

				/* SET SCHOOL ID
				 * We make one variable, school_id to catch all the possible results
				 * for school id. If user picks a school from his/her attended list, we use
				 * that value. If the user picks new from the dropdown, we use either the
				 * id supplied by autocomplete, or the value of the new school (that was added
				 * to the db above) that they entered
				 */
				$hs_attended = $input['hs_info_change_hs_attended'];
				$schoolIdSubmitted = isset( $new_school ) ? $new_school->id : $hs_attended;
				if( $hs_attended == 'new' && $input['hsSchoolPickedId'] != '' ){
					$schoolIdSubmitted = $input['hsSchoolPickedId'];
				}
				//Temp input submits till we build validate block.
				$originalSchoolId = $input['originalSchoolId'];
				$currentChecked = ( isset($input['hsInfoSchoolCurrent'] ) ? true : false );

				//used to store if supplied school ids are found in the users return.
				$oldSchoolIdFound = false;
				$schoolIdSubmittedFound = false;

				// return COLLECTION of all the schools for this user.
				$schools = $user->educations()->get();

				// Search the collection and set oldSchoolIdFound  & schoolIdSubmittedFound to true if found.
				foreach ($schools as $key => $value) {
					if ($value->school_id ==  $originalSchoolId) {
						$oldSchoolIdFound = true;
					}

					if ($value->school_id ==  $schoolIdSubmitted) {
						$schoolIdSubmittedFound = true;
					}
				}

				// Exit if oldSchoolIdFound was not found.
				// Why is user trying to edit it?
				if (!$oldSchoolIdFound ) {
					return 'That school is not found.';
				}

				//Check if user wanted to update originalSchoolId entries to new schoolIdSubmitted. This will change school id info and link courses to the new new id.
				if ($originalSchoolId !== $schoolIdSubmitted ) {

					echo "Changing the schools \n";

					// We check if schoolIdSubmitted is all ready in this users list. if so we just delete this entry. and change the courses in next block.
					if ($schoolIdSubmittedFound) {
						echo "Delete the old ID from DB \n";
						foreach ($schools as $key => $school) {
							if ($school->school_id  == $originalSchoolId) {
								$school = $school->delete();
							}
						}
					} else {
						// Since this is a NEW school for this user we just change this school id to the new one.
						foreach ($schools as $key => $school) {
							if ($school->school_id  == $originalSchoolId) {
								$school->school_id = $schoolIdSubmitted;
								$school->save();
							}
						}
					}

					/* If the user happens to merge his/her current school into another
					 * we turn the new school into the user's current.
					 */
					if( $originalSchoolId == $user->current_school_id && $user->in_college == 0 ){
						$user->current_school_id = $schoolIdSubmitted;
						$user->save();
					}

					// Now we update ALL the courses that where under the originalSchoolId to schoolIdSubmitted submitted.
					$courses = $user->courses()->get();

					// We update ONLY the schools that match the originalSchoolId to the schoolIdSubmitted.
					foreach ($courses as $key => $course) {
						if ($course->school_id == $originalSchoolId) {
							$course->school_id = $schoolIdSubmitted;
							$course->save();
						}
					}
					//Since we deleted items from database
					$schools = $user->educations()->get();
				}

				// if User checked the current tab we can update the schools to match.
				//We needed to add a switch to go from Highschool to college.
				if ($currentChecked) {
					$user->current_school_id = $schoolIdSubmitted;
					$user->in_college = 0;
					$user->save();
				}

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "User Info Updated.";
			}


			/*
			|-------------------------------------
			| Handle School DELETE items here.
			|-------------------------------------
			*/
			if ($input['postType'] == 'deleteSchool') {
				$removal_candidate = $input['hs_info_change_hs_attended'];
				if( $removal_candidate == 'new' ){
					return 'Cannot remove a high school that has not been added!';
				}

				//used to store if supplied school ids are found in the users return.
				$oldSchoolIdFound = false;

				// return COLLECTIONs of all the schools for this user then courses under it..
				$schools = $user->educations()->where('school_id', '=', $removal_candidate)->get();
				$courses = $user->courses()->where('school_id', '=', $removal_candidate)->get();

				//if course collection is empty for the class you want to delete you can remove the school.
				if ( $courses->isEmpty() ) {
					// Search the collection and if  removal_candidate is found delete that row.
					foreach ($schools as $key => $value) {
						if ($value->school_id ==  $removal_candidate && $value->school_type == 'highschool') {
							$value->delete();
						}
					}

					// Delete un-associated/unused custom schools from high_schools table
					$unused_custom_schools = HighSchool::where('user_id_submitted', $user->id)
						->leftjoin('educations', 'high_schools.id', '=', 'educations.school_id')
						->select('high_schools.id', 'educations.id as edu_id')
						->where('high_schools.id', $removal_candidate)
						->where('educations.id', '=', null)
						->delete();

					$this->CalcIndicatorPercent();
					$this->CalcProfilePercent();
					$this->CalcOneAppPercent();
					return 'Deleted the school';
				}else{
					return 'Error: Can not delete the school. Course is not empty!';
				}
			}


			/*
			|-------------------------------------
			| Handle new course items here.
			|-------------------------------------
			*/
			if ($input['postType'] == 'newandEditCourse') {

				/* New code added to ADD a custom school not all ready in the Plexuss DB.
				 * This ADDS a new school for a user when they submit after
				 * selecting 'search for your school...' in the dropdown
				 * and entering a NEW, UNVERIFIED school
				 */
				$add_school = $input['hs_info_hs_attended'];
				if( $add_school == 'new' && $input['hsSchoolId'] == '' ){
					$new_school = new Highschool;
					$new_school->school_name = $input['hs_info_new_school'];
					$new_school->verified = 0;
					$new_school->user_id_submitted = $user->id;
					$new_school->save();
				}

				/* SET SCHOOL ID
				 * We make one variable, school_id to catch all the possible results
				 * for school id. If user picks a school from his/her attended list, we use
				 * that value. If the user picks new from the dropdown, we use either the
				 * id supplied by autocomplete, or the value of the new school (that was added
				 * to the db above) that they entered
				 */
				$hs_attended = $input['hs_info_hs_attended'];
				$school_id = isset( $new_school ) ? $new_school->id : $hs_attended;
				// If user searched for and found a new school from autocomplete
				if( $hs_attended == 'new' && $input['hsSchoolId'] != '' ){
					$school_id = $input['hsSchoolId'];
				}

				// This determines if we're adding a new course or editing an existing one
				if (isset( $input['courseId'] )) {
					$courseId = $input['courseId'];
				} else {
					$courseId = false;
				}

				//Check if this user all ready has this school on his list.
				$school = $user->educations()
					->where('school_id', '=', $school_id)
					->where('school_type', '=', 'highschool')
					->first();

				// If this school is not in their list we add it.
				if( !$school ){
					$edu = new Education(array(
						'school_type' => 'highschool',
						'school_id' => $school_id,
					));
					$school = $user->educations()->save($edu);
				}

				// Check If user entered a custom class; create custom class
				if( $input['hsInfoClassName'] == 'new' && $input['hs_info_new_class'] != '' ){
					$new_class = new schoolClass;
					$new_class->subject_id = $input['hsInfoSubject'];
					$new_class->class_name = $input['hs_info_new_class'];
					$new_class->user_id_submitted = $user->id;
					$new_class->save();
				}

				/* Change the class ID variable to new_class id if present. If not,
				 * set the class_id to whatever the user selected in the dropdown
				 */
				$class_id = isset( $new_class ) ? $new_class->id : $input['hsInfoClassName'];

				if ($courseId) {
					//we get the course by id user is trying to edit.
					$course = $user->courses()->where('id','=', $courseId)->first();

					//update the items in this return.
					$course->school_id = $school_id;
					$course->school_type = 'highschool';
					$course->school_year = $input['hsInfoEducationlevel'];
					$course->semester = $input['hsInfoSemster'];
					$course->class_type = $input['hsInfoSubject'];
					$course->class_name = $class_id;
					$course->class_level = $input['hsclassLevel'];
					$course->units = $input['hsInfoUnits'];
					$course->course_grade_type = $input['hsclassGrade'];
					$course->course_grade = $input['hsclassGradeSub'];
					$course->save();

				} else {
					$course = new Course(array(
						'school_id' => $school_id,
						'school_type' => 'highschool',
						'school_year' => $input['hsInfoEducationlevel'],
						'semester' => $input['hsInfoSemster'],
						'class_type' => $input['hsInfoSubject'],
						'class_name' => $class_id,
						'class_level' => $input['hsclassLevel'],
						'units' => $input['hsInfoUnits'],
						'course_grade_type' => $input['hsclassGrade'],
						'course_grade' => $input['hsclassGradeSub'],
					));

					$course = $user->courses()->save($course);
				}
				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Course edited";
			}


			/*
			|-------------------------------------
			| Handle delete course items here.
			|-------------------------------------
			*/
			if ($input['postType'] == 'deleteCourse') {
				$hsCourseId = $input['courseId'];
				//get the course user wants to delete.
				$courseId = $user->courses()->where('id', '=', $hsCourseId)->get();

				//delete for each return.
				foreach ($courseId as $key => $value) {
					$value->delete();
				}

				/* Delete un-associated custom classes that have neither a high school
				 * or college associated with it.
				 */
				$unused_custom_classes = schoolClass::where('user_id_submitted', $user->id)
					->leftjoin('courses', 'classes.id', '=', 'courses.class_name')
					->select('classes.*', 'courses.id as course_id')
					->where('courses.id', '=', null)
					->delete();

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return 'Course has been deleted.';
			}

		}
	}


	public function getCollegeInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			//Build to $data array to pass to view.
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$data['ajaxtoken'] = $token;

			//Build The $hsinfo array to place in $data
			$hsinfo = array();
			$school_years = array();
			$currentSchool = array();

			$incollege = $user->in_college;
			$currentSchoolId = $user->current_school_id;

			/* Create an array to hold colleges attended. This
			 * array will hold the user's attended schools in a dropdown
			 *
			 * Note: I know this isn't the most efficient or beautiful way
			 * of doing this, but we're crunched for time so I'm just going to
			 * add another query instead of worming my way inside this code.
			 */
			$schools_attended = DB::table( 'educations' )
				->select(
					'educations.user_id',
					'educations.school_id',
					'educations.school_type',
					'colleges.school_name',
					'colleges.slug',
					'colleges.city',
					'colleges.state'
				)
				->join( 'colleges', 'educations.school_id', '=', 'colleges.id' )
				->where( 'educations.school_type', 'college' )
				->where( 'user_id', $id )
				->get();

			/***********************************************************************
			 * ==============Create an array to hold dropdown options===============
			 ***********************************************************************
			 */
			// Prepend default option to front of dropdown array
			$colleges_attended = array( '' => 'Select a school...' );
			// Generate dropdown array
			foreach( $schools_attended as $school_attended ){
					$colleges_attended["Schools you've attended:"][ $school_attended->school_id ] = $school_attended->school_name;
			}
			// Append 'new school' option to end of dropdown
			$colleges_attended['Add another school:'] = array( 'new' => 'find another school...' );

			// This array is added to $data later
			$schools_attended = array();
			$schools_attended['colleges'] = $colleges_attended;
			/***********************************************************************/

			/***********************************************************************
			 *========= Create an array to hold subject dropdown options ==========
			 ***********************************************************************
			 * Fetch subjects from DB and prepare an array for blade format
			 */
			$subjects_obj = Subject::all();
			$subjects = array( '' => 'Pick a Subject' );
			foreach( $subjects_obj as $subject ){
				$subjects[$subject->id] = $subject->subject;
			}
			/***********************************************************************/

			// Create current_hs index in user var
			$user->current_college_id = $incollege ? $user->current_school_id : null;

			// Get all the schools for the user in DB.
			$edu = $user->educations()->where('school_type', '=', 'college')->get()->toArray();

			// THIS NEEDS TO BE REBUILT. IT DOES A QUERY FOR EVERY SCHOOL THAT A USER HAS
			foreach ($edu as $key => $edRow ) {
				$courseCount = 0;
				$unitCount = 0;
				// For each education returned look up the high school info using id.
				$schoolInfo = College::find($edRow['school_id'])->toArray();

				// For each education get back a list of courses that user has under that school.
				$courses = $user->courses()
				->select('courses.*', 'subjects.subject', 'classes.class_name as clName')
				->leftjoin('subjects', 'courses.class_type', '=', 'subjects.id')
				->leftjoin('classes', 'courses.class_name', '=', 'classes.id')
				->where('school_id', '=', $edRow['school_id'])
				->orderBy('semester')
				->get()
				->toArray();

				foreach ($courses as $cor) {

					$unitCount = $unitCount + $cor['units'];
					$courseCount++;
					$cor['colorNum'] = $key;
					$cor['school_name'] = $schoolInfo['school_name'];
 					$school_years[$cor['school_year']][$cor['semester']][] = $cor;
				}

				if ($incollege && $currentSchoolId == $edRow['school_id']) {
					$latest = 1;
				} else {
					$latest = 0;
				}

				$hsinfo[$key] = array(
					'school_id' => $edRow['school_id'],
					'school_type' => $edRow['school_type'],
					'school_name' => $schoolInfo['school_name'],
					'latest' => $latest,
					//'graduation_date' => $edRow['graduation_date'],
					'courseCount' =>$courseCount,
					'courseUnits' =>$unitCount,
					'colorNum' => $key
				);

				if($latest){
					$currentSchool['label'] = $schoolInfo['school_name'];
					$currentSchool['id'] = $edRow['school_id'];
				}

				$schoolInfo = null;
				$courses = null;
			}
			//END FOREACH

			$data['collegeSchoolInfo'] = $hsinfo;
			$data['collegeSchoolYears'] = $school_years;
			$data['currentSchool'] = $currentSchool;
			$data['schools_attended'] = $schools_attended;
			$data['subjects'] = $subjects;
			$data['user'] = $user->toArray();

			$data['transcript_data']=array();
			$transcriptData=DB::table('transcript')
				->where('user_id',$user->id)->where('school_type','college')
				->get();
			$data['transcript_data']=$transcriptData;

			return View( 'private.profile.ajax.college', $data);
		}
	}


	public function postCollegeInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			$input = Request::all();

			$filter = array(
				'col_info_col_attended' => array(
					'required_if:postType,newandEditCourse',
					'alpha_num'
				),
				'col_info_new_school' => array(
					'required_if:col_info_col_attended,new',
					'regex: /^([0-9a-zA-Z\.\(\),\-\'"!@#& ])+$/'
				),
				'col_info_change_col_attended' => array(
					'required_if:postType,editCollege'
				),
				'postType' =>'alpha_dash',
				'collegeSchoolId' =>'sometimes|numeric',
				'originalCollegeId' =>'sometimes|required|numeric',
				'CollegePickedId' =>'numeric',
				'collegeInfoSchoolCurrent' =>'sometimes|required|numeric',
				'collegeInfoEducationlevel' =>'sometimes|required|alpha_dash',
				'collegeInfoUnits' => 'sometimes|required|numeric',
				'classLevel' => 'sometimes|required|numeric',
				// 'courseId' => 'numeric|present',
				//'classGrade' => 'sometimes|required|regex:@^[a-zA-Z0-9/+-]+$@',
				//'classGradeSub' => 'sometimes|required|regex:/^[a-zA-Z0-9+-]+$/',
				'collegeInfoSubject' => 'numeric',
				'collegeInfoClassName' => 'alpha_num',
				// 'col_info_new_class' => array(
				// 	'regex: /^([0-9a-zA-Z\.\(\),\-\'"!@#& ])+$/'
				// ),
				'collegeInfoSemster' => 'sometimes|required|regex:/^([-a-z0-9_ ])+$/i',
				'TransId' => 'numeric',
				'transcript' => 'mimes:jpeg,png,gif,bmp,doc,docx,pdf'
			);
			$validator = Validator::make( $input, $filter );

			if ($validator->fails())
			{
				//return 'There was an error with the form';
				$messages = $validator->messages();
				return $messages;
				exit();
			}

			//Look up user data by id.
			$user = User::find($id);

			/*
			|-------------------------------------
			| Handle EDIT school items here.
			|-------------------------------------
			*/

			if ($input['postType'] == 'transcriptremove') {
				// Get transcript from DB
				$transcript = Transcript::where( 'id', $input['TransId'] )
					->where( 'user_id', $user->id )
					->where( 'school_type', 'college' )
					->first();

				// Remove from S3 Bucket
				$s3 = AWS::get('s3');
					$bucket = 'asset.plexuss.com/users/transcripts';
					$keyname = $transcript->transcript_name;

				$delete = $s3->deleteObject(array(
					'Bucket' => $bucket,
					'Key'    => $keyname
				));

				// Remove from DB if S3 Remove successful
				if( $delete ){
					$transcript->delete();
					return json_encode( array( 'msg' => 'Transcript deleted successfully!' ) );
				}
				else{
					return json_encode( array( 'msg' => 'There was a problem deleting that transcript.' ) );
				}
			}

			if ($input['postType'] == 'transcriptupload'){
				// If filename isn't empty
				if( Request::hasFile('transcript') ){
					// Get file info
					$transcript = Request::file('transcript');
					$path = $transcript->getRealPath();
					$filename = $transcript->getClientOriginalName();
					$ext = $transcript->getClientOriginalExtension();
					$mime = $transcript->getMimeType();
					$saveas = $user->id . '_transcript_col_' . date('Y-m-d_H-i-s') . "." . strtolower($ext);

					// upload to aws regardless of filetype
					$s3 = AWS::get('s3');
					$s3->putObject(array(
						'ACL' => 'public-read',
						'Bucket' => 'asset.plexuss.com/users/transcripts',
						'Key' => $saveas,
						'SourceFile' => $path
					));

					// Public download path
					$public_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/";

					// Create new transcript record
					$transcript = new Transcript;
					$transcript->transcript_name = $saveas;
					$transcript->transcript_path = $public_path;
					$transcript->school_type = 'college';
					$transcript->user_id = $user->id;
					$transcript->save();

					return json_encode( array( 'msg' => "Transcript uploaded successfully!" ) );
				}
				else{
					return json_encode( array( 'msg' => "There was an error uploading your transcript."  ) );
				}

				/*
				if(@$_FILES['transcript']['name']!="")
				{
				$Obj=new User();
				$PhotoName=$Obj->ProcessResize('transcript',$user->id);
				$aws = AWS::get('s3');
				$aws->putObject(array(
				'ACL'	=> 'public-read',
				'Bucket'     => 'asset.plexuss.com/users/transcripts',
				'Key'        =>$PhotoName,
				'SourceFile' =>'../public/dropzone/images/'.$PhotoName,
				));
				unlink('../public/dropzone/images/'.$PhotoName);
				$theId =DB::table('transcript')->insertGetId(array(
				'user_id' =>$user->id,
				'transcript_name' =>$PhotoName,
				'transcript_path' =>$PhotoName,
				'transcript_date' =>date('Y-m-d H:i:s'),
				'status' =>'1'
				));
				return "Transcript Uploaded Successfully.";
				}
				else if($input['TranscriptPathCol']!="")
				{
					$aws = AWS::get('s3');
					$explode=explode(",",substr($input['TranscriptPathCol'],0,strlen($input['TranscriptPathCol'])-1));
					for($i=0;$i<count($explode);$i++)
					{
						$aws->putObject(array(
						'ACL'	=> 'public-read',
						'Bucket'     => 'asset.plexuss.com/users/transcripts',
						'Key'        =>$explode[$i],
						'SourceFile' =>'../public/dropzone/images/'.$explode[$i],
						));
						unlink('../public/dropzone/images/'.$explode[$i]);
						$theId =DB::table('transcript')->insertGetId(array(
						'user_id' =>$user->id,
						'transcript_name' =>$explode[$i],
						'transcript_path' =>$explode[$i],
						'transcript_date' =>date('Y-m-d H:i:s'),
						'status' =>'1'
						));
					}
					return "Transcript Uploaded Successfully.";
				}
				else
				{
				return "Please choose a photo to upload.";
				}
				*/
			}

			if ($input['postType'] == 'editCollege') {

				/* New code added to ADD a custom school not all ready in the Plexuss DB.
				 * This ADDS a new school for a user when they submit after
				 * selecting 'search for your school...' in the dropdown
				 * and entering a NEW, UNVERIFIED school
				 */
				$add_school = $input['col_info_change_col_attended'];
				if( $add_school == 'new' && $input['CollegePickedId'] == '' ){
					$new_school = new College;
					$new_school->school_name = $input['col_info_change_new_school'];
					$new_school->verified = 0;
					$new_school->user_id_submitted = $user->id;
					$new_school->save();
				}

				/* SET SCHOOL ID
				 * We make one variable, school_id to catch all the possible results
				 * for school id. If user picks a school from his/her attended list, we use
				 * that value. If the user picks new from the dropdown, we use either the
				 * id supplied by autocomplete, or the value of the new school (that was added
				 * to the db above) that they entered
				 */
				$col_attended = $input['col_info_change_col_attended'];
				$schoolIdSubmitted = isset( $new_school ) ? $new_school->id : $col_attended;
				if( $col_attended == 'new' && $input['CollegePickedId'] != '' ){
					$schoolIdSubmitted = $input['CollegePickedId'];
				}

				//Temp input submits till we build validate block.
				$originalCollegeId = $input['originalCollegeId'];
				$currentChecked = ( isset($input['collegeInfoSchoolCurrent'] ) ? true : false );

				//used to store if supplied school ids are found in the users return.
				$oldSchoolIdFound = false;
				$schoolIdSubmittedFound = false;

				// return COLLECTION of all the schools for this user.
				$schools = $user->educations()->get();

				// Search the collection and set oldSchoolIdFound  & schoolIdSubmittedFound to true if found.
				foreach ($schools as $key => $value) {
					if ($value->school_id ==  $originalCollegeId) {
						$oldSchoolIdFound = true;
					}

					if ($value->school_id ==  $schoolIdSubmitted) {
						$schoolIdSubmittedFound = true;
					}
				}

				// Exit if oldSchoolIdFound was not found.
				// Why is user trying to edit it?
				if (!$oldSchoolIdFound ) {
					return 'That school is not found.';
				}

				//Check if user wanted to update originalCollegeId entries to new schoolIdSubmitted. This will change school id info and link courses to the new new id.
				if ($originalCollegeId !== $schoolIdSubmitted ) {

					echo "Changing the schools \n";

					// We check if schoolIdSubmitted is all ready in this users list. if so we just delete this entry. and change the courses in next block.
					if ($schoolIdSubmittedFound) {
						echo "Delete the old ID from DB \n";
						foreach ($schools as $key => $school) {
							if ($school->school_id  == $originalCollegeId) {
								$school = $school->delete();
							}
						}
					} else {
						// Since this is a NEW school for this user we just change this school id to the new one.
						foreach ($schools as $key => $school) {
							if ($school->school_id  == $originalCollegeId) {
								$school->school_id = $schoolIdSubmitted;
								$school->save();
							}
						}
					}

					/* If the user happens to merge his/her current school into another
					 * we turn the new school into the user's current.
					 */
					if( $originalCollegeId == $user->current_school_id && $user->in_college == 1 ){
						$user->current_school_id = $schoolIdSubmitted;
						$user->save();
					}

					// Now we update ALL the courses that where under the originalCollegeId to schoolIdSubmitted submitted.
					$courses = $user->courses()->get();

					// We update ONLY the schools that match the originalCollegeId to the schoolIdSubmitted.
					foreach ($courses as $key => $course) {
						if ($course->school_id == $originalCollegeId) {
							$course->school_id = $schoolIdSubmitted;
							$course->save();
						}
					}
					//Since we deleted items from database
					$schools = $user->educations()->get();
				}

				// if User checked the current tab we can update the schools to match.
				if ($currentChecked) {
					$user->current_school_id = $schoolIdSubmitted;
					$user->in_college = 1;
					$user->save();
				}

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "User Info Updated.";
			}


			/*
			|-------------------------------------
			| Handle School DELETE items here.
			|-------------------------------------
			*/
			if ($input['postType'] == 'deleteSchool') {
				$removal_candidate = $input['col_info_change_col_attended'];
				if( $removal_candidate == 'new' ){
					return 'Cannot remove a college that has not been added!';
				}

				//used to store if supplied school ids are found in the users return.
				$oldSchoolIdFound = false;

				// return COLLECTIONs of all the schools for this user then courses under it..
				$schools = $user->educations()->where('school_id', '=', $removal_candidate)->get();
				$courses = $user->courses()->where('school_id', '=', $removal_candidate)->get();

				//if course collection is empty for the class you want to delete you can remove the school.
				if ( $courses->isEmpty() ) {
					// Search the collection and if  removal_candidate is found delete that row.
					foreach ($schools as $key => $value) {
						if ($value->school_id ==  $removal_candidate && $value->school_type == 'college') {
							$value->delete();
						}
					}

					// Delete un-associated/unused custom schools from colleges table
					$unused_custom_schools = College::where('user_id_submitted', $user->id)
						->leftjoin('educations', 'colleges.id', '=', 'educations.school_id')
						->select('colleges.id', 'educations.id as edu_id')
						->where('colleges.id', $removal_candidate)
						->where('educations.id', '=', null)
						->delete();

					$this->CalcIndicatorPercent();
					$this->CalcProfilePercent();
					$this->CalcOneAppPercent();
					return 'Deleted the school';
				}else{
					return 'Error: Can not delete the school. Course is not empty!';
				}
			}


			/*
			|-------------------------------------
			| Handle new course items here.
			|-------------------------------------
			*/
			if ($input['postType'] == 'newandEditCourse') {

				/* New code added to ADD a custom school not all ready in the Plexuss DB.
				 * This ADDS a new school for a user when they submit after
				 * selecting 'search for your school...' in the dropdown
				 * and entering a NEW, UNVERIFIED school
				 */
				$add_school = $input['col_info_col_attended'];
				if( $add_school == 'new' && $input['collegeSchoolId'] == '' ){
					$new_school = new College;
					$new_school->school_name = $input['col_info_new_school'];
					$new_school->verified = 0;
					$new_school->user_id_submitted = $user->id;
					$new_school->save();
				}

				/* SET SCHOOL ID
				 * We make one variable, school_id to catch all the possible results
				 * for school id. If user picks a school from his/her attended list, we use
				 * that value. If the user picks new from the dropdown, we use either the
				 * id supplied by autocomplete, or the value of the new school (that was added
				 * to the db above) that they entered
				 */
				$col_attended = $input['col_info_col_attended'];
				$school_id = isset( $new_school ) ? $new_school->id : $col_attended;
				// If user searched for and found a new school from autocomplete
				if( $col_attended == 'new' && $input['collegeSchoolId'] != '' ){
					$school_id = $input['collegeSchoolId'];
				}

				// This determines if we're adding a new course or editing an existing one
				if (isset( $input['courseId'] )) {
					$courseId = $input['courseId'];
				} else {
					$courseId = false;
				}

				//Check if this user all ready has this school on his list.
				$school = $user->educations()
					->where('school_id', '=', $school_id)
					->where('school_type', '=', 'college')
					->first();

				// If this school is not in their list we add it.
				if( !$school ) {
					$edu = new Education( array(
						'school_type' => 'college',
						'school_id' => $school_id
					));
					$school = $user->educations()->save($edu);
				}

				// Check If user entered a custom class; create custom class
				if( $input['collegeInfoClassName'] == 'new' && $input['col_info_new_class'] != '' ){
					$new_class = new schoolClass;
					$new_class->subject_id = $input['collegeInfoSubject'];
					$new_class->class_name = $input['col_info_new_class'];
					$new_class->user_id_submitted = $user->id;
					$new_class->save();
				}

				/* Change the class ID variable to new_class id if present. If not,
				 * set the class_id to whatever the user selected in the dropdown
				 */
				$class_id = isset( $new_class ) ? $new_class->id : $input['collegeInfoClassName'];

				if ($courseId) {
					//we get the course by id user is trying to edit.
					$course = $user->courses()->where('id','=', $courseId)->first();

					//update the items in this return.
					$course->school_id = $school_id;
					$course->school_type = 'college';
					$course->school_year = $input['collegeInfoEducationlevel'];
					$course->semester = $input['collegeInfoSemster'];
					$course->class_type = $input['collegeInfoSubject'];
					$course->class_name = $class_id;
					$course->class_level = $input['classLevel'];
					$course->units = $input['collegeInfoUnits'];
					$course->course_grade_type = $input['classGrade'];
					$course->course_grade = $input['classGradeSub'];
					$course->save();

				} else {

					$course = new Course(array(
						'school_id' => $school_id,
						'school_type' => 'college',
						'school_year' => $input['collegeInfoEducationlevel'],
						'semester' => $input['collegeInfoSemster'],
						'class_type' => $input['collegeInfoSubject'],
						'class_name' => $class_id,
						'class_level' => $input['classLevel'],
						'units' => $input['collegeInfoUnits'],
						'course_grade_type' => $input['classGrade'],
						'course_grade' => $input['classGradeSub'],
					));

					$course = $user->courses()->save($course);
				}
				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Course edited";
			}


			/*
			|-------------------------------------
			| Handle delete course items here.
			|-------------------------------------
			*/
			if ($input['postType'] == 'deleteCourse') {
				$hsCourseId = $input['courseId'];
				//get the course user wants to delete.
				$courseId = $user->courses()->where('id', '=', $hsCourseId)->get();

				//delete for each return.
				foreach ($courseId as $key => $value) {
					$value->delete();
				}

				/* Delete un-associated custom classes that have neither a high school
				 * or college associated with it.
				 */
				$unused_custom_classes = schoolClass::where('user_id_submitted', $user->id)
					->leftjoin('courses', 'classes.id', '=', 'courses.class_name')
					->select('classes.*', 'courses.id as course_id')
					->where('courses.id', '=', null)
					->delete();

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return 'Course has been deleted.';
			}

		}
	}


	public function getExperienceInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;
			$data['exp_data']=array();
			$data['bulletPoints']=array();
			$experienceData = DB::table('experience')->where('user_id',$user->id)->get();

			//If there's experience data, loop through and decode json bullets to array
			if(count($experienceData)>0) {
				foreach($experienceData as $key => $expData) {

					// Check if user has a bullet points json object
					if($expData->bullet_points != ""){
						// Decode json to an array
						$experienceData[$key]->bullet_points = json_decode($expData->bullet_points);
					}
					else{
						$experienceData[$key]->bullet_points = array();
					}
				}
			}

			$data['exp_data'] = $experienceData;

			$data = array_add( $data, 'user', array(
					'id' => $user->id,
					'fname' => $user->fname,
					'lname' => $user->lname,
			));
			return View('private.profile.ajax.experience', $data);
		}
	}


	public function postExperienceInfo( $token = null ) {

		if (Auth::check()){
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	  		//       if ( !$this->checkToken( $token ) ){
	  		//           return 'Invalid Token';
			// }
			$input = Request::all();

			$filter = array(
	            'whocansee' =>'required|alpha',
				'company_name' =>'required|regex:/^[a-z0-9_\.\-,\'" ]+$/i',
				'title' =>'required|regex:/^[a-z0-9_\' ]+$/i',
				'location' =>'required|regex:/^[a-z0-9_\.\-,\'" ]+$/i',
				'month_from' =>'required|alpha',
				'year_from' =>'required|numeric',
				'month_to' =>'required_without:icurrentlyworkhere',
				'year_to' =>'required_without:icurrentlyworkhere',
				'icurrentlyworkhere' => 'required_without:month_to,year_to',
				//'exp_type' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'bullet' => 'array',
	        );
	        isset($input['description']) ? $input['description'] : $input['description'] = '';
			$validator = Validator::make($input,$filter);
			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;
	            //return 'There was an error with the form';
	        }
			//Sets icurrentlyworkhere option
			$chk_current=0;
			$month_to=$input['month_to'];
			$year_to=$input['year_to'];
			if(isset($input['icurrentlyworkhere'])){
				$chk_current=1;
				$month_to="";
				$year_to="";
			}
			//Look up user data by id.
			$user = User::find($id);
			if(isset($input['expId']) && $input['expId']!="") {
				$expId = $input['expId'];
			}
			else {
				$expId = false;
			}

			//Encode bullets to store in db as json, or empty string
			if(isset($input['bullets'])){
				$bullets = json_encode($input['bullets']);
			}
			else{
				$bullets = null;
			}

			// Post is a new experience
			if(!$expId){
				$theId =DB::table('experience')->insertGetId(array(
				'user_id' =>$id,
				'whocanseethis' => $input['whocansee'],
				'company' => $input['company_name'],
				'title' => $input['title'],
				'location' => $input['location'],
				'month_from' => $input['month_from'],
				'year_from' => $input['year_from'],
				'month_to' =>$month_to,
				'year_to' =>$year_to,
				'currentlyworkhere' =>$chk_current,
				'exp_type' => $input['exp_type'],
				'exp_description' => $input['description'],
				'bullet_points' => $bullets,
				'created_at' =>date('Y-m-d H:i:s'),
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Experience Added Successfully.";
			// Post an EDITED experience
			}else{
				$ObjId =DB::table('experience')->where('id',$expId)
				->update(array(
				'whocanseethis' => $input['whocansee'],
				'company' => $input['company_name'],
				'title' => $input['title'],
				'location' => $input['location'],
				'month_from' => $input['month_from'],
				'year_from' => $input['year_from'],
				'month_to' =>$month_to,
				'year_to' =>$year_to,
				'currentlyworkhere' =>$chk_current,
				'exp_type' => $input['exp_type'],
				'exp_description' => $input['description'],
				'bullet_points' => $bullets,
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Experience Updated Successfully.";
			}
		}
	}

	public function removeExperienceInfo( $token = null ){

		if (Auth::check()){

			//get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	  		//       if ( !$this->checkToken( $token ) ){
	  		//           return 'Invalid Token';
			// }

			//get current user
			$user = User::find($id);
			//stores current experience values - add validation to make sure input isn't empty
			$input = Request::all();
			//get the experience data id that is to be deleted
			$experienceDataId = $input['expId'];

			//deleting experience data where user_id = user's id and id = experienceId
			$deleteExperienceResult = DB::table('experience')
				->where('user_id',$user->id)
				->where('id', $experienceDataId)
				->delete();

			if( $deleteExperienceResult == 1 ){
				return 'Successfully deleted Work Experience!';
			}else{
				return 'Oops! Something went wrong. Could not delete work experience';
			}

		}//end of auth check
	}

	public function removeClubOrgInfo( $token = null ){

		if (Auth::check()){

			//get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	  		//       if ( !$this->checkToken( $token ) )
			// {
	  		//           return 'Invalid Token';
			// }

			//get current user
			$user = User::find($id);
			//stores club/org input field values - add validation to make sure input isn't empty
			$input = Request::all();
			//get the clubOrg data id that is to be deleted
			$clubOrgDataId = $input['clubId'];

			//deleting club/org data where user_id = user's id and id = club/org data id
			$deleteClubOrgResult = DB::table('club_org')
				->where('user_id',$user->id)
				->where('id', $clubOrgDataId)
				->delete();
			/*$deleteClubOrgResult = DB::table('club_org')
				->where('user_id',$user->id)
				->where('id', $clubOrgDataId)
				->get();*/

			if( $deleteClubOrgResult == 1 ){
				return 'Successfully deleted Club/Organization information!';
			}else{
				return 'Oops! Something went wrong. Could not delete Club/Organization information';
			}

		}//end of auth check
	}

	public function removeCertificationInfo( $token = null ){

		if (Auth::check()){

			//get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	  		//       if ( !$this->checkToken( $token ) )
			// {
	  		//           return 'Invalid Token';
			// }

			//get current user
			$user = User::find($id);
			//stores club/org input field values - add validation to make sure input isn't empty
			$input = Request::all();
			//get the clubOrg data id that is to be deleted
			$certificationId = $input['certiId'];

			//deleting club/org data where user_id = user's id and id = club/org data id
			$deleteCertificationsResult = DB::table('certifications')
				->where('user_id',$user->id)
				->where('id', $certificationId)
				->delete();

			if( $deleteCertificationsResult == 1 ){
				return 'Successfully deleted Certification information!';
			}else{
				return 'Oops! Something went wrong. Could not delete Certification information';
			}

		}//end of auth check
	}

	public function removePatentsInfo( $token = null ){

		if (Auth::check()){

			//get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	  		//       if ( !$this->checkToken( $token ) )
			// {
	  		//           return 'Invalid Token';
			// }

			//get current user
			$user = User::find($id);
			//stores club/org input field values - add validation to make sure input isn't empty
			$input = Request::all();
			//get the clubOrg data id that is to be deleted
			$patentId = $input['patentId'];

			//deleting club/org data where user_id = user's id and id = club/org data id
			$deletePatentResult = DB::table('patents')
				->where('user_id',$user->id)
				->where('id', $patentId)
				->delete();

			if( $deletePatentResult == 1 ){
				return 'Successfully deleted Patent information!';
			}else{
				return 'Oops! Something went wrong. Could not delete Patent information';
			}

		}//end of auth check
	}

	public function removePublicationsInfo( $token = null ){

		if (Auth::check()){

			//get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//get current user
			$user = User::find($id);
			//stores club/org input field values - add validation to make sure input isn't empty
			$input = Request::all();
			//get the clubOrg data id that is to be deleted
			$publicationId = $input['publicationId'];

			//deleting club/org data where user_id = user's id and id = club/org data id
			$deletePublicationResult = DB::table('publications')
				->where('user_id',$user->id)
				->where('id', $publicationId)
				->delete();

			if( $deletePublicationResult == 1 ){
				return 'Successfully deleted Publication information!';
			}else{
				return 'Oops! Something went wrong. Could not delete Publication information';
			}

		}//end of auth check
	}

	public function removeHonorAndAwardsInfo( $token = null ){

		if (Auth::check()){

			//get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//get current user
			$user = User::find($id);
			//stores club/org input field values - add validation to make sure input isn't empty
			$input = Request::all();
			//get the clubOrg data id that is to be deleted
			$honorAwardId = $input['honorId'];

			//deleting club/org data where user_id = user's id and id = club/org data id
			$deleteHonorAwardResult = DB::table('honor_award')
				->where('user_id',$user->id)
				->where('id', $honorAwardId)
				->delete();

			if( $deleteHonorAwardResult == 1 ){
				return 'Successfully deleted Honors/Awards information!';
			}else{
				return 'Oops! Something went wrong. Could not delete Honors/Awards information';
			}

		}//end of auth check
	}


	public function getSkillsInfo( $token = null ) {

		if (Auth::check())
		{
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			//Look up user data by id.
			$user = User::find($id);
			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			$skillsData=DB::table('skill_int_lang')->where('user_id',$user->id)->where('flag_type','1')->get();
			$data['skills_data']=$skillsData;

			return View('private.profile.ajax.skills', $data);
		}
	}


   	public function postSkillsInfo( $token = null ) {

		if (Auth::check()) {
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$input = Request::all();
			//Look up user data by id.
			$user = User::find($id);
			if($input['count_skills']>0)
			{
				for($i=1;$i<=$input['count_skills'];$i++)
				{
					if($input['skill_deleted_'.$i]==0 && $input['skill_id_'.$i]==0 && trim($input['skill_'.$i])!="")
					{
						//$BulletPointsData.=$input['bullet_'.$i]."|||";
						$ObjId =DB::table('skill_int_lang')->insert(array(
						'user_id' =>$id,
						'whocanseethis' => $input['whocansee'],
						'name' =>$input['skill_'.$i],
						'name_value' =>$input['skill_value_'.$i],
						'flag_type' =>'1',
						'created_at' =>date('Y-m-d H:i:s'),
						'updated_at' =>date('Y-m-d H:i:s')
						));
					}
					if($input['skill_deleted_'.$i]==0 && $input['skill_id_'.$i]>0 && trim($input['skill_'.$i])!="")
					{
						$ObjId =DB::table('skill_int_lang')
						->where('id','=',$input['skill_id_'.$i])
						->update(array(
						'whocanseethis' => $input['whocansee'],
						'name' =>$input['skill_'.$i],
						'name_value' =>$input['skill_value_'.$i],
						'flag_type' =>'1',
						'updated_at' =>date('Y-m-d H:i:s')
						));
					}
					if($input['skill_deleted_'.$i]==1 && $input['skill_id_'.$i]>0)
					{
						$ObjId =DB::table('skill_int_lang')->where('id','=',$input['skill_id_'.$i])->delete();
					}
				}
			}
			$this->CalcIndicatorPercent();
			$this->CalcProfilePercent();
			$this->CalcOneAppPercent();
			return "Skills Updates Successfully.";
		}
	}


	public function getInterestInfo( $token = null ) {

		if (Auth::check())
		{
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			//Look up user data by id.
			$user = User::find($id);
			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			$interestData=DB::table('skill_int_lang')->where('user_id',$user->id)->where('flag_type','2')->get();
			$data['interests_data']=$interestData;

			return View('private.profile.ajax.interest', $data);
		}
	}


	public function postInterestInfo( $token = null ) {

		if (Auth::check()){
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$input = Request::all();
			//Look up user data by id.
			$user = User::find($id);
			if($input['count_interest']>0)
			{
				for($i=1;$i<=$input['count_interest'];$i++)
				{
					if($input['interest_deleted_'.$i]==0 && $input['interest_id_'.$i]==0 && trim($input['interest_'.$i])!="")
					{
						//$BulletPointsData.=$input['bullet_'.$i]."|||";
						$ObjId =DB::table('skill_int_lang')->insert(array(
						'user_id' =>$id,
						'whocanseethis' => $input['whocansee'],
						'name' =>$input['interest_'.$i],
						'name_value' =>$input['interest_value_'.$i],
						'flag_type' =>'2',
						'created_at' =>date('Y-m-d H:i:s'),
						'updated_at' =>date('Y-m-d H:i:s')
						));
					}
					if($input['interest_deleted_'.$i]==0 && $input['interest_id_'.$i]>0 && trim($input['interest_'.$i])!="")
					{
						$ObjId =DB::table('skill_int_lang')
						->where('id','=',$input['interest_id_'.$i])
						->update(array(
						'whocanseethis' => $input['whocansee'],
						'name' =>$input['interest_'.$i],
						'name_value' =>$input['interest_value_'.$i],
						'flag_type' =>'2',
						'updated_at' =>date('Y-m-d H:i:s')
						));
					}
					if($input['interest_deleted_'.$i]==1 && $input['interest_id_'.$i]>0)
					{
						$ObjId =DB::table('skill_int_lang')->where('id','=',$input['interest_id_'.$i])->delete();
					}
				}
			}
			$this->CalcIndicatorPercent();
			$this->CalcProfilePercent();
			$this->CalcOneAppPercent();
			return "Interests Updated Successfully.";
		}
	}


	public function getClubsOrgsInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			$data['club_data']=array();
			$data['bulletPoints']=array();
			$cluborgData=DB::table('club_org')->where('user_id',$user->id)->get();

			// If there's clubs/orgs data, loop through and decode json bullets to array
			if(count($cluborgData)>0) {
				foreach($cluborgData as $key=>$clubData) {

					if($clubData->bullet_points != ""){
						$cluborgData[$key]->bullet_points = json_decode($clubData->bullet_points);
					}
					else{
						$cluborgData[$key]->bullet_points = array();
					}
				}
			}
			$data['club_data'] = $cluborgData;

			$data = array_add( $data, 'user', array(
					'id' => $user->id,
					'fname' => $user->fname,
					'lname' => $user->lname,
			));
			return View('private.profile.ajax.clubs_orgs', $data);
		}
	}


	public function postClubsOrgsInfo( $token = null ) {

		if (Auth::check()) {
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$input = Request::all();
			$filter = array(
	            'whocansee' =>'required|alpha',
				'club_name' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'position' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'location' =>'required|regex:/^([,-a-z0-9_ ])+$/i',
				'month_from' =>'required|alpha',
				'year_from' =>'required|numeric',
				'month_to' =>'required_without:icurrentlyworkhere',
				'year_to' =>'required_without:icurrentlyworkhere',
				'icurrentlyworkhere' => 'required_without:month_to,year_to'
	        );
	        isset($input['description']) ? $input['description'] : $input['description'] = '';
			$validator = Validator::make($input,$filter);
			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;
	            //return 'There was an error with the form';
	        }
			$chk_current=0;$month_to=$input['month_to'];$year_to=$input['year_to'];
			if(isset($input['icurrentlyworkhere']))
			{
			$chk_current=1;
			$month_to="";
			$year_to="";
			}
			//Look up user data by id.
			$user = User::find($id);
			if(isset($input['clubId']) && @$input['clubId']!="")
			{
			$clubId = $input['clubId'];
			}
			else
			{
			$clubId = false;
			}

			//Encode bullets to store in db as json, or empty string
			if(isset($input['bullets'])){
				$bullets = json_encode($input['bullets']);
			}
			else{
				$bullets = null;
			}

			// Post is a new club/org
			if(!$clubId) {
				$theId =DB::table('club_org')->insertGetId(array(
				'user_id' =>$id,
				'whocanseethis' => $input['whocansee'],
				'club_name' => $input['club_name'],
				'position' => $input['position'],
				'location' => $input['location'],
				'month_from' => $input['month_from'],
				'year_from' => $input['year_from'],
				'month_to' =>$month_to,
				'year_to' =>$year_to,
				'currentlyworkhere' =>$chk_current,
				'club_description' => $input['description'],
				'bullet_points' => $bullets,
				'created_at' =>date('Y-m-d H:i:s'),
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Club Added Successfully.";
			// Post an EDITED eperience
			} else {
				$ObjId =DB::table('club_org')->where('id',$clubId)
				->update(array(
				'whocanseethis' => $input['whocansee'],
				'club_name' => $input['club_name'],
				'position' => $input['position'],
				'location' => $input['location'],
				'month_from' => $input['month_from'],
				'year_from' => $input['year_from'],
				'month_to' =>$month_to,
				'year_to' =>$year_to,
				'currentlyworkhere' =>$chk_current,
				'club_description' => $input['description'],
				'bullet_points' => $bullets,
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Club Updated Successfully.";
			}

		}
	}


	public function getHonorAndAwardsInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;


			$data['honor_data']=array();
			$data['bulletPoints']=array();
			$honorData = DB::table('honor_award')->where('user_id',$user->id)->get();

			//If there's experience data, loop through and decode json bullets to array
			if(count($honorData)>0) {
				foreach($honorData as $key => $honData) {

					if($honData->bullet_points != ""){
						$honorData[$key]->bullet_points = json_decode($honData->bullet_points);
					}
					else{
						$honorData[$key]->bullet_points = array();
					}
				}
			}
			$data['honor_data'] = $honorData;

			$data = array_add( $data, 'user', array(
					'id' => $user->id,
					'fname' => $user->fname,
					'lname' => $user->lname,
			));
			return View('private.profile.ajax.honor_awards', $data);
		}
	}


	public function postHonorAndAwardsInfo( $token = null ) {

		if (Auth::check())
		{
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$input = Request::all();
			$filter = array(
	            'whocansee' =>'required|alpha',
				'title' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'issuer' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'month_received' =>'required|alpha',
				'year_received' =>'required|numeric',
	        );
	        isset($input['description']) ? $input['description'] : $input['description'] = '';
			$validator = Validator::make($input,$filter);
			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;
	            //return 'There was an error with the form';
	        }
			//Look up user data by id.
			$user = User::find($id);
			if(isset($input['honorId']) && @$input['honorId']!="") {
				$honorId = $input['honorId'];
			}
			else {
				$honorId = false;
			}

			//Encode bullets to store in db as json, or empty string
			if(isset($input['bullets'])){
				$bullets = json_encode($input['bullets']);
			}
			else{
				$bullets = null;
			}

			// Post is a NEW honor
			if(!$honorId) {
				$theId =DB::table('honor_award')->insertGetId(array(
				'user_id' =>$id,
				'whocanseethis' => $input['whocansee'],
				'title' => $input['title'],
				'issuer' => $input['issuer'],
				'month_received' => $input['month_received'],
				'year_received' => $input['year_received'],
				'honor_description' => $input['description'],
				'bullet_points' => $bullets,
				'created_at' =>date('Y-m-d H:i:s'),
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Honor and Award Added Successfully.";
				// Post an EDITED honor/award
			} else {
				$ObjId =DB::table('honor_award')->where('id',$honorId)
				->update(array(
				'whocanseethis' => $input['whocansee'],
				'title' => $input['title'],
				'issuer' => $input['issuer'],
				'month_received' => $input['month_received'],
				'year_received' => $input['year_received'],
				'honor_description' => $input['description'],
				'bullet_points' => $bullets,
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Honor and Award Updated Successfully.";
			}

		}
	}


	public function getLanguagesInfo( $token = null ) {

		if (Auth::check())
		{
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			//Look up user data by id.
			$user = User::find($id);
			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			$languagesData=DB::table('skill_int_lang')->where('user_id',$user->id)->where('flag_type','3')->get();
			$data['language_data']=$languagesData;

			return View('private.profile.ajax.languages', $data);
		}
	}


	public function postLanguagesInfo( $token = null ) {

		if (Auth::check())
		{
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$input = Request::all();
			//Look up user data by id.
			$user = User::find($id);
			if($input['count_language']>0)
			{
				for($i=1;$i<=$input['count_language'];$i++)
				{
					if($input['language_deleted_'.$i]==0 && $input['language_id_'.$i]==0 && trim($input['language_'.$i])!="")
					{
						//$BulletPointsData.=$input['bullet_'.$i]."|||";
						$ObjId =DB::table('skill_int_lang')->insert(array(
						'user_id' =>$id,
						'whocanseethis' => $input['whocansee'],
						'name' =>$input['language_'.$i],
						'name_value' =>$input['language_value_'.$i],
						'flag_type' =>'3',
						'created_at' =>date('Y-m-d H:i:s'),
						'updated_at' =>date('Y-m-d H:i:s')
						));
					}
					if($input['language_deleted_'.$i]==0 && $input['language_id_'.$i]>0 && trim($input['language_'.$i])!="")
					{
						$ObjId =DB::table('skill_int_lang')
						->where('id','=',$input['language_id_'.$i])
						->update(array(
						'whocanseethis' => $input['whocansee'],
						'name' =>$input['language_'.$i],
						'name_value' =>$input['language_value_'.$i],
						'flag_type' =>'3',
						'updated_at' =>date('Y-m-d H:i:s')
						));
					}
					if($input['language_deleted_'.$i]==1 && $input['language_id_'.$i]>0)
					{
						$ObjId =DB::table('skill_int_lang')->where('id','=',$input['language_id_'.$i])->delete();
					}
				}
			}
			$this->CalcIndicatorPercent();
			$this->CalcProfilePercent();
			$this->CalcOneAppPercent();
			return "Languages Updated Successfully.";
		}
	}


	public function getCertificationInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			$data['certi_data']=array();
			$data['bulletPoints']=array();
			$certiData=DB::table('certifications')->where('user_id',$user->id)->get();

			//If there's experience data, loop through and decode json bullets to array
			if(count($certiData)>0) {
				foreach($certiData as $key=>$certData) {

					if($certData->bullet_points != ""){
						$certiData[$key]->bullet_points = json_decode($certData->bullet_points);
					}
					else{
						$certiData[$key]->bullet_points = array();
					}
				}
			}
			$data['certi_data'] = $certiData;

			$data = array_add( $data, 'user', array(
					'id' => $user->id,
					'fname' => $user->fname,
					'lname' => $user->lname,
			));
			return View('private.profile.ajax.certification', $data);
		}
	}


	public function postCertificationInfo( $token = null ) {

		if (Auth::check())
		{
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$input = Request::all();
			//look for http, prepend if necessary
			$url = $input['certi_url'];
			if(!$this->hasHTTP($url)){
				$input['certi_url'] = 'http://' . $input['certi_url'];
			}
			$filter = array(
	            'whocansee' =>'required|alpha',
				'certi_name' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'certi_auth' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'certi_license' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'certi_url' => 'required|url',
				'month_received' =>'required|alpha',
				'year_received' =>'required|numeric',
				'month_expire' =>'required_without:notexpire',
				'year_expire' =>'required_without:notexpire',
				'notexpire' => 'required_without:month_expire,year_expire'
	        );
			//Check if "doesn't expire" checkbox is checked. Removes required validation if so
			$notexpire=0;
			$month_expire = isset($input['month_expire']) ? $input['month_expire'] : '';
			$year_expire = isset($input['year_expire']) ? $input['year_expire'] : '';
			isset($input['description']) ? $input['description'] : $input['description'] = '';
			// if(isset($input['notexpire'])){
			// 	$notexpire=1;
			// 	$month_expire="";
			// 	$year_expire="";
			// 	$filter['month_expire'] = 'alpha';
			// 	$filter['year_expire'] = 'numeric';
			// }
			$validator = Validator::make($input,$filter);
			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;
	        }
			/*
			'month_expire' => $input['month_expire'],
				'year_expire' => $input['year_expire'],
			*/
			//Look up user data by id.
			$user = User::find($id);
			if(isset($input['certiId']) && @$input['certiId']!="") {
				$certiId = $input['certiId'];
			}
			else {
				$certiId = false;
			}

			//Encode bullets to store in db as json, or empty string
			if(isset($input['bullets'])){
				$bullets = json_encode($input['bullets']);
			}
			else{
				$bullets = null;
			}

			// Post is a NEW certification
			if(!$certiId) {
				$theId =DB::table('certifications')->insertGetId(array(
				'user_id' =>$id,
				'whocanseethis' => $input['whocansee'],
				'certi_name' => $input['certi_name'],
				'certi_auth' => $input['certi_auth'],
				'certi_license' => $input['certi_license'],
				'certi_url' => $input['certi_url'],
				'month_received' => $input['month_received'],
				'year_received' => $input['year_received'],
				'month_expire' =>$month_expire,
				'year_expire' =>$year_expire,
				'notexpire' =>$notexpire,
				'certi_description' => $input['description'],
				'bullet_points' => $bullets,
				'created_at' =>date('Y-m-d H:i:s'),
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Certification Added Successfully.";
			// Post an EDITED certification
			} else {
				$ObjId =DB::table('certifications')->where('id',$certiId)
				->update(array(
				'whocanseethis' => $input['whocansee'],
				'certi_name' => $input['certi_name'],
				'certi_auth' => $input['certi_auth'],
				'certi_license' => $input['certi_license'],
				'certi_url' => $input['certi_url'],
				'month_received' => $input['month_received'],
				'year_received' => $input['year_received'],
				'month_expire' =>$month_expire,
				'year_expire' =>$year_expire,
				'notexpire' =>$notexpire,
				'certi_description' => $input['description'],
				'bullet_points' => $bullets,
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Certification Updated Successfully.";
			}

		}
	}


	public function getPatentsInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			$data['patents_data']=array();
			$data['bulletPoints']=array();
			$patentsData=DB::table('patents')->where('user_id',$user->id)->get();
			if(count($patentsData)>0) {

				//If there's experience data, loop through and decode json bullets to array
				foreach($patentsData as $key => $patData) {

					if($patData->bullet_points != ""){
						$patentsData[$key]->bullet_points = json_decode($patData->bullet_points);
					}
					else{
						$patentsData[$key]->bullet_points = array();
					}
				}
			}
			$data['patents_data'] = $patentsData;

			$countries_raw = Country::all()->toArray();
			$countries = array();
			foreach( $countries_raw as $country ){
				$countries[ $country[ 'id' ] ] = $country[ 'country_name' ];
			}
			$data['countries'] = $countries;

			$data = array_add( $data, 'user', array(
					'id' => $user->id,
					'fname' => $user->fname,
					'lname' => $user->lname,
			));
			return View('private.profile.ajax.patents', $data);
		}
	}


	public function postPatentsInfo( $token = null ) {

		if (Auth::check())
		{
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$input = Request::all();

			//look for http, prepend if necessary
			$url = $input['patent_url'];
			if(!$this->hasHTTP($url)){
				$input['patent_url'] = 'http://' . $input['patent_url'];
			}
			$filter = array(
	            'whocansee' =>'required|alpha',
				'patent_office' =>'required|regex:/^([0-9])+$/i',
				'patent_authority' =>'required|numeric',
				'patent_app_number' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'patent_title' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'issue_month' =>'required|alpha',
				'issue_day' =>'required|numeric',
				'issue_year' =>'required|numeric',
				'patent_url' =>'required|url',
	        );
	        isset($input['description']) ? $input['description'] : $input['description'] = '';
			$validator = Validator::make($input,$filter);
			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;exit();
	            //return 'There was an error with the form';
	        }
			//Look up user data by id.
			$user = User::find($id);
			if(isset($input['patentId']) && @$input['patentId']!="") {
				$patentId = $input['patentId'];
			}
			else {
				$patentId = false;
			}

			//Encode bullets to store in db as json, or empty string
			if(isset($input['bullets'])){
				$bullets = json_encode($input['bullets']);
			}
			else{
				$bullets = null;
			}

			// Post is a NEW patent
			if(!$patentId) {
				$theId =DB::table('patents')->insertGetId(array(
				'user_id' =>$id,
				'whocanseethis' => $input['whocansee'],
				'patent_office' => $input['patent_office'],
				'patent_authority' => $input['patent_authority'],
				'patent_app_number' => $input['patent_app_number'],
				'patent_title' => $input['patent_title'],
				'issue_month' => $input['issue_month'],
				'issue_day' => $input['issue_day'],
				'issue_year' => $input['issue_year'],
				'patent_url' => $input['patent_url'],
				'patent_description' => $input['description'],
				'bullet_points' => $bullets,
				'created_at' =>date('Y-m-d H:i:s'),
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Patents Added Successfully.";
			}

			// Post is an EDITED patent
			else {
				$ObjId =DB::table('patents')->where('id',$patentId)
				->update(array(
				'whocanseethis' => $input['whocansee'],
				'patent_office' => $input['patent_office'],
				'patent_authority' => $input['patent_authority'],
				'patent_app_number' => $input['patent_app_number'],
				'patent_title' => $input['patent_title'],
				'issue_month' => $input['issue_month'],
				'issue_day' => $input['issue_day'],
				'issue_year' => $input['issue_year'],
				'patent_url' => $input['patent_url'],
				'patent_description' => $input['description'],
				'bullet_points' => $bullets,
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Patents Updated Successfully.";
			}

		}
	}


	public function getPublicationsInfo( $token = null ) {
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find($id);

			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;

			$data['publication_data']=array();
			$data['bulletPoints']=array();
			$publicationData=DB::table('publications')->where('user_id',$user->id)->get();
			if(count($publicationData)>0) {
				foreach($publicationData as $key=>$pubData) {

					if($pubData->bullet_points != ""){
						$publicationData[$key]->bullet_points = json_decode($pubData->bullet_points);
					}
					else{
						$publicationData[$key]->bullet_points = array();
					}
				}
			}
			$data['publication_data'] = $publicationData;


			$data = array_add( $data, 'user', array(
					'id' => $user->id,
					'fname' => $user->fname,
					'lname' => $user->lname,
			));
			return View('private.profile.ajax.publications', $data);
		}
	}


	public function postPublicationsInfo( $token = null ) {

		if (Auth::check())
		{
		    //get id by authcheck
	        $id = Auth::id();
	        //check if token is good.
	        // if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$input = Request::all();
			//add http if missing
			$url = $input['publication_url'];
			if(!$this->hasHTTP($url)){
				$input['publication_url'] = 'http://' . $input['publication_url'];
			}
			$filter = array(
	            'whocansee' =>'required|alpha',
				'title' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'publication' =>'required|regex:/^([-a-z0-9_ ])+$/i',
				'publication_url' =>'required|url',
				'pub_month' =>'required|alpha',
				'pub_day' =>'required|numeric',
				'pub_year' =>'required|numeric',
	        );
	        isset($input['description']) ? $input['description'] : $input['description'] = '';
			$validator = Validator::make($input,$filter);
			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;exit();
	            //return 'There was an error with the form';
	        }
			//Look up user data by id.
			$user = User::find($id);
			if(isset($input['publicationId']) && @$input['publicationId']!="") {
				$publicationId = $input['publicationId'];
			}
			else {
				$publicationId = false;
			}

			//Encode bullets to store in db as json, or empty string
			if(isset($input['bullets'])){
				$bullets = json_encode($input['bullets']);
			}
			else{
				$bullets = null;
			}

			// Post is a NEW publication
			if(!$publicationId) {
				$theId =DB::table('publications')->insertGetId(array(
				'user_id' =>$id,
				'whocanseethis' => $input['whocansee'],
				'title' => $input['title'],
				'publication' => $input['publication'],
				'publication_url' => $input['publication_url'],
				'pub_month' => $input['pub_month'],
				'pub_day' => $input['pub_day'],
				'pub_year' => $input['pub_year'],
				'pub_description' => $input['description'],
				'bullet_points' => $bullets,
				'created_at' =>date('Y-m-d H:i:s'),
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Publications Added Successfully.";
			}
			// Post is an EDITED publication
			else {
				$ObjId =DB::table('publications')->where('id',$publicationId)
				->update(array(
				'whocanseethis' => $input['whocansee'],
				'title' => $input['title'],
				'publication' => $input['publication'],
				'publication_url' => $input['publication_url'],
				'pub_month' => $input['pub_month'],
				'pub_day' => $input['pub_day'],
				'pub_year' => $input['pub_year'],
				'pub_description' => $input['description'],
				'bullet_points' => $bullets,
				'updated_at' =>date('Y-m-d H:i:s')
				));

				$this->CalcIndicatorPercent();
				$this->CalcProfilePercent();
				$this->CalcOneAppPercent();
				return "Publications Updated Successfully.";
			}

		}
	}


	/*
	|------------------------
	| Modal form Ajax controls for profile page.
	|------------------------
	*/

	/**
	* Sets the modal school info.
	*
	* @return string
	*/
	public function modalFormSchoolInfoPost ($token = null){

		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();

			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//validation for firsttimemodal no email field
			$email_validity = false;
			$this_user = User::find($id);
			if( isset($this_user->email) && $this_user->email == 'none' ){
				$no_email_validator = Validator::make(
					array('no_email' => Request::get('no_email')),
					array('no_email' => 'required|email')
				);

				if( $no_email_validator->fails() ){
					return $no_email_validator->failed();
				}else{
					$email_validity = true;
				}
			}



			// First validate user_type, then use user type to validate correct fields
			$filter = array(
				'user_type' => array(
					'required',
					'regex:/^(student|alumni|parent|counselor|university_rep){1}$/'
				)
			);

			// Validate the one field...
			$validator = Validator::make(Request::all(), $filter );

			if($validator->fails()){
				return $validator->failed();
			}
			// Continue Validating only the user input for the form set they filled out
			else{

				$user_type = Request::get('user_type');

				//set grad year rule
				$grad_year_min = date('Y') - 54;
				$grad_year_max = date('Y') + 7;
				$grad_year_rule = "between:" . $grad_year_min . ',' . $grad_year_max;

				// Add only user_type's set of fields to be validated
				$possible_fields = array(
					// Set school id validation
					'school_id' => 'integer',
					//Set key field to [user_type]_field
					$user_type . '_zipcode' => array( 'regex:/^[a-zA-Z0-9\.,\- ]+$/'),
					$user_type . '_country' => array( 'integer' ),
					$user_type . '_school_type' => array( 'regex:/^(highschool|college)$/'),
					$user_type . '_homeschooled' => array( 'regex:/^(1)$/'),
					$user_type . '_school_name' => array(
						'regex: /^([0-9a-zA-Z\.\(\),\-\'"!@#& ])+$/'
					),
					$user_type . '_grad_year' => array(
						'after:' . $grad_year_min,
						'before:' . $grad_year_max
					),
					'is_' . $user_type => array(
						'required',
						'regex:/^(1)$/'
					)
				);
				$form = array();
				foreach($possible_fields as $field => $rules){
					// Set input fields to update the DB
					if(Request::get($field) !== null && Request::get($field) !== ''){
						$form[$field] = Request::get($field);
					}
				}
				//set is_[user_type] to 1 (for DB) eg. is_student, is_counselor etc.
				$form['is_' . $user_type] = 1;
				/*
				echo "<pre>";
				var_dump( $form );
				exit;
				 */

				$validator = Validator::make($form, $possible_fields);
				if($validator->fails()){
					return $validator->messages();
				}
				// UPDATE TO DB AFTER VALIDATION!
				else{

					/* New code here for new school/existing school logic
					 * Sets catch-all homeschooled school id here
					 */
					if( Request::get( $user_type . '_homeschooled' ) == 1 ){
						$school_id = 35829; //catch-all home schooled id here
					}
					else{
						$school_id = Request::get( 'school_id' );
					}

					// set the grad year column we need to target in the users table.
					if (Request::get( $user_type . '_school_type' ) == 'highschool') {
						$gradYearColumn = 'hs_grad_year';
						$inCollege = 0;
					}else{
						$gradYearColumn = 'college_grad_year';
						$inCollege = 1;
					}

					// Make user_type column name
					$user_type_col = 'is_' . $user_type;

					/* IF USER IS ENTERING A NEW SCHOOL
					 * Insert into corresponding DB table as not verified
					 */
					if( $school_id == "" ){
						// Add new school
						$new_school = $inCollege == 1 ? new College : new Highschool;
						$new_school->school_name = Request::get( $user_type . '_school_name' );
						$new_school->verified = 0;
						$new_school->user_id_submitted = $id;
						$new_school->save();

						// Update user table
						$user = User::find( $id );
						$user->country_id = Request::get( $user_type . '_country' );
						// Zip only required for US residents
						if( $user->country_id == 1 ){
							$user->zip = Request::get( $user_type . '_zipcode' );
						}

						if( isset($user->email) && $user->email == 'none' && $email_validity == true ){
							$user->email = Request::get('no_email');
						}

						$user->in_college = $inCollege;
						$user->current_school_id = $new_school->id;
						// $user->profile_page_lock_modal = 0;
						$user->showFirstTimeHomepageModal = 0;
						$user->$user_type_col = 1;
						$user->$gradYearColumn = Request::get( $user_type . '_grad_year' );
						$user->save();

						$education = new Education(array(
							'school_id' => $new_school->id,
							'school_type' => Request::get( $user_type . '_school_type' )
						));
						$user->educations()->save($education);

					}
					/* IF USER IS ENTERING AN EXISTING SCHOOL
					 * Check if ID matches an existing school, update
					 * user table
					 */
					else{
						// Check for school in DB
						if( $inCollege == 0 ){
							$school_id_match = Highschool::where( 'id', '=', $school_id )->first();
						}
						else if( $inCollege == 1 ){
							$school_id_match = College::where ( 'id', '=', $school_id )->first();
						}

						// check if school is in the DB
						$school_exists = !is_null($school_id_match) ? true : false;

						// IF USER IS ENTERING A SCHOOL WE HAVE
						if($school_exists){

							// Check School name for matches
							$input_school = Request::get($user_type . '_school_name');
							$stripped_school = substr($input_school, 0, strripos($input_school, ' - '));

							$user = User::find( $id );
							$user->country_id = Request::get( $user_type . '_country' );
							// Zip only required for US residents
							if( $user->country_id == 1 ){
								$user->zip = Request::get( $user_type . '_zipcode' );
							}

							if( isset($user->email) && $user->email == 'none' && $email_validity == true ){
								$user->email = Request::get('no_email');
							}

							$user->in_college = $inCollege;
							$user->current_school_id = $school_id;
							// $user->profile_page_lock_modal = 0;
							$user->showFirstTimeHomepageModal = 0;
							$user->$user_type_col = 1;
							$user->$gradYearColumn = Request::get( $user_type . '_grad_year' );
							$user->save();

							$education = new Education(array(
								'school_id' => $school_id,
								'school_type' => Request::get( $user_type . '_school_type' )
							));
							$user->educations()->save($education);
						}
						else{
							return "there was an error. =/";
						}
						// End of school_exists if statement
					}
				}
				// END OF VALIDATION if()
			}
		}
	}


	/**
	* Sets the colleges interested in, accepts 3 inputs
	*
	* @return string
	*/
	public function modalFormCollegeInterestsPost ($token = null){
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }



			$filter = array(
				'schoolinterested1' => 'integer',
				'schoolinterested2' => 'integer',
				'schoolinterested3' => 'integer'
			);

			$validator = Validator::make(Request::get(), $filter );

			if ($validator->fails()){
			   return  $validator->failed();
			} else {


				$user = User::find( $id );

				if(Request::get( 'schoolinterested1' )){
					$collegelist = new CollegeList(array(
						'school_id' => Request::get( 'schoolinterested1'),
					 ));
					$collegelist = $user->collegelists()->save($collegelist);
				}

				if(Request::get( 'schoolinterested2' )){
					$collegelist2 = new CollegeList(array(
						'school_id' => Request::get( 'schoolinterested2'),
					 ));
					$collegelist2 = $user->collegelists()->save($collegelist2);
				}

				if(Request::get( 'schoolinterested3' )){
					$collegelist3 = new CollegeList(array(
						'school_id' => Request::get( 'schoolinterested3'),
					 ));
					$collegelist3 = $user->collegelists()->save($collegelist3);
				}
			}
		}
	}


	/**
	* Sets the skip modal (showFirstTimeHomepageModal) on homepage flag in database.
	*
	* @return string
	*/
	public function modalFormSkipSchoolInfo ($token = null){
		//We do two forms of checks.
		//First a Auth Check that we use to get the users ID from session.
		//Then we do a ajaxtoken check. This is deleted when a user logs out or in.
		//A user can lock out his account if he left it logged in at a Library for Example.
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			$user = User::find( $id );
			$user->showFirstTimeHomepageModal = 0;
			$user->save();
			return 'pass';
		}
	}


	public function getRankingInfo( $token = null ){
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if (!$this->checkToken( $token ) ){
			return 'Invalid Token';
			}
			//Look up user data by id.
			$user = User::find($id);
			$data = array( 'token' => $token );
		}
		return View('private.college.ajax.ranking', $data);
	}


	//checkToken returns a user ID id found.
	private function checkToken( $token ) {
		$ajaxtoken = AjaxToken::where( 'token', '=', $token )->first();
		if ( !$ajaxtoken ) {
			return 0;
		} else {
			return 1;
		}
	}


	public function getTopnavSearch(){
		$filter = array( 'search' =>'regex:/^([,-a-zA-Z0-9_ ])+$/i');

		$validator = Validator::make(Request::get(), $filter );

		if ($validator->fails()){
	   		return  $validator->failed();
		} else {
			$esearch = new ElasticSearchController;
			return $return = $esearch->searchCollegeInfo(Request::get('search'));
		}
	}

	public function hasHTTP( $url ){
		$scheme = parse_url($url, PHP_URL_SCHEME);
		if(is_null($scheme)){
			return false;
		}else if($scheme === 'http'){
			return true;
		}else{
			return $scheme;
		}
	}

	//I DONT EVEN KNOW WHAT THESE THINGS DO. WILL COMMENT FOR NOW AND WE CAN REMOVE ONCE WE ARE DONE.
	/*

	public function getCheckListsInfo( $token = null ) {

		if ( !$this->checkToken( $token ) ) {
			return 'Invalid Token';
		}

		$data = array( 'token' => $token );

		$objective = User::find( $userid )->objective;

		if ( !$objective ) {
			$data = array_add( $data, 'isempty', 'true' );
		} else {
			$data = $array_add( $data, 'isempty', 'false' );
		}

		return View( 'private.profile.ajax.checklist', $data );
	}


	public function postCheckListsInfo( $token = null ) {

		if ( !$this->checkToken( $token ) ) {
			return 'Invalid Token';
		}

		$data = array( 'token' => $token );

		return 'Posting Data!';
	}

	public function getTimelineInfo( $token = null ) {

		if ( !$this->checkToken( $token ) ) {
			return 'Invalid Token';
		}

		$data = array( 'token' => $token );

		$objective = User::find( $userid )->objective;

		if ( !$objective ) {
			$data = array_add( $data, 'isempty', 'true' );
		} else {
			$data = $array_add( $data, 'isempty', 'false' );
		}

		return View( 'private.profile.ajax.timeline', $data );
	}


	public function postTimelineInfo( $token = null ) {

		if ( !$this->checkToken( $token ) ) {
			return 'Invalid Token';
		}

		$data = array( 'token' => $token );

		return 'Posting Data!';
	}

	public function getDigitalprofileInfo( $token = null ) {

		if ( !$this->checkToken( $token ) ) {
			return 'Invalid Token';
		}

		$data = array( 'token' => $token );

		$objective = User::find( $userid )->objective;

		if ( !$objective ) {
			$data = array_add( $data, 'isempty', 'true' );
		} else {
			$data = $array_add( $data, 'isempty', 'false' );
		}

		return View( 'private.profile.ajax.digitalprofile', $data );
	}


	public function postDigitalprofileInfo( $token = null ) {

		if ( !$this->checkToken( $token ) ) {
			return 'Invalid Token';
		}

		$data = array( 'token' => $token );

		return 'Posting Data!';
	}
	*/

	public function generateNotiBoxes($color,$percent,$icon,$title,$subtitle,$description,$flag=1){
		$StrBox='<div class="noti-main-div noti-main-div-'.$color.'">
		<span class="arrow-top"></span>
		<div class="noti-inner-div1">
		<div class="noti-title"><div class="title-icon"><img src="/images/'.$icon.'"></div>'.$title.'</div>';
		if($subtitle!=""){
		$StrBox.='<div class="noti-subtitle">'.$subtitle.'</div>';
		}
		$StrBox.='<div class="noti-desc">'.$description.'</div>
		</div>
		<div class="noti-inner-div2">
		YOU’RE AT '.$percent.'%
		</div>
		</div>';
		echo $StrBox;
	}

	public function getUserZip( $token = null ){
		if(Auth::check()){
			$id = Auth::id();
			/*
			if(!$this->checkToken($token)){
				return 'Invalid Token';
			}
			 */
			$zip = DB::table('users')->where('id', '=', $id)->pluck('zip');

			return json_encode($zip);
		}
	}

	/* For What's Next:
	 * Receives all Posts for whats next. Validates and inserts
	 * into DB upon a pass.
	 */
	public function postWhatsNext(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (!isset($input['step_num'])) {
			return false;
		}

		if ($input['step_num'] == 1) {
			$user = User::find($data['user_id']);

			if ($input['user_type'] == 'student') {
				$user->is_student = 1;
			}elseif ($input['user_type'] == 'intl_student') {
				$user->is_intl_student = 1;
			}elseif ($input['user_type'] == 'alumni') {
				$user->is_alumni = 1;
			}elseif ($input['user_type'] == 'parent') {
				$user->is_parent = 1;
			}elseif ($input['user_type'] == 'counselor') {
				$user->is_counselor = 1;
			}elseif ($input['user_type'] == 'university_rep') {
				$user->is_university_rep = 1;
			}

			$country = Country::on('rds1')->where('country_code', $input['country_code'])->first();

			if (isset($country)) {
				$user->country_id = $country->id;
			}

			if (isset($input['zip'])) {
				$user->zip = $input['zip'];

				$zipCodes = new ZipCodes;
				$zipCodes = $zipCodes->getCityStateByZip($input['zip']);

				if (isset($zipCodes)) {
					$user->city   = $zipCodes->CityName;
					$user->state  = $zipCodes->StateAbbr;
				}
			}

			//if user is alum, counselor, or rep, no need to move on to other steps
			if( $input['user_type'] != 'student' && $input['user_type'] != 'parent' ){
				$user->completed_signup = 1;
			}

			$user->save();

		}elseif ($input['step_num'] == 2) {
			$user = User::find($data['user_id']);

			if (isset($input['in_college']) && $input['in_college'] == 1) {
				$user->in_college = $input['in_college'];
				$college = College::on('rds1')->where('school_name', $input['school_name'])->first();

				if (isset($college)) {
					$user->current_school_id = $college->id;
				}
				if (isset($input['grad_year'])) {
					$user->college_grad_year = $input['grad_year'];
				}

			}elseif (isset($input['in_college']) && $input['in_college'] == 0) {
				$user->in_college = $input['in_college'];
				$hs = HighSchool::on('rds1')->where('school_name', $input['school_name'])->first();

				if (isset($hs)) {
					$user->current_school_id = $hs->id;
				}
				if (isset($input['grad_year'])) {
					$user->hs_grad_year = $input['grad_year'];
				}
			}

			$user->save();

		}elseif ($input['step_num'] == 3) {
			$user = User::find($data['user_id']);

			if (isset($input['planned_start_term'])) {
				$user->planned_start_term = $input['planned_start_term'];
			}

			if (isset($input['planned_start_yr'])) {
				$user->planned_start_yr = $input['planned_start_yr'];
			}

			$user->save();

		}elseif ($input['step_num'] == 4) {
			$user = User::find($data['user_id']);

			$attr = array();
			$val  = array();

			$attr['user_id'] = $data['user_id'];

			if (isset($input['degree_id'])) {
				$val['degree_type'] = $input['degree_id'];
			}
			if (isset($input['major_id'])) {
				$val['major_id'] = $input['major_id'];
			}
			if (isset($input['profession_id'])) {
				$val['profession_id'] = $input['profession_id'];
			}


			if (!empty($val)) {
				$tmp = Objective::updateOrCreate($attr, $val);
			}

			if (isset($input['interested_school_type'])) {
				$user->interested_school_type = $input['interested_school_type'];

				$user->save();
			}

		}elseif ($input['step_num'] == 5) {
			$user = User::find($data['user_id']);

			$attr = array();
			$val  = array();

			$attr['user_id'] = $data['user_id'];

			if ($user->in_college == 1) {
				$val['overall_gpa'] = $input['overall_gpa'];//overall_gpa is just input name, not changing on front end based on if in hs or not
			}else{
				$val['hs_gpa'] = $input['overall_gpa'];
			}

			if (isset($input['act_composite'])) {
				$val['act_composite'] = $input['act_composite'];
			}
			if (isset($input['sat_total'])) {
				$val['sat_total'] = $input['sat_total'];
			}
			if (isset($input['toefl_total'])) {
				$val['toefl_total'] = $input['toefl_total'];
			}
			if (isset($input['ielts_total'])) {
				$val['ielts_total'] = $input['ielts_total'];
			}

			if (!empty($val)) {
				$tmp = Score::updateOrCreate($attr, $val);
			}

			if (isset($input['financial_firstyr_affordibility'])) {
				$user->financial_firstyr_affordibility = $input['financial_firstyr_affordibility'];
				$user->completed_signup = 1;

				$user->save();

				// ADD TO FINANCIAL LOGS FOR THE USER.
				$uffal = new UsersFinancialFirstyrAffordibilityLog;
				$uffal->add($data['user_id'], $input['financial_firstyr_affordibility'], $data['user_id'], 'postWhatsNext');
			}
		}
		$this->CalcIndicatorPercent();
		$this->CalcProfilePercent();
		$this->CalcOneAppPercent();
	}

	public function whatsNext(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$user_model = new User;

		$user = $user_model->whatsNextQry($data['user_id']);

		$ret = array();

		$ret['step_num'] = '';
		$ret['content']  = array();

		if(isset($user->completed_signup) && $user->completed_signup == 0 ){
			// Step 1
			if (empty($ret['step_num'])) {
				if (($user->is_student == 0 && $user->is_intl_student == 0 && $user->is_alumni == 0
					&& $user->is_parent == 0 && $user->is_counselor == 0 && $user->is_university_rep == 0) ||
					(!isset($user->country_id))	)  {

					$ret['step_num'] = 1;

					if ($user->is_student == 1) {
						$ret['content']['user_type'] = 'student';
					}elseif ($user->is_intl_student == 1) {
						$ret['content']['user_type'] = 'intl_student';
					}elseif ($user->is_alumni == 1) {
						$ret['content']['user_type'] = 'alumni';
					}elseif ($user->is_parent == 1) {
						$ret['content']['user_type'] = 'parent';
					}elseif ($user->is_counselor == 1) {
						$ret['content']['user_type'] = 'counselor';
					}elseif ($user->is_university_rep == 1) {
						$ret['content']['user_type'] = 'university_rep';
					}

					if (isset($user->country_id)) {
						$ret['content']['country_id'] = $user->country_id;
						$ret['content']['country_code'] = $user->country_code;
						$ret['content']['country_name'] = $user->country_name;
					}

					//get countries
					if (Cache::has(env('ENVIRONMENT') .'_all_countries_with_code')) {
						$countries = Cache::get(env('ENVIRONMENT') .'_all_countries_with_code');
					}else{

						$countries_raw = Country::all()->toArray();
						$countries = array();
						$countries[''] = 'Select...';

						foreach( $countries_raw as $val ){
							$countries[$val['country_code']] = $val['country_name'];
						}

						Cache::put(env('ENVIRONMENT') .'_all_countries_with_code', $countries, 120);
					}

					$ret['countries'] = $countries;
				}
			}

			// Step 2
			if (empty($ret['step_num'])) {
				if ( (!isset($user->college_grad_year) && !isset($user->hs_grad_year)) || !isset($user->current_school_id) || !isset($user->in_college)) {
					$ret['step_num'] = 2;

					if (isset($user->in_college) && isset($user->current_school_id) && $user->in_college == 1 ) {
						$ret['content']['grad_year'] = $user->college_grad_year;
						$ret['content']['in_college']  = $user->in_college;
						$college = College::on('rds1')->where('id', $user->current_school_id)->first();
						$ret['content']['school_name'] = $college->school_name;
					}elseif (isset($user->in_college) && isset($user->current_school_id) && $user->in_college == 0 ) {
						$ret['content']['grad_year'] = $user->hs_grad_year;
						$ret['content']['in_college']  = $user->in_college;
						$college = HighSchool::on('rds1')->where('id', $user->current_school_id)->first();
						$ret['content']['school_name'] = $college->school_name;
					}
				}
			}

			// Step 3
			if (empty($ret['step_num'])) {
				if (!isset($user->planned_start_term) || !isset($user->planned_start_yr)) {
					$ret['step_num'] = 3;

					if (isset($user->planned_start_term)) {
						$ret['content']['planned_start_term'] = $user->planned_start_term;
					}

					if (isset($user->planned_start_yr)) {
						$ret['content']['planned_start_yr'] = $user->planned_start_yr;
					}
				}
			}

			// Step 4
			if (empty($ret['step_num'])) {
				if (!isset($user->degree_id) || !isset($user->major_id) || !isset($user->profession_id)
					 || !isset($user->interested_school_type)) {
					$ret['step_num'] = 4;

					if (isset($user->degree_id)) {
						$ret['content']['degree_id'] 	   = $user->degree_id;
						$ret['content']['degree_initials'] = $user->degree_initials;
						$ret['content']['degree_name'] 	   = $user->degree_name;
					}

					if (isset($user->major_id)) {
						$ret['content']['major_id'] 	   = $user->major_id;
						$ret['content']['major_name'] 	   = $user->major_name;
					}

					if (isset($user->profession_id)) {
						$ret['content']['profession_id']   = $user->profession_id;
						$ret['content']['profession_name'] = $user->profession_name;
					}

					if( isset($user->interested_school_type) ){
						$ret['content']['interested_school_type'] = $user->interested_school_type;
					}

					$degree = new Degree;

					$degree = $degree::all();

					$ret['degree'] = $degree;
				}
			}

			// Step 5
			if (empty($ret['step_num'])) {
				if (!isset($user->financial_firstyr_affordibility) || (!isset($user->overall_gpa) && !isset($user->hs_gpa))) {
					$ret['step_num'] = 5;

					if (isset($user->financial_firstyr_affordibility)) {
						$ret['content']['financial_firstyr_affordibility'] 	   = $user->financial_firstyr_affordibility;
					}

					if ($user->in_college == 1 && isset($user->overall_gpa)) {
						$ret['content']['overall_gpa'] 	   = $user->overall_gpa;
					}elseif ($user->in_college == 0 && isset($user->hs_gpa)) {
						$ret['content']['overall_gpa'] 	   = $user->hs_gpa;
					}
					if (isset($user->sat_total)) {
						$ret['content']['sat_total'] = $user->sat_total;
					}
					if (isset($user->act_composite)) {
						$ret['content']['act_composite'] = $user->act_composite;
					}
					if (isset($user->toefl_total)) {
						$ret['content']['toefl_total'] = $user->toefl_total;
					}
					if (isset($user->ielts_total)) {
						$ret['content']['ielts_total'] = $user->ielts_total;
					}
				}
			}
		}

		return json_encode($ret);
	}

	/* Function for "What's Next" feature:
	 * Get a user's current section, based on the items they've filled out
	 * in their profile.
	 */
	private function getSection($queries){
		// If a user has not initialized their session's section value, then
		// find the first section with nulls and return that
			$num_queries = count($queries);
			/*
			echo "queries: " . $num_queries . "<br>";
			exit;
			 */
			$section = 0;
			foreach($queries as $pair_key => $pair){
				$section_array = $this->getSectionArray($pair);
				foreach($section_array as $s_key => $s_val){
					if(is_null($s_val)){
						break 2;
					}
				}
				//$section = ($section -1 <  $num_queries) ? $section++ : $section;
				$section++;
			}
		if($section > $num_queries -1){
			$section = $num_queries -1;
		}
			/*
			echo "section: " . $section . "<br>";
			echo "num queries: " . $num_queries . "<br>";
			 */
		return $section;
	}

	/* Function for "What's Next" feature:
	 * Gets user's section, skipped forms, and action, and returns
	 * the three in an array
	 */
	private function getUserVars($queries){

		$skip = Request::get('skip');
		if(!is_null($skip) && $skip != ''){
			Session::push('whats_next.skips', $skip);
		}
		$action = Request::get('action');

		if(Session::has('whats_next.section')){
			$section = Session::get('whats_next.section');
			if($section > count($queries) -1){
				$section = 0;
			}
		}
		else{
			$section = $this->getSection($queries);
		}

		return array(
			'skip' => $skip,
			'action' => $action,
			'section' => $section
		);
	}

	/* Function for "What's Next" feature:
	 * Receives an array of column names that had null values. Compares
	 * against an array of columns that the user wants to skip. Returns
	 * the first name in the nulls array that is not on the skip array.
	 * If all are skip, the array is cleared.
	 * param	$nulls		array		an array of columns which have unfilled
	 * 									values. Corresponds to empty steps
	 * return				string		the name of the column/empty step that
	 * 									is to be displayed to the user
	 */
	private function getNextStep($nulls){

		//Session::forget('whats_next.skips');
		//Session::put('whats_next.skips', array('b'));
		if(!Session::has('whats_next.skips')){
			Session::put('whats_next.skips', array());
		}
		$skips = Session::get('whats_next.skips');
		//resets the skip counter if user has skipped all unfilled forms
		if(count($skips) == count($nulls)){
			$skips = array();
			Session::put('whats_next.skips', $skips);
		}
		for($i = count($skips); $i > 0; $i--){
			array_shift($nulls);
		}
		return array_shift($nulls);
	}

	/* Function for 'Whats's Next' feature:
	 * Receives an array of keys and values which correspond to the user's
	 * current section. Sorts through array looking for null values. returns
	 * the array with key => value intact with only those values that were
	 * null.
	 * param	$section	array		an array of key=>val pairs which corresponds
	 * 									to the section the user is currently
	 * 									"stuck" in
	 * return	$nulls		array		an array of column names that have incomplete
	 * 									values
	 */
	private function getIncomplete($section){

		$nulls = array();
		foreach($section as $key => $val){
			if(is_null($val)){
				//add exception for highschool/college here
				if($key == "course_filled"){
					$id = Auth::id();
					$in_college = DB::table('users')->where('id', $id)->pluck('in_college');
					$in_college = $in_college[0];

					if($in_college){
						$key = "in_college";
					}
					else{
						$key = "in_hs";
					}
				}
				$nulls[] = $key;
			}
		}
		return $nulls;
	}

	/* Function for 'What's Next' feature:
	 * Receives a MySQL query string to run in order to generate an array
	 * of values to loop through in order to check what parts of his/her
	 * profile a user has filled out.
	 * param	$pair		array		a query string at [0] and an array of steps
	 * 									at [1]
	 * return	$arr		array		an array containing the results
	 * 									of the mysql query
	 */
	private function getSectionArray($pair){

		$query = $pair[0];
		$steps = $pair[1];
		$obj = DB::select($query);

		if(!isset($obj[0])){
			foreach($steps as $value){
				$arr[$value] = null;
			}
		}
		else{
			$arr = array();
			foreach($obj[0] as $key => $value){
				$arr[$key] = $value;
			}
		}
		return $arr;
	}

	public function suppressProgressAlert(){
		if(Auth::check()){
			$id = Auth::id();
			$user = User::find($id);

			$suppress = Request::get('suppress');
			if( $suppress == 1 ){
				$user->profile_progress_alert = 0;
				$user->save();
			}
		}
	}

	public function getNotifications() {
		if(Auth::check()) {
			$id = Auth::id();
			$user = User::find($id);
			//All we should return from this is a percent number. This numnber is updated on a new post.

			//check to see if we can run a recommendation for the user

			if($user->profile_percent >= 30){



				$portal_notification_model = PortalNotification::where('user_id', '=', $user->id)
											->where('is_recommend' , '=', 1)
											->first();
				if (!isset($portal_notification_model)) {
					$urc = new UserRecommendationController;
					$tmp= $urc->generateCollegeRecommendation();
				}

			}
			$params = array();
			$params['percent'] = $user->profile_percent;

			// Suppress notification if profile_progress is switched on

			//the below statement checked if the profile status button has already been clicked, if so disable it
			//$params['show_progress_alert'] = $user->profile_progress_alert == 0 ? 0 : 1;

			$params['show_progress_alert'] = 1; //this makes it so user can always click the profile button

			return json_encode( $params );
		}

		return 0;
	}

	/* submit Quiz ajax method*/
	public function submitQuizResult(){
		$quiz_id = Request::get('quiz_id');
		$quizresult = Request::get('quizresult');

		$testQuizdata = Validator::make(
			array(
		        'quiz_id' => $quiz_id,
		        'quizresult' => $quizresult,
		    ),
		    array(
		        'quiz_id' => 'required|numeric',
		        'quizresult' => 'required|boolean',
		    )
		);

		if ($testQuizdata->fails()){
		    return "error 667";
		}

		if (Auth::check()){
		    $user_id = Auth::id();
		}else{
			$user_id = 0;
		}

		$ip = '';
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			if (isset( $_SERVER['REMOTE_ADDR']) ) {
				$ip =  $_SERVER['REMOTE_ADDR'];
			}

		}


		//stuff into DB!
		$result = new QuizzeResults;
		$result->quizzes_1_id = $quiz_id;
		$result->user_id = $user_id;
		$result->is_correct = $quizresult;
		$result->ip = $ip;
		$result->save();
		return "SUBMITED";
	}

	//**************** RIGHTHAND SIDE ********************//

	public function getRightHandsidePin(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		return json_encode($this->getRightHandSide($data));
	}

	public function setRightHandSideCityCarousel(){

		$input = Request::all();

		$zip = ZipCodes::where('id', $input['cityId'])->first();
		Session::put('userinfo.longitude', $zip->Longitude);
		Session::put('userinfo.latitude', $zip->Latitude);


	}

	//************** RIGHTHAND SIDE ENDS *****************//

	/**
	 * Set the like for a college/news/list
	 *
	 * @return nothing
	 */
	public function setLikesTally(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$attr = array('type' => $input['type'], 'type_col' => $input['type_col'], 'type_val' => Crypt::decrypt($input['type_val']));

		$val = array('type' => $input['type'], 'type_col' => $input['type_col'], 'type_val' => Crypt::decrypt($input['type_val']), 'ip' => $data['ip']);

		if ($data['signed_in'] == 1) {

			$attr['user_id'] = $data['user_id'];
			$val['user_id'] = $data['user_id'];
		}else{
			$attr['ip'] = $data['ip'];
			$attr['user_id'] = '';
		}

		$tmp = LikesTally::updateOrCreate($attr, $val);
	}
	/**
	 * This sets the notification to read
	*/
	public function setReadNotification(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if($input['type'] == 'notify'){
			$user_id = $data['user_id'];
			$org_school_id = $data['org_school_id'];
			if (isset($data['agency_collection'])) {
				$agency = $data['agency_collection'];
			}else{
				$agency = null;
			}


			if(isset($data['is_organization']) && $data['is_organization'] == 1){

				$ntn = DB::statement('UPDATE `notification_topnavs` SET is_read = 1 where
					(`type_id` = '.$user_id.' and `type` = "user") or
					(`type` = "college" and `type_id` = '.$org_school_id.')');

			}elseif (isset($agency)) {

				$ntn = DB::statement('UPDATE `notification_topnavs` SET is_read = 1 where
					(`type_id` = '.$user_id.' and `type` = "user") or
					(`type` = "agency" and `type_id` = '.$agency->agency_id.')');

			}else{

				$ntn = DB::statement('UPDATE `notification_topnavs` SET is_read = 1 where
					`type_id` = '.$user_id.' and `type` = "user"');
			}

		}elseif ($input['type'] == 'msg'){
			$cmtm = CollegeMessageThreadMembers::where('user_id', $data['user_id'])
													->update(['num_unread_msg' => 0]);
		}

	}

	/**
	 * Setting college viewing student profile
	*/
	public function setCollegeViewingYourProfile(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		$user_id = Crypt::decrypt($input['hashedid']);
		$ntn = new NotificationController();
		$ntn->create( $data['school_name'], 'user', 1, null, $data['user_id'] , $user_id);
	}

	/**
	 * Setting college viewing student profile
	*/
	public function setAgencyViewingYourProfile(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		$user_id = Crypt::decrypt($input['hashedid']);
		$ntn = new NotificationController();
		$ntn->create( $data['agency_collection']->name, 'user', 9, null, $data['user_id'] , $user_id);
	}

	/**
	 * This sets the notification to read
	 */
	public function getTopNavNotification(){
        $input = Request::all();
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$ntn = new NotificationTopNav;

		$ret = array();

		$ret['notification'] = $ntn->getMyNotifications($data, true);

        $app_id = "74c58f77-8cd7-47ae-ba9c-bf79dd86b3bc";
        foreach($ret['notification']['data'] as $val){
            if($val['is_read'] != 1) {
                $type = $val['type'];
                if($type == "college"){
                    $college_id = $val['type_id'];
                    $college = College::where('id', $college_id)->first();
                    $title = ucfirst($college['school_name']);
                    $profile = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/".$college['logo_url'];
                } else if($type == "user") {
                    $user_id = $val['type_id'];
                    $user = User::where('id', $user_id)->first();
                    if($user['profile_img_loc'] == ''){
                        $profile = url("/images/profile/default.png");
                    } else {
                        $profile = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user['profile_img_loc'];
                    }
                    $title = ucfirst($user['fname']). ' '.ucfirst($user['lname']);
                } else {
                    $profile = url("/images/profile/default.png");
                }

                $content = array(
                    "en" => $val['name'].' '.$val['msg'],
                );
                $heading =  array(
                    "en" => $title
                );
                $fields = array(
                    'app_id' => $app_id,
                    'include_player_ids' => array($input['onsignal_user_id']),
                    'data' => array("notification_id" => $val['id']),
                    'url' => url($val['link']),
                    'small_icon' => $profile,
                    'chrome_icon' => $profile,
                    'chrome_web_icon' => $profile,
                    'headings' => $heading,
                    'contents' => $content,
                );

                $fields = json_encode($fields);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                    'Authorization: Basic OWI4MjE1MzAtZDM5MC00YWY4LWE1OWItMmRkZDk4ZDQwNTcy'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_exec($ch);
                curl_close($ch);
            }
        }

		//$ret['notification']['data'] = array_reverse($ret['notification']['data']);

		$ret['messages'] = $ntn->getTopNavMessages($data);
		// dd($ret);
		$ret = $this->array_utf8_encode($ret);
		return response()->json($ret);
	}

	/*
	 ** update notification
	 */
	public function updateTopNavNotification(){
	    $id = Request::get('id');
        $ntn = new NotificationTopNav;
        $data = $ntn->updateTopNavNotification($id);
        return "success";
    }

	/**
	 * This sets the carepackage notify me
	 */
	public function setCarePackageNotifyMe(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$attr = array('email' => $input['email']);

		$vals = array('email' => $input['email']);

		if($data['signed_in'] == 1){
			$attr['user_id'] = $data['user_id'];

			$vals['user_id'] = $data['user_id'];
			$vals['ip'] = $data['ip'];
		}else{

			$attr['ip'] = $data['ip'];

			$vals['ip'] = $data['ip'];
		}

		$tmp = CarePackageNotifyMe::updateOrCreate($attr, $vals);

	}


	/**
	 * This method resends the confirmation email to the user
	 *
	 * @return 'already exist' if email is already in the system and goal was to change email
	 *		   'success' if email changed and email confirmation sent or email has been resent
	 */
	public function resendConfirmationEmail(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();


		$user = User::find($data['user_id']);


		//if operatino was to change email -- search for new email in Database
		//and save if not there
		if (isset($input['new_email'])) {

			$duplicateEmail = User::where('email', $input['new_email'])->first();//returns user with this email, if any
			//if no duplicate email, save in DB, otherwise don't save and return
			if( !isset($duplicateEmail) ){
				$user->email = $input['new_email'];
				$user->save();
				$email = $input['new_email'];
				Session::put('userinfo.session_reset', 1);
			}else{
				return 'already exists';
			}

		}

		//if operation is to resend
		if(isset($input['operation']) && $input['operation'] == 'resend'){
			$email = $user->email;
		}

		//--send a confirmation email --//
		$confirmation = str_random( 20 );
		$token = ConfirmToken::where('user_id', $data['user_id'])->first();

		if(isset($token)){
			$token->delete();
		}

		$token = new ConfirmToken( array( 'token' => $confirmation ) );
		$user->confirmtoken()->save( $token );
		$name = $data['fname'] . " " . $data['lname'];
		$mac = new MandrillAutomationController;
		$mac->confirmationEmailForUsers($name, $email, $confirmation);
		return 'success';
	}





	/**
	 * This method saves the users financial info
	 *
	 * @return null
	 */

	public function saveFinancialInfo(){

		$input = Request::all();

		$user = Session::get('user_table');

		if (isset($user)) {
			$amount = $input['amt_able_to_pay'];

			$user->financial_firstyr_affordibility = $amount;

			$user->save();

			// ADD TO FINANCIAL LOGS FOR THE USER.
			$uffal = new UsersFinancialFirstyrAffordibilityLog;
			$uffal->add($user->id, $amount, $user->id, 'saveFinancialInfo');
			return "success";
		}else{

			return "fail";
		}

	}

	/**
	 * This method returns the user info for an admin chat
	 *
	 * @return null
	 */

	public function adminGetStudentInfo(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		if (!isset($input['userId'])) {
			return;
		}
		//$userId =Crypt::decrypt($input['userId']);
		$userId = $input['userId'];

		$valInput = array('userId' => $userId);

		$valFilters = array('userId' => 'numeric');

		$this->validateInput($valInput, $valFilters);

		$user = new User;

		$user = $user->getUsersInfo($userId);

		$ret = array();

		$ret['transcript'] = array();
		foreach ($user as $key) {

			$ret['fname'] = $key->fname;
			$ret['lname'] = $key->lname;

			if (!isset($key->hs_grad_year) || $key->hs_grad_year == 0) {
				$ret['hs_grad_year'] = 'N/A';
			}else{
				$ret['hs_grad_year'] = $key->hs_grad_year;
			}

			if (!isset($key->college_grad_year) || $key->college_grad_year == 0) {
				$ret['college_grad_year'] = 'N/A';
			}else{
				$ret['college_grad_year'] = $key->college_grad_year;
			}



			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";
			if(isset($key->profile_img_loc)  && $key->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$key->profile_img_loc;
			}


			$ret['profile_img_loc'] = $src;


			if($key->in_college){

				$ret['grad_year'] = $ret['college_grad_year'];

				if(isset($key->overall_gpa)){
					$ret['gpa'] = $key->overall_gpa;
				}else{
					$ret['gpa'] = 'N/A';
				}

				if(isset($key->collegeName)){
					$ret['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->collegeName))));

					$ret['school_city'] = $key->collegeCity;
					$ret['school_state'] = $key->collegeState;
					if ($ret['current_school'] == "Home Schooled") {
						$ret['address'] = $ret['current_school'];
					}else{
						$ret['address'] = $ret['current_school'].', '.$ret['school_city']. ', '.$ret['school_state'];
					}

				}else{
					$ret['current_school'] = 'N/A';
					$ret['school_city'] = 'N/A';
					$ret['school_state'] = 'N/A';

					$ret['address'] = '';
				}

			}else{

				$ret['grad_year'] = $ret['hs_grad_year'];

				if(isset($key->hs_gpa)){
					$ret['gpa'] = $key->hs_gpa;
				}else{
					$ret['gpa'] = 'N/A';
				}


				if(isset($key->hsName)){
					$ret['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->hsName))));
					$ret['school_city'] = $key->hsCity;
					$ret['school_state'] = $key->hsState;

					if ($ret['current_school'] == "Home Schooled") {
						$ret['address'] = $ret['current_school'];
					}else{
						$ret['address'] = $ret['current_school'].', '.$ret['school_city']. ', '.$ret['school_state'];
					}
				}else{
					$ret['current_school'] = 'N/A';
					$ret['school_city'] = 'N/A';
					$ret['school_state'] = 'N/A';

					$ret['address'] = '';
				}

			}

			if($ret['gpa'] == "0.00"){
				$ret['gpa'] = "N/A";
			}

			if($ret['current_school'] !="N/A"){
				if ($ret['current_school'] == "Home Schooled") {
					$ret['current_school'] = $ret['current_school'];
				}else{
					$ret['current_school'] = $ret['current_school'].', '.$ret['school_city']. ', '.$ret['school_state'];
				}
			}

			if(isset($key->sat_total)){
				$ret['sat_score'] = $key->sat_total;
			}else{
				$ret['sat_score'] = 'N/A';
			}

			if(isset($key->act_composite)){
				$ret['act_composite'] = $key->act_composite;
			}else{
				$ret['act_composite'] = 'N/A';
			}

			if(isset($key->toefl_total)){
				$ret['toefl_total'] = $key->toefl_total;
			}else{
				$ret['toefl_total'] = 'N/A';
			}

			if(isset($key->ielts_total)){
				$ret['ielts_total'] = $key->ielts_total;
			}else{
				$ret['ielts_total'] = 'N/A';
			}

			if($ret['toefl_total'] == 0 ){
				$ret['toefl_total'] = 'N/A';
			}

			if($ret['ielts_total'] == 0.0 ){
				$ret['ielts_total'] = 'N/A';
			}


			if($ret['gpa'] == "0.00"){
				$ret['gpa'] = "N/A";
			}

			if($ret['sat_score'] == 0 ){
				$ret['sat_score'] = 'N/A';
			}

			if ($ret['act_composite'] == 0) {
				$ret['act_composite'] = 'N/A';
			}

			if (isset($key->country_code)) {
				$ret['country_code'] = $key->country_code;
				$ret['country_name'] = $key->country_name;
			}else{
				$ret['country_code'] = 'N/A';
				$ret['country_name'] = 'N/A';
			}

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
				$ret['objective'] = null;
			}else{
				$ret['objective'] = "I would like to get a/an ".$degree_name." in ".$major_name.". My dream would be to one day work as a(n) ".$profession_name;
			}
			$ret['major'] = $major_name;

			$ret['financial_firstyr_affordibility'] = $key->financial_firstyr_affordibility;

			if (!isset($ret['financial_firstyr_affordibility']) || $ret['financial_firstyr_affordibility'] ==0) {
				$ret['financial_firstyr_affordibility'] = 'N/A';
			}else{

				if (strpos($ret['financial_firstyr_affordibility'], '-') !== false) {
				    $tmp_arr = array();
				    $tmp_arr = explode("-", $ret['financial_firstyr_affordibility']);

				    $ret['financial_firstyr_affordibility'] = '$'.trim($tmp_arr[0]).' - $'.trim($tmp_arr[1]);
				}else{
					$ret['financial_firstyr_affordibility'] = '$'. $ret['financial_firstyr_affordibility'];
				}

			}

			if (!isset($ret['uploads'])) {
				$ret['uploads'] = array();
			}

			if (isset($key->doc_type) && !in_array($key->doc_type, $ret['uploads'])) {
				$ret['uploads'][] = $key->doc_type;
			}
		}

		return $ret;
	}

	private function validateInput($valInput, $valFilters){

		$validator = Validator::make( $valInput, $valFilters );

		if ($validator->fails()){
			$messages = $validator->messages();
			return $messages;
			exit();
		}
	}


	//**************** ADMIN RECOMMENDATION ADVANCED FILTER ********************//
	/**
	 * This method takes data from admin and saves filters for recommendation
	 *
	 * @return null
	 */

	public function setAdminRecommendationFilter($tab_name = null, $input = NULL){
		if ($tab_name == null) {
			return;
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		(!isset($input)) ? $input = Request::all() : NULL;


		$crf = new CollegeRecommendationFilters;
		$crfl = new CollegeRecommendationFilterLogs;

		$agency_id = null;
		if(isset($data['agency_collection'])){
			$agency_id = $data['agency_collection']->agency_id;
		}

		$org_portal_id = NULL;
		if (isset($data['default_organization_portal'])) {
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])){
			$aor_id = $data['aor_id'];
		}

		$post_id = NULL;
		if (isset($input['post_id'])){
			$post_id = $input['post_id'];
			$data['post_id'] = $input['post_id'];

			$agency_id = NULL;
			$org_portal_id = NULL;
			$aor_id = NULL;
			$data['org_school_id'] = NULL;
			$data['org_branch_id'] = NULL;
		}

		$crfl->clearFilter($agency_id, $tab_name, $data['org_school_id'], $data['org_branch_id'], $org_portal_id, $aor_id, $post_id);
		$crf->clearFilter($agency_id, $tab_name, $data['org_school_id'], $data['org_branch_id'], $org_portal_id, $aor_id, $post_id);

		switch ($tab_name) {
			case 'location':

				// $us_filter = $this->inputIsset($input, 'us_filter');
				// $intl_filter = $this->inputIsset($input, 'intl_filter');
				$us_filter = 'true';
				$intl_filter = '';

				//if (isset($us_filter)) {

				$all_country_filter = $this->inputIsset($input, 'all_country_filter');
				$include_country_filter = $this->inputIsset($input, 'include_country_filter');
				$exclude_country_filter = $this->inputIsset($input, 'exclude_country_filter');
				$country = $this->inputIsset($input, 'country');

				$all_state_filter = $this->inputIsset($input, 'all_state_filter');
				$include_state_filter = $this->inputIsset($input, 'include_state_filter');
				$exclude_state_filter = $this->inputIsset($input, 'exclude_state_filter');
				$state = $this->inputIsset($input, 'state');

				$all_city_filter = $this->inputIsset($input, 'all_city_filter');
				$include_city_filter = $this->inputIsset($input, 'include_city_filter');
				$exclude_city_filter = $this->inputIsset($input, 'exclude_city_filter');
				$city = $this->inputIsset($input, 'city');
				//}
				// if ($intl_filter === 'true') {
				// 	$this->saveAdminFilterRecommendation($data, $crf, 'include', $tab_name, 'intl_filter');
				// }else{
				// 	$this->saveAdminFilterRecommendation($data, $crf, 'exclude', $tab_name, 'intl_filter');
				// }

				// if ($us_filter === 'true') {


				// 	$this->saveAdminFilterRecommendation($data, $crf, 'include', $tab_name, 'us_filter');

				// 	$this->saveFilterTemplate2($all_country_filter, $include_country_filter,
				// 	$data, $crf, $tab_name, 'country', $country, $crfl);

				// 	$this->saveFilterTemplate2($all_state_filter, $include_state_filter,
				// 	$data, $crf, $tab_name, 'state', $state, $crfl);

				// 	$this->saveFilterTemplate2($all_city_filter, $include_city_filter,
				// 	$data, $crf, $tab_name, 'city', $city, $crfl);

				// }else{
				// 	$this->saveAdminFilterRecommendation($data, $crf, 'exclude', $tab_name, 'us_filter');
				// }



				$this->saveFilterTemplate2($all_country_filter, $include_country_filter,
				$data, $crf, $tab_name, 'country', $country, $crfl);

				$this->saveFilterTemplate2($all_state_filter, $include_state_filter,
				$data, $crf, $tab_name, 'state', $state, $crfl);

				$this->saveFilterTemplate2($all_city_filter, $include_city_filter,
				$data, $crf, $tab_name, 'city', $city, $crfl);

				break;

			case 'major':
				$all_department_filter = $this->inputIsset($input, 'all_department_filter');
				$include_department_filter = $this->inputIsset($input, 'include_department_filter');
				$exclude_department_filter = $this->inputIsset($input, 'exclude_department_filter');
				$department = $this->inputIsset($input, 'department');

				$this->saveFilterTemplate2($all_department_filter, $include_department_filter,
					$data, $crf, $tab_name, 'department', $department, $crfl);

				$all_major_filter = $this->inputIsset($input, 'all_major_filter');
				$include_major_filter = $this->inputIsset($input, 'include_major_filter');
				$exclude_major_filter = $this->inputIsset($input, 'exclude_major_filter');
				$major = $this->inputIsset($input, 'major');

				$this->saveFilterTemplate2($all_major_filter, $include_major_filter,
					$data, $crf, $tab_name, 'major', $major, $crfl);

				break;

			case 'majorDeptDegree':

				$this->saveFilterForMajor($data, $input, $crf, $crfl);
				break;

			case 'scores':

				$gpaMin_filter = $this->inputIsset($input, 'gpaMin_filter');
				$gpaMax_filter = $this->inputIsset($input, 'gpaMax_filter');
				$hsWeightedGPAMin_filter = $this->inputIsset($input, 'hsWeightedGPAMin_filter');
				$hsWeightedGPAMax_filter = $this->inputIsset($input, 'hsWeightedGPAMax_filter');
				$collegeGPAMin_filter = $this->inputIsset($input, 'collegeGPAMin_filter');
				$collegeGPAMax_filter = $this->inputIsset($input, 'collegeGPAMax_filter');
				$satMin_filter = $this->inputIsset($input, 'satMin_filter');
				$satMax_filter = $this->inputIsset($input, 'satMax_filter');
				$actMin_filter = $this->inputIsset($input, 'actMin_filter');
				$actMax_filter = $this->inputIsset($input, 'actMax_filter');
				$toeflMin_filter = $this->inputIsset($input, 'toeflMin_filter');
				$toeflMax_filter = $this->inputIsset($input, 'toeflMax_filter');
				$ieltsMin_filter = $this->inputIsset($input, 'ieltsMin_filter');
				$ieltsMax_filter = $this->inputIsset($input, 'ieltsMax_filter');

				//gpa
				if(!empty($gpaMin_filter) && !empty($gpaMax_filter)){
					$this->saveFilterTemplate1($gpaMin_filter.','.$gpaMax_filter, $data, $crf, $tab_name, 'gpa_filter', $crfl);
				}
				elseif(!empty($gpaMin_filter)){
					$this->saveFilterTemplate1($gpaMin_filter.',4', $data, $crf, $tab_name, 'gpa_filter', $crfl);
				}
				elseif(!empty($gpaMax_filter)){
					$this->saveFilterTemplate1('0,'.$gpaMax_filter, $data, $crf, $tab_name, 'gpa_filter', $crfl);
				}
				//act/sat
				if(!empty($satMin_filter) && !empty($satMax_filter)){
					$this->saveFilterTemplate1('sat,'.$satMin_filter.','.$satMax_filter, $data, $crf, $tab_name, 'sat_act', $crfl);
				}elseif(!empty($satMin_filter)){
					$this->saveFilterTemplate1('sat,'.$satMin_filter.',2400', $data, $crf, $tab_name, 'sat_act', $crfl);
				}elseif(!empty($satMax_filter)){
					$this->saveFilterTemplate1('sat,0,'.$satMax_filter, $data, $crf, $tab_name, 'sat_act', $crfl);
				}
				if (!empty($actMin_filter) && !empty($actMax_filter)){
					$this->saveFilterTemplate1('act,'.$actMin_filter.','.$actMax_filter, $data, $crf, $tab_name, 'sat_act', $crfl);
				}elseif(!empty($actMin_filter)){
					$this->saveFilterTemplate1('act,'.$actMin_filter.',36', $data, $crf, $tab_name, 'sat_act', $crfl);
				}elseif(!empty($actMax_filter)){
					$this->saveFilterTemplate1('act,0,'.$actMax_filter, $data, $crf, $tab_name, 'sat_act', $crfl);
				}
				//toefl/ielts
				if (!empty($toeflMin_filter) && !empty($toeflMax_filter)){
					$this->saveFilterTemplate1('toefl,'.$toeflMin_filter.','.$toeflMax_filter, $data, $crf, $tab_name, 'ielts_toefl', $crfl);
				}elseif(!empty($toeflMin_filter)){
					$this->saveFilterTemplate1('toefl,'.$toeflMin_filter.',120', $data, $crf, $tab_name, 'ielts_toefl', $crfl);
				}elseif(!empty($toeflMax_filter)){
					$this->saveFilterTemplate1('toefl,0,'.$toeflMax_filter, $data, $crf, $tab_name, 'ielts_toefl', $crfl);
				}
				if (!empty($ieltsMin_filter) && !empty($ieltsMax_filter)){
					$this->saveFilterTemplate1('ielts,'.$ieltsMin_filter.','.$ieltsMax_filter, $data, $crf, $tab_name, 'ielts_toefl', $crfl);
				}elseif(!empty($ieltsMin_filter)){
					$this->saveFilterTemplate1('ielts,'.$ieltsMin_filter.',9', $data, $crf, $tab_name, 'ielts_toefl', $crfl);
				}elseif(!empty($ieltsMax_filter)){
					$this->saveFilterTemplate1('ielts,0,'.$ieltsMax_filter, $data, $crf, $tab_name, 'ielts_toefl', $crfl);
				}
				break;

			case 'uploads':
				$transcript_filter = $this->inputIsset($input, 'transcript_filter');
				$financialInfo_filter = $this->inputIsset($input, 'financialInfo_filter');
				$ielts_fitler = $this->inputIsset($input, 'ielts_fitler');
				$toefl_filter = $this->inputIsset($input, 'toefl_filter');
				$resume_filter = $this->inputIsset($input, 'resume_filter');

				if ($transcript_filter == 'false' || $financialInfo_filter == 'false' || $ielts_fitler == 'false' ||
					$toefl_filter == 'false' || $resume_filter == 'false') {

					$qry_id = $this->saveAdminFilterRecommendation($data, $crf, 'include', $tab_name, 'uploads');

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
				$ethnicity = $this->inputIsset($input, 'ethnicity');
				$all_rgs_filter = $this->inputIsset($input, 'all_rgs_filter');
				$include_rgs_filter = $this->inputIsset($input, 'include_rgs_filter');
				$exclude_rgs_filter = $this->inputIsset($input, 'exclude_rgs_filter');
				$religion = $this->inputIsset($input, 'religion');


				$this->saveFilterTemplate1($ageMin_filter , $data, $crf, $tab_name, 'ageMin_filter', $crfl);
				$this->saveFilterTemplate1($ageMax_filter , $data, $crf, $tab_name, 'ageMax_filter', $crfl);

				if ($all_gender_filter == 'false') {

					$qry_id = $this->saveAdminFilterRecommendation($data, $crf, 'include', $tab_name, 'gender');

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

				$this->saveFilterTemplate2($all_eth_filter, $include_eth_filter,
					$data, $crf, $tab_name, 'include_eth_filter', $ethnicity, $crfl);

				$this->saveFilterTemplate2($all_rgs_filter, $include_rgs_filter,
					$data, $crf, $tab_name, 'include_rgs_filter', $religion, $crfl);

				break;

			case 'educationLevel':

				$hsUsers_filter = $this->inputIsset($input, 'hsUsers_filter');
				$collegeUsers_filter = $this->inputIsset($input, 'collegeUsers_filter');
				if ($hsUsers_filter == 'false' || $collegeUsers_filter == 'false') {

					$qry_id = $this->saveAdminFilterRecommendation($data, $crf, 'include', $tab_name, 'educationLevel');

					$educationLevel = '';
					if ($hsUsers_filter == 'true') {
						$educationLevel = 'hsUsers_filter';
					}else{
						$educationLevel = 'collegeUsers_filter';
					}
					$arr = array();
					$tmp = array();
					$tmp['val'] = $educationLevel;
					$tmp['rec_filter_id'] = $qry_id;
					$arr[] = $tmp;

					$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
				}


				break;

			case 'desiredDegree':

				$certificate = $this->inputIsset($input, '1_filter');
				$associate = $this->inputIsset($input, '2_filter');
				$bachelor = $this->inputIsset($input, '3_filter');
				$master = $this->inputIsset($input, '4_filter');
				$phd = $this->inputIsset($input, '5_filter');
				$undecided = $this->inputIsset($input, '6_filter');
				$diploma = $this->inputIsset($input, '7_filter');
				$other = $this->inputIsset($input, '8_filter');
				$jd = $this->inputIsset($input, '9_filter');

				if ($certificate == "false" || $associate == "false" || $bachelor == "false" ||
					$master == "false" || $phd == "false" || $undecided == "false" ||
					$diploma == "false" || $other == "false" || $jd == "false") {

					$qry_id = $this->saveAdminFilterRecommendation($data, $crf, 'exclude', $tab_name, 'desiredDegree');

					$desiredDegree = array();
					if ($certificate == 'false') {
						$desiredDegree[] = 'Certificate Programs';
					}
					if ($associate == 'false') {
						$desiredDegree[] = "Associate's Degree";
					}
					if ($bachelor == 'false') {
						$desiredDegree[] = "Bachelor's Degree";
					}

					if ($master == 'false') {
						$desiredDegree[] = "Master's Degree";
					}
					if ($phd == 'false') {
						$desiredDegree[] = "PHD / Doctorate";
					}
					if ($undecided == 'false') {
						$desiredDegree[] = "Undecided";
					}

					if ($diploma == 'false') {
						$desiredDegree[] = "Diploma";
					}
					if ($other == 'false') {
						$desiredDegree[] = "Other";
					}
					if ($jd == 'false') {
						$desiredDegree[] = "Juris Doctor";
					}

					$arr = array();
					foreach ($desiredDegree as $key => $value) {
						$tmp = array();
						$tmp['val'] = $value;
						$tmp['rec_filter_id'] = $qry_id;
						$arr[] = $tmp;
					}

					$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);

				}

				break;

			case 'skillsInterests':
				break;
			case 'militaryAffiliation':

				$inMilitary_filter = $this->inputIsset($input, 'inMilitary');
				$militaryAffiliation_filter = $this->inputIsset($input, 'militaryAffiliation');

				$this->saveFilterTemplate1($inMilitary_filter , $data, $crf, $tab_name, 'inMilitary', $crfl);

				if ($militaryAffiliation_filter != false && $militaryAffiliation_filter != 'false') {
					$this->saveFilterTemplate2('false', 'true', $data, $crf, $tab_name, 'militaryAffiliation', $militaryAffiliation_filter, $crfl);
				}

				break;

			case 'profileCompletion':

				$profileCompletion_filter = $this->inputIsset($input, 'profileCompletion');
				$this->saveFilterTemplate1($profileCompletion_filter , $data, $crf, $tab_name, 'profileCompletion', $crfl);

				break;

			case 'startDateTerm':

				$startDateTerm_filter = $this->inputIsset($input, 'startDateTerm');

				$this->saveFilterTemplate2('false', 'true', $data, $crf, $tab_name, 'startDateTerm', $startDateTerm_filter, $crfl);
				break;

			case 'financial':
				if(isset($input['financial'])){
					//get filter
					$financials_filter = $this->inputIsset($input, 'financial');
					//exclude the $interested_in_aid fiter
					$financials_filter = array_diff($financials_filter, array('interested_in_aid'));
					$this->saveFilterTemplate2('false', 'true', $data, $crf, $tab_name, 'financial', $financials_filter, $crfl);
				}
				if(isset($input['interested_in_aid']) && $input['interested_in_aid'] == 'true') {
						$this->saveFilterTemplate1('1', $data, $crf, $tab_name, 'interested_in_aid', $crfl);
				}
				break;
			
			case 'typeofschool':
				$interested_school_type = '0';

				if ($input['both_typeofschool'] == 'true') {
					$interested_school_type = '2';
				}elseif ($input['online_only_typeofschool'] == 'true') {
					$interested_school_type = '1';
				}elseif ($input['campus_only_typeofschool']) {
					$interested_school_type = '0';
				}

				$this->saveFilterTemplate1($interested_school_type , $data, $crf, $tab_name, 'interested_school_type', $crfl);

				break;
			default:
				# code...
				break;
		}

		// Add user ids to recruitment tag cron job to be reset for tagging
		if (Cache::has(env('ENVIRONMENT').'_'.'addTargettingUsersToRecruitmentTagCronJob')) {
			$arr = Cache::get(env('ENVIRONMENT').'_'.'addTargettingUsersToRecruitmentTagCronJob');
		}else{
			$arr = array();
		}
		if (!in_array($data['org_school_id'], $arr)) {
			$arr[] = $data['org_school_id'];
		}


		Cache::forever(env('ENVIRONMENT').'_'.'addTargettingUsersToRecruitmentTagCronJob', $arr);
		// recruitment tag cron jobs ends here.

		$ret = array();
		$ret['status'] = "success";

		return $ret;
	}

	public function resetAdminRecommendationFilter($tab_name = null){
		if( !isset($tab_name) ){
			return 'failed';
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$crf = new CollegeRecommendationFilters;
		$crfl = new CollegeRecommendationFilterLogs;

		$agency_id = null;
		if(isset($data['agency_collection'])){
			$agency_id = $data['agency_collection']->agency_id;
		}

		$org_portal_id = NULL;
		if (isset($data['default_organization_portal'])) {
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		$post_id = NULL;
		if (isset($input['post_id'])) {
			$post_id = $input['post_id'];
		}

		$crfl->clearFilter($agency_id, $tab_name, $data['org_school_id'], $data['org_branch_id'], $org_portal_id, $aor_id, $post_id);
		$crf->clearFilter($agency_id, $tab_name, $data['org_school_id'], $data['org_branch_id'], $org_portal_id, $aor_id, $post_id);

		return 'done';
	}

	/**
	 * This method returns if the input is set or not
	 * @ret boolean
	 */
	private function inputIsset($input, $val){

		if (isset($input[$val])) {
			return $input[$val];
		}else{
			return false;
		}
	}

	private function saveAdminFilterRecommendation($data, $crf, $type, $category, $name){

		$agency_id = null;
		if(isset($data['agency_collection'])){
			$agency_id = $data['agency_collection']->agency_id;
		}

		$org_portal_id = NULL;
		if (isset($data['default_organization_portal'])) {
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		$post_id = NULL;
		if (isset($data['post_id'])) {
			$post_id = $data['post_id'];
		}

		$crf = $crf->saveFilter($agency_id, $data['org_school_id'], $data['org_branch_id'], $type, $category, $name, $org_portal_id, $aor_id, $post_id);

		return $crf->id;
	}

	private function saveAdminFilterLogRecommendation($crfl ,$qry_id = null, $arr = null){



		$crfl->saveFilterLog($arr, $qry_id);

	}

	private function saveFilterTemplate1($cat_name , $data, $crf, $tab_name, $name, $crfl){

		if ($cat_name != 'false' && $cat_name != '') {
			$qry_id = $this->saveAdminFilterRecommendation($data, $crf, 'include', $tab_name, $name);

			$arr = array();
			$tmp = array();
			$tmp['val'] = $cat_name;
			$tmp['rec_filter_id'] = $qry_id;
			$arr[] = $tmp;

			$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
		}
	}

	private function saveFilterTemplate2($all_filter, $include_filter, $data, $crf, $tab_name, $name, $val_arr, $crfl){
		if ($all_filter === 'false') {
			if ($include_filter === 'true') {
				$qry_id =$this->saveAdminFilterRecommendation($data, $crf, 'include', $tab_name, $name);
			}else{
				$qry_id = $this->saveAdminFilterRecommendation($data, $crf, 'exclude', $tab_name, $name);
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

	private function saveFilterForMajor($data, $input, $crf, $crfl){

		//print_r($data);
		//print_r($input);

		$include_bool = false;
		$exclude_bool = false;
		$include_arr  = array();
		$exclude_arr  = array();

		if( isset($input['data']) && !empty($input['data']) ){
			foreach ($input['data'] as $key) {
				if ($include_bool == false && $key['in_ex'] == 'include') {
					$include_qry_id =$this->saveAdminFilterRecommendation($data, $crf, $key['in_ex'], 'majorDeptDegree', 'majorDeptDegree');
					$include_bool = true;
				}

				if ($exclude_bool == false && $key['in_ex'] == 'exclude') {
					$exclude_qry_id =$this->saveAdminFilterRecommendation($data, $crf, $key['in_ex'], 'majorDeptDegree', 'majorDeptDegree');
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
	public function getNumberOfUsersForFilter(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (isset($input['sales_pid'])) {

			$data['post_id'] = $input['sales_pid'];

			$data['agency_collection'] = null;
			$data['default_organization_portal'] = null;
			$data['aor_id'] = null;
			$data['aor_portal_id'] = null;
			$data['scholarship_id'] = null;
			$data['org_school_id'] = null;
			$data['org_branch_id'] = null;
			$data['department_query'] = null;

		}
		$crf = new CollegeRecommendationFilters;
		$filter_cnt = $crf->generateFilterQry($data);
		if (!isset($filter_cnt)) {
			return 100.0;
		}
		

		if (isset($data['agency_collection'])) {
			$filter_cnt = $filter_cnt->leftjoin(DB::raw('(select id,user_id from college_recommendations where agency_id='
						  .$data['agency_collection']->agency_id.') as x1'), 'x1.user_id', '=', 'userFilter.id')
						  ->whereRaw('x1.id IS NULL');
		}elseif(!isset($data['post_id'])){
			
			$filter_cnt = $filter_cnt->leftjoin(DB::raw('(select id,user_id from college_recommendations where college_id='
						  .$data['org_school_id'].') as x1'), 'x1.user_id', '=', 'userFilter.id')
						  ->whereRaw('x1.id IS NULL');
		}

		$filter_cnt = $filter_cnt->count();

		$users = User::on('rds1')->count();

		$perc = ($filter_cnt/ $users) *100;

		$perc = number_format($perc, 2, '.', '');

		return $perc;
	}

	//**************** END OF ADMIN RECOMMENDATION ADVANCED FILTER ********************//

	public function requestToBecomeMember(){
		$mac = new MandrillAutomationController;
		$mac->requestToBecomeMember();
	}

	public function getMajorByDepartment($name = null){
		$major = new Major;
		$name = str_replace('&', '/', $name);
		return $major->getMajorByDepartment($name);
	}

	public function getMajorByDepartmentWithNames(){
		$input = Request::all();
		$major = new Major;
		$name = str_replace('&', '/', $input["name"]);
		return $major->getMajorByDepartment($name);
	}

	public function getMajorByDepartmentWithIds($id = null){
		$major = new Major;
		$name = null;
		$noDefaultOption = true;
		return $major->getMajorByDepartment($name, $id, $noDefaultOption);
	}

	public function getMajorByDepartmentWithNamesAndIds($id = null){
		$major = new Major;
		return $major->getMajorNameAndIdByDepartment($id);
	}

	public function getAllMajorByDepartment(){
		$major = new Major;
		return $major->getAllMajorByDept();
	}

	// -- Admin Content Management - start
	public function saveRankingPin(){
		$data = array();
    	$data['org_school_id'] = Session::get('userinfo.school_id');
        $data['school_slug'] = Session::get('userinfo.school_slug');

		$school_id = $data['org_school_id'];
		$rankingPin_save_id = null;

		$input = Request::all();
		$tmp = array();
		$rankPinData = array();

		$rankingPin_save_id = $input['save_id'] != 'null' ? $input['save_id'] : null;
		$tmp['type'] = 'ranking';
		$tmp['title'] = $input['title'] ? $input['title'] : null;
		$tmp['rank_num'] = $input['rank_num'] ? $input['rank_num'] : null;
		$tmp['source'] = $input['source'] ? $input['source'] : null;
		$tmp['updated_at'] = date("Y-m-d H:i:s");
		$tmp['created_at'] = date("Y-m-d H:i:s");
		$tmp['slug'] = $data['school_slug'];
		$tmp['rank_descript'] = $input['rank_descript'] ? $input['rank_descript'] : null;
		$tmp['custom_college'] = $school_id;

		//if rank image is uploaded, then save it to aws and save image name to db
		if( !empty($input['image']) ){
			$rand_num = mt_rand(0, 10000);
			$rankImg = Request::file('image');
			$imgPath = $rankImg->getRealPath();
			$imgName = $rankImg->getClientOriginalName();
			$imgExtension = $rankImg->getClientOriginalExtension();
			$imgMimeType = $rankImg->getMimeType();
			$saveToAWS = $school_id . '_rankingPin_' . date('Y-m-d') . '_' . $rand_num . "." . strtolower($imgExtension);

			// upload to aws regardless of filetype
            $s3 = AWS::createClient('s3');

			$s3->putObject(array(
				'ACL' => 'public-read',
				'Bucket' => 'asset.plexuss.com',
				'Key' => 'admin/' . $saveToAWS,
				'SourceFile' => $imgPath
			));

			$tmp['image'] = $saveToAWS;
		}

		$rankPinData = $tmp;
		$returnFromSavingId = null;

		if( $rankingPin_save_id == null ){
			$returnFromSavingId = DB::table('lists')->insertGetId($rankPinData);
			return $returnFromSavingId;
		}else{
			$returnFromSavingId = DB::table('lists')->where('id', $rankingPin_save_id)->update($rankPinData);
			return 'successfully updated';
		}

		return $rankPinData;
	}

	//remove ranking pin function
	public function removeRankingPin(){
		$input = Request::all();
		$id = $input['id'];

		DB::table('lists')->where('id', '=', $id)->delete();

		return $id;
	}

	//get already saved pins for editing or deleting
	public function getSavedRankingPins(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$school_id = $data['org_school_id'];

		//get already saved pins
		$getSavedPins = DB::table('lists')->where('custom_college', '=', $school_id)->orderBy('id', 'desc')->get();

		return $getSavedPins;
	}

	public function getSchoolData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$college = new College();
		$collegeData = $college->CollegeOverview($data['org_school_id']);

		$overview_img = CollegeOverviewImages::where('college_id', '=', $data['org_school_id'])
												->where('is_video', '=', 0)
												->get();
		$single_image = null;

		foreach ($overview_img as $key => $value) {
			if( !empty($value->url) ){
				$single_image = $value->url;
				break;
			}
		}

		$collegeData->overview_image = $single_image;

		$overview_base = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/';
		$logo_base = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/';
		$default_logo = 'default-missing-college-logo.png';
		$default_overview = 'no-image-default.png';

		$collegeData->logo_url = isset($collegeData->logo_url) ? $logo_base.$collegeData->logo_url : $logo_base.$default_logo;
		$collegeData->overview_image = isset($collegeData->overview_image) ? $overview_base.$collegeData->overview_image : $overview_base.$default_overview;

		return json_encode($collegeData);
	}

	//save new uploaded school logo
	public function saveLogo(){
		$data = array();
    	$data['org_school_id'] = Session::get('userinfo.school_id');

		$input = Request::all();
		if( !empty($input['logo']) ){

			//get current url name so we can override the old image with the new one under the same name
			$college = College::find($data['org_school_id']);
			$current_logo_name = $college->logo_url;

			$rankImg = Request::file('logo');
			$imgPath = $rankImg->getRealPath();

			// upload to aws regardless of filetype
			$s3 = AWS::get('s3');
			$s3->putObject(array(
				'ACL' => 'public-read',
				'Bucket' => 'asset.plexuss.com/college/logos',
				'Key' => $current_logo_name,
				'SourceFile' => $imgPath
			));

			Session::put('userinfo.session_reset', 1);

			return 'success';
		}

		return 'empty';
	}

	public function getRepData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$org = new Organization;
		$rep = $org->getFrontPageReps($data['org_school_id']);

		$file = null;
		$imgData = null;
		$newFilePath = null;

		if(isset($rep) && count($rep) > 0) {
			if($rep[0]->profile_img_loc != null) {
				$file = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$rep[0]->profile_img_loc;

				try {
				    $imgData = file_get_contents($file);

				 	// $newFilePath = public_path().'/images/temporary/'.$rep[0]->profile_img_loc;
					// $fileHandler = fopen($newFilePath, 'w');
					// fwrite($fileHandler, $imgData);
					// fclose($fileHandler);

				    // use image cache
					if(Session::has(env('ENVIRONMENT').'_'.$data['user_id'].'_profile_pic')){
                        Session::forget(env('ENVIRONMENT').'_'.$data['user_id'].'_profile_pic');
                    }
                    Session::put(env('ENVIRONMENT').'_'.$data['user_id'].'_profile_pic', $imgData);

					// $newFilePath = public_path().'/images/temporary/'.$rep[0]->profile_img_loc;
					// $fileHandler = fopen($newFilePath, 'w');
					// fwrite($fileHandler, $imgData);
					// fclose($fileHandler);
				}
				catch (\Exception $e) {
					$error_alert['msg'] = 'There was a problem to load original representative image.';
		 			// return json_encode( $error_alert );
				}
			}
		}
		$rep[0]->temp_img = base64_encode(Session::get(env('ENVIRONMENT').'_'.$data['user_id'].'_profile_pic'));
		// dd($rep);

		return $rep;
	}

	public function saveRepData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$change_made = false;
		$input = Request::all();

		$rep = OrganizationBranchPermission::find($input['obp_id']);

		//only update attributes if they are different from what we already have
		if( isset($input['name']) && !empty($input['name']) ){
			$name_change = false;
			$split_name = explode(' ', $input['name']);
			$fname = $split_name[0];
			$lname = $split_name[1];
			$user = User::find($rep->user_id);

			if( $user->fname != $fname ){
				$user->fname = $fname;
				$name_change = true;
			}

			if( $user->lname != $lname ){
				$user->lname = $lname;
				$name_change = true;
			}

			if( $name_change ){
				$user->save();
			}

		}

		if( isset($input['title']) && !empty($input['title']) && $rep->title != $input['title'] ){
			$rep->title = $input['title'];
			$change_made = true;
		}

		if( isset($input['description']) && !empty($input['description']) && $rep->description != $input['description'] ){
			$rep->description = $input['description'];
			$change_made = true;
		}

		if( isset($input['member_since']) && !empty($input['member_since']) && $rep->member_since != $input['member_since'] ){
			$rep->member_since = $input['member_since'];
			$change_made = true;
		}

		//if rep changes have been made, then trigger save
		if( $change_made ){
			$rep->save();
		}

		return 'complete';
	}

	public function saveRepPic(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$user = User::find($input['user_id']);
		$randNum = rand(0, 99999);
		$pic = null;

		// delete original user's profile image
		if(isset($user->profile_img_loc) && !is_null( $user->profile_img_loc ) && !empty($user->profile_img_loc) ){

			$image_name = $user->profile_img_loc;
			// Remove from S3 Bucket
			$s3 = AWS::get('s3');
				$bucket = 'asset.plexuss.com/users/images';
				$keyname = $image_name;

			$delete = $s3->deleteObject(array(
				'Bucket' => $bucket,
				'Key'    => $keyname
			));

			if( !$delete ){
				return 'Error';
			}
		}

		// if both rep_temp_preview_pic and profile_pic existed
		// use rep_temp_preview_pic first
		if( isset($input['rep_temp_preview_pic']) && !empty($input['rep_temp_preview_pic']) ) {
			$pic = Request::file('rep_temp_preview_pic');
		}
		// if there is no rep_temp_preview_pic, only has profile_pic
		else if ( isset($input['profile_pic']) && !empty($input['profile_pic']) ){
			//get current url name so we can override the old image with the new one under the same name
			$pic = Request::file('profile_pic');
		} else {
		// if there is no change
			return 'Error';
		}

		$imageinfo = null;

		try {
		    $imageinfo = getimagesize($pic);
		} catch (\Exception $e) {
		    return 'Error';
		}

		if( $pic != null && !empty($pic) && $imageinfo != null && !empty($imageinfo) ) {

			$imgPath = $pic->getRealPath();
			$content_type = $pic->getMimeType();
			$originalName = $pic->getClientOriginalName();
			$ext = $pic->getClientOriginalExtension();
			$newImageName = $input['user_id'].'_'.$user->fname.'_'.$user->lname.'_profile_pic_'.$randNum.'.'.$ext;

			$user->profile_img_loc = $newImageName;
			$user->save();

			//save to aws
			$s3 = AWS::get('s3');
			$s3->putObject(array(
				'ACL' => 'public-read',
				'Bucket' => 'asset.plexuss.com/users/images',
				'Key' => $newImageName,
				'ContentType' => $content_type,
				'SourceFile' => $imgPath
			));

			Session::put('userinfo.session_reset', 1);

			// delete original temperary file name begin with $input['user_id'].'_'.$user->fname.'_'.$user->lname.'_profile_pic'
			// $match_files = File::glob(public_path()."/images/temporary/".$input['user_id'].'_'.$user->fname.'_'.$user->lname.'_profile_pic_*');

			// if($match_files !== false) {
			// 	foreach($match_files as $profile_picture_tbd) {
			// 		if(file_exists($profile_picture_tbd)) {
			// 			File::delete($profile_picture_tbd);
			// 		}
			// 	}
			// }

			// // write into a new temp file
			// $dataPic = file_get_contents($pic);
			// file_put_contents(public_path().'/images/temporary/'.$newImageName, $dataPic);

			return $newImageName;
		}

		return 'Error';
	}

	// -- Admin Content Management - end

	// Admin Methods start here

	public function setAddToH(){

		$data = array();
		$data['org_school_id'] = Session::get('userinfo.school_id');

		if( Session::has('userinfo.aor_id') ){
			$data['aor_id']	   = Session::get('userinfo.aor_id');
		}
		$input = Request::all();

		if (isset($input['user_id'])) {
			$input['this_student'] = Crypt::decrypt($input['user_id']);
		}

		$user = User::on('bk')->find($input['this_student']);

		$today = Carbon::today();
		$now   = Carbon::now();

		$updated_at = $this->rand_date_time($today, $now);

		$rec = Recruitment::where('user_id', $input['this_student'])
						  ->where('college_id', $data['org_school_id']);

		if (isset($data['aor_id'])){
			$rec = $rec->where('aor_id', $data['aor_id']);
		}
		else{
			$rec = $rec->whereNull('aor_id');
		}

		$rec = $rec->update(array('user_recruit' => 1, 'college_recruit' => 1, 'updated_at' => $updated_at));

		if (isset($data['aor_id'])) {
			$rec = Recruitment::on('bk')->where('user_id', $input['this_student'])
						  				->where('college_id', $data['org_school_id'])
						  				->where('aor_id', $data['aor_id'])
						  				->first();

			$aor = Aor::find($data['aor_id']);

			// If this is a handshake contract
			if(isset($aor) && $aor->contract == 2 && $rec->college_recruit == 1){
				$this->chargeCollegePerInquiry($rec->id, null, $input['this_student'], $aor->id);
			// end of this is a hanshake contract.
			}
		}else{
			$ob = OrganizationBranch::where('school_id', $data['org_school_id'])->first();
			$rec = Recruitment::on('bk')->where('user_id', $input['this_student'])
						  				->where('college_id', $data['org_school_id'])
						  				->whereNull('aor_id')
						  				->first();

			if(isset($ob)){
				// If this is a handshake contract
				if($ob->contract == 2 && $rec->college_recruit == 1){
					$this->chargeCollegePerInquiry($rec->id, $ob->id, $input['this_student']);
				}
			}
		}

		$ntn = new NotificationController();
		$ntn->create( $user->fname. ' '. $user->lname, 'college', 2, null, $input['this_student'], $data['org_school_id'], $updated_at );

		return "success";
	}

    public function setRecruitForInquiriesOrConverted($recruitment_id = NULL, $recruit_bool = NULL, $is_remove = NULL) {
        $data = array();
        $data['org_branch_id'] = Session::get('userinfo.org_branch_id');
        $data['org_school_id'] = Session::get('userinfo.school_id');
        $data['is_aor']        = Session::get('userinfo.is_aor');
        $data['school_name']   = Session::get('userinfo.school_name');
        $data['user_id']       = Session::get('userinfo.id');
        $data['contract']      = Session::get('userinfo.contract');
        if( Session::has('userinfo.aor_id') ){
            $data['aor_id']    = Session::get('userinfo.aor_id');
        }

        if($recruitment_id == NULL){
            return 'invalid recruitment_id';
        }

        $recruitment = Recruitment::find($recruitment_id);

        if (isset($is_remove)) {
            $recruitment->status = 0;
            $recruitment->save();
        }else{
            $recruitment->college_recruit = $recruit_bool;
            $recruitment->save();
        }

        return 'success';
    }

	public function setRecruit($user_id = NULL, $recruit_bool = NULL, $is_remove = NULL){

		$data = array();
		$data['org_branch_id'] = Session::get('userinfo.org_branch_id');
		$data['org_school_id'] = Session::get('userinfo.school_id');
		$data['is_aor']		   = Session::get('userinfo.is_aor');
		$data['school_name']   = Session::get('userinfo.school_name');
		$data['user_id']   	   = Session::get('userinfo.id');
		$data['contract']	   = Session::get('userinfo.contract');
		if( Session::has('userinfo.aor_id') ){
			$data['aor_id']	   = Session::get('userinfo.aor_id');
		}

		if($user_id == NULL || $data['org_school_id'] == NULL){
			return "Invalid school or user id";
		}

		$recruitment = Recruitment::where('user_id', $user_id)
						->where('college_id', $data['org_school_id']);

		if(isset($data['aor_id'])){
			$recruitment = $recruitment->where('aor_id', '=', $data['aor_id'])
									   ->first();

		    $data['contract'] = Aor::on('rds1')
		    	->where('id', '=', $data['aor_id'])
		    	->pluck('contract');

		    $data['contract'] = $data['contract'][0];


		}else{
			$recruitment = $recruitment->whereNull('aor_id')
									   ->first();
		}

		if (isset($is_remove)) {
			$recruitment->status = 0;
			$recruitment->save();
		}else{
			$recruitment->college_recruit = $recruit_bool;
			$recruitment->save();
		}

		// If this is a handshake contract
		if($data['contract'] == 2 && $recruit_bool == 1){
			$cphl = CollegePaidHandshakeLog::on('bk')
										  ->where('recruitment_id', $recruitment->id)
										  ->first();
			if (!isset($cphl)) {
				if(isset($data['aor_id'])){
					$this->chargeCollegePerInquiry($recruitment->id, null, $user_id, $data['aor_id']);
				}
				else{
					$this->chargeCollegePerInquiry($recruitment->id, $data['org_branch_id'], $user_id);
				}
			}
		}
		// end if this is handshake contract

		// if this is a handshake
		if($recruit_bool != -1 && !isset($is_remove)){
			// add notification to the user
			$ntn = new NotificationController();
			$ntn->create( $data['school_name'], 'user', 2, null, $data['user_id'] , $user_id);

			// if user is coveted and college is priority, send handshake email
			$cu = CovetedUser::on('rds1')->where('user_id', $user_id)->count();

			$pc = Priority::on('rds1')->where('college_id', $data['org_school_id']);
			if(isset($data['aor_id'])){
				$pc = $pc->where('aor_id', $data['aor_id']);
			}else{
				$pc = $pc->whereNull('aor_id');
			}
			$pc = $pc->count();

			if(!empty($pc) && !empty($cu)){

				// SettingNotificationName ids
				$email_snn_id = 4;
				$text_snn_id  = 11;

				// if this person has not filtered email
				$email_snn = SettingNotificationLog::on('bk')
												   ->where('type', 'email')
												   ->where('user_id', $user_id)
												   ->where('snn_id', $email_snn_id)
												   ->first();

				$user = User::on('rds1')->find($user_id);

				if (!isset($email_snn)) {
					$mac = new MandrillAutomationController;
					$tmp = array();
					$tmp['college_id'] = $data['org_school_id'];
					$tmp['school_name'] = $data['school_name'];
					$tmp['fname'] = $user->fname;
					$tmp['email'] = $user->email;
					$mac->handshakeNextSteps($tmp);

				}

				// if this person has not filtered text
				$text_snn = SettingNotificationLog::on('bk')
											   ->where('type', 'text')
											   ->where('user_id', $user_id)
											   ->where('snn_id', $text_snn_id)
											   ->first();
				if(!isset($text_snn)){
					$tmp = array();
					$tmp['college_id'] = $data['org_school_id'];
					$tmp['school_name'] = $data['school_name'];

					// Send the text message.
					// $tc = new TwilioController;
					// $tc->sendHandshakeTxt($user, $tmp);
				}

			}
		}
	}

	public function setRecommendationRecruit($user_id = NULL, $recruit_bool = NULL){

		$data = array();
		$data['org_school_id'] = Session::get('userinfo.school_id');
		$data['is_aor']		   = Session::get('userinfo.is_aor');
		$data['school_name']   = Session::get('userinfo.school_name');
		$data['user_id']   	   = Session::get('userinfo.id');
		if( Session::has('userinfo.aor_id') ){
			$data['aor_id']	   = Session::get('userinfo.aor_id');
		}

		if($user_id == NULL || $data['org_school_id'] == NULL){
			return "Invalid school or user id";
		}

		if ($recruit_bool == -1) {
			$cr = CollegeRecommendation::where('college_id', $data['org_school_id'])
									   ->where('user_id', $user_id);
		    if(isset($data['aor_id'])){
		    	$cr = $cr->where('aor_id',$data['aor_id']);
	    	}
	    	else{
	    		$cr = $cr->whereNull('aor_id');
	    	}
			$cr = $cr->update(array('active' => -1));
		}else{

			$attr = array('user_id' => $user_id, 'college_id' => $data['org_school_id'] );

			$val = array('user_id' => $user_id, 'college_id' => $data['org_school_id'],
						 'user_recruit' => 0, 'college_recruit' => $recruit_bool,
						 'reputation' => 0,  'location' => 0, 'tuition' => 0,
						 'program_offered' => 0, 'athletic' => 0, 'onlineCourse' => 0,
						 'religion' => 0, 'campus_life' => 0, 'status' => 1,
						 'type' => 'recommendation');

			if (isset($data['aor_id'])){
				$attr['aor_id'] = $data['aor_id'];
			}
			else{
				$attr['aor_id'] = null;
			}

			$tmp = Recruitment::updateOrCreate($attr, $val);

			$cr = CollegeRecommendation::where('college_id', $data['org_school_id'])
									   ->where('user_id', $user_id);
		    if(isset($data['aor_id'])){
		    	$cr = $cr->where('aor_id',$data['aor_id']);
	    	}
	    	else{
				$cr = $cr->whereNull('aor_id');
			}
			$cr = $cr->update(array('active' => 0));


			////*************Add Notification to the user *********************///////
			$ntn = new NotificationController();

			$saved_ntn = $ntn->create( $data['school_name'], 'user', 3, null, $data['user_id'] , $user_id);

			$ntn_data['thread_room'] = $this->hashIdForSocial($user_id);
			
			$ntn = DB::connection('rds1')->table('notification_topnavs')->find($saved_ntn);
			$ntn = json_encode($ntn);
	        $ntn = json_decode($ntn);
    	    $ntn = $this->hashALoopNotAnArray($ntn);

	        $ntn_data['notification_data'] = $ntn;
      		Redis::publish('push:notification', json_encode($ntn_data));


		}
	}

	public function setRestore($user_id = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if($user_id == NULL || $data['org_school_id'] == NULL){
			return "Invalid school or user id";
		}


		$recruitment = Recruitment::where('user_id', $user_id)
						->where('college_id', $data['org_school_id'])
						->first();
		$recruitment->status = 1;
		$recruitment->save();
	}

    public function setRestoreToInquiries($user_id = NULL) {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        if($user_id == NULL || $data['org_school_id'] == NULL){
            return "Invalid school or user id";
        }

        $recruitment = Recruitment::where('user_id', $user_id)
                        ->where('college_id', $data['org_school_id'])
                        ->first();

        $recruitment->user_recruit = 1;
        $recruitment->college_recruit = 0;
        $recruitment->status = 1;
        $recruitment->save();
    }

	public function setRecruitmentNote(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (!isset($input['note']) && !isset($input['user_id'])) {
			return 'Error! #1091';
		}

		$note = $input['note'];
		$user_id = $input['user_id'];

		$rec = Recruitment::where('user_id', $user_id)
							->where('college_id', $data['org_school_id'])
							->update(array('note' => $note, 'updated_at' => date( "Y-m-d H:i:s" )));

		$arr = $this->iplookup();

		if (isset($arr['time_zone'])) {
			// date_default_timezone_set($arr['time_zone']);
			$now = Carbon::now($arr['time_zone']);
			return $now->toTimeString();
		}

		return date('H:i');
	}

    public function saveRecruitmentNote() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $input = Request::all();

        if (!isset($input['note']) && !isset($input['user_id'])) {
            return 'Error';
        }

        $attributes = [
            'creator_user_id' => $data['user_id'],
            'org_branch_id' => $data['org_branch_id'],
            'student_user_id' => $input['user_id'],
        ];

        $values = [
            'note' => $input['note'],
            'creator_user_id' => $data['user_id'],
            'org_branch_id' => $data['org_branch_id'],
            'student_user_id' => $input['user_id'],
        ];

        $query = CrmNotes::updateOrCreate($attributes, $values);

        return $this->xTimeAgo($query->updated_at, date("Y-m-d H:i:s"));
    }

	public function setAdvancedSearchNote(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (!isset($input['note']) && !isset($input['user_id'])) {
			return 'Error! #1091';
		}

		$note = $input['note'];
		$user_id = $input['user_id'];

		$attr = array('user_id' => $user_id);
		$val  = array('user_id' => $user_id, 'note' => $note);

		$asn = AdvancedSearchNote::updateOrCreate($attr, $val);

		$arr = $this->iplookup();

		if (isset($arr['time_zone'])) {
			// date_default_timezone_set($arr['time_zone']);
			$now = Carbon::now($arr['time_zone']);
			return $now->toTimeString();
		}

		return date('H:i');
	}

	public function sendAdminUrgentMatterMsg(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$input['COLLNAME']   = $data['school_name'];
		$rep_name 			 = $data['fname'] . ' ' . $data['lname'];
		$input['REP_NAME']   = ucwords(strtolower($rep_name));
		$input['MESSAGEPRV'] = $input['msg'];
		$input['REP_EMAIL']  = $data['email'];
		$input['REP_PHONE']  = $data['phone'];


		$mac = new MandrillAutomationController;
		$mac->sendUrgentAdminEmail($input);

		return "success";
	}

	public function sendAgencyUrgentMatterMsg(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$input['AGENCY_NAME']   = $data['agency_collection']->name;
		$rep_name 			 = $data['fname'] . ' ' . $data['lname'];
		$input['REP_NAME']   = ucwords(strtolower($rep_name));
		$input['MESSAGEPRV'] = $input['msg'];
		$input['REP_EMAIL']  = $data['email'];
		$input['REP_PHONE']  = $data['agency_collection']->phone;

		$mac = new MandrillAutomationController;
		$mac->sendUrgentAgencyEmail($input);

		return "success";
	}

	public function dismissPlexussAnnouncement(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (Cache::has(env('ENVIRONMENT') .'_dismissPlexussAnnouncement_'.$data['user_id'])) {
			$arr = Cache::get(env('ENVIRONMENT') .'_dismissPlexussAnnouncement_'.$data['user_id']);
			$arr[] = $input['id'];

			Cache::forever(env('ENVIRONMENT') .'_dismissPlexussAnnouncement_'.$data['user_id'], $arr);
		}else{
			$arr   = array();
			$arr[] = $input['id'];

			Cache::forever(env('ENVIRONMENT') .'_dismissPlexussAnnouncement_'.$data['user_id'], $arr);
		}

		return "success";
	}
	// Admin methods ends here

	// Agency methods begins here

	/**
	 * This method returns agency approval
	 *
	 * @return view
	 */
	public function agencyApproval($token = null){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$data['currentPage'] = 'agency-approval';

		if ($token == null) {
			return redirect( '/home' );
		}

		$token = urlencode($token);

		$ar = AgencyRecruitment::join('agency as a', 'a.id', '=', 'agency_recruitment.agency_id')
								->where('agency_recruitment.token', $token)->first();

		if ($ar == null) {
			return redirect( '/home' );
		}

		$data['agency'] = $ar;

		$data['thread_id'] = -1;

		$cmt = CollegeMessageThreadMembers::where('user_id', $data['user_id'])
				->where('agency_id', $ar->agency_id)
				->first();
		if (isset($cmt)) {
			$data['thread_id'] = $cmt->thread_id;
		}

		//Payment section
		$this->chargeAgencyPerInquiry($ar, $data['user_id']);

		return View('agency.agencyApproval', $data);
	}

	/**
	 * This method returns agency inquiry
	 *
	 * @return view
	 */
	public function agencyUserInquiry($token = null){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$data['currentPage'] = 'agency-approval';

		if ($token == null) {
			return redirect( '/home' );
		}

		$token = Crypt::decrypt(urldecode($token));

		//Payment section
	    $agency = Agency::find($token);
		// $paid = 0;
		// $balance = $agency->balance;

		// if($agency->is_trial_period == 1){
		// 	$paid = 1;
		// 	$cost = 0;
		// }else{

		// 	$balance = $agency->balance - $agency->cost_per_approved;
		// 	$cost = $agency->cost_per_approved;

		// 	if ($balance >= 0) {
		// 		$paid = 1;
		// 	}else{
		// 		$paid = 0;
		// 	}
		// }

		$attr = array('user_id' => $data['user_id'], 'agency_id' => $token);
		$val  = array('user_id' => $data['user_id'], 'agency_id' => $token,
					'user_recruit' => 1, 'agency_recruit' => 0, 'active' => 1,
					'paid' => 0);


		$ar = AgencyRecruitment::updateOrCreate($attr, $val);
		$ar->name = $agency->name;

		$data['agency'] = $ar;


		$data['thread_id'] = -1;

		$cmt = CollegeMessageThreadMembers::where('user_id', $data['user_id'])
				->where('agency_id', $ar->agency_id)
				->first();
		if (isset($cmt)) {
			$data['thread_id'] = $cmt->thread_id;
		}

		////*************Add Notification to the user *********************///////
		$ntnv = NotificationTopNav::where('type', 'agency')
									->where('name', $data['fname']. ' '. $data['lname'])
									->where('submited_id', $data['user_id'])
									->where('type_id', $agency->id)
									->first();
		if (!isset($ntnv)) {
			$ntn = new NotificationController();
			$ntn->create( $data['fname']. ' '. $data['lname'], 'agency', 1, null, $data['user_id'] , $agency->id);
		}

		return View('agency.agencyApproval', $data);
	}
	// Agency methods ends here

	/**
	 * This method returns an excel file containing information of the students in the admin's/agent's approved list
	 *
	 * @return excel file
	 */
	// -- export students into csv
	public function exportApprovedStudentsFile($admintype = null, $data = null){

		if( $admintype == null ){
			return 'failed';
		}

		if (isset($data)) {
			$input = $data['input'];
			$data = $data['data'];
			$store = true;
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$input = Request::all();
			$store = NULL;
			$input['user_id'] = $data['user_id'];
			$input['fname'] = $data['fname'];
			$input['email'] = $data['email'];
			$input['org_school_id'] = $data['org_school_id'];
			$input['org_branch_id'] = $data['org_branch_id'];
			$data['all_inputs'] = $input;
		}

		$currentPage = null;
		if (isset($input['currentPage'])) {
			$currentPage = $input['currentPage'];
		}

		$data['rec_date'] = explode(" to ", $input['date']);
		$data['unformatted_rec_date'] = array();
		$data['rec_date'][0] = str_replace('/', '-', $data['rec_date'][0]);
		$data['unformatted_rec_date'][0] = $data['rec_date'][0];
		$temp_date = explode("-", $data['rec_date'][0]);
		$data['rec_date'][0] = $temp_date[2]. '-'. $temp_date[0]. '-'. $temp_date[1].' 00:00:00';

		$data['rec_date'][1] = str_replace('/', '-', $data['rec_date'][1]);
		$data['unformatted_rec_date'][1] = $data['rec_date'][1];
		$temp_date = explode("-", $data['rec_date'][1]);
		$data['rec_date'][1] = $temp_date[2]. '-'. $temp_date[0]. '-'. $temp_date[1].' 23:59:59';

		if (isset($data['org_school_id']) &&  $data['org_school_id'] != '') {
			$type = 'college';
			$type_id = $data['org_school_id'];

			$data['paid_member'] = true;
		}else{
			$type = 'agency';
			$type_id = $data['agency_collection']->agency_id;

			if($data['agency_collection']->is_trial_period == 1 || $data['agency_collection']->balance > 0){

				$data['paid_member'] = true;

			}else{
				$data['paid_member'] = false;
			}
		}
		$export_fields = ExportField::all();
		$export_fields_exclusion = ExportFieldExclusion::select('export_field_id')
														->where('type', $type)
														->where('type_id', $type_id)
														->get()
														->toArray();
		$user_export_fields = '';
		$user_export_field_names = array();
		$user_export_field_ids = array();

		foreach ($export_fields as $key) {

			if (!$data['paid_member'] && ($key->id == 2 || $key->id == 3 || $key->id == 4)) {
				continue;
			}

			if ($this->get_index_multidimensional_boolean($export_fields_exclusion, 'export_field_id', $key->id) === false
				 && isset($input[$key->id])) {

				if (isset($key->select_field_name) && $key->select_field_name != '') {
					$user_export_fields .= $key->select_field_name.", ";
				}

				$user_export_field_names[] = $key->name;
				$user_export_field_ids[] = $key->id;
			}
		}


		$user_export_fields = 'IF(r.user_recruit = 1, "Handshake", "Pending") as type, ' .$user_export_fields;
		$user_export_fields = substr($user_export_fields, 0, -2);


		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$export_url = '';

		switch ($admintype) {
			case 'admin':
				$approvedList = $this->getStudentsListForAdmin($data, $user_export_fields);

				$approvedList_cnt = $approvedList->count();

				$approvedList = $approvedList->groupBy('u.id');
				//if approved list count is greater than 100, let cron job make excel file, save to aws, and email to user
				//else if less than 100, just process, create file, and return to user now
				if( $approvedList_cnt > 1000 && !isset($store) ){

					//if we already have exportsArr in cache, use this
					//else, create new empty arr
					if( Cache::has(env('ENVIRONMENT').'_export_info') ){
						$exportsArr = Cache::get(env('ENVIRONMENT').'_export_info');
					}else{
						$exportsArr = array();
					}

					$exportInfo = array();
					$exportInfo['user_id'] = $data['user_id'];
					$exportInfo['input'] = $data['all_inputs'];
					$exportInfo['has_run'] = 0;
					$exportInfo['adminType'] = $admintype;
					$exportInfo['data'] = $data;

					$exportsArr[] = $exportInfo;

					//save current export info to cache
					Cache::put(env('ENVIRONMENT').'_export_info', $exportsArr, 120);
					Cache::put(env('ENVIRONMENT').'_export_is_processing_msg_'.$data['user_id'], 1, 10);
					return redirect()->back();
				}else{
					DB::table('organization_branch_permissions')
						->where('user_id', $data['user_id'])
						->where('organization_branch_id', $data['org_branch_id'])
						->increment('export_file_cnt');

					$export_url = $this->exportToExcel($data, $approvedList, $user_export_field_names, $user_export_field_ids, $currentPage, $store);
				}
				break;

			case 'agent':
				$approvedList = $this->getApprovedListForAgency($data, $user_export_fields);
				$export_url = $this->exportToExcel($data, $approvedList, $user_export_field_names, $user_export_field_ids, $currentPage, $store);
				break;

			default:
				# code...
				return 'failed';
				break;
		}

		if (isset($store)) {
			return $export_url;
		}else{
			return Response::download();
		}

	}//-- end of export function

	public function saveExportsToEmailLater(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$exports = Cache::get(env('ENVIRONMENT').'_export_info');

		$i = 0;

		if( isset($exports) ){
			foreach ($exports as $key => $value) {
				if( $value['has_run'] == 0 ){
					$i++;
					$exports[$key]['has_run'] = 1; //make true
					Cache::put(env('ENVIRONMENT').'_export_info', $exports, 120); //update cache
					$this->exportApprovedStudentsFile($value['adminType'], $value); //save to aws
					unset($exports[$key]); //remove item from array when done saving file
					Cache::put(env('ENVIRONMENT').'_export_info', $exports, 120); //update cache again
				}
			}
		}

		return $i.' export requests processed and sent.';
	}

	private function getStudentsListForAdmin( $data, $user_export_fields) {

		DB::statement('SET SESSION group_concat_max_len = 1000000;');

		$recruitMeList = DB::connection('bk')
			->table('recruitment as r')
			->join('users as u', 'u.id', '=', 'r.user_id')
			->leftjoin('college_recommendations as cre', function($join)
			 {
			   $join->on('cre.user_id', '=', 'r.user_id');
			   $join->on('cre.college_id', '=', 'r.college_id');

			 })
			->leftjoin('scores as s', 's.user_id', '=','r.user_id')
			->leftjoin('colleges as c', 'c.id','=', 'u.current_school_id')
			->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')
			->leftjoin('objectives as o', 'o.user_id', '=', 'r.user_id')
			->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
			->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
			->leftjoin('professions as p', 'p.id', '=', 'o.profession_id')
			->leftjoin('countries as ctr', 'ctr.id', '=' , 'u.country_id')
			->leftjoin('recruitment as r2',function($join) use($data)
			 {
			    $join->on('r2.user_id', '=', 'u.id');
			    $join->on('r2.user_recruit', '=', DB::raw(1));
			    $join->on('r2.status', '=', DB::raw(1));
			    $join->on('r2.college_id', '!=', DB::raw($data['org_school_id']));
			 })
			->leftjoin('colleges as c2', 'r2.college_id', '=' , 'c2.id')
			->leftjoin('notification_topnavs as nt', function($join)
			 {
			    $join->on('nt.type_id', '=' , 'u.id');
			    $join->on('nt.type', '=', DB::raw("'user'"));
				$join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));

			 })
			->select(DB::raw($user_export_fields.', u.id as user_id'),
				DB::raw("GROUP_CONCAT(DISTINCT c2.school_name SEPARATOR ', ') AS competitor_colleges"))
			->where('r.college_id', '=', $data['org_school_id'])
			->where('r.status', 1)
			->where('r.college_recruit', 1)
			->where("r.updated_at", ">=", $data['rec_date'][0])
			->where("r.updated_at", "<=", $data['rec_date'][1])
			->orderBy('r.updated_at', 'desc')
			->orderBy('nt.type');

			if(isset($data['aor_id'])){
				$recruitMeList = $recruitMeList->where('r.aor_id', $data['aor_id']);
			}
			else{
				$recruitMeList = $recruitMeList->whereNull('r.aor_id');
			}

			//$recruitMeList = $recruitMeList->get();

		return $recruitMeList;
	}

	private function getApprovedListForAgency( $data, $user_export_fields ){

		DB::statement('SET SESSION group_concat_max_len = 1000000;');

		$recruitMeList = DB::connection('rds1')
			->table('agency_recruitment as r')

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
			->leftjoin('countries as ctr', 'ctr.id', '=' , 'u.country_id')
			->leftjoin('recruitment as r2',function($join) use($data)
			 {
			    $join->on('r2.user_id', '=', 'u.id');
			    $join->on('r2.user_recruit', '=', DB::raw(1));
			    $join->on('r2.status', '=', DB::raw(1));
			    $join->on('r2.college_id', '!=', DB::raw($data['org_school_id']));
			 })
			->leftjoin('colleges as c2', 'r2.college_id', '=' , 'c2.id')
			->leftjoin('notification_topnavs as nt', function($join)
			 {
			    $join->on('nt.type_id', '=' , 'u.id');
			    $join->on('nt.type', '=', DB::raw("'user'"));
				$join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));

			 })

			->select(DB::raw($user_export_fields.', u.id as user_id'),
				DB::raw("GROUP_CONCAT(DISTINCT c2.school_name SEPARATOR ', ') AS competitor_colleges"))
			->where('r.agency_id', '=', $data['agency_collection']->agency_id)
			->where('r.active', 1)
			->where('r.agency_recruit', 1)
			->where('r.user_recruit', 1)
			->where("r.created_at", ">=", $data['rec_date'][0])
			->where("r.created_at", "<=", $data['rec_date'][1])
			->where("r.paid", 1)
			->groupBy('u.id')
			->orderBy('r.created_at', 'desc')->get();

		return $recruitMeList;
	}

	private function exportToExcel($data, $approvedStudentList, $user_export_field_names, $user_export_field_ids, $currentPage, $store){
		if(!isset($currentPage)) {
			$currentPage = '';
		}
		//cell data
		$approvedStudentsData = array();
		$pendingStudentsData = array();
		//headers data
		$plex_header_row_num = 1;
		$plex_header_row = array('Plexuss');
		$header_row_num = 2;
		$header_row = $user_export_field_names;
		array_push($approvedStudentsData, $plex_header_row);
		array_push($approvedStudentsData, $header_row);
		array_push($pendingStudentsData, $plex_header_row);
		array_push($pendingStudentsData, $header_row);
		$alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZAAABACADAEAFAGAH';
		$alpha_array = array();
		for ($i=0; $i < strlen($alpha); $i++) {
			array_push($alpha_array, substr($alpha, $i, 1));
		}
		$plex_header_row_cells = $alpha_array[0].$plex_header_row_num.':'.$alpha_array[count($header_row) - 1].$plex_header_row_num;
		$freeze_cols = $alpha_array[0].$header_row_num;
		//push to studentData info of each student
		$approvedStudentList = $approvedStudentList->get();

		foreach ($approvedStudentList as $key) {

			$fname = !isset($key->fname) ? 'N/A' : $key->fname;
			$lname = !isset($key->lname) ? 'N/A' : $key->lname;
			$email = !isset($key->email) ? 'N/A' : $key->email;
			$phone = !isset($key->phone) ? 'N/A' : $key->phone;
			if(isset($key->in_college) && $key->in_college == 1){

				$in_college = "Yes";
				$college_name = !isset($key->userCollegeName) ? 'N/A' : $key->userCollegeName;
				$college_grad_year = !isset($key->college_grad_year) ? 'N/A' : $key->college_grad_year;
				$college_city = !isset($key->userCollegeCity) ? 'N/A' : $key->userCollegeCity;
				$college_state = !isset($key->userCollegeState) ? 'N/A' : $key->userCollegeState;
				$highSchool_name = 'N/A';
				$highSchool_grad_year = 'N/A';
				$highSchool_city = 'N/A';
				$highSchool_state = 'N/A';

			}else{
				$in_college = "No";
				$highSchool_name = !isset($key->highSchoolName) ? 'N/A' : $key->highSchoolName;
				$highSchool_grad_year = !isset($key->hs_grad_year) ? 'N/A' : $key->hs_grad_year;
				$highSchool_city = !isset($key->highSchoolCity) ? 'N/A' : $key->highSchoolCity;
				$highSchool_state = !isset($key->highSchoolState) ? 'N/A' : $key->highSchoolState;
				$college_name = 'N/A';
				$college_grad_year = 'N/A';
				$college_city = 'N/A';
				$college_state = 'N/A';
			}
			$financial = !isset($key->financial) ? 'N/A' : $key->financial;
			$birth_date = !isset($key->birth_date) ? 'N/A' : $key->birth_date;
			$applied   = (!isset($key->applied) || $key->applied == 0) ? '' : 'X';
			$enrolled  = (!isset($key->enrolled) || $key->enrolled == 0) ? '' : 'X';
			if (isset($key->overall_gpa) && $key->overall_gpa != 0) {
				$gpa = $key->overall_gpa;
			}elseif (isset($key->hs_gpa) && $key->hs_gpa != 0 && !isset($gpa)) {

				$gpa = $key->hs_gpa;
			}elseif (isset($key->weighted_gpa) && $key->weighted_gpa != 0 && !isset($gpa)) {

				$gpa = $key->weighted_gpa;
			}elseif (isset($key->max_weighted_gpa) && $key->max_weighted_gpa != 0 && !isset($gpa)) {

				$gpa = $key->max_weighted_gpa;
			}else{
				$gpa = 'N/A';
			}
			$sat_total = !isset($key->sat_total) ? 'N/A' : $key->sat_total;
			$act_composite = !isset($key->act_composite) ? 'N/A' : $key->act_composite;
			$toefl_total = !isset($key->toefl_total) ? 'N/A' : $key->toefl_total;
			$ielts_total = !isset($key->ielts_total) ? 'N/A' : $key->ielts_total;
			$country_name = !isset($key->country_name) ? 'N/A' : $key->country_name;
			$degreeName = !isset($key->degreeName) ? 'N/A' : $key->degreeName;
			$majorName = !isset($key->majorName) ? 'N/A' : $key->majorName;
			$profession_name = !isset($key->profession_name) ? 'N/A' : $key->profession_name;
			$created_at = !isset($key->created_at) ? 'N/A' : $key->created_at;
			$rec_type   =  !isset($key->type) ? 'N/A' : $key->type;
			$competitor_colleges = !isset($key->competitor_colleges) ? 'N/A' : $key->competitor_colleges;
			$financial = !isset($key->financial_firstyr_affordibility) ? 'N/A' : $key->financial_firstyr_affordibility;
			$start_date = !isset($key->start_date) ? 'N/A' : $key->start_date;

			$student_detail_data = array();
			if (in_array(1, $user_export_field_ids)) {
				array_push($student_detail_data, $rec_type);
			}
			if (in_array(2, $user_export_field_ids)) {
				array_push($student_detail_data, $fname);
			}
			if (in_array(3, $user_export_field_ids)) {
				array_push($student_detail_data, $lname);
			}
			if (in_array(4, $user_export_field_ids)) {
				array_push($student_detail_data, $applied);
			}
			if (in_array(5, $user_export_field_ids)) {
				array_push($student_detail_data, $enrolled);
			}
			if (in_array(6, $user_export_field_ids)) {
				array_push($student_detail_data, $birth_date);
			}
			if (in_array(7, $user_export_field_ids)) {
				array_push($student_detail_data, $email);
			}
			if (in_array(8, $user_export_field_ids)) {
				array_push($student_detail_data, $phone);
			}
			if (in_array(9, $user_export_field_ids)) {
				array_push($student_detail_data, $start_date);
			}
			if (in_array(10, $user_export_field_ids)) {
				array_push($student_detail_data, $financial);
			}
			if (in_array(11, $user_export_field_ids)) {
				array_push($student_detail_data, $in_college);
			}
			if (in_array(12, $user_export_field_ids)) {
				array_push($student_detail_data, $highSchool_grad_year);
			}
			if (in_array(13, $user_export_field_ids)) {
				array_push($student_detail_data, $college_grad_year);
			}
			if (in_array(14, $user_export_field_ids)) {
				array_push($student_detail_data, $financial);
			}
			if (in_array(15, $user_export_field_ids)) {
				array_push($student_detail_data, $gpa);
			}
			if (in_array(16, $user_export_field_ids)) {
				array_push($student_detail_data, $sat_total);
			}
			if (in_array(17, $user_export_field_ids)) {
				array_push($student_detail_data, $act_composite);
			}
			if (in_array(18, $user_export_field_ids)) {
				array_push($student_detail_data, $toefl_total);
			}
			if (in_array(19, $user_export_field_ids)) {
				array_push($student_detail_data, $ielts_total);
			}
			if (in_array(20, $user_export_field_ids)) {
				array_push($student_detail_data, $college_name);
			}
			if (in_array(21, $user_export_field_ids)) {
				array_push($student_detail_data, $college_city);
			}
			if (in_array(22, $user_export_field_ids)) {
				array_push($student_detail_data, $college_state);
			}
			if (in_array(23, $user_export_field_ids)) {
				array_push($student_detail_data, $highSchool_name);
			}
			if (in_array(24, $user_export_field_ids)) {
				array_push($student_detail_data, $highSchool_city);
			}
			if (in_array(25, $user_export_field_ids)) {
				array_push($student_detail_data, $highSchool_state);
			}
			if (in_array(26, $user_export_field_ids)) {
				array_push($student_detail_data, $country_name);
			}
			if (in_array(27, $user_export_field_ids)) {
				array_push($student_detail_data, $degreeName);
			}
			if (in_array(28, $user_export_field_ids)) {
				array_push($student_detail_data, $majorName);
			}
			if (in_array(29, $user_export_field_ids)) {
				array_push($student_detail_data, $profession_name);
			}
			if (in_array(30, $user_export_field_ids)) {
				array_push($student_detail_data, $competitor_colleges);
			}
			if (in_array(31, $user_export_field_ids)) {
				array_push($student_detail_data, $created_at);
			}

			// the actual data
			if( $rec_type == 'Pending' ) {
				array_push($pendingStudentsData, $student_detail_data);
			} else if ( $rec_type == 'Handshake') {
				array_push($approvedStudentsData, $student_detail_data);
			}

		}

		// dd($pendingStudentsData);
		// dd($approvedStudentsData);
		// creating object to store all necessary info to use in creating excel
		$excelData = new \stdClass();
		$excelData->pendingStudentsData = $pendingStudentsData;
		$excelData->approvedStudentsData = $approvedStudentsData;
		$excelData->headerRowNum = $header_row_num;
		$excelData->plexHeaderRowNum = $plex_header_row_num;
		$excelData->plexHeaderCells = $plex_header_row_cells;
		$excelData->freezeColumns = $freeze_cols;
		$excelData->currentPage = $currentPage;
		// creating excel file for exporting, passing studentData
		if (isset($data['school_name'])) {
			$filename = str_replace(" ", "_", $data['school_name']).'_Plexuss_Approved_Student_List_'.$data['unformatted_rec_date'][0].'_to_'.$data['unformatted_rec_date'][1];
		}else{
			$filename = 'Plexuss_Approved_Student_List_'.$data['unformatted_rec_date'][0].'_to_'.$data['unformatted_rec_date'][1];
		}

		$exp = \Excel::create($filename, function($excel) use($excelData){
			//set title, creator, company, and description
			$excel->setTitle('Approved Students Information')
				  ->setCreator('Plexuss')
				  ->setCompany('Plexuss')
				  ->setDescription('This file contains the information of the list of students in your Approved section.');
			//creating data sheet
			if($excelData->currentPage == 'admin-approved') {
				$excel->sheet('Approved Students', function($sheet) use($excelData){
					//populate cells with data
					$sheet->fromArray($excelData->approvedStudentsData, null, 'A1', true, false);
					//manipulating row 1 - plex header row
					$sheet->row($excelData->plexHeaderRowNum, function($row){
								$row->setBackground('#202020')
									->setFontColor('#ffffff')
									->setFontWeight('bold')
									->setFontSize(14);
							})
						  ->mergeCells($excelData->plexHeaderCells)
						  ->cell('A1', function($cell){
						  		$cell->setAlignment('center');
						  	});
					//manipulating row 2 - header row
					$sheet->row($excelData->headerRowNum, function($row){
						$row->setBackground('#FF5C26')
							->setFontColor('#ffffff')
							->setFontWeight('bold');
					});
					//freeze header row
					// $sheet->setFreeze($excelData->freezeColumns);
				});
			} else if ($excelData->currentPage == 'admin-pending') {
				$excel->sheet('Pending Students', function($sheet) use($excelData){
					//populate cells with data
					$sheet->fromArray($excelData->pendingStudentsData, null, 'A1', true, false);
					//manipulating row 1 - plex header row
					$sheet->row($excelData->plexHeaderRowNum, function($row){
								$row->setBackground('#202020')
									->setFontColor('#ffffff')
									->setFontWeight('bold')
									->setFontSize(14);
							})
						  ->mergeCells($excelData->plexHeaderCells)
						  ->cell('A1', function($cell){
						  		$cell->setAlignment('center');
						  	});
					//manipulating row 2 - header row
					$sheet->row($excelData->headerRowNum, function($row){
						$row->setBackground('#FF5C26')
							->setFontColor('#ffffff')
							->setFontWeight('bold');
					});
					//freeze header row
					// $sheet->setFreeze($excelData->freezeColumns);
				});
			} else {

				$excel->sheet('Approved Students', function($sheet) use($excelData){
					//populate cells with data
					$sheet->fromArray($excelData->approvedStudentsData, null, 'A1', true, false);
					// for ($i=10; $i <count($excelData->approvedStudentsData) ; $i++) {
					// 	$sheet->row($i, $excelData->approvedStudentsData[$i]);
					// }
					//manipulating row 1 - plex header row
					$sheet->row($excelData->plexHeaderRowNum, function($row){
								$row->setBackground('#202020')
									->setFontColor('#ffffff')
									->setFontWeight('bold')
									->setFontSize(14);
							})
						  ->mergeCells($excelData->plexHeaderCells)
						  ->cell('A1', function($cell){
						  		$cell->setAlignment('center');
						  	});
					//manipulating row 2 - header row
					$sheet->row($excelData->headerRowNum, function($row){
						$row->setBackground('#FF5C26')
							->setFontColor('#ffffff')
							->setFontWeight('bold');
					});
					//freeze header row
					// $sheet->setFreeze($excelData->freezeColumns);
				});

				$excel->sheet('Pending Students', function($sheet) use($excelData){
					//populate cells with data
					$sheet->fromArray($excelData->pendingStudentsData, null, 'A1', true, false);

					// for ($i=10; $i <count($excelData->pendingStudentsData) ; $i++) {
					// 	$sheet->row($i, $excelData->pendingStudentsData[$i]);
					// }
					//manipulating row 1 - plex header row
					$sheet->row($excelData->plexHeaderRowNum, function($row){
								$row->setBackground('#202020')
									->setFontColor('#ffffff')
									->setFontWeight('bold')
									->setFontSize(14);
							})
						  ->mergeCells($excelData->plexHeaderCells)
						  ->cell('A1', function($cell){
						  		$cell->setAlignment('center');
						  	});
					//manipulating row 2 - header row
					$sheet->row($excelData->headerRowNum, function($row){
						$row->setBackground('#FF5C26')
							->setFontColor('#ffffff')
							->setFontWeight('bold');
					});
					//freeze header row
					// $sheet->setFreeze($excelData->freezeColumns);
				});
			}
		});

		if (isset($store)) {
			// dd('in saving!!!');
			// $exp = $exp->store('xls', storage_path('excel'));
			// return $exp->filename.".xls";
			//save to app/storage/exports folder locally
			$exp->store('xls', storage_path('/excel/exports'));
			$exportFilename = storage_path('/excel/exports/'.$exp->filename.'.xls');
			//find locally stored excel file and save to aws
			$s3 = AWS::get('s3');
			$s3->putObject(array(
				'ACL' => 'public-read',
				'Bucket' => 'asset.plexuss.com/admin/exports',
				'Key' => $exp->filename.'.xls',
				'SourceFile' => $exportFilename
			));
			//remove file from local storage after saving to aws
			if( \File::exists( $exportFilename ) ){
				\File::delete( $exportFilename );
			}
			$input = array();
			$input['fname'] = $data['fname'];
			$input['link'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/exports/'.$exp->filename.'.xls';
			$input['email'] = $data['email'];
			//emailing uploaded excel file to user
			$man = new MandrillAutomationController;
			$man->exportAFileForColleges( $input );
			return $input['link'];
		}else{
			$exp->export('xls');
			return 'success';
		}

	}
	// -- end of export approved list to excel functions


	// -- saving applied student  - start
	public function saveAppliedStudent(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		$user_id = Crypt::decrypt($input['id']);

		$now = Carbon::now()->toDateTimeString();
		//if type is agency, save to agencyrecruitment, otherwise save to recruitment table
		if( $input['type'] == 'agency' ){
			$agency_id = $data['agency_collection']->agency_id;
			$agency_rec = AgencyRecruitment::where('user_id', '=', $user_id)->where('agency_id', '=', $agency_id)->update(array('applied' => $input['applied'], 'applied_at' => $now));
		}else{
			$rec = Recruitment::where('user_id', '=', $user_id)->where('college_id', '=', $data['org_school_id'])->update(array('applied' => $input['applied'], 'applied_at' => $now));
		}

		return 'success';
	}
	// -- saving applied student  - end

	// -- saving enrolled student  - start
	public function saveEnrolledStudent() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$user_id = Crypt::decrypt($input['id']);

		$now = Carbon::now()->toDateTimeString();
		if($input['type'] == 'agency'){
			$agency_id = $data['agency_collection']->agency_id;
			$agency_rec = AgencyRecruitment::where('user_id', '=', $user_id)->where('agency_id', '=', $agency_id)->update(array('enrolled' => $input['enrolled'], 'enrolled_at' => $now));
		}else{
			$rec = Recruitment::where('user_id', '=', $user_id)->where('college_id', '=', $data['org_school_id'])->update(array('enrolled' => $input['enrolled'], 'enrolled_at' => $now));
		}
		return 'success';
	}
	// -- saving enrolled student  - end

	// -- applied student reminder - start
	public function appliedReminder(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		//if type is agency, save to agencyrecruitment, otherwise save to recruitment table
		if( $input['type'] == 'agency' ){
			$user_id = $data['agency_collection']->user_id;
			$agency_perm = DB::table('agency_permissions')->where('user_id', '=', $user_id)->update(array('applied_reminder' => $input['remind_next_month']));
		}else{
			$org_branch_perm = DB::table('organization_branch_permissions')->where('user_id', '=', $data['user_id'])->update(array('applied_reminder' => $input['remind_next_month']));
		}

		return 'success';
	}
	// -- applied student reminder - end

	// -- applied student remind me later - start
	public function appliedRemindMeLater(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if( $input['type'] == 'agency' ){
			$user_id = $data['agency_collection']->user_id;
		}else{
			$user_id = $data['user_id'];
		}

		Cache::put(env('ENVIRONMENT').'_'.$user_id.'_applied_remind_me_later', $input['remind_me_later'], 40320);

		return 'success';
	}
	// -- applied student remind me later - end

	// -- text message remind me later - start
	public function textmsgRemindMeLater() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if( $input['type'] == 'agency' ){
			$user_id = $data['agency_collection']->user_id;
		}else{
			$user_id = $data['user_id'];
		}

		Cache::put(env('ENVIRONMENT').'_'.$user_id.'_textmsg_remind_me_later', $input['textmsg_remind'], 120);

		return 'success';
	}

	public function getMessageTemplatesList(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
		$data = $this->getMessageTemplates($data, true);
		return $data['message_template'];
	}

	// -- text message remind me later - end
	/**
	 * saveMessageTemplates
	 *
	 * saves the message template for the college or agency
	 *
	 * @return null
	 */
	public function saveMessageTemplates(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (!isset($input['name']) || empty($input['name']) || !isset($input['content']) || empty($input['content'])) {
			return "failed";
		}

		if (isset($data['agency_collection'])) {
			$agency_id = $data['agency_collection']->agency_id;
			$college_id = NULL;

		}else{
			$college_id = $data['org_school_id'];
			$agency_id = NULL;
		}

		//if saving from admin/messages
		if( isset($input['id']) && $input['id'] != ''){
			$decryptedId = Crypt::decrypt($input['id']);
			$attr = array('id' => $decryptedId);
		}else{
			//else coming from admin/campaign<div></div>s
			$attr = array('name' => $input['name'], 'agency_id' => $agency_id, 'college_id' => $college_id);
		}

		$val  = array('name' => $input['name'], 'agency_id' => $agency_id, 'college_id' => $college_id, 'content' => $input['content']);

		$mt = MessageTemplate::updateOrCreate($attr, $val);

		$arr = array();
		$arr['id'] = Crypt::encrypt($mt->id);
		$arr['name'] = $input['name'];
		$arr['content'] = $input['content'];

		return $arr;
	}


	/**
	 * loadMessageTemplates
	 *
	 * load content of a message template for the college or agency
	 *
	 * @return null
	 */
	public function loadMessageTemplates(){

		$input = Request::all();

		if (!isset($input['id']) || empty($input['id'])) {
			return "failed";
		}

		$mt = MessageTemplate::find(Crypt::decrypt($input['id']));
		$arr = array();

		$arr['content'] = $mt->content;

		if (isset($input['txtOnly'])) {
			$arr['content'] = strip_tags($mt->content);
		}

		return $arr;
	}

	/**
	 * loadMessageTemplates
	 *
	 * load content of a message template for the college or agency
	 *
	 * @return null
	 */
	public function deleteMessageTemplates(){

		$input = Request::all();

		if (!isset($input['id']) || empty($input['id'])) {
			return "failed";
		}

		$mt = MessageTemplate::find(Crypt::decrypt($input['id']));
		$deleted = $mt->delete();

		return $deleted ? 'success' : 'failed';
	}

	public function lightbox($id = null){
		if( $id == null ){
			return 'fail';
		}

		$article = NewsArticle::find($id);
		$data['script'] = $article['img_lg'];
		return View('private.news.youniversitytv_lightbox', $data);
	}

	//same sort of function as the above lightbox function but for college pages
	public function collegeYouniversity($id = null){
		if( $id == null ){
			return 'fail';
		}

		$media = CollegeOverviewImages::find($id);
		$data['script'] = $media['url'];
		return View('private.news.youniversitytv_lightbox', $data);
	}

	////////************ Setting Methods ****************/////////

	// public function getMessageTemplatesList(){
	// 	$viewDataController = new ViewDataController();
	// 	$data = $viewDataController->buildData(true);
	// 	$data = $this->getMessageTemplates($data, true);
	// 	return $data['message_template'];
	// }

	/**
	 * setOrgnizationPortal
	 *
	 * This method sets the organization portal for the current user.
	 *
	 * @return null
	 */
	public function setOrgnizationPortal(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (!isset($input['hashedId'])) {
			return "Error!";
		}

		$id = $input['hashedId'];

		try {
			$id = Crypt::decrypt($id);
		} catch (\Exception $e) {
			return "Error!";
		}
		$opu = OrganizationPortalUser::where('user_id', $data['user_id'])->update(array('is_default' => 0));
		if ($id != -1) {
			$opu = OrganizationPortalUser::where('user_id', $data['user_id'])
										 ->where('org_portal_id', $id)
										 ->update(array('is_default' => 1));

			if( !isset($opu) ){
				return 'error with getting org portal user';
			}

			$op = OrganizationPortal::find($id);
		}elseif( $id == -1 ){
			Session::put('userinfo.session_reset', 1);
			return 'General';
		}

		if( !isset($op) ){
			return 'error with getting org portal';
		}

		Session::put('userinfo.session_reset', 1);

		Cache::put(env('ENVIRONMENT') .'_'. $data['user_id'] . '_session_reset', 1, 60);

		return $op;
	}

	/**
	 * createEditPortal
	 *
	 * This method create/rename/delete a portal
	 *
	 * @return null
	 */
	public function createEditPortal(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		// dd($input);

		if (!isset($input['type']) && $input['type'] != 'create' && $input['type'] != 'rename' && $input['type'] != 'deactivate' && $input['type']!= 'reactivate' && $input['type'] != 'remove') {
			return "Error! wrong type";
		}

		if ($input['type'] == 'create') {

			$op = OrganizationPortal::where('org_branch_id', $data['org_branch_id'])
									->where('name', $input['name'])
									->first();

			if (isset($op) || strtolower($input['name']) ==  'general') {
				return "Error! Can not create the portal. Portal already exists.";
			}

			$op = new OrganizationPortal;

			$op->name = $input['name'];
			$op->org_branch_id = $data['org_branch_id'];
			$op->save();

			$super_admins = OrganizationBranchPermission::join('users as u', 'u.id', '=', 'organization_branch_permissions.user_id')
														->where('organization_branch_id', $data['org_branch_id'])
														->where('super_admin', 1)->select('u.id as user_id' ,'u.email')->get();

			// dd($super_admins);
			$ret = array();
			$ret['users'] = array();
			$ret['hashedportalid'] = Crypt::encrypt($op->id);
			foreach ($super_admins as $super_admin) {
				$tmp = array();
				$tmp['user_id'] = $super_admin->user_id;
				$tmp['hasheduserid'] = Crypt::encrypt($super_admin->user_id);
				$tmp['email'] = $super_admin->email;
				$tmp['super_admin'] = 1;

				$ret['users'][] = $tmp;

				$opu = OrganizationPortalUser::where('user_id', $super_admin->user_id)
											 ->where('org_portal_id', $op->id)
										     ->first();
				if($opu) {
					continue;
				}

				$opu = new OrganizationPortalUser;
				$opu->org_portal_id = $op->id;
				$opu->user_id = $super_admin->user_id;
				$opu->is_default = 0;
				$opu->save();

			}
			return json_encode($ret);

		}elseif ($input['type'] == 'rename') {

			try {
				$id = Crypt::decrypt($input['hashedid']);
			} catch (\Exception $e) {
				return "Error! wrong id";
			}

			$op = OrganizationPortal::where('org_branch_id', $data['org_branch_id'])
									->where('name', $input['name'])
									->first();
			if (isset($op)) {
				return "Error! Can not rename the portal. Please choose another name";
			}

			$op = OrganizationPortal::find($id);
			$op->name = $input['name'];
			$op->save();

		}elseif ($input['type'] == 'deactivate') {
			try {
				$id = Crypt::decrypt($input['hashedid']);
			} catch (\Exception $e) {
				return "Error! wrong id";
			}
			$ret = '';
			$op = OrganizationPortal::find($id);
			if ($op->active == 1) {
				$op->active = 0;
				$ret = 'deactivate';
			}else{
				$op->active = 1;
				$ret = 'activate';
			}

			$op->save();

			return $ret;
		}elseif ($input['type'] == 'remove') {
			try {
				$id = Crypt::decrypt($input['hashedid']);
			} catch (\Exception $e) {
				return "Error! wrong id";
			}

			$op = OrganizationPortal::findOrFail($id);

			$op->delete();

		}elseif( $input['type'] == 'reactivate'){
			try{
				$id = Crypt::decrypt($input['hashedid']);
			}catch(Exception $e) {
				return "Error! Wrong id";
			}
			$ret = '';
			$op = OrganizationPortal::find($id);
			if ( $op->active == 0 ) {
				$op->active = 1;
				$ret = 'reactive';
			} else {
				$op->active = 0;
				$ret = 'deactive';
			}
			$op->save();

			return $ret;
		}

		return "success";
	}

	/**
	 * addRemoveUsers
	 *
	 * This method create/remove users from a particular portal
	 *
	 * @return null
	 */
	public function addRemoveUsers(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (!isset($input['type']) && $input['type'] != 'add' && $input['type'] != 'delete') {
			return "Error! wrong type";
		}

		if ($input['type'] == 'add') {
			$super_admin = 0;

			if (isset($input['users_access']) && $input['users_access'] == 'Admin') {
				$super_admin = 1;
			}

			if( $super_admin == 0 ){
				try {
					$portal_id = Crypt::decrypt($input['hashedid']);
				} catch (\Exception $e) {
					return "Error! wrong id";
				}
			}

			$email = $input['email'];
			$email_arr = array();

			if (strpos($email, ',') !== false) {
				$email_arr = explode(",", $email);
			}else{
				$email_arr[] = $email;
			}

			$ret = array();

			foreach ($email_arr as $key => $value) {
				$value = trim($value);
				$user = User::where('email', $value)->first();

				if(!$user){
					$mac = new MandrillAutomationController;
					$mac->addNewUserFromPortal($value, $portal_id, $super_admin);
					//return "User doesn't exists!";
					//return "Success";

					$tmp = array();
					$tmp['hasheduserid'] = '';
					$tmp['email'] = $value;
					$tmp['super_admin'] = $super_admin;

					$ret[] = $tmp;
					continue;
				}

				$opu = OrganizationPortalUser::where('user_id', $user->id)
											 ->where('org_portal_id', $portal_id)
											 ->first();
				if ($opu) {
					//return "User is already in this portal";
					continue;
				}

				// set is organization to 1 in users table
				$user->is_organization = 1;
				$user->save();

				// update organization branch permissions with this person's id and organization branch id
				$attr = array('user_id' => $user->id, 'organization_branch_id' => $data['org_branch_id']);
				$val  = array('user_id' => $user->id, 'organization_branch_id' => $data['org_branch_id'], 'super_admin' => $super_admin);

				$obp = OrganizationBranchPermission::updateOrCreate($attr, $val);

				// Make all of the user portal default to 0 because if we have two or more is_default=1 for one user.
				// It would break the logic.
				$tmp_opu = OrganizationPortalUser::where('user_id', $user->id)
											 	 ->update(array('is_default' => 0));

				$opu = new OrganizationPortalUser;
				$opu->org_portal_id = $portal_id;
				$opu->user_id = $user->id;
				$opu->is_default = 1;

				$opu->save();

				$cmt = new CollegeMessageThreads;
				$cmt = $cmt->getThisCollegeChatThread($data['org_branch_id']);

				if (!isset($cmt)) {
					continue;
				}

				$chat_thread_id = $cmt->thread_id;

				$cmtm = new CollegeMessageThreadMembers;
				$cmtm->thread_id     = $chat_thread_id;
				$cmtm->user_id       = $user->id;
				$cmtm->org_branch_id = $data['org_branch_id'];

				$cmtm->save();

				$mac = new MandrillAutomationController;
				$mac->addPortalExistingUser($value, $portal_id);


				$tmp = array();
				$tmp['hasheduserid'] = Crypt::encrypt($user->id);
				$tmp['email'] = $user->email;
				$tmp['super_admin'] = $super_admin;

				$ret[] = $tmp;
			}

			Cache::put(env('ENVIRONMENT') .'_'. $data['user_id'] . '_session_reset', 1, 60);
			Session::put('userinfo.session_reset', 1);

			return json_encode($ret);
		}elseif ($input['type'] == 'delete') {

			try {
				$portal_id = Crypt::decrypt($input['hashedid']);
				$user_id = Crypt::decrypt($input['hasheduserid']);
			} catch (\Exception $e) {
				return "Error! wrong id";
			}

			$opu = OrganizationPortalUser::where('user_id', $user_id)
										 ->where('org_portal_id', $portal_id)
										 ->first();
			if (!$opu) {
				return "User doesn't exists to delete";
			}

			$opu->delete();
		}

		Cache::put(env('ENVIRONMENT') .'_'. $data['user_id'] . '_session_reset', 1, 60);
		Session::put('userinfo.session_reset', 1);

		return "Success";
	}

	public function saveSettingInfo(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$user_id = $input['targetted_user_id'];

		if (isset($input['users-access']) && $input['users-access'] == 'Admin') {
			$super_admin = 1;
		}else{
			$super_admin = 0;
		}

		$obp = OrganizationBranchPermission::where('user_id', $user_id)
										   ->where('organization_branch_id', $data['org_branch_id'])
										   ->update(array('super_admin' => $super_admin));

		$all_portals = OrganizationPortal::where('org_branch_id', $data['org_branch_id'])
										 ->get();

		$user = User::find($user_id);

		$user->fname = $input['fname'];
		$user->lname = $input['lname'];
		$user->save();

		foreach ($all_portals as $key) {

			$portal_name = trim($key->name);
			$portal_name = str_replace(" ", "_", $portal_name);

			if (isset($input['portal-'.$portal_name])) {

				$attr = array('org_portal_id' => $key->id, 'user_id' => $user_id);
				$val  = array('org_portal_id' => $key->id, 'user_id' => $user_id, 'is_default' => 0);

				$update = OrganizationPortalUser::updateOrCreate($attr, $val);
			} else {
				$opu = OrganizationPortalUser::where('user_id', $user_id)
											 ->where('org_portal_id', $key->id)
											 ->first();

				if ($opu) {
					$opu->delete();
				}

			}

		}

		Cache::put(env('ENVIRONMENT') .'_'. $data['user_id'] . '_session_reset', 1, 60);
		Session::put('userinfo.session_reset', 1);

		return "success";
	}

	public function addUserFromManageUser(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$portal_id = array();
		foreach ($input as $key => $value) {
			if (strpos($key, 'portal_name_') !== false) {
				$tmp_key = str_replace("portal_name_", "", $key);
				try {
					$portal_id[] = Crypt::decrypt($tmp_key);
				} catch (\Exception $e) {
					return "Error! wrong id";
				}
			}
		}

		$email = $input['users_name'];
		$email_arr = array();

		if (strpos($email, ',') !== false) {
			$email_arr = explode(",", $email);
		}else{
			$email_arr[] = $email;
		}

		$super_admin = 0;

		if (isset($input['users-access']) && $input['users-access'] == 'Admin') {
			$super_admin = 1;
		}

		$ret = array();

		foreach ($email_arr as $key => $value) {
			$value = trim($value);
			$user = User::where('email', $value)->first();

			if(!$user){
				$mac = new MandrillAutomationController;
				$mac->addNewUserFromPortal($value, $portal_id, $super_admin);
				//return "User doesn't exists!";
				//return "Success";

				$tmp = array();
				$tmp['hasheduserid'] = '';
				$tmp['email'] = $value;//$user->email;
				$tmp['super_admin'] = $super_admin;

				$ret[] = $tmp;
				continue;
			}

			// multiple portal_id should be injected
			$op = DB::connection('rds1')->table('organization_portals as op')
										->where('op.org_branch_id', $data['org_branch_id'])
										->select('op.id')->get();

			// set is organization to 1 in users table
			$user->is_organization = 1;
			$user->save();

			// update organization branch permissions with this person's id and organization branch id
			$attr = array('user_id' => $user->id, 'organization_branch_id' => $data['org_branch_id']);
			$val  = array('user_id' => $user->id, 'organization_branch_id' => $data['org_branch_id'], 'super_admin' => $super_admin);

			$obp = OrganizationBranchPermission::updateOrCreate($attr, $val);

			// Make all of the user portal default to 0 because if we have two or more is_default=1 for one user.
			// It would break the logic.
			$tmp_opu = OrganizationPortalUser::where('user_id', $user->id)
										 	 ->update(array('is_default' => 0));

			// Add the user to each of the following portals
			$is_default = 1;
			if (isset($portal_id) && empty($portal_id)) {

				$cmt = new CollegeMessageThreads;
				$cmt = $cmt->getThisCollegeChatThread($data['org_branch_id']);

				if (!isset($cmt)) {
					return;
				}

				$chat_thread_id = $cmt->thread_id;

				$cmtm = new CollegeMessageThreadMembers;
				$cmtm->thread_id     = $chat_thread_id;
				$cmtm->user_id       = $user->id;
				$cmtm->org_branch_id = $data['org_branch_id'];

				$cmtm->save();

				$is_default = 0;

				$mac = new MandrillAutomationController;
				$mac->addPortalExistingUser($user->email, -1);

			}else{

				if($super_admin == 0) {
					// user mode : add portal_id by selection
					foreach ($portal_id as $k => $v) {
						# code...
						$opu = OrganizationPortalUser::where('user_id', $user->id)
												 ->where('org_portal_id', $v)
												 ->first();
						if ($opu) {
							//return "User is already in this portal";
							continue;
						}

						$opu = new OrganizationPortalUser;
						$opu->org_portal_id = $v;
						$opu->user_id = $user->id;
						$opu->is_default = $is_default;

						$opu->save();

						$cmt = new CollegeMessageThreads;
						$cmt = $cmt->getThisCollegeChatThread($data['org_branch_id']);

						if (!isset($cmt)) {
							continue;
						}

						$chat_thread_id = $cmt->thread_id;

						$cmtm = new CollegeMessageThreadMembers;
						$cmtm->thread_id     = $chat_thread_id;
						$cmtm->user_id       = $user->id;
						$cmtm->org_branch_id = $data['org_branch_id'];

						$cmtm->save();

						$is_default = 0;

						$mac = new MandrillAutomationController;
						$mac->addPortalExistingUser($value, $v);
					}

				} else if($super_admin == 1){

					foreach ($op as $key) {
						$opu = OrganizationPortalUser::where('user_id', $user->id)
													 ->where('org_portal_id', $key->id)
													 ->first();
						if($opu) {
							continue;
						}

						$opu = new OrganizationPortalUser;
						$opu->org_portal_id = $key->id;
						$opu->user_id = $user->id;
						$opu->is_default = $is_default;
						$opu->save();

						$cmt = new CollegeMessageThreads;
						$cmt = $cmt->getThisCollegeChatThread($data['org_branch_id']);

						if (!isset($cmt)) {
							continue;
						}

						$chat_thread_id = $cmt->thread_id;

						$cmtm = new CollegeMessageThreadMembers;
						$cmtm->thread_id     = $chat_thread_id;
						$cmtm->user_id       = $user->id;
						$cmtm->org_branch_id = $data['org_branch_id'];

						$cmtm->save();

						$is_default = 0;

						$mac = new MandrillAutomationController;
						$mac->addPortalExistingUser($value, $key->id);
					}
				}
			}

			$tmp = array();
			$tmp['hasheduserid'] = Crypt::encrypt($user->id);
			$tmp['email'] = $user->email;
			$tmp['super_admin'] = $super_admin;

			$ret[] = $tmp;
		}

		Cache::put(env('ENVIRONMENT') .'_'. $data['user_id'] . '_session_reset', 1, 60);
		Session::put('userinfo.session_reset', 1);

		return json_encode($ret);
	}

	public function deleteUserFromOrganization(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$user_id = $input['targetted_user_id'];

		$user = User::find($user_id);
		$user->is_organization = 0;
		$user->save();

		$cmtm = CollegeMessageThreadMembers::where('user_id', $user_id)
										   ->where('org_branch_id', $data['org_branch_id'])
										   ->delete();

		$obp  = OrganizationBranchPermission::where('user_id', $user_id)
											->where('organization_branch_id', $data['org_branch_id'])
											->delete();

		$opu  = OrganizationPortalUser::where('user_id', $user_id)
									  ->delete();

		return "success";
	}

	////////************ Setting Methods Ends *************/////////

	public function getNumberOfHandshakes(){
		$rec = new Recruitment;
		$rec = $rec->getCountOfRecruitment();
		return $rec;
	}

	public function recruitMePls($schoolId = null){

		//$this->generateCollegeRecommendation();

		if(Auth::check()){

			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$id = Auth::id();
			$input = Request::all();

			$user = User::find($id);
			$data['selected'] = array();

			// list of required fields
			$data['fname'] = $user->fname;
			$data['lname'] = $user->lname;
			$data['email'] = $user->email;
			$data['address'] = $user->address;
			$data['city'] = $user->city;
			$data['state'] = $user->state;
			$data['zip'] = $user->zip;
			$data['phone'] = $user->phone;
			$data['txt_opt_in'] = $user->txt_opt_in;
			$data['is_intl_student'] = isset($user->is_intl_student) ? $user->is_intl_student : 0;
			$usrScores = DB::table('scores')
				->select('hs_gpa', 'sat_total', 'act_composite')
				->where('user_id', $user->id)
				->first();

			if ( isset($usrScores->hs_gpa) && $usrScores->hs_gpa != "" ) {
				$data['hs_gpa'] = $usrScores->hs_gpa;
			}else{
				$data['hs_gpa'] = "";
			}

			$obj = DB::table('objectives')
				->select('degree_type', 'major_id', 'profession_id')
				->where('user_id', $user->id)
				->first();


			if( !isset($obj->degree_type) || !isset($obj->major_id) || !isset($obj->profession_id)){

				$data['major'] = '';
				$data['profession'] = '';
				$data['selected']['degree'] = '';

			}else{
				$data['selected']['degree'] = $obj->degree_type;
				$major_id = $obj->major_id;
				$profession_id = $obj->profession_id;
				$m = Major::find($major_id);
				$p = Profession::find($profession_id);

				if (isset($m->name)) {
					$data['major']  = $m->name;
				} else {
					$data['major']  = '';
				}

				if (isset($p->profession_name)) {
					$data['profession'] = $p->profession_name;
				} else {
					$data['profession'] = '';
				}
			}


			$degreeInitArr = Degree::get()->toArray();
			//$degreeArr[] = "";

			$degreeArr = array('' => 'Select...');

			foreach ($degreeInitArr as $k) {
				$id = $k['id'];
				$degreeArr[$id] = $k['display_name'];
			}
			$data['degree'] = $degreeArr;
			$c = new Country;
			$data['countriesAreaCode'] = $c->getAllCountriesAndAreaCodes();
			$mystring = $data['phone'];
			$findme   = ' ';
			$pos = strpos($mystring, $findme);

			if (isset($data['phone'][0]) && $data['phone'][0] == '+' && $pos < 5) {
				$data['phone'] = substr($data['phone'], $pos);
			}
			//end of list of required fields


			// check which modal we gonna populate

			// $modaltype = "";
			// if($data['fname'] != "" && $data['lname'] != "" && $data['email'] != ""
			// 	&& $data['address'] != "" && $data['city'] != "" && $data['state'] != "" && $data['zip'] != ""
			// 	&& $data['phone'] != ""  && $data['hs_gpa'] != "" && $data['selected']['degree']!="" && $data['major']!="" && $data['profession']!="" ){

			// 	$modaltype = 'recruitMeModal';

			// }else
			// {
			// 	$modaltype = 'hotModal';
			// }

			$modaltype = 'recruitMeModal';

			if( !isset($schoolId) ){
				$schoolId = $input['school_id'];
			}

			if($modaltype == 'recruitMeModal'){
				$data = $this->getRecruitmentModalData($data, $usrScores, $schoolId);
			}else{
				//HotModal Starts here
				//NOTE: We DON'T need to get all of the form again, since we already have some values from 'require field' check up on top
				$data = $this->getHotModalData($data, $schoolId, $user);

				// Show profile more info modal
				if ($data['country_id'] == 1) {
					if ($data['txt_opt_in'] == 0 || empty($data['phone']) || empty($data['address']) || empty($data['city'])
						|| empty($data['state']) || empty($data['zip'])) {
						$data['showProfileInfo'] = 'showProfileModal';
						$data['zipRequired'] = true;
					}
				}else{
					if ($data['txt_opt_in'] == 0 || empty($data['phone']) || empty($data['address']) || empty($data['city'])
						|| empty($data['state'])) {
						$data['showProfileInfo'] = 'showProfileModal';
						$data['zipRequired'] = false;
					}
				}
			}

			return View('private.includes.recruitme', $data);
		}
	}

	public function logBackIn($user_id = null){

		try {
			$user_id = Crypt::decrypt($user_id);
		} catch (\Exception $e) {
			return "Bad user id";
		}


		if (!Session::has('sales_log_back_in_user_id') && !Session::has('aor_log_back_in_user_id') || $user_id == null) {
			print_r('Oops you shouldn\'t be here <br>');
			print_r('Go back to <a href="https://plexuss.com/home">'.'home page</a>');
			exit();
		}

		$sales_log_back_in_user_id = Session::get('sales_log_back_in_user_id');
		$aor_log_back_in_user_id = Session::get('aor_log_back_in_user_id');

		$ac = new AuthController();


		$ac->clearAjaxToken( Auth::user()->id );
		Auth::logout();
		Session::flush();

		Auth::loginUsingId( $user_id, true );
		$ac->setAjaxToken($user_id);
		Session::put('userinfo.session_reset', 1);

		if($sales_log_back_in_user_id) {
			return redirect( '/sales/clients' );
		} else if($aor_log_back_in_user_id) {
			return redirect( '/admin/manageCollege' );
		} else {

		}

		return redirect('/home');

	}

	public function logBackInAdvancedSearch($user_id = null){

		try {
			$user_id = Crypt::decrypt($user_id);
		} catch (\Exception $e) {
			return "Bad user id";
		}


		if (!Session::has('sales_log_back_in_user_id') && !Session::has('aor_log_back_in_user_id') || $user_id == null) {
			print_r('Oops you shouldn\'t be here <br>');
			print_r('Go back to <a href="https://plexuss.com/home">'.'home page</a>');
			exit();
		}

		$sales_log_back_in_user_id = Session::get('sales_log_back_in_user_id');
		$aor_log_back_in_user_id = Session::get('aor_log_back_in_user_id');

		$ac = new AuthController();


		$ac->clearAjaxToken( Auth::user()->id );
		Auth::logout();
		Session::flush();

		Auth::loginUsingId( $user_id, true );
		$ac->setAjaxToken($user_id);
		Session::put('userinfo.session_reset', 1);

		if($sales_log_back_in_user_id) {
			return redirect( '/admin/studentsearch' );
		} else if($aor_log_back_in_user_id) {
			return redirect( '/admin/manageCollege' );
		} else {

		}

		return redirect('/home');

	}


	public function getAllCountries(){
		$country = new Country;
		$countries = $country->getAllCountries();
		return $countries;
	}

	public function getCountriesWithNameId(){
		$country = new Country;
		$countries = $country->getCountriesWithNameId();
		return $countries;
	}

	public function getAllReligionsCustom(){
		$religion = new Religion;
		return $religion->getAllReligionsCustom();
	}

	public function getAllReligions(){
		$religion = new Religion;
		$religions = $religion->getAllUsReligionsById();
		return $religions;
	}


	public function getAllStates(){
		$st = DB::connection('rds1')->table('states')->get();

		$temp = array();

		foreach ($st as $key) {
			$tmp = array();
			$tmp['id'] = (int)$key->id;
			$tmp['name'] = $key->state_name;
			$tmp['abbr'] = $key->state_abbr;

			$temp[] = $tmp;
		}

		return $temp;
	}

	public function getAttendedSchools($api_input = null){
		if( isset($api_input) ){
			$id = $api_input['user_id'];
		}else{
			$id = Session::get('userinfo.id');
		}

		$user = User::find($id);

		if( $user->in_college == 1 ){
			$col = new College;
			$attended = $col->getCollegesAttended($id);
		}else{
			$hs = new HighSchool;
			$attended = $hs->getHighSchoolsAttended($id);
		}

		return $attended;
	}

	public function findSchools($api_input = null){
		if( isset($api_input) ){
			$id = $api_input['user_id'];
			$input = $api_input;
		}else{
			$id = Session::get('userinfo.id');
			$input = Request::all();
		}

		$user = User::find($id);

		$inCollege = isset($input['in_college']) ? $input['in_college'] : $user->in_college;

		if( $inCollege == 1 ){
			$col = new College;
			$schools = $col->findCollegesCustom($input['search_for_school']);
		}else{
			$hs = new HighSchool;
			$schools = $hs->findHighschoolsCustom($input['search_for_school']);
		}

		return $schools;
	}

	public function findSchoolsForCollegeAndHS($api_input = null){
		if( isset($api_input) ){
			$id = $api_input['user_id'];
			$input = $api_input;
		}else{
			$id = Session::get('userinfo.id');
			$input = Request::all();
		}

		$user = User::find($id);

		$col = new College;
		$colleges = $col->findCollegesWithType($input['search_for_school']);

		$hs = new HighSchool;
		$high_schools = $hs->findHighschoolsCustom($input['search_for_school']);

		$merged = $colleges->merge($high_schools);

		// $all_schools = array_merge($colleges, $high_schools);

		return $merged->all();
	}

	public function searchForHighSchools(){
		$input = Request::all();
		$hs = new Highschool;
		$hsData = $hs->findHighschools($input['input']);

		return isset($hsData) && !empty($hsData) ? $hsData : array();
	}

	public function searchForColleges(){
		$input = Request::all();
		$college = new College;
		$colleges = $college->findColleges($input['input']);

		return isset($colleges) && !empty($colleges) ? $colleges : array();
	}

	public function searchForCollegesForThisUser(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$college = new College;
		$colleges = $college->findCollegesForThisUser($input['input'], $data['user_id']);

		return isset($colleges) && !empty($colleges) ? $colleges : array();
	}

	public function searchForCollegesForSales(){
		$input = Request::all();
		$college = new College;
		$colleges = $college->searchCollegesForSales($input['input']);

		return isset($colleges) && !empty($colleges) ? $colleges : array();
	}

	public function searchForMajors(){
		$input = Request::all();
		$major = new Major;
		$majors = $major->findMajor($input['input']);
		return $majors;
	}

	public function searchForProfessions(){
		$input = Request::all();
		$pro = new Profession;
		$professions = $pro->findProfession($input['input']);
		return $professions;
	}

	/**
	 * deleteUserAccount: this method delete's a user's account FOREVER
	 *
	 * @return route
	 */
	public function deleteUserAccount($api_input = null){

		if( isset($api_input) ){
			$input = $api_input;
			$data = $api_input;
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$input = Request::all();
			$data['reason'] = $input['deactivate_suggestion'];
		}

		$mac = new MandrillAutomationController;

		$unsub_ret = 'false';
		$cnt = 0;

		while ($unsub_ret == 'false' && $cnt < 10) {
			$unsub_ret = $this->unsubscribeThisEmail($data['email']);
			$cnt++;
		}

		$mac->deleteAccountInternalEmail($data);

		$user = User::where('id', $data['user_id'])
					->delete();

		if ($cnt >= 10 && $unsub_ret == 'false') {
			$mac->manuallyAddToSuppresionList($data);
		}

		return redirect('/signout');
	}

	public function unsubscribeThisEmail($email){

		$input = array();
		$input['email'] = $email;

		// Validation rules
		$rules = array(
			'email' => array(
				'regex:/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD'
			)
		);

		// Validate
		$validator = Validator::make( $input, $rules );
		if( $validator->fails() ){
			return "Bad email";
		}

		$uid = NULL;
		$uiid = NULL;
		$user = User::on('rds1')->where('email', $email)->select('id as user_id', 'email')->first();

		if (!isset($user)) {
			$user = UsersInvite::on('rds1')->where('invite_email', $input['email'])->first();
			if (isset($user)) {
				$uiid = $user->id;
			}
		}else{
			$uid = $user->id;
		}

		// 	if (isset($user)) {
		// 		$user_id = -1;
		// 	}else{
		// 		$user_id = -2;
		// 	}

		// }else{
		// 	$user_id = $user->user_id;
		// }

		// $dt  = array();

		// $dt['user_id'] = $user_id;
		// $dt['email']   = $input['email'];
		// $dt['fname']= isset($user->fname) ? $user->fname : NULL;

		// $mac = new MandrillAutomationController();
		// $mac->manuallyAddToSuppresionList($dt);

		$url = "https://api.sparkpost.com/api/v1/suppression-list/";
		$client = new Client(['base_uri' => 'http://httpbin.org']);

		$attr = array('recipient' => $email);
        $val  = array('recipient' => $email, 'uid'=> $uid, 'uiid' =>$uiid, 'description' => '',
        			  'source' => 'Manually Added', 'type' => 'transactional', 'non_transactional' => 1);

        $esl = EmailSuppressionList::updateOrCreate($attr, $val);

		try {
			$response = $client->request('PUT', $url, [
			    'headers' => [
			        'Content-Type'       => 'application/json',
			        'Accept'             => 'application/json',
			        'Authorization'      => env('SPARKPOST_KEY')
			    ],
			    'json' => ['recipients' => [["email" => $email, 'transactional' => true],
			    							["email" => $email, 'non_transactional' => true]]]
			]);
		} catch (\Exception $e) {
			return 'false';
		}

		$str = $response->getReasonPhrase();


		try {
			$response = $client->request('PUT', $url, [
			    'headers' => [
			        'Content-Type'       => 'application/json',
			        'Accept'             => 'application/json',
			        'Authorization'      => env('SPARKPOST_DIRECT_KEY')
			    ],
			    'json' => ['recipients' => [["email" => $email, 'transactional' => true],
			    							["email" => $email, 'non_transactional' => true]]]
			]);
		} catch (\Exception $e) {
			return 'false';
		}

		$str = $response->getReasonPhrase();

		if ($str == 'OK') {
			return 'true';
		}else{
			return 'false';
		}
	}

	public function getUnsubscribe($email = null){

		$data = array();
		$data['currentPage'] = 'unsubscribeThisEmail';

		if(!isset($email)) {
			return View('public.includes.unsubscribeStart', $data);
		}

		$input = array();
		$input['email'] = $email;
		$mac = new MandrillAutomationController;

		// Validation rules
		$rules = array(
			'email' => array(
				'regex:/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD'
			)
		);

		// Validate
		$validator = Validator::make( $input, $rules );
		if( $validator->fails() ){
			return "Bad email";
		}

		$user = User::on('rds1')->where('email', $email)->select('id as user_id', 'email')->first();

		if (!isset($user)) {
			$user = UsersInvite::on('rds1')->where('invite_email', $input['email'])->first();

			if (isset($user)) {
				$user_id = -1;
			}else{
				$user_id = -2;
			}

		}else{
			$user_id = $user->user_id;
		}

		$data['user_id'] = $user_id;
		$data['email']   = $input['email'];
		$data['fname']	 = isset($user->fname) ? $user->fname : NULL;

		return View('public.includes.unsubscribe', $data);
	}

	public function whyUserUnsubscribed(){

		$input = Request::all();
		$mac = new MandrillAutomationController;

		// dd($input);

		$data = array();

		$user = User::on('rds1')->where('email', $input['email'])->select('id as user_id', 'email')->first();

		if (!isset($user)) {
			$user = UsersInvite::on('rds1')->where('invite_email', $input['email'])->first();

			if (isset($user)) {
				$user_id = -1;
			}else{
				return "Bad email given";
			}

		}else{
			$user_id = $user->user_id;
		}

		$data['user_id'] = $user_id;
		$data['email']   = $input['email'];
		$data['reason']  = $input['reason'];

		$mac->whyUserUnsubscribed($data);

		return "success";
	}

	public function getInvoiceForUsers(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$opm = new OmniPayModel;

		return json_encode($opm->getInvoiceForUsers($data));
	}

	public function getInvoiceForAdmin() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$opm = new OmniPayModel;

		return json_encode($opm->getInvoiceForAdmin($data));
	}

	public function diceRoll(){
		$players = array('Adam', 'Sina', 'JP', 'Anthony', 'Gary', 'Nic', 'Drew', 'Peter');

		$rand_player = rand(0, count($players) -1);
		$player1 = $players[$rand_player];
		unset($players[$rand_player]);
		$players = array_values($players);

		$rand_player = rand(0, count($players) -1);
		$player2 = $players[$rand_player];
		unset($players[$rand_player]);
		$players = array_values($players);

		$rand_player = rand(0, count($players) -1);
		$player3 = $players[$rand_player];
		unset($players[$rand_player]);
		$players = array_values($players);

		$rand_player = rand(0, count($players) -1);
		$player4 = $players[$rand_player];
		unset($players[$rand_player]);
		$players = array_values($players);

		$rand_player = rand(0, count($players) -1);
		$player5 = $players[$rand_player];
		unset($players[$rand_player]);
		$players = array_values($players);

		$rand_player = rand(0, count($players) -1);
		$player6 = $players[$rand_player];
		unset($players[$rand_player]);
		$players = array_values($players);

		$rand_player = rand(0, count($players) -1);
		$player7 = $players[$rand_player];
		unset($players[$rand_player]);
		$players = array_values($players);

		$rand_player = rand(0, count($players) -1);
		$player8 = $players[$rand_player];
		unset($players[$rand_player]);
		$players = array_values($players);

		echo $player1 . " with ". $player2 ."<br>";
		echo "<br />";
		echo $player3 . " with ". $player4 ."<br>";
		echo "<br />";
		echo $player5 . " with ". $player6 ."<br>";
		echo "<br />";
		echo $player7 . " with ". $player8 ."<br>";
		echo "<br />";
	}

	public function adClicked($input = NULL){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$browser = Agent::browser();
		$version = Agent::version( $browser );

		$browser = $browser. " ". $version;


		$platform = Agent::platform();
		$version = Agent::version( $platform );

		$platform = $platform. " ".  $version;

		$device = Agent::isMobile();

		if ( $device ) {
			$device = Agent::device();
		}else {
			$device = Agent::device();
			if (!isset($device) || $device == 0) {
				$device = "Desktop";
			}
		}

		$iplookup = $this->iplookup($data['ip']);

		if (!isset($input)) {
			$input = Request::all();
		}

		if (!isset($data['user_id']) && isset($input['uid'])) {
			$data['user_id'] = $input['uid'];
		}

		// Don't send clicks to these partners. not real users.
		if (isset($input['utm_source'])) {
			if ((strpos($input['utm_source'], 'get_started') !== false) && $device == "iPhone" &&
			    (strpos($browser, 'Mozilla') !== false)) {
				return "failed";
			}
		}

		//if identified as a bot send them to the sign up page.
		if (isset($iplookup['countryName']) && isset($iplookup['stateName']) && isset($iplookup['cityName']) &&
			$iplookup['countryName'] == "United States" && $iplookup['stateName'] == "Oregon" &&
			$iplookup['cityName'] == "Boardman" && !isset($input['passon'])) {

			$redirect = "https://plexuss.com/adRedirect?passon=true&company=".$input['company'];

			isset($input['utm_source']) ? $redirect .= "&utm_source=".$input['utm_source'] : NULL;
			isset($input['cid']) ? $redirect .= "&cid=".$input['cid'] : NULL;
			$arr = array();
			$arr['url'] = "https://plexuss.com/signup?utm_term=Oregon_BOT&redirect=".urlencode($redirect);

			return $arr;
		}

		// Shorelight restrictions
		if ($iplookup['ip'] != "173.217.194.28" && $iplookup['ip'] != "24.4.84.172" && $iplookup['ip'] != "96.43.167.135" && $iplookup['ip'] != "72.27.155.145" && $iplookup['ip'] != "216.10.216.125" && isset($input['company']) && isset($iplookup['countryName']) && $input['company'] == 'shorelight' &&
			$iplookup['countryName'] != 'Republic of Korea' && $iplookup['countryName'] != 'Canada' &&
			$iplookup['countryName'] != 'Bahrain' && $iplookup['countryName'] != 'China' && $iplookup['countryName'] != 'Hong Kong' &&
			$iplookup['countryName'] != 'Japan' && $iplookup['countryName'] != 'Kuwait' && $iplookup['countryName'] != 'Mexico' &&
			$iplookup['countryName'] != 'Oman' && $iplookup['countryName'] != 'Qatar' && $iplookup['countryName'] != 'Saudi Arabia' &&
			$iplookup['countryName'] != 'Taiwan' && $iplookup['countryName'] != 'United Arab Emirates'){

			return "failed";
		}

		// for eddy if url not from US redirect to edx
		if ((isset($input['company']) && $input['company'] == 'eddy' || isset($input['company']) && $input['company'] == 'scholarshipowl')  && $iplookup['countryName'] != "United States") {

			$ac = new AdClick;

			$ac->company  	 = 'edx';
			$ac->user_id  	 = isset($data['user_id']) ? $data['user_id'] : -1;
			$ac->ip       	 = $iplookup['ip'];
			$ac->slug     	 = isset($input['slug']) ? $input['slug'] : -1;
			$ac->browser  	 = $browser;
			$ac->device   	 = $device;
			$ac->platform    = $platform;
			$ac->countryName = isset($iplookup['countryName']) ? $iplookup['countryName'] : 'N/A';
			$ac->stateName   = isset($iplookup['stateName']) ? $iplookup['stateName'] : 'N/A';
			$ac->cityName    = isset($iplookup['cityName']) ? $iplookup['cityName'] : 'N/A';
			$ac->ad_copy_id  = isset($input['adCopyId']) ? $input['adCopyId'] : '';
			isset($input['utm_source']) ? $ac->utm_source = $input['utm_source'] : NULL;
			(isset($input['uiid']) && $input['uiid'] !=0) ? $ac->user_invite_id = $input['uiid'] : NULL;
			isset($input['college_id']) ? $ac->college_id = $input['college_id'] : NULL;
            isset($input['ad_passthrough_id']) ? $ac->ad_passthrough_id = $input['ad_passthrough_id'] : NULL;
            isset($input['passthru_status']) ? $ac->passthru_status = $input['passthru_status'] : NULL;

			$ac->save();

			$edx = AdRedirectCampaign::on('rds1')->where('id', 3)->first();

			$input = array();
			$input['url'] = $edx->url;
			return $input;
		}

		$ac = new AdClick;

		$ac->company  	 = isset($input['company']) ? $input['company'] : 'eddy';
		$ac->user_id  	 = isset($data['user_id']) ? $data['user_id'] : -1;
		$ac->ip       	 = $iplookup['ip'];
		$ac->slug     	 = isset($input['slug']) ? $input['slug'] : -1;
		$ac->browser  	 = $browser;
		$ac->device   	 = $device;
		$ac->platform    = $platform;
		$ac->countryName = isset($iplookup['countryName']) ? $iplookup['countryName'] : 'N/A';
		$ac->stateName   = isset($iplookup['stateName']) ? $iplookup['stateName'] : 'N/A';
		$ac->cityName    = isset($iplookup['cityName']) ? $iplookup['cityName'] : 'N/A';
		$ac->ad_copy_id  = isset($input['adCopyId']) ? $input['adCopyId'] : '';
		isset($input['utm_source']) ? $ac->utm_source = $input['utm_source'] : NULL;
		(isset($input['uiid']) && $input['uiid'] !=0) ? $ac->user_invite_id = $input['uiid'] : NULL;
		isset($input['college_id']) ? $ac->college_id = $input['college_id'] : NULL;
        isset($input['ad_passthrough_id']) ? $ac->ad_passthrough_id = $input['ad_passthrough_id'] : NULL;
        isset($input['passthru_status']) ? $ac->passthru_status = $input['passthru_status'] : NULL;

		$ac->save();

		return "success";
	}

	public function applyNowClicked(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$browser = Agent::browser();
		$version = Agent::version( $browser );

		$browser = $browser. " ". $version;


		$platform = Agent::platform();
		$version = Agent::version( $platform );

		$platform = $platform. " ".  $version;

		$device = Agent::isMobile();

		if ( $device ) {
			$device = Agent::device();
		}else {
			$device = Agent::device();
			if (!isset($device) || $device == 0) {
				$device = "Desktop";
			}
		}

		$iplookup = $this->iplookup($data['ip']);

		$input = Request::all();

		$input['slug']   = isset($input['slug']) ? $input['slug'] : -1;
		$input['source'] = isset($input['source']) ? $input['source'] : '';
		$input['org_app_url'] = isset($input['org_app_url']) ? $input['org_app_url'] : '';

		$countryName = isset($iplookup['countryName']) ? $iplookup['countryName'] : 'N/A';
		$stateName   = isset($iplookup['stateName']) ? $iplookup['stateName'] : 'N/A';
		$cityName    = isset($iplookup['cityName']) ? $iplookup['cityName'] : 'N/A';

		$pos = strpos($data['ip'], ",");
		if ($pos !== false) {
			$data['ip'] = substr($data['ip'], 0, $pos);
		}

		$data['user_id'] = isset($data['user_id']) ? $data['user_id'] : -1;

		$attr = array('ip' => $data['ip'], 'slug' => $input['slug']);
		$val  = array('ip' => $data['ip'], 'slug' => $input['slug'], 'browser' => $browser, 'device' => $device,
					  'platform' => $platform, 'countryName' => $countryName, 'stateName' =>$stateName,
					  'cityName' => $cityName, 'user_id' => $data['user_id'], 'source' => $input['source'],
					  'org_app_url' => $input['org_app_url']);

		$response = ApplyClick::updateOrCreate($attr, $val);

		return "success";
	}

	public function addAdImpression(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$browser = Agent::browser();
		$version = Agent::version( $browser );

		$browser = $browser. " ". $version;


		$platform = Agent::platform();
		$version = Agent::version( $platform );

		$platform = $platform. " ".  $version;

		$device = Agent::isMobile();

		if ( $device ) {
			$device = Agent::device();
		}else {
			$device = Agent::device();
			if (!isset($device) || $device == 0) {
				$device = "Desktop";
			}
		}

		$iplookup = $this->iplookup($data['ip']);

		$input = Request::all();

		$input['slug'] = isset($input['slug']) ? $input['slug'] : -1;
		$input['company'] = isset($input['company']) ? $input['company'] : null;
		$countryName = isset($iplookup['countryName']) ? $iplookup['countryName'] : 'N/A';
		$stateName   = isset($iplookup['stateName']) ? $iplookup['stateName'] : 'N/A';
		$cityName    = isset($iplookup['cityName']) ? $iplookup['cityName'] : 'N/A';

		$pos = strpos($data['ip'], ",");
		if ($pos !== false) {
			$data['ip'] = substr($data['ip'], 0, $pos);
		}

		$data['user_id'] = isset($data['user_id']) ? $data['user_id'] : -1;

		$attr = array('ip' => $data['ip'], 'slug' => $input['slug'], 'company' => $input['company']);
		$val  = array('ip' => $data['ip'], 'slug' => $input['slug'], 'company' => $input['company'],
					  'browser' => $browser, 'device' => $device,
					  'platform' => $platform, 'countryName' => $countryName, 'stateName' =>$stateName,
					  'cityName' => $cityName, 'user_id' => $data['user_id']);

		$response = AdImpression::updateOrCreate($attr, $val);

		return "success";
	}

	public function trackApplyPixel(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$browser = Agent::browser();
		$version = Agent::version( $browser );

		$browser = $browser. " ". $version;


		$platform = Agent::platform();
		$version = Agent::version( $platform );

		$platform = $platform. " ".  $version;

		$device = Agent::isMobile();

		if ( $device ) {
			$device = Agent::device();
		}else {
			$device = Agent::device();
			if (!isset($device) || $device == 0) {
				$device = "Desktop";
			}
		}

		$iplookup = $this->iplookup($data['ip']);

		$input = Request::all();

		$input['slug'] = isset($input['slug']) ? $input['slug'] : -1;
		$countryName = isset($iplookup['countryName']) ? $iplookup['countryName'] : 'N/A';
		$stateName   = isset($iplookup['stateName']) ? $iplookup['stateName'] : 'N/A';
		$cityName    = isset($iplookup['cityName']) ? $iplookup['cityName'] : 'N/A';

		$pos = strpos($data['ip'], ",");
		if ($pos !== false) {
			$data['ip'] = substr($data['ip'], 0, $pos);
		}

		$data['user_id'] = isset($data['user_id']) ? $data['user_id'] : -1;
		$pixel_fired_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;

		$now = Carbon::now();

		$ac = ApplyClick::on('bk')->where('ip', $data['ip'])->first();

		if(isset($ac)){
			$attr = array('ip' => $data['ip']);
			$val  = array('ip' => $data['ip'], 'browser' => $browser, 'device' => $device,
						  'platform' => $platform, 'countryName' => $countryName, 'stateName' =>$stateName,
						  'cityName' => $cityName ,'pixel_fired_url' => $pixel_fired_url, 'pixel_tracked' => 1,
						  'pixel_fired_at' => $now);

			$response = ApplyClick::updateOrCreate($attr, $val);
		}

	}


	public function collegeNewsClicked(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);


		$browser = Agent::browser();
		$version = Agent::version( $browser );

		$browser = $browser. " ". $version;


		$platform = Agent::platform();
		$version = Agent::version( $platform );

		$platform = $platform. " ".  $version;

		$device = Agent::isMobile();

		if ( $device ) {
			$device = Agent::device();
		}else {
			$device = Agent::device();
			if (!isset($device) || $device == 0) {
				$device = "Desktop";
			}
		}

		$iplookup = $this->iplookup($data['ip']);

		$input = Request::all();

		$cnc = new CollegeNewsClick;

		$cnc->user_id  	 = isset($data['user_id']) ? $data['user_id'] : -1;
		$cnc->ip       	 = $data['ip'];
		$cnc->slug     	 = isset($input['slug']) ? $input['slug'] : -1;
		$cnc->browser  	 = $browser;
		$cnc->device   	 = $device;
		$cnc->platform    = $platform;
		$cnc->countryName = isset($iplookup['countryName']) ? $iplookup['countryName'] : 'N/A';
		$cnc->stateName   = isset($iplookup['stateName']) ? $iplookup['stateName'] : 'N/A';
		$cnc->cityName    = isset($iplookup['cityName']) ? $iplookup['cityName'] : 'N/A';
		$cnc->news_link   = isset($input['news_link']) ? $input['news_link'] : 'N/A';


		$cnc->save();

		return "success";
	}

	public function getPortals(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$set = new SettingController;
		$data = $set->getManageUsers($data);

		$portals = array();

		$activePortals = array();

		if( $data['super_admin'] == 1 ){
			$default = array();
			$default['name'] = 'General';
			$default['deactivatable'] = false;
			$default['hasEditTooltip'] = true;
			$default['hasNameTooltip'] = true;
			$default['hashedid'] = Crypt::encrypt(-1);
			$activePortals[] = $default;
		}

		$portals['active_portals'] = array_merge($activePortals, $data['active_portals']);
		$portals['deactivated_portals'] = $data['deactive_portals'];
		$portals['organization_portals'] = $data['organization_portals'];
		$portals['default_organization_portal'] = $data['default_organization_portal'];
		$portals['users'] = $data['users'];

		return $portals;
	}

	public function getProfile(){
		$data = array();
		$data['id']                  	  = (int)Session::get('userinfo.id');
		$data['fname']                    = Session::get('userinfo.fname');
		$data['lname']                    = Session::get('userinfo.lname');
    	$data['email']                    = Session::get('userinfo.email');
    	$data['org_id']                   = (int)Session::get('userinfo.org_id');
        $data['school_logo']              = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.Session::get('userinfo.school_logo');
    	$data['org_branch_id']            = (int)Session::get('userinfo.org_branch_id');
    	$data['org_school_id']            = (int)Session::get('userinfo.school_id');
        $data['completed_signup']         = (int)Session::get('userinfo.completed_signup');
        $data['super_admin']              = (int)Session::get('userinfo.super_admin');
    	$data['profile_pic']          	  = Session::get('userinfo.profile_img_loc');
		$data['profile_pic'] = $data['profile_pic'] ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_pic'] : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/default.png';
        $data['aor_id']     			  = Session::get('userinfo.aor_id');

		$default = array();
		$default['portal_name'] = 'General';
		$default['hashedid'] = Crypt::encrypt(-1);
		$data['portal_info'] = array($default);

		$org = OrganizationBranchPermission::where('user_id', $data['id'])
											->where('organization_branch_id', $data['org_branch_id'])->first();

		$college = CollegeOverviewImages::where('college_id', $data['org_school_id'])
										->where('is_video', 0)
										->where('is_tour', 0)
										->first();

		$data['title'] = $org->title;
		$data['blurb'] = $org->description;
		$data['working_since'] = $org->member_since ? explode('-', $org->member_since)[0] : null;
		$data['department'] = $org->department;
		$data['show_on_front_page'] = (int)$org->show_on_front_page;
		$data['show_on_college_page'] = (int)$org->show_on_college_page;

		if( $org->department != 'International recruitment' && $org->department != 'Domestic recruitment' &&
			$org->department != 'Marketing' && $org->department != 'Administration' ){
			$data['added_department'] = $org->department;
		}

		$data['school_background'] = isset($college) && $college->url ?  'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/'.$college->url : null;

		return $data;
	}

    public function searchCollegeWithBackgroundImage() {
        $input = Request::all();

        $term = $input['term'];

        $college_model = new College;

        $college_list = $college_model->findColleges($term)->toArray();

        foreach ($college_list as $college) {
            $image_query = CollegeOverviewImages::where('college_id', $college->id)
                ->where('is_video', 0)
                ->where('is_tour', 0)
                ->first();

            if (!empty($image_query) && isset($image_query->url)) {
                $college->school_background = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/' . $image_query->url;
            } else {
                $college->school_background = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/default-college-page-photo_overview.jpg";
            }

            if (isset($college->logo_url)) {
                $college->logo_url = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/" . $college->logo_url;
            } else {
                $college->logo_url = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png";
            }
        }

        return $college_list;
    }

	public function getAgencyProfile(){

		$viewDataController = new ViewDataController();
		$dt = $viewDataController->buildData(true);

		$data = array();
		$data['id']                  	  = (int)Session::get('userinfo.id');
		$data['fname']                    = Session::get('userinfo.fname');
		$data['lname']                    = Session::get('userinfo.lname');
    	$data['email']                    = Session::get('userinfo.email');
    	$data['agency_logo']              = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/'. $dt['agency_collection']->logo_url;
    	$data['agency_id']                = $dt['agency_collection']->agency_id;

    	$data['org_id']                   = NULL;
        $data['school_logo']              = NULL;
    	$data['org_branch_id']            = NULL;
    	$data['org_school_id']            = NULL;
        $data['completed_signup']         = (int)Session::get('userinfo.completed_signup');
        $data['super_admin']              = NULL;
    	$data['profile_pic']          	  = Session::get('userinfo.profile_img_loc');
		$data['profile_pic'] = $data['profile_pic'] ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_pic'] : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/default.png';
        $data['aor_id']     			  = Session::get('userinfo.aor_id');

		$default = array();
		$default['portal_name'] = 'General';
		$default['hashedid'] = Crypt::encrypt(-1);
		$data['portal_info'] = array($default);

		// $org = OrganizationBranchPermission::where('user_id', $data['id'])
		// 									->where('organization_branch_id', $data['org_branch_id'])->first();


		$data['title'] = NULL;
		$data['blurb'] = $dt['agency_collection']->detail;
		$data['working_since'] = NULL;
		$data['department'] = null;
		$data['show_on_front_page'] = NULL;
		$data['show_on_college_page'] = NULL;
		// if( $org->department != 'International recruitment' && $org->department != 'Domestic recruitment' &&
		// 	$org->department != 'Marketing' && $org->department != 'Administration' ){
		// 	$data['added_department'] = $org->department;
		// }

		$data['school_background'] = NULL;

		return $data;
	}

    public function savePhoneWithUserId() {
        $input = Request::all();

        $response = [];

        if (!isset($input['user_id']) || !isset($input['phone'])) {
            $response['status'] = 'failed';
            return $response;
        }

        $user = User::find($input['user_id']);

        $user->phone = $input['phone'];

        $user->save();

        $response['status'] = 'success';

        return $response;
    }

	public function saveProfile(){
		$input = Request::all();

		if( Request::has('user_id') && $input['user_id'] != 'undefined' ){
			$user_id = $input['user_id'];
			$org_branch_id = $input['org_branch_id'];
		}else{
			$data = array();
    		$data['user_id'] = Session::get('userinfo.id');
    		$data['org_branch_id'] = Session::get('userinfo.org_branch_id');

			$user_id = $data['user_id'];
			$org_branch_id = $data['org_branch_id'];
		}


		$user = User::find($user_id);
		$rep = OrganizationBranchPermission::where('user_id', $user_id)
										   ->where('organization_branch_id', $org_branch_id)->first();

		if(isset($user)){
			if(isset($input['fname'])) {
				$user->fname = $input['fname'];
			}

			if(isset($input['lname'])) {
				$user->lname = $input['lname'];
			}

			if( isset($input['email']) ){
				//if the input email is different from their current email, then check for duplicate
				//else do nothing
				if( $user->email != $input['email'] ){
					$duplicateEmail = User::where('email', $input['email'])->first();//returns user with this email, if any

					//if email doesn't already exist, allow user to change email
					//else don't save and return err msg
					if( !isset($duplicateEmail) ){
						$user->email = $input['email'];
						$email = $input['email'];
					}else{
						$input['email_exists'] = true;
						$input['err_msg'] = 'The email you entered already exists in our system. Please try a different email.';
						return $input;
					}
				}
			}

			// upload File pic if exist
			if( isset($input['avatar_url']) ){
				$split = explode('/', $input['avatar_url']);
				$path = end( $split );
				$user->profile_img_loc = $path;

			}elseif( isset($input['profile_pic']) ) {
                $response = $this->generalUploadDoc($input, 'profile_pic', 'asset.plexuss.com/users/images');

                if (!empty($response))
				    $user->profile_img_loc = $response['saved_as'];
			}

			//save edits
			$user->save();
		}else{
			return 'error';
		}

		if(isset($rep) && !empty($rep)) {

			if(isset($input['title'])) {
				$rep->title = $input['title'];
			}

			if(isset($input['working_since'])) {
				$rep->member_since = Carbon::parse('01/01/'.$input['working_since'])->format('Y-m-d'); // $input['working_since'];
			}

			if(isset($input['blurb'])) {
				$rep->description = $input['blurb'];
			}

			if( isset($input['department']) ){
				$rep->department = $input['department'];
			}

			$rep->save();

		} else {

			// if they have not get a match. build a new one
			$newRep = new OrganizationBranchPermission;
			$newRep->super_admin = 1;
			$newRep->organization_branch_id = $data['org_branch_id'];
			$newRep->user_id = $user_id;

			$newRep->title = isset($input['title']) ? $input['title'] : null;
			$newRep->member_since = isset($input['working_since']) ? Carbon::parse('01/01/'.$input['working_since'])->format('Y-m-d') : null;
			$newRep->description = isset($input['blurb']) ? $input['blurb'] : null;
			$newRep->description = isset($input['department']) ? $input['department'] : null;

			$newRep->save();
		}

		Session::put('userinfo.session_reset', 1);

		return 'success';
	}

	public function setupCompleted(){
		$data = array();
    	$data['user_id'] = Session::get('userinfo.id');

		//save completed_signup
		$user = User::find($data['user_id']);
		$user->completed_signup = 1;
		$user->save();

		//reset session
		Session::put('userinfo.session_reset', 1);

		return $user;
	}

	public function updateUsersForManageUsers(){
		$input = Request::all();

		//input should have user id, org branch id, name, role: super_admin=0/1, frontpageShow:true/false, collegepageShow: true/false
		//should save role(could've changed), frontpage Profile to be shown, college page to be shown

		//if input has user_id and org_branch_id (it should), use that
		//else get it from session
		if( Request::has('id') && $input['id'] != 'undefined' ){
			$user_id = $input['id'];
			$org_branch_id = $input['org_branch_id'];
		}else{
			$data = array();
    		$data['user_id'] = Session::get('userinfo.id');
    		$data['org_branch_id'] = Session::get('userinfo.org_branch_id');

			$user_id = $data['user_id'];
			$org_branch_id = $data['org_branch_id'];
		}

		$super_admin = $input['super_admin'];
		$show_on_college_page = $input['show_on_college_page'];
		$show_on_front_page   = $input['show_on_front_page'];

		$obp = OrganizationBranchPermission::on('rds1')->where('organization_branch_id', $org_branch_id)
													   ->where('show_on_front_page', 1)
													   ->first();

		if ( (isset($obp) && $obp->user_id == $user_id) && $show_on_front_page == 0) {
			$err = array();
			$err['has_err'] = true;
			$err['msg'] = "Can't unset show on front page for this user!";
			return $err;
		}

		if ($show_on_front_page == 1) {
			OrganizationBranchPermission::where('organization_branch_id', $org_branch_id)
										->update(array('show_on_front_page' => 0));
		}

		OrganizationBranchPermission::where('organization_branch_id', $org_branch_id)
									->where('user_id', $user_id)
									->update(array('super_admin' => $super_admin,
										           'show_on_front_page' => $show_on_front_page,
										           'show_on_college_page' => $show_on_college_page));
		return 'success';
	}

	public function getUsers(){
		$input = Request::all();

		if( Request::has('org_branch_id') ){
			$org_branch_id = $input['org_branch_id'];
			$user_id = $input['id'];
		}else{
	    	$org_branch_id = Session::get('userinfo.org_branch_id');
	    	$user_id = Session::get('userinfo.id');
		}

		$obp = new OrganizationBranchPermission;
		$allUsers = $obp->getAllAdminUserAllInfo($user_id, $org_branch_id);

		return $allUsers;
	}

	public function reasonsWhyRemovingStudent($userid){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$agency_id = isset($data['agency_collection']->agency_id) ? $data['agency_collection']->agency_id : '';
		$org_branch_id = $data['org_branch_id'];
		$aor_id    = isset($data['aor_id']) ? $data['aor_id'] : '';

		foreach ($input as $key => $val) {
			if ($key == 'other_reason_response') {
				$reason = $val;
			}else{
				$reason = $key;
			}
			$attr = array('aor_id' => $aor_id, 'user_id' => $userid, 'org_branch_id' => $org_branch_id,
					      'agency_id' =>$agency_id, 'reason' => $reason );
			$vals = array('aor_id' => $aor_id, 'user_id' => $userid, 'org_branch_id' => $org_branch_id,
						  'agency_id' =>$agency_id, 'reason' => $reason );

			$update = CollegeRemoveStudentsFeedback::updateOrCreate($attr, $vals);

		}

		$this->setRecruit($userid, 1, true);

		return "success";
	}

	public function savePickACollegeView(){

		$user_id = Session::get('userinfo.id');

		if (!isset($user_id) || $user_id == -1) {
			return "Bad userid";
		}
		$college_id = Request::get('college_id');

		$pacv = new PickACollegeView;

		$pacv->user_id    = $user_id;
		$pacv->college_id = $college_id;

		$pacv->save();

		return "success";
	}

	// User Home page Start here

	public function saveAppliedToSchools(){

		$data = array();
		$data['fname'] = ucwords(strtolower(Session::get('userinfo.fname')));
    	$data['lname'] = ucwords(strtolower(Session::get('userinfo.lname')));
    	$data['user_id'] = Session::get('userinfo.id');

		$input = Request::all();

		$rec_id = NULL;
		$pr_id  = NULL;
		$is_already_handshake = false;
		$is_already_inquiry   = false;

		if(isset($input['rec_id'])){
			try {
				$rec_id = Crypt::decrypt($input['rec_id']);
			} catch (\Exception $e) {
				return "failed";
			}
		}
		if(isset($input['pr_id'])){
			try {
				$pr_id = Crypt::decrypt($input['pr_id']);
			} catch (\Exception $e) {
				return "failed";
			}
		}

		if($input['applied'] == 'yes'){
			$now = Carbon::now();

			if (isset($rec_id)) {
				$rec = Recruitment::find($rec_id);

				if ($rec->user_recruit == 1 && $rec->college_recruit == 1) {
					$is_already_handshake = true;
				}
				if ($rec->user_recruit == 1 && $rec->college_recruit == 0) {
					$is_already_inquiry = true;
				}

				$rec->user_recruit = 1;

			}elseif (isset($pr_id)) {
				$rec = PrescreenedUser::find($pr_id);
			}else{
				$rec = new Recruitment;

				$rec->user_id 		  = $data['user_id'];
				$rec->college_id 	  = $input['school_id'];
				$rec->user_recruit 	  = 1;
				$rec->college_recruit = 0;
			}

			$rec->user_applied_at = $now;
			$rec->user_applied = 1;
			$rec->save();

			if (!isset($input['school_id'])) {
				$input['school_id'] = $rec->college_id;
			}

			$has_applied_code = 3;
			if (isset($rec->user_recruit)) {

				if ($rec->user_recruit == 1 && $rec->college_recruit == 1 && $is_already_handshake == false) {

					// Handshake notification
					$ntn = new NotificationController();
					$ntn->create( $data['fname'].' '.$data['lname'], 'college', 2, null, $data['user_id'], $input['school_id']);


				}elseif ($rec->user_recruit == 1 && $rec->college_recruit == 0 && $is_already_inquiry == false){

					// Inquiry notification
					$ntn = new NotificationController();
					$ntn->create(  $data['fname'].' '.$data['lname'], 'college', 1, null, $data['user_id'], $input['school_id'] );
				}

				if ($rec->user_recruit == 1 && $rec->college_recruit == 1){
					if (isset($pr_id)) {
						$has_applied_code = 5;
					}else{
						$has_applied_code = 3;
					}

				}elseif ($rec->user_recruit == 1 && $rec->college_recruit == 0) {
					$has_applied_code = 4;
				}
			}

			// Has applied to your school notification
			$ntn = new NotificationController();
			$ntn->create(  $data['fname'].' '.$data['lname'], 'college', $has_applied_code, null, $data['user_id'], $input['school_id'] );
			return "success";
		}elseif ($input['applied'] == 'no') {

			if (isset($rec_id)) {
				$rec = Recruitment::find($rec_id);
			}elseif (isset($pr_id)) {
				$rec = PrescreenedUser::find($pr_id);
			}

			$rec->user_applied = -1;
			$rec->user_applied_at = NULL;
			$rec->save();

			return "success";
		}

		return "failed";
	}

	public function getAllDepts(){
		$depts = new Department;
		return $depts->getAllDepartments(null, true, true);
	}

	public function getAllMilitaries(){
		$military_affiliation_raw = MilitaryAffiliation::all()->toArray();
		/*$military_affiliation_arr = array();
		foreach( $military_affiliation_raw as $mar ){
			$military_affiliation_arr[ $mar[ 'name' ] ] = $mar[ 'name' ];
		}*/
		return $military_affiliation_raw;
	}

	public function getAllMajors(){
		if (Cache::has(env('ENVIRONMENT').'_'.'getAllMajors_from_ajaxcontroller') ) {
			
			$ret = Cache::get(env('ENVIRONMENT').'_'.'getAllMajors_from_ajaxcontroller'); 
			Cache::put(env('ENVIRONMENT').'_'.'getAllMajors_from_ajaxcontroller', $ret, 720);

			return $ret;
		}

		$mjr = new Major;
		$ret = $mjr->getAllMajors();
		
		Cache::put(env('ENVIRONMENT').'_'.'getAllMajors_from_ajaxcontroller', $ret, 720);

		return $ret;
	}

	public function getAllEthnicities(){
		if (Cache::has(env('ENVIRONMENT').'_'.'getAllEthnicities_from_ajaxcontroller') ) {
			
			$ret = Cache::get(env('ENVIRONMENT').'_'.'getAllEthnicities_from_ajaxcontroller'); 
			Cache::put(env('ENVIRONMENT').'_'.'getAllEthnicities_from_ajaxcontroller', $ret, 720);

			return $ret;
		}

		$eth = new Ethnicity;
		$ret = $eth->getAllEthnicities();

		Cache::put(env('ENVIRONMENT').'_'.'getAllEthnicities_from_ajaxcontroller', $ret, 720);

		return $ret;
	}
	// User Home page ends here

	// Admin International Tools page start here
	public function internationalToolSave(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		// $this->customdd($input);
		$college_id = $data['org_school_id'];
		$ret = array();

		switch ($input['tab']) {
			case 'program':

				$attr = array('college_id' => $college_id);
				$val  = array('college_id' => $college_id);

				$program = $input['program'];

				if (!empty($program['epp']) && !empty($program['undergrad']) && !empty($program['grad'])) {

					$val['define_program'] = 'all';
				}elseif (!empty($program['undergrad']) && !empty($program['grad'])) {

					$val['define_program'] = 'both';
				}elseif (!empty($program['undergrad']) && !empty($program['epp'])) {

					$val['define_program'] = 'undergrad_epp';
				}elseif (!empty($program['grad']) && !empty($program['epp'])) {

					$val['define_program'] = 'grad_epp';
				}elseif (!empty($program['grad'])) {

					$val['define_program'] = 'grad';
				}elseif (!empty($program['undergrad'])) {

					$val['define_program'] = 'undergrad';
				}elseif (!empty($program['epp'])) {

					$val['define_program'] = 'epp';
				}

				$update = CollegesInternationalTab::updateOrCreate($attr, $val);
				$ret['status'] = "success";

				break;
			case 'header_info':

				$attr = array('college_id' => $college_id);
				$val  = array('college_id' => $college_id);

				(isset($input['undergrad_total_yearly_cost']) && !empty($input['undergrad_total_yearly_cost'])) ? $val['undergrad_total_yearly_cost'] = $input['undergrad_total_yearly_cost'] : NULL;
				(isset($input['undergrad_tuition'])    && !empty($input['undergrad_tuition'])) ? $val['undergrad_tuition'] = $input['undergrad_tuition'] : NULL;
				(isset($input['undergrad_room_board']) && !empty($input['undergrad_room_board'])) ? $val['undergrad_room_board'] = $input['undergrad_room_board'] : NULL;
				(isset($input['undergrad_book_supplies']) && !empty($input['undergrad_book_supplies'])) ? $val['undergrad_book_supplies'] = $input['undergrad_book_supplies'] : NULL;

				(isset($input['grad_total_yearly_cost']) && !empty($input['grad_total_yearly_cost'])) ? $val['grad_total_yearly_cost'] = $input['grad_total_yearly_cost'] : NULL;
				(isset($input['grad_tuition']) && !empty($input['grad_tuition'])) ? $val['grad_tuition'] = $input['grad_tuition'] : NULL;
				(isset($input['grad_room_board']) && !empty($input['grad_room_board'])) ? $val['grad_room_board'] = $input['grad_room_board'] : NULL;
				(isset($input['grad_book_supplies']) && !empty($input['grad_book_supplies'])) ? $val['grad_book_supplies'] = $input['grad_book_supplies'] : NULL;

				(isset($input['epp_total_yearly_cost']) && !empty($input['epp_total_yearly_cost'])) ? $val['epp_total_yearly_cost'] = $input['epp_total_yearly_cost'] : NULL;
				(isset($input['epp_tuition']) && !empty($input['epp_tuition'])) ? $val['epp_tuition'] = $input['epp_tuition'] : NULL;
				(isset($input['epp_room_board']) && !empty($input['epp_room_board'])) ? $val['epp_room_board'] = $input['epp_room_board'] : NULL;
				(isset($input['epp_book_supplies']) && !empty($input['epp_book_supplies'])) ? $val['epp_book_supplies'] = $input['epp_book_supplies'] : NULL;

				if (isset($val) && !empty($val)) {
					$update = CollegesInternationalTab::updateOrCreate($attr, $val);
					$ret['status'] = "success";
				}else{
					$ret['status'] = "failed";
				}

				break;
			case 'testimonials':
				$cit = CollegesInternationalTab::on('rds1')->where('college_id', $college_id)->first();

				if (!isset($cit)) {
					$ret['status'] = "failed";
				}

				if (strpos($input['url'], 'youtube') !== FALSE){
					$url = $input['url'];
					$url = substr($url, strrpos($url, '=') + 1);
					$source = "youtube";
				}elseif (strpos($input['url'], 'vimeo') !== FALSE){
					$url = $input['url'];
					$url = substr($url, strrpos($url, '/') + 1);
					$source = "vimeo";
				}

				if (isset($input['id'])) {
					$attr = array('id' => $input['id']);
				}else{
					$attr = array('cit_id' => $cit->id, 'college_id' => $college_id, 'title' => $input['title'], 'url' => $url, 'source' => $source);
				}
				$val = array('cit_id' => $cit->id, 'college_id' => $college_id, 'title' => $input['title'], 'url' => $url, 'source' => $source);

				$citl = CollegesInternationalTestimonial::updateOrCreate($attr, $val);

				$ret['video_testimonial_id'] = (int)$citl->id;
				$ret['status'] = "success";

				break;
			case 'admission':
				$attr = array('college_id' => $college_id);
				$val  = array('college_id' => $college_id);

				$undergrad_admissions_available = 0;
				if (isset($input['undergrad_admissions_available']) && $input['undergrad_admissions_available'] == 'yes') {
					$undergrad_admissions_available = 1;
				}elseif(isset($input['undergrad_admissions_available']) && $input['undergrad_admissions_available'] == 'no') {
					$undergrad_admissions_available = -1;
				}

				$val['undergrad_admissions_available'] = $undergrad_admissions_available;

				$grad_admissions_available = 0;
				if (isset($input['grad_admissions_available']) && $input['grad_admissions_available'] == 'yes') {
					$grad_admissions_available = 1;
				}elseif (isset($input['grad_admissions_available']) && $input['grad_admissions_available'] == 'no') {
					$grad_admissions_available = -1;
				}

				$val['grad_admissions_available'] = $grad_admissions_available;

				$epp_admissions_available = 0;
				if (isset($input['epp_admissions_available']) && $input['epp_admissions_available'] == 'yes') {
					$epp_admissions_available = 1;
				}elseif (isset($input['epp_admissions_available']) && $input['epp_admissions_available'] == 'no') {
					$epp_admissions_available = -1;
				}

				$val['epp_admissions_available'] = $epp_admissions_available;


				(isset($input['undergrad_application_deadline']) && !empty($input['undergrad_application_deadline'])) ? $val['undergrad_application_deadline'] = $input['undergrad_application_deadline'] : NULL;

				(isset($input['undergrad_application_fee']) && !empty($input['undergrad_application_fee'])) ? $val['undergrad_application_fee'] = $input['undergrad_application_fee'] : NULL;

				(isset($input['undergrad_num_of_applicants']) && !empty($input['undergrad_num_of_applicants'])) ? $val['undergrad_num_of_applicants'] = $input['undergrad_num_of_applicants'] : NULL;

				(isset($input['undergrad_num_of_admitted']) && !empty($input['undergrad_num_of_admitted'])) ? $val['undergrad_num_of_admitted'] = $input['undergrad_num_of_admitted'] : NULL;

				(isset($input['undergrad_num_of_admitted_enrolled']) && !empty($input['undergrad_num_of_admitted_enrolled'])) ? $val['undergrad_num_of_admitted_enrolled'] = $input['undergrad_num_of_admitted_enrolled'] : NULL;

				(isset($input['grad_application_deadline']) && !empty($input['grad_application_deadline'])) ? $val['grad_application_deadline'] = $input['grad_application_deadline'] : NULL;

				(isset($input['grad_application_fee']) && !empty($input['grad_application_fee'])) ? $val['grad_application_fee'] = $input['grad_application_fee'] : NULL;

				(isset($input['grad_num_of_applicants']) && !empty($input['grad_num_of_applicants'])) ? $val['grad_num_of_applicants'] = $input['grad_num_of_applicants'] : NULL;

				(isset($input['grad_num_of_admitted']) && !empty($input['grad_num_of_admitted'])) ? $val['grad_num_of_admitted'] = $input['grad_num_of_admitted'] : NULL;

				(isset($input['grad_num_of_admitted_enrolled']) && !empty($input['grad_num_of_admitted_enrolled'])) ? $val['grad_num_of_admitted_enrolled'] = $input['grad_num_of_admitted_enrolled'] : NULL;

				////
				(isset($input['epp_application_deadline']) && !empty($input['epp_application_deadline'])) ? $val['epp_application_deadline'] = $input['epp_application_deadline'] : NULL;

				(isset($input['epp_application_fee']) && !empty($input['epp_application_fee'])) ? $val['epp_application_fee'] = $input['epp_application_fee'] : NULL;

				(isset($input['epp_num_of_applicants']) && !empty($input['epp_num_of_applicants'])) ? $val['epp_num_of_applicants'] = $input['epp_num_of_applicants'] : NULL;

				(isset($input['epp_num_of_admitted']) && !empty($input['epp_num_of_admitted'])) ? $val['epp_num_of_admitted'] = $input['epp_num_of_admitted'] : NULL;

				(isset($input['epp_num_of_admitted_enrolled']) && !empty($input['epp_num_of_admitted_enrolled'])) ? $val['epp_num_of_admitted_enrolled'] = $input['epp_num_of_admitted_enrolled'] : NULL;


				if (isset($val) && !empty($val)) {
					$update = CollegesInternationalTab::updateOrCreate($attr, $val);
					$ret['status'] = "success";
				}else{
					$ret['status'] = "failed";
				}

				break;
			case 'scholarship':
				$attr = array('college_id' => $college_id);
				$val  = array('college_id' => $college_id);
				$undergrad_scholarship_available = 0;
				if (isset($input['undergrad_scholarship_available']) && $input['undergrad_scholarship_available'] == 'yes') {
					$undergrad_scholarship_available = 1;
				}elseif(isset($input['undergrad_scholarship_available']) && $input['undergrad_scholarship_available'] == 'no') {
					$undergrad_scholarship_available = -1;
				}
				$grad_scholarship_available = 0;
				if (isset($input['grad_scholarship_available']) && $input['grad_scholarship_available'] == 'yes') {
					$grad_scholarship_available = 1;
				}elseif (isset($input['grad_scholarship_available']) && $input['grad_scholarship_available'] == 'no') {
					$grad_scholarship_available = -1;
				}
				$epp_scholarship_available = 0;
				if (isset($input['epp_scholarship_available']) && $input['epp_scholarship_available'] == 'yes') {
					$epp_scholarship_available = 1;
				}elseif (isset($input['epp_scholarship_available']) && $input['epp_scholarship_available'] == 'no') {
					$epp_scholarship_available = -1;
				}

				(isset($input['undergrad_scholarship_student_received_aid']) && !empty($input['undergrad_scholarship_student_received_aid'])) ? $val['undergrad_scholarship_student_received_aid'] = $input['undergrad_scholarship_student_received_aid'] : NULL;
				(isset($undergrad_scholarship_available) && !empty($undergrad_scholarship_available)) ? $val['undergrad_scholarship_available'] = $undergrad_scholarship_available : NULL;
				(isset($input['undergrad_scholarship_avg_financial_aid_given']) && !empty($input['undergrad_scholarship_avg_financial_aid_given'])) ? $val['undergrad_scholarship_avg_financial_aid_given'] = $input['undergrad_scholarship_avg_financial_aid_given'] : NULL;
				(isset($input['undergrad_scholarship_requirments']) && !empty($input['undergrad_scholarship_requirments'])) ? $val['undergrad_scholarship_requirments'] = $input['undergrad_scholarship_requirments'] : NULL;
				(isset($input['undergrad_scholarship_gpa']) && !empty($input['undergrad_scholarship_gpa'])) ? $val['undergrad_scholarship_gpa'] = $input['undergrad_scholarship_gpa'] : NULL;
				(isset($input['undergrad_scholarship_link']) && !empty($input['undergrad_scholarship_link'])) ? $val['undergrad_scholarship_link'] = $input['undergrad_scholarship_link'] : NULL;

				(isset($input['grad_scholarship_student_received_aid']) && !empty($input['grad_scholarship_student_received_aid'])) ? $val['grad_scholarship_student_received_aid'] = $input['grad_scholarship_student_received_aid'] : NULL;
				(isset($input['grad_scholarship_available']) && !empty($input['grad_scholarship_available'])) ? $val['grad_scholarship_available'] = $grad_scholarship_available : NULL;
				(isset($input['grad_scholarship_avg_financial_aid_given']) && !empty($input['grad_scholarship_avg_financial_aid_given'])) ? $val['grad_scholarship_avg_financial_aid_given'] = $input['grad_scholarship_avg_financial_aid_given'] : NULL;
				(isset($input['grad_scholarship_requirments']) && !empty($input['grad_scholarship_requirments'])) ? $val['grad_scholarship_requirments'] = $input['grad_scholarship_requirments'] : NULL;
				(isset($input['grad_scholarship_gpa']) && !empty($input['grad_scholarship_gpa'])) ? $val['grad_scholarship_gpa'] = $input['grad_scholarship_gpa'] : NULL;
				(isset($input['grad_scholarship_link']) && !empty($input['grad_scholarship_link'])) ? $val['grad_scholarship_link'] = $input['grad_scholarship_link'] : NULL;

				(isset($input['epp_scholarship_student_received_aid']) && !empty($input['epp_scholarship_student_received_aid'])) ? $val['epp_scholarship_student_received_aid'] = $input['epp_scholarship_student_received_aid'] : NULL;
				(isset($input['epp_scholarship_available']) && !empty($input['epp_scholarship_available'])) ? $val['epp_scholarship_available'] = $epp_scholarship_available : NULL;
				(isset($input['epp_scholarship_avg_financial_aid_given']) && !empty($input['epp_scholarship_avg_financial_aid_given'])) ? $val['epp_scholarship_avg_financial_aid_given'] = $input['epp_scholarship_avg_financial_aid_given'] : NULL;
				(isset($input['epp_scholarship_requirments']) && !empty($input['epp_scholarship_requirments'])) ? $val['epp_scholarship_requirments'] = $input['epp_scholarship_requirments'] : NULL;
				(isset($input['epp_scholarship_gpa']) && !empty($input['epp_scholarship_gpa'])) ? $val['epp_scholarship_gpa'] = $input['epp_scholarship_gpa'] : NULL;
				(isset($input['epp_scholarship_link']) && !empty($input['epp_scholarship_link'])) ? $val['epp_scholarship_link'] = $input['epp_scholarship_link'] : NULL;

				if (isset($val) && !empty($val)) {
					$update = CollegesInternationalTab::updateOrCreate($attr, $val);
					$ret['status'] = "success";
				}else{
					$ret['status'] = "failed";
				}
				break;
			case 'notes':

				$cit = CollegesInternationalTab::on('rds1')->where('college_id', $college_id)->first();

				if (!isset($cit)) {
					$ret['status'] = "failed";
				}
				if (isset($input['content']) && !empty($input['content'])) {
					$content   = $input['content'];
				}
				if (isset($input['view_type']) && !empty($input['view_type'])) {
					$view_type   = $input['view_type'];
				}
				if (isset($input['id'])) {
					$attr = array('id' => $input['id']);
				}else{
					$attr = array('college_id' => $cit->college_id, 'cit_id' => $cit->id, 'view_type' => $view_type, 'content' => $content);
				}

				$val  = array('college_id' => $cit->college_id, 'cit_id' => $cit->id, 'view_type' => $view_type, 'content' => $content);

				$update = CollegesInternationalAdditionalNote::updateOrCreate($attr, $val);

				$ret['note_id'] = (int)$update->id;
				$ret['status'] = "success";

				break;
			case 'grades':
				// $this->customdd($input);
				$attr = array('college_id' => $college_id);
				$val  = array('college_id' => $college_id);

				(isset($input['undergrad_grade_act_composite_avg']) && !empty($input['undergrad_grade_act_composite_avg'])) ? $val['undergrad_grade_act_composite_avg'] = $input['undergrad_grade_act_composite_avg'] : NULL;
				(isset($input['undergrad_grade_act_composite_min']) && !empty($input['undergrad_grade_act_composite_min'])) ? $val['undergrad_grade_act_composite_min'] = $input['undergrad_grade_act_composite_min'] : NULL;
				(isset($input['undergrad_grade_act_english_avg']) && !empty($input['undergrad_grade_act_english_avg'])) ? $val['undergrad_grade_act_english_avg'] = $input['undergrad_grade_act_english_avg'] : NULL;
				(isset($input['undergrad_grade_act_english_min']) && !empty($input['undergrad_grade_act_english_min'])) ? $val['undergrad_grade_act_english_min'] = $input['undergrad_grade_act_english_min'] : NULL;
				(isset($input['undergrad_grade_act_math_avg']) && !empty($input['undergrad_grade_act_math_avg'])) ? $val['undergrad_grade_act_math_avg'] = $input['undergrad_grade_act_math_avg'] : NULL;
				(isset($input['undergrad_grade_act_math_min']) && !empty($input['undergrad_grade_act_math_min'])) ? $val['undergrad_grade_act_math_min'] = $input['undergrad_grade_act_math_min'] : NULL;
				(isset($input['undergrad_grade_gmat_quant_min']) && !empty($input['undergrad_grade_gmat_quant_min'])) ? $val['undergrad_grade_gmat_quant_min'] = $input['undergrad_grade_gmat_quant_min'] : NULL;
				(isset($input['undergrad_grade_gmat_verbal_avg']) && !empty($input['undergrad_grade_gmat_verbal_avg'])) ? $val['undergrad_grade_gmat_verbal_avg'] = $input['undergrad_grade_gmat_verbal_avg'] : NULL;
				(isset($input['undergrad_grade_gpa_avg']) && !empty($input['undergrad_grade_gpa_avg'])) ? $val['undergrad_grade_gpa_avg'] = $input['undergrad_grade_gpa_avg'] : NULL;
				(isset($input['undergrad_grade_gpa_min']) && !empty($input['undergrad_grade_gpa_min'])) ? $val['undergrad_grade_gpa_min'] = $input['undergrad_grade_gpa_min'] : NULL;
				(isset($input['undergrad_grade_gre_quant_avg']) && !empty($input['undergrad_grade_gre_quant_avg'])) ? $val['undergrad_grade_gre_quant_avg'] = $input['undergrad_grade_gre_quant_avg'] : NULL;
				(isset($input['undergrad_grade_gre_quant_min']) && !empty($input['undergrad_grade_gre_quant_min'])) ? $val['undergrad_grade_gre_quant_min'] = $input['undergrad_grade_gre_quant_min'] : NULL;
				(isset($input['undergrad_grade_gre_verbal_avg']) && !empty($input['undergrad_grade_gre_verbal_avg'])) ? $val['undergrad_grade_gre_verbal_avg'] = $input['undergrad_grade_gre_verbal_avg'] : NULL;
				(isset($input['undergrad_grade_gre_verbal_min']) && !empty($input['undergrad_grade_gre_verbal_min'])) ? $val['undergrad_grade_gre_verbal_min'] = $input['undergrad_grade_gre_verbal_min'] : NULL;
				(isset($input['undergrad_grade_gre_writing_avg']) && !empty($input['undergrad_grade_gre_writing_avg'])) ? $val['undergrad_grade_gre_writing_avg'] = $input['undergrad_grade_gre_writing_avg'] : NULL;
				(isset($input['undergrad_grade_gre_writing_min']) && !empty($input['undergrad_grade_gre_writing_min'])) ? $val['undergrad_grade_gre_writing_min'] = $input['undergrad_grade_gre_writing_min'] : NULL;
				(isset($input['undergrad_grade_ielts_avg']) && !empty($input['undergrad_grade_ielts_avg'])) ? $val['undergrad_grade_ielts_avg'] = $input['undergrad_grade_ielts_avg'] : NULL;
				(isset($input['undergrad_grade_ielts_min']) && !empty($input['undergrad_grade_ielts_min'])) ? $val['undergrad_grade_ielts_min'] = $input['undergrad_grade_ielts_min'] : NULL;
				(isset($input['undergrad_grade_psat_math_avg']) && !empty($input['undergrad_grade_psat_math_avg'])) ? $val['undergrad_grade_psat_math_avg'] = $input['undergrad_grade_psat_math_avg'] : NULL;
				(isset($input['undergrad_grade_psat_math_min']) && !empty($input['undergrad_grade_psat_math_min'])) ? $val['undergrad_grade_psat_math_min'] = $input['undergrad_grade_psat_math_min'] : NULL;
				(isset($input['undergrad_grade_psat_reading_avg']) && !empty($input['undergrad_grade_psat_reading_avg'])) ? $val['undergrad_grade_psat_reading_avg'] = $input['undergrad_grade_psat_reading_avg'] : NULL;
				(isset($input['undergrad_grade_psat_reading_min']) && !empty($input['undergrad_grade_psat_reading_min'])) ? $val['undergrad_grade_psat_reading_min'] = $input['undergrad_grade_psat_reading_min'] : NULL;
				(isset($input['undergrad_grade_sat_math_avg']) && !empty($input['undergrad_grade_sat_math_avg'])) ? $val['undergrad_grade_sat_math_avg'] = $input['undergrad_grade_sat_math_avg'] : NULL;
				(isset($input['undergrad_grade_sat_math_min']) && !empty($input['undergrad_grade_sat_math_min'])) ? $val['undergrad_grade_sat_math_min'] = $input['undergrad_grade_sat_math_min'] : NULL;
				(isset($input['undergrad_grade_sat_reading_avg']) && !empty($input['undergrad_grade_sat_reading_avg'])) ? $val['undergrad_grade_sat_reading_avg'] = $input['undergrad_grade_sat_reading_avg'] : NULL;
				(isset($input['undergrad_grade_sat_reading_min']) && !empty($input['undergrad_grade_sat_reading_min'])) ? $val['undergrad_grade_sat_reading_min'] = $input['undergrad_grade_sat_reading_min'] : NULL;
				(isset($input['undergrad_grade_toefl_avg']) && !empty($input['undergrad_grade_toefl_avg'])) ? $val['undergrad_grade_toefl_avg'] = $input['undergrad_grade_toefl_avg'] : NULL;
				(isset($input['undergrad_grade_toefl_min']) && !empty($input['undergrad_grade_toefl_min'])) ? $val['undergrad_grade_toefl_min'] = $input['undergrad_grade_toefl_min'] : NULL;

				(isset($input['grad_grade_act_composite_avg']) && !empty($input['grad_grade_act_composite_avg'])) ? $val['grad_grade_act_composite_avg'] = $input['grad_grade_act_composite_avg'] : NULL;
				(isset($input['grad_grade_act_composite_min']) && !empty($input['grad_grade_act_composite_min'])) ? $val['grad_grade_act_composite_min'] = $input['grad_grade_act_composite_min'] : NULL;
				(isset($input['grad_grade_act_english_avg']) && !empty($input['grad_grade_act_english_avg'])) ? $val['grad_grade_act_english_avg'] = $input['grad_grade_act_english_avg'] : NULL;
				(isset($input['grad_grade_act_english_min']) && !empty($input['grad_grade_act_english_min'])) ? $val['grad_grade_act_english_min'] = $input['grad_grade_act_english_min'] : NULL;
				(isset($input['grad_grade_act_math_avg']) && !empty($input['grad_grade_act_math_avg'])) ? $val['grad_grade_act_math_avg'] = $input['grad_grade_act_math_avg'] : NULL;
				(isset($input['grad_grade_act_math_min']) && !empty($input['grad_grade_act_math_min'])) ? $val['grad_grade_act_math_min'] = $input['grad_grade_act_math_min'] : NULL;
				(isset($input['grad_grade_gmat_quant_min']) && !empty($input['grad_grade_gmat_quant_min'])) ? $val['grad_grade_gmat_quant_min'] = $input['grad_grade_gmat_quant_min'] : NULL;
				(isset($input['grad_grade_gmat_verbal_avg']) && !empty($input['grad_grade_gmat_verbal_avg'])) ? $val['grad_grade_gmat_verbal_avg'] = $input['grad_grade_gmat_verbal_avg'] : NULL;
				(isset($input['grad_grade_gpa_avg']) && !empty($input['grad_grade_gpa_avg'])) ? $val['grad_grade_gpa_avg'] = $input['grad_grade_gpa_avg'] : NULL;
				(isset($input['grad_grade_gpa_min']) && !empty($input['grad_grade_gpa_min'])) ? $val['grad_grade_gpa_min'] = $input['grad_grade_gpa_min'] : NULL;
				(isset($input['grad_grade_gre_quant_avg']) && !empty($input['grad_grade_gre_quant_avg'])) ? $val['grad_grade_gre_quant_avg'] = $input['grad_grade_gre_quant_avg'] : NULL;
				(isset($input['grad_grade_gre_quant_min']) && !empty($input['grad_grade_gre_quant_min'])) ? $val['grad_grade_gre_quant_min'] = $input['grad_grade_gre_quant_min'] : NULL;
				(isset($input['grad_grade_gre_verbal_avg']) && !empty($input['grad_grade_gre_verbal_avg'])) ? $val['grad_grade_gre_verbal_avg'] = $input['grad_grade_gre_verbal_avg'] : NULL;
				(isset($input['grad_grade_gre_verbal_min']) && !empty($input['grad_grade_gre_verbal_min'])) ? $val['grad_grade_gre_verbal_min'] = $input['grad_grade_gre_verbal_min'] : NULL;
				(isset($input['grad_grade_gre_writing_avg']) && !empty($input['grad_grade_gre_writing_avg'])) ? $val['grad_grade_gre_writing_avg'] = $input['grad_grade_gre_writing_avg'] : NULL;
				(isset($input['grad_grade_gre_writing_min']) && !empty($input['grad_grade_gre_writing_min'])) ? $val['grad_grade_gre_writing_min'] = $input['grad_grade_gre_writing_min'] : NULL;
				(isset($input['grad_grade_ielts_avg']) && !empty($input['grad_grade_ielts_avg'])) ? $val['grad_grade_ielts_avg'] = $input['grad_grade_ielts_avg'] : NULL;
				(isset($input['grad_grade_ielts_min']) && !empty($input['grad_grade_ielts_min'])) ? $val['grad_grade_ielts_min'] = $input['grad_grade_ielts_min'] : NULL;
				(isset($input['grad_grade_psat_math_avg']) && !empty($input['grad_grade_psat_math_avg'])) ? $val['grad_grade_psat_math_avg'] = $input['grad_grade_psat_math_avg'] : NULL;
				(isset($input['grad_grade_psat_math_min']) && !empty($input['grad_grade_psat_math_min'])) ? $val['grad_grade_psat_math_min'] = $input['grad_grade_psat_math_min'] : NULL;
				(isset($input['grad_grade_psat_reading_avg']) && !empty($input['grad_grade_psat_reading_avg'])) ? $val['grad_grade_psat_reading_avg'] = $input['grad_grade_psat_reading_avg'] : NULL;
				(isset($input['grad_grade_psat_reading_min']) && !empty($input['grad_grade_psat_reading_min'])) ? $val['grad_grade_psat_reading_min'] = $input['grad_grade_psat_reading_min'] : NULL;
				(isset($input['grad_grade_sat_math_avg']) && !empty($input['grad_grade_sat_math_avg'])) ? $val['grad_grade_sat_math_avg'] = $input['grad_grade_sat_math_avg'] : NULL;
				(isset($input['grad_grade_sat_math_min']) && !empty($input['grad_grade_sat_math_min'])) ? $val['grad_grade_sat_math_min'] = $input['grad_grade_sat_math_min'] : NULL;
				(isset($input['grad_grade_sat_reading_avg']) && !empty($input['grad_grade_sat_reading_avg'])) ? $val['grad_grade_sat_reading_avg'] = $input['grad_grade_sat_reading_avg'] : NULL;
				(isset($input['grad_grade_sat_reading_min']) && !empty($input['grad_grade_sat_reading_min'])) ? $val['grad_grade_sat_reading_min'] = $input['grad_grade_sat_reading_min'] : NULL;
				(isset($input['grad_grade_toefl_avg']) && !empty($input['grad_grade_toefl_avg'])) ? $val['grad_grade_toefl_avg'] = $input['grad_grade_toefl_avg'] : NULL;
				(isset($input['grad_grade_toefl_min']) && !empty($input['grad_grade_toefl_min'])) ? $val['grad_grade_toefl_min'] = $input['grad_grade_toefl_min'] : NULL;

				(isset($input['epp_grade_act_composite_avg']) && !empty($input['epp_grade_act_composite_avg'])) ? $val['epp_grade_act_composite_avg'] = $input['epp_grade_act_composite_avg'] : NULL;
				(isset($input['epp_grade_act_composite_min']) && !empty($input['epp_grade_act_composite_min'])) ? $val['epp_grade_act_composite_min'] = $input['epp_grade_act_composite_min'] : NULL;
				(isset($input['epp_grade_act_english_avg']) && !empty($input['epp_grade_act_english_avg'])) ? $val['epp_grade_act_english_avg'] = $input['epp_grade_act_english_avg'] : NULL;
				(isset($input['epp_grade_act_english_min']) && !empty($input['epp_grade_act_english_min'])) ? $val['epp_grade_act_english_min'] = $input['epp_grade_act_english_min'] : NULL;
				(isset($input['epp_grade_act_math_avg']) && !empty($input['epp_grade_act_math_avg'])) ? $val['epp_grade_act_math_avg'] = $input['epp_grade_act_math_avg'] : NULL;
				(isset($input['epp_grade_act_math_min']) && !empty($input['epp_grade_act_math_min'])) ? $val['epp_grade_act_math_min'] = $input['epp_grade_act_math_min'] : NULL;
				(isset($input['epp_grade_gmat_quant_min']) && !empty($input['epp_grade_gmat_quant_min'])) ? $val['epp_grade_gmat_quant_min'] = $input['epp_grade_gmat_quant_min'] : NULL;
				(isset($input['epp_grade_gmat_verbal_avg']) && !empty($input['epp_grade_gmat_verbal_avg'])) ? $val['epp_grade_gmat_verbal_avg'] = $input['epp_grade_gmat_verbal_avg'] : NULL;
				(isset($input['epp_grade_gmat_verbal_min']) && !empty($input['epp_grade_gmat_verbal_min'])) ? $val['epp_grade_gmat_verbal_min'] = $input['epp_grade_gmat_verbal_min'] : NULL;
				(isset($input['epp_grade_gpa_avg']) && !empty($input['epp_grade_gpa_avg'])) ? $val['epp_grade_gpa_avg'] = $input['epp_grade_gpa_avg'] : NULL;
				(isset($input['epp_grade_gpa_min']) && !empty($input['epp_grade_gpa_min'])) ? $val['epp_grade_gpa_min'] = $input['epp_grade_gpa_min'] : NULL;
				(isset($input['epp_grade_gre_quant_avg']) && !empty($input['epp_grade_gre_quant_avg'])) ? $val['epp_grade_gre_quant_avg'] = $input['epp_grade_gre_quant_avg'] : NULL;
				(isset($input['epp_grade_gre_quant_min']) && !empty($input['epp_grade_gre_quant_min'])) ? $val['epp_grade_gre_quant_min'] = $input['epp_grade_gre_quant_min'] : NULL;
				(isset($input['epp_grade_gre_verbal_avg']) && !empty($input['epp_grade_gre_verbal_avg'])) ? $val['epp_grade_gre_verbal_avg'] = $input['epp_grade_gre_verbal_avg'] : NULL;
				(isset($input['epp_grade_gre_verbal_min']) && !empty($input['epp_grade_gre_verbal_min'])) ? $val['epp_grade_gre_verbal_min'] = $input['epp_grade_gre_verbal_min'] : NULL;
				(isset($input['epp_grade_gre_writing_avg']) && !empty($input['epp_grade_gre_writing_avg'])) ? $val['epp_grade_gre_writing_avg'] = $input['epp_grade_gre_writing_avg'] : NULL;
				(isset($input['epp_grade_gre_writing_min']) && !empty($input['epp_grade_gre_writing_min'])) ? $val['epp_grade_gre_writing_min'] = $input['epp_grade_gre_writing_min'] : NULL;
				(isset($input['epp_grade_ielts_avg']) && !empty($input['epp_grade_ielts_avg'])) ? $val['epp_grade_ielts_avg'] = $input['epp_grade_ielts_avg'] : NULL;
				(isset($input['epp_grade_ielts_min']) && !empty($input['epp_grade_ielts_min'])) ? $val['epp_grade_ielts_min'] = $input['epp_grade_ielts_min'] : NULL;
				(isset($input['epp_grade_psat_math_avg']) && !empty($input['epp_grade_psat_math_avg'])) ? $val['epp_grade_psat_math_avg'] = $input['epp_grade_psat_math_avg'] : NULL;
				(isset($input['epp_grade_psat_math_min']) && !empty($input['epp_grade_psat_math_min'])) ? $val['epp_grade_psat_math_min'] = $input['epp_grade_psat_math_min'] : NULL;
				(isset($input['epp_grade_psat_reading_avg']) && !empty($input['epp_grade_psat_reading_avg'])) ? $val['epp_grade_psat_reading_avg'] = $input['epp_grade_psat_reading_avg'] : NULL;
				(isset($input['epp_grade_psat_reading_min']) && !empty($input['epp_grade_psat_reading_min'])) ? $val['epp_grade_psat_reading_min'] = $input['epp_grade_psat_reading_min'] : NULL;
				(isset($input['epp_grade_sat_math_avg']) && !empty($input['epp_grade_sat_math_avg'])) ? $val['epp_grade_sat_math_avg'] = $input['epp_grade_sat_math_avg'] : NULL;
				(isset($input['epp_grade_sat_math_min']) && !empty($input['epp_grade_sat_math_min'])) ? $val['epp_grade_sat_math_min'] = $input['epp_grade_sat_math_min'] : NULL;
				(isset($input['epp_grade_sat_reading_avg']) && !empty($input['epp_grade_sat_reading_avg'])) ? $val['epp_grade_sat_reading_avg'] = $input['epp_grade_sat_reading_avg'] : NULL;
				(isset($input['epp_grade_sat_reading_min']) && !empty($input['epp_grade_sat_reading_min'])) ? $val['epp_grade_sat_reading_min'] = $input['epp_grade_sat_reading_min'] : NULL;
				(isset($input['epp_grade_toefl_avg']) && !empty($input['epp_grade_toefl_avg'])) ? $val['epp_grade_toefl_avg'] = $input['epp_grade_toefl_avg'] : NULL;
				(isset($input['epp_grade_toefl_min']) && !empty($input['epp_grade_toefl_min'])) ? $val['epp_grade_toefl_min'] = $input['epp_grade_toefl_min'] : NULL;
				(isset($input['epp_grade_gmat_quant_avg']) && !empty($input['epp_grade_gmat_quant_avg'])) ? $val['epp_grade_gmat_quant_avg'] = $input['epp_grade_gmat_quant_avg'] : NULL;
				(isset($input['epp_grade_gmat_quant_min']) && !empty($input['epp_grade_gmat_quant_min'])) ? $val['epp_grade_gmat_quant_min'] = $input['epp_grade_gmat_quant_min'] : NULL;


				if (isset($val) && !empty($val)) {
					$update = CollegesInternationalTab::updateOrCreate($attr, $val);
					$ret['status'] = "success";
				}else{
					$ret['status'] = "failed";
				}

				break;
			case 'requirements':
				// $this->customdd($input);
				$cit = CollegesInternationalTab::on('rds1')->where('college_id', $college_id)->first();

				if (!isset($cit)) {
					$ret['status'] = "failed";
				}
				$view_type = '';

				foreach ($input as $key => $v) {
					if (strpos($key, 'grad_') !== false) {
						$view_type = 'grad';
					}
					if (strpos($key, 'undergrad_') !== false) {
						$view_type = 'undergrad';
					}
					if (strpos($key, 'epp_') !== false) {
						$view_type = 'epp';
					}
					$org_key = $key;
					$key = str_replace("undergrad", "", $key);
					$key = str_replace("grad", "", $key);
					$key = str_replace("epp", "", $key);
					$key = str_replace("_", "", $key);

					$input[$key] = $input[$org_key];

					if( !isset($input['id']) ){
						unset($input[$org_key]);
					}
				}

				if (isset($input['id'])) {
					$attr = array('id' => $input['id']);
				}
				$val  = array('college_id' => $cit->college_id, 'cit_id' => $cit->id, 'view_type' => $view_type);

				if(isset($input['visa']) && !empty($input['visa'])){
					$val['type'] 		= 'visa';
					$val['title'] 		= $input['visa'];
					$val['description'] = $input['visadescription'];
				}
				if(isset($input['academic']) && !empty($input['academic'])){
					$val['type'] 		= 'academic';
					$val['title'] 		= $input['academic'];
					$val['description'] = $input['academicdescription'];
				}
				if(isset($input['financial']) && !empty($input['financial'])){
					$val['type'] 		= 'financial';
					$val['title'] 		= $input['financial'];
					$val['description'] = $input['financialdescription'];
				}
				if(isset($input['other']) && !empty($input['other'])){
					$val['type'] 		= 'other';
					$val['title'] 		= $input['other'];
					$val['description'] = $input['otherdescription'];
				}

				if (isset($input['attachement'])) {
					$this_ret = $this->generalUploadDoc($input, $view_type.'_attachement', 'asset.plexuss.com/admin/internationalTools');

					if ($this_ret['status'] == 'success') {
						$val['attachment_url'] = $this_ret['url'];
					}
				}

				if (!isset($attr)) {
					$attr = $val;
				}

				if (isset($val) && !empty($val)) {
					$update = CollegesInternationalRequirment::updateOrCreate($attr, $val);
					$ret['requirement_id'] = (int)$update->id;
					$ret['status'] = "success";
				}else{
					$ret['status'] = "failed";
				}

				break;
			case 'majors':

				$cit = CollegesInternationalTab::on('rds1')->where('college_id', $college_id)->first();

				if (!isset($cit)) {
					$ret['status'] = "failed";
				}
				// Remove the existing majors
				$cim = CollegesInternationalMajor::where('cit_id', $cit->id)->delete();
				$dt  = array();

				if (isset($input['departments']) && $input['departments'] == 'all') {
					$degree = Degree::on('rds1')->whereNotIn('id', array(6,8,9))->get();

					$dep    = new Department;
					$dep    = $dep->getConcatAllDepAndMajor();

					foreach ($dep as $key) {
						foreach ($degree as $k) {
							$tmp = array();

							$tmp['cit_id']     = $cit->id;
							$tmp['college_id'] = $college_id;
							$tmp['type']	   = 'include';
							$tmp['val']		   = $key->depMajor.",".$k->id;
							$tmp['created_at'] = date('Y-m-d H:i:s');
							$tmp['updated_at'] = date('Y-m-d H:i:s');

							$dt[] = $tmp;
						}
					}
				}elseif (isset($input['departments'])) {
					foreach ($input['departments'] as $key) {
						$tmp = array();

						$tmp['cit_id'] 	   = $cit->id;
						$tmp['college_id'] = $college_id;
						$tmp['type']	   = 'include';

						$dep_id    = $key['department_id'];
						$major_id  = $key['major_id'];
						$degree_id = isset($key['degree_id']) ? $key['degree_id'] : '';

						$tmp['val']		   = $dep_id.",".$major_id.",".$degree_id;
						$tmp['created_at'] = date('Y-m-d H:i:s');
						$tmp['updated_at'] = date('Y-m-d H:i:s');

						$dt[] = $tmp;

					}
				}

				$update = CollegesInternationalMajor::insert($dt);

				$ret['status'] = "success";

				break;
			case 'alumni':

				$cit = CollegesInternationalTab::on('rds1')->where('college_id', $college_id)->first();

				if (!isset($cit)) {
					$ret['status'] = "failed";
				}
				if (isset($input['id'])) {
					$attr = array('id' => $input['id']);
				}

				$val  = array('college_id' => $cit->college_id, 'cit_id' => $cit->id);

				if (isset($input['alumni_name'])) {
					$val['alumni_name']  = $input['alumni_name'];
				}
				if (isset($input['dep_id'])) {
					$val['dep_id']  = $input['dep_id'];
				}
				if (isset($input['grad_year'])) {
					$val['grad_year']  = $input['grad_year'];
				}
				if (isset($input['location'])) {
					$val['location']  = $input['location'];
				}
				if (isset($input['linkedin'])) {
					$val['linkedin']  = $input['linkedin'];
				}

				if (isset($input['photo_url'])) {
					$this_ret = $this->generalUploadDoc($input, 'photo_url', 'asset.plexuss.com/college/alumni');

					if ($this_ret['status'] == 'success') {
						$val['photo_url'] = str_replace("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/alumni/", "", $this_ret['url']);
					}
				}

				if (!isset($attr)) {
					$attr = $val;
				}

				$update = CollegesInternationalAlum::updateOrCreate($attr, $val);

				$ret['alumni_id'] = (int)$update->id;
				$ret['status'] = "success";

				break;

			default:
				# code...
				break;
		}

		return $ret;
	}

	public function internationalTuitionCostSave(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$college_id = $data['org_school_id'];

		$attr = array('college_id' => $college_id);
		$val  = array('college_id' => $college_id);

		(isset($input['undergrad_application_fee']) && !empty($input['undergrad_application_fee'])) ? $val['undergrad_application_fee'] = $input['undergrad_application_fee'] : NULL;
		(isset($input['undergrad_avg_tuition'])     && !empty($input['undergrad_avg_tuition'])) ? $val['undergrad_avg_tuition'] = $input['undergrad_avg_tuition'] : NULL;
		(isset($input['undergrad_other_cost'])      && !empty($input['undergrad_other_cost'])) ? $val['undergrad_other_cost'] = $input['undergrad_other_cost'] : NULL;
		(isset($input['undergrad_avg_scholarship']) && !empty($input['undergrad_avg_scholarship'])) ? $val['undergrad_avg_scholarship'] = $input['undergrad_avg_scholarship'] : NULL;
		(isset($input['undergrad_avg_work_study'])  && !empty($input['undergrad_avg_work_study'])) ? $val['undergrad_avg_work_study'] = $input['undergrad_avg_work_study'] : NULL;
		(isset($input['undergrad_other_financial']) && !empty($input['undergrad_other_financial'])) ? $val['undergrad_other_financial'] = $input['undergrad_other_financial'] : NULL;

		(isset($input['grad_application_fee'])      && !empty($input['grad_application_fee'])) ? $val['grad_application_fee'] = $input['grad_application_fee'] : NULL;
		(isset($input['grad_avg_tuition'])          && !empty($input['grad_avg_tuition'])) ? $val['grad_avg_tuition'] = $input['grad_avg_tuition'] : NULL;
		(isset($input['grad_other_cost']) 			&& !empty($input['grad_other_cost'])) ? $val['grad_other_cost'] = $input['grad_other_cost'] : NULL;
		(isset($input['grad_avg_scholarship']) 		&& !empty($input['grad_avg_scholarship'])) ? $val['grad_avg_scholarship'] = $input['grad_avg_scholarship'] : NULL;
		(isset($input['grad_avg_work_study']) 		&& !empty($input['grad_avg_work_study'])) ? $val['grad_avg_work_study'] = $input['grad_avg_work_study'] : NULL;
		(isset($input['grad_other_financial']) 		&& !empty($input['grad_other_financial'])) ? $val['grad_other_financial'] = $input['grad_other_financial'] : NULL;

		(isset($input['epp_application_fee'])       && !empty($input['epp_application_fee'])) ? $val['epp_application_fee'] = $input['epp_application_fee'] : NULL;
		(isset($input['epp_avg_tuition'])           && !empty($input['epp_avg_tuition'])) ? $val['epp_avg_tuition'] = $input['epp_avg_tuition'] : NULL;
		(isset($input['epp_other_cost']) 			&& !empty($input['epp_other_cost'])) ? $val['epp_other_cost'] = $input['epp_other_cost'] : NULL;
		(isset($input['epp_avg_scholarship']) 		&& !empty($input['epp_avg_scholarship'])) ? $val['epp_avg_scholarship'] = $input['epp_avg_scholarship'] : NULL;
		(isset($input['epp_avg_work_study']) 		&& !empty($input['epp_avg_work_study'])) ? $val['epp_avg_work_study'] = $input['epp_avg_work_study'] : NULL;
		(isset($input['epp_other_financial']) 		&& !empty($input['epp_other_financial'])) ? $val['epp_other_financial'] = $input['epp_other_financial'] : NULL;

		(isset($input['quick_tip']) 				&& !empty($input['quick_tip'])) ? $val['quick_tip'] = $input['quick_tip'] : NULL;

		if (isset($input['company_logo_file'])) {
			// asset.plexuss.com/admin/internationalTools/tuitionCosts
			$bucket_url = 'asset.plexuss.com/admin/internationalTools/tuitionCosts';
			$upload = $this->generalUploadDoc($input, 'company_logo_file', $bucket_url);

			$val['company_logo_file'] = $upload['url'];
		}

		if (count($val) == 1) {
			$ret['status'] = 'failed';
		}else{
			$ret['status'] = 'success';

			$update = CollegesInternationalTuitionCost::updateOrCreate($attr, $val);
		}

		return $ret;
	}

	public function getInternationalMajorDegreeTab(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$cim = CollegesInternationalMajor::on('rds1')->where('college_id', $data['org_school_id']);
		$cnt = $cim->count();
		$ret = array();

		// if you havent set  majors, or you have selected all departments and majors return this.
		if ($cnt == 0 || $cnt == 8832) {
			$ret['department_option'] = 'all';

			return $ret;
		}

		$ret['department_option'] = 'include';
		$cim = $cim->get();
		$dep_arr = array();
		$major_arr = array();

		$ret['departments'] = array();

		foreach ($cim as $key) {
			$key_arr = explode(",", $key->val);

			$this_dept_id   = $key_arr[0];
			$this_major_id  = $key_arr[1];
			$this_degree_id = $key_arr[2];

			if (in_array($this_dept_id, $dep_arr)) {

				for ($i=0; $i < count($ret['departments']); $i++) {
					if($ret['departments'][$i]['id'] == $this_dept_id){
						if ($this_degree_id == 1) {
							$ret['departments'][$i]['certificate'] = true;
						}
						if ($this_degree_id == 2) {
							$ret['departments'][$i]['associate'] = true;
						}
						if ($this_degree_id == 3) {
							$ret['departments'][$i]['bachelor'] = true;
						}
						if ($this_degree_id == 4) {
							$ret['departments'][$i]['master'] = true;
						}
						if ($this_degree_id == 5) {
							$ret['departments'][$i]['doctorate'] = true;
						}

						break;
					}
				}
			}else{
				$dep_arr[] = $this_dept_id;
				$tmp = array();

				$tmp['id'] = (int)$this_dept_id;

				$dep_name  = Department::on('rds1')->find($this_dept_id);
				$dep_name  = $dep_name->name;

				$tmp['name'] = $dep_name;

				if(empty($this_major_id)){
					if ($this_degree_id == 1) {
						$tmp['certificate'] = true;
					}else{
						$tmp['certificate'] = false;
					}
					if ($this_degree_id == 2) {
						$tmp['associate'] = true;
					}else{
						$tmp['associate'] = false;
					}
					if ($this_degree_id == 3) {
						$tmp['bachelor'] = true;
					}else{
						$tmp['bachelor'] = false;
					}
					if ($this_degree_id == 4) {
						$tmp['master'] = true;
					}else{
						$tmp['master'] = false;
					}
					if ($this_degree_id == 5) {
						$tmp['doctorate'] = true;
					}else{
						$tmp['doctorate'] = false;
					}

				}

				$ret['departments'][] = $tmp;
			}

			if(empty($this_major_id)){
				$ret['option_for_dept_majors_'.$this_dept_id] = 'all';

			}else{
				$ret['option_for_dept_majors_'.$this_dept_id] = 'include';

				if (isset($ret['selected_majors_for_dept_'.$this_dept_id])) {

					$tmp_selected_major_for_dept = $ret['selected_majors_for_dept_'.$this_dept_id];

					if (in_array($this_major_id, $major_arr)) {

						for ($i=0; $i < count($ret['selected_majors_for_dept_'.$this_dept_id]); $i++) {
							if ($ret['selected_majors_for_dept_'.$this_dept_id][$i]['id'] == $this_major_id) {

								if ($this_degree_id == 1) {
									$ret['selected_majors_for_dept_'.$this_dept_id][$i]['certificate'] = true;
								}
								if ($this_degree_id == 2) {
									$ret['selected_majors_for_dept_'.$this_dept_id][$i]['associate'] = true;
								}
								if ($this_degree_id == 3) {
									$ret['selected_majors_for_dept_'.$this_dept_id][$i]['bachelor'] = true;
								}
								if ($this_degree_id == 4) {
									$ret['selected_majors_for_dept_'.$this_dept_id][$i]['master'] = true;
								}
								if ($this_degree_id == 5) {
									$ret['selected_majors_for_dept_'.$this_dept_id][$i]['doctorate'] = true;
								}

								break;
							}
						}

					}else{
						$tmp = array();

						if ($this_degree_id == 1) {
							$tmp['certificate'] = true;
						}else{
							$tmp['certificate'] = false;
						}
						if ($this_degree_id == 2) {
							$tmp['associate'] = true;
						}else{
							$tmp['associate'] = false;
						}
						if ($this_degree_id == 3) {
							$tmp['bachelor'] = true;
						}else{
							$tmp['bachelor'] = false;
						}
						if ($this_degree_id == 4) {
							$tmp['master'] = true;
						}else{
							$tmp['master'] = false;
						}
						if ($this_degree_id == 5) {
							$tmp['doctorate'] = true;
						}else{
							$tmp['doctorate'] = false;
						}

						$tmp['id'] 		= (int)$this_major_id;
						$tmp['dept_id'] = (int)$this_dept_id;

						$major_name = Major::on('rds1')->find($this_major_id);
						$major_name = $major_name->name;

						$tmp['name'] = $major_name;
						$major_arr[] = $this_major_id;

						$ret['selected_majors_for_dept_'.$this_dept_id][] = $tmp;
					}

				}else{
					$ret['selected_majors_for_dept_'.$this_dept_id] = array();
					$tmp = array();

					if ($this_degree_id == 1) {
						$tmp['certificate'] = true;
					}else{
						$tmp['certificate'] = false;
					}
					if ($this_degree_id == 2) {
						$tmp['associate'] = true;
					}else{
						$tmp['associate'] = false;
					}
					if ($this_degree_id == 3) {
						$tmp['bachelor'] = true;
					}else{
						$tmp['bachelor'] = false;
					}
					if ($this_degree_id == 4) {
						$tmp['master'] = true;
					}else{
						$tmp['master'] = false;
					}
					if ($this_degree_id == 5) {
						$tmp['doctorate'] = true;
					}else{
						$tmp['doctorate'] = false;
					}

					$tmp['id'] 		= (int)$this_major_id;
					$tmp['dept_id'] = (int)$this_dept_id;

					$major_name = Major::on('rds1')->find($this_major_id);
					$major_name = $major_name->name;

					$tmp['name'] = $major_name;
					$major_arr[] = $this_major_id;

					$ret['selected_majors_for_dept_'.$this_dept_id][] = $tmp;
				}
			}
		}

		// echo "<pre>";
		// print_r($ret);
		// echo "</pre>";
		// exit();
		return $ret;
	}

	public function getPortalsForInternationalTab(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$qry = DB::connection('rds1')->table('college_recommendation_filters as crf')
									 ->where('crf.type', 'include')
									 ->where('crf.category', 'majorDeptDegree')
									 ->where('crf.name', 'majorDeptDegree')
									 ->where('crf.college_id', $data['org_school_id']);

		if($data['is_aor'] == 0){
			$qry = $qry->leftjoin('organization_portals as op', 'op.id', '=', 'crf.org_portal_id')
					   ->where('op.active', 1)
					   ->whereNull('crf.aor_id')
					   ->whereNull('crf.is_aor')
					   ->whereNull('crf.aor_portal_id')
					   ->select('op.name', 'op.id', 'crf.id as crf_id');


		}else{
			$qry = $qry->where('crf.is_aor', 1)
					   ->leftjoin('aor_portals as ap' , 'ap.id', '=', 'crf.aor_portal_id')
					   ->select('ap.name', 'ap.id', 'crf.id as crf_id');
		}

		$qry = $qry->get();

		return $qry;
	}

	public function importMajorsFromTargetting(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$college_id  = $data['org_school_id'];
		$cit = CollegesInternationalTab::on('rds1')->where('college_id', $college_id)->first();

		if (!isset($cit) || !isset($input['crf_id'])) {
			$ret['status'] = "failed";
		}
		// Remove the existing majors
		$cim  = CollegesInternationalMajor::where('cit_id', $cit->id)->delete();

		$crfl = CollegeRecommendationFilterLogs::on('rds1')->where('rec_filter_id', $input['crf_id'])->get();

		$dt   = array();

		foreach ($crfl as $key) {
			$tmp = array();

			$tmp['cit_id']     = $cit->id;
			$tmp['college_id'] = $college_id;
			$tmp['type']	   = 'include';
			$tmp['val']		   = $key->val;
			$tmp['created_at'] = date('Y-m-d H:i:s');
			$tmp['updated_at'] = date('Y-m-d H:i:s');

			$dt[] = $tmp;

		}
		$update = CollegesInternationalMajor::insert($dt);

		$ret['data'] = $this->getInternationalMajorDegreeTab();
		$ret['status'] = 'success';

		return $ret;
	}

	public function removeVideoTestimonial(){
		$input = Request::all();
		if (!isset($input['id'])) {
			return "failed";
		}
		$cit = new CollegesInternationalTestimonial;

		return $cit->removeVideoTestimonial($input['id']);
	}
	public function removeInternationalAlumni(){
		$input = Request::all();
		if (!isset($input['id'])) {
			return "failed";
		}
		$cit = new CollegesInternationalAlum;

		return $cit->removeInternationalAlumni($input['id']);
	}

	public function removeInternationalRequirment(){
		$input = Request::all();
		if (!isset($input['id'])) {
			return "failed";
		}
		$cit = new CollegesInternationalRequirment;

		$tmp = $cit->removeInternationalAttachment($input['id']);

		return $cit->removeInternationalRequirment($input['id']);
	}

	public function removeInternationalAttachment(){
		$input = Request::all();
		if (!isset($input['id'])) {
			return "failed";
		}
		$cit = new CollegesInternationalRequirment;

		return $cit->removeInternationalAttachment($input['id']);
	}

	public function removeTranscriptAttachment($api_input = null){
		if( isset($api_input) ){
			$data = array();
			$data['user_id'] = $api_input['user_id'];

			$input = $api_input;

		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);

			$input = Request::all();
		}

		if (!isset($input['transcript_id'])) {
			return "failed";
		}

		if (isset($input['hashed_user_id'])) {
            $data['user_id'] = Crypt::decrypt($input['hashed_user_id']);
        }

		$t = new Transcript;

		return $t->removeAttachment($input['transcript_id'], $data['user_id']);
	}

	// Admin International Tools ends here

	// Admin Overview Tools starts here
	public function uploadOverviewImages(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$maxWidth = 830;
		$maxHeight = 380;

		$imgSize = getimagesize(Request::file('overview_image'));

		if( $imgSize[0] === $maxWidth && $imgSize[1] === $maxHeight ){
			$response = array();

			$bucket_url 	  = 'asset.plexuss.com/college/overview_images';
			$file_upload_name = 'overview_image';

			$ret = $this->generalUploadDoc($input, $file_upload_name, $bucket_url, $data['org_school_id']);
			if (!isset($ret['url'])) {
				return $response["status"] = "failed";
			}
			$file_name = substr($ret['url'], strrpos($ret['url'], '/') + 1);

			$attr = array('college_id' => $data['org_school_id'], 'url' => $file_name);
			$val  = array('college_id' => $data['org_school_id'], 'url' => $file_name, 'title' => $data['school_name'],
						  'is_video' => 0, 'is_tour' => 0, 'is_youtube' => 1);

			$update = CollegeOverviewImages::updateOrCreate($attr, $val);

			$response['status']  = "success";
			$response['url']	 = $ret['url'];
			$response['id']      = Crypt::encrypt($update->id);

			return $response;

		}

		return 'wrong dimensions';
	}

	public function uploadOverviewVideo(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$response = array();

		$attr = array('college_id' => $data['org_school_id'], 'video_id' => $input['video_id'], 'is_youtube' => $input['is_youtube']);
		$val  = array('college_id' => $data['org_school_id'], 'video_id' => $input['video_id'],
					  'title' => $data['school_name'], 'is_video' => 1, 'section' => $input['section'],
					  'is_tour' => 0, 'is_youtube' => $input['is_youtube']);

		$update = CollegeOverviewImages::updateOrCreate($attr, $val);


		$response['status']  = "success";
		$response['id']      = Crypt::encrypt($update->id);

		Cache::forget(env('ENVIRONMENT') .'_'.'college_overview_' . $data['org_school_id'] );

		return $response;
	}

	public function removeOverviewImageVideo(){
		$data = array();
    	$data['org_school_id'] = Session::get('userinfo.school_id');

		$input = Request::all();

		try {
			$id = Crypt::decrypt($input['id']);

			$coi = CollegeOverviewImages::find($id);
			if (isset($coi->url) && !empty($coi->url) && $coi->is_video == 0) {
				$this->generalDeleteFile('asset.plexuss.com/college/overview_images', $coi->url);
			}
			$coi->delete();

			Cache::forget(env('ENVIRONMENT') .'_'.'college_overview_' . $data['org_school_id'] );

			return "success";
		} catch (\Exception $e) {
			return "failed";
		}
	}

	public function saveOverviewContent(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$college = College::find($data['org_school_id']);
		if (isset($input['overview_content'])) {
			$college->overview_content = $input['overview_content'];
		}
		$college->overview_source  = isset($input['overview_source']) ? $input['overview_source'] : 'Wikipedia';

		$college->save();

		Cache::forget(env('ENVIRONMENT') .'_'.'college_overview_' . $data['org_school_id'] );
		Cache::forget(env('ENVIRONMENT') .'_'.'college_'.$data['school_slug']);

		return "success";
	}
	// Admin Overview tools ends here


	public function uploadPreScreenFile(){

		$input = Request::all();

		$bucket_url 	  = 'asset.plexuss.com/users/prescreened';
		$file_upload_name = 'prescreen_upload';

		$transcript_path  = 'https://s3-us-west-2.amazonaws.com/'.$bucket_url.'/';

		$ret = $this->generalUploadDoc($input, $file_upload_name, $bucket_url);

		if (!isset($ret['url'])) {
			return "failed";
		}
		$transcript_name = substr($ret['url'], strrpos($ret['url'], '/') + 1);

		$attr = array('user_id' => $input['user_id'], 'transcript_name' => $transcript_name,
					  'transcript_path' => $transcript_path);

		$val  = array('user_id' => $input['user_id'], 'transcript_name' => $transcript_name,
					  'transcript_path' => $transcript_path, 'school_type' => 'highschool',
					  'doc_type' => $input['docType'].'_interview');

		$update = Transcript::updateOrCreate($attr, $val);

		return "success";
	}

	public function getApplicationLink(){
		$data = array();
    	$data['org_school_id'] = Session::get('userinfo.school_id');
        $data['school_slug'] = Session::get('userinfo.school_slug');
        $data['college_page_url'] = '/college/'.$data['school_slug'].'/overview';

    	$college = College::find($data['org_school_id']);

    	$data['application_url'] = $college->application_url;

    	return $data;
	}

	public function saveApplicationLink(){
		$input = Request::all();

		$data = array();
    	$data['org_school_id'] = Session::get('userinfo.school_id');

    	$college = College::find($data['org_school_id']);

    	$college->application_url = $input['application_link'];
    	$college->paid_app_url = $input['application_link'];

    	$college->save();

		Cache::forget(env('ENVIRONMENT') .'_'.'college_overview_' . $data['org_school_id'] );

		return $input['application_link'];
	}


	public function getStudentData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
		$data['appointment_set'] = (int)$data['appointment_set'];
		$data['bachelor_plan'] = (int)$data['bachelor_plan'];
		$data['completed_signup'] = (int)$data['completed_signup'];
		$data['country_id'] = (int)$data['country_id'];
		$data['profile_percent'] = (int)$data['profile_percent'];

		$user = User::on('rds1')->find($data['user_id']);
		$url = "https://gpx.globalpay.wu.com/geo-buyer/sso/plain/?clientId=1000127030&clientReference";

		isset($user->id)      ? $url .= "&buyer.id=". $user->id : null;
		isset($user->fname)   ? $url .= "&buyer.firstName=". urlencode($user->fname) : null;
		isset($user->lname)   ? $url .= "&buyer.lastName=". urlencode($user->lname) : null;
		isset($user->address) ? $url .= "&buyer.address=". urlencode($user->address) : null;
		isset($user->city)    ? $url .= "&buyer.city=". urlencode($user->city) : null;
		isset($user->state)   ? $url .= "&buyer.state=". urlencode($user->state) : null;
		isset($user->zip)     ? $url .= "&buyer.zip=". urlencode($user->zip) : null;

		if (isset($user->country_id)) {
			$cntry = Country::on('rds1')->find($user->country_id);
			isset($cntry->sso_country_code) ? $url .= "&buyer.country=". urlencode($cntry->sso_country_code) : null;
		}

		isset($user->email)   ? $url .= "&buyer.email=". urlencode($user->email) : null;

		$data['western_union__onetime'] 	  = $url.'&service1.id=100000147032&service1.amount=9.99';
		$data['western_union__onetime_plus']  = $url.'&service1.id=100000147033&service1.amount=199';

		return $data;
	}

	public function getProfileData($userId = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);



		if( !isset($userId) ){
			$user_id = Session::get('userinfo.id');
		}else{
			$user_id = Crypt::decrypt($userId);
		}

		if ( (isset($data['user_id']) && $data['user_id'] == $user_id) ||
			 ($data['user_id'] == 1408142) || // MyCounselor account
			 (isset($data['is_organization']) && $data['is_organization'] == 1) ||
			 (isset($data['is_agency']) && $data['is_agency'] == 1)) {

			(!isset($user_id)) ? $user_id = $data['user_id'] : NULL;
			$user = new User;
			$profile = $user->getUsersProfileData($user_id);

			$npc = new NewPortalController;
			$profile['MyApplicationList'] = $npc->applicationData(true, $user_id);
			$profile['MyCollegeList'] 	  = $npc->getManageSchool(true, $user_id);

			$profile['MyApplicationList'] = $profile['MyApplicationList']['colleges'];
			$profile['MyCollegeList']     = $profile['MyCollegeList']['colleges'];
			$profile['applyTo_schools']   = $profile['MyApplicationList'];

			return json_encode($profile);

		}else{
			return NULL;
		}

	}


	/****************************************************
	*  gets a student user's profile information  - basic. getUsersProfileData returns much more
	*******************************************************/
	public function getStudentProfile($userId = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		if( !isset($userId) ){
			$user_id = Session::get('userinfo.id');
		}else{
			$user_id = Crypt::decrypt($userId);
		}

		if ( (isset($data['user_id']) && $data['user_id'] == $user_id) ||
			 (isset($data['is_organization']) && $data['is_organization'] == 1) ||
			 (isset($data['is_agency']) && $data['is_agency'] == 1)) {

			$user = new User;
			$social = new SocialController;
			$profile = $user->getUsersInfo($user_id);
			$profile->user_id = $social->hashIdForSocial($profile->user_id);

			return json_encode($profile);

		}else{
			return NULL;
		}

	}


	/****************************************************************
	*	updates a student's profile given arbitrary fields
	******************************************************************/
	public function updateStudentProfile(){
		$input = Request::all();

		dd($input);
	}



	public function getGradeConversions(){
		$input = Request::all();
		$gc = new GradeConversions;
		$grades = $gc->getConversionsFor($input['name']);
		return $grades;
	}

	public function getGPAGradingScales($country_id = null){
		return GPAConverterHelper::getCountryGradingScales($country_id);
	}

	public function convertToUnitedStatesGPA($gch_id = null, $old_value = null, $conversion_type = null){
		return GPAConverter::convertToUnitedStatesGPA($gch_id, $old_value, $conversion_type);
	}

	public function getAllLanguages(){
		$lang = DB::connection('rds1')
			       ->table('languages')
			       ->get();

		foreach( $lang as $key ){
			$key->id = (int)$key->id;
		}

		return $lang;
	}

	public function saveCollegeApplication($api_input = null){

		if( isset($api_input) ){
			$data = $api_input;
			$input = $api_input;

			if (!isset($data['user_id']) && isset($data['id'])) {
				$data['user_id'] = $data['id'];
			}
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);

			$input = Request::all();
		}

		$ret = array();

		$page = $input['page'];

		$user_id = $data['user_id'];

		if( isset($input['impersonateAs_id']) ){
			try{
				$user_id = Crypt::decrypt($input['impersonateAs_id']);
			}catch(Exception $e){
				return "Bad user id";
			}
		}

		switch( $page ){

			case 'basic':
				$in_college = $input['in_college'];
				$schoolName = $input['schoolName'];
				$grad_year  = $input['grad_year'];
				$degree_id  = $input['degree_id'];

				isset($input['majors'])     ? $majors  = $input['majors'] 	  : NULL;
				isset($input['majors_arr']) ? $majors  = $input['majors_arr'] : NULL;

				$is_transfer= $input['is_transfer'];
                $gender     = $input['gender'];

				if ($in_college == 0) {
					$hs = new Highschool;

					$current_school = $hs->findHighschoolsCustom($schoolName);
					$current_school_id = NULL;

					foreach ($current_school as $key) {
						$current_school_id = $key->id;
						break;
					}

				}else{
					$c  = new College;

					$current_school = $c->findCollegesCustomVerifiedNotVerified($schoolName);
					$current_school_id = NULL;

					foreach ($current_school as $key) {
						$current_school_id = $key->id;
						break;
					}
				}

				if (!isset($current_school_id)) {
					$new_school = $in_college == 1 ? new College : new Highschool;
					$new_school->school_name = $schoolName;
					$new_school->verified = 0;
					$new_school->user_id_submitted = $user_id;
					$new_school->save();

					$current_school_id = $new_school->id;
				}

				$user = User::find($user_id);

                if (isset($gender)) {
                    $user->gender = $gender;
                }

				$user->completed_signup = 1;
				$user->in_college = $in_college;

				if ($in_college == 0) {
					$user->hs_grad_year = $grad_year;
				}else{
					$user->college_grad_year = $grad_year;
				}

				$user->current_school_id = $current_school_id;

				if ($user->is_student == 0 && $user->is_intl_student == 0 && $user->is_alumni == 0 && $user->is_parent == 0 &&
					$user->is_counselor == 0 && $user->is_university_rep == 0 && $user->is_organization == 0 && $user->is_aor == 0 &&
					$user->is_plexuss == 0 &&  $user->is_agency == 0) {

					$user->is_student = 1;
				}

				$user->save();

				$obj = Objective::where('user_id', $user_id)->first();

				if (isset($obj)) {

					$delete = Objective::where('user_id', $user_id)->delete();

					foreach ($majors as $key => $value) {

						$tmp = new Objective;

						$tmp->user_id 			  = $user_id;
						$tmp->degree_type 		  = $degree_id;
						$tmp->major_id  		  = $value;
						$tmp->profession_id 	  = $obj->profession_id;
						$tmp->school_type 		  = $obj->school_type;
						$tmp->obj_text  		  = $obj->obj_text;
						$tmp->university_location = $obj->university_location;
						$tmp->whocansee           = $obj->whocansee;

						$tmp->save();
					}
				}else{
					foreach ($majors as $key => $value) {
						$obj = new Objective;

						$obj->user_id 	  = $user_id;
						$obj->degree_type = $degree_id;
						$obj->major_id    = $value;

						$obj->save();
					}
				}

				$update = Objective::where('user_id', $user_id)
								   ->update(array('degree_type' => $degree_id));

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id, 'is_transfer' => $input['is_transfer']);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'start':
				$user = User::find($user_id);

				isset($input['planned_start_term']) ? $user->planned_start_term = $input['planned_start_term'] : NULL;
				isset($input['planned_start_yr'])   ? $user->planned_start_yr = $input['planned_start_yr'] : NULL;
                isset($input['interested_school_type'])   ? $user->interested_school_type = $input['interested_school_type'] : NULL;

				$user->completed_signup = 1;

				$user->save();

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'identity':
				$user = User::find($user_id);

				isset($input['fname']) 		? $user->fname = $input['fname'] : NULL;
				isset($input['lname']) 		? $user->lname = $input['lname'] : NULL;
				isset($input['birth_date']) ? $user->birth_date = $input['birth_date'] : NULL;
				$user->completed_signup = 1;

				//-------------
				if (isset($input['birth_date'])) {
					$today = date("Y-m-d");
					$diff = date_diff(date_create($input['birth_date']), date_create($today));
					$age = $diff->format('%y');
				}
				//------------
				if (preg_match('/[0-9]/', $input['fname']))
				{
					$ret['status']    = "failed";
					$ret['error_msg'] = 'Enter valid first name.';
					break;
				}
				if (preg_match('/[0-9]/', $input['lname']))
				{
					$ret['status']    = "failed";
					$ret['error_msg'] = 'Enter valid last name.';
					break;
				}
				if($age <13){
					$ret['status'] = 'failed';
					$ret['error_msg'] = 'Age should be 13 years or older';
					break;

				}

				$user->save();

				if (isset($input["alternate_name_used"]) && $input["alternate_name_used"]=="yes") {
					$attr = array('user_id' => $user_id);
					$val  = array('user_id' => $user_id, 'alternate_name' => $input['alternate_name']);

					// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
					$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);
				}else{
					$attr = array('user_id' => $user_id);
					$val  = array('user_id' => $user_id);

					// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
					$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);
				}

				$ret['status'] = 'success';
				break;
			case 'contact':
				$user = User::find($user_id);

				$user->completed_signup = 1;

				if (isset($input['email'])) {
					$tmp = User::on('rds1')->where('email', trim($input['email']))
										   ->where('id', '!=', $user_id)
										   ->first();

					if (isset($tmp)) {
						$ret['status']    = "failed";
						$ret['error_msg'] = 'Email already exists! Please choose another email address.';
						break;
					}
				}

				isset($input['email']) 	  ? $user->email    = trim($input['email']) : NULL;
				isset($input['skype_id']) ? $user->skype_id = trim($input['skype_id']) : NULL;
				isset($input['phone'])    ? $user->phone    = '+' . trim($input['phone_code']) . ' '. $input['phone'] : NULL;
				isset($input['line1'])    ? $user->address  = $input['line1'] : NULL;
				isset($input['city'])	  ? $user->city     = $input['city'] : NULL;

				if ($input['txt_opt_in'] == 0) {
					$input['txt_opt_in'] = -1;
				}
				isset($input['txt_opt_in']) ? $user->txt_opt_in = $input['txt_opt_in'] : NULL;

				if (preg_match('/[0-9]/', $input['city']))
				{
					$ret['status']    = "failed";
					$ret['error_msg'] = 'Enter valid city name.';
					break;
				}

				if (isset($input['state_id']) && !empty($input['state_id'])) {
					$state = State::on('rds1')->find($input['state_id']);
					if (isset($state)) {
						$user->state = $state->state_abbr;
					}else{
						$user->state = $input['state_id'];
					}

				}else if(isset($input['state']) && !empty($input['state'])){
					$user->state = $input['state'];
					if (preg_match('/[0-9]/', $input['state']))
					{
					  	$ret['status']    = "failed";
						$ret['error_msg'] = 'Enter valid state name.';
						break;
					}
				}

        		isset($input['country_id']) ? $user->country_id = $input['country_id'] : NULL;

				isset($input['zip']) ? $user->zip = $input['zip'] : NULL;

				$user->save();

				(isset($input['alternate_phone']) && isset($input['alternate_phone_code'])) ? $alternate_phone = '+' . trim($input['alternate_phone_code']). ' '. trim($input['alternate_phone']) : NULL;


				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				isset($input['preferred_phone'])		   ? $val['preferred_phone'] = $input['preferred_phone'] : NULL;
				isset($alternate_phone) 				   ? $val['alternate_phone'] = $alternate_phone : NULL;
				isset($input['preferred_alternate_phone']) ? $val['preferred_alternate_phone'] = $input['preferred_alternate_phone'] : NULL;
				isset($input['line2']) 				       ? $val['address2'] = $input['line2'] : NULL;
				isset($input['alternate_line1']) 	  	   ? $val['alternate_line1'] = $input['alternate_line1'] : NULL;
				isset($input['alternate_line2']) 	  	   ? $val['alternate_line2'] = $input['alternate_line2'] : NULL;
				isset($input['alternate_city']) 	  	   ? $val['alternate_city'] = $input['alternate_city'] : NULL;
				isset($input['alternate_state_id'])   	   ? $val['alternate_state_id'] = $input['alternate_state_id'] : NULL;
				isset($input['alternate_state'])   	   	   ? $val['alternate_state'] = $input['alternate_state'] : NULL;

				if (isset($input['alternate_state_id']) && !empty($input['alternate_state_id'])) {
					$state = State::on('rds1')->find($input['alternate_state_id']);
					if (isset($state)) {
						$val['alternate_state_id'] = $state->state_abbr;
					}else{
						$val['alternate_state_id'] = $input['alternate_state_id'];
					}

				}if(isset($input['alternate_state']) && !empty($input['alternate_state'])){
					$val['alternate_state'] = $input['alternate_state'];
				}

				isset($input['alternate_country_id']) 	   ? $val['alternate_country_id'] = $input['alternate_country_id'] : NULL;
				isset($input['alternate_zip'])			   ? $val['alternate_zip'] = $input['alternate_zip'] : NULL;

				// $val['application_state'] = $page;

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'study':

				$countries_to_study_in  = isset($input['countries_to_study_in']) ? $input['countries_to_study_in'] : NULL;

				if (isset($countries_to_study_in)) {
					$dt = implode(",", $countries_to_study_in);

					$update = Objective::where('user_id', $user_id)
										   ->update(array('university_location' => $dt));

				}else{
					$update = Objective::where('user_id', $user_id)
										   ->update(array('university_location' => NULL));
				}

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'citizenship':

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				isset($input['country_of_birth']) 	      ? $val['country_of_birth'] = $input['country_of_birth'] : NULL;
				isset($input['city_of_birth']) 	   	      ? $val['city_of_birth'] = $input['city_of_birth'] : NULL;
				isset($input['citizenship_status'])    	  ? $val['citizenship_status'] = $input['citizenship_status'] : NULL;
				isset($input['languages']) 	   		   	  ? $val['languages'] = implode(",", $input['languages']) : NULL;
				isset($input['num_of_yrs_in_us']) 	   	  ? $val['num_of_yrs_in_us'] = $input['num_of_yrs_in_us'] : NULL;
				isset($input['num_of_yrs_outside_us']) 	  ? $val['num_of_yrs_outside_us'] = $input['num_of_yrs_outside_us'] : NULL;
				isset($input['dual_citizenship_country']) ? $val['dual_citizenship_country'] = $input['dual_citizenship_country'] : NULL;

				// $val['application_state'] = $page;

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'financials':

				$user = User::find($user_id);

				$user->completed_signup = 1;
				isset($input['interested_in_aid']) 				 ? $user->interested_in_aid = $input['interested_in_aid'] : NULL;
				isset($input['financial_firstyr_affordibility']) ? $user->financial_firstyr_affordibility = $input['financial_firstyr_affordibility'] : NULL;

				$user->save();

				// ADD TO FINANCIAL LOGS FOR THE USER.
				$uffal = new UsersFinancialFirstyrAffordibilityLog;
				$uffal->add($user_id, $input['financial_firstyr_affordibility'], $user_id, 'saveCollegeApplication');


				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'gpa':

				$user = User::find($user_id);

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				isset($input['weighted_gpa']) ? $val['weighted_gpa'] = $input['weighted_gpa'] : NULL;
				isset($input['overall_gpa'])  ? $overall_gpa = $input['overall_gpa'] : NULL;
				isset($input['gpa']) 		  ? $hs_gpa = $input['gpa'] : NULL;

				if ($user->in_college == 1 && !isset($overall_gpa) && isset($hs_gpa)) {
					$overall_gpa = $hs_gpa;
					$hs_gpa = NULL;
				}

				if (isset($overall_gpa)) {
					$val['overall_gpa'] = $overall_gpa;
				}

				if (isset($hs_gpa)) {
					$val['hs_gpa'] = $hs_gpa;
				}

				$update = Score::updateOrCreate($attr, $val);

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'scores':

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				$val['act_english'] = isset($input['act_english']) ? $input['act_english'] : NULL;
				$val['act_math'] = isset($input['act_math']) ? $input['act_math'] : NULL;
				$val['act_composite'] = isset($input['act_composite']) ? $input['act_composite'] : NULL;

				$val['sat_english'] = isset($input['sat_english']) ? $input['sat_english'] : NULL;
				$val['sat_math'] = isset($input['sat_math']) ? $input['sat_math'] : NULL;
				$val['sat_reading'] = isset($input['sat_reading']) ? $input['sat_reading'] : NULL;
				$val['sat_writing'] = isset($input['sat_writing']) ? $input['sat_writing'] : NULL;
				$val['sat_reading_writing'] = isset($input['sat_reading_writing']) ? $input['sat_reading_writing'] : NULL;
				$val['sat_total'] =isset($input['sat_total']) ? $input['sat_total'] : NULL;

				$val['psat_math'] = isset($input['psat_math']) ? $input['psat_math'] : NULL;
				$val['psat_reading_writing'] = isset($input['psat_reading_writing']) ? $input['psat_reading_writing'] : NULL;
				$val['psat_total'] = isset($input['psat_total']) ? $input['psat_total'] : NULL;

				$val['lsat_total'] = isset($input['lsat_total']) ? $input['lsat_total'] : NULL;

				$val['gmat_total'] = isset($input['gmat_total']) ? $input['gmat_total'] : NULL;

				$val['gre_verbal'] = isset($input['gre_verbal']) ? $input['gre_verbal'] : NULL;
				$val['gre_quantitative'] = isset($input['gre_quantitative']) ? $input['gre_quantitative'] : NULL;
				$val['gre_analytical'] = isset($input['gre_analytical'])  ? $input['gre_analytical'] : NULL;

				$val['ap_overall'] = isset($input['ap_overall']) ? $input['ap_overall'] : NULL;

				$val['ged_score'] = isset($input['ged_score']) ? $input['ged_score'] : NULL;

				$val['toefl_reading'] = isset($input['toefl_reading']) ? $input['toefl_reading'] : NULL;
				$val['toefl_listening'] = isset($input['toefl_listening']) ? $input['toefl_listening'] : NULL;
				$val['toefl_writing'] = isset($input['toefl_writing']) ? $input['toefl_writing'] : NULL;
				$val['toefl_speaking'] = isset($input['toefl_speaking']) ? $input['toefl_speaking'] : NULL;
				$val['toefl_total'] = isset($input['toefl_total']) ? $input['toefl_total'] : NULL;

				$val['ielts_reading'] = isset($input['ielts_reading']) ? $input['ielts_reading'] : NULL;
				$val['ielts_listening'] = isset($input['ielts_listening']) ? $input['ielts_listening'] : NULL;
				$val['ielts_writing'] = isset($input['ielts_writing']) ? $input['ielts_writing'] : NULL;
				$val['ielts_speaking'] = isset($input['ielts_speaking']) ? $input['ielts_speaking'] : NULL;
				$val['ielts_total'] = isset($input['ielts_total']) ? $input['ielts_total'] : NULL;

				$val['pte_total'] = isset($input['pte_total']) ? $input['pte_total'] : NULL;

				$val['other_exam'] = isset($input['other_exam']) ?  $input['other_exam'] : NULL;
				$val['other_values'] = isset($input['other_values']) ? $val['other_values'] = $input['other_values'] : NULL;

				$val['toefl_ibt_reading'] = isset($input['toefl_ibt_reading']) ? $input['toefl_ibt_reading'] : NULL;
				$val['toefl_ibt_listening'] = isset($input['toefl_ibt_listening']) ? $input['toefl_ibt_listening'] : NULL;
				$val['toefl_ibt_speaking'] = isset($input['toefl_ibt_speaking']) ? $input['toefl_ibt_speaking'] : NULL;
				$val['toefl_ibt_writing'] = isset($input['toefl_ibt_writing']) ? $input['toefl_ibt_writing'] : NULL;
				$val['toefl_ibt_total'] = isset($input['toefl_ibt_total']) ? $input['toefl_ibt_total'] : NULL;

				$val['toefl_pbt_reading'] = isset($input['toefl_pbt_reading']) ? $input['toefl_pbt_reading'] : NULL;
				$val['toefl_pbt_listening'] = isset($input['toefl_pbt_listening']) ? $input['toefl_pbt_listening'] : NULL;
				$val['toefl_pbt_written'] = isset($input['toefl_pbt_written']) ? $input['toefl_pbt_written'] : NULL;
				$val['toefl_pbt_total'] = isset($input['toefl_pbt_total']) ? $input['toefl_pbt_total'] : NULL;

				$val['is_pre_2016_sat'] = isset($input['is_pre_2016_sat']) ? $input['is_pre_2016_sat'] : 0;

				$val['is_pre_2016_psat'] = isset($input['is_pre_2016_psat']) ? $input['is_pre_2016_psat'] : 0;

				$update = Score::updateOrCreate($attr, $val);

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
            case 'scholarships':
                // Set all scholarships user applied to to null that were not already submitted
                ScholarshipsUserApplied::where('user_id', '=', $user_id)->where('status', '!=', 'submitted')->update(['status' => null]);

                $scholarshipsList = $input['scholarshipsList'];

                // Set all scholarships the user applied to as 'finish' status
                foreach ($scholarshipsList as $scholarship) {
                    $attributes = [
                        'user_id' => $user_id,
                        'scholarship_id' => $scholarship['id'],
                    ];

                    $values = [
                        'user_id' => $user_id,
                        'scholarship_id' => $scholarship['id'],
                        'status' => 'finish',
                        'last_status' => null,
                    ];

                    ScholarshipsUserApplied::updateOrCreate($attributes, $values);
                }

                $ret['status'] = 'success';
                break;

            case 'applications':    
			case 'colleges':

				if ($page != "applications") {
					$delete = UsersAppliedColleges::where('user_id', $user_id)
											  ->where('submitted', 0)
											  ->delete();
				}

				isset($input['MyApplicationList']) ? $applyTo_schools = $input['MyApplicationList'] : $applyTo_schools = $input['applyTo_schools'];



				foreach ($applyTo_schools as $key) {
					isset($key['college_id']) ? $college_id = $key['college_id'] : $college_id = $key['id'];
					$attr = array('user_id' => $user_id, 'college_id' => $college_id);
					$val  = array('user_id' => $user_id, 'college_id' => $college_id);

					$update = UsersAppliedColleges::updateOrCreate($attr, $val);

                    // Add college to user's list and college's inquiries bucket
                    $recruitment_attributes = [
                        'user_id' => $user_id,
                        'college_id' => $college_id,
                    ];

                    $recruitment_values = [
                        'user_id' => $user_id,
                        'college_id' => $college_id,
                        'status' => 1,
                        'user_recruit' => 1,
                        'type' => 'one_app_application'
                    ];

                    Recruitment::updateOrCreate($recruitment_attributes, $recruitment_values);
				}

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['MyCollegeList'] = $applyTo_schools;
				$ret['status'] = 'success';
				break;


			case 'family':

				$user = User::find($user_id);

				$user->completed_signup = 1;
				isset($input['married']) ? $user->married = $input['married'] : NULL;
				isset($input['children']) ? $user->children = $input['children'] : NULL;

				$user->save();

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				isset($input['parents_married']) ? $val['parents_married'] = $input['parents_married'] : NULL;
				isset($input['siblings'])		 ? $val['siblings'] = $input['siblings'] : NULL;

				// $val['application_state'] = $page;
				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'uploads':
				$ret['data'] = $this->addUploadedDocument($user_id, $input);

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';

				break;
			case 'courses':

				$current_schools = $input['current_schools'];

				foreach ($current_schools as $key) {
					$courses 	 = $key['courses'];
					$semester 	 = ucwords($key['scheduling_system']);
					$school_type = $key['school_type'];
					$college_id  = $key['id'];


					foreach ($courses as $k) {
						$class_level = 1;
						if (isset($k['designation'])) {
							if ($k['designation'] == 'basic') {
								$class_level = 1;
							}elseif ($k['designation'] == 'honors') {
								$class_level = 2;
							}elseif ($k['designation'] == 'AP') {
								$class_level = 3;
							}
						}

						$school_year = ucwords($k['edu_level']);

						$attr = array('user_id' => $user_id, 'school_id' => $college_id, 'school_type' => $school_type, 'school_year' => $school_year,
									  'semester' => $semester, 'class_type' => $k['subject'], 'class_name' => $k['course_id'], 'class_level' => $class_level,
									  'units' => $k['credits']);
						$val  = array('user_id' => $user_id, 'school_id' => $college_id, 'school_type' => $school_type, 'school_year' => $school_year,
									  'semester' => $semester, 'class_type' => $k['subject'], 'class_name' => $k['course_id'], 'class_level' => $class_level,
									  'units' => $k['credits']);

						$update = Course::updateOrCreate($attr, $val);
					}
				}

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'awards':

				$awards = $input['my_awards'];

				$delete = HonorAward::where('user_id', $user_id)->delete();

				foreach ($awards as $key) {

					$notes = isset($key['award_notes']) ? $key['award_notes'] : '';

					$attr = array('user_id' => $user_id, 'title' => $key['award_name'], 'issuer' => $key['award_accord'],
								  'month_received' => $key['award_received_month'], 'year_received' => $key['award_received_year'],
								  'honor_description' => $notes );

					$val = array('user_id' => $user_id, 'title' => $key['award_name'], 'issuer' => $key['award_accord'],
								  'month_received' => $key['award_received_month'], 'year_received' => $key['award_received_year'], 'honor_description' => $notes, 'whocanseethis' => 'public');

					$update = HonorAward::updateOrCreate($attr, $val);
				}

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'clubs':

				$clubs = $input['my_clubs'];

				$delete = ClubOrg::where('user_id', $user_id)->delete();

				foreach ($clubs as $key) {

					$notes = isset($key['club_notes']) ? $key['club_notes'] : '';

					$attr = array('user_id' => $user_id, 'club_name' => $key['club_name'], 'position' => $key['club_role'],
								  'month_from' => $key['club_active_start_month'], 'year_from' => $key['club_active_start_year'],
								  'month_to' => $key['club_active_end_month'], 'year_to' => $key['club_active_end_year'],
 								  'club_description' => $notes);

					$val = array('user_id' => $user_id, 'club_name' => $key['club_name'], 'position' => $key['club_role'],
								 'month_from' => $key['club_active_start_month'], 'year_from' => $key['club_active_start_year'],
								 'month_to' => $key['club_active_end_month'], 'year_to' => $key['club_active_end_year'],
 								 'club_description' => $notes, 'whocanseethis' => 'public');

					$update = ClubOrg::updateOrCreate($attr, $val);
				}

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'essay':

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id, 'essay_content' => $input['essay_content']);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'additional_info':

				$this->addUploadedDocument($user_id, $input);

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				isset($input['passport_expiration_date'])		 ? $val['passport_expiration_date'] = $input['passport_expiration_date'] : NULL;
				isset($input['passport_number'])		 		 ? $val['passport_number'] = $input['passport_number'] : NULL;
				isset($input['living_in_us'])		     		 ? $val['living_in_us'] = $input['living_in_us'] : NULL;
				isset($input['emergency_contact_name'])			 ? $val['emergency_contact_name'] = $input['emergency_contact_name'] : NULL;
				isset($input['emergency_phone'])		 		 ? $val['emergency_phone'] = $input['emergency_phone'] : NULL;
				isset($input['emergency_phone_code'])		 	 ? $val['emergency_phone_code'] = $input['emergency_phone_code'] : NULL;
				isset($input['num_of_yrs_outside_us'])		 	 ? $val['num_of_yrs_outside_us'] = $input['num_of_yrs_outside_us'] : NULL;
				isset($input['num_of_yrs_in_us'])		 	 	 ? $val['num_of_yrs_in_us'] = $input['num_of_yrs_in_us'] : NULL;
				isset($input['financial_plan'])		 			 ? $val['financial_plan'] = $input['financial_plan'] : NULL;
				isset($input['have_sponsor'])		 	 		 ? $val['have_sponsor'] = $input['have_sponsor'] : NULL;
				isset($input['ielts_date'])		 				 ? $val['ielts_date'] = $input['ielts_date'] : NULL;
				isset($input['took_pearson_versant_exam'])		 ? $val['took_pearson_versant_exam'] = $input['took_pearson_versant_exam'] : NULL;
				isset($input['toefl_ibt_date'])		 			 ? $val['toefl_ibt_date'] = $input['toefl_ibt_date'] : NULL;
				isset($input['name_of_hs'])						 ? $val['name_of_hs'] = $input['name_of_hs'] : NULL;
				isset($input['city_of_hs'])		 				 ? $val['city_of_hs'] = $input['city_of_hs'] : NULL;
				isset($input['country_of_hs'])		 			 ? $val['country_of_hs'] = $input['country_of_hs'] : NULL;
				isset($input['hs_start_date'])		 			 ? $val['hs_start_date'] = $input['hs_start_date'] : NULL;
				isset($input['hs_end_date'])		 			 ? $val['hs_end_date'] = $input['hs_end_date'] : NULL;
				isset($input['gap_in_academic_record'])	 		 ? $val['gap_in_academic_record'] = $input['gap_in_academic_record'] : NULL;
				isset($input['attended_additional_institutions'])? $val['attended_additional_institutions'] = $input['attended_additional_institutions'] : NULL;
				isset($input['academic_misconduct'])		 	 ? $val['academic_misconduct'] = $input['academic_misconduct'] : NULL;
				isset($input['behavior_misconduct'])		 	 ? $val['behavior_misconduct'] = $input['behavior_misconduct'] : NULL;
				isset($input['criminal_offense'])		 		 ? $val['criminal_offense'] = $input['criminal_offense'] : NULL;
				isset($input['academic_expulsion'])		 		 ? $val['academic_expulsion'] = $input['academic_expulsion'] : NULL;
				isset($input['misdemeanor'])		 		 	 ? $val['misdemeanor'] = $input['misdemeanor'] : NULL;
				isset($input['disciplinary_violation'])	 		 ? $val['disciplinary_violation'] = $input['disciplinary_violation'] : NULL;
				isset($input['guilty_of_crime'])		 	 	 ? $val['guilty_of_crime'] = $input['guilty_of_crime'] : NULL;
				isset($input['i20_institution'])		 	 	 ? $val['i20_institution'] = $input['i20_institution'] : NULL;
				isset($input['i20_dependents'])		 	 		 ? $val['i20_dependents'] = $input['i20_dependents'] : NULL;
				isset($input['addtl__post_secondary_school_type']) ? $val['addtl__post_secondary_school_type'] = $input['addtl__post_secondary_school_type'] : NULL;
				isset($input['addtl__post_secondary_name'])		 ? $val['addtl__post_secondary_name'] = $input['addtl__post_secondary_name'] : NULL;
				isset($input['addtl__post_secondary_city'])		 ? $val['addtl__post_secondary_city'] = $input['addtl__post_secondary_city'] : NULL;
				isset($input['addtl__post_secondary_country'])	 ? $val['addtl__post_secondary_country'] = $input['addtl__post_secondary_country'] : NULL;
				isset($input['addtl__post_secondary_start_date'])? $val['addtl__post_secondary_start_date'] = $input['addtl__post_secondary_start_date'] : NULL;
				isset($input['addtl__post_secondary_end_date'])	 ? $val['addtl__post_secondary_end_date'] = $input['addtl__post_secondary_end_date'] : NULL;
				isset($input['addtl__post_secondary_resume'])	 ? $val['addtl__post_secondary_resume'] = $input['addtl__post_secondary_resume'] : NULL;
				isset($input['took_pearson_versant_exam_date'])	 ? $val['took_pearson_versant_exam_date'] = $input['took_pearson_versant_exam_date'] : NULL;
				isset($input['took_pearson_versant_exam_score']) ? $val['took_pearson_versant_exam_score'] = $input['took_pearson_versant_exam_score'] : NULL;
				isset($input['emergency_contact_relationship'])  ? $val['emergency_contact_relationship'] = $input['emergency_contact_relationship'] : NULL;
				if (isset($input['home_phone'])) {
					$home_phone = '';
					if (isset($input['home_phone_code'])) {
						$home_phone = '+'. $input['home_phone_code'];
					}
					$val['home_phone'] = $home_phone.' '.$input['home_phone'];
				}
				isset($input['is_hispanic'])  					 ? $val['is_hispanic'] = $input['is_hispanic'] : NULL;
				isset($input['have_allergies'])  				 ? $val['have_allergies'] = $input['have_allergies'] : NULL;
				isset($input['have_medical_needs'])  			 ? $val['have_medical_needs'] = $input['have_medical_needs'] : NULL;
				isset($input['have_dietary_restrictions'])  	 ? $val['have_dietary_restrictions'] = $input['have_dietary_restrictions'] : NULL;
				isset($input['have_student_visa_and_will_transfer']) ? $val['have_student_visa_and_will_transfer'] = $input['have_student_visa_and_will_transfer'] : NULL;
				isset($input['need_form_i20_for_visa'])  		 ? $val['need_form_i20_for_visa'] = $input['need_form_i20_for_visa'] : NULL;
				isset($input['i20_end_date'])  					 ? $val['i20_end_date'] = $input['i20_end_date'] : NULL;
				isset($input['racial_category']) 				 ? $val['racial_category'] = $input['racial_category'] : NULL;
				isset($input['visa_type']) 				 		 ? $val['visa_type'] = $input['visa_type'] : NULL;
				isset($input['visa_expiration']) 				 ? $val['visa_expiration'] = $input['visa_expiration'] : NULL;
				isset($input['i94_expiration']) 				 ? $val['i94_expiration'] = $input['i94_expiration'] : NULL;
				isset($input['took_pearson_versant_exam_date'])  ? $val['took_pearson_versant_exam_date'] = $input['took_pearson_versant_exam_date'] : NULL;
				isset($input['took_pearson_versant_exam_score']) ? $val['took_pearson_versant_exam_score'] = $input['took_pearson_versant_exam_score'] : NULL;
				isset($input['emergency_contact_address']) 		 ? $val['emergency_contact_address'] = $input['emergency_contact_address'] : NULL;
				isset($input['emergency_contact_email']) 		 ? $val['emergency_contact_email'] = $input['emergency_contact_email'] : NULL;

				isset($input['devry_funding_plan']) 		 	 ? $val['devry_funding_plan'] = $input['devry_funding_plan'] : NULL;
				isset($input['already_attended_school_of_management_or_nursing']) 		 ? $val['already_attended_school_of_management_or_nursing'] = $input['already_attended_school_of_management_or_nursing'] : NULL;
				isset($input['graduate_of_carrington_or_chamberlain']) ? $val['graduate_of_carrington_or_chamberlain'] = $input['graduate_of_carrington_or_chamberlain'] : NULL;


				isset($input['fathers_name']) 				 		 ? $val['fathers_name'] = $input['fathers_name'] : NULL;
				isset($input['fathers_job']) 				 		 ? $val['fathers_job'] = $input['fathers_job'] : NULL;
				isset($input['mothers_name']) 				 		 ? $val['mothers_name'] = $input['mothers_name'] : NULL;
				isset($input['mothers_job']) 				 		 ? $val['mothers_job'] = $input['mothers_job'] : NULL;
				isset($input['guardian_name']) 				 		 ? $val['guardian_name'] = $input['guardian_name'] : NULL;
				isset($input['guardian_job']) 				 		 ? $val['guardian_job'] = $input['guardian_job'] : NULL;
				isset($input['parents_have_degree']) 				 ? $val['parents_have_degree'] = $input['parents_have_degree'] : NULL;
				isset($input['why_did_you_apply']) 				 	 ? $val['why_did_you_apply'] = $input['why_did_you_apply'] : NULL;
				isset($input['parent_guardian_email']) 				 ? $val['parent_guardian_email'] = $input['parent_guardian_email'] : NULL;
				isset($input['understand_health_insurance_is_required']) ? $val['understand_health_insurance_is_required'] = $input['understand_health_insurance_is_required'] : NULL;
				isset($input['have_any_of_the_following_conditions'])? $val['have_any_of_the_following_conditions'] = $input['have_any_of_the_following_conditions'] : NULL;
				isset($input['have_good_physical_and_mental_health'])? $val['have_good_physical_and_mental_health'] = $input['have_good_physical_and_mental_health'] : NULL;
				isset($input['state_of_hs']) 				 		 ? $val['state_of_hs'] = $input['state_of_hs'] : NULL;
				isset($input['hs_completion_status']) 				 ? $val['hs_completion_status'] = $input['hs_completion_status'] : NULL;
				isset($input['have_graduated_from_a_university']) 	 ? $val['have_graduated_from_a_university'] = $input['have_graduated_from_a_university'] : NULL;
				isset($input['have_attended_language_school']) 		 ? $val['have_attended_language_school'] = $input['have_attended_language_school'] : NULL;
				isset($input['academic_goal']) 				 		 ? $val['academic_goal'] = $input['academic_goal'] : NULL;
				isset($input['have_dependents']) 				 	 ? $val['have_dependents'] = $input['have_dependents'] : NULL;
				isset($input['have_currency_restrictions']) 		 ? $val['have_currency_restrictions'] = $input['have_currency_restrictions'] : NULL;
				isset($input['lived_at_permanent_addr_more_than_6_months'])  ? $val['lived_at_permanent_addr_more_than_6_months'] = $input['lived_at_permanent_addr_more_than_6_months'] : NULL;
				isset($input['mailing_and_permanent_addr_same']) 	 ? $val['mailing_and_permanent_addr_same'] = $input['mailing_and_permanent_addr_same'] : NULL;
				isset($input['contact_preference']) 				 ? $val['contact_preference'] = $input['contact_preference'] : NULL;

				isset($input['was_instruction_taught_in_english']) 	 ? $val['was_instruction_taught_in_english'] = $input['was_instruction_taught_in_english'] : NULL;
				isset($input['fathers_addr']) 				 		 ? $val['fathers_addr'] = $input['fathers_addr'] : NULL;
				isset($input['fathers_city']) 				 		 ? $val['fathers_city'] = $input['fathers_city'] : NULL;
				isset($input['fathers_district']) 				 	 ? $val['fathers_district'] = $input['fathers_district'] : NULL;
				isset($input['fathers_country']) 				 	 ? $val['fathers_country'] = $input['fathers_country'] : NULL;
				isset($input['mothers_addr']) 				 		 ? $val['mothers_addr'] = $input['mothers_addr'] : NULL;
				isset($input['mothers_city']) 				 		 ? $val['mothers_city'] = $input['mothers_city'] : NULL;
				isset($input['mothers_district']) 				 	 ? $val['mothers_district'] = $input['mothers_district'] : NULL;
				isset($input['mothers_country']) 				 	 ? $val['mothers_country'] = $input['mothers_country'] : NULL;
				isset($input['guardian_addr']) 				 		 ? $val['guardian_addr'] = $input['guardian_addr'] : NULL;
				isset($input['guardian_city']) 				 		 ? $val['guardian_city'] = $input['guardian_city'] : NULL;
				isset($input['guardian_district']) 				     ? $val['guardian_district'] = $input['guardian_district'] : NULL;
				isset($input['guardian_country']) 				     ? $val['guardian_country'] = $input['guardian_country'] : NULL;

				isset($input['academic_goal__complete_associate_or_certificate'])      ? $val['academic_goal__complete_associate_or_certificate'] = $input['academic_goal__complete_associate_or_certificate'] : NULL;
				isset($input['academic_goal__improve_english']) 				       ? $val['academic_goal__improve_english'] = $input['academic_goal__improve_english'] : NULL;
				isset($input['academic_goal__meet_transfer_reqs_for_bachelors_degree'])? $val['academic_goal__meet_transfer_reqs_for_bachelors_degree'] = $input['academic_goal__meet_transfer_reqs_for_bachelors_degree'] : NULL;
				isset($input['academic_goal__prep_for_graduate_school']) 			   ? $val['academic_goal__prep_for_graduate_school'] = $input['academic_goal__prep_for_graduate_school'] : NULL;

				isset($input['illnesses']) 			   				  ? $val['illnesses'] = $input['illnesses'] : NULL;
				isset($input['planning_to_take_esl_classes']) 		  ? $val['planning_to_take_esl_classes'] = $input['planning_to_take_esl_classes'] : NULL;
				isset($input['lang_school_completed_current_level'])  ? $val['lang_school_completed_current_level'] = $input['lang_school_completed_current_level'] : NULL;
				isset($input['date_attended_lang_school']) 			  ? $val['date_attended_lang_school'] = $input['date_attended_lang_school'] : NULL;
				isset($input['num_of_yrs_studied_english_after_hs'])  ? $val['num_of_yrs_studied_english_after_hs'] = $input['num_of_yrs_studied_english_after_hs'] : NULL;
				isset($input['have_been_dismissed_from_school_for_disciplinary_reasons'])
																	  ? $val['have_been_dismissed_from_school_for_disciplinary_reasons'] = $input['have_been_dismissed_from_school_for_disciplinary_reasons'] : NULL;

				isset($input['have_used_drugs_last_12_months']) 	  ? $val['have_used_drugs_last_12_months'] = $input['have_used_drugs_last_12_months'] : NULL;

				isset($input['applying_for_admission_school']) 		  ? $val['applying_for_admission_school'] = $input['applying_for_admission_school'] : NULL;
				isset($input['plan_to_enroll_in']) 				 	  ? $val['plan_to_enroll_in'] = $input['plan_to_enroll_in'] : NULL;
				isset($input['understand_I_need_to_submit_medical_examination_form'])
				? $val['understand_I_need_to_submit_medical_examination_form'] = $input['understand_I_need_to_submit_medical_examination_form'] : NULL;
				isset($input['academic_goals_essay']) 				  ? $val['academic_goals_essay'] = $input['academic_goals_essay'] : NULL;
				isset($input['have_you_graduated_from_hs']) 		  ? $val['have_you_graduated_from_hs'] = $input['have_you_graduated_from_hs'] : NULL;
				isset($input['program_you_are_interested_in']) 		  ? $val['program_you_are_interested_in'] = $input['program_you_are_interested_in'] : NULL;
				isset($input['understand_christian_position_of_liberty'])  ? $val['understand_christian_position_of_liberty'] = $input['understand_christian_position_of_liberty'] : NULL;
				isset($input['wish_to_study_at_christian_university'])? $val['wish_to_study_at_christian_university'] = $input['wish_to_study_at_christian_university'] : NULL;
				isset($input['liberty_housing_requirements']) 		  ? $val['liberty_housing_requirements'] = $input['liberty_housing_requirements'] : NULL;
				isset($input['are_you_christian']) 				 	  ? $val['are_you_christian'] = $input['are_you_christian'] : NULL;
				isset($input['faith_essay']) 				 		  ? $val['faith_essay'] = $input['faith_essay'] : NULL;
				isset($input['seeking_u_of_arkansas_degree']) 		  ? $val['seeking_u_of_arkansas_degree'] = $input['seeking_u_of_arkansas_degree'] : NULL;
				isset($input['who_graduated_from_u_of_arkansas']) 	  ? $val['who_graduated_from_u_of_arkansas'] = $input['who_graduated_from_u_of_arkansas'] : NULL;
				isset($input['previously_attended_u_of_arkansas']) 	  ? $val['previously_attended_u_of_arkansas'] = $input['previously_attended_u_of_arkansas'] : NULL;
				isset($input['are_graduating_from_hs']) 			  ? $val['are_graduating_from_hs'] = $input['are_graduating_from_hs'] : NULL;
				isset($input['will_have_fewer_than_24_transferrable_credits'])
				? $val['will_have_fewer_than_24_transferrable_credits'] = $input['will_have_fewer_than_24_transferrable_credits'] : NULL;
				isset($input['will_have_more_than_24_transferrable_credits'])
				? $val['will_have_more_than_24_transferrable_credits'] = $input['will_have_more_than_24_transferrable_credits'] : NULL;
				isset($input['have_earned_undergrad_grad_pro_degree'])? $val['have_earned_undergrad_grad_pro_degree'] = $input['have_earned_undergrad_grad_pro_degree'] : NULL;

				isset($input['applying_to_esl_program']) 			  ? $val['applying_to_esl_program'] = $input['applying_to_esl_program'] : NULL;
				isset($input['previous_college_experience']) 		  ? $val['previous_college_experience'] = $input['previous_college_experience'] : NULL;
				isset($input['how_did_you_hear_about_msoe']) 		  ? $val['how_did_you_hear_about_msoe'] = $input['how_did_you_hear_about_msoe'] : NULL;
				isset($input['financial_support_provided_by']) 		  ? $val['financial_support_provided_by'] = $input['financial_support_provided_by'] : NULL;

				isset($input['num_of_yrs_planning_on_studying_at_pccd']) 	   ? $val['num_of_yrs_planning_on_studying_at_pccd'] = $input['num_of_yrs_planning_on_studying_at_pccd'] : NULL;
				isset($input['liberty_housing_requirements__residence_hall'])  ? $val['liberty_housing_requirements__residence_hall'] = $input['liberty_housing_requirements__residence_hall'] : NULL;
				isset($input['liberty_housing_requirements__off_campus']) 	   ? $val['liberty_housing_requirements__off_campus'] = $input['liberty_housing_requirements__off_campus'] : NULL;
				isset($input['have_you_graduated_from_hs__have_graduated_on_this_date']) 			  ? $val['have_you_graduated_from_hs__have_graduated_on_this_date'] = $input['have_you_graduated_from_hs__have_graduated_on_this_date'] : NULL;
				isset($input['have_you_graduated_from_hs__will_graduate_on_this_date']) 			  ? $val['have_you_graduated_from_hs__will_graduate_on_this_date'] = $input['have_you_graduated_from_hs__will_graduate_on_this_date'] : NULL;
				isset($input['peralta_have_graduated_on_this_date']) 		   ? $val['peralta_have_graduated_on_this_date'] = $input['peralta_have_graduated_on_this_date'] : NULL;
				isset($input['peralta_will_graduate_on_this_date']) 		   ? $val['peralta_will_graduate_on_this_date'] = $input['peralta_will_graduate_on_this_date'] : NULL;
				isset($input['name_of_church']) 			  				   ? $val['name_of_church'] = $input['name_of_church'] : NULL;
				isset($input['financial_support_provided_by__student']) 	   ? $val['financial_support_provided_by__student'] = $input['financial_support_provided_by__student'] : NULL;
				isset($input['financial_support_provided_by__student_parents'])? $val['financial_support_provided_by__student_parents'] = $input['financial_support_provided_by__student_parents'] : NULL;
				isset($input['financial_support_provided_by__private_sponsor'])? $val['financial_support_provided_by__private_sponsor'] = $input['financial_support_provided_by__private_sponsor'] : NULL;
				isset($input['financial_support_provided_by__govt_scholarship']) ? $val['financial_support_provided_by__govt_scholarship'] = $input['financial_support_provided_by__govt_scholarship'] : NULL;
				isset($input['financial_support_provided_by__athletic_scholarship'])  ? $val['financial_support_provided_by__athletic_scholarship'] = $input['financial_support_provided_by__athletic_scholarship'] : NULL;
				isset($input['financial_support_provided_by__other']) 			  ? $val['financial_support_provided_by__other'] = $input['financial_support_provided_by__other'] : NULL;
				isset($input['intend_to_follow_code_of_conduct']) 		  ? $val['intend_to_follow_code_of_conduct'] = $input['intend_to_follow_code_of_conduct'] : NULL;
				isset($input['will_study_english_prior_to_attending_devry']) 			  ? $val['will_study_english_prior_to_attending_devry'] = $input['will_study_english_prior_to_attending_devry'] : NULL;
				isset($input['plan_to_enroll_in__esl_program']) 		  ? $val['plan_to_enroll_in__esl_program'] = $input['plan_to_enroll_in__esl_program'] : NULL;
				isset($input['plan_to_enroll_in__academic_program'])      ? $val['plan_to_enroll_in__academic_program'] = $input['plan_to_enroll_in__academic_program'] : NULL;
				isset($input['have_you_graduated_from_hs__no_will_not_graduate'])      ? $val['have_you_graduated_from_hs__no_will_not_graduate'] = $input['have_you_graduated_from_hs__no_will_not_graduate'] : NULL;

				if (isset($input['religious_affiliation'])) {
					$user = User::find($data['user_id']);

					$user->religion = $input['religious_affiliation'];
					$user->save();
				}

				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'declaration':

				foreach ($input as $key => $value) {
					if (strpos($key, 'declaration_') !== FALSE && $key != "declaration_form_done"){
						$declaration_id = str_replace("declaration_", "", $key);

						// $declaration_id is already a string, so we must parse the string for digits.
						// Using a type cast to avoid possibility of it actually being a real integer.
						if (!ctype_digit((string) $declaration_id)) {
							continue;
						}

						$attr = array('user_id' => $user_id, 'declaration_id' => $declaration_id);
						$val  = array('user_id' => $user_id, 'declaration_id' => $declaration_id);

						$update = UsersAppliedCollegesDeclaration::updateOrCreate($attr, $val);
					}else{
						continue;
					}
				}

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'sponsor':

				$ret['new_data'] = SponsorUserContacts::saveUserContacts($user_id, $input);

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id);

				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ret['status'] = 'success';
				break;
			case 'submit':

				$delete = UsersAppliedColleges::where('user_id', $user_id)
											  ->where('submitted', 0)
											  ->delete();

				if (isset($input['scholarshipsList'])) {
	                $scholarshipsList = $input['scholarshipsList'];

	                if (!empty($scholarshipsList)) {
	                    foreach ($scholarshipsList as $scholarship) {
	                        $attributes = array('user_id' => $user_id, 'scholarship_id' => $scholarship['id']);
	                        $values  = array('user_id' => $user_id, 'scholarship_id' => $scholarship['id'], 'status' => 'submitted');

	                        ScholarshipsUserApplied::updateOrCreate($attributes, $values);
	                    }
	                }
                }
				// $applyTo_schools = $input['applyTo_schools'];
				$completed_colleges = $input['completed_colleges'];

				foreach ($completed_colleges as $key) {
					$attr = array('user_id' => $user_id, 'college_id' => $key['college_id'], 'submitted' => 1);
					$val  = array('user_id' => $user_id, 'college_id' => $key['college_id'], 'submitted' => 1);

					$update = UsersAppliedColleges::updateOrCreate($attr, $val);

					$attributes = ['user_id' => $user_id, 'college_id' => $key['college_id']];

					// Create a new college application status entry if none exists. Defaults status to pending.
					CollegesApplicationStatus::firstOrCreate($attributes);
				}

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id, 'application_terms_of_conditions' => $input['terms_of_conditions'],
							  'application_signature' => $input['signature'], 'application_signature_date' => date('Y-m-d H:i:s'),
							  'application_submitted' => 1);

				// $update = UsersCustomQuestion::updateOrCreate($attr, $val);
				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);

				$ar = new AgencyRecruitment;

				$ar->moveUserToCompletedApplicationBucket($user_id);

				$is_app = isset($api_input) ? true : NULL;
				$mac = new MandrillAutomationController;
				$mac->internalFinishedApplicationEmail($user_id, $is_app);

				$ret['status'] = 'success';
				break;
			case 'scholarship-submission':
				$scholarship_arr = $input['scholarships'];
				foreach ($scholarship_arr as $key => $value) {
					$attr = array('user_id' => $user_id, 'scholarship_id' => $value);
					$val  = array('user_id' => $user_id, 'scholarship_id' => $value, 'status' => 'submitted');
					ScholarshipsUserApplied::updateOrCreate($attr, $val);
				}

				$ret['status'] = 'success';
				break;
			case 'demographics':

				$user = User::find($user_id);
				isset($input['gender'])    ? $user->gender = $input['gender'] : NULL;
				isset($input['religion'])  ? $user->religion = $input['religion'] : NULL;
				isset($input['ethnicity']) ? $user->ethnicity = $input['ethnicity'] : NULL;
				$user->save();

				isset($input['family_income']) ? $family_income = $input['family_income'] : $family_income = NULL;

				$attr = array('user_id' =>  $user_id);
				$val  = array('user_id' =>  $user_id, 'family_income' => $family_income);

				$this->changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val);
				$ret['status'] = 'success';
				break;


			default:

				$ret['status'] = "failed";
				break;
		}


		$ret['profile_percent']  = $this->CalcOneAppPercent($user_id);

		$ret['app_last_updated'] = date('m/d/Y @ h:i A');

		return json_encode($ret);
	}

	public function saveCollegeAcceptanceStatus(){
		$input = Request::all();

		// Missing parameter, return failed
		if (!isset($input['hashed_id']) || !isset($input['status'])) {
			return 'failed';
		}

		$user_id = Crypt::decrypt($input['hashed_id']);

		CollegesApplicationStatus::updateOrCreateStatus($user_id, $input['college_id'], $input['status']);

		// If status is accepted, ensure the student has applied and submitted an application to the college.
		$is_submitted = $input['status'] == 'accepted' ? 1 : 0;

		$attributes = [ 'user_id' => $user_id, 'college_id' => $input['college_id'] ];
		$values = [ 'submitted' => $is_submitted ];

		UsersAppliedColleges::updateOrCreate($attributes, $values);

		return 'success';
	}

	// Helper route to just save current uploaded files.
	public function saveUploadedFiles(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		return $this->addUploadedDocument($data['user_id'], $input);
	}

	private function addUploadedDocument($user_id, $input, $mobile_device_file = NULL){
		$bucket_url = 'asset.plexuss.com/users/transcripts';
		$url 		= 'https://s3-us-west-2.amazonaws.com/'. $bucket_url.'/';

		$ret['data'] = array();

		foreach ($input as $key => $value) {

			if ( Request::hasFile($key) ){

				$doc_type = substr($key, 0, strpos($key, "_"));
				$doc_type = str_replace("_", "", $doc_type);

				$response = $this->generalUploadDoc($input, $key, 'asset.plexuss.com/users/transcripts');

				$transcript_name = str_replace($url, '', $response['url']);
				$transcript_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/";

				$attr = array('user_id' => $user_id, 'transcript_name' => $transcript_name);

				$val  = array('user_id' => $user_id, 'transcript_name' => $transcript_name,
							  'transcript_path' => $transcript_path, 'school_type' => 'highschool', 'doc_type' => $doc_type);

				$update = Transcript::updateOrCreate($attr, $val);

				$tmp = array();
				$tmp['transcript_id'] 	= $update->id;
				$tmp['url'] 			= $response['url'];
				$tmp['doc_type'] 		= $doc_type;
				// $tmp['transcript_date'] = date('m/d/Y H:i:s', strtotime($update->created_at));
				$tmp['transcript_date'] = date('m/d/Y h:ia', strtotime($update->created_at));
				$tmp['mime_type'] = $response['mime_type'];

				$response['filename'] !== null ? $tmp['name'] = $response['filename'] : null;

				// different names for saveMeTab
				$tmp['id'] = $update->id;
				$tmp['path'] = $transcript_path.$transcript_name;
				$tmp['file_name'] = $transcript_name;
				$tmp['date'] = date('m/d/Y', strtotime($update->created_at));
				// different names for saveMeTab ends

				$ret['data'][] = $tmp;
			}
		}

		return $ret['data'];
	}

	/**
	 * changeApplicationState : this method decides if this application state is higher than the user's current application state
	 * if it is not then it passes back the new application state, this method also saves UsersCustomQuestion
	 *
	 * @param page    : application page
	 * @param user_id : this person's user_id
	 * @param attr    : attribute array for UsersCustomQuestion
	 * @param val     : value array for UsersCustomQuestion
	 * @return array
	 */
	private function changeApplicationStateAndSaveUsersCustomQuestion($page, $user_id, $attr, $val){

		$ucq = UsersCustomQuestion::on('rds1')->where('user_id', $user_id)->first();
		$futureState  = CollegesApplicationsState::on('rds1')->where('name', $page)->first();

		if (!isset($ucq)) {
			$val['application_state'] = $page;
			$val['application_state_id'] = $futureState->id;
			$update = UsersCustomQuestion::updateOrCreate($attr, $val);
			return;
		}

		if (!isset($ucq->application_state)) {
			$val['application_state'] = $page;
			$val['application_state_id'] = $futureState->id;
			$update = UsersCustomQuestion::updateOrCreate($attr, $val);
			return;
		}

		$currentState = CollegesApplicationsState::on('rds1')->where('name', $ucq->application_state)->first();


		if (isset($val['family_income'])) {

					}
		else{
			if ($futureState->id > $currentState->id) {
			$val['application_state'] = $page;
			$val['application_state_id'] = $futureState->id;
			}
		}

		$update = UsersCustomQuestion::updateOrCreate($attr, $val);
		return;
	}

	public function getCoursesSubjects(){
		if (Cache::has(env('ENVIRONMENT').'_'.'_getCoursesSubjects') ) {
			return Cache::get(env('ENVIRONMENT').'_'.'_getCoursesSubjects');
		}

		$s = Subject::on('rds1')->orderBy('subject', 'ASC')
								->groupBy('subject')
		                        ->get();

		$ret = array();

		foreach ($s as $key) {
			$tmp = array();
			$tmp['id'] = (int)$key->id;
			$tmp['name'] = $key->subject;
			$ret[] = $tmp;
		}

		Cache::put(env('ENVIRONMENT').'_'.'_getCoursesSubjects', $ret, 60);

		return $ret;
	}

	public function getClassesBasedOnSubjects($subject_id){
		if (Cache::has(env('ENVIRONMENT').'_'.'getClassesBasedOnSubjects_'. $subject_id) ) {
			
			$ret = Cache::get(env('ENVIRONMENT').'_'.'getClassesBasedOnSubjects_'. $subject_id); 

			Cache::put(env('ENVIRONMENT').'_'.'getClassesBasedOnSubjects_'. $subject_id, $ret, 720);

			return $ret;
		}

		$c = SubjectClass::on('rds1')->where('subject_id', $subject_id)
									 ->orderBy('verified', 'ASC')
									 ->orderBy('class_name', 'ASC')
									 ->groupBy('class_name')
                                     ->get();

		$ret = array();

		foreach ($c as $key) {
			$tmp = array();
			$tmp['id'] = (int)$key->id;
			$tmp['name'] = $key->class_name;
			$ret[] = $tmp;
		}

		$ret = json_encode($ret);

		Cache::put(env('ENVIRONMENT').'_'.'getClassesBasedOnSubjects_'. $subject_id, $ret, 720);

		return $ret;
	}

	public function removeCourse(){

		$input = Request::all();

		try{
			$course_id = Crypt::decrypt($input['course_table_id']);
		}catch(Exception $e) {
			return "Bad course id";
		}

		$c = Course::find($course_id);

		$c->delete();

		return "success";
	}

	public function getInternationalStudentsAjax($api_input = null){

		if( isset($api_input) ){
			$data = $api_input;
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);
		}


		$type 	 = NULL;
		$aor_id  = NULL;

		$input = Request::all();

		isset($input['type']) ? $type = $input['type'] : NULL;
		isset($input['aid']) ? $aor_id = $input['aid'] : NULL;

		if (isset($input['user_id'])) {
			try {
				$data['user_id'] = Crypt::decrypt($input['user_id']);
			} catch (\Exception $e) {
				return NULL;
			}
		}

		$p = new Priority;
		$dt = $p->getPrioritySchoolsForIntlStudents($data['user_id'], $aor_id, $type);

		return $dt;
	}


	public function saveCollegeApplicationAllowedSection(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		// $this->customdd($input);
		$page = $input['page'];

		if ($input['page'] == "uploads") {

			$delete = CollegeApplicaitonAllowedSection::where('college_id', $data['org_school_id'])
													      ->where('page', $input['page'])
													      ->delete();

			if( isset($input['epp_required_uploads']) ){
				foreach ($input['epp_required_uploads'] as $key => $value) {
					if ($value == 1) {

						$required = 1;
						if (strpos($key, '_optional') !== FALSE){
							$key 	  = str_replace("_optional", "", $key);
							$required = 0;
						}

						$attr = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'sub_section' => $key, 'define_program' => 'epp');
						$val  = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'sub_section' => $key, 'define_program' => 'epp',
									  'required' => $required);

						$update = CollegeApplicaitonAllowedSection::updateOrCreate($attr, $val);
					}
				}
			}

			if( isset($input['grad_required_uploads']) ){
				foreach ($input['grad_required_uploads'] as $key => $value) {
					if ($value == 1) {

						$required = 1;
						if (strpos($key, '_optional') !== FALSE){
							$key 	  = str_replace("_optional", "", $key);
							$required = 0;
						}

						$attr = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'sub_section' => $key, 'define_program' => 'grad');
						$val  = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'sub_section' => $key, 'define_program' => 'grad',
									  'required' => $required);

						$update = CollegeApplicaitonAllowedSection::updateOrCreate($attr, $val);
					}
				}
			}

			if( isset($input['undergrad_required_uploads']) ){
				foreach ($input['undergrad_required_uploads'] as $key => $value) {
					if ($value == 1) {

						$required = 1;
						if (strpos($key, '_optional') !== FALSE){
							$key 	  = str_replace("_optional", "", $key);
							$required = 0;
						}

						$attr = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'sub_section' => $key, 'define_program' => 'undergrad');
						$val  = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'sub_section' => $key, 'define_program' => 'undergrad',
									  'required' => $required);

						$update = CollegeApplicaitonAllowedSection::updateOrCreate($attr, $val);
					}
				}
			}
		}elseif ($input['page'] == 'custom') {
			if (isset($input['custom_fields'])) {

				$delete = CollegeApplicaitonAllowedSection::where('college_id', $data['org_school_id'])
													      ->where('page', $input['page'])
													      ->delete();

				foreach ($input['custom_fields'] as $key => $value) {

					if ($value == 1) {
						$define_program = strstr($key, '_', true);
						$sub_section    = str_replace($define_program.'_', "", $key);

						$required = 1;
						if (strpos($sub_section, '_optional') !== FALSE){
							$sub_section 	  = str_replace("_optional", "", $sub_section);
							$required = 0;
						}

						$attr = array('college_id' => $data['org_school_id'], 'page' => $input['page'],
									  'sub_section' => $sub_section, 'define_program' => $define_program);
						$val  = array('college_id' => $data['org_school_id'], 'page' => $input['page'],
									  'sub_section' => $sub_section, 'define_program' => $define_program, 'required' => $required);

						$update = CollegeApplicaitonAllowedSection::updateOrCreate($attr, $val);
					}
				}
			}

		}elseif ($input['page'] == 'additional') {
			if (isset($input['additional_fields'])) {

				$delete = CollegeApplicaitonAllowedSection::where('college_id', $data['org_school_id'])
													      ->where('page', $input['page'])
													      ->delete();

				foreach ($input['additional_fields'] as $key => $value) {

					if ($value == 1) {
						$define_program = strstr($key, '_', true);
						$sub_section    = str_replace($define_program.'_', "", $key);

						$required = 1;
						if (strpos($sub_section, '_optional') !== FALSE){
							$sub_section 	  = str_replace("_optional", "", $sub_section);
							$required = 0;
						}

						$attr = array('college_id' => $data['org_school_id'], 'page' => $input['page'],
									  'sub_section' => $sub_section, 'define_program' => $define_program);
						$val  = array('college_id' => $data['org_school_id'], 'page' => $input['page'],
									  'sub_section' => $sub_section, 'define_program' => $define_program, 'required' => $required);

						$update = CollegeApplicaitonAllowedSection::updateOrCreate($attr, $val);
					}
				}
			}

		}else{
			if(isset($input['epp_require_'.$page]) || isset($input['undergrad_require_'.$page]) || isset($input['grad_require_'.$page])){

				$delete = CollegeApplicaitonAllowedSection::where('college_id', $data['org_school_id'])
													      ->where('page', $input['page'])
													      ->delete();

				$required = 1;
				if (strpos($input['page'], '_optional') !== FALSE){
					$input['page'] 	  = str_replace("_optional", "", $input['page']);
					$required = 0;
				}

				if (isset($input['epp_require_'.$page])) {
					$attr = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'define_program' => 'epp');
					$val  = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'define_program' => 'epp', 'required' => $required);

					$update = CollegeApplicaitonAllowedSection::updateOrCreate($attr, $val);
				}

				if (isset($input['undergrad_require_'.$page])) {
					$attr = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'define_program' => 'undergrad');
					$val  = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'define_program' => 'undergrad', 'required' => $required);

					$update = CollegeApplicaitonAllowedSection::updateOrCreate($attr, $val);
				}

				if (isset($input['grad_require_'.$page])) {
					$attr = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'define_program' => 'grad');
					$val  = array('college_id' => $data['org_school_id'], 'page' => $input['page'], 'define_program' => 'grad', 'required' => $required);

					$update = CollegeApplicaitonAllowedSection::updateOrCreate($attr, $val);
				}

			}

		}

		return "success";

	}

	public function getCollegeApplicaitonAllowedSection(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$qry = CollegeApplicaitonAllowedSection::on('rds1')->where('college_id', $data['org_school_id'])->get();

		$ret = array();

		foreach ($qry as $key) {
			$optional = '';
			(isset($key->required) && $key->required == 0) ? $optional = '_optional' : null;
			if ($key->page == "uploads") {
				if (isset($ret[$key->define_program.'_required_'.$key->page])) {
					$tmp_arr = $ret[$key->define_program.'_required_'.$key->page];
					$tmp_arr[$key->sub_section.$optional] = true;

					$ret[$key->define_program.'_required_'.$key->page] = $tmp_arr;
				}else{
					$ret[$key->define_program.'_require_'.$key->page.$optional] = true;
					$ret[$key->define_program.'_required_'.$key->page] = array();
					$tmp = array();
					$tmp[$key->sub_section.$optional] = true;

					$ret[$key->define_program.'_required_'.$key->page] = $tmp;
				}
			}elseif ($key->page == 'custom') {
                if (isset($ret['custom_fields'])) {
                    $ret['custom_fields'][$key->define_program.'_'.$key->sub_section.$optional] = true;
                }else{
                    $ret['custom_fields']   = array();
                    $ret['custom_fields'][$key->define_program.'_'.$key->sub_section.$optional] = true;
                }
            }elseif ($key->page == 'additional') {
                if (isset($ret['additional_fields'])) {
                    $ret['additional_fields'][$key->define_program.'_'.$key->sub_section.$optional] = true;
                }else{
                    $ret['additional_fields']   = array();
                    $ret['additional_fields'][$key->define_program.'_'.$key->sub_section.$optional] = true;
                }
            }else{
				$ret[$key->define_program.'_require_'.$key->page.$optional] = true;
			}
		}

		return json_encode($ret);
	}

	public function getViewDataController($user_id){

		if (!isset($user_id) || empty($user_id)) {
			return "Bad user id";
		}

		try{
			$user_id = Crypt::decrypt($user_id);
		}catch(Exception $e){
			return "Bad user id";
		}

		$data= array();
		$data['alerts'] = array();

		$event_id = 6;
		//get signed in status
		$data['signed_in'] = 1;

		$user = User::on('rds1')->find($user_id);
		//get organaztion status
		if ($user->is_organization == 1) {
			$data['is_organization'] = 1;
		}

		//get mobile status. should always be set in session
        $data['phone']                    = $user->phone;
		$data['is_mobile']                = Agent::isMobile();

		if( isset($user->is_student) && $user->is_student ){
            $data['is_student'] = 1;
        }elseif( isset($user->is_parent) && $user->is_parent ){
            $data['is_parent'] = 1;
        }

		if(isset($data['is_organization']) && $data['is_organization'] == 1){

			$vo = DB::table('organizations as vo')
			->join('organization_branches as vob', 'vo.id', '=','vob.organization_id' )

			->join('organization_branch_permissions as vobp', 'vob.id', '=', 'vobp.organization_branch_id')
			->join('colleges as c', 'c.id', '=', 'vob.school_id')
			->where('vobp.user_id', '=', $user->id)
			->select('vo.name', 'vob.school_id', 'vo.id',
					 'vob.id as branch_id', 'vob.bachelor_plan', 'vob.premier_trial_end_date', 'vob.requested_upgrade',
					 'vob.premier_trial_begin_date', 'vob.slug', 'vob.is_auto_approve_rec',
					 'vob.set_goal_reminder', 'vob.existing_client', 'vob.appointment_set', 'vob.num_of_applications', 'vob.num_of_enrollments',
					 'vob.contract', 'vob.cost_per_handshake', 'vob.balance',
                     'c.school_name', 'c.logo_url',
                     'vobp.super_admin')->first();

			//$vo = VendorOrganization::find($user_orgs[0]);
			//$vob = VendorOrganizationBranch::where('vendor_organization_id' , '=', $vo->id)->first();

			$op = new OrganizationPortal;
			$data['organization_portals'] =  $op->getUsersOrgnizationPortals($vo->branch_id, $user->id, true);

            if ($vo->super_admin == 1) {
                $tmp_arr = array();

                $tmp_arr['id'] = -1;
                $tmp_arr['org_branch_id'] =  $vo->branch_id;
                $tmp_arr['name'] = 'General';
                $tmp_arr['is_default'] = 0;

                array_unshift($data['organization_portals'], (object) $tmp_arr);
            }

            if($user->is_aor == 1){
                $data['aor_id'] = AorPermission::on('rds1')->where('user_id','=',$data['id'])->pluck('aor_id');

                $data['aor_id'] = $data['aor_id'][0];
            }

			/******************* IMPORTANT*****************************/
			// When this variable is null, it would mean that the default portal is the General portal
			// We use this variable everywhere to determine if we want to filter by their targeting or not.
			$data['default_organization_portal'] = null;
            $default_has_been_set = 0;

			if (isset($data['organization_portals']) && !empty($data['organization_portals'])) {
				foreach ($data['organization_portals'] as $key => $value) {
					if ($value->is_default == 1) {
						$data['default_organization_portal'] = $value;
						$data['num_of_applications']         = $value->num_of_applications;
						$data['num_of_enrollments']  		 = $value->num_of_enrollments;
						$data['premier_trial_end_date']		 = $value->premier_trial_end_date;
						$data['premier_trial_begin_date']	 = $value->premier_trial_begin_date;
                        $default_has_been_set = 1;
					}
				}
                if (!isset($data['default_organization_portal']) && $vo->super_admin == 0) {
                    $data['default_organization_portal'] = $data['organization_portals'][0];
                }
			}else{
				$data['num_of_applications'] = $vo->num_of_applications;
				$data['num_of_enrollments'] = $vo->num_of_enrollments;
				$data['premier_trial_end_date'] = $vo->premier_trial_end_date;
				$data['premier_trial_begin_date'] = $vo->premier_trial_begin_date;
			}

            if ($default_has_been_set == 0 && isset($data['organization_portals'][0])) {
                $data['organization_portals'][0]->is_default = 1;
            }

			if (!isset($data['num_of_applications']) && !isset($data['num_of_enrollments'])) {
				// none of the custom portals has been set
				$data['num_of_applications'] = $vo->num_of_applications;
				$data['num_of_enrollments'] = $vo->num_of_enrollments;
				$data['premier_trial_end_date'] = $vo->premier_trial_end_date;
				$data['premier_trial_begin_date'] = $vo->premier_trial_begin_date;
			}
			$data['super_admin'] = $vo->super_admin;

            $data['balance'] = $vo->balance;
            $data['contract'] = $vo->contract;
            $data['cost_per_handshake'] = $vo->cost_per_handshake;
			$data['school_slug'] = $vo->slug;
			$data['org_id'] = $vo->id;
			$data['org_branch_id'] = $vo->branch_id;
			$data['org_name'] = $vo->name;
			$data['org_school_id'] = $vo->school_id;
			$data['school_name'] = $vo->school_name;
			if (isset($vo->logo_url)) {
				$data['school_logo'] = $vo->logo_url;
			}else{
				$data['school_logo'] = '';
			}
			$data['bachelor_plan'] = $vo->bachelor_plan;
			$data['set_goal_reminder'] = $vo->set_goal_reminder;
			$data['existing_client'] = $vo->existing_client;
			$data['appointment_set'] = $vo->appointment_set;

			$data['is_auto_approve_rec'] = $vo->is_auto_approve_rec;
			$data['requested_upgrade'] = $vo->requested_upgrade;

			if ($data['bachelor_plan'] == 1 ) {
				$data['org_plan_status'] = 'Bachelor';
			}else{
				$data['org_plan_status'] = 'Free';
			}
		}else{
            $pu = PremiumUser::on('rds1')->where('user_id', $user->id)->first();

            $data['premium_user_level_1'] = isset($pu) ? 1 : 0;
            $data['premium_user_type'] = isset($pu) ? $pu->type : null;

            $num_of_premium_essays_viewed = UsersPremiumEssay::on('rds1')->where('user_id', $user->id)->count();

            $num_of_applied_colleges      = UsersAppliedColleges::on('rds1')->where('user_id', $user->id)
                                                                            ->where('submitted', 1)
                                                                            ->count();


            if( isset($pu) ){
                $data['premium_user_plan'] = $pu->type === 'onetime_plus' ? 'premium plus' : 'premium';

                $total_num_of_eligible_essays = 0;
                $total_num_of_applied_colleges = 0;

                if ($pu->type === 'onetime_plus') {
                    $total_num_of_eligible_essays = 50;
                    $total_num_of_applied_colleges = 10;
                }elseif ($pu->type === 'onetime') {
                    $total_num_of_eligible_essays = 20;
                    $total_num_of_applied_colleges = 5;
                }elseif ($pu->type =='plexuss_free') {
                    $total_num_of_eligible_essays = 1;
                    $total_num_of_applied_colleges = 5;
                }else{
                    $total_num_of_eligible_essays = 1;
                    $total_num_of_applied_colleges = 5;
                }

                $data['num_of_eligible_premium_essays'] = $total_num_of_eligible_essays - $num_of_premium_essays_viewed;
                if ($data['num_of_eligible_premium_essays'] < 0) {
                    $data['num_of_eligible_premium_essays'] = 0;
                }

                $data['num_of_eligible_applied_colleges'] = 10 - $num_of_applied_colleges;
                if ($data['num_of_eligible_applied_colleges'] < 0) {
                    $data['num_of_eligible_applied_colleges'] = 0;
                }

            }else{
                $total_num_of_eligible_essays = 1;

                $data['num_of_eligible_premium_essays'] = $total_num_of_eligible_essays - $num_of_premium_essays_viewed;
                if ($data['num_of_eligible_premium_essays'] < 0) {
                    $data['num_of_eligible_premium_essays'] = 0;
                }
            }
        }

		if ($user->is_plexuss == 1) {
			$usc = UsersSalesControl::on('rds1')->where('user_id', $user->id)->first();

			if (isset($usc)) {
				$data['is_sales'] = true;
			}

            $data['is_plexuss'] = 1;
		}else{
            $data['is_plexuss'] = 0;
        }


		$data['remember_token']           = $user->remember_token;
		$data['profile_img_loc']          = $user->profile_img_loc;
		$data['fname']                    = $user->fname;
		$data['lname']                    = $user->lname;
		$data['user_id']                  = $user->id;
		$ip = Request::getClientIp();
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		   if (isset( $_SERVER['REMOTE_ADDR']) ) {
		    $ip =  $_SERVER['REMOTE_ADDR'];
		   }
		}
		$data['ip'] = $ip;
		$data['email']                    = $user->email;
		$data['email_provider']           = $this->getEmailProviderDomain($data['email']);
        $data['school_logo']              = isset($data['school_logo']) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$data['school_logo'] : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/default.png';
		$data['gender']                   = $user->gender;
        $data['profile_percent']          = $user->profile_percent;
        $data['zip']                      = $user->zip;
        $data['email_confirmed']          = $user->email_confirmed;
        $data['completed_signup']         = $user->completed_signup;
        $data['txt_opt_in']               = $user->txt_opt_in;
        $data['country_id']               = $user->country_id;
        $data['is_plexuss']               = $user->is_plexuss;

        if( Session::has('userinfo.is_student') ){
            $data['is_student']     = Session::get('userinfo.is_student');
        }elseif( Session::has('userinfo.is_parent') ){
            $data['is_parent']      = Session::get('userinfo.is_parent');
        }
        if( Session::has('userinfo.aor_id') ){
            $data['aor_id']     = Session::get('userinfo.aor_id');
        }

        $data['premier_trial_end_date_ACTUAL']   =  isset($data['premier_trial_end_date']) ? $data['premier_trial_end_date'] : null;
        $data['premier_trial_begin_date_ACTUAL'] = isset($data['premier_trial_begin_date']) ? $data['premier_trial_begin_date'] : null;

        $data['organization_portals'] = Session::get('userinfo.organization_portals');
        $data['default_organization_portal'] = Session::get('userinfo.default_organization_portal');

        if (isset($data['premier_trial_end_date'])) {
            $today = date('Y-m-d');

            $trial_date = $data['premier_trial_end_date'];
            $datetime1 = new DateTime($today);
            $datetime2 = new DateTime($trial_date);

            $interval = $datetime1->diff($datetime2);

            $data['premier_trial_end_date'] = $interval->format('%R%a');

            $data['premier_trial_end_date'] = str_replace('+', '', $data['premier_trial_end_date']);

            $data['premier_trial_end_date'] = $data['premier_trial_end_date'] > 0 ? $data['premier_trial_end_date'] : null;

            if (!isset($data['premier_trial_end_date'])) {
                $data['show_upgrade_button'] = 1;
            }else{
                $data['show_upgrade_button'] = 0;
            }
        }

        if (isset($data['org_plan_status']) && $data['org_plan_status'] == 'Free') {
            $data['show_upgrade_button'] = 1;
        }

        $current_live_colleges = $this->is_any_school_online();

		if ($current_live_colleges == true) {
			$data['is_any_school_live'] = true;
		}else{
			$data['is_any_school_live'] = false;
		}

        $is_sales = Session::get('userinfo.is_sales');
        if (isset($is_sales)) {
            $data['is_sales'] = true;
        }

        $data['is_agency'] = Session::get('userinfo.is_agency');

        if ($data['is_agency'] == 1) {
            $agency = new Agency;

            $data['agency_collection'] = $agency->getAgencyProfile($data['user_id']);

            if (!isset($data['agency_collection'])) {
            	$data['is_agency'] = 0;
            	return $data;
            }

            $today = date('Y-m-d');

            $trial_date = $data['agency_collection']->trial_end_date;
            $datetime1 = new DateTime($today);
            $datetime2 = new DateTime($trial_date);

            $interval = $datetime1->diff($datetime2);

            $data['remaining_trial'] = $interval->format('%R%a');

            $data['remaining_trial'] = str_replace('+', '', $data['remaining_trial']);
            $data['balance'] = $data['agency_collection']->balance;
        }

		return $data;
	}


	public function saveCollegeSubmission(){
		$input = Request::all();

		$collegeSubmission = new CollegeSubmission;
		$collegeSubmission->company = isset($input['company'])? $input['company'] : '';
		$collegeSubmission->contact = isset($input['joinName'])? $input['joinName'] : '';
		$collegeSubmission->title   = isset($input['title'])? $input['title'] : '';
		if (isset($input['news_email'])) {
			$collegeSubmission->email   = isset($input['news_email'])? $input['news_email'] : '';
		} else if (isset($input['email'])){
			$collegeSubmission->email   = isset($input['email'])? $input['email'] : '';
		} else {
			$collegeSubmission->email   = isset($input['joinEmail'])? $input['joinEmail'] : '';
		}


		isset($input['joinPhone'])? $collegeSubmission->phone = $input['joinPhone'] : NULL;

		isset($input['phone'])? $collegeSubmission->phone = $input['phone'] : NULL;

		$collegeSubmission->notes   = isset($input['notes'])? $input['notes'] : '';

		isset($input['joinBlogNews']) ? $collegeSubmission->newsletter = 1 : NULL;
		isset($input['joinAnalytics']) ? $collegeSubmission->analyticsletter = 1 : NULL;

		$collegeSubmission->first_name = isset($input['first_name']) ? $input['first_name'] : '';
		$collegeSubmission->last_name = isset($input['last_name']) ? $input['last_name'] : '';

		$collegeSubmissionClientType = new CollegeSubmissionClientType();

		foreach ($input['client_type'] as $client_type) {
			if($client_type == 'domestic_recruitment') {
				$collegeSubmissionClientType->domestic_recruitment = 1;
			} else if ($client_type == 'intl_recruitment') {
				$collegeSubmissionClientType->intl_recruitment = 1;
			} else if ($client_type == 'retention_student_success') {
				$collegeSubmissionClientType->retention_student_success = 1;
			} else if ($client_type == 'consulting') {
				$collegeSubmissionClientType->consulting = 1;
			} else if ($client_type == 'advertising') {
				$collegeSubmissionClientType->advertising = 1;
			} else if ($client_type == 'other') {
				$collegeSubmissionClientType->other = 1;
			}
		}

		$collegeSubmission->save();
		$collegeSubmission->collegeSubmissionClientType()->save($collegeSubmissionClientType);


		$template_name  = 'plexuss_survey_email_r1';
		$mac = new MandrillAutomationController();

		$mac->generalEmailSend('collegeservices@plexuss.com', $template_name, NULL, $collegeSubmission->email);

		$plexuss_emails = array('anthony.shayesteh@plexuss.com', 'sina.shayesteh@plexuss.com', 'jp.novin@plexuss.com');

		$this->sendemailalert('New College Submission' ,$plexuss_emails  ,array('data' => Request::all() ));

		// $email_content = "<p>Thank you for signing up on Plexuss.  We would like an opportunity to tell you more about our services.  To make scheduling easier, I am providing a link to my calendar.</p>

		// 				<p><a href='https://plexuss.youcanbook.me'>https://plexuss.youcanbook.me</a></p>

		// 				<p>Look forward to talk to you,</p>

		// 				<p>JP</p>";

		// $subject = 'Invite from Plexuss';
		// $toEmails = array($collegeSubmission->email);
		// $name = "JP From Plexuss";
		// $data = array();
		// $data['email_content'] = $email_content;

		// Mail::send( 'emails.blankEmail', $data, function( $message ) use ( $subject ,$toEmails, $name, $plexuss_emails ) {
		// 		$message->to( $toEmails, $name )
		// 				->bcc($plexuss_emails)
		// 		        ->subject( $subject );
		// 	}
		// );

		return "success";
	}

	/**
	 * saveOrgSavedAttachments
	 *
	 * saves the message template for the college or agency
	 *
	 * @return null
	 */
	public function saveOrgSavedAttachments(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (!isset($input['name']) || empty($input['name']) || !isset($input['file']) || empty($input['file'])) {
			return "failed";
		}

		if (isset($data['agency_collection'])) {
			$agency_id = $data['agency_collection']->agency_id;
			$org_branch_id = NULL;

		}else{
			$org_branch_id = $data['org_branch_id'];
			$agency_id = NULL;
		}

		// If this is a regular user, then upload it to their transcript bucket url not the attachment url
		if (isset($input['file_type'])) {
			$bucket_url = 'asset.plexuss.com/users/transcripts';
		}else{
			$bucket_url = 'asset.plexuss.com/admin/attachments';
		}

		$upload_file = $this->generalUploadDoc($input, 'file', $bucket_url);
		$url = $upload_file['url'];

		//if saving from admin/messages
		if( isset($input['id']) ){
			$decryptedId = Crypt::decrypt($input['id']);
			$attr = array('id' => $decryptedId);
		}else{
			//else coming from admin/campaign<div></div>s
			$attr = array('name' => $input['name'], 'agency_id' => $agency_id, 'org_branch_id' => $org_branch_id);
		}

		$val  = array('name' => $input['name'], 'agency_id' => $agency_id, 'org_branch_id' => $org_branch_id, 'url' => $url);

		$mt = OrgSavedAttachment::updateOrCreate($attr, $val);

		$arr = array();
		$arr['id']   = Crypt::encrypt($mt->id);
		$arr['name'] = $input['name'];
		$arr['url']  = $url;

		// If this is a regular user, then upload it to their transcript.
		if (isset($input['file_type'])) {
			$transcript = new Transcript;

			$transcript->user_id     = $data['user_id'];
			$transcript->doc_type    = strtolower($input['file_type']);
			$transcript->school_type = 'highschool';

			$file_name = substr($url, strrpos($url, '/') + 1);

			$transcript->transcript_name = $file_name;
			$transcript->transcript_path = str_replace($file_name, '', $url);

			$transcript->save();
		}

		return $arr;
	}


	/**
	 * loadOrgSavedAttachments
	 *
	 * load content of a message template for the college or agency
	 *
	 * @return null
	 */
	public function loadOrgSavedAttachments(){

		$input = Request::all();

		if (!isset($input['id']) || empty($input['id'])) {
			return "failed";
		}

		$mt = OrgSavedAttachment::find(Crypt::decrypt($input['id']));
		$arr = array();

		$arr['url'] = $mt->url;
		$arr['name'] = $mt->name;
		$arr['date'] = $mt->created_at;

		return $arr;
	}

	/**
	 * loadOrgSavedAttachments
	 *
	 * load content of a message template for the college or agency
	 *
	 * @return null
	 */
	public function deleteOrgSavedAttachments(){

		$input = Request::all();

		if (!isset($input['id']) || empty($input['id'])) {
			return "failed";
		}

		$mt = OrgSavedAttachment::find(Crypt::decrypt($input['id']));
		$deleted = $mt->delete();

		return $deleted ? 'success' : 'failed';
	}

	public function getOrgSavedAttachmentsList(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
		$data = $this->getOrgSavedAttachments($data, true);
		return $data['saved_attachments'];
	}

    /**
     * This method confirms the user has shared their signup link on facebook
     *
     * @return 'success' || 'fail'
     */
    public function saveSignupFacebookShare() {
        $input = Request::all();

        return UsersSharedSignup::insertShare($input['user_id'], $input['utm_term'], 'additional_apps');
    }

    public function nrccuaInsertSchools() {
        $nrccua_school_array = [102049,104151,105899,107141,109785,110334,110361,110556,110714,112570,114813,115728,119173,120184,120403,120537,121150,121345,121691,122931,123165,123457,126669,128391,128744,130226,130590,130776,130989,133553,135726,136330,137847,138868,139931,140872,141334,141486,142285,144050,145619,145725,146612,147341,147369,149505,149772,149781,150774,151290,151379,151777,152080,153144,153375,153533,154004,154095,154590,154688,155317,157793,160977,161086,161457,161572,164076,164155,164739,164988,166513,167358,167394,167899,168740,170675,171146,173142,173258,174358,174783,174899,176053,177418,180416,180489,181446,181464,181783,182005,182980,183910,185572,186380,186584,187912,188030,189705,190248,190372,191630,191649,191676,191968,192271,192323,192749,193399,194310,194392,194578,194958,195030,195216,195526,195809,196060,196079,196097,196167,196185,196194,196200,197036,197984,198419,200572,201195,201548,201964,202046,202523,203517,204194,206349,207263,207500,208822,209603,210331,210669,211088,212577,212601,212674,212805,212832,213011,213321,213358,213996,214069,214157,215053,215099,215266,216542,216597,216807,217305,217633,217998,218539,218663,219082,219259,219976,220808,221014,221519,221953,221999,222831,224226,226833,227845,227881,228149,228769,228875,229018,229115,229160,230597,231174,233921,235167,235316,236230,236452,236939,237011,237066,237525,238476,238980,239017,240338,243780,245652,245953,247649,455770];

        foreach ($nrccua_school_array as $ipeds_id) {
            $college = College::where('ipeds_id', '=', $ipeds_id)->first();

            // Skip if college does not exist.
            if (!$college) continue;

            $school_id = $college->id;

            $org_branch = OrganizationBranch::select('id')->where('school_id', '=', $school_id)->first();

            if (!$org_branch) {
                // insert into Organization
                $org_attributes = ['name' => $college->school_name];
                $org_values = ['name' => $college->school_name, 'address' => $college->address];
                $org = Organization::updateOrCreate($org_attributes, $org_values);

                // insert into Organization branch
                $org_branch_attributes = ['school_id' => $school_id, 'slug' => $college->slug, 'organization_id' => $org->id];
                $org_branch_values = ['school_id' => $school_id, 'slug' => $college->slug, 'organization_id' => $org->id];
                $org_branch = OrganizationBranch::updateOrCreate($org_branch_attributes, $org_branch_values);
            }

            $org_branch_id = $org_branch->id;

            $attributes = [
                'org_branch_id' => $org_branch_id,
            ];

            $values = [
                'org_branch_id' => $org_branch_id,
                'delivery_url' => 'https://api.partner.nrccua.org/v1/lead',
                'delivery_type' => 'POST',
                'response_type' => 'XML',
                'success_tag' => 'httpCode',
                'success_string' => '200',
                'failed_tag' => 'errors',
            ];

            DistributionClient::updateOrCreate($attributes, $values);
        }

        return 'success';

    }

    public function nrccuaDistributionClientFieldMapping() {
        $clients = DistributionClient::select('*')->where('delivery_url', '=', 'https://api.partner.nrccua.org/v1/lead')->get();

        $fields = [
            // ['client_field_name' => 'globalContactOptIn', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'email', 'plexuss_field_id' => 9, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'firstName', 'plexuss_field_id' => 1, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'lastName', 'plexuss_field_id' => 2, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'birthday', 'plexuss_field_id' => 28, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'addressLine1', 'plexuss_field_id' => 6, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'city', 'plexuss_field_id' => 7, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'state', 'plexuss_field_id' => 14, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'zip', 'plexuss_field_id' => 8, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'country', 'plexuss_field_id' => 13, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'gender', 'plexuss_field_id' => 11, 'field_type' => 'dropdown', 'is_required' => 1],
            // ['client_field_name' => 'userType', 'plexuss_field_id' => 10, 'field_type' => 'dropdown', 'is_required' => 1],
            // ['client_field_name' => 'utmCampaign', 'plexuss_field_id' => 19, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            // ['client_field_name' => 'highSchoolGpa', 'plexuss_field_id' => 20, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'satEnglish', 'plexuss_field_id' => 22, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'satWriting', 'plexuss_field_id' => 23, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'satMath', 'plexuss_field_id' => 24, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'satReadingWriting', 'plexuss_field_id' => 25, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'partnerInquiryCaptureDate', 'plexuss_field_id' => 26, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'highSchoolGradYear', 'plexuss_field_id' => 27, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'cellPhone', 'plexuss_field_id' => 29, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'isInterestedInOnlineEducation', 'plexuss_field_id' => 30, 'field_type' => 'dropdown', 'is_required' => 0],
            ['client_field_name' => 'currentSchoolIpedId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'interestedSchoolIpedId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

        ];

        foreach ($clients as $client) {
            foreach ($fields as $field) {
                $dc_id = $client->id;

                $attributes = [
                    'dc_id' => $dc_id,
                    'client_field_name' => $field['client_field_name'],
                ];

                $values = [
                    'dc_id' => $dc_id,
                    'client_field_name' => $field['client_field_name'],
                    'plexuss_field_id' => $field['plexuss_field_id'],
                    'field_type' => $field['field_type'],
                    'is_required' => $field['is_required'],
                ];

                DistributionClientFieldMapping::updateOrCreate($attributes, $values);
            }
        }

        return 'success';
    }

    public function nrccuaDistributionClientValueMapping() {
        // $distribution_field_mappings = DistributionClientFieldMapping::select('*')->where('client_field_name', '=', 'userType')->get();

        // $fields = [
        //     ['is_default' => '0', 'plexuss_value' => 1, 'client_value' => 'college-student', 'client_value_name' => null],
        //     ['is_default' => '0', 'plexuss_value' => 0, 'client_value' => 'highschool-student', 'client_value_name' => null],
        // ];

        // $distribution_field_mappings = DistributionClientFieldMapping::select('*')->where('client_field_name', '=', 'gender')->get();

        // $fields = [
        //     ['is_default' => '0', 'plexuss_value' => 'm', 'client_value' => 'male', 'client_value_name' => null],
        //     ['is_default' => '0', 'plexuss_value' => 'f', 'client_value' => 'female', 'client_value_name' => null],
        // ];

        $distribution_field_mappings = DistributionClientFieldMapping::select('*')->where('client_field_name', '=', 'interestedSchoolIpedId')->get();

        $fields = [
            ['is_default' => '0', 'plexuss_value' => null, 'client_value_name' => null],
        ];

        foreach ($distribution_field_mappings as $mapping) {
            // Get distribution org_school_id
            $dc = DistributionClient::find($mapping->dc_id);

            $org_branch = OrganizationBranch::find($dc->org_branch_id);

            $colleges = College::find($org_branch->school_id);

            foreach ($fields as $field) {
                $attributes = [
                    'is_default' => $field['is_default'],
                    'plexuss_value' => $field['plexuss_value'],
                    'client_value' => $colleges->ipeds_id,
                    'client_value_name' => '',
                    'dc_id' => $mapping->dc_id,
                    'dcfm_id' => $mapping->id,
                ];

                $values = [
                    'is_default' => $field['is_default'],
                    'plexuss_value' => $field['plexuss_value'],
                    'client_value' => $colleges->ipeds_id,
                    'client_value_name' => '',
                    'dc_id' => $mapping->dc_id,
                    'dcfm_id' => $mapping->id,
                ];

                DistributionClientValueMapping::updateOrCreate($attributes, $values);
            }
        }

        return 'success';
    }

    public function nrccuaStoreMajors() {
        $client = new Client();
        $response = $client->request('GET', 'http://chegg-partner-service-stage.8pt9xqz6bp.us-east-1.elasticbeanstalk.com/partner-api-lookup?f.type=fieldOfStudy&offset=0&limit=500');

        $majors = json_decode($response->getBody(), 1)['result'];

        foreach ($majors as $major) {
            $attributes = ['key' => $major['key'], 'display_name' => $major['displayName']];
            $values = ['key' => $major['key'], 'display_name' => $major['displayName']];
            NrccuaMajors::updateOrCreate($attributes, $values);
        }
    }

    public function nrccuaHandleMajorMapping() {
        $clients = DistributionClient::select('id')->where('delivery_url', '=', 'https://api.partner.nrccua.org/v1/lead')->get();
        $ids = [];

        foreach ($clients as $client) {
            $ids[] = $client->id;
        }

        return($ids);
    }

    public function insertEducationDynamicsSchools() {
        $college_ids = [4481,2437,1754,750,761,1128,3014,3131,533,2606,756,2443,3388,2490,1129,3554,1072,1851,2059,3601,3767,2908,1501,4124,2169,6033,962,1391,1552,1086,3169,5787,2375,635,1472,5391,105,5782,5861,2791,599,4106,1795,3602,5968,4081,488,1846,669,885];

        $school_names = ["Purdue University-Main Campus","Rutgers University-New Brunswick","Brandeis University","University of Delaware","George Washington University","University of Illinois at Chicago","University of Cincinnati-Main Campus","Ohio University-Main Campus","University of San Francisco","Hofstra University","Catholic University of America","Seton Hall University","Duquesne University","Adelphi University","Benedictine University","Saint Joseph's University","Bradley University","Simmons College","Saint Mary's University of Minnesota","Widener University-Main Campus","Maryville College","Queens University of Charlotte","Eastern Kentucky University","Liberty University","A T Still University of Health Sciences","American InterContinental University-Online","South University-Savannah","Ashford University","Sullivan University","The Chicago School of Professional Psychology at Chicago","Tiffin University","Ultimate Medical Academy-Clearwater","Centenary College","Colorado Technical University-Colorado Springs","University of Saint Mary","Embry-Riddle Aeronautical University-Worldwide","Grand Canyon University","University of the Rockies","Grantham University","Utica College","Walden University","Jefferson College of Health Sciences","Lasell College","Wilkes University","Northcentral University","Norwich University","Pacific Oaks College","Regis College","Regis University","Saint Leo University"];

        $delivery_urls = ["https://www.elearners.com/a/Plexuss/form/140","https://www.elearners.com/a/Plexuss/form/3372","https://www.elearners.com/a/Plexuss/form/48","https://www.elearners.com/a/Plexuss/form/4317","https://www.elearners.com/a/Plexuss/form/198","https://www.elearners.com/a/Plexuss/form/84","https://www.elearners.com/a/Plexuss/form/82","https://www.elearners.com/a/Plexuss/form/292","https://www.elearners.com/a/Plexuss/form/553","https://www.elearners.com/a/Plexuss/form/1934","https://www.elearners.com/a/Plexuss/form/555","https://www.elearners.com/a/Plexuss/form/349","https://www.elearners.com/a/Plexuss/form/147","https://www.elearners.com/a/Plexuss/form/5472","https://www.elearners.com/a/Plexuss/form/133","https://www.elearners.com/a/Plexuss/form/143","https://www.elearners.com/a/Plexuss/form/941","https://www.elearners.com/a/Plexuss/form/3581","https://www.elearners.com/a/Plexuss/form/492","https://www.elearners.com/a/Plexuss/form/5525","https://www.elearners.com/a/Plexuss/form/5176","https://www.elearners.com/a/Plexuss/form/5191","https://www.elearners.com/a/Plexuss/form/157","https://www.elearners.com/a/Plexuss/form/247","https://www.elearners.com/a/Plexuss/form/36","https://www.elearners.com/a/Plexuss/form/21","https://www.elearners.com/a/Plexuss/form/357","https://www.elearners.com/a/Plexuss/form/44","https://www.elearners.com/a/Plexuss/form/371","https://www.elearners.com/a/Plexuss/form/134","https://www.elearners.com/a/Plexuss/form/385","https://www.elearners.com/a/Plexuss/form/446","https://www.elearners.com/a/Plexuss/form/8398","https://www.elearners.com/a/Plexuss/form/119","https://www.elearners.com/a/Plexuss/form/444","https://www.elearners.com/a/Plexuss/form/1570","https://www.elearners.com/a/Plexuss/form/188","https://www.elearners.com/a/Plexuss/form/437","https://www.elearners.com/a/Plexuss/form/195","https://www.elearners.com/a/Plexuss/form/145","https://www.elearners.com/a/Plexuss/form/475","https://www.elearners.com/a/Plexuss/form/2197","https://www.elearners.com/a/Plexuss/form/244","https://www.elearners.com/a/Plexuss/form/5037","https://www.elearners.com/a/Plexuss/form/272","https://www.elearners.com/a/Plexuss/form/282","https://www.elearners.com/a/Plexuss/form/3137","https://www.elearners.com/a/Plexuss/form/3294","https://www.elearners.com/a/Plexuss/form/322","https://www.elearners.com/a/Plexuss/form/366"];

        for ($i = 0; $i < 50; $i++) {
            $tmp = [];
            $tmp['college_id'] = $college_ids[$i];
            $tmp['school_name'] = $school_names[$i];
            $tmp['delivery_url'] = $delivery_urls[$i];
            $insertArray[] = $tmp;
        }

        foreach ($insertArray as $values) {
            // Uncomment this to run, disabled for safety.
            // DistributionClient::insert($values);
        }
    }

    public function insertEductionDynamicsClientFieldMappings() {
        $distribution_client_ids = [215, 216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265];

        $fields = [
            ['client_field_name' => 'First_Name', 'plexuss_field_id' => 1, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'Last_Name', 'plexuss_field_id' => 2, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'Prefix', 'plexuss_field_id' => 11, 'field_type' => 'dropdown', 'is_required' => 0],

            ['client_field_name' => 'Address', 'plexuss_field_id' => 6, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'City', 'plexuss_field_id' => 7, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'State', 'plexuss_field_id' => 14, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'Phone', 'plexuss_field_id' => 3, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'Highest_Level_of_Education_Completed', 'plexuss_field_id' => 10, 'field_type' => 'dropdown', 'is_required' => 1],

            ['client_field_name' => 'Year_of_Highest_Education_Completed', 'plexuss_field_id' => 27, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'Desired_Start_Date', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],

            ['client_field_name' => 'Age', 'plexuss_field_id' => 31, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'us_citizen', 'plexuss_field_id' => 32, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'Email', 'plexuss_field_id' => 9, 'field_type' => 'plexuss_field_value', 'is_required' => 1],

            ['client_field_name' => 'Country', 'plexuss_field_id' => 13, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        ];

        foreach($distribution_client_ids as $distribution_client_id) {
            foreach ($fields as $field) {
                $values = [];
                $values['dc_id'] = $distribution_client_id;
                $values['client_field_name'] = $field['client_field_name'];
                $values['plexuss_field_id'] = $field['plexuss_field_id'];
                $values['field_type'] = $field['field_type'];
                $values['is_required'] = $field['is_required'];

                // Disabled for safety, uncomment to insert
                // DistributionClientFieldMapping::insert($values);
            }
        }
    }

    public function insertEducationDynamicsClientValueMappings() {
        // $distribution_field_mappings = DistributionClientFieldMapping::select('*')->where('client_field_name', '=', 'Prefix')->where('dc_id', '>=', 215)->get();

        // $fields = [
        //     ['is_default' => '0', 'plexuss_value' => 'm', 'client_value' => 'Mr.'],
        //     ['is_default' => '0', 'plexuss_value' => 'f', 'client_value' => 'Ms.'],
        // ];

        // $distribution_field_mappings = DistributionClientFieldMapping::select('*')->where('client_field_name', '=', 'Highest_Level_of_Education_Completed')->where('dc_id', '>=', 215)->get();

        // $fields = [
        //     ['is_default' => '0', 'plexuss_value' => '0', 'client_value' => '3'],
        //     ['is_default' => '0', 'plexuss_value' => '1', 'client_value' => '5'],
        // ];

        $distribution_field_mappings = DistributionClientFieldMapping::select('*')->where('client_field_name', '=', 'Desired_Start_Date')->where('dc_id', '>=', 215)->get();

        $fields = [
            ['is_default' => '0', 'plexuss_value' => null, 'client_value' => '1'],
        ];

        foreach ($distribution_field_mappings as $mapping) {
            foreach ($fields as $field) {
                $values = [
                    'is_default' => $field['is_default'],
                    'plexuss_value' => $field['plexuss_value'],
                    'client_value' => $field['client_value'],
                    'dc_id' => $mapping->dc_id,
                    'dcfm_id' => $mapping->id,
                ];
                // Disabled for safety, uncomment to insert
                // DistributionClientValueMapping::insert($values);
            }
        }
    }

    public function checkIpedsIdExist() {
        $ipeds_ids = [141486,154493,215275,146481,240417,238193,118888,130989,145691,191621,188854,240471,237367,232706,145619,196185,148584,125897,130776,196024,212601,145646,164173,235167,231420,213321,147244,140447,227845,144962,164739,201371,215266,119173,217776,147828,141325,233277,219046,178721,174844,150455,239017,110097,239390,199272,199607,233541,209922,235769,237525,215743,214175,154590,228149,179955,173300,236230,211644,193399,133669,193654,145813,194578,227216,121257,158477,230931,107558,163295,207582,228325,224554,229160,176965,171146,151801,190099,217998,445188,482680,186584,195030,200004,129215,238333,146825,192819,122409,196149,154004,130226,133492,216807,181534,219976,228529,232308,130697,204909,136215,215132,196237,196200,171137,217925,141334,122436,217165,150400,216278,153162,166513,134079,196194,173142,210669,183239,144281,151777,146427,236452,233295,198808,126818,149231,177065,184773,366252,152390,167394,197142,150163,179326,161165,180258,192703,239512,156408,143048,173665,142461,110680,101587,181215,142285,170286,183910,110370,190549,133553,185572,195544,195526,155317,200217,174075,240107,199865,140951,196097,169248,217420,140988,196413,122454,165662,129020,228769,168227,152992,110361,128771,212197,232089,150756,151290,174127,216542,240277,213358,215099,104151,183026,233374,212133,151111,120184,194392,245652,196866,104179,127060,174066,215309,240268,154527,216764,125727,168421,164632,180416,238661,220613,182290,209551,218973,188429,116846,238430,201654,211556,202046,202134,120537,196158,118541,194824,232025,237066,174817,237057,165802,147703,204796,168005,204857,139861,220631,204501,153658,153603,243780,194091,184348,177418,193292,221254,152099,125763,134130,239318,127741,121691,195216,170675,216357,153269,112570,234207,231095,193946,165936,154095,430810,197045,233718,229018,228802,189848,115728,195720,175078,228246,144050,190372,138789,218539,163578,139931,148654,150145,232423,230807,215293,236939,191649,127556,238476,199148,153375,218742,177214,218654,227881,122612,139959,234030,209542,198950,149222,199412,151351,139755,448840,194310,164720,132657,157757,230995,213011,147679,178341,133881,113698,137032,102614,185828,178411,201885,462354,237950,238032,165334,202806,146667,203845,181376,123457,195234,128744,110671,161457,123651,207458,144971,128391,137847,159939,217059,195809,132471,218724,148496,196006,162760,234085,197230,130624,212674,230852,221661,153834,206835,239071,219082,203535,222178,216010,171100,109785,222831,143118,233426,229115,142115,126182,234173,226833,192323,192864,120254,110556,218414,206330,220710,225247,199069,211291,196042,168290,217934,161873,161554,227863,196051,110705,150534,174233,219602,224004,191126,213251,185590,195474,123952,163912,221971,170976,228787,240426,199962,178411,168786,235097,112075,121150,215284,230898,139199,156745,159656,237330,145725,192749,215099,130943,240727,210401];

        $empty_list = [];

        $college_ids = '';

        foreach ($ipeds_ids as $ipeds_id) {
            $query = College::where('ipeds_id', '=', $ipeds_id)->first();

            $college_ids .= ($query->id . ',');
        }

        dd($college_ids);
    }

    public function insertCappexSchools() {
        // $ipeds_ids = [141486,154493,215275,146481,240417,238193,118888,130989,145691,191621,188854,240471,237367,232706,145619,31155,196185,148584,125897,130776,196024,212601,145646,164173,235167,231420,213321,147244,140447,227845,144962,164739,201371,215266,119173,217776,147828,141325,233277,219046,178721,174844,150455,239017,110097,239390,199272,199607,233541,209922,235769,237525,215743,214175,154590,228149,179955,173300,236230,211644,193399,133669,193654,145813,194578,227216,121257,158477,230931,107558,163295,207582,228325,224554,229160,176965,171146,151801,190099,217998,445188,482680,186584,195030,200004,129215,238333,146825,192819,122409,196149,154004,130226,133492,216807,181534,219976,228529,232308,130697,204909,136215,215132,196237,31089,196200,171137,217925,141334,122436,217165,150400,216278,153162,166513,134079,196194,173142,210669,183239,144281,151777,146427,236452,233295,198808,126818,149231,177065,184773,366252,152390,167394,197142,150163,179326,161165,180258,192703,239512,156408,143048,173665,142461,110680,101587,181215,142285,170286,183910,110370,190549,133553,185572,31227,195544,195526,155317,200217,174075,240107,199865,140951,196097,169248,217420,140988,196413,122454,165662,129020,228769,168227,152992,110361,128771,212197,232089,150756,151290,174127,216542,240277,213358,215099,104151,183026,233374,212133,151111,120184,194392,245652,196866,104179,127060,174066,215309,240268,154527,216764,125727,168421,164632,180416,238661,220613,182290,209551,218973,188429,116846,238430,201654,211556,202046,202134,120537,196158,118541,194824,232025,237066,174817,237057,165802,31113,147703,204796,168005,204857,139861,220631,204501,153658,153603,243780,194091,31200,31231,184348,177418,31234,193292,221254,152099,125763,134130,239318,127741,121691,195216,170675,216357,153269,112570,234207,231095,31221,193946,165936,154095,430810,197045,233718,229018,228802,189848,115728,195720,175078,228246,144050,190372,138789,218539,163578,139931,148654,150145,232423,230807,215293,236939,191649,127556,238476,199148,153375,218742,177214,218654,227881,122612,139959,234030,209542,198950,149222,199412,151351,139755,448840,194310,164720,132657,157757,230995,213011,147679,178341,133881,113698,137032,102614,185828,178411,201885,462354,237950,238032,165334,202806,146667,203845,181376,123457,195234,128744,110671,161457,123651,207458,144971,128391,137847,159939,217059,195809,132471,218724,148496,196006,162760,234085,197230,130624,212674,230852,221661,153834,206835,239071,219082,203535,222178,216010,171100,109785,222831,143118,233426,229115,142115,126182,234173,226833,192323,192864,120254,110556,218414,206330,220710,225247,199069,211291,196042,168290,217934,161873,161554,227863,196051,110705,150534,174233,31163,219602,224004,191126,213251,185590,195474,123952,163912,221971,170976,228787,240426,199962,178411,168786,235097,112075,121150,215284,230898,140322,139199,156745,159656,237330,145725,192749,215099,130943,240727,210401];

        // $campaign_ids = [300580,297459,298071,298197,298351,299195,299275,300309,299298,299350,302367,302387,302536,302560,302850,302863,302458,302565,303027,303130,303138,304401,304405,304440,304446,304714,304933,305314,305456,305640,305619,305832,306181,306201,306211,308106,308291,308817,309076,309641,309627,310205,310201,310740,311167,311546,311714,312167,312244,312380,313359,313687,313960,314465,314567,314942,315809,316386,317217,317469,318225,320264,321711,321714,321718,324040,324257,324261,324263,324264,324265,324266,324382,324381,324390,324391,326799,327722,328161,328165,329691,329693,329697,329704,329705,329845,330604,330834,331321,331327,331328,331329,331362,331364,332004,332063,332066,332289,332290,332891,332996,333502,334878,334881,334884,335437,335440,339189,339685,339693,340668,340667,340877,340884,341475,341945,341952,341956,341959,341961,341963,341969,342112,342280,342555,343503,343506,343452,343453,343515,343517,343521,343525,343802,343803,343805,343806,343807,343816,343817,343818,343819,343821,344124,344161,344160,344159,347434,347670,347943,347945,347946,348164,348178,348189,348197,348199,348203,348205,348232,348236,348247,348470,348475,349740,350442,350957,351823,352205,353004,353009,354780,362043,362750,362797,362804,362805,362810,362814,362816,362843,362855,362857,300372,364013,368165,368863,323945,371792,371806,371807,371810,371815,371816,372122,372123,372126,372138,372140,372142,372144,372145,372148,372149,373368,373370,373372,376416,376422,376431,376896,376899,376909,377892,376913,376916,376917,376927,376928,377368,377369,377552,377640,377743,377864,377865,377870,377871,377873,377874,377875,377876,377878,377879,377881,377882,377886,377888,377890,378610,379168,379169,381381,382314,382317,382323,384434,384441,384442,384747,384749,384750,390720,390855,390941,393383,393382,394051,394062,394063,394065,394069,394070,394072,394073,394074,394077,394078,394079,394080,394081,394082,394085,395112,397770,398942,399368,399369,399433,399435,399441,399444,399447,399450,399451,399452,399560,399695,400042,400352,400469,400470,400473,400806,400808,400809,400812,400813,400814,400816,400817,400818,400820,400821,400822,400825,400828,400829,400831,400835,400837,400838,400840,400846,400849,400853,400854,401054,401055,401118,401610,401778,401779,401782,401792,401795,401796,401797,401798,401800,401801,401803,401815,402086,402245,402415,402881,402921,402922,402923,402924,402925,402926,403500,403499,404138,404139,404140,404142,404146,404148,404149,404150,404152,404740,404776,404988,405408,405409,405410,405454,405455,405463,405464,405465,405466,405468,405469,405470,405471,405480,405481,405482,405483,405484,405485,405486,405487,405488,405489,405490,405491,405492,405530,405531,405536,405587,406170,406746,406747,406748,406749,406750,406751,406752,406753,406754,406755,406756,406757,406758,406823,407183,407301,407619,407620,407621,407879,407880,408679,409107,409108,409248,409249,409250,409251,409252,409254,409255];

        // $ipeds_ids = [149505,157863,166674,168421,170301,228787,231174];

        // $campaign_ids = [410913,410912,410459,372148,410909,406756,410910];

        $ipeds_ids = [131098,169442,173258,174358,178891,211352,218690,220312,440828];

        $campaign_ids = [409941,409936,410005,409938,409465,409934,409940,409937,409375];

        // Skipped due to not existing in Plexuss Database.
        $skip_ipeds_ids = [31155,31089,31227,31113,31200,31231,31234,31221,31163,140322];

        $pre_delivery_url = 'http://cappex.linktrustleadgen.com/Lead/';
        $post_delivery_url = '/SimplePost';

        $not_skipped = [];

        for ($i = 0; $i < count($ipeds_ids); $i++) {
            if (!in_array($ipeds_ids[$i], $skip_ipeds_ids)) {
                $not_skipped[] = $ipeds_ids[$i];

                $values = [];

                $college = College::where('ipeds_id', '=', $ipeds_ids[$i])->select('id', 'school_name')->first();

                $values['school_name'] = $college->school_name;
                $values['ro_id'] = 2;
                $values['college_id'] = $college->id;
                $values['delivery_url'] = $pre_delivery_url . $campaign_ids[$i] . $post_delivery_url;
                $values['delivery_type'] = 'GET';
                $values['response_type'] = 'TEXT';
                $values['success_tag'] = 'httpCode';
                $values['success_string'] = 'Lead was successfully submitted';
                $values['failed_tag'] = '<div class="greybox">BETWEEN</div>';

                // Disabled for safety, uncomment when ready to insert
                DistributionClient::updateOrCreate($values, $values);
            }
        }

        return 'success';
    }

    public function getCappexCampaignIds() {
        $dc_ids = DistributionClient::where('id', '>=', 266)->select('id', 'delivery_url')->get();

        $campaign_ids = [];

        foreach ($dc_ids as $dc_id) {
            $delivery_url = $dc_id->delivery_url;

            $replaced_once = str_replace('http://cappex.linktrustleadgen.com/Lead/', '', $delivery_url);

            $campaign_id = str_replace('/SimplePost', '', $replaced_once);

            $campaign_ids[] = $campaign_id;
        }

        dd($campaign_ids);
    }

    public function insertCappexClientFieldMappings() {
        $dcs = DistributionClient::where('id', '>=', 703)->where('ro_id', '=', 2)->select('id')->get();

        $fields = [
            ['client_field_name' => 'AFID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'CID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'email_address', 'plexuss_field_id' => 9, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'f_name', 'plexuss_field_id' => 1, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'l_name', 'plexuss_field_id' => 2, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'gender', 'plexuss_field_id' => 11, 'field_type' => 'dropdown', 'is_required' => 1],
            ['client_field_name' => 'b_year', 'plexuss_field_id' => 33, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'b_month', 'plexuss_field_id' => 34, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'b_date', 'plexuss_field_id' => 35, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'address', 'plexuss_field_id' => 6, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'zip_code', 'plexuss_field_id' => 8, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'city_name', 'plexuss_field_id' => 7, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'state_name', 'plexuss_field_id' => 14, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'country_id', 'plexuss_field_id' => 13, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'hs_grad_month', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'hs_grad_year', 'plexuss_field_id' => 27, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'nonweighted_hs_gpa', 'plexuss_field_id' => 20, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'college_considering', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'studentType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'phone_number', 'plexuss_field_id' => 36, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            ['client_field_name' => 'Test', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        ];

        $campaign_ids = [];

        foreach ($dcs as $dc) {
            $dc_id = $dc->id;
            foreach ($fields as $field) {
                $values = [];
                $values['dc_id'] = $dc_id;
                $values['client_field_name'] = $field['client_field_name'];
                $values['plexuss_field_id'] = $field['plexuss_field_id'];
                $values['field_type'] = $field['field_type'];
                $values['is_required'] = $field['is_required'];

                // Disabled for safety, uncomment to insert
                // DistributionClientFieldMapping::updateOrCreate($values, $values);
            }
        }

        dd('done');
    }

    public function insertCappexClientValueMappings() {
        $distribution_field_mappings = DistributionClientFieldMapping::select('*')->where('client_field_name', '=', 'Test')->where('dc_id', '>=', 703)->get();


        $fields = [
            ['is_default' => '1', 'plexuss_value' => null, 'client_value' => 'No'],
        ];

        foreach ($distribution_field_mappings as $mapping) {
            $dc = DistributionClient::select('college_id')->where('id', '=', $mapping->dc_id)->first();

            $college = College::select('ipeds_id')->where('id', '=', $dc->college_id)->first();

            foreach ($fields as $field) {
                $values = [
                    'is_default' => $field['is_default'],
                    'plexuss_value' => $field['plexuss_value'],
                    'client_value' => $field['client_value'],
                    'dc_id' => $mapping->dc_id,
                    'dcfm_id' => $mapping->id,
                ];

                // Disabled for safety, uncomment to insert
                // DistributionClientValueMapping::updateOrCreate($values, $values);
            }

        }

        dd('done');
    }

    public function insertCappexFilters() {
        $cappex_college_ids = [
            4198,
            2277,
            // 626,
            2580,
            3034,
            2958,
        ];

        foreach ($cappex_college_ids as $college_id) {
            $values = [
                'college_id' => $college_id,
                'aor_id' => 8,
                'type' => 'include',
                'category' => 'location',
                'name' => 'state',
            ];

            CollegeRecommendationFilters::updateOrCreate($values, $values);
        }
    }

    public function insertCappexFilterLogs() {
        $filter_ids = [
            5279,
            5280,
            5281,
            5282,
            5283,
            5284,
        ];

        $vals = [
            '2018,2019,2020',
            '2018,2019,2020',
            '2018,2019,2020',
            '2018,2019,2020',
            '2018,2019,2020',
            '2018,2019,2020',
        ];

        $temp = [];
        for ($i = 0; $i < count($filter_ids); $i++) {
            $values = [
                'rec_filter_id' => $filter_ids[$i],
                'val' => $vals[$i],
            ];

            $temp[] = $values;
            CollegeRecommendationFilterLogs::updateOrCreate($values, $values);

        }
    }

    public function insertCappexFilterLogsForStates() {
        $filter_ids = [
            5291,
            5292,
            5293,
            5294,
            5295,
        ];

        $states = [
            'AK,CA,HI,OR,WA',
            'CO,IA,KS,MO,NE,SD,WY',
            'CA,CT,FL,NJ,NY,PA,TX',
            'IL,IN,MI,OH',
            'CA,MN,MT,ND,SD,WY',
        ];


        for ($i = 0; $i < count($filter_ids); $i++) {
            $explode = explode(',', $states[$i]);

            foreach ($explode as $stateAbbr) {
                $stateModel = State::select('state_name')->where('state_abbr', $stateAbbr)->first();

                $values = [
                    "rec_filter_id" => $filter_ids[$i],
                    "val" => $stateModel->state_name,
                ];

                CollegeRecommendationFilterLogs::updateOrCreate($values, $values);
            }
        }

        dd('done');
    }

    public function insertGusClientFieldMappings(){
    	$dcs = DistributionClient::where('id', '=', 710)->select('id')->get();

        $fields = [
        	['client_field_name' => 'form[course]', 'plexuss_field_id' => 5, 'field_type' => 'dropdown', 'is_required' => 1],
        	['client_field_name' => 'form[course-location]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	['client_field_name' => 'form[course-start-date]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	['client_field_name' => 'form[course-funding-method]', 'plexuss_field_id' => 5, 'field_type' => 'dropdown', 'is_required' => 1],
        	['client_field_name' => 'form[firstName]', 'plexuss_field_id' => 1, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	['client_field_name' => 'form[lastName]', 'plexuss_field_id' => 2, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	['client_field_name' => 'form[email][email]', 'plexuss_field_id' => 9, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	['client_field_name' => 'form[telephone][countryCode]', 'plexuss_field_id' => 36, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	['client_field_name' => 'form[telephone][telephone]', 'plexuss_field_id' => 36, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	['client_field_name' => 'form[call-time]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	['client_field_name' => 'form[current-timezone]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'form[target-territory]', 'plexuss_field_id' => 40, 'field_type' => 'dropdown', 'is_required' => 0],
        	['client_field_name' => 'form[nationality]', 'plexuss_field_id' => 40, 'field_type' => 'dropdown', 'is_required' => 0],
        	['client_field_name' => 'form[education-level]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'form[english-proficiency]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'form[comments]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'form[school]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'form[programme]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'form[combined_comments]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'form[lead_id]', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'form[distribution_id', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],


            // ['client_field_name' => 'VendorId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'LocationId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'CurriculumID', 'plexuss_field_id' => 5, 'field_type' => 'dropdown', 'is_required' => 1],
            // ['client_field_name' => 'firstname', 'plexuss_field_id' => 1, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'lastname', 'plexuss_field_id' => 2, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'email', 'plexuss_field_id' => 9, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'dayphone', 'plexuss_field_id' => 36, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'about_us', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'TCPAconsentverbiage', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'required', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'FormID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'CampaignID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'AffiliateLocationID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'VendorID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'IsTest', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CaptureURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AffiliateTrackingCode', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ProspectIP', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CallCenterSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'JobSiteSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'SiteSourceURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'SubVendor', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'AddressLine1', 'plexuss_field_id' => 6, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'AddressLine2', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'City', 'plexuss_field_id' => 7, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'PostalCode', 'plexuss_field_id' => 8, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'Country', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'EveningPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'MobilePhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'EveningPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'Concentration', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadChannel', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'LeadType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'Keyword', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'DeviceType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ConversionWebSite', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ConversionURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CPI', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadIPAddress', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'VendorAccountCampaignType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowSMS', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CampaignSourceCode', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowEmail', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'EducationLevel', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'FormType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'SearchEngine', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ClientId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        ];

        $campaign_ids = [];

        foreach ($dcs as $dc) {
            $dc_id = $dc->id;
            foreach ($fields as $field) {
                $values = [];
                $values['dc_id'] = $dc_id;
                $values['client_field_name'] = $field['client_field_name'];
                $values['plexuss_field_id'] = $field['plexuss_field_id'];
                $values['field_type'] = $field['field_type'];
                $values['is_required'] = $field['is_required'];

                // Disabled for safety, uncomment to insert
                DistributionClientFieldMapping::updateOrCreate($values, $values);
            }
        }

        dd('done');
    }

    public function insertGusClientValueMappings() {
        $distribution_field_mappings = DistributionClientFieldMapping::where('dc_id', 710)
        															 ->where('client_field_name', 'form[course]')
                                                                     ->get();

        $fields = [
        	['is_default' => '0', 'plexuss_value' => '4,307', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,550', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1257', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1359', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1429', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1430', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1431', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1432', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1436', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1440', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1444', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1445', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1447', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1448', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1449', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1450', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1451', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,1454', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,307', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,550', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1257', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1359', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1429', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1430', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1431', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1432', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1436', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1440', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1444', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1445', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1447', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1448', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1449', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1450', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1451', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '5,1454', 'client_value_name' => 'MA Strategic Marketing', 'client_value' => 'MA Strategic Marketing',],
			['is_default' => '0', 'plexuss_value' => '4,455', 'client_value_name' => 'MSc Engineering Management', 'client_value' => 'MSc Engineering Management',],
			['is_default' => '0', 'plexuss_value' => '5,455', 'client_value_name' => 'MSc Engineering Management', 'client_value' => 'MSc Engineering Management',],
			['is_default' => '0', 'plexuss_value' => '4,204', 'client_value_name' => 'MSc Strategic IT Management', 'client_value' => 'MSc Strategic IT Management',],
			['is_default' => '0', 'plexuss_value' => '4,206', 'client_value_name' => 'MSc Strategic IT Management', 'client_value' => 'MSc Strategic IT Management',],
			['is_default' => '0', 'plexuss_value' => '4,207', 'client_value_name' => 'MSc Strategic IT Management', 'client_value' => 'MSc Strategic IT Management',],
			['is_default' => '0', 'plexuss_value' => '4,209', 'client_value_name' => 'MSc Strategic IT Management', 'client_value' => 'MSc Strategic IT Management',],
			['is_default' => '0', 'plexuss_value' => '5,204', 'client_value_name' => 'MSc Strategic IT Management', 'client_value' => 'MSc Strategic IT Management',],
			['is_default' => '0', 'plexuss_value' => '5,206', 'client_value_name' => 'MSc Strategic IT Management', 'client_value' => 'MSc Strategic IT Management',],
			['is_default' => '0', 'plexuss_value' => '5,207', 'client_value_name' => 'MSc Strategic IT Management', 'client_value' => 'MSc Strategic IT Management',],
			['is_default' => '0', 'plexuss_value' => '5,209', 'client_value_name' => 'MSc Strategic IT Management', 'client_value' => 'MSc Strategic IT Management',],
			['is_default' => '0', 'plexuss_value' => '4,1453', 'client_value_name' => 'MSc Telecommunications Management', 'client_value' => 'MSc Telecommunications Management',],
			['is_default' => '0', 'plexuss_value' => '5,1453', 'client_value_name' => 'MSc Telecommunications Management', 'client_value' => 'MSc Telecommunications Management',],
			['is_default' => '0', 'plexuss_value' => '4,3', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '4,91', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '4,93', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '4,94', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '4,95', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '4,101', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '4,375', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '5,3', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '5,91', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '5,93', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '5,94', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '5,95', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '5,101', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '5,375', 'client_value_name' => 'MSc Enterprise Architecture Management', 'client_value' => 'MSc Enterprise Architecture Management',],
			['is_default' => '0', 'plexuss_value' => '4,190', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '4,193', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '4,195', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '4,198', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '4,1385', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '5,190', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '5,193', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '5,195', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '5,198', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
			['is_default' => '0', 'plexuss_value' => '5,1385', 'client_value_name' => 'Big Data and Digital Ethics', 'client_value' => 'Big Data and Digital Ethics',],
        ];

        $response = [];

        foreach ($distribution_field_mappings as $mapping) {
            $dc = DistributionClient::select('college_id')->where('id', '=', $mapping->dc_id)->first();

            // $college = College::select('ipeds_id')->where('id', '=', $dc->college_id)->first();

            foreach ($fields as $field) {
                $values = [
                    'is_default' => $field['is_default'],
                    'plexuss_value' => $field['plexuss_value'],
                    'client_value' => $field['client_value'],
                    'client_value_name' => $field['client_value_name'],
                    'dc_id' => $mapping->dc_id,
                    'dcfm_id' => $mapping->id,
                ];

                $response[] = $values;

                // Disabled for safety, uncomment to insert
                DistributionClientValueMapping::updateOrCreate($values, $values);
            }

        }

        return 'finished';
    }

    public function insertKeypathClientFieldMappings() {
        $dcs = DistributionClient::where('id', '=', 699)->select('id')->get();

        $fields = [
            // ['client_field_name' => 'VendorId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'LocationId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'CurriculumID', 'plexuss_field_id' => 5, 'field_type' => 'dropdown', 'is_required' => 1],
            ['client_field_name' => 'firstname', 'plexuss_field_id' => 1, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'lastname', 'plexuss_field_id' => 2, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'email', 'plexuss_field_id' => 9, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'dayphone', 'plexuss_field_id' => 36, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            ['client_field_name' => 'about_us', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'TCPAconsentverbiage', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'required', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'FormID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'CampaignID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'AffiliateLocationID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'VendorID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            ['client_field_name' => 'IsTest', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'CaptureURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'AffiliateTrackingCode', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'ProspectIP', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'CallCenterSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'JobSiteSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'SiteSourceURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            ['client_field_name' => 'SubVendor', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'AddressLine1', 'plexuss_field_id' => 6, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'AddressLine2', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'City', 'plexuss_field_id' => 7, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'PostalCode', 'plexuss_field_id' => 8, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'Country', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'EveningPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'MobilePhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'EveningPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'Concentration', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadChannel', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'LeadType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'Keyword', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'DeviceType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ConversionWebSite', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ConversionURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CPI', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadIPAddress', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'VendorAccountCampaignType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowSMS', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CampaignSourceCode', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowEmail', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'EducationLevel', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'FormType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'SearchEngine', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ClientId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        ];

        $campaign_ids = [];

        foreach ($dcs as $dc) {
            $dc_id = $dc->id;
            foreach ($fields as $field) {
                $values = [];
                $values['dc_id'] = $dc_id;
                $values['client_field_name'] = $field['client_field_name'];
                $values['plexuss_field_id'] = $field['plexuss_field_id'];
                $values['field_type'] = $field['field_type'];
                $values['is_required'] = $field['is_required'];

                // Disabled for safety, uncomment to insert
                DistributionClientFieldMapping::updateOrCreate($values, $values);
            }
        }

        dd('done');
    }

    public function insertKeypathClientValueMappings() {
        $distribution_field_mappings = DistributionClientFieldMapping::select('*')->where('client_field_name', '=', 'EducationLevel')->where('field_type', '=', 'dropdown')->get();

        $fields = [
            ['is_default' => '0', 'plexuss_value' => '0', 'client_value' => 'High School / GED',],
            ['is_default' => '0', 'plexuss_value' => '1', 'client_value' => 'Bachelors Degree',],
        ];

        $response = [];

        foreach ($distribution_field_mappings as $mapping) {
            $dc = DistributionClient::select('college_id')->where('id', '=', $mapping->dc_id)->first();

            // $college = College::select('ipeds_id')->where('id', '=', $dc->college_id)->first();

            foreach ($fields as $field) {
                $values = [
                    'is_default' => $field['is_default'],
                    'plexuss_value' => $field['plexuss_value'],
                    'client_value' => $field['client_value'],
                    'dc_id' => $mapping->dc_id,
                    'dcfm_id' => $mapping->id,
                ];

                $response[] = $values;

                // Disabled for safety, uncomment to insert
                DistributionClientValueMapping::updateOrCreate($values, $values);
            }

        }

        return 'finished';
    }

    public function insertKeypathMissingClientFieldMappings() {
        // For US schools, insert educationlevel
        // For UK/AU schools, insert EducationLevel
        $uk_dc_ids = [693, 695, 697, 700];
        $us_dc_ids = [696, 698, 699];

        $fields = [
            // ['client_field_name' => 'educationlevel', 'plexuss_field_id' => 10, 'field_type' => 'dropdown', 'is_required' => 1], // US
            ['client_field_name' => 'EducationLevel', 'plexuss_field_id' => 10, 'field_type' => 'dropdown', 'is_required' => 1], // UK
        ];

        foreach ($uk_dc_ids as $dc_id) {
            foreach ($fields as $field) {
                $values = [];
                $values['dc_id'] = $dc_id;
                $values['client_field_name'] = $field['client_field_name'];
                $values['plexuss_field_id'] = $field['plexuss_field_id'];
                $values['field_type'] = $field['field_type'];
                $values['is_required'] = $field['is_required'];

                // Disabled for safety, uncomment to insert
                DistributionClientFieldMapping::updateOrCreate($values, $values);
            }
        }

        dd('done');
    }


    public function insertZetaClientFieldMappings(){
    	$dcs = DistributionClient::where('id', '=', 702)->select('id')->get();

        $fields = [
        	// ['client_field_name' => 'first_name', 'plexuss_field_id' => 1, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'last_name', 'plexuss_field_id' => 2, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'address_1', 'plexuss_field_id' => 6, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'address_2', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	// ['client_field_name' => 'city', 'plexuss_field_id' => 7, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'state', 'plexuss_field_id' => 14, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'zip', 'plexuss_field_id' => 8, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'email', 'plexuss_field_id' => 9, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'home_phone', 'plexuss_field_id' => 36, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'work_phone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	// ['client_field_name' => 'cell_phone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	// ['client_field_name' => 'dob', 'plexuss_field_id' => 33, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'gender', 'plexuss_field_id' => 11, 'field_type' => 'dropdown', 'is_required' => 1],
        	// ['client_field_name' => 'already_enrolled', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'us_citizen', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'military_affiliation', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'dog', 'plexuss_field_id' => 27, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
        	// ['client_field_name' => 'education', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'program', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'aos_category', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'aos_subject', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'start_timeframe', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'affiliate_id', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	// ['client_field_name' => 'rep', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	// ['client_field_name' => 'ip_address', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'lead_id', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	// ['client_field_name' => 'source', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],

        	// ['client_field_name' => 'authentication_key', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'campaign_id', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	['client_field_name' => 'current_major', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'vendorsource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
        	['client_field_name' => 'school', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'clientid', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        	// ['client_field_name' => 'clientpass', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],

            // ['client_field_name' => 'VendorId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'LocationId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'CurriculumID', 'plexuss_field_id' => 5, 'field_type' => 'dropdown', 'is_required' => 1],


            // ['client_field_name' => 'email', 'plexuss_field_id' => 9, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'dayphone', 'plexuss_field_id' => 36, 'field_type' => 'plexuss_field_value', 'is_required' => 1],
            // ['client_field_name' => 'about_us', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'TCPAconsentverbiage', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'required', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'FormID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'CampaignID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'AffiliateLocationID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'VendorID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'IsTest', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CaptureURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AffiliateTrackingCode', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ProspectIP', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CallCenterSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'JobSiteSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'SiteSourceURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'SubVendor', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'AddressLine1', 'plexuss_field_id' => 6, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'AddressLine2', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'City', 'plexuss_field_id' => 7, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'PostalCode', 'plexuss_field_id' => 8, 'field_type' => 'plexuss_field_value', 'is_required' => 0],
            // ['client_field_name' => 'Country', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'EveningPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'MobilePhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'EveningPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],

            // ['client_field_name' => 'Concentration', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadChannel', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
            // ['client_field_name' => 'LeadType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadSource', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'Keyword', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'DeviceType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ConversionWebSite', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ConversionURL', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CPI', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'LeadIPAddress', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'VendorAccountCampaignType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowSMS', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CampaignSourceCode', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowEmail', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'AllowPhone', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'EducationLevel', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'CID', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'FormType', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'SearchEngine', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 0],
            // ['client_field_name' => 'ClientId', 'plexuss_field_id' => null, 'field_type' => 'static', 'is_required' => 1],
        ];

        $campaign_ids = [];

        foreach ($dcs as $dc) {
            $dc_id = $dc->id;
            foreach ($fields as $field) {
                $values = [];
                $values['dc_id'] = $dc_id;
                $values['client_field_name'] = $field['client_field_name'];
                $values['plexuss_field_id'] = $field['plexuss_field_id'];
                $values['field_type'] = $field['field_type'];
                $values['is_required'] = $field['is_required'];

                // Disabled for safety, uncomment to insert
                DistributionClientFieldMapping::updateOrCreate($values, $values);
            }
        }

        dd('done');
    }

    public function insertZetaClientValueMappings() {
        $distribution_field_mappings = DistributionClientFieldMapping::where('dc_id', 702)
        															 ->where('client_field_name', 'military_affiliation')
                                                                     ->get();

     //    $fields = [
     //    			['is_default' => '0', 'plexuss_value' => '736', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1376', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1377', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1379', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1380', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1381', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '570', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1382', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1378', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1377', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1362', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1375', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1395', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1361', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1369', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1420', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1467', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '166', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '402', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1368', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '152', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '153', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1369', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '11', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '74', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '305', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '528', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '530', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '935', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '936', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '937', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '938', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '939', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '940', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1253', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1392', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '757', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '759', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '561', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1379', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1397', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1400', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1402', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1404', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1399', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '171', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '179', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1414', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1418', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1419', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '566', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1420', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '885', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '9', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '12', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '15', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '25', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '37', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '39', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '40', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '41', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '52', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '62', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '63', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '73', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '75', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '76', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '79', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '80', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '83', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '86', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '89', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '156', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '172', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '204', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '206', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '207', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '209', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '225', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '233', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '236', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '412', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '414', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '416', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '455', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '525', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '527', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '533', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '536', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '543', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '550', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '731', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '757', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '758', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '759', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '761', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '888', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '891', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '906', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '972', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '976', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1043', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1117', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1118', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1119', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1120', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1121', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1149', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1150', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1153', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1159', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1166', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1257', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1359', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1362', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1363', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1364', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1365', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1366', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1367', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1368', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1370', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1371', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1372', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1373', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1375', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1380', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1387', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1395', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1403', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1404', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1405', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1406', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1407', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1408', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1409', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1411', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1412', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1413', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1414', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1419', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1421', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1422', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1423', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1424', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1425', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1428', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1429', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1452', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1453', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1454', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1468', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '307', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '550', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1257', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1359', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1429', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1430', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1431', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1432', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1436', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1440', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1444', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1445', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1447', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1448', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1449', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1450', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1451', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1454', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '10', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '20', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '28', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '35', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '37', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '39', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '41', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '68', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '307', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '386', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '705', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '706', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '708', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '709', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '712', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '715', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '895', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '896', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '908', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1001', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1043', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1366', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1375', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1387', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1390', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1394', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1396', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1436', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1437', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1438', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1439', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1440', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1444', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1445', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1446', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1447', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1448', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1449', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1450', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1451', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '911', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '914', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '923', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '154', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '156', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '163', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1412', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1373', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1395', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1396', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '567', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1434', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1393', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '7', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '181', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '185', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '205', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '210', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1452', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1364', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '753', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1367', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '207', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1372', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1368', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '1468', 'client_value' => 1,],
					// ['is_default' => '0', 'plexuss_value' => '880', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '886', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '889', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '897', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '881', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '882', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '884', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '897', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '900', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '883', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '879', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '905', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '909', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '910', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '78', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '879', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '881', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '890', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '891', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '894', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '910', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '571', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '556', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '915', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '919', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '911', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '914', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '923', 'client_value' => 2,],
					// ['is_default' => '0', 'plexuss_value' => '947', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '950', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '199', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '1072', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '1300', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '1302', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '1068', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '1086', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '1071', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '174', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '1070', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '170', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '1065', 'client_value' => 3,],
					// ['is_default' => '0', 'plexuss_value' => '296', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '44', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '242', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '243', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '244', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '245', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '246', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '247', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '249', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '250', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '251', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '252', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '253', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '254', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '257', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '258', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '259', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '260', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '261', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '262', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '263', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '265', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '266', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '267', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '268', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '269', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '270', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '271', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '272', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '273', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '274', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '275', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '276', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '277', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '278', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '279', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '280', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '281', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '282', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '283', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '284', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '285', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '288', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '289', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '290', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '291', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '292', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '293', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '294', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '295', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '296', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '297', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '298', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '299', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '300', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '301', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '302', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '303', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '304', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '305', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '306', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '307', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '308', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '309', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '310', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '311', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '312', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '313', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '314', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '315', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '316', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '317', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '318', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '319', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '320', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '321', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '322', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '323', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '324', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '325', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '326', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '327', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '328', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '329', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '331', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '332', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '333', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '334', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '341', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '760', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '765', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '766', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '785', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '868', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '916', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '1136', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '1264', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '1323', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '1326', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '1348', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '1469', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '251', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '253', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '292', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '293', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '294', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '297', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '298', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '299', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '300', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '301', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '302', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '303', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '304', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '305', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '306', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '307', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '308', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '309', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '311', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '312', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '313', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '314', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '315', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '316', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '317', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '318', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '319', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '320', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '321', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '322', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '323', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '324', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '325', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '326', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '327', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '328', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '329', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '331', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '332', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '334', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '338', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '1324', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '338', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '541', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '289', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '310', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '760', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '765', 'client_value' => 4,],
					// ['is_default' => '0', 'plexuss_value' => '1310', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1124', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1183', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1204', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1327', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1233', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1145', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '729', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1296', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1183', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1170', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '405', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1187', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1188', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1209', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1156', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1224', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1274', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1173', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1226', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1276', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1136', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1260', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1264', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1267', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1270', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1292', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1342', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1278', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1280', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1282', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1283', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1298', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1191', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1154', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1155', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1192', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1213', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '1356', 'client_value' => 5,],
					// ['is_default' => '0', 'plexuss_value' => '359', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '362', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '442', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '446', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '186', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '187', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '188', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '189', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '196', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '727', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '736', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '198', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '183', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '207', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '209', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '202', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '204', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '361', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '533', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '202', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '365', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '402', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '1453', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '892', 'client_value' => 7,],
					// ['is_default' => '0', 'plexuss_value' => '5', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '45', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '147', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '148', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '149', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '150', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '165', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '151', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '168', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '144', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1079', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '169', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '182', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '219', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '228', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '229', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '230', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '231', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '232', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '240', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '295', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '299', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '302', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '306', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '332', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '588', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '589', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '592', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '821', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '827', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '830', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '942', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1001', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1048', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1058', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1059', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1060', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1061', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1066', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1074', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1075', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1083', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1088', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1089', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1090', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1091', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1097', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1098', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1099', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1100', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1117', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1118', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1120', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1121', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1122', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1271', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1387', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1447', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '584', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '132', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1443', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '167', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1084', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1085', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1088', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '328', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '941', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '943', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '96', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '324', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1078', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1091', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1102', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1360', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1455', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1456', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1457', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1458', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1459', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1460', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1461', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1462', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '588', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '589', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '592', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '461', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '463', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '466', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '468', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '469', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '473', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '474', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '479', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '480', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '482', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '487', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '489', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '490', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '491', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '492', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '498', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '499', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '500', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '501', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '504', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '505', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '506', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '509', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '510', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '514', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '516', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '519', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '853', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '309', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '786', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '999', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1081', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1101', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1102', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1103', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1104', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1105', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1111', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1112', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1116', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1119', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1273', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '947', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '950', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '169', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '768', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '769', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '775', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '780', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '781', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '951', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '953', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '954', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '137', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '577', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '578', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '579', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '581', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1077', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '5', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '6', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '45', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '143', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '144', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '145', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '146', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '152', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '153', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '154', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '155', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '158', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '159', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '160', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '161', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '162', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '163', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '165', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '166', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '170', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '171', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '179', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '180', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '202', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '365', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '366', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '400', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '402', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '524', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '987', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1065', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1127', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1131', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1391', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1453', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1466', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '302', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '335', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '337', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '575', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '576', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '584', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '587', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '70', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '77', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '755', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '756', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '757', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '759', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '767', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1277', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1450', 'client_value' => 8,],
					// ['is_default' => '0', 'plexuss_value' => '1348', 'client_value' => 9,],
					// ['is_default' => '0', 'plexuss_value' => '793', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '798', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '1235', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '788', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '794', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '795', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '796', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '797', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '794', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '787', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '783', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '1105', 'client_value' => 10,],
					// ['is_default' => '0', 'plexuss_value' => '3', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '91', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '93', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '94', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '95', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '101', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '375', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '348', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '353', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '358', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '399', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '450', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '382', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '438', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '354', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '415', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '384', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '371', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '372', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '390', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '406', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '433', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '434', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '427', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '430', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '379', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '394', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '710', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '82', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '318', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '599', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '603', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '606', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '607', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '608', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '609', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '610', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '613', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '614', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '615', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '617', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '618', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '619', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '620', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '621', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '622', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '626', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '628', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '632', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '633', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '645', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '650', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '663', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '664', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '667', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '668', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '669', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '670', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '671', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '672', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '673', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '675', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '676', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '680', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '681', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '682', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '693', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '747', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '843', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '858', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '1135', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '1287', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '1288', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '66', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '319', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '393', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '601', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '604', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '608', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '609', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '810', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '811', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '812', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '813', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '814', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '815', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '817', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '818', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '819', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '820', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '822', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '838', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '1250', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '1251', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '762', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '308', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '664', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '684', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '688', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '689', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '690', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '691', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '692', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '694', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '697', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '699', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '700', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '727', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '900', 'client_value' => 11,],
					// ['is_default' => '0', 'plexuss_value' => '912', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '855', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '869', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '863', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '285', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '286', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '287', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '793', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '798', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '865', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '877', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1230', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1233', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1234', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1235', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1237', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1238', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1278', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1471', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '874', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1234', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '866', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '331', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '728', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '851', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '852', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '853', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '854', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '855', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '856', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '857', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '858', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '859', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '860', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '862', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '863', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '864', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '865', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '866', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '867', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '868', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '869', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '870', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '871', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '872', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '873', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '874', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '875', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '877', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '878', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '737', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '951', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '953', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '954', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '927', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '928', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '929', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '930', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '931', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '953', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '313', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '924', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '925', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '955', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '310', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1374', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '725', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '370', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1231', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1237', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1261', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1341', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '1346', 'client_value' => 12,],
					// ['is_default' => '0', 'plexuss_value' => '100', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1433', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1382', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1390', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '572', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '222', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '227', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1124', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1183', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1204', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '230', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '217', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '959', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '382', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '438', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '956', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '957', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '966', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '972', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '975', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '976', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '982', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1048', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1452', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '216', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '226', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '228', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '229', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '232', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '240', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '193', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '195', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1385', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1145', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '961', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '222', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '227', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1443', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1035', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '997', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '38', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '998', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1319', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '18', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '367', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '984', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '995', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1005', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1006', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1007', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1008', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1183', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '212', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '214', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '215', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '223', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '978', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '981', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1412', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '225', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '222', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '422', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1029', 'client_value' => 13,],
					// ['is_default' => '0', 'plexuss_value' => '1178', 'client_value' => 13,],
     //    ];

        $fields = [
            ['is_default' => '1', 'plexuss_value' => NULL, 'client_value' => 'N',],
        ];

        $response = [];

        foreach ($distribution_field_mappings as $mapping) {
            $dc = DistributionClient::select('college_id')->where('id', '=', $mapping->dc_id)->first();

            // $college = College::select('ipeds_id')->where('id', '=', $dc->college_id)->first();

            foreach ($fields as $field) {
                $values = [
                    'is_default' => $field['is_default'],
                    'plexuss_value' => $field['plexuss_value'],
                    'client_value' => $field['client_value'],
                    'dc_id' => $mapping->dc_id,
                    'dcfm_id' => $mapping->id,
                ];

                $response[] = $values;

                // Disabled for safety, uncomment to insert
                DistributionClientValueMapping::updateOrCreate($values, $values);
            }

        }

        return 'finished';
    }

    public function zetaProgramGenerator(){

    	$arr = array("Accounting",
					"Administrative Assistant",
					"Auditing",
					"Bookkeeping",
					"Business Administration",
					"Business Communications",
					"Commerce",
					"Communications Technology",
					"Customer Service Management",
					"Digital Communication",
					"E-Commerce",
					"Economics",
					"Entertainment Management",
					"Facilities Management",
					"Fashion Marketing",
					"Finance",
					"Financial Planning",
					"Graphic Communications",
					"Hospitality Management",
					"Human Resources",
					"International Business",
					"Loss Prevention",
					"Management",
					"Management Info Systems",
					"Marketing",
					"Non-Profit Management",
					"Operations",
					"Organizational Communications",
					"Property Management",
					"Public Administration",
					"Public Relations",
					"Purchasing and Acquisitions",
					"Restaurant Management",
					"Retail Management",
					"Small Business",
					"Taxation",
					"Tourism Management",
					"Health Administration",
					"Information Systems Management",
					"Health Services Management",
					"Entrepreneurship",
					"Environmental Management",
					"Computer and Information",
					"Healthcare Management",
					"Construction Management",
					"Management and Leadership",
					"Supply Chain",
					"Sustainability",
					"Organizational Management",
					"Culinary Arts Management",
					"Risk Management",
					"Project Management",
					"Service Management",
					"Sports Management",
					"Human Services Administration",
					"Global Business",
					"Corrections",
					"Criminal Justice",
					"Fire Science",
					"Forensic Science",
					"Homeland Security",
					"Law Enforcement",
					"Law School",
					"Legal Aide",
					"Paralegal",
					"Pre-Law Studies",
					"Security and Protective Services",
					"Crime Scene Investigation",
					"Cyber Crime",
					"Juvenile Justice",
					"Non Profit",
					"Public Policy",
					"Public Administration",
					"Political Science",
					"Public Safety",
					"Audio Visual Communications",
					"Computer Graphics",
					"Web Design",
					"Fashion Design",
					"Illustration",
					"Industrial Design",
					"Photography",
					"Graphic Design",
					"Animation",
					"Interior Design",
					"Game Design",
					"3-D Animation",
					"Home Design",
					"Visual Communications",
					"Kitchen and Bath Design",
					"Building Information Modeling",
					"Web Design & Interactive Media",
					"Game Software Development",
					"Adult Education Administration",
					"Adult Education Teacher",
					"Early Childhood Education",
					"Education",
					"Education Administration",
					"Elementary School Teacher",
					"ESL Teacher",
					"Instructional Design",
					"Paraprofessional",
					"Resource Specialist",
					"Secondary Education Teacher",
					"Special Education Teacher",
					"Teacher",
					"Teacher Assistant",
					"Child Development",
					"English Language Learner",
					"Elementary Education",
					"Foreign Language Studies",
					"Child Study",
					"Physical Education",
					"Assessment & Measurement",
					"Curriculum & Instruction",
					"Distance Learning",
					"Teaching with Technology",
					"Acupuncture",
					"Allied Health",
					"Alternative Medicine",
					"Aromatherapy",
					"Asian Medicine",
					"Athletic and Personal Trainer",
					"Community Health Services",
					"Dental Assisting",
					"Gerontology",
					"Health Informatics",
					"Home Health Aide",
					"Medical Assisting",
					"Medical Billing and Coding",
					"Medical Laboratory Assistant",
					"Medical Office Administration",
					"Medical Technology",
					"Medical Transcription",
					"Nutritional Science",
					"Occupational Therapy",
					"Pharmacy Assisting",
					"Physical Therapy",
					"Public Health",
					"Rehabilitation",
					"Respiratory Care Therapy",
					"Sports Medicine",
					"Ultrasound and Sonography",
					"Veterinary Assisting",
					"Medical Records",
					"Health Studies",
					"Speech and Language Therapy",
					"Surgical Technology",
					"Phlebotomy",
					"Patient Care",
					"College Preparatory",
					"General High School Diploma",
					"Computer Engineering",
					"Computer Programming",
					"Computer Science",
					"Database Administration",
					"Information Technology",
					"IT Support Services",
					"Networking",
					"Software Engineering",
					"Systems Administration",
					"Telecommunications",
					"Web Development",
					"Information Systems Management",
					"Network Administration",
					"Web Administration",
					"Information Security",
					"Cisco Network Systems",
					"Computer Forensics",
					"Journalism",
					"Radio and Television",
					"Speech Communication",
					"Acting",
					"Art",
					"Drafting and CAD",
					"English Literature",
					"Ethnic Studies",
					"Fashion Modeling",
					"Film",
					"Geography",
					"History",
					"Liberal Arts",
					"Linguistics",
					"Music",
					"Political Science",
					"Recording Art",
					"Religious Studies",
					"Second Language Learning",
					"Sociology",
					"Women's Studies",
					"Writing",
					"Residential Planning",
					"Meditation",
					"Communication",
					"English",
					"Environmental Studies",
					"Recreation",
					"RN to BSN",
					"MSN",
					"Nursing Informatics",
					"Nursing Leadership",
					"Family Nurse Practitioner",
					"Nursing Education",
					"Nurse Administration",
					"Christen Studies",
					"Pastoral",
					"Ministry",
					"Youth Ministry",
					"Ministry Leadership",
					"Apologetics",
					"Theological Studies",
					"Biblical Studies",
					"Chaplaincy",
					"Ethnomusicology",
					"Evangelism",
					"Worship",
					"Discipleship",
					"Aeronautical Engineering",
					"Architecture",
					"Biomedical Engineering",
					"Civil Engineering",
					"Construction Engineering",
					"Drafting and CAD",
					"Electrical Engineering",
					"Environmental Engineering",
					"Industrial Engineering",
					"Materials Engineering",
					"Mechanical Engineering",
					"Quality Control",
					"Systems Engineering",
					"Wind Energy Technology",
					"Biology",
					"Chemistry",
					"Exercise Science",
					"Mathematics",
					"Engineering Studies",
					"Fire Science",
					"Human Services",
					"Child Psychology",
					"Clinical Psychology",
					"Counseling",
					"Forensic Psychology",
					"Marriage and Family Therapy",
					"Organizational Psychology",
					"Psychology",
					"Behavioral Science",
					"Health and Human Services",
					"Social Services",
					"Sociology",
					"Anthropology",
					"Social Science",
					"Coaching",
					"Organizational Leadership",
					"Health Psychology",
					"Conflict Resolution",
					"Mental Health",
					"Research and Measurement",
					"Sport Psychology",
					"Real Estate",
					"Secretarial",
					"Court Reporting",
					"Jewelry Design",
					"Aesthetician",
					"Aircraft Technology",
					"Allied Health",
					"Animal Training and Grooming",
					"Appliance Technician",
					"Athletic and Personal Trainer",
					"Automotive Technician",
					"Baking and Pastry",
					"Barbering",
					"Carpentry",
					"Child Care Training",
					"Construction",
					"Cosmetology",
					"Culinary Arts",
					"Culinary Arts Management",
					"Data Entry",
					"Dental Assisting",
					"Diesel Technician",
					"Electrician",
					"Esthetician",
					"Fashion Modeling",
					"Furniture Design",
					"Gunsmithing",
					"Hair Stylist",
					"Home Inspection",
					"HVAC",
					"Landscaping",
					"Laser Treatment",
					"Locksmithing",
					"Marine Technician",
					"Massage Therapy",
					"Mechanics",
					"Medical Assisting",
					"Mortuary Science",
					"Motorcycle Technician",
					"Nail Technician",
					"Plumbing",
					"Property Management",
					"Restaurant Management",
					"Salon Management",
					"Skin Care",
					"Truck Driving",
					"Veterinary Assisting",
					"Welding",
					"Respiratory Therapy",
					"Diagnostic Ultrasound Technology",
					"Lymph Drainage",
					"Personal Fitness Trainer",
					"Orthopedic Massage");
		$tmp = array("Accounting" => 1,
					"Administrative Assistant" => 1,
					"Auditing" => 1,
					"Bookkeeping" => 1,
					"Business Administration" => 1,
					"Business Communications" => 1,
					"Commerce" => 1,
					"Communications Technology" => 1,
					"Customer Service Management" => 1,
					"Digital Communication" => 1,
					"E-Commerce" => 1,
					"Economics" => 1,
					"Entertainment Management" => 1,
					"Facilities Management" => 1,
					"Fashion Marketing" => 1,
					"Finance" => 1,
					"Financial Planning" => 1,
					"Graphic Communications" => 1,
					"Hospitality Management" => 1,
					"Human Resources" => 1,
					"International Business" => 1,
					"Loss Prevention" => 1,
					"Management" => 1,
					"Management Info Systems" => 1,
					"Marketing" => 1,
					"Non-Profit Management" => 1,
					"Operations" => 1,
					"Organizational Communications" => 1,
					"Property Management" => 1,
					"Public Administration" => 1,
					"Public Relations" => 1,
					"Purchasing and Acquisitions" => 1,
					"Restaurant Management" => 1,
					"Retail Management" => 1,
					"Small Business" => 1,
					"Taxation" => 1,
					"Tourism Management" => 1,
					"Health Administration" => 1,
					"Information Systems Management" => 1,
					"Health Services Management" => 1,
					"Entrepreneurship" => 1,
					"Environmental Management" => 1,
					"Computer and Information" => 1,
					"Healthcare Management" => 1,
					"Construction Management" => 1,
					"Management and Leadership" => 1,
					"Supply Chain" => 1,
					"Sustainability" => 1,
					"Organizational Management" => 1,
					"Culinary Arts Management" => 1,
					"Risk Management" => 1,
					"Project Management" => 1,
					"Service Management" => 1,
					"Sports Management" => 1,
					"Human Services Administration" => 1,
					"Global Business" => 1,
					"Corrections" => 2,
					"Criminal Justice" => 2,
					"Fire Science" => 2,
					"Forensic Science" => 2,
					"Homeland Security" => 2,
					"Law Enforcement" => 2,
					"Law School" => 2,
					"Legal Aide" => 2,
					"Paralegal" => 2,
					"Pre-Law Studies" => 2,
					"Security and Protective Services" => 2,
					"Crime Scene Investigation" => 2,
					"Cyber Crime" => 2,
					"Juvenile Justice" => 2,
					"Non Profit" => 2,
					"Public Policy" => 2,
					"Public Administration" => 2,
					"Political Science" => 2,
					"Public Safety" => 2,
					"Audio Visual Communications" => 3,
					"Computer Graphics" => 3,
					"Web Design" => 3,
					"Fashion Design" => 3,
					"Illustration" => 3,
					"Industrial Design" => 3,
					"Photography" => 3,
					"Graphic Design" => 3,
					"Animation" => 3,
					"Interior Design" => 3,
					"Game Design" => 3,
					"3-D Animation" => 3,
					"Home Design" => 3,
					"Visual Communications" => 3,
					"Kitchen and Bath Design" => 3,
					"Building Information Modeling" => 3,
					"Web Design & Interactive Media" => 3,
					"Game Software Development" => 3,
					"Adult Education Administration" => 4,
					"Adult Education Teacher" => 4,
					"Early Childhood Education" => 4,
					"Education" => 4,
					"Education Administration" => 4,
					"Elementary School Teacher" => 4,
					"ESL Teacher" => 4,
					"Instructional Design" => 4,
					"Paraprofessional" => 4,
					"Resource Specialist" => 4,
					"Secondary Education Teacher" => 4,
					"Special Education Teacher" => 4,
					"Teacher" => 4,
					"Teacher Assistant" => 4,
					"Child Development" => 4,
					"English Language Learner" => 4,
					"Elementary Education" => 4,
					"Foreign Language Studies" => 4,
					"Child Study" => 4,
					"Physical Education" => 4,
					"Assessment & Measurement" => 4,
					"Curriculum & Instruction" => 4,
					"Distance Learning" => 4,
					"Teaching with Technology" => 4,
					"Acupuncture" => 5,
					"Allied Health" => 5,
					"Alternative Medicine" => 5,
					"Aromatherapy" => 5,
					"Asian Medicine" => 5,
					"Athletic and Personal Trainer" => 5,
					"Community Health Services" => 5,
					"Dental Assisting" => 5,
					"Gerontology" => 5,
					"Health Informatics" => 5,
					"Home Health Aide" => 5,
					"Medical Assisting" => 5,
					"Medical Billing and Coding" => 5,
					"Medical Laboratory Assistant" => 5,
					"Medical Office Administration" => 5,
					"Medical Technology" => 5,
					"Medical Transcription" => 5,
					"Nutritional Science" => 5,
					"Occupational Therapy" => 5,
					"Pharmacy Assisting" => 5,
					"Physical Therapy" => 5,
					"Public Health" => 5,
					"Rehabilitation" => 5,
					"Respiratory Care Therapy" => 5,
					"Sports Medicine" => 5,
					"Ultrasound and Sonography" => 5,
					"Veterinary Assisting" => 5,
					"Medical Records" => 5,
					"Health Studies" => 5,
					"Speech and Language Therapy" => 5,
					"Surgical Technology" => 5,
					"Phlebotomy" => 5,
					"Patient Care" => 5,
					"College Preparatory" => 6,
					"General High School Diploma" => 6,
					"Computer Engineering" => 7,
					"Computer Programming" => 7,
					"Computer Science" => 7,
					"Database Administration" => 7,
					"Information Technology" => 7,
					"IT Support Services" => 7,
					"Networking" => 7,
					"Software Engineering" => 7,
					"Systems Administration" => 7,
					"Telecommunications" => 7,
					"Web Development" => 7,
					"Information Systems Management" => 7,
					"Network Administration" => 7,
					"Web Administration" => 7,
					"Information Security" => 7,
					"Cisco Network Systems" => 7,
					"Computer Forensics" => 7,
					"Journalism" => 8,
					"Radio and Television" => 8,
					"Speech Communication" => 8,
					"Acting" => 8,
					"Art" => 8,
					"Drafting and CAD" => 8,
					"English Literature" => 8,
					"Ethnic Studies" => 8,
					"Fashion Modeling" => 8,
					"Film" => 8,
					"Geography" => 8,
					"History" => 8,
					"Liberal Arts" => 8,
					"Linguistics" => 8,
					"Music" => 8,
					"Political Science" => 8,
					"Recording Art" => 8,
					"Religious Studies" => 8,
					"Second Language Learning" => 8,
					"Sociology" => 8,
					"Women's Studies" => 8,
					"Writing" => 8,
					"Residential Planning" => 8,
					"Meditation" => 8,
					"Communication" => 8,
					"English" => 8,
					"Environmental Studies" => 8,
					"Recreation" => 8,
					"RN to BSN" => 9,
					"MSN" => 9,
					"Nursing Informatics" => 9,
					"Nursing Leadership" => 9,
					"Family Nurse Practitioner" => 9,
					"Nursing Education" => 9,
					"Nurse Administration" => 9,
					"Christen Studies" => 10,
					"Pastoral" => 10,
					"Ministry" => 10,
					"Youth Ministry" => 10,
					"Ministry Leadership" => 10,
					"Apologetics" => 10,
					"Theological Studies" => 10,
					"Biblical Studies" => 10,
					"Chaplaincy" => 10,
					"Ethnomusicology" => 10,
					"Evangelism" => 10,
					"Worship" => 10,
					"Discipleship" => 10,
					"Aeronautical Engineering" => 11,
					"Architecture" => 11,
					"Biomedical Engineering" => 11,
					"Civil Engineering" => 11,
					"Construction Engineering" => 11,
					"Drafting and CAD" => 11,
					"Electrical Engineering" => 11,
					"Environmental Engineering" => 11,
					"Industrial Engineering" => 11,
					"Materials Engineering" => 11,
					"Mechanical Engineering" => 11,
					"Quality Control" => 11,
					"Systems Engineering" => 11,
					"Wind Energy Technology" => 11,
					"Biology" => 11,
					"Chemistry" => 11,
					"Exercise Science" => 11,
					"Mathematics" => 11,
					"Engineering Studies" => 11,
					"Fire Science" => 11,
					"Human Services" => 12,
					"Child Psychology" => 12,
					"Clinical Psychology" => 12,
					"Counseling" => 12,
					"Forensic Psychology" => 12,
					"Marriage and Family Therapy" => 12,
					"Organizational Psychology" => 12,
					"Psychology" => 12,
					"Behavioral Science" => 12,
					"Health and Human Services" => 12,
					"Social Services" => 12,
					"Sociology" => 12,
					"Anthropology" => 12,
					"Social Science" => 12,
					"Coaching" => 12,
					"Organizational Leadership" => 12,
					"Health Psychology" => 12,
					"Conflict Resolution" => 12,
					"Mental Health" => 12,
					"Research and Measurement" => 12,
					"Sport Psychology" => 12,
					"Real Estate" => 13,
					"Secretarial" => 13,
					"Court Reporting" => 13,
					"Jewelry Design" => 13,
					"Aesthetician" => 13,
					"Aircraft Technology" => 13,
					"Allied Health" => 13,
					"Animal Training and Grooming" => 13,
					"Appliance Technician" => 13,
					"Athletic and Personal Trainer" => 13,
					"Automotive Technician" => 13,
					"Baking and Pastry" => 13,
					"Barbering" => 13,
					"Carpentry" => 13,
					"Child Care Training" => 13,
					"Construction" => 13,
					"Cosmetology" => 13,
					"Culinary Arts" => 13,
					"Culinary Arts Management" => 13,
					"Data Entry" => 13,
					"Dental Assisting" => 13,
					"Diesel Technician" => 13,
					"Electrician" => 13,
					"Esthetician" => 13,
					"Fashion Modeling" => 13,
					"Furniture Design" => 13,
					"Gunsmithing" => 13,
					"Hair Stylist" => 13,
					"Home Inspection" => 13,
					"HVAC" => 13,
					"Landscaping" => 13,
					"Laser Treatment" => 13,
					"Locksmithing" => 13,
					"Marine Technician" => 13,
					"Massage Therapy" => 13,
					"Mechanics" => 13,
					"Medical Assisting" => 13,
					"Mortuary Science" => 13,
					"Motorcycle Technician" => 13,
					"Nail Technician" => 13,
					"Plumbing" => 13,
					"Property Management" => 13,
					"Restaurant Management" => 13,
					"Salon Management" => 13,
					"Skin Care" => 13,
					"Truck Driving" => 13,
					"Veterinary Assisting" => 13,
					"Welding" => 13,
					"Respiratory Therapy" => 13,
					"Diagnostic Ultrasound Technology" => 13,
					"Lymph Drainage" => 13,
					"Personal Fitness Trainer" => 13,
					"Orthopedic Massage" => 13,);


		foreach ($arr as $key => $value) {
			$qry = DB::connection('rds1')->table('majors')
										 ->where('name', 'LIKE', '%'.$value.'%')
										 ->get();
			foreach ($qry as $k) {
				print_r("['is_default' => '0', 'plexuss_value' => '".$k->id."', 'client_value' => ".$tmp[$value].",]," ."<br/>");
			}
		}
    }
    public function savePlexussPixelInfo(){
        $input = Request::all();

        $collegeSubmission = new CollegeSubmission;
        $collegeSubmission->type    = 'Plexuss Pixel';
        $collegeSubmission->company = isset($input['joinInstitution'])? $input['joinInstitution'] : '';
        $collegeSubmission->contact = isset($input['joinName'])? $input['joinName'] : '';
        $collegeSubmission->title   = isset($input['joinTitle'])? $input['joinTitle'] : '';
        $collegeSubmission->email   = isset($input['joinEmail'])? $input['joinEmail'] : '';
        $collegeSubmission->phone   = isset($input['joinPhone'])? $input['joinPhone'] : '';

        $collegeSubmission->newsletter = (isset($input['joinBlogNews']) && $input['joinBlogNews'] === 'on') ? 1 : 0;
        $collegeSubmission->analyticsletter = (isset($input['joinAnalytics']) && $input['joinBlogNews'] === 'on') ? 1 : 0;

        // Message for text message to team.
        $msg = "Type: " . $collegeSubmission->type . "\nCompany: " . $collegeSubmission->company . "\nName: " . $collegeSubmission->contact . "\nTitle:" . $collegeSubmission->title . "\nEmail: " . $collegeSubmission->email . "\nPhone: " . $collegeSubmission->phone . "\nNewsletter Opt-in: " . $collegeSubmission->newsletter . "\nAnalytics Letter Opt-in: " . $collegeSubmission->analyticsletter;

        // Parameters for email template.
        $params = [
            'COMPANY' => $collegeSubmission->company,
            'CONTACTNAME' => $collegeSubmission->contact,
            'TITLE' => $collegeSubmission->title,
            'EMAIL' => $collegeSubmission->email,
            'PHONE' => $collegeSubmission->phone,
            'NEWSLETTEROPTIN' => $collegeSubmission->newsletter,
            'ANALYTICSLETTEROPTIN' => $collegeSubmission->analyticsletter,
        ];

        $insert =  $collegeSubmission->save();

        if($insert){
            $tc = new TwilioController;
            $tc->sendPlexussMsg($msg);

            $mac = new MandrillAutomationController();
            $mac->sendPlexussPixelRequestEmail($params);

            return "success";
        }
    }

    public function saveAudienceInfo(){
        $input = Request::all();

        $collegeSubmission = new CollegeSubmission;
        $collegeSubmission->type    = 'Audience';
        $collegeSubmission->company = isset($input['joinInstitution'])? $input['joinInstitution'] : '';
        $collegeSubmission->contact = isset($input['joinName'])? $input['joinName'] : '';
        $collegeSubmission->title   = isset($input['joinTitle'])? $input['joinTitle'] : '';
        $collegeSubmission->email   = isset($input['joinEmail'])? $input['joinEmail'] : '';
        $collegeSubmission->phone   = isset($input['joinPhone'])? $input['joinPhone'] : '';

        $collegeSubmission->newsletter = (isset($input['joinBlogNews']) && $input['joinBlogNews'] === 'on') ? 1 : 0;
        $collegeSubmission->analyticsletter = (isset($input['joinAnalytics']) && $input['joinBlogNews'] === 'on') ? 1 : 0;

        $insert =  $collegeSubmission->save();

        // Message for text message to team.
        $msg = "Type: " . $collegeSubmission->type . "\nCompany: " . $collegeSubmission->company . "\nName: " . $collegeSubmission->contact . "\nTitle:" . $collegeSubmission->title . "\nEmail: " . $collegeSubmission->email . "\nPhone: " . $collegeSubmission->phone . "\nNewsletter Opt-in: " . $collegeSubmission->newsletter . "\nAnalytics Letter Opt-in: " . $collegeSubmission->analyticsletter;

        if($insert){
            $tc = new TwilioController;
            $tc->sendPlexussMsg($msg);

            $mac = new MandrillAutomationController();
            $mac->sendB2bResourcesEmail($collegeSubmission->email);

            return "success";
        }
    }

    public function saveClientJourney(){
        $input = Request::all();

        $collegeSubmission = new CollegeSubmission;
        $collegeSubmission->type    = 'Client Journey';
        $collegeSubmission->company = isset($input['joinInstitution'])? $input['joinInstitution'] : '';
        $collegeSubmission->contact = isset($input['joinName'])? $input['joinName'] : '';
        $collegeSubmission->title   = isset($input['joinTitle'])? $input['joinTitle'] : '';
        $collegeSubmission->email   = isset($input['joinEmail'])? $input['joinEmail'] : '';
        $collegeSubmission->phone   = isset($input['joinPhone'])? $input['joinPhone'] : '';

        $collegeSubmission->newsletter = (isset($input['joinBlogNews']) && $input['joinBlogNews'] === 'on') ? 1 : 0;
        $collegeSubmission->analyticsletter = (isset($input['joinAnalytics']) && $input['joinBlogNews'] === 'on') ? 1 : 0;

        $insert =  $collegeSubmission->save();


        // Message for text message to team.
        $msg = "Type: " . $collegeSubmission->type . "\nCompany: " . $collegeSubmission->company . "\nName: " . $collegeSubmission->contact . "\nTitle:" . $collegeSubmission->title . "\nEmail: " . $collegeSubmission->email . "\nPhone: " . $collegeSubmission->phone . "\nNewsletter Opt-in: " . $collegeSubmission->newsletter . "\nAnalytics Letter Opt-in: " . $collegeSubmission->analyticsletter;

        if($insert){
            $tc = new TwilioController;
            $tc->sendPlexussMsg($msg);

            $mac = new MandrillAutomationController();
            $mac->sendB2bResourcesEmail($collegeSubmission->email);

            return "success";
        }
    }

    public function saveStudentJourney(){
        $input = Request::all();

        $collegeSubmission = new CollegeSubmission;
        $collegeSubmission->type    = 'Student Journey';
        $collegeSubmission->company = isset($input['joinInstitution'])? $input['joinInstitution'] : '';
        $collegeSubmission->contact = isset($input['joinName'])? $input['joinName'] : '';
        $collegeSubmission->title   = isset($input['joinTitle'])? $input['joinTitle'] : '';
        $collegeSubmission->email   = isset($input['joinEmail'])? $input['joinEmail'] : '';
        $collegeSubmission->phone   = isset($input['joinPhone'])? $input['joinPhone'] : '';

        $collegeSubmission->newsletter = (isset($input['joinBlogNews']) && $input['joinBlogNews'] === 'on') ? 1 : 0;
        $collegeSubmission->analyticsletter = (isset($input['joinAnalytics']) && $input['joinBlogNews'] === 'on') ? 1 : 0;

        $insert =  $collegeSubmission->save();


        // Message for text message to team.
        $msg = "Type: " . $collegeSubmission->type . "\nCompany: " . $collegeSubmission->company . "\nName: " . $collegeSubmission->contact . "\nTitle:" . $collegeSubmission->title . "\nEmail: " . $collegeSubmission->email . "\nPhone: " . $collegeSubmission->phone . "\nNewsletter Opt-in: " . $collegeSubmission->newsletter . "\nAnalytics Letter Opt-in: " . $collegeSubmission->analyticsletter;

        if($insert){
            $tc = new TwilioController;
            $tc->sendPlexussMsg($msg);

            $mac = new MandrillAutomationController();
            $mac->sendB2bResourcesEmail($collegeSubmission->email);

            return "success";
        }
    }

    public function checkCompany(){
        $input = Request::all();
        $model = AdRedirectCampaign::where('company',$input['company'])->first();
        if(isset($model) && $model != ''){
            return "exist";
        } else {
            return "success";
        }
    }

    public function deleteUserEmailSupressionList(){
        $input = Request::all();

        $user = User::find($input['uid']);

        /* sparkpost */
        if(env('SPARKPOST_KEY') != ''){
            $key = env('SPARKPOST_KEY');
        }else{
            $key = $_ENV['SPARKPOST_KEY'];
        }

//        echo $key.'<br/>';
        $url = "https://api.sparkpost.com/api/v1/suppression-list/".$user->email;
        $ch = curl_init($url);

        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization:' .$key;

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, $header);

        $res = curl_exec($ch);
        curl_close($ch);
//        print_r($res);
        $result = json_decode($res);
        if($result == ''){
            $sparkpost = '';
        } else{
            $sparkpost = serialize(json_decode($res));

        }

        /* sparkpost direct */
        if(env('SPARKPOST_DIRECT_KEY') != ''){
            $sparkpostdirectkey = env('SPARKPOST_DIRECT_KEY');
        } else {
            $sparkpostdirectkey = $_ENV['SPARKPOST_DIRECT_KEY'];
        }
        //echo $sparkpostdirectkey.'<br/>';

        $url = "https://api.sparkpost.com/api/v1/suppression-list/".$user->email;
        $ch = curl_init($url);

        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization:' .$sparkpostdirectkey;

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, $header);

        $res = curl_exec($ch);
        curl_close($ch);
//        print_r($res);
        $result = json_decode($res);
        if($result == ''){
            $sparkpostdirect = '';
        } else{
            $sparkpostdirect = serialize(json_decode($res));
        }

        /* sendgrid */
        if(env('SENDGRID_KEY') != ''){
            $sendgridkey = env('SENDGRID_KEY');
        } else {
            $sendgridkey = $_ENV['SENDGRID_KEY'];
        }
       // echo $sendgridkey.'<br/>';

        $url = "https://api.sendgrid.com/v3/asm/suppressions/global/".$user->email;
        $ch = curl_init($url);

        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization:Bearer '.$sendgridkey;

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, $header);

        $response = curl_exec($ch);
        curl_close($ch);
//        print_r($response);
        $result = json_decode($response);
        if($result == ''){
            $sendgrid = '';
        } else{
            $sendgrid = serialize(json_decode($response));
        }

        $delete = EmailSuppressionList::where('uid', $input['uid'])->delete();
//        echo $delete;

        $emailResubscribeList = new EmailResubscribeList();
        $emailResubscribeList->user_id = $user->id;
        $emailResubscribeList->sparkpost_response = $sparkpost;
        $emailResubscribeList->sparkpost_direct_response = $sparkpostdirect;
        $emailResubscribeList->sendgrid_response = $sendgrid;
        $emailResubscribeList->created_at = date('Y-m-d H:i:s',time());
        if($emailResubscribeList->save()){
            return "success";
        }
    }

	public function getAllDates(){
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

		return $dates;
	}

    public function plexussCookieAgree() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $platform = Agent::platform();
        $browser = Agent::browser();
        $device = Agent::device();

        $iplookup = $this->iplookup();

        $values = [];

        $agreement = new PlexussCookieAgreement;

        isset($data['user_id']) ? $values['user_id'] = $data['user_id'] : NULL;

        isset($platform) ? $values['platform'] = $platform : NULL;
        isset($browser) ? $values['browser'] = $browser : NULL;
        isset($device) ? $values['device'] = $device : NULL;
        isset($iplookup['ip']) ? $values['ip'] = $iplookup['ip'] : NULL;
        isset($iplookup['countryName']) ? $values['countryName'] = $iplookup['countryName'] : NULL;
        isset($iplookup['stateName']) ? $values['stateName'] = $iplookup['stateName'] : NULL;
        isset($iplookup['cityName']) ? $values['cityName'] = $iplookup['cityName'] : NULL;

        $values['updated_at'] = date('Y-m-d H:i:s');
        $values['created_at'] = date('Y-m-d H:i:s');

        $agreement->insert($values);

        return 'success';
    }

	public function closeSignupOffer(){
		Session::put('closeSignupOffer', 'true');
	}

	public function setAdminRecommendationFilter_scholarshipadmin($tab_name = null){
		if ($tab_name == null) {
			return;
		}


		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$crf = new CollegeRecommendationFilters;
		$crfl = new CollegeRecommendationFilterLogs;

		$agency_id = null;
		$org_portal_id = NULL;
		$aor_id = NULL;

		$scholarship_id = Session::get('temp_scholarshipadmin_id') ? Session::get('temp_scholarshipadmin_id') : 0;
		$user_id = $data['user_id'];

		$schModel = new Scholarshipcms();
		/*$checkcount = $schModel->checkScholarshipAdminExist($user_id);
		if($checkcount >0){
			$checkid = $schModel->checkScholarshipAdmin($user_id);
			$scholarship_id = $checkid->id;
		}*/

		$crfl->clearFilter_scholarship($tab_name, $scholarship_id);
		$crf->clearFilter_scholarship($tab_name, $scholarship_id);

		switch ($tab_name) {
			case 'location':

				// $us_filter = $this->inputIsset($input, 'us_filter');
				// $intl_filter = $this->inputIsset($input, 'intl_filter');
				$us_filter = 'true';
				$intl_filter = '';

				//if (isset($us_filter)) {

				$all_country_filter = $this->inputIsset($input, 'all_country_filter');
				$include_country_filter = $this->inputIsset($input, 'include_country_filter');
				$exclude_country_filter = $this->inputIsset($input, 'exclude_country_filter');
				$country = $this->inputIsset($input, 'country');

				$all_state_filter = $this->inputIsset($input, 'all_state_filter');
				$include_state_filter = $this->inputIsset($input, 'include_state_filter');
				$exclude_state_filter = $this->inputIsset($input, 'exclude_state_filter');
				$state = $this->inputIsset($input, 'state');

				$all_city_filter = $this->inputIsset($input, 'all_city_filter');
				$include_city_filter = $this->inputIsset($input, 'include_city_filter');
				$exclude_city_filter = $this->inputIsset($input, 'exclude_city_filter');
				$city = $this->inputIsset($input, 'city');




				$this->saveFilterTemplate2_scholarshipadmin($all_country_filter, $include_country_filter,
				$data, $crf, $tab_name, 'country', $country, $crfl,$scholarship_id);

				$this->saveFilterTemplate2_scholarshipadmin($all_state_filter, $include_state_filter,
				$data, $crf, $tab_name, 'state', $state, $crfl,$scholarship_id);

				$this->saveFilterTemplate2_scholarshipadmin($all_city_filter, $include_city_filter,
				$data, $crf, $tab_name, 'city', $city, $crfl,$scholarship_id);

				break;

			case 'major':
				$all_department_filter = $this->inputIsset($input, 'all_department_filter');
				$include_department_filter = $this->inputIsset($input, 'include_department_filter');
				$exclude_department_filter = $this->inputIsset($input, 'exclude_department_filter');
				$department = $this->inputIsset($input, 'department');

				$this->saveFilterTemplate2_scholarshipadmin($all_department_filter, $include_department_filter,
					$data, $crf, $tab_name, 'department', $department, $crfl,$scholarship_id);

				$all_major_filter = $this->inputIsset($input, 'all_major_filter');
				$include_major_filter = $this->inputIsset($input, 'include_major_filter');
				$exclude_major_filter = $this->inputIsset($input, 'exclude_major_filter');
				$major = $this->inputIsset($input, 'major');

				$this->saveFilterTemplate2_scholarshipadmin($all_major_filter, $include_major_filter,
					$data, $crf, $tab_name, 'major', $major, $crfl,$scholarship_id);

				break;

			case 'majorDeptDegree':

				$this->saveFilterForMajor_scholarshipadmin($data, $input, $crf, $crfl,$scholarship_id);
				break;

			case 'scores':

				$gpaMin_filter = $this->inputIsset($input, 'gpaMin_filter');
				$gpaMax_filter = $this->inputIsset($input, 'gpaMax_filter');
				$hsWeightedGPAMin_filter = $this->inputIsset($input, 'hsWeightedGPAMin_filter');
				$hsWeightedGPAMax_filter = $this->inputIsset($input, 'hsWeightedGPAMax_filter');
				$collegeGPAMin_filter = $this->inputIsset($input, 'collegeGPAMin_filter');
				$collegeGPAMax_filter = $this->inputIsset($input, 'collegeGPAMax_filter');
				$satMin_filter = $this->inputIsset($input, 'satMin_filter');
				$satMax_filter = $this->inputIsset($input, 'satMax_filter');
				$actMin_filter = $this->inputIsset($input, 'actMin_filter');
				$actMax_filter = $this->inputIsset($input, 'actMax_filter');
				$toeflMin_filter = $this->inputIsset($input, 'toeflMin_filter');
				$toeflMax_filter = $this->inputIsset($input, 'toeflMax_filter');
				$ieltsMin_filter = $this->inputIsset($input, 'ieltsMin_filter');
				$ieltsMax_filter = $this->inputIsset($input, 'ieltsMax_filter');

				//gpa
				if(!empty($gpaMin_filter) && !empty($gpaMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin($gpaMin_filter.','.$gpaMax_filter, $data, $crf, $tab_name, 'gpa_filter', $crfl,$scholarship_id);
				}
				elseif(!empty($gpaMin_filter)){
					$this->saveFilterTemplate1_scholarshipadmin($gpaMin_filter.',4', $data, $crf, $tab_name, 'gpa_filter', $crfl,$scholarship_id);
				}
				elseif(!empty($gpaMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('0,'.$gpaMax_filter, $data, $crf, $tab_name, 'gpa_filter', $crfl,$scholarship_id);
				}
				//act/sat
				if(!empty($satMin_filter) && !empty($satMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('sat,'.$satMin_filter.','.$satMax_filter, $data, $crf, $tab_name, 'sat_act', $crfl,$scholarship_id);
				}elseif(!empty($satMin_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('sat,'.$satMin_filter.',2400', $data, $crf, $tab_name, 'sat_act', $crfl,$scholarship_id);
				}elseif(!empty($satMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('sat,0,'.$satMax_filter, $data, $crf, $tab_name, 'sat_act', $crfl,$scholarship_id);
				}
				if (!empty($actMin_filter) && !empty($actMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('act,'.$actMin_filter.','.$actMax_filter, $data, $crf, $tab_name, 'sat_act', $crfl,$scholarship_id);
				}elseif(!empty($actMin_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('act,'.$actMin_filter.',36', $data, $crf, $tab_name, 'sat_act', $crfl,$scholarship_id);
				}elseif(!empty($actMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('act,0,'.$actMax_filter, $data, $crf, $tab_name, 'sat_act', $crfl,$scholarship_id);
				}
				//toefl/ielts
				if (!empty($toeflMin_filter) && !empty($toeflMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('toefl,'.$toeflMin_filter.','.$toeflMax_filter, $data, $crf, $tab_name, 'ielts_toefl', $crfl,$scholarship_id);
				}elseif(!empty($toeflMin_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('toefl,'.$toeflMin_filter.',120', $data, $crf, $tab_name, 'ielts_toefl', $crfl,$scholarship_id);
				}elseif(!empty($toeflMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('toefl,0,'.$toeflMax_filter, $data, $crf, $tab_name, 'ielts_toefl', $crfl,$scholarship_id);
				}
				if (!empty($ieltsMin_filter) && !empty($ieltsMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('ielts,'.$ieltsMin_filter.','.$ieltsMax_filter, $data, $crf, $tab_name, 'ielts_toefl', $crfl,$scholarship_id);
				}elseif(!empty($ieltsMin_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('ielts,'.$ieltsMin_filter.',9', $data, $crf, $tab_name, 'ielts_toefl', $crfl,$scholarship_id);
				}elseif(!empty($ieltsMax_filter)){
					$this->saveFilterTemplate1_scholarshipadmin('ielts,0,'.$ieltsMax_filter, $data, $crf, $tab_name, 'ielts_toefl', $crfl,$scholarship_id);
				}
				break;

			case 'uploads':
				$transcript_filter = $this->inputIsset($input, 'transcript_filter');
				$financialInfo_filter = $this->inputIsset($input, 'financialInfo_filter');
				$ielts_fitler = $this->inputIsset($input, 'ielts_fitler');
				$toefl_filter = $this->inputIsset($input, 'toefl_filter');
				$resume_filter = $this->inputIsset($input, 'resume_filter');

				if ($transcript_filter == 'false' || $financialInfo_filter == 'false' || $ielts_fitler == 'false' ||
					$toefl_filter == 'false' || $resume_filter == 'false') {

					$qry_id = $this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, 'include', $tab_name, 'uploads',$scholarship_id);

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
				$ethnicity = $this->inputIsset($input, 'ethnicity');
				$all_rgs_filter = $this->inputIsset($input, 'all_rgs_filter');
				$include_rgs_filter = $this->inputIsset($input, 'include_rgs_filter');
				$exclude_rgs_filter = $this->inputIsset($input, 'exclude_rgs_filter');
				$religion = $this->inputIsset($input, 'religion');


				$this->saveFilterTemplate1_scholarshipadmin($ageMin_filter , $data, $crf, $tab_name, 'ageMin_filter', $crfl, $scholarship_id);
				$this->saveFilterTemplate1_scholarshipadmin($ageMax_filter , $data, $crf, $tab_name, 'ageMax_filter', $crfl, $scholarship_id);

				if ($all_gender_filter == 'false') {

					$qry_id = $this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, 'include', $tab_name, 'gender', $scholarship_id);

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

				$this->saveFilterTemplate2_scholarshipadmin($all_eth_filter, $include_eth_filter,
					$data, $crf, $tab_name, 'include_eth_filter', $ethnicity, $crfl, $scholarship_id);

				$this->saveFilterTemplate2_scholarshipadmin($all_rgs_filter, $include_rgs_filter,
					$data, $crf, $tab_name, 'include_rgs_filter', $religion, $crfl, $scholarship_id);

				break;

			case 'educationLevel':

				$hsUsers_filter = $this->inputIsset($input, 'hsUsers_filter');
				$collegeUsers_filter = $this->inputIsset($input, 'collegeUsers_filter');
				if ($hsUsers_filter == 'false' || $collegeUsers_filter == 'false') {

					$qry_id = $this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, 'include', $tab_name, 'educationLevel', $scholarship_id);

					$educationLevel = '';
					if ($hsUsers_filter == 'true') {
						$educationLevel = 'hsUsers_filter';
					}else{
						$educationLevel = 'collegeUsers_filter';
					}
					$arr = array();
					$tmp = array();
					$tmp['val'] = $educationLevel;
					$tmp['rec_filter_id'] = $qry_id;
					$arr[] = $tmp;

					$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
				}


				break;

			case 'desiredDegree':

				$certificate = $this->inputIsset($input, '1_filter');
				$associate = $this->inputIsset($input, '2_filter');
				$bachelor = $this->inputIsset($input, '3_filter');
				$master = $this->inputIsset($input, '4_filter');
				$phd = $this->inputIsset($input, '5_filter');
				$undecided = $this->inputIsset($input, '6_filter');
				$diploma = $this->inputIsset($input, '7_filter');
				$other = $this->inputIsset($input, '8_filter');
				$jd = $this->inputIsset($input, '9_filter');

				if ($certificate == "false" || $associate == "false" || $bachelor == "false" ||
					$master == "false" || $phd == "false" || $undecided == "false" ||
					$diploma == "false" || $other == "false" || $jd == "false") {

					$qry_id = $this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, 'exclude', $tab_name, 'desiredDegree', $scholarship_id);

					$desiredDegree = array();
					if ($certificate == 'false') {
						$desiredDegree[] = 'Certificate Programs';
					}
					if ($associate == 'false') {
						$desiredDegree[] = "Associate's Degree";
					}
					if ($bachelor == 'false') {
						$desiredDegree[] = "Bachelor's Degree";
					}

					if ($master == 'false') {
						$desiredDegree[] = "Master's Degree";
					}
					if ($phd == 'false') {
						$desiredDegree[] = "PHD / Doctorate";
					}
					if ($undecided == 'false') {
						$desiredDegree[] = "Undecided";
					}

					if ($diploma == 'false') {
						$desiredDegree[] = "Diploma";
					}
					if ($other == 'false') {
						$desiredDegree[] = "Other";
					}
					if ($jd == 'false') {
						$desiredDegree[] = "Juris Doctor";
					}

					$arr = array();
					foreach ($desiredDegree as $key => $value) {
						$tmp = array();
						$tmp['val'] = $value;
						$tmp['rec_filter_id'] = $qry_id;
						$arr[] = $tmp;
					}

					$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);

				}

				break;

			case 'skillsInterests':
				break;
			case 'militaryAffiliation':

				$inMilitary_filter = $this->inputIsset($input, 'inMilitary');
				$militaryAffiliation_filter = $this->inputIsset($input, 'militaryAffiliation');

				$this->saveFilterTemplate1_scholarshipadmin($inMilitary_filter , $data, $crf, $tab_name, 'inMilitary', $crfl,$scholarship_id);

				if ($militaryAffiliation_filter != false && $militaryAffiliation_filter != 'false') {
					$this->saveFilterTemplate2_scholarshipadmin('false', 'true', $data, $crf, $tab_name, 'militaryAffiliation', $militaryAffiliation_filter, $crfl,$scholarship_id);
				}

				break;

			case 'profileCompletion':

				$profileCompletion_filter = $this->inputIsset($input, 'profileCompletion');
				$this->saveFilterTemplate1_scholarshipadmin($profileCompletion_filter , $data, $crf, $tab_name, 'profileCompletion', $crfl,$scholarship_id);

				break;

			case 'startDateTerm':

				$startDateTerm_filter = $this->inputIsset($input, 'startDateTerm');

				$this->saveFilterTemplate2_scholarshipadmin('false', 'true', $data, $crf, $tab_name, 'startDateTerm', $startDateTerm_filter, $crfl,$scholarship_id);
				break;

			case 'financial':


			if(isset($input['financial'])){

				//get filter
				$financials_filter = $this->inputIsset($input, 'financial');

				//exclude the $interested_in_aid fiter
				$financials_filter = array_diff($financials_filter, array('interested_in_aid'));


				$this->saveFilterTemplate2_scholarshipadmin('false', 'true', $data, $crf, $tab_name, 'financial', $financials_filter, $crfl,$scholarship_id);


			}
			if(isset($input['interested_in_aid']) && $input['interested_in_aid'] == 'true') {
					$this->saveFilterTemplate1_scholarshipadmin('1', $data, $crf, $tab_name, 'interested_in_aid', $crfl,$scholarship_id);
			}
				break;
			case 'typeofschool':
				$interested_school_type = '0';

				if ($input['both_typeofschool'] == 'true') {
					$interested_school_type = '2';
				}elseif ($input['online_only_typeofschool'] == 'true') {
					$interested_school_type = '1';
				}elseif ($input['campus_only_typeofschool']) {
					$interested_school_type = '0';
				}

				$this->saveFilterTemplate1_scholarshipadmin($interested_school_type , $data, $crf, $tab_name, 'interested_school_type', $crfl,$scholarship_id);

				break;
			default:
				# code...
				break;
		}

		// Add user ids to recruitment tag cron job to be reset for tagging
		if (Cache::has(env('ENVIRONMENT').'_'.'addTargettingUsersToRecruitmentTagCronJob')) {
			$arr = Cache::get(env('ENVIRONMENT').'_'.'addTargettingUsersToRecruitmentTagCronJob');
		}else{
			$arr = array();
		}
		if (!in_array($data['org_school_id'], $arr)) {
			$arr[] = $data['org_school_id'];
		}


		Cache::forever(env('ENVIRONMENT').'_'.'addTargettingUsersToRecruitmentTagCronJob', $arr);
		// recruitment tag cron jobs ends here.

	}

	public function resetAdminRecommendationFilter_scholarshipadmin($tab_name = null){
		if( !isset($tab_name) ){
			return 'failed';
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$crf = new CollegeRecommendationFilters;
		$crfl = new CollegeRecommendationFilterLogs;

		$agency_id = null;
		$org_portal_id = NULL;
		$aor_id = NULL;


		$scholarship_id = Session::get('temp_scholarshipadmin_id') ? Session::get('temp_scholarshipadmin_id') : 0;
		$user_id = $data['user_id'];

		$schModel = new Scholarshipcms();
		/*$checkcount = $schModel->checkScholarshipAdminExist($user_id);
		if($checkcount >0){
			$checkid = $schModel->checkScholarshipAdmin($user_id);
			$scholarship_id = $checkid->id;
		}*/

		$crfl->clearFilter_scholarship($tab_name, $scholarship_id);
		$crf->clearFilter_scholarship($tab_name, $scholarship_id);

		return 'done';
	}


	// get all notification on json
	public function getAllNotificationsJSON(){
		$nc = new NotificationController();
		return response()->json($nc->index(true));
	}


	private function saveFilterForMajor_scholarshipadmin($data, $input, $crf, $crfl,$scholarship_id){

		//print_r($data);
		//print_r($input);

		$include_bool = false;
		$exclude_bool = false;
		$include_arr  = array();
		$exclude_arr  = array();

		if( isset($input['data']) && !empty($input['data']) ){
			foreach ($input['data'] as $key) {
				if ($include_bool == false && $key['in_ex'] == 'include') {
					$include_qry_id =$this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, $key['in_ex'], 'majorDeptDegree', 'majorDeptDegree',$scholarship_id);
					$include_bool = true;
				}

				if ($exclude_bool == false && $key['in_ex'] == 'exclude') {
					$exclude_qry_id =$this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, $key['in_ex'], 'majorDeptDegree', 'majorDeptDegree',$scholarship_id);
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

	private function saveFilterTemplate1_scholarshipadmin($cat_name , $data, $crf, $tab_name, $name, $crfl,$scholarship_id){

		if ($cat_name != 'false' && $cat_name != '') {
			$qry_id = $this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, 'include', $tab_name, $name,$scholarship_id);

			$arr = array();
			$tmp = array();
			$tmp['val'] = $cat_name;
			$tmp['rec_filter_id'] = $qry_id;
			$arr[] = $tmp;

			$this->saveAdminFilterLogRecommendation($crfl, $qry_id, $arr);
		}
	}

	private function saveFilterTemplate2_scholarshipadmin($all_filter, $include_filter, $data, $crf, $tab_name, $name, $val_arr, $crfl,$scholarship_id){
		if ($all_filter === 'false') {
			if ($include_filter === 'true') {
				$qry_id =$this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, 'include', $tab_name, $name,$scholarship_id);
			}else{
				$qry_id = $this->saveAdminFilterRecommendation_scholarshipadmin($data, $crf, 'exclude', $tab_name, $name,$scholarship_id);
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

	private function saveAdminFilterRecommendation_scholarshipadmin($data, $crf, $type, $category, $name,$scholarship_id){

		$agency_id = NULL;
		$org_portal_id = NULL;
		$aor_id = NULL;

		$crf = $crf->saveFilter_scholarship($agency_id, NULL, NULL, $type, $category, $name, $org_portal_id, $aor_id,$scholarship_id);

		return $crf->id;
	}


}
