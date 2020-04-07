<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, DB, Session;
use Illuminate\Support\Facades\Auth;
use App\User, App\Search, App\CollegeOverviewImages, App\CollegeProgram, App\Country;
use Illuminate\Support\Facades\Cache;
use App\MetaDataCollegesByState;

class SearchController extends Controller{

    public function index($is_api = NULL, $request = NULL) {
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		$data['title'] = 'Plexuss Search';
		$data['currentPage'] = 'search';
		$src="/images/profile/default.png";
		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );
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
		if (isset($request)) {
			$input = $request;
			isset($request['type']) ? $type = $request['type'] : $type = NULL;
			isset($request['term']) ? $term = $request['term'] : $term = NULL;
			isset($request['cid']) ?  $cid  = $request['cid']  : $cid = NULL;	
		}else{
			$input = Request::all();
			$type=Request::get('type');
			$term= Request::get('term');
			$cid = Request::get('cid');

		}
		
		//$page=Request::get('page');

		// set url filter in pagination
		$querystringarray=array();
		$querystringarray=$input;
		unset($querystringarray['page']);
		$data['querystring']=$querystringarray;
		// set url filter in pagination

		//Advances Search Filter and on load search by url params
		if(isset($type) && $type!='')
		{
			$data['type']=$type;
			$data['term']=$term;
			$data['cid'] = $cid;
			if(Request::get('degree_type'))
				$data['degree_type'] = Request::get('degree_type');

			//$data['page']=$page;

			if($type=='college' || $type=='news' || $type=='default' || $type == 'students' || $type == 'majors')
			{
				$search_array=array();
				if(isset($input['type'])){$search_array['type']=$input['type'];}
				if(isset($input['term'])){$search_array['term']=$input['term'];}
				if(isset($input['cid'])){$search_array['cid']=$input['cid'];}

				// dd($input);
				if($type=='college' || $type=='majors')
				{
					if((!isset($term) || $term == '') && !isset($input['department']) && !isset($input['imajor'] )){
						$data['searchterm'] = ' all colleges';
					}

					if(isset($input['department'])){
						$search_array['department']=$input['department'];
					    $data['department'] = $input['department'];
					}
					//if else due to the fact that term takes precidence over department set -- ideally should be the same
					//will look further into it
					if($type =='majors' && isset($term) && !isset($input['department'])){
					    //get all majors list for department
					    $data['majors_for_cat'] = $this->getAllMajorsFromCat($term);
					}
					if(isset($input['department'])){
					    //get majors list for department-- happens to equal tag list
					    $data['majors_for_cat'] = $this->getAllMajorsFromCat($input['department']);
					}
					if(isset($input['imajor'])){
						$search_array['imajor'] = $input['imajor'];
					}
					if(isset($input['rmajor'])){
						$search_array['rmajor']=$input['rmajor'];
					}
					if(isset($input['school_name']))
						{$search_array['school_name']=$input['school_name'];}
					if(isset($input['city']))
						{$search_array['city']=$input['city'];}
					if(isset($input['country']))
						{$search_array['country']=$input['country'];}

					if(isset($input['state']))
						{$search_array['state']=$input['state'];}

					if(isset($input['zipcode']))
						{$search_array['zipcode']=$input['zipcode'];}

					if(isset($input['degree']))
						{$search_array['degree']=$input['degree'];}
					if(isset($input['degree_type'])){
						$search_array['degree_type']=$input['degree_type'];
						if($input['degree_type'] == 3)
							$search_array['degree']='bachelors_degree';
						if($input['degree_type'] == 4)
							$search_array['degree']='masters_degree';
					}

					if(isset($input['campus_housing']))
						{$search_array['campus_housing']=$input['campus_housing']; }

					if(isset($input['locale']))
						{$search_array['locale']=$input['locale'];}

					if(isset($input['religious_affiliation']))
						{$search_array['religious_affiliation']=$input['religious_affiliation'];}

					if(isset($input['min_reading']))
						{$search_array['min_reading']=$input['min_reading'];}

					if(isset($input['max_reading']))
						{$search_array['max_reading']=$input['max_reading'];}

					if(isset($input['min_sat_math']))
						{$search_array['min_sat_math']=$input['min_sat_math'];}

					if(isset($input['max_sat_math']))
						{$search_array['max_sat_math']=$input['max_sat_math'];}

					if(isset($input['min_act_composite']))
						{$search_array['min_act_composite']=$input['min_act_composite'];}

					if(isset($input['max_act_composite']))
						{$search_array['max_act_composite']=$input['max_act_composite'];}

					if(isset($input['miles_range_min_val']))
						{$search_array['miles_range_min_val']=$input['miles_range_min_val'];}

					if(isset($input['miles_range_max_val']))
						{$search_array['miles_range_max_val']=$input['miles_range_max_val'];}

					if(isset($input['tuition_max_val']) && $input['tuition_max_val'] != 0)
						{$search_array['tuition_max_val']=$input['tuition_max_val'];}

					if(isset($input['enrollment_min_val']))
						{$search_array['enrollment_min_val']=$input['enrollment_min_val'];}

					if(isset($input['enrollment_max_val']))
						{$search_array['enrollment_max_val']=$input['enrollment_max_val'];}

					if(isset($input['applicants_min_val']))
						{$search_array['applicants_min_val']=$input['applicants_min_val'];}

					if(isset($input['applicants_max_val']))
						{$search_array['applicants_max_val']=$input['applicants_max_val'];}

					if(isset($input['major_slug']))
						{$search_array['majors_department_slug']=$input['major_slug'];}
					//get majors tags for student filters
					$maj = $this->getMajorFiltersFromCat($input);
					$data['major_tags'] = $maj;
					//also prepop department select -- with dept_categories
					$search = new Search();
					$depts_cat = $search->getDepts();
					$data['depts_cat'] = $depts_cat;

				}
				// Actual search results and query here
				//majors uses a different search method
				if($data['type'] != 'majors'){
					$search = new Search();
					$searchData = $search->SearchData($search_array);

					if($data['type'] == 'college'){
						$data['searchData']=$searchData[1];
					}
					else{
						$data['searchData']=$searchData;
					}
				}

				if($data['type']=='default') {
					$total_college = DB::table('colleges')
					->select(array('id', DB::raw('COUNT(colleges.id) as totalresult')))
					->where( 'verified', '=', 1 )
					->where(function($query) use($term){
						$query->where( 'school_name', 'like', '%'.$term.'%' )
						      ->where('alias', 'like', '%'.$term.'%', 'OR');
					})
					->count();

					$total_news = DB::table('news_articles')
					->select(array('id', DB::raw('COUNT(news_articles.id) as totalresult')))
					->where( 'external_name', 'like', '%'.$term.'%' )
					->where('content', 'like', '%'.$term.'%', 'OR')
					->count();

					$recordcount=($total_college)+($total_news);
				}
				elseif($data['type']=='college') {
					$recordcount=$searchData[0];
				}
				elseif($data['type']=='news') {
					$total_news = DB::table('news_articles')
					->select(array('id', DB::raw('COUNT(news_articles.id) as totalresult')))
					->where( 'external_name', 'like', '%'.$term.'%' )
					->where('content', 'like', '%'.$term.'%', 'OR')
					->count();

					$recordcount=$total_news;
				}
				elseif($data['type']=='students'){

					if (!isset($data['is_organization']) || $data['is_organization'] == 0) {
		                return;
		            }

		            // echo "<pre>";
		            // print_r($data);
		            // echo "</pre>";
		            // exit();
		            $cid    = $data['org_school_id'];
		            $aor_id = isset($data['aor_id']) ? $data['aor_id'] : NULL;
		            $countval='';
		            $query = 'u.id, u.fname, u.lname, u.email, u.phone, u.profile_img_loc, rec.status, rec.user_recruit, rec.college_recruit';
		            $vh = 'IF (vh.id IS NOT NULL, "vh", NULL) as vhStat';
		            $va ='IF (va.id IS NOT NULL, "va", NULL) as vaStat';
		            $p = 'IF (p.id IS NOT NULL, "p", NULL) as pstat';

		            //TO DO or think about: can do string substitutions for user misspellings also ??

		            $qry = DB::connection('rds1')->table('users as u')
		                                         ->leftJoin('recruitment as rec', function($q) use($cid, $data){
		                                                    $q->on('u.id', '=', 'rec.user_id');
		                                                    $q->on('rec.college_id', '=', DB::raw($cid));
		                                                    if (isset($data['aor_id'])) {
		                                                        $q->on('rec.aor_id', '=', DB::raw($data['aor_id']));
		                                                    }else{
		                                                        $q->whereNull('rec.aor_id');
		                                                    }
		                                         })
		                                         ->leftJoin('recruitment_verified_hs as vh', function($q) use ($cid, $data){
		                                                    $q->on('u.id', '=', 'vh.user_id');
		                                                    $q->on('vh.college_id', '=', DB::raw($cid));
		                                                    if (isset($data['aor_id'])) {
		                                                        $q->on('vh.aor_id', '=', DB::raw($data['aor_id']));
		                                                    }else{
		                                                        $q->whereNull('vh.aor_id');
		                                                    }
		                                                    if (isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])) {
		                                                        $q->on('vh.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
		                                                    }else{
		                                                        $q->whereNull('vh.org_portal_id');
		                                                    }
		                                         })
		                                         ->leftJoin('recruitment_verified_apps as va', function($q) use ($cid, $data){
		                                                    $q->on('u.id', '=', 'va.user_id');
		                                                    $q->on('va.college_id', '=', DB::raw($cid));
		                                                    if (isset($data['aor_id'])) {
		                                                        $q->on('va.aor_id', '=', DB::raw($data['aor_id']));
		                                                    }else{
		                                                        $q->whereNull('va.aor_id');
		                                                    }
		                                                    if (isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])) {
		                                                        $q->on('va.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
		                                                    }else{
		                                                        $q->whereNull('va.org_portal_id');
		                                                    }
		                                         })
		                                         ->leftJoin('prescreened_users as p', function($q) use ($cid, $data){
		                                                    $q->on('u.id', '=', 'p.user_id');
		                                                    $q->on('p.college_id', '=', DB::raw($cid));
		                                                    if (isset($data['aor_id'])) {
		                                                        $q->on('p.aor_id', '=', DB::raw($data['aor_id']));
		                                                    }else{
		                                                        $q->whereNull('p.aor_id');
		                                                    }
		                                                    if (isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])) {
		                                                        $q->on('p.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
		                                                    }else{
		                                                        $q->whereNull('p.org_portal_id');
		                                                    }
		                                         })
		                                         ->select( DB::raw( $query ),DB::raw( $vh ),DB::raw( $va ), DB::raw($p) )
		                                         ->where(function($q) use ($term){
		                                                $q->orWhere('u.fname', 'like', '%'.$term.'%')
		                                                  ->orWhere('u.lname', 'like', '%'.$term.'%')
		                                                  ->orwhere(DB::raw("CONCAT(u.fname, ' ', u.lname)"), 'like' , '%'.$term.'%')
		                                                  ->orWhere('u.email', 'like', '%'.$term.'%');
		                                         })
		                                         ->where(function($q){
		                                                $q->orWhereNotNull('rec.status')
		                                                  ->orWhereNotNull('rec.user_recruit')
		                                                  ->orWhereNotNull('rec.college_recruit')
		                                                  ->orWhereNotNull('vh.id')
		                                                  ->orWhereNotNull('va.id')
		                                                  ->orWhereNotNull('p.id');
		                                         });
		            if (isset($data['default_organization_portal']) && !empty($data['default_organization_portal'])) {
		                $qry = $qry->leftJoin('recruitment_tags as rt', function($q) use ($cid, $data){
		                                 $q->on('rt.user_id', '=', 'u.id');
		                                 $q->on('rt.college_id', '=', 'rec.college_id');
		                                 $q->on('rt.org_portal_id', '=', DB::raw($data['default_organization_portal']->id));
		                                 $q->whereNull('rt.aor_id')
		                                   ->whereNull('rt.aor_portal_id');
		                });
		            }elseif (isset($data['aor_id'])) {
		                $qry = $qry->leftJoin('recruitment_tags as rt', function($q) use ($cid, $data){
		                                 $q->on('rt.user_id', '=', 'u.id');
		                                 $q->on('rt.college_id', '=', 'rec.college_id');
		                                 $q->on('rt.aor_id', '=', DB::raw($data['aor_id']));
		                                 $q->whereNull('rt.org_portal_id');
		                });
		            }else{
		                $qry = $qry->leftJoin('recruitment_tags as rt', function($q) use ($cid, $data){
		                                 $q->on('rt.user_id', '=', 'u.id');
		                                 $q->on('rt.college_id', '=', 'rec.college_id');
		                                 $q->on('rt.aor_id', '=', DB::raw(-1));
		                                 $q->on('rt.org_portal_id', '=', DB::raw(-1));
		                                 $q->on('rt.aor_portal_id', '=', DB::raw(-1));
		                });
		            }
		            $recordcount = $qry->distinct('u.id')
		            				   ->count();
		            //$data = $qry->where('blah',1);
		            $result = $qry->orderBy( DB::raw("CONCAT(u.fname, ' ', u.lname)") , 'ASC')
		                          ->groupBy('u.id')
		                          ->paginate(10);
					$data['searchData']  = $result;
					$data['recordcount'] = $recordcount;
				}
				elseif($data['type']=='majors'){

					$search = new Search();
					$uMajors = $this->getMyMajors();
					//if no term and user has majors chosen already, search all colleges with majors filters
					if(empty($data['term']) && empty($data['department']) && isset($uMajors) && count($uMajors) > 0 &&
					  ( !isset($input['myMajors']) ||
					  ( isset($input['myMajors']) && $input['myMajors'] == "true")) ){
						// dd($uMajors);

						$search_array['majors'] = $uMajors;

						$searchResults = $search->getCollegesWithDept($search_array);
						// dd($searchResults);
						//set data
						$data['searchData']=$searchResults[1];

						isset($searchResults[0]) ? $recordcount=$searchResults[0] : $recordcount=0;
						$data['recordcount'] = $recordcount;

						$data['searchterm'] = ' your intended majors ';//isset($uMajors[0]->name) ? $uMajors[0]->name : '';

						$majors = $this->getMajorFiltersFromCat($input);
						$data['major_tags'] = $majors;

                        $data['search_array'] = $search_array;

						return View('public.search.collegebyMajor_search', $data);
					}

					$searchResults = $search->getCollegesWithDept($search_array);

					//if no results found
					if(count($searchResults) < 1){
						$data['recordcount'] = 0;
						$data['searchterm'] = '';
						$data['SearchData'] = [];
						return View('public.search.collegebyMajor_search', $data);
					}
					$data['searchData']=$searchResults[1];
					isset($searchResults[0]) ? $recordcount=$searchResults[0] : $recordcount=0;
					isset($searchResults[2]) ? $data['searchterm'] = $searchResults[2]: $data['searchterm'] = '';
					$majors = $this->getMajorFiltersFromCat($input);
					$data['major_tags'] = $majors;


				}else{$recordcount='1';}

				$data['recordcount']=$recordcount;
			}
		}

        $data['search_array'] = $search_array;
        
        // Special case for colleges by state redirections
        if (count($search_array) == 7 || count($search_array) == 6) {
            if (isset($search_array['state']) && (!isset($search_array['country']) || $search_array['country'] == 'US') && $search_array['type'] == 'college' && $search_array['enrollment_min_val'] == 0 && $search_array['enrollment_max_val'] == 0 && $search_array['applicants_min_val'] == 0 && $search_array['applicants_max_val'] == 0) {

                $redirect_url = $this->buildCollegeByStateRedirect($search_array['state']);
                
                if (isset($is_api)) {
                	
                	$dt = array();
                	$slug = str_replace("/college/state/", "", $redirect_url);
                	$dt = $this->byState($slug,  true,  $input); 

                	return $dt;
                }
                if (isset($redirect_url)) {
                    return redirect($redirect_url);
                }
            }
        }

        if (isset($input['country'])) {
        	$country = Country::on('rds1')->where('country_code', $input['country'])->first();

        	if (isset($country)) {
        		$data['searchterm'] = "colleges in ". $country->country_name;
        	}
        }

        if (isset($is_api)) {
        	return $data;
        }

		if($data['type'] == 'college' || $data['type'] == 'majors')
			return View('public.search.collegebyMajor_search', $data);
		else
			return View('public.search.search', $data);
	}

    private function buildCollegeByStateRedirect($state) {
        $collegeByState = MetaDataCollegesByState::on('rds1')
                                                 ->where('state_name', '=', $state)
                                                 ->whereNotNull('slug')
                                                 ->select('slug')
                                                 ->first();

        if (!$collegeByState) return null;

        return '/college/state/' . $collegeByState->slug;
    }

    // This handles the single view of each state
    public function byState($slug = '',  $is_api = NULL,  $input  = NULL) {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();
        (!isset($input)) ? $input = Request::all() : NULL;

        // Default offset/page value
        $offset = 0;
        $page = 1;

        // Always take 10 at a time;
        $take = 10;

        if (isset($input['page']) && is_int((int)$input['page']) && (int)$input['page'] > 0) {
            $page = (int) $input['page'];
            $offset = (10 * $page) - 10;
        }

        $webpage_data = MetaDataCollegesByState::where('slug', '=', $slug)->first();

        if (!$webpage_data) {
            return redirect('/search?type=college&term=&country=US');
        }

        if (Auth::check()){
            $user = User::find( Auth::user()->id );
            if($user->profile_img_loc!=""){
                $src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
                $data['profile_img_loc'] = $src;
            }
        }

        $data['currentPage'] = "colleges-by-state";
        $data['slug'] = $slug;
        $data['type'] = 'state';
        $data['term'] = '';
        
        // Attempt to search
        try {
            $searchArray = [
                'type' => 'college',
                'country' => 'US',
                'state' => $webpage_data->state_name,
            ];

            $search = new Search();
            $searchData = $search->SearchData($searchArray, $take, $offset, $is_api);
            
            $data['recordcount'] = $searchData->distinct()->count('colleges.id');
            $data['depts_cat'] = $search->getDepts();

            $searchData = $searchData
                             ->groupBy('colleges.id')
                             ->take($take)
                             ->offset($offset)
                             ->get();

            $data['page'] = $page;
            $data['searchData']  = $searchData;
        } catch (\Exception $e) {
            // dd($e);
        }

        isset($searchArray) ? $data['search_array'] = $searchArray : NULL;

        $data['state_name'] = $webpage_data->state_name;

        $data['headline'] = $webpage_data->headline;

        $data['background_image'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/state/backgrounds/" . $webpage_data->background_img_name;

        $data['background_image_alt'] = $webpage_data->background_image_alt;

        $data['flag_image'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/state/flags/" . $webpage_data->flag_img_name;
        $data['flag_image_alt'] = $webpage_data->flag_image_alt;

        $data['state_content'] = $webpage_data->content;

        $data['meta_keyword'] = '';
        $data['meta_desc'] = $webpage_data->meta_description;
        $data['title'] = $webpage_data->meta_title;

        if (isset($is_api)) {
        	return $data;
        }

        return View('public.search.collegeByState_search', $data);
    }

	/**
	 *
	 *
	 *
	 */
	public function searchForApi($take, $offset){

		$retArr = array();
		$data = array();
		$input = Request::all();
		$retArr['status'] = '';

		if ($take > 50) {
			$retArr['status'] = "failed";
			$retArr['error_msg'] = "You can take 50 or less on each call.";

			return json_encode($retArr);
		}
		$type = $input['type'];

		$data['type']  = isset($input['type']) ? $input['type'] : NULL;
		$data['term']  = isset($input['term']) ? $input['term'] : NULL;
		$data['cid']  = isset($input['cid']) ? $input['cid'] : NULL;
		$degree_type  = isset($input['degree_type']) ? $input['degree_type'] : NULL;

		if($degree_type){
			$data['degree_type'] = $degree_type;
		}

		if($type=='college' || $type=='news' || $type=='default' || $type == 'majors')
		{
			$search_array=array();
			if(isset($input['type'])){$search_array['type']=$input['type'];}
			if(isset($input['term'])){$search_array['term']=$input['term'];}
			if(isset($input['cid'])){$search_array['cid']=$input['cid'];}


			// dd($input);
			if($type=='college' || $type=='majors')
			{
				if((!isset($term) || $term == '') && !isset($input['department']) && !isset($input['imajor'] )){
					$data['searchterm'] = ' all colleges';
				}

				if(isset($input['department'])){
					$search_array['department']=$input['department'];
				    $data['department'] = $input['department'];
				}

				//if else due to the fact that term takes precidence over department set -- ideally should be the same
				//will look further into it
				if($type =='majors' && isset($term) && !isset($input['department'])){
				    //get all majors list for department
				    $data['majors_for_cat'] = $this->getAllMajorsFromCat($term);
				}
				if(isset($input['department'])){
				    //get majors list for department-- happens to equal tag list
				    $data['majors_for_cat'] = $this->getAllMajorsFromCat($input['department']);
				}

				if(isset($input['imajor'])){
					$search_array['imajor'] = $input['imajor'];
				}

				if(isset($input['rmajor'])){
					$search_array['rmajor']=$input['rmajor'];
				}

				if(isset($input['school_name']))
					{$search_array['school_name']=$input['school_name'];}

				if(isset($input['city']))
					{$search_array['city']=$input['city'];}

				if(isset($input['state']))
					{$search_array['state']=$input['state'];}

				if(isset($input['zipcode']))
					{$search_array['zipcode']=$input['zipcode'];}

				if(isset($input['degree']))
					{$search_array['degree']=$input['degree'];}

				if(isset($input['degree_type'])){
					$search_array['degree_type']=$input['degree_type'];
					if($input['degree_type'] == 3)
						$search_array['degree']='bachelors_degree';
					if($input['degree_type'] == 4)
						$search_array['degree']='masters_degree';
				}

				if(isset($input['campus_housing']))
					{$search_array['campus_housing']=$input['campus_housing']; }

				if(isset($input['locale']))
					{$search_array['locale']=$input['locale'];}

				if(isset($input['religious_affiliation']))
					{$search_array['religious_affiliation']=$input['religious_affiliation'];}

				if(isset($input['min_reading']))
					{$search_array['min_reading']=$input['min_reading'];}

				if(isset($input['max_reading']))
					{$search_array['max_reading']=$input['max_reading'];}

				if(isset($input['min_sat_math']))
					{$search_array['min_sat_math']=$input['min_sat_math'];}

				if(isset($input['max_sat_math']))
					{$search_array['max_sat_math']=$input['max_sat_math'];}

				if(isset($input['min_act_composite']))
					{$search_array['min_act_composite']=$input['min_act_composite'];}

				if(isset($input['max_act_composite']))
					{$search_array['max_act_composite']=$input['max_act_composite'];}

				if(isset($input['miles_range_min_val']))
					{$search_array['miles_range_min_val']=$input['miles_range_min_val'];}

				if(isset($input['miles_range_max_val']))
					{$search_array['miles_range_max_val']=$input['miles_range_max_val'];}

				if(isset($input['tuition_max_val']) && $input['tuition_max_val'] != 0)
					{$search_array['tuition_max_val']=$input['tuition_max_val'];}

				if(isset($input['enrollment_min_val']))
					{$search_array['enrollment_min_val']=$input['enrollment_min_val'];}

				if(isset($input['enrollment_max_val']))
					{$search_array['enrollment_max_val']=$input['enrollment_max_val'];}

				if(isset($input['applicants_min_val']))
					{$search_array['applicants_min_val']=$input['applicants_min_val'];}

				if(isset($input['applicants_max_val']))
					{$search_array['applicants_max_val']=$input['applicants_max_val'];}



				//get majors tags for student filters
				$maj = $this->getMajorFiltersFromCat($input);
				$data['major_tags'] = $maj;

				//also prepop department select -- with dept_categories
				$search = new Search();
				$depts_cat = $search->getDepts();

				$data['depts_cat'] = $depts_cat;


			}

			// Actual search results and query here
			//majors uses a different search method
			if($data['type'] != 'majors'){
				$search = new Search();
				$searchData = $search->SearchData($search_array, $take, $offset);
				$retArr['count'] = $searchData->distinct()->count('colleges.id');

                $result = $searchData->take($take)
                					 ->skip($offset)
                					 ->groupBy('colleges.id')
                					 ->get();

                $data['searchData'] = $result;
			}

			if($data['type']=='default') {
				$total_college = DB::table('colleges')
				->select(array('id', DB::raw('COUNT(colleges.id) as totalresult')))
				->where( 'verified', '=', 1 )
				->where(function($query) use($term){
					$query->where( 'school_name', 'like', '%'.$term.'%' )
					      ->where('alias', 'like', '%'.$term.'%', 'OR');
				})
				->count();

				$total_news = DB::table('news_articles')
				->select(array('id', DB::raw('COUNT(news_articles.id) as totalresult')))
				->where( 'external_name', 'like', '%'.$term.'%' )
				->where('content', 'like', '%'.$term.'%', 'OR')
				->count();

				$recordcount=($total_college)+($total_news);

			}
			elseif($data['type']=='news') {
				$total_news = DB::table('news_articles')
				->select(array('id', DB::raw('COUNT(news_articles.id) as totalresult')))
				->where( 'external_name', 'like', '%'.$term.'%' )
				->where('content', 'like', '%'.$term.'%', 'OR')
				->count();

				$recordcount=$total_news;
			}
			elseif($data['type']=='majors'){
				$search = new Search();
				$uMajors = $this->getMyMajors();

				//if no term and user has majors chosen already, search all colleges with majors filters
				if(empty($data['term']) && empty($data['department']) && isset($uMajors) && count($uMajors) > 0 &&
				  ( !isset($input['myMajors']) ||
				  ( isset($input['myMajors']) && $input['myMajors'] == "true")) ){
					// dd($uMajors);

					$search_array['majors'] = $uMajors;

					$searchResults = $search->getCollegesWithDept($search_array);
					// dd($searchResults);

					//set data
					$data['searchData']=$searchResults[1];

					isset($searchResults[0]) ? $recordcount=$searchResults[0] : $recordcount=0;
					$data['recordcount'] = $recordcount;

					$data['searchterm'] = ' your intended majors ';//isset($uMajors[0]->name) ? $uMajors[0]->name : '';

					$majors = $this->getMajorFiltersFromCat($input);
					$data['major_tags'] = $majors;
					return View('public.search.collegebyMajor_search', $data);
				}

				$searchResults = $search->getCollegesWithDept($search_array);

				//if no results found
				if(count($searchResults) < 1){
					$data['recordcount'] = 0;
					$data['searchterm'] = '';
					$data['SearchData'] = [];
					return View('public.search.collegebyMajor_search', $data);
				}

				$data['searchData']=$searchResults[1];

				isset($searchResults[0]) ? $recordcount=$searchResults[0] : $recordcount=0;
				isset($searchResults[2]) ? $data['searchterm'] = $searchResults[2]: $data['searchterm'] = '';

				$majors = $this->getMajorFiltersFromCat($input);
				$data['major_tags'] = $majors;


			}else{$recordcount='1';}

			$data['recordcount']=$recordcount;
		}

		$coi = new CollegeOverviewImages;
		$cp  = new CollegeProgram;
		$retArr['data'] = array();
		foreach ($result as $k) {

			if (Cache::has(env('ENVIRONMENT') .'_'.'_searchAPI_collegeOverviewImages_'. $k->id)) {
				$k->college_imgs = Cache::get(env('ENVIRONMENT') .'_'.'_searchAPI_collegeOverviewImages_'. $k->id);
			}else{
				$k->college_imgs = $coi->getImagesByCollegeId($k->id);
				Cache::add(env('ENVIRONMENT') .'_'.'_searchAPI_collegeOverviewImages_'. $k->id, $k->college_imgs, 4480);
			}

			if (Cache::has(env('ENVIRONMENT') .'_'.'_searchAPI_department_major_'. $k->id)) {
				$k->department_major = Cache::get(env('ENVIRONMENT') .'_'.'_searchAPI_department_major_'. $k->id);
			}else{
				$k->department_major = $cp->getDepAndMajorWithCollegeId($k->id);
				Cache::add(env('ENVIRONMENT') .'_'.'_searchAPI_department_major_'. $k->id, $k->department_major, 4480);
			}

			$retArr['data'][] = $k;
		}
		$retArr['status'] = 'success';
		return json_encode($retArr);
	}

	/***********************************
	* use to dereference the session variable,
	* which is returned as an object
	* because we do not want to have it update its contents , only read
	* and use what is read
	* converts the session var to arrays
	****************************************/
	private function getMyMajors(){

		$majorsObj = Session::get('user_majors');
		if(isset($majorsObj) && $majorsObj != ''){
//			$tmp = (clone $majorsObj)->toArray();
            $tmp = json_decode($majorsObj);

			$res = [];
			foreach($tmp as $t){
				$res[] = clone $t;
			}


			// $res =  $tmp;
			// dd($majorsObj);

			return $res;
		}
		return null;
	}

	/******************************************************
	*  helper function
	*  gets majors from a category
	*  or returns majors chosen by user, if term in empty
	****************************************************/
	public function getAllMajorsFromCatAjax() {

		$input = Request::all();

		//if category not set and majors have been chosen by user, return those majors
        $majors = array();
        if(!isset($input['cat'])){
			return [];
        }

        //perform catagory departments, majors search
		$search = new Search;
		$majors = $search->getMajorsFromCat($input['cat']);


		return $majors;

	}

	/******************************************************
	*  helper function
	*  gets majors from a category
	*  or returns majors chosen by user, if term in empty
	****************************************************/
	public function getAllMajorsFromCat($cat) {

		//if category not set and majors have been chosen by user, return those majors
        $majors = array();
        if(!isset($cat)){
			return [];
        }

        //perform catagory departments, majors search
		$search = new Search;
		$majors = $search->getMajorsFromCat($cat);


		return $majors;

	}



	/******************************************************
	*  helper function
	*  gets majors from a category
	*  or returns majors chosen by user, if term in empty
	****************************************************/
	public function getMajorFiltersFromCatAjax () {

		$input = Request::all();

		//if category not set and majors have been chosen by user, return those majors
        $major_l = $this->getMyMajors();
        $majors = array();
        if(!isset($input['cat']) && isset($major_l) ){
            $majors =  $major_l;
            if(isset($input['rmajor'])){
				$majors = $this->removeMajors($majors, $input['rmajor']);
			}
			return $majors;
        }else if(!isset($input['cat']) && !isset($major_l)){
        	return [];
        }

        //perform catagory departments, majors search
		$search = new Search;
		if(isset($input['imajor'])){
			$majors = $search->getMajorsFromCat($input['cat'], $input['imajor']);
		}
		else{
			$majors = $search->getMajorsFromCat($input['cat']);
		}

		//if need to remove some majors, remove
		if(isset($input['rmajor'])){
			$majors = $this->removeMajors($majors, $input['rmajor']);
		}

		return $majors;

	}


	/******************************************************
	*  helper function
	*  gets major filters for user info
	****************************************************/
	private function getMajorFiltersFromCat ($info) {

		//if category not set and majors have been chosen by user, return those majors
        $major_l = $this->getMyMajors();

        if(!isset($info['term']) && !isset($info['department']) && $info['type'] == 'majors' &&
		  ( !isset($info['myMajors']) ||
		  ( isset($info['myMajors']) && $info['myMajors'] == "true")) ){
            $majors =  $major_l;

            if(isset($info['rmajor'])){
				$majors = $this->removeMajors($majors, $info['rmajor']);
			}
			return $majors;
        }

        //set category depending on source
        $cat = null;
        if(isset($info['cat']) ){
        	$cat = $info['cat'];
        }else if(isset($info['department']) ){
        	$cat = $info['department'];
        }else if(isset($info['term']) && $info['type'] == 'majors'){
        	$cat = $info['term'];
        }

        //perform catagory departments, majors search
		$search = new Search;
		if(isset($info['imajor'])){
			$majors = $search->getMajorsFromCat($cat, $info['imajor']);
		}
		else{
			$majors = $search->getMajorsFromCat($cat);
		}

		//if need to remove some majors, remove
		if(isset($info['rmajor'])){
			$majors = $this->removeMajors($majors, $info['rmajor']);
		}

		return $majors;

	}

	/************************
	* helper function to remove majors
	* can later make more performant remove alg
	***********************************/
	private function removeMajors($majors , $rmajor){

		if(isset($rmajor)){
			$copy = $majors;
			for($i=0 ; $i < count($majors); $i++ ){
				for($k=0; $k < count($rmajor); $k++){

					if( isset($majors[$i]) && $rmajor[$k] == $majors[$i]->id){

							unset($copy[$i]);
					}
				}
			}

			//inner objects causes unset to convert array to an object
			//want to send back an array...
			$result = [];
			$i = 0;
			foreach($copy as $major){
				$result[$i] = $major;
				$i++;
			}
			return $result;
		}
		return $majors;
	}


	/*****************************************
	*  handles getting colleges on load  (Majors)-
	*  -with majors as type
	*  -with dept as term
	*  -with nothing as term
	*  -can have filters in params suchs as school_name, city
	*		(ex user refreshes or perform adv college search on searching for majors page)
	*  -alos handles the all.bachelors,masters filters for majors page
	***********************************************/
	public function getCollegesWithDept(){
		$input = Request::all();

		$search = new Search;
		$results = $search->getCollegesWithDept($input);
		return $results;
	}



}
