<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Illuminate\Support\Facades\Auth;
use Request, Session, DB, Validator;
use App\ContactUs, App\ScholarshipSubmission, App\CareerSubmit, App\User, App\PlexussAdmin, App\Faq;

class FooterFormController extends Controller
{
   	private $emailSubject = 'New form submited.';
	private $scholarshipTable;
	private $scholarshipId;

	/**
	 * Returns Register user page.
	 *
	 * @return void
	 */
	public function contactPost() {


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


		$filter = array(
			'fname' => 'alpha_dash|required',
			'lname' => 'alpha_dash|required',
			'email' => 'required|email',
			'phone' => 'alpha_dash|required',
			'company' => 'required',
			'tellusmore' => 'required'
		);

		$validator = Validator::make(Request::all(), $filter );

		if ($validator->fails()){
			return redirect('contact')->withErrors($validator)->withInput();
		}

		$contact = new ContactUs(array(
			'fname' => Request::get( 'fname' ),
			'lname' => Request::get( 'lname' ),
			'email' => Request::get( 'email' ),
			'phone' => Request::get( 'phone' ),
			'company' => Request::get( 'company' ),
			'tell_us_more' => Request::get( 'tellusmore' ),
			'type' => Request::get( 'contactType' ),
		));

		$contact->save();
		$this->sendemailalert($this->emailSubject, array('jp.novin@plexuss.com', 'dave.moniz@plexuss.com', 
														 'brad.johnson@plexuss.com', 'john.hall@plexuss.com') , array('data' => Request::all() ));

		//$this->sendemailalert(array('anthony.shayesteh@plexuss.com', 'sina.shayesteh@plexuss.com') , array('data' => Request::all() ));


		$data['thank_you'] = true;

		return View('public.footerpages.contact', $data);
	}

	public function collegePrepPost() {
		//Template base arrays
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'college-prep';

		$src="/images/profile/default.png";

		if($data['signed_in'] ==1) {
		   //Get user logged in info.
			//$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link

			$user = Session::get('userinfo');
			$admin = PlexussAdmin::where('user_id', '=', $user['id'])->get()->toArray();
			$admin = array_shift($admin);


			if($user['profile_img_loc']!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user['profile_img_loc'];
			}

			if ( !$user['email_confirmed'] ) {
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
		    $data['email'] = $user['email'];

		}else{

			return redirect('/signup');
		}

		$filter = array(
			'company' => 'required',
			'title' => 'required',
			'phone' => 'required|regex:/^1?\-?\(?([0-9]){3}\)?([\.\-])?([0-9]){3}([\.\-])?([0-9]){4}$/',
		);

		$college_counseling = Request::get( 'CollegeCounseling' );
		$tutoring_center = Request::get( 'TutoringCenter' );
		$test_preparation =Request::get( 'TestPreparation' );
		$international_student_assistance = Request::get('InternationalStudentAssistance');

		if(isset($college_counseling)){
			$college_counseling = 1;
		}else{
			$college_counseling = 0;
		}
		if(isset($tutoring_center)){
			$tutoring_center = 1;
		}else{
			$tutoring_center = 0;
		}
		if(isset($test_preparation)){
			$test_preparation = 1;
		}else{
			$test_preparation = 0;
		}
		if(isset($international_student_assistance)){
			$international_student_assistance = 1;
		}else{
			$international_student_assistance = 0;
		}

		

		//dd(Request::all());
		$validator = Validator::make(Request::all(), $filter );

		if ($validator->fails()){
			return $validator;
		}

		$emailData = array(
			'company' => Request::get( 'company' ),
			'type' => Request::get('companyTypes'),
			'contact' => $data['username'],
			'title' => Request::get( 'title' ),
			'email' => $data['email'],
			'phone' => Request::get( 'phone' ),
			'notes' => Request::get( 'notes' ),
			'college_counseling' => $college_counseling,
			'tutoring_center' => $tutoring_center,
			'test_preparation' => $test_preparation,
			'international_student_assistance' => $international_student_assistance,
		);


		$collegeprep = new CollegePrep($emailData);

		$collegeprep->save();

		$agencyData = array(
			'name' => Request::get( 'company' ),
			'type' => Request::get('companyTypes'),
			'college_counseling' => $college_counseling,
			'tutoring_center' => $tutoring_center,
			'test_preparation' => $test_preparation,
			'international_student_assistance' => $international_student_assistance,
			'phone' => Request::get('phone'),
		);

		$agency = new Agency($agencyData);
		$agency->save();



		$this->sendemailalert($this->emailSubject, array('jp.novin@plexuss.com' , 'sina.shayesteh@plexuss.com') , $emailData);
		//$this->sendemailalert(array('anthony.shayesteh@plexuss.com') , $emailData);

		//$data['thank_you'] = true;
		return "completed";
		//return View('public.footerpages.college-prep', $data);
	}

	public function collegeSubmissionPost() {
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();

		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'college-submission';
		$arr = $this->iplookup();

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

		$filter = array(
			'company' => 'required',
			'fname' => 'required',
			'lname' => 'required',
			'title' => 'required',
			'email' => 'email|required',
			'phone' => 'alpha_dash|required'
		);

		$validator = Validator::make(Request::all(), $filter );

		if ($validator->fails()){
			return redirect('college-submission')->withErrors($validator)->withInput();
		}

		$collegeSubmissionArr = array(
			'company' => Request::get( 'company' ),
			'contact' => Request::get('fname').' '.Request::get('lname'),
			'title' => Request::get( 'title' ),
			'email' => Request::get( 'email' ),
			'phone' => Request::get( 'phone' ),
			'notes' => Request::get( 'notes' ),
		);

		$collegeSubmission = new CollegeSubmission;
		$collegeSubmission->company = isset($input['company'])? $input['company'] : '';
		$collegeSubmission->contact = isset($input['fname']) && isset($input['lname'])? $input['fname'].' '.$input['lname'] : '';
		$collegeSubmission->title = isset($input['title'])? $input['title'] : '';
		$collegeSubmission->email = isset($input['email'])? $input['email'] : '';
		$collegeSubmission->phone = isset($input['phone'])? $input['phone'] : '';
		$collegeSubmission->notes = isset($input['notes'])? $input['notes'] : '';

		if (Session::has('college_submission_params_')) {
            $college_sub_params = Session::get('college_submission_params_');

            $collegeSubmission->utm_source     = isset($college_sub_params['utm_source']) ? $college_sub_params['utm_source'] : '';
            $collegeSubmission->utm_medium     = isset($college_sub_params['utm_medium']) ? $college_sub_params['utm_medium'] : '';
            $collegeSubmission->utm_content    = isset($college_sub_params['utm_content']) ? $college_sub_params['utm_content'] : '';
            $collegeSubmission->utm_campaign   = isset($college_sub_params['utm_campaign']) ? $college_sub_params['utm_campaign'] : '';
            $collegeSubmission->utm_term       = isset($college_sub_params['utm_term']) ? $college_sub_params['utm_term'] : '';

        }elseif(Cache::has(env('ENVIRONMENT') .'_'.'college_submission_params_'.$arr['ip'])){
            $college_sub_params = Cache::get(env('ENVIRONMENT') .'_'.'college_submission_params_'.$arr['ip']);

            $collegeSubmission->utm_source     = isset($college_sub_params['utm_source']) ? $college_sub_params['utm_source'] : '';
            $collegeSubmission->utm_medium     = isset($college_sub_params['utm_medium']) ? $college_sub_params['utm_medium'] : '';
            $collegeSubmission->utm_content    = isset($college_sub_params['utm_content']) ? $college_sub_params['utm_content'] : '';
            $collegeSubmission->utm_campaign   = isset($college_sub_params['utm_campaign']) ? $college_sub_params['utm_campaign'] : '';
            $collegeSubmission->utm_term       = isset($college_sub_params['utm_term']) ? $college_sub_params['utm_term'] : '';

            Cache::forget(env('ENVIRONMENT') .'_'.'college_submission_params_'.$arr['ip']);
        }

		$collegeSubmission->save();

		//$this->sendemailalert(array('jp.novin@plexuss.com' , 'sina.shayesteh@plexuss.com') , $collegeSubmission);


		$this->sendemailalert($this->emailSubject, array('anthony.shayesteh@plexuss.com', 'sina.shayesteh@plexuss.com') , $collegeSubmissionArr);

		$data['thank_you'] = true;
		return View('public.footerpages.college-submission', $data);
	}

	public function scholarshipSubmissionPost() {
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
			return redirect('scholarship-submission')->withErrors($validator)->withInput();
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
			'max_amount' => Request::get( 'maximumAmount' ),
			'website' => Request::get( 'websiteAddress' ),
			'address' => Request::get( 'address' ),
			'Address2' => Request::get( 'Address2' ),
			'city' => Request::get( 'city' ),
			'state' => Request::get( 'state' ),
			'zip' => Request::get( 'zip' ),
			'scholarship_description' => Request::get( 'scholarshipDescription' ),
			'random_token' => $random_token,
		);

		$ScholarshipSubmission = new ScholarshipSubmission($scholarshipData);

		$data['rndtoken'] = $random_token;

		$ScholarshipSubmission->save();

		$this->sendemailalert($this->emailSubject, array('jp.novin@plexuss.com', 'dave.moniz@plexuss.com') , $scholarshipData);


		return View( 'public.footerpages.scholarship-submission-step2', $data);
	}

	public function scholarshipSubmissionGetList($type) {
		switch ($type) {
			case 'academicMajor':
				$return = DB::table('aoc')->join('aos', 'aoc.aos_id', '=', 'aos.id')->select('aos.display_name AS aosName', 'aoc.id AS id', 'aoc.display_name AS value' )->orderBy('aosName')->orderBy('value')->get();
				foreach ($return as $val) {
					$data[$val->aosName][] = array('id'=> $val->id , 'value'=> $val->value);
				}

			break;
		    case 'artisticAbility':
		        $return = DB::table('artistic_abilities')->select( 'id', 'ability as value')->get();
		        foreach ($return as $val) {
					$data['artisticAbility'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'athleticAbility':
		        $return = DB::table('athletic_ability')->select( 'id', 'sport as value')->orderBy('value')->get();
		        foreach ($return as $val) {
					$data['athleticAbility'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'attendanceState':
		    	$return = DB::table('zip_codes')->select( 'StateAbbr AS id', 'StateName as value')->distinct()->orderBy('id')->get();
		    	foreach ($return as $val) {
					$data['attendanceState'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'attendanceCollege':
		    	$return = DB::table('colleges')->select( 'state', 'id', 'school_name as value', 'city')->where('verified', '=', 1)->orderBy('state')->orderBy('school_name')->get();

		    	foreach ($return as $val) {
					$data[$val->state][] = array('id'=> $val->id , 'value'=> $val->value.' - '.$val->city );
				}

		    break;
		    case 'ethnicity':
		        $return = DB::table('ethnicities')->select( 'id', 'ethnicity as value')->get();
		        foreach ($return as $val) {
					$data['ethnicity'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'honorOrganization':
		        $return = DB::table('honor_organizations')->select( 'id', 'organization as value')->get();
		        foreach ($return as $val) {
					$data['honorOrganization'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'disabilities':
		        $return = DB::table('disabilities')->select( 'id', 'disability as value')->get();
		        foreach ($return as $val) {
					$data['disabilities'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'religion':
		        $return = DB::table('religions')->select( 'id', 'religion as value')->orderBy('value')->get();
		        foreach ($return as $val) {
					$data['religion'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'residenceCounty':
		    	$return = DB::table('zip_codes')->select( 'StateAbbr' , 'CountyName as value')->where('CountyName', '!=', 'none')->where('CountyName','!=', '')->groupBy('StateAbbr', 'CountyName' )->orderBy('StateAbbr')->get();
		    	foreach ($return as $val) {
					$data[$val->StateAbbr][] = array('id'=> $val->value , 'value'=> $val->value);
				}
				
		    break;
		    case 'residenceState':
		    	$return = DB::table('zip_codes')->select( 'StateAbbr AS id', 'StateName as value')->distinct()->orderBy('id')->get();
		    	foreach ($return as $val) {
					$data['residenceState'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'yearofFinancialNeed':
		    	$return = DB::table('school_year')->select( 'id', 'level as value')->get();
		    	foreach ($return as $val) {
					$data["yearofFinancialNeed"][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'studentsCurrentSchoolYear':
		    	$return = DB::table('school_year')->select( 'id', 'level as value')->get();
		    	foreach ($return as $val) {
					$data["studentsCurrentSchoolYear"][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    case 'specialMiscCriteria':
		        $return = DB::table('special_misc_criteria')->select( 'id', 'criteria as value')->get();
		        foreach ($return as $val) {
					$data['specialMiscCriteria'][] = array('id'=> $val->id , 'value'=> $val->value);
				}

		    break;
		    default:
		    	return "error";
		}
		return json_encode($data);
	}

	public function scholarshipSubmission2Post($random_token){
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

		//Declare rndtoken OUTSIDE of user authentication
		$data['rndtoken'] = $random_token;
		//First we check random_token for clean token and get ID.
		$this->scholarshipTable = 'scholarship_submission';
		$this->scholarshipId = $this->scholarshipSubmission2PostGetId($random_token);

		//Validate the simple form elements.
		$filter = array(
			'minAct' => 'integer',
			'minSat' => 'integer',
			'minGpa' => 'regex:/^([0-5]){0,1}\.?([0-9]){0,2}$/',
			'maxGpa' => 'regex:/^([0-5]){0,1}\.?([0-9]){0,2}$/',
			//'minClassRank' => 'integer', // LEAVE OFF
			'minGedScore' => 'integer',
			'gender' =>'required|alpha',
			'maxAge' => 'integer',
			'minAge' => 'integer',
		);

		$validator = Validator::make(Request::all(), $filter );

		if ( $validator->fails() ){
			Request::flash();
			return View( 'public.footerpages.scholarship-submission-step2', $data)->withErrors($validator);
		}

		//Validate the checkbox items.
		//Need regular expression for county!!!!!!!!!!               
		$formItems = array(
			'academicMajor' => 'integer',
			'artisticAbility' => 'integer',
			'athleticAbility' => 'integer',
			'attendanceState' => 'alpha',
			'attendanceCollege' => 'integer',
			'ethnicity' => 'integer',
			'honorOrganization' => 'integer',
			'disabilities' => 'integer',
			'religion' => 'integer',
			'residenceCounty' => 'regex:/^[a-zA-Z ]*$/',
			'residenceState' => 'alpha',
			'yearofFinancialNeed' => 'integer',
			'studentsCurrentSchoolYear' => 'integer',
			'specialMiscCriteria' => 'integer'
			);

		$check = $this->scholarshipSubmissionDropdownItemsChecker($formItems);

		if(!$check){
			echo "There was an Error.  Code:9898";
			exit;
		}

		// Add info to DB!!!
		if(Request::get('financialNeedRequirement') == true){
			$this->submitScholarshipInputsToRulesTable('financialNeedRequirement', '', '', 'id', '=', Request::get('financialNeedRequirement'), 'financialNeedRequirement');
		}

		if(Request::get('minAct')){
			$this->submitScholarshipInputsToRulesTable('exams', 'exam_name', 'act', 'exam_score', '>=', Request::get('minAct'), 'minAct');
		}
		
		if(Request::get('minSat')){
			$this->submitScholarshipInputsToRulesTable('exams', 'exam_name', 'sat', 'exam_score', '>=', Request::get('minSat'), 'minSat');
		}

		if(Request::get('minGpa')){
			$this->submitScholarshipInputsToRulesTable('scores', '', '', 'gpa', '>=', Request::get('minGpa'), 'minGpa');
		}

		if(Request::get('maxGpa')){
			$this->submitScholarshipInputsToRulesTable('scores', '', '', 'gpa', '<=', Request::get('maxGpa'), 'maxGpa');
		}

		if(Request::get('gender') != 'anygender'){
			$this->submitScholarshipInputsToRulesTable('users', '', '', 'gender', '=', Request::get('gender'), 'gender');
		}

		if(Request::get('minAge')){
			$this->submitScholarshipInputsToRulesTable('users', '', '', 'birth_date', '>=', Request::get('minAge'), 'minAge');
		}

		if(Request::get('maxAge')){
			$this->submitScholarshipInputsToRulesTable('users', '', '', 'birth_date', '<=', Request::get('maxAge'), 'maxAge');
		}

		if(Request::get('academicMajor')){
			$this->submitScholarshipInputsToRulesTable('aoc', '', '', 'id', '=', Request::get('academicMajor'), 'academicMajor');
		}

		if(Request::get('athleticAbility')){
			$this->submitScholarshipInputsToRulesTable('athletic_ability', '', '', 'id', '=', Request::get('athleticAbility'), 'athleticAbility');
		}

		if(Request::get('attendanceState')){
			$this->submitScholarshipInputsToRulesTable('zip_codes', '', '', 'StateAbbr', '=', Request::get('attendanceState'), 'attendanceState');
		}

		if(Request::get('attendanceCollege')){
			$this->submitScholarshipInputsToRulesTable('colleges', '', '', 'id', '=', Request::get('attendanceCollege'), 'attendanceCollege');
		}

		if(Request::get('ethnicity')){
			$this->submitScholarshipInputsToRulesTable('ethnicities', '', '', 'id', '=', Request::get('ethnicity'), 'ethnicity');
		}

		if(Request::get('honorOrganization')){
			$this->submitScholarshipInputsToRulesTable('honor_organizations', '', '', 'id', '=', Request::get('honorOrganization'), 'honorOrganization');
		}

		if(Request::get('disabilities')){
			$this->submitScholarshipInputsToRulesTable('disabilities', '', '', 'id', '=', Request::get('disabilities'), 'disabilities');
		}

		if(Request::get('religion')){
			$this->submitScholarshipInputsToRulesTable('religions', '', '', 'id', '=', Request::get('religion'), 'religion');
		}

		if(Request::get('residenceCounty')){
			$this->submitScholarshipInputsToRulesTable('zip_codes', '', '', 'CountyName', '=', Request::get('residenceCounty'), 'residenceCounty');
		}

		if(Request::get('residenceState')){
			$this->submitScholarshipInputsToRulesTable('zip_codes', '', '', 'StateAbbr', '=', Request::get('residenceState'), 'residenceState');
		}

		if(Request::get('specialMiscCriteria')){
			$this->submitScholarshipInputsToRulesTable('special_misc_criteria', '', '', 'id', '=', Request::get('specialMiscCriteria'), 'specialMiscCriteria');
		}


		// ASK JP!!  CLASS RANK NOT IN DB!
		if(Request::get('minClassRank')){
			$this->submitScholarshipInputsToRulesTable('minClassRank', '', '', 'id', '=', Request::get('minClassRank'), 'minClassRank');
		}
		
		if(Request::get('minGedScore')){
			$this->submitScholarshipInputsToRulesTable('minGedScore', '', '', 'id', '=', Request::get('minGedScore'), 'minGedScore');
		}

		if(Request::get('yearofFinancialNeed')){
			$this->submitScholarshipInputsToRulesTable('yearofFinancialNeed', '', '', 'id', '=', Request::get('yearofFinancialNeed'), 'yearofFinancialNeed');
		}

		if(Request::get('studentsCurrentSchoolYear')){
			$this->submitScholarshipInputsToRulesTable('studentsCurrentSchoolYear', '', '', 'id', '=', Request::get('studentsCurrentSchoolYear'), 'studentsCurrentSchoolYear');
		}

		if(Request::get('militaryVetAffilation') == true){
			$this->submitScholarshipInputsToRulesTable('militaryVetAffilation', '', '', 'id', '=', Request::get('militaryVetAffilation'), 'militaryVetAffilation');
		}

		if(Request::get('citizenshipStatus') == true){
			$this->submitScholarshipInputsToRulesTable('citizenshipStatus', '', '', 'id', '=', Request::get('citizenshipStatus'), 'citizenshipStatus');
		}

		$data['thank_you'] = true;
		return View('public.footerpages.scholarship-submission', $data);
	}

	public function showCareersAndInternships(){
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Help';
		$data['currentPage'] = 'careers-internships';

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
		//check for tracking info.
		$this->trackerInputChecker();

		//Get all the jobs and internships on the careers table
		$Dbreturn = DB::table('careers')->select('id', 'job_type', 'field', 'job_title', 'city', 'state')->get();

		//build a list of all fields so we can group them.
		foreach ($Dbreturn as $row){
				$careers[$row->job_type][$row->field][] = array( 'id' => $row->id, 'field' => $row->field, 'job_title' => $row->job_title, 'city' => $row->city, 'state' => $row->state);
		}

		$data['careers'] = $careers;
		return View( 'public.footerpages.careers-internships', $data);
	}

	public function showCareer($id){
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
			
			$data['faq'] = Faq::where('type', '=', 'general')->get()->toArray();

			$data['faq_page'] = 1;
			
			$data['username'] = $user['fname'].' '.$user['lname'];
		    $data['profile_img_loc'] = $src;
		}
		//check for tracking info.
		$this->trackerInputChecker();

		//CHECK TO MAKE SURE $id IS JUST A NUMBER!!
		$validator = Validator::make(
			array('id' => $id ),
			array('id' => 'required|integer')
		);

		if ($validator->fails()){
			echo 'There was an issue looking up that post. Error:9876';
			exit;
		}

		//Get all the jobs and internships on the careers table
		$data['career'] = DB::table('careers')->where('id', '=', $id)->get();
		return View( 'public.footerpages.careers-internshipsDisplay', $data);
	}

	public function postCareer($id){
		//CHECK TO MAKE SURE $id IS JUST A NUMBER!!
		$validator = Validator::make(
			array('id' => $id ),
			array('id' => 'required|integer')
		);

		if ($validator->fails()){
			echo 'There was an issue looking up that post. Error:9875';
			exit;
		}

		$filter = array(
			'fname' => 'alpha_dash|required',
			'lname' => 'alpha_dash|required',
			'email' => 'required|email',
			'phone' => 'alpha_dash|required',
			'zip' => 'alpha_dash|required',
			'schoolname' => 'sometimes|required',
			'gradelevel' => 'sometimes|required',
			'counselorname' => '',
			'gpa' => '',
			'position' => 'alpha_dash',
			'referer'=>'sometimes|url',
			'camid' => 'sometimes|alpha_dash',
			'specid' => 'sometimes|alpha_dash',
			'pixel' => 'sometimes|alpha_dash'
		);

		$validator = Validator::make(Request::all(), $filter );

		if ($validator->fails()){
			$data = DB::table('careers')->where('id', '=', $id)->get();
			Request::flash();
			return View( 'public.footerpages.careers-internshipsDisplay', array('data' => $data) )->withErrors($validator);
		}
		
		$careersubmit = new CareerSubmit(array(
			'fname' => Request::get( 'fname' ),
			'lname' => Request::get( 'lname' ),
			'email' => Request::get( 'email' ),
			'phone' => Request::get( 'phone' ),
			'zipcode' => Request::get( 'zip' ),
			'school' => Request::get( 'schoolname' ),
			'grade_level' => Request::get( 'gradelevel' ),
			'counselor' => Request::get( 'counselorname' ),
			'gpa' => Request::get( 'gpa' ),
			'position' => Request::get( 'position' ),
			'camid' => Request::get( 'camid' ),
			'specid' => Request::get( 'specid' ),
			'pixel' => Request::get( 'pixel' ),
			'referer' => Request::get( 'referer' )
		));

		$careersubmit->save();

		$this->sendemailalert($this->emailSubject, array('jp.novin@plexuss.com') , array('data' => Request::all() ));
		// $mac = new MandrillAutomationController;

		// $input = array();
		// $input['type']  = Request::get( 'position' );
		// $input['fname'] = Request::get( 'fname' );
		// $input['email'] = Request::get( 'email' );

		// $mac->careersSurveyEmail($input);

		return redirect('careers-internships')->with('thankyou', true);
	}

	private function scholarshipSubmissionDropdownItemsChecker ($formItems){
		foreach ($formItems as $key => $val) {
			if(Request::get($key)){
				$specialMiscCriteria = $this->validateScholarshipSubmission(Request::get($key), $val);
				if($specialMiscCriteria == 0){
					return 0;
				}
			}
		}
		return 1;
	}

	private function scholarshipSubmission2PostGetId($random_token){
		$tokenValidator = Validator::make(
			array('token' => $random_token),
			array('token' => 'required|alpha_num')
		);
		if ( $tokenValidator->fails() ){
			echo "There was an error with the form token.  Code:9896";
			exit;
		}

		//Get the id that the random_token belongs to OR throw error.
		$id = DB::table('scholarship_submission')->where('random_token', '=', $random_token)->first();

		if(!$id){
			echo "There was an error with the form token.  Code:9897";
			exit;
		}
		return $id->id;
	}

	private function validateScholarshipSubmission($array, $rules){
		if($array){
			foreach ($array as $value) {
	    		$validator = Validator::make(
	    			array('id' => $value),
	    			array('id' => $rules)
	    		);

	    		if ( $validator->fails() ){ 
	    			return 0;
	    		}
			}
		}
		return 1;
	}

	private function submitScholarshipInputsToRulesTable($table, $fieldName, $fieldFilter, $ruleCol, $opr, $input, $memo){
		//This is after validation!!
		if( gettype($input) == 'array'){
			$arr = [];
			foreach ($input as $key => $value) {
				$arr[] = $value;
			}
			$input = implode(",", $arr);
		}
		
		DB::table('rules')->insert(array(
			'parent' => $this->scholarshipTable,
			'parent_id' => $this->scholarshipId,
			'table' => $table,
			'field_name' => $fieldName,
			'field_filter' => $fieldFilter,
			'rule_column' => $ruleCol,
			'operator' => $opr,
			'rule' => $input,
			'memo' => $memo
		));
	}

	private function trackerInputChecker(){
		//Session::flush();
		if( isset($_SERVER["HTTP_REFERER"]) ){
			if ( !Session::has('refererUrl')){
				Session::put('refererUrl', $_SERVER["HTTP_REFERER"]);
			}
		}

		if(Request::get('camid') || Request::get('specid') || Request::get('pixel')){
			//clean up any tracking input submited if any. Exit if fails validation.
			$trackingSubmited = array(
				'camid' => Request::get('camid'),
				'specid' => Request::get('specid'),
				'pixel' => Request::get('pixel')
			);

			foreach ($trackingSubmited as $key => $value) {
				$validator = Validator::make(
			    	array('checkTracking' => $value),
			    	array('checkTracking' => 'alpha_num')
				);
			}

			if ($validator->fails()){
			   echo 'There was an error in the request. Error: 9564';
			   exit;
			}

			Session::put('trackingParams', $trackingSubmited);
		}
	}
}
