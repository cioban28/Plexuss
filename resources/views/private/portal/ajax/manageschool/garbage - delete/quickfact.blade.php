
<div class="quick-link-div">
@if($tab=='manageschool' && $menu=='menu3')
<div class="row">
    <div class="small-6 medium-4 column no-padding">
        <div class="row">
            <div class="small-8 column no-padding c79 fs10"><strong>SAT SCORE</strong><br />             
                <span class="fs10 f-normal">25th - 75th Percentile <br /> {{$listdata->sat_read_75 + $listdata->sat_write_75 + $listdata->sat_math_75}} </span>     
            </div>
            <div class="small-4 column no-padding c79 fs9"><!--<img src="/images/portal/score.png">-->            
                <div style="display:inline-block; margin-left:-15px; vertical-align: middle;">
                <input class="averge_sat_score" id="averge_sat_score" value="{{$listdata->sat_read_75 + $listdata->sat_write_75 + $listdata->sat_math_75}}"  style="display:none;">
                </div>
            </div>
        </div>
    </div>
    <div class="small-6 medium-4 column no-padding">
        <div class="row">
            <div class="small-8 column no-padding c79 fs10"><strong>ACT SCORE</strong><br />
                <span class="fs10 f-normal">25th - 75th Percentile <br />{{$listdata->act_composite_75}} </span>                
            </div>
            <div class="small-4 column no-padding c79 fs9"><!--<img src="/images/portal/score.png">-->
            	 <div style="display:inline-block; margin-left:-15px;  vertical-align: middle;">
                <input class="averge_sat_score" id="averge_sat_score" value="{{$listdata->act_composite_75}}"  style="display:none;">
                </div>
            </div>
        </div>
    </div>
    <div class="small-12 medium-4 column no-padding">
        <div class="row">
            <div class="small-4 column no-padding c05 fs10 f-bold">COMPARED TO <br /> YOUR SCORES
                
            </div>
            <div class="small-8 column no-padding c79 fs9">
                <div style="display:inline-block;  vertical-align: middle;">
                	<input class="compare_your_score" value="1100"  style="display:none;">
                </div>
                
                <div style="display:inline-block; margin-left:2px;  vertical-align: middle;">
                	<input class="compare_your_score" value="1250"  style="display:none;">
                </div>
           <!-- <img src="/images/portal/score-blue.png">-->
            </div>
        </div>
    </div>
 </div><?php */?>
 
<div class="row">
    <div class="small-12 medium-4 column no-padding">
		<span class="c-blue fs12">You are receiving this recommendation because you chose Syracuse University. </span>
    </div>
    
    <div class="small-12 medium-8 column no-padding">
      	<span class="c-blue fs12 f-bold">{{$listdata->school_name}}</span><br />
        <span class="c79 fs12">a) A higher rank (85th)</span><br />
        <span class="c79 fs12">b) Lower Tuition</span><br />
        <span class="c79 fs12">c) Your score put you in the top 75% percentile of their past year’s enrollment    
     class</span><br />
        
    </div>
 </div>
  
@elseif($tab=='managescholarships' && $menu=='menu1') 
<div class="small-12">
    <div class="row">
        <div class="small-3 column no-padding c79 fs10 f-bold">RENEWABLE:</div>
        <div class="small-9 column no-padding c79 fs10">No</div>
    </div>
    
    <div class="row">
        <div class="small-3 column no-padding c79 fs10 f-bold">OFFERED BY:</div>
        <div class="small-9 column no-padding c79 fs10">
        Akash Kuruvilla Memorial Scholarship Fund Inc.<br />
        P.O. Box 140900<br />
        Gainesville, FL 32614<br />
        </div>
    </div>
    
    <div class="row">
        <div class="small-3 column no-padding c79 fs10 f-bold">ELIGIBILITY:</div>
        <div class="small-9 column no-padding c79 fs10">
            High School Seniors through College Juniors may apply.
            Applicants must be entering or current full-time college students at an accredited U.S. four-year college or university. 
            They must demonstrate academic achievement, leadership, integrity and excellence in diversity. 
            Selection is based on character, financial need and the applicant’s potential to impact his or her community.
        </div>
    </div>
    
    <div class="row">
        <div class="small-3 column no-padding c79 fs10 f-bold">DESCRIPTION:</div>
        <div class="small-9 column no-padding c79 fs10">
           Candy canes chocolate cake cookie gummies tootsie roll. Oat cake gingerbread muffin cookie tootsie roll fruitcake applicake. Brownie sweet caramels marzipan dessert tiramisu soufflé. Pudding bear claw apple pie powder oat cake icing jelly-o. Jujubes dragée chocolate cake fruitcake bear claw brownie caramels oat cake. Chocolate jelly-o oat cake candy icing carrot cake sugar plum. Macaroon toffee marshmallow. 
        </div>
    </div>    
</div> 
@else 
<div class="row">
    <div class="small-12  medium-4 column no-padding">
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-bold">Admission Deadline:</div>
            <div class="small-4 column no-padding c79 fs10">{{$listdata->deadline}}</div>
        </div>
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-bold">Acceptance Rate:</div>
            <div class="small-4 column no-padding c79 fs10">
            	<?php 
					if($listdata->applicants_total!='' &&  $listdata->applicants_total>0)
					{
						 echo $acception_rate=number_format(($listdata->admissions_total/$listdata->applicants_total)*100,2).'%';
					}
					else
					{
						echo $acception_rate=(0).'%';
					}
				?>
            
                    @if($listdata->applicants_total!='' &&  $listdata->applicants_total>0)
                   
                    @endif
            </div>
        </div>
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-bold">Student-Teacher Ratio:</div>
            <div class="small-4 column no-padding c79 fs10">{{$listdata->student_faculty_ratio}}</div>
        </div>        
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-bold">In-state Tuition:</div>
            <div class="small-4 column no-padding c79 fs10">${{number_format($listdata->tuition_avg_in_state_ftug,0,"-",",");}}</div>
        </div>
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-bold">Out-of-state Tuition:</div>
            <div class="small-4 column no-padding c79 fs10">${{number_format($listdata->tuition_avg_out_state_ftug,0,"-",",");}} </div>
        </div>
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-bold">Student Body Size:</div>
            <div class="small-4 column no-padding c79 fs10">{{$listdata->student_body_total}}</div>
        </div>
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-bold">Athletics:</div>
            <div class="small-4 column no-padding c79 fs10">{{$listdata->class_name}}</div>
        </div>
        
    </div>
    <div class="small-12  medium-4 column no-padding">
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-blod"><strong>SAT SCORE:</strong><br />             
                <span class="fs10 f-normal">25th - 75th Percentile <br /> {{$listdata->sat_read_75 + $listdata->sat_write_75 + $listdata->sat_math_75}} </span>     
            </div>
            <div class="small-4 column no-padding c79 fs9"><!--<img src="/images/portal/score.png">-->            
                <div style="display:inline-block; margin-left:-15px; vertical-align: middle;">
                <input class="averge_sat_score" id="averge_sat_score" value="{{$listdata->sat_read_75 + $listdata->sat_write_75 + $listdata->sat_math_75}}"  style="display:none;">
                </div>
            </div>
        </div>
    </div>
    <div class="small-12 medium-4 column no-padding">
        <div class="row">
            <div class="small-8 column no-padding c79 fs10 f-blod"><strong>ACT SCORE:</strong><br />
                <span class="fs10 f-normal">25th - 75th Percentile <br />{{$listdata->act_composite_75}} </span>                
            </div>
            <div class="small-4 column no-padding c79 fs9"><!--<img src="/images/portal/score.png">-->
            	 <div style="display:inline-block; margin-left:-15px;  vertical-align: middle;">
                <input class="averge_sat_score" id="averge_sat_score" value="{{$listdata->act_composite_75}}"  style="display:none;">
                </div>
            </div>
        </div>
    </div>
 </div>
@endif
 
<div class="row cursor tr-close-div"  align="center" style="background:#f5f5f5">
    <span class="expand-toggle-span" style="margin:0px; left:288px; margin-top:7px">&nbsp;</span>
    <span class="fs10 pl20 f-bold">Close</span>
</div>

</div>

<script type="text/javascript">
$(document).ready(function(e) {
		$('.tr-close-div').click(function(){
			var obj = $(this).parents('tr');		
			var tr_id =$(obj).attr('id');			
			$('#'+tr_id).hide();
		})

	   $(".compare_your_score").knob({
		min : 0, 
		max : 2000, 
		angleOffset : 0, 
		angleArc : 360, 
		stopper : true, 
		readOnly : true, 
		cursor : false,  
		lineCap : 'butt', 
		thickness : '0.30', 
		width :65,
		height :65, 
		displayInput : false, 
		displayPrevious : true, 
		fgColor : '#05CED3', 
		inputColor : '#93D63B', 
		bgColor : '#DDDDDD'
	});

	   $(".averge_sat_score").knob({
		min : 0, 
		max : 2000, 
		angleOffset : 0, 
		angleArc : 360, 
		stopper : true, 
		readOnly : true, 
		cursor : false,  
		lineCap : 'butt', 
		thickness : '0.30', 
		width :65,
		height :65, 
		displayInput : false, 
		displayPrevious : true, 
		fgColor : '#797979', 
		inputColor : '#93D63B', 
		bgColor : '#DDDDDD'
	}); 
});
</script>
