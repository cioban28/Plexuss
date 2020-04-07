<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, DB, Session, Validator;
use Illuminate\Support\Facades\Auth;
use App\PlexussAdmin, App\User, App\Search;
use App\Http\Controllers\QuizController;

class RankingController extends Controller
{
    public function index(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
			
		$data['title']='Plexuss Ranking';
		$data['currentPage'] = 'ranking';

		//instantiating profile percentage to zero to show side bar when signed in
		$data['profile_perc'] = 0;
		
		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;

			//grabbing users profile percent to use to hide/show 'get started' right-hand-side bar
			$data['profile_perc'] = $user->profile_percent;


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
			
		}
		
		$data['profile_img_loc'] = $src;
		$RankingData=DB::select(DB::raw("select colleges_ranking.plexuss,colleges_ranking.College,colleges.city,colleges.state,colleges.slug from colleges_ranking 
		join colleges on (colleges_ranking.college_id=colleges.id)
		order by -plexuss desc
		limit 3"));
		$data['RankingData']=$RankingData;
		
		$catData=DB::connection('rds1')->table('lists')->take(3)->get();
		$data['catData']=$catData;
		
		$quizs = new QuizController();
		$data['quizInfo'] = $quizs->LoadQuiz();

		$data['right_handside_carousel'] = $this->getRightHandSide($data);
		
		return View('private.ranking.index', $data);
	}


	public function categories($is_api = true){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		

		//instantiating profile percentage to zero to show side bar when signed in
		$data['profile_perc'] = 0;

		$data['title']='College Comparison and Rankings | College Recruiting Network | Plexuss.com';
		$data['meta_desc']='Find Plexuss college comparison and rankings of American universities. Compare various college rankings of all the major sources; including, US News, Reuters, Forbes, QS, and Shanghai rankings.';
		$data['meta_keyword']='college comparison';


		$data['currentPage'] = 'ranking';		
		$src="/images/profile/default.png";
		if (Auth::check())
		{
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;

			//grabbing users profile percent to use to hide/show 'get started' right-hand-side bar
			$data['profile_perc'] = $user->profile_percent;

			if($user->profile_img_loc!="")
			{
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

		if ( !$user->email_confirmed )
		{
			array_push( $data['alerts'], 
				array(
					'img' => '/images/topAlert/envelope.png',
					'type' => 'hard',
					'dur' => '10000',
					'msg' => '<span class=\"pls-confirm-msg\">Please confirm your email to gain full access to Plexuss tools.</span> <span class=\"go-to-email\" data-email-domain=\"'.$data['email_provider'].'\"><span class=\"hide-for-small-only\">Go to</span><span class=\"show-for-small-only\">Confirm</span> Email</span> <span> | <span class=\"resend-email\">Resend <span class=\"hide-for-small-only\">Email</span></span></span> <span> | <span class=\"change-email\" data-current-email=\"'.$data['email'].'\">Change email <span class=\"hide-for-small-only\">address</span></span></span>'
				)
			);
		}
		
		}
		$data['profile_img_loc'] = $src;
		

		$RankingData = DB::select(DB::raw("
		select 
		    lists.id,
		    lists.title,
		    lists.type,
		    lists.image,
		    lists.source,
		    colleges.school_name,
		    colleges.city,
		    colleges.state,
		    list_schools.colleges_id,
		    list_schools.order,
		    colleges.slug
		from
		    lists
		        left join
		    list_schools ON (lists.id = list_schools.lists_id)
		        left join
		    colleges ON (list_schools.colleges_id = colleges.id)
		        left join
		    colleges_ranking ON (colleges.id = colleges_ranking.college_id)
		where
		    type = 'interesting'
		order by lists.title desc , - list_schools.order desc
		"));

		$listArray = array();

		foreach ($RankingData as $key => $value){
			$listArray[$value->title][]=$value;
		}

		//echo "<pre>";
		//print_r($listArray);
		//exit();
		$data['list_array']=$listArray;

		$quizs = new QuizController();
		$data['quizInfo'] = $quizs->LoadQuiz();
		
		$data['right_handside_carousel'] = $this->getRightHandSide($data);

		if (isset($is_api)) {
			return json_encode($data);
		}
		return View('private.ranking.categories', $data);
	}


	public function listing($is_api = true){
		// Create $data array
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title']='Plexuss College Ratings | College Recruiting Network | Plexuss.com';
		$data['meta_desc']='Find Plexuss college ratings of American universities. A comprehensive college ranking from all the major sources; including, US News, Reuters, Forbes, QS, and Shanghai rankings.';
		$data['meta_keyword']='college ratings';

		$data['currentPage'] = 'ranking';
		$data['PageAction']='listing';

		// $session = Session::all();
		$user_info = Session::get( 'userinfo' );

		if( $user_info && $user_info['signed_in']){
			$user = Session::get( 'user_table' );
		}

		$src="/images/profile/default.png";

		if ($data['signed_in'] == 1) {
			
			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}

		}
		
		$data['profile_img_loc'] = $src;

		/***********************************************************************
		 *======================== VALIDATE INPUT!!! ===========================
		 ***********************************************************************
		 */
		$input = Request::all();
		$rules = array(
			'school_name_tag_list' => 'nullable|regex:/^\[([0-9a-zA-Z\-",])+\]$/',
			'state_tag_list' => array(
				'regex:/^\[([A-Z\",])+\]$/'
			),
			'ranking_zip' => 'nullable|regex:/^\d{5}(-\d{4})?$/',

			'ranking_search_zip_max' => array(
				'regex:/^([0-9]){1,5}$/'
			),
			'ranking_search_zip_min' => array(
				'regex:/^([0-9]){1,5}$/'
			),
			'degree_tag_list' => 'nullable|regex:/^\[([a-z_",])+\]$/',
			'school_sector_tag_list' => 'nullable|regex:/^\[([24a-zA-Z,\-" ])+\]$/',
			'ranking_source_tag_list' => 'nullable|regex:/^\[([a-z_",])+\]$/',
			'campus_housing' => array(
				'regex:/^Yes$/'
			),
			'campus_setting_tag_list' => 'nullable|regex:/^\[([a-zA-Z:", ])+\]$/',
			'tuition_fee_max' => 'nullable|integer',
			'tuition_fee_min' => 'nullable|integer',
			'undergrade_max' => 'nullable|integer',
			'undergrade_min' => 'nullable|integer',
			'admitted_max' => array(
				'max:100',
				'min:0'
			),
			'admitted_min' => array(
				'max:100',
				'min:0'
			),
			'sat_read_max' => 'nullable|integer',
			'sat_read_min' => 'nullable|integer',
			'sat_math_max' => 'nullable|integer',
			'sat_math_min' => 'nullable|integer',
			'act_composite_max' => 'nullable|integer',
			'act_composite_min' => 'nullable|integer',
			'religious_affiliation_tag_list' => 'nullable|regex:/^\[([a-zA-Z\(\),\-", ])+\]$/',
			'Search' => 'alpha'
		);

		/*********************************************************************** 
		 *=================== Get and set Search Parameters ===================
		 ***********************************************************************
		 * Not used unless these pass validation!
		 */
		$more=0;
		$schoolName=Request::get('school_name_tag_list');
		$ranking_state=Request::get('state_tag_list');
		$ranking_zip=Request::get('ranking_zip');
		$ranking_degree=Request::get('degree_tag_list');
		$ranking_school_sector=Request::get('school_sector_tag_list');
		$ranking_source=Request::get('ranking_source_tag_list');
		$campus_housing=Request::get('campus_housing');
		$campus_settings=Request::get('campus_setting_tag_list');
		$tuition_fee_max=Request::get('tuition_fee_max');
		$tuition_fee_min=Request::get('tuition_fee_min');
		$undergrade_max=Request::get('undergrade_max');
		$undergrade_min=Request::get('undergrade_min');
		$admitted_max=Request::get('admitted_max');
		$admitted_min=Request::get('admitted_min');
		$ranking_search_zip_max=Request::get('ranking_search_zip_max');
		$ranking_search_zip_min=Request::get('ranking_search_zip_min');
		$sat_read_min=Request::get('sat_read_min');
		$sat_read_max=Request::get('sat_read_max');
		$sat_math_min=Request::get('sat_math_min');
		$sat_math_max=Request::get('sat_math_max');
		$act_composite_min=Request::get('act_composite_min');
		$act_composite_max=Request::get('act_composite_max');
		$religious_affiliation=Request::get('religious_affiliation_tag_list');

		/* Set Page, sort, order variables to pass back to view
		 */
		$page = Request::get('page');
		$sort = Request::get('sort');
		$order = Request::get('order');

		if( !isset( $page ) && $page == "" ) {
			$page = 1;
		}
		if( !isset( $order ) && $order == "" ) {
			$order = "asc";
		}
		if( !isset( $sort ) && $sort == "" ) {
			$sort = "ISNULL(plexuss), plexuss";
		}
		if( $sort != "College" ) {
			$orderBy = $order == 'asc' ? " " . $sort . " asc" : " " . $sort . " desc";
		}
		else {
			$orderBy = " College " . $order;
		}

		$data['PageLayout'] = "right";
		$data['pageNo'] = $page;
		$data['order'] = $order;
		$data['sort'] = $sort;

		/* Variables to set correct page on page load. Jinita Code.
		 */
		$data['QueryStrings'] = $_REQUEST;
		$data['qstring'] = array( "sort" => $sort, "order" => $order);
		unset( $data['QueryStrings']['page'] );
		$data['qstring'] = array_merge( $data['qstring'], $data['QueryStrings'] );
		$data['ReqeustStr'] = "";
		if( isset( $data['QueryStrings'] ) and count( $data['QueryStrings'] ) > 0 ) {

			foreach($data['QueryStrings'] as $keyRequest=>$dataRequest) {

				if($keyRequest=="sort" || $keyRequest=="order") {
					continue;
				}
				$data['ReqeustStr'].="&".$keyRequest."=".urlencode($dataRequest);
			}
		}

		// Validator
		$validator = Validator::make( $input, $rules );
		if( $validator->fails() ){
			$messages = $validator->messages();
			array_push( $data['alerts'], 
				array(
					'img' => '/images/topAlert/urgent.png',
					'bkg' => '#de4728',
					'type' => 'soft',
					'dur' => '3500',
					'msg' => 'There was an error with your query.'
				)
			);
		}
		/***********************************************************************/
		// IF WE PASSED VALIDATION
		else{
			$RankingData=DB::connection('rds1')->table('colleges')
			->leftJoin('colleges_ranking', 'colleges_ranking.college_id', '=', 'colleges.id')
			->leftJoin('colleges_admissions', 'colleges.id', '=', 'colleges_admissions.college_id')
			->select('colleges_ranking.plexuss', 'colleges_ranking.us_news','colleges_ranking.reuters','colleges_ranking.forbes','colleges_ranking.qs','colleges_ranking.shanghai_academic','colleges.school_name','colleges.city','colleges.state','colleges.slug','colleges.id','colleges.logo_url');

			// Used to decide whether to use a ->where or a ->orWhere
			$has_where = false;

			// Decode incoming JSON
			$school_name_decoded = json_decode( $schoolName );

			if(isset($school_name_decoded) && count($school_name_decoded) > 0) {
				foreach($school_name_decoded as $key=>$sname) {
					if(trim($sname)!="") {
						$RankingData = $has_where ? $RankingData->orWhere( 'colleges.slug', $sname ) : $RankingData->where( 'colleges.slug', $sname );
						$has_where = true;
					}
				}
			}

			// Decode incoming JSON
			$state_decoded = json_decode( $ranking_state );

			if( isset( $state_decoded ) && count( $state_decoded ) > 0 ) {
				foreach($state_decoded as $key=>$sname) {
					if( trim( $sname ) != "" ) {
						$RankingData = $has_where ? $RankingData->orWhere( 'colleges.state', $sname ) : $RankingData->where( 'colleges.state', $sname );
						$has_where = true;
					}
				}
			}
			
			// Decode json string and search, if decoding successful
			$school_sector_decoded = json_decode( $ranking_school_sector );

			if(isset($school_sector_decoded) && count($school_sector_decoded)>0) {
				foreach($school_sector_decoded as $key => $sname) {
					if(trim($sname)!="") {
						$RankingData = $has_where ? $RankingData->orWhere( 'colleges.school_sector', $sname ) : $RankingData->where( 'colleges.school_sector', $sname );
						$has_where = true;
					}
				}
			}
			
			// Decode json string and search if decode success
			$campus_setting_decoded = json_decode( $campus_settings );

			if(isset($campus_setting_decoded) && count($campus_setting_decoded)>0) {
				foreach($campus_setting_decoded as $key=>$sname) {
					if(trim($sname)!="") {
						$RankingData = $has_where ? $RankingData->orWhere( 'colleges.locale', $sname ) : $RankingData->where( 'colleges.locale', $sname );
						$has_where = true;
					}
				}
			}
			
			// Decode json string and search if decode success
			$religious_affiliation_decoded = json_decode( $religious_affiliation );

			if(isset($religious_affiliation_decoded) && count($religious_affiliation_decoded)>0) {
				foreach($religious_affiliation_decoded as $key=>$sname) {
					if(trim($sname)!="") {
						$RankingData = $has_where ? $RankingData->orWhere( 'colleges.religious_affiliation', $sname ) : $RankingData->where( 'colleges.religious_affiliation', $sname );
						$has_where = true;
					}
				}
			}
			
			// Decode json string and search if decode success
			$degree_decoded = json_decode( $ranking_degree );

			if(isset($degree_decoded) && count($degree_decoded)>0) {
				foreach($degree_decoded as $key=>$sname) {
					if(trim($sname)!="") {
						$RankingData = $has_where ? $RankingData->orWhere( 'colleges.' . $sname, 'Yes' ) : $RankingData->where( 'colleges.' . $sname, 'Yes' );
						$has_where = true;
					}
				}
			}
			
			// Decode json string and search if decode success
			$ranking_source_decoded = json_decode( $ranking_source );

			if(isset($ranking_source_decoded) && count($ranking_source_decoded)>0) {
				foreach($ranking_source_decoded as $key=>$sname) {
					if(trim($sname)!="") {
						$RankingData = $has_where ? $RankingData->orWhere( 'colleges_ranking.' . $sname, '!=', '' ) : $RankingData->where( 'colleges_ranking.' . $sname, '!=', '' );
						$has_where = true;
					}
				}
			}
			
			if($ranking_search_zip_max=="") {
				$ranking_search_zip_max="100";
			}
			if($ranking_search_zip_min=="") {
				$ranking_search_zip_min="0";
			}
			if($ranking_zip!="") {
				$objSearch=new Search();
				$LatLang=$objSearch->getUserLocationByZip($ranking_zip);
				if($ranking_search_zip_min!='' && $ranking_search_zip_max!='') {
					$RankingData=$RankingData->whereRaw("( 3959 * acos( cos( radians(".$LatLang[0].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$LatLang[1].") ) + sin( radians(".$LatLang[0].") ) * sin(radians(latitude)) ) ) between ".$ranking_search_zip_min." and ".$ranking_search_zip_max."");
				}
				else {
				$RankingData=$RankingData->where('colleges.zip','=',$ranking_zip);
				}
			}
			if(isset($campus_housing) && $campus_housing=="Yes")
			{
			$RankingData=$RankingData->where('colleges.campus_housing','=','Yes');
			$more=1;
			}
			if($sat_read_min!="")
			{
			$RankingData=$RankingData->where('colleges_admissions.sat_read_25','>=',$sat_read_min);
			$more=1;
			}
			if($sat_read_max!="")
			{
			$RankingData=$RankingData->where('colleges_admissions.sat_read_25','<=',$sat_read_max);
			$more=1;
			}
			if($sat_math_min!="")
			{
			$RankingData=$RankingData->where('colleges_admissions.sat_math_25','>=',$sat_math_min);
			$more=1;
			}
			if($sat_math_max!="")
			{
			$RankingData=$RankingData->where('colleges_admissions.sat_math_25','<=',$sat_math_max);
			$more=1;
			}
			if($act_composite_min!="")
			{
			$RankingData=$RankingData->where('colleges_admissions.act_composite_25','>=',$act_composite_min);
			$more=1;
			}
			if($act_composite_max!="")
			{
			$RankingData=$RankingData->where('colleges_admissions.act_composite_25','<=',$act_composite_max);
			$more=1;
			}
			if($tuition_fee_min!="")
			{
			$RankingData=$RankingData->where('colleges.tuition_fees_1213','>=',$tuition_fee_min);
			$more=1;
			}
			if($tuition_fee_max!="")
			{
			$RankingData=$RankingData->where('colleges.tuition_fees_1213','<=',$tuition_fee_max);
			$more=1;
			}		
			if($undergrade_min!="")
			{
			$RankingData=$RankingData->where('colleges.undergrad_enroll_1112','>=',$undergrade_min);
			$more=1;
			}
			if($undergrade_max!="")
			{
			$RankingData=$RankingData->where('colleges.undergrad_enroll_1112','<=',$undergrade_max);
			$more=1;
			}		
			if($admitted_min!="")
			{
			$RankingData=$RankingData->where('colleges_admissions.percent_admitted','>=',$admitted_min);
			$more=1;
			}
			if($admitted_max!="")
			{
			$RankingData=$RankingData->where('colleges_admissions.percent_admitted','<=',$admitted_max);
			$more=1;
			}

			$RankingData=$RankingData->orderByRaw($orderBy);//$sort,$order
			$RankingData = $RankingData->where('colleges.verified', 1);
			$RankingData=$RankingData->paginate(10);
			$data['RankingData']=$RankingData;

		} // END VALIDATED ELSE BLOCK

		//Code to get Default Data
		$sliderData1 = DB::connection('rds1')->select(DB::raw("select 
			min(tuition_fees_1213) as minTuition,
			max(tuition_fees_1213) as maxTuition,
			min(undergrad_enroll_1112) as minUndergrad,
			max(undergrad_enroll_1112) as maxUndergrad,
			min(percent_admitted) as minAdmitted,
			max(percent_admitted) as maxAdmitted
			from 
			colleges
			join colleges_admissions on(colleges.id=colleges_admissions.college_id)
			where colleges.verified = 1"
		));

		$data['sliderData1']=$sliderData1[0];
		
		$minTuition=$sliderData1[0]->minTuition;
		if($tuition_fee_min!="") {

			$minTuition=$tuition_fee_min;
		}
		$maxTuition=$sliderData1[0]->maxTuition;
		if($tuition_fee_max!="") {

			$maxTuition=$tuition_fee_max;
		}
		$minUndergrad=$sliderData1[0]->minUndergrad;
		if($undergrade_min!="") {

			$minUndergrad=$undergrade_min;
		}
		$maxUndergrad=$sliderData1[0]->maxUndergrad;
		if($undergrade_max!="") {

			$maxUndergrad=$undergrade_max;
		}
		$minAdmitted=$sliderData1[0]->minAdmitted;
		if($admitted_min!="") {

			$minAdmitted=$admitted_min;
		}
		$maxAdmitted=$sliderData1[0]->maxAdmitted;
		if($admitted_max!="") {

			$maxAdmitted=$admitted_max;
		}
		
		$data['minTuition']=$minTuition;
		$data['maxTuition']=$maxTuition;
		$data['minUndergrad']=$minUndergrad;
		$data['maxUndergrad']=$maxUndergrad;
		$data['minAdmitted']=$minAdmitted;
		$data['maxAdmitted']=$maxAdmitted;
		/***********************************************************************
		 *============= Ranking Search $data array loopback stuff ==============
		 ***********************************************************************
		 */
		$data['ranking_search_zip_max']=$ranking_search_zip_max;
		$data['ranking_search_zip_min']=$ranking_search_zip_min;
		$data['displayMoreFilter'] = $more ? 'block' : 'none';
		/***********************************************************************/

		/***********************************************************************
		 *================= JSON LOOPBACK FOR TAG INFORMATION ==================
		 ***********************************************************************
		 * This block sends the JSON array of tag information back to the browser
		 * to be rebuilt. This is done so that if a user searches, their search tags
		 * are rebuilt in their kayak once their browser receives the returned results.
		 */
		if (Request::has('school_name_tag_list_json')){
		    $data['school_name_tag_list_json'] = Request::get('school_name_tag_list_json');
		}
		if( Request::has('state_tag_list_json') ){
			$data['state_tag_list_json'] = Request::get( 'state_tag_list_json' );
		}
		if( Request::has('degree_tag_list_json') ){
			$data['degree_tag_list_json'] = Request::get( 'degree_tag_list_json' );
		}
		if( Request::has('ranking_source_tag_list_json') ){
			$data['ranking_source_tag_list_json'] = Request::get( 'ranking_source_tag_list_json' );
		}
		if( Request::has('campus_setting_tag_list_json') ){
			$data['campus_setting_tag_list_json'] = Request::get( 'campus_setting_tag_list_json' );
		}
		if( Request::has('school_sector_tag_list_json') ){
			$data['school_sector_tag_list_json'] = Request::get( 'school_sector_tag_list_json' );
		}
		if( Request::has('religious_affiliation_tag_list_json') ){
			$data['religious_affiliation_tag_list_json'] = Request::get( 'religious_affiliation_tag_list_json' );
		}
		/***********************************************************************
		 *================= JSON LOOPBACK FOR TAG INFORMATION ==================
		 ***********************************************************************/

		/***********************************************************************
		 *================== Build select element options ======================
		 ***********************************************************************
		 * Builds dropdown data for select input elements. Saves us some DB calls but, not
		 * the most elegant.
		 * I know this is not neat, but I need further input from AO/AS on how to proceed here
		 */
		// Build degree type array
		$data['degree_select'] = array(
			'' => 'Degrees by type',
			'bachelors_degree' => "Bachelor's Degree",
			'masters_degree' => "Master's Degree",
			'post_masters_degree' => "Post Master's Degree",
			'doctors_degree_research' => "Doctor's Degree - Research",
			'doctors_degree_professional' => "Doctor's Degree - Professional"
		);

		$data['ranking_source_select'] = array(
			'' => 'Ranking sources',
			'plexuss' => 'Plexuss',
			'us_news' => 'U.S. News',
			'reuters' => 'Reuters',
			'forbes' => 'Forbes',
			'qs' => 'QS',
			'shanghai_academic' => 'Shanghai Academic'
		);

		$data['school_sector_select'] = array(
			'' => 'Institutions by type',
			'Public, 2-year' => 'Public, 2-year',
			'Public, 4-year or above' => 'Public, 4-year or above',
			'Public, less-than 2-year' => 'Public, less-than 2-year',
			'Private for-profit, 2-year' => 'Private for-profit, 2-year',
			'Private for-profit, 4-year or above' => 'Private for-profit, 4-year or above',
			'Private for-profit, less-than 2-year' => 'Private for-profit, less-than 2-year',
			'Private not-for-profit, 2-year' => 'Private not-for-profit, 2-year',
			'Private not-for-profit, 4-year or above' => 'Private not-for-profit, 4-year or above',
			'Private not-for-profit, less-than 2-year' => 'Private not-for-profit, less-than 2-year'
		);

		$data['campus_setting_select'] = array(
			'' => 'Campus Settings',
			'City: Large' => 'City: Large',
			'City: Midsize' => 'City: Midsize',
			'City: Small' => 'City: Small',
			'Rural: Distant' => 'Rural: Distant',
			'Rural: Fringe' => 'Rural: Fringe',
			'Rural: Remote' => 'Rural: Remote',
			'Suburb: Large' => 'Suburb: Large',
			'Suburb: Midsize' => 'Suburb: Midsize',
			'Suburb: Small' => 'Suburb: Small',
			'Town: Distant' => 'Town: Distant',
			'Town: Fringe' => 'Town: Fringe',
			'Town: Remote' => 'Town: Remote'
		);

		/***********************************************************************
		 *================== Build select element options ======================
		 ***********************************************************************/
		if (isset($is_api)) {
			return json_encode($data);
		}
		//Code to get Default Data	
		return View('private.ranking.listing', $data);
	}


	public function getschools(){
		$strSearch=Request::get('q');
		$where="";
		if($strSearch!="")
		{			
		$where="where school_name like '%".$strSearch."%'";
		}
		$schoolData=DB::select(DB::raw("select school_name	from colleges $where limit 0,20"));
		$SchoolNameArr=array();
		foreach($schoolData as $key=>$data)
		{
		$SchoolNameArr[]=$data->school_name;
		}
		echo json_encode($SchoolNameArr);
		exit();
	}

	public function view()
	{
		
	}
}
