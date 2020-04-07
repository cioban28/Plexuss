<?php

namespace App\Http\Controllers;

use Request, Session, DB, DateTime;
use Illuminate\Support\Facades\Cache;
use App\organization, App\OrganizationBranchPermission, App\CollegeMessageThreads, App\CollegeMessageThreadMembers, App\User, App\College;
use App\CollegeCampaign, App\CollegeMessageLog, App\CollegeRecommendationFilters, App\Agency, App\ListUser;
use Carbon\Carbon;


class CollegeMessageController extends Controller{

	protected $adminThreadIds ='';
	private $show_thread_campaign = true;
	const NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD = 10;
	/*
	* Posting a new message to another user
	*
	*/
	/*public function postMessage($thread_id = NULL, $receviver_user_id = NULL, $type =Null){

		$inputs = Request::all();

		$valInputs = array(
			'thread_id' => $thread_id,
			'receviver_user_id' => $receviver_user_id,
			'type' => $type,
			'message' => $inputs['message']);

		$valFilters =array (
			'thread_id' => 'required',
			'receviver_user_id' => 'required|numeric',
			'type' => 'required|alpha',
			'message' => 'required'
			);


		$validator = Validator::make( $valInputs, $valFilters );
		if ($validator->fails()){
			$messages = $validator->messages();
			return $messages;
		}

		$message = $inputs['message'];

		//remove all html tags from user entry.
		$message = trim(preg_replace("/<[^>]*>/", ' ', $message));
		$message = trim(preg_replace("/\s{2,}/", ' ', $message));

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);
		$data['currentPage'] = "admin-messages";



		$sender_user_id = Session::get('userinfo.id');
		$receiver_user_info = User::find($receviver_user_id);

		// Get topic id for this conversation
		$college_message_thread_id = $thread_id;
		$org_branch_user_ids = array();

		//if Topic thread doesn't exitst, create a new topic.
		if( $college_message_thread_id == -1){

			// Add a new thread.
			$cmt = new CollegeMessageThreads;
			$cmt->save();
			$college_message_thread_id = $cmt->id;


			// Attach the user, and organization to the thread members
			$cmtm = new CollegeMessageThreadMembers;

			$cmtm->user_id = $receviver_user_id ;
			$cmtm->org_branch_id = $data['org_branch_id'];
			$cmtm->thread_id = $college_message_thread_id;
			$cmtm->save();

			//*********************************important
			// this piece adds all of the "current" member of organizations to the thread.
			$org_branch_user_ids_collection = OrganizationBranchPermission::where('organization_branch_id', $data['org_branch_id'])->get();
			foreach ($org_branch_user_ids_collection as $key) {
				$cmtm = new CollegeMessageThreadMembers;

				$cmtm->user_id = $key->user_id;
				$cmtm->org_branch_id = $data['org_branch_id'];
				$cmtm->thread_id = $college_message_thread_id;
				$cmtm->save();
				$org_branch_user_ids[] = $key->user_id;
			}


		}else{

			// if the user is not part of thread members, add him to the mix.
			$cmt = CollegeMessageThreadMembers::where('org_branch_id', '=', $data['org_branch_id'])
				->where('user_id', '=', $sender_user_id)
				->where('thread_id', '=', $college_message_thread_id)
				->first();

			if(!$cmt){

				$cmtm = new CollegeMessageThreadMembers;
				$cmtm->user_id = $sender_user_id;
				$cmtm->org_branch_id = $data['org_branch_id'];
				$cmtm->thread_id = $college_message_thread_id;
				$cmtm->save();

			}

		}
		//dd($college_message_thread_id);
		$cml = new CollegeMessageLog;

		$cml->user_id = $sender_user_id;
		$cml->thread_id = $college_message_thread_id;
		$cml->msg = $message;
		$cml->is_read = 0;
		$cml->attachment_url = "";
		$cml->is_deleted = 0;
		$cml->save();


		if (empty($org_branch_user_ids)) {
			$org_branch_user_ids_collection = OrganizationBranchPermission::where('organization_branch_id', $data['org_branch_id'])->get();
			foreach ($org_branch_user_ids_collection as $key) {
				$org_branch_user_ids[] = $key->user_id;
			}
		}
		//Reset all the cache values of each member of each thread, and increment the num of unread msgs
		$thread_members = CollegeMessageThreadMembers::where('thread_id', '=', $college_message_thread_id)
							->whereNotIn('user_id', $org_branch_user_ids)
							->pluck('user_id');

		//print_r($thread_members);
		//exit();

		foreach ($thread_members as $k) {
			Cache::forget(env('ENVIRONMENT') .'_'.$k.'_msg_thread_ids');
		}

		$ids = DB::connection('rds1')
			->table('college_message_thread_members as cmtm')
			->whereIn('user_id', $thread_members)
			->where('thread_id', $college_message_thread_id)
			->pluck('id');

		if(!empty($ids)){
			DB::table('college_message_thread_members')
				->whereIn('id', $ids)
				->update(array(
					'num_unread_msg' => DB::raw('num_unread_msg + 1'),
					'email_sent' => 0
				));
		}

		// update the cache for this user.
		$this->getAllThreads($data, "");

		$data['thread_id'] = $college_message_thread_id;
		$data['stickyUsr'] = "";

		return $college_message_thread_id;
	}*/

	public function getInitialThreadList($user_id = null, $type = null){
		$data = $this->getUsrTopics($user_id, $type, true, true);

		if ($data == 'failed') {
			return redirect()->to('/');
		}

		$crucialData = array();
		$crucialData['template_list'] = $data['message_template'];
		$crucialData['threads'] = $data['topicUsr'];

		return $crucialData;
	}


	public function getInitialThreadListAgency($user_id = null, $type = null){
		$data = $this->getUsrTopics($user_id, $type, true, true);

		if ($data == 'failed') {
			return redirect()->to('/');
		}

		$crucialData = array();
		$crucialData['template_list'] = $data['message_template'];
		$crucialData['threads'] = $data['topicUsr'];

		return $crucialData;
	}

	/*
	* Returns the contact list of a user. with latest message, name, picture,
	* and the date latest message was sent to.
	*
	*/
	public function getUsrTopics($receiver_id = NULL, $type =NULL, $returnDataOnly = null, $modMsgTemplate = false){

		$viewDataController = new ViewDataController();
		if (isset($returnDataOnly)) {
			$data = $viewDataController->buildData(true);
		}else{
			$data = $viewDataController->buildData();
		}

		$is_agency = isset($data['is_agency']) && $data['is_agency'] == 1;

		if(!isset($data['is_organization']) && !$is_agency){
			return 'failed';
		}

		$data['currentPage'] = $is_agency ? 'agency-messages' : 'admin-messages';

		if($receiver_id != NULL){
			$data['stickyUsr'] = $receiver_id;
		}else{
			$data['stickyUsr'] = "";
		}

		if (isset($is_agency) && $is_agency == 1) {
			$data['topicUsr'] = $this->getAllThreadsAgency($data, $receiver_id);
		}elseif (isset($data['is_organization'])) {
			$data['topicUsr'] = $this->getAllThreads($data, $receiver_id);
		}

		$data['hashed_uid'] = $data['remember_token'];

		if(!isset($type)){
			$type = '';
		}
		$data['sticky_thread_type'] = $type;

		if ($is_agency) {
			$agency = Agency::find($data['agency_collection']->agency_id);
			$data['school_name'] = $agency->name;
			$data['school_logo']= 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/'.$agency->logo_url;
		} else {
			$college = College::find($data['org_school_id']);
			$data['school_name'] = $college->school_name;
			$data['school_logo']= 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;
		}

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$data = $this->getMessageTemplates($data, $modMsgTemplate);

		if( isset($returnDataOnly) ){
			return $data;
		}

		/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		exit();
		*/
		//$tmp = $data['topicUsr'];

		//$data['topicUsr'] = json_encode($tmp);
		//$data['topicUsr'] = array_reverse($data['topicUsr']);
		return View( 'admin.messaging', $data);

	}

	/*
	* Returns the list of threads on heart beat.
	*
	*
	*/
	public function getThreadListHeartBeat($receiver_id = NULL, $type = NULL, $redis_input = NULL, $api_input = null){

		if( isset($api_input) ){
			$data = $api_input;
			$input = $api_input;
			$sender_user_id = $api_input['user_id'];
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);

			$sender_user_id = Session::get('userinfo.id');

			$input = Request::all();
		}

		if (isset($input['loadMore'])) {
			if (Cache::has(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore')) {
				$tmp_loadMore_cnt = Cache::get(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore');
				Cache::put(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore', $tmp_loadMore_cnt + self::NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD, 60);
			}else{
				Cache::put(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore', self::NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD, 60);
			}
		}elseif(!Cache::has(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore')) {
			Cache::put(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore', self::NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD, 60);
		}

		//print_r(Session::get($data['user_id'].'_loadMore'));

		if (isset($redis_input)) {
			if(isset($redis_input['org_branch_id'])){
				$data['org_branch_id'] = $redis_input['org_branch_id'];
				$is_college = true;
			}elseif (isset($redis_input['agency_id'])) {
				$data['agency_id'] = $redis_input['agency_id'];
				$data['agency_collection'] = new \stdClass;
				$data['agency_collection']->agency_id = $redis_input['agency_id'];
				$is_agency = true;
			}
			elseif (isset($redis_input['user_id'])) {
				$data['user_id'] = $redis_input['user_id'];
				$is_user = true;
			}

		}else{
			if (isset($data['is_organization']) && $data['is_organization'] == 1) {
				$is_college = true;
			}elseif (isset($data['agency_collection']->agency_id)) {
				$is_agency = true;
			}
		}

		if( isset($api_input) ){
			if (isset($is_college)) {
				$data['topicUsr'] = $this->getAllThreads($data, $receiver_id, $type, $api_input['isNotification'], true);
			}elseif (isset($is_agency)) {
				$data['topicUsr'] = $this->getAllThreadsAgency($data, $receiver_id, $type, $api_input['isNotification'], true);
			}
		}else{
			if (isset($is_college)) {
				$data['topicUsr'] = $this->getAllThreads($data, $receiver_id, $type, NULL, NULL, $redis_input);
			}elseif (isset($is_agency)) {
				$data['topicUsr'] = $this->getAllThreadsAgency($data, $receiver_id, $type, NULL, NULL, $redis_input);
			}elseif (isset($is_user)) {
				$data['topicUsr'] = $this->getAllThreads($data, $receiver_id, $type, NULL, NULL, $redis_input);
			}
		}

		$data['currentPage'] = "admin-messages";

		/*
		if (Cache::has(env('ENVIRONMENT') .'_'.$sender_user_id.'_msg_thread_ids')){

			$thread_arr = Cache::get(env('ENVIRONMENT') .'_'.$sender_user_id.'_msg_thread_ids');
			foreach ($thread_arr as $key => $value) {
				$data['topicUsr'][] = Cache::get(env('ENVIRONMENT') .'_'.'thread_'.$value.'_data');
			}
		}else{
			$data['topicUsr'] = $this->getAllThreads($data, $receiver_id);
		}
		*/
		//$data['topicUsr'] = array_reverse($data['topicUsr']);
		if ( isset($redis_input) || isset($api_input) ) {
			return json_encode($data['topicUsr']);
		}

		$data = $this->array_utf8_encode($data);
		return response()->json($data);
	}

	/*
	* This method returns all of the threads related to the user.
	*
	*/
	public function getAllThreads($data ,$receiver_id = null, $type = NULL, $isNotification = NULL, $is_api = false, $redis_input = NULL){

		if (isset($redis_input)) {
			$my_user_id = $redis_input['user_id'];
		}elseif( $is_api ){
			$my_user_id = $data['user_id'];
		}else{
			$my_user_id = Session::get('userinfo.id');
		}

		if (isset($receiver_id) && !is_numeric($receiver_id) && !empty($receiver_id) ) {
			return false;
		}

		/******************************** New block of code starts ************************/

		// This query returns the latest topics(contact list) of the user.

		$read_from_cache = false;
		$usersTopicsAndMessages = array();

		if (isset($redis_input)) {
			$read_from_cache = false;

		}elseif (Cache::has(env('ENVIRONMENT') .'_usersTopicsAndMessages_colleges_'. $my_user_id) &&
			Cache::has(env('ENVIRONMENT') .'_usersTopicsAndMessages_colleges_count_'. $my_user_id) &&
			!isset($receiver_id)) {

			$cache_usersTopicsAndMessages = Cache::get(env('ENVIRONMENT') .'_usersTopicsAndMessages_colleges_'. $my_user_id);

			if ($cache_usersTopicsAndMessages['status'] == 'good') {
				$usersTopicsAndMessages = $cache_usersTopicsAndMessages['content'];
				$usersTopicsAndMessages_cnt = Cache::get(env('ENVIRONMENT') .'_usersTopicsAndMessages_colleges_count_'. $my_user_id);

				$read_from_cache = true;
			}

		}

		if (isset($isNotification)) {
		$read_from_cache = false;
		}

		if (!$read_from_cache || empty($usersTopicsAndMessages)) {

			// if (isset($isNotification)) {
			// 	$usersTopicsAndMessages = DB::connection('rds1')->table('college_message_threads as cmt');
			// }else{
			// 	$usersTopicsAndMessages = DB::connection('rds1')->table('college_message_threads as cmt');
			// }
			$usersTopicsAndMessages = DB::connection('bk')->table('college_message_threads as cmt');
			$usersTopicsAndMessages = $usersTopicsAndMessages->select('cml.thread_id','cml.msg', 'cml.user_id', 'cml.is_read',
																	  'cml.attachment_url', 'cml.is_deleted', 'cml.created_at',
																	  'cmtm.org_branch_id', 'cmt.id as thread_id', 'cmtm.num_unread_msg',
																	  'cmt.name as thread_name', 'cmt.campaign_id', 'cmt.has_text')
															->join("college_message_logs as cml", 'cmt.id', '=', 'cml.thread_id')
															->join('college_message_thread_members as cmtm', function($qry){
																$qry = $qry->on('cmtm.thread_id', '=', 'cmt.id');
																$qry = $qry->on('cmtm.user_id', '!=', DB::raw(0));
															})
															->leftjoin('college_campaigns as cc', 'cc.id', '=', 'cmt.campaign_id')
															->leftjoin('college_message_logs as cml2', function($join)
															{
															    $join->on('cml2.thread_id', '=', 'cmtm.thread_id');
															    $join->on('cml.id', '<', 'cml2.id');
															})
															->where('cmtm.org_branch_id', '=', $data['org_branch_id'])
															->whereRaw('cml2.id IS NULL')
															->where('cmt.is_chat', 0)
															->where('cmtm.user_id', $my_user_id)
															->where('cml.msg', 'NOT LIKE', DB::RAW('"<b>Subject: </b>%"'));// Don't show the messages that are campaigns intial message
			// if we they want to message someone, they've already messaged , we need to put that person on top
			if (isset($receiver_id)) {
				$usersTopicsAndMessages = $usersTopicsAndMessages->orderBy(DB::raw('cml.user_id ='.$receiver_id), ' DESC')
																 ->orderBy('cml.created_at', 'DESC');
			}else{
				$usersTopicsAndMessages_cnt = $usersTopicsAndMessages->orderBy('cml.created_at', 'DESC');
			}

			// Redis info just use this thread.
			if (isset($redis_input)) {
				$usersTopicsAndMessages = $usersTopicsAndMessages->where('cmt.id', $redis_input['thread_id']);
			}

			$usersTopicsAndMessages_cnt = $usersTopicsAndMessages->count();

			$usersTopicsAndMessages = $usersTopicsAndMessages->groupBy('cml.thread_id')
														     ->get();

			if (!isset($redis_input)) {
				$tmp = array();

				$tmp['content'] = $usersTopicsAndMessages;

				$tmp['status'] = 'good';

				Cache::put(env('ENVIRONMENT') .'_usersTopicsAndMessages_colleges_'. $my_user_id, $tmp, 60);

				Cache::put(env('ENVIRONMENT') .'_usersTopicsAndMessages_colleges_count_'. $my_user_id, $usersTopicsAndMessages_cnt, 60);
			}
		}
		/******************************** New block of code ends ************************/

		// If user has a department set, show results based on the filters they have set
		$filter_user = NULL;

		if (isset($data['default_organization_portal'])) {
			if ($read_from_cache && Cache::has(env('ENVIRONMENT') .'_filteredusers_colleges_'. $data['default_organization_portal']->id .'_'. $my_user_id)) {
				$filter_user = Cache::get(env('ENVIRONMENT') .'_filteredusers_colleges_'. $data['default_organization_portal']->id .'_'. $my_user_id);
			}

			if(!isset($filter_user)){
				$crf = new CollegeRecommendationFilters;
				$filter_qry = $crf->generateFilterQry($data);
				$raw_filter_qry = NULL;

				if (isset($filter_qry)) {
					DB::connection('bk')->statement('SET SESSION group_concat_max_len = 1000000;');
					$filter_qry = $filter_qry->selectRaw('GROUP_CONCAT( DISTINCT(cmtm.thread_id) SEPARATOR ",") as filterThreadId');
					$filter_qry = $filter_qry->join('college_message_thread_members as cmtm', 'cmtm.user_id', '=', 'userFilter.id')
											 ->where('cmtm.org_branch_id', $data['org_branch_id']);
					$filter_user  = $filter_qry->first();
					$filter_user = explode(",", $filter_user->filterThreadId);

					Cache::put(env('ENVIRONMENT') .'_filteredusers_colleges_'. $data['default_organization_portal']->id .'_'. $my_user_id, $filter_user, 30);
				}
			}
		}
		// end of department set
		$topicsUsr = array();

		// store all this branch user ids.
		$org_branch_user_ids_collection = OrganizationBranchPermission::where('organization_branch_id', $data['org_branch_id'])->get();

		$org_branch_user_ids = array();
		foreach ($org_branch_user_ids_collection as $key) {
			$org_branch_user_ids[] = $key->user_id;
		}
		$receiver_id_check =false;

		$uid = '';

		$this_week = Carbon::now()->startOfWeek();
		$takeCount = 0;
		$offsetCount = 0;
		$foreachCount = 0; // num of foreach count the loop iterates
		$loopCount = 0; // num of loops count that have took data


		if (isset($isNotification)) {
			$takeCount = 5;
		}elseif (Cache::has(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore')) {
			$takeCount = Cache::get(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore');
		}else{
			$takeCount = self::NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD;
		}
		if(isset($usersTopicsAndMessages)){

			foreach ($usersTopicsAndMessages as $k) {
				$topicsUsr = array();
				// if filter user array is set, and user id is in the filter user array
				// then we can show the message, else we skip the user.
				if (isset($filter_user) && !in_array($k->thread_id, $filter_user)) {
					continue;
				}

				if (Cache::has(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data')){

					Cache::forget(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data');
				}

				$this->show_thread_campaign = true;

				if ((isset($k->campaign_id) && !empty($k->campaign_id)) ) {
					$this->handleCampaigns($data, $k->campaign_id, $k->msg);
				}

				if (!$this->show_thread_campaign) {
					continue;
				}

				$foreachCount++;

				if ($offsetCount !=0 && $foreachCount <= $offsetCount) {
					continue;
				}

				if ($takeCount   !=0 && $takeCount == $loopCount) {
					break;
				}

				$loopCount++;

				$topicsUsr['is_campaign'] = (isset($k->campaign_id) && !empty($k->campaign_id)) ? 1 : 0 ;
				$topicsUsr['has_text']    = (isset($k->has_text) && !empty($k->has_text)) ? 1 : 0 ;

				$topicsUsr['thread_total_count'] = $usersTopicsAndMessages_cnt;

				if (Cache::has(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore')) {
					$topicsUsr['session_num'] =  Cache::get(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore');
				}else{
					$topicsUsr['session_num'] =  -1;
				}

				$user_member_of_thread = false;

				$topicsUsr['Name'] = '';
				$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

				$dt = Carbon::parse($k->created_at);
				$dateDiff = $dt->diffInDays($this_week);
				$dateTimeStr = $dt->toDayDateTimeString();

				// if the message was created this week, follow this format
				if ($dateDiff < 7) {
					$str_to_remove = $this->getStringBetween($dateTimeStr, ',', ', '.date("Y"));
					$tmp = str_replace($str_to_remove, "", $dateTimeStr);
					$tmp = str_replace(", ".date("Y"), " @", $tmp);
					$tmp = str_replace(",", "", $tmp);
				}else{
					$tmp = substr($dateTimeStr, 4);
					$tmp = str_replace(", ".date("Y"), " @", $tmp);
				}

				$topicsUsr['formatted_date'] = $tmp;

				$topicsUsr['date'] = date_format(date_create($k->created_at), 'Y-m-d H:i:s');

				// $topicsUsr['date'] = $this->xTimeAgo(date_format($k->created_at, 'Y-m-d H:i:s'), date("Y-m-d H:i:s"));

				if(strlen($k->msg) > 26){
					$topicsUsr['msg'] = substr(strip_tags($k->msg), 0, 26) . "...";
				}else{
					$topicsUsr['msg'] = strip_tags($k->msg);
				}

				$topicsUsr['thread_name'] = $k->thread_name;
				//$topicsUsr['whosent_id'] = $k->user_id;
				$topicsUsr['thread_id'] = $k->thread_id;
				$topicsUsr['thread_type_id'] = $k->user_id;
				$topicsUsr['thread_type'] = 'users';

				// Add is this person online right now!
				$topicsUsr['is_online'] = $this->is_user_online($k->thread_id, $k->user_id, 30);


				// Add the list of thread members to the mix

				$topicsUsr['thread_members'] = array();
				$threadMembers  = CollegeMessageThreadMembers::where('thread_id', '=', $k->thread_id);


				//***************************IMPORTANT********************************//
				// if the number of thread members is one, it MUST be a user to college/prep
				// so we are going to set the thread type and id according to them

				$threadMembersCount = $threadMembers->count();

				$threadMembers = $threadMembers->orderBy('created_at', 'DESC')
												->groupBy('user_id')
												->select('user_id', 'num_unread_msg', 'updated_at', 'is_list_user')
												->distinct()
												->get();

				if($threadMembersCount == 1){
					foreach ($threadMembers as $tm) {
						if ($tm->is_list_user == 1) {
							$user = ListUser::find($tm->user_id);
						}else{
							$user = User::find($tm->user_id);
						}


						if (isset($user)) {

							$uid = $tm->user_id;

							$topicsUsr['Name'] = $user->fname . ' ' . $user->lname;

							if(!isset($user->profile_img_loc) || $user->profile_img_loc == ""){
								$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

							}else{
								$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

							}
						}

					}
				}

				//***************************IMPORTANT Finish********************************//

				foreach ($threadMembers as $tm) {

					if(!in_array($tm->user_id, $org_branch_user_ids)){

						if ($tm->is_list_user == 1) {
							$user = ListUser::find($tm->user_id);
						}else{
							$user = User::find($tm->user_id);
						}

						if(isset($user))  {

							$uid = $tm->user_id;

							$topicsUsr['Name'] = $user->fname . ' ' . $user->lname;

							if(!isset($user->profile_img_loc) || $user->profile_img_loc == ""){
								$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

							}else{
								$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

							}

							$arr = array();

							// if ($tm->is_list_user == 1) {
							// 	$user_member = ListUser::find($tm->user_id);
							// }else{
							// 	$user_member = User::find($tm->user_id);
							// }

							// $arr['Name'] = $user->fname. " ". $user->lname;
							$arr['Name'] = $topicsUsr['Name'];
							$arr['img'] = $topicsUsr['img'];

							// if(!isset($user->profile_img_loc) || $user->profile_img_loc == ""){
							// 	$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

							// }else{
							// 	$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

							// }

							// See if the user has read the message or not
							$topicsUsr['msg_read_time'] = -1;

							if ($tm->num_unread_msg == 0) {

								$dt = Carbon::parse($tm->updated_at);
								$dateDiff = $dt->diffInDays($this_week);
								$dateTimeStr = $dt->toDayDateTimeString();

								// if the message was read this week, follow this format
								if ($dateDiff < 7) {
									$str_to_remove = $this->getStringBetween($dateTimeStr, ',', ', '.date("Y"));
									$tmp = str_replace($str_to_remove, "", $dateTimeStr);
									$tmp = str_replace(", ".date("Y"), " @", $tmp);
									$tmp = str_replace(",", "", $tmp);
								}else{
									$tmp = substr($dateTimeStr, 4);
									$tmp = str_replace(", ".date("Y"), " @", $tmp);
								}

								$topicsUsr['msg_read_time'] = $tmp;
							}
							// End of message read or not

							$arr['user_id'] = $user->id;
							$topicsUsr['thread_members'][] = $arr;
						}
					}


					if($tm->user_id == $my_user_id){
						$topicsUsr['num_unread_msg'] = $tm->num_unread_msg;
					}
					/*
					else{


						$arr = array();

						$user = User::find($tm->user_id);

						$arr['Name'] = $user_member->fname. " ". $user_member->lname;

						if($user_member->profile_img_loc == ""){
							$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

						}else{
							$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user_member->profile_img_loc;

						}

						$arr['user_id'] = $user_member->id;
						$topicsUsr['thread_members'][] = $arr;
					}
					*/
					if($receiver_id == $tm->user_id){
						$user_member_of_thread = true;
					}
				}
				// The Composed user was in the contact list,
				// so we are going to move them to top of array
				//$data['topicUsr'][] = $topicsUsr;
				// if ($topicsUsr['has_text'] == 1) {
				// 	$topicsUsr['img'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/text-message-dark.png';
				// }elseif($topicsUsr['is_campaign'] == 1){
				// 	$topicsUsr['img'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/mass-message-icon.png';
				// }


				if($receiver_id == $uid){

					// We need to know who is the sticky receiver user_id if we are trying to stick them to the top.
					// This condition only happens on a case like /admin/messages/{{Sticky user_id}}
					if( isset($receiver_id) && !empty($receiver_id) ){
						$topicsUsr['receiver_id'] = (int)$receiver_id;
					}

					if (isset($data['topicUsr'] )) {
						array_unshift($data['topicUsr'] , $topicsUsr);
					}else{
						$data['topicUsr'][] = $topicsUsr;
					}

					$receiver_id_check = true;
				}else{
					$data['topicUsr'][] = $topicsUsr;
				}


				Cache::put(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data', $topicsUsr, 10);
			}

		}

		// The composed user was not on the contact list, so we manually gonna add them to top of array
		if(!$receiver_id_check && $receiver_id!= NULL){

			$d = new DateTime('today');

			$user = User::find($receiver_id);
			$topicsUsr['Name'] = $user->fname. " ". $user->lname;
			$topicsUsr['thread_name'] = $user->fname. " ". $user->lname;

			if($user->profile_img_loc == ""){
				$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

			}else{
				$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

			}

			$topicsUsr['date'] = date_format($d, 'Y-m-d');
			$topicsUsr['msg'] = "";
			$topicsUsr['thread_type_id'] = $receiver_id;
			$topicsUsr['thread_type'] = 'users';
			$topicsUsr['is_read'] = 0;
			$topicsUsr['thread_id'] = -1;
			$topicsUsr['thread_members'] = array();
			$topicsUsr['num_unread_msg'] = 0;

			if( isset($receiver_id) && !empty($receiver_id) ){
				$topicsUsr['receiver_id'] = (int)$receiver_id;
			}

			if ($type == 'inquiry-txt') {
				$topicsUsr['has_text'] = 1;
			}else{
				$topicsUsr['has_text'] = 0;
			}


			if (isset($data['topicUsr'] )) {
				array_unshift($data['topicUsr'] , $topicsUsr);
			}else{
				$data['topicUsr'][] = $topicsUsr;
			}

			//$data['topicUsr'][] = $topicsUsr;
		}


		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();

		if (!isset($data['topicUsr']) ) {

			$data['topicUsr'] = array();
		}
		return $data['topicUsr'];
	}

	/*
	 * This method returns all of the threads related to the user.
	 *
	 */
	public function getAllThreadsAgency($data ,$receiver_id = null, $type = NULL, $isNotification = NULL, $is_api = false, $redis_input = NULL){

		if (isset($redis_input)) {
            $my_user_id = $redis_input['user_id'];
        }elseif( $is_api ){
            $my_user_id = $data['user_id'];
        }else{
            $my_user_id = Session::get('userinfo.id');
        }

        if (isset($receiver_id) && !is_numeric($receiver_id) && !empty($receiver_id) ) {
            return false;
        }


		/******************************** New block of code starts ************************/

		// This query returns the latest topics(contact list) of the user.
		$read_from_cache = false;
		$usersTopicsAndMessages = array();

		if (isset($redis_input)) {
            $read_from_cache = false;

        }elseif (Cache::has(env('ENVIRONMENT') .'_usersTopicsAndMessages_agencies_'. $my_user_id) &&
            Cache::has(env('ENVIRONMENT') .'_usersTopicsAndMessages_agencies_count_'. $my_user_id) &&
            !isset($receiver_id)) {

            $cache_usersTopicsAndMessages = Cache::get(env('ENVIRONMENT') .'_usersTopicsAndMessages_agencies_'. $my_user_id);

            if ($cache_usersTopicsAndMessages['status'] == 'good') {
                $usersTopicsAndMessages = $cache_usersTopicsAndMessages['content'];
                $usersTopicsAndMessages_cnt = Cache::get(env('ENVIRONMENT') .'_usersTopicsAndMessages_agencies_count_'. $my_user_id);

                $read_from_cache = true;
            }

        }

		if (isset($isNotification)) {
			$read_from_cache = false;
		}

		if (!$read_from_cache || empty($usersTopicsAndMessages)) {

            // if (isset($isNotification)) {
            //  $usersTopicsAndMessages = DB::connection('rds1')->table('college_message_threads as cmt');
            // }else{
            //  $usersTopicsAndMessages = DB::connection('rds1')->table('college_message_threads as cmt');
            // }
            $usersTopicsAndMessages = DB::connection('bk')->table('college_message_threads as cmt');
            $usersTopicsAndMessages = $usersTopicsAndMessages->select('cml.thread_id','cml.msg', 'cml.user_id', 'cml.is_read',
                                                                      'cml.attachment_url', 'cml.is_deleted', 'cml.created_at',
                                                                      'cmtm.org_branch_id', 'cmt.id as thread_id', 'cmtm.num_unread_msg',
                                                                      'cmt.name as thread_name', 'cmt.campaign_id', 'cmt.has_text', 'cmtm.agency_id')
                                                            ->join("college_message_logs as cml", 'cmt.id', '=', 'cml.thread_id')
                                                            ->join('college_message_thread_members as cmtm', function($qry){
                                                                $qry = $qry->on('cmtm.thread_id', '=', 'cmt.id');
                                                                $qry = $qry->on('cmtm.user_id', '!=', DB::raw(0));
                                                            })
                                                            ->leftjoin('college_campaigns as cc', 'cc.id', '=', 'cmt.campaign_id')
                                                            ->leftjoin('college_message_logs as cml2', function($join)
                                                            {
                                                                $join->on('cml2.thread_id', '=', 'cmtm.thread_id');
                                                                $join->on('cml.id', '<', 'cml2.id');
                                                            })
                                                            // ->where('cmtm.org_branch_id', '=', $data['org_branch_id'])
                                                            ->where('cmtm.agency_id', '=', $data['agency_collection']->agency_id)
                                                            ->whereRaw('cml2.id IS NULL')
                                                            ->where('cmt.is_chat', 0)
                                                            ->where('cmtm.user_id', $my_user_id)
                                                            ->where('cml.msg', 'NOT LIKE', DB::RAW('"<b>Subject: </b>%"'));// Don't show the messages that are campaigns intial message
            // if we they want to message someone, they've already messaged , we need to put that person on top
            if (isset($receiver_id)) {
                $usersTopicsAndMessages = $usersTopicsAndMessages->orderBy(DB::raw('cml.user_id ='.$receiver_id), ' DESC')
                                                                 ->orderBy('cml.created_at', 'DESC');
            }else{
                $usersTopicsAndMessages_cnt = $usersTopicsAndMessages->orderBy('cml.created_at', 'DESC');
            }

            // Redis info just use this thread.
            if (isset($redis_input)) {
                $usersTopicsAndMessages = $usersTopicsAndMessages->where('cmt.id', $redis_input['thread_id']);
            }

            $usersTopicsAndMessages_cnt = $usersTopicsAndMessages->count();

            $usersTopicsAndMessages = $usersTopicsAndMessages->groupBy('cml.thread_id')
                                                             ->get();

            if (!isset($redis_input)) {
                $tmp = array();

                $tmp['content'] = $usersTopicsAndMessages;

                $tmp['status'] = 'good';

                Cache::put(env('ENVIRONMENT') .'_usersTopicsAndMessages_agencies_'. $my_user_id, $tmp, 60);

                Cache::put(env('ENVIRONMENT') .'_usersTopicsAndMessages_agencies_count_'. $my_user_id, $usersTopicsAndMessages_cnt, 60);
            }
        }


		/******************************** New block of code ends ************************/
		$topicsUsr = array();

		// store all this branch user ids.
		$agency = new Agency;

		$org_branch_user_ids_collection = $agency->getAgencyUsers();

		$org_branch_user_ids = array();
		foreach ($org_branch_user_ids_collection as $key) {
			$org_branch_user_ids[] = $key->user_id;
		}
		$receiver_id_check =false;

		$uid = '';

		$this_week = Carbon::now()->startOfWeek();
        $takeCount = 0;
        $offsetCount = 0;
        $foreachCount = 0; // num of foreach count the loop iterates
        $loopCount = 0; // num of loops count that have took data


        if (isset($isNotification)) {
            $takeCount = 5;
        }elseif (Cache::has(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore')) {
            $takeCount = Cache::get(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore');
        }else{
            $takeCount = self::NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD;
        }

		if(isset($usersTopicsAndMessages)){

			foreach ($usersTopicsAndMessages as $k) {

				$topicsUsr = array();
                // if filter user array is set, and user id is in the filter user array
                // then we can show the message, else we skip the user.
                if (isset($filter_user) && !in_array($k->thread_id, $filter_user)) {
                    continue;
                }

                if (Cache::has(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data')){

                    Cache::forget(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data');
                }

                $this->show_thread_campaign = true;

                if ((isset($k->campaign_id) && !empty($k->campaign_id)) ) {
                    $this->handleCampaigns($data, $k->campaign_id, $k->msg);
                }

                if (!$this->show_thread_campaign) {
                    continue;
                }

                $foreachCount++;

                if ($offsetCount !=0 && $foreachCount <= $offsetCount) {
                    continue;
                }

                if ($takeCount   !=0 && $takeCount == $loopCount) {
                    break;
                }

                $loopCount++;

                $topicsUsr['is_campaign'] = (isset($k->campaign_id) && !empty($k->campaign_id)) ? 1 : 0 ;
                $topicsUsr['has_text']    = (isset($k->has_text) && !empty($k->has_text)) ? 1 : 0 ;

                $topicsUsr['thread_total_count'] = $usersTopicsAndMessages_cnt;

                if (Cache::has(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore')) {
                    $topicsUsr['session_num'] =  Cache::get(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore');
                }else{
                    $topicsUsr['session_num'] =  -1;
                }

                $user_member_of_thread = false;

				$topicsUsr['Name'] = '';
				$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

				$dt = Carbon::parse($k->created_at);
                $dateDiff = $dt->diffInDays($this_week);
                $dateTimeStr = $dt->toDayDateTimeString();

                // if the message was created this week, follow this format
                if ($dateDiff < 7) {
                    $str_to_remove = $this->getStringBetween($dateTimeStr, ',', ', '.date("Y"));
                    $tmp = str_replace($str_to_remove, "", $dateTimeStr);
                    $tmp = str_replace(", ".date("Y"), " @", $tmp);
                    $tmp = str_replace(",", "", $tmp);
                }else{
                    $tmp = substr($dateTimeStr, 4);
                    $tmp = str_replace(", ".date("Y"), " @", $tmp);
                }

                $topicsUsr['formatted_date'] = $tmp;

				$topicsUsr['date'] = date_format(date_create($k->created_at), 'Y-m-d H:i:s');
				// $topicsUsr['date'] = $this->xTimeAgo(date_format($k->created_at, 'Y-m-d H:i:s'), date("Y-m-d H:i:s"));

				if(strlen($k->msg) > 26){
					$topicsUsr['msg'] = substr($k->msg, 0, 26) . "...";
				}else{
					$topicsUsr['msg'] = $k->msg;
				}

				$topicsUsr['thread_name'] = $k->thread_name;
				//$topicsUsr['whosent_id'] = $k->user_id;
				$topicsUsr['thread_id'] = $k->thread_id;
				$topicsUsr['thread_type_id'] = $k->user_id;
				$topicsUsr['thread_type'] = 'users';

				// Add is this person online right now!
				$topicsUsr['is_online'] = $this->is_user_online($k->thread_id, $k->user_id, 30);


				// Add the list of thread members to the mix

				$topicsUsr['thread_members'] = array();
				$threadMembers  = CollegeMessageThreadMembers::where('thread_id', '=', $k->thread_id);


                //***************************IMPORTANT********************************//
                // if the number of thread members is one, it MUST be a user to college/prep
                // so we are going to set the thread type and id according to them

                $threadMembersCount = $threadMembers->count();

                $threadMembers = $threadMembers->orderBy('created_at', 'DESC')
                                                ->groupBy('user_id')
                                                ->select('user_id', 'num_unread_msg', 'updated_at', 'is_list_user')
                                                ->distinct()
                                                ->get();

                if($threadMembersCount == 1){
                    foreach ($threadMembers as $tm) {
                        if ($tm->is_list_user == 1) {
                            $user = ListUser::find($tm->user_id);
                        }else{
                            $user = User::find($tm->user_id);
                        }


                        if (isset($user)) {

                            $uid = $tm->user_id;

                            $topicsUsr['Name'] = $user->fname . ' ' . $user->lname;

                            if(!isset($user->profile_img_loc) || $user->profile_img_loc == ""){
                                $topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

                            }else{
                                $topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

                            }
                        }

                    }
                }

				//***************************IMPORTANT Finish********************************//

				foreach ($threadMembers as $tm) {

					if(!in_array($tm->user_id, $org_branch_user_ids)){

						if ($tm->is_list_user == 1) {
                            $user = ListUser::find($tm->user_id);
                        }else{
                            $user = User::find($tm->user_id);
                        }

						if(isset($user))  {

                            $uid = $tm->user_id;

                            $topicsUsr['Name'] = $user->fname . ' ' . $user->lname;

                            if(!isset($user->profile_img_loc) || $user->profile_img_loc == ""){
                                $topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

                            }else{
                                $topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

                            }

                            $arr = array();

                            // if ($tm->is_list_user == 1) {
                            //  $user_member = ListUser::find($tm->user_id);
                            // }else{
                            //  $user_member = User::find($tm->user_id);
                            // }

                            // $arr['Name'] = $user->fname. " ". $user->lname;
                            $arr['Name'] = $topicsUsr['Name'];
                            $arr['img'] = $topicsUsr['img'];

                            // if(!isset($user->profile_img_loc) || $user->profile_img_loc == ""){
                            //  $arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

                            // }else{
                            //  $arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

                            // }

                            // See if the user has read the message or not
                            $topicsUsr['msg_read_time'] = -1;

                            if ($tm->num_unread_msg == 0) {

                                $dt = Carbon::parse($tm->updated_at);
                                $dateDiff = $dt->diffInDays($this_week);
                                $dateTimeStr = $dt->toDayDateTimeString();

                                // if the message was read this week, follow this format
                                if ($dateDiff < 7) {
                                    $str_to_remove = $this->getStringBetween($dateTimeStr, ',', ', '.date("Y"));
                                    $tmp = str_replace($str_to_remove, "", $dateTimeStr);
                                    $tmp = str_replace(", ".date("Y"), " @", $tmp);
                                    $tmp = str_replace(",", "", $tmp);
                                }else{
                                    $tmp = substr($dateTimeStr, 4);
                                    $tmp = str_replace(", ".date("Y"), " @", $tmp);
                                }

                                $topicsUsr['msg_read_time'] = $tmp;
                            }
                            // End of message read or not

                            $arr['user_id'] = $user->id;
                            $topicsUsr['thread_members'][] = $arr;
                        }
					}


					if($tm->user_id == $my_user_id){
						$topicsUsr['num_unread_msg'] = $tm->num_unread_msg;
					}
					/*
					else{


						$arr = array();

						$user_member = User::find($tm->user_id);

						$arr['Name'] = $user_member->fname. " ". $user_member->lname;

						if($user_member->profile_img_loc == ""){
							$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

						}else{
							$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user_member->profile_img_loc;

						}

						$arr['user_id'] = $user_member->id;
						$topicsUsr['thread_members'][] = $arr;
					}
					*/
					if($receiver_id == $tm->user_id){
						$user_member_of_thread = true;
					}
				}
				// The Composed user was in the contact list,
				// so we are going to move them to top of array
				//$data['topicUsr'][] = $topicsUsr;


				if($receiver_id == $uid){

                    // We need to know who is the sticky receiver user_id if we are trying to stick them to the top.
                    // This condition only happens on a case like /admin/messages/{{Sticky user_id}}
                    if( isset($receiver_id) && !empty($receiver_id) ){
                        $topicsUsr['receiver_id'] = (int)$receiver_id;
                    }

                    if (isset($data['topicUsr'] )) {
                        array_unshift($data['topicUsr'] , $topicsUsr);
                    }else{
                        $data['topicUsr'][] = $topicsUsr;
                    }

                    $receiver_id_check = true;
                }else{
                    $data['topicUsr'][] = $topicsUsr;
                }


				Cache::put(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data', $topicsUsr, 10);
			}

		}



		// The composed user was not on the contact list, so we manually gonna add them to top of array
		if(!$receiver_id_check && $receiver_id!= NULL){

            $d = new DateTime('today');

            $user = User::find($receiver_id);
            $topicsUsr['Name'] = $user->fname. " ". $user->lname;
            $topicsUsr['thread_name'] = $user->fname. " ". $user->lname;

            if($user->profile_img_loc == ""){
                $topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

            }else{
                $topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

            }

            $topicsUsr['date'] = date_format($d, 'Y-m-d');
            $topicsUsr['msg'] = "";
            $topicsUsr['thread_type_id'] = $receiver_id;
            $topicsUsr['thread_type'] = 'users';
            $topicsUsr['is_read'] = 0;
            $topicsUsr['thread_id'] = -1;
            $topicsUsr['thread_members'] = array();
            $topicsUsr['num_unread_msg'] = 0;

            if( isset($receiver_id) && !empty($receiver_id) ){
                $topicsUsr['receiver_id'] = (int)$receiver_id;
            }

            if ($type == 'agency-txt') {
                $topicsUsr['has_text'] = 1;
            }else{
                $topicsUsr['has_text'] = 0;
            }


            if (isset($data['topicUsr'] )) {
                array_unshift($data['topicUsr'] , $topicsUsr);
            }else{
                $data['topicUsr'][] = $topicsUsr;
            }

            //$data['topicUsr'][] = $topicsUsr;
        }

		/*
		echo "<pre>";
		var_dump($data);
		echo "</pre>";
		*/
		if (!isset($data['topicUsr']) ) {

			$data['topicUsr'] = array();
		}
		return $data['topicUsr'];
	}

	/**
	 * handle a campaign thread on left hand side
	 * @param $data: holds all of the information that we need
	 * @return $data
	 */
	private function handleCampaigns($data, $campaign_id, $msg){

		// if(Session::has(env('ENVIRONMENT').'_'.$data['user_id'].'_'.$campaign_id)){

		// }

		$cc = CollegeCampaign::find($campaign_id);

		if (isset($cc)) {
			# code...
			$cc_msg = '<b>Subject: </b>'.$cc->subject."<br>".$cc->body;

			if ($msg == $cc_msg) {
				$this->show_thread_campaign = false;
			}
		}else{

			if (strpos($msg, '<b>Subject: </b>') !== false) {
				$this->show_thread_campaign = false;
			}

		}

	}

}
