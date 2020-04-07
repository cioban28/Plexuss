<?php

namespace App\Http\Controllers;

use Request, Session, DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\User, App\PlexussAdmin, App\Recruitment, App\PortalNotification, App\College, App\Ranking, App\Priority, App\RevenueOrganization, App\ScholarshipsUserApplied, App\UsersCustomQuestion, App\Scholarship;

use App\Http\Controllers\UserMessageController;
use App\Http\Controllers\SocketController;
use App\Events\EventName;
use App\Events\UserSignedup;
use Illuminate\Support\Facades\Redis;
use App\Country;
use App\RecommendModalShow;

class PortalController extends Controller
{
    const NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD = 10;

	public function Index( $section = 'portal' ) {

		if ( Auth::check() ) {
			//Get user logged in info and ajaxtoken.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where( 'user_id', '=', $user->id )->get()->toArray();
			$admin = array_shift( $admin );

			// $token = $user->ajaxtoken->toArray();

			$logged_user=Auth::user()->id;

			//Build to $data array to pass to view.
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();

			$data['admin'] = $admin;

			$data['title'] = 'Plexuss Portal Page';
			$data['currentPage'] = 'portal';
			// $data['ajaxtoken'] = $token['token'];

			$data['userinfo'] = array(
				'id' => $user["id"],
				'fname' => $user["fname"],
				'lname' => $user["lname"],
				'zip' => $user["zip"],
				'allow_public' => $user["allow_public"],
				'allow_private' => $user["allow_private"],
				'allow_non_traditional' => $user["allow_non_traditional"],
				'allow_2_year' => $user["allow_2_year"],
				'allow_4_year' => $user["allow_4_year"],
				'only_ranked' => $user["only_ranked"],
			);

			$user_interest = DB::table( 'interested_reason' )->where( 'user_id', $logged_user )->first();
			$data['user_interest']=$user_interest;


			//set the avatar image.
			$src="/images/profile/default.png";
			if ( $user->profile_img_loc!="" ) {
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}

			$data['profile_img_loc'] = $src;

			//this sets the section of the portal page to load in ajax
			$data['section'] = $section;

			$data['didJoyride'] = 0;

			if( Cache::has(env('ENVIRONMENT').'_'.$data['user_id'].'didJoyride') ){
				$data['didJoyride'] = 1;
			}
			// echo "<pre>";
			// print_r($data);
			// echo "</pre>"; exit;
			return View( 'private.portal.master' , $data );
		}else {
			return redirect( '/signin' );
		}
	}

	public function getManageSchool($is_api = null, $user_id = null) {

		if ( Auth::check() || $is_api ) {


			if( $is_api ){
				$data = array();
				$id = $user_id;

			}else{
				$viewDataController = new ViewDataController();
				$data = $viewDataController->buildData();

				$id = Auth::id();
				$input = Request::all();
			}

			$leftmenu=Request::get( 'menu' );

			$user = User::find( $id );

			$recruitment = Recruitment::where( 'user_id', '=', $user->id )
										->where( 'status', '=', 1 )
										->where('user_recruit', 1)
										->groupBy('college_id')
										->get();

			$data['profile_percent'] = $user->profile_percent;

			$incmt = 0;
			$data['colleges'] = array();

			foreach ( $recruitment as $key ) {
				$college_id = $key['college_id'];

				$collegeObj = College::where( 'id', '=', $college_id )->first();
       			$countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();

				$data['colleges'][$incmt]['college_id'] = $college_id;
				$data['colleges'][$incmt]['school_name'] = $collegeObj->school_name;
				$data['colleges'][$incmt]['city'] = $collegeObj->city;
				$data['colleges'][$incmt]['state'] = $collegeObj->state;
        		$data['colleges'][$incmt]['country_code'] = strtolower($countryObj->country_code);
				$data['colleges'][$incmt]['logo_url'] = $collegeObj->logo_url;
				$data['colleges'][$incmt]['school_name'] =  $collegeObj->school_name;
				$data['colleges'][$incmt]['slug'] =  $collegeObj->slug;
				$data['colleges'][$incmt]['hand_shake'] = false;
				$data['colleges'][$incmt]['in_our_network'] = $collegeObj->in_our_network;
				$data['colleges'][$incmt]['user_applied'] = $key->user_applied;

				if ( $key->college_recruit == 1 && $key->user_recruit == 1 ) {
					$data['colleges'][$incmt]['hand_shake'] = true;
				}

				if( $is_api ){
					$data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$collegeObj->logo_url;
					$data['colleges'][$incmt]['status'] = $data['colleges'][$incmt]['hand_shake'] ? 'Handshake!' : 'Pending';
					$data['colleges'][$incmt]['status_code'] = $data['colleges'][$incmt]['hand_shake'];
				}

				$rankingObj = Ranking::where( 'college_id' , '=', $college_id )->first();

				if ( isset( $rankingObj->plexuss ) ) {
					$data['colleges'][$incmt]['rank'] = $rankingObj->plexuss;
				} else {
					$data['colleges'][$incmt]['rank'] = 'N/A';
				}

				$incmt++;
			}

			$data['type'] = 'yourlist';

			if( $is_api ){
				return $data;
			}

			return View( 'private.portal.ajax.manageschool.yourlist' , $data );

			/*
			elseif($leftmenu=='menu2')
			{
				//$listdata = $notification->manageSchoolData($id,'recruit');
				//$data['listdata']=$listdata;
				return View('private.portal.ajax.manageschool.schoolrecruit', $data);
			}
			elseif($leftmenu=='menu3')
			{
				//$listdata = $notification->manageSchoolData($id,'recommended');
				//$data['listdata']=$listdata;
				return View('private.portal.ajax.manageschool.plexussrecommended', $data);
			}
			elseif($leftmenu=='menu4')
			{
			//	$listdata = $notification->manageSchoolData($id,'viewing');
			//	$data['listdata']=$listdata;
				return View('private.portal.ajax.manageschool.collegeviewing', $data);
			}
			elseif($leftmenu=='menu5')
			{
				//$listdata = $notification->manageSchoolData(121,'trash');
			//	$listdata = $notification->getTrashRecord($id);
			//	$data['listdata']=$listdata;
				return View('private.portal.ajax.manageschool.trash', $data);
			}

			// ajax testing in datatables
			*/

			/*elseif($leftmenu=='menu6')
			{
				$listdata = $collegelist->yourListData($id);
				$data['listdata']=$listdata;
				return View('private.portal.ajax.manageschool.ajaxyourlist1', $data);
			}	*/

		}
	}

	public function getApplicationData($is_api = NULL, $user_id = NULL){
		$data = array();
		$data['user_id'] = Session::get('userinfo.id');

		if( isset($user_id) ){
			$data['user_id'] = $user_id;
		}

		$p = new Priority;
		$p = $p->getPrioritySchoolsForIntlStudents();
		$schools = $p['undergrad'];


		$user = new User;
		$profile = $user->getUsersProfileData($data['user_id']);
		$decoded = json_decode($profile);

		$mergedSchools = array();

		foreach ($decoded->applyTo_schools as $key) {
			(int)$id = $key->college_id;
			$submitted = $key->submitted;
			
			$collegeObj = College::where( 'id', '=', $id)->first();
			$countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();
			foreach ($schools as $s) {
				if((int)$s['college_id'] == $id){
					$s['rank'] = $s['rank'] == 999999 ? 'N/A' : $s['rank'];
					$s['submitted'] = $submitted;
					$s['submitted_msg'] = $submitted == 1 ? 'Submitted!' : 'Incomplete';
					$split = explode('/', $s['slug']);
					$s['slug'] = end($split);

					if( $is_api ){
						$s['status'] = $s['submitted_msg'];
						$s['status_code'] = $submitted;

						if( isset($s['logo_url']) ){
							$s['logo_url'] = trim($s['logo_url']);
						}
					}
					
					if($countryObj){
						$s['country_code'] = strtolower($countryObj->country_code);
					}else{
						$s['country_code'] ='';
					}

					$mergedSchools[] = $s;
				}
			}

		}

		$data['colleges'] = $mergedSchools;
		
		$data['type'] = 'applications';

		if( $is_api ){
			return $data;
		}

		return View( 'private.portal.ajax.manageschool.applications' , $data );
	}

	public function getUsrRecommendationList($is_api = null, $user_id = NULL) {
		if ( Auth::check() || $is_api ) {

			if( $is_api ){
				$data = array();
				$id = $user_id;

			}else{
				$viewDataController = new ViewDataController();
				$data = $viewDataController->buildData();

				//get id by authcheck
				$id = Auth::id();
				$input = Request::all();
			}


			$user = User::find( $id );

			$portal_notification = DB::connection('rds1')->table('portal_notifications as pn')
														 ->join('colleges as c', 'c.id', '=', 'pn.school_id')

														 ->leftjoin('distribution_clients as dc', 'dc.college_id', '=', 'c.id')
									                     ->leftjoin('ad_redirect_campaigns as arc', 'arc.college_id', '=', 'c.id')

														 ->where( 'pn.user_id', '=', $user->id )
														 ->where( 'pn.is_recommend' , '=' , '1' )
														 ->where( 'pn.is_recommend_trash', '!=', '1' )
														 ->orderBy( 'pn.id', 'DESC' )

														 ->select('pn.*', 'c.id as college_id', 'c.school_name', 'c.city', 'c.state',
																  'c.logo_url', 'c.slug', 'dc.ro_id as dc_ro_id', 'arc.ro_id as arc_ro_id')
														 ->groupBy('c.id')
														 ->get();

	 		foreach($portal_notification as $portal){
				PortalNotification::where( 'id', '=', $portal->id)
					              ->update(['is_seen'=> '1']);
			}																	 

			$incmt = 0;
			$data['colleges'] = array();

			$recommendedBasedOnCollegeId = -1;
			foreach ( $portal_notification as $key ) {
				$ro_id = NULL;
				//dd($collegeObj);
				$collegeObj = College::where( 'id', '=', $key->college_id)->first();
        		$countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();
		
				$data['colleges'][$incmt]['college_id'] = $key->college_id;
				$data['colleges'][$incmt]['school_name'] = $key->school_name;
				$data['colleges'][$incmt]['city'] = $key->city;
				$data['colleges'][$incmt]['state'] = $key->state;
				$data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;
				$data['colleges'][$incmt]['school_name'] =  $key->school_name;
				$data['colleges'][$incmt]['slug'] =  $key->slug;
				$data['colleges'][$incmt]['country_code'] = strtolower($countryObj->country_code);
				$data['colleges'][$incmt]['is_higher_rank_recommend'] =  $key->is_higher_rank_recommend;
				$data['colleges'][$incmt]['is_major_recommend'] =  $key->is_major_recommend;
				$data['colleges'][$incmt]['is_department_recommend'] =  $key->is_department_recommend;
				$data['colleges'][$incmt]['is_lower_tuition_recommend'] =  $key->is_lower_tuition_recommend;
				$data['colleges'][$incmt]['is_top_75_percentile_recommend'] =  $key->is_top_75_percentile_recommend;

				if( $is_api ){
					$data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;
				}


				if ( $key->recommend_based_on_college_id!= -1 && $key->recommend_based_on_college_id!= null ) {

					$recommendedBasedOnCollegeId = $key->recommend_based_on_college_id;

					$c_model = College::where( 'id', '=', $recommendedBasedOnCollegeId )->first();

					$data['colleges'][$incmt]['recommend_based_on_college_name'] =  $c_model->school_name;


				}

				$data['colleges'][$incmt]['date_added'] =  date( 'm/d/Y', strtotime( $key->updated_at ) );

				$rankingObj = Ranking::where( 'college_id' , '=', $key->college_id )->first();

				if ( isset( $rankingObj->plexuss ) ) {
					$data['colleges'][$incmt]['rank'] = $rankingObj->plexuss;
				} else {
					$data['colleges'][$incmt]['rank'] = 'N/A';
				}

				if(isset($key->arc_ro_id)){
					$ro_id = $key->arc_ro_id;
				}elseif (isset($key->dc_ro_id)) {
					$ro_id = $key->dc_ro_id;
				}

				// Check if this ro is active or not. if not, make ro_id null
				$is_ro_active = RevenueOrganization::on('rds1')->find($ro_id);
				
                if(isset($is_ro_active->active) && $is_ro_active->active !== 1){
					$ro_id = NULL;
				}

				if(isset($ro_id)){
					$ro = RevenueOrganization::find($ro_id);
					$this_ro = new RevenueOrganization;

					$this_temp = array();
					$this_temp['cap']   = $this_ro->getRevenueOrganizationCap($ro, $user->id);
					$this_temp['type']  = $ro->type;
					$this_temp['ro_id'] = $ro_id;

					$data['colleges'][$incmt]['ro_detail'] = $this_temp;
				}

				$incmt++;
			}

			$schModel = new ScholarshipsUserApplied;

			$scholarships = $schModel->getScholarshipsForPortal($user->id, 'recommendation');

			if(isset($scholarships) && !empty($scholarships)){
				foreach ( $scholarships as $key ) {
					
					$data['colleges'][$incmt]['college_id']  = $key->id;
					$data['colleges'][$incmt]['school_name'] = $key->scholarship_name;
					$data['colleges'][$incmt]['city'] 		 = NULL;
					$data['colleges'][$incmt]['state'] 		 = NULL;
					$data['colleges'][$incmt]['slug'] 		 = NULL;
					$data['colleges'][$incmt]['logo_url'] 	 = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/scholarship-hand-icon.png';
					$data['colleges'][$incmt]['type'] 		 = 'scholarship';

					$data['colleges'][$incmt]['country_code'] = '';
					
					$data['colleges'][$incmt]['rank'] 		  = 'N/A';
					$incmt++;
				}

			}

		}

		$data['type'] = 'recommendationlist';

		if( $is_api ){
			return $data;
		}
	
		$user_id_in_recommend_modal = RecommendModalShow::on("rds1")
													->select('user_id')
													->where('user_id','=', $data['user_id'])
													->first();
		// if(isset($user_id_in_recommend_modal)){
		// 	echo $user_id_in_recommend_modal['user_id'];
		// }else{
		// 	echo "Not record Found";
		// 	DB::table('recommend_modal')->insert(
		// 	    ['user_id' => $data['user_id']]
		// 	);			
		// }																			
		// exit;
		if(!isset($user_id_in_recommend_modal)){
			RecommendModalShow::updateOrCreate(['user_id' => $data['user_id']]);
		}

		$latest_recruit = DB::connection('rds1')->table('plexuss.recruitment as r')
									    ->join('plexuss.colleges as c', 'r.college_id', '=', 'c.id')
									    ->leftjoin('plexuss.recommend_modal as rm', 'r.user_id', '=', 'rm.user_id')
									    ->select('r.college_id as college_id', 'c.slug as college_slug', 'c.school_name as college_name', 'rm.recommend_modal_show as show_modal')
									    ->where('r.user_id', $data['user_id'])
									    ->orderBy('r.id','DESC')
									    ->first();
		$data['latest_recruit'] = (array)$latest_recruit;

		dd($data);

		return View( 'private.portal.ajax.manageschool.plexussrecommended' , $data );
	}

	/*
	Author: Ash
	Purpose: get list of trash for user
	*/
	public function getUsrPortalTrash($is_api = NULL, $user_id = NULL) {
		if ( Auth::check() || (is_bool($is_api) && $is_api) ) {

			if( $is_api ){
				$data = array();
				$id = $user_id;
			}else{
				$viewDataController = new ViewDataController();
				$data = $viewDataController->buildData();

				$id = Auth::id();
				$input = Request::all();
			}

			//get id by authcheck

			$user = User::find( $id );

			$trash_ids = array();

			// $recruitment = Recruitment::where('recruitment.user_id', '=', $user->id )
			// 	->where('recruitment.status', '=', 0 )
			// 	->leftjoin('recruitment as r2',function($join) use($data){
			// 		$join->on('recruitment.college_id','=','r2.college_id')
			// 			 ->where('r2.user_id','=',$data['user_id'])
			// 			 ->where('r2.status','=',1);
			// 		})
			// 	->whereNull('r2.id')
			// 	->select('recruitment.*')
			// 	->groupBy('recruitment.college_id')
			// 	->get();

			$recruitment = Recruitment::on('rds1')->where('recruitment.user_id', '=', $user->id )
				->where('recruitment.status', '=', 0 )
				->select('recruitment.*')
				->groupBy('recruitment.college_id')
				->get();
			
			foreach ( $recruitment as $school ) {

				array_push( $trash_ids, $school->college_id );

				Recruitment::where( 'id', '=', $school->id)->update(['is_seen_trash' => '1']);
			}

			$portal_notification = PortalNotification::where( 'user_id', '=', $user->id )
			->where( 'is_recommend_trash', '=', 1 )
			->get();

			foreach ( $portal_notification as $school ) {

				array_push( $trash_ids, $school->school_id );

			}

			$schModel = new ScholarshipsUserApplied;

			$scholarships = $schModel->getScholarshipsForPortal($user->id, 'removed');

			// $sch_trash_ids = array();
			// foreach ( $portal_notification as $school ) {
			// 	$sch_trash_ids[] = $school->id;
			// }

			// dd($sch_trash_ids);
			$incmt = 0;
			$data['colleges'] = array();
			foreach ( $trash_ids as $key => $value ) {
				$college_id = $value;

				$collegeObj = College::where( 'id', '=', $college_id )->first();
				
				$countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();
				
				$data['colleges'][$incmt]['college_id'] = $college_id;
				$data['colleges'][$incmt]['school_name'] = $collegeObj->school_name;
				$data['colleges'][$incmt]['city'] = $collegeObj->city;
				$data['colleges'][$incmt]['state'] = $collegeObj->state;
				$data['colleges'][$incmt]['slug'] =  $collegeObj->slug;
				$data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$collegeObj->logo_url; //$collegeObj->logo_url;
				$data['colleges'][$incmt]['type'] = 'college';

				
				if($countryObj){
					$data['colleges'][$incmt]['country_code'] = strtolower($countryObj->country_code);
				}else{
					$data['colleges'][$incmt]['country_code'] ='';
				}
				if( $is_api ){
					$data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$collegeObj->logo_url;
				}

				$rankingObj = Ranking::where( 'college_id' , '=', $college_id )->first();

				if ( isset( $rankingObj->plexuss ) ) {
					$data['colleges'][$incmt]['rank'] = $rankingObj->plexuss;
				} else {
					$data['colleges'][$incmt]['rank'] = 'N/A';
				}

				$incmt++;
			}

			if(isset($scholarships) && !empty($scholarships)){
				foreach ( $scholarships as $key ) {
					
					$data['colleges'][$incmt]['college_id']  = $key->id;
					$data['colleges'][$incmt]['school_name'] = $key->scholarship_name;
					$data['colleges'][$incmt]['city'] 		 = NULL;
					$data['colleges'][$incmt]['state'] 		 = NULL;
					$data['colleges'][$incmt]['slug'] 		 = NULL;
					$data['colleges'][$incmt]['logo_url'] 	 = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/scholarship-hand-icon.png';
					$data['colleges'][$incmt]['type'] 		 = 'scholarship';

					$data['colleges'][$incmt]['country_code'] = '';
					
					$data['colleges'][$incmt]['rank'] 		  = 'N/A';
					$incmt++;
				}

			}

			$data['type'] = 'trash';

			if( is_bool($is_api) && $is_api ){
				return $data;
			}

			return View( 'private.portal.ajax.manageschool.trash' , $data );

		}

	}

	public function getManageScholarships( $token = null ) {

		if ( Auth::check() ) {
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find( $id );

			$leftmenu=Request::get( 'menu' );
			$data = array( 'token' => $token );
			// $data['ajaxtoken'] = $token;

			$notification = new Notification;

			if ( $leftmenu=='menu1' ) {
				$listdata = $notification->scholarshipData( 121, 'scholarship' );
				$data['listdata']=$listdata;
				return View( 'private.portal.ajax.managescholarships.scholarship' , $data );
			}
			elseif ( $leftmenu=='menu2' ) {
				$listdata = $notification->scholarshipData( 121, 'trash' );
				$data['listdata']=$listdata;
				return View( 'private.portal.ajax.managescholarships.trash' , $data );
			}
		}
	}


	/****************
	*  gets Scholarships a user is interested in
	*
	*  @return view
	****************************/
	public function getScholarships(){
		
		$data['scholarships'] = [];

		if ( Auth::check() ) {
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find( $id );
			$schModel = new ScholarshipsUserApplied;
			$res = $schModel->getUsersScholarships($user->id);
			$data['scholarships'] = $res;
		}

		
		//get if user OneApp step
		$appModel = new UsersCustomQuestion();
		$oneapp_status = $appModel->getUserStatus($user->id);
		$data['oneapp_status'] = $oneapp_status;
		return View( 'private.portal.ajax.managescholarships.myScholarships' , $data );

	}



	/**
	 * Author : ASH
	 * getCollegesRecruitYou
	 *
	 * get the list of schools that want to recruit the student
	 *
	 * @return view
	 */
	public function getCollegesRecruitYou($is_api = null, $user_id = null) {

		if( $is_api ){
			$data = array();
			$my_user_id = $user_id;

		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
			$my_user_id = $data['user_id'];
		}


		$qry = DB::connection('rds1')->table( 'colleges as c' )
									 ->join( 'recruitment as rt', 'c.id', '=', 'rt.college_id' )
									 ->join( 'colleges_ranking as cr', 'cr.college_id', '=' , 'c.id' )
									 ->leftjoin('recruitment as r2',function($join) use($my_user_id){
										$join->on('c.id','=','r2.college_id')
											 ->where('r2.user_id','=',$my_user_id)
											 ->where('r2.user_recruit','=',1);
										})
							         ->leftJoin('aor_colleges as ac', 'c.id', '=', 'ac.college_id')

							         ->leftjoin('distribution_clients as dc', 'dc.college_id', '=', 'c.id')
									 ->leftjoin('ad_redirect_campaigns as arc', 'arc.college_id', '=', 'c.id')

									 ->whereNull('r2.id')
									 ->select( 'c.id as college_id', 'c.school_name', 'c.slug as slug', 'c.logo_url as logo_url', 'c.city', 'c.state', 'cr.plexuss as rank', 'ac.aor_id as aor_id', 'dc.ro_id as dc_ro_id', 'arc.ro_id as arc_ro_id', 'rt.id as rt_id' )
									 ->where('rt.user_id', $my_user_id )
									 ->where('rt.college_recruit', 1 )
									 ->where('rt.user_recruit', 0)
									 ->where('rt.status', 1)
									 ->groupBy('rt.college_id')
									 ->get();


		$data['colleges'] = array();
		foreach ( $qry as $key ) {

			// Recruitment::where( 'id', '=', $key->session_regenerate_id())->update(['is_seen_recruit' => '1']);

			$collegeObj = College::where( 'id', '=', $key->college_id)->first();
        	$countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();
				
			$ro_id = NULL;
			$tmp = array();
            $tmp['aor_id'] = isset($key->aor_id) ? $key->aor_id : NULL;
			$tmp['college_id'] = $key->college_id;
			$tmp['school_name'] = $key->school_name;
			$tmp['slug'] = $key->slug;
			$tmp['logo_url'] = $key->logo_url;
			$tmp['city'] = $key->city;
			$tmp['state'] = $key->state;
			$tmp['rank'] = isset($key->rank) ? $key->rank : 'N/A';
			$tmp['country_code'] = strtolower($countryObj->country_code);

			if( $is_api ){
				$tmp['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;
			}

			if(isset($key->arc_ro_id)){
				$ro_id = $key->arc_ro_id;
			}elseif (isset($key->dc_ro_id)) {
				$ro_id = $key->dc_ro_id;
			}

			if(isset($ro_id)){
				$ro = RevenueOrganization::find($ro_id);
				$this_ro = new RevenueOrganization;

				$this_temp = array();
				$this_temp['cap']   = $this_ro->getRevenueOrganizationCap($ro, $my_user_id);
				$this_temp['type']  = $ro->type;
				$this_temp['ro_id'] = $ro_id;

				$tmp['ro_detail'] = $this_temp;
			}

			$data['colleges'][] = $tmp;
		}

		$data['type'] = 'collegesrecruityou';

		if( $is_api ){
			return $data;
		}

		return View( 'private.portal.ajax.manageschool.collegesrecruityou' , $data );
	}

	/**
	 * Author : ASH
	 * getCollegesViewedYourProfile
	 *
	 * get the list of schools that have viewed your profile
	 *
	 * @return view
	 */
	public function getCollegesViewedYourProfile($is_api = null, $user_id = null) {

		if( $is_api ){
			$data = array();
			$data['user_id'] = $user_id;
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
		}

		// $topnav_notifications = $data['topnav_notifications']['data'];

		// $schools_name_arr = array();

		// foreach ($topnav_notifications as $key) {
		// 	//commmand 1
		// 	if($key['command'] == 1 && $key['type'] == 'user'){
		// 		$schools_name_arr[] = $key['name'];
		// 	}
		// }


		// $my_user_id = $data['user_id'];

		// $qry = DB::table( 'colleges as c' )
		// ->join( 'colleges_ranking as cr', 'cr.college_id', '=' , 'c.id' )
		// ->select( 'c.id as college_id', 'c.school_name', 'c.slug as slug', 'c.logo_url as logo_url', 'c.city', 'c.state', 'cr.plexuss as rank' )
		// ->whereIn('c.school_name', $schools_name_arr)
		// ->get();	 

		$qry = DB::connection('rds1')->table('notification_topnavs as nt')
									 ->join('colleges as c', 'c.school_name', '=', 'nt.name')
									 ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									 ->leftjoin('distribution_clients as dc', 'dc.college_id', '=', 'c.id')
									 ->leftjoin('ad_redirect_campaigns as arc', 'arc.college_id', '=', 'c.id')
									 ->where('nt.command', 1)
									 ->where('nt.msg', 'viewed your profile')
									 ->where('nt.type_id', $data['user_id'])
									 ->where('c.verified', 1)
									 ->groupBy('c.id')
									 ->orderBy('nt.updated_at', 'desc')
									 ->select('c.id as college_id', 'c.school_name', 'c.slug' , 'c.logo_url', 'c.city', 'c.state', 'cr.plexuss as rank', 'dc.ro_id as dc_ro_id', 'arc.ro_id as arc_ro_id','nt.id as nt_id')
									 ->get();
			
		$data['colleges'] = array();
		foreach ( $qry as $key ) {

			DB::table('notification_topnavs as nt')->where('nt.id',$key->nt_id)->update(['nt.is_read' => '1']);

			$collegeObj = College::where( 'id', '=', $key->college_id)->first();
        	$countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();
			
			$ro_id = NULL;

			$tmp = array();
			$tmp['college_id'] = $key->college_id;
			$tmp['school_name'] = $key->school_name;
			$tmp['slug'] = $key->slug;
			$tmp['logo_url'] = $key->logo_url;
			$tmp['city'] = $key->city;
			$tmp['state'] = $key->state;
			$tmp['rank'] = isset($key->rank) ? $key->rank : 'N/A';
			$tmp['country_code'] = strtolower($countryObj->country_code);

			if(isset($key->arc_ro_id)){
				$ro_id = $key->arc_ro_id;
			}elseif (isset($key->dc_ro_id)) {
				$ro_id = $key->dc_ro_id;
			}

			if(isset($ro_id)){
				$ro = RevenueOrganization::find($ro_id);
				$this_ro = new RevenueOrganization;

				$this_temp = array();
				$this_temp['cap']   = $this_ro->getRevenueOrganizationCap($ro, $data['user_id']);
				$this_temp['type']  = $ro->type;
				$this_temp['ro_id'] = $ro_id;

				$tmp['ro_detail'] = $this_temp;
			}

			if( $is_api ){
				$tmp['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$tmp['logo_url'];
			}

			$data['colleges'][] = $tmp;
		}

		$data['type'] = 'collegesviewedprofile';

		if( $is_api ){
			return $data;
		}

		// $this->customdd($data);
		// exit();

		return View( 'private.portal.ajax.manageschool.collegesviewedprofile' , $data );
	}

	//*****************PORTAL MESSAGES***********************************************************//
	public function getMessageCenter( $receiver_id = null , $type = null ) {

		$data = array();
		return View( 'private.portal.ajax.messagecenter.messageList' , $data );

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$umc = new UserMessageController;
		$data['currentPage'] = "portal-messages";

		if ( $receiver_id != NULL ) {
			$data['stickyUsr'] = $receiver_id;
		}else {
			$data['stickyUsr'] = "";
		}


		$data['topicUsr'] = $umc->getAllThreads( $data, $receiver_id, $type );

		$data['sticky_thread_type'] = 'inquiry-msg';
		/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		*/
		//exit();

		// event(new EventName());
		// Redis::connection();

		$soc = new SocketController;
		$soc->index($data['user_id']);

		// Redis::subscribe(['backToLaravel'], function ($message) {
  //           dd($message);
  //           echo $message;
  //       });

		// event(new UserSignedup(833433));

		return View( 'private.portal.ajax.messagecenter.messageList' , $data );
	}

	public function portalMessageCenter( $receiver_id = null , $type = null, $thread_id = NULL ) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$umc = new UserMessageController;
		$data['currentPage'] = "portal";

		if ( $receiver_id != NULL ) {
			$data['stickyUsr'] = $receiver_id;
		}else {
			$data['stickyUsr'] = "";
		}

		if (isset($thread_id)) {
			if (!is_numeric($thread_id)) {
				return "Bad thread_id";
			}
		}

		if (isset($receiver_id)) {
			if (!is_numeric($receiver_id)) {
				return "Bad receiver_id";
			}
		}

		$user_table = Session::get( 'user_table' );

		// $data['ajaxtoken'] = $user_table->remember_token;

		$data['userinfo'] = array(
			'id' => $user_table->id,
			'fname' => $user_table->fname,
			'lname' => $user_table->lname,
			'zip' => $user_table->zip,
			'allow_public' => $user_table->allow_public,
			'allow_private' => $user_table->allow_private,
			'allow_non_traditional' => $user_table->allow_non_traditional,
			'allow_2_year' => $user_table->allow_2_year,
			'allow_4_year' => $user_table->allow_4_year,
			'only_ranked' =>  $user_table->only_ranked,
		);

		$user_interest = DB::table( 'interested_reason' )->where( 'user_id', $user_table->id )->first();
		$data['user_interest']=$user_interest;


		//set the avatar image.
		$src="/images/profile/default.png";
		if ( $user_table->profile_img_loc!="" ) {
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user_table->profile_img_loc;
		}

		$data['profile_img_loc'] = $src;
		$data['school_id'] = $receiver_id;

		//$data['topicUsr'] = $umc->getAllThreads( $data, $receiver_id, $type, NULL, NULL, NULL, $thread_id);

		$data['section'] = 'messages';

		$data['sticky_thread_type'] = 'inquiry-msg';

		// dd($data);

		return View( 'private.portal.master' , $data );

	}

	public function getUserMessages( $thread_id = null, $latest_msg_id = null, $first_msg_id = null, $data = NULL, $is_api = false ) {

		$umc = new UserMessageController;

		return $umc->getUserMessages( $thread_id, $latest_msg_id, $first_msg_id);
	}


	public function getThreadListHeartBeat( $receiver_id = NULL, $type = NULL, $thread_id = NULL  ) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		if (isset($input['loadMore'])) {
			if (Session::has($data['user_id'].'_loadMore')) {
				$tmp_loadMore_cnt = Session::get($data['user_id'].'_loadMore');
				Session::put($data['user_id'].'_loadMore', $tmp_loadMore_cnt + self::NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD);
			}else{
				Session::put($data['user_id'].'_loadMore', self::NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD);
			}
		}elseif(!Session::has($data['user_id'].'_loadMore')) {
			Session::put($data['user_id'].'_loadMore', self::NUM_OF_THREADS_TO_SHOW_ON_EACH_LOAD);
		}
		
		isset($receiver_id) ? $receiver_id = $this->decodeIdForSocial($receiver_id) : NULL;

		$umc = new UserMessageController;
		
		return $umc->getThreadListHeartBeat( $receiver_id, $type, NULL, $thread_id );

	}

	// public function postMessage( $thread_id = NULL, $receviver_user_id = NULL, $type = NULL ) {

	// 	$umc = new UserMessageController;

	// 	return $umc->postMessage( $thread_id, $receviver_user_id, $type );
	// 	// $is_api = false, $api_inputs = NULL, $my_user_id = NULL, $inputs = NULL

	// }

	public function setMsgRead( $thread_id = NULL, $is_api = NULL ) {
		$umc = new UserMessageController;

		return $umc->setMsgRead( $thread_id );
	}

	//*****************END OF PORTAL MESSAGES***********************************************************//

	public function getCalender( $token = null ) {

		if ( Auth::check() ) {
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find( $id );
			$leftmenu=Request::get( 'menu' );
			$data = array( 'token' => $token );
			// $data['ajaxtoken'] = $token;

			return View( 'private.portal.ajax.portalcalender.calender' , $data );

		}
	}

	public function addEvents() {

		$user = User::find( Auth::user()->id );
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss Add Event';
		$data['currentPage'] = 'portal';
		// $token = AjaxToken::where( 'user_id', '=', $user->id )->first();

		// if ( !$token ) {
		// 	Auth::logout();
		// 	return redirect( '/' );
		// }

		return View( 'private.portal.ajax.portalcalender.addevent' , $data );
	}

	public function eventCollege() {
		$user = User::find( Auth::user()->id );
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss College Event';
		$data['currentPage'] = 'portal';
		// $token = AjaxToken::where( 'user_id', '=', $user->id )->first();

		// if ( !$token ) {
		// 	Auth::logout();
		// 	return redirect( '/' );
		// }

		return View( 'private.portal.ajax.portalcalender.eventcollege' , $data );
	}

	public function getNotificationDeatils( $token = null ) {


		if ( Auth::check() ) {
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			//Look up user data by id.
			$user = User::find( $id );
			$data = array( 'token' => $token );
			// $data['ajaxtoken'] = $token;


			$list_ID=Request::get( 'list_ID' );
			$tab=Request::get( 'tab' );
			$menu=Request::get( 'menu' );
			$data=explode( "-", $list_ID );
			$recordId=$data[0];


			if ( $data[1]=='college_list' ) {
				$collegelist = new CollegeList;
				$listdata = $collegelist->yourListData( $id, $recordId, $menu );
				$data['listdata']=$listdata[0];
			}
			else {
				$notification = new Notification;
				$listdata = $notification->GetListRecord( $recordId, $tab );
				$data['listdata']=$listdata;
			}

			$data['tab']=$tab;
			$data['menu']=$menu;
			return View( 'private.portal.ajax.manageschool.quickfact' , $data );
		}
	}

	public function settingStatus( $token = null ) {
		if ( Auth::check() ) {
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }
			$user = User::find( $id );
			$input=Request::get();
			parse_str( $input['datastring'], $params );

			foreach ( $params as &$data ) {
				if ( $data=='on' )$data='1';
			}
			$update_array=array();
			{
				if ( isset( $params['allow_public'] ) ) {$update_array['allow_public']='1';}else {$update_array['allow_public']='0';}
				if ( isset( $params['allow_private'] ) ) {$update_array['allow_private']='1';}else {$update_array['allow_private']='0';}
				if ( isset( $params['allow_non_traditional'] ) ) {$update_array['allow_non_traditional']='1';}else {$update_array['allow_non_traditional']='0';}
				if ( isset( $params['allow_2_year'] ) ) {$update_array['allow_2_year']='1';}else {$update_array['allow_2_year']='0';}
				if ( isset( $params['allow_4_year'] ) ) {$update_array['allow_4_year']='1';}else {$update_array['allow_4_year']='0';}
				if ( isset( $params['only_ranked'] ) ) {$update_array['only_ranked']='1';}else {$update_array['only_ranked']='0';}

			}

			$update_status=User::where( 'id', $input['UserId'] )->update( $update_array );

			exit();
		}
	}

	public function interestedSetting( $token = null ) {
		if ( Auth::check() ) {
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			$input=Request::get();

			parse_str( $input['datastring'], $params );

			foreach ( $params as &$data ) {
				if ( $data=='on' )$data='1';
			}

			$insert_array=array();
			$insert_array['user_id']=$input['UserId'];
			$insert_array['college_id']=$input['collegeID'];

			if ( isset( $params['academic_reputation'] ) ) {$insert_array['academic_reputation']='1';}
			else {$insert_array['academic_reputation']='0';}

			if ( isset( $params['location'] ) ) {$insert_array['location']='1';}
			else {$insert_array['location']='0';}

			if ( isset( $params['cost_of_tuition'] ) ) {$insert_array['cost_of_tuition']='1';}
			else {$insert_array['cost_of_tuition']='0';}

			if ( isset( $params['majors_programs_offered'] ) ) {$insert_array['majors_programs_offered']='1';}
			else {$insert_array['majors_programs_offered']='0';}

			if ( isset( $params['athletics'] ) ) {$insert_array['athletics']='1';}
			else {$insert_array['athletics']='0';}

			if ( isset( $params['religion'] ) ) {$insert_array['religion']='1';}
			else {$insert_array['religion']='0';}

			if ( isset( $params['campus_life'] ) ) {$insert_array['campus_life']='1';}
			else {$insert_array['campus_life']='0';}

			if ( isset( $params['other-chk'] ) && $params['other_val']!='' ) {$insert_array['other']=$params['other_val'];}
			else {$insert_array['other']=NULL;}

			$checkuser = DB::table( 'interested_reason' )->where( 'user_id', $input['UserId'] )->where( 'college_id', $input['collegeID'] )->first();
			if ( count( $checkuser )>0 ) {
				$InterestedReason=InterestedReason::where( 'user_id', $input['UserId'] )->where( 'college_id', $input['collegeID'] )->update( $insert_array );
			}
			else {
				$data = new InterestedReason;
				$InterestedReason=$data->insertGetId( $insert_array );
			}
			echo $InterestedReason;
			exit();
		}
	}

	public function plexussnotification() {

		if ( Auth::check() ) {
			//Get user logged in info and ajaxtoken.
			$user = User::find( Auth::user()->id );
			// $token = $user->ajaxtoken->toArray();

			$logged_user=Auth::user()->id;

			$college_id=Request::get( 'college_id' );

			//Build to $data array to pass to view.
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();


			$data['title'] = 'Plexuss Portal Page';
			$data['currentPage'] = 'portal';
			// $data['ajaxtoken'] = $token['token'];
			$data['logged_user'] = $logged_user;
			$data['college_id'] = $college_id;

			$user_interest = DB::table( 'interested_reason' )->where( 'user_id', $logged_user )->where( 'college_id', $college_id )->first();
			$data['user_interest']=$user_interest;
			return View( 'private.portal.ajax.manageschool.plexussnotification' , $data );
		} else {
			return redirect( '/signin' );
		}
	}
	/*------------------------------------ Aajax call function -------------------------------------- */

	public function writeMessage() {
		$data=array();
		return View( 'private.portal.ajax.messagecenter.writemessage' , $data );
	}

	public function messageThread() {
		$ID=Request::get( 'ID' );
		$data=array();
		return View( 'private.portal.ajax.messagecenter.messagethread' , $data );
	}


	public function applyScholarship() {
		$user = User::find( Auth::user()->id );
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss College Event';
		$data['currentPage'] = 'portal';
		// $token = AjaxToken::where( 'user_id', '=', $user->id )->first();

		// if ( !$token ) {
		// 	Auth::logout();
		// 	return redirect( '/' );
		// }

		return View( 'private.portal.ajax.managescholarships.applyscholarship' , $data );
	}

	public function addSchool() {
		$user = User::find( Auth::user()->id );
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss Manage School';
		$data['currentPage'] = 'portal';
		// $token = AjaxToken::where( 'user_id', '=', $user->id )->first();

		// if ( !$token ) {
		// 	Auth::logout();
		// 	return redirect( '/' );
		// }

		$datastring=Request::get( 'datastring' );
		$success='';
		if ( isset( $datastring ) && $datastring!='' ) {
			$collegelist = new CollegeList;
			$params = array();
			parse_str( $datastring, $params );
			$data_array=array();
			$data_array=$params['college_id'];

			$insert_array=array();
			foreach ( $data_array as $key=>$c_id ) {
				$checkdata = CollegeList::where( 'school_id', '=', $c_id )->where( 'user_id', '=', $user->id )->first();
				if ( $checkdata=='' ) {
					$insert_array['user_id']=$user->id;
					$insert_array['school_id']=$c_id;
					$insert_array['status']='just looking';

					/*$collegelist->user_id =$user->id;
					$collegelist->school_id =$c_id;
					$collegelist->status ='just looking';*/
					$resultval=$collegelist->insertGetId( $insert_array );
				}
			}

			echo $success=1;
			exit();
		}

		return View( 'private.portal.ajax.manageschool.addschool' , $data );
	}


	//set Your list status
	public function setlistStatus() {
		$status=Request::Get( 'status' );
		$trcolor='';
		$ID=Request::Get( 'ID' );
		$status_val='';
		if ( $status==1 ) {
			$status_val='applied';
			$trcolor='#EAF6FC';
		}
		elseif ( $status==2 ) {
			$status_val='accepted';
			$trcolor='#E9F7ED';
		}
		else {
			$status_val='just looking';
			$trcolor='#FFFFFF';
		}

		$collegelist = new CollegeList;
		$collegelist = CollegeList::find( $ID );
		$collegelist->status=$status_val;
		$collegelist->save();

		$return_array=array();
		$return_array['status_val']=$status_val;
		$return_array['trcolor']=$trcolor;

		$json=json_encode( $return_array );
		echo $json;

		exit;
	}

	//set trash School
	public function trashSchool() {

		$ID=Request::Get( 'ID' );
		$tab=Request::Get( 'tab' );
		$menu=Request::Get( 'menu' );


		if ( $ID!="" ) {
			$arrId=explode( "|", $ID );
			if ( count( $arrId )>0 ) {
				foreach ( $arrId as $key=>$Id ) {

					$data=explode( "-", $Id );
					if ( isset( $data[1] ) && $data[1]=='college_list' ) {
						$collegelist=CollegeList::where( 'id', $data[0] )->update( array( 'trash' => 1 ) );
					}
					else {
						$notification=Notification::where( 'id', $data[0] )->update( array( 'trash' => 1 ) );
					}
				}
			}
		}

		exit;
	}



	public function trashScholarships(){

		$input = Request::all();
		if(isset($input['trashList'])){
			$trashList = $input['trashList'];
		}else{
			$trashList = array();
			$trashList[] = $input['scholarship_id'];
		}
		

		$res = null;
		if ( Auth::check() ) {
			//Get user logged in info and ajaxtoken.
			$user = User::find( Auth::user()->id );
			// $token = $user->ajaxtoken->toArray();

			$user_id=$user->id;

			//foreach scholarship -- update row
			// YourTable::query()->whereIn('scholarship_id', $trashList)->update(...
			$schModel = new ScholarshipsUserApplied;
			$res = $schModel->trashScholarships($user_id, $trashList);
		}

		return "success";
	}


	//set  restore school
	public function restoreSchool() {

		$ID=Request::Get( 'ID' );
		$tab=Request::Get( 'tab' );
		$menu=Request::Get( 'menu' );

		if ( $ID!="" ) {
			$arrId=explode( "|", $ID );

			if ( count( $arrId )>0 ) {
				foreach ( $arrId as $key=>$Id ) {
					$data=explode( "-", $Id );
					if ( isset( $data[1] ) && $data[1]=='college_list' ) {
						$notification=CollegeList::where( 'id', $data[0] )->update( array( 'trash' =>NULL ) );
					}
					else {
						$notification=Notification::where( 'id', $data[0] )->update( array( 'trash' =>NULL ) );
					}
				}
			}
		}

		exit;
	}

	//Permanantly Delete School
	public function permanantlyDeleteSchool() {

		$ID=Request::Get( 'ID' );
		$tab=Request::Get( 'tab' );
		$menu=Request::Get( 'menu' );

		if ( $ID!="" ) {
			$arrId=explode( "|", $ID );
			if ( count( $arrId )>0 ) {
				foreach ( $arrId as $key=>$Id ) {

					$data=explode( "-", $Id );
					if ( isset( $data[1] ) && $data[1]=='college_list' ) {
						$collegelist=CollegeList::where( 'id', $Id )->update( array( 'trash' => 1, 'deleted' => 1 ) );
					}
					else {
						$notification=Notification::where( 'id', $Id )->update( array( 'trash' => 1, 'deleted' => 1 ) );
					}
				}
			}
		}

		exit;
	}


	public function updateGridOrder() {
		$listIDs=Request::Get( 'listIDs' );
		$user = User::find( Auth::user()->id );
		//$listdata=CollegeList::where('user_id',$user->id)->get();
		if ( $listIDs!="" ) {
			$arrId=explode( ",", trim( $listIDs, ',' ) );
			if ( count( $arrId )>0 ) {

				$sortorder=1;
				foreach ( $arrId as $key=>$Id ) {
					$data=explode( "-", $Id );
					if ( isset( $data[1] ) && $data[1]=='college_list' ) {
						$collegelist=CollegeList::where( 'id', $data[0] )->update( array( 'sortorder' =>$sortorder ) );
					}
					else {
						$notification=Notification::where( 'id', $data[0] )->update( array( 'trash' => 1 ) );
					}
					$sortorder++;
				}
			}
			echo 1;
		}
		exit();

	}



	// ajax testing in datatables
	public function getGridData1() {

		//Get user logged in info and ajaxtoken.
		$id = Auth::id();

		$user = User::find( $id );

		$token = $user->ajaxtoken->toArray();


		$aColumns = array(
			'college_list.id as list_id', 'colleges.id', 'colleges.school_name', 'colleges_ranking.plexuss', 'college_list.status', 'colleges.slug' , 'colleges.ipeds_id', 'colleges.address', 'colleges.city', 'colleges.state', 'colleges.long_state', 'colleges.zip', 'colleges.logo_url',
			'colleges.chief_title', 'colleges.chief_name', 'colleges.logo_url', 'colleges.alias',
		);
		$sIndexColumn = 'id';
		$sTable = 'college_list';
		/* * Paging*/
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
			$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
		}
		/** Ordering */
		$sOrder = "";
		if ( isset( $_GET['iSortCol_0'] ) ) {
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
				if ( $_GET[ 'bSortable_'.intval( $_GET['iSortCol_'.$i] ) ] == "true" ) {
					$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
						( $_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc' ) .", ";
				}
			}
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" ) {
				$sOrder = "";
			}
		}
		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset( $_GET['sSearch'] ) and $_GET['sSearch'] != "" ) {
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count( $aColumns ) ; $i++ ) {
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		/* Individual column filtering */
		for ( $i=0 ; $i<count( $aColumns ) ; $i++ ) {
			if ( isset( $_GET['bSearchable_'.$i] ) and $_GET['bSearchable_'.$i] == "true" and $_GET['sSearch_'.$i] != '' ) {
				if ( $sWhere == "" ) {
					$sWhere = "WHERE ";
				}
				else {
					$sWhere .= " AND ";
				}
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
			}
		}

		if ( $id ) {
			//$sWhere.=" and notify_permission=".$this->view->user->user_id;
			$sWhere.="WHERE college_list.user_id=".$id;
		}else {
			$sWhere="";
		}

		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace( " , ", " ", implode( ", ", $aColumns ) )." FROM  $sTable
					join colleges on (colleges.id=college_list.school_id)
					left join colleges_ranking on (colleges_ranking.ipeds_id=colleges.ipeds_id)
		 			$sWhere $sOrder $sLimit";
		$qry = DB::select( DB::raw( $sQuery ) );


		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  DB::select( DB::raw( $sQuery ) );

		$iFilteredTotal = $aResultFilterTotal[0]->fcnt;

		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable";
		$rResultTotal = DB::select( DB::raw( $sQuery ) );
		$iTotal = $rResultTotal[0]->cnt;

		/* * Output */
		$output = array(
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		$j=0;

		$onclickstr='';
		foreach ( $qry as $row1 ) {

			if ( $row1->logo_url!='' ) {
				$img_url='<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$row1->logo_url.'" class="college_logo"/>';
			}
			else {
				$img_url='<img src="/images/no_photo.jpg" class="college_logo"/>';
			}


			if ( $row1->status!='' ) {
				$status=$row1->status;
			}
			else {
				$status='none';
			}



			$row=array();

			$row[]='<input type="checkbox" name="'.$sTable.'['.$row1->$sIndexColumn.']" value="'.$row1->$sIndexColumn.'" class="check_group"/>';

			$row[]='
			 <div class="row pt10">
                                    <div class="college-arrow-up"></div>
                                    <div class="college-arrow-down"></div>

                                    <div class="fr rank f-bold mr10">#1</div>
                                    <div class="clear"></div>
                            </div>';

			$row[]="
					<div class='row'>
                                <div class='mall-2 medium-3 column no-padding show-for-medium-up'>
                                  ".$img_url."
                                </div>
                                <div class='small-12 medium-9 column pl5 pos-rel'>
                                   <div class='c-blue fs14 f-bold'>".$row1->school_name."<div class='status_green'><!--------></div></div>
                                    <span class='c79 fs12 d-block'>
                                        ".$row1->city." ".$row1->long_state." |
                                        <span data-spanID=".$row1->id." class='cursor'>
                                            quick facts
                                            <span id='quick-link-div' class='expand-toggle-span'>&nbsp;</span>
                                         </span>
                                    </span>
                                 </div>
                             </div>

					";
			$row[]='<div class="rankdiv">#'.$row1->plexuss.'</div>';
			$row[]='
				  <span class="cursor c79 fs12" id="status_val'.$row1->list_id.'" onClick="expandDivContent("status-span-'.$row1->list_id.'","menu-nav-div-'.$row1->list_id.'");" style="text-transform:capitalize">
                                    '.$status.'
                               		 <span class="expand-toggle-span" id="status-span-'.$row1->list_id.'"></span>
                                </span>
                                <div class="cursor c79 fs12 d-none menu-nav-div" id="menu-nav-div-'.$row1->list_id.'">
                                	<div class="menu-nav-div-arrow" style="top:-10px"></div>

                                    <ul class="mobile-top-nav pos-rel">
                                     	<div align="center" class="msgloader pt40 d-none pos-abs" style="left:55px; top:-5px;"><img src="/images/AjaxLoader.gif"></div>
                                    	<li class="pl15 pt5" onclick="setlistStatus("1",'.$row1->list_id.')">APPLIED</li>
                                        <li class="pl15 pt5" onclick="setlistStatus("2",'.$row1->list_id.')">ACCEPTED</li>
                                        <li class="pl15 pt5" onclick="setlistStatus("3",'.$row1->list_id.')">JUST LOOKING</li>
                                    </ul>
                                </div>
				';
			$row[]='<img src="/images/nav-icons/massage-gray.png">';

			$row[]='
					<tr class="row d-none" id="quick-link-'.$row1->id.'">
                        <td colspan="5" align="center">
                             text come here
                             <div class="row cursor" onClick="expandDivContent("quick-link-div-'.$row1->id.'","quick-link-'.$row1->id.'");" align="center">
                             		<span class="expand-toggle-span" id="quick-link-div-'.$row1->id.'">&nbsp;</span>
                                    <span class="fs10 pl20">Close</span>
                             </div>
                        </td>
                    </tr>  ';

			$output['aaData'][] = $row;
			$j++;
		}

		return Response::json( $output );
		exit();
	}



	//set get event in calender
	public function getEvents() {


		$events[0]= array(
			'id' => '1',
			'title' =>'All Day Event',
			'start' =>'2014-09-15',
			'end' => '2014-09-20',
			'url' => false,
			'allDay'=>false
			//'url' => ""
		);
		echo $json = json_encode( $events );
		exit;
	}

	public function getPortalData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$portalNums = array();

		// -- your list
		$inList = Recruitment::on('rds1')
			->where( 'user_id', '=', $data['user_id'] )
			->where( 'status', '=', 1 )
			->where('user_recruit', 1)
			->where('is_seen',0)
			->distinct('college_id')
			->count('college_id');

		if($inList){
			$inList_records = Recruitment::on('rds1')
			->select('id')
			->where( 'user_id', '=', $data['user_id'] )
			->where( 'status', '=', 1 )
			->where('user_recruit', 1)
			->where('is_seen',0)
			->distinct('college_id')
			->get();

			foreach($inList_records as $inlist){
				Recruitment::where( 'id', '=', $inlist['id'])
				           ->update(['is_seen' => '1']);
			}

		}

		// -- messages
		$unread = DB::connection('rds1')->table('college_message_thread_members')
			->where('user_id', '=', $data['user_id'])
			->sum('num_unread_msg');

		// -- recommended by plexuss
		$recommended = PortalNotification::on('rds1')
			->where( 'user_id', '=', $data['user_id'] )
			->where('is_seen', '=', '0' )
			->where( 'is_recommend' , '=' , '1' )
			->where( 'is_recommend_trash', '!=', '1' )
			->orderBy( 'id', 'DESC' )
			->count();
	
		// -- colleges want to recruit you
		$recruit = DB::connection('rds1')->table( 'colleges as c' )
			->join( 'recruitment as rt', 'c.id', '=', 'rt.college_id' )
			->join( 'colleges_ranking as cr', 'cr.college_id', '=' , 'c.id' )
			->select( 'c.id as college_id', 'c.school_name', 'c.slug as slug', 'c.logo_url as logo_url', 'c.city', 'c.state', 'cr.plexuss as rank' )
			->where( 'rt.user_id', $data['user_id'] )
			->where('rt.college_recruit', 1 )
			->where('rt.user_recruit', 0)
			->where('rt.status', 1)
			->where('rt.is_seen_recruit',0)
			->distinct('rt.college_id')
			->count('rt.college_id');

		// -- colleges viewed your profile
		$viewed = DB::connection('rds1')->table('notification_topnavs as nt')
									 ->join('colleges as c', 'c.school_name', '=', 'nt.name')
									 ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									 ->where('nt.command', 1)
									 ->where('nt.msg', 'viewed your profile')
									 ->where('nt.type_id', $data['user_id'])
									 ->where('nt.is_read', 0)
									 ->distinct('nt.id')
									 ->orderBy('nt.updated_at', 'desc')
									 ->count('nt.id');
									 // ->where('nt.is_seen', '0')
		// echo $viewed;									 
		// if($viewed){
		// 	$viewed_records = DB::connection('rds1')->table('notification_topnavs as nt')
		// 		 ->join('colleges as c', 'c.school_name', '=', 'nt.name')
		// 		 ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
		// 		 ->where('nt.command', 1)
		// 		 ->where('nt.msg', 'viewed your profile')
		// 		 ->where('nt.type_id', $data['user_id'])
		// 		 ->where('nt.is_seen', '0')
		// 		 ->distinct('nt.id')
		// 		 ->orderBy('nt.updated_at', 'desc')
		// 		 ->select('nt.id')
		// 		 ->get();
		// 	print_r($viewed_records);
		// 	exit;	
		// 	// foreach($viewed_records as $vr){
		// 	// 	echo $vr['id'];
		// 	// }
		// }

		// if( isset($data['topnav_notifications']) && isset($data['topnav_notifications']['data']) && !empty($data['topnav_notifications']['data']) ){
		// 	$topnav_notifications = $data['topnav_notifications']['data'];
		// 	$schools_name_arr = array();
		// 	foreach ($topnav_notifications as $key) {
		// 		if($key['command'] == 1 && $key['type'] == 'user'){
		// 			$schools_name_arr[] = $key['name'];
		// 		}
		// 	}
		// 	$viewed = DB::connection('rds1')->table( 'colleges as c' )
		// 		->join( 'colleges_ranking as cr', 'cr.college_id', '=' , 'c.id' )
		// 		->select( 'c.id as college_id', 'c.school_name', 'c.slug as slug', 'c.logo_url as logo_url', 'c.city', 'c.state', 'cr.plexuss as rank' )
		// 		->whereIn('c.school_name', $schools_name_arr)
		// 		->count();
		// }else{
		// 	$viewed = 0;
		// }

		$user = new User;
		$profile = $user->getUsersProfileData($data['user_id']);
		$decoded = json_decode($profile);

		$applications = count($decoded->applyTo_schools);

		// $applications = 0;

		// foreach ($decoded->applyTo_schools as $key) {
		// 	if ($key->submitted == 1) {
		// 		$applications += 1;
		// 	}
		// }


		// -- trashed
		// $trashed = Recruitment::on('rds1')->where('recruitment.user_id', '=', $data['user_id'] )
		// 	->where('recruitment.status', '=', 0 )
		// 	->leftjoin('recruitment as r2',function($join) use($data){
		// 		$join->on('recruitment.college_id','=','r2.college_id')
		// 			 ->where('r2.user_id','=',$data['user_id'])
		// 			 ->where('r2.status','=',1);
		// 		})
		// 	->whereNull('r2.id')
		// 	->distinct('recruitment.college_id')
		// 	->count('recruitment.college_id');

		$trashed = Recruitment::on('rds1')->where('recruitment.user_id', '=', $data['user_id'] )
										  ->where('recruitment.status', '=', 0 )
										  ->select('recruitment.*')
										  ->distinct('recruitment.college_id')
										  ->where('recruitment.is_seen_trash', '=', 0)
										  ->count('recruitment.college_id');


		$portalNums['portal'] = $inList;
		$portalNums['messages'] = $unread;
		$portalNums['recommendationlist'] = $recommended;
		$portalNums['collegesrecruityou'] = $recruit;
		$portalNums['collegesviewedprofile'] = $viewed;
		$portalNums['getTrashSchoolList'] = $trashed; //$trashed;
		$portalNums['applications'] = $applications;
		return $portalNums;
	}

	public function didJoyride(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		$input = Request::all();
		Cache::forever(env('ENVIRONMENT').'_'.$data['user_id'].'didJoyride', 1);
		return 'done';
	}

	/*------------------------------------ Ajax call function -------------------------------------- */



	private function checkToken( $token ) {
		$ajaxtoken = AjaxToken::where( 'token', '=', $token )->first();
		if ( !$ajaxtoken ) {
			return 0;
		} else {
			return 1;
		}
	}

	public function dontShowModal($userId){
			if(RecommendModalShow::where('user_id','=',$userId)->update(['recommend_modal_show'=>'0'])){
				return "success";
			}
			else
			{
				return "failed";
			}
	}
}
