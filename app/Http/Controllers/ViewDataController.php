<?php

namespace App\Http\Controllers;

use Request, Session, DateTime, Hash;
use App\UsersSharedSignup;
use App\WebinarControllerModel, App\NotificationTopNav, App\Agency;

class ViewDataController extends Controller
{
    //
    private $event_id = 6;

	public function buildData($is_ajax = null){
        $ip = Request::getClientIp();
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
           if (isset( $_SERVER['REMOTE_ADDR']) ) {
            $ip =  $_SERVER['REMOTE_ADDR'];
           }     
        }

		$data= array();
		$data['alerts'] = array();
        
		//get signed in status
		if (Session::get('userinfo.signed_in') == 1){
			$data['signed_in'] = 1;
			$data['user_id'] = Session::get('userinfo.id');
		} else {
			$data['signed_in'] = 0;
		}

		//get organaztion status
		if (Session::get('userinfo.is_organization') == 1) {
			$data['is_organization'] = 1;
		}


        $date = Session::get('userinfo.created_at');
        
        if (isset($date)) {
            $getdate = substr($date->date, 0, strrpos($date->date, ' '));
            $timestamp = strtotime($getdate);
            $created_date = date('M d, Y',$timestamp);  
        }

		//get mobile status. should always be set in session
        $data['created_at']               = isset($created_date) ? $created_date : null;
        $data['phone']                    = Session::get('userinfo.phone');
		$data['is_mobile']                = Session::get('userinfo.is_mobile');
		$data['org_id']                   = Session::get('userinfo.org_id');  
		$data['org_branch_id']            = Session::get('userinfo.org_branch_id');  
		$data['org_name']                 = Session::get('userinfo.org_name');    		
		$data['org_school_id']            = Session::get('userinfo.school_id');
        $data['bachelor_plan']            = Session::get('userinfo.bachelor_plan');
        $data['set_goal_reminder']        = Session::get('userinfo.set_goal_reminder');
        $data['existing_client']          = Session::get('userinfo.existing_client');
        $data['appointment_set']          = Session::get('userinfo.appointment_set');
        $data['num_of_applications']      = Session::get('userinfo.num_of_applications');
        $data['num_of_enrollments']       = Session::get('userinfo.num_of_enrollments');
		$data['remember_token']           = Session::get('userinfo.remember_token');
		$data['profile_img_loc']          = Session::get('userinfo.profile_img_loc');
		$data['fname']                    = ucwords(strtolower(Session::get('userinfo.fname')));
		$data['lname']                    = ucwords(strtolower(Session::get('userinfo.lname')));
		$data['user_id']                  = Session::get('userinfo.id');
        $data['hashed_user_id']           = Session::get('userinfo.hashed_user_id');
        $data['fb_id']                    = Session::get('userinfo.fb_id');
		$data['school_name']              = Session::get('userinfo.school_name');
		$data['ip']                       = isset($ip) ? $ip : Session::get('userinfo.ip');
		$data['email']                    = Session::get('userinfo.email');
		$data['email_provider']           = $this->getEmailProviderDomain($data['email']);
        $data['email_hashed']             = Hash::make($data['email']);
        $data['school_slug']              = Session::get('userinfo.school_slug');
        $data['school_logo']              = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.Session::get('userinfo.school_logo');
		$data['gender']                   = Session::get('userinfo.gender');
        $data['org_plan_status']          = Session::get('userinfo.org_plan_status');
        $data['premier_trial_end_date']   = Session::get('userinfo.premier_trial_end_date');
        $data['premier_trial_begin_date'] = Session::get('userinfo.premier_trial_begin_date');
        $data['profile_percent']          = Session::get('userinfo.profile_percent');
        $data['requested_upgrade']        = Session::get('userinfo.requested_upgrade');
        $data['super_admin']              = Session::get('userinfo.super_admin');
        $data['state']                    = Session::get('userinfo.state');
        $data['zip']                      = Session::get('userinfo.zip');
        $data['email_confirmed']          = Session::get('userinfo.email_confirmed');
        $data['is_aor']                   = Session::get('userinfo.is_aor');
        $data['premium_user_level_1']     = Session::get('userinfo.premium_user_level_1');
        $data['premium_user_type']        = Session::get('userinfo.premium_user_type');
        $data['premium_user_plan']        = Session::get('userinfo.premium_user_plan');
        $data['completed_signup']         = Session::get('userinfo.completed_signup');
        $data['txt_opt_in']               = Session::get('userinfo.txt_opt_in');
        $data['country_id']               = Session::get('userinfo.country_id');
        $data['country_based_on_ip']      = isset($ip) ? $this->iplookup($ip)['countryAbbr'] : null;
        $data['contract']                 = Session::get('userinfo.contract');
        $data['balance']                  = Session::get('userinfo.balance');
        $data['cost_per_handshake']       = Session::get('userinfo.cost_per_handshake');
        $data['is_plexuss']               = Session::get('userinfo.is_plexuss');
        $data['num_of_eligible_premium_essays']   = Session::get('userinfo.num_of_eligible_premium_essays');
        $data['birth_date']               = Session::get('userinfo.birth_date');
        $data['is_gdpr']                  = Session::get('userinfo.is_gdpr');
        $data['is_scholarship_admin_only']= Session::get('userinfo.is_scholarship_admin_only');
        $data['scholarship_ids']          = Session::get('userinfo.scholarship_ids');
        $data['scholarship_org_id']       = Session::get('userinfo.scholarship_org_id');
        // $data['is_incognito']             = Session::get('userinfo.is_incognito');
        $data['one_app_percent']          = Session::get('userinfo.one_app_percent');
        $data['get_started_percent']      = Session::get('userinfo.get_started_percent');
        $data['user_school_names']        = Session::get('userinfo.user_school_names');
        $data['in_college']               = Session::get('userinfo.in_college');
        $data['current_url_wo_domain']    = str_replace(Request::url(), "", env('CURRENT_URL'));

        // $data['num_of_eligible_applied_colleges'] = Session::get('userinfo.num_of_eligible_applied_colleges');

        $data = $this->determineNumOfAppliedColleges($data);

        if( Session::has('userinfo.is_student') ){
            $data['is_student']     = Session::get('userinfo.is_student');
        }elseif( Session::has('userinfo.is_parent') ){
            $data['is_parent']      = Session::get('userinfo.is_parent');
        }
        if( Session::has('userinfo.aor_id') ){
            $data['aor_id']     = Session::get('userinfo.aor_id');
        }
        $data['premier_trial_end_date_ACTUAL']   =  $data['premier_trial_end_date'] ;
        $data['premier_trial_begin_date_ACTUAL'] = $data['premier_trial_begin_date'];

        $data['organization_portals'] = Session::get('userinfo.organization_portals');
        $data['default_organization_portal'] = Session::get('userinfo.default_organization_portal');

        if (isset($data['premier_trial_end_date'])) {
            $today = date('Y-m-d');

            $trial_date = $data['premier_trial_end_date'];
            $datetime1 = new DateTime($today);
            $datetime2 = new DateTime($trial_date);

            $interval = $datetime1->diff($datetime2);

            $data['premier_trial_end_date'] = $interval->format('%R%a');

            $data['premier_trial_end_date'] = str_replace('+', '', $data['premier_trial_end_date']);

            $data['premier_trial_end_date'] = $data['premier_trial_end_date'] > 0 ? $data['premier_trial_end_date'] : null;

            if (!isset($data['premier_trial_end_date'])) {
                $data['show_upgrade_button'] = 1;
            }else{
                $data['show_upgrade_button'] = 0;
            }
        }

        if ($data['org_plan_status'] == 'Free') {
            $data['show_upgrade_button'] = 1;
        }

        $current_live_colleges = $this->is_any_school_online();

		if ($current_live_colleges == true) {
			$data['is_any_school_live'] = true;
		}else{
			$data['is_any_school_live'] = false;
		}
        
        $is_sales = Session::get('userinfo.is_sales');
        if (isset($is_sales)) {
            $data['is_sales'] = true;
        }

        $data['is_agency'] = Session::get('userinfo.is_agency');

        if ($data['is_agency'] == 1) {
            $agency = new Agency;
            $data['agency_collection'] = $agency->getAgencyProfile($data['user_id']);
            $today = date('Y-m-d');

            $trial_date = $data['agency_collection']->trial_end_date;
            $datetime1 = new DateTime($today);
            $datetime2 = new DateTime($trial_date);

            $interval = $datetime1->diff($datetime2);

            $data['remaining_trial'] = $interval->format('%R%a');

            $data['remaining_trial'] = str_replace('+', '', $data['remaining_trial']);
            $data['balance'] = $data['agency_collection']->balance;
        }

        if($data['signed_in'] == 1 && !isset($is_ajax)){
            // // Get notifications for top nav
            // $ntn = new NotificationTopNav;
            // $dt = $ntn->getMyNotifications($data, true);
            
            // //$dt['data'] = array_reverse($dt['data']);
            // $data['topnav_notifications'] = $dt;

            // $data['topnav_messages'] = $ntn->getTopNavMessages($data);
            $data['topnav_notifications'] = null;
            $data['topnav_messages'] = null;
        } 

        // Webinar settings.
        if (!isset($is_ajax)) {
            $wc = new WebinarControllerModel;
            $webinar_is_live  = $wc->isWebinarLive($this->event_id);
            if (isset($webinar_is_live->video)) {
                $video = $webinar_is_live->video;
            }

            $wc = new WebinarControllerModel;
            $can_show_webinar = $wc->canShowWebinarFrontPage($this->event_id);
            if (isset($can_show_webinar->video)) {
                $video = $can_show_webinar->video;
            }

            $data['webinar_is_live'] = false;
            $data['can_show_webinar'] = false;

            if (isset($webinar_is_live) || isset($can_show_webinar)) {

                if (isset($webinar_is_live)) {
                    $data['webinar_is_live'] = true;
                }
                if (isset($can_show_webinar)) {
                    $data['can_show_webinar'] = true;
                }

                $wls = new WebinarLiveSignup;
                $data['event_id'] = $this->event_id;

                $wls = $wls->hasWebinarSignedup($data);
                $data['webinar_live_already_signup'] = false;
                if(isset($wls)){
                    $data['webinar_live_already_signup'] = true;
                    $data['webinar_embeded_video']       = $video;
                    if ($data['is_mobile'] == true) {
                        $data['webinar_embeded_video'] = str_replace("560", "320", $data['webinar_embeded_video']);
                    }
                }
            }
        }

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

        $data['is_admin_premium'] = $is_admin_premium;
        // Mixpanel instance

        // $mp = Mixpanel::getInstance("83db7245f0f367f0d330982651b8309a");

        // $data['mixpanel'] = $mp;
		// /dd($data['topnav_messages']);
		
        //dd($data);
		//dd(Session::get('userinfo'));
		return $data;
	}

    public function determineNumOfAppliedColleges($data) {
        $data['num_of_eligible_applied_colleges'] = 1;

        // Check if this person has shared on facebook
        $has_facebook_shared = UsersSharedSignup::select('utm_term')
                                    ->where('user_id', $data['user_id'])
                                    ->where('utm_content', '=', 'additional_apps')
                                    ->get();
        
        $data['has_facebook_shared'] = 0;
        
        if (!$has_facebook_shared->isEmpty()) {
            $data['has_facebook_shared'] = 1;
            $data['num_of_eligible_applied_colleges'] = 2;
        }

        // Only allow premium 10 colleges if they are also international (not United States)
        // Allow United States Premium with 2.
        if ($data['premium_user_level_1'] === 1) {
            $data['num_of_eligible_applied_colleges'] = 2;

            if ($data['country_id'] !== 1)
                $data['num_of_eligible_applied_colleges'] = 10;
        }

        return $data;
    }
}
