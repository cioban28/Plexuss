<?php 

namespace App\Library;

use GuzzleHttp\Client;
use Illuminate\Http\Request;


/****************************************************************
*****************************************************************
*  handful of helper methods used for Amplitude Analytics
*
*****************************************************************/
class Amplitude{
    
    //API KEY
    private $API_KEY = "cff1f9c1d7282ca62ce783557d55b9e7";   // test project "3171c69c32aaed4982b31b95d2c1a770";
                                                             // live (Plexuss project) cff1f9c1d7282ca62ce783557d55b9e7


    /////////////////////////////////
    //  function to create an event to send to Amplitude API 
    //  params: user = user_id
    //          event = event_type 
    //          props = event_properties
    //  
    public function createEventObject($user, $event, $props=null){

        $e = '';

        if(isset($props)){
            $e =  json_encode( (Object)["user_id" => (String)$user, 
                                        "event_type" => (String)$event, 
                                        "event_properties" => $props] );
        }else{
            $e =  json_encode( (Object)["user_id" => (String)$user, 
                                        "event_type" => (String)$event] );
        }
        //urlencode('['.$e.']');
        return  $e;
    }


    ////////////////////////////////
    // function to send event
    public function sendEvent($eventData){

    	$client = new Client();
   		$res2 = $client->request('POST', 'https://api.amplitude.com/httpapi', [
            'form_params' => [
                'api_key' => $this->API_KEY,
                'event' => $eventData
            ]
        ]);

        //if($res->getStatusCode() != 200)
        //log error statuc code
        //log $res->getHeader('content-type');
        //log $res->getBody();
	      
    }
}
 ?>