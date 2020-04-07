<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Request, DB;
use App\User;
use App\UsersSalesControl;

class PublisherController extends Controller
{
	//index
	public function index(){

		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		
		$data['title'] = 'Plexuss Publisher';
		$data['currentPage'] = 'plex-publisher';
		//$data['ajaxtoken'] = $token['token'];

		return View('publisher.articlePostView', $data);
	}

	//post article to dev and live
	public function postArticle(){

		$input = Request::all();

		$tmp = array();
		$insertArticle = array();

		$tmp['news_subcategory_id'] = $this->getSubCategoryId($input['articleData']['category']); //$input['articleData']['category'] ? $input['articleData']['category'] : 3;
		$tmp['source'] = "external";
		$tmp['external_name'] = $input['articleData']['author_institution'] ? $input['articleData']['author_institution'] : "Plexuss.com";
		$tmp['external_author'] = $input['articleData']['author_name'] ? $input['articleData']['author_name'] : 'Plexuss';
		$tmp['external_url'] = $input['articleData']['author_link'] ? $input['articleData']['author_link'] : "https://plexuss.com/";
		$tmp['slug'] = $input['articleData']['slug'];
		$tmp['title'] = $input['articleData']['article_title'];
		$tmp['author'] = 147;
		$tmp['img_sm'] = $input['articleData']['sm_img_path'];
		$tmp['img_lg'] = $input['articleData']['lg_img_path'];
		$tmp['updated_at'] = date("Y-m-d H:i:s");
		$tmp['created_at'] = date("Y-m-d H:i:s");
		$tmp['page_title'] = $input['articleData']['meta_title'];
		$tmp['meta_keywords'] = $input['articleData']['meta_keywords'];
		$tmp['meta_description'] = $input['articleData']['meta_description'];
		$tmp['live_status'] = 1;
		$tmp['content'] = $input['articleData']['article_body_content'] ? $input['articleData']['article_body_content'] : null;
		$tmp['basic_content'] = $input['articleData']['article_body_content_basic'] ? $input['articleData']['article_body_content_basic'] : null;
		$tmp['premium_content'] = $input['articleData']['article_body_content_premium'] ? $input['articleData']['article_body_content_premium'] : null;
		$tmp['highlighted'] = $input['articleData']['highlighted'] ? $input['articleData']['highlighted'] : ' '; 
		$tmp['authors_img'] = $input['articleData']['author_img'] ? $input['articleData']['author_img'] : 'author_default.png';
		$tmp['authors_description'] = $input['articleData']['author_description'] ? $input['articleData']['author_description'] : null;
		$tmp['authors_profile_link'] = $input['articleData']['author_link'] ? $input['articleData']['author_link'] : null;
        $tmp['meta_url']  = $input['articleData']['meta_url'];
		$insertArticle = $tmp;
		$insertReturn = null;

		if( isset($insertArticle) ){
			$insertReturn = DB::table('news_articles')->insert($insertArticle);
		}


		if( $insertReturn ){
			return 'success';
		}else{
			return 'fail';
		}
	}

	//auto save article
	public function autoSaveArticle( $id = null ){

		$input = Request::all();

		$tmp = array();
		$saveArticle = array();

		$tmp['news_subcategory_id'] = $this->getSubCategoryId($input['articleData']['category']); //$input['articleData']['category'] ? $input['articleData']['category'] : 3;
		$tmp['source'] = "external";
		$tmp['external_name'] = "Plexuss.com";
		$tmp['external_author'] = $input['articleData']['author_name'] ? $input['articleData']['author_name'] : 'Plexuss';
		$tmp['external_url'] = "https://plexuss.com/";
		$tmp['slug'] = $input['articleData']['slug'];
		$tmp['title'] = $input['articleData']['article_title'];
		$tmp['author'] = 147;
		$tmp['img_sm'] = $input['articleData']['sm_img_path'];
		$tmp['img_lg'] = $input['articleData']['lg_img_path'];
		$tmp['updated_at'] = date("Y-m-d H:i:s");
		$tmp['created_at'] = date("Y-m-d H:i:s");
		$tmp['page_title'] = $input['articleData']['meta_title'];
		$tmp['meta_keywords'] = $input['articleData']['meta_keywords'];
		$tmp['meta_description'] = $input['articleData']['meta_description'];
		$tmp['live_status'] = 0;
		$tmp['content'] = $input['articleData']['article_body_content'] ? $input['articleData']['article_body_content'] : null;
		$tmp['highlighted'] =  $input['articleData']['highlighted'] ? $input['articleData']['highlighted'] : ' ';
		$tmp['authors_img'] = $input['articleData']['author_img'] ? $input['articleData']['author_img'] : 'author_default.png';
		$tmp['authors_description'] = $input['articleData']['author_description'] ? $input['articleData']['author_description'] : null;
		$tmp['authors_profile_link'] = $input['articleData']['author_link'] ? $input['articleData']['author_link'] : null;

		$saveArticle = $tmp;
		$returnFromSavingId = null;

		if( $id == null ){
			$returnFromSavingId = DB::connection('dev')->table('news_articles_temporary')->insertGetId($saveArticle);
			return $returnFromSavingId;
		}else{
			$returnFromSavingId = DB::connection('dev')->table('news_articles_temporary')->where('id', $id)->update($saveArticle);
			return 'successfully updated';
		}

	}

	//ajax function to get and return list of news authors
	public function getAuthors(){
		
		$getAllAuthorsReturn = DB::table('news_authors')->get();

		return $getAllAuthorsReturn;	
	}

	//ajax function to get and return a list of saved/published articles
	public function getAllArticles(){

		$getAllArticlesReturn = DB::table('news_articles_temporary')->orderBy('id', 'desc')->take(10)->get();

		return $getAllArticlesReturn;	
	}

	//gets article subcategory
	private function getSubCategoryId( $category_name ){
		$subcategory_id = 0;

		switch ($category_name) {
			case 'Getting Into College':
				$subcategory_id = 1;
				break;
			case 'Ranking':
				$subcategory_id = 2;
				break;
			case 'Campus Life':
				$subcategory_id = 3;
				break;
			case 'College Sports':
				$subcategory_id = 4;
				break;
			case 'Celebrity Alma Mater':
				$subcategory_id = 5;
				break;
			case 'Financial-Aid':
				$subcategory_id = 6;
				break;
			case 'Careers':
				$subcategory_id = 7;
				break;
			case 'Blog':
				$subcategory_id = 22;
				break;
			case 'Plexuss New Features':
				$subcategory_id = 12;
				break;
			case 'B2B Press':
				$subcategory_id = 11;
				break;	
			default:
				$subcategory_id = 3;
				break;
		}

		return $subcategory_id;
	}

	//post event to dev and live
	public function postEvent(){

		$input = Request::all();
		if(!empty($input['file'])){
			$file_upload = $this->generalUploadDoc($input, 'file', 'asset.plexuss.com/events');
		}
		if(isset($input['event_date_time']) &&  isset($input['event_title']) && isset($input['event_description']) && isset($input['event_city']) ){
			$tmp = array();
			$insertEvent = array();

			$event_dates = array_map('trim',explode('to',$input['event_date_time']));

			$start_datetime = new \DateTime($event_dates[0]);
			$start_date = $start_datetime->format('Y-m-d');
			$start_time = $start_datetime->format('H:i:s');

			$end_datetime = new \DateTime($event_dates[1]);
			$end_date = $end_datetime->format('Y-m-d');
			$end_time = $end_datetime->format('H:i:s');

			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
			$tmp['user_id'] = $data['user_id'];
			$tmp['whocanseethis'] = "public";
			$tmp['event_title'] = $input['event_title'];
			$tmp['event_url'] = $input['event_url'];
			$tmp['event_start_date'] = $start_date;
			$tmp['event_start_time'] = $start_time;
			$tmp['event_end_date'] = $end_date;

			$tmp['event_end_time'] = $end_time;
			$tmp['event_image'] = isset($file_upload)?$file_upload['url']:null;
			$tmp['event_description'] = $input['event_description'];
			$tmp['updated_at'] = date("Y-m-d H:i:s");
			$tmp['created_at'] = date("Y-m-d H:i:s");
          

           $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.str_replace(' ','+',$input['event_city']).'&key=AIzaSyCcfvCwbcQmmGDFC98uCak_BuvJjOxvJw8';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $responsefromcurl = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($responsefromcurl, true);


            if($result['status'] == "ZERO_RESULTS")
            {
            	$response['status'] = "fail";
				$response['msg'] = "Invalid City Name";
				return $response;
            }
            elseif($result['status'] == "OK"){

          $tmp['event_city'] = $result['results'][0]['address_components'][0]['long_name'];
          $tmp['event_country'] = trim(last(explode(',' , $result['results'][0]['formatted_address'])));
          $tmp['event_city_full_address']=$result['results'][0]['formatted_address'];
          $tmp['event_city_longitude'] = $result['results'][0]['geometry']['location']['lng'];
          $tmp['event_city_latitude'] = $result['results'][0]['geometry']['location']['lat'];
            }
          else{

                $response['status'] = "fail";
				$response['msg'] = "Currently Somethings Goes Wrong.Please Try Again";
				return $response;

          }


			$insertEvent = $tmp;
			$insertReturn = null;

			if( isset($insertEvent) ){
				$insertReturn = DB::table('college_events')->insert($insertEvent);
			}

			if( $insertReturn ){
				$response['status'] = "success";
				$response['msg'] = "Event Added Succesfully";
				return $response;
			}else{
				$response['status'] = "fail";
				$response['msg'] = "Event not added";
				return $response;
			}
		} else {
			$response['status'] = "fail";
			$response['msg'] = "Data missing";
			return $response;
		}
	}

	//ajax function to get and return a list of saved events
	public function getAllEvents(){

		$getAllEventsReturn = DB::table('college_events')->orderBy('event_start_date', 'desc')->get();

		return $getAllEventsReturn;
	}

	public function updateEvent(){
		$input = Request::all();

		$event_id = $input['event_id'];
		if(!empty($input['file'])){
			$file_upload = $this->generalUploadDoc($input, 'file', 'asset.plexuss.com/events');
		}

		$tmp = array();
		$updateEvent = array();
		if(isset($input['event_date_time'])){
			$event_dates = array_map('trim',explode('to',$input['event_date_time']));

			$start_datetime = new \DateTime($event_dates[0]);
			$start_date = $start_datetime->format('Y-m-d');
			$start_time = $start_datetime->format('H:i:s');

			$end_datetime = new \DateTime($event_dates[1]);
			$end_date = $end_datetime->format('Y-m-d');
			$end_time = $end_datetime->format('H:i:s');

			$tmp['event_start_date'] = $start_date;
			$tmp['event_start_time'] = $start_time;
			$tmp['event_end_date'] = $end_date;
			$tmp['event_end_time'] = $end_time;
		}
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$tmp['user_id'] = $data['user_id'];
		if(isset($input['event_title'])){
			$tmp['event_title'] = $input['event_title'];
		}
		if(isset($input['event_url'])){
			$tmp['event_url'] = $input['event_url'];
		}
		if(isset($file_upload)){
			$tmp['event_image'] = $file_upload['url'];
		}
		if(isset($input['event_description'])){
			$tmp['event_description'] = $input['event_description'];
		}
		$tmp['updated_at'] = date("Y-m-d H:i:s");

        
        if(isset($input['event_city'])){
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.str_replace(' ','+',$input['event_city']).'&key=AIzaSyCcfvCwbcQmmGDFC98uCak_BuvJjOxvJw8';


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $responsefromcurl = curl_exec($ch);
            
           
            $result = json_decode($responsefromcurl, true);

          
            if($result['status'] == "ZERO_RESULTS")
            {
            	$response['status'] = "fail";
				$response['msg'] = "Invalid City Name";
				return $response;
            }
            elseif($result['status'] == "OK"){

          $tmp['event_city'] = $result['results'][0]['address_components'][0]['long_name'];
          $tmp['event_country'] = trim(last(explode(',' , $result['results'][0]['formatted_address'])));
          $tmp['event_city_full_address']=$result['results'][0]['formatted_address'];
          $tmp['event_city_longitude'] = $result['results'][0]['geometry']['location']['lng'];
          $tmp['event_city_latitude'] = $result['results'][0]['geometry']['location']['lat'];
            }
            
          else{

                $response['status'] = "fail";
				$response['msg'] = "Currently Somethings Goes Wrong.Please Try Again";
				return $response;

          }
      }

		$updateEvent = $tmp;
		$updateReturn = null;

		if( isset($updateEvent) ){
			$updateReturn = DB::table('college_events')->where('id', $event_id)->update($updateEvent);
		}

		if( $updateReturn ){
			$response['status'] = "success";
			$response['msg'] = "Event Updated Successfully";
			return $response;
		}else{
			$response['status'] = "fail";
			$response['msg'] = "Event not updated";
			return $response;
		}
	}

	public function removeEvent(){
		$input = Request::all();
		$event_id = $input['event_id'];

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		 if (!isset($event_id) || !isset($data['user_id'])) {
            $response['status'] = 'fail';
			$response['msg'] = 'Data missing';
			return $response;
        }

        $ce = DB::table('college_events')->where('id', $event_id)
                        ->where('user_id', $data['user_id']); // Ensure the user_id is associated
        if (!isset($ce)) {
           $response['status'] = 'fail';
			$response['msg'] = 'Event not deleted';
			return $response;
        }
        $ce->delete();

        $response['status'] = 'success';
		$response['msg'] = 'Event deleted successfully';
		return $response;
	}
}
