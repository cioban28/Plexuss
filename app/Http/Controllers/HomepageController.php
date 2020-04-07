<?php

namespace App\Http\Controllers;

use Request;

use App\Http\Controllers\ViewDataController;
use App\Http\Controllers\SearchController;

use App\FrontpageBackgroundImages, App\Organization, App\NewsArticle, App\ZipCodes, App\Religion, App\Country, App\Search;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use DB;
use Jenssegers\Agent\Agent;

ini_set('memory_limit', '1G'); // or you could use 1G

class HomepageController extends Controller
{
    //

	private $mobileItemsToTakeOnReady = 6;
	private $mobileItemsToTakeBeforeReady = 6;
	private $itemsToTakeOnReady = 6;
	private $desktopItemsToTakeBeforeReady = 6;
	private $is_any_school_live = 0;

	private $webinar_id = 6;

	/**
	 * Redirect to public homepage enabling chat tab
	 *
	 * @return view or redirect
	 */

	public function enableChat() {
		return $this->index(true);
	}

	/**
	 * Redirect to public homepage if user is not logged in or to /home if user is logged in.
	 *
	 * @return view or redirect
	 */

	public function index($enable_chat = null) {
		// dd('plexuss.com/sales/loginas/'. Crypt::encrypt(127) );

		$incoming_enable_chat = $enable_chat;

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		// Send users back to home page, if they are already signed in.
		if (isset($data['signed_in']) && $data['signed_in'] == 1) {
			return redirect('/home');
		}

		// Go  to india page if you are from india
		$iplookup = $this->iplookup($this->getIp());
		if (isset($iplookup) && $iplookup['countryName'] == 'India') {
			return redirect('/india');
		}elseif (isset($iplookup) && $iplookup['countryName'] !== 'United States') {
			return redirect('/general');
		}

		$data['currentPage'] = 'frontPage';
		
		if (isset($iplookup) && $iplookup['countryName'] !== 'United States') {
			$data['show_regional_reps'] = true;
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

        $country = new Country();

        $data['country_code_list'] = $country->getAllCountriesAndIdsWithCountryCode();

        if (isset($iplookup) && isset($iplookup['countryName'])) {
            $is_gdpr = Country::on('rds1')
                              ->where('country_name', '=', $iplookup['countryName'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();

            $data['is_gdpr'] = $is_gdpr;
        }

		//get frontpage background image, school name, and slug 
		// test comment
		$bg_image_return = FrontpageBackgroundImages::on('rds1')->where('is_active', '=', 1)->get()->toArray();

		if( isset($bg_image_return) && !empty($bg_image_return) && count($bg_image_return) != 0 ){
			foreach ($bg_image_return as $key => $value) {
				$data['frontpage_bg_info']['image'] = $value['image_url'];
				$data['frontpage_bg_info']['school'] = $value['school_belongs_to'];
				$data['frontpage_bg_info']['slug'] = $value['school_slug'];
				$data['frontpage_bg_info']['is_video'] = $value['is_video'];
				$data['frontpage_bg_info']['poster'] = $value['video_poster'];
				$data['frontpage_bg_info']['custom_class'] = $value['custom_class'];
				$data['frontpage_bg_info']['show_chat_btn'] = $value['show_chat_button'];
			}
		}
		
		// if (isset($enable_chat)) {
		// 	$data['enable_chat'] = true;
		// }else{
		// 	$data['enable_chat'] = false;
		// }

		// if ($this->is_any_school_live == 1) {
		// 	$data['enable_chat'] = true;
		// }

        $data['enable_chat'] = false;
        
		$current_live_colleges = $this->current_live_colleges();

		if (isset($current_live_colleges) && !empty($current_live_colleges)) {
			$data['college_chatting'] = true;
		}
		// // $data['enable_chat'] = false;
		// if (!isset($incoming_enable_chat) && isset($data['enable_chat']) && $data['enable_chat'] == true) {
		// 	return redirect('/chat');
		// }
		$agent = new Agent();
		$data['is_mobile'] = $agent->isMobile();

		if ((isset($data['webinar_is_live']) && $data['webinar_is_live'] == true) ||
			(isset($data['can_show_webinar']) && $data['can_show_webinar'] == true)) {
				
			//flag for what js and css to include 
			//because both homepage and homepageWebinarSignup share a footer
			$data['is_webinar'] = true;

			return View( 'public.homepage.homepageWebinarSignup', $data);
		}else{
			// return View( 'public.homepage.homepage', $data);
			return View( 'public.homepage.index', $data);
		}
	}

	public function signedOutIndex(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'signedOutIndex';

		return View( 'public.homepage.index', $data);
	}

	public function indianIndex($enable_chat = null) {

		$incoming_enable_chat = $enable_chat;

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'frontPage';

		$iplookup = $this->iplookup($this->getIp());
		
		if (isset($iplookup) && $iplookup['countryName'] !== 'United States') {
			$data['show_regional_reps'] = true;
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

        $country = new Country();

        $data['country_code_list'] = $country->getAllCountriesAndIdsWithCountryCode();

        if (isset($iplookup) && isset($iplookup['countryName'])) {
            $is_gdpr = Country::on('rds1')
                              ->where('country_name', '=', $iplookup['countryName'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();

            $data['is_gdpr'] = $is_gdpr;
        }

		//get frontpage background image, school name, and slug 
		// test comment
		$bg_image_return = FrontpageBackgroundImages::on('rds1')->where('is_active', '=', 1)->get()->toArray();

		if( isset($bg_image_return) && !empty($bg_image_return) && count($bg_image_return) != 0 ){
			foreach ($bg_image_return as $key => $value) {
				$data['frontpage_bg_info']['image'] = $value['image_url'];
				$data['frontpage_bg_info']['school'] = $value['school_belongs_to'];
				$data['frontpage_bg_info']['slug'] = $value['school_slug'];
				$data['frontpage_bg_info']['is_video'] = $value['is_video'];
				$data['frontpage_bg_info']['poster'] = $value['video_poster'];
				$data['frontpage_bg_info']['custom_class'] = $value['custom_class'];
				$data['frontpage_bg_info']['show_chat_btn'] = $value['show_chat_button'];
			}
		}
		
		// if (isset($enable_chat)) {
		// 	$data['enable_chat'] = true;
		// }else{
		// 	$data['enable_chat'] = false;
		// }

		// if ($this->is_any_school_live == 1) {
		// 	$data['enable_chat'] = true;
		// }

        $data['enable_chat'] = false;
        
		$current_live_colleges = $this->current_live_colleges();

		if (isset($current_live_colleges) && !empty($current_live_colleges)) {
			$data['college_chatting'] = true;
		}
		// // $data['enable_chat'] = false;
		// if (!isset($incoming_enable_chat) && isset($data['enable_chat']) && $data['enable_chat'] == true) {
		// 	return redirect('/chat');
		// }
		$agent = new Agent();
		$data['is_mobile'] = $agent->isMobile();

		if ((isset($data['webinar_is_live']) && $data['webinar_is_live'] == true) ||
			(isset($data['can_show_webinar']) && $data['can_show_webinar'] == true)) {
				
			//flag for what js and css to include 
			//because both homepage and homepageWebinarSignup share a footer
			$data['is_webinar'] = true;

			return View( 'public.homepage.homepageWebinarSignup', $data);
		}else{
			// return View( 'public.homepage.indianHomePage', $data);
			return View( 'public.homepage.index', $data);
		}
	}
	public function generalIndex($enable_chat = null) {

		$incoming_enable_chat = $enable_chat;

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'frontPage';

		$iplookup = $this->iplookup($_SERVER['REMOTE_ADDR']);
		
		if (isset($iplookup) && $iplookup['countryName'] !== 'United States') {
			$data['show_regional_reps'] = true;
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

        $country = new Country();

        $data['country_code_list'] = $country->getAllCountriesAndIdsWithCountryCode();

        if (isset($iplookup) && isset($iplookup['countryName'])) {
            $is_gdpr = Country::on('rds1')
                              ->where('country_name', '=', $iplookup['countryName'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();

            $data['is_gdpr'] = $is_gdpr;
        }

		//get frontpage background image, school name, and slug 
		// test comment
		$bg_image_return = FrontpageBackgroundImages::on('rds1')->where('is_active', '=', 1)->get()->toArray();

		if( isset($bg_image_return) && !empty($bg_image_return) && count($bg_image_return) != 0 ){
			foreach ($bg_image_return as $key => $value) {
				$data['frontpage_bg_info']['image'] = $value['image_url'];
				$data['frontpage_bg_info']['school'] = $value['school_belongs_to'];
				$data['frontpage_bg_info']['slug'] = $value['school_slug'];
				$data['frontpage_bg_info']['is_video'] = $value['is_video'];
				$data['frontpage_bg_info']['poster'] = $value['video_poster'];
				$data['frontpage_bg_info']['custom_class'] = $value['custom_class'];
				$data['frontpage_bg_info']['show_chat_btn'] = $value['show_chat_button'];
			}
		}
		
        $data['enable_chat'] = false;
        
		$current_live_colleges = $this->current_live_colleges();

		if (isset($current_live_colleges) && !empty($current_live_colleges)) {
			$data['college_chatting'] = true;
		}
	
		$agent = new Agent();
		$data['is_mobile'] = $agent->isMobile();

		if ((isset($data['webinar_is_live']) && $data['webinar_is_live'] == true) ||
			(isset($data['can_show_webinar']) && $data['can_show_webinar'] == true)) {
				
			$data['is_webinar'] = true;

			return View( 'public.homepage.homepageWebinarSignup', $data);
		}else{
			// return View( 'public.homepage.generalHomePage', $data);
			return View( 'public.homepage.index', $data);
		}
	}

	/**
	 * ajax in more items to each one of the carousels.
	 * @param carouselName: this is the carousel name we want to get the data for
	 * @param offset: this is offset by which we want the next 10 items from.
	 * @return json
	*/
	public function getCarouselItems($carouselName = null, $offset = null){

		$input = Request::all();

		isset($input['page']) ? $page = $input['page'] : $page = 1;

		$ret = array();
		if($carouselName == "near-you-carousel-container-unique"){
			
			$ret = json_encode($this->getSchoolsNearYou($offset));
			return $ret;

		}elseif ($carouselName == "top-ranking-carousel-container-unique") {
			if (Cache::has(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page)){
				$ret = Cache::get(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page);
			}else{
				$ret = json_encode($this->getTopRankedSchools($offset));
				Cache::put(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page, $ret, 1440);
			}
			return $ret;
			
		}elseif ($carouselName == "virtual-tours-carousel-container-unique") {
			if (Cache::has(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page)){
				$ret = Cache::get(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page);
			}else{
				$ret = json_encode($this->getVirtualTourSchools($offset));
				Cache::put(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page, $ret, 1440);
			}
			return $ret;
			
		}elseif ($carouselName == "quad-article-carousel-container-unique") {
			if (Cache::has(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page)){
				$ret = Cache::get(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page);
			}else{
				$ret = json_encode($this->getTheQuad($offset));
				Cache::put(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page, $ret, 1440);
			}
			return $ret;
			
		}elseif ($carouselName == "message-a-college-container-unique"){
			if (Cache::has(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page)){
				$ret = Cache::get(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page);
			}else{
				$ret = json_encode($this->getCollegeReps($offset));
				Cache::put(env('ENVIRONMENT').'_'.'homepagecontroller_getCarouselItems_'.$carouselName.'_'.$page, $ret, 1440);
			}
			return $ret;
		}

		return json_encode($ret);
	}

	/**
	 * get colleges in our network and colleges that are live to chat now.
	 * @return array
	*/
	protected function getOurNetworkSchoolsAndChat($skip = null, $limit = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$current_live_colleges = $this->current_live_colleges();

		$colleges = DB::connection('rds1')->table('colleges as c')
			->select('c.id', 'c.logo_url', 'c.school_name', 'c.city', 'c.state', 'c.slug', 'cr.plexuss as rank', 'ct.country_code', 'aor.id as aor_id')
			->leftjoin('aor_colleges as aorc', 'aorc.college_id', '=', 'c.id')
			->leftjoin('aor', function($q){
				$q->on('aor.id', '=', 'aorc.aor_id');
				$q->on(function($q2){
					$q2->orWhere('aor.id', '=', DB::raw(7))
					   ->orWhere('aor.id', '=', DB::raw(8));
				});
				// $q->whereIn('aor.id', '=', array(7,8));
			})
			->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
			->leftjoin('countries as ct', 'c.country_id', '=', 'ct.id')
			
			->where(function($q){
				$q->orWhere('c.in_our_network', '=', DB::raw(1))
				  ->orWhere('aorc.active', '=', DB::raw(1));
			})
			->where(function($colleges){
				$colleges = $colleges->orWhereNull('aor.id')
									 ->orWhere('aorc.aor_only', 0)
									 ->orWhere('aor.show_on_frontpage', 1);
			});

		if( isset($skip) ){
			$colleges->skip($skip);
		}

		if( isset($limit) ){
			$colleges->limit($limit);
		}

		if(!empty($current_live_colleges)){

			$custom_order_by = '';
			foreach ($current_live_colleges as $key => $value) {
				$custom_order_by .= 'c.id ='. $value .' DESC, ';
			}

			$custom_order_by = substr($custom_order_by, 0, -6);
			$colleges = $colleges->orWhereIn('c.id', $current_live_colleges)
								 ->groupBy('c.id')
								 ->orderBy(DB::raw($custom_order_by), 'DESC')
								 ->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'))
								 ->get();

		}else{
			$colleges = $colleges->groupBy('c.id')
								 ->orderby(DB::raw('`aor`.`id` IS NULL, `aor`.`id`'))
								 ->get();
		}

		$ret = array();

		foreach ($colleges as $key) {
			$temp = array();
			$temp['college_id'] = $key->id;
			$temp['logo_url'] = $key->logo_url;
			$temp['school_name'] = $key->school_name;
			$temp['city'] = $key->city;
			$temp['state'] = $key->state;
			$temp['slug'] = $key->slug;
			$temp['rank'] = $key->rank;
			$temp['isInUserList'] = false;
			$temp['country_code'] = $key->country_code;

			if( $data['signed_in'] == 1 ){
				$recruitment = $this->hasAlreadyAskedToBeRecruited($data['user_id'], $key->id);

				if( isset($recruitment->id) ){
					$temp['isInUserList'] = true;
				}
			}

			if(in_array($key->id, $current_live_colleges )){

				$temp['redirect_url'] = "/college/".$key->slug."/chat";
				$temp['is_live'] = 1;
				$this->is_any_school_live = 1;
			}else{
				$temp['redirect_url'] = "/portal/messages/".$key->id."/college";
				$temp['is_live'] = 0;
			}

			if ($temp['is_live'] == 1) {
				array_unshift($ret , $temp);
			}else{
				$ret[] = $temp;
			}
			
		}

		return $ret;
	}	

	/**
	 * get the schools that are close to user based on their ip address
	 * @param offset: this is offset by which we want the next 10 items from.
	 * @return array
	*/
	public function getSchoolsNearYou($offset = NULL, $is_api = NULL){

		$ret = array();

		$limit = $this->getOffsetNumForMobile();

		// if($offset == NULL){
		// 	$offset = 0;
		// }

		$locationArr = $this->iplookup();

		if (!isset($locationArr['latitude']) || empty($locationArr['latitude'])) {
			$locationArr['latitude'] = '37.910078';
			$locationArr['longitude'] = '-122.065182';
		}

		$query = 'slug, school_name as school_name, city, state, ( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(latitude)) ) ) AS distance ';

		$distance = '( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(latitude)) ) )';

		$colleges = DB::connection('rds1')->table('colleges as c')
			->select( DB::raw( 'c.id as college_id, cr.plexuss as rank, coi.url as img_url, c.logo_url, '.$query ) )
			->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
			->leftjoin('college_overview_images as coi', 'coi.college_id', '=', 'c.id')
			->where( 'verified', '=', 1 )
			->where(DB::raw($distance), '<', 100)
			//->where('coi.url', '!=', '')
			->groupby('c.id')
			//->orderBy('distance', 'ASC')
			->orderby(DB::raw($distance), 'ASC')
			->orderby(DB::raw('`cr`.plexuss IS NULL, `cr`.`plexuss`'), 'ASC')
			// ->having('distance', '<', 100)
			// ->skip($offset)
			// ->limit($limit)
			// ->get();
			->paginate($limit);

		foreach ($colleges as $key) {
			$temp = array();
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
			
			isset($is_api) ? $temp['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$temp['logo_url'] : NULL;

			$temp['slug'] = $key->slug;

			if($key->img_url ==''){
				$temp['img_url'] = 'no-image-default.png';
			}else{	
				$temp['img_url'] = $key->img_url;
			}

			isset($is_api) ? $temp['img_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/'.$temp['img_url'] : NULL;
			$ret[] = $temp;
		}

		return $ret;

	}

	/**
	 * Redirect to public homepage if user is not logged in or to /home if user is logged in.
	 * @param offset: this is offset by which we want the next 10 items from.
	 * @return array
	*/
	public function getTopRankedSchools($offset = NULL, $is_api = NULL){

		$ret = array();
		$locationArr = $this->iplookup();

		$limit = $this->getOffsetNumForMobile();

		if (!isset($locationArr['latitude']) || empty($locationArr['latitude'])) {
			$locationArr['latitude'] = '37.910078';
			$locationArr['longitude'] = '-122.065182';
		}

		$query = '( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(latitude)) ) ) AS distance ';

		$distance = '( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(latitude)) ) )';

		$colleges = DB::connection('rds1')->table('colleges as c')
					->select( DB::raw( $query .', c.id as college_id, cr.plexuss as rank, coi.url as img_url, c.logo_url, c.school_name, c.city, c.state, c.slug' ))
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('college_overview_images as coi', 'coi.college_id', '=', 'c.id')
					->where( 'verified', '=', 1 )
					// ->where(DB::raw($distance), '<', 100)
					//->where('coi.url', '!=', '')
					->groupby('c.id')
					//->orderBy('distance', 'ASC')
					->orderby(DB::raw('`cr`.plexuss IS NULL, `cr`.`plexuss`'), 'ASC')
					// ->orderby(DB::raw($distance), 'ASC')
					// ->having('distance', '<', 100)
					// ->skip($offset)
					// ->limit($limit)
					// ->get();
					->paginate($limit);	

		// if ($offset == NULL) {
		// 	$offset =0;
		// }

		//if the college array size is less than the total college array then college array count should be the limit

		// if(($offset + $limit) > count($colleges)){
		// 	$limit = count($colleges);
		// }else{
		// 	$limit += $offset;
		// }

		foreach ($colleges as $key) {
			$temp = array();
			$temp['college_id'] = $key->college_id;
			$temp['city'] = $key->city;
			$temp['state'] = $key->state;
			$temp['rank'] = $key->rank;
			$temp['slug'] = $key->slug;
			$temp['school_name'] = $key->school_name;
			$temp['distance'] = ceil($key->distance);

			if(isset($key->logo_url)){
				$temp['logo_url'] = $key->logo_url;
			}else{
				$temp['logo_url'] = 'default-missing-college-logo.png';
			}

			isset($is_api) ? $temp['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$temp['logo_url'] : NULL;

			if($key->img_url ==''){
				$temp['img_url'] = 'no-image-default.png';
			}else{	
				$temp['img_url'] = $key->img_url;
			}

			isset($is_api) ? $temp['img_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/'.$temp['img_url'] : NULL;
			
			$ret[] = $temp;			
		}			

		return $ret;

	}

	/**
	 * Redirect to public homepage if user is not logged in or to /home if user is logged in.
	 * @param offset: this is offset by which we want the next 10 items from.
	 * @return array
	*/
	protected function getVirtualTourSchools($offset = NULL){

		$ret = array();
		$locationArr = $this->iplookup();

		$limit = $this->getOffsetNumForMobile();

		if (!isset($locationArr['latitude']) || empty($locationArr['latitude'])) {
			$locationArr['latitude'] = '37.910078';
			$locationArr['longitude'] = '-122.065182';
		}

		$query = '( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(latitude)) ) ) AS distance ';

		$colleges = DB::connection('rds1')->table('colleges as c')
				->select( DB::raw( $query. ', c.id as college_id, cr.plexuss as rank, coi.url as img_url, c.logo_url, c.school_name, c.city, c.state, c.slug' ))
				->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
				->leftjoin('college_overview_images as coi', 'coi.college_id', '=', 'c.id')
				//->where( 'verified', '=', 1 )
				->whereRaw(DB::raw('c.id IN (Select DISTINCT college_id from college_overview_images where is_tour=1)'))
				//->where('coi.url', '!=', '')
				->groupby('c.id')
				//->orderBy('distance', 'ASC')
				->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'))
				//->having('distance', '<', 100)
				//->skip(0)
				//->limit(10)
				// ->get();
				->paginate($limit);

		foreach ($colleges as $key) {
			$temp = array();
			$temp['college_id'] = $key->college_id;
			$temp['city'] = $key->city;
			$temp['state'] = $key->state;
			$temp['rank'] = $key->rank;
			$temp['slug'] = $key->slug;
			$temp['school_name'] = $key->school_name;
			$temp['distance'] = $key->distance;
			//$temp['distance'] = ceil($key->distance);
			if(isset($key->logo_url)){
				$temp['logo_url'] = $key->logo_url;
			}else{
				$temp['logo_url'] = 'default-missing-college-logo.png';
			}

			if($key->img_url ==''){
				$temp['img_url'] = 'no-image-default.png';
			}else{	
				$temp['img_url'] = $key->img_url;
			}
			$ret[] = $temp;

		}

		return $ret;

	}

	/**
	 * Redirect to public homepage if user is not logged in or to /home if user is logged in.
	 * @param offset: this is offset by which we want the next 10 items from.
	 * @return array
	*/
	public function getTheQuad($offset = NULL){

		$ret = array();

		$limit = $this->getOffsetNumForMobile();

		$articles = NewsArticle::on('rds1')
						->where('news_articles.live_status', '1')
						->where('news_articles.news_subcategory_id', '!=', 22)
						->where('news_articles.news_subcategory_id', '!=', 12)
						->where('news_articles.news_subcategory_id', '!=', 11)
						->where('news_articles.news_subcategory_id', '!=', 9)
						->orderby('id', 'DESC')
						->paginate($limit);
		
		foreach ($articles as $key) {
			$temp = array();
			$temp['article_id'] = $key->id;
			$temp['slug'] = $key->slug;
			$temp['title'] = $key->title;
			$temp['author'] = $key->external_author;
			isset($key->author_img) ? $temp['author_img'] = $key->author_img : $temp['author_img'] = 'author_plexuss.png';
			$temp['created_at'] = $key->created_at;
			$temp['is_essay'] = 0;

			if (!isset($key->content) || empty($key->content)) {
				$temp['is_essay'] = 1;
			}

			if($key->img_lg ==''){
				$temp['img_url'] = 'no-image-default.png';
			}else{	
				if( isset($key->has_video) && $key->has_video == 1 ){
					$temp['img_url'] = $key->img_sm;
					$temp['video'] = $key->img_lg;
				}else{
					$temp['img_url'] = $key->img_lg;
				}
			}
			$ret[] = $temp;

		}

		
		return $ret;

	}

	/**
	 * @return array of college reps for Message A College carousel on frontpage
	*/
	public function getCollegeReps($offset = null){
		$ret = array();
		$org = new Organization;
		$limit = $this->getOffsetNumForMobile($offset);
		$ret = $org->getFrontPageReps(null, $limit, $offset);
		return $ret;
	}

	/**
	 * @return the frontpage section view
	*/
	public function getSection($section = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		switch( $section ){
			case 'message_a_college_carousel':
				$data['msg_a_college'] = $this->getCollegeReps();
				break;
			case 'colleges_near_you_carousel':
				$data['near_colleges'] = $this->getSchoolsNearYou();
				break;
			case 'top_ranked_colleges_carousel':
				$data['ranked_colleges'] = $this->getTopRankedSchools();
				break;
			case 'college_virtual_tours_carousel':
				$data['virtual_tour_colleges'] =  $this->getVirtualTourSchools();
				break;
			case 'quad_carousel':
				$data['articles'] = $this->getTheQuad();
				break;
			case 'get_started_section':
				break;
			case 'find_a_college_section':
			
			$avc = new Country();
			$ccountry = $avc->getAvailableCollegeCountriesWithNameId();
			
			
			$zipCodes = new ZipCodes();
			$temp = $zipCodes->getAllUsState();
			
			

			$states = $temp;
			$cities = array('' => 'Select state first' );

			if(Cache::has(env('ENVIRONMENT') .'_'.'frontpage_religions')){
				$tempReligion = Cache::get(env('ENVIRONMENT') .'_'.'frontpage_religions');

			}else{
				$tempReligion =  Religion::on('rds1')->select('religion')->get();
				Cache::forever(env('ENVIRONMENT') .'_'.'frontpage_religions', $tempReligion);
			}	

			$temp = array('' => 'Select a religion' );
			if( isset($tempReligion) ){
				foreach ($tempReligion as $key) {
					$temp[] = $key->religion;
				}
			}
			
			//get Countries
			$country = array();
			$country[''] = 'Select Country...';
			
			foreach( $ccountry as $cval ){
				$abbr = $cval['abbr'];
				$name = $cval['name'];
				$country[$abbr] = $name;
			}
			
			//get states
			if (Cache::has(env('ENVIRONMENT') .'_all_states')) {
				$states = Cache::get(env('ENVIRONMENT') .'_all_states');
			}else{

				$states_raw = DB::table('states')->get();
				$states = array();
				$states[''] = 'Select...';

				if( isset($states_raw) ){
					foreach( $states_raw as $val ){
						$states[$val->state_name] = $val->state_name;
					}
					
					Cache::put(env('ENVIRONMENT') .'_all_states', $states, 120);
				}
				
			}

			//also prepop department select -- with dept_categories
			$searchModel = new Search();
			$depts_cat = $searchModel->getDepts();

			$data['depts_cat'] = $depts_cat;

			$data['country'] = $country;
			$data['states'] = $states;
			$data['cities'] = $cities;
			
			$data['religion'] = $temp;
				break;
			case 'member_colleges_section':
				$data['colleges_in_our_network'] = $this->getOurNetworkSchoolsAndChat(0, 10);
				break;
			case 'compare_colleges_section':
				break;
		}

		return View( 'frontpage.'.$section, $data);
	}


	/**
	 * @return the next 10 members in our network
	*/
	public function getMembersInOurNetwork($skip = null, $limit = null){
		if( !isset($skip) && !isset($limit) ){
			return 'fail';
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$data['colleges_in_our_network'] = $this->getOurNetworkSchoolsAndChat($skip, $limit);

		if( empty($data['colleges_in_our_network']) ){
			return 'done';
		}

		return View( 'frontpage.member_colleges', $data);
	}


	/**
	 * get the number of carousels we want to show on page load of a mobile
	 * @param offset: this is offset by which we want the next X items from.
	 * @return int
	*/
	private function getOffsetNumForMobile($offset = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if($offset == NULL && $data['is_mobile'] == 1){
			return $this->mobileItemsToTakeBeforeReady;
		}else if ($data['is_mobile'] == 1) {
			return $this->mobileItemsToTakeOnReady;
		}else if ($offset == NULL){
			return $this->desktopItemsToTakeBeforeReady;
		}

		return $this->itemsToTakeOnReady;

	}

	/**
	 * Saves the name and email of webinar participants, when the webinar is live and running
	 * @return json
	*/
	public function saveWebinarLiveSignups(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$arr = array();

		if (!isset($input['name']) || !isset($input['email'])) {
			
			$arr['status']    = 'error';
			$arr['error_msg'] = "Error: name and email are required!";
			return json_encode($arr);
		}

		$wls = new WebinarLiveSignup;
		$wls->ip 	   = $data['ip'];
		$wls->user_id  = $data['user_id'];
		$wls->event_id = $this->webinar_id;
		$wls->name 	   = $input['name'];
		$wls->email    = $input['email'];

		$wls->save();


		$wc = new WebinarControllerModel;
		$wc = $wc->isWebinarLive($this->webinar_id);

		if (!isset($wc)) {
			$wc = new WebinarControllerModel;
			$wc = $wc->canShowWebinarFrontPage($this->webinar_id);
		}

		$arr['status'] = 'ok';
		$arr['video']  = $wc->video;

		if ($data['is_mobile'] == true) {
			$arr['video'] = str_replace("560", "320", $arr['video']);
		}

		return json_encode($arr);
	}

	public function getWebinarId(){
		return $this->webinar_id;
	}
}
