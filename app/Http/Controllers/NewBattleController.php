<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request;
use App\User, App\PlexussAdmin, App\Recruitment, App\College;
use Illuminate\Support\Facades\Auth;

class NewBattleController extends Controller {

  public function comparison() {

    $type = Request::get('type');
    $UrlSlugs=Request::get('UrlSlugs');
    $remove_ele = Request::get('remove_ele');
    $typefor = Request::get('typefor');
    $college_id= Request::get('college_id');

    $schoolSlugArray = array_map('trim', explode(',', $UrlSlugs));

    $viewDataController = new ViewDataController();
    $data = $viewDataController->buildData();

    $data['collegeData']=array();

    $college      = new College();
    $collegeData  = $college->collegeInfo($schoolSlugArray);


    if (!empty($collegeData)) {
      $data['collegeData'] = $collegeData;
      $data['addschool'] = true;
    }

    $startloop='0';

    if (isset($type) && $type == 'Ajaxcall') {
      if (isset($remove_ele) && $remove_ele == 'true') {
        for($i = $startloop; $i < count($schoolSlugArray); $i++) {
          $data['collegeData'][$i] = '';
          $data['collegeData'][$i] = (object)$data['collegeData'][$i];
        }
      }

      if ($typefor == 'json') {
        $college = College::where('id', '=',$college_id)->first()->toArray();
        $return_array = array();

        if($college['logo_url'] != '') {
          $logo_img = $logoPath.$college['logo_url'].'';
        }
        else {
          $logo_img = $defaultLogo;
        }

        $return_array['logo_url'] = $logo_img;
        $return_array['slug'] = $college['slug'];
        $return_array['school_name'] = $college['school_name'];

        return Response::json($return_array);
      }
      else {
        // return View('private.battle.comparisonColumn', $data);
        return response()->json($data['collegeData']);
      }
    }
    else {
      if(count($data['collegeData']) < 3) {
        if(count($collegeData) == 1)
          {$startloop = 1;}
        elseif(count($collegeData) == 2)
          {$startloop = 2;}

        for($i = $startloop; $i < 3; $i++) {
          $data['collegeData'][$i] = '';
          $data['collegeData'][$i] = (object)$data['collegeData'][$i];
        }
      }

      $quizs = new QuizController();
      $data['quizInfo'] = $quizs->LoadQuiz();

      $data['right_handside_carousel'] = $this->getRightHandSide($data);

      return response()->json($data);
    }
  }
}
