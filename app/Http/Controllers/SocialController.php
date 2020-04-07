<?php

namespace App\Http\Controllers;

use App\User;
use App\Post;
use App\SocialArticle;
use App\ArticleTag;
use App\PublicProfileProjectsAndPublications;
use App\PostComment;
use App\Like;
use App\PostImage;
use App\FriendRelation;
use App\CollegeMessageThreads;
use App\CollegeMessageThreadMembers, App\SocialHiddenArticlesPost, App\SocialAbuseReport;
use App\CollegeMessageLog, App\UsersCustomQuestion, App\CollegeRecommendationFilters, App\SocialLinkPreview, App\SocialLinkPreviewPic;
use Carbon\Carbon;
use Request, Session, DB, Validator, AWS, DateTime, Hash;

use App\Http\Controllers\OmniPayController;
use App\Http\Controllers\NotificationController;
use App\Country, App\State, App\Organization, App\OrganizationPortal, App\PurchasedPhone, App\AdminText, App\FreeTextCountry, App\SettingNotificationName, App\UsersInvite, App\AjaxToken;
use App\SettingNotificationLog, App\SettingDataPreferenceLog, App\SettingNotificationLogHistory, App\UserEducation, App\PublicProfileClaimToFame, App\Objective, App\Occupation, App\PublicProfileSkills, App\PublicProfileSkillsEndorsements, App\PublicProfileSettings, App\UserAccountSettings, App\MobileDeviceToken;
use App\Http\Controllers\ProfilePageController, App\Http\Controllers\CollegeRecommendationController;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

use LinkPreview\LinkPreview;
use LinkPreview\Model\VideoLink;
use GuzzleHttp\Exception\RequestException;

class SocialController extends Controller
{
  public function index() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    $data['currentPage'] = 'social-dashboard';

    $avatar_src = "/images/profile/default.png";

    if($data['profile_img_loc'] != ""){
        $avatar_src = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
    }

    $data['profile_img_loc'] = $avatar_src;

    return View('socialNetwork.index', $data);
  }

  public function changePassword(){
		$data["error_msg"] = "";
		$data["pass_not_match"] = "";
		$data["success_msg"] = "";
		$input = Request::all();

		if(isset($input["action"]) && $input["action"]=="change_pass"){
			$user=User::find(Auth::user()->id);
			$validator = Validator::make( $input, [
				'old_pass' => 'required',
				'new_pass' => 'required',
				'verify_pass' => 'required|same:new_pass'],[
				'old_pass.required' => ' The Old Password field is required.',
				'new_pass.min' => ' The Verify Password and Old Password Must be same.',
				] );
			if($input['new_pass']!=$input['verify_pass']){
				$data["pass_not_match"] = "New Password & Verify Password not matched";
			}else if (Hash::check($input['old_pass'],$user->password))
			{
				$user->password=Hash::make($input['new_pass']);
				$user->update();
				$data["success_msg"] =  "Account Password Changed Successfully.";
			}
			else
			{
				$data["error_msg"] = "Old Password not matched";
			}
		}

    return response()->json($data);
	}

	public function getSettingData(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (isset($data['phone'])) {

			$tmp_phone = explode(' ', $data['phone']);

			if (count($tmp_phone) == 1) {
				$phone = $tmp_phone[0];
			}elseif (count($tmp_phone) == 2) {
				$phone = $tmp_phone[1];
			}else{
				$phone = $data['phone'];
			}
		}else{
			$phone = null;
		}

		$data['phone_without_calling_code'] = $phone;

		$snn = new SettingNotificationName;
    $snas = new UserAccountSettings;

		$data['setting_notification'] = $snn->getSettingNotifications();
    $data['account_settings'] = $snas->getUserAccountSettings($data['user_id']);

		$user = User::find($data['user_id']);
		if( isset($user) ){
			$data['verified_phone'] = $user->verified_phone;
		}

		$sdpl = SettingDataPreferenceLog::on('rds1')->where('user_id', $data['user_id'])
													->orderBy('id', 'DESC')
													->first();

		if (isset($sdpl)) {
			$data['setting_notification']['data_preferences']['all'] = $sdpl->optin;

			$data['setting_notification']['data_preferences']['lcca'] 		= $sdpl->lcca;
			$data['setting_notification']['data_preferences']['st_patrick'] = $sdpl->st_patrick;
			$data['setting_notification']['data_preferences']['lbsf'] 		= $sdpl->lbsf;
			$data['setting_notification']['data_preferences']['gisma'] 		= $sdpl->gisma;
			$data['setting_notification']['data_preferences']['aul'] 		= $sdpl->aul;
			$data['setting_notification']['data_preferences']['bsbi'] 		= $sdpl->bsbi;
			$data['setting_notification']['data_preferences']['tsom'] 		= $sdpl->tsom;
			$data['setting_notification']['data_preferences']['tlg'] 		= $sdpl->tlg;
			$data['setting_notification']['data_preferences']['ulaw'] 		= $sdpl->ulaw;

		}
		return response()->json($data);
	}

  public function settings(){

    $viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['active_tab'] = 'setting';

		if(isset($page)){
			$data['active_tab'] = $page;
		}

		$data['title'] = 'Plexuss Setting Page';
		$data['currentPage'] = 'setting';

		if (isset($data['profile_img_loc']) && !empty($data['profile_img_loc'])) {
			$data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
		}

		$data['userinfo'] = array(
			'id' => $data["user_id"],
			'fname' => $data["fname"],
			'lname' => $data["lname"],
			'zip' => $data["zip"]
		);

		if ( !$data['email_confirmed'] ) {
			array_push( $data['alerts'],
				array(
					'img' => '/images/topAlert/envelope.png',
					'type' => 'hard',
					'dur' => '10000',
					'msg' => '<span class=\"pls-confirm-msg\">Please confirm your email to gain full access to Plexuss tools.</span> <span class=\"go-to-email\" data-email-domain=\"'.$data['email_provider'].'\"><span class=\"hide-for-small-only\">Go to</span><span class=\"show-for-small-only\">Confirm</span> Email</span> <span> | <span class=\"resend-email\">Resend <span class=\"hide-for-small-only\">Email</span></span></span> <span> | <span class=\"change-email\" data-current-email=\"'.$data['email'].'\">Change email <span class=\"hide-for-small-only\">address</span></span></span>'
				)
			);

		}

		$contactList = array();
		if(Session::has('invite_contactList')){
			$contactList = Session::get('invite_contactList');
		}
		if (isset($data['super_admin']) && $data['super_admin'] == 1) {
			// $data = $this->getManageUsers($data);
		}

		$data['contactList'] = $contactList;

		$states_names = State::all();
		$states = array('' => 'Select...');

		if( isset($states_names) && !empty($states_names) ){
			foreach ($states_names as $key => $value) {
				$states[$value->state_abbr] = $value->state_name;
			}
		}

		$data['states'] = $states;

		$opc = new OmniPayController;
		$data['paymentInfo'] = $opc->retrieveCustomer();

		$adminText = AdminText::where('org_branch_id', $data['org_branch_id'])->first();

		if(isset($adminText) && !empty($adminText)) {
			$data['num_of_free_texts'] = $adminText->num_of_free_texts;
			$data['num_of_eligble_texts'] = $adminText->num_of_eligble_texts;
			$data['textmsg_tier'] = $adminText->tier;
			$data['textmsg_expires_date'] = Carbon::parse($adminText->expires_at);
			$data['flat_fee_sub_tier'] = $adminText->flat_fee_sub_tier;
			$data['auto_renew'] = $adminText->auto_renew;
		}

		// get free text countries
        $data['free_text_countries'] = FreeTextCountry::select('country_code', 'country_name')->get();

        // get phone number
        $purchasedPhone = PurchasedPhone::where('org_branch_id', $data['org_branch_id'])->select('phone')->first();
        if(isset($purchasedPhone) && !empty($purchasedPhone)) {
        	$data['purchased_phone'] = $purchasedPhone->phone;
        }

        $data['current_time'] = Carbon::now();

        $countries_names= Country::on('rds1')->get();

        $countries = array('' => 'Select...');

		foreach ($countries_names as $key => $value) {
			$countries[$value->country_code] = $value->country_name;
		}

		$data['countries'] = $countries;

		$c = new Country;
		$data['callingCodes'] = $c->getUniqueAreaCodes();

		$country = Country::on('rds1')->find($data['country_id']);
		if( isset($country) ){
			$data['calling_code'] = $country->country_phone_code;
		}

		// Show data preferences only to GDPR users
		$arr = $this->iplookup();
		$is_gdpr = Country::on('rds1')
                              ->where('country_name', '=', $arr['countryName'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();
        if (!$is_gdpr) {
        	$is_gdpr = Country::on('rds1')
                              ->where('id', '=', $data['country_id'])
                              ->where('is_gdpr', '=', 1)
                              ->exists();
        }

        $data['is_gdpr'] = $is_gdpr;

		return View('socialNetwork.index', $data);
  }

  public function getAllUserSchools($user_id = ''){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();
    if(!empty($user_id)){
       $user = User::find($user_id);
    }else{
      $user = User::find($data['user_id']);
    }

    if (!isset($user)) {
      return NULL;
    }

    //potential school names (college & highschool) check via in_college and alumni education
    if(isset($user->current_school_id)){
      $ucol = DB::connection('rds1')->table('colleges as c')
                                    ->where('c.id', $user->current_school_id)
                                    ->select('c.school_name')
                                    ->first();

      $uhs = DB::connection('rds1')->table('high_schools as hs')
                                  ->where('hs.id', $user->current_school_id)
                                  ->select('hs.school_name')
                                  ->first();
    }else{
      $ucol = (object) ['school_name' => null];
      $uhs = (object) ['school_name' => null];
    }

    $ued = DB::connection('rds1')->table('sn_users_educations as sned')
                                 ->leftjoin('sn_users_education_majors as snum', 'snum.sue_id', '=', 'sned.id');
    if($user->in_college == 0){
      $ued = $ued->leftjoin('high_schools as sch', 'sch.id', '=', 'sned.school_id');
    } else{
      $ued = $ued->leftjoin('colleges as sch', 'sch.id', '=', 'sned.school_id');
    }

    $ued = $ued->leftjoin('majors as m', 'm.id', '=', 'snum.major_id')
               ->where('sned.user_id', $user->id)
               ->select('sch.school_name', 'sned.grad_year')
               ->orderBy('sned.grad_year', 'desc')
               ->first();

    $schools = (object) ['college' => (isset($ucol->school_name) ? $ucol->school_name : null ),
                          'highschool' => (isset($uhs->school_name) ? $uhs->school_name : null ),
                          'profile' => (isset($ued->school_name) ? $ued->school_name : null ),];

    return $schools;
  }

  public function getUser() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $ucq = UsersCustomQuestion::on('rds1')->where('user_id', $data['user_id'])->first();

    if (isset($ucq)) {
      $data['oneapp_step'] = $ucq->application_state;
    }

    $snas = new UserAccountSettings;
    $data['userAccountSettings'] = $snas->getUserAccountSettings($data['user_id']);
    $data['user_id'] = $this->hashIdForSocial($data['user_id']);
    $data['user_school_names'] = $this->getAllUserSchools();

    return response()->json($data);
  }

  private function checkPostFilter($user_id, $input){

    $tmp = CollegeRecommendationFilters::on('rds1');


    if (isset($input['post_id'])) {
      $tmp = $tmp->where('post_id', $input['post_id']);
    }

    if (isset($input['social_article_id'])) {
      $tmp = $tmp->where('social_article_id', $input['social_article_id']);
    }

    $tmp = $tmp->first();

    if (!isset($tmp)) {
      return true;
    }

    $crc = new CollegeRecommendationController;
    $ret = $crc->filterThisPostForThisUser($user_id, $input);

    return $ret;
  }

  private function applyShareWithConditions($qry, $data, $table_name){

    if (isset($data['signed_in']) &&  $data['signed_in'] ==  1) {

      $snfr = new FriendRelation;
      $arr = $snfr->getMyConnections($data['user_id']);

      $qry = $qry->where(function($q) use($data, $arr, $table_name){
                        $q->orWhere($table_name.'.share_with_id', '=', DB::raw(1)); // Share with Public
                        $q->orWhere($table_name.'.user_id', '=', DB::raw($data['user_id']));
                        if (isset($data['is_organization']) && $data['is_organization'] === 1) {
                          $q->orWhere($table_name.'.share_with_id', '=', DB::raw(3));  // Only Me & Colleges
                        }
                        $q->orWhere(function($query) use ($arr, $table_name){
                            $query->whereIn($table_name.'.user_id', $arr)
                                  ->where(function($inner_q) use ($table_name){
                                      $inner_q->orWhere($table_name.'.share_with_id', '=', DB::raw(1))
                                              ->orWhere($table_name.'.share_with_id', '=', DB::raw(2));
                                  });
                        });

                      });
    }else{
      $qry = $qry->where($table_name.'.share_with_id', 1);
    }
    return $qry;
  }

  private function applyHiddenCondition($qry, $data, $table_name){

    if (isset($data['signed_in']) &&  $data['signed_in'] ==  1) {
      if ($table_name == 'sn_posts') {
        $qry = $qry->whereRaw("NOT EXISTS (Select 1 from sn_hidden_articles_posts as snh where snh.user_id = ".$data['user_id']." and snh.post_id = ".$table_name.".id)");
      }else{
        $qry = $qry->whereRaw("NOT EXISTS (Select 1 from sn_hidden_articles_posts as snh where snh.user_id = ".$data['user_id']." and snh.social_article_id = ".$table_name.".id)");
      }
    }

    return $qry;
  }

  public function getHomePosts() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    $request = Request::all();
    isset($request['offset']) ? $offset = $request['offset'] : $offset = 0;

    $posts = Post::on('rds1')->with('user:id,fname,lname,profile_img_loc,is_student,country_id,in_college,current_school_id',
                                    'user.country:id,country_code',
                                    'user.highschool:id,school_name',
                                    'user.college:id,school_name',
                                    'comments.userAccountSettings', 'comments.likes', 'comments.user:id,fname,lname,profile_img_loc,country_id',
                                    'comments.user.country:id,country_code', 'comments.images', 'likes', 'images', 'userAccountSettings:*')
                              ->where('post_status','=',1)
                              ->offset($offset*10)
                              ->limit(10)
                              ->orderBy('created_at', 'DESC');

    $posts = $this->applyShareWithConditions($posts, $data, 'sn_posts');
    $posts = $this->applyHiddenCondition($posts, $data, 'sn_posts');

    $posts = $posts->get()
                    // Manually going through any posts & comments with settings and applying them after the fact.
                    // Causes a decent amount of slowdown. Try to find another solution.
                    ->map(function ($post) use ($data) {
                      if(($post->user_id != $data['user_id']) && !is_null($post->userAccountSettings)){
                        $post->user = $this->applyUserAccountSettings($post->user_id, $post->user);
                      }
                      foreach($post->comments as $key => $value){
                        if(($value->user_id != $data['user_id']) && !is_null($value->userAccountSettings)){
                          $value->user = $this->applyUserAccountSettings($value->user_id, $value->user);
                        }
                      }
                      return $post;
                    });

    $encode = json_encode($posts);
    $posts  = json_decode($encode);

    foreach ($posts as $key => $value) {
      if ($value->posted_by_plexuss == 1) {

        $tmp_input = array();
        $tmp_input['post_id'] = $value->id;

        $check = $this->checkPostFilter($data['user_id'], $tmp_input);
        if ($check == false) {
          unset($posts[$key]);
        }
      }

      $value->id         = $this->hashIdForSocial($value->id);
      $value->user_id    = $this->hashIdForSocial($value->user_id);
      isset($value->original_post_id) ? $value->original_post_id = $this->hashIdForSocial($value->original_post_id) : NULL;
      isset($value->share_post_id) ? $value->share_post_id = $this->hashIdForSocial($value->share_post_id) : NULL;
      $value->comments   = $this->hashALoop($value->comments);
      $value->likes      = $this->hashALoop($value->likes);
      $value->user       = $this->hashALoopNotAnArray($value->user);

      isset($value->tags) ? $value->tags = $this->hashALoop($value->tags) : NULL;
      $value->images     = $this->hashALoop($value->images);
    }

    $articles = SocialArticle::on('rds1')->with('user:id,fname,lname,profile_img_loc,is_student,country_id,in_college,current_school_id',
                                              'user.country:id,country_code',
                                              'user.highschool:id,school_name',
                                              'user.college:id,school_name',
                                              'comments.userAccountSettings', 'comments.likes',
                                              'comments.user:id,fname,lname,profile_img_loc,country_id',
                                              'comments.user.country:id,country_code', 'comments.images', 'likes', 'images', 'userAccountSettings:*')
                                         ->where('status', 1)
                                         ->offset($offset*10)
                                         ->limit(10)
                                         ->orderBy('created_at', 'DESC');

    $articles = $this->applyShareWithConditions($articles, $data, 'sn_articles');
    $articles = $this->applyHiddenCondition($articles, $data, 'sn_articles');

    $articles = $articles->get()
                          ->map(function ($article) use ($data) {
                                if(($article->user_id != $data['user_id']) && !is_null($article->userAccountSettings)){
                                  $article->user = $this->applyUserAccountSettings($article->user_id, $article->user);
                                }
                                foreach($article->comments as $key => $value){
                                  if(($value->user_id != $data['user_id']) && !is_null($value->userAccountSettings)){
                                    $value->user = $this->applyUserAccountSettings($value->user_id, $value->user);
                                  }
                                }
                                return $article;
                              });

    $encode   = json_encode($articles);
    $articles = json_decode($encode);

    foreach ($articles as $key => $value) {
      if ($value->posted_by_plexuss == 1) {

        $tmp_input = array();
        $tmp_input['social_article_id'] = $value->id;

        $check = $this->checkPostFilter($data['user_id'], $tmp_input);
        if ($check == false) {
          unset($articles[$key]);
        }
      }

      $value->id         = $this->hashIdForSocial($value->id);
      $value->user_id    = $this->hashIdForSocial($value->user_id);
      isset($value->original_article_id) ? $value->original_article_id = $this->hashIdForSocial($value->original_article_id) : NULL;

      $value->comments   = $this->hashALoop($value->comments);
      $value->likes      = $this->hashALoop($value->likes);
      isset($value->tags) ? $value->tags = $this->hashALoop($value->tags) : NULL;
      $value->images     = $this->hashALoop($value->images);
      $value->user       = $this->hashALoopNotAnArray($value->user);
    }

    $home_page_data = array_merge($posts, $articles);

    // sort based on created_at
    $created_at_arr = array();
    foreach ($home_page_data as $key => $value) {
      $created_at_arr[$key] = $value->created_at;
    }

    array_multisort($created_at_arr,SORT_DESC,$home_page_data);

    return response()->json($home_page_data);
  }

  public function getSalesPosts() {

    $request = Request::all();
    isset($request['page']) ? $offset = $request['page'] : $offset = 0;

    $posts = Post::on('rds1')->with('user:id,fname,lname,profile_img_loc,is_student', 'comments.likes',
                                    'comments.user:id,fname,lname,profile_img_loc', 'comments.images',
                                    'likes', 'images')
                              ->where('post_status', 1)
                              // ->where('posted_by_plexuss', 1)
                              // ->offset($offset*10)
                              // ->limit(10)
                              ->orderBy('updated_at', 'DESC');

    isset($request['only_plexuss']) ? $posts = $posts->where('posted_by_plexuss', 1) : NULL;

    $posts = $posts->paginate(10);

    $encode = json_encode($posts);
    $posts  = json_decode($encode);

    foreach ($posts->data as $key => $value) {

      $value->id         = $this->hashIdForSocial($value->id);
      $value->user_id    = $this->hashIdForSocial($value->user_id);
      isset($value->original_post_id) ? $value->original_post_id = $this->hashIdForSocial($value->original_post_id) : NULL;
      isset($value->share_post_id) ? $value->share_post_id = $this->hashIdForSocial($value->share_post_id) : NULL;
      $value->comments   = $this->hashALoop($value->comments);
      $value->likes      = $this->hashALoop($value->likes);
      $value->user       = $this->hashALoopNotAnArray($value->user);

      isset($value->tags) ? $value->tags = $this->hashALoop($value->tags) : NULL;
      $value->images     = $this->hashALoop($value->images);
    }

    return response()->json($posts);
  }

  private function applyPublicProfileSettings($auth_user, $request_user, $data) {
    foreach ($data['publicProfileSettings'] as $key => $value) {
      if( $key == 'user_id' ){ continue; }

      if( ($value == 2 && !in_array($auth_user, $data['friendsList'])) || ($value == 3 && !isset($data['user']->is_organization)) ){
        switch($key){
          case "claim_to_fame":           $data['claimToFame'] = null; break;
          case "objective":               $data['objective'] = null; break;
          case "skills_endorsements":     $data['skillsAndEndorsement'] = null; break;
          case "projects_publications":   $data['projectsAndPublications'] = null; break;
          case "liked_colleges":          $data['likedColleges'] = null; break;
        }
      }
    }
    return $data;
  }

  public function getProfile() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $ppc = new ProfilePageController();
    $snpps = new PublicProfileSettings;
    $sna = new SocialArticle;
    $snas = new UserAccountSettings;
    $snfr = new FriendRelation;

    $request = Request::all();
    $user = new User;
    $auth_user_id = $data['user_id'];
    $userId = $this->decodeIdForSocial($request['user_id']);

    // if MyCounselor add the view application
    if ($data['user_id'] == 1408142) {
      $tmp_uid = Crypt::encrypt($userId);
      $data['student_app_url'] = 'view-student-application/'.$tmp_uid;
    }

    $min_id = $max_id = 0;

    if($userId < $auth_user_id) {
      $min_id = $userId;
      $max_id = $auth_user_id;
    }
    else{
      $min_id = $auth_user_id;
      $max_id = $userId;
    }

    $relation_status = FriendRelation::on('rds1')->select('relation_status','action_user')
                                     ->where('user_one_id', $min_id)
                                     ->where('user_two_id', $max_id)
                                     ->get();

    $tmp_dt = $user->getUsersInfo($userId);

    $dt     =  array();
    $dt     = (object)$dt;
    $dt->user_id = $this->hashIdForSocial($tmp_dt->user_id);
    isset($tmp_dt->fname)             ? $dt->fname = $tmp_dt->fname : $dt->fname = NULL;
    isset($tmp_dt->lname)             ? $dt->lname = $tmp_dt->lname : $dt->lname = NULL;
    isset($tmp_dt->user_type)         ? $dt->user_type = $tmp_dt->user_type : $dt->user_type = NULL;
    isset($tmp_dt->profile_img_loc)   ? $dt->profile_img_loc = $tmp_dt->profile_img_loc : $dt->profile_img_loc = NULL;
    isset($tmp_dt->collegeName)       ? $dt->collegeName = $tmp_dt->collegeName : $dt->collegeName = NULL;
    isset($tmp_dt->collegeState)      ? $dt->collegeState = $tmp_dt->collegeState : $dt->collegeState = NULL;
    isset($tmp_dt->country_code)      ? $dt->country_code = $tmp_dt->country_code : $dt->country_code = NULL;
    isset($tmp_dt->country_name)      ? $dt->country_name = $tmp_dt->country_name : $dt->country_name = NULL;
    isset($tmp_dt->currentSchoolName) ? $dt->currentSchoolName = $tmp_dt->currentSchoolName : $dt->currentSchoolName = NULL;
    isset($tmp_dt->edu_level)         ? $dt->edu_level = $tmp_dt->edu_level : $dt->edu_level = NULL;
    isset($tmp_dt->gender)            ? $dt->gender = $tmp_dt->gender : $dt->gender = NULL;
    isset($tmp_dt->gradYear)          ? $dt->gradYear = $tmp_dt->gradYear : $dt->gradYear = NULL;
    isset($tmp_dt->grad_year)         ? $dt->grad_year = $tmp_dt->grad_year : $dt->grad_year = NULL;
    isset($tmp_dt->hsCity)            ? $dt->hsCity = $tmp_dt->hsCity : $dt->hsCity = NULL;
    isset($tmp_dt->hsName)            ? $dt->hsName = $tmp_dt->hsName : $dt->hsName = NULL;
    isset($tmp_dt->hsState)           ? $dt->hsState = $tmp_dt->hsState : $dt->hsState = NULL;
    isset($tmp_dt->in_college)        ? $dt->in_college = $tmp_dt->in_college : $dt->in_college = NULL;
    isset($tmp_dt->degree_name)       ? $dt->degree_name = $tmp_dt->degree_name : $dt->degree_name = NULL;
    isset($tmp_dt->major_name)        ? $dt->major_name = $tmp_dt->major_name : $dt->major_name = NULL;
    isset($tmp_dt->profession_name)   ? $dt->profession_name = $tmp_dt->profession_name : $dt->profession_name = NULL;
    isset($tmp_dt->occupation_name)   ? $dt->occupation_name = $tmp_dt->occupation_name : $dt->occupation_name = NULL;


    $data['user'] = $dt;
    $data['user']->user_school_names = $this->getAllUserSchools($userId);

    $data['relation_status'] = $relation_status;
    if(!empty($data['relation_status'][0])) {
      $data['relation_status'][0]->action_user= $this->hashIdForSocial($data['relation_status'][0]->action_user);
    }
    $hashedUserId    = Crypt::encrypt($userId);
    $data['claimToFame'] = $ppc->getProfileClaimToFame($hashedUserId);
    $data['education'] = $ppc->getEducation($userId);
    $data['objective']= array();
    $data['objective']['degree_name'] = $data['user']->degree_name;
    $data['objective']['major_name'] = $data['user']->major_name;
    $data['objective']['profession_name'] = $data['user']->profession_name;
    $data['occupation']= array('occupation_name' => $data['user']->occupation_name);
    $data['skillsAndEndorsement'] = $ppc->getSkillsAndEndorsements($hashedUserId);
    $data['projectsAndPublications'] = $ppc->getProjectsAndPublications($hashedUserId);
    $data['articles'] =  $sna->getSocialArticles($userId, true);
    $data['likedColleges'] = $ppc->getLikedColleges($hashedUserId);
    $data['publicProfileSettings'] = $snpps->getPublicProfileSettings($userId);
    $data['userAccountSettings'] = $snas->getUserAccountSettings($userId);
    $data['friendsList'] = $snfr->getMyConnections($userId);

    if($auth_user_id != $userId && isset($data['publicProfileSettings'])){
      $data = $this->applyPublicProfileSettings($auth_user_id, $userId, $data);
    }

    unset($data['friendsList']);

    if($auth_user_id != $userId && isset($data['userAccountSettings'])){
      $data['user'] = $this->applyUserAccountSettings($userId, $data['user']);
      if ($data['user']->profile_img_loc == 'incognito.png'){
        $data['user']->profile_img_loc = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/incognito.png';
      }
    }
    $data['user_id'] = $this->hashIdForSocial($data['user_id']);

    return response()->json($data);
  }

  public function getProfileCompleteness(){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $ret = array();

    $user  = User::on('rds1')->find($data['user_id']);
    $ret['profile_percent'] = $user->profile_percent;

    if(!isset($user->profile_img_loc)){
      $ret['step'] = 'profile-picture';

      return json_encode($ret);
    }

    if(!isset($user->fname) || !isset($user->lname) || !isset($user->in_college) ){
      // || (!isset($user->hs_grad_year) && (!isset($user->college_grad_year)) )){

      $ret['step'] = 'basic-info';

      return json_encode($ret);

    }

    $check = UserEducation::on('rds1')
                           ->where('user_id', $data['user_id'])
                           ->first();

    if(!isset($check)){

      $ret['step'] = 'education';

      return json_encode($ret);

    }

    $check = PublicProfileClaimToFame::on('rds1')
                                     ->where('user_id', $data['user_id'])
                                     ->first();

    if (!isset($check)) {
      $ret['step'] = 'claim-to-fame';

      return json_encode($ret);
    }

    if($user->in_college == 0){
      $check = Objective::on('rds1')
                        ->where('user_id', $data['user_id'])
                        ->first();

      if (!isset($check)) {
        $ret['step'] = 'objective';

        return json_encode($ret);
      }
    }else if($user->in_college == 1){
      $check = Occupation::on('rds1')
                        ->where('user_id', $data['user_id'])
                        ->first();

      if (!isset($check)) {
        $ret['step'] = 'occupation';

        return json_encode($ret);
      }
    }

    $check = PublicProfileSkills::on('rds1')
                                ->where('user_id', $data['user_id'])
                                ->first();

    if (!isset($check)) {
      $ret['step'] = 'skills-endorsements';

      return json_encode($ret);
    }

    $check = PublicProfileProjectsAndPublications::on('rds1')
                      ->where('user_id', $data['user_id'])
                      ->first();

    if (!isset($check)) {
      $ret['step'] = 'projects-publications';

      return json_encode($ret);
    }

    $ret['step'] = 'complete';

    return json_encode($ret);
  }

  public function getProfilePosts() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    $request['user_id'] = $this->decodeIdForSocial($request['user_id']);

    //if user is incognito, do not show any posts
    $snas = new UserAccountSettings;
    $settings = $snas->getUserAccountSettings($request['user_id']);
    if($request['user_id'] != $data['user_id'] && isset($settings) && $settings->is_incognito == 1){
      return NULL;
    }

    isset($request['offset']) ? $offset = $request['offset'] : $offset = 0;
    $take = 20;

    $profile_posts = Post::on('rds1')->with('user:id,fname,lname,profile_img_loc,is_student,country_id,in_college,current_school_id',
                                            'user.country:id,country_code',
                                            'user.highschool:id,school_name',
                                            'user.college:id,school_name',
                                    'comments.userAccountSettings', 'comments.likes', 'comments.user:id,fname,lname,profile_img_loc,country_id',
                                    'comments.user.country:id,country_code', 'comments.images', 'userAccountSettings:*', 'likes', 'images')
                              ->where('post_status','=',1)
                              ->where('user_id', $request['user_id'])
                              ->offset($offset*$take)
                              ->limit($take)
                              ->orderBy('updated_at', 'DESC');

    // if you are looking your own profile page show everything, if you are looking at someone else profile
    // then we need to make sure the posts can be shown to the user who is viewing this
    if ($data['user_id'] !== $request['user_id']) {
      $profile_posts = $this->applyShareWithConditions($profile_posts, $data, 'sn_posts');
      $profile_posts = $this->applyHiddenCondition($profile_posts, $data, 'sn_posts');
    }

    $profile_posts = $profile_posts->get()
                                    // Manually going through any posts & comments with settings and applying them after the fact.
                                    // Causes a decent amount of slowdown. Try to find another solution.
                                    ->map(function ($post) use ($data) {
                                      if(($post->user_id != $data['user_id']) && !is_null($post->userAccountSettings)){
                                        $post->user = $this->applyUserAccountSettings($post->user_id, $post->user);
                                      }
                                      foreach($post->comments as $key => $value){
                                        if(($value->user_id != $data['user_id']) && !is_null($value->userAccountSettings)){
                                          $value->user = $this->applyUserAccountSettings($value->user_id, $value->user);
                                        }
                                      }
                                      return $post;
                                    });

    $encode        = json_encode($profile_posts);
    $profile_posts = json_decode($encode);

    foreach ($profile_posts as $key => $value) {
      if ($value->posted_by_plexuss == 1) {

        $tmp_input = array();
        $tmp_input['post_id'] = $value->id;

        $check = $this->checkPostFilter($data['user_id'], $tmp_input);
        if ($check == false) {
          unset($profile_posts[$key]);
        }
      }

      $value->id         = $this->hashIdForSocial($value->id);
      $value->user_id    = $this->hashIdForSocial($value->user_id);
      isset($value->original_post_id) ? $value->original_post_id = $this->hashIdForSocial($value->original_post_id) : NULL;

      $value->comments   = $this->hashALoop($value->comments);
      $value->likes      = $this->hashALoop($value->likes);
      $value->user       = $this->hashALoopNotAnArray($value->user);

      isset($value->tags) ? $value->tags = $this->hashALoop($value->tags) : NULL;
      $value->images     = $this->hashALoop($value->images);
    }


    $articles = SocialArticle::on('rds1')->with('user:id,fname,lname,profile_img_loc,is_student,country_id,in_college,current_school_id',
                                                'user.country:id,country_code',
                                                'user.highschool:id,school_name',
                                                'user.college:id,school_name',
                                                'comments.userAccountSettings', 'comments.likes', 'comments.user:id,fname,lname,profile_img_loc,country_id',
                                                'comments.user.country:id,country_code', 'userAccountSettings:*', 'comments.images', 'likes', 'images')
                                         ->offset($offset*$take)
                                         ->limit($take)
                                         ->where('status', 1)
                                         ->where('user_id', $request['user_id'])
                                         ->orderBy('updated_at', 'DESC');

    // if you are looking your own profile page show everything, if you are looking at someone else profile
    // then we need to make sure the posts can be shown to the user who is viewing this
    if ($data['user_id'] !== $request['user_id']) {
      $articles = $this->applyShareWithConditions($articles, $data, 'sn_articles');
      $articles = $this->applyHiddenCondition($articles, $data, 'sn_articles');
    }

    $articles = $articles->get()
                          // Manually going through any posts & comments with settings and applying them after the fact.
                          // Causes a decent amount of slowdown. Try to find another solution.
                          ->map(function ($article) use ($data) {
                            if(($article->user_id != $data['user_id']) && !is_null($article->userAccountSettings)){
                              $article->user = $this->applyUserAccountSettings($article->user_id, $article->user);
                            }
                            foreach($article->comments as $key => $value){
                              if(($value->user_id != $data['user_id']) && !is_null($value->userAccountSettings)){
                                $value->user = $this->applyUserAccountSettings($value->user_id, $value->user);
                              }
                            }
                            return $article;
                          });

    $encode   = json_encode($articles);
    $articles = json_decode($encode);

    foreach ($articles as $key => $value) {
      if ($value->posted_by_plexuss == 1) {

        $tmp_input = array();
        $tmp_input['social_article_id'] = $value->id;

        $check = $this->checkPostFilter($data['user_id'], $tmp_input);
        if ($check == false) {
          unset($articles[$key]);
        }
      }

      $value->id         = $this->hashIdForSocial($value->id);
      $value->user_id    = $this->hashIdForSocial($value->user_id);
      isset($value->original_article_id) ? $value->original_article_id = $this->hashIdForSocial($value->original_article_id) : NULL;

      $value->comments   = $this->hashALoop($value->comments);
      $value->likes      = $this->hashALoop($value->likes);
      isset($value->tags) ? $value->tags = $this->hashALoop($value->tags) : NULL;
      $value->images     = $this->hashALoop($value->images);
      $value->user       = $this->hashALoopNotAnArray($value->user);
    }

    $response = array_merge($profile_posts, $articles);

    // $profile_posts = Post::with('user:id,fname,lname,profile_img_loc,is_student', 'comments.likes','comments.user:id,fname,lname,profile_img_loc', 'comments.images', 'likes', 'images')->where('user_id', $data['user_id'])->skip($offset*$take)->take($take)->get();

    return response()->json($response);
  }

  public function savePublicProfileSettings(){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    $profile_settings = PublicProfileSettings::on('rds1')
                                     ->where('user_id', $data['user_id'])
                                     ->first();

    if(isset($profile_settings)){
      $profile_settings = $profile_settings->toArray();
      $profile_settings[$request['section']] = $request['share_with_id'];
    }
    else{
      $profile_settings['user_id'] = $data['user_id'];
      $profile_settings['basic_info'] = 1;
      $profile_settings['claim_to_fame'] = 1;
      $profile_settings['objective'] = 1;
      $profile_settings['skills_endorsements'] = 1;
      $profile_settings['projects_publications'] = 1;
      $profile_settings['liked_colleges'] = 1;
      $profile_settings[$request['section']] = $request['share_with_id'];
    }

    $snpps = new PublicProfileSettings;

    $snpps->insertOrUpdate($profile_settings);

    return 'OK';
  }

  public function updatePostShareCount($request = NULL){
    if (!isset($request)) {
      $request = Request::all();
    }
    $post = Post::find($this->decodeIdForSocial($request['post_id']));
    if($request['update'] == 'increment'){
      $post->share_count += 1;
    }else{
      $post->share_count -= 1;
    }
    $post->updated_at = Carbon::now('UTC');

    if ($post->save()) {

      $publish_data = array();
      $publish_data['thread_room'] = $request['thread_room'];
      $publish_data['id'] = $request['post_id'];
      $publish_data['share_count'] = $post->share_count;
      $publish_data['type'] = 'post';
      Redis::publish('update:shareCount', json_encode($publish_data));

      return response()->json(array('status' => "post shared count has been updated successfully.", 'success' => true), 200);
    }else{
      return response()->json(array('success' => false), 500);
    }
  }

  public function dupPost($request = NULL){

    if (!isset($request)) {
      $request = Request::all();
    }

    $post = Post::find($request['sales_pid']);
    $dupost = $post->replicate();
    $dupost->save();

    return response()->json(array('status' => "post has been successfully duplicated.",
                                  'success' => true, 'post_id' => $dupost->id), 200);
  }

  private function publishPostToThisUser($request, $publish_data){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    // POSTS FROM SALES
    if (isset($request['posted_by_plexuss']) && $request['posted_by_plexuss'] == 1) {

      $online_users_arr = Redis::hvals('online:users');
      $already_seen_users_arr = array();
      foreach ($online_users_arr as $k => $v) {
        if ($v == "") {
          continue;
        }
        $check = $this->checkPostFilter($v, $request);
        if ($check && !in_array($v, $already_seen_users_arr)) {
          $publish_data['thread_room'] = 'post:room:'. $this->hashIdForSocial($v);
          Redis::publish('publish:post', json_encode($publish_data));
        }
        $already_seen_users_arr[] = $v;
      }

    }elseif(isset($request['share_with_id'])){ // ALL USERS POSTS GOES HERE

      switch ($request['share_with_id']) {
        // public
        case 1:
          Redis::publish('publish:post', json_encode($publish_data));
          break;

        // My Connections only
        case 2:
          $publish_data['thread_room'] = 'post:room:'. $this->hashIdForSocial($data['user_id']);
          Redis::publish('publish:post', json_encode($publish_data));
          $snfr = new FriendRelation;
          $arr = $snfr->getMyConnections($data['user_id']);

          $online_users_arr = Redis::hvals('online:users');

          foreach ($arr as $k => $v) {
            if ($v == "") {
              continue;
            }
            if (in_array((int)$v, $online_users_arr)) {
              $publish_data['thread_room'] = 'post:room:'. $this->hashIdForSocial($v);
              Redis::publish('publish:post', json_encode($publish_data));
            }
          }

          break;
        // Only Me & Colleges
        case 3:
          $publish_data['thread_room'] = 'post:room:'. $this->hashIdForSocial($data['user_id']);
          Redis::publish('publish:post', json_encode($publish_data));
          $snfr = new FriendRelation;
          $arr = $snfr->getMyConnections($data['user_id']);

          $online_users_arr = Redis::hvals('online:users');
          $already_seen_users_arr =  array();

          foreach ($arr as $k =>$v) {
            if (in_array($v, $online_users_arr) && $v != $data['user_id'] && !in_array($v, $already_seen_users_arr)) {

              $user = User::on('rds1')->find($v);

              if ($user->is_organization == 1) {
                $publish_data['thread_room'] = 'post:room:'. $this->hashIdForSocial($v);
                Redis::publish('publish:post', json_encode($publish_data));
              }

              $already_seen_users_arr[] = $v;
            }
          }
          break;
        // Only Me
        case 4:
          $publish_data['thread_room'] = 'post:room:'. $this->hashIdForSocial($data['user_id']);
          Redis::publish('publish:post', json_encode($publish_data));

          break;
      }
    }else{
      Redis::publish('publish:post', json_encode($publish_data));
    }
  }

  public function hidePostArticle(){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    $attr = array('user_id' => $data['user_id'], 'post_id' => $this->decodeIdForSocial($request['post_id']), 'social_article_id' => $this->decodeIdForSocial($request['social_article_id']));

    SocialHiddenArticlesPost::updateOrCreate($attr, $attr);

    return response()->json(array('status' => "post has been hidden successfully.", 'success' => true), 200);
  }

  public function undoHidePostArticle(){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    $shap = SocialHiddenArticlesPost::where('user_id', $data['user_id']);

    isset($request['post_id']) ? $shap = $shap->where('post_id', $this->decodeIdForSocial($request['post_id'])) : NULL;
    isset($request['social_article_id']) ? $shap = $shap->where('social_article_id', $this->decodeIdForSocial($request['social_article_id'])) : NULL;

    $shap = $shap->delete();

    return response()->json(array('status' => "post has been hidden successfully.", 'success' => true), 200);
  }

  public function savePost($request = NULL) {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $now = Carbon::now();

    if (!isset($request)) {
      $request = Request::all();
    }
    if (isset($request['sales_pid'])) {
      $post = Post::find($this->decodeIdForSocial($request['sales_pid']));

    }elseif (isset($request['post_id'])) {
      $post = Post::find($this->decodeIdForSocial($request['post_id']));

      if ($post->user_id != $data['user_id']) {
        return response()->json(array('status' => 'You are not allowed to edit this post.'), 500);
      }
      // EDIT POST REMOVE THE IMAGES HERE
      if (isset($request['remove_images'][0]) && !empty($request['remove_images'][0])) {
        foreach ($request['remove_images'] as $key => $value) {
          $post_img  = PostImage::where('id', $this->decodeIdForSocial($value))
                                ->where('post_id', $this->decodeIdForSocial($request['post_id']))
                                ->first();

          if (!isset($post_img)) {
            return response()->json(array('status' => 'You are not allowed to remove this image.'), 500);
          }

          $this->deletePostImage($post_img);
        }
      }
    }else{

      $post = new Post();
      $post->created_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');
    }

    $post->updated_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');
    isset($request['title']) ? $post->title = $request['title'] : NULL;
    isset($request['posted_by_plexuss']) ? $post->posted_by_plexuss = $request['posted_by_plexuss'] : NULL;
    isset($request['post_status']) ? $post_status = $request['post_status'] : $post_status = true;


    $post->user_id = $data['user_id'];
    $post->post_text = $request['post_text'];

    // check if the url is good before submitting it
    if (isset($request['shared_link'])) {
      $tmp_arr = [];
      $tmp_arr['url'] = $request['shared_link'];
      $is_good_url = json_decode($this->getLinkPreview($tmp_arr));

      if (isset($is_good_url->status) && $is_good_url->status  == 'success') {
        $post->shared_link = $request['shared_link'];
      }

    }

    $post->share_with_id = $request['share_with_id'];
    $post->share_count = $request['share_count'];
    $post->post_status = $post_status;

    if($request['is_shared'] == 'true' || $request['is_shared'] == 1 ){
      $post->share_type = $request['share_type'];
      $post->share_post_id = $this->decodeIdForSocial($request['share_post_id']);
      $post->share_post_type = $request['share_post_type'];
      $post->is_shared = true;
    }else{
      $post->is_shared = false;
    }
    $post->original_post_id = $this->decodeIdForSocial($request['original_post_id']);
    if ($post->save()) {

      if($request['is_shared'] == 'true'){

        if ($this->decodeIdForSocial($request['user_id']) != $this->decodeIdForSocial($request['target_id'])) {
          /* Push new notification for target user */
          $user_name = $request['user_name'];
          $ntn = new NotificationController();
          $saved_ntn = $ntn->create( $user_name, 'user', 14, null, $this->decodeIdForSocial($request['user_id']) , $this->decodeIdForSocial($request['target_id']), null, null, $post->original_post_id);

          $ntn_data['thread_room'] = $request['target_id'];
          $ntn = DB::table('notification_topnavs')->find($saved_ntn);
          $msg = $ntn->msg;

          if ($ntn->icon_img_route == "users_image") {
            $user = DB::connection('rds1')->table('users as u')
                            ->select(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as student_profile_photo'))
                            ->where('id', $data['user_id'])
                            ->first();
            if (isset($user->student_profile_photo)) {
              $ntn->icon_img_route = $user->student_profile_photo;
            }
          }
          $ntn = json_encode($ntn);
          $ntn = json_decode($ntn);
          $ntn = $this->hashALoopNotAnArray($ntn);

          $ntn_data['notification_data'] = $ntn;
          Redis::publish('push:notification', json_encode($ntn_data));
          $url = "post/".$this->hashIdForSocial($post->id);
          $this->pushNotification($request['user_id'], $request['target_id'], $url, $msg);
        }
      }

      if ($request['is_gif'] == 'true') {
        $image = new PostImage();
        $image->post_id = $post->id;
        $image->post_comment_id = null;
        $image->social_article_id = null;
        $image->is_gif = true;
        $image->gif_link = $request['gif_link'];
        $image->image_link = $request['image_link'];
        if(!$image->save())
          return response()->json(array('success' => false), 500);
      }

      if($request['post_images'] != null) {

        $files = $request['post_images'];
        $image_count = 1;

        foreach ($files as $file) {
          $filename = true;
          $response = $this->generalUploadPic($file, $filename, 'asset.plexuss.com/social');

          if ($response['status'] == 'success') {
            $file_path = $response['url'];
          }
          else{
            return response()->json(array('status' => 'could not store image to S3'), 500);
          }

          $image = new PostImage();

          $image->post_id = $post->id;
          $image->post_comment_id = null;
          $image->social_article_id = null;
          $image->is_gif = false;
          $image->gif_link = null;
          $image->image_link = $file_path;
          $image_count++;

          if(!$image->save())
            return response()->json(array('success' => false), 500);
        }
      }

      // $post = Post::with('user:id,fname,lname,profile_img_loc,is_student', 'comments.likes','comments.user:id,fname,lname,profile_img_loc', 'comments.images', 'likes', 'images')->where('id', $post->id)->get();

      /* publish new post with Redis */
      $publish_data = array();
      //for friends
      //for(user in friend_list)
      //{
      //  $publish_data['thread_room'] = 'post:room:'+friend.user_id;
      //  $publish_data['post_data'] = Post::with('user:id,fname,lname,profile_img_loc,is_student', 'comments.likes','comments.user:id,fname,lname,profile_img_loc', 'comments.images', 'likes', 'images')->where('id', $post->id)->get();
      //  $this->publishPostToThisUser($request['posted_by_plexuss'], $publish_data);
      //}
      // for private post
      // $publish_data['thread_room'] = $request['private_thread_room'];
      // $publish_data['post_data'] = Post::with('user:id,fname,lname,profile_img_loc,is_student', 'comments.likes','comments.user:id,fname,lname,profile_img_loc', 'comments.images', 'likes', 'images')->where('id', $post->id)->get();
      // $this->publishPostToThisUser($request['posted_by_plexuss'], $publish_data);
      $publish_data['thread_room'] = $request['thread_room'];
      $tmp_post = Post::with('user:id,fname,lname,profile_img_loc,is_student,country_id,in_college,current_school_id',
                              'user.country:id,country_code',
                              'user.highschool:id,school_name',
                              'user.college:id,school_name',
                              'comments.userAccountSettings','comments.likes','comments.user:id,fname,lname,profile_img_loc', 'comments.images',
                              'likes', 'images', 'userAccountSettings:*')->where('id', $post->id)->get();

      $encode = json_encode($tmp_post);
      $tmp_post  = json_decode($encode);
      $publish_data['post_data'] = $this->hashALoop($tmp_post);

      $request['post_id'] = $this->hashIdForSocial($post->id);
      $this->publishPostToThisUser($request, $publish_data);

      return response()->json(array('status' => "post has been saved successfully.", 'success' => true), 200);
    }
    else
      return response()->json(array('success' => false));
  }

  private function deletePostImage($post_img){
    $bucket_url =  str_replace("https://s3-us-west-2.amazonaws.com/", "", $post_img->image_link);
    $keyname = substr($post_img->image_link, strrpos($post_img->image_link, '/') + 1);
    $bucket_url = str_replace($keyname, "", $bucket_url);
    $tmp = $this->generalDeleteFile($bucket_url, $keyname, true);

    $post_img->delete();
  }

  public function deletePost() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    // $query = Post::destroy($request['post_id']);
    if($request['is_sales']){
      $query = Post::where('id', $this->decodeIdForSocial($request['post_id']))
                 ->delete();
    }else{
      $query = Post::where('id', $this->decodeIdForSocial($request['post_id']))
                  ->where('user_id', $data['user_id'])
                  ->delete();
    }
    if ($query) {
      $post_img = PostImage::where('post_id', $this->decodeIdForSocial($request['post_id']))
                     ->get();
      foreach ($post_img as $key) {
        $this->deletePostImage($key);
      }
      $publish_data = array();
      $publish_data['thread_room'] = $request['thread_room'];
      $publish_data['id'] = $request['post_id'];
      $publish_data['type'] = 'post';
      Redis::publish('delete:post', json_encode($publish_data));
      return response()->json(array('success' => true), 200);
    }
    else {
      return response()->json(array('success' => false), 500);
    }
  }

  public function readNotification(){
    $request = Request::all();
    $publish_data = array();
    $publish_data['thread_room'] = $request['thread_room'];
    $publish_data['id'] = $request['id'];
    Redis::publish('read:notification', json_encode($publish_data));
    return response()->json(array('success' => true), 200);
  }
  public function addThread(){
    $request = Request::all();
    $publish_data = array();
    $publish_data['thread_room'] = $request['user_thread_room'];
    $publish_data['id'] = $request['id'];
    Redis::publish('add:messageThread', json_encode($publish_data));
    $publish_data['thread_room'] = $request['thread_room'];
    $publish_data['id'] = $request['id'];
    Redis::publish('add:messageThread', json_encode($publish_data));
    return response()->json(array('success' => true), 200);
  }

  public function updatePostStatus() {

    $request = Request::all();
    $post = Post::find($this->decodeIdForSocial($request['post_id']));
    $post->post_status = $request['post_status'];

    $now = Carbon::now();
    $post->updated_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');

    if ($post->update) {
      return response()->json(array('success' => true), 200);
    }
    else {
      return response()->json(array('success' => false), 500);
    }
  }

  public function getSinglePost() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    $request['post-id'] = $this->decodeIdForSocial($request['post-id']);

    $post = Post::on('rds1')->with('user:id,fname,lname,profile_img_loc',
                                   'comments.likes','comments.user:id,fname,lname,profile_img_loc','comments.images','likes', 'views',
                                   'images')
                            ->where('id', $request['post-id']);

    $post = $this->applyShareWithConditions($post, $data, 'sn_posts');
    $post = $this->applyHiddenCondition($post, $data, 'sn_posts');
    $post = $post->get();


    if (!isset($post[0]) || empty($post[0])) {
      $ret = array();
      return response()->json($ret);
    }

    if ($post[0]->posted_by_plexuss == 1) {

      $tmp_input = array();
      $tmp_input['post_id'] = $post[0]->id;

      $check = $this->checkPostFilter($data['user_id'], $tmp_input);

      if ($check == false) {
        $ret = array();
        return response()->json($ret);
      }
    }

    if($post[0]->user_id != $data['user_id']){
      $post[0]->user = $this->applyUserAccountSettings($post[0]->user_id, $post[0]->user);
    }
    foreach($post[0]->comments as $key => $value){
      if($post[0]->comments[$key]->user_id != $data['user_id']){
        $post[0]->comments[$key]->user = $this->applyUserAccountSettings($post[0]->comments[$key]->user_id, $post[0]->comments[$key]->user);
      }
    }

    $encode = json_encode($post);
    $post = json_decode($encode);

    foreach ($post as $key => $value) {
      if ($value->posted_by_plexuss == 1) {

        $tmp_input = array();
        $tmp_input['post_id'] = $value->id;

        $check = $this->checkPostFilter($data['user_id'], $tmp_input);
        if ($check == false) {
          unset($posts[$key]);
          continue;
        }
      }

      $value->id         = $this->hashIdForSocial($value->id);
      $value->user_id    = $this->hashIdForSocial($value->user_id);
      isset($value->original_post_id) ? $value->original_post_id = $this->hashIdForSocial($value->original_post_id) : NULL;
      isset($value->share_post_id) ? $value->share_post_id = $this->hashIdForSocial($value->share_post_id) : NULL;

      $value->comments   = $this->hashALoop($value->comments);
      $value->likes      = $this->hashALoop($value->likes);
      $value->user       = $this->hashALoopNotAnArray($value->user);

      isset($value->tags) ? $value->tags = $this->hashALoop($value->tags) : NULL;
      $value->images     = $this->hashALoop($value->images);
    }

    return response()->json($post);
  }

  public function typeMsg(){
    $request = Request::all();
    $publish_data = array();
    $publish_data['thread_room'] = $request['thread_room'];
    $publish_data['thread_id'] = $request['thread_id'];
    $publish_data['user_id'] = $request['user_id'];
    Redis::publish('type:message', json_encode($publish_data));
  }

  public function cancelTyping(){
    $request = Request::all();
    $publish_data = array();
    $publish_data['thread_room'] = $request['thread_room'];
    $publish_data['thread_id'] = $request['thread_id'];
    $publish_data['user_id'] = $request['user_id'];
    Redis::publish('cancel-typing:message', json_encode($publish_data));
  }

  public function deleteComments() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    $query = PostComment::where('id', $this->decodeIdForSocial($request['comment_id']))
                        ->where('user_id', $data['user_id'])
                        ->first();
    if($query) {
      $query->delete();

      $publish_data = array();
      $publish_data['thread_room'] = $request['thread_room'];
      $publish_data['comment_id'] = $request['comment_id'];
      $publish_data['post_id'] = $request['post_id'];
      $publish_data['social_article_id'] = $request['social_article_id'];
      Redis::publish('delete:comment', json_encode($publish_data));
      return response()->json(array('success' => true), 200);
    }
    else {
      return response()->json(array('success' => false), 500);
    }
  }

  public function updateComment() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $now = Carbon::now();
    $request = Request::all();

    $comment = PostComment::where('id', $this->decodeIdForSocial($request['comment_id']))
                          ->where('user_id', $data['user_id'])
                          ->first();

    if (!isset($comment)) {
      return response()->json(array('success' => false), 500);
    }

    $comment->updated_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');
    $comment->comment_text = $request['comment_text'];
    if($request['removeImage'] != -1){
      $query = PostImage::destroy($this->decodeIdForSocial($request['removeImage']));
      if(!$query) {
        return response()->json(array('success' => false), 500);
      }
    }
    if ($comment->save()) {
      if($request['comment_images'] != -1) {
        $files = $request['comment_images'];
        $image_count = 1;

        foreach ($files as $file) {

          $filename = true;
          $response = $this->generalUploadDoc($file, $filename, 'asset.plexuss.com/social');

          if ($response['status'] == 'success') {
            $file_path = $response['url'];
          }
          else{
            return response()->json(array('status' => 'could not store image to S3'), 500);
          }

          $image = new PostImage();

          $image->post_id = $comment->post_id;
          $image->post_comment_id = $comment->id;
          $image->social_article_id = $comment->social_article_id;
          $image->is_gif = false;
          $image->gif_link = $request['gif_link'];
          $image->image_link = $file_path;
          $image_count++;

          if(!$image->save())
            return response()->json(array('success' => false), 500);
        }
      }
      $publish_data = array();
      $publish_data['thread_room'] = $request['thread_room'];
      $publish_data['comment_data'] = PostComment::on('rds1')->with('user:id,fname,lname,profile_img_loc', 'images', 'likes')
                                                             ->where('id', $this->decodeIdForSocial($request['comment_id']))
                                                             ->get();

      $encode = json_encode($publish_data['comment_data']);
      $comment_data  = json_decode($encode);
      $publish_data['comment_data'] = $this->hashALoop($comment_data);

      Redis::publish('edit:comment', json_encode($publish_data));
      return response()->json(array('success' => true), 200);
    }else{
      return response()->json(array('success' => false), 500);
    }
  }

  public function savePostComment() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $now = Carbon::now();
    $request = Request::all();
    $comment = new PostComment();

    $comment->created_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');
    $comment->updated_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');
    $comment->parent_id = $this->decodeIdForSocial($request['parent_id']);
    $comment->user_id = $this->decodeIdForSocial($request['user_id']);
    $comment->post_id = $this->decodeIdForSocial($request['post_id']);
    $comment->social_article_id = $this->decodeIdForSocial($request['article_id']);
    $comment->comment_text = $request['comment_text'];
    $comment->shared_link = $request['shared_link'];

    if ($comment->save()) {

      if ($request['is_gif'] == 'true') {
        $image = new PostImage();

        $image->post_id = $comment->post_id;
        $image->post_comment_id = $comment->id;
        $image->social_article_id = $comment->social_article_id;
        $image->is_gif = true;
        $image->gif_link = $request['gif_link'];
        $image->image_link = $request['image_link'];
        if(!$image->save())
          return response()->json(array('success' => false), 500);
      }

      if($request['comment_images'] != -1) {
        $files = $request['comment_images'];
        $image_count = 1;

        if (isset($files)) {
          foreach ($files as $file) {

            $filename = true;
            $response = $this->generalUploadDoc($file, $filename, 'asset.plexuss.com/social');

            if ($response['status'] == 'success') {
              $file_path = $response['url'];
            }
            else{
              return response()->json(array('status' => 'could not store image to S3'), 500);
            }

            $image = new PostImage();

            $image->post_id = $comment->post_id;
            $image->post_comment_id = $comment->id;
            $image->social_article_id = $comment->social_article_id;
            $image->is_gif = false;
            $image->gif_link = $request['gif_link'];
            $image->image_link = $file_path;
            $image_count++;

            if(!$image->save())
              return response()->json(array('success' => false), 500);
          }
        }
      }

      /* publish new comment with Redis */
      $publish_data = array();

      $publish_data['thread_room'] = $request['thread_room'];
      $comment_data = PostComment::with('user:id,fname,lname,profile_img_loc', 'images', 'likes', 'userAccountSettings')->where('id', $comment->id)->get();
      $encode = json_encode($comment_data);
      $comment_data  = json_decode($encode);
      $publish_data['comment_data'] = $this->hashALoop($comment_data);

      Redis::publish('post:comments', json_encode($publish_data));

      if ($this->decodeIdForSocial($request['user_id']) != $this->decodeIdForSocial($request['target_id'])) {
        /* Push new notification for target user */
        $user_name = $request['user_name'];
        $ntn = new NotificationController();
        if ($request['post_id']){
          if (isset($request['is_shared']) && $request['is_shared'] == 1) {
            $saved_ntn = $ntn->create( $user_name, 'user', 20, null, $this->decodeIdForSocial($request['user_id']) , $this->decodeIdForSocial($request['target_id']), null, null, $this->decodeIdForSocial($request['post_id']));
          }
          else {
            $saved_ntn = $ntn->create( $user_name, 'user', 10, null, $this->decodeIdForSocial($request['user_id']) , $this->decodeIdForSocial($request['target_id']), null, null, $this->decodeIdForSocial($request['post_id']));
          }
        }
        else {
          if (isset($request['is_shared']) && $request['is_shared'] == 1) {
            $saved_ntn = $ntn->create( $user_name, 'user', 21, null, $this->decodeIdForSocial($request['user_id']) , $this->decodeIdForSocial($request['target_id']), null, null, $this->decodeIdForSocial($request['article_id']));
          }
          else {
            $saved_ntn = $ntn->create( $user_name, 'user', 15, null, $this->decodeIdForSocial($request['user_id']) , $this->decodeIdForSocial($request['target_id']), null, null, $this->decodeIdForSocial($request['article_id']));
          }
        }
        $ntn_data['thread_room'] = $request['target_id'];
        $ntn = DB::table('notification_topnavs')->find($saved_ntn);
        $msg = $ntn->msg;

        if ($ntn->icon_img_route == "users_image") {
          $user = DB::connection('rds1')->table('users as u')
                          ->select(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as student_profile_photo'))
                          ->where('id', $this->decodeIdForSocial($request['user_id']))
                          ->first();
          if (isset($user->student_profile_photo)) {
            $ntn->icon_img_route = $user->student_profile_photo;
          }
        }

        $ntn = json_encode($ntn);
        $ntn = json_decode($ntn);
        $ntn = $this->hashALoopNotAnArray($ntn);

        $ntn_data['notification_data'] = $ntn;
        Redis::publish('push:notification', json_encode($ntn_data));

        if (isset($request['post_id'])) {
          $url = "post/".$request['post_id'];
          $this->pushNotification($request['user_id'], $request['target_id'], $url, $msg);
        }
        else {
          $url = "social/article/".$request['article_id'];
          $this->pushNotification($request['user_id'], $request['target_id'], $url, $msg);
        }
      }

      return response()->json(array('success' => true), 200);
    }
    else
      return response()->json(array('success' => false), 500);
  }

  private function applyUserAccountSettings($user_id, $data){
    $snas = new UserAccountSettings;
    $settings = $snas->getUserAccountSettings($user_id);
    if(!isset($settings)){
      return $data;
    }
    if($settings->show_lname == 0){
      if(is_object($data)){
        $data->lname = "";
      }else if (is_array($data)){
        $data['lname'] = "";
      }
    }
    if($settings->show_school === 0){
      if(is_object($data)){
        if(isset($data->currentSchoolName)) { $data->currentSchoolName = ""; }
        if(isset($data->user_type)) { $data->user_type = ""; }
      }
    }
    if($settings->show_profile_pic == 0){
      if(is_object($data)){
        $data->profile_img_loc = "incognito.png";
      }else if (is_array($data)){
        $data['profile_img_loc'] = "incognito.png";
      }
    }
    return $data;
  }

  public function getSingleArticle() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    $request['article-id'] = $this->decodeIdForSocial($request['article-id']);

    $article = SocialArticle::on('rds1')->with('user:id,fname,lname,profile_img_loc', 'comments.likes',
                                               'comments.user:id,fname,lname,profile_img_loc','comments.images','likes', 'views','images',
                                               'tags')
                            // ->leftjoin('sn_users_account_settings as snas', 'snas.user_id', '=', 'users.id')
                            ->where('id', $request['article-id']);

    $article = $this->applyShareWithConditions($article, $data, 'sn_articles');
    $article = $this->applyHiddenCondition($article, $data, 'sn_articles');
    $article = $article->get();


    if (!isset($article[0]) || empty($article[0])) {
      $ret = array();
      return response()->json($ret);
    }

    if ($article[0]->posted_by_plexuss == 1) {

      $tmp_input = array();
      $tmp_input['social_article_id'] = $article[0]->id;

      $check = $this->checkPostFilter($data['user_id'], $tmp_input);

      if ($check == false) {
        $ret = array();
        return response()->json($ret);
      }
    }

    if (isset($article)) {

      if($article[0]->user_id != $data['user_id']){
        $article[0]->user = $this->applyUserAccountSettings($article[0]->user_id, $article[0]->user);
      }
      foreach($article[0]->comments as $key => $value){
        if($article[0]->comments[$key]->user_id != $data['user_id']){
          $article[0]->comments[$key]->user = $this->applyUserAccountSettings($article[0]->comments[$key]->user_id, $article[0]->comments[$key]->user);
        }
      }

      $encode = json_encode($article);
      $article = json_decode($encode);

      foreach ($article as $key => $value) {
        if ($value->posted_by_plexuss == 1) {

          $tmp_input = array();
          $tmp_input['social_article_id'] = $value->id;

          $check = $this->checkPostFilter($data['user_id'], $tmp_input);
          if ($check == false) {
            unset($articles[$key]);
            continue;
          }
        }

        $value->id         = $this->hashIdForSocial($value->id);
        $value->user_id    = $this->hashIdForSocial($value->user_id);
        isset($value->original_article_id) ? $value->original_article_id = $this->hashIdForSocial($value->original_article_id) : NULL;

        $value->comments   = $this->hashALoop($value->comments);
        $value->likes      = $this->hashALoop($value->likes);
        isset($value->tags) ? $value->tags = $this->hashALoop($value->tags) : NULL;
        $value->images     = $this->hashALoop($value->images);
        $value->user       = $this->hashALoopNotAnArray($value->user);
      }

    }

    return response()->json($article);
  }

  public function getArticles() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $articles = SocialArticle::on('rds1')->with('comments','likes', 'views', 'tags', 'images')
                                         ->where('user_id', $data['user_id'])
                                         ->get();

    $encode = json_encode($articles);
    $decode = json_decode($encode);

    foreach ($decode as $key => $value) {

      $value->id      = $this->hashIdForSocial($value->id);
      $value->user_id = $this->hashIdForSocial($value->user_id);

      $value->comments   = $this->hashALoop($value->comments);
      $value->likes      = $this->hashALoop($value->likes);
      $value->tags       = $this->hashALoop($value->tags);
      $value->images     = $this->hashALoop($value->images);
    }

    return response()->json($decode);
  }

  private function hashALoop($value){
    if (isset($value)) {
      foreach ($value as $k => $v) {
        isset($v->id)      ? $v->id      = $this->hashIdForSocial($v->id) : NULL;
        isset($v->submited_id)         ? $v->submited_id      = $this->hashIdForSocial($v->submited_id) : NULL;
        isset($v->user_id) ? $v->user_id = $this->hashIdForSocial($v->user_id) : NULL;
        isset($v->parent_id)           ? $v->parent_id = $this->hashIdForSocial($v->parent_id) : NULL;
        isset($v->post_id)             ? $v->post_id = $this->hashIdForSocial($v->post_id) : NULL;
        isset($v->social_article_id)   ? $v->social_article_id = $this->hashIdForSocial($v->social_article_id) : NULL;
        isset($v->post_comment_id)     ? $v->post_comment_id = $this->hashIdForSocial($v->post_comment_id) : NULL;
        isset($v->original_article_id) ? $v->original_article_id = $this->hashIdForSocial($v->original_article_id) : NULL;
        isset($v->original_post_id)    ? $v->original_post_id = $this->hashIdForSocial($v->original_post_id) : NULL;
        isset($v->share_post_id)    ? $v->share_post_id = $this->hashIdForSocial($v->share_post_id) : NULL;
        if (isset($v->user)) {
          isset($v->user->id) ? $v->user->id = $this->hashIdForSocial($v->user->id) : NULL;
        }
        if (isset($v->likes)) {
          foreach ($v->likes as $x => $y) {
            isset($y->id)      ? $y->id      = $this->hashIdForSocial($y->id) : NULL;
            isset($y->user_id) ? $y->user_id = $this->hashIdForSocial($y->user_id) : NULL;
            isset($y->post_comment_id)     ? $y->post_comment_id = $this->hashIdForSocial($y->post_comment_id) : NULL;
            isset($y->social_article_id)   ? $y->social_article_id = $this->hashIdForSocial($y->social_article_id) : NULL;
            isset($y->post_id)             ? $y->post_id = $this->hashIdForSocial($y->post_id) : NULL;
          }
        }
        if (isset($v->images)) {
          foreach ($v->images as $l => $n) {
            isset($n->id)      ? $n->id      = $this->hashIdForSocial($n->id) : NULL;
            isset($n->user_id) ? $n->user_id = $this->hashIdForSocial($n->user_id) : NULL;
            isset($n->post_comment_id)     ? $n->post_comment_id = $this->hashIdForSocial($n->post_comment_id) : NULL;
            isset($n->social_article_id)   ? $n->social_article_id = $this->hashIdForSocial($n->social_article_id) : NULL;
            isset($n->post_id)             ? $n->post_id = $this->hashIdForSocial($n->post_id) : NULL;
          }
        }

      }
    }
    return $value;
  }

  private function hashALoopNotAnArray($v){
    if (isset($v)) {

      isset($v->id)                  ? $v->id      = $this->hashIdForSocial($v->id) : NULL;
      isset($v->submited_id)         ? $v->submited_id      = $this->hashIdForSocial($v->submited_id) : NULL;
      isset($v->user_id)             ? $v->user_id = $this->hashIdForSocial($v->user_id) : NULL;
      isset($v->parent_id)           ? $v->parent_id = $this->hashIdForSocial($v->parent_id) : NULL;
      isset($v->post_id)             ? $v->post_id = $this->hashIdForSocial($v->post_id) : NULL;
      isset($v->social_article_id)   ? $v->social_article_id = $this->hashIdForSocial($v->social_article_id) : NULL;
      isset($v->post_comment_id)     ? $v->post_comment_id = $this->hashIdForSocial($v->post_comment_id) : NULL;
      isset($v->original_article_id) ? $v->original_article_id = $this->hashIdForSocial($v->original_article_id) : NULL;
      isset($v->original_post_id)    ? $v->original_post_id = $this->hashIdForSocial($v->original_post_id) : NULL;
      if (isset($v->user)) {
        isset($v->user->id) ? $v->user->id = $this->hashIdForSocial($v->user->id) : NULL;
      }
      if (isset($v->link)) {
        if ((strpos($v->link, '/post/') !== FALSE) || (strpos($v->link, '/social/article/') !== FALSE)){
          $id = substr($v->link, strrpos($v->link, '/') + 1);
          if (is_numeric($id)) {
            $v->link = str_replace($id, "", $v->link);
            $v->link .= $this->hashIdForSocial($id);
          }
        }
      }

    }
    return $v;
  }

  public function saveArticle() {

    $now = Carbon::now();
    $request = Request::all();
    $article = new SocialArticle();
    if($request['is_shared'] == 'true'){
      $exists = $article->where('user_id', $this->decodeIdForSocial($request['user_id']))
                        ->where('id', $this->decodeIdForSocial($request['original_article_id']))
                        ->first();
      if($exists){
        return response()->json(array('status' => 2), 200);
      }
      $exists = $article->where('user_id', $this->decodeIdForSocial($request['user_id']))
                        ->where('original_article_id', $this->decodeIdForSocial($request['original_article_id']))
                        ->first();
      if($exists){
        return response()->json(array('status' => 2), 200);
      }
    }
    $article->created_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');
    $article->updated_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');

    $article->share_with_id = $request['share_with_id'];
    $article->user_id = $this->decodeIdForSocial($request['user_id']);
    $article->article_title = $request['article_title'];
    $article->article_text = $request['article_text'];
    $article->status = $request['status'] == '1' ? 1 : 0; // 0 for Draft and 1 for Publish
    $article->project_and_publication = $request['project_and_publication'] == 'true' ? 1 : 0; // false for "not in project" and true for "in projects"
    $article->share_count = $request['share_count'];
    if($request['is_shared'] == 'true'){
      $article->is_shared = true;
      $article->original_article_id = $this->decodeIdForSocial($request['original_article_id']);
    }else{
      $article->is_shared = false;
      $article->original_article_id = null;
    }
    $ret[] = array();

    if ($article->save()) {
      if($request['is_shared'] == 'true'){
        if ($this->decodeIdForSocial($request['user_id']) != $this->decodeIdForSocial($request['target_id'])) {
          /* Push new notification for target user */
          $user_name = $request['user_name'];
          $ntn = new NotificationController();
          $saved_ntn = $ntn->create( $user_name, 'user', 17, null, $this->decodeIdForSocial($request['user_id']) , $this->decodeIdForSocial($request['target_id']), null, null, $article->original_article_id);

          $ntn_data['thread_room'] = $request['target_id'];
          $ntn = DB::table('notification_topnavs')->find($saved_ntn);
          $msg = $ntn->msg;

          if ($ntn->icon_img_route == "users_image") {
            $user = DB::connection('rds1')->table('users as u')
                            ->select(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as student_profile_photo'))
                            ->where('id', $this->decodeIdForSocial($request['user_id']))
                            ->first();
            if (isset($user->student_profile_photo)) {
              $ntn->icon_img_route = $user->student_profile_photo;
            }
          }

          $ntn = json_encode($ntn);
          $ntn = json_decode($ntn);
          $ntn = $this->hashALoopNotAnArray($ntn);

          $ntn_data['notification_data'] = $ntn;
          Redis::publish('push:notification', json_encode($ntn_data));
          $url = "social/article/".$this->hashIdForSocial($article->id);
          $this->pushNotification($request['user_id'], $request['target_id'], $url, $msg);
        }
      }
      if ($request['article_tags'] != null) {
        /* Tags name mapping:
        *  news: 1
        *  ranking: 2
        *  admissions: 3
        *  sports: 4
        *  campus_life: 5
        *  paying_for_college: 6
        *  financial_add: 7
        */
        $tags = $request['article_tags'];

        foreach ($tags as $tag_value) {
          $tag = new ArticleTag();

          $tag->tag_number = $tag_value;
          $tag->social_article_id = $article->id;

          if(!$tag->save())
            return response()->json(array('status' => "couldn't save tags"), 500);
        }
      }
      // Articles should be kept separate from Projects for now.
      // if ($article->project_and_publication == 1) {
      //   $project_and_publication = new PublicProfileProjectsAndPublications();

      //   $project_and_publication->user_id = $article->user_id;
      //   $project_and_publication->title = $article->article_title;
      //   $article_link = 'social/articles/'.$article->id;
      //   $project_and_publication->url = $article_link;
      //   $project_and_publication->active = $article->project_and_publication ? 1 : 0;

      //   if(!$project_and_publication->save())
      //       return response()->json(array('status' => "couldn't add to publications and projects"), 500);
      // }

      if ($request['is_gif'] == 'true') {
        $image = new PostImage();

        $image->post_id = null;
        $image->post_comment_id = null;
        $image->social_article_id = $article->id;
        $image->is_gif = true;
        $image->gif_link = $request['gif_link'];
        $image->image_link = $request['image_link'];
        if(!$image->save())
          return response()->json(array('success' => false), 500);
      }

      if($request['article_images'] != null) {

        $files = $request['article_images'];
        $image_count = 1;

        foreach ($files as $file) {

          $filename = true;
          $response = $this->generalUploadDoc($file, $filename, 'asset.plexuss.com/social');

          if ($response['status'] == 'success') {
            $file_path = $response['url'];
          }
          else{
            return response()->json(array('status' => 'could not store image to S3'), 500);
          }

          $image = new PostImage();

          $image->post_id = null;
          $image->post_comment_id = null;
          $image->social_article_id = $article->id;
          $image->is_gif = false;
          $image->gif_link = null;
          $image->image_link = $file_path;
          $image_count++;

          if(!$image->save())
            return response()->json(array('success' => false), 500);
        }
      }
      if($article->status == 1){
        $publish_data = array();
        $publish_data['thread_room'] = $request['thread_room'];
        $tmp_post = SocialArticle::with('user:id,fname,lname,profile_img_loc,is_student', 'comments.likes','comments.user:id,fname,lname,profile_img_loc', 'comments.images', 'likes', 'images')->where('id', $article->id)->get();

        $encode = json_encode($tmp_post);
        $tmp_post  = json_decode($encode);

        foreach ($tmp_post as $key => $value) {
          $value->id         = $this->hashIdForSocial($value->id);
          $value->user_id    = $this->hashIdForSocial($value->user_id);
          isset($value->original_article_id) ? $value->original_article_id = $this->hashIdForSocial($value->original_article_id) : NULL;

          $value->comments   = $this->hashALoop($value->comments);
          $value->likes      = $this->hashALoop($value->likes);
          $value->user       = $this->hashALoopNotAnArray($value->user);

          isset($value->tags) ? $value->tags = $this->hashALoop($value->tags) : NULL;
          $value->images     = $this->hashALoop($value->images);
        }

        $publish_data['post_data'] = $tmp_post;

        $request['social_article_id'] = $this->hashIdForSocial($article->id);
        $this->publishPostToThisUser($request, $publish_data);
        // Redis::publish('publish:post', json_encode($publish_data));
      }
      $saved_article_id = $article->id;
      return response()->json(array('success' => true, 'article' => $article, 'article_id' => $this->hashIdForSocial($saved_article_id)), 200);
    }
    else
      return response()->json(array('success' => false), 500);
  }

  public function updateSharedArticleCount(){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();
    if (!isset($request)) {
      $request = Request::all();
    }

    $article = SocialArticle::where('id', $this->decodeIdForSocial($request['article_id']))
                 ->where('user_id', $data['user_id'])
                 ->first();
    if (!isset($article)) {
      return response()->json(array('success' => false), 500);
    }
    if($request['update'] == 'increment'){
      $article->share_count += 1;
    }else{
      $article->share_count -= 1;
    }
    $article->updated_at = Carbon::now('UTC');
    if ($article->save()) {

      $publish_data = array();
      $publish_data['thread_room'] = $request['thread_room'];
      $publish_data['id'] = $request['article_id'];
      $publish_data['share_count'] = $article->share_count;
      $publish_data['type'] = 'article';
      Redis::publish('update:shareCount', json_encode($publish_data));

      return response()->json(array('status' => "post shared count has been updated successfully.", 'success' => true), 200);
    }else{
      return response()->json(array('success' => false), 500);
    }
  }
  public function updateArticle() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    $article = SocialArticle::where('id', $this->decodeIdForSocial($request['article_id']))
                            ->where('user_id', $this->decodeIdForSocial($request['user_id']))
                            ->firstOrFail();

    if (isset($article)) {
      $now = Carbon::now();
      $article->updated_at = $this->convertTimeZone($now, 'America/Los_Angeles', 'UTC');
      $article->share_with_id = $request['share_with_id'];
      $article->article_title = $request['article_title'];
      $article->article_text = $request['article_text'];
      $article->status = $request['status'] == '1' ? 1 : 0; // 0 for Draft and 1 for Publish
      $article->project_and_publication = $request['project_and_publication'] == 'true' ? 1 : 0; // false for "not in project" and true for "in projects"
      $article->share_count = $request['share_count'];
      if($request['is_shared'] == 'true'){
        $article->is_shared = true;
        $article->original_article_id = $this->decodeIdForSocial($request['original_article_id']);
      }else{
        $article->is_shared = false;
        $article->original_article_id = null;
      }
    }

    if ($article->save()) {
      if ($request['article_tags'] != null) {
        ArticleTag::where('social_article_id', $article->id)->delete();
        $tags = $request['article_tags'];

        foreach ($tags as $tag_value) {
        $tag = new ArticleTag();

          $tag->tag_number = $tag_value;
          $tag->social_article_id = $article->id;

          if(!$tag->save())
            return response()->json(array('status' => "couldn't save tags"), 500);
        }
      }
      if($request['article_images'] != null) {

        $files = $request['article_images'];
        $image_count = 1;

        foreach ($files as $file) {

          $filename = true;
          $response = $this->generalUploadDoc($file, $filename, 'asset.plexuss.com/social');

          if ($response['status'] == 'success') {
            $file_path = $response['url'];
          }
          else{
            return response()->json(array('status' => 'could not store image to S3'), 500);
          }

          $image = new PostImage();

          $image->post_id = null;
          $image->post_comment_id = null;
          $image->social_article_id = $article->id;
          $image->is_gif = false;
          $image->gif_link = null;
          $image->image_link = $file_path;
          $image_count++;

          if(!$image->save())
            return response()->json(array('success' => false), 500);
        }
      }
    }
    else {
      return response()->json(array('status' => "couldn't update article"), 500);
    }

    // SocialArticle::where('id', $request['article_id'])->update(['title'=>'Updated title']);
  }

  public function deleteArticle() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $request = Request::all();

    if($request['is_sales']){
      $qry = SocialArticle::where('id', $this->decodeIdForSocial($request['article_id']))
                          ->first();
    }else{
      $qry = SocialArticle::where('id', $this->decodeIdForSocial($request['article_id']))
                          ->where('user_id', $data['user_id'])
                          ->first();
    }
    if (!isset($qry)) {
      return response()->json(array('success' => false), 500);
    }
    $qry->delete();

    $publish_data = array();
    $publish_data['thread_room'] = $request['thread_room'];
    $publish_data['id'] = $request['article_id'];
    $publish_data['type'] = 'article';
    Redis::publish('delete:post', json_encode($publish_data));
    return response()->json(array('success' => true), 200);
  }

  public function getPostLikes(){
    
    $request = Request::all();

    $ret = array();
    
    if (!isset($request['post_comment_id']) && !isset($request['post_id']) && !isset($request['social_article_id'])) {
      
      $ret['status'] = "failed";  
      return response()->json($ret, 500);   
    }

    $likes = Like::on('rds1')->with('user:id,fname,lname,profile_img_loc,is_student,is_alumni,is_university_rep,is_organization,is_agency,country_id,in_college,current_school_id',
                                    'user.country:id,country_code',
                                    'user.highschool:id,school_name',
                                    'user.college:id,school_name')
                              ->orderBy('created_at', 'DESC');

    isset($request['post_comment_id'])   ? $likes = $likes->where('post_comment_id', $this->decodeIdForSocial($request['post_comment_id']))  : NULL;

    isset($request['post_id'])   ? $likes = $likes->where('post_id', $this->decodeIdForSocial($request['post_id']))  : NULL;

    isset($request['social_article_id'])   ? $likes = $likes->where('social_article_id', $this->decodeIdForSocial($request['social_article_id']))  : NULL;

    $likes = $likes->paginate(10);

    $encode = json_encode($likes);
    $likes  = json_decode($encode);
    foreach ($likes->data as $key => $value) {
      $value->id         = $this->hashIdForSocial($value->id);
      isset($value->post_id) ? $value->post_id = $this->hashIdForSocial($value->post_id) : NULL;
      $value->user_id    = $this->hashIdForSocial($value->user_id);
      
      isset($value->post_comment_id) ? $value->post_comment_id = $this->hashIdForSocial($value->post_comment_id) : NULL;
      isset($value->social_article_id) ? $value->social_article_id = $this->hashIdForSocial($value->social_article_id) : NULL;
      $value->user       = $this->hashALoopNotAnArray($value->user);
      
      $input = array();
      $input['user_two_id'] = $value->user_id;
      $value->friend_status = $this->friendStatus($input);

    }

    $comment = PostComment::on('rds1')->with('user:id,fname,lname,profile_img_loc,is_student,is_alumni,is_university_rep,is_organization,is_agency,country_id,in_college,current_school_id',
                                    'user.country:id,country_code',
                                    'user.highschool:id,school_name',
                                    'user.college:id,school_name')
                              ->orderBy('created_at', 'DESC');

    isset($request['post_id'])   ? $comment = $comment->where('post_id', $this->decodeIdForSocial($request['post_id']))  : NULL;

    isset($request['social_article_id'])   ? $comment = $comment->where('social_article_id', $this->decodeIdForSocial($request['social_article_id']))  : NULL;

    $comment = $comment->paginate(10);

    $encode = json_encode($comment);
    $comment  = json_decode($encode);
    foreach ($comment->data as $key => $value) {
      $value->id         = $this->hashIdForSocial($value->id);
      isset($value->post_id) ? $value->post_id = $this->hashIdForSocial($value->post_id) : NULL;
      $value->user_id    = $this->hashIdForSocial($value->user_id);
      
      isset($value->post_comment_id) ? $value->post_comment_id = $this->hashIdForSocial($value->post_comment_id) : NULL;
      isset($value->social_article_id) ? $value->social_article_id = $this->hashIdForSocial($value->social_article_id) : NULL;
      $value->user       = $this->hashALoopNotAnArray($value->user);
      
      $input = array();
      $input['user_two_id'] = $value->user_id;
      $value->friend_status = $this->friendStatus($input);

    }

    $ret['shares'] = NULL;
    if (isset($request['post_id']) || isset($request['social_article_id'])) {
      $post = Post::on('rds1')->with('user:id,fname,lname,profile_img_loc,is_student,is_alumni,is_university_rep,is_organization,is_agency,country_id,in_college,current_school_id',
                                    'user.country:id,country_code',
                                    'user.highschool:id,school_name',
                                    'user.college:id,school_name')
                              ->orderBy('created_at', 'DESC');


      if (isset($request['post_id'])) {
        $post = $post->where('share_type', 'post')
                     ->where('share_post_type', 'post')
                     ->where('share_post_id', $request['post_id']);
      }elseif (isset($request['social_article_id'])) {
        $post = $post->where('share_type', 'article')
                     ->where('share_post_type', 'article')
                     ->where('share_post_id', $request['social_article_id']);
      }

      $post = $post->paginate(10);

      $encode = json_encode($post);
      $post  = json_decode($encode);
      foreach ($post->data as $key => $value) {
        $value->id         = $this->hashIdForSocial($value->id);
        isset($value->post_id) ? $value->post_id = $this->hashIdForSocial($value->post_id) : NULL;
        $value->user_id    = $this->hashIdForSocial($value->user_id);
        
        isset($value->post_comment_id) ? $value->post_comment_id = $this->hashIdForSocial($value->post_comment_id) : NULL;
        isset($value->social_article_id) ? $value->social_article_id = $this->hashIdForSocial($value->social_article_id) : NULL;
        $value->user       = $this->hashALoopNotAnArray($value->user);
        
        $input = array();
        $input['user_two_id'] = $value->user_id;
        $value->friend_status = $this->friendStatus($input);

      }

      $ret['shares'] = $post;
    }

    $ret['status'] = "success";
    $ret['likes'] = $likes;
    $ret['comments'] = $comment;

    return response()->json($ret, 200);    
  }

  public function addLikes() {

    $request = Request::all();

    isset($request['post_comment_id'])   ? $post_comment_id = $this->decodeIdForSocial($request['post_comment_id']) : $post_comment_id = NULL;
    isset($request['post_id'])           ? $post_id = $this->decodeIdForSocial($request['post_id']) : $post_id = NULL;
    isset($request['social_article_id']) ? $social_article_id = $this->decodeIdForSocial($request['social_article_id']) : $social_article_id = NULL;
    isset($request['target_id'])         ? $target_id = $this->decodeIdForSocial($request['target_id']) : $target_id = NULL;
    isset($request['user_id'])           ? $user_id = $this->decodeIdForSocial($request['user_id']) : $user_id = NULL;

    $like = new Like();

    $like->user_id = $user_id;
    $like->post_id = $post_id;
    $like->social_article_id = $social_article_id;
    $like->post_comment_id = $post_comment_id;

    if ($like->save()) {

      /* publish new comment with Redis */
      $publish_data = array();
      $publish_data['thread_room'] = $request['thread_room'];

      $like_data = Like::find($like->id);

      $like_data = json_encode($like_data);
      $like_data = json_decode($like_data);
      $like_data = $this->hashALoopNotAnArray($like_data);

      $publish_data['like_data'] = $like_data;
      Redis::publish('add:like', json_encode($publish_data));

      if ($user_id != $target_id) {
        /* Push new notification for target user */
        $user_name = $request['user_name'];
        $ntn = new NotificationController();
        if (isset($post_id)) {
          if (isset($request['is_shared']) && $request['is_shared'] == 1) {
            $saved_ntn = $ntn->create( $user_name, 'user', 18, null, $user_id , $target_id, null, null, $post_id);
          }
          else if(isset($request['liked_comment']) && $request['liked_comment'] == 1) {
            $saved_ntn = $ntn->create( $user_name, 'user', 23, null, $user_id , $target_id, null, null, $post_id);
          }
          else {
            $saved_ntn = $ntn->create( $user_name, 'user', 11, null, $user_id , $target_id, null, null, $post_id);
          }
        }
        else {
          if (isset($request['is_shared']) && $request['is_shared'] == 1) {
            $saved_ntn = $ntn->create( $user_name, 'user', 19, null, $user_id , $target_id, null, null, $social_article_id);
          }
          else {
            $saved_ntn = $ntn->create( $user_name, 'user', 16, null, $user_id , $target_id, null, null, $social_article_id);
          }
        }
        $ntn_data['thread_room'] = $this->hashIdForSocial($target_id);
        $ntn = DB::table('notification_topnavs')->find($saved_ntn);
        $msg = $ntn->msg;

        if ($ntn->icon_img_route == "users_image") {
          $user = DB::connection('rds1')->table('users as u')
                          ->select(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as student_profile_photo'))
                          ->where('id', $user_id)
                          ->first();
          if (isset($user->student_profile_photo)) {
            $ntn->icon_img_route = $user->student_profile_photo;
          }
        }

        $ntn = json_encode($ntn);
        $ntn = json_decode($ntn);
        $ntn = $this->hashALoopNotAnArray($ntn);

        $ntn_data['notification_data'] = $ntn;
        Redis::publish('push:notification', json_encode($ntn_data));

        if (isset($request['post_id'])) {
          $url = "post/".$request['post_id'];
          $this->pushNotification($request['user_id'], $request['target_id'], $url, $msg);
        }
        else {
          $url = "social/article/".$request['social_article_id'];
          $this->pushNotification($request['user_id'], $request['target_id'], $url, $msg);
        }
      }

      return response()->json(array('success' => true), 200);
    }
    else
      return response()->json(array('success' => false), 500);
  }

  public function removeLikes() {
    $request = Request::all();

    isset($request['post_comment_id'])   ? $post_comment_id = $this->decodeIdForSocial($request['post_comment_id']) :
    $post_comment_id = NULL;
    isset($request['post_id'])           ? $post_id = $this->decodeIdForSocial($request['post_id']) : NULL;
    isset($request['social_article_id']) ? $social_article_id = $this->decodeIdForSocial($request['social_article_id']) : NULL;
    isset($request['target_id'])         ? $target_id = $this->decodeIdForSocial($request['target_id']) : NULL;
    isset($request['user_id'])           ? $user_id = $this->decodeIdForSocial($request['user_id']) : NULL;

    $publish_data = array();
    $publish_data['thread_room'] = $request['thread_room'];
    $publish_data['data'] = $request;
    Redis::publish('remove:like', json_encode($publish_data));

    if ($post_comment_id != null) {

      Like::where('user_id', $user_id)
          ->where('post_id', $post_id)
          ->where('post_comment_id', $post_comment_id)
          ->delete();

      return response()->json(array('success' => true), 200);
    }
    else{

      Like::where('user_id', '=', $user_id)
          ->where('post_id', '=', $post_id)
          ->delete();

      return response()->json(array('success' => true), 200);
    }
  }

  public function addFriend() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    $request = Request::all();

    if ($data['user_id'] < $this->decodeIdForSocial($request['user_two_id'])) {
      $min_id = $data['user_id'];
      $max_id = $this->decodeIdForSocial($request['user_two_id']);
    }
    else {
      $min_id = $this->decodeIdForSocial($request['user_two_id']);
      $max_id = $data['user_id'];
    }

    $friend = FriendRelation::firstOrNew(array('user_one_id' => $min_id, 'user_two_id' => $max_id));

    // $friend = FriendRelation::updateOrCreate(['user_one_id' => $min_id], ['user_two_id' => $max_id], ['relation_status' => $request['relation_status'], 'action_user' => $request['action_user']]);

    $friend->user_one_id = $min_id;
    $friend->user_two_id = $max_id;
    $friend->relation_status = $request['relation_status'];
    $friend->action_user = $data['user_id'];

    if ($min_id != $data['user_id']) {
      $friend_user_id = $min_id;
    }else{
      $friend_user_id = $max_id;
    }

    if ($friend->save()) {
      /* Push new notification for target user */
      $target_id = $request['user_two_id'];
      $user_id   = $data['user_id'];
      $ntn = new NotificationController();

      if ($friend->relation_status == 'Accepted') {
        $saved_ntn = $ntn->create( $request['user_name'], 'user', 12, null, $user_id , $this->decodeIdForSocial($target_id));

        $ntn_data['thread_room'] = $target_id;
        $saved_ntn = DB::table('notification_topnavs')->find($saved_ntn);
        $msg = $saved_ntn->msg;

        $saved_ntn = json_encode($saved_ntn);
        $saved_ntn = json_decode($saved_ntn);
        $saved_ntn = $this->hashALoopNotAnArray($saved_ntn);

        $ntn_data['notification_data'] = $saved_ntn;
        Redis::publish('push:notification', json_encode($ntn_data));
        $url = "social/networking/connection";
        $this->pushNotification($this->hashIdForSocial($user_id), $target_id, $url, $msg);
      }
      elseif ($friend->relation_status == 'Pending') {

        $saved_ntn = $ntn->create( $request['user_name'], 'user', 13, null, $user_id , $this->decodeIdForSocial($target_id));

        $ntn_data['thread_room'] = $target_id;

        $ntn = DB::table('notification_topnavs')->find($saved_ntn);
        $msg = $ntn->msg;

        $ntn = json_encode($ntn);
        $ntn = json_decode($ntn);
        $ntn = $this->hashALoopNotAnArray($ntn);

        $ntn_data['notification_data'] = $ntn;

        Redis::publish('push:notification', json_encode($ntn_data));
        $url = "social/networking/requests";
        $this->pushNotification($this->hashIdForSocial($user_id), $target_id, $url, $msg);
      }

      return response()->json(array('success' => true, 'user_id' => $this->hashIdForSocial($friend_user_id)), 200);
    }
    else
      return response()->json(array('success' => false, 'user_id' => $this->hashIdForSocial($friend_user_id)), 500);
  }

  public function declineFriend() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    $request = Request::all();

    $dt = array();
    $dt['min_id'] = $data['user_id'];
    $dt['max_id'] = $this->decodeIdForSocial($request['user_two_id']);

    $friend = FriendRelation::orWhere(function($q) use($dt){
                                      $q->where('user_one_id', '=', $dt['min_id'])
                                        ->where('user_two_id', '=', $dt['max_id']);
                                      })
                            ->orWhere(function($q) use($dt){
                                      $q->where('user_one_id', '=', $dt['max_id'])
                                        ->where('user_two_id', '=', $dt['min_id']);
                                      })
                            ->update('relation_status' ,'Declined');

    return response()->json(array('success' => true, 'user_id' => $dt['max_id']), 200);
  }

  public function cancelFriend() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    $request = Request::all();

    $dt = array();
    $dt['min_id'] = $data['user_id'];
    $dt['max_id'] = $this->decodeIdForSocial($request['user_two_id']);

    $friend = FriendRelation::where('action_user', $data['user_id'])
                            ->where(function($query) use($dt){
                              $query->orWhere(function($q) use($dt){
                                              $q->where('user_one_id', '=', $dt['min_id'])
                                                ->where('user_two_id', '=', $dt['max_id']);
                                              })
                                    ->orWhere(function($q) use($dt){
                                              $q->where('user_one_id', '=', $dt['max_id'])
                                                ->where('user_two_id', '=', $dt['min_id']);
                                              });
                            })
                            ->where('relation_status', 'Pending')
                            ->delete();

    return response()->json(array('success' => true, 'user_id' => $dt['max_id']), 200);
  }

  public function addAbuser(){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    $request = Request::all();
    $request['user_id'] = $data['user_id'];

    $sar = new SocialAbuseReport;
    $sar->add($request);
    return response()->json(array('success' => true), 200);
  }

  public function friendStatus($input = NULL){
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    (isset($input)) ? NULL: $input = Request::all();

    if (!isset($input['user_two_id'])) {
      return '';
    }

    $input['user_two_id'] = $this->decodeIdForSocial($input['user_two_id']);

    $response = '';
    $status = FriendRelation::on('rds1')->where(function($query) use($input, $data){
                              $query->orWhere(function($q) use($input, $data){
                                              $q->where('user_one_id', '=', $data['user_id'])
                                                ->where('user_two_id', '=', $input['user_two_id']);
                                              })
                                    ->orWhere(function($q) use($input, $data){
                                              $q->where('user_one_id', '=', $input['user_two_id'])
                                                ->where('user_two_id', '=', $data['user_id']);
                                              });
                            })
                            ->select('relation_status')
                            ->first();

    if (isset($status->relation_status)) {
      $response = $status->relation_status;
    }

    return $response;
  }

  public function networkUsers() {

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    if (!isset($data['signed_in']) || $data['signed_in'] == 0) {
      $dt = array();

      $dt['requests'] = array();
      $dt['friends']  = array();

      return response()->json($dt);
    }

    $take = 20;
    isset($inputs['offset']) ? $offset = $inputs['offset'] : $offset = 0;

    $this_user_info = User::on('rds1')->find($data['user_id']);
    $in_college = 0;
    isset($this_user_info->in_college) ? $in_college = $this_user_info->in_college : NULL;

    $requests_relations = FriendRelation::on('rds1')->select('action_user')
                                        ->where('relation_status', 'Pending')
                                        ->where('action_user','<>',$data['user_id'])
                                        ->where(function ($query) use ($data) {
                                                $query->orWhere('user_one_id', 'like', $data['user_id'])
                                                      ->orWhere('user_two_id', 'like', $data['user_id']);
                                              })
                                        ->pluck('action_user')
                                        ->toArray();

    $requests = DB::connection('rds1')->table('users as u')
                                     ->leftjoin('countries as co', 'co.id', '=', 'u.country_id')

                                     ->whereIn('u.id', $requests_relations)
                                     // don't include myself
                                     ->where('u.id', '!=', $data['user_id'])
                                     // ->where('u.is_plexuss', '!=', 1)
                                     ->where('u.is_ldy', 0)
                                     ->where('u.fname', 'NOT LIKE', DB::raw('"%test%"'))
                                     ->where('u.lname', 'NOT LIKE', DB::raw('"%test%"'))
                                     ->where('u.email', 'NOT LIKE', DB::raw('"%test%"'));


    $requests = $requests->select(DB::raw("LOWER(co.country_code) as country_code"), 'u.is_student', 'u.is_alumni',                            'u.is_parent', 'u.is_counselor', 'u.is_university_rep',
                                  'u.is_organization', 'u.id as user_id',
                                   DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`fname`, 1, 1)),LOWER(SUBSTRING(`u`.`fname`, 2))) as fname"),
                                   DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`lname`, 1, 1)),LOWER(SUBSTRING(`u`.`lname`, 2))) as lname"),
                                   DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) , NULL) as user_img'));


    $requests = $requests->leftJoin('colleges as c', 'c.id', '=', 'u.current_school_id')
                         ->leftJoin('high_schools as hs', 'hs.id', '=', 'u.current_school_id')
                         ->addSelect(DB::raw("IF(u.in_college = 0, hs_grad_year, college_grad_year) as grad_year"))
                         ->addSelect(DB::raw("IF(u.in_college = 0, CONCAT(UCASE(SUBSTRING(`hs`.`school_name`, 1, 1)),LOWER(SUBSTRING(`hs`.`school_name`, 2))), CONCAT(UCASE(SUBSTRING(`c`.`school_name`, 1, 1)),LOWER(SUBSTRING(`c`.`school_name`, 2)))) as school_name"));

    $requests = $requests->get();

    $friends = DB::connection('rds1')->table('users as u')
                                     ->join(DB::raw("(select IF(
                                                    user_one_id != ".$data['user_id']." ,
                                                    user_one_id ,
                                                    user_two_id
                                                  ) as friend_uid
                                                  from
                                                    `sn_friend_relations`
                                                  where
                                                    `relation_status` = 'Accepted'
                                                  and(
                                                    `user_one_id` like ".$data['user_id']."
                                                    or `user_two_id` like ".$data['user_id']."
                                                  )) as tbl1"), "tbl1.friend_uid", "=", 'u.id')
                                     ->leftjoin('countries as co', 'co.id', '=', 'u.country_id')

                                     // don't include myself
                                     ->where('u.id', '!=', $data['user_id'])
                                     // ->where('u.is_plexuss', '!=', 1)
                                     ->where('u.is_ldy', 0)
                                     ->where('u.fname', 'NOT LIKE', DB::raw('"%test%"'))
                                     ->where('u.lname', 'NOT LIKE', DB::raw('"%test%"'))
                                     ->where('u.email', 'NOT LIKE', DB::raw('"%test%"'));


    $friends = $friends->select(DB::raw("LOWER(co.country_code) as country_code"), 'u.is_student', 'u.is_alumni',
                                'u.is_parent', 'u.is_counselor', 'u.is_university_rep',
                                'u.is_organization', 'u.id as user_id',
                                DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`fname`, 1, 1)),LOWER(SUBSTRING(`u`.`fname`, 2))) as fname"),
                                DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`lname`, 1, 1)),LOWER(SUBSTRING(`u`.`lname`, 2))) as lname"),
                                   DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) , NULL) as user_img'));

    $friends = $friends->leftJoin('colleges as c', 'c.id', '=', 'u.current_school_id')
                         ->leftJoin('high_schools as hs', 'hs.id', '=', 'u.current_school_id')
                         ->addSelect(DB::raw("IF(u.in_college = 0, hs_grad_year, college_grad_year) as grad_year"))
                         ->addSelect(DB::raw("IF(u.in_college = 0, CONCAT(UCASE(SUBSTRING(`hs`.`school_name`, 1, 1)),LOWER(SUBSTRING(`hs`.`school_name`, 2))), CONCAT(UCASE(SUBSTRING(`c`.`school_name`, 1, 1)),LOWER(SUBSTRING(`c`.`school_name`, 2)))) as school_name"));

    $friends = $friends->get();

    $dt = array();

    if (isset($requests) && !empty($requests)) {
      foreach ($requests as $key) {
        $key->user_id = $this->hashIdForSocial($key->user_id);
      }
    }

    if (isset($friends) && !empty($friends)) {
      foreach ($friends as $key) {
        $key->user_id = $this->hashIdForSocial($key->user_id);
      }
    }

    $dt['requests'] = $requests;
    $dt['friends'] = $friends;

    return response()->json($dt);
  }

  public function getFriends() {
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    $take = 20;
    isset($inputs['offset']) ? $offset = $inputs['offset'] : $offset = 0;

    $this_user_info = User::on('rds1')->find($data['user_id']);
    $in_college = 0;
    isset($this_user_info->in_college) ? $in_college = $this_user_info->in_college : NULL;


    $friend_relations = FriendRelation::on('rds1')
                                      ->select('action_user')
                                      ->where('relation_status', 'Accepted')
                                      ->where(function ($query) use ($data) {
                                                $query->orWhere('user_one_id', '=', $data['user_id'])
                                                      ->orWhere('user_two_id', '=', $data['user_id']);
                                              })
                                      ->offset($offset)
                                      ->limit(20)
                                      ->pluck('action_user')
                                      ->toArray();

    $friends = DB::connection('rds1')->table('users as u')
                                     ->leftjoin('countries as co', 'co.id', '=', 'u.country_id')
                                     ->whereIn('u.id', $friend_relations)

                                     // don't include myself
                                     ->where('u.id', '!=', $data['user_id'])
                                     // ->where('u.is_plexuss', '!=', 1)
                                     ->where('u.is_ldy', 0)
                                     ->where('u.fname', 'NOT LIKE', DB::raw('"%test%"'))
                                     ->where('u.lname', 'NOT LIKE', DB::raw('"%test%"'))
                                     ->where('u.email', 'NOT LIKE', DB::raw('"%test%"'))

                                     ->skip($offset*$take)
                                     ->take($take);

    $friends = $friends->select(DB::raw("LOWER(co.country_code) as country_code"), 'u.is_student', 'u.is_alumni',
                                'u.is_parent', 'u.is_counselor', 'u.is_university_rep',
                                'u.is_organization', 'u.id as user_id',
                                DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`fname`, 1, 1)),LOWER(SUBSTRING(`u`.`fname`, 2))) as fname"),
                                DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`lname`, 1, 1)),LOWER(SUBSTRING(`u`.`lname`, 2))) as lname"));

    if ($in_college == 1) {
      $friends = $friends->join('colleges as c', 'c.id', '=', 'u.current_school_id')
                         ->addSelect(DB::raw("CONCAT(UCASE(SUBSTRING(`c`.`school_name`, 1, 1)),LOWER(SUBSTRING(`c`.`school_name`, 2))) as `school_name`"), 'u.college_grad_year as grad_year')
                         ->addSelect(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as user_img'));

    }else{
      $friends = $friends->join('high_schools as c', 'c.id', '=', 'u.current_school_id')
                         ->addSelect(DB::raw("CONCAT(UCASE(SUBSTRING(`c`.`school_name`, 1, 1)),LOWER(SUBSTRING(`c`.`school_name`, 2))) as `school_name`"), 'u.hs_grad_year as grad_year')
                         ->addSelect(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as user_img'));

    }

    $friends = $friends->get();

    if (isset($friends) && !empty($friends)) {
      foreach ($friends as $key) {
        $key->user_id = $this->hashIdForSocial($key->user_id);
      }
    }
    $dt = array();

    $dt['friends'] = $friends;

    return response()->json($dt);
  }

  public function getNetworkingSuggestions(){

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData(true);

    ////////////PAUSING SUGGESTION FOR NOW
    $dt = array();
    return response()->json($dt);

    //////////// END

    $inputs = Request::all();
    isset($inputs['offset']) ? $offset = $inputs['offset'] : $offset = 0;
    isset($inputs['from_sic']) ? $from_sic = true : NULL;
    $take = 10;

    // Suggest to be connected with a representative from a college

    $qry1 = DB::connection('rds1')->table('users as u')
                                  ->join('organization_branch_permissions as obp', 'obp.user_id', '=', 'u.id')
                                  ->join('organization_branches as ob', 'ob.id', 'obp.organization_branch_id')
                                  ->join('colleges as c', 'c.id', '=', 'ob.school_id')
                                  ->join('recruitment as r',  function($qry) use($data){
                                      $qry->where('r.college_id', '=', 'c.id')
                                          ->where('r.user_id', '=', DB::raw($data['user_id']));
                                  })
                                  ->leftjoin('countries as co', 'co.id', '=', 'u.country_id')
                                  ->groupBy('u.id')

                                  // don't include myself
                                  ->where('u.id', '!=', $data['user_id'])

                                  // don't include user's request/pending/contacts
                                  ->whereRaw("NOT EXISTS( select 1 FROM sn_friend_relations as sfr WHERE user_one_id = ".$data['user_id']." and u.id = sfr.user_two_id)")
                                  ->whereRaw("NOT EXISTS( select 1 FROM sn_friend_relations as sfr WHERE user_two_id = ".$data['user_id']." and u.id = sfr.user_one_id)")

                                  ->where('u.is_plexuss', '!=', 1)
                                  ->where('u.is_ldy', 0)
                                  ->where('u.fname', 'NOT LIKE', DB::raw('"%test%"'))
                                  ->where('u.lname', 'NOT LIKE', DB::raw('"%test%"'))
                                  ->where('u.email', 'NOT LIKE', DB::raw('"%test%"'))

                                  ->where('ob.id', '!=', 1)

                                  ->skip($offset*$take)
                                  ->take($take)
                                  ->select(DB::raw("LOWER(co.country_code) as country_code"),'u.is_student', 'u.is_alumni', 'u.is_parent', 'u.is_counselor', 'u.is_university_rep',
                                  'u.is_organization', 'u.id as user_id',
                                  DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`fname`, 1, 1)),LOWER(SUBSTRING(`u`.`fname`, 2))) as fname"),
                                  DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`lname`, 1, 1)),LOWER(SUBSTRING(`u`.`lname`, 2))) as lname"), DB::raw("CONCAT(UCASE(SUBSTRING(`c`.`school_name`, 1, 1)),LOWER(SUBSTRING(`c`.`school_name`, 2))) as `school_name`"), DB::raw("'NULL' as `grad_year`"),
                                  DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) , NULL) as user_img'));

    if (isset($from_sic)) {
      $qry1 = $qry1->orderBy(DB::raw('ISNULL(u.profile_img_loc), u.profile_img_loc'), 'ASC');
    }else{
      $qry1 = $qry1->orderBy(DB::raw("RAND()"));
    }

    // Suggest to be connected with a Plexuss college counselor


    // Suggest to be connected with people in your own school
    $this_user_info = User::on('rds1')->find($data['user_id']);

    $in_college = 0;

    isset($this_user_info->in_college) ? $in_college = $this_user_info->in_college : NULL;

    $main_qry = DB::connection('rds1')->table('users as u')
                                      ->leftjoin('countries as co', 'co.id', '=', 'u.country_id')
                                      ->leftjoin('sn_users_account_settings as snas', 'snas.user_id', '=', 'u.id')
                                      ->where('u.in_college', $in_college)

                                      // don't include incognito users, and those who do not want to appear in suggestions or receive requests
                                      ->where(function ($qry) {
                                              $qry->where('snas.is_incognito', '=', 0)
                                                  ->orWhereNull('snas.is_incognito');
                                              })
                                      ->where(function ($qry) {
                                              $qry->where('snas.appear_in_suggestions', '=', 1)
                                                  ->orWhereNull('snas.appear_in_suggestions');
                                              })
                                      ->where(function ($qry) {
                                              $qry->where('snas.receive_requests', '=', 1)
                                                  ->orWhereNull('snas.appear_in_suggestions');
                                              })

                                      ->where('u.is_plexuss', '!=', 1)
                                      ->where('u.is_ldy', 0)
                                      ->where('u.fname', 'NOT LIKE', DB::raw('"%test%"'))
                                      ->where('u.lname', 'NOT LIKE', DB::raw('"%test%"'))
                                      ->where('u.email', 'NOT LIKE', DB::raw('"%test%"'))

                                      // don't include myself
                                      ->where('u.id', '!=', $data['user_id'])

                                      // don't include user's request/pending/contacts
                                      ->whereRaw("NOT EXISTS( select 1 FROM sn_friend_relations as sfr WHERE user_one_id = ".$data['user_id']." and u.id = sfr.user_two_id)")
                                      ->whereRaw("NOT EXISTS( select 1 FROM sn_friend_relations as sfr WHERE user_two_id = ".$data['user_id']." and u.id = sfr.user_one_id)");

    $main_qry = $main_qry->select(DB::raw("LOWER(co.country_code) as country_code"), 'u.is_student', 'u.is_alumni', 'u.is_parent', 'u.is_counselor', 'u.is_university_rep',
                                  'u.is_organization', 'u.id as user_id',
                          DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`fname`, 1, 1)),LOWER(SUBSTRING(`u`.`fname`, 2))) as fname"),
                          DB::raw("CONCAT(UCASE(SUBSTRING(`u`.`lname`, 1, 1)),LOWER(SUBSTRING(`u`.`lname`, 2))) as lname"));

    if ($in_college == 1) {
      $main_qry = $main_qry->join('colleges as c', 'c.id', '=', 'u.current_school_id')
                   ->addSelect(DB::raw("CONCAT(UCASE(SUBSTRING(`c`.`school_name`, 1, 1)),LOWER(SUBSTRING(`c`.`school_name`, 2))) as `school_name`"), 'u.college_grad_year as grad_year')
                   ->addSelect(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) , NULL) as user_img'));

    }else{
      $main_qry = $main_qry->join('high_schools as c', 'c.id', '=', 'u.current_school_id')
                   ->addSelect(DB::raw("CONCAT(UCASE(SUBSTRING(`c`.`school_name`, 1, 1)),LOWER(SUBSTRING(`c`.`school_name`, 2))) as `school_name`"), 'u.hs_grad_year as grad_year')
                   ->addSelect(DB::raw('IF(LENGTH(u.profile_img_loc), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/", u.profile_img_loc) , NULL) as user_img'));

    }

    $main_qry = $main_qry->where('c.id', $this_user_info->current_school_id)
                 ->skip($offset*$take)
                 ->take($take);

    if (isset($from_sic)) {
      $main_qry = $main_qry->orderBy(DB::raw('ISNULL(u.profile_img_loc), u.profile_img_loc'), 'ASC');
    }else{
      $main_qry = $main_qry->orderBy(DB::raw("RAND()"));
    }

    $main_qry = $main_qry->union($qry1)
                         ->get();

    if (isset($main_qry) && !empty($main_qry)) {
      foreach ($main_qry as $key) {
        $key->user_id = $this->hashIdForSocial($key->user_id);
      }
    }

    // $main_qry = count($main_qry);

    return response()->json($main_qry);
  }

  public function getLinkPreview($input = NULL){

    (!isset($input)) ? $input = Request::all() :  NULL;

    if (strpos($input['url'], 'http') === FALSE){
      $input['url'] = 'https://'.$input['url'];
    }
    if (strpos($input['url'], 'http://') !== FALSE){
      $input['url'] = str_replace("http://", "https://", $input['url']);
    }

    if (Cache::has( env('ENVIRONMENT') .'_'. 'social_getLinkPreview'.$input['url'])) {

      $cron = Cache::get( env('ENVIRONMENT') .'_'. 'social_getLinkPreview'.$input['url']);
      return $cron;

    }

    $res = SocialLinkPreview::on('rds1')->where('url', 'LIKE', "%".$input['url']."%")->first();

    if (isset($res)) {
      $res->pictures = SocialLinkPreviewPic::on('rds1')->where('sn_link_preview_id', $res->id)->pluck('path');
      $res->status   = "success";

      Cache::put( env('ENVIRONMENT') .'_'. 'social_getLinkPreview'.$input['url'], json_encode($res), 720);
      return response()->json($res);
    }

    $ret = array();

    try {

      $linkPreview = new LinkPreview($input['url']);
      $parsed = $linkPreview->getParsed();

    } catch (RequestException $e) {

      $ret['status'] = 'failed';
      return json_encode($ret);
    }

    if (empty($parsed)) {
      $ret['status'] = 'failed';

      return json_encode($ret);
    }

    foreach ($parsed as $parserName => $link) {

      $ret['url'] = $link->getUrl();
      $ret['url'] = str_replace("http://", "https://", $ret['url']);
      $ret['real_url'] = $link->getRealUrl();
      $ret['real_url'] = str_replace("http://", "https://", $ret['real_url']);
      $ret['title']  = $link->getTitle();
      $ret['description'] = $link->getDescription();
      $ret['image'] =  $link->getImage();

      $valid_img = $this->isImage($ret['image']);

      $ret['pictures'] =  NULL;
      $pics = $link->getPictures();
      if (isset($pics)) {
        foreach ($pics as $k => $v) {
          if (!empty($v)) {
            if (!$valid_img) {
              $tmp_valid_img = $this->isImage($v);
              if ($tmp_valid_img) {
                $ret['image'] = $v;
                $valid_img    = true;
              }
            }
            $ret['pictures'][] =  $v;
          }
        }
      }

      if ($link instanceof VideoLink) {
          $ret['video_id'] = $link->getVideoId();
          $ret['embed_code'] = $link->getEmbedCode();
          $ret['embed_code'] = str_replace("http://", "https://", $ret['embed_code']);
      }
      $ret['status'] = 'success';

      $attr = array();
      $attr['url'] = $ret['url'];
      $response = SocialLinkPreview::updateOrCreate($attr, $ret);
      if (isset($ret['pictures'])) {
        foreach ($ret['pictures'] as $key => $value) {
          $attr = array();
          $val  = array();
          $attr['sn_link_preview_id'] = $response->id;
          $val = $attr;
          $val['path'] = $value;

          SocialLinkPreviewPic::updateOrCreate($val, $val);

        }
      }
      Cache::put( env('ENVIRONMENT') .'_'. 'social_getLinkPreview'.$input['url'], json_encode($ret), 720);

      return json_encode($ret);
    }
  }

  /**
   * return the imported list of students
   *
   * @return json
   */
  public function getImportedContacts(){

    //Build to $data array to pass to view.
    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();
    $inputs = Request::all();

    if (isset($inputs['offset']) && $inputs['offset'] == "undefined") {
      $inputs['offset'] = 0;
    }

    isset($inputs['offset']) ? $offset = $inputs['offset'] : $offset = 0;
    $take = 50;
    $subscribed_emails = array();

    $users_invites = UsersInvite::on('rds1')->where('user_id', $data['user_id'])
            ->where('sent', 0)
            ->skip($offset*$take)
            ->take($take)
            ->select('user_id','invite_email', 'invite_name', 'source',
             'sent', 'created_at', 'updated_at');

    $invite_emails = $users_invites->pluck('invite_email')->toArray();

    $plexuss_members = User::on('rds1')->whereIn('email', $invite_emails)
                                       ->select('id', 'fname', 'lname', 'profile_img_loc', DB::raw("'N/A' AS relation_status"))
                                       ->get();

    $fr =  new FriendRelation;


    $encode = json_encode($plexuss_members);
    $plexuss_members  = json_decode($encode);

    foreach ($plexuss_members as $key) {
      $key->id  = $this->hashIdForSocial($key->id);
      $relation_status = $fr->getFriendStatus($key->id, $data['user_id']);
      $key->relation_status = isset($relation_status) ? $relation_status : 'N/A';
    }

    // dd($plexuss_members);

    if(isset($subscribed_emails)){
      $users_invites = $users_invites->whereNotIn('invite_email', $subscribed_emails);
    }

    $users_invites = $users_invites->get()->toArray();

    $data = array();
    $data['users_invites'] = $users_invites;
    $data['plexuss_members'] = $plexuss_members;

    return json_encode($data);
  }

  public function getTest() {
    $return = PostComment::with('user:id,fname,lname,profile_img_loc', 'images')->where('id', 53)->get();

    return response()->json($return);
  }

  // ADD COLLEGES BUTTON SEARCH RESULTS
  public function getAutoCompleteSearchForPortalAddColleges(){

    $viewDataController = new ViewDataController();
    $dt = $viewDataController->buildData(true);

    $data=array();
    $return_array = array();

    if ($dt['signed_in']  == 0) {
      return response()->json( $return_array );
    }

    $type = strtolower( Request::get( 'type' ) );
    $term = strtolower( Request::get( 'term' ) );
    $cid = strtolower( Request::get( 'cid' ) );

    // College Top Search
    if($type == 'college') {
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

    return response()->json( $return_array );
  }

  // Set user's endorsment
  public function setMyEndorsment(){

    $input = Request::all();

    $ppse = PublicProfileSkillsEndorsements::where('public_profile_skills_id', $input['public_profile_skills_id'])
                                           ->where('endorser_user_id', $this->decodeIdForSocial($input['endorser_user_id']))
                                           ->first();

    if (!isset($ppse)) {
      $ppse = new PublicProfileSkillsEndorsements;
      $ppse->public_profile_skills_id = $input['public_profile_skills_id'];
      $ppse->endorser_user_id         = $this->decodeIdForSocial($input['endorser_user_id']);

      $ppse->save();
    }else{
      $ppse->delete();
    }

    return "success";
  }

  public function getApplicationAndMycollegesData(){

    $arr = array();
    $npc = new NewPortalController;
    $app_data = $npc->applicationData(true);

    $fav_data = $npc->getManageSchool(true);

    $arr['MyApplicationList'] = $app_data['colleges'];
    $arr['MyCollegeList']     = $fav_data['colleges'];

    return $arr;
  }

  public function pushNotification($my_user_id, $target_user_id, $url, $message) {
    /**** for push notification ****/
    $mdt = new MobileDeviceToken;
    $my_user = User::on('rds1')->find($this->decodeIdForSocial($my_user_id));

    $recipientUserHasDeviceToken = $mdt->getToken($this->decodeIdForSocial($target_user_id));

    if( isset($recipientUserHasDeviceToken) && !empty($recipientUserHasDeviceToken) ){
      $publish_data = array();
      $publish_data['platform']       = $recipientUserHasDeviceToken->platform;
      $publish_data['device_token']   = $recipientUserHasDeviceToken->device_token;

      $publish_data['user_id']        = $target_user_id;
      $publish_data['msg']            = $my_user->fname." ".$message;
      $publish_data['thread_url']     = $url;
      $publish_data['social_app?']    = true;

      $publish_data['thread_type_id'] = $target_user_id;
      $publish_data['thread_type']    = 'users';

      $publish_data = json_encode($publish_data);
      // print_r("<pre>".$publish_data."</pre><br/>");
      Redis::publish('send:pushNotification', $publish_data);
    }
  }
}
