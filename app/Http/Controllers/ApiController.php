<?php

namespace App\Http\Controllers;

use App\User;
use App\Country;
use App\College, App\Search;
use App\Highschool;
use App\NewsArticle;
use App\Transcript;
use App\UsersInvite;
use Request, Hash, Auth, DB, AWS, DOMDocument, Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\AjaxController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\TwilioController;
use App\GPAConverter, App\GPAConverterHelper, App\LikesTally, App\Priority, App\MobileDeviceToken, App\ConfirmToken;

use App\ScholarshipsUserApplied, App\OmniPurchaseHistory, App\PremiumUser, App\FriendRelation;

use App\Http\Controllers\GetStartedController;
use App\Http\Controllers\UserMessageController;
use App\Http\Controllers\CollegeMessageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfilePageController;
use App\Http\Controllers\ScholarshipsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OmniPayController;

use App\Http\Controllers\RankingController, App\Http\Controllers\NewsController, App\Http\Controllers\SearchController;

use \Eventviva\ImageResize;

class ApiController extends Controller
{
    public function login(){
        $input = Request::all();
        $rules = array( 'email' => 'required', 'password' => array( 'required', 'regex:/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/' ) );
        $v = Validator::make( $input, $rules );

        $ret = array();
        $ret['token']    = '';
        $ret['user_id']    = '';
        $ret['userType'] = '';
        $ret['response'] = 'failed';
        $ret['token']    = '';
        $ret['fname']    = '';
        $ret['lname']    = '';
        $ret['email']    = '';
        $ret['profile_percent']  = '';
        $ret['profile_img_loc']  = '';
        $ret['is_organization']  = '';
        $ret['org_branch_id']  = '';
        $ret['country_id']  = '';

        if ( $v->passes() ) {

            $credentials = array( 'email' => $input['email'], 'password' => $input['password'] );

            if ( Auth::attempt( $credentials, true ) ) {
                $user_id  = Auth::user()->id;
                $user = User::find($user_id);

                // Creating a token without scopes...
                $token = $user->createToken('myApp1')->accessToken;

                $public_path = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/';

                $ret['token']    = $token;
                $ret['user_id']    = $user_id;
                $ret['hashed_user_id']    = Crypt::encrypt($user_id);
                $ret['fname']    = $user->fname;
                $ret['lname']    = $user->lname;
                $ret['email']    = $user->email;
                $ret['profile_percent']  = $user->profile_percent;
                $ret['profile_img_loc']  = isset($user->profile_img_loc) ? $public_path.$user->profile_img_loc : null;
                $ret['is_organization']  = $user->is_organization;
                $ret['userType'] = 'user';
                $ret['response'] = 'success';
                $ret['country_id'] = $user->country_id;

                $mdt = new MobileDeviceToken;
                $mdt = $mdt->getToken($user_id);

                $ret['push_notification_state'] = NULL;
                if (isset($mdt)) {
                    $ret['push_notification_state'] = $mdt->active;
                }

                $ret['isPremium'] = 0;
                $pu = PremiumUser::on('rds1')->where('user_id',  $user_id)
                                             ->first();
                if (isset($pu)) {
                    $ret['isPremium'] = 1;
                    $tmp_date  = Carbon::parse($pu->created_at);
                    $ret['premium_date'] =  $tmp_date->toFormattedDateString();
                }

                if( $user->is_organization ){

                    $vo = DB::table('organizations as vo')
                        ->join('organization_branches as vob', 'vo.id', '=','vob.organization_id' )
                        ->join('organization_branch_permissions as vobp', 'vob.id', '=', 'vobp.organization_branch_id')
                        ->join('colleges as c', 'c.id', '=', 'vob.school_id')
                        ->where('vobp.user_id', '=', $user->id)
                        ->select('vo.name', 'vob.school_id', 'vo.id', 'vob.id as branch_id')->first();

                    $ret['org_branch_id'] = $vo->branch_id;
                }
            }
        }

        return json_encode($ret);
    }

    public function getNrccuaSchools($user_id) {
        $response = [];

        $colleges = DB::connection('rds1')->table('nrccua_colleges as nc')
                    ->leftJoin('colleges as c', 'c.id', '=', 'nc.college_id')
                    ->leftJoin('users as u', 'u.id', '=', DB::raw($user_id))
                    ->leftjoin('college_overview_images as coi', function($join)
                    {
                        $join->on('c.id', '=', 'coi.college_id');
                        $join->on('coi.url', '!=', DB::raw('""'));
                        $join->on('coi.is_video', '=', DB::raw(0));
                        $join->on('coi.is_tour', '=', DB::raw(0));
                    })
                    ->select('c.city', 'c.id', 'c.logo_url', 'coi.url as img_url', 'nc.rank as plexuss', 'c.school_name', 'nc.state')
                    ->orderByRaw('u.state = nc.state DESC, if (nc.rank is null, 99999, nc.rank) ASC')
                    ->groupBy('c.id')
                    ->get();

        $response['colleges'] = $colleges;

        return $response;
    }

    public function loginWithFb(){
        $input = Request::all();

        $ret = array();
        $ret['token']    = '';
        $ret['user_id']    = '';
        $ret['userType'] = '';
        $ret['response'] = 'failed';
        $ret['token']    = '';
        $ret['fname']    = '';
        $ret['lname']    = '';
        $ret['email']    = '';
        $ret['profile_percent']  = '';
        $ret['profile_img_loc']  = '';
        $ret['is_organization']  = '';
        $ret['org_branch_id']  = '';
        $ret['country_id']  = '';
        $ret['new_user'] = '';

        $public_path = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/';

        if( isset($input['accessToken']) ){
            $id = $input['id'];

            $email = isset($input['email'] ) ? $input['email'] : 'none';

            $first_name = $input['first_name'];
            $last_name = $input['last_name'];

            $rnd = str_random( 20 );
            $password =  $rnd;

            //Check if id is in the database.
            $user = User::on('rds1')->where( 'fb_id', '=', $id );

            if ($email !== 'none') {
                $user = $user->orWhere('email', $email);
            }

            $user = $user->first();

            if( $user ){

                // Creating a token without scopes...
                $token = $user->createToken('myApp1')->accessToken;

                $ret['token']    = $token;
                $ret['user_id']    = $user->id;
                $ret['hashed_user_id']    = Crypt::encrypt($user->id);
                $ret['fname']    = $user->fname;
                $ret['lname']    = $user->lname;
                $ret['email']    = $user->email;
                $ret['profile_percent']  = $user->profile_percent;
                $ret['profile_img_loc']  = isset($user->profile_img_loc) ? $public_path.$user->profile_img_loc : null;
                $ret['is_organization']  = $user->is_organization;
                $ret['new_user'] = false;
                $ret['userType'] = 'user';
                $ret['response'] = 'success';
                $ret['country_id'] = $user->country_id;

                $ret['isPremium'] = 0;
                $pu = PremiumUser::on('rds1')->where('user_id',  $ret['user_id'] )
                                             ->first();
                if (isset($pu)) {
                    $ret['isPremium'] = 1;
                    $tmp_date  = Carbon::parse($pu->created_at);
                    $ret['premium_date'] =  $tmp_date->toFormattedDateString();
                }

            }else{
                $user = new User;
                $user->fb_id = $input['id'];
                $user->fname = $input['first_name'];
                $user->lname = $input['last_name'];
                $user->email = $email;
                $user->password = Hash::make( $password );
                $user->email_confirmed = 1;

                $arr = $this->iplookup();

                if (isset($arr['countryName'])) {
                    $countries = Country::where('country_name', $arr['countryName'])->first();

                    if (isset($countries)) {
                        $user->country_id = $countries->id;
                    }
                }

                $user->save();

                // Creating a token without scopes...
                $token = $user->createToken('myApp1')->accessToken;

                $ret['token']    = $token;
                $ret['user_id']    = $user->id;
                $ret['hashed_user_id']    = Crypt::encrypt($user->id);
                $ret['fname']    = $user->fname;
                $ret['lname']    = $user->lname;
                $ret['email']    = $user->email;
                $ret['profile_percent']  = $user->profile_percent;
                $ret['profile_img_loc']  = isset($user->profile_img_loc) ? $public_path.$user->profile_img_loc : null;
                $ret['is_organization']  = $user->is_organization;
                $ret['new_user'] = true;
                $ret['userType'] = 'user';
                $ret['response'] = 'success';
                $ret['country_id'] = $user->country_id;

                $ret['isPremium'] = 0;
                $pu = PremiumUser::on('rds1')->where('user_id',  $ret['user_id'])
                                             ->first();
                if (isset($pu)) {
                    $ret['isPremium'] = 1;
                    $tmp_date  = Carbon::parse($pu->created_at);
                    $ret['premium_date'] =  $tmp_date->toFormattedDateString();
                }

                if (isset($user->email)) {
                    $confirmation = str_random( 20 );
                    $token = new ConfirmToken( array( 'token' => $confirmation ) );
                    $user->confirmtoken()->save( $token );

                    $ac = new AuthController();
                    $ac->sendconfirmationEmail( $user->id, $user->email, $confirmation );

                }
            }

        }

        return json_encode($ret);
    }

    public function signup() {
        $input = Request::all();

        $v = Validator::make( $input, User::$rules );
        $email = $input['email'];
        $confirmation = str_random( 20 );
        $adminToken = null;

        $ret = array();
        $ret['token']    = '';
        $ret['user_id']    = '';
        $ret['userType'] = '';
        $ret['response'] = 'failed';
        $ret['token']    = '';
        $ret['fname']    = '';
        $ret['lname']    = '';
        $ret['email']    = '';
        $ret['profile_percent']  = '';
        $ret['profile_img_loc']  = '';
        $ret['country_id'] = '';

        if ( $v->passes() ) {
            $user = new User;
            $user->fname = $input['fname'];
            $user->lname = $input['lname'];
            $user->email = $input['email'];
            $user->password = Hash::make( $input['password'] );

            $arr = $this->iplookup();
            if (isset($arr['countryName'])) {
                $countries = Country::where('country_name', $arr['countryName'])->first();
                if (isset($countries)) {
                    $user->country_id = $countries->id;
                }
            }

            $user->birth_date = $input['year'] . '-' . $input['month'] . '-' . $input['day'];
            $user->save();

            //why do we do TWO db saves to users table?! we should run this above and insert into the DB in one pass.
            // $token = new ConfirmToken( array( 'token' => $confirmation ) );
            // $user->confirmtoken()->save( $token );
            // $this->sendconfirmationEmail( $name, $email, $confirmation );

            Auth::loginUsingId( $user->id, true );

            // Creating a token without scopes...
            $token = $user->createToken('myApp1')->accessToken;

            $ret['token']    = $token;
            $ret['user_id']    = $user->id;
            $ret['hashed_user_id']    = Crypt::encrypt($user->id);
            $ret['fname']    = $user->fname;
            $ret['lname']    = $user->lname;
            $ret['email']    = $user->email;
            $ret['profile_percent']  = $user->profile_percent;
            $ret['profile_img_loc']  = $user->profile_img_loc;
            $ret['userType'] = 'user';
            $ret['response'] = 'success';
            $ret['country_id'] = $user->country_id;

            if (isset($user->email)) {
                $confirmation = str_random( 20 );
                $token = new ConfirmToken( array( 'token' => $confirmation ) );
                $user->confirmtoken()->save( $token );

                $ac = new AuthController();
                $ac->sendconfirmationEmail( $user->id, $user->email, $confirmation );
            }


        }else{
            $ret['signup_error'] = 'That email address has already been taken';
        }

        return json_encode($ret);
    }

    public function setup(){
        $input = Request::all();

        $screen = $input['screen'];

        switch ($input['screen']) {
            case 'getstarted':
                $input['step'] = '1';
                if( isset($input['home_schooled']) && $input['home_schooled'] == true){
                    $input['home_schooled'] = NULL;
                    $input['school_id']  =  35829;
                }
                break;

            case 'getstarted_birthday':
                $input['step'] = 'birthday';
                break;

            case 'getstarted_gpa':
                $input['step'] = '2';

                break;

            case 'getstarted_study':
                $input['step'] = '3';

                break;

            case 'getstarted_location':
                $input['step'] = '4';

                break;

            case 'getstarted_term':
                $input['step'] = '5';

                isset($input['school_term'])         ? $input['term'] = $input['school_term'] : NULL;
                isset($input['term_year'])           ? $input['year'] = $input['term_year'] : NULL;
                isset($input['family_contribution']) ? $input['payment'] = $input['family_contribution'] : NULL;
                isset($input['aid_interest'])        ? $input['intrest'] = $input['aid_interest'] : NULL;
                break;

            case 'getstarted_email':
                $input['step'] = 'email';
                break;

            default:
                break;
        }

        $gsc = new GetStartedController;
        return $gsc->save(true, $input);
    }

    public function getStartedCollegeRecommendations() {
        $input = Request::all();

        $gsc = new GetStartedController;

        return $gsc->getGetStartedThreeCollegesPins($input['user_id']);
    }

    public function saveGetStartedCollegeRecommendations() {
        $input = Request::all();

        $gsc = new GetStartedController;

        return $gsc->saveGetStartedThreeCollegesPins($input);
    }

    public function saveContactsList() {
        $input = Request::all();

        $contactsList = $input['contactsList'];
        $user_id = $input['user_id'];

        if (!empty($contactsList)) {
            foreach ($contactsList as $contact) {
                $values = [];
                $values['user_id'] = $user_id;
                $values['source'] = 'Mobile App';
                $values['invite_name'] = isset($contact['invite_name']) ? $contact['invite_name'] : NULL;
                $values['invite_email'] = isset($contact['invite_email']) ? $contact['invite_email'] : NULL;
                $values['invite_phone'] = isset($contact['invite_phone']) ? $contact['invite_phone'] : NULL;

                UsersInvite::create($values);
            }
        }

        return 'success';
    }

    public function getDiscoverColleges($category = NULL, $user_id = null){
        $ret = array();
        $ret['discover_colleges'] = array();

        switch ($category) {
            case 'near_by':
                // Near by colleges
                $hpc = new HomepageController();
                $ret['discover_colleges']['near_by'] = $hpc->getSchoolsNearYou(0, true);
                break;

            case 'ranked':
                if (Cache::has(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category)) {
                    $ret['discover_colleges']['ranked'] = Cache::get(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category);
                }else{
                    // Top Ranked School
                    $hpc = new HomepageController();
                    $ret['discover_colleges']['ranked'] = $hpc->getTopRankedSchools(0, true);
                    Cache::put(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category, $ret['discover_colleges']['ranked'], 1440);
                }
                break;

            case 'apply_to_colleges':
                if (Cache::has(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category. '_'. $user_id)) {
                    $ret['discover_colleges']['apply_to_colleges'] = Cache::get(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category. '_'. $user_id);
                }else{
                    // Apply to Colleges
                    $type    = NULL;
                    $aor_id  = NULL;

                    $p = new Priority;
                    $dt = $p->getPrioritySchoolsForIntlStudents($user_id, $aor_id, $type, 0);
                    $ret['discover_colleges']['apply_to_colleges'] = $dt['undergrad'];
                    Cache::put(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category. '_'. $user_id, $ret['discover_colleges']['apply_to_colleges'], 1440);
                }
                break;

            case 'best_matched':
                if (Cache::has(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category. '_'. $user_id)) {
                    $ret['discover_colleges']['apply_to_colleges'] = Cache::get(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category. '_'. $user_id);
                }else{
                    $input = array();

                    $input['user_id'] = $user_id;
                    $ac = new AdminController();
                    $ret = $ac->getMatchedCollegesForThisUser($input, true, 0);
                    Cache::put(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category. '_'. $user_id, $ret, 1440);
                }
                break;

            case 'favorite_college':
                if (Cache::has(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category)) {
                    $ret['discover_colleges']['apply_to_colleges'] = Cache::get(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category);
                }else{
                    $lt = new LikesTally;
                    $ret = $lt->getLikesTallyForAPI(0);
                    Cache::put(env('ENVIRONMENT').'_api_getDiscoverColleges_'. $category, $ret, 1440);
                }
                break;
        }

        return json_encode($ret);
    }

    public function getQuadArticles($category = NULL, $user_id = null, $offset = null){
        $ret = array();
        $ret['quad'] = array();

        // $hpc = new HomepageController();
        $na = new NewsArticle;

        switch ($category) {
            case 'life_after_college':
                if (Cache::has(env('ENVIRONMENT').'_api_getNewsByCategoryName_life-after-college')) {
                    $ret['quad']['life_after_college'] = Cache::get(env('ENVIRONMENT').'_api_getNewsByCategoryName_life-after-college');
                }else{
                    $tmp = $na->getNewsByCategoryName('life-after-college', 0);
                    $tmp = $this->calculateQuadArticle($tmp);

                    $ret['quad']['life_after_college'] = $tmp;
                    Cache::put(env('ENVIRONMENT').'_api_getNewsByCategoryName_life-after-college', $ret['quad']['life_after_college'], 1440);
                }
                break;
            case 'student_essays':
                if (Cache::has(env('ENVIRONMENT').'_api_getNewsByCategoryName_college-essays')) {
                    $ret['quad']['student_essays'] = Cache::get(env('ENVIRONMENT').'_api_getNewsByCategoryName_college-essays');
                }else{
                    $tmp = $na->getNewsByCategoryName('college-essays', 0);
                    $tmp = $this->calculateQuadArticle($tmp);

                    $ret['quad']['student_essays'] = $tmp;
                    Cache::put(env('ENVIRONMENT').'_api_getNewsByCategoryName_college-essays', $ret['quad']['student_essays'], 1440);
                }
                break;
            case 'college_news':
                if (Cache::has(env('ENVIRONMENT').'_api_getNewsByCategoryName_college-news')) {
                    $ret['quad']['college_news'] = Cache::get(env('ENVIRONMENT').'_api_getNewsByCategoryName_college-news');
                }else{
                    $tmp = $na->getNewsByCategoryName('college-news', 0);
                    $tmp = $this->calculateQuadArticle($tmp);

                    $ret['quad']['college_news'] = $tmp;
                    Cache::put(env('ENVIRONMENT').'_api_getNewsByCategoryName_college-news', $ret['quad']['college_news'], 1440);
                }
                break;
            case 'paying_for_college':
                if (Cache::has(env('ENVIRONMENT').'_api_getNewsByCategoryName_paying-for-college')) {
                    $ret['quad']['paying_for_college'] = Cache::get(env('ENVIRONMENT').'_api_getNewsByCategoryName_paying-for-college');
                }else{
                    $tmp = $na->getNewsByCategoryName('paying-for-college', 0);
                    $tmp = $this->calculateQuadArticle($tmp);

                    $ret['quad']['paying_for_college'] = $tmp;
                    Cache::put(env('ENVIRONMENT').'_api_getNewsByCategoryName_paying-for-college', $ret['quad']['paying_for_college'], 1440);
                }
                break;
        }
        return json_encode($ret);
    }

    public function getPremiumArticles(){
        $input = Request::all();
        $ret   = array();
        // try {
        //     $user_id = Crypt::decrypt($input['hashed_user_id']);
        // } catch (Exception $e) {
        //     $ret['status'] = 'failed';
        //     $ret['error_msg'] =  "Bad user_id";
        //     return json_encode($ret);
        // }
        // $is_premium = $pu = PremiumUser::on('rds1')->where('user_id',  $user_id)
        //                                      ->first();
        // if (!isset($is_premium)) {
        //     $ret['status'] = 'failed';
        //     $ret['error_msg'] =  "Not a premium user";
        //     return json_encode($ret);
        // }
        $news = new NewsArticle;
        $newsdata = $news->newsDetails(false, 5,'DESC', 1, false, null, true);
        $ret['status']   = "success";
        $ret['newsdata'] =  $newsdata;
        return json_encode($ret);
    }

    // College Methods
    public function getSingleCollegeInfo($section, $college_id, $user_id = NULL){

        $ret = array();
        $cc = new CollegeController();

        switch ($section) {
            case 'overview':
                if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                }else{
                    $ret = $cc->getOverviewInfo($college_id, true);

                    if (isset($ret->overview_content) && !empty($ret->overview_content)) {
                        $height = 0;
                        $tmp_content = trim($ret->overview_content);
                        $doc=new DOMDocument();
                        libxml_use_internal_errors(true);
                        $doc->loadHTML($tmp_content);
                        libxml_use_internal_errors(false);
                        $xml=simplexml_import_dom($doc); // just to make xpath more simple
                        $images=$xml->xpath('//img');
                        foreach ($images as $img) {
                            // echo $img['src'] . ' ' . $img['alt'] . ' ' . $img['title'];
                            try {
                                $size = getimagesize($img['src']);
                                if (isset($size) && isset($size[0])) {
                                    $height += $size[0];
                                }
                            } catch (\Exception $e) {
                                // continue;
                            }
                        }
                        $tmp_height = 0;
                        $tmp_height += substr_count($tmp_content,"<br>");
                        $tmp_height += substr_count($tmp_content,"<br/>");
                        $tmp_height += substr_count($tmp_content,"<br />");
                        $tmp_height += substr_count($tmp_content,"<td>");
                        $tmp_height += substr_count($tmp_content,"<li>");

                        $height = ($tmp_height * 35) + $height;
                        $cnt = strlen(strip_tags($tmp_content));

                        $content_height = ceil($cnt / 65) * 35;

                        $ret->content_height = $content_height + $height + 50;
                    }else{
                        $ret->overview_content = "<p>No description has been added yet!</p>";
                        $ret->content_height = 20;
                    }
                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                }
                break;

            case 'stats':
                if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                }else{
                    $ret = $cc->getStatsInfo($college_id, true);
                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                }
                break;

            case 'ranking':
                if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                }else{
                    $ret = $cc->getRankingInfo($college_id, true);
                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                }
                break;

            case 'admissions':
                if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                }else{
                    $ret = $cc->getAdmissionsInfo($college_id, true);
                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                }

                break;

            case 'news':
                $ret = $cc->getNewsInfo($college_id, true);
                break;

            case 'financial-aid':
                if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                }else{
                    $ret = $cc->getFinancialInfo($college_id, true);
                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                }
                break;

            case 'enrollment':
                if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                }else{
                    $ret = $cc->getEnrollmentInfo($college_id, true);
                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                }
                break;

            case 'tuition':
                if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                }else{
                    $ret = $cc->getTuitionInfo($college_id, true);
                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                }
                break;

            case 'current-students':
                // if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                //     $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                // }else{
                    $collegeModel =  new College();
                    $ret = $collegeModel->CurrentStudent($college_id, true);

                    if (isset($user_id) && $user_id != "null") {
                        if (!is_numeric($user_id)) {
                            $user_id = $this->decodeIdForSocial($user_id);
                        }
                        $friend_relations = FriendRelation::on('rds1')
                                      ->select('action_user', 'relation_status', 'user_one_id', 'user_two_id')
                                      // ->where('relation_status', 'Accepted')
                                      ->where(function ($query) use ($user_id) {
                                                $query->orWhere('user_one_id', '=', $user_id)
                                                      ->orWhere('user_two_id', '=', $user_id);
                                              })
                                      // ->pluck('action_user')
                                      // ->where('action_user', '!=', $user_id)
                                      ->get();

                        $tmp = array();

                        $friend_relations_arr = array();
                        $black_list_arr = array();

                        foreach ($friend_relations as $key) {

                            $friend_user_id = NULL;
                            if ($key->user_one_id == $user_id) {
                                $friend_user_id = $key->user_two_id;
                            }else{
                                $friend_user_id = $key->user_one_id;
                            }
                            $friend_relations_arr[] = $friend_user_id;

                            $tmp[$friend_user_id] = array();
                            if ($key->relation_status == "Accepted") {
                                $tmp[$friend_user_id]['friend_status'] = "connected";
                            }elseif ($key->relation_status == "Pending") {
                                if ($key->action_user == $user_id) {
                                    $tmp[$friend_user_id]['friend_status'] = "request_sent";
                                }else{
                                    $tmp[$friend_user_id]['friend_status'] = "request_received";
                                }
                            }elseif($key->relation_status == "Declined" || $key->relation_status == "Blocked") {
                                $black_list_arr[] = $friend_user_id;
                            }
                        }

                        $tmp_ret = array();
                        foreach ($ret as $key) {
                            if (in_array($key->user_id, $black_list_arr)) {
                                continue;
                            }elseif (in_array($key->user_id, $friend_relations_arr)) {
                                $key->has_any_relationship = 1;
                                $key->relationship         = $tmp[$key->user_id];
                            }else{
                                $key->has_any_relationship = 0;
                            }
                            $key->user_id = $this->hashIdForSocial($key->user_id);
                            $tmp_ret[] = $key;
                        }
                        $ret = $tmp_ret;
                    }else{
                        foreach ($ret as $key) {
                            $key->user_id = $this->hashIdForSocial($key->user_id);
                            $key->has_any_relationship = 0;
                        }

                        $tmp_ret = json_encode($ret);
                        $ret = json_decode($tmp_ret);
                    }
                    
                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                // }
                break;

            case 'alumni':
                // if (Cache::has(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id)) {
                //     $ret = Cache::get(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id);
                // }else{
                    $collegeModel =  new College();
                    $ret = $collegeModel->AlumniStudent($college_id, true);

                    if (isset($user_id) && $user_id != "null") {
                        if (!is_numeric($user_id)) {
                            $user_id = $this->decodeIdForSocial($user_id);
                        }
                        $friend_relations = FriendRelation::on('rds1')
                                      ->select('action_user', 'relation_status', 'user_one_id', 'user_two_id')
                                      // ->where('relation_status', 'Accepted')
                                      ->where(function ($query) use ($user_id) {
                                                $query->orWhere('user_one_id', '=', $user_id)
                                                      ->orWhere('user_two_id', '=', $user_id);
                                              })
                                      // ->pluck('action_user')
                                      // ->where('action_user', '!=', $user_id)
                                      ->get();

                        $tmp = array();

                        $friend_relations_arr = array();
                        $black_list_arr = array();

                        foreach ($friend_relations as $key) {

                            $friend_user_id = NULL;
                            if ($key->user_one_id == $user_id) {
                                $friend_user_id = $key->user_two_id;
                            }else{
                                $friend_user_id = $key->user_one_id;
                            }
                            $friend_relations_arr[] = $friend_user_id;

                            $tmp[$friend_user_id] = array();
                            if ($key->relation_status == "Accepted") {
                                $tmp[$friend_user_id]['friend_status'] = "connected";
                            }elseif ($key->relation_status == "Pending") {
                                if ($key->action_user == $user_id) {
                                    $tmp[$friend_user_id]['friend_status'] = "request_sent";
                                }else{
                                    $tmp[$friend_user_id]['friend_status'] = "request_received";
                                }
                            }elseif($key->relation_status == "Declined" || $key->relation_status == "Blocked") {
                                $black_list_arr[] = $friend_user_id;
                            }
                        }

                        $tmp_ret = array();
                        foreach ($ret as $key) {
                            if (in_array($key->user_id, $black_list_arr)) {
                                continue;
                            }elseif (in_array($key->user_id, $friend_relations_arr)) {
                                $key->has_any_relationship = 1;
                                $key->relationship         = $tmp[$key->user_id];
                            }else{
                                $key->has_any_relationship = 0;
                            }
                            $key->user_id = $this->hashIdForSocial($key->user_id);
                            $tmp_ret[] = $key;
                        }
                        $ret = $tmp_ret;
                    }else{
                        foreach ($ret as $key) {
                            $key->user_id = $this->hashIdForSocial($key->user_id);
                            $key->has_any_relationship = 0;
                        }

                        $tmp_ret = json_encode($ret);
                        $ret = json_decode($tmp_ret);
                    }
                    

                    Cache::put(env('ENVIRONMENT').'_api_getSingleCollegeInfo_'. $section.'_'. $college_id, $ret, 1440);
                // }
                break;

            default:
                # code...
                break;
        }

        $ret = (array)$ret;
        if (isset($ret['overview_content'])) {
            $ret['overview_content'] = $this->array_utf8_encode($ret['overview_content']);
        }
        return json_encode($ret);
    }

    public function getMajorAndDepartmentData($slug='', $major_slug = NULL) {
        $cc = new CollegeController;
        return $cc->department($slug, $major_slug, true);
    }
    // College method ends

    public function getProfile($user_id = null){
        // decrypt user id
        $userId = Crypt::decrypt($user_id);

        // get profile data from model
        $user = new User;
        $profile = $user->getUsersProfileData($userId);

        return $profile;
    }

    public function getLocationData() {
        return $this->iplookup();
    }

    public function myApplications($user_id = NULL){
        if (!isset($user_id)) {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData();
            $user_id = $data['user_id'];
        }
        $portal = new PortalController;
        return $portal->getApplicationData(true, $user_id);
    }

    public function myTrash($user_id = NULL){
        if (!isset($user_id)) {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData();
            $user_id = $data['user_id'];
        }
        $portal = new PortalController;
        return $portal->getUsrPortalTrash(true, $user_id);
    }

    public function myFavoriteColleges($user_id = NULL){
        if (!isset($user_id)) {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData();
            $user_id = $data['user_id'];
        }
        $portal = new PortalController;
        return $portal->getManageSchool(true, $user_id);
    }

    public function collegesSeekingMe($user_id = NULL){
        if (!isset($user_id)) {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData();
            $user_id = $data['user_id'];
        }
        $portal = new PortalController;
        return $portal->getCollegesRecruitYou(true, $user_id);
    }

    public function myRecommendations($user_id = NULL){
        if (!isset($user_id)) {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData();
            $user_id = $data['user_id'];
        }
        $portal = new PortalController;
        return $portal->getUsrRecommendationList(true, $user_id);
    }

    public function collegesViewedMe($user_id = NULL){
        if (!isset($user_id)) {
            $viewDataController = new ViewDataController();
            $data = $viewDataController->buildData();
            $user_id = $data['user_id'];
        }
        $portal = new PortalController;
        return $portal->getCollegesViewedYourProfile(true, $user_id);
    }

    public function addToTrash(){
        $input = Request::all();

        $user_id = $input['user_id'];

        $api_input = array();
        $api_input["obj"] = json_encode(array($input['college_id']));

        $ajax = new AjaxController;
        $ajax->adduserschooltotrash(true, $user_id, $api_input);

        return $input['college_id'];
    }

    public function addToFavorites(){
        $input = Request::all();

        $user_id = $input['user_id'];
        $college_id = $input['college_id'];

        $ajax = new AjaxController;
        $ajax->saveUserRecruitMe($college_id, true, $input);

        return $input['college_id'];
    }

    public function savePhone(){
        $input = Request::all();
        $input['dialing_code'] = $input['phone_code'];

        $gsc = new GetStartedController;
        return $gsc->saveNewPhone($input);
    }

    public function validatePhone(){
        $input = Request::all();
        return $this->validatePhoneNumber($input);
    }

    public function checkCode(){
        $api_input = Request::all();
        $tw = new TwilioController;
        return $tw->checkPhoneConfirmation($api_input);
    }

    public function sendCode(){
        $input = Request::all();
        $input['dialing_code'] = $input['phone_code'];
        $tw = new TwilioController;
        return $tw->sendPhoneConfirmation(null, $input);
    }

    public function scholarshipIndex(){
        $sc = new ScholarshipsController;
        return $sc->index(true);
    }

    public function rankingListing(){
        $rc = new RankingController;
        return $rc->listing(true);
    }

    public function rankingCategories(){
        $rc = new RankingController;
        return $rc->categories(true);
    }

    //News URLS
    public function newsIndex($first=false,$second=false){

        // if (Cache::has( env('ENVIRONMENT') .'_'. 'api_newsIndex_'.$first."_".$second)) {
        //     $dt = Cache::get( env('ENVIRONMENT') .'_'. 'api_newsIndex_'.$first."_".$second);
        //     return $dt;
        // }

        $nc = new NewsController;
        $tmp = $nc->index($first, false, true);

        $dt = array();
        isset($tmp['college_recruits']) ?  $dt['college_recruits'] = $tmp['college_recruits'] : NULL;
        $dt['newsdata'] = $tmp['newsdata'];
        foreach ($dt['newsdata'] as $key) {
            $key->content = str_replace("&rsquo;","'", $key->content);
            $key->content = str_replace("&ldquo;","“", $key->content); 
            $key->content = str_replace("&rdquo;","”", $key->content); 
            $key->content = str_replace("&bdquo;","„", $key->content); 
            
            $key->content = str_replace("&lsquo;","‘", $key->content); 
            $key->content = str_replace("&rsquo;","’", $key->content); 

        }
        $dt['featured_rand_news'] = $tmp['featured_rand_news'];
        foreach ($dt['featured_rand_news'] as $key) {
            $key->content = str_replace("&rsquo;","'", $key->content);
            $key->content = str_replace("&ldquo;","“", $key->content); 
            $key->content = str_replace("&rdquo;","”", $key->content); 
            $key->content = str_replace("&bdquo;","„", $key->content); 

            $key->content = str_replace("&lsquo;","‘", $key->content); 
            $key->content = str_replace("&rsquo;","’", $key->content); 
        }
        $dt['news_scat_data'] = $tmp['news_scat_data'];
        $dt['college_scat_data'] = $tmp['college_scat_data'];
        $dt['college_after_data'] = $tmp['college_after_data'];

        // Cache::put( env('ENVIRONMENT') .'_'. 'api_newsIndex_'.$first."_".$second, $dt, 720);

        return $dt;
    }

    public function viewNewsArticle($articleName, $articleType=''){
        $nc = new NewsController;
        $tmp = $nc->view($articleName, $articleType, true);

        $dt = array();
        $dt['OrderUrl1'] = $tmp['OrderUrl1'];
        $dt['OrderUrl2'] = $tmp['OrderUrl2'];
        $dt['source']  =$tmp['source'];
        $dt['news_details']  =$tmp['news_details'];

        // $dt['related_news']  =$tmp['related_news'];
        $dt['news_scat_data']  =$tmp['news_scat_data'];
        $dt['news_cat_data']  =$tmp['news_cat_data'];
        $dt['college_scat_data']  =$tmp['college_scat_data'];
        $dt['college_after_data']  =$tmp['college_after_data'];
        $dt['bread_data']  =$tmp['bread_data'];

        return $dt;
    }

    /*** utility methods ***/
    public function allMajors(){
        $ajax = new AjaxController;
        return $ajax->getAllMajors();
    }

    public function findSchool($edu_level, $school){
        if( $edu_level == 'hs' ){
            $hs = new Highschool;
            return $hs->findHighschools($school);
        }else{
            $college = new College;
            return $college->findColleges($school);
        }
    }

    public function allCountries(){
        $ajax = new AjaxController;
        return $ajax->getCountriesWithNameId();
    }

    public function allStates(){
        $ajax = new AjaxController;
        return $ajax->getAllStates();
    }

    public function allLanguages(){
        $ajax = new AjaxController;
        return $ajax->getAllLanguages();
    }

    public function gradingScales($country_id = null){
        return GPAConverterHelper::getCountryGradingScales($country_id);
    }

    public function convertToUnitedStatesGPA($grade_scale_id = null, $gpa_applicant_value = null, $conversion_type = null){
        return GPAConverter::convertToUnitedStatesGPA($grade_scale_id, $gpa_applicant_value, $conversion_type);
    }
    /*** utility methods - end ***/

    /*** message methods - start ***/
    // return list of threads for single user
    public function myMessageThreads($user_id = null, $org_branch_id = null, $loadMore = false){
        if( !isset($user_id) ){
            return 'need user id';
        }

        $data = array();
        $data['type'] = null;
        $data['is_api'] = true;
        $data['user_id'] = $user_id;
        $data['receiver_id'] = null;
        $data['loadMore'] = $loadMore;
        $data['isNotification'] = null;
        $data['org_branch_id'] = $org_branch_id;
        $data['is_organization'] = isset($org_branch_id) ? 1 : 0;

        if( isset($org_branch_id) ){
            $mc = new CollegeMessageController;
            return $mc->getThreadListHeartBeat($data['receiver_id'], $data['type'], null, $data);
        }else{
            $mc = new UserMessageController;
            return $mc->getThreadListHeartBeat($data['receiver_id'], $data['type'], null, null, $data);
        }
    }

    // return conversation of a single thread
    public function threadConvo($user_id = null, $thread_id = null, $org_id = null, $latest_msg_id = null, $first_msg_id = null){
        if( !isset($user_id) || !isset($thread_id) ){
            return 'need user id AND thread id';
        }

        $data = array();
        $data['user_id'] = $user_id;
        $data['org_id'] = $org_id;

        return $this->getUserMessages($thread_id, $latest_msg_id, $first_msg_id, $data, true);
    }

    // posts message
    public function sendMessage(){
        // look in portal_message vs sendMessageField for params
        $inputs = Request::all();
        return $this->postMessage(true, $inputs);
    }

    // sets message unread to 0
    public function messageRead($user_id = null, $thread_id = null){
        if( !isset($thread_id) || !isset($user_id) ){
            return 'need thread id AND user id';
        }

        $this->setMsgRead($thread_id, $user_id);

        return 'done';
    }

    public function saveApplication(){
        $inputs = Request::all();

        $ajax = new AjaxController;
        return $ajax->saveCollegeApplication($inputs);
    }

    public function prioritySchools($user_id = null){
        if( !isset($user_id) ) return 'need user id';

        $data = [];
        $data['user_id'] = $user_id;

        $ajax = new AjaxController;
        return $ajax->getInternationalStudentsAjax($data);
    }

    public function attendedSchools($user_id = null){
        if( !isset($user_id) ) return 'need user id';

        $data = [];
        $data['user_id'] = $user_id;

        $ajax = new AjaxController;
        return $ajax->getAttendedSchools($data);
    }

    public function searchSchools($user_id = null, $search_val = ''){
        if( !isset($user_id) ) return 'need user id';

        $data = [];
        $data['user_id'] = $user_id;
        $data['search_for_school'] = $search_val;

        $ajax = new AjaxController;
        return $ajax->findSchoolsForCollegeAndHS($data);
    }

    // Main Search method
    public function mainSearchMethod(){
        $request = Request::all();

        $sc = new SearchController;
        $dt =  array();
        
        $data = $sc->index(true, $request); 

        if (isset($request['country'])) {
            $country = Country::on('rds1')->where('country_code', $request['country'])
                                          ->first();

            isset($country) ? $dt['country_name'] = $country->country_name : NULL;
        }

        
        $dt['title'] = $data['title'];
        $dt['currentPage'] = $data['currentPage'];
        
        isset($data['querystring']) ? $dt['querystring'] = $data['querystring']: NULL;

        isset($data['type'])         ? $dt['type'] = $data['type']: NULL;
        isset($data['term'])         ? $dt['term'] = $data['term']: NULL;
        isset($data['cid'])          ? $dt['cid'] = $data['cid']: NULL;
        
        isset($data['department'])   ? $dt['department'] = $data['department'] : NULL;
        isset($data['searchData'])   ? $dt['searchData'] = $data['searchData']: NULL;
        isset($data['recordcount'])  ? $dt['recordcount'] = $data['recordcount']: NULL;
        isset($data['search_array']) ? $dt['search_array'] = $data['search_array']: NULL;

        isset($data['state_name'])   ? $dt['state_name'] = $data['state_name']: NULL;

        isset($data['headline'])             ? $dt['headline'] = $data['headline']: NULL;
        isset($data['background_image'])     ? $dt['background_image'] = $data['background_image']: NULL;
        isset($data['background_image_alt']) ? $dt['background_image_alt'] = $data['background_image_alt']: NULL;
        isset($data['flag_image'])           ? $dt['flag_image'] = $data['flag_image']: NULL;
        isset($data['flag_image_alt'])       ? $dt['flag_image_alt'] = $data['flag_image_alt']: NULL;
        isset($data['state_content'])        ? $dt['state_content'] = $data['state_content']: NULL;
        isset($data['meta_keyword'])         ? $dt['meta_keyword'] = $data['meta_keyword']: NULL;
        isset($data['meta_desc'])            ? $dt['meta_desc'] = $data['meta_desc']: NULL;
        
        return  $dt;
    }

    public function courseSubjects(){
        $ajax = new AjaxController;
        return $ajax->getCoursesSubjects();
    }

    public function allReligions(){
        $ajax = new AjaxController;
        return $ajax->getAllReligionsCustom();
    }

    public function subjectClasses($subject_id = null){
        if( !isset($subject_id) ) return 'need subject id';

        $ajax = new AjaxController;
        return $ajax->getClassesBasedOnSubjects($subject_id);
    }

    public function deleteUpload(){
        $api_input = Request::all();

        $ajax = new AjaxController;
        return $ajax->removeTranscriptAttachment($api_input);
    }

    public function saveUpload(){

         $input = Request::all();

        // return "File name : ". $input['file_name'] . " ****** image : ". $input['image'];

        if (!isset($input['file_name']) || !isset($input['image']) || !isset($input['user_id']) || !isset($input['doc_type'])) {
            return "file_name ,image, and user_id are required";
        }

        $imageDir = storage_path().'/img/';

        $filePath =  $this->saveBase64ImagePng($input['image'], $imageDir, $input['file_name']);

        $bucket_url = "asset.plexuss.com";

        $temp_file = explode(".", $input['file_name']);
        $temp_file[0] .=  '_'. date('Y_m_d_H_i_s');

        $input['file_name'] = $temp_file[0] . '.'. $temp_file[1];

        $saveas = $input['file_name'];

        $s3 = AWS::createClient('s3');
        $s3->putObject(array(
            'ACL' => 'public-read',
            'Bucket' => $bucket_url,
            'Key' => 'users/transcripts/' . $saveas,
            'SourceFile' => $filePath
        ));

        $transcript_path = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/";

        $public_path = 'https://s3-us-west-2.amazonaws.com/'. $bucket_url."/users/transcripts/".$saveas;

        $attr = array('user_id' => $input['user_id'], 'transcript_name' => $saveas);

        $val  = array('user_id' => $input['user_id'], 'transcript_name' => $saveas,
                      'transcript_path' => $transcript_path, 'school_type' => 'highschool', 'doc_type' => $input['doc_type']);

        $update = Transcript::updateOrCreate($attr, $val);


        $tmp = array();
        $tmp['transcript_id']   = $update->id;
        $tmp['transcript_url']             = $public_path;
        $tmp['transcript_type']        = $input['doc_type'];
        // $tmp['transcript_date'] = date('m/d/Y H:i:s', strtotime($update->created_at));
        $tmp['transcript_date'] = date('m/d/Y h:ia', strtotime($update->created_at));
        $tmp['file_name']       = $saveas;

        return json_encode($tmp);
    }

    public function uploadProfilePic(){

        $input = Request::all();

        if (!isset($input['file_name']) || !isset($input['image']) || !isset($input['user_id'])) {
            return "file_name and image are required";
        }

        try {
            $user_id       = Crypt::decrypt($input['user_id']);
        } catch (\Exception $e) {
            return "Bad user id";
        }

        $imageDir = storage_path().'/img/';

        $filePath =  $this->saveBase64ImagePng($input['image'], $imageDir, $input['file_name'], true);

        $bucket_url = "users/images";

        $temp_file = explode(".", $input['file_name']);
        $temp_file[0] .=  '_'. date('Y_m_d_H_i_s');

        $input['file_name'] = $temp_file[0] . '.'. $temp_file[1];

        $saveas = $input['file_name'];

        $s3 = AWS::createClient('s3');
        $s3->putObject(array(
            'ACL' => 'public-read',
            'Bucket' => 'asset.plexuss.com',
            'Key' => $bucket_url . '/' . $saveas,
            'SourceFile' => $filePath,
        ));

        $public_path = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/'. $bucket_url."/".$saveas;

        $user = User::find($user_id);
        $user->profile_img_loc = $saveas;
        $user->save();

        unlink($filePath);

        return $public_path;
    }

    private function saveBase64ImagePng($base64Image, $imageDir, $fileName, $resized = NULL){
        //set name of the image file

        $base64Image = trim($base64Image);
        $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
        $base64Image = str_replace('data:application/pdf;base64,', '', $base64Image);
        $base64Image = str_replace('data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,', '', $base64Image);
        $base64Image = str_replace('data:application/vnd.google-apps.document;base64,', '', $base64Image);

        $base64Image = str_replace(' ', '+', $base64Image);

        $imageData = base64_decode($base64Image);
        //Set image whole path here
        $filePath = $imageDir . $fileName;
        file_put_contents($filePath, $imageData);

        if (isset($resized)) {
            $size = getimagesize($filePath);
            if (isset($size[0]) && isset($size[1]) && ($size[0] > 500 || $size[1] > 500)) {
                $image = new ImageResize($filePath);
                $image->scale(50);
                $image->save($filePath);
            }
        }

       return $filePath;
    }

    public function loadMoreItems($category = NULL, $skip = NULL, $user_id = NULL){

        if( !isset($category) || !isset($skip) ){
            return 'list, category, and skip are all required';
        }

        $ret = array();
        switch( $category ){

            case 'apply_to_colleges':
                if (Cache::has(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $user_id. '_'. $skip)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $user_id. '_'. $skip);
                }else{
                    $type    = NULL;
                    $aor_id  = NULL;
                    $data    = array();
                    if (isset($user_id)) {
                        $p = new Priority;
                        $dt = $p->getPrioritySchoolsForIntlStudents($user_id, $aor_id, $type, $skip);
                        $ret = $dt['undergrad'];
                    }

                    Cache::put(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $user_id. '_'. $skip, $ret, 1440);
                }
                break;

            case 'best_matched':
                if (Cache::has(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $user_id. '_'. $skip)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $user_id. '_'. $skip);
                }else{
                    $input = array();

                    $input['user_id'] = $user_id;
                    $ac = new AdminController();
                    $ret = $ac->getMatchedCollegesForThisUser($input, true, $skip);

                    Cache::put(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $user_id. '_'. $skip, $ret, 1440);
                }
                break;

            case 'near_by':
                $hpc = new HomepageController();
                $ret = $hpc->getSchoolsNearYou($skip, true);
                break;

            case 'ranked':
                if (Cache::has(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip);
                }else{
                    $hpc = new HomepageController();
                    $ret = $hpc->getTopRankedSchools($skip, true);

                    Cache::put(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip, $ret, 1440);
                }
                break;

            case 'favorite_college':
                if (Cache::has(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip);
                }else{
                    $lt = new LikesTally;
                    $ret = $lt->getLikesTallyForAPI($skip);

                    Cache::put(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip, $ret, 1440);
                }
                break;

            case 'life_after_college':
                if (Cache::has(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip);
                }else{
                    $na = new NewsArticle;
                    $ret = $na->getNewsByCategoryName('life-after-college', $skip);
                    $ret = $this->calculateQuadArticle($ret);

                    Cache::put(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip, $ret, 1440);
                }
                break;

            case 'student_essays':
                if (Cache::has(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip);
                }else{

                    $na = new NewsArticle;
                    $ret = $na->getNewsByCategoryName('college-essays', $skip);
                    $ret = $this->calculateQuadArticle($ret);

                    Cache::put(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip, $ret, 1440);
                }
                break;

            case 'college_news':
                if (Cache::has(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip);
                }else{
                    $na = new NewsArticle;
                    $ret = $na->getNewsByCategoryName('college-news', $skip);
                    $ret = $this->calculateQuadArticle($ret);

                    Cache::put(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip, $ret, 1440);
                }
                break;

            case 'paying_for_college':
                if (Cache::has(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip)) {
                    $ret = Cache::get(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip);
                }else{
                    $na = new NewsArticle;
                    $ret = $na->getNewsByCategoryName('paying-for-college', $skip);
                    $ret = $this->calculateQuadArticle($ret);

                    Cache::put(env('ENVIRONMENT').'_api_loadMoreItems_'. $category.'_'. $skip, $ret, 1440);
                }
                break;

        }

        return $ret;
    }
    /*** message methods - end ***/

    public function searchForCollegesApplyToColleges($user_id, $term){

        if (Cache::has(env('ENVIRONMENT').'_api_searchForCollegesApplyToColleges_'. $term.'_'. $user_id)) {
            $ret = Cache::get(env('ENVIRONMENT').'_api_searchForCollegesApplyToColleges_'. $term.'_'. $user_id);
        }else{
            $ret = array();

            $type    = NULL;
            $aor_id  = NULL;
            $cnt = 0;

            $p   = new Priority;
            $dt  = $p->getPrioritySchoolsForIntlStudents($user_id, $aor_id, $type, NULL, $term);

            $tmp = array();
            $tmp['name']    = 'apply_to_colleges';
            $tmp['label']   = 'Apply to Colleges';
            $tmp['results'] = $dt['undergrad'];
            $cnt            = count($tmp['results']);
            $tmp['count']   = $cnt;
            // if (isset($tmp['results']) && !empty($tmp['results'])) {
            //     foreach ($tmp['results'] as $key) {
            //         dd($key);
            //         $key->logo_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;
            //     }
            // }
            $ret[]          = $tmp;

            Cache::put(env('ENVIRONMENT').'_api_searchForCollegesApplyToColleges_'. $term.'_'. $user_id, $ret, 1440);
        }

        return response()->json($ret);
    }

    public function searchForCollegesDiscoverColleges($user_id, $term){

        if (Cache::has(env('ENVIRONMENT').'_api_searchForCollegesDiscoverColleges_'. $term)) {
            $ret = Cache::get(env('ENVIRONMENT').'_api_searchForCollegesDiscoverColleges_'. $term);
        }else{
            $ret = array();

            $type    = NULL;
            $aor_id  = NULL;
            $cnt = 0;

            $tmp = array();
            $college        = new College;
            $tmp['name']    = 'discover_list';
            $tmp['label']   = 'Discover List';
            $tmp['results'] = $college->searchCollegesForSales($term);
            $cnt            = count($tmp['results']);
            $tmp['count']   = $cnt;
            $ret[]          = $tmp;

            Cache::put(env('ENVIRONMENT').'_api_searchForCollegesDiscoverColleges_'. $term, $ret, 1440);
        }

        return response()->json($ret);
    }

    public function searchForTheQuad($user_id, $term){

        if (Cache::has(env('ENVIRONMENT').'_api_searchForTheQuad_'. $term)) {
            $ret = Cache::get(env('ENVIRONMENT').'_api_searchForTheQuad_'. $term);
        }else{
            $ret = array();

            $type    = NULL;
            $aor_id  = NULL;
            $cnt = 0;

            $tmp = array();
            $news_article   = new NewsArticle;
            $tmp['name']    = 'the_quad';
            $tmp['label']   = 'The Quad';
            $tmp['results'] = $news_article->searchArticles($term);
            $tmp['results'] = $this->calculateQuadArticle($tmp['results']);

            $cnt            = count($tmp['results']);
            $tmp['count']   = $cnt;
            $ret[]          = $tmp;

            Cache::put(env('ENVIRONMENT').'_api_searchForTheQuad_'. $term, $ret, 1440);
        }

        return response()->json($ret);
    }

    private function calculateQuadArticle($ret){
        if (isset($ret) && !empty($ret)) {
            foreach ($ret as $key) {
                $key->img_sm      = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/".$key->img_sm;
                $key->img_lg      = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/".$key->img_lg;
                $key->authors_img = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/".$key->authors_img;

                $key->content         = $this->array_utf8_encode($key->content);
                $key->basic_content   = $this->array_utf8_encode($key->basic_content);
                $key->premium_content = $this->array_utf8_encode($key->premium_content);
                $height = 0;
                $content_height = 0;

                // if (isset($key->content) && !empty($key->content)) {
                //     $height = 0;
                //     $tmp_content = trim($key->content);
                //     $doc=new DOMDocument();
                //     libxml_use_internal_errors(true);
                //     $doc->loadHTML($tmp_content);
                //     libxml_use_internal_errors(false);
                //     $xml=simplexml_import_dom($doc); // just to make xpath more simple
                //     $images=$xml->xpath('//img');
                //     foreach ($images as $img) {
                //         // echo $img['src'] . ' ' . $img['alt'] . ' ' . $img['title'];
                //         try {
                //             $size = getimagesize($img['src']);
                //             if (isset($size) && isset($size[0])) {
                //                 $height += $size[0];
                //             }
                //         } catch (\Exception $e) {
                //             // continue;
                //         }
                //     }
                //     $tmp_height = 0;
                //     $tmp_height += substr_count($tmp_content,"<br>");
                //     $tmp_height += substr_count($tmp_content,"<br/>");
                //     $tmp_height += substr_count($tmp_content,"<br />");
                //     $tmp_height += substr_count($tmp_content,"<td>");
                //     $tmp_height += substr_count($tmp_content,"<li>");

                //     $height = ($tmp_height * 30) + $height;
                //     $cnt = strlen(strip_tags($tmp_content));

                //     $content_height = ceil($cnt / 65) * 30;

                //     $content_height = $content_height + 50;

                // }elseif (isset($key->basic_content) && !empty($key->basic_content)) {
                //     $height = 0;
                //     $tmp_content = $key->basic_content;

                //     $doc=new DOMDocument();
                //     libxml_use_internal_errors(true);
                //     $doc->loadHTML($tmp_content);
                //     libxml_use_internal_errors(false);
                //     $xml=simplexml_import_dom($doc); // just to make xpath more simple
                //     $images=$xml->xpath('//img');
                //     foreach ($images as $img) {
                //         // echo $img['src'] . ' ' . $img['alt'] . ' ' . $img['title'];
                //         try {
                //             $size = getimagesize($img['src']);
                //             if (isset($size) && isset($size[0])) {
                //                 $height += $size[0];
                //             }
                //         } catch (\Exception $e) {
                //             // continue;
                //         }
                //     }
                //     $tmp_height = 0;
                //     $tmp_height += substr_count($tmp_content,"<br>");
                //     $tmp_height += substr_count($tmp_content,"<br/>");
                //     $tmp_height += substr_count($tmp_content,"<br />");
                //     $tmp_height += substr_count($tmp_content,"<td>");
                //     $tmp_height += substr_count($tmp_content,"<li>");

                //     $height = ($tmp_height * 30) + $height;
                //     $cnt = strlen(strip_tags($tmp_content));

                //     $content_height = ceil($cnt / 65) * 30;

                //     $content_height = $content_height + 50;
                // }

                $key->height = $key->mobile_height;
            }
        }

        return $ret;
    }

    public function saveMobileDeviceToken(){
        $input = Request::all();

        $ret = array();

        try {
            $user_id       = Crypt::decrypt($input['user_id']);
        } catch (\Exception $e) {
            $ret['status']    = 'failed';
            $ret['error_msg'] = "Bad user id";

            return json_encode($ret);
        }

        $mdt = new MobileDeviceToken;

        $arr = array();
        $arr['user_id']      = $user_id;
        $arr['platform']     = $input['platform'];
        $arr['device_token'] = $input['device_token'];

        $qry = $mdt->saveToken($arr);

        $ret['status']    = 'success';

        return json_encode($ret);
    }

    public function getMobileDeviceTokenForThisUser(){
        $input = Request::all();

        $ret = array();

        try {
            $user_id       = Crypt::decrypt($input['user_id']);
        } catch (\Exception $e) {
            $ret['status']    = 'failed';
            $ret['error_msg'] = "Bad user id";

            return json_encode($ret);
        }

        $mdt = new MobileDeviceToken;

        return $mdt->getToken($user_id);
    }

    public function updateUsersPushNotification(){

        $input = Request::all();
        $ret = array();

        try {
            $user_id       = Crypt::decrypt($input['user_id']);
        } catch (\Exception $e) {
            $ret['status']    = 'failed';
            $ret['error_msg'] = "Bad user id";

            return json_encode($ret);
        }

        $update = DB::statement('UPDATE mobile_device_tokens SET active = 1 - active WHERE user_id ='. $user_id);

        $ret['status']    = 'success';

        $mdt = MobileDeviceToken::where('user_id', $user_id)->first();
        $ret['push_notification_state'] = $mdt->active;

        return json_encode($ret);
    }

    public function deleteAccount(){
        // need users user_id, email, reason, fname, lname
        $input = Request::all();
        $ajax = new AjaxController;
        $ret = $ajax->deleteUserAccount($input);
        return $ret;
    }

    public function getStudentData(){
        $input = Request::all();

        try {
            $user_id = Crypt::decrypt($input['hashed_user_id']);
        } catch (Exception $e) {
            return NULL;
        }

        $user = new User;
        $profile = $user->getUsersInfo($user_id);

        return json_encode($profile);
    }

    public function getProfileClaimToFame() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->getProfileClaimToFame($input['hashed_user_id']);
    }

    public function getSkillsAndEndorsements() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->getSkillsAndEndorsements($input['hashed_user_id']);
    }

    public function getProjectsAndPublications() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->getProjectsAndPublications($input['hashed_user_id']);
    }

    public function getLikedColleges() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->getLikedColleges($input['hashed_user_id']);
    }

    public function saveProfileClaimToFameSection() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->saveClaimToFameSection($input);
    }

    public function saveProfileLikedCollegesSection() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->saveLikedCollegesSection($input);
    }

    public function saveProfileSkillsSection() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->saveSkillsAndEndorsements($input);
    }

    public function removePublicProfilePublication() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->removePublicProfilePublication($input);
    }

    public function insertPublicProfilePublication() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->insertPublicProfilePublication($input);
    }

    public function searchCollegesWithLogos() {
        $input = Request::all();

        $ppc = new ProfilePageController();

        return $ppc->searchCollegesWithLogos($input);
    }

    public function generalSaveMeTab() {
        $input = Request::all();

        $ac = new AjaxController();

        return $ac->saveMeTab($input);
    }

    public function getAllScholarshipsNotSubmitted() {
        $input = Request::all();

        $sc = new ScholarshipsController();

        return $sc->getAllScholarshipsNotSubmitted($input['hashed_user_id']);
    }

    public function getFindCollegesInitialData(){

        $data = array();
        $searchModel = new Search();
        $depts_cat = $searchModel->getDepts();
        $data['depts_cat'] = $depts_cat;

        $collegeModel = new College();
        $depts = $collegeModel->getDepartmentCats();
        $data['depts'] = $depts;

        $listReturn=DB::connection('rds1')->select(DB::raw("select `lists`.`id` as `list_id`, `lists`.`type` as `list_type`, `lists`.`title` as `list_title`, `list_schools`.`colleges_id` as `college_id`, `C`.`school_name`, `C`.`slug`, `C`.`city`, `C`.`long_state`, `R`.`plexuss` as `plexuss_rating` from `lists` left join `list_schools` on `lists`.`id` = `list_schools`.`lists_id` left join `colleges` as `C` on `list_schools`.`colleges_id` = `C`.`id` left join `colleges_ranking` as `R` on `C`.`id` = `R`.`college_id` order by `list_type` desc, `list_title` desc, -`plexuss_rating` desc"));

        $listArray = array();

        foreach ($listReturn as $key => $value) {
            $listArray[$value->list_type][$value->list_title][] = $value;
        }

        $data['lists'] = $listArray;


        //Build the directory for the A section. needs to be revisted for perforance!
        $dirCollege = new College();
        $dirAList = $dirCollege->Colleges('A');
        $data['dirAList']=$dirAList;

        return $data;
    }
    // Selected scholarships that the user applied for but has not yet submitted.
    public function getUserSelectedScholarships() {
        $input = Request::all();

        $sc = new ScholarshipsController();

        return $sc->getUserSubmitScholarships($input['hashed_user_id']);
    }

    public function getUserScholarshipsStatus() {
        $input = Request::all();

        $user_id = null;

        $hashed_user_id = $input['hashed_user_id'];

        if (!empty($hashed_user_id)) {
            try {
                $user_id = Crypt::decrypt($hashed_user_id);
            } catch (\Exception $e) {
                return [];
            }
        }

        if (!isset($user_id)) {
            return [];
        }

        $sua = new ScholarshipsUserApplied;

        return $sua->getUsersScholarships($user_id);
    }

    public function setUserPremium(){
        $input = Request::all();
        $ret  = array();

        if (isset($input['transactionId'])  && isset($input['user_id']) && isset($input['transactionReceipt'])) {

            $oph = new OmniPurchaseHistory;

            $oph->sale_id      = $input['transactionId'];
            $oph->user_id      = $input['user_id'];
            $oph->receipt_json = $input['transactionReceipt'];
            $oph->save();

            $omni_pay_id = $oph->id;

            $usr = User::find($input['user_id']);
            $usr->profile_percent = $usr->profile_percent + 10;
            $usr->save();

            $input['fname'] = $usr->fname;
            $input['email'] = $usr->email;

            $opc = new OmniPayController;
            $opc->addUserToPremium($input, $omni_pay_id, 'onetime');


            $user = User::find($input['user_id']);
            $user_id =  $input['user_id'];

            // Creating a token without scopes...
            $public_path = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/';
            $ret['user_id']    = $user_id;
            $ret['hashed_user_id']    = Crypt::encrypt($user_id);
            $ret['fname']    = $user->fname;
            $ret['lname']    = $user->lname;
            $ret['email']    = $user->email;
            $ret['profile_percent']  = $user->profile_percent;
            $ret['profile_img_loc']  = isset($user->profile_img_loc) ? $public_path.$user->profile_img_loc : null;
            $ret['is_organization']  = $user->is_organization;
            $ret['userType'] = 'user';
            $ret['response'] = 'success';
            $ret['country_id'] = $user->country_id;

            $mdt = new MobileDeviceToken;
            $mdt = $mdt->getToken($user_id);

            $ret['push_notification_state'] = NULL;
            if (isset($mdt)) {
                $ret['push_notification_state'] = $mdt->active;
            }

            $ret['isPremium'] = 0;
            $pu = PremiumUser::on('rds1')->where('user_id',  $user_id)
                                         ->first();
            if (isset($pu)) {
                $ret['isPremium'] = 1;
                $tmp_date  = Carbon::parse($pu->created_at);
                $ret['premium_date'] =  $tmp_date->toFormattedDateString();
            }

        }else{

            $ret['response'] = 'failed';
        }

        return json_encode($ret);
    }
}
