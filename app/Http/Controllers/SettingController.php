<?php

namespace App\Http\Controllers;

use Request, DB, Session,Validator,Hash;;
use Carbon\Carbon;
use App\Http\Controllers\OmniPayController;
use App\Country, App\State, App\Organization, App\OrganizationPortal, App\PurchasedPhone, App\AdminText, App\FreeTextCountry, App\SettingNotificationName, App\UsersInvite, App\AjaxToken;
use App\User, App\SettingNotificationLog, App\SettingDataPreferenceLog, App\SettingNotificationLogHistory, App\UserAccountSettings;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;


class SettingController extends Controller
{
    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex( $page = null ) {
		
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['active_tab'] = 'setting';

		if(isset($page)){
			$data['active_tab'] = $page;
		}
		
		$data['title'] = 'Plexuss Setting Page';
		$data['currentPage'] = 'setting';

		if (isset($data['profile_img_loc']) && !empty($data['profile_img_loc'])) {
			$data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}
		
		$data['userinfo'] = array(
			'id' => $data["user_id"],
			'fname' => $data["fname"],
			'lname' => $data["lname"],
			'zip' => $data["zip"]
		);

		if ( !$data['email_confirmed'] ) {
			array_push( $data['alerts'], 
				array(
					'img' => '/images/topAlert/envelope.png',
					'type' => 'hard',
					'dur' => '10000',
					'msg' => '<span class=\"pls-confirm-msg\">Please confirm your email to gain full access to Plexuss tools.</span> <span class=\"go-to-email\" data-email-domain=\"'.$data['email_provider'].'\"><span class=\"hide-for-small-only\">Go to</span><span class=\"show-for-small-only\">Confirm</span> Email</span> <span> | <span class=\"resend-email\">Resend <span class=\"hide-for-small-only\">Email</span></span></span> <span> | <span class=\"change-email\" data-current-email=\"'.$data['email'].'\">Change email <span class=\"hide-for-small-only\">address</span></span></span>'
				)
			);

		}

		$contactList = array();
		if(Session::has('invite_contactList')){
			$contactList = Session::get('invite_contactList');
		}
		if (isset($data['super_admin']) && $data['super_admin'] == 1) {
			$data = $this->getManageUsers($data);
		}
		
		$data['contactList'] = $contactList;

		$states_names = State::all();
		$states = array('' => 'Select...');

		if( isset($states_names) && !empty($states_names) ){
			foreach ($states_names as $key => $value) {
				$states[$value->state_abbr] = $value->state_name;
			}
		}

		$data['states'] = $states;
		
		$opc = new OmniPayController;
		$data['paymentInfo'] = $opc->retrieveCustomer();

		$adminText = AdminText::where('org_branch_id', $data['org_branch_id'])->first();

		if(isset($adminText) && !empty($adminText)) {
			$data['num_of_free_texts'] = $adminText->num_of_free_texts;
			$data['num_of_eligble_texts'] = $adminText->num_of_eligble_texts;
			$data['textmsg_tier'] = $adminText->tier;
			$data['textmsg_expires_date'] = Carbon::parse($adminText->expires_at); 
			$data['flat_fee_sub_tier'] = $adminText->flat_fee_sub_tier;
			$data['auto_renew'] = $adminText->auto_renew;
		}
		
		// get free text countries
        $data['free_text_countries'] = FreeTextCountry::select('country_code', 'country_name')->get();

        // get phone number 
        $purchasedPhone = PurchasedPhone::where('org_branch_id', $data['org_branch_id'])->select('phone')->first();
        if(isset($purchasedPhone) && !empty($purchasedPhone)) {
        	$data['purchased_phone'] = $purchasedPhone->phone;  	
        }

        $data['current_time'] = Carbon::now();
        
        $countries_names= Country::on('rds1')->get();

        $countries = array('' => 'Select...');

		foreach ($countries_names as $key => $value) {
			$countries[$value->country_code] = $value->country_name;
		}

		$data['countries'] = $countries;

		$c = new Country;
		$data['callingCodes'] = $c->getUniqueAreaCodes();

		if (isset($data['phone'])) {

			$tmp_phone = explode(' ', $data['phone']);

			if (count($tmp_phone) == 1) {
				$phone = $tmp_phone[0];
			}elseif (count($tmp_phone) == 2) {
				$phone = $tmp_phone[1];
			}else{
				$phone = $data['phone'];
			}
		}else{
			$phone = null;
		}

		$data['phone_without_calling_code'] = $phone;

		$country = Country::on('rds1')->find($data['country_id']);
		if( isset($country) ){
			$data['calling_code'] = $country->country_phone_code;
		}

		$snn = new SettingNotificationName;

		$data['setting_notification'] = $snn->getSettingNotifications();

		$user = User::find($data['user_id']);
		if( isset($user) ){
			$data['verified_phone'] = $user->verified_phone;
		}
		
		$data["error_msg"] = "";
		$data["pass_not_match"] = "";
		$data["success_msg"] = "";
		$input = Request::all();
		if(isset($input["action"]) && $input["action"]=="change_pass"){
			$user=User::find(Auth::user()->id);
			$validator = Validator::make( $input, [
				'old_pass' => 'required',
				'new_pass' => 'required',
				'verify_pass' => 'required|same:new_pass'],[
				'old_pass.required' => ' The Old Password field is required.',
				'new_pass.min' => ' The Verify Password and Old Password Must be same.',
				] );
			if($input['new_pass']!=$input['verify_pass']){
				$data["pass_not_match"] = "New Password & Verify Password not matched";
			}else if (Hash::check($input['old_pass'],$user->password))
			{
				$user->password=Hash::make($input['new_pass']);
				$user->save();
				$data["success_msg"] =  "Account Password Changed Successfully.";
			}
			else
			{
				$data["error_msg"] = "Old Password not matched";
			}	
			
		}

		// Setting Notification Data Prefenreces
		// $data['setting_notification']['data_preferences'] = 'false';
		$sdpl = SettingDataPreferenceLog::on('rds1')->where('user_id', $data['user_id'])
													->orderBy('id', 'DESC')
													->first();

		if (isset($sdpl)) {
			$data['setting_notification']['data_preferences']['all'] = $sdpl->optin;

			$data['setting_notification']['data_preferences']['lcca'] 		= $sdpl->lcca;
			$data['setting_notification']['data_preferences']['st_patrick'] = $sdpl->st_patrick;
			$data['setting_notification']['data_preferences']['lbsf'] 		= $sdpl->lbsf;
			$data['setting_notification']['data_preferences']['gisma'] 		= $sdpl->gisma;
			$data['setting_notification']['data_preferences']['aul'] 		= $sdpl->aul;
			$data['setting_notification']['data_preferences']['bsbi'] 		= $sdpl->bsbi;
			$data['setting_notification']['data_preferences']['tsom'] 		= $sdpl->tsom;
			$data['setting_notification']['data_preferences']['tlg'] 		= $sdpl->tlg;
			$data['setting_notification']['data_preferences']['ulaw'] 		= $sdpl->ulaw;

		}			

		// Show data preferences only to GDPR users
		$arr = $this->iplookup();
		$is_gdpr = Country::on('rds1')
                              ->where('country_name', '=', $arr['countryName'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();
        if (!$is_gdpr) {
        	$is_gdpr = Country::on('rds1')
                              ->where('id', '=', $data['country_id'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();
        }

        $data['is_gdpr'] = $is_gdpr;			

		return View('private.setting.index', $data);
	}
	
	/**
	 * return the imported list of students
	 *
	 * @return json
	 */
	public function getImportedStudents(){

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$subscribed_emails = array();
		/*$mc = new MailChimpModel('users_invites');
		try{
			$opt = array('limit' => '100');
			$members = $mc->members($opt);

			$members_data = $members['data'];

			if($members['total'] > 100){

				foreach ($members_data as $key) {
					$subscribed_emails[] = $key['email'];
				}

				$pages = ceil($members['total']/100);

				$cnt = 1;
				while ($cnt <= $pages ) {
					$opt['start'] = $cnt;
					$members = $mc->members($opt);

					$members_data = $members['data'];

					foreach ($members_data as $key) {
						$subscribed_emails[] = $key['email'];
					}
					$cnt++;
				}
			}else{
				foreach ($members_data as $key) {
					$subscribed_emails[] = $key['email'];
				}
			}
		}catch (Mailchimp_Error $e) {
			//error
			// $error = $e->xdebug_message;
			//return "Error Occured!";
		}*/
		
		$users_invites = UsersInvite::where('user_id', $data['user_id'])
						->where('sent', 0)
						->select('user_id','invite_email', 'invite_name', 'source',
						 'sent', 'created_at', 'updated_at');

		if(isset($subscribed_emails)){
			$users_invites = $users_invites->whereNotIn('invite_email', $subscribed_emails);
		}

		$users_invites = $users_invites->get()->toArray();

		Session::put('invite_contactList', $users_invites);
		
		return json_encode($users_invites);
	}

	public function getManageUsers($data){

		$org = new Organization;
		$org = $org->getThisOrgInfo($data['org_branch_id'], false);

		$opu = new OrganizationPortal;

		$arr = array();
		foreach ($org as $key) {
			$tmp = array();
			$tmp['user_id'] = Crypt::encrypt($key->user_id);
			$tmp['fname'] = $key->fname;
			$tmp['lname'] = $key->lname;
			$tmp['profile_img_loc'] = null;
			if (isset($key->profile_img_loc)) {
				$tmp['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$key->profile_img_loc;
			}
			$tmp['super_admin'] = $key->super_admin;
			$tmp['portal_info'] = $opu->getUsersOrgnizationPortals($data['org_branch_id'], $key->user_id);

			$arr[] = $tmp;
		}

		$data['users'] = $arr;

		$portals = $opu->getUsersOrgnizationPortalsByOrgBranchId($data['org_branch_id'], $data['user_id']);

		$tmp_portal_id = array();
		$tmp = array();
		$tmp_deactives = array();

		foreach ($portals as $key) {
			if (in_array($key->id, $tmp_portal_id)) {
				$index = array_search($key->id, $tmp_portal_id); 
				// dd($tmp);
				$tmp_arr = array();
				$tmp_arr['user_id'] = Crypt::encrypt($key->user_id);
				$tmp_arr['email']   = $key->email;
				$tmp_arr['super_admin']   = $key->super_admin;
				$tmp[$index]->users[] = $tmp_arr;
				continue;
			}else{
				$tmp_portal_id[] = $key->id;
			}
	
			$tmp_key = $key;

			$tmp_arr = array();
			$tmp_key->users     = array();
			$tmp_key->hashedid  = Crypt::encrypt($key->id);

			if (isset($key->user_id) && !empty($key->user_id)) {
				$tmp_arr['user_id'] = Crypt::encrypt($key->user_id);
				$tmp_arr['email']   = $key->email;
				$tmp_arr['super_admin']   = $key->super_admin;

				$tmp_key->users[]   = $tmp_arr;
			}
			unset($tmp_key->user_id);
			unset($tmp_key->email);
			unset($tmp_key->is_default);
			unset($tmp_key->id);

			$tmp[] = $tmp_key;
		}

		$data['active_portals'] = array();
		$data['deactive_portals'] = array();

		foreach ($tmp as $key) {
			
			if ($key->active == 1) {
				$data['active_portals'][] = $key;
			}else{
				$data['deactive_portals'][] = $key;
			}
		}

		return $data;
	}
	
	
	//********************************************* Ajax Call Function ******************************************* //
	public function getAccountSettinInfo( $token = null ) {
		
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if ( !$this->checkToken( $token ) ) {
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);	
				
			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;
		
			//echo "<pre>";print_r($data);exit();
			return View( 'private.setting.ajax.accountSetting', $data);
		}
	}

	public function postAccountSettinInfo( $token = null )
	{
		
		
	}
	
	
	public function getEmailSettinInfo( $token = null ) {
		
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if ( !$this->checkToken( $token ) ) {
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);	
				
			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;
		
			//echo "<pre>";print_r($data);exit();
			return View( 'private.setting.ajax.emailNotification', $data);
		}
	}
	public function getMobileSettinInfo( $token = null ) {
		
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if ( !$this->checkToken( $token ) ) {
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);	
				
			$data = array( 'token' => $token );
			$data['ajaxtoken'] = $token;
		
			//echo "<pre>";print_r($data);exit();
			return View( 'private.setting.ajax.mobileNotification', $data);
		}
	}
	
	
	public function getPotalSettinInfo( $token = null ) {
		
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if ( !$this->checkToken( $token ) ) {
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);	
				
			$data = array( 'token' => $token );
			$data['currentPage'] = 'setting';
			$data['ajaxtoken'] = $token;

			//echo "<pre>";print_r($data);exit();
			return View( 'private.setting.ajax.portalSetting', $data);
		}
	}
	
	public function getGrantSettinInfo( $token = null ) {
		
		if (Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if ( !$this->checkToken( $token ) ) {
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);	
				
			$data = array( 'token' => $token);			
			$data['ajaxtoken'] = $token;
		
			//echo "<pre>";print_r($data);exit();
			return View( 'private.setting.ajax.grantSetting', $data);
		}
	}

	
	
	private function checkToken( $token ) {
		$ajaxtoken = AjaxToken::where( 'token', '=', $token )->first();
		if ( !$ajaxtoken ) {
			return 0;
		} else {
			return 1;
		}
	}

	public function saveUserAccountPrivacy(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$arr = array();
		$arr['user_id'] = $data['user_id'];

		foreach ($input as $key => $value) {
			$arr[$key] = $value;
		}

		$snas2 = new UserAccountSettings;
		$snas2->insertOrUpdate($arr);

		$ret = array();
		$ret['response'] = 'success';
		$ret['account_settings'] = $snas2->getUserAccountSettings($data['user_id']);

		return $ret;
	}

	public function saveEmailNotifications(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$type = $input['type'];
		$arr = array();

		foreach ($input as $key => $val) {
			if ($val == false) {
				$arr[] = $key;
			}
		}

		$snn = SettingNotificationName::on('rds1')
									   ->whereIn('name', $arr)
									   ->where('type', $type)
									   ->select('id')
									   ->get();

		// remove all the notification for this user_id and	update history
		$del = SettingNotificationLog::where("user_id", $data['user_id'])
									 ->where('type', $type)
									 ->delete();

		$update_history = SettingNotificationLogHistory::where("user_id", $data['user_id'])
									 				   ->where('type', $type)
									 				   ->update(array('removed_date' => Carbon::now()));

		// add the updated notification settings
		foreach ($snn as $key) {
		   	$attr = array('user_id' => $data['user_id'], 'snn_id' => $key->id);
		   	$val  = array('user_id' => $data['user_id'], 'snn_id' => $key->id, 'type' => $type);

		   	$response = SettingNotificationLog::updateOrCreate($attr, $val);

		   	$snlh = new SettingNotificationLogHistory;
		   	$snlh->user_id = $data['user_id'];
		   	$snlh->snn_id  = $key->id;
		   	$snlh->type    = $type;

		   	$snlh->save();
		}
		$snn2 = new SettingNotificationName;

		$ret = array();
		$ret['response'] = 'success';
		$ret['setting_notification'] = $snn2->getSettingNotifications();
		
		return $ret;
	}

	public function saveDataPreferences(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$sdpl 			  = new SettingDataPreferenceLog;
		$sdpl->user_id 	  = $data['user_id'];
		$sdpl->optin 	  = $input['data_preferences'];
		$sdpl->lcca 	  = $input['lcca'];
		$sdpl->st_patrick = $input['st_patrick'];
		$sdpl->lbsf 	  = $input['lbsf'];
		$sdpl->gisma 	  = $input['gisma'];
		$sdpl->aul 		  = $input['aul'];
		$sdpl->bsbi 	  = $input['bsbi'];
		$sdpl->tsom 	  = $input['tsom'];
		$sdpl->tlg 		  = $input['tlg'];
		$sdpl->ulaw 	  = $input['ulaw'];


		$sdpl->save();

		return "success";
	}

	public function savePhoneInfo(){
		$input = Request::all();

    	$user_id = Session::get('userinfo.id');
		$user = User::find($user_id);

		$user->phone = isset($input['formatted_phone']) ? $input['formatted_phone'] : null;
		$user->save();

		Session::put('userinfo.session_reset', 1);

		return 'success';
	}

	public function optInUserForText(){
		$user_id = Session::get('userinfo.id');
		$user = User::find($user_id);	

		$user->txt_opt_in = 1;
		$user->save();

		Session::put('userinfo.session_reset', 1);

		return 'success';
	}

	public function saveEditedPhoneNumber(){
		$input = Request::all();

		if( isset($input['phone']) && isset($input['dialing_code']) ){
    		$user_id = Session::get('userinfo.id');
    		$user = User::find($user_id);
    		$user->phone = '+'.$input['dialing_code'].' '.$input['phone'];
    		$user->save();

    		$ret = array();
		    $ret['response'] = 'success';
		    $ret['msg'] = 'Successfully saved new phone number.';
		    
			Session::put('userinfo.session_reset', 1);

		}else{
			$ret = array();
		    $ret['response'] = 'failed';
		    $ret['msg'] = 'No phone and/or dialing code input found.';
		}

		return $ret;
	}

	public function deleteUsersPhoneNumber(){
		$user_id = Session::get('userinfo.id');
		$user = User::find($user_id);	
		
		$user->txt_opt_in = 0;
		$user->verified_phone = 0;
		$user->phone = null;
		$user->save();

		Session::put('userinfo.session_reset', 1);

		return 'success';
	}
}
