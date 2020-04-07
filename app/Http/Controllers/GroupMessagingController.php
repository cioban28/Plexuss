<?php

namespace App\Http\Controllers;

use Request, DB, Session;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use App\Organization, App\OrganizationBranch, App\OrganizationBranchPermission, App\PurchasedPhone, App\FreeTextCountry, App\User, App\Recruitment;
use App\CollegeMessageThreadMembers, App\AdminText, App\CollegeCampaign, App\CollegeCampaignTemplate, App\ZipCodes, App\CollegeMessageThreads;
use App\TextSuppressionList, App\ListUser, App\CollegeCampaignStudent, App\CollegeMessageLog;
use App\Http\Controllers\OmnipayController;

class GroupMessagingController extends Controller
{

	/**
	 * index
	 *
	 * Generates the campaign page index page.
	 *
	 * @return view
	 */
	public function index(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		if( $data['is_agency'] == 1 ){
			$type = 'agency';
		}else{
			$type = 'admin';

            $is_admin_premium = $this->validateAdminPremium();

            if (!$is_admin_premium) {
                return redirect( '/admin/premium-plan-request' );
            }
		}

		$data['title'] = 'Group Messaging';
		$data['currentPage'] = $type.'-groupmsg';
		$data['adminType'] = $type;

		//student list generated from approval page for group messaging
		$data['student_list'] = array();
		$data['student_count'] = 0;
		$data['campaign_count'] = 0;

		if( Cache::has(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList') ){
			$data['student_list'] = Cache::pull(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList');
			$data['student_count'] = count($data['student_list']);
			// var_dump($data['student_list']);
			// dd($data['student_count']);
		}

		// $user_id_arr = '';
		// foreach ($data['student_list'] as $key) {

		// 	if (isset($key['id'])) {
		// 		$user_id_arr .= Crypt::decrypt($key['id']).",";
		// 	}
		// 	if (isset($key['user_id'])) {
		// 		$user_id_arr .= Crypt::decrypt($key['user_id']).",";
		// 	}		

		// }

		// $data['recipients'] = array();

		// if ($user_id_arr !='') {
		// 	$tmp = $this->getRecipientName($data, $user_id_arr);

		// 	foreach ($tmp as $key) {
		// 		$arr = array();
		// 		$arr['user_id'] = Crypt::encrypt($key['user_id']);
		// 		$arr['fname'] = $key['fname'];
		// 		$arr['lname'] = $key['lname'];
		// 		$arr['json'] = json_encode($key);
		// 		$arr['json'] = str_replace("'", "&lsquo;", $key);
		// 		$arr['json'] = json_encode($arr['json']);

		// 		$data['recipients'][] = $arr;
		// 	}

		// }
		// generate list of campaigns
		$data = $this->generateListOfCampaigns($data, 0);

		//get templates
		$data = $this->getMessageTemplates($data);

		$org = new Organization;
		$org = $org->getThisOrgInfo($data['org_branch_id']);
		$this_org_user_ids = array();
		foreach ($org as $key) {
			$this_org_user_ids[] = $key->user_id;
		}

		$cmt = new CollegeMessageThreads;
		$data['total_num_campaign_messages']         = $cmt->getTotalNumOfCampaignMessages($data['tmp_campaign_ids'], $this_org_user_ids);
		$data['total_num_campaign_read_messages']    = $cmt->getTotalNumOfCampaignReadMessages($data['tmp_campaign_ids'], $this_org_user_ids);
		$data['total_num_campaign_replied_messages'] = $cmt->getTotalNumOfCampaignRepliedMessages($data['tmp_campaign_ids'], $this_org_user_ids);
		$data['read_response_rate']    = isset($data['total_num_campaign_messages']) && $data['total_num_campaign_messages'] > 0  ? number_format(($data['total_num_campaign_read_messages']/ $data['total_num_campaign_messages']) * 100, 2) : 0;
		$data['replied_response_rate'] = isset($data['total_num_campaign_messages']) && $data['total_num_campaign_messages'] > 0 ? number_format(($data['total_num_campaign_replied_messages']/ $data['total_num_campaign_messages']) * 100, 2) : 0;

		$data['total_num_campaign_messages']         = number_format($data['total_num_campaign_messages']);
		$data['total_num_campaign_read_messages']    = number_format($data['total_num_campaign_read_messages']);
		$data['total_num_campaign_replied_messages'] = number_format($data['total_num_campaign_replied_messages']);
		
		$ob = OrganizationBranch::on('rds1')->where('id', $data['org_branch_id'])
											->select('pending_auto_campaign', 'handshake_auto_campaign')
											->first();

		$data['pending_auto_campaign']   = $ob->pending_auto_campaign == 1 ? true : false;
		$data['handshake_auto_campaign'] = $ob->handshake_auto_campaign == 1 ? true : false;


		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
		// dd($data['campaigns']);

		return View('groupMessaging.index', $data);
	}

	/*
		* Text Message Index
		* return view
	*/
	public function textmsgIndex() {

		$viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        Session::forget('text_message_campaign_users');

        if( isset($data['profile_img_loc']) ){
            $data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
        }

        if( $data['is_agency'] == 1 ){
            $type = 'agency';
        }else{
            $type = 'admin';

            $is_admin_premium = $this->validateAdminPremium();

            if (!$is_admin_premium) {
                return redirect( '/admin/premium-plan-request' );
            }
        }

        $data['title'] = 'Text Messaging';
        $data['currentPage'] = $type.'-textmsg';
        $data['adminType'] = $type;

        //student list generated from approval page for group messaging
		$data['student_list'] = array();
		$data['student_count'] = 0;

		if( Cache::has(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList') ){
			$data['student_list'] = Cache::pull(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList');
			$data['student_count'] = count($data['student_list']);
		}

        $data = $this->generateListOfCampaigns($data, 1, NULL, true);

        $data = $this->getMessageTemplates($data);

        //get states
        
        if (Cache::has(env('ENVIRONMENT') .'_all_states_with_abbr')) {
            $states = Cache::get(env('ENVIRONMENT') .'_all_states_with_abbr');

        }else{

            $zc = new ZipCodes;
       		$states = $zc->getAllUsStateAbbreviation();
            Cache::put(env('ENVIRONMENT') .'_all_states_with_abbr', $states, 120);
        }

        $data['states'] = $states;

        $data['txt_first_time'] = true;

        $pp = PurchasedPhone::on('rds1')->where('org_branch_id', $data['org_branch_id'])->first();

        if (isset($pp) && !empty($pp)) {
        	$data['txt_first_time'] = false;
        	$data['purchased_phone'] = $pp->phone;
        }

        $at = AdminText::where('org_branch_id', $data['org_branch_id'])->first();

        if(isset($at) && !empty($at)){
        	$data['num_of_free_texts'] = $at->num_of_free_texts;
        	$data['num_of_eligble_texts'] = $at->num_of_eligble_texts;
        	$data['textmsg_tier'] = $at->tier;
        	$data['flat_fee_sub_tier'] = $at->flat_fee_sub_tier;
        	$data['textmsg_expires_date'] = Carbon::parse($at->expires_at);
        	$data['auto_renew'] = $at->auto_renew;
        }
        
        $data['current_time'] = Carbon::now();

        $data['free_text_countries'] = FreeTextCountry::select('country_code', 'country_name')->get();
        // dd($data['free_text_countries']);

        return View('groupMessaging.index', $data);
	}

	/*
	 * viewCampaign
	 *
	 * View the campaign based on campaign_id passed to this method
	 *
	 * @return json
	 */
	public function viewCampaign(){
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$campaign_id = Crypt::decrypt($input['id']);
		$ret = array();
		$student_user_ids = '';

		if(isset($input['currentPage']) && $input['currentPage'] == 'admin-textmsg') {
			$ret = $this->generateListOfCampaigns($data, 1 ,$campaign_id);
		} else {
			$ret = $this->generateListOfCampaigns($data, 0 ,$campaign_id);
		}

		if ($campaign_id == -1 || $campaign_id == -2) {
			$is_for_approved = $campaign_id == -1 ? true : false;
			$user_recruit_status = $is_for_approved ? 1 : 0;

			$ret['c_name'] = $is_for_approved ? 'Handshakes' : 'Pendings';
			$ret['c_subject'] = 'N/A';
			$ret['c_body'] = 'N/A';
			$ret['last_sent_on'] = '';
			$ret['is_text'] = 0;
		}else{
			$cc = CollegeCampaign::find($campaign_id);	
			$ret['c_name'] = $cc->name;
			$ret['c_subject'] = $cc->subject;
			$ret['c_body'] = $cc->body;
			$ret['last_sent_on'] = $cc->last_sent_on;
			$ret['recipients'] = $cc->num_of_student_user_ids;
			$ret['is_text'] = $cc->is_text_msg;

			if (isset($cc->scheduled_at)) {
				$scheduled_at = Carbon::parse($cc->scheduled_at);

				$ret['date'] = $scheduled_at->format('m/d/Y');
				$ret['hr'] = $scheduled_at->format('H');
				if ($ret['hr'] > 12) {
					$ret['period'] = 'pm';
					$ret['hr'] = $ret['hr'] - 12;
				}else{
					$ret['period'] = 'am';
				}
				$ret['min'] = $scheduled_at->format('i');
			}
		}


		$org = new Organization;
		$org = $org->getThisOrgInfo($data['org_branch_id']);
		$this_org_user_ids = array();
		foreach ($org as $key) {
			$this_org_user_ids[] = $key->user_id;
		}

		$cmt = new CollegeMessageThreads;
		$ret['total_num_campaign_messages']         = $cmt->getTotalNumOfCampaignMessages(array($campaign_id), $this_org_user_ids);
		$ret['total_num_campaign_read_messages']    = $cmt->getTotalNumOfCampaignReadMessages(array($campaign_id), $this_org_user_ids);
		$ret['total_num_campaign_replied_messages'] = $cmt->getTotalNumOfCampaignRepliedMessages(array($campaign_id), $this_org_user_ids);
		$ret['read_response_rate']    = isset($ret['total_num_campaign_messages']) && $ret['total_num_campaign_messages'] > 0  ? number_format(($ret['total_num_campaign_read_messages']/ $ret['total_num_campaign_messages']) * 100, 2) : 0;
		$ret['replied_response_rate'] = isset($ret['total_num_campaign_messages']) && $ret['total_num_campaign_messages'] > 0 ? number_format(($ret['total_num_campaign_replied_messages']/ $ret['total_num_campaign_messages']) * 100, 2) : 0;

		$ret['total_num_campaign_messages']         = number_format($ret['total_num_campaign_messages']);
		$ret['total_num_campaign_read_messages']    = number_format($ret['total_num_campaign_read_messages']);
		$ret['total_num_campaign_replied_messages'] = number_format($ret['total_num_campaign_replied_messages']);

		//Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList', $tmp_arr, 60);

		return json_encode($ret);
	}

	/**
	 * textmsgSummary
	 * 
	 * get msg cost summary
	 * 
	 * @return json
	 */
	public function textmsgSummary() {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$student_chosen = array();
		$campaign_chosen = array();
		$campaign_excludes_stu = array();

		// dd($input);
		$student_user_ids = isset($input['selected_students_ids'])? $input['selected_students_ids'] : '';
		if($student_user_ids != '') {
			$student_user_ids_arr = explode(',' , $student_user_ids);
			$student_user_ids_arr = array_filter($student_user_ids_arr);

			$student_chosen = array_map(function($elem) {
				return Crypt::decrypt($elem);
			}, $student_user_ids_arr);
		}
		// dd($input['campaign_chosen']);
		// $input['campaign_excludes_stu'];
		if(isset($input['campaign_chosen'])) {
			$campaign_chosen = array_map(function($elem){
				if($elem != 'selected_from_manage_students') 
					return Crypt::decrypt($elem);
			}, $input['campaign_chosen']);

			$campaign_chosen = array_filter($campaign_chosen);
		}

		if(isset($input['campaign_excludes_stu'])) {
			$campaign_excludes_stu = array_map(function($elem){
				return Crypt::decrypt($elem);
			}, $input['campaign_excludes_stu']);

			$campaign_excludes_stu = array_filter($campaign_excludes_stu);
		}
		// echo '<pre>';
		// print_r($student_chosen);
		// echo '</pre>';
		// dd($campaign_chosen);
		// dd($campaign_excludes_stu);

		// now calc twillio cost
		$res = $this->getTextMsgCost($campaign_chosen, $campaign_excludes_stu ,$student_chosen);
		// dd($res);
		return $res;
	}

	/**
	 * get text message cost summary
	 */
	public function getTextMsgCost($campaign_chosen = null, $campaign_excludes_stu = null, $student_chosen = null) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$twillioCost = array();

		$student_user_ids_arr = $this->getRecipientInfo($campaign_chosen, $campaign_excludes_stu, true);
		foreach ($student_chosen as $student_chosen_id) {
			$student_user_ids_arr[] = $student_chosen_id;
		}

		$student_user_ids_arr = array_unique($student_user_ids_arr);

		if(empty($student_user_ids_arr)) {
			return $twillioCost;
		}

		// dd($student_user_ids_arr);
		// calc pricing base on total students ids
		// 2 problems here : 1. country maybe null, 2. whereIn cost more processing time.
		$is_list_user = Cache::get(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user');

		if (isset($is_list_user) && $is_list_user == 1) {
			
			$twillioCostList = ListUser::on('rds1')->select(DB::raw('count(*) as user_count, group_concat(phone) as phone_list'), 'co.country_code', 'co.country_name', 'co.id as country_id')
										   ->leftjoin('countries as co', 'co.country_name', '=' , 'list_users.country') 
                                           ->where(function($query) use ($student_user_ids_arr){
                                           		foreach ($student_user_ids_arr as $key => $value) {
                                           			if (!empty($value)) {
														$query = $query->orWhere('list_users.id', '=', $value);
													}
												}
                                           	})
                                           ->groupBy('country_id')
                                           ->get();
		}else{

			$twillioCostList = User::on('rds1')->select(DB::raw('count(*) as user_count, country_id, group_concat(phone) as phone_list'), 'co.country_code', 'co.country_name')
										   ->leftjoin('countries as co', 'co.id', '=' , 'country_id') 
                                           ->where(function($query) use ($student_user_ids_arr){
                                           		foreach ($student_user_ids_arr as $key => $value) {
                                           			if (!empty($value)) {
														$query = $query->orWhere('users.id', '=', $value);
													}
												}
                                           	})
                                           ->groupBy('country_id')
                                           ->get();
		}
		
        // dd($twillioCostList);

        
        foreach ($twillioCostList as $twillioCostPerCountry) {
            $temp = array();
            $temp['country_id'] = $twillioCostPerCountry->country_id;
            $temp['country_name'] = $twillioCostPerCountry->country_name;
            $temp['user_count'] = $twillioCostPerCountry->user_count;
            $temp['country_code'] = $twillioCostPerCountry->country_code;
            $temp['phone_list'] = $twillioCostPerCountry->phone_list;
            // .. add other infomation from twillio api here

            $twillioCost[] = $temp;
        }
        // dd($twillioCost);

        return $twillioCost;
	}

	/**
	 * setGroupMsg
	 *
	 * Set the cache for student list of this user_id
	 *
	 * @return string
	 */
	public function setGroupMsg(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$input = Request::all();
		Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList', $input, 60);
		Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user', 0, 60);
		return 'success';
	}

	/**
	 * setGroupMsgForText
	 *
	 * Set the cache for student list of this user_id
	 *
	 * @return string
	 */
	public function setGroupMsgForText($input = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (!isset($input)) {
			$input = Request::all();
			foreach ($input as $key => $val) {

				$user = User::on('bk')->find(Crypt::decrypt($val['id']));					  
				
				if (isset($user) && $user->txt_opt_in == 0) {
					unset($input[$key]);
				}elseif(isset($user)){
					$phone = preg_replace("/[^0-9]/", "", $user->phone);
					$tsl = TextSuppressionList::on('bk')->where('phone', 'LIKE', '%'.$phone)->first();
				
					if (isset($tsl)) {
						unset($input[$key]);
					}
				}else{
					unset($input[$key]);
				}
			}
			
			$input = array_values($input);

			Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user', 0, 60);

		}else{
			Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user', 1, 60);
		}
		
		if (isset($input) && !empty($input)) {
			Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList', $input, 60);
			return 'success';

		}else{
			return 'failed';
		}
		
	}

	/**
	 * removeStudentFromList
	 *
	 * Remove a user_id from studentList cache
	 *
	 * @return string
	 */
	public function removeStudentFromList(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();	

		if (!isset($input['user_id']) || empty($input['user_id'])) {
			return 'failed';
		}

		$cache = Cache::get(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList');

		$cnt = 0;
		foreach ($cache as $key) {
			if ($key['user_id'] == $input['user_id']) {
				unset($cache[$cnt]);
				break;
			}
			$cnt++;
		}

		Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList', $cache, 60);

		return 'success';
	}

	/**
	 * saveCampaign
	 *
	 * Remove a user_id from studentList cache
	 *
	 * @return string
	 */
	public function saveCampaign($input = null, $data = null){

		if (!isset($data)) {
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$is_automatic_campaign = false;
		}else{
			$is_automatic_campaign = true;
		}

		if (!isset($input)) {
			$input = Request::all();
		}

		// $input['recipients'] is not equal to $input['validcnt']

		// Is campaign_id available?
		if (isset($input['id'])) {
			Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_newCampaignId', Crypt::decrypt($input['id']), 600);
		}

		$c_name = isset($input['c_name']) ? $input['c_name'] : '';
		$c_subject = isset($input['c_subject']) ? $input['c_subject'] : '';
		$c_body = isset($input['c_body']) ? $input['c_body'] : '';
		$recipients = isset($input['recipients']) ? $input['recipients'] : 0;
		$save = isset($input['save']) ? $input['save'] : false;
		$send = isset($input['send']) ? $input['send'] : false;
		$schedule_later = isset($input['schedule_later']) ? $input['schedule_later'] : false;
		$date = isset($input['date']) ? $input['date'] : '';
		$hr = isset($input['hr']) ? $input['hr'] : '';
		$min = isset($input['min']) ? $input['min'] : '';
		$period = isset($input['period']) ? $input['period'] : '';
		$is_default = isset($input['is_default']) ? $input['is_default'] : '';

		// check if content contains img
		$is_mms = 0;
		$c_body_img = array();
    	preg_match( '/src="([^"]*)"/i', $c_body, $c_body_img ) ;
    	if(count($c_body_img) > 0) {
    		$is_mms = 1;
    	}

    	// check if current user is free trial or not
    	if(isset($input['currentPage']) && $input['currentPage'] == 'admin-textmsg' ){
    		$at = AdminText::where('org_branch_id', $data['org_branch_id'])->first();

	        if( $is_mms == 1 && isset($at) && !empty($at) && $at->tier == 'free') {
	        	return 'failed';
	        }
    	}
        

		$agency_id  = null;
		$college_id = null;
		$scheduled_at = null;

		$data['campaign_name'] = $c_name;
		$data['campaign_subject'] = $c_subject;
		$data['campaign_body'] = $c_body;

		if (isset($data['agency_collection'])) {
			$agency_id = $data['agency_collection']->agency_id;
		}else{
			$college_id = $data['org_school_id'];
		}

		if (isset($schedule_later) && $schedule_later == 'true' && !empty($input['date'])) {

			$this_date = explode("/", $date);
			$date = $this_date[2] ."-".$this_date[0]."-".$this_date[1];

			if($period == 'pm'){
				$hr = $hr + 12;
			}
			if (strlen($hr) == 1) {
				$hr = "0".$hr;
			}

			$date .= " ".$hr.":".$min.":00";

			$scheduled_at = $date;
		} else {
			$scheduled_at = null;
		}	

		$student_user_ids = '';

		if (!isset($is_automatic_campaign)) {
			// dd('I am not automatic campaign');
			if(isset($input['selected_students_id']) && !empty($input['selected_students_id'])){
				$student_user_ids_arr = explode(',', $input['selected_students_id']);
				foreach ($student_user_ids_arr as $key) {
					if(!empty($key)){
						$student_user_ids = $student_user_ids.Crypt::decrypt($key).",";
					}
				}
			}
		}else{
			// dd(empty($input['selected_students_id']));
			if (isset($input['selected_students_id']) && !empty($input['selected_students_id']) && strpos($input['selected_students_id'], ',') !== FALSE){
				$student_user_ids_arr = explode(',', $input['selected_students_id']);
				foreach ($student_user_ids_arr as $key) {
					if(!empty($key) && !is_numeric($key)){
						$student_user_ids = $student_user_ids.Crypt::decrypt($key).",";
					}elseif (!empty($key)) {
						$student_user_ids = $student_user_ids.$key.",";
					}
				}
			}else{
				if (isset($input['selected_students_id']) && !empty($input['selected_students_id']) && !is_numeric($input['selected_students_id'])) {
					$student_user_ids = Crypt::decrypt($input['selected_students_id']).',';
				}else if(isset($input['selected_students_id']) && !empty($input['selected_students_id'])) {
					$student_user_ids = $input['selected_students_id'].',';
				}
			}
		}

		//dd($student_user_ids); //string '349089,347688,721,23939,225338,40160,12380,23117,' (length=49)

		$campaign_chosen = array();
		$campaign_excludes_stu = array();

		foreach ($input as $key => $value) {
			// extract properties begin with "campaign_shown_" => indicates which campaign you choose 

			if (strpos($key, 'campaign_shown_') !== FALSE){
				array_push($campaign_chosen, Crypt::decrypt($value));
			}
			// extract properties begin with "excludes_check_" => indicates which campaign you want to exclude students messaged before
			else if(strpos($key, 'excludes_check_') !== FALSE){
				array_push($campaign_excludes_stu, Crypt::decrypt($value));
			}
		}

		$student_user_ids_arr = $this->getRecipientInfo($campaign_chosen, $campaign_excludes_stu);
		//dd($student_user_ids_arr);
		
		$student_user_ids_temp = explode(',', $student_user_ids);
		foreach ($student_user_ids_temp as $value) {
			if(isset($value) && !empty($value))
				$student_user_ids_arr[] = $value;
		}
		//dd($student_user_ids_arr);// return array('0'=>'2159', '1' => '2880', ...) 

		$student_user_ids_arr = array_unique($student_user_ids_arr);

		$student_user_ids_tmp = array();
		if (isset($student_user_ids) && !empty($student_user_ids)) {
			$student_user_ids_tmp = explode(",", $student_user_ids);
		}

		$student_user_ids = array_unique(array_merge($student_user_ids_arr, $student_user_ids_tmp));
		$student_user_ids = array_filter($student_user_ids, function($elem) { return !empty(trim($elem)); });

		// These are the two lines we tried before. instead of above two lines of code. if error happens look into these.
 		//$student_user_ids = array_values(array_unique(array_merge($student_user_ids_arr, $student_user_ids_tmp)));
		// if (isset($student_user_ids[count($student_user_ids) - 1]) && $student_user_ids[count($student_user_ids) - 1] == '') {
		// 	unset($student_user_ids[count($student_user_ids) - 1]);
		// }

		$recipients = count($student_user_ids); // count total number

		$org_student_user_ids = implode(',', $student_user_ids); // restore total student ids

		$ready_to_send = 0;

		if (isset($send) && $send == 'true' && ($schedule_later == 'false' || $schedule_later == false)) {
			//$campaign_id = $cc->id;
			//$this->sendCampaignMessage($data, $campaign_id, $student_user_ids, $campaign_chosen, $campaign_excludes_stu);	
			$ready_to_send = 1;
		}

		$is_text = 0;

		if(isset($input['currentPage']) && $input['currentPage'] == 'admin-textmsg') {
			$is_text = 1;
		}
		
		$is_list_user = Cache::get(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user');

		if (Cache::has(env('ENVIRONMENT').'_'.$data['user_id'].'_newCampaignId')) {
			$attr = array('id' => Cache::get(env('ENVIRONMENT').'_'.$data['user_id'].'_newCampaignId'));
		}else{
			$attr = array('name' => $c_name, 'subject' => $c_subject, 'college_id' => $college_id);
		}
		$val  = array('name' => $c_name, 'subject' => $c_subject, 'body' => $c_body, 'student_user_ids' => $org_student_user_ids,
					  'num_of_student_user_ids' => $recipients, 'college_id' => $college_id, 'sender_user_id' => $data['user_id'],
					  'agency_id' => $agency_id, 'scheduled_at' => $scheduled_at, 'last_sent_on' => NULL, 'ready_to_send' => $ready_to_send, 
					  'is_text_msg' => $is_text, 'is_list_user' => $is_list_user);

		$cc = CollegeCampaign::updateOrCreate($attr, $val);

		$college_campaign_student_data = array();
		foreach ($student_user_ids as $key => $value) {
			$tmp = array();

			$tmp['user_id'] 	= $value;
			$tmp['campaign_id'] = $cc->id;
			$tmp['sent']        = 0;
			$tmp['created_at']  = Carbon::now();
			$tmp['updated_at']  = Carbon::now();

			$college_campaign_student_data[] = $tmp;
		}
		DB::table('college_campaign_students')->insert($college_campaign_student_data);

		$this->forgetCache($data);

		// if(isset($input['currentPage']) && $input['currentPage'] == 'admin-textmsg' && $ready_to_send == 1 &&
		//    isset($is_text) && $is_text == 1) {

		// 	$data['college_id']       = $cc->college_id;
		// 	$data['campaign_id']      = $cc->id;
		// 	$data['campaign_subject'] = $c_subject;
		// 	$data['campaign_body']    = $c_body;
			
		// 	$tw = new TwilioController;
		// 	$tw->sendSms($data);

		// }
		
		return "success";
	}

	public function forgetCache($data){
		Cache::forget(env('ENVIRONMENT').'_'.$data['user_id'].'_studentList');
		Cache::forget(env('ENVIRONMENT').'_'.$data['user_id'].'_newCampaignId');
	}
	/**
	 * createNewCampaign
	 *
	 * Create a new campign in the db, and place the campaign id in cache. 
	 *
	 * @return NULL
	 */
	public function createNewCampaign(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$cc = new CollegeCampaign;
		$cc->save();

		Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_newCampaignId', $cc->id, 600);
	}


	/**
	 * get RecipientInfo
	 * 
	 * @return json
	 */
	public function getRecipientInfo($campaign_chosen = null, $campaign_excludes_stu = null, $txtmsg_page = NULL) {
		$rec  = new Recruitment;
		$cmtm = new CollegeMessageThreadMembers;
		$student_user_ids = '';
		$temp = array();
		
		$suppression_user_ids = $cmtm->getUsersAlreadyMessaged();
		$suppression_user_ids = $suppression_user_ids->users;
		$suppression_user_ids_array = explode(',', $suppression_user_ids);
		
		if(!isset($campaign_chosen) or empty($campaign_chosen))
			return $temp;

		foreach ($campaign_chosen as $key => $value) {
			// if campaign is the approved one
			if ($value == -1) {
				if (in_array($value, $campaign_excludes_stu)) {
					$student_user_ids .= $rec->getRecruitmentDistinctUsers('approved', $suppression_user_ids_array, NULL, $txtmsg_page);
				}else{
					$student_user_ids .= $rec->getRecruitmentDistinctUsers('approved');
				}
				$student_user_ids .= ',';
			
			}elseif ($value == -2) {  //if campaign is the pending one
				if (in_array($value, $campaign_excludes_stu)) {
					$student_user_ids .= $rec->getRecruitmentDistinctUsers('pending', $suppression_user_ids_array, NULL, $txtmsg_page);
				}else{
					$student_user_ids .= $rec->getRecruitmentDistinctUsers('pending');
				}
				$student_user_ids .= ',';
			}else if($value){
				//if it's an actual campaign

				//get the user ids for this campaign
				$cc   = CollegeCampaign::on('rds1')->find($value);
				if (empty($student_user_ids)) {
					$student_user_ids = $cc->student_user_ids;
				}else{
					$tmp_student_user_ids = $cc->student_user_ids;
					$tmp_student_user_ids = explode(',', $tmp_student_user_ids);

					// get the current students in an array
					$student_user_ids_arr = explode(",", $student_user_ids);

					// subtract this campaign user ids that already exists in student_user_ids
					$tmp_student_user_ids = array_diff($tmp_student_user_ids, $student_user_ids_arr);
					// subtract this campaign user ids that are in suppression list
					$tmp_student_user_ids = array_diff($tmp_student_user_ids, $suppression_user_ids_array);
					$student_user_ids .= implode(",", $tmp_student_user_ids);
				}
			}
		}

		$student_user_ids_arr = explode(",", $student_user_ids);
		// count for student_user_id is not null or empty
		$student_user_ids_arr = array_filter($student_user_ids_arr, function($elem) { return !empty(trim($elem)); });
		$student_user_ids_arr = array_unique($student_user_ids_arr);

		$ret = array();
		$ret = $student_user_ids_arr;

		// Text Suppresssion 
		if (isset($txtmsg_page)) {	
			foreach ($student_user_ids_arr as $key => $value) {
				$user = User::on('bk')->find($value);

				if (!isset($user)) {
					unset($ret[$key]);
				}else{
					$phone = preg_replace("/[^0-9]/", "", $user->phone);
					$tsl = TextSuppressionList::on('bk')->where('phone', 'LIKE', '%'.$phone)->first();

					if (isset($tsl)) {
						unset($ret[$key]);
					}
				}
			}
			
			$ret = array_values($ret);
		}
		
		return $ret;
	}

	/**
	 * getRecipientName
	 *
	 * Get Users' information and get a json return back with all the info
	 * @param data
	 * @param user_id_arr
	 * @return array
	 */
	private function getRecipientName($data, $user_id_arr){
		
		$user_id_arr = explode(",", $user_id_arr);

		if (empty($user_id_arr[count($user_id_arr) -1])) {
			unset($user_id_arr[count($user_id_arr) - 1]);
		}
		
		if (isset($data['agency_collection'])) {
			$rec = 'agency_recruitment as r';
		}else{
			$rec = 'recruitment as r';
		}

		$ret = array();

		$users = DB::connection('bk')->table('users as u')
				    ->leftjoin('scores as s', 'u.id', '=', 's.user_id')
				    ->leftjoin($rec, 'u.id', '=', 'r.user_id')
					->leftjoin('colleges as c', 'c.id','=', 'u.current_school_id')
					->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')
					->leftjoin('objectives as o', 'o.user_id', '=', 'r.user_id')
					->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
					->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
					->leftjoin('professions as p', 'p.id', '=', 'o.profession_id')
					->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
				    ->select('u.id as user_id', 'u.fname', 'u.lname', 'u.current_school_id', 'u.in_college', 'u.id as user_id',
				    		'u.hs_grad_year', 'u.college_grad_year', 'u.profile_img_loc', 'u.financial_firstyr_affordibility',
				    		'u.profile_percent',
							's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.toefl_total', 's.ielts_total',
							'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
							'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
							'co.country_code', 'co.country_name',
							'dt.display_name as degree_name',
							'm.name as major_name',
							'p.profession_name')

				  	->whereIn('u.id', $user_id_arr)
				  	->groupBy('u.id')
				    ->get();

		foreach ($users as $key) {
			$arr = array();

			$arr['user_id'] = Crypt::encrypt($key->user_id);
			$arr['fname']   = ucfirst(strtolower($key->fname));
			$arr['lname']   = ucfirst(strtolower($key->lname));

			$src="/images/profile/default.png";
			if($key->profile_img_loc !=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$key->profile_img_loc;
			}
			$arr['profile_img_loc'] = $src;

			if($key->in_college){
				
				if(isset($key->overall_gpa)){
					$arr['gpa'] = $key->overall_gpa;
				}else{
					$arr['gpa'] = 'N/A';
				}

				if(isset($key->collegeName)){
					$arr['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->collegeName)))); 

					$arr['school_city'] = $key->collegeCity;
					$arr['school_state'] = $key->collegeState;
					if ($arr['current_school'] == "Home Schooled") {
						$arr['address'] = $arr['current_school'];
					}else{
						$arr['address'] = $arr['current_school'].', '.$arr['school_city']. ', '.$arr['school_state'];
					}
					
				}else{
					$arr['current_school'] = 'N/A';
					$arr['school_city'] = 'N/A';
					$arr['school_state'] = 'N/A';

					$arr['address'] = 'N/A';
				}

			}else{

				if(isset($key->hs_gpa)){
					$arr['gpa'] = $key->hs_gpa;
				}else{
					$arr['gpa'] = 'N/A';
				}
				

				if(isset($key->hsName)){
					$arr['current_school'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($key->hsName)))); 
					$arr['school_city'] = $key->hsCity;
					$arr['school_state'] = $key->hsState;

					if ($arr['current_school'] == "Home Schooled") {
						$arr['address'] = $arr['current_school'];
					}else{
						$arr['address'] = $arr['current_school'].', '.$arr['school_city']. ', '.$arr['school_state'];
					}
				}else{
					$arr['current_school'] = 'N/A';
					$arr['school_city'] = 'N/A';
					$arr['school_state'] = 'N/A';

					$arr['address'] = 'N/A';
				}

			}

			$tmp_address = substr($arr['address'], -4, 3);
			if ($tmp_address == ', ,') {
				$arr['address'] = substr($arr['address'], 0, strlen($arr['address'])-4);
			}

			if($arr['gpa'] == "0.00"){
				$arr['gpa'] = "N/A";
			}
			if($arr['current_school'] !="N/A"){
			if ($arr['current_school'] == "Home Schooled") {
					$arr['current_school'] = $arr['current_school'];
				}else{
					$arr['current_school'] = $arr['current_school'].', '.$arr['school_city']. ', '.$arr['school_state'];
				}
			}

			if(isset($key->sat_total)){
				$arr['sat_score'] = $key->sat_total;
			}else{
				$arr['sat_score'] = 'N/A';
			}

			if(isset($key->act_composite)){
				$arr['act_composite'] = $key->act_composite;
			}else{
				$arr['act_composite'] = 'N/A';
			}


			if($arr['gpa'] == "0.00"){
				$arr['gpa'] = "N/A";
			}

			if($arr['sat_score'] == 0 ){
				$arr['sat_score'] = 'N/A';
			}
			
			if ($arr['act_composite'] == 0) {
				$arr['act_composite'] = 'N/A';
			}

			if(isset($key->toefl_total)){
				$arr['toefl_total'] = $key->toefl_total;
			}else{
				$arr['toefl_total'] = 'N/A';
			}
			if ($arr['toefl_total'] == 0) {
				$arr['toefl_total'] = 'N/A';
			}

			if(isset($key->ielts_total)){
				$arr['ielts_total'] = $key->ielts_total;
			}else{
				$arr['ielts_total'] = 'N/A';
			}
			if ($arr['ielts_total'] == 0) {
				$arr['ielts_total'] = 'N/A';
			}


			if(isset($key->financial_firstyr_affordibility)){
				$arr['financial_firstyr_affordibility'] = '$'.$key->financial_firstyr_affordibility;
			}else{
				$arr['financial_firstyr_affordibility'] = 'N/A';
			}

			if ($arr['financial_firstyr_affordibility'] === 0) {
				$arr['financial_firstyr_affordibility'] = 'N/A';
			}

			if (isset($key->country_code)) {
				$arr['country_code'] = $key->country_code;
				$arr['country_name'] = $key->country_name;
			}else{
				$arr['country_code'] = 'N/A';
				$arr['country_name'] = 'N/A';			
			}

			if (isset($key->degree_name)) {

				$degree_name = $key->degree_name;
				$major_name = $key->major_name;
				$profession_name = $key->profession_name;
			}else{
				$degree_name = NULL;
				$major_name = NULL;
				$profession_name = NULL;

			}
			
			
			if ($degree_name == "") {
				$arr['objective'] = null;
			}else{
				$arr['objective'] = "I would like to get a/an ".$degree_name." in ".$major_name.". My dream would be to one day work as a(n) ".$profession_name;
			}
			$arr['major'] = $major_name;

			$trc = new Transcript;

			$trc = $trc->getUsersTranscript($key->user_id);
			$uploads_arr = array();

			$arr['transcript'] = 0;
			$arr['resume'] = 0;
			$arr['ielts'] = 0;
			$arr['toefl'] = 0;
			$arr['financial'] = 0;

			foreach ($trc as $key) {
				$tmp = array();

				$tmp['doc_type'] = $key->doc_type;
				$tmp['path'] = $key->transcript_path. $key->transcript_name;
				$tmp['transcript_name'] = $key->transcript_name;

				if ($key->doc_type == 'transcript') {
					$arr['transcript'] = 1;
				}
				if ($key->doc_type == 'resume') {
					$arr['resume'] = 1;
				}
				if ($key->doc_type == 'ielts') {
					$arr['ielts'] = 1;
				}
				if ($key->doc_type == 'toefl') {
					$arr['toefl'] = 1;
				}
				if ($key->doc_type == 'financial') {
					$arr['financial'] = 1;
				}

				$uploads_arr[] = $tmp;
			}

			$arr['upload_docs'] = $uploads_arr;

			$ret[] = $arr;
		}

		return $ret;
	}

	/**
	 * sendCampaignMessage
	 *
	 * Send campaign message to all the user receivers as a message 
	 * @param data
	 * @param campaign_id
	 * @param student_user_ids
	 * @return NULL
	 */
	private function sendCampaignMessage($data, $campaign_id, $student_user_ids, $is_text = NULL, $is_list_user = NULL){

		$org_branch_user_ids_collection = OrganizationBranchPermission::where('organization_branch_id', $data['org_branch_id'])
																	  ->get();

		if (isset($is_text)) {
			$email_sent = 1;
			$is_text    = 1;
			$has_text   = 1;
		}else{
			$email_sent = 0;
			$is_text    = 0;
			$has_text   = 0;
		}
		
		if (!isset($is_list_user)) {
			$is_list_user = 0;
		}

		
		// $student_user_ids = explode(",", $student_user_ids);

		$ccs = CollegeCampaignStudent::where('sent', 0)
									 ->where('campaign_id', $campaign_id)
									 ->get();

		if (isset($ccs) && !empty($ccs)) {
			foreach ($ccs as $main_key) {

				$val = $main_key->user_id;
				if ($val == '0' || $val == 0 || $val == '') {
					continue;
				}

				$cmt = DB::connection('bk')->table('college_message_threads as cmt')
						->join('college_message_thread_members as cmtm', 'cmt.id', '=', 'cmtm.thread_id')
						->where('cmt.campaign_id', $campaign_id)
						->where('cmtm.user_id', $val)
						->select('cmt.id as id')
						->first();

				$first_thread = false;

				if (!isset($cmt)) {
					$cmt = new CollegeMessageThreads;
					$cmt->campaign_id = $campaign_id;
					$cmt->has_text    = $has_text;
						$cmt->save();
					$first_thread = true;
				}
				
				if ($first_thread) {
					
					foreach ($org_branch_user_ids_collection as $key) {

						if ($key->user_id == 0 || $key->user_id == -1) {
							continue;
						}

						$attr = array('user_id' => $key->user_id, 'org_branch_id' => $data['org_branch_id'], 'thread_id' => $cmt->id);
						$value  = array('user_id' => $key->user_id, 'org_branch_id' => $data['org_branch_id'], 'thread_id' => $cmt->id);
						
						$cmtm = CollegeMessageThreadMembers::updateOrCreate($attr, $value);
					}
						
					// Add the user to thread members
					$attr = array('user_id' => $val, 'org_branch_id' => $data['org_branch_id'], 'thread_id' => $cmt->id);
					$value  = array('user_id' => $val, 'org_branch_id' => $data['org_branch_id'], 'thread_id' => $cmt->id, 
									'is_list_user' => $is_list_user);
					
					$cmtm = CollegeMessageThreadMembers::updateOrCreate($attr, $value);
				}

				// Add the substitute data
				if (isset($is_list_user) && $is_list_user == 1) {
					$vars_user = ListUser::on('rds1')->where('id', $val)->select('fname', 'lname')->first();	
				}else{
					$vars_user = User::on('rds1')->where('id', $val)->select('fname', 'lname')->first();
				}
				// If this person doesn't exist then move on to the next person.
				if (!isset($vars_user)) {
					continue;
				}
				
				$vars = array();
				$vars['STUDENTNAME'] = $this->convertNameToUTF8(ucfirst(strtolower($vars_user->fname)) . ' ' . ucfirst(strtolower($vars_user->lname)));
				$tmp_campaign_body = $data['campaign_body'];
				$tmp_campaign_body = $this->setSubstitutionData($tmp_campaign_body, $vars);
			

				$msg = '<b>Subject: </b>'.$data['campaign_subject']."<br>".$tmp_campaign_body;

				// Check if the message has already been sent.
				$cml = CollegeMessageLog::where('user_id', $data['user_id'])
										->where('thread_id', $cmt->id)
										->where('msg', $msg)
										->first();

				if (!isset($cml)) {
					
					// set last sent on
					$cc = CollegeCampaign::find($campaign_id);
					$cc->last_sent_on = Carbon::now();
					$cc->save();

					$attr = array('user_id' => $data['user_id'], 'thread_id' => $cmt->id, 'msg' => $msg,
							  'is_read' => 0, 'attachment_url' => '', 'is_deleted' => 0);
					$val  = array('user_id' => $data['user_id'], 'thread_id' => $cmt->id, 'msg' => $msg,
								  'is_read' => 0, 'attachment_url' => '', 'is_deleted' => 0, 'is_text' => $is_text);
					
					$cml = CollegeMessageLog::updateOrCreate($attr, $val);
					
					$ids = DB::connection('rds1')
						->table('college_message_thread_members as cmtm')
						->where('user_id', $data['user_id'])
						->where('thread_id', $cmt->id)
						->pluck('id');

					// increment number of sent messages
					if(!empty($ids)){
						DB::table('college_message_thread_members')
							->whereIn('id', $ids)
							->increment('num_of_sent_msg');
					}

					//Reset all the cache values of each member of each thread, and increment the num of unread msgs
					$thread_members = CollegeMessageThreadMembers::where('thread_id', '=', $cmt->id)
										->where('user_id', '!=', $data['user_id'])
										->pluck('user_id');

					$this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $data['user_id']);
					$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $data['user_id']);
					$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $data['user_id']);

					foreach ($thread_members as $k) {
						Cache::forget(env('ENVIRONMENT') .'_'.$k.'_msg_thread_ids');

						$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $k);
						$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $k);
						$this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $k);
					}

					$ids = DB::connection('rds1')
						->table('college_message_thread_members as cmtm')
						->whereIn('user_id', $thread_members)
						->where('thread_id', $cmt->id)
						->pluck('id');

					// increment number of sent messages
					if(!empty($ids)){
						DB::table('college_message_thread_members')
							->whereIn('id', $ids)
							->update(array(
								'num_unread_msg' => DB::raw('num_unread_msg + 1'),
								'email_sent' => $email_sent
							));
					}
				}

				// save to College Campaign Student
				$main_key->sent = 1;
				$main_key->save();
			}
		}
		
	}

	/**
	 * approvedStudentSearch
	 *
	 * Search through the approved list of organization, and return the users' information
	 * that match the entry term
	 * @return json
	 */
	public function approvedStudentSearch(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (!isset($input['term']) || empty($input['term'])) {
			return json_encode($term);
		}

		$term = $input['term'];

		if (isset($data['agency_collection'])) {

			$rec = DB::connection('bk')->table('users as u')
					->join('agency_recruitment as r', 'r.user_id', '=', 'u.id')
					->where('r.status', 1)
				   	->where('r.agency_recruit', 1)
				   	->where('r.user_recruit', 1)
				   	->where('r.agency_id', $data['agency_collection']->agency_id)
				   	->select('u.id')
				    ->where(DB::raw('CONCAT(fname, lname)'), 'LIKE', '%'.$term.'%' )
				   	->groupBy('u.id');
		}else{
			$rec = DB::connection('bk')->table('users as u')
					->join('recruitment as r', 'r.user_id', '=', 'u.id')
					->where('r.status', 1)
				   	->where('r.college_recruit', 1)
				   	->where('r.user_recruit', 1)
				   	->where('r.college_id', $data['org_school_id'])
				   	->select('u.id')
				    ->where(DB::raw('CONCAT(fname, lname)'), 'LIKE', '%'.$term.'%' )
				   	->groupBy('u.id');
		}
		
		
		if (isset($input['user_ids']) && !empty($input['user_ids'])) {
			
			$user_ids = $input['user_ids'];	
			$arr = array();
			foreach ($user_ids as $key => $value) {
				$arr[] = Crypt::decrypt($value);
			}

			$rec = $rec->whereNotIn('u.id', $arr);
		}

		$rec = $rec->get();

		if (empty($rec)) {
			return json_encode($rec);
		}

		$rec_arr = array();

		foreach ($rec as $key) {
			$rec_arr[] = $key->id;
		}
		
		$rec_arr = implode(",", $rec_arr);

		$ret = array();
		
		$tmp = $this->getRecipientName($data, $rec_arr);

		foreach ($tmp as $key) {
			$arr = array();
			$arr['user_id'] =Crypt::encrypt($key['user_id']);
			$arr['fname'] = $key['fname'];
			$arr['lname'] = $key['lname'];
			$arr['json'] = json_encode($key);
			$arr['json'] = str_replace("'", "&lsquo;", $key);
			$arr['json'] = json_encode($arr['json']);

			$ret[] = $arr;
		}


		return $ret;
	}

	/**
	 * sendScheduleCampaign
	 *
	 * Cron job to send schedule campaign to users
	 * @return NULL
	 */
	public function sendScheduleCampaign(){

		$now = Carbon::now();
		$sevenminago = Carbon::now()->subMinutes(7);

		$cc = DB::connection('rds1')->table('college_campaigns as cc')
									->join('college_campaign_students as ccs', 'cc.id', '=', 'ccs.campaign_id')
								  	->where('ccs.sent', 0)
								  	->groupBy('cc.id')
								  	->select('cc.*')
								  	->orderBy('is_text_msg', 'DESC')
				 				  	->orderBy('id', 'DESC')
				 				  	->where('scheduled_at', '>=', $sevenminago)
									->where('scheduled_at', '<=', $now)
									->whereNull('last_sent_on')
									->whereNotNull('college_id')
									->where('num_of_student_user_ids', '>', 0)
									->get();

		$this->sendReadyCampaign(null, NULL, $cc);
		
		return "success";
	}

	/**
	 * sendReadyCampaign
	 *
	 * Cron job to send readiness campaign to users
	 * @return NULL
	 */
	public function sendReadyCampaign($campaign_id = NULL, $is_list_user = NULL, $cc = NULL){

		if (!isset($cc)) {
			$cc = DB::connection('rds1')->table('college_campaigns as cc')
									->join('college_campaign_students as ccs', 'cc.id', '=', 'ccs.campaign_id')
								  	->where('ccs.sent', 0)
								  	->groupBy('cc.id')
								  	->select('cc.*')
								  	->orderBy('is_text_msg', 'DESC')
								  	->where('cc.ready_to_send', 1)
				 				  	->orderBy('id', 'DESC');

			if (isset($campaign_id)) {
				$cc = $cc->where('id', $campaign_id);
			}else{
				// $cc = $cc->where('ready_to_send', 1)
				// 		 ->where('num_of_student_user_ids', '>', 0)
				// 		 ->take(5);
				$cc = $cc->take(5);
			}

			$cc = $cc->get();
		}
		
		foreach ($cc as $key) {
						
			$data = array();

			$obp = DB::table('organization_branches as ob')
					 ->join('organization_branch_permissions as obp', 'ob.id', '=', 'obp.organization_branch_id')
					 ->where('ob.school_id', $key->college_id)
					 ->select('ob.id as org_branch_id', 'obp.user_id')
					 ->first();

			if (!isset($obp)) {
				continue;
			}

			if (isset($key->sender_user_id) && !empty($key->sender_user_id)) {
				$user_id = $key->sender_user_id;
			}else{
				$user_id = $obp->user_id;
			}

			$data['org_branch_id'] = $obp->org_branch_id;
			$data['college_id'] = $key->college_id;
			$data['user_id'] = $user_id;
			$data['campaign_subject'] = $key->subject;
			$data['campaign_body'] = $key->body;
			$data['campaign_id'] = $key->id;

			$is_text = NULL;

			if (isset($key->is_text_msg) && $key->is_text_msg == 1) {
				$is_text = true;
			}

			if (isset($key->is_list_user)) {
				$is_list_user = $key->is_list_user;
			}
			
			if (isset($is_text)) {

				Cache::put(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user', $is_list_user, 60);

				$gmc = new GroupMessagingController;
				
				$input = array();	

				$input['selected_students_id'] = '';
				$student_user_ids_arr = explode(",", $key->student_user_ids);

				foreach ($student_user_ids_arr as $k => $v) {
					$input['selected_students_id'] .= Crypt::encrypt($v).',';
				}

				$ret = json_decode($gmc->getNumOfEligbleTextUsers($input, $data));

				if ($ret->can_send == 1) {
					$tw = new TwilioController;
					$result = $tw->sendSms($data, $ret);
					$this->sendCampaignMessage($data, $key->id, NULL, $is_text, $is_list_user);
				}
			}else{
				$this->sendCampaignMessage($data, $key->id, NULL);
			}

			$tmp = CollegeCampaign::find($key->id);
			$tmp->ready_to_send = 0;
			$tmp->save();

		}

		return "success";
	}

	/**
	 * removeCampaign
	 *
	 * remove a camaign based on campaign_id
	 * @return (string) 
	 */
	public function removeCampaign(){

		$input = Request::all();
		try {
			$campaign_id = Crypt::decrypt($input['id']);
			$cc = CollegeCampaign::destroy($campaign_id);

		} catch (\Exception $e) {
			return "bad campaign_id";
		}
		
		return "success";
	}

	/**
	 * uploadAttachment
	 *
	 * upload image/pdf/doc/txt for the campaign
	 * @return (string) profile_image_name
	 */
	public function uploadAttachment($type){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		// Create message arrays
		$error_alert = array(
			'img' => '/images/topAlert/urgent.png',
			'bkg' => '#de4728',
			'textColor' => '#fff',
			'dur' => '7000'
		);
		$success_alert = array(
			'img' => '/images/topAlert/checkmark.png',
			'bkg' => '#a0db39',
			'textColor' => '#fff',
			'dur' => '5000'
		);

		// Validation rules
		$rules = array(
			'remove' => array(
				'regex:/^1$/'
			),
			'profile_picture' => array(
				'mimes:jpeg,png,gif'
			)
		);

		// Validate
		$validator = Validator( $input, $rules );
		if( $validator->fails() ){
			$error_alert['msg'] = 'An image of type: jpeg, png, or gif is required.';
			return json_encode( $error_alert );
		}

		if ($type == 'attach') {
			$file_info = (array)$input['files'];
		}

		if ($type == 'img') {
			$file_info = (array)$input['files2'];
		}
	
		$cnt = 0;
		foreach ($file_info as $key => $val) {
			if ($cnt == 1) {
				$originalName = $val;
			}
			if ($cnt == 2) {
				$fileType = $val;
			}
			$cnt++;
		}

		if ($fileType != 'image/jpeg' && $fileType != 'image/png' && $fileType != 'image/gif') {

			if (Request::hasFile('files') || Request::hasFile('files2')) {

				if ($type == 'attach') {
					$file = Request::file('files');
				}
				
				if ($type == 'img') {
					$file = Request::file('files2');
				}

				return $this->uploadAFile($file, $data);
			}else{
				return json_encode( array( 'msg' => "There was an error uploading your transcript."  ) );
			}
			
		}
		// add photo or update
		$Obj = new User();

		if ($type == 'attach') {
			$profile_image_name = $Obj->ProcessResize( 'files', $data['user_id']);
		}
		
		if ($type == 'img') {
			$profile_image_name = $Obj->ProcessResize( 'files2', $data['user_id']);
		}
		
		// Get extension to set AWS's 'content type' field in s3
		$exploded = explode( '.', $profile_image_name );
		$extension = $exploded[ count( $exploded ) -1 ];
		// return 'jpg'

		switch( $extension ){
			case 'jpg':
			case 'jpeg':
				$content_type = 'image/jpeg';
				break;
			case 'png':
				$content_type = 'image/png';
				break;
			case 'gif':
				$content_type = 'image/gif';
				break;
		}

		// Halt upload process
		if( !isset( $content_type ) ){
			// Delete temp image
			unlink('dropzone/images/' . $profile_image_name);
			// Return topAlert object
			$error_alert['msg'] = 'The uploaded image must be in jpeg, png, or gif format.';
			return json_encode( $error_alert );
		}

		$Pic_Src = $Obj->get_profile_image_path();

		/*
		// testing on this output, GetAnimation() successfully returns a long string includes all the animation needed.

		$file_path = $input['files2']->getPathName();
		$text = "Hello World";
		$image = imagecreatefromgif($file_path);
		$text_color = imagecolorallocate($image, 200, 200, 200);
		imagestring($image, 5, 20, 20, $text, $text_color);

		ob_start();
		imagegif($image);
		$frames[] = ob_get_contents();
		$framed[] = 40;
		ob_end_clean();

		$gif = new GIFEncoder($frames,$framed,0,2,0,0,0,'bin');
		dd( $gif->GetAnimation() );
		*/


		// Upload to AWS bucket
		$aws = AWS::get('s3');
		$aws->putObject(array(
			'ACL'         => 'public-read',
			'Bucket'      => 'asset.plexuss.com/admin/campaign/uploads',
			'Key'         => $profile_image_name,
			'ContentType' => $content_type,
			'SourceFile'  => $Obj->get_profile_image_path() .'\\'. $profile_image_name
		));

		// Delete temp image
		unlink( $Obj->get_profile_image_path() .'\\'. $profile_image_name );
		// Update user image in DB

		$ret = array();
		$ret['is_image'] = 1;
		$ret['name'] = $profile_image_name;

		return json_encode($ret);
	}

	/**
	 * uploadAFile
	 *
	 * upload a file that is not an image
	 * @return (json)
	 */
	private function uploadAFile($file, $data){

		$path = $file->getRealPath();
		$filename = $file->getClientOriginalName();
		$ext = $file->getClientOriginalExtension();
		$mime = $file->getMimeType();
		$file_path = pathinfo($filename);

		$hashed_id = Crypt::encrypt($data['user_id']);

		$hashed_id = substr($hashed_id, 0, 10);
		
		$saveas = $file_path['filename'].'_' . date('Y-m-d_H-i-s') . '_' . $hashed_id . "." . strtolower($ext);

		// upload to aws regardless of filetype
		$s3 = AWS::get('s3');
		$s3->putObject(array(
			'ACL' => 'public-read',
			'Bucket' => 'asset.plexuss.com/admin/campaign/uploads',
			'Key' => $saveas,
			'SourceFile' => $path
		));

		$ret = array();
		$ret['is_image'] = 0;
		$ret['name'] = $saveas;

		return json_encode($ret);
		
	}//end of uploadcenter post function

	private function generateListOfCampaigns($data, $is_text, $default_campaign_id = null, $txtmsg_page = NULL){

		if (isset($data['agency_collection'])) {
			$cc = CollegeCampaign::where('agency_id', $data['agency_collection']->agency_id)
								->orderBy('updated_at', 'DESC')
								->get();
			$arr = array();
			$arr[] = $data['agency_collection']->agency_id;
			$rec = new AgencyRecruitment;
			$rec_cnt = $rec->getNumOfApprovedForAgency($arr);
			foreach ($rec_cnt as $key) {
				$rec_cnt = $key->cnt;
			}
			
		}else{
			// $cc_temp = new CollegeCampaign;
			// $cc_temp = $cc_temp->cleanUpCampaignUsers($data['org_school_id']);

			$cc = CollegeCampaign::where('college_id', $data['org_school_id'])
								 ->where('is_text_msg', $is_text)
								 ->orderBy('updated_at', 'DESC')
								 ->get();
			$arr = array();
			$arr[] = $data['org_school_id'];
			$rec = new Recruitment;
			$rec_cnt = $rec->getNumOfApprovedForColleges($arr);
			foreach ($rec_cnt as $key) {
				$rec_cnt = $key->cnt;
			}

			$pending_cnt = $rec->getNumOfPendingForColleges($arr);
			foreach ($pending_cnt as $key) {
				$pending_cnt = $key->cnt;
			}
		}

		$data['campaigns'] = array();
		$data['campaigns']['previous'] = array();
		$data['campaigns']['scheduled'] = array();
		$data['campaigns']['draft'] = array();
		$data['campaigns']['all_approved'] = array();

		$tmp_campaign_ids = array();

		foreach ($cc as $key) {
			$tmp_campaign_ids[] = $key->id;

			$arr = array();
			$arr['campaign_id'] = Crypt::encrypt($key->id);
			$arr['name'] = $key->name;
			$arr['recipients'] = isset($key->num_of_student_user_ids) ? $key->num_of_student_user_ids : 0;
			$arr['last_sent_on'] = NULL;
			$arr['is_mms'] = $key->is_mms;

			if (isset($default_campaign_id) && $key->id == $default_campaign_id) {
				$arr['is_selected']  = 1;
			}else{
				$arr['is_selected']  = 0;
			}

			if ((isset($key->last_sent_on) && !empty($key->last_sent_on)) || $key->ready_to_send == 1) {
				if ($key->ready_to_send == 1) {
					$arr['last_sent_on'] = 'Pending to send...';
				}else{
					$arr['last_sent_on'] = Carbon::parse($key->last_sent_on);
					$arr['last_sent_on'] = $arr['last_sent_on']->toDayDateTimeString();
				}
				$data['campaigns']['previous'][] = $arr;
			}elseif ($key->scheduled_at && !empty($key->scheduled_at) && empty($key->last_sent_on)) {
			 	$arr['scheduled_at'] = Carbon::parse($key->scheduled_at);
			 	$arr['scheduled_at'] = $arr['scheduled_at']->toDayDateTimeString();

			 	$data['campaigns']['scheduled'][] = $arr;
			}else{
				$data['campaigns']['draft'][] = $arr;
			} 
		}

		$arr = array();
		$arr['campaign_id'] = Crypt::encrypt(-1);
		$arr['name'] = 'Handshakes';
		$arr['recipients'] = $rec_cnt;
		$arr['last_sent_on'] = NULL;
		$arr['is_mms'] = 0;
		$data['campaigns']['all_approved'] = $arr;

		if (((isset($txtmsg_page) && $txtmsg_page == true && Session::has('sales_super_power')) 
			|| $data['user_id'] == 463) || !isset($txtmsg_page) ) {

			$data['campaigns']['all_pending'] = array();
			$arr['campaign_id'] = Crypt::encrypt(-2);
			$arr['name'] = 'Pendings';
			$arr['recipients'] = $pending_cnt;
			$data['campaigns']['all_pending'] = $arr;
		}
		
		$data['tmp_campaign_ids'] = $tmp_campaign_ids;

		return $data;
	}

	/**
	 * generateAutoCampaign
	 *
	 * Generate automatic campaign for schools who have been idle this week
	 * @return (string)
	 */
	public function generateAutoCampaign(){

		$monday = Carbon::parse('last sunday 11:59:59 pm');
		$tomorrow    = Carbon::tomorrow();

		DB::connection('rds1')->statement('SET SESSION group_concat_max_len = 1000000;');

		$pending_auto_campaigns = CollegeCampaignTemplate::on('rds1')
														 ->where('type', 'pending')
														 ->select(DB::raw('GROUP_CONCAT(DISTINCT name SEPARATOR ",") as name'))
														 ->orderBy('id')
														 ->first();
		$pending_auto_campaigns = $pending_auto_campaigns->name;

		$handshake_auto_campaigns = CollegeCampaignTemplate::on('rds1')
														   ->where('type', 'handshake')
														   ->select(DB::raw('GROUP_CONCAT(DISTINCT name SEPARATOR ",") as name'))
														   ->orderBy('id')
														   ->first();
		$handshake_auto_campaigns = $handshake_auto_campaigns->name;

		// Colleges that have sent a campaign this week.
		$suppression_colleges = CollegeCampaign::on('rds1')->where('updated_at', '>', $monday)
														   ->where('updated_at', '<', $tomorrow)
														   ->select(DB::raw('GROUP_CONCAT(DISTINCT college_id SEPARATOR ",") as college_id'))
														   ->first();	


		if (isset($suppression_colleges->college_id) && !empty($suppression_colleges->college_id)) {
	   		$suppression_colleges_arr = explode(",", $suppression_colleges->college_id);	
	    }												   

		$ob = OrganizationBranch::on('rds1')
								->join('colleges as c', 'c.id', '=', 'organization_branches.school_id')
								->join('organization_branch_permissions as obp', 'obp.organization_branch_id', '=', 'organization_branches.id')
								->join('users as u', 'u.id', '=', 'obp.user_id')
								->where('obp.super_admin', 1)
								->where(function($query){
									$query = $query->where('organization_branches.pending_auto_campaign', '=', 1)
												   ->orWhere('organization_branches.handshake_auto_campaign', '=', 1);
								});

		if (isset($suppression_colleges_arr) && !empty($suppression_colleges_arr)) {
			$ob = $ob->whereNotIn('school_id', $suppression_colleges_arr);
		}

		$ob_count = $ob->count();

		$take = ceil($ob_count/6);

		$ob = $ob->take($take)->groupBy('organization_branches.id')->orderByRaw("RAND()")->get();

		// Run for Pending
		foreach ($ob as $key) {
			if ($key->pending_auto_campaign == 1) {
				// Find out which college campaigns we've already sent pending template
				$cc = NULL;
				$cct = NULL;
				$cnt_cc = NULL;
				$tmp_pending_auto_campaigns = str_replace("{{COLLNAME}}", $key->school_name, $pending_auto_campaigns);
				$tmp_pending_auto_campaigns_arr   = explode(",", $tmp_pending_auto_campaigns);
				$cc = CollegeCampaign::on('rds1')->whereIn('name', $tmp_pending_auto_campaigns_arr)->where('college_id', $key->school_id);

				$cnt_cc = $cc->count();

				if ($cnt_cc > 0) {
					$cc = $cc->get();
					$cct_id_array = array();
					
					foreach ($cc as $k) {
						$campaign_name = str_replace($key->school_name, "{{COLLNAME}}", $k->name);
						$cct = CollegeCampaignTemplate::on('rds1')->where('name', $campaign_name)->first();
						$campaign_id = Crypt::encrypt($k->id);
						if (isset($cct)) {
							$this->saveAutomaticCampaign($cct, $key, 'pending', $campaign_id);
							$cct_id_array[] = $cct->id;
						}

					}

					$cct = NULL;
					if (isset($cct_id_array) && !empty($cct_id_array)) {	

						$cct = CollegeCampaignTemplate::on('rds1')->where('type', 'pending')->whereNotIn('id', $cct_id_array)->first();
						if (isset($cct)) {
							$this->saveAutomaticCampaign($cct, $key, 'pending');
						}
					}
				}else{
					$cct = CollegeCampaignTemplate::on('rds1')->where('type', 'pending')->first();
					$this->saveAutomaticCampaign($cct, $key, 'pending');
				}
			}
	   	}	

		// Run for handshakes
	   	foreach ($ob as $key) {
	   		if ($key->handshake_auto_campaign == 1) {
				// Find out which college campaigns we've already sent pending template
				$cc = NULL;
				$cct = NULL;
				$cnt_cc = NULL;
				$tmp_handshake_auto_campaigns = str_replace("{{COLLNAME}}", $key->school_name, $handshake_auto_campaigns);
				$tmp_handshake_auto_campaigns_arr   = explode(",", $tmp_handshake_auto_campaigns);
				$cc = CollegeCampaign::on('rds1')->whereIn('name', $tmp_handshake_auto_campaigns_arr)->where('college_id', $key->school_id);

				$cnt_cc = $cc->count();
				
				if ($cnt_cc > 0) {
					$cc = $cc->get();
					$cct_id_array = array();
					
					foreach ($cc as $k) {
						$campaign_name = str_replace($key->school_name, "{{COLLNAME}}", $k->name);
						$cct = CollegeCampaignTemplate::on('rds1')->where('name', $campaign_name)->first();
						$campaign_id = Crypt::encrypt($k->id);
						if (isset($cct)) {
							$this->saveAutomaticCampaign($cct, $key, 'handshake', $campaign_id);
							$cct_id_array[] = $cct->id;
						}

					}

					$cct = NULL;
					if (isset($cct_id_array) && !empty($cct_id_array)) {	

						$cct = CollegeCampaignTemplate::on('rds1')->where('type', 'handshake')->whereNotIn('id', $cct_id_array)->first();
						if (isset($cct)) {
							$this->saveAutomaticCampaign($cct, $key, 'handshake');
						}
					}
				}else{
					$cct = CollegeCampaignTemplate::on('rds1')->where('type', 'handshake')->first();
					$this->saveAutomaticCampaign($cct, $key, 'handshake');
				}
	   		}
	   	}	

	   	return "success";
	}

	/**
	 * saveAutomaticCampaign
	 *
	 * Gathers the variables to setup automatic campaigns.
	 * @param cct : College Campaign template properties.
	 * @param key : This college information
	 * @param campaign_id 
	 * @return null
	 */
	private function saveAutomaticCampaign($cct, $key, $rec_type, $campaign_id = NULL){
		
		$cmtm = new CollegeMessageThreadMembers;
		$rec  = new Recruitment;

		$input = array();
		$data  = array();
		$vars  = array();

		$vars['COLLNAME']	  = $key->school_name;
		$vars['MAINURL'] 	  = 'https://plexuss.com/college/'.$key->slug;
		$vars['GETRECRUITED'] = 'https://plexuss.com/home?requestType=recruitme&amp;collegeId='.$key->school_id;
		$vars['RANKINGURL']   = 'https://plexuss.com/college/'.$key->slug.'/ranking';
		$vars['STATSURL']     = 'https://plexuss.com/college/'.$key->slug.'/stats';
		$vars['ADMISSIONURL'] = $key->admission_url;
		$vars['REPNAME']	  = ucwords(strtolower($key->fname)) . ' '. ucwords(strtolower($key->lname));
		$vars['FINAID']		  = 'https://plexuss.com/college/'.$key->slug.'/financial-aid';
		$vars['APPLYURL']	  = $key->application_url;
		
		$data['user_id']       = $key->user_id;
		$data['org_school_id'] = $key->school_id;
		$data['org_branch_id'] = $key->organization_branch_id;
		$data['subject']       = $this->setSubstitutionData($cct->subject, $vars);

		$input['c_name']   	   = $this->setSubstitutionData($cct->name, $vars);
		$input['c_subject']    = $data['subject'];
		$input['c_body']	   = $this->setSubstitutionData($cct->content, $vars);
		$input['send']         = 'true';
		$input['save']         = 'true';
		if (isset($campaign_id)) {
			$input['id'] = $campaign_id;
		}

		$suppression_user_ids = $cmtm->getUsersWhoHaveReceivedThisMessage($data);
		$input['selected_students_id'] = $rec->getRecruitmentDistinctUsers($rec_type, $suppression_user_ids->users, $data);
		if (isset($input['selected_students_id']) && !empty($input['selected_students_id'])) {
			$this->saveCampaign($input, $data);
		}
	}

	public function setAutomaticCampaign($type){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if ($type == 'pending') {
			//$ob = OrganizationBranch::where('id', $data['org_branch_id'])->update(array('pending_auto_campaign' => '1 - `pending_auto_campaign`'));
			DB::statement('UPDATE `organization_branches` SET `pending_auto_campaign` = 1 - `pending_auto_campaign` WHERE `id` ='.$data['org_branch_id'].';');
			return "success";
		}

		if ($type == 'handshake') {
			//$ob = OrganizationBranch::where('id', $data['org_branch_id'])->update(array('1handshake_auto_campaign' => '1 - `handshake_auto_campaign`'));
			DB::statement('UPDATE `organization_branches` SET `handshake_auto_campaign` = 1 - `handshake_auto_campaign` WHERE `id` ='.$data['org_branch_id'].';');
			return "success";
		}

		return "fail";
	}

	public function uploadCsv() {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$input = Request::all();

	    $rules = array(
	        'file' => 'required',
	    );

	    $validator = Validator($input, $rules);
	    // process the form
	    if ($validator->fails()) {
	        return '*upload files error';
	    }else {

	    	$dt = array();

	        try {

	            $ret = Excel::load(Request::file('file'), function ($reader) use($data){

	            })->get();

	        } catch (\Exception $e) {
	            
	            return 'Something went wrong! Try again later. If the problem presists try contacting support@plexuss.com';
	        }

	        $cnt = 1;
            $params = array();

            foreach ($ret->toArray() as $row) {

            	$row['phone'] = preg_replace("/[^0-9]/","",$row['phone']);
           
            	$tpl = TextSuppressionList::on('bk')->where('phone', 'LIKE', '%'.$row['phone'])->first();

            	if (isset($tpl) && !empty($tpl)) {
            		continue;
            	}

            	$lu = new ListUser;

            	$lu->country       = $row['country'];
            	$lu->fname         = $row['fname'];
            	$lu->lname         = $row['lname'];
            	$lu->org_branch_id = $data['org_branch_id'];
            	$lu->phone 		   = $row['phone'];

            	$lu->save();

            	$tmp = array();
            	$tmp['id'] = Crypt::encrypt($lu->id);
            	$tmp['list_id'] = $cnt;
            	$tmp['name'] = $row['fname']. ' '. $row['lname'];

            	$params[] = $tmp;

            	$cnt++;
            }

            $this->setGroupMsgForText($params);
	    } 
	}

	// TEXT MESSAGE AJAX CALLS
	public function getNumOfEligbleTextUsers($input = NULL, $data = NULL){

		if (!isset($data)) {
			$viewDataController = new ViewDataController();
        	$data = $viewDataController->buildData();
		}
		
        if (!isset($input)) {
        	$input = Request::all();
        }
		
		$student_user_ids = '';
		
		if (isset($input['selected_students_id']) && !empty($input['selected_students_id']) && strpos($input['selected_students_id'], ',') !== FALSE){
			$student_user_ids_arr = explode(',', $input['selected_students_id']);
			foreach ($student_user_ids_arr as $key) {
				if(!empty($key) && !is_numeric($key)){
					$student_user_ids = $student_user_ids.Crypt::decrypt($key).",";
				}elseif (!empty($key)) {
					$student_user_ids = $student_user_ids.$key.",";
				}
			}
		}else{
			if (isset($input['selected_students_id']) && !empty($input['selected_students_id']) && !is_numeric($input['selected_students_id'])) {
				$student_user_ids = Crypt::decrypt($input['selected_students_id']).',';
			}else if(isset($input['selected_students_id']) && !empty($input['selected_students_id'])) {
				$student_user_ids = $input['selected_students_id'].',';
			}
		}

		// dd($student_user_ids); //string '349089,347688,721,23939,225338,40160,12380,23117,' (length=49)

		$campaign_chosen = array();
		$campaign_excludes_stu = array();

		foreach ($input as $key => $value) {
			// extract properties begin with "campaign_shown_" => indicates which campaign you choose 

			if (strpos($key, 'campaign_shown_') !== FALSE){
				array_push($campaign_chosen, Crypt::decrypt($value));
			}
			// extract properties begin with "excludes_check_" => indicates which campaign you want to exclude students messaged before
			else if(strpos($key, 'excludes_check_') !== FALSE){
				array_push($campaign_excludes_stu, Crypt::decrypt($value));
			}
		}

		$student_user_ids_arr = $this->getRecipientInfo($campaign_chosen, $campaign_excludes_stu, true);
		
		$student_user_ids_temp = explode(',', $student_user_ids);
		foreach ($student_user_ids_temp as $value) {
			if(isset($value) && !empty($value))
				$student_user_ids_arr[] = $value;
		}

		$student_user_ids_arr = array_unique($student_user_ids_arr);

		$recipients = count($student_user_ids_arr); // count total number
		$student_user_ids .= implode(',', $student_user_ids_arr); // restore total student ids

		$is_list_user = Cache::get(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user');

		if (isset($is_list_user) && $is_list_user == 1) {
			$total_users = ListUser::on('rds1')->where(function($query) use ($student_user_ids_arr){
											foreach ($student_user_ids_arr as $key => $value) {
													if (!empty($value)) {
														$query = $query->orWhere('list_users.id', '=', $value);
													}
												}				
											})
											->whereNotNull('country')
											->whereNotNull('phone');

			$total_users_cnt = $total_users->count();

			$total_users = $total_users->leftjoin('countries as c', 'c.country_name', '=', 'list_users.country')
									   ->leftjoin('free_text_countries as ftc', 'ftc.country_code', '=', 'c.country_code')
									   ->select('list_users.id', 'c.country_name', 'c.country_code', 'list_users.phone',
									   			DB::raw('IF(ftc.id IS NULL, "Paid", "Free") as tier'))
									   ->get()
									   ->toArray();

			$total_eligble_users = ListUser::on('rds1')->join('countries as c', 'c.country_name', '=', 'list_users.country')
												   ->join('free_text_countries as ftc', 'ftc.country_code', '=', 'c.country_code')
												   ->where(function($query) use ($student_user_ids_arr){
														foreach ($student_user_ids_arr as $key => $value) {
																if (!empty($value)) {
																	$query = $query->orWhere('list_users.id', '=', $value);
																}
															}				
														})
												   ->whereNotNull('list_users.country')
												   ->whereNotNull('list_users.phone');

			$total_eligble_users_cnt = $total_eligble_users->count();

			$total_eligble_users 	 = $total_eligble_users->select('list_users.id', 'c.country_name', 'c.country_code', 'list_users.phone')
														   ->get()
														   ->toArray();
		}else{
			$total_users = User::on('rds1')->where(function($query) use ($student_user_ids_arr){
											foreach ($student_user_ids_arr as $key => $value) {
													if (!empty($value)) {
														$query = $query->orWhere('users.id', '=', $value);
													}
												}				
											})
											->whereNotNull('country_id')
											->whereNotNull('phone');

			$total_users_cnt = $total_users->count();

			$total_users = $total_users->leftjoin('countries as c', 'c.id', '=', 'users.country_id')
									   ->leftjoin('free_text_countries as ftc', 'ftc.country_code', '=', 'c.country_code')
									   ->select('users.id', 'c.country_name', 'c.country_code', 'users.phone',
									   			DB::raw('IF(ftc.id IS NULL, "Paid", "Free") as tier'))
									   ->get()
									   ->toArray();

			$total_eligble_users = User::on('rds1')->join('countries as c', 'c.id', '=', 'users.country_id')
												   ->join('free_text_countries as ftc', 'ftc.country_code', '=', 'c.country_code')
												   ->where(function($query) use ($student_user_ids_arr){
														foreach ($student_user_ids_arr as $key => $value) {
																if (!empty($value)) {
																	$query = $query->orWhere('users.id', '=', $value);
																}
															}				
														})
												   ->whereNotNull('users.country_id')
												   ->whereNotNull('users.phone');

			$total_eligble_users_cnt = $total_eligble_users->count();

			$total_eligble_users 	 = $total_eligble_users->select('users.id', 'c.country_name', 'c.country_code', 'users.phone')
														   ->get()
														   ->toArray();
		}

		$ret = array();

		$ret['total_users'] 			= $total_users;
		$ret['total_eligble_users'] 	= $total_eligble_users;
		$ret['total_users_cnt']     	= $total_users_cnt;
		$ret['total_eligble_users_cnt'] = $total_eligble_users_cnt;

		$adminText = AdminText::where('org_branch_id', $data['org_branch_id'])->first();

		if(isset($adminText) && !empty($adminText)) {
			$ret['textmsg_tier']		= $adminText->tier;
			$ret['textmsg_expires_date']= $adminText->expires_at;
			$ret['flat_fee_sub_tier']   = $adminText->flat_fee_sub_tier;
			$ret['num_of_free_texts']   = $adminText->num_of_free_texts;
			$ret['num_of_eligble_texts']= $adminText->num_of_eligble_texts;
		}

		$pp = PurchasedPhone::on('rds1')
    						->where('org_branch_id', $data['org_branch_id'])->first();
    						// ->where('purchased_by_user_id', $data['user_id'])

    	$ret['textmsg_phone'] = $pp->phone;
    	$ret['can_send']      = $this->canSendTextMsg($ret);

		Session::put('text_message_campaign_users', $ret);

		return json_encode($ret);	
	}

	private function canSendTextMsg($ret){

		$bool = 0;

		switch ($ret['textmsg_tier']) {
			case 'free':
				if ($ret['num_of_free_texts'] >= $ret['total_eligble_users_cnt']) {
					$bool = 1;
				}
				break;
			case 'flat_fee':
				if ($ret['num_of_eligble_texts'] >= $ret['total_eligble_users_cnt']) {
					$bool = 1;
				}
				break;
			default:
				# code...
				break;
		}

		return $bool;
	}

	public function getTextmsgOrder() {
		$viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

		$input = Request::all();

		if (isset($data['is_organization'])) {
    		// $type = 'Text';
    		if (!isset($input['textmsg_tier'], $input['textmsg_plan'], $input['textmsg_phone'])) {
    			return;
    		}
    		Session::forget('text_cost_and_plan');
    	}

		$opc = new OmnipayController();
		$total_cost = $opc->calculateTextCost($input, $data);

		return $total_cost;
	}
	// END OF TEXT MESSAGE CALLS
}