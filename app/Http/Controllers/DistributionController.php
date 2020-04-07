<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Request, DB;
use App\DistributionClient, App\DistributionResponse, App\DistributionClientValueMapping, App\User, App\NrccuaResponse;
use App\OrganizationBranch, App\RevenueSchoolsMatching, App\State, App\CappexPossibleMatch, App\TrackingPage, App\NrccuaCronSchedule, App\RevenueUtm, App\TrackingPageId;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Objective, App\AorCollege, App\GenderLookup, App\NrccuaUser, App\Priority, App\TmpNrccua, App\NrccuaQueue, App\Recruitment;
use App\Http\Controllers\CollegeRecommendationController;
use Illuminate\Support\Facades\Cache;
use App\ProfanityList, App\RevenueOrganization, App\TmpNrccuaNew;

class DistributionController extends Controller
	{
    private $qry = null;

	public function generatePostUrl($org_branch_id = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$input = Request::all();
		$user_id = $input['user_id'];
		if(!isset($org_branch_id)){
			$org_branch_id = $data['org_branch_id'];
		}
		
		$dc = DistributionClient::on('rds1')->where('org_branch_id', $org_branch_id)->first();

		if (!isset($dc)) {
			return "This college is not setup yet";
		}

		$arr = $this->getFieldsAndValues($dc->id, $user_id);
		$response = $this->postInquiry($arr, $dc->delivery_type, $dc->delivery_url);

		if ($dc->response_type == 'XML') {
			$response_parse = $this->parseXMLResponse($dc, $response);
		}

		$this->savePostInquiryResponse($dc->delivery_url, json_encode($arr), $response_parse['response'], $user_id, $dc->id, $response_parse);


		return json_encode($response_parse);
		
	}

    public function testKeyPathDistribution() {
        $dc_id_array = [698];
        $college_id = 2;

        $user_id = 1159162;
        // $user_id = 795396;

        $res = $this->postInquiriesWithQueue(11, $college_id, $user_id, 1);

        return $res;

        foreach ($dc_id_array as $dc_id) {
            $fields[] = $this->getFieldsAndValues($dc_id, $user_id);
        }

        dd($fields);
    }

    public function testEducationDynamicsDistribution() {
        $dc_id_array = [215, 216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265];

        $user_id = 1106992;

        $fields = [];

        foreach ($dc_id_array as $dc_id) {
            $fields[] = $this->getFieldsAndValues($dc_id, $user_id);
        }

        dd($fields);

    }

    public function testCappexDistribution() {
        $dc_id_array = [266, 674];

        $user_id = 1106992;

        $crc = new CollegeRecommendationController();

        return $crc->findCollegesForThisUserOnGetStarted($user_id, 8, 2861);

        // foreach ($dc_id_array as $dc_id) {
        //     $fields[] = $this->getFieldsAndValues($dc_id, $user_id);
        // }

        // dd($fields);
    }

    // Test case for getting NRCCUA params
    public function testNRCCUADistribution() {
        $dc_id_array = [206];

        $user_id = 1106992;
        $fields = [];

        foreach ($dc_id_array as $dc_id) {
            $fields[] = $this->getFieldsAndValues($dc_id, $user_id);
        }

        foreach ($fields as $key => $value) {
            $fields[$key] = $this->handleNRCCUAParams($value, $user_id);
        }

        dd($fields);
    }

    // This function was created for getstarted last step for posting new leads.
    public function postDistributionForNRCCUA($college_id, $user_id) {
        $distribution_client = DB::connection('rds1')
                                 ->table('organization_branches as ob')
                                 ->leftJoin('distribution_clients as dc', 'dc.org_branch_id', '=', 'ob.id')
                                 ->where('ob.school_id', '=', $college_id)
                                 ->select('dc.id', 'dc.delivery_url', 'dc.delivery_type')
                                 ->first();

        $dc_id = $distribution_client->id;

        $url = $distribution_client->delivery_url;

        $type = $distribution_client->delivery_type;

        $headers = array();

        // Replace with .env when we get production API key
        $headers['x-api-key'] = 'qeQ06b6xOdw9URQmRqG43Mk0ZapJKiD3CIb4Hnx6';

        $fields_and_values = $this->getFieldsAndValues($dc_id, $user_id);

        $params = $this->handleNRCCUAParams($fields_and_values, $user_id);

        $response = $this->postInquiry($params, $type, $url, $headers);
        

        $tmp = json_decode($response, true);

        $success = 0;
        $error_msg = '';

        if (isset($tmp['httpCode']) && $tmp['httpCode'] == 200) {
            $success = 1;
        }else{
            if (isset($tmp['errors']) && is_array($tmp['errors'])) {
                foreach ($tmp['errors'] as $error) {
                    $error_msg .= ($error['message'] . ', ');
                }
            } else {
                $error_msg = $tmp['errors']['message'];
            }
        }

        $nr = new NrccuaResponse;
        $nr->url = $url;
        $nr->params = json_encode($params);
        $nr->response = $response;
        $nr->user_id  = $user_id;
        $nr->success  = $success;
        $nr->error_msg = $error_msg;
        $nr->success  = $success;
        $nr->error_msg = $error_msg;

        $nr->save();

        return $success === 1 ? 'success' : 'failed';
    }

    // Helper NRCCUA params method to handle some special cases and add majors.
    private function handleNRCCUAParams($array, $user_id) {
        // Static values.
        $array['globalContactOptIn'] = 1;
        $array['utmCampaign'] = 'Plexuss-Inquiry';

        $array['isInterestedInOnlineEducation'] = filter_var($array['isInterestedInOnlineEducation'], FILTER_VALIDATE_BOOLEAN);

        $nrccua_majors = Objective::select('key')
                        ->join('nrccua_majors_mapping as nmm', 'major_id', '=', 'nmm.plexuss_majors_id')
                        ->join('nrccua_majors as nm', 'nmm.nrccua_majors_id', '=', 'nm.id')
                        ->where('user_id', '=', $user_id)
                        ->get();

        $gradFieldOfStudy = [];

        foreach ($nrccua_majors as $nrccua_major) {
            $gradFieldOfStudy[] = $nrccua_major->key;
        }

        $array['gradFieldOfStudy'] = $gradFieldOfStudy;

        return $array;
    }

	private function getFieldsAndValues($dc_id, $user_id, $course_id = null){
		
		$dcfm = DB::connection('rds1')->table('distribution_client_field_mappings as dcfm')
							->leftjoin('distribution_plexuss_field_names as dpfn', 'dpfn.id', '=', 'dcfm.plexuss_field_id')
							->join('distribution_clients as dc', 'dc.id', '=', 'dcfm.dc_id')			
							->where('dcfm.dc_id', $dc_id)
							->select('dpfn.table_name', 'dpfn.raw_field_name','dpfn.field_name as select_field','dcfm.field_type', 'dcfm.client_field_name', 'dcfm.id as dcfmId','dc.id as dcId', 'dpfn.id as dpfn_id', 'dc.ro_id')
							->get();
					
		$ret = array();
		$table_arr = array();
		$select_qry = null;
		$tmp_qry = null;
		$has_ip  = null; 

		foreach ($dcfm as $key) {

			if (!isset($tmp_qry)) {
				if ($key->ro_id == 1 ) {
					$tmp_qry = DB::connection('bk');  
				}else{
					$tmp_qry = DB::connection('mysql');  
				}
				            
        $tmp_qry = $tmp_qry->table('users as u')
                           ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'u.id');
        $table_arr[] = 'users';    
			}
			
			$select_qry .= $key->select_field;

			switch ($key->table_name) {
                case 'recruitment':
                    if (!in_array("recruitment", $table_arr)) {
                        $tmp_qry = $tmp_qry->leftjoin('recruitment as r', 'r.user_id', '=', 'r.id');
                        $table_arr[] = $key->table_name;    
                    }

                    break;

				case 'majors':
					if (!in_array("objectives", $table_arr)) {
						$tmp_qry = $tmp_qry->leftjoin('objectives as o', 'o.user_id', '=', 'u.id');
						$table_arr[] = 'objectives';
					}

					if (!in_array("majors", $table_arr)) {

						$tmp_qry = $tmp_qry->leftjoin('majors as m', 'm.id', '=', 'o.major_id');
						$table_arr[] = $key->table_name;
					}

					break;

				case 'objectives':
					if (!in_array("objectives", $table_arr)) {
						$tmp_qry = $tmp_qry->leftjoin('objectives as o', 'o.user_id', '=', 'u.id');
						$table_arr[] = 'objectives';

					}					
					break;

				case 'scores':
					if (!in_array("scores", $table_arr)) {

						$tmp_qry = $tmp_qry->leftjoin('scores as s', 's.user_id', '=', 'u.id');
						$table_arr[] = 'scores';
					}
					break;

				case 'countries':
					if (!in_array("countries", $table_arr)) {

						$tmp_qry = $tmp_qry->leftjoin('countries as c', 'c.id', '=', 'u.country_id');
						$table_arr[] = 'countries';
					}
					break;

                case 'users_custom_questions':
                    if (!in_array("users_custom_questions", $table_arr)) {

                        $tmp_qry = $tmp_qry->leftjoin('users_custom_questions as ucq', 'ucq.user_id', '=', 'u.id');
                        $table_arr[] = 'users_custom_questions';
                    }
                    break;

                case 'tracking_pages':
                	if (!in_array("tracking_pages", $table_arr)) {

                        $tmp_qry = $tmp_qry->leftjoin('plexuss_logging.tracking_pages as tp', 'tp.user_id', '=', 'u.id')
                        				   ->take(1);
                        $table_arr[] = 'tracking_pages';
                        $has_ip = true;
                    }
                	break;

                case 'high_schools':
                    if (!in_array("high_schools", $table_arr)) {

                        $tmp_qry = $tmp_qry->leftjoin('high_schools as h', 'u.current_school_id', '=', 'h.id');
                        $table_arr[] = 'high_schools';
                    }
                    break;

                case 'cip_expanded':
                	if (!in_array("objectives", $table_arr)) {
						$tmp_qry = $tmp_qry->leftjoin('objectives as o', 'o.user_id', '=', 'u.id');
						$table_arr[] = 'objectives';
					}

                	if (!in_array("majors", $table_arr)) {

						$tmp_qry = $tmp_qry->leftjoin('majors as m', 'm.id', '=', 'o.major_id');
						$table_arr[] = 'majors';
					}

					if (!in_array("cip_expanded", $table_arr)) {

						$tmp_qry = $tmp_qry->leftjoin('cip_expanded as ce', 'm.cip_code', '=', 'ce.cip_code');
						$table_arr[] = $key->table_name;
					}

                	break;
			}
		}
		
		$select_qry = rtrim($select_qry,',');

		$ret_qry    = $tmp_qry->selectraw($select_qry)
							  ->where('u.id', $user_id)
							  ->get();
		return $this->tmpFunction($dcfm, $ret_qry, $has_ip, $course_id);
	}

	private function tmpFunction($dcfm, $ret_qry, $has_ip, $course_id){
		$ret = array();

		foreach ($dcfm as $key) {
			
			switch ($key->field_type) {
				case 'plexuss_field_value':
					
					foreach ($ret_qry as $k) {
						if ((isset($has_ip) && $has_ip == true) && !isset($k->ip)) {

							$rand = rand(1, 239937261);

							$tp = TrackingPage::on('bk-log')->select('ip')
														  ->where('id', '<', $rand)
														  ->orderBy('id', 'DESC')
														  ->first();
							
							
							if (strpos($tp->ip, ',') !== FALSE){
								$k->ip = substr($tp->ip, 0, strpos($tp->ip, ","));
							}

						}elseif (isset($k->ip)) {
							if (strpos($k->ip, ',') !== FALSE){
								$k->ip = substr($k->ip, 0, strpos($k->ip, ","));
							}
						}
						$select_field = $key->raw_field_name;
						$ret[$key->client_field_name] = $k->$select_field;
		
					}
					if (!isset($ret[$key->client_field_name]) || $ret[$key->client_field_name] == '') {
						
						$dcvm = DistributionClientValueMapping::on('rds1')->where('dc_id', $key->dcId)
																	  ->where('dcfm_id', $key->dcfmId)
																	  ->first();
						if (isset($dcvm) && $dcvm->is_default == 1) {
						  	$ret[$key->client_field_name] = $dcvm->client_value;
						}											  
					
					}
					break;
				
				case 'static':
					$dcvm = DistributionClientValueMapping::on('rds1')->where('dc_id', $key->dcId)
																	  ->where('dcfm_id', $key->dcfmId)
																	  ->first();
					if (isset($dcvm)) {
						$ret[$key->client_field_name] = $dcvm->client_value;
					}else{
						$ret[$key->client_field_name] = '';
					}
					break;

				case 'dropdown':
					$plexuss_value = null;
					$check = false;

					$key_name = $key->raw_field_name;
					foreach ($ret_qry as $k) {

						if ($check) {
							break;
						}

						foreach ($k as $innerK => $innerV) {

							if ($key_name == $innerK ) {
								$plexuss_value = $innerV ;
								break;
							}
						}
						if (isset($plexuss_value)) {
							$dcvm = DistributionClientValueMapping::on('rds1')->where('dc_id', $key->dcId)
																		  ->where('dcfm_id', $key->dcfmId)
																		  ->where('plexuss_value', $plexuss_value)
																		  ->first();
							if (isset($dcvm)) {
								$ret[$key->client_field_name] = $dcvm->client_value;
								$check = true;
							}
						}

						$plexuss_value = null;
						
					}
					
					
					if ($key->dpfn_id == 5 && isset($course_id)) {
						$dcvm = DistributionClientValueMapping::on('rds1')->where('dc_id', $key->dcId)
																	  ->where('dcfm_id', $key->dcfmId)
																	  ->where('is_default', 1)
																	  ->first();

						if (isset($dcvm)) {
							$ret[$key->client_field_name] = $course_id; //$dcvm->client_value;
							$check = true;
						}
					}

					if (!$check) {
						$dcvm = DistributionClientValueMapping::on('rds1')->where('dc_id', $key->dcId)
																	  ->where('dcfm_id', $key->dcfmId)
																	  ->where('is_default', 1)
																	  ->first();

						if (isset($dcvm)) {
							$ret[$key->client_field_name] = $dcvm->client_value;
							$check = true;
						}
					}

					break;

				case 'range':
					
					$plexuss_value = null;
					$check = false;

					$key_name = $key->raw_field_name;
					foreach ($ret_qry as $k) {

						foreach ($k as $innerK => $innerV) {

							if ($key_name == $innerK ) {
								$plexuss_value = $innerV ;
								break;
							}
						}						
					}

					if (isset($plexuss_value) && !empty($plexuss_value)) {
						$dcvm = DistributionClientValueMapping::on('rds1')->where('dc_id', $key->dcId)
																		  ->where('dcfm_id', $key->dcfmId)
																		  ->get();

						foreach ($dcvm as $k) {
							$val_arr = explode("-", $k->plexuss_value);

							if ($plexuss_value >= $val_arr[0] && $plexuss_value <= $val_arr[1]) {
								$ret[$key->client_field_name] = $k->client_value;
								break;
							}
						}
					}

					break;
			}
		}
		return $ret;
	}

	private function postInquiry($params, $postMethod, $url, $headers = null){
		
		$client = new Client(['base_uri' => 'http://httpbin.org']);
		
		try {
			if (isset($headers) && !empty($headers)) {
				if ($headers == "application/x-www-form-urlencoded") {
					$response = $client->request($postMethod, $url, [
					    'form_params' => $params
					]);
				}else{
					$response = $client->request($postMethod, $url, [
			        'json' => $params,
			    	'headers' => $headers]);
				}
			}else{
				$response = $client->request($postMethod, $url, [
		        'query' => $params]);
			}
			
			$ret = $response->getBody()->getContents();
		} catch (\Exception $e) {
			$ret = $e->getResponse()->getBody()->getContents();
		}
		return $ret;
	}

	private function savePostInquiryResponse($url, $params, $response, $user_id, $dc_id, $response_parse, $manual = null, $ro_id = null){

		$dr = new DistributionResponse;

		$dr->url       = $url;
		$dr->params    = $params;
		$dr->response  = $response;
		$dr->user_id   = $user_id;
		$dr->dc_id 	   = $dc_id;
		$dr->success   = $response_parse['success'];
		$dr->error_msg = $response_parse['error_msg'];
		isset($manual) ? $dr->manual = $manual : null;
		isset($ro_id)  ? $dr->ro_id = $ro_id : null;
		
		$dr->save();

        return $dr;
	}

	private function parseXMLResponse($dc, $response){

		$response = trim(preg_replace('/\s\s+/', ' ', $response));
		$response = trim(preg_replace('/\s+/', ' ', $response));
		$response = substr($response, 0, strpos($response, "-->"));
		$response = str_replace("<!--", "", $response);
		
		$success_tag = $dc->success_tag;
		$failed_tag  = $dc->failed_tag;

		$ret = array();

		$ret['response']  = $response;
		$ret['success']   = 0;
		$ret['error_msg'] = null;

		$xml=simplexml_load_string($response) or die("Error: Cannot create object");
		
		if ($xml->$success_tag == $dc->success_string) {
			$ret['success'] = 1;
		}else{
			$str = $xml->xpath($failed_tag);
			$str = (string)$str[0];
			$ret['error_msg'] = $str;
		}
		
		return $ret;
	}

	private function parseJSONResponse($dc, $response){
		
		$success_tag = $dc->success_tag;
		$failed_tag  = $dc->failed_tag;

		$ret = array();

		$ret['response']  = $response;
		$ret['success']   = 0;
		$ret['error_msg'] = null;

		$json = json_decode(trim($response));
		if (isset($json->$success_tag) && $json->$success_tag == $dc->success_string) {
			$ret['success'] = 1;
		}else{
			isset($json->$failed_tag) ? $str = $json->$failed_tag : $str = $json;
			$ret['error_msg'] = json_encode($str);
		}
		
		return $ret;
	}

	private function parseTEXTResponse($dc, $response){
		
		$success_tag = $dc->success_tag;
		$failed_tag  = explode("BETWEEN", $dc->failed_tag);

		$ret = array();

		$ret['response']  = $response;
		$ret['success']   = 0;
		$ret['error_msg'] = null;

		if (strpos($response, $dc->success_string) !== false) {
			$ret['success'] = 1;
		}else{
			$msg = $this->getStringBetween($response, $failed_tag[0], $failed_tag[1]);
			$ret['error_msg'] = $msg;
		}
		
		return $ret;
	}

	public function isEligible($user_id, $org_branch_id = null, $ro_id = null, $college_id = null,$course_id=null){
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$dc = DistributionClient::on('rds1')->orderBy('id');
		if(!isset($org_branch_id) && !isset($ro_id)){
			$org_branch_id = $data['org_branch_id'];
		}
		if (isset($org_branch_id)) {
			$dc = $dc->where('org_branch_id', $org_branch_id);
		}
		if (isset($ro_id)) {
			$dc = $dc->where('ro_id', $ro_id);
		}
		if (isset($college_id)) {
			$dc = $dc->where('college_id', $college_id);
		}
		
		$dc = $dc->first();

		if (!isset($dc)) {
			$ret['status'] = 'failed';
			$ret['errors'] = "This college is not setup yet";

			return json_encode($ret);
		}

		$fields_values   = $this->getFieldsAndValues($dc->id, $user_id,$course_id);

		$required_fields = DB::connection('rds1')->table('distribution_client_field_mappings')
							 ->where('is_required', 1)
							 ->where('dc_id', $dc->id)
							 ->pluck('client_field_name');
		
		$error_arr = array();
		
		$error_arr['field_name'] = array();
		$error_arr['possible_fields'] = array();

		foreach ($required_fields as $key => $value) {
			if (!isset($fields_values[$value])) {
				$error_arr['field_name'][] = $value;
				$error_arr['possible_fields'][] = $this->getPossibleFields($dc->id, $value);

			}elseif($fields_values[$value] == null){
				$error_arr['field_name'][] = $value;
				$error_arr['possible_fields'][] = $this->getPossibleFields($dc->id, $value);
			}
		}
		$ret = array();
		if (isset($error_arr['field_name']) && empty($error_arr['field_name'])) {
			$ret['status'] = "success";
		}else{
			$ret['status'] = 'failed';
			$ret['errors'] = $error_arr;
		}
		return json_encode($ret);
	}

	private function getPossibleFields($dc_id, $field_name){

		$qry = DB::connection('rds1')->table('distribution_client_field_mappings as dcfm')
									 ->join('distribution_client_value_mappings as dcvm', 'dcfm.id', '=', 'dcvm.dcfm_id')
									 ->join('distribution_plexuss_field_names as dpfn', 'dpfn.id', '=', 'dcfm.plexuss_field_id')
									 ->where('dcfm.dc_id', $dc_id)
									 ->where('dcfm.client_field_name', $field_name)
									 ->select('dpfn.raw_field_name', 'dcvm.plexuss_value', 'dcvm.id as dcvm_id')
									 ->get();
		$arr = array();
		$raw_field_name = '';
		$ret = array();

		foreach ($qry as $key) {
			$raw_field_name = $key->raw_field_name;
			$arr[] = $key->dcvm_id;
			$ret[] = $key->plexuss_value;
		}

		switch ($raw_field_name) {
			case 'degreeMajor':
				
				$qry = DB::connection('rds1')->table('distribution_client_value_mappings as dcvm')
									 ->join('degree_type as dt', DB::raw("substring_index(plexuss_value , ',' , 1)"), '=', 'dt.id')
									 ->join('majors as m', DB::raw("substring_index(plexuss_value , ',' , - 1)"), '=', 'm.id')
									 ->whereIn('dcvm.id', $arr)
									 ->selectRaw('CONCAT(dt.display_name, " ", m.name) as degreeMajor')
									 ->get();
				$ret = array();

				foreach ($qry as $key) {
					$ret[] = $key->degreeMajor;
				}

				break;
			
			default:
				# code...
				break;
		}

		return $ret;	
	}

	public function sendCustomNRCCUA(){

		$qry = DB::connection('bk')->table('nrccua_users as nu')
								   ->join('nrccua_recruitment as nr', 'nu.user_id', '=', 'nr.user_id')
								   // ->join('nrccua_colleges as nc', 'nc.user_id', '=', 'nu.user_id')
								   ->leftjoin('nrccua_responses as ns', 'nu.user_id', '=', 'ns.user_id')
								   ->whereNull('ns.id')
								   ->select('nu.*', 'nr.ipeds_id')
								   ->take(10)
								   ->get();
		$headers = array();
		$headers['x-api-key'] = 'qeQ06b6xOdw9URQmRqG43Mk0ZapJKiD3CIb4Hnx6';

		$url = 'https://api.test-partner.nrccua.org/v1/lead';

		foreach ($qry as $key) {
			$year = Carbon::now()->year;
			
			if (isset($key->grad_year)) {
				$grad_year = $key->grad_year;
			}else{
				$tmp_birth_date = substr($key->birth_date, 0, 4);
				$age = ($year - $tmp_birth_date) - 18;

				$grad_year = $year - $age;
				if($grad_year < 2018){
					$grad_year = 2018;
				}
			}
			
			$user = User::on('rds1')->find($key->user_id);

			$params = array();
			$params['globalContactOptIn'] = 1;
			$params['email'] = $key->email;
			$params['firstName'] = $key->fname;
			$params['lastName'] = $key->lname;
			$params['birthday'] = $key->birth_date;
			$params['cellPhone'] = $user->phone;
			$params['landPhone'] = '';
			$params['addressLine1'] = $key->address;
			$params['addressLine2'] = '';
			$params['city'] = $key->city;
			$params['state'] = $key->state;
			$params['zip'] = $key->zip;
			$params['country'] = 'US';
			$params['ethnicity'] = '';
			$params['gender'] = 'male';
			$params['parentEmail'] = '';
			$params['counselorEmail'] = '';
			$params['imInfo'] = '';
			$params['imService'] = '';
			$params['religion'] = '';
			$params['citizenship'] = '';
			$params['heritage'] = '';
			$params['fluentLanguages'] = '';
			$params['interests'] = '';
			$params['militaryAffiliations'] = '';
			$params['sexualOrientation'] = '';
			$params['userType'] = 'highschool-student';
			$params['educationPreference'] = '';
			$params['currentDegreeType'] = '';
			$params['interestedDegreeTypes'] = '';
			$params['interestedDegreeTerms'] = '';
			$params['interestedSchoolPublicOrPrivate'] = '';
			$params['gradFieldOfStudy'] = '';
			$params['attendedHighSchoolCeebCodes'] = '';
			$params['highSchoolGradYear'] = $grad_year;
			$params['highSchoolGpa'] = $key->gpa;
			$params['currentSchoolIpedId'] = $key->ipeds_id;
			$params['collegeGradYear'] = '';
			$params['collegeGpa'] = '';
			$params['transferStatus'] = '';
			$params['intendedStartTerm'] = '';
			$params['interestedSchoolIpedId'] = $key->ipeds_id;
			$params['interestedCheggSchoolId'] = '';
			$params['partnerInquiryCaptureDate'] = '';
			$params['currentMajorCipCodes'] = '';
			$params['interestedCipCodes'] = '';
			$params['satMath'] = '';
			$params['satEnglish'] = '';
			$params['satWriting'] = '';
			$params['satReadingWriting'] = '';
			$params['actScore'] = '';
			$params['toeflScore'] = '';
			$params['ieltsScore'] = '';
			$params['programOfInterest'] = '';
			$params['highestLevelOfEducationCompleted'] = '';
			$params['isInterestedInOnlineEducation'] = '';
			$params['additionalInfo'] = '';
			$params['tcpaOptIn'] = '';
			$params['tcpaVerbiage'] = '';
			$params['externalLeadId'] = '';
			$params['utmSource'] = '';
			$params['utmCampaign'] = 'Plexuss-Inquiry';
			$params['utmMedium'] = '';
			$params['utmContent'] = '';
			$params['utmTerm'] = '';

			// dd(939812371);
			$response =  $this->postInquiry($params, 'POST', $url, $headers);

			$tmp = json_decode($response);

			$success = 0;
			$error_msg = '';
			if ($tmp['httpCode'] == 200) {
				$success = 1;
			}else{
				$error_msg = $tmp['errors'];
			}

			$nr = new NrccuaResponse;
			$nr->url = $url;
			$nr->params = $params;
			$nr->response = $response;
			$nr->user_id  = $key->user_id;
			$nr->success  = $success;
			$nr->error_msg = $error_msg;

			$nr->save();
		}
	}

	public function sendNCSAinquiries(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData(true);

		$ro_id = 38;
		$dc_id = 776;
		
		$input   = Request::all();

		$dc 	 = DistributionClient::on('rds1')->find($dc_id);
		$url     = $dc->delivery_url;

		$params  = array();
		$headers = array();

		$params['recruit'] = $input;
		
		$response 		=  $this->postInquiry($params, 'POST', $url, $headers);
		$response_parse = $this->parseJSONResponse($dc, $response);

		$params = json_encode($params);

		$this->savePostInquiryResponse($url, $params, $response, $data['user_id'], $dc_id, $response_parse, null, $ro_id);

		return "success";
	}

	public function generateLinkoutUrl($dc_id, $user_id){

		$arr = array();
		$arr = $this->getFieldsAndValues($dc_id, $user_id);
	
		$dc = DistributionClient::find($dc_id);
		$str = $dc->delivery_url."?";

		foreach ($arr as $key => $value) {
			$str .= $key."=".$value."&";
		}

		$str = substr($str, 0, -1);
		
		return $str;
	}

	public function postInquiriesWithQueue($ro_id, $college_id, $user_id, $manual = null, $course_id = null, 
										   $utmSource = null, $partnerInquiryCaptureDate = null, $addtl_fields = null){
		
		$dc = DistributionClient::on('rds1')->where('ro_id', $ro_id);

		if (isset($college_id)) {
			$dc = $dc->where('college_id', $college_id);
		}
		$dc = $dc->first();
		if (!isset($dc)) {
			return "This college is not setup yet";
		}

		$headers = null;
		if (isset($dc->headers)) {
			$headers = json_decode($dc->headers);
			$headers = (array)$headers;	
			if (empty($headers)) {
				$headers = $dc->headers;
			}
		}

		$arr = $this->getFieldsAndValues($dc->id, $user_id, $course_id);
		
		// This is only for NRCCUA
		if ($ro_id == 1) {
			$ro = RevenueOrganization::on('rds1')->find($ro_id);
			$cap = new RevenueOrganization;
			$cap = $cap->getRevenueOrganizationCap($ro, $user_id);
			
			if (isset($partnerInquiryCaptureDate)) {
				$tmp = Carbon::parse($partnerInquiryCaptureDate);
				$tmp = $tmp->toDateString();
				$arr['partnerInquiryCaptureDate'] = $tmp;
			}

			$pl = new ProfanityList;
			if ($pl->hasProfanity($user_id) == true || $cap <= 0) {
				$this->savePostInquiryResponse($dc->delivery_url, json_encode($arr), null, $user_id, $dc->id, null, -1, $ro_id);
				return;
			}

			if (isset($utmSource)) {
				$arr['utmSource']  = $utmSource;
			}else{
				$arr['utmSource']  = $this->getNrccuaUtmSource($user_id, $arr['partnerInquiryCaptureDate']);
			}
		}

		 // College Express state lookup if it doesn't exist
	    if ($dc->id == 764 && (!isset($arr['state_province']) || empty($arr['state_province']))) {
	            $arr['state_province'] = 'N/A';
	    }

		// For keypath Aston and Exeter
		if ($ro_id == 10 && isset($addtl_fields) && ($dc->college_id == 261842 || $dc->college_id == 261841)) {
			
			(isset($addtl_fields['gdpr_email']) && $addtl_fields['gdpr_email'] == 'true') ? $arr['AllowEmail'] = 'Yes' : $arr['AllowEmail'] = 'No'; 

			(isset($addtl_fields['gdpr_phone']) && $addtl_fields['gdpr_phone'] == 'true') ? $arr['AllowPhone'] = 'Yes' : $arr['AllowPhone'] = 'No'; 
		}

		// This is for CollegeXpress Intl
		if ($ro_id ==  27  && $dc->id == 764 && (!isset($arr['enroll_date']) || empty($arr['enroll_date']))) {
			
			$year = Carbon::today()->year;
			if ($year  == 2018) {
				$year = 2019;
			}
			$term = '';
			$rand = rand(1, 100);
			if ($rand >= 1  && $rand <= 43) {
				$term = 'fall ';	
			}elseif ($rand >= 44  && $rand <= 92) {
				$term = 'spring ';	
			}else{
				$term = 'summer ';	
			}

			$user_year = $this->getPlannedStartYear($user_id);

			if ($user_year < $year) {
				$arr['enroll_date'] = $term.$year;
			}else{
				$arr['enroll_date'] = $term.$user_year;
			}
			
		}		

		// $this->customdd($dc->delivery_url);
		// $this->customdd("==================<br/>");
		// $this->customdd($arr);
		// $this->customdd("==================<br/>");
		// $this->customdd(http_build_query($arr));
		// // exit();
		// $this->customdd("==================<br/>");
		// exit();

		// $this->customdd(json_encode($arr));
		// $this->customdd("==================<br/>");
		
		$response = $this->postInquiry($arr, $dc->delivery_type, $dc->delivery_url, $headers);
		//$this->customdd($response);
		// exit();

		if ($dc->response_type == 'XML') {
			$response_parse = $this->parseXMLResponse($dc, $response);
		}elseif ($dc->response_type == 'JSON') {
			$response_parse = $this->parseJSONResponse($dc, $response);
		}elseif ($dc->response_type == 'TEXT') {
			$response_parse = $this->parseTEXTResponse($dc, $response);
		}

		$dr = $this->savePostInquiryResponse($dc->delivery_url, json_encode($arr), $response_parse['response'], $user_id, $dc->id, $response_parse, $manual, $ro_id);
		// $this->customdd($arr);
		// $this->customdd("=================<br>");
		// $this->customdd($response);
		// $this->customdd("=================<br>");
		// $this->customdd($response_parse);
		// $this->customdd("=================<br>");

    	$response_parse['dr'] = $dr;
		return json_encode($response_parse);
		
	}


	// This method is only for collegeXpress Intl to get their start year...
	private function getPlannedStartYear($user_id){

		$qry_str = "Select
						birth_date ,
						planned_start_yr ,
						(case
					when u.birth_date between cast(
						concat(year(current_date) - 20 , '-08-02') as date
					)
					and cast(
						concat(year(current_date) - 19 , '-08-01') as date
					) then
						year(current_date)
					when u.birth_date is null
					or u.birth_date = '0000-00-00' then
						null
					else

					if(
						month(u.birth_date) in(9 , 10 , 11 , 12) ,
						year(u.birth_date) + 19 ,
						year(u.birth_date) + 18
					)
					end) as yr
					from
						users u
					where
						u.planned_start_yr is NULL
					and country_id != 1
					and u.id= ".$user_id;

		$qry = DB::connection('bk')->select($qry_str);

		$yr = NULL;
		foreach ($qry as $key) {

			$yr = $key->yr;
		}

		return $yr;
	}

	public function sendNrccuaQueue(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_sendNrccuaQueue')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_sendNrccuaQueue');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_sendNrccuaQueue', 'in_progress', 7);

		$take = 4;
		$qry = DB::connection('rds1')->table('nrccua_queues as nq')
									 ->join('users as u', 'u.id', '=', 'nq.user_id')
									 ->join('distribution_clients as dc', function($q){
									  		$q->on('dc.college_id', '=', 'nq.college_id');
									  		$q->on('dc.active', '=', DB::raw(1));
									  		$q->where('dc.ro_id', '=', DB::raw(1));

									  })
									 ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'u.id')
									 ->whereRaw("not exists(
													select 1
													from
														distribution_responses dr
													join distribution_clients dc on dr.dc_id = dc.id
													join colleges c on c.id = dc.college_id
													where
														dc.ro_id = 1
													and dr.user_id = nq.user_id
													and c.id = nq.college_id
												)")
								     ->leftjoin('tmp_nrccua as nt', 'nq.id', '=', 'nt.nq_id')
								     ->whereRaw("not exists(
													Select
														1
													from
														revenue_eab_students_selected_user_ids ressui
													where
														ressui.id = u.id
												)")
								     ->where(function($q) {
										 	 $q->orWhereNotNull('u.address')
										 	   ->orWhereNotNull('nu.address');
										 })
								     ->where("u.birth_date", "!=", "0000-00-00")
								     ->take($take)
								     ->select('nq.*', 'nt.utmSource', 'u.utm_source')
								     ->orderBy('nq.id')
								     ->get();

		foreach ($qry as $key) {
			// Alert don't run this person
			if ($key->utmSource == "SEO" && $key->manual == 1) {
				continue;
			}
			$this->postInquiriesWithQueue($key->ro_id, $key->college_id, $key->user_id, 0, null, 
										  $key->utmSource, $key->created_at);
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_sendNrccuaQueue', 'done', 7);
	}

	public function sendManualNRCCUA(){

		$start_time = "09:00:00";
		$end_time   = "21:00:00";
		$time_now = Carbon::now()->toTimeString();

		if (isset($start_time) && isset($end_time)) {

			$can_i_run = false;
			if ($time_now >= $start_time && $time_now <= $end_time) {
				$can_i_run = true;
			}

			if ($can_i_run == false) {
				return "Can't run this at this time";
			}
		}

		$qry = TmpNrccuaNew::where('sent', 0)
						   // ->orderBy('id', 'DESC')
						   ->take(10)
						   ->get();


		foreach ($qry as $key) {
			
			$user = User::find($key->user_id);
			if (!isset($user)) {
				$key->sent = -2;
				$key->save();
				continue;
			}

			// dd($key->user_id);
			$check = DistributionResponse::on('rds1')
		                                ->where('user_id', $key->user_id)
										->where('dc_id', $key->dc_id)
										->where('ro_id', $key->ro_id)
										->where('success', 1)
										->first();

			$dc = DistributionClient::on('bk')->where('id', $key->dc_id)->first();

			if(!isset($check)){
				$is_eligible = json_decode($this->isEligible($key->user_id, null, $key->ro_id, $dc->college_id));
				
				if ($is_eligible->status == "success") {
					$dt = Carbon::parse($key->inquiry_date);
					$dt = $dt->toDateString();

					$utm_source = $this->getNrccuaUtmSource($key->user_id, $dt);

					$this->postInquiriesWithQueue($key->ro_id, $dc->college_id, $key->user_id, 0, null, 
										  $utm_source, $key->inquiry_date);

					$key->sent = 1;
					$key->save();
				}else{
					$key->sent = -3;
					$key->save();
				}
			}else{
				$key->sent = -1;
				$key->save();
			}
		}

		return  "success";
	}

	public function addToNrccuaQueue(){

		$qry = DB::connection('rds1')->select("Select r.user_id, r.college_id, r.created_at
												from recruitment r 
												join users u on r.user_id = u.id
												join distribution_clients dc on r.college_id = dc.college_id
												join scores s on r.user_id = s.user_id

													and dc.ro_id = 1 and dc.active = 1
												left join distribution_responses as dr on r.user_id = dr.user_id
												and dr.ro_id = 1 and dr.dc_id = dc.id
												where user_recruit = 1
													and dr.id is null
													and u.country_id = 1
													and not exists (select user_id from country_conflicts where user_id = u.id)
													and is_ldy = 0
													and u.country_id = 1
													and u.address is not null
													and length(u.address) >= 3
													and zip is not null
													and length(city) >= 2
													and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
													and u.id not in (Select user_id from country_conflicts)
													and year(birth_date) >= year(current_date()) - 18
													and gender in ('m', 'f')
													and coalesce(hs_gpa, overall_gpa, weighted_gpa) is not null
													and email not like '%test%'
													and fname not like '%test'
													and email not like '%nrccua%'
												and not exists (
														select 1
														from
															distribution_responses dr
														join distribution_clients dc on dr.dc_id = dc.id
														join colleges c on c.id = dc.college_id
														where
															dc.ro_id = 1
														and dr.user_id = r.user_id
														and c.id = r.college_id
												)
												and not exists (
														select 1
														from revenue_schools_matching rsm
														where is_uploaded = 1
														and rsm.user_id = r.user_id
														and rsm.college_id = r.college_id
														and is_uploaded = 1
												)
												and date(r.created_at) >= '2018-08-01'");
		
		
		foreach ($qry as $key) {
			$check = NrccuaQueue::on('rds1')->where('user_id', $key->user_id)
											->where('college_id', $key->college_id)
											->where('ro_id', 1)
											->first();

			if(!isset($check)){
				$arr = array('user_id' => $key->user_id, 'college_id' => $key->college_id, 
							 'created_at' => $key->created_at, 'updated_at' => $key->created_at, 'ro_id' => 1);

				$tmp = NrccuaQueue::updateOrCreate($arr, $arr);
			}
		}
	}

	public function tmpNrccuaAddUserId(){
		$qry = TmpNrccua::whereNull('user_id')
						// ->take(10)
						->orderBy(DB::raw("RAND()"))
						->get();

		foreach ($qry as $key) {
			if (isset($key->nq_id)) {
				$uid = NrccuaQueue::find($key->nq_id);
				$uid = $uid->user_id;
			}elseif (isset($key->rec_id)) {
				$uid = Recruitment::find($key->rec_id);
				$uid = $uid->user_id;
			}

			$key->user_id = $uid;
			$key->save();
		}
	}

	public function tmpNrccuaFixInquiryDate(){
		$qry = TmpNrccua::get();

		foreach ($qry as $key) {
			if (isset($key->nq_id)) { 
				$inquiry_date = NrccuaQueue::where('id', $key->nq_id)
								  ->select(DB::raw("Date(created_at) as dt"))
								  ->first();
				$inquiry_date = $inquiry_date->dt;
			}elseif (isset($key->rec_id)) {
				$inquiry_date = Recruitment::where('id', $key->rec_id)
								  ->select(DB::raw("Date(created_at) as dt"))
								  ->first();
				$inquiry_date = $inquiry_date->dt;
			}

			$key->partnerInquiryCaptureDate = $inquiry_date;
			$key->save();
		}
	}

	public function getNrccuaUtmSource($user_id, $partnerInquiryCaptureDate){
		$dt = Carbon::parse($partnerInquiryCaptureDate);
		$dt = $dt->addDay(1);
		$dt = $dt->toDateString();

		$tpid = TrackingPageId::on('bk-log')->where('date', $partnerInquiryCaptureDate)->first();
		$start_date = $tpid->tp_id;

		$tpid2 = TrackingPageId::on('bk-log')->where('date', $dt)->first();
		if (isset($tpid2)) {
			$end_date = $tpid2->tp_id;
		}else{
			$end_date = TrackingPage::on('bk-log')->orderBy('id', 'desc')->first();
			$end_date = $end_date->id;
		}	


		$sub_qry = null;
		$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
												 ->where('id', '<=', $end_date)
												 ->where('user_id', $user_id)
												 ->where('url', 'LIKE', "%next-step%")
												 ->first();
		
		if (isset($sub_qry)) {
			return "email";
		}
		
		$sub_qry = null;
		$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
												 ->where('id', '<=', $end_date)
												 ->where('user_id', $user_id)
												 ->where('url', 'LIKE', "%get_started%")
												 ->first();

		if (isset($sub_qry)) {
			return "get_started";
		}

		$sub_qry = null;
		$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
												 ->where('id', '<=', $end_date)
												 ->where('user_id', $user_id)
												 ->where('url', 'LIKE', "%saveCollegeApplication%")
												 ->first();
		
		if (isset($sub_qry)) {
			return "college_app_page";
		}

		$sub_qry = null;
		$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
												 ->where('id', '<=', $end_date)
												 ->where('user_id', $user_id)
												 ->where(function($q){
												 		$q->orWhere('url', 'LIKE', "%recruitme%")
												 		  ->orWhere('url', 'LIKE', "%portal%");
												 })
												 ->first();

		if (isset($sub_qry)) {
			return "portal/get_recruited";
		}
		

		return 'portal_list';
	}

	public function tmpFindNrccuaSource(){
		$qry = TmpNrccua::whereNotNull('user_id')->whereNull("utmSource")->get();

		foreach ($qry as $key) {
			$dt = Carbon::parse($key->partnerInquiryCaptureDate);
			$dt = $dt->addDay(1);
			$dt = $dt->toDateString();

			$tpid = TrackingPageId::on('bk-log')->where('date', $key->partnerInquiryCaptureDate)->first();
			$start_date = $tpid->tp_id;

			$tpid2 = TrackingPageId::on('bk-log')->where('date', $dt)->first();
			if (isset($tpid2)) {
				$end_date = $tpid2->tp_id;
			}else{
				$end_date = 243220858;
			}
			

			$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
												 ->where('id', '<=', $end_date)
												 ->where('user_id', $key->user_id)
												 ->where('url', 'LIKE', "%saveCollegeApplication%")
												 ->first();

			if (isset($sub_qry)) {
				$key->utmSource = "college_app_page";
				$key->save();
			}
		}
	}

	public function manualNrccua(){

		$qry = DB::connection('rds1')->table('nrccua_queues as nq')
									 ->join('distribution_clients as dc', function($q){
									 		$q->on('dc.college_id', '=', 'nq.college_id');
									 		$q->on('dc.ro_id', '=', DB::raw(1));
									 })
									 ->leftjoin('tmp_nrccua as tn', 'tn.nq_id', '=', 'nq.id')
									 ->whereNull('tn.id')
									 ->select('nq.user_id', 'dc.id as dc_id', 'nq.id as nq_id', 'nq.user_id')
									 ->take(5)
									 ->get();

		$arr = array();
		foreach ($qry as $key) {
			$arr = $this->getFieldsAndValues($key->dc_id, $key->user_id);
			$arr['nq_id']   = $key->nq_id;
			
			$val = array();
			$val = $arr;

			$val['user_id'] = $key->user_id;
			TmpNrccua::updateOrCreate($arr, $val);
		}
	}

	public function manualNrccuaRecruitment(){

		$qry = DB::connection('rds1')->table('recruitment as r')
									 ->join('distribution_clients as dc', function($q){
									 		$q->on('dc.college_id', '=', 'r.college_id');
									 		$q->on('dc.ro_id', '=', DB::raw(1));
									 		$q->on('dc.active', '=', DB::raw(1));
									 })
									 ->leftjoin('distribution_responses as dr', function($q){
									 		$q->on('dr.user_id', '=', 'r.user_id');
									 		$q->on('dr.dc_id', '=', 'dc.id');
									 		$q->on('dr.ro_id', '=', DB::raw(1));
									 })
									 ->leftjoin('tmp_nrccua as tn', 'tn.rec_id', '=', 'r.id')
									 ->whereNull('tn.id')

									 ->where('r.type', 'not like', 'auto_recommendation')
									 ->whereNull('dr.id')
									 ->select('r.user_id', 'dc.id as dc_id', 'r.id as rec_id', 'r.user_id')
									 ->where('r.created_at', ">=", "2018-08-10 12:58:32")
									 ->take(5)
									 ->get();

		foreach ($qry as $key) {
			$arr = $this->getFieldsAndValues($key->dc_id, $key->user_id);
			
			$val = array();
			$val = $arr;
			$val['rec_id']  = $key->rec_id;
			$val['user_id'] = $key->user_id;
			TmpNrccua::updateOrCreate($arr, $val);
		}
	}
	// Auto posting for NRCCUA
	public function autoPostingInquiries($this_utm_source = null){

		$today  = Carbon::today()->toDateString();
		$rand   = rand(0, 10);

		if(isset($this_utm_source)){
			// if ($rand < 3) {
			// 	return "autoPostingInquiries can't be ran bcz of rand";
			// }
		}else{
			// if ($rand < 4) {
			// 	return "autoPostingInquiries can't be ran bcz of rand";
			// }
		}
		
		if(isset($this_utm_source)){
			if (Cache::has( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries'.$this_utm_source)) {
    		
	    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries'.$this_utm_source);

	    		if ($cron == 'in_progress') {
	    			return "a cron is already running";
	    		}
	    	}

	    	Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries'.$this_utm_source, 'in_progress', 5);
		}else{
			if (Cache::has( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries')) {
    		
	    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries');

	    		if ($cron == 'in_progress') {
	    			return "a cron is already running";
	    		}
	    	}

	    	Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries', 'in_progress', 5);
		}
		

		$start_of_hour	= Carbon::now()->startOfHour()->toTimeString();

		$day_of_week 	= Carbon::now()->format('l');

		$take_min_max = NrccuaCronSchedule::on('rds1')->where('day', $day_of_week )
													  ->where('hour', $start_of_hour)
													  ->first();

		$take = 2;
		if (isset($take_min_max)) {
			$take = rand($take_min_max->min, $take_min_max->max);
		}

		$qry = DB::connection('rds1')->table('revenue_schools_matching as rsm')
									  ->join('distribution_clients as dc', function($q){
									  		$q->on('dc.college_id', '=', 'rsm.college_id');
									  		$q->on('dc.active', '=', DB::raw(1));
									  		$q->where('dc.ro_id', '=', DB::raw(1));

									  })
									  ->join("users as u", "u.id", "=", "rsm.user_id")
									  ->leftjoin("nrccua_users as nu", "nu.user_id", "=", "u.id")
									  ->leftjoin('users_ip_locations as uil', 'u.id', '=', 'uil.user_id')
									  
									  ->where(function($q){
									  		$q->orWhere('uil.ip_zip', '!=', 64113)
									  		  ->orWhereNull('uil.ip_zip');
									  })

									  ->where('u.email', 'NOT LIKE', '%test%')
									  ->where('u.fname', 'NOT LIKE', '%test%')
									  ->where('u.lname', 'NOT LIKE', '%test%')
 									  ->where('u.utm_source', '!=', 'seo')
 									  ->where('u.utm_source', '!=', DB::raw("''"))
 									  ->whereNotNull('u.utm_source')
 									  ->whereNotNull('u.utm_campaign')
 									  ->whereNotNull('u.utm_medium')
 									  ->whereNotNull('u.utm_content')
 									  ->whereNotNull('rsm.utm_source')
 									  

 									  ->where('u.utm_campaign', '!=', DB::raw("''"))
 									  ->where('u.utm_medium', '!=', DB::raw("''"))
 									  ->where('u.utm_content', '!=', DB::raw("''"))

 									  ->where(DB::raw('length(u.utm_content)'), '>=', 2)
 									  ->where(DB::raw('length(u.utm_campaign)'), '>=', 2)

									  ->where('inquiry_date', '<=', $today)
									  ->where('is_uploaded', 0)
									  ->where('inquiry_date', '>', DB::raw("date_sub(current_date, interval 150 day)"))
									  ->whereRaw("not exists(
											select
												*
											from
												distribution_responses dr
											join distribution_clients dc on dr.dc_id = dc.id
											join colleges c on dc.college_id = c.id
											where
												dc.ro_id = 1
											and dr.user_id = rsm.user_id
											and c.id = rsm.college_id
										)")
									  ->whereRaw("((u.hs_grad_year is not null AND u.hs_grad_year BETWEEN 2018 and 2022 ) OR (nu.grad_year is not null and nu.grad_year  BETWEEN 2018 and 2022))")

									  ->take($take)
									  ->select('rsm.id','rsm.user_id','rsm.college_id','rsm.inquiry_date','rsm.is_appended','u.utm_source','u.utm_medium','u.email','rsm.utm_source as utm_source_post')
									  ->orderBy('inquiry_date', 'ASC');
									  // ->where('is_appended', 1)
		
		if (isset($this_utm_source)) {
			$qry = $qry->where('rsm.utm_source', 'get_started')
					   ->groupBy('rsm.user_id');

		  	$qry = $qry->get();
		  	

		  	if (!isset($qry[0])) {
		  		Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries'.$this_utm_source, 'done', 7);
		  		return "uid was not found!";
		  	}

		  	$uid = array();
		  	foreach ($qry as $key) {
		  		$uid[] = $key->user_id;
		  	}

		  	$qry = $qry = DB::connection('rds1')->table('revenue_schools_matching as rsm')
		  										->join("users as u", "u.id", "=", "rsm.user_id")
		  										->leftjoin("nrccua_users as nu", "nu.user_id", "=", "u.id")

		  										->where('u.email', 'NOT LIKE', '%test%')
											    ->where('u.fname', 'NOT LIKE', '%test%')
											    ->where('u.lname', 'NOT LIKE', '%test%')
		 									    ->where('u.utm_source', '!=', 'seo')
		 									    ->where('u.utm_source', '!=', DB::raw("''"))
		 									    ->whereNotNull('u.utm_source')
		 									    ->whereNotNull('u.utm_campaign')
		 									    ->whereNotNull('u.utm_medium')
		 									    ->whereNotNull('u.utm_content')
		 									    ->whereNotNull('rsm.utm_source')
		 									  

		 									    ->where('u.utm_campaign', '!=', DB::raw("''"))
		 									    ->where('u.utm_medium', '!=', DB::raw("''"))
		 									    ->where('u.utm_content', '!=', DB::raw("''"))

		 									    ->where(DB::raw('length(u.utm_content)'), '>=', 2)
		 									    ->where(DB::raw('length(u.utm_campaign)'), '>=', 2)

											    ->where('inquiry_date', '<=', $today)
											    ->where('is_uploaded', 0)
											    ->where('inquiry_date', '>', DB::raw("date_sub(current_date, interval 150 day)"))
											    ->whereRaw("not exists(
													select
														*
													from
														distribution_responses dr
													join distribution_clients dc on dr.dc_id = dc.id
													join colleges c on dc.college_id = c.id
													where
														dc.ro_id = 1
													and dr.user_id = rsm.user_id
													and c.id = rsm.college_id
												  )")
											    ->whereRaw("((u.hs_grad_year is not null AND u.hs_grad_year BETWEEN 2018 and 2022 ) OR (nu.grad_year is not null and nu.grad_year  BETWEEN 2018 and 2022))")
		  										->select('rsm.id','rsm.user_id','rsm.college_id','rsm.inquiry_date','rsm.is_appended','u.utm_source','u.utm_medium','u.email','rsm.utm_source as utm_source_post')
		  										->whereIn('rsm.user_id', $uid)
		  										->where('rsm.utm_source', 'get_started')
		  										->orderBy('rsm.user_id')
		  										->get();
		}else{
			$qry = $qry->where('rsm.utm_source', '!=', 'get_started')
					   ->get();
		}							  
		
		$response_parse = array();
		
		$cnt = 0;
		foreach ($qry as $key) {
			// Check if the user's utm_source and utm_medium exists
			$str = $key->utm_source . "-" . $key->utm_medium;
			$ru = RevenueUtm::on('rds1')->where(DB::raw('concat(utm_source, "-", utm_medium)'), '=', $str)
										->first();

						
			if (!isset($ru)) {
				$tmp = RevenueSchoolsMatching::find($key->id);
				$tmp->is_uploaded = -1;
				$tmp->save();
				continue;
			}

			// Check for duplicate of this email
			$cfnd = $this->checkForNrccuaDuplicate($key->user_id, $key->email);
			
			if(isset($cfnd)){
				$tmp = RevenueSchoolsMatching::find($key->id);
				$tmp->is_uploaded = -2;
				$tmp->save();
				continue;
			}

			// Check if the college is active or not
			$dc = DistributionClient::on('rds1')->where('ro_id', 1)
											->where('college_id', $key->college_id)
											->where('active', 1)
											->first();

			if (!isset($dc)) {
				continue;
			}

			$headers = null;
			if (isset($dc->headers)) {
				$headers = json_decode($dc->headers);
				$headers = (array)$headers;	
			}

			$arr = $this->getFieldsAndValues($dc->id, $key->user_id);
			$arr['partnerInquiryCaptureDate'] = $key->inquiry_date;
			$arr['utmSource'] 				  = $key->utm_source_post;
			// $arr['utmSource'] 				  = $this->getNrccuaUtmSource($key->user_id, $key->inquiry_date);

			// Already sent to Nrccua if not make the cellphone null.
			$already_sent = DistributionResponse::on('rds1')->where('user_id', $key->user_id)
														  ->where('ro_id', 1)
														  ->select('id')
														  ->first();

			if (!isset($already_sent)) {
				$arr['cellPhone']	= null;
			}

			if(strlen( $arr['state'] ) > 2 ){
				$state = State::where('state_name', $arr['state'])->first();
				if (isset($state)) {
					$arr['state'] = $state->state_abbr;
				}else{
					// This is a bad lead.
					$tmp = RevenueSchoolsMatching::find($key->id);
					$tmp->is_uploaded = -3;
					$tmp->save();
					continue;
				}
			}

			$check_appened = $this->canSendAppendedUser($key->user_id, 'nrccua');
			if (!$check_appened) {
				// return "Appended Collges has reached max";
				continue;
			}
			
			// Last check before sending make sure this user has not been sent to this school already
			// Since we send a lot, alot can happen in this for loop. So 
			$check_already_sent = DistributionResponse::on('rds1')->where('user_id', $key->user_id)
														  ->where('ro_id', 1)
														  ->where('dc_id', $dc->id)
														  ->select('id')
														  ->first();
			if (isset($check_already_sent)) {
				continue;
			}

			$response = $this->postInquiry($arr, $dc->delivery_type, $dc->delivery_url, $headers);

			if ($dc->response_type == 'XML') {
				$response_parse = $this->parseXMLResponse($dc, $response);
			}elseif ($dc->response_type == 'JSON') {
				$response_parse = $this->parseJSONResponse($dc, $response);
			}

			$manual = 1;
			if(isset($key->is_appended) && $key->is_appended == 1){
				$manual = 3;
			}
			
			$this->savePostInquiryResponse($dc->delivery_url, json_encode($arr), $response_parse['response'], $key->user_id, $dc->id, $response_parse, $manual, 1);

			$tmp = RevenueSchoolsMatching::find($key->id);
			$tmp->is_uploaded = 1;
			$tmp->save();

			$cnt++;
		}

		if(isset($this_utm_source)){
			Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries'.$this_utm_source, 'done', 7);
		}else{
			Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingInquiries', 'done', 7);
		}
		
		return "Number of inquiries sent: ". $cnt;
	}

	// Set NRCCUA cron schedule
	public function setNrccuaCronSchedule(){

		$time_now = Carbon::now()->toTimeString();
		$check = false;
		if ($time_now >= "00:00:01" && $time_now <= "11:00:00") {
			$check  = true;
		}

		if (!$check) {
			return "not now";
		}

		$day_of_week = Carbon::now()->format('l');
		
		$qry = NrccuaCronSchedule::where('day', $day_of_week)->get();

		$today =  Carbon::today()->toDateString();

		$count = DB::connection('rds1')->select("select
													count(`rsm`.`id`) as cnt
												from
													`revenue_schools_matching` as `rsm`
												inner join `distribution_clients` as `dc` on `dc`.`college_id` = `rsm`.`college_id`
												and `dc`.`active` = 1
												and `dc`.`ro_id` = 1
												join `users` as `u` on `u`.`id` = `rsm`.`user_id`
												left join `nrccua_users` as `nu` on `nu`.`user_id` = `u`.`id`
												where
													`u`.`email` NOT LIKE '%test%'
												and `u`.`fname` NOT LIKE '%test%'
												and `u`.`lname` NOT LIKE '%test%'
												and `u`.utm_source != 'seo' and `u`.utm_source != ''  and u.utm_source is not null
												and `inquiry_date` <='".$today ."'
												and `is_uploaded` = 0
												and `rsm`.`utm_source` is not null
												and `inquiry_date` > date_sub(current_date , interval 150 day)
												and not exists(
													select
														*
													from
														distribution_responses dr
													join distribution_clients dc on dr.dc_id = dc.id
													join colleges c on dc.college_id = c.id
													where
														dc.ro_id = 1
													and dr.user_id = rsm.user_id
													and c.id = rsm.college_id
												)
												and(
													(
														u.hs_grad_year is not null
														AND u.hs_grad_year BETWEEN 2018
														and 2022
													)
													OR(
														nu.grad_year is not null
														and nu.grad_year BETWEEN 2018
														and 2022
													)
												)");

		$count = $count[0]->cnt;


		foreach ($qry as $key) {
			$avg = ceil(($key->probabality * $count ) /20);
			if ($avg <= 25) {
				$min = $avg - 1;
				$max = $avg + 1;

			}elseif ($avg > 25 && $avg < 43) {
				$min = $avg - 2;
				$max = $avg + 2;

			}elseif ($avg > 43) {
				$min = $avg - 2;
				$max = $avg + 2;
			}

			if ($min < 0) {
				$min = 0;
			}
			if ($max == 1) {
				$max = 2;
			}
			
			$key->min = $min;
			$key->max = $max;

			$key->save();
		}
	}

	// check for duplicate on nrccua leads
	public function checkForNrccuaDuplicate($user_id, $email){

		$qry = DB::connection('bk')->table('users as u')
								   ->join('distribution_responses as dr', function($q){
								   		$q->on('dr.user_id', '=', 'u.id');
								   		$q->on('dr.ro_id'  , '=', DB::raw(1));
								   })
								   ->where('u.id', '!=', $user_id)
								   ->where('u.email', $email)
								   ->select('dr.id')
								   ->first();

		if (isset($qry)) {
			$tmp = RevenueSchoolsMatching::where('user_id', $user_id)
										 ->where('is_uploaded', 0)
										 ->update(array('is_uploaded' => -2));
		}

		return $qry;
	}

	// Cappex 
	public function autoPostingCappex($cron_type){

		$march = Carbon::create(2019, 1, 1, 0, 0, 0);
		$today = Carbon::today();
		if ($today->gte($march)) {
			return "Cappex is off";
		}
		
		$time_now = Carbon::now()->toTimeString();
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_autoPostingCappex')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_autoPostingCappex');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingCappex', 'in_progress', 7);

    	$rand = rand(1, 10);

		$take = 20;
		if ($cron_type == "pick_a_college_views") {
		
			$qry = DB::connection('rds1')->table('pick_a_college_views as pacv')
										 ->join('aor_colleges as ac', 'ac.college_id', '=', 'pacv.college_id')
										 ->join('users as u', 'u.id', '=', 'pacv.user_id')
										 ->leftjoin('distribution_clients as dc', function($q){
										 	$q->on('ac.college_id', '=', 'dc.college_id');
										 	$q->on('dc.ro_id', '=', DB::raw(2));
										 })
										 ->leftjoin('distribution_responses as dr', function($q){
										 	$q->on('dr.user_id', '=', 'u.id');
										 	$q->on('dr.ro_id', '=', DB::raw(2));
										 })
										 ->leftjoin('cappex_possible_matches as cpm', 'cpm.user_id', '=', 'pacv.user_id')

										 ->whereNull('dr.id')
										 ->whereNull('cpm.user_id')
										 ->where('dc.ro_id', 2)
										 ->where('u.country_id', 1)
										 ->where('u.in_college', 0)
										 ->where('ac.aor_id', 8)
										 ->where('ac.active', 1)

										 ->where('u.email', 'NOT LIKE', '%test%')
									  	 ->where('u.fname', 'NOT LIKE', '%test%')
									  	 ->where('u.lname', 'NOT LIKE', '%test%')

										 // ->where('u.profile_percent', '>', 15)
										 ->whereNotNull('u.address')
										 // ->whereNotNull('u.phone')
										 // ->where('u.phone', '!=', "")
										 
										 ->groupBy('pacv.user_id')
										 ->select('pacv.*', 'ac.aor_id', 'dc.ro_id')
										 ->take($take);
            if ($rand <=5) {
				$qry = $qry->orderBy('u.id', 'DESC');
			}else{
				$qry = $qry->orderBy(DB::raw("RAND()"));
			}									

			$qry = $qry->get();
		}elseif ($cron_type == "all_users") {
			$take = 100;
			if ($time_now >= "00:00:01" && $time_now <= "05:00:00") {
				// return "not a good time to run this";
				$take = 10;
			}
			$qry = DB::connection('rds1')->table('users as u')
										 ->leftjoin('distribution_responses as dr', function($q){
										 	$q->on('dr.user_id', '=', 'u.id');
										 	$q->on('dr.ro_id', '=', DB::raw(2));
										 })
										 ->leftjoin('cappex_possible_matches as cpm', 'cpm.user_id', '=', 'u.id')
										 ->leftjoin('pick_a_college_views as pacv', 'pacv.user_id', '=', 'u.id')
										 ->leftjoin('country_conflicts as cc', 'cc.user_id', '=', 'u.id')
										 ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'u.id')
					   					 
					   					 ->whereNull('cc.id')
										 ->whereNull('dr.id')
										 ->whereNull('cpm.user_id')
										 ->whereNull('pacv.id')

										 ->where('u.email', 'NOT LIKE', '%test%')
									  	 ->where('u.fname', 'NOT LIKE', '%test%')
									  	 ->where('u.lname', 'NOT LIKE', '%test%')

										 ->where('u.country_id', 1)
										 ->where('u.is_organization', 0)
										 ->where('u.is_ldy', 0)
										 ->where('u.is_organization', 0)
										 ->where('u.is_university_rep', 0)
										 ->where('u.is_counselor', 0)
										 ->where('u.is_aor', 0)
										 // ->where('u.profile_percent', '>', 15)
										 ->where(function($q) {
										 	 $q->orWhereNotNull('u.address')
										 	   ->orWhereNotNull('nu.address');
										 })
										 // ->whereNotNull('u.phone')
										 // ->where('u.phone', '!=', "")

										 ->where('u.utm_source', '!=', 'SEO')

										 ->where('u.in_college', 0)
										 ->groupBy('u.id')
										 ->take($take)

										 ->select('u.id as user_id', DB::raw("'8' as aor_id, '2' as ro_id"));

			if ($rand <=5) {
				$qry = $qry->orderBy('u.id', 'DESC');
			}else{
				$qry = $qry->orderBy(DB::raw("RAND()"));
			}									

			$qry = $qry->get();							 
		}elseif ($cron_type == "in_college_not_set") {
			$take = 50;
			if ($time_now >= "00:00:01" && $time_now <= "05:00:00") {
				// return "not a good time to run this";
				$take = 10;
			}
			$qry = DB::connection('rds1')->table('users as u')
										 ->leftjoin('distribution_responses as dr', function($q){
										 	$q->on('dr.user_id', '=', 'u.id');
										 	$q->on('dr.ro_id', '=', DB::raw(2));
										 })
										 ->leftjoin('cappex_possible_matches as cpm', 'cpm.user_id', '=', 'u.id')
										 ->leftjoin('pick_a_college_views as pacv', 'pacv.user_id', '=', 'u.id')
										 ->leftjoin('country_conflicts as cc', 'cc.user_id', '=', 'u.id')
										 ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'u.id')
					   					 
					   					 ->whereNull('cc.id')
										 ->whereNull('dr.id')
										 ->whereNull('cpm.user_id')
										 ->whereNull('pacv.id')

										 ->where('u.email', 'NOT LIKE', '%test%')
									  	 ->where('u.fname', 'NOT LIKE', '%test%')
									  	 ->where('u.lname', 'NOT LIKE', '%test%')

										 ->where('u.country_id', 1)
										 ->where('u.is_organization', 0)
										 ->where('u.is_ldy', 0)
										 ->where('u.is_organization', 0)
										 ->where('u.is_university_rep', 0)
										 ->where('u.is_counselor', 0)
										 ->where('u.is_aor', 0)
										 // ->where('u.profile_percent', '>', 15)
										 ->where(function($q) {
										 	 $q->orWhereNotNull('u.address')
										 	   ->orWhereNotNull('nu.address');
										 })
										 // ->whereNotNull('u.phone')
										 // ->where('u.phone', '!=', "")

										 ->where('u.utm_source', '!=', 'SEO')

										 ->whereNull('u.in_college')
										 ->groupBy('u.id')
										 ->take($take)

										 ->select('u.id as user_id', DB::raw("'8' as aor_id, '2' as ro_id"));

			if ($rand <=5) {
				$qry = $qry->orderBy('u.id', 'DESC');
			}else{
				$qry = $qry->orderBy(DB::raw("RAND()"));
			}									

			$qry = $qry->get();							 
		}elseif ($cron_type == "in_college_user") {
			$take = 50;
			if ($time_now >= "00:00:01" && $time_now <= "05:00:00") {
				// return "not a good time to run this";
				$take = 10;
			}
			$qry = DB::connection('rds1')->table('users as u')
										 ->leftjoin('distribution_responses as dr', function($q){
										 	$q->on('dr.user_id', '=', 'u.id');
										 	$q->on('dr.ro_id', '=', DB::raw(2));
										 })
										 ->leftjoin('cappex_possible_matches as cpm', 'cpm.user_id', '=', 'u.id')
										 ->leftjoin('pick_a_college_views as pacv', 'pacv.user_id', '=', 'u.id')
										 ->leftjoin('country_conflicts as cc', 'cc.user_id', '=', 'u.id')
										 ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'u.id')
					   					 
					   					 ->whereNull('cc.id')
										 ->whereNull('dr.id')
										 ->whereNull('cpm.user_id')
										 ->whereNull('pacv.id')

										 ->where('u.email', 'NOT LIKE', '%test%')
									  	 ->where('u.fname', 'NOT LIKE', '%test%')
									  	 ->where('u.lname', 'NOT LIKE', '%test%')

										 ->where('u.country_id', 1)
										 ->where('u.is_organization', 0)
										 ->where('u.is_ldy', 0)
										 ->where('u.is_organization', 0)
										 ->where('u.is_university_rep', 0)
										 ->where('u.is_counselor', 0)
										 ->where('u.is_aor', 0)
										 // ->where('u.profile_percent', '>', 15)
										 ->where(function($q) {
										 	 $q->orWhereNotNull('u.address')
										 	   ->orWhereNotNull('nu.address');
										 })
										 // ->whereNotNull('u.phone')
										 // ->where('u.phone', '!=', "")

										 ->where('u.utm_source', '!=', 'SEO')

										 ->where('u.in_college', 1)
										 ->groupBy('u.id')
										 ->take($take)

										 ->select('u.id as user_id', DB::raw("'8' as aor_id, '2' as ro_id"));

			if ($rand <=5) {
				$qry = $qry->orderBy('u.id', 'DESC');
			}else{
				$qry = $qry->orderBy(DB::raw("RAND()"));
			}									

			$qry = $qry->get();							 
		}

		$num_of_users_posted = 0;
		foreach ($qry as $key) {
			
			$crc = new CollegeRecommendationController();
			$matches = $crc->findCollegesForThisUserOnGetStarted($key->user_id, $key->aor_id);

			if (!isset($matches) || empty($matches)) {
				$attr = array('user_id' => $key->user_id, 'college_id' => -1);
				$val  = array('user_id' => $key->user_id, 'college_id' => -1);

				$update = CappexPossibleMatch::updateOrCreate($attr, $val);
				continue;
			}
			// echo $key->user_id. " <br/>";
			// does the user has picked this college
			$user_selected_this_college = DB::connection('rds1')->table('pick_a_college_views as pacv')
									 ->join('aor_colleges as ac', 'ac.college_id', '=', 'pacv.college_id')
									 ->join('revenue_organizations as ro', 'ro.aor_id', '=', 'ac.aor_id')
									 ->where('ro.id', 2)
									 ->where('pacv.user_id', $key->user_id)
									 ->where('ac.active', 1)
									 ->orderBy('pacv.created_at', 'DESC')
									 ->whereIn('pacv.college_id', $matches)
									 ->groupBy('pacv.college_id')
									 ->pluck('pacv.college_id');

			

			if (count($user_selected_this_college) == 0) {
				foreach ($matches as $k => $v) {
					$is_eligible = json_decode($this->isEligible($key->user_id, null, $key->ro_id, $v));
					$error_log = isset($is_eligible->errors) ? json_encode($is_eligible->errors) : null;

					$attr = array('user_id' => $key->user_id, 'college_id' => $v, 'error_log' => $error_log, 'user_selected' => 0);
					$val  = array('user_id' => $key->user_id, 'college_id' => $v, 'error_log' => $error_log, 'user_selected' => 0);

					$update = CappexPossibleMatch::updateOrCreate($attr, $val);
					
				}
				continue;
			}
			// $this->customdd("count ------- <br>");
			// $this->customdd(count($user_selected_this_college));
			// $this->customdd("count ends------- <br>");
			// $this->customdd("key ------- <br>");
			// $this->customdd($key);
			// $this->customdd("key ends------- <br>");
			// $this->customdd("user_selected_this_college ------- <br>");
			// $this->customdd($user_selected_this_college);
			// $this->customdd("user_selected_this_college ends ------- <br>");
			// exit();

			$check = false;
			foreach ($user_selected_this_college as $k => $v) {
				$is_eligible = json_decode($this->isEligible($key->user_id, null, $key->ro_id, $v));
				// $this->customdd($is_eligible);
				// exit();
				if ($is_eligible->status == "success" && !$check) {
					// dd(2222);
					$num_of_users_posted++;
					// Queue::push( new PostInquiriesThroughDistributionClient($key->ro_id, $key->college_id, $key->user_id));

					$check_appened = $this->canSendAppendedUser($key->user_id, 'cappex');
					if (!$check_appened) {
						return "Appended Collges has reached max";
					}
					$this->postInquiriesWithQueue($key->ro_id, $key->college_id, $key->user_id);
					$check = true;
				}else{

					$error_log = isset($is_eligible->errors) ? json_encode($is_eligible->errors) : null;
					$attr = array('user_id' => $key->user_id, 'college_id' => $v, 'error_log' => $error_log, 'user_selected' => 1);
					$val  = array('user_id' => $key->user_id, 'college_id' => $v, 'error_log' => $error_log, 'user_selected' => 1);

					$update = CappexPossibleMatch::updateOrCreate($attr, $val);
				}
				
			}
			
		}
		Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingCappex', 'done', 7);
		return "Number of users that has been posted: ". $num_of_users_posted;
	}

	public function autoPostingCappexUsersWhoSelectedAPickACollege(){
		
		$march = Carbon::create(2019, 1, 1, 0, 0, 0);
		$today = Carbon::today();
		if ($today->gte($march)) {
			return "Cappex is off";
		}

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_autoPostingCappexUsersWhoSelectedAPickACollege')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_autoPostingCappexUsersWhoSelectedAPickACollege');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingCappexUsersWhoSelectedAPickACollege', 'in_progress', 7);

		$ro_id = 2;
		$qry = DB::connection('rds1')->table('cappex_possible_matches as cpm')
									 ->join('users as u', 'u.id', '=', 'cpm.user_id')

									 ->where('u.email', 'NOT LIKE', '%test%')
									 ->where('u.fname', 'NOT LIKE', '%test%')
									 ->where('u.lname', 'NOT LIKE', '%test%')
									 
									 ->join('aor_colleges as ac', function($q){
									 		$q->on('cpm.college_id', '=', 'ac.college_id');
									 		$q->on('ac.active', '=', DB::raw(1));
									 		$q->on('ac.aor_id', '=', DB::raw(8));
									 })
									 ->join('distribution_clients as dc', function($q){
									 		$q->on('dc.college_id', '=', 'ac.college_id');
									 		$q->on('dc.ro_id', '=', DB::raw(2));
									 })
									 ->where('cpm.sent', 0)
									 ->where(function($q){
									 	$q->orWhere('cpm.error_log', '=', "");
									 	$q->orWhereNull('cpm.error_log');
									 })

									 // ->orderBy('cpm.user_id', 'DESC')
									 ->orderBy("dc.price", 'DESC')
									 ->groupBy('cpm.user_id')
									 ->select('cpm.*')
									 ->first();

		if (isset($qry)) {

			$user_qry = CappexPossibleMatch::where('user_id', $qry->user_id)->get();

			foreach ($user_qry as $key) {
				$is_eligible = json_decode($this->isEligible($qry->user_id, null, $ro_id, $qry->college_id));
				// $this->customdd($is_eligible);
				// exit();
				if ($is_eligible->status == "success") {
					// dd(2222);
					// Queue::push( new PostInquiriesThroughDistributionClient($ro_id, $qry->college_id, $qry->user_id));
					$manual = null;
					if ($key->user_selected == 1) {
						$manual = 1;
					}
					$check = $this->canSendAppendedUser($qry->user_id, 'cappex');
					if (!$check) {
						return "Appended Collges has reached max";
					}
					$this->postInquiriesWithQueue($ro_id, $qry->college_id, $qry->user_id, $manual);


					CappexPossibleMatch::where('sent', 0)
							           ->where('user_id', $qry->user_id)
							           ->update(['sent' => 1]);

					Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingCappexUsersWhoSelectedAPickACollege', 'done', 7);
					return "Posted!";
				}else{
					$key->error_log = json_encode($is_eligible->errors);
					$key->sent = -1;
					$key->save();
				}
			}

			Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingCappexUsersWhoSelectedAPickACollege', 'done', 7);
			return "No match found for this user";
			
		}else{
			Cache::put( env('ENVIRONMENT') .'_'. 'is_autoPostingCappexUsersWhoSelectedAPickACollege', 'done', 7);
			return "No more matches";
		}
	}

	/*
	 * autoTurnoffPostingLeadsCappex
	 * This method currently is only for cappex, it turns off schools after last 5 leads are getting rejected.
	 *
	 */
	public function autoTurnoffPostingLeadsCappexCappex(){
		
		$march = Carbon::create(2019, 1, 1, 0, 0, 0);
		$today = Carbon::today();
		if ($today->gte($march)) {
			return "Cappex is off";
		}

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_autoTurnoffPostingLeadsCappex')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_autoTurnoffPostingLeadsCappex');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_autoTurnoffPostingLeadsCappex', 'in_progress', 5);

		$qry = DB::connection('rds1')->table('distribution_responses as dr')
									 ->join('users as u', 'u.id', '=', 'dr.user_id')
									 ->join('distribution_clients as dc', 'dc.id', '=', 'dr.dc_id')
									 ->join('colleges as c', 'c.id', '=', 'dc.college_id')
									 ->join('aor_colleges as ac', function($q){
									 		$q->on('ac.college_id', '=', 'c.id');
									 		$q->on('ac.aor_id', '=', DB::raw(8));
									 		$q->on('ac.active', '=', DB::raw(1));
									 })
									 ->where('dr.ro_id', 2)
									 // ->whereBetween('dr.created_at', array(Carbon::now()->subHours(2), Carbon::tomorrow()))
									 ->whereBetween('dr.created_at', array(Carbon::today(), Carbon::tomorrow()))
									 
									 ->groupBy('dc_id')
									 ->select(DB::raw("count(dr.id) as cnt"), 'dr.dc_id', 'ac.id as ac_id', 'c.id as college_id')
									 ->orderBy('cnt', 'DESC')
									 ->having('cnt', '>=', 5)
									 
									 // ->where('u.utm_source', '!=', 'SEO')

									 ->get();

		$ac_id_arr = array();
		foreach ($qry as $key) {
			$innerQ = DistributionResponse::on('rds1')->where('ro_id', 2)
												      ->where('dc_id', $key->dc_id)
												      ->groupBy('user_id')
												      ->take(5)
												      ->orderBy('id', 'DESC')
												      ->get();

			$cnt = 0;
			foreach ($innerQ as $k) {

				if ($k->error_msg == "Campaign is not accepting leads.") {
					$ac_id_arr['ac_id'] = $key->ac_id;
					$ac_id_arr['dc_id'] = $key->dc_id;
					$ac = AorCollege::find($key->ac_id);
					$ac->active = 0;
					$ac->save();
					break;
				}
				
				if ($k->error_msg == "Unable to accept lead.") {
					$cnt++;
				}else{
					$cnt = 0;
				}
			}
			
			if ($cnt >= 5) {
				$ac_id_arr['ac_id'] = $key->ac_id;
				$ac_id_arr['dc_id'] = $key->dc_id;
				$ac = AorCollege::find($key->ac_id);
				$ac->active = 0;
				$ac->save();


				$priority = Priority::where('ro_id', 2)
									->where('college_id', $key->college_id)
									->where('aor_id', 8)
									->first();

				if (isset($priority)) {
					$priority->active = 0;
					$priority->save();
				}

				$distribution_clients = DistributionClient::find($key->dc_id);

				if (isset($distribution_clients)) {
					$distribution_clients->active = 0;
					$distribution_clients->save();
				}
			}
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_autoTurnoffPostingLeadsCappex', 'done', 5);
		
		return json_encode($ac_id_arr);
	}


	/*
	 * autoTurnoffPostingLeadsNrccua
	 * This method currently is only for NRCCUA, it turns off schools after last 10 leads are getting rejected.
	 *
	 */
	public function autoTurnoffPostingLeadsNrccua(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_autoTurnoffPostingLeadsNrccua')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_autoTurnoffPostingLeadsNrccua');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_autoTurnoffPostingLeadsNrccua', 'in_progress', 5);

		$take = 10;
		$qry = DB::connection('rds1')->table('distribution_responses as dr')
									 ->join('distribution_clients as dc', 'dc.id', '=', 'dr.dc_id')
									 ->join('colleges as c', 'c.id', '=', 'dc.college_id')
									 ->join('aor_colleges as ac', function($q){
									 		$q->on('ac.college_id', '=', 'c.id');
									 		$q->on('ac.aor_id', '=', DB::raw(7));
									 		$q->on('ac.active', '=', DB::raw(1));
									 })
									 ->where('dr.ro_id', 1)
									 // ->whereBetween('dr.created_at', array(Carbon::now()->subHours(2), Carbon::tomorrow()))
									 ->whereBetween('dr.created_at', array(Carbon::today(), Carbon::tomorrow()))
									 ->groupBy('dc_id')
									 ->select(DB::raw("count(dr.id) as cnt"), 'dr.dc_id', 'ac.id as ac_id', 'c.id as college_id', 'ac.aor_id', 'dr.ro_id')
									 ->orderBy('cnt', 'DESC')
									 ->having('cnt', '>=', $take)
									 ->get();

		$ac_id_arr = array();
		foreach ($qry as $key) {
			$innerQ = DistributionResponse::on('rds1')->where('ro_id', $key->ro_id)
												      ->where('dc_id', $key->dc_id)
												      ->groupBy('user_id')
												      ->take($take)
												      ->orderBy('id', 'DESC')
												      ->get();

			$cnt = 0;
			foreach ($innerQ as $k) {

				if ($k->error_msg == "Campaign is not accepting leads.") {
					$ac_id_arr['ac_id'] = $key->ac_id;
					$ac_id_arr['dc_id'] = $key->dc_id;
					$ac = AorCollege::find($key->ac_id);
					$ac->active = 0;
					$ac->save();
					break;
				}
				
				if ($k->success == 1) {
					$cnt = 0;
				}else{
					$cnt++;
				}
			}
			
			if ($cnt >= $take) {

				$ac_id_arr['ac_id'] = $key->ac_id;
				$ac_id_arr['dc_id'] = $key->dc_id;
				$ac = AorCollege::find($key->ac_id);
				$ac->active = 0;
				$ac->save();


				$priority = Priority::where('ro_id', $key->ro_id)
									->where('college_id', $key->college_id)
									->where('aor_id', $key->aor_id)
									->first();

				if (isset($priority)) {
					$priority->active = 0;
					$priority->save();
				}

				$distribution_clients = DistributionClient::find($key->dc_id);

				if (isset($distribution_clients)) {
					$distribution_clients->active = 0;
					$distribution_clients->save();
				}

			}
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_autoTurnoffPostingLeadsNrccua', 'done', 5);
		
		return json_encode($ac_id_arr);
	}

	public function fixCappexMatchesForGenderAndZip(){

		$qry = DB::connection('rds1')->table('cappex_possible_matches as cpm')
									 ->join('users as u', 'u.id', '=', 'cpm.user_id')
									 ->where(function($q){
									 		$q->orWhere('cpm.error_log', 'LIKE', '%"gender"%')
									 		  ->orWhere('cpm.error_log', 'LIKE', '%"zip_code"%');
									 })
									 ->where('sent', 0)
									 ->where('tried_to_fix', 0)
									 ->groupBy('cpm.user_id')
									 ->orderBy('cpm.user_id', 'DESC')
									 ->take(1)
									 ->select('cpm.*')
									 ->orderBy(DB::raw("RAND()"))
									 ->get();

		foreach ($qry as $key) {
			$attr = array('user_id' => $key->user_id );
			$val  = array('user_id' => $key->user_id );

			$user = User::find($key->user_id);
			
			if (strpos($key->error_log, '"gender"') !== FALSE){
				$gl = GenderLookup::on('rds1')->where('fname', $user->fname)->first();
				if (isset($gl)) {
					$val['gender'] = strtolower($gl->gender);	
				}else{
					$update = CappexPossibleMatch::where('user_id', $key->user_id)
												 ->update(array('tried_to_fix' => 1 ));

					return "this name cannot be found: ". $user->fname;
				}
			}


			if (strpos($key->error_log, '"zip_code"') !== FALSE){
				$gl = $this->getZipcode($user->address. ', '. $user->city);
				$gl = json_decode($gl);
				if ($gl->status == "success" && isset($gl->zip)) {
					$val['zip']   = $gl->zip;
					if (isset($gl->state) && $gl->state !="") {
						$val['state'] = $gl->state;
					}	
				}else{
					$update = CappexPossibleMatch::where('user_id', $key->user_id)
												 ->update(array('tried_to_fix' => 1 ));

					return "this address cannot be found: ". $user->address. ', '. $user->city;
				}
			}

			$nrccua_tmp = NrccuaUser::where('user_id', $key->user_id)->first();
			if (isset($nrccua_tmp)) {
				isset($nrccua_tmp->gender) ? $val['gender'] = strtolower($nrccua_tmp->gender) : null;
				isset($nrccua_tmp->state)  ? $val['state']  = $nrccua_tmp->state : null;
				isset($nrccua_tmp->zip)    ? $val['zip']  = $nrccua_tmp->zip : null;
			}
			
			$del_nrccua = NrccuaUser::where('user_id', $key->user_id)->delete();
			$del_rows 	= CappexPossibleMatch::where('user_id', $key->user_id)->delete();
			

			$update = NrccuaUser::updateOrCreate($attr, $val);

		}
		return "updated!";
	}

	/*
	 * sendCollegeXpressLeads
	 * This method posts the collegeXpress Inquiries
	 *
	 */
	public function sendCollegeXpressLeads(){
		$ro_id = 27;
		$take  = 1;

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_sendCollegeXpressLeads')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_sendCollegeXpressLeads');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_sendCollegeXpressLeads', 'in_progress', 4);

		$qry = DB::connection('rds1')->table('users as u')
									 ->leftjoin('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
									 ->leftJoin('distribution_responses as dr', function($q){
									 			$q->on('u.id', '=', 'dr.user_id');
									 			$q->on('dr.ro_id', '=', DB::raw(27));
									 })
									 ->leftjoin('scores as s', 'u.id', '=', 's.user_id')

									 ->whereNotNull(DB::raw("coalesce(IF(u.in_college = 0, hs_grad_year, college_grad_year), nu.grad_year)"))
									 ->whereNotNull(DB::raw("coalesce(s.hs_gpa, s.overall_gpa, if(weighted_gpa is null, null, least(weighted_gpa, 4)), nu.gpa)"))
									 ->whereBetween(DB::raw("coalesce(IF(u.in_college = 0, hs_grad_year, college_grad_year), nu.grad_year)"), [2019, 2021])
									 ->whereNotNull(DB::raw("if(coalesce(u.gender, '') not in ('m', 'f'), nu.gender, u.gender)"))
									 ->whereNull('dr.id')
									 ->whereNotNull('u.birth_date')

									 ->where('u.fname', 'NOT LIKE', 'test%')
									 ->where('u.lname', 'NOT LIKE', 'test%')

									 ->where("u.birth_date", "!=", "0000-00-00")
									 ->where('u.email', '!=', 'none')
									 ->where('u.is_organization', 0)
									 ->where('u.is_university_rep', 0)
									 ->where('u.is_counselor', 0)
									 ->where('u.is_aor', 0)
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', 1)
									 ->where('u.is_plexuss', 0)
									 ->whereRaw("((u.address is not null or nu.address is not null) and (u.address !='' or nu.address != ''))")
									 ->whereRaw("date(u.created_at) < date_sub(current_date, interval 90 day)")

									 ->where('u.utm_source', '!=', 'SEO')

									 ->take($take)
									 ->orderBy(DB::raw("RAND()"))
									 ->select('u.id as user_id')
									 ->distinct()
									 ->get();

		$cnt = 0;
		foreach ($qry as $key) {
			// dd($key);
			$is_eligible = json_decode($this->isEligible($key->user_id, null, $ro_id, null));
			// $this->customdd($is_eligible);
			// exit();
			if ($is_eligible->status == "success") {
				$cnt++;
				$manual = 1;
				$this->postInquiriesWithQueue($ro_id, null, $key->user_id, $manual);
			}
		}
		Cache::put( env('ENVIRONMENT') .'_'. 'is_sendCollegeXpressLeads', 'done', 7);

		return "Number of posts: ". $cnt;
	}

	/*
	 * sendCollegeXpressLeadsIntl
	 * This method posts the collegeXpress Intl Inquiries
	 *
	 */
	public function sendCollegeXpressLeadsIntl(){
		$ro_id = 27;
		$take  = 1;

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_sendCollegeXpressLeadsIntl')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_sendCollegeXpressLeadsIntl');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_sendCollegeXpressLeadsIntl', 'in_progress', 4);

    	$today = Carbon::today();
    	$tomorrow = Carbon::tomorrow();

    	$cnt = DistributionResponse::on('rds1')->where('dc_id', 764)
    										   ->whereBetween('created_at', [$today, $tomorrow])
    										   ->count();

    	if ($cnt > 100) {
    		return "sendCollegeXpressLeadsIntl reached a limit";
    	}


		$qry = DB::connection('rds1')->table('users as u')
									 ->leftjoin('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
									 ->leftJoin('distribution_responses as dr', function($q){
									 			$q->on('u.id', '=', 'dr.user_id');
									 			$q->on('dr.ro_id', '=', DB::raw(27));
									 })
									 ->leftjoin('scores as s', 'u.id', '=', 's.user_id')

									 ->whereNotNull(DB::raw("coalesce(IF(u.in_college = 0, hs_grad_year, college_grad_year), nu.grad_year)"))
									 ->whereNotNull(DB::raw("coalesce(s.hs_gpa, s.overall_gpa, if(weighted_gpa is null, null, least(weighted_gpa, 4)), nu.gpa)"))
									 ->whereBetween(DB::raw("coalesce(IF(u.in_college = 0, hs_grad_year, college_grad_year), nu.grad_year)"), [2019, 2021])
									 ->whereNotNull(DB::raw("if(coalesce(u.gender, '') not in ('m', 'f'), nu.gender, u.gender)"))
									 ->whereNull('dr.id')
									 ->whereNotNull('u.birth_date')

									 ->where('u.fname', 'NOT LIKE', 'test%')
									 ->where('u.lname', 'NOT LIKE', 'test%')

									 ->where("u.birth_date", "!=", "0000-00-00")
									 ->where('u.email', '!=', 'none')
									 ->where('u.is_organization', 0)
									 ->where('u.is_university_rep', 0)
									 ->where('u.is_counselor', 0)
									 ->where('u.is_aor', 0)
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', '!=', 1)

									 ->where('u.is_plexuss', 0)
									 ->whereRaw("((u.address is not null or nu.address is not null) and (u.address !='' or nu.address != ''))")
									 ->whereRaw("date(u.created_at) < date_sub(current_date, interval 90 day)")

									 // ->whereNotNull('u.planned_start_term')
									 // ->whereNotNull('u.planned_start_yr')
									 ->where(function($q){
									 	$q->orWhere('u.planned_start_yr', '>=', 2019)
									 	  ->orWhereNull('u.planned_start_yr');
									 })

									 ->take($take)
									 ->orderBy(DB::raw("RAND()"))
									 ->select('u.id as user_id')
									 ->distinct()
									 ->get();

		$cnt = 0;
		foreach ($qry as $key) {
			
			$is_eligible = json_decode($this->isEligible($key->user_id, null, $ro_id, 2));
			
			// $this->customdd($is_eligible);
			// exit();
			if ($is_eligible->status == "success") {
				$cnt++;
				$manual = 1;
				$this->postInquiriesWithQueue($ro_id, 2, $key->user_id, $manual);
			}
		}
		Cache::put( env('ENVIRONMENT') .'_'. 'is_sendCollegeXpressLeadsIntl', 'done', 4);

		return "Number of posts: ". $cnt;
	}

	/**
	*
	* Function Name: getZipcode()
	* $address => Full address.
	*
	**/
	private function getZipcode($address){
		$ret = array();

	    if(!empty($address)){
	        //Formatted address
	        $formattedAddr = urlencode($address);
	        //Send request and receive json data by address
	        $geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDsqOEp6oEvNMvdjiPfkh5trBjtSLWoWpw&address='.$formattedAddr.'&sensor=true_or_false'); 
	        $output1 = json_decode($geocodeFromAddr);
	        if ($output1->status == "ZERO_RESULTS") {
	        	$ret['status'] = 'failed';
	        	return json_encode($ret);  
	        }
	        //Get latitude and longitute from json data
	        $latitude  = $output1->results[0]->geometry->location->lat; 
	        $longitude = $output1->results[0]->geometry->location->lng;
	        //Send request and receive json data by latitude longitute
	        $geocodeFromLatlon = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDsqOEp6oEvNMvdjiPfkh5trBjtSLWoWpw&latlng='.$latitude.','.$longitude.'&sensor=true_or_false');
	        $output2 = json_decode($geocodeFromLatlon);
	        if(!empty($output2) && $output2->status != "ZERO_RESULTS"){
	            $addressComponents = $output2->results[0]->address_components;
	            foreach($addressComponents as $addrComp){
	                if($addrComp->types[0] == 'postal_code'){
	                    //Return the zipcode
	                    $ret['status'] = 'success';
	                    $ret['zip']    =  $addrComp->long_name;
	                }
	                if($addrComp->types[0] == 'administrative_area_level_1'){
	                    //Return the zipcode
	                    $ret['status'] = 'success';
	                    $ret['state']  =  $addrComp->short_name;
	                }
	            }
	            if (isset($ret['status']) && $ret['status'] == 'success') {
	            	return json_encode($ret);
	            }
	            $ret['status'] = 'failed';
	            return json_encode($ret);
	        }else{
	            $ret['status'] = 'failed';
	            return json_encode($ret);
	        }
	    }else{
	        $ret['status'] = 'failed';
	        return json_encode($ret);   
	    }
	}

	private function canSendAppendedUser($user_id, $company){

		$cap = array();
		$cap['nrccua'] = 900;
		$cap['cappex'] = 500;

		$today = Carbon::today();

		$nu = NrccuaUser::where('user_id', $user_id)
						->where('is_manual', 1)
						->first();

		if (!isset($nu)) {
			return true;
		}

		if (Cache::has(env('ENVIRONMENT') .'_'. 'canSendAppendedUser_'. $today)) {
			$arr = Cache::get(env('ENVIRONMENT') .'_'. 'canSendAppendedUser_'. $today);
		}else{
			$arr = array();
		}

		if (!isset($arr[$company])) {
			$arr[$company] = 1;

		}else{
			if ($cap[$company] < $arr[$company]) {
				return false;
			}
			$arr[$company] += 1;
		}

		Cache::put(env('ENVIRONMENT') .'_'. 'canSendAppendedUser_'. $today, $arr, 2880);

		return true;
	}

	// This method sets the access token for post urls of all GUS post URLs 
	// This is necessary since every hour access token gets expired.

	public function setGusAccessToken(){
		$client = new Client(['base_uri' => 'http://httpbin.org']);
		$postMethod = "GET";
		$url        = "https://gus.cvtr.io/oauth/v2/token?grant_type=http://convertr.cvtr.io/grants/api_key&client_id=3_16qhmrg5cnms84wgowowkcgksc04400occcwswcg88kc4swosc&client_secret=580h2l7ulbswk0g8oowc8wk0okcg8o4s48c8k4ks004gk88cg4&api_key=253882ac505e948c17ab96ea7d40eafe538e";

		try {
			$response = $client->request($postMethod, $url);
			
			$ret = $response->getBody()->getContents();
		} catch (\Exception $e) {
			$ret = $e->getResponse()->getBody()->getContents();
		}
		$ret = json_decode(trim($ret));

		$dc = DistributionClient::where('ro_id', 31)->get();

		foreach ($dc as $key) {
			$delivery_url =  substr($key->delivery_url, 0, strpos($key->delivery_url, "access_token="));

			$key->delivery_url = $delivery_url. "access_token=". $ret->access_token;
			$key->save();
		}


		return "success";
	}	
}