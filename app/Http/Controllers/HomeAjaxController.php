<?php

namespace App\Http\Controllers;

use Request;
use App\User, App\UserClosedPin, App\NewsArticle;

use Illuminate\Support\Facades\Auth;

class HomeAjaxController extends Controller
{
    /* Part of the tour page pins functionality
	 * Removes saves a user's choice to remove/hide a help pin
	 * from their home page when they click close or the close message
	 * @param		$pin		string		the number of the pin to hide. This corresponds
	 * 										to the numbered column in the database
	 */
	public function closeGettingStartedPin(){
		
		//Redirect to public homepage if user is not logged in.
		if ( !Auth::check() ) {
			return Redirect::to('/signin');
		}

		//receive ajax data
		$pin = Request::get('pin');

		//Get user logged in info.
		$user = User::find( Auth::user()->id ); 

		//Check if user already has a row in the table
		// Huehuehue...
		$clothespins = UserClosedPin::where('user_id', '=', $user->id)->get()->toArray();

		//Create row for user if no rows, set selected pin to 0
		// *note: no need to set other pins to 1, default is 1
		if(empty($clothespins)){
			UserClosedPin::insert(array(
				'user_id' => $user->id,
				'getting_started_' . $pin => '0'
			));
		}
		else{
			//set selected pin to 0 to hide it
			UserClosedPin::where('user_id', '=', $user->id)
				->update(array('getting_started_' . $pin => 0));
		}

	}

	// Infinite Scroll Load More News
	public function loadMoreNews() {
		$skipAmount = Request::get('skipAmount');

		$news = new NewsArticle;
		$newsdata = $news->HomeNewsDetails($skipAmount);

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['newsdata'] = $newsdata;

		return View('private.home.ajax.newsBlock', $data);
	}

}
