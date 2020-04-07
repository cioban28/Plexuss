<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use GeoIp2\Database\Reader;
use Carbon\Carbon;
use Hashids\Hashids;

use mikehaertl\wkhtmlto\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Request, DB, Session, finfo, DateTime, DateTimeZone, AWS, Validator;
use App\RightHandsideLog, App\User, App\RecruitmentTag, App\RecruitmentTagsCronJob, App\MicrosoftImageQuery, App\CollegeCampaign, App\Aor, App\AorCollege;
use App\Recruitment, App\Organization, App\OrganizationBranch, App\OrganizationPortal, App\OrganizationBranchPermission, App\SettingNotificationLog;
use App\ZipCodes, App\CollegePaidHandshakeLog, App\MessageTemplate, App\CollegeMessageLog, App\CollegeMessageThreadMembers, App\CollegeMessageThreads;

use App\OrgSavedAttachment, App\Priority, App\TrackingPage, App\PickACollegeView, App\PrescreenedUser, App\RedisUserList, App\CovetedUser, App\AdClick;
use App\ListUser, App\CollegeCampaignStudent, App\ApplicationEmailSuppresion, App\UsersAppliedColleges, App\UsersCustomQuestion, App\Agency, App\AgencyPermission, App\Country, App\InternalCollegeContactInfo, App\MobileDeviceToken, App\AdRedirectCampaign, App\CollegeRecommendationFilters, App\College;

use App\PublicProfileClaimToFame, App\UserEducation, App\Objective, App\Occupation, App\PublicProfileSkills, App\PublicProfileProjectsAndPublications, App\Score, App\Transcript, App\ScholarshipsUserApplied, App\UsersAddtlInfo;

use App\EmailSuppressionList, App\UsersClusterLog;

use App\Http\Controllers\WatsonController, App\Http\Controllers\TwilioController, App\Http\Controllers\DistributionController;
use App\Http\Controllers\MandrillAutomationController;

use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\Redis;
use Spatie\ImageOptimizer\OptimizerChainFactory;

use App\PassthroughCustomQuestion;
use App\PassthroughCustomQuestionsAcceptedAnswers, App\UsersCompletionTimestamp, App\CollegeMessageViewTime;

class Controller extends BaseController{

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $chat_thread_id;
	protected $secs_to_consider_live = 30;
	private $number_of_carousels = 5;

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout() {
		if ( ! is_null( $this->layout ) ) {
			$this->layout = View( $this->layout );
		}

	}
	/**
	 * Find the position of the Xth occurrence of a substring in a string
	 * @param $haystack
	 * @param $needle
	 * @param $number integer > 0
	 * @return int
	 */
	public function strposX($haystack, $needle, $number){
	    if($number == '1'){
	        return strpos($haystack, $needle);
	    }elseif($number > '1'){
	        return strpos($haystack, $needle, $this->strposX($haystack, $needle, $number - 1) + strlen($needle));
	    }else{
	        return error_log('Error: Value for parameter $number is out of range');
	    }
	}


	/**
	 * Find an elem in a multidimensional array
	 * @param $haystack
	 * @param $needle
	 * @param $strict
	 * @return bool
	 */
	public function in_array_multidimensional($needle, $haystack, $strict = true) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_multidimensional($needle, $item, $strict))) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Find the index for a multidimensional array
	 * @param $products
	 * @param $field
	 * @param $value
	 * @return int
	*/
	public function get_index_multidimensional($products, $field, $value){

	   foreach($products as $key => $product)
	   {
	      if ( $product[$field] === $value )
	         return $key;
	   }
	   return false;
	}

	/**
	 * Find the index for a multidimensional array
	 * @param $products
	 * @param $field
	 * @param $value
	 * @return boolean
	*/
	public function get_index_multidimensional_boolean($products, $field, $value){

	   foreach($products as $key => $product)
	   {
	      if ( $product[$field] === $value )
	         return true;
	   }
	   return false;
	}

	/**
	 * sort the array alphabetically, ascending or descending
	 * @param $records              The array that needs to be sorted
	 * @param $field                The field index name that needs to be sorted
	 * @param $reverse              if true, do descending
	 * @return array
	 */
	public function record_sort($records, $field, $reverse=false){
	    $hash = array();

	    foreach($records as $key => $record)
	    {
	        $hash[$key] = $record[$field];
	    }

	    ($reverse)? array_multisort($hash, SORT_DESC, SORT_NUMERIC, $records) : array_multisort($hash, SORT_ASC, SORT_NUMERIC, $records);

	    return $records;
	}

	/**
	 * Get list of cities based on state pass by
	 * @param $stateAbbr
	 * @return json
	 */
	public function getCityByState($stateAbbr = null){
		$zipCodes = new ZipCodes;
		return json_encode($zipCodes->getCityByState($stateAbbr));
	}

	public function getCityByStateFilt(){
		$input = Request::all();
		$zipCodes = new ZipCodes;
		return $zipCodes->getCityByState($input["state_name"]);
	}

	/**
	 * Get the time elapsed
	 * @param $timestamp
	 * @param $full, if set: display full date.
	 * @return Time elapsed since timestamp
	 */
	public function timeElapsed($timestamp, $full = false) {
		$now = new DateTime;
	    $ago = new DateTime($timestamp);
	    $diff = $now->diff($ago);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}


	/**
	 * ip lookup return cit, state, country, latitude, and longitude
	 *
	 * @return view or redirect
	 */
	public function iplookup($this_ip = null) {

		// This creates the Reader object, which should be reused across
		// lookups.
		$reader = new Reader( base_path() . env('GEOLITE') );

		// Replace "city" with the appropriate method for your database, e.g.,
		// "country".

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
		    	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		   if (isset( $_SERVER['REMOTE_ADDR']) ) {
		   		$ip =  $_SERVER['REMOTE_ADDR'];
		   }
		}

		if (isset($this_ip)) {
			$ip = $this_ip;
		}

		$ip_comma = strpos($ip, ",");

		if(isset($ip_comma) && $ip_comma != ''){
			$to_remove = substr($ip, $ip_comma , strlen($ip) - $ip_comma );
			$ip = str_replace($to_remove, '', $ip);
		}

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

		if (!isset($record->country->isoCode) && !isset($record->country->name) && !isset($record->mostSpecificSubdivision->name) && !isset($record->mostSpecificSubdivision->isoCode) &&  !isset($record->city->name) &&
			!isset($record->postal->code) && !isset($record->location->latitude) && !isset($record->location->longitude)
			&& !isset($record->location->timeZone)) {
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
		if ($excp === true) {
			$arr['cityName'] .= '-Forced';
		}
		$arr['cityAbbr'] = $record->postal->code;
		$arr['latitude'] = $record->location->latitude;
		$arr['longitude'] = $record->location->longitude;
		$arr['time_zone'] = $record->location->timeZone;
		$arr['ip'] = $ip;
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

	/**
	 * check for a private ip address
	 *
	 * @return true or false
	 */
	protected function checkForPrivateIP ($ip) {
	    $reserved_ips = array( // not an exhaustive list
	    '167772160'  => 184549375,  /*    10.0.0.0 -  10.255.255.255 */
	    '3232235520' => 3232301055, /* 192.168.0.0 - 192.168.255.255 */
	    '2130706432' => 2147483647, /*   127.0.0.0 - 127.255.255.255 */
	    '2851995648' => 2852061183, /* 169.254.0.0 - 169.254.255.255 */
	    '2886729728' => 2887778303, /*  172.16.0.0 -  172.31.255.255 */
	    '3758096384' => 4026531839, /*   224.0.0.0 - 239.255.255.255 */
	    );

    	$ip_long = sprintf('%u', ip2long($ip));

	    foreach ($reserved_ips as $ip_start => $ip_end)
	    {
	        if (($ip_long >= $ip_start) && ($ip_long <= $ip_end))
	        {
	            return TRUE;
	        }
	    }

    	return FALSE;
	}

	/**
	 * removes an item from an array
	 *
	 * @return arr
	 */
	private function remove_item_from_arr( $array, $item ) {
		$index = array_search($item, $array);

		if ( $index !== false ) {
			unset( $array[$index] );
		}
		return array_values($array);
	}


	/**
	 * Get email provider domain of user's email
	 *
	 * @return string
	 */
	protected function getEmailProviderDomain($email){

		if (!isset($email)) {
			return;
		}

		$provider = substr($email, strpos($email, "@") + 1);

		$ret = '';
		switch ($provider) {
			case 'aol.com':

				$ret = 'https://mail.aol.com';
				break;

			case 'yahoo.com':

				$ret = 'https://mail.yahoo.com';
				break;

			case 'gmail.com':

				$ret = 'https://mail.google.com';
				break;

			case 'comcast.net':

				$ret = 'https://login.comcast.net/login';
				break;

			case 'aim.com':

				$ret = 'http://mail.aim.com';
				break;


			default:
				$ret = 'http://'.$provider;
				break;
		}

		return $ret;
	}


	//*************************** RIGHT HAND SIDE FUNCTIONALITY *****************************///

	/**
	 * get right hand side carousels
	 *
	 * @return true or false
	 */
	public function getRightHandSide ($data) {

		$input = Request::all();

		$carouselsArray = array('near you','news', 'ranking');

		if(isset($input['cityId']) && $input['cityId'] != -1){
			$carouselsArray = array('near you');

			$zip = ZipCodes::where('id', $input['cityId'])->first();

			if($zip){

				$locationArr = array();
				$locationArr['latitude'] = $zip->Latitude;
				$locationArr['longitude'] = $zip->Longitude;
				$locationArr['cityName'] = $zip->CityName;
				$locationArr['stateAbbr'] = $zip->StateAbbr;

				Session::put('userCustomLocationArr', $locationArr);
			}else{
				$carouselsArray = array('near you','news', 'ranking');
			}
		}else{
			$carouselsArray = array('near you','news', 'ranking');
			//$carouselsArray = array('ranking');
		}

		$ret = array();

		while (count($carouselsArray) > 0) {

			//print_r($carouselsArray);

			$ret = array();
			//break if there's no more carousel left out of all available carousels
			if (count($carouselsArray) == 0) {
				break;
			}

			$rnd = mt_rand(0, count($carouselsArray) -1);

			// print_r(" rand is " .$rnd);

			$pickedCarousel = $carouselsArray[$rnd];

			if($pickedCarousel == 'near you'){
				$passed_vars = array();
				$passed_vars = $data;
				/* ***** important to keep the order like this ******/
				$passed_vars['cat_id'] = 1;
				$passed_vars['type'] = 'college';

				$ret['data'] = $this->getRightHandSideCollegesNearYou(array($this->getRightHandSideAlreadySeenTypeIds($passed_vars)));
				$passed_vars['data'] = $ret['data'];
				$this->insertUserLogs($passed_vars);
				//$ret['type'] = 'near you';

				if (empty($ret['data'][0])) {

					$carouselsArray = $this->remove_item_from_arr($carouselsArray, 'near you');
					//$ret['type3'] = 'here';
				}else{
					$ret['type'] = 'near you';
					//$ret['arr'] = $carouselsArray;
					break;
				}

				//$ret['type2'] = $carouselsArray;

			}elseif ($pickedCarousel == 'news') {
				$passed_vars = array();
				$passed_vars = $data;
				/* ***** important to keep the order like this ******/
				$passed_vars['cat_id'] = 2;
				$passed_vars['type'] = 'news';

				$ret['data'] = $this->getRightHandSideLatestNews(array($this->getRightHandSideAlreadySeenTypeIds($passed_vars)));
				$passed_vars['data'] = $ret['data'];

				$this->insertUserLogs($passed_vars);
				//$ret['type'] = 'news';

				if (empty($ret['data'][0])) {
					$carouselsArray =  $this->remove_item_from_arr($carouselsArray, 'news');
					//$ret['type3'] = 'here';
				}else{
					$ret['type'] = 'news';
					//$ret['arr'] = $carouselsArray;
					break;
				}

				//$ret['type2'] = $carouselsArray;

			}elseif ($pickedCarousel == 'ranking') {

				$passed_vars = array();
				$passed_vars = $data;
				/* ***** important to keep the order like this ******/
				$passed_vars['cat_id'] = 3;
				$passed_vars['type'] = 'college';

				$ret['data'] = $this->getRightHandSideOtherRanking(array($this->getRightHandSideAlreadySeenTypeIds($passed_vars)));

				foreach ($ret['data'] as $key) {
					$passed_vars['data'] = $key;
					$this->insertUserLogs($passed_vars);
				}

				if (empty($ret['data'][0])) {
					$carouselsArray =  $this->remove_item_from_arr($carouselsArray, 'ranking');
					//$ret['type3'] = 'here';
				}else{
					$ret['type'] = 'ranking';
					$ret['link'] = '/ranking/categories';
					//$ret['arr'] = $carouselsArray;
					break;
				}

				//$ret['type2'] = $carouselsArray;
				/*
				echo "<pre>";
				print_r($ret);
				echo "</pre>";
				exit();
				*/

			}elseif ($pickedCarousel == 'similar ranking') {
				$ret['data'] = $this->getRightHandSideOtherRanking();
				//$ret['type'] = 'similar ranking';
				$ret['type'] = 'ranking';
			}elseif ($pickedCarousel == 'comparisons') {
				$ret['data'] = $this->getRightHandSideOtherRanking();
				//$ret['type'] = 'comparisons';
				$ret['type'] = 'ranking';
			}
		}
		return $ret;

	}

	private function getRightHandSideCollegesNearYou($filterColleges = null){

		$ret = array();

		if(Session::has('userCustomLocationArr')){
			$locationArr = Session::get('userCustomLocationArr');

		}else{
			$locationArr = $this->iplookup();
		}

		$query = 'slug, school_name as school_name, city, state, ( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(latitude)) ) ) AS distance ';

		$colleges = DB::table('colleges as c')
			->select( DB::raw( 'c.id as college_id, c.id as type_id, cr.plexuss as rank, coi.url as img_url, c.logo_url, '.$query ) )
			->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
			->leftjoin('college_overview_images as coi', 'coi.college_id', '=', 'c.id')
			->where( 'verified', '=', 1 )
			//->where('coi.url', '!=', '')
			->groupby('c.id')
			//->orderBy('distance', 'ASC')
			->take(5)
			->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'))
			->having('distance', '<', 100);

		if ($filterColleges) {

			$colleges = $colleges->whereNotIn('c.id', $filterColleges);
		}

		$colleges = $colleges->get();

		foreach ($colleges as $key) {

			$temp = array();
			$temp['userCityName'] = $locationArr['cityName'];
			$temp['userStateName'] = $locationArr['stateAbbr'];

			$temp['type_id'] = $key->type_id;
			$temp['college_id'] = $key->college_id;
			$temp['city'] = $key->city;
			$temp['state'] = $key->state;
			if($key->rank == ''){
				$temp['rank'] = 'NA';
			}else{
				$temp['rank'] = $key->rank;
			}

			$temp['school_name'] = $key->school_name;
			$temp['distance'] = ceil($key->distance);

			if(isset($key->logo_url)){
				$temp['logo_url'] = $key->logo_url;
			}else{
				$temp['logo_url'] = 'default-missing-college-logo.png';
			}

			$temp['slug'] = $key->slug;

			if($key->img_url ==''){
				$temp['img_url'] = 'no-image-default.png';
			}else{
				$temp['img_url'] = $key->img_url;
			}

			$temp['like_type'] = 'college';
			$temp['like_type_col'] = 'id';
			$temp['like_type_val'] = Crypt::encrypt($key->college_id);

			$ret[] = $temp;
		}

		return $ret;

	}

	private function getRightHandSideLatestNews($filterArticles = null){

		$qry = DB::table('news_articles as na')
					->join('news_subcategories as ns','na.news_subcategory_id', '=', 'ns.id')
					->join('news_categories as nc', 'nc.id', '=', 'ns.news_category_id')
					->select('na.slug as slug', 'na.title', 'nc.slug as catSlug', 'na.img_sm',
						'nc.name as catName' , 'na.img_lg', 'na.created_at', 'na.external_author', 'na.id as news_id', 'na.has_video')
					->whereNotNull('na.content')
					->whereNull('na.basic_content')
					->whereNull('na.premium_content')
					->where('na.news_subcategory_id', '!=', 22)
					->where('na.news_subcategory_id', '!=', 12)
					->where('na.news_subcategory_id', '!=', 11)
					->orderby('created_at', 'DECS');


		if($filterArticles){
			$qry = $qry->whereNotIn('na.id', $filterArticles);
		}

		$qry = $qry->take($this->number_of_carousels)->get();


		$ret = array();

		if(isset($qry)){

			foreach ($qry as $key) {
				$tmp = array();

				$tmp['type_id'] = $key->news_id;
				$tmp['slug'] = $key->slug;
				$tmp['title'] = $key->title;
				$tmp['catSlug'] = $key->catSlug;
				$tmp['catName'] = $key->catName;
				if( isset($key->has_video) && $key->has_video == 1 ){
					$tmp['img'] = $key->img_sm;
				}else{
					$tmp['img'] = $key->img_lg;
				}
				$tmp['date'] = $this->xTimeAgo($key->created_at, date("Y-m-d H:i:s"));
				$tmp['author'] = $key->external_author;
				$tmp['has_video'] = $key->has_video;

				$tmp['like_type'] = 'news_articles';
				$tmp['like_type_col'] = 'id';
				$tmp['like_type_val'] = Crypt::encrypt($key->news_id);

				$ret[] = $tmp;
			}

		}

		return $ret;

	}

	private function getRightHandSideOtherRanking($filterColleges = null){

		$qry = DB::table('lists as li')->join('list_schools as ls', 'li.id', '=', 'ls.lists_id')
			->join('colleges as c', 'c.id', '=', 'ls.colleges_id')
			->leftjoin('colleges_ranking as cr', 'c.id', '=', 'cr.college_id')
			->orderby('ls.lists_id')
			->orderby('ls.order')
			->select('c.id as college_id', 'c.id as type_id', 'c.slug as slug',
			 'c.logo_url', 'cr.plexuss', 'ls.order', 'li.title', 'c.school_name', 'li.image as listImage')
			->where('li.type', 'interesting')
			->where('ls.order', '<', 4)
			->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'));

		if ($filterColleges) {
			$qry = $qry->whereNotIn('ls.colleges_id', $filterColleges);
		}
		$qry = $qry->get();


		$rankingArr = array();
		$ret = array();

		$title = '';


		foreach ($qry as $key) {

			if(count($rankingArr) == 5){
				break;
			}

			if ($title != '' && $key->title != $title) {
				$title = $key->title;
				$rankingArr[] = $ret;
				$ret = array();
			}
			$tmp = array();

			$tmp['college_id'] = $key->college_id;
			$tmp['type_id'] = $key->type_id;

			$tmp['slug'] = $key->slug;
			$tmp['logo_url'] = $key->logo_url;

			if(!isset($key->plexuss)){
				$tmp['rank'] = "NA";
			}else{
				$tmp['rank'] = $key->plexuss;
			}

			if(!isset($key->order)){
				$tmp['order'] = "NA";
			}else{
				$tmp['order'] = $key->order;
			}

			$tmp['title'] = $key->title;

			if($title == ''){
				$title = $key->title;
			}

			$tmp['school_name']= $key->school_name;

			if(isset($key->listImage)){
				$tmp['listImage'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/lists/images/'.$key->listImage;
			}else{
				$tmp['listImage'] = '';
			}

			$tmp['like_type'] = 'lists';
			$tmp['like_type_col'] = 'title';
			$tmp['like_type_val'] = Crypt::encrypt($key->title);

			$ret[] = $tmp;
		}


		// add the last array to the mix if return arr is less than 5
		if (isset($rankingArr) && count($rankingArr) != 5) {
			$rankingArr[] = $ret;
		}


		$ret = $rankingArr;

		return $ret;

	}

	private function getRightHandSideAlreadySeenTypeIds($data){

		$arr = array();

		if($data['cat_id'] != null){

			$tmp_qry = RightHandsideLog::select(DB::raw('DISTINCT type_id, cat_id, type'))
									   ->where('updated_at', '>', Carbon::now()->subDays(7));

			if($data['signed_in'] == 1 ){

				$tmp_qry = $tmp_qry->where('user_id', $data['user_id']);
			}else{
				$tmp_qry = $tmp_qry->where('ip', $data['ip']);
			}

			$tmp_qry = $this->getRawSqlWithBindings($tmp_qry);

			$arr = DB::connection('bk')->table(DB::raw('('.$tmp_qry. ') tbl1'))
									   ->where('cat_id', $data['cat_id'])
									   ->where('type', $data['type'])
									   ->select('type_id')
									   ->pluck('type_id');


			isset($arr[0]) ? $arr = $arr[0] : $arr = NULL;
		}

		return $arr;

	}
	private function insertUserLogs($data){


		foreach ($data['data'] as $key) {


			$attr = array('type' =>$data['type'], 'type_id' => $key['type_id'],
			 'cat_id' => $data['cat_id']);

			$val  = array('type' =>$data['type'], 'type_id' => $key['type_id'],
			 'cat_id' => $data['cat_id'], 'updated_at' => date( "Y-m-d H:i:s" ) );

			if($data['signed_in'] == 1){
				$attr['user_id'] = $data['user_id'];
				$val['user_id'] = $data['user_id'];
				$val['ip'] = $data['ip'];
			}else{
				$attr['ip'] = $data['ip'];
				$val['ip'] = $data['ip'];
				$attr['user_id'] = '';
			}

			$tmp = RightHandsideLog::updateOrCreate($attr, $val);
		}

	}
	//************************* END RIGHT HAND SIDE FUNCTIONALITY ***************************///


	//***************************chat and messsages methods*****************************///

	/**
	 * getChatCnt
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	protected function getChatCnt(){

		$college_id = Session::get('userinfo.school_id');

		if (!isset($college_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		$org_branch_id = Session::get('userinfo.org_branch_id');

		$cnt = DB::connection('rds1')->table('college_message_threads as cmt')
									 ->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
									 ->where('cmt.is_chat' ,1)
									 ->where('cmt.has_text', 0)
									 ->where('cmtm.user_id' , $my_user_id)
									 ->where('cmtm.org_branch_id', $org_branch_id)
									 ->sum('num_unread_msg');

		return $cnt;
	}

	/**
	 * getChatCntTotal
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	protected function getChatCntTotal(){

		$college_id = Session::get('userinfo.school_id');

		if (!isset($college_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		$org_branch_id = Session::get('userinfo.org_branch_id');

		$cnt = DB::connection('rds1')->table('college_message_threads as cmt')
									 ->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
									 ->join('college_message_logs AS cml', 'cml.thread_id', '=', 'cmt.id')
									 ->where('cmt.is_chat' ,1)
									 ->where('cmt.has_text', 0)
									 ->where('cmtm.user_id' , $my_user_id)
									 ->where('cmtm.org_branch_id', $org_branch_id)
									 ->selectRaw('count(DISTINCT cml.id) as cnt')
									 ->first();

		$cnt = $cnt->cnt;

		return $cnt;
	}

	/**
	 * getMessageCnt
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	protected function getMessageCnt($raw_filter_qry = NULL){

		$org_branch_id = Session::get('userinfo.org_branch_id');

		if (!isset($org_branch_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		if (isset($raw_filter_qry)) {
			$raw_filter_qry = $raw_filter_qry->join('college_message_thread_members as cmtm1', 'userFilter.id', '=', 'cmtm1.user_id')
											 ->where('cmtm1.org_branch_id', $org_branch_id)
											 ->groupby('cmtm1.thread_id')
											 ->addSelect('cmtm1.thread_id');

			$raw_filter_qry = $this->getRawSqlWithBindings($raw_filter_qry);
		}

		$cnt = DB::connection('rds1')->table('college_message_threads as cmt')
									 ->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
									 ->join('college_message_logs AS cml', 'cml.thread_id', '=', 'cmt.id')
									 ->where('cmt.is_chat' ,0)
									 ->where('cmt.has_text', 0)
									 ->where('cmtm.user_id' , $my_user_id)
									 ->where('cmtm.org_branch_id', $org_branch_id)
									 ->where('cml.msg', 'NOT LIKE', DB::RAW('"<b>Subject: </b>%"'));

		if (isset($raw_filter_qry)) {
 			$cnt = $cnt->join(DB::raw('('.$raw_filter_qry.')  as t2'), 't2.thread_id' , '=', 'cmtm.thread_id');
 		}
 		$cnt = $cnt->sum('num_unread_msg');

		return $cnt;
	}

	/**
	 * getMessageCntTotal
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	protected function getMessageCntTotal($raw_filter_qry = NULL){

		$org_branch_id = Session::get('userinfo.org_branch_id');

		if (!isset($org_branch_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		// *************** This part is build from the getMessageCnt
		// if (isset($raw_filter_qry)) {
		// 	$raw_filter_qry = $raw_filter_qry->join('college_message_thread_members as cmtm1', 'userFilter.id', '=', 'cmtm1.user_id')
		// 									 ->where('cmtm1.org_branch_id', $org_branch_id)
		// 									 ->groupby('cmtm1.thread_id')
		// 									 ->addSelect('cmtm1.thread_id');

		// 	$raw_filter_qry = $this->getRawSqlWithBindings($raw_filter_qry);
		// }

		$cnt = DB::connection('rds1')->table('college_message_threads as cmt')
									 ->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
									 ->join('college_message_logs AS cml', 'cml.thread_id', '=', 'cmt.id')
									 ->where('cmt.is_chat' ,0)
									 ->where('cmt.has_text', 0)
									 ->where('cmtm.user_id' , $my_user_id)
									 ->where('cmtm.org_branch_id', $org_branch_id)
									 ->where('cml.msg', 'NOT LIKE', DB::RAW('"<b>Subject: </b>%"'))
									 ->selectRaw('count(DISTINCT cml.id) as cnt');


		if (isset($raw_filter_qry)) {
			$raw_filter_qry = $this->getRawSqlWithBindings($raw_filter_qry);
 			$cnt = $cnt->join(DB::raw('('.$raw_filter_qry.')  as t2'), 't2.thread_id' , '=', 'cmtm.thread_id');
 		}

 		$cnt = $cnt->first();

		$cnt = $cnt->cnt;
		return $cnt;
	}

	/**
	 * getTxtCnt
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	protected function getTxtCnt(){

		$org_branch_id = Session::get('userinfo.org_branch_id');

		if (!isset($org_branch_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		$cnt = DB::connection('rds1')->table('college_message_threads as cmt')
									 ->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
									 ->where('cmt.is_chat' ,0)
									 ->where('cmt.has_text', 1)
									 ->where('cmtm.user_id' , $my_user_id)
									 ->where('cmtm.org_branch_id', $org_branch_id)
									 ->sum('num_unread_msg');

		return $cnt;
	}

	/**
	 * getTxtCntTotal
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	protected function getTxtCntTotal(){

		$org_branch_id = Session::get('userinfo.org_branch_id');

		if (!isset($org_branch_id)) {
			return "You are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		$cnt = DB::connection('rds1')->table('college_message_threads as cmt')
									 ->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
									 ->join('college_message_logs AS cml', 'cml.thread_id', '=', 'cmt.id')
									 ->where('cmt.is_chat' ,0)
									 ->where('cmt.has_text', 1)
									 ->where('cmtm.user_id' , $my_user_id)
									 ->where('cmtm.org_branch_id', $org_branch_id)
									 ->where('cml.msg', 'NOT LIKE', DB::RAW('"<b>Subject: </b>%"'))
									 ->selectRaw('count(DISTINCT cml.id) as cnt')
									 ->first();

		$cnt = $cnt->cnt;

		return $cnt;
	}


	protected function getCampaignTotal(){

		$college_id = Session::get('userinfo.school_id');

		if (!isset($college_id)) {
			return "You are not an admin!";
		}

		$cc = CollegeCampaign::on('rds1')->where('college_id', $college_id)
										 ->count('id');

		return $cc;

	}

	/**
	 * getTextMsgCnt
	 *
	 * used in AdminController->index, this method returns the number of "Active" inquiries for a college
	 *
	 * @return (int) (count)
	 */
	// protected function getTextMsgCnt() {
	// 	// this copy from getMessageCnt
	// 	$org_branch_id = Session::get('userinfo.org_branch_id');

	// 	if (!isset($org_branch_id)) {
	// 		return "You are not an admin!";
	// 	}

	// 	$my_user_id = Session::get('userinfo.id');

	// 	$cnt = DB::table('college_message_threads as cmt')
	// 	->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
	// 	->where('cmt.is_chat' , 0)
	// 	->where('cmtm.user_id' , $my_user_id)
	// 	->sum('num_unread_msg');

	// 	return $cnt;
	// }

	/**
	* Returns the list of threads on heart beat.
	*
	*
	*/
	public function showCache() {

		$input =  Request::all();

		if (!isset($input['t']) ) {
			return redirect( '/' );
		}
		if(isset($input['t']) && $input['t'] == "joie3e23riuhneisuio"){

			return Cache::get( env('ENVIRONMENT') .'_'.'college_chat_threads' );

		}
		return redirect( '/' );
	}

	public function showCacheThread($user_id) {

		$input =  Request::all();

		if (!isset($input['t']) ) {
			return redirect( '/' );
		}
		if(isset($input['t']) && $input['t'] == "joie3e23riuhneisuio"){

			$ret = array();

			if (Cache::has(env('ENVIRONMENT') .'_usersTopicsAndMessages_colleges_'. $user_id)) {

				$ret['college'] =  Cache::get(env('ENVIRONMENT') .'_usersTopicsAndMessages_colleges_'. $user_id);

			}

			if (Cache::has(env('ENVIRONMENT') .'_usersTopicsAndMessages_users_'. $user_id)) {

				$ret['user'] =  Cache::get(env('ENVIRONMENT') .'_usersTopicsAndMessages_users_'. $user_id);

			}

			echo "<pre>";
			print_r($ret);
			echo "</pre>";

			exit();

		}

		return redirect( '/' );

	}

	/**
	 * createThread
	 *
	 * get a thread between two users, if already exists, just return the thread_id
	 * @return (int) (thread_id)
	*/
	public function createThread(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$request = Request::all();
		$request['user_id'] = $this->decodeIdForSocial($request['user_id']);

		$thread_already_exists = DB::connection('rds1')->table('college_message_thread_members as cmtm')
											   ->join(DB::raw('( SELECT DISTINCT thread_id FROM college_message_thread_members as cmtm2 WHERE user_id = '.$request['user_id'].' ) as tb1'), 'tb1.thread_id', '=', 'cmtm.thread_id')
											   ->where('user_id', $data['user_id'])
											   ->orderby('cmtm.thread_id', 'DESC')
											   ->select(DB::raw('DISTINCT cmtm.thread_id'))
											   ->first();

		if (isset($thread_already_exists)) {
	   		$college_message_thread_id = $thread_already_exists->thread_id;
	   	}else{
	   		$cmt = new CollegeMessageThreads;
			$cmt->save();
			$college_message_thread_id = $cmt->id;

			$cmtm = new CollegeMessageThreadMembers;
			$cmtm->user_id = $request['user_id'];
			$cmtm->thread_id = $college_message_thread_id;
			$cmtm->save();

			$cmtm = new CollegeMessageThreadMembers;
			$cmtm->user_id = $data['user_id'];
			$cmtm->thread_id = $college_message_thread_id;
			$cmtm->save();

		}

		// $publish_data = array();
		// $publish_data['thread_room'] = $request['user_thread_room'];
		// $publish_data['id'] = $this->hashIdForSocial($college_message_thread_id);
		// Redis::publish('add:messageThread', json_encode($publish_data));
		// $publish_data['thread_room'] = $request['thread_room'];
		// $publish_data['id'] = $this->hashIdForSocial($college_message_thread_id);
		// Redis::publish('add:messageThread', json_encode($publish_data));

		$ret = array();
		$ret['thread_id'] = $this->hashIdForSocial($college_message_thread_id);
		$ret['status']    = "success";

		return json_encode($ret);
	}

	/**
	 * setReadTime
	 *
	 * set the readd time for the last message the user has receivedd.
	 * @return (success)
	*/
	public function setReadTime(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$request = Request::all();

		$now = Carbon::now('UTC');

		$attr = array('msg_id' => $request['msg_id'], 'read_user_id' => $data['user_id']);
		$val  = array('msg_id' => $request['msg_id'], 'read_user_id' => $data['user_id'], 'read_time' => $now);

		CollegeMessageViewTime::updateOrCreate($attr, $val);

		$publish_data = array();
		$publish_data['thread_room'] = $request['thread_room'];
		$publish_data['thread_id'] = $request['thread_id'];
		$publish_data['msg_id'] = $request['msg_id'];
		$publish_data['read_time'] = $now;
		Redis::publish('set:readTime', json_encode($publish_data));

		return response()->json(array('success' => true), 200);
	}

	/**
	 * addUserToThread
	 *
	 * Adding a user to a thread
	 * @return (success)
	*/
	public function addUserToThread(){
		$request = Request::all();

		$user_id   = $this->decodeIdForSocial($request['user_id']);
		$thread_id = $this->decodeIdForSocial($request['thread_id']);

		$attr = array('user_id' => $user_id, 'thread_id' => $thread_id);
		$instance = CollegeMessageThreadMembers::updateOrCreate($attr, $attr);

		$publish_data = array();
		if(isset($instance)){
			$response = CollegeMessageThreadMembers::on('rds1')->where('thread_id', $thread_id)->get();
			if(isset($response)){
				foreach($response as $res){
					$hashUsr = $this->hashIdForSocial($res['user_id']);
					$publish_data['thread_room'] = 'post:room:'.$hashUsr;
					$publish_data['user_id'] = $hashUsr;
					$publish_data['thread_id'] = $this->hashIdForSocial($thread_id);
					Redis::publish('add:threadUser', json_encode($publish_data));
				}
			}
		}

		return response()->json(array('success' => true), 200);
	}

	/**
	 * getMyCounselorThread
	 *
	 * get a thread between the user and my counselor
	 * @return (int) (thread_id)
	*/
	public function getMyCounselorThread(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		// if (Cache::has( env('ENVIRONMENT') .'_'. 'controller_getMyCounselorThread_'.$data['user_id'])) {

	 //      $response = Cache::get( env('ENVIRONMENT') .'_'. 'controller_getMyCounselorThread_'.$data['user_id']);
	 //      return $response;

	 //    }

		$my_counselor_user_id = 1408142;

		$thread_already_exists = DB::connection('rds1')->table('college_message_thread_members as cmtm')
													   ->join(DB::raw('( SELECT DISTINCT thread_id FROM college_message_thread_members as cmtm2 WHERE user_id = '.$my_counselor_user_id.' ) as tb1'), 'tb1.thread_id', '=', 'cmtm.thread_id')
													   ->where('user_id', $data['user_id'])
													   ->orderby('cmtm.thread_id', 'DESC')
													   ->select(DB::raw('DISTINCT cmtm.thread_id'))
													   ->first();

		if (isset($thread_already_exists)) {
	   		$college_message_thread_id = $thread_already_exists->thread_id;
	   	}else{
	   		$cmt = new CollegeMessageThreads;
			$cmt->save();
			$college_message_thread_id = $cmt->id;

			$cmtm = new CollegeMessageThreadMembers;
			$cmtm->user_id = $my_counselor_user_id;
			$cmtm->thread_id = $college_message_thread_id;
			$cmtm->save();

			$cmtm = new CollegeMessageThreadMembers;
			$cmtm->user_id = $data['user_id'];
			$cmtm->thread_id = $college_message_thread_id;
			$cmtm->save();

			// this creates the message

			$message = "Hi ".ucwords((strtolower($data['fname']))). ", can I be of help?";
			$cml = new CollegeMessageLog;

			$cml->user_id = $my_counselor_user_id;
			$cml->thread_id = $college_message_thread_id;
			$cml->msg = $message;
			$cml->is_read = 0;
			$cml->attachment_url = "";
			$cml->is_deleted = 0;
			$cml->save();
			$publish_data = array();

	   	}

	   	Cache::forever( env('ENVIRONMENT').'_'.'controller_getMyCounselorThread_'.$data['user_id'], $college_message_thread_id);
	   	return $college_message_thread_id;
	}

	/**
	 * postMessage
	 *
	 * global method for posting a message
	 *
	 * @param (numeric) (to_user_id) recipient user id
	 * @param (numeric) (college_id) college_id of the associated organization
	 * @param (alpha) (thread_type) determine the nature of thread that needs to be created. inquiry-msg/chat-msg
	 * @param (numeric) (thread_id) thread id the message is associated with, -1 if the thread hasn't been created yet
	 * @param (alphanumeric) (message) the actual message user wants to send
	 * @return (int) (thread_id)
	 */

	public function postMessage($is_api = false, $api_inputs = null, $my_user_id = NULL, $inputs = NULL){

		if( $is_api ){
			$data = array();
			if (!isset($my_user_id)) {
				$watson_check = false;
				$my_user_id = $api_inputs['user_id'];
			}else{
				$watson_check = true;
			}

		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
			$data['currentPage'] = "chat-messages";

			if (!isset($my_user_id)) {
				$watson_check = false;
				$my_user_id = Session::get('userinfo.id');
			}else{
				$watson_check = true;
			}
		}


		if( $is_api ){
			$inputs = $api_inputs;
		}else{
			$collegeid = Session::get('userinfo.school_id');
			!isset($inputs) ? $inputs = Request::all() : NULL;
		}

		if(isset($inputs['thread_type']) && $inputs['thread_type'] == 'agency'){
			$agency_id = $this->decodeIdForSocial($inputs['to_user_id']);
			$is_agency = true;
		}elseif(isset($inputs['thread_type']) && $inputs['thread_type'] == 'college'){
			$college_id = $this->decodeIdForSocial($inputs['to_user_id']);
			$is_college = true;
		}elseif( isset($data['agency_collection']->agency_id) ){
			$agency_id = $data['agency_collection']->agency_id;
			$is_agency = true;
		}else{
			$agency_id = '';
		}

		if(isset($inputs['to_user_id'])){
			$to_user_id = $this->decodeIdForSocial($inputs['to_user_id']);
		}else{
			$to_user_id = '';
		}

		if (!isset($college_id)) {
			if(isset($inputs['college_id'])){
				$college_id = $inputs['college_id'];
				$is_college = true;
			}elseif( isset($collegeid) ){
				$college_id = $collegeid;
				$is_college = true;
			}else{
				$college_id = '';
			}
		}

		if(isset($inputs['thread_type'])){
			$thread_type = $inputs['thread_type'];
		}else{
			$thread_type = '';
		}

		if(isset($inputs['file'])){
			$file = $inputs['file'];
		}else{
			$file = '';
		}

		$message = $inputs['message'];

		if ($inputs['thread_id'] == -1) {
			$thread_id = $inputs['thread_id'];
		}else{
			$thread_id = $this->decodeIdForSocial($inputs['thread_id']);
		}

		//upload file if file attachment
		if($file != ''){
			$attch_ret = $this->generalUploadDoc($file, "file", "asset.plexuss.com/messages");

			//change message markup to include link to
			$search =  'download="" href="';
			$i = strpos($message, $search);
			$message = substr_replace($message, $attch_ret['url'], $i+strlen($search), 0);
		}


		if (isset($is_college)) {
			$valInputs = array(
				'to_user_id' => $to_user_id,
				'college_id' => $college_id,
				'thread_type' => $thread_type,
				'thread_id' => $thread_id,
				'message' => $message);

			$valFilters =array (
				'to_user_id' => 'numeric',
				'college_id' => 'numeric',
				'thread_type' => 'alpha_dash',
				'thread_id' => 'numeric|required',
				'message' => 'required');


		}elseif (isset($is_agency)) {
			$valInputs = array(
				'to_user_id' => $to_user_id,
				'agency_id' => $agency_id,
				'thread_type' => $thread_type,
				'thread_id' => $thread_id,
				'message' => $message);

			$valFilters =array (
				'to_user_id' => 'numeric',
				'agency_id' => 'numeric',
				'thread_type' => 'alpha_dash',
				'thread_id' => 'numeric|required',
				'message' => 'required');
		}

		if (isset($is_college) || isset($is_agency)) {
			$validator = Validator::make( $valInputs, $valFilters );
			if ($validator->fails()){
				$messages = $validator->messages();
				return $messages;
			}
		}

		//remove all html tags from user entry.
		//$message = trim(preg_replace("/<[^>]*>/", ' ', $message));
		//$message = trim(preg_replace("/\s{2,}/", ' ', $message));

		// Get topic id for this conversation
		$college_message_thread_id = $thread_id;
		$org_branch_user_ids = array();

		//if Topic thread doesn't exitst, create a new topic.
		if( $college_message_thread_id == -1){

			if (isset($is_college)) {
				$ob = OrganizationBranch::where('school_id', $college_id)->first();

				if(!isset($ob)){
					return "The school is not setup yet";
				}

				if((empty($college_id)) && (!isset($to_user_id) || $to_user_id == '' || $to_user_id == 0)){
					return "User id does not exist";
				}
			}elseif(isset($is_agency)){
				$ob = Agency::where('id', $agency_id)->first();

				if(!isset($ob)){
					return "The agency is not setup yet";
				}
			}

			$thread_already_exists = DB::connection('rds1')->table('college_message_thread_members as cmtm')
														   ->join(DB::raw('( SELECT DISTINCT thread_id FROM college_message_thread_members as cmtm2 WHERE user_id = '.$to_user_id.' ) as tb1'), 'tb1.thread_id', '=', 'cmtm.thread_id')
														   ->where('user_id', $my_user_id)
														   ->orderby('cmtm.thread_id', 'DESC')
														   ->select(DB::raw('DISTINCT cmtm.thread_id'))
														   ->first();

			if (isset($thread_already_exists)) {
		   		$college_message_thread_id = $thread_already_exists->thread_id;
		   	}

		   	if ($college_message_thread_id == -1) {
				switch ($thread_type) {
					case 'college':

						// Add a new thread.
						$cmt = new CollegeMessageThreads;
						$cmt->save();
						$college_message_thread_id = $cmt->id;

						//*********************************important
						// this piece adds all of the "current" member of organizations to the thread.
						$org_branch_user_ids_collection = OrganizationBranchPermission::where('organization_branch_id', $ob->id)->get();
						foreach ($org_branch_user_ids_collection as $key) {
							$cmtm = new CollegeMessageThreadMembers;

							$cmtm->user_id = $key->user_id;
							$cmtm->org_branch_id = $ob->id;
							$cmtm->thread_id = $college_message_thread_id;
							$cmtm->save();
							$org_branch_user_ids[] = $key->user_id;
						}

						$uid = '';
						if(in_array($my_user_id, $org_branch_user_ids)){

							$uid = $to_user_id;

						}else{

							$uid = $my_user_id;
						}

						$cmtm = new CollegeMessageThreadMembers;
						$cmtm->user_id = $uid;
						$cmtm->org_branch_id = $ob->id;
						$cmtm->thread_id = $college_message_thread_id;
						$cmtm->save();

						$this_org_branch_id = $ob->id;

						break;
					case 'inquiry-msg':

						// Add a new thread.
						$cmt = new CollegeMessageThreads;
						$cmt->save();
						$college_message_thread_id = $cmt->id;

						//*********************************important
						// this piece adds all of the "current" member of organizations to the thread.
						$org_branch_user_ids_collection = OrganizationBranchPermission::where('organization_branch_id', $ob->id)->get();
						foreach ($org_branch_user_ids_collection as $key) {
							$cmtm = new CollegeMessageThreadMembers;

							$cmtm->user_id = $key->user_id;
							$cmtm->org_branch_id = $ob->id;
							$cmtm->thread_id = $college_message_thread_id;
							$cmtm->save();
							$org_branch_user_ids[] = $key->user_id;
						}

						$uid = '';
						if(in_array($my_user_id, $org_branch_user_ids)){

							$uid = $to_user_id;

						}else{

							$uid = $my_user_id;
						}

						$cmtm = new CollegeMessageThreadMembers;
						$cmtm->user_id = $uid;
						$cmtm->org_branch_id = $ob->id;
						$cmtm->thread_id = $college_message_thread_id;
						$cmtm->save();

						$this_org_branch_id = $ob->id;

						break;
					case 'inquiry-txt':

						// Add a new thread.
						$cmt = new CollegeMessageThreads;
						$cmt->has_text = 1;
						$cmt->save();
						$college_message_thread_id = $cmt->id;
						$data['thread_id'] = $cmt->id;

						//*********************************important
						// this piece adds all of the "current" member of organizations to the thread.
						$org_branch_user_ids_collection = OrganizationBranchPermission::where('organization_branch_id', $ob->id)->get();
						foreach ($org_branch_user_ids_collection as $key) {
							$cmtm = new CollegeMessageThreadMembers;

							$cmtm->user_id = $key->user_id;
							$cmtm->org_branch_id = $ob->id;
							$cmtm->thread_id = $college_message_thread_id;
							$cmtm->save();
							$org_branch_user_ids[] = $key->user_id;
						}

						$uid = '';
						if(in_array($my_user_id, $org_branch_user_ids)){

							$uid = $to_user_id;

						}else{

							$uid = $my_user_id;
						}

						$cmtm = new CollegeMessageThreadMembers;
						$cmtm->user_id = $uid;
						$cmtm->org_branch_id = $ob->id;
						$cmtm->thread_id = $college_message_thread_id;
						$cmtm->save();

						$this_org_branch_id = $ob->id;

						break;
					case 'chat-msg':

						// Add a new thread.
						$cmt = new CollegeMessageThreads;
						$cmt->save();
						$college_message_thread_id = $cmt->id;

						// Attach the user, and organization to the thread members
						$cmtm = new CollegeMessageThreadMembers;

						$cmtm->user_id = $to_user_id ;
						$cmtm->org_branch_id = $ob->id;
						$cmtm->thread_id = $college_message_thread_id;
						$cmtm->save();


						$cmtm = new CollegeMessageThreadMembers;
						$cmtm->user_id = $my_user_id;
						$cmtm->org_branch_id = $ob->id;
						$cmtm->thread_id = $college_message_thread_id;
						$cmtm->save();


						$this_org_branch_id = $ob->id;
						break;
					case 'agency-msg':

						// Add a new thread.
						$cmt = new CollegeMessageThreads;
						$cmt->save();
						$college_message_thread_id = $cmt->id;

						//*********************************important
						// this piece adds all of the "current" member of organizations to the thread.
						$org_branch_user_ids_collection = AgencyPermission::where('agency_id', $ob->id)->get();
						foreach ($org_branch_user_ids_collection as $key) {
							$cmtm = new CollegeMessageThreadMembers;

							$cmtm->user_id = $key->user_id;
							$cmtm->agency_id = $ob->id;
							$cmtm->thread_id = $college_message_thread_id;
							$cmtm->save();
							$org_branch_user_ids[] = $key->user_id;
						}

						$uid = '';
						if(in_array($my_user_id, $org_branch_user_ids)){

							$uid = $to_user_id;

						}else{

							$uid = $my_user_id;
						}

						$cmtm = new CollegeMessageThreadMembers;
						$cmtm->user_id = $uid;
						$cmtm->agency_id = $ob->id;
						$cmtm->thread_id = $college_message_thread_id;
						$cmtm->save();

						break;
					case 'agency':

						// Add a new thread.
						$cmt = new CollegeMessageThreads;
						$cmt->save();
						$college_message_thread_id = $cmt->id;

						//*********************************important
						// this piece adds all of the "current" member of organizations to the thread.
						$org_branch_user_ids_collection = AgencyPermission::where('agency_id', $ob->id)->get();
						foreach ($org_branch_user_ids_collection as $key) {
							$cmtm = new CollegeMessageThreadMembers;

							$cmtm->user_id = $key->user_id;
							$cmtm->agency_id = $ob->id;
							$cmtm->thread_id = $college_message_thread_id;
							$cmtm->save();
							$org_branch_user_ids[] = $key->user_id;
						}

						$uid = '';
						if(in_array($my_user_id, $org_branch_user_ids)){

							$uid = $to_user_id;

						}else{

							$uid = $my_user_id;
						}

						$cmtm = new CollegeMessageThreadMembers;
						$cmtm->user_id = $uid;
						$cmtm->agency_id = $ob->id;
						$cmtm->thread_id = $college_message_thread_id;
						$cmtm->save();

						break;
					case 'users':
	                   // Add a new thread.
	                   $cmt = new CollegeMessageThreads;
	                   $cmt->save();
	                   $college_message_thread_id = $cmt->id;


	                   $cmtm = new CollegeMessageThreadMembers;
	                   $cmtm->user_id = $to_user_id;
	                   $cmtm->thread_id = $college_message_thread_id;
	                   $cmtm->save();

	                   $cmtm = new CollegeMessageThreadMembers;
	                   $cmtm->user_id = $my_user_id;
	                   $cmtm->thread_id = $college_message_thread_id;
	                   $cmtm->save();
	                   break;
					default:
						return "Invalid thread type";
						break;
				}
			}

			// this creates the message
			$cml = new CollegeMessageLog;

			$cml->user_id = $my_user_id;
			$cml->thread_id = $college_message_thread_id;
			isset($inputs['post_id']) ? $cml->post_id = $this->decodeIdForSocial($inputs['post_id']) : NULL;
			isset($inputs['share_article_id']) ? $cml->share_article_id = $this->decodeIdForSocial($inputs['share_article_id']) : NULL;
			$cml->msg = $message;
			$cml->is_read = 0;
			$cml->attachment_url = "";
			$cml->is_deleted = 0;
			$cml->save();

			/* publish new message with Redis */
			$publish_data = array();

			// get the last message
			$new_messages = $this->getUserMessages($this->hashIdForSocial($cml->thread_id), $cml->id - 1);

			$publish_data['user'] = $inputs;
			$publish_data['msgs'] = $new_messages['msg'];

			Redis::publish('send:message', json_encode($publish_data));

			if(isset($inputs['to_user_id'])){
				$msg_ntn_data['thread_room'] = $inputs['to_user_id'];
				Redis::publish('send:msgNotification', json_encode($msg_ntn_data));
			}
			/* publish new message with Redis */

		}else{

			Cache::forget(env('ENVIRONMENT').'_'.'controller_getUserMessages_'.$college_message_thread_id);
			// this creates the message
			$cml = new CollegeMessageLog;

			$cml->user_id = $my_user_id;
			$cml->thread_id = $college_message_thread_id;
			isset($inputs['post_id']) ? $cml->post_id = $this->decodeIdForSocial($inputs['post_id']) : NULL;
			isset($inputs['share_article_id']) ? $cml->share_article_id = $this->decodeIdForSocial($inputs['share_article_id']) : NULL;
			$cml->msg = $message;
			$cml->is_read = 0;
			$cml->attachment_url = "";
			$cml->is_deleted = 0;
			$cml->save();

			/* publish new message with Redis */
			$publish_data = array();

			// get the last message
			$new_messages = $this->getUserMessages($this->hashIdForSocial($cml->thread_id), $cml->id - 1);

			$publish_data['user'] = $inputs;
			$publish_data['msgs'] = $new_messages['msg'];

			Redis::publish('send:message', json_encode($publish_data));

			if(isset($inputs['to_user_id'])){
				$msg_ntn_data['thread_room'] = $inputs['to_user_id'];
				Redis::publish('send:msgNotification', json_encode($msg_ntn_data));
			}

			/* publish new message with Redis */

			// if the user is not part of thread members, add him to the mix.
			$cmt = CollegeMessageThreadMembers::where('user_id', '=', $my_user_id)
				->where('thread_id', '=', $college_message_thread_id)
				->first();

			if(!$cmt){
				$cmt = CollegeMessageThreadMembers::where('thread_id', '=', $college_message_thread_id)
				->first();

				if(!$cmt){

					return "Bad Thread Id";
				}


				$cmtm = new CollegeMessageThreadMembers;
				$cmtm->user_id = $my_user_id;

				if (isset($is_agency)) {
					$cmtm->org_branch_id = $cmt->agency_id;
				}elseif (isset($is_college)) {
					$cmtm->org_branch_id = $cmt->org_branch_id;
				}

				$cmtm->thread_id = $college_message_thread_id;
				$cmtm->save();
			}

			if (isset($cmt->agency_id)) {
				$this_agency_id = $cmt->agency_id;
			}elseif (isset($cmt->org_branch_id)) {
				$this_org_branch_id = $cmt->org_branch_id;
			}

		}

		/**** for push notification ****/
		$thread_members_for_push_notification = DB::connection('rds1')->table('college_message_thread_members as cmtm')
													 ->join('users as u', 'u.id', '=', 'cmtm.user_id')
													 ->leftjoin('organization_branches as ob', 'ob.id', '=', 'cmtm.org_branch_id')

	                                                 ->where('cmtm.thread_id', $college_message_thread_id)
	                                                 ->select('u.id as user_id', 'u.is_organization', 'u.is_agency', 'cmtm.org_branch_id', 'cmtm.agency_id',
	                                                 		  'cmtm.id as cmtmid', 'cmtm.num_unread_msg', 'ob.school_id as college_id')
	                                                 ->where('cmtm.is_list_user', 0)
	                                                 ->where('u.id', '!=', $my_user_id)
	                                                 ->groupBy('u.id')
	                                                 ->get();

	    foreach ($thread_members_for_push_notification as $key) {
	    	if (isset($key->college_id)) {
	    		$pushNotification_college_id = $key->college_id;
	    		break;
	    	}
	    	if (isset($key->agency_id)) {
	    		$pushNotification_agency_id  = $key->agency_id;
	    		break;
	    	}
	    	if (isset($key->user_id)) {
	    		$pushNotification_user_id  = $key->user_id;
	    		break;
	    	}
	    }

		$mdt = new MobileDeviceToken;
		$my_user = User::find($my_user_id);

		if (isset($inputs['msg_type'])) {
			$msg_type = $inputs['msg_type'];
		}

		foreach ($thread_members_for_push_notification as $key) {
			$recipientUserHasDeviceToken = $mdt->getToken($key->user_id);

			if( isset($recipientUserHasDeviceToken) && !empty($recipientUserHasDeviceToken) ){
				$publish_data = array();
				$publish_data['platform'] 	  	= $recipientUserHasDeviceToken->platform;
				$publish_data['device_token']   = $recipientUserHasDeviceToken->device_token;
				$publish_data['num_unread_msg'] = $key->num_unread_msg;
				$publish_data['user_id']		= $this->hashIdForSocial($key->user_id);
				if (isset($msg_type)) {
					if ($msg_type == "post") {
						$publish_data['msg']			= $my_user->fname.": shared a post with you";
					}
					elseif ($msg_type == "article") {
						$publish_data['msg']			= $my_user->fname.": shared an article with you";
					}
				}
				else {
					$publish_data['msg']			= $my_user->fname.": ".$message;
				}
      	$publish_data['social_app?']    = true;
				$publish_data['thread_url']    = "social/messages/".$this->hashIdForSocial($college_message_thread_id);

				$publish_data['thread_id'] 		= $this->hashIdForSocial($college_message_thread_id);
				if (isset($key->is_organization) && $key->is_organization == 1) {
					$publish_data['thread_type_id'] = $this->hashIdForSocial($key->user_id);
					$publish_data['thread_type']	= 'users';
				}else{
					if (isset($pushNotification_college_id)) {
						$publish_data['thread_type_id'] = $this->hashIdForSocial($pushNotification_college_id);
						$publish_data['thread_type']	= 'college';
					}elseif (isset($pushNotification_agency_id)) {
						$publish_data['thread_type_id'] = $this->hashIdForSocial($pushNotification_agency_id);
						$publish_data['thread_type']	= 'agency';
					}elseif (isset($pushNotification_user_id)) {
						$publish_data['thread_type_id'] = $this->hashIdForSocial($pushNotification_user_id);
						$publish_data['thread_type']	= 'users';
					}
				}

				$publish_data = json_encode($publish_data);
				// print_r("<pre>".$publish_data."</pre><br/>");
				Redis::publish('send:pushNotification', $publish_data);
			}
		}
		/**** for push notification ****/

		// Create latest threads for people who are involved in this thread.
		$this_thread_members = DB::connection('rds1')->table('college_message_thread_members as cmtm')
													 ->join('users as u', 'u.id', '=', 'cmtm.user_id')
	                                                 ->where('cmtm.thread_id', $college_message_thread_id)
	                                                 ->select('u.id as uid', 'u.is_organization', 'u.is_agency', 'cmtm.org_branch_id', 'cmtm.agency_id', 'cmtm.id as cmtmid')
	                                                 ->where('cmtm.is_list_user', 0)
	                                                 ->groupBy('u.id')
	                                                 ->get();
	    $cmc = new CollegeMessageController();
	    $umc = new UserMessageController();

	    //Reset all the cache values of myself
	    $this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $my_user_id);
		$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $my_user_id);
		$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $my_user_id);

		$online_users_arr = Redis::hvals('online:users');

		foreach ($this_thread_members as $key) {
			if (in_array($key->uid, $online_users_arr)) {

				//Reset all the cache values of each member of each thread, and increment the num of unread msgs
				if ($key->uid != $my_user_id) {
					Cache::forget(env('ENVIRONMENT') .'_'.$key->uid.'_msg_thread_ids');

					$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $key->uid);
					$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $key->uid);
					$this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $key->uid);
				}

				// Increment number of sent messages
				if ($key->uid == $my_user_id) {
					DB::table('college_message_thread_members')
					->where('id', $key->cmtmid)
					->increment('num_of_sent_msg');
				}else{

					DB::table('college_message_thread_members')
					->where('id', $key->cmtmid)
					->update(array(
						'num_unread_msg' => DB::raw('num_unread_msg + 1'),
						'email_sent' => 0
					));
				}

				$redis_input = array();

				$redis_input['user_id']   = $key->uid;
				$redis_input['thread_id'] = $college_message_thread_id;

	      		if ($key->is_organization == 1) {
	      			$redis_input['org_branch_id'] = $key->org_branch_id;
	      			// $latest_thread = $cmc->getThreadListHeartBeat(NULL, NULL, $redis_input);
	      		}elseif ($key->is_agency == 1) {
	      			$redis_input['agency_id'] = $key->agency_id;
	      			// $latest_thread = $cmc->getThreadListHeartBeat(NULL, NULL, $redis_input);
	      		}
	      		// else{
	      		// 	$latest_thread = $umc->getThreadListHeartBeat(NULL, NULL, $redis_input);
	      		// }

	      		$latest_thread = $umc->getThreadListHeartBeat(NULL, NULL, $redis_input);
	      		$latest_thread = json_decode($latest_thread);

	      		if (isset($latest_thread) && !empty($latest_thread)) {
	      			$latest_thread = (array)$latest_thread;

	      			if (isset($latest_thread[0])) {
	      				$latest_thread = $latest_thread[0];
	      			}else{
	      				$topicUsr = (array)$latest_thread['topicUsr'];
	      				$latest_thread = (array)$topicUsr[0];
	      			}

	      			$latest_thread['user_id']     = $this->hashIdForSocial($key->uid);
	      			isset($inputs['thread_room']) ? $latest_thread['thread_room'] = $inputs['thread_room'] : NULL;

		      		$latest_thread = json_encode($latest_thread);
		      		Redis::publish('update:thread', $latest_thread);
	      		}
      		}
      	}


		//Send text message method
		$cmt = CollegeMessageThreads::on('rds1')->where('id', $college_message_thread_id)->first();

		if (isset($cmt) && $cmt->has_text == 1 && isset($data['is_organization']) && $data['is_organization'] == 1) {

			$to_user_id = DB::connection('rds1')->table('college_message_thread_members AS cmtm')
							->leftjoin('organization_branch_permissions AS obp', 'obp.user_id', '=', 'cmtm.user_id')
							->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
							->whereNull('obp.id')
							->where('cmtm.thread_id', $college_message_thread_id)
							->select('cmtm.user_id', 'cmtm.is_list_user', 'cmt.campaign_id')
							->first();

			// dd($cmt);

			if (!isset($to_user_id)) {
				return 'No user founded';
			}

			Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user', $to_user_id->is_list_user, 60);


			$gmc = new GroupMessagingController;

			$input = array();
			$input['selected_students_id'] = Crypt::encrypt($to_user_id->user_id);


			$ret = json_decode($gmc->getNumOfEligbleTextUsers($input));

			if ($ret->can_send == 1) {

				$data['campaign_id']      = $to_user_id->campaign_id;
				$data['campaign_body']    = $message;

				$tw = new TwilioController;
				$tw->sendSms($data);

			}else{
				return "Need to upgrade.";
			}
		}
		// End of text message method

		//dd($college_message_thread_id);

		// $ids = DB::connection('rds1')
		// 	->table('college_message_thread_members as cmtm')
		// 	->where('user_id', $my_user_id)
		// 	->where('thread_id', $college_message_thread_id)
		// 	->pluck('id');

		// // increment number of sent messages
		// if(!empty($ids)){
		// 	DB::table('college_message_thread_members')
		// 		->whereIn('id', $ids)
		// 		->increment('num_of_sent_msg');
		// }

		//Reset all the cache values of each member of each thread, and increment the num of unread msgs
		// $thread_members = DB::connection('rds1')
		// 					->table('college_message_thread_members as cmtm')
		// 					->where('cmtm.user_id', '!=', $my_user_id)
		// 					->where('cmtm.thread_id', '=', $college_message_thread_id)
		// 					->pluck('cmtm.user_id');


		// $this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $my_user_id);
		// $this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $my_user_id);
		// $this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $my_user_id);

		// foreach ($thread_members as $k) {
		// 	Cache::forget(env('ENVIRONMENT') .'_'.$k.'_msg_thread_ids');

		// 	$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $k);
		// 	$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $k);
		// 	$this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $k);
		// }

		// $ids = DB::connection('rds1')
		// 	->table('college_message_thread_members as cmtm')
		// 	->whereIn('user_id', $thread_members)
		// 	->where('thread_id', $college_message_thread_id)
		// 	->pluck('id');

		// if(!empty($ids)){
		// 	DB::table('college_message_thread_members')
		// 		->whereIn('id', $ids)
		// 		->update(array(
		// 			'num_unread_msg' => DB::raw('num_unread_msg + 1'),
		// 			'email_sent' => 0
		// 		));
		// }

		// update the cache for this user.
		//$this->getAllThreads($data, "");

		$data['thread_id'] = $college_message_thread_id;
		$data['stickyUsr'] = "";

		$user = User::on('rds1')->find($my_user_id);

		// Watson work here
		if (isset($this_org_branch_id) && $this_org_branch_id == 21 && !$watson_check && $user->is_organization == 0 && $user->is_agency == 0){
			$wc = new WatsonController();
			if (isset($data['ip'])) {
				$ip = $data['ip'];
			}else{
				$arr = $this->iplookup();
				$ip  = $arr['ip'];
			}
			$response = $wc->sendMessage($message, $ip);
			$response_msg = $response['output']['text'][0];

			$thread_room = $inputs['thread_room'];

			$inputs = array();
			$inputs['thread_id']   = $college_message_thread_id;
			$inputs['message']     = $response_msg;
			$inputs['thread_room'] = $thread_room;
			$inputs['user_id']     = $my_user_id;

			$thread_members = DB::connection('rds1')
							->table('college_message_thread_members as cmtm')
							->join('users as u', 'u.id', '=', 'cmtm.user_id')
							->where('cmtm.user_id', '!=', $my_user_id)
							->where('cmtm.thread_id', '=', $college_message_thread_id)
							->where('u.is_organization', 1)
							->select('u.id as user_id')
							->first();

			$this->postMessage(false, NULL, $thread_members->user_id, $inputs);
		}

		//will return hased id along with it and file information if there was an attatchment

		if (isset($inputs['rthash'])) {
			$ret = array();
			$ret['thread_id'] = $college_message_thread_id;
			$ret['hashed_thread_id'] = hash('crc32b', $college_message_thread_id);

			if(isset($inputs['file'])){
				$ret['file_info'] = $attch_ret;
			}

			return json_encode($ret);
		}

		return $this->hashIdForSocial($college_message_thread_id);

	}

	/**
	 * postMessageAgency
	 *
	 * global method for posting a message to an agency
	 *
	 * @param (numeric) (to_user_id) recipient user id
	 * @param (numeric) (college_id) college_id of the associated organization
	 * @param (alpha) (thread_type) determine the nature of thread that needs to be created. inquiry-msg/chat-msg
	 * @param (numeric) (thread_id) thread id the message is associated with, -1 if the thread hasn't been created yet
	 * @param (alphanumeric) (message) the actual message user wants to send
	 * @return (int) (thread_id)
	 */
	// private function postMessageAgency($inputs){

	// 	if(isset($inputs['college_id'])){
	// 		return "Wrong place to send college_id";
	// 	}

	// 	if(isset($this->decodeIdForSocial($inputs['to_user_id']))){
	// 		$to_user_id = $this->decodeIdForSocial($inputs['to_user_id']);
	// 	}else{
	// 		$to_user_id = '';
	// 	}

	// 	if(isset($inputs['thread_type'])){
	// 		$thread_type = $inputs['thread_type'];
	// 	}else{
	// 		$thread_type = '';
	// 	}

	// 	if(isset($inputs['agency_id'])){
	// 		$agency_id = $inputs['agency_id'];
	// 	}else{
	// 		$agency_id = '';
	// 	}

	// 	$message = $inputs['message'];

	// 	$thread_id = $inputs['thread_id'];


	// 	$valInputs = array(
	// 		'to_user_id' => $to_user_id,
	// 		'agency_id' => $agency_id,
	// 		'thread_type' => $thread_type,
	// 		'thread_id' => $thread_id,
	// 		'message' => $message);

	// 	$valFilters =array (
	// 		'to_user_id' => 'numeric',
	// 		'agency_id' => 'numeric',
	// 		'thread_type' => 'alpha_dash',
	// 		'thread_id' => 'numeric|required',
	// 		'message' => 'required');


	// 	$validator = Validator::make( $valInputs, $valFilters );
	// 	if ($validator->fails()){
	// 		$messages = $validator->messages();
	// 		return $messages;
	// 	}

	// 	//remove all html tags from user entry.
	// 	$message = trim(preg_replace("/<[^>]*>/", ' ', $message));
	// 	$message = trim(preg_replace("/\s{2,}/", ' ', $message));

	// 	$viewDataController = new ViewDataController();
	// 	$data = $viewDataController->buildData();
	// 	$data['currentPage'] = "agency-messages";



	// 	$my_user_id = $data['user_id'];

	// 	// Get topic id for this conversation
	// 	$college_message_thread_id = $thread_id;
	// 	$org_branch_user_ids = array();

	// 	//if Topic thread doesn't exitst, create a new topic.
	// 	if( $college_message_thread_id == -1){

	// 		$ob = Agency::where('id', $agency_id)->first();

	// 		if(!isset($ob)){
	// 			return "The agency is not setup yet";
	// 		}

	// 		switch ($thread_type) {
	// 			case 'agency-msg':

	// 				// Add a new thread.
	// 				$cmt = new CollegeMessageThreads;
	// 				$cmt->save();
	// 				$college_message_thread_id = $cmt->id;

	// 				//*********************************important
	// 				// this piece adds all of the "current" member of organizations to the thread.
	// 				$org_branch_user_ids_collection = AgencyPermission::where('agency_id', $ob->id)->get();
	// 				foreach ($org_branch_user_ids_collection as $key) {
	// 					$cmtm = new CollegeMessageThreadMembers;

	// 					$cmtm->user_id = $key->user_id;
	// 					$cmtm->agency_id = $ob->id;
	// 					$cmtm->thread_id = $college_message_thread_id;
	// 					$cmtm->save();
	// 					$org_branch_user_ids[] = $key->user_id;
	// 				}

	// 				$uid = '';
	// 				if(in_array($my_user_id, $org_branch_user_ids)){

	// 					$uid = $to_user_id;

	// 				}else{

	// 					$uid = $my_user_id;
	// 				}

	// 				$cmtm = new CollegeMessageThreadMembers;
	// 				$cmtm->user_id = $uid;
	// 				$cmtm->agency_id = $ob->id;
	// 				$cmtm->thread_id = $college_message_thread_id;
	// 				$cmtm->save();

	// 				break;

	// 			default:
	// 				return "Invalid thread type";
	// 				break;
	// 		}

	// 	}else{
	// 		// if the user is not part of thread members, add him to the mix.
	// 		$cmt = CollegeMessageThreadMembers::where('user_id', '=', $my_user_id)
	// 			->where('thread_id', '=', $college_message_thread_id)
	// 			->first();

	// 		if(!$cmt){
	// 			$cmt = CollegeMessageThreadMembers::where('thread_id', '=', $college_message_thread_id)
	// 			->first();

	// 			if(!$cmt){

	// 				return "Bad Thread Id";
	// 			}


	// 			$cmtm = new CollegeMessageThreadMembers;
	// 			$cmtm->user_id = $my_user_id;
	// 			$cmtm->agency_id = $cmt->agency_id;
	// 			$cmtm->thread_id = $college_message_thread_id;
	// 			$cmtm->save();

	// 		}

	// 	}

	// 	//dd($college_message_thread_id);
	// 	$cml = new CollegeMessageLog;

	// 	$cml->user_id = $my_user_id;
	// 	$cml->thread_id = $college_message_thread_id;
	// 	$cml->msg = $message;
	// 	$cml->is_read = 0;
	// 	$cml->attachment_url = "";
	// 	$cml->is_deleted = 0;
	// 	$cml->save();

	// 	$ids = DB::connection('rds1')
	// 		->table('college_message_thread_members as cmtm')
	// 		->where('user_id', $my_user_id)
	// 		->where('thread_id', $college_message_thread_id)
	// 		->pluck('id');


	// 	// increment number of sent messages
	// 	if(!empty($ids)){
	// 		DB::table('college_message_thread_members')
	// 			->whereIn('id', $ids)
	// 			->increment('num_of_sent_msg');
	// 	}

	// 	//Reset all the cache values of each member of each thread, and increment the num of unread msgs
	// 	$thread_members = CollegeMessageThreadMembers::where('user_id', '!=', $my_user_id)
	// 		->where('thread_id', '=', $college_message_thread_id)
	// 		->pluck('user_id');

	// 	$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $my_user_id);
	// 	$this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $my_user_id);
	// 	$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $my_user_id);

	// 	foreach ($thread_members as $k) {
	// 		Cache::forget(env('ENVIRONMENT') .'_'.$k.'_msg_thread_ids');

	// 		$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $k);
	// 		$this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $k);
	// 		$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $k);
	// 	}

	// 	$ids = DB::connection('rds1')
	// 		->table('college_message_thread_members as cmtm')
	// 		->whereIn('user_id', $thread_members)
	// 		->where('thread_id', $college_message_thread_id)
	// 		->pluck('id');


	// 	if(!empty($ids)){
	// 		DB::table('college_message_thread_members')
	// 			->whereIn('id', $ids)
	// 			->update( array(
	// 				'num_unread_msg' => DB::raw('num_unread_msg + 1'),
	// 				'email_sent' => 0
	// 			));
	// 	}

	// 	// update the cache for this user.
	// 	//$this->getAllThreads($data, "");

	// 	$data['thread_id'] = $college_message_thread_id;
	// 	$data['stickyUsr'] = "";

	// 	return $college_message_thread_id;
	// }

	public function getHistoryMsg( $thread_id = null, $latest_msg_id = null, $first_msg_id = null) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		return $this->getUserMessages($thread_id, $latest_msg_id, $first_msg_id, $data);
	}

	/**
	 * Returns the list of messages for a user_id, if latest messsage sent,
	 * we will return the messages after that id
	 *
	 */
	public function getUserMessages( $thread_id = null, $latest_msg_id = null, $first_msg_id = null, $data = NULL, $is_api = false) {

		if ( $thread_id == NULL ) {
			return;
		}

		if (!isset($data)) {
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);

			//$data['called_from'] = 'getUserMessages';
		}else{
			//$data['called_from'] = 'getHistoryMsg';
		}

		// Read from cache if available
		// if (!isset($input['return_type']) && !isset($latest_msg_id) && !isset($first_msg_id)) {
		// 	if (Cache::has(env('ENVIRONMENT').'_'.'controller_getUserMessages_'.$thread_id)) {
	 //            $return_arr = Cache::get(env('ENVIRONMENT').'_'.'controller_getUserMessages_'.$thread_id);
	 //            return $return_arr;
	 //        }
  //   	}


		$input = Request::all();

		if( $is_api ){
			$sender_user_id = $data['user_id'];
		}else{
			$data['currentPage'] = "admin-messages";
			$sender_user_id = Session::get( 'userinfo.id' );
		}

		$thread_id = $this->decodeIdForSocial($thread_id);

		// Get topic id for this conversation
		//$college_message_thread_id = $this->getTopicNumber($data['org_branch_id'], $user_id);

		$cml = CollegeMessageLog::on('rds1')->join('college_message_thread_members as cmtm', function($query){
												 $query->on('cmtm.thread_id', '=', 'college_message_logs.thread_id')
												 	   ->on('cmtm.user_id', '=', 'college_message_logs.user_id');
											})
											->leftjoin('college_message_view_times as cmvt', function($qry) use($data){
												$qry->on('cmvt.msg_id', '=', 'college_message_logs.id');
											})
											->where('cmtm.thread_id', '=', $thread_id )
											->select('college_message_logs.*', 'cmtm.is_list_user', 'cmvt.read_time');

		$organization = new Organization;

		// Determine if we want to show the "Show previous messages" button
		$show_previous_msg = 0;

		$thread_count = $cml->count();

		if ($thread_count > 20) {
			$show_previous_msg = 1;
		}

		$cml = $cml->groupby('college_message_logs.id');

		if ( $latest_msg_id == NULL ) {
			$cml = $cml->orderBy( 'college_message_logs.created_at', 'desc' )->take( 20 )->get();
		}else {
			if($first_msg_id != NULL){
				$cml = $cml->where( 'college_message_logs.id', '<', $first_msg_id )->orderBy( 'college_message_logs.id', 'desc' )->take( 20 )->get();
			}else{
				$cml = $cml->where( 'college_message_logs.id', '>', $latest_msg_id )->orderBy( 'college_message_logs.id', 'desc' )->take( 20 )->get();
			}

		}

		$ret = array();

		//var_dump($cml);
		//exit();
		$todayDate = new DateTime( 'today' );

		$message_date = '';
		$old_message_date = '';

		$user_arr = array();
		$org_user_arr = array();

		// get the org branch id and user ids for this thread.
		$cmtm_obp = DB::connection('rds1')->table('college_message_thread_members as cmtm')
										  ->join('organization_branch_permissions as obp', 'cmtm.org_branch_id', '=', 'obp.organization_branch_id')
										  ->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
										  ->where('cmtm.thread_id', $thread_id)
										  ->groupBy('cmtm.user_id')
										  ->select('cmtm.org_branch_id', 'cmtm.user_id', 'cmt.has_text')
										  ->get();

		$cmtm_obp_arr = array();

		$has_text = false;
		foreach ($cmtm_obp as $key) {
			$cmtm_obp_arr[$key->user_id] = $key->org_branch_id;
			($key->has_text == 1) ? $has_text = true : NULL;
		}

		// The user id for a person who is in list users table, and not a regular user just yet.
		$list_user_id = -1;
		// Determine if the current user is a list or not
		$is_list_user_arr = array();


		foreach ( $cml as $k ) {

			// $dt = new DateTime( date_format( $k->created_at, 'Y-m-d H:i:s' ), new DateTimeZone( 'America/Los_Angeles' ) );
			// $dt->setTimezone( new DateTimeZone( 'UTC' ) );

			if (isset($user_arr[$k->user_id])) {
				$user = $user_arr[$k->user_id];
			}else{

				if ($k->is_list_user == 1) {
					$user = ListUser::find($k->user_id);
					$list_user_id = $user->id;
				}else{
					$user = User::find($k->user_id);
				}

				if(isset($user)){
					$user_arr[$k->user_id] = $user;
					$is_list_user_arr[$k->user_id] = (isset($k->is_list_user) && $k->is_list_user == 1) ? true : false;
				}
			}

			if ( isset( $user ) ) {
				$tmp = array();

				$tmp['show_previous_msg'] = $show_previous_msg;

				$tmp['Name'] = $user->fname. " ". $user->lname;

				//convert name to utf-8 format
				$tmp['Name'] = ucwords(strtolower($this->convertNameToUTF8($tmp['Name'])));

				if ( strlen( $tmp['Name'] ) > 15 ) {
					$tmp['Name'] = substr( $tmp['Name'], 0, 15 ) . "...";
				}

				$tmp['full_name'] = $user->fname. " ". $user->lname;

				//convert name to utf-8 format
				$tmp['full_name'] = ucwords(strtolower($this->convertNameToUTF8($tmp['full_name'])));

				if (!isset($user->profile_img_loc) || $user->profile_img_loc == "" ) {
					$tmp['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

				}else {
					$tmp['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

				}

				if($k->msg != strip_tags($k->msg)) {
					$tmp['msg'] = $k->msg;
				}else{
					$tmp['msg'] = $this->auto_link_text( $k->msg );
				}

				if (isset($is_api) && $is_api == true) {
					$tmp['msg'] = strip_tags($tmp['msg'], '<a>');
				}

				// Adding read time here.
				$tmp['read_time'] = $k->read_time;

				$tmp['post_id']           = $this->hashIdForSocial($k->post_id);
				$tmp['share_article_id']  = $this->hashIdForSocial($k->share_article_id);

				$tmp['is_current_user'] = 0;

				if ( $k->user_id == $sender_user_id ) {
					$tmp['is_current_user'] = 1;
					$tmp['Name'] = "Me";
				}
				
				$dt = $this->convertTimeZone($k->created_at, 'America/Los_Angeles', 'UTC');
				$tmp['date'] = $dt->format( 'Y-m-d H:i:s' );
				//$tmp['date'] = $this->xTimeAgo(date_format($k->created_at, 'Y-m-d H:i:s'), date("Y-m-d H:i:s"));
				$tmp['time'] = date('g:i A', strtotime($k->created_at));
				$tmp['msg_id'] = $k->id;
				$tmp['user_id'] = $this->hashIdForSocial($k->user_id);
				$tmp['is_read'] = $k->is_read;
				$tmp['is_org'] = 0;
				$tmp['msg_of_thread'] = $this->hashIdForSocial($thread_id);


				// Determine if the user is part of this organization, it's important to check THIS organization
				// not any organization
				if ( $user->is_organization == 1 ) {

					$obp = OrganizationBranchPermission::where( 'user_id', $k->user_id )->get();
					$cmtm = CollegeMessageThreadMembers::where( 'thread_id', $thread_id )->first();

					if ( isset( $cmtm ) ) {
						foreach ( $obp as $x ) {
							if ( $x->organization_branch_id == $cmtm->org_branch_id ) {
								$tmp['is_org'] = 1;

								if (isset($org_user_arr[$k->user_id]) || isset($user_arr[$k->user_id]) ) {
									$org_user_arr[$k->user_id]  = isset($org_user_arr[$k->user_id]) ? $org_user_arr[$k->user_id] : $user_arr[$k->user_id];
								}else{
									$user = User::on('rds1')->find( $k->user_id );
									if(isset($user)){
										$org_user_arr[$k->user_id] = $user;
									}
								}

								break;
							}

						}

					}

				}

				$ret[] = $tmp;
			}
		}

		// if there's no more messages. then we need to get the users info.


		$cml = DB::connection('rds1')->table('college_message_thread_members as cmtm')
									 ->join('users as u', 'cmtm.user_id', '=', 'u.id')
									 ->where('u.is_organization', 0)
		                             ->where( 'cmtm.thread_id', $thread_id )->get();

		foreach ( $cml as $k ) {

			if (isset($user_arr[$k->user_id])) {
				$user = $user_arr[$k->user_id];
			}else{

				if ($k->is_list_user == 1) {
					$user = ListUser::on('rds1')->find($k->user_id);
					$list_user_id = $user->id;
				}else{
					$user = User::on('rds1')->find($k->user_id);
				}

				if(isset($user)){
					$user_arr[$k->user_id] = $user;
					$is_list_user_arr[$k->user_id] = (isset($k->is_list_user) && $k->is_list_user == 1) ? true : false;
				}
			}

			// Determine if the user is part of this organization, it's important to check THIS organization
			// not any organization
			if ( $user->is_organization == 1 ) {

				$obp = OrganizationBranchPermission::on('rds1')->where( 'user_id', $k->user_id )->get();
				$cmtm = CollegeMessageThreadMembers::on('rds1')->where( 'thread_id', $thread_id )->first();

				if ( isset( $cmtm ) ) {
					foreach ( $obp as $x ) {
						if ( $x->organization_branch_id == $cmtm->org_branch_id ) {

							if (isset($org_user_arr[$k->user_id]) || isset($user_arr[$k->user_id]) ) {
								$org_user_arr[$k->user_id]  = isset($org_user_arr[$k->user_id]) ? $org_user_arr[$k->user_id] : $user_arr[$k->user_id];
							}else{
								$user = User::on('rds1')->find( $k->user_id );
								if(isset($user)){
									$org_user_arr[$k->user_id] = $user;
								}
							}

							break;
						}

					}
				}
			}elseif (isset($data['is_agency'])) {

				$obp = AgencyPermission::on('rds1')->where( 'user_id', $k->user_id )->get();
				$cmtm = CollegeMessageThreadMembers::on('rds1')->where( 'thread_id', $thread_id )->first();

				if ( isset( $cmtm ) ) {
					foreach ( $obp as $x ) {
						if ( $x->agency_id == $cmtm->agency_id ) {

							if (isset($org_user_arr[$k->user_id]) || isset($user_arr[$k->user_id]) ) {
								$org_user_arr[$k->user_id]  = isset($org_user_arr[$k->user_id]) ? $org_user_arr[$k->user_id] : $user_arr[$k->user_id];
							}else{
								$user = User::on('rds1')->find( $k->user_id );
								if(isset($user)){
									$org_user_arr[$k->user_id] = $user;
								}
							}

							break;
						}

					}
				}
			}
		}

		$return_arr = array();

		if (!isset($input['return_type'])) {
			if ( !isset($data['org_id']) && !isset($data['is_agency'])) {
				$user_arr = array_reverse( $user_arr );
				$college_rep_info = array();

				if (isset($user_arr)) {
					foreach ($user_arr as $key) {
						if (isset($cmtm_obp_arr[$key->id])) {
							$this_receiver_user_id = $key->id;
							$rep_info = $organization->getCollegeRepInfo($key->id, $cmtm_obp_arr[$key->id]); //first param is user_id, second is org_branch_id
							if (isset($rep_info) && !empty($rep_info)) {
								$college_rep_info['is_list_user']    = $is_list_user_arr[$key->id];
								$college_rep_info['name']            = ucwords(strtolower($rep_info->fname .' '. $rep_info->lname));

								$college_rep_info['profile_img_loc'] = isset($rep_info->profile_img_loc) ? $rep_info->profile_img_loc : 'default.png';
								$college_rep_info['profile_img_loc'] ='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'. $college_rep_info['profile_img_loc'];

								$college_rep_info['title']           = isset($rep_info->title) ? $rep_info->title : 'N/A';
								$college_rep_info['description']     = isset($rep_info->description) ? $rep_info->description : 'N/A';
								$college_rep_info['member_since']    = isset($rep_info->member_since) ? $rep_info->member_since : 'N/A';
								$college_rep_info['school_name']     = isset($rep_info->school_name) ? $rep_info->school_name : 'N/A';

								$college_rep_info['logo_url']        = isset($rep_info->logo_url) ? $rep_info->logo_url : 'N/A';
								$college_rep_info['logo_url']        = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college_rep_info['logo_url'];

								$college_rep_info['slug']            = isset($rep_info->slug) ? $rep_info->slug : 'N/A';
								$college_rep_info['slug']            = 'https://plexuss.com/college/'.$college_rep_info['slug'];

								$college_rep_info['school_bk_img']   = isset($rep_info->school_bk_img) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/'.$rep_info->school_bk_img : 'https://plexuss.com/images/colleges/default-college-page-photo_overview.jpg';

								$college_rep_info['rank']            = isset($rep_info->rank) ? $rep_info->rank : 'N/A';

								$college_rep_info['address']         = $rep_info->address. ' '.$rep_info->city. ' '.$rep_info->state.' '. $rep_info->zip;
								break;
							}
						}
					}
				}
				$return_arr['user_info'] = json_encode($college_rep_info);
			}else{

				$user = new User;
				$user_info        = array();

				if (isset($user_arr)) {
					foreach ($user_arr as $key) {

						if (!isset($org_user_arr[$key->id]) && $list_user_id != $key->id && $key->id != $data['user_id']) {
							$user_model = $user->getUsersInfo($key->id);

							if (isset($user_model)) {

								$this_receiver_user_id = $key->id;

								$user_info['is_list_user']    = $is_list_user_arr[$key->id];

								$user_info['name']            = ucwords(strtolower($user_model->fname .' '. $user_model->lname));

								$user_info['profile_img_loc'] = isset($user_model->profile_img_loc) ? $user_model->profile_img_loc : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';

								$user_info['country_code']           = isset($user_model->country_code) ? $user_model->country_code : 'N/A';

								$user_info['country_name'] = isset($user_model->country_name) ? $user_model->country_name : '';

								$in_college = $user_model->in_college;

								if($in_college){

									if(isset($user_model->overall_gpa)){
										$user_info['gpa'] = $user_model->overall_gpa;
									}else{
										$user_info['gpa'] = 'N/A';
									}

									if(isset($user_model->college_grad_year)){
										$user_info['grad_year'] = $user_model->college_grad_year;
									}else{
										$user_info['grad_year'] = 'N/A';
									}

									if(isset($user_model->collegeName)){
										$user_info['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($user_model->collegeName))));

										$user_info['school_city'] = $user_model->collegeCity;
										$user_info['school_state'] = $user_model->collegeState;
										if ($user_info['current_school'] == "Home Schooled") {
											$user_info['address'] = $user_info['current_school'];
										}else{
											$user_info['address'] = $user_info['current_school'].', '.$user_info['school_city']. ', '.$user_info['school_state'];
										}

									}else{
										$user_info['current_school'] = 'N/A';
										$user_info['school_city'] = 'N/A';
										$user_info['school_state'] = 'N/A';

										$user_info['address'] = 'N/A';
									}
								}else{

									if(isset($user_model->hs_gpa)){
										$user_info['gpa'] = $user_model->hs_gpa;
									}else{
										$user_info['gpa'] = 'N/A';
									}

									if(isset($user_model->hs_grad_year)){
										$user_info['grad_year'] = $user_model->hs_grad_year;
									}else{
										$user_info['grad_year'] = 'N/A';
									}

									if(isset($user_model->hsName)){
										$user_info['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($user_model->hsName))));
										$user_info['school_city'] = $user_model->hsCity;
										$user_info['school_state'] = $user_model->hsState;

										if ($user_info['current_school'] == "Home Schooled") {
											$user_info['address'] = $user_info['current_school'];
										}else{
											$user_info['address'] = $user_info['current_school'].', '.$user_info['school_city']. ', '.$user_info['school_state'];
										}
									}else{
										$user_info['current_school'] = 'N/A';
										$user_info['school_city'] = 'N/A';
										$user_info['school_state'] = 'N/A';

										$user_info['address'] = 'N/A';
									}
								}

								if (isset($user_model->degree_name)) {

									$user_info['degree_name']     = $user_model->degree_name;
									$user_info['degree_initials'] = $user_model->degree_initials;
									$user_info['major_name']      = $user_model->major_name;
									$user_info['profession_name'] = $user_model->profession_name;
								}else{
									$user_info['degree_name']     = NULL;
									$user_info['major_name']      = NULL;
									$user_info['profession_name'] = NULL;
									$user_info['degree_initials'] = NULL;
								}

								if (isset($user_model->financial_firstyr_affordibility)) {
									$user_info['financial'] = $user_model->financial_firstyr_affordibility;
									if (strpos($user_info['financial'], '-') !== false) {
									    $tmp_arr = array();
									    $tmp_arr = explode("-", $user_info['financial']);

									    $user_info['financial'] = '$'.trim($tmp_arr[0]).' - $'.trim($tmp_arr[1]);
									}else{
										$user_info['financial'] = '$'. $user_info['financial'];
									}
								}else{
									$user_info['financial'] = '';
								}

								$user_info['start_date'] = isset($user_model->planned_start_term) ? ucwords(strtolower($user_model->planned_start_term. ' '. $user_model->planned_start_yr)) : '';

								break;
							}
						}
					}
				}
				$return_arr['user_info'] 		= json_encode($user_info);
			}

			// Find the receiver id if we haven't found it above.
			if (!isset($this_receiver_user_id)) {
				$tmp_cmt = CollegeMessageThreadMembers::on('rds1')->where('thread_id', $thread_id)
																  ->where('user_id', '!=', $data['user_id'])
																  ->first();
				
				isset($tmp_cmt->user_id) ? $this_receiver_user_id = $tmp_cmt->user_id : NULL;
			}
			if (isset($this_receiver_user_id)) {
				/// Add TopicUser to this call
				$umc = new UserMessageController;
				$return_arr['topicUsr'] = $umc->getAllThreads($data, $this_receiver_user_id, 'users', NULL, true, NULL, NULL, $thread_id);
				$return_arr['topicUsr'] = json_encode($return_arr['topicUsr']);
			}else{
				
				$umc = new UserMessageController;
				$return_arr['topicUsr'] = $umc->getAllThreads($data, $data['user_id'], 'users', NULL, true, NULL, NULL, $thread_id);
				$return_arr['topicUsr'] = json_encode($return_arr['topicUsr']);
			}

		}



		if (isset($input['return_type']) && $input['return_type'] == 'blade') {

			$ret = array_reverse( $ret );

			// dd($has_text);
			$return_arr['msg'] = $ret;
			$return_arr['thread_id'] = $thread_id;

			$return_arr['called_from'] = isset($input['called_from']) ? $input['called_from'] : 'other';

			if (isset($has_text) && $has_text == true) {
				return View('admin.contactPane.ajax.contactText', $return_arr);
			}else{
				return View('admin.contactPane.ajax.contactMsg', $return_arr);
			}
		}else{


			$ret = json_encode( array_reverse( $ret ) );

			$return_arr['msg'] = $ret;

			// set the cache for this thread_id
			if (!isset($input['return_type']) && !isset($latest_msg_id) && !isset($first_msg_id)) {
				Cache::put(env('ENVIRONMENT').'_'.'controller_getUserMessages_'.$thread_id, $return_arr, 720);
			}
			return $return_arr;
		}
	}

	/**
	 * Returns the contact list of a user. with latest message, name, picture,
	 * and the date latest message was sent to.
	 *
	 */
	public function setMsgRead( $thread_id = null, $is_api = null ) {

		if ( $thread_id == NULL ) {
			return;
		}

		if( $is_api ){
			$sender_user_id = $is_api; // is_api contains user_id from api route
		}else{
			$sender_user_id = Session::get( 'userinfo.id' );
		}

		$thread_id = $this->decodeIdForSocial($inputs['thread_id']);

		$cmtm = CollegeMessageThreadMembers::where( 'thread_id', '=', $thread_id )
		->where( 'user_id', '=', $sender_user_id )
		->update( array( 'num_unread_msg' => 0, 'email_sent' => 0 ) );
	}


	protected function get_num_of_unread_msgs( $thread_id, $user_id ) {

		$thread_id = $this->decodeIdForSocial($inputs['thread_id']);

		$cmtm = CollegeMessageThreadMembers::where( 'thread_id', $thread_id )
		->where( 'user_id', $user_id )
		->first();

		if ( !isset( $cmtm ) ) {
			return '';
		}
		else {
			if ( $cmtm->num_unread_msg == 0 ) {
				return '';
			}
			return $cmtm->num_unread_msg;
		}

	}

	protected function getTopicNumber( $org_branch_id, $user_id ) {

		$cmt = CollegeMessageThreadMembers::where( 'org_branch_id', '=', $org_branch_id )
		->where( 'user_id', '=', $user_id )
		->first();

		if ( $cmt ) {
			return $cmt->thread_id;
		}

		return -1;

	}
	protected function xTimeAgo( $oldTime, $newTime ) {
		$timeCalc = strtotime( $newTime ) - strtotime( $oldTime );
		if ( $timeCalc > ( 60*60*24*365.25 ) ) {$timeCalc = round( $timeCalc/60/60/24/365.25 ) . " years ago";}
		else if( $timeCalc > ( 60*60*24 ) ) {$timeCalc = round( $timeCalc/60/60/24 ) . " days ago";}
		else if ( $timeCalc > ( 60*60 ) ) {$timeCalc = round( $timeCalc/60/60 ) . " hrs ago";}
		else if ( $timeCalc > 60 ) {$timeCalc = round( $timeCalc/60 ) . " mins ago";}
		else if ( $timeCalc > 0 ) {$timeCalc .= " secs ago";}
		return $timeCalc;
	}

	protected function auto_link_text( $text ) {
		return preg_replace( '@(https?://([-\w\.]+)+(:\d+)?(/([-\w/_\.#]*(\?\S+)?)?)?)@', '<a rel="nowfollow" target="_blank" href="$1">$1</a>', $text );
	}

	protected function array_search2d( $needle, $haystack ) {
		for ( $i = 0, $l = count( $haystack ); $i < $l; ++$i ) {
			if ( isset( $haystack[$i] ) ) {
				if ( in_array( $needle, $haystack[$i] ) ) return $i;
			}
		}
		return false;
	}

	// Check to see if the user is online right now.

	protected function is_user_online($user_id , $secs_to_consider_live ) {
		$college_chat_threads = Cache::get( env('ENVIRONMENT') .'_'.'college_chat_threads' );

		//dd($college_chat_threads[$this->chat_thread_id]);
		if ( !isset( $college_chat_threads[$this->chat_thread_id] ) ) {
			return 0;
		}

		$tmp_thread= $college_chat_threads[$this->chat_thread_id];
		foreach ( $tmp_thread as $key ) {

			if (isset($key['user_id'])) {
				if ( $key['user_id'] == $user_id ) {
					if ( isset( $key['updated_at'] ) ) {
						$timeCalc = strtotime( date( "Y-m-d H:i:s" ) ) - strtotime( $key['updated_at'] );
						if ( $timeCalc < $this->secs_to_consider_live ) {
							//echo "xxxx";
							return 1;
						}else{
							return 0;
						}

					}
				}
			}

		}
		return 0;

	}

	/**
	 * retrives the college ids that are live right now
	 * @return array of college ids
	*/
	protected function current_live_colleges(){

		$ret = array();

		if (!Cache::has(env('ENVIRONMENT') .'_'.'college_chat_threads')) {
			return $ret;
		}

		$college_threads = DB::table('college_message_threads as cmt')->where('cmt.is_chat', 1)
					->join('college_message_thread_members as cmtm', 'cmtm.thread_id', '=' , 'cmt.id')
					->join('organization_branches as ob', 'ob.id', '=', 'cmtm.org_branch_id')
					->select('cmt.id as thread_id', 'ob.school_id')
					->groupBy('cmt.id')
					->get();


		$chat_threads  = array();

		foreach ($college_threads as $key) {
			$chat_threads[$key->thread_id] = $key->school_id;
		}

		$threads = Cache::get( env('ENVIRONMENT') .'_'.'college_chat_threads' );

		foreach ($threads as $thread_val => $thread) {
			foreach ($thread as $key ) {
				if ( isset( $key['updated_at']) && $key['is_org'] == 1 ) {
					$timeCalc = strtotime( date( "Y-m-d H:i:s" ) ) - strtotime( $key['updated_at'] );
					if ( $timeCalc < $this->secs_to_consider_live ) {
						if (isset($chat_threads[$thread_val])) {
							$ret[] = $chat_threads[$thread_val];
						}
					}

				}
			}

		}

		return $ret;

	}

	protected function is_any_school_online(){

		if (!Cache::has(env('ENVIRONMENT') .'_'.'college_chat_threads')) {
			return false;
		}

		$threads = Cache::get( env('ENVIRONMENT') .'_'.'college_chat_threads' );

		foreach ($threads as $thread_val => $thread) {
			foreach ($thread as $key ) {
				if ( isset( $key['updated_at']) && $key['is_org'] == 1 ) {
					$timeCalc = strtotime( date( "Y-m-d H:i:s" ) ) - strtotime( $key['updated_at'] );
					if ( $timeCalc < $this->secs_to_consider_live ) {
						return true;
					}

				}
			}

		}

		return false;

	}

	protected function set_chat_thread_id($thread_id){
		$this->chat_thread_id = $thread_id;
	}


	protected function setThreadListStatusToBad($cache_key, $user_id){

		if (Cache::has(env('ENVIRONMENT') . $cache_key. $user_id)) {

			$cache_usersTopicsAndMessages = Cache::get(env('ENVIRONMENT') . $cache_key. $user_id);

			$cache_usersTopicsAndMessages['status'] = 'bad';

			Cache::put(env('ENVIRONMENT') . $cache_key. $user_id, $cache_usersTopicsAndMessages, 60);
		}
	}


	// --------------- convert name to utf-8 format
	public function convertNameToUTF8($name){
		$valid_name = $name;

		if (preg_match('/[^A-Za-z ]/', $name)){
			$convert_string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name); //convert to ascii utf-8
			$strip_chars = preg_replace("/[^a-zA-Z ]/", "", $convert_string); //get rid of any chars that aren't letters or spaces

			//when trimmed, if it's empty, then that means entire name doesn't contain any english letters, so it's a different language,
			//so don't save converted name, save original name
			if( trim($strip_chars) == '' ){
				$valid_name = 'Cannot Display Name';
			}else{
				//if here, then that means converting the name was able to produce english letters so save the converted name
				$valid_name = $strip_chars;
			}
		}

		return $valid_name;
	}
	//*********************** end ofchat and messsages methods**************************///

	/**
     * This method returns the sql query with binding values
     * @param sql (string)
     *
     * @return string
     */

	public function getRawSqlWithBindings($qry){

		$sql = $qry->toSql();
		foreach($qry->getBindings() as $binding){
	      $value = is_numeric($binding) ? $binding : '"'.$binding.'"';
	      $sql = preg_replace('/\?/', $value, $sql, 1);
	    }

	    return $sql;

	}

	public function chargeAgencyPerInquiry($agency_collection, $user_id){
		//Payment section
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$agency = Agency::find($agency_collection->agency_id);
		$paid = 0;
		if (isset($agency->balance)) {
			$balance = $agency->balance;
		}else{
			$balance = 0;
		}


		if($agency->is_trial_period == 1){
			$paid = 1;
			$cost = 0;
		}else{

			$balance = $agency->balance - $agency->cost_per_approved;
			$cost = $agency->cost_per_approved;

			if ($balance >= 0) {
				$paid = 1;
			}else{
				$paid = 0;
			}
		}

		$attr = array('user_id' => $user_id, 'agency_id' => $agency_collection->agency_id);
		$val  = array('user_id' => $user_id, 'agency_id' => $agency_collection->agency_id, 'cost' => $cost, 'paid' => $paid);


		AgencyTransactionLog::updateOrCreate($attr, $val);

		// Recruitment table

		$agencyRec = AgencyRecruitment::where('user_id', $user_id)
										->where('agency_id', $agency_collection->agency_id)
										->first();
		// check to see if this recruitment has been paid or not, if not subtract from balance
		$agency->balance = $balance;
		$agency->save();

		$agencyRec->user_recruit = 1;
		$agencyRec->paid = $paid;
		$agencyRec->save();
	}

	public function chargeCollegePerInquiry($recruitment_id, $org_branch_id, $user_id, $aor_id = null){

		$cphl = CollegePaidHandshakeLog::on('rds1')->where('recruitment_id', $recruitment_id)->first();

		if (!isset($cphl)) {

			$rec = Recruitment::find($recruitment_id);
			if(isset($rec->aor_id)){

				$aor = Aor::find($aor_id);
				$aor->balance = $aor->balance - $aor->cost_per_handshake;

				$aor->save();

			}else{

				$ob = OrganizationBranch::find($org_branch_id);
				$ob->balance = $ob->balance - $ob->cost_per_handshake;

				$ob->save();
			}

			$cphl = new CollegePaidHandshakeLog;
			$cphl->recruitment_id = $recruitment_id;
			$cphl->org_branch_id  = $org_branch_id;
			$cphl->aor_id 		  = $aor_id;
			$cphl->user_id 		  = $user_id;

			$cphl->save();
		}

		Session::put('userinfo.session_reset', 1);
	}
	/*
	Temporary function to seperate REMOVE and REJECT from recruitment.

	*/
	public function fixRecruitment(){

		$rec = Recruitment::where('user_recruit', 1)
						  ->where('college_recruit', -1)
						  ->orderby('id','desc')
						  ->get();

		foreach ($rec as $key) {

			$tp = DB::table('plexuss_logging.tracking_pages as tp')
						->join('users as u', 'u.id', '=', 'tp.user_id')
						->join('organization_branch_permissions as obp', 'obp.user_id', '=', 'u.id')
						->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')
						->join('colleges as c', 'c.id', '=', 'ob.school_id')
						->where('tp.url', 'https://plexuss.com/admin/inquiries/setRecruit/'.$key->user_id.'/1')
						->where('c.id', $key->college_id)
						->first();

			$tp2 = DB::table('plexuss_logging.tracking_pages as tp')
						->join('users as u', 'u.id', '=', 'tp.user_id')
						->join('organization_branch_permissions as obp', 'obp.user_id', '=', 'u.id')
						->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')
						->join('colleges as c', 'c.id', '=', 'ob.school_id')
						->where('tp.url', 'https://plexuss.com/admin/inquiries/setRecruit/'.$key->user_id.'/-1')
						->where('c.id', $key->college_id)
						->first();

			if (isset($tp) && isset($tp2)) {
				$tmp_rec = Recruitment::find($key->id);

				$tmp_rec->college_recruit = 1;
				$tmp_rec->status = 0;

				$tmp_rec->save();
				print_r($key->id. "<br>");
			}
		}
	}

	/**
     * This method runs auto approve students cron job from recommendation for colleges
     *
     * @return null
     */

	public function autoApproveRecommendationColleges(){

		$qry = DB::table('organization_branches as ob')
				 ->join(DB::raw('(select max(id) as MAX_ID, user_id,organization_branch_id from organization_branch_permissions WHERE user_id != -1 AND user_id!=93 AND user_id!=120 AND user_id!=127 group by organization_branch_id ) as obp'), 'obp.organization_branch_id', '=', 'ob.id')
				 ->join('college_recommendations as cr', 'cr.college_id', '=', 'ob.school_id')
				 ->join('colleges as c', 'c.id', '=', 'ob.school_id')
				 ->join('college_recommendation_filters as crf', 'crf.college_id', '=', 'ob.school_id')
				 ->where('cr.active', 1)
				 ->where('cr.created_at', '>=', Carbon::now()->today())
				 ->where('cr.created_at', '<=', Carbon::now()->tomorrow())
				 ->where('ob.is_auto_approve_rec', 1)
				 ->where('cr.type', 'filtered')
				 ->select('cr.user_id', 'cr.college_id', 'cr.id as rec_id', 'cr.aor_id',
				 		  'obp.user_id as college_user_id',
				 		  'c.school_name',
				 		  'crf.id as has_filter')
				 ->orderby('cr.id')
				 ->groupby('cr.id')
				 ->get();

		foreach ($qry as $key) {

			$attr = array('user_id' => $key->user_id, 'college_id' => $key->college_id,
						  'aor_id' => $key->aor_id );

			$val = array('user_id' => $key->user_id, 'college_id' => $key->college_id,
						 'user_recruit' => 0, 'college_recruit' => 1, 'aor_id' => $key->aor_id,
						 'reputation' => 0,  'location' => 0, 'tuition' => 0,
						 'program_offered' => 0, 'athletic' => 0, 'onlineCourse' => 0,
						 'religion' => 0, 'campus_life' => 0, 'status' => 1, 'type' => 'auto_approve_recommendation');

			$tmp = Recruitment::updateOrCreate($attr, $val);

			$cr = CollegeRecommendation::find($key->rec_id);

			$cr->active = 0;
			$cr->save();


			////*************Add Notification to the user *********************///////
			$ntn = new NotificationController();
			$ntn->create( $key->school_name, 'user', 3, null, $key->college_user_id , $key->user_id);

		}

		$this->autoApproveInquiryColleges();
	}

	/**
     * This method is called at the end of autoApproveRecommendationColleges.
     * It automatically approveds inquiries for targeted schools if the user meets the targeting
     * for one of their portals (and the inquiry was created BEFORE that last recommendation time)
     */
	public function autoApproveInquiryColleges(){
		$qry = Priority::on('rds1')
			->join('recruitment as r', function($join){
				$join->on('r.college_id', '=', 'priority.college_id')
					 ->on(DB::raw('(r.aor_id = priority.aor_id or r.aor_id is null and priority.aor_id is null)'), DB::raw("''"), DB::raw("''"));
			})
			->join('colleges as c', 'r.college_id', '=', 'c.id')
			->join(DB::raw('(SELECT
								 college_id, aor_id, MAX(created_at) as created
							 FROM
							 	 college_recommendations
							 GROUP BY college_id, aor_id) as cr'), function($join){
				$join->on('cr.college_id', '=', 'priority.college_id')
					 ->on(DB::raw('(cr.aor_id = priority.aor_id or cr.aor_id is null and priority.aor_id is null)'), DB::raw("''"), DB::raw("''"));
					})
			->join(DB::raw('(SELECT
								 ob.school_id, obp.user_id
							 FROM
								 organization_branches ob
									 JOIN
								 organization_branch_permissions obp ON ob.id = obp.organization_branch_id
							 WHERE
								super_admin = 1
							 GROUP BY ob.school_id) as obp'),
				   'obp.school_id', '=', 'r.college_id')
			->whereIn('r.type', array('inquiry', 'inquiry from email', 'inquiry from text'))
			->where('r.user_recruit', 1)
			->where('r.status', 1)
			->where('r.created_at', '<=', 'cr.created')
			->where('r.college_recruit', 0)
			->select('c.school_name', 'r.college_id', 'r.user_id', 'r.aor_id', 'obp.user_id as college_user_id')
			->take(100)
			->get();

		foreach($qry as $key){
			$match = $this->checkPortalMatch($key->college_id, $key->user_id, $key->aor_id);

			if($match){
				$attr = array('user_id' => $key->user_id, 'college_id' => $key->college_id,
							  'aor_id' => $key->aor_id );

				$val = array('college_recruit' => 1, 'note' => 'auto-approved inquiry');

				$tmp = Recruitment::updateOrCreate($attr, $val);

				$ntn = new NotificationController();
				$ntn->create( $key->school_name, 'user', 2, null, $key->college_user_id , $key->user_id);
			}
		}
	}

	public function checkPortalMatch($college_id, $user_id, $aor_id=null, $portal_name=null){

		$data = array();
		$data['org_school_id'] = $college_id;
		$data['aor_id'] = $aor_id;

		if(isset($aor_id)){
			$portals = DB::connection('rds1')
				->table('college_recommendation_filters')
				->where('college_id', $college_id)
				->where('aor_id', $aor_id)
				->select('org_branch_id', 'aor_portal_id')
				->groupby('aor_portal_id')
				->get();
		}else{
			$portals = DB::connection('rds1')
				->table('organization_branches as ob')
				->join('organization_portals as op', 'op.org_branch_id', '=', 'ob.id')
				->where('ob.school_id', $college_id)
				->where('active', 1);

			if(isset($portal_name)){
				$portals = $portals->where('op.name', $portal_name);
			}

			$portals = $portals->select('ob.id as org_branch_id', 'op.id as org_portal_id')
							   ->get();
		}

		foreach($portals as $portal){
			$data['org_branch_id'] = $portal->org_branch_id;
			$data['default_organization_portal'] = (object) array();
			$data['default_organization_portal']->id = isset($portal->org_portal_id) ? $portal->org_portal_id : null;

			$data['aor_portal_id'] = isset($portal->aor_portal_id) ? $portal->aor_portal_id : null;

			$crf = new CollegeRecommendationFilters;

			$qry = $crf->generateFilterQry($data);

			if(!empty($qry)){

				$qry = $this->getRawSqlWithBindings($qry);

				$qry = $qry.' and userFilter.id = '.$user_id;

				$qry = DB::select($qry);

				if (!empty($qry)){
					return True;
				}
			}
		}
		return False;
	}

	/* check is user has already asked to be recruited by school */
	public function hasAlreadyAskedToBeRecruited( $user_id, $school_id ){
		$recruitment = Recruitment::where('user_id', '=', $user_id)
				->where('college_id', '=', $school_id)
				->where('user_recruit', 1)
				->where('status', '=', '1')
				->first();

		return $recruitment;

	}

	public function populateSentMsg(){

		$cmtm = DB::table('college_message_thread_members')
							->take(1500)
							->skip(23500)
							->get();

		foreach ($cmtm as $key) {
			$cml = CollegeMessageLog::where('thread_id', $key->thread_id)
									->where('user_id', $key->user_id)
									->count();

			//dd($cml);
			$temp = CollegeMessageThreadMembers::find($key->id);

			if (isset($cml)) {
				$temp->num_of_sent_msg = $cml;
			}else{
				$temp->num_of_sent_msg = 0;
			}

			$temp->save();
		}
	}

	/**
	 * setDailyRecGoals
     * Cron job to be ran to set daily num of recommendation. The formula is as follow:
     *
	 * yearly_approved: (year goal) # applications * 10
	 * monthly_approved: yearly_approved / 12
	 * monthly_approved_adjusted: monthly_approved - # approved since beginning of month period
	 * [Daily Approved Goal Adjusted]: monthly_approved_adjusted / days remaining till end of month period
	 *
	 *
	 * recommendations = total recommendations
	 * p = Handshakes from recommendations / recommendations
	 * q = 1 - p
	 * LR = p - 1.96 * sqrt[(p x q)/recommendations]
     *
	 * Daily Recs = [Daily Approved Goal Adjusted] / LR
     *
     * @return null
     */
	public function setDailyRecGoals(){

		// $ob = OrganizationBranch::where('num_of_applications', '>', 0)
		// 						->where('bachelor_plan', 1)
		// 						->get();

		$ob = College::on('rds1')->where('in_our_network', 1)
				     ->join('organization_branches as ob', 'ob.school_id', '=', 'colleges.id')
					 ->leftjoin('college_recommendation_filters as crf', 'crf.college_id', '=', 'colleges.id')
					 ->leftjoin('organization_portals as op', 'op.org_branch_id', '=', 'ob.id')
					 ->where('ob.bachelor_plan', 1)
					 ->where(function($query){
					 	$query = $query->orWhere('ob.num_of_applications', '>', 0)
					 				   ->orWhere('op.num_of_applications', '>', 0);

					 })
					 ->select('colleges.id as college_id', 'crf.org_portal_id', 'ob.id as org_branch_id',
					 	DB::raw("IF(crf.org_portal_id IS NULL, ob.num_of_applications, op.num_of_applications) as num_of_applications"))
					 ->groupby('crf.college_id','crf.org_portal_id')
					 ->get();

		$min = 10;
		$max = 100;

		$first_day_month = Carbon::now()->startOfMonth();
		$end_of_month    = Carbon::now()->endOfMonth();

		$now             = Carbon::now()->toDateTimeString();

        $datetime1 = new DateTime($first_day_month->toDateString());
        $datetime2 = new DateTime($end_of_month->toDateString());

        $interval = $datetime1->diff($datetime2);
		$interval = $interval->format('%R%a');
		$num_days_remaining = str_replace("+", "", $interval);
		$num_days_remaining = (int)str_replace("-", "", $num_days_remaining);

		$rec = new Recruitment;
		$cr = new CollegeRecommendation;

		foreach ($ob as $mainKey) {

			$college_id_arr = array();
			$college_id_arr[] = $mainKey->college_id;
			$org_portal_id = NULL;
			$raw_filter_qry = NULL;

			if (isset($mainKey->org_portal_id)) {
				$org_portal_id = $mainKey->org_portal_id;
				// If user has a department set, show results based on the filters they have set
				$data['org_school_id'] = $mainKey->college_id;
				$data['org_branch_id'] = $mainKey->org_branch_id;
				$data['default_organization_portal'] = (object) array();
				$data['default_organization_portal']->id = $org_portal_id;

				$crf = new CollegeRecommendationFilters;
				$filter_qry = $crf->generateFilterQry($data);

				if (isset($filter_qry)) {
					$filter_qry = $filter_qry->select('userFilter.id as filterUserId');
					$raw_filter_qry = $this->getRawSqlWithBindings($filter_qry);
				}
				// end of department set
			}

			$total_rec = $cr->getTotalNumOfRecommendationsForColleges($college_id_arr, $org_portal_id);

			foreach ($total_rec as $key) {
				$tmp = $key->cnt;
			}

			$total_rec = isset($tmp) ? $tmp : 0;

			// if the college have 400 or more recommendation apply the formula
			// if not set to 50 recommendations.
			if($total_rec > 400){
				$year_approved = $mainKey->num_of_applications * 10;
				$monthly_approved = $year_approved / 12;

				$approved_since_begginning = $rec->getNumOfTotalApprovedForColleges($college_id_arr, $first_day_month->toDateTimeString(), NULL, $raw_filter_qry);

				foreach ($approved_since_begginning as $key) {
					$approved_since_begginning_tmp = $key->cnt;
				}

				$approved_since_begginning_tmp = isset($approved_since_begginning_tmp) ? $approved_since_begginning_tmp : 0;

				$monthly_approved_adjusted = $monthly_approved - $approved_since_begginning_tmp;

				$approved_goal = $monthly_approved_adjusted / $num_days_remaining;

				$temp =0;
				$handshake_recs_cnt = 0;

				$handshake_recs = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'recommendation', NULL, NULL, $raw_filter_qry);

				foreach ($handshake_recs as $key) {
					$temp = $key->cnt;
				}

				$handshake_recs_cnt = isset($temp) ? $temp : 0;

				$handshake_recs = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'auto_approve_recommendation', NULL, NULL, $raw_filter_qry);

				foreach ($handshake_recs as $key) {
					$handshake_recs_cnt = $handshake_recs_cnt + $key->cnt;
				}

				if ($handshake_recs_cnt == 0) {
					$daily_recs == $max;
				}else{
					$p = $handshake_recs_cnt /  $total_rec;
					$q = 1 - $p;
					$lr = $p - 1.96 * sqrt(($p * $q) / $total_rec);
					$daily_recs = ceil($approved_goal / $lr);
				}

				// print_r("year_approved ". $year_approved . "<br>");
				// print_r("monthly_approved ". $monthly_approved . "<br>");
				// print_r("approved_since_begginning ". $approved_since_begginning_tmp . "<br>");
				// print_r("monthly_approved_adjusted ". $monthly_approved_adjusted . "<br>");
				// print_r("num_days_remaining ". $num_days_remaining . "<br>");
				// print_r("handshake_recs ". $handshake_recs_cnt . "<br>");
				// print_r("approved_goal ". $approved_goal . "<br>");
				// print_r("p ". $p . "<br>");
				// print_r("q ". $q . "<br>");
				// print_r("lr ". $lr . "<br>");
				// print_r("daily_recs ". $daily_recs . "<br>");
				// exit();


				if ($daily_recs < $min) {
					$daily_recs = $min;
				}

				if ($daily_recs > $max) {
					$daily_recs = $max;
				}
			}else{
				$daily_recs = 50;
			}

			if (isset($mainKey->org_portal_id)) {
				$ob_tmp = OrganizationPortal::find($mainKey->org_portal_id);
				$ob_tmp->num_of_filtered_rec = $daily_recs;

				$ob_tmp->save();
			}else{
				$ob_tmp = OrganizationBranch::find($mainKey->org_branch_id);
				$ob_tmp->num_of_filtered_rec = $daily_recs;

				$ob_tmp->save();
			}

		}

		return "success";
	}

	/*
		This method returns the string between start and end strings
	*/

	protected function getStringBetween($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

	//function that determines if we need to remind admin/agency again of what the applied student column
	public function appliedReminderOnLoad($data){
		$aReminder = 0;
		//**************************** OLD CODE ****************************************//
		//get applied reminder data

		// $data['last_updated_reminder'] = '';
		// $is_agency = isset($data['is_agency']) && $data['is_agency'] == 1 ? true : false;
		// $uid = $is_agency ? $data['agency_collection']->user_id : $data['user_id'];

		//if cache has applied variable, then they chose to remind later - set $aReminder = 0 because we don't want to show the modal unless they have signout and signed back in
		//else check if today is the 21st of the month to also change applied_reminder to 1
		//else do nothing - keep applied_reminder 0
		// if( Cache::has(env('ENVIRONMENT').'_'.$uid.'_applied_remind_me_later') ){
		// 	$aReminder = 0;
		// }else{

		// 	if( $is_agency ){
		// 		$permissionsReturn = DB::table('agency_permissions')->where('user_id', '=', $uid)->get();
		// 	}else{
		// 		$permissionsReturn = DB::table('organization_branch_permissions')->where('user_id', '=', $uid)->get();
		// 	}

		// 	foreach ($permissionsReturn as $key) {
		// 		$aReminder = $key->applied_reminder;
		// 		$data['last_updated_reminder'] = $key->updated_at;
		// 	}

		// 	$today = Carbon::now()->today();
		// 	$twenty_first = Carbon::now()->startOfMonth()->addDays(20);
		// 	$end_of_month = Carbon::now()->endOfMonth();
		// 	$updated_today = Carbon::createFromTimestamp( strtotime($data['last_updated_reminder']) )->isToday();

		// 	//if it's the twenty first day of the current month AND applied_reminder is 0, meaning they clicked ok and understood the reminder before
		// 	//then change applied_reminder to 1 to show them the reminder modal again because its been a month since they last saw it
		// 	if( $today->between($twenty_first, $end_of_month) && (int)$aReminder == 0 && !$updated_today){
		// 		//if in here, then date is 21st of month, applied_reminder is 0, and hasnt already been updated today
		// 		if( $is_agency ){
		// 			$permissionsUpdate = DB::table('agency_permissions')->where('user_id', '=', $uid)->update(array('applied_reminder' => 1));
		// 		}else{
		// 			$permissionsUpdate = DB::table('organization_branch_permissions')->where('user_id', '=', $uid)->update(array('applied_reminder' => 1));
		// 		}

		// 		$aReminder = $permissionsUpdate;
		// 	}
		// }
		//**************************** OLD CODE ENDS ****************************************//
		$minutes = 200;
		if(Cache::has(env('ENVIRONMENT') .'_'.$data['user_id'].'_applied_remind_me_later') ){
			$aReminder = 0;
		}else{
			$aReminder = 1;

			$arr = array();
			$arr['reminder_later'] = 'true';
			// Stay in cache for a month
			Cache::put(env('ENVIRONMENT') .'_'.$data['user_id'].'_applied_remind_me_later', $arr, $minutes);
		}
		// print_r($aReminder."<br/>");

		return $aReminder;
	}

	//function that determines if we need to remind text msg plan is expired
	public function textmsgReminderOnLoad($data) {
		$bReminder = 0;
		$data['last_textmsg_updated_reminder'] = '';
		$is_agency = isset($data['is_agency']) && $data['is_agency'] == 1 ? true : false;
		$uid = $is_agency ? $data['agency_collection']->user_id : $data['user_id'];

		if( Cache::has(env('ENVIRONMENT').'_'.$uid.'_textmsg_remind_me_later') ) {
			$bReminder = 0;
		} else {
			// pending ...
			if( $is_agency ){
				$permissionsReturn = DB::table('agency_permissions')->where('user_id', '=', $uid)->orderBy('updated_at', 'ASC')->get();
			}else{
				$permissionsReturn = DB::table('organization_branch_permissions')->where('user_id', '=', $uid)->orderBy('updated_at', 'ASC')->get();
			}

			foreach ($permissionsReturn as $key) {
				$data['last_textmsg_updated_reminder'] = $key->updated_at;
			}

			$today = Carbon::now()->today();
			$twenty_first = Carbon::now()->startOfMonth()->addDays(20);
			$end_of_month = Carbon::now()->endOfMonth();
			$updated_today = Carbon::createFromTimestamp( strtotime($data['last_textmsg_updated_reminder']) )->isToday();

			if(isset($data['textmsg_reminder']) && $data['textmsg_reminder'] == 1) {
				// if flag is not stored in Cache and there is a need to show reminder modal so we need to keep flag
				$bReminder = 1;
			} else {
				// if flag is not stored in Cache, also does not need to show, we need to check if the timestamp meet some criteria.
				if(!$updated_today && (int)$bReminder == 0 && $today->between($twenty_first, $end_of_month) ) {
					$bReminder = 1;
				} else {
					$bReminder = 0;
				}
			}

		}

		return $bReminder;

	}

	/**
	 * getMessageTemplate
	 *
	 * Generate message templates for college or an agency
	 *
	 * @param (obj) (data) passing data from the college or agency
	 * @return (obj) (data)
	 */
	protected function getMessageTemplates($data, $noDefault = false){

		if( $data['is_agency'] == 1 ){
			$mt = MessageTemplate::where('agency_id', $data['agency_collection']->agency_id)
								 ->get();
		}else{
			$mt = MessageTemplate::where('college_id', $data['org_school_id'])
								 ->get();
		}

		$data['message_template'] = NULL;

		if (!isset($mt)) {
			return $data;
		}
		$arr = array();

		if( !$noDefault ){
			$arr[''] = 'Insert Template';
			foreach ($mt as $key) {
				$arr[Crypt::encrypt($key->id)] = $key->name;
			}
		}else{
			foreach ($mt as $key) {
				$tm = array();
				$tm['id'] = Crypt::encrypt($key->id);
				$tm['name'] = $key->name;
				$tm['content'] = $key->content;
				$arr[] = $tm;
			}
		}

		$data['message_template'] = $arr;

		return $data;
	}

	/**
	 * getMessageTemplate
	 *
	 * Generate message templates for college or an agency
	 *
	 * @param (obj) (data) passing data from the college or agency
	 * @return (obj) (data)
	 */
	protected function getOrgSavedAttachments($data, $noDefault = false){

		if( $data['is_agency'] == 1 ){
			$mt = OrgSavedAttachment::where('agency_id', $data['agency_collection']->agency_id)
								 ->get();
		}else{
			$mt = OrgSavedAttachment::where('org_branch_id', $data['org_branch_id'])
								 ->get();
		}

		$data['saved_attachments'] = NULL;

		if (!isset($mt)) {
			return $data;
		}
		$arr = array();

		if( !$noDefault ){
			$arr[''] = 'Insert Attachment';
			foreach ($mt as $key) {
				$arr[Crypt::encrypt($key->id)] = $key->name;
			}
		}else{
			foreach ($mt as $key) {
				$tm = array();
				$tm['id'] = Crypt::encrypt($key->id);
				$tm['name'] = $key->name;
				$tm['url'] = $key->url;
				$arr[] = $tm;
			}
		}

		$data['saved_attachments'] = $arr;

		return $data;
	}

	public function setCountryWithIp(){

		$usr = User::on('rds1')->whereNull('country_id')
							   ->select('id')
							   ->orderBy(DB::raw('RAND()'))
							   ->first();

		$tp = TrackingPage::on('bk-log')->where('user_id', $usr->id)
							->orderBy('id', 'DESC')
							->select('ip')
							->first();

		if (isset($tp)) {
			$arr = array();

			$arr = $this->iplookup($tp->ip);

			if (isset($arr['countryName'])) {
				$countries = Country::on('rds1')->where('country_name', $arr['countryName'])->first();
				if (isset($countries)) {

					$user = User::find($usr->id);
				 	$user->country_id = $countries->id;
				 	$user->save();
					return "success";
					//return $usr->id;
				}
			}
		}

		return "failed";
	}

	/**
	 * updateProfilePercent
	 *
	 * CRON JOB: Update profile percentage for users who it has not been updated.
	 *
	 * @return null
	 */
	public function updateProfilePercent(){

		$tmp = DB::connection('rds1')->table('users as u')
									 ->leftjoin('objectives as o', 'o.user_id', '=', 'u.id')
									 ->leftjoin('transcript as t', 't.user_id', '=', 'u.id')
									 ->leftjoin('scores as s', 's.user_id', '=', 'u.id')
									 ->where('u.profile_percent', '<', 30)
									 ->where('u.profile_percent', '>', 0)
									 //->where('u.profile_percent', '=', 0)
									 ->select('u.id as user_id', 'u.fname', 'u.city', 'u.country_id', 'u.hs_grad_year',
									 		  'u.college_grad_year', 'u.phone', 'u.profile_percent',
									 		  't.id as tid',
									 		  'o.id as oid',
									 		  's.id as sid')
									 ->groupby('u.id')
									 ->orderby(DB::raw('tid IS NULL,`tid`, sid IS NULL,`sid`,
									 				    oid IS NULL,`oid`, phone IS NULL,`phone`, RAND()'))
									 ->take(1000)
									 ->get();

		foreach ($tmp as $key) {

			$percent = 0;

			if (isset($key->tid) && !empty($key->tid)) {
				$percent += 40;
			}

			if (isset($key->sid) && !empty($key->sid)) {
				$percent += 15;
			}

			if (isset($key->oid) && !empty($key->oid)) {
				$percent += 5;
			}

			if (isset($key->fname, $key->city, $key->country_id, $key->phone)
				&& !empty($key->fname) && !empty($key->city) && !empty($key->country_id) && !empty($key->phone)) {
				$percent += 10;
			}

			if ($percent > $key->profile_percent) {
				$usr = User::find($key->user_id);
				$usr->profile_percent = $percent;

				$usr->save();
			}
			//print_r("user_id: ". $key->user_id. " percent: ". $percent." <br>");
		}
	}

	/**
	 * salesGenerator
	 *
	 * CRON JOB: Runs a cron job to generate sales data
	 *
	 * @return null
	 */
	public function salesGenerator(){

		$takeSkip = array();
		$ret      = array();

		$sc = new SalesController(true);

		if (Cache::has(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate_takeSkip')) {
			$takeSkip =  Cache::get(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate_takeSkip');
			$takeSkip['skip'] = $takeSkip['skip'] + 2;
		}else{
			$takeSkip['skip'] = 0;
			$takeSkip['take'] = 2;
		}

		// Determine if there's any more schools to run.
		// if not go back to the beginning.
		$orgs = new Organization;

		$orgs = $orgs->getOrgsAdminInfo(NULL, NULL, NULL, $takeSkip);

		if (isset($orgs) && !empty($orgs)) {
			Cache::put(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate_takeSkip', $takeSkip, 240);

			$ret = $sc->generateSalesClientReportingData($takeSkip);
		}else{

			$takeSkip['skip'] = 0;
			$takeSkip['take'] = 2;

			Cache::put(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate_takeSkip', $takeSkip, 240);

			$ret = $sc->generateSalesClientReportingData($takeSkip);
		}

		print_r($takeSkip);
    	return "success";
	}

	public function salesGeneratorCache(){

		$tmp =  Cache::get(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate_takeSkip');
		echo "<pre>";
		print_r($tmp);
		echo "</pre>";
		exit();
	}
	/**
	 * CalcIndicatorPercent
	 * calculates profile percentage based off of the information the user has provided
	 * Used in AjaxController and GetStartedController
	 *
	 * @return int
	 */
	public function CalcIndicatorPercent($user_id = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (isset($user_id)) {
			$data['user_id'] = $user_id;
		}
		$user_model = new User;
		$user_info = $user_model->whatsNextQry($data['user_id']);
		$userAI  = UsersAddtlInfo::on('rds1')->where('user_id', $user_id)->first();

		$percent = 0;
		$i = 0;
		$user;

		$user_info_bool 			 = false;
		$gpa_bool					 = false;
		$obj_bool					 = false;
		$citizenship_bool			 = false;
		$term_bool 					 = false;
		$financial_bool 			 = false;

		$completed_signup_bool		 = false;


		foreach ($user_info as $key) {
			$user = $key;


			//Get Started Step 1 check
			if ( ($user->is_student == 1 || $user->is_intl_student == 1 || $user->is_alumni == 1 || $user->is_parent == 1 || $user->is_counselor == 1 || $user->is_university_rep == 1) && (isset($user->country_id))
					&& isset($user->current_school_id) && ( isset($user->college_grad_year) || isset($user->hs_grad_year) && isset($user->in_college) ) && !$user_info_bool ){
				$percent += 20;
				$user_info_bool = true;
			}

			//Get Started Step 2 check
			if ((isset($user->hs_gpa) || isset($user->weighted_gpa) || isset($user->max_weighted_gpa) || isset($user->overall_gpa)) && !$gpa_bool){
				$percent += 20;
				$gpa_bool = true;
			}

			//Get Started Step 3 check
			if ( isset($user->gs_degree_type) && isset($user->gs_major_id) && isset($user->gs_profession_id) && isset($user->interested_school_type) && isset($user->university_location) && !$obj_bool ){
				$percent += 20;
				$obj_bool = true;
			}

			//Get Started Step 4 check
			if ( isset($user->country_id) && isset($user->city) && isset($user->address) && isset($user->state) && isset($user->zip)
				&& !$citizenship_bool ){
				$percent += 20;
				$citizenship_bool = true;
			}

			//Get Started Step 5 check
			if ( isset($user->planned_start_term) && isset($user->planned_start_yr) && isset($user->financial_firstyr_affordibility) && !$financial_bool && !$term_bool ){
				$percent += 20;
				$term_bool = true;
				$financial_bool = true;
			}

			// Completed sign up
			if (isset($user->completed_signup) && $user->completed_signup == 1 && $user->profile_percent < 30  && !$completed_signup_bool) {
				$percent = 30;
				$completed_signup_bool = true;
			}

			$i++;
		}

		if ($percent > 100) {
			$percent = 100;
		}

		if(!isset($userAI)){
			$savePercent = UsersAddtlInfo::updateOrCreate(['user_id' => $data['user_id']], ['get_started_percent' => $percent]);
			Session::put('userinfo.session_reset', 1);
		}else if( $percent > $userAI->get_started_percent ){
			$savePercent = UsersAddtlInfo::where('user_id', $data['user_id'])->update(['get_started_percent' => $percent]);
			Session::put('userinfo.session_reset', 1);
		}

		$rtcj = new RecruitmentTagsCronJob;
		$rtcj->add($data['user_id'], 'pending', 'pending');

		$ucl = new UsersClusterLog;
		$ucl->updateCluster($data['user_id'], 1);

		if ($percent >= 15) {
			$uct = new UsersCompletionTimestamp;
			$uct->addToProfileCompletionTimestamp($user_id);
		}

		return $percent;
	}
	/**
	 * CalcProfilePercent
	 * calculates Public Profile percentage based off of the information the user has provided
	 * Used in AjaxController, ProfilePageController, and SocialController
	 *
	 * return int
	 */
	public function CalcProfilePercent($user_id = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (isset($user_id)) {
			$data['user_id'] = $user_id;
		}
		$user  = User::on('rds1')->find($data['user_id']);
		$userCQ  = UsersCustomQuestion::on('rds1')->where('user_id', $user_id)->first();
		$profile_percent = 0;

		// Profile Picture check
		if(isset($user->profile_img_loc)){
	      $profile_percent += 10;
	    }

	    // Basic Info check
	    if(isset($user->fname) && isset($user->lname) && isset($user->in_college) ){
	      //&& ( isset($user->hs_grad_year) || isset($user->college_grad_year) ) ){

	      $profile_percent += 10;
	    }

	    // Education check
	    $check = UserEducation::on('rds1')
	                             ->where('user_id', $data['user_id'])
	                             ->first();
	    if(isset($check)){

	      $profile_percent += 10;
	    }

	    // Claim to Fame check
	    $check = PublicProfileClaimToFame::on('rds1')
	                                     ->where('user_id', $data['user_id'])
	                                     ->first();

	    if (isset($check)) {
			$profile_percent += 10;
	    }

	    if($user->in_college == 0){
			// Objective check
		    $check = Objective::on('rds1')
		                      ->where('user_id', $data['user_id'])
		                      ->first();

		    if (isset($check)) {
				$profile_percent += 20;
		    }
	    }else if($user->in_college == 1){
		    // Occupation check
		    $check = Occupation::on('rds1')
		                      ->where('user_id', $data['user_id'])
		                      ->first();

		    if (isset($check)) {
				$profile_percent += 20;
		    }
		}

	    // Skills check
	    $check = PublicProfileSkills::on('rds1')
	                                ->where('user_id', $data['user_id'])
	                                ->first();

	    if (isset($check)) {
			$profile_percent += 20;
	    }

	    // Projects check
	    $check = PublicProfileProjectsAndPublications::on('rds1')
	                      ->where('user_id', $data['user_id'])
	                      ->first();

	    if (isset($check)) {
			$profile_percent += 20;
	    }

		if ($profile_percent > 100) {
			$profile_percent = 100;
		}
		//if calc percent is not equal to current percent, then save new percent
		if( $profile_percent != $user->profile_percent ){
			$savePercent = User::where('id', $data['user_id'])->update(['profile_percent' => $profile_percent]);
			//$savePercent = UsersCustomQuestion::where('user_id', $data['user_id'])->update(['one_app_percent' => $profile_percent]);
			Session::put('userinfo.session_reset', 1);
		}

		$rtcj = new RecruitmentTagsCronJob;
		$rtcj->add($data['user_id'], 'pending', 'pending');

		$ucl = new UsersClusterLog;
		$ucl->updateCluster($data['user_id'], 1);

		if ($profile_percent >= 15) {
			$uct = new UsersCompletionTimestamp;
			$uct->addToProfileCompletionTimestamp($user_id);
		}

		return $profile_percent;
	}
	/**
	 * CalcOneAppPercent
	 * calculates OneApp percentage based off of the information the user has provided
	 * Used in AjaxController
	 *
	 * return int
	 */
	public function CalcOneAppPercent($user_id = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (isset($user_id)) {
			$data['user_id'] = $user_id;
		}
		$user_model = new User;

		$user  = User::on('rds1')->find($data['user_id']);
		$userCQ  = UsersCustomQuestion::on('rds1')->where('user_id', $user_id)->first();
		$userObj  = Objective::on('rds1')->where('user_id', $user_id)->first();
		$userSC = Score::on('rds1')->where('user_id', $user_id)->first();
		$userSCH = ScholarshipsUserApplied::on('rds1')->where('user_id', $user_id)->first();
		$userAC = UsersAppliedColleges::on('rds1')->where('user_id', $user_id)->first();
		$userAI = UsersAddtlInfo::on('rds1')->where('user_id', $user_id)->first();
		$userTR = Transcript::on('rds1')->where('user_id', $user_id)->get();

		$percent = 0;


		//Basic Info check
		if ( isset($user->in_college) && isset($user->current_school_id) && isset($user->gender) && (isset($user->hs_grad_year) || isset($user->college_grad_year)) && isset($userObj->degree_type) && isset($userObj->major_id) && isset($userCQ->is_transfer) ){

			$percent += 5;
		}

		//Planned Start check
		if ( isset($user->planned_start_term) && isset($user->planned_start_yr) && isset($user->interested_school_type) ){

			$percent += 5;
		}

		//Contact Info check
		if ( isset($user->email) && isset($user->phone) && isset($user->address) && isset($user->city) && isset($user->state) && isset($user->country_id) && isset($user->zip) ){

			$percent += 10;
		}

		//Citizenship check
		if ( isset($userCQ->country_of_birth) && isset($userCQ->city_of_birth) && isset($userCQ->citizenship_status) && isset($userCQ->languages) && isset($userCQ->num_of_yrs_in_us) && isset($userCQ->num_of_yrs_outside_us) ){

			$percent += 5;
		}

		//Financials check
		if ( isset($user->financial_firstyr_affordibility) && isset($user->interested_in_aid) ){

			$percent += 5;
		}

		//GPA check
		if ( isset($userSC->overall_gpa) || isset($userSC->hs_gpa) ){

			$percent += 10;
		}

		//Scores check
		if ( (isset($userSC->act_english) && isset($userSC->act_math) && isset($userSC->act_composite))
			|| (isset($userSC->sat_reading_writing) && isset($userSC->sat_math) && isset($userSC->sat_total))
			|| (isset($userSC->sat_reading) && isset($userSC->sat_writing) && isset($userSC->sat_math) && isset($userSC->sat_total))
			|| (isset($userSC->psat_reading_writing) && isset($userSC->psat_math) && isset($userSC->psat_total))
			|| (isset($userSC->psat_reading) && isset($userSC->psat_writing) && isset($userSC->psat_math) && isset($userSC->psat_total))
			|| (isset($userSC->gre_verbal) && isset($userSC->gre_quantitative) && isset($userSC->gre_analytical))
			|| isset($userSC->lsat_total) || isset($userSC->gmat_total) || isset($userSC->ap_overall) || isset($userSC->ged_score)
			//International tests
			|| (isset($userSC->toefl_reading) && isset($userSC->toefl_listening) && isset($userSC->toefl_speaking) && isset($userSC->toefl_writing) && isset($userSC->toefl_total))
			|| (isset($userSC->toefl_ibt_reading) && isset($userSC->toefl_ibt_listening) && isset($userSC->toefl_ibt_speaking) && isset($userSC->toefl_ibt_writing) && isset($userSC->toefl_ibt_total))
			|| (isset($userSC->toefl_pbt_reading) && isset($userSC->toefl_pbt_listening) && isset($userSC->toefl_pbt_written) && isset($userSC->toefl_pbt_total))
			|| (isset($userSC->ielts_reading) && isset($userSC->ielts_listening) && isset($userSC->ielts_speaking) && isset($userSC->ielts_writing) && isset($userSC->ielts_total))
			|| (isset($userSC->pte_total))
			|| (isset($userSC->other_exam) && isset($userSC->other_values))
		){
			$percent += 10;
		}

		//Demographics check
		if ( isset($user->gender) && isset($user->ethnicity) && isset($user->religion) && isset($userCQ->family_income) ){

			$percent += 5;
		}

		//Select Scholarships check
		if ( isset($userSCH) ){

			$percent += 5;
		}

		//Select Colleges check
		if ( isset($userAC) ){

			$percent += 10;
		}

		//MyCounselor check IMPLEMENT LATER
		if ( 0 == 1 ){

			$percent += 5;
		}

		//MyApplications check
		if ( 0 ==1 ){

			$percent += 5;
		}

		//*** Optional Sections ***

		//Essay check
		if (isset($userCQ->essay_content) ) {

			$percent += 5;
		}

		//Sponsor check
		if (isset($userCQ->have_sponsor) ) {

			$user->country_id == 1 ? $percent += 0 : $percent += 10;
		}

		//Uploads check
		foreach ($userTR as $key) {
			//Transcript
			if (isset($key->doc_type) && $key->doc_type == 'transcript' ) {

				$user->country_id == 1 ? $percent += 5 : $percent += 3;
			}
			//Financial
			if (isset($key->doc_type) && $key->doc_type == 'financial' ) {

				$user->country_id == 1 ? $percent += 0 : $percent += 3;
			}
			//Other
			if (isset($key->doc_type) && $key->doc_type == 'other' ) {

				$user->country_id == 1 ? $percent += 10 : $percent += 2;
			}
			//Essay
			if (!isset($userCQ->essay_content) && isset($key->doc_type) && $key->doc_type == 'essay' ) {

				$user->country_id == 1 ? $percent += 0 : $percent += 2;
			}
		}


		if ($percent > 100) {
			$percent = 100;
		}
		//if calc percent is not equal to current percent, then save new percent
		if(!isset($userCQ)){
			$savePercent = UsersCustomQuestion::updateOrCreate(['user_id' => $data['user_id']], ['one_app_percent' => $percent]);
			Session::put('userinfo.session_reset', 1);
		}else if( $percent > $userCQ->one_app_percent ){
			$savePercent = UsersCustomQuestion::where('user_id', $data['user_id'])->update(['one_app_percent' => $percent]);
			Session::put('userinfo.session_reset', 1);
		}

		$rtcj = new RecruitmentTagsCronJob;
		$rtcj->add($data['user_id'], 'pending', 'pending');

		$ucl = new UsersClusterLog;
		$ucl->updateCluster($data['user_id'], 1);

		if ($percent >= 15) {
			$uct = new UsersCompletionTimestamp;
			$uct->addToProfileCompletionTimestamp($user_id);
		}

		return $percent;
	}

	public function convertInputsForFilters($input){

		$arrObj = array();
		// convert array to an array of objects
		foreach ($input as $key => $value) {

			switch ($key) {
				case 'hasApplied':
					if (count($value) != 9) {

						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'hasApplied';
						$tmp['filter'] = 'hasApplied';
						$tmp['hasApplied'] = array();
						if (is_array($value)) {
							foreach ($value as $k => $v) {
								$tmp['hasApplied'][] = $v;
							}
						}else{
							$tmp['hasApplied'][] = $value;
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'application':
					if (count($value) != 9) {

						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'application';
						$tmp['filter'] = 'application';
						$tmp['application'] = array();
						if (is_array($value)) {
							foreach ($value as $k => $v) {
								$tmp['application'][] = $v;
							}
						}else{
							$tmp['application'][] = $value;
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'contact':

					$tmp = array();
					$tmp['type'] = 'include';
					$tmp['category'] = 'contact';
					$tmp['filter'] = 'contact';
					$tmp['contact'] = array();
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							$tmp['contact'][] = $v;
						}
					}else{
						$tmp['contact'][] = $value;
					}

					$arrObj[] = (object) $tmp;
					break;
				case 'interview':

					$tmp = array();
					$tmp['type'] = 'include';
					$tmp['category'] = 'interview';
					$tmp['filter'] = 'interview';
					$tmp['interview'] = array();
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							$tmp['interview'][] = $v;
						}
					}else{
						$tmp['interview'][] = $value;
					}

					$arrObj[] = (object) $tmp;
					break;
				case 'country_ie':
					if ( $value != 'all' && !empty($input['country']) ) {

						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'location';
						$tmp['filter'] = 'country';
						$tmp['country'] = array();
						if (is_array($input['country'])) {
							foreach ($input['country'] as $k => $v) {
								$tmp['country'][] = $v;
							}
						}else{
							$tmp['country'][] = $input['country'];
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'state_ie':
					if ( $value != 'all' && !empty($input['state']) ) {
						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'location';
						$tmp['filter'] = 'state';
						$tmp['state'] = array();
						if (is_array($input['state'])) {
							foreach ($input['state'] as $k => $v) {
								$tmp['state'][] = $v;
							}
						}else{
							$tmp['state'][] = $input['state'];
						}
						$arrObj[] = (object) $tmp;
					}
					break;
				case 'city_ie':

					if ( $value != 'all' && !empty($input['city']) ) {
						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'location';
						$tmp['filter'] = 'city';
						$tmp['city'] = array();
						if (is_array($input['city'])) {
							foreach ($input['city'] as $k => $v) {
								$tmp['city'][] = $v;
							}
						}else{
							$tmp['city'][] = $input['city'];
						}
						$arrObj[] = (object) $tmp;
					}
					break;
				case 'department_ie':

					if ( $value != 'all' && !empty($input['department']) ) {
						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'major';
						$tmp['filter'] = 'department';
						$tmp['department'] = array();
						if (is_array($input['department'])) {
							foreach ($input['department'] as $k => $v) {
								$tmp['department'][] = $v;
							}
						}else{
							$tmp['department'][] = $input['department'];
						}
						$arrObj[] = (object) $tmp;
					}
					break;
				case 'major_ie':

					if ( $value != 'all' && !empty($input['major']) ) {
						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'major';
						$tmp['filter'] = 'major';
						$tmp['major'] = array();
						if (is_array($input['major'])) {
							foreach ($input['major'] as $k => $v) {
								$tmp['major'][] = $v;
							}
						}else{
							$tmp['major'][] = $input['major'];
						}
						$arrObj[] = (object) $tmp;
					}
					break;
				case 'gpa_scores':
					/*
					if ($value[0] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'gpaMin_filter';
						$tmp['gpaMin_filter'] = array();
						$tmp['gpaMin_filter'][] = $value[0];
						$arrObj[] = (object) $tmp;
					}

					if ($value[1] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'gpaMax_filter';
						$tmp['gpaMax_filter'] = array();
						$tmp['gpaMax_filter'][] = $value[1];
						$arrObj[] = (object) $tmp;
					}
					*/



					//if both empty do not save
					if($value[0] == '' && $value[1] == ''){
						break;
					}
					else{

						//if either empty, treat it as if the user is choosing only a min or only a max
						if($value[0] == '')
							$value[0] = '0.00';
						if($value[1] == '')
							$value[1] = '5.00';

						//saving filter in array
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'gpa_filter';
						$tmp['gpa_filter'] = array();
						$tmp['gpa_filter'][0] = $value[0];
						$tmp['gpa_filter'][1] = $value[1];
						$arrObj[] = (object) $tmp;
					}

					break;
				case 'hsWeightedGPA':
					/*
					if ($value[0] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'hsWeightedGPAMin_filter';
						$tmp['hsWeightedGPAMin_filter'] = array();
						$tmp['hsWeightedGPAMin_filter'][] = $value[0];
						$arrObj[] = (object) $tmp;
					}

					if ($value[1] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'hsWeightedGPAMax_filter';
						$tmp['hsWeightedGPAMax_filter'] = array();
						$tmp['hsWeightedGPAMax_filter'][] = $value[1];
						$arrObj[] = (object) $tmp;
					}

					*/

					//if both empty do not save
					if($value[0] == '' && $value[1] == ''){
						break;
					}
					else{

						//if either empty, treat it as if the user is choosing only a min or only a max
						if($value[0] == '')
							$value[0] = '0.00';
						if($value[1] == '')
							$value[1] = '6.00';

						//saving filter in array
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'weighted_gpa_filter';
						$tmp['weighted_gpa_filter'] = array();
						$tmp['weighted_gpa_filter'][0] = $value[0];
						$tmp['weighted_gpa_filter'][1] = $value[1];
						$arrObj[] = (object) $tmp;
					}

					break;
				case 'collegeGPA':
					/*
					if ($value[0] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'collegeGPAMin_filter';
						$tmp['collegeGPAMin_filter'] = array();
						$tmp['collegeGPAMin_filter'][] = $value[0];
						$arrObj[] = (object) $tmp;
					}

					if ($value[1] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'collegeGPAMax_filter';
						$tmp['collegeGPAMax_filter'] = array();
						$tmp['collegeGPAMax_filter'][] = $value[1];
						$arrObj[] = (object) $tmp;
					}
					*/

					//if both empty do not save
					if($value[0] == '' && $value[1] == ''){
						break;
					}
					else{

						//if either empty, treat it as if the user is choosing only a min or only a max
						if($value[0] == '')
							$value[0] = '0.00';
						if($value[1] == '')
							$value[1] = '6.00';

						//saving filter in array
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'college_gpa_filter';
						$tmp['college_gpa_filter'] = array();
						$tmp['college_gpa_filter'][0] = $value[0];
						$tmp['college_gpa_filter'][1] = $value[1];
						$arrObj[] = (object) $tmp;
					}

					break;
				case 'sat_scores':
					/*
					if ($value[0] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'satMin_filter';
						$tmp['satMin_filter'] = array();
						$tmp['satMin_filter'][] = $value[0];
						$arrObj[] = (object) $tmp;
					}

					if ($value[1] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'satMax_filter';
						$tmp['satMax_filter'] = array();
						$tmp['satMax_filter'][] = $value[1];
						$arrObj[] = (object) $tmp;
					}
					*/

					//if both empty do not save
					if($value[0] == '' && $value[1] == ''){
						break;
					}
					else{

						//if either empty, treat it as if the user is choosing only a min or only a max
						if($value[0] == '')
							$value[0] = '0.00';
						if($value[1] == '')
							$value[1] = '2400';

						//saving filter in array
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'sat_filter';
						$tmp['sat_filter'] = array();
						$tmp['sat_filter'][0] = $value[0];
						$tmp['sat_filter'][1] = $value[1];
						$arrObj[] = (object) $tmp;
					}

					break;
				case 'act_scores':
					/*
					if ($value[0] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'actMin_filter';
						$tmp['actMin_filter'] = array();
						$tmp['actMin_filter'][] = $value[0];
						$arrObj[] = (object) $tmp;
					}

					if ($value[1] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'actMax_filter';
						$tmp['actMin_filter'] = array();
						$tmp['actMax_filter'][] = $value[1];
						$arrObj[] = (object) $tmp;
					}
					*/


					//if both empty do not save
					if($value[0] == '' && $value[1] == ''){
						break;
					}
					else{

						//if either empty, treat it as if the user is choosing only a min or only a max
						if($value[0] == '')
							$value[0] = '0.00';
						if($value[1] == '')
							$value[1] = '37';

						//saving filter in array
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'act_filter';
						$tmp['act_filter'] = array();
						$tmp['act_filter'][0] = $value[0];
						$tmp['act_filter'][1] = $value[1];
						$arrObj[] = (object) $tmp;
					}

					break;
				case 'toefl_scores':
					/*
					if ($value[0] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'toeflMin_filter';
						$tmp['toeflMin_filter'] = array();
						$tmp['toeflMin_filter'][] = $value[0];
						$arrObj[] = (object) $tmp;
					}

					if ($value[1] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'toeflMax_filter';
						$tmp['toeflMax_filter'] = array();
						$tmp['toeflMax_filter'][] = $value[1];
						$arrObj[] = (object) $tmp;
					}
					*/
					//if both empty do not save
					if($value[0] == '' && $value[1] == ''){
						break;
					}
					else{

						//if either empty, treat it as if the user is choosing only a min or only a max
						if($value[0] == '')
							$value[0] = '0.00';
						if($value[1] == '')
							$value[1] = '120';

						//saving filter in array
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'toefl_filter';
						$tmp['toefl_filter'] = array();
						$tmp['toefl_filter'][0] = $value[0];
						$tmp['toefl_filter'][1] = $value[1];
						$arrObj[] = (object) $tmp;
					}


					break;
				case 'ielts_scores':
					/*
					if ($value[0] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'ieltsMin_filter';
						$tmp['ieltsMin_filter'] = array();
						$tmp['ieltsMin_filter'][] = $value[0];
						$arrObj[] = (object) $tmp;
					}

					if ($value[1] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'ieltsMax_filter';
						$tmp['ieltsMax_filter'] = array();
						$tmp['ieltsMax_filter'][] = $value[1];
						$arrObj[] = (object) $tmp;
					}
					*/

					//if both empty do not save
					if($value[0] == '' && $value[1] == ''){
						break;
					}
					else{

						//if either empty, treat it as if the user is choosing only a min or only a max
						if($value[0] == '')
							$value[0] = '1';
						if($value[1] == '')
							$value[1] = '9';

						//saving filter in array
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'scores';
						$tmp['filter'] = 'ielts_filter';
						$tmp['ielts_filter'] = array();
						$tmp['ielts_filter'][0] = $value[0];
						$tmp['ielts_filter'][1] = $value[1];
						$arrObj[] = (object) $tmp;
					}

					break;
				case 'uploads':
					if (count($value) != 9) {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'uploads';
						$tmp['filter'] = 'uploads';
						$tmp['uploads'] = array();

						if (is_array($value)) {
							foreach ($value as $k => $v) {
								$tmp['uploads'][] = $v.'_filter';
							}
						} else {
							$tmp['uploads'][] = $value.'_filter';
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'age':

					if ($value[0] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'demographic';
						$tmp['filter'] = 'ageMin_filter';
						$tmp['ageMin_filter'] = array();
						$tmp['ageMin_filter'][] = $value[0];
						$arrObj[] = (object) $tmp;
					}

					if ($value[1] != '') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'demographic';
						$tmp['filter'] = 'ageMax_filter';
						$tmp['ageMax_filter'] = array();
						$tmp['ageMax_filter'][] = $value[1];
						$arrObj[] = (object) $tmp;
					}

					break;
				case 'gender':

					if ($value != 'all') {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'demographic';
						$tmp['filter'] = 'gender';
						$tmp['gender'] = array();
						$tmp['gender'][] = $value;

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'religion_ie':

					if ( $value != 'all' && !empty($input['religion']) ) {
						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'demographic';
						$tmp['filter'] = 'religion';
						$tmp['religion'] = array();
						if (is_array($input['religion'])) {
							foreach ($input['religion'] as $k => $v) {
								$tmp['religion'][] = $v;
							}
						}else{
							$tmp['religion'][] = $input['religion'];
						}
						$arrObj[] = (object) $tmp;
					}
					break;
				case 'ethnic_ie':

					if ( $value != 'all' && !empty($input['ethnic']) ) {
						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'demographic';
						$tmp['filter'] = 'ethnicity';
						$tmp['ethnicity'] = array();
						if (is_array($input['ethnic'])) {
							foreach ($input['ethnic'] as $k => $v) {
								$tmp['ethnicity'][] = $v;
							}
						}else{
							$tmp['ethnicity'][] = $input['ethnic'];
						}
						$arrObj[] = (object) $tmp;
					}
					break;
				case 'education':

					if (count($value) < 2) {

						if (isset($value[0]) && $value[0] == 'highschool') {
							$tmp = array();
							$tmp['type'] = 'include';
							$tmp['category'] = 'educationLevel';
							$tmp['filter'] = 'educationLevel';
							$tmp['educationLevel'] = array();
							$tmp['educationLevel'][] = 'hsUsers_filter';

							$arrObj[] = (object) $tmp;
						}elseif (isset($value[0]) && $value[0] == 'college') {
							$tmp = array();
							$tmp['type'] = 'include';
							$tmp['category'] = 'educationLevel';
							$tmp['filter'] = 'educationLevel';
							$tmp['educationLevel'] = array();
							$tmp['educationLevel'][] = 'collegeUsers_filter';

							$arrObj[] = (object) $tmp;
						}
					}
					break;
				case 'degree':
					if (count($value) != 9) {

						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'desiredDegree';
						$tmp['filter'] = 'desiredDegree';
						$tmp['desiredDegree'] = array();
						if (is_array($value)) {
							foreach ($value as $k => $v) {
								$tmp['desiredDegree'][] = $v;
							}
						}else{
							$tmp['desiredDegree'][] = $value;
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'inMilitary':
					$tmp = array();
					$tmp['type'] = 'include';
					$tmp['category'] = 'inMilitary';
					$tmp['filter'] = 'inMilitary';
					$tmp['inMilitary'] = array();
					$tmp['inMilitary'][] = $value;

					$arrObj[] = (object) $tmp;
					break;
				case 'militaryAffiliation':
					if (!empty($value)) {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'militaryAffiliation';
						$tmp['filter'] = 'militaryAffiliation';
						$tmp['militaryAffiliation'] = array();
						$tmp['militaryAffiliation'][] = $value;

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'profileCompletion' :
					if(!empty($value)) {
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'profileCompletion';
						$tmp['filter'] = 'profileCompletion';
						$tmp['profileCompletion'] = array();
						$tmp['profileCompletion'][] = $value;

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'name_ie':
					if ( $value != 'all' && !empty($input['name']) ) {

						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'name';
						$tmp['filter'] = 'name';
						$tmp['name'] = array();

						if (is_array($input['name'])) {
							foreach ($input['name'] as $k => $v) {
								$tmp['name'][] = $v;
							}
						}else{
							$tmp['name'][] = $input['name'];
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'startyr_ie':
					if ( $value != 'all' && !empty($input['startyr']) ) {

						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'startyr';
						$tmp['filter'] = 'startyr';
						$tmp['startyr'] = array();
						if (is_array($input['startyr'])) {
							foreach ($input['startyr'] as $k => $v) {
								$tmp['startyr'][] = $v;
							}
						}else{
							$tmp['startyr'][] = $input['startyr'];
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'startterm_ie':
					if ( $value != 'all' && !empty($input['startterm']) ) {

						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'startterm';
						$tmp['filter'] = 'startterm';
						$tmp['startterm'] = array();
						if (is_array($input['startterm'])) {
							foreach ($input['startterm'] as $k => $v) {
								$tmp['startterm'][] = $v;
							}
						}else{
							$tmp['startterm'][] = $input['startterm'];
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'financial_ie':
					if ( $value != 'all' && !empty($input['financial']) ) {

						$tmp = array();
						$tmp['type'] = $value;
						$tmp['category'] = 'financial';
						$tmp['filter'] = 'financial';
						$tmp['financial'] = array();
						if (is_array($input['financial'])) {
							foreach ($input['financial'] as $k => $v) {
								$tmp['financial'][] = $v;
							}
						}else{
							$tmp['financial'][] = $input['financial'];
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'schooltype':
					if ( $value != 'all' && !empty($input['schooltype']) ) {

						$check = false;

						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'schooltype';
						$tmp['filter'] = 'schooltype';
						$tmp['schooltype'] = array();

						if (is_array($input['schooltype'])) {
							foreach ($input['schooltype'] as $k => $v) {
								if ($v == 2 || $v == 'Both') {
									$check = true;
								}
								$tmp['schooltype'][] = $v;
							}
						}else{
							if ($input['schooltype'] == 2 || $input['schooltype'] == 'Both') {
								$check = true;
							}
							$tmp['schooltype'][] = $input['schooltype'];
						}

						// if they have selected both, then disregard, and continue
						// because logically both covers all three options which are
						// online only, on campus only and both
						if(!$check)
							$arrObj[] = (object) $tmp;
					}
					break;
				case 'startDateTerm':
					if( isset($input['startDateTerm']) && !empty($input['startDateTerm']) ){
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'startDateTerm';
						$tmp['filter'] = 'startDateTerm';
						$tmp['startDateTerm'] = array();

						if (is_array($input['startDateTerm'])) {
							foreach ($input['startDateTerm'] as $k => $v) {
								$tmp['startDateTerm'][] = $v;
							}
						}else{
							$tmp['startDateTerm'][] = $input['startDateTerm'];
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'financial':
					if( isset($input['financial']) && !empty($input['financial']) ){
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'financial';
						$tmp['filter'] = 'financial';
						$tmp['financial'] = array();
						if (is_array($input['financial'])) {
							foreach ($input['financial'] as $k => $v) {
								$tmp['financial'][] = $v;
							}
						}else{
							$tmp['financial'][] = $input['financial'];
						}

						$arrObj[] = (object) $tmp;
					}
					break;
				case 'typeofschool':
					if( isset($input['typeofschool']) && !empty($input['typeofschool']) ){
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'typeofschool';
						$tmp['filter'] = 'typeofschool';
						$tmp['typeofschool'] = array();
						$tmp['typeofschool'][] = $value;

						// if they have selected both, then disregard, and continue
						// because logically both covers all three options which are
						// online only, on campus only and both
						if ($value != 2 && $value != 'Both') {
							$arrObj[] = (object) $tmp;
						}
					}
					break;
				case 'schooltype':
					if( isset($input['schooltype']) && !empty($input['schooltype']) ){
						$tmp = array();
						$tmp['type'] = 'include';
						$tmp['category'] = 'schooltype';
						$tmp['filter'] = 'schooltype';
						$tmp['schooltype'] = array();
						$tmp['schooltype'][] = $value;

						$arrObj[] = (object) $tmp;
					}
					break;
				default:
					# code...
					break;
			}
		}

		return $arrObj;
	}

	public function validatePhoneNumber($api_input = null){
		if( isset($api_input) ){
			$input = $api_input;
		}else{
			$input = Request::all();
		}

		$phone = str_replace("/", "", $input['phone']);
		$phone = str_replace("\\", "", $phone);

		if (strpos($phone, '+') !== true){
			$phone = '+'.$phone;
		}

		$url = "https://api.plexuss.com/phone/validatePhoneNumber/".$phone;
		$client = new Client(['base_uri' => 'http://httpbin.org']);

		try {
			$response = $client->request('GET', $url);
			return json_decode($response->getBody()->getContents(), true);

			return $response;
		} catch (\Exception $e) {
			return "something bad happened";
		}
	}

	/**
	 * setRecruitModular
	 *
	 * Adds a user to handshake via their reply on email or via text message
	 * @param data : consists of fname, lname, email, user_id, college_id, school_name
	 * @param default_type (for recruitment table)
	 * @return null
	 */
	protected function setRecruitModular($data, $default_type = NULL){
		$ac = new AorCollege;
		$matches = $ac->addAORCondition($data['college_id'], $data['user_id']);

		foreach($matches as $key){

			$recAttr = array('user_id' => $data['user_id'],
							 'college_id' => $data['college_id'],
							 'aor_id' => $key['aor_id']);
			$recVal  = array('user_recruit' => 1,
							 'status' => 1);

			$recVal['reputation'] = isset($data['reputation']) ? $data['reputation'] : 0;
			$recVal['location'] = isset($data['location']) ? $data['location'] : 0;
			$recVal['tuition'] = isset($data['tuition']) ? $data['tuition'] : 0;
			$recVal['program_offered'] = isset($data['program_offered']) ? $data['program_offered'] : 0;
			$recVal['athletic'] = isset($data['athletic']) ? $data['athletic'] : 0;
			$recVal['religion'] = isset($data['religion']) ? $data['religion'] : 0;
			$recVal['onlineCourse'] = isset($data['onlineCourse']) ? $data['onlineCourse'] : 0;
			$recVal['campus_life'] = isset($data['campus_life']) ? $data['campus_life'] : 0;
			$recVal['other'] = isset($data['other']) ? $data['other'] : '';
			$recVal['type'] = isset($default_type) ? $default_type : '';

			$rec = Recruitment::updateOrCreate($recAttr,$recVal);

			if (!isset($default_type)) {
				$default_type = 'inquiry';
			}

			if(!isset($rec->type)){
				$rec->update(['type' => $default_type]);
			}

			// check if the handshake next step email should send
			if(!isset($sendHandshake) && $rec->college_recruit == 1){
				// if user is coveted and college is priority, mark to send handshake email
				$cu = CovetedUser::on('rds1')->where('user_id', $data['user_id'])->count();

				$pc = Priority::on('rds1')->where('college_id', $data['college_id']);
				if(isset($key['aor_id'])){
					$pc = $pc->where('aor_id', $key['aor_id']);
				}else{
					$pc = $pc->whereNull('aor_id');
				}
				$pc = $pc->count();

				if(!empty($pc) && !empty($cu)){
					$sendHandshake = true;
				}
			}

			if(isset($key['aor_id'])){
				$aor = Aor::find($key['aor_id']);

				// If this is a handshake contract
				if(isset($aor) && $aor->contract == 2 && $rec->college_recruit == 1){
					$this->chargeCollegePerInquiry($rec->id, null, $data['user_id'], $aor->id);
				// end of this is a hanshake contract.
				}
			}else{
				$ob = OrganizationBranch::where('school_id', $data['college_id'])->first();

				if(isset($ob)){
					// If this is a handshake contract
					if($ob->contract == 2 && $rec->college_recruit == 1){
						$this->chargeCollegePerInquiry($rec->id, $ob->id, $data['user_id']);
					}
					// end of this is a hanshake contract.
					// add notification to the college (non-AOR only)
					$ntn = new NotificationController();
					if ($rec->college_recruit == 1) {
						$ntn->create($data['fname']. ' '. $data['lname'], 'college', 2, null, $data['user_id'], $data['college_id']);
					}else{
						$ntn->create($data['fname']. ' '. $data['lname'], 'college', 1, null, $data['user_id'], $data['college_id']);
					}
					// end notification
				}
			}
		}

		// SettingNotificationName ids
		$email_snn_id = 4;
		$text_snn_id  = 11;

		// if this person has not filtered email
		$email_snn = SettingNotificationLog::on('bk')
										   ->where('type', 'email')
										   ->where('user_id', $data['user_id'])
										   ->where('snn_id', $email_snn_id)
										   ->first();

		// send the handshake next steps email
		if(isset($sendHandshake) && !isset($email_snn)){
			$mac = new MandrillAutomationController;
			$tmp = array();
			$tmp['school_name'] = $data['school_name'];
			$tmp['fname'] = $data['fname'];
			$tmp['email'] = $data['email'];
			$mac->handshakeNextSteps($tmp);
		}

		// make inquiries for the traditional school or losing AOR(s) inactive
		$rec_update = Recruitment::where('user_id',$data['user_id'])
								 ->where('college_id',$data['college_id'])
								 ->where('user_recruit',0)
								 ->update(['status' => 0]);
	}

	/**
	 * setSubstitutionData
	 *
	 * Generate automatic campaign for schools who have been idle this week
	 * @param content : the content text
	 * @param vars    : the varibles that need to be replace by the values
	 * @return (string)
	 */
	protected function setSubstitutionData($content, $vars){

		foreach ($vars as $key => $value) {
			$content = str_replace("{{".$key."}}", $value, $content);
			$content = str_replace("&nbsp;", '', $content);
		}
		return $content;
	}

	protected function rand_date_time($min_date, $max_date) {
	    /* Gets 2 dates as string, earlier and later date.
	       Returns date in between them.
	    */

	    $min_epoch = strtotime($min_date);
	    $max_epoch = strtotime($max_date);

	    $rand_epoch = rand($min_epoch, $max_epoch);

	    return date('Y-m-d H:i:s', $rand_epoch);
	}

	// UTILITY method, do NOT use.
	public function changeFinancial(){

		$users = User::on('rds1')
					 ->whereNotNull('financial_firstyr_affordibility')
					 ->where('financial_firstyr_affordibility' , '>', 0)
					 ->select('id', 'financial_firstyr_affordibility')
					 ->whereNotIn('financial_firstyr_affordibility', array('0', '0 - 5,000', '5,000 - 10,000', '10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '50,000'))
					 ->take(1000)
					 ->get();

		foreach ($users as $key) {
			$tmp_user = User::find($key->id);
			if ($key->financial_firstyr_affordibility <= 5000) {
				$tmp_user->financial_firstyr_affordibility = '0 - 5,000';
			}elseif ($key->financial_firstyr_affordibility <= 10000) {
				$tmp_user->financial_firstyr_affordibility = '5,000 - 10,000';
			}elseif ($key->financial_firstyr_affordibility <= 20000) {
				$tmp_user->financial_firstyr_affordibility = '10,000 - 20,000';
			}elseif ($key->financial_firstyr_affordibility <= 30000) {
				$tmp_user->financial_firstyr_affordibility = '20,000 - 30,000';
			}elseif ($key->financial_firstyr_affordibility <= 50000) {
				$tmp_user->financial_firstyr_affordibility = '30,000 - 50,000';
			}elseif ($key->financial_firstyr_affordibility > 50000) {
				$tmp_user->financial_firstyr_affordibility = '50,000';
			}
			$tmp_user->save();
		}
	}

	// UTILITY method, do NOT use
	public function fixUsersProfessions(){

		$users = DB::table('plexuss_logging.tracking_pages as tp')->where('tp.created_at', '>=', '2016-06-24 16:00:00')
					  ->where('u.profile_percent', 25)
					  ->join('plexuss.objectives as o', 'o.user_id', '=', 'tp.user_id')
					  ->join('plexuss.users as u', 'u.id', '=', 'o.user_id')
					  ->where('o.profession_id', 0)
					  ->where('tp.url', "https://plexuss.com/get_started/save")
					  ->where('tp.params', 'LIKE', '{"step":"3",%')
					  ->select('tp.id as tpid' ,'u.id', 'tp.params')
					  ->groupby('u.id')
					  ->limit(1000)
					  ->get();

		$del_cnt = 0;
		$add_cnt = 0;

		foreach ($users as $key) {
			$user_id = $key->id;
			$params = json_decode($key->params);

			$profession = Profession::where('profession_name', 'LIKE', "%".$params->career."%")->first();

			if (isset($profession)) {
				$obj = Objective::where('user_id', $user_id)->first();

				$obj->profession_id = $profession->id;
				$obj->save();

				$u = User::find($user_id);
				$u->profile_percent = $u->profile_percent+5;
				$u->completed_signup = 1;
				$u->save();


				$add_cnt++;

				echo "Objective for the user: ". $user_id. ' has been fixed! <br>';

			}else{
				$obj = Objective::where('user_id', $user_id)->get();

				foreach ($obj as $k) {
					$k->profession_id = NULL;
					$k->save();
				}

				$u = User::find($user_id);
				$u->completed_signup = 0;
				$u->save();

				echo "Objective for the user: ". $user_id. ' with profession name <b>'.$params->career. '</b> has been deleted! <br>';
				$del_cnt++;
			}
		}
		echo "<br> <br>Total number of Fix:  ". $add_cnt. " Total number of deletes: ".$del_cnt ;
	}

	public function getImageUrlsFromHTMLText($string){
		$matches = array();
		$dom = new domDocument;
		$dom->loadHTML($string);
		$dom->preserveWhiteSpace = false;
		$images = $dom->getElementsByTagName('img');
		foreach ($images as $image) {
		  $matches[] = $image->getAttribute('src');
		}

		return $matches;
	}

	public function trackingPixel(){

		$input = Request::all();

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

		$iplookup = $this->iplookup();
		$company = isset($input['company']) ? $input['company'] : -1;
		$paid_client = (isset($input['pc']) && $input['pc'] == 'true') ? 1 : 0;

		// $subject = "Pixel Tracked";
		// $toEmails = array('anthony.shayesteh@plexuss.com');

		// $dt = array();
		// $dt['input'] = $input;
		// $dt['ip'] = $iplookup['ip'];
		// $dt['company'] = $company;

		// $this->sendemailalert($subject, $toEmails , $dt);

		$ac = DB::table('ad_clicks')
					->where('ip', $iplookup['ip'])
					->where('company', $company)
					->update(array(
						'pixel_tracked' => 1,
						'paid_client'   => $paid_client
					));
	}

	public function ar($company, $cid, $utm_source){
		$input = array();

		$input['company'] = $company;
		$input['cid']	  = $cid;
		$input['utm_source'] = $utm_source;

		return $this->adRedirect($input);
	}

    private function getPassthroughRedirect($hashed_user_id, $input, $missingSection = 'main') {

    	$pass_through_url = '/userMissingFields/';

    	// company
    	if (isset($input['company'])) {
            $pass_through_url .= $input['company']. '/';
        }else{
        	$pass_through_url .= 'NULL/';
        }

        // cid
        if (isset($input['cid'])) {
            $pass_through_url .= $input['cid']. '/';
        }else{
        	$pass_through_url .= 'NULL/';
        }

        // uid
        if (isset($hashed_user_id)) {
            $pass_through_url .= $hashed_user_id. '/';
        }else{
        	$pass_through_url .= 'NULL/';
        }

        // uiid
        if (isset($input['uiid'])) {
            $pass_through_url .= $input['uiid']. '/';
        }else{
        	$pass_through_url .= 'NULL/';
        }

        // utm_source
        if (isset($input['utm_source'])) {
            $pass_through_url .= $input['utm_source']. '/';
        }else{
        	$pass_through_url .= 'NULL/';
        }

        return $pass_through_url . $missingSection;
    }

    private function buildIntermissionRedirect($input) {
        $company           = isset($input['company']) ? $input['company'] : 'NULL';
        $cid               = isset($input['cid']) ? $input['cid'] : 'NULL';
        $uid               = isset($input['uid']) ? $input['uid'] : 'NULL';
        $uiid              = isset($input['uiid']) ? $input['uiid'] : 'NULL';
        $utm_source        = isset($input['utm_source']) ? $input['utm_source'] : 'NULL';
        $ad_passthrough_id = 'NULL';

        $redirect_url = '/passthruIntermission/'.$company.'/'.$cid.'/'.$ad_passthrough_id.'/'.$uid.'/'.$uiid.'/'.$utm_source.'/nonpassthrough';

        return $redirect_url;
    }

    // Check if the company has a passthrough page setup
    private function hasAdPassthrough($cid) {
        if (!isset($cid)) return false;

        $has_passthrough = AdRedirectCampaign::on('rds1')
                                             ->where('id', '=', $cid)
                                             ->where('active_passthrough', '=', 1)
                                             ->exists();

        return $has_passthrough;
    }

    // Check if company has a step 2 custom question, if so, send the student there.
    public function getPassthroughCustomQuestionRedirect($input) {
        if (!isset($input['cid']) || !isset($input['hid'])) return null;

        $ip = $this->iplookup();

        try {
            $user_id = Crypt::decrypt($input['hid']);

        } catch (\Exception $e) {
            return null;
        }

        $customQuestion = PassthroughCustomQuestion::on('rds1')
                                                       ->select('id', 'question_name')
                                                       ->where('ad_redirect_campaign_id', $input['cid'])
                                                       ->first();

        if (!$customQuestion) return null;

        switch ($customQuestion->question_name) {
            case 'ielts':
                $ucq = UsersCustomQuestion::on('rds1')
                                         ->select('plan_to_take_ielts')
                                         ->whereNotNull('plan_to_take_ielts')
                                         ->where('user_id', '=', $user_id)
                                         ->first();

                if (!isset($ucq) || !isset($ucq->plan_to_take_ielts)) {
                    return $this->getPassthroughRedirect($input['hid'], $input, 'custom_question_ielts');

                }

                $answer = $ucq->plan_to_take_ielts;

                $redirect_url = $this->getPassthroughRedirect($input['hid'], $input, 'not_qualified_ielts');

                break;

            case 'financials':
                $user = User::on('rds1')
                            ->select('financial_firstyr_affordibility', 'country_id')
                            ->where('id', '=', $user_id)
                            ->first();

                if (isset($ip) && isset($ip['countryAbbr'])) {
                    $country = Country::on('rds1')->select('toggle_passthrough_financials')->where('country_code', '=', $ip['countryAbbr'])->first();

                } else if (isset($user->country_id)) {
                    $country = Country::on('rds1')->select('toggle_passthrough_financials')->where('id', '=', $user->country_id)->first();
                }

                if (isset($country) && isset($country->toggle_passthrough_financials) && $country->toggle_passthrough_financials == 0) {
                    return null;
                }

                if (!isset($user->financial_firstyr_affordibility)) {
                    return $this->getPassthroughRedirect($input['hid'], $input, 'custom_question_financials');
                }

                $answer = $user->financial_firstyr_affordibility;

                $redirect_url = $this->getPassthroughRedirect($input['hid'], $input, 'not_qualified_financials');

                break;

            default:
                return null;
        }

        $isAccepted = PassthroughCustomQuestionsAcceptedAnswers::on('rds1')
                        ->where('pcq_id', '=', $customQuestion->id)
                        ->where('value', '=', $answer)
                        ->exists();

        // If not accepted, send to not qualified step
        if (isset($isAccepted) && $isAccepted == false) {
            return $redirect_url;
        }

        // If accepted, just return null (Continue normal flow)
        return null;
    }

	public function adRedirect($input = NULL){
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

		if (!isset($input)) {
			$input = Request::all();
		}

		if (!isset($input['cid']) || !isset($input['utm_source']) || !isset($input['company'])) {
			return "inputs are required";
		}

        $hasPassthrough = $this->hasAdPassthrough($input['cid']);

        if ((!isset($input['pass_through']) || $input['pass_through'] === 'true') && $hasPassthrough === true) {
            $hashed_user_id = null;

            // The user_id can be obtained from the following in order of priority.
            if (isset($data['user_id']) && $data['user_id'] != -1) {
                try {
                    $hashed_user_id = Crypt::encrypt($data['user_id']);
                } catch (\Exception $e) {
                    // Nothing
                }

                if ($input['company'] === 'music_inst') {
                    if (isset($input['hid']) && $input['hid'] != -1) {
                        $hashed_user_id = $input['hid']; // Encrypted

                        // Test decrypt it, if not valid just skip out and redirect to ad.
                        try {
                            $test_user_id = Crypt::decrypt($hashed_user_id);

                            if ($test_user_id == -1) {
                                $invalid_hash = true;
                            }
                        } catch (\Exception $e) {
                            $invalid_hash = true;
                        }
                    } else {
                        $skip_passthrough = true;
                    }
                }

            } else if (isset($input['hid']) && $input['hid'] != -1) {
                $hashed_user_id = $input['hid']; // Encrypted

                // Test decrypt it, if not valid just skip out and redirect to ad.
                try {
                    $test_user_id = Crypt::decrypt($hashed_user_id);

                    if ($test_user_id == -1) {
                        $invalid_hash = true;
                    }
                } catch (\Exception $e) {
                    $invalid_hash = true;
                }
            }

            $missingSection = $this->isThereUserMissingData($hashed_user_id, $input['cid']);

            if (!isset($skip_passthrough) && isset($missingSection) && $missingSection !== false) {
                $hid = isset($hashed_user_id) ? $hashed_user_id : NULL;

                if (isset($invalid_hash) && $invalid_hash == true) {
                    $hid = NULL;
                }

                $url = $this->getPassthroughRedirect($hid, $input, $missingSection);

                return redirect($url);
            }

        }

        /// Send to passthruIntermission if got here and pass_thru was not set as false.
        if (!isset($input['passthru_intermission']) || $input['passthru_intermission'] == 'false') {
            $intermission_redirect = $this->buildIntermissionRedirect($input);
            return redirect($intermission_redirect);
        }

		$ac = new AjaxController();
		$res = $ac->adClicked($input);

		if (is_array($res)) {
			if (isset($res['url'])) {
				return redirect($res['url']);
			}
		}else{
			if ($res == 'success' && !isset($input['url'])) {
				$arc = AdRedirectCampaign::on('rds1')
						                         ->where('id', $input['cid'])
						                         ->where('company', $input['company'])
						                         ->first();

				if (isset($arc)) {
					if (isset($arc->dc_id)) {

						$viewDataController = new ViewDataController();
						$data = $viewDataController->buildData();

						$this_user_id = NULL;
                        if (!isset($invalid_hash) && isset($hashed_user_id)) {
                            // $user = User::find(Crypt::decrypt($hashed_user_id)); // hashed_user_id is already validated.
                            $this_user_id = Crypt::decrypt($hashed_user_id);
                        } else if (isset($input['hid'])) {
                            try {
                                $unhashed_user_id = Crypt::decrypt($input['hid']);

                                if ($unhashed_user_id !== -1 && $unhashed_user_id !== NULL && $unhashed_user_id != 'NULL') {
                                    // $user = User::find($unhashed_user_id);
                                    $this_user_id = $unhashed_user_id;
                                } else if (isset($data['user_id'])) {
                                    // $user = User::find($data['user_id']);
                                    $this_user_id = $data['user_id'];
                                }

                            } catch (\Exception $e) {
                                if (isset($data['user_id'])) {
                                    // $user = User::find($data['user_id']);
                                    $this_user_id = $data['user_id'];
                                }
                            }
                        } else {
						    // $user = User::find($data['user_id']);
						    $this_user_id = $data['user_id'];
                        }
                        if (isset($arc->dc_id) && $this_user_id) {
                        	$dc = new DistributionController();
							$arc->url = $dc->generateLinkoutUrl($arc->dc_id, $this_user_id);
                        }
					}
					$url = str_replace("SOURCE_GOES_HERE", $input['utm_source'], $arc->url);
					return redirect($url);
				}else{
					return "Ad was not found!";
				}
			}elseif ($res == "failed") {
				return redirect("https://plexuss.com/college-application");
			}else{
				return redirect($input['url']);
			}

		}
	}

	// This method is made for adRedirect to make sure the user is missing one or more required field.
    // Returns section name or false
	private function isThereUserMissingData($hashed_user_id, $cid){
        if (!isset($hashed_user_id)) {
            return 'main'; // If not set, a new user.
        }

        try {
            $user_id = Crypt::decrypt($hashed_user_id);
        } catch (\Exception $e) {
            return false; // Invalid hashed_user_id, just skip to ad.
        }

        $adRedirectCampaign = AdRedirectCampaign::on('rds1')->where('id', '=', $cid)->first();

        if (!$adRedirectCampaign) return false; // Invalid $cid

        $ip = $this->iplookup();

		$qry = DB::connection('rds1')->table('users as u')
									 ->leftjoin('scores as s', 's.user_id', '=', 'u.id')
                                     ->leftjoin('objectives as o', 'o.user_id', '=', 'u.id')
                                     ->leftjoin('users_custom_questions as ucq', 'ucq.user_id', '=', 'u.id')
									 ->where('u.id', $user_id)
									 ->select('u.fname', 'u.lname', 'u.email', 'u.phone', 'u.address', 'u.city', 'u.state',     'u.zip', 'country_id', 'gender', 'birth_date',

                                                 /* NEW AFTER THIS */
                                                 'interested_school_type', 'financial_firstyr_affordibility', 'interested_in_aid', 'in_college',


                                                 'is_university_rep', 'is_student', 'is_alumni', 'is_parent', 'current_school_id', 'is_counselor',


                                                 'current_school_id', 'hs_grad_year', 'college_grad_year',

                                                 'o.degree_type', 'o.major_id', 'o.profession_id', 'ucq.is_transfer', 'o.university_location',

                                                 /* NEW BEFORE THIS */
									 		  DB::raw("IF(u.in_college = 1, s.overall_gpa, s.hs_gpa) as gpa"))
									 ->first();

        if (isset($qry->country_id)) {
            $from_united_states = $qry->country_id == 1;
        } else {
            $from_united_states = isset($ip['countryAbbr']) && $ip['countryAbbr'] == 'US';
        }

		$check = false;
		((!isset($qry->fname) || empty($qry->fname)) && $check == false)  		? $check = true : NULL;
		((!isset($qry->lname) || empty($qry->lname)) && $check == false)  		? $check = true : NULL;
		((!isset($qry->email) || empty($qry->email)) && $check == false )  		? $check = true : NULL;
		((((!isset($qry->birth_date) || empty($qry->birth_date)) || $qry->birth_date === '0000-00-00') && $adRedirectCampaign['toggle_birth_date'] != 0) && $check == false)  	? $check = true : NULL;

        if ($check) {
            return 'main';
        }

        if (!empty($this->isUserPassthruNrccuaTarget($user_id))) {
            ($check == false && $this->checkIfMissingMainSection($qry, $adRedirectCampaign)) ? $check = $this->checkIfMissingMainSection($qry, $adRedirectCampaign) : NULL;
        }

        //////////////// Check personal section: ////////////////
        ($check == false && $this->checkIfMissingPersonalSection($qry)) ? $check = $this->checkIfMissingPersonalSection($qry) : NULL;

        //////////////// Contact section: ////////////////
        ($check == false && $this->checkIfMissingContactSection($qry, $from_united_states)) ? $check = $this->checkIfMissingContactSection($qry, $from_united_states) : NULL;


        // Priority for United States is different from international. Check Scores before Goals for US. Opposite for Intl.
        if (isset($from_united_states) && $from_united_states) {
            //////////////// Check scores section: ////////////////
            ($check == false && $this->checkIfMissingScoresSection($qry)) ? $check = $this->checkIfMissingScoresSection($qry) : NULL;

            //////////////// Check goals section: ////////////////
            ($check == false && $this->checkIfMissingGoalsSection($qry)) ? $check = $this->checkIfMissingGoalsSection($qry) : NULL;
        } else {
            //////////////// Check goals section: ////////////////
            ($check == false && $this->checkIfMissingGoalsSection($qry)) ? $check = $this->checkIfMissingGoalsSection($qry) : NULL;

            //////////////// Check scores section: ////////////////
            ($check == false && $this->checkIfMissingScoresSection($qry)) ? $check = $this->checkIfMissingScoresSection($qry) : NULL;
        }

        ////////////////Check preferences section: ////////////////
        ($check == false && $this->checkIfMissingPreferencesSection($qry)) ? $check = $this->checkIfMissingPreferencesSection($qry) : NULL;

		return $check;
	}

    private function isUserPassthruNrccuaTarget($user_id) {
        $query = DB::connection('rds1')->select("
                    select id
                    from plexuss.users u
                    where country_id = 1
                        and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
                        and not exists
                            (Select user_id from plexuss.country_conflicts cc where u.id = cc.user_id)
                        and year(birth_date) between (year(current_date()) - 18) and (year(current_date()) - 14)
                        and is_plexuss = 0
                        and is_organization = 0
                        and is_ldy = 0
                        and is_university_rep = 0
                        and is_aor = 0
                        and is_university_rep = 0
                        and (in_college = 0 or in_college is null)
                        and u.id = ".$user_id."
                        limit 1;");
        return $query;
    }

    private function checkIfMissingMainSection($qry, $adRedirectCampaign) {
        $check = false;

        ((!isset($qry->fname) || empty($qry->fname)) && $check == false)        ? $check = true : NULL;
        ((!isset($qry->lname) || empty($qry->lname)) && $check == false)        ? $check = true : NULL;
        ((!isset($qry->email) || empty($qry->email)) && $check == false)        ? $check = true : NULL;
        ((!isset($qry->address) || empty($qry->address)) && $check == false)      ? $check = true : NULL;
        ((!isset($qry->city) || empty($qry->city)) && $check == false)       ? $check = true : NULL;
        ((!isset($qry->state) || empty($qry->state)) && $check == false)        ? $check = true : NULL;
        ((!isset($qry->zip) || empty($qry->zip)) && $check == false)         ? $check = true : NULL;
        ((!isset($qry->country_id) || empty($qry->country_id)) && $check == false)    ? $check = true : NULL;
        ((!isset($qry->gender) || empty($qry->gender)) && $check == false)        ? $check = true : NULL;
        ((((!isset($qry->birth_date) || empty($qry->birth_date)) || $qry->birth_date === '0000-00-00')) && $check == false)    ? $check = true : NULL;
        ((!isset($qry->gpa) || empty($qry->gpa)) && $check == false)        ? $check = true : NULL;
        ((!isset($qry->phone) || empty($qry->phone) || $qry->phone == ' ' ) && $check == false && $adRedirectCampaign['toggle_phone'] == 0)        ? $check = true : NULL;

        return $check ? 'main_override' : false;

    }

    // Returns section name or false. (Used with isThereUserMissingData)
    private function checkIfMissingPersonalSection($qry) {
        $check = false;

        if ($qry->is_university_rep == 0 && $qry->is_student == 0 && $qry->is_alumni == 0 && $qry->is_university_rep == 0 && $qry->is_parent == 0 && $qry->is_counselor == 0) {
            $check = true;
        }

        ((!isset($qry->gender) || empty($qry->gender)) && $check == false)          ? $check = true : NULL;
        (!isset($qry->in_college) && $check == false) ? $check = true : NULL;
        ((!isset($qry->current_school_id) || empty($qry->current_school_id)) && $check == false) ? $check = true : NULL;

        if (isset($qry->in_college) && $qry->in_college == 0) {
            ((!isset($qry->hs_grad_year) || empty($qry->hs_grad_year)) && $check == false) ? $check = true : NULL;

        } else {
            ((!isset($qry->college_grad_year) || empty($qry->college_grad_year)) && $check == false) ? $check = true : NULL;
        }

        return $check ? 'personal' : false;
    }

    // Returns section name or false. (Used with isThereUserMissingData)
    private function checkIfMissingContactSection($qry, $from_united_states) {
        $check = false;

        ((!isset($qry->address) || empty($qry->address)) && $check == false)    ? $check = true : NULL;
        ((!isset($qry->city) || empty($qry->city)) && $check == false)          ? $check = true : NULL;
        ((!isset($qry->country_id) || empty($qry->country_id)) && $check == false)  ? $check = true : NULL;
        ((!isset($qry->phone) || empty($qry->phone)) && $check == false)        ? $check = true : NULL;

        // The below fields are only required for US users.
        if (isset($from_united_states) && $from_united_states) {
            ((!isset($qry->state) || empty($qry->state)) && $check == false)        ? $check = true : NULL;
            ((!isset($qry->zip) || empty($qry->zip)) && $check == false)        ? $check = true : NULL;
        }

        return $check ? 'contact' : false;
    }

    // Returns section name or false. (Used with isThereUserMissingData)
    private function checkIfMissingScoresSection($qry) {
        $check = false;

        ((!isset($qry->gpa) || empty($qry->gpa)) && $check == false) ? $check = true : NULL;

        return $check ? 'scores' : false;
    }

    // Returns section name or false. (Used with isThereUserMissingData)
    private function checkIfMissingGoalsSection($qry) {
        $check = false;

        ((!isset($qry->degree_type) || empty($qry->degree_type)) && $check == false) ? $check = true : NULL;
        ((!isset($qry->major_id) || empty($qry->major_id)) && $check == false) ? $check = true : NULL;
        ((!isset($qry->profession_id) || empty($qry->profession_id)) && $check == false) ? $check = true : NULL;
        (!isset($qry->financial_firstyr_affordibility) && $check == false) ? $check = true : NULL;
        (!isset($qry->interested_in_aid) && $check == false) ? $check = true : NULL;

        return $check ? 'goals' : false;
    }

    // Returns section name or false. (Used with isThereUserMissingData)
    private function checkIfMissingPreferencesSection($qry) {
        $check = false;

        (!isset($qry->is_transfer) && $check == false) ? $check = true : NULL;
        (!isset($qry->interested_school_type) && $check == false) ? $check = true : NULL;
        ((!isset($qry->university_location) || empty($qry->university_location)) && $check == false) ? $check = true : NULL;

        return $check ? 'preferences' : false;
    }

	public function generateRandBingImage(){

		$miq = MicrosoftImageQuery::orderBy(DB::raw('RAND()'))->first();

		$url = "https://api.cognitive.microsoft.com/bing/v7.0/images/search";
		$client = new Client(['base_uri' => 'http://httpbin.org']);

		//q is the news search query (colleges.school_name), count->take, offset->skip
		//backup subscription key - 7f4cd30cc8e14f9b87162aaac8e18256
		try {
			$response = $client->request('GET', $url, ['headers' => [
		        'Ocp-Apim-Subscription-Key'	=> '6df34635ac4f4b42a9392a4036dea783'],
		        'query' => [
				'q' => $miq->query,
			    'count' => '20',
			    'offset' => '0',
			    'mkt' => 'en-us',
			    'safeSearch' => 'Moderate',
			    'size' => 'Large']]);

			$imgQuery = json_decode($response->getBody()->getContents(), true)['value'];
		} catch (\Exception $e) {
			$imgQuery = array();
		}


		$arr = array();
		foreach ($imgQuery as $key) {

			if ($key['width'] >= 1600) {
				$arr[] = $key['contentUrl'];
			}
		}

		$ret = '';
		if (empty($arr)) {
			$ret = 'http://www.sftravel.com/sites/sftraveldev.prod.acquia-sites.com/files/SanFrancisco_0.jpg';
		}else{
			$rd = rand(0, count($arr)-1);
			$ret = $arr[$rd];
		}

		return $ret;
	}

    public function getBingBackground($query) {
        $url = "https://api.cognitive.microsoft.com/bing/v7.0/images/search";
        $client = new Client(['base_uri' => 'http://httpbin.org']);

        //backup subscription key - 7f4cd30cc8e14f9b87162aaac8e18256
        try {
            $response = $client->request('GET', $url, ['headers' => [
                'Ocp-Apim-Subscription-Key' => '6df34635ac4f4b42a9392a4036dea783'],
                'query' => [
                'q' => $query,
                'count' => '20',
                'offset' => '0',
                'mkt' => 'en-us',
                'safeSearch' => 'Moderate',
                'size' => 'Large']]);

            $imgQuery = json_decode($response->getBody()->getContents(), true)['value'];
        } catch (\Exception $e) {
            $imgQuery = array();
        }


        $arr = array();
        foreach ($imgQuery as $key) {

            if ($key['width'] >= 1600) {
                $arr[] = $key['contentUrl'];
            }
        }

        $ret = '';
        if (empty($arr)) {
            $ret = 'http://www.sftravel.com/sites/sftraveldev.prod.acquia-sites.com/files/SanFrancisco_0.jpg';
        }else{
            $rd = rand(0, count($arr)-1);
            $ret = $arr[$rd];
        }

        return $ret;
    }

	// UTILITY method, do NOT use
	public function inviteWebinarUsers(){
		$now = Carbon::now();

		$current_time = $now->format('H:i a');
		$start = "04:00 am";
		$end = "14:00 pm";
		$date1 = DateTime::createFromFormat('H:i a', $current_time);
		$date2 = DateTime::createFromFormat('H:i a', $start);
		$date3 = DateTime::createFromFormat('H:i a', $end);

		if ($date1 > $date2 && $date1 < $date3){

			$qry = DB::connection('bk')->table('users as u')
									   ->join('webinar_invite as wi', 'u.id', '=', 'wi.user_id')
									   ->where('u.email', 'NOT LIKE', '%plexuss%')
									   ->where('u.email', 'LIKE', '__%@_%._%')
									   ->where('u.is_organization', 0)
									   ->where('u.is_agency', 0)
									   ->where('u.is_university_rep', 0)
									   ->where('u.is_plexuss', 0)
									   ->where('wi.email_sent', 0)
									   ->whereRaw('u.id NOT IN (select user_id from webinar_rsvp where event_id = 6)')
									   ->whereRaw('(u.religion not in (35, 1, 2) or religion is null)')
									   ->groupby('u.id')
									   ->take(8000)
									   ->select('wi.id as wi_id', 'fname', 'lname', 'email')
									   ->get();

			$mac = new MandrillAutomationController;

			foreach ($qry as $key) {
				$input = array();
				$input['email'] = $key->email;
				$input['fname'] = $key->fname;

				$wi = WebinarInvite::find($key->wi_id);

				$wi->email_sent = 1;
				$wi->save();

				$mac->webinarInitialInvite($input);
			}

			return "success";
		}
		return "failed";
	}

	// UTILITY method, do NOT use
	public function inviteWebinarFifteenMin(){

		$today = date("Y-m-d");

		if ($today == '2017-03-01') {
			$qry = DB::connection('bk')->table('users as u')
									   ->join('webinar_rsvp as wr', 'u.id', '=', 'wr.user_id')
									   ->where('wr.event_id', 6)
									   ->groupby('wr.user_id')
									   ->orderBy('wr.id')
									   ->select('u.email','u.fname')
									   ->get();

			$mac = new MandrillAutomationController;

			foreach ($qry as $key) {
				$input = array();
				$input['email'] = $key->email;
				$input['fname'] = $key->fname;
				$mac->webinarFifteenMinBefore($input);
			}

			return "success";
		}

		return "failed";
	}

	// UTILITY method , do NOT use
	public function retrainModels(){

		exec('python '.public_path().'/python/retrain.py', $return, $status);

        return "success";
	}

	// UTILITY method , do NOT use
	public function sendReminders(){
		exec('python '.public_path().'/python/reminders.py', $return, $status);

        return "success";
	}

	// UTILITY method , do NOT use
	public function sendPrepData(){
		exec('python '.public_path().'/python/prep.py', $return, $status);

        return "success";
	}

	/*
	 * Cron job to set users to portals.
	 *
	 */
	public function setRecruitmentTag(){

		$now = Carbon::now();

		$current_time = $now->format('H:i a');
		$start = "11:30 am";
		$end   = "23:59 pm";
		$date1 = DateTime::createFromFormat('H:i a', $current_time);
		$date2 = DateTime::createFromFormat('H:i a', $start);
		$date3 = DateTime::createFromFormat('H:i a', $end);

		ini_set('max_execution_time', 800);
		$ob = OrganizationBranch::on('rds1')->get();

		$crc = new CollegeRecommendationController;

		foreach ($ob as $key) {
			$rec = DB::connection('bk')->table('recruitment as r')
										 ->leftjoin('recruitment_tags as rt', function($join){
										 	$join->on('r.user_id', '=', 'rt.user_id');
										 	$join->on('r.college_id', '=', 'rt.college_id');
										 })
										 ->where('r.college_id', $key->school_id)
										 ->whereNull('rt.id')
										 ->select('r.user_id', 'r.college_id')
										 ->get();

			foreach ($rec as $k) {
				$tmp = $crc->findPortalsForThisUserAtThisCollege($k->user_id, $k->college_id);
				$this->insertIntoRecruitmentTag($tmp, $k->user_id, $k->college_id);
			}
		}

		// if ($date1 > $date2 && $date1 < $date3){}
	}

	// This method checks to see if the general tagged user ids can be tagged.

	public function checkGeneralRecruitmentTag(){

		$takeSkip = array();

		if (Cache::has(env('ENVIRONMENT') .'_'.'checkGeneralRecruitmentTag_takeSkip')) {
			$takeSkip =  Cache::get(env('ENVIRONMENT') .'_'.'checkGeneralRecruitmentTag_takeSkip');
			$takeSkip['skip'] = $takeSkip['skip'] + 5000;
		}else{
			$takeSkip['skip'] = 0;
			$takeSkip['take'] = 5000;
		}

		$rt = RecruitmentTag::on('rds1')->take($takeSkip['take'])
										->skip($takeSkip['skip'])
										->where('aor_id', -1)
										->where('org_portal_id', -1)
										->where('aor_portal_id', -1)
										->get();

		$cnt_rec = RecruitmentTag::on('rds1')->select('id')->orderBy('id', 'desc')->first();


		if ($takeSkip['skip'] >= $cnt_rec->id ) {
			$takeSkip['skip'] = 0;
			$takeSkip['take'] = 5000;

			$rt = RecruitmentTag::on('rds1')->take($takeSkip['take'])
										->skip($takeSkip['skip'])
										->where('aor_id', -1)
										->where('org_portal_id', -1)
										->where('aor_portal_id', -1)
										->get();
		}
		$crc = new CollegeRecommendationController;

		$cnt = 0;
		$changes_arr = array();

		foreach ($rt as $k) {

			$tmp = $crc->findPortalsForThisUserAtThisCollege($k->user_id, $k->college_id);

			if (isset($tmp) && !empty($tmp)) {

				$del = RecruitmentTag::find($k->id);
				$del->delete();

				$tmp = $this->insertIntoRecruitmentTag($tmp, $k->user_id, $k->college_id);

				$arr = array();
				$arr['user_id'] = $k->user_id;
				$arr['college_id'] = $k->college_id;
				$arr['id'] = $tmp->id;

				$changes_arr[] = $arr;

				$cnt++;
			}
		}

		Cache::put(env('ENVIRONMENT') .'_'.'checkGeneralRecruitmentTag_takeSkip', $takeSkip, 240);

		$ret = array();
		$ret['msg'] = 'numnber of changes => '.$cnt;
		$ret['whereAt'] = "take => ". $takeSkip['take'] . " skip => ".$takeSkip['skip'];
		$ret['changes_arr'] = $changes_arr;

		return json_encode($ret);
	}

	public function checkTaggedRecruitmentTag(){

		$takeSkip = array();

		if (Cache::has(env('ENVIRONMENT') .'_'.'checkTaggedRecruitmentTag_takeSkip')) {
			$takeSkip =  Cache::get(env('ENVIRONMENT') .'_'.'checkTaggedRecruitmentTag_takeSkip');
			$takeSkip['skip'] = $takeSkip['skip'] + 5000;
		}else{
			$takeSkip['skip'] = 0;
			$takeSkip['take'] = 5000;
		}

		$rt = RecruitmentTag::on('rds1')->take($takeSkip['take'])
										->skip($takeSkip['skip'])
										->where(function($q){
											$q->orWhere('aor_id', '!=', -1)
											  ->orWhere('org_portal_id', '!=', -1);
										})
										->get();

		$cnt_rec = RecruitmentTag::on('rds1')->select('id')->orderBy('id', 'desc')->first();


		if ($takeSkip['skip'] >= $cnt_rec->id ) {

			$takeSkip['skip'] = 0;
			$takeSkip['take'] = 5000;

			$rt = RecruitmentTag::on('rds1')->take($takeSkip['take'])
										->skip($takeSkip['skip'])
										->where(function($q){
											$q->orWhere('aor_id', '!=', -1)
											  ->orWhere('org_portal_id', '!=', -1);
										})
										->get();
		}

		$crc = new CollegeRecommendationController;

		$cnt = 0;
		$changes_arr = array();

		foreach ($rt as $k) {

			$tmp = $crc->findPortalsForThisUserAtThisCollege($k->user_id, $k->college_id);

			if (empty($tmp)) {

				$del = RecruitmentTag::find($k->id);
				$del->delete();

				$tmp = $this->insertIntoRecruitmentTag($tmp, $k->user_id, $k->college_id);

				$arr = array();
				$arr['user_id'] = $k->user_id;
				$arr['college_id'] = $k->college_id;
				$arr['id'] = $tmp->id;

				$changes_arr[] = $arr;

				$cnt++;
			}else{

				$founded = false;
				foreach ($tmp as $key) {
					if ($key['aor_id'] == $k->aor_id && $key['org_portal_id'] == $k->org_portal_id && $key['aor_portal_id'] == $k->aor_portal_id ) {
						$founded = true;
						break;
					}
				}

				if (!$founded) {
					$del = RecruitmentTag::find($k->id);
					$del->delete();

					$tmp = $this->insertIntoRecruitmentTag($tmp, $k->user_id, $k->college_id);

					$arr = array();
					$arr['user_id'] = $k->user_id;
					$arr['college_id'] = $k->college_id;
					$arr['id'] = $tmp->id;

					$changes_arr[] = $arr;

					$cnt++;
				}
			}
		}

		Cache::put(env('ENVIRONMENT') .'_'.'checkTaggedRecruitmentTag_takeSkip', $takeSkip, 240);

		$ret = array();
		$ret['msg'] = 'numnber of changes => '.$cnt;
		$ret['whereAt'] = "take => ". $takeSkip['take'] . " skip => ".$takeSkip['skip'];
		$ret['changes_arr'] = $changes_arr;

		return json_encode($ret);
	}

	public function addTargettingUsersToRecruitmentTagCronJob(){

		$arr = Cache::get(env('ENVIRONMENT').'_'.'addTargettingUsersToRecruitmentTagCronJob');
		$ret = $arr;
		if (isset($arr) && !empty($arr)) {
			foreach ($arr as $key => $value) {

				$rec = Recruitment::on('rds1')->where('college_id', $value);

				if (isset($aor_id)) {
					$rec->where('aor_id', $aor_id);
				}else{
					$rec->whereNull('aor_id');
				}

				$rec = $rec->select('user_id')->get();


				$rtcj = new RecruitmentTagsCronJob;
				foreach ($rec as $k) {
					$rtcj->add($k->user_id, 'pending', 'pending');
				}

				$arr = array_splice($arr, 1, $key);
				Cache::forever(env('ENVIRONMENT').'_'.'addTargettingUsersToRecruitmentTagCronJob', $arr);

			}
		}

		return json_encode($ret);
	}

	private function insertIntoRecruitmentTag($tmp, $user_id, $college_id){

		if (empty($tmp)) {
			$attr = array('user_id' => $user_id, 'college_id' => $college_id);
			$val  = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => -1,
						  'org_portal_id' => -1, 'aor_portal_id' => -1);

			$update = RecruitmentTag::updateOrCreate($attr, $val);
		}else{

			foreach ($tmp as $key) {

				$attr = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => $key['aor_id'],
						  'org_portal_id' => $key['org_portal_id'], 'aor_portal_id' => $key['aor_portal_id']);
				$val  = array('user_id' => $user_id, 'college_id' => $college_id, 'aor_id' => $key['aor_id'],
						  'org_portal_id' => $key['org_portal_id'], 'aor_portal_id' => $key['aor_portal_id']);

				$update = RecruitmentTag::updateOrCreate($attr, $val);
			}

		}

		return $update;
	}

	/*
	 * setUserTargettedForPickACollege
	 * This method sets a pick a college view to targetted or not based on targetting of that college
	 */
	public function setUserTargettedForPickACollege(){

		$crc = new CollegeRecommendationController;

		$pacv = PickACollegeView::where('targetted', 0)
								->orderby('id', 'DESC')
								->take(1000)
								->get();

		foreach ($pacv as $key) {
			$tmp = $crc->findPortalsForThisUserAtThisCollege($key->user_id, $key->college_id);

			if (empty($tmp)) {
				$key->targetted = -1;
				$key->save();
			}else{
				$key->targetted = 1;
				$key->save();
			}
		}
	}

	public function custom_number_format($n, $precision = 1) {
	    if ($n < 1000) {
	        // Anything less than a thousand
	        $n_format = number_format($n);
	    } else if ($n < 1000000) {
	        // Anything less than a million
	        $n_format = number_format($n / 1000, $precision) . 'K';
	    } else if ($n < 1000000000) {
	        // Anything less than a billion
	        $n_format = number_format($n / 1000000, $precision) . 'M';
	    } else {
	        // At least a billion
	        $n_format = number_format($n / 1000000000, $precision) . 'B';
	    }

	    return $n_format;
	}

	//changePriorityTier
	public function changePriorityTier(){
		$rec = new Recruitment;
  		$pr = new Priority;
		$pr = $pr->getPrioritySchools();

		foreach ($pr as $key) {

			$tmp = array();

			$goal		= $key->goal;
			$start_date	= $key->start_goal;
			$end_date	= $key->end_goal;
			$tier       = $key->tier;
			$org_tier	= $key->org_tier;

			$tmp['start_date']	= $key->start_goal;
			$tmp['end_date']	= $key->end_goal;

			if (isset($tmp['start_date'])) {
				$start_date = $tmp['start_date']." 00:00:00";
			}else{
				$start_date = NULL;
			}

			if (isset($tmp['end_date'])) {
				$end_date = $tmp['end_date']." 23:59:59";
			}else{
				$end_date = NULL;
			}

			$tmp['handshakes']	= 0;

			$handshakes 		= $rec->getNumOfTotalApprovedForColleges(array($key->college_id), $start_date, $end_date, null, $key->aor_id, 'inquiry_pick_a_college');
			foreach ($handshakes as $k) {
				$tmp['handshakes']	= $k->cnt;
			}

			// print_r('college_id: '. $key->college_id. ' aor_id: '.$key->aor_id. ' school_name: '. $key->school_name. ' '.  $goal. ' '. $tmp['handshakes']);
			// print_r('<br>');
			if ($tier == 7) {
				if ($goal > $tmp['handshakes']) {
					$p = Priority::find($key->priority_id);

					$p->tier = $org_tier;
					$p->save();
				}
			}else{
				if ($goal < $tmp['handshakes']) {
					$p = Priority::find($key->priority_id);

					$p->tier = 7;
					$p->save();
				}
			}
		}

		return 'success';
  	}

  	public function autoApproveTargettedInquiries(){

  		$qry = DB::connection('rds1')->table('recruitment as r')
  				 ->join('recruitment_tags AS rt', function($q){
  				 	    $q->on('rt.college_id', '=', 'r.college_id');
  				 	    $q->on('rt.user_id', '=', 'r.user_id');
  				 })
  				 ->join('colleges as c', 'c.id', '=', 'r.college_id')
  				 ->join('organization_branches as ob', 'ob.school_id', '=', 'c.id')
				 ->join(DB::raw('(select max(id) as MAX_ID, user_id,organization_branch_id from organization_branch_permissions WHERE user_id != -1 AND user_id!=93 AND user_id!=120 AND user_id!=127 group by organization_branch_id ) as obp'), 'obp.organization_branch_id', '=', 'ob.id')
  				 ->join('users as u', 'u.id', '=', 'r.user_id')
  				 ->where(DB::raw("(rt.aor_id, rt.aor_portal_id, rt.org_portal_id)"), '!=', DB::raw("(-1,-1,-1)"))
  				 ->where('r.status', 1)
  				 ->where('r.user_recruit', 1)
  				 ->where('r.college_recruit', 0)
  				 ->select('r.id as rec_id', 'r.college_id', 'r.user_id', 'r.aor_id',
  				 		 'c.school_name', 'obp.user_id as college_user_id',
  				 		  'u.fname', 'u.lname')
  				 ->take(10)
  				 ->get();

  		foreach ($qry as $k) {

  			$affectedRows = Recruitment::where('id', $k->rec_id)
  									   ->update(array('email_sent' => 1, 'college_recruit' => 1,
  									   				  'type' => DB::raw("CONCAT(type, ' auto_approve_targetted_inquiries')")));



		   if(isset($k->aor_id)){
				$aor = Aor::find($k->aor_id);

				// If this is a handshake contract
				if(isset($aor) && $aor->contract == 2){
					$this->chargeCollegePerInquiry($k->rec_id, null, $k->user_id, $k->aor_id);
				// end of this is a hanshake contract.
				}
			}else{
				$ob = OrganizationBranch::where('school_id', $k->college_id)->first();

				if(isset($ob)){
					// If this is a handshake contract
					if($ob->contract == 2){
						$this->chargeCollegePerInquiry($k->rec_id, $ob->id, $k->user_id);
					}

				}
			}
			// add notification to the user
			$ntn = new NotificationController();
			$ntn->create( $k->school_name, 'user', 2, null, $k->college_user_id , $k->user_id, NULL, '1');
			$ntn->create( $k->fname. ' '. $k->lname, 'college', 2, null, $k->user_id, $k->college_id, NULL, '1');
  		}

  		return "success";
  	}

  	public function devryCollegesTempFixUps(){

  		$college_ids = array(7726,7728,7732,188842,188843,207820,188846);

  		$yesterday = Carbon::yesterday();
  		$rec = Recruitment::whereIn('college_id', $college_ids)
					  ->whereNotNull('aor_id')
					  ->where('created_at', '>=', $yesterday)
					  ->get();

		foreach ($rec as $key) {
			$crc = new CollegeRecommendationController;
			$tmp = $crc->findPortalsForThisUserAtThisCollege($key->user_id, $key->college_id);

			if (empty($tmp)) {
				$key->aor_id = NULL;
				$key->save();
			}
		}
  	}



  	/*
     * This method purpose is to make upload general
  	 */
  	protected function generalUploadDoc($input, $file_upload_name, $bucket_url, $prepend_file_name = NULL){
  		$ret = array();
  		if (!isset($input) || !isset($file_upload_name) || !isset($bucket_url)) {
  			$ret['status'] = "failed";

  			return $ret;
  		}

  		// Get file info
  		if (!is_string($file_upload_name) && $file_upload_name == true) {
  			$file = $input;
  		}
  		else {
  			$file = Request::file($file_upload_name);
  		}

		$path = $file->getRealPath();

		$filename = $file->getClientOriginalName();
		$ext = $file->getClientOriginalExtension();
		$mime = $file->getMimeType();
		$file_path = pathinfo($filename);

		if (!isset($prepend_file_name)) {
			$saveas = $file_path['filename'] . '_'. date('Y_m_d_H_i_s') . "." . strtolower($ext);
		}else{
			$saveas = $prepend_file_name.'_'.$file_path['filename'] . "." . strtolower($ext);
		}
		$bucket_url = str_replace("asset.plexuss.com/", "", $bucket_url);

        $s3 = AWS::createClient('s3');

		$s3->putObject(array(
			'ACL' => 'public-read',
			'Bucket' => 'asset.plexuss.com',
			'Key' => $bucket_url . '/' . $saveas,
			'SourceFile' => $path,
		));

		$public_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/";

		$filename ? $ret['filename'] = $filename : null;
		$ret['status'] = "success";
		$ret['mime_type'] = $mime;
        $ret['saved_as'] = $saveas;
		$ret['url']	   = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/'. $bucket_url."/". $saveas;

		return $ret;
  	}

  	/**
	 * This function uses finfo to get the mimetype from URLs
	 *
	 * @return mimetype
	 */
    protected function getMimeTypeFromURL($url_path) {
    	try {
    		$url = str_replace(' ', '+', $url_path);
			$buffer = $this->curl_get_contents($url);
		    $finfo = new finfo(FILEINFO_MIME_TYPE);
		    $mime_type = $finfo->buffer($buffer);
	    	return $mime_type;
    	} catch(Exception $exception) {
    		return "DNE"; // Does not exist
    	}
    }

    /**
	 * This helper function grabs URL information
	 *
	 * @return url_contents
	 */
    private function curl_get_contents($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$url_contents = curl_exec($ch);
		curl_close($ch);
		return $url_contents;
	}

  	/*
     * This method purpose is to delete a file
  	 */
  	public function generalDeleteFile($bucket_url, $keyname, $no_space = NULL){
  		// Remove from S3 Bucket
		// $s3 = AWS::get('s3');
        $s3 = AWS::createClient('s3');

		// $bucket_url = 'asset.plexuss.com';
		// $keyname = 'The_New_England_Conservatory_of_Music_Plexuss_Approved_Student_List_01-01-2014_to_09-13-2016.xls';
        $folder_path = str_replace("asset.plexuss.com/", "", $bucket_url);

        if (isset($no_space)) {
        	$key = $folder_path.$keyname;
        }else{
        	$key = $folder_path . ' ' . $keyname;
        }

		$delete = $s3->deleteObject(array(
			'Bucket' => 'asset.plexuss.com',
			'Key'    => $key,
		));

		$ret = array();

		if ($delete) {
			$ret['status'] = "success";
		}else{
			$ret['status'] = "failed";
		}

		return $ret;
  	}

  	public function postTest(){
  		$input = Request::all();

  		echo "<pre>";
  		print_r($input);
  		echo "</pre>";
  		exit();
  	}
  	/*
     * This method is for cronJobs, removing people who ha
  	 */
  	public function prescreenedRemoveBadPhone(){

  		$ntn = DB::statement('update recruitment
								set status = 0
								where
									college_id = 7916
								and status = 1
								and user_id in(
									Select
										id
									from
										users
									where
										(phone is null or length(phone) < 5)
								)
								and user_id not in(
									Select
										user_id
									from
										prescreened_users
									where
										college_id = 7916
								);');

  		return "success";
  	}

  	public function generatePDF(){
  		$input = Request::all();

  		if (!isset($input['url'])) {
  			return "url is required";
  		}

		// $snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
		// // $snappy->generateFromHtml('<h1>Bill</h1><p>You owe me money, dude.</p>', storage_path().'/bill-123.pdf');
  		// header('Content-Type: application/pdf');
		// header('Content-Disposition: attachment; filename="file.pdf"');
		// echo $snappy->getOutput($input['url']);

  		$pdf = new Pdf(array(
		    'binary' => env('WKHTMLTOPDF'),
		    'tmpDir' => storage_path(),
		    'ignoreWarnings' => true,
		    'commandOptions' => array(
		        'useExec' => true,      // Can help if generation fails without a useful error message
		        'procEnv' => array(
		            // Check the output of 'locale' on your system to find supported languages
		            'LANG' => 'en_US.utf-8',
		        ),
		    ),
		    'no-outline',         // Make Chrome not complain
		    'margin-top'    => 0,
		    'margin-right'  => 0,
		    'margin-bottom' => 0,
		    'margin-left'   => 0,

		    // Default page options
		    'disable-smart-shrinking',
		));

  		$pdf->addPage($input['url']);
  		$pdf->send();
  		dd($pdf);
  	}

  	/**
	 * autoPrescreenedUserAppliedColleges
	 *
	 * Automatically add users to prescreen folder for users applied colleges
	 * @return (string)
	 */
  	public function autoPrescreenedUserAppliedColleges(){

		$crc = new CollegeRecommendationController;

  		$uac = UsersAppliedColleges::where('auto_prescreened', 0)
  		                           ->take(100)
  		                           ->orderby('id', 'desc')
  		                           ->get();

  		foreach ($uac as $key) {
  		 	$in_matches = $crc->findPortalsForThisUserAtThisCollege($key->user_id, $key->college_id);

  		 	if (empty($in_matches)) {
  		 		$attr = array('user_id' => $key->user_id, 'college_id' => $key->college_id);
				$val  = array('user_id' => $key->user_id, 'college_id' => $key->college_id, 'active' => 1, 'note' => 'autoPrescreenedUserAppliedColleges');

				$ac = AorCollege::on('rds1')->where('college_id', $key->college_id)->first();

				if (isset($ac)) {
					$attr['aor_id'] = $ac->aor_id;
					$val['aor_id']  = $ac->aor_id;
				}else{
					$attr['aor_id'] = NULL;
					$val['aor_id']  = NULL;
				}

				//if (isset($attr['aor_id'])) {
					$this->addToPlexussPortalPrescreened($attr['aor_id'], $key->user_id);
				//}

				$update =  PrescreenedUser::updateOrCreate($attr, $val);

				// Add this user to verified application if the person
				$ucq = UsersCustomQuestion::on('rds1')
				                          ->where('user_id', $key->user_id)
				                          ->where('application_state', 'submit')
				                          ->first();
				if (isset($ucq)) {
					$ac = new AdminController();

					$dt = array();
					$it = array();

					$it['user_id'] = Crypt::encrypt($key->user_id);
					$it['page']	   = 'admin-prescreened';
					$it['rec_id']  = $update->id;

					$dt['org_school_id'] = $key->college_id;

					$ac->saveVerifiedApplication($dt, $it);
				}

  		 	}else{
  		 		foreach ($in_matches as $k) {

  		 			$attr = array('user_id' => $key->user_id, 'college_id' => $key->college_id, 'aor_id' => $k['aor_id'], 'org_portal_id' => $k['org_portal_id'],
  		 						  'aor_portal_id' => $k['aor_portal_id']);
					$val  = array('user_id' => $key->user_id, 'college_id' => $key->college_id, 'aor_id' => $k['aor_id'], 'org_portal_id' => $k['org_portal_id'],
  		 						  'aor_portal_id' => $k['aor_portal_id'], 'active' => 1, 'note' => 'autoPrescreenedUserAppliedColleges');

					$update =  PrescreenedUser::updateOrCreate($attr, $val);

					//if (isset($k['aor_id'])) {
						$this->addToPlexussPortalPrescreened($k['aor_id'], $key->user_id);
					//}

					// Add this user to verified application if the person
					$ucq = UsersCustomQuestion::on('rds1')
					                          ->where('user_id', $key->user_id)
					                          ->where('application_state', 'submit')
					                          ->first();
					if (isset($ucq)) {
						$ac = new AdminController();

						$dt = array();
						$it = array();

						$it['user_id'] = Crypt::encrypt($key->user_id);
						$it['page']	   = 'admin-prescreened';
						$it['rec_id']  = $update->id;

						$dt['org_school_id'] = $key->college_id;
						$dt['aor_id']		 = $k['aor_id'];
						$dt['aor_portal_id'] = $k['aor_portal_id'];

						if (isset($k['org_portal_id'])) {
							$dt['default_organization_portal'] = stdClass();
							$dt['default_organization_portal']->id = $k['org_portal_id'];
						}

						$ac->saveVerifiedApplication($dt, $it);
					}
  		 		}

  		 	}

  		 	$key->auto_prescreened = 1;
  		 	$key->save();

  		}

  		return "success";
  	}

  	/*
  	 * This method is built to manually send the user to Plexuss portals that are built for AORs so for example
  	 * if someone is matched to ELS then we want to add that person to Plexuss ELS portal
  	 */
  	private function addToPlexussPortalPrescreened($aor_id, $user_id){

  		$aor = Aor::on('rds1')->find($aor_id);

  		if (isset($aor)) {
  			$op = OrganizationPortal::on('rds1')->where('name', $aor->name)->first();

  			if (isset($op)) {
  				// Shorelight case, only add to prescreened if they have a budget of greater than 20,000.
  				if ($op->id == 227) {

  					$attr = array('user_id' => $user_id, 'college_id' => 7916, 'org_portal_id' => 227, 'note' => 'autoPrescreenedUserAppliedColleges');
	  				$val  = array('user_id' => $user_id, 'college_id' => 7916, 'org_portal_id' => 227, 'active' => 1, 'note' => 'autoPrescreenedUserAppliedColleges');

  					$user = User::on('rds1')->where('id', $user_id)->select('financial_firstyr_affordibility')->first();
  					$max = null;
  					$split = null;

  					// Standard financial format example: '10,000 - 20,000'
  					if (isset($user->financial_firstyr_affordibility)) {
  						$user->financial_firstyr_affordibility = str_replace(',', '', $user->financial_firstyr_affordibility);
  						$split = explode(' - ', $user->financial_firstyr_affordibility);
  					} else {
						return 'fail';
  					}

					if (isset($split) && count($split) == 2) {
						$max = $split[0];

					} else if (isset($split) && count($split) == 1) {
						$max = $split[0];

						if (intval($max) == 0) return 'fail';

					} else { // If no financial data, just return.
						return 'fail';
					}

					if (isset($max) && $max >= 20000) {
						$update =  PrescreenedUser::updateOrCreate($attr, $val);
					}

  				} else {
	  				$attr = array('user_id' => $user_id, 'college_id' => 7916, 'org_portal_id' => $op->id, 'active' => 1, 'note' => 'autoPrescreenedUserAppliedColleges');
	  				$val  = array('user_id' => $user_id, 'college_id' => 7916, 'org_portal_id' => $op->id, 'active' => 1, 'note' => 'autoPrescreenedUserAppliedColleges');
  					$update =  PrescreenedUser::updateOrCreate($attr, $val);
  				}

  			}


  		}

  	}

  	// this is an internal method so we can display data much easier.
  	public function customdd($input){
  		echo "<pre>";
  		print_r($input);
  		echo "</pre>";
  	}

  	public function obj($data)
	{
	    if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            $result[$key] = $this->obj($value);
	        }
	        return $result;
	    }
	    return $data;
	}

  	public function applicationSuppression(){
  		$input = Request::all();

  		try{
  			$user_id       = Crypt::decrypt($input['user_id']);
  			$template_name = Crypt::decrypt($input['template_name']);
  		} catch (\Exception $e) {
  			return "Bad user/template name";
  		}

  		$attr = array('user_id' => $user_id, 'template_name' => $template_name);
  		$val  = array('is_supressed' => 1, 'user_id' => $user_id, 'template_name' => $template_name);
  		$update = ApplicationEmailSuppresion::updateOrCreate($attr, $val);

  		return "Thank you for updating your application email settings, we will not send this email to you anymore.";
  	}

  	public function scheduleoneapp(){
  		$input = Request::all();

  		try{
  			$user_id       = Crypt::decrypt($input['user_id']);
  			$template_name = Crypt::decrypt($input['template_name']);
  		} catch (\Exception $e) {
  			return "Bad user/template name";
  		}

  		$attr = array('no_more_email' => 1, 'user_id' => $user_id, 'template_name' => $template_name);
  		$val  = array('no_more_email' => 1, 'user_id' => $user_id, 'template_name' => $template_name);
  		$update = ApplicationEmailSuppresion::updateOrCreate($attr, $val);

  		return redirect('https://oneapp.youcanbook.me/');
  	}

  	// Utility method
  	public function changeApplicationStates(){

  		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		if ($data['is_plexuss'] != 1) {
			return "You need to be logged in as a Plexuss User";
		}

  		$qry = DB::connection('rds1')->table('users_custom_questions as ucq')
  									 ->leftjoin('users_applied_colleges as uac', 'ucq.user_id', '=', 'uac.user_id')
  									 ->leftjoin('objectives as o', 'o.user_id', '=', 'ucq.user_id')
  									 ->where('ucq.application_state', 'submit')
  									 ->orderBy('ucq.id', 'ASC')
  									 ->select('ucq.*', 'uac.submitted', 'uac.college_id', 'o.degree_type')
  									 ->get();

  		$cnt = 0;
  		foreach ($qry as $key) {

  			// if this person has not submitted an application then send them back to colleges state
  			if (!isset($key->submitted)) {
  				$tmp = UsersCustomQuestion::where('user_id', $key->user_id)->first();
  				$tmp->application_state = "colleges";
  				$tmp->save();
  			}

  			$degree = 'undergrad';
  			// $this->customdd($key);
  			if ($key->degree_type == 4 || $key->degree_type == 5) {
  				$degree = "grad";
  			}
  			$caas =  CollegeApplicaitonAllowedSection::on('rds1')->where('college_id', $key->college_id)
  																 ->where(function($q) use ($degree){
  																 		$q->orWhere('define_program', '=', $degree)
  																 		  ->orWhere('define_program', '=', 'epp');
  																 })
  																 ->where('page', 'additional')
  																 ->where('required', 1)
  																 ->get();

  			// if this school doesn't have any additional questions then skip it
  			if (!isset($caas[0])) {
  				continue;
  			}
  			$step = '';
  			$failed_section = '';
  			foreach ($caas as $k) {

  				$sub_section 		  = $k->sub_section;
  				$sub_section_no_addtl = str_replace('addtl__', "", $k->sub_section);
  				// print_r($sub_section);
  				// print_r("<br />");
  				// print_r($sub_section_no_addtl);
  				// print_r("<br /> k is :");
  				// $this->customdd($k);
  				// print_r("<br /> key is :");
  				// $this->customdd($key);
  				// print_r("<br /> ------- is :");
  				// print_r($key->$sub_section_no_addtl);
  				// exit();
			 	if (isset($key->$sub_section) && (empty($key->$sub_section) || ($key->$sub_section == NULL) ) && $key->$sub_section != "0") {

			 		// dd($key->$sub_section);
			 		$step = $k->page;
			 		$failed_section = $sub_section;
			 		break;
			 	}

			 	if (isset($key->$sub_section_no_addtl) && (empty($key->$sub_section_no_addtl) || ($key->$sub_section_no_addtl == NULL) )
			 		&& $key->$sub_section_no_addtl != "0") {

			 		// dd($key->$sub_section_no_addtl);
			 		$step = $k->page;
			 		$failed_section = $sub_section_no_addtl;
			 		break;
			 	}
			}
			if ($step != '' || $failed_section != '') {
	  			echo "<pre>";
	  			print_r("user_id: ". $key->user_id. " college_id: ". $key->college_id. " degree: " . $key->degree_type . " step: ". $step." failed_section: ". $failed_section );
	  			echo "</pre>";
	  			echo "<br />";

	  			$cnt++;

	  			$ucq = UsersCustomQuestion::where('user_id', $key->user_id)->first();
	  			$ucq->application_state = "additional_info";
	  			$ucq->application_submitted = 0;

	  			$ucq->save();


	  		}
  			// exit();
  			// if ($cnt == 30) {
  			// 	exit();
  			// }

  		}
  	}

  	// Utility method
  	public function changeApplicationStatesUploads(){

  		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		if ($data['is_plexuss'] != 1) {
			return "You need to be logged in as a Plexuss User";
		}

  		$qry = DB::connection('rds1')->table('users_custom_questions as ucq')
  									 ->leftjoin('users_applied_colleges as uac', 'ucq.user_id', '=', 'uac.user_id')
  									 ->leftjoin('objectives as o', 'o.user_id', '=', 'ucq.user_id')
  									 ->where('ucq.application_state', 'submit')
  									 ->orderBy('uac.updated_at', 'desc')
  									 ->select('ucq.*', 'uac.submitted', 'uac.college_id', 'o.degree_type', 'uac.updated_at')
  									 ->get();

  		$cnt = 0;
  		foreach ($qry as $key) {

  			// if this person has not submitted an application then send them back to colleges state
  			if (!isset($key->submitted)) {
  				$tmp = UsersCustomQuestion::where('user_id', $key->user_id)->first();
  				$tmp->application_state = "colleges";
  				$tmp->save();
  			}

  			$degree = 'undergrad';
  			// $this->customdd($key);
  			if ($key->degree_type == 4 || $key->degree_type == 5) {
  				$degree = "grad";
  			}
  			$caas =  CollegeApplicaitonAllowedSection::on('rds1')->where('college_id', $key->college_id)
  																 ->where(function($q) use ($degree){
  																 		$q->orWhere('define_program', '=', $degree)
  																 		  ->orWhere('define_program', '=', 'epp');
  																 })
  																 ->where('page', 'uploads')
  																 ->where('required', 1)
  																 ->get();

  			// if this school doesn't have any additional questions then skip it
  			if (!isset($caas[0])) {
  				continue;
  			}
  			$step = '';
  			$failed_section = '';

  			$transcript = DB::table('transcript')->where('user_id', $key->user_id)->pluck('doc_type');

  			$check = false;

  			// $this->customdd($transcript);

  			foreach ($caas as $k) {
  				if (!in_array($k->sub_section, $transcript)) {

  					$check = true;
  					break;
  				}
			}

			if ($check) {
				echo "<pre>";
	  			print_r("user_id: ". $key->user_id. " college_id: ". $key->college_id. " degree: " . $key->degree_type. " updated_at:". $key->updated_at );
	  			echo "</pre>";
	  			echo "<br />";
	  			print_r('plexuss.dev/sales/loginas/'. Crypt::encrypt($key->user_id) );
	  			echo "<br />";
	  			$cnt++;

	  			// $ucq = UsersCustomQuestion::where('user_id', $key->user_id)->first();

	  			// $ucq->application_state = "uploads";
	  			// $ucq->application_submitted = 0;

	  			// $ucq->save();


	  		}

	  		// if ($cnt == 5) {
  			// 	exit();
  			// }

  		}
  	}

  	// Utility method
  	public function fixPresscreenedAddToRecTab(){

  		$pu = PrescreenedUser::whereNull('rec_id')->get();

  		foreach ($pu as $key) {

  			$rec = Recruitment::where('user_id', $key->user_id)
  							  ->where('college_id', $key->college_id);


  			if (isset($key->aor_id)) {
  				$rec = $rec->where('aor_id', $key->aor_id);
  			}else{
  				$rec = $rec->whereNull('aor_id');
  			}

  			$rec = $rec->first();

  			if (!isset($rec)) {
  				// echo "<pre>";
  				// echo ($key);
  				// echo "</pre>";
  				// exit();
  				$rt  = RecruitmentTag::where('user_id', $key->user_id)
  									 ->where('college_id', $key->college_id)
  									 ->where('aor_id', -1)
  									 ->where('org_portal_id', -1)
  									 ->where('aor_portal_id', -1)
  									 ->delete();

  				// dd(1);
  				$attr = array('user_id' => $key->user_id, 'college_id' => $key->college_id, 'aor_id' => $key->aor_id);
  				$val  = array('user_id' => $key->user_id, 'college_id' => $key->college_id, 'aor_id' => $key->aor_id,
  							  'user_recruit' => 10, 'college_recruit' => 11, 'status' => 1, 'type' => "manual prescreen addon", 'email_sent' => 1);

  				$update = Recruitment::updateOrCreate($attr, $val);

  				if ($key->org_portal_id == NULL && $key->aor_id == NULL && $key->aor_portal_id == NULL) {
  					$org_portal_id = -1;
  					$aor_id = -1;
  					$aor_portal_id = -1;
  				}else{
  					$org_portal_id = $key->org_portal_id;
  					$aor_id = $key->aor_id;
  					$aor_portal_id = $key->aor_portal_id;
  				}

  				$attr = array('user_id' => $key->user_id, 'college_id' => $key->college_id, 'aor_id' => $aor_id,
  							  'org_portal_id' => $org_portal_id, 'aor_portal_id' => $aor_portal_id);

  				$val = array('user_id' => $key->user_id, 'college_id' => $key->college_id, 'aor_id' => $aor_id,
  							  'org_portal_id' => $org_portal_id, 'aor_portal_id' => $aor_portal_id);

  				$rt  = RecruitmentTag::updateOrCreate($attr, $val);

  				// $this->customdd($key);
  				// exit();
  			}
  		}

  		return "success";
  	}

  	// Cron job for pick a college

  	public function generatePickACollegeData(){

  		$minutes = 60;

  		//if (Cache::has(env('ENVIRONMENT') .'_generatePickACollegeData')) {
  			//$data = Cache::get(env('ENVIRONMENT') .'_generatePickACollegeData');
  		//}else{
  			/////
  			$data = array();
	  		$pr = new Priority;
			$pr = $pr->getPrioritySchools();

			$tp   = new TrackingPage;
			$rec  = new Recruitment;
			$pavc = new PickACollegeView;

			$data['pickACollege_data'] = array();

			foreach ($pr as $key) {
				//dd($key);
				$tmp = array();

				$tmp['id'] 			= (int)$key->priority_id;
				$tmp['college_id'] 	= (int)$key->college_id;
				$tmp['name']  		= $key->school_name;
				$tmp['promoted']    = (int)$key->promoted;
				$tmp['contract']	= (int)$key->contract_id;
				$tmp['contract_name'] = $key->contract;
				$tmp['aor']			= isset($key->aor) ? $key->aor : 'N/A';
				$tmp['aor_id']		= (int)$key->aor_id;
				$tmp['balance']		= isset($key->balance) ? $key->balance : 'N/A';
				$tmp['price']		= isset($key->price) ? $key->price : 0;
				$tmp['goal']		= isset($key->goal) ? (int)$key->goal : 0;
				$tmp['start_date']	= $key->start_goal;
				$tmp['end_date']	= $key->end_goal;

				if (isset($tmp['start_date'])) {
					$start_date = $tmp['start_date']." 00:00:00";
				}else{
					$start_date = NULL;
				}

				if (isset($tmp['end_date'])) {
					$end_date = $tmp['end_date']." 23:59:59";
				}else{
					$end_date = NULL;
				}

				$tmp['views']		= 0;

				$tmp['views']		= $pavc->getNumOfPickACollegeViews($key->college_id, $start_date, $end_date);
				$tmp['handshakes']	= 0;

				$handshakes 		= $rec->getNumOfTotalApprovedForColleges(array($key->college_id), $start_date,
													$end_date, null, $key->aor_id, true, 'inquiry_pick_a_college');
				if (isset($handshakes)) {
					foreach ($handshakes as $k) {
						$tmp['handshakes']	= (int)$k->cnt;
					}
				}

				$picks 			= $rec->getNumberOfPicksBasedOnType(array($key->college_id), 'inquiry_pick_a_college',
																			 null, $key->aor_id, $start_date, $end_date, true);
				$tmp['picks']	= 0;
				if (isset($picks)) {
					foreach ($picks as $k) {
						$tmp['picks']	= (int)$k->cnt;
					}
				}

				$tmp['conversion'] = 0;

				if ($tmp['picks'] > 0 && $tmp['views'] > 0) {
					$tmp['conversion'] = (($tmp['picks']/ $tmp['views']) * 100);
				}

				$tmp['conversion'] = (float)number_format((float)$tmp['conversion'], 2, '.', '');

				$data['pickACollege_data'][] = $tmp;
			}

			//Cache::put(env('ENVIRONMENT') .'_generatePickACollegeData', $data, $minutes);
  		//}

  		return $data['pickACollege_data'];
  	}

    public function generatePickACollegeDataById($id){
        $data = array();
        $pr = new Priority;
        $pr = $pr->getPrioritySchoolsById($id);

        $tp   = new TrackingPage;
        $rec  = new Recruitment;
        $pavc = new PickACollegeView;

        $data['pickACollege_data'] = array();

        $tmp = array();

        $tmp['id'] 			= (int)$pr->priority_id;
        $tmp['college_id'] 	= (int)$pr->college_id;
        $tmp['name']  		= $pr->school_name;
        $tmp['promoted']    = (int)$pr->promoted;
        $tmp['contract']	= (int)$pr->contract_id;
        $tmp['contract_name'] = $pr->contract;
        $tmp['aor']			= isset($pr->aor) ? $pr->aor : 'N/A';
        $tmp['aor_id']		= (int)$pr->aor_id;
        $tmp['balance']		= isset($pr->balance) ? $pr->balance : 'N/A';
        $tmp['price']		= isset($pr->price) ? $pr->price : 0;
        $tmp['goal']		= isset($pr->goal) ? (int)$pr->goal : 0;
        $tmp['start_date']	= $pr->start_goal;
        $tmp['end_date']	= $pr->end_goal;

        if (isset($tmp['start_date'])) {
            $start_date = $tmp['start_date']." 00:00:00";
        }else{
            $start_date = NULL;
        }

        if (isset($tmp['end_date'])) {
            $end_date = $tmp['end_date']." 23:59:59";
        }else{
            $end_date = NULL;
        }

        $tmp['views']		= 0;

        $tmp['views']		= $pavc->getNumOfPickACollegeViews($pr->college_id, $start_date, $end_date);
        $tmp['handshakes']	= 0;

        $handshakes = $rec->getNumOfTotalApprovedForColleges(array($pr->college_id), $start_date,
        $end_date, null, $pr->aor_id, true, 'inquiry_pick_a_college');
        if (isset($handshakes)) {
            foreach ($handshakes as $k) {
                $tmp['handshakes']	= (int)$k->cnt;
            }
        }

        $picks 	= $rec->getNumberOfPicksBasedOnType(array($pr->college_id), 'inquiry_pick_a_college',
            null, $pr->aor_id, $start_date, $end_date, true);
        $tmp['picks']	= 0;
        if (isset($picks)) {
            foreach ($picks as $k) {
                $tmp['picks']	= (int)$k->cnt;
            }
        }

        $tmp['conversion'] = 0;

        if ($tmp['picks'] > 0 && $tmp['views'] > 0) {
            $tmp['conversion'] = (($tmp['picks']/ $tmp['views']) * 100);
        }

        $tmp['conversion'] = (float)number_format((float)$tmp['conversion'], 2, '.', '');

        $data['pickACollege_data'] = $tmp;

        return $data['pickACollege_data'];
    }

  	public function sendemailalert($subject, $toEmails , $data = null) {

		//dd($data);
		$name = 'Plexuss Admin';

		$dt = array();

		$dt['data'] = $data;

		Mail::send( 'emails.formPostPlexussAdminAlert', $dt, function( $message ) use ( $subject ,$toEmails, $name ) {
				$message->to( $toEmails, $name )->subject( $subject );
			}
		);

	}

	/*
	 * Tracking who is online
	 * This method saves the user ids and redis client ids of people who are currently online.
	 *
	 *
	 */
	public function setRedisOnlineUser(){
		$input = Request::all();

		$attr = array('is_online' => 1, 'user_id' => $input['user_id'], 'client_id' => $input['client_id'], 'connected_at' => Carbon::now());
		$val  = array('is_online' => 1, 'user_id' => $input['user_id'], 'client_id' => $input['client_id'], 'connected_at' => Carbon::now());


		$update = RedisUserList::updateOrCreate($attr, $val);

	}

	/*
	 * Tracking who is online
	 * This method flags the disconnected user.
	 *
	 *
	 */
	public function removeRedisOnlineUser(){
		$input = Request::all();

		$attr = array('is_online' => 0, 'client_id' => $input['client_id'], 'disconnected_at' => Carbon::now());

		$update = RedisUserList::where('client_id', $input['client_id'])->update($attr);

	}

	public function forgetChatCache(){
		Cache::forget(env('ENVIRONMENT') .'_'.'college_chat_threads');
	}

	public function campaignFix(){

		$qry = CollegeCampaign::on('rds1')->where('ready_to_send', 1)->orderBy('id', 'desc')->first();

		if (isset($qry)) {
			$student_user_ids = $qry->student_user_ids;

			$student_user_ids = explode(",", $student_user_ids);
			$cnt = 1;
			foreach ($student_user_ids as $key => $value) {
				if ($cnt < 9363) {
					$cnt++;
					continue;

				}
				$ccs = new CollegeCampaignStudent;
				$ccs->campaign_id = $qry->id;
				$ccs->user_id = $value;

				$ccs->save();
				$cnt++;
			}
		}
	}

	/**
	 * Encode array to utf8 recursively
	 * @param $dat
	 * @return array|string
	 */
	public function array_utf8_encode($dat){
	    if (is_string($dat))
	        return utf8_encode($dat);
	    if (!is_array($dat))
	        return $dat;
	    $ret = array();
	    foreach ($dat as $i => $d)
	        $ret[$i] = self::array_utf8_encode($d);
	    return $ret;
	}
	public function forget(){
		$ip = $this->iplookup();
		Cache::forget(env('ENVIRONMENT') .'_'. $ip['ip']. '_'. 'context');
	}

	public function move_to_top($array, $key) {
	    if(isset($array[$key])) {
		    $value = $array[$key];
		    unset($array[$key]);
		    array_unshift($array, $value);

		    return $array;
		}
	}

	public function addingCollegeIdToContactInfo(){

		if (Cache::has(env('ENVIRONMENT') .'_addingCollegeIdToContactInfo')) {
			$cache_count =  Cache::get(env('ENVIRONMENT') .'_addingCollegeIdToContactInfo');
		}


		$icci = InternalCollegeContactInfo::take(5000);

		if (isset($cache_count)) {
			$icci = $icci->skip($cache_count);
		}
		// $icci = $icci->where('blah', 1);
		$icci = $icci->get();

		$cnt = 0;
		foreach ($icci as $key) {

			$iccd = DB::table('internal_college_contact_dump')->where('email', $key->email)->first();

			if (isset($iccd)) {
				print_r("id: ". $key->id. "<br/>");
				$key->college_id = $iccd->college_id;

				$key->save();
			}
			$cnt++;
		}

		if (isset($cache_count)) {
			$total = $cache_count + $cnt;
		}else{
			$total = $cnt;
		}

		Cache::put(env('ENVIRONMENT') .'_addingCollegeIdToContactInfo', $total, 1660);
	}

	/*
	 * isUserCompleteProfile
	 * This method checks if user has complete profile or not.
	 *
	 *
	 */
	public function engagementEmailCheckups($type, $user_id){

		switch ($type) {
			case 'complete_profile':
				$qry = DB::connection('bk')->table('users as u')
						 		->join('scores as s', 'u.id', '=', 's.user_id')

								->where('is_ldy', 0)
								->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
								->where('email', 'NOT LIKE', '%test%')
								->where('fname', 'NOT LIKE', '%test%')
								->where('email', 'NOT LIKE', '%nrccua%')

								->where('is_plexuss', 0)
								->whereNotNull('u.address')
								->where(DB::raw('length(u.address)'), '>=', 3)
								->whereNotNull('u.zip')
								->whereIn('u.gender', array('m', 'f'))
								->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
								->where('u.id', $user_id)
							 	->select('u.id as user_id')->first();

				if (isset($qry->user_id)) {
					return true;
				}
				break;
			case 'students_selected_1_5':
				$qry = DB::connection('bk')
						  ->select("select count(distinct email) as cnt
							from (
								Select user_id, email, count(*)
								from
									(select user_id, college_id
									from users u
									join recruitment r on u.id = r.user_id
										and r.user_recruit = 1
									where
									u.id = ".$user_id."

									UNION

									select user_id, college_id
									from users u
									join pick_a_college_views pacw on u.id = pacw.user_id
									where
									u.id = ".$user_id."

									UNION

									select user_id, college_id
									from users u
									join ad_clicks ac on ac.user_id = u.id
										and `ac`.`utm_source` NOT LIKE '%test%'
										and `ac`.`utm_source` NOT LIKE 'email%'
										and (pixel_tracked = 1 or paid_client = 1)
									where
									u.id = ".$user_id."

									union

									select user_id, college_id
									from users u
									join users_applied_colleges uac on u.id = uac.user_id
									where
									u.id = ".$user_id."
								) tbl1
								join users u on tbl1.user_id = u.id
								where is_ldy = 0
									and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
									and email not like '%test%'
									and fname not like '%test'
									and email not like '%nrccua%'
								group by u.id
								having count(*) between 1 and 4
							) tbl2
							");

				$cnt = $qry[0]->cnt;

				if ($cnt > 0) {
					return true;
				}
				break;
			case 'selected_over_5':
				$qry = DB::connection('bk')
						 ->select("select count(distinct email) as cnt
							from (
								Select user_id, email, count(*)
								from
									(select user_id, college_id
									from users u
									join recruitment r on u.id = r.user_id
										and r.user_recruit = 1
									where
										u.id = ".$user_id."

									UNION

									select user_id, college_id
									from users u
									join pick_a_college_views pacw on u.id = pacw.user_id
									where
										u.id = ".$user_id."

									UNION

									select user_id, college_id
									from users u
									join ad_clicks ac on ac.user_id = u.id
										and `ac`.`utm_source` NOT LIKE '%test%'
										and `ac`.`utm_source` NOT LIKE 'email%'
										and (pixel_tracked = 1 or paid_client = 1)
									where
										u.id = ".$user_id."

									union

									select user_id, college_id
									from users u
									join users_applied_colleges uac on u.id = uac.user_id
									where
										u.id = ".$user_id."
								) tbl1
								join users u on tbl1.user_id = u.id
								where is_ldy = 0
									and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
									and email not like '%test%'
									and fname not like '%test'
									and email not like '%nrccua%'
								group by u.id
								having count(*) >= 5
							) tbl2
							");

				$cnt = $qry[0]->cnt;
				if ($cnt > 0) {
					return true;
				}
				break;
			default:
				# code...
				break;
		}


		return false;
	}
	public function testNumOfHandShake(){
		if (Cache::has(env('ENVIRONMENT') .'_'.'getCountOfRecruitment')) {
 			$rec = Cache::get(env('ENVIRONMENT') .'_'.'getCountOfRecruitment');
 		}else{
 			$rec = -1;
 		}

 		return $rec;
	}

	// Image base 64 upload
    public function uploadBase64Img($input = NULL){

        if (!isset($input)) {
            $input = Request::all();
        }

        // return "File name : ". $input['file_name'] . " ****** image : ". $input['image'];

        if (!isset($input['file_name']) || !isset($input['image']) || !isset($input['user_id']) || !isset($input['bucket_url'])) {
            return "file_name and image are required";
        }

        try {
            $user_id       = Crypt::decrypt($input['user_id']);
        } catch (\Exception $e) {
            return "Bad user id";
        }

        $imageDir = storage_path().'/img/';

        $filePath =  $this->saveBase64ImagePng($input['image'], $imageDir, $input['file_name']);

        // $bucket_url = "asset.mycollegefund.com/users/profile_pic";
        $bucket_url = str_replace("asset.plexuss.com/", "", $input['bucket_url']);

        $temp_file = explode(".", $input['file_name']);
        $temp_file[0] .=  '_'. date('Y_m_d_H_i_s');

        $input['file_name'] = $temp_file[0] . '.'. $temp_file[1];

        $saveas = $input['file_name'];

        $s3 = AWS::createClient('s3');
		$s3->putObject(array(
			'ACL' => 'public-read',
			'Bucket' => 'asset.plexuss.com',
			'Key' => $bucket_url . '/' . $saveas,
			'SourceFile' => $filePath,
		));

        $public_path = 'https://s3-us-west-2.amazonaws.com/'. $bucket_url."/".$saveas;

        unlink($filePath);

        return $saveas;
    }

    protected function generalUploadPic($input, $file_upload_name, $bucket_url, $prepend_file_name = NULL){
    	$ret = array();
  		if (!isset($input) || !isset($file_upload_name) || !isset($bucket_url)) {
  			$ret['status'] = "failed";

  			return $ret;
  		}

  		// Get file info
  		if (!is_string($file_upload_name) && $file_upload_name == true) {
  			$file = $input;
  		}
  		else {
  			$file = Request::file($file_upload_name);
  		}

		$path = $file->getRealPath();

		$filename = $file->getClientOriginalName();
		$ext = $file->getClientOriginalExtension();
		// $mime = $file->getMimeType();
		$file_path = pathinfo($filename);

		if (!isset($prepend_file_name)) {
			$saveas = $file_path['filename'] . '_'. date('Y_m_d_H_i_s') . "." . strtolower($ext);
		}else{
			$saveas = $prepend_file_name.'_'.$file_path['filename'] . "." . strtolower($ext);
		}
		// $this->customdd("====================file====================<br/>");
		// $this->customdd($file);
		// $this->customdd("====================path====================<br/>");
		// $this->customdd($path);
		$img_data = file_get_contents($path);
		// $this->customdd("====================img_data====================<br/>");
		// $this->customdd($img_data);
		$base64 = base64_encode($img_data);
		// $this->customdd("====================base64====================<br/>");
		// $this->customdd($base64);
		// dd(1231);
		$imageDir = storage_path().'/img/';

        $filePath =  $this->saveBase64ImagePng($base64, $imageDir, trim($saveas));

        $this->compressImage($filePath, $filePath, 60);

  		// $factory = new \ImageOptimizer\OptimizerFactory();
		// $optimizer = $factory->get();

		// $optimizer->optimize($filePath);

 		// $optimizerChain = OptimizerChainFactory::create();
		// $optimizerChain->optimize($filePath);

        $bucket_url = str_replace("asset.plexuss.com/", "", $bucket_url);

        $s3 = AWS::createClient('s3');

		$s3->putObject(array(
			'ACL' => 'public-read',
			'Bucket' => 'asset.plexuss.com',
			'Key' => $bucket_url . '/' . $saveas,
			'SourceFile' => $filePath,
		));

		unlink($filePath);

		$public_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/";

		$filename ? $ret['filename'] = $filename : null;
		$ret['status'] = "success";
		// $ret['mime_type'] = $mime;
        $ret['saved_as'] = $saveas;
		$ret['url']	   = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/'. $bucket_url."/". $saveas;

		return $ret;
    }

    private function compressImage($source, $destination, $quality) {

	  $info = getimagesize($source);

	  if ($info['mime'] == 'image/jpeg')
	    $image = imagecreatefromjpeg($source);

	  elseif ($info['mime'] == 'image/gif')
	    $image = imagecreatefromgif($source);

	  elseif ($info['mime'] == 'image/png')
	    $image = imagecreatefrompng($source);

	  imagejpeg($image, $destination, $quality);
	}

    private function saveBase64ImagePng($base64Image, $imageDir, $fileName){
    	ini_set('memory_limit', '40M');
        //set name of the image file

        $base64Image = trim($base64Image);
        $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
        $base64Image = str_replace(' ', '+', $base64Image);

        $imageData = base64_decode($base64Image);
        //Set image whole path here
        $filePath = $imageDir . $fileName;


       file_put_contents($filePath, $imageData);

       return $filePath;
    }

    public function plexussAppSendInvitation(){

    	$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (Cache::has(env('ENVIRONMENT'). '_mobileAppInvite_'. $data['ip'])) {
			$count = Cache::get(env('ENVIRONMENT'). '_mobileAppInvite_'. $data['ip']);
			if ($count >= 10) {
				return "Too many attempts, retry again in a day.";
			}else{
				Cache::increment(env('ENVIRONMENT'). '_mobileAppInvite_'. $data['ip']);
			}
		}else{
			$count = 1;
			Cache::put(env('ENVIRONMENT'). '_mobileAppInvite_'. $data['ip'], $count, 1440);
		}

		$msg = "Download Plexuss International College Application \n(iOS) - http://apple.co/2x0hv8I \n(Android) - http://bit.ly/2MSG5U7";

    	$input = Request::all();

    	$data = array();
		$data['msg'] = $msg;
		$data['from'] = '8777463228';
		$data['to']   = $input['phone'];
		$data['college_id'] = NULL;

		$data['receiver_user_id'] = isset($data['user_id']) ? $data['user_id'] : NULL;
		$data['user_id'] = -1;
		$data['campaign_id']  = NULL;
		$data['smsBy'] = 'Plexuss Mobile Invitation';

		$tc = new TwilioController();
		$response = $tc->sendSingleSms($data);

		return "success";
    }

    protected function validateAdminPremium() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        if (isset($data['is_organization']) && $data['is_organization'] === 1) {
            $todays_date = strtotime(date('Y-m-d'));
            $trial_end_date = isset($data['premier_trial_end_date_ACTUAL'])
                ? strtotime($data['premier_trial_end_date_ACTUAL']) : null;

            $is_admin_premium = false;

            if (isset($data['bachelor_plan']) && $data['bachelor_plan'] === 1) {
                $is_admin_premium = true;
            }

            if ($todays_date <= $trial_end_date) {
                $is_admin_premium = true;
            }
        } else {
            $is_admin_premium = false;
        }

        return $is_admin_premium;
    }

    public function isMobile(){
    	$device = Agent::isMobile();
    	return $device;
    }

    public function testSES(){

    	// $ses = AWS::createClient('ses');

    	// $arr = array();

    	// $val = array();
    	// $val['TemplateName'] = 'your-student-recommendations-w-dynamic-content';
    	// // $val['SubjectPart']  = $subject;
    	// // $val['HtmlPart']	 = $html;

    	// // $arr['Template']     = array();
    	// // $arr['Template']     = $val;

    	// $ses->DeleteTemplate($val);

    	// dd(93103129031);
    	$url = "https://api.sparkpost.com/api/v1/templates";
        $client = new Client(['base_uri' => 'http://httpbin.org']);

        try {
            $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => env('SPARKPOST_KEY'),
                            'Accept'        => 'application/json'
                            ]
                        ]);

            $result = json_decode($response->getBody()->getContents(), true);

            } catch (\Exception $e) {

	            $this->customdd($e);
	            exit();
        }

        $ses = AWS::createClient('ses');
        $count = 1;
        foreach ($result['results'] as $key) {

      //   	if ($count <= 86) {
	    	// 	$count++;
	    	// 	continue;
	    	// }

        	// SENDGRID STARTS

        	$url = "https://api.sendgrid.com/v3/templates";
	        $client = new Client(['base_uri' => 'http://httpbin.org']);


            $response = $client->request('POST', $url, [
                        'headers' => [
                            'Authorization' => env('SENDGRID_KEY'),
                            'Accept'        => 'application/json',
                            'query' => [
								'name' => $key['id']]
                            ]
                        ]);

            $sg_inner_result = json_decode($response->getBody()->getContents(), true);

            dd($sg_inner_result);
        	// SENDGRID ENDS

	    	// SPARKPOST PART
        	$url = "https://api.sparkpost.com/api/v1/templates/".$key['id'];
	        $client = new Client(['base_uri' => 'http://httpbin.org']);


            $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => env('SPARKPOST_KEY'),
                            'Accept'        => 'application/json'
                            ]
                        ]);

            $inner_result = json_decode($response->getBody()->getContents(), true);

            // SPARKPOST PART ENDS

            $subject = $inner_result['results']['content']['subject'];
            $html    = $inner_result['results']['content']['html'];

            $html = str_replace("{{{", "{{", $html);
            $html = str_replace("}}}", "}}", $html);

            $arr = array();

	    	$val = array();
	    	$val['TemplateName'] = $key['id'];
	    	$val['SubjectPart']  = $subject;
	    	$val['HtmlPart']	 = $html;

	    	$arr['Template']     = array();
	    	$arr['Template']     = $val;

	    	// $this->customdd($arr);
	    	// exit();
	    	$ses->CreateTemplate($arr);

	    	sleep(1);
        }






    	dd(31321);
    	$this->customdd($ses);
    	exit();
    }

    public function testPixel(){
    	$data = array();
    	$data['currentPage'] = 'frontPage';

    	return View( 'public.homepage.test', $data);
    }

    // function defination to convert array to xml
    public function array_to_xml( $data, &$xml_data ) {
	    foreach( $data as $key => $value ) {
	        if( is_numeric($key) ){
	            $key = 'item'.$key; //dealing with <0/>..<n/> issues
	        }
	        if( is_array($value) ) {
	            $subnode = $xml_data->addChild($key);
	            $this->array_to_xml($value, $subnode);
	        } else {
	            $xml_data->addChild("$key",htmlspecialchars("$value"));
	        }
	     }

	     return $xml_data;
	}

	// Check if the external url exist
	public function remote_file_exists($url){
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_exec($ch);
	    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    if( $httpCode == 200 ){return true;}
	    return false;
	}


	public function customUpdateOrCreate($table, $attr, $val){

		$qry  = $table::on('rds1');
		foreach ($attr as $key => $value) {
			$qry = $qry->where($key, $value);
		}
		$qry = $qry->first();
		if (isset($qry)) {
			if (isset($val['created_at'])) {
				unset($val['created_at']);
			}
			$update = $table::where('id', $qry->id)
							->update($val);
			$ret =  $qry;
		}else{
			$save = $table::create($val);
			$ret =  $save;
		}
		return $ret;
	}
	
	public function getIp(){
	    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
	        if (array_key_exists($key, $_SERVER) === true){
	            foreach (explode(',', $_SERVER[$key]) as $ip){
	                $ip = trim($ip); // just to be safe
	                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
	                    return $ip;
	                }
	            }
	        }
	    }
	}

	// is image method
	public function isImage($url){
		$params = array('http' => array(
		          'method' => 'HEAD'
		       ));
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp)
			return false;  // Problem with url

		$meta = stream_get_meta_data($fp);
		if ($meta === false)
		{
			fclose($fp);
			return false;  // Problem reading data from url
		}

		$wrapper_data = $meta["wrapper_data"];
		if(is_array($wrapper_data)){
			foreach(array_keys($wrapper_data) as $hh){
			  if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") // strlen("Content-Type: image") == 19
			  {
			    fclose($fp);
			    return true;
			  }
			}
		}

		fclose($fp);
		return false;
  	}

  	// convert timezone
  	public function convertTimeZone($timestamp, $from_timezone, $to_timezone){
  		$tmp_created_at = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $from_timezone);
	    $tmp_created_at->setTimezone($to_timezone);
	    return $tmp_created_at;
  	}

  	public function hashIdForSocial($id){
  		if (!isset($id) || empty($id)) {
  			return $id;
  		}
  		$hashids = new Hashids('', 25);
  		$ret = $hashids->encode($id);

  		return $ret;
  	}

  	public function decodeIdForSocial($id){
  		if (!isset($id) || empty($id)) {
  			return $id;
  		}
  		$hashids = new Hashids('', 25);
  		$ret = $hashids->decode($id);
  		if (!isset($ret) || empty($ret)) {
  			dd("invalid id");
  		}
  		return $ret[0];
  	}

  	public function testId(){
  		$id = "NDm8VvJ4open2pe7Az1XPYRrK";

  		$hashids = new Hashids('', 25);
  		$ret = $hashids->decode($id);
  		if (!isset($ret) || empty($ret)) {
  			dd("invalid id");
  		}
  		return $ret[0];
  	}
}
