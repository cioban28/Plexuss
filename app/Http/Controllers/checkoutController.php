<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, DB, Session;
use App\User, App\PlexussAdmin, App\PortalNotification, App\Country, App\NewsArticle, App\UserClosedPin, App\Recruitment, App\LocalizationPage,  App\UsersPortalEmailEffortLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\RecommendModalShow;

use App\Http\Controllers\MandrillAutomationController;

class checkoutController extends Controller
{
    public function index(){

      //Template base arrays
      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData();

      $input = Request::all();

      if (isset($input)) {

        $arr = $this->iplookup();
        if (!Session::has('signup_params')) {
          Session::put('signup_params', $input);
        }
        if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
          Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
        }

      }

      // Localization code here
      $lp = new LocalizationPage;
      if (isset($input['lang']) && $input['lang'] != "en") {

        $data['page_content'] = $lp->getPageContent('premium-plans-info', $input['lang']);

        if (empty($data['page_content'])) {
          $data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
        }

      }else{
        $data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
      }
      // Localization code ends here

      $data['title'] = 'Plexuss Premium';
      $data['currentPage'] = 'premium';
      return View('checkout.index', $data);
    }

    public function congratulationsPage(){

      //Template base arrays
      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData();

      $input = Request::all();

      if (isset($input)) {

        $arr = $this->iplookup();
        if (!Session::has('signup_params')) {
          Session::put('signup_params', $input);
        }
        if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
          Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
        }

      }

      // Localization code here
      // $lp = new LocalizationPage;
      // if (isset($input['lang']) && $input['lang'] != "en") {

      //   $data['page_content'] = $lp->getPageContent('premium-plans-info', $input['lang']);

      //   if (empty($data['page_content'])) {
      //     $data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
      //   }

      // }else{
      //   $data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
      // }
      // Localization code ends here

      $template_name = 'premium_email';
      
      if ($data['signed_in'] == 1) {
        $qry = UsersPortalEmailEffortLog::on('rds1')
                                        ->where('user_id', $data['user_id'])
                                        ->where('template_name', $template_name)
                                        ->first();
      }
      if(!isset($qry) && $data['signed_in'] == 1){

        $mac = new MandrillAutomationController();
        $upeel = new UsersPortalEmailEffortLog;

        $params = array();

        $params['USER_IMAGE_URL'] = isset($data['profile_img_loc']) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'] : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
        
        $params['FNAME']          = ucwords(strtolower($data['fname']));
        $params['LNAME']          = ucwords(strtolower($data['lname']));

        $reply_email = "support@plexuss.com";

        $mac->generalEmailSend($reply_email, $template_name, $params, $data['email']);


        $attr['user_id']       = $data['user_id'];
        $attr['template_name'] = $template_name;
        $attr['params']        = json_encode($params);

        $upeel->saveLog($attr);
      }

      
      $src="/images/profile/default.png";
      if($data['profile_img_loc']!=""){
        $src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
      }
      $data['profile_img_loc'] = $src;

      $data['title'] = 'Plexuss Premium';
      $data['currentPage'] = 'congratulation-premium';
      return View('checkout.congratulation', $data);
    }

    public function confirmationPage(){

      //Template base arrays
      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData();

      $input = Request::all();

      if (isset($input)) {

        $arr = $this->iplookup();
        if (!Session::has('signup_params')) {
          Session::put('signup_params', $input);
        }
        if (!Cache::has(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'])) {
          Cache::put(env('ENVIRONMENT') .'_'.'signup_params_'.$arr['ip'], $input, 5);
        }

      }

      // Localization code here
      $lp = new LocalizationPage;
      if (isset($input['lang']) && $input['lang'] != "en") {

        $data['page_content'] = $lp->getPageContent('premium-plans-info', $input['lang']);

        if (empty($data['page_content'])) {
          $data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
        }

      }else{
        $data['page_content'] = $lp->getPageContent('premium-plans-info', 'en');
      }
      // Localization code ends here

      $data['title'] = 'Plexuss Premium';
      $data['currentPage'] = 'premium';
      return View('checkout.confirmation', $data);
    }

    public function checkoutPremiumPage(){

      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData();

      $data['title'] = 'Checkout Page';
      $data['currentPage'] = 'checkout-premium';

      // $input = Request::all();

      // $str = '';

      // foreach ($input as $key => $value) {
      //  $str .= $key.'='.$value."&";
      // }

      // $str = rtrim($str, "&");

      // $data['url_params'] = $str;

      // isset($input['aid']) ?  $data['aid']  = $input['aid']  : null;
      // isset($input['type']) ? $data['type'] = $input['type'] : null;

      if( isset($data['profile_img_loc']) ){
        $data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
      }

      return View('premium.index', $data);
    }
}
