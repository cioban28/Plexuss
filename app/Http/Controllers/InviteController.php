<?php

namespace App\Http\Controllers;

use Request, DB, Session, Queue, Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\User, App\UsersInvite;
use App\Http\Controllers\MandrillAutomationController;
use App\Console\Commands\SendInviteEmail;
use App\UsersSharedSignup, App\RoleBaseEmail;

class InviteController extends Controller
{

	/**
	 * get list of Google contacts
	 *
	 * @return void
	 */
	public function inviteWithGoogle() {

	    // get data from input
	    $code = Request::get( 'code' );

	    // get google service
	    $googleService = \OAuth::consumer( 'Google' );

	    //Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'home';

	    // check if code is valid

	    // if code is provided get user data and sign in
	    if ( !empty( $code ) ) {

	        // This was a callback request from google, get the token
	        $token = $googleService->requestAccessToken( $code );

	        // Send a request with it
	        //$endpoint = "https://www.google.com/m8/feeds/contacts/reza.shayesteh%40gmail.com/full?alt=json&start-index=51&max-results=1000";

	        $endpoint = "https://www.google.com/m8/feeds/contacts/default/full?alt=json&start-index=1&max-results=1000";
	        // $endpoint = 'https://www.googleapis.com/oauth2/v1/userinfo';
	        $result = json_decode( $googleService->request($endpoint), true );

	        $res = array();

	        $res['authorName'] = $result['feed']['author'][0]['name']['$t'];
	        $res['authorEmail'] = $result['feed']['author'][0]['email']['$t'];
	        $res['totalResults'] = $result['feed']['openSearch$totalResults']['$t'];

	        $res['startIndex'] = $result['feed']['openSearch$startIndex']['$t'];
	        $res['itemsPerPage'] = $result['feed']['openSearch$itemsPerPage']['$t'];

	        $contactList = array();

	        if (isset($result['feed']['entry'])) {
	        	$contactList = $result['feed']['entry'];
	        }

	        $contactRes = array();

	        foreach ($contactList as $key) {
	        	$tmp = array();

	        	if(isset($key['gd$email'][0]['address'])){
	        		$tmp['name'] = $key['title']['$t'];
	        		$tmp['email'] = $key['gd$email'][0]['address'];
	        		$contactRes[] = $tmp;
	        	}
	        	
	        }

	        $res['contactList'] = $contactRes;

			$res = json_encode($res);

	        return $res;

	        // REMOVED ON 3/19/19
	        // $data['contactList'] = $this->handleInviteContacts($res, 'Google');

	        //if coming from welcome page, save contact info in cache for use in invite modal on welcome page
	        // if( Cache::has('from_welcome_page') ){
	        // 	$return_to = Cache::get('from_welcome_page');
	        // 	Cache::put($data['user_id'].'userinfo_contact_list', $data['contactList'], 10);
	        // 	Cache::forget('from_welcome_page');
		       //  return redirect('/'.$return_to.'?param=invite');
	        // }else{
		       //  return redirect('/settings/invite');
	        // }

	        // REMOVED ON 3/19/19
	        // return redirect('/settings/invite');

	    }
	    // if not ask for permission first
	    else {
	        // get googleService authorization
			$url = $googleService->getAuthorizationUri();

			// return to google login url
	        return redirect( (string)$url );
	    }
	}

	public function inviteWithGoogleForSocialApp() {
	    // get data from input
	    $code = Request::get( 'code' );
	    // get google service
	    $googleService = \OAuth::consumer( 'Google' );

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$data['currentPage'] = 'home';
	    if ( !empty( $code ) ) {

	        // This was a callback request from google, get the token
	        $token = $googleService->requestAccessToken( $code );
	        $endpoint = "https://www.google.com/m8/feeds/contacts/default/full?alt=json&start-index=1&max-results=1000";
	        $result = json_decode( $googleService->request($endpoint), true );
	        $res = array();

	        $res['authorName'] = $result['feed']['author'][0]['name']['$t'];
	        $res['authorEmail'] = $result['feed']['author'][0]['email']['$t'];
	        $res['totalResults'] = $result['feed']['openSearch$totalResults']['$t'];

	        $res['startIndex'] = $result['feed']['openSearch$startIndex']['$t'];
	        $res['itemsPerPage'] = $result['feed']['openSearch$itemsPerPage']['$t'];

	        $contactList = array();

	        if (isset($result['feed']['entry'])) {
	        	$contactList = $result['feed']['entry'];
	        }

	        $contactRes = array();

	        foreach ($contactList as $key) {
	        	$tmp = array();

	        	if(isset($key['gd$email'][0]['address'])){
	        		$tmp['name'] = $key['title']['$t'];
	        		$tmp['email'] = $key['gd$email'][0]['address'];
	        		$contactRes[] = $tmp;
	        	}
	        	
			}
			$importContactCount = 0;
			if (!empty($contactRes)) {
				foreach ($contactRes as $contact) {
					$values = [];
					$values['user_id'] = $data['user_id'];
					$values['source'] = 'Google';
					$values['invite_name'] = isset($contact['name']) ? $contact['name'] : NULL;
					$values['invite_email'] = isset($contact['email']) ? $contact['email'] : NULL;
	
					$found = UsersInvite::where('invite_email', $values['invite_email'])->count();
					if($found == 0){
						UsersInvite::create($values);
						$importContactCount++;
					}
				}
			}
			return redirect('/social/networking/importContacts/'.$importContactCount);
	    }
	    else {
	        // get googleService authorization
	        $url = $googleService->getAuthorizationUri();
			// return to google login url
	        return redirect( (string)$url );
	    }
	}

	/**
	 * get list of yahoo contacts
	 *
	 * @return void
	 */
	public function inviteWithYahoo() {
		// get data from input
	    // $token = Request::get( 'oauth_token' );
	    // $verify = Request::get( 'oauth_verifier' );
	    $code = Request::get( 'code' );
	    $input = Request::all();
	    // get yahoo service
	    $yh = \OAuth::consumer( 'Yahoo' );

	    //Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
	    // if code is provided get user data and sign in
	    if ( !empty( $code ) ) {

            // This was a callback request from yahoo, get the token
            $token = $yh->requestAccessToken( $code );
            $xid = array($token->getExtraParams());
            $endpoint = 'https://social.yahooapis.com/v1/user/'.$xid[0]['xoauth_yahoo_guid'] .'/contacts?format=json';
            //$endpoint = 'https://social.yahooapis.com/v1/user/'.$xid[0]['xoauth_yahoo_guid'].'/profile?format=json' ;
            $result = json_decode( $yh->request($endpoint), true );   

            $res = array();

	        $res['authorName'] = '';
	        $res['authorEmail'] = '';
	        $res['totalResults'] = '';

	        $res['startIndex'] = '';
	        $res['itemsPerPage'] = '';

	        $contactList = array();

	        $contactList = $result['contacts']['contact'];


	        $contactRes = array();

	        //$cnt = 0;

	        foreach ($contactList as $key) {

	         $fields = $key['fields'];

	        	$tmp = array();

	        	$tmp['name'] ='';
	        	$tmp['email'] = '';
	        	
	        	foreach ($fields as $k) {
	        		
	        		if ($k['type'] == 'name') {
	        			if (isset($k['middleName']) && $k['middleName'] == '') {
	        				$tmp['name'] = $k['value']['givenName']. ' '. $k['value']['familyName'];
	        			}else{
	        				$tmp['name'] = $k['value']['givenName']. ' '. $k['value']['middleName']. ' '.$k['value']['familyName'];
	        			}
	        			
	        		}

	        		if ($k['type'] == 'email') {
	        			$tmp['email'] = $k['value'];

	        		}

	        	}

	        	if($tmp['email'] != ''){
	        		$contactRes[] = $tmp;
	        	}
	        	
	        }
	        $res['contactList'] = $contactRes;

	        
	        $res = json_encode($res);

	        $this->customdd($res);
	        exit();

	        $data['contactList'] = $this->handleInviteContacts($res, 'Yahoo');

	        //if coming from welcome page, save contact info in cache for use in invite modal on welcome page
	        // if( Cache::has('from_welcome_page') ){
	        // 	$return_to = Cache::get('from_welcome_page');
	        // 	Cache::put($data['user_id'].'userinfo_contact_list', $data['contactList'], 10);
	        // 	Cache::forget('from_welcome_page');
		       //  return redirect('/'.$return_to.'?param=invite');
	        // }else{
		       //  return redirect('/settings/invite');
	        // }
	        return redirect('/settings/invite');
	     
	    }
	    // if not ask for permission first
	    else {
	        $url = $yh->getAuthorizationUri();
			return redirect((string)$url);
	    }
	}

	/**
	 * get list of Microsoft contacts
	 *
	 * @return void
	 */
	public function inviteWithMicrosoft(){

	    // get data from input
	    $code = Request::get( 'code' );

	    // get google service
	    $microsoftService = \OAuth::consumer( 'Microsoft' );

	    //Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

	    // check if code is valid

	    // if code is provided get user data and sign in
	    if ( !empty( $code ) ) {

	        // This was a callback request from google, get the token
	        $token = $microsoftService->requestAccessToken( $code );

	        // Send a request with it
	        //$endpoint = "https://www.google.com/m8/feeds/contacts/reza.shayesteh%40gmail.com/full?alt=json&start-index=51&max-results=1000";	        

	        $endpoint = 'https://apis.live.net/v5.0/me/contacts';

	        $result = json_decode( $microsoftService->request($endpoint), true );


	        $res = array();

	        $res['authorName'] = $result['name'];


	        if ($result['emails']['preferred'] != '') {
	        	$res['authorEmail'] = $result['emails']['preferred'];
	        }else if ($result['emails']['account'] != '') {
	        	$res['authorEmail'] = $result['emails']['account'];
	        }else if ($result['emails']['personal'] != '') {
	        	$res['authorEmail'] = $result['emails']['personal'];
	        }else if ($result['emails']['business'] != '') {
	        	$res['authorEmail'] = $result['emails']['business'];
	        }


	        $res['totalResults'] = '';

	        $res['startIndex'] = '';
	        $res['itemsPerPage'] = '';

	        $contactList = array();

	        $endpoint = "https://apis.live.net/v5.0/me/contacts";
	        //$endpoint = 'https://apis.live.net/v5.0/me';

	        $result = json_decode( $microsoftService->request($endpoint), true );


	        $contactList = $result['data'];

	        $contactRes = array();

	        foreach ($contactList as $key) {
	        	$tmp = array();

	        	$tmp['name'] = '';
	        	$tmp['email'] = '';

	        	if ($key['emails']['preferred'] != '') {
	        		$tmp['name'] = $key['name'];
		        	$tmp['email'] = $key['emails']['preferred'];
		        }else if ($key['emails']['account'] != '') {
		        	$tmp['name'] = $key['name'];
		        	$tmp['email'] = $key['emails']['account'];
		        }else if ($key['emails']['personal'] != '') {
		        	$tmp['name'] = $key['name'];
		        	$tmp['email'] = $key['emails']['personal'];
		        }else if ($key['emails']['business'] != '') {
		        	$tmp['name'] = $key['name'];
		        	$tmp['email'] = $key['emails']['business'];
		        }else if ($key['emails']['other'] != '') {
		        	$tmp['name'] = $key['name'];
		        	$tmp['email'] = $key['emails']['other'];
		        }
		        if($tmp['name'] != '' && $tmp['email'] != ''){
		        	$contactRes[] = $tmp;
		        }
	        	
	        }

	        $res['contactList'] = $contactRes;
	        $res = json_encode($res);

	        $data['contactList'] = $this->handleInviteContacts($res, 'Microsoft');

	        //if coming from welcome page, save contact info in cache for use in invite modal on welcome page
	        // if( Cache::has('from_welcome_page') ){
	        // 	$return_to = Cache::get('from_welcome_page');
	        // 	Cache::put($data['user_id'].'userinfo_contact_list', $data['contactList'], 10);
	        // 	Cache::forget('from_welcome_page');
		       //  return redirect('/'.$return_to.'?param=invite');
	        // }else{
		       //  return redirect('/settings/invite');
	        // }
	        return redirect('/settings/invite');

	    }
	    // if not ask for permission first
	    else {
	        // get microsoftService authorization
	        $url = $microsoftService->getAuthorizationUri();

	        // return to google login url
	        return redirect( (string)$url );
	    }

	}

	/**
	 * get list of contacts from the email, or already imported list
	 *
	 * @return void
	 */
	private function handleInviteContacts($ret = null, $source = null){

		if($ret == null){
			return array();
		}
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$users_invites = UsersInvite::where('user_id', $data['user_id'])
						->where('sent', 0)
						->select('user_id','invite_email', 'invite_name', 'source',
						 'sent', 'created_at', 'updated_at')->get()->toArray();

		$ret = json_decode($ret);

		$ret = $ret->contactList;

		$insertArr = array();
		$rbe = new RoleBaseEmail;

		foreach ($ret as $key) {

			$tmp = array();

			if (!$this->in_array_multidimensional($key->email, $users_invites)) {
				$is_role_base = $rbe->isRoleBase($key->email);
				if ($is_role_base) {
					continue;
				}

				$is_dup = 0;
				$ui = UsersInvite::on('bk')->where('invite_email', $key->email)->first();
				if (isset($ui)) {
					$is_dup = 1;
				}

				$tmp['user_id'] = $data['user_id'];
				$tmp['invite_email'] = $key->email;
				$tmp['invite_name'] = $key->name;
				$tmp['source'] = $source;
				$tmp['sent'] = 0;
				$tmp['is_dup'] = $is_dup;
				$tmp['created_at'] = date("Y-m-d H:i:s");
				$tmp['updated_at'] = date("Y-m-d H:i:s");

				$insertArr[] = $tmp;

			}
		}

		if(isset($insertArr) && isset($insertArr[0]['invite_email'])){
			DB::table('users_invites')->insert($insertArr);
		}

		$insertArr = array_merge($users_invites, $insertArr);

		// sort the array alphabetically 
		$insertArr = $this->record_sort($insertArr, 'invite_name');

	    Session::put('invite_contactList', $insertArr);

		return $insertArr;
	}

    private function formatInviteContacts($ret = null, $source = null){
        if($ret == null){
            return array();
        }
        
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $ret = json_decode($ret);

        $ret = $ret->contactList;

        $insertArr = array();
        $rbe = new RoleBaseEmail;

        foreach ($ret as $key) {
        	$is_role_base = $rbe->isRoleBase($key->email);
			if ($is_role_base) {
				continue;
			}
			$is_dup = 0;
			$ui = UsersInvite::on('bk')->where('invite_email', $key->email)->first();
			if (isset($ui)) {
				$is_dup = 1;
			}
            $tmp = array();

            $tmp['user_id'] = $data['user_id'];
            $tmp['invite_email'] = $key->email;
            $tmp['invite_name'] = $key->name;
            $tmp['source'] = $source;
            $tmp['sent'] = 0;
            $tmp['is_dup'] = $is_dup;
            $tmp['created_at'] = date("Y-m-d H:i:s");
            $tmp['updated_at'] = date("Y-m-d H:i:s");

            $insertArr[] = $tmp;

        }

        if(isset($insertArr) && isset($insertArr[0]['invite_email'])){
            DB::table('users_invites')->insert($insertArr);
        }

        // sort the array alphabetically 
        $insertArr = $this->record_sort($insertArr, 'invite_name');

        return $insertArr;
    }

	/**
	 * send invite emails to mailchimp
	 *
	 * @return success/error
	 */
	public function sendInvites(){
		
		$reply_email = 'support@plexuss.com';

		$input = Request::all();
		//dd($input);
		$people = $input;
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		//$mc = new MailChimpModel('users_invites');

		$emails_to_update = array();
		$emails_to_error = array();

		$contactList = Session::get('invite_contactList');

		$last_sent_on = Carbon::now()->toDateTimeString();

		$error = false;
		$md = new MandrillAutomationController;
		// dd($people);
		foreach ($people as $key) {
			
			$params = array();

			$email_arr = array('email' => $key['invite_me_email'],
						   'name'  => '',
						   'type'  => 'to');

			$params['FNAME'] = ucwords(strtolower($data['fname']));
			$params['LNAME'] = ucwords(strtolower($data['lname']));

			$params['RNAME'] = ucwords(strtolower($key['invite_me_name']));

			if(isset($contactList)){
				$session_index_tobe_deleted = $this->get_index_multidimensional($contactList, 'invite_email', $key['invite_me_email']);

				if ($session_index_tobe_deleted !== false) {
					unset( $contactList[$session_index_tobe_deleted] );

					$contactList = array_values($contactList);
				}

				Session::put('invite_contactList', $contactList);
			}
			
			$this_error = false;				
			try{
				//$subscribe = $mc->subscribe($email, $params);
				$md->sendMandrillEmail('users_friend_invite', $email_arr, $params, $reply_email);
			}catch (Mandrill_Error $e) {
				//error
				// $error = $e->xdebug_message;
				//return "Error Occured!";
				$error = true;
				$this_error = true;
				$str = (String) $e;

				$update = UsersInvite::where('invite_email', $key['invite_me_email'])
										->where('user_id', $data['user_id'])
										->update(array('sent' => -1, 'error_log' => $str, 'last_sent_on' => $last_sent_on));

				$tmp = UsersInvite::whereIn('invite_email', $emails_to_update)->increment('count');
														
			}

			if (!$this_error) {
				$emails_to_update[] = $key['invite_me_email'];
			}
		}

		if (isset($emails_to_update)) {
			$update = UsersInvite::whereIn('invite_email', $emails_to_update)
								 ->update(array('sent' => 1, 'last_sent_on' => $last_sent_on));

			$tmp = UsersInvite::whereIn('invite_email', $emails_to_update)->increment('count');
		}

		/*
		if ($error) {
			return "Error Occured!";
		}
		*/
		return "success";

	}

	/**
	 * send a single invite to an email
	 *
	 * @return success/error
	 */
	public function sendSingleInvite(){
		
		$reply_email = 'support@plexuss.com';

		$input = Request::all();
		//dd($input);
		$people = $input;
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		// $mc = new MailChimpModel('users_invites');

		$emails_to_update = array();

		$last_sent_on = Carbon::now()->toDateTimeString();
			
		$params = array();

		$email_arr = array('email' => $people['invite_me_email'],
						   'name'  => '',
						   'type'  => 'to');

		$params['FNAME'] = ucwords(strtolower($data['fname']));
		$params['LNAME'] = ucwords(strtolower($data['lname']));

		$params['RNAME'] = '';

		$attr =  array('invite_email' =>  $people['invite_me_email'], 'user_id' => $data['user_id'] );
		$vals =  array('invite_name' => '', 'invite_email' => $people['invite_me_email'], 'user_id' => $data['user_id'],
		 'source' => 'Manual', 'sent' => 1, 'count' => 1, 'last_sent_on' => $last_sent_on);
		$updateScore = UsersInvite::updateOrCreate($attr, $vals);

		$md = new MandrillAutomationController;

		try{
			//$subscribe = $mc->subscribe($email, $params);
			$md->sendMandrillEmail('users_friend_invite', $email_arr, $params, $reply_email);
		}catch (Mandrill_Error $e) {
			$error = true;
			$this_error = true;
			$str = (String) $e;

			$update = UsersInvite::where('invite_email', $people['invite_me_email'])
									->where('user_id', $data['user_id'])
									->update(array('sent' => -1, 'error_log' => $str, 'last_sent_on' => $last_sent_on));
			
			$tmp = UsersInvite::whereIn('invite_email', $emails_to_update)->increment('count');

			return "Error Occured!";
		}
		return "success";

	}

	/**
	 * send invite emails to mailchimp
	 *
	 * @return success/error
	 */
	public function autoSendInvites(){

		//$mc = new MailChimpModel('users_invites');
		$reply_email = 'support@plexuss.com';

		$emails_to_update = array();
		$emails_to_error = array();

		$people = UsersInvite::on('bk')->where('users_invites.sent', 0)
						->join('users as u', 'u.id', '=', 'users_invites.user_id')
						->where('u.country_id', '!=', 1)
						->take(200)
						->orderBy('users_invites.id', 'DESC')
						->get();

		$error = false;

		// dd($people);
		$this_user_id = null;

		$last_sent_on = Carbon::now()->toDateTimeString();

		$md = new MandrillAutomationController;

		foreach ($people as $key) {
			
			$params = array();

			$email_arr = array('email' => $key->invite_email, 
						   'name'  => ucwords(strtolower($key->invite_name)),
						   'type'  => 'to');

			if (!isset($this_user_id) || $this_user_id != $key->user_id) {
				$this_user_id = $key->user_id;
				$user = User::on('bk')->find($key->user_id);
			}
			

			$params['FNAME'] = ucwords(strtolower($user->fname));
			$params['LNAME'] = ucwords(strtolower($user->lname));

			$params['RNAME'] = ucwords(strtolower($key->invite_name));

			$this_error = false;				
			try{
				//$subscribe = $mc->subscribe($email, $params);
				$md->sendMandrillEmail('users_friend_invite', $email_arr, $params, $reply_email);
			}catch (Mandrill_Error $e) {
				
				$error = true;
				$this_error = true;
				$str = (String) $e;

				$update = UsersInvite::where('invite_email', $key->invite_email)
										->where('user_id', $user->id)
										->update(array('sent' => -1, 'error_log' => $str, 'last_sent_on' => $last_sent_on));
										
				$tmp = UsersInvite::whereIn('invite_email', $emails_to_update)->increment('count');

			}

			if (!$this_error) {
				$emails_to_update[] = $key->invite_email;
			}
		}

		if (isset($emails_to_update)) {
			$update = UsersInvite::whereIn('invite_email', $emails_to_update)
								 ->update(array('sent' => 1, 'last_sent_on' => $last_sent_on));
			$tmp = UsersInvite::whereIn('invite_email', $emails_to_update)->increment('count');
		}

		return "success";
	}

	/**
	 * Follow up users invite emails
	 *
	 * @return success/error
	 */
	public function autoSendInvitesFollowUp($type){

		//$mc = new MailChimpModel('users_invites');
		$reply_email = 'support@plexuss.com';

		$emails_to_update = array();
		$emails_to_error = array();

		$from  = Carbon::today()->subMonths($type);  
		$to    = Carbon::today()->subMonths($type)->addDays(1);

		$from = $from->toDateTimeString();
		$to   = $to->toDateTimeString();

		$people = UsersInvite::on('rds1')->where('users_invites.count', $type)
									   ->where('last_sent_on', '>=', $from)
									   ->where('last_sent_on', '<=', $to)
									   ->whereNull('error_log')
									   ->take(200)
									   ->get();

		$error = false;

		// dd($people);
		$this_user_id = null;

		$last_sent_on = Carbon::now()->toDateTimeString();

		$md = new MandrillAutomationController;

		foreach ($people as $key) {
			$_user = User::where('email', $key->invite_email)->select('id')->first();

			if(isset($_user)){
				if (!isset($_user->utm_source)) {
					$_user->utm_source = 'email';
					$_user->utm_medium = 'cronJob';
					$_user->utm_content = 'email'.($type+1);
					$_user->utm_campaign = 'friend_invite';

					$_user->save();
				}
				continue;
			}

			$params = array();

			$email_arr = array('email' => $key->invite_email, 
						   'name'  => ucwords(strtolower($key->invite_name)),
						   'type'  => 'to');

			if (!isset($this_user_id) || $this_user_id != $key->user_id) {
				$this_user_id = $key->user_id;
				$user = User::on('bk')->find($key->user_id);
			}
			

			$params['FNAME'] = ucwords(strtolower($user->fname));
			$params['LNAME'] = ucwords(strtolower($user->lname));

			$params['RNAME'] = ucwords(strtolower($key->invite_name));

			if ($type == 1) {

				$college_arr = array();

				$college = College::on('rds1')->where('in_our_network', 1)
							  ->join('colleges_ranking as cr', 'cr.college_id', '=', 'colleges.id')
							  ->where('cr.plexuss', '<=', 100)
							  ->orderByRaw("RAND()")
							  ->select('colleges.id as college_id', 'colleges.school_name', 'colleges.city', 'colleges.state',
							  		   'colleges.logo_url', 'colleges.slug')
							  ->take(3)
							  ->get();
				$cnt = 1;
				foreach ($college as $k) {
					$college_arr[] = $k->college_id;

					$params['COLLEGE'.$cnt]     = $k->school_name; 
					$params['COLL'.$cnt.'LOC']  = $k->city .', '.$k->state;
					$params['COLL'.$cnt.'LOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'. $k->logo_url;
					$params['COLL'.$cnt.'LINK'] = 'https://plexuss.com/college/'.$k->slug;
					
					$cnt++;
				}


				$college = College::on('rds1')->where('in_our_network', 1)
								  ->whereNotIn('id', $college_arr)
								  ->orderByRaw("RAND()")
								  ->take(2)
								  ->get();
				$cnt = 4;
				foreach ($college as $k) {

					$params['COLLEGE'.$cnt]     = $k->school_name; 
					$params['COLL'.$cnt.'LOC']  = $k->city .', '.$k->state;
					$params['COLL'.$cnt.'LOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'. $k->logo_url;
					$params['COLL'.$cnt.'LINK'] = 'https://plexuss.com/college/'.$k->slug;
					
					$cnt++;
				}
			}
			
			$template_name = 'users_friend_invite_'.($type+1).'_mdl';
			$this_error = false;				

			try{
				//$subscribe = $mc->subscribe($email, $params);
				$md->sendMandrillEmail($template_name, $email_arr, $params, $reply_email);
			}catch (Mandrill_Error $e) {
				
				$error = true;
				$this_error = true;
				$str = (String) $e;

				$update = UsersInvite::where('invite_email', $key->invite_email)
										->where('user_id', $user->id)
										->update(array('sent' => -1, 'error_log' => $str, 'last_sent_on' => $last_sent_on));
										
				$tmp = UsersInvite::whereIn('invite_email', $emails_to_update)->increment('count');

			}

			if (!$this_error) {
				$emails_to_update[] = $key->invite_email;
			}
		}

		if (isset($emails_to_update) && !empty($emails_to_update)) {
			$update = UsersInvite::whereIn('invite_email', $emails_to_update)
								 ->update(array('sent' => 1, 'last_sent_on' => $last_sent_on));
			$tmp = UsersInvite::whereIn('invite_email', $emails_to_update)->increment('count');
		}else{
			return "fail";
		}

		return "success";
	}

    /**
     * get list of Google contacts or return url to login
     *
     * @return url string or array of contacts
     */
    public function getGoogleContacts() {

        // get data from input
        $code = Request::get( 'code' );

        // get google service
        $googleService = \OAuth::consumer( 'Google' );

        //Build to $data array to pass to view.
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['currentPage'] = 'email-contact-list';

        // check if code is valid

        // if code is provided get user data and sign in
        if ( !empty( $code ) ) {

            // This was a callback request from google, get the token
            $token = $googleService->requestAccessToken( $code );

            // Send a request with it
            //$endpoint = "https://www.google.com/m8/feeds/contacts/reza.shayesteh%40gmail.com/full?alt=json&start-index=51&max-results=1000";

            $endpoint = "https://www.google.com/m8/feeds/contacts/default/full?alt=json&start-index=1&max-results=1000";
            //$endpoint = 'https://www.googleapis.com/oauth2/v3/userinfo';
            $result = json_decode( $googleService->request($endpoint), true );

            $res = array();

            $res['authorName'] = $result['feed']['author'][0]['name']['$t'];
            $res['authorEmail'] = $result['feed']['author'][0]['email']['$t'];
            $res['totalResults'] = $result['feed']['openSearch$totalResults']['$t'];

            $res['startIndex'] = $result['feed']['openSearch$startIndex']['$t'];
            $res['itemsPerPage'] = $result['feed']['openSearch$itemsPerPage']['$t'];

            $contactList = array();

            if (isset($result['feed']['entry'])) {
                $contactList = $result['feed']['entry'];
            }

            $contactRes = array();

            foreach ($contactList as $key) {
                $tmp = array();

                if(isset($key['gd$email'][0]['address'])){
                    $tmp['name'] = $key['title']['$t'];
                    $tmp['email'] = $key['gd$email'][0]['address'];
                    $contactRes[] = $tmp;
                }
                
            }

            $res['contactList'] = $contactRes;

            $res = json_encode($res);

            $data['contactList'] = $this->formatInviteContacts($res, 'Google');

            return View( 'emailContactList.master', $data );
        }
        // if not ask for permission first
        else {
            // get googleService authorization
            $url = $googleService->getAuthorizationUri();

            $response = ['url' => (string) $url];

            // return to google login url
            return $response;
        }
    }

    /**
     * get list of Yahoo contacts or return url to login
     *
     * @return url string or array of contacts
     */
    public function getYahooContacts() {
        // get data from input
        $code = Request::get( 'code' );
        // get yahoo service
        $yh = \OAuth::consumer( 'Yahoo' );

        //Build to $data array to pass to view.
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['currentPage'] = 'email-contact-list';

        // if code is provided get user data and sign in
        if ( !empty( $code ) ) {
            // This was a callback request from yahoo, get the token
            $token = $yh->requestAccessToken( $code );
            $xid = array($token->getExtraParams());

            $endpoint = 'https://social.yahooapis.com/v1/user/'.$xid[0]['xoauth_yahoo_guid'] .'/contacts?format=json';
            //$endpoint = 'https://social.yahooapis.com/v1/user/'.$xid[0]['xoauth_yahoo_guid'].'/profile?format=json' ;
            $result = json_decode( $yh->request($endpoint), true );   
            
            dd($result);
            $res = array();

            $res['authorName'] = '';
            $res['authorEmail'] = '';
            $res['totalResults'] = '';

            $res['startIndex'] = '';
            $res['itemsPerPage'] = '';

            $contactList = array();

            $contactList = isset($result['contacts']['contact']) ? $result['contacts']['contact'] : [];

            $contactRes = array();

            //$cnt = 0;

            foreach ($contactList as $key) {

             $fields = $key['fields'];

                $tmp = array();

                $tmp['name'] ='';
                $tmp['email'] = '';
                
                foreach ($fields as $k) {
                    
                    if ($k['type'] == 'name') {
                        if (isset($k['middleName']) && $k['middleName'] == '') {
                            $tmp['name'] = $k['value']['givenName']. ' '. $k['value']['familyName'];
                        }else{
                            $tmp['name'] = $k['value']['givenName']. ' '. $k['value']['middleName']. ' '.$k['value']['familyName'];
                        }
                        
                    }

                    if ($k['type'] == 'email') {
                        $tmp['email'] = $k['value'];

                    }

                }

                if($tmp['email'] != ''){
                    $contactRes[] = $tmp;
                }
                
            }

            $res['contactList'] = $contactRes;

            $res = json_encode($res);

            $data['contactList'] = $this->formatInviteContacts($res, 'Yahoo');

            return View( 'emailContactList.master', $data );
         
        }
        // if not ask for permission first
        else {
            $url = $yh->getAuthorizationUri();

            $response = ['url' => (string) $url];

            // return to google login url
            return $response;
        }
    }

    /**
     * get list of Microsoft contacts or return url to login
     *
     * @return url string or array of contacts
     */
    public function getMicrosoftContacts(){

        // get data from input
        $code = Request::get( 'code' );

        // get google service
        $microsoftService = \OAuth::consumer( 'Microsoft' );

        //Build to $data array to pass to view.
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['currentPage'] = 'email-contact-list';

        // check if code is valid

        // if code is provided get user data and sign in
        if ( !empty( $code ) ) {

            // This was a callback request from google, get the token
            $token = $microsoftService->requestAccessToken( $code );

            // Send a request with it
            //$endpoint = "https://www.google.com/m8/feeds/contacts/reza.shayesteh%40gmail.com/full?alt=json&start-index=51&max-results=1000";          

            $endpoint = 'https://apis.live.net/v5.0/me';

            $result = json_decode( $microsoftService->request($endpoint), true );


            $res = array();

            $res['authorName'] = $result['name'];


            if ($result['emails']['preferred'] != '') {
                $res['authorEmail'] = $result['emails']['preferred'];
            }else if ($result['emails']['account'] != '') {
                $res['authorEmail'] = $result['emails']['account'];
            }else if ($result['emails']['personal'] != '') {
                $res['authorEmail'] = $result['emails']['personal'];
            }else if ($result['emails']['business'] != '') {
                $res['authorEmail'] = $result['emails']['business'];
            }


            $res['totalResults'] = '';

            $res['startIndex'] = '';
            $res['itemsPerPage'] = '';

            $contactList = array();

            $endpoint = "https://apis.live.net/v5.0/me/contacts";
            //$endpoint = 'https://apis.live.net/v5.0/me';

            $result = json_decode( $microsoftService->request($endpoint), true );


            $contactList = $result['data'];

            $contactRes = array();

            foreach ($contactList as $key) {
                $tmp = array();

                $tmp['name'] = '';
                $tmp['email'] = '';

                if ($key['emails']['preferred'] != '') {
                    $tmp['name'] = $key['name'];
                    $tmp['email'] = $key['emails']['preferred'];
                }else if ($key['emails']['account'] != '') {
                    $tmp['name'] = $key['name'];
                    $tmp['email'] = $key['emails']['account'];
                }else if ($key['emails']['personal'] != '') {
                    $tmp['name'] = $key['name'];
                    $tmp['email'] = $key['emails']['personal'];
                }else if ($key['emails']['business'] != '') {
                    $tmp['name'] = $key['name'];
                    $tmp['email'] = $key['emails']['business'];
                }else if ($key['emails']['other'] != '') {
                    $tmp['name'] = $key['name'];
                    $tmp['email'] = $key['emails']['other'];
                }
                if($tmp['name'] != '' && $tmp['email'] != ''){
                    $contactRes[] = $tmp;
                }
            }

            $res['contactList'] = $contactRes;

            $res = json_encode($res);

            $data['contactList'] = $this->formatInviteContacts($res, 'Microsoft');

            return View( 'emailContactList.master', $data );
        }
        // if not ask for permission first
        else {
            // get microsoftService authorization
            $url = $microsoftService->getAuthorizationUri();

            $response = ['url' => (string) $url];

            return $response;
        }

    }

    /**
     * send invites by placing invites in a queue
     *
     * @return success/error
     */
    public function sendReferralInvitesByQueue() {
        $reply_email = 'support@plexuss.com';

        $input = Request::all();
        //dd($input);
        $people = $input['people'];
        
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        UsersSharedSignup::insertShare($data['user_id'], substr($data['email_hashed'], 7), 'suggest_connections');

        foreach ($people as $person) {
            Queue::push(new SendInviteEmail($data, $person));
        }

        return 'success';
    }

    /**
     * send invites by 
     * 
     * @return success/error
     */ 
    public function sendReferralInvitesByQueueMobile() {
        $input = Request::all();
        $data = [];

        $people = $input['people'];

        $data['user_id'] = $input['user_id'];

        $user = User::select('email', 'fname', 'lname')->where('id', '=', $data['user_id'])->first();

        $data['email'] = $user->email;
        $data['fname'] = $user->fname;
        $data['lname'] = $user->fname;

        $data['email_hashed'] = Hash::make($data['email']);

        UsersSharedSignup::insertShare($data['user_id'], substr($data['email_hashed'], 7), 'suggest_connections');

        foreach ($people as $person) {
            Queue::push(new SendInviteEmail($data, $person));
        }

        return 'success';
    }

    /**
     * send a single invite to an email
     *
     * @return success/error
     */
    public static function sendSingleReferralInvite($data, $person) {
        
        $reply_email = 'support@plexuss.com';

        $emails_to_update = array();

        $last_sent_on = Carbon::now()->toDateTimeString();
            
        $params = array();

        $email_arr = array('email' => $person['invite_email'],
                           'name'  => '',
                           'type'  => 'to');

        $params['FNAME'] = ucwords(strtolower($data['fname']));
        $params['LNAME'] = ucwords(strtolower($data['lname']));
        $params['RNAME'] = ucwords(strtolower($person['invite_name']));
        $params['UTM_TERM'] = substr($data['email_hashed'], 7);

        $attr =  array('invite_email' =>  $person['invite_email'], 'user_id' => $data['user_id'] );
        $vals =  array('invite_name' => $person['invite_name'], 'invite_email' => $person['invite_email'], 'user_id' => $data['user_id'],
         'source' => 'Manual', 'sent' => 1, 'count' => 1, 'last_sent_on' => $last_sent_on);
        $updateScore = UsersInvite::updateOrCreate($attr, $vals);

        $md = new MandrillAutomationController;

        try{
            //$subscribe = $mc->subscribe($email, $params);
            $md->sendMandrillEmail('users_friend_invite', $email_arr, $params, $reply_email);
        }catch (Mandrill_Error $e) {
            $error = true;
            $this_error = true;
            $str = (String) $e;

            $update = UsersInvite::where('invite_email', $person['invite_email'])
                                    ->where('user_id', $data['user_id'])
                                    ->update(array('sent' => -1, 'error_log' => $str, 'last_sent_on' => $last_sent_on));
            
            $tmp = UsersInvite::whereIn('invite_email', $emails_to_update)->increment('count');

            return "Error Occured!";
        }
        return "success";

    }
}
