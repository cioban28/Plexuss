<?php

namespace App\Http\Controllers;

use Request, Validator, Session, DB;
use App\OrganizationBranch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

use App\College, App\CollegeMessageThreadMembers, App\User;

class ChatMessageController extends Controller
{
    protected $adminThreadIds ='';
	protected $secs_to_consider_live = 30;
	
	/*
	* Posting a new message to another user	
	*
	*/
	// public function postMessage($thread_id = NULL, $receiver_user_id = NULL, $type =Null){

	// 	$inputs = Request::all();

	// 	$valInputs = array(
	// 		'thread_id' => $thread_id,
	// 		'receiver_user_id' => $receiver_user_id,
	// 		'type' => $type, 
	// 		'message' => $inputs['message']);

	// 	$valFilters =array (
	// 		'thread_id' => 'required',
	// 		'receiver_user_id' => 'required|numeric',
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

	// 	$viewDataController = new ViewDataController();
	// 	$data = $viewDataController->buildData();
	// 	$data['currentPage'] = "chat-messages";
		
		
		
	// 	$my_user_id = Session::get('userinfo.id');

	// 	// Get topic id for this conversation
	// 	$college_message_thread_id = $thread_id;
	// 	$org_branch_user_ids = array();

	// 	if($type == "users"){

	// 		//if Topic thread doesn't exitst, create a new topic.
	// 		if( $college_message_thread_id == -1){

	// 			// Add a new thread.
	// 			$cmt = new CollegeMessageThreads;
	// 			$cmt->save();
	// 			$college_message_thread_id = $cmt->id; 

	// 			//Get org_branch_id for the receiver user id.
				
	// 			$obp = OrganizationBranchPermission::where('user_id', $receiver_user_id)->first();

	// 			if(!isset($obp)){

	// 				return "This is not an admin!";
	// 			}


	// 			// Attach the user, and organization to the thread members
	// 			$cmtm = new CollegeMessageThreadMembers;
		
	// 			$cmtm->user_id = $receiver_user_id ;
	// 			$cmtm->org_branch_id = $obp->organization_branch_id;
	// 			$cmtm->thread_id = $college_message_thread_id;
	// 			$cmtm->save();


	// 			$cmtm = new CollegeMessageThreadMembers;
	// 			$cmtm->user_id = $my_user_id;
	// 			$cmtm->org_branch_id = $obp->organization_branch_id;
	// 			$cmtm->thread_id = $college_message_thread_id;
	// 			$cmtm->save();

	// 		}else{

	// 			// if the user is not part of thread members, add him to the mix.
	// 			$cmt = CollegeMessageThreadMembers::where('user_id', '=', $my_user_id)
	// 				->where('thread_id', '=', $college_message_thread_id)
	// 				->first();

	// 			if(!$cmt){
	// 				$cmt = CollegeMessageThreadMembers::where('thread_id', '=', $college_message_thread_id)
	// 				->first();

	// 				if(!$cmt){

	// 					return "Bad Thread Id";
	// 				}


	// 				$cmtm = new CollegeMessageThreadMembers;
	// 				$cmtm->user_id = $my_user_id;
	// 				$cmtm->org_branch_id = $cmt->org_branch_id;
	// 				$cmtm->thread_id = $college_message_thread_id;
	// 				$cmtm->save();
					
	// 			}

	// 		}

	// 	}elseif ($type == "college") {

	// 		//if Topic thread doesn't exitst, create a new topic.

	// 		if(!isset($data['org_branch_id'])){
	// 			return "You are not an admin!";
	// 		}
	// 		if( $college_message_thread_id == -1){

	// 			// Add a new thread.
	// 			$cmt = new CollegeMessageThreads;
	// 			$cmt->save();
	// 			$college_message_thread_id = $cmt->id; 

	// 			// Attach the user, and organization to the thread members
	// 			$cmtm = new CollegeMessageThreadMembers;
		
	// 			$cmtm->user_id = $receiver_user_id ;
	// 			$cmtm->org_branch_id = $data['org_branch_id'];
	// 			$cmtm->thread_id = $college_message_thread_id;
	// 			$cmtm->save();


	// 			$cmtm = new CollegeMessageThreadMembers;
	// 			$cmtm->user_id = $my_user_id;
	// 			$cmtm->org_branch_id = $data['org_branch_id'];
	// 			$cmtm->thread_id = $college_message_thread_id;
	// 			$cmtm->save();

	// 		}else{

	// 			// if the user is not part of thread members, add him to the mix.
	// 			$cmt = CollegeMessageThreadMembers::where('user_id', '=', $my_user_id)
	// 				->where('thread_id', '=', $college_message_thread_id)
	// 				->first();

	// 			if(!$cmt){
	// 				$cmt = CollegeMessageThreadMembers::where('thread_id', '=', $college_message_thread_id)
	// 				->first();

	// 				if(!$cmt){

	// 					return "Bad Thread Id";
	// 				}

	// 				$cmtm = new CollegeMessageThreadMembers;
	// 				$cmtm->user_id = $my_user_id;
	// 				$cmtm->org_branch_id = $cmt->org_branch_id;
	// 				$cmtm->thread_id = $college_message_thread_id;
	// 				$cmtm->save();
					
	// 			}

	// 		}
		
	// 	}else{
	// 		return "Type is invalid!";
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
	// 	$thread_members = CollegeMessageThreadMembers::where('user_id', '!=', $my_user_id)
	// 		->where('thread_id', '=', $college_message_thread_id)
	// 		->pluck('user_id');

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
	// 			->update(array(
	// 				'num_unread_msg' => DB::raw('num_unread_msg + 1'),
	// 				'email_sent' => 0
	// 			));
	// 	}

	// 	// update the cache for this user.
	// 	//$this->getAllThreads($data, "");

	// 	$data['thread_id'] = $college_message_thread_id;
	// 	$data['stickyUsr'] = "";

	// 	return $college_message_thread_id;	
	// }

	/*
	* Populate chat initial page.
	*
	*/
	public function getChatPage(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if(!isset($data['is_organization'])){
			return redirect('/');
		}

        $data['is_admin_premium'] = $this->validateAdminPremium();

        if( !$data['is_admin_premium'] ){
            return redirect( '/admin/premium-plan-request' );
        }

		$data['currentPage'] = "admin-chat";
		//$data['currentPage'] = "admin-messages";
		
		$cmc = new CollegeMessageController();

		$data['topicUsr'] = $cmc->getAllThreads($data, null);
		//$data['topicUsr'] = $tmp['topicUsr'];

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}

		$data['stickyUsr'] = "";
		$data['hashed_uid'] = $data['remember_token'];

		$college = College::find($data['org_school_id']);
		$data['school_name'] = $college->school_name;
		$data['school_logo']= 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;

		$data = $this->getMessageTemplates($data);
		
		return View( 'admin.messaging', $data);	

	}
	/*
	* Returns the contact list of a user. with latest message, name, picture, 
	* and the date latest message was sent to.
	*
	*/
	public function getUsrTopics($school_id = NULL, $type =NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = "admin-chat";
		 
		if($school_id != NULL){
			$data['stickyUsr'] = $school_id;
		}else{
			$data['stickyUsr'] = "";
		}
		
		$data['topicUsr'] = $this->getAllThreads($data, $school_id, $type);

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];			
		}
		
		return $data['topicUsr'];

	}
	/*
	* Returns the list of threads on heart beat.
	* 
	*
	*/
	public function getThreadListHeartBeat($receiver_id = NULL, $type = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$sender_user_id = Session::get('userinfo.id');

		$tmp = $this->getAllThreads($data, $receiver_id, $type);


		$data['currentPage'] = "admin-chat";
		$data['topicUsr'] = $tmp['topicUsr'];

		//dd($tmp);
		if(isset($tmp['main_chat_unread_msg'])){
			if($tmp['main_chat_unread_msg'] == 0){
				$tmp['main_chat_unread_msg'] = '';
			}
		}else{
			$tmp['main_chat_unread_msg'] = '';
		}

		if(isset($tmp['private_msg_unread_msg'])){
			if($tmp['private_msg_unread_msg'] == 0){
				$tmp['private_msg_unread_msg'] = '';
			}
		}else{
			$tmp['private_msg_unread_msg'] = '';
		}
		$data['main_chat_unread_msg'] =$tmp['main_chat_unread_msg'];
		$data['private_msg_unread_msg'] =$tmp['private_msg_unread_msg'];


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

		return json_encode($data);
	}

	/*
	* This method returns all of the threads related to the user.
	*
	*/
	private function getAllThreads($data ,$school_id = null, $type = NULL){

		// user means what users will see on the college page,
		// college means what admin will see on their portal.
		$retArr = array();
		if($type == "users"){
			
			$org_branch_id = OrganizationBranch::on('bk')->where('school_id', $school_id)->first();

			
			if(!isset($org_branch_id)){
				$retArr['topicUsr'] = array();
				$retArr['isLive'] = 0;
				$retArr['in_our_network'] = 0;
				return $retArr;
			}

			$org_branch_id = $org_branch_id->id;
		}
		else if($type =='college'){
			$org_branch_id = $data['org_branch_id'];
		}else{
			return "This type is invalid.";
		}

		$my_user_id = Session::get('userinfo.id');



		$adminTopics  = DB::connection('bk')->table('college_message_thread_members as cmtm')
											->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
											->where('cmt.is_chat', 1)
											->where('cmtm.org_branch_id', $org_branch_id)
											->select('cmt.id as thread_id')
											->distinct()
											->get();


		$adminThreadIds = '';
		
		//dd($adminTopics);
		if (Cache::has(env('ENVIRONMENT') .'_'.$my_user_id.'_msg_thread_ids')){	
			Cache::forget(env('ENVIRONMENT') .'_'.$my_user_id.'_msg_thread_ids');
		}
		$user_threads_arr = array();

		foreach ($adminTopics as $key => $value) {
			$adminThreadIds .= $value->thread_id." ,";
			$user_threads_arr[] = $value->thread_id;
		}

		$this->adminThreadIds = substr($adminThreadIds, 0, -2);

		//dd($this->adminThreadIds);
		$thread_id = $this->adminThreadIds;

		// Set the gloabal chat thread id
		$this->set_chat_thread_id($thread_id);

		$retArr['in_our_network'] = 1;
		$retArr['topicUsr'] = array();

		if($type == "users"){

			$college_chat_threads = Cache::get(env('ENVIRONMENT') .'_'.'college_chat_threads');
			// echo "<pre>";
			// print_r($college_chat_threads);
			// echo "</pre>";
			// exit();
			
			if(!isset($college_chat_threads[$thread_id])){
				// Chat is not made yet
				$retArr['isLive'] = 0;
				return $retArr;
			}

			$tmp_thread = $college_chat_threads[$thread_id];

			$check = false;
			//dd($tmp_thread);
			
			$topicsUsr = array();
			$topicsUsr['Name'] = 'Main Chat';
			$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";
			$topicsUsr['date'] = '';
			$topicsUsr['thread_name'] = "";
			//$topicsUsr['whosent_id'] = $k->user_id;
			$topicsUsr['thread_id'] = $thread_id;
			$topicsUsr['thread_type_id'] = -1;
			$topicsUsr['hashed_thread_type_id'] = Crypt::encrypt(-1);
			$topicsUsr['thread_type'] = 'users';
			$topicsUsr['thread_members'] = array();
			$topicsUsr['num_unread_msg'] = $this->get_num_of_unread_msgs($thread_id, $my_user_id);
			$topicsUsr['is_main_chat'] = 1;

			$retArr['main_chat_unread_msg'] = $topicsUsr['num_unread_msg'];

			$private_msg_unread_msg = CollegeMessageThreadMembers::where('user_id', $my_user_id)
																	->where('org_branch_id', $org_branch_id)
																	->where('thread_id', '!=', $thread_id)
																	->sum('num_unread_msg');
			$retArr['private_msg_unread_msg'] = $private_msg_unread_msg;

			$retArr['topicUsr'][] =  $topicsUsr;

			$chat_thread_id = $thread_id;

			foreach ($tmp_thread as $key) {
				if(isset($key['updated_at']) && isset($key['org_branch_id'])){

					$topicsUsr = array();

					$timeCalc = strtotime(date("Y-m-d H:i:s")) - strtotime($key['updated_at']);

					if($key['org_branch_id'] == $college_chat_threads[$thread_id]['org_branch_id']  && $timeCalc < $this->secs_to_consider_live){
						//echo "here";
						$check = true;
						$user = User::find($key['user_id']);

						$topicsUsr['Name'] = $user->fname . ' ' . $user->lname;
					
						if($user->profile_img_loc == ""){
							$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

						}else{
							$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

						}
						$topicsUsr['date'] = $this->xTimeAgo($key['updated_at'], date("Y-m-d H:i:s")); 

						$topicsUsr['thread_name'] = "";


						//$topicsUsr['whosent_id'] = $k->user_id;

						$topicsUsr['thread_id'] = $key['thread_id'];
						$topicsUsr['thread_type_id'] = $key['user_id'];
						$topicsUsr['thread_type'] = 'users';

						$user_threads_with_this_org = CollegeMessageThreadMembers::where('org_branch_id', $key['org_branch_id'])
														->where('user_id', $my_user_id)->get();

						//dd(count($user_threads_with_this_org));
						$topicsUsr['num_unread_msg'] = '';

						$topicsUsr['sticky_thread_type'] = 'chat-msg';

						if(count($user_threads_with_this_org) > 0){

							$admin_threads = CollegeMessageThreadMembers::where('org_branch_id', $key['org_branch_id'])
																		->where('user_id', $key['user_id'])
																		->where('thread_id', '!=', $chat_thread_id)
																		->select('thread_id')
																		->distinct()
																		->get();
							//dd(count($admin_threads));
							$admin_threads_arr = array();
							foreach ($admin_threads as $k) {
								$admin_threads_arr[] = $k->thread_id;

							}
							//dd($admin_threads_arr);

							foreach ($user_threads_with_this_org as $k) {

								if(in_array($k->thread_id, $admin_threads_arr)){

									$topicsUsr['thread_id'] = $k->thread_id;
									$topicsUsr['num_unread_msg'] = $this->get_num_of_unread_msgs($topicsUsr['thread_id'], $my_user_id);
									break;
								}
							}
						}

						// Add the list of thread members to the mix

						$topicsUsr['is_main_chat'] = 0;

						$topicsUsr['thread_members'] = array();

					}
					if(!empty($topicsUsr)){

						$retArr['topicUsr'][] =  $topicsUsr;
					}
				
				}
			}
			//dd($retArr);
			if(!$check){
				// No Rep is available
				$retArr['isLive'] = 0;
				return $retArr;
			}else{
				$retArr['isLive'] = 1;
			}

			// Add myself to chat cache
			$this->add_user_chat_cache($thread_id);
			$retArr['num_of_chat_online'] = $this->num_of_chat_online($thread_id);
			$retArr['chat_thread_id'] = $thread_id;

		}elseif ($type == "college") {

			if(!isset($data['org_branch_id'])){
				//you are not an admin

				return $retArr;
			}

			$org_branch_id = $data['org_branch_id'];

			$college_chat_threads = Cache::get(env('ENVIRONMENT') .'_'.'college_chat_threads');


			if(!isset($college_chat_threads[$thread_id])){

				//chat is not made yet
				return $retArr;
			}

			$tmp_thread = $college_chat_threads[$thread_id];

			$check = false;
			//dd($tmp_thread);


			
			$topicsUsr = array();
			$topicsUsr['Name'] = 'Main Chat';
			$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";
			$topicsUsr['date'] = '';
			$topicsUsr['thread_name'] = "";
			//$topicsUsr['whosent_id'] = $k->user_id;
			$topicsUsr['thread_id'] = $thread_id;
			$topicsUsr['thread_type_id'] = -1;
			$topicsUsr['thread_type'] = 'college';
			$topicsUsr['thread_members'] = array();

			$topicsUsr['num_unread_msg'] = $this->get_num_of_unread_msgs($thread_id, $my_user_id);
			$topicsUsr['is_main_chat'] = 1;

			$retArr['main_chat_unread_msg'] = $topicsUsr['num_unread_msg'];

			$private_msg_unread_msg = CollegeMessageThreadMembers::where('user_id', $my_user_id)
																	->where('org_branch_id', $org_branch_id)
																	->where('thread_id', '!=', $thread_id)
																	->sum('num_unread_msg');
			$retArr['private_msg_unread_msg'] = $private_msg_unread_msg;
			$retArr['topicUsr'][] =  $topicsUsr;

			// Get the thread ids that I'm involved with
			$my_thread_ids = DB::table('college_message_threads as cmt')
							->join(DB::raw('(SELECT thread_id FROM college_message_thread_members WHERE user_id='.$my_user_id.' GROUP BY thread_id) as t1'), 't1.thread_id', '=', 'cmt.id')
							->join('college_message_thread_members as cmtm', 't1.thread_id', '=', 'cmtm.thread_id')
							->where('cmt.is_chat', 0)
							->where('cmtm.org_branch_id', $org_branch_id)
							->whereNull('cmt.campaign_id')
							->where('cmt.has_text', 0)

							->select('cmt.id as thread_id', 'cmtm.user_id as user_id')
							->groupby('thread_id', 'cmtm.user_id')
							//->distinct()
							->get();


			$my_threads_arr = array();

			foreach ($my_thread_ids as $key) {

				if(isset($my_threads_arr[$key->thread_id])){
					$my_threads_arr[$key->thread_id][] = $key->user_id;
				}else{
					$my_threads_arr[$key->thread_id] = array();
					$my_threads_arr[$key->thread_id][] = $key->user_id;

				}
			}

			//dd($tmp_thread);
			
			//dd($retArr);

			// print("<pre>");
			// var_dump($tmp_thread);
			// print("</pre>");
			// print("=============================================================");

			foreach ($tmp_thread as $key) {
				if(isset($key['updated_at'])){
					$topicsUsr = array();

					$timeCalc = strtotime(date("Y-m-d H:i:s")) - strtotime($key['updated_at']);

					if(!isset($key['org_branch_id'])){
						$org_branch_id = -1;
					}else{
					 	$org_branch_id = $key['org_branch_id'] ;
					}

					if($org_branch_id != $college_chat_threads[$thread_id]['org_branch_id']  && $timeCalc < $this->secs_to_consider_live){

						//dd('here');
						//echo "here";
						$check = true;


						$user = User::find($key['user_id']);

						$topicsUsr['Name'] = $user->fname . ' ' . $user->lname;
					
						if($user->profile_img_loc == ""){
							$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";

						}else{
							$topicsUsr['img'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/". $user->profile_img_loc;

						}
						$topicsUsr['date'] = $this->xTimeAgo($key['updated_at'], date("Y-m-d H:i:s")); 

						$topicsUsr['thread_name'] = "";

						$topicsUsr['num_unread_msg'] = '';

						//$topicsUsr['whosent_id'] = $k->user_id;

						$topicsUsr['thread_id'] = $key['thread_id'];
						$topicsUsr['thread_type_id'] = $key['user_id'];
						$topicsUsr['hashed_thread_type_id'] = Crypt::encrypt($key['user_id']);
						$topicsUsr['thread_type'] = 'college';

						foreach ($my_threads_arr as $x => $value) {
							//dd($x);
							if(in_array($key['user_id'], $value)){
								$topicsUsr['thread_id'] = $x;
								$topicsUsr['num_unread_msg'] = $this->get_num_of_unread_msgs($topicsUsr['thread_id'], $my_user_id);
								break;
							}
						}

						// Add the list of thread members to the mix

						$topicsUsr['is_main_chat'] = 0;

						$topicsUsr['thread_members'] = array();

					}
					if(!empty($topicsUsr)){

						$retArr['topicUsr'][] =  $topicsUsr;
					}
				}
				
			}
			//dd($retArr);
			if(!$check){
				// No Rep is available
				$retArr['isLive'] = 0;
				return $retArr;
			}else{
				$retArr['isLive'] = 1;
			}

			// Add myself to chat cache
			$this->add_user_chat_cache($thread_id);
			$retArr['num_of_chat_online'] = $this->num_of_chat_online($thread_id);
			$retArr['chat_thread_id'] = $thread_id;

			// print("<pre>");
			// var_dump($retArr);
			// print("</pre>");
			// exit();

		}else{
			return "Bad type!";
		}
		
		return $retArr;
	}

	/*
	* number of people who are online for this chat thread.
	*
	*/
	
	private function num_of_chat_online($thread_id = NULL){

		$college_chat_threads = Cache::get(env('ENVIRONMENT') .'_'.'college_chat_threads');

		if (!isset($college_chat_threads[$thread_id])) {
			return "Chat doesn't exists, admin needs to be live"; 
		}

		$tmp_thread = $college_chat_threads[$thread_id];


		$now_time = date("Y-m-d H:i:s");
		$cnt = 0;

		foreach ($tmp_thread as $key) {
			if(isset($key['updated_at'])){
				$timeCalc = strtotime($now_time) - strtotime($key['updated_at']);
			
				if($timeCalc < $this->secs_to_consider_live){
					$cnt++;
				}
			}
			
		}

		return $cnt;
	}
	

	/*
	* Add users to chat cache
	*
	*/

	public function add_user_chat_cache($thread_id = NULL){



		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$my_user_id = Session::get('userinfo.id');

		if(isset($my_user_id)){
			
			$is_org = 0;
			$org_branch_id =0;

			if(isset($data['org_branch_id'])){
				$is_org = 1;
				$org_branch_id = $data['org_branch_id'];
			}

			$college_chat_threads = Cache::get(env('ENVIRONMENT') .'_'.'college_chat_threads');

			if (!isset($college_chat_threads[$thread_id])) {
				return "Chat doesn't exists, admin needs to be live"; 
			}

			$tmp_thread = $college_chat_threads[$thread_id];

			$check = false;
			//$cnt = 0;

			
			foreach ($tmp_thread as $key) {
				if(isset($key['user_id'])){

					$index = $this->array_search2d($key['user_id'], $tmp_thread);

					if($key['user_id'] == $my_user_id && $check == false){
						
						$college_chat_threads[$thread_id][$index]['user_id'] = $key['user_id'];
						$college_chat_threads[$thread_id][$index]['is_org'] = $key['is_org'];
						$college_chat_threads[$thread_id][$index]['org_branch_id'] = $key['org_branch_id'];
						$college_chat_threads[$thread_id][$index]['updated_at'] = date("Y-m-d H:i:s");
						$college_chat_threads[$thread_id][$index]['thread_id'] = $key['thread_id'];
						$college_chat_threads[$thread_id][$index]['name'] = $key['name'];

						$check = true;
					}
					//$cnt++;
				}
			}

			if(!$check){

				$tmp = array();
				$tmp['user_id'] = $my_user_id;
				$tmp['is_org'] = $is_org;
				$tmp['org_branch_id'] = $org_branch_id;
				$tmp['updated_at'] = date("Y-m-d H:i:s");
				$tmp['thread_id'] = -1;
				$tmp['name'] = Session::get('userinfo.fname'). ' '. Session::get('userinfo.lname');
				
				$college_chat_threads[$thread_id][] = $tmp;
			}
			//dd(Cache::get(env('ENVIRONMENT') .'_'.'college_chat_threads'));
			Cache::put(env('ENVIRONMENT') .'_'.'college_chat_threads', $college_chat_threads, 1440);
		}

	}

	/*
	* Check to see if the chat is live or not.
	*
	*/
	public  function is_chat_live($school_id){

		$org_branch_id = OrganizationBranch::where('school_id', $school_id)->first();

		$org_branch_id = $org_branch_id->id;

		$adminTopics  = DB::table('college_message_thread_members as cmtm')
							->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
							->where('cmt.is_chat', 1)
							->where('cmtm.org_branch_id', $org_branch_id)
							->select('cmt.id as thread_id')
							->distinct()
							->first();

		$thread_id = $adminTopics->thread_id;
		//$cnt = 0;
		
		if(Cache::has(env('ENVIRONMENT') .'_'.'college_chat_threads')){
			$college_chat_threads = Cache::get(env('ENVIRONMENT') .'_'.'college_chat_threads');



			if(isset($college_chat_threads[$thread_id])){
				$tmp_thread = $college_chat_threads[$thread_id];

				foreach ($tmp_thread as $key) {
					
					if(isset($key['updated_at'])){

						$timeCalc = strtotime(date("Y-m-d H:i:s")) - strtotime($key['updated_at']);
						
						
						if($key['is_org'] == 1 && $timeCalc < $this->secs_to_consider_live){
							//echo "xxxx";
							return 1; 
						}
						
					}
					
					//$cnt++;
				}

			}
			else{
				return 0;
			}
			
		}
		
		return 0;
	}

	/*
	* This initilizes the chat room for the college.
	*
	*/
	public function init_chat(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if(!isset($data['org_branch_id'])){
			return "you are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		if(!Cache::has(env('ENVIRONMENT') .'_'.'college_chat_threads')){
			$college_chat_threads = array();
		}else{
			$college_chat_threads = Cache::get(env('ENVIRONMENT') .'_'.'college_chat_threads');
		}

		

		$adminTopics  = DB::table('college_message_thread_members as cmtm')
							->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
							->where('cmt.is_chat', 1)
							->where('cmtm.org_branch_id', $data['org_branch_id'])
							->select('cmt.id as thread_id')
							->distinct()
							->first();

		$thread_id = $adminTopics->thread_id;

		if(isset($college_chat_threads[$thread_id])){

			$chat_thread_tmp = $college_chat_threads[$thread_id];

			$check = false;
			//$cnt = 0;
			$unset_users_arr = array();

			//echo "<pre>";
			
			foreach ($chat_thread_tmp as $key) {

				if(isset($key['user_id'])){
					$index = $this->array_search2d($key['user_id'], $chat_thread_tmp);

					if($key['user_id'] == $my_user_id && $check == false && $index != false){
						$college_chat_threads[$thread_id][$index]['user_id'] = $key['user_id'];
						$college_chat_threads[$thread_id][$index]['is_org'] = $key['is_org'];
						$college_chat_threads[$thread_id][$index]['org_branch_id'] = $key['org_branch_id'];
						$college_chat_threads[$thread_id][$index]['updated_at'] = date("Y-m-d H:i:s");
						$college_chat_threads[$thread_id][$index]['thread_id'] = $key['thread_id'];
						$college_chat_threads[$thread_id][$index]['name'] = $key['name'];
						$check = true;
					}else{

						// if the user has been sitting idle in the array for more than the idle time allowed.
						// remove the user from the cache.
						$timeCalc = strtotime(date("Y-m-d H:i:s")) - strtotime($key['updated_at']);
						
						if($timeCalc > $this->secs_to_consider_live){
							$unset_users_arr[] = $index;
						}
					}
					//$cnt++;
				}

				
			}


			// clean up cache by unsetting users who've been sitting idle
			foreach ($unset_users_arr as $key => $value) {
				unset($college_chat_threads[$thread_id][$value]);
			}


			//reorder indexes of the array
			$tmp_org_branch_id = $college_chat_threads[$thread_id]['org_branch_id'];
			unset($college_chat_threads[$thread_id]['org_branch_id']);
			$college_chat_threads[$thread_id] = array_values($college_chat_threads[$thread_id]);
			$college_chat_threads[$thread_id]['org_branch_id'] = $tmp_org_branch_id;


			if(!$check){

				$tmp = array();

				$tmp['user_id'] = $my_user_id;
				$tmp['is_org'] = 1;
				$tmp['org_branch_id'] = $data['org_branch_id'];
				$tmp['updated_at'] = date("Y-m-d H:i:s");
				$tmp['thread_id'] = -1;
				$tmp['name'] = Session::get('userinfo.fname'). ' '. Session::get('userinfo.lname');

				$college_chat_threads[$thread_id][] = $tmp;
			}
		}else{
			$college_chat_threads[$thread_id] = array();
			$college_chat_threads[$thread_id]['org_branch_id'] = $data['org_branch_id'];
			$tmp = array();

			$tmp['user_id'] = $my_user_id;
			$tmp['is_org'] = 1;
			$tmp['org_branch_id'] = $data['org_branch_id'];
			$tmp['updated_at'] = date("Y-m-d H:i:s");
			$tmp['thread_id'] = -1;
			$tmp['name'] = Session::get('userinfo.fname'). ' '. Session::get('userinfo.lname');

			$college_chat_threads[$thread_id][] = $tmp;

		}
		//dd($college_chat_threads);
		Cache::put(env('ENVIRONMENT') .'_'.'college_chat_threads', $college_chat_threads, 1440);
	}

	/*
	* Returns the topic number between two users.
	* 
	*
	*/







	/*****************************************
	*	get messages, templates, .. first load
	*	between two users (college and student)
	*
	*******************************************/
	public function contactMessageIndex(){

		// $my_user_id = Session::get('userinfo.id');
		$userId = Request::get('userId');
		$collegeUser = Request::get('collegeUser');

		$threadId = $this->getMessageThreadId($collegeUser, $userId);

		//if thread_id -- then get messages
		//else messages = null
		if(empty($threadId)){

			$data['messages'] = null;
			//$data['templates'] = null;
			$data['threadId'] = null;
		
		}else{

			$data['messages'] =  $this->getAllMessages($threadId);
			//$data['templates'] = $this->getMessageTemplates($data); 
			$data['threadId'] = $threadId;		
		}

		return View('admin.contactPane.ajax.contactMsg', $data);

	}
	


	/***************************************
	*	get thread ID.  
	*	within DB threads are converstations between any set of users
	*	this function gets a message thread between a college and student user
	*
	*****************************************/
	public function getMessageThreadId($college_user, $student_user){


		$communications = new CollegeMessageThreads();
		$results = $communications->getMessageThreadId($college_user, $student_user);
		return $results;

	}



	/**************************************
	*	get message templates
	*
	***************************************/
	public function getMsgTemplates(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$templates = $this->getMessageTemplates($data);

		return json_encode($templates['message_template']);
	}
	


	/******************************************
	* get last 20 of all messages, not last one sent,, ect...
	*
	*******************************************/
	public function getAllMessages($threadId){

		$messageModel = new CollegeMessageLog();
		$results = $messageModel->getAllMessages($threadId);

		return $results;

	}

	/*
	 * Chat init
	 *
	 *
	 *
	 */
	public function init(){
		$data = array();

		$data['org_branch_id'] = Session::get('userinfo.org_branch_id');
		$data['fname']		   = ucwords(strtolower(Session::get('userinfo.fname')));
		$data['lname']		   = ucwords(strtolower(Session::get('userinfo.lname')));

		if(!isset($data['org_branch_id'])){
			return "you are not an admin!";
		}

		$my_user_id = Session::get('userinfo.id');

		if (!Cache::has(env('ENVIRONMENT').'_'.'college_chat_threads')) {
			$college_chat_threads = array();
		}else {
			$college_chat_threads = Cache::get(env('ENVIRONMENT').'_'.'college_chat_threads');
		}

		$chat_thread = DB::connection('rds1')->table('college_message_thread_members AS cmtm')
											 ->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
											 ->where('cmt.is_chat', 1)
											 ->where('cmtm.org_branch_id', $data['org_branch_id'])
											 ->select('cmt.id AS thread_id')
											 ->distinct()
											 ->first();

		$thread_id = $chat_thread->thread_id;


		if ( isset( $college_chat_threads[$thread_id] ) ) {

			$chat_thread_tmp = $college_chat_threads[$thread_id];

			$check = false;
			//$cnt = 0;
			$unset_users_arr = array();

			//echo "<pre>";

			foreach ( $chat_thread_tmp as $key ) {

				if ( isset( $key['user_id'] ) ) {
					$index = $this->array_search2d( $key['user_id'], $chat_thread_tmp );

					if ( $key['user_id'] == $my_user_id && $check == false) {
						$college_chat_threads[$thread_id][$index]['user_id'] = $key['user_id'];
						$college_chat_threads[$thread_id][$index]['is_org'] = $key['is_org'];
						$college_chat_threads[$thread_id][$index]['org_branch_id'] = $key['org_branch_id'];
						$college_chat_threads[$thread_id][$index]['updated_at'] = date( "Y-m-d H:i:s" );
						$college_chat_threads[$thread_id][$index]['thread_id'] = $key['thread_id'];
						$college_chat_threads[$thread_id][$index]['name'] = $key['name'];
						$check = true;
						//break;
					}else {

						// if the user has been sitting idle in the array for more than the idle time allowed.
						// remove the user from the cache.
						$timeCalc = strtotime( date( "Y-m-d H:i:s" ) ) - strtotime( $key['updated_at'] );

						if ( $timeCalc > $this->secs_to_consider_live ) {
							$unset_users_arr[] = $index;
						}
					}
					//$cnt++;
				}


			}


			// clean up cache by unsetting users who've been sitting idle
			foreach ( $unset_users_arr as $key => $value ) {
				unset( $college_chat_threads[$thread_id][$value] );
			}


			//reorder indexes of the array
			$tmp_org_branch_id = $college_chat_threads[$thread_id]['org_branch_id'];
			unset( $college_chat_threads[$thread_id]['org_branch_id'] );
			$college_chat_threads[$thread_id] = array_values( $college_chat_threads[$thread_id] );
			$college_chat_threads[$thread_id]['org_branch_id'] = $tmp_org_branch_id;


			if ( !$check ) {

				$tmp = array();

				$tmp['user_id'] = $my_user_id;
				$tmp['is_org'] = 1;
				$tmp['org_branch_id'] = $data['org_branch_id'];
				$tmp['updated_at'] = date( "Y-m-d H:i:s" );
				$tmp['thread_id'] = -1;
				$tmp['name'] = $data['fname']. ' '. $data['lname'];

				$college_chat_threads[$thread_id][] = $tmp;
			}
		}else {
			$college_chat_threads[$thread_id] = array();
			$college_chat_threads[$thread_id]['org_branch_id'] = $data['org_branch_id'];
			$tmp = array();

			$tmp['user_id'] = $my_user_id;
			$tmp['is_org'] = 1;
			$tmp['org_branch_id'] = $data['org_branch_id'];
			$tmp['updated_at'] = date( "Y-m-d H:i:s" );
			$tmp['thread_id'] = -1;
			$tmp['name'] = $data['fname']. ' '. $data['lname'];

			$college_chat_threads[$thread_id][] = $tmp;
		}

		Cache::put(env('ENVIRONMENT').'_'.'college_chat_threads', $college_chat_threads, 1440 );
	}

	/*
	 * Chat isLive
	 *
	 *
	 *
	 */
	public function isLive(){
		$data = array();

		$data['org_branch_id'] = Session::get('userinfo.org_branch_id');

		$input = Request::all();

		$qry = DB::connection('rds1')->table('college_message_thread_members AS cmtm')
									 ->join('college_message_threads AS cmt', 'cmt.id', '=', 'cmtm.thread_id')
									 ->join('organization_branches as ob', 'ob.id', '=', 'cmtm.org_branch_id')
									 ->where('ob.school_id', $input['school_id'])
									 ->where('cmt.is_chat', 1)
									 ->select('cmt.id as thread_id')
									 ->distinct()
									 ->first();

		$thread_id = isset($qry->thread_id) ? $qry->thread_id : null;							 
		$cache = Cache::get(env('ENVIRONMENT').'_'.'college_chat_threads');

		if (isset($cache) && isset($thread_id)) {
			$college_chat_threads = $cache;

			if ( isset( $college_chat_threads[$thread_id] ) ) {
				$tmp_thread = $college_chat_threads[$thread_id];

				foreach ( $tmp_thread as $key ) {

					if ( isset( $key['updated_at'] ) ) {

						$timeCalc = strtotime( date( "Y-m-d H:i:s" ) ) - strtotime( $key['updated_at'] );
						//echo "time now ". strtotime( date( "Y-m-d H:i:s" ) ). "<br>";
						//echo "cache time ".strtotime( $key['updated_at']) ."<br>";
						//echo "timeCalc ". $timeCalc. "<br>";

						if ( $key['is_org'] == 1 && $timeCalc < $this->secs_to_consider_live ) {
							//echo "xxxx";
							echo 1;
							exit();
						}

					}

					//$cnt++;
				}

			}
		}

		echo 0;
		exit();
	}
}
