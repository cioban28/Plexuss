<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function index(){
		$input = Request::all();

		$access_token = "EAACFiHkGhQsBACcK8buyX51EnSV6ZB1tPds6lNWXsH5ZAeIRDoLceq1AZAvqdo0jGsvrYZAdt5Vps12ck5QbtlZBprOgBOet0UwGv3S2knZC64Y1ZC4YOnZAujAbCYC0MS21yKkKMehzQhAXDlZBbk5dCgEAnjeU3SN4b18hBh3JKGQZDZD";
		$verify_token = "fb_time_bot";
		$hub_verify_token = null;
		 
		if(isset($input['hub_challenge'])) {
		    $challenge = $input['hub_challenge'];
		    $hub_verify_token = $input['hub_verify_token'];
		}
		 
		 
		if ($hub_verify_token === $verify_token) {
		    echo $challenge;
		    die();
		}


		$input = json_decode(file_get_contents('php://input'), true);
		 
		$sender = $input['entry'][0]['messaging'][0]['sender']['id'];
		$message = $input['entry'][0]['messaging'][0]['message']['text'];

		$message_to_reply = "Hmm... I think it's Negin's Birthday! You'd better be there :D";
		/**
		 * Some Basic rules to validate incoming messages
		 */
		// if(preg_match('[time|current time|now]', strtolower($message))) {
		//     // Make request to Time API
		//     ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
		//     $result = file_get_contents("http://www.timeapi.org/utc/now?format=%25a%20%25b%20%25d%20%25I:%25M:%25S%20%25Y");
		//     if($result != '') {
		//         $message_to_reply = $result;
		//     }
		// } else {
		//     $message_to_reply = 'Huh! what do you mean?';
		// }
		//API Url
		$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;

		//Initiate cURL.
		$ch = curl_init($url);
		//The JSON data.
		$jsonData = '{
		    "recipient":{
		        "id":"'.$sender.'"
		    },
		    "message":{
		        "text":"'.$message_to_reply.'"
		    }
		}';
		//Encode the array into JSON.
		$jsonDataEncoded = $jsonData;
		//Tell cURL that we want to send a POST request.
		curl_setopt($ch, CURLOPT_POST, 1);
		//Attach our encoded JSON string to the POST fields.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		//Set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		//Execute the request
		if(!empty($input['entry'][0]['messaging'][0]['message'])){
		    $result = curl_exec($ch);
		}

	}
}
