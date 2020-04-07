<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Illuminate\Http\Request;

class AdminFeedsController extends Controller
{
    /*==================================================
	 *================FEED SECTION BEGINS===============
	 *==================================================*/
	public function feed(){
		if(!Auth::check()){
			App::abort(404);
		}

		$user = User::find( Auth::user()->id );
		$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
		$admin = array_shift($admin);
		if(empty($admin)){
			App::abort(404);
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['admin'] = $admin;

        /* check user in email suppression list */
        $emailSuppressionList = EmailSuppressionList::where('uid', $user->id)->first();

        if (isset($emailSuppressionList)) {
            if ($emailSuppressionList['uid'] != '') {
                array_push( $data['alerts'],
                    array(
                        'type' => 'hard',
                        'dur' => '10000',
                        'msg' => '<span class=\"pls-confirm-msg subcribe-msg\">Oops, seems like you are on our unsubscribe list. In order to get the best experience from Plexuss,</span> <span id=\"'.$emailSuppressionList['uid'].'\" class=\"subscribe-now\">Subscribe Now</span> <div class=\"loader loader-hidden\"></div>'
                    )
                );
            }
        }

		if ( !$user->email_confirmed ) {
			array_push( $data['alerts'], 
				array(
					'img' => '/images/topAlert/envelope.png',
					'type' => 'hard',
					'dur' => '10000',
					'msg' => '<span class=\"pls-confirm-msg\">Please confirm your email to gain full access to Plexuss tools.</span> <span class=\"go-to-email\" data-email-domain=\"'.$data['email_provider'].'\"><span class=\"hide-for-small-only\">Go to</span><span class=\"show-for-small-only\">Confirm</span> Email</span> <span> | <span class=\"resend-email\">Resend <span class=\"hide-for-small-only\">Email</span></span></span> <span> | <span class=\"change-email\" data-current-email=\"'.$data['email'].'\">Change email <span class=\"hide-for-small-only\">address</span></span></span>'
				)
			);
		}
		if(Session::has('topAlerts')){
			$session_alerts = Session::get('topAlerts', 'default');
			Session::forget('topAlerts');
			foreach($session_alerts as $top_alert){
				array_push( $data['alerts'], $top_alert);
			}
		}
		$data['title'] = 'Plexuss Admin Panel';
		$data['currentPage'] = 'admin';
		$token = AjaxToken::where('user_id', '=', $user->id)->first();

		if(!$token) {
			Auth::logout();
			return Redirect::to('/');
		}

		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;

		$data['ajaxtoken'] = $token->token;

		if($user->showFirstTimeHomepageModal) {
			$data['showFirstTimeHomepageModal'] = $user->showFirstTimeHomepageModal;
		} 

		
		$data['admin_title'] = "Add College";
		return View('admin.feed', $data);
		
	}

	/*==================================================
	 *================FEED SECTION ENDS=================
	 *==================================================*/
}
