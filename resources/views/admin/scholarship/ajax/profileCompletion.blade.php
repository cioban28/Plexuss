@php $profileCompletion = null;@endphp
@if( isset($filters) && !empty($filters) )
	@foreach($filters as $filter)
		@if (isset($filter['profileCompletion']))
			@php $profileCompletion = $filter['profileCompletion'][0]; @endphp
		@endif
	@endforeach
@endif

<div class="filter-crumbs-container">
  <ul class="inline-list filter-crumb-list">
    <li>
      <div class="clearfix">
        <div class="left section">{{$section}}: </div>
        	 @if( isset($profileCompletion) && $profileCompletion != null)
				<div class="left tag" data-tag-belongsto="{{$section}}" data-tag-val="{{$profileCompletion}}" data-tag-component="{{$section}}" data-elem="{{$section}}_filter"><span class=""></span>{{$profileCompletion}}%<span class="remove">x</span></div>
      		 @endif
	 	</div>
    </li>
  </ul>
</div>
<div class="row filter-by-profileCompletion-container filter-page-section" data-section="profileCompletion">

	<div class="column small-12 large-6">
	
		
		<br />
		<div class="row component" data-component="profileCompletion">
			<div class="column small-12 medium-9">

				{{Form::label('profileCompletion_filter', 'Profile Completion:', array('class' => 'make_bold'))}}
				{{Form::select('profileCompletion', array('' => 'Select...', '10' => '10%', '20' => '20%', '30' => '30%', '40' => '40%', '50' => '50%', '60' => '60%', '70' => '70%', '80' => '80%', '90' => '90%', '100' => '100%'), $profileCompletion, array('id' => 'profileCompletion_filter', 'class' => 'select-filter filter-this isProfComp'))}}
			</div>
		</div>
		
	</div>



	<div class="column small-12 large-6">
		
		<br />
			Select the minimum Profile Completion percentage that a student must reach to be considered a viable candidate for recruitment.
		
	</div>	

</div>