<?php

namespace App\Http\Controllers;

use Request, DB, Validator, Session, DateTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\User, App\UsersSalesControl, App\Organization, App\OrganizationBranch, App\CollegeRecommendation, App\CollegeMessageLog, App\CollegeMessageThreads, App\CollegeRecommendationFilters;
use App\CollegeMessageThreadMembers, App\Recruitment, App\NotificationTopNav, App\LikesTally, App\RankingList, App\Priority, App\TrackingPage;
use Carbon\Carbon;
use App\Http\Controllers\AgencyController;
use App\Agency;
use App\ZipCodes, App\Country, App\Department, App\Degree, App\Ethnicity, App\Religion, App\MilitaryAffiliation, App\DistributionClient, App\DistributionResponse;

use App\AdClick, App\Post;
use App\PixelTrackedTesting, App\UserTrackingNumber;

use App\Http\Controllers\SocialController, App\Http\Controllers\AjaxController;

class SalesController extends Controller
{

	public function index(){

		return redirect( 'sales/tracking' );
		// //Get user logged in info and ajaxtoken.
		// $user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss Sales Page';
		$data['currentPage'] = 'sales';
		//$data['ajaxtoken'] = $token['token'];

		$org = Organization::all();

		$data['num_of_clients'] = count($org) -1;

		return View('sales.dashboard', $data);
	}

	public function getPickACollege(){
		$data = array();

		$data['title'] = 'Plexuss | Pick A College';
		$data['currentPage'] = 'sales-pick-a-college';

		return View('sales.pickACollege', $data);
	}

	public function getApplicationOrder(){
		$data = array();

		$data['title'] = 'Plexuss | Application Order';
		$data['currentPage'] = 'sales-application-order';

		return View('sales.pickACollege', $data);
	}

	public function getAgencyReporting(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Agency Reporting';
		$data['currentPage'] = 'sales-agency-reporting';

		return View('sales.agencyReporting', $data);
	}

    public function getPixelTrackingIndex() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['title'] = 'Plexuss Pixel Tracking Test';
        $data['currentPage'] = 'sales-pixel-tracking-test';

        $data['ip'] = $this->iplookup();

        return View('sales.pixelTrackingTest', $data);
    }

	public function getClientReporting(){

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Client Report Page';
		$data['currentPage'] = 'sales-clientReporting';

		if(!Cache::has(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate')){

			print_r('The cron has not ran yet :( <br>');
			exit();
		}

		$ret = Cache::get(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate');
	    //$ret = $this->generateSalesClientReportingData();
		$data['clients_arr'] = $ret;

		return View('sales.clientReporting', $data);
	}

	public function getSalesScholarships(){
		$data = array();

		$data['title'] = 'Plexuss | Scholarships';
		$data['currentPage'] = 'sales-scholarships';

		return View('sales.salesScholarships', $data);
	}

	public function generateSalesClientReportingData($takeSkip = null){

		$org = new Organization;

		$admins_info = $org->getOrgsAdminInfo(NULL, NULL, NULL, $takeSkip);

		$tp = new TrackingPage;

		$cml = new CollegeMessageLog;

		$rec = new Recruitment;

		$ntn = new NotificationTopNav;

		$lt = new LikesTally;

		$cr = new CollegeRecommendation;

		$lst = new RankingList;

		$ret = array();

		$name_arr = array();
		$org_name_arr = array();
		$org_branch_id_arr = array();
		$user_id_arr = array();
		$school_name_arr = array();
		$date_joined_arr = array();
		$college_id_arr = array();
		$customer_type_arr = array();
		$export_file_cnt_arr = array();

		foreach ($admins_info as $key) {
			$name_arr[] = ucfirst($key->fname). ' '. ucfirst($key->lname);
			$org_name_arr[] = ucfirst($key->org_name);
			$org_branch_id_arr[] = $key->org_branch_id;
			$user_id_arr[] = $key->user_id;
			$school_name_arr[] = $key->school_name;
			$date_joined_arr[] = $key->date_joined;
			$college_id_arr[] = $key->college_id;

			if ($key->bachelor_plan == 1) {
				$customer_type_arr[] = 'Premier';
			}else{
				$customer_type_arr[] = 'Free';
			}
			$export_file_cnt_arr[] = $key->export_file_cnt;
		}

		$num_of_inquiries_arr = $rec->getNumOfInquiryForColleges($college_id_arr, 'inquiry');
		$num_of_inquiries_rejected_arr = $rec->getNumOfInquiryRejectedForColleges($college_id_arr, 'inquiry');
		$num_of_inquiries_accepted_arr = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'inquiry');
		$num_of_inquiries_idle_arr = $rec->getNumOfInquiryIdleForColleges($college_id_arr, 'inquiry');
		$num_of_enrolled = $rec->getNumOfEnrolled($college_id_arr);
		$num_of_applied  = $rec->getNumOfApplied($college_id_arr);

		$num_of_recommendations_arr = $cr->getTotalNumOfRecommendationsForColleges($college_id_arr);
		$num_of_recommendations_accepted_pending_arr = $cr->getNumOfRecommendationsAcceptedPendingForColleges($college_id_arr);
		$num_of_recommendations_rejected_arr = $cr->getNumOfRecommendationsRejectedForColleges($college_id_arr);
		$num_of_recommendations_idle_arr = $cr->getNumOfRecommendationsIdleForColleges($college_id_arr);

		$num_of_advance_search_arr = $rec->getNumOfInquiryForColleges($college_id_arr, 'advance_search');
		//$num_of_advance_search_rejected_arr = $rec->getNumOfInquiryRejectedForColleges($college_id_arr, 'advance_search');
		$num_of_advance_search_accepted_arr = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'advance_search');
		//$num_of_advance_search_idle_arr = $rec->getNumOfInquiryIdleForColleges($college_id_arr, 'advance_search');

		$num_of_recommendation_approved_arr = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'recommendation');

		$num_of_auto_approve_recommendation_arr = $rec->getNumOfInquiryForColleges($college_id_arr, 'auto_approve_recommendation');
		$num_of_auto_approve_recommendation_approved_arr = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'auto_approve_recommendation');

		$total_num_of_pending_from_all_sources_arr = $rec->getNumOfTotalPendingFromAllSourcesForColleges($college_id_arr);
		$total_num_of_approved_by_pending_arr = $rec->getNumOfTotalPendingApprovedForColleges($college_id_arr);

		//$num_of_pending_arr = $rec->getNumOfPendingForColleges($college_id_arr);
		$num_of_approved_arr = $rec->getNumOfApprovedForColleges($college_id_arr);
		//$num_of_rejected_arr = $rec->getNumOfRejectedUsersForColleges($college_id_arr);
		$num_profile_view_arr = $ntn->getNumProfileViews($user_id_arr);
		$num_of_likes_arr = $lt->getLikesTallyMultipleCollegeIds($college_id_arr);
		//$num_user_activity_arr = $tp->getUserActivity($user_id_arr);
		//$num_of_idle_msgs_arr = $cml->getNumOfIdleMsgs($org_branch_id_arr);

		$num_of_uploaded_ranking_arr = $lst->getNumOfUploadedRankingForColleges($college_id_arr);

		$num_of_daily_chat_arr = $cml->getNumOfDailyChat($org_branch_id_arr);
		$num_of_days_chatted_arr = $cml->getNumOfDaysChatted($org_branch_id_arr);

		$overall_num_sent_chat_messages = $cml->getNumOfMsgSentOfChatAndMessages($user_id_arr);

		$total_chat_received_arr = array();
		$total_chat_sent_arr = array();
		$total_msg_sent_arr = array();
		$total_msg_received_arr = array();

		$total_user_chat_sent_arr = array();
		$total_user_msg_sent_arr = array();

		if (isset($overall_num_sent_chat_messages)) {
			foreach ($overall_num_sent_chat_messages as $key) {

				// chat sent
				if ($key->is_organization == 1 && $key->is_chat == 1) {
					$total_user_chat_sent_arr[$key->org_branch_id][$key->user_id] = $key->cnt;
				}

				// msg sent
				if ($key->is_organization == 1 && $key->is_chat == 0) {
					$total_user_msg_sent_arr[$key->org_branch_id][$key->user_id] = $key->cnt;
				}
			}
		}

		$overall_num_idls_msgs = $cml->getNumOfIdleMsgs($user_id_arr);

		//Activity Score Formula set - lllllll
		$ta1 = array();
		$ta14 = array();
		$ta30 = array();
		$now = Carbon::now();
		$today = $now->today();
		$yesterday = $now->yesterday();
		$twoWeeksAgo = $now->subDays(7);
		$thirtyDaysAgo = $today->subDays(30);

		//getting data from yesterday ---------------------
		$iRejectNum1 = $rec->getNumOfInquiryRejectedForColleges($college_id_arr, 'inquiry', $yesterday);//inquiries
		$iAcceptNum1 = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'inquiry', $yesterday);//inquiries
		$iIdleNum1 = $rec->getNumOfInquiryIdleForColleges($college_id_arr, 'inquiry', $yesterday);//inquiries
		$rRejectNum1 = $cr->getNumOfRecommendationsRejectedForColleges($college_id_arr, $yesterday);//recommendations
		$rAcceptNum1 = $cr->getNumOfRecommendationsAcceptedPendingForColleges($college_id_arr, $yesterday);//recommendations
		$rIdleNum1 = $cr->getNumOfRecommendationsIdleForColleges($college_id_arr, $yesterday);//recommendations
		$chatted1 = $cml->getNumOfDaysChatted($org_branch_id_arr, $yesterday);//num of days chatted
		$acceptedFromAdvSearch1 = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'advance_search', $yesterday);//num of accepted from advanced search

		$chatMsgOverall1 = $cml->getNumOfMsgSentOfChatAndMessages($user_id_arr, $yesterday);//number of chat and messages sent
		$tempOverall1 = $this->filterChatAndMsg($chatMsgOverall1);//separate chat and msg
		$chatSentArr1 = $tempOverall1['chat'];//save chat in own array
		$msgSentArr1 = $tempOverall1['msg'];//save msg in own array

		$profViews1 = $ntn->getNumProfileViews($user_id_arr, $yesterday);//num of profile views
		$upRankingList1 = $lst->getNumOfUploadedRankingForColleges($college_id_arr, $yesterday);//num of uploaded ranking lists

		$export1 = $org->getOrgsAdminInfo($yesterday);//get admin info
		$exportArr1 = $this->getExportArr($export1);//filter admin info to return only export cnt

		//getting data from 14 days ago from today ---------------------
		$iTotal14 = $rec->getNumOfInquiryForColleges($college_id_arr, 'inquiry', $twoWeeksAgo);//inquiries
		$iRejectNum14 = $rec->getNumOfInquiryRejectedForColleges($college_id_arr, 'inquiry', $twoWeeksAgo);//inquiries
		$iAcceptNum14 = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'inquiry', $twoWeeksAgo);//inquiries
		$iIdleNum14 = $rec->getNumOfInquiryIdleForColleges($college_id_arr, 'inquiry', $twoWeeksAgo);//inquiries
		$rRejectNum14 = $cr->getNumOfRecommendationsRejectedForColleges($college_id_arr, $twoWeeksAgo);//recommendations
		$rAcceptNum14 = $cr->getNumOfRecommendationsAcceptedPendingForColleges($college_id_arr, $twoWeeksAgo);//recommendations
		$rIdleNum14 = $cr->getNumOfRecommendationsIdleForColleges($college_id_arr, $twoWeeksAgo);//recommendations
		$chatted14 = $cml->getNumOfDaysChatted($org_branch_id_arr, $twoWeeksAgo);//num of days chatted
		$acceptedFromAdvSearch14 = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'advance_search', $twoWeeksAgo);//num of accepted from advanced search

		$chatMsgOverall14 = $cml->getNumOfMsgSentOfChatAndMessages($user_id_arr, $twoWeeksAgo);//number of chat and messages sent
		$chatMsgOverall1 = $cml->getNumOfMsgSentOfChatAndMessages($user_id_arr, $twoWeeksAgo);//number of chat and messages sent
		$tempOverall14 = $this->filterChatAndMsg($chatMsgOverall14);//separate chat and msg
		$chatSentArr14 = $tempOverall14['chat'];//save chat in own array
		$msgSentArr14 = $tempOverall14['msg'];//save msg in own array

		$profViews14 = $ntn->getNumProfileViews($user_id_arr, $twoWeeksAgo);//num of profile views
		$upRankingList14 = $lst->getNumOfUploadedRankingForColleges($college_id_arr, $twoWeeksAgo);//num of uploaded ranking lists

		$export14 = $org->getOrgsAdminInfo($twoWeeksAgo);//get admin info then filter out only export data
		$exportArr14 = $this->getExportArr($export14);	//filter out only export data

		$num_of_approved_arr14 = $rec->getNumOfApprovedForColleges($college_id_arr, $twoWeeksAgo);//total number of approved

		//getting data from 30 days ago from today ------------------------
		$iTotal30 = $rec->getNumOfInquiryForColleges($college_id_arr, 'inquiry', $thirtyDaysAgo);//inquiries
		$iRejectNum30 = $rec->getNumOfInquiryRejectedForColleges($college_id_arr, 'inquiry', $thirtyDaysAgo);//inquiries
		$iAcceptNum30 = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'inquiry', $thirtyDaysAgo);//inquiries
		$iIdleNum30 = $rec->getNumOfInquiryIdleForColleges($college_id_arr, 'inquiry', $thirtyDaysAgo);//inquiries
		$rRejectNum30 = $cr->getNumOfRecommendationsRejectedForColleges($college_id_arr, $thirtyDaysAgo);//recommendations
		$rAcceptNum30 = $cr->getNumOfRecommendationsAcceptedPendingForColleges($college_id_arr, $thirtyDaysAgo);//recommendations
		$rIdleNum30 = $cr->getNumOfRecommendationsIdleForColleges($college_id_arr, $thirtyDaysAgo);//recommendations
		$chatted30 = $cml->getNumOfDaysChatted($org_branch_id_arr, $thirtyDaysAgo);//num of days chatted
		$acceptedFromAdvSearch30 = $rec->getNumOfInquiryAcceptedForColleges($college_id_arr, 'advance_search', $thirtyDaysAgo);//num of accepted from advanced search

		$chatMsgOverall30 = $cml->getNumOfMsgSentOfChatAndMessages($user_id_arr, $thirtyDaysAgo);//number of chat and messages sent
		$tempOverall30 = $this->filterChatAndMsg($chatMsgOverall30);//separate chat and msg
		$chatSentArr30 = $tempOverall30['chat'];//save chat in own array
		$msgSentArr30 = $tempOverall30['msg'];//save msg in own array

		$profViews30 = $ntn->getNumProfileViews($user_id_arr, $thirtyDaysAgo);//num of profile views
		$upRankingList30 = $lst->getNumOfUploadedRankingForColleges($college_id_arr, $thirtyDaysAgo);//num of uploaded ranking lists
		$export30 = $org->getOrgsAdminInfo($thirtyDaysAgo);//get admin info then filter out only export data
		$exportArr30 = $this->getExportArr($export30);	//filter out only export data
		$num_of_approved_arr30 = $rec->getNumOfApprovedForColleges($college_id_arr, $thirtyDaysAgo);//total number of approved

		//$temp_count =0;
		for ($i=0; $i < count($admins_info); $i++) {

			// if ($temp_count == 5) {
			// 	break;
			// }
			//$temp_count++;

			$arr = array();

			$client_exists = false;

			foreach ($ret as $l) {
				if ($l['org_branch_id'] == $org_branch_id_arr[$i]) {
					$client_exists = true;
					$arr = $l;
					break;
				}
			}

			if ($client_exists == true) {



				$arr['login_as'] = '/sales/loginas/'.Crypt::encrypt($user_id_arr[$i]);
				$arr['name'] = $name_arr[$i];
				$arr['user_id'] = $user_id_arr[$i];
				$arr['export_file_cnt'] = $export_file_cnt_arr[$i];
				$arr['org_branch_id'] = $org_branch_id_arr[$i];
				$arr['school_id'] = $college_id_arr[$i];
				$arr['date_joined'] = $date_joined_arr[$i];

				if ($tp->getLastLoggedInDate($user_id_arr[$i]) == "N/A") {
					$arr['last_logged_in'] = '999999';
				}else{
					$arr['last_logged_in'] = $this->xTimeAgoHr($tp->getLastLoggedInDate($user_id_arr[$i]), date("Y-m-d H:i:s"));
				}

				$total_received = $cml->getNumOfMsgReceivedOfChatAndMessages($user_id_arr[$i]);

				if (isset($total_received)) {
					foreach ($total_received as $k) {
						if ($k->org_branch_id == $org_branch_id_arr[$i]) {
							if ($k->is_chat == 0) {
								$arr['total_msg_received'] = $k->cnt;
							}else{
								$arr['total_chat_received'] = $k->cnt;
							}
						}
					}
				}

				$arr['total_chat_received'] = (isset($arr['total_chat_received']) ? $arr['total_chat_received'] : 0);
				$arr['total_msg_received'] = (isset($arr['total_msg_received']) ? $arr['total_msg_received'] : 0);

				$arr['total_chat_sent'] = (isset($total_user_chat_sent_arr[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $total_user_chat_sent_arr[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);
				$arr['total_msg_sent'] = (isset($total_user_msg_sent_arr[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $total_user_msg_sent_arr[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);

				$arr['total_messages'] = $arr['total_chat_received'] + $arr['total_chat_sent'] + $arr['total_msg_sent'] + $arr['total_msg_received'];

				$arr['num_of_idle_msgs'] = $this->getArrayValue('cnt', 'user_id', $user_id_arr[$i], $overall_num_idls_msgs);

				if ($arr['total_msg_sent'] + $arr['num_of_idle_msgs'] == 0) {
					$arr['avg_response_rate'] = '0.00%';
				}else{
					$arr['avg_response_rate'] = number_format((($arr['total_msg_sent'] + $arr['total_chat_sent']) / ($arr['total_msg_sent'] + $arr['total_chat_sent'] + $arr['num_of_idle_msgs']) ) * 100 , 2, '.', ''). '%';
				}
				$past2weeks = 14;
				if (!isset($arr['logged_in_recently']) || $arr['logged_in_recently'] == 'No') {
					$arr['logged_in_recently'] = $arr['last_logged_in'] < 24*$past2weeks ? 'Yes' : 'No';
				}else{
					$arr['logged_in_recently'] = 'No';
				}

				$arr['this_user_id']    = $user_id_arr[$i];

			}else{
				$arr['login_as'] = '/sales/loginas/'.Crypt::encrypt($user_id_arr[$i]);
				$arr['name'] = $name_arr[$i];
				$arr['org_name'] = $org_name_arr[$i];
				$arr['org_branch_id'] = $org_branch_id_arr[$i];
				$arr['user_id'] = $user_id_arr[$i];
				$arr['customer_type'] = $customer_type_arr[$i];
				$arr['is_client'] = ($arr['customer_type'] == 'Free' ? false : true);
				$arr['export_file_cnt'] = $export_file_cnt_arr[$i];
				$arr['school_id'] = $college_id_arr[$i];

				// print_r($org_branch_id_arr[$i] ."<br/>");
				$total_received = $cml->getNumOfMsgReceivedOfChatAndMessages($user_id_arr[$i]);

				if (isset($total_received)) {
					foreach ($total_received as $k) {
						if ($k->org_branch_id == $org_branch_id_arr[$i]) {
							if ($k->is_chat == 0) {
								$arr['total_msg_received'] = $k->cnt;
							}else{
								$arr['total_chat_received'] = $k->cnt;
							}
						}
					}
				}


				$arr['total_chat_received'] = (isset($arr['total_chat_received']) ? $arr['total_chat_received'] : 0);
				$arr['total_msg_received'] = (isset($arr['total_msg_received']) ? $arr['total_msg_received'] : 0);

				$arr['total_chat_sent'] = (isset($total_user_chat_sent_arr[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $total_user_chat_sent_arr[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);
				$arr['total_msg_sent'] = (isset($total_user_msg_sent_arr[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $total_user_msg_sent_arr[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);

				$arr['total_messages'] = $arr['total_chat_received'] + $arr['total_chat_sent'] + $arr['total_msg_sent'] + $arr['total_msg_received'];

				$arr['num_of_idle_msgs'] = $this->getArrayValue('cnt', 'user_id', $user_id_arr[$i], $overall_num_idls_msgs);

				if (strlen($school_name_arr[$i]) >20) {
					$arr['school_name'] = substr($school_name_arr[$i], 0, 25). '...';
				}else{
					$arr['school_name'] = $school_name_arr[$i];
				}

				$arr['date_joined'] = $date_joined_arr[$i];

				if ($tp->getLastLoggedInDate($user_id_arr[$i]) == "N/A") {
					$arr['last_logged_in'] = '999999';
				}else{
					$arr['last_logged_in'] = $this->xTimeAgoHr($tp->getLastLoggedInDate($user_id_arr[$i]), date("Y-m-d H:i:s"));
				}

				$arr['num_daily_chat'] =  $this->getArrayValue('cnt', 'org_branch_id', $org_branch_id_arr[$i], $num_of_daily_chat_arr);

				$arr['num_of_days_chatted'] =  $this->getArrayValue('cnt', 'org_branch_id', $org_branch_id_arr[$i], $num_of_days_chatted_arr);

				$arr['num_of_inquiries'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_inquiries_arr);
				$arr['num_of_inquiries_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_inquiries_rejected_arr);
				$arr['num_of_inquiries_accepted'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_inquiries_accepted_arr);
				$arr['num_of_inquiries_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_inquiries_idle_arr);

				$arr['num_of_recommendations'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_recommendations_arr);
				$arr['num_of_recommendations_accepted_pending'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_recommendations_accepted_pending_arr);
				$arr['num_of_recommendations_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_recommendations_rejected_arr);
				$arr['num_of_recommendations_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_recommendations_idle_arr);

				$arr['num_of_advance_search'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_advance_search_arr);
				//$arr['num_of_advance_search_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_advance_search_rejected_arr);
				$arr['num_of_advance_search_approved'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_advance_search_accepted_arr);
				//$arr['num_of_advance_search_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_advance_search_idle_arr);

				$arr['total_num_of_pending_from_all_sources'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $total_num_of_pending_from_all_sources_arr);
				$arr['total_num_of_approved_by_pending'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $total_num_of_approved_by_pending_arr);
				if ($arr['total_num_of_pending_from_all_sources'] == 0) {
					$arr['percent_of_approved_by_pending'] = '0.00%';
				}else{
					$arr['percent_of_approved_by_pending'] = number_format(($arr['total_num_of_approved_by_pending'] / $arr['total_num_of_pending_from_all_sources']) * 100 , 2, '.', '').'%';
				}


				$arr['num_of_total_approved']  = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_approved_arr);
				if ($arr['num_of_inquiries'] == 0) {
					$arr['percent_approved_via_inquiry'] = '0.00%';
				}else{
					$arr['percent_approved_via_inquiry'] = number_format(($arr['num_of_inquiries_accepted'] / $arr['num_of_inquiries']) * 100 , 2, '.', '').'%';
				}

				$arr['num_of_recommendation_approved'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_recommendation_approved_arr);
				if ($arr['num_of_recommendations'] == 0) {
					$arr['percent_approved_via_recommendation'] = '0.00%';
				}else{
					$arr['percent_approved_via_recommendation'] = number_format(($arr['num_of_recommendation_approved'] / $arr['num_of_recommendations']) * 100 , 2, '.', '').'%';
				}

				$arr['num_of_auto_approve_recommendation_approved'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_auto_approve_recommendation_approved_arr);
				$arr['num_of_auto_approve_recommendation_approved'] = (isset($arr['num_of_auto_approve_recommendation_approved']) ? $arr['num_of_auto_approve_recommendation_approved'] : 0);

				$arr['num_of_auto_approve_recommendation'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_auto_approve_recommendation_arr);
				$arr['num_of_auto_approve_recommendation'] = (isset($arr['num_of_auto_approve_recommendation']) ? $arr['num_of_auto_approve_recommendation'] : 0);

				if ($arr['num_of_auto_approve_recommendation'] == 0) {
					$arr['percent_approved_via_auto_approve_recommendation'] = '0.00%';
				}else{
					$arr['percent_approved_via_auto_approve_recommendation'] = number_format(($arr['num_of_auto_approve_recommendation_approved'] / $arr['num_of_auto_approve_recommendation'])  * 100 , 2, '.', '').'%';
				}

				if ($arr['num_of_advance_search'] == 0) {
					$arr['percent_approved_via_advance_search'] = '0.00%';
				}else{
					$arr['percent_approved_via_advance_search'] = number_format(($arr['num_of_advance_search_approved'] / $arr['num_of_advance_search'])  * 100 , 2, '.', '').'%';
				}
				//$arr['num_of_pending']   = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_pending_arr);
				//$arr['num_of_rejected']  = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_rejected_arr);

				$arr['num_profile_view'] = $this->getArrayValue('cnt', 'submited_id', $user_id_arr[$i], $num_profile_view_arr);
				$arr['num_of_likes']     = $this->getArrayValue('cnt', 'type_val'  , $college_id_arr[$i], $num_of_likes_arr);

				$arr['num_of_uploaded_ranking'] = $this->getArrayValue('cnt', 'custom_college', $college_id_arr[$i], $num_of_uploaded_ranking_arr);

				//$arr['user_activity']    = $this->getArrayValue('cnt', 'user_id'  , $user_id_arr[$i], $num_user_activity_arr);

				// $tmp = array();

				// $tmp['type'] = 'college';
				// $tmp['type_col'] = 'id';
				// $tmp['type_val'] = $college_id_arr[$i];

				// $num_of_likes = $lt->getLikesTally($tmp);
				// $arr['num_of_likes'] = $num_of_likes->cnt;
				// $arr['total_num_of_msgs'] = $cml->numOfMsgSent($user_id_arr[$i], $org_branch_id_arr[$i]);

				 $arr['num_of_idle_msgs'] = $this->getArrayValue('cnt', 'user_id', $user_id_arr[$i], $overall_num_idls_msgs);


				//$num_of_idle_msgs_arr = $cml->getNumOfIdleMsgs($org_branch_id_arr[$i]);

				//$arr['num_of_idle_msgs'] = $num_of_idle_msgs_arr->cnt ;

				// $arr['num_of_idle_msgs'] = 'N/A';


				if ($arr['total_msg_sent'] + $arr['num_of_idle_msgs'] == 0) {
					$arr['avg_response_rate'] = '0.00%';
				}else{
					$arr['avg_response_rate'] = number_format((($arr['total_msg_sent'] + $arr['total_chat_sent']) / ($arr['total_msg_sent'] + $arr['total_chat_sent'] + $arr['num_of_idle_msgs']) ) * 100 , 2, '.', ''). '%';
				}

				$arr['filtered_recommendations'] = 0;
				$arr['non_filtered_recommendations'] =0;
				$arr['college_recommendation_action'] = '';

				$this_college_recommendation = $cr->getTodayRecommendations($college_id_arr[$i]);

				$yes_rec = 0;
				$no_rec = 0;
				$neutral_rec = 0;

				foreach ($this_college_recommendation as $rec_key) {

					if ($rec_key->type == 'not_filtered') {
						$arr['non_filtered_recommendations']++;
					}else{
						$arr['filtered_recommendations']++;
					}

					if($rec_key->active == 1){
						$neutral_rec++;

					}elseif ($rec_key->active == 0) {
						$yes_rec++;
					}else{
						$no_rec++;
					}

				}

				$arr['college_recommendation_action'] = $yes_rec.'/'.$no_rec.'/'.$neutral_rec;


				//engagement calculations - llllllllll
				$dateJoined = Carbon::createFromTimestamp( strtotime($arr['date_joined']) );
				$daysSinceJoining = $now->diffInDays( $dateJoined );
				$twoWeeksAgo_data = array();
				$monthAgo_data = array();
				$overall_data = array();

				$twoWeeksAgo_data['grading_type'] = 14;
				$monthAgo_data['grading_type'] = 30;

				$arr['engagement_score'] = 'engagement_score';
				$arr['overall_activity_score'] = number_format($this->calculateOverallActivityScore($arr), 2);

				// -- yesterday
				//passing above values into getArrayValue method to extract value and store in temp array
				$ta1['num_of_inquiries_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iRejectNum1);
				$ta1['num_of_inquiries_accepted'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iAcceptNum1);
				$ta1['num_of_inquiries_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iIdleNum1);
				$ta1['num_of_recommendations_accepted_pending'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rRejectNum1);
				$ta1['num_of_recommendations_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rAcceptNum1);
				$ta1['num_of_recommendations_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rIdleNum1);
				$ta1['num_of_days_chatted'] =  $this->getArrayValue('cnt', 'org_branch_id', $org_branch_id_arr[$i], $chatted1);
				$ta1['num_of_advance_search_approved'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $acceptedFromAdvSearch1);
				$ta1['num_profile_view'] = $this->getArrayValue('cnt', 'submited_id', $user_id_arr[$i], $profViews1);
				$ta1['num_of_uploaded_ranking'] = $this->getArrayValue('cnt', 'custom_college', $college_id_arr[$i], $upRankingList1);
				$ta1['total_chat_sent'] = (isset($chatSentArr1[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $chatSentArr1[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);
				$ta1['total_msg_sent'] = (isset($msgSentArr1[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $msgSentArr1[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);
				$ta1['export_file_cnt'] = $exportArr1[$i];//not right numbers - fix query from Organization Model

				$yesterdaysActivityScore = $this->calculateOverallActivityScore($ta1);
				$arr['yesterdays_activity_score'] = number_format($yesterdaysActivityScore);
				$arr['yesterdays_activity_status'] = $yesterdaysActivityScore > 1 ? 'Increasing' : 'Decreasing';
				$arr['yesterdays_activity_grade'] = $this->getGrade($yesterdaysActivityScore);

				// -- last 14 days
				//passing above values into getArrayValue method to extract value and store in temp array
				$ta14['num_of_inquiries'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iTotal14);
				$ta14['num_of_inquiries_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iRejectNum14);
				$ta14['num_of_inquiries_accepted'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iAcceptNum14);
				$ta14['num_of_inquiries_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iIdleNum14);
				$ta14['num_of_recommendations_accepted_pending'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rRejectNum14);
				$ta14['num_of_recommendations_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rAcceptNum14);
				$ta14['num_of_recommendations_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rIdleNum14);
				$ta14['num_of_days_chatted'] =  $this->getArrayValue('cnt', 'org_branch_id', $org_branch_id_arr[$i], $chatted14);
				$ta14['num_of_advance_search_approved'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $acceptedFromAdvSearch14);
				$ta14['num_profile_view'] = $this->getArrayValue('cnt', 'submited_id', $user_id_arr[$i], $profViews14);
				$ta14['num_of_uploaded_ranking'] = $this->getArrayValue('cnt', 'custom_college', $college_id_arr[$i], $upRankingList14);
				$ta14['total_chat_sent'] = (isset($chatSentArr14[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $chatSentArr14[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);
				$ta14['total_msg_sent'] = (isset($msgSentArr14[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $msgSentArr14[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);
				$ta14['export_file_cnt'] = $exportArr14[$i];
				$ta14['num_of_total_approved']  = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_approved_arr14);

				$last14dayActivityScore = $this->calculateOverallActivityScore($ta14);
				$arr['last_14days_activity_score'] = number_format($last14dayActivityScore, 2);
				$arr['last_14days_activity_status'] = $last14dayActivityScore > 1 ? 'Increasing' : 'Decreasing';
				$arr['last_14days_activity_grade'] = $this->getGrade($last14dayActivityScore);

				// -- last 30 days
				//passing above values into getArrayValue method to extract value and store in temp array
				$ta30['num_of_inquiries'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iTotal30);
				$ta30['num_of_inquiries_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iRejectNum30);
				$ta30['num_of_inquiries_accepted'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iAcceptNum30);
				$ta30['num_of_inquiries_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $iIdleNum30);
				$ta30['num_of_recommendations_accepted_pending'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rRejectNum30);
				$ta30['num_of_recommendations_rejected'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rAcceptNum30);
				$ta30['num_of_recommendations_idle'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $rIdleNum30);
				$ta30['num_of_days_chatted'] =  $this->getArrayValue('cnt', 'org_branch_id', $org_branch_id_arr[$i], $chatted30);
				$ta30['num_of_advance_search_approved'] = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $acceptedFromAdvSearch30);
				$ta30['num_profile_view'] = $this->getArrayValue('cnt', 'submited_id', $user_id_arr[$i], $profViews30);
				$ta30['num_of_uploaded_ranking'] = $this->getArrayValue('cnt', 'custom_college', $college_id_arr[$i], $upRankingList30);
				$ta30['total_chat_sent'] = (isset($chatSentArr30[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $chatSentArr30[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);
				$ta30['total_msg_sent'] = (isset($msgSentArr30[$org_branch_id_arr[$i]][$user_id_arr[$i]]) ? $msgSentArr30[$org_branch_id_arr[$i]][$user_id_arr[$i]] : 0);
				$ta30['export_file_cnt'] = $exportArr30[$i];
				$ta30['num_of_total_approved']  = $this->getArrayValue('cnt', 'college_id', $college_id_arr[$i], $num_of_approved_arr30);

				$last30dayActivityScore = $this->calculateOverallActivityScore($ta30);
				$arr['last_30days_activity_score'] = number_format($last30dayActivityScore, 2);
				$arr['last_30days_activity_status'] = $last30dayActivityScore > 1 ? 'Increasing' : 'Decreasing';
				$arr['last_30days_activity_grade'] = $this->getGrade($last30dayActivityScore);


				//calculating engagement delta - positive means likely to upgrade account, negative means not likely
				// Delta = last 30 days score - Threshold point (480)
				$past2weeks = 14;
				$eThreshold = 480;
				$arr['engagement_delta'] = (int)$last30dayActivityScore - $eThreshold;

				//engagement activity calculations
				if ($past2weeks == 0 || $arr['overall_activity_score'] == 0 || $daysSinceJoining == 0) {
					$eActivity = 0 ;
				}else{
					$eActivity = ($arr['last_14days_activity_score'] / $past2weeks) / ($arr['overall_activity_score'] / $daysSinceJoining );
				}

				$arr['engagement_activity'] = $eActivity > 1 ? 'Increasing' : 'Decreasing';
				$arr['engagement_activity_grade'] = $this->getGrade($eActivity);

				//logged in recently calculations - last_logged_in is greater than $past2weeks (two weeks) than college has NOT logged in recently
				if( $arr['last_logged_in'] == '999999' ){
					$arr['logged_in_recently'] = 'No';
				}else{
					$arr['logged_in_recently'] = $arr['last_logged_in'] < 24*$past2weeks ? 'Yes' : 'No';
				}
				$arr['last_logged_in_text'] = $this->getLastLoggedInText($arr['last_logged_in']);

				$twoWeeksAgo_data['last_logged_in'] = $arr['last_logged_in'];
				$monthAgo_data['last_logged_in'] = $arr['last_logged_in'];

				// ----- inquiry volume - past two weeks
				//if inquiries in past two weeks is 4+ then above average
				//if less than 2 then below average
				//otherwise if 2 or 3 then average
				if( $ta14['num_of_inquiries'] > 3 ){
					$arr['inquiry_volume'] = 'Above Average';
				}elseif( $ta14['num_of_inquiries'] < 2 ){
					$arr['inquiry_volume'] = 'Below Average';
				}else{
					$arr['inquiry_volume'] = 'Average';
				}

				// ----- inquiry activity - past two weeks
				//above average =  x > 50.01%
				//average = 30.01% > x < 50%
				//below average = x < 30%
				if( $ta14['num_of_inquiries'] > 0 ){
					$iActivity = ($ta14['num_of_inquiries_accepted'] + $ta14['num_of_inquiries_rejected']) / $ta14['num_of_inquiries'];
					if( $iActivity > 0.50 ){
						$arr['inquiry_activity'] = 'Above Average';
					}elseif( $iActivity < 0.30 ){
						$arr['inquiry_activity'] = 'Below Average';
					}else{
						$arr['inquiry_activity'] = 'Average';
					}
				}else{
					$arr['inquiry_activity'] = 'Below Average';
				}

				// ----- recommendation activity - past two weeks
				//above average = accepts or rejects > 3 recommendations
				//average = accepts or rejects 2 recommendations
				//below average = accepts or rejects < 2 recommendations
				$arr['recommendation_activity'] = '';
				if( $ta14['num_of_recommendations_idle'] > 0 ){
					$rActivity = (($ta14['num_of_recommendations_accepted_pending'] / $ta14['num_of_recommendations_idle']) + $ta14['num_of_recommendations_rejected']) / $past2weeks;
					if( $rActivity > 2.5 ){
						$arr['recommendation_activity'] = 'Above Average';
					}elseif( $rActivity < 1.5 ){
						$arr['recommendation_activity'] = 'Below Average';
					}else{
						$arr['recommendation_activity'] = 'Average';
					}
				}else{
					$arr['recommendation_activity'] = 'Below Average';
				}

				// ----- Approval Activity - past two weeks
				//above average = 6+ students moved to approved
				//average = 3-5 students moved to approved
				//below average = < 2 moved to approved
				$arr['approval_activity'] = '';
				if( $ta14['num_of_total_approved'] > 5 ){
					$arr['approval_activity'] = 'Above Average';
				}elseif( $ta14['num_of_total_approved'] < 3 ){
					$arr['approval_activity'] = 'Below Average';
				}else{
					$arr['approval_activity'] = 'Average';
				}

				//college has used advanced search in past two weeks?
				$arr['adv_search_use'] = $ta14['num_of_advance_search_approved'] > 0 ? 'Yes' : 'No';

				//college has used chat/msg in last two weeks?
				$arr['chat_use'] = $ta14['total_chat_sent'] > 0 ? 'Yes' : 'No';
				$arr['msg_use'] = $ta14['total_msg_sent'] > 0 ? 'Yes' : 'No';

				//college's filter recommendations is active; is receiving filtered recommendations
				$arr['filter_active'] = $arr['filtered_recommendations'] > 0 ? 'Yes' : 'No';


				// ------------------------------------------- grading
				$topics = array('num_profile_view', 'num_of_inquiries','inquiry_activity',
								'recommendation_activity','num_of_total_approved','num_of_advance_search_approved',
								'num_of_days_chatted','total_chat_sent','total_msg_sent');

				$criteria_twoweeks = array('20,13,5,1', '5,4,3,1', '0.99,0.94,0.86,0.35', '48,43,33,8', '6,5,3,1',
											'20,16,11,6', '5,4,2,1', '30,21,11,1', '40,27,14,1');

				$criteria_month = array('40,25,10,2', '12,9,5,2', '99,94,86,35', '96,85,65,15', '13,9,5,2',
										'40,31,21,11', '10,7,4,1', '60,41,21,1', '80,53,27,1');

				$criteria_overall = array('1.33,0.81,0.27,0.07', '0.5,0.31,0.14,0.04', '99,94,86,35', '3.4,3.01,2.34,0.51', '0.5,0.34,0.21,0.06',
										'1.43,1.08,0.72,0.37', '0.36,0.23,0.08,0.01', '2.14,1.44,0.72,0.01', '2.86,1.87,0.94,0.01');

				//generate scores and grades
				$twoWeeksAgo_data = $this->generateScoreData($topics, $ta14, $criteria_twoweeks, 'twoWeeks');
				$monthAgo_data = $this->generateScoreData($topics, $ta30, $criteria_month, 'month');
				$overall_data = $this->generateScoreData($topics, $arr, $criteria_overall, 'overall', $arr['date_joined']);

				// adding last logged in data
				$twoWeeksAgo_data['last_logged_in'] = $this->generateLastLoggedInData($arr['last_logged_in']);
				$monthAgo_data['last_logged_in'] = $this->generateLastLoggedInData($arr['last_logged_in']);
				$overall_data['last_logged_in'] = $this->generateLastLoggedInData($arr['last_logged_in']);

				//generate total score and total grades
				$twoWeeksAgo_data['total'] = $this->generateTotals($twoWeeksAgo_data);
				$monthAgo_data['total'] = $this->generateTotals($monthAgo_data);
				$overall_data['total'] = $this->generateTotals($overall_data);

				//format for display
				// $twoWeeksAgo_data = $this->formatScoresForDisplay($twoWeeksAgo_data);
				// $monthAgo_data = $this->formatScoresForDisplay($monthAgo_data);
				// $overall_data = $this->formatScoresForDisplay($overall_data);

				//add filter active data
				$twoWeeksAgo_data['filter_active'] = '--';
				$monthAgo_data['filter_active'] = '--';
				$overall_data['filter_active'] = $arr['filter_active'];
				$overall_data['days_since_joining'] = $this->getDaysSinceJoiningText($arr['date_joined']);

				//save to $arr
				$arr['twoWeeksAgo_data'] = $twoWeeksAgo_data;
				$arr['monthAgo_data'] = $monthAgo_data;
				$arr['overall_data'] = $overall_data;

				//college id
				$arr['this_college_id'] = $college_id_arr[$i];

				//user_id
				$arr['this_user_id']    = $user_id_arr[$i];

				// ------------------------------------------- goal tracking
				$goals = array();





				// $goals['apps'] = array();
				// $goals['enrolls'] = array();
				// $goals['approved'] = array();
				$applied_progress = 0;
				$enrolled_progress = 0;
				$appr_progress = 0;
				$app_perc = 0;
				$enr_perc = 0;
				$date1 = Carbon::now();
				$date2 = Carbon::now();
				$firstOfQuarter = $date1->firstOfQuarter()->toDateTimeString();
				$lastOfQuarter  = $date2->lastOfQuarter()->toDateTimeString();
				$firstDayYear = Carbon::now()->startOfYear()->toDateTimeString();
				$firstDayMonth = Carbon::now()->startOfMonth()->toDateTimeString();

				$rec = new Recruitment;
				$temp_appr_arr = array();
				$periods = array('monthly', 'quarterly', 'annually');
				$startOfMonth = Carbon::today()->startOfMonth();
				$endOfMonth = Carbon::today()->endOfMonth();

				//get current goals
				$orgBranchData = OrganizationBranch::find($arr['org_branch_id']);
				$enrolled_goals = $orgBranchData->num_of_enrollments;
				$applied_goals = $orgBranchData->num_of_applications;
				if ($orgBranchData->bachelor_plan == 1 && $applied_goals != 0) {
					$arr['is_goal_setup'] = 1;
				}else{
					$arr['is_goal_setup'] = 0;
				}

				// Approved methods
				$tmp = $rec->getNumOfTotalApprovedForColleges(array($college_id_arr[$i]), $firstOfQuarter, $lastOfQuarter);
				foreach ($tmp as $key) {
					$approvedQuarterly =  $key->cnt;
				}
				$approvedQuarterly =  isset($approvedQuarterly) ? $approvedQuarterly : 0;

				$tmp = $rec->getNumOfTotalApprovedForColleges(array($college_id_arr[$i]), $firstDayYear);
				foreach ($tmp as $key) {
					$approvedAnnually =  $key->cnt;
				}
				$approvedAnnually =  isset($approvedAnnually) ? $approvedAnnually : 0;

				$tmp = $rec->getNumOfTotalApprovedForColleges(array($college_id_arr[$i]), $firstDayMonth);
				foreach ($tmp as $key) {
					$approvedMonthly =  $key->cnt;
				}
				$approvedMonthly =  isset($approvedMonthly) ? $approvedMonthly : 0;
				// End of approved methods

				// Applied methods
				$tmp = $rec->getNumOfApplied(array($college_id_arr[$i]), $firstOfQuarter, $lastOfQuarter);
				foreach ($tmp as $key) {
					$appliedQuarterly =  $key->cnt;
				}
				$appliedQuarterly =  isset($appliedQuarterly) ? $appliedQuarterly : 0;

				$tmp = $rec->getNumOfApplied(array($college_id_arr[$i]), $firstDayYear);
				foreach ($tmp as $key) {
					$appliedAnnually =  $key->cnt;
				}
				$appliedAnnually =  isset($appliedAnnually) ? $appliedAnnually : 0;

				$tmp = $rec->getNumOfApplied(array($college_id_arr[$i]), $firstDayMonth);
				foreach ($tmp as $key) {
					$appliedMonthly =  $key->cnt;
				}
				$appliedMonthly =  isset($appliedMonthly) ? $appliedMonthly : 0;
				// End of applied methods

				// Enrolled methods
				$tmp = $rec->getNumOfEnrolled(array($college_id_arr[$i]), $firstOfQuarter, $lastOfQuarter);
				foreach ($tmp as $key) {
					$enrolledQuarterly =  $key->cnt;
				}
				$enrolledQuarterly =  isset($enrolledQuarterly) ? $enrolledQuarterly : 0;

				$tmp = $rec->getNumOfEnrolled(array($college_id_arr[$i]), $firstDayYear);
				foreach ($tmp as $key) {
					$enrolledAnnually =  $key->cnt;
				}
				$enrolledAnnually =  isset($enrolledAnnually) ? $enrolledAnnually : 0;

				$tmp = $rec->getNumOfEnrolled(array($college_id_arr[$i]), $firstDayMonth);
				foreach ($tmp as $key) {
					$enrolledMonthly =  $key->cnt;
				}
				$enrolledMonthly =  isset($enrolledMonthly) ? $enrolledMonthly : 0;
				// End of enrolled methods

				$goals['enrolls-progress-monthly'] = $enrolledMonthly;
				$goals['enrolls-progress-quarterly'] = $enrolledQuarterly;
				$goals['enrolls-progress-annually'] = $enrolledAnnually;

				$goals['enrolls-goal-monthly']   = ceil($enrolled_goals  / 12);
				$goals['enrolls-goal-quarterly'] = ceil($enrolled_goals / 4);
				$goals['enrolls-goal-annually']  = ceil($enrolled_goals) ;

				$goals['enrolls-perc-monthly']   = $goals['enrolls-goal-monthly'] > 0 ? (number_format(($goals['enrolls-progress-monthly'] / $goals['enrolls-goal-monthly']) * 100, 2)) : 0;
				$goals['enrolls-perc-quarterly'] = $goals['enrolls-goal-quarterly'] > 0 ? (number_format(($goals['enrolls-progress-quarterly'] / $goals['enrolls-goal-quarterly']) * 100, 2)) : 0;
				$goals['enrolls-perc-annually']  = $goals['enrolls-goal-annually'] > 0 ? (number_format(($goals['enrolls-progress-annually'] / $goals['enrolls-goal-annually']) * 100, 2)) : 0;

				$goals['enrolls-delta-monthly']   = $goals['enrolls-progress-monthly']  - $goals['enrolls-goal-monthly'];
				$goals['enrolls-delta-quarterly'] = $goals['enrolls-progress-quarterly']  - $goals['enrolls-goal-quarterly'];
				$goals['enrolls-delta-annually']  = $goals['enrolls-progress-annually']  - $goals['enrolls-goal-annually'];

				$goals['enrolls-delta-monthly-color']   = $goals['enrolls-delta-monthly'] >= 0 ? 'positive' : 'negative';
				$goals['enrolls-delta-quarterly-color'] = $goals['enrolls-delta-quarterly'] >= 0 ? 'positive' : 'negative';
				$goals['enrolls-delta-annually-color']  = $goals['enrolls-delta-annually'] >= 0 ? 'positive' : 'negative';

				$goals['apps-progress-monthly'] = $appliedMonthly;
				$goals['apps-progress-quarterly'] = $appliedQuarterly;
				$goals['apps-progress-annually'] = $appliedAnnually;

				$goals['apps-goal-monthly']   = ceil(($applied_goals) /12);
				$goals['apps-goal-quarterly'] = ceil(($applied_goals) /4);
				$goals['apps-goal-annually']  = ceil($applied_goals);

				$goals['apps-perc-monthly']   = $goals['apps-goal-monthly'] > 0 ? (number_format(($goals['apps-progress-monthly'] / $goals['apps-goal-monthly']) * 100, 2)) : 0;
				$goals['apps-perc-quarterly'] = $goals['apps-goal-quarterly'] > 0 ? (number_format(($goals['apps-progress-quarterly'] / $goals['apps-goal-quarterly']) * 100, 2)) : 0;
				$goals['apps-perc-annually']  = $goals['apps-goal-annually'] > 0 ? (number_format(($goals['apps-progress-annually'] / $goals['apps-goal-annually']) * 100, 2)) : 0;

				$goals['apps-delta-monthly']   = $goals['apps-progress-monthly']  - $goals['apps-goal-monthly'];
				$goals['apps-delta-quarterly'] = $goals['apps-progress-quarterly']  - $goals['apps-goal-quarterly'];
				$goals['apps-delta-annually']  = $goals['apps-progress-annually']  - $goals['apps-goal-annually'];

				$goals['apps-delta-monthly-color'] = $goals['apps-delta-monthly'] >= 0 ? 'positive' : 'negative';
				$goals['apps-delta-quarterly-color'] = $goals['apps-delta-quarterly'] >= 0 ? 'positive' : 'negative';
				$goals['apps-delta-annually-color'] = $goals['apps-delta-annually'] >= 0 ? 'positive' : 'negative';

				$goals['approved-progress-monthly'] = $approvedMonthly;
				$goals['approved-progress-quarterly'] = $approvedQuarterly;
				$goals['approved-progress-annually'] = $approvedAnnually;

				$goals['approved-goal-monthly']   = ceil(($applied_goals * 10 ) /12);
				$goals['approved-goal-quarterly'] = ceil(($applied_goals * 10 ) /4);
				$goals['approved-goal-annually']  = ceil($applied_goals * 10 );

				$goals['approved-perc-monthly'] = $goals['approved-goal-monthly'] > 0 ? (number_format(($goals['approved-progress-monthly'] / $goals['approved-goal-monthly']) * 100, 2)) : 0;
				$goals['approved-perc-quarterly'] = $goals['approved-goal-quarterly'] > 0 ? (number_format(($goals['approved-progress-quarterly'] / $goals['approved-goal-quarterly']) * 100, 2)) : 0;
				$goals['approved-perc-annually'] = $goals['approved-goal-annually'] > 0 ? (number_format(($goals['approved-progress-annually'] / $goals['approved-goal-annually']) * 100, 2)) : 0;

				$goals['approved-delta-monthly']   = $goals['approved-progress-monthly']  - $goals['approved-goal-monthly'];
				$goals['approved-delta-quarterly'] = $goals['approved-progress-quarterly']  - $goals['approved-goal-quarterly'];
				$goals['approved-delta-annually']  = $goals['approved-progress-annually']  - $goals['approved-goal-annually'];

				$goals['approved-delta-monthly-color']   = $goals['approved-delta-monthly'] >= 0 ? 'positive' : 'negative';
				$goals['approved-delta-quarterly-color'] = $goals['approved-delta-quarterly'] >= 0 ? 'positive' : 'negative';
				$goals['approved-delta-annually-color']  = $goals['approved-delta-quarterly'] >= 0 ? 'positive' : 'negative';


				$arr['goals'] =  json_encode($goals);

				$arr['approved-perc-monthly'] =  $goals['approved-perc-monthly'];

				// ----------------------------------------- triggers
				$triggers = array();
				$triggers['is_set'] = $orgBranchData->trigger_set;
				$triggers['frequency'] = $orgBranchData->trigger_frequency;
				$triggers['emergency'] = array();
				$triggers['emergency']['is_set'] = $orgBranchData->trigger_emergency_set;
				$triggers['emergency']['perc'] = $orgBranchData->trigger_emergency_percentage;
				$triggers['emails'] = $orgBranchData->trigger_notify_emails;

				$arr['triggers'] = $triggers;
			}
			$ret[] = $arr;
		}

		$ret = $this->calculateRank($ret);

		// The following method takes a look at the cache, if the school already exists in the cache, we remove it
		// and replace it with the new data that we have, if not, we would just add it to cache, and place it back
		// in the cache

		if (Cache::has(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate')) {

			$cached_ret = Cache::get(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate');

			foreach ($ret as $key => $value) {
				$check = false;
				if ($this->get_index_multidimensional_boolean($cached_ret, 'user_id', $value['user_id'])) {
					unset($cached_ret[$key]);
					$cached_ret[$key] = $value;
				}else{
					$cached_ret[] = $value;
				}
			}

			// $ret = array_merge(array_values($cached_ret), array_values($ret));
			Cache::put(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate', $cached_ret, 120);
		}else{
			Cache::put(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate', $ret, 120);
		}

		return "success";
		//return $ret;
	}


	public function tracking(){
		$viewDataController = new ViewDataController();
	    $data = $viewDataController->buildData();

	    $data['title'] = 'Plexuss Sales Tracking';
	    $data['currentPage'] = 'sales-tracking';

	    $data['ip'] = $this->iplookup();

	    $start_date = "2018-09-03";
		$end_date   = "2018-09-03";

		$ret = array();
	    // $ret = $this->getUserStats();
	    $data['userStats'] = $ret;
	    return View('sales.tracking', $data);

	}

	public function site_performance() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Sales Site Performance';
		$data['currentPage'] = 'sales-site-performance';

		$data['ip'] = $this->iplookup();

		return View('sales.site_performance', $data);
	}


	public function socialNewsfeedAllPosts(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Sales Social Newsfeed';
		$data['currentPage'] = 'sales-social-newsfeed';
		$data['newsfeed_sub_page'] = 'All Posts';

		$data['ip'] = $this->iplookup();

		return View('sales.socialNewsfeed', $data);
	}

	public function socialNewsfeedPlexussOnlyPosts(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Sales Social Newsfeed';
		$data['currentPage'] = 'sales-social-newsfeed';
		$data['newsfeed_sub_page'] = 'Plexuss Only';

		$data['ip'] = $this->iplookup();

		return View('sales.socialNewsfeed', $data);
	}

	public function device_os_reporting() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Sales Site Performance';
		$data['currentPage'] = 'sales-device-os-reporting';

		$data['ip'] = $this->iplookup();

		return View('sales.device_os_reporting', $data);
	}

	public function student_tracking() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Sales Site Performance';
		$data['currentPage'] = 'sales-student-tracking';

		$data['ip'] = $this->iplookup();

		return View('sales.site_performance', $data);
	}

	public function getStats(){
		$input = Request::all();
		$ret = [];
		$response = [];
		$start_date = $input['startDate'];
		$end_date = $input['endDate'];
		$user = $input['userType'];

		if($user == 'Plexuss Users'){
			$user = 'users';
		}elseif ($user == 'Mobile App Users') {
			$user = 'apps';
		}else{
			$user = 'user_invites';
		}
		$ret = UserTrackingNumber::on('rds1')
						->select(DB::raw('sum(num_of_us_students) as num_of_us_students,
							sum(num_of_intl_students) as num_of_intl_students,
							sum(num_of_us_students_com) as num_of_us_students_com,
							sum(num_of_intl_students_com) as num_of_intl_students_com,
							sum(monetized_us_students_selected_1_5) as monetized_us_students_selected_1_5,
							sum(monetized_intl_students_selected_1_5) as monetized_intl_students_selected_1_5,
							sum(monetized_us_students_selected_over_5) as monetized_us_students_selected_over_5,
							sum(monetized_intl_students_selected_over_5) as monetized_intl_students_selected_over_5,
							sum(allschools_us_students_selected_1_5) as allschools_us_students_selected_1_5,
							sum(allschools_intl_students_selected_1_5) as allschools_intl_students_selected_1_5,
							sum(allschools_us_students_selected_over_5) as allschools_us_students_selected_over_5,
							sum(allschools_intl_students_selected_over_5) as allschools_intl_students_selected_over_5,
							sum(num_of_us_students_premium) as num_of_us_students_premium,
							sum(num_of_intl_students_premium) as num_of_intl_students_premium,
							sum(num_of_us_students_com_premium) as num_of_us_students_com_premium,
							sum(num_of_intl_students_com_premium) as num_of_intl_students_com_premium,
							sum(num_of_us_android) as num_of_us_android,
							sum(num_of_us_ios) as num_of_us_ios,
							sum(num_of_intl_android) as num_of_intl_android,
							sum(num_of_intl_ios) as num_of_intl_ios'))
						->where('type', $user)
						->whereBetween('date',array($start_date, $end_date))->first()->toArray();

		return $ret;
	}

	public function exportUserStats($start_date=null, $end_date=null, $user){
			if($user == 'Plexuss Users'){
				$user = 'users';
			}
			else{
				$user = 'user_invites';
			}
			if(!$start_date){
				$start_date = date('Y-m-d');
			}
			if(!$end_date){
				$end_date = date('Y-m-d');
			}

			$response = UserTrackingNumber::on('rds1')->where('type', $user)->whereBetween('date',array($start_date, $end_date))->get()->toArray();
			$delimiter = ",";
      $filename = $user."_".$start_date."_". $end_date . ".csv";
      $f = fopen('php://memory', 'w');
      $fields = array('Date','Type','num_of_us_students','num_of_intl_students','num_of_us_students_com','num_of_intl_students_com','monetized_us_students_selected_1_5','monetized_intl_students_selected_1_5','monetized_us_students_selected_over_5','monetized_intl_students_selected_over_5','allschools_us_students_selected_1_5','allschools_intl_students_selected_1_5','allschools_us_students_selected_over_5','allschools_intl_students_selected_over_5','num_of_us_students_premium','num_of_intl_students_premium','num_of_us_students_com_premium','num_of_intl_students_com_premium');
      fputcsv($f, $fields, $delimiter);
			foreach($response as $record){
				$lineData = array($record['date'],$record['type'],$record['num_of_us_students'],$record['num_of_intl_students'],$record['num_of_us_students_com'],$record['num_of_intl_students_com'],$record['monetized_us_students_selected_1_5'],$record['monetized_intl_students_selected_1_5'],$record['monetized_us_students_selected_over_5'],$record['monetized_intl_students_selected_over_5'],$record['allschools_us_students_selected_1_5'],$record['allschools_intl_students_selected_1_5'],$record['allschools_us_students_selected_over_5'],$record['allschools_intl_students_selected_over_5'],$record['num_of_us_students_premium'],$record['num_of_intl_students_premium'],$record['num_of_us_students_com_premium'],$record['num_of_intl_students_com_premium']);
					fputcsv($f, $lineData,$delimiter);
      }

      $ret = UserTrackingNumber::on('rds1')
						->select(DB::raw('sum(num_of_us_students) as num_of_us_students,
							sum(num_of_intl_students) as num_of_intl_students,
							sum(num_of_us_students_com) as num_of_us_students_com,
							sum(num_of_intl_students_com) as num_of_intl_students_com,
							sum(monetized_us_students_selected_1_5) as monetized_us_students_selected_1_5,
							sum(monetized_intl_students_selected_1_5) as monetized_intl_students_selected_1_5,
							sum(monetized_us_students_selected_over_5) as monetized_us_students_selected_over_5,
							sum(monetized_intl_students_selected_over_5) as monetized_intl_students_selected_over_5,
							sum(allschools_us_students_selected_1_5) as allschools_us_students_selected_1_5,
							sum(allschools_intl_students_selected_1_5) as allschools_intl_students_selected_1_5,
							sum(allschools_us_students_selected_over_5) as allschools_us_students_selected_over_5,
							sum(allschools_intl_students_selected_over_5) as allschools_intl_students_selected_over_5,
							sum(num_of_us_students_premium) as num_of_us_students_premium,
							sum(num_of_intl_students_premium) as num_of_intl_students_premium,
							sum(num_of_us_students_com_premium) as num_of_us_students_com_premium,
							sum(num_of_intl_students_com_premium) as num_of_intl_students_com_premium'))
						->where('type', $user)
						->whereBetween('date',array($start_date, $end_date))->first()->toArray();

			$lineData = array('','Total',$ret['num_of_us_students'],$ret['num_of_intl_students'],$ret['num_of_us_students_com'],$ret['num_of_intl_students_com'],$ret['monetized_us_students_selected_1_5'],$ret['monetized_intl_students_selected_1_5'],$ret['monetized_us_students_selected_over_5'],$ret['monetized_intl_students_selected_over_5'],$ret['allschools_us_students_selected_1_5'],$ret['allschools_intl_students_selected_1_5'],$ret['allschools_us_students_selected_over_5'],$ret['allschools_intl_students_selected_over_5'],$ret['num_of_us_students_premium'],$ret['num_of_intl_students_premium'],$ret['num_of_us_students_com_premium'],$ret['num_of_intl_students_com_premium']);
					fputcsv($f, $lineData,$delimiter);

			//================Calculating Values=========================//
			$lineData = array('');
			fputcsv($f, $lineData,$delimiter);
			$lineData = array('***Total Summary***');
			fputcsv($f, $lineData,$delimiter);
			$lineData = array('');
			fputcsv($f, $lineData,$delimiter);
				$Total_Students = ($ret['num_of_us_students'] + $ret['num_of_intl_students']);

			/* Completed 30 Calculation Start */
				$Total_Students_Completed30 = ($ret['num_of_us_students_com'] + $ret['num_of_intl_students_com']);

				if($ret['num_of_us_students']!=0){
					$Total_US_Students_Completed30_percent = round(($ret['num_of_us_students_com']/$ret['num_of_us_students'])*100) ;//. '%';
				}
				else{
					$Total_US_Students_Completed30_percent = 0;
				}

				if($ret['num_of_intl_students']!=0){
					$Total_Intl_Students_Completed30_percent = round(($ret['num_of_intl_students_com']/$ret['num_of_intl_students'])*100) ;//. '%';
				}else{
					$Total_Intl_Students_Completed30_percent = 0;
				}

				if($Total_Students!=0){
					$Total_Students_Completed30_percent = round(($Total_Students_Completed30/$Total_Students)*100) ;//. '%';
				}
				else{
					$Total_Students_Completed30_percent = 0;
				}
			/* Completed 30 Calculation Finish*/

			/* Select_1_School_Monetised Calculation Start */
				$Total_Students_Select_1_School_Monetised = ($ret['monetized_us_students_selected_1_5'] + $ret['monetized_intl_students_selected_1_5']);
				if($ret['num_of_us_students']!=0){
				$Total_US_Students_Select_1_School_Monetised_percent = round(($ret['monetized_us_students_selected_1_5']/$ret['num_of_us_students'])*100) ;//. '%';
				}
				else{
					$Total_US_Students_Select_1_School_Monetised_percent = 0;
				}
				if($ret['num_of_intl_students']!=0){
				$Total_Intl_Students_Select_1_School_Monetised_percent = round(($ret['monetized_intl_students_selected_1_5']/$ret['num_of_intl_students'])*100) ;//. '%';
				}
				else{
					$Total_Intl_Students_Select_1_School_Monetised_percent = 0;
				}
				if($Total_Students!=0){
				$Total_Students_Select_1_School_Monetised_percent = round(($Total_Students_Select_1_School_Monetised/$Total_Students)*100) ;//. '%';
				}
				else{
					$Total_Students_Select_1_School_Monetised_percent = 0;
				}
			/* Select_1_School_Monetised Calculation Finish*/

			/* Select_1_School_All_Schools Calculation Start */
				$Total_Students_Select_1_School_All_Schools = ($ret['allschools_us_students_selected_1_5'] + $ret['allschools_intl_students_selected_1_5']);
				if($ret['num_of_us_students']!=0){
				$Total_US_Students_Select_1_School_All_Schools_percent = round(($ret['allschools_us_students_selected_1_5']/$ret['num_of_us_students'])*100) ;//. '%';
				}
				else{
					$Total_US_Students_Select_1_School_All_Schools_percent = 0;
				}
				if($ret['num_of_intl_students']!=0){
				$Total_Intl_Students_Select_1_School_All_Schools_percent = round(($ret['allschools_intl_students_selected_1_5']/$ret['num_of_intl_students'])*100) ;//. '%';
				}
				else{
					$Total_Intl_Students_Select_1_School_All_Schools_percent = 0;
				}
				if($Total_Students!=0){
				$Total_Students_Select_1_School_All_Schools_percent = round(($Total_Students_Select_1_School_All_Schools/$Total_Students)*100) ;//. '%';
				}
				else{
					$Total_Students_Select_1_School_All_Schools_percent = 0;
				}
			/* Select_1_School_All_Schools Calculation Finish*/

			/* Select_5_School_Monetised Calculation Start */
				$Total_Students_Select_5_School_Monetised = ($ret['monetized_us_students_selected_over_5'] + $ret['monetized_intl_students_selected_over_5']);
				if($ret['num_of_us_students']!=0){
					$Total_US_Students_Select_5_School_Monetised_percent = round(($ret['monetized_us_students_selected_over_5']/$ret['num_of_us_students'])*100) ;//. '%';
					}
					else{
						$Total_US_Students_Select_5_School_Monetised_percent = 0;
					}
				if($ret['num_of_intl_students']!=0){
					$Total_Intl_Students_Select_5_School_Monetised_percent = round(($ret['monetized_intl_students_selected_over_5']/$ret['num_of_intl_students'])*100);//.'%';
				}
				else{
					$Total_Intl_Students_Select_5_School_Monetised_percent = 0;
				}
				if($Total_Students!=0){
					$Total_Students_Select_5_School_Monetised_percent = round(($Total_Students_Select_5_School_Monetised/$Total_Students)*100) ;//. '%';
				}
				else{
					$Total_Students_Select_5_School_Monetised_percent = 0;
				}
			/* Select_5_School_Monetised Calculation Finish*/

			/* Select_5_School_All_Schools Calculation Start */
				$Total_Students_Select_5_School_All_Schools = ($ret['allschools_us_students_selected_over_5'] + $ret['allschools_intl_students_selected_over_5']);
				if($ret['num_of_us_students']!=0){
					$Total_US_Students_Select_5_School_All_Schools_percent = round(($ret['allschools_us_students_selected_over_5']/$ret['num_of_us_students'])*100) ;//. '%';
				}else{
					$Total_US_Students_Select_5_School_All_Schools_percent = 0;
				}

				if($ret['num_of_intl_students']!=0){
					$Total_Intl_Students_Select_5_School_All_Schools_percent = round(($ret['allschools_intl_students_selected_over_5']/$ret['num_of_intl_students'])*100) ;//. '%';
				}else{
					$Total_Intl_Students_Select_5_School_All_Schools_percent = 0;
				}

				if($Total_Students!=0){
					$Total_Students_Select_5_School_All_Schools_percent = round(($Total_Students_Select_5_School_All_Schools/$Total_Students)*100) ;//. '%';
				}else{
					$Total_Students_Select_5_School_All_Schools_percent = 0;
				}

			/* Select_5_School_All_Schools Calculation Finish*/

			/* Upgraded Premium Calculation Start */
				$Total_Students_Premium = ($ret['num_of_us_students_premium'] + $ret['num_of_intl_students_premium']);

				$Total_Students_Premium_Com = ($ret['num_of_us_students_com_premium'] + $ret['num_of_intl_students_com_premium']);
				if($ret['num_of_us_students_premium']){
					$Total_US_Students_Premium_percent = round(($ret['num_of_us_students_com_premium']/$ret['num_of_us_students_premium'])*100) ;//. '%';
				}
				else{
					$Total_US_Students_Premium_percent = 0;
				}
				if($ret['num_of_intl_students_premium']){
					$Total_Intl_Students_Premium_percent = round(($ret['num_of_intl_students_com_premium']/$ret['num_of_intl_students_premium'])*100) ;//. '%';
				}
				else{
					$Total_Intl_Students_Premium_percent = 0;
				}
				if($Total_Students_Premium){
					$Total_Students_Premium_percent = round(($Total_Students_Premium_Com/$Total_Students_Premium)*100) ;//. '%';
				}
				else{
					$Total_Students_Premium_percent = 0;
				}
			/* Upgraded Premium Calculation Finish */

			//================Calculating Values Done=========================//

			//=====================Putting Values in CSV==============================//
			$lineData = array('Total_Students', $Total_Students);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Completed30', $Total_Students_Completed30);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_US_Students_Completed30_percent', $Total_US_Students_Completed30_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Intl_Students_Completed30_percent', $Total_Intl_Students_Completed30_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Completed30_percent', $Total_Students_Completed30_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Select_1_School_Monetised', $Total_Students_Select_1_School_Monetised);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_US_Students_Select_1_School_Monetised_percent', $Total_US_Students_Select_1_School_Monetised_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Intl_Students_Select_1_School_Monetised_percent', $Total_Intl_Students_Select_1_School_Monetised_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Select_1_School_Monetised_percent', $Total_Students_Select_1_School_Monetised_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Select_1_School_All_Schools', $Total_Students_Select_1_School_All_Schools);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_US_Students_Select_1_School_All_Schools_percent', $Total_US_Students_Select_1_School_All_Schools_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Intl_Students_Select_1_School_All_Schools_percent', $Total_Intl_Students_Select_1_School_All_Schools_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Select_1_School_All_Schools_percent', $Total_Students_Select_1_School_All_Schools_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Select_5_School_Monetised', $Total_Students_Select_5_School_Monetised);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_US_Students_Select_5_School_Monetised_percent', $Total_US_Students_Select_5_School_Monetised_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Intl_Students_Select_5_School_Monetised_percent', $Total_Intl_Students_Select_5_School_Monetised_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Select_5_School_Monetised_percent', $Total_Students_Select_5_School_Monetised_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Select_5_School_All_Schools', $Total_Students_Select_5_School_All_Schools);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_US_Students_Select_5_School_All_Schools_percent', $Total_US_Students_Select_5_School_All_Schools_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Intl_Students_Select_5_School_All_Schools_percent', $Total_Intl_Students_Select_5_School_All_Schools_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Select_5_School_All_Schools_percent', $Total_Students_Select_5_School_All_Schools_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Premium', $Total_Students_Premium);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Premium_Com', $Total_Students_Premium_Com);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_US_Students_Premium_percent', $Total_US_Students_Premium_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Intl_Students_Premium_percent', $Total_Intl_Students_Premium_percent);
			fputcsv($f, $lineData,$delimiter);

			$lineData = array('Total_Students_Premium_percent', $Total_Students_Premium_percent);
			fputcsv($f, $lineData,$delimiter);
			//=====================Putting Values in CSV Done==============================//

      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
	}


	public function getUserStats($start_date = NULL, $end_date =NULL){
		// $start_date = "2018-09-03";
		// $end_date   = "2018-09-03";

		// if (!isset($start_date)) {
		// 	$start_date = new Carbon('first day of this month');
		// 	$start_date = $start_date->toDateString();

		// 	$end_date = new Carbon('last day of this month');
		// 	$end_date = $end_date->toDateString();
		// }

		// $start_date = Carbon::now()->today()->toDateString();
		// $end_date   = Carbon::now()->today()->toDateString();

		if (!isset($start_date) && !isset($end_date)) {
			$qry = UserTrackingNumber::on('bk')->where('type', 'users')
											   ->orderBy('date', 'asc')
											   ->first();
			$date = $qry->date. " 00:00:00";

			$dt = Carbon::parse($date);
			$dt = $dt->subDay(1);
			$dt = $dt->toDateString();

			if ($dt == "2015-01-28") {
				return "got to end of the list";
			}

			$start_date = $dt;
			$end_date   = $dt;
		}

		$ret = array();

		////////////////////
		$ret['num_of_us_students']   = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $end_date])
													 ->whereRaw("not exists (select user_id from country_conflicts where user_id = users.id)")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('country_id', 1)
													 ->where('is_plexuss', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_intl_students'] = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $end_date])
													 ->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = users.id))")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('is_plexuss', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_us_students_com']   = DB::connection('bk')->table('users as u')
											 		->join('scores as s', 'u.id', '=', 's.user_id')
											 		->whereBetween(DB::raw('DATE(u.created_at)'), [$start_date, $end_date])
													->where('is_ldy', 0)
													->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													->where('email', 'NOT LIKE', '%test%')
													->where('fname', 'NOT LIKE', '%test%')
													->where('email', 'NOT LIKE', '%nrccua%')
													->where('country_id', 1)
													->where('is_plexuss', 0)

													->whereNotNull('u.address')
													->where(DB::raw('length(u.address)'), '>=', 3)
													->whereNotNull('u.zip')
													->whereIn('u.gender', array('m', 'f'))
													->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
												 	->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_intl_students_com'] = DB::connection('bk')->table('users as u')
											 		->join('scores as s', 'u.id', '=', 's.user_id')
											 		->whereBetween(DB::raw('DATE(u.created_at)'), [$start_date, $end_date])
													->where('is_ldy', 0)
													->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													->where('email', 'NOT LIKE', '%test%')
													->where('fname', 'NOT LIKE', '%test%')
													->where('email', 'NOT LIKE', '%nrccua%')
													->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = u.id))")
													->where('is_plexuss', 0)

													->whereNotNull('u.address')
													->where(DB::raw('length(u.address)'), '>=', 3)
													->whereNotNull('u.zip')
													->whereIn('u.gender', array('m', 'f'))
													->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
													->whereNotNull('u.financial_firstyr_affordibility')
												 	->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['monetized_us_students_selected_1_5'] = DB::connection('bk')
											 ->select("select count(distinct email) as cnt
											  from (
											        Select user_id, email, count(*)
											        from
											                (select user_id, college_id
											                from users u
											                join recruitment r on u.id = r.user_id
											                        and r.user_recruit = 1
											                where date(u.created_at) between '".$start_date."' and '".$end_date."'
											                and country_id = 1

											                UNION

											                select user_id, college_id
											                from users u
											                join pick_a_college_views pacw on u.id = pacw.user_id
											                where date(u.created_at) between '".$start_date."' and '".$end_date."'
											                and country_id = 1

											                UNION

											                select user_id, college_id
											                from users u
											                join ad_clicks ac on ac.user_id = u.id
											                        and `ac`.`utm_source` NOT LIKE '%test%'
											                        and `ac`.`utm_source` NOT LIKE 'email%'
											                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
											                and country_id = 1

											                union

											                select user_id, college_id
											                from users u
											                join users_applied_colleges uac on u.id = uac.user_id
											                where date(u.created_at) between '".$start_date."' and '".$end_date."'
											                and country_id = 1
											        ) tbl1
											        join users u on tbl1.user_id = u.id
											        where not exists (select user_id from country_conflicts where user_id = u.id)
											                and is_ldy = 0
											                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
											                and email not like '%test%'
											                and fname not like '%test'
											                and email not like '%nrccua%'
											                                and if(college_id REGEXP('[0-9]') != 1 or college_id in
											                                        (SELECT DISTINCT
											                                         college_id
											                                         FROM revenue_organizations as ro
											                                         JOIN aor_colleges as ac ON(ro.aor_id = ac.aor_id)
											                                         WHERE ro.active = 1 AND ro.id != 2
											                                         UNION
											                                         SELECT DISTINCT college_id
											                                         FROM distribution_clients
											                                         WHERE ro_id != 2), 1, 0) = 1
											        group by u.id
											        having count(*) between 1 and 4
											  ) tbl2");

		$ret['monetized_us_students_selected_1_5'] = $ret['monetized_us_students_selected_1_5'][0]->cnt;

		$ret['monetized_intl_students_selected_1_5'] = DB::connection('bk')
											   ->select("select count(distinct email) as cnt
												from (
												        Select user_id, email, count(*)
												        from
												                (select user_id, college_id
												                from users u
												                join recruitment r on u.id = r.user_id
												                        and r.user_recruit = 1
												                where date(u.created_at) between '".$start_date."' and '".$end_date."'
												                and country_id != 1

												                UNION

												                select user_id, college_id
												                from users u
												                join pick_a_college_views pacw on u.id = pacw.user_id
												                where date(u.created_at) between '".$start_date."' and '".$end_date."'
												                and country_id != 1

												                UNION

												                select user_id, college_id
												                from users u
												                join ad_clicks ac on ac.user_id = u.id
												                        and `ac`.`utm_source` NOT LIKE '%test%'
												                        and `ac`.`utm_source` NOT LIKE 'email%'
												                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
												                and country_id != 1

												                union

												                select user_id, college_id
												                from users u
												                join users_applied_colleges uac on u.id = uac.user_id
												                where date(u.created_at) between '".$start_date."' and '".$end_date."'
												                and country_id != 1
												        ) tbl1
												        join users u on tbl1.user_id = u.id
												        where not exists (select user_id from country_conflicts where user_id = u.id)
												                and is_ldy = 0
												                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
												                and email not like '%test%'
												                and fname not like '%test'
												                and email not like '%nrccua%'
												                                and if(college_id REGEXP('[0-9]') != 1 or college_id in
												                                        (SELECT DISTINCT
												                                         college_id
												                                         FROM revenue_organizations as ro
												                                         JOIN aor_colleges as ac ON(ro.aor_id = ac.aor_id)
												                                         WHERE ro.active = 1 AND ro.id != 2
												                                         UNION
												                                         SELECT DISTINCT college_id
												                                         FROM distribution_clients
												                                         WHERE ro_id != 2), 1, 0) = 1
												        group by u.id
												        having count(*) between 1 and 4
												) tbl2
												");

		$ret['monetized_intl_students_selected_1_5'] = $ret['monetized_intl_students_selected_1_5'][0]->cnt;

		$ret['monetized_us_students_selected_over_5'] = DB::connection('bk')
												->select("select count(distinct email) as cnt
													from (
													        Select user_id, email, count(*)
													        from
													                (select user_id, college_id
													                from users u
													                join recruitment r on u.id = r.user_id
													                        and r.user_recruit = 1
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id = 1

													                UNION

													                select user_id, college_id
													                from users u
													                join pick_a_college_views pacw on u.id = pacw.user_id
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id = 1

													                UNION

													                select user_id, college_id
													                from users u
													                join ad_clicks ac on ac.user_id = u.id
													                        and `ac`.`utm_source` NOT LIKE '%test%'
													                        and `ac`.`utm_source` NOT LIKE 'email%'
													                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id = 1

													                union

													                select user_id, college_id
													                from users u
													                join users_applied_colleges uac on u.id = uac.user_id
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id = 1
													        ) tbl1
													        join users u on tbl1.user_id = u.id
													        where not exists (select user_id from country_conflicts where user_id = u.id)
													                and is_ldy = 0
													                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
													                and email not like '%test%'
													                and fname not like '%test'
													                and email not like '%nrccua%'
													                                and if(college_id REGEXP('[0-9]') != 1 or college_id in
													                                        (SELECT DISTINCT
													                                         college_id
													                                         FROM revenue_organizations as ro
													                                         JOIN aor_colleges as ac ON(ro.aor_id = ac.aor_id)
													                                         WHERE ro.active = 1 AND ro.id != 2
													                                         UNION
													                                         SELECT DISTINCT college_id
													                                         FROM distribution_clients
													                                         WHERE ro_id != 2), 1, 0) = 1
													        group by u.id
													        having count(*) >= 5
													) tbl2
													");

		$ret['monetized_us_students_selected_over_5'] = $ret['monetized_us_students_selected_over_5'][0]->cnt;

		$ret['monetized_intl_students_selected_over_5'] = DB::connection('bk')
												  ->select("select count(distinct email) as cnt
													from (
													        Select user_id, email, count(*)
													        from
													                (select user_id, college_id
													                from users u
													                join recruitment r on u.id = r.user_id
													                        and r.user_recruit = 1
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id != 1

													                UNION

													                select user_id, college_id
													                from users u
													                join pick_a_college_views pacw on u.id = pacw.user_id
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id != 1

													                UNION

													                select user_id, college_id
													                from users u
													                join ad_clicks ac on ac.user_id = u.id
													                        and `ac`.`utm_source` NOT LIKE '%test%'
													                        and `ac`.`utm_source` NOT LIKE 'email%'
													                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id != 1

													                union

													                select user_id, college_id
													                from users u
													                join users_applied_colleges uac on u.id = uac.user_id
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id != 1
													        ) tbl1
													        join users u on tbl1.user_id = u.id
													        where is_ldy = 0
													                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
													                and email not like '%test%'
													                and fname not like '%test'
													                and email not like '%nrccua%'
																	and if(college_id REGEXP('[0-9]') != 1 or college_id in
																		(SELECT DISTINCT
																		 college_id
																		 FROM revenue_organizations as ro
																		 JOIN aor_colleges as ac ON(ro.aor_id = ac.aor_id)
																		 WHERE ro.active = 1 AND ro.id != 2
																		 UNION
																		 SELECT DISTINCT college_id
																		 FROM distribution_clients
																		 WHERE ro_id != 2), 1, 0) = 1
													        group by u.id
													        having count(*) >= 5
													) tbl2
													");

		$ret['monetized_intl_students_selected_over_5'] = $ret['monetized_intl_students_selected_over_5'][0]->cnt;

		///////////////////////////////////////////////////////////////////////////////////////

		$ret['allschools_us_students_selected_1_5'] = DB::connection('bk')
														->select("select count(distinct email) as cnt
															from (
															        Select user_id, email, count(*)
															        from
															                (select user_id, college_id
															                from users u
															                join recruitment r on u.id = r.user_id
															                        and r.user_recruit = 1
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                UNION

															                select user_id, college_id
															                from users u
															                join pick_a_college_views pacw on u.id = pacw.user_id
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                UNION

															                select user_id, college_id
															                from users u
															                join ad_clicks ac on ac.user_id = u.id
															                        and `ac`.`utm_source` NOT LIKE '%test%'
															                        and `ac`.`utm_source` NOT LIKE 'email%'
															                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                union

															                select user_id, college_id
															                from users u
															                join users_applied_colleges uac on u.id = uac.user_id
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1
															        ) tbl1
															        join users u on tbl1.user_id = u.id
															        where not exists (select user_id from country_conflicts where user_id = u.id)
															                and is_ldy = 0
															                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
															                and email not like '%test%'
															                and fname not like '%test'
															                and email not like '%nrccua%'
															        group by u.id
															        having count(*) between 1 and 4
															) tbl2
															");

		$ret['allschools_us_students_selected_1_5'] = $ret['allschools_us_students_selected_1_5'][0]->cnt;
		$ret['allschools_intl_students_selected_1_5'] = DB::connection('bk')
														  ->select("select count(distinct email) as cnt
															from (
																Select user_id, email, count(*)
																from
																	(select user_id, college_id
																	from users u
																	join recruitment r on u.id = r.user_id
																		and r.user_recruit = 1
																	where date(u.created_at) between '".$start_date."' and '".$end_date."'
																	and country_id != 1

																	UNION

																	select user_id, college_id
																	from users u
																	join pick_a_college_views pacw on u.id = pacw.user_id
																	where date(u.created_at) between '".$start_date."' and '".$end_date."'
																	and country_id != 1

																	UNION

																	select user_id, college_id
																	from users u
																	join ad_clicks ac on ac.user_id = u.id
																		and `ac`.`utm_source` NOT LIKE '%test%'
																		and `ac`.`utm_source` NOT LIKE 'email%'
																		and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
																	and country_id != 1

																	union

																	select user_id, college_id
																	from users u
																	join users_applied_colleges uac on u.id = uac.user_id
																	where date(u.created_at) between '".$start_date."' and '".$end_date."'
																	and country_id != 1
																) tbl1
																join users u on tbl1.user_id = u.id
																where is_ldy = 0
																	and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
																	and email not like '%test%'
																	and fname not like '%test'
																	and email not like '%nrccua%'
																group by u.id
																having count(*) between 1 and 4
															) tbl2
															");

		$ret['allschools_intl_students_selected_1_5'] = $ret['allschools_intl_students_selected_1_5'][0]->cnt;

		$ret['allschools_us_students_selected_over_5'] = DB::connection('bk')
														   ->select("select count(distinct email) as cnt
															from (
															        Select user_id, email, count(*)
															        from
															                (select user_id, college_id
															                from users u
															                join recruitment r on u.id = r.user_id
															                        and r.user_recruit = 1
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                UNION

															                select user_id, college_id
															                from users u
															                join pick_a_college_views pacw on u.id = pacw.user_id
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                UNION

															                select user_id, college_id
															                from users u
															                join ad_clicks ac on ac.user_id = u.id
															                        and `ac`.`utm_source` NOT LIKE '%test%'
															                        and `ac`.`utm_source` NOT LIKE 'email%'
															                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                union

															                select user_id, college_id
															                from users u
															                join users_applied_colleges uac on u.id = uac.user_id
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1
															        ) tbl1
															        join users u on tbl1.user_id = u.id
															        where not exists (select user_id from country_conflicts where user_id = u.id)
															                and is_ldy = 0
															                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
															                and email not like '%test%'
															                and fname not like '%test'
															                and email not like '%nrccua%'
															        group by u.id
															        having count(*) >= 5
															) tbl2
															");

		$ret['allschools_us_students_selected_over_5'] = $ret['allschools_us_students_selected_over_5'][0]->cnt;

		$ret['allschools_intl_students_selected_over_5'] = DB::connection('bk')
															 ->select("select count(distinct email) as cnt
																from (
																	Select user_id, email, count(*)
																	from
																		(select user_id, college_id
																		from users u
																		join recruitment r on u.id = r.user_id
																			and r.user_recruit = 1
																		where date(u.created_at) between '".$start_date."' and '".$end_date."'
																		and country_id != 1

																		UNION

																		select user_id, college_id
																		from users u
																		join pick_a_college_views pacw on u.id = pacw.user_id
																		where date(u.created_at) between '".$start_date."' and '".$end_date."'
																		and country_id != 1

																		UNION

																		select user_id, college_id
																		from users u
																		join ad_clicks ac on ac.user_id = u.id
																			and `ac`.`utm_source` NOT LIKE '%test%'
																			and `ac`.`utm_source` NOT LIKE 'email%'
																			and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
																		and country_id != 1

																		union

																		select user_id, college_id
																		from users u
																		join users_applied_colleges uac on u.id = uac.user_id
																		where date(u.created_at) between '".$start_date."' and '".$end_date."'
																		and country_id != 1
																	) tbl1
																	join users u on tbl1.user_id = u.id
																	where is_ldy = 0
																		and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
																		and email not like '%test%'
																		and fname not like '%test'
																		and email not like '%nrccua%'
																	group by u.id
																	having count(*) >= 5
																) tbl2
																");

		$ret['allschools_intl_students_selected_over_5'] = $ret['allschools_intl_students_selected_over_5'][0]->cnt;

		///////////////////////////////////////////////////////////////////////////////////////

		$ret['num_of_us_students_premium']   = User::on('bk')
													 ->join('premium_users as pu', 'pu.user_id', '=', 'users.id')
													 ->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $end_date])
													 ->whereRaw("not exists (select user_id from country_conflicts where user_id = users.id)")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('country_id', 1)
													 ->where('is_plexuss', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_intl_students_premium'] = User::on('bk')
													 ->join('premium_users as pu', 'pu.user_id', '=', 'users.id')
													 ->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $end_date])
													 ->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = users.id))")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('is_plexuss', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_us_students_com_premium']   = DB::connection('bk')->table('users as u')
													->join('premium_users as pu', 'pu.user_id', '=', 'u.id')
											 		->join('scores as s', 'u.id', '=', 's.user_id')
											 		->whereBetween(DB::raw('DATE(u.created_at)'), [$start_date, $end_date])
													->where('is_ldy', 0)
													->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													->where('email', 'NOT LIKE', '%test%')
													->where('fname', 'NOT LIKE', '%test%')
													->where('email', 'NOT LIKE', '%nrccua%')
													->where('country_id', 1)
													->where('is_plexuss', 0)

													->whereNotNull('u.address')
													->where(DB::raw('length(u.address)'), '>=', 3)
													->whereNotNull('u.zip')
													->whereIn('u.gender', array('m', 'f'))
													->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
												 	->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_intl_students_com_premium'] = DB::connection('bk')->table('users as u')
											 		->join('premium_users as pu', 'pu.user_id', '=', 'u.id')
											 		->join('scores as s', 'u.id', '=', 's.user_id')
											 		->whereBetween(DB::raw('DATE(u.created_at)'), [$start_date, $end_date])
													->where('is_ldy', 0)
													->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													->where('email', 'NOT LIKE', '%test%')
													->where('fname', 'NOT LIKE', '%test%')
													->where('email', 'NOT LIKE', '%nrccua%')
													->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = u.id))")
													->where('is_plexuss', 0)

													->whereNotNull('u.address')
													->where(DB::raw('length(u.address)'), '>=', 3)
													->whereNotNull('u.zip')
													->whereIn('u.gender', array('m', 'f'))
													->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
													->whereNotNull('u.financial_firstyr_affordibility')
												 	->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;


		$ret['date'] = $start_date;
		$ret['type'] = 'users';

		$attr['date'] = $start_date;
		$attr['type'] = 'users';

		UserTrackingNumber::updateOrCreate($attr, $ret);
		// dd($ret);
		return $ret;
	}

	public function getUserInviteStats($start_date = NULL, $end_date =NULL){
		// $start_date = "2018-09-03";
		// $end_date   = "2018-09-03";

		// if (!isset($start_date)) {
		// 	$start_date = new Carbon('first day of this month');
		// 	$start_date = $start_date->toDateString();

		// 	$end_date = new Carbon('last day of this month');
		// 	$end_date = $end_date->toDateString();
		// }

		// $start_date = Carbon::now()->today()->toDateString();
		// $end_date   = Carbon::now()->today()->toDateString();
		if (!isset($start_date) && !isset($end_date)) {
			$qry = UserTrackingNumber::on('bk')->where('type', 'user_invites')
											   ->orderBy('date', 'asc')
											   ->first();
			$date = $qry->date. " 00:00:00";

			$dt = Carbon::parse($date);
			$dt = $dt->subDay(1);
			$dt = $dt->toDateString();

			if ($dt == "2015-01-28") {
				return "got to end of the list";
			}

			$start_date = $dt;
			$end_date   = $dt;
		}
		$ret = array();

		////////////////////
		$ret['num_of_us_students']   = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $end_date])
													 ->join('users_invites as ui', function($q){
													 		$q->on('users.email', '=', 'ui.invite_email')
													 		  ->on('ui.sent', '=', DB::raw(1))
													 		  ->on('ui.is_dup', '=', DB::raw(0));
													 })
													 ->whereRaw("not exists (select user_id from country_conflicts where user_id = users.id)")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('country_id', 1)
													 ->where('is_plexuss', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_intl_students'] = User::on('bk')->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $end_date])
													 ->join('users_invites as ui', function($q){
													 		$q->on('users.email', '=', 'ui.invite_email')
													 		  ->on('ui.sent', '=', DB::raw(1))
													 		  ->on('ui.is_dup', '=', DB::raw(0));
													 })
													 ->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = users.id))")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('is_plexuss', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_us_students_com']   = DB::connection('bk')->table('users as u')
													->join('users_invites as ui', function($q){
													 		$q->on('u.email', '=', 'ui.invite_email')
													 		  ->on('ui.sent', '=', DB::raw(1))
													 		  ->on('ui.is_dup', '=', DB::raw(0));
													 })
											 		->join('scores as s', 'u.id', '=', 's.user_id')
											 		->whereBetween(DB::raw('DATE(u.created_at)'), [$start_date, $end_date])
													->where('is_ldy', 0)
													->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													->where('email', 'NOT LIKE', '%test%')
													->where('fname', 'NOT LIKE', '%test%')
													->where('email', 'NOT LIKE', '%nrccua%')
													->where('country_id', 1)
													->where('is_plexuss', 0)

													->whereNotNull('u.address')
													->where(DB::raw('length(u.address)'), '>=', 3)
													->whereNotNull('u.zip')
													->whereIn('u.gender', array('m', 'f'))
													->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
												 	->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_intl_students_com'] = DB::connection('bk')->table('users as u')
													->join('users_invites as ui', function($q){
													 		$q->on('u.email', '=', 'ui.invite_email')
													 		  ->on('ui.sent', '=', DB::raw(1))
													 		  ->on('ui.is_dup', '=', DB::raw(0));
													 })
											 		->join('scores as s', 'u.id', '=', 's.user_id')
											 		->whereBetween(DB::raw('DATE(u.created_at)'), [$start_date, $end_date])
													->where('is_ldy', 0)
													->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													->where('email', 'NOT LIKE', '%test%')
													->where('fname', 'NOT LIKE', '%test%')
													->where('email', 'NOT LIKE', '%nrccua%')
													->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = u.id))")
													->where('is_plexuss', 0)

													->whereNotNull('u.address')
													->where(DB::raw('length(u.address)'), '>=', 3)
													->whereNotNull('u.zip')
													->whereIn('u.gender', array('m', 'f'))
													->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
													->whereNotNull('u.financial_firstyr_affordibility')
												 	->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['monetized_us_students_selected_1_5'] = DB::connection('bk')
											 ->select("select count(distinct email) as cnt
											  from (
											        Select user_id, email, count(*)
											        from
											                (select u.id as user_id, college_id
											                from users u
											                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
											                join recruitment r on u.id = r.user_id
											                        and r.user_recruit = 1
											                where date(u.created_at) between '".$start_date."' and '".$end_date."'
											                and country_id = 1

											                UNION

											                select u.id as user_id, college_id
											                from users u
											                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
											                join pick_a_college_views pacw on u.id = pacw.user_id
											                where date(u.created_at) between '".$start_date."' and '".$end_date."'
											                and country_id = 1

											                UNION

											                select u.id as user_id, college_id
											                from users u
											                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
											                join ad_clicks ac on ac.user_id = u.id
											                        and `ac`.`utm_source` NOT LIKE '%test%'
											                        and `ac`.`utm_source` NOT LIKE 'email%'
											                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
											                and country_id = 1

											                union

											                select u.id as user_id, college_id
											                from users u
											                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
											                join users_applied_colleges uac on u.id = uac.user_id
											                where date(u.created_at) between '".$start_date."' and '".$end_date."'
											                and country_id = 1
											        ) tbl1
											        join users u on tbl1.user_id = u.id
											        where not exists (select user_id from country_conflicts where user_id = u.id)
											                and is_ldy = 0
											                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
											                and email not like '%test%'
											                and fname not like '%test'
											                and email not like '%nrccua%'
											                                and if(college_id REGEXP('[0-9]') != 1 or college_id in
											                                        (SELECT DISTINCT
											                                         college_id
											                                         FROM revenue_organizations as ro
											                                         JOIN aor_colleges as ac ON(ro.aor_id = ac.aor_id)
											                                         WHERE ro.active = 1 AND ro.id != 2
											                                         UNION
											                                         SELECT DISTINCT college_id
											                                         FROM distribution_clients
											                                         WHERE ro_id != 2), 1, 0) = 1
											        group by u.id
											        having count(*) between 1 and 4
											  ) tbl2");

		$ret['monetized_us_students_selected_1_5'] = $ret['monetized_us_students_selected_1_5'][0]->cnt;

		$ret['monetized_intl_students_selected_1_5'] = DB::connection('bk')
											   ->select("select count(distinct email) as cnt
												from (
												        Select user_id, email, count(*)
												        from
												                (select u.id as user_id, college_id
												                from users u
												                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
												                join recruitment r on u.id = r.user_id
												                        and r.user_recruit = 1
												                where date(u.created_at) between '".$start_date."' and '".$end_date."'
												                and country_id = 1

												                UNION

												                select u.id as user_id, college_id
												                from users u
												                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
												                join pick_a_college_views pacw on u.id = pacw.user_id
												                where date(u.created_at) between '".$start_date."' and '".$end_date."'
												                and country_id = 1

												                UNION

												                select u.id as user_id, college_id
												                from users u
												                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
												                join ad_clicks ac on ac.user_id = u.id
												                        and `ac`.`utm_source` NOT LIKE '%test%'
												                        and `ac`.`utm_source` NOT LIKE 'email%'
												                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
												                and country_id = 1

												                union

												                select u.id as user_id, college_id
												                from users u
												                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
												                join users_applied_colleges uac on u.id = uac.user_id
												                where date(u.created_at) between '".$start_date."' and '".$end_date."'
												                and country_id = 1
												        ) tbl1
												        join users u on tbl1.user_id = u.id
												        where not exists (select user_id from country_conflicts where user_id = u.id)
												                and is_ldy = 0
												                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
												                and email not like '%test%'
												                and fname not like '%test'
												                and email not like '%nrccua%'
												                                and if(college_id REGEXP('[0-9]') != 1 or college_id in
												                                        (SELECT DISTINCT
												                                         college_id
												                                         FROM revenue_organizations as ro
												                                         JOIN aor_colleges as ac ON(ro.aor_id = ac.aor_id)
												                                         WHERE ro.active = 1 AND ro.id != 2
												                                         UNION
												                                         SELECT DISTINCT college_id
												                                         FROM distribution_clients
												                                         WHERE ro_id != 2), 1, 0) = 1
												        group by u.id
												        having count(*) between 1 and 4
												) tbl2
												");

		$ret['monetized_intl_students_selected_1_5'] = $ret['monetized_intl_students_selected_1_5'][0]->cnt;

		$ret['monetized_us_students_selected_over_5'] = DB::connection('bk')
												->select("select count(distinct email) as cnt
													from (
													        Select user_id, email, count(*)
													        from
													                (select u.id as user_id, college_id
													                from users u
													                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
													                join recruitment r on u.id = r.user_id
													                        and r.user_recruit = 1
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id = 1

													                UNION

													                select u.id as user_id, college_id
													                from users u
													                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
													                join pick_a_college_views pacw on u.id = pacw.user_id
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id = 1

													                UNION

													                select u.id as user_id, college_id
													                from users u
													                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
													                join ad_clicks ac on ac.user_id = u.id
													                        and `ac`.`utm_source` NOT LIKE '%test%'
													                        and `ac`.`utm_source` NOT LIKE 'email%'
													                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id = 1

													                union

													                select u.id as user_id, college_id
													                from users u
													                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
													                join users_applied_colleges uac on u.id = uac.user_id
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id = 1
													        ) tbl1
													        join users u on tbl1.user_id = u.id
													        where not exists (select user_id from country_conflicts where user_id = u.id)
													                and is_ldy = 0
													                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
													                and email not like '%test%'
													                and fname not like '%test'
													                and email not like '%nrccua%'
													                                and if(college_id REGEXP('[0-9]') != 1 or college_id in
													                                        (SELECT DISTINCT
													                                         college_id
													                                         FROM revenue_organizations as ro
													                                         JOIN aor_colleges as ac ON(ro.aor_id = ac.aor_id)
													                                         WHERE ro.active = 1 AND ro.id != 2
													                                         UNION
													                                         SELECT DISTINCT college_id
													                                         FROM distribution_clients
													                                         WHERE ro_id != 2), 1, 0) = 1
													        group by u.id
													        having count(*) >= 5
													) tbl2
													");

		$ret['monetized_us_students_selected_over_5'] = $ret['monetized_us_students_selected_over_5'][0]->cnt;

		$ret['monetized_intl_students_selected_over_5'] = DB::connection('bk')
												  ->select("select count(distinct email) as cnt
													from (
													        Select user_id, email, count(*)
													        from
													                (select u.id as user_id, college_id
													                from users u
													                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
													                join recruitment r on u.id = r.user_id
													                        and r.user_recruit = 1
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id != 1

													                UNION

													                select u.id as user_id, college_id
													                from users u
													                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
													                join pick_a_college_views pacw on u.id = pacw.user_id
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id != 1

													                UNION

													                select u.id as user_id, college_id
													                from users u
													                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
													                join ad_clicks ac on ac.user_id = u.id
													                        and `ac`.`utm_source` NOT LIKE '%test%'
													                        and `ac`.`utm_source` NOT LIKE 'email%'
													                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id != 1

													                union

													                select u.id as user_id, college_id
													                from users u
													                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
													                join users_applied_colleges uac on u.id = uac.user_id
													                where date(u.created_at) between '".$start_date."' and '".$end_date."'
													                and country_id != 1
													        ) tbl1
													        join users u on tbl1.user_id = u.id
													        where is_ldy = 0
													                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
													                and email not like '%test%'
													                and fname not like '%test'
													                and email not like '%nrccua%'
																	and if(college_id REGEXP('[0-9]') != 1 or college_id in
																		(SELECT DISTINCT
																		 college_id
																		 FROM revenue_organizations as ro
																		 JOIN aor_colleges as ac ON(ro.aor_id = ac.aor_id)
																		 WHERE ro.active = 1 AND ro.id != 2
																		 UNION
																		 SELECT DISTINCT college_id
																		 FROM distribution_clients
																		 WHERE ro_id != 2), 1, 0) = 1
													        group by u.id
													        having count(*) >= 5
													) tbl2
													");

		$ret['monetized_intl_students_selected_over_5'] = $ret['monetized_intl_students_selected_over_5'][0]->cnt;

		///////////////////////////////////////////////////////////////////////////////////////

		$ret['allschools_us_students_selected_1_5'] = DB::connection('bk')
														->select("select count(distinct email) as cnt
															from (
															        Select user_id, email, count(*)
															        from
															                (select u.id as user_id, college_id
															                from users u
															                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
															                join recruitment r on u.id = r.user_id
															                        and r.user_recruit = 1
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                UNION

															                select u.id as user_id, college_id
															                from users u
															                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
															                join pick_a_college_views pacw on u.id = pacw.user_id
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                UNION

															                select u.id as user_id, college_id
															                from users u
															                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
															                join ad_clicks ac on ac.user_id = u.id
															                        and `ac`.`utm_source` NOT LIKE '%test%'
															                        and `ac`.`utm_source` NOT LIKE 'email%'
															                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                union

															                select u.id as user_id, college_id
															                from users u
															                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
															                join users_applied_colleges uac on u.id = uac.user_id
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1
															        ) tbl1
															        join users u on tbl1.user_id = u.id
															        where not exists (select user_id from country_conflicts where user_id = u.id)
															                and is_ldy = 0
															                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
															                and email not like '%test%'
															                and fname not like '%test'
															                and email not like '%nrccua%'
															        group by u.id
															        having count(*) between 1 and 4
															) tbl2
															");

		$ret['allschools_us_students_selected_1_5'] = $ret['allschools_us_students_selected_1_5'][0]->cnt;
		$ret['allschools_intl_students_selected_1_5'] = DB::connection('bk')
														  ->select("select count(distinct email) as cnt
															from (
																Select user_id, email, count(*)
																from
																	(select u.id as user_id, college_id
																	from users u
																	JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
																	join recruitment r on u.id = r.user_id
																		and r.user_recruit = 1
																	where date(u.created_at) between '".$start_date."' and '".$end_date."'
																	and country_id != 1

																	UNION

																	select u.id as user_id, college_id
																	from users u
																	JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
																	join pick_a_college_views pacw on u.id = pacw.user_id
																	where date(u.created_at) between '".$start_date."' and '".$end_date."'
																	and country_id != 1

																	UNION

																	select u.id as user_id, college_id
																	from users u
																	JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
																	join ad_clicks ac on ac.user_id = u.id
																		and `ac`.`utm_source` NOT LIKE '%test%'
																		and `ac`.`utm_source` NOT LIKE 'email%'
																		and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
																	and country_id != 1

																	union

																	select u.id as user_id, college_id
																	from users u
																	JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
																	join users_applied_colleges uac on u.id = uac.user_id
																	where date(u.created_at) between '".$start_date."' and '".$end_date."'
																	and country_id != 1
																) tbl1
																join users u on tbl1.user_id = u.id
																where is_ldy = 0
																	and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
																	and email not like '%test%'
																	and fname not like '%test'
																	and email not like '%nrccua%'
																group by u.id
																having count(*) between 1 and 4
															) tbl2
															");

		$ret['allschools_intl_students_selected_1_5'] = $ret['allschools_intl_students_selected_1_5'][0]->cnt;

		$ret['allschools_us_students_selected_over_5'] = DB::connection('bk')
														   ->select("select count(distinct email) as cnt
															from (
															        Select user_id, email, count(*)
															        from
															                (select u.id as user_id, college_id
															                from users u
															                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
															                join recruitment r on u.id = r.user_id
															                        and r.user_recruit = 1
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                UNION

															                select u.id as user_id, college_id
															                from users u
															                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
															                join pick_a_college_views pacw on u.id = pacw.user_id
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                UNION

															                select u.id as user_id, college_id
															                from users u
															                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
															                join ad_clicks ac on ac.user_id = u.id
															                        and `ac`.`utm_source` NOT LIKE '%test%'
															                        and `ac`.`utm_source` NOT LIKE 'email%'
															                        and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1

															                union

															                select u.id as user_id, college_id
															                from users u
															                JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
															                join users_applied_colleges uac on u.id = uac.user_id
															                where date(u.created_at) between '".$start_date."' and '".$end_date."'
															                and country_id = 1
															        ) tbl1
															        join users u on tbl1.user_id = u.id
															        where not exists (select user_id from country_conflicts where user_id = u.id)
															                and is_ldy = 0
															                and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
															                and email not like '%test%'
															                and fname not like '%test'
															                and email not like '%nrccua%'
															        group by u.id
															        having count(*) >= 5
															) tbl2
															");

		$ret['allschools_us_students_selected_over_5'] = $ret['allschools_us_students_selected_over_5'][0]->cnt;

		$ret['allschools_intl_students_selected_over_5'] = DB::connection('bk')
															 ->select("select count(distinct email) as cnt
																from (
																	Select user_id, email, count(*)
																	from
																		(select u.id as user_id, college_id
																		from users u
																		JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
																		join recruitment r on u.id = r.user_id
																			and r.user_recruit = 1
																		where date(u.created_at) between '".$start_date."' and '".$end_date."'
																		and country_id != 1

																		UNION

																		select u.id as user_id, college_id
																		from users u
																		JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
																		join pick_a_college_views pacw on u.id = pacw.user_id
																		where date(u.created_at) between '".$start_date."' and '".$end_date."'
																		and country_id != 1

																		UNION

																		select u.id as user_id, college_id
																		from users u
																		JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
																		join ad_clicks ac on ac.user_id = u.id
																			and `ac`.`utm_source` NOT LIKE '%test%'
																			and `ac`.`utm_source` NOT LIKE 'email%'
																			and (pixel_tracked = 1 or paid_client = 1)where date(u.created_at) between '".$start_date."' and '".$end_date."'
																		and country_id != 1

																		union

																		select u.id as user_id, college_id
																		from users u
																		JOIN users_invites as ui ON (u.email = ui.invite_email and ui.sent = 1 and ui.is_dup = 0)
																		join users_applied_colleges uac on u.id = uac.user_id
																		where date(u.created_at) between '".$start_date."' and '".$end_date."'
																		and country_id != 1
																	) tbl1
																	join users u on tbl1.user_id = u.id
																	where is_ldy = 0
																		and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
																		and email not like '%test%'
																		and fname not like '%test'
																		and email not like '%nrccua%'
																	group by u.id
																	having count(*) >= 5
																) tbl2
																");

		$ret['allschools_intl_students_selected_over_5'] = $ret['allschools_intl_students_selected_over_5'][0]->cnt;

		///////////////////////////////////////////////////////////////////////////////////////

		$ret['num_of_us_students_premium']   = User::on('bk')
													 ->join('premium_users as pu', 'pu.user_id', '=', 'users.id')
													 ->join('users_invites as ui', function($q){
													 		$q->on('users.email', '=', 'ui.invite_email')
													 		  ->on('ui.sent', '=', DB::raw(1))
													 		  ->on('ui.is_dup', '=', DB::raw(0));
													 })
													 ->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $end_date])
													 ->whereRaw("not exists (select user_id from country_conflicts where user_id = users.id)")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('country_id', 1)
													 ->where('is_plexuss', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_intl_students_premium'] = User::on('bk')
													 ->join('users_invites as ui', function($q){
													 		$q->on('users.email', '=', 'ui.invite_email')
													 		  ->on('ui.sent', '=', DB::raw(1))
													 		  ->on('ui.is_dup', '=', DB::raw(0));
													 })
													 ->join('premium_users as pu', 'pu.user_id', '=', 'users.id')
													 ->whereBetween(DB::raw('DATE(users.created_at)'), [$start_date, $end_date])
													 ->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = users.id))")
													 ->where('is_ldy', 0)
													 ->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													 ->where('email', 'NOT LIKE', '%test%')
													 ->where('fname', 'NOT LIKE', '%test%')
													 ->where('email', 'NOT LIKE', '%nrccua%')
													 ->where('is_plexuss', 0)
											 		 ->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_us_students_com_premium']   = DB::connection('bk')->table('users as u')
													->join('users_invites as ui', function($q){
													 		$q->on('u.email', '=', 'ui.invite_email')
													 		  ->on('ui.sent', '=', DB::raw(1))
													 		  ->on('ui.is_dup', '=', DB::raw(0));
													 })
													->join('premium_users as pu', 'pu.user_id', '=', 'u.id')
											 		->join('scores as s', 'u.id', '=', 's.user_id')
											 		->whereBetween(DB::raw('DATE(u.created_at)'), [$start_date, $end_date])
													->where('is_ldy', 0)
													->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													->where('email', 'NOT LIKE', '%test%')
													->where('fname', 'NOT LIKE', '%test%')
													->where('email', 'NOT LIKE', '%nrccua%')
													->where('country_id', 1)
													->where('is_plexuss', 0)

													->whereNotNull('u.address')
													->where(DB::raw('length(u.address)'), '>=', 3)
													->whereNotNull('u.zip')
													->whereIn('u.gender', array('m', 'f'))
													->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
												 	->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;

		$ret['num_of_intl_students_com_premium'] = DB::connection('bk')->table('users as u')
													->join('users_invites as ui', function($q){
													 		$q->on('u.email', '=', 'ui.invite_email')
													 		  ->on('ui.sent', '=', DB::raw(1))
													 		  ->on('ui.is_dup', '=', DB::raw(0));
													 })
											 		->join('premium_users as pu', 'pu.user_id', '=', 'u.id')
											 		->join('scores as s', 'u.id', '=', 's.user_id')
											 		->whereBetween(DB::raw('DATE(u.created_at)'), [$start_date, $end_date])
													->where('is_ldy', 0)
													->where('email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
													->where('email', 'NOT LIKE', '%test%')
													->where('fname', 'NOT LIKE', '%test%')
													->where('email', 'NOT LIKE', '%nrccua%')
													->whereRaw("((country_id != 1 or country_id is null) or country_id = 1 and exists (select user_id from country_conflicts where user_id = u.id))")
													->where('is_plexuss', 0)

													->whereNotNull('u.address')
													->where(DB::raw('length(u.address)'), '>=', 3)
													->whereNotNull('u.zip')
													->whereIn('u.gender', array('m', 'f'))
													->whereRaw('coalesce(s.hs_gpa, s.overall_gpa, s.weighted_gpa) is not null')
													->whereNotNull('u.financial_firstyr_affordibility')
												 	->select(DB::raw("count(DISTINCT email) as cnt"))->first()->cnt;


		$ret['date'] = $start_date;
		$ret['type'] = 'user_invites';

		$attr['date'] = $start_date;
		$attr['type'] = 'user_invites';

		UserTrackingNumber::updateOrCreate($attr, $ret);
		// dd($ret);
		return $ret;
	}

	//get application goal data
	private function getGoals( $goal, $period ){
		$now = Carbon::now();
		$startOfYear = $now->copy()->startOfYear();
		$thisYearSoFar = $now->diffInMonths($startOfYear) + 1;
		$goals = array();

		$thisYearSoFar = $thisYearSoFar == 0 ? 1 : $thisYearSoFar;

		foreach ($period as $key => $value) {
			switch( $value ){
				case 'monthly':
					$goals[$value] = ceil(($goal *10) / 12);
					break;
				case 'quarterly':
					$goals[$value] = ceil($goal / 4);
					break;
				case 'annually':
					$goals[$value] = ceil($goal / $thisYearSoFar);
					break;
			}
		}

		return $goals;
	}


	private function getDeltaColor( $delta ){
		foreach ($delta as $key => $value) {
			$tmp = $key.'_color';
			$delta[$tmp] = $value >= 0 ? 'positive' : 'negative';
		}

		return $delta;
	}


	private function getGoalPercentageAndDelta( $arr, $period ){
		$both = array();
		$perc = array();
		$delta = array();

		foreach ($period as $key => $value) {
			switch ($value) {
				case 'monthly':
					$perc[$value] = $arr['goal'][$value] > 0 ? $arr['progress'] / $arr['goal'][$value] : 0;
					$delta[$value] = $arr['progress'] - $arr['goal'][$value];
					break;
				case 'quarterly':
					$perc[$value] = $arr['goal'][$value] > 0 ? $arr['progress'] / $arr['goal'][$value] : 0;
					$delta[$value] = $arr['progress'] - $arr['goal'][$value];
					break;
				case 'annually':
					$perc[$value] = $arr['goal'][$value] > 0 ? $arr['progress'] / $arr['goal'][$value] : 0;
					$delta[$value] = $arr['progress'] - $arr['goal'][$value];
					break;
			}
		}

		$both['perc'] = $perc;
		$both['delta'] = $delta;

		return $both;
	}


	private function formatGoalData( $arr ){
		$formatThese = array('goal', 'perc', 'delta');
		$explode = '';

		foreach ($formatThese as $key => $value) {
			if( $value == 'perc' ){
				$arr[$value]['monthly'] = number_format($arr[$value]['monthly'] * 100, 0);
				$arr[$value]['quarterly'] = number_format($arr[$value]['quarterly'] * 100, 0);
				$arr[$value]['annually'] = number_format($arr[$value]['annually'] * 100, 0);
			}else{
				$explode = explode( '.', $arr[$value]['monthly'] );
				$arr[$value]['monthly'] = isset($explode[1]) ? number_format($arr[$value]['monthly'], 2) : $explode[0];

				$explode = explode( '.', $arr[$value]['quarterly'] );
				$arr[$value]['quarterly'] = isset($explode[1]) ? number_format($arr[$value]['quarterly'], 2) : $explode[0];

				$explode = explode( '.', $arr[$value]['annually'] );
				$arr[$value]['annually'] = isset($explode[1]) ? number_format($arr[$value]['annually'], 2) : $explode[0];
			}

		}

		return $arr;
	}


	//replace adjusted scores with actual scores
	private function replaceAdjustedScores($adj, $act){

		foreach ($adj as $key => $value) {
			$adj[$key]['score'] = $act[$key];
		}

		return $adj;
	}


	// returns a ranking of each school based on engagement data
	private function calculateRank($school){
		$rank = 1;
		$prev_score = 0;
		$prev_rank = 0;
		$score = 0;
		$tmp_scores = array();
		$tmp_ids = array();
		$id = 0;

		//store each score in temp array
		foreach ($school as $key => $value) {
			$school[$key]['overall_data']['total']['id'] = $id;
			$school[$key]['overall_data']['total']['rank'] = $rank;
			$tmp_scores[] = $value['overall_data']['total']['score'];
			$tmp_ids[] = $id;
			$id++;
		}

		// sort by score desc, highest to lowest
		array_multisort($tmp_scores, SORT_DESC, $school);

		foreach ($school as $key => $value) {

			//store current score
			$score = $value['overall_data']['total']['score'];

			//if current score equals previous score, then give it same rank
			//else give new rank
			if( $score == $prev_score ){
				$school[$key]['overall_data']['total']['rank'] = $prev_rank;
			}else{
				$school[$key]['overall_data']['total']['rank'] = $rank;
				//update previous rank
				$prev_rank = $rank;
			}

			//update previous score w/current score and increase rank
			$prev_score = $score;
			$rank++;
		}

		//return school array to original order by sorting by id given in beginning of function
		usort($school, function($a, $b){
			if( $a['overall_data']['total']['id'] == $b['overall_data']['total']['id'] ) return 0;
			return $a['overall_data']['total']['id'] > $b['overall_data']['total']['id'] ? 1 : -1;
		});

		return $school;
	}


	// returns total scores for each date criteria range
	private function calculateTotalScoreAndGrade($arr, $column = null, $date = null){
		$total = array();
		$total['score'] = 0;
		$id = 1;

		$tmp = isset($arr['last_logged_in_grade']) ? $arr['last_logged_in_grade'] : $arr['last_logged_in']['grade'];
		$total['score'] += $this->getValueFromGrade($tmp) * 1;
		$total['score'] += $arr['num_profile_view']['score'] > 2 ? (2 * 0.75) : ($arr['num_profile_view']['score'] * 0.75);
		$total['score'] += $arr['num_of_inquiries']['score'] > 18 ? (18 * 29.42) : ($arr['num_of_inquiries']['score'] * 29.42);
		$total['score'] += $arr['inquiry_activity']['score'] > 5 ? (5 * 5)*100 : ($arr['inquiry_activity']['score'] * 5)*100;
		$total['score'] += $arr['recommendation_activity']['score'] > 20 ? (20 * 4.40) : ($arr['recommendation_activity']['score'] * 4.40);
		$total['score'] += $arr['num_of_total_approved']['score'] > 33 ? (33 * 49) : ($arr['num_of_total_approved']['score'] * 49);
		$total['score'] += $arr['num_of_advance_search_approved']['score'] > 13 ? (13 * 6.94) : ($arr['num_of_advance_search_approved']['score'] * 6.94);
		$total['score'] += $arr['num_of_days_chatted']['score'] > 4 ? (4 * 8.11) : ($arr['num_of_days_chatted']['score'] * 8.11);
		$total['score'] += $arr['total_chat_sent']['score'] > 9 ? (9 * 3.26) : ($arr['total_chat_sent']['score'] * 3.26);
		$total['score'] += $arr['total_msg_sent']['score'] > 12 ? (12 * 4.65) : ($arr['total_msg_sent']['score'] * 4.65);

		if( isset($column) && isset($date) ){
			$now = Carbon::now();
			$dateJoined = Carbon::createFromTimestamp( strtotime($date) );
			$daysSinceJoining = $now->diffInDays( $dateJoined );

			$daysSinceJoining = $daysSinceJoining == 0 ? 1 : $daysSinceJoining;

			$total['score'] = $total['score'] / $daysSinceJoining;
			$total['grade'] = $this->getOverallGrade($total['score']);
		}else{
			$total['grade'] = $this->getGrade($total['score']);
		}

		$total['score'] = number_format($total['score'], 2);

		return $total;
	}


	// return a grade value for last logged in to be used in calculateTotalScoreAndGrade function
	private function getValueFromGrade($grade){
		$value = '';

		switch($grade){
			case 'A':
				$value = 9;
			break;
			case 'B':
				$value = 8;
			break;
			case 'C':
				$value = 7;
			break;
			case 'D':
				$value = 6;
			break;
			case 'F':
				$value = 0;
			break;
			default:
				$value = 0;
			break;
		}

		return $value;
	}


	private function getDaysSinceJoiningText( $date ){
		$now = Carbon::now();
		$dateJoined = Carbon::createFromTimestamp( strtotime($date) );
		$daysSinceJoining = $now->diffInDays( $dateJoined );

		if( $daysSinceJoining == 1 ){
			return $daysSinceJoining.' day';
		}

		return $daysSinceJoining.' days';
	}


	private function buildOverallDataForGrading($topics, $arr){
		$overall = array();
		$now = Carbon::now();
		$dateJoined = Carbon::createFromTimestamp( strtotime($arr['date_joined']) );
		$daysSinceJoining = $now->diffInDays( $dateJoined );

		$daysSinceJoining = $daysSinceJoining == 0 ? 1 : $daysSinceJoining;

		foreach ($topics as $key => $value) {
			if( $value == 'inquiry_activity' ){
				$tmpp = $this->getInquiryActivity($arr);
				$overall[$value] = (float)$tmpp / $daysSinceJoining;
			}elseif( $value == 'recommendation_activity' ){
				$tmpp = $arr['num_of_recommendations_accepted_pending'] + $arr['num_of_recommendations_rejected'];
				$overall[$value] = (float)$tmpp / $daysSinceJoining;
			}else{
				$overall[$value] = (float)$arr[$value] / $daysSinceJoining;
			}
		}

		return $overall;
	}

	//for overall score
	private function buildOverallDataForScores($topics, $arr){
		$overall = array();
		$now = Carbon::now();
		$dateJoined = Carbon::createFromTimestamp( strtotime($arr['date_joined']) );
		$daysSinceJoining = $now->diffInDays( $dateJoined );

		foreach ($topics as $key => $value) {
			if( $value == 'inquiry_activity' ){
				$tmpp = $this->getInquiryActivity($arr);
				$overall[$value] = $tmpp;
			}elseif( $value == 'recommendation_activity' ){
				$tmpp = $arr['num_of_recommendations_accepted_pending'] + $arr['num_of_recommendations_rejected'];
				$overall[$value] = $tmpp;
			}else{
				$overall[$value] = $arr[$value];
			}

		}

		return $overall;
	}


	private function generateEngagementData( $topics, $ar, $criteria, $forWho ){

		$tmparr = array();

		foreach ($topics as $key => $value) {
			$tmparr[$value] = array();

			if( $forWho == 'overall' ){
				$tmparr[$value]['score'] = $ar[$value];
			}else{
				if( $value == 'inquiry_activity' ){
					$tmparr[$value]['score'] = $this->getInquiryActivity($ar);
				}elseif( $value == 'recommendation_activity' ){
					$tmparr[$value]['score'] = $ar['num_of_recommendations_accepted_pending'] + $ar['num_of_recommendations_rejected'];
				}else{
					$tmparr[$value]['score'] = $ar[$value];
				}
			}

			$tmparr[$value]['criteria'] = $criteria[$key];
			$tmparr[$value]['grade'] = $this->getGrade((float)$tmparr[$value]['score'], $tmparr[$value]['criteria']);
		}

		return $tmparr;
	}


	private function getDaysInDateRange($forWho, $date = null){
		$days = 0;

		if( $forWho == 'twoWeeks' ){
			$days = 14;
		}elseif( $forWho == 'month' ){
			$days = 30;
		}elseif( $forWho == 'overall' ){
			$now = Carbon::now();
			$dateJoined = Carbon::createFromTimestamp( strtotime($date) );
			$daysSinceJoining = $now->diffInDays( $dateJoined );
			$days = $daysSinceJoining;
		}elseif( $forWho == 'custom' ){
			$days = $date;
		}

		return $days;
	}


	private function formatScoresForDisplay($arr){

		foreach ($arr as $key => $value) {
			$arr[$key]['score'] = number_format($arr[$key]['score'], 2);
		}

		return $arr;
	}


	private function generateLastLoggedInData( $hrs ){
		$data = array();

		$data['grade'] = $this->getLastLoggedInGrade($hrs);
		$data['score'] = $this->getValueFromGrade($data['grade']);
		$data['text'] = $this->getLastLoggedInText($hrs);

		return $data;
	}


	private function generateTotals($arr){
		$total = array();
		$score = 0;

		foreach ($arr as $key => $value) {
			$score += $value['score'];
		}

		$total['score'] = ceil($score);
		$total['score'] = $total['score'] > 125 ? 125 : $total['score'];
		$total['grade'] = $this->getOverallGrade($total['score']);

		return $total;
	}


	private function generateScoreData( $topics, $ar, $criteria, $forWho, $date = null ){
		$tmparr = array();
		$days = $this->getDaysInDateRange($forWho, $date);
		$score = 0;

		$days = $days == 0 ? 1 : $days;

		foreach ($topics as $key => $value) {

			switch( $value ){
				case 'num_profile_view':
					$tmparr[$value]['raw_value'] = (int)$ar[$value];
					$score = ($ar[$value] / $days) * 0.75;
					$tmparr[$value]['score'] = $score > 2 ? 2 : $score;
					break;
				case 'num_of_inquiries':
					$tmparr[$value]['raw_value'] = (int)$ar[$value];
					$score = ($ar[$value] / $days) * 29.42;
					$tmparr[$value]['score'] = $score > 18 ? 18 : $score;
					break;
				case 'inquiry_activity':
					$i_activity = $this->getInquiryActivity($ar);
					$tmp = $i_activity * 100;
					$tmparr[$value]['raw_value'] = $tmp > 100 ? '100%' : number_format($tmp, 0).'%';
					$score = $i_activity * 5;
					$tmparr[$value]['score'] = $score > 5 ? 5 : $score;
					break;
				case 'recommendation_activity':
					$r_activity = $ar['num_of_recommendations_accepted_pending'] + $ar['num_of_recommendations_rejected'];
					$tmparr[$value]['raw_value'] = (int)$r_activity;
					$r_activity = ($r_activity / $days) * 4.40;
					$score = $r_activity > 20 ? 20 : $r_activity;
					$tmparr[$value]['score'] = $score;
					break;
				case 'num_of_total_approved':
					$tmparr[$value]['raw_value'] = (int)$ar[$value];
					$score = ($ar[$value] / $days) * 49;
					$tmparr[$value]['score'] = $score > 33 ? 33 : $score;
					break;
				case 'num_of_advance_search_approved':
					$tmparr[$value]['raw_value'] = (int)$ar[$value];
					$score = ($ar[$value] / $days) * 6.94;
					$tmparr[$value]['score'] = $score > 13 ? 13 : $score;
					break;
				case 'num_of_days_chatted':
					$tmparr[$value]['raw_value'] = (int)$ar[$value];
					$score = ($ar[$value] / $days) * 8.11;
					$tmparr[$value]['score'] = $score > 4 ? 4 : $score;
					break;
				case 'total_chat_sent':
					$tmparr[$value]['raw_value'] = (int)$ar[$value];
					$score = ($ar[$value] / $days) * 3.26;
					$tmparr[$value]['score'] = $score > 9 ? 9 : $score;
					break;
				case 'total_msg_sent':
					$tmparr[$value]['raw_value'] = (int)$ar[$value];
					$score = ($ar[$value] / $days) * 4.65;
					$tmparr[$value]['score'] = $score > 12 ? 12 : $score;
					break;
			}

			$tmparr[$value]['criteria'] = $criteria[$key];
			$tmparr[$value]['grade'] = $this->getGrade((float)$tmparr[$value]['score'], $tmparr[$value]['criteria']);
		}

		return $tmparr;
	}


	// returns inquiry activity
	private function getInquiryActivity( $inquiry ){
		$total = isset($inquiry['num_of_inquiries']) ? $inquiry['num_of_inquiries'] : $inquiry['num_of_inquiries_accepted'] + $inquiry['num_of_inquiries_rejected'] + $inquiry['num_of_inquiries_idle'];
		$tmp = 0;

		if( $total == 0 ){
			return 0;
		}else{
			$tmp = (($inquiry['num_of_inquiries_accepted'] + $inquiry['num_of_inquiries_rejected']) / $total);
			return $tmp;
		}
	}


	// returns last logged in text in human readable format
	private function getLastLoggedInText($time){
		$text = '';

		if( $time <= 24 ){
			$text = 'yesterday';
		}elseif( $time >= 25 && $time <= 72 ){
			$text = 'Less than 3 Days';
		}elseif( $time >= 73 && $time <= 168 ){
			$text = 'Less than a Week';
		}elseif( $time >= 169 && $time <= 336 ){
			$text = 'Less than 2 Weeks';
		}elseif( $time >= 337 && $time <= 720 ){
			$text = 'Less than a Month';
		}elseif( $time > 720 ){
			$text = (int)($time / 24).' days ago';
		}else{
			$text = (int)($time / 24).' days ago';
		}

		return $text;
	}


	// returns grade for last logged in, accept time in hours
	private function getLastLoggedInGrade($hrs){
		$grade = '';

		if( $hrs <= 24 || $hrs <= (24*3) ){
			$grade = 'A';
		}elseif( $hrs <= (24*7) ){
			$grade = 'B';
		}elseif( $hrs <= (24*14) ){
			$grade = 'C';
		}elseif( $hrs <= (24*30) ){
			$grade = 'D';
		}elseif( $hrs > (24*30) ){
			$grade = 'F';
		}else{
			$grade = 'F';
		}

		return $grade;
	}


	// returns letter grade
	private function getGrade( $score, $criteria = array() ){
		$grade = '';

		$crit = !empty($criteria) ? explode(',', $criteria) : array(89, 80, 70, 60);

		if( $score > (float)$crit[0] ){
			$grade = 'A';
		}elseif( $score >= (float)$crit[1] && $score <= (float)$crit[0] ){
			$grade = 'B';
		}elseif( $score >= (float)$crit[2] && $score < (float)$crit[1] ){
			$grade = 'C';
		}elseif( $score >= (float)$crit[3] && $score < (float)$crit[2] ){
			$grade = 'D';
		}elseif( $score < (float)$crit[3] ){
			$grade = 'F';
		}else{
			$grade = 'F';
		}

		return $grade;
	}


	// returns -/+ grades for overall scores
	private function getOverallGrade( $score ){
		$grade = '';

		if( $score >= 111 ){
			$grade = 'A+++';
		}elseif( $score >= 101 && $score <= 110 ){
			$grade = 'A++';
		}elseif( $score >= 96 && $score <= 100 ){
			$grade = 'A+';
		}elseif( $score >= 91 && $score <= 95 ){
			$grade = 'A';
		}elseif( $score >= 86 && $score <= 90 ){
			$grade = 'A-';
		}elseif( $score >= 81 && $score <= 85 ){
			$grade = 'B+';
		}elseif( $score >= 76 && $score <= 80 ){
			$grade = 'B';
		}elseif( $score >= 71 && $score <= 75 ){
			$grade = 'B-';
		}elseif( $score >= 66 && $score <= 70 ){
			$grade = 'C+';
		}elseif( $score >= 61 && $score <= 65 ){
			$grade = 'C';
		}elseif( $score >= 56 && $score <= 60 ){
			$grade = 'C-';
		}elseif( $score >= 51 && $score <= 55 ){
			$grade = 'D+';
		}elseif( $score >= 46 && $score <= 50 ){
			$grade = 'D';
		}elseif( $score >= 41 && $score <= 45 ){
			$grade = 'D-';
		}elseif( $score <= 40 ){
			$grade = 'F';
		}else{
			$grade = 'F';
		}

		return $grade;
	}


	// -- setting sales trigger
	public function setTrigger(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		$emails = array();

		foreach ($input['emails'] as $key => $value) {
			array_push($emails, $value);
		}

		$emails = implode(',', $emails);

		$org = (int)$input['school_id'];
		$org = OrganizationBranch::find($org);
		$org->trigger_set = 1;
		$org->trigger_frequency = $input['frequency'];
		$org->trigger_notify_emails = $emails;
		$org->trigger_emergency_set = (int)$input['emergency_trigger'] > 0 ? 1 : 0;
		$org->trigger_emergency_percentage = (int)$input['emergency_percentage'];
		$org->save();

		return 'success';
	}


	private function getScoreForCustomDates($topics, $arr, $date_range, $criteria){
		$ret = array();

		foreach ($topics as $key => $value) {
			if( $value == 'last_logged_in' ){
				$ret[$value]['value'] = $arr[$value];
				$temp = $this->getLastLoggedInGrade($arr[$value]);
				$ret[$value]['score'] = $this->getLastLoggedInText($arr[$value]);
				$ret[$value]['grade'] = $temp;
			}else{
				$ret[$value]['value'] = $arr[$value];
				$temp = ( (int)$arr[$value] > 0 && (int)$date_range > 0 ) ? ((int)$arr[$value] / (int)$date_range) : 0;
				$ret[$value]['score'] = $temp;
				$ret[$value]['grade'] = $this->getGrade($temp, $criteria[$key]);
			}

		}

		$ret['overall'] = array();
		$ret['overall']['value'] = '';
		$tot_score = $this->calculateTotalScoreAndGrade($ret);
		$ret['overall']['score'] = $tot_score['score'];
		$ret['overall']['grade'] = $this->getGrade($ret['overall']['score']);

		return $ret;
	}


	// -- set comparison returns data about specific date ranges
	public function setComparison(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();


		if (false) {
			# code...
		// if (!Cache::has(env('ENVIRONMENT') .'_'.'salesControllerSetComparison_'.$input['school_id'].'_'.$input['l_end_date']
		// 			.'_'.$input['l_start_date'].'_'.$input['r_end_date'].'_'.$input['r_start_date'])) {
		// 	$data['comparison_data'] =  Cache::get(env('ENVIRONMENT') .'_'.'salesControllerSetComparison_'.$input['school_id'].'_'.$input['l_end_date']
		// 			.'_'.$input['l_start_date'].'_'.$input['r_end_date'].'_'.$input['r_start_date']);
		}else{

			$org = new Organization;
			$tp = new TrackingPage;
			$cml = new CollegeMessageLog;
			$rec = new Recruitment;
			$ntn = new NotificationTopNav;
			$lt = new LikesTally;
			$cr = new CollegeRecommendation;
			$lst = new RankingList;

			$admins_info = $org->getOrgsAdminInfo();

			$now = Carbon::now();
			$today = Carbon::today();
			$yesterday = Carbon::yesterday();
			$dayBefore = Carbon::yesterday()->subDays(1);
			$pastWeek = Carbon::yesterday()->subDays(7);
			$weekBefore = Carbon::yesterday()->subDays(14);
			$twoWeeksAgo = $weekBefore->copy();
			$threeWeeksAgo = Carbon::yesterday()->subDays(21);
			$fourWeeksAgo = Carbon::yesterday()->subDays(28);
			$pastCoupleWeeks = $weekBefore->copy();
			$thisMonth = Carbon::yesterday()->subDays(30);
			$lastMonth = Carbon::yesterday()->subDays(60);

			$start = null;
			$end = null;
			$leftColumn = array();
			$rightColumn = array();
			$data['comparison_data'] = array();

			$id = array($input['school_id']);

			$this_college = College::find($input['school_id']);

			$this_user_id_arr = array($input['user_id']);

			switch( $input['option'] ){
				case 1:
					$l_start = $today;
					$l_end = $yesterday;
					$r_start = $yesterday;
					$r_end = $dayBefore;
					break;
				case 2:
					$l_start = $yesterday;
					$l_end = $pastWeek;
					$r_start = $pastWeek;
					$r_end = $weekBefore;
					break;
				case 3:
					$l_start = $yesterday;
					$l_end = $weekBefore;
					$r_start = $threeWeeksAgo;
					$r_end = $fourWeeksAgo;
					break;
				case 4:
					$l_start = $yesterday;
					$l_end = $thisMonth;
					$r_start = $thisMonth;
					$r_end = $lastMonth;
					break;
				case 5:
					$c = explode('-', $input['l_start_date']);
					$l_start = Carbon::create($c[0], $c[1], $c[2], 0, 0, 0);

					$c = explode('-', $input['l_end_date']);
					$l_end = Carbon::create($c[0], $c[1], $c[2], 0, 0, 0);

					$c = explode('-', $input['r_start_date']);
					$r_start = Carbon::create($c[0], $c[1], $c[2], 0, 0, 0);

					$c = explode('-', $input['r_end_date']);
					$r_end = Carbon::create($c[0], $c[1], $c[2], 0, 0, 0);
					break;
				default:
					return 'no valid option number sent';
			}

			$l_diffInDateRange = $l_start->diffInDays($l_end);
			$r_diffInDateRange = $r_start->diffInDays($r_end);

			$topics = array('num_profile_view', 'num_of_inquiries','inquiry_activity', 'recommendation_activity',
							'num_of_total_approved','num_of_advance_search_approved',
							'num_of_days_chatted','total_chat_sent','total_msg_sent');

			$criteria = array('1.33,0.81,0.27,0.07', '0.5,0.31,0.14,0.04', '99,94,86,35', '3.4,3.01,2.34,0.51', '0.5,0.34,0.21,0.06',
							  '1.43,1.08,0.72,0.37', '0.36,0.23,0.08,0.01', '2.14,1.44,0.72,0.01', '2.86,1.87,0.94,0.01');

			// adding last logged in data == get here man
			$lli = $this->xTimeAgoHr($tp->getLastLoggedInDate($data['user_id']), date("Y-m-d H:i:s"));

			//total profile views
			$leftColumn['num_profile_view']  = $this->getArrayValue('cnt', 'name', $this_college->school_name, $ntn->getNumProfileViewsByCollegeId(array($this_college->school_name), $l_end, $l_start));
			$rightColumn['num_profile_view'] = $this->getArrayValue('cnt', 'name', $this_college->school_name, $ntn->getNumProfileViewsByCollegeId(array($this_college->school_name), $r_end, $r_start));

			// total inquiries
			$leftColumn['num_of_inquiries'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfInquiryForColleges($id, 'inquiry', $l_end, $l_start));
			$rightColumn['num_of_inquiries'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfInquiryForColleges($id, 'inquiry', $r_end, $r_start));

			//get inquiry/recommendation activity left side
			$leftColumn['num_of_inquiries_rejected'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfInquiryRejectedForColleges($id, 'inquiry', $l_end, $l_start));
			$leftColumn['num_of_inquiries_accepted'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfInquiryAcceptedForColleges($id, 'inquiry', $l_end, $l_start));
			$leftColumn['num_of_recommendations_accepted_pending'] = $this->getArrayValue('cnt', 'college_id', $id[0], $cr->getNumOfRecommendationsAcceptedPendingForColleges($id, $l_end, $l_start));
			$leftColumn['num_of_recommendations_rejected'] = $this->getArrayValue('cnt', 'college_id', $id[0], $cr->getNumOfRecommendationsRejectedForColleges($id, $l_end, $l_start));
			$leftColumn['inquiry_activity'] = $this->getInquiryActivity($leftColumn);
			$leftColumn['recommendation_activity'] = $leftColumn['num_of_recommendations_accepted_pending'] + $leftColumn['num_of_recommendations_rejected'];

			//get inquiry/recommendation activity right side
			$rightColumn['num_of_inquiries_rejected'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfInquiryRejectedForColleges($id, 'inquiry', $r_end, $r_start));
			$rightColumn['num_of_inquiries_accepted'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfInquiryAcceptedForColleges($id, 'inquiry', $r_end, $r_start));
			$rightColumn['num_of_recommendations_accepted_pending'] = $this->getArrayValue('cnt', 'college_id', $id[0], $cr->getNumOfRecommendationsAcceptedPendingForColleges($id, $r_end, $r_start));
			$rightColumn['num_of_recommendations_rejected'] = $this->getArrayValue('cnt', 'college_id', $id[0], $cr->getNumOfRecommendationsRejectedForColleges($id, $r_end, $r_start));
			$rightColumn['inquiry_activity'] = $this->getInquiryActivity($leftColumn);
			$rightColumn['recommendation_activity'] = $rightColumn['num_of_recommendations_accepted_pending'] + $rightColumn['num_of_recommendations_rejected'];

			//get total approved
			$leftColumn['num_of_total_approved'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfApprovedForColleges($id, $l_end, $l_start));
			$rightColumn['num_of_total_approved'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfApprovedForColleges($id, $r_end, $r_start));

			//get num of advanced search approved
			$leftColumn['num_of_advance_search_approved'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfInquiryAcceptedForColleges($id, 'advance_search', $l_end, $l_start));
			$rightColumn['num_of_advance_search_approved'] = $this->getArrayValue('cnt', 'college_id', $id[0], $rec->getNumOfInquiryAcceptedForColleges($id, 'advance_search', $r_end, $r_start));

			//get number of days chatted
			$leftColumn['num_of_days_chatted'] = $this->getArrayValue('cnt', 'college_id', $id[0], $cml->getNumOfDaysChatted(null, $l_end, $l_start, $id));
			$rightColumn['num_of_days_chatted'] = $this->getArrayValue('cnt', 'college_id', $id[0], $cml->getNumOfDaysChatted(null, $r_end, $r_start, $id));

			//get chat / msg for left side
			$leftColumn['total_chat_sent'] = $this->getArrayValue('cnt', 'is_chat',1 , $cml->getNumOfMsgSentReceivedOfChatAndMessagesByDate($this_user_id_arr, $l_end, $l_start, '='));
			$leftColumn['total_msg_sent'] = $this->getArrayValue('cnt', 'is_chat',0 , $cml->getNumOfMsgSentReceivedOfChatAndMessagesByDate($this_user_id_arr, $l_end, $l_start, '='));

			//get chat / msg for right side
			$rightColumn['total_chat_sent'] = $this->getArrayValue('cnt', 'is_chat',1 , $cml->getNumOfMsgSentReceivedOfChatAndMessagesByDate($this_user_id_arr, $r_end, $r_start, '='));
			$rightColumn['total_msg_sent'] = $this->getArrayValue('cnt', 'is_chat',0 , $cml->getNumOfMsgSentReceivedOfChatAndMessagesByDate($this_user_id_arr, $r_end, $r_start, '='));

			//getting filter info
			$arr['filtered_recommendations'] = 0;
			$arr['non_filtered_recommendations'] = 0;
			$arr['college_recommendation_action'] = '';
			$yes_rec = 0;
			$no_rec = 0;
			$neutral_rec = 0;
			$tmp_arr['l_this_college_recommendation'] = array();
			$tmp_arr['l_this_college_recommendation']['val'] = null;
			$tmp_arr['l_this_college_recommendation']['start'] = $l_start;
			$tmp_arr['l_this_college_recommendation']['end'] = $l_end;
			$tmp_arr['r_this_college_recommendation'] = array();
			$tmp_arr['r_this_college_recommendation']['val'] = null;
			$tmp_arr['r_this_college_recommendation']['start'] = $l_start;
			$tmp_arr['r_this_college_recommendation']['end'] = $l_end;

			//for both left/right sides, filter recommendation types then if filtered_recommendations total is greater than 1, then filter is active
			foreach($tmp_arr as $key => $value){
				$recruit_key = $cr->getTodayRecommendations($id[0], $value['start'], $value['end']);

				foreach( $recruit_key as $rec_key ){
					if ($rec_key->type == 'not_filtered') {
						$arr['non_filtered_recommendations']++;
					}else{
						$arr['filtered_recommendations']++;
					}

					if($rec_key->active == 1){
						$neutral_rec++;

					}elseif ($rec_key->active == 0) {
						$yes_rec++;
					}else{
						$no_rec++;
					}
				}

				$tmp_arr[$key]['val'] = $arr['filtered_recommendations'];
			}

			//get score and grades for custom dates
			$leftColumn = $this->generateScoreData( $topics, $leftColumn, $criteria, 'custom', $l_diffInDateRange );
			$rightColumn = $this->generateScoreData( $topics, $rightColumn, $criteria, 'custom', $r_diffInDateRange );

			//get filter active and create raw_value prop - left column
			$leftColumn['filter_active'] = array();
			$leftColumn['filter_active']['raw_value'] = '--';
			$leftColumn['filter_active']['score'] = '--';
			$leftColumn['filter_active']['grade'] = $tmp_arr['l_this_college_recommendation']['val'] > 0 ? 'Yes' : 'No';

			//get filter active and create raw_value prop - right column
			$rightColumn['filter_active'] = array();
			$rightColumn['filter_active']['raw_value'] = '--';
			$rightColumn['filter_active']['score'] = '--';
			$rightColumn['filter_active']['grade'] = $tmp_arr['r_this_college_recommendation']['val'] > 0 ? 'Yes' : 'No';

			//get last logged in and create raw_value prop
			$leftColumn['last_logged_in'] = $this->generateLastLoggedInData($lli);
			$leftColumn['last_logged_in']['raw_value'] = $leftColumn['last_logged_in']['text'];
			$rightColumn['last_logged_in'] = $this->generateLastLoggedInData($lli);
			$rightColumn['last_logged_in']['raw_value'] = $rightColumn['last_logged_in']['text'];

			//get totals
			$leftColumn['total'] = $this->generateTotals($leftColumn);
			$leftColumn['total']['raw_value'] = $leftColumn['total']['score'];
			$rightColumn['total'] = $this->generateTotals($rightColumn);
			$rightColumn['total']['raw_value'] = $rightColumn['total']['score'];

			//save to $data
			$data['comparison_data']['left'] = $leftColumn;
			$data['comparison_data']['right'] = $rightColumn;
			// echo "<pre>";
			// print_r($data);
			// echo "</pre>";
			// exit();
		}

		$today = date('Y-m-d');

		// if ($today == $input['l_end_date'] || $today == $input['l_start_date'] ||
		// 	$today == $input['r_end_date'] || $today == $input['r_start_date']) {
		// 	Cache::put(env('ENVIRONMENT') .'_'.'salesControllerSetComparison_'.$input['school_id'].'_'.$input['l_end_date']
		// 			.'_'.$input['l_start_date'].'_'.$input['r_end_date'].'_'.$input['r_start_date'], $data['comparison_data'], 50);
		// }else{
		// 	Cache::forever(env('ENVIRONMENT') .'_'.'salesControllerSetComparison_'.$input['school_id'].'_'.$input['l_end_date']
		// 			.'_'.$input['l_start_date'].'_'.$input['r_end_date'].'_'.$input['r_start_date'], $data['comparison_data']);
		// }

		return $data;
	}


	public function getMessages($org_branch_id = null, $user_id = null){
		//Get user logged in info and ajaxtoken.
		//$user = User::find( Auth::user()->id );
		//$token = $user->ajaxtoken->toArray();

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss Message Center Page';
		$data['currentPage'] = 'sales-messages';
		//$data['ajaxtoken'] = $token['token'];

		$active_thread = '';
		if ($org_branch_id != null && $user_id != null) {

			$cmtm = new CollegeMessageThreadMembers;

			$threads = $cmtm->getAdminThreads($org_branch_id, $user_id);

			$thread_list = array();

			$user = User::find($user_id);

			$data['msg_owner_name'] = $user->fname.' '.$user->lname;

			$data['msg_owner_school_name'] = 'N/A';

			foreach ($threads as $key) {

				$data['msg_owner_school_name'] = $key->school_name;

				$tmp = array();

				$tmp['thread_id'] = $key->thread_id;
				$tmp['name'] = $key->fname . ' '. $key->lname;
				$tmp['is_chat'] = $key->is_chat;

				if ($key->is_chat == 0 && $active_thread == '') {
					$active_thread = $key->thread_id;
					$tmp['active_thread'] = true;
				}else{
					$tmp['active_thread'] = false;
				}

				$tmp['msg_link'] = '/admin/messages/'.$key->user_id.'/inquiry-msg';
				$tmp['school_name'] = $key->school_name;


				$tmp['plexuss_note'] = $key->plexuss_note;
				$tmp['note_date'] = $this->xTimeAgo($key->note_date, date("Y-m-d H:i:s"));

				$tmp['idle'] = $key->idle;

				if ($tmp['is_chat'] == 1) {
					array_unshift($thread_list , $tmp);
				}else{
					$thread_list[] = $tmp;
				}

			}

			$data['thread_list'] = $thread_list;
		}else{
			// this is for plexuss only display

			$cmtm = new CollegeMessageThreadMembers;

			$threads = $cmtm->getAdminThreads(1, $data['user_id']);

			$thread_list = array();

			$data['msg_owner_name'] = $data['fname'].' '.$data['lname'];

			$data['msg_owner_school_name'] = 'N/A';

			foreach ($threads as $key) {

				$data['msg_owner_school_name'] = $key->school_name;

				$tmp = array();

				$tmp['thread_id'] = $key->thread_id;
				$tmp['name'] = $key->fname . ' '. $key->lname;

				//convert name to utf-8 format
				$tmp['name'] = $this->convertNameToUTF8($tmp['name']);

				$tmp['is_chat'] = $key->is_chat;

				if ($key->is_chat == 0 && $active_thread == '') {
					$active_thread = $key->thread_id;
					$tmp['active_thread'] = true;
				}else{
					$tmp['active_thread'] = false;
				}

				$tmp['msg_link'] = '/admin/messages/'.$key->user_id.'/inquiry-msg';
				$tmp['school_name'] = $key->school_name;

				$tmp['plexuss_note'] = $key->plexuss_note;
				$tmp['note_date'] = $this->xTimeAgo($key->note_date, date("Y-m-d H:i:s"));

				$tmp['idle'] = $key->idle;

				if ($tmp['is_chat'] == 1) {
					array_unshift($thread_list , $tmp);
				}else{
					$thread_list[] = $tmp;
				}

			}

			$data['thread_list'] = $thread_list;

		}

		$data['threads_cnt'] = count($threads);

		$data['active_thread_msg'] = json_decode($this->getThreadMsgs($active_thread));

		return View('sales.messages', $data);
	}

	public function getThreadMsgs($thread_id = null){

		if ($thread_id == null) {
			return null;
		}

		$cmt = new CollegeMessageThreads($thread_id);



		$dt =json_decode($this->getUserMessages($thread_id));

		$ret = array();

		foreach ($dt as $key) {

			$tmp = array();

			$tmp = $key;

			$myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $key->date);

			$dt = new DateTime( date_format( $myDateTime, 'Y-m-d H:i:s' ), new DateTimeZone( 'UTC' ) );
			$dt->setTimezone( new DateTimeZone( 'America/Los_Angeles' ) );

			$tmp->date = $dt->format( 'Y-m-d H:i:s' );

			$ret[] = (array) $tmp;
		}

		$data['data'] = json_encode($ret);

		$data['note_arr'] = $cmt->getPlexussNote();

		$data['note_arr']['note_date'] = $this->xTimeAgo($data['note_arr']['note_date'], date("Y-m-d H:i:s"));

		$data['note_arr'] = json_encode($data['note_arr']);
		$data = json_encode($data);

		return $data;
	}

	public function getBilling(){
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		$token = $user->ajaxtoken->toArray();

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss Billing Report Page';
		$data['currentPage'] = 'sales-billing';
		$data['ajaxtoken'] = $token['token'];

		return View('sales.billing', $data);
	}

	public function setPlexussNote(){

		$input = Request::all();

		$cmt = new CollegeMessageThreads($input['thread_id']);

		$ret = $cmt->updatePlexussNote($input['note']);

		if ($ret == null) {
			return '';
		}

		$dt = date('h:i',strtotime($ret));

		return $dt;

	}

	/**
	 * IMPORTNAT!!!!!!!!!!!!!!!
	 * This method manually log in you as a particular user, this method is not
	 * suppose to be used by public rather ONLY for plexuss crew to check someone's
	 * profile
	 * @param user_id
	 * @return uri
	 */

	public function loginas($user_id = null){

		$handshake_power_arr =  array(833429, 463, 791242, 93, 9160);
		try {
			$user_id = Crypt::decrypt($user_id);
		} catch (\Exception $e) {
			return "Bad user id";
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if ($user_id == null) {
			return;
		}

		if (in_array($data['user_id'], $handshake_power_arr)) {
			$handshake_power = true;
		}

		// $ac = new AuthController();

		// $ac->clearAjaxToken( Auth::user()->id );
		Auth::logout();
		Session::flush();

		Auth::loginUsingId( $user_id, true );
		// $ac->setAjaxToken($user_id);
		Session::put('sales_log_back_in_user_id', Crypt::encrypt($data['user_id']));
		Session::put('userinfo.session_reset', 1);
		Session::put('sales_super_power', 1);
		Session::put('sales_log_back_in_user_id', Crypt::encrypt($data['user_id']));
		if (isset($handshake_power)) {
			Session::put('handshake_power', 1);
		}
		return redirect( '/admin' );

	}

	private function getArrayValue($retField, $field, $val, $obj){

		foreach ($obj as $key) {

			if (!is_object($key)) {
				if (isset($key[$field]) && $key[$field] == $val) {
					return  $key[$retField];
				}
			}else{
				if (isset($key->$field) && $key->$field == $val) {
					return  $key->$retField;
				}
			}

		}
		return 0;
	}


	// -------------- convert name to utf-8 format
	public function convertNameToUTF8($name){
		$valid_name = $name;

		if (preg_match('/[^A-Za-z ]/', $name)){
			$convert_string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name); //convert to ascii utf-8
			$strip_chars = preg_replace("/[^a-zA-Z ]/", "", $convert_string); //get rid of any chars that aren't letters or spaces

			//when trimmed, if it's empty, then that means entire name doesn't contain any english letters, so it's a different language,
			//so don't save converted name, save original name
			if( trim($strip_chars) == '' ){
				$valid_name = 'Cannot Display Name';
			}else{
				//if here, then that means converting the name was able to produce english letters so save the converted name
				$valid_name = $strip_chars;
			}
		}

		return $valid_name;
	}

	private function xTimeAgoHr( $oldTime, $newTime ) {
		$timeCalc = strtotime( $newTime ) - strtotime( $oldTime );
		$timeCalc = round( $timeCalc/60/60 );
		// if ( $timeCalc > ( 60*60 ) ) {$timeCalc = round( $timeCalc/60/60 ) . " hrs ago";}
		// else if ( $timeCalc > 60 ) {$timeCalc = round( $timeCalc/60 ) . " mins ago";}
		// else if ( $timeCalc > 0 ) {$timeCalc .= " secs ago";}
		return $timeCalc;
	}

	private function calculateOverallActivityScore($data){
		//query for all these fields
		$oas = 0;

		$oas += (int)$data['num_of_inquiries_accepted'] * 2;
		$oas += (int)$data['num_of_inquiries_rejected'] * 2;
		$oas += (int)$data['num_of_inquiries_idle'] * 0.25;
		$oas += (int)$data['num_of_recommendations_accepted_pending'] * 1;
		$oas += (int)$data['num_of_recommendations_rejected'] * 1;
		$oas += (int)$data['num_of_recommendations_idle'] * 0.1667;
		$oas += (int)$data['num_of_days_chatted'] * 3;
		$oas += (int)$data['total_chat_sent'] > 0.25 ? $oas + ($data['total_chat_sent'] * 1) : $oas + ($data['total_chat_sent'] * 0.5);
		$oas += (int)$data['total_msg_sent'] > 0.33 ? $oas + ($data['total_msg_sent'] * 1.5) : $oas + ($data['total_msg_sent'] * 0.75);
		$oas += (int)$data['num_of_advance_search_approved'] * 2;
		$oas += (int)$data['num_profile_view'] * 0.5;
		// $oas += (int)$data['export_file_cnt'] * 2.5;
		$oas += (int)$data['num_of_uploaded_ranking'] * 5;

		return $oas;
	}

	private function filterChatAndMsg($chatMsg){
		$chat = array();
		$msg = array();
		$both = array();

		if (isset($chatMsg)) {
			foreach ($chatMsg as $key) {
				//chat sent
				if( $key->is_organization == 1 && $key->is_chat == 1 ){
					$chat[$key->org_branch_id][$key->user_id] = $key->cnt;
				}

				// msg sent
				if ($key->is_organization == 1 && $key->is_chat == 0) {
					$msg[$key->org_branch_id][$key->user_id] = $key->cnt;
				}
			}
		}

		if( empty($chat) && empty($msg) ){
			$both['chat'] = count($chat);
			$both['msg'] = count($msg);
		}else{
			$both['chat'] = $chat;
			$both['msg'] = $msg;
		}

		return $both;
	}

	private function getExportArr( $admins_info ){
		$exportArr = array();

		foreach ($admins_info as $key) {
			$exportArr[] = $key->export_file_cnt;
		}

		return $exportArr;
	}

	public function forgetSalesCache(){

  		Cache::forget(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate');
 		Cache::forget(env('ENVIRONMENT') .'_'.'salesControllerGenerateDate_takeSkip');
  	}

  	public function showExportCache(){
  		echo "<pre>";
  		print_r(Cache::get(env('ENVIRONMENT').'_export_info'));
  		echo "</pre>";
  	}

  	public function forgetExportCache(){
  		Cache::forget(env('ENVIRONMENT').'_export_info');
  	}

  	public function getPrioritySchools(){
  		return $this->generatePickACollegeData();
  	}

  	public function getApplicationCollege(){
  		$pr = new Priority;
		$pr = $pr->getApplicationCollege();

		return $pr;
  	}

  	/*
  	** saveDataToPrioritySchools
  	*/
    public function saveDataToPrioritySchools()
    {
        $input = Request::all();
        if (isset($input['id'])) {
            $pr = new Priority;
            $pr->college_id = $input['id'];
            if($pr->save()){
                $id = $pr->id;
                return $this->generatePickACollegeDataById($id);
            } else {
                return "error";
            }
        }
    }

  	public function saveEditsToPrioritySchools(){
  		$input = Request::all();

  		if( isset($input['id']) ){
  			$pr = Priority::find($input['id']);

  			$pr->promote = $input['promoted'];
  			if( isset($input['contract']) ) $pr->contract = $input['contract'];
  			if( isset($input['goal']) ) $pr->goal = $input['goal'];
  			if( isset($input['financial_filter_order']) ) $pr->financial_filter_order = $input['financial_filter_order'];

  			$pr->save();

  			return 'success';
  		}

  		return 'error';
  	}

  	public function getContractTypes(){
  		$result = DB::connection('rds1')->table('contract_types')->get();

  		foreach ($result as $key){
  			$key->id = (int)$key->id;
  		}

  		return $result;
  	}

  	public function saveGoalDates(){
  		$input = Request::all();

  		$pr = DB::table('priority')
  				->update(array('start_goal' => $input['start_date'], 'end_goal' => $input['end_date']));

  		return 'success';
  	}

  	public function getDateForPickACollege(){
  		$pr = Priority::first();
  		$pr->start_goal = str_replace('-', '/', $pr->start_goal);
  		$pr->end_goal = str_replace('-', '/', $pr->end_goal);
  		return $pr;
  	}

  	// Agency reporting here
  	public function getAgencyReportingData(){

  		$input = Request::all();

  		$ts1 = strtotime($input['start_date']);
		$ts2 = strtotime($input['end_date']);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$num_of_months = (int)floor((($year2 - $year1) * 12) + ($month2 - $month1));

		$an = DB::connection('rds1')->table('agency as an')
									->join('agency_permissions as ap', 'an.id', '=', 'ap.agency_id')
									->join('users as u', 'u.id', '=', 'ap.user_id')
									->where('an.active', 1)
									->select('u.fname', 'u.lname', 'an.*', 'u.id as uid')
									->groupBy('an.id')
									->get();

		$ac = new AgencyController;
		$tp = new TrackingPage;
		$ret = array();

		$input['start_date'] = str_replace("/", "-", $input['start_date']) . " 00:00:00";
		$input['end_date']   = str_replace("/", "-", $input['end_date']). " 23:59:39";

		foreach ($an as $key) {

			$tmp = array();

			$tmp['id'] = $key->id;
			$tmp['plexuss_note']  = $key->plexuss_note;
			$tmp['login_as']   = '/sales/loginas/'.Crypt::encrypt($key->uid);
			$tmp['last_logged_in'] = $this->xTimeAgoHr($tp->getLastLoggedInDate($key->uid), date("Y-m-d H:i:s"));
			$tmp['company']    = $key->name;
			$tmp['agent_name'] = ucwords(ucfirst($key->fname) . " " . ucfirst($key->lname));
			$tmp['location']   = $key->country;
			$tmp['start_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $key->created_at)->toDateString();
			$tmp['start_date'] = date("d/m/Y", strtotime($tmp['start_date']));
			// $tmp['start_date'] = Carbon::createFromFormat('Y/m/d', $tmp['start_date'])->timezone('UTC');

			$queries = $ac->topDashboardQueries($key->id, $input['start_date'], $input['end_date']);

			$totals = $queries['agency_recruitment'];
			$totals = $totals->get();

			$app_cnt = $queries['app_cnt'];
			$app_cnt = $app_cnt->distinct('ucq.user_id')
							   ->count('ucq.user_id');


			$enroll_cnt = $queries['enroll_cnt'];
			$enroll_cnt = $enroll_cnt->distinct('r.user_id')
								     ->count('r.user_id');


			$tmp['removed']       = 0;
			$tmp['opportunities'] = 0;
			$tmp['applications']  = $app_cnt;
			$tmp['enrolled']      = $enroll_cnt;

			foreach ($totals as $key) {
				$tmp[$key->name] = $key->total;
			}

			$tmp['applications']  = $tmp['applications']  - ($key->application_pacing * $num_of_months);
			$tmp['opportunities'] = $tmp['opportunities'] - ($key->opportunity_pacing * $num_of_months);
			$tmp['enrolled']      = $tmp['enrolled']      - ($key->enrolled_pacing * $num_of_months);

			$ret[] = $tmp;
		}

		return $ret;
  	}

  	public function setAgencyPlexussNote(){

  		$input = Request::all();

		if (!isset($input['note']) && !isset($input['agency_id'])) {
			return 'Error! #1092';
		}

		$note = $input['note'];
		$agency_id = $input['agency_id'];

		$agency = Agency::where('id', $agency_id)->first();

		if (isset($agency)) {
			$agency->plexuss_note = $note;
			$agency->save();
		}

		$arr = $this->iplookup();

		if (isset($arr['time_zone'])) {
			// date_default_timezone_set($arr['time_zone']);
			$now = Carbon::now($arr['time_zone']);
			return $now->toTimeString();
		}

		return date('H:i');
  	}

	public function getScholarshipPopup(){
		$data[] = '';
		$input = Request::all();
		$data['gdata'] = $input["gdata"];
		$filter_section = 'admin.scholarship.ajax.scholarshipdata';
		return View($filter_section, $data);
	}


	public function getScholarshipTargeting(){
		$data[] = '';
		$input = Request::all();
		$data["section"] = $section =  $input['input'];
		$data['gdata'] = $input["gdata"];

		if(!isset($input['gdata']["id"])){
			$id = '';
		}else{
			$id = $input['gdata']["id"];
		}
		$filter_section = 'admin.scholarship.ajax.'.$section;
		$crf = new CollegeRecommendationFilters;
		$filters = $crf->getFiltersAndLogs_scholarship($id);

		//print_r($filters);
		$ret = array();
		$name = '';
		if (isset($filters)) {
			$temp = array();

			foreach ($filters as $key) {
				if ($name == '') {

					$name = $key->name;

					$temp = array();
					$temp['type'] = $key->type;
					$temp['category'] = $key->category;
					$temp['filter'] = $key->name;
					if (isset($key->val)) {
						$temp[$key->name] = array();
						$temp[$key->name][] = $key->val;
					}
				}elseif($name == $key->name){

					if (isset($key->val)) {
						$temp[$key->name][] = $key->val;
					}
					$name = $key->name;
				}else{
					$ret[] = $temp;

					$name = $key->name;

					$temp = array();
					$temp['type'] = $key->type;
					$temp['category'] = $key->category;
					$temp['filter'] = $key->name;
					if (isset($key->val)) {
						$temp[$key->name] = array();
						$temp[$key->name][] = $key->val;
					}

				}
			}

			$ret[] = $temp;
		}

		$data['filters'] = $ret;

		switch ($section) {
			case 'location':
				$zipCodes = new ZipCodes;
				$states = $zipCodes->getAllUsState();
				$countries = Country::all()->toArray();
				$tmp_ctry = array();
				$tmp_ctry[''] = 'Select a country...';
				foreach ($countries as $key => $value) {
					$tmp_ctry[$value['country_name']] = $value['country_name'];
				}

				$data['countries'] = $tmp_ctry;
				$states = $states;
				$cities = array('' => 'Select state first' );
				$data['states'] = $states;
				$data['cities'] = $cities;
				break;

            case 'scores':
              	$new_filters = array();
                foreach($data['filters'] as $key){
                    if(isset($key['sat_act'])){
                        foreach($key['sat_act'] as $i){
                            $val = explode(',', $i);
                            if($val[0] == 'sat'){
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'satMin_filter';
                                    $temp['satMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '2400'){
                                    $temp = array();
                                    $temp['filter'] = 'satMax_filter';
                                    $temp['satMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                            else{
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'actMin_filter';
                                    $temp['actMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '36'){
                                    $temp = array();
                                    $temp['filter'] = 'actMax_filter';
                                    $temp['actMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                        }
                    }elseif(isset($key['ielts_toefl'])){
                        foreach($key['ielts_toefl'] as $i){
                            $val = explode(',', $i);
                            if($val[0] == 'ielts'){
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'ieltsMin_filter';
                                    $temp['ieltsMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '9'){
                                    $temp = array();
                                    $temp['filter'] = 'ieltsMax_filter';
                                    $temp['ieltsMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }else{
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'toeflMin_filter';
                                    $temp['toeflMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '120'){
                                    $temp = array();
                                    $temp['filter'] = 'toeflMax_filter';
                                    $temp['toeflMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                        }
                    }elseif(isset($key['gpa_filter'])){
                        $val = explode(',', $key['gpa_filter'][0]);
                        if ($val[0] != '0'){
                            $temp = array();
                            $temp['filter'] = 'gpaMin_filter';
                            $temp['gpaMin_filter'] = array($val[0]);
                            $new_filters[] = $temp;
                        }
                        if ($val[1] != '4'){
                            $temp = array();
                            $temp['filter'] = 'gpaMax_filter';
                            $temp['gpaMax_filter'] = array($val[1]);
                            $new_filters[] = $temp;
                        }
                    }
                }
                $data['filters'] = $new_filters;
                break;

			case 'major':
			case 'majorDeptDegree':
				$dep = new Department;
				$useIds = true;
				$dep_name = $dep->getAllDepartments($useIds);

				$departments = $dep_name;

				$majors = array('' => 'Select department first' );
				$data['departments'] = $departments;

				break;
			case 'uploads':
				$tmpArr = $data['filters'];

				if(isset($tmpArr) && !empty($tmpArr[0]) && isset($tmpArr[0]['uploads'])){
					$tmp = array();
					$tmpArr = $data['filters'][0]['uploads'];

					foreach ($tmpArr as $key => $value) {
						$tmp[$value] = true;
					}

					$tmpArr = $tmp;
					$data['filters'][0]['uploads'] = $tmpArr;
				}

				break;

			case 'desiredDegree':
				$degree = new Degree;

				$degree = $degree::all();

				$data['degree'] = $degree;

				$tmp_arr = $data['filters'];

				if(isset($tmp_arr) && !empty($tmp_arr[0])){
					$tmp = array();
					$tmp_arr = $data['filters'][0]['desiredDegree'];

					foreach ($tmp_arr as $key => $value) {
						$tmp[$value] = true;
					}

					$tmp_arr = $tmp;
					$data['filters'][0]['desiredDegree'] = $tmp_arr;
				}

				break;

			case 'demographic':
				//get ethnicity
				if (Cache::has(env('ENVIRONMENT') .'_all_eth')) {
					$eth = Cache::get(env('ENVIRONMENT') .'_all_eth');
				}else{
					$eth = new Ethnicity;
					$eth = $eth->getAllUsEthnicities();
					Cache::put(env('ENVIRONMENT') .'_all_eth', $eth, 120);
				}
				$data['ethnicities'] = $eth;

				//get religion
				if (Cache::has(env('ENVIRONMENT') .'_all_religions')) {
					$religions = Cache::get(env('ENVIRONMENT') .'_all_religions');
				}else{
					$religions = new Religion;
					$religions = $religions->getAllUsReligions();
					Cache::put(env('ENVIRONMENT') .'_all_religions', $religions, 120);
				}
				$data['religions'] = $religions;

				$filters = $data['filters'];
				$arr = array();

				if( !empty($data['filters'][0]) ){
					foreach ($filters as $key) {
						$f = $key['filter'];
						//var_dump($f);
						//var_dump($key);

						$num = isset($key[$f]) ? $key[$f] : array('');

						if ($key['filter'] == 'include_eth_filter') {
							$arr[$key['filter']] = $num;
							$arr['include_eth_filter_type'] = $key['type'];
						}elseif ($key['filter'] == 'include_rgs_filter') {
							$arr[$key['filter']] = $num;
							$arr['include_rgs_filter_type'] = $key['type'];
						}else{
							$arr[$key['filter']] = $num[0];
						}

					}
				}

				$data['filters'] = $arr;

				break;
			case 'militaryAffiliation':
				$tmpFilters = $data['filters'];
				if (Cache::has(env('ENVIRONMENT') .'_all_militaryAff')) {
					$ma = Cache::get(env('ENVIRONMENT') .'_all_militaryAff');
				}else{
					$ma = new MilitaryAffiliation;
					$ma = $ma->getAll();
					Cache::put(env('ENVIRONMENT') .'_all_militaryAff', $ma, 120);
				}
				$data['militaryAffiliation'] = $ma;
				// $data['filters'][0]['militaryAffiliation'] = $tmpFilters;
				break;
			case 'startDateTerm':
				$dates = array();
				$today = Carbon::today();
				$month = $today->month;
				$yr = $today->year;

				$fall = '';
				$spring = '';
				$dates[''] = 'Select...';
				for ($i = 0; $i < 7; $i++) {
					//if the current month is past fall season already, skip it
					if( $i == 0 ){
						if( $month < 7 ){
							$fall = 'Fall ' . ($yr + $i);
							$dates[$fall] = $fall;
						}
					}else{
						$fall = 'Fall ' . ($yr + $i);
						$dates[$fall] = $fall;
					}

					$spring = 'Spring ' . ($yr + $i);
					$dates[$spring] = $spring;
				}

				$dates = $dates;
				$data['dates'] = $dates;
				break;
			case 'financial':
				$opts = array('0.00','0 - 5,000','5,000 - 10,000','10,000 - 20,000','20,000 - 30,000','30,000 - 50,000', '50,000');
				$fin = array();

				$fin[''] = 'Select...';
				for ($i=0; $i < count($opts); $i++) {
					$tmp = explode('-', $opts[$i]);

					if( count($tmp) > 1 ){
						$fin[$opts[$i]] = '$'.trim($tmp[0]).' - $'.trim($tmp[1]);
					}else{
						if( $tmp[0] == '0.00' ){
							$fin[$opts[$i]] = '$0';
						}elseif( $i == 6 ){
							$fin[$opts[$i]] = '$'.$tmp[0].'+';
						}else{
							$fin[$opts[$i]] = '$'.trim($tmp[0]);
						}
					}
				}

				$data['financial_options'] = $fin;
				break;
			case 'typeofschool':
				break;
			default:
				# code...
				break;
		}

		return View($filter_section, $data);
	}


	public function getScholarshipFilter(){
		$input = Request::all();
		$id = $input["id"];

		$crf = new CollegeRecommendationFilters;
		$filters = $crf->getFiltersAndLogs_scholarship($id);

		$ret = array();
		$name = '';
		if (isset($filters)) {
			$temp = array();

			foreach ($filters as $key) {
				if ($name == '') {

					$name = $key->name;

					$temp = array();
					$temp['type'] = $key->type;
					$temp['category'] = $key->category;
					$temp['filter'] = $key->name;
					if (isset($key->val)) {
						$temp[$key->name] = array();
						$temp[$key->name][] = $key->val;
					}
				}elseif($name == $key->name){

					if (isset($key->val)) {
						$temp[$key->name][] = $key->val;
					}
					$name = $key->name;
				}else{
					$ret[] = $temp;

					$name = $key->name;

					$temp = array();
					$temp['type'] = $key->type;
					$temp['category'] = $key->category;
					$temp['filter'] = $key->name;
					if (isset($key->val)) {
						$temp[$key->name] = array();
						$temp[$key->name][] = $key->val;
					}

				}
			}

			$ret[] = $temp;
		}

		$data['filters'] = $ret;
		//scores
		/*$new_filters = array();
        foreach($data['filters'] as $key){
                    if(isset($key['sat_act'])){
                        foreach($key['sat_act'] as $i){
                            $val = explode(',', $i);
                            if($val[0] == 'sat'){
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'satMin_filter';
                                    $temp['satMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '2400'){
                                    $temp = array();
                                    $temp['filter'] = 'satMax_filter';
                                    $temp['satMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                            else{
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'actMin_filter';
                                    $temp['actMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '36'){
                                    $temp = array();
                                    $temp['filter'] = 'actMax_filter';
                                    $temp['actMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                        }
                    }elseif(isset($key['ielts_toefl'])){
                        foreach($key['ielts_toefl'] as $i){
                            $val = explode(',', $i);
                            if($val[0] == 'ielts'){
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'ieltsMin_filter';
                                    $temp['ieltsMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '9'){
                                    $temp = array();
                                    $temp['filter'] = 'ieltsMax_filter';
                                    $temp['ieltsMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }else{
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'toeflMin_filter';
                                    $temp['toeflMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '120'){
                                    $temp = array();
                                    $temp['filter'] = 'toeflMax_filter';
                                    $temp['toeflMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                        }
                    }elseif(isset($key['gpa_filter'])){
                        $val = explode(',', $key['gpa_filter'][0]);
                        if ($val[0] != '0'){
                            $temp = array();
                            $temp['filter'] = 'gpaMin_filter';
                            $temp['gpaMin_filter'] = array($val[0]);
                            $new_filters[] = $temp;
                        }
                        if ($val[1] != '4'){
                            $temp = array();
                            $temp['filter'] = 'gpaMax_filter';
                            $temp['gpaMax_filter'] = array($val[1]);
                            $new_filters[] = $temp;
                        }
                    }
                }
        $data['filters'] = $new_filters;

		//Uploads
		$tmpArr = $data['filters'];
		if(isset($tmpArr) && !empty($tmpArr[0]) && isset($tmpArr[0]['uploads'])){
			$tmp = array();
			$tmpArr = $data['filters'][0]['uploads'];
			foreach ($tmpArr as $key => $value) {
				$tmp[$value] = true;
			}

			$tmpArr = $tmp;
			$data['filters'][0]['uploads'] = $tmpArr;
		}

		//desiredDegree
		$tmp_arr = $data['filters'];
		if(isset($tmp_arr) && !empty($tmp_arr[0])){
			$tmp = array();
			$tmp_arr = $data['filters'][0]['desiredDegree'];

			foreach ($tmp_arr as $key => $value) {
				$tmp[$value] = true;
			}

			$tmp_arr = $tmp;
			$data['filters'][0]['desiredDegree'] = $tmp_arr;
		}

		//demographic
		$filters = $data['filters'];
		$arr = array();

		if( !empty($data['filters'][0]) ){
			foreach ($filters as $key) {
				$f = $key['filter'];
				$num = isset($key[$f]) ? $key[$f] : array('');

				if ($key['filter'] == 'include_eth_filter') {
					$arr[$key['filter']] = $num;
					$arr['include_eth_filter_type'] = $key['type'];
				}elseif ($key['filter'] == 'include_rgs_filter') {
					$arr[$key['filter']] = $num;
					$arr['include_rgs_filter_type'] = $key['type'];
				}else{
					$arr[$key['filter']] = $num[0];
				}
			}
		}

		$data['filters'] = $arr;*/

		return $data;
	}

    public function removePixelTestAdClicks() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $twentyFourhoursAgo = Carbon::now()->subHours(24);

        $user_id = $data['user_id'];

        $input = Request::all();
        $adLink = isset($input['adLink']) ? $input['adLink'] : null;

        $ip = $this->iplookup();

        if (!isset($ip['ip'])) return 'failed looking up IP';

        if (!isset($adLink) || !isset($adLink['utm_source']) || !isset($adLink['company']))
                return 'failed missing adLink data';

        $removeAdClicks = AdClick::where('utm_source', '=', $adLink['utm_source'])
                                 ->where('company', '=', $adLink['company'])
                                 ->where('user_id', '=', $user_id)
                                 ->where('ip', '=', $ip['ip'])
                                 ->where('created_at', '>=', $twentyFourhoursAgo)
                                 ->delete();

        return 'success';
    }

    public function checkPixelTracked() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $user_id = $data['user_id'];

        $input = Request::all();
        $adLink = isset($input['adLink']) ? $input['adLink'] : null;

        $ip = $this->iplookup();
        //73.223.90.73

        if (!isset($ip['ip'])) return 'failed looking up IP';

        if (!isset($adLink) || !isset($adLink['utm_source']) || !isset($adLink['company']) || !isset($adLink['cid'])) {
                return 'failed missing adLink data';
        }

        $pixelTracked = AdClick::on('rds1')
                               ->select('pixel_tracked', 'paid_client')
                               ->where('utm_source', '=', $adLink['utm_source'])
                               ->where('company', '=', $adLink['company'])
                               ->where('user_id', '=', $user_id)
                               ->where('ip', '=', $ip['ip'])
                               ->orderBy('id', 'desc')
                               ->first();

       $values = [
           'user_id' => $user_id,
           'cid' => $adLink['cid'],
           'company' => $adLink['company']
       ];

       if (isset($pixelTracked)) {
            $values['pixel_tracked'] = $pixelTracked->pixel_tracked;
            $values['paid_client'] = $pixelTracked->paid_client;

            PixelTrackedTesting::create($values);
       }

       return 'success';
    }

    public function getPixelTrackedTestingLogs() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $twentyFourhoursAgo = Carbon::now()->subHours(24);

        $user_id = $data['user_id'];

        $logs = PixelTrackedTesting::on('rds1')
                                   ->where('user_id', '=', $user_id)
                                   ->where('created_at', '>=', $twentyFourhoursAgo)
                                   ->orderBy('id', 'desc')
                                   ->get();
        return $logs->toArray();
    }

    // Social Methods Start here
    public function saveNewsfeedPost(){
    	$input = Request::all();
    	$sc = new SocialController();

		$pattern = '~[a-z]+://\S+~';

		if($num_found = preg_match_all($pattern, $input['post_text'], $out))
		{
		  $input['shared_link']  = $out[0][0];
		}
		$input['posted_by_plexuss'] = 1;

    	return $sc->savePost($input);
    }

    // Edit a sales post
    public function editNewsfeedPost(){
    	$input = Request::all();
    	$sc = new SocialController();

		$pattern = '~[a-z]+://\S+~';

		if($num_found = preg_match_all($pattern, $input['post_text'], $out))
		{
		  $input['shared_link']  = $out[0][0];
		}
		$input['posted_by_plexuss'] = 1;

    	return $sc->savePost($input);
    }

    // Duplicate sales post
    public function duplicateNewsfeedPost(){
    	$input = Request::all();
    	$sc = new SocialController();
    	$input['sales_pid'] = 470;
    	return $sc->dupPost($input);
    }

    public function getPosts(){
    	$input = Request::all();
    	$sc = new SocialController();

    	return $sc->getSalesPosts($input);
    }

    public function setAdminRecommendationFilter($tab_name = null){
		if ($tab_name == null) {
			return;
		}

		$input = Request::all();
		$now = Carbon::now();

		if (!isset($input['sales_pid']) || $input['sales_pid'] == '') {
			$post = new Post;

			$post->created_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');
			$post->updated_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');

			$post->save();

			$input['sales_pid'] = $post->id;
			$input['post_id']   = $post->id;
		}else{
			$input['post_id'] = $input['sales_pid'];
		}

		$ac = new AjaxController;
		$ret = $ac->setAdminRecommendationFilter($tab_name, $input);

		$ret['sales_pid'] = $input['sales_pid'];

		return $ret;
	}

    public function testPost(){
    	$user_id = 93;
    	$post_id = 510;
    	$crc = new CollegeRecommendationController;

    	$ret = $crc->filterThisPostForThisUser($user_id, $post_id);

    	dd($ret);
    }
    // Social Methods End here
}
