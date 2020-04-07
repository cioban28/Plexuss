<div class='row'>
	<div class='small-6  medium-6  large-6 column infoColumn'>
		<div class='row'>
			<div class='small-12 column small-text-center box1title'>
				Admission Deadline:
			</div>
			<div class='small-12 column small-text-center'>
				{{ $deadline or 'N/A'}}
			</div>
		</div>
		<div class='row'>
			<div class='small-12 column small-text-center box1title'>
				Acceptance Rate:
			</div>
			<div class='small-12 column small-text-center'>
				{{$percent_admitted or 'N/A'}}%
			</div>
		</div>
		<div class='row'>
			<div class='small-12 column small-text-center box1title'>
				Student-Teacher Ratio:
			</div>
			<div class='small-12 column small-text-center'>
				{{ $student_faculty_ratio or 'N/A'}}:1
			</div>
		</div>
		<div class='row'>
			<div class='small-12 column small-text-center box1title'>
				In-state Tuition:
			</div>
			<div class='small-12 column small-text-center'>
				${{$inStateTuition or 'N/A'}}
			</div>
		</div>
	</div>

	<div class='small-6  medium-6  large-6 column infoColumn'>
		<div class='row'>
			<div class='small-12 column small-text-center box1title'>
				Out-of-state Tuition:
			</div>
			<div class='small-12 column small-text-center'>
				${{$outStateTuition or 'N/A'}}
			</div>
		</div>
		<div class='row'>
			<div class='small-12 column small-text-center box1title'>
				Student Body Size:
			</div>
			<div class='small-12 column small-text-center'>
				{{$student_body_total or 'N/A'}}
			</div>
		</div>
		<div class='row'>
			<div class='small-12 column small-text-center box1title'>
				Athletics:
			</div>
			<div class='small-12 column small-text-center'>
				{{$athletic or 'N/A'}}
			</div>
		</div>
	</div>
</div>

<!-- circle area -->
<div class='row show-for-medium-up circleArea'>
	<div class='small-6 medium-6 large-6 column infoColumn'>
		<div class='row text-center'>
			<div class='small-12 column box1title'>
				SAT SCORE
			</div>
			<div class='small-12 column'>
				75th Percentile
			</div>
			<div class='small-12 column circleBox'>
				@if ( isset($sat_total) && $sat_total != '' &&  $sat_total != 0  )
					<input class="" id="indicator_alert_{{ $id }}" value="{{$sat_total or 'N/A'}}"/>
					<div class='row' style='position: relative; top: -13px;'>
                        <div class="small-6 columns text-right2">600</div>
                    	<div class="small-6 columns text-left2">2400</div>
                    </div>
				@else
					N/A
				@endif
			</div>
		</div>
	</div>
	<div class='small-6 medium-6 large-6 column infoColumn'>
		<div class='row text-center'>
			<div class='small-12 column box1title'>
				ACT SCORE
			</div>
			<div class='small-12 column'>
				75th Percentile
			</div>
			<div class='small-12 column circleBox'>
				@if ( isset($act) && $act != '' &&  $act != 0   )
					<input class="" id="indicator_alert2_{{ $id }}" value="{{$act or 'N/A'}}"/>
					<div class='row' style='position: relative; top: -13px;'>
                        <div class="small-6 columns text-right2">1</div>
                    	<div class="small-6 columns text-left2">36</div>
                    </div>
				@else
					N/A
				@endif
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">

$("#indicator_alert_{{ $id }}").knob({
	min : 600, 
	max : 2400, 
	angleOffset : 235, 
	angleArc : 250, 
	stopper : true, 
	readOnly : true, 
	cursor : false,  
	lineCap : 'butt', 
	thickness : '0.3', 
	width : 70,
	height : 70, 
	displayInput : true, 
	displayPrevious : true, 
	fgColor : '#959697', 
	inputColor : '#000000', 
	bgColor : '#DDDDDD'
});





$("#indicator_alert2_{{ $id }}").knob({
	min : 1, 
	max : 36, 
	angleOffset : 235, 
	angleArc : 250, 
	stopper : true, 
	readOnly : true, 
	cursor : false,  
	lineCap : 'butt', 
	thickness : '0.3', 
	width : 70,
	height : 70, 
	displayInput : true, 
	displayPrevious : true, 
	fgColor : '#959697', 
	inputColor : '#000000', 
	bgColor : '#DDDDDD'
});


$(".act-doughnut-box").knob({
	min : 1, 
	max : 36, 
	angleOffset : 235, 
	angleArc : 250, 
	stopper : true, 
	readOnly : true, 
	cursor : false,  
	lineCap : 'butt', 
	thickness : '0.3', 
	width : 175, 
	displayInput : true, 
	displayPrevious : true, 
	fgColor : '#000000', 
	inputColor : '#FFFFFF', 
	font : 'Arial', 
	fontWeight : 'bold', 
	bgColor : '#FFFFFF',
	});

</script>