<?php

namespace App\Http\Controllers;

use Request;
use App\College;

class WikiApiController extends Controller
{
	public function index(){

		//$colleges = College::where('id', '>=','2007')
		//->where('id', '<=','2007')->get();
		#$colleges = College::where('overview_content', '=', '')->get();

		$colleges = College::where('slug', '=', 'the-masters-college-and-seminary')->get();

		$allowed_sections = array('History','Campus', 'Facilities', 'Academics', 'Student Life', 'Athletics', 'Faculty', 'Medals', 'Awards', 'Notable alumni');

		//$allowed_sections = array('History');
		//dd($colleges);
		//print_r($colleges);
		//exit();
		foreach ($colleges as $key) {

			echo $key->school_name."<br>";
			$wiki = College::find($key->id);

			$school_desc = ""; 

			//$school_name = $key->school_name;

			$school_arr = array();	
			if(isset($key->alias)){
				$school_arr[] = $key->school_name;
				$tmp = explode('|', $key->alias);
				foreach ($tmp as $key => $value) {
					if($value!=''){
						$school_arr[] = $value;
					}
					
				}
				
			}


			$jsonBody = "badurl";

			foreach ($school_arr as $key => $value) {
				if ($jsonBody!="badurl") {
					break;
				}

				$school_name = str_replace(' ', '%20', $value);
				$school_name = str_replace('&', '&amp;', $value);
				$school_name = str_replace('-Main Campus', '', $value);

				if(strpos($school_name, '-') !== false){

					$school_name = 'startDelimeter'.$school_name;
					$school_name = $this->get_string_between($school_name, 'startDelimeter', '-');
				}
				//$school_name= "Lewis_%26_Clark_College";
				//$school_name = "Queens_College,_City_University_of_New_York";
				//$school_name = 'St._John%27s_College_(Annapolis/Santa_Fe)';
				//$school_name= 'Washington_%26_Jefferson_College';
				//$school_name = 'The_Citadel,_The_Military_College_of_South_Carolina';
				//$school_name = "College_of_Staten_Island";
				//$school_name = "Carson–Newman_University";
				//$school_name= "Benedictine_University";
				//$school_name = "North_Carolina_Agricultural_and_Technical_State_University";
				//$school_name = "College_of_Saint_Benedict_and_Saint_John%27s_University";
				//$school_name = "Johnson_%26_Wales_University";
				//$school_name = "Fairleigh_Dickinson_University";
				//$school_name = "Baruch_College";
				//$school_name = "Embry–Riddle_Aeronautical_University";
				$school_name = 'The_Master%27s_College';
				//echo $school_name."<br>";
				//exit();
				$dec = $this->urlRequest($school_name, 0);

				$jsonBody = $this->getJsonBody($dec);

			}
			//print_r($jsonBody);
			//exit();
			//dd($jsonBody);
			
			//dd($jsonBody);
			//print_r('jsonBody is '. $jsonBody);
			//exit();
			/*	
			if(isset($key->alias)){
				$aliasArr = explode('|', $key->alias);
				$school_name = $aliasArr[0];
			}else{
				$school_name = $key->school_name;
			}
					
			$school_name = str_replace(' ', '%20', $school_name);
			$school_name = str_replace('&', '&amp;', $school_name);

			$dec = $this->urlRequest($school_name, 0);
			$jsonBody = $this->getJsonBody($dec);

			*/
			
			if (strpos(substr($jsonBody, 0, 328),'index.php?title=') !== false) {

				$school_name = $this->get_string_between($jsonBody, 'index.php?title=', '&amp');
				$dec = $this->urlRequest($school_name, 0);
				$jsonBody = $this->getJsonBody($dec);
				

			}

			//print_r($jsonBody);
			//exit();
			//dd($jsonBody);

			$sectionCnt = 1;
			while ($jsonBody!= "" && $jsonBody !='badurl' && $sectionCnt <40) {

				echo $sectionCnt."<br>";
				//print_r($jsonBody);
					
				if($sectionCnt == 1){

					//echo $jsonBody."<br><br><br>";
					$tmpStr = $this->get_string_between($jsonBody, '</table>', '<ol class="references"');
					//dd('tmpStr '. $tmpStr);
					if($tmpStr == ''){
						$tmpStr = $jsonBody . 'endDelimeter';

						$school_desc .= $this->get_string_between($tmpStr, '</table>', 'endDelimeter');
					}else{
						$school_desc .= $tmpStr;
					}

					//dd($school_desc);
				}else{
					//get the first 128 characters of json body see if the section matches any of allowed sections
					$first_line = substr($jsonBody, 0, 628);

					foreach ($allowed_sections as $key => $value) {
						if (strpos( strtolower($first_line), strtolower($value)) !== false) {

							/*
							if($value == "Notable alumni"){
								print_r($jsonBody);
								exit();
							}
							*/
							$tmpStr = 'startDelimeter'.$jsonBody;

							$tmp_desc = $this->get_string_between($tmpStr, 'startDelimeter', '<ol class="references">');

							if($tmp_desc == ""){
								$school_desc .= $jsonBody;
							}else{
								//if (strpos($school_desc,$tmp_desc) !== true) {
									$school_desc .= $tmp_desc;
								//}
								
							}
							//print_r($sectionCnt,$tmpStr);
							//print_r($sectionCnt . " -- ". $jsonBody);
							//exit();
							
							/*
							$left_box = $this->get_string_between($jsonBody, '<div class="thumb tleft">', '</div></div></div>');
							$school_desc = str_replace('<div class="thumb tleft">'.$left_box.'</div></div></div>', '', $school_desc);

							$right_box = $this->get_string_between($jsonBody, '<div class="thumb tright">', '</div></div></div>');
							$school_desc = str_replace('<div class="thumb tright">'.$right_box.'</div></div></div>', '', $school_desc);

							$infobox = $this->get_string_between($jsonBody, '<table class="infobox"', '</table>');
							$school_desc = str_replace('<table class="infobox"'.$infobox.'</table>', '', $school_desc);

							*/

							$school_desc = preg_replace(array('"<a href(.*?)>"', '"</a>"'), array('',''), $school_desc);
							$school_desc = preg_replace(array('"<a rel(.*?)>"', '"</a>"'), array('',''), $school_desc);
							$school_desc = preg_replace(array('"<a class(.*?)>"', '"</a>"'), array('',''), $school_desc);

							$infobox = $this->get_string_between($school_desc, '<table class="infobox', '</table>');
							$school_desc = str_replace('<table class="infobox'.$infobox.'</table>', '', $school_desc);

							$wikitable = $this->get_string_between($school_desc, '<table class="wikitable', '</table>');
							$school_desc = str_replace('<table class="wikitable'.$wikitable.'</table>', '', $school_desc);

							/*
							$removeTable = $this->get_string_between($school_desc, '<table class="', '</table>');
							$school_desc = str_replace('<table class="'.$removeTable.'</table>', '', $school_desc);
							*/
							if(strpos($school_desc, '<h2><span class="mw-headline" id="External_links">') !== false){
								$school_desc = $school_desc . "endDelimeter";
								$externalLinks = $this->get_string_between($school_desc, '<h2><span class="mw-headline" id="External_links">', 'endDelimeter');
								$school_desc = str_replace('<h2><span class="mw-headline" id="External_links">'.$externalLinks.'endDelimeter', '', $school_desc);

								$school_desc .= '</li></ul>';

							}

							


							/*
							$tmpStr = $jsonBody.'endDelimeter';

							$tmp_desc = $this->get_string_between($tmpStr, '<span class="mw-headline" id="Additional_reading">', 'endDelimeter');

							$school_desc = str_replace($tmp_desc, '', $school_desc);

							*/
							
							

						}
					}
					
					
				}
				for ($i=1; $i < 200 ; $i++) { 
					$school_desc = str_replace('<span>[</span>'.$i.'<span>]</span>', '', $school_desc);				
				}
				$school_desc = preg_replace(array('"<a href(.*?)>"', '"</a>"'), array('',''), $school_desc);
				$school_desc = preg_replace(array('"<a rel(.*?)>"', '"</a>"'), array('',''), $school_desc);
				$school_desc = preg_replace(array('"<a class(.*?)>"', '"</a>"'), array('',''), $school_desc);

				$school_desc  = str_replace('<span class="mw-editsection"><span class="mw-editsection-bracket">[</span>edit<span class="mw-editsection-bracket">]</span></span>', '', $school_desc);
				$school_desc = str_replace('â€“', '-', $school_desc);

				$dec = $this->urlRequest($school_name, $sectionCnt);
				$jsonBody = $this->getJsonBody($dec);
				$sectionCnt++;

				
			}

			
			//print_r($school_desc);
			//exit();
			//dd($school_desc.'here');

			$wiki->overview_content = $school_desc;
			$wiki->save();

			
			

		}

		
		

	}

	private function urlRequest($school_name, $section){
		$url = "http://en.wikipedia.org/w/api.php?format=json&action=query&prop=revisions&titles=".$school_name."&rvprop=content&rvsection=".$section."&rvparse";
		//echo $url."<br>";
		$headers = array('Accept' => 'application/json');
		$request = Requests::get($url, $headers);
		

		return json_decode($request->body);
	}
	private function get_string_between($string, $start, $end){
		
	    $string = " ".$string;
	    $ini = strpos($string,$start);
	    if ($ini == 0) return "";
	    $ini += strlen($start);
	    $len = strpos($string,$end,$ini) - $ini;

	    if($start == 'startDelimeter'){

			//print_r($ini. "  ".$len);
			//exit();
		}
	    return substr($string,$ini,$len);
	}

	private function getJsonBody($dec){
		if(isset($dec->query)){
			$dec = $dec->query;
			$dec = $dec->pages;

			$jsonBody = "";
			foreach ($dec as $k) {

				if(!isset($k->revisions[0])){

					return "badurl";

				}
				
				$tmp1 = $k->revisions[0];
				foreach ($tmp1 as $e) {
					return $e;
				}
			}
		}else{
			return "";
		}
		

	}
}
