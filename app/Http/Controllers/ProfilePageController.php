<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, Session, DB, Validator, AWS, DateTime;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

use App\PlexussAdmin; 
use App\User;
use App\College, App\Highschool;
use App\Country;
use App\LikesTally;
use App\PublicProfileClaimToFame;
use App\PublicProfileSkills;
use App\PublicProfileSkillsEndorsements;
use App\PublicProfileProjectsAndPublications, App\Recruitment, App\UserEducation, App\UserEducationMajor, App\Major;
use App\Http\Controllers\SocialController;

class ProfilePageController extends Controller
{
    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex(){
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );

		//Set admin array for topnav Link
		$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
		$admin = array_shift($admin);

		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['admin'] = $admin;
		
			
		$data['title'] = 'Plexuss Profile Page';
		$data['currentPage'] = 'profile';
		// $data['ajaxtoken'] = $token['token'];
		$data['Section']="personalInfo";		
		if(isset($_REQUEST['section']))
		{
			$obj=new AjaxController();
			$percent=$obj->CalcProfilePercent();
			if(
			($percent<10 && ($_REQUEST['section']=='scores' || $_REQUEST['section']=='highschoolInfo' || $_REQUEST['section']=='collegeInfo'))
			||
			($percent<70 && ($_REQUEST['section']=='experience' || $_REQUEST['section']=='skills' || $_REQUEST['section']=='interests' || $_REQUEST['section']=='clubOrgs' || $_REQUEST['section']=='honorsAwards' || $_REQUEST['section']=='languages' || $_REQUEST['section']=='certifications' || $_REQUEST['section']=='patents' || $_REQUEST['section']=='publications'))
			)
			{			
			$data['Section']="personalInfo";
			}
			else
			{
			$data['Section']=$_REQUEST['section'];
			}
		}
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);

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

		

		//trigger to show reveal modal on homepage.
		if($user->profile_page_lock_modal) {
			$data['profile_page_lock_modal'] = $user->profile_page_lock_modal;
		}

		// include countries
		$countries_raw = Country::all()->toArray();
		$countries = array();
		foreach( $countries_raw as $val ){
			$countries[$val['id']] = $val['country_name'];
		}
		$data['countries'] = $countries;

		$data['user_country_id']  = $user->country_id;
		$data['prof_intl_country_chng'] = $user->prof_intl_country_chng;
		$data['amt_able_to_pay'] = $user->financial_firstyr_affordibility;
		$data['profile_perc'] = $user->profile_percent;

		//if has not gone through our signup or does not have an email add, send to get started
		// if( $user->completed_signup == 0 || (!isset($user->email) || empty($user->email) || $user->email == 'none') ){
		// 	return redirect()->intended('/college-application');
		// }else{
			return View('private.profile.me', $data);
		// }
	}
	public function score()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'score';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.score', $data);
	}
	public function highschool()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'highschool';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.highschool', $data);
	}
	public function college()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'college';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.college', $data);
	}
	public function experience()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.experience', $data);
	}
	public function skills()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.skills', $data);
	}
	public function interests()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.interests', $data);
	}
	public function cluborg()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.cluborg', $data);
	}
	public function honoraward()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.honoraward', $data);
	}
	public function language()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.language', $data);
	}
	public function certifications()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.certifications', $data);
	}
	public function patents()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.patents', $data);
	}
	public function publications()
	{
		//Get user logged in info and ajaxtoken.
		$user = User::find( Auth::user()->id );
		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title'] = 'Plexuss Profile View';
		$data['currentPage'] = 'profile';
		$data['profileTab'] = 'accomp';
		// $data['ajaxtoken'] = $token['token'];
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
		

		$data['userinfo'] = array(
			'id' => $user["id"],
			'fname' => $user["fname"],
			'lname' => $user["lname"],
			'zip' => $user["zip"]
		);
		return View('private.profile.publications', $data);
	}

	public function setUserCountry(){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$user = new User;
		$input = Request::all();

		$ret = $user->setUserCountry($data['user_id'], $input['country_id']);

		return $ret;
	}

	public function setProfileIntlCountryChange(){

		$user = Session::get('user_table');

		$user->prof_intl_country_chng = 1;

		$user->save();
	}

    public function getProjectsAndPublications($hashed_user_id = '') {
        if (!empty($hashed_user_id)) {
            try {
                $data = ['user_id' => Crypt::decrypt($hashed_user_id)];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);
        }

        $query = PublicProfileProjectsAndPublications::on('rds1')
                                                     ->select('id', 'title', 'url', 'created_at')
                                                     ->where('user_id', '=', $data['user_id'])
                                                     ->where('active', '=', 1)
                                                     ->get();

        return json_decode(json_encode($query, true));
    }

    public function insertPublicProfilePublication($input = []) {
        if (!empty($input)) {
            try {
                $data = ['user_id' => Crypt::decrypt($input['hashed_user_id'])];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);

            $input = Request::all();    
        }

        $values = [
            'user_id' => $data['user_id'],
            'title' => $input['title'],
            'url' => $input['url'],
        ];

        $ret = PublicProfileProjectsAndPublications::insertOrUpdate($values);
        
        $this->CalcIndicatorPercent($data['user_id']);
        $this->CalcProfilePercent($data['user_id']);
        $this->CalcOneAppPercent($data['user_id']);

        return $ret;
    }

    public function removePublicProfilePublication($input = []) {
        if (!empty($input)) {
            try {
                $data = ['user_id' => Crypt::decrypt($input['hashed_user_id'])];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);

            $input = Request::all();    
        }

        $values = [
            'publication_id' => $input['publication_id'],
            'user_id' => $data['user_id'],
        ];

        return PublicProfileProjectsAndPublications::removePublication($values);
    }

    public function getProfileClaimToFame($hashed_user_id = '') {
        if (!empty($hashed_user_id)) {
            try {
                $data = ['user_id' => Crypt::decrypt($hashed_user_id)];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);
        }

        $response = [
            'claimToFameDescription' => '',
            'claimToFameYouTubeVideoUrl' => '',
            'claimToFameVimeoVideoUrl' => '',
        ];

        $input = Request::all();

        $query = PublicProfileClaimToFame::on('rds1')
                                         ->select('description', 'youtube_url', 'vimeo_url')
                                         ->where('user_id', '=', $data['user_id'])
                                         ->first();

        if (isset($query)) {
            $response['claimToFameDescription'] = $query->description;
            $response['claimToFameYouTubeVideoUrl'] = $query->youtube_url;
            $response['claimToFameVimeoVideoUrl'] = $query->vimeo_url;
        }

        return $response;
    }

    public function saveClaimToFameSection($input = []) {
        if (!empty($input)) {
            try {
                $data = ['user_id' => Crypt::decrypt($input['hashed_user_id'])];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);

            $input = Request::all();
        }

        $values = [
            'user_id' => $data['user_id'],
            'description' => $input['description'],
            'youtube_url' => $input['youtube_url'],
            'vimeo_url' => $input['vimeo_url'],
        ];

        $ret = PublicProfileClaimToFame::insertOrUpdate($values);
        
        $this->CalcIndicatorPercent($data['user_id']);
        $this->CalcProfilePercent($data['user_id']);
        $this->CalcOneAppPercent($data['user_id']);
        
        return $ret;
    }

    public function getLikedColleges($hashed_user_id = '') {
        if (!empty($hashed_user_id)) {
            try {
                $data = ['user_id' => Crypt::decrypt($hashed_user_id)];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);
        }

        $response = [];

        // $likesTally = LikesTally::on('rds1')
        //                       ->select('likes_tally.id as likes_tally_id', 'c.id', 'c.school_name', 'c.logo_url', 'c.slug')
        //                       ->where('likes_tally.user_id', '=', $data['user_id'])
        //                       ->where('likes_tally.type', '=', 'college')
        //                       ->leftJoin('colleges as c', 'c.id', '=', 'likes_tally.type_val')
        //                       ->get();

        $likesTally =  DB::connection('rds1')->table('recruitment as r')
        									 ->join('colleges as c', 'c.id', '=', 'r.college_id')
        									 ->select('r.id as likes_tally_id', 'c.id', 'c.school_name', 'c.logo_url', 'c.slug')
        									 ->where('r.user_id', $data['user_id'])
        									 ->where('r.user_recruit', 1)
        									 ->where('r.status', 1)
        									 ->orderBy('r.created_at', 'asc')
        									 ->get();

        foreach ($likesTally as $college) {
            $response[$college->id] = $college;
        }

        return array_values($response);
    }

    public function removeLikedCollege() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $input = Request::all();

        if (!isset($input['likes_tally_id']) || !isset($data['user_id'])) {
            return 'failed';
        }

        // $lt = LikesTally::where('id', '=', $input['likes_tally_id'])
        //                 ->where('user_id', '=', $data['user_id']) // Ensure the user_id is associated with likes_tally_id
        //                 ->first();

        $lt = Recruitment::where('id', $input['likes_tally_id'])
                         ->where('user_id', '=', $data['user_id']) // Ensure the user_id is associated with likes_tally_id
                         ->first();

        if (!isset($lt)) {
            return 'failed';
        }

        $lt->delete();

        return 'success';
    }

    public function getSkillsAndEndorsements($hashed_user_id = '') {
        if (!empty($hashed_user_id)) {
            try {
                $data = ['user_id' => Crypt::decrypt($hashed_user_id)];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);
        }

        $query = PublicProfileSkills::on('rds1')
                                    ->where('user_id', '=', $data['user_id'])
                                    ->where('active', '=', 1)
                                    ->get();
        foreach ($query as $key) {
        	$tmp_query = DB::connection('rds1')->table('public_profile_skills_endorsements as ppse')
        									   ->join('users as u', 'u.id', '=', 'ppse.endorser_user_id')
        									   ->select('u.id as user_id', DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as student_profile_photo'), 'u.fname', 'u.lname')
        									   ->where('public_profile_skills_id', $key->id)
        									   ->groupBy('u.id')
        									   ->get();
        	foreach ($tmp_query as $k) {
        		$k->user_id = $this->hashIdForSocial($k->user_id);
		    }								   
        	$key->endorsers = $tmp_query;
			$key->user_id = $this->hashIdForSocial($key->user_id);
        }

        return $query->toArray();
    }

    public function getUsersEndorsingMe(){
    	if (!empty($hashed_user_id)) {
            try {
                $data = ['user_id' => Crypt::decrypt($hashed_user_id)];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);
        }
    }

    public function saveSkillsAndEndorsements($input = []) {
        if (!empty($input)) {
            try {
                $data = ['user_id' => Crypt::decrypt($input['hashed_user_id'])];
            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);

            $input = Request::all();
        }

        $pendingRemovedIds = $input['pendingRemovedIds'];

        $skillsEditedList = $input['skillsEditedList'];

        if (!empty($skillsEditedList)) {
            foreach ($skillsEditedList as $skill) {
                $values = [
                    'user_id' => $data['user_id'],
                    'name' => $skill['name'],
                    'group' => $skill['group'],
                    'position' => $skill['position'],
                    'awards' => $skill['awards'],
                ];

                PublicProfileSkills::insertOrUpdate($values);
            }
        }

        if (!empty($pendingRemovedIds)) {
            foreach ($pendingRemovedIds as $skill_id) {
                $values = [
                    'user_id' => $data['user_id'],
                    'id' => $skill_id,
                ];

                PublicProfileSkills::removeSkill($values);
            }
        }

        $this->CalcIndicatorPercent($data['user_id']);
        $this->CalcProfilePercent($data['user_id']);
        $this->CalcOneAppPercent($data['user_id']);

        $response['status'] = 'success';
        $response['skills'] = $this->getSkillsAndEndorsements();

        return $response;
    }

    public function searchCollegesWithLogos($input = []) {
        if (empty($input)) {
            $input = Request::all();
        }

        $query = $input['query'];

        $colleges = College::on('rds1')
                           ->select('id', 'logo_url', 'school_name', 'slug')
                           ->where(function($q) use($query){
								$q->orWhere( 'school_name', 'LIKE', '%'.$query.'%' )
								  ->orWhere( 'alias',       'LIKE', '%'.$query.'%' );
							})
                           ->where('verified', '=', 1)
                           ->whereNotNull('logo_url')
                           ->whereNull('user_id_submitted')
                           ->get();

        return $colleges;
    }

    public function saveLikedCollegesSection($input = []) {
        if (!empty($input)) {
            try {
                $data = [
                    'ip' => $input['ip'],
                    'user_id' => Crypt::decrypt($input['hashed_user_id']), 
                ];

            } catch (\Exception $e) {
                return 'bad user id';
            }
        } else {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData(true);

            $input = Request::all();
        }

        $user_id = $data['user_id'];

        $ip = $data['ip'];

        // commented out as now referring to Recruitment table
        // Add new liked colleges to likes_tally
        // $newLikedColleges = $input['newLikedColleges'];
        
        // if (!empty($newLikedColleges)) {
        //     foreach ($newLikedColleges as $college) {
        //         $attributes = [
        //             'user_id' => $user_id,
        //             'type' => 'college',
        //             'type_col' => 'id',
        //             'type_val' => $college['id'],
        //         ];

        //         $values = [
        //             'user_id' => $user_id,
        //             'type' => 'college',
        //             'type_col' => 'id',
        //             'type_val' => $college['id'],
        //             'ip' => $ip,
        //         ];

        //         LikesTally::updateOrCreate($attributes, $values);
        //     }
        // }

        // // Remove unliked colleges from likes_tally
        // $pendingRemovedIds = $input['pendingRemovedIds'];

        // if (!empty($pendingRemovedIds)) {
        //     foreach ($pendingRemovedIds as $college_id) {
        //         LikesTally::where('user_id', '=', $user_id)
        //                   ->where('type', '=', 'college')
        //                   ->where('type_val', '=', $college_id)
        //                   ->delete();
        //     }
        // }

        $this->CalcIndicatorPercent($data['user_id']);
        $this->CalcProfilePercent($data['user_id']);
        $this->CalcOneAppPercent($data['user_id']);

        $response['status'] = 'success';
        $response['liked_colleges'] = $this->getLikedColleges();

        return $response;
    }

    public function uploadProfilePicture() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $input = Request::all();

        $user_id = $data['user_id'];

        $response = [];

        try {
            $uploadResponse = $this->generalUploadDoc($input, 'profile-picture', 'asset.plexuss.com/users/images');

        } catch (\Exception $e) {
            $response['status'] = 'failed';

            return $response;
        }

        $user = User::find($user_id);

        $user->profile_img_loc = $uploadResponse['saved_as'];

        $user->save();

        Session::put('userinfo.session_reset', 1);

        $response['status'] = 'success';
        $response['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/' . $uploadResponse['saved_as'];

        $this->CalcProfilePercent($data['user_id']);

        return $response;
    }

    public function getEducation($user_id = null){

    	if (!isset($user_id)) {
    		$viewDataController = new ViewDataController();
        	$data = $viewDataController->buildData(true);
        	$user_id = $data['user_id'];
    	}    	

        $ue  = UserEducation::on('rds1')->with('highschool:id,school_name',
        									   'college:id,school_name,logo_url',
    										   'degree:id,name,display_name',
    										   'majors:*')
        								->where('user_id', $user_id)
        								->orderBy('grad_year', 'DESC')
        								->get();

        foreach ($ue as $key) {
			$key->user_id = $this->hashIdForSocial($key->user_id);
			
			if (isset($key->majors) && !empty($key->majors)) {
				foreach ($key->majors as $k) {
					$major = Major::on('rds1')->find($k->major_id);
					$k->major_name = $major->name;
				}
			}
		}								
        return $ue;
    }

    public function saveEducation(){
    	$viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $input = Request::all();

        if (isset($input['data'])) {
			
			$ue = UserEducation::on('rds1')->where('user_id', $data['user_id'])
										   ->first();

			$is_verified = 0;
			if (isset($ue)) {
				$is_verified = $ue->is_verified;
			}						

        	UserEducation::where('user_id', $data['user_id'])->delete();
        	
        	foreach ($input['data'] as $key) {
	        	if (!isset($key['school_id']) || empty($key['school_id'])) {
	        		$check = false;

	        		// For some reason we didn't get the id, but we have this exact school name in our db
	        		if ($key['edu_level'] ==  1) {
	        			$college =  College::on('rds1')->where('verified', 1)
	        										   ->where('school_name', 'LIKE', '%'.$key['school_name'].'%') 
	        										   ->first();

	        			if (isset($college)) {
	        				$key['school_id'] = $college->id;
	        				$check = true;
	        			}
	        		}else{
	        			$hs =  Highschool::on('rds1')->where('verified', 1)
	        										   ->where('school_name', 'LIKE', '%'.$key['school_name'].'%') 
	        										   ->first();

	        			if (isset($hs)) {
	        				$key['school_id'] = $hs->id;
	        				$check = true;
	        			}
	        		}

	        		if (!$check) {
	        			$newSchool = $key['edu_level'] == 1 ? new College : new Highschool;
						$newSchool->school_name = $key['school_name'];
						$newSchool->verified = 0;
						$newSchool->user_id_submitted = $data['user_id'];
						$newSchool->save();
						$key['school_id'] = $newSchool->id;
	        		}
		        			        }
		        if($key['degree_type'] == 0){
		        	$key['degree_type'] = null;
		        }
		        $attr = array('user_id' => $data['user_id'], 'edu_level' => $key['edu_level'], 'school_id' => $key['school_id'],
		        			  'degree_type' => $key['degree_type'], 'grad_year' => $key['grad_year'], 
		        			  'is_verified' => $is_verified);

		        $res = UserEducation::updateOrCreate($attr, $attr);

		        if (isset($key['majors'])) {
		        	foreach ($key['majors'] as $key => $value) {
		        		$is_minor = isset($value['is_minor']) ? $value['is_minor'] : 0;
						$attr = array('major_id' => $value['major_id'], 'sue_id' => $res->id, 'is_minor' => $is_minor );
						$this->customUpdateOrCreate(new UserEducationMajor, $attr, $attr);
			        }
		        }
        	}
        }

        $sc = new SocialController;

        Session::put('userinfo.session_reset', 1);

        return response()->json(array('success' => true, 'education' => $this->getEducation(), 'user_school_names' => $sc->getAllUserSchools()), 200);
    }
}
