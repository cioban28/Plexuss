<?php

namespace App\Http\Controllers;

use DB, Request, Session, Illuminate\Support\Facades\Auth, Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Cache;

use App\User, App\Objective, App\PremiumUser, App\UsersPremiumEssay, App\UsersAppliedColleges, App\OrganizationPortal, App\AorPermission, App\UsersSalesControl, App\Country, App\UsersAddtlInfo, App\UsersCustomQuestion;
use App\RevenueOrganization, App\ScholarshipAdminUser;
use Illuminate\Support\Facades\Crypt;

class StartController extends Controller
{
    //
    //This Controller gets called BEFORE other controllers on EACH page load.
	
	public function setupcore(){
		
		//Setting to allow long scripts to run on the server. We will hange this bck to 300 when done testing.
		ini_set('max_execution_time', 300);
		
		$inputs = Request::all();
		
		//save the redirect url in session
		if(isset($inputs['redirect'])){
			//dd($inputs['redirect']);
			Session::put('redirect_value', $inputs['redirect']);
		}
		// dd($inputs);
		//This builds the user session.
		if (!Session::has('userinfo')){
			$newSession = $this->buildUserInfo();

		} else {
			//check if 'reset_session' is true. If it is RESET the session
			if (Session::get('userinfo.session_reset') == 1 || 
				(Cache::has(env('ENVIRONMENT') .'_'. Session::get('userinfo.id') . '_session_reset') 
				&& Cache::get(env('ENVIRONMENT') .'_'. Session::get('userinfo.id') . '_session_reset') ==1)) {
					
				Cache::pull(env('ENVIRONMENT') .'_'. Session::get('userinfo.id') . '_session_reset');
				$newSession = $this->buildUserInfo();
			}

			/*ANTHONY WILL COME BACK TO THIS **************************************************
			
			//if user is signed in redirect the user to the appropriate url
			$userinfo = Session::get('userinfo');

			if($userinfo['signed_in'] ==1){


				$redirect_value = Session::get('redirect_value');
				//dd($redirect_value);
				if(isset($redirect_value)){

					Session::forget('redirect_value');
					//dd('ready to redirect');

					header('Location :'.'/'.$redirect_value);
					return Redirect::to('/'.$redirect_value);
				}
				

			}

			*/
		}

		/*
		|--------------------------------------------------------------------------
		| Plexuss Tracking initiation
		|--------------------------------------------------------------------------
		|
		|
		*/
		$tp = new TrackingPageController();
		$tp->setPageView();

		//Print out session
		//dd(Session::get('userinfo'));
	}


	//This builds the user session if needed.
	private function buildUserInfo(){
		$data = [];
		$agent = new Agent();

		//defaults
		$data['signed_in'] = 0;
		$data['session_reset'] = 0;
		$data['is_organization'] = 0;
		$data['browser'] = $agent->browser();
		$data['browser_verion'] = $agent->version( $data['browser'] );
		$data['platform'] = $agent->platform();
		$data['platform_verion'] = $agent->version( $data['platform'] );
		$data['device_name'] = $agent->device();
		$data['is_mobile'] = $agent->isMobile();

		$ip = Request::getClientIp();
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		   if (isset( $_SERVER['REMOTE_ADDR']) ) {
		    $ip =  $_SERVER['REMOTE_ADDR'];
		   }     
		}
		$data['ip'] = $ip;
		// dd(991931);
		//signed in additions

		if (Auth::check()){
			$user = User::find( Auth::user()->id );
			$objective = new Objective();
			$objs = $objective->getUserMajors($user->id);
			// set user_table index in session
			Session::put( 'user_table', $user );
			Session::put( 'user_majors', $objs);

			//get_started_percent from user_addtl_info
			$uai = UsersAddtlInfo::on('rds1')->where('user_id', $user->id)->first();
			isset($uai->get_started_percent) ? $data['get_started_percent'] = $uai->get_started_percent : $data['get_started_percent'] = 0;

			//one_app_percent from user_custom_questions
			$uai = UsersCustomQuestion::on('rds1')->where('user_id', $user->id)->first();
			isset($uai->one_app_percent) ? $data['one_app_percent'] = $uai->one_app_percent : $data['one_app_percent'] = 0;

			// build userinfo
			$data['id'] = $user->id;
			$data['hashed_user_id'] = Crypt::encrypt($user->id);
			$data['signed_in'] = 1;
			$data['email'] = $user->email;
			$data['phone'] = $user->phone;
			$data['state'] = $user->state;
			$data['zip'] = $user->zip;
			$data['session_reset'] = 0;
			$data['is_organization'] = $user->is_organization;
			$data['profile_img_loc'] = $user->profile_img_loc;
			$data['email_confirmed'] = $user->email_confirmed;
			$data['fname'] = $user->fname;
			$data['lname'] = $user->lname;
			$data['remember_token'] = $user->remember_token;
			$data['gender'] = $user->gender;
            $data['is_aor'] = $user->is_aor;
            $data['txt_opt_in'] = $user->txt_opt_in;
            $data['country_id'] = $user->country_id;
            $data['premium_user_level_1'] = 0;
            $data['premium_user_type'] = null;
            $data['premium_user_plan'] = null;
            $data['completed_signup'] = $user->completed_signup;
            $data['profile_percent'] = $user->profile_percent;
            $data['fb_id'] = isset($user->fb_id) ? $user->fb_id : null;
            $data['created_at'] = $user->created_at;
            $data['user_majors'] = $objs;
            $data['birth_date'] = $user->birth_date;
            $data['in_college'] = $user->in_college;

            if( isset($user->is_student) && $user->is_student ){
                $data['is_student'] = 1;
            }elseif( isset($user->is_alumni) && $user->is_alumni ){
                $data['is_alumni'] = 1;
            }elseif( isset($user->is_parent) && $user->is_parent ){
                $data['is_parent'] = 1;
            }
			
			if($data['is_organization'] == 1){

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

                	$arr = array();
                    $tmp_arr = array();

                    $tmp_arr['id'] = -1;
                    $tmp_arr['org_branch_id'] =  $vo->branch_id;
                    $tmp_arr['name'] = 'General';
                    $tmp_arr['is_default'] = 0;
                    
                    $ro = RevenueOrganization::on('rds1')
                                             ->select('id', 'type')
                                             ->where('org_branch_id', $vo->branch_id)
                                             ->first();

                    if (isset($ro)) {
                        $tmp_arr['ro_id'] = $ro->id;
                        $tmp_arr['ro_type'] = $ro->type;
                    }

                    $arr[] = (object)$tmp_arr;

                    $data['organization_portals']->splice(0, 0, (object)$arr);
                }

                if($data['is_aor'] == 1){
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
                    if (!isset($data['default_organization_portal']) && $vo->super_admin == 0 && isset($data['organization_portals'][0])) {
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
				$data['school_id'] = $vo->school_id;
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
                    // $data['premium_user_plan'] = $pu->type === 'onetime_plus' ? 'premium plus' : 'premium';

                    if ($pu->type === 'onetime_plus') {
                        $data['premium_user_plan'] = 'premium plus';
                    }elseif ($pu->type === 'onetime_unlimited') {
                        $data['premium_user_plan'] = 'premium unlimited';
                    }else{
                        $data['premium_user_plan'] = 'premium';
                    }
                    
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
                    }elseif ($pu->type =='onetime_unlimited') {
                        $total_num_of_eligible_essays = 99999;
                        $total_num_of_applied_colleges = 99999;
                    }else{
                        $total_num_of_eligible_essays = 1;
                        $total_num_of_applied_colleges = 5;
                    }

                    $data['num_of_eligible_premium_essays'] = $total_num_of_eligible_essays - $num_of_premium_essays_viewed;
                    if ($data['num_of_eligible_premium_essays'] < 0) {
                        $data['num_of_eligible_premium_essays'] = 0;
                    }

                    $data['num_of_eligible_applied_colleges'] = $total_num_of_applied_colleges - $num_of_applied_colleges;
                    if ($data['num_of_eligible_applied_colleges'] < 0) {
                        $data['num_of_eligible_applied_colleges'] = 0;
                    }

                }else{
                    $total_num_of_applied_colleges = 5;
                    $total_num_of_eligible_essays  = 1;

                    $data['num_of_eligible_premium_essays'] = $total_num_of_eligible_essays - $num_of_premium_essays_viewed;
                    if ($data['num_of_eligible_premium_essays'] < 0) {
                        $data['num_of_eligible_premium_essays'] = 0;
                    }

                    $data['num_of_eligible_applied_colleges'] = $total_num_of_applied_colleges - $num_of_applied_colleges;
                    if ($data['num_of_eligible_applied_colleges'] < 0) {
                        $data['num_of_eligible_applied_colleges'] = 0;
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


			$data['is_agency'] = $user->is_agency;

			// Is GDPR add it to data everywhere.
			$arr = $this->iplookup();
			$is_gdpr = Country::on('rds1')
                              ->where('country_name', '=', $arr['countryName'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();
	        if (!$is_gdpr) {
	        	if (isset($user->country_id)) {
	        		$is_gdpr = Country::on('rds1')
	                              ->where('id', '=', $user->country_id)
	                              ->where('is_gdpr', '=', 1)
	                              ->exists();
	        	}	
	        }

	        $data['is_gdpr'] = $is_gdpr;

	        // Check if the user is scholarship admin 
	        $data['is_scholarship_admin_only'] = false;

	        $sau = DB::connection('rds1')->table('scholarship_providers as so')
	        							 ->join('scholarship_admin_users as sau', 'sau.scholarship_org_id', '=', 'so.id')
	        							 ->join('scholarship_verified as sv', 'sv.provider_id', '=', 'so.id')
	        							 ->where('sau.user_id', $user->id)
	        						     ->where('sau.active', 1)
	        						     ->select('only_scholarship', 'sv.id as scholarship_id', 'so.id as scholarship_org_id')
	        						     ->get();

	        if (isset($sau)) {

	        	$arr = array();
	        	foreach ($sau as $key) {
	        		($key->only_scholarship == 1) ? $data['is_scholarship_admin_only'] = true : null;
	        		$arr[] = $key->scholarship_id;
	        		$data['scholarship_org_id'] = $key->scholarship_org_id;
	        	}
	        	
	        	$data['scholarship_ids'] = $arr;
	        }
		}

		Session::put('userinfo', $data);

		//********************************important
		//We needed to add cache for userinfo ONLY for helper folder since it doesn't have access to laravel sessions
		// if we find a way to get access to laravel session we need to REMOVE the below line.(Also AuthController->signout)
		Cache::put(env('ENVIRONMENT') .'_'.'userinfo', $data, 1440);
	}
}
