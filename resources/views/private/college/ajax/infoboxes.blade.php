@if($boxfor=='rankingbox')
<style>
.owl-theme .owl-controls .owl-buttons div{
	background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
	border-radius: 30px;
	color: #fff;
	display: inline-block;
	font-size: 12px;
	margin: 5px;
	opacity: 0.5;
	padding: 3px 10px;
}
</style>

<div class="header-banner" style="background-color:#<?php echo $topcolor?>"><?php echo $toptitle?></div>
<div class="banner-content-div" style="background-color:#<?php echo $midcolor?>">
    <div id="msg-carousel" class="owl-carousel">
        @foreach($rankData as $rankData)
        <div class="item text-center text-white p5 fs16 bold-font" data-rankid="{{$rankData->id}}">{{$rankData->title}}</div>
        @endforeach
    </div>
    
    <div class="rank-div" style="background:#<?php echo $subheadcolor?>; color:#ffffff">
        <ul class="silder_ul fs14">
            <li>
                <!--<div class="arrowup-down">
                    <div class="arrow-up"></div><div class="arrow-down"></div>
                </div>-->
                Plexuss&nbsp;<br>Rank
            </li>
            <li style="border-left:solid 1px; height:32px;border-left-color:#344a3a"></li>
            <li>
                <!--<div class="arrowup-down">
                    <div class="arrow-up"></div><div class="arrow-down"></div>
                </div>-->
                School Name
            </li>
        </ul>
    </div>
    
    <!-- Data Displayed Here -->
    <div class="row-data" id="rankingBoxTab"></div>
    
    <!-- Expand Button Here -->
    <div class="footer-banner" style="background-color:#<?php echo $topcolor?>;cursor:pointer;" onclick="expandDiv(<?php echo $expandID?>);">
        <h6 class="battlefont fs14 txt-center" id="expand-toggle<?php echo $expandID?>"></h6>
        <img src="/images/colleges/expand.png" alt="">
    </div>
</div>
    
<!--    <div class="footer-banner" style="background-color:#<?php echo $topcolor?>">
            <h6 class="battlefont fs14 txt-center" id="expand-toggle22" onclick="expandDiv(22);">dgasdhsai</h6>
            <img src="/images/colleges/expand.png">
        </div>
-->    
	<style>
    #expand-toggle<?php echo $expandID?>:before{
    content:"Expand"; background:none;cursor:pointer;
    }
    #expand-toggle<?php echo $expandID?>.run:before{
    content:"Collapse"; background:none;cursor:pointer;
    }
    </style>                        
@endif

@if($boxfor=='conferencebox')
<style>
.owl-theme .owl-controls .owl-buttons div{
	background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
	border-radius: 30px;
	color: #fff;
	display: inline-block;
	font-size: 12px;
	margin: 5px;
	opacity: 0.5;
	padding: 3px 10px;
}
</style>

<div class="header-banner" style="background-color:#<?php echo $topcolor?>"><?php echo $toptitle?></div>
<div class="banner-content-div" style="background-color:#<?php echo $midcolor?>">
    <div id="msg-carouse2" class="owl-carousel">
        @foreach($rankData as $rankData)
        <div class="item text-center text-white p5 fs16 bold-font" data-rankid="{{$rankData->id}}">{{$rankData->title}}</div>
        @endforeach
    </div>
    
    <div class="rank-div" style="background:#<?php echo $subheadcolor?>; color:#ffffff">
        <ul class="silder_ul fs14">
            <li>
                <!--<div class="arrowup-down">
                    <div class="arrow-up"></div><div class="arrow-down"></div>
                </div>-->
                Plexuss&nbsp;<br>Rank
            </li>
            <li style="border-left:solid 1px; height:32px;border-left-color:#344a3a"></li>
            <li>
                <!--<div class="arrowup-down">
                    <div class="arrow-up"></div><div class="arrow-down"></div>
                </div>-->
                School Name
            </li>
        </ul>
    </div>
    
    <!-- Data Displayed Here -->
    <div class="row-data" id="conferenceboxBoxTab"></div>
    
    <!-- Expand Button Here -->
    <div class="footer-banner" style="background-color:#<?php echo $topcolor?>;cursor:pointer;"  onclick="expandDiv(<?php echo $expandID?>);">
        <h6 class="battlefont fs14 txt-center" id="expand-toggle<?php echo $expandID?>"></h6>
        <img src="/images/colleges/expand.png" alt="">
    </div>
</div>
    
<!--    <div class="footer-banner" style="background-color:#<?php echo $topcolor?>">
        <h6 class="battlefont fs14 txt-center" id="expand-toggle22" onclick="expandDiv(22);">dgasdhsai</h6>
        <img src="/images/colleges/expand.png">
    </div>
-->    
	<style>
    #expand-toggle<?php echo $expandID?>:before{
    content:"Expand"; background:none;
    }
    #expand-toggle<?php echo $expandID?>.run:before{
    content:"Collapse"; background:none;
    }
    </style>                        
@endif
<?php //Doughnut Function Box View Page ?>
@if($boxfor=='doughnutbox')
<div class="row">
    <div class="radial-bdr margin10bottom" style="background-color:{{{$bgcolor}}};min-height: 300px;">
        <div class="text-center row2-box1-title-head">{{{$head_title}}}</div>
        <div class="text-center box-1-chart-donut">
            {{$donutImage}}
        </div>
        <div class="percentile">{{{$footer_title}}}</div>
    </div>
</div>
@endif
<?php //Doughnut Function Box View Page ?>
@if($boxfor=='act-doughnut-box' || $boxfor=='sat-doughnut-box')
<div class="row">
    <div class="margin10bottom" style="background-color:{{{$bgcolor}}};min-height: 354px;">
        <div class="titleAvgBox">{{{$headPercent}}}<br /><br /><span>{{{$headCourse}}}</span></div>
        <br />
        <div class="text-center box-1-chart-donut">
            <br />
            <div style="width:100%; text-align:center;">
                <input class="{{ $boxfor }}" value="{{$content}}">
            </div>
            <div class="large-12 columns pos-arc-values">
                <div class="large-6 small-6 columns">{{$minScore}}</div>
                <div class="large-6 small-6 columns">{{$maxScore}}</div>
            </div>
        </div>
    </div>
</div>
@endif
<?php //Doughnut Function Box View Page ?>
@if($boxfor=='avg-doughnut-box')
<div class="row">
    <div class="margin10bottom" style="background-color:{{{$bgcolor}}};min-height: 354px;">
        <div class="titleAvgBox">{{{$headPercent}}}<br /><br /><span>{{{$headCourse}}}</span></div>
        <br />
        <div class="text-center box-1-chart-donut">
            <br />
            <div style="width:100%; text-align:center;">
                <input class="{{ $boxfor }}" value="{{$content}}">
            </div>
        </div>
    </div>
</div>
@endif
<?php //Ranking Function Box View Page ?>
@if($boxfor=='ranking-box')
<div class="ranking-infobox">
    <div class="rank-box-header-text" style="background:{{{$headbgColor}}}">{{{$headBgText}}}</div>
    <div class="large-12 columns mt10">
        <div class="large-6 columns mt10 text-center">
            <div class="ranked-text">Ranked</div>
            <div class="ranked-number">{{{$rankNumber}}}</div>
        </div>
        <div class="large-6 columns mt10 text-center">{{$collegeLogo}}</div>
    </div>
    <div class="large-12 columns">
        <div class="news-company-name">{{{$collegeName}}}</div>
    </div>
    <div class="large-12 columns">
        <div class="mt10"><a class="full-rank-link" href="{{$collegeLink}}">SEE FULL RANKING</a></div>
    </div>
</div>
@endif
<?php //Salary Box ?>
@if($boxfor=='value-box')
<div class="median-salary-value radial-bdr">
    <div class="salary-head-value" style="background:{{{$headbgColor}}}">{{{$headBgText}}}</div>
    <div class="large-12 small-12 columns salary-structure-list">
        <div class="large-8 small-7 columns salary-content-text-value">SOFTWARE ENGINEER</div>
        <div class="large-4 small-5 columns salary-content-text-value">$87,012</div>
    </div>
    <div class="large-12 columns salary-cross-color salary-structure-list">
        <div class="large-8 small-7 columns salary-content-text-value">MECHANICAL ENGINEER</div>
        <div class="large-4 small-5 columns salary-content-text-value">$70,554</div>
    </div>
    <div class="large-12 columns salary-structure-list">
        <div class="large-8 small-7 columns salary-content-text-value">MARKETING MANAGER</div>
        <div class="large-4 small-5 columns salary-content-text-value">$69,964</div>
    </div>
    <div class="large-12 columns salary-cross-color salary-structure-list">
        <div class="large-8 small-7 columns salary-content-text-value">SOFTWARE ENGINEER</div>
        <div class="large-4 small-5 columns salary-content-text-value">$87,012</div>
    </div>
    <div class="large-12 columns salary-structure-list">
        <div class="large-8 small-7 columns salary-content-text-value">MECHANICAL ENGINEER</div>
        <div class="large-4 small-5 columns salary-content-text-value">$70,554</div>
    </div>
    <div class="large-12 columns salary-cross-color salary-structure-list">
        <div class="large-8 small-7 columns salary-content-text-value">MARKETING MANAGER</div>
        <div class="large-4 small-5 columns salary-content-text-value">$69,964</div>
    </div>
</div>
@endif
<?php //Graduation Rate Function Box ?>
@if($boxfor=='graduation-rate-value')
<div class="graduation-rate-value-panel">
    <div class="graduation-rate-value-header">GRADUATION RATE</div>
    <div class="graduation-img-value">
        <div class="graduation-percentage-value">92%</div>
    </div>
</div>
@endif
<?php //Gender Comparison Box ?>
@if($boxfor=='gender-comparison-box')
<div class="row">
    <div class="large-6 small-6 column bg-men-side text-center">
        <img src="/images/colleges/men-figure-compare.png" alt=""/>
        {{$menContent}}
    </div>
    <div class="large-6 small-6 column  bg-women-side text-center">
        <img src="/images/colleges/women-figure-compare.png" alt=""/>
        {{$womenContent}}
    </div>
</div>
<p class="comparison-title">
<span class="font-14">{{{$headingSmall}}}</span><br />{{{$headingBig}}}
</p>
@endif
<?php //Top Skills learned Function ?>
@if($boxfor=='top_skills_learned')
<div class="wide-4 column comparison-value-panel pos-relative no-padding">
    <div class="large-12 columns no-padding">
        <div class="row">
            <div class="bg-pure-white box-graduation-degree radial-bdr">
                <div class="box-top-content-image">{{$header}}</div>
                <div class="skill_title text-center">{{{$title}}}</div>
                <ul class="skill-content">
                    <li>Project management</li>
                    <li class="skill-content-even">Microsoft Excel</li>
                    <li>Microsoft Office</li>
                    <li class="skill-content-even">Data Analysis</li>
                    <li>Financial Analysis</li>
                    <li class="skill-content-even">Microsoft Word</li>
                    <li>Engineering Design</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
<?php //Popular Salary Box Function ?>
@if($boxfor=='popular_box_avg_salary')
<div class="avg-salary-pop-degree pos-relative no-padding">
    @if($headImage!="")
    <div class="salarybox-headerImage">
        <img src="/images/colleges/{{$headImage}}" alt=""/>
    </div>
    @endif
    <div class="avg-salary-title p10 fs12" style="background:{{$headerbg}}">
        @if($title!="")
        {{{$title}}}
        @else
        <div class="large-4 small-4 columns">TEST</div>
        <div class="large-4 small-4 columns">25TH PERCENTILE</div>
        <div class="large-4 small-4 columns">75TH PERCENTILE</div>
        @endif
    </div>
    <div class="row" style="background-color:{{$contentbgColor}}">
        <div class="large-12 columns salary-structure-list" style="background:{{$evenbgColor}}">
            <div class="large-4 small-4 columns salary-content-text-value">SAT CRITICAL READING</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$sat_read_25}}</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$sat_read_75}}</div>
        </div>
        <div class="large-12 columns salary-structure-list">
            <div class="large-4 small-4 columns salary-content-text-value">SAT MATH</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$sat_math_25}}</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$sat_math_75}}</div>
        </div>
        <div class="large-12 columns salary-structure-list" style="background:{{$evenbgColor}}">
            <div class="large-4 small-4 columns salary-content-text-value">SAT WRITING</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$sat_write_25}}</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$sat_write_75}}</div>
        </div>
        <div class="large-12 columns salary-structure-list">
            <div class="large-4 small-4 columns salary-content-text-value">ACT COMPOSITE</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$act_composite_25}}</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$act_composite_75}}</div>
        </div>
        <div class="large-12 columns salary-structure-list" style="background:{{$evenbgColor}}">
            <div class="large-4 small-4 columns salary-content-text-value">ACT ENGLISH</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$act_english_25}}</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$act_english_75}}</div>
        </div>
        <div class="large-12 columns salary-structure-list" style="background:{{$evenbgColor}}">
            <div class="large-4 small-4 columns salary-content-text-value">ACT MATH</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$act_math_25}}</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$act_math_75}}</div>
        </div>
        <div class="large-12 columns salary-structure-list" style="background:{{$evenbgColor}}">
            <div class="large-4 small-4 columns salary-content-text-value">ACT WRITING</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$act_write_25}}</div>
            <div class="large-4 small-4 columns salary-content-text-value">{{$act_write_75}}</div>
        </div>
    </div>
</div>
@endif
<?php  //Considered Admission Box ?>     
@if($boxfor=='considered_admission_box')
<div class="avg-salary-pop-degree pos-relative no-padding">
    @if($headImage!="")
    <div class="salarybox-headerImage">
        <img src="/images/colleges/{{$headImage}}" alt=""/>
    </div>
    @endif
    
    <div class="avg-salary-title p10" style="background:{{$headerbg}}">{{{$title}}}</div>
    <div class="row" style="background-color:{{$contentbgColor}}">
        {{$content}}
    </div>
</div>
@endif
<?php  //Application Information Box Function ?>
@if($boxfor=='appInfo-box')
<div class="adm-InfoBox" style="background:{{$bgColor}}">
    <div class="adm-infobox-title">{{{$title}}}</div>
    <div class="adm-infobox-ques">OPEN ADMISSIONS:</div>
    <div class="adm-infobox-ans">{{{$firstAns}}}</div>
    
    <div class="adm-infobox-ques">COMMON APPLICATION:</div>
    <div class="adm-infobox-ans">{{{$secondAns}}}</div>
    
    <div class="adm-infobox-ques">APPLICATION FEE:</div>
    <div class="adm-infobox-ans">{{{$thirdAns}}}</div>
    
    <div class="adm-infobox-weblink">
        {{{$webLink}}}
    </div>
</div>
@endif
<?php //Notables Page Box Function ?>
@if($boxfor=='notables-box')
<div class="large-3 columns notable-peoples-box no-padding">
    <div class="notable-head-img">
        <img src="/images/colleges/{{$headImg}}" alt=""/>
    </div>
    <div class="notable-title">{{{$title}}}</div>
    
    <div class="notable-content">
        <div>{{{$name}}}</div>
        <div>{{$dob}}</div>
        <div>Class of {{{$classYear}}}</div>
    </div>
    <div class="notable-about">{{$speciality}}</div>
    <div class="notable-majors">Major : {{{$majors}}}</div>
</div>
@endif
<?php //Tuition Page Box Function ?>
@if($boxfor=='tuition-box')
<div class="tuition-boxes">
    <div class="tuition-head-img" style="background-image:url(/images/colleges/{{$headImg}});background-size:100%;background-repeat:no-repeat">
        <div class="impact-title"></div>
        <div class="tuition-campus-title">{{{$title}}}</div>
        <div class="title-head-icon"><img src="/images/colleges/{{{$icon}}}" alt=""/> </div>
    </div>
    <div class="tuition-content">
        <div class="expenses-header" style="color:{{$InStateTitleColor}}">IN STATE</div>
        <div class="large-12 columns tution-inner-content">
            <div class="row">
                <div class="large-6 small-6 columns no-padding">Tuition</div>
                <div class="large-6 small-6 columns no-padding text-center">${{{$InTuitionValue}}}</div>
            </div>
            <div class="row">
                <div class="large-6 small-6 columns no-padding">Books & Supplies:</div>
                <div class="large-6 small-6 columns no-padding text-center">${{{$InBooksValue}}}</div>
            </div>
            <div class="row">
                <div class="large-6 small-6 columns no-padding">Room & Board: </div>
                <div class="large-6 small-6 columns no-padding text-center">${{{$InRoomValue}}}</div>
            </div>
            <div class="row">
                <div class="large-6 small-6 columns no-padding">Other: </div>
                <div class="large-6 small-6 columns no-padding text-center">${{{$InOtherValue}}}</div>
            </div>
        </div>
    </div>
    <div class="tuition-total-expense" style="color:{{{$totalInExpenseColor}}}">Total Expenses : {{{$inExpenseValue}}}</div>
    <div class="tuition-content">
        <div class="expenses-header" style="color:{{$OutStateTitleColor}}">OUT OF STATE</div>
        <div class="large-12 columns tution-inner-content">
            <div class="row">
                <div class="large-6 small-6 columns no-padding">Tuition</div>
                <div class="large-6 small-6 columns no-padding text-center">${{{$OutTuitionValue}}}</div>
            </div>
            <div class="row">
                <div class="large-6 small-6 columns no-padding">Books & Supplies:</div>
                <div class="large-6 small-6 columns no-padding text-center">${{{$OutBooksValue}}}</div>
            </div>
            <div class="row">
                <div class="large-6 small-6 columns no-padding">Room & Board: </div>
                <div class="large-6 small-6 columns no-padding text-center">${{{$OutRoomValue}}}</div>
            </div>
            <div class="row">
                <div class="large-6 small-6 columns no-padding">Other: </div>
                <div class="large-6 small-6 columns no-padding text-center">${{{$OutOtherValue}}}</div>
            </div>
        </div>
    </div>
    <div class="tuition-total-expense" style="color:{{{$totalOutExpenseColor}}}">Total Expenses : <span>{{{$outExpenseValue}}}</span></div>
</div>
@endif
<?php //financial-box ?>    
@if($boxfor=='financial-box')
<div class="tuition-boxes">
    <div class="tuition-head-img" style="background-image:url(/images/colleges/{{$headImg}});background-size:100%;background-repeat:no-repeat">
        <div class="impact-title"></div>
        <div class="financial-top-title">AVG COST AFTER AID</div>
        <div class="financial-campus-title">{{{$title}}}</div>
        <div class="title-head-icon"><img src="/images/colleges/{{{$icon}}}" alt=""/> </div>
    </div>
    <div class="tuition-content">
        <div class="expenses-header" style="color:{{$InStateTitleColor}}">IN STATE</div>
        <div class="large-12 columns tution-inner-content fs11">
            <div class="row">
                <div class="large-8 small-6 columns no-padding">Total Expense:</div>
                <div class="large-4 small-6 columns no-padding text-center">${{{$InTuitionValue}}}</div>
            </div>
            <div class="row">
                <div class="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                <div class="large-4 small-6 columns no-padding text-center">${{{$InBooksValue}}}</div>
            </div>
            <div class="row">
                <div class="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                <div class="large-4 small-6 columns no-padding text-center">${{{$InRoomValue}}}</div>
            </div>
        </div>
    </div>
    <div class="tuition-total-expense" style="color:{{{$totalInExpenseColor}}}">Out of pocket : {{{$inExpenseValue}}}</div>
    <div class="tuition-content">
        <div class="expenses-header" style="color:{{$OutStateTitleColor}}">OUT OF STATE</div>
        <div class="large-12 columns tution-inner-content fs11">
            <div class="row">
                <div class="large-8 small-6 columns no-padding">Total Expense:</div>
                <div class="large-4 small-6 columns no-padding text-center">${{{$OutTuitionValue}}}</div>
            </div>
            <div class="row">
                <div class="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                <div class="large-4 small-6 columns no-padding text-center">${{{$OutBooksValue}}}</div>
            </div>
            <div class="row">
                <div class="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                <div class="large-4 small-6 columns no-padding text-center">${{{$OutRoomValue}}}</div>
            </div>
        </div>
    </div>
    <div class="tuition-total-expense" style="color:{{{$totalOutExpenseColor}}}">Total Expenses : <span>{{{$outExpenseValue}}}</span></div>
</div>
@endif      
@if($boxfor=='calculator-box')
<div class="calculator-box" style="background-color:{{$bgColor}}">
    <div class="cal-head-text">{{{$headTitle}}}</div>
    <div class="text-center pt20 pb20"><img src="/images/colleges/{{$headImage}}" alt=""/></div>
</div>
@endif
@if($boxfor=='loan-box')
<div class="loan-rate-box" style="background-color:{{$bgColor}}">
    <div class="loan-head-panel" style="background-color:{{$headBgColor}}">{{{$headTitle1}}}<br /><span class="fs20">{{{$headTitle2}}}</span></div>
    <div class="loan-content-panel">
        <div class="row" style="background-color:{{$contentCrossColor}}">
            <div class="large-7 small-6 columns">DEFAULT RATE</div>
            <div class="large-5 small-6 columns no-padding text-center">3.1%</div>
        </div>
        <div class="row">
            <div class="large-7 small-6 columns"># IN DEFAULT</div>
            <div class="large-5 small-6 columns no-padding text-center">198</div>
        </div>
        <div class="row">
            <div class="large-7 small-6 columns"># OUT DEFAULT</div>
            <div class="large-5 small-6 columns no-padding text-center">6,349</div>
        </div>
    </div>
</div>
@endif
@if($boxfor=='campus-dinner-box')
<div class="campus-dine-box">
    <div class="campus-head-text" style="background-image:url(/images/colleges/{{$bgImage}});background-size:100% 100% height: 52px;">{{$headTitle}}</div>
    <div class="content-DineCampus">
        <div>Meal Plan Available</div>
        <div class="font-18">YES</div>
        
        <div>AVG MEALS PER WEEK</div>
        <div class="font-18">19</div>
        
        <div>24-HR DINING?</div>
        <div class="font-18">NO</div>
        
        <div>FRESHMAN MEAL-PLAN REQUIRED?</div>
        <div class="font-18">YES</div>
        
        <div class="font-12">*ALL ON CAMPUS RESIDENT STUDENTS GET A MEAL PLAN</div>
    </div>
</div>
@endif
@if($boxfor=='biglist-box')
<div class="list-box">
    <div class="listbox-head-text" style="background-image:url(/images/colleges/{{$bgImage}}); background-size:100% 100% height: 52px;">
        {{$headTitle}}<br /><span class="font-22">{{$headTitle2}}</span>
    </div>
    <div class="bg-ext-black">
        {{$ListContent}}
    </div>
</div>
@endif
@if($boxfor=='weather-box')
<div class="weather-infoBox">
    <div class="current-place">{{{$currPlace}}}</div>
    <div class="row">
        <div class="large-6 columns weather-current-temp small-text-center">{{$currTemp}}</div>
        <div class="large-6 columns">
            <div class="text-center"><img src="/images/colleges/{{$WeatherImage}}" alt=""/></div>
            <div class="weather-type">{{{$WeatherType}}}</div>
            <br />
        </div>
    </div>
    <div class="row">
        <div class="large-2 small-2 columns next-tempdata">
            <span>MON</span>
            <span>83</span>
            <span>66</span>
        </div>
        <div class="large-2 small-2 columns next-tempdata">
            <span>TUE</span>
            <span>83</span>
            <span>66</span>
        </div>
        <div class="large-2 small-2 columns next-tempdata">
            <span>WED</span>
            <span>83</span>
            <span>66</span>
        </div>
        <div class="large-2 small-2 columns next-tempdata">
            <span>THU</span>
            <span>83</span>
            <span>66</span>
        </div>
        <div class="large-2 small-2 columns next-tempdata">
            <span>FRI</span>
            <span>83</span>
            <span>66</span>
        </div>
        <div class="large-2 small-2 columns">
        </div>
    </div>
</div>
@endif
@if($boxfor=='athletics-box')
<div class="athletics-infoBox">
    <div class="athletics-Boxheader" style="background-image:url(/images/colleges/{{$topImage}});background-repeat:no-repeat;background-size:100% 100%;height:90px;">
        <span class="fs18 text-center">{{{$topTitleSub}}}</span>
        <br />
        <span class="font-26 text-center">{{{$topTitleMain}}}</span>
    </div>
    <div class="athletics-content">
        <div class="row">
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-baseball.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">BaseBall</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$basemen}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$basewomen}}.png" alt=""/>
                </div>
            </div>
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-basketball.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">BasketBall</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$basketmen}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$basketwomen}}.png" alt=""/>
                </div>
            </div>
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-alltrack.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">All track combined</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$all_track_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$all_track_women}}.png" alt=""/>
                </div>
            </div>
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-cross-country.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Cross Country</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$cross_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$cross_women}}.png" alt=""/>
                </div>
            </div>
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-football.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Football</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$football_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$football_women}}.png" alt=""/>
                </div>
            </div>
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-golf.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Golf</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$golf_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$golf_women}}.png" alt=""/>
                </div>
            </div>
            
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-alltrack.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">All Track combined</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$all_track_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$all_track_women}}.png" alt=""/>
                </div>
            </div>
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-gymnastics.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Gymnastics</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$gymnastics_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$gymnastics_women}}.png" alt=""/>
                </div>
            </div>
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-rowing.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Rowing</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$rowing_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$rowing_women}}.png" alt=""/>
                </div>
            </div>
            
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-soccer.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Soccer</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$soccer_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$soccer_women}}.png" alt=""/>
                </div>
            </div>
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-softball.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Softball</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$softball_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$softball_women}}.png" alt=""/>
                </div>
            </div>
            
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-swimming.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Swimming & Diving</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$swimming_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$swimming_women}}.png" alt=""/>
                </div>
            </div>
            
            
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-tennis.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Tennis</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$tennis_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$tennis_women}}.png" alt=""/>
                </div>
            </div>
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-volleyball.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Volleyball</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$volleyball_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$volleyball_women}}.png" alt=""/>
                </div>
            </div>
            <div class="large-12 columns no-padding text-center p10">
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/icon-ath-tennis.png" alt=""/>
                </div>
                <div class="large-4 small-3 columns no-padding ath-gameName">Water Polo</div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$water_polo_men}}.png" alt=""/>
                </div>
                <div class="large-2 small-3 columns no-padding">
                    <img src="/images/colleges/{{$water_polo_women}}.png" alt=""/>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@if($boxfor=='ug-ethnic-box')
<div class="custom-two-col-box-head" style="background-image:url(/images/colleges/{{$headImg}});background-repeat:no-repeat;height: 98px;">
    <span class="font-18">{{$subTitle}}</span><br /> {{$headTitle}}
</div>
<div class="enrollment-twobox-content">
    <div class="row mb20">
        <div class="large-5 columns small-text-center text-right no-padding mt10">
            <ul class="ethnic-ul">
                <li>AMERICAN INDIAN OR ALASK NATIVE</li>
                <li style="color:#05CCD2">ASIAN</li>
                <li style="color:#04A5AC">BLACK OR AFRICAN AMERICAN</li>
                <li style="color:#004358">HISPANIC / LATINO</li>
                <li>NATIVE HAWAIIN /<br /> OTHER PACIFIC ISLANDER</li>
                <li style="color:#9FD939">WHITE</li>
                <li style="color:#1DB151">2 OR MORE RACES</li>
                <li>RACE / ETHNICITY UNKNOWN</li>
                <li style="color:#148D39">NON-RESIDENT-ALIEN</li>
            </ul>
        </div>
        <div class="large-7 columns small-text-center">
            <ul class="ethnic-bar-graph-ul">
                <li class="horizontal-graph-cover">
                    <div class="horizontal-graph" style="width:{{$aianpercent}}%;background:#000"></div>{{$aianpercent}}%
                </li>
                <li class="horizontal-graph-cover">
                    <div class="horizontal-graph" style="width:{{$asiapercent}}%;background:#05CCD2"></div>{{$asiapercent}}%
                </li>
                <li class="horizontal-graph-cover">
                    <div class="horizontal-graph" style="width:{{$bkaapercent}}%;background:#04A5AC"></div>{{$bkaapercent}}%
                </li>
                <li class="horizontal-graph-cover">
                    <div class="horizontal-graph" style="width:{{$hisppercent}}%;background:#004358"></div>{{$hisppercent}}%
                </li>
                <li class="horizontal-graph-cover mt25">
                    <div class="horizontal-graph" style="width:{{$nhpipercent}}%;background:#000000"></div>{{$nhpipercent}}%
                </li>
                <li class="horizontal-graph-cover">
                    <div class="horizontal-graph" style="width:{{$whitpercent}}%;background:#9FD939"></div>{{$whitpercent}}%
                </li>
                <li class="horizontal-graph-cover">
                    <div class="horizontal-graph" style="width:{{$twomorepercent}}%;background:#1DB151"></div>{{$twomorepercent}}%
                </li>
                <li class="horizontal-graph-cover">
                    <div class="horizontal-graph" style="width:{{$unknpercent}}%;background:#000000"></div>{{$unknpercent}}%
                </li>
                <li class="horizontal-graph-cover">
                    <div class="horizontal-graph" style="width:{{$nralpercent}}%;background:#148D39"></div>{{$nralpercent}}%
                </li>
            </ul>
        </div>
    </div>
</div>
@endif
@if($boxfor=='ug-enrollnut-box')
<div class="undergrad-distEducation-headText" style="background:{{$headBg}}">
    <span class="font-14">{{{$smallHeadTitle}}}</span><br />{{{$bigHeadTitle}}}
</div>
<div class="dist-subTitle">{{$headText}}</div>
<div class="text-center"><img src="/images/colleges/{{$graphImage}}" /></div>
<div class="dist-subTitle">{{$footText}}</div>
@endif
@if($boxfor=='threeway-nut-box')
<div class="row col-in-line">
    <div class="large-3 small-3 columns no-padding"><img src="/images/colleges/{{$sideImage}}" alt=""/></div>
    <div class="large-6 small-5 columns"><img src="/images/colleges/{{$midGraph}}" /></div>
    <div class="large-3 small-4 column enroll-box-text no-padding">
        <div class="text-white">{{{$rightContentOne}}}</div>
        <div class="text-gold">{{{$rightContentTwo}}}</div>
        <div class="text-black">{{{$rightContentThree}}}</div>
    </div>
</div>
@endif
@if($boxfor=='programs-box')
<div class="row col-in-line">
    <div class="programBox-headPanel" style="background:{{$bgColor}}">{{{$title}}}</div>
    <div class="programBox-subtitle">{{{$subTitle}}}</div>
    <div class="bg-pure-white p10 programBox-Content">
        {{$progContent}}
    </div>
    <div class="row">
        <div class="large-6 small-6 columns programBox-link"><a href="{{$progLink}}">See all majors</a></div>
        <div class="large-6 small-6 columns text-right"><img src="/images/colleges/{{{$progIcon}}}" alt=""/></div>
    </div>
</div>
@endif
<?php //Scripts Applied ?>    

@if($boxfor=='rankingbox' || $boxfor=='conferencebox')
<script type="text/javascript">
$(document).foundation();
    /*
    $(document).foundation();
    
    $('#comparebox_content_div').toggle();
        setResizeBox();
    })
    $('#expand-toggle-span').click(function(){
    expandDivContent('expand-toggle-span','expand-toggle-div')
    });
    $('#overall-price').click(function()
    {
    expandDivContent('overall-price','overall-price-div')
    });
    $('#average-score').click(function(){
    expandDivContent('average-score','average-score-div')
    });
   
	*/
	<!-- Carousel for Lists-->    
	
	 $("#owl-compare").owlCarousel({
    items :3,
    itemsDesktop : [1199,3],
    itemsDesktopSmall : [979,3],
    itemsMobile : [479,3],
    itemsCustom : [320,3],
    navigation : true, // Show next and prev buttons
    slideSpeed : 300,
    paginationSpeed : 400,
    singleItem:false
    })
        $("#msg-carousel").owlCarousel({
        items :1,
        navigation : true, // Show next and prev buttons
		slideSpeed : 300,
		pagination  :   false,
		paginationSpeed : 400,
		navigationText : ["<li class='nav-arrow navleft-arrow' data-index='0' id='conference-prev'></li>","<li class='nav-arrow navright-arrow' data-index='2' id='conference-next'></li>"],
		singleItem:true,
		rewindNav :false,
		mouseDrag : false,
		touchDrag : false,
		beforeMove: function(e){
		},
		afterInit:function(){
			setArrowAction();
		}
        })
		
		/* Function Set Arrow Function */
		function setArrowAction(){
			$(".navright-arrow,.navleft-arrow").click(function(){
				var index = parseInt($(this).data("index"));
				
				if($(this).hasClass("navleft-arrow")){
					if(index!=0){
						$(this).data("index",index-1);
						$(".navright-arrow").data("index",index+1);						
						getRankingBoxData(index,'<?php echo isset($expandID)?$expandID:'0'?>');
					}
				}
				else{
					if(index!=13){
					$(this).data("index",index+1);								
					$(".navleft-arrow").data("index",index-1);								
					
					getRankingBoxData(index,'<?php echo isset($expandID)?$expandID:'0'?>');
					}
				}								
			})
		}
		 $("#msg-carouse2").owlCarousel({
        items :1,
        navigation : true, // Show next and prev buttons
		slideSpeed : 300,
		pagination  :   false,
		paginationSpeed : 400,
		navigationText : ["<li class='nav-arrow navleft-arrow1' data-index='13' id='conference-prev1'></li>","<li class='nav-arrow navright-arrow1' data-index='15' id='conference-next1'></li>"],
		singleItem:true,
		rewindNav :false,
		mouseDrag : false,
		touchDrag : false,
		beforeMove: function(e){
		},
		afterInit:function(){
			setArrowAction2();
		}
        })
		
		/* Function Set Arrow Function */
		function setArrowAction2(){
			$(".navright-arrow1,.navleft-arrow1").click(function(){
				var index = parseInt($(this).data("index"));
				
				if($(this).hasClass("navleft-arrow1")){
					if(index!=12){
						$(this).data("index",index-1);
						$(".navright-arrow1").data("index",index+1);						
						getRankingBoxData1(index,'<?php echo isset($expandID)?$expandID:'0'?>');
					}
				}
				else{
					if(index!=35){
					$(this).data("index",index+1);								
					$(".navleft-arrow1").data("index",index-1);								
					
					getRankingBoxData1(index,'<?php echo isset($expandID)?$expandID:'0'?>');
					}
				}								
			})
		}
</script>
@endif

