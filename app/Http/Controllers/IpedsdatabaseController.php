<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IpedsdatabaseController extends Controller
{
	//URL of IPED Data http://nces.ed.gov/ipeds/datacenter/DataFiles.aspx

	//We can NOT update the College ids , ipeds_id or the school name.
	//We need to check that The IPEDS ID has not changed since the last update. If so any user under the old Id will show a different college.

	//checkipedstable function variables

	//Info of IPEDS master Data Table.
	private $ipedsCheckDataTable = 'hd2013';
	private $ipedsCheckColumnId = 'UNITID';
	//private $ipedsCheckColumnSchoolName = 'INSTNM';

	// Info of NEW College table we are making.
	private $plexussCheckTable = 'colleges-new';	
	private $plexussCheckColumnId = 'ipeds_id';
	//private $plexussCheckColumnSchoolName = 'school_name';

	public function __construct(){
		// Is there a new college table ready?
		//$this->checkForTable($this->plexussCheckTable);

		// Is the IPEDS table ready?
		//$this->checkForTable($this->ipedsCheckDataTable);
	}

	public function cleanRankingNameColumn(){
		$col = DB::table('PlexussRankingsForAlex2')->select('id', 'College')->get();
		foreach ($col as $key => $row) {
			$new = str_replace('??', ' ', $row->College);
			$new = str_replace('?', '', $new);
			DB::table('PlexussRankingsForAlex2')->where( 'id', '=', $row->id )->update(array('College' => $new));
		}
		echo 'Done';
		exit;
	}

	public function cleanFindthebestNameColumn(){

		$col = DB::table('findthebestimages')
		->where('name', 'LIKE', '%(%)%')
		->get();


		foreach ($col as $key => $row) {
			$string = preg_replace( '/\(([^\)]+)\)/' , '', $row->name);

			DB::table('findthebestimages_copy')->where( 'id', '=', $row->id )->update(array('name' => $string));
		}
		echo 'Done';
		exit;
	}

	public function linkRankingToCollegeTable (){
		/*
		$col = DB::table('PlexussRankingFinal')->select( 'ipeds_id', 'College')->get();

		$found = 0;
		$notFound = 0;
		$mutiFound = 0;

		foreach ($col as $key => $row) {
			$college = DB::table('colleges')->select('id', 'school_name')->where('ipeds_id', '=', $row->ipeds_id)->first();
			$found++;


			DB::table('PlexussRankingFinal')
			->where('ipeds_id', '=', $row->ipeds_id)
			->update(array('college_id' => $college->id));
		}
		echo 'we found ' . $found;
		*/
	}


	public function addcollegeimages(){

		//$imageDomain = 'http://img1.findthebest.com/sites/default/files/10/media/images/t/';

		$imageTable = DB::table('findthebestimagesGaryEdit')->select('image_name', 'ipeds_id')->get();

		//dd($imageTable);
		


		//$found = 0;
		//$notFound = 0;
		//$mutiFound = 0;
	
		
		foreach ($imageTable as $key => $row) {

			echo '<br/>';
			echo $row->image_name;


			DB::table('colleges')->where('ipeds_id', '=', $row->ipeds_id)
			->update(array('logo_url' => $row->image_name));

			/*
			public 'image_name' => string 'University_of_Alabama_at_Birmingham_221458.png' (length=46)
      		public 'ipeds_id' => int 100663
			
			$college = DB::table('colleges')->select('id', 'school_name')->where('school_name', '=', $row->name)->get();


			if (!$college) {
				$notFound++;
				//echo  '<h4 style="color:red;"> ' .$row->name . ' Was not Found in DB.<br/> <img src="'. $imageDomain  .$row->image_name .'"  />  </h4><br/><br/><br/>';
				echo  '<h4 style="color:red;"> ' .$row->name . ' Was not Found in DB. </h4><br/><br/><br/>';
				continue;
			}



			if (count($college) > 1) {
				$mutiFound ++;
				echo '<h5 color:blue;>There was more than one school with name</h5>';
				echo '<pre>';
				print_r($college);
				echo '</pre>';
				echo '<br/><br/>';
				continue;
			}

			$found++;
			//echo  $row->name . 'Was Found! for image<br/> <img src="'. $imageDomain  .$row->image_name .'"/><br/><br/><br/>';
			echo  $row->name . 'Was Found! for image<br/><br/><br/>';
		*/
		}


		/*
		echo '<h2>' . $found .' School have been found in the college Database.</h2>';
		echo '<h2>' . $notFound .' School have NOT found in the college Database.</h2>';
		echo '<h2>' . $mutiFound .' School have multiple entries in the DB.</h2>';
		echo 'done';
		*/
		exit;
	}


	public function addNewSchoolsToDb (){

		echo 'Getting schools from the IPEDS Table.<br/>';

		// Get all the IPEDS ids in the NEW data file. 
		$ipedDataIds = DB::table($this->ipedsCheckDataTable)->select($this->ipedsCheckColumnId)->get();

		// Get all the IPEDS ids all ready in out plexuss dtabase. 
		$plexussIpedIds = DB::table($this->plexussCheckTable)->select($this->plexussCheckColumnId)->get();

		$ipedsCol = $this->ipedsCheckColumnId;
		$plexCol = $this->plexussCheckColumnId;

		// /dd($plexussIpedIds);
		// Loop the IPEDS data Ids one by one searching for matching id in the plexuss ipeds Ids. 
		// If one is found that is NOT already in out database we will add it.
		$cnt = 0;
		foreach ($ipedDataIds as $ipedRow) {
			$fnd = 0;
			foreach ($plexussIpedIds as $plexussRow) {
				if ($ipedRow->$ipedsCol == $plexussRow->$plexCol) {
					$fnd=1;
					break;
				}
			}

			if (!$fnd) {
				$cnt++;
				echo 'Adding '. $ipedRow->$ipedsCol . ' to plexuss college table! <br/>';
				DB::table($this->plexussCheckTable)->insert(array( $plexCol => $ipedRow->$ipedsCol));
			}
		}

		echo $cnt . ' Schools have been added to the College list.';
		exit;
	}


	public function updateIpedsDB(){

		// Build a todo list from column info table
		$updateList = DB::table('column_info')->select( 'column_name', 'plexuss_name', 'table_name', 'use_label', 'plexuss_table')->where( 'allow_sync','=', 1 )->get();
		
		// Was there at least one column to update?

		if (!count($updateList)) {
			echo "There was No column marked to sync in the Database.<br/>Please enable a column to update in the column_info table.";
			exit;
		}

		//Loop into the list one by one.
		foreach ($updateList as $key => $value) {
			//Set the plexuss college table name that we will be updating.
			$this->collegeTable = $value->plexuss_table;

			//run the loop method.
			$this->runUpdateColumnLoop( $value->table_name, $value->column_name, $value->plexuss_name, $value->use_label);
		}
	}


	private function runUpdateColumnLoop( $data_file, $ipedColumn = null, $plexussColumn = null, $use_label = null ){
		ini_set('mysql.connect_timeout', 3600);
		ini_set('default_socket_timeout', 3600);
		ini_set('max_execution_time', 3600);

		//Does this ipeds datafile table exist?
		$this->checkForTable($data_file);
		
		//Get a all the ids, columns that we want to merge into the new college page.
		$columnItems = DB::table( $data_file )->select( 'UNITID', $ipedColumn)->get();

		//If use label is set, get a list of column variables from the data_label table.
		if ($use_label) {
			$labelData = DB::table( $data_file . '_label' )->select('varname', 'codevalue', 'valuelabel')->where('varname','=', $ipedColumn)->get();
		}

		//Set the counter to zero.
		$count = 0;

		//Loop the rows for the IPEDS Data table that were returned above in $columnItems..
		foreach ($columnItems as $key => $columnItemRow) {
			$count++;

			if ($use_label) {
				//if has $use_label check the data against the label data $labelData
				foreach ($labelData as $key => $value) {

					//If we find it then replace the variable with the label alternative 
					if ($value->codevalue == $columnItemRow->$ipedColumn) {
						$columnVar = $value->valuelabel;
						break;
					}
				}

			} else {
				$columnVar = $columnItemRow->$ipedColumn;
			}

			//update the table with the prepared data.
			DB::table( $this->collegeTable )->where( 'ipeds_id', '=', $columnItemRow->UNITID )->update(array($plexussColumn => $columnVar));

		}

		echo "$count items were added to $this->collegeTable under $plexussColumn . <br/> <br/> <br/> <br/>";
	
	}


	private function checkForTable ( $tableName ){
		
		if(!Schema::hasTable($tableName)) {
		    echo "College table is missing please make one called $tableName";
		    exit;
		}
		
	}


	public function downloadImages(){

		$start = 4501;

		$stop = 5000;


		$count = 0;

		$url = 'http://img1.findthebest.com/sites/default/files/10/media/images/t/';

		$saveto =  storage_path();

		$images = DB::table('findthebestimages')->select('image_name')->get();

		foreach ($images as $key => $row) {

			if ($key >= $start && $key <= $stop) {

				$imagename = $row->image_name;
				$url = 'http://img1.findthebest.com/sites/default/files/10/media/images/t/' . $imagename;
				echo $url . '<br/>';
				$this->grab_image($url, $saveto, $imagename);
				$count++;
			}
			
			
		}

		echo 'Count was ' . $count ;
	}


	private function grab_image($url,$saveto , $imagename){
		    $ch = curl_init ($url);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		    $raw=curl_exec($ch);
		    curl_close ($ch);

		    $filepath = $saveto . '\cache\/' . $imagename;

		    $fp = fopen($filepath,'w');
			fwrite($fp, $raw); 
			fclose($fp);
			
			
			$aws = AWS::get('s3');


			// Post a image to S3 example
			$result = $aws->putObject(array(
				'ACL'	=> 'public-read',
		    	'Bucket'     => 'asset.plexuss.com/college/logos',
		    	'Key'        => $imagename,
		    	'SourceFile' => $filepath,
			));
		}
}
