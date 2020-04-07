<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Request, DB, Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

use App\Country, App\CollegeRecommendationFilters, App\Department, App\Ethnicity, App\Religion, App\AdvancedSearchFilterName, App\AdvancedSearchNote, App\TrackingPage, App\Transcript;
use App\MilitaryAffiliation, App\User, App\College, App\OrganizationPortal, App\Recruitment, App\RecruitmentTag, App\PrescreenedUser, App\OrganizationBranch, App\UsersCustomQuestion, App\RecruitmentVerifiedApp, App\CovetedUser, App\Priority;

use App\UsersAppliedColleges, App\CollegesApplicationStatus;

class AdvancedStudentSearchController extends Controller
{
	/**
	 * This method displays default Advanced Student Search page
	 *
	 * @return view
	 */
	public function index(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		//We need to make sure the skip is ZERO on load
		if (Cache::has(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id'])) {
			$tmp = Cache::get(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id']);
			$tmp['arrObj'] = null;
			$tmp['skip'] = 0;
			Cache::put(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id'], $tmp, 60);
		}

		$total_results_count = 0;

		$data['title'] = 'Student Search';

		if( $data['is_agency'] == 0 ){
			$data['topnav'] = 'topnav';
			$data['currentPage'] = 'admin-student-search';
			$data['adminType'] = 'admin';

            $is_admin_premium = $this->validateAdminPremium();

            if (!$is_admin_premium) {
                return redirect( '/admin/premium-plan-request' );
            }

		}else{
			$data['topnav'] = 'agencyTopNav';
			$data['currentPage'] = 'agency-student-search';
			$data['adminType'] = 'agency';
		}
		
		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		//get start dates
		if( Cache::has(env('ENVIRONMENT') .'_start_dates') ){
			$dates = Cache::get(env('ENVIRONMENT') .'_start_dates');
		}else{
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
			Cache::put(env('ENVIRONMENT') .'_start_dates', $dates, 120);
		}
		$data['dates'] = $dates;

		//get financial options
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

		//get countries
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
		$data['cities'] = array('' => 'Select state first' );

		//get departments
		if (Cache::has(env('ENVIRONMENT') .'_all_dep_name')) {
			$dep_name = Cache::get(env('ENVIRONMENT') .'_all_dep_name');
		}else{
			$dep = new Department;
			$dep_name = $dep->getAllDepartments();
			Cache::put(env('ENVIRONMENT') .'_all_dep_name', $dep_name, 120);
		}
		$data['departments'] = $dep_name;
		$data['majors'] = array('' => 'Select department first' );

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

		// - Military Affiliations
		$military_affiliation_raw = MilitaryAffiliation::all()->toArray();
		$military_affiliation_arr = array();
		foreach( $military_affiliation_raw as $mar ){
			$military_affiliation_arr[ $mar[ 'id' ] ] = $mar[ 'name' ];
		}
		$military_affiliation_arr = array( '' => 'Select...' ) + $military_affiliation_arr;

		$data['military_affiliation_arr'] = $military_affiliation_arr;

		// Saved advanced search filters in data
		$asfn = new AdvancedSearchFilterName;
		$data['savedFilters'] = $asfn->getAllAdvancedSearchFilter();

		$arrObj = null;
		$crf = new CollegeRecommendationFilters;

		$qry = $crf->globalMethodGenerateFilterQry($arrObj, true); 

		if (isset($data['is_organization']) && $data['is_organization'] == 1) {
			$qry = $qry->leftjoin('recruitment as r',  function($q) use($data){
						$q->where('r.college_id', '=', $data['org_school_id']);
						$q->on('r.user_id', '=', 'userFilter.id');
					});

			if($data['bachelor_plan'] == 1){

				$data['paid_customer'] = true;

			}else{
				$data['paid_customer'] = false;
			}

		}elseif (isset($data['agency_collection'])) {
			$qry = $qry->leftjoin('agency_recruitment as r', function($q) use($data){
						$q->where('r.agency_id', '=', $data['agency_collection']->agency_id);
						$q->on('r.user_id', '=', 'userFilter.id');
					});
			if ($data['agency_collection']->is_trial_period == 1 || $data['agency_collection']->balance > 0) {
				$data['paid_customer'] = true;
			}else{
				$data['paid_customer'] = false;
			}
		}

		$qry =  $qry->select('userFilter.id as user_id', 'userFilter.fname', 'userFilter.lname', 'userFilter.in_college', 'userFilter.id as userFilterser_id',
							's.overall_gpa', 's.hs_gpa', 's.weighted_gpa', 's.sat_total', 's.act_composite',
							'ct.country_code', 'ct.country_name',
							DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
							'r.id as is_rec')
					// ->where('userFilter.is_alumni', 0)
					// ->where('userFilter.is_parent', 0)
					// ->where('userFilter.is_counselor', 0)
					->where('userFilter.is_organization', 0)
					->where('userFilter.is_agency', 0)
					->where('userFilter.is_plexuss', 0);
											 
		$qry = $qry->groupby('userFilter.id');

		if (Cache::has(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_total_results_count')) {
			$total_results_count = Cache::get(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_total_results_count');
		}else{
			$total_results_count = User::on('rds1')->where('is_alumni', 0)
											 // ->where('is_parent', 0)
											 // ->where('is_counselor', 0)
											 ->where('is_organization', 0)
											 ->where('is_agency',0)
											 ->where('is_university_rep', 0)
											 ->count();

			Cache::put(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_total_results_count', $total_results_count, 60);
		}

		$data['total_results_count'] = $total_results_count;

		$queryTake = 0;
		$querySkip = 0;
		// get env obj to set take and skip
		$obj = Cache::get(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id']);

		// if can not find take value
		if(!isset($obj)) {
			$obj = array();
			$obj['arrObj'] = $arrObj;
			$obj['take'] = 15;
			$obj['skip'] = 0;
			$obj['from_date'] = null;
			$obj['to_date'] = null;
			$queryTake = intval($obj['take']);
			$querySkip = intval($obj['skip']);
		}else {
			// already set take, then reset skip
			$queryTake = intval($obj['take']);
			$obj['skip'] = $querySkip + $queryTake;
			$obj['from_date'] = null;
			$obj['to_date'] = null;
		}
		
		// if not set the filter
		if (!isset($arrObj)) {

			$usr  = User::on('rds1')
                                    // ->where('is_alumni', 0)
									// ->where('is_parent', 0)
									// ->where('is_counselor', 0)
									->where('is_organization', 0)
									->where('is_agency', 0)
									->where('is_plexuss', 0)
									->select('id')
									->orderBy('profile_percent', 'DESC')
								    ->orderBy('id', 'DESC')
								    ->take($obj['take']);


			$usr = $usr->get()->toArray();

			$qry = $qry->where(function($q) use($usr){
						foreach ($usr as $k => $v) {
							$q->orWhere('userFilter.id', $v['id']);
						}
					});
		
		}
		$qry = $qry->leftjoin('transcript as t', 't.user_id', '=', 'userFilter.id')
									   ->addSelect(DB::raw("GROUP_CONCAT(
														DISTINCT t.doc_type
														ORDER BY
															t.id ASC SEPARATOR ', '
													) as transcript_arr "));

		$qry = $qry->orderBy('userFilter.profile_percent', 'DESC')
				   ->orderBy('userFilter.id', 'DESC')
				   ->take($queryTake)->get();
		
		
		$data['display_option'] = $queryTake;
		$data['total_viewing_count'] = $querySkip + $queryTake;

		Cache::put(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id'], $obj, 60);

		

		// if number of view is equal or bigger than number of total results then has_results is full
		if ($data['total_viewing_count'] >= $data['total_results_count']) {
			$data['has_searchResults'] = false;
			$data['total_viewing_count'] = $data['total_results_count'];
		}else{
			$data['has_searchResults'] = true;
		}
		
		$data = $this->generatePartialData($data, $qry);

		return View('advancedStudentSearch.index', $data);
	}

	public function loadMore(){

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all(); 

		$obj = Cache::get(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id']);	

		if(!isset($obj)){
			$obj = array();
			$obj['arrObj'] = $arrObj;
			$obj['take'] = 15;
			$obj['skip'] = 0;
			$obj['from_date'] = null;
			$obj['to_date'] = null;	
		}else{			
			$obj['take'] = isset($input['display_option']) ? intval($input['display_option']) : 15;		
		}

		$arrObj = $obj['arrObj'];
		
		$crf = new CollegeRecommendationFilters;

		$qry = $crf->globalMethodGenerateFilterQry($arrObj, true); 

		if (isset($data['is_organization']) && $data['is_organization'] == 1) {
			$qry = $qry->leftjoin('recruitment as r', function($q) use($data){
						$q->where('r.college_id', '=', $data['org_school_id']);
						$q->on('r.user_id', '=', 'userFilter.id');
					});

			if($data['bachelor_plan'] == 1){

				$data['paid_customer'] = true;

			}else{
				$data['paid_customer'] = false;
			}

		}elseif (isset($data['agency_collection'])) {
			$qry = $qry->leftjoin('agency_recruitment as r', function($q) use($data){
						$q->where('r.agency_id', '=', $data['agency_collection']->agency_id);
						$q->on('r.user_id', '=', 'userFilter.id');
					});

			if ($data['agency_collection']->is_trial_period == 1 || $data['agency_collection']->balance > 0) {
				$data['paid_customer'] = true;
			}else{
				$data['paid_customer'] = false;
			}
		}

		$qry =  $qry->select('userFilter.id as user_id', 'userFilter.fname', 'userFilter.lname', 'userFilter.in_college', 'userFilter.id as userFilterser_id',
							's.overall_gpa', 's.hs_gpa', 's.weighted_gpa', 's.sat_total', 's.act_composite',
							'ct.country_code', 'ct.country_name',
							DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
							'r.id as is_rec')
					// ->where('userFilter.is_alumni', 0)
					// ->where('userFilter.is_parent', 0)
					// ->where('userFilter.is_counselor', 0)
					->where('userFilter.is_organization', 0)
					->where('userFilter.is_agency', 0)
					->where('userFilter.is_plexuss', 0);

		if (isset($obj['from_date']) && isset($obj['to_date'])) {
			$qry = $qry->where('userFilter.created_at', '>=', $obj['from_date'])
					   ->where('userFilter.created_at', '<=', $obj['to_date']);
		}


		$qry = $qry->orderBy('userFilter.profile_percent', 'DESC')
				   ->orderBy('userFilter.id', 'DESC');

		$total_results_count = $qry->distinct('userFilter.id')->count('userFilter.id');
		$qry = $qry->groupby('userFilter.id');

		$qry = $qry->leftjoin('transcript as t', 't.user_id', '=', 'userFilter.id')
									   ->addSelect(DB::raw("GROUP_CONCAT(
														DISTINCT t.doc_type
														ORDER BY
															t.id ASC SEPARATOR ', '
													) as transcript_arr "));
		
		$obj['take'] = isset($input['loadmore_option']) ? intval($input['loadmore_option']) : 15;

		$data['display_option'] = $obj['take'];

		if (!isset($arrObj)) {
			$usr  = User::on('rds1')
         //                            ->where('is_alumni', 0)
									// ->where('is_parent', 0)
									// ->where('is_counselor', 0)
									->where('is_organization', 0)
									->where('is_agency', 0)
									->where('is_plexuss', 0)
									->select('id')
									->orderBy('profile_percent', 'DESC')
								    ->orderBy('id', 'DESC')
								    ->take($obj['take'] + $obj['skip']);

			$usr = $usr->get()->toArray();

			$qry = $qry->where(function($q) use($usr){
						foreach ($usr as $k => $v) {
							$q->orWhere('userFilter.id', $v['id']);
						}
					});
			$qry = $qry->skip($obj['skip'])->take($obj['take'])->get();
		}else{
			$qry = $qry->skip($obj['skip'])->take($obj['take'])->get();
		}

		$data = $this->generatePartialData($data, $qry);

		$data['total_results_count'] = $total_results_count;
		$data['total_viewing_count'] = $obj['take'] + $obj['skip'];

		// if number of view is equal or bigger than number of total results then has_results is full
		if ($data['total_viewing_count'] >= $data['total_results_count']) {
			$data['has_searchResults'] = false;
			$data['total_viewing_count'] = $data['total_results_count'];
		}else{
			$data['has_searchResults'] = true;
		}

		// Saved advanced search filters in data
		$asfn = new AdvancedSearchFilterName;
		$data['savedFilters'] = $asfn->getAllAdvancedSearchFilter();

		//after finish calculation, update skip, put into Cache
		$obj['skip'] += $obj['take'];

		Cache::put(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id'], $obj, 60);

		return View('advancedStudentSearch.ajax.searchResults', $data);
	}

	/**
	 * This method updates the student search results 
	 *
	 * @return view
	 */
	public function update($savedFilterInput = null){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (isset($savedFilterInput)){
			$input = $savedFilterInput;
		}else{
			$input = Request::all();
		}

		$data['search_input'] = json_encode($input);
		$data['search_input'] = str_replace("'", '&#39', $data['search_input']);
		
		$newResults = null;

		//We need to make sure the skip is ZERO on load
		if (Cache::has(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id'])) {
			$tmp = Cache::get(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id']);
			$tmp['skip'] = 0;
			Cache::put(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id'], $tmp, 60);
		}

		// Date format
		if (isset($input['date']) && !empty($input['date'])) {
			$date_arr = explode(" to ", $input['date']);
			$from_date = str_replace('/', '-', $date_arr[0]);
			$temp_date = explode("-", $from_date);
			$from_date = $temp_date[2]. '-'. $temp_date[0]. '-'. $temp_date[1].' 00:00:00';
			

			$to_date = str_replace('/', '-', $date_arr[1]);
			$temp_date = explode("-", $to_date);
			$to_date = $temp_date[2]. '-'. $temp_date[0]. '-'. $temp_date[1].' 23:59:59';
		}else{
			$from_date = null;
			$to_date = null;
		}
		//end date format

		$arrObj = $this->convertInputsForFilters($input);

		$crf = new CollegeRecommendationFilters;

		$qry = $crf->globalMethodGenerateFilterQry($arrObj, true); 

		if (isset($data['is_organization']) && $data['is_organization'] == 1) {
			$qry = $qry->leftjoin('recruitment as r', function($q) use($data){
						$q->where('r.college_id', '=', $data['org_school_id']);
						$q->on('r.user_id', '=', 'userFilter.id');
					});

			if($data['bachelor_plan'] == 1){

				$data['paid_customer'] = true;

			}else{
				$data['paid_customer'] = false;
			}
		}elseif (isset($data['agency_collection'])) {
			$qry = $qry->leftjoin('agency_recruitment as r', function($q) use($data){
						$q->where('r.agency_id', '=', $data['agency_collection']->agency_id);
						$q->on('r.user_id', '=', 'userFilter.id');
					});

			if ($data['agency_collection']->is_trial_period == 1 || $data['agency_collection']->balance > 0) {
				$data['paid_customer'] = true;
			}else{
				$data['paid_customer'] = false;
			}
		}	

		$qry =  $qry->select('userFilter.id as user_id', 'userFilter.fname', 'userFilter.lname', 'userFilter.in_college', 'userFilter.id as userFilterser_id', 
							'userFilter.hs_grad_year', 'userFilter.college_grad_year', 'userFilter.profile_img_loc', 'userFilter.financial_firstyr_affordibility', 'userFilter.profile_percent', 
							's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.toefl_total', 's.ielts_total', 's.weighted_gpa',
							'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
							'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
							'ct.country_code', 'ct.country_name',
							'dt.display_name as degree_name',
							DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
							'p.profession_name',
							'r.id as is_rec')
					// ->where('userFilter.is_alumni', 0)
					// ->where('userFilter.is_parent', 0)
					// ->whereis_counselor('userFilter.', 0)
					->where('userFilter.is_organization', 0)
					->where('userFilter.is_agency', 0)
					->where('userFilter.is_plexuss', 0);

		if (isset($from_date) && isset($to_date)) {
			$qry = $qry->where('userFilter.created_at', '>=', $from_date)
					   ->where('userFilter.created_at', '<=', $to_date);
		}

		$obj = Cache::get(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id']);
		// if can not find take value
		if(!isset($obj)) {
			$obj = array();
			$obj['arrObj'] = $arrObj;
			$obj['take'] = 15;
			$obj['skip'] = 0;
			$obj['from_date'] = null;
			$obj['to_date'] = null;
		}else {
			$obj['arrObj'] = $arrObj;
			$obj['from_date'] = $from_date;
			$obj['to_date'] = $to_date;
		}

		//after finish calculation, update skip
		$obj['skip'] += $obj['take'];

		Cache::put(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id'], $obj, 60);

		$qry = $qry->orderBy('userFilter.profile_percent', 'DESC')
				   ->orderBy('userFilter.id', 'DESC');

		$total_results_count = $qry->distinct('userFilter.id')->count('userFilter.id');

		$qry = $qry->leftjoin('transcript as t', 't.user_id', '=', 'userFilter.id')
									   ->addSelect(DB::raw("GROUP_CONCAT(
														DISTINCT t.doc_type
														ORDER BY
															t.id ASC SEPARATOR ', '
													) as transcript_arr "));

		$qry = $qry->groupby('userFilter.id');

		$qry = $qry->take($obj['take'])->get();
		
		$data = $this->generatePartialData($data, $qry);

		$data['total_results_count'] = $total_results_count;
		$data['total_viewing_count'] = $obj['take'];

		// if number of view is equal or bigger than number of total results then has_results is full
		if($data['total_viewing_count'] >= $data['total_results_count']){
			$data['has_searchResults'] = false;
			$data['total_viewing_count'] = $data['total_results_count'];
		}else{
			$data['has_searchResults'] = true;
		}



		// Saved advanced search filters in data
		$asfn = new AdvancedSearchFilterName;
		$data['savedFilters'] = $asfn->getAllAdvancedSearchFilter();

		return View('advancedStudentSearch.ajax.searchResults', $data);
	}

	/**
	 * This method generates partial data for advanced search to 
	 * lighten the amount of data passed to view on load to improve page load performance
	 * @return view
	 */
	public function generatePartialData($data, $qry){
    
        $this_college = College::on('rds1')->where('id', $data['org_school_id'])->first();

        $data['searchResults'] = array();

        $obj = Cache::get(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id']);

        foreach ($qry as $key) {
            $tmp = array();

            $tmp['hashed_user_id'] = Crypt::encrypt($key->user_id);

            $tmp['user_id'] = $key->user_id;

            if (isset($key->is_rec)) {
                $tmp['already_recruited'] = true;
            }else{
                $tmp['already_recruited'] = false;
            }

            $tmp['fname'] = ucwords($key->fname);

            $tmp['lname'] = ucwords($key->lname);

            $in_college = $key->in_college;

            $tmp['in_college'] = $key->in_college;

            $gpa_to_select = null;

			if (isset($obj['arrObj'])) {
				foreach ($obj['arrObj'] as $k) {	
					if ($k->filter == "gpaMin_filter" || $k->filter == "gpaMax_filter") {
						$gpa_to_select ='hs_gpa';
						break;
					}elseif ($k->filter == "hsWeightedGPAMin_filter" || $k->filter == "hsWeightedGPAMax_filter") {
						$gpa_to_select ='weighted_gpa';
						break;
					}elseif ($k->filter == "collegeGPAMin_filter" || $k->filter == "collegeGPAMax_filter") {
						$gpa_to_select ='overall_gpa';
						break;
					}	
				}
			}

            if(isset($gpa_to_select)){
            	$tmp['gpa'] = $key->$gpa_to_select;
            }

            if(!isset($tmp['gpa']) && $in_college){

                if(isset($key->overall_gpa) && !isset($tmp['gpa'])){
                    $tmp['gpa'] = $key->overall_gpa;
                }elseif(!isset($tmp['gpa'])){
                    $tmp['gpa'] = 'N/A';
                }
            }elseif(!isset($tmp['gpa']) && !$in_college){
            	if(isset($key->hs_gpa) && !isset($tmp['gpa'])){
                    $tmp['gpa'] = $key->hs_gpa;
                }elseif(!isset($tmp['gpa'])){
                    $tmp['gpa'] = 'N/A';
                }
            }

            $tmp['sat_score'] = isset($key->sat_total) ? $key->sat_total : 'N/A';
            $tmp['act_composite'] = isset($key->act_composite) ? $key->act_composite : 'N/A';
            $tmp['country_code'] = isset($key->country_code) ? $key->country_code : 'N/A';
            $tmp['country_name'] = isset($key->country_name) ? $key->country_name : 'N/A';

            $tmp['major'] = isset($key->major_name) ? $key->major_name : 'N/A';
            $tmp['already_recruited'] = isset($key->is_rec) ? true : false;

            $trc = $key->transcript_arr;

			if (isset($trc) && !empty($trc)) {
				$trc = explode(",", $trc);
			}else{
				$trc = NULL;
			}

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
            $data['searchResults'] [] = $tmp;   
        }

        // $data['has_searchResults'] = true;

        // if (isset($data['searchResults']) && $data['total_viewing_count'] < $data['total_results_count']) {
        //     $data['has_searchResults'] = true;
        // }else{
        //     $data['has_searchResults'] = false;
        // }

        return $data;
    }


	/**
	 * This method generates the data for advanced search profile view on ajax call
	 *
	 * @return view
	 */
	public function loadProfileData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$uid = Crypt::decrypt($input['hashed_user_id']);

		$arrObj = null;
		$crf = new CollegeRecommendationFilters;

		$qry = $crf->globalMethodGenerateFilterQry($arrObj, true); 

		if (isset($data['is_organization']) && $data['is_organization'] == 1) {
			$qry = $qry->leftjoin('recruitment as r',  function($q) use($data){
						$q->where('r.college_id', '=', $data['org_school_id']);
						$q->on('r.user_id', '=', 'userFilter.id');
					});

			if($data['bachelor_plan'] == 1){

				$data['paid_customer'] = true;

			}else{
				$data['paid_customer'] = false;
			}

		}elseif (isset($data['agency_collection'])) {
			$qry = $qry->leftjoin('agency_recruitment as r', function($q) use($data){
						$q->where('r.agency_id', '=', $data['agency_collection']->agency_id);
						$q->on('r.user_id', '=', 'userFilter.id');
					});
			if ($data['agency_collection']->is_trial_period == 1 || $data['agency_collection']->balance > 0) {
				$data['paid_customer'] = true;
			}else{
				$data['paid_customer'] = false;
			}
		}

		$qry =  $qry->groupby('userFilter.id')
					->select('userFilter.id as user_id', 'userFilter.fname', 'userFilter.lname', 'userFilter.in_college', 'userFilter.id as userFilterser_id', 'userFilter.country_id',
							'userFilter.hs_grad_year', 'userFilter.college_grad_year', 'userFilter.profile_img_loc', 'userFilter.financial_firstyr_affordibility',
							'userFilter.planned_start_yr', 'userFilter.interested_school_type', 'userFilter.skype_id', 'userFilter.address as userAddress', 'userFilter.email as userEmail', 'userFilter.planned_start_term', 'userFilter.planned_start_yr', 'userFilter.birth_date',
							'userFilter.city as userCity', 'userFilter.state as userState', 'userFilter.zip as userZip', 'userFilter.phone as userPhone', 'userFilter.email_confirmed', 'userFilter.txt_opt_in as userTxt_opt_in',
							'userFilter.fb_id', 'userFilter.verified_phone',
							's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.toefl_total', 's.ielts_total', 's.weighted_gpa',
							'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
							'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
							'ct.country_code', 'ct.country_name',
							'dt.display_name as degree_name',
							DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY o.id ASC SEPARATOR ', ') as major_name"),
							'p.profession_name',
							'r.id as is_rec',
							'puv.status as plexuss_status', 
							'puv.verified_skype',
							'puv.phonecall_verified')
					->leftjoin('plexuss_users_verifications as puv', 'puv.user_id', '=', 'userFilter.id')

					->where('userFilter.id', $uid)
					// ->where('userFilter.is_alumni', 0)
					// ->where('userFilter.is_parent', 0)
					// ->where('userFilter.is_counselor', 0)
					->where('userFilter.is_organization', 0)
					->where('userFilter.is_agency', 0)
					->where('userFilter.is_plexuss', 0);

		$qry = $qry->get();
		//nothing to do with $obj
		$ctry = new Country;
		$data['country_list'] = $ctry->getAllCountriesAndIdsWithCountryCode();
		
		$data = $this->generateData($data, $qry);
		// If this is a plexuss user then show the matches colleges for this user, and include plexuss notes.
		if(Session::has('handshake_power')){
			$data['uid'] = $uid;
			$data['loginas'] = '/sales/loginas/'.Crypt::encrypt($uid);
			$note = AdvancedSearchNote::where('user_id', $data['uid'])
									  ->first();
			if (isset($note)) {
				$data['plexuss_note'] = $note->note;
				$data['plexuss_note_updated_at'] = $this->xTimeAgo($note->updated_at ,date("Y-m-d H:i:s"));
			}
			
		}

		return View('advancedStudentSearch.ajax.profileData', $data);
	}

	/**
	 * This method generates the data for advanced search
	 *
	 * @return view
	 */
	private function generateData($data, $qry){

		// Tracking pages model
		$tp = new TrackingPage;

		$this_college = College::where('id', $data['org_school_id'])->first();

		$data['searchResults'] = array();

		$obj = Cache::get(env('ENVIRONMENT') .'_'.'advancedStudentSearchObj_'.$data['user_id']);

		$gpa_to_select = null;

		if (isset($obj['arrObj'])) {
			foreach ($obj['arrObj'] as $key) {	
				if ($key->filter == "gpaMin_filter" || $key->filter == "gpaMax_filter") {
					$gpa_to_select ='hs_gpa';
					break;
				}elseif ($key->filter == "hsWeightedGPAMin_filter" || $key->filter == "hsWeightedGPAMax_filter") {
					$gpa_to_select ='weighted_gpa';
					break;
				}elseif ($key->filter == "collegeGPAMin_filter" || $key->filter == "collegeGPAMax_filter") {
					$gpa_to_select ='overall_gpa';
					break;
				}	
			}
		}
		

		foreach ($qry as $key ) {
			
			$tmp = array();

			$tmp['hashed_user_id'] = Crypt::encrypt($key->user_id);
			
			$tmp['hashed_id'] = Crypt::encrypt($key->user_id);

		
			if (isset($key->is_rec)) {
				$tmp['already_recruited'] = true;
			}else{
				$tmp['already_recruited'] = false;
			}
			
			$tmp['fname'] = ucwords($key->fname);

			$tmp['lname'] = ucwords($key->lname);
			
			$tmp['name'] = $tmp['fname'] . ' ' . $tmp['lname'];
			
			$tmp['planned_start_yr'] 	   = isset($key->planned_start_yr) ? $key->planned_start_yr : 'N/A';
			$tmp['interested_school_type'] = isset($key->interested_school_type) ? $key->interested_school_type : 'N/A';
			$tmp['skype_id'] 			   = isset($key->skype_id) ? $key->skype_id : 'N/A';
			$tmp['userEmail'] 			   = isset($key->userEmail) ? $key->userEmail : 'N/A';
			$tmp['userAddress'] 		   = isset($key->userAddress) ? $key->userAddress : '';
			$tmp['userCity'] 			   = isset($key->userCity) ? $key->userCity : '';
			$tmp['userState']              = isset($key->userState) ? $key->userState : '';
			$tmp['userZip'] 			   = isset($key->userZip) ? $key->userZip : '';
			$tmp['userPhone'] 			   = isset($key->userPhone) ? $key->userPhone : 'N/A';
			$tmp['email_confirmed'] 	   = isset($key->email_confirmed) ? $key->email_confirmed : 0;
			$tmp['fb_id'] 			   	   = isset($key->fb_id) ? $key->fb_id : '';

			$tmp['rec_id']				   = NULL;
			$tmp['student_user_id']        = $key->user_id;
			$tmp['show_matched_colleges']  = false;

			if(Session::has('handshake_power') && $data['is_plexuss'] == 1){
				$tmp['show_matched_colleges'] = true;
			}
			$tmp['currentPage']            = 'admin-student-search';
			$tmp['page']				   = 'search';
			$tmp['loginas'] 			   = '/sales/loginas/'.Crypt::encrypt($key->user_id);
			$tmp['plexuss_status']  	   = isset($key->plexuss_status) ? $key->plexuss_status : 0;
			$tmp['verified_skype']  	   = isset($key->verified_skype) ? $key->verified_skype : 0;
			$tmp['phonecall_verified']     = isset($key->phonecall_verified) ? $key->phonecall_verified : 0;
			$tmp['start_term'] 			   = ucwords($key->planned_start_term. " ". $key->planned_start_yr);
			$tmp['birth_date'] 			   = isset($key->birth_date) ? $key->birth_date : NULL;
			$tmp['why_recommended']		   = NULL;
			$tmp['verified_phonecall']	   = NULL;
			$tmp['verified_phone']  	   = isset($key->verified_phone) ? $key->verified_phone : 0;
			$tmp['post_students']		   = NULL;
			$tmp['is_plexuss']			   = isset($data['is_plexuss']) ? $data['is_plexuss'] : NULL;
			$tmp['matched_colleges']	   = NULL;

			$in_college = $key->in_college;

			$tmp['in_college'] = $key->in_college;

			if (!isset($key->hs_grad_year) || $key->hs_grad_year == 0) {
				$tmp['hs_grad_year'] = 'N/A';
			}else{
				$tmp['hs_grad_year'] = $key->hs_grad_year;
				$tmp['grad_year'] = $key->hs_grad_year;
			}

			if (!isset($key->college_grad_year) || $key->college_grad_year == 0) {
				$tmp['college_grad_year'] = 'N/A';
			}else{
				$tmp['college_grad_year'] = $key->college_grad_year;
				$tmp['grad_year'] = $key->college_grad_year;
			}
			
			$tmp['profile_img_loc'] = $key->profile_img_loc;

			if (isset($gpa_to_select)) {
				$tmp['gpa'] = $key->$gpa_to_select;
			}
			//print_r('<pre>'.$key.'</pre>');exit();
			if($in_college){
				
				if(isset($key->overall_gpa) && !isset($tmp['gpa'])){
					$tmp['gpa'] = $key->overall_gpa;
				}elseif(!isset($tmp['gpa'])){
					$tmp['gpa'] = 'N/A';
				}

				if(isset($key->collegeName)){
					$tmp['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->collegeName)))); 

					$tmp['school_city'] = $key->collegeCity;
					$tmp['school_state'] = $key->collegeState;
					if ($tmp['current_school'] == "Home Schooled") {
						$tmp['address'] = $tmp['current_school'];
					}else{
						$tmp['address'] = $tmp['current_school'].', '.ucwords($tmp['school_city']). ', '.$tmp['school_state'];
					}
					
				}else{
					$tmp['current_school'] = 'N/A';
					$tmp['school_city'] = 'N/A';
					$tmp['school_state'] = 'N/A';

					$tmp['address'] = 'N/A';
				}

			}else{

				if(isset($key->hs_gpa) && !isset($tmp['gpa'])){
					$tmp['gpa'] = $key->hs_gpa;
				}elseif(!isset($tmp['gpa'])){
					$tmp['gpa'] = 'N/A';
				}
				

				if(isset($key->hsName)){
					$tmp['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->hsName)))); 
					$tmp['school_city'] = $key->hsCity;
					$tmp['school_state'] = $key->hsState;

					if ($tmp['current_school'] == "Home Schooled") {
						$tmp['address'] = $tmp['current_school'];
					}else{
						$tmp['address'] = $tmp['current_school'].', '.ucwords($tmp['school_city']). ', '.$tmp['school_state'];
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
				$tmp['country_id'] = $key->country_id;
			}else{
				$tmp['country_code'] = 'N/A';
				$tmp['country_name'] = 'N/A';			
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

			$usr_college_info = array();

			if (isset($this_college)) {
				$usr_college_info['name'] = $this_college->school_name;
				$usr_college_info['slug'] = $this_college->slug;
				$usr_college_info['logo'] = $this_college->logo_url;
				$usr_college_info['page_views'] = $tp->getNumCollegeView($key->user_id,$this_college->slug);
			}
			

			$tmp['college_info'] = $usr_college_info;

			$rec = Recruitment::where('user_id', $key->user_id)
								->join('colleges as c', 'c.id', '=','recruitment.college_id')
								->where('recruitment.user_recruit', '=', 1)
								->where('recruitment.status', '=', 1)
								->select('c.id as college_id', 'c.school_name', 'c.slug','c.logo_url');

			if (isset($this_college)) {
				$rec = $rec->where('c.id', '!=', $data['org_school_id'])
						   ->get();
			}else{
				$rec = $rec->get();
			}

			$user_id = $key->user_id;
			$competitor_colleges = array();

			foreach ($rec as $key) {

				$arr = array();
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
			$tmp['other'] = false;
			$tmp['passport'] = false;
			$tmp['essay'] = false;


			// set uploads with upload of application type 
			$arr = array();
			$arr['doc_type'] = 'application';
			$arr['mime_type'] = 'text/html';
			$arr['path'] = '/view-student-application/'.$tmp['hashed_id'];
			$domain = 'https://plexuss.com';

			if( env('ENVIRONMENT') == 'DEV' ){
				$domain = 'http://plexuss.dev';
			}

			$arr['transcript_name'] = '/generatePDF?url='.$domain.$arr['path'];

			$uploads_arr[] = $arr;

			foreach ($trc as $key) {
				$arr = array();

				$arr['doc_type'] = $key->doc_type;
				$arr['path'] = $key->transcript_path. $key->transcript_name;
				$arr['transcript_name'] = $key->transcript_name;
				$arr['transcript_id'] = $key->id;
				$arr['transcript_label'] = $key->label;
				
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
				if ($key->doc_type == 'passport') {
					$tmp['passport'] = true;
				}
				if ($key->doc_type == 'essay') {
					$tmp['essay'] = true;
				}

				$uploads_arr[] = $arr;
			}

			$tmp['upload_docs'] = $uploads_arr;

			$tmp['show_matched_colleges'] = false;
			if(Session::has('handshake_power') && $data['is_plexuss'] == 1){
				$tmp['show_matched_colleges'] = true;
			}

			// Get applied colleges and application status
			$uac = DB::connection('rds1')->table('users_applied_colleges as uac')
										 ->join('colleges as c', 'c.id', '=', 'uac.college_id')
										 ->leftjoin('colleges_application_status as cas', 'c.id', '=', 'cas.college_id')
										 ->select('c.school_name', 'c.logo_url', 'c.slug', 'c.id as college_id', 'cas.status', 'uac.submitted')
										 ->where('uac.user_id', $user_id)
										 ->where('cas.user_id', $key->user_id)
										 ->groupBy('c.id')
										 ->orderBy(DB::raw('ISNULL(cas.status), cas.status'), 'ASC')
										 ->get();

			$tmp['applied_colleges'] = $uac;

			$ucq_state = UsersCustomQuestion::select('application_state')
									   ->where('user_id', $user_id)->first();

			if (!empty($ucq_state))
				$tmp['application_state'] = $ucq_state->application_state;

			$data['searchResults'] [] = $tmp;	
		}
		
		if (isset($data['searchResults']) && count($data['searchResults']) == $obj['take']) {
			$data['has_searchResults'] = true;
		}else{
			$data['has_searchResults'] = false;
		}

		return $data;
	}

	public function setRecruit(){

		$input = Request::all();

		//$hashed_user_id = 
		if (!isset($input['userid'])) {
			return 'failed';
		}
		$user_id = Crypt::decrypt($input['userid']);

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$num_allowed_recruits = 0;
		$now = Carbon::now();

		if (isset($data['is_organization']) && $data['is_organization'] == 1) {
			if ($data['bachelor_plan'] == 1) {
				$num_allowed_recruits = 999999;
			}else{
				$num_allowed_recruits = 100;
			}

			$rec = Recruitment::where('college_id', $data['org_school_id'])
								->where('type', 'advance_search')
								->where('created_at', '>=', $now->startOfMonth())
								->where('created_at', '<=', $now->today())
								->count();

			if ($rec >= $num_allowed_recruits) {
				return "limit reached";
			}

			$attr = array('user_id' => $user_id, 'college_id' => $data['org_school_id'] );

			$val = array('user_id' => $user_id, 'college_id' => $data['org_school_id'],
						 'user_recruit' => 0, 'college_recruit' => 1,
						 'reputation' => 0,  'location' => 0, 'tuition' => 0,
						 'program_offered' => 0, 'athletic' => 0, 'onlineCourse' => 0,
						 'religion' => 0, 'campus_life' => 0, 'status' => 1, 'type' => 'advance_search');
				
			$tmp = Recruitment::updateOrCreate($attr, $val);

			////*************Add Notification to the user *********************///////
			$ntn = new NotificationController();
			$ntn->create( $data['school_name'], 'user', 3, null, $data['user_id'] , $user_id);

		}elseif (isset($data['agency_collection'])) {
			$num_allowed_recruits = 5;

			$rec = AgencyRecruitment::where('agency_id', $data['agency_collection']->agency_id)
								->where('type', 'advance_search')
								->where('created_at', '>=', $now->today())
								->where('created_at', '<=', $now->tomorrow())
								->count();

			if ($rec >= $num_allowed_recruits) {
				return "limit reached";
			}

			$attr = array('user_id' => $user_id, 'agency_id' => $data['agency_collection']->agency_id );

			$val = array('user_id' => $user_id, 'agency_id' => $data['agency_collection']->agency_id,
						 'user_recruit' => 0, 'agency_recruit' => 1, 'active' => 1, 'type' => 'advance_search');
				
			$tmp = AgencyRecruitment::updateOrCreate($attr, $val);

			////*************Add Notification to the user *********************///////
			
			$ntn = new NotificationController();
			$ntn->create( $data['agency_collection']->name, 'user', 8, null, $data['user_id'] , $user_id);	
			
		}

		return "success";
	}

	public function saveFilter(){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$asfn = new AdvancedSearchFilterName;
		$tmp = $asfn->saveAdvancedSearchFilter($data, $input);
		
		return $tmp;
	}

	public function getAdvancedSearchFilter(){

		$input = Request::all();

		$id = Crypt::decrypt($input['id']);

		$asfn = new AdvancedSearchFilterName;
		$ret = $asfn->getAdvancedSearchFilter($id);

 		return $this->update($ret);
	}

	public function deleteFilter(){

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		
		$asfn = new AdvancedSearchFilterName;

		$asfn->clearAdvancedSearchFilter($data, $input);

		return "success";
	}

	public function addStudentManual(){
		$input = Request::all();

		$type          = $input['type'];
		$user_id       = $input['user_id'];
		$college_id    = $input['college_id'];
		$aor_id        = ($input['aor_id'] == -1 || empty($input['aor_id'])) ? NULL : $input['aor_id'];
		$org_portal_id = ($input['org_portal_id'] == -1 || empty($input['org_portal_id'])) ? NULL : $input['org_portal_id'];
		$aor_portal_id = ($input['aor_portal_id'] == -1 || empty($input['aor_portal_id'])) ? NULL : $input['aor_portal_id'];
		$school_name   = $input['school_name'];
		$rec_id = isset($input['rec_id']) ? $input['rec_id'] : NULL;

		// Adding the the user to Recruitment Tag table
		if (!isset($aor_id) && !isset($aor_portal_id) && !isset($org_portal_id) ) {
			$attr = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => -1, 
						  'org_portal_id' => -1, 'aor_portal_id' => -1);
			$val  = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => -1, 
						  'org_portal_id' => -1, 'aor_portal_id' => -1);

			$update = RecruitmentTag::updateOrCreate($attr, $val);
		}else{

			$attr = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => $aor_id, 
						  'org_portal_id' => $org_portal_id, 'aor_portal_id' => $aor_portal_id);
			$val  = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => $aor_id, 
						  'org_portal_id' => $org_portal_id, 'aor_portal_id' => $aor_portal_id);

			$update = RecruitmentTag::updateOrCreate($attr, $val);
		}

		
		switch ($type) {
			case 'PreScreened':
				$attr = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => $aor_id,
							  'org_portal_id' => $org_portal_id, 'aor_portal_id' => $aor_portal_id );
				$val  = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => $aor_id,
							  'org_portal_id' => $org_portal_id, 'aor_portal_id' => $aor_portal_id, 'active' => 1); 

				$update = PrescreenedUser::updateOrCreate($attr, $val);

				if (isset($rec_id)) {
					// Hide from other buckets if rec_id set.
					$rec = Recruitment::find($rec_id);

					if (!empty($rec)) {
						if ($rec->college_recruit < 9) {
							$rec->college_recruit = $rec->college_recruit + 10;
						}

						if ($rec->user_recruit < 9) {
							$rec->user_recruit = $rec->user_recruit + 10;
						}

						$rec->status = 0;

						$rec->save();
					}
				}

				break;

			case 'Pending':
				$attr = array('user_id' => $user_id, 'college_id' => $college_id,
						  'aor_id' => $aor_id );

				$val = array('user_id' => $user_id, 'college_id' => $college_id,
							 'user_recruit' => 0, 'college_recruit' => 1, 'aor_id' => $aor_id,
							 'reputation' => 0,  'location' => 0, 'tuition' => 0,
							 'program_offered' => 0, 'athletic' => 0, 'onlineCourse' => 0,
							 'religion' => 0, 'campus_life' => 0, 'status' => 1, 'type' => 'manual_pending');

				$tmp = Recruitment::updateOrCreate($attr, $val);

				$ob = DB::connection('rds1')->table('organization_branches AS ob')
											->join('organization_branch_permissions AS obp', 'ob.id', '=', 'obp.organization_branch_id')
											->where('ob.school_id', $college_id)
											->where('obp.super_admin', 1)
											->select('obp.user_id')
											->first();
				if (isset($ob)) {
					////*************Add Notification to the user *********************///////
					$ntn = new NotificationController();
					$ntn->create( $school_name, 'user', 3, null, $ob->user_id , $user_id);
				}

				// Set inactive prescreen if there.
				$psu_qry = PrescreenedUser::select('active')
						   ->where('user_id', $user_id)
						   ->where('college_id', $college_id)
						   ->where('aor_id', $aor_id)
						   ->where('org_portal_id', $org_portal_id)
						   ->where('aor_portal_id', $aor_portal_id)
						   ->update(['active' => 0]);
			
				break;

			case 'VerifiedApp':

				if (!isset($rec_id)) {
					// Find rec_id based on information passed
					$rec = Recruitment::select('id')
						   ->where('user_id', $user_id)
						   ->where('college_id', $college_id)
						   ->where('aor_id', $aor_id)
						   ->first();

					if (!empty($rec)) {
						$rec_id = $rec->id;
					} else { // Create a new recruitment entry if none exists
						$attr = array('user_id' => $user_id, 'college_id' => $college_id,
					  			'aor_id' => $aor_id );

						$val = array('user_id' => $user_id, 'college_id' => $college_id,
								 'user_recruit' => 0, 'college_recruit' => 1, 'aor_id' => $aor_id,
								 'reputation' => 0,  'location' => 0, 'tuition' => 0,
								 'program_offered' => 0, 'athletic' => 0, 'onlineCourse' => 0,
								 'religion' => 0, 'campus_life' => 0, 'status' => 1, 'type' => 'inquiry');

						$tmp = Recruitment::updateOrCreate($attr, $val);
						$rec_id = $tmp->id;
					}
				}

				// Add to verified apps
				$attr = array('user_id' => $user_id, 'college_id' => $college_id);

				$val  = array('aor_id' => $aor_id, 'aor_portal_id' => $aor_portal_id, 'org_portal_id' => $org_portal_id, 
							  'rec_id' => $rec_id);
				
				$update =  RecruitmentVerifiedApp::updateOrCreate($attr, $val);
				
				if (isset($rec_id)) {
					// Hide from recruitment if there.
					$rec = Recruitment::find($rec_id);

					if (!empty($rec)) {
						if ($rec->college_recruit < 9) {
							$rec->college_recruit = $rec->college_recruit + 10;
						}

						if ($rec->user_recruit < 9) {
							$rec->user_recruit = $rec->user_recruit + 10;
						}

						$rec->status = 0;

						$rec->save();
					}

					// Hide from prescreened if there.
					$psu = PrescreenedUser::find($rec_id);

					if (!empty($psu)) {
						$psu->active = 0;

						$psu->save();
					} else { // Find prescreened if it exists
						$psu_qry = PrescreenedUser::select('active')
								   ->where('user_id', $user_id)
								   ->where('college_id', $college_id)
								   ->where('aor_id', $aor_id)
								   ->where('org_portal_id', $org_portal_id)
								   ->where('aor_portal_id', $aor_portal_id)
								   ->update(['active' => 0]);
					}
				}

				// Add student to applied colleges
				UsersAppliedColleges::updateOrCreate([ 'user_id' => $user_id, 'college_id' => $college_id ], 
													 [ 'submitted' => 1 ]);

				// Create a student college app status
				CollegesApplicationStatus::firstOrCreate([ 'user_id' => $user_id, 'college_id' => $college_id ]);

				break;

			case 'HandShake':
				$data = array();

				$data['org_school_id'] = $college_id;
				$data['aor_id']	       = $aor_id;
			
				
				$user = User::on('rds1')->find($user_id);

				$today = Carbon::today();
				$now   = Carbon::now();

				$updated_at = $this->rand_date_time($today, $now); 

				$attr = array('user_id' => $user_id, 'college_id' => $data['org_school_id'], 
							  'user_recruit' => 1, 'college_recruit' => 1, 'aor_id' => $data['aor_id'] );

				$val  = array('user_id' => $user_id, 'college_id' => $data['org_school_id'], 
							  'user_recruit' => 1, 'college_recruit' => 1, 'aor_id' => $data['aor_id'],
							  'status' => 1, 'type' => 'manual_hs' );

				$update = Recruitment::updateOrCreate($attr, $val);

				if (isset($data['aor_id'])) {
					$rec = Recruitment::on('rds1')->where('user_id', $user_id)
								  				->where('college_id', $data['org_school_id'])
								  				->where('aor_id', $data['aor_id'])
								  				->first();

					$aor = Aor::find($data['aor_id']);

					// If this is a handshake contract
					if(isset($aor) && $aor->contract == 2 && $rec->college_recruit == 1){
						$this->chargeCollegePerInquiry($rec->id, null, $user_id, $aor->id);
					// end of this is a hanshake contract.
					}
				}else{
					$ob = OrganizationBranch::where('school_id', $data['org_school_id'])->first();
					$rec = Recruitment::on('rds1')->where('user_id', $user_id)
								  				->where('college_id', $data['org_school_id'])
								  				->whereNull('aor_id')
								  				->first();

					if(isset($ob)){
						// If this is a handshake contract
						if($ob->contract == 2 && $rec->college_recruit == 1){
							$this->chargeCollegePerInquiry($rec->id, $ob->id, $user_id);
						}
					}
				}
				
				$ntn = new NotificationController();
				$ntn->create( $user->fname. ' '. $user->lname, 'college', 2, null, $user_id, $data['org_school_id'], $updated_at );

				// if this is a handshake

				$ob = DB::connection('rds1')->table('organization_branches AS ob')
											->join('organization_branch_permissions AS obp', 'ob.id', '=', 'obp.organization_branch_id')
											->where('ob.school_id', $college_id)
											->where('obp.super_admin', 1)
											->select('obp.user_id')
											->first();
				if (isset($ob)) {
				
					$data['user_id'] = $ob->user_id;							
					// add notification to the user
					$ntn = new NotificationController();
					$ntn->create( $school_name, 'user', 2, null, $data['user_id'] , $user_id);

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
						$email_snn = SettingNotificationLog::on('rds1')
														   ->where('type', 'email')
														   ->where('user_id', $user_id)
														   ->where('snn_id', $email_snn_id)
														   ->first();

						$user = User::on('rds1')->find($user_id);

						if (!isset($email_snn)) {
							$mac = new MandrillAutomationController;
							$tmp = array();
							$tmp['college_id'] = $data['org_school_id'];
							$tmp['school_name'] = $school_name;
							$tmp['fname'] = $user->fname;
							$tmp['email'] = $user->email;
							$mac->handshakeNextSteps($tmp);
						
						}

						// if this person has not filtered text
						$text_snn = SettingNotificationLog::on('rds1')
													   ->where('type', 'text')
													   ->where('user_id', $user_id)
													   ->where('snn_id', $text_snn_id)
													   ->first();
						if(!isset($text_snn)){
							$tmp = array();
							$tmp['college_id'] = $data['org_school_id'];
							$tmp['school_name'] = $school_name;

							// Send the text message.
							// $tc = new TwilioController;
							// $tc->sendHandshakeTxt($user, $tmp);
						}		
					}
				}

				// Set inactive prescreen if there.
				$psu_qry = PrescreenedUser::select('active')
						   ->where('user_id', $user_id)
						   ->where('college_id', $college_id)
						   ->where('aor_id', $aor_id)
						   ->where('org_portal_id', $org_portal_id)
						   ->where('aor_portal_id', $aor_portal_id)
						   ->update(['active' => 0]);

				break;
			default:
				
				break;
		}
		

		return "success";
	}

	public function searchForCollegesByKeyword(){
		$input = Request::all();

		$ob = new OrganizationBranch;

		$ob = $ob->searchCollegesInOrgBranchesByKeyword($input['keyword']);

		return $ob;
	}

	public function getOrganizationPortalsForThisCollege(){
		$input = Request::all();

		$op = new OrganizationPortal;

		$op = $op->getOrganizationPortalsForThisCollege($input['college_id']);

		$ret = array();

		foreach ($op as $key) {
			$tmp = array();
			if (isset($key->aor_id)) {
				$tmp['aor_id'] = $key->aor_id;
				$tmp['aor_portal_id'] = NULL;
				$tmp['aor_portal_name'] = 'General';
				$tmp['id'] = $key->id;
				$tmp['org_portal_id'] = NULL;
				$tmp['org_portal_name'] = NULL;
				$tmp['school_name'] = $key->school_name;
			}else{
				$tmp['aor_id'] = NULL;
				$tmp['aor_portal_id'] = NULL;
				$tmp['aor_portal_name'] = NULL;
				$tmp['id'] = $key->id;
				$tmp['org_portal_id'] = NULL;
				$tmp['org_portal_name'] = 'General';
				$tmp['school_name'] = $key->school_name;
			}
			break;
		}
		$ret['portals'] = $op;
		if (!isset($tmp)) {
			$tmp = array();

			$tmp['aor_id'] = NULL;
			$tmp['aor_portal_id'] = NULL;
			$tmp['aor_portal_name'] = NULL;
			$tmp['id'] = $input['college_id'];
			$tmp['org_portal_id'] = NULL;
			$tmp['org_portal_name'] = 'General';
			$tmp['school_name'] = '';
		}

		$ret['general'] = (object) $tmp;
	
		return $ret;
	}
}
