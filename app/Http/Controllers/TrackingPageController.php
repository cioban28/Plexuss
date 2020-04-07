<?php

namespace App\Http\Controllers;

use App\TrackingPage, App\User;
use App\TrackingPageId, App\TrackingPageUrl, App\TrackingPageUrlCount,  App\TrackingPageUrlIpCount, App\TrackingPageUrlUserCount, App\College, App\TrackingPageParam, App\Browser, App\Device, App\Platform, App\TrackingPageUrlDictionary, App\TrackingPageUrlDictionaryLog, App\TrackingPageCronTracker, App\Fragment, App\TrackingUrlFragmentId, App\UniqueLoggedInUsersPerDay, App\UniqueLoggedOutUsersPerDay, App\OverviewReportingUniqueVisit, App\DatesModel, App\RevenueOrganization, App\OverviewReportingMonetization, App\OverviewReportingGeneral, App\TrackingModal;

use Request, Session, Cookie, Illuminate\Support\Facades\Auth, Jenssegers\Agent\Agent, Illuminate\Support\Facades\Route;
use DateTime, DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TrackingPageController extends Controller
{
    //
    public function setPageView() {

		$url = Request::url();

		$user_id = 0;
		$token  = '';
		$ip = '';

		$input = Request::all();

		$user_table = Session::get('user_table');

		//$ip = Request::getClientIp();


		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			if (isset( $_SERVER['REMOTE_ADDR']) ) {
				$ip =  $_SERVER['REMOTE_ADDR'];
			}

		}

		$user_cookie_id = Cookie::get('user_id');

		$log = '';

		$tp = new TrackingPage;

		$tp->setConnection('log');


		if(isset($user_cookie_id) && !Session::has('tracking_pages_updated_user_ids') && (strpos($url,'chat') !== true) ){

			if ($ip != '127.0.0.1') {

				$d = new DateTime('today');

				$tmp = TrackingPage::on('log')->whereraw("created_at >= DATE('".date_format($d, 'Y-m-d')."')")
				->where('ip', '=', $ip)
				->where('user_id', 0)
				->update(array('user_id' => $user_cookie_id));

				Session::put('tracking_pages_updated_user_ids', 'true' );
				$log = ' UPDATED ALL ROWS WITH THE COOKIE USER ID <br>' ;
			}
		}


		if(isset($user_table )){

			$user = $user_table;
			$user_id = $user->id;
			$token = $user->remember_token;

			$log .= ' SESSION WAS FOUND <BR>';
		}else{
			if ( Auth::check() ) {

				$id = Auth::id();

				$user = User::find( $id );

				$user_id = $user->id;

				$token = $user->remember_token;

				Cookie::queue('user_id', $user->id, 10080);
				Session::put('user_table', $user );

				$log .= ' NO SESSION FOUND COOKIE CREATED <BR>';
			}

		}

		$agent = new Agent();

		$requestFormat = Request::format();

		$isAjax = Request::ajax();

		$server_info = Request::server('PATH_INFO');

		$browser = $agent->browser();
		$version = $agent->version( $browser );

		$browser = $browser. " ". $version;


		$platform = $agent->platform();
		$version = $agent->version( $platform );

		$platform = $platform. " ".  $version;


		$device = $agent->isMobile();

		if ( $device ) {
			$device = $agent->device();
		}else {
			$device = "Desktop";
		}

		$params = Request::all();

		isset($params['user_id']) ? $user_id = $params['user_id'] : NULL;
		isset($params['uid']) 	  ? $user_id = $params['uid'] : NULL;

		//Don't save the password of users!
		if(strpos($url, "signin") || strpos($url, "signup")){
			$params = "";
		}else{
			$params = json_encode($params);
		}

		if ($url == 'https://plexuss.com/ajax/getTopNavNotification' || $url == 'https://dev.plexuss.com/ajax/getTopNavNotification'
			|| $url == 'http://plexuss.dev/ajax/getTopNavNotification' || $url == 'https://plexuss.com/getNumberOfHandshakes') {
			return;
		}


		$keyword = $this->search_engine_query_string($url);

		$slug = '';
		if (strpos($url, "/college/") && !strpos($url, 'ajax')) {
			$slug = $this->get_string_between($url , "/college/", substr($url, -4));

			if(strpos($slug, "/")){
				$dashPos = strpos($slug, "/");
				$slugLength = strlen($slug);

				$replaceStr = substr($slug, - ($slugLength - $dashPos));

				$slug = str_replace($replaceStr, '', $slug);
			}
		}else{
			$slug = '';
		}




		if(isset($input['pixel'])){
			$pixel = $input['pixel'];
		}else{
			$pixel = '';
		}

		if(isset($input['camid'])){
			$camid = $input['camid'];
		}else{
			$camid = '';
		}

		if(isset($input['specid'])){
			$specid = $input['specid'];
		}else{
			$specid = '';
		}

		$tp->ip = $ip;
		$tp->pixel = $pixel;
		$tp->specid = $specid;
		$tp->camid = $camid;
		$tp->url = $url;
		$tp->user_id = $user_id;
		$tp->isAjax = $isAjax;
		$tp->slug = $slug;
		$tp->device = $device;
		$tp->browser = $browser;
		$tp->platform = $platform;
		$tp->keyword = $keyword;
		$tp->token = $token;
		$tp->params = $params;
		$tp->requestFormat = $requestFormat;

		$tp->save();


		return;
	}

	private function search_engine_query_string($url = false) {

	    if(!$url && !$url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false) {
	        return '';
	    }

	    $parts_url = parse_url($url);
	    $query = isset($parts_url['query']) ? $parts_url['query'] : (isset($parts_url['fragment']) ? $parts_url['fragment'] : '');
	    if(!$query) {
	        return '';
	    }
	    parse_str($query, $parts_query);
	    return isset($parts_query['q']) ? $parts_query['q'] : (isset($parts_query['p']) ? $parts_query['p'] : '');
	}

	private function get_string_between($string, $start, $end){
	    $string = " ".$string;
	    $ini = strpos($string,$start);
	    if ($ini == 0) return "";

	    $ini += strlen($start);


	    $len = strpos($string,$end,$ini) - $ini;


	    return substr($string,$ini,$len) . $end;
	}

	public function setTrackingUrls($from_date = null, $to_date = null){

		$time_now = Carbon::now()->toTimeString();
		$start_time = "00:00:00";
		$end_time   = "07:30:00";

		if (isset($start_time) && isset($end_time)) {

			$can_i_run = true;
			if ($time_now >= $start_time && $time_now <= $end_time) {
				$can_i_run = false;
			}

			if ($can_i_run == false) {
				return "Can't run this at this time";
			}
		}

		if (!isset($from_date) && !isset($to_date)) {

			if (Cache::has( env('ENVIRONMENT') .'_'. '__setTrackingUrls')) {

	    		$cron = Cache::get( env('ENVIRONMENT') .'_'. '__setTrackingUrls');

	    		if ($cron == 'in_progress') {
	    			return "a cron is already running";
	    		}
	    	}

	    	Cache::put( env('ENVIRONMENT') .'_'. '__setTrackingUrls', 'in_progress', 30);



			$from_date = null;
			$to_date   = null;

			$dt = TrackingPageId::on('bk-log')
								->orderBy('date', 'DESC')
								->take(2)
								->get();

			foreach ($dt as $key) {

				if (!isset($to_date)) {
					$to_date =  $key->date;
				}elseif (!isset($from_date)) {
					$from_date =  $key->date;
				}
			}
		}else{
			if (Cache::has( env('ENVIRONMENT') .'_'. '__setTrackingUrls_'.$from_date."__".$to_date)) {

	    		$cron = Cache::get( env('ENVIRONMENT') .'_'. '__setTrackingUrls_'.$from_date."__".$to_date);

	    		if ($cron == 'in_progress') {
	    			return "a cron is already running";
	    		}
	    	}

	    	Cache::put( env('ENVIRONMENT') .'_'. '__setTrackingUrls_'.$from_date."__".$to_date, 'in_progress', 30);
		}

		$from_tpi = TrackingPageId::on('bk-log')
		                     ->where('date', $from_date)
		                     ->first();

		$to_tpi   = TrackingPageId::on('bk-log')
		                     ->where('date', $to_date)
		                     ->first();


		$tmp = TrackingPageCronTracker::on('bk-log')
									  ->where('from_date', $from_date)
									  ->where('to_date', $to_date)
									  ->first();


		$take = 5000;
		$skip = null;

		if (!isset($tmp)) {
			$cnt = TrackingPage::on('bk-log')
		                   ->whereBetween('id', array($from_tpi->tp_id, $to_tpi->tp_id))
		                   ->count();

			$new = new TrackingPageCronTracker;
			$new->setConnection('log');
			$new->from_date = $from_date;
			$new->to_date   = $to_date;
			$new->num_ran   = $take;
			$new->total     = $cnt;

			$new->save();
		}else{
			if ($tmp->num_ran >= $tmp->total) {
				return "no more today";
			}
			$skip = $tmp->num_ran;
			TrackingPageCronTracker::on('log')
								   ->where('id', $tmp->id)
								   ->update(array('num_ran' => $skip + $take));
		}

		$qry = TrackingPage::on('bk-log')
		                   ->whereBetween('id', array($from_tpi->tp_id, $to_tpi->tp_id))
		                   ->take($take);

		if (isset($skip)) {
			$qry =  $qry->skip($skip);
		}

		$qry =  $qry->get();


		$cnt = 0;
		foreach ($qry as $key) {

			$date = Carbon::parse($key->created_at);
			$date = $date->toDateString();

			$college_id = null;
			if (!empty($key->slug)) {
				$college 	= College::on('bk')
				                     ->where('slug', $key->slug)
				                     ->where('verified', 1)
				                     ->first();
				if (isset($college)) {
					$college_id = $college->id;
				}
			}

			$check= false;
			if (strpos($key->url, 'https://plexuss.com') !== FALSE){
				$check = true;
			}
			if (strpos($key->url, 'https://www.plexuss.com') !== FALSE){
				$check = true;
			}
			if (!$check){
				// $this->customdd($key->url);
				continue;
			}


			$attr = array('url' =>  $key->url);
			$val  = array('url' =>  $key->url);

			$url = $this->thisUpdateOrCreate(new TrackingPageUrl, $attr, $val);
			// $url  = TrackingPageUrl::on('log')->updateOrCreate($attr, $val);

			$tpud_id =  $this->setUrlDictionary($key->url);

			$attr = array('tpu_id' => $url->id, 'tpud_id' =>  $tpud_id);
			$val  = array('tpu_id' => $url->id, 'tpud_id' =>  $tpud_id);

			$this->thisUpdateOrCreate(new TrackingPageUrlDictionaryLog, $attr, $val);
			// TrackingPageUrlDictionaryLog::on('log')->updateOrCreate($attr, $val);

			// $this->customdd($ret_id);
			// $this->customdd($key->url);
			// exit();

			if (!empty($key->params) && $key->params != "[]") {
				$attr = array('date' => $date, 'tpu_id' => $url->id, 'params' => $key->params);
				$val  = array('date' => $date, 'tpu_id' => $url->id, 'params' => $key->params);

				$this->thisUpdateOrCreate(new TrackingPageParam, $attr, $val);
				// TrackingPageParam::on('log')->updateOrCreate($attr, $val);
			}

			$attr = array('date' => $date, 'tpu_id' => $url->id);
			$val  = array('date' => $date, 'tpu_id' => $url->id,
				     	  'count' => DB::raw('count + 1'), 'college_id' => $college_id);

			$this->thisUpdateOrCreate(new TrackingPageUrlCount, $attr, $val);
			// TrackingPageUrlCount::on('log')->updateOrCreate($attr, $val);

			$device_id   = $this->findDeviceIds('device',   $key->device);
			$platform_id = $this->findDeviceIds('platform', $key->platform);
			$browser_id  = $this->findDeviceIds('browser',  $key->browser);

			if ($key->user_id == 0) {
				$attr = array('ip' => $key->ip, 'tpu_id' => $url->id);
				$val  = array('ip' => $key->ip, 'date' => $date, 'count' => DB::raw('count + 1'),
					          'tpu_id' => $url->id, 'device_id' => $device_id, 'browser_id' => $browser_id,
					          'platform_id' => $platform_id, 'college_id' => $college_id);

				$this->thisUpdateOrCreate(new TrackingPageUrlIpCount, $attr, $val);
				// TrackingPageUrlIpCount::on('log')->updateOrCreate($attr, $val);
			}else{
				$attr = array('user_id' => $key->user_id, 'tpu_id' => $url->id);
				$val  = array('user_id' => $key->user_id, 'date' => $date, 'count' => DB::raw('count + 1'),
					          'tpu_id' => $url->id, 'device_id' => $device_id, 'browser_id' => $browser_id,
					          'platform_id' => $platform_id, 'college_id' => $college_id);

				$this->thisUpdateOrCreate(new TrackingPageUrlUserCount, $attr, $val);
				// TrackingPageUrlUserCount::on('log')->updateOrCreate($attr, $val);
			}
			$cnt++;
			// $this->customdd($cnt);
		}

		if (isset($from_date) && isset($to_date)) {
			Cache::put( env('ENVIRONMENT') .'_'. '__setTrackingUrls_'.$from_date."__".$to_date, 'done', 30);
		}else{
			Cache::put( env('ENVIRONMENT') .'_'. '__setTrackingUrls', 'done', 30);
		}

		return "success";
	}

	public  function customSetTrackingUrls(){
		$from_date = "2018-12-06";
		$to_date   = "2018-12-07";

		$tmp = TrackingPageCronTracker::on('bk-log')
									  ->where('from_date', $from_date)
									  ->where('to_date', $to_date)
									  ->first();

		if ($tmp->num_ran > $tmp->total) {
			return "this date is  done";
		}
		return $this->setTrackingUrls($from_date, $to_date);
	}

	public function saveLoggedInUsersForEachDay($is_logged_out = NULL, $force_date = NULL){

		if (isset($is_logged_out)) {
			$check = 'true';
		}else{
			$check = '';
		}

		if (isset($force_date)) {
			$check .= "_".$force_date;
		}
		if (Cache::has( env('ENVIRONMENT') .'_'. 'saveLoggedInUsersForEachDay'.$check)) {

    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'saveLoggedInUsersForEachDay'.$check);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'saveLoggedInUsersForEachDay'.$check, 'in_progress', 10);

    	if (isset($force_date)) {
    		$start_date = $force_date;
    	}else{
    		if (isset($is_logged_out)) {
	    		$tmp  = UniqueLoggedOutUsersPerDay::on('bk-log')->orderBy('date', 'asc')
													   ->first();
	    	}else{
	    		$tmp  = UniqueLoggedInUsersPerDay::on('bk-log')->orderBy('date', 'asc')
													   ->first();
	    	}


			$start_date = Carbon::parse($tmp->date)->subDay(1);
			$start_date = Carbon::parse($start_date)->toDateString();
    	}

		$dt_model = DatesModel::on('bk-log')->where('date', $start_date)
											->first();
		// $start_date = "2018-12-17";

		$end_date = Carbon::parse($start_date);
		$end_date = $end_date->addDay(1);
		$end_date = $end_date->toDateString();

		$dt = TrackingPageId::on('bk-log')
								->orderBy('date', 'DESC')
								->orWhere('date', $start_date)
								->orWhere('date', $end_date)
								->groupBy('date')
								->get();

		$from_id = NULL;
		$to_id 	 = NULL;
		foreach ($dt as $key) {
			if (!isset($to_id)) {
				$to_id = $key->tp_id;
			}elseif (!isset($from_id)) {
				$from_id = $key->tp_id;
			}
		}

		$tp = TrackingPage::on('bk-log')
						  ->where('id', '>=', $from_id)
						  ->where('id', '<', $to_id)
						  ->select('*')

						  ->where(function($q){
						  		$q->orWhere('url', 'LIKE', DB::raw("'https://plexuss.com%'"));
						  		$q->orWhere('url', 'LIKE', DB::raw("'https://www.plexuss.com%'"));
						  });


		if (isset($is_logged_out)) {
			$tp = $tp->where('user_id', 0)
					 ->groupBy('ip');
		}else{
			$tp = $tp->where('user_id', '!=', 0)
					 ->groupBy('user_id');
		}

		$tp = $tp->get();

		$dt = Carbon::now();
		$user_arr = array();

		foreach ($tp as $key) {
			$tmp  = array();

			if (!isset($is_logged_out)) {
				$device_id   = $this->findDeviceIds('device',   $key->device);
				$platform_id = $this->findDeviceIds('platform', $key->platform);
				$browser_id  = $this->findDeviceIds('browser',  $key->browser);

				$tmp['user_id']    = $key->user_id;

				$tmp['device_id']    = $device_id;
				$tmp['platform_id']  = $platform_id;
				$tmp['browser_id']   = $browser_id;

				$lookup = $this->iplookup($key->ip);

				$tmp['countryName'] = $lookup['countryName'];
				$tmp['stateAbbr']   = $lookup['stateAbbr'];
				$tmp['cityName']    = $lookup['cityName'];
				$tmp['zip']    		= $lookup['cityAbbr'];
			}

			$tmp['date'] 	   = $start_date;
			$tmp['ip']    	   = $key->ip;
			$tmp['date_id']	   = $dt_model->id;

			$tmp['created_at'] = $dt;
			$tmp['updated_at'] = $dt;

			$user_arr[] = $tmp;
		}

		if (isset($is_logged_out)) {
			foreach (array_chunk($user_arr,10000) as $t) {
				UniqueLoggedOutUsersPerDay::insert($t);
			}
		}else{
			foreach (array_chunk($user_arr,10000) as $t) {
				UniqueLoggedInUsersPerDay::insert($t);
			}
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'saveLoggedInUsersForEachDay'.$check, 'done', 7);
	}

	public function saveLoggedInUsersForEachDayRunDaily($is_logged_out = NULL){

		if (isset($is_logged_out)) {
			$check = 'true';
		}else{
			$check = '';
		}

		if (Cache::has( env('ENVIRONMENT') .'_'. 'saveLoggedInUsersForEachDayRunDaily'.$check)) {

    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'saveLoggedInUsersForEachDayRunDaily'.$check);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'saveLoggedInUsersForEachDayRunDaily'.$check, 'in_progress', 10);

		$today = Carbon::today()->toDateString();

		$dates = DatesModel::on('bk-log')->orderBy('id', 'desc')
										->where('date', '!=', $today)
										->get();

		$date = NULL;
		foreach ($dates as $key) {
			if (isset($is_logged_out)) {
				$tmp = UniqueLoggedOutUsersPerDay::on('bk-log')
											->where('date_id',  $key->id)
											->first();
			}else{
				$tmp = UniqueLoggedInUsersPerDay::on('bk-log')
											->where('date_id',  $key->id)
											->first();
			}

			if (isset($tmp)) {
				continue;
			}else{
				$date = $key->date;
				break;
			}
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'saveLoggedInUsersForEachDayRunDaily'.$check, 'done', 7);
		if (isset($date)) {
			return $this->saveLoggedInUsersForEachDay($is_logged_out, $date);
		}else{
			return "nothing to run";
		}
	}

	//  overview_reporting_unique_visits
	public function generateOverviewReportingUniqueVisit(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'generateOverviewReportingUniqueVisit')) {

    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'generateOverviewReportingUniqueVisit');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'generateOverviewReportingUniqueVisit', 'in_progress', 10);

		$today = Carbon::today()->toDateString();

		$dates = DatesModel::on('bk-log')->orderBy('id', 'desc')
										->where('date', '!=', $today)
										->get();

		$date 	 = NULL;
		$date_id = NULL;
		foreach ($dates as $key) {

			$tmp = OverviewReportingUniqueVisit::on('bk-log')
											->where('date_id',  $key->id)
											->first();
			if (isset($tmp)) {
				continue;
			}else{
				$date = $key->date;
				$date_id = $key->id;
				break;
			}
		}


		$start_date = $date;
		$qry = DB::connection('bk-log')->table(DB::raw("(
														Select user_id, date_id
														from plexuss_logging.unique_logged_in_users_per_days
														where date_id = (Select id from plexuss_logging.dates where date = '".$start_date."')
													) as uliupd"))
									   ->join("plexuss.users as u", 'u.id', '=', 'uliupd.user_id')
									   ->join(DB::raw("(
													Select user_id, count(*) as cnt from
														(	Select distinct user_id, date_id
															from plexuss_logging.unique_logged_in_users_per_days
															where date_id <= (Select id from plexuss_logging.dates where date = '".$start_date."')
													) uliupd_cnt_tmp
													group by user_id
												) as uliupd_cnt"), 'uliupd.user_id', '=', 'uliupd_cnt.user_id')

									   ->where('u.is_plexuss', 0)
									   ->where('u.is_ldy', 0)

									   ->where('u.is_organization', 0)
									   ->where('u.is_agency', 0)

									   ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
									   ->where('u.email', 'NOT LIKE', '%test%')
									   ->where('u.fname', 'NOT LIKE', '%test%')
									   ->where('u.email', 'NOT LIKE', '%nrccua%')
									   ->orderBy('cnt', 'desc')
									   ->select('uliupd.user_id', 'cnt', 'u.country_id')
									   ->get();


		$intl_users = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $start_date])
													 ->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = users.id))")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('is_plexuss', 0)
													 ->where('is_organization', 0)
									   				 ->where('is_agency', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$us_users = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $start_date])
													 ->whereRaw("not exists (select user_id from country_conflicts where user_id = users.id)")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('country_id', 1)
													 ->where('is_plexuss', 0)
													 ->where('is_organization', 0)
									   				 ->where('is_agency', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;
		$us_arr = array();
		$us_arr['date'] 	   = $start_date;
		$us_arr['date_id']	   = $date_id;
		$us_arr['total_users'] = $us_users;
		$us_arr['type']        = 1;
		$us_arr['one'] 	 	 = 0;
		$us_arr['two'] 	 	 = 0;
		$us_arr['three'] 	 = 0;
		$us_arr['four'] 	 = 0;
		$us_arr['five'] 	 = 0;
		$us_arr['six'] 	 	 = 0;
		$us_arr['seven'] 	 = 0;
		$us_arr['eight'] 	 = 0;
		$us_arr['nine'] 	 = 0;
		$us_arr['ten_plus']  = 0;

		$intl_arr = array();
		$intl_arr['date'] 	     = $start_date;
		$intl_arr['date_id']	 = $date_id;
		$intl_arr['total_users'] = $intl_users;
		$intl_arr['type']        = 2;
		$intl_arr['one'] 	 = 0;
		$intl_arr['two'] 	 = 0;
		$intl_arr['three'] 	 = 0;
		$intl_arr['four'] 	 = 0;
		$intl_arr['five'] 	 = 0;
		$intl_arr['six'] 	 = 0;
		$intl_arr['seven'] 	 = 0;
		$intl_arr['eight'] 	 = 0;
		$intl_arr['nine'] 	 = 0;
		$intl_arr['ten_plus'] = 0;

		foreach ($qry as $key) {
			if (isset($key->country_id) && $key->country_id == 1) {
				if ($key->cnt == 1) {
					$us_arr['one']++;
				}elseif ($key->cnt == 2) {
					$us_arr['two']++;
				}elseif ($key->cnt == 3) {
					$us_arr['three']++;
				}elseif ($key->cnt == 4) {
					$us_arr['four']++;
				}elseif ($key->cnt == 5) {
					$us_arr['five']++;
				}elseif ($key->cnt == 6) {
					$us_arr['six']++;
				}elseif ($key->cnt == 7) {
					$us_arr['seven']++;
				}elseif ($key->cnt == 8) {
					$us_arr['eight']++;
				}elseif ($key->cnt == 9) {
					$us_arr['nine']++;
				}elseif ($key->cnt >= 10) {
					$us_arr['ten_plus']++;
				}
			}else{
				if ($key->cnt == 1) {
					$intl_arr['one']++;
				}elseif ($key->cnt == 2) {
					$intl_arr['two']++;
				}elseif ($key->cnt == 3) {
					$intl_arr['three']++;
				}elseif ($key->cnt == 4) {
					$intl_arr['four']++;
				}elseif ($key->cnt == 5) {
					$intl_arr['five']++;
				}elseif ($key->cnt == 6) {
					$intl_arr['six']++;
				}elseif ($key->cnt == 7) {
					$intl_arr['seven']++;
				}elseif ($key->cnt == 8) {
					$intl_arr['eight']++;
				}elseif ($key->cnt == 9) {
					$intl_arr['nine']++;
				}elseif ($key->cnt >= 10) {
					$intl_arr['ten_plus']++;
				}
			}
		}

		OverviewReportingUniqueVisit::updateOrCreate($us_arr,  $us_arr);
		OverviewReportingUniqueVisit::updateOrCreate($intl_arr,  $intl_arr);

		Cache::put( env('ENVIRONMENT') .'_'. 'generateOverviewReportingUniqueVisit', 'done', 7);

		return "success";
	}

	// overview_reporting_monetizations
	public function generateOverviewReportingMonetization(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'generateOverviewReportingMonetization')) {

    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'generateOverviewReportingMonetization');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'generateOverviewReportingMonetization', 'in_progress', 10);

		$today = Carbon::today()->toDateString();

		$dates = DatesModel::on('bk-log')->orderBy('id', 'desc')
										->where('date', '!=', $today)
										->get();

		$date 	 = NULL;
		$date_id = NULL;
		foreach ($dates as $key) {

			$tmp = OverviewReportingMonetization::on('bk-log')
											->where('date_id',  $key->id)
											->first();
			if (isset($tmp)) {
				continue;
			}else{
				$date = $key->date;
				$date_id = $key->id;
				break;
			}
		}

		$start_date = $date;
		$intl_users = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $start_date])
													 ->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = users.id))")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('is_plexuss', 0)
													 ->where('is_organization', 0)
													 ->where('is_agency', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$us_users = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $start_date])
													 ->whereRaw("not exists (select user_id from country_conflicts where user_id = users.id)")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('country_id', 1)
													 ->where('is_plexuss', 0)
													 ->where('is_organization', 0)
													 ->where('is_agency', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;


		$qry = DB::connection('bk-log')->table('plexuss_logging.unique_logged_in_users_per_days as uliupd')
									   ->join('plexuss.users as u', 'u.id', '=', 'uliupd.user_id')

									   ->where('uliupd.date', $start_date)
									   ->where('u.is_plexuss', 0)
									   ->where('u.is_ldy', 0)
									   ->where('u.is_organization', 0)
									   ->where('u.is_agency', 0)

									   ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
									   ->where('u.email', 'NOT LIKE', '%test%')
									   ->where('u.fname', 'NOT LIKE', '%test%')
									   ->where('u.email', 'NOT LIKE', '%nrccua%')

									   ->groupBy('u.id')

									   ->select('u.id as user_id', 'u.country_id')
									   ->get();

		$us_arr = array();
		$us_arr['date'] 	   = $start_date;
		$us_arr['date_id']	   = $date_id;
		$us_arr['total_users'] = $us_users;
		$us_arr['type']        = 1;
		$us_arr['zero'] = 0;
		$us_arr['zero_dollar'] = 0;
		$us_arr['one_five'] = 0;
		$us_arr['ten_twenty'] = 0;
		$us_arr['twenty_fifty'] = 0;
		$us_arr['fifty_499'] = 0;
		$us_arr['five_hundred_plus'] = 0;

		$intl_arr = array();
		$intl_arr['date'] 	   	 = $start_date;
		$intl_arr['date_id']	 = $date_id;
		$intl_arr['total_users'] = $intl_users;
		$intl_arr['type']        = 2;
		$intl_arr['zero'] = 0;
		$intl_arr['zero_dollar'] = 0;
		$intl_arr['one_five'] = 0;
		$intl_arr['ten_twenty'] = 0;
		$intl_arr['twenty_fifty'] = 0;
		$intl_arr['fifty_499'] = 0;
		$intl_arr['five_hundred_plus'] = 0;

		$ro = RevenueOrganization::on('bk')->orderBy('priority', 'ASC')
												   ->where('active', 1)
												   ->get();
		$tmp_date = Carbon::parse($start_date);
		$tmp_date = $tmp_date->addDay(1);
		$end_date = $tmp_date->toDateString();

		$bc = new BetaUserController;
		foreach ($qry as $key) {

			$cnt = 0;
			foreach ($ro as $k) {
				$cnt += (int)$bc->getIndividualCompanyRev($k->name, $start_date, $end_date, $key->user_id);
			}

			if ($key->country_id == 1) {

				if ($cnt <= 0) {
					$us_arr['zero'] += $cnt;
				}elseif ($cnt > 0 AND $cnt <= 1) {
					$us_arr['zero_dollar'] += $cnt;
				}elseif ($cnt > 1 AND $cnt <= 5) {
					$us_arr['one_five'] += $cnt;
				}elseif ($cnt > 5 AND $cnt <= 20) {
					$us_arr['ten_twenty'] += $cnt;
				}elseif ($cnt > 20 AND $cnt <= 50) {
					$us_arr['twenty_fifty'] += $cnt;
				}elseif ($cnt > 50 AND $cnt <= 499) {
					$us_arr['fifty_499'] += $cnt;
				}elseif ($cnt >= 500) {
					$us_arr['five_hundred_plus'] += $cnt;
				}
			}else{

				if ($cnt <= 0) {
					$intl_arr['zero'] += $cnt;
				}elseif ($cnt > 0 AND $cnt <= 1) {
					$intl_arr['zero_dollar'] += $cnt;
				}elseif ($cnt > 1 AND $cnt <= 5) {
					$intl_arr['one_five'] += $cnt;
				}elseif ($cnt > 5 AND $cnt <= 20) {
					$intl_arr['ten_twenty'] += $cnt;
				}elseif ($cnt > 20 AND $cnt <= 50) {
					$intl_arr['twenty_fifty'] += $cnt;
				}elseif ($cnt > 50 AND $cnt <= 499) {
					$intl_arr['fifty_499'] += $cnt;
				}elseif ($cnt >= 500) {
					$intl_arr['five_hundred_plus'] += $cnt;
				}
			}
		}

		OverviewReportingMonetization::on('log')->updateOrCreate($us_arr, $us_arr);
		OverviewReportingMonetization::on('log')->updateOrCreate($intl_arr, $intl_arr);

		Cache::put( env('ENVIRONMENT') .'_'. 'generateOverviewReportingMonetization', 'done', 7);

		return "success";
	}

	// overview_reporting_generals
	public function generateOverviewReportingGeneral(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'generateOverviewReportingGeneral')) {

    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'generateOverviewReportingGeneral');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'generateOverviewReportingGeneral', 'in_progress', 10);

		$today = Carbon::today()->toDateString();

		$dates = DatesModel::on('bk-log')->orderBy('id', 'desc')
										->where('date', '!=', $today)
										->get();

		$date 	 = NULL;
		$date_id = NULL;
		foreach ($dates as $key) {

			$tmp = OverviewReportingGeneral::on('bk-log')
										   ->where('date_id',  $key->id)
										   ->first();
			if (isset($tmp)) {
				continue;
			}else{
				$date = $key->date;
				$date_id = $key->id;
				break;
			}
		}

		$start_date = $date;
		$intl_users = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $start_date])
													 ->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = users.id))")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('is_plexuss', 0)
													 ->where('is_organization', 0)
													 ->where('is_agency', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$us_users = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $start_date])
													 ->whereRaw("not exists (select user_id from country_conflicts where user_id = users.id)")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('country_id', 1)
													 ->where('is_plexuss', 0)
													 ->where('is_organization', 0)
													 ->where('is_agency', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;


		$thirty = DB::connection('bk-log')->table('plexuss.users as u')
						 		->join('plexuss.scores as s', 'u.id', '=', 's.user_id')

								->where('u.is_ldy', 0)
								->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
								->where('u.email', 'NOT LIKE', '%test%')
								->where('u.fname', 'NOT LIKE', '%test%')
								->where('u.email', 'NOT LIKE', '%nrccua%')

								->where('u.is_plexuss', 0)
								->where('u.is_organization', 0)
								->where('u.is_agency', 0)
								->whereNotNull('u.address')
								->where(DB::raw('length(u.address)'), '>=', 3)
								->whereNotNull('u.zip')
								->whereIn('u.gender', array('m', 'f'))
								->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
								->join(DB::raw("(SELECT
												user_id
												FROM
												unique_logged_in_users_per_days as tmp
												join dates as dt on (dt.id = tmp.date_id)
												WHERE
												dt.date BETWEEN '".$start_date."' and '".$start_date."'
												GROUP BY user_id) as tmpTable"), 'tmpTable.user_id', '=', 'u.id')


							 	->select('u.id as user_id', 'u.country_id')
							 	->groupBy('u.id')
							 	->get();

		$thirty_us_cnt = 0;
		$thirty_intl_cnt = 0;

		foreach ($thirty as $key) {
	 		if (isset($key->country_id) && $key->country_id == 1) {
	 			$thirty_us_cnt++;
	 		}else{
	 			$thirty_intl_cnt++;
	 		}
	 	}

	 	$thirty_public_profile = DB::connection('bk-log')->table('plexuss.users as u')
						 		->join('plexuss.scores as s', 'u.id', '=', 's.user_id')

								->where('u.is_ldy', 0)
								->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
								->where('u.email', 'NOT LIKE', '%test%')
								->where('u.fname', 'NOT LIKE', '%test%')
								->where('u.email', 'NOT LIKE', '%nrccua%')

								->where('u.is_plexuss', 0)
								->where('u.is_organization', 0)
								->where('u.is_agency', 0)
								->whereNotNull('u.address')
								->where(DB::raw('length(u.address)'), '>=', 3)
								->whereNotNull('u.zip')
								->whereIn('u.gender', array('m', 'f'))
								->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
								->join(DB::raw("(SELECT
												user_id
												FROM
												unique_logged_in_users_per_days as tmp
												join dates as dt on (dt.id = tmp.date_id)
												WHERE
												dt.date BETWEEN '".$start_date."' and '".$start_date."'
												GROUP BY user_id) as tmpTable"), 'tmpTable.user_id', '=', 'u.id')

								->join('plexuss.public_profile_claim_to_fame as ppctf', 'ppctf.user_id', '=', 'u.id')
								->whereNotNull('ppctf.description')
								->where(function($q){
									$q->orWhereNotNull('ppctf.youtube_url')
									  ->orWhereNotNull('ppctf.vimeo_url');
								})

								->join('plexuss.objectives as ob', 'ob.user_id', '=', 'u.id')
								->join('plexuss.public_profile_skills as pps', 'pps.user_id', '=', 'u.id')
								->whereNotNull('pps.group')
								->whereNotNull('pps.position')
								->whereNotNull('pps.awards')

								->join('plexuss.public_profile_projects_and_publications as pppap', 'pppap.user_id', '=', 'u.id')
								->whereNotNull('pppap.title')
								->whereNotNull('pppap.url')


								->join('plexuss.likes_tally as lt', 'lt.user_id', '=', 'u.id')

							 	->select('u.id as user_id', 'u.country_id')
							 	->groupBy('u.id')
							 	->get();

		$thirty_public_profile_us_cnt = 0;
		$thirty_public_profile_intl_cnt = 0;

		foreach ($thirty_public_profile as $key) {
	 		if (isset($key->country_id) && $key->country_id == 1) {
	 			$thirty_public_profile_us_cnt++;
	 		}else{
	 			$thirty_public_profile_intl_cnt++;
	 		}
	 	}

	 	$thirty_completed_app = DB::connection('bk-log')->table('plexuss.users as u')
						 		->join('plexuss.scores as s', 'u.id', '=', 's.user_id')
						 		->join('plexuss.users_custom_questions as ucq', 'ucq.user_id', 'u.id')

						 		->where('ucq.application_state', 'submit')
								->where('u.is_ldy', 0)
								->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
								->where('u.email', 'NOT LIKE', '%test%')
								->where('u.fname', 'NOT LIKE', '%test%')
								->where('u.email', 'NOT LIKE', '%nrccua%')

								->where('u.is_plexuss', 0)
								->where('u.is_organization', 0)
								->where('u.is_agency', 0)
								->whereNotNull('u.address')
								->where(DB::raw('length(u.address)'), '>=', 3)
								->whereNotNull('u.zip')
								->whereIn('u.gender', array('m', 'f'))
								->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
								->join(DB::raw("(SELECT
												user_id
												FROM
												unique_logged_in_users_per_days as tmp
												join dates as dt on (dt.id = tmp.date_id)
												WHERE
												dt.date BETWEEN '".$start_date."' and '".$start_date."'
												GROUP BY user_id) as tmpTable"), 'tmpTable.user_id', '=', 'u.id')


							 	->select('u.id as user_id', 'u.country_id')
							 	->groupBy('u.id')
							 	->get();

		$thirty_completed_app_us_cnt = 0;
		$thirty_completed_app_intl_cnt = 0;

		foreach ($thirty_completed_app as $key) {
	 		if (isset($key->country_id) && $key->country_id == 1) {
	 			$thirty_completed_app_us_cnt++;
	 		}else{
	 			$thirty_completed_app_intl_cnt++;
	 		}
	 	}

	 	$thirty_public_profile_completed_app = DB::connection('bk-log')->table('plexuss.users as u')
						 		->join('plexuss.scores as s', 'u.id', '=', 's.user_id')
						 		->join('plexuss.users_custom_questions as ucq', 'ucq.user_id', 'u.id')

						 		->where('ucq.application_state', 'submit')
								->where('u.is_ldy', 0)
								->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
								->where('u.email', 'NOT LIKE', '%test%')
								->where('u.fname', 'NOT LIKE', '%test%')
								->where('u.email', 'NOT LIKE', '%nrccua%')

								->where('u.is_plexuss', 0)
								->where('u.is_organization', 0)
								->where('u.is_agency', 0)
								->whereNotNull('u.address')
								->where(DB::raw('length(u.address)'), '>=', 3)
								->whereNotNull('u.zip')
								->whereIn('u.gender', array('m', 'f'))
								->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
								->join(DB::raw("(SELECT
												user_id
												FROM
												unique_logged_in_users_per_days as tmp
												join dates as dt on (dt.id = tmp.date_id)
												WHERE
												dt.date BETWEEN '".$start_date."' and '".$start_date."'
												GROUP BY user_id) as tmpTable"), 'tmpTable.user_id', '=', 'u.id')

								->join('plexuss.public_profile_claim_to_fame as ppctf', 'ppctf.user_id', '=', 'u.id')
								->whereNotNull('ppctf.description')
								->where(function($q){
									$q->orWhereNotNull('ppctf.youtube_url')
									  ->orWhereNotNull('ppctf.vimeo_url');
								})

								->join('plexuss.objectives as ob', 'ob.user_id', '=', 'u.id')
								->join('plexuss.public_profile_skills as pps', 'pps.user_id', '=', 'u.id')
								->whereNotNull('pps.group')
								->whereNotNull('pps.position')
								->whereNotNull('pps.awards')

								->join('plexuss.public_profile_projects_and_publications as pppap', 'pppap.user_id', '=', 'u.id')
								->whereNotNull('pppap.title')
								->whereNotNull('pppap.url')


								->join('plexuss.likes_tally as lt', 'lt.user_id', '=', 'u.id')

							 	->select('u.id as user_id', 'u.country_id')
							 	->groupBy('u.id')
							 	->get();

		$thirty_public_profile_completed_app_us_cnt = 0;
		$thirty_public_profile_completed_app_intl_cnt = 0;

		foreach ($thirty_public_profile_completed_app as $key) {
	 		if (isset($key->country_id) && $key->country_id == 1) {
	 			$thirty_public_profile_completed_app_us_cnt++;
	 		}else{
	 			$thirty_public_profile_completed_app_intl_cnt++;
	 		}
	 	}

		$us_arr = array();
		$us_arr['date'] 	   = $start_date;
		$us_arr['date_id']	   = $date_id;
		$us_arr['total_users'] = $us_users;
		$us_arr['type'] = 1;
		$us_arr['thirty'] = $thirty_us_cnt;
		$us_arr['thirty_public_profile'] = $thirty_public_profile_us_cnt;
		$us_arr['thirty_completed_app'] = $thirty_completed_app_us_cnt;
		$us_arr['thirty_public_profile_completed_app'] = $thirty_public_profile_completed_app_us_cnt;

		$intl_arr = array();
		$intl_arr['date'] 	   	 = $start_date;
		$intl_arr['date_id']	 = $date_id;
		$intl_arr['total_users'] = $intl_users;
		$intl_arr['type'] = 2;
		$intl_arr['thirty'] = $thirty_intl_cnt;
		$intl_arr['thirty_public_profile'] = $thirty_public_profile_intl_cnt;
		$intl_arr['thirty_completed_app'] = $thirty_completed_app_intl_cnt;
		$intl_arr['thirty_public_profile_completed_app'] = $thirty_public_profile_completed_app_intl_cnt;

		OverviewReportingGeneral::on('log')->updateOrCreate($us_arr, $us_arr);
		OverviewReportingGeneral::on('log')->updateOrCreate($intl_arr, $intl_arr);

		Cache::put( env('ENVIRONMENT') .'_'. 'generateOverviewReportingGeneral', 'done', 7);

		return "success";
	}

	public function getOverviewReport(){

		// $start_date = "2019-01-06";
		// $end_date   = "2019-01-06";

		$input = Request::all();
		$start_date = $input['start_date'];
		$end_date   = $input['end_date'];
		$ret = array();

		$general_data = DB::connection('bk-log')->table('overview_reporting_generals as oruv')
									   ->join('dates as  dt', 'dt.id', '=', 'oruv.date_id')
									   ->whereBetween('dt.date', array($start_date, $end_date))
									   ->groupBy('oruv.type')
									   ->selectRaw("`type` ,
													SUM(total_users) as total_users,
													SUM(`thirty`) as thirty,
													SUM(`thirty_public_profile`) as thirty_public_profile,
													SUM(`thirty_completed_app`) as thirty_completed_app,
													SUM(`thirty_public_profile_completed_app`) as thirty_public_profile_completed_app")
									   ->get()->toArray();

		$ret['general_data'] = $general_data;

		$monetizations = DB::connection('bk-log')->table('overview_reporting_monetizations as oruv')
									   ->join('dates as  dt', 'dt.id', '=', 'oruv.date_id')
									   ->whereBetween('dt.date', array($start_date, $end_date))
									   ->groupBy('oruv.type')
									   ->selectRaw("`type` ,
									   				SUM(total_users) as total_users,
													SUM(`zero`) as `zero`,
													SUM(`zero_dollar`) as `zero_dollar`,
													SUM(`one_five`) as `one_five`,
													SUM(`ten_twenty`) as `ten_twenty`,
													SUM(`twenty_fifty`) as `twenty_fifty`,
													SUM(`fifty_499`) as `fifty_499`,
													SUM(`five_hundred_plus`) as `five_hundred_plus`
													")
									   ->get()->toArray();

		$ret['monetizations'] = $monetizations;

		$unique_visits = DB::connection('bk-log')->table('overview_reporting_unique_visits as oruv')
									   ->join('dates as  dt', 'dt.id', '=', 'oruv.date_id')
									   ->whereBetween('dt.date', array($start_date, $end_date))
									   ->groupBy('oruv.type')
									   ->selectRaw("`type` ,
													SUM(total_users) as total_users,
													SUM(one) as one,
													SUM(`two`) as two,
													SUM(`three`) as three,
													SUM(`four`) as four,
													SUM(`five`) as five,
													SUM(`six`) as six,
													SUM(`seven`) as seven,
													SUM(`eight`) as eight,
													SUM(`nine`) as nine,
													SUM(`ten_plus`) as ten_plus")
									   ->get()->toArray();

		$ret['unique_visits'] = $unique_visits;

		return $ret;
	}

	private function thisUpdateOrCreate($table, $attr, $val){
		$qry  = $table::on('bk-log');
		foreach ($attr as $key => $value) {
			$qry = $qry->where($key, $value);
		}

		$qry = $qry->first();

		if (isset($qry)) {
			$update = $table::on('log')
							->where('id', $qry->id)
							->update($val);
			$ret =  $qry;
		}else{
			$save = $table::on('log')
					      ->create($val);
			$ret =  $save;
		}

		return $ret;
	}

	private function setUrlDictionary($url){

		$url = str_replace("https://plexuss.com/", "", $url);
		$url = str_replace("https://www.plexuss.com/", "", $url);

		$qry = TrackingPageUrlDictionary::on('bk-log')->where('url', $url)
												  ->first();

		$ret_id = null;

		if (isset($qry)) {
			$ret_id = $qry->id;
		}else{
			$qry = TrackingPageUrlDictionary::on('bk-log')
			                                ->where('url', 'LIKE', DB::raw('"%{%"'))
			                                ->orderByRaw("LENGTH(url) DESC")
			                                ->get();


			$arr = array();
			foreach ($qry as $key) {
				$tmp_url   = $key->url;
				$to_remove = strstr($tmp_url, '/{');

				$tmp_url  = str_replace($to_remove, "", $tmp_url);

				if (strpos($url, $tmp_url) !== FALSE){
					$ret_id = $key->id;
				}
			}



			if (!isset($ret_id)) {

				$attr = array('url' => $url);
				$val  = array('url' => $url, 'manual' => 1);

				$ret = TrackingPageUrlDictionary::on('log')->updateOrCreate($attr, $val);

				$ret_id = $ret->id;
				// $this->customdd("This url doesnt exists");
				// dd($url);
			}
		}
		return $ret_id;
	}

	private function findDeviceIds($type, $name){

		switch ($type) {
			case 'device':

				$qry = Device::on('bk-log')->where('name', $name)->first();
				if (isset($qry)) {
					return $qry->id;
				}else{
					$tmp = new Device;
					$tmp->setConnection('log');
					$tmp->name = $name;
					$tmp->save();

					return $tmp->id;
				}

				break;

			case 'browser':
				$qry = Browser::on('bk-log')->where('name', $name)->first();
				if (isset($qry)) {
					return $qry->id;
				}else{
					$tmp = new Browser;
					$tmp->setConnection('log');
					$tmp->name = $name;
					$tmp->save();

					return $tmp->id;
				}
				break;

			case 'platform':
				$qry = Platform::on('bk-log')->where('name', $name)->first();
				if (isset($qry)) {
					return $qry->id;
				}else{
					$tmp = new Platform;
					$tmp->setConnection('log');
					$tmp->name = $name;
					$tmp->save();

					return $tmp->id;
				}
				break;

			default:
				# code...
				break;
		}
	}

	public function getSitePerfomanceReport(){

		$input = Request::all();
		$start_date = $input['start_date'];
		$end_date   = $input['end_date'];

		// $start_date = "2018-11-27";
		// $end_date   = "2018-11-27";

		$qry = DB::connection('bk-log')->table('tracking_page_url_counts as tpuc')
				 ->join('tracking_page_urls as tpu', 'tpuc.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
				 ->join('dates as dt', 'dt.id', '=', 'tpuc.date_id')

				 ->whereBetween('dt.date', array($start_date, $end_date))
				 ->where('tpud.tpudt_id', '!=', 3)
				 ->whereNotNull('tpud.title')
				 ->whereNotNull('tpud.sub_title')
				 ->where('tpud.title', '!=', 'App')

				 ->groupBy('tpud.id')
				 ->orderby(DB::raw('tpud.title_rank IS NULL,tpud.title_rank'))
				 ->orderby(DB::raw('tpud.sub_rank IS NULL,tpud.sub_rank'))
				 ->orderBy('cnt', 'DESC')
				 ->select(DB::raw('sum(tpuc.count) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id')
				 ->get();

		$ret = array();

		$qry2 = DB::connection('bk-log')->table('tracking_page_url_dictionary')
									    ->get();

		$tmp = array();

		foreach ($qry2 as $key) {
			if(isset($tmp[$key->title][$key->sub_title])){
				$tmp[$key->title][$key->sub_title][] = $key->url;
			}else{
				$tmp[$key->title][$key->sub_title] = array();
				$tmp[$key->title][$key->sub_title][] = $key->url;
			}
		}

		$ret = $this->calculateSitePrfomanceViewsAndClicks($qry, $ret, $tmp);

		$qry = DB::connection('bk-log')->select("Select sum(cnt) as cnt
												, `title`
												, `sub_title`
												, `tpudt_id`
												, `name` as `fragment_name`
												from (
												    select distinct tpuc.id,
												        tpuc.count as cnt ,
												        `tpud`.`title` ,
												        `tpud`.`sub_title` ,
												        `tpud`.`tpudt_id` ,
												        `f`.`name`
												    from
												        `tracking_page_url_counts` as `tpuc`
												    inner join `dates` as `dt` on `dt`.`id` = `tpuc`.`date_id`
												    inner join `tracking_page_urls` as `tpu` on `tpuc`.`tpu_id` = `tpu`.`id`
												    inner join `tracking_page_url_dictionary_logs` as `tpudl` on `tpudl`.`tpu_id` = `tpu`.`id`
												    inner join `tracking_page_url_dictionary` as `tpud` on `tpud`.`id` = `tpudl`.`tpud_id`
												    inner join `tracking_url_fragment_ids` as `tufi` on `tufi`.`tpu_id` = `tpu`.`id`
												    inner join `fragments` as `f` on `f`.`id` = `tufi`.`fragment_id`
												    where `f`.`active` = 1
												    and `f`.`id` between 1 and 13
												    and `dt`.`date` between '".$start_date ."' and '". $end_date. "'
												    and `tpud`.`tpudt_id` != 3
												    and `tpud`.`title` is not null
												    and `tpud`.`sub_title` is not null
												) tbl1
												group by `name`");
		$college_inner_page_cnt = 0;
		foreach ($qry as $key) {
			if ($key->fragment_name != 'overview') {
				$college_inner_page_cnt += $key->cnt;
			}
			if (isset($ret['College'][$key->fragment_name]['View'])) {
				$ret['College'][$key->fragment_name]['View'] += $key->cnt;
			}else{
				$ret['College'][$key->fragment_name]['View'] = $key->cnt;
			}
			if (!isset($ret['College'][$key->fragment_name]['Click'])) {
				$ret['College'][$key->fragment_name]['Click'] = 0;
			}
		}

		$tmp_college = array();
		isset($ret['College']['Get Recruited']) ? $tmp_college['College']['Get Recruited'] = $ret['College']['Get Recruited'] : NULL;
		isset($ret['College']['Apply now']) ? $tmp_college['College']['Apply now'] = $ret['College']['Apply now'] : NULL;
		isset($ret['College']['Tour']) ? $tmp_college['College']['Tour'] = $ret['College']['Tour'] : NULL;

		$tmp_college['College']['overview']['View'] = $ret['College']['college page']['View']  - $college_inner_page_cnt;
		isset($ret['College']['overview']) ? $tmp_college['College']['overview'] +=  $ret['College']['overview'] : NULL;
		isset($ret['College']['stats']) ? $tmp_college['College']['stats'] = $ret['College']['stats'] : NULL;
		isset($ret['College']['admissions']) ? $tmp_college['College']['admissions'] = $ret['College']['admissions'] : NULL;
		isset($ret['College']['enrollment']) ? $tmp_college['College']['enrollment'] = $ret['College']['enrollment'] : NULL;
		isset($ret['College']['ranking']) ? $tmp_college['College']['ranking'] = $ret['College']['ranking'] : NULL;
		isset($ret['College']['tuition']) ? $tmp_college['College']['tuition'] = $ret['College']['tuition'] : NULL;
		isset($ret['College'][' financial-aid']) ? $tmp_college['College'][' financial-aid'] = $ret['College'][' financial-aid'] : NULL;
		isset($ret['College']['Live News']) ? $tmp_college['College']['Live News'] = $ret['College']['news'] : NULL;

		isset($ret['College']['Live News']) ? $tmp_college['College']['Live News']['View'] += $ret['College']['Live News']['View'] : NULL;
		isset($ret['College']['Current Students']) ? $tmp_college['College']['Current Students'] = $ret['College']['Current Students'] : NULL;
		isset($ret['College']['alumni']) ? $tmp_college['College']['alumni'] = $ret['College']['alumni'] : NULL;
		isset($ret['College']['chat']) ? $tmp_college['College']['chat'] = $ret['College']['chat'] : NULL;

		isset($ret['College']['Undergrad']) ? $tmp_college['College']['Undergrad'] = $ret['College']['Undergrad'] : NULL;
		isset($ret['College']['Grad']) ? $tmp_college['College']['Grad'] = $ret['College']['Grad'] : NULL;
		isset($ret['College']['EPP']) ? $tmp_college['College']['EPP'] = $ret['College']['EPP'] : NULL;

		isset($ret['College']['Search']) ? $tmp_college['College']['Search'] = $ret['College']['Search'] : NULL;
		isset($ret['College']['Ad click']) ? $tmp_college['College']['Ad click'] = $ret['College']['Ad click'] : NULL;
		isset($ret['College']['News clicked']) ? $tmp_college['College']['News clicked'] = $ret['College']['News clicked'] : NULL;
		isset($ret['College']['Programs']) ? $tmp_college['College']['Programs'] = $ret['College']['Programs'] : NULL;

		$ret['College'] = $tmp_college['College'];

		return json_encode($ret);
	}

	public function getSitePerfomanceReportByFilter(){

		$input = Request::all();
		$start_date = $input['start_date'];
		$end_date   = $input['end_date'];

		// $start_date = "2018-11-27";
		// $end_date   = "2018-11-27";

		isset($input['device_id'])    ? $device_id     = $input['device_id']   : NULL;
		isset($input['platform_id'])  ? $platform_id   = $input['platform_id'] : NULL;
		isset($input['browser_id'])   ? $browser_id    = $input['browser_id']  : NULL;
		isset($input['unique_users']) ? $unique_users  = $input['unique_users']  : NULL;
		isset($input['type']) 		  ? $type  = $input['type']  : NULL;

		$ret = array();
		$qry2 = DB::connection('bk-log')->table('tracking_page_url_dictionary')
									    ->get();

		$tmp = array();

		foreach ($qry2 as $key) {
			if(isset($tmp[$key->title][$key->sub_title])){
				$tmp[$key->title][$key->sub_title][] = $key->url;
			}else{
				$tmp[$key->title][$key->sub_title] = array();
				$tmp[$key->title][$key->sub_title][] = $key->url;
			}
		}

		// Begin logged out users
		$qry = DB::connection('rds1-log')->table('tracking_page_url_ip_counts as tpuic')
				 ->join('tracking_page_urls as tpu', 'tpuic.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
				 ->join('dates as dt', 'dt.id', '=', 'tpuic.date_id')

				 ->whereBetween('dt.date', array($start_date, $end_date))
				 ->where('tpud.tpudt_id', '!=', 3)
				 ->where('tpud.title', '!=', 'App')
				 ->whereNotNull('tpud.title')
				 ->whereNotNull('tpud.sub_title')
				 ->groupBy('tpud.id')
				 ->orderBy('cnt', 'DESC');

		if (isset($unique_users)) {
			$qry = $qry->select(DB::raw('sum(DISTINCT tpuic.id) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id');
		}else{
			$qry = $qry->select(DB::raw('sum(tpuic.count) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id');
		}

		if (isset($device_id)) {
			$qry = $qry->join('devices as dv', 'tpuic.device_id', '=', 'dv.id')
					   ->where('dv.id', $device_id);
		}

		if (isset($platform_id)) {
			$qry = $qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
					   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
					   ->where('up.id', $platform_id);
		}

		if (isset($browser_id)) {
			$qry = $qry->join('browsers as br', 'tpuic.browser_id', '=', 'br.id')
					   ->join('unique_browsers as ub', 'ub.id', '=', 'br.ub_id')
					   ->where('ub.id', $browser_id);
		}

		if (isset($type)){			
			switch ($type) {
				case 'desktop':
					$qry = $qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->whereIn('up.id', array(2,3,4,6,7,13,18));
					break;

				case 'mobile_web':
					$qry = $qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->whereNotIn('up.id', array(2,3,4,6,7,13,18));
					break;

				case 'ios':
					$qry = $qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->where('up.id', 5);
					break;

				case 'android':
					$qry = $qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->where('up.id', 1);
					break;
			}
		}
		
		$qry =	$qry->get();

		$ret = $this->calculateSitePrfomanceViewsAndClicks($qry, $ret, $tmp);

		// College fragments begins
		$tmp_qry = DB::connection('rds1-log')->table('tracking_page_url_ip_counts as tpuic')
										   ->join('dates as dt', 'dt.id', '=', 'tpuic.date_id')
										   ->join('tracking_page_urls as tpu', 'tpuic.tpu_id', '=', 'tpu.id')
										   ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
										   ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
										   ->join('tracking_url_fragment_ids as tufi', 'tufi.tpu_id', '=', 'tpu.id')
										   ->join('fragments as f', 'f.id', '=', 'tufi.fragment_id')

										   ->where('f.active', 1)
										   ->whereBetween('f.id', array(1,13))
										   ->whereraw("`dt`.`date` between '".$start_date."' and '".$end_date."'")
										   ->where('tpud.tpudt_id', '!=', 3)
										   ->where('tpud.title', '!=', DB::raw("'App'"))
										   ->whereNotNull('tpud.title')
										   ->whereNotNull('tpud.sub_title');

		if (isset($unique_users)) {
			$tmp_qry = $tmp_qry->select(DB::raw('distinct tpuic.id'), DB::raw('distinct tpuic.id as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id', 'f.name');
		}else{
			$tmp_qry = $tmp_qry->select(DB::raw('distinct tpuic.id'), 'tpuic.count as cnt', 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id', 'f.name');
		}

		if (isset($device_id)) {
			$tmp_qry = $tmp_qry->join('devices as dv', 'tpuic.device_id', '=', 'dv.id')
					   ->where('dv.id', $device_id);
		}

		if (isset($platform_id)) {
			$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
					   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
					   ->where('up.id', $platform_id);
		}

		if (isset($browser_id)) {
			$tmp_qry = $tmp_qry->join('browsers as br', 'tpuic.browser_id', '=', 'br.id')
					   ->join('unique_browsers as ub', 'ub.id', '=', 'br.ub_id')
					   ->where('ub.id', $browser_id);
		}

		if (isset($type)){
			switch ($type) {
				case 'desktop':
					$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->whereIn('up.id', array(2,3,4,6,7,13,18));
					break;

				case 'mobile_web':
					$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->whereNotIn('up.id', array(2,3,4,6,7,13,18));
					break;

				case 'ios':
					$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->where('up.id', 5);
					break;

				case 'android':
					$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->where('up.id', 1);
					break;
			}
		}

		$str = str_replace_array('?', $tmp_qry->getBindings(), $tmp_qry->toSql());
		
		$qry = DB::connection('rds1-log')->select("Select sum(cnt) as cnt
												, `title`
												, `sub_title`
												, `tpudt_id`
												, `name` as `fragment_name`
												from (
												    ".$str."
												) tbl1
												group by `name`");

		$college_inner_page_cnt = 0;
		foreach ($qry as $key) {
			if ($key->fragment_name != 'overview') {
				$college_inner_page_cnt += $key->cnt;
			}
			if (isset($ret['College'][$key->fragment_name]['View'])) {
				$ret['College'][$key->fragment_name]['View'] += $key->cnt;
			}else{
				$ret['College'][$key->fragment_name]['View'] = $key->cnt;
			}
			if (!isset($ret['College'][$key->fragment_name]['Click'])) {
				$ret['College'][$key->fragment_name]['Click'] = 0;
			}
		}

		$tmp_college = array();
		isset($ret['College']['Get Recruited']) ? $tmp_college['College']['Get Recruited'] = $ret['College']['Get Recruited'] : NULL;
		isset($ret['College']['Apply now']) ? $tmp_college['College']['Apply now'] = $ret['College']['Apply now'] : NULL;
		isset($ret['College']['Tour']) ? $tmp_college['College']['Tour'] = $ret['College']['Tour'] : NULL;

		$tmp_college['College']['overview']['View'] = $ret['College']['college page']['View']  - $college_inner_page_cnt;
		isset($ret['College']['overview']) ? $tmp_college['College']['overview'] +=  $ret['College']['overview'] : NULL;
		isset($ret['College']['stats']) ? $tmp_college['College']['stats'] = $ret['College']['stats'] : NULL;
		isset($ret['College']['admissions']) ? $tmp_college['College']['admissions'] = $ret['College']['admissions'] : NULL;
		isset($ret['College']['enrollment']) ? $tmp_college['College']['enrollment'] = $ret['College']['enrollment'] : NULL;
		isset($ret['College']['ranking']) ? $tmp_college['College']['ranking'] = $ret['College']['ranking'] : NULL;
		isset($ret['College']['tuition']) ? $tmp_college['College']['tuition'] = $ret['College']['tuition'] : NULL;
		isset($ret['College'][' financial-aid']) ? $tmp_college['College'][' financial-aid'] = $ret['College'][' financial-aid'] : NULL;
		isset($ret['College']['Live News']) ? $tmp_college['College']['Live News'] = $ret['College']['news'] : NULL;

		isset($ret['College']['Live News']) ? $tmp_college['College']['Live News']['View'] += $ret['College']['Live News']['View'] : NULL;
		isset($ret['College']['Current Students']) ? $tmp_college['College']['Current Students'] = $ret['College']['Current Students'] : NULL;
		isset($ret['College']['alumni']) ? $tmp_college['College']['alumni'] = $ret['College']['alumni'] : NULL;
		isset($ret['College']['chat']) ? $tmp_college['College']['chat'] = $ret['College']['chat'] : NULL;

		isset($ret['College']['Undergrad']) ? $tmp_college['College']['Undergrad'] = $ret['College']['Undergrad'] : NULL;
		isset($ret['College']['Grad']) ? $tmp_college['College']['Grad'] = $ret['College']['Grad'] : NULL;
		isset($ret['College']['EPP']) ? $tmp_college['College']['EPP'] = $ret['College']['EPP'] : NULL;

		isset($ret['College']['Search']) ? $tmp_college['College']['Search'] = $ret['College']['Search'] : NULL;
		isset($ret['College']['Ad click']) ? $tmp_college['College']['Ad click'] = $ret['College']['Ad click'] : NULL;
		isset($ret['College']['News clicked']) ? $tmp_college['College']['News clicked'] = $ret['College']['News clicked'] : NULL;
		isset($ret['College']['Programs']) ? $tmp_college['College']['Programs'] = $ret['College']['Programs'] : NULL;

		$ret['College'] = $tmp_college['College'];
		
		// College fragments ends

		// End logged out users

		// Begin logged in users
		$qry = DB::connection('rds1-log')->table('tracking_page_url_user_counts as tpuuc')
				 ->join('tracking_page_urls as tpu', 'tpuuc.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
				 ->join('dates as dt', 'dt.id', '=', 'tpuuc.date_id')

				 ->whereBetween('dt.date', array($start_date, $end_date))
				 ->where('tpud.tpudt_id', '!=', 3)
				 ->where('tpud.title', '!=', 'App')
				 ->whereNotNull('tpud.title')
				 ->whereNotNull('tpud.sub_title')
				 ->groupBy('tpud.id')
				 ->orderBy('cnt', 'DESC');

		if (isset($unique_users)) {
			$qry = $qry->select(DB::raw('sum(DISTINCT tpuuc.id) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id');
		}else{
			$qry = $qry->select(DB::raw('sum(tpuuc.count) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id');
		}

		if (isset($device_id)) {
			$qry = $qry->join('devices as dv', 'tpuuc.device_id', '=', 'dv.id')
					   ->where('dv.id', $device_id);
		}

		if (isset($platform_id)) {
			$qry = $qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
					   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
					   ->where('up.id', $platform_id);
		}

		if (isset($browser_id)) {
			$qry = $qry->join('browsers as br', 'tpuuc.browser_id', '=', 'br.id')
					   ->join('unique_browsers as ub', 'ub.id', '=', 'br.ub_id')
					   ->where('ub.id', $browser_id);
		}

		if (isset($type)){
			switch ($type) {
				case 'desktop':
					$qry = $qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->whereIn('up.id', array(2,3,4,6,7,13,18));
					break;

				case 'mobile_web':
					$qry = $qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->whereNotIn('up.id', array(2,3,4,6,7,13,18));
					break;

				case 'ios':
					$qry = $qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->where('up.id', 5);
					break;

				case 'android':
					$qry = $qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->where('up.id', 1);
					break;
			}
		}

		$qry =	$qry->get();

		$ret = $this->calculateSitePrfomanceViewsAndClicks($qry, $ret, $tmp);

		// College fragments begins
		$tmp_qry = DB::connection('rds1-log')->table('tracking_page_url_user_counts as tpuuc')
										   ->join('dates as dt', 'dt.id', '=', 'tpuuc.date_id')
										   ->join('tracking_page_urls as tpu', 'tpuuc.tpu_id', '=', 'tpu.id')
										   ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
										   ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
										   ->join('tracking_url_fragment_ids as tufi', 'tufi.tpu_id', '=', 'tpu.id')
										   ->join('fragments as f', 'f.id', '=', 'tufi.fragment_id')

										   ->where('f.active', 1)
										   ->whereBetween('f.id', array(1,13))
										   ->whereraw("`dt`.`date` between '".$start_date."' and '".$end_date."'")
										   ->where('tpud.tpudt_id', '!=', 3)
										   ->where('tpud.title', '!=', DB::raw("'App'"))
										   ->whereNotNull('tpud.title')
										   ->whereNotNull('tpud.sub_title');

		if (isset($unique_users)) {
			$tmp_qry = $tmp_qry->select(DB::raw('distinct tpuuc.id'), DB::raw('distinct tpuuc.id as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id', 'f.name');
		}else{
			$tmp_qry = $tmp_qry->select(DB::raw('distinct tpuuc.id'), 'tpuuc.count as cnt', 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id', 'f.name');
		}

		if (isset($device_id)) {
			$tmp_qry = $tmp_qry->join('devices as dv', 'tpuuc.device_id', '=', 'dv.id')
					   ->where('dv.id', $device_id);
		}

		if (isset($platform_id)) {
			$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
					   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
					   ->where('up.id', $platform_id);
		}

		if (isset($browser_id)) {
			$tmp_qry = $tmp_qry->join('browsers as br', 'tpuuc.browser_id', '=', 'br.id')
					   ->join('unique_browsers as ub', 'ub.id', '=', 'br.ub_id')
					   ->where('ub.id', $browser_id);
		}

		if (isset($type)){
			switch ($type) {
				case 'desktop':
					$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->whereIn('up.id', array(2,3,4,6,7,13,18));
					break;

				case 'mobile_web':
					$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->whereNotIn('up.id', array(2,3,4,6,7,13,18));
					break;

				case 'ios':
					$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->where('up.id', 5);
					break;

				case 'android':
					$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
							   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
							   ->where('up.id', 1);
					break;
			}
		}

		$str = str_replace_array('?', $tmp_qry->getBindings(), $tmp_qry->toSql());

		$qry = DB::connection('rds1-log')->select("Select sum(cnt) as cnt
												, `title`
												, `sub_title`
												, `tpudt_id`
												, `name` as `fragment_name`
												from (
												    ".$str."
												) tbl1
												group by `name`");

		$college_inner_page_cnt = 0;
		foreach ($qry as $key) {
			if ($key->fragment_name != 'overview') {
				$college_inner_page_cnt += $key->cnt;
			}
			if (isset($ret['College'][$key->fragment_name]['View'])) {
				$ret['College'][$key->fragment_name]['View'] += $key->cnt;
			}else{
				$ret['College'][$key->fragment_name]['View'] = $key->cnt;
			}
			if (!isset($ret['College'][$key->fragment_name]['Click'])) {
				$ret['College'][$key->fragment_name]['Click'] = 0;
			}
		}

		$tmp_overview = $ret['College']['college page']['View']  - $college_inner_page_cnt;
		$ret['College']['overview']['View'] += $tmp_overview;
		
		// College fragments ends
		// End logged in users

		return json_encode($ret);
	}

	public function getSitePerfomanceReportDetailed(){

		$input = Request::all();
		$start_date = $input['start_date'];
		$end_date   = $input['end_date'];

		// $start_date = "2018-11-27";
		// $end_date   = "2018-11-27";

		isset($input['device_id'])    ? $device_id     = $input['device_id']   : NULL;
		isset($input['platform_id'])  ? $platform_id   = $input['platform_id'] : NULL;
		isset($input['browser_id'])   ? $browser_id    = $input['browser_id']  : NULL;
		isset($input['unique_users']) ? $unique_users  = $input['unique_users']  : NULL;

		$ret = array();
		$qry2 = DB::connection('bk-log')->table('tracking_page_url_dictionary')
									    ->get();

		$tmp = array();

		foreach ($qry2 as $key) {
			if(isset($tmp[$key->title][$key->sub_title])){
				$tmp[$key->title][$key->sub_title][] = $key->url;
			}else{
				$tmp[$key->title][$key->sub_title] = array();
				$tmp[$key->title][$key->sub_title][] = $key->url;
			}
		}

		// Begin logged out users
		$qry = DB::connection('bk-log')->table('tracking_page_url_ip_counts as tpuic')
				 ->join('tracking_page_urls as tpu', 'tpuic.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
				 ->join('dates as dt', 'dt.id', '=', 'tpuic.date_id')

				 ->whereBetween('dt.date', array($start_date, $end_date))
				 ->where('tpud.tpudt_id', '!=', 3)
				 ->whereNotNull('tpud.title')
				 ->whereNotNull('tpud.sub_title')
				 ->groupBy('tpud.id')
				 ->orderBy('cnt', 'DESC');

		if (isset($unique_users)) {
			$qry = $qry->select(DB::raw('sum(DISTINCT tpuic.id) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id');
		}else{
			$qry = $qry->select(DB::raw('sum(tpuic.count) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id');
		}

		if (isset($device_id)) {
			$qry = $qry->join('devices as dv', 'tpuic.device_id', '=', 'dv.id')
					   ->where('dv.id', $device_id);
		}

		if (isset($platform_id)) {
			$qry = $qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
					   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
					   ->where('up.id', $platform_id);
		}

		if (isset($browser_id)) {
			$qry = $qry->join('browsers as br', 'tpuic.browser_id', '=', 'br.id')
					   ->join('unique_browsers as ub', 'ub.id', '=', 'br.ub_id')
					   ->where('ub.id', $browser_id);
		}

		$qry =	$qry->get();

		$ret = $this->calculateSitePrfomanceViewsAndClicks($qry, $ret, $tmp);

		// College fragments begins
		$tmp_qry = DB::connection('bk-log')->table('tracking_page_url_ip_counts as tpuic')
										   ->join('dates as dt', 'dt.id', '=', 'tpuic.date_id')
										   ->join('tracking_page_urls as tpu', 'tpuic.tpu_id', '=', 'tpu.id')
										   ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
										   ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
										   ->join('tracking_url_fragment_ids as tufi', 'tufi.tpu_id', '=', 'tpu.id')
										   ->join('fragments as f', 'f.id', '=', 'tufi.fragment_id')

										   ->where('f.active', 1)
										   ->whereBetween('f.id', array(1,13))
										   ->whereBetween('dt.date', array($start_date, $end_date))
										   ->where('tpud.tpudt_id', '!=', 3)
										   ->whereNotNull('tpud.title')
										   ->whereNotNull('tpud.sub_title');

		if (isset($unique_users)) {
			$tmp_qry = $tmp_qry->select(DB::raw('distinct tpuic.id'), DB::raw('distinct tpuic.id as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id', 'f.name');
		}else{
			$tmp_qry = $tmp_qry->select(DB::raw('distinct tpuic.id'), 'tpuic.count as cnt', 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id', 'f.name');
		}

		if (isset($device_id)) {
			$tmp_qry = $tmp_qry->join('devices as dv', 'tpuic.device_id', '=', 'dv.id')
					   ->where('dv.id', $device_id);
		}

		if (isset($platform_id)) {
			$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuic.platform_id', '=', 'pt.id')
					   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
					   ->where('up.id', $platform_id);
		}

		if (isset($browser_id)) {
			$tmp_qry = $tmp_qry->join('browsers as br', 'tpuic.browser_id', '=', 'br.id')
					   ->join('unique_browsers as ub', 'ub.id', '=', 'br.ub_id')
					   ->where('ub.id', $browser_id);
		}

		$str = str_replace_array('?', $tmp_qry->getBindings(), $tmp_qry->toSql());

		$qry = DB::connection('bk-log')->select("Select sum(cnt) as cnt
												, `title`
												, `sub_title`
												, `tpudt_id`
												, `name` as `fragment_name`
												from (
												    ".$str."
												) tbl1
												group by `name`");

		foreach ($qry as $key) {
			if (isset($ret['College'][$key->fragment_name]['View'])) {
				$ret['College'][$key->fragment_name]['View'] += $key->cnt;
			}else{
				$ret['College'][$key->fragment_name]['View'] = $key->cnt;
			}
			if (!isset($ret['College'][$key->fragment_name]['Click'])) {
				$ret['College'][$key->fragment_name]['Click'] = 0;
			}
		}
		// College fragments ends

		// End logged out users

		// Begin logged in users
		$qry = DB::connection('bk-log')->table('tracking_page_url_user_counts as tpuuc')
				 ->join('tracking_page_urls as tpu', 'tpuuc.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
				 ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
				 ->join('dates as dt', 'dt.id', '=', 'tpuuc.date_id')

				 ->whereBetween('dt.date', array($start_date, $end_date))
				 ->where('tpud.tpudt_id', '!=', 3)
				 ->whereNotNull('tpud.title')
				 ->whereNotNull('tpud.sub_title')
				 ->groupBy('tpud.id')
				 ->orderBy('cnt', 'DESC');

		if (isset($unique_users)) {
			$qry = $qry->select(DB::raw('sum(DISTINCT tpuuc.id) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id');
		}else{
			$qry = $qry->select(DB::raw('sum(tpuuc.count) as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id');
		}

		if (isset($device_id)) {
			$qry = $qry->join('devices as dv', 'tpuuc.device_id', '=', 'dv.id')
					   ->where('dv.id', $device_id);
		}

		if (isset($platform_id)) {
			$qry = $qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
					   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
					   ->where('up.id', $platform_id);
		}

		if (isset($browser_id)) {
			$qry = $qry->join('browsers as br', 'tpuuc.browser_id', '=', 'br.id')
					   ->join('unique_browsers as ub', 'ub.id', '=', 'br.ub_id')
					   ->where('ub.id', $browser_id);
		}

		$qry =	$qry->get();

		$ret = $this->calculateSitePrfomanceViewsAndClicks($qry, $ret, $tmp);

		// College fragments begins
		$tmp_qry = DB::connection('bk-log')->table('tracking_page_url_user_counts as tpuuc')
										   ->join('dates as dt', 'dt.id', '=', 'tpuuc.date_id')
										   ->join('tracking_page_urls as tpu', 'tpuuc.tpu_id', '=', 'tpu.id')
										   ->join('tracking_page_url_dictionary_logs as tpudl', 'tpudl.tpu_id', '=', 'tpu.id')
										   ->join('tracking_page_url_dictionary as tpud', 'tpud.id', '=', 'tpudl.tpud_id')
										   ->join('tracking_url_fragment_ids as tufi', 'tufi.tpu_id', '=', 'tpu.id')
										   ->join('fragments as f', 'f.id', '=', 'tufi.fragment_id')

										   ->where('f.active', 1)
										   ->whereBetween('f.id', array(1,13))
										   ->whereBetween('dt.date', array($start_date, $end_date))
										   ->where('tpud.tpudt_id', '!=', 3)
										   ->whereNotNull('tpud.title')
										   ->whereNotNull('tpud.sub_title');

		if (isset($unique_users)) {
			$tmp_qry = $tmp_qry->select(DB::raw('distinct tpuuc.id'), DB::raw('distinct tpuuc.id as cnt'), 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id', 'f.name');
		}else{
			$tmp_qry = $tmp_qry->select(DB::raw('distinct tpuuc.id'), 'tpuuc.count as cnt', 'tpud.title', 'tpud.sub_title', 'tpud.tpudt_id', 'f.name');
		}

		if (isset($device_id)) {
			$tmp_qry = $tmp_qry->join('devices as dv', 'tpuuc.device_id', '=', 'dv.id')
					   ->where('dv.id', $device_id);
		}

		if (isset($platform_id)) {
			$tmp_qry = $tmp_qry->join('platforms as pt', 'tpuuc.platform_id', '=', 'pt.id')
					   ->join('unique_platforms as up', 'up.id', '=', 'pt.up_id')
					   ->where('up.id', $platform_id);
		}

		if (isset($browser_id)) {
			$tmp_qry = $tmp_qry->join('browsers as br', 'tpuuc.browser_id', '=', 'br.id')
					   ->join('unique_browsers as ub', 'ub.id', '=', 'br.ub_id')
					   ->where('ub.id', $browser_id);
		}

		$str = str_replace_array('?', $tmp_qry->getBindings(), $tmp_qry->toSql());

		$qry = DB::connection('bk-log')->select("Select sum(cnt) as cnt
												, `title`
												, `sub_title`
												, `tpudt_id`
												, `name` as `fragment_name`
												from (
												    ".$str."
												) tbl1
												group by `name`");

		foreach ($qry as $key) {
			if (isset($ret['College'][$key->fragment_name]['View'])) {
				$ret['College'][$key->fragment_name]['View'] += $key->cnt;
			}else{
				$ret['College'][$key->fragment_name]['View'] = $key->cnt;
			}
			if (!isset($ret['College'][$key->fragment_name]['Click'])) {
				$ret['College'][$key->fragment_name]['Click'] = 0;
			}
		}
		// College fragments ends
		// End logged in users

		return json_encode($ret);
	}

	public function getSitePerfomanceByPlatform(){
		$input = Request::all();
		$start_date = $input['start_date'];
		$end_date   = $input['end_date'];
		$type       = 'all';
		(isset($input['type'])) ? $type = $input['type'] : NULL;

		// $start_date = "2019-02-01";
		// $end_date   = "2019-02-01";
		$ret  = array();

		if ($type == 'all' || $type == 'logged_out') {
			// Begin logged out users
			$qry = DB::connection('bk-log')->table('platform_reportings as pr')
										   ->join('unique_platforms as up', 'pr.platform_id', '=', 'up.id')
										   ->join('dates as dt', 'dt.id', '=', 'pr.date_id')
										   
										   ->whereBetween('dt.date', array($start_date, $end_date))
										   ->where('pr.logged_in', 0)
										   ->select('up.name as platform_name', 'pr.num_users', 'pr.page_views')
										   ->orderby('pr.num_users', 'DESC')
										   ->get();


			foreach ($qry as $key) {
				if (!isset($ret[$key->platform_name])) {
					$ret[$key->platform_name] = array();
					$ret[$key->platform_name]['num_users']  = $key->num_users;
					$ret[$key->platform_name]['page_views'] = $key->page_views;
				}else{
					$ret[$key->platform_name]['num_users']  += $key->num_users;
					$ret[$key->platform_name]['page_views'] += $key->page_views;
				}
			}
		}

		if ($type == 'all' || $type == 'logged_in') {
			// Begin logged in users
			$qry = DB::connection('bk-log')->table('platform_reportings as pr')
										   ->join('unique_platforms as up', 'pr.platform_id', '=', 'up.id')
										   ->join('dates as dt', 'dt.id', '=', 'pr.date_id')
										   
										   ->whereBetween('dt.date', array($start_date, $end_date))
										   ->where('pr.logged_in', 1)
										   ->select('up.name as platform_name', 'pr.num_users', 'pr.page_views')
										   ->orderby('pr.num_users', 'DESC')
										   ->get();


			foreach ($qry as $key) {
				if (!isset($ret[$key->platform_name])) {
					$ret[$key->platform_name] = array();
					$ret[$key->platform_name]['num_users']  = $key->num_users;
					$ret[$key->platform_name]['page_views'] = $key->page_views;
				}else{
					$ret[$key->platform_name]['num_users']  += $key->num_users;
					$ret[$key->platform_name]['page_views'] += $key->page_views;
				}
			}
		}

		return json_encode($ret);
	}

	public function getSitePerfomanceByBrowser(){
		$input = Request::all();
		$start_date = $input['start_date'];
		$end_date   = $input['end_date'];
		$type       = 'all';
		(isset($input['type'])) ? $type = $input['type'] : NULL;

		// $start_date = "2019-02-01";
		// $end_date   = "2019-02-01";
		$ret  = array();

		if ($type == 'all' || $type == 'logged_out') {
			// Begin logged out users
			$qry = DB::connection('bk-log')->table('browser_reportings as pr')
										   ->join('unique_browsers as up', 'pr.browser_id', '=', 'up.id')
										   ->join('dates as dt', 'dt.id', '=', 'pr.date_id')
										   
										   ->whereBetween('dt.date', array($start_date, $end_date))
										   ->where('pr.logged_in', 0)
										   ->select('up.name as browser_name', 'pr.num_users', 'pr.page_views')
										   ->orderby('pr.num_users', 'DESC')
										   ->get();


			foreach ($qry as $key) {
				if (!isset($ret[$key->browser_name])) {
					$ret[$key->browser_name] = array();
					$ret[$key->browser_name]['num_users']  = $key->num_users;
					$ret[$key->browser_name]['page_views'] = $key->page_views;
				}else{
					$ret[$key->browser_name]['num_users']  += $key->num_users;
					$ret[$key->browser_name]['page_views'] += $key->page_views;
				}
			}
		}

		if ($type == 'all' || $type == 'logged_in') {
			// Begin logged in users
			$qry = DB::connection('bk-log')->table('browser_reportings as pr')
										   ->join('unique_browsers as up', 'pr.browser_id', '=', 'up.id')
										   ->join('dates as dt', 'dt.id', '=', 'pr.date_id')
										   
										   ->whereBetween('dt.date', array($start_date, $end_date))
										   ->where('pr.logged_in', 1)
										   ->select('up.name as browser_name', 'pr.num_users', 'pr.page_views')
										   ->orderby('pr.num_users', 'DESC')
										   ->get();


			foreach ($qry as $key) {
				if (!isset($ret[$key->browser_name])) {
					$ret[$key->browser_name] = array();
					$ret[$key->browser_name]['num_users']  = $key->num_users;
					$ret[$key->browser_name]['page_views'] = $key->page_views;
				}else{
					$ret[$key->browser_name]['num_users']  += $key->num_users;
					$ret[$key->browser_name]['page_views'] += $key->page_views;
				}
			}
		}

		return json_encode($ret);
	}

	public function getSitePerfomanceByDevice(){
		$input = Request::all();
		$start_date = $input['start_date'];
		$end_date   = $input['end_date'];
		$type       = 'all';
		(isset($input['type'])) ? $type = $input['type'] : NULL;

		// $start_date = "2019-02-01";
		// $end_date   = "2019-02-01";
		$ret  = array();

		if ($type == 'all' || $type == 'logged_out') {
			// Begin logged out users
			$qry = DB::connection('bk-log')->table('device_reportings as pr')
										   ->join('devices as up', 'pr.device_id', '=', 'up.id')
										   ->join('dates as dt', 'dt.id', '=', 'pr.date_id')
										   
										   ->whereBetween('dt.date', array($start_date, $end_date))
										   ->where('pr.logged_in', 0)
										   ->select('up.name as device_name', 'pr.num_users', 'pr.page_views')
										   ->orderby('pr.num_users', 'DESC')
										   ->get();


			foreach ($qry as $key) {
				if (!isset($ret[$key->device_name])) {
					$ret[$key->device_name] = array();
					$ret[$key->device_name]['num_users']  = $key->num_users;
					$ret[$key->device_name]['page_views'] = $key->page_views;
				}else{
					$ret[$key->device_name]['num_users']  += $key->num_users;
					$ret[$key->device_name]['page_views'] += $key->page_views;
				}
			}
		}

		if ($type == 'all' || $type == 'logged_in') {
			// Begin logged in users
			$qry = DB::connection('bk-log')->table('device_reportings as pr')
										   ->join('devices as up', 'pr.device_id', '=', 'up.id')
										   ->join('dates as dt', 'dt.id', '=', 'pr.date_id')
										   
										   ->whereBetween('dt.date', array($start_date, $end_date))
										   ->where('pr.logged_in', 1)
										   ->select('up.name as device_name', 'pr.num_users', 'pr.page_views')
										   ->orderby('pr.num_users', 'DESC')
										   ->get();


			foreach ($qry as $key) {
				if (!isset($ret[$key->device_name])) {
					$ret[$key->device_name] = array();
					$ret[$key->device_name]['num_users']  = $key->num_users;
					$ret[$key->device_name]['page_views'] = $key->page_views;
				}else{
					$ret[$key->device_name]['num_users']  += $key->num_users;
					$ret[$key->device_name]['page_views'] += $key->page_views;
				}
			}
		}

		return json_encode($ret);
	}

	private function calculateSitePrfomanceViewsAndClicks($qry, $ret, $tmp){
		foreach ($qry as $key) {

			if (!isset($ret[$key->title])) {
				$ret[$key->title] = array();

				$ret[$key->title][$key->sub_title] = array();
				$ret[$key->title][$key->sub_title]['View']  = 0;
				$ret[$key->title][$key->sub_title]['Click'] = 0;
				$ret[$key->title][$key->sub_title]['urls']  = $tmp[$key->title][$key->sub_title];

				if ($key->tpudt_id == 1) {
					$ret[$key->title][$key->sub_title]['View'] = $key->cnt;
				}elseif ($key->tpudt_id == 2) {
					$ret[$key->title][$key->sub_title]['Click'] = $key->cnt;
				}
			}else{
				if (isset($ret[$key->title][$key->sub_title])) {
					if ($key->tpudt_id == 1) {
						$ret[$key->title][$key->sub_title]['View'] += $key->cnt;
					}elseif ($key->tpudt_id == 2) {
						$ret[$key->title][$key->sub_title]['Click'] += $key->cnt;
					}
				}else{
					$ret[$key->title][$key->sub_title] 			= array();
					$ret[$key->title][$key->sub_title]['View']  = 0;
					$ret[$key->title][$key->sub_title]['Click'] = 0;
					$ret[$key->title][$key->sub_title]['urls']  = $tmp[$key->title][$key->sub_title];

					if ($key->tpudt_id == 1) {
						$ret[$key->title][$key->sub_title]['View'] = $key->cnt;
					}elseif ($key->tpudt_id == 2) {
						$ret[$key->title][$key->sub_title]['Click'] = $key->cnt;
					}
				}
			}
		}

		return $ret;
	}

	public function setTrackingFragmentsForCollegePages(){

		$qry = DB::connection('bk-log')->table('tracking_page_urls as  tpu')
									   ->leftjoin('tracking_url_fragment_ids as tufi', 'tpu.id', '=', 'tufi.tpu_id')
									   ->whereNull('tufi.id')
									   ->where(function($q){
									   		$q->orWhere('tpu.url', 'LIKE', 'https://plexuss.com/college/%')
									   		  ->orWhere('tpu.url', 'LIKE', 'https://www.plexuss.com/college/%');
									   })

									   ->take(100)
									   ->select('tpu.*')
									   ->orderBy(DB::raw("RAND()"))
									   ->get();


		foreach ($qry as $key) {
			$url = str_replace("https://plexuss.com/college/", "", $key->url);
			$url = str_replace("https://www.plexuss.com/college/", "", $url);

			$url_arr = explode("/", $url);

			if (!isset($url_arr[1]) || empty($url_arr[1])) {
				$fragment_id = 20;
			}else{
				$attr = array('name' => $url_arr[1]);
				$val  = array('name' => $url_arr[1]);

				$ret = Fragment::on('log')->updateOrCreate($attr, $val);
				$fragment_id = $ret->id;
			}


			$college = College::on('bk')->where('slug',  $url_arr[0])
										->where('verified', 1)
										->select('id')
										->first();

			if (!isset($college)) {
				continue;
			}

			$attr = array('tpu_id' => $key->id);
			$val  = array('tpu_id' => $key->id, 'fragment_id' => $fragment_id, 'college_id' => $college->id );

			TrackingUrlFragmentId::on('log')->updateOrCreate($attr, $val);
		}
	}

	public function setTrackingFragmentsForCollegeMajors(){

		$qry = DB::connection('bk-log')->table('tracking_page_urls as  tpu')
									   ->leftjoin('tracking_url_fragment_ids as tufi', 'tpu.id', '=', 'tufi.tpu_id')
									   ->whereNull('tufi.id')
									   ->where(function($q){
									   		$q->orWhere('tpu.url', 'LIKE', 'https://plexuss.com/college-majors/%')
									   		  ->orWhere('tpu.url', 'LIKE', 'https://www.plexuss.com/college-majors/%');
									   })

									   ->take(100)
									   ->select('tpu.*')
									   ->orderBy(DB::raw("RAND()"))
									   ->get();


		foreach ($qry as $key) {
			$url = str_replace("https://plexuss.com/college-majors/", "", $key->url);
			$url = str_replace("https://www.plexuss.com/college-majors/", "", $url);

			$url_arr = explode("/", $url);


			$attr = array('name' => $url_arr[0]);
			$val  = array('name' => $url_arr[0]);

			$ret = Fragment::on('log')->updateOrCreate($attr, $val);
			$fragment_id = $ret->id;


			$attr = array('tpu_id' => $key->id);
			$val  = array('tpu_id' => $key->id, 'fragment_id' => $fragment_id);

			TrackingUrlFragmentId::on('log')->updateOrCreate($attr, $val);
		}
	}

	public function setTrackingModal($modal_name, $trigged_action){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$page = url()->previous();

		$agent = new Agent();
		$browser = $agent->browser();
		$version = $agent->version( $browser );

		$browser = $browser. " ". $version;


		$platform = $agent->platform();
		$version = $agent->version( $platform );

		$platform = $platform. " ".  $version;


		$device = $agent->isMobile();

		if ( $device ) {
			$device = $agent->device();
		}else {
			$device = "Desktop";
		}


		$device_id   = $this->findDeviceIds('device',   $device);
		$platform_id = $this->findDeviceIds('platform', $platform);
		$browser_id  = $this->findDeviceIds('browser',  $browser);

		$tm = new TrackingModal;
		$tm->setConnection('log');

		$tm->user_id 		= $data['user_id'];
		$tm->page    		= $page;
		$tm->modal_name 	= $modal_name;
		$tm->trigged_action = $trigged_action;
		$tm->device_id		= $device_id;
		$tm->platform_id	= $platform_id;
		$tm->browser_id		= $browser_id;

		$tm->save();
	}
}
