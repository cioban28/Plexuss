<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, DB, Session;
use App\User, App\PlexussAdmin, App\PortalNotification, App\Country, App\NewsArticle, App\UserClosedPin, App\Recruitment, App\LocalizationPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\RecommendModalShow;

class HomeController extends Controller
{
  public function index() {

		//Redirect to public homepage if user is not logged in.
		if ( !Auth::check() ) {
			return redirect('/signin');
		}

		//Get user logged in info.
		$user = User::find( Auth::user()->id );

		//Set admin array for topnav Link
		$admin = PlexussAdmin::on('rds1')->where('user_id', '=', $user->id)->get()->toArray();
		$admin = array_shift($admin);

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$my_user_id = Session::get('userinfo.id');


		$data['admin'] = $admin;


		$data['title'] = 'Plexuss Dashboard';
		$data['currentPage'] = 'home';
		// $token = AjaxToken::on('rds1')->where('user_id', '=', $user->id)->first();

		// if(!$token) {
		// 	Auth::logout();
		// 	return redirect('/');
		// }

        /* check user in email suppression list */
        $emailSuppressionList = EmailSuppressionList::on('rds1')->where('uid', $user->id)->first();

        //get the count of Recommends this user has to populate the indicators on home
		$data['recommendCount'] = PortalNotification::on('rds1')->where('user_id', $user->id)
		->where('is_recommend_trash', '!=', 1)
		->count();




		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;

		// $data['ajaxtoken'] = $token->token;

		$data['is_welcome'] = 0;
		// First time homepage modal data
		if($user->showFirstTimeHomepageModal) {
			$data['showFirstTimeHomepageModal'] = $user->showFirstTimeHomepageModal;
			// include countries
			$countries_raw = Country::all()->toArray();
			$countries = array();
			foreach( $countries_raw as $val ){
				$countries[$val['id']] = $val['country_name'];
			}
			$data['countries'] = $countries;
			Cache::put('from_welcome_page', 'welcome', 20);

			$data['is_welcome'] = 1;
		}

		//******************************************************************
		//WE NEED A WAY TO PASS IN A REDIRECT LINK FOR FUTURE REDIRECTS!!!!!!
		//******************************************************************
		//Here we can escape this and redirect them to the saved session place IF they are NOT a new user!!!

		$input = Request::all();

		if(isset($input['requestType'])){
			//set the request type and if you need more values set do the below.
			Session::put('requestType', $input['requestType']);

		}

		if(isset($input['collegeId'])){
			Session::put('RecruitCollegeId', $input['collegeId']);
		}

		if (Session::has('redirect') ) {

			$redirect_to_page = Session::get('redirect');

			if( $redirect_to_page == 'carepackage' ){
				$redirect_to_page .= '#cart';
			}

			Session::forget('redirect');
			return redirect($redirect_to_page);//THIS NEEDS TO BE FIXED TO ALLOW DYNAMIC LINKS!!!!
		}


		//dd(Request::all());
		//handle all sessions, set or reset based on each session
		$listSession = Session::all();

		//dd($listSession);
		foreach( array_keys( $listSession ) as $index=>$key ) {

			$data = $this->handleAllSessions($key, $data);

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

		$news = new NewsArticle;
		$newsdata = $news->HomeNewsDetails();

		// Get user's closed pins, if any
		$pins = UserClosedPin::on('rds1')->where('user_id', '=', $user->id)
			->select('getting_started_1', 'getting_started_2', 'getting_started_3', 'getting_started_4', 'getting_started_5', 'getting_started_6', 'getting_started_7', 'getting_started_8', 'getting_started_9')
			->get()
			->toArray();

		//Only put pins in $data if they're set, otherwise, view
		//will show all pins
		if(!empty($pins)){
			$pins = $pins[0];

			//Logic to hide orange getting started circle/arrow
			foreach($pins as $key => $val){
				if($val == 1){
					$show_gs_pin = true;
					break;
				}
			}
			if(!isset($show_gs_pin)){
				$data['hide_gs_circle_arrow'] = true;
			}
			$data['pins'] = $pins;
		}

		if(count($newsdata)>0 && $newsdata!=''){
			$data['newsdata'] = $newsdata;
		}

		$quizs = new QuizController();

		$data['quizInfo'] = $quizs->LoadQuiz();

		$recruitment = Recruitment::on('bk')->where('user_id' ,$my_user_id)
											->where('college_recruit', 1)
											->count();
		$data['num_of_recruit'] = $recruitment;

		if (!isset($data['topnav_notifications']['data'])) {
			$topnav_notifications = null;
		}else{
			$topnav_notifications = $data['topnav_notifications']['data'];
		}

		$data['right_handside_carousel'] = $this->getRightHandSide($data);

		$colleges_viewed_cnt = 0;

		if (isset($topnav_notifications)) {
			foreach ($topnav_notifications as $key) {
				//commmand 1
				if($key['command'] == 1 && $key['type'] == 'user'){
					$colleges_viewed_cnt++;
				}
			}
		}

		$data['num_of_colleges_viewed_you'] = $colleges_viewed_cnt;

		if( Cache::has($data['user_id'].'userinfo_contact_list') ){
			$data['contactList'] = Cache::get($data['user_id'].'userinfo_contact_list');
			// Session::forget('userinfo_contact_list');
			// dd(Cache::has('from_welcome_page'));
		}

		// $start_date = Carbon::create(2016, 12, 7); //this is hardcoded b/c we want to start showing the modal every two week from this date
		// $today = Carbon::now();
		// $diff_in_days = $start_date->diffInDays($today);

		// only show user feedback modal once a session
		if(!Cache::has(env('ENVIRONMENT') .'_'. $data['user_id'].'_user_feedback_college')){

			// add to session to make not that user has seen this modal at least once during this session
			Cache::add(env('ENVIRONMENT') .'_'. $data['user_id'].'_user_feedback_college', 'lablab', 40320);
			$rec = new Recruitment;
			$data['user_feedback_college'] = $rec->getUserFeedbackColleges($data['user_id']);

			if (isset($data['user_feedback_college'])) {
				if (isset($data['user_feedback_college']->rec_id)) {
					$data['user_feedback_college']->rec_id = Crypt::encrypt($data['user_feedback_college']->rec_id);
				}
				if (isset($data['user_feedback_college']->pr_id)) {
					$data['user_feedback_college']->pr_id = Crypt::encrypt($data['user_feedback_college']->pr_id);
				}

				// FIX APPLICATION URL
				if( !empty(trim($data['user_feedback_college']->app_url)) ){
					if (!(strpos($data['user_feedback_college']->app_url, 'http') !== false)){
						$data['user_feedback_college']->app_url = "http://".$data['user_feedback_college']->app_url;
					}elseif (strpos($data['user_feedback_college']->app_url, 'http//') !== false){
						$data['user_feedback_college']->app_url = str_replace("http//", "http://", $data['user_feedback_college']->app_url);
					}elseif (strpos($data['user_feedback_college']->app_url, 'https//') !== false){
						$data['user_feedback_college']->app_url = str_replace("https//", "https://", $data['user_feedback_college']->app_url);
					}
				}else{
					$data['user_feedback_college']->app_url = null;
				}
			}else{
				$data['user_feedback_college'] = (object)array();
				$data['user_feedback_college']->hideModal = true;
			}

		}else{
			$data['user_feedback_college'] = (object)array();
			$data['user_feedback_college']->hideModal = true;
		}

		$show_home_modal = RecommendModalShow::on('rds1')
											 ->where('user_id', $user->id)
											 ->select('home_modal_show')
											 ->first();


		if (isset($show_home_modal)) {
			$show_home_modal = $show_home_modal->toArray();
			$data['show_modal'] = $show_home_modal['home_modal_show'];
		}else{

			$attr = array('user_id' => $user->id, 'recommend_modal_show' => 1, 'home_modal_show' => 1);
			RecommendModalShow::updateOrCreate($attr, $attr);
			$data['show_modal'] = 1;
		}

		if ($data['show_modal'] == 1) {
			if( Session::has('show_home_modal') ){
	        // Session::put('show_home_modal', 0);
		    	$data['show_modal'] = 0;//Session::get('show_home_modal');
		    }else{
		    	$data['show_modal'] = 1;
		        Session::put('show_home_modal', 1);
		    }
	   	}

    	// echo $data['has_session_modal'];
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// print_r("<br><br>");
		return View('private.home.home', $data);
	}

	//////////////////
	public function premiumIndex() {

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (isset($input)) {

			$arr = $this->iplookup();
			if (!Session::has('signup_params')) {
				Session::put('signup_params', $input);
			}
			if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
				Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
			}

		}

		// Localization code here
		$lp = new LocalizationPage;
		if (isset($input['lang']) && $input['lang'] != "en") {

			$data['page_content'] = $lp->getPageContent('premium-plans-info', $input['lang']);

			if (empty($data['page_content'])) {
				$data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
			}

		}else{
			$data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
		}
		// Localization code ends here

		$data['title'] = 'Plexuss Premium';
		$data['currentPage'] = 'premium';

		return View('products.premium', $data);
	}

	/////////////////

	public function premiumPage($country = null) {

		//Redirect to public homepage if user is not logged in.
		// if ( !Auth::check() ) {
		// 	return redirect('/signin');
		// }

		// //Get user logged in info.
		// $user = User::find( Auth::user()->id );

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (isset($country)) {
			if($country=='india'){
				$page='products.premiumPlexuss';
			}
		}

		if (!isset($page)) {
			$iplookup = $this->iplookup($this->getIp());
			if (isset($iplookup) && $iplookup['countryName'] == 'India') {
				$page='products.premiumPlexuss';
			}else{
				$page='products.premiumGeneral';
			}
		}

		// Temporary code
		if (!isset($page)) {
			$page='products.premiumGeneral';
		}

		if (isset($input)) {

			$arr = $this->iplookup();
			if (!Session::has('signup_params')) {
				Session::put('signup_params', $input);
			}
			if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
				Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
			}

		}

		// Localization code here
		// $lp = new LocalizationPage;
		// if (isset($input['lang']) && $input['lang'] != "en") {

		// 	$data['page_content'] = $lp->getPageContent('premium-plans-info', $input['lang']);

		// 	if (empty($data['page_content'])) {
		// 		$data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
		// 	}

		// }else{
		// 	$data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
		// }
		// Localization code ends here

		$data['title'] 		 = 'Plexuss Premium';
		$data['currentPage'] = 'premium';

		$check = false;
		$data['utm_source']   = '?india';
		$data['utm_content']  = '';
		$data['utm_medium']	  = '';
		$data['utm_campaign'] = '';
		$data['utm_term']     = '';

		if (isset($input['utm_source'])) {
			if (!$check) {
				$data['utm_source']  = '?';
				$check = true;
			}else{
				$data['utm_source']  = '&';
			}
			$data['utm_source']  .= 'utm_source='.$input['utm_source'];
		}
		if (isset($input['utm_content'])) {
			if (!$check) {
				$data['utm_content']  = '?';
				$check = true;
			}else{
				$data['utm_content']  = '&';
			}
			$data['utm_content']  .= 'utm_content='.$input['utm_content'];
		}
		if (isset($input['utm_medium'])) {
			$data['utm_medium']  = $input['utm_medium'];
		}
		if (isset($input['utm_campaign'])) {
			if (!$check) {
				$data['utm_campaign']  = '?';
				$check = true;
			}else{
				$data['utm_campaign']  = '&';
			}
			$data['utm_campaign']  .= 'utm_campaign='.$input['utm_campaign'];
		}
		if (isset($input['utm_term'])) {
			if (!$check) {
				$data['utm_term']  = '?';
				$check = true;
			}else{
				$data['utm_term']  = '&';
			}
			$data['utm_term']  .= 'utm_term='.$input['utm_term'];
		}

		if (isset($input) && isset($input['fs'])) {

			if ($input['fs'] == 1) {
				$data['showSignUp'] = 1;
			}else{
				$data['showSignUp'] = 0;
			}
		}

		if(isset($data['profile_img_loc'])  && !empty($data['profile_img_loc'])){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}else{
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/images/profile/default.png";
		}
		
		$data['profile_img_loc'] = $src;
		
		return View($page, $data);
	}

	public function premiumGeneral() {

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (isset($input)) {

			$arr = $this->iplookup();
			if (!Session::has('signup_params')) {
				Session::put('signup_params', $input);
			}
			if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
				Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
			}

		}

		$data['profile_img_loc'] = isset($data['profile_img_loc']) ? $data['profile_img_loc'] : "/images/profile/default.png";

		$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];

		// Localization code here
		// $lp = new LocalizationPage;
		// if (isset($input['lang']) && $input['lang'] != "en") {

		// 	$data['page_content'] = $lp->getPageContent('premium-plans-info', $input['lang']);

		// 	if (empty($data['page_content'])) {
		// 		$data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
		// 	}

		// }else{
		// 	$data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
		// }
		// Localization code ends here

		$data['title'] = 'Plexuss Premium';
		$data['currentPage'] = 'premium';

		return View('products.premiumGeneral', $data);
	}

	private function handleAllSessions($sessionName, $data){

		switch ($sessionName) {
			case 'RecruitCollegeId':
				$data[$sessionName] = Session::get($sessionName);
				Session::forget($sessionName);
				break;
			/*
			case 'redirect':
				$data[$sessionName] = Session::get($sessionName);

				//dd('jere');
				return redirect('/webinar');
				break;
			*/
			case 'requestType':

				$tmp = Session::get($sessionName);
				Session::forget($sessionName);
				switch ($tmp) {
					case 'prep':
						$data['showPrepSchoolModal'] = 1;
						break;


					default:
						# code...
						break;
				}
				break;

			default:
				# code...
				break;
		}

		return $data;
	}

	public function modal(){
		$input = Request::all();

		$attr = array('user_id' => $input['user_id']);
    	$val  = array('user_id' => $input['user_id'] , 'home_modal_show' => '0');

		if(RecommendModalShow::updateOrCreate($attr, $val))
		{
			return 'success';
		}
		return 'error';
	}
}
