<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Illuminate\Http\Request;

class AdminCollegesController extends Controller
{
    /*==================================================
	 *===============BEGIN COLLEGE SECTION==============
	 *==================================================*/

	public function colleges(){
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

		
		$data['admin_title'] = "Colleges";

		
		/*
		$colleges = new College();
		$colleges_list = $colleges->listAll();
		foreach($colleges_list as $college){
			//Format Date
			$arr = date_parse($college->updated);
			$college->date = $arr['month'] . '-' . $arr['day'] . '-' . $arr['year'];
		}

		$data['colleges_list'] = $colleges_list;
		 */
		return View('admin.colleges', $data);
		
	}

	public function addCollege($section = null){
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
		$section = ($section == null) ? 'stats' : $section;
		$data['admin']['page_vars']['section'] = $section;
		return View('admin.add.college_' . $section, $data);
		
	}

	public function editCollege($id, $section = null){
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

		//Fetch school's data to fill form values
		$college = new College();
		$college_data = $college->get_stats($id);
		if($college_data != null){
			$college_data = $college_data[0];
			$college_data->logo_prefix = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/';
		}
		$section = ($section == null) ? 'stats' : $section;
		$data['college'] = $college_data;
		/*
		echo "<pre>";
		var_dump($college_data);
		echo "</pre>";
		echo "data <pre>";
		var_dump($data);
		echo "</pre>";
		 */


		return View('admin.add.college_' . $section, $data);
		
	}

	public function postCollege($id, $section){
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

		//END TOPNAV DATA

		$input = Request::all();
		echo "id: " . $id . "<br>";
		echo "section: " . $section . "<br>";

		$rules = array(
			'school_name' => array('Min:3', 'Max:255;'),
			'address' => array('min:10', 'max:255'),
			'general_phone' => 'regex:/^1?\-?\(?([0-9]){3}\)?([\.\-])?([0-9]){3}([\.\-])?([0-9]){4}$/',
			'tour_url' => 'url',
			//'brochure' =>
			'logo_url' => 'url',
			'slug' => 'regex:/^([0-9a-zA-Z\_\-])+$/',
			'page_title' => 'regex:/^([0-9a-zA-Z\- ])+$/',
			'meta_keywords' => array('min:3', 'max:255'),
			'meta_description' => array('min:3', 'max:255'),
			'image' => 'image', //above safe!
			'deadline' => 'regex:/^([0-9a-zA-Z\.\- ])+$/',
			'percent_admitted' => array('regex:/^100|[1-9][0-9]?$/'),
			'student_body_total' => 'numeric',
			'undergrad_enroll_1112' => 'numeric',
			'tuition_avg_in_state_ftug' => 'numeric',
			'tuition_avg_out_state_ftug' => 'numeric',
			'graduation_rate_4_year' => array('regex:/^100|[1-9][0-9]?$/'),
			'school_sector' => 'regex:/^([0-9a-zA-Z\.\,\- ])+$/',
			'locale' => 'regex:/^([0-9a-zA-Z\: ])+$/',
			'campus_housing' => array('regex:/^Yes|No$/'),
			'religious_affiliation' => 'regex:/^([0-9a-zA-Z\.\- ])+$/',
			'academic_calendar' => 'regex:/^([0-9azA-Z\.\- ])+$/',
			/*
			 * Temporarily commented due to http:// requirement
			'school_url' => 'url',
			'admission_url' => 'url',
			'apply_online_url' => 'url',
			'financial_aid_url' => 'url',
			'calculator_url' => 'url',
			'mission_url' => 'url',
			 */
			'sat_read_25' => array('regex:/^([2-7][0-9][0]|[8][0][0])$/'),
			'sat_write_25' => array('regex:/^([2-7][0-9][0]|[8][0][0])$/'),
			'sat_math_25' => array('regex:/^([2-7][0-9][0]|[8][0][0])$/'),
			'sat_read_75' => array('regex:/^([2-7][0-9][0]|[8][0][0])$/'),
			'sat_write_75' => array('regex:/^([2-7][0-9][0]|[8][0][0])$/'),
			'sat_math_75' => array('regex:/^([2-7][0-9][0]|[8][0][0])$/'),
			'sat_percent' => array('regex:/^100|[1-9][0-9]?$/'),
			'act_composite_25' => array('regex:/^([1-9]|[1-2][0-9]|[3][0-6])$/'),
			'act_composite_75' => array('regex:/^([1-9]|[1-2][0-9]|[3][0-6])$/'),
			'act_percent' => array('regex:/^100|[1-9][0-9]?$/'),
			//This value will need more processing to determine column
			'total_endow' => 'numeric',
			'student_faculty_ratio' => 'numeric',
			'accred_agency' => 'regex:/^([0-9a-zA-Z\.\,\- ])+$/',
			'accred_period' => 'regex:/^([0-9a-zA-Z\.\/\- ])+$/',
			'accred_status' => 'regex:/^([0-9a-zA-Z\.\,\- ])+$/',
			'bachelors_degree' => array('regex:/^1|0$/'),
			'masters_degree' => array('regex:/^1|0$/'),
			'post_masters_degree' => array('regex:/^1|0$/'),
			'doctors_degree_research' => array('regex:/^1|0$/'),
			'doctors_degree_professional' => array('regex:/^1|0$/'),
			'rotc_army' => array('regex:/^1|0$/'),
			'rotc_navy' => array('regex:/^1|0$/'),
			'rotc_air' => array('regex:/^1|0$/'),
			'qa' => array('regex:/^1|0$/'),
			'live' => array('regex:/^1|0$/')
			/*
			'bachelors_degree' => 'boolean',
			'masters_degree' => 'boolean',
			'post_masters_degree' => 'boolean',
			'doctors_degree_research' => 'boolean',
			'doctors_degree_professional' => 'boolean',
			'rotc_army' => 'boolean',
			'rotc_navy' => 'boolean',
			'rotc_air' => 'boolean',
			'qa' => 'boolean',
			'live' => 'boolean'
			 */
		);
		$v = Validator::make( $input, $rules );
		// If we pass validation, continue
		if($v->passes()){
			// Set slug -- either from input or generate from title
			$slugger = new Slugger();
			$slug = isset($input['slug']) ? $input['slug'] : $slugger->makeSlug($input['title']);
			// Decide if post was an ADD or EDIT
			if($id == 'new'){
				$add = true;
				echo "That was an ADD post! ID: ";
				var_dump($id);
				echo "<br>";
			}
			else{
				$add = false;
				echo "that was an EDIT news! ID: " . $id . "<br>";
			}
			echo "add: ";
			var_dump($add);
			echo "<br>";

			//Determine if there is a LIVE revision
			$liveRev = College::where
				('live_status', '=', '1')
				->where('id', '=', $id)
				->get()
				->toArray();
			$live = empty($liveRev) ? false : true;
			echo "live: ";
			var_dump($live);
			echo "<br>";

			//Determine if there is a PENDING revision
			$pendingRev = College::where
				('live_status', '=', '0')
				->where('id', '=', $id)
				->get()
				->toArray();
			$pending = empty($pendingRev) ? false : true;

			echo "pending: ";
			var_dump($pending);
			echo "<br>";

			/* ADD NEW COLLEGE
			 * =============== */
			if($add){
				echo "we're going to ADD a NEW college as a NEW pending revision!<br>";

			}
			/* EDIT EXISTING COLLEGE
			 * ===================== */
			else{
				/* NEW PENDING COLLEGE
				 * =================== */
				if(!$pending){
					echo "We're going to ADD a NEW pending revision!<br>";

					$prev_id = $id;
					$slugCheck = $slugger->checkNewsSlug('add', $slug, 'colleges', $id);

				}
				/* EDIT PENDING COLLEGE REVISION
				 * ============================= */
				else{
					echo "We're going to EDIT an EXISTING pending revision!<br>";

				}
			}
		}
		/* IF USER INPUT DOESN'T PASS VALIDATION
		 * ===================================== */
		// will add error message/alert later
		else{
			echo "input: <pre>";
			var_dump($input);
			echo "</pre>";
			$msgs = $v->messages();
			echo "error messages: <pre>";
			var_dump($msgs);
			echo "</pre>";
			//return $msgs;
		}

		exit;
		
	}
	/*==================================================
	 *==============END COLLEGE SECTION=================
	 *==================================================*/
}
