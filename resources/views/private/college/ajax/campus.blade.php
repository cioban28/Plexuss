<div style='display:block;'>
	<div class="row university-stats-content pt10">
        <div class="large-12 columns no-padding bg-ext-black radial-bdr">
            <div class="large-6 small-12 columns no-padding college-rank-divide">
                <div class="university_content_admis_deadline pl20">
                    CAMPUS LIFE
                </div>
                <div class="detail-university-grey pl20">
                    <span>ON-CAMPUS HOUSING CAPACTIY</span>
                </div>
                <div class="detail-university-green-content pl20">
                    N/A
                </div>
                <div class="large-12 small-12 columns detail-university-grey pl20">
                    UNDERGRADS LIVING ON CAMPUS
                </div>
                <div class="row">
                	<div class="large-3 small-3 columns detail-university-green-content pl20 text-right">N/A</div>
                    <div class="large-7 small-8 columns detail-university-green-content text-center">N/A <span class="font-12">FRESHMEN</span></div>
                </div>
                
                <div class="detail-university-grey pl20">
                    CAMPUS SIZE
                </div>
                <div class="detail-university-green-content pl30">N/A <span class="font-12">ACRES</span></div>
                
                <div class="row detail-university-grey">
                	<div class="large-4 small-4 columns text-center">GREEK LIFE?</div>
                    <div class="large-4 small-4 columns text-center bold-font">FRATERNATIES</div>
                    <div class="large-4 small-4 columns text-center bold-font">SORORITIES </div>
                </div>
                <div class="row mb20">
                    <div class="detail-university-green-content large-4 small-4 columns text-center">N/A</div>
                    <div class="detail-university-green-content large-4 small-4 columns text-center">N/A</div>
                    <div class="detail-university-green-content large-4 small-4 columns text-center">N/A</div>
                </div>
            </div>
            <div class="large-6 columns no-padding">
                <img src="/images/colleges/stats-top-content.jpg" alt="">
            </div>
        </div>
    </div>
</div>

<div class="custom-row">
    <div class="custom-4">
		<div id="undergrad-gender-compare"></div>
    </div>
    <div class="custom-4">
		<div id="campus-dinner"></div>
    </div>
    <div class="custom-4">
    	<div id="campus-biglist-box"></div>
    </div>
    <!--
    <div class="custom-4">
    	<div class="bg-army-block radial-bdr text-black margin20bottom">
            <div class="box-2-header">ROTC</div>
            <div class="margin20top">
                <div class="large-12 columns no-padding margin20bottom">
                    <div class="large-4 columns no-padding">
                        <img src="/images/colleges/correct-big.png">
                    </div>
                    <div class="large-8 bg-ext-black columns rotc-content-title">AIR FORCE</div>
                </div>
                <div class="large-12 columns no-padding margin20bottom">
                    <div class="large-4 columns no-padding">
                        <img src="/images/colleges/correct-big.png">
                    </div>
                    <div class="large-8 bg-ext-black columns rotc-content-title">AIR FORCE</div>
                </div>
                <div class="large-12 columns no-padding margin20bottom">
                    <div class="large-4 columns no-padding">
                        <img src="/images/colleges/correct-big.png">
                    </div>
                    <div class="large-8 bg-ext-black columns rotc-content-title">AIR FORCE</div>
                </div>
            </div>
            <br>
        </div>
    </div>
    -->
    <div class="custom-4">
    	<div id="campus-weather-box"></div>
    </div>
</div>




<?php
/* Gender Comparison Box Content */
$men_content='<div class="comparison-content"><span>13%</span><br /><span class="font-14">MEN IN FRATERNITIES</span></div>';
$women_content='<div class="comparison-content"><span>29 </span><br /><span class="font-14">WOMEN IN SOROITIES</span><br /></div>';

/* Campus Dining Box */
/*$dine_content = '<div class="campus-content"><div class="row"><div>Meal Plan Available</div></div></div>';*/

$list_content = '<ul class="listPanel-campus"><li>BRUIN CAFÉ</li><li>CAFÉ 1919</li><li>CAFE SYNAPSE</li><li>CARL’S JR</li><li>BRUIN CAFÉ</li><li>CAFÉ 1919</li><li>CAFE SYNAPSE</li><li>CARL’S JR</li><li>BRUIN CAFÉ</li><li>CAFÉ 1919</li><li>CAFE SYNAPSE</li><li>CARL’S JR</li><li>BRUIN CAFÉ</li><li>CAFÉ 1919</li><li>CAFE SYNAPSE</li><li>CARL’S JR</li></ul>';

?>

<script language="javascript">
/* Functions for the Boxes */
$(document).ready(function(e) {
	//getGenderComparisonBox('UNDERGRAD','STUDENT GENDER', '21%' ,'ADMITTED', '35%', 'ADMITTED','undergrad_in_greek','','','student-gender-compare');
	//getGenderComparisonBox('UNDERGRAD STUDENTS','IN GREEK LIFE','<?php echo $men_content;?>','<?php echo $women_content;?>','gender-comparison-box','undergrad-gender-compare');
	
	//getCampusDineBox('fork-image.png','CAMPUS DINING','campus-dinner-box','campus-dinner');
	
	//getBigListBox('milk-cookies.png','DINING HALLS & CAMPUS','RESTAURANTS','<?php echo $list_content;?>','biglist-box','campus-biglist-box');
	
	//getWeatherBox('#05CED3','LOS ANGELES CA','76°F','sunny-day.png','SUNNY','weather-box','campus-weather-box');
	
});
</script>