<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
ini_set('memory_limit', '1G'); // or you could use 1G

use Request, DB;
use App\User, App\CollegeRecommendation, App\CollegeRanking, App\CollegeRecommendationFilters, App\CountryConflict, App\College, App\OrganizationBranch;
use App\Aor, App\ZipCodes, App\Priority, App\RecommendationTier, App\Agency, App\Recruitment, App\AgencyRecruitment, App\ScholarshipsUserApplied;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\MandrillAutomationController;

class CollegeRecommendationController extends Controller
{
    private $college_suppression_list = array();
	private $agency_suppression_list = array();
	const NUM_OF_RECOMMENDATIONS = 5;
	private $org_branch_id;
	private $filter_sql;
	private $filter_sql_raw;
	private $filtered_user_array = array();
	private $is_paying_customer = false;
	private $num_of_filtered_rec;
	private $country_conflicts = array();

	/**
	 * Generate list of users recommendations for colleges
	 * this is accessible through GET method
	 *
	 * @return void
	 */
	public function index(){

		$input = Request::all();

		if(isset($input['t']) && $input['t'] == "joie3e23riuhneisuio"){
			
			echo "<pre>";
			print_r($this->create());
			echo "</pre>";
			exit();
		}else{
			return redirect( '/' );
		}
	}

    /**
     * Get list of users recommendations from college_recommendations table for today
     * and add them to recruitment table for specific college_ids.
     *
     * @return 'success' string
     */
    public function convertRecommendationsToInquiries() {
        $now = Carbon::now();
        // College IDs we are inserting into inquiries for:
        $college_ids = [1348, 4071, 4124, 3298, 3813, 533, 1349, 1730, 148, 243, 4083, 4138, 527, 1129, 663, 456];

        $recommendations = CollegeRecommendation::where('created_at', '>=', $now->today())
                                                ->select('user_id', 'college_id')
                                                ->whereNotNull('college_id')
                                                ->whereNull('org_portal_id')
                                                ->whereIn('college_id', $college_ids)
                                                ->get();

        // dd($recommendations);
        foreach ($recommendations as $recommendation) {
            $attributes = [
                'user_id' => $recommendation->user_id, 
                'college_id' => $recommendation->college_id,
            ];

            $values = [
                'user_id' => $recommendation->user_id, 
                'college_id' => $recommendation->college_id,
                'type' => 'recommendation',
                'user_recruit' => 1,
                'status' => 1,
            ];

            Recruitment::updateOrCreate($attributes, $values);
        }

        return 'success';
    }

	/**
	 * Generate list of users recommendations for colleges
	 * that are in our network.
	 *
	 * @return void
	 */
	public function create() {

		$tempDate = date("Y-m-d");
		
		$day_of_week = date('l', strtotime( $tempDate));

		if($day_of_week =="Saturday" || $day_of_week == "Sunday"){
			return null;
		}

		$rec_list = array();

		$now = Carbon::now();

		$crf = new CollegeRecommendationFilters;

		$this->country_conflicts = CountryConflict::on('rds1')->select('user_id')->get();

		$filter_no_match = array();

		$today_college_recommendation = CollegeRecommendation::where('created_at', '>=', $now->today())
															->select('college_id','org_portal_id','aor_id')
															->whereNotNull('college_id')
															->groupby('college_id','org_portal_id','aor_id')
															->get();				

		// $potential_clients = DB::table('potential_clients as pc')
		// 					->join('colleges as c', 'c.id', '=', 'pc.college_id')
		// 					->select('c.*');

		// if (isset($today_college_recommendation) && !empty($today_college_recommendation)) {
		// 	$potential_clients = $potential_clients->whereNotIn('c.id', $today_college_recommendation);
		// }

		// $in_our_network = College::where('in_our_network', 1)
		// 						 ->union($potential_clients);

		$in_our_network = College::where('in_our_network', 1)
								 ->leftjoin('college_recommendation_filters as crf', 'crf.college_id', '=', 'colleges.id')
					 			 ->join(DB::raw('(SELECT op.id AS op_id FROM organization_portals AS op WHERE active = 1) AS t2'),function($join)
	 				 			 {
					 		    	// $join->on(DB::raw('t2.op_id = crf.org_portal_id'), DB::raw('OR'), DB::raw('crf.org_portal_id IS NULL'));
					 		    	$join->orWhere('t2.op_id', '=', 'crf.org_portal_id')
					 		    		 ->orWhereNull('crf.org_portal_id');
					 			 })
								->leftjoin('priority', 'priority.college_id', '=', 'colleges.id')
					 		 	 ->select('colleges.id', 'crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id')
								 ->groupby('crf.college_id','crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id')

								 ->join('organization_branches as ob', 'ob.school_id', '=', 'colleges.id')
								 ->where('ob.bachelor_plan', '=', 1);

		if (isset($today_college_recommendation) && !empty($today_college_recommendation)) {
			$in_our_network = $in_our_network->where(function($q) use ($today_college_recommendation){
				foreach ($today_college_recommendation as $key) {
					$org_portal_id = isset($key->org_portal_id) ? $key->org_portal_id : 0;
					$aor_id = isset($key->aor_id) ? $key->aor_id : 0;
					$q = $q->whereRaw('(`colleges`.`id`,coalesce(`crf`.`org_portal_id`,0),coalesce(`crf`.`aor_id`,0)) != ('.$key->college_id.','.$org_portal_id.','.$aor_id.')');
				}
			});
		}

		//exclude colleges without recs that have already run
		if (Cache::has(env('ENVIRONMENT').'_'.'filterNoMatch')){
			$filter_no_match = Cache::get(env('ENVIRONMENT').'_'.'filterNoMatch');
			$in_our_network = $in_our_network->where(function($q) use ($filter_no_match){
				foreach ($filter_no_match as $key => $value) {
					$q = $q->whereRaw('(`colleges`.`id`,coalesce(`crf`.`org_portal_id`,0),coalesce(`crf`.`aor_id`,0),coalesce(`crf`.`aor_portal_id`,0)) != '.$value);
				}
			});
		}

		//$in_our_network = $in_our_network->where('colleges.id', 974);

		//$in_our_network = $in_our_network->orderByRaw("RAND()")->get();
		$in_our_network = $in_our_network->orderBy(DB::raw('ISNULL(priority.id)'))
										 ->orderBy('priority.contract', 'DESC')
										 ->orderBy('priority.type', 'ASC')
										 ->get();
										 
		foreach ($in_our_network as $key) {
			$this->filter_sql = null;
			$this->filter_sql_raw = null;
			// Reset the users supression list for a college
			$this->college_suppression_list = array();

			//Get the supressions list of users for a college
			// $this->setUsersIdsForCollege($key->id);
			$college_id = $key->id;

			// Array of recommended students for a single college
			$unique_college_rec = array();

			//set org branch id


			if(isset($key->aor_id)){
				$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id, $key->aor_id);
			}
			else{
				$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id);
			}
			//Get Recommendation filter

			$arr = array();
			if (isset($this->org_branch_id)) {
				$arr['org_school_id'] = $key->id;

				$arr['org_branch_id'] = $this->org_branch_id;
				if (isset($key->org_portal_id) && !empty($key->org_portal_id)) {
					$arr['default_organization_portal'] = (object) array();
					$arr['default_organization_portal']->id = $key->org_portal_id; 
				}

				if (isset($key->aor_id)) {
					$arr['aor_id'] = $key->aor_id;
				}

				if (isset($key->aor_portal_id)) {
					$arr['aor_portal_id'] = $key->aor_portal_id;
				}

				$qry = $crf->generateFilterQry($arr);

				if (isset($qry)) {				
					$qry = $qry->where('userFilter.recommendation_tier_id', '!=', 0)
							   ->where('userFilter.financial_firstyr_affordibility', '!=', DB::raw("'0.00'"))
							   ->orderBy(DB::raw('userFilter.recommendation_tier_id ASC, rp.probability'), 'DESC')
							   //->orderby('userFilter.recommendation_tier_id', 'rp.probability')
							   ->leftjoin('recommendation_probability as rp', function($query) use($college_id){
							   		$query = $query->on('rp.user_id', '=', 'userFilter.id');
							   		$query = $query->where('rp.college_id', '=', $college_id);
							   });

					$this->filter_sql = $qry;
					$tmp = $qry;
					$tmp = $tmp->select('userFilter.id as userFilterId');
					$this->filter_sql_raw = $this->getRawSqlWithBindings($tmp);
					// $this->filter_sql = $this->getRawSqlWithBindings($qry);
				}
			}

			// Get list of filtered users 
			if (isset($this->filter_sql) && !empty($this->filter_sql)) {
				$users = $this->filteredUsers($key, $key->org_portal_id, $key->aor_id);
				$this->filtered_user_array = array();
				if (isset($users) && !empty($users)) {
					$this->filtered_user_array = $users;
				}
				else {
					$org_portal_id = isset($key->org_portal_id) ? $key->org_portal_id : 0;
					$aor_id = isset($key->aor_id) ? $key->aor_id : 0;
					$aor_portal_id = isset($key->aor_portal_id) ? $key->aor_portal_id : 0;
					array_push($filter_no_match, '('.$key->id.','.$org_portal_id.','.$aor_id.','.$aor_portal_id.')');
					Cache::put(env('ENVIRONMENT').'_'.'filterNoMatch', $filter_no_match, 120);
					continue;
				}
			}else{
				$org_portal_id = isset($key->org_portal_id) ? $key->org_portal_id : 0;
				$aor_id = isset($key->aor_id) ? $key->aor_id : 0;
				$aor_portal_id = isset($key->aor_portal_id) ? $key->aor_portal_id : 0;
				array_push($filter_no_match, '('.$key->id.','.$org_portal_id.','.$aor_id.','.$aor_portal_id.')');
				Cache::put(env('ENVIRONMENT').'_'.'filterNoMatch', $filter_no_match, 120);
				continue;
			}
			
			if(isset($this->filtered_user_array) && !empty($this->filtered_user_array) && isset($this->num_of_filtered_rec) && $this->num_of_filtered_rec > 0){

				$tmp_arr = array();
				$rec_list[] = $this->takeNRecommendations($tmp_arr);
				$this->filter_sql = null;
				$this->filtered_user_array = array();
				continue;
			}else { //if the targetting returns 0 recs, and the school have targeting then skip
				$this->filter_sql = null;
				$this->filtered_user_array = array();
				continue;
			}

			// Get list of users who liked the college
			$users = $this->usersLikedCollege($key->id);
	
			if (isset($users) && !empty($users)) {
				$unique_college_rec[] = $users;
			}

			// Get list of users who viewed the college
			$users = $this->usersViewedCollege($key);

			if (isset($users) && !empty($users)) {
				$unique_college_rec[] = $users;
			}
			
			// Get list of users who compated the college against other colleges
			$users = $this->usersComparedCollege($key);

			if (isset($users) && !empty($users)) {
				$unique_college_rec[] = $users;
			}
		
			// Get list of users who have been recommended this college
			$users = $this->usersRecommendedCollege($key->id);

			if (isset($users) && !empty($users)) {
				$unique_college_rec[] = $users;
			}
			
			// Get list of users who are around this college
			$users = $this->usersAroundTheCollege($key);

			if (isset($users) && !empty($users)) {
				$unique_college_rec[] = $users;
			}
		
			// Get list of users who want to get recruited by similar tier college
			$users = $this->similarTierColleges($key->id);

			if (isset($users) && !empty($users)) {
				$unique_college_rec[] = $users;
			}

			// Get list of users randomly
			$users = $this->randomUsers($key);

			if (isset($users) && !empty($users)) {
				$unique_college_rec[] = $users;
			}
			

			if (isset($unique_college_rec) && !empty($unique_college_rec)) {

				$arr = $this->takeNRecommendations($unique_college_rec);
				if (isset($arr) && !empty($arr)) {
					$rec_list[] = $arr;
				}
				
			}

			$this->filter_sql = null;
			$this->filtered_user_array = array();			
		}

		return $rec_list;
	}

	/**
	 * Generate list of users recommendations for colleges
	 * that are in our network.
	 *
	 * @return void
	 */
	public function generateScholarshipRecommendation(){

		$tempDate = date("Y-m-d");
		
		$day_of_week = date('l', strtotime( $tempDate));

		if($day_of_week =="Saturday" || $day_of_week == "Sunday"){
			return null;
		}

		$rec_list = array();

		$now = Carbon::now();

		$crf = new CollegeRecommendationFilters;

		$main_qry = DB::connection('rds1')->table('college_recommendation_filters as cr')
										  ->join('scholarship_verified as sv', 'sv.id', '=', 'cr.scholarship_id')
		 								  ->whereNotNull('cr.scholarship_id')
									   	  ->groupby('cr.scholarship_id')
									   	  ->select('cr.scholarship_id', 'sv.num_of_rec')
									      ->get();

		foreach ($main_qry as $key) {

			$arr = array();
			$arr['scholarship_id'] = $key->scholarship_id;
			
			$qry = $crf->generateFilterQry($arr);

			if (isset($qry)) {				
				// $qry = $qry->where('userFilter.recommendation_tier_id', '!=', 0)
				// 		   ->where('userFilter.financial_firstyr_affordibility', '!=', DB::raw("'0.00'"))
				// 		   ->orderBy(DB::raw('userFilter.recommendation_tier_id ASC, rp.probability'), 'DESC');
						   //->orderby('userFilter.recommendation_tier_id', 'rp.probability')
						   // ->leftjoin('recommendation_probability as rp', function($query) use($college_id){
						   // 		$query = $query->on('rp.user_id', '=', 'userFilter.id');
						   // 		$query = $query->where('rp.college_id', '=', $college_id);
						   // });
				$qry = $qry->orderBy(DB::raw("RAND()"))
						   ->groupby('userFilter.id')
						   ->take($key->num_of_rec)
						   ->leftjoin('scholarships_user_applied as sua', function($q) use($key){
						   				$q->on('sua.user_id', '=', 'userFilter.id');
						   				$q->on('sua.scholarship_id', '=', DB::raw($key->scholarship_id));
						   })
						   ->whereNull('sua.id')
						   ->select('userFilter.id as user_id')
						   ->get();

				if (isset($qry)) {
					foreach ($qry as $k) {
						$attr = array('user_id' => $k->user_id, 'scholarship_id' => $key->scholarship_id);
						$val  = array('user_id' => $k->user_id, 'scholarship_id' => $key->scholarship_id, 
							'status' => 'recommendation');
						ScholarshipsUserApplied::updateOrCreate($attr, $val);
					}
				}

				// $this->filter_sql = $qry;
				// $tmp = $qry;
				// $tmp = $tmp->select('userFilter.id as userFilterId');
				// $this->filter_sql_raw = $this->getRawSqlWithBindings($tmp);
				// $this->customdd($this->filter_sql_raw);
				// exit();
				// $this->filter_sql = $this->getRawSqlWithBindings($qry);
			}
		}
	}

	public function sendEmailToUsersWhoReceivedScholarshipRecommendation(){

		$qry = DB::connection('rds1')->table('scholarships_user_applied as sua')
									 ->join('users as u', 'u.id', '=', 'sua.user_id')
									 ->join('scholarship_verified as sv', 'sv.id', 'sua.scholarship_id')
									 ->where('sua.email_sent', 0)
									 ->where('sua.status', 'recommendation')
									 ->orderBy('u.id', 'DESC')
									 ->select('sv.*', 'sua.user_id', 'u.email')
									 ->get();
		
		$template_name = 'plexuss_scholarship_email_user_provider_r2_four';
		$reply_email   = 'support@plexuss.com';

		$mda = new MandrillAutomationController();

		// dd($qry);
		if (isset($qry)) {
			$user_id = NULL;

			foreach ($qry as $key) {

				$email = $key->email;
				
				if (!isset($user_id)) {

					$cnt = 1;
					$params = array();
					$user_id = $key->user_id;

					$params['scholarship_title_'.$cnt] 		= $key->scholarship_title;
					$params['scholarshipsub_title_'.$cnt]   = $key->scholarshipsub_title;
					$params['scholarship_max_amount_'.$cnt] = $key->max_amount;
					$params['scholarship_deadline_'.$cnt]   = $key->deadline;

				}elseif ($user_id != $key->user_id) {

					$mda->generalEmailSend($reply_email, $template_name, $params, $email);
					
					ScholarshipsUserApplied::where('user_id', $user_id)
										   ->where('status', 'recommendation')
										   ->update(array('email_sent' => 1));

					$cnt = 1;
					$params = array();
					$user_id = $key->user_id;

					$params['scholarship_title_'.$cnt] 		= $key->scholarship_title;
					$params['scholarshipsub_title_'.$cnt]   = $key->scholarshipsub_title;
					$params['scholarship_max_amount_'.$cnt] = $key->max_amount;
					$params['scholarship_deadline_'.$cnt]   = $key->deadline;

				}else{

					$params['scholarship_title_'.$cnt] 		= $key->scholarship_title;
					$params['scholarshipsub_title_'.$cnt]   = $key->scholarshipsub_title;
					$params['scholarship_max_amount_'.$cnt] = $key->max_amount;
					$params['scholarship_deadline_'.$cnt]   = $key->deadline;
				}
				
				$cnt++;
			}

			$mda->generalEmailSend($reply_email, $template_name, $params, $email);
			ScholarshipsUserApplied::where('user_id', $user_id)
										   ->where('status', 'recommendation')
										   ->update(array('email_sent' => 1));
		}

		
	}

	/**
	 * Generate list of users recommendations for colleges agencies
	 *
	 * @return void
	 */
	public function createAgencyRec($agency_id = NULL) {

		$now = Carbon::now();
		$crf = new CollegeRecommendationFilters;
		// Agency Recommendation starts here

		$unique_college_rec = array();
		
		$agency = Agency::where('active', 1);

		isset($agency_id) ? $agency->where('agency.id', $agency_id) : NULL;

		$today_agency_recommendation = CollegeRecommendation::where('created_at', '>=', $now->today())
															->select('agency_id')
															->groupby('agency_id')
															->whereNotNull('agency_id');
															
		if (!isset($agency_id)) {
			$today_agency_recommendation = $today_agency_recommendation->get()
																	   ->toArray();
			if(isset($today_agency_recommendation) && !isset($today_agency_recommendation[0]['agency_id'])){
				unset($today_agency_recommendation[0]);
			}

			if (isset($today_agency_recommendation) && !empty($today_agency_recommendation)) {
				$agency = $agency->where(function($q) use ($today_agency_recommendation){
					foreach ($today_agency_recommendation as $key => $value) {
						$q = $q->where('agency.id', '!=', $value);
					}
				});
			}
		}else{
			$leads_today_cnt = $today_agency_recommendation->where('agency_id', $agency_id)->count();
		}
		
		$agency = $agency->get();
		
		$this->agency_suppression_list = array();

		$ret = array();
		$ret['status']    = 'failed';
		$ret['data']      = array();
		$ret['error_msg'] = '';
		foreach ($agency as $key) {
			
			if (isset($leads_today_cnt)) {
				if ($leads_today_cnt >= $key->max_num_of_filtered_rec) {
					$ret['error_msg'] = 'You have reached the maximum amount of leads for today. Please check back tomorrow, to receive more leads!';
					continue;
				}
			}
			
			$unique_college_rec = array();

			//Get Recommendation filter
			$arr = array();
			$arr['agency_collection'] = $key;
			$arr['org_school_id'] = null;

			$arr['org_branch_id'] = null;

			$qry = $crf->generateFilterQry($arr);
	
			if (isset($qry)) {
				$qry = $qry->groupby('userFilter.id')->select('userFilter.*');
				$this->filter_sql = $this->getRawSqlWithBindings($qry);
			}

			// Get list of filtered users 
			if (isset($this->filter_sql)) {
				$this->num_of_filtered_rec = $key->num_of_filtered_rec;

				$users = $this->filteredUsersAgency($key);
				$this->filtered_user_array = array();

				if (isset($users) && !empty($users)) {
					$this->filtered_user_array = $users;
				}
			}

			if(isset($this->filtered_user_array) && !empty($this->filtered_user_array)){

				
				$tmp_arr = array();
				$rec_list[] = $this->AgencytakeNRecommendations($tmp_arr);
				$this->filter_sql = null;
				$this->filtered_user_array = array();

				$ret['status'] = 'success';
				$ret['data'] = $rec_list;	
				continue;
			}


			// Get list of agency users
			// $users = $this->agencyCountryUsers($key);

			
			// if (isset($users) && !empty($users)) {
			// 	$unique_college_rec[] = $users;
			// }
			// if (isset($unique_college_rec) && !empty($unique_college_rec)) {

			// 	$arr = $this->AgencytakeNRecommendations($unique_college_rec);
				
			// 	if (isset($arr) && !empty($arr)) {
			// 		$rec_list[] = $arr;
			// 	}
			// }
			if (isset($rec_list)) {
				$ret['status'] = 'success';
				$ret['data'] = $rec_list;
			}else{
				$ret['status'] = 'failed';
				$ret['data'] = NULL;
			}
				
		}
		// Agency Recommendation ends here

		return $ret;
	
	}


	/**
	* This method recycles the old recommendations up to 100 users for the last 30 days and beyond.
	*/
	public function recycleOldRecs(){
		$tempDate = date("Y-m-d");
		
		$day_of_week = date('l', strtotime( $tempDate));

		if($day_of_week =="Saturday" || $day_of_week == "Sunday"){
			return null;
		}

		$rec_list = array();

		$now = Carbon::now();

		$thirty_days_ago = Carbon::today()->subDays(30);

		$limit = 100;

		$crf = new CollegeRecommendationFilters;

		$filter_no_match = array();

		if (Cache::has(env('ENVIRONMENT').'_recycleOldRecs')) {
			$filtered_recycled_user = Cache::get(env('ENVIRONMENT').'_recycleOldRecs');
		}else{
			$filtered_recycled_user = 0;
		}

		$in_our_network = College::where('in_our_network', 1)
								 ->leftjoin('college_recommendation_filters as crf', 'crf.college_id', '=', 'colleges.id')
					 			 ->join(DB::raw('(SELECT op.id AS op_id FROM organization_portals AS op WHERE active = 1) AS t2'),function($join)
	 				 			 {
					 		    	// $join->on(DB::raw('t2.op_id = crf.org_portal_id'), DB::raw('OR'), DB::raw('crf.org_portal_id IS NULL'));
					 		    	$join->orWhere('t2.op_id', '=', 'crf.org_portal_id')
					 		    		 ->orWhereNull('crf.org_portal_id');
					 			 })
								 ->join('priority', 'priority.college_id', '=', 'colleges.id')
					 		 	 ->select('colleges.id', 'crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id')
								 ->groupby('crf.college_id','crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id');
		
		if ($filtered_recycled_user != 0) {
			$in_our_network = $in_our_network->skip($filtered_recycled_user)
											 ->limit(10000);
		}

		$in_our_network = $in_our_network->orderBy(DB::raw('ISNULL(priority.id)'))
										 ->orderBy('priority.contract', 'DESC')
										 ->orderBy('priority.type', 'ASC')
										 ->get();


		foreach ($in_our_network as $key) {

			// Reset the users supression list for a college
			$this->college_suppression_list = array();

			//Get the supressions list of users for a college
			// $this->setUsersIdsForCollege($key->id);
			$college_id = $key->id;

			// Array of recommended students for a single college
			$unique_college_rec = array();

			//set org branch id


			if(isset($key->aor_id)){
				$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id, $key->aor_id);
			}
			else{
				$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id);
			}
			//Get Recommendation filter

			$arr = array();
			if (isset($this->org_branch_id)) {
				$arr['org_school_id'] = $key->id;

				$arr['org_branch_id'] = $this->org_branch_id;
				if (isset($key->org_portal_id) && !empty($key->org_portal_id)) {
					$arr['default_organization_portal'] = (object) array();
					$arr['default_organization_portal']->id = $key->org_portal_id; 
				}

				if (isset($key->aor_id)) {
					$arr['aor_id'] = $key->aor_id;
				}

				if (isset($key->aor_portal_id)) {
					$arr['aor_portal_id'] = $key->aor_portal_id;
				}

				$qry = $crf->generateFilterQry($arr);

				if (isset($qry)) {				
					$qry = $qry->groupby('userFilter.id')->select('r.user_id', 'r.id as rec_id')
							   ->where('userFilter.recommendation_tier_id', '!=', 0)
							   ->where('userFilter.financial_firstyr_affordibility', '!=', DB::raw("'0.00'"))
							   ->orderBy(DB::raw('userFilter.recommendation_tier_id ASC, rp.probability'), 'DESC')
							   //->orderby('userFilter.recommendation_tier_id', 'rp.probability')
							   ->leftjoin('recommendation_probability as rp', function($query) use($college_id){
							   		$query = $query->on('rp.user_id', '=', 'userFilter.id');
							   		$query = $query->where('rp.college_id', '=', $college_id);
							   });
					$this_qry = $qry;
					//$this->filter_sql = $this->getRawSqlWithBindings($qry);
				}
			}

			if (isset($this_qry) && !empty($this_qry)) {
				
				$this_qry = $this_qry->join('recruitment as r', function($q) use($arr, $thirty_days_ago){
								   $q->on('r.user_id', '=', 'userFilter.id');
								   if (isset($arr['org_school_id'])) {
								   		$q->on('r.college_id', '=', DB::raw($arr['org_school_id']));
								   }
								   $q->on('r.user_recruit', '=', DB::raw(0))
								   	 ->on('r.college_recruit', '=', DB::raw(1))
								   	 ->on('r.status', '=', DB::raw(1))
								   	 ->on('r.created_at', '<', DB::raw("'".$thirty_days_ago."'"));
								   if (isset($arr['aor_id'])) {
								   		$q->on('r.aor_id', '=', DB::raw($arr['aor_id']));
								   }

				});

				$this_qry = $this_qry->limit($limit)->get();
				
				$user_id_arr = array();
				$rec_id_arr  = array();

				foreach ($this_qry as $key) {
					$user_id_arr[] = $key->user_id;
					$rec_id_arr[]  = $key->rec_id;
				}

				$this->removeStudentsFromRecommendations($rec_id_arr);
				$this->addRecylcedUsersToRecommendations($user_id_arr, $arr);
			}

			$this_qry = NULL;
			$qry      = NULL;
			$filtered_recycled_user++;
			Cache::put(env('ENVIRONMENT').'_recycleOldRecs', $filtered_recycled_user, 120);
		}
	}

	public function findCollegesForThisUser($user_id, $add_priority_table = NULL, $supression_colleges = NULL, $offset = NULL){

		$rec_list = array();

		$now = Carbon::now();

		$thirty_days_ago = Carbon::today()->subDays(30);

		$limit = 100;

		$crf = new CollegeRecommendationFilters;

		$filter_no_match = array();

		$in_our_network = College::where('in_our_network', 1)
								 ->leftjoin('college_recommendation_filters as crf', 'crf.college_id', '=', 'colleges.id')
					 			 ->join(DB::raw('(SELECT op.id AS op_id FROM organization_portals AS op WHERE active = 1) AS t2'),function($join)
	 				 			 {
					 		    	// $join->on(DB::raw('t2.op_id = crf.org_portal_id'), DB::raw('OR'), DB::raw('crf.org_portal_id IS NULL'));
					 		    	$join->orWhere('t2.op_id', '=', 'crf.org_portal_id')
					 		    		 ->orWhereNull('crf.org_portal_id');
					 			 })
								->leftjoin('priority as p', 'p.college_id', '=', 'colleges.id')
								->leftjoin('client_types as ct', 'ct.id', '=', 'p.type')
								->leftjoin('contract_types as cs', 'cs.id', '=', 'p.contract')
								->leftjoin('organization_portals as op', 'op.id', '=', 'crf.org_portal_id')
								->leftjoin('aor_portals as ap', 'ap.id', '=', 'crf.aor_portal_id')
								->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'colleges.id')
					 		 	->select(  'crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id',
					 		 		       'colleges.school_name', 'colleges.id', 'colleges.logo_url', 'colleges.slug',
					 		 		       'cs.name as contract_type',
					 		 		       'ct.name as client_type',
					 		 		       'op.name as org_portal_name',
					 		 		       'ap.name as aor_portal_name',
					 		 		       'cr.plexuss as rank')
								->groupby('crf.college_id','crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id');

		if (isset($add_priority_table)) {
			$in_our_network = $in_our_network->whereNotNull('p.financial_filter');

		}
		if (isset($supression_colleges)) {
			$in_our_network = $in_our_network->whereNotIn('colleges.id', $supression_colleges);
		}

		if (isset($offset)) {
			$in_our_network = $in_our_network->skip($offset)
											 ->take(10);
		}

		$in_our_network = $in_our_network->orderBy(DB::raw('ISNULL(p.id)'))
										 ->orderBy('p.contract', 'DESC')
										 ->orderBy('p.type', 'ASC')
										 ->get();

		$ret = array();

		if (!isset($add_priority_table)) {
			$tmp = array();
			$tmp['school_name']     = 'Plexuss';
			$tmp['contract_type']   = NULL;
			$tmp['client_type']     = NULL;
			$tmp['org_portal_name'] = NULL;
			$tmp['aor_portal_name'] = NULL;
			$tmp['college_id']      = 7916;
			$tmp['aor_id']          = NULL;
			$tmp['org_portal_id']   = NULL;
			$tmp['aor_portal_id']   = NULL;
			$tmp['rank']			= NULL;

			$ret[] = $tmp;

			$tmp = array();
			$tmp['school_name']     = 'Plexuss';
			$tmp['contract_type']   = NULL;
			$tmp['client_type']     = NULL;
			$tmp['org_portal_name'] = 'Coveted Users';
			$tmp['aor_portal_name'] = NULL;
			$tmp['college_id']      = 7916;
			$tmp['aor_id']          = NULL;
			$tmp['org_portal_id']   = 218;
			$tmp['aor_portal_id']   = NULL;
			$tmp['rank']			= NULL;

			$ret[] = $tmp;
		}
		
		foreach ($in_our_network as $key) {

			// Reset the users supression list for a college
			$this->college_suppression_list = array();

			//Get the supressions list of users for a college
			// $this->setUsersIdsForCollege($key->id);
			$college_id = $key->id;

			// Array of recommended students for a single college
			$unique_college_rec = array();

			//set org branch id


			if(isset($key->aor_id)){
				$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id, $key->aor_id);
			}
			else{
				$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id);
			}
			//Get Recommendation filter

			$arr = array();
			if (isset($this->org_branch_id)) {
				$arr['org_school_id'] = $key->id;

				$arr['org_branch_id'] = $this->org_branch_id;
				if (isset($key->org_portal_id) && !empty($key->org_portal_id)) {
					$arr['default_organization_portal'] = (object) array();
					$arr['default_organization_portal']->id = $key->org_portal_id; 
				}

				if (isset($key->aor_id)) {
					$arr['aor_id'] = $key->aor_id;
				}

				if (isset($key->aor_portal_id)) {
					$arr['aor_portal_id'] = $key->aor_portal_id;
				}

				$qry = $crf->generateFilterQry($arr);

				if (isset($qry)) {				
					$qry = $qry->groupby('userFilter.id')->select('r.user_id', 'r.id as rec_id')
							   ->where('userFilter.recommendation_tier_id', '!=', 0)
							   ->where('userFilter.financial_firstyr_affordibility', '!=', DB::raw("'0.00'"));
							   //->orderBy(DB::raw('userFilter.recommendation_tier_id ASC, rp.probability'), 'DESC');
							   //->orderby('userFilter.recommendation_tier_id', 'rp.probability')
							   // ->leftjoin('recommendation_probability as rp', function($query) use($college_id){
							   // 		$query = $query->on('rp.user_id', '=', 'userFilter.id');
							   // 		$query = $query->where('rp.college_id', '=', $college_id);
							   // });
					$this_qry = $qry;
					//$this->filter_sql = $this->getRawSqlWithBindings($qry);
				}
			}

			if (isset($this_qry) && !empty($this_qry)) {
				
				$this_qry = $this_qry->leftjoin('recruitment as r', function($q) use($arr, $user_id){
								   $q->on('r.user_id', '=', 'userFilter.id');
								   if (isset($arr['org_school_id'])) {
								   		$q->on('r.college_id', '=', DB::raw($arr['org_school_id']));
								   }
								   if (isset($arr['aor_id'])) {
								   		$q->on('r.aor_id', '=', DB::raw($arr['aor_id']));
								   }else{
								   	    $q->whereNull('r.aor_id');
								   }

								   if (isset($user_id)) {
								   		$q->on('r.user_id', '=', DB::raw($user_id));
								   }

				});

				$this_qry = $this_qry->leftjoin('prescreened_users as pr', function($q) use($key, $user_id){
								   $q->on('pr.user_id', '=', 'userFilter.id');
								   $q->on('pr.active', '=', DB::raw(1));

								   if (isset($key->id)) {
								   		$q->on('pr.college_id', '=', DB::raw($key->id));
								   }
								   if (isset($key->aor_id)) {
								   		$q->on('pr.aor_id', '=', DB::raw($key->aor_id));
								   }else{
								   		$q->whereNull('pr.aor_id');
								   }

								   if (isset($user_id)) {
								   		$q->on('pr.user_id', '=', DB::raw($user_id));
								   }

								   if (isset($key->org_portal_id)) {
								   		$q->on('pr.org_portal_id', '=', DB::raw($key->org_portal_id));
								   }else{
								   		$q->whereNull('pr.org_portal_id');
								   }

								   if (isset($key->aor_portal_id)) {
								   		$q->on('pr.aor_portal_id', '=', DB::raw($key->aor_portal_id));
								   }else{
								   		$q->whereNull('pr.aor_portal_id');
								   }

				});

				$this_qry = $this_qry->where('userFilter.id', $user_id)
									 ->whereNull('r.id')
									 ->whereNull('pr.id')
								     ->first();
			}
			if (isset($this_qry)) {
				$tmp = array();
				$tmp['school_name']     = $key->school_name;
				$tmp['contract_type']   = $key->contract_type;
				$tmp['client_type']     = $key->client_type;
				$tmp['org_portal_name'] = $key->org_portal_name;
				$tmp['aor_portal_name'] = $key->aor_portal_name;
				$tmp['college_id']      = $key->id;
				$tmp['aor_id']          = $key->aor_id;
				$tmp['org_portal_id']   = $key->org_portal_id;
				$tmp['aor_portal_id']   = $key->aor_portal_id;
				$tmp['logo_url']		= $key->logo_url;
				$tmp['slug']			= $key->slug;
				$tmp['rank']			= $key->rank;

				$ret[] = $tmp;
			}

			$this_qry = NULL;
			$qry      = NULL;
		}
		

		return $ret;
	}

	public function findCollegesForThisUserOnGetStarted($user_id, $aor_id = NULL, $college_id = NULL, $cap = NULL){

		//$user_id = 722069;

		if (!isset($aor_id) && !isset($college_id) && Cache::has(env('ENVIRONMENT').'_'.'school_matches_'.$user_id)){
			$ret = Cache::get(env('ENVIRONMENT').'_'.'school_matches_'.$user_id);
			return $ret;
		}

		$rec_list = array();

		$now = Carbon::now();

		$thirty_days_ago = Carbon::today()->subDays(30);

		$limit = 100;

		$crf = new CollegeRecommendationFilters;

		$filter_no_match = array();

		if (isset($aor_id)) {
			$in_our_network = DB::connection('rds1')->table('aor as a')
													->join('aor_colleges as ac', function($q) use($aor_id, $college_id){
														   $q->on('ac.aor_id', '=', 'a.id')
														   	 ->on('ac.active', '=', DB::raw(1))
														   	 ->on('ac.aor_id', '=', DB::raw($aor_id));
													})
													->join('colleges as c', 'c.id', '=', 'ac.college_id')
													->leftjoin('college_recommendation_filters as crf', 'crf.college_id', '=', 'c.id')
													->whereNotNull('a.id')
											  		->where('a.id', $aor_id)
											  		->select(  'crf.org_portal_id', 'a.id as aor_id', 'crf.aor_portal_id',
										 		 		       'c.school_name', 'c.id')
													->groupby('c.id');
		}else{
			$in_our_network = College::on('rds1')->where('colleges.in_our_network', 1)
								 ->leftjoin('college_recommendation_filters as crf', 'crf.college_id', '=', 'colleges.id')
					 			
					 		 	 ->select(  'crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id',
					 		 		       'colleges.school_name', 'colleges.id')
								 ->groupby('crf.college_id','crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id');
		}
		
		// if (isset($college_id)) {
		// 	$in_our_network = $in_our_network->where('c.id', $college_id);
		// }

		// Exception for higher ed to make it faster
		$do_not_check = NULL;
		if (isset($aor_id) && $aor_id == 33) {
			//	WE HAVE THIS BECAUSE ZIP CODE CHECK UP IS VERY SLOW. 
			$do_not_check = array();
			$do_not_check['zipcode'] = true;

			ini_set('max_execution_time', 300);
			$this_user = User::on('rds1')->find($user_id);

			$in_our_network = $in_our_network->leftjoin('college_recommendation_filter_logs as crfl', 
														'crf.id', '=', 'crfl.rec_filter_id')
											 ->where('crf.category', '=', DB::raw('"location"'))
											 ->where('crf.name', '=', DB::raw('"zipcode"'))

											 ->where('crfl.val', 'LIKE', DB::raw('"%'.$this_user->zip.'%"'));
											 // ->addSelect('crfl.val as crfl_val');
		}

		$in_our_network = $in_our_network->get();
		$ret = array();

		foreach ($in_our_network as $key) {

			if (isset($cap)) {
				if (count($ret) == $cap) {
					break;
				}
			}
			
			// Reset the users supression list for a college
			$this->college_suppression_list = array();

			//Get the supressions list of users for a college
			// $this->setUsersIdsForCollege($key->id);
			$college_id = $key->id;

			// Array of recommended students for a single college
			$unique_college_rec = array();

			//set org branch id

			// if(isset($key->aor_id)){
			// 	$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id, $key->aor_id);
			// }
			// else{
			// 	$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id);
			// }

			//Get Recommendation filter

			$arr = array();
			
			$arr['org_school_id'] = $key->id;
			$arr['org_branch_id'] = NULL;

			// if (isset($key->org_portal_id) && !empty($key->org_portal_id)) {
			// 	$arr['default_organization_portal'] = (object) array();
			// 	$arr['default_organization_portal']->id = $key->org_portal_id; 
			// }

			if (isset($key->aor_id)) {
				$arr['aor_id'] = $key->aor_id;
			}

			// if (isset($key->aor_portal_id)) {
			// 	$arr['aor_portal_id'] = $key->aor_portal_id;
			// }
			$qry = $crf->generateFilterQry($arr, $do_not_check);

			if (isset($qry)) {				
				$qry = $qry->groupby('userFilter.id')->select('userFilter.id');
						   // ->where('userFilter.recommendation_tier_id', '!=', 0)
						   // ->where('userFilter.financial_firstyr_affordibility', '!=', DB::raw("'0.00'"));
						   //->orderBy(DB::raw('userFilter.recommendation_tier_id ASC, rp.probability'), 'DESC');
						   //->orderby('userFilter.recommendation_tier_id', 'rp.probability')
						   // ->leftjoin('recommendation_probability as rp', function($query) use($college_id){
						   // 		$query = $query->on('rp.user_id', '=', 'userFilter.id');
						   // 		$query = $query->where('rp.college_id', '=', $college_id);
						   // });
				$this_qry = $qry;
				//$this->filter_sql = $this->getRawSqlWithBindings($qry);
			}else{
				$ret[] = $college_id;
				continue;
			}


			if (isset($this_qry) && !empty($this_qry)) {
				$this_qry = $this_qry->where('userFilter.id', $user_id);
				$this_qry = $this_qry->first();
			}

			if (isset($this_qry)) {
				$ret[] = $college_id;
			}

			$this_qry = NULL;
			$qry      = NULL;
		}

		if (!isset($aor_id) && !isset($college_id)){
			Cache::put(env('ENVIRONMENT').'_'.'school_matches_'.$user_id,$ret,460);
		}

		return $ret;
	}

	public function findPortalsForThisUserAtThisCollege($user_id, $college_id){

		$rec_list = array();

		$now = Carbon::now();

		$thirty_days_ago = Carbon::today()->subDays(30);

		$limit = 100;

		$crf = new CollegeRecommendationFilters;

		$filter_no_match = array();

		$in_our_network = College::on('rds1')->leftjoin('college_recommendation_filters as crf', 'crf.college_id', '=', 'colleges.id')
								 			 ->join(DB::raw('(SELECT op.id AS op_id FROM organization_portals AS op) AS t2'),function($join)
				 				 			 {
								 		    	// $join->on(DB::raw('t2.op_id = crf.org_portal_id'), 'OR', DB::raw('crf.org_portal_id IS NULL'));
								 		    	$join->orWhere('t2.op_id', '=', 'crf.org_portal_id')
								 		    		 ->orWhereNull('crf.org_portal_id');
								 			 })
											->leftjoin('organization_portals as op', 'op.id', '=', 'crf.org_portal_id')
											->leftjoin('aor_portals as ap', 'ap.id', '=', 'crf.aor_portal_id')
								 		 	->select(  'crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id',
								 		 		       'colleges.school_name', 'colleges.id',
								 		 		       'op.name as org_portal_name',
								 		 		       'ap.name as aor_portal_name')
											->where('colleges.id', $college_id)
											->where(function($q){
												$q->where('crf.org_portal_id', '!=', DB::raw('""'))
												  ->orWhere('crf.aor_portal_id', '!=', DB::raw('""'));
											})
											->groupby('crf.college_id','crf.org_portal_id', 'crf.aor_id', 'crf.aor_portal_id');

		$in_our_network = $in_our_network->get();

		$ret = array();
		foreach ($in_our_network as $key) {

			// Reset the users supression list for a college
			$this->college_suppression_list = array();

			//Get the supressions list of users for a college
			// $this->setUsersIdsForCollege($key->id);
			$college_id = $key->id;

			// Array of recommended students for a single college
			$unique_college_rec = array();

			//set org branch id


			if(isset($key->aor_id)){
				$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id, $key->aor_id);
			}
			else{
				$this->org_branch_id = $this->getOrgBranchId($key->id, $key->org_portal_id);
			}
			//Get Recommendation filter

			$arr = array();
			if (isset($this->org_branch_id)) {
				$arr['org_school_id'] = $key->id;

				$arr['org_branch_id'] = $this->org_branch_id;
				if (isset($key->org_portal_id) && !empty($key->org_portal_id)) {
					$arr['default_organization_portal'] = (object) array();
					$arr['default_organization_portal']->id = $key->org_portal_id; 
				}

				if (isset($key->aor_id)) {
					$arr['aor_id'] = $key->aor_id;
				}

				if (isset($key->aor_portal_id)) {
					$arr['aor_portal_id'] = $key->aor_portal_id;
				}

				$qry = $crf->generateFilterQry($arr);

				if (isset($qry)) {				
					$qry = $qry->groupby('userFilter.id')->select('userFilter.id');
							   //->orderBy(DB::raw('userFilter.recommendation_tier_id ASC, rp.probability'), 'DESC');
							   //->orderby('userFilter.recommendation_tier_id', 'rp.probability')
							   // ->leftjoin('recommendation_probability as rp', function($query) use($college_id){
							   // 		$query = $query->on('rp.user_id', '=', 'userFilter.id');
							   // 		$query = $query->where('rp.college_id', '=', $college_id);
							   // });
					$this_qry = $qry;
					//$this->filter_sql = $this->getRawSqlWithBindings($qry);
				}
			}

			if (isset($this_qry) && !empty($this_qry)) {
				$this_qry = $this_qry->where('userFilter.id', $user_id);
				$this_qry = $this_qry->first();
			}

			if (isset($this_qry)) {
				$tmp = array();
				$tmp['school_name']     = $key->school_name;
				$tmp['org_portal_name'] = $key->org_portal_name;
				$tmp['aor_portal_name'] = $key->aor_portal_name;
				$tmp['college_id']      = $key->id;
				$tmp['aor_id']          = $key->aor_id;
				$tmp['org_portal_id']   = $key->org_portal_id;
				$tmp['aor_portal_id']   = $key->aor_portal_id;

				$ret[] = $tmp;
			}

			$this_qry = NULL;
			$qry      = NULL;
		}

		return $ret;
	}

	/**
	 * This method returns the users who viewed this college on the college page
	 *
	 * @param college           This is the college object, which contains colleges table info
	 *
	 * @return array or null
	 */
	private function usersViewedCollege($college = null){

		if ($college == null) {
			return null;
		}

		$supression_users = $this->getSuppresionUsers('user_viewed_college', $college->id);


		$users = TrackingPage::on('bk-log')
					->join('plexuss.users as u', 'u.id', '=', 'tracking_pages.user_id')
					->where('tracking_pages.slug', $college->slug)
					->where(function ($query) {
					    $query->orwhere('u.is_student', 1)
					          ->orwhere('u.is_intl_student', 1);
					})
					->where('u.is_alumni', 0)
					->where('u.is_parent', 0)
					->where('u.is_counselor', 0)
					->where('u.is_organization', 0)
					->where('u.is_agency', 0)
					->where('u.is_university_rep', 0)
					->select('u.id as id')
					->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
					->groupBy('u.id');

		// if (isset($this->filter_sql)) {

		// 	$users = $users->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'tracking_pages.user_id');
		// 	$users = $users->join('plexuss.users as u', 'u.id', '=', 't1.id');
		// }else{
		// 	$users = $users->join('plexuss.users as u', 'u.id', '=', 'tracking_pages.user_id');
		// }

		if(isset($supression_users)){
			$users = $users->whereNotIn('u.id', $supression_users);
		}
		$users = $users->take(self::NUM_OF_RECOMMENDATIONS)->get();

		if (isset($users)) {
			$ret = array();
			foreach ($users as $key) {
				$tmp = array();
				$tmp['user_id'] = $key->id;
				$tmp['college_id'] = $college->id;
				$tmp['reason'] = 'user_viewed_college';
				$tmp['type'] = 'not_filtered';

				$this->college_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}

		return null;
	}

	/**
	 * This method returns the users who liked this college on the college page
	 *
	 * @param college_id           The college_id of the college in our network
	 *
	 * @return array or null
	 */
	private function usersLikedCollege($college_id = null){
		
		if ($college_id == null) {
			return null;
		}

		$supression_users = $this->getSuppresionUsers('liked_college', $college_id);

		$likes_tally = LikesTally::on('bk')
									->join('users as u', 'u.id', '=','user_id')
									->where('type', 'college')
									->where('type_col', 'id')
									->where('type_val', $college_id)
									->where(function ($query) {
									    $query->orwhere('u.is_student', 1)
									          ->orwhere('u.is_intl_student', 1);
									})
									->where('u.is_alumni', 0)
									->where('u.is_parent', 0)
									->where('u.is_counselor', 0)
									->where('u.is_organization', 0)
									->where('u.is_agency', 0)
									->where('u.is_university_rep', 0)
									->select('u.id as user_id')
									->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
									->groupBy('user_id');

		// if (isset($this->filter_sql)) {

		// 	$likes_tally = $likes_tally->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'user_id');
		// 	$likes_tally = $likes_tally->join('users as u', 'u.id', '=','t1.id');
		// }else{
		// 	$likes_tally = $likes_tally->join('users as u', 'u.id', '=','user_id');
		// }

		if(isset($supression_users)){
			$likes_tally = $likes_tally->whereNotIn('user_id', $supression_users);
		}
		$likes_tally = $likes_tally->take(self::NUM_OF_RECOMMENDATIONS)->get();

		if (isset($likes_tally)) {
			$ret = array();
			foreach ($likes_tally as $key) {
				$tmp = array();
				$tmp['user_id'] = $key->user_id;
				$tmp['college_id'] = $college_id;
				$tmp['reason'] = 'liked_college';
				$tmp['type'] = 'not_filtered';

				$this->college_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}


		return null;
	}

	/**
	 * This method returns the users who compared this college against others
	 *
	 * @param college           This is the college object, which contains colleges table info
	 *
	 * @return array or null
	 */
	private function usersComparedCollege($college = null){

		if ($college == null) {
			return null;
		}

		$supression_users = $this->getSuppresionUsers('user_compared_college', $college->id);


		$users = TrackingPage::on('bk-log')
					->join('plexuss.users as u', 'u.id', '=', 'tracking_pages.user_id')
					->where(function ($query) {
					    $query->where('tracking_pages.url', '=', 'http://plexuss.dev/comparison')
					          ->orwhere('tracking_pages.url', '=', 'https://plexuss.com/comparison')
					          ->orwhere('tracking_pages.url', '=', 'https://dev.plexuss.com/comparison')
					          ->orwhere('tracking_pages.url', '=', 'http://qa.plexuss.com/comparison');
					})
					->where('tracking_pages.params', 'LIKE', '%'.$college->slug.'%')
					->where(function ($query) {
					    $query->orwhere('u.is_student', 1)
					          ->orwhere('u.is_intl_student', 1);
					})
					->where('u.is_alumni', 0)
					->where('u.is_parent', 0)
					->where('u.is_counselor', 0)
					->where('u.is_organization', 0)
					->where('u.is_agency', 0)
					->where('u.is_university_rep', 0)
					->select('u.id as id')
					->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
					->groupBy('u.id');

		// if (isset($this->filter_sql)) {

		// 	$users = $users->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'tracking_pages.user_id');
		// 	$users = $users->join('plexuss.users as u', 'u.id', '=', 't1.id');
		// }else{
		// 	$users = $users->join('plexuss.users as u', 'u.id', '=', 'tracking_pages.user_id');
		// }

		if(isset($supression_users)){
			$users = $users->whereNotIn('u.id', $supression_users);
		}
		$users = $users->take(self::NUM_OF_RECOMMENDATIONS)->get();

		if (isset($users)) {
			$ret = array();
			foreach ($users as $key) {
				$tmp = array();
				$tmp['user_id'] = $key->id;
				$tmp['college_id'] = $college->id;
				$tmp['reason'] = 'user_compared_college';
				$tmp['type'] = 'not_filtered';

				$this->college_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}

		return null;
	}

	/**
	 * This method returns the users who have been recommended this college through our "Get Recuited" process 
	 *
	 * @param college_id           The college_id of the college in our network
	 *
	 * @return array or null
	 */
	private function usersRecommendedCollege($college_id = null){

		if ($college_id == null) {
			return null;
		}

		$supression_users = $this->getSuppresionUsers('recommended_college', $college_id);

		$portal_notification = PortalNotification::on('bk')
													->join('users as u', 'u.id', '=','user_id')
													->where('school_id', $college_id)
													->where('is_recommend', 1)
													->where('is_recommend_trash', 0)
													->where(function ($query) {
													    $query->orwhere('u.is_student', 1)
													          ->orwhere('u.is_intl_student', 1);
													})
													->where('u.is_alumni', 0)
													->where('u.is_parent', 0)
													->where('u.is_counselor', 0)
													->where('u.is_organization', 0)
													->where('u.is_agency', 0)
													->where('u.is_university_rep', 0)
													->select('u.id as user_id')

													->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
													->groupBy('user_id');

		// if (isset($this->filter_sql)) {

		// 	$portal_notification = $portal_notification->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'user_id');
		// 	$portal_notification = $portal_notification->join('plexuss.users as u', 'u.id', '=', 't1.id');
		// }else{
		// 	$portal_notification = $portal_notification->join('users as u', 'u.id', '=','user_id');
		// }

		if(isset($supression_users)){
			$portal_notification = $portal_notification->whereNotIn('user_id', $supression_users);
		}
		$portal_notification = $portal_notification->take(self::NUM_OF_RECOMMENDATIONS)->get();


		if (isset($portal_notification)) {
			$ret = array();
			foreach ($portal_notification as $key) {
				$tmp = array();
				$tmp['user_id'] = $key->user_id;
				$tmp['college_id'] = $college_id;
				$tmp['reason'] = 'recommended_college';
				$tmp['type'] = 'not_filtered';

				$this->college_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}

		return null;													
	}

	/**
	 * This method returns the users who are within 50 miles radius of this college
	 *
	 * @param college           This is the college object, which contains colleges table info
	 *
	 * @return array or null
	 */
	private function usersAroundTheCollege($college = null){

		if ($college == null) {
			return null;
		}
		$college_id = $college->id;

		$supression_users = $this->getSuppresionUsers('near_college_user', $college_id);

		// We need to read from Master since the backup is not updated in real time.

		$query = 'u.id, ( 3959 * acos( cos( radians('.$college->latitude.') ) * cos( radians( u.latitude ) ) * cos( radians( u.longitude ) - radians('.$college->longitude.') ) + sin( radians('.$college->latitude.') ) * sin(radians(u.latitude)) ) ) AS distance ';	
		$users = DB::table('users as u')
			->select( DB::raw($query))
			->orderby('distance')
			->having('distance', '<=', 50)
			->where(function ($query) {
			    $query->orwhere('u.is_student', 1)
			          ->orwhere('u.is_intl_student', 1);
			})
			->where('u.is_alumni', 0)
			->where('u.is_parent', 0)
			->where('u.is_counselor', 0)
			->where('u.is_organization', 0)
			->where('u.is_agency', 0)
			->where('u.is_university_rep', 0)
			->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
			->groupBy('u.id');
		
		// if (isset($this->filter_sql)) {
		// 	$users = $users->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'u.id');
		// }

		if(isset($supression_users)){
			$users = $users->whereNotIn('u.id', $supression_users);
		}

		$users = $users->take(self::NUM_OF_RECOMMENDATIONS)->get();


		if (isset($users)) {
			$ret = array();
			foreach ($users as $key) {

				$tmp = array();
				$tmp['user_id'] = $key->id;
				$tmp['college_id'] = $college_id;
				$tmp['reason'] = 'near_college_user';
				$tmp['type'] = 'not_filtered';

				$this->college_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}

		return null;
	}
	
	/**
	 * This method returns the user who clicked on get recruited on the similar tier colleges
	 *
	 * @param college_id           The college_id of the college in our network
	 *
	 * @return array or null
	 */
	private function similarTierColleges($college_id = null){
		if ($college_id == null) {
			return null;
		}

		$cr = CollegeRanking::where('college_id', $college_id)
								->select('plexuss')
								->first();

		//print_r('college_id: '.$college_id.'<br>');
		//print_r('Ranking: '.$cr->plexuss.'<br>');
		if (isset($cr->plexuss) && !empty($cr->plexuss)) {

			//dd('here');
			$supression_users = $this->getSuppresionUsers('similar_tier_colleges', $college_id);

			$rank = $cr->plexuss;
			$min = 0;
			$max =0;

			if ($rank <=20) {
				$min = 1;
				$max = 20;
			}elseif ($rank > 20 && $rank <= 50) {
				$min = 21;
				$max = 50;
			}elseif ($rank > 50 && $rank <= 100) {
				$min = 51;
				$max = 100;
			}elseif ($rank > 100 && $rank <= 150) {
				$min = 101;
				$max = 150;
			}elseif ($rank > 150 && $rank <= 200) {
				$min = 151;
				$max = 200;
			}elseif ($rank > 200 && $rank <= 250) {
				$min = 201;
				$max = 250;
			}elseif ($rank > 250 && $rank <= 300) {
				$min = 251;
				$max = 300;
			}elseif ($rank > 300 && $rank <= 350) {
				$min = 301;
				$max = 350;
			}elseif ($rank > 350 && $rank <= 400) {
				$min = 351;
				$max = 400;
			}elseif ($rank > 400 && $rank <= 450) {
				$min = 401;
				$max = 450;
			}elseif ($rank > 450 && $rank <= 500) {
				$min = 451;
				$max = 500;
			}elseif ($rank > 500 && $rank <= 550) {
				$min = 501;
				$max = 550;
			}elseif ($rank > 550 && $rank <= 600) {
				$min = 551;
				$max = 600;
			}elseif ($rank > 600 && $rank <= 650) {
				$min = 601;
				$max = 650;
			}elseif ($rank > 650 && $rank <= 700) {
				$min = 651;
				$max = 700;
			}

			$users = DB::connection('bk')->table('users as u')
						->join('recruitment as r', 'u.id', '=', 'r.user_id')
						->join('colleges_ranking as cr', 'cr.college_id', '=', 'r.college_id')
						->having('cr.plexuss', '>=', $min)
						->having('cr.plexuss', '<=',$max)
						->where(function ($query) {
						    $query->orwhere('u.is_student', 1)
						          ->orwhere('u.is_intl_student', 1);
						})
						->where('u.is_alumni', 0)
						->where('u.is_parent', 0)
						->where('u.is_counselor', 0)
						->where('u.is_organization', 0)
						->where('u.is_agency', 0)
						->where('u.is_university_rep', 0)
						->select('u.id as id', 'cr.plexuss as rank')
						->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
						->groupBy('u.id');

			// if (isset($this->filter_sql)) {
			// 	$users = $users->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'u.id');
			// }
							
			if(isset($supression_users)){
				$users = $users->whereNotIn('u.id', $supression_users);
			}
			$users = $users->take(self::NUM_OF_RECOMMENDATIONS)->get();

			if (isset($users)) {
				$ret = array();
				foreach ($users as $key) {
					$tmp = array();
					$tmp['user_id'] = $key->id;
					$tmp['college_id'] = $college_id;
					$tmp['reason'] = 'near_college_user';
					$tmp['type'] = 'not_filtered';

					$this->college_suppression_list[] = $tmp;

					$ret[] = $tmp;
				}
				return $ret;
			}

		}

		return null;
	}

	
	/**
	 * This method returns the users randomly
	 *
	 * @param college           This is the college object, which contains colleges table info
	 *
	 * @return array or null
	 */
	private function randomUsers($college = null){

		if ($college == null) {
			return null;
		}
		$college_id = $college->id;

		$supression_users = $this->getSuppresionUsers('random_user', $college_id);

		// We need to read from Master since the backup is not updated in real time.

		$users = DB::connection('bk')->table('users as u')
			->where('u.is_alumni', 0)
			->where('u.is_parent', 0)
			->where('u.is_counselor', 0)
			->where('u.is_organization', 0)
			->where('u.is_agency', 0)
			->where('u.is_university_rep', 0)
			->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
			//->orderByRaw("RAND()")
			->groupBy('u.id');

		// if (isset($this->filter_sql)) {
		// 	$users = $users->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'u.id');
		// }

		if(isset($supression_users)){
			$users = $users->whereNotIn('u.id', $supression_users);
		}
		$users = $users->take(self::NUM_OF_RECOMMENDATIONS)->get();


		if (isset($users)) {
			$ret = array();
			foreach ($users as $key) {

				$tmp = array();
				$tmp['user_id'] = $key->id;
				$tmp['college_id'] = $college_id;
				$tmp['reason'] = 'random_user';
				$tmp['type'] = 'not_filtered';

				$this->college_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}

		return null;
	}

	/**
	 * This method returns the users randomly
	 *
	 * @param agency           This is the agency object, which contains agencys table info
	 *
	 * @return array or null
	 */
	private function agencyCountryUsers($agency = null){

		if ($agency == null) {
			return null;
		}
		$agency_id = $agency->id;

		$supression_users = $this->getSuppresionUsersAgency('agency_gen_user', $agency_id);

		// We need to read from Master since the backup is not updated in real time.

		$users = DB::connection('bk')->table('users as u')
			->join('countries as c', 'c.id', '=', 'u.country_id')
			->where('u.is_alumni', 0)
			->where('u.is_parent', 0)
			->where('u.is_counselor', 0)
			->where('u.is_organization', 0)
			->where('u.is_agency', 0)
			->where('u.is_university_rep', 0)
			->where('u.is_plexuss', 0)
			->where('c.country_name',$agency->country )
			->select('u.*')
			->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
			//->orderByRaw("RAND()")
			->groupBy('u.id');

		// if (isset($this->filter_sql)) {
		// 	$users = $users->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'u.id');
		// }

		if(isset($supression_users)){
			$users = $users->whereNotIn('u.id', $supression_users);
		}
		$users = $users->take(self::NUM_OF_RECOMMENDATIONS)->get();


		if (isset($users)) {
			$ret = array();
			foreach ($users as $key) {

				$tmp = array();
				$tmp['user_id'] = $key->id;
				$tmp['agency_id'] = $agency_id;
				$tmp['reason'] = 'agency_gen_user';
				$tmp['type'] = 'not_filtered';

				$this->agency_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}

		return null;
	}

	/**
	 * This method returns the users filtered users
	 *
	 * @param college           This is the college object, which contains colleges table info
	 *
	 * @return array or null
	 */
	private function filteredUsers($college = null, $org_portal_id = null, $aor_id = NULL){

		if ($college == null) {
			return null;
		}
		$college_id = $college->id;
		//$supression_users = $this->getSuppresionUsers('filtered_user', $college_id);

		// We need to read from Master since the backup is not updated in real time.

		// $this->filter_sql = str_replace("`userFilter`.*", "`userFilter`.`id`", $this->filter_sql);

		// $users = DB::connection('bk')->table('users as u')
		// 	->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'u.id')
		// 	->where('u.is_alumni', 0)
		// 	->where('u.is_parent', 0)
		// 	->where('u.is_counselor', 0)
		// 	->where('u.is_organization', 0)
		// 	->where('u.is_agency', 0)
		// 	->where('u.is_university_rep', 0)
		// 	->where('u.is_plexuss', 0)

		// 	->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
		// 	//->orderByRaw("RAND()")
		// 	->groupBy('u.id');

		$users = NULL;

		if ($this->num_of_filtered_rec != 0) {
			$users = $this->filter_sql;
			$users = $users->where('userFilter.is_alumni', 0)
							->where('userFilter.is_parent', 0)
							->where('userFilter.is_counselor', 0)
							->where('userFilter.is_organization', 0)
							->where('userFilter.is_agency', 0)
							->where('userFilter.is_university_rep', 0)
							->where('userFilter.is_plexuss', 0)

							->orderByRaw('`userFilter`.`profile_percent` IS NULL,`userFilter`.`profile_percent` DESC')
							//->orderByRaw("RAND()")
							->groupBy('userFilter.id');

			$users = $this->addSuppressionQry($users, $college_id, $org_portal_id); 
			// if(isset($supression_users)){
			// 	$users = $users->whereNotIn('u.id', $supression_users);
			// }

			$users = $users->select('userFilter.id as this_user_id')
							->groupBy('this_user_id')
						   	->take($this->num_of_filtered_rec)
						   	->get();
		}

		if (isset($users)) {
			$ret = array();
			foreach ($users as $key) {
				$tmp = array();
				$tmp['user_id'] = $key->this_user_id;
				$tmp['college_id'] = $college_id;
				$tmp['reason'] = 'filtered_user';
				$tmp['type'] = 'filtered';
				$tmp['org_portal_id'] = $org_portal_id;

				if (isset($aor_id)) {
					$tmp['aor_id'] = $aor_id;
				}

				$this->college_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}

		return null;
	}

	/**
	 * This method returns the users filtered users
	 *
	 * @param college           This is the college object, which contains colleges table info
	 *
	 * @return array or null
	 */
	private function filteredUsersAgency($agency = null){

		if ($agency == null) {
			return null;
		}
		$agency_id = $agency->id;

		$supression_users = $this->getSuppresionUsersForAgencies('filtered_user', $agency_id);

		// We need to read from Master since the backup is not updated in real time.

		$this->filter_sql = str_replace("`userFilter`.*", "`userFilter`.`id`", $this->filter_sql);

		$users = DB::connection('bk')->table('users as u')
			->join(DB::raw('('.$this->filter_sql.') as t1'), 't1.id', '=', 'u.id')
			->where('u.is_alumni', 0)
			->where('u.is_parent', 0)
			->where('u.is_counselor', 0)
			->where('u.is_organization', 0)
			->where('u.is_agency', 0)
			//->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
			//->orderByRaw("RAND()")
			->groupBy('u.id');

		$users = $users->leftjoin('users_custom_questions as ucq', 'ucq.user_id', '=', 'u.id')
					   ->where('ucq.application_state', '!=', 'submit')
					   ->orderBy(DB::raw('ISNULL(ucq.application_state_id), ucq.application_state_id'), 'DESC')
					   ->select('u.*', 'ucq.application_state');

		if(isset($supression_users)){
			$users = $users->whereNotIn('u.id', $supression_users);
		}

		$users = $users->take($this->num_of_filtered_rec)->get();


		if (isset($users)) {
			$ret = array();
			foreach ($users as $key) {

				$tmp = array();
				$tmp['user_id'] = $key->id;
				$tmp['agency_id'] = $agency_id;
				$tmp['reason'] = 'filtered_user';
				$tmp['type'] = 'filtered';

				(isset($key->application_state) && $key->application_state == 'submit') ? $tmp['bucket_name'] ='applications' : $tmp['bucket_name'] = 'leads';

				$this->agency_suppression_list[] = $tmp;

				$ret[] = $tmp;
			}
			return $ret;
		}

		return null;
	}
	
	/**
	 * This method sets the suppression list of users who have been recommended for 
	 * this college already
	 *
	 * @param college_id           The college_id of the college in our network
	 *
	 * @return void
	 */
	private function setUsersIdsForCollege($college_id = null){

		if ($college_id == null) {
			return null;
		}

		$this->college_suppression_list = array();

		$cr = CollegeRecommendation::on('bk')
								   ->where('college_id', $college_id)
								   ->select('user_id', 'reason', DB::raw($college_id . " AS `college_id`"))
								   ->get()->toArray();

		if (isset($cr) && !empty($cr)) {
			$this->college_suppression_list = $cr;
		}
	}

	/**
	 * This method gets the suppression list of users who have been recommended for 
	 * this college already or they have clicked on "Get Recruited" already
	 *
	 * @param college_id           The college_id of the college in our network
	 *
	 * @return array
	 */
	private function getAgency($key){

	}

	/**
	 * This method gets the suppression list of users who have been recommended for 
	 * this college already or they have clicked on "Get Recruited" already
	 *
	 * @param college_id           The college_id of the college in our network
	 *
	 * @return array
	 */
	private function getSuppresionUsers($reason = null, $college_id){
		
		$supression_users = array();
		$conflicts = array();

		$cr = CollegeRecommendation::on('bk')
								   ->where('college_id', $college_id)
								   ->select('user_id', 'reason', DB::raw($college_id . " AS `college_id`"))
								   ->get()->toArray();


		if (isset($this->college_suppression_list)) {
			foreach ($this->college_suppression_list as $key) {
				if ($key['college_id'] == $college_id) {
					$supression_users[] = $key['user_id'];
				}
			}
		}

		if (isset($this->country_conflicts) && !empty($this->country_conflicts)) {
			foreach ($this->country_conflicts as $key) {
				$conflicts[] = $key->user_id;
			}
			$supression_users = array_merge($conflicts, $supression_users);
		}

		$recruitment = Recruitment::on('bk')
									->where('college_id', $college_id)
									->select('user_id')
									->where('status', 1)
									->get()->toArray();

		$today_recommended = CollegeRecommendation::where('created_at', '>=', Carbon::today())
				    							    ->where('created_at', '<', Carbon::tomorrow())
				    							    ->distinct()->select('user_id')
				    							    ->get()->toArray();
		
		$supression_users = array_merge($today_recommended, $supression_users);

		$supression_users = array_merge($recruitment, $supression_users);

		$supression_users = array_unique($supression_users, SORT_REGULAR);

		return $supression_users;
	}


	/**
	 * This method gets the suppression list of users who have been recommended for 
	 * this agency.
	 *
	 * @param agency_id           The agency_id of the agency in our network
	 *
	 * @return array
	 */
	private function getSuppresionUsersForAgencies($reason = null, $agency_id){
		
		$supression_users = array();
		$conflicts = array();

		$cr = CollegeRecommendation::on('bk')
								   ->where('agency_id', $agency_id)
								   ->select('user_id', 'reason', DB::raw($agency_id . " AS `agency_id`"))
								   ->get()->toArray();


		if (isset($this->college_suppression_list)) {
			foreach ($this->college_suppression_list as $key) {
				if ($key['agency_id'] == $agency_id) {
					$supression_users[] = $key['user_id'];
				}
			}
		}

		if (isset($this->country_conflicts) && !empty($this->country_conflicts)) {
			foreach ($this->country_conflicts as $key) {
				$conflicts[] = $key->user_id;
			}
			$supression_users = array_merge($conflicts, $supression_users);
		}

		$recruitment = AgencyRecruitment::on('bk')
									->where('agency_id', $agency_id)
									->select('user_id')
									->where('active', 1)
									->get()->toArray();

		$today_recommended = CollegeRecommendation::where('created_at', '>=', Carbon::today())
				    							    ->where('created_at', '<', Carbon::tomorrow())
				    							    ->distinct()->select('user_id')
				    							    ->get()->toArray();
		
		$supression_users = array_merge($today_recommended, $supression_users);

		$supression_users = array_merge($recruitment, $supression_users);

		$supression_users = array_unique($supression_users, SORT_REGULAR);

		return $supression_users;
	}

	public function addSuppressionQry($qry, $college_id, $org_portal_id){
		$cid = $college_id;
	
		$qry = $qry
			->leftjoin('college_recommendations as cr',function($join) use($college_id, $org_portal_id){
				$join->on('cr.college_id','=', DB::raw($college_id));
				if (isset($org_portal_id) && !empty($org_portal_id)) {
					$join->on('cr.org_portal_id','=', DB::raw($org_portal_id));
				}
				$join->on('cr.user_id','=','userFilter.id');
			});

		$qry = $qry->whereNull('cr.id')
				   ->leftjoin('recruitment as rec',function($join1) use($cid){		
						
						$join1->on('rec.college_id','=', DB::raw($cid));
						$join1->on('rec.user_id','=','userFilter.id');
				   })
				   ->whereNull('rec.id')
				   ->leftjoin('college_recommendations as cr2',function($join2){
						$join2->on('cr2.created_at', '>=', DB::raw("'".Carbon::today()."'"));
						$join2->on('cr2.created_at', '<', DB::raw("'".Carbon::tomorrow()."'"));
						$join2->on('userFilter.id','=','cr2.user_id');
					})
				   ->whereNull('cr2.id')
				   ->leftjoin('country_conflicts as cc','userFilter.id','=','cc.user_id')
				   ->whereNull('cc.id');

		return $qry;
	}

	/**
	 * This method gets the suppression list of users who have been recommended for 
	 * this agency already or they have clicked on "Get Recruited" already
	 *
	 * @param agency_id           The agency_id of the agency in our network
	 *
	 * @return array
	 */
	private function getSuppresionUsersAgency($reason = null, $agency_id){
		
		$supression_users = array();
		$conflicts = array();

		if (isset($this->agency_suppression_list)) {
			foreach ($this->agency_suppression_list as $key) {
				if ($key['agency_id'] == $agency_id) {
					$supression_users[] = $key['user_id'];
				}
			}
		}

		if (isset($this->country_conflicts) && !empty($this->country_conflicts)) {
			foreach ($this->country_conflicts as $key) {
				$conflicts[] = $key->user_id;
			}
			$supression_users = array_merge($conflicts, $supression_users);
		}

		$recruitment = AgencyRecruitment::on('bk')
									->where('agency_id', $agency_id)
									->select('user_id')
									->get()->toArray();

		$today_recommended = CollegeRecommendation::where('agency_id', $agency_id)
				    							    ->distinct()->select('user_id')
				    							    ->get()->toArray();
		
		$supression_users = array_merge($today_recommended, $supression_users);

		$supression_users = array_merge($recruitment, $supression_users);
		
		$supression_users = array_unique($supression_users, SORT_REGULAR);

		return $supression_users;
	}

	/**
	 * This method sets the longitude and latitude of the users who recently
	 * have signed up, or their longitude and latitude is not set
	 *
	 * @return void
	 */
	public function setUsersLongLat(){

		// We need to read from Master since the backup is not updated in real time.
		$users = User::whereNotNull('zip')
						->whereNull('longitude')
						->whereNull('latitude')
						->where('country_id', 1)
						->take(10)
						->get();

		foreach ($users as $key) {
			if (count($key->zip) > 0) {

				$zip = ZipCodes::where('ZIPCode', $key->zip)->first();

				if (isset($zip)) {
					$usr = User::find($key->id);

					$usr->longitude = $zip->Longitude;
					$usr->latitude = $zip->Latitude;

					$usr->save();
				}

			}
		}
	}

	/**
	 * This method takes $NUM_OF_RECOMMENDATIONS recommendations per college, and insert it 
	 * into the college_recommendation table
	 *
	 * @return void
	 */
	private function takeNRecommendations($arr = null){

		$ret = array();

		$users_arr = array();

		$orginal_arr = $arr;

		while (count($ret) < self::NUM_OF_RECOMMENDATIONS && !empty($arr)) {
			
			$cnt = 0;
			foreach ($arr as $key) {

				if(isset($key[0]) && count($ret) < self::NUM_OF_RECOMMENDATIONS){

					$tmp = array();

					$tmp = $key[0];
					$tmp['created_at'] = date("Y-m-d H:i:s");
					$tmp['updated_at'] = date("Y-m-d H:i:s");

					$ret[] = $tmp;
					unset($arr[$cnt][0]);

					$arr[$cnt] = array_values($arr[$cnt]);

					$cnt++;

					$users_arr[] = $tmp['user_id'];
				}else{
					unset($arr[$cnt]);

					$arr = array_values($arr);
				}
				
			}
		}

		if (isset($this->filtered_user_array) && !empty($this->filtered_user_array)) {

			$filtered_user = $this->filtered_user_array;
			$filtered_user_ret = array();
			foreach ($filtered_user as $key) {
				$tmp = array();

				$tmp = $key;
				$tmp['created_at'] = date("Y-m-d H:i:s");
				$tmp['updated_at'] = date("Y-m-d H:i:s");

				$users_arr[] = $tmp['user_id'];

				$filtered_user_ret[] = $tmp;
			}
			$ret = array_merge($filtered_user_ret, $ret);
		}

		//import recommendations into the database.
		if (isset($ret) && !empty($ret)) {
			DB::table('college_recommendations')->insert($ret);
		}

		if (!isset($orginal_arr) || empty($orginal_arr)) {
			$orginal_arr = $ret;
		}

		$mda = new MandrillAutomationController;
		$mda->collegeAdminEverydayRecommendations($orginal_arr, $users_arr, $this->filter_sql_raw);

		return $ret;
	}

	/**
	 * This method takes $NUM_OF_RECOMMENDATIONS recommendations per agency, and insert it 
	 * into the college_recommendation table
	 *
	 * @return void
	 */
	private function AgencytakeNRecommendations($arr = null){

		$ret = array();

		$users_arr = array();

		$orginal_arr = $arr;

		while (count($ret) < self::NUM_OF_RECOMMENDATIONS && !empty($arr)) {
			
			$cnt = 0;
			foreach ($arr as $key) {

				if(isset($key[0]) && count($ret) < self::NUM_OF_RECOMMENDATIONS){

					$tmp = array();

					$tmp = $key[0];
					$tmp['created_at'] = date("Y-m-d H:i:s");
					$tmp['updated_at'] = date("Y-m-d H:i:s");

					$ret[] = $tmp;
					unset($arr[$cnt][0]);

					$arr[$cnt] = array_values($arr[$cnt]);

					$cnt++;

					$users_arr[] = $tmp['user_id'];
				}else{
					unset($arr[$cnt]);

					$arr = array_values($arr);
				}
				
			}
		}

		if (isset($this->filtered_user_array) && !empty($this->filtered_user_array)) {

			$filtered_user = $this->filtered_user_array;
			$filtered_user_ret = array();
			foreach ($filtered_user as $key) {
				$tmp = array();

				$tmp = $key;
				$tmp['created_at'] = date("Y-m-d H:i:s");
				$tmp['updated_at'] = date("Y-m-d H:i:s");

				$users_arr[] = $tmp['user_id'];

				$filtered_user_ret[] = $tmp;
			}
			$ret = array_merge($filtered_user_ret, $ret);
		}

		if (!isset($orginal_arr) || empty($orginal_arr)) {
			$orginal_arr = $ret;
		}

		// $mda = new MandrillAutomationController;
		// $mda->agencyAdminEverydayRecommendations($orginal_arr, $users_arr);
		//import recommendations into the database.
		if (isset($ret) && !empty($ret)) {
			DB::table('college_recommendations')->insert($ret);
		}
		return $ret;
	}


	private function getOrgBranchId($college_id, $org_portal_id, $aor_id=null){

		$org_branch = OrganizationBranch::where('school_id', $college_id)->first();

		if (isset($org_branch)) {

			if (isset($org_portal_id) && !empty($org_portal_id)) {
				$op = OrganizationPortal::find($org_portal_id);
				$this->num_of_filtered_rec = $op->num_of_filtered_rec;
			}elseif(isset($aor_id)){
				$aor = Aor::find($aor_id);
				$this->num_of_filtered_rec = $aor->num_of_filtered_rec;
			}else{
				$this->num_of_filtered_rec = $org_branch->num_of_filtered_rec;
			}

			if($org_branch->bachelor_plan == 1){
				$this->is_paying_customer = true;
			}else{
				$this->is_paying_customer = false;
			}	
			return $org_branch->id;
		}
		
		return null;
	}

	private function removeStudentsFromRecommendations($rec_id_arr){
		
		DB::table('recruitment')->whereIn('id', $rec_id_arr)->delete();
	}

	private function addRecylcedUsersToRecommendations($user_id_arr, $arr){

		$ret = array();

		foreach ($user_id_arr as $key => $value) {
			$tmp = array();

			$tmp['is_aor'] = 0;

			if (isset($arr['aor_id']) && !empty($arr['aor_id'])) {
				$tmp['aor_id'] = $arr['aor_id'];
				$tmp['is_aor'] = 1;
			}

			if (isset($arr['org_portal_id']) && !empty($arr['org_portal_id'])) {
				$tmp['org_portal_id'] = $arr['org_portal_id'];
			}

			$tmp['user_id'] = $value;
			$tmp['college_id'] = $arr['org_school_id'];

			$tmp['reason'] = 'filtered_recycled_user';
			$tmp['active'] = 1;
			$tmp['type']   = 'filtered';

			$tmp['created_at'] = date("Y-m-d H:i:s");
			$tmp['updated_at'] = date("Y-m-d H:i:s");

			$ret[] = $tmp;
		}

		//import recommendations into the database.
		if (isset($ret) && !empty($ret)) {
			DB::table('college_recommendations')->insert($ret);
		}
	}

	// Cron Jobs to setup user tiers
	/**
	 * setupRecommendationTier 
	 * This method sets up the recommendation tier of all users
	 *
	 * @return void
	 */
	public function setupRecommendationTier(){

		// Setup tiers for all users who don't have the recommendation tier
		$ct = RecommendationTier::all();

		foreach ($ct as $key) {
			$is_thirty_percent = 0;
			if (strpos($key->name, '30%') !== false) {
				$is_thirty_percent = 1;
			}

			$country_arr = $key->country_ids;
			$country_arr = explode(",", $country_arr);

			$user = User::whereIn('country_id', $country_arr)
						->where('recommendation_tier_id', 0);

			if ($is_thirty_percent == 1) {
				$user = $user->where('profile_percent', '>=', 30);
			}else{
				$user = $user->where('profile_percent', '<', 30);
			}
			$user = $user->update(array('recommendation_tier_id' => $key->id));

			// update the users who have upgraded to 30% since we've done the recommendation tier
			if ($key->id >= 5) {
				$user = User::whereIn('country_id', $country_arr)
						->where('recommendation_tier_id', $key->id)
						->where('profile_percent', '>=', 30)
						->update(array('recommendation_tier_id' => ($key->id - 4)));
			}
		}

	}
	// Cron jobs ends

	public function filterThisPostForThisUser($user_id, $input){
		$arr = array();
		
		if (isset($input['post_id'])) {
			$arr['post_id'] = $input['post_id'];
		}	

		if (isset($input['social_article_id'])) {
			$arr['social_article_id'] = $input['social_article_id'];
		}	
		
		$crf = new CollegeRecommendationFilters;
		$qry = $crf->generateFilterQry($arr);

		if (isset($qry)) {				
			$qry = $qry->groupby('userFilter.id')->select('userFilter.id');

			$this_qry = $qry;
			//$this->filter_sql = $this->getRawSqlWithBindings($qry);
		}else{
			return true;
		}


		if (isset($this_qry) && !empty($this_qry)) {
			$this_qry = $this_qry->where('userFilter.id', $user_id);
			$this_qry = $this_qry->first();
		}

		if (isset($this_qry)) {
			return true;
		}

		return false;
	}
}
