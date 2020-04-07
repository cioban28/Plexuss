@php
	$gpa_m = null;
	$gpa_M = null;
	$sat_m = null;
	$sat_M = null;
	$act_m = null;
	$act_M = null;
	$toefl_m = null;
	$toefl_M = null;
	$ielts_m = null;
	$ielts_M = null;
@endphp
<div class="filter-crumbs-container">
  <ul class="inline-list filter-crumb-list">
    <li>
      <div class="clearfix">
        <div class="left section">{{$section}}: </div>
        	 @if( isset($filters) && !empty($filters[0][$section]) )
			 @foreach( $filters[0][$section] as $date )
				<div class="left tag" data-tag-belongsto="{{$section}}" data-tag-val="{{$date}}" data-tag-component="{{$section}}" data-elem="{{$section}}_filter"><span class=""></span>{{$date}}<span class="remove">x</span></div>
      		 @endforeach
			@endif
	 	</div>
    </li>
  </ul>
</div>
<div class="row filter-by-scores-container filter-page-section" data-section="scores">
	<div class="column small-12 large-6">
	
	
			<!-- filter by age -->
			<div class="row collapse">
				<div class="column small-12 filter-instructions">
					We will recommend students to you within the score ranges you set here.
				</div>
			</div>
			<br />
			@if( isset($filters) && !empty($filters) )

				@foreach( $filters as $scores )
					
						@if($scores['filter'] == 'gpaMin_filter' && !isset($gpa_m))
							@php $gpa_m = $scores[$scores['filter']][0]; @endphp
						@elseif($scores['filter'] == 'gpaMax_filter' && !isset($gpa_M))
							@php $gpa_M = $scores[$scores['filter']][0]; @endphp
						@elseif ($scores['filter'] == 'satMin_filter' && !isset($sat_m))
							@php $sat_m = $scores[$scores['filter']][0]; @endphp
						@elseif ($scores['filter'] == 'satMax_filter' && !isset($sat_M))
							@php $sat_M = $scores[$scores['filter']][0]; @endphp
						@elseif ($scores['filter'] == 'actMin_filter' && !isset($act_m))
							@php $act_m = $scores[$scores['filter']][0]; @endphp
						@elseif ($scores['filter'] == 'actMax_filter' && !isset($act_M))
							@php $act_M = $scores[$scores['filter']][0]; @endphp
						@elseif ($scores['filter'] == 'toeflMin_filter' && !isset($toefl_m))
							@php $toefl_m = $scores[$scores['filter']][0]; @endphp
						@elseif ($scores['filter'] == 'toeflMax_filter' && !isset($toefl_M))
							@php $toefl_M = $scores[$scores['filter']][0]; @endphp
						@elseif ($scores['filter'] == 'ieltsMin_filter' && !isset($ielts_m))
							@php $ielts_m = $scores[$scores['filter']][0]; @endphp
						@elseif ($scores['filter'] == 'ieltsMax_filter' && !isset($ielts_M))
							@php $ielts_M = $scores[$scores['filter']][0]; @endphp
						@endif
				@endforeach

				@if( $gpa_m != null )
				<!-- filter by GPA -->
				<div class="row collapse component" data-component="Hs gpa">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'GPA:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('gpa_min', $gpa_m, array('id' => 'gpaMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'gpa', 'data-scores' => 'min'))}} 
						<small id="gpaMin_filter_error" class="error" style="display: none;">Incorrect values. Make sure Min GPA isn't greater than Max GPA.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@else
				<div class="row collapse component" data-component="Hs gpa">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'GPA:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('gpa_min', null, array('id' => 'gpaMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'gpa', 'data-scores' => 'min'))}} 
						<small id="gpaMin_filter_error" class="error" style="display: none;">Incorrect values. Make sure Min GPA isn't greater than Max GPA.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@endif

				@if( $gpa_M != null )
					<div class="column small-3 medium-2 large-3 end">
						{{Form::text('gpa_max', $gpa_M, array('id' => 'gpaMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 4.0', 'pattern' => 'gpa', 'data-scores' => 'max'))}}
						<small id="gpaMax_filter_error" class="error" style="display: none;">Incorrect values. Ex: 2.5 - 4.0</small>
					</div>
					<div class="column small-12 error-msg">
					</div>
				</div>
				@else
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('gpa_max', null, array('id' => 'gpaMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 4.0', 'pattern' => 'gpa', 'data-scores' => 'max'))}}
						<small id="gpaMax_filter_error" class="error" style="display: none;">Incorrect values. Ex: 2.5 - 4.0</small>
					</div>
					<div class="column small-12 error-msg">
					</div>
				</div>
				@endif

				@if( $sat_m != null )
				<!-- filter by SAT -->
				<div class="row collapse component" data-component="SAT">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'SAT:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('sat_min', $sat_m, array('id' => 'satMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 600', 'pattern' => 'sat', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min SAT isn't greater than Max SAT.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@else
				<!-- filter by SAT -->
				<div class="row collapse component" data-component="SAT">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'SAT:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('sat_min', null, array('id' => 'satMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 600', 'pattern' => 'sat', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min SAT isn't greater than Max SAT.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@endif

				@if( $sat_M != null )
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('sat_max', $sat_M, array('id' => 'satMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max:2400', 'pattern' => 'sat', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 1200 - 2200</small>
					</div>
				</div>
				@else
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('sat_max', null, array('id' => 'satMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max:2400', 'pattern' => 'sat', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 1200 - 2200</small>
					</div>
				</div>
				@endif

				@if( $act_m != null )
				<!-- filter by ACT -->
				<div class="row collapse component" data-component="ACT">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'ACT:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('act_min', $act_m, array('id' => 'actMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'act', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min ACT isn't greater than Max ACT.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@else
				<!-- filter by ACT -->
				<div class="row collapse component" data-component="ACT">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'ACT:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('act_min', null, array('id' => 'actMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'act', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min ACT isn't greater than Max ACT.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@endif

				@if( $act_M != null )
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('act_max', $act_M, array('id' => 'actMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 36', 'pattern' => 'act', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 20 - 32</small>
					</div>
				</div>
				@else
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('act_max', null, array('id' => 'actMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 36', 'pattern' => 'act', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 20 - 32</small>
					</div>
				</div>
				@endif

				@if( $toefl_m != null )
				<!-- filter by TOEFL -->
				<div class="row collapse component" data-component="TOEFL">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'TOEFL:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('toefl_min', $toefl_m, array('id' => 'toeflMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'toefl', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min TOEFL isn't greater than Max TOEFL.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@else
				<!-- filter by TOEFL -->
				<div class="row collapse component" data-component="TOEFL">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'TOEFL:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('toefl_min', null, array('id' => 'toeflMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'toefl', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min TOEFL isn't greater than Max TOEFL.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@endif

				@if( $toefl_M != null )
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('toefl_max', $toefl_M, array('id' => 'toeflMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 120', 'pattern' => 'toefl', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 50 - 100</small>
					</div>
				</div>
				@else
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('toefl_max', null, array('id' => 'toeflMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 120', 'pattern' => 'toefl', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 50 - 100</small>
					</div>
				</div>
				@endif

				@if( $ielts_m != null )
				<!-- filter by IELTS -->
				<div class="row collapse component" data-component="IELTS">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'IELTS:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('ielts_min', $ielts_m, array('id' => 'ieltsMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'ielts', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min IELTS isn't greater than Max IELTS.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@else
				<!-- filter by IELTS -->
				<div class="row collapse component" data-component="IELTS">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'IELTS:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('ielts_min', null, array('id' => 'ieltsMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'ielts', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min IELTS isn't greater than Max IELTS.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
				@endif

				@if( $ielts_M != null )
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('ielts_max', $ielts_M, array('id' => 'ieltsMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 9', 'pattern' => 'ielts', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 5 - 9</small>
					</div>
				</div>
				@else
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('ielts_max', null, array('id' => 'ieltsMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 9', 'pattern' => 'ielts', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 5 - 9</small>
					</div>
				</div>
				@endif

			@else

				<!-- filter by GPA -->
				<div class="row collapse component" data-component="Hs gpa">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'GPA:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('gpa_min', null, array('id' => 'gpaMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'gpa', 'data-scores' => 'min'))}} 
						<small class="error" style="display: none;">Incorrect values. Make sure Min GPA isn't greater than Max GPA.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('gpa_max', null, array('id' => 'gpaMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 4.0', 'pattern' => 'gpa', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 2.5 - 4.0</small>
					</div>
					<div class="column small-12 error-msg">
					</div>
				</div>

				<!-- filter by SAT -->
				<div class="row collapse component" data-component="SAT">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'SAT:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('sat_min', null, array('id' => 'satMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 600', 'pattern' => 'sat', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min SAT isn't greater than Max SAT.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('sat_max', null, array('id' => 'satMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 2400', 'pattern' => 'sat', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 1200 - 2200</small>
					</div>
				</div>

				<!-- filter by ACT -->
				<div class="row collapse component" data-component="ACT">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'ACT:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('act_min', null, array('id' => 'actMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'act', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min ACT isn't greater than Max ACT.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('act_max', null, array('id' => 'actMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 36', 'pattern' => 'act', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 20 - 32</small>
					</div>
				</div>

				<!-- filter by TOEFL -->
				<div class="row collapse component" data-component="TOEFL">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'TOEFL:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('toefl_min', null, array('id' => 'toeflMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'toefl', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min TOEFL isn't greater than Max TOEFL.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('toefl_max', null, array('id' => 'toeflMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 120', 'pattern' => 'toefl', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 50 - 100</small>
					</div>
				</div>

				<!-- filter by IELTS -->
				<div class="row collapse component" data-component="IELTS">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'IELTS:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-4 medium-1 large-4">
						{{Form::text('ielts_min', null, array('id' => 'ieltsMin_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Min: 0', 'pattern' => 'ielts', 'data-scores' => 'min'))}}
						<small class="error" style="display: none;">Incorrect values. Make sure Min IELTS isn't greater than Max IELTS.</small>
					</div>
					<div class="column small-3 medium-2 text-center scores-desc">
						to
					</div>
					<div class="column small-4 medium-1 large-4 end">
						{{Form::text('ielts_max', null, array('id' => 'ieltsMax_filter', 'class' => 'text-filter scores filter-this', 'placeholder' => 'Max: 9', 'pattern' => 'ielts', 'data-scores' => 'max'))}}
						<small class="error" style="display: none;">Incorrect values. Ex: 5 - 9</small>
					</div>
				</div>

			@endif


			<div class="row collapse minMaxError hide">
				<div class="column small-12">
					Invalid input(s). Check to make sure none of the MIN values are greater than the MAX values.
				</div>
			</div>
		

		<script type="text/javascript">
			$(document)
			.foundation({
				abide : {
				  patterns: {
				    gpa: /^(([0-3]){1}\.([0-9]){1,2}|4\.(0){1,2}|([0-4]){1})$/,
					toefl: /^([0-9]?[0-9]|[1][0-1][0-9]|12[0])$/,
					ielts: /^[0-9]?(.[0-9]{1,2})?$/,
					sat: /^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/,
					act: /^([1-9]|[1-2][0-9]|[3][0-6])$/,
				  }
				}
			});
		</script>

	</div>
</div>