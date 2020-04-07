<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NotificationTopNav;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /***********************************************************************
	 *======================= NOTIFICATION INDEX PAGE ======================
	 ***********************************************************************
	 * Returns data to the notifications view.
	 */

	public function index($is_api = null){

		// Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['currentPage'] = 'notifications';

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}

		$ntn = new NotificationTopNav;

		$tmp = $ntn->getMyNotifications($data, null, $is_api);

		//$tmp['data'] = array_reverse($tmp['data']);
		$data['notifications'] =  $tmp;

		if (isset($is_api)) {
			return $tmp;
		}

		return View( 'private.notifications.index', $data );
	}

	public function create( $name = null, $type = null, $command = null, $param = null, $user_id = null, $type_id= null, $created_at = null, $email_sent = null, $post_id = null ){

		$name = ucwords(strtolower($name));

		$arr = $this->getNotification($name, $type, $command, $param, $user_id, $post_id);

		$is_read = 0;
		if (isset($arr)) {

			if (isset($email_sent)) {
				$email_sent = '1';
			}else{
				$email_sent = '0';
			}

			$attr = array('type' => $type, 'type_id' => $type_id, 'submited_id' => $user_id,
						  'command' => $command, 'msg' => $arr['msg']);

			$vals = array('type' => $type, 'type_id' => $type_id, 'submited_id' => $user_id,
						 'msg' => $arr['msg'], 'img' => $arr['img'], 'link' => $arr['link'],
						 'icon_img_route' => $arr['icon_img_route'],
						 'name' => $name, 'is_read' => $is_read, 'command' => $command, 'email_sent' => $email_sent);

			if (isset($created_at)) {
				$vals['created_at'] = $created_at;
				$vals['updated_at'] = $created_at;
			}else{
				$vals['created_at'] = Carbon::now();
				$vals['updated_at'] = Carbon::now();
			}

			// $c = new Controller;

			// $updateScore = $c->customUpdateOrCreate(new NotificationTopNav, $attr, $vals);
			$updateScore = NotificationTopNav::create($vals);
			return $updateScore->id;
		}

		// $this->sendAutomatedEmail($name, $type, $command, $param, $type_id);

	}

	private function sendAutomatedEmail( $name = null, $type = null, $command = null, $param = null, $type_id = null, $user_id = null ) {

		if ( $type == "user" ) {
			switch ( $command ) {
			case '1':

				break;

			case '2':
				// $mda = new MandrillAutomationController;
				// $mda->collegeAgreedToRecruitYou($name, $type_id);

				break;
			case '5':
				// $mda = new MandrillAutomationController;
				// $mda->collegeAgreedToRecruitYou($name, $type_id);
				break;
			}
		}elseif ( $type == "college" ) {

			switch ($command) {
				case '1':
					break;
				default:
					# code...
					break;
			}

		}
	}

	private function getNotification( $name = null, $type = null, $command = null, $param = null, $user_id = null, $post_id = null ) {

		$arr = array();
		if ( $type == null || $command == null ) {
			return $arr;
		}

		// $student_profile_photo = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png";
		// if (isset($user_id)) {
		// 	$user = DB::connection('rds1')->table('users as u')
		// 								  ->select(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as student_profile_photo'))
		// 								  ->where('id', $user_id)
		// 								  ->first();

		// 	if (isset($user->student_profile_photo)) {
		// 		$student_profile_photo = $user->student_profile_photo;
		// 	}
		// }
		//dd($type. ' '. $command);
		if ( $type == "user" ) {
			switch ( $command ) {
			case '1':
				$arr['img'] = "notify_image notify_view";
				$arr['msg'] = "viewed your profile";
				$arr['link'] = "/portal/collegesviewedprofile";
				$arr['icon_img_route'] = "/social/images/notifications/Viewed.svg";
				break;

			case '2':
				$arr['img'] = "notify_image notify_recruit";
				$arr['msg'] = "has recruited you";
				$arr['link'] = "/portal/collegesrecruityou";
				$arr['icon_img_route'] = "/social/images/notifications/recruit you.svg";
				break;
			case '3':
				$arr['img'] = "notify_image notify_recruit";
				$arr['msg'] = "wants to recruit you. Are you interested?";
				$arr['link'] = "/portal/collegesrecruityou";
				$arr['icon_img_route'] = "/social/images/notifications/recruit you.svg";
				break;
			case '4':
				$arr['img'] = "";
				$arr['msg'] = " <b>".$name."</b> admission deadline in ".$param." days";
				$arr['link'] = "";
				$arr['icon_img_route'] = "/social/images/notifications/";
				break;
			case '5':
				$arr['img'] = "notify_image notify_recruit";
				$arr['msg'] = "wants to recruit you. Are you interested?";
				$arr['link'] = "/portal/messages";
				$arr['icon_img_route'] = "/social/images/notifications/recruit you.svg";
				break;
			case '6':
				$arr['img'] = "";
				$arr['msg'] = " <b>".$name."</b> wants to be your friend!";
				$arr['link'] = "";
				$arr['icon_img_route'] = "/social/images/notifications/recruit you.svg";
				break;
			case '7':
				$arr['img'] = "";
				$arr['msg'] = " <b>".$name."</b> You have ".$param. " recommendations!";
				$arr['link'] = "";
				$arr['icon_img_route'] = "/social/images/notifications/Recommend-green.svg";
				break;
			case '8':
				$arr['img'] = "notify_image notify_recruit";
				$arr['msg'] = "has recruited you";
				$arr['link'] = "#";
				$arr['icon_img_route'] = "/social/images/notifications/recruit you.svg";
				break;
			case '9':
				$arr['img'] = "notify_image notify_view";
				$arr['msg'] = "viewed your profile";
				$arr['link'] = "#";
				$arr['icon_img_route'] = "/social/images/notifications/Viewed.svg";
				break;
			case '10':
				$arr['img'] = "notify_image notify_comment";
				$arr['msg'] = "commented on your post";
				$arr['link'] = "/post/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '11':
				$arr['img'] = "notify_image notify_user_applied";
				$arr['msg'] = "liked your post";
				$arr['link'] = "/post/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '12':
				$arr['img'] = "notify_image accept_request";
				$arr['msg'] = "accepted your friend request";
				$arr['link'] = "/social/networking/connection";
				$arr['icon_img_route'] = "/social/images/notifications/accept request.svg";
				break;
			case '13':
				$arr['img'] = "notify_image connect";
				$arr['msg'] = "sent you a friend request";
				$arr['link'] = "/social/networking/requests";
				$arr['icon_img_route'] = "/social/images/notifications/connect.svg";
				break;
			case '14':
				$arr['img'] = "notify_image notify_view";
				$arr['msg'] = "shared your post";
				$arr['link'] = "/post/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '15':
				$arr['img'] = "notify_image notify_comment";
				$arr['msg'] = "commented on your article";
				$arr['link'] = "/social/article/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '16':
				$arr['img'] = "notify_image notify_user_applied";
				$arr['msg'] = "liked your article";
				$arr['link'] = "/social/article/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '17':
				$arr['img'] = "notify_image notify_view";
				$arr['msg'] = "shared your article";
				$arr['link'] = "/social/article/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '18':
				$arr['img'] = "notify_image notify_user_applied";
				$arr['msg'] = "has liked a post you shared";
				$arr['link'] = "/post/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '19':
				$arr['img'] = "notify_image notify_user_applied";
				$arr['msg'] = "has liked an article you shared";
				$arr['link'] = "/social/article/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '20':
				$arr['img'] = "notify_image notify_comment";
				$arr['msg'] = "commented on a post you shared";
				$arr['link'] = "/post/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '21':
				$arr['img'] = "notify_image notify_comment";
				$arr['msg'] = "commented on an article you shared";
				$arr['link'] = "/social/article/".$post_id;
				$arr['icon_img_route'] = "users_image";
				break;
			case '22':
				$arr['img'] = "notify_image notify_comment";
				$arr['msg'] = "sent you a message";
				$arr['icon_img_route'] = "users_image";
			case '23':
				$arr['img'] = "notify_image notify_user_applied";
				$arr['msg'] = "liked your comment";
				$arr['link'] = "/post/".$post_id;
				$arr['icon_img_route'] = "users_image";
			default:
				// code...
				break;
			}

		}elseif ( $type == "college" ) {

			switch ($command) {
				case '1':

					$arr['img'] = "notify_image notify_friend";
					$arr['msg'] = "wants to get recruited";
					$arr['link'] = "/admin/inquiries";
					break;

				case '2':

					$arr['img'] = "notify_image notify_handshake";
					$arr['msg'] = "is now a handshake";
					$arr['link'] = "/admin/inquiries/approved";
					break;

				case '3':

					$arr['img'] = "notify_image notify_user_applied";
					$arr['msg'] = "has applied to your school";
					$arr['link'] = "/admin/inquiries/approved";
					break;

				case '4':

					$arr['img'] = "notify_image notify_user_applied";
					$arr['msg'] = "has applied to your school";
					$arr['link'] = "/admin/inquiries";
					break;

				case '5':

					$arr['img'] = "notify_image notify_user_applied";
					$arr['msg'] = "has applied to your school";
					$arr['link'] = "/admin/inquiries/prescreened";
					break;

				default:
					# code...
					break;
			}

		}elseif ( $type == "agency" ) {

			switch ($command) {
				case '1':

					$arr['img'] = "notify_image notify_friend";
					$arr['msg'] = "wants to get recruited";
					$arr['link'] = "/agency/inquiries";
					break;

				default:
					# code...
					break;
			}

		}

		return $arr;
	}
}
