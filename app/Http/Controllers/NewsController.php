<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Carbon\Carbon;
use Request, Validator, Date, Session;
use App\NewsArticle, App\User, App\UsersPremiumEssay, App\PlexussAdmin, App\ShareButtons, App\LikesTally, App\NewsSubcategory, App\Recruitment;
use App\NewsCategory;

use	App\College, App\CommentThread;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;

class NewsController extends Controller
{
    public function index($first=false,$second=false, $is_api = null){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$order=Request::get('order');		
		$UrlforPage="";
		if($first=="" && $second==""){
			$UrlforPage="/news/";
		}	

		if (isset($is_api)) {
			$inURL=Request::segment(3);
		}else{
			$inURL=Request::segment(2);
		}
		
		$cat_id="";$sub_cat_id="";$flagCat="";$catName="";
		$news = new NewsArticle;

		if($inURL=="catalog"){
			$page=$second;
			$cat_id=$news->getCatSubCatIdbyName($first,'news_categories');
			$flagCat=1;
			$catName=$first;
			
		}
		else if($inURL=="subcategory"){
			$page=$second;
			$sub_cat_id=$news->getCatSubCatIdbyName($first,'news_subcategories');
			$cat_id=$sub_cat_id;
			$flagCat=2;
			$catName=$first;
		}
		else{
			$page=$first;
		}
		
		//Show public homepage if user is not logged in.		
		if(!isset($page) || $page==""){		
			$page="1";
		}
		if($order==""){
			$order=$sort="desc";
		}
		else{
			$sort=$order;
		}		


		/*if(isset($id) && $id!='')
		{
			$validator = Validator::make(
				array('id' => $id ),
				array('id' => 'required|integer')
			);
	
			if ($validator->fails()){
				echo 'There was an issue looking up that post. Error:9876';
				exit;
			}
		}*/
		/*$urlParam="";
		if($page>1)
		{
			$urlParam.=$page."/";
		}if($order=="asc")
		{
			$urlParam.="?order=".$order;
		}*/
		

		$OrderUrl1="";$OrderUrl2="";
		if($flagCat=='1')
		{
			$OrderUrl1="/news/catalog/".$catName."/";
			$OrderUrl2="/news/catalog/".$catName."?order=asc";
			if($page>1)
			{
			$OrderUrl1.=$page;	
			$OrderUrl2="/news/catalog/".$catName."/".$page."?order=asc";
			}			
		}
		else if($flagCat=='2')
		{
			$OrderUrl1="/news/subcategory/".$catName."/";
			$OrderUrl2="/news/subcategory/".$catName."?order=asc";
			if($page>1)
			{
			$OrderUrl1.=$page;	
			$OrderUrl2="/news/subcategory/".$catName."/".$page."?order=asc";
			}			
		}
		else
		{
			$OrderUrl1="/news/";
			$OrderUrl2="/news?order=asc";			
			if($page>1)
			{
			$OrderUrl1.=$page;
			$OrderUrl2="/news/".$page."?order=asc";			
			}			
		}		
		
		$data['order']=$order;
		$data['page']=$page;		
		$data['cat_id']=$cat_id;
		$data['sub_cat_id']=$sub_cat_id;
		$data['flagCat']=$flagCat;
		$data['OrderUrl1']=$OrderUrl1;
		$data['OrderUrl2']=$OrderUrl2;
		$data['UrlforPage']=$UrlforPage;
		$data['catName']=$catName;		
		
		//instantiating profile percentage to zero to show side bar when signed in
		$data['profile_perc'] = 0;
			
		$data['title']='Plexuss News';
		if($catName == ''){
			$data['currentPage'] = 'news';	
		}else if($catName =="college-essays"){
			$data['title'] = 'College Essays | Plexuss.com';
			$data['currentPage'] = 'college-essays';
			$data['catName'] = $catName;
		}
		else if($catName =="quad-testimonials"){
			$data['title'] = 'Student Testimonials | Plexuss.com';
			$data['currentPage'] = 'quad-testimonials';
			$data['catName'] = $catName;
		}
		else{
			$data['currentPage'] = 'news';
			$data['catName'] = $catName;
		}
		
		
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
		
			//get schools that the user has said 'get recruited' to
			$colleges = array();
			$recruitment = Recruitment::where( 'user_id', '=', $user->id )
				->where( 'status', '=', 1 )
				->where('user_recruit', 1)
				->get();

			foreach ($recruitment as $key) {
				$college = College::select('school_name')->where( 'id', '=', $key->college_id )->first();
				if (isset($college)) {
					$tmp['name'] = $college->school_name;
					$colleges[] = $tmp;
				}
			}
			$data['college_recruits'] = $colleges;	
		}
		$data['profile_img_loc'] = $src;

		
		//$newsdata=$news->newsDetails(false,"",$sort);
		$newsdata=$news->newsDetails(false,$cat_id,$order,$flagCat, false, $data['user_id']);
		$news_scat_data=NewsSubcategory::where('news_category_id','=','1')->orderBy('name',$sort)->get()->toArray();

		if(count($newsdata)>0 && $newsdata!=''){
			$data['newsdata'] = $newsdata;

			//converting the format of which the article was created at to display how long ago the article was created
			foreach ($data['newsdata'] as $value) {
				$value->created_at = $this->xTimeAgo($value->created_at, date("Y-m-d H:i:s"));
			}
		}
		$college_scat_data=NewsSubcategory::where('news_category_id','=','2')->orderBy('name',$sort)->get()->toArray();
		
		$college_after_data=NewsSubcategory::where('news_category_id','=','3')->orderBy('name',$sort)->get()->toArray();
		
		$randomNews=$news->FeaturedArticles($cat_id);

		$data['featured_rand_news'] = $randomNews;
		//$data['featured_rand_news'] = NewsArticle::where('id', 36);
		
		$data['news_scat_data'] = $news_scat_data;
		$data['college_scat_data'] = $college_scat_data;
		$data['college_after_data'] = $college_after_data;
		$data['cat_id'] = $cat_id;

		// Share buttons
		$buttons = new ShareButtons();

		$buttons->setPlatforms( array( 'facebook', 'twitter', 'pinterest', 'linkedin' ) );

		$buttons->setTitle( $data['featured_rand_news'][0]->title );

		//To check whether video or image
		if($data['featured_rand_news'][0]->has_video == 1){
			$buttons->setImage( $data['featured_rand_news'][0]->img_sm );
			$buttons->setImagePath( "" );
		}
		else
		{
			$buttons->setImage( $data['featured_rand_news'][0]->img_lg );
			$buttons->setImagePath( "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/" );
		}

		$buttons->setSlug( $data['featured_rand_news']['0']->slug );
		$buttons->setSlugPath( "http://www.plexuss.com/news/article/" );
		$buttons->makeParams();
		$share_buttons_params = $buttons->getParams();
		$data['share_buttons']['params'] = $share_buttons_params;

		$quizs = new QuizController();
		$data['quizInfo'] = $quizs->LoadQuiz();

		//**********LIKES TALLY CODE STARTS**********/
		//dd($data);
		$likes_tally = new LikesTally;

		$arr = array();
		$arr['type'] = 'news_articles';
		$arr['type_col'] = 'id';
		$arr['type_val'] = $data['featured_rand_news'][0]->id;
		$likes_tally = $likes_tally->getLikesTally($arr);

		$data['likes_tally'] = $likes_tally->cnt;
		if($likes_tally->isLiked == 0){
			$is_liked_img = '/images/social/like-icon-dark-gray.png';
		}else{
			$is_liked_img = '/images/social/like-icon-orange.png';
		}

		$data['is_liked_img'] = $is_liked_img;
		$data['hashed_news_id'] = Crypt::encrypt($arr['type_val']);
		//**********LIKES TALLY CODE ENDS ***********/
		$data['right_handside_carousel'] = $this->getRightHandSide($data);	
		
		$data['is_mobile'] = $this->isMobile();
    	// echo "<pre>";
    	// print_r($data);
    	// echo "</pre>";exit;
    	if (isset($is_api)) {
    		return $data;
    	}
		return View('private.news.index', $data);
	}

	public function view($articleName, $articleType='', $is_api = null){
		$news = new NewsArticle;


		//check if $articleName is a number (the article's id)
		if(is_numeric($articleName)){
			$id = $articleName;
		}
		else{
			$id = $news->getNewsIdbyName($articleName);
		}

		//CHECK TO MAKE SURE $id IS JUST A NUMBER!!
		$validator = Validator::make(
			array('id' => $id ),
			array('id' => 'required|integer')
		);

		if ($validator->fails()){
			echo 'There was an issue looking up that post. Error:9876';
			exit;
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		

		if($articleType == 'essay'){
			$data['title'] = 'Student Essay';
			$data['currentPage'] = 'college-essays';
		}
		else if($articleType == 'blog'){
			dd('type is blog');
		}
		else{
			$data['title'] = 'Plexuss News Detail';
			$data['currentPage'] = 'news';	
		}


		
		$data['NewsId'] =$id;
		$data['order']="desc";
		$data['page']="1";
		$OrderUrl1="/news/";
		$OrderUrl2="/news/1?order=asc";
		$data['OrderUrl1']=$OrderUrl1;
		$data['OrderUrl2']=$OrderUrl2;
		//$data['urlParam']="";	

		//instantiating profile percentage to zero to show side bar when signed in
		$data['profile_perc'] = 0;
			

		$src="/images/profile/default.png";

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );
			$data['profile_perc'] = $user->profile_percent;

			/*
			// Add aTeam to the data bucket
			// awong4242[at]gmail, alex[at]leadility, andrew.markiv[at]plexuss1
			$ateam_dev = array( 272, 271, 191 );
			// awong4242[at]gmail, alex[at]leadility, andrey.markiv[at]plexuss
			$ateam_live = array( 160, 755, 381 );
			foreach( $ateam_live as $ateam_member ){
				if( $user->id == $ateam_member ){
					$data['ateam'] = true;
				}
			}
			 */

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
			
		} else {
			
		}


		$data['profile_img_loc'] = $src;

		 /* We need to be able to distinguish between admins and non-admins. The newsDetails method
		 * has been modified so that $admin is false by default*/
		/*
		 * THIS IS NO LONGER NEEDED, ADMIN SYSTEM WILL BE REBUILT
		 if(isset($admin)){
			 $news_details = $news->newsDetails( $id ,false, false, false, $admin);
		 }
		 else{
			 $news_details = $news->newsDetails($id);
		 }
		 */
		$news_article = $news->getArticle( $id, $is_api );

		// News Author Code
		$sourceData = NewsArticle::find($id);
		$source = $sourceData->source;

		if($source == "external"){
			$data['source']['external_name'] = $sourceData->external_name;
			$data['source']['external_author'] = $sourceData->external_author;
			$data['source']['external_url'] = $sourceData->external_url;
			$data['source']['external_img'] = "/images/profile/default.png" ;
		} else{
			$authorID = $sourceData->author;
			$internal = User::find($authorID);
			$data['source']['internal_fname'] = $internal->fname;
			$data['source']['internal_lname'] = $internal->lname;
			if($internal->profile_img_loc != ""){
				$data['source']['internal_img'] = "http://asset.plexuss.com/users/images/" .  $internal->profile_img_loc;
			} else{
				$data['source']['internal_img'] = "/images/profile/default.png" ;
			}
		}

		/***************************************************
		 *================== COMMENTS CODE==================
		 ***************************************************/
		// Find comment thread, if any, and create if necessary
		// $thread_checker = new CommentThread;
		// $thread_checker->setRecord( $news_article );
		// $has_thread = $thread_checker->checkHasThreadId();
		// if( !$has_thread ){
		// 	$thread_checker->createNewThread();
		// 	$thread_id = $thread_checker->getNewThreadId();
		// }
		// // will always pass here, if stmt not needed
		// if( !is_null(  $news_article->comment_thread_id  ) ){
		// 	$data['comments']['thread_id'] = $news_article->comment_thread_id;
		// 	$comments = new CommentController;
		// 	$comments_json = $comments->getLatest(
		// 		array(
		// 			'thread_id' => $data['comments']['thread_id'],
		// 			'latest_comment_id' => 0 // set to 0 to get newest
		// 		)
		// 	);
		// 	$comments_array = json_decode( $comments_json );
		// 	if( isset( $comments_array->latest_comment_id ) ){
		// 		$data['comments']['latest_comment_id'] = $comments_array->latest_comment_id;
		// 	}
		// 	if( isset( $comments_array->earliest_comment_id ) ){
		// 		$data['comments']['earliest_comment_id'] = $comments_array->earliest_comment_id;
		// 	}
		// 	$data['comments']['comments'] = $comments_json;
		// }
		// // update user's anonymous status for 'post_anon' checkbox
		// if( isset( $user->comment_anon ) ){
		// 	$data['comments']['anon'] = $user->comment_anon ? 1 : 0;
		// }
		/**************************************************/

		$data['news_details'] =$news_article;
		// Share buttons href
		$data['news_details']->href = 'www.plexuss.com/news/article/' . $data['news_details']->slug;
		
	    $related_news=$news->relatedNews($data['news_details']->news_subcategory_id,$data['news_details']->id);
		$data['related_news'] =$related_news;

		//grabbing and storing current news articles meta keywords and descriptions for SEO on in the headers file
		if( $sourceData->external_name == 'Plexuss.com'){
			//if it's a Plexuss written article, we should have meta info for title, keywords, and description
			$data['news_details']->meta_keywords = $sourceData->meta_keywords;
			$data['news_details']->meta_descrip = $sourceData->meta_description;
			$data['news_details']->meta_title = $sourceData->page_title;
		
		//NOTES: still want the meta title given by external sites, even if the title is truncated
		}else{
			//otherwise if it's an outside written article, check if the article title/page title is less than 70 chars for SEO reasons
			$articleTitle = $data['news_details']->meta_title = $sourceData->title;
			$articleTitle .= ' | Plexuss.com';
			$titleLength = strlen($articleTitle);

			if(!empty($articleTitle)){
				if( $titleLength <= 120 ){
					$data['news_details']->meta_title = $articleTitle;
				}else{
					$data['news_details']->meta_title = 'Plexuss News Detail';
				}
			}
			$data['news_details']->meta_keywords = $sourceData->meta_keywords;
			$data['news_details']->meta_descrip = $sourceData->meta_description;
			
		}
		

		//add has video attr to news_details
		$data['news_details']->has_video = $sourceData->has_video;
	
		/* News category items??? */
		$news_scat_data=NewsSubcategory::where('news_category_id','=','1')
			->orderBy('name','asc')->get()->toArray();
		
		$news_cat_data=NewsCategory::orderBy('name','asc')
			->get()->toArray();
		
		$college_scat_data=NewsSubcategory::where('news_category_id','=','2')
			->orderBy('name','asc')->get()->toArray();
		
		$college_after_data=NewsSubcategory::where('news_category_id','=','3')
			->orderBy('name','asc')->get()->toArray();

		$data['news_scat_data'] = $news_scat_data;
		$data['news_cat_data'] = $news_cat_data;
		$data['college_scat_data'] = $college_scat_data;
		$data['college_after_data'] = $college_after_data;
		
		$data['bread_data'] = $news_article;

		// Share buttons
		$buttons = new ShareButtons();
		$buttons->setPlatforms( array( 'facebook', 'twitter', 'pinterest', 'linkedin' ) );
			$buttons->setTitle( $data['news_details']->title );
			$buttons->setImage( $data['news_details']->img_lg );
			$buttons->setImagePath( "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/" );
			$buttons->setSlug( $data['news_details']->slug );
			$buttons->setSlugPath( "http://www.plexuss.com/news/article/" );
			$buttons->makeParams();
		$share_buttons_params = $buttons->getParams();
		$data['share_buttons']['params'] = $share_buttons_params;
		$data['share_buttons']['stl_text'] = 'SHARE THE LOVE: ';

		$quizs = new QuizController();
		$data['quizInfo'] = $quizs->LoadQuiz();

		//**********LIKES TALLY CODE STARTS**********/

		$likes_tally = new LikesTally;

		$arr = array();
		$arr['type'] = 'news_articles';
		$arr['type_col'] = 'id';
		$arr['type_val'] = $data['NewsId'];
		$likes_tally = $likes_tally->getLikesTally($arr);

		$data['likes_tally'] = $likes_tally->cnt;
		if($likes_tally->isLiked == 0){
			$is_liked_img = '/images/social/like-icon-dark-gray.png';
		}else{
			$is_liked_img = '/images/social/like-icon-orange.png';
		}

		 $data['is_liked_img'] = $is_liked_img;
		 $data['hased_news_id'] = Crypt::encrypt($arr['type_val']);
		//**********LIKES TALLY CODE ENDS ***********/

		$data['right_handside_carousel'] = $this->getRightHandSide($data);

		//if this article has_video, then grab 10 other video articles randomly selected ones
		if( $data['news_details']->has_video == 1 ){
			$newsArt = new NewsArticle;
			$vidArticles = $newsArt->getVideoArticles();
			$data['video_articles'] = $vidArticles;
		}
		// echo "<pre>";
		// print_r($arr['type_val']);
		// echo "</pre>";
		// exit();
		// $dt = $data['news_details']->created_at;
		// $dt = $dt->toDateTimeString();
		// $dt = Carbon::parse($dt)->diffForHumans();
		
		$data['is_mobile'] = $this->isMobile();

		// $data['news_details']->created_at = $this->xTimeAgo($dt, date("Y-m-d H:i:s"));
		if (isset($is_api)) {
			return $data;
		}

		if($articleType == 'essay'){
			$data['catName'] = 'college-essays';

			$upe = UsersPremiumEssay::on('rds1')->where('user_id', $data['user_id'])
											  ->where('news_id', $arr['type_val'])
											  ->first();

			$data['hashed_news_id'] = Crypt::encrypt($arr['type_val']);
			$data['is_essay_purchased'] = (isset($upe)) ? true : false;

			return View('private.news.essaysView', $data);
		}
		else
			return View('private.news.view', $data);
	}
	



	/******************************************** added on 23 july *************************************************/
	
	public function getSubCategory(){

		$categoryId = $_REQUEST['categoryId'];
		$subcats = NewsSubcategory::where("news_category_id", "=", $categoryId)->get()->toArray();
		$matches = array();
		foreach($subcats as $row){
			$arr = array(
				'id' => $row['id'],
				'name' => $row['name'],
			);
			$matches[] = $arr;
		}

		$defaultRow = array(
			'id' => '',
			'name' => 'Select a Subcategory...',
		);
		array_unshift($matches, $defaultRow);
		return json_encode($matches);
	}
	
	public function newsListing()
	{
		$user = User::find( Auth::user()->id );

		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
		$data['title'] = 'Plexuss News';
		$data['currentPage'] = 'addnews';
		$token = AjaxToken::where('user_id', '=', $user->id)->first();

		if(!$token)
		{
			Auth::logout();
			return Redirect::to('/');
		}
		$news = new News;
		$newsdata=$news->getNews();
		
		$data['ajaxtoken'] = $token->token;
		$data['fname']=$user->fname;
		$data['lname']=$user->lname;
		
		$data['newsdata']=$newsdata;
		return View( 'private.news.newslisting', $data);
	}
	
	public function newsTest(){

		$subcats = NewsSubcategory::where("news_category_id", "=", 1)->get()->toArray();
		$matches = array();
		foreach($subcats as $row){
			$matches[$row['id']] = $row['name'];
		}

		//array_unshift($matches, "Select a Category First...");

		echo "<pre>";
		var_dump($matches);
		echo "</pre>";
		//$subcat = NewsSubcategory::find(2)->newsarticles->toArray();
	}

	public function addNews(){
	
		$user = User::find(Auth::user()->id);
		$newsCat = NewsCategory::all()->toArray();
		$categories = array('' => 'Select a Category...');
	
		foreach($newsCat as $category){
			$categories[] = $category['name'];
		}
	
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
		$data['title'] = 'Plexuss Add News';
		$data['currentPage'] = 'addnews';
		$data['categories'] = $categories;
		$token = AjaxToken::where('user_id', '=', $user->id)->first();
		
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
	
		if(!$token) {
			Auth::logout();
			return Redirect::to('/');
		}
		
		$data['ajaxtoken'] = $token->token;

		return View( 'private.admin.news.add', $data);
	}

	public function editNews($id){
		$validator = Validator::make(
			array('id' => $id ),
			array('id' => 'required|integer')
		);

		if ($validator->fails()){
			echo 'There was an issue looking up that post. Error:9876';
			exit;
		}

		$user = User::find(Auth::user()->id);
		$news = NewsArticle::find($id)->toArray();
		$newsCat = NewsCategory::all()->toArray();
		$categories = array('' => 'Select a Category...');
		$subcategories = array('' => 'Select a Subcategory...');

		$subcategory = $news['news_subcategory_id'];
		$category = NewsSubcategory::find($subcategory)->toArray();//['news_category_id']
		$subcats = NewsSubcategory::where('news_category_id', '=', $category['news_category_id'])->get()->toArray();


		foreach($newsCat as $cat){
			$categories[$cat['id']] = $cat['name'];
		}

		foreach($subcats as $subcat){
			$subcategories[$subcat['id']] = $subcat['name'];
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
		$data['title'] = 'Plexuss List News';
		$data['currentPage'] = 'addnews';
		$data['news'] = $news;
		$data['categories'] = $categories;
		$data['subcategories'] = $subcategories;
		$data['category'] = $category;
		$data['subcategory'] = $subcategory;
		$data['id'] = $id;
		$token = AjaxToken::where('user_id', '=', $user->id)->first();
		
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
	

		if(!$token) {
			Auth::logout();
			return Redirect::to('/');
		}

		return View( 'private.admin.news.edit', $data);
	}

	public function listNews(){

		$user = User::find(Auth::user()->id);
		$news = NewsArticle::all()->toArray();

		$subcatids = array();
		foreach($news as $row){
			$subcatid = NewsSubcategory::find($row['news_subcategory_id'])->toArray();
			$subcatids[] = $subcatid['name'];
		}

		$i=0;
		foreach($subcatids as $id){
			$news[$i]['news_subcategory_id'] = $id;
			$i++;
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
		$data['title'] = 'Plexuss List News';
		$data['currentPage'] = 'addnews';
		$data['news'] = $news;
		$token = AjaxToken::where('user_id', '=', $user->id)->first();
		
		$src="/images/profile/default.png";
		if($user->profile_img_loc!=""){
			$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}
		$data['profile_img_loc'] = $src;
	
		if(!$token) {
			Auth::logout();
			return Redirect::to('/');
		}
		
		$data['ajaxtoken'] = $token->token;

		return View( 'private.admin.news.list', $data);

	}

	public function postEditedNews($id){
		$input = Request::all();

		$rules = array(
			'title' => 'required|Min:3|Max:255',
			'news_subcategory_id' => 'required',
			'content' => 'required',
			'img_lg' => 'image',
			'img_sm' => 'image',
			
		);	
		$v = Validator::make( $input, $rules );

		if ( $v->passes() ) 
		{		
				$user_id=Auth::user()->id;
				$news = NewsArticle::find($id);
				
				$news->author = Auth::user()->id;
				$news->title = Request::get('title');
				$news->content = Request::get('content');
				$news->source = Request::get('source');
				if($news->source === 'external'){
					$news->external_name = Request::get('external_name');
					$news->external_url = Request::get('external_url');
					$news->external_author = Request::get('external_author');
				}
				$news->news_subcategory_id = Request::get('news_subcategory_id');

				$lg = Request::file('img_lg');
				$sm = Request::file('img_sm');
				$images = array($lg, $sm);

				$i = 0;
				$uid = Auth::user()->id;
				$timestamp = date('Y-m-d_His');
				foreach($images as $image){

					if(is_null($image)){
						$i++;
						break;
					}
					$filePath = $image->getRealPath();
					$extension = $image->getClientOriginalExtension();
					$path = $filePath;

					switch($i){
						case 0:
							$size = "_lg";
							$filename = $uid . "_" . $timestamp . $size . "." . $extension;
							$news->img_lg = $filename;
							break;

						case 1:
							$size = "_sm";
							$filename = $uid . "_" . $timestamp . $size . "." . $extension;
							$news->img_sm = $filename;
							break;
					}

					$aws = AWS::get('s3');
					$aws->putObject(array(
						'ACL'        => 'public-read',
						'Bucket'     => 'asset.plexuss.com/news/images',
						'Key'        => $filename,
						'SourceFile' => $path, 
					));
					$i++;

				}

				// push news to DB
				$insert = $news->save();

				if($insert)
				{
					return Redirect::to('listnews');
				}
				else
				{
					$error = array( 'Check your information again.' );
					return Redirect::to( 'addnews' )->withErrors( $error );
				}
			
		}
		return Redirect::to('addnews')->withErrors( $v )->withInput();	
	}

	public function postNews(){
		$input = Request::all();

		$rules = array(
			'title' => 'required|Min:3|Max:255',
			'news_subcategory_id' => 'required',
			'content' => 'required',
			'img_lg' => 'image|required',
			'img_sm' => 'image|required',
			
		);	
		$v = Validator::make( $input, $rules );

		if ( $v->passes() ) 
		{		
			$user_id=Auth::user()->id;
			$news = new NewsArticle;

			$news->author = Auth::user()->id;
			$news->title = Request::get('title');
			$news->content = Request::get('content');
			$news->source = Request::get('source');
			if($news->source === 'external'){
				$news->external_name = Request::get('external_name');
				$news->external_url = Request::get('external_url');
			}
			$news->news_subcategory_id = Request::get('news_subcategory_id');
			
				$lg = Request::file('img_lg');
				$sm = Request::file('img_sm');
				$images = array($lg, $sm);

			$i = 0;
			$uid = Auth::user()->id;
			$timestamp = date('Y-m-d_His');

			foreach($images as $image){

				$filePath = $image->getRealPath();
				$extension = $image->getClientOriginalExtension();
				$path = $filePath;

				switch($i){
					case 0:
						$size = "_lg";
						$filename = $uid . "_" . $timestamp . $size . "." . $extension;
						$news->img_lg = $filename;
						break;

					case 1:
						$size = "_sm";
						$filename = $uid . "_" . $timestamp . $size . "." . $extension;
						$news->img_sm = $filename;
						break;
				}

				$aws = AWS::get('s3');
				$aws->putObject(array(
					'ACL'        => 'public-read',
					'Bucket'     => 'asset.plexuss.com/news/images',
					'Key'        => $filename,
					'SourceFile' => $path, 
				));
				$i++;
			}

			// push news to DB
			$insert = $news->save();

			if($insert)
			{
				return Redirect::to('listnews');
			}
			else
			{
				$error = array( 'Check your information again.' );
				return Redirect::to( 'addnews' )->withErrors( $error );
			}
		}
		return Redirect::to('addnews')->withErrors( $v )->withInput();	
	}
	/******************************************** added on 23 july *************************************************/	
	
	public function newsAjaxData(){
		$html ='';
		$newsid='0';

		$cat_id = Request::get('cat_id');
		$sub_cat_id = Request::get('sub_cat_id');
		$flagCat = Request::get('flagCat');
		$order = Request::get('order');

	
		
		//is cat subcategory? 2 : 1
		if(isset($flagCat) && $flagCat=='1'){
			$cat_id = Request::get('cat_id');
		} else if(isset($flagCat) && $flagCat=='2') {
			$cat_id = Request::get('sub_cat_id');
		} else {
			$cat_id="";
		}

		if(!isset($order)) {
			$order="desc";
		}

		if(isset($id) && $id!='') {

			$validator = Validator::make(
				array('id' => $id ),
				array('id' => 'required|integer')
			);
	
			if ($validator->fails()){
				echo 'There was an issue looking up that post. Error:9876';
				exit;
			}
		}
		
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		
		$data['title'] = 'Plexuss News';
		$data['currentPage'] = 'news';
		
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
				array_push( $data['alerts'], '<span class=\"pls-confirm-msg\">Please confirm your email to gain full access to Plexuss tools.</span> <span class=\"go-to-email\" data-email-domain=\"'.$data['email_provider'].'\"><span class=\"hide-for-small-only\">Go to</span><span class=\"show-for-small-only\">Confirm</span> Email</span> <span> | <span class=\"resend-email\">Resend <span class=\"hide-for-small-only\">Email</span></span></span> <span> | <span class=\"change-email\" data-current-email=\"'.$data['email'].'\">Change email <span class=\"hide-for-small-only\">address</span></span></span>' );
			}
			
		}
		
		$data['profile_img_loc'] = $src;
		
		$news = new NewsArticle;

		$newsdata = $news->newsDetails(false,$cat_id,$order,$flagCat);
		//check if there are any news to return. If not EXIT!
		if(count($newsdata)>0 && $newsdata!=''){
			$data['newsdata'] = $newsdata;
		}else{
			return "";
		}

		//This looks to build the 3 main news menu dropdowns.
		$news_scat_data = NewsSubcategory::where('news_category_id','=','1')->orderBy('name','asc')->get()->toArray();
		$data['news_scat_data'] = $news_scat_data;
		$college_scat_data = NewsSubcategory::where('news_category_id','=','2')->orderBy('name','asc')->get()->toArray();
		$data['college_scat_data'] = $college_scat_data;
		$college_after_data = NewsSubcategory::where('news_category_id','=','3')->orderBy('name','asc')->get()->toArray();
		$data['college_after_data'] = $college_after_data;

		//This did not seem to be used at all since te featured news is pulled in with PHP NOT ajax
		//$randomNews = $news->FeaturedArticles($cat_id);
		//$data['featured_rand_news'] = $randomNews;
		if(isset($data['newsdata']) && $data['newsdata']!=''){
			foreach($data['newsdata'] as $news){
				$news->timeAgo = $this->xTimeAgo($news->created_at, date("Y-m-d H:i:s"));
			}
		}
		if ($cat_id == 5) {
			return View('private.news.ajax.essayBlock', $data);
		}else{
			return View('private.news.ajax.newsBlock', $data);
		}
		
	
	}

	// -- search for articles
	public function search(){

		$input = Request::all();
		$na = new NewsArticle;
		$results = $na->searchArticles($input['search']);
		//$results = (array)$results;

		
		if( $results == 'fail' ){
			return 'No results found';
		}else{
			$tmp = array();
			foreach ($results as $key) {
				switch ($key->news_subcategory_id) {
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
						$key->category_color = 'collegeNews';
						break;
					case 6:
						$key->category_color = 'payingForCollege';
						break;
					case 7:
						$key->category_color = 'lifeAfterCollege';
						break;
					case 9:
						$key->category_color = 'essays';
						break;
					case 10:
						$key->category_color = 'testimonials';
					default:
						$key->category_color = 'collegeNews';
						break;
				}
				$key->time = $this->xTimeAgo($key->created_at, date("Y-m-d H:i:s"));
				if( isset($key->has_video) && $key->has_video == 1 ){
					$key->short_descrip = $key->meta_description;
				}else if ($key->news_subcategory_id == 9) {
					$key->short_descrip = substr(strip_tags($key->basic_content),0,200).'...';
					$key->content = utf8_encode(trim(strip_tags($key->basic_content)));
				}else{
					$key->short_descrip = substr(strip_tags($key->content),0,200).'...';
					$key->content = utf8_encode(trim(strip_tags($key->content)));
				}
				$tmp[] = $key;
			}

			return $tmp;
		}
	}

	public function purchaseEssay(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();

		$ret = array();

		if (isset($data['signed_in']) && $data['signed_in'] == 0) {
			$ret['status'] = 'failed';
			$ret['error']  = "You need to be signed in to view essays";
			return json_encode($ret);
		}

		try {
			$news_id = Crypt::decrypt($input['news_hashed_id']);
		} catch (Exception $e) {
			$ret['status'] = 'failed';
			$ret['error']  = "Invalid news id";
			return json_encode($ret);
		}

		$num_of_premium_essays_viewed = UsersPremiumEssay::on('rds1')->where('user_id', $data['user_id'])->count();

		$total_num_of_eligible_essays = 0;
        $total_num_of_applied_colleges = 0;

        if ($data['premium_user_type'] === 'onetime_plus') {
            $total_num_of_eligible_essays = 50;
            $total_num_of_applied_colleges = 10;
        }elseif ($data['premium_user_type'] === 'onetime') {
            $total_num_of_eligible_essays = 20;
            $total_num_of_applied_colleges = 5;
        }elseif ($data['premium_user_type'] =='plexuss_free') {
            $total_num_of_eligible_essays = 1;
            $total_num_of_applied_colleges = 5;
        }else{
            $total_num_of_eligible_essays = 1;
            $total_num_of_applied_colleges = 5;
        }

        $data['num_of_eligible_premium_essays'] = $total_num_of_eligible_essays - $num_of_premium_essays_viewed;

		if ($data['num_of_eligible_premium_essays'] > 0) {
			$attr = array('user_id' => $data['user_id'], 'news_id' => $news_id);
			$val  = array('user_id' => $data['user_id'], 'news_id' => $news_id);

			$update = UsersPremiumEssay::updateOrCreate($attr, $val);

			$content = NewsArticle::on('rds1')->where('id', $news_id)->first();
			$ret['status']  = 'success';
			$ret['content'] = $content->premium_content;
		}else{
			$ret['status'] = 'failed';
			$ret['error']  = 'Insufficient funds';
		}

		Session::put('userinfo.session_reset', 1);
		Cache::forget(env('ENVIRONMENT') .'_'.'userinfo');

		return json_encode($ret);
	}
}
