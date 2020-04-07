<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '1G'); // or you could use 1G

use App\EmailSuppressionList;
use App\NewsArticle;
use App\NewsCollegeMapping;
use Carbon\Carbon;
use Request, Session, DB, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Cache;

use App\College, App\PlexussAdmin, App\AdPage, App\AdAffiliate, App\PlexussBannerCopy, App\CollegesInternationalTab, App\CollegeOverviewImages, App\Search, App\CollegeProgram;
use App\LikesTally, App\CollegeStats, App\User, App\CollegeMessageThreadMembers, App\CollegeCustomTuition;

use App\Http\Controllers\ChatMessageController, App\Http\Controllers\UtilityController;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Agency, App\AgencyPermission, App\AgencyUserReview;
use App\DistributionClient;
use App\RevenueProgram;
use App\RevenueProgramsSellingPoint;
use App\MetaDataCollegesByState, App\MetaDataMajor;
use Illuminate\Support\Facades\Input;

class CollegeController extends Controller
{
    	//to determine if we're  ajaxing information or we're loading the information
	//if we are ajaxing we need to return a view, if we're loading the page , we need
	//to return data to index() method switch case
	protected $cid_for_college_page = -1;


	public function index() {

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss Colleges';
		$data['currentPage'] = 'college-home';

		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;


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


		} else {

		}
		$data['profile_img_loc'] = $src;

		$data['type']='college';
		$data['term']='';


		//also prepop department select -- with dept_categories
		$searchModel = new Search();
		$depts_cat = $searchModel->getDepts();
		$data['depts_cat'] = $depts_cat;



		//get college department categories
		$collegeModel = new College();
		$depts = $collegeModel->getDepartmentCats();

		// dd($depts);

		$data['depts'] = $depts;


		/* Code to get lists */
		//$collegeModel = new College();

		/* Code to get College lists */
		//$college_lists = $collegeModel->getInterestingList();


		//$data['college_lists'] =$college_lists;


		//$lists_schools = $collegeModel->getCollegeListSchools();

		//$data['lists_schools'] = $lists_schools;
		$listReturn=DB::select(DB::raw("select `lists`.`id` as `list_id`, `lists`.`type` as `list_type`, `lists`.`title` as `list_title`, `list_schools`.`colleges_id` as `college_id`, `C`.`school_name`, `C`.`slug`, `C`.`city`, `C`.`long_state`, `R`.`plexuss` as `plexuss_rating` from `lists` left join `list_schools` on `lists`.`id` = `list_schools`.`lists_id` left join `colleges` as `C` on `list_schools`.`colleges_id` = `C`.`id` left join `colleges_ranking` as `R` on `C`.`id` = `R`.`college_id` order by `list_type` desc, `list_title` desc, -`plexuss_rating` desc"));
		//print_r($data);
		//exit();
		//Below query is commented to get the data as per ranking as -(minus) sign in giving error in the below query
		/*$listReturn = DB::table('lists')
		->select( 'lists.id as list_id', 'lists.type as list_type', 'lists.title as list_title', 'list_schools.colleges_id as college_id', 'C.school_name', 'C.slug', 'C.city', 'C.long_state', 'R.plexuss as plexuss_rating')
		->leftJoin('list_schools', 'lists.id', '=', 'list_schools.lists_id')
		->leftJoin( 'colleges as C', 'list_schools.colleges_id', '=', 'C.id' )
		->leftJoin( 'colleges_ranking as R', 'C.id', '=', 'R.college_id' )
		->orderBy('list_type', 'desc')
		->orderBy('list_title', 'desc')
		->orderBy('-`plexuss_rating`', 'desc')
		->toSql();
		echo $listReturn ;
		exit();*/
		$listArray = array();

		foreach ($listReturn as $key => $value) {
			$listArray[$value->list_type][$value->list_title][] = $value;
		}

		//echo '<pre>';
		//print_r($listArray);
		//echo '</pre>';
		//exit;

		$data['lists'] = $listArray;


		//Build the directory for the A section. needs to be revisted for perforance!
		$dirCollege = new College();
		$dirAList = $dirCollege->Colleges('A');
		$data['dirAList']=$dirAList;

		//return 'Hello Home is working.';
		return View('private.college.collegeHome', $data);
	}


	public function getMajorsView() {

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss Colleges | Majors';
		$data['currentPage'] = 'majors';

		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;


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


		} else {

		}
		$data['profile_img_loc'] = $src;

		$data['type']='college';
		$data['term']='a';





		/* Code to get lists */
		//$collegeModel = new College();

		/* Code to get College lists */
		//$college_lists = $collegeModel->getInterestingList();


		//$data['college_lists'] =$college_lists;


		//$lists_schools = $collegeModel->getCollegeListSchools();

		//$data['lists_schools'] = $lists_schools;
		$listReturn=DB::select(DB::raw("select `lists`.`id` as `list_id`, `lists`.`type` as `list_type`, `lists`.`title` as `list_title`, `list_schools`.`colleges_id` as `college_id`, `C`.`school_name`, `C`.`slug`, `C`.`city`, `C`.`long_state`, `R`.`plexuss` as `plexuss_rating` from `lists` left join `list_schools` on `lists`.`id` = `list_schools`.`lists_id` left join `colleges` as `C` on `list_schools`.`colleges_id` = `C`.`id` left join `colleges_ranking` as `R` on `C`.`id` = `R`.`college_id` order by `list_type` desc, `list_title` desc, -`plexuss_rating` desc"));
		//print_r($data);
		//exit();
		//Below query is commented to get the data as per ranking as -(minus) sign in giving error in the below query
		/*$listReturn = DB::table('lists')
		->select( 'lists.id as list_id', 'lists.type as list_type', 'lists.title as list_title', 'list_schools.colleges_id as college_id', 'C.school_name', 'C.slug', 'C.city', 'C.long_state', 'R.plexuss as plexuss_rating')
		->leftJoin('list_schools', 'lists.id', '=', 'list_schools.lists_id')
		->leftJoin( 'colleges as C', 'list_schools.colleges_id', '=', 'C.id' )
		->leftJoin( 'colleges_ranking as R', 'C.id', '=', 'R.college_id' )
		->orderBy('list_type', 'desc')
		->orderBy('list_title', 'desc')
		->orderBy('-`plexuss_rating`', 'desc')
		->toSql();
		echo $listReturn ;
		exit();*/
		$listArray = array();

		foreach ($listReturn as $key => $value) {
			$listArray[$value->list_type][$value->list_title][] = $value;
		}

		//echo '<pre>';
		//print_r($listArray);
		//echo '</pre>';
		//exit;

		$data['lists'] = $listArray;


		//Build the directory for the A section. needs to be revisted for perforance!
		$dirCollege = new College();
		$dirAList = $dirCollege->Colleges('A');
		$data['dirAList']=$dirAList;

		//return 'Hello Home is working.';
		return View('private.college.collegeMajorsPage', $data);
	}

	// This handles the SINGLE VIEW of the college pages.
	public function view( $slug="", $type=""){

        ini_set('max_execution_time', 120);

		// Check for college if it needs to be redirected.
		$is_redirect = College::on('rds1')->where('slug', $slug)->where('verified', 1)->first();

		if (!isset($is_redirect)) {
			// return App::make("ErrorController")->callAction("error", ['code'=> 404, 'exception'=> '']);
			$ec = new ErrorController();
			return $ec->error(404, '');
		}

		if ($is_redirect->redirect != -2) {
			$redirect_to = College::on('rds1')->where('ipeds_id', $is_redirect->redirect)->where('verified', 1)->first();
			return redirect('college/' . $redirect_to->slug);
		}


		//we will no longer allow ids to work for colleges ever.
		//Any attempt to pull up a id will redirect to the college name.
		if(is_numeric($slug)){
			$schoolName = DB::table('colleges')->select('slug')->where( 'id', $id )->first();
			return redirect('college/' . $schoolName->slug);
		}



		// This is where you 'could' place the Pixel, PId, CamId, ClickId, RecruitCollegeId and save to session.

		// this sets the RecruitCollegeId if it is set. I tested it to make sure it works.
		if ( Request::get('RecruitCollegeId') ) {
			Session::put('RecruitCollegeId', Request::get('RecruitCollegeId'));
		}

		// End of temp linked in stuff!!!




		$pagetype=Request::get('type');

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss College Detail';
		$data['currentPage'] = 'college';
		$data['Section']=$type;
		$data['CollegName']="/college/".$slug."/";
		$data['college_slug'] = $slug;

		$iplookup = $this->iplookup($_SERVER['REMOTE_ADDR']);

		$agency_rep = Agency::join('agency_permissions as ap', 'ap.agency_id', '=', 'agency.id')
						    ->join('users as u', 'u.id', '=', 'ap.user_id')
						    ->where('agency.active', 1)
						    ->select('agency.id as agency_id', 'u.id as user_id', 'u.fname', 'agency.country', 'agency.logo_url');

		if (isset($iplookup)) {
			$agency_rep = $agency_rep->where('agency.country', '=', $iplookup['countryName']);
		}

		$agency_rep = $agency_rep->inRandomOrder()->first();

		if (isset($agency_rep)) {
			$tmp = [];

			if (isset($agency_rep->logo_url)) {
				$tmp['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/' . $agency_rep->logo_url;
			}

			$tmp['profile_slug'] = '/agency-profile/' . $agency_rep->agency_id . '/' . $agency_rep->user_id;
			$tmp['message_slug'] = '/portal/messages/' . $agency_rep->agency_id . '/agency';
			$tmp['location'] = $agency_rep->country;
			$tmp['fname'] = $agency_rep->fname;

			$review_average = AgencyUserReview::getAverage($agency_rep->agency_id);

			if (isset($review_average))
				$tmp['review_average'] = $review_average;
			else
				$tmp['review_average'] = 0;

			$data['agency_ad'] = $tmp;
		}

		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;

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



		} else {

		}

		// Ad Generator scripts
		$data['eddy_found'] = false;
		$adpage = new AdPage;
		$eddyFound = $adpage->findEddyAd($slug);
		$users_ip_info = $this->iplookup($data['ip']);

		//only want to show eddy ads to US users on specific college pages
		if( $eddyFound && $users_ip_info['countryAbbr'] == 'US' ){
			$data['eddy_found'] = true;
		}

        $data['is_online_school'] = false;

        // All Keypath schools will be set as a Online School
        // Keypath has a ro_id of 10.
        if (isset($is_redirect->id)) {
            $is_online = College::on('rds1')
                                 ->where('id', '=', $is_redirect->id)
                                 ->where('is_online', '=', 1)
                                 ->first();

            if (isset($is_online)) {
                $data['is_online_school'] = true;
            }
        }

		if (isset($users_ip_info['countryName']) && !empty($users_ip_info['countryName'])) {
			$aa = new AdAffiliate;
			$aa = $aa->getAdCopies($users_ip_info['countryName']);

			if (isset($aa) && !empty($aa)) {
				$data['affiliate_ad'] = $aa;
			}
		}

		$pbc = new PlexussBannerCopy;
		$data['plexuss_banners'] = $pbc->getAdCopies();

		// Determine if this school has international tab
		$cit = CollegesInternationalTab::on('rds1')
									   ->where('college_id', $is_redirect->id)
									   ->first();

		$input = Request::all();
		$data['show_international_tab'] = false;

		//if has international tab and type is empty -> show default for international
		if (isset($cit) && $users_ip_info['countryAbbr'] != 'US' &&
		   (!isset($type) || empty($type)) && !isset($input['showUS'])) {
			$data['show_international_tab'] = true;

			$prog = $cit->define_program;

				// dd($prog);


				if ($prog == 'epp'){   //just an english pathway
					return redirect()->intended('/college/'.$slug."/epp");
				}
				else if ($prog == "grad" || $prog == "grad_epp") {   //just grad
					return redirect()->intended('/college/'.$slug."/grad");
				}
				elseif ($prog == 'undergrad') {
					return redirect()->intended('/college/'.$slug."/undergrad");
				}
				elseif ($prog == 'both') {
					return redirect()->intended('/college/'.$slug."/undergrad");
				}
				//if all, or undergrad_epp
				else{
					return redirect()->intended('/college/'.$slug."/epp");
				}



		}
		if (isset($input['showUS'])) {
			$data['show_international_tab'] = true;
		}

		// Ad Generator scripts ends.

		//select * from `colleges` where `slug` = 'stanford-university' and `verified` = '1' limit
		//For memcached lets set this to key = college_slug

		$college_data = College::on('rds1')->where('slug', '=', $slug)
								   ->join('countries as c', 'c.id', '=', 'colleges.country_id')
								   ->leftJoin('aor_colleges as ac', 'ac.college_id', '=', 'colleges.id')
								   ->select('colleges.*', 'c.country_code', 'c.country_name', 'ac.aor_id')
								   ->where( 'verified', '=', 1 )
								   ->first();

		if (!isset($college_data)) {
			return App::make("ErrorController")->callAction("error", ['code'=> 404, 'exception'=> '']);
		}
		$college_data = $college_data->toArray();

		Cache::add(env('ENVIRONMENT') .'_'.'college_'.$slug, $college_data, 60);

		// if (Cache::has(env('ENVIRONMENT') .'_'.'college_'.$slug)){
		// 	$college_data = Cache::get(env('ENVIRONMENT') .'_'.'college_'.$slug);
		// } else {
		// 	$college_data = College::where('slug', '=', $slug)
		// 						   ->join('countries as c', 'c.id', '=', 'colleges.country_id')
		// 						   ->leftJoin('aor_colleges as ac', 'ac.college_id', '=', 'colleges.id')
		// 						   ->select('colleges.*', 'c.country_code', 'c.country_name', 'ac.aor_id')
		// 						   ->where( 'verified', '=', 1 )
		// 						   ->first();

		// 	if (!isset($college_data)) {
		// 		return App::make("ErrorController")->callAction("error", ['code'=> 404, 'exception'=> '']);
		// 	}
		// 	$college_data = $college_data->toArray();

		// 	Cache::add(env('ENVIRONMENT') .'_'.'college_'.$slug, $college_data, 60);
		// }

		$data['CollegeId'] = $college_data['id'];
		$data['noindex']   = $college_data['noindex'];

		/* survival guide */
        $newCollegeMapping = NewsCollegeMapping::where('college_id',$college_data['id'])->first();
        if(isset($newCollegeMapping)){
            $newArticle = NewsArticle::where('id',$newCollegeMapping['news_id'])->first();
            if(isset($newArticle)){
                if($newArticle['news_subcategory_id'] == 13){
                    $data['survivalGuide'] = $newArticle;
                }
            }
        }

        /* for news articles */
        $data['college_name'] = $college_data['school_name'];
        $data['collegeMappings'] = NewsCollegeMapping::where('college_id',$college_data['id'])->get();

        // Get all the images for this college
		// select * from `college_overview_images` where `college_id` = '4480'
		if ( Cache::has(env('ENVIRONMENT') .'_'.'collegeOverviewImages_'. $data['CollegeId'])){
			$data['collegeImages'] = Cache::get(env('ENVIRONMENT') .'_'.'collegeOverviewImages_'. $data['CollegeId']);
		} else {
			$data['collegeImages'] = CollegeOverviewImages::where('college_id', '=', $data['CollegeId'])->get()->toArray();
			Cache::add(env('ENVIRONMENT') .'_'.'collegeOverviewImages_'. $data['CollegeId'], $data['collegeImages'], 60);
		}

		// format phone number
		$ph = $college_data['general_phone'];
		$ph_len = strlen( $ph );

		// Declare pieces
		$one = '';
		$area_code = '';
		$three = '';
		$four = '';

		// Format here can only handle US phone numbers
		if( $ph_len >= 7 ){
			$four_offset = $ph_len - 4;
			$three_offset = $ph_len - 7;
			$four = substr( $ph, $four_offset, 4 );
			$three = substr( $ph, $three_offset, 3 ) . '-';
		}
		if( $ph_len >= 10 ){
			$area_offset = $ph_len - 10;
			$area_code = '(' . substr( $ph, $area_offset, 3 ) . ')-';
		}
		if( $ph_len == 11 ){
			$one = '1';
		}
		$ph = $one . $area_code . $three . $four;

		// Only format phone if the school is US based.
		if (!isset($college_data['country_id']) || $college_data['country_id'] == 1) {
			$college_data['general_phone'] = $ph;
		}

		if(isset($college_data)) {
			$data['college_data'] = $college_data;
		}

		$data['profile_img_loc'] = $src;
		$data['pagetype'] = $pagetype;

		$data['isInUserList'] = 0;
		// check the user if school is in his list
		if (Auth::check()){

			//get id by authcheck
			$id = Auth::id();
			$user = User::find($id);
			$data['profile_perc'] = $user->profile_percent;

			$recruitment = $this->hasAlreadyAskedToBeRecruited($user->id, $college_data['id']);
			// $recruitment = Recruitment::where('user_id', '=', $user->id)
			// 	->where('college_id', '=', $college_data['id'])
			// 	->where('user_recruit', 1)
			// 	->where('status', '=', '1')
			// 	->first();
			// dd($recruitment->id);

			if(isset($recruitment->id)){
				$data['isInUserList'] = 1;
			}
		}

		//We need a way to tell the view what sub view in the Ajax folder to include on page load.
		//I think a switch would be the easiest option.
		//Also we can set SPECIAL META DATA HERE!!!!!
		$showView = '';

		// Does it have paid url account??
 		$ro_id = DB::connection('rds1')->table('revenue_organizations as ro')
										 ->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
										 ->where('ro.type', 'click')
										 ->where('ro.active', 1)
										 ->where('ac.college_id', $college_data['id'])
										 ->select('ro.id as ro_id')
										 ->first();
		if (isset($ro_id->ro_id)) {
			$input = array();
			$input['college_id'] = $college_data['id'];
			$input['ro_id']		 = $ro_id->ro_id;
			$user_id = (isset($data['user_id']) && !empty($user_id)) ? $user_id = $data['user_id'] : null;
 			$uc = new UtilityController();
			$url = $uc->getRevenueOrganizationLinksForEmails($input, 'college_page_apply_now', $user_id);

			$college_data['paid_app_url'] = $url['url'];
			$data['college_data'] = $college_data;
		}

		switch ($type) {
		    case '':

		        $showView = 'overview';

		        $this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        //We NEED to unset these variables so that the merge can replace them with new info.
		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);
		        unset($arr['school_url']);
		        unset($arr['admission_url']);
		        unset($arr['financial_aid_url']);
		        unset($arr['application_url']);
		        unset($arr['calculator_url']);

		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        // select `college_overview`.`content`, `college_overview`.`page_title`, `college_overview`.`meta_description`, `college_overview`.`meta_keywords`, `colleges`.*, `colleges_ranking`.`plexuss` as `plexuss_ranking` from `college_overview` inner join `colleges` on `colleges`.`id` = `college_overview`.`college_id` inner join `colleges_ranking` on `colleges_ranking`.`college_id` = `college_overview`.`college_id` where `college_overview`.`college_id` = '4480'
		        if (Cache::has(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id'] )) {
		        	$data['college_data'] = Cache::get(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id']);
		        } else {
		        	$data['college_data'] = (object) array_merge((array)$this->getOverviewInfo(), $arr);
		        	Cache::add(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id'], $data['college_data'], 60);
		        }

		        $this->cid_for_college_page = -1;

		        break;
		    case 'overview':
		        $showView = 'overview';

		        $this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        // select `college_overview`.`content`, `college_overview`.`page_title`, `college_overview`.`meta_description`, `college_overview`.`meta_keywords`, `colleges`.*, `colleges_ranking`.`plexuss` as `plexuss_ranking` from `college_overview` inner join `colleges` on `colleges`.`id` = `college_overview`.`college_id` inner join `colleges_ranking` on `colleges_ranking`.`college_id` = `college_overview`.`college_id` where `college_overview`.`college_id` = '4480'
		        if (Cache::has(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id'] )) {
		        	$data['college_data'] = Cache::get(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id']);
		        } else {
		        	$data['college_data'] = (object) array_merge((array)$this->getOverviewInfo(), $arr);
		        	Cache::add(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id'], $data['college_data'], 60);
		        }

		        $this->cid_for_college_page = -1;
		        break;
		    case 'stats':
		        $showView = 'stats';
		        $this->cid_for_college_page = $college_data['id'];
		        $data['college_data'] = $this->getStatsInfo();

		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        //THIS IS NOT STATS - ITS STATS PAGE META DATA!!!!! FIX
		        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $college_data['id'] )) {
		        	$college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $college_data['id']);
		        } else {
		        	$college_stats_model = CollegeStats::where('college_id', '=', $college_data['id'])->first();
		        	Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $college_data['id'], $college_stats_model, 60);
		        }

				// SEO
		        $data['college_data']->page_title = $college_stats_model->page_title;
		        $data['college_data']->meta_keywords = $college_stats_model->meta_keywords;
		        $data['college_data']->meta_description = $college_stats_model->meta_description;

		        $this->cid_for_college_page = -1;
        		break;
        	case 'ranking':
		        $showView = 'ranking';
		        $this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data'] = (object) array_merge((array)$this->getRankingInfo(), $arr);

		        $this->cid_for_college_page = -1;
        		break;
        	case 'financial-aid':
		        $showView = 'financial';
		        $this->cid_for_college_page = $college_data['id'];

				$arr = $data['college_data'];


		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);
		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data'] = (object) array_merge((array)$this->getFinancialInfo(), $arr);
		        $this->cid_for_college_page = -1;
        		break;
        	case 'admissions':
		        $showView = 'admissions';
		        $this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data'] = (object) array_merge((array)$this->getAdmissionsInfo(), $arr);
		        $this->cid_for_college_page = -1;
        		break;
        	case 'tuition':
		        $showView = 'tuition';
		        $this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data'] = (object) array_merge((array)$this->getTuitionInfo(), $arr);
		        $this->cid_for_college_page = -1;
        		break;
        	case 'enrollment':
		        $showView = 'enrollment';
		        $this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data'] = (object) array_merge((array)$this->getEnrollmentInfo(), $arr);
		        $this->cid_for_college_page = -1;
        		break;
        	case 'chat':
        		$showView = 'chat';

		        //$this->cid_for_college_page = $college_data['id'];

		       	$chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		       	$this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $cmtm = new CollegeMessageThreadMembers();
		        $thread_id = $cmtm->getThreadidWithThisUserIdAndThisCollegeId($data['user_id'], $data['CollegeId']);
		        isset($thread_id) ? $arr['thread_id'] = $thread_id : null;

		        if (Cache::has(env('ENVIRONMENT') .'_'.'college_chat_' . $college_data['id'] )) {
		        	$data['college_data'] = Cache::get(env('ENVIRONMENT') .'_'.'college_chat_' . $college_data['id']);
		        } else {
		        	$data['college_data'] = (object) array_merge((array)$this->getOverviewInfo(), $arr);
		        	Cache::add(env('ENVIRONMENT') .'_'.'college_chat_' . $college_data['id'], $data['college_data'], 60);
		        }
		        $this->cid_for_college_page = -1;

        		break;
			case 'news':
				$showView = 'news';

				$this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();
		       	$data['chat'] = array();
		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data']  = (object) array_merge( array('news' => (array)$this->getNewsInfo() ) , $arr);
		        //$data['college_data']['page_title'] = $arr['school_name'].' | News | Plexuss.com';

		        $data['news'] = $this->getNewsFromBing($arr['school_name']);
		        $tmp_news = $data['college_data']->news;
		        $data['college_data']->page_title = $tmp_news['page_title'];
		        $data['college_data']->share_image_path = $tmp_news['share_image_path'];
		        $data['college_data']->share_image = $tmp_news['share_image'];

		        $this->cid_for_college_page = -1;

				break;
			case 'undergrad':
				$showView = 'undergrad';

				$this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();
		       	$data['chat'] = array();
		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data']  = (object) array_merge( array('news' => (array)$this->getUnderGradInfo() ) , $arr);

		        $data['college_data']->page_title = $data['college_data']->school_name .' International Students | Undergrad Applicants | Plexuss';
		        // Start of getting undergrad data.
				$cit = new CollegesInternationalTab;
				$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($college_data['id']);
				// End if getting undergrad data.


		        $this->cid_for_college_page = -1;

				break;
			case 'grad':
				$showView = 'grad';

				$this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();
		       	$data['chat'] = array();
		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data']  = (object) array_merge( array('news' => (array)$this->getGradInfo() ) , $arr);

		        $data['college_data']->page_title = $data['college_data']->school_name .' International Students | Graduate Applicants | Plexuss';

		        // Start of getting undergrad data.
				$cit = new CollegesInternationalTab;
				$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($college_data['id']);
				// End if getting undergrad data.

				$collegeModel= new College();

				$cd = $collegeModel->CollegesTuition($college_data['id']);

				if (isset($cd->cct_id)) {
					$cct = CollegeCustomTuition::on('rds1')->where('college_id', $college_data['id'])
													->select('amount as cct_amount', 'currency as cct_currency', 'title as cct_title', 'sub_title as cct_sub_title')
													->get();
					$data['college_data_custom_tuition'] = $cct;
				}


		        $this->cid_for_college_page = -1;

				break;
			case 'epp':
				$showView = 'englishProg';

				// if aor_id is set and not empty, this college is an aor school, so we need to show different view on /epp path
				if( isset($college_data['aor_id']) && $college_data['aor_id'] == 5 ){
					$showView = 'els';
				}

				$this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);

		        $chat = new ChatMessageController();
		       	$data['chat'] = array();
		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        $data['college_data']  = (object) array_merge( array('news' => (array)$this->getEppInfo() ) , $arr);

		        $data['college_data']->page_title = $data['college_data']->school_name .' International Students | English Programs Applicants | Plexuss';

		        // Start of getting undergrad data.
				$cit = new CollegesInternationalTab;
				$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($college_data['id']);
				// End if getting undergrad data.


		        $this->cid_for_college_page = -1;

				break;
            case 'current-students':
                $showView = 'currentStudent';

                $this->cid_for_college_page = $college_data['id'];

                $arr = $data['college_data'];

                unset($arr['page_title']);
                unset($arr['meta_keywords']);
                unset($arr['meta_description']);

                $chat = new ChatMessageController();
                $data['chat'] = array();
                $data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

                $collegeModel =  new College();
                $currentStudent = $collegeModel->CurrentStudent($college_data['id']);
                $data['currentStudents'] = $currentStudent;
                $data['college_id'] = $college_data['id'];
                // count
                // $data['countCurrentStudent'] = $collegeModel->countCurrentStudent($college_data['id']);
                $data['college_data']  = (object) array_merge((array)$this->getCurrentStudent() , $arr);

                $this->cid_for_college_page = -1;
                break;
            case 'alumni':
                $showView = 'alumni';

                $this->cid_for_college_page = $college_data['id'];

                $arr = $data['college_data'];

                unset($arr['page_title']);
                unset($arr['meta_keywords']);
                unset($arr['meta_description']);

                $chat = new ChatMessageController();
                $data['chat'] = array();
                $data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');
                $collegeModel =  new College();
                $alumni = $collegeModel->AlumniStudent($college_data['id']);
                $data['alumniStudents'] = $alumni;
                $data['college_id'] = $college_data['id'];
                // count
                // $data['countAlumni'] = $collegeModel->countAlumni($college_data['id']);
                $data['college_data']  = (object) array_merge((array)$this->getAlumni() , $arr);

                $this->cid_for_college_page = -1;
                break;
	}

		$data['pageViewType'] = $showView;

		// Show skype call/chat for plexuss
		$now = Carbon::now();

		$current_time = $now->format('H:i a');
		$start = "09:00 am";
		$end   = "17:00 pm";
		$date1 = Carbon::createFromFormat('H:i a', $current_time);
		$date2 = Carbon::createFromFormat('H:i a', $start);
		$date3 = Carbon::createFromFormat('H:i a', $end);

		$tempDate = date("Y-m-d");

		$day_of_week = date('l', strtotime( $tempDate));

		if ($date1 > $date2 && $date1 < $date3 && $day_of_week !="Saturday" && $day_of_week !="Sunday"){
			$data['plexuss_skype_call_chat'] = true;
		}

		$data['college_data']->school_url = $this->urlCheckProtical($data['college_data']->school_url);
		$data['college_data']->admission_url = $this->urlCheckProtical($data['college_data']->admission_url);
		$data['college_data']->financial_aid_url = $this->urlCheckProtical($data['college_data']->financial_aid_url);
		$data['college_data']->application_url = $this->urlCheckProtical($data['college_data']->application_url);
		$data['college_data']->calculator_url = $this->urlCheckProtical($data['college_data']->calculator_url);
		$data['college_data']->mission_url = $this->urlCheckProtical($data['college_data']->mission_url);
		if (isset($data['college_data']->country_code)) {
			$data['college_data']->country_code = strtolower($data['college_data']->country_code);
		}

		$yt_overview_videos = [];
		$overview_virtual_tours = [];

		if ( isset($data['collegeImages']) && count($data['collegeImages']) > 0 ){

			foreach ($data['collegeImages'] as $key => $value) {

				if ($value['is_video'] == 1 && isset($value['section']) && $value['section'] == 'overview') {
					array_push($yt_overview_videos, $value);
				}elseif( $value['is_video'] == 0 && $value['is_tour'] == 1 ){
	 				array_push($overview_virtual_tours, $value);
	 			}

			}

		}

		//check if youtube array is set and not empty
		if( isset($yt_overview_videos) && count($yt_overview_videos) > 0 ){
			$data['college_data']->yt_overview_vids = $yt_overview_videos[0]['video_id'];
		}else{
			$data['college_data']->yt_overview_vids = '';
		}
		//check if virtual tour array is set and not empty
		if( isset($overview_virtual_tours) && count($overview_virtual_tours) > 0 ){
			$data['college_data']->virtualTour_overview = $overview_virtual_tours[0]['tour_id'];
		}else{
			$data['college_data']->virtualTour_overview = '';
		}


		$data['right_handside_carousel'] = $this->getRightHandSide($data);


		//for now so that I may test
		// later direct based on ip not type
		// NOTE: must start on undergrad view though...
		// and let users toggle
		//create toggel function that gets called on click of toggle link
		if($type == 'undergrad' || $type == 'grad' || $type == 'epp') {
			return View('private.college.internationalSingleView', $data);
		}
		else if(Request::segment(1) == 'social-college') {
			return response()->json($data);
		} else
			$data['onlycollgepage'] = true;
			return View('private.college.collegeSingleView', $data);

		$rand = rand(0,1);

		$data['rand_num'] = $rand;
        $data['onlycollgepage'] = true;
		return View('private.college.collegeSingleView', $data);
	}


   	/* public function comparebox(){

		$college_id = Request::get('college_id');
		$typefor = Request::get('typefor');

		if ( !Auth::check() ) {
			return View( 'public.homepage.homepage' );
		}

		$user = User::find( Auth::user()->id );

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss Colleges';
		$data['currentPage'] = 'college';
		$token = AjaxToken::where('user_id', '=', $user->id)->first();

		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;

		if(!$token) {
			Auth::logout();
			return redirect('/');
		}

		if($college_id!='')
		{

			if($typefor=='drag')
			{

				$college = College::where('id', '=',$college_id)->get()->toArray();
				$datavalue=$college[0];

			}

			if($typefor=='drop') {


				$college = new College;
				$collegeData=$college->CollegeData($college_id);
				$datavalue=(array)$collegeData[0];

				$total_expense=($datavalue['tuition_avg_in_state_ftug'])+($datavalue['books_supplies_1213'])+
							   ($datavalue['room_board_on_campus_1213'])+($datavalue['other_expenses_on_campus_1213']);
				$acceptance_rate='';

				if($datavalue['applicants_total']>0)	{
					$acceptance_rate=($datavalue['admissions_total'])/($datavalue['applicants_total']);
					$acceptance_rate=number_format($acceptance_rate,2).'%';
				}

				$return_array=array();
				$return_array['acceptance_rate']=$acceptance_rate;
				$return_array['tuition_fees']='$'.$datavalue['tuition_fees_1213'];
				$return_array['student_body']=$datavalue['student_body_total'];
				$return_array['total_expense']='$'.number_format($total_expense,2);
				$return_array['application_deadline']='N/A';
				$return_array['application_fee']='$'.$datavalue['application_fee_undergrad'];
				$return_array['sector_of_institution']=$datavalue['school_sector'];
				$return_array['calendar_system']=$datavalue['calendar_system'];
				$return_array['religous_affiliation']=$datavalue['religious_affiliation'];
				$return_array['locale_type']='N/A';
				$return_array['endowment']='$'.$datavalue['public_endowment_end_fy_12'].'<span class="fs10">BILLION</span>';
				$return_array['overall_net_price']=$datavalue['calendar_system'];
				$return_array['average_sat_math_range']=$datavalue['sat_math_25'].'-'.$datavalue['sat_math_75'];
				$return_array['average_sat_writing']=$datavalue['sat_write_25'].'-'.$datavalue['sat_write_75'];
				$return_array['average_act_score']=$datavalue['act_composite_25'].'-'.$datavalue['act_composite_75'];
				$return_array['m-average_act_score']=$datavalue['act_composite_25'].'&nbsp;<img src="images/colleges/compare/blue_loader.png"/>';

				return Response::json( $return_array );
			}
		}

		$college = new College;

		$data['ajaxtoken'] = $token->token;
		//return 'Hello Home is working.';
		return View('private.college.comparebox', $data);
	}*/


	public function getSocialCollege($slug='', $type='') {
		$college_data = College::on('rds1')->where('slug', '=', $slug)
						   ->join('countries as c', 'c.id', '=', 'colleges.country_id')
						   ->leftJoin('aor_colleges as ac', 'ac.college_id', '=', 'colleges.id')
						   ->select('colleges.*', 'c.country_code', 'c.country_name', 'ac.aor_id')
						   ->where( 'verified', '=', 1 )
						   ->first();

		$data['college_data'] = $college_data;

		switch ($type) {
		    case '':

		        $showView = 'overview';

		        $this->cid_for_college_page = $college_data['id'];

		        $arr = $data['college_data'];

		        //We NEED to unset these variables so that the merge can replace them with new info.
		        unset($arr['page_title']);
		        unset($arr['meta_keywords']);
		        unset($arr['meta_description']);
		        unset($arr['school_url']);
		        unset($arr['admission_url']);
		        unset($arr['financial_aid_url']);
		        unset($arr['application_url']);
		        unset($arr['calculator_url']);

		        $chat = new ChatMessageController();

		       	$data['chat'] = array();

		       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		        // select `college_overview`.`content`, `college_overview`.`page_title`, `college_overview`.`meta_description`, `college_overview`.`meta_keywords`, `colleges`.*, `colleges_ranking`.`plexuss` as `plexuss_ranking` from `college_overview` inner join `colleges` on `colleges`.`id` = `college_overview`.`college_id` inner join `colleges_ranking` on `colleges_ranking`.`college_id` = `college_overview`.`college_id` where `college_overview`.`college_id` = '4480'
		        if (Cache::has(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id'] )) {
		        	$data['college_data'] = Cache::get(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id']);
		        } else {
		        	$data['college_data'] = (object) array_merge((array)$this->getOverviewInfo(), $arr);
		        	Cache::add(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id'], $data['college_data'], 60);
		        }

		        $this->cid_for_college_page = -1;

		        break;
	    case 'overview':
	        $showView = 'overview';

	        $this->cid_for_college_page = $college_data['id'];

	        $arr = $data['college_data'];

	        unset($arr['page_title']);
	        unset($arr['meta_keywords']);
	        unset($arr['meta_description']);

	        $chat = new ChatMessageController();

	       	$data['chat'] = array();

	       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

	        // select `college_overview`.`content`, `college_overview`.`page_title`, `college_overview`.`meta_description`, `college_overview`.`meta_keywords`, `colleges`.*, `colleges_ranking`.`plexuss` as `plexuss_ranking` from `college_overview` inner join `colleges` on `colleges`.`id` = `college_overview`.`college_id` inner join `colleges_ranking` on `colleges_ranking`.`college_id` = `college_overview`.`college_id` where `college_overview`.`college_id` = '4480'
	        if (Cache::has(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id'] )) {
	        	$data['college_data'] = Cache::get(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id']);
	        } else {
	        	$data['college_data'] = (object) array_merge((array)$this->getOverviewInfo(), $arr);
	        	Cache::add(env('ENVIRONMENT') .'_'.'college_overview_' . $college_data['id'], $data['college_data'], 60);
	        }

	        $this->cid_for_college_page = -1;
	        break;
	    case 'stats':
	        $showView = 'stats';
	        $this->cid_for_college_page = $college_data['id'];
	        $data['college_data'] = $this->getStatsInfo();

	        $chat = new ChatMessageController();

	       	$data['chat'] = array();

	       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

	        //THIS IS NOT STATS - ITS STATS PAGE META DATA!!!!! FIX
	        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $college_data['id'] )) {
	        	$college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $college_data['id']);
	        } else {
	        	$college_stats_model = CollegeStats::where('college_id', '=', $college_data['id'])->first();
	        	Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $college_data['id'], $college_stats_model, 60);
	        }

			// SEO
	        $data['college_data']->page_title = $college_stats_model->page_title;
	        $data['college_data']->meta_keywords = $college_stats_model->meta_keywords;
	        $data['college_data']->meta_description = $college_stats_model->meta_description;

	        $this->cid_for_college_page = -1;
	    		break;
	    	case 'ranking':
	        $showView = 'ranking';
	        $this->cid_for_college_page = $college_data['id'];

	        $arr = $data['college_data'];

	        unset($arr['page_title']);
	        unset($arr['meta_keywords']);
	        unset($arr['meta_description']);

	        $chat = new ChatMessageController();

	       	$data['chat'] = array();

	       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

	        $data['college_data'] = (object) array_merge((array)$this->getRankingInfo(), $arr);

	        $this->cid_for_college_page = -1;
	    		break;
	    	case 'financial-aid':
	        $showView = 'financial';
	        $this->cid_for_college_page = $college_data['id'];

				$arr = $data['college_data'];


	        unset($arr['page_title']);
	        unset($arr['meta_keywords']);
	        unset($arr['meta_description']);
	        $chat = new ChatMessageController();

	       	$data['chat'] = array();

	       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

	        $data['college_data'] = (object) array_merge((array)$this->getFinancialInfo(), $arr);
	        $this->cid_for_college_page = -1;
	    		break;
	    	case 'admissions':
	        $showView = 'admissions';
	        $this->cid_for_college_page = $college_data['id'];

	        $arr = $data['college_data'];

	        unset($arr['page_title']);
	        unset($arr['meta_keywords']);
	        unset($arr['meta_description']);

	        $chat = new ChatMessageController();

	       	$data['chat'] = array();

	       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

	        $data['college_data'] = (object) array_merge((array)$this->getAdmissionsInfo(), $arr);
	        $this->cid_for_college_page = -1;
	    		break;
	    	case 'tuition':
	        $showView = 'tuition';
	        $this->cid_for_college_page = $college_data['id'];

	        $arr = $data['college_data'];

	        unset($arr['page_title']);
	        unset($arr['meta_keywords']);
	        unset($arr['meta_description']);

	        $chat = new ChatMessageController();

	       	$data['chat'] = array();

	       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

	        $data['college_data'] = (object) array_merge((array)$this->getTuitionInfo(), $arr);
	        $this->cid_for_college_page = -1;
	    		break;
	    	case 'enrollment':
	        $showView = 'enrollment';
	        $this->cid_for_college_page = $college_data['id'];

	        $arr = $data['college_data'];

	        unset($arr['page_title']);
	        unset($arr['meta_keywords']);
	        unset($arr['meta_description']);

	        $chat = new ChatMessageController();

	       	$data['chat'] = array();

	       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

	        $data['college_data'] = (object) array_merge((array)$this->getEnrollmentInfo(), $arr);
	        $this->cid_for_college_page = -1;
	    		break;
	    	case 'chat':
	    		$showView = 'chat';

	        //$this->cid_for_college_page = $college_data['id'];

	       	$chat = new ChatMessageController();

	       	$data['chat'] = array();

	       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

	       	$this->cid_for_college_page = $college_data['id'];

	        $arr = $data['college_data'];

	        unset($arr['page_title']);
	        unset($arr['meta_keywords']);
	        unset($arr['meta_description']);

	        $cmtm = new CollegeMessageThreadMembers();
	        $thread_id = $cmtm->getThreadidWithThisUserIdAndThisCollegeId($data['user_id'], $data['CollegeId']);
	        isset($thread_id) ? $arr['thread_id'] = $thread_id : null;

	        if (Cache::has(env('ENVIRONMENT') .'_'.'college_chat_' . $college_data['id'] )) {
	        	$data['college_data'] = Cache::get(env('ENVIRONMENT') .'_'.'college_chat_' . $college_data['id']);
	        } else {
	        	$data['college_data'] = (object) array_merge((array)$this->getOverviewInfo(), $arr);
	        	Cache::add(env('ENVIRONMENT') .'_'.'college_chat_' . $college_data['id'], $data['college_data'], 60);
	        }
	        $this->cid_for_college_page = -1;

	    		break;
				case 'news':
					$showView = 'news';

					$this->cid_for_college_page = $college_data['id'];

			        $arr = $data['college_data'];

			        unset($arr['page_title']);
			        unset($arr['meta_keywords']);
			        unset($arr['meta_description']);

			        $chat = new ChatMessageController();
			       	$data['chat'] = array();
			       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

			        $data['college_data']  = (object) array_merge( array('news' => (array)$this->getNewsInfo() ) , $arr);
			        //$data['college_data']['page_title'] = $arr['school_name'].' | News | Plexuss.com';

			        $data['news'] = $this->getNewsFromBing($arr['school_name']);
			        $tmp_news = $data['college_data']->news;
			        $data['college_data']->page_title = $tmp_news['page_title'];
			        $data['college_data']->share_image_path = $tmp_news['share_image_path'];
			        $data['college_data']->share_image = $tmp_news['share_image'];

			        $this->cid_for_college_page = -1;

					break;
				case 'undergrad':
					$showView = 'undergrad';

					$this->cid_for_college_page = $college_data['id'];

			        $arr = $data['college_data'];

			        unset($arr['page_title']);
			        unset($arr['meta_keywords']);
			        unset($arr['meta_description']);

			        $chat = new ChatMessageController();
			       	$data['chat'] = array();
			       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

			        $data['college_data']  = (object) array_merge( array('news' => (array)$this->getUnderGradInfo() ) , $arr);

			        $data['college_data']->page_title = $data['college_data']->school_name .' International Students | Undergrad Applicants | Plexuss';
			        // Start of getting undergrad data.
					$cit = new CollegesInternationalTab;
					$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($college_data['id']);
					// End if getting undergrad data.


			        $this->cid_for_college_page = -1;

					break;
				case 'grad':
					$showView = 'grad';

					$this->cid_for_college_page = $college_data['id'];

			        $arr = $data['college_data'];

			        unset($arr['page_title']);
			        unset($arr['meta_keywords']);
			        unset($arr['meta_description']);

			        $chat = new ChatMessageController();
			       	$data['chat'] = array();
			       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

			        $data['college_data']  = (object) array_merge( array('news' => (array)$this->getGradInfo() ) , $arr);

			        $data['college_data']->page_title = $data['college_data']->school_name .' International Students | Graduate Applicants | Plexuss';

			        // Start of getting undergrad data.
					$cit = new CollegesInternationalTab;
					$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($college_data['id']);
					// End if getting undergrad data.

					$collegeModel= new College();

					$cd = $collegeModel->CollegesTuition($college_data['id']);

					if (isset($cd->cct_id)) {
						$cct = CollegeCustomTuition::on('rds1')->where('college_id', $college_data['id'])
														->select('amount as cct_amount', 'currency as cct_currency', 'title as cct_title', 'sub_title as cct_sub_title')
														->get();
						$data['college_data_custom_tuition'] = $cct;
					}


			        $this->cid_for_college_page = -1;

					break;
				case 'epp':
					$showView = 'englishProg';

					// if aor_id is set and not empty, this college is an aor school, so we need to show different view on /epp path
					if( isset($college_data['aor_id']) && $college_data['aor_id'] == 5 ){
						$showView = 'els';
					}

					$this->cid_for_college_page = $college_data['id'];

			        $arr = $data['college_data'];

			        unset($arr['page_title']);
			        unset($arr['meta_keywords']);
			        unset($arr['meta_description']);

			        $chat = new ChatMessageController();
			       	$data['chat'] = array();
			       	$data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

			        $data['college_data']  = (object) array_merge( array('news' => (array)$this->getEppInfo() ) , $arr);

			        $data['college_data']->page_title = $data['college_data']->school_name .' International Students | English Programs Applicants | Plexuss';

			        // Start of getting undergrad data.
					$cit = new CollegesInternationalTab;
					$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($college_data['id']);
					// End if getting undergrad data.


			        $this->cid_for_college_page = -1;

					break;
		      case 'current-students':
		          $showView = 'currentStudent';

		          $this->cid_for_college_page = $college_data['id'];

		          $arr = $data['college_data'];

		          unset($arr['page_title']);
		          unset($arr['meta_keywords']);
		          unset($arr['meta_description']);

		          $chat = new ChatMessageController();
		          $data['chat'] = array();
		          $data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');

		          $collegeModel =  new College();
		          $currentStudent = $collegeModel->CurrentStudent($college_data['id']);
		          $data['currentStudents'] = $currentStudent;
		          $data['college_id'] = $college_data['id'];
		          // count
		          // $data['countCurrentStudent'] = $collegeModel->countCurrentStudent($college_data['id']);
		          $data['college_data']  = (object) array_merge((array)$this->getCurrentStudent() , $arr);

		          $this->cid_for_college_page = -1;
		          break;
		      case 'alumni':
		          $showView = 'alumni';

		          $this->cid_for_college_page = $college_data['id'];

		          $arr = $data['college_data'];

		          unset($arr['page_title']);
		          unset($arr['meta_keywords']);
		          unset($arr['meta_description']);

		          $chat = new ChatMessageController();
		          $data['chat'] = array();
		          $data['chat'] = $chat->getUsrTopics($college_data['id'], 'users');
		          $collegeModel =  new College();
		          $alumni = $collegeModel->AlumniStudent($college_data['id']);
		          $data['alumniStudents'] = $alumni;
		          $data['college_id'] = $college_data['id'];
		          // count
		          // $data['countAlumni'] = $collegeModel->countAlumni($college_data['id']);
		          $data['college_data']  = (object) array_merge((array)$this->getAlumni() , $arr);

		          $this->cid_for_college_page = -1;
		          break;
		}

		return response()->json($data);
	}

	// This is the NEW method that will be used to call in AJAX stuff  .Needed for SEO Rebuild!
	public function getOverviewInfo($college_id = null, $is_api = null){


		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}elseif (isset($college_id)) {
			$collegeId = $college_id;
		}else{
			$collegeId = Request::get('college_id');
		}

		// select `college_overview`.`content`, `college_overview`.`page_title`, `college_overview`.`meta_description`, `college_overview`.`meta_keywords`, `colleges`.*, `colleges_ranking`.`plexuss` as `plexuss_ranking` from `college_overview` inner join `colleges` on `colleges`.`id` = `college_overview`.`college_id` inner join `colleges_ranking` on `colleges_ranking`.`college_id` = `college_overview`.`college_id` where `college_overview`.`college_id` = '4480'
		if ( Cache::has(env('ENVIRONMENT') .'_'.'collegeAjaxOverview_'. $collegeId)){
			$college_data = Cache::get(env('ENVIRONMENT') .'_'.'collegeAjaxOverview_'. $collegeId);
		} else {
			$collegeModel= new College();
			$college_data = $collegeModel->CollegeOverview($collegeId);
			Cache::add(env('ENVIRONMENT') .'_'.'collegeAjaxOverview_'. $collegeId, $college_data, 60);
		}

		/***************************************************
		 *================== COMMENTS CODE==================
		 ***************************************************/
		/* READ FIRST!!! MAY SAVE YOU SOME TROUBLE!!!
		 * Code from trying to integrate comments into college page.
		 * I was trying to get my comments data into the
		 * getOverviewInfo/getStatsInfo etc methods so that it would
		 * be passed to the view for every ajax college page. I was
		 * having trouble with the eloquent object returned by
		 * CollegeOverview::where()... rather than the DB::table()...
		 * method, so it may be best to stick with DB::table in trying
		 * to get comments on the college pages.
		 *
		 * This block checks to see if there is a comment thread id already
		 * existing in the college section's table, and if the returned value
		 * is null, it creates a thread id. This is where eloquent comes in,
		 * actually. I was intending to just make a new comment thread id
		 * if we didn't find one for the college, effectively creating a thread
		 * on pageload if it didn't exist. That way i didn't have to worry
		 * about creating a thread on the fly.
		 *
		 * The idea was to set $record->comment_thread_id and do $record->save()
		 * to both create and save the new thread id, but you can't do that
		 * (afiak) with the DB method; it'll have to be two queries.
		 *
		 * OR we'll have to refactor the array/object typecast merge in the
		 * view methods to return a usable object to pass to the view. -AW

		$record = $college_data;
		// Find comment thread, if any, and create if necessary
		$thread_checker = new CommentThread;
		$thread_checker->setRecord( $record );
		$has_thread = $thread_checker->checkHasThreadId();
		if( !$has_thread ){
			$thread_checker->createNewThread();
			$thread_id = $thread_checker->getNewThreadId();
		}
		// will always pass here, if stmt not needed
		if( !is_null(  $record->comment_thread_id  ) ){
			$data['comments']['thread_id'] = $record->comment_thread_id;
			$comments = new CommentController;
			$comments_json = $comments->getLatest(
				array(
					'thread_id' => $data['comments']['thread_id'],
					'latest_comment_id' => 0 // set to 0 to get newest
				)
			);
			$comments_array = json_decode( $comments_json );
			if( isset( $comments_array->latest_comment_id ) ){
				$data['comments']['latest_comment_id'] = $comments_array->latest_comment_id;
			}
			if( isset( $comments_array->earliest_comment_id ) ){
				$data['comments']['earliest_comment_id'] = $comments_array->earliest_comment_id;
			}
			$data['comments']['comments'] = $comments_json;
		}
		// update user's anonymous status for 'post_anon' checkbox
		if( isset( $user->comment_anon ) ){
			$data['comments']['anon'] = $user->comment_anon ? 1 : 0;
		}
		 */
		/**************************************************/

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if(isset($college_data))
		{
			$data['college_data'] = $college_data;
		}else{
			$data['college_data'] = null;
		}

		//Check to see if the urls have http in them and add if the dont.
		//We have 4 so far on overview.
		$data['college_data']->school_url = $this->urlCheckProtical($data['college_data']->school_url);
		$data['college_data']->admission_url = $this->urlCheckProtical($data['college_data']->admission_url);
		$data['college_data']->financial_aid_url = $this->urlCheckProtical($data['college_data']->financial_aid_url);
		$data['college_data']->application_url = $this->urlCheckProtical($data['college_data']->application_url);
		$data['college_data']->calculator_url = $this->urlCheckProtical($data['college_data']->calculator_url);

		//Get all the images for this college
		//Dont know why they put the object into single array  - AO
		// 	select * from `college_overview_images` where `college_id` = '4480'
		if ( Cache::has(env('ENVIRONMENT') .'_'.'collegeAjaxOverviewImages_'. $college_data->id)){
			$overviewQueryReturn = Cache::get(env('ENVIRONMENT') .'_'.'collegeAjaxOverviewImages_'. $college_data->id);
		} else {
			$overviewQueryReturn = CollegeOverviewImages::where('college_id', '=', $college_data->id )->get()->toArray();
			Cache::add(env('ENVIRONMENT') .'_'.'collegeAjaxOverviewImages_'. $college_data->id, $overviewQueryReturn, 60);
		}


		//grabbing all media content from College Overview Images table in DB and sorting images and vids and tours into their own array
		$youtube_vids = [];
		$college_img = [];
		$virtual_tours = [];

		if( isset($overviewQueryReturn) && !empty($overviewQueryReturn) ){

			foreach ($overviewQueryReturn as $key => $value) {
				if ($value['is_video'] == 1 && $value['section'] == 'overview') {
					array_push($youtube_vids, $value);
				}elseif( $value['is_video'] == 0 && $value['is_tour'] == 1 ){
	 				array_push($virtual_tours, $value);
	 			}elseif( $value['is_video'] == 0 && $value['is_tour'] == 0 ){
					array_push($college_img, $value);
				}
			}
		}


		// set share image
		$share_image_arr = $this->getShareImage( $college_data->overview_image, $college_data->logo_url );

		// Overview Images
		$data['college_data']->virtual_tours = $virtual_tours;
		$data['college_data']->youtube_videos = $youtube_vids;
		$data['college_data']->college_media = $college_img;

		// Share buttons
		// We don't need to create the parameters on the college pages
		$share_buttons = array();
		$share_buttons['extra_classes'] = array( 'share_college' );
		$share_buttons['stl_text'] = "SHARE: ";
		$data['college_data']->share_buttons = $share_buttons;
		/*
		$data['college_data']->share_buttons['extra_classes'] = array( 'share_college' );
		$data['college_data']->share_buttons['stl_text'] = "SHARE: ";
		 */

		// Set collegedata share images
		$data['college_data']->share_image = $share_image_arr['share_image'];
		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];

		//**********LIKES TALLY CODE STARTS**********/
		$data['college_data']->college_id = $data['college_data']->id;
		$data = $this->getCollegeLikesTally($data);
		//**********LIKES TALLY CODE ENDS ***********/

        $newCollegeMapping = NewsCollegeMapping::where('college_id', $data['college_data']->id)->first();
        if(isset($newCollegeMapping)){
            $newArticle = NewsArticle::where('id',$newCollegeMapping['news_id'])->first();
            if(isset($newArticle)){
                if($newArticle['news_subcategory_id'] == 13){
                    $data['survivalGuide'] = $newArticle;
                }
            }
        }

		if($this->cid_for_college_page == -1 && !isset($is_api)){

			return View('private.college.ajax.overview', $data);
		}else{
			if (isset($data['college_data']->overview_content)) {
				$data['college_data']->overview_content = str_replace("//upload.wikimedia.org", "https://upload.wikimedia.org", $data['college_data']->overview_content);
			}
			return $data['college_data'];
		}

	}
	/* Get Stats Information */
	public function getStatsInfo($college_id = null, $is_api = null){

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}elseif (isset($college_id)) {
			$collegeId = $college_id;
		}else{
			$collegeId = Request::get('college_id');
		}

		if ( Cache::has(env('ENVIRONMENT') .'_'.'collegeAjaxStats_' . $collegeId ) ) {
			$college_data = Cache::get(env('ENVIRONMENT') .'_'.'collegeAjaxStats_' . $collegeId );
		} else {
			$collegeModel= new College();
			$college_data = $collegeModel->CollegeData($collegeId);
			Cache::add(env('ENVIRONMENT') .'_'.'collegeAjaxStats_' . $collegeId, $college_data, 60);
		}

		if($college_data->applicants_total){
			$college_data->acceptance_rate = round(($college_data->admissions_total / $college_data->applicants_total) * 100);
		}
		$college_data->student_body_total = $college_data->student_body_total;
		$college_data->undergrad_enroll_1112 = $college_data->undergrad_enroll_1112;

		if(!$college_data->tuition_fees_1213){
			//select `tuition_avg_in_state_ftug` from `colleges_tuition` where `id` = '1785' limit 1
			if ( 'collegeAjaxStatsTuition_' . $collegeId ) {
				$tuitionAlt = Cache::get(env('ENVIRONMENT') .'_'. 'collegeAjaxStatsTuition_' . $collegeId );
			} else {
				$tuitionAlt = DB::table('colleges_tuition')->where('id', $collegeId)->pluck('tuition_avg_in_state_ftug');

				$tuitionAlt = isset($tuitionAlt[0]) ? $tuitionAlt[0] : null;
				Cache::add(env('ENVIRONMENT') .'_'. 'collegeAjaxStatsTuition_' . $collegeId, $tuitionAlt, 60);
			}

			$college_data->tuition_fees_1213 = $tuitionAlt;
		}

		//select `deadline` from `colleges_admissions` where `id` = '1785' limit 1
		if (Cache::has(env('ENVIRONMENT') .'_'.'collegeAjaxStatsDeadline_' . $collegeId )) {
			$deadline = Cache::get(env('ENVIRONMENT') .'_'. 'collegeAjaxStatsDeadline_' . $collegeId );
		} else {
			$deadline = DB::table('colleges_admissions')->where('id', '=', $collegeId)->pluck('deadline');
			$deadline = isset($deadline[0]) ? $deadline[0] : null;

			Cache::add(env('ENVIRONMENT') .'_'. 'collegeAjaxStatsDeadline_' . $collegeId , $deadline, 60);
		}

		$deadline = (is_null($deadline) ? 'N/A' : $deadline);
		$college_data->deadline = $deadline;
		/*
		$explode=explode(',',$college_data->school_sector);
		echo "<pre>";
		var_dump($explode);
		echo "</pre>";
		exit;

		if($explode[0]=="Public"){
			$totalEndowment = $college_data->public_endowment_end_fy_12;
			$college_data->totalEndowment = $totalEndowment;
		}
		else{
			$totalEndowment = $college_data->private_endowment_end_fy_12;
			$college_data->totalEndowment = $totalEndowment;
		}
		 */

		if($college_data->public_endowment_end_fy_12){
			$totalEndowment = $college_data->public_endowment_end_fy_12;

		} else if($college_data->private_endowment_end_fy_12){
			$totalEndowment = $college_data->private_endowment_end_fy_12;
		} else{
			$totalEndowment = "N/A";
		}
		$college_data->totalEndowment = $totalEndowment;


		$college_data->sat75percentile = $college_data->sat_read_75 + $college_data->sat_write_75 + $college_data->sat_math_75;
		$college_data->sat25percentile = $college_data->sat_read_25 + $college_data->sat_write_25 + $college_data->sat_math_25;

		// the values in this array are unset in the final $data array if they're not usable
		// this is used to hide the infoboxes
		$hideableBoxes = array(
			'sat75percentile' => $college_data->sat75percentile,
			'sat25percentile' => $college_data->sat25percentile,
			'graduation_rate_4_year' => $college_data->graduation_rate_4_year,
			'act_composite_75' => $college_data->act_composite_75,
			'act_composite_25' => $college_data->act_composite_25,
			'student_faculty_ratio' => $college_data->student_faculty_ratio,
			'sat_percent' => $college_data->sat_percent,
			'act_percent' => $college_data->act_percent,
			'rotc' => $college_data->rotc
		);

		// Figure out which boxes to hide
		$boxesToHide = array();
		foreach($college_data as $key => $val){
			$foo = $this->getHideArray($key, $val, $hideableBoxes);
			if(!is_null($foo)){
				$boxesToHide[] = $foo;
				$college_data->$foo = true;
			}
		}


		// Share images
		// Prefer overview image
		$share_image_arr = $this->getShareImage( $college_data->overview_image, $college_data->logo_url );

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if(isset($college_data))
		{
			$data['college_data'] =$college_data;
		}else{
			$data['college_data'] = null;
		}

		//Check to see if the urls have http in them and add if the dont.
		//We have 6 so far on stats.
		$data['college_data']->school_url = $this->urlCheckProtical($data['college_data']->school_url);
		$data['college_data']->admission_url = $this->urlCheckProtical($data['college_data']->admission_url);
		$data['college_data']->financial_aid_url = $this->urlCheckProtical($data['college_data']->financial_aid_url);
		$data['college_data']->application_url = $this->urlCheckProtical($data['college_data']->application_url);
		$data['college_data']->calculator_url = $this->urlCheckProtical($data['college_data']->calculator_url);
		$data['college_data']->mission_url = $this->urlCheckProtical($data['college_data']->mission_url);

		//get overview_images_table which contains all college media
		$mediaReturn = CollegeOverviewImages::where('college_id', '=', $college_data->id )->get()->toArray();

		//array to store all the stats youtube video ids
		$youtube_stats_vids = [];

		if( isset($mediaReturn) && !empty($mediaReturn) ){

			foreach ($mediaReturn as $key => $value) {

				if ( isset($value['section']) && $value['is_video'] == 1 && $value['section'] == "stats" ) {
					array_push($youtube_stats_vids, $value);
				}

			}

		}

		//add stats vid array to data array to be passed to stats view
		$data['college_data']->youtube_stats_videos = $youtube_stats_vids;

		// Share buttons
		// We don't need to create the parameters on the college pages
		$data['college_data']->share_buttons['extra_classes'] = array( 'share_college' );
		$data['college_data']->share_buttons['stl_text'] = "SHARE: ";

		// Overview image for sharing
		$data['college_data']->share_image = $share_image_arr['share_image'];
		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];


		//**********LIKES TALLY CODE STARTS**********/
		$data['college_data']->college_id = $data['college_data']->id;
		$data = $this->getCollegeLikesTally($data);
		//**********LIKES TALLY CODE ENDS ***********/

		if($this->cid_for_college_page == -1 && !isset($is_api)){
			return View('private.college.ajax.stats', $data);
		}else{
			return $data['college_data'];
		}

	}

	/* For social share feature on the /college/[slug] page.
	 * Receives first overview image for the school and school logo and then picks
	 * between them, depending on which is set. Falls back on plexuss logo if neither
	 * exists.
	 * @param		overview		string			overview image from college_overview_images table
	 * @param		logo			string			school logo from colleges table
	 * @return						array			an array with two indices:
	 * 												share_image is the name of the image
	 * 												share_image_path is the absolute path
	 * 												to the image
	 */
	private function getShareImage( $overview, $logo ){
		// Share images
		// Prefer overview image
		if( $overview ){
			$share_image = $overview;
			$share_image_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/";
		}
		// Then prefer college logo
		else if( $logo ){
			$share_image = $logo;
			$share_image_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/";
		}
		// Last resort is Plexuss logo
		else{
			$share_image = "plexussLogoLetterBlack.png";
			if (env('ENVIRONMENT') == 'LIVE') {
				$share_image_path = "/images/";
			}else{
				$share_image_path = "https://plexuss.com/images/";
			}
		}

		return array(
			"share_image" => $share_image,
			"share_image_path" => $share_image_path
		);
	}

	private function getHideArray($key, $val, $list){
		foreach($list as $k => $v){
			if($k == $key){
				if( $val === 0 ||
					$val === "0" ||
					$val === "" ||
					$val === " " ||
					is_null($val) ||
					strtolower($val) === 'implied no'
				){
					return "hide_" . $key;
				}
			}
		}
	}

    public function getRankingInfo($college_id = null, $is_api = null){

        if($this->cid_for_college_page != -1){
            $collegeId = $this->cid_for_college_page;
        }elseif (isset($college_id)) {
            $collegeId = $college_id;
        }else{
            $collegeId = Request::get('college_id');
        }

        $collegeModel = new College();
        $college_data = $collegeModel->CollegesRanking($collegeId);
        $college_data->plexuss_ranking = $college_data->plexuss;

        // set share image
        $share_image_arr = $this->getShareImage( $college_data->overview_image, $college_data->logo_url );

        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        if(isset($college_data))
        {
            $data['college_data'] =$college_data;
        }else{
            $data['college_data'] = null;
        }

        // Share buttons
        // We don't need to create the parameters on the college pages
        $data['college_data']->share_buttons['extra_classes'] = array( 'share_college' );
        $data['college_data']->share_buttons['stl_text'] = "SHARE: ";

        // Overview image for sharing
        $data['college_data']->share_image = $share_image_arr['share_image'];
        $data['college_data']->share_image_path = $share_image_arr['share_image_path'];

        //get college pins created by this school's admins
        $getPins = DB::table('lists')->where('custom_college', '=', $collegeId)->orderBy('rank_num', 'asc')->get();

        $tmp_array = array(
            array(),
            array(),
            array()
        );

        for ($i=0; $i < count($getPins); $i++) {
            if( $i % 3 ==  0 ){
                array_push($tmp_array[0], $getPins[$i]);
            }elseif( $i % 3 == 1 ){
                array_push($tmp_array[1], $getPins[$i]);
            }else{
                array_push($tmp_array[2], $getPins[$i]);
            }
        }

        $data['college_data']->ranking_pins_col_one = $tmp_array[0];
        $data['college_data']->ranking_pins_col_two = $tmp_array[1];
        $data['college_data']->ranking_pins_col_three = $tmp_array[2];

        //**********LIKES TALLY CODE STARTS**********/
        $data = $this->getCollegeLikesTally($data);
        //**********LIKES TALLY CODE ENDS ***********/

        if($this->cid_for_college_page == -1 && !isset($is_api)){
            return View( 'private.college.ajax.ranking', $data);
        }else{
            return $data['college_data'];
        }

    }

	public function getValueInfo($token = null){
		if(Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if(!$this->checkToken($token)){
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);
			$college_data=College::where('id', '=',$id)->get()->toArray();

			$data = array('token' => $token);
			$data['ajaxtoken'] = $token;

			if(isset($college_data))
			{
				$data['college_data'] =$college_data[0];
			}else{
				$data['college_data'] = null;
			}

			return View( 'private.college.ajax.value', $data);
		}
	}

	public function getAdmissionsInfo($college_id = null, $is_api = null){

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}elseif (isset($college_id)) {
			$collegeId = $college_id;
		}else{
			$collegeId = Request::get('college_id');
		}

		$collegeModel= new College();

		//$college_data=College::where('id', '=',$collegeId)->get()->toArray();
		$college_data = $collegeModel->CollegesAdmission($collegeId);

		if($college_data->applicants_total>0)
		{
			$college_data->percentadmitted = round(($college_data->admissions_total / $college_data->applicants_total) * 100);
		}
		if ($college_data->admissions_total>0){
			$college_data->per_adm_enrolled = round(($college_data->enrolled_total / $college_data->admissions_total) * 100);
		}

		$college_data->percentadmitted = (isset($college_data->percentadmitted) ? $college_data->percentadmitted : 0);
		$college_data->per_adm_enrolled = (isset($college_data->per_adm_enrolled) ? $college_data->per_adm_enrolled : 0);

		$college_data->application_url = $this->urlCheckProtical($college_data->application_url);

		$deadline = $college_data->deadline;
		$deadline = (is_null($deadline) ? 'N/A' : $deadline);
		$college_data->deadline = $deadline;

		if ($deadline === 'rolling admission') {
			$college_data->rollingadmissions = 'rollingadmissions';
		}

		// set share image
		$share_image_arr = $this->getShareImage( $college_data->overview_image, $college_data->logo_url );

		/*if(!$college_data['tuition_fees_1213']){
			$tuitionAlt = DB::table('colleges_tuition')->where('id', $collegeId)->pluck('tuition_avg_in_state_ftug');
			$college_data['tuition_fees_1213'] = $tuitionAlt;
		}*/

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if(isset($college_data))
		{
			$data['college_data'] = $college_data;
		}
		else{
			$data['college_data'] = null;
		}

		//get overview_images_table which contains all college media
		$admissionsMediaReturn = CollegeOverviewImages::where('college_id', '=', $college_data->id )->get()->toArray();

		//array to store all the admissions youtube video ids
		$youtube_admissions_vids = [];

		//loop through the media return and check if is video to pull necessary rows into array
		foreach ($admissionsMediaReturn as $key => $value) {

			if ( $value['is_video'] == 1 && isset($value['section']) && $value['section'] == 'admissions' ) {
				array_push($youtube_admissions_vids, $value);
			}

		}
		//add stats vid array to data array to be passed to stats view
		$data['college_data']->youtube_admissions_videos = $youtube_admissions_vids;

		// Share buttons
		// We don't need to create the parameters on the college pages
		$data['college_data']->share_buttons['extra_classes'] = array( 'share_college' );
		$data['college_data']->share_buttons['stl_text'] = "SHARE: ";

		// Set collegedata share images
		$data['college_data']->share_image = $share_image_arr['share_image'];
		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];

		//**********LIKES TALLY CODE STARTS**********/
		$data = $this->getCollegeLikesTally($data);
		//**********LIKES TALLY CODE ENDS ***********/

		if($this->cid_for_college_page == -1 && !isset($is_api)){
			return View( 'private.college.ajax.admissions', $data);
		}else{
			return $data['college_data'];
		}

	}

	public function getNotablesInfo($token = null){
		if(Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if(!$this->checkToken($token)){
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);
			$college_data=College::where('id', '=',$id)->get()->toArray();

			$data = array('token' => $token);
			$data['ajaxtoken'] = $token;

			if(isset($college_data))
			{
				$data['college_data'] =$college_data[0];
			}
			else{
				$data['college_data'] = null;
			}
			return View( 'private.college.ajax.notables', $data);
		}
	}

	public function getTuitionInfo($college_id = null, $is_api = null){

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}elseif (isset($college_id)) {
			$collegeId = $college_id;
		}else{
			$collegeId = Request::get('college_id');
		}

		$collegeModel= new College();

		$college_data = $collegeModel->CollegesTuition($collegeId);

		if (isset($college_data->cct_id)) {
			$cct = CollegeCustomTuition::on('rds1')->where('college_id', $collegeId)
											->select('amount as cct_amount', 'currency as cct_currency', 'title as cct_title', 'sub_title as cct_sub_title')
											->get();

			$college_data->custom_tuition = $cct;
		}

		// dd($college_data);
		/* In Campus */
		$college_data->total_inexpenses = round($college_data->tuition_avg_in_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_on_campus_1213 + $college_data->other_expenses_on_campus_1213);

		$college_data->total_outexpenses = round($college_data->tuition_avg_out_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_on_campus_1213 + $college_data->other_expenses_on_campus_1213);

		/* Off Campus */
		$college_data->total_off_inexpenses = round($college_data->tuition_avg_in_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_off_campus_nofam_1213 + $college_data->other_expenses_off_campus_nofam_1213);

		$college_data->total_off_outexpenses = round($college_data->tuition_avg_out_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_off_campus_nofam_1213 + $college_data->other_expenses_off_campus_nofam_1213);

		/* Stay Home */
		$college_data->total_home_inexpenses = round($college_data->tuition_avg_in_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_off_campus_nofam_1213 + $college_data->other_expenses_off_campus_yesfam_1213);

		$college_data->total_home_outexpenses = round($college_data->tuition_avg_out_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_off_campus_nofam_1213 + $college_data->other_expenses_off_campus_nofam_1213);

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if(isset($college_data))
		{
			$data['college_data'] =$college_data;
		}else{
			$data['college_data'] = null;
		}

		// set share image
		$share_image_arr = $this->getShareImage( $college_data->overview_image, $college_data->logo_url );


		//get overview_images_table which contains all college media
		$tuitionMediaReturn = CollegeOverviewImages::where('college_id', '=', $college_data->id )->get()->toArray();

		//array to store all the tuition youtube video ids
		$youtube_tuition_vids = [];

		//loop through the media return and check if is video to pull necessary rows into array
		foreach ($tuitionMediaReturn as $key => $value) {

			if ( $value['is_video'] == 1 && isset($value['section']) && $value['section'] == 'tuition' ) {
				array_push($youtube_tuition_vids, $value);
			}

		}
		//add stats vid array to data array to be passed to enrollment view
		$data['college_data']->youtube_tuition_videos = $youtube_tuition_vids;

		// Share buttons
		// We don't need to create the parameters on the college pages
		$data['college_data']->share_buttons['extra_classes'] = array( 'share_college' );
		$data['college_data']->share_buttons['stl_text'] = "SHARE: ";

		// Set collegedata share images
		$data['college_data']->share_image = $share_image_arr['share_image'];
		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];

		//**********LIKES TALLY CODE STARTS**********/
		$data = $this->getCollegeLikesTally($data);
		//**********LIKES TALLY CODE ENDS ***********/

		if($this->cid_for_college_page == -1 && !isset($is_api)){
			return View( 'private.college.ajax.tuition', $data);
		}else{
			return $data['college_data'];
		}
	}

	public function getFinancialInfo($college_id = null, $is_api = null){

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}elseif (isset($college_id)) {
			$collegeId = $college_id;
		}else{
			$collegeId = Request::get('college_id');
		}

		$collegeModel= new College();

		$college_data = $collegeModel->CollegesFinancial($collegeId);

		// Math Operation to Get Total Aid Amount
		$college_data->undergrad_aid_avg_amt = round(($college_data->undergrad_grant_avg_amt + $college_data->undergrad_loan_avg_amt) / 2);
		//$college_data->avg_netprice = round(($college_data->enrolled_total / $college_data->applicants_total) * 100);



		/* In Campus */
		$college_data->total_inexpenses = round($college_data->tuition_avg_in_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_on_campus_1213 + $college_data->other_expenses_on_campus_1213);

		/*	Difference in college data total for IN Campus financial aka total - (grant + loan)	*/
		$college_data->total_incamp_financial = round($college_data->total_inexpenses - ($college_data->undergrad_grant_avg_amt + $college_data->undergrad_loan_avg_amt));

		$college_data->total_outexpenses = round($college_data->tuition_avg_out_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_on_campus_1213 + $college_data->other_expenses_on_campus_1213);

		/*	Difference in college data total for OUT Campus financial aka total - (grant + loan)	*/
		$college_data->total_outcamp_financial = round($college_data->total_outexpenses - ($college_data->undergrad_grant_avg_amt + $college_data->undergrad_loan_avg_amt));





		/* Off Campus */
		$college_data->total_off_inexpenses = round($college_data->tuition_avg_in_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_off_campus_nofam_1213 + $college_data->other_expenses_off_campus_nofam_1213);

		/*	Difference in college data total for IN Campus financial aka total - (grant + loan)	*/
		$college_data->total_off_outcamp_infinancial = round($college_data->total_off_inexpenses - ($college_data->undergrad_grant_avg_amt + $college_data->undergrad_loan_avg_amt));

		$college_data->total_off_outexpenses = round($college_data->tuition_avg_out_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_off_campus_nofam_1213 + $college_data->other_expenses_off_campus_nofam_1213);

		/*	Difference in college data total for IN Campus financial aka total - (grant + loan)	*/
		$college_data->total_off_outcamp_outfinancial = round($college_data->total_off_outexpenses - ($college_data->undergrad_grant_avg_amt + $college_data->undergrad_loan_avg_amt));



		/* Stay Home */
		$college_data->total_home_inexpenses = round($college_data->tuition_avg_in_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_off_campus_nofam_1213 + $college_data->other_expenses_off_campus_yesfam_1213);

		/*	Difference in college data total for IN Campus financial aka total - (grant + loan)	*/
		$college_data->total_home_infinancial = round($college_data->total_home_inexpenses - ($college_data->undergrad_grant_avg_amt + $college_data->undergrad_loan_avg_amt));

		$college_data->total_home_outexpenses = round($college_data->tuition_avg_out_state_ftug + $college_data->books_supplies_1213 + $college_data->room_board_off_campus_nofam_1213 + $college_data->other_expenses_off_campus_nofam_1213);

		/*	Difference in college data total for IN Campus financial aka total - (grant + loan)	*/
		$college_data->total_home_outfinancial = round($college_data->total_home_outexpenses - ($college_data->undergrad_grant_avg_amt + $college_data->undergrad_loan_avg_amt));

		// set share image
		$share_image_arr = $this->getShareImage( $college_data->overview_image, $college_data->logo_url );

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if(isset($college_data))
		{
			$data['college_data'] =$college_data;
		}
		else{
			$data['college_data'] = null;
		}

		// Share buttons
		// We don't need to create the parameters on the college pages
		$data['college_data']->share_buttons['extra_classes'] = array( 'share_college' );
		$data['college_data']->share_buttons['stl_text'] = "SHARE: ";

		// Set collegedata share images
		$data['college_data']->share_image = $share_image_arr['share_image'];
		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];


		//get overview_images_table which contains all college media
		$financialAidMediaReturn = CollegeOverviewImages::where('college_id', '=', $college_data->id )->get()->toArray();

		//array to store all the financial youtube video ids
		$youtube_financial_vids = [];

		//loop through the media return and check if is video to pull necessary rows into array
		foreach ($financialAidMediaReturn as $key => $value) {

			if ( $value['is_video'] == 1 && isset($value['section']) && $value['section'] == 'financial' ) {
				array_push($youtube_financial_vids, $value);
			}

		}

		//add stats vid array to data array to be passed to stats view
		$data['college_data']->youtube_financial_videos = $youtube_financial_vids;
		//**********LIKES TALLY CODE STARTS**********/
		$data = $this->getCollegeLikesTally($data);
		//**********LIKES TALLY CODE ENDS ***********/


		if($this->cid_for_college_page == -1 && !isset($is_api)){
			return View('private.college.ajax.financial', $data);
		}else{
			return $data['college_data'];
		}
	}

	public function getCampusInfo($token = null){
		if(Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if(!$this->checkToken($token)){
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);
			$college_data=College::where('id', '=',$id)->get()->toArray();

			$data = array('token' => $token);
			$data['ajaxtoken'] = $token;

			if(isset($college_data))
			{
				$data['college_data'] =$college_data[0];
			}
			else{
				$data['college_data'] = null;
			}
			return View( 'private.college.ajax.campus', $data);
		}
	}

	public function getAthleticsInfo($token = null){
		$collegeId = Request::get('college_id');
		if(Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if(!$this->checkToken($token)){
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);
			$collegeModel= new College();

			$college_data = $collegeModel->CollegesAthletics($collegeId);

			$data = array('token' => $token);
			$data['ajaxtoken'] = $token;

			/*$data['games'] = array();
			foreach($college_data[0] as $keyIndex=>$keyData){
				$type = explode("_",$keyIndex);
				$type = $type[count($type)-1];
				$game = str_replace("_".$type,'',$keyIndex);
				if($type == "men" || $type == "women"){
					$data['games'][$game][$type] = $keyData;

				}
			}*/


			if(isset($college_data))
			{
				$data['college_data'] =$college_data[0];
			}
			else{
				$data['college_data'] = null;
			}
			return View( 'private.college.ajax.athletics', $data);
		}
	}

	public function getEnrollmentInfo($college_id = null, $is_api = null){

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}elseif (isset($college_id)) {
			$collegeId = $college_id;
		}else{
			$collegeId = Request::get('college_id');
		}

		$collegeModel= new College();

		$college_data = $collegeModel->CollegesEnrollment($collegeId);

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		//Math for distance education percentages
		$distanceTotal = $college_data->undergrad_dist_excl_total + $college_data->undergrad_dist_some_total;
		if($college_data->undergrad_total){
			$distancePercent = round(($distanceTotal / $college_data->undergrad_total) * 100);
			$distancePercentNone = 100 - $distancePercent;
		} else{
			$distancePercent = "N/A";
			$distancePercentNone = "N/A";
		}
		$college_data->distance_pct = $distancePercent;
		$college_data->distance_none_pct = $distancePercentNone;

		//Math for over/under 25 percentages
		if($college_data->undergrad_total){
			$over25Pct = round(($college_data->undergrad_age_25_over_total / $college_data->undergrad_total) * 100);
			$under24Pct = round(($college_data->undergrad_age_24_under_total / $college_data->undergrad_total) * 100);
		} else{
			$over25Pct = "N/A";
			$under24Pct = "N/A";
		}
		$college_data->age_25_over_pct = $over25Pct;
		$college_data->age_24_under_pct = $under24Pct;

		//Math for foreign/out of state, etc.
		if($college_data->undergrad_total){
			$foreignPct = round(($college_data->undergrad_foreign_total / $college_data->undergrad_total) * 100);
		} else{
			$foreignPct = "N/A";
		}
		$college_data->foreign_pct = $foreignPct;


		// Math Operations to get UG Ehtnicity Box values.

		// American Indian or Alaska Native Students
		if($college_data->undergrad_total != 0){
			$aianfinalPercent = round(($college_data->undergrad_aian_total / $college_data->undergrad_total) * 100);
			$college_data->aianfinalPercent = $aianfinalPercent;

			// Asian Students
			$asianfinalPercent = round(($college_data->undergrad_asia_total / $college_data->undergrad_total) * 100);
			$college_data->asianfinalPercent = $asianfinalPercent;

			// Black or African American Students
			$bkaafinalPercent = round(($college_data->undergrad_bkaa_total / $college_data->undergrad_total) * 100);
			$college_data->bkaafinalPercent = $bkaafinalPercent;

			// Hispanic or Latino Students
			$hispfinalPercent = round(($college_data->undergrad_hisp_total / $college_data->undergrad_total) * 100);
			$college_data->hispfinalPercent = $hispfinalPercent;

			// Native Hawaiin Students
			$nhpifinalPercent = round(($college_data->undergrad_nhpi_total / $college_data->undergrad_total) * 100);
			$college_data->nhpifinalPercent = $nhpifinalPercent;

			// White Students
			$whitefinalPercent = round(($college_data->undergrad_whit_total / $college_data->undergrad_total) * 100);
			$college_data->whitefinalPercent = $whitefinalPercent;

			// 2 or more race Students
			$twomorefinalPercent = round(($college_data->undergrad_2mor_total / $college_data->undergrad_total) * 100);
			$college_data->twomorefinalPercent = $twomorefinalPercent;

			// Unknown Race Students
			$unknownfinalPercent = round(($college_data->undergrad_unkn_total / $college_data->undergrad_total) * 100);
			$college_data->unknownfinalPercent = $unknownfinalPercent;

			// Unknown Non Resident Alien Students
			$alienfinalPercent = round(($college_data->undergrad_nral_total / $college_data->undergrad_total) * 100);
			$college_data->alienfinalPercent = $alienfinalPercent;

		} else{
			$college_data->aianfinalPercent = 0;
			$college_data->asianfinalPercent = 0;
			$college_data->bkaafinalPercent = 0;
			$college_data->hispfinalPercent = 0;
			$college_data->nhpifinalPercent = 0;
			$college_data->whitefinalPercent = 0;
			$college_data->twomorefinalPercent = 0;
			$college_data->unknownfinalPercent = 0;
			$college_data->alienfinalPercent = 0;
		}

		if(isset($college_data))
		{
			$data['college_data'] =$college_data;
		}else{
			$data['college_data'] = null;
		}

		// set share image
		$share_image_arr = $this->getShareImage( $college_data->overview_image, $college_data->logo_url );

		// Share buttons
		// We don't need to create the parameters on the college pages
		$data['college_data']->share_buttons['extra_classes'] = array( 'share_college' );
		$data['college_data']->share_buttons['stl_text'] = "SHARE: ";

		// Set collegedata share images
		$data['college_data']->share_image = $share_image_arr['share_image'];
		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];


		//get overview_images_table which contains all college media
		$enrollmentMediaReturn = CollegeOverviewImages::where('college_id', '=', $college_data->id )->get()->toArray();

		//array to store all the enrollment youtube video ids
		$youtube_enrollment_vids = [];

		//loop through the media return and check if is video to pull necessary rows into array
		foreach ($enrollmentMediaReturn as $key => $value) {

			if ( $value['is_video'] == 1 && isset($value['section']) && $value['section'] == "enrollment" ) {
				array_push($youtube_enrollment_vids, $value);
			}

		}
		//add stats vid array to data array to be passed to enrollment view
		$data['college_data']->youtube_enrollment_videos = $youtube_enrollment_vids;

		//**********LIKES TALLY CODE STARTS**********/
		$data = $this->getCollegeLikesTally($data);
		//**********LIKES TALLY CODE ENDS ***********/

		if($this->cid_for_college_page == -1 && !isset($is_api)){
			return View('private.college.ajax.enrollment', $data);
		}else{
			return $data['college_data'];
		}
	}

	public function getProgramsInfo($token = null){
		if(Auth::check()){
			//get id by authcheck
			$id = Auth::id();
			//check if token is good.
			if(!$this->checkToken($token)){
				return 'Invalid Token';
			}

			//Look up user data by id.
			$user = User::find($id);
			$college_data=College::where('id', '=',$id)->get()->toArray();

			$data = array('token' => $token);
			$data['ajaxtoken'] = $token;

			if(isset($college_data))
			{
				$data['college_data'] =$college_data[0];
			}

			return View( 'private.college.ajax.programs', $data);
		}
	}

	public function getChatInfo($token = null){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}else{
			$collegeId = Request::get('college_id');
		}

		// build default view data. We need this to check if a user
		// is signed in or not
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$chat = new ChatMessageController();

		// create array to hold chat data
		$chat_data = array();
	    $chat_data = $chat->getUsrTopics($collegeId, 'users');
		$data['chat'] = $chat_data;

		// we need this to build the /portal/messages/[college_id] link
		// if a college is not currently hosting online chat
		$data['CollegeId'] = $collegeId;

		return View( 'private.college.ajax.chat', $data);
	}

	public function getNewsInfo($college_id = null, $is_api = null){

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}elseif (isset($college_id)) {
			$collegeId = $college_id;
		}else{
			$collegeId = Request::get('college_id');
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$collegeModel = new College();
		$college_overview = $collegeModel->CollegesRanking($collegeId);

		$data['college_data'] = $college_overview;

		//THIS IS NOT STATS - ITS STATS PAGE META DATA!!!!! FIX
        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId )) {
        	$college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId);
        } else {
        	$college_stats_model = CollegeStats::where('college_id', '=', $collegeId)->first();
        	Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId, $college_stats_model, 60);
        }

        // news articles
        $college = College::where('id',$collegeId)->first();
        $data['college_name'] = $college['school_name'];
        $data['collegeMappings'] = NewsCollegeMapping::where('college_id',$collegeId)->get();

        // SEO
        $data['college_data']->page_title = $college_stats_model->page_title;

		$share_image_arr = $this->getShareImage( $college_overview->overview_image, $college_overview->logo_url );

		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];
		$data['college_data']->share_image = $share_image_arr['share_image'];

		// dd($data['news']);
		if(isset($is_api)){
			$data['news'] = $this->getNewsFromBing($college_overview->College);
			return $data['news'];

		}elseif($this->cid_for_college_page == -1){

			$data['news'] = $this->getNewsFromBing($college_overview->College);

			return View('private.college.ajax.news', $data);
		}else{
			return $data['college_data'];
		}
	}

	public function getUnderGradInfo($token = null) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}else{
			$collegeId = Request::get('college_id');
		}

		$collegeModel = new College();
		$college_overview = $collegeModel->CollegesRanking($collegeId);

		$data['college_data'] = $college_overview;

		//THIS IS NOT STATS - ITS STATS PAGE META DATA!!!!! FIX
        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId )) {
        	$college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId);
        } else {
        	$college_stats_model = CollegeStats::where('college_id', '=', $collegeId)->first();
        	Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId, $college_stats_model, 60);
        }

        $qry = DB::connection('rds1')->table('revenue_programs as rp')
        							 ->join('revenue_programs_selling_point as rpsp', 'rp.id', '=', 'rpsp.rp_id')
        							 ->where('rp.college_id', $collegeId)
        							 ->orderBy('rp.id')
        							 ->select('rp.id as rp_id', 'rp.program_name', 'degree_type', 'rpsp.selling_point')
        							 ->get();

        $tmp_arr = array();
        $tmp_rp_id = null;
        foreach ($qry as $key) {
        	if (!isset($tmp_rp_id) || $key->rp_id != $tmp_rp_id) {
        		if (isset($arr)) {
        			$tmp_arr[] = $arr;
        		}
        		$arr = array();
        		$arr['program_name'] = $key->program_name;
        		$arr['degree_type']  = $key->degree_type;
        		$arr['selling_points'] = array();
        		$arr['selling_points'][] = $key->selling_point;

        		$tmp_rp_id = $key->rp_id;
        	}else{
        		$arr['selling_points'][] = $key->selling_point;
        	}

        }

        if (isset($arr)) {
        	$tmp_arr[] = $arr;
        }
        $data['college_data']->revenue_programs = $tmp_arr;
		// SEO
        $data['college_data']->page_title = $college_stats_model->page_title;

		$share_image_arr = $this->getShareImage( $college_overview->overview_image, $college_overview->logo_url );

		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];
		$data['college_data']->share_image = $share_image_arr['share_image'];

		// dd($data['news']);

		if($this->cid_for_college_page == -1){

			$cit = new CollegesInternationalTab;
			$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($collegeId);

			return View('private.college.ajax.undergrad', $data);
		}else{
			return $data['college_data'];
		}
	}

	public function getGradInfo($token = null) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}else{
			$collegeId = Request::get('college_id');
		}

		$collegeModel = new College();
		$college_overview = $collegeModel->CollegesRanking($collegeId);

		$data['college_data'] = $college_overview;

		//THIS IS NOT STATS - ITS STATS PAGE META DATA!!!!! FIX
        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId )) {
        	$college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId);
        } else {
        	$college_stats_model = CollegeStats::where('college_id', '=', $collegeId)->first();
        	Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId, $college_stats_model, 60);
        }

        $qry = DB::connection('rds1')->table('revenue_programs as rp')
        							 ->join('revenue_programs_selling_point as rpsp', 'rp.id', '=', 'rpsp.rp_id')
        							 ->where('rp.college_id', $collegeId)
        							 ->orderBy('rp.id')
        							 ->select('rp.id as rp_id', 'rp.program_name', 'degree_type', 'rpsp.selling_point')
        							 ->get();

        $tmp_arr = array();
        $tmp_rp_id = null;
        foreach ($qry as $key) {
        	if (!isset($tmp_rp_id) || $key->rp_id != $tmp_rp_id) {
        		if (isset($arr)) {
        			$tmp_arr[] = $arr;
        		}
        		$arr = array();
        		$arr['program_name'] = $key->program_name;
        		$arr['degree_type']  = $key->degree_type;
        		$arr['selling_points'] = array();
        		$arr['selling_points'][] = $key->selling_point;

        		$tmp_rp_id = $key->rp_id;
        	}else{
        		$arr['selling_points'][] = $key->selling_point;
        	}

        }

        if (isset($arr)) {
        	$tmp_arr[] = $arr;
        }
        $data['college_data']->revenue_programs = $tmp_arr;

		// SEO
        $data['college_data']->page_title = $college_stats_model->page_title;

		$share_image_arr = $this->getShareImage( $college_overview->overview_image, $college_overview->logo_url );

		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];
		$data['college_data']->share_image = $share_image_arr['share_image'];

		// dd($data['news']);

		if($this->cid_for_college_page == -1){


			$cit = new CollegesInternationalTab;
			$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($collegeId);

			return View('private.college.ajax.grad', $data);
		}else{
			return $data['college_data'];
		}
	}

	public function getEppInfo($token = null) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}else{
			$collegeId = Request::get('college_id');
		}

		$collegeModel = new College();
		$college_overview = $collegeModel->CollegesRanking($collegeId);

		$data['college_data'] = $college_overview;

		//THIS IS NOT STATS - ITS STATS PAGE META DATA!!!!! FIX
        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId )) {
        	$college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId);
        } else {
        	$college_stats_model = CollegeStats::where('college_id', '=', $collegeId)->first();
        	Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId, $college_stats_model, 60);
        }

		// SEO
        $data['college_data']->page_title = $college_stats_model->page_title;

		$share_image_arr = $this->getShareImage( $college_overview->overview_image, $college_overview->logo_url );

		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];
		$data['college_data']->share_image = $share_image_arr['share_image'];

		// dd($data['news']);

		if($this->cid_for_college_page == -1){


			$cit = new CollegesInternationalTab;
			$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($collegeId);

			return View('private.college.ajax.englishProg', $data);
		}else{
			return $data['college_data'];
		}
	}

	public function getPathwayInfo($token = null) {

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		if($this->cid_for_college_page != -1){
			$collegeId = $this->cid_for_college_page;
		}else{
			$collegeId = Request::get('college_id');
		}

		$collegeModel = new College();
		$college_overview = $collegeModel->CollegesRanking($collegeId);

		$data['college_data'] = $college_overview;

		//THIS IS NOT STATS - ITS STATS PAGE META DATA!!!!! FIX
        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId )) {
        	$college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId);
        } else {
        	$college_stats_model = CollegeStats::where('college_id', '=', $collegeId)->first();
        	Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId, $college_stats_model, 60);
        }

		// SEO
        $data['college_data']->page_title = $college_stats_model->page_title;

		$share_image_arr = $this->getShareImage( $college_overview->overview_image, $college_overview->logo_url );

		$data['college_data']->share_image_path = $share_image_arr['share_image_path'];
		$data['college_data']->share_image = $share_image_arr['share_image'];

		// dd($data['news']);

		if($this->cid_for_college_page == -1){

			$cit = new CollegesInternationalTab;
			$data['undergrad_grad_data'] = $cit->getCollegeInternationalTab($collegeId);


			if( isset($data['college_data']->aor_id) && $data['college_data']->aor_id == 5 ){
				return View('private.college.ajax.els', $data);
			}else{
				return View('private.college.ajax.englishProg', $data);
			}
		}else{
			return $data['college_data'];
		}
	}

    public function getCurrentStudent($college_id = null, $is_api = null){

        if($this->cid_for_college_page != -1){
            $collegeId = $this->cid_for_college_page;
        }elseif (isset($college_id)) {
            $collegeId = $college_id;
        }else{
            $collegeId = Request::get('college_id');
        }

        $collegeModel = new College();
        $college_overview = $collegeModel->CollegesRanking($collegeId);

        $data['college_data'] = $college_overview;

        //THIS IS NOT STATS - ITS STATS PAGE META DATA!!!!! FIX
        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId )) {
            $college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId);
        } else {
            $college_stats_model = CollegeStats::where('college_id', '=', $collegeId)->first();
            Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId, $college_stats_model, 60);
        }

        // SEO
        $data['college_data']->page_title = $college_stats_model->page_title;
        $share_image_arr = $this->getShareImage( $college_overview->overview_image, $college_overview->logo_url );
        $data['college_data']->share_image_path = $share_image_arr['share_image_path'];
        $data['college_data']->share_image = $share_image_arr['share_image'];

        //get college
        $data['CollegeData'] = College::find($collegeId);
       // current student
        $currentStudent = $collegeModel->CurrentStudent($collegeId);
        $data['currentStudents'] = $currentStudent;
        $data['college_id'] = $collegeId;
        // count
        // $data['countCurrentStudent'] = $collegeModel->countCurrentStudent($collegeId);

        if($this->cid_for_college_page == -1 && !isset($is_api)){
            return View( 'private.college.ajax.currentStudent', $data);
        }else{
            return $data['college_data'];
        }

    }

    public function getAlumni($college_id = null, $is_api = null){

        if($this->cid_for_college_page != -1){
            $collegeId = $this->cid_for_college_page;
        }elseif (isset($college_id)) {
            $collegeId = $college_id;
        }else{
            $collegeId = Request::get('college_id');
        }

        $collegeModel = new College();
        $college_overview = $collegeModel->CollegesRanking($collegeId);

        $data['college_data'] = $college_overview;

        if (Cache::has(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId )) {
            $college_stats_model = Cache::get(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId);
        } else {
            $college_stats_model = CollegeStats::where('college_id', '=', $collegeId)->first();
            Cache::add(env('ENVIRONMENT') .'_'.'college_stats_' . $collegeId, $college_stats_model, 60);
        }

        // SEO
        $data['college_data']->page_title = $college_stats_model->page_title;
        $share_image_arr = $this->getShareImage( $college_overview->overview_image, $college_overview->logo_url );
        $data['college_data']->share_image_path = $share_image_arr['share_image_path'];
        $data['college_data']->share_image = $share_image_arr['share_image'];

        //alumni
        $alumni = $collegeModel->AlumniStudent($collegeId);
        $data['alumniStudents'] = $alumni;
        $data['college_id'] = $collegeId;
        // count
        // $data['countAlumni'] = $collegeModel->countAlumni($collegeId);

        if($this->cid_for_college_page == -1 && !isset($is_api)){
            return View( 'private.college.ajax.alumni', $data);
        }else{
            return $data['college_data'];
        }

    }

    public function getCollegeListSchools(){
		$schoolId = Request::get('rankid');
		$expandID= Request::get('expandID');

		$collegeModel= new College();
		$listData = $collegeModel->getCollegeListSchools($schoolId);
		$data = "";
		//{{$schoolData->city}}, {{$schoolData->long_state}}
		$i=1;
		foreach($listData as $datalist){
		if($i>4)
		break;
		$data.='
		<div>
			<div>
				<ul class="ul-d-inline">
					<li class="box_image-no mt10 ml10">#'.$datalist->plexuss_rank.'</li>
					<li class="pl25" style="width:80%">
						<span class="battlefont fs14"><a href="/college/'.$datalist->collegeURI.'" style="color:#fff;">'.$datalist->schoolName.'</a></span><br>
						<span class="battlefont fs14 f-normal ">'.$datalist->city.' , '.$datalist->long_state.'</span>
					</li>
				</ul>
			</div>
		</div>';
		$i++;
		}


		$data.='
		<div id="expanddiv'.$expandID.'" style="display:none" class="expanddiv">';
		$j=0;
		foreach($listData as $datalist){
		if($j>4)
		$data.='
		<div>
			<div>
				<ul class="ul-d-inline">
					<li class="box_image-no mt10 ml10">#'.$datalist->plexuss_rank.'&nbsp;</li>
					<li class="pl25" style="width:80%">
						<span class="battlefont fs14">'.$datalist->schoolName.'</span><br>
						<span class="battlefont fs14 f-normal ">'.$datalist->city.' , '.$datalist->long_state.'</span>
					</li>
				</ul>
			</div>
		</div>;
		';
		$j++;
		}
		$data.='</div>';



		/*$data.='
		<div class="footer-banner" style="background-color:#26b24b">
		<h6 class="battlefont fs14 txt-center" id="expand-toggle22" onclick="expandDiv(22);">gfdfhgjk</h6>
		<img src="/images/colleges/expand.png">
		</div>';
		*/



		/*$innerContent.='<tr style="background:#000000">
							<td width="5%"><div class="box_image-no ml10">#1</div></td>
							<td></td>
							<td>
								<div>
									<span class="battlefont fs14">'.$datalist->schoolName.'</span><br>
									<span class="battlefont fs14 f-normal ">'.$datalist->city.' , '.$datalist->long_state.'</span>
								</div>
							</td>
						</tr>';


		return $innerContent;*/
		return $data;
	}

	public function viewStudentApplication(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'College Application Preview';
		$data['currentPage'] = 'college-application-preview-page';

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}

		return View('socialNetwork.index', $data);
	}

	public function getInternationalStudentsView(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss International Students';
		$data['currentPage'] = 'international-students-page';

		$input = Request::all();

		$str = '';

		foreach ($input as $key => $value) {
			$str .= $key.'='.$value."&";
		}

		$str = rtrim($str, "&");

		$data['url_params'] = $str;

		isset($input['aid']) ?  $data['aid']  = $input['aid']  : null;
		isset($input['type']) ? $data['type'] = $input['type'] : null;

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}

		$utm_source = Input::get('utm_source');

		if( $utm_source == 'whatsapppage' ) {
			$data['title'] = 'Get Started';
			$data['currentPage'] = 'plex-get-started';

			return View('internationalStudents.phoneNumber', $data);
		}

		return View('internationalStudents.master', $data);
	}


	public function userPremiumPlans(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss International Students';
		$data['currentPage'] = 'international-students-page';

		$input = Request::all();

		$str = '';

		foreach ($input as $key => $value) {
			$str .= $key.'='.$value."&";
		}

		$str = rtrim($str, "&");

		$data['url_params'] = $str;

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}

		return View('internationalStudents.master', $data);
	}

	public function collegeApplication(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss International Students';
		$data['currentPage'] = 'international-students-page';

		$input = Request::all();

		$str = '';

		foreach ($input as $key => $value) {
			$str .= $key.'='.$value."&";
		}

		$str = rtrim($str, "&");

		$data['url_params'] = $str;

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}

		return View('internationalStudents.master', $data);
	}
	public function getInternationalResourcesView(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$data['title'] = 'Plexuss International Resources';
		$data['currentPage'] = 'international-resources-page';

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}

		return View('internationalStudents.master', $data);
	}

	public function getInternationalStudentsAjax(){

		$p = new Priority;
		$dt = $p->getPrioritySchoolsForIntlStudents();

		return $dt;
	}

	/* Check if token is same or not */
	private function checkToken( $token ) {

		$ajaxtoken = AjaxToken::where( 'token', '=', $token )->first();
		if ( !$ajaxtoken ) {
			return 0;
		} else {
			return 1;
		}
	}

	private function urlCheckProtical( $url ){
		//The database has fields with JUST spaces!!!! so I check for both :P
		if (strpos( $url ,'http') === false && $url != " " && $url != "") {
			$url = 'http://'. $url;
		}

		return $url;
	}

	private function getCollegeLikesTally($data){

		//**********LIKES TALLY CODE STARTS**********/

		$likes_tally = new LikesTally;

		$arr = array();
		$arr['type'] = 'college';
		$arr['type_col'] = 'id';
		$arr['type_val'] = $data['college_data']->college_id;
		$likes_tally = $likes_tally->getLikesTally($arr);

		$data['college_data']->likes_tally = $likes_tally->cnt;
		if($likes_tally->isLiked == 0){
			$is_liked_img = '/images/social/like-icon-dark-gray.png';
		}else{
			$is_liked_img = '/images/social/like-icon-orange.png';
		}

		 $data['college_data']->is_liked_img = $is_liked_img;
		 $data['college_data']->hased_college_id = Crypt::encrypt($arr['type_val']);
		//**********LIKES TALLY CODE ENDS ***********/

		 return $data;
	}

	private function getNewsFromBing($school_name){
		$url = "https://api.cognitive.microsoft.com/bing/v7.0/news/search";
		$client = new Client(['base_uri' => 'http://httpbin.org']);

		//q is the news search query (colleges.school_name), count->take, offset->skip
		//backup subscription key - 7f4cd30cc8e14f9b87162aaac8e18256
		$response = $client->request('GET', $url, ['headers' => [
		        'Ocp-Apim-Subscription-Key'	=> '6df34635ac4f4b42a9392a4036dea783'],
		        'query' => [
				'q' => '"'.$school_name.'"',
			    'count' => '10',
			    'offset' => '0',
			    'mkt' => 'en-us',
			    'safeSearch' => 'Moderate',
			    'originalImg' => true]]);

		$newsArray = json_decode($response->getBody()->getContents(), true)['value'];

		foreach ($newsArray as $key => $val) {
			$newsArray[$key]['datePublished'] = str_replace("T", " ", $newsArray[$key]['datePublished']);

			if (strlen($newsArray[$key]['datePublished']) == 10) {
				$newsArray[$key]['datePublished'] .= ' 00:00:00';
			}

			$newsArray[$key]['datePublished'] = substr($newsArray[$key]['datePublished'], 0, strpos($newsArray[$key]['datePublished'], "."));
			$dt = Carbon::createFromFormat('Y-m-d H:i:s', $newsArray[$key]['datePublished'])->timezone('UTC');
			// try{

			// }
			// catch(exception $e){
			// 	dd($newsArray[$key]['datePublished']);
			// }
			$newsArray[$key]['datePublished'] = Carbon::now()->diffForHumans($dt);
			$newsArray[$key]['datePublished'] = str_replace("after", "ago", $newsArray[$key]['datePublished']);
			$newsArray[$key]['datePublished'] = str_replace("before", "ago", $newsArray[$key]['datePublished']);
		}

		return $newsArray;
	}

	public function currentStudentAjaxData(){
	    $skipAmount = Request::get('skipAmount');
	    $collegeId = Request::get('collegeId');
	    $collegeModel = new College();

        $currentStudent = $collegeModel->currentStudentAjaxData($skipAmount, $collegeId);
        $data['currentStudents'] = $currentStudent;
        $data['college_id'] = $collegeId;

        return View( 'private.college.ajax.currentStudentAjaxData', $data);
    }

    public function alumniAjaxData(){
        $skipAmount = Request::get('skipAmount');
        $collegeId = Request::get('collegeId');
        $collegeModel = new College();

        $alumniStudents = $collegeModel->alumniAjaxData($skipAmount, $collegeId);
        $data['alumniStudents'] = $alumniStudents;
        $data['college_id'] = $collegeId;

        return View( 'private.college.ajax.alumniAjaxData', $data);
    }

    public function department($slug='', $major_slug = NULL, $is_api = NULL) {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Department';
		$data['currentPage'] = 'department';

		$data['country_based_on_ip']      = $this->iplookup()['countryAbbr'];

		$src="/images/profile/default.png";

		if($data['profile_img_loc'] !=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}

		$data['profile_img_loc'] = $src;

		$input = Request::all();

		if($slug==''){
			
			if ( Cache::has(env('ENVIRONMENT') .'_'.'college_department_main_page')){
				$tmp = array();
				$tmp = Cache::get(env('ENVIRONMENT') .'_'.'college_department_main_page');
			} else {
				$tmp = array();
				$obj = (object) array('meta_title' => 'Find You Major | College Degree Programs | Plexuss','meta_description' => 'Choosing a major is a big decision, but you don’t have to make it alone. Our majors guide will make finding the right major a little easier');
				$tmp['metainfo'] = $obj;

				$collegeModel = new College();
				$depts = $collegeModel->getDepartmentCats();
				$tmp['depts'] = $depts;

				Cache::put(env('ENVIRONMENT') .'_'.'college_department_main_page', $tmp, 1440);
			}

			$data['metainfo'] = $tmp['metainfo'];
			$data['depts']    = $tmp['depts'];

			if (isset($is_api)) {
				return json_encode($tmp);
			}else{
				return View('public.department.main_majors', $data);
			}

		}elseif(isset($major_slug)){
			$search = new Search();
			if ( Cache::has(env('ENVIRONMENT') .'_'.'CollegeController_departments')){
				$department_arr = array();
				$department_arr = Cache::get(env('ENVIRONMENT') .'_'.'CollegeController_departments');
			} else {
				$departments = $search->getDepts();
				foreach ($departments as &$department)
			    {
					$info = $search->getDeptMetaInfo($department->url_slug);
					$majors = $search->getMajorsByDepartment($info->id);
					$all_majors[$department->name]['slug'] = $department->url_slug;
					$all_majors[$department->name]['majors'] = $majors;
				}
				$department_arr = array();
				$department_arr['departments'] = $departments;
				$department_arr['all_majors'] = $all_majors;
				Cache::put(env('ENVIRONMENT') .'_'.'CollegeController_departments', $department_arr, 1440);
			}

			$mdm = MetaDataMajor::on('rds1')->where('slug', $major_slug)->first();

			if ( Cache::has(env('ENVIRONMENT') .'_'.'college_department_major_slug_'.$slug.'_'.$major_slug)){
				$tmp = array();
				$tmp = Cache::get(env('ENVIRONMENT') .'_'.'college_department_major_slug_'.$slug.'_'.$major_slug);
				Cache::forever(env('ENVIRONMENT') .'_'.'college_department_major_slug_'.$slug.'_'.$major_slug, $tmp);
			}
			// elseif(isset($mdm->json_data) && !empty($mdm->json_data)) {
			// 	$searchData = json_decode($mdm->json_data);
			// 	$tmp = array();

			// 	$tmp['selected'] = $slug;
			// 	$tmp['selected_major'] = $major_slug;
			// 	//$info = $search->getDeptInfo($slug);
			// 	$tmp['metainfo'] = $metainfo = $search->getMajorMetaInfo($slug, $major_slug);
			// 	$tmp['departments'] = $department_arr['departments'];
			// 	//$tmp['info'] = $info->info;
			// 	$tmp['querystring'] = $search_array = array('type'=>'college', 'majors_department_slug'=>$major_slug);

			// 	$tmp['searchData'] = $searchData[1];

			//     $tmp['all_departments_with_majors'] = $department_arr['all_majors'];

			// }
			else {
				$tmp = array();

				$tmp['selected'] = $slug;
				$tmp['selected_major'] = $major_slug;
				//$info = $search->getDeptInfo($slug);
				$tmp['metainfo'] = $metainfo = $search->getMajorMetaInfo($slug, $major_slug);
				$tmp['departments'] = $department_arr['departments'];
				//$tmp['info'] = $info->info;
				$tmp['querystring'] = $search_array = array('type'=>'college', 'majors_department_slug'=>$major_slug);

				// $searchData = $search->SearchData($search_array);
				// $tmp['searchData'] = $searchData[1];
				if (Cache::has(env('ENVIRONMENT') .'_'.'college_department_major_slug_searchData_'.$slug.'_'.$major_slug)) {
					$searchData = Cache::get(env('ENVIRONMENT') .'_'.'college_department_major_slug_searchData_'.$slug.'_'.$major_slug);
					$tmp['searchData'] = $searchData[1];
				}else{
					$tmp['searchData'] = null;
				}

			    $tmp['all_departments_with_majors'] = $department_arr['all_majors'];
		    	Cache::forever(env('ENVIRONMENT') .'_'.'college_department_major_slug_'.$slug.'_'.$major_slug, $tmp);
			}
			// dd($tmp);
			$data['selected'] 	 	= $tmp['selected'];
			$data['selected_major'] = $tmp['selected_major'];
			$data['metainfo'] 	 	= $tmp['metainfo'];
			$data['departments'] 	= $tmp['departments'];
			$data['querystring'] 	= $tmp['querystring'];
			$data['searchData']	 	= $tmp['searchData'];
			$data['all_departments_with_majors'] = $tmp['all_departments_with_majors'];
			// dd($data);
			if (isset($is_api)) {
				return json_encode($tmp);
			}else{
				return View('public.department.major', $data);
			}

		}else{
			$search = new Search();
			if ( Cache::has(env('ENVIRONMENT') .'_'.'CollegeController_departments')){
				$department_arr = array();
				$department_arr = Cache::get(env('ENVIRONMENT') .'_'.'CollegeController_departments');
			} else {
				$departments = $search->getDepts();
				foreach ($departments as &$department)
			    {
					$info = $search->getDeptMetaInfo($department->url_slug);
					$majors = $search->getMajorsByDepartment($info->id);
					$all_majors[$department->name]['slug'] = $department->url_slug;
					$all_majors[$department->name]['majors'] = $majors;
				}
				$department_arr = array();
				$department_arr['departments'] = $departments;
				$department_arr['all_majors'] = $all_majors;

				Cache::put(env('ENVIRONMENT') .'_'.'CollegeController_departments', $department_arr, 1440);
			}

			if ( Cache::has(env('ENVIRONMENT') .'_'.'college_department_inner_page_new'. $slug) && !isset($input['page']) && $slug !="study-trades"){
				$tmp = array();
				$tmp = Cache::get(env('ENVIRONMENT') .'_'.'college_department_inner_page_new'. $slug);
			} else {
				$tmp = array();
				$tmp['selected'] = $slug;
				$tmp['selected_major'] = $major_slug;
				//$info = $search->getDeptInfo($slug);
				$tmp['metainfo'] = $metainfo = $search->getDeptMetaInfo($slug);
				$tmp['majors_for_department'] = $search->getMajorsByDepartment($metainfo->id);
				$tmp['departments'] = $department_arr['departments'];

			    $tmp['all_departments_with_majors'] = $department_arr['all_majors'];
				//$tmp['info'] = $info->info;

				$tmp['querystring'] = $search_array = array('type'=>'college', 'department'=>$slug);
				$searchData = $search->SearchData($search_array);
				$tmp['searchData'] = $searchData[1];

				if (!isset($input['page'])) {
					Cache::put(env('ENVIRONMENT') .'_'.'college_department_inner_page_new'. $slug, $tmp, 1440);
				}
			}
			$data['selected'] 	 = $tmp['selected'];
			$data['selected_major'] 	 = $tmp['selected_major'];
			$data['metainfo'] 	 = $tmp['metainfo'];
			$data['departments'] = $tmp['departments'];
			$data['querystring'] = $tmp['querystring'];
			$data['searchData']	 = $tmp['searchData'];
			$data['majors_for_department'] = $tmp['majors_for_department'];
			$data['all_departments_with_majors'] = $tmp['all_departments_with_majors'];

			// dd($data);
			if (isset($is_api)) {
				return json_encode($tmp);
			}else{
				return View('public.department.main', $data);
			}
		}
	}

	public function getCollegeMajors($slug = NULL, $major= NULL){
        if (!isset($slug)) {
            $slug = '';
        }
        $cc = new CollegeController;
        return $cc->department($slug, $major, true);
    }
}
