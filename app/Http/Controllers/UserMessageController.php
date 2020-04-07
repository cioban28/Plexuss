<?php

namespace App\Http\Controllers;

use Request, DB, Session, Validator, DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\CollegeMessageThreads, App\CollegeMessageThreadMembers, App\OrganizationBranch, App\OrganizationBranchPermission, App\College;
use App\Agency, App\CollegeRecommendationFilters, App\User, App\UsersIpLocation;


class UserMessageController extends Controller
{

	protected $userThreadIds ='';
	const NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD = 10;

	/*
	* Posting a new message to another user
	*
	*/
	// public function postMessage($thread_id = NULL, $thread_type_id = NULL, $type = NULL){

	// 	$inputs = Request::all();

	// 	$valInputs = array(
	// 		'thread_id' => $thread_id,
	// 		'thread_type_id' => $thread_type_id,
	// 		'type' => $type,
	// 		'message' => $inputs['message']);

	// 	$valFilters =array (
	// 		'thread_id' => 'required',
	// 		'thread_type_id' => 'required|numeric',
	// 		'type' => 'required|alpha',
	// 		'message' => 'required'
	// 		);

	// 	$validator = Validator::make( $valInputs, $valFilters );
	// 	if ($validator->fails()){
	// 		$messages = $validator->messages();
	// 		return $messages;
	// 	}

	// 	$message = $inputs['message'];

	// 	//remove all html tags from user entry.
	// 	$message = trim(preg_replace("/<[^>]*>/", ' ', $message));
	// 	$message = trim(preg_replace("/\s{2,}/", ' ', $message));

	// 	//return $thread_id . " ". $thread_type_id . " " . $type;
	// 	$viewDataController = new ViewDataController();
	// 	$data = $viewDataController->buildData();
	// 	$data['currentPage'] = "portal-messages";

	// 	$my_user_id = Session::get('userinfo.id');

	// 	// Get topic id for this conversation
	// 	$college_message_thread_id = $thread_id;


	// 	//if Topic thread doesn't exitst, create a new topic.
	// 	if( $college_message_thread_id == -1){



	// 		if($type == 'college'){



	// 			$ob = OrganizationBranch::where('school_id', $thread_type_id)->first();

	// 			if(!$ob){
	// 				return "organization has not been setup";
	// 			}

	// 			// Add a new thread.
	// 			$cmt = new CollegeMessageThreads;
	// 			$cmt->save();
	// 			$college_message_thread_id = $cmt->id;



	// 			//*********************************important
	// 			// this piece adds all of the "current" member of organizations to the thread.
	// 			$org_branch_user_ids_collection = OrganizationBranchPermission::where('organization_branch_id', $ob->id)->get();

	// 			foreach ($org_branch_user_ids_collection as $key) {
	// 				$cmtm = new CollegeMessageThreadMembers;

	// 				$cmtm->user_id = $key->user_id;
	// 				$cmtm->org_branch_id = $ob->id;
	// 				$cmtm->thread_id = $college_message_thread_id;
	// 				$cmtm->save();
	// 			}

	// 			$cmtm = new CollegeMessageThreadMembers;

	// 			$cmtm->user_id = $my_user_id ;
	// 			$cmtm->org_branch_id = $ob->id;
	// 			$cmtm->thread_id = $college_message_thread_id;
	// 			$cmtm->save();

	// 		}else{

	// 			// Add a new thread.
	// 			$cmt = new CollegeMessageThreads;
	// 			$cmt->save();
	// 			$college_message_thread_id = $cmt->id;

	// 			$cmtm = new CollegeMessageThreadMembers;

	// 			$cmtm->user_id = $my_user_id ;
	// 			$cmtm->org_branch_id = -1;
	// 			$cmtm->thread_id = $college_message_thread_id;
	// 			$cmtm->save();

	// 			$cmtm = new CollegeMessageThreadMembers;

	// 			$cmtm->user_id = $thread_type_id ;
	// 			$cmtm->org_branch_id = -1;
	// 			$cmtm->thread_id = $college_message_thread_id;
	// 			$cmtm->save();

	// 		}

	// 	}else{

	// 		// if the user is not part of thread members, add him to the mix.
	// 		$cmt = CollegeMessageThreadMembers::where('user_id', '=', $my_user_id)
	// 			->where('thread_id', '=', $college_message_thread_id)
	// 			->first();

	// 		if(!$cmt){

	// 			$cmtm = new CollegeMessageThreadMembers;
	// 			$cmtm->user_id = $my_user_id;
	// 			$cmtm->org_branch_id = $data['org_branch_id'];
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


	// 	//Reset all the cache values of each member of each thread, and increment the num of unread msgs
	// 	$thread_members = CollegeMessageThreadMembers::where('thread_id', '=', $college_message_thread_id)
	// 						->where('user_id', '!=', $my_user_id)
	// 						->pluck('user_id');

	// 	foreach ($thread_members as $k) {
	// 		Cache::forget(env('ENVIRONMENT') .'_'.$k.'_msg_thread_ids');
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
	// 	$this->getAllThreads($data, "", "");

	// 	$data['thread_id'] = $college_message_thread_id;
	// 	$data['stickyUsr'] = "";

	// 	return $college_message_thread_id;
	// }


	/*
	* Returns the list of threads on heart beat.
	*
	*
	*/
	public function getThreadListHeartBeat($receiver_id = NULL, $type = NULL, $redis_input = NULL, $thread_id_param = NULL, $api_input = null){


		if( isset($api_input) ){
			$input = $api_input;
		}else{
			$input = Request::all();
		}

		$valInputs = array(
			'receiver_id' => $receiver_id,
			'type' => $type);

		$valFilters =array (
			'receiver_id' => 'alpha_dash|nullable',
			'type' => 'alpha|nullable'
			);


		$validator = Validator::make( $valInputs, $valFilters );
		if ($validator->fails()){
			$messages = $validator->messages();
			return $messages;
		}

		if (isset($redis_input)) {
			$data = array();
			$data['user_id'] = $redis_input['user_id'];

		}elseif( isset($api_input) ){
			$data = $api_input;

		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$sender_user_id = Session::get('userinfo.id');
		}

		if( isset($api_input) ){
			$data['topicUsr'] = $this->getAllThreads($data, $receiver_id, $type, NULL, true, $redis_input, $thread_id_param);
		}else{
			$data['topicUsr'] = $this->getAllThreads($data, $receiver_id, $type, NULL, NULL, $redis_input, $thread_id_param);	
		}
		
		$data['currentPage'] = "portal-messages";

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
		$dt = array();
		$dt['topicUsr'] = $data['topicUsr'];

		if ( isset($redis_input) || isset($api_input) ) {
			return json_encode($dt);
		}

		// return json_encode($dt);

		// $dt = $this->array_utf8_encode($dt);
		// return response()->json($dt);

		return response()->json($dt, 200, [], JSON_UNESCAPED_UNICODE);
	}

	/*
	* This method returns all of the threads related to the user.
	*
	*/
	public function getAllThreads($data ,$receiver_id = null, $type = null, $isNotification = NULL,
								  $is_api = false, $redis_input = NULL, $thread_id_param = NULL, $this_thread_id = NULL){

		if (isset($redis_input)) {
			$my_user_id = $redis_input['user_id'];
		}elseif( $is_api ){
			$my_user_id = $data['user_id'];
		}else{
			$my_user_id = Session::get('userinfo.id');
		}

		if (isset($receiver_id) && !is_numeric($receiver_id) && !empty($receiver_id)) {
			return false;
		}

		/******************************** New block of code starts ************************/

		// This query returns the latest topics(contact list) of the user.

		$read_from_cache = false;

		if (isset($redis_input)) {
			$read_from_cache = false;

		}elseif (Cache::has(env('ENVIRONMENT') .'_usersTopicsAndMessages_users_'. $my_user_id) &&
			Cache::has(env('ENVIRONMENT') .'_usersTopicsAndMessages_users_count_'. $my_user_id) &&
			!isset($receiver_id)) {

			$cache_usersTopicsAndMessages = Cache::get(env('ENVIRONMENT') .'_usersTopicsAndMessages_users_'. $my_user_id);

			if ($cache_usersTopicsAndMessages['status'] == 'good') {
				$usersTopicsAndMessages = $cache_usersTopicsAndMessages['content'];
				$usersTopicsAndMessages_cnt = Cache::get(env('ENVIRONMENT') .'_usersTopicsAndMessages_users_count_'. $my_user_id);

				$read_from_cache = true;
			}

		}
		$read_from_cache = false;
		if (!$read_from_cache) {

			$usersTopicsAndMessages = DB::connection('rds1')->table('college_message_threads as cmt')
										->join('college_message_thread_members as cmtm', 'cmtm.thread_id', '=', 'cmt.id')
										->leftjoin(DB::raw("(
													SELECT
														cml.*
													FROM
														college_message_logs as cml
													JOIN college_message_thread_members as cmtm ON(
														cmtm.thread_id = cml.thread_id
														OR cml.id is NULL
													)
													where
														cmtm.user_id = ".$my_user_id."
													ORDER BY
														cml.id DESC
												) as cml"), 'cmt.id', '=', 'cml.thread_id')
										// ->join("college_message_logs as cml", 'cmt.id', '=', 'cml.thread_id')
										// ->join('users as u', 'u.id', '=', 'cml.user_id')
										// ->leftjoin('college_message_logs as cml2', function($join)
										// {
										//     $join->on('cml2.thread_id', '=', 'cmtm.thread_id');
										//     $join->on('cml.id', '<', 'cml2.id');
										// })
										// ->whereRaw('cml2.id IS NULL')

										->where('cmt.is_chat', 0)
										->where('cmtm.user_id', $my_user_id)
										->where('cmtm.is_list_user', 0)

										->select('cml.thread_id','cml.msg', 'cml.user_id', 'cml.is_read', 'cml.attachment_url', 
												 'cml.is_deleted', 'cml.created_at', 'cml.post_id', 'cml.share_article_id',
											'cmt.id as thread_id', 'cmtm.num_unread_msg',
											'cmt.name as thread_name', 'cmt.receiver_user_id as thread_receiver_user_id', 
											'cmtm.org_branch_id', 'cmtm.agency_id', 
											DB::raw("IF(
															cml.id is not NULL ,
															`cml`.`created_at` ,
															cmt.created_at
														) as dt"));

			isset($this_thread_id) ? $usersTopicsAndMessages = $usersTopicsAndMessages->where('cmt.id', $this_thread_id) : NULL;

			// If I'm not My Counselor Myself, do the following
			if ($my_user_id != 1408142) {
				$my_counselor_thread_id = $this->getMyCounselorThread();
				$usersTopicsAndMessages->orderBy(DB::raw('cmt.id ='.$my_counselor_thread_id), ' DESC');
			}else{
				$usersTopicsAndMessages->where('cml.msg', 'NOT LIKE', 'Hi %, can I be of help?');
			}

			isset($thread_id_param) ? $usersTopicsAndMessages->orderBy(DB::raw('cmt.id ='.$thread_id_param), ' DESC') : NULL;
			// if we they want to message someone, they've already messaged , we need to put that person on top
			if (isset($receiver_id)) {
				$usersTopicsAndMessages = $usersTopicsAndMessages->orderBy('dt', 'DESC');
			}else{
				$usersTopicsAndMessages_cnt = $usersTopicsAndMessages->orderBy('dt', 'DESC');
			}

			// Redis info just use this thread.
			if (isset($redis_input)) {
				$usersTopicsAndMessages = $usersTopicsAndMessages->where('cmt.id', $redis_input['thread_id']);
			}

			$usersTopicsAndMessages_cnt = $usersTopicsAndMessages->count();

			// TEMPORARLY LIMIT  NUMBER OF THREADS SHOWN
			// $usersTopicsAndMessages = $usersTopicsAndMessages->take(30);
			// $usersTopicsAndMessages = $usersTopicsAndMessages->groupBy('cml.thread_id')
			// 												 ->get();

			$usersTopicsAndMessages = $usersTopicsAndMessages->groupBy('cmt.id')
															 // ->take(30)
															 // ->get()
															 ->paginate(10);

			if (!isset($redis_input)) {
				$tmp = array();

				$tmp['content'] = $usersTopicsAndMessages;

				$tmp['status'] = 'good';

				Cache::put(env('ENVIRONMENT') .'_usersTopicsAndMessages_users_'. $my_user_id, $tmp, 60);
				Cache::put(env('ENVIRONMENT') .'_usersTopicsAndMessages_users_count_'. $my_user_id, $usersTopicsAndMessages_cnt, 60);
			}
		}


		/******************************** New block of code ends ************************/

		// If user has a department set, show results based on the filters they have set
		$filter_user = NULL;

		if (isset($data['default_organization_portal'])) {
			if ($read_from_cache && Cache::has(env('ENVIRONMENT') .'_filteredusers_users_'. $data['default_organization_portal']->id .'_'. $my_user_id)) {
				$filter_user = Cache::get(env('ENVIRONMENT') .'_filteredusers_users_'. $data['default_organization_portal']->id .'_'. $my_user_id);
			}

			if(!isset($filter_user)){
				$crf = new CollegeRecommendationFilters;
				$filter_qry = $crf->generateFilterQry($data);
				$raw_filter_qry = NULL;

				if (isset($filter_qry)) {
					DB::connection('rds1')->statement('SET SESSION group_concat_max_len = 1000000;');
					$filter_qry = $filter_qry->selectRaw('GROUP_CONCAT(userFilter.id SEPARATOR ",") as filterUserId');
					$filter_user  = $filter_qry->first();
					$filter_user = explode(",", $filter_user->filterUserId);

					Cache::put(env('ENVIRONMENT') .'_filteredusers_users_'. $data['default_organization_portal']->id .'_'. $my_user_id, $filter_user, 60);
				}
			}
		}
		// end of department set


		$topicsUsr = array();

		$receiver_id_check =false;

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

		$uil = new UsersIpLocation;
		if(isset($usersTopicsAndMessages)){

			foreach ($usersTopicsAndMessages as $k) {

				// if filter user array is set, and user id is in the filter user array
				// then we can show the message, else we skip the user.
				if (isset($filter_user) && !in_array($k->user_id, $filter_user)) {
					continue;
				}

				if (Cache::has(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data')){

					Cache::forget(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data');
				}

				$foreachCount++;

				if ($offsetCount !=0 && $foreachCount <= $offsetCount) {
					continue;
				}

				// if ($takeCount   !=0 && $takeCount == $loopCount) {
				// 	break;
				// }

				$loopCount++;

				$user_member_of_thread = false;
				
				if (isset($k->created_at)) {
					$k->created_at = $this->convertTimeZone($k->created_at, 'America/Los_Angeles', 'UTC');
				}
				
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

				$topicsUsr['thread_total_count'] = $usersTopicsAndMessages_cnt;

				if (Cache::has(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore')) {
					$topicsUsr['session_num'] =  Cache::get(env('ENVIRONMENT') . '_' . $data['user_id'].'_loadMore');
				}else{
					$topicsUsr['session_num'] =  -1;
				}

				$topicsUsr['formatted_date'] = $tmp;

				$topicsUsr['date'] = date_format(date_create($k->created_at), 'Y-m-d H:i:s');

				$topicsUsr['post_id'] 		   = $this->hashIdForSocial($k->post_id);
				$topicsUsr['share_article_id'] = $this->hashIdForSocial($k->share_article_id);

				$topicsUsr['thread_name'] = $k->thread_name;

				$topicsUsr['is_read']   = $k->is_read;
				$topicsUsr['thread_id'] = $this->hashIdForSocial($k->thread_id);

				// Add the list of thread members to the mix

				$topicsUsr['thread_members'] = array();
				$threadMembers = DB::connection('rds1')->table('college_message_thread_members as cmtm')
														->join('users as u', 'u.id', '=', 'cmtm.user_id')

														->where('cmtm.thread_id', $k->thread_id)
														->select('cmtm.user_id', 'cmtm.num_unread_msg', 'cmtm.org_branch_id', 'cmtm.agency_id', 'cmtm.updated_at')
														->distinct()
														->orderBy('cmtm.created_at', 'DESC')
														->get();

				// $threadMembers  = CollegeMessageThreadMembers::on('rds1')->where('thread_id', '=', $k->thread_id)
				// 	//->where('user_id', '!=', $my_user_id)
				// 	->select('cmtm.user_id', 'cmtm.num_unread_msg', 'cmtm.org_branch_id', 'cmtm.agency_id', 'cmtm.updated_at')
				// 	->distinct()
				// 	->orderBy('cmtm.created_at', 'DESC')
				// 	->get();

				$first_name_last_msg = NULL;

				$num_of_thread_members = count($threadMembers);

				// use to see if we have set who other party of the thread is if org, show org,
				// if one to one show the other part i.e grandma.
				$thread_name_bool = false;

				//***************************IMPORTANT********************************//
				// if the number of thread members is one, it MUST be a user to college/prep
				// so we are going to set the thread type and id according to them

				$threadMembersCount = count($threadMembers);

				if($threadMembersCount == 1){
					foreach ($threadMembers as $tm) {

						if (isset($tm->org_branch_id)) {
							$branch = OrganizationBranch::on('rds1')->where('id' , '=', $tm->org_branch_id)->first();

							$branch_college = College::on('rds1')->find($branch->school_id);

							if($receiver_id == $branch->school_id){
								$user_member_of_thread = true;
							}

							$topicsUsr['Name'] = trim($branch_college->school_name);
							$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/". $branch_college->logo_url;



							$topicsUsr['thread_type'] = 'college';
							//adding college id for thread type id
							$topicsUsr['thread_type_id'] = $branch_college->id;
							$topicsUsr['country_code']   = 'US';
							$first_name_last_msg = $topicsUsr['Name'];
						}elseif(isset($tm->agency_id)){
							$branch = Agency::on('rds1')->find($tm->agency_id);

							if($receiver_id == $branch->id){
								$user_member_of_thread = true;
							}

							$topicsUsr['Name'] = trim($branch->name);
							$topicsUsr['img'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/'. $branch->logo_url;



							$topicsUsr['thread_type'] = 'agency';
							//adding college id for thread type id
							$topicsUsr['thread_type_id'] = $branch->id;
							$topicsUsr['country_code']   = 'US';
							$first_name_last_msg = $topicsUsr['Name'];
						}elseif(isset($tm->user_id)){
							$user = User::on('rds1')->find($tm->user_id);

							$topicsUsr['Name'] = trim($user->fname);
							$topicsUsr['img'] = $user->profile_img_loc;

							$topicsUsr['thread_type'] = 'users';
							$topicsUsr['thread_type_id'] = null;

							$topicsUsr['country_code'] = $uil->getUsersCountryCode($tm->user_id);
							$first_name_last_msg = $topicsUsr['Name'];
						}

					}

				}

				//***************************IMPORTANT Finish********************************//
				// print_r("<br />*************");
				// echo($user_member_of_thread);
				// print_r("<br />*************");

				$topicsUsr['msg_read_time'] = -1;

				foreach ($threadMembers as $tm) {

					//check to see if the member is myself or not, if it is I just need to save
					//the num_unread_msg s, else add it to thread_members array
					if($tm->user_id == $my_user_id){
						$topicsUsr['num_unread_msg'] = $tm->num_unread_msg;
					}else{

						// Only run once to determine the nature of thread(i.e user to user, user to college)
						//if($thread_name_bool == false){

							if (isset($tm->agency_id)) {
								if($tm->agency_id != -1){

									$branch = Agency::on('rds1')->find($tm->agency_id);
									if (isset($branch)) {
										if($receiver_id == $branch->id){
											$user_member_of_thread = true;
										}

										$topicsUsr['Name'] = $branch->name;
										$topicsUsr['img'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/'. $branch->logo_url;



										$topicsUsr['thread_type'] = 'agency';
										//adding college id for thread type id
										$topicsUsr['thread_type_id'] = $branch->id;
										$topicsUsr['country_code'] = 'US';
										$first_name_last_msg = $topicsUsr['Name'];
									}
								}else{

									if($receiver_id == $tm->user_id){
										$user_member_of_thread = true;
									}
									$uid = $tm->user_id;

									$user = User::on('rds1')->find($uid);


									$topicsUsr['Name'] = trim($user->fname. " ". $user->lname);
									$first_name_last_msg = trim($user->fname);

									if(!isset($k->profile_img_loc) || $k->profile_img_loc == ""){
										$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

									}else{
										$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $k->profile_img_loc;

									}
									$topicsUsr['thread_type'] = 'user';
									//adding user id for thread type id
									$topicsUsr['thread_type_id'] = $this->hashIdForSocial($uid);

									$topicsUsr['country_code'] = $uil->getUsersCountryCode($uid);

									$arr = array();

									$user_member = User::on('rds1')->find($tm->user_id);

									$arr['Name'] = trim($user_member->fname. " ". $user_member->lname);

									if($user_member->profile_img_loc == ""){
										$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

									}else{
										$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user_member->profile_img_loc;

									}

									$arr['user_id'] = $this->hashIdForSocial($user_member->id);
									$arr['country_code'] = $uil->getUsersCountryCode($user_member->user_id);

									$topicsUsr['thread_members'][] = $arr;
								}
							}elseif(isset($tm->org_branch_id)){
								if($tm->org_branch_id != -1){

									$branch = OrganizationBranch::on('rds1')->where('id' , '=', $tm->org_branch_id)->first();

									if(isset($branch)){
										$branch_college = College::on('rds1')->find($branch->school_id);
										//dd($branch_college);
	
										if($receiver_id == $branch->school_id){
											$user_member_of_thread = true;
										}
	
										$topicsUsr['Name'] = trim($branch_college->school_name);
										$first_name_last_msg = $topicsUsr['Name'];
										$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/". $branch_college->logo_url;
	
	
	
										$topicsUsr['thread_type'] = 'college';
										// See if the user has read the message or not
										if ($tm->num_unread_msg == 0) {
	
											$topicsUsr['msg_read_time'] = "Read";
										}
										// End of message read or not
										//adding college id for thread type id
										$topicsUsr['thread_type_id'] = $branch_college->id;
										$topicsUsr['country_code'] = 'US';
									}
								}else{

									if($receiver_id == $tm->user_id){
										$user_member_of_thread = true;
									}
									$uid = $tm->user_id;

									$user = User::on('rds1')->find($uid);


									$topicsUsr['Name'] = trim($user->fname. " ". $user->lname);
									$first_name_last_msg = trim($user->fname);
									if(!isset($k->profile_img_loc) || $k->profile_img_loc == ""){
										$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

									}else{
										$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $k->profile_img_loc;

									}
									$topicsUsr['thread_type'] = 'user';
									//adding user id for thread type id
									$topicsUsr['thread_type_id'] = $this->hashIdForSocial($uid);
									$topicsUsr['country_code'] = $uil->getUsersCountryCode($uid);

									$arr = array();

									$user_member = User::on('rds1')->find($tm->user_id);

									$arr['Name'] = trim($user_member->fname. " ". $user_member->lname);

									if($user_member->profile_img_loc == ""){
										$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

									}else{
										$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user_member->profile_img_loc;

									}

									$arr['user_id'] = $this->hashIdForSocial($user_member->id);
									$arr['country_code'] = $uil->getUsersCountryCode($user_member->id);
									$topicsUsr['thread_members'][] = $arr;
								}
							}else{
								if($receiver_id == $tm->user_id){
									$user_member_of_thread = true;
								}
								$uid = $tm->user_id;

								$user = User::on('rds1')->find($uid);


								$topicsUsr['Name'] = trim($user->fname. " ". $user->lname);
								$first_name_last_msg = trim($user->fname);
								// if(!isset($k->profile_img_loc) || $k->profile_img_loc == ""){
								// 	$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

								// }else{
								// 	$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $k->profile_img_loc;

								// }
								$topicsUsr['thread_type'] = 'user';
								//adding user id for thread type id
								$topicsUsr['thread_type_id'] = $this->hashIdForSocial($uid);
								$topicsUsr['country_code'] = $uil->getUsersCountryCode($uid);

								$arr = array();

								$user_member = User::on('rds1')->find($tm->user_id);

								$arr['Name'] = trim($user_member->fname. " ". $user_member->lname);

								if($user_member->profile_img_loc == ""){
									$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

								}else{
									$arr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user_member->profile_img_loc;

								}
								$topicsUsr['img'] = $arr['img'];

								$arr['user_id'] = $this->hashIdForSocial($user_member->id);
								$arr['country_code'] = $uil->getUsersCountryCode($user_member->id);

								$topicsUsr['thread_members'][] = $arr;
							}



							$thread_name_bool = true;
						//}

						/*
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
						*/
					}
				}



				($my_user_id == $k->user_id) ? $name_last_msg = 'You: '  : $name_last_msg = $first_name_last_msg.": ";
				// Detect if the msg is in English
				if(strlen($k->msg) != mb_strlen($k->msg, 'utf-8')){ //NOT ENGLISH			        
					$topicsUsr['msg'] = $name_last_msg.$k->msg;
			    }else {  										   // IS ENGLISH
			        if(strlen($k->msg) > 26){
						$topicsUsr['msg'] = $name_last_msg.substr(strip_tags($k->msg), 0, 26) . "...";
					}else{
						$topicsUsr['msg'] = $name_last_msg.strip_tags($k->msg);
					}
			    }


				// The Composed user was in the contact list,
				// so we are going to move them to top of array
				//$data['topicUsr'][] = $topicsUsr;


				if($user_member_of_thread == true){

					// if (isset($data['topicUsr'] )) {
					// 	array_unshift($data['topicUsr'] , $topicsUsr);
					// }else{
						$data['topicUsr'][] = $topicsUsr;
					//}

					$index_to_go_on_top = count($data['topicUsr']) - 1;
					$receiver_id_check = true;
				}else{
					$data['topicUsr'][] = $topicsUsr;
				}


				Cache::put(env('ENVIRONMENT') .'_'.'thread_'.$k->thread_id.'_data', $topicsUsr, 10);
			}

		}
	
		if (isset($index_to_go_on_top)) {

			$temp_arr =  $this->move_to_top($data['topicUsr'], $index_to_go_on_top);

			if (isset($temp_arr) && !empty($temp_arr)) {
				$data['topicUsr'] = $temp_arr;
			}
		}

		// The composed user was not on the contact list, so we manually gonna add them to top of array

		if(!$receiver_id_check && $receiver_id!= NULL && $receiver_id != $data['user_id']){
			$d = new DateTime('today');

			if($type == 'college'){

				$college = College::find($receiver_id);

				$topicsUsr['Name'] = trim($college->school_name);
				$topicsUsr['thread_name'] = '';

				if($college->logo_url == ""){
					$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

				}else{
					$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/". $college->logo_url;

				}

				$topicsUsr['date'] = date_format($d, 'Y-m-d');
				$topicsUsr['msg'] = "";
				$topicsUsr['thread_type_id'] = $receiver_id;
				$topicsUsr['thread_type'] = 'college';
				$topicsUsr['is_read'] = 0;
				$topicsUsr['thread_id'] = -1;
				$topicsUsr['thread_members'] = array();
				$topicsUsr['num_unread_msg'] = 0;

			}elseif ($type == 'agency') {

				$agency = Agency::find($receiver_id);

				$topicsUsr['Name'] = trim($agency->name);
				$topicsUsr['thread_name'] = '';

				if($agency->logo_url == ""){
					$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

				}else{
					$topicsUsr['img'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/'. $agency->logo_url;

				}

				$topicsUsr['date'] = date_format($d, 'Y-m-d');
				$topicsUsr['msg'] = "";
				$topicsUsr['thread_type_id'] = $receiver_id;
				$topicsUsr['thread_type'] = 'agency';
				$topicsUsr['is_read'] = 0;
				$topicsUsr['thread_id'] = -1;
				$topicsUsr['thread_members'] = array();
				$topicsUsr['num_unread_msg'] = 0;
			}else{


				$user = User::find($receiver_id);
				// Deleted user here
				if (!isset($user)) {
					$topicsUsr['Name'] = "Deleted User";
					$topicsUsr['thread_name'] = "Deleted User";
					$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";
				}else{
					$topicsUsr['Name'] = trim($user->fname. " ". $user->lname);
					$topicsUsr['thread_name'] = trim($user->fname. " ". $user->lname);

					if($user->profile_img_loc == ""){
						$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

					}else{
						$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

					}
				}
				

				$topicsUsr['date'] = date_format($d, 'Y-m-d');
				$topicsUsr['msg'] = "";
				$topicsUsr['thread_type_id'] = $this->hashIdForSocial($receiver_id);
				$topicsUsr['thread_type'] = 'users';
				$topicsUsr['is_read'] = 0;
				$topicsUsr['thread_id'] = -1;
				$topicsUsr['thread_members'] = array();
				$topicsUsr['num_unread_msg'] = 0;

			}

			if (isset($data['topicUsr'] )) {
				array_unshift($data['topicUsr'] , $topicsUsr);
			}else{
				$data['topicUsr'][] = $topicsUsr;
			}

			//$data['topicUsr'][] = $topicsUsr;
		}

		if (!isset($data['topicUsr']) ) {

			$data['topicUsr'] = array();
		}
		return $data['topicUsr'];

	}
}
