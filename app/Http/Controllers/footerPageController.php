<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, Session, Validator;;
use App\User, App\PlexussAdmin, App\ScholarshipSubmission, App\ScholarshipSubmissionFooter;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class footerPageController extends Controller
{
    //
    	public function collegeprep(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'college-prep';

		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			//$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			//$admin = array_shift($admin);


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

			//$data['faq'] = Faq::where('type', '=', 'general')->get()->toArray();


			$data['username'] = $user['fname'].' '.$user['lname'];
		    $data['profile_img_loc'] = $src;
		}
		/*
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			exit;
		 */
		return View('public.footerpages.college-prep', $data);

	}

	public function collegeSubmission(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		if (isset($input)) {
            $arr = $this->iplookup();
            if (!Session::has('college_submission_params')) {
                Session::put('college_submission_params', $input);
            }
            if (!Cache::has(env('ENVIRONMENT') .'_'.'college_submission_params_'.$arr['ip'])) {
                Cache::put(env('ENVIRONMENT') .'_'.'college_submission_params_'.$arr['ip'], $input, 5);
            }
        }

        // dd(Session::get('signup_params'));

		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'college-submission';

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
		/*
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			exit;
		 */
		return View('public.footerpages.college-submission', $data);

	}

	public function scholarshipSubmission(){
    //Template base arrays
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();



    $data['title'] = 'Plexuss Help';
    $data['currentPage'] = 'scholarship-submission';

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
    return View('public.footerpages.scholarship-submission-link', $data);
  }

  	public function scholarshipStepOne() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        if (isset($data['signed_in']) && $data['signed_in'] == 1) {
            Session::put('last_step', "1");
            return redirect('/scholarship-info');
        }

		Session::put('last_step', "");
		$data["fstep"] = 1;
    	$input = Request::all();

		if( isset($input['redirect']) ){
			Session::put('redirect_from_signin', $input['redirect']);
		}
   		$data['title'] = 'Plexuss Scholarship Submision';
    	$data['currentPage'] = 'signup';

		return View( 'public.footerpages.scholarship-step-one', $data);
  	}

	public function scholarshipStepTwo() {
		if(Session::get('last_step')== 3){
			return redirect('signin');
		}elseif(Session::get('last_step')!= 1){
			return redirect('scholarship-get-started');
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$user_id = $data['user_id'];
	   	$input = Request::all();
		if( isset($input['redirect']) ){
			Session::put('redirect_from_signin', $input['redirect']);
		}
		$data['title'] = 'Plexuss Scholarship Submision';
		$data['currentPage'] = 'scholarship-submission';
		$data["fstep"] = 2;
		$data['date'] = date('Y-m-d', strtotime('+1 day'));

		if(isset($input['footer_step']) && $input['footer_step']==2){
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
			} else {

			}

			$filter = array(
				'scholarshiptitle' => 'required',
				'contact' => 'required',
				'phone' => 'alpha_dash|required',
				'email' => 'email|required',
				'applicationDeadline' => 'required',
				'numberofawards' => 'required',
				'maximumAmount' => 'required',
				'websiteAddress' => '',
				'address' => 'required',
				'Address2' => '',
				'city' => 'required',
				'state' => 'required',
				'zip' => 'regex:/^\d{5}(-\d{4})?$/',
				'scholarshipDescription' => 'required'
			);

			$validator = Validator::make(Request::all(), $filter );

			if ($validator->fails()){
				return redirect('scholarship-info')->withErrors($validator)->withInput();
			}
			$random_token = str_random( 20 );

			$scholarshipData = array(
				'scholarship_title' => Request::get( 'scholarshiptitle' ),
				'contact' => Request::get( 'contact' ),
				'phone' => Request::get( 'phone' ),
				'fax' => Request::get( 'fax' ),
				'email' => Request::get( 'email' ),
				'deadline' => Request::get( 'applicationDeadline' ),
				'number_of_awards' => Request::get( 'numberofawards' ),
				'max_amount' => preg_replace("/[^a-z0-9.]/i", "", Request::get( 'maximumAmount' )),
				'website' => Request::get( 'websiteAddress' ),
				'address' => Request::get( 'address' ),
				'Address2' => Request::get( 'Address2' ),
				'city' => Request::get( 'city' ),
				'state' => Request::get( 'state' ),
				'zip' => Request::get( 'zip' ),
				'scholarship_description' => Request::get( 'scholarshipDescription' ),
				'random_token' => $random_token,
				'user_id' => Auth::user()->id,
			);

			$ScholarshipSubmission = new ScholarshipSubmission($scholarshipData);
			$data['rndtoken'] = $random_token;
			$ScholarshipSubmission->save();
			//$this->sendemailalert($this->emailSubject, array('jp.novin@plexuss.com') , $scholarshipData);
			Session::put('last_step', "2");
			return redirect('scholarship-intrest');
		}

		return View( 'public.footerpages.scholarship-step-two', $data);
  }

	public function scholarshipStepThree($type ="") {

		if(Session::get('last_step')== 4){
			return redirect('signin');
		}elseif(Session::get('last_step')!= 2){
			return redirect('scholarship-get-started');
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

    	$input = Request::all();
		if( isset($input['redirect']) ){
			Session::put('redirect_from_signin', $input['redirect']);
		}
		if( !isset(Auth::user()->id)){
			return redirect('scholarship-get-started');
		}
    	$data['title'] = 'Plexuss Scholarship Submision';
    	$data['currentPage'] = 'signup';
		$data["fstep"] = 3;

		if($type!=''){
			$sfdata = array("interested_service" =>$type);
			$ScholarshipSubmission = new ScholarshipSubmissionFooter();
			$ScholarshipSubmission->update_scholarship_footer($sfdata,Auth::user()->id);

			$ss = ScholarshipSubmission::on('rds1')->where('user_id', Auth::user()->id)->first();
			$user = User::on('rds1')->where('id', Auth::user()->id)
									->select('fname', 'lname', 'email')
									->first();

			if (isset($ss)) {
				$mda = new MandrillAutomationController();
				$mda->alertInternalScholarshipSubmission($ss, $user);

				$reply_email = "support@plexuss.com";
				$template_name = "plexuss_scholarship_email_user_thankyou";

				$params = array();
				$params['SERVICE'] = ucwords(strtolower($ss->interested_service));

				$mda->generalEmailSend($reply_email, $template_name, $params, $user->email );
			}
			Session::put('last_step', "3");
			return redirect('scholarship-thankyou');
		}

		return View( 'public.footerpages.scholarship-step-three', $data);
  }

  	public function scholarshipStepFour() {
		if(Session::get('last_step')!= 3){
			return redirect('scholarship-get-started');
		}
   	 	$input = Request::all();
		if( isset($input['redirect']) ){
			Session::put('redirect_from_signin', $input['redirect']);
		}
		$data['title'] = 'Plexuss Scholarship Submision';
		$data['currentPage'] = 'signup';
		$data["fstep"] = 4;
		Session::put('last_step', "4");
		return View( 'public.footerpages.scholarship-step-four', $data);
  }

	public function scholarshipSubmissionForm() {
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'scholarship-submission';

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
		return View('public.footerpages.scholarship-submission', $data);

	}


	/*****************************************
	 *=============COMPANY PAGES==============
	 *****************************************/
	public function about(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'about';

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

//            if (isset($emailSuppressionList)) {
//                if ($emailSuppressionList['uid'] != '') {
                    array_push( $data['alerts'],
                        array(
                            'type' => 'hard',
                            'dur' => '10000',
                            'msg' => '<span class=\"pls-confirm-msg subcribe-msg\">Oops, seems like you are on our unsubscribe list. In order to get the best experience from Plexuss,</span> <span id=\"'.$emailSuppressionList['uid'].'\" class=\"subscribe-now\">Subscribe Now</span> <div class=\"loader loader-hidden\"></div>'
                        )
                    );
//                }
//            }

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
		return View('public.footerpages.about', $data);

	}

	public function team(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'team';

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
		return View('public.footerpages.team', $data);

	}

	public function contact(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'contact';

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

		return View('public.footerpages.contact', $data);

	}
	/*****************************************
	 *===========END COMPANY PAGES============
	 *****************************************/

	/*****************************************
	 *==============BOTTOM PAGES==============
	 *****************************************/
	public function tos(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'terms-of-service';

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
		return View('public.footerpages.termsofservice', $data);

	}

	public function privacyPolicy(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'privacy-policy';

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
		return View('public.footerpages.privacypolicy', $data);
	}

	public function txtPrivacyPolicy(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Text Privacy Policy';
		$data['currentPage'] = 'privacy-policy';

		$src="/images/profile/default.png";

		$data['username'] = $data['fname'].' '.$data['lname'];
		$data['profile_img_loc'] = $src;

		return View('public.footerpages.txtprivacypolicy', $data);
	}
}
