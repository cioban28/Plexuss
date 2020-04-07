<?php

namespace App\Http\Controllers;

use App\AdRedirectCampaign;
use App\Country;
use App\PlexussOnboardingSignupApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Request, Validator, Session;
use Illuminate\Support\Facades\Cache;
use App\User, App\NewsArticle, App\ShareButtons, App\CommentThread;
use Carbon\Carbon;

class B2BController extends Controller
{
    //
    	/*******************************
	* renders B2B page on load
	*
	***********************************/
	public function index() {
		$data['title'] = 'Plexuss Partnerships | Products ';
		$data['currentPage'] = 'b2b';
		$data['b2b_subpage'] = '_Home';

		if (Cache::has(env('ENVIRONMENT') .'_'.'plexuss_user_cnt')) {
			$data['plexuss_user_cnt'] = Cache::get(env('ENVIRONMENT') .'_'.'plexuss_user_cnt');
		}else{
			$data['plexuss_user_cnt'] = User::on('rds1')->count();
			Cache::put(env('ENVIRONMENT') .'_'.'plexuss_user_cnt', $data['plexuss_user_cnt'], 240);
		}

		return View('b2b.landingPage', $data);
	}



		/***********************************
	*	renders partial views -- AJAX in parts of B2B
	*   gives the effect of having very static top navigation (no total page reload)
	*	could hide() show() in JS -- can test to see if load all first go or partial is more optimal?
	*   IMPORTANT -- If not AJAX -- return a static page
	***********************************/
	public function partialIndex($subpage='') {

		$nest = '';
		$input = Request::all();

		$isAjax = Request::get('isAjax');

		$data['title'] = 'Plexuss Partnerships';
		$data['currentPage'] = 'b2b';
		$data['b2b_subpage'] = '_Home';
		$page = "b2b.landingPage";


		if($subpage == 'home'){
			$data['b2b_subpage'] = '_Home';
			$page = 'b2b.landingPage';

			if($isAjax == null){
				$page = 'b2b.landingPage';
				$data['meta-title']  = ' B2B | Plexuss | College Partnerships';
			}
		}
		else if($subpage == 'about-us'){
			$data['b2b_subpage'] = '_About-Us';
			$page = 'b2b.b2bAbout';

			if($isAjax == null){
				$page = 'b2b.b2bAbout';
				$data['meta-title']  = ' About | Plexuss | College Partnerships';
			}
		}

		else if($subpage == 'why-plexuss'){
			$data['b2b_subpage'] = '_Why-Plexuss';
			$page = 'b2b.b2bWhyPlexuss';

			if($isAjax == null){
				$page = 'b2b.b2bWhyPlexuss';
				$data['meta-title']  = ' Why Plexuss | Plexuss | College Partnerships';
			}
		}
		else if($subpage == 'our-solutions'){
			$data['b2b_subpage'] = '_Our-Solutions';
			$page = 'b2b.b2bOurSolutions';

			if($isAjax == null){
				$page = 'b2b.b2bOurSolutions';
				$data['meta-title']  = ' Our Solutions | Plexuss | College Partnerships';
			}
		}
		else if($subpage == 'testimonials'){
			$data['b2b_subpage'] = '_Testimonials';
			$page = 'b2b.b2bTestimonials';

			if($isAjax == null){
				$page = 'b2b.b2bTestimonials';
				$data['meta-title']  = ' Testimonials | Plexuss | College Partnerships';
			}
		}
		else if($subpage == 'news'){
			$news = new NewsArticle;
			$data['title'] = 'Plexuss Pixel';
			$data['currentPage'] = 'b2b';
			$data['b2b_subpage'] = '_Resources';
			$data['resources_subpage'] = '_PlexussPixel';
			$articles = $news->getBlogArticles("news", '1');

			$data['articles'] = $articles;
			$data['b2b_subpage'] = '_News';
			$page = 'b2b.b2bNews';

			if($isAjax == null){
				$page = 'b2b.b2bNews';
				$data['meta-title']  = ' News | Plexuss | College Partnerships';
			}
		}
		else if($subpage == 'contact-us'){
			$data['b2b_subpage'] = '_Contact-Us';
			$page = 'b2b.b2bContactUs';

			if($isAjax == null){
				$page = 'b2b.b2bContactUs';
				$data['meta-title']  = ' Contact Us | Plexuss | College Partnerships';
			}
		}
		else if($subpage == 'terms-of-service'){
			$data['b2b_subpage'] = '_Terms-Of-Service';
			$page = 'b2b.b2bToS';
		}
		else if($subpage == 'privacy-policy'){
			$data['b2b_subpage'] = '_Privacy-Policy';
			$page = 'b2b.b2bPrivacyPolicy';
		}
		else{
			$data['b2b_subpage'] = '_Home';
			$page = 'b2b.landingPage';

			if($isAjax == null){
				$page = 'b2b.static.landingPage';
				$data['meta-title']  = ' B2B | Plexuss | College Partnerships';
			}
		}


		if (Cache::has(env('ENVIRONMENT') .'_'.'plexuss_user_cnt')) {
			$data['plexuss_user_cnt'] = Cache::get(env('ENVIRONMENT') .'_'.'plexuss_user_cnt');
		}else{
			$data['plexuss_user_cnt'] = User::on('rds1')->count();
			Cache::put(env('ENVIRONMENT') .'_'.'plexuss_user_cnt', $data['plexuss_user_cnt'], 240);
		}


		//if nested view -- make nested view (data in both parent and child) if not just make view
		if($nest != ''){

			$data['showNest'] = true;
			// return View($page)->with('data', $data)->nest( $name, $nest, array('data'=>$data)) ;
		}else{
			$data['showNest'] = false;

		}

		return View($page, $data);

	}



	/****************************************
	* returns press releases
	*
	******************************************/
	public function pressReleases(){
		$data['title'] = 'Plexuss Partnerships';
		$data['currentPage'] = 'b2b';
		$data['b2b_subpage'] = '_Blog';
		$data['blog_subpage'] = '_PressReleases';


		$isAjax = Request::get('isAjax');

		//get articles
		$news = new NewsArticle;
		$articles = $this->getArticles('press', 1);
		$featured = $this->getFeatured(11);

		// Share buttons
		$buttons = new ShareButtons();
		$buttons->setPlatforms( array( 'facebook', 'twitter', 'pinterest', 'linkedin' ) );
			$buttons->setTitle( $featured[0]->title );
			$buttons->setImage( $featured[0]->img_lg );
			$buttons->setImagePath( "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/" );
			$buttons->setSlug( $featured[0]->slug );
			$buttons->setSlugPath( "http://www.plexuss.com/news/article/" );
			$buttons->makeParams();
		$share_buttons_params = $buttons->getParams();
		$data['share_buttons']['params'] = $share_buttons_params;
		$data['share_buttons']['stl_text'] = 'SHARE THE LOVE: ';

		$data['articles'] = $articles;
		$data['featured'] = $featured;

		if($isAjax){
			return View('b2b.blog.blogHome', $data);
		}
		else{
			$data['meta-title']  = ' Press Releases | Plexuss | College Partnerships';
			return View('b2b.static.blogHome', $data);
		}
	}


	/******************************************
	* returns new features view for blog
	*
	******************************************/
	public function newFeatures(){

		$data['title'] = 'Plexuss Partnerships';
		$data['currentPage'] = 'b2b';
		$data['b2b_subpage'] = '_Blog';
		$data['blog_subpage'] = '_NewFeatures';

		//get articles
		$news = new NewsArticle;
		$month = Request::get('month');
		$isAjax = Request::get('isAjax');

		$articles = $news->getNewFeatures(0);


		if(isset($articles) && $articles != ''){
			foreach($articles as $a){
				$a->timeAgo = $this->xTimeAgo($a->created_at, date("Y-m-d H:i:s"));

				$a->article_date = Carbon::createFromTimestamp( strtotime($a->created_at) )->toDayDateTimeString();
				// Share buttons
				$buttons = new ShareButtons();
				$buttons->setPlatforms( array( 'facebook', 'twitter', 'pinterest', 'linkedin' ) );
					$buttons->setTitle( $a->title );
					$buttons->setImage( $a->img_lg );
					$buttons->setImagePath( "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/" );
					$buttons->setSlug( $a->slug );
					$buttons->setSlugPath( "http://www.plexuss.com/news/article/" );
					$buttons->makeParams();
				$share_buttons_params = $buttons->getParams();
				$data['share_buttons']['params'] = $share_buttons_params;
				$data['share_buttons']['stl_text'] = 'SHARE THE LOVE: ';
			}
		}


		$data['articles'] = $articles;

		if($isAjax){
			return View('b2b.blog.newFeatures', $data);
		}
		else{
			$data['meta-title']  = ' New Features | Plexuss | College Partnerships';
			return View('b2b.static.newFeatures', $data);
		}
	}



	/*************************************
	*  get article single data
	*
	**************************************/
	public function getBlogView($articleName = ""){

		$news = new NewsArticle;

		$isAjax = Request::get('isAjax');

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



		$data['title'] = 'Plexuss | Blog';
		$data['currentPage'] = 'b2b-blog';




		$data['NewsId'] =$id;
		$data['order']="desc";
		$data['page']="1";
		$OrderUrl1="/news/";
		$OrderUrl2="/news/1?order=asc";
		$data['OrderUrl1']=$OrderUrl1;
		$data['OrderUrl2']=$OrderUrl2;
		//$data['urlParam']="";

		$news_article = $news->getArticle( $id );

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

		//if this article has_video, then grab 10 other video articles randomly selected ones
		if( $data['news_details']->has_video == 1 ){
			$newsArt = new NewsArticle;
			$vidArticles = $newsArt->getVideoArticles();
			$data['video_articles'] = $vidArticles;
		}

		$data['timeago'] = $this->xTimeAgo($news_article->created_at, date("Y-m-d H:i:s"));


		if($isAjax){
			$data['b2b_subpage'] = '_NewsArticle';
			return View('b2b.b2bNewsArticle', $data);
		}
		else{
			$data['title'] = 'Plexuss Partnerships';
			$data['currentPage'] = 'b2b';
			$data['b2b_subpage'] = '_NewsArticle';

			$data['meta-title']  = $news_article->title . ' | Plexuss | College Partnerships';
			return View('b2b.b2bNewsArticleNoAjax', $data);
		}
	}




	/*************************************
	*	returns array of all articles for blog
	*	-- called when loading blog page
	*************************************/
	public function getAllArticles(){

		$news = new NewsArticle;

		$articles = $news->getBlogArticles('blog', '1');

		// $randomNews=$news->FeaturedArticles(11);

		if(isset($articles) && $articles != ''){
			foreach($articles as $a){
				$a->timeAgo = $this->xTimeAgo($a->created_at, date("Y-m-d H:i:s"));
			}
		}

		return $articles;

	}


	/*******************************************
	*  returns fetaured blog articles
	*
	*************************************************/
	public function getFeatured($catId){
		$news = new NewsArticle;

		$articles = $news->featuredBlog($catId);

		return $articles;

	}



	/*************************************
	*	returns array of articles based on subcategory
	*   'all' 'press' or 'features'
	*	--called when AJAXing subsection of blog page (ex 'all' 'press')
	*************************************/
	public function getArticles($subCat = ''){

		$news = new NewsArticle;
		$subCat = Request::get('subCat') ? Request::get('subCat') : $subCat;

        $data['title'] = 'Plexuss Pixel';
        $data['currentPage'] = 'b2b';
        $data['b2b_subpage'] = '_Resources';
        $data['resources_subpage'] = '_PlexussPixel';
		$articles = $news->getBlogArticles($subCat, '1');

		$data['articles'] = $articles;
		if(isset($articles) && $articles != ''){
			foreach($articles as $a){
				$a->timeAgo = $this->xTimeAgo($a->created_at, date("Y-m-d H:i:s"));
			}
		}
		return View('b2b.b2bNews', $data);

	}


	/******************************
	*	get articles infinite scroll
	*
	***********************************/
	public function getMoreArticles(){

		$news = new NewsArticle;
		$subCat = Request::get('sub_cat_id');
		$page = Request::get('page');
		$articles = $news->getBlogArticles($subCat, $page);


		if(isset($articles) && $articles != ''){
			foreach($articles as $a){
				$a->timeAgo = $this->xTimeAgo($a->created_at, date("Y-m-d H:i:s"));
			}
		}

		$data['articles'] = $articles;

		return View('b2b.b2bNewsArticleBox', $data);

	}


	/**********************************
	*
	*
	*****************************************/
	public function getMoreNewFeatures(){
		$news = new NewsArticle;

		$offset = Request::get('offset');

		$articles = $news->getNewFeatures($offset);


		if(isset($articles) && $articles != ''){
			foreach($articles as $a){
				$a->timeAgo = $this->xTimeAgo($a->created_at, date("Y-m-d H:i:s"));
				// Share buttons

				$a->article_date = Carbon::createFromTimestamp( strtotime($a->created_at) )->toDayDateTimeString();

				$buttons = new ShareButtons();
				$buttons->setPlatforms( array( 'facebook', 'twitter', 'pinterest', 'linkedin' ) );
					$buttons->setTitle( $a->title );
					$buttons->setImage( $a->img_lg );
					$buttons->setImagePath( "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/" );
					$buttons->setSlug( $a->slug );
					$buttons->setSlugPath( "http://www.plexuss.com/news/article/" );
					$buttons->makeParams();
				$share_buttons_params = $buttons->getParams();
				$a->share_buttons['params'] = $share_buttons_params;
				$a->share_buttons['stl_text'] = 'SHARE THE LOVE: ';
			}
		}

		$data['articles'] = $articles;

		return View('b2b.blog.newFeaturesArticle', $data);

	}


	/*************************************
	*  search for blogs with term
	*
	****************************************/
	public function searchBlog(){

		$term = Request::get('term');
		$count = 0;

		$type= Request::get('type');

		$news = new NewsArticle;
		$articles = $news->searchBlog($term, $type);


		if(isset($articles) && $articles != ''){
			foreach($articles as $a){

				$a->timeAgo = $this->xTimeAgo($a->created_at, date("Y-m-d H:i:s"));
				$count++;
			}
		}

		$data['results'] = $articles;
		$data['count'] = $count;
		$data['term'] = $term;



		return View('b2b.blog.searchResults', $data);


	}

	public function plexussPixel(){
        $data['title'] = 'Plexuss Pixel';
        $data['currentPage'] = 'b2b';
        $data['b2b_subpage'] = '_Resources';
        $data['resources_subpage'] = '_PlexussPixel';
        return View('b2b.resources.plexussPixel',$data);

    }

    public function plexussOnboarding(){
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['title'] = 'Plexuss Onboarding';
        $data['currentPage'] = 'b2b';
        $data['b2b_subpage'] = '_Resources';
        $data['resources_subpage'] = '_PlexussOnboarding';

        $country = new Country();
        $data['country_list'] = $country->getCountriesWithNameIdAsKeys();
        $data['country_code_list'] = $country->getAllCountriesAndIdsWithCountryCode();

        $data['onboardingInfo'] = PlexussOnboardingSignupApplication::where('email',$data['email'])->first();
        return View('b2b.resources.plexussOnboarding',$data);
    }

    public function postOnboardingSignup(){
        $input = Request::all();
        $v = Validator::make( $input, User::$rules );
        $adminToken = null;

        if ( $v->passes() ) {
            $user = new User();
            $user->fname = Request::get( 'fname' );
            $user->lname = Request::get( 'lname' );
            $user->email = Request::get( 'email' );
            $user->password = Hash::make( Request::get( 'password' ) );

            $arr = $this->iplookup();

            if(Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])){
                $signup_params = Cache::get(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);

                $user->utm_source = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
                $user->utm_medium = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
                $user->utm_content= isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
                $user->utm_campaign   = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
                $user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
                $adminToken = isset($signup_params['adminToken']) ? $signup_params['adminToken'] : '';

                Cache::forget(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip']);

            }elseif (Session::has('signup_params')) {

                $signup_params = Session::get('signup_params');

                $user->utm_source = isset($signup_params['utm_source']) ? $signup_params['utm_source'] : '';
                $user->utm_medium = isset($signup_params['utm_medium']) ? $signup_params['utm_medium'] : '';
                $user->utm_content   = isset($signup_params['utm_content']) ? $signup_params['utm_content'] : '';
                $user->utm_campaign  = isset($signup_params['utm_campaign']) ? $signup_params['utm_campaign'] : '';
                $user->utm_term   = isset($signup_params['utm_term']) ? $signup_params['utm_term'] : '';
                $adminToken = isset($signup_params['adminToken']) ? $signup_params['adminToken'] : '';
            }

            $user->birth_date = Request::get ( 'year' ) . '-' . Request::get( 'month' ) . '-' . Request::get( 'day' );
            if($user->save()){
                Auth::loginUsingId( $user->id, true );
                $authController = new AuthController();
                $authController->setAjaxToken( Auth::user()->id );
                Session::put('userinfo.session_reset', 1);

                $startcontroller = new StartController();
                $startcontroller->setupcore();

                $response = [
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'email' => $user->email,
                    'birth_date' => $user->birth_date,
                    'id' => $user->id,
                    'status' => 'success'
                ];

            }
            return $response;
        }
        return view( 'b2b.resources.step_1' )->withErrors( $v )->withInput( Request::except( 'password' ) );
    }

    public function postOnboardingApplication() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $input = Request::all();
        $profile_img_loc = '';
        $response = array();

        try {
            if ( isset($input['admin-profile-photo'])) {
                $response = $this->generalUploadDoc($input, 'admin-profile-photo', 'asset.plexuss.com/users/images');

                $profile_img_loc = $response['saved_as'];
            }

        } catch (\Exception $e) {

        }

        $timestamp = strtotime($input['working_since_date']);
        $workingsince = date('Y-m-d', $timestamp);

        $country_code = $input['country_code'];
        $phone_number = $input['phone_number'];
        $phone = $country_code.''.$phone_number;

        $checkExistingEmail = PlexussOnboardingSignupApplication::where('email', $input['email'])->first();

        if(isset($checkExistingEmail) && count($checkExistingEmail) > 0){
            $update = array(
                        "user_id" => $data['user_id'],
                        "fname" => $input['fname'],
                        "lname" => $input['lname'],
                        "email" => $input['email'],
                        "birth_date" => $input['birth_date'],
                        "company" => $input['company'],
                        "title" => $input['title'],
                        "working_since" => $workingsince,
                        "phone" => $phone,
                        "skype" => $input['skype_id'],
                        "blurb" => $input['blurb'],
                        "profile_photo" => $profile_img_loc,
                    );

            $result = PlexussOnboardingSignupApplication::where('email', $input['email'])->update($update);
        } else {
            $plexussOnBoardingSignup = new PlexussOnboardingSignupApplication();
            $plexussOnBoardingSignup->user_id = $data['user_id'];
            $plexussOnBoardingSignup->fname = $input['fname'];
            $plexussOnBoardingSignup->lname = $input['lname'];
            $plexussOnBoardingSignup->email = $input['email'];
            $plexussOnBoardingSignup->birth_date = $input['birth_date'];
            $plexussOnBoardingSignup->company = $input['company'];
            $plexussOnBoardingSignup->title = $input['title'];
            $plexussOnBoardingSignup->working_since = $workingsince;
            $plexussOnBoardingSignup->phone = $phone;
            $plexussOnBoardingSignup->skype = $input['skype_id'];
            $plexussOnBoardingSignup->blurb = $input['blurb'];
            $plexussOnBoardingSignup->profile_photo = $profile_img_loc;
            $result = $plexussOnBoardingSignup->save();
        }

        if($result) {
            $getCompany = PlexussOnboardingSignupApplication::where('email', $input['email'])->first();
            $user = User::where('email', $input['email'])->first();
            $response = [
                'company' => $getCompany->company,
                'email' => $getCompany->email,
                'id' => $user->id,
                'status' => 'success'
            ];
        }
        return $response;
    }

    public function postAdRedirectCampaign(){
        $input = Request::all();
        $response = '';

        if(isset($input['url'])) {
            if(isset($input['inquiry_check'])){
                $IC = 1;
            } else {
                $IC = 0;
            }
            $adRedirectCampaign = new AdRedirectCampaign();
            $adRedirectCampaign->company = $input['rep_company'];
            $adRedirectCampaign->url = $input['url'];
            if (isset($input['note'])) {
                $adRedirectCampaign->comment = $input['note'];
            }
            $adRedirectCampaign->creator_user_id = $input['id'];
            if ($adRedirectCampaign->save()) {
                $update = array(
                    "service" => $input['service'],
                    "inquiry_check" => $IC
                );

                $update = PlexussOnboardingSignupApplication::where('email', $input['email'])->update($update);
                if ($update) {
                    $plexuss_signup_update = array('signup_complete'=> 1);
                    PlexussOnboardingSignupApplication::where('email',$input['email'])->update($plexuss_signup_update);
                    $onboardingUser = PlexussOnboardingSignupApplication::where('email', $input['email'])->first();
                    $response = [
                        'status' => 'success',
                        'name' => ucfirst($onboardingUser['fname'].' '.$onboardingUser['lname'])

                    ];

                    $pixel_url = "https://plexuss.com/trackPixel?company=" . $adRedirectCampaign->company;
                    $client_pixel = '<img src="' . $pixel_url . '" height="1" width="1" style="display:none;">';
                    $params = array();
                    $params['FNAME'] = ucfirst($onboardingUser['fname']);
                    $params['CLIENT_PIXEL'] = $client_pixel;
                    $params['CLIENT_URL'] = $adRedirectCampaign->url;

                    $email_address = $onboardingUser['email'];

                    $msg = $onboardingUser->company . " has requested to sign up for Client onboarding services. \n \n" . "plexuss_onboarding_signup_applications id: " . $onboardingUser->id . "\n" . "Client Name: " . $onboardingUser->fname . ' ' . $onboardingUser->lname . "\n" . "Client Email: " . $onboardingUser->email . "\n" . "Service type: " . $onboardingUser->service;

                    $tc = new TwilioController;
                    $tc->sendPlexussMsg($msg);

                    $mac = new MandrillAutomationController();
                    $mac->onboardingEmailSend($email_address, $params);
                }
            }
        } else {
            if(isset($input['posting_instruction']) || isset($input['verify_lead'])){
                if(isset($input['posting_instruction'])){
                    $PI = 1;
                } else {
                    $PI = 0;
                }
                if(isset($input['verify_lead'])){
                    $VL = 1;
                } else {
                    $VL = 0;
                }
            } else {
                $PI = 0;
                $VL = 0;
            }

            $update = array(
                "service" => $input['service'],
                "posting_instruction" => $PI,
                "verify_lead" => $VL
            );

            $update = PlexussOnboardingSignupApplication::where('email', $input['email'])->update($update);
            if ($update) {
                $plexuss_signup_update = array('signup_complete'=> 1);
                PlexussOnboardingSignupApplication::where('email',$input['email'])->update($plexuss_signup_update);
                $onboardingUser = PlexussOnboardingSignupApplication::where('email',$input['email'])->first();
                $response = [
                    'status' => 'success',
                    'name' => ucfirst($onboardingUser['fname'].' '.$onboardingUser['lname'])
                ];

                $params = ['FNAME' => $onboardingUser['fname']];
                $email_address = $onboardingUser['email'];

                $msg = $onboardingUser->company . " has requested to sign up for Client onboarding services. \n \n" . "plexuss_onboarding_signup_applications id: " . $onboardingUser->id . "\n" . "Client Name: " . $onboardingUser->fname . ' ' . $onboardingUser->lname . "\n" . "Client Email: " . $onboardingUser->email . "\n" . "Service type: " . $onboardingUser->service;

                $tc = new TwilioController;
                $tc->sendPlexussMsg($msg);

                $mac = new MandrillAutomationController();
                $mac->onboardingEmailSend($email_address, $params);
            }
        }
        return $response;
    }
}
