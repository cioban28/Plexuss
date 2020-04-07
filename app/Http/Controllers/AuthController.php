<?php

namespace App\Http\Controllers;

use Request, DB, Validator, Session, Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

use App\User,App\UsersAddtlInfo, App\Score, App\CovetedUser, App\ProfanityList, App\Country, App\OrganizationPortal, App\OrganizationBranchPermission, App\OrganizationPortalUser, App\AjaxToken, App\CollegeMessageThreadMembers;
use App\ConfirmToken, App\UsersCustomQuestion, App\AgencyRecruitment;
use GuzzleHttp\Client;
use App\Agency, App\AgencyProfileInfo, App\AgencyProfileInfoServices, App\AgencyProfileInfoHours;
use App\Library\Amplitude;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\ResetPasswordController;
use App\CollegeSelfSignupApplications, App\EmailTemplateSenderProvider, App\UsersPortalEmailEffortLog;

use App\Jobs\PickACollegeProcess;
use App\Jobs\EmailQueueProcess;

use App\Http\Controllers\UtilityController;

class AuthController extends Controller
{
    /**
	 * Display Signin form.
	 *
	 * @return Response
	 */
	public function getSignin() {
		$input = Request::all();

		if ( Auth::check() ) {
			if( isset($input['redirect']) ){
				return redirect($input['redirect']);
			}else{
				return redirect( 'home' );
			}
		}
		
		if( isset($input['redirect']) ){
			Session::put('redirect_from_signin', $input['redirect']);
		}

        $iplookup = $this->iplookup();

        if (isset($iplookup) && isset($iplookup['countryName'])) {
            $is_gdpr = Country::on('rds1')
                              ->where('country_name', '=', $iplookup['countryName'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();
            
            if (isset($is_gdpr) && $is_gdpr == true) {                              
                return redirect('/');
            }
        }

        $date = array();
        $data['currentPage'] = 'signin'; 
        
		return View( 'public.registration.signin' , $data);
	}

	/**
	 * Post Signin form.
	 *
	 * @return Response
	 */
	public function postSignin() {

		$input = Request::all();
		$rules = array( 'email' => 'required', 'password' => array( 'required', 'regex:/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/' ) );// "/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/"

        $ip = $this->iplookup();

        $country_based_on_ip = $ip['countryName'];

		$v = Validator::make( $input, $rules );

		$handshake_power_arr =  array(833429, 463, 791242, 93, 9160, 833660, 127);

		if ( $v->passes() ) {
			$credentials = array( 'email' => Request::get( 'email' ), 'password' => Request::get( 'password' ) );

			if ( Auth::attempt( $credentials, true ) ) {
			
				Session::put('userinfo.session_reset', 1);

				$data = $this->setSignedInUserData();
				
				// if is a studednt and has less than 30 percent send them back to get_started
				$uai = UsersAddtlInfo::on('rds1')->where('user_id', Auth::user()->id)
												 ->first();

				if (!isset($uai->get_started_percent)) {
					if(isset($input['is_api'])){
						return '/get_started';
					}
					return redirect()->intended( '/get_started' );
				}

				if (isset($uai->get_started_percent) && $uai->get_started_percent < 30 && (!isset($data['is_student']) || $data['is_student'] == 1)) {
					if(isset($input['is_api'])){
						return '/get_started';
					}
					return redirect()->intended( '/get_started' );
				}

				if (in_array(Auth::user()->id, $handshake_power_arr)) {
					$handshake_power = true;
				}

				if (isset($handshake_power)) {
					Session::put('handshake_power', 1);
				}

				$fromIntlPage = isset($input['from_intl_students']) && !empty($input['from_intl_students']);

				// Mixpanel PHP starts here
				// // identify the current request as user id
				// $mp = $data['mixpanel'];
				// $mp->identify(Auth::user()->id);

				// // track an event associated to user id
				// $mp->track("Logged In");
				// // Mixpanel PHP ends here

                if( Session::has('redirect_from_signin') ){
                	$redirect = Session::get('redirect_from_signin');
                	if (substr($redirect, 0, 1)  == '/') {
                		$path = $redirect;
                	}else{
                		$path = '/'. $redirect;
                	}
                    Session::forget('redirect_from_signin');

                    // Don't redirect here!!!! this was a bug.
                    if ($path !== 'ajax/profile/getnotifications' && $path !== '/ajax/profile/getnotifications') {
                    	if(isset($input['is_api'])){
							return $path;
	                    }
                    	return redirect($path);
                    }
                }

                if (isset($data['is_scholarship_admin_only']) && $data['is_scholarship_admin_only'] == true) {
					return redirect()->intended( '/scholarshipadmin' );
				}

				if (isset($data['is_organization']) && $data['is_organization'] == 1) {
					if (isset($data['is_aor']) && $data['is_aor'] == 1) {
						if(isset($input['is_api'])){
							return '/admin/manageCollege';
						}
						return redirect()->intended( '/admin/manageCollege' );
					}
					if(isset($input['is_api'])){
						return '/admin';
					}
					return redirect()->intended( '/admin' );
				}

				if (isset($data['agency_collection'])) {
					if(isset($input['is_api'])){
						return '/agency';
					}
					return redirect()->intended( '/agency' );
				}

				if(isset($input['is_api'])){
					return '/home';
				}
				return redirect()->intended( '/home' );

				// if( $fromIntlPage ){
				// 	return redirect($input['from_intl_students']);
				// }

				// $ucq = UsersCustomQuestion::on('rds1')->where('user_id', Auth::user()->id)->first();

			    //             // United States flow
			    //             if ($country_based_on_ip === 'US') {
			    //                 if (Auth::user()->completed_signup == 0) {
			    //                     return redirect()->intended('/get_started');
			    //                 } else {
			    //                     return redirect()->intended('/profile');
			    //                 }

			    //             // International Flow
			    //             } elseif(isset($ucq)) { 
			    //                 $step = $ucq->application_state;

							// 	if (isset($step) && !empty($step) && $step == 'submit') {
							// 		return redirect()->intended( '/profile' );
							// 	}else if (isset($step) && !empty($step)) {
							// 		return redirect()->intended( '/social/one-app/'.$step);
							// 	}else{
							// 		return redirect()->intended( '/social/one-app/');
							// 	}
			    //             }

				// if( Auth::user()->completed_signup == 0 && ( isset($data['is_organization']) && $data['is_organization'] == 0) ){
				// 	return redirect()->intended('/social/one-app');
				// }

				// // If coveted users then redirect to colleges want to recruit you.
				// $cu = CovetedUser::on('rds1')->where('user_id', Auth::user()->id)->first();			
				// if (isset($cu)) {
				// 	return redirect()->intended( '/portal/collegesrecruityou' );	
				// }

				// // if international student, send to intl students page
				// if( Auth::user()->country_id != 1 ){
				// 	return redirect()->intended( '/international-students' );
				// }

				// return redirect()->intended( '/social/one-app/');

			} else {
				$error = array( 'We can not find that email or password in the system.' );
				if(isset($input['is_api'])){
					return '/signin';
				}
				return redirect( 'signin' )->withErrors( $error );
			}
		}
		if(isset($input['is_api'])){
			return '/signin';
		}
		return redirect( 'signin' )->withErrors( $v )->withInput();
	}

	/**
	 * Display Sign up form.
	 *
	 * @return Response
	 */
	public function getSignup() {

		$input = Request::all();

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$arr = $this->iplookup();
		if (isset($input)) {	
			if (!Session::has('signup_params')) {
				Session::put('signup_params', $input);
			}
			if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
				Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
			}
		}
		
		$data['currentPage'] = 'signup'; 

        if (isset($arr) && isset($arr['countryName'])) {
            $is_gdpr = Country::on('rds1')
                              ->where('country_name', '=', $arr['countryName'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();

            $data['is_gdpr'] = $is_gdpr;

            if (isset($is_gdpr) && $is_gdpr == true) {                              
                return redirect('/');
            }
        }

		//Check for requesttype parameter passed into signup
		if(isset($input['requestType'])){
			//set the request type and if you need more values set do the below.
			Session::put('requestType', $input['requestType']);

		}

		if(isset($input['collegeId'])){
			Session::put('RecruitCollegeId', $input['collegeId']);
		}

		if(isset($input['redirect'])){
			Session::put('redirect', $input['redirect']);
			Cache::put('AuthController_redirect_'. $arr['ip']. $input['redirect'], 10);
			$c = new Country;
			$data['countriesAreaCode'] = $c->getAllCountriesAndAreaCodes();
			$data['redirect'] = $input['redirect'];
		}

		//If signed in do show them signup!! send to home
		if ( Auth::check() ) {
			return redirect( 'home' );
		}

		return View( 'public.registration.signup' , $data);
	}

	/**
	 * Store a newly created user in db and send email with token.
	 *
	 * @return Response
	 */
	public function postSignup($is_api = NULL) {

		$input = Request::all();
		/* echo "<pre>";
		 print_r($input);
		 echo "</pre>";
		 exit();*/
		if (isset($is_api)) {
			$ret = array();
		}

		$email = Request::get('email');
		
		$name = Request::get( 'fname' ) . ' ' . Request::get( 'lname' );
		$email = Request::get( 'email' );
		$phone = '';
		$phone = Request::get('country_code');
		$phone = $phone . ' '. Request::get('phone');
		$confirmation = str_random( 20 );
		$adminToken = null;
		$v = Validator::make( $input, User::$rules );
		//print_r($v);exit;

		//used to redirect back if signup from premium upgrade modal
		$fromUpgrade = Request::get('fromUpgradeModal');
		$currentPage = Request::get('currentPage');


		//////// VALIDATION ///////////////////

		//check for profanity in first and last names
		$fname = Request::get( 'fname' );
		$lname= Request::get( 'lname' );
		$name = $fname . ' ' . $lname;
		$email = Request::get('email'); 
		
		//check for profanity

		$fnameList = explode(' ', $fname);
		$lnameList = explode(' ', $lname);
		$testArray = array_merge($fnameList, $lnameList);	

		$fromIntlPage = isset($input['from_intl_students']) && !empty($input['from_intl_students']);
		
		$isProfane = ProfanityList::on('rds1')
					->where(function($query) use ($testArray){
						foreach($testArray as $nameTok)
							$query->orWhere('name', $nameTok);
						
					})->first();
	   
		if(isset($isProfane) && $isProfane != ''){
			$msg = "No profanity please!";
			if (isset($is_api)) {
				$ret['error_message'] = $msg;
				return json_encode($ret);
			}else{
				if(isset($input['footer_page'])){
					return redirect( 'scholarship-get-started' )->withErrors( $msg )->withInput( Request::except( 'password' ) );
				}else{
					return redirect( 'signup' )->withErrors( $msg )->withInput( Request::except( 'password' ) );
				}
			}

			
		}


		//use Validator to check against $rules in User.php
		if ( $v->passes() ) {
			$user = new User;		//if passes, make a new user
			$user->fname = Request::get( 'fname' );
			$user->lname = Request::get( 'lname' );
			$user->email = Request::get( 'email' );
			$user->phone = $phone;
			$user->password = Hash::make( Request::get( 'password' ) );

			$arr = $this->iplookup();

			if(Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])){
				$signup_params = Cache::get(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
				
				$user->utm_source = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
				$user->utm_medium = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
				$user->utm_content= isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
				$user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
				$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
				$adminToken = isset($signup_params['adminToken']) ? $signup_params['adminToken'] : '';

				Cache::forget(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
			
			}elseif (Session::has('signup_params')) {

				$signup_params = Session::get('signup_params');
				
				$user->utm_source = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
				$user->utm_medium = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
				$user->utm_content   = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
				$user->utm_campaign  = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
				$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
				$adminToken = isset($signup_params['adminToken']) ? $signup_params['adminToken'] : '';
			}	

			if (isset($arr['countryName'])) {
				$countries = Country::where('country_name', $arr['countryName'])->first();
				if (isset($countries)) {
				 	$user->country_id = $countries->id;
				}
				isset($arr['cityAbbr'])  ? $user->zip   = $arr['cityAbbr'] : NULL;
				isset($arr['cityName'])  ? $user->city  = $arr['cityName'] : NULL;
				isset($arr['stateAbbr']) ? $user->state = $arr['stateAbbr'] : NULL;
			}

			// is organization?
			if (isset($adminToken) && !empty($adminToken) && Cache::has(env('ENVIRONMENT') . '_'. $adminToken)) {
				$user->is_organization = 1;
			}
			/************MAILCHIMP WELCOME EMAIL ***********/
			// $dt = array();

			// $dt['email'] = Request::get( 'email' );
			// $dt['fname'] = Request::get( 'fname' );
			// $dt['lname'] = Request::get( 'lname' );

		 	// $this->integrateMailChimp($dt);
			/************END OF MAILCHIMP WELCOME EMAIL*****/


			// If the there is a prep school in the session we set first homepage modal to false in the DB.
			if (Session::has('requestType') && Session::get('requestType') == 'prep') {
				$user->showFirstTimeHomepageModal = 0;
			}
			
			$user->birth_date = Request::get ( 'year' ) . '-' . Request::get( 'month' ) . '-' . Request::get( 'day' );
			$user->save();

			

			// if the user has been added by a super admin this is the setup for the new admin user.
			if (isset($adminToken) && !empty($adminToken) && Cache::has(env('ENVIRONMENT') . '_'. $adminToken)) {
				$admin_arr = Cache::pull(env('ENVIRONMENT') . '_'. $adminToken);
				$user_id = $user->id;

				$obp = new OrganizationBranchPermission;
				$obp->organization_branch_id = $admin_arr['org_branch_id'];
				$obp->user_id = $user_id;
				$obp->super_admin = $admin_arr['super_admin'];

				$obp->save();

				$cmtm = new CollegeMessageThreadMembers;
				$cmtm->thread_id = $admin_arr['chat_thread_id'];
				$cmtm->user_id   = $user_id;
				$cmtm->org_branch_id = $admin_arr['org_branch_id'];

				$cmtm->save();

				$is_default = 1;
				$portal_arr = array();

				if ($admin_arr['super_admin'] == 1) {
					$tmp = OrganizationPortal::where('org_branch_id', $admin_arr['org_branch_id'])
													->select('id')
													->get();	
					foreach ($tmp as $k) {
						$portal_arr[] = $k->id;
					}
				}else{
					if (is_array($admin_arr['portal_id']) && !empty($admin_arr['portal_id'])) {
						$portal_arr = $admin_arr['portal_id'];
					}else if(!empty($admin_arr['portal_id'])){
						$portal_arr[] = $admin_arr['portal_id'];
					}
				}

				foreach ($portal_arr as $key => $value) {
					$opu = new OrganizationPortalUser;
					$opu->org_portal_id = $value;
					$opu->user_id = $user_id;
					$opu->is_default = $is_default;

					$opu->save();
					$is_default = 0;
				}
			}

			// is this lead for an agency? 
			if (isset($user->utm_source)) {
				$agency = Agency::on('rds1')->where('utm_source', $user->utm_source)->first();

				if (isset($agency)) {
					$ar = new AgencyRecruitment();
        		
	        		$ar->user_id          = $user->id;
	        		$ar->agency_id        = $agency->id;
	        		$ar->agency_bucket_id = 1;
	        		$ar->active           = 1;
	        		$ar->save();

	        		$input = array();
	        		$input['hashed_id']   = Crypt::encrypt($user->id);
	        		$input['bucket_name'] = 'leads';

	        		$dt = array();
	        		$dt['agency_collection'] = new \stdClass;
					$dt['agency_collection']->agency_id = $agency->id;

	        		$ac = new AgencyController;
	        		$ac->changeStudentAgencyBucket($input, $dt);
				}
			}

			//why do we do TWO db saves to users table?! we should run this above and insert into the DB in one pass.
			$token = new ConfirmToken( array( 'token' => $confirmation ) );
			$user->confirmtoken()->save( $token );
			
			$this->sendconfirmationEmail( $user->id, $email, $confirmation );

			Auth::loginUsingId( $user->id, true );
			$this->setAjaxToken( Auth::user()->id );
			Session::put('userinfo.session_reset', 1);
			
			$startcontroller = new StartController();
       	 	$startcontroller->setupcore();
			
			if(Session::has('redirect')){
				$redirect = Session::get('redirect');
				if (isset($is_api)) {
					$ret['url'] = $redirect;
					return json_encode($ret);
				}else{
					return redirect($redirect);	
				}
				
			}else{
				if (isset($adminToken) && !empty($adminToken) && Cache::has(env('ENVIRONMENT') . '_'. $adminToken)) {
					Cache::forget(env('ENVIRONMENT') . '_'. $adminToken);
					if (isset($is_api)) {
						$ret['url'] = '/admin';
						return json_encode($ret);
					}else{
						return redirect()->intended( '/admin' );
					}
					
				}else{
					if( $fromIntlPage ){
						if (isset($is_api)) {
							$ret['url'] = $input['from_intl_students'];
							return json_encode($ret);
						}else{
							return redirect()->intended($input['from_intl_students']);
						}
						
					}elseif(isset($input['footer_step']) && $input['footer_step'] ==1){
						Session::put('last_step', "1");

						if (isset($is_api)) {
							$ret['url'] = '/scholarship-info';
							return json_encode($ret);
						}else{
							return redirect()->intended('/scholarship-info');
						}
						
					}
					else if(isset($fromUpgrade)){
						if (isset($is_api)) {
							$ret['url'] = $currentPage . "?showUpgrade=1";
							return json_encode($ret);
						}else{
							return redirect()->intended($currentPage . "?showUpgrade=1");
						}
						
					}
					else{
						if (isset($is_api)) {
							$ret['url'] = '/get_started';
							return json_encode($ret);
						}else{
							return redirect()->intended('/get_started');
						}
						
						// if (isset($user) && $user->country_id == 1) {
						// 	
						// }else{
						// 	return redirect()->intended( '/social/one-app' );
						// }
					}
				}
			}
			
			
		}
		
		if(isset($input['footer_step']) && $input['footer_step'] ==1){
			if (isset($is_api)) {
				$ret['url'] = '/scholarship-get-started';
				$ret['error_message'] = $v;
				return json_encode($ret);
			}else{
				return redirect( 'scholarship-get-started' )->withErrors( $v )->withInput( Request::except( 'password' ) );
			}
			
		}else{
			if (isset($is_api)) {
				$ret['url'] = '/signup';
				$ret['error_message'] = 'That email address has already been taken.';
				return json_encode($ret);
			}else{
				return redirect( 'signup' )->withErrors( $v )->withInput( Request::except( 'password' ) );
			}			
		}

		
	}

	/**
	 * Sign out user.
	 *
	 * @return Response
	 */
	public function signOut($is_api = NULL) {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$this->clearAjaxToken( Auth::user()->id );
		Auth::logout();
		Session::flush();
		//********************************important
		//We needed to add cache for userinfo ONLY for helper folder since it doesn't have access to laravel sessions
		// if we find a way to get access to laravel session we need to REMOVE the below line.(Also StartController)
		Cache::forget(env('ENVIRONMENT') .'_'.'userinfo');

		//clearing applied remind variable in cache - when cleared, next time user logs in, they will get applied student modal reminder
		//Cache::forget(env('ENVIRONMENT').'_'.$data['user_id'].'_applied_remind_me_later');

		//clearing textmsg remind variable in cache - when cleared, next time user logs in, they will get textmsg expire reminder
		Cache::forget(env('ENVIRONMENT').'_'.$data['user_id'].'_textmsg_remind_me_later');
		//Cache::forget(env('ENVIRONMENT') .'_'. $data['user_id'].'_user_feedback_college');

		if (isset($is_api)) {
			$ret = array();
			$ret['response'] = 'success';
			$ret['url'] = '/';
			return json_encode($ret);
		}
		return redirect( '/' );
	}

    public function getAdminSignup() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['currentPage'] = 'admin-signup';

        $country = new Country();

        $data['country_list'] = $country->getCountriesWithNameIdAsKeys();

        $data['country_code_list'] = $country->getAllCountriesAndIdsWithCountryCode();

        if (isset($data['user_id'])) {
            $user = User::select('is_agency', 'is_organization')
                             ->where('id', $data['user_id'])
                             ->first();

            // Do not allow admin accounts to sign up for an admin account. If so, redirect to admin dashboard.
            if (isset($user->is_organization) && $user->is_organization == 1) {
                return redirect('/admin');
            }
                 
            // Check if this user is already an agent.
            if (isset($user->is_agency) && $user->is_agency == 1) {
                return redirect('/');
            }

            // Check if this user has already completed the full application.
            $self_signup = CollegeSelfSignupApplications::select('id')
                              ->where('user_id', $data['user_id'])
                              ->first();
                              
            if (isset($self_signup)) {
                $data['admin_application_completed'] = 1;
            }
        }

        return View('admin.signup.index', $data);
    }

    /**
     * Store a newly created user in db and send email with token.
     *
     * @return Response
     */
    public function postAdminSignup() {

        $input = Request::all();
        // echo "<pre>";
        // print_r($input);
        // echo "</pre>";
        // exit();
        $email = Request::get('email');
        $v = Validator::make( $input, User::$rules );
        $name = Request::get( 'fname' ) . ' ' . Request::get( 'lname' );
        $email = Request::get( 'email' );
        $phone = '';
        $phone = Request::get('country_code');
        $phone = $phone . ' '. Request::get('phone');
        $confirmation = str_random( 20 );
        $adminToken = null;


        //////// VALIDATION ///////////////////

        //check for profanity in first and last names
        $fname = Request::get( 'fname' );
        $lname= Request::get( 'lname' );
        $name = $fname . ' ' . $lname;
        $email = Request::get('email'); 
        
        //check for profanity

        $fnameList = explode(' ', $fname);
        $lnameList = explode(' ', $lname);
        $testArray = array_merge($fnameList, $lnameList);   

        $fromIntlPage = isset($input['from_intl_students']) && !empty($input['from_intl_students']);
        
        $isProfane = ProfanityList::on('bk')
                    ->where(function($query) use ($testArray){
                        foreach($testArray as $nameTok)
                            $query->orWhere('name', $nameTok);
                        
                    })->first();
       

        if(isset($isProfane) && $isProfane != ''){
            $msg = "No profanity please!";
            return redirect( 'admin.signup.step_1' )->withErrors( $msg )->withInput( Request::except( 'password' ) );
        }


        //use Validator to check against $rules in User.php
        if ( $v->passes() ) {
            $user = new User;       //if passes, make a new user
            $user->fname = Request::get( 'fname' );
            $user->lname = Request::get( 'lname' );
            $user->email = Request::get( 'email' );
            $user->phone = $phone;
            $user->password = Hash::make( Request::get( 'password' ) );

            $arr = $this->iplookup();

            if(Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])){
                $signup_params = Cache::get(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
                
                $user->utm_source = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
                $user->utm_medium = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
                $user->utm_content= isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
                $user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
                $user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
                $adminToken = isset($signup_params['adminToken']) ? $signup_params['adminToken'] : '';

                Cache::forget(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
            
            }elseif (Session::has('signup_params')) {

                $signup_params = Session::get('signup_params');
                
                $user->utm_source = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
                $user->utm_medium = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
                $user->utm_content   = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
                $user->utm_campaign  = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
                $user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
                $adminToken = isset($signup_params['adminToken']) ? $signup_params['adminToken'] : '';
            }   

            if (isset($arr['countryName'])) {
                $countries = Country::where('country_name', $arr['countryName'])->first();
                if (isset($countries)) {
                    $user->country_id = $countries->id;
                }
            }

            
            // is organization?
            if (isset($adminToken) && !empty($adminToken) && Cache::has(env('ENVIRONMENT') . '_'. $adminToken)) {
                $user->is_organization = 1;
            }
            /************MAILCHIMP WELCOME EMAIL ***********/
            // $dt = array();

            // $dt['email'] = Request::get( 'email' );
            // $dt['fname'] = Request::get( 'fname' );
            // $dt['lname'] = Request::get( 'lname' );

            // $this->integrateMailChimp($dt);
            /************END OF MAILCHIMP WELCOME EMAIL*****/


            // If the there is a prep school in the session we set first homepage modal to false in the DB.
            if (Session::has('requestType') && Session::get('requestType') == 'prep') {
                $user->showFirstTimeHomepageModal = 0;
            }
            
            $user->birth_date = Request::get ( 'year' ) . '-' . Request::get( 'month' ) . '-' . Request::get( 'day' );
            $user->save();

            

            // if the user has been added by a super admin this is the setup for the new admin user.
            if (isset($adminToken) && !empty($adminToken) && Cache::has(env('ENVIRONMENT') . '_'. $adminToken)) {
                $admin_arr = Cache::pull(env('ENVIRONMENT') . '_'. $adminToken);
                $user_id = $user->id;

                $obp = new OrganizationBranchPermission;
                $obp->organization_branch_id = $admin_arr['org_branch_id'];
                $obp->user_id = $user_id;
                $obp->super_admin = $admin_arr['super_admin'];

                $obp->save();

                $cmtm = new CollegeMessageThreadMembers;
                $cmtm->thread_id = $admin_arr['chat_thread_id'];
                $cmtm->user_id   = $user_id;
                $cmtm->org_branch_id = $admin_arr['org_branch_id'];

                $cmtm->save();

                $is_default = 1;
                $portal_arr = array();

                if ($admin_arr['super_admin'] == 1) {
                    $tmp = OrganizationPortal::where('org_branch_id', $admin_arr['org_branch_id'])
                                                    ->select('id')
                                                    ->get();    
                    foreach ($tmp as $k) {
                        $portal_arr[] = $k->id;
                    }
                }else{
                    if (is_array($admin_arr['portal_id']) && !empty($admin_arr['portal_id'])) {
                        $portal_arr = $admin_arr['portal_id'];
                    }else if(!empty($admin_arr['portal_id'])){
                        $portal_arr[] = $admin_arr['portal_id'];
                    }
                }

                foreach ($portal_arr as $key => $value) {
                    $opu = new OrganizationPortalUser;
                    $opu->org_portal_id = $value;
                    $opu->user_id = $user_id;
                    $opu->is_default = $is_default;

                    $opu->save();
                    $is_default = 0;
                }

            }

            //why do we do TWO db saves to users table?! we should run this above and insert into the DB in one pass.
            $token = new ConfirmToken( array( 'token' => $confirmation ) );
            $user->confirmtoken()->save( $token );
            
            $this->sendconfirmationEmail( $user->id, $email, $confirmation );

            Auth::loginUsingId( $user->id, true );
            $this->setAjaxToken( Auth::user()->id );
            Session::put('userinfo.session_reset', 1);
            
            $startcontroller = new StartController();
            $startcontroller->setupcore();

            $response = [
                'fname' => $user->fname, 
                'lname' => $user->lname, 
                'email' => $user->email, 
                'status' => 'success'
            ];
            
            return $response;
        }
        return view( 'admin.signup.step_1' )->withErrors( $v )->withInput( Request::except( 'password' ) );
    }

	public function getAgencySignup() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'agency-signup';

		$country = new Country();

		$data['country_list'] = $country->getAllCountriesAndIds();

		$data['country_code_list'] = $country->getAllCountriesAndIdsWithCountryCode();

		if (isset($data['user_id'])) {
			$user = User::select('is_agency', 'is_organization')
							 ->where('id', $data['user_id'])
							 ->first();

			// Do not allow admin accounts to sign up for an agency account.
			if (isset($user->is_organization) && $user->is_organization == 1) {
				return redirect('/');
			}
				 
			// Check if this user is already an agent. If so, redirect to agency dashboard.
			if (isset($user->is_agency) && $user->is_agency == 1) {
				return redirect('/agency');
			}

			// Check if this user has already completed the full application.
			$agency_profile = AgencyProfileInfo::select('id')
							  ->where('user_id', $data['user_id'])
							  ->first();
							  
			if (isset($agency_profile)) {
				$data['agency_application_completed'] = 1;
			}
		}

		return View('agency.signup.index', $data);
	}

    /**
     * Post admin (college) self-signup application
     *
     */
    public function postAdminApplication() {
        $viewDataController = new ViewDataController();

        $data = $viewDataController->buildData();

        $input = Request::all();

        $user_id = $data['user_id'];

        try {

            if ( isset($input['admin-profile-photo']) ) { 
                $response = $this->generalUploadDoc($input, 'admin-profile-photo', 'asset.plexuss.com/users/images');

                $profile_img_loc = $response['saved_as'];
            }

        } catch (\Exception $e) {

        }

        $user = User::find($user_id);

        if (isset($input['country_code']) && isset($input['phone_number'])) {
            $phone = $input['country_code'] . ' ' . $input['phone_number'];
            $user->phone = $phone;
        }

        $user->fname = $input['fname'];
        $user->lname = $input['lname'];

        if (isset($profile_img_loc))
            $user->profile_img_loc = $profile_img_loc;

        $user->save();

        $response =  CollegeSelfSignupApplications::insertOrUpdate($user_id, $input);

        $mac = new MandrillAutomationController;
        $mac->collegesSelfSignUpRequest($user_id);

        return $response;
    }

	/**
	 * Store a newly created user in db and send email with token.
	 *
	 * @return Response
	 */
	public function postAgencySignup() {

		$input = Request::all();
		// echo "<pre>";
		// print_r($input);
		// echo "</pre>";
		// exit();
		$email = Request::get('email');
		$v = Validator::make( $input, User::$rules );
		$name = Request::get( 'fname' ) . ' ' . Request::get( 'lname' );
		$email = Request::get( 'email' );
		$phone = '';
		$phone = Request::get('country_code');
		$phone = $phone . ' '. Request::get('phone');
		$confirmation = str_random( 20 );
		$adminToken = null;


		//////// VALIDATION ///////////////////

		//check for profanity in first and last names
		$fname = Request::get( 'fname' );
		$lname= Request::get( 'lname' );
		$name = $fname . ' ' . $lname;
		$email = Request::get('email'); 
		
		//check for profanity

		$fnameList = explode(' ', $fname);
		$lnameList = explode(' ', $lname);
		$testArray = array_merge($fnameList, $lnameList);	

		$fromIntlPage = isset($input['from_intl_students']) && !empty($input['from_intl_students']);
		
		$isProfane = ProfanityList::on('bk')
					->where(function($query) use ($testArray){
						foreach($testArray as $nameTok)
							$query->orWhere('name', $nameTok);
						
					})->first();
	   

		if(isset($isProfane) && $isProfane != ''){
			$msg = "No profanity please!";
			return redirect( 'signup' )->withErrors( $msg )->withInput( Request::except( 'password' ) );
		}


		//use Validator to check against $rules in User.php
		if ( $v->passes() ) {
			$user = new User;		//if passes, make a new user
			$user->fname = Request::get( 'fname' );
			$user->lname = Request::get( 'lname' );
			$user->email = Request::get( 'email' );
			$user->phone = $phone;
			$user->password = Hash::make( Request::get( 'password' ) );

			$arr = $this->iplookup();

			if(Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])){
				$signup_params = Cache::get(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
				
				$user->utm_source = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
				$user->utm_medium = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
				$user->utm_content= isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
				$user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
				$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
				$adminToken = isset($signup_params['adminToken']) ? $signup_params['adminToken'] : '';

				Cache::forget(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
			
			}elseif (Session::has('signup_params')) {

				$signup_params = Session::get('signup_params');
				
				$user->utm_source = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
				$user->utm_medium = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
				$user->utm_content   = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
				$user->utm_campaign  = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
				$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
				$adminToken = isset($signup_params['adminToken']) ? $signup_params['adminToken'] : '';
			}	

			if (isset($arr['countryName'])) {
				$countries = Country::where('country_name', $arr['countryName'])->first();
				if (isset($countries)) {
				 	$user->country_id = $countries->id;
				}
			}

			
			// is organization?
			if (isset($adminToken) && !empty($adminToken) && Cache::has(env('ENVIRONMENT') . '_'. $adminToken)) {
				$user->is_organization = 1;
			}
			/************MAILCHIMP WELCOME EMAIL ***********/
			// $dt = array();

			// $dt['email'] = Request::get( 'email' );
			// $dt['fname'] = Request::get( 'fname' );
			// $dt['lname'] = Request::get( 'lname' );

		 	// $this->integrateMailChimp($dt);
			/************END OF MAILCHIMP WELCOME EMAIL*****/


			// If the there is a prep school in the session we set first homepage modal to false in the DB.
			if (Session::has('requestType') && Session::get('requestType') == 'prep') {
				$user->showFirstTimeHomepageModal = 0;
			}
			
			$user->birth_date = Request::get ( 'year' ) . '-' . Request::get( 'month' ) . '-' . Request::get( 'day' );
			$user->save();

			

			// if the user has been added by a super admin this is the setup for the new admin user.
			if (isset($adminToken) && !empty($adminToken) && Cache::has(env('ENVIRONMENT') . '_'. $adminToken)) {
				$admin_arr = Cache::pull(env('ENVIRONMENT') . '_'. $adminToken);
				$user_id = $user->id;

				$obp = new OrganizationBranchPermission;
				$obp->organization_branch_id = $admin_arr['org_branch_id'];
				$obp->user_id = $user_id;
				$obp->super_admin = $admin_arr['super_admin'];

				$obp->save();

				$cmtm = new CollegeMessageThreadMembers;
				$cmtm->thread_id = $admin_arr['chat_thread_id'];
				$cmtm->user_id   = $user_id;
				$cmtm->org_branch_id = $admin_arr['org_branch_id'];

				$cmtm->save();

				$is_default = 1;
				$portal_arr = array();

				if ($admin_arr['super_admin'] == 1) {
					$tmp = OrganizationPortal::where('org_branch_id', $admin_arr['org_branch_id'])
													->select('id')
													->get();	
					foreach ($tmp as $k) {
						$portal_arr[] = $k->id;
					}
				}else{
					if (is_array($admin_arr['portal_id']) && !empty($admin_arr['portal_id'])) {
						$portal_arr = $admin_arr['portal_id'];
					}else if(!empty($admin_arr['portal_id'])){
						$portal_arr[] = $admin_arr['portal_id'];
					}
				}

				foreach ($portal_arr as $key => $value) {
					$opu = new OrganizationPortalUser;
					$opu->org_portal_id = $value;
					$opu->user_id = $user_id;
					$opu->is_default = $is_default;

					$opu->save();
					$is_default = 0;
				}

			}

			//why do we do TWO db saves to users table?! we should run this above and insert into the DB in one pass.
			$token = new ConfirmToken( array( 'token' => $confirmation ) );
			$user->confirmtoken()->save( $token );
			
			$this->sendconfirmationEmail( $user->id, $email, $confirmation );

			Auth::loginUsingId( $user->id, true );
			$this->setAjaxToken( Auth::user()->id );
			Session::put('userinfo.session_reset', 1);
			
			$startcontroller = new StartController();
       	 	$startcontroller->setupcore();

			
			return 'success';
			
			
		}
		return view( 'agency.signup.step_1' )->withErrors( $v )->withInput( Request::except( 'password' ) );
	}

	public function postAgencyApplication() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$email_data = [];

		$user_id = $data['user_id'];

		$agency_profile = null;

		// Update user table's phone, country_id, and city.
		$user = User::find($user_id);

		if (isset($input['country_code']) && isset($input['phone_number'])) {
			$phone = $input['country_code'] . ' ' . $input['phone_number'];
			$user->phone = $phone;
		}

		if (isset($input['country_id'])) {
			$user->country_id = $input['country_id'];
		}

		if (isset($input['city'])) {
			$user->city = $input['city'];
		}

		$user->save();
		/**/

        try {
    		if (isset($input['agency-profile-photo'])) {
                $response = $this->generalUploadDoc($input, 'agency-profile-photo', 'asset.plexuss.com/agency/profilepics', NULL);
    			$input['profile_photo_url'] = $response['url'];
    		}
        } catch (\Exception $e) {

        }

		$response = AgencyProfileInfo::insertOrUpdate($user_id, $input);

		// If services exist, insert into services table.
		if ($response == 'success') {
			$agency_profile = AgencyProfileInfo::on('rds1')
											   ->select('id')
											   ->where('user_id', $user_id)
											   ->first();
			
			if (isset($input['services']) && $input['services'] != '') {
				$services = json_decode($input['services']);

				$response = AgencyProfileInfoServices::insertServices($agency_profile->id, $services);
			}

			if (isset($input['days_of_operation']) && $input['days_of_operation'] != '') {
					$days_of_operation = json_decode($input['days_of_operation']);
					AgencyProfileInfoHours::insertOrUpdate('signup', $agency_profile->id, $days_of_operation);

			}
		}

		$mac = new MandrillAutomationController;
		$mac->agencySignUpRequest($user_id);

		return $response;
	}


	/**
	 * Handle email confirmation.
	 *
	 * @return Response
	 */
	public function confirmEmail( $confirmation = null ) {
		$token = ConfirmToken::where( 'token', '=', $confirmation )->first();

		if ( !$token ) {
			return "Sorry that token has expired. Please sign in with your email or password and we will send you a new confirmation";
		}

		$userid = $token->user_id;
		$user = User::find( $userid );
		$user->email_confirmed = 1;
		$user->save();
		$token->delete();

		//$this->thankyouEmail( $user->fname, $user->email );

		// $mac = new MandrillAutomationController;
		// $mac->welcomeEmailForUsers($userid);

		$reply_email   = 'support@plexuss.com';

		if (isset($user->country_id) && $user->country_id == 1) {
			$template_name = 'welcome_mail';
		}else{
			$template_name = 'welcome_mail_int';
		}

		// $ten_mins = Carbon::now()->addMinutes(10);

		// EmailQueueProcess::dispatch($reply_email, $template_name, $params, $emailaddress, $user_id)
		// 				 ->delay($ten_mins);


		$upeel = UsersPortalEmailEffortLog::on('bk')
									  ->where('user_id', $userid)
									  ->where('template_name', $template_name)
									  ->select('id')
									  ->first();

		if (!isset($upeel)) {
									  
			$template_to_send = EmailTemplateSenderProvider::on('bk')
														   ->where('template_name', $template_name)
														   ->first();

			$ab_test_id = NULL;
			if (isset($template_to_send)) {
				$ab_test_id = $template_to_send->ab_test_id;
			}

			$params = array();
			$params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
			$eqp = new EmailQueueProcess($reply_email, $template_name, $params, $user->email, $userid, NULL, $ab_test_id, 2);
			$eqp->handle();
		}	

		Auth::loginUsingId( $userid );

		$this->setAjaxToken( Auth::user()->id );
		Session::put('userinfo.session_reset', 1);
		$data = $this->setSignedInUserData();

		return redirect( 'get_started' );
	}

	/**
	 * Login user with facebook
	 *
	 * @return void
	 */

	public function loginWithFacebook() {

		// get data from input
		$code = Request::get( 'code' );

		// get fb service
		$fb = \OAuth::consumer( 'Facebook' );

		$input = Request::all();

		$arr = $this->iplookup();
		if (isset($input)) {
			if (!Session::has('signup_params')) {
				Session::put('signup_params', $input);
			}
			if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
				Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
			}
		}
		
		// check if code is valid
		// if code is provided get user data and sign in
		if ( !empty( $code ) ) {

			// This was a callback request from facebook, get the token
			$token = $fb->requestAccessToken( $code );

			// Send a request with it
			$result = json_decode( $fb->request( '/me?fields=id,name,email,first_name,last_name' ), true );

			$id = $result['id'];
			if ( isset($result['email'] )) {
				$email = $result['email'];
			} else {
				$email = 'none';
			}
			$first_name = $result['first_name'];
			$last_name = $result['last_name'];
			$rnd = str_random( 20 );
			$password =  $rnd;

			//Check if id is in the database.
			$user = User::on('rds1')->where( 'fb_id', '=', $result['id'] );

			if ($email !== 'none') {
				$user = $user->orWhere('email', $email);
			}

			$user = $user->first();

			if ( $user ) {
				//Auth::login( $user );
				Auth::loginUsingId( $user->id, true );
				$this->setAjaxToken( Auth::user()->id );
				Session::put('userinfo.session_reset', 1);

				$data = $this->setSignedInUserData();

				if(Session::has('redirect')){
					$redirect = Session::get('redirect');

					return redirect($redirect);
				}

                if( Session::has('redirect_from_signin') ){
                	$redirect = Session::get('redirect_from_signin');
                	if (substr($redirect, 0, 1)  == '/') {
                		$path = $redirect;
                	}else{
                		$path = '/'. $redirect;
                	}
                    Session::forget('redirect_from_signin');

                    // Don't redirect here!!!! this was a bug.
                    if ($path !== 'ajax/profile/getnotifications' && $path !== '/ajax/profile/getnotifications') {
                    	return redirect($path);
                    }
                }

				if (isset($data['is_organization']) && $data['is_organization'] == 1) {
					return redirect()->intended( '/admin' );
				}

				if (isset($data['agency_collection'])) {
					return redirect()->intended( '/agency' );
				}

				if( (isset($user->profile_percent) && $user->profile_percent < 30) ||
					(!isset($user->email) || empty($user->email) || $user->email == 'none') ){
					// return redirect()->intended('/social/one-app');
					return redirect()->intended('/get_started');
				}

				return redirect( '/home' );
			} else {
				$user = new User;
				$user->fb_id = $result['id'];
				$user->fname = $result['first_name'];
				$user->lname = $result['last_name'];
				$user->email = $email;
				$user->password = Hash::make( $password );
				$user->email_confirmed = 1;
                
                $ip_lookup = $this->iplookup();

				if (Session::has('signup_params')) {
					$signup_params = Session::get('signup_params');
					
					$user->utm_source     = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
					$user->utm_medium     = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
					$user->utm_content    = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
					$user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
					$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
				
				}elseif(Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])){
					$signup_params = Cache::get(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
					
					$user->utm_source     = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
					$user->utm_medium     = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
					$user->utm_content    = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
					$user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
					$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';

					Cache::forget(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
				}		

				$arr = $this->iplookup();

				if (isset($arr['countryName'])) {
					$countries = Country::where('country_name', $arr['countryName'])->first();
					if (isset($countries)) {
					 	$user->country_id = $countries->id;
					}
				}

				$user->save();
				$confirmation = str_random( 20 );
				$token = new ConfirmToken( array( 'token' => $confirmation ) );
				$user->confirmtoken()->save( $token );

				
				/************MAILCHIMP WELCOME EMAIL ***********/

				// if (isset($result['email'])) {

				// 	$dt = array();
				// 	$dt['email'] = $result['email'];
				// 	$dt['fname'] = $result['first_name'];
				// 	$dt['lname'] = $result['last_name'];

				// 	$this->integrateMailChimp($dt);
				// }
				/************END OF MAILCHIMP WELCOME EMAIL*****/

				Auth::loginUsingId( $user->id, true );
				$this->setAjaxToken( Auth::user()->id );
				Session::put('userinfo.session_reset', 1);
				
				$data = $this->setSignedInUserData();

				if(Session::has('redirect') || Cache::has('AuthController_redirect_'. $arr['ip'])){
					
					if (Cache::has('AuthController_redirect_'. $arr['ip'])) {
						$redirect = Cache::get('AuthController_redirect_'. $arr['ip']);
						Cache::forget('AuthController_redirect_'. $arr['ip']);
					}else{
						$redirect = Session::get('redirect');
					}

					return redirect($redirect);
				}

                if( Session::has('redirect_from_signin') ){
                    $path = '/'.Session::get('redirect_from_signin');
                    Session::forget('redirect_from_signin');
                    return redirect($path);
                }

				if (isset($data['is_organization']) && $data['is_organization'] == 1) {
					return redirect()->intended( '/admin' );
				}

				if (isset($data['agency_collection'])) {
					return redirect()->intended( '/agency' );
				}

                // if (isset($ip_lookup) && $ip_lookup['countryName'] == 'United States') {
                //     return redirect()->intended( '/get_started' );
                // } else {
                //     // return redirect()->intended( '/social/one-app' );
                //     return redirect()->intended('/get_started');
                // }
                
                return redirect()->intended('/get_started');
				// $ucq = UsersCustomQuestion::on('rds1')->where('user_id', Auth::user()->id)->first();

				// if (isset($ucq)) {
				// 	$step = $ucq->application_state;

				// 	if (isset($step) && !empty($step) && $step == 'submit') {
				// 		return redirect()->intended( '/portal/collegesrecruityou' );
				// 	}else if (isset($step) && !empty($step)) {
				// 		return redirect()->intended( '/social/one-app/'.$step);
				// 	}else{
				// 		return redirect()->intended( '/social/one-app/');
				// 	}
				// }

				// if( isset($user->profile_percent) && $user->profile_percent < 30 ||
				// 	(!isset($user->email) || empty($user->email) || $user->email == 'none') ){
				// 	// return redirect()->intended('/social/one-app');
				// 	return redirect()->intended('/get_started');
				// }
				// if (isset($arr['countryAbbr']) && $arr['countryAbbr'] != 'US') {
				// 	return redirect( '/international-students' );
				// }
				// return redirect( '/home' );
			}
		}
		// if not ask for permission first
		else {
			// get fb authorization
			$url = $fb->getAuthorizationUri();

			// return to facebook login url
			return redirect( (string)$url );
		}
	}

	public function loginWithFBBackToCCP(){

		// get data from input
		$code = Request::get( 'code' );

		// get fb service
		$fb = \OAuth::consumer( 'Facebook' );

		// check if code is valid
		// if code is provided get user data and sign in
		if ( !empty( $code ) ) {

			// This was a callback request from facebook, get the token
			$token = $fb->requestAccessToken( $code );

			// Send a request with it
			$result = json_decode( $fb->request( '/me?fields=id,name,email,first_name,last_name' ), true );

			$id = $result['id'];
			if ( $result['email'] ) {
				$email = $result['email'];
			} else {
				$email = 'none';
			}
			$first_name = $result['first_name'];
			$last_name = $result['last_name'];
			$rnd = str_random( 20 );
			$password =  $rnd;

			//Check if id is in the database.
			$user = User::where( 'fb_id', '=', $result['id'] )->first();

			if ( $user ) {
				//Auth::login( $user );
				Auth::loginUsingId( $user->id, true );
				$this->setAjaxToken( Auth::user()->id );
				Session::put('userinfo.session_reset', 1);

				$data = $this->setSignedInUserData();

				if (isset($data['is_organization']) && $data['is_organization'] == 1) {
					return redirect()->intended( '/admin' );
				}

				if (isset($data['agency_collection'])) {
					return redirect()->intended( '/agency' );
				}
				$ucq = UsersCustomQuestion::on('rds1')->where('user_id', Auth::user()->id)->first();

				if (isset($ucq)) {
					$step = $ucq->application_state;

					if (isset($step) && !empty($step) && $step == 'submit') {
						return redirect( '/portal/collegesrecruityou' );
					}else if (isset($step) && !empty($step)) {
						return redirect( '/social/one-app/'.$step);
					}else{
						return redirect( '/social/one-app/');
					}
				}

				return redirect( '/social/one-app/');
				
			} else {
				$user = new User;
				$user->fb_id = $result['id'];
				$user->fname = $result['first_name'];
				$user->lname = $result['last_name'];
				$user->email = $email;
				$user->password = Hash::make( $password );
				$user->email_confirmed = 1;
				$user->save();
				$confirmation = str_random( 20 );
				$token = new ConfirmToken( array( 'token' => $confirmation ) );
				$user->confirmtoken()->save( $token );

				/************MAILCHIMP WELCOME EMAIL ***********/
				// if (isset($result['email'])) {
					
				// 	$dt = array();
				// 	$dt['email'] = $result['email'];
				// 	$dt['fname'] = $result['first_name'];
				// 	$dt['lname'] = $result['last_name'];

				// 	$this->integrateMailChimp($dt);
				// }
				/************END OF MAILCHIMP WELCOME EMAIL*****/

				Auth::loginUsingId( $user->id, true );
				$this->setAjaxToken( Auth::user()->id );
				Session::put('userinfo.session_reset', 1);

				$data = $this->setSignedInUserData();

				if (isset($data['is_organization']) && $data['is_organization'] == 1) {
					return redirect()->intended( '/admin' );
				}

				if (isset($data['agency_collection'])) {
					return redirect()->intended( '/agency' );
				}

				return redirect( '/carepackage#cart' );
			}
		}
		// if not ask for permission first
		else {
			// get fb authorization
			$url = $fb->getAuthorizationUri();

			// return to facebook login url
			return redirect( (string)$url );
		}
	}

	/**
	 * Login user with google
	 *
	 * @return void
	 */

	public function loginWithGoogle() {

		// get data from input
		$code = Request::get( 'code' );

		// get google service
		$google = \OAuth::consumer( 'Google' );

		$input = Request::all();

		$arr = $this->iplookup();
		if (isset($input)) {
			if (!Session::has('signup_params')) {
				Session::put('signup_params', $input);
			}
			if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
				Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
			}
		}
		
		// check if code is valid
		// if code is provided get user data and sign in
		if ( !empty( $code ) ) {

			// This was a callback request from google, get the token
			$token = $google->requestAccessToken( $code );

			// Send a request with it
			$result = json_decode( $google->request( 'https://www.googleapis.com/oauth2/v1/userinfo' ), true );

			$id = $result['id'];
			if ( isset($result['email'] )) {
				$email = $result['email'];
			} else {
				$email = 'none';
			}
			$first_name = $result['given_name'];
			$last_name = $result['family_name'];
			$rnd = str_random( 20 );
			$password =  $rnd;

			//Check if id is in the database.

			$query = DB::connection('rds1')->table('users as u')
										   ->leftjoin('users_addtl_info as uai', 'u.id', '=', 'uai.user_id')
										   ->select('u.id as user_id', 'u.profile_percent', 'u.email', 'uai.id as uai_id')
										   
										   ->orWhere('u.email', $email)
										   ->orWhere('uai.google_id', $result['id'])

										   ->first();

			if ( $query ) {

				$user = User::find($query->user_id);
				if(isset($result['gender'])) {
					$user->gender = $result['gender'] == 'male' ? 'm' : $result['gender'] == 'female' ? 'f' : null;
				}
				if (!isset($user->profile_img_loc) || empty($user->profile_img_loc) || $user->profile_img_loc == 'default.png') {
					if(isset($result['picture'])) {
						$utility = new UtilityController;
						$img = json_decode($utility->uploadUsersPictureWithAUrl($result['picture']));
						if ($img->status == "success") {
							$user->profile_img_loc = $img->url;
						}		
					}
				}
				$user->save();

				$attr = array('user_id' => $query->user_id);
				$val  = array('user_id' => $query->user_id, 'google_id' => $result['id']);
				UsersAddtlInfo::updateOrCreate($attr, $val);

				//Auth::login( $user );
				Auth::loginUsingId( $user->id, true );
				$this->setAjaxToken( Auth::user()->id );
				Session::put('userinfo.session_reset', 1);

				$data = $this->setSignedInUserData();

				if(Session::has('redirect')){
					$redirect = Session::get('redirect');

					return redirect($redirect);
				}

                if( Session::has('redirect_from_signin') ){
                	$redirect = Session::get('redirect_from_signin');
                	if (substr($redirect, 0, 1)  == '/') {
                		$path = $redirect;
                	}else{
                		$path = '/'. $redirect;
                	}
                    Session::forget('redirect_from_signin');

                    // Don't redirect here!!!! this was a bug.
                    if ($path !== 'ajax/profile/getnotifications' && $path !== '/ajax/profile/getnotifications') {
                    	return redirect($path);
                    }
                }

				if (isset($data['is_organization']) && $data['is_organization'] == 1) {
					return redirect()->intended( '/admin' );
				}

				if (isset($data['agency_collection'])) {
					return redirect()->intended( '/agency' );
				}

				if( (isset($user->profile_percent) && $user->profile_percent < 30) ||
					(!isset($user->email) || empty($user->email) || $user->email == 'none') ){
					// return redirect()->intended('/social/one-app');
					return redirect()->intended('/get_started');
				}

				return redirect( '/home' );
			} else {
				$user = new User;
				$user->fname = $result['given_name'];
				$user->lname = $result['family_name'];
				$user->email = $email;
				$user->password = Hash::make( $password );
				$user->email_confirmed = 1;

				if(isset($result['gender'])) {
					$user->gender = $result['gender'] == 'male' ? 'm' : $result['gender'] == 'female' ? 'f' : null;
				}
				if(isset($result['picture'])) {
					$utility = new UtilityController;
					$img = json_decode($utility->uploadUsersPictureWithAUrl($result['picture']));
					if ($img->status == "success") {
						$user->profile_img_loc = $img->url;
					}		
				}
                
                $ip_lookup = $this->iplookup();

				if (Session::has('signup_params')) {
					$signup_params = Session::get('signup_params');
					
					$user->utm_source     = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
					$user->utm_medium     = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
					$user->utm_content    = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
					$user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
					$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
				
				}elseif(Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])){
					$signup_params = Cache::get(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
					
					$user->utm_source     = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
					$user->utm_medium     = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
					$user->utm_content    = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
					$user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
					$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';

					Cache::forget(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
				}		

				$arr = $this->iplookup();

				if (isset($arr['countryName'])) {
					$countries = Country::where('country_name', $arr['countryName'])->first();
					if (isset($countries)) {
					 	$user->country_id = $countries->id;
					}
				}

				$user->save();
				$confirmation = str_random( 20 );
				$token = new ConfirmToken( array( 'token' => $confirmation ) );
				$user->confirmtoken()->save( $token );

				$uai = new UsersAddtlInfo;
				$uai->user_id   = $user->id;
				$uai->google_id = $result['id'];

				$uai->save();
				
				/************MAILCHIMP WELCOME EMAIL ***********/

				// if (isset($result['email'])) {

				// 	$dt = array();
				// 	$dt['email'] = $result['email'];
				// 	$dt['fname'] = $result['first_name'];
				// 	$dt['lname'] = $result['last_name'];

				// 	$this->integrateMailChimp($dt);
				// }
				/************END OF MAILCHIMP WELCOME EMAIL*****/

				Auth::loginUsingId( $user->id, true );
				$this->setAjaxToken( Auth::user()->id );
				Session::put('userinfo.session_reset', 1);
				
				$data = $this->setSignedInUserData();

				if(Session::has('redirect') || Cache::has('AuthController_redirect_'. $arr['ip'])){
					
					if (Cache::has('AuthController_redirect_'. $arr['ip'])) {
						$redirect = Cache::get('AuthController_redirect_'. $arr['ip']);
						Cache::forget('AuthController_redirect_'. $arr['ip']);
					}else{
						$redirect = Session::get('redirect');
					}

					return redirect($redirect);
				}

                if( Session::has('redirect_from_signin') ){
                    $path = '/'.Session::get('redirect_from_signin');
                    Session::forget('redirect_from_signin');
                    return redirect($path);
                }

				if (isset($data['is_organization']) && $data['is_organization'] == 1) {
					return redirect()->intended( '/admin' );
				}

				if (isset($data['agency_collection'])) {
					return redirect()->intended( '/agency' );
				}
                
                return redirect()->intended('/get_started');
			}						   
		}
		// if not ask for permission first
		else {
			// get google authorization
			$url = $google->getAuthorizationUri();

			// return to facebook login url
			return redirect( (string)$url );
		}
	}

	/**
	 * Login user with LinkedIn
	 *
	 * @return void
	 */

	// public function loginWithLinkedIn() {

	// 	// get data from input
	// 	$code = Request::get( 'code' );

	// 	// get linkedin service
	// 	$linkedin = \OAuth::consumer( 'Linkedin' );

	// 	$input = Request::all();

	// 	$arr = $this->iplookup();
	// 	if (isset($input)) {
	// 		if (!Session::has('signup_params')) {
	// 			Session::put('signup_params', $input);
	// 		}
	// 		if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
	// 			Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
	// 		}
	// 	}
		
	// 	// check if code is valid
	// 	// if code is provided get user data and sign in
	// 	if ( !empty( $code ) ) {

	// 		// This was a callback request from linkedin, get the token
	// 		$token = $linkedin->requestAccessToken( $code );

	// 		// Send a request with it
	// 		$result = json_decode( $linkedin->request( '/people/~?format=json' ), true );

	// 		$id = $result['id'];
	// 		if ( isset($result['email'] )) {
	// 			$email = $result['email'];
	// 		} else {
	// 			$email = 'none';
	// 		}
	// 		$first_name = $result['firstName'];
	// 		$last_name = $result['lastName'];
	// 		$rnd = str_random( 20 );
	// 		$password =  $rnd;

	// 		//Check if id is in the database.
	// 		$query = DB::connection('rds1')->table('users as u')
	// 									   ->leftjoin('users_addtl_info as uai', 'u.id', '=', 'uai.user_id')
	// 									   ->select('u.id as user_id', 'u.profile_percent', 'u.email', 'uai.id as uai_id')
										   
	// 									   ->orWhere('u.email', $email)
	// 									   ->orWhere('uai.linedin_id', $result['id'])

	// 									   ->first();

	// 		if ( $query ) {
	// 			$user = User::find($query->user_id);
	// 			if(isset($result['gender'])) {
	// 				$user->gender = $result['gender'] == 'male' ? 'm' : $result['gender'] == 'female' ? 'f' : null;
	// 			}
	// 			if(isset($result['picture'])) {
	// 				$utility = new UtilityController;
	// 				$img = json_decode($utility->uploadUsersPictureWithAUrl($result['picture']));
	// 				if ($img->status == "success") {
	// 					$user->profile_img_loc = $img->url;
	// 				}		
	// 			}
	// 			$user->save();

	// 			$attr = array('user_id' => $query->user_id);
	// 			$val  = array('user_id' => $query->user_id, 'linkedin_id' => $result['id']);
	// 			UsersAddtlInfo::updateOrCreate($attr, $val);

	// 			//Auth::login( $user );
	// 			Auth::loginUsingId( $user->id, true );
	// 			$this->setAjaxToken( Auth::user()->id );
	// 			Session::put('userinfo.session_reset', 1);

	// 			$data = $this->setSignedInUserData();

	// 			if(Session::has('redirect')){
	// 				$redirect = Session::get('redirect');

	// 				return redirect($redirect);
	// 			}

	// 			if( Session::has('redirect_from_signin') ){
	// 				$redirect = Session::get('redirect_from_signin');
	// 				if (substr($redirect, 0, 1)  == '/') {
	// 					$path = $redirect;
	// 				}else{
	// 					$path = '/'. $redirect;
	// 				}
	// 					Session::forget('redirect_from_signin');

	// 					// Don't redirect here!!!! this was a bug.
	// 					if ($path !== 'ajax/profile/getnotifications' && $path !== '/ajax/profile/getnotifications') {
	// 						return redirect($path);
	// 					}
	// 			}

	// 			if (isset($data['is_organization']) && $data['is_organization'] == 1) {
	// 				return redirect()->intended( '/admin' );
	// 			}

	// 			if (isset($data['agency_collection'])) {
	// 				return redirect()->intended( '/agency' );
	// 			}

	// 			if( (isset($user->profile_percent) && $user->profile_percent < 30) ||
	// 				(!isset($user->email) || empty($user->email) || $user->email == 'none') ){
	// 				// return redirect()->intended('/social/one-app');
	// 				return redirect()->intended('/get_started');
	// 			}

	// 			return redirect( '/home' );
	// 		} else {
	// 			$user = new User;
	// 			$user->fname = $result['firstName'];
	// 			$user->lname = $result['lastName'];
	// 			$user->email = $email;
	// 			$user->password = Hash::make( $password );
	// 			$user->email_confirmed = 1;

	// 			if(isset($result['gender'])) {
	// 				$user->gender = $result['gender'] == 'male' ? 'm' : $result['gender'] == 'female' ? 'f' : null;
	// 			}
	// 			if(isset($result['picture'])) {
	// 				$utility = new UtilityController;
	// 				$img = json_decode($utility->uploadUsersPictureWithAUrl($result['picture']));
	// 				if ($img['status'] == "success") {
	// 					$user->profile_img_loc = $img['url'];
	// 				}		
	// 			}

	// 			$ip_lookup = $this->iplookup();

	// 			if (Session::has('signup_params')) {
	// 				$signup_params = Session::get('signup_params');
					
	// 				$user->utm_source     = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
	// 				$user->utm_medium     = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
	// 				$user->utm_content    = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
	// 				$user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
	// 				$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
				
	// 			}elseif(Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])){
	// 				$signup_params = Cache::get(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
					
	// 				$user->utm_source     = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
	// 				$user->utm_medium     = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
	// 				$user->utm_content    = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
	// 				$user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
	// 				$user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';

	// 				Cache::forget(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);
	// 			}		

	// 			$arr = $this->iplookup();

	// 			if (isset($arr['countryName'])) {
	// 				$countries = Country::where('country_name', $arr['countryName'])->first();
	// 				if (isset($countries)) {
	// 				 	$user->country_id = $countries->id;
	// 				}
	// 			}

	// 			$user->save();
	// 			$confirmation = str_random( 20 );
	// 			$token = new ConfirmToken( array( 'token' => $confirmation ) );
	// 			$user->confirmtoken()->save( $token );

	// 			$uai = new UsersAddtlInfo;
	// 			$uai->user_id   = $user->id;
	// 			$uai->linkedin_id = $result['id'];

	// 			$uai->save();

	// 			/************MAILCHIMP WELCOME EMAIL ***********/

	// 			// if (isset($result['email'])) {

	// 			// 	$dt = array();
	// 			// 	$dt['email'] = $result['email'];
	// 			// 	$dt['fname'] = $result['first_name'];
	// 			// 	$dt['lname'] = $result['last_name'];

	// 			// 	$this->integrateMailChimp($dt);
	// 			// }
	// 			/************END OF MAILCHIMP WELCOME EMAIL*****/

	// 			Auth::loginUsingId( $user->id, true );
	// 			$this->setAjaxToken( Auth::user()->id );
	// 			Session::put('userinfo.session_reset', 1);
				
	// 			$data = $this->setSignedInUserData();

	// 			if(Session::has('redirect') || Cache::has('AuthController_redirect_'. $arr['ip'])){
					
	// 				if (Cache::has('AuthController_redirect_'. $arr['ip'])) {
	// 					$redirect = Cache::get('AuthController_redirect_'. $arr['ip']);
	// 					Cache::forget('AuthController_redirect_'. $arr['ip']);
	// 				}else{
	// 					$redirect = Session::get('redirect');
	// 				}

	// 				return redirect($redirect);
	// 			}

  //               if( Session::has('redirect_from_signin') ){
  //                   $path = '/'.Session::get('redirect_from_signin');
  //                   Session::forget('redirect_from_signin');
  //                   return redirect($path);
  //               }

	// 			if (isset($data['is_organization']) && $data['is_organization'] == 1) {
	// 				return redirect()->intended( '/admin' );
	// 			}

	// 			if (isset($data['agency_collection'])) {
	// 				return redirect()->intended( '/agency' );
	// 			}
                
	// 			return redirect()->intended('/get_started');
	// 		}
	// 	}
	// 	// if not ask for permission first
	// 	else {
	// 		// get linkedin authorization
	// 		$url = $linkedin->getAuthorizationUri(['state'=>'DCEEFWF45453sdffef424']);

	// 		// return to facebook login url
	// 		return redirect( (string)$url );
	// 	}
	// }

	/**
	 * Login user to allow ccp purchase
	 *
	 * @return to cart page
	 */
	public function signInForCCP( $returl = null, $getSection = null ){

		$input = Request::all();

		$input_except_some = Request::except('password', '_token');

		Session::put('prefilled_data', $input_except_some);
		//$data['prefilled_data'] = $input_except_some;
		$rules = array( 'email' => 'required', 'password' => array( 'required', 'regex:/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/' ) );// "/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/"

		$v = Validator::make( $input, $rules );

		if ( $v->passes() ) {
			$credentials = array( 'email' => Request::get( 'email' ), 'password' => Request::get( 'password' ) );

			if ( Auth::attempt( $credentials, true ) ) {
				$this->setAjaxToken( Auth::user()->id );
				Session::put('userinfo.session_reset', 1);
				return redirect( $returl.'#'.$getSection );
			} else {
				$error = array( 'We can not find that email or password in the system.' );
				return redirect( '/' )->withErrors( $error );
			}
		}
		return redirect( 'signin' )->withErrors( $v )->withInput(Request::except( 'password' ));
	}

	public function signupForCCP() {

		$input = Request::all();
		$input_except_some = Request::except('password', '_token');

		//dd($input_except_some);
		Session::put('prefilled_data', $input_except_some);

		$v = Validator::make( $input, User::$rules );
		$name = ucfirst($input['fname']). ' '. ucfirst($input['lname']) ;
		$email = Request::get( 'email' );
		$confirmation = str_random( 20 );
		$input_except_some['autoOpenModal'] = 'true';//Add the auto open indicator flag as an input.
		if ( $v->passes() ) {
			$user = new User;
			$user->fname = Request::get( 'fname' );
			$user->lname = Request::get( 'lname' );
			$user->email = Request::get( 'email' );
			$user->password = Hash::make( Request::get( 'password' ) );
			
			$user->birth_date = Request::get ( 'year' ) . '-' . Request::get( 'month' ) . '-' . Request::get( 'day' );
			$user->save();

			/************MAILCHIMP WELCOME EMAIL ***********/
			// if (isset($result['email'])) {
					
			// 	$dt = array();
			// 	$dt['email'] = $result['email'];
			// 	$dt['fname'] = $result['first_name'];
			// 	$dt['lname'] = $result['last_name'];

			// 	$this->integrateMailChimp($dt);
			// }
			/************END OF MAILCHIMP WELCOME EMAIL*****/

			//why do we do TWO db saves to users table?! we should run this above and insert into the DB in one pass.
			$token = new ConfirmToken( array( 'token' => $confirmation ) );
			$user->confirmtoken()->save( $token );

			$this->sendconfirmationEmail( $user->id, $email, $confirmation );

			Auth::loginUsingId( $user->id, true );
			$this->setAjaxToken( Auth::user()->id );
			Session::put('userinfo.session_reset', 1);
			return redirect( '/carepackage#cart' );
		}
		return Redirect::back()->withErrors($v)->withInput($input);
		//return redirect( '/carepackage#cart' )->withErrors( $v )->withInput( Request::except( 'password' ) );
	}

	/**
	 * IMPORTNAT!!!!!!!!!!!!!!!
	 * This method manually log in you as a particular user, this method is not 
	 * suppose to be used by public rather ONLY for plexuss crew to check someone's
	 * profile
	 * @return uri
	 */

	public function loginAs(){
		$input =  Request::all();

		if (!isset($input['t']) ) {
			return redirect( '/' );
		}
		if(isset($input['t']) && $input['t'] == "joie3e23riuhneisuio"){

			$user_id = 2814;

			Auth::loginUsingId( $user_id, true );
			$this->setAjaxToken($user_id);
			Session::put('userinfo.session_reset', 1);
			return redirect( '/home' );

		}
		return redirect( '/' );

	}

    // Creates a new user using the input retrieved by the passthrough page (userMissingFields)
    public function saveNewUserMissingFields($input) {
        $response = [];
        $confirmation = str_random(20);

        $ip = $this->iplookup();

        if (!isset($input['fname']) || !isset($input['lname']) || !isset($input['email'])/* || !isset($input['birth_date'])*/) {
            $response['status'] = 'failed';
            $response['error_message'] = 'Missing fields';

            return $response;
        }

        if (isset($input['email'])) {
            $emailAlreadyExists = User::on('rds1')
                                      ->select('id')
                                      ->where('email', '=', $input['email'])
                                      ->first();

            if (!empty($emailAlreadyExists)) {
                $response['status'] = 'failed';
                $response['error_message'] = 'Email already exists';

                return $response;
            }
        }

        if (isset($input['birth_date'])) {
            $age = floor((time() - strtotime($input['birth_date'])) / 31556926);

            if ($age < 13) {
                $response['status'] = 'failed';
                $response['error_message'] = 'Age less than 13';

                return $response;
            }
        }

        $user = new User;

        $user->fname = $input['fname'];
        $user->lname = $input['lname'];
        $user->email = $input['email'];
        $user->password = Hash::make(str_random(8)); // Randomize password
        $user->remember_token = str_random(60);
        $user->is_passthru_signup = 1;
        
        isset($input['address']) ? $user->address = $input['address'] : NULL;
        isset($input['city']) ? $user->city = $input['city'] : NULL;
        isset($input['country']) ? $user->country_id = $input['country'] : NULL;
        isset($input['state']) ? $user->state = $input['state'] : NULL;
        isset($input['gender']) ? $user->gender = $input['gender'] : NULL;
        isset($input['zip']) ? $user->zip = $input['zip'] : NULL;
        isset($input['birth_date']) ? $user->birth_date = $input['birth_date'] : NULL;
        isset($input['phone']) ? $user->phone = $input['phone'] : NULL;
        (isset($input['txt_opt_in']) && $input['txt_opt_in'] == 'on') ? $user->txt_opt_in = 1 : NULL;

        if (isset($ip['countryName'])) {
            $countries = Country::where('country_name', $ip['countryName'])->first();
            if (isset($countries)) {
                $user->country_id = $countries->id;
            }
        }

        $user->save();

        if (isset($user->id)) {
            if(isset($input['overall-gpa'])|| isset($input['hs-gpa'])) {
                if ($input['in_college'] == 1) {
                    $attributes = ['user_id' => $user->id];
                    $values = ['user_id' => $user->id, 'overall_gpa' => $input['overall-gpa']];
                    $updateScore = Score::updateOrCreate($attributes, $values);
                } else {
                    $attributes = ['user_id' => $user->id];
                    $values = ['user_id' => $user->id, 'hs_gpa' => $input['hs-gpa']];
                    $updateScore = Score::updateOrCreate($attributes, $values);
                }
            }

            $this->CalcIndicatorPercent($user->id);
            $this->CalcProfilePercent($user->id);
            $this->CalcOneAppPercent($user->id);

            $rpc = new ResetPasswordController;
            $rpc->postPasswordResetQueue($user->email);
        }

        $response['status'] = 'success';
        $response['user_id'] = isset($user->id) ? $user->id : NULL;
        $response['hid'] = isset($user->id) ? Crypt::encrypt($user->id) : NULL;

        Auth::loginUsingId( $user->id, true );
        $this->setAjaxToken( Auth::user()->id );
        Session::put('userinfo.session_reset', 1);

        $startcontroller = new StartController();
        $startcontroller->setupcore();

        return $response;
    }

	/*
	***************Private methods below here.***************
	*/

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

	public function clearAjaxToken( $id = null ) {
		$user = User::find( $id );
		$hasToken = $user->ajaxToken()->first();
		$ajaxtoken = new AjaxToken( array( 'token'=> "" ) );
		$user->ajaxtoken()->delete();
	}

	public function sendconfirmationEmail( $user_id, $emailaddress, $confirmation ) {

		// $mac = new MandrillAutomationController;
		// $mac->confirmationEmailForUsers($name, $emailaddress, $confirmation);
		$params = array();
		$params['EMAIL'] = $emailaddress;
		$params['CTA_LINK'] = env('CURRENT_URL').'confirmemail/'.$confirmation;
		
		$user = User::on('bk')->find($user_id);

		$reply_email   = 'support@plexuss.com';

		if (isset($user->country_id) && $user->country_id == 1) {
			$template_name = 'verify_email_template';
		}else{
			$template_name = 'verify_email_template_int';
		}
		
		
		$template_to_send = EmailTemplateSenderProvider::on('bk')
													   ->where('template_name', $template_name)
													   ->first();

		$ab_test_id = NULL;
		if (isset($template_to_send)) {
			$ab_test_id = $template_to_send->ab_test_id;
		}	

		EmailQueueProcess::dispatch($reply_email, $template_name, $params, $emailaddress, $user_id, NULL, $ab_test_id, 1);
		// dispatch((new EmailQueueProcess($reply_email, $template_name, $params, $emailaddress, 
		// 	$user_id, NULL, $ab_test_id, 1))->onQueue("high"));

		$params = array();
		$params['EMAIL'] = $emailaddress;
		$params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
		
		$reply_email   = 'support@plexuss.com';

		if (isset($user->country_id) && $user->country_id == 1) {
			$template_name = 'welcome_mail';
		}else{
			$template_name = 'welcome_mail_int';
		}

		$ten_mins = Carbon::now()->addMinutes(10);


		$template_to_send = EmailTemplateSenderProvider::on('bk')
													   ->where('template_name', $template_name)
													   ->first();

		$ab_test_id = NULL;
		if (isset($template_to_send)) {
			$ab_test_id = $template_to_send->ab_test_id;
		}
		EmailQueueProcess::dispatch($reply_email, $template_name, $params, $emailaddress, $user_id, NULL, $ab_test_id, 2)
                                                ->delay($ten_mins);
		// dispatch((new EmailQueueProcess($reply_email, $template_name, $params, $emailaddress, $user_id, NULL, $ab_test_id, 2))->onQueue("high")->delay($ten_mins));
	}

	private function thankyouEmail( $name, $emailaddress ) {
		$data = array( 'name'=> $name );
		Mail::send( 'emails.auth.thankYouForJoining', $data, function( $message ) use ( $emailaddress, $name ) {
				$message->to( $emailaddress, $name )->subject( 'Welcome to Plexuss!' );
			}
		);
	}

	private function integrateMailChimp($data = null){

		if ($data == null) {
			return 'bad';
		}
		$mc = new MailChimpModel('welcome_email');
		$params = array();

		$params['FNAME'] = $data['fname'];
		$params['LNAME'] = $data['lname'];
		$params['EMAIL'] = $data['email'];

		$email = array('email' => $data['email']);


		try{
			$subscribe = $mc->subscribe($email, $params);
		}catch (Mailchimp_Error $e) {
			return $e;
			//error
			// $error = $e->xdebug_message;
			//return "Error Occured!";
		}
		return 'success';
	}

	private function setSignedInUserData(){
		$startcontroller = new StartController();
		$startcontroller->setupcore();
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		return $data;
	}

}
