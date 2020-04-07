<?php

namespace App\Http\Controllers;

use Request, DB, Validator, Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\CollegeMessageThreadMembers;
use EmailReplyParser\Parser\EmailParser;
use Ddeboer\Imap\Server;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\To;
use Ddeboer\Imap\Search\Text\Body;
use App\InternalCollegeContactPlexussEmail, App\InternalCollegeContactInfo, App\SparkpostModel;
use App\User, App\College, App\CollegeMessageLog;
use App\Http\Controllers\MandrillAutomationController;

class EmailParserController extends Controller
{

	private $server = '';

	/**
	 * Setting up gmail server
	*/
	public function __construct(){

		$hostname = 'imap.gmail.com';
		$port = 993;
		$flags = '/imap/ssl/novalidate-cert';
		$parameters = array();

		$this->server = new Server(
		    $hostname, // required
		    $port,     // defaults to 993
		    $flags,    // defaults to '/imap/ssl/validate-cert'
		    $parameters
		);
	}

	/**
	 * This method flags someone as unsububscribe in internal_college_contact_info, so we don't send another email to them
	*/
	public function unsubInternalEmailColleges(){

		$qry = InternalCollegeContactPlexussEmail::on('rds1')->get();

		$sm = new SparkpostModel('test_template');

		foreach ($qry as $key) {
			$username = $key->email;
			$password = env('UNIVERSAL_EMAIL_PASS'); 

			$connection = $this->server->authenticate($username, $password);

			// dd($connection);
			$mailbox = $connection->getMailbox('INBOX');

			$unsub    	  = $connection->getMailbox('Unsubscribe');
			$unsub_parsed = $connection->getMailbox('Unsubscribe-parsed');
			$messages = $unsub->getMessages();

			foreach ($messages as $message) {
				$to_address = null;

				$to = $message->getTo();

				$to = (array) $to[0];
				$to_address = '';
				
				foreach ($to as $k) {
					$to_address = $k;
				}

				if (isset($to_address) && $key->email != $to_address) {
					$icci = InternalCollegeContactInfo::where('email', $to_address)->first();

					if (isset($icci)) {
						$icci->unsub = 1;
						$icci->save();
					}else{
						$icci = new InternalCollegeContactInfo;
						$icci->fname       = "name not found";
						$icci->email       = $to_address;
						$icci->template_id = 1;
						$icci->unsub       = 1;

						$icci->save();
					}

					$message->move($unsub_parsed);
					$message->delete();

					// Add to sparkpost supression list
					$sm->isSupressed($to_address);
				}

				$from = $message->getFrom();

				$from = (array) $from;
				$from_address = '';
				

				foreach ($from as $k) {
					$from_address = $k;
				}

				if (isset($from_address) && $key->email != $from_address) {
					$icci = InternalCollegeContactInfo::where('email', $from_address)->first();

					if (isset($icci)) {
						$icci->unsub = 1;
						$icci->save();
					}else{
						$icci = new InternalCollegeContactInfo;
						$icci->fname       = "name not found";
						$icci->email       = $from_address;
						$icci->template_id = 1;
						$icci->unsub       = 1;

						$icci->save();
					}

					$message->move($unsub_parsed);
					$message->delete();

					// Add to sparkpost supression list
					$sm->isSupressed($from_address);
				}
			}
		}
	}

	/**
	 * Parse user's response on a college message email
	 * @return null
	*/
	public function parseUserResponds(){

		$username = 'support@plexuss.com';
		$password = env('EMAIL_PASS'); 
		$searchStr = 'To respond from your Portal on Plexuss.com';

		$this->parseEmailMessage($username, $password, $searchStr);

	}

	/**
	 * Parse college's response on a user message email
	 * @return null
	*/
	public function parseCollegeResponds(){

		$username = 'collegeservices@plexuss.com';
		$password = env('EMAIL_PASS'); 
		$searchStr = 'Inquiry from a prospective student';

		$this->parseEmailMessage($username, $password, $searchStr);
	}

	/**
	 * Parse user's response on recurrying email of "USERS: Rec in-network college- score comparison"
	 * and add it to "recruitment" table as a new inquiry from the user.
	 * @return null
	*/
	public function parseIsItAGoodFitForYou(){

		$arr = array();

		$arr['username']      = 'support@plexuss.com';
		$arr['password']      = env('EMAIL_PASS');
		$arr['searchStr']     = 'Is it a good fit? Why not get to know them better?';
		$arr['subject_str1']  = 'Is ';
		$arr['subject_str2']  = ' a good fit for you?';
		$arr['html_str1']     = 'Get recruited by the ';
		$arr['html_str2']     = '. Compare your stats with the school';

		$this->parseUsersEverydayEmail($arr);
	}
	/**
	 * Parse user's response on recurrying email of "USERS: Rec in-network college- score comparison"
	 * and add it to "recruitment" table as a new inquiry from the user.
	 * @return null
	*/

	public function parseCollegeWantsToRecruitYou(){

		$arr = array();

		$arr['username']      = 'support@plexuss.com';
		$arr['password']      = env('EMAIL_PASS');
		$arr['searchStr']     = 'Important stats about this college';
		$arr['subject_str1']  = 'Re: ';
		$arr['subject_str2']  = ' Wants to Recruit You';
		$arr['html_str1']     = 'if you want to connect with ';
		$arr['html_str2']     = '. Reply directly';

		$this->parseUsersEverydayEmail($arr);
	}

	/**
	 * Parse user's response on recurrying email of "USERS: college ranking update -school on list"
	 * and add it to "recruitment" table as a new inquiry from the user.
	 * @return null
	*/
	public function parseCollegeRankingUpdateForUsers(){

		$arr = array();

		$arr['username']      = 'support@plexuss.com';
		$arr['password']      = env('EMAIL_PASS');
		$arr['searchStr']     = 'has a new ranking update!';
		$arr['subject_str1']  = 'Re: Check out ';
		$arr['subject_str2']  = ' ';
		$arr['html_str1']     = '<span style="font-size:26px">';
		$arr['html_str2']     = ' has a new ranking update!';

		$this->parseUsersEverydayEmail($arr);
	}

	/**
	 * Parse user's response on recurrying email of "USERS: In-network school you liked"
	 * and add it to "recruitment" table as a new inquiry from the user.
	 * @return null
	*/
	public function parseSchoolsYouLiked(){

		$arr = array();

		$arr['username']      = 'support@plexuss.com';
		$arr['password']      = env('EMAIL_PASS');
		$arr['searchStr']     = 'We are recommending the college below because you liked';
		$arr['subject_str1']  = 'Re: Because You Showed Interest In ';
		$arr['subject_str2']  = ' ';
		$arr['html_str1']     = 'By clicking on Get Recruited, you will be able to send a direct message to the college admission officer at&nbsp;</span>';
		$arr['html_str2']     = '<br>';

		$this->parseUsersEverydayEmail($arr);
	}

	/**
	 * Parse user's response on recurrying email of "USERS: In-network school you liked"
	 * and add it to "recruitment" table as a new inquiry from the user.
	 * @return null
	*/
	public function parseSchoolsNearYou(){

		$arr = array();

		$arr['username']      = 'support@plexuss.com';
		$arr['password']      = env('EMAIL_PASS');
		$arr['searchStr']     = 'Want to stay close to home? Here is terrific university to consider in your home state';
		$arr['subject_str1']  = 'Want to Stay Close to Home While in College?';
		$arr['subject_str2']  = ' ';
		$arr['html_str1']     = 'class="this_college_name"><b>';
		$arr['html_str2']     = '</b></a></h4><span>';

		$this->parseUsersEverydayEmail($arr);
	}

	public function deleteSinaLabel(){

		// $username     = 'support@plexuss.com';
		// $password       = env('EMAIL_PASS');

		$connection = $this->server->authenticate($username, $password);
		//$connection = $this->server->authenticate('anthony.shayesteh@gmail.com', 'plexuss1234');
		$mailboxes = $connection->getMailboxes();
		// dd($mailboxes);
		foreach ($mailboxes as $key) {
			$name = '';
			if (strpos($key->getName(), '&') !== FALSE || strpos($key->getName(), 'Gmail') !== FALSE) {
				$name = str_replace("&", "&amp;", $key->getName());
			}
			// else{
				if ($key->getName() != "MJ's Beauty Academy" && $key->getName() != "MOACAC" && $key->getName() != "Trash"
				&& $key->getName() != "Tagus Let's Go Study - Tashkent" && $key->getName() != "INBOX" 
				&& $key->getName() != "UK Education Institute " && $key->getName() != "[Gmail]") {
					try {
						$connection->deleteMailbox($connection->getMailbox($name ));
					} catch (Exception $e) {
						print_r("error happened");
						$this->customdd($e);
						$this->customdd("===========<br/>");
						continue;
					}
					
					// dd('done');
				}
			// }
			// dd($key->getName());
			// $tmp = array($key);
			// dd($tmp);
		}

		dd('done');
		dd($mailboxes);	

	}
	/**
	 * Parse email to find thread_id , the message, and user who has sent the message
	 * @param $username  : email user name
	 * @param $password  : email password
	 * @param $searchStr : the string that we want to search the email body for.
	 * @return null
	*/
	private function parseEmailMessage($username, $password, $searchStr){

		$connection = $this->server->authenticate($username, $password);
		//$connection = $this->server->authenticate('anthony.shayesteh@gmail.com', 'plexuss1234');
		$mailbox = $connection->getMailbox('INBOX');
	
		$search = new SearchExpression();
		$search->addCondition(new Body($searchStr));

		$messages = $mailbox->getMessages($search);

		$move_mailbox_success = $connection->getMailbox('Plexuss-Auto-Captured');
		$move_mailbox_failed  = $connection->getMailbox('Plexuss-Auto-Captured-Failed');

		foreach ($messages as $message) {
		    // $message is instance of \Ddeboer\Imap\Message
			$msg_arr = array();

			$body = $message->getBodyText();
			$from = $message->getFrom();
			$html = $message->getBodyHtml();

			$from = (array) $from;
			$from_address = '';
			foreach ($from as $k) {
				$from_address = $k;
			}

			if (isset($html)) {
				$tdid = $this->getStringBetween($html, 'tdidbg:', ':tdidend');
			}else{
				$tdid = $this->getStringBetween($body, 'tdidbg:', ':tdidend');
			}
			
			$tdid = str_replace("<wbr>", "", $tdid);
			
			if (!isset($tdid) || empty($tdid)) {
				$message->move($move_mailbox_failed);
				continue;
			}

			$tdid = Crypt::decrypt($tdid);
			
			$email = (new EmailParser())->parse($body);

			$fragment = current($email->getFragments());

			$replied_msg = $fragment->getContent();

			// Check to see if I have content from the original email
			// if so remove them from the replied message
			if ($this->getStringBetween($replied_msg, 'On Monday, ', 'Plexuss <collegeservices@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Tuesday, ', 'Plexuss <collegeservices@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Wednesday, ', 'Plexuss <collegeservices@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Thursday, ', 'Plexuss <collegeservices@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Friday, ', 'Plexuss <collegeservices@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Saturday, ', 'Plexuss <collegeservices@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Sunday, ', 'Plexuss <collegeservices@plexuss.com> wrote:') != '' ||
				$this->getStringBetween($replied_msg, 'On Monday, ', 'Plexuss <support@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Tuesday, ', 'Plexuss <support@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Wednesday, ', 'Plexuss <support@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Thursday, ', 'Plexuss <support@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Friday, ', 'Plexuss <support@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Saturday, ', 'Plexuss <support@plexuss.com> wrote:') != '' || 
				$this->getStringBetween($replied_msg, 'On Sunday, ', 'Plexuss <support@plexuss.com> wrote:') != '' ) {
				
				$pos = '';

				if ($pos == '') {
					$pos = substr($replied_msg , 0, strrpos($replied_msg, 'On Monday, '));
				}
				if ($pos == '') {
					$pos = substr($replied_msg , 0, strrpos($replied_msg, 'On Tuesday, '));
				}
				if ($pos == '') {
					$pos = substr($replied_msg , 0, strrpos($replied_msg, 'On Wednesday, '));
				}
				if ($pos == '') {
					$pos = substr($replied_msg , 0, strrpos($replied_msg, 'On Thursday, '));
				}
				if ($pos == '') {
					$pos = substr($replied_msg , 0, strrpos($replied_msg, 'On Friday, '));
				}
				if ($pos == '') {
					$pos = substr($replied_msg , 0, strrpos($replied_msg, 'On Saturday, '));
				}
				if ($pos == '') {
					$pos = substr($replied_msg , 0, strrpos($replied_msg, 'On Sunday, '));
				}
				
				$replied_msg = $pos;
			}


			$user = User::where('email', $from_address)->first();

			if (!isset($replied_msg) || !isset($user)) {
				$message->move($move_mailbox_failed);
				continue;
			}

			$msg_arr['user_id'] = $user->id;
		
			
			$msg_arr['replied_msg'] = trim($replied_msg);
			$msg_arr['thread_id'] = $tdid;
			
			$this->postMessageThroughEmail($msg_arr, $move_mailbox_success, $move_mailbox_failed);
			$message->move($move_mailbox_success);
		}
	}

	/**
	 * Post the message that has been parsed to our database.
	 * @param $msg_arr  : array of user_id, thread_id, and the message
	 * @param $move_mailbox_success: Success mailbox
	 * @param $move_mailbox_failed : Failed mailbox
	 * @return null
	*/
	private function postMessageThroughEmail($msg_arr, $move_mailbox_success, $move_mailbox_failed){

		$my_user_id                = $msg_arr['user_id'];
		$college_message_thread_id = $msg_arr['thread_id'];
		$message                   = $msg_arr['replied_msg'];

		$cml = new CollegeMessageLog;

		$cml->user_id = $my_user_id;
		$cml->thread_id = $college_message_thread_id;
		$cml->msg = $message;
		$cml->is_read = 0;
		$cml->attachment_url = "";
		$cml->is_deleted = 0;
		$cml->save();
		
		$ids = DB::connection('rds1')
			->table('college_message_thread_members as cmtm')
			->where('user_id', $my_user_id)
			->where('thread_id', $college_message_thread_id)
			->pluck('id');

		// increment number of sent messages
		if(!empty($ids)){
			DB::table('college_message_thread_members')
				->whereIn('id', $ids)
				->increment('num_of_sent_msg');
		}

		//Reset all the cache values of each member of each thread, and increment the num of unread msgs
		$thread_members = CollegeMessageThreadMembers::where('user_id', '!=', $my_user_id)
			->where('thread_id', '=', $college_message_thread_id)
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


	/**
	 * Parse the emails that users receive everyday and add it as an inquiry 
	 * @param $arr  : array of all info for this particular email
	 * @return null
	*/
	private function parseUsersEverydayEmail($arr){
	
		$username = $arr['username'];
		$password = $arr['password'];

		$searchStr = $arr['searchStr'];

		$connection = $this->server->authenticate($username, $password);
		//$connection = $this->server->authenticate('anthony.shayesteh@gmail.com', 'plexuss1234');
		$mailbox = $connection->getMailbox('INBOX');

		$search = new SearchExpression();
		$search->addCondition(new Body($searchStr));
		$messages = $mailbox->getMessages($search);

		$move_mailbox_success = $connection->getMailbox('Plexuss-Auto-Captured');
		$move_mailbox_failed  = $connection->getMailbox('Plexuss-Auto-Captured-Failed');

		foreach ($messages as $message) {
		    // $message is instance of \Ddeboer\Imap\Message
			$msg_arr = array();
			$body = $message->getBodyText();
			$from = $message->getFrom();
			$html = $message->getBodyHtml();

			$subject = $message->getSubject();

			$tdid = '';
			if (isset($html)) {
				$tdid = $this->getStringBetween($html, 'tdidbg:', ':tdidend');
			}else{
				$tdid = $this->getStringBetween($body, 'tdidbg:', ':tdidend');
			}
			
			if ($tdid == '*|collegeid|*' || $tdid == '*|COLLEGEID|*') {
				$tdid = '';
			}
			if (!isset($tdid) || empty($tdid)) {
				
				if (!isset($html)) {
					$html = isset($subject) ? $subject : $body;
					$school_name = $this->getStringBetween($html, $arr['subject_str1'], $arr['subject_str2']);
				}else{
					$school_name = $this->getStringBetween($html, $arr['html_str1'], $arr['html_str2']);
				}

				$college = College::where('school_name', trim($school_name))
								  ->where('verified', 1)
							      ->first();
			}else{
				try{
					$tdid = Crypt::decrypt($tdid);
				} catch (\Exception $e){
					continue;
				}
				$college = College::where('id', $tdid)
								  ->where('verified', 1)
							      ->first();
			}			

			$from = (array) $from;
			$from_address = '';
			foreach ($from as $k) {
				$from_address = $k;
			}

			$user = User::where('email', $from_address)->first();

			$check = false;

			$email = (new EmailParser())->parse($body);

			$fragment = current($email->getFragments());

			$replied_msg = $fragment->getContent();

			$replied_msg = strtolower(trim($replied_msg));

			//dd($replied_msg);
			if (isset($replied_msg)) {	
				if (strpos($replied_msg,'yes') !== false || strpos($replied_msg,'recruit') !== false ||
				 	strpos($replied_msg,'admit') !== false || strpos($replied_msg,'I want to study') !== false || strpos($replied_msg,'si') !== false || strpos($replied_msg,'agree') !== false || 
				 	(strpos($replied_msg,'interest') !== false && strpos($replied_msg,'not interest') !== true) || strpos($replied_msg,'I want to') !== false || strpos($replied_msg,'attend') !== false || strpos($replied_msg,'I would like to') !== false ||
				 	strpos($replied_msg,'admission') !== false || strpos($replied_msg,'accept') !== false ||
				 	strpos($replied_msg,'yeah') !== false || strpos($replied_msg,'yup') !== false ||
				 	strpos($replied_msg,'yap') !== false || strpos($replied_msg,'yah') !== false ) {
				    $check = true;
				}
			}

			if (!isset($college) || !isset($user) || !$check) {
				$message->move($move_mailbox_failed);
				continue;
			}

			if(!isset($user->phone) || !isset($user->address) || !isset($user->city)){
				$message->move($move_mailbox_success);
				$data = array();
				$data['school_name'] = $college->school_name;
				$data['college_id'] = $college->id;
				$data['fname'] = $user->fname;
				$data['email'] = $user->email;
				$mac = new MandrillAutomationController;
				$mac->collegeNeedsMoreInfo($data);
				continue;
			}
			$data = array();
			$data['school_name'] = $college->school_name;
			$data['college_id'] = $college->id;
			$data['fname'] = $user->fname;
			$data['email'] = $user->email;
			$data['lname'] = $user->lname;
			$data['user_id'] = $user->id;
			$this->setRecruitModular($data, 'inquiry_from_email');

			$message->move($move_mailbox_success);
		}
	}

	public function parseIsItAGoodFitForYouFixUPS(){

		$username = 'collegeservices@plexuss.com';
		$password = env('EMAIL_PASS');

		$table = DB::table('recruitment as r')
					->join('colleges as c', 'c.id', '=', 'r.college_id')
					->join('users as u', 'u.id', '=', 'r.user_id')
					->where('type', "inquiry_from_email")
					->where('c.id', 7900)
					->select('u.email', 'r.id as rec_id')
					->orderBy('r.user_id', 'DESC')
					->first();

		//$searchStr = 'Is it a good fit? Why not get to know them better?';
		$searchStr = $table->email;

		$connection = $this->server->authenticate($username, $password);
		//$connection = $this->server->authenticate('anthony.shayesteh@gmail.com', 'plexuss1234');
		$mailbox = $connection->getMailbox('Plexuss-Auto-Captured');
		//$mailbox = $connection->getMailbox('INBOX');

		$search = new SearchExpression();
		$search->addCondition(new Body($searchStr));

		$messages = $mailbox->getMessages($search);

		$move_mailbox_success = $connection->getMailbox('Plexuss-Auto-Captured');
		$move_mailbox_failed  = $connection->getMailbox('Plexuss-Auto-Captured-Failed');

		foreach ($messages as $message) {
		    // $message is instance of \Ddeboer\Imap\Message
			$msg_arr = array();

			$body = strtolower($message->getBodyText());
			$from = $message->getFrom();
			$html = $message->getBodyHtml();
			$subject = $message->getSubject();

			if (!isset($html)) {
				$html = isset($subject) ? $subject : $body;
				$school_name = $this->getStringBetween($html, 'Is ', ' a good fit for you?');
			}else{
				$school_name = $this->getStringBetween($html, 'Get recruited by the ', '. Compare your stats with the school');
			}
			//dd($school_name);
			$college = College::where('school_name', trim($school_name))
							->where('verified', 1)
							->first();

			$rec = Recruitment::find($table->rec_id);
			$rec->college_id = $college->id;
			$rec->save();

			$from = (array) $from;
			$from_address = '';
			foreach ($from as $k) {
				$from_address = $k;
			}

			$user = User::where('email', $from_address)->first();

			$check = false;

			$email = (new EmailParser())->parse($body);

			$fragment = current($email->getFragments());

			$replied_msg = $fragment->getContent();

			$replied_msg = trim($replied_msg);

			//dd($replied_msg);
			if (isset($replied_msg)) {	
				if (strpos($replied_msg,'yes') !== false || strpos($replied_msg,'recruit') !== false ||
				 	strpos($replied_msg,'admit') !== false || strpos($replied_msg,'I want to study') !== false ||
				 	(strpos($replied_msg,'interest') !== false && strpos($replied_msg,'not interest') !== true) || 
				 	strpos($replied_msg,'admission') !== false || strpos($replied_msg,'accept') !== false ) {
				    $check = true;
				}
			}

			if (!isset($college) || !isset($user) || !$check) {
				try{
					$message->move($move_mailbox_failed);
				}
				catch (\Exception $e){
					continue;
				}
				continue;
			}

			$attr = array('user_id' => $user->id, 'college_id' => $college->id );

			$val = array('user_id' => $user->id, 'college_id' => $college->id,
						 'user_recruit' => 1, 'college_recruit' => 0, 'status' => 1, 'type' => 'inquiry_from_email');
				
			$tmp = Recruitment::updateOrCreate($attr, $val);

			$ntn = new NotificationController();
			$ntn->create(  ucfirst($user->fname). ' '. ucfirst($user->lname), 'college', 1, null, $user->id, $college->id );

			$message->move($move_mailbox_success);

		}
	}

}
