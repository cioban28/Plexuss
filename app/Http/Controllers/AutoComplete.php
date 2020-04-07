<?php

namespace App\Http\Controllers;

use Request, DB, Validator;
use App\College, App\Profession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Country;
use Illuminate\Support\Facades\Cache;

class AutoComplete extends Controller
{
    //
    	/**
	 * Return high school or college names.
	 *
	 * @return Response
	 */
	public function school_autocomplete() {
		$usrLatLang = array();
		$term = strtolower( Request::get( 'term' ) );
		$zipcode = strtolower( Request::get( 'zipcode' ) );
		$schoolTable = strtolower( Request::get( 'type' ) );
		$unverified = strtolower( Request::get('unverified') );

		if( $schoolTable == 'college'){
			$schoolTable = 'colleges';
		} else {
			$schoolTable = 'high_schools';
		}

		if( $zipcode) {
			//we need to clean the $zipcode input since it WILL be used for DB call.
			$ziprules = Validator::make(
				array('zipcode' => $zipcode), 
				array('zipcode' => 'integer|required|min:9999')
			);
			
			if( $ziprules->passes() ) {
	        	# code for validation success!
	        	$usrLatLang  = $this->getUserLocationByZip($zipcode);
			} else { 
	        	# code for validation failure
	        	$usrLatLang  = $this->getUserLocationByIp();

			}

			if (isset($usrLatLang[0]) && !empty($usrLatLang[0])) {
				$query = 'slug, school_name as value, city, state, ( 3959 * acos( cos( radians('.$usrLatLang[0].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$usrLatLang[1].') ) + sin( radians('.$usrLatLang[0].') ) * sin(radians(latitude)) ) ) AS distance ';
				$orderBy = 'distance';
				$orderType = 'asc';
			}else{
				$query = 'slug, school_name as value, city, state ';
				$orderBy = 'value';
				$orderType = 'asc';
			}
			
		} else {
			$query = 'slug, school_name as value, city, state ';
			$orderBy = 'value';
			$orderType = 'asc';
		}
		

		// We need the user id to return the user's custom inputted/unverified schools
		$user_id = Auth::id();

		/***********************************************************************
		 *========================DB QUERY STARTS HERE==========================
		 ***********************************************************************/
		// Queries are built piecemeal, not as different blocks
		// High School
		if($schoolTable == 'high_schools') {

			$school_list = DB::connection('rds1')->table( 'high_schools' )
			->select( DB::raw( 'id, '.$query ) )
			->where( 'school_name', 'like', '%'.$term.'%' )
				->where( 'verified', '=', 1 );
		}

		// College
		if($schoolTable == 'colleges') {

			$school_list = DB::connection('rds1')->table( 'colleges' )
			->select( DB::raw( 'colleges.id, '.$query ) )
			->where('school_name', 'like', '%'.$term.'%')
				->where( 'verified', '=', 1 )
			->orWhere('alias', 'like', '%'.$term.'%')
				->where( 'verified', '=', 1 );
		}

		// Allow unverified results to be returned if switched
		if( $unverified == 1 ){
			$school_list = $school_list->orWhere('school_name', 'like', '%'.$term.'%')
				->where( 'user_id_submitted', '=',  $user_id );

			// Search aliases (only colleges) for unverified results
			if( $schoolTable == 'colleges' ){
				$school_list = $school_list->where('alias', 'like', '%'.$term.'%', 'OR');
			}
		}

		// Finish query and GET
		if($schoolTable == 'high_schools') {
			$school_list = $school_list->orderby( 'verified', 'asc' )
				->orderby( $orderBy, $orderType )
				->LIMIT(12)
				->get();
		}else{
			$school_list = $school_list->leftJoin('colleges_ranking', 'colleges_ranking.college_id', '=', 'colleges.id')
				->orderby(DB::raw('`colleges_ranking`.plexuss IS NULL,`colleges_ranking`.`plexuss`'))
				->LIMIT(12)
				->get();
			
		}
			/***********************************************************************
		 *===============================END DB QUERY==========================
		 ***********************************************************************/


		$return_array = array();

		// Adds a city name 
		foreach ( $school_list as $school ) {

			$city = $school->city;
			$state = $school->state;
			// Adds ' - [city name]' to the end of the label and value
			// Also Adds ', [state abv.]' to the end of label and value
			$label = isset( $city ) && $city != "" ? $school->value . " - " . $city : $school->value;
			$value = isset( $city ) && $city != "" ? $school->value . " - " . $city : $school->value;
				isset( $state ) && $state != "" ? $label .= ", " . $state : $label = $label;
				isset( $state ) && $state != "" ? $value .= ", " . $state : $value = $value;
			$return_array[] = array(
				'id' => $school->id,
				'label' => $label,
				'value' => $value,
				'city' => $school->city,
				'state' => $school->state,
				'slug' => $school->slug
			);
		}
		
		return response()->json( $return_array ); 
	}


	public function getUserLocationByZip($zipcode) {
		$ret = array();
		$data = DB::connection('rds1')->table('zip_codes')->where('ZipCode', '=', $zipcode)->first();

		//We need to check again to see if the zip code was in our database! If not we return back to IP look up.
		if(!$data){
			return $this->getUserLocationByIp();
		}

		$ret[] = $data->Latitude;
		$ret[] = $data->Longitude;

		return $ret;
	}


	public function getUserLocationByIp(){
		$ret = array();
		$ip_address = $_SERVER['REMOTE_ADDR'];

		$privateIP = $this->checkForPrivateIP($ip_address);
		


		//If remote IP fails we default to office IP.
		if($ip_address == '::1' || $privateIP) {
			$ip_address = '50.0.50.17';
		}

		//The better ip locator API
		$url = "http://freegeoip.net/json/".$ip_address;
		$headers = array('Accept' => 'application/json');
		$request = Requests::get($url, $headers);
		$dec = json_decode($request->body);

		//if freegeoip failed, then go to this sucky API
		if (isset($dec->latitude)) {
			
			if ($dec->latitude =="" || $dec->longitude =="") {
				$url ='http://api.ipinfodb.com/v3/ip-city/?key=ca7f2dc6b3e06662e0359d2c378dc1cc9153afeea793c30fd1084fa85e9ea509&ip='.$ip_address.'&format=json';
				$request = Requests::get($url, $headers);
				$dec = json_decode($request->body);
				// if the sucky API failed, then just set lat, and long to walnut creek
				if ($dec->latitude =="" || $dec->longitude =="") {
					$ret[] = "37.8916";
					$ret[] = "-122.0381";
				}else{
					$ret[] = $dec->latitude;
					$ret[] = $dec->longitude;
				}
			}
		}else{
			$iplookup = $this->iplookup();
			$ret[] = $iplookup['latitude'];
			$ret[] = $iplookup['longitude'];

		}
		
		
		return $ret;
	}

	public function checkForPrivateIP ($ip) {
	    $reserved_ips = array( // not an exhaustive list
	    '167772160'  => 184549375,  /*    10.0.0.0 -  10.255.255.255 */
	    '3232235520' => 3232301055, /* 192.168.0.0 - 192.168.255.255 */
	    '2130706432' => 2147483647, /*   127.0.0.0 - 127.255.255.255 */
	    '2851995648' => 2852061183, /* 169.254.0.0 - 169.254.255.255 */
	    '2886729728' => 2887778303, /*  172.16.0.0 -  172.31.255.255 */
	    '3758096384' => 4026531839, /*   224.0.0.0 - 239.255.255.255 */
	    );

    	$ip_long = sprintf('%u', ip2long($ip));

	    foreach ($reserved_ips as $ip_start => $ip_end)
	    {
	        if (($ip_long >= $ip_start) && ($ip_long <= $ip_end))
	        {
	            return TRUE;
	        }
	    }

    	return FALSE;
	}
	
	public function getBattleAutocomplete($type=false) {
		
		$data=array();
		$return_array = array();
		
	
		$type = strtolower( Request::get( 'type' ) );
		$term = strtolower( Request::get( 'term' ) );
		
		if($type == 'college') {
		
		$query = 'id, school_name as value, city, state, logo_url, slug';
		$orderBy = 'value';
		$orderType = 'asc';
	
		$data = DB::connection('rds1')->table( 'colleges' )
		->select( DB::raw( $query ) )
		->where( 'school_name', 'like', '%'.$term.'%' )
		->where('alias', 'like', '%'.$term.'%', 'OR')
		->where('verified', 1)
		->orderby( $orderBy, $orderType )
		->LIMIT(8)
		->get();
		}
	
		
		$return_array = array();
		$logo_img='';
		foreach ( $data as $k => $v ) {
			
		if($v->logo_url!='')
		{
			$logo_img='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$v->logo_url.'';				
		}
		else
		{
			$logo_img='images/no_photo.jpg';				
		}
		
		$return_array[] = array(
		'id' => $v->id,
		'label' => $v->value,
		'value' => $v->value,
		'image' => $logo_img,
		'slug' => $v->slug,
		'state' => $v->city . ", " .$v->state  ,		
		);
		}
		return response()->json( $return_array ); 
	}
	
	public function getTopSearchAutocomplete($type=false) 
	{
		
		$data=array();
		$return_array = array();
		
		
		$type = strtolower( Request::get( 'type' ) );
		$term = strtolower( Request::get( 'term' ) );
		$cid = strtolower( Request::get( 'cid' ) );

		// if (Cache::has( env('ENVIRONMENT') .'_'. 'getTopSearchAutocomplete_type_'.$type.'_term_'.$term.'_cid_'.$cid)) {
		// 	$return_array = Cache::get( env('ENVIRONMENT') .'_'. 'getTopSearchAutocomplete_type_'.$type.'_term_'.$term.'_cid_'.$cid);

		// 	Cache::put(env('ENVIRONMENT') .'_'. 'getTopSearchAutocomplete_type_'.$type.'_term_'.$term.'_cid_'.$cid, $return_array, 720);
			
		// 	return response()->json( $return_array ); 
		// }

		//echo "here";
		//dd(Request::all());
		// College ,Ranking or News Top Search
		if($type == 'default'){
			
			/**************** college ****************/			
			$query = 'colleges.id, slug, school_name as value, city, state, logo_url, "college" as category';
			$orderBy = 'value';
			$orderType = 'asc';
		
			$total_college = DB::connection('rds1')->table('colleges')
			->select(array('id', DB::raw('COUNT(colleges.id) as totalresult')))
			->where( 'verified', '=', 1 )
			->where(function($query) use($term){
				$query->where( 'school_name', 'like', '%'.$term.'%' )
				      ->where('alias', 'like', '%'.$term.'%', 'OR');
			})
			->count();
		
			$college_data = DB::connection('rds1')->table( 'colleges' )
			->select( DB::raw( $query ) )
			->where( 'verified', '=', 1 )
			->where(function($query) use($term){
				$query->where( 'school_name', 'like', '%'.$term.'%' )
				      ->where('alias', 'like', '%'.$term.'%', 'OR');
			})
			->leftJoin('colleges_ranking', 'colleges_ranking.college_id', '=', 'colleges.id')
			->orderby(DB::raw('`colleges_ranking`.plexuss IS NULL,`colleges_ranking`.`plexuss`'))
			->orderBy('colleges.in_our_network', 'desc')
			->LIMIT(3)
			->get();		
			//print_r($college_data);
			//exit();
			/**************** college ****************/			
			
			/**************** news ****************/			
			$query2 = 'id, slug, external_name as value, content,img_sm, "news" as category';
			$orderBy2 = 'value';
			$orderType2 = 'asc';
			
			$total_news = DB::connection('rds1')->table('news_articles')
			->select(array('id', DB::raw('COUNT(news_articles.id) as totalresult')))
			->where( 'external_name', 'like', '%'.$term.'%' )
			->where('content', 'like', '%'.$term.'%', 'OR')
			->count();
			
			$news_data= DB::connection('rds1')->table( 'news_articles' )
			->select( DB::raw( $query2 ) )
			->where( 'external_name', 'like', '%'.$term.'%' )
			->where('content', 'like', '%'.$term.'%', 'OR')
			->orderby( $orderBy2, $orderType2 )
			->LIMIT(3)
			->get();
			 	
			/**************** news ****************/
			
			// $data=array_merge_recursive($college_data,$news_data);
			$data = $college_data->merge($news_data);
			
			$return_array = array();
			$logo='';$desc='';$lable='';
			$count=$total_college+$total_news;
			foreach ( $data as $k => $v ) {

				if($v->category=='college'){	
					$logo='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$v->logo_url.'';				
					$desc=$v->city .', '. $v->state;
					$lable='<a href="/college/'.$v->id.'" target="_blank">'.$v->value.'</a>';	
					$type_val='college';		
					
				}

				if($v->category=='news'){	
					$logo='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/'.$v->img_sm.'';				
					$desc=substr(strip_tags($v->content),0,40);		
					$lable='<a href="/news/'.$v->id.'">'.$v->value.'</a>';	
				    $type_val='news';			
				}else{

					if($v->logo_url!=''){
						{$logo='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$v->logo_url.'';}
					}else{
						{$logo='/images/no_photo.jpg';}
					}
				}
					
			
				$return_array[] = array(
				'id' => $v->id,
				//'label' =>$lable,
				'label' =>$v->value,
				'value' => $v->value,
				'term' => $term,
				'type' => $type_val,
				'searchtype' => 'default',
				'count' => $count,
				'image' => $logo,
				'desc' =>  $desc,
				'category' =>$v->category,
				'slug' => $v->slug, 
				);			
			}			
		}
		
		// College Top Search		
		if($type == 'college') {
		  $viewDataController = new ViewDataController();
    	  $dt = $viewDataController->buildData(true);
	      
	      $countval='';
	      $query = 'colleges.id, colleges.school_name as value, colleges.city, colleges.state, colleges.logo_url, r.id as already_selected, colleges_ranking.plexuss as rank, ca.application_fee_undergrad';
	      $orderBy = 'value';
	      $orderType = 'asc';

	      $data = DB::connection('rds1')->table( 'colleges' )
	                                    ->leftJoin('colleges_ranking', 'colleges_ranking.college_id', '=', 'colleges.id')
	                                    ->leftjoin('recruitment as r', function($q) use ($dt){
	                                              $q->where('r.user_id', '=', DB::raw($dt['user_id']))
	                                                ->where('r.status', '=', DB::raw(1))
	                                                ->where('r.user_recruit', '=', DB::raw(1))
	                                                ->where('colleges.id', '=', 'r.college_id');
	                                    })
	                                    ->leftJoin('colleges_admissions as ca', 'ca.college_id', '=', 'colleges.id')


	                                    ->where( 'colleges.verified', '=', 1 )
	                                    ->where( 'colleges.school_name', 'like', '%'.$term.'%' )
	                                    ->orwhere('colleges.alias', 'like', '%'.$term.'%');

	      $total_college = $data;
	      $total_college = $total_college->count();

	      $data = $data->orderby(DB::raw('`colleges_ranking`.plexuss IS NULL,`colleges_ranking`.`plexuss`'))
	                    ->select( DB::raw( $query ) )
	                    ->LIMIT(5)
	                    ->get();

	      $return_array = array();

	      foreach ( $data as $k => $v ) {

	        $logo='';
	        if($v->logo_url!=''){
	          $logo='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$v->logo_url.'';
	        }else{
	          $logo='/images/no_photo.jpg';
	        }

	        $slug = strtolower($v->value);
	        $slug = str_replace(' ', '-', $slug);
	        isset($v->already_selected) ? $already_selected = true : $already_selected = false;
	        $return_array[] = array(
	                                'id' => $v->id,
	                                'slug' => $slug,
	                                'term' => $term,
	                                'type' => $type,
	                                'searchtype' => 'college',
	                                'count' => $total_college,
	                                'label' => $v->value,
	                                'value' => $v->value,
	                                'image' => $logo,
	                                'desc' =>  $v->city .', '. $v->state,

	                                'city' => $v->city,
	                                'state' => $v->state,
	                                'rank' => $v->rank,
	                                'application_fee' => $v->application_fee_undergrad,

	                                'already_selected' => $already_selected,

	        );
	      }
	    }		
		
		// Raning Top Search		
		if($type == 'ranking'){	
			
			$data['0']['id']='1';
			$data['0']['image']='ranking_icon.png';
			$data['0']['label']='Top Grad School Programs';
			$data['0']['desc']='Georgia Tech comes in at #3...';
			
			$data['1']['id']='2';
			$data['1']['image']='ranking_icon.png';
			$data['1']['label']='Top Grad School Programs';
			$data['1']['desc']='Georgia Tech comes in at #3...';
			
			$data['2']['id']='3';
			$data['2']['image']='ranking_icon.png';
			$data['2']['label']='Best Campus Life';	
			$data['2']['desc']='Georgia Tech comes in at #3...';	
			
			$return_array = array();
			
			foreach ( $data as $k => $v ) {
			
			$return_array[] = array(
			'id' => $v['id'],
			'term' => $term,
			'type' => $type,
			'serachtype' => 'ranking',
			'label' => $v['label'],
			'value' => $v['label'],
			'image' => $v['image'],
			'desc' => $v['desc'],
			);
			}			
		}
		
		// New Top Search		
		if($type == 'news'){	
			$total_news = DB::connection('rds1')->table('news_articles')
			->select(array('id', DB::raw('COUNT(news_articles.id) as totalresult')))
			->where( 'external_name', 'like', '%'.$term.'%' )
			->where('content', 'like', '%'.$term.'%', 'OR')
			->count();
			
			$query2 = 'id, external_name as value, content,img_sm';
			$orderBy2 = 'value';
			$orderType2 = 'asc';
			
			$data= DB::connection('rds1')->table( 'news_articles' )
			->select( DB::raw( $query2 ) )
			->where( 'external_name', 'like', '%'.$term.'%' )
			->where('content', 'like', '%'.$term.'%', 'OR')
			->orderby( $orderBy2, $orderType2 )
			->LIMIT(5)
			->get();
				
			$return_array = array();$logo='';				
			foreach ( $data as $k => $v ) {
				
			
			if($v->img_sm!='')
			{$logo='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/'.$v->img_sm.'';}
			else
			{$logo='/images/no_photo.jpg';}
			
				$return_array[] = array(
				'id' => $v->id,
				'label' =>'<a href="/news/'.$v->id.'">'.$v->value.'</a>',
				'value' => $v->value,
				'term' => $term,
				'type' => $type,
				'searchtype' => 'news',
				'count' => $total_news,
				'image' => $logo,
				'desc' =>  substr(strip_tags($v->content),0,40),
				);
			}
		}
	
		// Student Top Search
		if($type == 'students'){
			
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);

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
										 })
										 ->groupBy('u.id');

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

			$total_students = $qry->count();

			$qry = $qry->limit(5);

			//$data = $qry->where('blah',1);

			$data = $qry->orderBy( DB::raw("CONCAT(u.fname, ' ', u.lname)") , 'ASC')
						->get();

			
			$return_array = array();
			
			foreach ( $data as $k => $v ) {
				$logo='';	
				if($v->profile_img_loc!='')
					{$logo='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$v->profile_img_loc.'';}
				else
					{$logo='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';}
				



				//get which tab the student is in 'inquiries', 'handshakes'
				//$tab = 'inquiries', recommened, pending, handshakes, prescreen, verified handshakes, verified applications, removed, rejected
				//type: /admin/..: inquiries, recommendations, pending, approved, verifiedHS, prescreened, verifiedApp, removed, rejected
				$tab = '';


				// Inqueries
				if($v->vhStat == '' && $v->vaStat == '' && $v->pstat == ''){
					
					if($v->status == 1 && $v->user_recruit == 1 && $v->college_recruit == 0){
						$tab = 'Inquiries';
						$type = 'inquiries';
					}
					else if($v->status == 1 && $v->user_recruit == 0 && $v->college_recruit == 1){
						$tab = 'Pending';
						$type = 'inquiries/pending';
					}
					else if($v->status == 1 && $v->user_recruit == 1 && $v->college_recruit == 1){
						$tab= 'Handshakes';
						$type = 'inquiries/approved';
					}
					else if($v->status == 0 && $v->user_recruit < 9 && $v->college_recruit < 9){
						$tab = 'Removed';
						$type = 'inquiries/removed';
					}
					else if($v->status == 1 && $v->user_recruit == 1 && $v->college_recruit == -1){
						$tab = 'Rejected';
						$type=  'inquiries/rejected';
					}

				}else{

					if($v->vhStat == 'vh'){
						$tab = 'Verified Handshakes';
						$type= 'inquiries/verifiedHs';
					}else if($v->vaStat == 'va'){
						$tab = 'Verified Application';
						$type= 'inquiries/verifiedApp';
					}else if($v->pstat == 'p'){
						$tab = 'Prescreened';
						$type = 'inquiries/prescreened';
					}
				}

				// if the user doesn't exists in recruitment table, add him to handshake there	
				// if ($type == 'inquiries/verifiedApp' || $type == 'inquiries/prescreened') {
				// 	$rec = Recruitment::on('rds1')->where('user_id', $v->id)
				// 					  ->where('college_id', $cid)
				// 					  ->where('aor_id', $aor_id)
				// 					  ->first();
				// 	dd($rec);

				// 	if (!isset($rec)) {
				// 		$rec = new Recruitment;

				// 	}
				// }

				$return_array[] = array(
				'id' => $v->id,
				'term' => $term,
				'type' => $type.'?uid='.Crypt::encrypt($v->id),
				'tab' => $tab,
				'cid' => $cid,
				'searchtype' => 'students',
				'count' => $total_students,
				'fname' => $v->fname,
				'lname' => $v->lname,
				'phone' => $v->phone,
				'email' => $v->email,
				'value' => $v->fname .' '. $v->lname,
				'image' => $logo
				);
			}//end foreach $data
		}//end if students
		
		Cache::put(env('ENVIRONMENT') .'_'. 'getTopSearchAutocomplete_type_'.$type.'_term_'.$term.'_cid_'.$cid, $return_array, 720);

		return response()->json( $return_array ); 
	}
	
	
	public function getSelectBoxVal() {
		$ID =Request::get('ID');
		$filterby =Request::get('filterby');
		
		$OptionState="";
		if( $ID=='country' ) {
			$country = new Country;
			// $data = $country->getCountriesWithNameId();

            $data = $country->getAvailableCollegeCountriesWithNameId();
			// print_r($data);exit;
		}
		if( $ID=='state' ) {
			$data = College::select('long_state','id')->where( 'verified', '=', 1 )->whereNotNull('state')->whereNotNull('long_state')->whereNotIn('state',[''])->whereNotIn('long_state',[''])->groupBy('long_state')->get()->toArray();
			//print_r($data);exit;
		}
		if( $ID=='city' ) {
			$data= College::select('city','id','long_state')->where('long_state', '=',$filterby)->where( 'verified', '=', 1 )->groupBy('city')->get()->toArray();
		}
		if( $ID=='locale' ) {
			$data = College::select('locale','id')->where( 'verified', '=', 1 )->groupBy('locale')->get()->toArray();

			//Checks for a null, which would show up on the dropdown, and if found, unsets it
			//May want to use something with this functionality for all of these later
			foreach($data as $k => $v){
				if(is_null($v['locale'])){
					unset($data[$k]);
					//echo 'found!';
					//echo 'data key: ' . $k;
					//var_dump($k);
					//var_dump($v);
				}
			}
		}
		if( $ID=='religious_affiliation' ) {
			$data = College::on('rds1')->select('religious_affiliation','id')->where( 'verified', '=', 1 )->groupBy('religious_affiliation')->get()->toArray();
		}
		
		
		if( count($data)>0 ) {
			$OptionState = '';
			foreach($data as $key=>$value) {
				if( $ID=='country' ) {
					$OptionState.=$value['abbr']."|||".$value['name']."***";
				}
				if( $ID=='city' ) {
					$OptionState.=$value['city']."|||".$value['city']."***";
				}
				if( $ID=='state' ) {
					$OptionState.=$value['long_state']."|||".$value['long_state']."***";
				}
				if( $ID=='locale' ) {
					$OptionState.=$value['locale']."|||".$value['locale']."***";
				}
				if( $ID=='religious_affiliation' ) {
					$OptionState.=$value['religious_affiliation']."|||".$value['religious_affiliation']."***";
				}
			}
			$OptionState = trim($OptionState);
			$OptionState=trim($OptionState,'*');
		}
		
		echo $OptionState;
		exit();
	}
	
	
	public function getslugAutoCompleteData() {

		$usrLatLang = array();
		$term = strtolower( Request::get( 'term' ) );
		$zipcode = strtolower( Request::get( 'zipcode' ) );
		$schoolTable = strtolower( Request::get( 'type' ) );
		$UrlSlugs = Request::get( 'urlslug' );
	
		$schoolSlugArray = array_map('trim', explode(',', $UrlSlugs));
		
		/*$data = DB::table($schoolTable)
		->select( DB::raw( $query ) )			
		->where('school_name', 'like', $term.'%')
		->whereNotIn('slug', $schoolSlugArray)
		->where('alias', 'like', $term.'%', 'OR')
		->orderby( $orderBy, $orderType )
		->LIMIT(12)
		->get();*/
		
		$query = 'colleges.id as id, colleges.id as college_id, slug, colleges.school_name as value, city, state, colleges_ranking.plexuss as rank, ca.application_fee_undergrad';
		$orderBy = 'value';
		$orderType = 'asc';
		
		$result=DB::connection('rds1')->table('colleges')			
									  ->select( DB::raw( $query ), DB::raw('IF(LENGTH(colleges.logo_url), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", colleges.logo_url) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default.png") as logo_url'));			
		
		if($schoolSlugArray!='')
		{
			$result = $result->whereNotIn('slug', $schoolSlugArray);
		}

		$result = $result->where( 'verified', '=', 1 )
						 ->where('colleges.school_name', 'like', '%'.$term.'%')
						 ->orwhere('alias', 'like', '%'.$term.'%')
						 ->leftJoin('colleges_ranking', 'colleges_ranking.college_id', '=', 'colleges.id')
										 ->leftJoin('colleges_admissions as ca', 'ca.college_id', '=', 'colleges.id')
										 ->orderby(DB::raw('`colleges_ranking`.plexuss IS NULL,`colleges_ranking`.`plexuss`'))
						 ->take(12)	
						 ->get();   
		
		//print_r($result);
		//exit();
		$data=  $result;

		foreach ( $data as $k => $v ) {
			if ($v->application_fee_undergrad == 0) {
				$v->application_fee_undergrad = 'N/A';
			}
			$return_array[] = array(
				'id' => $v->id,
				'label' => $v->value . " - " . $v->city,
				'value' => $v->value . " - " . $v->city,
				'city' => $v->city,
				'state' => $v->state,
				'slug' => $v->slug,
				'rank' => $v->rank,
				'application_fee' => $v->application_fee_undergrad,
				'logo_url' => $v->logo_url
			);
		}
		
		$return_array = (isset($return_array) ? $return_array : array());
		return response()->json( $return_array ); 
	}

	/* AJAX Autocomplete controller to get majors for objective section of
	 * the profile page.
	 * @param		term				string			the user's search term passed as a get variable
	 * @return							json object		a json object with a list of majors relevant
	 * 													to the user's search
	 * @note		majors_arr			array			a auto (zero) indexed, single level array
	 * 													required to display autocomplete results list correctly
	 */
	public function getMajors(){
		$input = Request::all();
		$filter = array(
			'term' => array(
				'regex:/^[0-9a-zA-Z\- ]+$/'
			)
		);
		$validator = Validator::make( $input, $filter );

		if ($validator->fails()) {
			$messages = $validator->messages();
			return $messages;
			exit();
		}

		$term = Request::get('term');
		$majors = Major::select(array('name'))
			->where('name', 'like', '%' . $term . '%' )
			//->take('20')
			->get()
			->toArray();
		$majors_arr = array();
		foreach($majors as $major){
			$majors_arr[] = $major['name'];
		}
		return response()->json ($majors_arr);
	}

	/*AJAX Autocomplete controller to get professions for objective section
	 * of the profile page.
	 * @param		term					string			the user's search term passed as a get variable
	 * @return								json object		a json object with a list of professions relevant
	 * 														to the user's search
	 * @note		professions_arr			array			a auto (zero) indexed, single level array
	 */
	public function getProfessions(){
		$input = Request::all();
		$filter = array(
			'term' => array(
				'regex:/^[0-9a-zA-Z\- ]+$/'
			)
		);
		$validator = Validator::make( $input, $filter );

		if ($validator->fails()) {
			$messages = $validator->messages();
			return $messages;
			exit();
		}

		$term = Request::get('term');
		$professions = Profession::on('rds1')->select(array('profession_name'))
			->where('profession_name', 'like', '%' . $term . '%' )
			//->take('20')
			->get()
			->toArray();
		$professions_arr = array();
		foreach($professions as $profession){
			$professions_arr[] = $profession['profession_name'];
		}
		return response()->json ($professions_arr);
	}

	/* AJAX Autocomplete controller to get a list of states. Originally
	 * for ranking/listing section.
	 * @return		json object				A json object containing the matching states
	 */
	public function getStates(){
		$input = Request::all();
		$filter = array(
			'term' => array(
				'regex:/^[0-9a-zA-Z\- ]+$/'
			)
		);
		$validator = Validator::make( $input, $filter );

		if ($validator->fails()) {
			$messages = $validator->messages();
			return $messages;
			exit();
		}

		$query = Request::get('term');
		$states = DB::connection('rds1')->table('states')
			->where('state_name', 'like', '%' . $query . '%')
			->orWhere('state_abbr', 'like', '%' . $query . '%')
			->get();
			//->toArray();
		$states_arr = array();
		foreach( $states as $state ){
			$states_arr[] = array(
				'label' => $state->state_name . ' - ' . $state->state_abbr,
				'value' => $state->state_abbr,
				'id' => $state->id
			);
		}

		return response()->json( $states_arr );
	}

	public function getCities(){
		$input = Request::all();
		$filter = array(
			'term' => array(
				'regex:/^[0-9a-zA-Z\- ]+$/'
			)
		);
		$validator = Validator::make( $input, $filter );

		if ($validator->fails()) {
			$messages = $validator->messages();
			return $messages;
			exit();
		}

		$query = Request::get('term');
		$cities = DB::connection('rds1')->table('zip_codes')
			->where('CityName', 'like', '%' . $query . '%')
			->get();

		$cities_arr = array();
		foreach( $cities as $city ){
			$cities_arr[] = array(
				'label' => $city->CityName . ', ' . $city->StateAbbr . ' ' . $city->ZIPCode,
				'value' => $city->ZIPCode,
				'id' => $city->id
			);
		}

		return response()->json( $cities_arr );
	}

	public function getCollegeReligions(){
		$input = Request::all();
		$filter = array(
			'term' => array(
				'regex:/^[0-9a-zA-Z\- ]+$/'
			)
		);
		$validator = Validator::make( $input, $filter );

		if ($validator->fails()) {
			$messages = $validator->messages();
			return $messages;
			exit();
		}

		$query = Request::get('term');
		$school_religions = DB::connection('rds1')->table('colleges')
			->where( 'religious_affiliation', 'like', '%' . $query . '%' )
			->where( 'verified', 1 )
			->groupBy( 'religious_affiliation' )
			->limit( 10 )
			->get();
		$religions_arr = array();
		foreach( $school_religions as $religion ){
			$religions_arr[] = array(
				'label' => $religion->religious_affiliation,
				'value' => $religion->religious_affiliation,
				'id' => $religion->religious_affiliation
			);
		}

		return response()->json( $religions_arr );

	
	}

	public function getUsersByEmail () {
		$input = Request::all();

		$filter = array(
			'term' => array(
				'regex:/^[0-9a-zA-Z\-_@., ]+$/'
			)
		);
		$validator = Validator::make( $input, $filter );

		if ($validator->fails()) {
			$messages = $validator->messages();
			return $messages;
			exit();
		}

		$query = Request::get('term');

		$org = new Organization;
		$userEmails = $org->getOrgsAdminInfo(null, $query);

		$userlist = array();
		$cnt = 0;
		foreach ($userEmails as $userEmail) {
			$userlist[] = array(
				'id' => $userEmail->user_id,
				'value' => $userEmail->school_name,
				'label' => $userEmail->email,
				'fname' => $userEmail->fname,
				'lname' => $userEmail->lname,
				'email' => $userEmail->email
			);
			$cnt += 1;
			if($cnt > 5) {
				break;
			}
		}

		return response()->json( $userlist );
	}
}
