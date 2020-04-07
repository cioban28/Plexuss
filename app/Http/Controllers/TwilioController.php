<?php

namespace App\Http\Controllers;

use Request, Session, DB;
use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use Carbon\Carbon;
// use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;

require_once(base_path().'/vendor/twilio/sdk/Services/Twilio.php');
use Illuminate\Support\Facades\Cache;
use App\PhoneLog, App\SmsLog, App\AdminText, App\PurchasedPhone, App\User, App\VerifyPhoneCodeLog, App\Transcript, App\ZipCodes;
use App\TextTemplate, App\ReplyTextTemplate, App\CollegeMessageLog, App\CollegeMessageThreadMembers, App\Country;
use Illuminate\Support\Facades\Redis;

class TwilioController extends Controller
{
    private $client = '';
	private $from = '8777463228';
	const NUM_OF_FREE_TEXT = 500;

	/**
	* Setup twilio client connection
	*/
	public function __construct(){

		$sid = env('TWILIO_SID');
		$token = env('TWILIO_TOKEN');

		// $this->client = new \Services_Twilio($sid, $token);
		$this->client = new \Twilio\Rest\Client($sid, $token);

        // $this->lookup_client = new \Lookups_Services_Twilio($sid, $token);
	}

	/**
	 * sendSms
	 * Sending text message using Twilio API
	 * @param fromPhone : phone number we want to send from
	 * @param toPhone   : phone number we want to send to
	 * @param msg       : body of the text message
	 * @param smsBy     : user name who is sending the text message
	 * @param leadid    : lead id for tracking_test
	 * @return null
	 */

	public function sendSmsOld($fromPhone = null, $toPhone = null,  $msg = null, $smsBy = null, $leadid = null){

		$time = time();
		$time = date("h:i:s");
		$date = date("Y-m-d");

		if (!isset($fromPhone)) {
			$fromPhone = $this->from;
		}

		$smsBy = (isset($smsBy) ? $smsBy : 'Plexuss');
		$leadid = (isset($leadid) ? $leadid : -1);

		$_this_client = $this->client;

		$message = $_this_client->account->messages->sendMessage(
		  $fromPhone, // From a valid Twilio number
		  $toPhone, // Text this number
		  $msg
		);

		$smsStatus = (isset($message->Status) ? $message->Status : 'delivered');

		$tsl = new TwSmsLog;

	    $tsl->url = '';
	    $tsl->leadid = $leadid;
	    $tsl->time = $time;
	    $tsl->date = $date;
	    $tsl->fromPhone = $message->from;
	    $tsl->toPhone = $message->to;
	    $tsl->smsBy = $smsBy;
	    $tsl->smsMessageSid = $message->sid;
	    $tsl->smsSid = $message->sid;
	    $tsl->smsStatus = $smsStatus;
	    $tsl->body = $msg;
	    $tsl->price = $message->price;
		$tsl->price_unit = $message->price_unit;
		$tsl->error_code = $message->error_code;
		$tsl->error_message = $message->error_message;

	    $tsl->save();

		// $smsLog = new SmsLog;

		// $smsLog->user_id = '';
		// $smsLog->sid = $message->sid;
		// $smsLog->from = $message->from;
		// $smsLog->to = $message->to;
		// $smsLog->smsBy = $smsBy;
		// $smsLog->status = $message->status;
		// $smsLog->body = $message->body;
		// $smsLog->price = $message->price;
		// $smsLog->price_unit = $message->price_unit;
		// $smsLog->error_code = $message->error_code;
		// $smsLog->error_message = $message->error_message;

		// $smsLog->save();
	}

	/**
	 * smsReceiveOld
	 * Receive text message using Twilio API
	 * @return null
	 */
	public function smsReceiveOld(){

		$input = Request::all();

		$url = '';
		$leadid =(isset($input['leadId']) ? $input['leadId'] : -1);
		$time = time();
		$time = date("h:i:s");
		$date = date("Y-m-d");
		$fromPhone =  (isset($input['From']) ? $input['From'] : -1); 
		$toPhone = (isset($input['To']) ? $input['To'] : -1);
		$smsBy = "Lead";
		$smsMessageSid = (isset($input['SmsMessageSid']) ? $input['SmsMessageSid'] : -1);
		$smsSid = (isset($input['SmsSid']) ? $input['SmsSid'] : -1);
		$smsStatus = (isset($input['SmsStatus']) ? $input['SmsStatus'] : 'NaN');
		$body = (isset($input['Body']) ? $input['Body'] : -1);
		$fromPhoneTemp = str_replace("+1", "", $fromPhone);

		$tt = TrackingTest::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ')',''), '(', ''), ' ' ,'') LIKE '%".$fromPhoneTemp."%'")
							->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(secondaryPhone, '+', ''), '-', ''), ')',''), '(', ''), ' ' ,'') LIKE '%".$fromPhoneTemp."%'")
							->first();

	    $leadid = (isset($leadid) ? $tt->id : -1);

	    $tsl = new TwSmsLog;

	    $tsl->url = $url;
	    $tsl->leadid = $leadid;
	    $tsl->time = $time;
	    $tsl->date = $date;
	    $tsl->fromPhone = $fromPhone;
	    $tsl->toPhone = $toPhone;
	    $tsl->smsBy = $smsBy;
	    $tsl->smsMessageSid = $smsMessageSid;
	    $tsl->smsSid = $smsSid;
	    $tsl->smsStatus = $smsStatus;
	    $tsl->body = $body;

	    $tsl->save();

	   // $this->sendemailalert(array('anthony.shayesteh@plexuss.com') , $input);
	}

	/**
	 * smsReceive
	 * Receive text message using Twilio API
	 * @return null
	 */
	public function smsReceive(){

		$input = Request::all();
		$data  = array();

		$fromPhone =  (isset($input['From']) ? $input['From'] : -1); 
		$toPhone = (isset($input['To']) ? $input['To'] : -1);
		$smsMessageSid = (isset($input['SmsMessageSid']) ? $input['SmsMessageSid'] : -1);
		$data['smsSid']			  = (isset($input['SmsSid']) ? $input['SmsSid'] : -1);
		$data['smsStatus'] 		  = (isset($input['SmsStatus']) ? $input['SmsStatus'] : 'NaN');
		$data['msg'] 			  = (isset($input['Body']) ? $input['Body'] : -1);
		$data['smsBy']            = 'User'; 
		$data['num_segments']	  = (isset($input['NumSegments']) ? $input['NumSegments'] : -1);
		$data['num_media']		  = (isset($input['NumMedia']) ? $input['NumMedia'] : -1);

		$sms_log = SmsLog::on('rds1')->where('to', $fromPhone)
									 ->where('from', $toPhone)
									 ->orderBy('id','DESC')
									 ->first();

		if (!isset($sms_log)) {
			$sms_log = PhoneLog::on('rds1')
			                   ->where('to', $fromPhone)
							   ->where('from', $toPhone)
							   ->orderBy('id','DESC')
							   ->first();
		}

		if (isset($sms_log)) {

			$data['from'] 			  = $fromPhone;
			$data['to']   			  = $toPhone;
			$data['campaign_id'] 	  = $sms_log->campaign_id;
			$data['thread_id']		  = $sms_log->thread_id;
			$data['sender_user_id']   = $sms_log->receiver_user_id;
			$data['receiver_user_id'] = $sms_log->sender_user_id;
			$data['is_list_user']	  = $sms_log->is_list_user;

		}

		$sl = new SmsLog;

		$sl->campaign_id   	  = $data['campaign_id'];
		$sl->thread_id   	  = $data['thread_id'];
	    $sl->sender_user_id   = $data['sender_user_id'];
	    $sl->receiver_user_id = $data['receiver_user_id'];
	    $sl->is_list_user  	  = $data['is_list_user'];
	    $sl->from 	  	   	  = $data['from'];
	    $sl->to 	  	   	  = $data['to'];
	    $sl->smsBy 		   	  = $data['smsBy'];
	    $sl->sid 		   	  = $data['smsSid'];
	    $sl->status 	   	  = $data['smsStatus'];
	    $sl->body 		   	  = $data['msg'];
		$sl->num_media        = $data['num_media'];
		$sl->num_segments     = $data['num_segments'];

	    $sl->save();

	    if (!empty($data['thread_id'])) {
	    	$thread_id = $data['thread_id'];
	    }elseif($data['receiver_user_id'] != -1){
	    	$cmt = DB::connection('rds1')->table('college_message_threads as cmt')
		    							 ->join('college_message_thread_members as cmtm', 'cmtm.thread_id', '=', 'cmt.id')
		    							 ->where('cmt.campaign_id', $data['campaign_id'])
		    							 ->where('cmtm.user_id', $data['sender_user_id'])
		    							 ->select('cmt.id')
		    							 ->first();

		    if(!isset($cmt)){
		    	$gmc 	= new GroupMessagingController;
		    	$update = $gmc->sendReadyCampaign($data['campaign_id']);

		    	$cmt = DB::connection('rds1')->table('college_message_threads as cmt')
		    							 ->join('college_message_thread_members as cmtm', 'cmtm.thread_id', '=', 'cmt.id')
		    							 ->where('cmt.campaign_id', $data['campaign_id'])
		    							 ->where('cmtm.user_id', $data['sender_user_id'])
		    							 ->select('cmt.id')
		    							 ->first();
		    }

		    $thread_id = $cmt->id;
	    }
	   
	    if(isset($thread_id)){
		    $attr =  array('thread_id' => $thread_id, 'user_id' => $data['sender_user_id'], 'msg' => $data['msg']);
		    $val  =  array('thread_id' => $thread_id, 'user_id' => $data['sender_user_id'], 'msg' => $data['msg'],
		    			   'is_text'   => 1);

		    $update = CollegeMessageLog::updateOrCreate($attr, $val);


		    $cmtm = CollegeMessageThreadMembers::on('rds1')->where('thread_id', $thread_id)->first();

		    $this->calculateReceivedTextMessage($cmtm, $data);

		    // increment number of sent messages

		    $college_message_thread_id = $thread_id;
			$my_user_id = $data['sender_user_id'];

			if($data['is_list_user'] == -1){
				$this_is_list_user = 0;
			}else{
				$this_is_list_user = $data['is_list_user'];
			}

			$ids = DB::connection('rds1')
				->table('college_message_thread_members as cmtm')
				->where('user_id', $my_user_id)
				->where('thread_id', $college_message_thread_id)
				->where('is_list_user', $this_is_list_user)
				->pluck('id');
			
			if(!empty($ids)){
				DB::table('college_message_thread_members')
					->whereIn('id', $ids)
					->increment('num_unread_msg');
			}

			//Reset all the cache values of each member of each thread, and increment the num of unread msgs
			$thread_members = CollegeMessageThreadMembers::where('user_id', '!=', $my_user_id)
								->where('thread_id', '=', $college_message_thread_id)
								->where('is_list_user', 0)
								->pluck('user_id');

			$this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $my_user_id);
			$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $my_user_id);
			$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $my_user_id);

			foreach ($thread_members as $k) {
				Cache::forget(env('ENVIRONMENT') .'_'.$k.'_msg_thread_ids');

				$this->setThreadListStatusToBad('_usersTopicsAndMessages_agencies_', $k);
				$this->setThreadListStatusToBad('_usersTopicsAndMessages_users_', $k);
				$this->setThreadListStatusToBad('_usersTopicsAndMessages_colleges_', $k);
			}

			$ids = DB::connection('rds1')
				->table('college_message_thread_members as cmtm')
				->whereIn('user_id', $thread_members)
				->where('thread_id', $college_message_thread_id)
				->pluck('id');

			if(!empty($ids)){
				DB::table('college_message_thread_members')
					->whereIn('id', $ids)
					->update( array(
						'num_unread_msg' => DB::raw('num_unread_msg + 1'),
						'email_sent' => 0
					));
			}
		}
		
		// Add to suppression if indicated.
	    if (strpos(strtolower(trim($data['msg'])), 'stop') !== FALSE) {
	    	$org_branch_id = isset($cmtm->org_branch_id) ? $cmtm->org_branch_id : NULL;
	    	$user = User::on('rds1')->find($data['sender_user_id']);

			$attr = array('user_id' => $data['sender_user_id'], 'org_branch_id' => $org_branch_id);
	    	$val  = array('user_id' => $data['sender_user_id'], 'org_branch_id' => $org_branch_id, 'phone' => $user->phone,
	    				  'is_list_user' => $data['is_list_user']);

	    	if ($data['is_list_user'] == 1) {
    			$user = ListUser::on('rds1')->where('id', $data['sender_user_id'])->first();	
			}else{
				$user = User::on('rds1')->where('id', $data['sender_user_id'])->first();
			}

			$val['email'] = $user->email;

	    	$update = TextSuppressionList::updateOrCreate($attr, $val);
	    }

	    // Check if the response is for application text workflow
	    $cmtm = isset($cmtm) ? $cmtm : NULL;
	    $bool = $this->applicationTxtWorkflow($data, $cmtm);

	    // Add to Handshake if indicated.
	    if (!$bool) {
		    if (strpos(strtolower(trim($data['msg'])), 'yes') !== FALSE) {
		    	$tmp = array();
		    	$check = false;

		    	$user = User::on('bk')->find($data['sender_user_id']);
	    		$tmp['fname'] = $user->fname;
	    		$tmp['lname'] = $user->lname;
	    		$tmp['email'] = $user->email;
	    		$tmp['user_id'] = $user->id;

		    	if ($data['receiver_user_id'] == -1) {

		    		$tt = TextTemplate::all();

		    		foreach ($tt as $key) {
		    			$body = $key->body;
		    			$pos  = strpos($body, '}}') + 2;
		    			$body = substr($body, $pos);

		    			$tmp['college_id'] = SmsLog::on('bk')->where('body', 'LIKE', '%'.$body.'%')
		    								  ->where('sender_user_id', -1)
		    								  ->where('receiver_user_id', $tmp['user_id'])
		    								  ->orderBy('id', 'DESC')
		    								  ->pluck('college_id');

		    			$tmp['college_id'] = $tmp['college_id'][0];

		    			if (!empty($tmp['college_id'])) {
		    				$check = true;
		    				break;
		    			}
		    		}
		    	}elseif(isset($data['thread_id'])) {

		    		$tt = TextTemplate::all();

		    		foreach ($tt as $key) {
		    			$body = $key->body;
		    			$pos  = strpos($body, '}}') + 2;
		    			$body = substr($body, $pos);
		    			$tmp['college_id'] = SmsLog::on('bk')->where('thread_id', $data['thread_id'])
		    								  ->where('body', 'LIKE', '%'.$body.'%')
		    								  ->orderBy('id')
		    								  ->pluck('college_id');
		    			
		    			$tmp['college_id'] = $tmp['college_id'][0];

		    			if (isset($tmp['college_id'])) {
		    				$check = true;
		    				break;
		    			}
		    		}
		    	}elseif (isset($data['campaign_id'])) {

		    		$tt = TextTemplate::all();

		    		foreach ($tt as $key) {
		    			$body = $key->body;
		    			$pos  = strpos($body, '}}') + 2;
		    			$body = substr($body, $pos);
		    			$tmp['college_id'] = SmsLog::on('bk')->where('campaign_id', $data['campaign_id'])
		    								  ->where('body', 'LIKE', '%'.$body.'%')
		    								  ->orderBy('id')
		    								  ->pluck('college_id');

		    			$tmp['college_id'] = $tmp['college_id'][0];

		    			if (isset($tmp['college_id'])) {
		    				$check = true;
		    				break;
		    			}
		    		}
		    	}
	    		if ($check) {
	    			$tmp['school_name'] = College::on('rds1')->where('id', $tmp['college_id'])
	    													 ->pluck('school_name');
	    			$tmp['school_name'] = $tmp['school_name'][0];

	    			$sl->college_id = $tmp['college_id'];
	    			$sl->save();
		    		$this->setRecruitModular($tmp, 'text');

		    		// Reply text message
		    		$this->sendReplyTextMessage($user, $tmp['college_id']);
		    	}
		    }
	    }
	}

	private function applicationTxtWorkflow($data, $cmtm){

		if ($data['sender_user_id'] == -1) {
			return false;
		}

		$sl = SmsLog::on('rds1')->where('receiver_user_id', $data['sender_user_id'])
		                        ->orderBy('id', 'DESC')
		                        ->where('status', 'delivered')
		                        ->first();

		if (isset($sl) && $sl->body == "Are you still interested to apply to a university?") {

			$user = User::on('rds1')->find($data['sender_user_id']);

			if (strpos(strtolower(trim($data['msg'])), 'yes') !== FALSE) {

				$input = array();
				$input['msg'] 	  = 'Click here to start your complementary free application to universities http://bit.ly/2o7L8Ax';
				$input['user_id'] = $data['sender_user_id'];
				$input['phone']   = $user->phone;
				// $input['phone']   = '+1 310-598-0347';
				$this->sendTextWorkflow($input);
				
				return true;
			}
			if (strpos(strtolower(trim($data['msg'])), 'no') !== FALSE) {
				
				$input = array();
				$input['msg'] 	  = 'Thank you, we will not text or email you any longer regarding oneApp.';
				$input['user_id'] = $data['sender_user_id'];
				$input['phone']   = $user->phone;
				// $input['phone']   = '+1 310-598-0347';
				$this->sendTextWorkflow($input);

				$org_branch_id = isset($cmtm->org_branch_id) ? $cmtm->org_branch_id : NULL;

				$attr = array('user_id' => $data['sender_user_id'], 'org_branch_id' => $org_branch_id);
		    	$val  = array('user_id' => $data['sender_user_id'], 'org_branch_id' => $org_branch_id,
		    				  'is_list_user' => $data['is_list_user']);

		    	if ($data['is_list_user'] == 1) {
	    			$user = ListUser::on('rds1')->where('id', $data['sender_user_id'])->first();	
				}else{
					$user = User::on('rds1')->where('id', $data['sender_user_id'])->first();
				}

				$val['email'] = $user->email;
				$val['phone'] = $user->phone;

		    	$update = TextSuppressionList::updateOrCreate($attr, $val);

		    	$attr = array('user_id' => $user->id);
		  		$val  = array('is_supressed' => 1, 'user_id' => $user->id);
		  		$update = ApplicationEmailSuppresion::updateOrCreate($attr, $val);

				return true;
			}
		}

		return false;
	}

	private function calculateReceivedTextMessage($cmtm, $data){

		$admin_text = AdminText::where('org_branch_id', $cmtm->org_branch_id)->first();

		switch ($admin_text->tier) {
			case 'free':
				AdminText::where('org_branch_id', $cmtm->org_branch_id)
						 ->decrement('num_of_free_texts', $data['num_segments']);
				break;

			case 'flat_fee':
				AdminText::where('org_branch_id', $cmtm->org_branch_id)
						 ->decrement('num_of_eligble_texts', $data['num_segments']);

				break;
			
			default:
				# code...
				break;
		}
	}

	public function followupInfilawSms(){

		$tt = InfilawSurveyUser::where('is_finished', 1)
								->where('interested_in_stipend', 'No')
								->get();

		foreach ($tt as $key) {
			
			$smsBodyArr = array();

			$first_name = explode(" ", $key->name);
			if ( $key->school_name == "Florida Coastal School of Law" ) {
				$fromPhone = "8777463228";

				$smsBodyArr[] = "Hello ".ucfirst($first_name[0])."! We hope that you received the $50 Amazon card. You qualify for at least $1,000 more stipend.  A representative from ".$key->school_name." will call you to go over the details. We just need your employer name, start date at current employer and exact 2015 income. Please provide if you are interested.";
				$smsBodyArr[] = "Hello ".ucfirst($first_name[0])."! We hope that you received the $50 Amazon card. You qualify for at least $1,000 more stipend.  A representative from ".$key->school_name." will call you to go over the details. We just need your employer name, start date at current employer and exact 2015 income. Please provide if you are interested.";
				$smsBodyArr[] = "Hello ".ucfirst($first_name[0])."! We hope that you received the $50 Amazon card. You qualify for at least $1,000 more stipend.  A representative from ".$key->school_name." will call you to go over the details. We just need your employer name, start date at current employer and exact 2015 income. Please provide if you are interested.";
				$smsBodyArr[] = "Hello ".ucfirst($first_name[0])."! We hope that you received the $50 Amazon card. You qualify for at least $1,000 more stipend.  A representative from ".$key->school_name." will call you to go over the details. We just need your employer name, start date at current employer and exact 2015 income. Please provide if you are interested.";
				
			}elseif ( $key->school_name == "Charlotte School of Law" ) {
				$fromPhone = "8777463228";

				$smsBodyArr[] = "Hello ".ucfirst($first_name[0])."! We hope that you received the $50 Amazon card. You qualify for at least $1,000 more stipend.  A representative from ".$key->school_name." will call you to go over the details. We just need your employer name, start date at current employer and exact 2015 income. Please provide if you are interested.";
				$smsBodyArr[] = "Hello ".ucfirst($first_name[0])."! We hope that you received the $50 Amazon card. You qualify for at least $1,000 more stipend.  A representative from ".$key->school_name." will call you to go over the details. We just need your employer name, start date at current employer and exact 2015 income. Please provide if you are interested.";
				$smsBodyArr[] = "Hello ".ucfirst($first_name[0])."! We hope that you received the $50 Amazon card. You qualify for at least $1,000 more stipend.  A representative from ".$key->school_name." will call you to go over the details. We just need your employer name, start date at current employer and exact 2015 income. Please provide if you are interested.";
				$smsBodyArr[] = "Hello ".ucfirst($first_name[0])."! We hope that you received the $50 Amazon card. You qualify for at least $1,000 more stipend.  A representative from ".$key->school_name." will call you to go over the details. We just need your employer name, start date at current employer and exact 2015 income. Please provide if you are interested.";
			}

			$randNum = rand( 0, 2 );

			$msg = $smsBodyArr[$randNum];
			$toPhone = $key->phone;
			//$toPhone = $key->secondaryPhone;
			$leadid = $key->id;

			print_r($leadid."<br>");
			print_r($msg."<br>");
			//$temp_tt = DB::connection('ldy')->statement('UPDATE `tracking_test` set `call_count` = `call_count`+1 where `id` = '.$leadid);


			$this->sendSms($fromPhone, $toPhone, $msg, 'AutoContact', $leadid);

		}
	}

	public function infilawSms(){

		// $tt = TrackingTest::where(function ($query){
		// 						$query->orWhere('pixel', '=', 'csl-survey');
		// 						$query->orWhere('pixel', '=', 'fcsl-survey');
		// 					})
		// 					->whereNull('ad_num')
		// 					->where('call_count', 3)
		// 					->take(50)
		// 					->orderBy(DB::raw('RAND()'))
		// 					->get();

		$tt = TrackingTest::where(function ($query){
								$query->orWhere('pixel', '=', 'csl-survey');
								$query->orWhere('pixel', '=', 'fcsl-survey');
							})
							->whereNull('ad_num')
							->where('call_count', 4)
							->where('phone', '!=', DB::raw('secondaryPhone'))
							->orderBy(DB::raw('RAND()'))
							->get();
		$cnt = 0;
		foreach ($tt as $key) {

			$phone = $key->phone;
			$secondaryPhone = $key->secondaryPhone;

			$phone = str_replace("-", "", $phone);

			
			if ($phone === $secondaryPhone) {
				continue;
			}
			// echo "---".$phone."---".$secondaryPhone."---<br>";
			$cnt++;
			$smsBodyArr = array();

			if ( $key->pixel == "fcsl-survey" ) {
				$fromPhone = "8777463228";

				$smsBodyArr[] = "Hello ".ucfirst($key->first_name).",  Today (12/28/2015) is the last day to take advantage of the $50 Amazon card. If you are interested, here is the link to complete the 3-minute survey: http://bit.ly/1jTjLXE";
				$smsBodyArr[] = "Hello ".ucfirst($key->first_name).",  Today (12/28/2015) is the last day to take advantage of the $50 Amazon card. If you are interested, here is the link to complete the 3-minute survey: http://bit.ly/1jTjLXE";
				$smsBodyArr[] = "Hello ".ucfirst($key->first_name).",  Today (12/28/2015) is the last day to take advantage of the $50 Amazon card. If you are interested, here is the link to complete the 3-minute survey: http://bit.ly/1jTjLXE";
				$smsBodyArr[] = "Hello ".ucfirst($key->first_name).",  Today (12/28/2015) is the last day to take advantage of the $50 Amazon card. If you are interested, here is the link to complete the 3-minute survey: http://bit.ly/1jTjLXE";
				
			}elseif ( $key->pixel == "csl-survey" ) {
				$fromPhone = "8777463228";

				$smsBodyArr[] = "Hello ".ucfirst($key->first_name).",  Today (12/28/2015) is the last day to take advantage of the $50 Amazon card. If you are interested, here is the link to complete the 3-minute survey: http://bit.ly/1jTjLXE";
				$smsBodyArr[] = "Hello ".ucfirst($key->first_name).",  Today (12/28/2015) is the last day to take advantage of the $50 Amazon card. If you are interested, here is the link to complete the 3-minute survey: http://bit.ly/1jTjLXE";
				$smsBodyArr[] = "Hello ".ucfirst($key->first_name).",  Today (12/28/2015) is the last day to take advantage of the $50 Amazon card. If you are interested, here is the link to complete the 3-minute survey: http://bit.ly/1jTjLXE";
				$smsBodyArr[] = "Hello ".ucfirst($key->first_name).",  Today (12/28/2015) is the last day to take advantage of the $50 Amazon card. If you are interested, here is the link to complete the 3-minute survey: http://bit.ly/1jTjLXE";
			}

			$randNum = rand( 0, 2 );

			$msg = $smsBodyArr[$randNum];
			//$toPhone = $key->phone;
			$toPhone = $phone;
			$leadid = $key->id;

			print_r($leadid."<br>");
			print_r($msg."<br>");
			$temp_tt = DB::connection('ldy')->statement('UPDATE `tracking_test` set `call_count` = `call_count`+1 where `id` = '.$leadid);


			$this->sendSms($fromPhone, $toPhone, $msg, 'AutoContact', $leadid);

		}

		echo $cnt;
	}

	/**
	 * smsCallBack
	 * Fall back function for text message using Twilio API
	 * @return null
	 */
	public function smsCallBack(){

		$input = Request::all();

		$attr = array('sid' => $input['SmsSid']);
		$val  = array('sid' => $input['SmsSid'], 'error_url' => $input['ErrorUrl'], 'error_code' => $input['ErrorCode'],
					  'num_segments' => $input['NumSegments'], 'status' => $input['SmsStatus'], 'from' => $input['From'],
					  'to' => $input['To'], 'body' => $input['Body'], 'num_media' => $input['NumMedia']);

		$update = SmsLog::updateOrCreate($attr, $val);
	}

	public function searchForPhoneNumbers(){

		$input = Request::all();

		$term 	   = $input['term'];
		$in_region = $input['InRegion'];

		if ($in_region == '') {
			if (!is_numeric($term)) {
				$searchBy = 'pattern';
			}else{
				
				if (strlen($term) == 3) {
					$searchBy = 'areaCode';
				}elseif (strlen($term) == 5) {
					$searchBy = 'zipCode';
				}else{
					$searchBy = 'numberPattern';
				}
			}
		}else{
			$searchBy = 'state';
			$term = $in_region;
		}

		$is_toll_free = 'Local';
		
		if (isset($input['isTollFree']) && $input['isTollFree'] == 'yes') {
			$is_toll_free = 'TollFree';
		}
		
		$param = array();

		switch ($searchBy) {
			case 'areaCode':
				$param['AreaCode'] = $term;
				break;
			
			case 'numberPattern':
				$param['Contains'] = $term;

				$len = strlen($term);

				$num_of_stars = 10 - $len;

				if ($num_of_stars > 0) {
					for ($i=0; $i < $num_of_stars ; $i++) { 
						$param['Contains'] .= '*';
					}
				}

				break;
			case 'pattern':
				$param['Contains'] = $term;
				break;

			case 'state':
				$param['InRegion'] = $term;
				break;

			case 'zipCode':
				$zip = new ZipCodes;
				$latLong = $zip->getLatLongByZip($term);
				$param['NearLatLong'] = $latLong->Latitude .','. $latLong->Longitude;
				break;

			default:
				# code...
				break;
		}
		
		$ret = array();

		$numbers = $this->client->account->available_phone_numbers->getList('US', $is_toll_free, $param);
		foreach($numbers->available_phone_numbers as $number) {
		    $ret[] = $number->phone_number;
		}

		return json_encode($ret);
	}

	public function purchasePhone(){

		$viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $now = Carbon::now();
        $next_month = $now->addMonth();

        $next_month = $next_month->toDateString(); 
       
        $input = Request::all();

        if (!isset($input['phone'])) {
        	return "Phone is required!";
        }

        $input['phone'] = preg_replace( '/[^0-9]/', '', $input['phone'] );

        try {
        	$number = $this->client->account->incoming_phone_numbers->create(array(
	        	"FriendlyName" 	 => $data['org_name'],
			    "PhoneNumber"    => $input['phone'],
			    "SmsUrl" 		 => "https://plexuss.com/phone/sms/receive",
			    "SmsFallbackUrl" => "https://plexuss.com/phone/sms/callback",
			    "VoiceUrl"       => "http://twimlets.com/forward?PhoneNumber=3102373494",
			    "VoiceMethod"    => "GET"
			));

        } catch (\Exception $e) {
        	return "Something went wrong purchasing the phone number";
        }

        $attr = array( 'phone' => $input['phone'], "org_branch_id" => $data['org_branch_id']);
        $val  = array( 'phone' => $input['phone'], "org_branch_id" => $data['org_branch_id'], 
        			   "sid" => $number->sid, "purchased_by_user_id" => $data['user_id'],
        			   "expires_at" => $next_month);
        
        $update = PurchasedPhone::updateOrCreate($attr, $val);


        $attr = array('org_branch_id' => $data['org_branch_id']);
        $val  = array('org_branch_id' => $data['org_branch_id'], 'num_of_eligble_texts' => Self::NUM_OF_FREE_TEXT,
        			  'tier' => 'free');

        $update = AdminText::updateOrCreate($attr, $val);

        return "success";
	}

	public function sendHandshakeTxt($user, $school_info){

		if ($user->txt_opt_in == 1) {

			$data['from'] = $this->from;
			$data['to']   = $user->phone;

			$template = DB::connection('rds1')
				->table('text_templates')
				->where('type', 'handshake')
				->pluck('body');

			$template = $template[0];

			$start = stripos($template, '{{');
			$stop = stripos($template, '}}') + 2;

			$data['msg'] = substr($template,0,$start).$school_info['school_name'].substr($template,$stop);
			$data['college_id'] = $school_info['college_id'];
			$data['receiver_user_id'] = $user->id;
			$data['user_id'] = -1;
			$data['campaign_id']  = NULL;
			$data['smsBy'] = 'Plexuss';

			$response = $this->sendSingleSms($data);
		}
	}

	public function sendPendingTxt($user, $school_info){

		if ($user->txt_opt_in == 1 && isset($user->address) && isset($user->city) && isset($user->phone) && isset($user->zip)){

			$template = DB::connection('rds1')
				->table('text_templates')
				->where('type', 'pending')
				->pluck('body');

			$template = $template[0];

			$start = stripos($template, '{{');
			$stop = stripos($template, '}}') + 2;

			$data = array();
			$data['msg'] = substr($template,0,$start).$school_info['school_name'].substr($template,$stop);
			$data['from'] = $this->from;
			$data['to']   = $user->phone;
			$data['college_id'] = $school_info['college_id'];

			$data['receiver_user_id'] = $user->id;
			$data['user_id'] = -1;
			$data['campaign_id']  = NULL;
			$data['smsBy'] = 'Plexuss';

			$response = $this->sendSingleSms($data);
		}
	}

	public function sendPlexussMsg($msg){	

		$arr = array();

		// Anthony
		$tmp = array();
		$tmp['user_id'] = 93;
		$tmp['phone']   = '310-598-0347';

		$arr[] = $tmp;

		// Sina
		$tmp = array();
		$tmp['user_id'] = 120;
		$tmp['phone']   = '310-237-3494';

		$arr[] = $tmp;

		// JP
		$tmp = array();
		$tmp['user_id'] = 127;
		$tmp['phone']   = '408-646-2000';

		$arr[] = $tmp;

		foreach ($arr as $key) {
			$data = array();
			$data['msg'] = $msg;
			$data['from'] = $this->from;
			$data['to']   = $key['phone'];
			$data['college_id'] = NULL;

			$data['receiver_user_id'] = $key['user_id'];
			$data['user_id'] = -1;
			$data['campaign_id']  = NULL;
			$data['smsBy'] = 'Plexuss';

			$response = $this->sendSingleSms($data);
		}	
	}

	public function releasePurchasePhone(){

		$today = Carbon::today();
		$today = $today->toDateString(); 

		$_this_client = $this->client;

		$pp = PurchasedPhone::on('rds1')
							->where('expires_at', $today)
							->get();

		foreach ($pp as $key) {
			$_this_client->account->incoming_phone_numbers->delete($key->sid);
			$key->delete();
		}

		return "success";
	}

	/**
	 * sendPhoneConfirmation
	 * Send a confirmation code to the user to verify their phone number 
	 * @return json
	 */
	public function sendPhoneConfirmation($input = null, $api_input = null){
		
		if( isset($api_input) ){
			$data = array();
			$data['user_id'] = $api_input['user_id'];
			$input = $api_input;
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);
		}

		// Check to see how many times we have sent verification texts to this person today.
		$sl = SmsLog::on('rds1')->where('receiver_user_id', $data['user_id'])
								->where('created_at', '>=', Carbon::today())
								->where('created_at', '<=', Carbon::tomorrow())
								->where('body', 'LIKE', '%is your Plexuss verification code.')
								->count();

		// If more than 5 times, then don't send another text message.
		if ($sl > 5) {

			$ret = array();
	    	$ret['response'] = 'failed';
		    $ret['error_message'] = 'You have reached the maximum amount of verification texts. If you still have issues please email us at support@plexuss.com';

	    	return json_encode($ret);
		}


		if( !isset($input) ){
			$input = Request::all();
		}

		if (!isset($input['phone'])) {
			$ret = array();
		    $ret['response'] = 'failed';
		    $ret['error_message'] = 'Phone is required';
		    return json_encode($ret);
		}

		$input['phone'] = '+'.$input['dialing_code'].$input['phone'];

		$msg = rand(1000, 9999);

		$msg = $msg. ' is your Plexuss verification code.';

		$data['to']   		 = $input['phone'];
		$data['from'] 		 = $this->from;
		$data['msg']  		 = $msg;
		$data['campaign_id'] = null;
		$data['smsBy']       = 'Plexuss';
		$data['receiver_user_id'] = $data['user_id'];
		$data['user_id']     = -1;
 
		$txt_response = $this->sendSingleSms($data);

		if ($txt_response == 0) {
			$ret = array();
		    $ret['response'] = 'failed';
		    $ret['error_message'] = 'Invalid phone number!';
		    return json_encode($ret);
		}

	    $vpcl = new VerifyPhoneCodeLog;
	    $vpcl->user_id = $data['receiver_user_id'];
	    $vpcl->code    = $msg;

	    $vpcl->save();

	    $ret = array();
	    $ret['response'] = 'success';

	    return json_encode($ret);
	}

	/**
	 * sendTextWorkflow
	 * Send text message to workflow users
	 * @return json
	 */
	public function sendTextWorkflow($input = null){

		$data = array();

		if( !isset($input) ){
			$input = Request::all();
		}

		if (!isset($input['phone'])) {
			$ret = array();
		    $ret['response'] = 'failed';
		    $ret['error_message'] = 'Phone is required';
		    return json_encode($ret);
		}

		// $input['phone'] = '+'.$input['dialing_code'].$input['phone'];

		// $msg = rand(1000, 9999);

		// $msg = $msg. ' is your Plexuss verification code.';

		$msg = $input['msg'];

		$data['to']   		 = $input['phone'];
		$data['from'] 		 = $this->from;
		$data['msg']  		 = $msg;
		$data['campaign_id'] = null;
		$data['smsBy']       = 'Plexuss';
		$data['receiver_user_id'] = $input['user_id'];
		$data['user_id']     = -1;
 
		$txt_response = $this->sendSingleSms($data);

		if ($txt_response == 0) {
			$ret = array();
		    $ret['response'] = 'failed';
		    $ret['error_message'] = 'Invalid phone number!';
		    return json_encode($ret);
		}

	    $ret = array();
	    $ret['response'] = 'success';

	    return json_encode($ret);
	}

	/**
	 * checkPhoneConfirmation
	 * Check the confirmation code sent to the user's cell phone
	 * @return json
	 */
	public function checkPhoneConfirmation($api_input = null){

		if( isset($api_input) ){
			$request = $api_input;
			$user_id = $api_input['user_id'];
		}else{
			$request = Request::all();
			$user_id = Session::get('userinfo.id');
		}

		if (!isset($request['code'])) {
			$ret = array();
		    $ret['response'] = 'failed';
		    $ret['error_message'] = 'Code is required!';
		    return json_encode($ret);
		}

		$now = Carbon::now();

		$vpcl = VerifyPhoneCodeLog::on('rds1')
								  ->where('user_id', $user_id)
								  ->where('created_at', '>=', $now->subMinutes(5))
								  ->where('code', $request['code'])
								  ->first();

		if (!isset($vpcl)) {
			$ret = array();
		    $ret['response'] = 'failed';
		    $ret['error_message'] = 'Invalid/expired code, try again!';
		    return json_encode($ret);
		}

		$user = User::find($user_id);
		$user->verified_phone = 1;

		$user->save();

		$ret = array();
	    $ret['response'] = 'success';
	    return json_encode($ret);
	}

	public function sendSms($data, $text_message_campaign_users = null){

		$at = AdminText::where('org_branch_id', $data['org_branch_id'])
					   ->first();

		if (!isset($at)) {
			return false;
		}

		if (!isset($text_message_campaign_users)) {
			$text_message_campaign_users = Session::get('text_message_campaign_users');
		}

		if (!isset($text_message_campaign_users)) {
			return false;
		}

		$num_of_eligble_texts = $at->num_of_eligble_texts;
		$num_of_free_texts    = $at->num_of_free_texts;

		$pp = PurchasedPhone::on('rds1')->where('org_branch_id', $data['org_branch_id'])->first();

		if (!isset($pp)) {
			return false;
		}

		$text_message_campaign_users = (array)$text_message_campaign_users;
		$data['users_to_send_txt_msg'] = $text_message_campaign_users['total_eligble_users'];
		$data['users_to_send_txt_msg'] = $this->formatPhoneNumber($data['users_to_send_txt_msg']);
		switch ($at->tier) {
			case 'free':
				$data['smsBy']  = 'CollegeRep';
				$data['from']   = $pp->phone;
				
				// We don't send MMS for Free tiers
				//$data['images'] = $this->getImageUrlsFromHTMLText($data['campaign_body']);
				$orginal_msg    = strip_tags($data['campaign_body']);

				$num_of_sent_segments = 0;
				
				foreach ($data['users_to_send_txt_msg'] as $key) {
					//$data['to']	   = preg_replace("/^[0-9]*$/", "", $key['phone']);
					$key = (array)$key;
					$data['to'] = $key['phone'];
					$data['receiver_user_id'] = $key['id'];

					$vars_user = User::on('bk')->find($key['id']);
					
					if (!isset($vars_user)) {
						continue;	
					}

					$vars = array();
					$vars['STUDENTNAME'] = $this->convertNameToUTF8(ucfirst(strtolower($vars_user->fname)) . ' ' . ucfirst(strtolower($vars_user->lname)));
					$data['msg'] = $this->setSubstitutionData($orginal_msg, $vars);
			
					if ($num_of_free_texts > 0) {
						$num_of_sent_segments += $this->sendSingleSms($data);
					}
				}

				$num_of_free_texts     = $num_of_free_texts - $num_of_sent_segments; 
				$at->num_of_free_texts = $num_of_free_texts;

				$at->save();
				
				break;

			case 'pay_as_you_go':
				$data['smsBy']  = 'CollegeRep';
				$data['from']   = $pp->phone;
				
				// We don't send MMS for Free tiers
				$data['images'] = $this->getImageUrlsFromHTMLText($data['campaign_body']);
				$orginal_msg    = strip_tags($data['campaign_body']);
				
				foreach ($data['users_to_send_txt_msg'] as $key) {
					$key = (array)$key;
					//$data['to']	   = preg_replace("/^[0-9]*$/", "", $key['phone']);
					$data['to'] = $key['phone'];
					$data['receiver_user_id'] = $key['id'];

					$vars_user = User::on('bk')->find($key['id']);
					
					if (!isset($vars_user)) {
						continue;	
					}

					$vars = array();
					$vars['STUDENTNAME'] = $this->convertNameToUTF8(ucfirst(strtolower($vars_user->fname)) . ' ' . ucfirst(strtolower($vars_user->lname)));
					$data['msg'] = $this->setSubstitutionData($orginal_msg, $vars);
			

					if ($num_of_eligble_texts > 0) {
						$this->sendSingleSms($data);

						$num_of_eligble_texts--;
					}else{
						break;
					}
				}

				$at->num_of_eligble_texts = $num_of_eligble_texts;

				$at->save();

				break;		
			case 'flat_fee':
				$data['smsBy']  = 'CollegeRep';
				$data['from']   = $pp->phone;
				
				// We don't send MMS for Free tiers
				$data['images'] = $this->getImageUrlsFromHTMLText($data['campaign_body']);
				$orginal_msg    = strip_tags($data['campaign_body']);

				$num_of_sent_segments = 0;
			
				foreach ($data['users_to_send_txt_msg'] as $key) {
					//$data['to']	   = preg_replace("/^[0-9]*$/", "", $key['phone']);
					$key = (array)$key;
					$data['to'] = $key['phone'];
					$data['receiver_user_id'] = $key['id'];

					$vars_user = User::on('bk')->find($key['id']);

					if (!isset($vars_user)) {
						continue;	
					}

					$vars = array();
					$vars['STUDENTNAME'] = $this->convertNameToUTF8(ucfirst(strtolower($vars_user->fname)) . ' ' . ucfirst(strtolower($vars_user->lname)));
					$data['msg'] = $this->setSubstitutionData($orginal_msg, $vars);
			
					if ($num_of_eligble_texts > 0) {
						$num_of_sent_segments += $this->sendSingleSms($data);
					}
				}

				$num_of_eligble_texts     = $num_of_eligble_texts - $num_of_sent_segments; 
				$at->num_of_eligble_texts = $num_of_eligble_texts;

				$at->save();
				
				break;

			default:
				# code...
				break;
		}
	}

	public function sendSingleSms($data){
		
		$_this_client = $this->client;

		$arr     = array();
		$img_arr = array();

		if (isset($data['images'])) {
			
			foreach ($data['images'] as $key => $value) {
				array_push($img_arr, $value);
			}
		}

		//message is in $data['msg']
		//html codes/names need to be replaced
		$data['msg'] = str_replace('&nbsp;', ' ', $data['msg']);
		$data['msg'] = str_replace('&amp;', '&', $data['msg']);
		$data['msg'] = str_replace('&ndash;', '-', $data['msg']);

		try {
			// $message = $_this_client->account->messages->sendMessage($data['from'], $data['to'], $data['msg'], $img_arr);
			$message = $_this_client->messages->create($data['to'], // to
						                           array(
						                               "body" => $data['msg'],
						                               "from" => $data['from'],
						                               "mediaUrl" => $img_arr
						                           ));

		} catch (\Exception $e) {
			return 0;
		}

		$smsStatus = (isset($message->Status) ? $message->Status : 'delivered');

		$is_list_user = Cache::get(env('ENVIRONMENT').'_'.$data['user_id'].'_is_list_user');

		if (isset($data['thread_id']) && !empty($data['thread_id']) && $data['thread_id'] != 0) {
			$thread_id = $data['thread_id'];
		}else{
			$thread_id = NULL;
		}

		$sl = new SmsLog;

		$sl->college_id 	  = isset($data['college_id']) ? $data['college_id'] : null;
		$sl->campaign_id   	  = $data['campaign_id'];
		$sl->thread_id 		  = $thread_id;
	    $sl->sender_user_id   = $data['user_id'];
	    $sl->receiver_user_id = $data['receiver_user_id'];
	    $sl->from 	  	   	  = $message->from;
	    $sl->to 	  	   	  = $message->to;
	    $sl->smsBy 		   	  = $data['smsBy'];
	    $sl->sid 		   	  = $message->sid;
	    $sl->status 	   	  = $smsStatus;
	    $sl->body 		   	  = $data['msg'];
	    $sl->price 		   	  = $message->price;
		$sl->price_unit    	  = isset($message->price_unit) ? $message->price_unit : NULL;
		$sl->error_code       = isset($message->error_code) ? $message->error_code : NULL; 
		$sl->error_message    = isset($message->error_message) ? $message->error_message : NULL; 
		$sl->num_media        = isset($message->num_media) ? $message->num_media : NULL;
		// $sl->num_segments     = isset($message->num_segments) ? $message->num_segments : NULL; 

		if (isset($is_list_user) && $is_list_user == 1) {
			$sl->is_list_user  	  =  1;
		}else{
			$sl->is_list_user  	  = -1;
		}

	    $sl->save();

	    return 1;
	}

	private function formatPhoneNumber($input){
		
		$ret = array();

		foreach ($input as $key) {
			$key = (array)$key;
			// dd(strpos($key['phone'], '+'));
			if ($key['country_code'] !== 'US' && strpos($key['phone'], '+') != 0 ) {
				
				$ctr = Country::on('rds1')->where('country_name', $key['country_name'])->first();

				if (!isset($ctr)) {
					$key['phone'] = '+'.$key['phone']; 
				}else{
					$cpc = $ctr['country_phone_code'];
					$len = strlen($cpc);	

					$tmp_phone = substr($key['phone'], 0, $len-1);

					if ($tmp_phone == $cpc) {
						$key['phone'] = '+'.$key['phone'];
					}else{
						$key['phone'] = '+'.$cpc.$key['phone'];
					}
				}
				
			}

			$ret[] = $key;
		}

		return $ret;
	}

	public function sendReplyTextMessage($user, $college_id){

		if ($user->txt_opt_in == 1){

			$college_info = DB::connection('rds1')->table('organization_branches AS ob')
												  ->join('organization_branch_permissions AS obp', 'ob.id', '=', 'obp.organization_branch_id')
												  ->join('users as u', 'u.id', '=', 'obp.user_id')
												  ->join('colleges as c', 'c.id', '=', 'ob.school_id')
												  ->where('c.id', $college_id)
												  ->groupBy('ob.id')
												  ->select('u.fname', 'c.school_name', 'c.alias')
												  ->first();
			if(isset($college_info)){
				$rtt  = ReplyTextTemplate::on('rds1')->where('type', 'pending')->first();
				$body = $rtt->body; 

				if (isset($college_info->alias) && !empty($college_info->alias)) {
					
					if (strpos($college_info->alias, '|') !== FALSE){
						$alias = explode('|', $college_info->alias);
						$college_name = trim($alias[0]);
					}else{
						$college_name = trim($college_info->alias);
					}
				}else{
					$college_name = $college_info->school_name;
				}

				$body = str_replace('{{REP_FIRSTNAME}}', $college_info->fname, $body);
				$body = str_replace('{{COLLNAME}}', $college_name, $body);

				$data = array();
				$data['msg'] = $body;
				$data['from'] = $this->from;
				$data['to']   = $user->phone;
				$data['college_id'] = $college_id;

				$data['receiver_user_id'] = $user->id;
				$data['user_id'] = -1;
				$data['campaign_id']  = NULL;
				$data['smsBy'] = 'Plexuss';

				$response = $this->sendSingleSms($data);
			}		
		}
	}









	//////////////////////////////////////////////////////////////////////////////
	// 				Phone Calls
	//////////////////////////////////////////////////////////////////////////////



	/*********************************************
	*  capability token needs to get generated to make phone calls
	* 
	**********************************************/

	public function makeCall(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

        if (isset($data['org_branch_id']) && isset($data['user_id'])) {
            $client_name = $this->getTwimlClientName($data['org_branch_id'], $data['user_id']);
        }

		// put your Twilio API credentials here
		$accountSid = env('TWILIO_SID');
		$authToken  = env('TWILIO_TOKEN');

		// put your TwiML Application Sid here

		$appSid = env('TWILIO_APPSID');

		$capability = new \Services_Twilio_Capability($accountSid, $authToken);
        $capability->allowClientOutgoing($appSid);

        if (isset($client_name)) {
		    $capability->allowClientIncoming($client_name);
        }

		$token = $capability->generateToken();

		$data = array();
		$data['token'] = $token;

		//return View('twilio.master', $data);
		// return View('admin.contactPane.contactPaneCall', $data);
		return $token;
	}

    // Tries to get the client_name from PurchasedPhone table, 
    // Returns client_name or null depending on if it exists;
    private function getTwimlClientName($org_branch_id, $user_id) {
        $client_name = null;

        $pp = PurchasedPhone::where('org_branch_id', '=', $org_branch_id)->where('user_id', '=', $user_id)->first();

        if (!isset($pp)) {
            $pp = PurchasedPhone::where('org_branch_id', '=', $org_branch_id)->first();

            if (isset($pp) && isset($pp->twiml_client_name)) {
                $client_name = $pp->twiml_client_name;
            }

        } else if (isset($pp->twiml_client_name)) {
            $client_name = $pp->twiml_client_name;
        }

        return $client_name;
    }

	public function initilizePhoneLog() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$attr = array('sender_user_id' => $data['user_id'], 'receiver_user_id' => $input['user_id'], 
					  'org_branch_id' => $data['org_branch_id'], 'status' => 'ready', 'to' => $input['phoneNumber']);
		$val = array('sender_user_id' => $data['user_id'], 'receiver_user_id' => $input['user_id'], 
					  'org_branch_id' => $data['org_branch_id'], 'status' => 'ready', 'to' => $input['phoneNumber']);

		$update = PhoneLog::updateOrCreate($attr, $val);

        $publish_data = [
            'org_branch_id' => $data['org_branch_id'],
            'caller_user_id' => $data['user_id'],
            'black_list' => [(int) $input['user_id']],
        ];

        Redis::publish('update:crmAutoDialerBlacklist', json_encode($publish_data));

		return "success";
	}

    public function logIncomingCall() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $input = Request::all();

        $attr = array('sender_user_id' => $data['user_id'], 'receiver_user_id' => $input['user_id'], 
                      'org_branch_id' => $data['org_branch_id'], 'status' => 'ready', 'to' => $input['phoneNumber']);

        $val = array('sender_user_id' => $data['user_id'], 'receiver_user_id' => $input['user_id'], 
                      'org_branch_id' => $data['org_branch_id'], 'status' => 'ready', 'to' => $input['phoneNumber']);

        $update = PhoneLog::updateOrCreate($attr, $val);

        return 'success';
    }

	public function phoneTwiml(){
		$input = Request::all();

		// $caller = $input['Caller'];
		// $caller = str_replace("client:", "", $caller);
		// $this->sendemailalert('phoneTwiml' ,array('anthony.shayesteh@plexuss.com') , $input);
		// exit();
		// $this->customdd($input);
		// exit();
		
		$pl = PhoneLog::where('to', $input['phoneNumber'])
					  //->where('receiver_user_id', $caller)
		              ->where('status', 'ready')
		              ->orderBy('id','DESC')
		              ->first();

		// if ($pl->org_branch_id == 1) {
		// 	$fromPhone = $this->from;
		// } else {
        $pp = PurchasedPhone::on('rds1')->where('org_branch_id', $pl->org_branch_id)->where('user_id', '=', $pl->sender_user_id)->first();
        
        if (isset($pp)) {
            $fromPhone = '+1'.$pp->phone;
        } else {
            $pp = PurchasedPhone::on('rds1')->where('org_branch_id', $pl->org_branch_id)->first();
            if (isset($pp)) {
                $fromPhone = '+1'.$pp->phone;
            } else {
                $fromPhone = $this->from;
            }
        }
		// }

		$user = User::on('rds1')->find($pl->receiver_user_id);

		if (isset($user)) {
			$user_id = $user->id;
			$toPhone = $user->phone;
		}else{
			$user_id = -1;
			$toPhone = -2;
		}

		$data = $input;
		$data['fromPhone'] = $fromPhone;
		$data['toPhone']   = $toPhone;

		$data['currentUrlForTwilio'] = env('CURRENT_TWILIO_URL');

        try {
            $pl->from   = $this->lookup_client->phone_numbers->get($data['fromPhone'])->phone_number;

        } catch (\Exception $e) {
            $pl->from   = $data['fromPhone'];
        }

        try {
            $pl->to   = $this->lookup_client->phone_numbers->get($data['toPhone'])->phone_number;

        } catch (\Exception $e) {
            $pl->to     = $data['toPhone'];
        }

		$pl->status = $input['CallStatus'];
		$pl->sid    = $input['CallSid'];
		$pl->save();

		// $this->sendemailalert('phoneTwiml' ,array('anthony.shayesteh@plexuss.com') , $data);

		return View('twilio.twiml', $data);
	}

    public function incomingCall() {
        $input = Request::all();

        $phoneLog = new PhoneLog;

        $phoneLog->sid = $input['CallSid'];

        $phoneLog->from = $input['From'];
        
        $phoneLog->to = $input['To'];

        $phoneLog->direction = 'INCOMING';

        $phoneLog->status = $input['CallStatus'];

        // Get client name based on phone number. In our database, we do not store the country code.
        $phoneWithoutCountryCode = str_replace('+1', '', $input['To']);

        $pp = PurchasedPhone::select('twiml_client_name as client_name', 'org_branch_id', 'user_id')->where('phone', '=', $phoneWithoutCountryCode)->first();

        if (empty($pp)) {
            return 'Incoming calls not setup for this number';
        }

        if (isset($pp->user_id)) {
            $phoneLog->receiver_user_id = $pp->user_id;
        }

        $phoneLog->org_branch_id = $pp->org_branch_id;

        $data = [
            'client_name' => $pp->client_name,
            'fromPhone' => $input['From'],
        ];

        $data['currentUrlForTwilio'] = env('CURRENT_TWILIO_URL');

        $phoneLog->save();

        return View('twilio.incomingTwiml', $data);
    }

	public function recordCallBack(){
		$input = Request::all();

		$pl = PhoneLog::where('sid', $input['CallSid'])->first();

		if (isset($pl)) {
			$pl->recording_url 		= $input['RecordingUrl'];
			$pl->recording_duration = $input['RecordingDuration'];

			$pl->save();

			if (isset($input['RecordingDuration']) && $input['RecordingDuration'] >= 60) {
			
				$org_url = $input['RecordingUrl'];

				$url = substr($org_url, strrpos($org_url, '/') + 1);

				$org_url = str_replace($url, "", $org_url);

				$transcript = new Transcript;
				$transcript->transcript_name = $url;
				$transcript->transcript_path = $org_url;
				$transcript->school_type = 'highschool';
				$transcript->doc_type = 'prescreen_interview';
				$transcript->user_id = $pl->receiver_user_id;
				$transcript->save();
			}
		}
		// $this->sendemailalert('recordCallBack' ,array('anthony.shayesteh@plexuss.com') , $input);
	}

	public function callStatus(){
		$input = Request::all();

		$pl = PhoneLog::where('sid', $input['CallSid'])->first();

		if (isset($pl)) {
			$pl->status = $input['CallStatus'];
			$pl->save();
		}

		// $this->sendemailalert('callStatus' ,array('anthony.shayesteh@plexuss.com') , $input);
	}

	public function newToken(){
        // $forPage = Request::get('forPage');
        // $applicationSid = config('services.twilio')['applicationSid'];
        // $clientToken->allowClientOutgoing($applicationSid);

        // if ($forPage === route('dashboard', [], false)) {
        //     $clientToken->allowClientIncoming('support_agent');
        // } else {
        //     $clientToken->allowClientIncoming('customer');
        // }

        // $token = env('TWILIO_TOKEN');
        // return response()->json(['token' => $token]);


		// put your Twilio API credentials here
		$accountSid = env('TWILIO_SID');
		$authToken  =  env('TWILIO_TOKEN');
		// $appSid = 

		// dd($this->client);

		$capability = new ClientToken($accountSid, $authToken);
		$this->client->allowClientOutgoing($appSid);

		$token = $capability->generateToken();
    }



     /*****************************************
     * Process a new call
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     ***********************************************/
    public function newCall(Request $request)
    {
        $response = new Twiml();
        $callerIdNumber = config('services.twilio')['number'];

        $dial = $response->dial(['callerId' => $callerIdNumber]);

        $phoneNumberToDial = $request->input('phoneNumber');

        if (isset($phoneNumberToDial)) {
            $dial->number($phoneNumberToDial);
        } else {
            $dial->client('support_agent');
        }

        return $response;
    }

    public function answerACall(){
    	$input = Request::all();

    	$reply_email = 'support@plexuss.com';
        $template_name = 'test_template';
        // $email = 'ajay.a@mitlag.com';
        $email_arr = array( "anthony.shayesteh@plexuss.com", "jp.novin@plexuss.com", "sina.shayesteh@plexuss.com" );
        // $email_arr = array ( "anthony.shayesteh@plexuss.com" );
        // $params = array('fname' => 'Full', 'lname' => 'Name', 'email' => $email);
        // $params['data'] = json_encode($input);
        $user_info = DB::connection('rds1')->table('users as u')
        								   ->join('sms_log as sl', 'u.id', '=', 'sl.receiver_user_id')
        								   ->leftjoin('countries as c', 'c.id', '=', 'u.country_id')
        								   ->where('sl.smsBy', 'Plexuss')
        								   ->where('sl.status', 'delivered')
        								   ->where('sl.from', $input['To'])
        								   ->where('sl.to', $input['From'])
        								   ->select( 'u.id as user_id', 'u.fname', 'u.lname', 'u.email', 'u.phone', 
        								   			 'c.country_name')
        								   ->orderBy('sl.id', 'DESC')
        								   ->take(1)
        								   ->get()->toArray();

        
        $params['data'] = json_encode($user_info);

        if (!empty($user_info)) {
        	isset($user_info[0]->user_id) ? $input['user_id'] = $user_info[0]->user_id : $input['user_id'] = -1;
        	isset($user_info[0]->fname)   ? $params['fname']   = $user_info[0]->fname : NULL;
        	isset($user_info[0]->lname)   ? $params['lname']   = $user_info[0]->lname : NULL;
        	isset($user_info[0]->email)   ? $params['email']   = $user_info[0]->email : NULL;
        	isset($user_info[0]->phone)   ? $params['phone']   = $user_info[0]->phone : NULL;
        	isset($user_info[0]->country_name)   ? $params['country_name']   = $user_info[0]->country_name : NULL;
        }
        
        $input['phoneBy'] = "Student";
        $this->savePhoneLog($input);

        foreach ($email_arr as $key => $value) {
        	$mda = new MandrillAutomationController();
        	$mda->generalEmailSend($reply_email, $template_name, $params, $value);
        }
        
        $data['currentUrlForTwilio'] = env('CURRENT_TWILIO_URL');
        $data['phone'] = "+13102373494";

        return View( 'twilio.redirectPhone', $data);
    }

    private function savePhoneLog($input){

    	$phoneLog = new PhoneLog;

        $phoneLog->sid = $input['CallSid'];

        $phoneLog->from = $input['From'];
        
        $phoneLog->to = $input['To'];

        $phoneLog->direction = 'INCOMING';

        $phoneLog->status = $input['CallStatus'];

        $phoneLog->receiver_user_id = $input['user_id'];

        $phoneLog->phoneBy = $input['phoneBy'];

        $phoneLog->save();

        return "success";
    }

    public function redirectPhone(){

    	$input = Request::all();
    	$data  = array();

    	$data['currentUrlForTwilio'] = env('CURRENT_TWILIO_URL');
    	$data['phone']               = $input['phone'];
    	$data['fromPhone']			 = $input['fromPhone'];

    	return View( 'twilio.redirectPhone', $data);
    }

    public function modifyLiveCalls(){
    	$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

    	$input = Request::all();

    	$_this_client = $this->client;

    	// $pp = PurchasedPhone::on('rds1')->where('user_id', $data['user_id'])
    	// 								->where('org_branch_id', $data['org_branch_id'])
    	// 								->first();

    	// if (!isset($pp)) {
    	// 	$pp = PurchasedPhone::on('rds1')->where('org_branch_id', $data['org_branch_id'])
    	// 								    ->first();
    	// }

    	// if (!isset($pp)) {
    	// 	return "failed";
    	// }

    	// $input['fromPhone'] = $pp->phone;
    	
    	 // Code for version 5.X
	     // $_this_client->calls($input['sid'])
	     //           ->update(array(
	     //                        'method' => "GET",
	     //                        'url' => env("CURRENT_URL")."phone/redirectPhone?phone=".$input['phone']
	     //                    )
	     //           );

        $call = $_this_client->account->calls->get($input['sid']);
		$call->update(array(
					        "Url" => env("CURRENT_URL")."phone/redirectPhone?phone=".$input['phone'].'&fromPhone='.$input['user_phone'],
					    	"Method" => "GET"
					    ));

        return "success";
    }
}
