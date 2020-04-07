<?php 
// echo '<pre>';
// print_r($data);
// echo '</pre>';
// exit();
$inMili = '';
if( isset($filters[0]) && !empty($filters[0]) ){
	$filter = $filters[0];
	if (isset($filter)) {
		if (isset($filter['inMilitary'][0]) && $filter['inMilitary'][0] == 'Yes') {
			$inMili = 1;
		}else{
			$inMili = 0;
		}
	}
}
if( isset($filters[1]) && !empty($filters[1]) ){
	$filter = $filters[1];
	if (isset($filter)) {
		if (isset($filter['militaryAffiliation'])) {
			$military = $filter['militaryAffiliation'];
		}
	}
}

?>
<div class="row filter-by-militaryAffilation-container filter-page-section" data-section="militaryAffiliation">
	<div class="column small-12">
		Military Affiliation	
	</div>


	<div class="column small-12">
	
		{{Form::open()}}
		<br />
		<div class="row component" data-component="inMilitary">
			<div class="column small-12 medium-9">
				{{Form::label('inMilitary_filter', 'In Military?', array('class' => 'make_bold'))}}
				{{Form::select('inMilitary', array(''=>'Select...','0'=>'No', '1'=>'Yes'), $inMili, array('id' => 'inMilitary_filter', 'class' => 'select-filter filter-this inMili mili'))}}
			</div>
		</div>

		<div class="row component miliAffili @if(isset($inMili) && $inMili != '1') hide @endif" data-component="militaryAffiliation">
			<div class="column small-12 medium-9">
				{{Form::label('militaryAffiliation_filter', 'Military Affiliation:', array('class' => 'make_bold'))}}
				{{Form::select('militaryAffiliation', $militaryAffiliation, '', array('id' => 'militaryAffiliation_filter', 'class' => 'select-filter filter-this mili'))}}

				@if(isset($military) && count($military) > 0 )
					@foreach( $military as $key => $v )
						<div class="military-values hide" data-val="{{$v}}"></div>
					@endforeach
				@endif	
			</div>
		</div>
		{{Form::close()}}

	</div>
</div>