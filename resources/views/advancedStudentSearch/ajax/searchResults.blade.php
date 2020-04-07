<?php
if (!isset($searchResults)){
	$searchResults =array();
}
// echo "<pre>";
// print_r($search_input);
// echo "</pre>";
// exit();
// dd($data);
?>

<div class="savedFilters hide" data-saved-filters='{{$search_input or "null"}}'></div>
<div class="hasResults hide" data-last-results="{{$has_searchResults or 'null'}}" data-total-results="{{$total_results_count or 0}}" data-viewing-count="{{$total_viewing_count or 0}}" data-college="{{$user_id or 'null'}}"></div>
<!-- remove applied colleges confirmation modal -->
<div style='display: none;' id='remove-applied-college-confirm' title='Remove College'>
  	<p>Are you sure you want to remove this student from this college?</p>
  	<p class='removed-college-name'></p>
  	<p class='removed-student-name'></p>
  	<p class='removed-student-status'></p>
</div>
@foreach($searchResults as $k)
<div class="row item resulting-students inquirie_row" data-id={{$k['hashed_user_id']}} data-uid="{{$k['user_id']}}" data-hashedid='{{$k['hashed_user_id'] or ""}}'>
	<!--<div class="column small-1 select-col">
		{{Form::checkbox('student-result', 'student-result', false, array('class'=>'student-result'))}}
	</div>-->
	<div class="column small-1 arrow-col toggle-profile-btn">
		<div class="arrow"></div>
	</div>
	<div class="column small-6 medium-3 large-2 name">
		@if($paid_customer == true)
			{{$k['fname']. ' ' .$k['lname']}}
		@else
			{{$k['fname']}}
		@endif
	</div>
	<div class="column small-2 medium-1 gpa">
		{{$k['gpa'] or 'N/A'}}
	</div>
	<div class="column medium-1 sat show-for-large-up">
		{{$k['sat_score'] or 'N/A'}}
	</div>
	<div class="column medium-1 act show-for-large-up">
		{{$k['act_composite'] or 'N/A'}}
	</div>
	<div class="column medium-4 large-3 degree show-for-medium-up">
		{{$k['major'] or 'N/A'}}
	</div>
	<div class="column medium-1 country hide-for-small-only">
		@if (isset($k['country_code']) && $k['country_code'] != 'N/A')
		<div data-tooltip aria-haspopup="true" title="{{$k['country_name']}}" class="has-tip tip-top flag flag-{{ strtolower($k['country_code'])}}"></div>
		<span class="countryName">{{ $k['country_name'] or ''}} </span>
		@else
		<div>N/A</div>
		@endif
	</div>
	<div class="column medium-1 docs show-for-large-up hide-for-small-only">
		<ul class="uploaded-docs-list">
			@if(isset($k['prescreen_interview']) && $k['prescreen_interview'] == true)
			<li class="has-tip uploaded-docs-thumb prescreen_interview" data-tooltip aria-haspopup="true" title="Prescreen Interview"></li>
			@endif
			@if($k['transcript'] == true)
			<li class="has-tip uploaded-docs-thumb transcript" data-tooltip aria-haspopup="true" title="Transcript"></li>
			@endif
			@if($k['toefl'] == true)
			<li class="has-tip uploaded-docs-thumb toefl" data-tooltip aria-haspopup="true" title="TOEFL"></li>
			@endif
			@if($k['ielts'] == true)
			<li class="has-tip uploaded-docs-thumb ielts" data-tooltip aria-haspopup="true" title="IELTS"></li>
			@endif
			@if($k['financial'] == true)
			<li class="has-tip uploaded-docs-thumb financial" data-tooltip aria-haspopup="true" title="Financial Document"></li>
			@endif
			@if($k['resume'] == true)
			<li class="has-tip uploaded-docs-thumb resume" data-tooltip aria-haspopup="true" title="Resume / CV"></li>
			@endif
			@if($k['other'] == true)
			<li class="has-tip uploaded-docs-thumb other" data-tooltip aria-haspopup="true" title="Other"></li>
			@endif
			@if($k['essay'] == true)
			<li class="has-tip uploaded-docs-thumb essay" data-tooltip aria-haspopup="true" title="Essay"></li>
			@endif
			@if($k['passport'] == true)
			<li class="has-tip uploaded-docs-thumb passport" data-tooltip aria-haspopup="true" title="Passport"></li>
			@endif
		  	@if($k['transcript'] == false && 
		  	  	$k['toefl'] == false && 
		  	  	$k['ielts'] == false && 
		  	  	$k['financial'] == false &&
		  	  	$k['resume'] == false &&
		  	  	$k['other'] == false &&
		  	  	$k['essay'] == false &&
		  	  	$k['passport'] == false &&
		  	  	$k['prescreen_interview'] == false)

		  	  <li></li>
		  @endif
		</ul>	
	</div>

	<div class="column small-2 large-1 recruit" data-hashed-id="{{$k['hashed_user_id']}}">
		@if($k['already_recruited'] == true)
			<div class="recruit-student-btn already-recruited text-center">Recruited!</div>
		@else
			<a data-hashed-id="{{$k['hashed_user_id']}}">
				<div class="recruit-student-btn recruit-me text-center">Recruit</div>
			</a>
		@endif
	</div>
	<!--<div class="column small-2 large-1 prof-img hide-for-small-only">
		<a href=""><img src="" alt=""> <span>Profile</span></a>
	</div>-->

	<div class="column small-12 full-profile-container hidden ajax-init">

	</div> 
</div>
@endforeach

<script type="text/javascript">
	$(document).ready(function() {
		$(document).foundation('interchange', 'reflow');
		Plex.studentSearch.initNewOwl();
	});
	
</script>
