<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Faq, App\PlexussAdmin, App\User;


class helpController extends Controller
{

    public function index() {
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'help';

		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);


			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}

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

			
			$data['username'] = $user['fname'].' '.$user['lname'];
		    $data['profile_img_loc'] = $src;
		}
		$data['help_heading'] = "Getting Started";
		$data['help_page'] = 1;

		return View('public.footerpages.gettingStarted', $data);
	}

	public function faq($faq_type) {
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'faqs';

		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);


			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}
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


			
			$data['username'] = $user['fname'].' '.$user['lname'];
		    $data['profile_img_loc'] = $src;
		}
		//Make help heading
		if(($faq_type) == 'general'){
			$help_heading = 'Frequently Asked Questions';
		}
		else{
			$help_heading = ucfirst($faq_type) . " FAQ";
		}
		$data['help_heading'] = $help_heading;
		
		// get FAQ question/answer array
		$data['faq'] = Faq::where('type', '=', $faq_type)->get()->toArray();
		// $slugger = new Slugger();

		foreach($data['faq'] as $key => $val){
			$slug = str_replace(" ", "-", $val['question']);
			$data['faq'][$key]['anchor'] = $slug;
		}

		$data['faq_question_heading'] = strtoupper($faq_type);
		$data['help_page'] = 1;
		/*
		echo "<pre>";
		var_dump($data);
		echo "</pre>";
		exit;
		 */
		return View('public.footerpages.faq', $data);
	}

	public function helpfulVideos(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'help';

		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);


			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}

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

			
			$data['username'] = $user['fname'].' '.$user['lname'];
		    $data['profile_img_loc'] = $src;
		}
		$data['help_page'] = 1;
		$data['help_heading'] = "Helpful Videos";

		return View('public.footerpages.helpful_videos', $data);
		
	}
}
