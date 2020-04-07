<?php

namespace App\Http\Controllers;

use Request, Session, DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\User, App\PlexussAdmin, App\Recruitment, App\PortalNotification, App\College, App\Ranking, App\Priority, App\RevenueOrganization, App\ScholarshipsUserApplied, App\UsersCustomQuestion, App\Scholarship, App\UsersAppliedColleges;

use App\Http\Controllers\UserMessageController;
use App\Http\Controllers\SocketController;
use App\Events\EventName;
use App\Events\UserSignedup;
use Illuminate\Support\Facades\Redis;
use App\Country;
use App\RecommendModalShow;

class NewPortalController extends Controller {

  public function applicationData($is_api = NULL, $user_id = NULL){
    $data = array();
    $data['user_id'] = Session::get('userinfo.id');

    if( isset($user_id) ){
      $data['user_id'] = $user_id;
    }
    // if (Cache::has( env('ENVIRONMENT') .'_NewPortalController_'. 'applicationData')) {
      
    //   $data = Cache::get( env('ENVIRONMENT') .'_NewPortalController_'. 'applicationData');

    //   Cache::put( env('ENVIRONMENT') .'_NewPortalController_'. 'applicationData', $data, 720);

    //   if( $is_api ){
    //     return $data;
    //   }
    //   return response()->json($data);
    // }

    // $p = new Priority;
    // $p = $p->getPrioritySchoolsForIntlStudents();
    // $schools = $p['undergrad'];


    // $user = new User;
    // $profile = $user->getUsersProfileData($data['user_id']);
    // $decoded = json_decode($profile);

    $uac = new UsersAppliedColleges; 
    $applyTo_schools = $uac->getAppliedColleges($data['user_id']);

    // $mergedSchools = array();

    // foreach ($applyTo_schools as $k => $key) {
    //   (int)$id = $key['college_id'];
    //   $submitted = $key['submitted'];

    //   $collegeObj = College::where( 'id', '=', $id)->first();
    //   $countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();

    //   foreach ($schools as $s) {
    //     if((int)$s['college_id'] == $id){
    //       $s['rank'] = $s['rank'] == 999999 ? 'N/A' : $s['rank'];
    //       $s['submitted'] = $submitted;
    //       $s['submitted_msg'] = $submitted == 1 ? 'Submitted!' : 'Incomplete';
    //       $split = explode('/', $s['slug']);
    //       $s['slug'] = end($split);

    //       if( $is_api ){
    //         $s['status'] = $s['submitted_msg'];
    //         $s['status_code'] = $submitted;

    //         if( isset($s['logo_url']) ){
    //           $s['logo_url'] = trim($s['logo_url']);
    //         }
    //       }

    //       if($countryObj){
    //         $s['country_code'] = strtolower($countryObj->country_code);
    //       }else{
    //         $s['country_code'] ='';
    //       }

    //       $mergedSchools[] = $s;
    //     }
    //   }

    // }

    $data['colleges'] = $applyTo_schools;

    $data['type'] = 'applications';

    Cache::put( env('ENVIRONMENT') .'_NewPortalController_'. 'applicationData', $data, 720);

    if( $is_api ){
      return $data;
    }
    return response()->json($data);
  }

  public function trashScholarships(){

    $input = Request::all();

    if(isset($input['trashList']))
    {
      $trashList = $input['trashList'];
    }
    else
    {
      $trashList = array();
      $trashList[] = $input['scholarship_id'];
    }
    $res = null;

    if ( Auth::check() )
    {
      $user = User::find( Auth::user()->id );
      $user_id=$user->id;
      $schModel = new ScholarshipsUserApplied;
      $res = $schModel->trashScholarships($user_id, $trashList);
    }

    return response()->json("success");
  }

  public function getManageSchool($is_api = null, $user_id = null) {

    if( $is_api ){
      $data = array();
      $data['user_id'] = $user_id;

    }else{
      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData();

      $input = Request::all();
    }

    // if (Cache::has( env('ENVIRONMENT') .'_NewPortalController_'. 'getManageSchool')) {
      
    //   $data = Cache::get( env('ENVIRONMENT') .'_NewPortalController_'. 'getManageSchool');

    //   Cache::put( env('ENVIRONMENT') .'_NewPortalController_'. 'getManageSchool', $data, 720);

    //   if( $is_api ){
    //     return $data;
    //   }
    //   return response()->json($data);
    // }

    $leftmenu=Request::get( 'menu' );

    $recruitment = DB::connection('rds1')->table('recruitment as r')
                                         ->join('colleges_admissions as ca', 'ca.college_id', '=', 'r.college_id')
                                         ->leftjoin('users_applied_colleges as uac', function($q){
                                                    $q->on('uac.college_id', '=', 'r.college_id')
                                                      ->on('uac.user_id', '=', 'r.user_id');

                                         })
                                         ->where( 'r.user_id', '=', $data['user_id'] )

                                         ->where( 'r.status', '=', 1 )
                                         ->where( 'r.user_recruit', 1 )
                                         ->groupBy( 'r.college_id' )
                                         ->orderBy( 'r.updated_at', 'DESC' )
                                         ->select('r.*', 'ca.application_fee_undergrad as application_fee', 'uac.id as user_applied',
                                                  'uac.submitted')
                                         ->get();

    // $data['profile_percent'] = $user->profile_percent;

    $incmt = 0;
    $data['colleges'] = array();

    foreach ( $recruitment as $key ) {
      $college_id = $key->college_id;

      $collegeObj = College::on('rds1')->where( 'id', '=', $college_id )->first();
      $countryObj = Country::on('rds1')->where( 'id', '=', $collegeObj->country_id )->first();

      $data['colleges'][$incmt]['college_id'] = $college_id;
      $data['colleges'][$incmt]['application_fee'] = $key->application_fee;
      $data['colleges'][$incmt]['school_name'] = $collegeObj->school_name;
      $data['colleges'][$incmt]['city'] = $collegeObj->city;
      $data['colleges'][$incmt]['state'] = $collegeObj->state;
          $data['colleges'][$incmt]['country_code'] = strtolower($countryObj->country_code);
      $data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$collegeObj->logo_url;
      $data['colleges'][$incmt]['school_name'] =  $collegeObj->school_name;
      $data['colleges'][$incmt]['slug'] =  $collegeObj->slug;
      $data['colleges'][$incmt]['hand_shake'] = false;
      $data['colleges'][$incmt]['in_our_network'] = $collegeObj->in_our_network;
      $data['colleges'][$incmt]['user_applied'] = isset($key->user_applied) ? true : false;

      $data['colleges'][$incmt]['submitted'] = $key->submitted;
      $data['colleges'][$incmt]['submitted'] = $key->submitted == 1 ? 'Submitted!' : 'Incomplete';

      if ( $key->college_recruit == 1 && $key->user_recruit == 1 ) {
        $data['colleges'][$incmt]['hand_shake'] = true;
      }

      if( $is_api ){
        $data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$collegeObj->logo_url;
        $data['colleges'][$incmt]['status'] = $data['colleges'][$incmt]['hand_shake'] ? 'Handshake!' : 'Pending';
        $data['colleges'][$incmt]['status_code'] = $data['colleges'][$incmt]['hand_shake'];
      }

      $rankingObj = Ranking::on('rds1')->where( 'college_id' , '=', $college_id )->first();

      if ( isset( $rankingObj->plexuss ) ) {
        $data['colleges'][$incmt]['rank'] = $rankingObj->plexuss;
      } else {
        $data['colleges'][$incmt]['rank'] = 'N/A';
      }

      $incmt++;
    }

    $data['type'] = 'yourlist';

    Cache::put( env('ENVIRONMENT') .'_NewPortalController_'. 'getManageSchool', $data, 720);

    if( $is_api ){
      return $data;
    }

    return response()->json($data);

      /*
      elseif($leftmenu=='menu2')
      {
        //$listdata = $notification->manageSchoolData($id,'recruit');
        //$data['listdata']=$listdata;
        return View('private.portal.ajax.manageschool.schoolrecruit', $data);
      }
      elseif($leftmenu=='menu3')
      {
        //$listdata = $notification->manageSchoolData($id,'recommended');
        //$data['listdata']=$listdata;
        return View('private.portal.ajax.manageschool.plexussrecommended', $data);
      }
      elseif($leftmenu=='menu4')
      {
      //  $listdata = $notification->manageSchoolData($id,'viewing');
      //  $data['listdata']=$listdata;
        return View('private.portal.ajax.manageschool.collegeviewing', $data);
      }
      elseif($leftmenu=='menu5')
      {
        //$listdata = $notification->manageSchoolData(121,'trash');
      //  $listdata = $notification->getTrashRecord($id);
      //  $data['listdata']=$listdata;
        return View('private.portal.ajax.manageschool.trash', $data);
      }

      // ajax testing in datatables
      */

      /*elseif($leftmenu=='menu6')
      {
        $listdata = $collegelist->yourListData($id);
        $data['listdata']=$listdata;
        return View('private.portal.ajax.manageschool.ajaxyourlist1', $data);
      } */
  }

  public function Recommendations($is_api = null, $user_id = NULL) {
    if ( Auth::check() || $is_api ) {

      if( $is_api ){
        $data = array();
        $id = $user_id;

      }else{
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        //get id by authcheck
        $id = Auth::id();
        $input = Request::all();
      }


      $user = User::find( $id );

      $portal_notification = DB::connection('rds1')->table('portal_notifications as pn')
                             ->join('colleges as c', 'c.id', '=', 'pn.school_id')

                             ->leftjoin('distribution_clients as dc', 'dc.college_id', '=', 'c.id')
                                       ->leftjoin('ad_redirect_campaigns as arc', 'arc.college_id', '=', 'c.id')

                             ->where( 'pn.user_id', '=', $user->id )
                             ->where( 'pn.is_recommend' , '=' , '1' )
                             ->where( 'pn.is_recommend_trash', '!=', '1' )
                             ->orderBy( 'pn.id', 'DESC' )

                             ->select('pn.*', 'c.id as college_id', 'c.school_name', 'c.city', 'c.state',
                                  'c.logo_url', 'c.slug', 'dc.ro_id as dc_ro_id', 'arc.ro_id as arc_ro_id')
                             ->groupBy('c.id')
                             ->paginate(10);

      foreach($portal_notification as $portal){
        PortalNotification::where( 'id', '=', $portal->id)
                        ->update(['is_seen'=> '1']);
      }

      $incmt = 0;
      $data['colleges'] = array();

      $recommendedBasedOnCollegeId = -1;
      foreach ( $portal_notification as $key ) {
        $ro_id = NULL;
        //dd($collegeObj);
        $collegeObj = College::where( 'id', '=', $key->college_id)->first();
            $countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();

        $data['colleges'][$incmt]['college_id'] = $key->college_id;
        $data['colleges'][$incmt]['school_name'] = $key->school_name;
        $data['colleges'][$incmt]['city'] = $key->city;
        $data['colleges'][$incmt]['state'] = $key->state;
        $data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;
        $data['colleges'][$incmt]['school_name'] =  $key->school_name;
        $data['colleges'][$incmt]['slug'] =  $key->slug;
        $data['colleges'][$incmt]['country_code'] = strtolower($countryObj->country_code);
        $data['colleges'][$incmt]['is_higher_rank_recommend'] =  $key->is_higher_rank_recommend;
        $data['colleges'][$incmt]['is_major_recommend'] =  $key->is_major_recommend;
        $data['colleges'][$incmt]['is_department_recommend'] =  $key->is_department_recommend;
        $data['colleges'][$incmt]['is_lower_tuition_recommend'] =  $key->is_lower_tuition_recommend;
        $data['colleges'][$incmt]['is_top_75_percentile_recommend'] =  $key->is_top_75_percentile_recommend;

        if( $is_api ){
          $data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;
        }


        if ( $key->recommend_based_on_college_id!= -1 && $key->recommend_based_on_college_id!= null ) {

          $recommendedBasedOnCollegeId = $key->recommend_based_on_college_id;

          $c_model = College::where( 'id', '=', $recommendedBasedOnCollegeId )->first();

          $data['colleges'][$incmt]['recommend_based_on_college_name'] =  $c_model->school_name;


        }

        $data['colleges'][$incmt]['date_added'] =  date( 'm/d/Y', strtotime( $key->updated_at ) );

        $rankingObj = Ranking::where( 'college_id' , '=', $key->college_id )->first();

        if ( isset( $rankingObj->plexuss ) ) {
          $data['colleges'][$incmt]['rank'] = $rankingObj->plexuss;
        } else {
          $data['colleges'][$incmt]['rank'] = 'N/A';
        }

        if(isset($key->arc_ro_id)){
          $ro_id = $key->arc_ro_id;
        }elseif (isset($key->dc_ro_id)) {
          $ro_id = $key->dc_ro_id;
        }

        // Check if this ro is active or not. if not, make ro_id null
        $is_ro_active = RevenueOrganization::on('rds1')->find($ro_id);

                if(isset($is_ro_active->active) && $is_ro_active->active !== 1){
          $ro_id = NULL;
        }

        if(isset($ro_id)){
          $ro = RevenueOrganization::find($ro_id);
          $this_ro = new RevenueOrganization;

          $this_temp = array();
          $this_temp['cap']   = $this_ro->getRevenueOrganizationCap($ro, $user->id);
          $this_temp['type']  = $ro->type;
          $this_temp['ro_id'] = $ro_id;

          $data['colleges'][$incmt]['ro_detail'] = $this_temp;
        }

        $incmt++;
      }

      $schModel = new ScholarshipsUserApplied;

      $scholarships = $schModel->getScholarshipsForPortal($user->id, 'recommendation');

      if(isset($scholarships) && !empty($scholarships)){
        foreach ( $scholarships as $key ) {

          $data['colleges'][$incmt]['college_id']  = $key->id;
          $data['colleges'][$incmt]['school_name'] = $key->scholarship_name;
          $data['colleges'][$incmt]['city']      = NULL;
          $data['colleges'][$incmt]['state']     = NULL;
          $data['colleges'][$incmt]['slug']      = NULL;
          $data['colleges'][$incmt]['logo_url']    = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/scholarship-hand-icon.png';
          $data['colleges'][$incmt]['type']      = 'scholarship';

          $data['colleges'][$incmt]['country_code'] = '';

          $data['colleges'][$incmt]['rank']       = 'N/A';
          $incmt++;
        }

      }

    }

    $data['type'] = 'recommendationlist';

    if( $is_api ){
      return $data;
    }

    $user_id_in_recommend_modal = RecommendModalShow::on("rds1")
                          ->select('user_id')
                          ->where('user_id','=', $data['user_id'])
                          ->first();
    // if(isset($user_id_in_recommend_modal)){
    //  echo $user_id_in_recommend_modal['user_id'];
    // }else{
    //  echo "Not record Found";
    //  DB::table('recommend_modal')->insert(
    //      ['user_id' => $data['user_id']]
    //  );
    // }
    // exit;
    if(!isset($user_id_in_recommend_modal)){
      RecommendModalShow::updateOrCreate(['user_id' => $data['user_id']]);
    }

    $latest_recruit = DB::connection('rds1')->table('plexuss.recruitment as r')
                      ->join('plexuss.colleges as c', 'r.college_id', '=', 'c.id')
                      ->leftjoin('plexuss.recommend_modal as rm', 'r.user_id', '=', 'rm.user_id')
                      ->select('r.college_id as college_id', 'c.slug as college_slug', 'c.school_name as college_name', 'rm.recommend_modal_show as show_modal')
                      ->where('r.user_id', $data['user_id'])
                      ->orderBy('r.id','DESC')
                      ->first();
    $data['latest_recruit'] = (array)$latest_recruit;

    return response()->json($data);
  }

  public function getRecruitment($is_api = null, $user_id = null) {

    if( $is_api ){
      $data = array();
      $my_user_id = $user_id;

    }else{
      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData();
      $my_user_id = $data['user_id'];
    }


    $qry = DB::connection('rds1')->table( 'colleges as c' )
                   ->join( 'recruitment as rt', 'c.id', '=', 'rt.college_id' )
                   ->join( 'colleges_ranking as cr', 'cr.college_id', '=' , 'c.id' )
                   ->leftjoin('recruitment as r2',function($join) use($my_user_id){
                    $join->on('c.id','=','r2.college_id')
                       ->where('r2.user_id','=',$my_user_id)
                       ->where('r2.user_recruit','=',1);
                    })
                       ->leftJoin('aor_colleges as ac', 'c.id', '=', 'ac.college_id')

                       ->leftjoin('distribution_clients as dc', 'dc.college_id', '=', 'c.id')
                   ->leftjoin('ad_redirect_campaigns as arc', 'arc.college_id', '=', 'c.id')

                   ->whereNull('r2.id')
                   ->select( 'c.id as college_id', 'c.school_name', 'c.slug as slug', 'c.logo_url as logo_url', 'c.city', 'c.state', 'cr.plexuss as rank', 'ac.aor_id as aor_id', 'dc.ro_id as dc_ro_id', 'arc.ro_id as arc_ro_id', 'rt.id as rt_id' )
                   ->where('rt.user_id', $my_user_id )
                   ->where('rt.college_recruit', 1 )
                   ->where('rt.user_recruit', 0)
                   ->where('rt.status', 1)
                   ->groupBy('rt.college_id')
                   ->get();


    $data['colleges'] = array();
    foreach ( $qry as $key ) {

      // Recruitment::where( 'id', '=', $key->session_regenerate_id())->update(['is_seen_recruit' => '1']);

      $collegeObj = College::where( 'id', '=', $key->college_id)->first();
          $countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();

      $ro_id = NULL;
      $tmp = array();
            $tmp['aor_id'] = isset($key->aor_id) ? $key->aor_id : NULL;
      $tmp['college_id'] = $key->college_id;
      $tmp['school_name'] = $key->school_name;
      $tmp['slug'] = $key->slug;
      $tmp['logo_url'] = $key->logo_url;
      $tmp['city'] = $key->city;
      $tmp['state'] = $key->state;
      $tmp['rank'] = isset($key->rank) ? $key->rank : 'N/A';
      $tmp['country_code'] = strtolower($countryObj->country_code);

      if( $is_api ){
        $tmp['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;
      }

      if(isset($key->arc_ro_id)){
        $ro_id = $key->arc_ro_id;
      }elseif (isset($key->dc_ro_id)) {
        $ro_id = $key->dc_ro_id;
      }

      if(isset($ro_id)){
        $ro = RevenueOrganization::find($ro_id);
        $this_ro = new RevenueOrganization;

        $this_temp = array();
        $this_temp['cap']   = $this_ro->getRevenueOrganizationCap($ro, $my_user_id);
        $this_temp['type']  = $ro->type;
        $this_temp['ro_id'] = $ro_id;

        $tmp['ro_detail'] = $this_temp;
      }

      $data['colleges'][] = $tmp;
    }

    $data['type'] = 'collegesrecruityou';

    return response()->json($data);
  }

  public function getCollegesViewedYourProfile($is_api = null, $user_id = null) {

    if( $is_api ){
      $data = array();
      $data['user_id'] = $user_id;
    }else{
      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData();
    }

    // $topnav_notifications = $data['topnav_notifications']['data'];

    // $schools_name_arr = array();

    // foreach ($topnav_notifications as $key) {
    //  //commmand 1
    //  if($key['command'] == 1 && $key['type'] == 'user'){
    //    $schools_name_arr[] = $key['name'];
    //  }
    // }


    // $my_user_id = $data['user_id'];

    // $qry = DB::table( 'colleges as c' )
    // ->join( 'colleges_ranking as cr', 'cr.college_id', '=' , 'c.id' )
    // ->select( 'c.id as college_id', 'c.school_name', 'c.slug as slug', 'c.logo_url as logo_url', 'c.city', 'c.state', 'cr.plexuss as rank' )
    // ->whereIn('c.school_name', $schools_name_arr)
    // ->get();

    $qry = DB::connection('rds1')->table('notification_topnavs as nt')
                   ->join('colleges as c', 'c.school_name', '=', 'nt.name')
                   ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
                   ->leftjoin('distribution_clients as dc', 'dc.college_id', '=', 'c.id')
                   ->leftjoin('ad_redirect_campaigns as arc', 'arc.college_id', '=', 'c.id')
                   ->where('nt.command', 1)
                   ->where('nt.msg', 'viewed your profile')
                   ->where('nt.type_id', $data['user_id'])
                   ->where('c.verified', 1)
                   ->groupBy('c.id')
                   ->orderBy('nt.updated_at', 'desc')
                   ->select('c.id as college_id', 'c.school_name', 'c.slug' , 'c.logo_url', 'c.city', 'c.state', 'cr.plexuss as rank', 'dc.ro_id as dc_ro_id', 'arc.ro_id as arc_ro_id','nt.id as nt_id')
                   ->get();

    $data['colleges'] = array();
    foreach ( $qry as $key ) {

      DB::table('notification_topnavs as nt')->where('nt.id',$key->nt_id)->update(['nt.is_read' => '1']);

      $collegeObj = College::where( 'id', '=', $key->college_id)->first();
          $countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();

      $ro_id = NULL;

      $tmp = array();
      $tmp['college_id'] = $key->college_id;
      $tmp['school_name'] = $key->school_name;
      $tmp['slug'] = $key->slug;
      $tmp['logo_url'] = $key->logo_url;
      $tmp['city'] = $key->city;
      $tmp['state'] = $key->state;
      $tmp['rank'] = isset($key->rank) ? $key->rank : 'N/A';
      $tmp['country_code'] = strtolower($countryObj->country_code);

      if(isset($key->arc_ro_id)){
        $ro_id = $key->arc_ro_id;
      }elseif (isset($key->dc_ro_id)) {
        $ro_id = $key->dc_ro_id;
      }

      if(isset($ro_id)){
        $ro = RevenueOrganization::find($ro_id);
        $this_ro = new RevenueOrganization;

        $this_temp = array();
        $this_temp['cap']   = $this_ro->getRevenueOrganizationCap($ro, $data['user_id']);
        $this_temp['type']  = $ro->type;
        $this_temp['ro_id'] = $ro_id;

        $tmp['ro_detail'] = $this_temp;
      }

      if( $is_api ){
        $tmp['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$tmp['logo_url'];
      }

      $data['colleges'][] = $tmp;
    }

    $data['type'] = 'collegesviewedprofile';

    // $this->customdd($data);
    // exit();

    return response()->json($data);
  }

  public function getUsrPortalTrash($is_api = NULL, $user_id = NULL) {
    if ( Auth::check() || (is_bool($is_api) && $is_api) ) {

      if( $is_api ){
        $data = array();
        $id = $user_id;
      }else{
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $id = Auth::id();
        $input = Request::all();
      }

      //get id by authcheck

      $user = User::find( $id );

      $trash_ids = array();

      // $recruitment = Recruitment::where('recruitment.user_id', '=', $user->id )
      //  ->where('recruitment.status', '=', 0 )
      //  ->leftjoin('recruitment as r2',function($join) use($data){
      //    $join->on('recruitment.college_id','=','r2.college_id')
      //       ->where('r2.user_id','=',$data['user_id'])
      //       ->where('r2.status','=',1);
      //    })
      //  ->whereNull('r2.id')
      //  ->select('recruitment.*')
      //  ->groupBy('recruitment.college_id')
      //  ->get();

      $recruitment = Recruitment::on('rds1')->where('recruitment.user_id', '=', $user->id )
        ->where('recruitment.status', '=', 0 )
        ->select('recruitment.*')
        ->groupBy('recruitment.college_id')
        ->get();

      foreach ( $recruitment as $school ) {

        array_push( $trash_ids, $school->college_id );

        Recruitment::where( 'id', '=', $school->id)->update(['is_seen_trash' => '1']);
      }

      $portal_notification = PortalNotification::where( 'user_id', '=', $user->id )
      ->where( 'is_recommend_trash', '=', 1 )
      ->get();

      foreach ( $portal_notification as $school ) {

        array_push( $trash_ids, $school->school_id );

      }

      $schModel = new ScholarshipsUserApplied;

      $scholarships = $schModel->getScholarshipsForPortal($user->id, 'removed');

      // $sch_trash_ids = array();
      // foreach ( $portal_notification as $school ) {
      //  $sch_trash_ids[] = $school->id;
      // }

      // dd($sch_trash_ids);
      $incmt = 0;
      $data['colleges'] = array();
      foreach ( $trash_ids as $key => $value ) {
        $college_id = $value;

        $collegeObj = College::where( 'id', '=', $college_id )->first();

        $countryObj = Country::where( 'id', '=', $collegeObj->country_id )->first();

        $data['colleges'][$incmt]['college_id'] = $college_id;
        $data['colleges'][$incmt]['school_name'] = $collegeObj->school_name;
        $data['colleges'][$incmt]['city'] = $collegeObj->city;
        $data['colleges'][$incmt]['state'] = $collegeObj->state;
        $data['colleges'][$incmt]['slug'] =  $collegeObj->slug;
        $data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$collegeObj->logo_url; //$collegeObj->logo_url;
        $data['colleges'][$incmt]['type'] = 'college';


        if($countryObj){
          $data['colleges'][$incmt]['country_code'] = strtolower($countryObj->country_code);
        }else{
          $data['colleges'][$incmt]['country_code'] ='';
        }
        if( $is_api ){
          $data['colleges'][$incmt]['logo_url'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$collegeObj->logo_url;
        }

        $rankingObj = Ranking::where( 'college_id' , '=', $college_id )->first();

        if ( isset( $rankingObj->plexuss ) ) {
          $data['colleges'][$incmt]['rank'] = $rankingObj->plexuss;
        } else {
          $data['colleges'][$incmt]['rank'] = 'N/A';
        }

        $incmt++;
      }

      if(isset($scholarships) && !empty($scholarships)){
        foreach ( $scholarships as $key ) {

          $data['colleges'][$incmt]['college_id']  = $key->id;
          $data['colleges'][$incmt]['school_name'] = $key->scholarship_name;
          $data['colleges'][$incmt]['city']      = NULL;
          $data['colleges'][$incmt]['state']     = NULL;
          $data['colleges'][$incmt]['slug']      = NULL;
          $data['colleges'][$incmt]['logo_url']    = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/scholarship-hand-icon.png';
          $data['colleges'][$incmt]['type']      = 'scholarship';

          $data['colleges'][$incmt]['country_code'] = '';

          $data['colleges'][$incmt]['rank']       = 'N/A';
          $incmt++;
        }

      }

      $data['type'] = 'trash';


      return response()->json($data);

    }
  }

  public function getPortalCollegeInfo($schoolId){
    if (Auth::check()) {
      $college_info = DB::table('colleges as cl')
      ->join('colleges_admissions as ca', 'ca.college_id' , '=', 'cl.id')
      ->join('colleges_athletics as cat' , 'cat.college_id', '=' , 'cl.id')
      ->join('colleges_tuition as ct', 'ct.college_id', '=', 'cl.id')
      ->select(DB::raw('cl.id, ca.deadline, ca.percent_admitted, cl.student_faculty_ratio, cl.student_body_total, SUM(ca.sat_read_75 + ca.sat_math_75 +ca.sat_write_75) as sat_total, ca.act_composite_75 as act,
         cat.class_name as athletic, ct.tuition_avg_in_state_ftug as inStateTuition, ct.tuition_avg_out_state_ftug as outStateTuition'))
      ->where('cl.id' , '=', $schoolId)
      ->first();

      $viewDataController = new ViewDataController();
      $data = $viewDataController->buildData();

      if (isset($college_info)) {
        $data['id'] = $college_info->id;
        $data['deadline'] = $college_info->deadline;
        $data['percent_admitted'] = $college_info->percent_admitted;
        $data['student_faculty_ratio'] = $college_info->student_faculty_ratio;
        $data['student_body_total'] = $college_info->student_body_total;
        $data['sat_total'] = $college_info->sat_total;
        $data['act'] = $college_info->act;
        $data['athletic'] = $college_info->athletic;
        $data['inStateTuition'] = $college_info->inStateTuition;
        $data['outStateTuition'] = $college_info->outStateTuition;

      } else {
        $data['deadline'] = '';
        $data['percent_admitted'] = '';
        $data['student_faculty_ratio'] = '';
        $data['student_body_total'] = '';
        $data['sat_total'] = '';
        $data['act'] = '';
        $data['athletic'] = '';
        $data['inStateTuition'] = '';
        $data['outStateTuition'] = '';
      }

      return response()->json($data);
    }
  }

  public function adduserschooltotrash($is_api = false, $user_id = null, $api_input = null){
    if (Auth::check() || $is_api){

      if( $is_api ){
        $id = $user_id;
      }else{
        $id = Auth::id();
      }

      $user = User::find($id);

      $input = Request::all();

      if( $is_api ){
        $input = $api_input;
      }

      if(isset($input['obj'])){
        $obj = $input['obj'];
        foreach ($obj as $key => $value) {

          $portal_notification_model = PortalNotification::where('user_id' , '=', $user->id)
            ->where('school_id', '=', $value)
            ->where('is_recommend' , '=' , 1)
            ->first();

          if(isset($portal_notification_model)){
            $portal_notification_model->is_recommend_trash = 1 ;
            $portal_notification_model->save();
          }else{

            $recruitment = Recruitment::where('user_id', '=', $user->id )
            ->where('college_id', '=', $value)
            ->get();

            if (!empty($recruitment)) {
              foreach($recruitment as $recr){
                $recr->status = 0;
                $recr->save();
              }
            }

          }
        }
      }

    }
  }

  public function restoreSchool(){

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $user_total_colleges = array();

    $input = Request::all();

    if(isset($input['obj'])){
      $obj = ($input['obj']);
      foreach ($obj as $key) {
        if (isset($key->type)) {
          if ($key->type == "scholarship") {
            $sua = ScholarshipsUserApplied::where('user_id', $data['user_id'])
                        ->where('scholarship_id', $key->id)
                        ->first();

            if (isset($sua)) {
                $sua->status      = $sua->last_status;
                $sua->last_status = NULL;

                $sua->save();
            }
          }elseif ($key->type == "college") {
            $recruitment = Recruitment::where('user_id', '=', $data['user_id'])
            ->where('college_id', '=', $key->id)
            ->get();

            if(!empty($recruitment)){

              $ac = new AorCollege;
              $matches = $ac->addAORCondition($key->id,$data['user_id']);

              foreach($matches as $match){

                $recruitment = Recruitment::where('user_id', '=', $data['user_id'])
                  ->where('college_id', $key->id)
                  ->where('aor_id', $match['aor_id'])
                  ->update(['status' => 1]);
              }
            }else{

              $portal_notification_model = PortalNotification::where('user_id', '=', $data['user_id'])
                ->where('school_id', '=', $key->id)
                ->first();

              $portal_notification_model->is_recommend_trash = 0;

              $portal_notification_model->save();
            }
          }
        }

      }
    }

  }
}
