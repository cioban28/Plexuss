<div class="filter-crumbs-container">
  <ul class="inline-list filter-crumb-list">
    <li>
      <div class="clearfix">
        <div class="left section">{{$section}}: </div>
		 @if(isset($filters))
			@foreach($filters as $filter)
				@if(isset($filter['interested_in_aid']) && $filter['interested_in_aid'][0] == '1' )
					<div class="left tag" data-tag-belongsto="{{$section}}" data-tag-val="interested_in_aid" data-tag-component="{{$section}}" data-elem="interested_in_aid"><span class=""></span>interested_in_aid<span class="remove">x</span></div>		
				@endif
				@if( isset($filter) && !empty($filter[$section]) )
					 @foreach( $filter[$section] as $date )
						<div class="left tag" data-tag-belongsto="{{$section}}" data-tag-val="{{$date}}" data-tag-component="{{$section}}" data-elem="{{$section}}_filter"><span class=""></span>{{$date}}<span class="remove">x</span></div>
					 @endforeach
				@endif
			@endforeach
		 @endif
	 	</div>
    </li>
  </ul>
</div>
<div class="row filter-by-financial-container filter-page-section" data-section="financial">
	<div class="column small-12 large-6">
	
	
			
			<div class="component" data-component="financial">
				<label for="financial_filter">Select a minimum range.</label>
				{{Form::select('financial', $financial_options, null, array('id'=>'financial_filter', 'class' => 'select-filter filter-this')) }}
				@php $interested_in_aid =false; @endphp
				@if( isset($filters))
					@foreach($filters as $filter)
			  			@if(!empty($filter['financial']))
							@foreach( $filter['financial'] as $amt )
								{{Form::hidden('financial_crumbs', $amt)}}
							@endforeach
						@endif
					    @if( isset($filter['interested_in_aid']) && $filter['interested_in_aid'][0]== '1' )
							@php $interested_in_aid =true; @endphp
						
							{{Form::hidden('financial_crumbs', 'interested_in_aid')}}
						@endif
					
				@endforeach
				@endif
			</div>

			<div class="component" data-component="financial">
				{{Form::checkbox('interested_in_aid', 'interested_in_aid',$interested_in_aid, array('id'=>'interested_in_aid', 'class' => 'checkbox-filter filter-this'))}}
				<label for="interested_in_aid">Filter by students who are NOT interested in financial aid, grants, and scholarships</label>
			</div>

		

	</div>

	<div class="column small-12 large-6">
		<div>
			If you would like to target students that are able to contribute financially to their college education, select the minimum amount that they might expect to contribute. These amounts are from the same list we give students to choose from on their profiles.
		</div>
	</div>
</div>