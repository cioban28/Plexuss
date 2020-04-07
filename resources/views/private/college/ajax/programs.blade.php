<div style='display:block;'>
	<div class="row university-stats-content pt10">
        <div class="large-12 columns no-padding bg-ext-black radial-bdr">
            <div class="large-6 columns no-padding college-rank-divide">
                <div class="programs-headTitle">PROGRAMS & MAJORS</div>
                <div class="detail-university-grey"><span># OF BACHELOR DEGREES AWARDED</span></div>
                <div class="programs-green-content">7,329</div>
                <div class="detail-university-grey">MOST POPULAR UNDERGRAD DEPARTMENT</div>
                <div class="row">
                	<div class="large-4 small-4 columns detail-university-green-content pb30 mt5">1,727</div>
                    <div class="large-8 small-8 columns detail-university-green-content font-14">DEGREES AWARDED IN SOCIAL SCIENCES</div>
                </div>
				<div class="detail-university-grey">MOST POPULAR UNDERGRAD MAJOR</div>
                <div class="row detail-university-green-content pb30">
                    <div class="large-4 small-4 columns no-padding">572</div>
                    <div class="large-8 small-8 columns font-14">DEGREES AWARDED IN POLITICAL SCIENCE</div>
                </div>
            </div>
			<div class="large-6 columns no-padding">
				<img src="/images/colleges/programs-top-image.png" alt="">
            </div>
        </div>
    </div>
</div>

<div class="row pos-relative">
	<div class="custom-one-col-box">
		<div id="program-factory-box"></div>
    </div>
    
    <div class="custom-one-col-box">
		<div id="program-globe-box"></div>
    </div>
</div>

<?php 
$factoryContent = "<ul><li>Architecture</li></ul>";
$worldBoxContent = "<ul><li>African-American/Black Studies</li><li>Asian American Studies</li><li>East Asian Studies</li><li>Latin American Studies</li><li>Women’s Studies</li></ul>";
?>





<script language="javascript">
/* Functions for the Boxes */
$(document).ready(function(e) {
	getMajorProgramBox('#004358','Architecture & Related Services','TOP MAJORS BY DEGREES AWARDED','<?php echo $factoryContent?>', 'http://google.co.in','major-box-factoryman.png','programs-box','program-factory-box');
	
	getMajorProgramBox('#04A6AE','Area, Ethnic, Cultural, Gender & Group Studies','TOP MAJORS BY DEGREES AWARDED','<?php echo $worldBoxContent?>','http://google.co.in','major-box-worldman.png','programs-box','program-globe-box');
	
});
</script>