<?php

namespace App\Http\Controllers;

use Request, DB;
use GeoIp2\Database\Reader;
use Carbon\Carbon;
use App\Http\Controllers\MandrillAutomationController;
use App\User, App\Recruitment, App\UsersEmail, App\OrganizationBranch, App\CollegeRecommendationFilters, App\TrackingPage, App\LikesTally;
use App\AgencyRecruitment, App\Agency, App\Country;

class CronEmailController extends Controller
{
    private $global_template_name_colleges = ['in_network_comparison', 'ranking_update', 'near_you', 'chat_session', 
		'school_you_liked' , 'users_in_network_school_you_messaged', 'users_in_network_school_u_wanted_to_get_recruited'];
	private $global_template_name_agencies = ['college_prep_in_area', 'agency_visa_help', 'english_recruiting_in_area'];

	public function runColleges(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$monday = Carbon::parse('last sunday 11:59:59 pm');
		$sunday = Carbon::parse('this sunday 11:59:59 pm');
		$three_days_ago = Carbon::now()->subDays(3);
		// get number of users who we should run in this instance
		// formula is total num of user / 7 days of week / num of mins in a day
		$cnt = User::on('rds1')->select(DB::raw('CEIL((count(*) / 7) /720) as cnt'))->first();

		$users = DB::connection('rds1')->table('users as u')
					
					->select('u.*')
					->where(function ($query) use ($monday, $sunday, $three_days_ago) {
						$query->where(function ($q) use($monday, $sunday, $three_days_ago) {
							$q->whereNull('cron_college_date');
							$q->orWhere('u.cron_college_date', '<', $monday);
						});
					
						$query->where(function ($q) use($monday, $sunday, $three_days_ago) {
							$q->whereNull('u.cron_agency_date');
							$q->orWhere('u.cron_agency_date', '<', $three_days_ago);
						});

						$query->orWhere('u.cron_college_date', '<', $monday);
					})
					->where('u.is_alumni', 0)
					->where('u.is_parent', 0)
					->where('u.is_counselor', 0)
					->where('u.is_organization', 0)
					->where('u.is_agency', 0)
					->where('u.is_plexuss', 0)
					->where('u.is_university_rep', 0)
					->where('u.is_ldy', 0)
					->take($cnt->cnt)
					->orderBy('u.cron_college_date')
					->orderBy('u.cron_agency_date')
					->orderBy('u.profile_percent', 'DESC')
					->get();

		//$users = User::where('id', 93)->get();
		foreach ($users as $user) {

			$rec = Recruitment::on('rds1')->where('user_id', $user->id)
								->select('college_id')
								->whereNotNull('college_id')
								->distinct()
								->get()
								->toArray();
			$users_emails = UsersEmail::on('rds1')->where('user_id', $user->id)
										->where('type', 'college')
										->select('type_id as college_id')
										->whereNotNull('type_id')
										->distinct()
										->get();
			
			$users_emails_college_id = array();

			foreach ($users_emails as $k) {
				$users_emails_college_id[] = $k->college_id;	
			}
			$supression_college_id = array_merge($rec, $users_emails_college_id);

			$template_cnt = UsersEmail::on('rds1')->where('user_id', $user->id)
										->where('type', 'college')
										->select(DB::raw('email_template, count(email_template) as cnt'))
										->groupby('email_template')
										->orderBy('cnt')
										->get();
			$user_template = array();
			foreach ($template_cnt as $k) {
				$user_template[] = $k->email_template;
			}

			$template_to_run = '';

			if (count($template_cnt) < 7) {

				$arr = array_intersect($this->global_template_name_colleges, $user_template);
				$tempArr = $this->global_template_name_colleges;
				foreach ($arr as $k) {
					if(($arrKey = array_search($k, $tempArr)) !== false) {
					    unset($tempArr[$arrKey]);
					}
				}
				$tempArr = array_values($tempArr);
				$template_to_run = $tempArr;
				
			}else{

				$template_to_run = $user_template;
			}
			//dd($template_to_run);							
			$check = false;	

			$current_live_colleges = $this->current_live_colleges();
			if (isset($current_live_colleges) && !empty($current_live_colleges)) {
				$check = $this->chatSessionCollege($supression_college_id, $user);
			}			

			foreach ($template_to_run as $k) {

				if ($check == true) {
					break;
				}
				switch ($k) {
					case 'in_network_comparison':
						$check = $this->inOurNetworkCollege($supression_college_id, $user);
						break;

					case 'ranking_update':
						$check = $this->rankingUpdateCollege($user);
						break;

					case 'near_you':
						$check = $this->nearYouCollege($supression_college_id, $user);
						break;

					case 'chat_session':
						$check = $this->chatSessionCollege($supression_college_id, $user);
						break;

					case 'school_you_liked':
						$check = $this->schoolYouLikedCollege($supression_college_id, $user);
						break;

					case 'users_in_network_school_you_messaged':
						$check = $this->schoolYouMessagedToCollege($supression_college_id, $user);
						break;

					case 'users_in_network_school_u_wanted_to_get_recruited':
						$check = $this->schoolYouWereRecruitedCollege($supression_college_id, $user);
						break;
					
					default:
						# code...
						break;
				}
			}
			// if none are applied, run in our network college
			if ($check === false) {
				$check = $this->inOurNetworkCollege($supression_college_id, $user);
			}

			$tmp_usr = User::find($user->id);
			$tmp_usr->cron_college_date = Carbon::now();

			$tmp_usr->save();
		}
	}

	public function runAgencies(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$monday = Carbon::parse('last sunday 11:59:59 pm');
		$sunday = Carbon::parse('this sunday 11:59:59 pm');
		$three_days_ago = Carbon::now()->subDays(3);

		// get number of users who we should run in this instance
		// formula is total num of user / 7 days of week / num of mins in a day
		$cnt = User::on('rds1')->select(DB::raw('CEIL((count(*) / 7) /720) as cnt'))->first();

		$users = DB::connection('rds1')->table('users as u')
					
					->select('u.*')
					->where(function ($query) use ($monday, $sunday, $three_days_ago) {
						$query->where(function ($q) use($monday, $sunday, $three_days_ago) {
							$q->whereNull('cron_agency_date');
							$q->orWhere('u.cron_agency_date', '<', $monday);
						});
					
						$query->where(function ($q) use($monday, $sunday, $three_days_ago) {
							$q->whereNull('u.cron_college_date');
							$q->orWhere('u.cron_college_date', '<', $three_days_ago);
						});

						$query->orWhere('u.cron_agency_date', '<', $monday);
					})
					->where('u.is_alumni', 0)
					->where('u.is_parent', 0)
					->where('u.is_counselor', 0)
					->where('u.is_organization', 0)
					->where('u.is_agency', 0)
					->where('u.is_university_rep', 0)
					->where('u.is_ldy', 0)
					->take($cnt->cnt)
					->orderBy('u.cron_agency_date')
					->orderBy('u.cron_college_date')
					->orderBy('u.profile_percent', 'DESC')
					->get();

		//$users = User::where('id', 913)->get();

		foreach ($users as $user) {

			$rec = AgencyRecruitment::on('rds1')->where('user_id', $user->id)
								->select('agency_id')
								->get()
								->toArray();

			$users_emails = UsersEmail::on('rds1')->where('user_id', $user->id)
										->where('type', 'agency')
										->select('type_id as agency_id')
										->get();
			
			$users_emails_agency_id = array();
			foreach ($users_emails as $k) {
				$users_emails_agency_id[] = $k->agency_id;	
			}
			$supression_agency_id = array_merge($rec, $users_emails_agency_id);

			$agency = Agency::on('rds1')->whereNotIn('id', $supression_agency_id)
							->where('active', 1)
							->orderBy(DB::raw('RAND()'));

			if (isset($user->country_id) && $user->country_id == 1) {
				$agency = $agency->where('type', 'College Prep');
				if (isset($user->state)) {
					$agency = $agency->where('state', $user->state);
				}
			}else{

				if (!isset($user->country_id)) {
					$agency = $agency->where('type', 'English Institution');
				}else{
					
					$rnd = rand(0,1);

					if ($rnd == 0) {
						$user_country = Country::where('id', $user->country_id)->first();
						$user_country = $user_country->country_name;

						$agency = $agency->where('type', 'International Agency')
										 ->where('country', $user_country);
					}else{
						$agency = $agency->where('type', 'English Institution');
					}
				}
				
			}

			$agency = $agency->first();

			if (isset($agency)) {		

				if ($agency->type == 'College Prep') {
					$template_name = 'college_prep_recruiting_in_area';
				}
				if ($agency->type == 'International Agency') {
					$template_name = 'agency_recruiting_visa_help';
				}
				if ($agency->type == 'English Institution') {
					$template_name = 'english_recruiting_in_area';
				}
				$this->sendEmailAgencies('agency', $template_name, $agency, $user);

				$tmp_usr = User::find($user->id);
				$tmp_usr->cron_agency_date = Carbon::now();

				$tmp_usr->save();
			}
		}
	}

	public function updateUsersTable(){

		UsersEmail::on('rds1')->where('id', '>', 4098)->chunk(200, function($users_emails)
		{
			$num = 0;
			foreach ($users_emails as $key) {
				if ($key->type == "college") {
					$ntn = DB::statement('UPDATE `users` SET cron_college_date ="'.$key->created_at.'" where 
						`id` = '.$key->user_id);
				}else{
					$ntn = DB::statement('UPDATE `users` SET cron_agency_date ="'.$key->created_at.'" where 
						`id` = '.$key->user_id);
				}
			}
			print_r("ola <br>");
		});
	}

	//College methods start here
	public function inOurNetworkCollege($supression_list, $user){

		$template_name = "in_network_comparison";

		$in_network = OrganizationBranch::on('bk')->orderBy(DB::raw('RAND()'))->where('school_id', '!=', 7916);

		if (isset($supression_list)) {
			$tmp_in_network = $in_network->whereNotIn('school_id', $supression_list);
			$in_network = $in_network->whereNotIn('school_id', $supression_list)->first();
		}else{
			$tmp_in_network = $in_network();
			$in_network = $in_network->first();
		}

		if (!isset($in_network)) {
			dd($tmp_in_network->where('aa', 1)->first());
		}

		$arr = array();
		$arr['org_branch_id'] = $in_network->id ;
		$arr['org_school_id'] = $in_network->school_id;
		$crf = new CollegeRecommendationFilters;
		$qry = $crf->generateFilterQry($arr);
			
		if (isset($qry)) {
			$qry = $qry->groupby('userFilter.id')->select('userFilter.*')->where('userFilter.id', $user->id);
			$filter_sql = $this->getRawSqlWithBindings($qry);

			$statement = DB::select(DB::raw($filter_sql));
			if (isset($statement)) {
				$this->sendEmailColleges('college', $template_name, $in_network, $user, $in_network->school_id);
				return true;
			}else{
				return false;
			}
		}else{
			$this->sendEmailColleges('college', $template_name, $in_network, $user, $in_network->school_id);
			return true;
		}
	}

	public function rankingUpdateCollege($user){

		$last_month = Carbon::now()->subMonth();

		$template_name = "ranking_update";

		$supression_list = UsersEmail::on('rds1')->where('user_id', $user->id)
										->where('type', 'college')
										->select('type_id as college_id')
										->where('created_at', '>=', $last_month)
										->get()
										->toArray();

		$colleges = DB::connection('bk')->table('lists as l')
										->join('organization_branches as ob', 'ob.school_id', '=', 'l.custom_college')
										->join('recruitment as r', 'r.college_id', '=', 'ob.school_id')
										->where('l.updated_at', '>=', $last_month)
										->where('ob.school_id', '!=', 7916)	
										->where('r.user_id', $user->id)			
										->orderBy(DB::raw('RAND()'));

		if (isset($supression_list)) {
			$colleges = $colleges->whereNotIn('ob.school_id', $supression_list)->first();
		}else{
			$colleges = $colleges->first();
		}

		if (isset($colleges)) {
			$this->sendEmailColleges('college', $template_name, $colleges, $user, $colleges->school_id);
			return true;
		}
		
		return false;
	}

	public function nearYouCollege($supression_list, $user){

		$template_name = "near_you";

		if (!isset($user->country_id) || $user->country_id !=1) {
			return false;
		}

		$tp = TrackingPage::on('rds1-log')->where('user_id', $user->id)
							->orderBy('id', 'DESC')
							->first();

		if (isset($tp)) {
			$ip = $tp->ip;
		}else{
			return false;
		}
		
		$ip = str_replace(",", "", $ip);
		$locationArr = $this->ipLookIpBasedOnIp($ip);

		if (!isset($locationArr['latitude'])) {
			return false;
		}
		
		$query = 'c.id as college_id, c.slug, c.school_name as school_name, c.city, c.state, c.verified, ( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( c.latitude ) ) * cos( radians( c.longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(c.latitude)) ) ) AS distance ';
			
		$colleges = DB::connection('bk')->table('colleges as c')
										->join('organization_branches as ob', 'ob.school_id', '=', 'c.id')
										->select( DB::raw( 'c.id as school_id, '.$query ) )
										->where('c.verified', 1)
										->groupby('c.id')
										->orderBy('distance', 'ASC')
										->having('distance', '<', 100);

		if (isset($supression_list)) {
			$colleges = $colleges->whereNotIn('ob.school_id', $supression_list)->first();
		}else{
			$colleges = $colleges->first();
		}

		if (isset($colleges)) {
			$this->sendEmailColleges('college', $template_name, $colleges, $user, $colleges->school_id);
			return true;
		}
		
		return false;
	}

	public function chatSessionCollege($supression_list, $user){

		$template_name = "chat_session";

		$current_live_colleges = $this->current_live_colleges();
		//$current_live_colleges = ['4618', '1002', '2541', '6721', '7191'];
		if (!isset($current_live_colleges) || empty($current_live_colleges)) {
			return false;
		}
		
		$this->sendEmailColleges('college', $template_name, $current_live_colleges, $user, $current_live_colleges[0]);
		
		return true;
	}

	public function schoolYouLikedCollege($supression_list, $user){
		$template_name = "school_you_liked";

		$lt   = LikesTally::on('bk')->where('user_id', $user->id)
									->where('type', 'college')
									->where('type_col', 'id')
									->join('colleges as c', 'c.id', '=', 'likes_tally.type_val')
									->join('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									//->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'))
									->select('cr.plexuss as rank', 'c.id as college_id', 'c.school_name')
									->orderBy(DB::raw('RAND()'))
									->whereNotNull('cr.plexuss')
									->first();

		if (!isset($lt->rank)) {
			return false;
		}

		$ob =   DB::connection('bk')->table('organization_branches as ob')
									->join('colleges_ranking as cr', 'cr.college_id', '=', 'ob.school_id')
									->leftjoin('recruitment as r', function ($query) use($user) {
										$query->on('r.college_id', '=', 'ob.school_id');
										$query->where('r.user_id', '=', $user->id);
									})
									->where('cr.plexuss', '<=', $lt->rank)
									->where('ob.school_id', '!=', 7916)
									->whereNull('r.id')
									->whereNotNull('cr.plexuss')
									->orderBy(DB::raw('RAND()'));

		if (isset($supression_list)) {
			$ob = $ob->whereNotIn('ob.school_id', $supression_list)->first();
		}else{
			$ob = $ob->first();
		}

		if (!isset($ob)) {
			return false;
		}

		$arr = array();
		$arr['college_id_you_liked'] = $lt->college_id;
		$arr['college_name_you_liked'] = $lt->school_name;
		$arr['college_id_recommended'] = $ob->school_id; 

		$this->sendEmailColleges('college', $template_name, $arr, $user, $arr['college_id_recommended']);
		
		return true;
	}

	public function schoolYouWereRecruitedCollege($supression_list, $user){
		$template_name = "users_in_network_school_u_wanted_to_get_recruited";

		$rec =  DB::connection('bk')->table('recruitment as r')
									->join('colleges as c', 'c.id', '=', 'r.college_id')
									->join('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									//->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'))
									->select('cr.plexuss as rank', 'c.id as college_id', 'c.school_name')
									->where('r.user_id', $user->id)
									->orderBy(DB::raw('RAND()'))
									->whereNotNull('cr.plexuss')
									->first();

		if (!isset($rec->rank)) {
			return false;
		}
		$ob   = DB::connection('bk')->table('organization_branches as ob')
									->join('colleges_ranking as cr', 'cr.college_id', '=', 'ob.school_id')
									->leftjoin('recruitment as r', function ($query) use($user) {
										$query->on('r.college_id', '=', 'ob.school_id');
										$query->where('r.user_id', '=', $user->id);
									})
									->where('cr.plexuss', '<=', $rec->rank)
									->where('ob.school_id', '!=', 7916)
									->whereNull('r.id')
									->whereNotNull('cr.plexuss')
									->orderBy(DB::raw('RAND()'));

		if (isset($supression_list)) {
			$ob = $ob->whereNotIn('ob.school_id', $supression_list)->first();
		}else{
			$ob = $ob->first();
		}

		if (!isset($ob)) {
			return false;
		}

		$arr = array();
		$arr['college_id_you_recruited'] = $rec->college_id;
		$arr['college_name_you_recruited'] = $rec->school_name;
		$arr['college_id_recommended'] = $ob->school_id; 

		$this->sendEmailColleges('college', $template_name, $arr, $user, $arr['college_id_recommended']);
		
		return true;
	}

	public function schoolYouMessagedToCollege($supression_list, $user){
		$template_name = "users_in_network_school_you_messaged";

		$msg  = DB::connection('bk')->table('college_message_threads as cmt')
									->join('college_message_thread_members as cmtm', function($query) use($user){
										$query->on('cmt.id', '=', 'cmtm.thread_id');
										$query->where('cmtm.user_id', '=', $user->id);
									})
									->join('organization_branches as ob', 'ob.id', '=', 'cmtm.org_branch_id')
									->join('colleges as c', 'c.id', '=', 'ob.school_id')
									->join('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									//->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'))
									->select('cr.plexuss as rank', 'c.id as college_id', 'c.school_name')
									->orderBy(DB::raw('RAND()'))
									->whereNotNull('cr.plexuss')
									->first();

		if (!isset($msg->rank)) {
			return false;
		}

		$ob =   DB::connection('bk')->table('organization_branches as ob')
									->join('colleges_ranking as cr', 'cr.college_id', '=', 'ob.school_id')
									->leftjoin('recruitment as r', function ($query) use($user) {
										$query->on('r.college_id', '=', 'ob.school_id');
										$query->where('r.user_id', '=', $user->id);
									})
									->where('cr.plexuss', '<=', $msg->rank)
									->where('ob.school_id', '!=', 7916)
									->whereNull('r.id')
									->whereNotNull('cr.plexuss')
									->orderBy(DB::raw('RAND()'));

		if (isset($supression_list)) {
			$ob = $ob->whereNotIn('ob.school_id', $supression_list)->first();
		}else{
			$ob = $ob->first();
		}

		if (!isset($ob)) {
			return false;
		}

		$arr = array();
		$arr['college_id_you_messaged'] = $msg->college_id;
		$arr['college_name_you_messaged'] = $msg->school_name;
		$arr['college_id_recommended'] = $ob->school_id; 

		$this->sendEmailColleges('college', $template_name, $arr, $user, $arr['college_id_recommended']);
		
		return true;
	}

	public function sendEmailColleges($type, $template_name, $type_collection, $user, $type_id){
		if ($template_name == "chat_session") {
			foreach ($type_collection as $key) {
				$ue = new UsersEmail;
				$ue->email_template = $template_name;
				$ue->type = $type;
				$ue->type_id = $key;
				$ue->user_id = $user->id;

				$ue->save(); 
			}
		}else{
			$ue = new UsersEmail;
			$ue->email_template = $template_name;
			$ue->type = $type;
			$ue->type_id = $type_id;
			$ue->user_id = $user->id;

			$ue->save(); 
		}
		

		$mac = new MandrillAutomationController;
		$mac->userEmailColleges($template_name, $type_collection, $user);
	}
	//College methods ends here
	
	//Agency methods start here
	public function sendEmailAgencies($type, $template_name, $type_collection, $user){
		
		$ue = new UsersEmail;
		$ue->email_template = $template_name;
		$ue->type = $type;
		$ue->type_id = $type_collection->id;
		$ue->user_id = $user->id;

		$ue->save(); 

		$mac = new MandrillAutomationController;
		$mac->userEmailAgencies($template_name, $type_collection, $user);
	}	
	//Agency methods ends here

	/**
	 * ip lookup return cit, state, country, latitude, and longitude
	 *
	 * @return view or redirect
	 */
	private function ipLookIpBasedOnIp($ip) {

		// This creates the Reader object, which should be reused across
		// lookups.
		$reader = new Reader( base_path(). env('GEOLITE'));

		$privateIP = $this->checkForPrivateIP($ip);

		//If remote IP fails we default to office IP.
		if($ip == '::1' || $privateIP) {
			$ip = '50.0.50.17';
		}

		// ip lookup fixups
		if (strpos($ip, ',') !== FALSE){

			$comma = strpos($ip, ',');
			if (strpos($ip, ':') !== FALSE){

				$ip = substr($ip, $comma+2, strlen($ip));

			}else{

				$ip = substr($ip, 0, $comma);
			}
		}
		
		$excp = false;
		try {
			$record = $reader->city($ip);
		} catch (\Exception $e) {
			$excp = true;
		}
		
		//dd($record->city->name. ' city | latitude ' .$record->location->latitude . '  | longitude  ' .$record->location->longitude);
		$arr = array();
		if ($excp === true) {
			$record = $reader->city('50.161.86.17');	
		}

		$arr['countryAbbr'] = $record->country->isoCode;
		$arr['countryName'] = $record->country->name;

		$arr['stateName'] = $record->mostSpecificSubdivision->name;
		$arr['stateAbbr'] = $record->mostSpecificSubdivision->isoCode;
		$arr['cityName'] = $record->city->name;
		$arr['cityAbbr'] = $record->postal->code;
		$arr['latitude'] = $record->location->latitude;
		$arr['longitude'] = $record->location->longitude;
		$arr['time_zone'] = $record->location->timeZone;
		/*
		print($record->country->isoCode . "\n"); // 'US'
		print($record->country->name . "\n"); // 'United States'
		print($record->country->names['zh-CN'] . "\n"); // '美国'

		print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
		print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'

		print($record->city->name . "\n"); // 'Minneapolis'

		print($record->postal->code . "\n"); // '55455'

		print($record->location->latitude . "\n"); // 44.9733
		print($record->location->longitude . "\n"); // -93.2323
		*/

		return $arr;
	}
}
