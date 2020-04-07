<?php

namespace App\Http\Controllers;

// use Mail;
use Carbon\Carbon, DateTime, DateTimeZone;
use Request, DB, Session, Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

use App\User, App\Organization, App\OrganizationBranch, App\OrganizationPortal, App\OrganizationBranchPermission, App\CollegeRecommendationFilters;
use App\ZipCodes, App\Country, App\Department, App\Degree, App\Ethnicity, App\Religion, App\MilitaryAffiliation, App\DistributionClient, App\DistributionResponse, App\AdminText;
use App\ExportField, App\MicrosoftImageQuery, App\Priority, App\PlexussAnnouncement, App\Recruitment, App\TrackingPage, App\PrescreenedUser;
use App\CollegeRecommendation, App\CollegesInternationalTab, App\CollegesInternationalTuitionCost, App\AdvancedSearchNote, App\Transcript, App\RecruitmentRevenueOrgRelation;
use App\UsersAppliedColleges, App\Score, App\Objective, App\Major, App\PhoneLog, App\SmsLog, App\PlexussVerificationsUser, App\RecruitmentVerifiedApp;
use App\UsersFinancialFirstyrAffordibilityLog, App\CollegeMessageLog, App\NotificationTopNav, App\LikesTally, App\RankingList;
use App\Http\Controllers\AjaxController, App\Http\Controllers\DistributionController, App\CollegesApplicationsState, App\CollegesApplicationStatus, App\College, App\RecruitmentVerifiedHS, App\UsersCustomQuestion, App\Http\Controllers\MandrillAutomationController, App\AorCollege, App\CrmAutoReporting, App\Scholarshipcms;
use App\CrmNotes;
use App\AdRedirectCampaign, App\AdClick, App\EmailTemplateSenderProvider;
use App\RecruitmentConvert;
use App\RevenueOrganization;
use GuzzleHttp\Client;
use App\EmailReporting, App\EmailClickLog;
use App\Http\Controllers\BetaUserController;

class AdminController extends Controller
{
    protected $school_name='';
	protected $school_logo='';

    const CRONTIME = "+6 hours";

    private $max_cron_date='';
    private $min_cron_date='';

    private $num_of_inquiry 		 = 0;
    private $num_of_recommended 	 = 0;
    private $num_of_pending 		 = 0;
    private $num_of_approved 		 = 0;
    private $num_of_removed 		 = 0;
    private $num_of_rejected 		 = 0;
    private $num_of_prescreened 	 = 0;

    private $num_of_inquiry_new 	 = 0;
    private $num_of_recommended_new  = 0;
    private $num_of_pending_new 	 = 0;
    private $num_of_approved_new 	 = 0;
    private $num_of_removed_new 	 = 0;
    private $num_of_rejected_new 	 = 0;
    private $num_of_verified_hs 	 = 0;
    private $num_of_verified_hs_new  = 0;
    private $num_of_verified_app 	 = 0;
    private $num_of_verified_app_new = 0;


    private function leftBarNumbers($data, $is_dashboard = NULL){
		$this->setRecommendationExpirationDate();
		if (isset($data['org_school_id'])) {
			$this->collegeAdminPanelCnt($data, $is_dashboard);
		}
    }

	public function index(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

        $data['is_admin_premium'] = $this->validateAdminPremium();

		if(!isset($data['is_organization']) || $data['is_organization']  == 0){
			return redirect('/');
		}

		//determine is current user is the organization's first user here
		if( isset($data['org_branch_id']) ){
			$obp = new OrganizationBranchPermission;
			$data['orgs_first_user'] = $obp->isOrgsFirstUser($data['org_branch_id']);
		}

		//if user is the organization's first user AND they have not completed signup, return view w/ basic data
		if( isset($data['completed_signup']) && $data['completed_signup'] == 0 && $data['orgs_first_user'] ){
			$data['currentPage'] = 'admin';
			$data['title'] = 'ADMIN';
			return View('admin.index', $data);
		}else{
			return $this->loadAdmin($data);
		}
	}
	
	
	 public function postInterestedPremiumServices() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $input = Request::all();

        $services = $input['services'];

        $mac = new MandrillAutomationController();

        $mac->collegeInterestPremiumServices($services);

        return 'success';
    }

	public function helpPage(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'admin-faq';
		$data['title'] = 'Help and FAQs';

		return View('admin.faq', $data);
	}

	public function messageIndex(){

		return $this->loadMessageAdmin();
	}

	public function convertedIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Converted Students';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_converted_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_converted_input');
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
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

	    Session::put(env('ENVIRONMENT') .'_'.'AdminController_converted_input', $input);

		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		return $this->loadInquirieAdmin($data, 'converted', $is_ajax, $input, $filter_audience);
	}

	public function inquirieIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Student Inquiries';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_input');
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
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

	    Session::put(env('ENVIRONMENT') .'_'.'AdminController_inquiries_input', $input);

		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		return $this->loadInquirieAdmin($data, null, $is_ajax, $input, $filter_audience);
	}

	public function updateSearchResults($currentPage = null) {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$filter_audience = Request::all();

		// env('ENVIRONMENT') .'_'.'AdminController_inquiries_input'
		if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience', $filter_audience);

		if($currentPage == 'admin-approved') {
			return $this->approvedIndex(true, $filter_audience);
		} else if($currentPage == 'admin-pending') {
			return $this->pendingIndex(true, $filter_audience);
		} else if($currentPage == 'admin-inquiries') {
			return $this->inquirieIndex(true, $filter_audience);
		} else if($currentPage == 'admin-removed') {
			return $this->removedIndex(true, $filter_audience);
		} else if($currentPage == 'admin-rejected') {
			return $this->rejectedIndex(true, $filter_audience);
		} else if($currentPage == 'admin-verifiedHs') {
			return $this->verifiedHsIndex(true, $filter_audience);
		} else if($currentPage == 'admin-verifiedApp') {
			return $this->verifiedAppIndex(true, $filter_audience);
		} else if($currentPage == 'admin-prescreened') {
			return $this->prescreenedIndex(true, $filter_audience);
		} else if($currentPage == 'admin-converted') {
			return $this->convertedIndex(true, $filter_audience);
		} 
		else {
			return null;
		}
	}

	public function pendingIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Pending Students';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_pending_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_pending_input');
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
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_pending_input', $input);
		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		$export_fields = ExportField::leftjoin('export_field_exclusions as efe', function($join) use ($data) {
			$join->on('export_fields.id', '=', 'efe.export_field_id');
			$join->on('efe.type', '=', DB::raw('"college"'));
			$join->on('efe.type_id', '=', DB::raw($data['org_school_id']));
		})
		->whereNull('efe.export_field_id')
		->select('export_fields.id', 'export_fields.name')
		->where('name', '!=', 'Type');

		$export_fields = $export_fields->get()->toArray();

		$data['export_fields'] = $export_fields;
        $data['export_fields_date'] = '01/01/2014 to '. date("m/d/Y");

		return $this->loadInquirieAdmin($data,'pending', $is_ajax, $input, $filter_audience);
	}

	public function approvedIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Approved Students';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_approved_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_approved_input');
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
			if(isset($input['init']) && $input['init'] == '1') {
				// begin from filter audience and sorting
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
					$filter_audience['init'] = '1';
				}
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
					$filter_audience['init'] = '0';
				}
			}
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_approved_input', $input);

		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		//get applied reminder data
		$data['applied_reminder'] = $this->appliedReminderOnLoad($data);

		$export_fields = ExportField::leftjoin('export_field_exclusions as efe', function($join) use($data)
			 {
			    $join->on('export_fields.id', '=', 'efe.export_field_id');
			    $join->on('efe.type', '=', DB::raw('"college"'));
			    $join->on('efe.type_id', '=', DB::raw($data["org_school_id"]));
			})
			->whereNull('efe.export_field_id')
			->select('export_fields.id', 'export_fields.name');

		$export_fields = $export_fields->get()->toArray();

		// if ($data['show_filter']) {
		// 	$export_fields = $export_fields->get()->toArray();
		// }else{
		// 	$export_fields = $export_fields->where('export_fields.id', '!=', 2)
		// 								   ->where('export_fields.id', '!=', 3)
		// 								   ->where('export_fields.id', '!=', 4)
		// 								   ->get()->toArray();
		// }

		$data['export_fields'] = $export_fields;
		$data['export_fields_date'] = '01/01/2014 to '. date("m/d/Y");

		return $this->loadInquirieAdmin($data,'approved', $is_ajax, $input, $filter_audience);
	}

	public function recommendationIndex($is_ajax = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Student Recommendations';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_recommendations_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_recommendations_input');
			} else {
				$input = array();
			}

			$input['display'] = '15';
			$input['orderBy'] = null;
			$input['sortBy'] = null;
			$input['init'] = '1';
		} else {
			if(!empty($input['init']) && $input['init'] == '1') {
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			}
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_recommendations_input', $input);

		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		if($data['bachelor_plan'] == 1){

			$data['show_filter'] = true;

		}else{
			$data['show_filter'] = false;
		}

		$sales_super_power = Session::get('sales_super_power');

		if (isset($sales_super_power)) {
			$data['show_filter'] = true;
		}

		return $this->loadInquirieAdmin($data,'recommendations', $is_ajax, $input);
	}

	public function prescreenedIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Student PreScreened';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_prescreened_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_prescreened_input');
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
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_prescreened_input', $input);

		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		if($data['bachelor_plan'] == 1){

			$data['show_filter'] = true;

		}else{
			$data['show_filter'] = false;
		}

		$sales_super_power = Session::get('sales_super_power');

		if (isset($sales_super_power)) {
			$data['show_filter'] = true;
		}

		return $this->loadInquirieAdmin($data,'prescreened', $is_ajax, $input, $filter_audience);
	}

	public function verifiedHsIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Student verifiedHs';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_input');
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
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_input', $input);

		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		if($data['bachelor_plan'] == 1){

			$data['show_filter'] = true;

		}else{
			$data['show_filter'] = false;
		}

		$sales_super_power = Session::get('sales_super_power');

		if (isset($sales_super_power)) {
			$data['show_filter'] = true;
		}

		return $this->loadInquirieAdmin($data,'verifiedHs', $is_ajax, $input, $filter_audience);
	}

	public function verifiedAppIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Student verifiedApp';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_input');
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
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_input', $input);

		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		if($data['bachelor_plan'] == 1){

			$data['show_filter'] = true;

		}else{
			$data['show_filter'] = false;
		}

		$sales_super_power = Session::get('sales_super_power');

		if (isset($sales_super_power)) {
			$data['show_filter'] = true;
		}

		return $this->loadInquirieAdmin($data,'verifiedApp', $is_ajax, $input, $filter_audience);
	}

	public function removedIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Removed Students';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_removed_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_removed_input');
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
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_removed_input', $input);


		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		return $this->loadInquirieAdmin($data,'removed', $is_ajax, $input, $filter_audience);
	}

	public function rejectedIndex($is_ajax = null, $filter_audience = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData($is_ajax);

		$data['title'] = 'Rejected Students';

		$input = Request::all();

		if (!isset($input) || empty($input)) {
			// if on load
			Session::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
			// Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');

			if (Session::has(env('ENVIRONMENT') .'_'.'AdminController_rejected_input')) {
				$input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_rejected_input');
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
				Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);

				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
                    $filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '1';
                }
			} else {
				// begin from loadMore and displayOption
				if(Session::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience')) {
					$filter_audience = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_filter_audience');
                    $filter_audience['init'] = '0';
				}

			}
		}

		Session::put(env('ENVIRONMENT') .'_'.'AdminController_rejected_input', $input);

		if (!isset($is_ajax)) {
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
			Cache::forget(env('ENVIRONMENT') .'_'.'AdminController_converted_'.$data['user_id']);

		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		if(!isset($data['is_organization'])){
			return redirect('/');
		}

		return $this->loadInquirieAdmin($data,'rejected', $is_ajax, $input, $filter_audience);
	}

	// ----- get filter index
	public function getFilterIndex(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'admin-adv-filtering';
		$data['title'] = 'Admin Advanced Filtering';

        $data['is_admin_premium'] = $this->validateAdminPremium();

        if (!$data['is_admin_premium']) {
            return redirect('/admin/premium-plan-request');
        }

		if($this->school_name == '' || $this->school_logo == ''){
			$this->setSchoolNameAndLogo($data);
		}

		$data['school_name'] = $this->school_name;
		$data['school_logo'] = $this->school_logo;

		$ac = new AjaxController();

		$data['filter_perc'] = $ac->getNumberOfUsersForFilter();

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		if($data['bachelor_plan'] == 1){

			$data['show_filter'] = true;

		}else{
			$data['show_filter'] = false;
		}

		$sales_super_power = Session::get('sales_super_power');

		if (isset($sales_super_power)) {
			$data['show_filter'] = true;
		}

		return View('admin.recommendation_filter.index', $data);

	}

	public function getAjaxFilterSections( $section ){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if ($section == null) {
			return;
		}

		$org_portal_id = NULL;
		if (isset($data['default_organization_portal'])) {
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		if($this->school_name == '' || $this->school_logo == ''){
			$this->setSchoolNameAndLogo($data);
		}
		$data['school_name'] = $this->school_name;
		$data['school_logo'] = $this->school_logo;

		$filter_section = 'admin.recommendation_filter.ajax.'.$section;

		if ($section == 'video') {
			return View($filter_section, $data);
		}

		$crf = new CollegeRecommendationFilters;
		$filters = $crf->getFiltersAndLogs(null, $data['org_school_id'], $data['org_branch_id'], $section, $org_portal_id, $aor_id);
		
		//exit;
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
		
		/*echo "<pre>";
		print_r($data);
		echo "</pre>";*/
		return View($filter_section, $data);
	}


	//-- get content management page
	public function getContentManagement(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$data['currentPage'] = 'admin-content-management';
		$data['title'] = 'Admin Content Management';

		if($this->school_name == '' || $this->school_logo == ''){
			$this->setSchoolNameAndLogo($data);
		}

		$data['school_name'] = $this->school_name;
		$data['school_logo'] = $this->school_logo;

		return View('admin.content_management.index', $data);
	}

	//formatting mth/day/year to year/mth/day
	private function formatPremierDate($date){
		$start = explode('-', str_replace('/', '-', $date));
		$tmp = implode('-', array($start[2], $start[0], $start[1]));
		return $tmp;
	}

	// -- save goals
	public function setGoals(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
	    $premier_start_end_date = explode(" ", $input['premier_start_end_date']);
	    if(!empty($premier_start_end_date)) {
			$input['premier_start_date'] = $premier_start_end_date[0];
			$input['premier_end_date'] = $premier_start_end_date[2];
	    }

		$rules = array('num_of_applications' => 'required|integer|between:1,500',
			'num_of_enrollments' => 'required|integer|between:1,500');


		if (isset($input['premier_start_date']) && !empty($input['premier_start_date'])) {
			$start = $this->formatPremierDate($input['premier_start_date']);
		}
		if (isset($input['premier_end_date']) && !empty($input['premier_end_date'])) {
			$end = $this->formatPremierDate($input['premier_end_date']);
		}

		$validator = Validator::make( $input, $rules);
		if ($validator->fails()){
			$messages = $validator->messages();
			return $messages;
			exit();
		}

		if (!isset($data['default_organization_portal'])) {
			$org = OrganizationBranch::find($data['org_branch_id']);
			$org->num_of_applications = (int)$input['num_of_applications'];
			$org->num_of_enrollments = (int)$input['num_of_enrollments'];

			if (isset($start, $end)) {
				$org->premier_trial_begin_date = $start;
				$org->premier_trial_end_date = $end;
			}

			$org->save();
		}else{

			if (isset($start, $end)) {
				$update_arr = array('num_of_applications' => (int)$input['num_of_applications'],
										 				'num_of_enrollments'  => (int)$input['num_of_enrollments'],
										 				'premier_trial_begin_date' => $start,
										 				'premier_trial_end_date' => $end);
			}else{
				$update_arr = array('num_of_applications' => (int)$input['num_of_applications'],
										 				'num_of_enrollments'  => (int)$input['num_of_enrollments']);
			}


			$opu = OrganizationPortal::where('id', $data['default_organization_portal']->id)
									 ->update($update_arr);
		}

		Session::put('userinfo.session_reset', 1);

		$result = array();
		$result['num_of_applications'] = (int)$input['num_of_applications'];
		$result['num_of_enrollments'] = (int)$input['num_of_enrollments'];
		$result['startGoalSetting'] = 0;

		return $result;
	}

	// -- appointement has been set
	public function appointmentWasSet(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$org = OrganizationBranch::find($data['org_branch_id']);
		$org->set_goal_reminder = 1;
		$org->appointment_set = 1;
		$org->save();

		Session::put('userinfo.session_reset', 1);

		return 'success';
	}

	private function loadAdmin($data){

		$data['currentPage'] = 'admin';
		$data['title'] = 'ADMIN';

		$data['textmsg_reminder'] = 0;

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$export_fields = ExportField::leftjoin('export_field_exclusions as efe', function($join) use($data)
			 {
			    $join->on('export_fields.id', '=', 'efe.export_field_id');
			    $join->on('efe.type', '=', DB::raw('"college"'));
			    $join->on('efe.type_id', '=', DB::raw($data["org_school_id"]));
			})
			->whereNull('efe.export_field_id')
			->select('export_fields.id', 'export_fields.name') 
			->where('name','!=', 'Type');

		$export_fields = $export_fields->get()->toArray();

		$data['export_fields'] = $export_fields;

		$data['export_fields_date'] = '01/01/2014 to '. date("m/d/Y");


		// if trying to export students -- should be in cache-- modal in admin/master.blade needs to open
		//set flag $data['processing_export'] to true
		if( Cache::has(env('ENVIRONMENT').'_export_is_processing_msg_'.$data['user_id']) ){
			$data['processing_export'] = true;
			Cache::forget(env('ENVIRONMENT').'_export_is_processing_msg_'.$data['user_id']);
		}

		// background bing image
		$data['bing_bkground_img'] = $this->generateRandBingImage();
		// $data['bing_bkground_img'] = '';

		return View('admin.index', $data);
	}

	private function loadMessageAdmin(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if($this->school_name == '' || $this->school_logo == ''){
			$this->setSchoolNameAndLogo($data);
		}
		$data['school_name'] = $this->school_name;
		$data['school_logo'] = $this->school_logo;

		$data['currentPage'] = 'admin';
		$data['title'] = 'Messages';
		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		return View('admin.messaging', $data);
	}

	private function setSchoolNameAndLogo($data){
		$college = College::find($data['org_school_id']);
		$this->school_name = $college->school_name;
		$this->school_logo = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;
	}

    private function getLast24HoursCalledUserIds($org_branch_id) {
        $date = new \DateTime();
        $date->modify('-24 hours');

        $phone_date = $date->format('Y-m-d H:i:s');

        $phone_logs = PhoneLog::select('receiver_user_id as user_id')
                              ->where('org_branch_id', '=', $org_branch_id)
                              ->where('created_at', '>', $phone_date)
                              ->where('status', '=', 'completed')
                              ->pluck('user_id');

        return $phone_logs->toArray();
    }

	private function loadInquirieAdmin($data, $page =null, $is_ajax = null, $input = NULL, $filter_audience = NULL){
        // Get last 24 hours worth of phone-logs
        if (isset($data['org_branch_id'])) {
            $data['auto_dialer_black_list'] = $this->getLast24HoursCalledUserIds($data['org_branch_id']);
        }

        // get premium status
        $data['is_admin_premium'] = $this->validateAdminPremium();

        // Only inquiries or removed as free services.
        if ($page !== null && $page !== 'removed' && $page !== 'converted') {
            if (!$data['is_admin_premium']) {
                return redirect( '/admin/premium-plan-request' );
            }
        }

		// FILTER AUDIENCE DATA BEGINS HERE
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

        $states = $states;
        $cities = array('' => 'Select state first' );

        //get departments
        if (Cache::has(env('ENVIRONMENT') .'_all_dep_name')) {
            $dep_name = Cache::get(env('ENVIRONMENT') .'_all_dep_name');
        }else{
            $dep = new Department;
            $dep_name = $dep->getAllDepartments();
            Cache::put(env('ENVIRONMENT') .'_all_dep_name', $dep_name, 120);
        }
        $departments = $dep_name;
        $majors = array('' => 'Select department first' );

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

		// FILTER AUDIENCE DATA ENDS HERE

        // ******************* REMOVED INTENTIALLAY ON MAY 8, 2018
		//left hand side numbers
		// if (!isset($is_ajax)) {
		// 	$this->leftBarNumbers($data);
		// }

		// If user has a department set, show results based on the filters they have set
		// $crf = new CollegeRecommendationFilters;
		// $filter_qry = $crf->generateFilterQry($data);
		$filter_qry_bool = 0;
		// end of department set

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

        // end of commonly used filters

		if($this->school_name == '' || $this->school_logo == ''){
			$this->setSchoolNameAndLogo($data);
		}
		$data['school_name'] = $this->school_name;
		$data['school_logo'] = $this->school_logo;

		$data['inquiry_list'] = array();

		// Expires recommendation time.

		$data['expiresIn'] = $this->setRecommendationExpirationDate();

		if( $data['is_agency'] == 0 ){
            $data['topnav'] = 'topnav';
            $data['adminType'] = 'admin';
        }else{
            $data['topnav'] = 'agencyTopNav';
            $data['adminType'] = 'agency';
        }

		/*
		$yesterday_cron_date = Carbon::now()->yesterday();
		$yesterday_cron_date = $yesterday_cron_date->modify(self::CRONTIME);

		print_r($tomorrow_cron_date);
		print_r($yesterday_cron_date);

		exit();
		*/
		if(isset($page) && $page == "prescreened"){

			$data['currentPage'] = 'admin-prescreened';

			$recruitMeList =  DB::connection('rds1')->table('prescreened_users as r')
								->join('users as u', 'u.id', '=', 'r.user_id')
								->leftjoin('scores as s', 's.user_id', '=','r.user_id')
								->leftjoin('objectives as o', 'o.user_id', '=', 'r.user_id')
								->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
								->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
								->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
								->leftjoin('transcript as tpt', 'tpt.user_id', '=', 'u.id')
								->leftjoin('notification_topnavs as nt', function($join)
								 {
								    $join->on('nt.type_id', '=' , 'u.id');
								    $join->on('nt.type', '=', DB::raw("'user'"));
									$join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));

								 })

								->select('r.*',
									'u.fname', 'u.lname', 'u.phone', 'u.in_college', 'u.id as user_id', 'u.txt_opt_in as userTxt_opt_in',
									's.overall_gpa' , 's.hs_gpa', 
									'co.country_code', 'co.country_name',
									'dt.display_name as degree_name',
									'dt.initials as degree_initials',
									DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
									'nt.id as is_notified',
									'tpt.id',
									'r.id as rid',
									'r.applied as prescreened_applied', 
									'r.enrolled as prescreened_enrolled')

								->where('r.college_id', '=', $data['org_school_id'])
								->where('r.active', 1)
								->whereNotIn('r.user_id', function($query) use ($data) {
									$query = $query->select('rva.user_id')
												   ->from('recruitment_verified_apps as rva')
												   ->where('rva.college_id', '=', $data['org_school_id']);

									if (isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])) {
										$query->where('rva.org_portal_id', $data['default_organization_portal']->id);
									}

									$query->get();
								});

			// Pre Screened order by custom questions
			$recruitMeList = $recruitMeList->leftjoin('users_custom_questions as ucq', 'ucq.user_id', '=', 'u.id')
												   ->leftjoin('colleges_applications_states as cas', 'cas.name', '=', 'ucq.application_state');

			// AOR : if the user is AOR, we only want to show the AOR students
			if (isset($data['aor_id'])) {
				$recruitMeList = $recruitMeList->where('r.aor_id', $data['aor_id']);

			}else{
				$recruitMeList = $recruitMeList->whereNull('r.aor_id');
			}
			// End of AOR

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
			// End add filter audience

			// Org portal id begins here
			if(isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])){
				$recruitMeList = $recruitMeList->where('r.org_portal_id', $data['default_organization_portal']->id);
			}
			// Org Portal id ends here

			if (isset($data['aor_portal_id'])) {
				$recruitMeList = $recruitMeList->where('r.aor_portal_id', $data['aor_portal_id']);
			}

			// order by user id that has been searched, place that person on top
			if (isset($input['uid'])) {
				try {
					$input['uid'] = Crypt::decrypt($input['uid']);
					$recruitMeList = $recruitMeList->orderByRaw("u.id = ".$input['uid']." DESC");	
				} catch (\Exception $e) {
					
				}
			}

			$recuitMeList = $recruitMeList->orderBy(DB::raw('ISNULL(cas.id), cas.id'), 'DESC');

			// default order is created_at and type
			if (!isset($data['column_orders'])) {
				$recruitMeList = $recruitMeList->orderBy('r.created_at', 'desc');
			} else {
				// Sort columns based on the inputs
				if ($data['column_orders']['orderBy'] == 'name') {
					$recruitMeList = $recruitMeList->orderBy(DB::raw('TRIM(fname)'), $data['column_orders']['sortBy'])
												   ->orderBy(DB::raw('TRIM(lname)'), $data['column_orders']['sortBy']);
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
					$recruitMeList = $recruitMeList->orderBy('r.created_at', $data['column_orders']['sortBy']);
				}
			}

			// Begin of filter query
			$cached_input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_prescreened_input');
			$input = array_merge( (array) $cached_input, $input );
			Session::put(env('ENVIRONMENT') .'_'.'AdminController_prescreened_input', $input);

			$total_results = $recruitMeList->distinct('u.id')->count('u.id');
			$data['total_results'] = $total_results;

			// var_dump($data['total_results']);
			$recruitMeList= $recruitMeList->groupBy('u.id');

			if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id'])) {
				$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id']);
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

			Cache::put(env('ENVIRONMENT') .'_'.'AdminController_prescreened_'.$data['user_id'], $obj, 60);

			$recruitMeList = $recruitMeList->take($obj['take'])
										   ->skip($obj['skip']);
		}elseif(isset($page) && $page == "recommendations"){

			$data['currentPage'] = 'admin-recommendations';

			$recruitMeList =  DB::connection('rds1')->table('college_recommendations as r')

								->join('users as u', 'u.id', '=', 'r.user_id')
								->leftjoin('scores as s', 's.user_id', '=','r.user_id')
								->leftjoin('objectives as o', 'o.user_id', '=', 'r.user_id')
								->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
								->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
								->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
								->leftjoin('transcript as tpt', 'tpt.user_id', '=', 'u.id')
								->leftjoin('notification_topnavs as nt', function($join)
								 {
								    $join->on('nt.type_id', '=' , 'u.id');
								    $join->on('nt.type', '=', DB::raw("'user'"));
									$join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));

								 })

								->select('r.*',
									'u.fname', 'u.lname', 'u.in_college', 'u.id as user_id', 'u.txt_opt_in as userTxt_opt_in',
									's.overall_gpa' , 's.hs_gpa', 
									'co.country_code', 'co.country_name',
									'dt.display_name as degree_name',
									'dt.initials as degree_initials',
									DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
									'nt.id as is_notified',
									'tpt.id')
								->where('r.college_id', '=', $data['org_school_id'])
								->where('r.created_at', '>=', $this->min_cron_date)
								->where('r.created_at', '<=', $this->max_cron_date)
								->where('r.active', '!=', 0);


			// AOR : if the user is AOR, we only want to show the AOR students
			if (isset($data['aor_id'])) {
				$recruitMeList = $recruitMeList->where('r.aor_id', $data['aor_id']);

			}else{
				$recruitMeList = $recruitMeList->whereNull('r.aor_id');
			}
			// End of AOR

			// Add Filter Audience
			if (isset($filter_audience)) {

				$arrObj = $this->convertInputsForFilters($filter_audience);
				$filter_audience_crf = new CollegeRecommendationFilters;
				$filter_audience_qry = $filter_audience_crf->globalMethodGenerateFilterQry($arrObj, true);

				$filter_audience_qry = $filter_audience_qry->select('userFilter.id as filterUserId');
				$tmp_qry = $this->getRawSqlWithBindings($filter_audience_qry);
				$recruitMeList = $recruitMeList->join(DB::raw('('.$tmp_qry.')  as t1'), 't1.filterUserId' , '=', 'u.id');
			}
			// End add filter audience

			if(isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])){
				$recruitMeList = $recruitMeList->where('r.org_portal_id', $data['default_organization_portal']->id);
			}else{
				$recruitMeList = $recruitMeList->whereNull('r.org_portal_id');
			}

			// order by user id that has been searched, place that person on top
			if (isset($input['uid'])) {
				try {
					$input['uid'] = Crypt::decrypt($input['uid']);
					$recruitMeList = $recruitMeList->orderByRaw("u.id = ".$input['uid']." DESC");	
				} catch (\Exception $e) {
					
				}
			}

			// default order is created_at and type
			if (!isset($data['column_orders'])) {
				$recruitMeList = $recruitMeList->orderBy('r.created_at', 'desc')
									           ->orderBy('r.type', 'asc');
			} else {
				// Sort columns based on the inputs
				if ($data['column_orders']['orderBy'] == 'name') {
					$recruitMeList = $recruitMeList->orderBy(DB::raw('TRIM(fname)'), $data['column_orders']['sortBy'])
												   ->orderBy(DB::raw('TRIM(lname)'), $data['column_orders']['sortBy']);
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
					$recruitMeList = $recruitMeList->orderBy('r.created_at', $data['column_orders']['sortBy']);
				}
				if ($data['column_orders']['orderBy'] == 'applied') {
					$recruitMeList = $recruitMeList->orderBy('r.applied', $data['column_orders']['sortBy']);
				}
				if ($data['column_orders']['orderBy'] == 'enrolled') {
					$recruitMeList = $recruitMeList->orderBy('r.enrolled', $data['column_orders']['sortBy']);
				}
			}

			// Begin of filter query
			$cached_input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_recommendations_input');
			$input = array_merge($cached_input, $input);
			Session::put(env('ENVIRONMENT') .'_'.'AdminController_recommendations_input', $input);

			$total_results = $recruitMeList->distinct('u.id')->count('u.id');
			$data['total_results'] = $total_results;

			// var_dump($data['total_results']);
			$recruitMeList= $recruitMeList->groupBy('u.id');

			if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id'])) {
				$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id']);
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

			Cache::put(env('ENVIRONMENT') .'_'.'AdminController_recommendations_'.$data['user_id'], $obj, 60);

			$recruitMeList = $recruitMeList->take($obj['take'])
										   ->skip($obj['skip']);
		}else{
			$recruitMeList =  DB::connection('rds1')->table('recruitment as r')

								->join('users as u', 'u.id', '=', 'r.user_id')
								->leftjoin('scores as s', 's.user_id', '=','r.user_id')
								->leftjoin('objectives as o', 'o.user_id', '=', 'r.user_id')
								->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
								->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
								->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
								->leftjoin('transcript as tpt', 'tpt.user_id', '=', 'u.id')
								->leftjoin('notification_topnavs as nt', function($join)
								 {
								    $join->on('nt.type_id', '=' , 'u.id');
								    $join->on('nt.type', '=', DB::raw("'user'"));
									$join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));

								 })

								->select('r.*',
									'u.fname', 'u.lname', 'u.phone', 'u.in_college', 'u.id as user_id', 'u.txt_opt_in as userTxt_opt_in', 'u.profile_percent',
									's.overall_gpa' , 's.hs_gpa', 
									'co.country_code', 'co.country_name',
									'dt.display_name as degree_name',
									'dt.initials as degree_initials',
									DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
									'nt.id as is_notified',
									'tpt.id')

								->where('r.college_id', '=', $data['org_school_id']);

			// Add Filter Audience
			//$filter_audience currently contains query pieces used to filter
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
			// End add filter audience

			// AOR : if the user is AOR, we only want to show the AOR students
			if (isset($data['aor_id'])) {
				$recruitMeList = $recruitMeList->where('r.aor_id',$data['aor_id']);
			}else{
				$recruitMeList = $recruitMeList->whereNull('r.aor_id');
			}
			// End of AOR

			// order by user id that has been searched, place that person on top
			if (isset($input['uid'])) {
				try {
					$input['uid'] = Crypt::decrypt($input['uid']);
					$recruitMeList = $recruitMeList->orderByRaw("u.id = ".$input['uid']." DESC");	
				} catch (\Exception $e) {
					
				}
			}

			if (!isset($data['column_orders'])) {
				if (isset($page) && $page == "converted") {
					# Dont do anything, we will be doing the orderby in converted section
				}else{
					if (isset($data['default_organization_portal']->ro_id) && 
						$data['default_organization_portal']->ro_id == 13) {
						# Dont do anything
					}else{
						$recruitMeList = $recruitMeList->orderBy('r.updated_at', 'desc');	
					}
				}
			} else {
				// Sort columns based on the inputs
				if ($data['column_orders']['orderBy'] == 'name') {
					$recruitMeList = $recruitMeList->orderBy(DB::raw('TRIM(fname)'), $data['column_orders']['sortBy'])
												   ->orderBy(DB::raw('TRIM(lname)'), $data['column_orders']['sortBy']);
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
					
					if (isset($page) && $page == "converted") {
						$recruitMeList = $recruitMeList->orderBy('r.updated_at', $data['column_orders']['sortBy']);
					}else{
						$recruitMeList = $recruitMeList->orderBy('r.updated_at', 'desc');	
					}

				}
				if ($data['column_orders']['orderBy'] == 'applied') {
					$recruitMeList = $recruitMeList->orderBy('r.applied', $data['column_orders']['sortBy']);
				}
				if ($data['column_orders']['orderBy'] == 'enrolled') {
					$recruitMeList = $recruitMeList->orderBy('r.enrolled', $data['column_orders']['sortBy']);
				}
			}

			if(isset($page)){
				if($page == "pending"){
					$data['currentPage'] = 'admin-pending';
					$recruitMeList = $recruitMeList->where('r.status', 1)
												   ->where('r.college_recruit', 1)
												   ->where('r.user_recruit', 0);

					// Begin of department query
					if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])) {

						$recruitMeList = $recruitMeList->join('recruitment_tags as rts', function($q) use ($data){
													$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
													$q->on('rts.user_id', '=', 'u.id');

													if (isset($data['aor_id'])) {
														$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

													}else{
														$q->whereNull('rts.aor_id')
														  ->whereNull('rts.aor_portal_id')
														  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
													}
						});

						$total_results = $recruitMeList->distinct('u.id')->count('u.id');

						$filter_qry_bool = 1;
					}else{
						$total_results = $recruitMeList->distinct('u.id')->count('u.id');
					}
					// End of department query set

					$data['total_results'] = $total_results;

					$current_viewing = $recruitMeList->distinct('u.id')->count('u.id');

					$recruitMeList = $recruitMeList->groupBy('u.id');

					if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id'])) {
						$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id']);
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

					if($data['current_viewing'] > $current_viewing)
                        $data['current_viewing'] = $current_viewing;

					Cache::put(env('ENVIRONMENT') .'_'.'AdminController_pending_'.$data['user_id'], $obj, 60);

					$recruitMeList = $recruitMeList->take($obj['take'])
												   ->skip($obj['skip']);
				}
				if($page == "approved"){
					$data['currentPage'] = 'admin-approved';

					$recruitMeList = $recruitMeList->where('r.status', 1)
												   ->where('r.college_recruit', 1)
												   ->where('r.user_recruit', 1);

					// Begin of department query
					if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])) {

						$recruitMeList = $recruitMeList->join('recruitment_tags as rts', function($q) use ($data){
													$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
													$q->on('rts.user_id', '=', 'u.id');

													if (isset($data['aor_id'])) {
														$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

													}else{
														$q->whereNull('rts.aor_id')
														  ->whereNull('rts.aor_portal_id')
														  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
													}
						});

						$total_results = $recruitMeList->distinct('u.id')->count('u.id');

						$filter_qry_bool = 1;
					}else{
						$total_results = $recruitMeList->distinct('u.id')->count('u.id');
					}
					// End of department query set

					$data['total_results'] = $total_results;

					$current_viewing = $recruitMeList->distinct('u.id')->count('u.id');

					$recruitMeList = $recruitMeList->groupBy('u.id');

					if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id'])) {
						$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id']);
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

					if($data['current_viewing'] > $current_viewing)
						$data['current_viewing'] = $current_viewing;

					Cache::put(env('ENVIRONMENT') .'_'.'AdminController_approved_'.$data['user_id'], $obj, 60);

					$recruitMeList = $recruitMeList->take($obj['take'])
												   ->skip($obj['skip']);

					// Add handshake dollar sign ( this is only for a college that is in hs contract )
					$recruitMeList = $recruitMeList->leftjoin('college_paid_handshake_logs as cphl', 'cphl.recruitment_id', '=', 'r.id')
												   ->addSelect('cphl.id as is_handshake_paid');
					//end handshake dollar sign
				}
				if($page == "removed"){
					$data['currentPage'] = 'admin-removed';
					$recruitMeList = $recruitMeList->where('r.status', 0)
												   ->where('r.college_recruit', '<', 9)
												   ->where('r.user_recruit', '<', 9);

					$cached_input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_removed_input');
					$input = array_merge($cached_input, $input);
					Session::put(env('ENVIRONMENT') .'_'.'AdminController_removed_input', $input);

					// Begin of department query
					if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])) {

						$recruitMeList = $recruitMeList->join('recruitment_tags as rts', function($q) use ($data){
													$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
													$q->on('rts.user_id', '=', 'u.id');

													if (isset($data['aor_id'])) {
														$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

													}else{
														$q->whereNull('rts.aor_id')
														  ->whereNull('rts.aor_portal_id')
														  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
													}
						});

						$total_results = $recruitMeList->distinct('u.id')->count('u.id');

						$filter_qry_bool = 1;
					}else{
						$total_results = $recruitMeList->distinct('u.id')->count('u.id');
					}
					// End of department query set

					$data['total_results'] = $total_results;

					$recruitMeList = $recruitMeList->groupBy('u.id');

					if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id'])) {
						$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id']);
						$obj['skip'] += $obj['take'];
						$obj['take'] = isset($data['display_option'])? $data['display_option'] : 15;
					}else{
						$obj = array();
						$obj['take'] = isset($data['display_option'])? $data['display_option'] :15;
						$obj['skip'] = 0;
					}

					$data['current_viewing'] = $obj['skip'] + $obj['take'];

					if($data['current_viewing'] > $data['total_results'])
						$data['current_viewing'] = $data['total_results'];

					Cache::put(env('ENVIRONMENT') .'_'.'AdminController_removed_'.$data['user_id'], $obj, 60);

					$recruitMeList = $recruitMeList->take($obj['take'])
												   ->skip($obj['skip']);
				}
				if($page == "rejected"){
					$data['currentPage'] = 'admin-rejected';
					$recruitMeList = $recruitMeList->where('r.status', 1)
												   ->where('r.college_recruit', -1)
												   ->where('r.user_recruit', 1);

					$cached_input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_rejected_input');
					$input = array_merge($cached_input, $input);
					Session::put(env('ENVIRONMENT') .'_'.'AdminController_rejected_input', $input);

					// Begin of department query
					if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])) {

						$recruitMeList = $recruitMeList->join('recruitment_tags as rts', function($q) use ($data){
													$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
													$q->on('rts.user_id', '=', 'u.id');

													if (isset($data['aor_id'])) {
														$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

													}else{
														$q->whereNull('rts.aor_id')
														  ->whereNull('rts.aor_portal_id')
														  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
													}
						});

						$total_results = $recruitMeList->distinct('u.id')->count('u.id');

						$filter_qry_bool = 1;
					}else{
						$total_results = $recruitMeList->distinct('u.id')->count('u.id');
					}
					// End of department query set

					$data['total_results'] = $total_results;

					$recruitMeList = $recruitMeList->groupBy('u.id');

					if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id'])) {
						$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id']);
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

					Cache::put(env('ENVIRONMENT') .'_'.'AdminController_rejected_'.$data['user_id'], $obj, 60);

					$recruitMeList = $recruitMeList->take($obj['take'])
												   ->skip($obj['skip']);
				}
				if($page == "verifiedHs"){
					$data['currentPage'] = 'admin-verifiedHs';
					$recruitMeList = $recruitMeList->join('recruitment_verified_hs as rvh', function($q) use ($data){
													$q->on('r.college_id', '=', 'rvh.college_id');
													$q->on('r.user_id', '=', 'rvh.user_id');
													if (isset($data['aor_id'])) {
														$q->where('rvh.aor_id', '=', DB::raw($data['aor_id']));

													}else{
														$q->whereNull('rvh.aor_id');
													}
													if (isset($data['aor_portal_id'])) {
														$q->where('rvh.aor_portal_id', '=', DB::raw($data['aor_portal_id']));

													}

													if (isset($data['default_organization_portal'])) {
														$q->where('rvh.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));

													}
					});

					// Begin of department query
					if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])) {

						// $recruitMeList = $recruitMeList->join('recruitment_tags as rts', function($q) use ($data){
						// 							$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
						// 							$q->on('rts.user_id', '=', 'u.id');

						// 							if (isset($data['aor_id'])) {
						// 								$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

						// 							}else{
						// 								$q->whereNull('rts.aor_id')
						// 								  ->whereNull('rts.aor_portal_id')
						// 								  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
						// 							}
						// });

						$total_results = $recruitMeList->distinct('u.id')->count('u.id');

						$filter_qry_bool = 1;
					}else{
						$total_results = $recruitMeList->distinct('u.id')->count('u.id');
					}
					// End of department query sets

					$data['total_results'] = $total_results;

					$current_viewing = $recruitMeList->distinct('u.id')->count('u.id');

					$recruitMeList = $recruitMeList->groupBy('u.id');

					if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id'])) {
						$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id']);
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

					if($data['current_viewing'] > $current_viewing)
                        $data['current_viewing'] = $current_viewing;

					Cache::put(env('ENVIRONMENT') .'_'.'AdminController_verifiedHs_'.$data['user_id'], $obj, 60);

					$recruitMeList = $recruitMeList->take($obj['take'])
												   ->skip($obj['skip']);
				}
				if($page == "verifiedApp"){
					$data['currentPage'] = 'admin-verifiedApp';
					$recruitMeList = $recruitMeList->join('recruitment_verified_apps as rva', function($q) use ($data){
													$q->on('r.college_id', '=', 'rva.college_id');
													$q->on('r.user_id', '=', 'rva.user_id');
													if (isset($data['aor_id'])) {
														$q->where('rva.aor_id', '=', $data['aor_id']);

													}else{
														$q->whereNull('rva.aor_id');
													}
													if (isset($data['aor_portal_id'])) {
														$q->where('rva.aor_portal_id', '=', $data['aor_portal_id']);

													}
													if (isset($data['default_organization_portal'])) {
														$q->where('rva.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));

													}
					});

					// Begin of department query
					if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])) {

						// $recruitMeList = $recruitMeList->join('recruitment_tags as rts', function($q) use ($data){
						// 							$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
						// 							$q->on('rts.user_id', '=', 'u.id');

						// 							if (isset($data['aor_id'])) {
						// 								$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

						// 							}else{
						// 								$q->whereNull('rts.aor_id')
						// 								  ->whereNull('rts.aor_portal_id')
						// 								  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
						// 							}
						// });

						$total_results = $recruitMeList->distinct('u.id')->count('u.id');

						$filter_qry_bool = 1;
					}else{
						$total_results = $recruitMeList->distinct('u.id')->count('u.id');
					}
					// End of department query set
					$data['total_results'] = $total_results;

					$current_viewing = $recruitMeList->distinct('u.id')->count('u.id');

					$recruitMeList = $recruitMeList->groupBy('u.id');

					if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id'])) {
						$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id']);
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

					if($data['current_viewing'] > $current_viewing)
                        $data['current_viewing'] = $current_viewing;

					Cache::put(env('ENVIRONMENT') .'_'.'AdminController_verifiedApp_'.$data['user_id'], $obj, 60);

					$recruitMeList = $recruitMeList->take($obj['take'])
												   ->skip($obj['skip']);

					// Pre Screened order by custom questions
					$recruitMeList = $recruitMeList->leftjoin('users_custom_questions as ucq', 'ucq.user_id', '=', 'u.id')
												   ->leftjoin('colleges_applications_states as cas', 'cas.name', '=', 'ucq.application_state')
												   ->orderBy(DB::raw('ISNULL(cas.id), cas.id'), 'DESC');
				}
				if ($page == "converted") {
					$data['currentPage'] = 'admin-converted';
					$recruitMeList = NULL;
					$raw_qry = DB::connection('rds1')->table('recruitment as r')
												 ->select("r.*");

					if (isset($data['default_organization_portal']->ro_type)) {
						
						$raw_qry = $raw_qry->addSelect(DB::raw("IFNULL(`ac`.`updated_at`, r.updated_at) as `ad_clicks_updated_at`"));
						$raw_qry = $raw_qry->join('recruitment_revenue_org_relations as rror', function($q) use($data){
											$q->on('r.college_id', '=', DB::raw($data['org_school_id']));
    										$q->whereNull('r.aor_id');
											// $q->on('rror.rec_id', '=', 'r.id');
											$q->on('rror.ro_id', '=', DB::raw($data['default_organization_portal']->ro_id));
											$q->on('r.ro_id', '=', DB::raw($data['default_organization_portal']->ro_id));
											$q->on('r.college_recruit', '=', DB::raw(0));
											$q->on('r.user_recruit', '=', DB::raw(0));
											$q->on('r.status', '=', DB::raw(1));
						});

						switch ($data['default_organization_portal']->ro_type) {
							case 'post':
								$raw_qry = $raw_qry->join('distribution_responses as ac', 'ac.id', '=', 'rror.related_id');
								break;

							case 'click':
								$raw_qry = $raw_qry->leftjoin('ad_clicks as ac', 'ac.id', '=', 'rror.related_id');
								break;
							
							default:
								# code...
								break;
						}

					}else{
                        if (!isset($data['default_organization_portal']) && isset($data['organization_portals'][0]) && isset($data['organization_portals'][0]->ro_type) && isset($data['organization_portals'][0]->ro_id)) {

                            $raw_qry = $raw_qry->addSelect(DB::raw("IFNULL(`ac`.`updated_at`, r.updated_at) as `ad_clicks_updated_at`"), 'nt.id as is_notified');
                            $raw_qry = $raw_qry->leftjoin('recruitment_revenue_org_relations as rror', 'rror.rec_id', '=', 'r.id')
                                               ->leftjoin('notification_topnavs as nt', function($join) {
                                                    $join->on('nt.type_id', '=' , 'r.user_id');
                                                    $join->on('nt.type', '=', DB::raw("'user'"));
                                                    $join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));
                                               });

                            switch ($data['organization_portals'][0]->ro_type) {
                                case 'post':
                                    $raw_qry = $raw_qry->leftjoin('distribution_responses as ac', 'ac.id', '=', 'rror.related_id');
                                    break;

                                case 'click':
                                    $raw_qry = $raw_qry->leftjoin('ad_clicks as ac', 'ac.id', '=', 'rror.related_id');
                                    break;
                                
                                default:
                                    # code...
                                    break;
                            }

                            $raw_qry = $raw_qry->where(function($qry) use ($data) {
                                $qry->where('r.college_id', '=', DB::raw($data['org_school_id']))
                                    ->orWhere('r.ro_id', '=', DB::raw($data['organization_portals'][0]->ro_id));
                            })
                            ->whereNull('r.aor_id')
                            ->where('r.college_recruit', '=', DB::raw(0))
                            ->where('r.user_recruit', '=', DB::raw(0))
                            ->where('r.status', '=', DB::raw(1));


                        } else {
                           $raw_qry = $raw_qry->where('r.college_recruit', 0)
                                               ->join('recruitment_revenue_org_relations as rror', 'rror.rec_id', '=', 'r.id')
                                               ->whereNull('rror.related_id')
                                               ->whereNull('rror.ro_id')
                                               ->where('r.user_recruit', 0)
                                               ->where('r.status', 1);
                       }
					}

					$cached_input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_input');
					
					if (isset($cached_input) && !empty($cached_input)) {
						$input = array_merge($cached_input, $input);
					}
					
					Session::put(env('ENVIRONMENT') .'_'.'AdminController_inquiries_input', $input);

					// Begin of department query
					if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])) {

						$raw_qry = $raw_qry->join('recruitment_tags as rts', function($q) use ($data){
														$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
														$q->on('rts.user_id', '=', 'r.user_id');

														if (isset($data['aor_id'])) {
															$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

														}else{
															$q->whereNull('rts.aor_id')
															  ->whereNull('rts.aor_portal_id')
															  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
														}
													});

						
						
						$filter_qry_bool = 1;
					}
					// End of department query set

					// Converted query
					$recruitMeList = DB::connection('rds1')->table(DB::raw("(".$this->getRawSqlWithBindings($raw_qry).") `filter_table`"))
													->join('recruitment_converts as rc', 'rc.rec_id', '=', 'filter_table.id')
												    ->join('users as u', 'u.id', '=', 'filter_table.user_id')
													->leftjoin('scores as s', 's.user_id', '=','filter_table.user_id')
													->leftjoin('objectives as o', 'o.user_id', '=', 'filter_table.user_id')
													->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
													->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
													->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
													->leftjoin('transcript as tpt', 'tpt.user_id', '=', 'filter_table.user_id')
													// ->leftjoin('notification_topnavs as nt', function($join)
													//  {
													//     $join->on('nt.type_id', '=' , 'filter_table.user_id');
													//     $join->on('nt.type', '=', DB::raw("'user'"));
													// 	$join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));

													//  })

                                                    ->leftjoin('phone_log as pl', 'pl.receiver_user_id', '=', 'u.id')

													->select('filter_table.*',
														'u.fname', 'u.lname', 'u.phone', 'u.in_college', 'u.id as user_id', 'u.txt_opt_in as userTxt_opt_in', 'u.profile_percent',
														's.overall_gpa' , 's.hs_gpa', 
														'co.country_code', 'co.country_name',
														'dt.display_name as degree_name',
														'dt.initials as degree_initials',
														DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
														// 'nt.id as is_notified',
														'tpt.id');
													
                    if (isset($data['default_organization_portal']->ro_type)) {
                        $recruitMeList = $recruitMeList->orderBy('ac.updated_at', 'DESC');
                    }

					// Add Filter Audience
					//$filter_audience currently contains query pieces used to filter
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
						$recruitMeList = $recruitMeList->join(DB::raw('('.$tmp_qry.')  as t1'), 't1.filterUserId' , '=', 'filter_table.user_id');
					}
					// End add filter audience

					// if we have ro_id run the following
					if (isset($data['default_organization_portal']->ro_type)) {
						switch ($data['default_organization_portal']->ro_type) {
							case 'post':
								$recruitMeList = $recruitMeList->join('distribution_responses as ac', 'ac.id', '=', 'rc.converted_id');
								break;

							case 'click':
								$recruitMeList = $recruitMeList->leftjoin('ad_clicks as ac', 'ac.id', '=', 'rc.converted_id');
								break;
							
							default:
								# code...
								break;
						}
						$recruitMeList = $recruitMeList->join('recruitment_revenue_org_relations as rror', function($q){
															$q->on('rror.rec_id', '=', 'filter_table.id');
															$q->where(function($q2){
																$q2->orWhere('rror.related_id', '=', 'ac.id')
																   ->orWhereNull('rror.related_id');
															});
															
						});
						
						$recruitMeList = $recruitMeList->where('rror.ro_id', $data['default_organization_portal']->ro_id);
					}

					$total_results = $recruitMeList->distinct('u.id')->count('u.id');

					$data['total_results'] = $total_results;

					$current_viewing = $recruitMeList->distinct('u.id')->count('u.id');

					$recruitMeList = $recruitMeList->groupBy('u.id');

					if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id'])) {
						$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
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

	               	if($data['current_viewing'] > $current_viewing)
						$data['current_viewing'] = $current_viewing;

					Cache::put(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id'], $obj, 60);

					$recruitMeList = $recruitMeList->take($obj['take'])
												   ->skip($obj['skip']);
					}
			}else{
				$data['currentPage'] = 'admin-inquiries';
				$recruitMeList = NULL;
				$raw_qry = DB::connection('rds1')->table('recruitment as r')
												 ->select("r.*");

				if (isset($data['default_organization_portal']->ro_type)) {
                    $raw_qry = $raw_qry->addSelect(DB::raw("IFNULL(`ac`.`updated_at`, r.updated_at) as `ad_clicks_updated_at`"), 'nt.id as is_notified');
                    $raw_qry = $raw_qry->leftjoin('recruitment_revenue_org_relations as rror', 'rror.rec_id', '=', 'r.id')
                                       ->leftjoin('notification_topnavs as nt', function($join) {
                                            $join->on('nt.type_id', '=' , 'r.user_id');
                                            $join->on('nt.type', '=', DB::raw("'user'"));
                                            $join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));
                                       });

					switch ($data['default_organization_portal']->ro_type) {
						case 'post':
							$raw_qry = $raw_qry->leftjoin('distribution_responses as ac', 'ac.id', '=', 'rror.related_id');
							break;

						case 'click':
							$raw_qry = $raw_qry->leftjoin('ad_clicks as ac', 'ac.id', '=', 'rror.related_id');
							break;
						
						default:
							# code...
							break;
					}

                    $data['ro_query'] = DB::connection('rds1')->table('revenue_organizations as ro')->select('school_id')->where('ro.id', '=', $data['default_organization_portal']->ro_id)->join('organization_branches as ob', 'ob.id', '=', 'ro.org_branch_id')->whereNotNull('ro.org_branch_id')->first();

                    $raw_qry = $raw_qry->where(function($qry) use ($data) {
                        if (isset($data['ro_query']) && isset($data['ro_query']->school_id)) {
                            $qry->where('r.college_id', '=', DB::raw($data['ro_query']->school_id))
                                ->orWhere('r.ro_id', '=', DB::raw($data['default_organization_portal']->ro_id));

                        } else {
                            $qry->where('r.ro_id', '=', DB::raw($data['default_organization_portal']->ro_id));

                        }

                    })
                    ->whereNull('r.aor_id')
                    ->where('r.college_recruit', '=', DB::raw(0))
                    ->where('r.user_recruit', '=', DB::raw(1))
                    ->where('r.status', '=', DB::raw(1));

				} else {
                    // For colleges that also have ad_clicks
                    if (!isset($data['default_organization_portal']) && isset($data['organization_portals'][0]) && isset($data['organization_portals'][0]->ro_type) && isset($data['organization_portals'][0]->ro_id)) {

                        $raw_qry = $raw_qry->addSelect(DB::raw("IFNULL(`ac`.`updated_at`, r.updated_at) as `ad_clicks_updated_at`"), 'nt.id as is_notified');
                        $raw_qry = $raw_qry->leftjoin('recruitment_revenue_org_relations as rror', 'rror.rec_id', '=', 'r.id')
                                           ->leftjoin('notification_topnavs as nt', function($join) {
                                                $join->on('nt.type_id', '=' , 'r.user_id');
                                                $join->on('nt.type', '=', DB::raw("'user'"));
                                                $join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));
                                           });

                        switch ($data['organization_portals'][0]->ro_type) {
                            case 'post':
                                $raw_qry = $raw_qry->leftjoin('distribution_responses as ac', 'ac.id', '=', 'rror.related_id');
                                break;

                            case 'click':
                                $raw_qry = $raw_qry->leftjoin('ad_clicks as ac', 'ac.id', '=', 'rror.related_id');
                                break;
                            
                            default:
                                # code...
                                break;
                        }

                        $raw_qry = $raw_qry->where(function($qry) use ($data) {
                                                $qry->where('r.college_id', '=', DB::raw($data['org_school_id']))
                                                    ->orWhere('r.ro_id', '=', DB::raw($data['organization_portals'][0]->ro_id));
                                           })
                                           ->whereNull('r.aor_id')
                                           ->where('r.college_recruit', '=', DB::raw(0))
                                           ->where('r.user_recruit', '=', DB::raw(1))
                                           ->where('r.status', '=', DB::raw(1));

                    } else {             
					   $raw_qry = $raw_qry->where('r.college_id', '=', $data['org_school_id'])
                                          ->where('r.college_recruit', 0)
										  ->where('r.user_recruit', 1)
										  ->where('r.status', 1);
                   }
				}
				
				// Begin of department query
				if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])) {

					$raw_qry = $raw_qry->join('recruitment_tags as rts', function($q) use ($data){
                                                // $q->on('r.college_id', '=', DB::raw($data['org_school_id']));
												// $q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
												$q->on('rts.user_id', '=', 'r.user_id');
												$q->on('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));

												if (isset($data['aor_id'])) {
													$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

												}else if (!isset($data['ro_query'])) {
													$q->whereNull('rts.aor_id')
													  ->whereNull('rts.aor_portal_id')
													  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
												}
					});

					$filter_qry_bool = 1;
				}

				$recruitMeList = DB::connection('rds1')->table(DB::raw("(".$this->getRawSqlWithBindings($raw_qry).") `filter_table`"))
													    ->join('users as u', 'u.id', '=', 'filter_table.user_id')
														->leftjoin('scores as s', 's.user_id', '=','filter_table.user_id')
														->leftjoin('objectives as o', 'o.user_id', '=', 'filter_table.user_id')
														->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
														->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
														->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
														->leftjoin('transcript as tpt', 'tpt.user_id', '=', 'filter_table.user_id')
														// ->leftjoin('notification_topnavs as nt', function($join)
														//  {
														//     $join->on('nt.type_id', '=' , 'filter_table.user_id');
														//     $join->on('nt.type', '=', DB::raw("'user'"));
														// 	$join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));

														//  })

                                                        ->leftjoin('phone_log as pl', 'pl.receiver_user_id', '=', 'u.id')

														->select('filter_table.*',
															'u.fname', 'u.lname', 'u.phone', 'u.in_college', 'u.id as user_id', 'u.txt_opt_in as userTxt_opt_in', 'u.profile_percent',
															's.overall_gpa' , 's.hs_gpa', 
															'co.country_code', 'co.country_name',
															'dt.display_name as degree_name',
															'dt.initials as degree_initials',
															DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
															// 'nt.id as is_notified',
															'tpt.id');

				// Add Filter Audience
				//$filter_audience currently contains query pieces used to filter
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
					$recruitMeList = $recruitMeList->join(DB::raw('('.$tmp_qry.')  as t1'), 't1.filterUserId' , '=', 'filter_table.user_id');
				}
				// End add filter audience


				$cached_input = Session::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_input');
				$input = array_merge($cached_input, $input);
				Session::put(env('ENVIRONMENT') .'_'.'AdminController_inquiries_input', $input);

                $recruitMeList = $recruitMeList->orderBy('pl.id', 'ASC');

                // For colleges that also have ad_clicks
                if (!isset($data['default_organization_portal']) && isset($data['organization_portals'][0]) && isset($data['organization_portals'][0]->ro_type) && isset($data['organization_portals'][0]->ro_id)) {
                    $recruitMeList = $recruitMeList->orderBy('filter_table.id', 'DESC');
                }

				if (isset($data['default_organization_portal']->ro_type)) {
					
					if ($data['default_organization_portal']->ro_id == 13) {
						$recruitMeList = $recruitMeList->leftjoin('users_custom_questions as ucq', 'ucq.user_id', '=', 'u.id')
													   ->orderBy(DB::raw("if(ucq.have_sponsor = 'yes', 1, 2)"), 'ASC');
					}

					$recruitMeList = $recruitMeList->orderBy('filter_table.ad_clicks_updated_at', 'DESC');
				}
				// dd("herere");
				// $total_results = $recruitMeList->distinct('filter_table.user_id')->count('filter_table.user_id');
                $total_results = 0;
				// End of department query set

				$data['total_results'] = $total_results;

				// $current_viewing = $recruitMeList->distinct('filter_table.user_id')->count('filter_table.user_id');
				$current_viewing = 0;

				$recruitMeList = $recruitMeList->groupBy('u.id');

				if (Cache::has(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id'])) {
					$obj = Cache::get(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id']);
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

               	if($data['current_viewing'] > $current_viewing)
					$data['current_viewing'] = $current_viewing;

				Cache::put(env('ENVIRONMENT') .'_'.'AdminController_inquiries_'.$data['user_id'], $obj, 60);

				$recruitMeList = $recruitMeList->take($obj['take'])
											   ->skip($obj['skip']);
			}
		}

		// Begin of department query
		if ($filter_qry_bool == 0 && isset($data['default_organization_portal'])
			&& $data['currentPage'] != 'admin-recommendations' && $data['currentPage'] != 'admin-prescreened') {

			$recruitMeList = $recruitMeList->join('recruitment_tags as rts', function($q) use ($data){
										$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
										$q->on('rts.user_id', '=', 'u.id');

										if (isset($data['aor_id'])) {
											$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

										}else{
											$q->whereNull('rts.aor_id')
											  ->whereNull('rts.aor_portal_id')
											  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
										}
			});
		}
		// End of department query set
        if (isset($data['is_plexuss']) && $data['is_plexuss'] == 1) {
            $recruitMeList = $recruitMeList->whereNotNull('u.phone')
                                           ->where('u.phone', '!=', ' ');
        }
		// $recruitMeList = $recruitMeList->where('b', 1);
		$recruitMeList = $recruitMeList->addSelect(DB::raw("GROUP_CONCAT(
														DISTINCT tpt.doc_type
														ORDER BY
															tpt.id ASC SEPARATOR ', '
													) as transcript_arr "));

        if ($data['currentPage'] == 'admin-converted' || $data['currentPage'] == 'admin-inquiries') {
            $recruitMeList = $recruitMeList->addSelect(DB::raw("filter_table.id as recruitment_id"));
        }

		// $recruitMeList = $recruitMeList->leftjoin('admin_texts as at', 'at.org_branch_id', '=', DB::raw($data['org_branch_id']))
		// 							   ->addSelect('at.id as is_admin_text');

		// $recruitMeList = $recruitMeList->where('a', 1);
		$recruitMeList = $recruitMeList->get();

		// $adminText = AdminText::on('rds1')->where('org_branch_id', $data['org_branch_id'])->first();

		$is_admin_text_qry = AdminText::on('rds1')->where('org_branch_id', $data['org_branch_id'])->first();

		$user_id_arr = array();

		foreach ($recruitMeList as $key ) {
			$user_id_arr[] = $key->user_id;

			$tmp = array();

			// Add handshake dollar sign ( this is only for a college that is in hs contract )
			$tmp['is_handshake_paid'] = isset($key->is_handshake_paid) ? $key->is_handshake_paid : NULL;
			// end hanshake dollar sign

            $tmp['recruitment_id'] = isset($key->recruitment_id) ? $key->recruitment_id : NULL;

			$tmp['name'] = ucwords(strtolower($key->fname)) . ' '. ucwords(strtolower($key->lname));

            $tmp['type'] = isset($key->type) ? $key->type : NULL;

            isset($key->phone) ? $tmp['phone'] = $key->phone : NULL;

			$tmp['profile_percent'] = isset($key->profile_percent) ? $key->profile_percent : null;

			// Check to make sure the school have setup their text messages
			if (isset($is_admin_text_qry)) {
				$tmp['userTxt_opt_in'] = isset($key->userTxt_opt_in) ? $key->userTxt_opt_in : 0;
			}

			$in_college = $key->in_college;

			$tmp['in_college'] = $key->in_college;

            isset($key->phone) ? $tmp['phone'] = $key->phone : NULL;

			if($in_college){

				if(isset($key->overall_gpa)){
					$tmp['gpa'] = $key->overall_gpa;
				}else{
					$tmp['gpa'] = 'N/A';
				}

			}else{

				if(isset($key->hs_gpa)){
					$tmp['gpa'] = $key->hs_gpa;
				}else{
					$tmp['gpa'] = 'N/A';
				}
			}

			if($tmp['gpa'] == "0.00"){
				$tmp['gpa'] = "N/A";
			}

			if (isset($key->country_code)) {
				$tmp['country_code'] = $key->country_code;
				$tmp['country_name'] = $key->country_name;
			}else{
				$tmp['country_code'] = 'N/A';
				$tmp['country_name'] = 'N/A';
			}

			$tmp['student_user_id'] = $key->user_id;
			isset($key->rid) ? $tmp['rid'] = Crypt::encrypt($key->rid) : NULL;

			$tmp['date'] = date_format(new DateTime($key->updated_at), 'm/d/Y');

			if (isset($key->degree_name)) {

				$degree_name = $key->degree_name;
				$degree_initials = $key->degree_initials;
				$major_name = $key->major_name;
			}else{
				$degree_name = NULL;
				$major_name = NULL;
				$degree_initials = NULL;
			}
			
			// THIS
			$tmp['degree_name'] = $degree_name;
			$tmp['degree_initials'] = $degree_initials;

			$tmp['major'] = $major_name;

			//Why Recommended block ends
			$tmp['hand_shake'] = 0;

			if(isset($key->college_recruit) && isset($key->user_recruit)
				&& $key->college_recruit == 1 && $key->user_recruit == 1){
				$tmp['hand_shake'] = 1;
			}elseif (isset($key->college_recruit) && $key->college_recruit == -1) {
				$tmp['hand_shake'] = -1;
			}

			// For recommendations page use this to set the hand shake
			if (isset($key->active)) {
				if ($key->active == 1) {
					$tmp['hand_shake'] = 0;
				}else{
					$tmp['hand_shake'] = $key->active;
				}
			}

			if( isset($key->is_notified) ){
				$tmp['is_notified'] = true;
			}else{
				$tmp['is_notified'] = false;
			}

			$tmp['hashed_id'] = Crypt::encrypt($key->user_id);
			
			//applied student
			if( isset($key->applied) ){
				$tmp['applied'] = $key->applied;
			}

			if( isset($key->user_applied)  ){
				$tmp['user_applied'] = $key->user_applied;
			}

			if( isset($key->interview_status)  ){
				$tmp['interview_status'] = $key->interview_status;
			}

			if( !isset($key->enrolled) || $key->enrolled == 0){
			 	$tmp['enrolled'] = 0;
			}else{
				$tmp['enrolled'] = 1;
			}

			if( isset($data['currentPage']) && $data['currentPage'] == 'admin-prescreened' ){
				if( isset($key->prescreened_applied) ){
					$tmp['applied'] = $key->prescreened_applied;
				}else{
					$tmp['applied'] = 0;
				}

				if( isset($key->prescreened_enrolled) ){
					$tmp['enrolled'] = $key->prescreened_enrolled;
				}else{
					$tmp['enrolled'] = 0;
				}
			}

			// Retrieving College Application Acceptance data
			if( isset($key->user_id) && isset($data['currentPage']) && $data['currentPage'] == 'admin-verifiedApp' ){
				
				if ( isset($data['is_plexuss']) && $data['is_plexuss'] ) {
					$tmp['accepted_status'] = CollegesApplicationStatus::getPlexussStatus($key->user_id);

					// Determine the status based on all college applications the user has submitted.
					$tmp['colleges_accepted_status'] = CollegesApplicationStatus::determineUserStatus($key->user_id);

				} else if ( isset($data['is_organization']) && $data['is_organization'] && isset($data['org_school_id']) ){
					$tmp['accepted_status'] = CollegesApplicationStatus::getStatus($key->user_id, $data['org_school_id']);

				}
			}

			$user_id = $key->user_id;

			// $trc = new Transcript;

			// $trc = $trc->getUsersTranscript($user_id);
			$trc = $key->transcript_arr;

			if (isset($trc) && !empty($trc)) {
				$trc = explode(",", $trc);
			}else{
				$trc = NULL;
			}

			$uploads_arr = array();

			$tmp['transcript'] 			= false;
			$tmp['resume'] 				= false;
			$tmp['ielts'] 				= false;
			$tmp['toefl'] 				= false;
			$tmp['financial'] 			= false;
			$tmp['prescreen_interview'] = false;
			$tmp['other'] 				= false;
			$tmp['essay']				= false;
			$tmp['passport']			= false;

			// adding application upload type to uploads list for everyone - no restrictions
			// $arr = array();
			// $arr['doc_type'] = 'application';
			// $arr['path'] = '/view-student-application/'.$tmp['hashed_id'];

			// $domain = 'https://plexuss.com';

			// if( env('ENVIRONMENT') == 'DEV' ){
			// 	$domain = 'http://plexuss.dev';
			// }

			// $arr['transcript_name'] = '/generatePDF?url='.$domain.$arr['path'];
			// $uploads_arr[] = $arr;
			if (isset($trc)) {
				foreach ($trc as $key) {
					$key = trim($key);
					// $arr = array();
		
					// $arr['doc_type'] = $key->doc_type;
					// $arr['path'] = $key->transcript_path. $key->transcript_name;
					// $arr['transcript_name'] = $key->transcript_name;
					// $arr['transcript_label'] = $key->label;
					// $arr['transcript_id'] = $key->id;

					// $arr['mime_type'] = $this->getMimeTypeFromURL($arr['path']);

					if ($key == 'transcript') {
						$tmp['transcript'] = true;
					}
					if ($key == 'resume') {
						$tmp['resume'] = true;
					}
					if ($key == 'ielts') {
						$tmp['ielts'] = true;
					}
					if ($key == 'toefl') {
						$tmp['toefl'] = true;
					}
					if ($key == 'financial') {
						$tmp['financial'] = true;
					}
					if ($key == 'prescreen_interview') {
						$tmp['prescreen_interview'] = true;
					}
					if ($key == 'other') {
						$tmp['other'] = true;
					}
					if ($key == 'essay') {
						$tmp['essay'] = true;
					}
					if ($key == 'passport') {
						$tmp['passport'] = true;
					}
					
					// $uploads_arr[] = $arr;
				}
			}

			$tmp['upload_docs'] = $uploads_arr;

			//check if school has had convo with user
			// $conversationResults = DB::connection('rds1')->table('college_message_thread_members as cmtm')
			// 					->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
			// 					->join('users as u', 'u.id', '=','cmtm.user_id')
			// 					->where('u.id', $user_id)
			// 					->where('cmtm.org_branch_id', $data['org_branch_id'])
			// 					->where('cmt.is_chat', 0)
			// 					->whereNull('campaign_id')
			// 					->first();

			//if there is a result, then we know they have had a convo and save true
			$tmp['haveMessaged'] = false;

			//check if school has text user before
			// $textedBefore = DB::connection('rds1')->table('college_message_thread_members as cmtm')
			// 									->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
			// 									->join('users as u', 'u.id', '=','cmtm.user_id')
			// 									->where('u.id', $user_id)
			// 									->where('cmtm.org_branch_id', $data['org_branch_id'])
			// 									->where('cmtm.is_list_user', 0)
			// 									->where('cmt.has_text', 1)
			// 									->first();

			//if there is a result, then we know they have text this person before 
			$tmp['haveTexted'] = false;
			
			$data['inquiry_list'] [] = $tmp;
		}
		// $this->customdd($data['inquiry_list']);
		// exit();
		if ($data['currentPage'] == 'admin-inquiries' || $data['currentPage'] == 'admin-approved' ) {
			$conversationResults = DB::connection('rds1')->table('college_message_thread_members as cmtm')
						->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
						->join('users as u', 'u.id', '=','cmtm.user_id')
						->where('cmtm.org_branch_id', $data['org_branch_id'])
						->where('cmt.is_chat', 0)
						->whereNull('campaign_id')
						->whereIn('u.id', $user_id_arr)
						->groupBy('u.id')
						->pluck('cmtm.user_id');

			$textedBefore = DB::connection('rds1')->table('college_message_thread_members as cmtm')
							  ->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
						 	  ->join('users as u', 'u.id', '=','cmtm.user_id')
							  ->where('cmtm.org_branch_id', $data['org_branch_id'])
							  ->where('cmtm.is_list_user', 0)
							  ->where('cmt.has_text', 1)
							  ->whereIn('u.id', $user_id_arr)
							  ->groupBy('u.id')
							  ->pluck('cmtm.user_id');

			for ($i=0; $i < count($data['inquiry_list']) ; $i++) { 
				
				if (in_array($data['inquiry_list'][$i]['student_user_id'], (array)$conversationResults)) {
					$data['inquiry_list'][$i]['haveMessaged'] = true;
				}

				if (in_array($data['inquiry_list'][$i]['student_user_id'], (array)$textedBefore)) {
					$data['inquiry_list'][$i]['haveTexted'] = true;
				}
			}
		}

		// var_dump(count($data['inquiry_list']));
		if (isset($data['inquiry_list']) && count($data['inquiry_list']) == intval($obj['take'])) {
			// echo "Yes! We can show the results";
			$data['has_searchResults'] = true;
		}else{
			// echo "No, there is no more result";
			$data['has_searchResults'] = false;
		}

		$data['num_of_inquiry'] 	 = $this->num_of_inquiry;
		$data['num_of_recommended']  = $this->num_of_recommended_new;
		$data['num_of_pending'] 	 = $this->num_of_pending;
		$data['num_of_approved'] 	 = $this->num_of_approved;
		$data['num_of_removed'] 	 = $this->num_of_removed;
		$data['num_of_rejected'] 	 = $this->num_of_rejected;
		$data['num_of_prescreened']  = $this->num_of_prescreened;
		$data['num_of_verified_hs']  = $this->num_of_verified_hs;
		$data['num_of_verified_app'] = $this->num_of_verified_app;

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();

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
		$data['departments'] = $departments;
		$data['majors'] = $majors;

		if (isset($page) && $page == "rejected") {
			if (!isset($is_ajax)) {
				return View('admin.inquirie', $data);
			}else{
				return View('admin.searchResultInquiries', $data);
			}
		}elseif(isset($page) && $page!="recommendations" && $page != "prescreened"){
			if($data['current_viewing'] > $data['total_results'])
				$data['current_viewing'] = $data['total_results'];
			if (!isset($is_ajax)) {
				// print_r('is not ajax or is_ajax but init == 1');
				if( Cache::has(env('ENVIRONMENT').'_export_is_processing_msg_'.$data['user_id']) ){
					$data['processing_export'] = true;
					Cache::forget(env('ENVIRONMENT').'_export_is_processing_msg_'.$data['user_id']);
				}
				return View('admin.approved_pending_views', $data);
			}else{
				// if((isset($is_ajax) && $input['init'] == '1')) {
				// 	return json_encode($data);
				// } else {
					return View('admin.searchResultApproved', $data);
				// }
			}
		}

		if($data['current_viewing'] > $data['total_results'])
			$data['current_viewing'] = $data['total_results'];
		if (!isset($is_ajax)) {
			return View('admin.inquirie', $data);
		}else{
			return View('admin.searchResultInquiries', $data);
		}
	}

	public function loadProfileData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
        $data['is_admin_premium'] = $this->validateAdminPremium();
		// $this->customdd($data);
		// exit();
		$input = Request::all();

		$page    = $input['currentPage'];
		$user_id = Crypt::decrypt($input['hashedId']);

		if ($page == 'prescreened') {
			$recruitMeList =  DB::connection('rds1')->table('prescreened_users as r')
													->leftjoin('recruitment as rt', function($q) use($data){
														$q->on('rt.user_id', '=', 'r.user_id');
														$q->on('rt.college_id', '=', 'r.college_id');

														if (isset($data['aor_id'])) {
															$q->where('rt.aor_id', '=', $data['aor_id']);

														}else{
															$q->whereNull('rt.aor_id');
														}

													});

			if (isset($data['aor_portal_id'])) {
				$recruitMeList = $recruitMeList->where('r.aor_portal_id', '=', DB::raw($data['aor_portal_id']));
			}

			if (isset($data['default_organization_portal'])) {
				$recruitMeList = $recruitMeList->where('r.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
			}
		
		}elseif ($page == 'inquiries') {
			$recruitMeList = DB::connection('rds1')->table('recruitment as r');
		}else{
			$recruitMeList = DB::connection('rds1')->table('recruitment as r');
		}


		//different queries depending on if prescreened or not
		$selectQry = 'r.*, r.type as recruitment_type,
											u.fname, u.lname, u.in_college, u.id as user_id, u.hs_grad_year, u.college_grad_year, u.fb_id,
											u.profile_img_loc, u.financial_firstyr_affordibility, u.profile_percent, u.planned_start_term, 
											u.planned_start_yr, u.interested_school_type, u.skype_id, u.address as userAddress, u.email as userEmail,
											u.city as userCity, u.state as userState, u.zip as userZip, u.phone as userPhone, u.txt_opt_in as userTxt_opt_in, u.email_confirmed, u.verified_phone, u.country_id, u.birth_date,
											s.overall_gpa , s.hs_gpa, s.sat_total, s.act_composite, s.toefl_total, s.ielts_total,
											c.school_name as collegeName, c.city as collegeCity, c.state as collegeState,
											h.school_name as hsName, h.city as hsCity, h.state as hsState,
											co.country_code, co.country_name,
											dt.display_name as degree_name,dt.initials as degree_initials, dt.id as degree_id';
		if($page == 'prescreened'){

			$selectQry = $selectQry.', rt.note';


		}
		$recruitMeList =  $recruitMeList->join('users as u', 'u.id', '=', 'r.user_id')

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
										->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
										->leftjoin('transcript as tpt', 'tpt.user_id', '=', 'u.id')
										->leftjoin('college_paid_handshake_logs as cphl', 'cphl.recruitment_id', '=', 'r.id')
										->leftjoin('notification_topnavs as nt', function($join)
										 {
										    $join->on('nt.type_id', '=' , 'u.id');
										    $join->on('nt.type', '=', DB::raw("'user'"));
											$join->on('nt.msg', '=' , DB::raw("'viewed your profile'"));

										 })
										->leftjoin('plexuss_users_verifications as puv', 'puv.user_id', '=', 'u.id')
										->select( DB::raw($selectQry),
											DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
											'p.profession_name',
											'cre.reason',
											'nt.id as is_notified',
											'tpt.id as tpt_id',
											'cphl.id as is_handshake_paid',
											'puv.status as plexuss_status', 'puv.verified_skype', 'puv.phonecall_verified')

										->where('r.college_id', '=', $data['org_school_id'])
										->where('u.id', $user_id);

		// Tracking pages model
		$tp = new TrackingPage;

		// This college model
		// $this_college = College::on('rds1')->where('id', $data['org_school_id'])->first();

		$recruitMeList = $recruitMeList->leftjoin('admin_texts as at', 'at.org_branch_id', '=', DB::raw($data['org_branch_id']))
									   ->addSelect('at.id as is_admin_text');

		$recruitMeList = $recruitMeList->get();

		// $adminText = AdminText::on('rds1')->where('org_branch_id', $data['org_branch_id'])->first();

		foreach ($recruitMeList as $key ) {

			$tmp = array();

			$tmp['rec_id'] = $key->id;

			/**
			 * Build the Generated label in inquiries dashboard view
			 * @author r0g3r <menjivar.rogelio@gmail.com>			 
			 * 
			 */
				$tmp['recruitment_type'] = NULL;
				if(isset($key->recruitment_type))
				{
					
					$inquiry = strpos($key->recruitment_type,'inquiry');
					$manualConvert = strpos($key->recruitment_type,'manual_convert');					
					$recomm = strpos($key->recruitment_type,'recommendation');

					if($inquiry !== false)
					{
						$tmp['recruitment_type'] = 'Get Recruited';
					}
					elseif ($manualConvert !== false) 
					{
						$tmp['recruitment_type'] = 'Passtru/clicks';	
					}
					elseif ($recomm !== false) 
					{
						$tmp['recruitment_type'] = 'Recommendation';		
					}

				}
				
	            //$tmp['recruitment_type'] = isset($key->recruitment_type) ? $key->recruitment_type : NULL;
			/**
			 * End fill Generated lbel
			 */
			

			// Add handshake dollar sign ( this is only for a college that is in hs contract )
			$tmp['is_handshake_paid'] = isset($key->is_handshake_paid) ? $key->is_handshake_paid : NULL;
			// end hanshake dollar sign

			$tmp['name'] = ucwords(strtolower($key->fname)) . ' '. ucwords(strtolower($key->lname));

			$tmp['skype_id'] = isset($key->skype_id) ? strtolower($key->skype_id) : $key->skype_id;
			$tmp['userPhone'] = $key->userPhone;
            $tmp['userEmail'] = isset($key->userEmail) ? strtolower($key->userEmail) : $key->userEmail;
			$tmp['userAddress'] = isset($key->userAddress) ? ucwords($key->userAddress) : $key->userAddress;
			$tmp['userCity']  = isset($key->userCity) ? ucwords($key->userCity) : $key->userCity;
			$tmp['userState'] = isset($key->userState) ? strtoupper($key->userState) : $key->userState;
			$tmp['userZip']   = $key->userZip;

			$tmp['page']  = $page;
			$tmp['currentPage'] = 'admin-'.$page;
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


			//NOTES: we only want Admins who have set up a number to be able to text
			//but, this also is used to verify SMS send both,  only show contact button if admin_hasNum is set
			// Check to make sure the school have setup their text messages
			if (isset($key->is_admin_text)) {
				$tmp['admin_hasNum'] = $key->is_admin_text;
			}

			$tmp['userTxt_opt_in'] = isset($key->userTxt_opt_in) ? $key->userTxt_opt_in : 0;


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

			if($in_college){

				if(isset($key->overall_gpa)){
					$tmp['gpa'] = $key->overall_gpa;
				}else{
					$tmp['gpa'] = '';
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
					$tmp['gpa'] = '';
				}


				if(isset($key->hsName)){
					$tmp['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->hsName))));
					$tmp['school_city'] = ucwords(strtolower($key->hsCity));
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

			if($tmp['current_school'] != "N/A" && $tmp['current_school'] != "Home Schooled" && $tmp['school_state'] != ''){
				$tmp['current_school'] = $tmp['current_school'].', '.$tmp['school_city']. ', '.$tmp['school_state'];
			}

			if(isset($key->sat_total)){
				$tmp['sat_score'] = $key->sat_total;
			}else{
				$tmp['sat_score'] = '';
			}

			if(isset($key->act_composite)){
				$tmp['act_composite'] = $key->act_composite;
			}else{
				$tmp['act_composite'] = '';
			}


			if($tmp['gpa'] == "0.00"){
				$tmp['gpa'] = "";
			}

			if($tmp['sat_score'] == 0 ){
				$tmp['sat_score'] = '';
			}

			if ($tmp['act_composite'] == 0) {
				$tmp['act_composite'] = '';
			}

			if(isset($key->toefl_total)){
				$tmp['toefl_total'] = $key->toefl_total;
			}else{
				$tmp['toefl_total'] = '';
			}
			if ($tmp['toefl_total'] == 0) {
				$tmp['toefl_total'] = '';
			}

			if(isset($key->ielts_total)){
				$tmp['ielts_total'] = $key->ielts_total;
			}else{
				$tmp['ielts_total'] = '';
			}
			if ($tmp['ielts_total'] == 0) {
				$tmp['ielts_total'] = '';
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

			$tmp['country_id'] = $key->country_id;
			$tmp['student_user_id'] = $key->user_id;


			$tmp['date'] = date_format(new DateTime($key->updated_at), 'm/d/Y');

			$tmp['loginas'] = '/sales/loginas/'.Crypt::encrypt($key->user_id);

			if (isset($key->fb_id)) {
				$tmp['fb_id'] = $key->fb_id;
			}

			if (isset($key->degree_name)) {

				$degree_name 	 = $key->degree_name;
				$degree_initials = $key->degree_initials;
				$degree_id       = $key->degree_id;
				$major_name 	 = $key->major_name;
				$profession_name = $key->profession_name;
			}else{
				$degree_name = NULL;
				$degree_id   = NULL;
				$major_name = NULL;
				$profession_name = NULL;
				$degree_initials = NULL;
			}

			$tmp['degree_name'] 	= $degree_name;
			$tmp['degree_initials'] = $degree_initials;
			$tmp['degree_id']		= $degree_id;

			if ($degree_name == "") {
				$tmp['objective'] = null;
			}
			else{
				$first_major = current(explode(",", $major_name));
				if ($profession_name == "") {
					$career_str = "";
				}
				else{
					if (in_array(substr($profession_name,0,1),array('A','E','I','O','U'))) {
						$career_str = '. My dream would be to one day work as an ';
					}
					else{
						$career_str = '. My dream would be to one day work as a ';
					}
				}
				if ($degree_name == 'Certificate Programs') {
					$tmp['objective'] = "I would like to get a Certificate in ".$first_major.$career_str.$profession_name.".";
				}
				elseif ($degree_name == 'Undecided') {
					$tmp['objective'] = "I would like to get an undecided degree in ".$first_major.$career_str.$profession_name.".";
				}
				elseif ($degree_name == 'Other') {
					$tmp['objective'] = "I would like to get another degree in ".$first_major.$career_str.$profession_name.".";
				}
				else{
					if (in_array(substr($degree_name,0,1),array('A','E','I','O','U'))) {
						$tmp['objective'] = "I would like to get an ".$degree_name." in ".$first_major.$career_str.$profession_name.".";
					}
					else{
						$tmp['objective'] = "I would like to get a ".$degree_name." in ".$first_major.$career_str.$profession_name.".";
					}
				}
			}
			$tmp['major'] = $major_name;

			if(isset($key->other) && $key->other !== 0 && $key->other !== ''){
				$tmp['why_interested_other'] = $key->other;
			}

			$tmp['why_interested'] = array();

			//We only set these options IF the user has checked true.
			//We will loop this'why_interested' array for the admin.

			if(isset($key->reputation) && $key->reputation == 1){
				$tmp['why_interested'][] = 'Academic Reputation';
			}

			if(isset($key->location) && $key->location == 1){
				$tmp['why_interested'][] = 'Location';
			}

			if (isset($key->tuition) && $key->tuition == 1) {
				$tmp['why_interested'][] = 'Cost of Tuition';
			}

			if (isset($key->program_offered) && $key->program_offered == 1) {
				$tmp['why_interested'][] = 'Majors or Programs Offered';
			}

			if (isset($key->athletic) && $key->athletic == 1) {
				$tmp['why_interested'][] = 'Athletics';
			}

			if (isset($key->onlineCourse) && $key->onlineCourse == 1) {
				$tmp['why_interested'][] = 'Online Courses';
			}

			if (isset($key->religion) && $key->religion == 1) {
				$tmp['why_interested'][] = 'Religion';
			}

			if (isset($key->campus_life) && $key->campus_life == 1) {
				$tmp['why_interested'] [] = 'Campus Life';
			}

			//Why Recommended block
			if(isset($key->reason)){
				if ($key->reason == "liked_college") {
					$tmp['why_recommended'] = "Liked your page";
				}
				if ($key->reason == "user_viewed_college") {
					$tmp['why_recommended'] = "Viewed your page";
				}
				if ($key->reason == "user_compared_college") {
					$tmp['why_recommended'] = "Compared your stats to another college";
				}
				if ($key->reason == "recommended_college") {
					$tmp['why_recommended'] = "You were previously recommended to this student";
				}
				if ($key->reason == "near_college_user") {
					$tmp['why_recommended'] = "Student is within a 50 mi of campus";
				}
				if ($key->reason == "similar_tier_colleges") {
					$tmp['why_recommended'] = "Student selected a similar school";
				}
				if ($key->reason == "random_user") {
					$tmp['why_recommended'] = "Recommended to keep in your pipeline for early engagement";
				}
				if ($key->reason == "filtered_user") {
					$tmp['why_recommended'] = "Student selected based on your filter";
				}
			}

			if (isset($key->type)) {
				$tmp['recommendation_type'] = $key->type;
			}else{
				$tmp['recommendation_type'] = 'not_filtered';
			}

			//Why Recommended block ends

			$tmp['hand_shake'] = 0;

			if(isset($key->college_recruit) && isset($key->user_recruit)
				&& $key->college_recruit == 1 && $key->user_recruit == 1){
				$tmp['hand_shake'] = 1;
			}elseif (isset($key->college_recruit) && $key->college_recruit == -1) {
				$tmp['hand_shake'] = -1;
			}

			// For recommendations page use this to set the hand shake
			if (isset($key->active)) {
				if ($key->active == 1) {
					$tmp['hand_shake'] = 0;
				}else{
					$tmp['hand_shake'] = $key->active;
				}
			}

			if( isset($key->is_notified) ){
				$tmp['is_notified'] = true;
			}else{
				$tmp['is_notified'] = false;
			}

			// THIS
			$tmp['hashed_id'] = Crypt::encrypt($key->user_id);

            // New Notes
            $tmp['other_notes'] = [];
            $tmp['my_notes'] = null;

            $all_notes = CrmNotes::where('crm_notes.org_branch_id', '=', $data['org_branch_id'])
                                 ->where('crm_notes.student_user_id', '=', $key->user_id)
                                 ->whereNotNull('crm_notes.note')
                                 ->whereRaw('crm_notes.note <> ""')
                                 ->join('users as u', 'u.id', '=', 'crm_notes.creator_user_id')
                                 ->select('u.fname', 'u.lname', 'crm_notes.creator_user_id', 'crm_notes.note', 'crm_notes.updated_at')
                                 ->orderBy('updated_at', 'desc')
                                 ->get();

            foreach ($all_notes as $notes) {
                $formatted_time = $this->xTimeAgo($notes->updated_at, date("Y-m-d H:i:s"));

                $tmp_notes = [
                    'fname' => $notes->fname,
                    'lname' => $notes->lname,
                    'note' => $notes->note,
                    'updated_at' => $formatted_time,
                ];

                if ($notes->creator_user_id == $data['user_id']) {
                    $tmp['my_notes'] = $tmp_notes;
                } else {
                    $tmp['other_notes'][] = $tmp_notes;
                }
            }
            // End New Notes

			$tmp['note'] = '';
			$tmp['note_updated_at'] = '';

			if(isset($key->note)){
				$tmp['note'] = $key->note;
			}
			if(isset($key->updated_at)){
				$tmp['note_updated_at'] = $this->xTimeAgo($key->updated_at ,date("Y-m-d H:i:s"));
			}

			$asn = new AdvancedSearchNote;
			$asn = $asn->getAdvancedSearchNote($key->user_id);

			$tmp['plexuss_note'] = '';
			$tmp['plexuss_note_updated_at'] = '';

			if(isset($asn->note)){
				$tmp['plexuss_note'] = $asn->note;
			}
			if(isset($asn->updated_at)){
				$tmp['plexuss_note_updated_at'] = $this->xTimeAgo($asn->updated_at ,date("Y-m-d H:i:s"));
			}
			
			//applied student
			if( isset($key->applied) ){
				$tmp['applied'] = $key->applied;
			}

			if( isset($key->user_applied)  ){
				$tmp['user_applied'] = $key->user_applied;
			}

			if( isset($key->interview_status)  ){
				$tmp['interview_status'] = $key->interview_status;
			}

			if( !isset($key->enrolled) || $key->enrolled == 0){
			 	$tmp['enrolled'] = 0;
			}else{
				$tmp['enrolled'] = 1;
			}

			// echo '<pre>';
			// print_r($tmp);
			// echo '</pre>';
			// exit();

			$usr_college_info = array();
			$usr_college_info['name'] = $data['school_name'];
			$usr_college_info['slug'] = $data['school_slug'];
			$usr_college_info['logo'] = substr($data['school_logo'], strrpos($data['school_logo'], '/') + 1);
			$usr_college_info['page_views'] = $tp->getNumCollegeView($key->user_id,$data['school_slug']);

			$tmp['college_info'] = $usr_college_info;

			$rec = Recruitment::where('user_id', $key->user_id)
								->join('colleges as c', 'c.id', '=','recruitment.college_id')
								->select('c.id as college_id', 'c.school_name', 'c.slug','c.logo_url')
								->where('recruitment.user_recruit', '=', 1)
								->where('recruitment.status', '=', 1)
								->where('c.id', '!=', $data['org_school_id'])
								->get();

			$user_id = $key->user_id;
			$competitor_colleges = array();

			foreach ($rec as $key) {

				$arr = array();
				$arr['id'] = $key->college_id;
				$arr['name'] = $key->school_name;
				$arr['slug'] = $key->slug;
				$arr['logo'] = $key->logo_url;
				$arr['page_views'] = $tp->getNumCollegeView($user_id,$key->slug);

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
				if ($key->doc_type == 'prescreen_interview') {
					$tmp['prescreen_interview'] = true;
				}
				if ($key->doc_type == 'other') {
					$tmp['other'] = true;
				}
				if ($key->doc_type == 'essay') {
					$tmp['essay'] = true;
				}
				if ($key->doc_type == 'passport') {
					$tmp['passport'] = true;
				}
				
				$uploads_arr[] = $arr;
			}	

			$tmp['upload_docs'] = $uploads_arr;

			//check if school has had convo with user
			$conversationResults = DB::connection('rds1')->table('college_message_thread_members as cmtm')
								->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
								->join('users as u', 'u.id', '=','cmtm.user_id')
								->where('u.id', $user_id)
								->where('cmtm.org_branch_id', $data['org_branch_id'])
								->where('cmt.is_chat', 0)
								->whereNull('campaign_id')
								->first();

			//if there is a result, then we know they have had a convo and save true
			$tmp['haveMessaged'] = false;
			if( isset($conversationResults) ){
				$tmp['haveMessaged'] = true;
			}

			//check if school has text user before
			$textedBefore = DB::connection('rds1')->table('college_message_thread_members as cmtm')
												->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
												->join('users as u', 'u.id', '=','cmtm.user_id')
												->where('u.id', $user_id)
												->where('cmtm.org_branch_id', $data['org_branch_id'])
												->where('cmtm.is_list_user', 0)
												->where('cmt.has_text', 1)
												->first();

			//if there is a result, then we know they have text this person before 
			$tmp['haveTexted'] = false;
			if( isset($textedBefore) ){
				$tmp['haveTexted'] = true;
			}

			// $dc = DistributionClient::on('rds1')->where('org_branch_id', $data['org_branch_id'])->first();

			// if (isset($dc)) {
			// 	$tmp['post_students'] = array();
			// 	$arr = array();

			// 	$dr = DistributionResponse::on('rds1')->where('user_id', $tmp['student_user_id'])
			// 										  ->where('dc_id', $dc->id)
			// 										  ->orderBy('id', 'DESC')
			// 										  ->get();
													  
			// 	$dc_controller = new DistributionController;
			// 	$arr['is_eligible'] = (array)json_decode($dc_controller->isEligible($tmp['student_user_id']));
	
			// 	if (count($dr) > 0) {
			// 		$tp = array();
			// 		$arr['responses'] = array();

			// 		foreach ($dr as $k) {
						
			// 			$tp['date'] 	 = date('m/d/Y H:i', strtotime($k->created_at));
			// 			$tp['success']   = $k->success;
			// 			$tp['error_msg'] = $k->error_msg;

			// 			$arr['responses'][] = $tp;
			// 		}
			// 	}

			// 	$tmp['post_students'] = $arr;
			// }

			$tmp['matched_colleges'] = NULL;
			$tmp['show_matched_colleges'] = false;

			if(Session::has('handshake_power') && $data['is_plexuss'] == 1){
				$tmp['show_matched_colleges'] = true;
			}

			$uac = DB::connection('rds1')->table('users_applied_colleges as uac')
										 ->join('colleges as c', 'c.id', '=', 'uac.college_id')
										 ->leftjoin('colleges_application_status as cas', 'c.id', '=', 'cas.college_id')
										 ->select('c.school_name', 'c.logo_url', 'c.slug', 'c.id as college_id', 'cas.status', 'uac.submitted')
										 ->where('uac.user_id', $key->user_id)
										 ->where('cas.user_id', $key->user_id)
										 ->groupBy('c.id')
										 ->orderBy(DB::raw('ISNULL(cas.status), cas.status'), 'ASC')
										 ->get();

			$tmp['applied_colleges'] = $uac;

			$ucq_state = UsersCustomQuestion::select('application_state')
									   ->where('user_id', $key->user_id)->first();
			if (!empty($ucq_state))
				$tmp['application_state'] = $ucq_state->application_state;
			
			$data['inquiry_list'] [] = $tmp;
		}

		$ctry = new Country;
		$data['country_list'] = $ctry->getAllCountriesAndIds();

		if(Session::has('handshake_power')){
			$data['uid'] = $key->user_id;
 			// return View('admin.salesProfilePane', $data);
		}
		// $data['applied_colleges'] = $this->getMatchedCollegesForThisUser($input['hashedId']);
		return View('admin.salesProfilePane', $data);
		// return View('admin.regStudentProfilePane', $data);
	}


	/**
	 * getInquiryCnt
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	private function getInquiryCnt(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$college_id = Session::get('userinfo.school_id');

		if (!isset($college_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		$cnt = Recruitment::where('user_id', '!=', $my_user_id)
							->where('college_id', $college_id)
							->where('user_recruit', 1)
							->where('college_recruit','!=',1)
							->where('status', 1);

		// If user has a department set, show results based on the filters they have set
		$crf = new CollegeRecommendationFilters;
		$filter_qry = $crf->generateFilterQry($data);

		if (isset($filter_qry) && isset($data['default_organization_portal'])) {
			$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
			$tmp_qry = $this->getRawSqlWithBindings($filter_qry);
			$cnt = $cnt->join(DB::raw('('.$tmp_qry.')  as t2'), 't2.filterUserId' , '=', 'recruitment.user_id');
		}
		// End of department query set


		return $cnt->count();
	}

	/**
	 * getInquiryCntTotal
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	private function getInquiryCntTotal(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$college_id = Session::get('userinfo.school_id');

		if (!isset($college_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		$cnt = Recruitment::where('user_id', '!=', $my_user_id)
							->where('college_id', $college_id)
							->where('college_recruit','!=',1)
							->where('status', 1);

		// If user has a department set, show results based on the filters they have set
		$crf = new CollegeRecommendationFilters;
		$filter_qry = $crf->generateFilterQry($data);

		if (isset($filter_qry) && isset($data['default_organization_portal'])) {
			$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
			$tmp_qry = $this->getRawSqlWithBindings($filter_qry);
			$cnt = $cnt->join(DB::raw('('.$tmp_qry.')  as t2'), 't2.filterUserId' , '=', 'recruitment.user_id');
		}
		// End of department query set

		return $cnt->count();
	}

	private function getApprovedCnt(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$college_id = Session::get('userinfo.school_id');

		if (!isset($college_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		$cnt = Recruitment::where('user_id', '!=', $my_user_id)
							->where('college_id', $college_id)
							->where('user_recruit', 1)
							->where('college_recruit','=',1)
							->where('status', 1);

		// If user has a department set, show results based on the filters they have set
		$crf = new CollegeRecommendationFilters;
		$filter_qry = $crf->generateFilterQry($data);

		if (isset($filter_qry) && isset($data['default_organization_portal'])) {
			$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
			$tmp_qry = $this->getRawSqlWithBindings($filter_qry);
			$cnt = $cnt->join(DB::raw('('.$tmp_qry.')  as t2'), 't2.filterUserId' , '=', 'recruitment.user_id');
		}
		// End of department query set

		return $cnt->count();
	}

	private function getRecommendCnt($data){

		$college_id = $data['org_school_id'];

		if (!isset($college_id)) {
			return "You are not an admin!";
		}

		$qry = CollegeRecommendation::where('college_id', $college_id)
							->where('created_at', '>=', $this->min_cron_date)
							->where('created_at', '<=', $this->max_cron_date)
							->where(function ($query) {
							    $query->orwhere('active', 1)
							          ->orwhere('active', -1);
							});
		if (isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])) {
			$qry = $qry->where('org_portal_id', $data['default_organization_portal']->id);
		}

		if (isset($data['aor_id'])) {
			$qry = $qry->where('aor_id', $data['aor_id']);
		}else{
			$qry = $qry->whereNull('aor_id');
		}

		return $qry->count();
	}

	private function getRecommendCntTotal($data){

		$college_id = $data['org_school_id'];

		if (!isset($college_id)) {
			return "You are not an admin!";
		}

		$qry = CollegeRecommendation::where('college_id', $college_id)
							->where('created_at', '>=', $this->min_cron_date)
							->where('created_at', '<=', $this->max_cron_date);

		if (isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])) {
			$qry = $qry->where('org_portal_id', $data['default_organization_portal']->id);
		}

		if (isset($data['aor_id'])) {
			$qry = $qry->where('aor_id', $data['aor_id']);
		}else{
			$qry = $qry->whereNull('aor_id');
		}

		return $qry->count();
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

	private function collegeAdminPanelCnt($data, $is_dashboard){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
		
		// AOR : if the user is AOR, we only want to show the aor users
		// if (isset($data['aor_id'])) {
		// 	$aor_id = $data['aor_id'];
		// }else{
		// 	$aor_id = NULL;
		// }
		// // End of AOR

		// $tmp_qry = NULL;
		// $filter_qry = NULL;
		// If user has a department set, show results based on the filters they have set
		// if (isset($data['default_organization_portal'])) {
		// 	$crf = new CollegeRecommendationFilters;
		// 	$filter_qry = $crf->generateFilterQry($data);
		// 	if (isset($filter_qry)) {
		// 		$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
		// 		$filter_qry = $filter_qry->join('recruitment as filterRec', 'filterRec.user_id', '=', 'userFilter.id');
		// 		$filter_qry = $filter_qry->where('filterRec.college_id', $data['org_school_id']);

		// 		$tmp_qry = $this->getRawSqlWithBindings($filter_qry);
		// 	}
		// }
		// End of department query set
        $is_admin_premium = $this->validateAdminPremium();

		if (isset($is_dashboard)) {
			$this->num_of_inquiry_new  	   = $this->runDashboardQry('inquiry-new', $data);
            $this->num_of_removed_new      = $this->runDashboardQry('removed-new', $data);

            if ($is_admin_premium) {
    			$this->num_of_pending_new  	   = $this->runDashboardQry('pending-new', $data);
    			$this->num_of_approved_new 	   = $this->runDashboardQry('approved-new', $data);
    			$this->num_of_rejected_new 	   = $this->runDashboardQry('rejected-new', $data);
    			$this->num_of_verified_hs_new  = $this->runDashboardQry('verified-hs-new', $data);
    			$this->num_of_verified_app_new = $this->runDashboardQry('verified-app-new', $data);
    			$this->num_of_recommended      = $this->getRecommendCntTotal($data);
            }
		}

		$this->num_of_inquiry      = $this->runDashboardQry('inquiry', $data);
        $this->num_of_removed      = $this->runDashboardQry('removed', $data);

        if ($is_admin_premium) {
    		$this->num_of_pending      = $this->runDashboardQry('pending', $data);
    		$this->num_of_approved     = $this->runDashboardQry('approved', $data);
    		$this->num_of_rejected     = $this->runDashboardQry('rejected', $data);
    		$this->num_of_verified_hs  = $this->runDashboardQry('verified-hs', $data);
    		$this->num_of_verified_app = $this->runDashboardQry('verified-app', $data);

    		$this->num_of_recommended_new  = $this->getRecommendCnt($data);
    		$pr = new PrescreenedUser;
    		$data['college_id'] = $data['org_school_id'];
    		$data['user_id'] 	= NULL;
    		$this->num_of_prescreened  = $pr->getTotalNumOfPrescreened($data); 
        }
	}

    public function getInquiriesCount() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $response = [];

        $response['count'] = $this->runDashboardQry('inquiry', $data);

        return $response;
    }

    public function getConvertedCount() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);
        $response = [];

        // Get ro_id if exists in the current portal
        if (isset($data['default_organization_portal']) && isset($data['default_organization_portal']->ro_id)) {
            $data['ro_id'] = $data['default_organization_portal']->ro_id;
        }

        // If currently in general, check if ro_id exists as well
        if (!isset($data['default_organization_portal']) && isset($data['organization_portals']) && isset($data['organization_portals'][0]) && isset($data['organization_portals'][0]->ro_id)) {
            $data['ro_id'] = $data['organization_portals'][0]->ro_id;
        }

        $response['count'] = $this->getConvertedCountBetweenDates($data);

        return $response;
    }

	private function runDashboardQry($type, $data){
        $org_branch_id = $data['org_branch_id'];
        $portal_id = isset($data['default_organization_portal']) ? $data['default_organization_portal']->id : 'general';

		switch ($type) {
			case 'inquiry':
				if (Cache::has(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id)) {
                    $rec = Cache::get(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id);

                    break;
                }

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 1)
						   ->where('rec.user_recruit', 1)
						   ->where('rec.college_recruit', 0);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->count('rec.user_id');

                Cache::put(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id, $rec, 30);

				break;
			case 'inquiry-new':
                if (Cache::has(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id)) {
                    $rec = Cache::get(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id);
                    break;
                }

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 1)
						   ->where('rec.user_recruit', 1)
						   ->where('rec.college_recruit', 0);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->whereNull('nt.id')
						   ->count('rec.user_id');

                Cache::put(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id, $rec, 30);
				break;
			case 'pending':

				$rec = $this->collegeAdminPanelQry($data, $type);

				$rec = $rec->where('rec.status', 1)
						   ->where('rec.user_recruit', 0)
						   ->where('rec.college_recruit', 1);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->count('rec.user_id');

				break;
			case 'pending-new':

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 1)
						   ->where('rec.user_recruit', 0)
						   ->where('rec.college_recruit', 1);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->whereNull('nt.id')
						   ->count('rec.user_id');
				break;
			case 'approved':

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 1)
						   ->where('rec.user_recruit', 1)
						   ->where('rec.college_recruit', 1);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->count('rec.user_id');

				break;
			case 'approved-new':

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 1)
						   ->where('rec.user_recruit', 1)
						   ->where('rec.college_recruit', 1);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->whereNull('nt.id')
						   ->count('rec.user_id');

				break;
			case 'removed':

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 0)
						   ->where('rec.college_recruit', '<', 9)
						   ->where('rec.user_recruit', '<', 9);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->count('rec.user_id');
				break;
			case 'removed-new':

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 0)
						   ->where('rec.college_recruit', '<', 9)
						   ->where('rec.user_recruit', '<', 9);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->whereNull('nt.id')
						   ->count('rec.user_id');
				break;
			case 'rejected':

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 1)
						   ->where('rec.user_recruit', 1)
						   ->where('rec.college_recruit', -1);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->count('rec.user_id');

				break;
			case 'rejected-new':

				$rec = $this->collegeAdminPanelQry($data, $type);
				$rec = $rec->where('rec.status', 1)
						   ->where('rec.user_recruit', 1)
						   ->where('rec.college_recruit', -1);
						   
				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->distinct('rec.user_id')
						   ->whereNull('nt.id')
						   ->count('rec.user_id');
				break;
			case 'verified-hs':
				
				$rec = $this->collegeAdminPanelQry($data, $type);

				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->join('recruitment_verified_hs as rvh', function($q) use ($data){
							$q->on('rec.college_id', '=', 'rvh.college_id');
							$q->on('rec.user_id', '=', 'rvh.user_id');
							if (isset($data['aor_id'])) {
								$q->where('rvh.aor_id', '=', DB::raw($data['aor_id']));

							}else{
								$q->whereNull('rvh.aor_id');
							}
							if (isset($data['aor_portal_id'])) {
								$q->where('rvh.aor_portal_id', '=', DB::raw($data['aor_portal_id']));

							}

							if (isset($data['default_organization_portal'])) {
								$q->where('rvh.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));

							}
						});

				$rec = $rec->distinct('rec.user_id')
						   ->count('rec.user_id');

				break;
			case 'verified-hs-new':
				
				$rec = $this->collegeAdminPanelQry($data, $type);

				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->join('recruitment_verified_hs as rvh', function($q) use ($data){
							$q->on('rec.college_id', '=', 'rvh.college_id');
							$q->on('rec.user_id', '=', 'rvh.user_id');
							if (isset($data['aor_id'])) {
								$q->where('rvh.aor_id', '=', DB::raw($data['aor_id']));

							}else{
								$q->whereNull('rvh.aor_id');
							}
							if (isset($data['aor_portal_id'])) {
								$q->where('rvh.aor_portal_id', '=', DB::raw($data['aor_portal_id']));

							}

							if (isset($data['default_organization_portal'])) {
								$q->where('rvh.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));

							}
						});

				$rec = $rec->distinct('rec.user_id')
						   ->whereNull('nt.id')
						   ->count('rec.user_id');

				break;
			case 'verified-app':

				$rec = $this->collegeAdminPanelQry($data, $type);

				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->join('recruitment_verified_apps as rva', function($q) use ($data){
							$q->on('rec.college_id', '=', 'rva.college_id');
							$q->on('rec.user_id', '=', 'rva.user_id');
							if (isset($data['aor_id'])) {
								$q->where('rva.aor_id', '=', DB::raw($data['aor_id']));

							}else{
								$q->whereNull('rva.aor_id');
							}
							if (isset($data['aor_portal_id'])) {
								$q->where('rva.aor_portal_id', '=', $data['aor_portal_id']);

							}
							if (isset($data['default_organization_portal'])) {
								$q->where('rva.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));

							}
						});

				$rec = $rec->distinct('rec.user_id')
						   ->count('rec.user_id');

				break;
			case 'verified-app-new':
				
				$rec = $this->collegeAdminPanelQry($data, $type);

				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->join('recruitment_verified_apps as rva', function($q) use ($data){
							$q->on('rec.college_id', '=', 'rva.college_id');
							$q->on('rec.user_id', '=', 'rva.user_id');
							if (isset($data['aor_id'])) {
								$q->where('rva.aor_id', '=', DB::raw($data['aor_id']));

							}else{
								$q->whereNull('rva.aor_id');
							}
							if (isset($data['aor_portal_id'])) {
								$q->where('rva.aor_portal_id', '=', $data['aor_portal_id']);

							}
							if (isset($data['default_organization_portal'])) {
								$q->where('rva.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));

							}
						});

				$rec = $rec->distinct('rec.user_id')
						   ->whereNull('nt.id')
						   ->count('rec.user_id');

				break;
			case 'converted':
                if (Cache::has(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id)) {
                    $rec = Cache::get(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id);
                    break;
                }

				$rec = $this->collegeAdminPanelQry($data, $type);

                $rec = $rec->where('rec.status', 1)
                           ->where('rec.user_recruit', 0)
                           ->where('rec.college_recruit', 0);

				if (isset($data['aor_id'])) {
					$rec = $rec->where('rec.aor_id', $data['aor_id']);
				}else{
					$rec = $rec->whereNull('rec.aor_id');
				}

				$rec = $rec->join('recruitment_converts as rc', 'rc.rec_id', '=', 'rec.id');

				$rec = $rec->distinct('rec.user_id')
						   // ->whereNull('nt.id')
						   ->count('rec.user_id');

                Cache::put(env('ENVIRONMENT') . '_admin_dashboard_count_for_' . $type . '_' . $org_branch_id . '_' . $portal_id, $rec, 30);
				break;

            case 'converted-page':

                $rec = $this->getConvertedCountBetweenDates($data, $dates);
                break;
		}

		return $rec;
	}
	
	private function collegeAdminPanelQry($data, $type){
		$rec = DB::connection('rds1')->table('recruitment as rec')->join('users as u', 'u.id', '=', 'rec.user_id')
						  ->where('rec.college_id', $data['org_school_id'])
						  ->leftjoin('notification_topnavs as nt', function($join)
							 {
							    $join->on('nt.type_id', '=' , 'u.id');
							    $join->where('nt.type', '=', 'user');
								$join->where('nt.command', '=', 1);

							 });
		if ($type != "verified-hs" && $type != "verified-hs-new" && $type != "verified-app" && $type != "verified-app-new") {
		  	if (isset($data['default_organization_portal'])) {
				$rec = $rec->join('recruitment_tags as rts', function($q) use ($data){
								$q->on('rts.college_id', '=', DB::raw($data['org_school_id']));
								$q->on('rts.user_id', '=', 'u.id');

								if (isset($data['aor_id'])) {
									$q->where('rts.aor_id', '=', DB::raw($data['aor_id']));

								}else{
									$q->whereNull('rts.aor_id')
									  ->whereNull('rts.aor_portal_id')
									  ->where('rts.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
								}
							});
			}
		}				  

		return $rec;
	}

    public function collegeAdminRevenueOrganizationQry($org_branch_id) {
        if (!isset($org_branch_id)) return 'failed trying to get org_branch_id';
        
        $data = [];
        $data['org_branch_id'] = $org_branch_id;

        $org_branch = OrganizationBranch::select('id', 'school_id')
                                        ->where('id', '=', $org_branch_id)
                                        ->first();

        if (!isset($org_branch)) return 'failed trying to find org_branch';

        $aor_college = AorCollege::on('rds1')->select('aor_id')->where('college_id', '=', $org_branch->school_id)->first();

        $data['aor_id'] = isset($aor_college) ? $aor_college->aor_id : NULL;

        $ro = RevenueOrganization::on('rds1')->select('id', 'name')->where('org_branch_id', '=', $org_branch_id)->first();

        $data['ro_id'] = isset($ro) ? $ro->id : NULL;

        $data['org_school_id'] = $org_branch->school_id;

        $response = [];

        $response['REPORTDATA'] = [];
        $response['MONTHDATA'] = [];

        for ($i = 1; $i < 4; $i++) {
            $today = date('Y-m-d', strtotime('-' . $i . ' days'));
            $dates = [$today . ' 00:00:00', $today . ' 23:59:59'];
            $inquiry_count = $this->getInquiriesCountBetweenDates($data, $dates);
            $converted_count = $this->getConvertedCountBetweenDates($data, $dates);

            $response['REPORTDATA'][] = [
                'date' => $today,
                'campaign' => $ro->name,
                'inquiries' => $inquiry_count,
                'conversions' => $converted_count
            ];
        }

        $beginning_of_month = date('Y-m-01');
        $dates = [$beginning_of_month . ' 00:00:00', date('Y-m-d', strtotime('-1 days')) . ' 23:59:59'];

        $month_inquiry_count = $this->getInquiriesCountBetweenDates($data, $dates);
        $month_converted_count = $this->getConvertedCountBetweenDates($data, $dates);

        $response['MONTHDATA'][] = [
            'campaign' => $ro->name,
            'inquiries' => $month_inquiry_count,
            'conversions' => $month_converted_count
        ];

        return $response;
    }

    private function getConvertedCountBetweenDates($data, $dates = NULL) {
        $college_count = 0;
        $ro_count = 0;

        $temp_user_ids = [];

        $collegeQuery = DB::connection('rds1')->table('recruitment as rec');

        $collegeQuery = $collegeQuery->where('rec.user_recruit', '=', 1)
                       ->where('rec.college_recruit', '=', 0)
                       ->where('rec.status', '=', 0);

        if (isset($aor_college)) {
            $collegeQuery = $collegeQuery->where('rec.aor_id', '=', $aor_college->aor_id);

        } else {
            $collegeQuery = $collegeQuery->whereNull('rec.aor_id');

        }

        $collegeQuery = $collegeQuery->join('recruitment_converts as rc', 'rc.rec_id', '=', 'rec.id');

        if (isset($data['is_plexuss']) && $data['is_plexuss'] == 1) {
            $collegeQuery = $collegeQuery->join('users as u', 'rec.user_id', '=', 'u.id');
            $collegeQuery = $collegeQuery->whereNotNull('u.phone')
                                         ->where('u.phone', '!=', ' ');
        }

        $collegeQuery = $collegeQuery->distinct('rec.user_id');

        if (isset($dates)) {
            $collegeQuery = $collegeQuery->whereBetween('rc.created_at', $dates);
        }

        $college_count = $collegeQuery->where('rec.college_id', $data['org_school_id'])->pluck('rec.user_id');

        if ($data['org_school_id'] !== 7916) {
            foreach ($college_count as $user_id) {
                $temp_user_ids[$user_id] = true;
            }
        }
        // If ro_id exists, do a ro query.
        if (isset($data['ro_id'])) {
            $roQuery = DB::connection('rds1')->table('recruitment as rec');

            $roQuery = $roQuery->where('rec.user_recruit', '=', 0)
                               ->where('rec.college_recruit', '=', 0)
                               ->where('rec.status', '=', 1);

            if (isset($aor_college)) {
                $roQuery = $roQuery->where('rec.aor_id', '=', $aor_college->aor_id);

            } else {
                $roQuery = $roQuery->whereNull('rec.aor_id');
            }

            $roQuery = $roQuery->join('recruitment_converts as rc', 'rc.rec_id', '=', 'rec.id');


            if (isset($data['is_plexuss']) && $data['is_plexuss'] == 1) {
                $roQuery = $roQuery->join('users as u', 'rec.user_id', '=', 'u.id');
                $roQuery = $roQuery->whereNotNull('u.phone')
                                   ->where('u.phone', '!=', ' ');
            }

            $roQuery = $roQuery->distinct('rec.user_id');

            if (isset($dates)) {
                $roQuery = $roQuery->whereBetween('rc.created_at', $dates);
            }

            $ro_count = $roQuery->where('rec.ro_id', '=', DB::raw($data['ro_id']))
                                ->pluck('rec.user_id');


            foreach ($ro_count as $user_id) {
                $temp_user_ids[$user_id] = true;
            }
        }

        // Add up result
        $total_count = count($temp_user_ids);

        return $total_count;
    }

    private function getInquiriesCountBetweenDates($data, $dates) {
        $college_count = 0;
        $ro_count = 0;

        $collegeQuery = DB::connection('rds1')->table('recruitment as rec');

        $collegeQuery = $collegeQuery->where('rec.user_recruit', '=', 1)
                       ->where('rec.college_recruit', '=', 0)
                       ->where('rec.status', '=', 1);

        if (isset($aor_college)) {
            $collegeQuery = $collegeQuery->where('rec.aor_id', '=', $aor_college->aor_id);

        } else {
            $collegeQuery = $collegeQuery->whereNull('rec.aor_id');
        }

        $collegeQuery = $collegeQuery->distinct('rec.user_id')
                   ->whereBetween('rec.created_at', $dates);

        $college_count = $collegeQuery->where('rec.college_id', $data['org_school_id'])->count('rec.user_id');

        // If ro_id exists, do a ro query.
        if (isset($data['ro_id'])) {
            $roQuery = DB::connection('rds1')->table('recruitment as rec');

            $roQuery = $roQuery->where('rec.user_recruit', '=', 1)
                               ->where('rec.college_recruit', '=', 0)
                               ->where('rec.status', '=', 1);

            if (isset($aor_college)) {
                $roQuery = $roQuery->where('rec.aor_id', '=', $aor_college->aor_id);

            } else {
                $roQuery = $roQuery->whereNull('rec.aor_id');
            }

            $roQuery = $roQuery->distinct('rec.user_id')
                               ->whereBetween('rec.created_at', $dates);

            $ro_count = $roQuery->where('rec.ro_id', '=', DB::raw($data['ro_id']))
                                ->count('rec.user_id');
        }           

        // Add up result
        $total_count = $college_count + $ro_count;

        return $total_count;
    }

	// AOR Methods begin here
	public function manageCollege(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$data['currentPage'] = 'manageCollegesReporting';
		$data['title'] = "Colleges Reporting";


		if (!isset($data['aor_id'])) {
			return redirect()->intended( '/admin' );
		}

		$ac = new AorCollege;
		$college_ids = $ac->getAORCollegeIds($data['aor_id']);

		$data['num_of_colleges'] = count($college_ids);

		$org = new Organization;

		$admins_info = $org->getOrgsAdminInfo(NULL, NULL, $data['aor_id']);

		if (!isset($admins_info) || empty($admins_info)) {
			return redirect()->intended( '/admin' );
		}

		$tp = new TrackingPage;

		$cml = new CollegeMessageLog;

		$rec = new Recruitment;

		$ntn = new NotificationTopNav;

		$lt = new LikesTally;

		$cr = new CollegeRecommendation;

		$lst = new RankingList;

		$ret = array();

		$name_arr = array();
		$org_name_arr = array();
		$org_branch_id_arr = array();
		$user_id_arr = array();
		$school_name_arr = array();
		$date_joined_arr = array();
		$college_id_arr = array();

		foreach ($admins_info as $key) {
			$name_arr[] = ucfirst($key->fname). ' '. ucfirst($key->lname);
			$org_name_arr[] = ucfirst($key->org_name);
			$org_branch_id_arr[] = $key->org_branch_id;
			$user_id_arr[] = $key->user_id;
			$school_name_arr[] = $key->school_name;
			$date_joined_arr[] = $key->date_joined;
			$college_id_arr[] = $key->college_id;
		}

		$num_of_inquiries_arr = $rec->getNumOfInquiryForColleges($college_id_arr, 'inquiry', null, null, $data['aor_id']);
		$total_num_of_pending_from_all_sources_arr = $rec->getNumOfTotalPendingFromAllSourcesForColleges($college_id_arr, $data['aor_id']);
		$num_of_recommendations_arr = $cr->getTotalNumOfRecommendationsForColleges($college_id_arr, null, $data['aor_id']);
		$num_of_recommendations_accepted_pending_arr = $cr->getNumOfRecommendationsAcceptedPendingForColleges($college_id_arr, null, null, $data['aor_id']);
		$total_num_of_approved_by_pending_arr = $rec->getNumOfTotalPendingApprovedForColleges($college_id_arr, $data['aor_id']);

		$num_of_approved_arr = $rec->getNumOfApprovedForColleges($college_id_arr, null, null, $data['aor_id']);

		for ($i=0; $i < count($admins_info); $i++) {

			$arr = array();

			$arr['login_as'] = '/admin/aor/loginas/'.Crypt::encrypt($user_id_arr[$i]);
			$arr['name'] = $name_arr[$i];
			$arr['org_name'] = $org_name_arr[$i];
			$arr['org_branch_id'] = $org_branch_id_arr[$i];
			$arr['user_id'] = $user_id_arr[$i];
			$arr['school_id'] = $college_id_arr[$i];

			if ($tp->getLastLoggedInDate($user_id_arr[$i]) == "N/A") {
				$arr['last_logged_in'] = 'N/A';
			}else{
				$arr['last_logged_in'] = $this->xTimeAgoHr($tp->getLastLoggedInDate($user_id_arr[$i]), date("Y-m-d H:i:s"));
			}
			$arr['num_of_inquiries'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_inquiries_arr);
			$arr['num_of_recommendations'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_recommendations_arr);

			$arr['total_num_of_pending_from_all_sources'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $total_num_of_pending_from_all_sources_arr);
			$arr['total_num_of_approved_by_pending'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $total_num_of_approved_by_pending_arr);
			if ($arr['total_num_of_pending_from_all_sources'] == 0) {
				$arr['percent_of_approved_by_pending'] = '0.00%';
			}else{
				$arr['percent_of_approved_by_pending'] = number_format(($arr['total_num_of_approved_by_pending'] / $arr['total_num_of_pending_from_all_sources']) * 100 , 2, '.', '').'%';
			}

			$arr['num_of_total_approved']  = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_approved_arr);

			$ret[] = $arr;
		}

		$data['clients_arr'] = $ret;

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
		return View('manageColleges.collegesReporting', $data);
	}

	private function getArrayValue($retField, $field, $val, $obj){

		foreach ($obj as $key) {

			if (!is_object($key)) {
				if (isset($key[$field]) && $key[$field] == $val) {
					return  $key[$retField];
				}
			}else{
				if (isset($key->$field) && $key->$field == $val) {
					return  $key->$retField;
				}
			}

		}
		return 0;
	}

	private function xTimeAgoHr( $oldTime, $newTime ) {
		$timeCalc = strtotime( $newTime ) - strtotime( $oldTime );
		$timeCalc = round( $timeCalc/60/60 );
		// if ( $timeCalc > ( 60*60 ) ) {$timeCalc = round( $timeCalc/60/60 ) . " hrs ago";}
		// else if ( $timeCalc > 60 ) {$timeCalc = round( $timeCalc/60 ) . " mins ago";}
		// else if ( $timeCalc > 0 ) {$timeCalc .= " secs ago";}
		return $timeCalc;
	}


	public function loginas($user_id = null){

		try {
			$user_id = Crypt::decrypt($user_id);
		} catch (\Exception $e) {
			return "Bad user id";
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if ($user_id == null) {
			return;
		}

		$ac = new AuthController();

		$ac->clearAjaxToken( Auth::user()->id );
		Auth::logout();
		Session::flush();

		Auth::loginUsingId( $user_id, true );
		$ac->setAjaxToken($user_id);
		Session::put('aor_log_back_in_user_id', Crypt::encrypt($data['user_id']));
		Session::put('userinfo.session_reset', 1);
		Session::put('sales_super_power', 1);
		Session::put('aor_log_back_in_user_id', Crypt::encrypt($data['user_id']));
		return redirect( '/admin' );
	}

	//AOR methods ends here

	public function getDashboardData() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$sales_super_power = Session::get('sales_super_power');

		// get conferences info
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
			break;
		}

		$result = array();

		$result['conferences'] = $ret;

        $result['is_admin_premium'] = $this->validateAdminPremium();

		// get info directly from $data
		$result['num_of_applications'] = (int)$data['num_of_applications'];
		$result['num_of_enrollments'] = (int)$data['num_of_enrollments'];
		$result['show_upgrade_button'] = isset($data['show_upgrade_button']) ? (int)$data['show_upgrade_button'] : 0;

		// get handshake stats info
		$rec = new Recruitment;

		$date1 = Carbon::now();
		$date2 = Carbon::now();
		$firstOfQuarter = $date1->firstOfQuarter()->toDateTimeString();
		$lastOfQuarter  = $date2->lastOfQuarter()->toDateTimeString();

		$has_premier_ended = 0;
		$now = Carbon::now();

		// If user has a department set, show results based on the filters they have set
		$raw_filter_qry = NULL;
		$org_qry        = NULL;

		if (isset($data['default_organization_portal'])) {
			$crf = new CollegeRecommendationFilters;
			$filter_qry = $crf->generateFilterQry($data);
			if (isset($filter_qry)) {
				$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
				$filter_qry = $filter_qry->join('recruitment as filterRec', 'filterRec.user_id', '=', 'userFilter.id');
				$filter_qry = $filter_qry->where('filterRec.college_id', $data['org_school_id']);

				$org_qry = $this->getRawSqlWithBindings($filter_qry);
				$raw_filter_qry = $filter_qry;
			}
		}

		// end of department set

		// GOALS CODES START HERE ***************************

		// calc approvedInfo
		// if(isset($data['premier_trial_end_date_ACTUAL']) && !empty($data['premier_trial_end_date_ACTUAL']) && $now->gt(Carbon::parse($data['premier_trial_end_date_ACTUAL']))){
		//    $has_premier_ended = 1;
		// }

		// if (isset($sales_super_power) || (isset($data['premier_trial_end_date']) && !empty($data['premier_trial_end_date']) && $has_premier_ended == 0)) {
		// 	$tmp = $rec->getNumOfTotalApprovedForColleges(array($data['org_school_id']), $firstOfQuarter, $lastOfQuarter, $raw_filter_qry);
		// 	foreach ($tmp as $key) {
		// 		$approvedQuarterly =  $key->cnt;
		// 	}
		// 	$data['approvedQuarterly'] =  isset($approvedQuarterly) ? (int)$approvedQuarterly : 0;

		// 	$firstDayYear = Carbon::now()->startOfYear()->toDateTimeString();
		// 	$tmp = $rec->getNumOfTotalApprovedForColleges(array($data['org_school_id']), $firstDayYear, NULL, $raw_filter_qry);
		// 	foreach ($tmp as $key) {
		// 		$approvedAnnually =  $key->cnt;
		// 	}
		// 	$data['approvedAnnually'] =  isset($approvedAnnually) ? (int)$approvedAnnually : 0;

		// 	$firstDayMonth = Carbon::now()->startOfMonth()->toDateTimeString();
		// 	$tmp = $rec->getNumOfTotalApprovedForColleges(array($data['org_school_id']), $firstDayMonth, NULL, $raw_filter_qry);
		// 	foreach ($tmp as $key) {
		// 		$approvedMonthly =  $key->cnt;
		// 	}
		// 	$data['approvedMonthly'] =  isset($approvedMonthly) ? (int)$approvedMonthly : 0;

		// 	$data['approvedGoal']  = ceil(($data['num_of_applications'] * 10 ) /12);

		// 	if( $data['approvedGoal'] > 0 ){
		// 		$temp_mon_perc = $data['approvedMonthly'] / $data['approvedGoal'];
		// 		$data['approvedMonthlyPerc'] = $temp_mon_perc > 1 ? 100 : (int)($temp_mon_perc * 100);

		// 		$temp_quart_perc = $data['approvedQuarterly'] / ($data['approvedGoal'] * 3);
		// 		$data['approvedQuarterlyPerc'] = $temp_quart_perc > 1 ? 100 : (int)($temp_quart_perc * 100);

		// 		$temp_annual_perc = $data['approvedAnnually'] / ($data['approvedGoal'] * 12);
		// 		$data['approvedAnnuallyPerc'] = $temp_annual_perc > 1 ? 100 : (int)($temp_annual_perc * 100);
		// 	}else{
		// 		$data['approvedMonthlyPerc'] = 0;
		// 		$data['approvedQuarterlyPerc'] = 0;
		// 		$data['approvedAnnuallyPerc'] = 0;
		// 	}

		// }else if(isset($data['premier_trial_end_date_ACTUAL']) && $has_premier_ended == 1){

		// 	$begin_date = $data['premier_trial_begin_date_ACTUAL'];
	    //           $end_date   = $data['premier_trial_end_date_ACTUAL'];
	    //           $datetime1 = new DateTime($begin_date);
	    //           $datetime2 = new DateTime($end_date);

	    //           $interval = $datetime1->diff($datetime2);

	    //           $num_of_days = $interval->format('%R%a');

	    //           $num_of_days = str_replace('+', '', $num_of_days);
	    //         	$num_of_days = ($num_of_days > 0) ? $num_of_days : 31;

		// 	$num_of_months = ceil(365 / $num_of_days);
		// 	$num_of_months = ($num_of_months > 0) ? $num_of_months : 12;
		// 	$data['approvedGoal']  = ceil(($data['num_of_applications'] * 10 ) / $num_of_months);

		// 	$tmp = $rec->getNumOfTotalApprovedForColleges(array($data['org_school_id']), $begin_date." 00:00:00", $end_date." 00:00:00", $raw_filter_qry);
		// 	foreach ($tmp as $key) {
		// 		$approvedMonthly =  $key->cnt;
		// 	}
		// 	$data['approvedMonthly'] =  isset($approvedMonthly) ? (int)$approvedMonthly : 0;


		// 	if( $data['approvedGoal'] > 0 ){
		// 		$temp_mon_perc = $data['approvedMonthly'] / $data['approvedGoal'];
		// 		$data['approvedMonthlyPerc'] = $temp_mon_perc > 1 ? 100 : (int)($temp_mon_perc * 100);

		// 		$data['approvedQuarterlyPerc'] = 0;
		// 		$data['approvedAnnuallyPerc'] = 0;
		// 	}else{
		// 		$data['approvedMonthlyPerc'] = 0;
		// 		$data['approvedQuarterlyPerc'] = 0;
		// 		$data['approvedAnnuallyPerc'] = 0;
		// 	}

		// 	$date = new DateTime($data['premier_trial_end_date_ACTUAL']);
		// 	$data['premier_trial_end_date_ACTUAL'] = $date->format('m/d/Y');
		// }

		// $applied = 0;
		// $enrolled = 0;
		// $app_perc = 0;
		// $enr_perc = 0;

		// $recrt = Recruitment::on('rds1')
		// 					->where('college_id', '=', $data['org_school_id']);

		// if (isset($raw_filter_qry)) {
	 	// 		$recrt = $recrt->join(DB::raw('('.$raw_filter_qry.')  as t2'), 't2.filterUserId' , '=', 'recruitment.user_id');
	 	// 	}

	 	// 	$applied = $recrt->where('applied', 1)
	 	// 					 ->count();

	 	// 	$enrolled = $recrt->where('enrolled', 1)
	 	// 					  ->count();

		// $result['current_application_count'] = (int)$applied;
		// $result['current_enrollement_count'] = (int)$enrolled;

		// if( $data['num_of_applications'] > 0 ){
		// 	$app_perc = $applied / $data['num_of_applications'];
		// 	$data['application_perc'] = $app_perc > 1 ? 100 : (int)($app_perc * 100) ;
		// }else{
		// 	$data['application_perc'] = 0;
		// }

		// if( $data['num_of_enrollments'] > 0 ){
		// 	$enr_perc = $enrolled / $data['num_of_enrollments'];
		// 	$data['enrollement_perc'] = $enr_perc > 1 ? 100 : (int)($enr_perc * 100) ;
		// }else{
		// 	$data['enrollement_perc'] = 0;
		// }

		// // --
		// $result['approvedGoal'] = isset($data['approvedGoal']) ? $data['approvedGoal'] : 0;
		// $result['approvedMonthly'] = isset($data['approvedMonthly']) ? $data['approvedMonthly'] : 0;
		// $result['approvedQuarterly'] = isset($data['approvedQuarterly']) ? $data['approvedQuarterly'] : 0;
		// $result['approvedAnnually'] = isset($data['approvedAnnually']) ? $data['approvedAnnually'] : 0;
		// $result['approvedMonthlyPerc'] = isset($data['approvedMonthlyPerc']) ? $data['approvedMonthlyPerc'] : 0;
		// $result['approvedQuarterlyPerc'] = isset($data['approvedQuarterlyPerc']) ? $data['approvedQuarterlyPerc'] : 0;
		// $result['approvedAnnuallyPerc'] = isset($data['approvedAnnuallyPerc']) ? $data['approvedAnnuallyPerc'] : 0;
		// $result['application_perc'] = $data['application_perc'];
		// $result['enrollement_perc'] = $data['enrollement_perc'];


		// GOALS CODES END HERE ***************************

		$result['premier_trial_end_date_ACTUAL'] = $data['premier_trial_end_date_ACTUAL'];

		$result['existing_client'] = (int)$data['existing_client'];

		$result['expiresIn'] = $this->setRecommendationExpirationDate();

		$this->leftBarNumbers($data, true);

		$result['inquiryCnt'] 		 = $this->num_of_inquiry_new;
		$result['inquiryCntTotal'] 	 = $this->num_of_inquiry;

		$result['recommendCnt'] 	 = $this->num_of_recommended_new;
		$result['recommendCntTotal'] = $this->num_of_recommended;

		$result['pendingCnt'] 		 = $this->num_of_pending_new;
		$result['pendingCntTotal']   = $this->num_of_pending;

		$result['approvedCnt'] 		 = $this->num_of_approved_new;
		$result['approvedCntTotal']  = $this->num_of_approved;

		$result['chatCntNew'] 	     = $this->getChatCnt();
		$result['chatCntTotal']  	 = $this->getChatCntTotal();
		
		$result['messageCntNew'] 	 = $this->getMessageCnt($raw_filter_qry);
		$result['messageCntTotal']   = $this->getMessageCntTotal($raw_filter_qry);
		
		$result['textCntNew']  	     = $this->getTxtCnt();
		$result['textCntTotal']  	 = $this->getTxtCnt();

		$result['campaignTotal']	 = $this->getCampaignTotal();

		$result['prescreenedCntTotal'] = $this->num_of_prescreened;

		$result['verifiedHsCnt'] 	   = $this->num_of_verified_hs_new;
		$result['verifiedHsCntTotal']  = $this->num_of_verified_hs;

		$result['verifiedAppCnt'] 	    = $this->num_of_verified_app_new;
		$result['verifiedAppCntTotal']  = $this->num_of_verified_app;
		// --

		// get show filter
		$data['is_superuser'] = false;
		// show filter if on bachelor plan
		$data['show_filter'] = ($data['bachelor_plan'] == 1);
		// if sales
		$sales_super_power = Session::get('sales_super_power');

		if (isset($sales_super_power)) {
			$data['show_filter'] = true;
			$data['is_superuser'] = true;
		}

		$result['is_superuser'] = $data['is_superuser'];
		$result['is_aor'] = (int)$data['is_aor'];
		$result['is_sales'] = isset($data['is_sales']) ? $data['is_sales'] : false;
		$result['appointment_set'] = (int)$data['appointment_set'];
		$result['show_filter'] = $data['show_filter'];

		$result['pending_hs_ratio'] = '0';
		if ($result['pendingCntTotal'] > 0) {
			$result['pending_hs_ratio'] = ceil(($result['approvedCntTotal'] / $result['pendingCntTotal']) * 100 );
		}

		$tp = new TrackingPage;
		$result['college_page_views'] = $this->custom_number_format($tp->getCollegePageViews($data['school_slug']));

		$result['avg_age_of_students'] = $rec->getAvgAgeOfStudents($data['org_school_id'], $data, $org_qry);
		
		$gender = $rec->getPercentageOfGender($data['org_school_id'], $data, $org_qry);
		$result['avg_num_of_male']   = isset($gender->males) ? $gender->males : 0;
		$result['avg_num_of_female'] = isset($gender->females) ? $gender->females : 0;
		$result['current_status'] = 'Free';

		$pri = new Priority;

		$pri = $pri->getCollegeClientType($data['org_school_id'], $data);

		if(isset($pri) && !empty($pri)){
			$result['current_status'] = $pri->contractName;
		}
		$pa = new PlexussAnnouncement;
		$result['announcements'] = $pa->getPlexussAnnouncement($data['user_id']);

		$ob = new OrganizationBranch;

		$ob = $ob->getCollegeMemberSince($data['org_school_id']);
		$member_since = $ob->created_at;
		$member_since = $member_since->toFormattedDateString();

		$result['member_since'] = $member_since;
		$result['fname'] = $data['fname'];
		$result['requested_upgrade'] = $data['requested_upgrade'];
		
		return $result;
	}

	// PreScreened begins here

	public function setInterviewStatus(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$org_portal_id = NULL;
		if (isset($data['default_organization_portal'])) {
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		$aor_portal_id = NULL;
		if (isset($data['aor_portal_id'])) {
			$aor_portal_id = $data['aor_portal_id'];
		}

		if (isset($input['interview_status']) && isset($input['user_id'])) {
			$pr = PrescreenedUser::where('college_id', $data['org_school_id'])
							 ->where('user_id', $input['user_id']);

			if (isset($org_portal_id)) {
				$pr = $pr->where('org_portal_id', $org_portal_id);
			}

			if (isset($aor_id)) {
				$pr = $pr->where('aor_id', $aor_id);
			}else{
				$pr = $pr->whereNull('aor_id');
			}

			if (isset($aor_portal_id)) {
				$pr = $pr->where('aor_portal_id', $aor_portal_id);
			}else{
				$pr = $pr->whereNull('aor_portal_id');
			}

			$pr = $pr->first();
			
			if (isset($pr)) {
				$is = 0;
				if ($input['interview_status'] == 'yes') {
					$is = 1;
				}elseif ($input['interview_status'] == 'no') {
					$is = -1;
				}

				$pr->interview_status = $is;
				$pr->save();

				return "success";
			}
			
		}

		return "failed";
	}
	
	public function setAppliedEnrolledPreScreened(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$now = Carbon::now();

		$input = Request::all();

		$org_portal_id = NULL;
		if (isset($data['default_organization_portal'])) {
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		$aor_portal_id = NULL;
		if (isset($data['aor_portal_id'])) {
			$aor_portal_id = $data['aor_portal_id'];
		}

		$pr = PrescreenedUser::where('college_id', $data['org_school_id'])
							 ->where('user_id', $input['user_id']);

		if (isset($org_portal_id)) {
			$pr = $pr->where('org_portal_id', $org_portal_id);
		}

		if (isset($aor_id)) {
			$pr = $pr->where('aor_id', $aor_id);
		}else{
			$pr = $pr->whereNull('aor_id');
		}

		if (isset($aor_portal_id)) {
			$pr = $pr->where('aor_portal_id', $aor_portal_id);
		}else{
			$pr = $pr->whereNull('aor_portal_id');
		}

		if (isset($input['user_applied_enrolled'])) {
			if ($input['user_applied_enrolled'] == 'applied') {
			
				$pr->update(array('applied' => DB::raw('NOT applied'),
								  'applied_at' => $now));

				return "success";
			}elseif ($input['user_applied_enrolled'] == 'enrolled') {
			
				$pr->update(array('enrolled' => DB::raw('NOT enrolled'),
								  'enrolled_at' => $now));

				return "success";
			}
		}

		return "failed";
	}
	// PreScreened ends here

	public function getCollegeInternationalTab(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$cit = new CollegesInternationalTab;

		return $cit->getCollegeInternationalTab($data['org_school_id']);
	}

	public function getInternatioanlTuitionCosts(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$citc = CollegesInternationalTuitionCost::on('rds1')->where('college_id', $data['org_school_id'])->first();

		$ret = array();

		if (isset($citc)) {
			$ret['undergrad_application_fee'] = (int)$citc->undergrad_application_fee ;
			$ret['undergrad_avg_tuition'] 	  = (int)$citc->undergrad_avg_tuition ;
			$ret['undergrad_other_cost']      = (int)$citc->undergrad_other_cost ;
			$ret['undergrad_avg_scholarship'] = (int)$citc->undergrad_avg_scholarship ;
			$ret['undergrad_avg_work_study']  = (int)$citc->undergrad_avg_work_study ;
			$ret['undergrad_other_financial'] = (int)$citc->undergrad_other_financial ;
			$ret['grad_application_fee'] 	  = (int)$citc->grad_application_fee ;
			$ret['grad_avg_tuition'] 		  = (int)$citc->grad_avg_tuition ;
			$ret['grad_other_cost'] 		  = (int)$citc->grad_other_cost ;
			$ret['grad_avg_scholarship'] 	  = (int)$citc->grad_avg_scholarship ;
			$ret['grad_avg_work_study'] 	  = (int)$citc->grad_avg_work_study ;
			$ret['grad_other_financial'] 	  = (int)$citc->grad_other_financial ;
			$ret['epp_application_fee'] 	  = (int)$citc->epp_application_fee ;
			$ret['epp_avg_tuition'] 		  = (int)$citc->epp_avg_tuition ;
			$ret['epp_other_cost'] 		  	  = (int)$citc->epp_other_cost ;
			$ret['epp_avg_scholarship'] 	  = (int)$citc->epp_avg_scholarship ;
			$ret['epp_avg_work_study'] 	  	  = (int)$citc->epp_avg_work_study ;
			$ret['epp_other_financial'] 	  = (int)$citc->epp_other_financial ;
			$ret['quick_tip'] 	  			  = $citc->quick_tip ;
			$ret['company_logo'] 	  	  	  = $citc->company_logo_file ;

		}
		return $ret;
	}

	public function forgetdismissPlexussAnnouncement(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
		Cache::forget(env('ENVIRONMENT') .'_dismissPlexussAnnouncement_'.$data['user_id']);;
	}

	public function getOverviewToolsTab(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$ret = array();
		$ret['overview'] = array();
        $overview_qry = DB::connection('rds1')->table('colleges as c')
        				  ->join('college_overview_images as coi', 'c.id', '=', 'coi.college_id')
        				  ->where('c.id', $data['org_school_id'])
        				  ->where('coi.is_tour', 0)
        				  ->select('c.overview_content', 'c.overview_source', 'c.slug',
        				  		   'coi.url', 'coi.is_video', 'coi.section', 
        				  		   'coi.video_id', 'coi.is_youtube', 'coi.id')
        				  ->get();

        foreach ($overview_qry as $key) {
        	$ret['overview']['route'] = '/college/'.$key->slug.'/overview';
        	$ret['overview']['content']['overview_content'] = $key->overview_content;
        	$ret['overview']['content']['overview_source']  = $key->overview_source;
        	if ($key->is_video == 0) {
        		$tmp = array();
        		$tmp['id']  = Crypt::encrypt($key->id);
        		$tmp['url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/'.$key->url;

        		$ret['overview']['images'][] = $tmp;
        	}else{
        		if (isset($key->video_id)) {
        			$tmp = array();
	        		$tmp['id']         = Crypt::encrypt($key->id);
	        		$tmp['section']    = $key->section;
	        		$tmp['video_id']   = $key->video_id;
	        		$tmp['is_youtube'] = (int)$key->is_youtube;

	        		$ret['overview']['videos'][] = $tmp;

        		}
        		
        	}
        }

        return $ret;
	}

    public function testConverted() {
        $ro_id = 13;
        $student_user_id = 1096884;

        $campaigns = AdRedirectCampaign::on('rds1')
                                       ->select('company')
                                       ->where('ro_id', '=', $ro_id)
                                       ->pluck('company')
                                       ->all();

        $converted = AdClick::on('rds1')
                            ->select('id')
                            ->where('user_id', '=', $student_user_id)
                            ->whereIn('company', $campaigns)
                            ->first();

        dd($converted->id);
    }

    public function moveStudentToConverted() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $currentPortal = $data['default_organization_portal'];

        $converted_id = null;

        $input = Request::all();

        try {
            $student_user_id = Crypt::decrypt($input['user_id']);
        } catch (\Exception $e) {
            return 'failed getting user id';
        }

        if (!isset($input['rec_id'])) {
            return 'failed getting recruitment id';
        }

        if (isset($currentPortal)) {
            switch ($currentPortal->ro_type) {
                case 'click':
                    $campaigns = AdRedirectCampaign::on('rds1')
                                                   ->select('company')
                                                   ->where('ro_id', '=', $currentPortal->ro_id)
                                                   ->pluck('company')
                                                   ->all();

                    if (!isset($campaigns)) return 'failed retrieving campaigns';

                    $converted = AdClick::on('rds1')
                                        ->select('id')
                                        ->where('user_id', '=', $student_user_id)
                                        ->whereIn('company', $campaigns)
                                        ->first();

                    if (isset($converted)) {
                        $converted_id = $converted->id;
                    }

                    break;

                case 'post':
                    $converted = DistributionResponse::on('rds1')
                                                     ->select('id')
                                                     ->where('user_id', '=', $student_user_id)
                                                     ->where('ro_id', '=', $currentPortal->ro_id)
                                                     ->first();

                    if (isset($converted)) {
                        $converted_id = $converted->id;
                    }

                    break;

                default: 
                    $converted_id = null;

                    break;
            }
        }

        $attributes = [
            'rec_id' => $input['rec_id'],
            'converted_id' => $converted_id,
        ];

        $values = [
            'rec_id' => $input['rec_id'],
            'converted_id' => $converted_id,
        ];

        RecruitmentConvert::updateOrCreate($attributes, $values);

        Recruitment::where('id', '=', $input['rec_id'])
                   ->update(['user_recruit' => 0, 'college_recruit' => 0]);

        if (isset($input['rec_id']) && isset($currentPortal->ro_id)) {
        	$attr = array('rec_id' => $input['rec_id'], 'ro_id' => $currentPortal->ro_id);
        	$val  = array('rec_id' => $input['rec_id'], 'ro_id' => $currentPortal->ro_id);

        	RecruitmentRevenueOrgRelation::updateOrCreate($attr, $val);
        }

        return 'success';
    }

	// Save verified handshake
	public function saveVerifiedHandShake(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
		$input = Request::all();

		if (!isset($input['user_id'])) {
			return "failed";
		}

		try {
			$user_id = Crypt::decrypt($input['user_id']);

		} catch (\Exception $e) {
			$user_id = $input['user_id'];	
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		$aor_portal_id = NULL;
		if (isset($data['aor_portal_id'])) {
			$aor_portal_id = $data['aor_portal_id'];
		}

		$org_portal_id = NULL;
		if(isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])){
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$prev_state = $input['page'];
		$rec_id     = $input['rec_id']; 

		$attr = array('user_id' => $user_id, 'college_id' => $data['org_school_id'], 'aor_id' => $aor_id, 'aor_portal_id' => $aor_portal_id,
					  'org_portal_id' => $org_portal_id);
		$val  = array('user_id' => $user_id, 'college_id' => $data['org_school_id'], 'aor_id' => $aor_id, 'aor_portal_id' => $aor_portal_id, 
					  'org_portal_id' => $org_portal_id, 'prev_state' => $prev_state, 'rec_id' => $rec_id);

		$update =  RecruitmentVerifiedHS::updateOrCreate($attr, $val);

		$this->setHideRecruitment($prev_state, $rec_id);

		// Add to Plexuss portals
		$this_rec_id = $this->setRecruitmentTableOrPrescreenedTableForPlexuss($attr, $prev_state);
		$this->saveVerifiedHandShakeForPlexuss($attr, $val, $this_rec_id);

		return "success";
	}

	// Save verified application
	public function saveVerifiedApplication($data = NULL, $input = NULL){
		if (!isset($data)) {
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);
		}
		
		if (!isset($input)) {
			$input = Request::all();
		}
	
		if (!isset($input['user_id'])) {
			return "failed";
		}

		try {
			$user_id = Crypt::decrypt($input['user_id']);

		} catch (\Exception $e) {
			$user_id = $input['user_id'];	
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		$aor_portal_id = NULL;
		if (isset($data['aor_portal_id'])) {
			$aor_portal_id = $data['aor_portal_id'];
		}

		$org_portal_id = NULL;
		if(isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])){
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$prev_state = $input['page'];
		$rec_id     = $input['rec_id']; 

		$attr = array('user_id' => $user_id, 'college_id' => $data['org_school_id'], 'aor_id' => $aor_id, 'aor_portal_id' => $aor_portal_id,
					  'org_portal_id' => $org_portal_id);
		$val  = array('user_id' => $user_id, 'college_id' => $data['org_school_id'], 'aor_id' => $aor_id, 'aor_portal_id' => $aor_portal_id, 
					  'org_portal_id' => $org_portal_id, 'prev_state' => $prev_state, 'rec_id' => $rec_id);

		
		$update =  RecruitmentVerifiedApp::updateOrCreate($attr, $val);

		$this->setHideRecruitment($prev_state, $rec_id);

		// Add to Plexuss portals
		$this_rec_id = $this->setRecruitmentTableOrPrescreenedTableForPlexuss($attr, $prev_state);
		$this->saveVerifiedHandShakeForPlexuss($attr, $val, $this_rec_id);

		return "success";
	}

	// Undo verified handshake
	public function undoVerifiedHandShake(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (!isset($input['user_id'])) {
			return "failed";
		}

		try {
			$user_id = Crypt::decrypt($input['user_id']);

		} catch (\Exception $e) {
			$user_id = $input['user_id'];	
		}

		$qry = RecruitmentVerifiedHS::where('user_id', $user_id)
									   ->where('college_id', $data['org_school_id']);

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		if (isset($aor_id)) {
			$qry = $qry->where('aor_id', $aor_id);
		}else{
			$qry = $qry->whereNull('aor_id');
		}

		$aor_portal_id = NULL;
		if (isset($data['aor_portal_id'])) {
			$aor_portal_id = $data['aor_portal_id'];
		}

		if (isset($aor_portal_id)) {
			$qry = $qry->where('aor_portal_id', $aor_portal_id);
		}else{
			$qry = $qry->whereNull('aor_portal_id');
		}

		$org_portal_id = NULL;
		if(isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])){
			$org_portal_id = $data['default_organization_portal']->id;
		}

		if (isset($org_portal_id)) {
			$qry = $qry->where('org_portal_id', $org_portal_id);
		}else{
			$qry = $qry->whereNull('org_portal_id');
		}

		$prev_state = $qry->first();

		$qry = $qry->delete();

		$this->bringBackPrevStateOfRecruitment($prev_state->prev_state, $prev_state->rec_id);
		
		$arr = array();

		$arr['msg']  = 'success';
		$arr['page'] = $prev_state->prev_state;

		return json_encode($arr);
	}

	// Save verified application
	public function undoVerifiedApplication(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (!isset($input['user_id'])) {
			return "failed";
		}

		try {
			$user_id = Crypt::decrypt($input['user_id']);

		} catch (\Exception $e) {
			$user_id = $input['user_id'];	
		}

		$qry =  RecruitmentVerifiedApp::where('user_id', $user_id)
									     ->where('college_id', $data['org_school_id']);

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		if (isset($aor_id)) {
			$qry = $qry->where('aor_id', $aor_id);
		}else{
			$qry = $qry->whereNull('aor_id');
		}

		$aor_portal_id = NULL;
		if (isset($data['aor_portal_id'])) {
			$aor_portal_id = $data['aor_portal_id'];
		}

		if (isset($aor_portal_id)) {
			$qry = $qry->where('aor_portal_id', $aor_portal_id);
		}else{
			$qry = $qry->whereNull('aor_portal_id');
		}

		$org_portal_id = NULL;
		if(isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])){
			$org_portal_id = $data['default_organization_portal']->id;
		}

		if (isset($org_portal_id)) {
			$qry = $qry->where('org_portal_id', $org_portal_id);
		}else{
			$qry = $qry->whereNull('org_portal_id');
		}
		
		$prev_state = $qry->first();

		$qry = $qry->delete();

		$this->bringBackPrevStateOfRecruitment($prev_state->prev_state, $prev_state->rec_id);

		$arr = array();

		$arr['msg']  = 'success';
		$arr['page'] = $prev_state->prev_state;

		return json_encode($arr);
	}
		// Save verified handshake for plexuss
	private function saveVerifiedHandShakeForPlexuss($attr, $val, $rec_id){

		$attr['college_id'] 	= 7916;
		$attr['aor_id']			= NULL;
		$attr['aor_portal_id']	= NULL;
		$attr['org_portal_id']	= NULL;
		$attr['rec_id']			= $rec_id;

		$val['college_id'] 		= 7916;
		$val['aor_id']			= NULL;
		$val['aor_portal_id']	= NULL;
		$val['org_portal_id']	= NULL;
		$val['rec_id']			= $rec_id;

		$update =  RecruitmentVerifiedHS::updateOrCreate($attr, $val);
	}

	// Save verified application for plexuss
	private function saveVerifiedApplicationForPlexuss($attr, $val, $rec_id){

		$attr['college_id'] 	= 7916;
		$attr['aor_id']			= NULL;
		$attr['aor_portal_id']	= NULL;
		$attr['org_portal_id']	= NULL;
		$attr['rec_id']			= $rec_id;

		$val['college_id'] 		= 7916;
		$val['aor_id']			= NULL;
		$val['aor_portal_id']	= NULL;
		$val['org_portal_id']	= NULL;
		$val['rec_id']			= $rec_id;

		$update =  RecruitmentVerifiedApp::updateOrCreate($attr, $val);
	}

	// Recruitment table or Prescreen Table for Plexuss Portal 7916
	private function setRecruitmentTableOrPrescreenedTableForPlexuss($attr, $prev_state){
		
		if ($prev_state == 'admin-prescreened') {
			$rec  = PrescreenedUser::where('college_id', 7916)
								  ->where('user_id', $attr['user_id'])
								  ->first();

			if (isset($rec)) {
				$rec->active = 0;
				$rec->save();

			}else{
				$rec 			= new PrescreenedUser;
				$rec->user_id    = $attr['user_id'];
				$rec->college_id = 7916;
				$rec->active = 0;

				$rec->save();
			}
			
		}else {

			$rec  = Recruitment::where('college_id', 7916)
								  ->where('user_id', $attr['user_id'])
								  ->first();

			if (isset($rec)) {

				// I dont want someone to get increased to bunch of numbers there.
				// this is only for hiding the recruitment from any bucket.
				if ($rec->college_recruit < 9) {
					$rec->college_recruit = $rec->college_recruit + 10;
				}
				if ($rec->user_recruit < 9) {
					$rec->user_recruit    = $rec->user_recruit + 10;
				}
				$rec->status          = 0;

				$rec->save();


			}else{
				$rec = new Recruitment;
				
				$rec->user_id         = $attr['user_id'];
				$rec->college_id      = 7916;
				$rec->college_recruit = 11;
				$rec->user_recruit    = 11;
				$rec->status          = 0;

				$rec->save();

			}
		}
		
		return $rec->id;
	}

	// Save verified prescreened
	public function savePrescreenedUser(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
	
		if (!isset($input['user_id'])) {
			return "failed";
		}

		try {
			$user_id = Crypt::decrypt($input['user_id']);

		} catch (\Exception $e) {
			$user_id = $input['user_id'];	
		}

		$aor_id = NULL;
		if (isset($data['aor_id'])) {
			$aor_id = $data['aor_id'];
		}

		$aor_portal_id = NULL;
		if (isset($data['aor_portal_id'])) {
			$aor_portal_id = $data['aor_portal_id'];
		}

		$prev_state = $input['page'];
		$rec_id     = $input['rec_id']; 

		$org_portal_id = NULL;
		if(isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])){
			$org_portal_id = $data['default_organization_portal']->id;
		}

		$attr = array('user_id' => $user_id, 'college_id' => $data['org_school_id'], 'aor_id' => $aor_id, 'org_portal_id' => $org_portal_id,
					  'aor_portal_id' => $aor_portal_id);
		$val  = array('user_id' => $user_id, 'college_id' => $data['org_school_id'], 'aor_id' => $aor_id, 'org_portal_id' => $org_portal_id,
					  'aor_portal_id' => $aor_portal_id, 'prev_state' => $prev_state, 'rec_id' => $rec_id, 'active' => 1);

		$update =  PrescreenedUser::updateOrCreate($attr, $val);

		$this->setHideRecruitment($prev_state, $rec_id);

		return "success";
	}

	// Save plexuss status
	public function savePlexussUserVerificationStatus(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (!isset($input['user_id'])) {
			return "failed";
		}

		$attr   = array('user_id' => $input['user_id']); 
		$val    = array('user_id' => $input['user_id'], 'status' => $input['status']);

		$update = PlexussVerificationsUser::updateOrCreate($attr, $val);

		return "success";
	}

	// Undo Plexuss status
	public function undoPlexussUserVerificationStatus(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		if (!isset($input['user_id'])) {
			return "failed";
		}

		$update = PlexussVerificationsUser::where('user_id', $input['user_id'])
										  ->first();

		$update->status = NULL;

		$update->save();

		return "success";
	}
	
	/*
	 * This method brings back previous state of a recruitment. ( inquiry, handshake, pending, etc..).
	 */
	private function bringBackPrevStateOfRecruitment($prev_state, $rec_id){

		switch ($prev_state) {
			case 'admin-inquiries':
				
				$rec = Recruitment::find($rec_id);

				$rec->college_recruit = 0;
				$rec->user_recruit    = 1;
				$rec->status   		  = 1;

				$rec->save();

				break;
			
			case 'admin-recommendations':
				# code...
				break;

			case 'admin-pending':

				$rec = Recruitment::find($rec_id);

				$rec->college_recruit = 1;
				$rec->user_recruit    = 0;
				$rec->status   		  = 1;

				$rec->save();

				break;

			case 'admin-approved':

				$rec = Recruitment::find($rec_id);

				$rec->college_recruit = 1;
				$rec->user_recruit    = 1;
				$rec->status   		  = 1;

				$rec->save();

				break;

			case 'admin-prescreened':
				
				$rec = PrescreenedUser::find($rec_id);
				$rec->active   		  = 1;

				$rec->save();

				break;
			
			case 'admin-removed':
				
				$rec = Recruitment::find($rec_id);

				$rec->college_recruit = $rec->college_recruit - 10;
				$rec->user_recruit    = $rec->user_recruit - 10;
				$rec->status   		  = 0;

				$rec->save();

				break;

			case 'admin-rejected':
				
				$rec = Recruitment::find($rec_id);

				$rec->college_recruit = -1;
				$rec->user_recruit    = 1;
				$rec->status   		  = 1;

				$rec->save();

				break;
		}
		
		return "success";
	}

	/*
	 * This method sets the rec id to all null, so it wont be shown in any of the buckets( inquiry, handshake, pending, etc..).
	 */
	private function setHideRecruitment($prev_state, $rec_id){

		if ($prev_state == 'admin-prescreened') {
			$rec = PrescreenedUser::find($rec_id);

			$rec->active = 0;

			$rec->save();
		} elseif (isset($rec_id) && $rec_id != '') {
			$rec = Recruitment::find($rec_id);

			// I dont want someone to get increased to bunch of numbers there.
			// this is only for hiding the recruitment from any bucket.
			if ($rec->college_recruit < 9) {
				$rec->college_recruit = $rec->college_recruit + 10;
			}
			if ($rec->user_recruit < 9) {
				$rec->user_recruit    = $rec->user_recruit + 10;
			}
			$rec->status          = 0;

			$rec->save();
		}
		
		return "success";
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
		$obj   = DB::table('objectives')->where('user_id', $user_id);
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

                case 'zip':
                    $user->zip = $value;
                    
                    $user_check = true;
                    break;

				case 'majors':
					$this_obj   = Objective::on('rds1')->where('user_id', $user_id)->first();
					$delete_obj = Objective::where('user_id', $user_id)->delete();
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
			$obj = $obj->update($obj_arr);
		}

		$this->CalcIndicatorPercent($user_id);
		$this->CalcProfilePercent($user_id);
		$this->CalcOneAppPercent($user_id);

		return "success";
	}

	public function hasAppliedToFilterAutoComplete(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$ret = array();
		if (!isset($data['aor_id']) && ($data['is_plexuss'] !=1 && $data['org_school_id'] != 7916)) {
			$ret['status'] = 'failed';
			$ret['error']  = "You need to be an aor or plexuss";

			return json_encode($ret);
		}
		$input = Request::all();

		if (!isset($input['input'])) {
			$ret['status'] = 'failed';
			$ret['error']  = "Input is needed";

			return json_encode($ret);
		}

		$term = $input['input'];

		if (isset($data['aor_id'])) {
			$qry = DB::connection('rds1')->table('colleges as c')
										 ->join('aor_colleges as ac', 'ac.college_id', '=', 'c.id')
										 ->select('c.id', 'c.school_name')
										 ->where('ac.aor_id', $data['aor_id'])
										 ->where(function ($q) use ($term) {
										 		$q->orWhere('c.school_name', 'like', '%'.$term.'%' );
												$q->orWhere('c.alias', 'like', '%'.$term.'%');
										 })
										 ->take(10)
										 ->get();
		}else{
			// Show schools in priority table
			$qry = DB::connection('rds1')->table('colleges as c')
										 ->join('priority as p', 'c.id', '=', 'p.college_id')
										 ->select('c.id', 'c.school_name')
										 ->where(function ($q) use ($term) {
										 		$q->orWhere('c.school_name', 'like', '%'.$term.'%' );
												$q->orWhere('c.alias', 'like', '%'.$term.'%');
										 })
										 ->whereNotNull('p.financial_filter')
										 ->whereNotNull('p.financial_filter_order')
										 ->groupBy('c.id')
										 ->take(10)
										 ->get();
		}
		

		$dt = array();

		foreach ($qry as $key) {
			$tmp = array();

			$tmp['id']          = $key->id;
			$tmp['school_name'] = $key->school_name;

			$dt[] = $tmp;
		}

		$ret['status'] = "success";
		$ret['data']   = $qry;

		return $ret;
	}

	public function addUserAppliedCollege(){
		try 
		{
			$input = Request::all();

			if (!isset($input['hashed_id']) || !isset($input['college_id'])) { return 'failed'; }

			$user_id = Crypt::decrypt($input['hashed_id']);

			$attributes = [ 'user_id' => $user_id, 'college_id' => $input['college_id'] ];

			$appliedCollege = new UsersAppliedColleges();
			$appliedCollege->user_id = $user_id;
			$appliedCollege->college_id = $input['college_id'];
			$appliedCollege->submitted = 1;
			$appliedCollege->save();
			
			return 'success';
			
		} 
		catch (\Exception $e) 
		{
			return 'fail';
			
		}
		
	}

	public function removeUserAppliedCollege(){
		$input = Request::all();

		if (!isset($input['hashed_id']) || !isset($input['college_id'])) { return 'failed'; }

		$user_id = Crypt::decrypt($input['hashed_id']);

		// Remove from applied colleges
		UsersAppliedColleges::removeUserAppliedCollege($user_id, $input['college_id']);

		// Remove application status
		CollegesApplicationStatus::where('user_id', $user_id)
								 ->where('college_id', $input['college_id'])
								 ->delete();

		Cache::put(env('ENVIRONMENT') .'_'. $user_id . '_session_reset', 1, 60);

		return 'success';
	}

	public function updateUserApplicationState(){
		$input = Request::all();

		if (!isset($input['hashed_id']) || !isset($input['state']) || !isset($input['prev_state'])) { return 'failed'; }

		$user_id = Crypt::decrypt($input['hashed_id']);

		$attributes = [ 'user_id' => $user_id ];
		$values = [ 'application_state' => $input['state'] ];

		UsersCustomQuestion::updateOrCreate($attributes, $values);

		$cas = new CollegesApplicationsState;
        $application_states = $cas->getAllApplicationState();

        $states_array = array_keys($application_states);

        $prev_state_position = array_search($input['prev_state'], $states_array);

        $state_position = array_search($input['state'], $states_array);

        // Send promote or demote email to user depending on OneApp state transition
        if ($input['prev_state'] == 'oneapp_state' || $prev_state_position < $state_position) {
			$mac = new MandrillAutomationController();
			$mac->promoteOneAppEmailForUsers($user_id, $input['state']);

        } else if ($prev_state_position > $state_position) {
			$mac = new MandrillAutomationController();
			$mac->demoteOneAppEmailForUsers($user_id, $input['state']);

        } // Do not send email if changed to the same state again.

		return 'success';
	}

	public function removePrescreenedUser($rid){

		try {
			$rid = Crypt::decrypt($rid);
		} catch (\Exception $e) {
			return "Invalid rid!";
		}

		$pu = PrescreenedUser::find($rid);
		$pu->delete();

		return "success";
	}

	// public function getMatchedCollegesForThisUser($input = NULL, $is_api = NULL, $offset = NULL){

	// 	$viewDataController = new ViewDataController();
	// 	$data = $viewDataController->buildData(true);

	// 	if (isset($is_api)) {
			
	// 		return $ret;
	// 	}

	// 	$input = Request::all();
		
	// 	$studentId = $input['user_id'];

	// 	$applied = DB::connection('rds1')->table('organization_portals as op')
	// 									 ->leftJoin('users_applied_colleges as usc','usc.user_id','=',DB::Raw('\''.$studentId.'\''))
	// 									 ->select('op.name', 'col.school_name','col.logo_url', 'usc.submitted','ac.college_id','usc.user_id','col.slug');

	// 	if (isset($data['organization_portals']->ro_id)) {

	// 		$applied = $applied->join('colleges as col','col.id', '=', 'ac.college_id')
	// 		                   ->join('revenue_organizations as rev','rev.id', '=', 'op.ro_id')
	// 						   ->join('aor_colleges as ac','ac.aor_id', '=', 'rev.aor_id')
	// 						   ->where('rev.id', $data['organization_portals']->ro_id);

	// 	}elseif (isset($data['aor_id']) && !empty($data['aor_id'])) {
	// 		$applied = $applied->join('colleges as col','col.id', '=', 'ac.college_id')
	// 		                   ->join('aor_colleges as ac','ac.aor_id', '=', DB::raw($data['aor_id']));

	// 	}else{
	// 		$applied = $applied->where('usc.college_id', $data['org_school_id']);
	// 	}

	// 	$applied = $applied->get();		
		
	// 	return response()->json($applied);
	// }

	public function getMatchedCollegesForThisUser($input = NULL, $is_api = NULL, $offset = NULL){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
		if (!isset($input)) {
			$input = Request::all();
		}
		
		$user_id = $input['user_id'];
		$ret = array();
		if((Session::has('handshake_power') && $data['is_plexuss'] == 1) || isset($is_api)){
			$crc = new CollegeRecommendationController;
			$matched_colleges = $crc->findCollegesForThisUser($user_id, true, NULL, $offset);
			
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
			
		}
		if (isset($is_api)) {
			return $ret;
		}
		return json_encode($ret);
	}


	public function getPreviousCalls(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$input['user_id'] = Crypt::decrypt($input['user_id']);
		
		//dd($input['user_id']);	
		$pl = PhoneLog::on('rds1')->where('org_branch_id', $data['org_branch_id'])
								  ->where('receiver_user_id', $input['user_id'])
								  ->orderBy('created_at', 'desc')
								  ->get();


		$ret = array();

		foreach ($pl as $key) {
			$tmp = array();

            $user = User::select('fname', 'lname')
                        ->where('id', $key->sender_user_id)
                        ->first();

            $tmp['caller_name']        = $user->fname . ' ' . ucwords($user->lname[0]);
			$tmp['recording_url']      = $key->recording_url;
			$tmp['recording_duration'] = $key->recording_duration;
			$tmp['call_date']          = $this->xTimeAgo($key->updated_at ,date("Y-m-d H:i:s"));
			$tmp['call_status']        = $key->status;

			$ret[] = $tmp;
		}

		return json_encode($ret);
	}


	public function getThisStudentThreads(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		try {
			$user_id = Crypt::decrypt($input['user_id']);
		} catch (\Exception $e) {
			return "Bad user id";
		}

		$qry = DB::connection('rds1')->table('college_message_thread_members as cmtm')
									 ->join(DB::raw("(
														SELECT
															thread_id
														FROM
															college_message_thread_members as cmtm
														WHERE
															cmtm.user_id = ".$data['user_id']."
														AND cmtm.org_branch_id = ".$data['org_branch_id']."
													) as ct"), 'ct.thread_id', '=', 'cmtm.thread_id')
									 ->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
									 ->where('cmtm.user_id', $user_id)
									 ->where('cmt.is_chat', 0)
									 ->groupBy('cmt.id')
									 ->select('cmt.*')
									 ->get();

		$ret = array();

		foreach ($qry as $key) {
			$tmp = array();

			$tmp['thread_id'] = $key->id;
			$tmp['hashed_thread_id'] = hash('crc32b', $key->id);

			if ($key->has_text == 1) {
				$tmp['thread_type'] = 'text_thread';
			}elseif (isset($key->campaign_id)) {
				$tmp['thread_type'] = 'campaign_thread';
			}else{
				$tmp['thread_type'] = 'msg_thread';
			}

			$ret[] = $tmp;
			
		}

		return json_encode($ret);
	}

	public function initDashboardStats($block = null){
		if( !isset($block) ) return 'failed';

		$data = array();
		$data['block'] = $block;

		switch($block){
			case 'pendingHandshake_stat':
				$data['list'] = 'stats';
				$data['stats'] = $this->getPendingHandshakeStatData();
				break;

			case 'avgAge_stat':
				$data['list'] = 'stats';
				$data['stats'] = $this->getAvgAgeStatData();
				break;

			case 'male_stat':
				$data['list'] = 'stats';
				$data['stats'] = $this->getMaleStatData();
				break;

			case 'female_stat':
				$data['list'] = 'stats';
				$data['stats'] = $this->getFemaleStatData();
				break;

			case 'pageviews_stat':
				$data['list'] = 'stats';
				$data['stats'] = $this->getPageViewsStatData();
				break;

			case 'admin_data':
				$data['stats'] = $this->getAdminDataData();
				break;	

			case 'announcements':
				$data['stats'] = $this->getAnnouncementData();
				break;
			
			case 'verifiedHs':
				$data['list'] = 'verified';
				$data['stats'] = $this->getVerifiedHsData();
				break;

			case 'prescreened':
				$data['list'] = 'verified';
				$data['stats'] = $this->getVerifiedPrescreenedData();
				break;

			case 'verifiedApp':
				$data['list'] = 'verified';
				$data['stats'] = $this->getVerifiedAppData();
				break;

			case 'inquiry':
				$data['list'] = 'recruitment';
				$data['stats'] = $this->getInquiryData();
				break;

			case 'recommend':
				$data['list'] = 'recruitment';
				$data['stats'] = $this->getRecommendData();
				break;

			case 'pending':
				$data['list'] = 'recruitment';
				$data['stats'] = $this->getPendingData();
				break;

			case 'approved':
				$data['list'] = 'recruitment';
				$data['stats'] = $this->getApprovedData();
				break;

			case 'message':
				$data['list'] = 'communication';
				$data['stats'] = $this->getMessageData();
				break;

			case 'text':
				$data['list'] = 'communication';
				$data['stats'] = $this->getTextData();
				break;

			case 'campaign':
				$data['list'] = 'communication';
				$data['stats'] = $this->getCampaignData();
				break;

			case 'chat':
				$data['list'] = 'communication';
				$data['stats'] = $this->getChatData();
				break;

			default: return 'failed';	
		}

		return $data;
	}

	private function getVerifiedHsData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$result['verifiedHsCnt'] 	   = $this->runDashboardQry('verified-hs-new', $data);
		$result['verifiedHsCntTotal']  = $this->runDashboardQry('verified-hs', $data);

		return $result;
	}

	private function getVerifiedPrescreenedData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$pr = new PrescreenedUser;
		$data['college_id'] = $data['org_school_id'];
		$data['user_id'] 	= NULL;
		$result['prescreenedCntTotal'] = $pr->getTotalNumOfPrescreened($data); 


		return $result;
	}

	private function getVerifiedAppData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$result['verifiedAppCnt'] 	   = $this->runDashboardQry('verified-app-new', $data);	
		$result['verifiedAppCntTotal'] = $this->runDashboardQry('verified-app', $data);

		return $result;
	}

	private function getInquiryData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$result['inquiryCnt'] 		 = $this->runDashboardQry('inquiry-new', $data);
		$result['inquiryCntTotal'] 	 = $this->runDashboardQry('inquiry', $data);
		$result['convertedCnt']      = $this->runDashboardQry('converted', $data);
		$result['verifiedCount']     = 0;

		return $result;
	}

	private function getRecommendData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();


		$result['existing_client']   = (int)$data['existing_client'];
		$result['expiresIn'] 	     = $this->setRecommendationExpirationDate();
		$result['recommendCnt'] 	 = $this->getRecommendCnt($data);
		$result['recommendCntTotal'] = $this->getRecommendCntTotal($data);

		return $result;
	}

	private function getPendingData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$result['pendingCnt'] 		 = $this->runDashboardQry('pending-new', $data);
		$result['pendingCntTotal']   = $this->runDashboardQry('pending', $data);

		return $result;
	}

	private function getApprovedData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$result['approvedCnt'] 		 = $this->runDashboardQry('approved-new', $data);
		$result['approvedCntTotal']  = $this->runDashboardQry('approved', $data);

		return $result;
	}

	private function getMessageData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

        $org_branch_id = $data['org_branch_id'];
        $portal_id = isset($data['default_organization_portal']) ? $data['default_organization_portal']->id : 'general';
		// If user has a department set, show results based on the filters they have set
		$raw_filter_qry = NULL;
		$org_qry        = NULL;
        $messageCnt = 0;
        $messageCntTotal = 0;

		if (isset($data['default_organization_portal'])) {
			$crf = new CollegeRecommendationFilters;
			$filter_qry = $crf->generateFilterQry($data);
			if (isset($filter_qry)) {
				$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
				$filter_qry = $filter_qry->join('recruitment as filterRec', 'filterRec.user_id', '=', 'userFilter.id');
				$filter_qry = $filter_qry->where('filterRec.college_id', $data['org_school_id']);

				$org_qry = $this->getRawSqlWithBindings($filter_qry);
				$raw_filter_qry = $filter_qry;
			}
		}

		$result = array();

        if (Cache::has(env('ENVIRONMENT') . '_admin_dashboard_count_for_messageCnt_' . $org_branch_id . '_' . $portal_id)) {
            $messageCnt = Cache::get(env('ENVIRONMENT') . '_admin_dashboard_count_for_messageCnt_' . $org_branch_id . '_' . $portal_id);

        } else {
            $messageCnt = $this->getMessageCnt($raw_filter_qry);

            Cache::put(env('ENVIRONMENT') . '_admin_dashboard_count_for_messageCnt_' . $org_branch_id . '_' . $portal_id, $messageCnt, 30);
        }

        if (Cache::has(env('ENVIRONMENT') . '_admin_dashboard_count_for_messageCntTotal_' . $org_branch_id . '_' . $portal_id)) {
            $messageCntTotal = Cache::get(env('ENVIRONMENT') . '_admin_dashboard_count_for_messageCntTotal_' . $org_branch_id . '_' . $portal_id);

        } else {
            $messageCntTotal = $this->getMessageCntTotal($raw_filter_qry);
            Cache::put(env('ENVIRONMENT') . '_admin_dashboard_count_for_messageCntTotal_' . $org_branch_id . '_' . $portal_id, $messageCntTotal, 30);
        }

		$result['messageCnt'] 	 = $messageCnt;
		$result['messageCntTotal']   = $messageCntTotal;

		return $result;
	}

	private function getTextData(){
		$result = array();

		$result['textCnt']  	     = $this->getTxtCnt();
		$result['textCntTotal']  	 = $result['textCnt'];

		return $result;
	}
	
	private function getCampaignData(){
		$result = array();

		$result['campaignCntTotal']	 = $this->getCampaignTotal();

		return $result;
	}

	private function getChatData(){
		$result = array();

		$result['chatCnt'] 	     	 = $this->getChatCnt();
		$result['chatCntTotal']  	 = $this->getChatCntTotal();

		return $result;
	}

	private function getPendingHandshakeStatData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$result['approvedCntTotal']  = $this->runDashboardQry('approved', $data);
		$result['pendingCntTotal']   = $this->runDashboardQry('pending', $data);

		$result['pending_hs_ratio'] = '0';
		if ($result['pendingCntTotal'] > 0) {
			$result['pending_hs_ratio'] = ceil(($result['approvedCntTotal'] / $result['pendingCntTotal']) * 100 );
		}

		return $result;
	}

	private function getAvgAgeStatData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		// get handshake stats info
		$rec = new Recruitment;

		// If user has a depemailReartment set, show results based on the filters they have set
		$raw_filter_qry = NULL;
		$org_qry        = NULL;

		if (isset($data['default_organization_portal'])) {
			$crf = new CollegeRecommendationFilters;
			$filter_qry = $crf->generateFilterQry($data);
			if (isset($filter_qry)) {
				$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
				$filter_qry = $filter_qry->join('recruitment as filterRec', 'filterRec.user_id', '=', 'userFilter.id');
				$filter_qry = $filter_qry->where('filterRec.college_id', $data['org_school_id']);

				$org_qry = $this->getRawSqlWithBindings($filter_qry);
				$raw_filter_qry = $filter_qry;
			}
		}

		$result['avg_age_of_students'] = $rec->getAvgAgeOfStudents($data['org_school_id'], $data, $org_qry);

		return $result;
	}

	private function getMaleStatData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();
		// get handshake stats info
		$rec = new Recruitment;

		// If user has a department set, show results based on the filters they have set
		$raw_filter_qry = NULL;
		$org_qry        = NULL;

		if (isset($data['default_organization_portal'])) {
			$crf = new CollegeRecommendationFilters;
			$filter_qry = $crf->generateFilterQry($data);
			if (isset($filter_qry)) {
				$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
				$filter_qry = $filter_qry->join('recruitment as filterRec', 'filterRec.user_id', '=', 'userFilter.id');
				$filter_qry = $filter_qry->where('filterRec.college_id', $data['org_school_id']);

				$org_qry = $this->getRawSqlWithBindings($filter_qry);
				$raw_filter_qry = $filter_qry;
			}
		}

		$gender = $rec->getPercentageOfGender($data['org_school_id'], $data, $org_qry);
		$result['avg_num_of_male']   = isset($gender->males) ? ceil($gender->males) : 0;

		return $result;
	}

	private function getFemaleStatData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();
		// get handshake stats info
		$rec = new Recruitment;

		// If user has a department set, show results based on the filters they have set
		$raw_filter_qry = NULL;
		$org_qry        = NULL;

		if (isset($data['default_organization_portal'])) {
			$crf = new CollegeRecommendationFilters;
			$filter_qry = $crf->generateFilterQry($data);
			if (isset($filter_qry)) {
				$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
				$filter_qry = $filter_qry->join('recruitment as filterRec', 'filterRec.user_id', '=', 'userFilter.id');
				$filter_qry = $filter_qry->where('filterRec.college_id', $data['org_school_id']);

				$org_qry = $this->getRawSqlWithBindings($filter_qry);
				$raw_filter_qry = $filter_qry;
			}
		}

		$gender = $rec->getPercentageOfGender($data['org_school_id'], $data, $org_qry);
		$result['avg_num_of_female'] = isset($gender->females) ? ceil($gender->females) : 0;

		return $result;
	}

	private function getPageViewsStatData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$tp = new TrackingPage;
		$result['college_page_views'] = $this->custom_number_format($tp->getCollegePageViews($data['school_slug']));

		return $result;
	}

	private function getAdminDataData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$ob = new OrganizationBranch;

		$ob = $ob->getCollegeMemberSince($data['org_school_id']);
		$result['member_since'] = $data['created_at'];
		$result['fname'] = $data['fname'];
		$result['requested_upgrade'] = $data['requested_upgrade'];

		$result['current_status'] = 'Free';

		$pri = new Priority;

		$pri = $pri->getCollegeClientType($data['org_school_id'], $data);

		if(isset($pri) && !empty($pri)){
			$result['current_status'] = $pri->contractName;
		}

		return $result;
	}

	private function getAnnouncementData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$result = array();

		$pa = new PlexussAnnouncement;
		$result['announcements'] = $pa->getPlexussAnnouncement($data['user_id']);

		return $result;
	}

	public function getReport($params = NULL){
	    if (isset($params)) {
	        $data = ['org_branch_id' => $params['org_branch_id']];

	        $input = [
	            'start_date' => $params['start_date'],
	            'end_date' => $params['end_date'],
	            'is_super_admin' => $params['is_super_admin'],
	            'requester_user_id' => $params['requester_user_id'],
	        ];
	    } else {
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);
			$input = Request::all();
	        $input['is_super_admin'] = isset($data['super_admin']) ? $data['super_admin'] : 0;
	        $input['requester_user_id'] = $data['user_id'];
	    }

	    $start_date = date("Y-m-d", strtotime($input['start_date'])) . ' 00:00:00';

	    $end_date = date("Y-m-d", strtotime($input['end_date'])) . ' 23:59:59';

			$users = OrganizationBranchPermission::on('rds1')
	                 ->select('user_id', 'u.fname', 'u.lname', 'organization_branch_permissions.created_at')
	                 ->join('users as u', 'u.id', '=', 'organization_branch_permissions.user_id')
	                 ->where('organization_branch_id', $data['org_branch_id']);

	    if ($input['is_super_admin'] !== 1) {
			$users = $users->where('organization_branch_permissions.user_id', '=', $input['requester_user_id']);
	    }

	    $users = $users->get();

	    $ret = array();
		foreach ($users as $key) {
			$tmp = array();

			$tmp['fname'] = ucwords(strtolower($key->fname));
			$tmp['lname'] = ucwords(strtolower($key->lname));

			$tmp['name'] = $tmp['fname'] . ' ' . $tmp['lname'];

			$tmp['date'] = date_format($key->created_at, 'Y/m/d');

			$phone_logs = PhoneLog::select(DB::raw("count(*) as total, sum(status = 'completed') as total_completed, status, sum(recording_duration) as total_duration"))
			                    ->where('sender_user_id', $key->user_id)
			                    ->whereBetween('created_at', [$start_date, $end_date])
			                    ->first();

			$sms_logs = SmsLog::select(DB::raw("count(*) as total_texts, sum(status = 'delivered') as sent_texts, sum(status = 'received') as received_texts"))
			              ->where('sender_user_id', $key->user_id)
			              ->whereBetween('created_at', [$start_date, $end_date])
			              ->first();

			$tmp['total_calls'] = $phone_logs->total;
			$tmp['completed_calls'] = isset($phone_logs->total_completed) ? $phone_logs->total_completed : 0;

			try {
			  $tmp['avg_duration'] = gmdate("H:i:s", $phone_logs->total_duration / $phone_logs->total_completed);
			} catch (\Exception $e) { // Probably divide by zero error
			  $tmp['avg_duration'] = gmdate("H:i:s", 0);                
			}

			$tmp['total_duration'] = gmdate("H:i:s", $phone_logs->total_duration);

			$tmp['total_texts'] = $sms_logs->total_texts;
			$tmp['sent_texts'] = isset($sms_logs->sent_texts) ? $sms_logs->sent_texts : 0;
			$tmp['received_texts'] = isset($sms_logs->received_texts) ? $sms_logs->received_texts : 0;

			$ret[] = $tmp;
		}

		return $ret;
	}

  public function getCallLogsWithTimeZone() {
      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData(true);
      $input = Request::all();

      if (!isset($input['phoneNumber'])) {
          return [];
      }
      
      $response = [];

      $phoneLogs = PhoneLog::select('created_at')
                           ->where('from', '=', $input['phoneNumber'])
                           ->orWhere('to', '=', $input['phoneNumber'])
                           ->get();

      foreach ($phoneLogs as $log) {
          $temp = [];

          $time_zone = 'America/Los_Angeles';

          $ip = $this->iplookup();

          $date = new DateTime($log->created_at, new DateTimeZone($time_zone));

          if (isset($ip['time_zone'])) {
              $time_zone = $ip['time_zone'];

              $date->setTimezone(new DateTimeZone($time_zone));
          }

          $temp['date'] = $date->format('M. d, Y h:ia');

          $temp['timeZone'] = str_replace('_', ' ', $time_zone);

          $response[] = $temp;
      }

      return $response;
  }

	public function saveCRMAutoReporting() {
	      $viewDataController = new ViewDataController();
	      $data = $viewDataController->buildData(true);

	      $input = Request::all();

	      return CrmAutoReporting::insertOrUpdate($input);
	}

	private function getGroupingTemplateId($provider_template_id){

		$ret = array();
		$qry = DB::connection('rds1')->table("email_template_grouping as etg")
									 ->join('email_template_sender_providers as etsp', 'etsp.id', '=', 'etg.etsp_id')
									 
									 ->select('etg.template_id')
									 ->where('etsp.sparkpost_key', $provider_template_id)
									 ->groupBy('etg.template_id')
									 ->get();

		if (isset($qry)) {
			foreach ($qry as $key) {
				$ret[] = $key->template_id;
			}
		}

		return $ret;
	}

  	public function emailReporting(){
	  	$viewDataController = new ViewDataController();
	    $data 	= 	$viewDataController->buildData();
	    $betaUser = new BetaUserController();
	    $type 	=		'sales';
	    $data1	=		[];
	    $data2	=		[];
	    $data['title']                 = 'Email Reporting';
	    $data['currentPage']           = $type . '-email-reporting';
	    $data['adminType']             = $type;
    
    	if( isset($data['profile_img_loc']) ){
      		$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];   
		}

		$sparkPost_Key = env('SPARKPOST_KEY');
	    $sparkPost_Direct_Key = env('SPARKPOST_DIRECT_KEY');
		$sendgrid_Key = env('SENDGRID_KEY');

	  	$data1['email']=EmailReporting::on('rds1')->where('provider','sparkpost')->get();
		$data2['email']=EmailReporting::on('rds1')->where('provider','sparkpost_direct')->get();

		// $from = new Carbon('first day of this month');
		$from = Carbon::today();
		$from = $from->toDateString();
		$from = $from."T00:00";

		// $to = new Carbon('last day of this month');
		$to = Carbon::tomorrow();
		$to = $to->toDateString();
		$to = $to."T23:59";

		$url = 'https://api.sparkpost.com/api/v1/metrics/deliverability/template?from='.$from.'&to='.$to.'&metrics=count_targeted,count_unique_confirmed_opened,count_unique_clicked';
		
	  	$data1['response']=$this->sparkApiResponse($url,$sparkPost_Key);
		$data2['response']=$this->sparkApiResponse($url,$sparkPost_Direct_Key);
		
		$countReturn=[];
		$i=0;
		
		//Parameters for GetRevenuePerClient along with Client name
		$start = date('Y-m-').'01';		
		$end = date('Y-m-d');		

		//Required Data from Data1 Array and it's Corresponding API response
    	foreach($data1['email'] as $emails){
			$template =''; $client_name = '';
			$template = $emails->sparkpost_key;
			$client_name = $emails->client_name;
			$category = DB::connection('rds1')->table('email_templates_category')->where('id','=',$emails->category_id)->first();

			$total=0;$open=0;$click=0;$open_rate=0;$click_rate=0;

			$grouping_template_ids = $this->getGroupingTemplateId($template);
			foreach($data1['response'] as $resp){
				if (empty($template) || empty($resp['template_id'])) {
					continue;
				}

				if (in_array($resp['template_id'], $grouping_template_ids)) {
					$total += $resp['count_targeted'];
					$open  += $resp['count_unique_confirmed_opened'];
					$click += $resp['count_unique_clicked'];
					if($total>0){
						$open_rate = ($open/$total)*100;
						if($open>0){
							$click_rate = ($click/$open)*100;
						}
					}				
				}
			}
			$countReturn[$i]['template']=$template;
			$countReturn[$i]['provider']=$emails->provider;
			$countReturn[$i]['total']=$total;
			$countReturn[$i]['open']=$open;
			$countReturn[$i]['click']=$click;
			$countReturn[$i]['open_rate']=$open_rate;
			$countReturn[$i]['click_rate']=$click_rate;
			$countReturn[$i]['category']=$category->category;

			if($click > 0){
				$countReturn[$i]['conversion'] = $this->getEmailReportingAddtlFields('revenue', $template, $start, $end);

				$countReturn[$i]['complete'] = $this->getEmailReportingAddtlFields('complete', $template, $start, $end);
				$countReturn[$i]['selected_1_4'] = $this->getEmailReportingAddtlFields('selected_1_4', $template, $start, $end);
				$countReturn[$i]['selected_5_more'] = $this->getEmailReportingAddtlFields('selected_5_more', $template, $start, $end);
				$countReturn[$i]['premium'] = $this->getEmailReportingAddtlFields('premium', $template, $start, $end);
			}else{
				$countReturn[$i]['complete'] = 0;
				$countReturn[$i]['selected_1_4'] = 0;
				$countReturn[$i]['selected_5_more'] = 0;
				$countReturn[$i]['premium'] = 0;
				$countReturn[$i]['conversion'] = number_format(0,2);
			}

			// if(isset($client_name)){
			// 	$countReturn[$i]['conversion'] = number_format($betaUser->getRevenuePerClient($client_name, $start, $end),2);
			// }
			// else{
			// 	$countReturn[$i]['conversion'] = number_format(0,2);
			// }

			$i++;
		}
		
		//Required Data from Data2 Array and it's Corresponding API response
		foreach($data2['email'] as $emails){
			$template=''; $client_name = '';
			$template = $emails->sparkpost_key;
			$client_name = $emails->client_name;
			$category = DB::connection('rds1')->table('email_templates_category')->where('id','=',$emails->category_id)->first();

			$total=0;$open=0;$click=0;$open_rate=0;$click_rate=0;
			$grouping_template_ids = $this->getGroupingTemplateId($template);
			foreach($data2['response'] as $resp){
				if (empty($template) || empty($resp['template_id'])) {
					continue;
				}
				if (in_array($resp['template_id'], $grouping_template_ids)) {
					$total += $resp['count_targeted'];
					$open  += $resp['count_unique_confirmed_opened'];
					$click += $resp['count_unique_clicked'];
					if($total>0){
						$open_rate = ($open/$total)*100;
						if($open>0){
							$click_rate = ($click/$open)*100;
						}
					}		
				}
			}
			$countReturn[$i]['template']=$template;
			$countReturn[$i]['provider']=$emails->provider;
			$countReturn[$i]['total']=$total;
			$countReturn[$i]['open']=$open;
			$countReturn[$i]['click']=$click;
			$countReturn[$i]['open_rate']=$open_rate;
			$countReturn[$i]['click_rate']=$click_rate;
			$countReturn[$i]['category']=$category->category;

			if($click > 0){
				$countReturn[$i]['conversion'] = $this->getEmailReportingAddtlFields('revenue', $template, $start, $end);
				$countReturn[$i]['complete'] = $this->getEmailReportingAddtlFields('complete', $template, $start, $end);
				$countReturn[$i]['selected_1_4'] = $this->getEmailReportingAddtlFields('selected_1_4', $template, $start, $end);
				$countReturn[$i]['selected_5_more'] = $this->getEmailReportingAddtlFields('selected_5_more', $template, $start, $end);
				$countReturn[$i]['premium'] = $this->getEmailReportingAddtlFields('premium', $template, $start, $end);
			}else{
				$countReturn[$i]['complete'] = 0;
				$countReturn[$i]['selected_1_4'] = 0;
				$countReturn[$i]['selected_5_more'] = 0;
				$countReturn[$i]['premium'] = 0;
				$countReturn[$i]['conversion'] = number_format(0,2);
			}

			// if(isset($client_name)){
			// 	$countReturn[$i]['conversion'] = number_format($betaUser->getRevenuePerClient($client_name, $start, $end),2);
			// }
			// else{
			// 	$countReturn[$i]['conversion'] = number_format(0,2);
			// }

			$i++;
		}

		//Loop for sorting - order by Total - Desc
		$temp = [];
		for($k=0;$k<$i-1;$k++){
			for($l=$k+1;$l<$i;$l++){
				if($countReturn[$k]['total'] < $countReturn[$l]['total']){
					$temp = $countReturn[$l];
					$countReturn[$l] = $countReturn[$k];
					$countReturn[$k] = $temp;
				}
			}	
		}
		
		$data['countReturn']=$countReturn;
		$emailTemplateCategory = 	DB::connection('rds1')
									->table('email_templates_category')
									->get();
		$email_cat = array();	
		foreach($emailTemplateCategory as $category){
			array_push($email_cat, (array)$category);
		}															

		$data['emailTemplateCategory'] = $email_cat;
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit;
		return View('admin.emailReporting',$data);
  	}


  	public function getEmailReportingAddtlFields($type, $template_id, $start_date, $end_date){

  		$grouping_template_ids = $this->getGroupingTemplateId($template_id);

		$str = '';
		if (count($grouping_template_ids) == 1) {
			$str = "AND ecl.template_id = '".$template_id."'";
		}else{
			$cnt =  0;
			foreach ($grouping_template_ids as $key => $value) {
				if ($cnt ==  0) {
					$str .= 'AND (';
				}
				$str .= "ecl.template_id = '".$value."' OR ";

				$cnt++;
			}
			$str = substr($str, 0, -3);
			$str .= ')';
		}

		$num = 0;
		switch ($type) {

			case 'complete':
				
				$str = str_replace("AND (", "(", $str);
				$str = str_replace("AND ", "", $str);
				$start_date = str_replace("T00:00", "", $start_date);
				$end_date   = str_replace("T23:59", "", $end_date);

				$num = DB::connection('rds1')->table('users as u')
				 		->join('scores as s', 'u.id', '=', 's.user_id')
				 		->join('email_click_logs as ecl', 'u.id', '=', 'ecl.user_id')
				 		->join('users_completion_timestamps as uct', 'u.id', '=', 'uct.user_id')
				 		// ->where('ecl.template_id', $template_id)
				 		->whereRaw($str)
				 		->where('ecl.valid', 1)
				 		->whereBetween(DB::raw('DATE(ecl.click_date)'), [$start_date, $end_date])
				 		->whereBetween('uct.profile_completion_timestamp', 
				 					   array($start_date." 00:00:00", $end_date." 23:59:59"))
						->where('is_ldy', 0)
						->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
						->where('u.email', 'NOT LIKE', '%test%')
						->where('u.fname', 'NOT LIKE', '%test%')
						->where('u.email', 'NOT LIKE', '%nrccua%')
						
						->where('is_plexuss', 0)

						->whereNotNull('u.address')
						->where(DB::raw('length(u.address)'), '>=', 3)
						->whereNotNull('u.zip')
						->whereIn('u.gender', array('m', 'f'))
						->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
					 	->select(DB::raw("count(DISTINCT u.email) as cnt"))->first()->cnt;

				break;
			case 'selected_1_4':
				
				$num = DB::connection('rds1')
						->select("select count(distinct email) as cnt
							from (
							        Select user_id, u.email, count(*)
							        from 
							                (select r.user_id, ecl.college_id
							                from users u
							                join recruitment r on u.id = r.user_id 
							                        and r.user_recruit = 1
							                join email_click_logs as ecl on (u.id = ecl.user_id)

							                where DATE(ecl.click_date) between '".$start_date."' and '".$end_date."'
							                ".$str."
							                AND ecl.valid =1

							                UNION

							                select u.id as user_id, ecl.college_id
							                from users u
							                join pick_a_college_views pacw on u.id = pacw.user_id
							                join email_click_logs as ecl on (u.id = ecl.user_id)

							                where DATE(ecl.click_date) between '".$start_date."' and '".$end_date."'
							                ".$str."
							                AND ecl.valid =1

							                UNION

							                select u.id as user_id, ecl.college_id
							                from users u
							                join email_click_logs as ecl on (u.id = ecl.user_id)
							                join ad_clicks ac on ac.user_id = u.id
							                        and `ac`.`utm_source` NOT LIKE '%test%'
							                        and `ac`.`utm_source` NOT LIKE 'email%'
							                        and (pixel_tracked = 1 or paid_client = 1)

							                where DATE(ecl.click_date) between '".$start_date."' and '".$end_date."'

							                ".$str."
							                AND ecl.valid =1

							                union 

							                select u.id as user_id, ecl.college_id
							                from users u
							                join users_applied_colleges uac on u.id = uac.user_id
							                join email_click_logs as ecl on (u.id = ecl.user_id)

							                where DATE(ecl.click_date) between '".$start_date."' and '".$end_date."'
							                ".$str."
							                AND ecl.valid =1
							        ) tbl1
							        join users u on tbl1.user_id = u.id
							        where not exists (select user_id from country_conflicts where user_id = u.id)
							                and is_ldy = 0
							                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
							                and u.email not like '%test%'
							                and u.fname not like '%test'
							                and u.email not like '%nrccua%'
							        group by u.id
							        having count(*) between 1 and 4
							) tbl2
							");

					$num = $num[0]->cnt;	
				break;
			case 'selected_5_more':

				$num = DB::connection('rds1')
					   ->select("select count(distinct email) as cnt
						from (
						        Select user_id, u.email, count(*)
						        from 
						                (select u.id as user_id, ecl.college_id
						                from users u
						                join recruitment r on u.id = r.user_id 
						                        and r.user_recruit = 1
						                join email_click_logs as ecl on (u.id = ecl.user_id)

							            where DATE(ecl.click_date) between '".$start_date."' and '".$end_date."'
							            ".$str."
							            AND ecl.valid =1
						                

						                UNION

						                select u.id as user_id, ecl.college_id
						                from users u
						                join pick_a_college_views pacw on u.id = pacw.user_id
						                join email_click_logs as ecl on (u.id = ecl.user_id)

							            where DATE(ecl.click_date) between '".$start_date."' and '".$end_date."'
							            ".$str."
							            AND ecl.valid =1

						                UNION

						                select u.id as user_id, ecl.college_id
						                from users u
						                join ad_clicks ac on ac.user_id = u.id
						                        and `ac`.`utm_source` NOT LIKE '%test%'
						                        and `ac`.`utm_source` NOT LIKE 'email%'
						                        and (pixel_tracked = 1 or paid_client = 1)

						                join email_click_logs as ecl on (u.id = ecl.user_id)
							            
							            where DATE(ecl.click_date) between '".$start_date."' and '".$end_date."'
							            ".$str."
							            AND ecl.valid =1
						                

						                union 

						                select u.id as user_id, ecl.college_id
						                from users u
						                join users_applied_colleges uac on u.id = uac.user_id
						                join email_click_logs as ecl on (u.id = ecl.user_id)
							            
							            where DATE(ecl.click_date) between '".$start_date."' and '".$end_date."'
							            ".$str."
							            AND ecl.valid =1
						                
						        ) tbl1
						        join users u on tbl1.user_id = u.id
						        where not exists (select user_id from country_conflicts where user_id = u.id)
						                and is_ldy = 0
						                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
						                and u.email not like '%test%'
						                and u.fname not like '%test'
						                and u.email not like '%nrccua%'
						        group by u.id
						        having count(*) >= 5
						) tbl2
						");
				$num = $num[0]->cnt;	

				break;
			case 'premium':
				
				$num = User::on('rds1')
							 ->join('premium_users as pu', 'pu.user_id', '=', 'users.id')
							 ->join('email_click_logs as ecl', 'users.id', '=', 'ecl.user_id')
				 			 ->where('ecl.template_id', $template_id)
				 			 ->where('ecl.valid', 1)
				 			 ->whereBetween(DB::raw('DATE(ecl.click_date)'), [$start_date, $end_date])
							 ->whereRaw("not exists (select user_id from country_conflicts where user_id = users.id)")
							 ->where('is_ldy', 0)
							 ->where('users.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
							 ->where('users.email', 'NOT LIKE', '%test%')
							 ->where('users.fname', 'NOT LIKE', '%test%')
							 ->where('users.email', 'NOT LIKE', '%nrccua%')
							 ->where('country_id', 1)
							 ->where('is_plexuss', 0)
					 		 ->select(DB::raw("count(DISTINCT users.email) as cnt"))->first()->cnt;
				break;
			case 'revenue':
				$qry =  EmailClickLog::on('rds1')
									 ->where('template_id', $template_id)
									 ->whereBetween(DB::raw('DATE(click_date)'), [$start_date, $end_date])
									 ->whereNotNull('company')
									 ->select('user_id', 'company')
									 ->groupBy('company', 'user_id')
									 ->orderBy('company', 'ASC')
									 ->get();

				if (empty($qry[0])) {
					return 0;
				}
				
				$arr = array();

				foreach ($qry as $key) {
					if (!isset($arr[$key->company])) {
						$arr[$key->company] = array();
					}

					$arr[$key->company][] = $key->user_id;
				}

				$num = 0;
				$buc  = new BetaUserController;
				foreach ($arr as $key => $value) {
					$user_arr = implode(",", $value);
					$num += $buc->getIndividualCompanyRev($key, $start_date, $end_date, $user_arr);
				}
				
				break;

			default:
				# code...
				break;
		}


		return $num;
	}


  public function emailTemplateAjax($start,$end){
	  	$betaUser = new BetaUserController();
	  	$url = "";
	  	$data1 = [];
	  	$data2 = [];
	  	$from = $start;
	  	$to = $end;
	  	if($start=='0' && $end != '0'){
	  		$start = date('Y-m').'-01'.date('\T00:00');
	  		$end= $end.date('\T23:59');
	  		$url = "https://api.sparkpost.com/api/v1/metrics/deliverability/template?from=".$start."&to=".$end."&metrics=count_targeted,count_unique_confirmed_opened,count_unique_clicked";
	  	}
	  	else if($start!='0' && $end == '0'){
	  		$start= $start.date('\T00:00');
	  		$end = new Carbon('last day of this month');
			$end = $end->toDateString();
			$end = $end."T23:59";
	  		$url = "https://api.sparkpost.com/api/v1/metrics/deliverability/template?from=".$start."&to=".$end."&metrics=count_targeted,count_unique_confirmed_opened,count_unique_clicked";
	  	}
	  	else if($start=='0' && $end == '0')
	  	{	
	  		$start = date('Y-m').'-01'.date('\T00:00');
	  		$url = "https://api.sparkpost.com/api/v1/metrics/deliverability/template?from=".$start."&metrics=count_targeted,count_unique_confirmed_opened,count_unique_clicked";
	  	}
	  	else if($start!='0' && $end != '0' && $start!=$end){
	  		$start= $start.date('\T00:00');
	  		$end= $end.date('\T23:59');
	  		$url = "https://api.sparkpost.com/api/v1/metrics/deliverability/template?from=".$start."&to=".$end."&metrics=count_targeted,count_unique_confirmed_opened,count_unique_clicked";
	  	}
	  	else if($start!='0' && $end != '0' && $start==$end){
	  		$start= $start.date('\T00:00');
	  		$end= $end.date('\T23:59');
	  		$url = "https://api.sparkpost.com/api/v1/metrics/deliverability/template?from=".$start."&to=".$end."&metrics=count_targeted,count_unique_confirmed_opened,count_unique_clicked";
	  	}
	  	
	  	$sparkPost_Key 		  = env('SPARKPOST_KEY');
	    $sparkPost_Direct_Key = env('SPARKPOST_DIRECT_KEY');
	    $sendgrid_Key 		  = env('SENDGRID_KEY');

		$data1['email']=EmailReporting::on('rds1')->where('provider','sparkpost')->get();
		$data2['email']=EmailReporting::on('rds1')->where('provider','sparkpost_direct')->get();

		$data1['response']=$this->sparkApiResponse($url,$sparkPost_Key);
		$data2['response']=$this->sparkApiResponse($url,$sparkPost_Direct_Key);

		$resp_ajax=[];
		$i=0;
		if($from == '0'){
			$from = date('Y-m').'01';
		}
		if($to == '0'){
			$to = date('Y-m-d');
		}

		$emailTemplateCategory = 	DB::connection('rds1')
															->table('email_templates_category')
															->get();
		$email_cat = array();	
		foreach($emailTemplateCategory as $category){
			array_push($email_cat, (array)$category);
		}
		
    	foreach($data1['email'] as $emails){
			$template=''; $client_name = '';
			$template = $emails->sparkpost_key;
			$client_name = $emails->client_name;
			$category = DB::connection('rds1')->table('email_templates_category')->where('id','=',$emails->category_id)->first();

			$total=0;$open=0;$click=0;$open_rate=0;$click_rate=0;

			$grouping_template_ids = $this->getGroupingTemplateId($template);
			foreach($data1['response'] as $resp){
				if (empty($template) || empty($resp['template_id'])) {
					continue;
				}

				if (in_array($resp['template_id'], $grouping_template_ids)) {
					$total += $resp['count_targeted'];
					$open  += $resp['count_unique_confirmed_opened'];
					$click += $resp['count_unique_clicked'];
					if($total>0){
						$open_rate = ($open/$total)*100;
						if($open>0){
							$click_rate = ($click/$open)*100;
						}
					}		
				}
			}
			$resp_ajax[$i]['template']=$template;
			$resp_ajax[$i]['provider']=$emails->provider;
			$resp_ajax[$i]['total']=$total;
			$resp_ajax[$i]['open']=$open;
			$resp_ajax[$i]['click']=$click;
			$resp_ajax[$i]['open_rate']=$open_rate;
			$resp_ajax[$i]['click_rate']=$click_rate;
			$resp_ajax[$i]['category']=$category->category;

			if($click > 0){
				$resp_ajax[$i]['conversion'] = $this->getEmailReportingAddtlFields('revenue', $template, $start, $end);
				$resp_ajax[$i]['complete'] = $this->getEmailReportingAddtlFields('complete', $template, $start, $end);
				$resp_ajax[$i]['selected_1_4'] = $this->getEmailReportingAddtlFields('selected_1_4', $template, $start, $end);
				$resp_ajax[$i]['selected_5_more'] = $this->getEmailReportingAddtlFields('selected_5_more', $template, $start, $end);
				$resp_ajax[$i]['premium'] = $this->getEmailReportingAddtlFields('premium', $template, $start, $end);
			}else{
				$resp_ajax[$i]['complete'] = 0;
				$resp_ajax[$i]['selected_1_4'] = 0;
				$resp_ajax[$i]['selected_5_more'] = 0;
				$resp_ajax[$i]['premium'] = 0;
				$resp_ajax[$i]['conversion'] = number_format(0,2);
			}

			// if(isset($client_name)){
			// 	$resp_ajax[$i]['conversion'] = number_format($betaUser->getRevenuePerClient($client_name, $start, $end),2);
			// }
			// else{
			// 	$resp_ajax[$i]['conversion'] = number_format(0,2);
			// }
			$resp_ajax[$i]['cat'] = $email_cat;
			$i++;
		}
		
		foreach($data2['email'] as $emails){
			$template=''; $client_name = '';
			$template = $emails->sparkpost_key;
			$client_name = $emails->client_name;
			$category = DB::connection('rds1')->table('email_templates_category')->where('id','=',$emails->category_id)->first();

			$total=0;$open=0;$click=0;$open_rate=0;$click_rate=0;
			
			$grouping_template_ids = $this->getGroupingTemplateId($template);
			foreach($data2['response'] as $resp){
				if (empty($template) || empty($resp['template_id'])) {
					continue;
				}

				if (in_array($resp['template_id'], $grouping_template_ids)) {
					$total += $resp['count_targeted'];
					$open  += $resp['count_unique_confirmed_opened'];
					$click += $resp['count_unique_clicked'];
					if($total>0){
						$open_rate = ($open/$total)*100;
						if($open>0){
							$click_rate = ($click/$open)*100;
						}
					}		
				}
			}
			$resp_ajax[$i]['template']=$template;
			$resp_ajax[$i]['provider']=$emails->provider;
			$resp_ajax[$i]['total']=$total;
			$resp_ajax[$i]['open']=$open;
			$resp_ajax[$i]['click']=$click;
			$resp_ajax[$i]['open_rate']=$open_rate;
			$resp_ajax[$i]['click_rate']=$click_rate;
			$resp_ajax[$i]['category']=$category->category;

			if($click > 0){
				$resp_ajax[$i]['conversion'] = $this->getEmailReportingAddtlFields('revenue', $template, $start, $end);
				$resp_ajax[$i]['complete'] = $this->getEmailReportingAddtlFields('complete', $template, $start, $end);
				$resp_ajax[$i]['selected_1_4'] = $this->getEmailReportingAddtlFields('selected_1_4', $template, $start, $end);
				$resp_ajax[$i]['selected_5_more'] = $this->getEmailReportingAddtlFields('selected_5_more', $template, $start, $end);
				$resp_ajax[$i]['premium'] = $this->getEmailReportingAddtlFields('premium', $template, $start, $end);
			}else{
				$resp_ajax[$i]['complete'] = 0;
				$resp_ajax[$i]['selected_1_4'] = 0;
				$resp_ajax[$i]['selected_5_more'] = 0;
				$resp_ajax[$i]['premium'] = 0;
				$resp_ajax[$i]['conversion'] = number_format(0,2);
			}

			// if(isset($client_name)){
			// 	$resp_ajax[$i]['conversion'] = number_format($betaUser->getRevenuePerClient($client_name, $start, $end),2);
			// }
			// else{
			// 	$resp_ajax[$i]['conversion'] = number_format(0,2);
			// }
			$resp_ajax[$i]['cat'] = $email_cat;
			$i++;
		}
		$temp = [];
		for($k=0;$k<$i-1;$k++){
			for($l=$k+1;$l<$i;$l++){
				if($resp_ajax[$k]['total'] < $resp_ajax[$l]['total']){
					$temp = $resp_ajax[$l];
					$resp_ajax[$l] = $resp_ajax[$k];
					$resp_ajax[$k] = $temp;
				}
			}	
		}
		return json_encode($resp_ajax);
  }	

  //For Fetching Template HTML
  public function templateHtml($templateId){
  	
  	$etsp =  EmailTemplateSenderProvider::on('bk')
  	                                    ->where('sparkpost_key', $templateId)
  										->first();
  	if ($etsp->provider ==  "sparkpost_direct") {
		$api_key = env('SPARKPOST_DIRECT_KEY');
	}else{
		$api_key = env('SPARKPOST_KEY');
	}								
  	$data = array(); 	
    $url="https://api.sparkpost.com/api/v1/templates/".$templateId;
    $data['response']=$this->sparkApiResponse($url,$api_key);
    return ($data['response']['content']);
  }

  // Sparkpost API For Fetching Data Using GuzzleHttp
  public function sparkApiResponse($url, $key){
		$client = new Client(['base_uri' => 'https://api.sparkpost.com']);
		$response1 = $client->request('GET', $url, ['headers' => [
    						'Content-Type'=> 'application/json',
						    'Authorization'     => $key
						    ]]);
    $response = json_decode($response1->getBody(), true);
    return ($response['results']);  
  }
	
  //For sending Email to oneself
  public function sendMail($template,$emailList){
  	$emails = array();
  	$emails = explode(',', $emailList);
  	$emails = array_unique($emails);
  	$template_name = EmailReporting::on('rds1')->where('sparkpost_key',$template)->first();
  	$template_name = $template_name->template_name;
  	
  	foreach($emails as $email){
  			
		    $reply_email = 'support@plexuss.com';
		    $email_arr = [
		        'email' => $email,
		        'name' => '',
		        'type' =>'to',
		    ];

		    $params = [
		        'SUBJECT' => 'Testing Email',
		    ];
		    $mac = new MandrillAutomationController;

				$response = $mac->sendMandrillEmail($template_name, $email_arr, $params, $reply_email);
  	}
  	return $response;
  }

  public function modifyEmailTemplateCategory($category=null,$ajax=null){

  	if($ajax){
  		$template = EmailReporting::on('rds1')
  								->where('category_id',$category)
  								->pluck('sparkpost_key')
  								->toArray();

		return ($template);
  	}

  	$input = Request::all();

    if(isset($input['category_id']) && isset($input['template'])){
	  	if(EmailReporting::where('sparkpost_Key','=',$input['template'])
	  										->update(['category_id'=>$input['category_id']])){
	  		return "success";
	  	}
	  	else{
	  		return "error occured";
	  	}
	  }

  }

  // Get transfer orgs
  public function getTransferOrgs(){
		$qry = DB::connection('rds1')->table('transfer_calls_orgs as tco')
					 ->join('transfer_calls_org_phones as tcop', 'tco.id', '=', 'tcop.transfer_calls_org_id')
					 ->leftjoin('revenue_organizations as ro', 'ro.id', '=', 'tco.ro_id')
					 ->leftjoin('organization_branches as ob', 'tco.org_branch_id', '=', 'ob.id')
					 ->leftjoin('organizations as o', 'o.id', '=', 'ob.organization_id')
					 ->select('o.name as org_name', 'ro.name as ro_name', 'tcop.phone', 'tcop.department')
					 ->get();

		$ret = array();							 

		foreach ($qry as $key) {
			if (isset($key->ro_name)) {
				$name = $key->ro_name;
			}elseif (isset($key->org_name)) {
				$name = $key->org_name;
			}
			if (!isset($name)) {
				continue;
			}

			if (!isset($ret[$name])) {
				$ret[$name]['phones'] = array();
				
				$phone_detail = array();
				$phone_detail['phone'] = $key->phone;
				$phone_detail['department'] = $key->department;

				$ret[$name]['phones'][] = $phone_detail;
			}else{
				
				$phone_detail = array();
				$phone_detail['phone'] = $key->phone;
				$phone_detail['department'] = $key->department;

				$ret[$name]['phones'][] = $phone_detail;
			}
		}
		return $ret;
  }

  public function getManualPostingDistributionData() {
      $response = [];

      $revenue_orgs = DB::connection('rds1')
                        ->table('revenue_organizations as ro')
                        ->select('ro.name', 'ro.id as ro_id')
                        ->where('ro.allow_manual_posting', '=', 1)
                        ->where('ro.type', '=', 'post')
                        ->get();

      foreach ($revenue_orgs as $org) {
          $temp = [];

          $temp['ro_id'] = $org->ro_id; 
          $temp['name'] = $org->name;

          $temp['options'] = DB::connection('rds1')
                               ->table('distribution_clients as dc')
                               ->select('dc.id as dc_id', 'dc.college_id', 'dc.school_name')
                               ->where('dc.ro_id', '=', $temp['ro_id'])
                               ->get();

          if (isset($temp['options'])) {
              $response[] = $temp;
          }
      }
    return $response;
  }

  public function getProgramData($col_id){
  		$response = [];
      $dc_id = DB::connection('rds1')
                        ->table('distribution_clients as dc')
                        ->select('dc.id as dc_id')
                        ->where('dc.college_id', '=', $col_id)
                        ->get();

      foreach($dc_id as $id){
      	$temp=[];
      	$temp['dc_id']= $id->dc_id;

      	$temp['options']=DB::connection('rds1')
      									->table('distribution_client_field_mappings as dcfm')
                        ->join('distribution_client_value_mappings as dcvm', 'dcfm.id','=','dcvm.dcfm_id')
                        ->where('dcfm.dc_id','=', $temp['dc_id'])
                        ->where('dcfm.plexuss_field_id','=','5')
                        ->select('dcvm.client_value', 'dcvm.client_value_name')
                        ->groupBy('dcvm.client_value')
                        ->get();

      }                  

      if(isset($temp['options'])){        	
      $response[] = $temp;
      }

    	return $response;
  }

  public function manuallyPostStudent() {
      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData(true);
      $response = [];
      $addtl_fields = array();

      $dc_controller = new DistributionController;
      $input = Request::all();

      $user_id = $input['user_id'];
      $ro_id = $input['ro_id'];

      // Extra checks for string null cases, due to HTML element formatting
      $college_id = (isset($input['college_id']) && $input['college_id'] != 'null' && $input['college_id'] != 'NULL')
          ? $input['college_id'] 
          : NULL;
      $course_id = (isset($input['course_id']) && $input['course_id'] != 'null' && $input['course_id'] != 'NULL')
          ? $input['course_id'] 
          : NULL;

      $gdpr_phone = (isset($input['gdpr_phone']) && $input['gdpr_phone'] != 'null' && $input['gdpr_phone'] != 'NULL')
          ? $input['gdpr_phone'] 
          : NULL;

      $gdpr_email = (isset($input['gdpr_email']) && $input['gdpr_email'] != 'null' && $input['gdpr_email'] != 'NULL')
          ? $input['gdpr_email'] 
          : NULL;

      isset($gdpr_email) ? $addtl_fields['gdpr_email'] = $gdpr_email : NULL;
      isset($gdpr_phone) ? $addtl_fields['gdpr_phone'] = $gdpr_phone : NULL;
      

      $eligible = $dc_controller->isEligible($user_id, null, $ro_id, $college_id,$course_id);
     
      $eligible = json_decode($eligible, true);

      if ($eligible['status'] == 'failed') {
          return $eligible;
      } else {
          $postResponse = $dc_controller->postInquiriesWithQueue($ro_id, $college_id, $user_id, 1, $course_id, NULL, NULL, $addtl_fields );
          $postResponse = json_decode($postResponse, true);
          
          if (isset($postResponse['dr']) && isset($postResponse['dr']['id'])) {
              $dr = DistributionResponse::find($postResponse['dr']['id']);

              if (isset($dr)) {
                  $dr->submitted_by_user_id = $data['user_id'];
                  $dr->save();
              }
          }
          
          $response['status'] = $postResponse['success'] == 1 ? 'success' : 'failed';
          $response['error_msg'] = $postResponse['error_msg'];
          
          return $response;
      }
  }
	
	
	/****************/
	
	public function scholarshipcms($scholarship_id = 0){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['is_admin_premium'] = $this->validateAdminPremium();
		$data["scholarship_info"] = '';
		
		$data['school_name'] = $this->school_name;
		$data['school_logo'] = $this->school_logo;
		$user_id = $data['user_id'];
		
				
		$schModel = new Scholarshipcms();
		if($scholarship_id!=0){
			$checkcount = $schModel->checkScholarshipcmsExist($user_id,$scholarship_id);
			if($checkcount>0){
				Session::put('temp_scholarshipadmin_id', $scholarship_id);
				$data["scholarship_info"] = $schModel->checkScholarshipAdmin($scholarship_id);
			}
		}
		
		$data["scholarships"] = $scholarships = $schModel->getAllScholarships($user_id);

		$data['currentPage'] = 'admin-cms-filtering';
		$data['adminscholarshipPage'] = 'admincms';
		$data['title'] = 'ADMIN';
		return View('admin.scholarshipcms.index', $data);
		
	}
	
	
	public function addScholarshipcms (){
		$error = 0;
		$opdata["scholarship_name_error"] ='';
		$opdata["scholarshipsub_name_error"] ='';
		$opdata["amount_error"] ='';
		$opdata["deadline_error"] ='';
		$opdata["description_error"] ='';
		$opdata["success"] ='';
		$opdata["scholarship_id"] =0;
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$user_id = $data['user_id'];
		
		$schModel = new Scholarshipcms();
		//$checkcount = $schModel->checkScholarshipAdminExist($user_id);
		
		$input = Request::all();
		$scholarship_name = trim($input['scholarship_title']);
		$scholarshipsub_name = trim($input['scholarship_subtitle']);
		$amount = trim($input['amount']);
		$deadline = trim($input['deadline']);
		$description = $input['description'];
		$scholarship_id = $input['scholarship_id'];
		
		if($scholarship_name==''){
			$opdata["scholarship_name_error"] = "Please enter scholarship title";
			$error = 1;
		}
		if($scholarshipsub_name==''){
			$opdata["scholarshipsub_name_error"] = "Please enter scholarship subtitle";
			$error = 1;
		}
		if($amount==''){
			$opdata["amount_error"] = "Please enter amount";
			$error = 1;
		}
		if($deadline==''){
			$opdata["deadline_error"] = "Please select deadline";
			$error = 1;
		}
		if($description==''){
			$opdata["description_error"] = "Please enter description";
			$error = 1;
		}
		
		if($error==0){
			if($scholarship_id ==0){
				$schModel->scholarship_title = $scholarship_name;
				$schModel->scholarshipsub_title = $scholarshipsub_name;
				$schModel->deadline = $deadline;
				$schModel->max_amount = $amount;
				$schModel->description = $description;
				$schModel->submission_id = null;
				$schModel->provider_id = null;
				$schModel->user_id = $user_id;
				$schModel->college_id = $data["org_school_id"];
				$schModel->save();
				$opdata["success"] ='success';
				$opdata["operation"] ='add';
				$opdata["scholarship_id"] = $schModel->id;
			}else{
				$schObj['scholarship_title'] = $scholarship_name;
				$schObj['scholarshipsub_title'] = $scholarshipsub_name;
				$schObj['deadline'] = $deadline;
				$schObj['max_amount'] = $amount;
				$schObj['description'] = $description;
				$schModel->updateOrCreate(['id'=> $scholarship_id], $schObj);
				
				$opdata["success"] ='success'; 
				$opdata["operation"] ='edit';
				$opdata["scholarship_id"] = $scholarship_id;
			}
		}
		
		return $opdata;
	}
	
	
	public function delScholarshipAdmin (){
		$error = 0;
				
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$input = Request::all();
		$schol_id = $input['id'];
		
		$schModel = new Scholarshipcms();
		$schObj["id"] = $schol_id;
		$schObj["college_id"] = $data['org_school_id'];
		//$schObj["scholarship_org_id"] = $data['scholarship_org_id'];
		
		if($schol_id=='' || $schol_id==0){
			$error = 1;
		}
		
		if($error==0){
			$delrst = $schModel->deleteRec($schObj);
			if($delrst==1){
				return "success";
			}
		}
	}

}