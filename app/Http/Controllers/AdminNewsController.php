<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Illuminate\Http\Request;

class AdminNewsController extends Controller
{
    /*==================================================
	 *================BEGIN NEWS SECTION================
	 *==================================================*/
	public function news(){

		if(!Auth::check()){
			App::abort(404);
		}

		$user = User::find( Auth::user()->id );
		$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
		$admin = array_shift($admin);
		if(empty($admin)){
			App::abort(404);
		}
		if($admin['news'] != 'w'){
			return Redirect::to('/admin');
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

		


		$newsArticle = new NewsArticle();
		$news_list = $newsArticle->listAll();
		foreach($news_list as $article){
			//Format Date
			$arr = date_parse($article->updated);
			$article->date = $arr['month'] . '-' . $arr['day'] . '-' . $arr['year'];
			//Format Name
			if(is_null($article->author_f) || is_null($article->author_l)){
				$article->author = 'N/A';
			}
			else{
				$article->author = $article->author_f . ' ' . $article->author_l;
			}
		}
		/*
		echo "<pre>";
		var_dump($news_list);
		echo "</pre>";
		exit;
		 */
		$data['admin_title'] = "News List";
		$data['news_list'] = $news_list;
		return View('admin.news', $data);

	}
	public function addNews(){

		if(!Auth::check()){
			App::abort(404);
		}

		$user = User::find( Auth::user()->id );
		$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
		$admin = array_shift($admin);
		if(empty($admin)){
			App::abort(404);
		}
		if($admin['news'] != 'w'){
			return Redirect::to('/admin');
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

		
		$data['admin_title'] = "Add News";
		
		$newsCat = NewsCategory::all()->toArray();
		$categories = array('0' => 'Select a Category...');
		foreach($newsCat as $category){
			$categories[] = $category['name'];
		}
		$data['news_article']['categories'] = $categories;
		return View('admin.add.news', $data);

	}

	public function editNews($id){

		if(!Auth::check()){
			App::abort(404);
		}

		$user = User::find( Auth::user()->id );
		$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
		$admin = array_shift($admin);
		if(empty($admin)){
			App::abort(404);
		}
		if($admin['news'] != 'r' && $admin['news'] != 'w'){
			return Redirect::to('/admin');
		}
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['admin'] = $admin;
		$data['admin']['page'] = 'news';

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

		
		$data['admin_title'] = "Edit News";

		//Add admin details to data container

		//check if there is a pending revision for the article clicked & redirect them
		//to the revision if so. Else, get the live article (which has no pending changes).
		$article = NewsArticle::where('prev_id', $id)
			->where('live_status', '0')->first();
		if(!is_null($article)){
			$alerts = array();
			array_push($alerts, 
				array(
					'img' => '/images/topAlert/urgent.png',
					'color' => '#de4728',
					'type' => 'soft',
					'dur' => '10000',
					'msg' => "You've been redirected to the latest pending revision of your article"
				)
			);
			Session::put('topAlerts', $alerts);
			return Redirect::to('/admin/edit/news/' . $article->id);
		}
		else{
			//Get article
			$article = NewsArticle::where('id', $id)->first();
			if(is_null($article)){
				return Redirect::to('/admin');
			}
		}

		//Get article
		/*
		$article = NewsArticle::where('id', $id)->first();
		if(is_null($article)){
			return Redirect::to('/admin');
		}
		 */

		$article = $article->toArray();
		$newsArticle = new NewsArticle();
		$article['news_category_id'] = $newsArticle->getCategoryId($article);
		$data['news_article'] = $article;
	
		//Get article category
		$newsCat = NewsCategory::all()->toArray();
		$categories = array('0' => 'Select a Category...');
		foreach($newsCat as $category){
			$categories[] = $category['name'];
		}
		//Get article subcategory
		$newsSubCat = NewsSubcategory::select('id', 'name')->where('news_category_id', $article['news_category_id'])->get()->toArray();
		$subcategories = array('0' => 'Select a Subcategory...');
		foreach($newsSubCat as $subcategory){
			$subcategories[$subcategory['id']] = $subcategory['name'];
		}
		// Check if there is a pending revision for this article
		$is_live = NewsArticle::where('id', '=', $id)
			->get()
			->toArray();
		$is_live = $is_live[0];
		if($is_live['live_status'] == 0){
			$is_live = false;
		}
		else{
			$is_live = true;
		}

		$data['news_article']['src_prefix'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/";
		$data['news_article']['categories'] = $categories;
		$data['news_article']['subcategories'] = $subcategories;
		$data['news_article']['is_live'] = $is_live;

		/*
		echo "<pre>";
		var_dump($data['news_article']);
		echo "</pre>";
		exit;
		 */
		return View('admin.add.news', $data);
	}

	/* Catches all new/edit posts to News articles.
	 */
	public function postNews($id = null){

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
		/*
		echo "input: <pre>";
		var_dump($input);
		echo "</pre>";
		exit;
		 */

		$rules = array(
			'title' => 'required|Min:3|Max:255',
			'news_category_id' => array('required', 'numeric'),
			'news_subcategory_id' => array('required', 'numeric'),
			'content' => 'required',
			'img_lg' => 'image',
			'img_sm' => 'image',
			'source' => array('required', 'regex:/^(internal|external)$/'),
			'external_name' => array('required_if:source,external', 'regex:/^([0-9a-zA-Z\.\-\' ])+$/'),
			'external_url' => array('required_if:source,external', 'url'),
			'external_author' => array('required_if:source,external', 'regex:/^([a-zA-Z\.\' ])+$/'),
			'qa' => array('required_if:live,1', 'regex:/^(0|1)$/'),
			'live' => array('regex:/^(0|1)$/')

			
		);
		$v = Validator::make( $input, $rules );
		if($v->passes()){
			//insert new row
				//if there is a news_id variable (if there's a pending revision)
					//check if there is a row with same id and if it's pending
						//if so, update that row
							//3 types of 'pending' style edits:
								// - de edit, which removes qa_uid and replaces de_uid
								// - qa edit, which replaces qa_uid
								// - before live edit, which inserts live_uid
									// AND sets prev_id live (1) to old (-1)
						//AND update the admin_activity table with all changes
							//This includes 
						//if not, create a new row with an incremented id, marked pending (if there is no pending revision)
				//if there is no news_id variable (if it's an ADD)
					//get the highest id in the table
					//create a new row with the additions, pending, with the highest id++
					//add changes to the admin_activity table
			
			//set slug via input or generate from title
			$slugger = new Slugger();
			if(isset($input['slug'])){
				$slug = $input['slug'];
			}
			else{
				$slug = $slugger->makeSlug($input['title']);
			}
			/*
			echo 'id: ';
			var_dump($id);
			echo '<br>';
			echo "input: <pre>";
			var_dump($input);
			echo "</pre>";
			exit;
			 */

			/* There are some variables which determines how we add an article
			 * to the DB:
			 * $add: which tells if it was an add or edit
			 * $live: which tells if the edit is live or pending
			 */

			// Decide if the post was an ADD or EDIT
			if($id == null){
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

			// Determine if there is a LIVE article
			$liveArticle = NewsArticle::where
				('live_status', '=', '1')
				->where('id', '=', $id)
				->get()
				->toArray();
			if(empty($liveArticle)){
				$live = false;
			}
			else{
				$live = true;
			}

			echo "live: ";
			var_dump($live);
			echo "<br>";

			// Determine if there is a PENDING article
			$pendingArticle = NewsArticle::where
				('live_status', '=', '0')
				->where('id', '=', $id)
				->get()
				->toArray();
			if(empty($pendingArticle)){
				$pending = false;
			}
			else{
				$pending = true;
			}

			echo "pending: ";
			var_dump($pending);
			echo "<br>";

			// ADD NEWS
			if($add){
				/* add news code here! */
				echo "That would be a NEW ARTICLE <br>";
				$slugCheck = $slugger->checkNewsSlug('new', $slug, 'news_articles');

				//SlugCheck returns true if no conflicting slugs are found
				if($slugCheck){
					echo "we're clear! <br>";
					//set values we need for DB row update
					$input['id'] = null;
					$input['prev_id'] = null;
					$input['user_id'] = $user->id;
					$input['slug'] = $slug;
					//set qc (de/qa/live) values to user id if present
					$input = $this->setQC($input);
					//run model code to update DB
					$newsArticle = new NewsArticle();
					$newsArticle->setInput($input);
					$newsArticle->addNewsRevision('new');

					$alerts = array();
					array_push($alerts, 
						array(
							'img' => '/images/topAlert/checkmark.png',
							'type' => 'soft',
							'dur' => '6000',
							'msg' => 'News Article Added!'
						)
					);
					Session::put('topAlerts', $alerts);

				}
				else{
					// Put this in a topAlert!!!
					echo "matches found! Enter a different title! <br>";

					$alerts = array();
					array_push($alerts, 
						array(
							'img' => '/images/topAlert/urgent.png',
							'color' => '#de4728',
							'type' => 'soft',
							'dur' => '8000',
							'msg' => 'There is already a news article with that title!'
						)
					);
					Session::put('topAlerts', $alerts);
					return Redirect::to('/admin/add/news/')->withInput();
				}

			}

			// EDIT NEWS (code for add/edit pending row)
			else{
				// NEW PENDING (code for add pending row)
				if($pending == false){
					// Code to SET LIVE BACK TO PENDING when live is not checked
					/*
					if($live == true && !isset($input['live'])){
						echo "Will take live article down now! <br>";
						exit;
						//set slug value; check to see if slug will be unique
						$prev_id = NewsArticle::where('id', '=', $id)->pluck('prev_id');
						$slugCheck = $slugger->checkNewsSlug('redo', $slug, 'news_articles', $id);
						
						if($slugCheck){
							//set values we need for DB row update
							$input['id'] = $id;
							$input['prev_id'] = $prev_id;
							$input['user_id'] = $user->id;
							$input['slug'] = $slug;
							//set qc (de/qa/live) values to user id if present
							$input = $this->setQC($input);
							//run model code to update DB
							$newsArticle = new NewsArticle();
							$newsArticle->setInput($input);
							$newsArticle->editNewsRevision();
						}
					}
					 */
					/* NEW PENDING CODE HERE! */
					//else{
						echo "Adding new Pending row! <br>";
						$prev_id = $id;
						$slugCheck = $slugger->checkNewsSlug('add', $slug, 'news_articles', $id);

						if($slugCheck){
							//set values we need for DB row update
							$input['id'] = $id;
							$input['prev_id'] = $prev_id;
							$input['user_id'] = $user->id;
							$input['slug'] = $slug;
							//set qc (de/qa/live) values to user id if present
							$input = $this->setQC($input);
							//run model code to update DB
							$newsArticle = new NewsArticle();
							$newsArticle->setInput($input);
							$newsArticle->addNewsRevision('existing');

							$alerts = array();
							array_push($alerts, 
								array(
									'img' => '/images/topAlert/checkmark.png',
									'type' => 'soft',
									'dur' => '10000',
									'msg' => 'News article updated!'
								)
							);
							Session::put('topAlerts', $alerts);
						}

					//}

				}
				// HAS PENDING (code for edit pending row)
				else{
					echo "Editing a Pending row! <br>";
					//set slug value; check to see if slug will be unique
					$prev_id = NewsArticle::where('id', '=', $id)->pluck('prev_id');
					$prev_id = $prev_id[0];
					$slugCheck = $slugger->checkNewsSlug('edit', $slug, 'news_articles', $id, $prev_id);

					if($slugCheck){
						//set values we need for DB row update
						$input['id'] = $id;
						$input['prev_id'] = $prev_id;
						$input['user_id'] = $user->id;
						$input['slug'] = $slug;
						//set qc (de/qa/live) values to user id if present
						$input = $this->setQC($input);
						//run model code to update DB
						$newsArticle = new NewsArticle();
						$newsArticle->setInput($input);
						$newsArticle->editNewsRevision();

						$alerts = array();
						array_push($alerts, 
							array(
								'img' => '/images/topAlert/checkmark.png',
								'type' => 'soft',
								'dur' => '10000',
								'msg' => 'News article updated!'
							)
						);
						Session::put('topAlerts', $alerts);
					}

				}

			}

		}
		else{
			//Alerts user: Form data did not pass validation
			$alerts = array();
			array_push($alerts, 
				array(
					'img' => '/images/topAlert/urgent.png',
					'color' => '#de4728',
					'type' => 'soft',
					'dur' => '10000',
					'msg' => 'Please check your form data.'
				)
			);
			Session::put('topAlerts', $alerts);
			return Redirect::to('/admin/add/news/')->withInput();

			$msgs = $v->messages();
			echo "input: <pre>";
			var_dump($input);
			echo "</pre>";
			return $msgs;
		}
		
		return Redirect::to('/admin/news');

	}

	/* Deletes a news article
	 * param		$id			int			the id of the article to be deleted
	 *
	 */
	public function deleteNews($id){
		echo "Time to delete news article no: " . $id . "<br>";
		$toDelete = NewsArticle::find($id);
		$toDelete->delete();

		$alerts = array();
		array_push($alerts, 
			array(
				'img' => '/images/topAlert/checkmark.png',
				'type' => 'soft',
				'dur' => '6000',
				'msg' => 'Article number ' . $id . ' deleted.'
			)
		);

		Session::put('topAlerts', $alerts);
		return Redirect::to('/admin/news');
		
	}

	/* Prepares the input array by setting who_qa, who_de, and
	 * who_live values based on if the checkbox was filled out.
	 * removes who_qa if data entry modifies the file.
	 * param		$input		array		an array containing all the form
	 * 										data submitted on an edit/add
	 * return		$input		array		the input array with the extra
	 * 										qc related key/val pairs.
	 */
	private function setQC($input){

		//Sets who_qa if qa is true and unsets if not selected or set
		if(isset($input['qa']) && $input['qa'] == true){
			$input['who_qa'] = $input['user_id'];
		}
		else{
			$input['who_de'] = $input['user_id'];
			$input['who_qa'] = null;
		}

		//Sets who_live if live is true, unsets if not selected or set
		if(isset($input['live']) && $input['live'] == true){
			$input['who_live'] = $input['user_id'];
		}
		else{
			$input['who_live'] = null;
		}

		// Resets QA if data entered by a DE admin
		if(!isset($input['qa']) && !isset($input['live'])){
			$input['who_de'] = $input['user_id'];
			$input['who_qa'] = null;
			$input['who_live'] = null;
		}

		return $input;
	}
	/*==================================================
	 *=================END NEWS SECTION=================
	 *==================================================*/
}
