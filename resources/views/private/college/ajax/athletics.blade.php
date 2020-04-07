<?php
$collegeData = $college_data;
?>

<div style='display:block;'>
	<div class="row university-stats-content pt10">
        <div class="large-12 columns no-padding bg-ext-black radial-bdr">
            <div class="large-6 small-12 columns no-padding college-rank-divide">
                <div class="athletics-TopTitle">ATHLETICS </div>
                <div class="athletics-green-content"> N/A </div>
                
                <div class="detail-university-grey pl20">CONFERENCE <img src="/images/colleges/question-query.png" alt=""/></div>
                <div class="detail-university-green-content pl20 fs18"> N/A <br /> {{$collegeData->class_name}}</div>
                
                <div class="detail-university-grey pl20"># OF COLLEGE CHAMPIONSHIPS <img src="/images/colleges/question-query.png" alt=""/></div>
                <div class="detail-university-green-content pl20">3,173</div>
                
                <div class="detail-university-grey pl20"># OF ATHLETIC TEAMS <img src="/images/colleges/question-query.png" alt=""/></div>
                <div class="detail-university-green-content pl20 mb5">14</div>
            </div>
            <div class="large-6 columns no-padding">
                <img src="/images/colleges/rugby-sports.png" alt="">
            </div>
        </div>
    </div>
</div>

<div class="row">
	
    <div class="large-12 small-12 columns no-padding mt10">
        <div class="box-div" id="college-sportsBox"></div>
        <div class="box-div" id="total-players-comparison-box"></div>
        <div class="box-div" id="athletic-aid-comparison-box"></div>
    </div>
    
    
    
	<!--<div class="large-4 columns">
    	<div class="box-div" id="college-sportsBox"></div>
    </div>
    
    <div class="large-4 columns">
    	<div class="box-div" id="total-players-comparison-box"></div>
    </div>
    
    <div class="large-4 columns">
    	<div class="box-div" id="athletic-aid-comparison-box"></div>
    </div>-->
    
    <!--<div class="large-8 columns no-padding">
    	<div id="expenses-box-athletics"></div>
    </div>-->
</div>

<?php 
$baseball_men = count($collegeData->baseball_men)>0?"icon-man-small":"icon-no-men";
$baseball_women = count($collegeData->baseball_women)>0?"icon-woman-small":"icon-no-women";

$basketball_men = count($collegeData->basketball_men)>0?"icon-man-small":"icon-no-men";
$basketball_women = count($collegeData->basketball_women)>0?"icon-woman-small":"icon-no-women";

$track_comb_men = count($collegeData->track_comb_men)>0?"icon-man-small":"icon-no-men";
$track_comb_women = count($collegeData->track_comb_women)>0?"icon-woman-small":"icon-no-women";

$xcountry_men = count($collegeData->xcountry_men)>0?"icon-man-small":"icon-no-men";
$xcountry_women = count($collegeData->xcountry_women)>0?"icon-woman-small":"icon-no-women";

$football_men = count($collegeData->football_men)>0?"icon-man-small":"icon-no-men";
$football_women = count($collegeData->football_women)>0?"icon-woman-small":"icon-no-women";

$golf_men = count($collegeData->golf_men)>0?"icon-man-small":"icon-no-men";
$golf_women = count($collegeData->golf_women)>0?"icon-woman-small":"icon-no-women";

$gymn_men = count($collegeData->gymn_men)>0?"icon-man-small":"icon-no-men";
$gymn_women = count($collegeData->gymn_women)>0?"icon-woman-small":"icon-no-women";

$rowing_men = count($collegeData->rowing_men)>0?"icon-man-small":"icon-no-men";
$rowing_women = count($collegeData->rowing_women)>0?"icon-woman-small":"icon-no-women";

$soccer_men = count($collegeData->soccer_men)>0?"icon-man-small":"icon-no-men";
$soccer_women = count($collegeData->soccer_women)>0?"icon-woman-small":"icon-no-women";

$softball_men = count($collegeData->softball_men)>0?"icon-man-small":"icon-no-men";
$softball_women = count($collegeData->softball_women)>0?"icon-woman-small":"icon-no-women";

$swim_dive_men = count($collegeData->swim_dive_men) > 0?"icon-man-small":"icon-no-men";
$swim_dive_women = count($collegeData->swim_dive_women) > 0?"icon-woman-small":"icon-no-women";

$swim_dive_men = count($collegeData->swim_dive_men) > 0?"icon-man-small":"icon-no-men";
$swim_dive_women = count($collegeData->swim_dive_women) > 0?"icon-woman-small":"icon-no-women";

$tennis_men = count($collegeData->tennis_men) > 0?"icon-man-small":"icon-no-men";
$tennis_women = count($collegeData->tennis_women) > 0?"icon-woman-small":"icon-no-women";

$volley_ball_men = count($collegeData->volley_ball_men) > 0?"icon-man-small":"icon-no-men";
$volley_ball_women = count($collegeData->volley_ball_women) > 0?"icon-woman-small":"icon-no-women";

$water_polo_men = count($collegeData->water_polo_men) > 0?"icon-man-small":"icon-no-men";
$water_polo_women = count($collegeData->water_polo_women) > 0?"icon-woman-small":"icon-no-women";


/* The Comparison Box content */
$men_content='<div class="comparison-content"><span class="fs36">'.number_format($collegeData->total_men).'</span><br />Admitted</div>';
$women_content='<div class="comparison-content"><span class="fs36">'.number_format($collegeData->total_women).'</span><br />Admitted</div>';

$aid_men_content='<div class="comparison-content"><span class="fs30">'.number_format($collegeData->stu_aid_men).'</span><br />Admitted</div>';
$aid_women_content='<div class="comparison-content"><span class="fs30">'.number_format($collegeData->stu_aid_women).'</span><br />Admitted</div>';

?>

<script type="text/javascript">
$(document).ready(function(e){
    getCollegeSportsBox('athletics-topimg.png','VARSITY','SPORTS','{{ $baseball_men }}','{{ $baseball_women }}','{{ $basketball_men }}','{{ $basketball_women }}','{{ $track_comb_men }}','{{ $track_comb_women }}','{{ $xcountry_men }}','{{ $xcountry_women }}','{{ $football_men }}','{{ $football_women }}','{{ $golf_men }}','{{ $golf_women }}','{{ $gymn_men }}','{{ $gymn_women }}','{{ $rowing_men }}','{{ $rowing_women }}','{{ $soccer_men }}','{{ $soccer_women }}','{{ $softball_men }}','{{ $softball_women }}','{{ $swim_dive_men }}','{{ $swim_dive_women }}','{{ $tennis_men }}','{{ $tennis_women }}','{{ $volley_ball_men }}','{{ $volley_ball_women }}','{{ $water_polo_men }}','{{ $water_polo_women }}','athletics-box','college-sportsBox');
	//getPopularSalaryBoxes('calendar-top-image.png','#000000','AVERAGE SALARY BY POPULAR DEGREES','#004358','popular_box_avg_salary','expenses-box-athletics');
});

getGenderComparisonBox('TOTAL # OF','ATHLETES','{{$men_content}}','{{$women_content}}','gender-comparison-box','total-players-comparison-box');
getGenderComparisonBox('TOTAL ATHLETICALLY RELATED','STUDENT AID GIVEN','{{$aid_men_content}}','{{$aid_women_content}}','gender-comparison-box','athletic-aid-comparison-box');

</script>
