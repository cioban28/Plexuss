<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt, Illuminate\Support\Facades\Auth;
use App\User, App\PlexussAdmin, App\AdPage, App\AdAffiliate, App\PlexussBannerCopy, App\CollegesInternationalTab, App\CollegeOverviewImages;
use App\Search, App\Scholarship, App\Scholarshipcms, App\ScholarshipProvider, App\UsersCustomQuestion, App\ScholarshipsUserApplied;
use App\CollegeRecommendationFilters;
use App\CollegeRecommendationFilterLogs;

class NewScholarshipsController extends Controller{

  public function index() {
    $input = Request::all();

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $data['title'] = 'Plexuss Colleges | Scholarships';
    $data['currentPage'] = 'scholarships';

    $src = '/images/profile/default.png';

    $user_id = null;
    $data['oneapp_status'] = null;

    if ($data['signed_in'] == 1) {
      $user_id = $data['user_id'];
      //get if user OneApp step
      $appModel = new UsersCustomQuestion();
      $oneapp_status = $appModel->getUserStatus($user_id);
      $data['oneapp_status'] = $oneapp_status;
      
    }

    $schModel = new Scholarship();

    $fincount = 0;
    if ($user_id) {
      $res = $schModel->getAllScholarshipsNotApplied($user_id, $input);
    }
    else {
      $res = $schModel->getAllScholarshipsFilters($input);
    }

    $data['fincount'] = $fincount;
    $data['scholarships'] =  $res;

    //if filters set in url query -- apply
    if(isset($input['rangeF'])){
      $data['rangeF'] = $input['rangeF'];
    }

    $dt = array();
    $dt['scholarships'] = $data['scholarships'];
    $dt['fincount'] = $data['fincount'];
    $dt['title'] = $data['title'];
    $dt['currentPage'] = $data['currentPage'];

    return response()->json(['data' => $dt]);
  }

  public function queueScholarship($input = NULL) {
    if (!isset($input)) {
      $input = Request::all();
    }

    $user_id = isset($input['user_id']) ? $input['user_id'] : null;
    if (!isset($user_id) || $user_id == null) {
      $user_id = Session::get('userinfo.id');
    }

    $scholarship = $input['scholarship'];
    $status = $input['status'];

    $schModel = new ScholarshipsUserApplied();

    $sch = array();
    $sch['user_id'] = $user_id;
    $sch['scholarship_id'] = $scholarship;
    $sch['status'] = $status;

    $res = $schModel->updateOrCreate(['user_id'=>$user_id, 'scholarship_id'=>$scholarship ], $sch);

    if ($res == false) {
      return 'error';
    }

    return response()->json([$input]);
  }
}
