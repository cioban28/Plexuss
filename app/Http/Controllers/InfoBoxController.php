<?php

namespace App\Http\Controllers;

use Request;
use App\College;

class InfoBoxController extends Controller
{
    // get college list filter //
	public function getFilterLetter() {			
		$input = Request::all();	
		$collegeModel = new College();
		$collegeList=$collegeModel->Colleges($input['name']);		
		
		if(count($collegeList)>0)
		{
			foreach($collegeList as $college)
			{
				$string=substr($college->school_name,0,40).'..';			
				echo ' <li><a href="/college/'.$college->slug.'">'.$string.' ('.$college->state.')</a></li>';
			}
		}		
		exit();	
	}


	// get college list filter //
	// Show Compare Box //
	public function getCompareBox() {
		$input = Request::all();
		$topcolor=$input['topcolcor'];
		$midcolor=$input['midcoclor'];
		$bottomcolor=$input['bottomcolor'];
		echo 
		'
		<div class="header-banner" style="background-color:#'.$topcolor.'">Compare Colleges</div>
		<div class="banner-content-div" style="background-color:#'.$midcolor.'">
				<div class="compareschool mt25">
					<div class="txt-center"><span class="battlefont " style="text-transform:uppercase">Battle</span>&nbsp;<span class="battlefont f-normal">Schools</span></div>
					<h6 class="battlefont fs14 txt-center">COMPARE THE TOP STATS OF ANY SCHOOLS</h6>
						<ol class="olsearch" type="1">
							<li><input type="text" name="search" class="search-text-box" placeholder="Start typing a school name..."/></li>
							<li><input type="text" name="search" class="search-text-box" placeholder="Start typing a school name..."/></li>
							<li><input type="text" name="search" class="search-text-box" placeholder="Start typing a school name..."/></li>
						</ol>
				</div>
		</div>
		<div class="footer-banner" style="background-color:#'.$bottomcolor.'">
			<div>
				<img src="/images/colleges/battle.png"> &nbsp; <span class="battlefont f-normal">Battle !</span>
			</div>
		</div>
		';
		exit();
	}
	
	public function getDirectoryBox() {
		
		$input = Request::all();
		$topcolor=$input['topcolcor'];
		$midcolor=$input['midcoclor'];
		$bottomcolor=$input['bottomcolor'];
		echo 
		'
		<div class="header-banner" style="background-color:#'.$topcolor.'">Compare Colleges</div>
		<div class="banner-content-div scrollbar" style="background-color:#'.$midcolor.'">
				<div>
				
								
							<div class="mt10" >
								 <ul class="bxslider alfa">
									<li><a href="">A</a></li> <li><a href="">B</a></li> <li><a href="">C</a></li>
									<li><a href="">D</a></li> <li><a href="">E</a></li> <li><a href="">F</a></li>
									<li><a href="">G</a></li> <li><a href="">H</a></li> <li><a href="">I</a></li>
									<li><a href="">J</a></li> <li><a href="">K</a></li> <li><a href="">L</a></li>
									<li><a href="">M</a></li> <li><a href="">N</a></li> <li><a href="">O</a></li>
									<li><a href="">P</a></li> <li><a href="">Q</a></li> <li><a href="">R</a></li>
									<li><a href="">S</a></li> <li><a href="">T</a></li> <li><a href="">U</a></li>
									<li><a href="">V</a></li> <li><a href="">W</a></li> <li><a href="">X</a></li>
									<li><a href="">Y</a></li> <li><a href="">Z</a></li>
								 </ul>
							</div>	
							
					<div class="large-12 mt10">
						<div class="large-10 columns no-padding pl10"> <input type="text" name="search" class="search_txt"/></div>
						<div class="large-2 columns"><input type="button" class="search-btn" style="border:none"/></div>
						<div class="clearfix"></div>
					</div> 
					
					<div class="directory">
						<ul >
							<li>Abilene Christian University - Abilene, TX 79601</li>
							<li>Abraham Baldwin Agricultural College (GA)</li>
							<li>Academy of Art University (CA)</li>
							<li>Adams State College (CO)</li>
							<li>Abraham Baldwin Agricultural College (GA)</li>
							<li>Academy of Art University (CA)</li>
							<li>Abraham Baldwin Agricultural College (GA)</li>
							<li>Academy of Art University (CA)</li>
							<li>Abraham Baldwin Agricultural College (GA)</li>
							<li>Abraham Baldwin Agricultural College (GA)</li>
						</ul>
					</div>
					</div>
		</div>
		<div class="footer-banner" style="background-color:#'.$bottomcolor.';min:height:20px;">&nbsp;</div>
		';
		exit();
	}
	
	public function getRankingBox() {
		
		$input = Request::all();
		$topcolor=$input['topcolcor'];
		$toptitle=$input['toptitle'];
		$subheadcolor=$input['subheadcolor'];
		$midcolor=$input['midcoclor'];
		$bottomcolor=$input['bottomcolor'];
		
		
		echo 
		'
		<div class="header-banner" style="background-color:#'.$topcolor.'">'.$toptitle.'</div>
		<div class="banner-content-div" style="background-color:#'.$midcolor.'">
			<ul class="silder_ul">
				<li class="nav-arrow navleft-arrow"></li>
				<li style="width:220px">Lvy League</li>
				<li class="nav-arrow navright-arrow"></li>
			</ul>
			
			<div class="rank-div" style="background:#'.$subheadcolor.'; color:#ffffff">
					<ul class="silder_ul fs14">
						<li> 
						<div class="arrowup-down">
							<div class="arrow-up"></div><div class="arrow-down"></div>
						</div>
						Plexuss&nbsp;<br>Rank</li>
						<li style="border-left:solid 1px; height:32px;border-left-color:#344a3a"></li>
						<li>
							<div class="arrowup-down">
							<div class="arrow-up"></div><div class="arrow-down"></div>
						</div>
						School Name</li>
					</ul>
			</div>
			
			<div class="row-data">
				<div>
					<div>
						<ul class="ul-d-inline">
							<li class="box_image-no mt10 ml10">#1</li>
							<li class="pl25" style="width:80%">
							<span class="battlefont fs14">Harvard University</span><br>
							<span class="battlefont fs14 f-normal ">Cambridge, Massachusetts</span>
							</li>
						</ul>
					</div>
				</div>
				
				<div style="background:#1f1f1f">
					<div>
						<ul class="ul-d-inline">
							<li class="box_image-no mt10 ml10">#2</li>
							<li class="pl25" style="width:80%">
							<span class="battlefont fs14">Yale University</span><br>
							<span class="battlefont fs14 f-normal ">New Haven, Connecticut</span>
							</li>
						</ul>
					</div>
				</div>
				
				<div>
					<div>
						<ul class="ul-d-inline">
							<li class="box_image-no mt10 ml10">#3</li>
							<li class="pl25" style="width:80%">
							<span class="battlefont fs14">Princeton University</span><br>
							<span class="battlefont fs14 f-normal ">Princeton, New Jersey</span>
							</li>
						</ul>
					</div>
				</div>
			
			
				<div style="background:#1f1f1f">
					<div>
						<ul class="ul-d-inline">
							<li class="box_image-no mt10 ml10">#4</li>
							<li class="pl25" style="width:80%">
							<span class="battlefont fs14">University of Pennsylvania</span></br>
							<span class="battlefont fs14 f-normal ">Philadelphia, Pennsylvania</span>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-banner" style="background-color:#'.$bottomcolor.'">
			<h6 class="battlefont fs14 txt-center">expand</h6>
			<img src="/images/colleges/expand.png">
		</div>
		';
		exit();
	}




	/* Average Dounghnut Box Function*/
	public function GetAvgDoughnutBox(){
		$input	= Request::all();
		$data	= array();
		$data['bgcolor']		= $input['bgcolor'];
		$data['headPercent']	= $input['headPercent'];
		$data['headCourse']		= $input['headCourse'];
		$data['content']		= $input['content'];
		$data['boxfor']			= $input['boxfor'];
		$data['minScore'] 		= $input['minScore'];
		$data['maxScore'] 		= $input['maxScore'];
		return View('private/college/ajax/infoboxes', $data);
	}
	
	/* Get Graph Scores Function */
	
	public function GetGraphScoresBox(){
		$input	= Request::all();
		$data	= array();
		
		$data['bgColor']	= $input['bgcolor'];
		$data['shareBtn']	= $input['shareBtn'];
		$data['percentage']	= $input['pecrcentage'];
		$data['ScoreType']  = $input['scoreType'];
		$data['satpercent'] = $input['satpercent'];
		$data['boxfor']		= $input['boxfor'];
		
		
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	
	public function GetTotalRankingBoxes(){
		$input	= Request::all();
		$data	= array();
		
		$data['headbgColor']	= $input['headbgcolor'];
		$data['headBgText']		= $input['headbgtext'];
		$data['rankNumber']		= $input['ranknumber'];
		$data['collegeLogo']  	= $input['collegelogo'];
		$data['collegeName']	= $input['collegename'];
		$data['collegeLink']	= $input['collegelink'];
		$data['boxfor']			= $input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);	
	}
	
	public function GetTotalValueBoxes(){
		$input	= Request::all();
		$data	= array();
		
		$data['headbgColor']	= $input['headbgcolor'];
		$data['headBgText']		= $input['headbgtext'];
		$data['boxfor']			= $input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetGraduationRateBoxes(){
		$input	= Request::all();
		$data	= array();
		$data['headBgText']		= $input['headbgtext'];
		$data['footPercent']	= $input['footPercent'];
		$data['boxfor']			= $input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);	
	}
	
	public function GetComparisonBoxes(){
		
		
		
		
		$input	= Request::all();
		
		$data	= array();
		$data['headingSmall']	= $input['headingSmall'];
		$data['headingBig']		= $input['headingBig'];
		$data['menContent']		= $input['menContent'];
		$data['womenContent']	= $input['womenContent'];
		$data['boxfor']			= $input['boxfor'];
		
		
		return View('private/college/ajax/infoboxes', $data);	
	}
	
	public function GetLearningSkillsBoxes(){
		$input	= Request::all();
		$data	= array();
		$data['header']			= $input['header'];
		$data['title']			= $input['title'];
		$data['boxfor']			= $input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);	
	}
	
	public function GetPopularSalaryBoxes(){
		$input	= Request::all();
		$data	= array();
		$data['headImage']		= $input['headImage'];
		$data['headerbg']		= $input['headerbg'];
		$data['title']			= $input['title'];
		$data['contentbgColor']	= $input['contentbgColor'];
		$data['evenbgColor']	= $input['evenbgColor'];
		
		$data['sat_read_25']	= $input['sat_read_25'];
		$data['sat_read_75']	= $input['sat_read_75'];
		$data['sat_math_25']	= $input['sat_math_25'];
		$data['sat_math_75']	= $input['sat_math_75'];
		$data['sat_write_25']	= $input['sat_write_25'];
		$data['sat_write_75']	= $input['sat_write_75'];
		$data['act_composite_25']	= $input['act_composite_25'];
		$data['act_composite_75']	= $input['act_composite_75'];
		$data['act_english_25']	= $input['act_english_25'];
		$data['act_english_75']	= $input['act_english_75'];
		$data['act_math_25']	= $input['act_math_25'];
		$data['act_math_75']	= $input['act_math_75'];
		$data['act_write_25']	= $input['act_write_25'];
		$data['act_write_75']	= $input['act_write_75'];
		
		$data['boxfor']			= $input['boxfor'];
		$data['divID']			= $input['divID'];
		
		return View('private/college/ajax/infoboxes', $data);	
	}
	
	public function GetConsideredAdmissionBoxes(){
		$input	= Request::all();
		$data	= array();
		$data['headImage']		= $input['headImage'];
		$data['headerbg']		= $input['headerbg'];
		$data['title']			= $input['title'];
		$data['contentbgColor']	= $input['contentbgColor'];
		$data['evenbgColor']	= $input['evenbgColor'];
		$data['content']		= $input['content'];
		$data['boxfor']			= $input['boxfor'];
		$data['divID']			= $input['divID'];
		
		return View('private/college/ajax/infoboxes', $data);	
	}
	
	public function GetAppInfoBox(){
		$input	= Request::all();
		$data	= array();
		$data['bgColor']		= $input['bgColor'];
		$data['title']			= $input['title'];
		$data['firstAns']		= $input['firstAns'];
		$data['secondAns']		= $input['secondAns'];
		$data['thirdAns']		= $input['thirdAns'];
		$data['webLink']		= $input['webLink'];
		$data['boxfor']			= $input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetTestScoresBox(){
		$input	= Request::all();
		$data	= array();
		$data['bgColor']		= $input['bgColor'];
		$data['headImage']		= $input['headImage'];
		$data['titleBgColor']	= $input['titleBgColor'];
		$data['secondAns']		= $input['secondAns'];
		$data['thirdAns']		= $input['thirdAns'];
		$data['webLink']		= $input['webLink'];
		$data['boxfor']			= $input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	
	public function GetNotablesBox(){
		$input	=	Request::all();
		$data	=	array();
		$data['headImg']	=	$input['headImg'];
		$data['title']		=	$input['title'];
		$data['name']		=	$input['name'];
		$data['dob']		=	$input['dob'];
		$data['classYear']	=	$input['classYear'];
		$data['speciality']	=	$input['speciality'];
		$data['majors']		=	$input['majors'];
		$data['boxfor']		= 	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetTuitionBox(){
		$input	=	Request::all();
		$data	=	array();
		$data['headImg']				=	$input['headImg'];
		$data['title']					=	$input['title'];
		$data['icon']					=	$input['icon'];
		$data['InStateTitleColor']		=	$input['InStateTitleColor'];
		$data['OutStateTitleColor']		=	$input['OutStateTitleColor'];
		$data['InTuitionValue']			=	$input['InTuitionValue'];
		$data['InBooksValue']			=	$input['InBooksValue'];
		$data['InRoomValue']			=	$input['InRoomValue'];
		$data['InOtherValue']			=	$input['InOtherValue'];
		$data['OutTuitionValue']		=	$input['OutTuitionValue'];
		$data['OutBooksValue']			=	$input['OutBooksValue'];
		$data['OutRoomValue']			=	$input['OutRoomValue'];
		$data['OutOtherValue']			=	$input['OutOtherValue'];
		$data['totalInExpenseColor']	=	$input['totalInExpenseColor'];
		$data['inExpenseValue']			=	$input['inExpenseValue'];
		$data['totalOutExpenseColor']	=	$input['totalOutExpenseColor'];
		$data['outExpenseValue']		=	$input['outExpenseValue'];
		$data['boxfor']					= 	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	
	public function GetCalculatorBox(){
		$input	=	Request::all();
		$data	=	array();
		$data['bgColor']				=	$input['bgColor'];
		$data['headTitle']				=	$input['headTitle'];
		$data['headImage']				=	$input['headImage'];
		$data['boxfor']					= 	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetLoanRateBox(){
		$input	=	Request::all();
		$data	=	array();
		$data['bgColor']				=	$input['bgColor'];
		$data['headBgColor']			=	$input['headBgColor'];
		$data['headTitle1']				=	$input['headTitle1'];
		$data['headTitle2']				=	$input['headTitle2'];
		$data['contentCrossColor']		=	$input['contentCrossColor'];
		$data['boxfor']					= 	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetCampusDineBox(){
		$input	=	Request::all();
		$data	=	array();
		$data['bgImage']				=	$input['bgImage'];
		$data['headTitle']				=	$input['headTitle'];
		/*$data['CampusContent']		=	$input['CampusContent'];*/
		$data['boxfor']					= 	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetBigListBox(){
		$input	=	Request::all();
		$data	=	array();
		$data['bgImage']				=	$input['bgImage'];
		$data['headTitle']				=	$input['headTitle'];
		$data['headTitle2']				=	$input['headTitle2'];
		$data['ListContent']			=	$input['ListContent'];
		$data['boxfor']					= 	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetWeatherBox(){
		$input	=	Request::all();
		$data	=	array();
			
		$data['bgColor']				=	$input['bgColor'];
		$data['currPlace']				=	$input['currPlace'];
		$data['currTemp']				=	$input['currTemp'];
		$data['WeatherImage']			=	$input['WeatherImage'];
		$data['WeatherType']			=	$input['WeatherType'];
		$data['boxfor']					= 	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetCollegeSportsBox(){
		$input = Request::all();
		$data  = array();
		
		$data['topImage']		=	$input['topImage'];
		$data['topTitleSub']	=	$input['topTitleSub'];
		$data['topTitleMain']	=	$input['topTitleMain'];
		
		$data['basemen']		=	$input['basemen'];
		$data['basewomen']		=	$input['basewomen'];
		
		$data['basketmen']		=	$input['basketmen'];
		$data['basketwomen']	=	$input['basketwomen'];
		
		$data['all_track_men']	=	$input['all_track_men'];
		$data['all_track_women']=	$input['all_track_women'];
		
		$data['cross_men']		=	$input['cross_men'];
		$data['cross_women']	=	$input['cross_women'];
		
		$data['football_men']	=	$input['football_men'];
		$data['football_women']=	$input['football_women'];
		
		$data['golf_men']		=	$input['golf_men'];
		$data['golf_women']		=	$input['golf_women'];
		
		$data['all_track_men']	=	$input['all_track_men'];
		$data['all_track_women']=	$input['all_track_women'];
		
		$data['gymnastics_men']	=	$input['gymnastics_men'];
		$data['gymnastics_women']=	$input['gymnastics_women'];
		
		$data['rowing_men']		=	$input['rowing_men'];
		$data['rowing_women']	=	$input['rowing_women'];
		
		$data['soccer_men']		=	$input['soccer_men'];
		$data['soccer_women']	=	$input['soccer_women'];
		
		$data['softball_men']	=	$input['softball_men'];
		$data['softball_women']	=	$input['softball_women'];
		
		$data['swimming_men']	=	$input['swimming_men'];
		$data['swimming_women']=	$input['swimming_women'];
		
		$data['tennis_men']		=	$input['tennis_men'];
		$data['tennis_women']	=	$input['tennis_women'];
		
		$data['volleyball_men']	=	$input['volleyball_men'];
		$data['volleyball_women']=	$input['volleyball_women'];
		
		$data['water_polo_men']	=	isset($input['water_polo_men'])?$input['water_polo_men']:'';
		$data['water_polo_women']=	isset($input['water_polo_women'])?$input['water_polo_women']:'';
		
		$data['boxfor']			= 	$input['boxfor'];
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetCollegeExpensesBox(){
		$input = Request::all();
		$data  = array();
		
		$data['bgColor']			=	$input['bgColor'];
		$data['title']				=	$input['title'];
		$data['boxfor']				= 	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetUgEthnicBox(){
		$input = Request::all();
		$data  = array();
		$data['headImg']		=	$input['headImg'];
		$data['subTitle']		=	$input['subTitle'];
		$data['headTitle']		=	$input['headTitle'];
		$data['aianpercent']	=	$input['aianpercent'];
		$data['asiapercent']	=	$input['asiapercent'];
		$data['bkaapercent']	=	$input['bkaapercent'];
		$data['hisppercent']	=	$input['hisppercent'];
		$data['nhpipercent']	=	$input['nhpipercent'];
		$data['whitpercent']	=	$input['whitpercent'];
		$data['twomorepercent']	=	$input['twomorepercent'];
		$data['unknpercent']	=	$input['unknpercent'];
		$data['nralpercent']	=	$input['nralpercent'];
		$data['boxfor']			=	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetEnrollNutBox(){
		$input = Request::all();
		$data  = array();
		$data['headBg']			=	$input['headBg'];
		$data['smallHeadTitle']	=	$input['smallHeadTitle'];
		$data['bigHeadTitle']	=	$input['bigHeadTitle'];
		$data['headText']		=	$input['headText'];
		$data['graphImage']		=	$input['graphImage'];
		$data['footText']		=	$input['footText'];
		$data['boxfor']			=	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetThreeNutBox(){
		$input = Request::all();
		$data  = array();
		$data['sideImage']			=	$input['sideImage'];
		$data['midGraph']			=	$input['midGraph'];
		$data['rightContentOne']	=	$input['rightContentOne'];
		$data['rightContentTwo']	=	$input['rightContentTwo'];
		$data['rightContentThree']	=	$input['rightContentThree'];
		$data['boxfor']				=	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
	
	public function GetMajorProgramBox(){
		$input = Request::all();
		
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['bgColor']	=	$input['bgColor'];
		$data['title']		=	$input['title'];
		$data['subTitle']	=	$input['subTitle'];
		$data['progContent']=	$input['progContent'];
		$data['progLink']	=	$input['progLink'];
		$data['progIcon']	=	$input['progIcon'];
		$data['boxfor']		=	$input['boxfor'];
		
		return View('private/college/ajax/infoboxes', $data);
	}
}
