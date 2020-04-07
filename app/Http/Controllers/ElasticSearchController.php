<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ElasticSearchController extends Controller
{
    private $ip = '54.187.179.40';

	public function resetCollegeInfo(){
		$method = 'DELETE';
		$url = 'http://'. $this->ip . ':9200/colleges';
		
		//echo 'Deleting /colleges<br/>';
		$curlReturn = $this->curl( $method, $url );
		echo $curlReturn;
	}


	public function deadline(){

		$count = 0;
		$missing = 0;
		$double = 0;


		$data = DB::table('deadline')->select('name')->get();

		foreach ($data as $key => $row) {
			
			$return = DB::table('colleges')->select('school_name')->where('school_name','=',$row->name)->get();


			echo '<br/>';
			$n = count($return);

			echo $n;

			if ($n == 0) {	
				echo "<p style='color:red;'>". $row->name  ." was NOT found in the database!!!!  Fix it!!!</p>";
				$missing++;
			}

			if ($n == 1) {
				echo "<p>". $row->name  ." was found in the database!</p>";
				$count++;
			}

			if ($n > 1) {
				echo "<p style='color:green'>Duplicates found for ". $row->name  ." in the database!  Fix it!!</p>";
				$double++;
			}

		}

		echo '<h1>Found ' . $count . ' school names</h1>';
		echo '<h1>Found ' . $missing . ' MISSING school names</h1>';
		echo '<h1>Found ' . $double . ' DOUBLE school names</h1>';
	}


	public function setMapping(){
		
		$method = 'PUT';
		$url = 'http://'. $this->ip . ':9200/colleges';

		$query = array(
			'mappings' => array(
				'school' => array(
					'properties' => array(
						'school_name' => array(
							'type' => 'string',
							'index'=> 'analyzed',
							'analyzer' => 'english'
							)
						)
					)
				)
			);

		$curlReturn = $this->curl( $method, $url, json_encode($query) );
		echo $curlReturn ;

	}


	public function getMapping(){
		$method = 'GET';
		$url = 'http://'. $this->ip . ':9200/colleges/_mapping/school';

		$curlReturn = $this->curl( $method, $url );
		echo $curlReturn ;
	}




	public function updateCollegeInfo(){

		$collegeTable = DB::table('colleges');

		$countTracker = 0;
		$collegeCount = $collegeTable->count();

		//echo 'We have ' . $collegeCount . ' rows to update in the database.<br/>';

		//Get a list of all ids we need to update.
		$collegeIdList = $collegeTable->select('id')->get();

		foreach ($collegeIdList as $key => $collegeId) {

			$collegeRow = DB::table('colleges')
			->select('id', 'slug', 'alias', 'school_name', 'address', 'city', 'state', 'long_state', 'zip')
			->where('id', '=', $collegeId->id )
			->first();

			$method = 'POST';
			$url = 'http://'. $this->ip . ':9200/colleges/school/' . $collegeId->id;

			$curlReturn = $this->curl( $method, $url, json_encode( $collegeRow) );

			echo '<pre>';
			echo $curlReturn ;
			echo '</pre>';
		}
	}

	public function searchCollegeInfo($needle){

		$method = 'GET';
		$url = 'http://'. $this->ip . ':9200/colleges/school/_search?size=20';

		$query = array(
			'query' => array(
				'query_string' => array(
					'query' => $needle
					)
				)
			);


		$curlReturn = $this->curl( $method, $url, json_encode( $query) );

		$data = json_decode($curlReturn);



		foreach ($data->hits->hits as $key => $value) {
			
			//print_r($value->_source);

			$payload[$key] = $value->_source;
		}
		

		//print_r($payload);
		if (isset($payload)) {
			return json_encode($payload);
		}else{
			return '';
		}

	}


	private function curl( $method, $url, $query=null ){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_PORT, 9200);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}
