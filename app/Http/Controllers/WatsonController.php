<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Session;
use Illuminate\Support\Facades\Cache;
use App\College;


class WatsonController extends Controller
{
	private $chatbot_workspace_id = "c7ee00e4-27f9-4801-9b34-ecaeef1e804f";

	private $workspaceUrl = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces?version=2017-05-26";
	private $messageUrl   = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/{workspace_id}/message?version=2017-05-26";
    
    private function apiCall($url, $input = null, $method = 'GET'){

    	$client = new Client(['base_uri' => 'http://httpbin.org']);

		try {
			if (!isset($input)) {
				$response = $client->request($method, $url,  ['auth' => ['af06a44a-bd99-4515-9cce-0f6bcb2a1247', 'x5JwojC7BHGO']]);
			}else{
				$response = $client->request($method, $url,  [
					'auth' => ['af06a44a-bd99-4515-9cce-0f6bcb2a1247', 'x5JwojC7BHGO'],
					'headers' => ['Content-Type' => 'application/json'],
					'body' => $input
					]);
			}
			
			return json_decode($response->getBody()->getContents(), true);

		} catch (\Exception $e) {
			return "something bad happened";
		}
    }

    public function getWorkspace(){
    	return $this->apiCall($this->workspaceUrl);
    }

    public function sendMessage($msg, $ip){
    	$input = array();

    	$input['input'] = array();
    	$input['input']['text'] = $msg;

    	if (Cache::has(env('ENVIRONMENT') .'_'. $ip. '_'. 'chatbot_context')) {
    		$input['context'] = Cache::get(env('ENVIRONMENT') .'_'. $ip. '_'. 'chatbot_context');

            // if college name exists in cache and we don't have it in context, then add it to context.
            (isset($input['context']['college_name']) && !empty($input['context']['college_name'])) ? 
            Cache::put(env('ENVIRONMENT') .'_'. $ip. '_'. 'chatbot_college_name', $input['context']['college_name'], 1440) : null;
    	}

    	$input = json_encode($input);

    	$url = str_replace("{workspace_id}", $this->chatbot_workspace_id, $this->messageUrl);

    	$response = $this->apiCall($url, $input, 'POST');

        // if college name exists in cache and we don't have it in context, then add it to context.
        ((!isset($response['context']['college_name']) || empty($response['context']['college_name'])) && Cache::has(env('ENVIRONMENT') .'_'. $ip. '_'. 'chatbot_college_name')) ? $response['context']['college_name'] = Cache::get(env('ENVIRONMENT') .'_'. $ip. '_'. 'chatbot_college_name') : null;

    	Cache::put(env('ENVIRONMENT') .'_'. $ip. '_'. 'chatbot_context', $response['context'], 1440);

        $response_msg = $response['output']['text'][0];

    	if (strpos($response_msg, '******') !== false) {
            $command = trim(str_replace('******', '', $response_msg));
           $response['output']['text'][0] =  $this->getPlexussResult($command, $response['context']);
        }
        return $response;
    }

    private function getPlexussResult($command, $context){
        switch ($command) {
            case 'get_college_info':
                $college_name = $context['college_name'];
                $college_tab  = $context['college_tab'];

                $c = new College();

                $msg = $c->getChatBotCollegeStats($college_name, $college_tab);
                break;
            
            case 'show_all_college_ranking':
                $college_name = $context['college_name'];
                $college_tab  = $context['college_tab'];

                $c = new College();

                $msg = $c->getChatBotCollegeOtherRankings($college_name, $college_tab);

                break;

            default:
                # code...
                break;
        }

        return $msg;
    }
}