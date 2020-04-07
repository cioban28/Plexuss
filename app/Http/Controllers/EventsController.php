<?php
namespace App\Http\Controllers;

use Request;
use DB;
use App\User, App\PlexussAdmin,App\Country,App\CollegeEvent,App\WorldwideCities;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;



class EventsController extends Controller
{
    /***********
      Loading the main view of the  college and events page

     ************/

    public function index()
    {

        $request = Request::all();
        isset($request['country']) ? $countryname = $request['country'] : $countryname = '';

        if (!empty($countryname))
        {

            $keyword = "%".trim($countryname)."%";

            $result =Country::where('country_name', 'like', $keyword)->get();

            if ($result->isNotEmpty()):
                Session::put('countryname', trim($countryname));
            else:
                Session::put('countryname', 0);
            endif;
        }
        else
        {
                Session::put('countryname', 0);
        }

        //Template base arrays
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['title'] = 'Plexuss Colleges';
        $data['currentPage'] = 'plex-events';

        return View('events.eventView', $data);
    }

    /****************
     return all online events on ajax

    ******************/


    public function getOnlineEvents()
    {

        if (Session::has('countryname')) {
          $countryname = Session::get('countryname');
        }else{
          $request = Request::all();
          if (isset($request['countryname'])) {
            $countryname = $request['countryname'];
          }
        }

        $today = Carbon::today()->format('Y-m-d');

        if (empty($countryname)):
            $getAllEventsReturn = CollegeEvent::on('rds1')
                                   ->whereDate('event_start_date','>=', $today)
                                   ->orderBy('event_start_date', 'asc')
                                   ->get();
        else:
            $getAllEventsReturn = CollegeEvent::on('rds1')
                                  ->whereDate('event_start_date','>=', $today)
                                  ->where('event_country', '=', $countryname)
                                  ->orderBy('event_start_date', 'asc')
                                  ->get();

        endif;

        return $getAllEventsReturn;
    }



    /****************
     return all offline events on ajax

    ******************/


    public function getOfflineEvents()
    {
        if (Session::has('countryname')) {
          $countryname = Session::get('countryname');
        }else{
          $request = Request::all();
          if (isset($request['countryname'])) {
            $countryname = $request['countryname'];
          }
        }

        $today = Carbon::today()->format('Y-m-d');

        if (empty($countryname)):

         $getAllEventsReturn = CollegeEvent::on('rds1')
                               ->whereDate('event_start_date','<', $today)
                               ->orderBy('event_start_date', 'asc')
                               ->get();
        else:

         $getAllEventsReturn = CollegeEvent::on('rds1')
                              ->whereDate('event_start_date','<', $today)
                              ->where('event_country', '=', $countryname)
                              ->orderBy('event_start_date', 'asc')
                              ->get();

        endif;
        return $getAllEventsReturn;
    }


     /****************
     return all nearest event depends upon user location . either from geolocation or if city is filed when registring the form.

    ******************/


    public function getNearestEvents()
    {

        $cityname = '';
        $citycountry = '';
        $today = Carbon::today()->format('Y-m-d');

        if (Auth::user())
        {
            if (!empty(Auth::user()->city) && isset(Auth::user()
                ->city)):

                $cityname = Auth::user()->city;

            endif;

            if (!empty(Auth::user()
                ->country_id) && isset(Auth::user()
                ->country_id)):

                $country = Country::where('id', '=', Auth::user()
                    ->country_id)
                    ->first();

                $citycountry = $country->country_name;

            endif;

        }
        $result = file_get_contents('http://api.ipstack.com/check?access_key=dba1c0447f8792b173d31047e0e0bf93&format=1');

        $nearestdata = json_decode($result);

        if (empty($cityname))
        {

            if (!empty($nearestdata->city)):
                $cityname = "%" . $nearestdata->city . "%";
                $city = $nearestdata->city;
            else:
                $cityname = "%notfound%";
            endif;

        }
        else
        {

            $city = $cityname;
            $cityname = "%" . $cityname . "%";
        }

        $data['getAllEventsReturn'] =  CollegeEvent::on('rds1')
                                      ->where('event_city_full_address','like', $cityname)
                                      ->whereDate('event_start_date', '>=', $today)
                                      ->orderBy('event_start_date', 'asc')
                                      ->get();


        $data['city'] = $city;

        if (empty($citycountry))
        {

            $data['country'] = $nearestdata->country_name;
        }
        else
        {
            $data['country'] = $citycountry;
        }

        return $data;
    }
     /****************
     return all nearest event depends upon user selection .

    ******************/


    public function getnearestCityEvents(Request $request)
    {
		$cityname = "%" . $request->get('cityname') . "%";
        $city = $request->get('cityname');
        $today = Carbon::today()->format('Y-m-d');

        $data['getAllEventsReturn'] =  CollegeEvent::on('rds1')
                                      ->where('event_city_full_address','like', $cityname)
                                      ->whereDate('event_start_date', '>=', $today)
                                      ->orderBy('event_start_date', 'asc')
                                      ->get();


        $data['city'] = $city;
        //$data['country'] = $input['cityCountry'];
		return $data;
    }



    /***************
      return all the countries available in our database in which events are going to happen or already happened . This used as filter for events.


    ***************/



    public function getCountryNames()
    {
        $getAllCountries = CollegeEvent::on('rds1')
                           ->groupBy('event_country')
                          ->orderBy('event_country', 'asc')
                          ->get();

        return $getAllCountries;
    }

    /************ fetching the city names on ajax on search field
            in nearest event tabs
           ***/

    public function getCityName(Request $request){
        $cityname =  $request->get('cityname');

        if(!isset($cityname))
        {

         return response()->json([
            "status"=>"200","code"=>00
           ]);
        }

        $cityresults = WorldwideCities::on('bk')
                                      // ->where('accent_city', function($q) use($cityname){
                                      //     $q->on('accent_city', '=', $cityname);
                                      //     $q->orWhere('accent_city', 'LIKE', '"'.$cityname.'%"');
                                      //     $q->orWhere('accent_city', 'LIKE', '"%'.$cityname.'"');
                                      //     $q->orWhere('accent_city', 'LIKE', '"%'.$cityname.'%"');
                                      // })
                                      ->orWhere('accent_city', '=', DB::raw('"'.$cityname.'"'))
                                      ->orWhere('accent_city', 'LIKE', DB::raw('"'.$cityname.'%"'))
                                      ->orWhere('accent_city', 'LIKE', DB::raw('"%'.$cityname.'"'))
                                      ->orWhere('accent_city', 'LIKE', DB::raw('"%'.$cityname.'%"'))
                                      ->orderByRaw('case
                                                    when city_name = "'.$cityname.'" then 1
                                                    when city_name LIKE "'.$cityname.'%" then 2
                                                    else 3 END')
                                      ->groupBy('accent_city')
                                      ->take(10)
                                      ->get();


        if($cityresults->isNotEmpty()){
          return response()->json([
             "status"=>"200","code"=>1,"result"=>$cityresults
            ]);
        }else{

           return response()->json([
             "status"=>"200","code"=>0
            ]);
        }
    }

}

