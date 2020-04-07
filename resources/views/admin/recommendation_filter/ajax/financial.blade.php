<div class="row filter-by-financial-container filter-page-section" data-section="financial">
	<div class="column small-12 large-6">
	
		{{Form::open()}}

			<div class="component" data-component="financial">
				<label for="financial_filter">Select a minimum range.</label>
				{{Form::select('financial', $financial_options, null, array('id'=>'financial_filter', 'class' => 'select-filter filter-this')) }}
				@if( isset($filters))
					@if(!empty($filters[0]['financial']))
						@foreach( $filters[0]['financial'] as $amt )
							{{Form::hidden('financial_crumbs', $amt)}}
						@endforeach
					@endif
					@if(array_key_exists(1, $filters))
						@if( $filters[1]['interested_in_aid'][0] == '1' )
							{{Form::hidden('financial_crumbs', 'interested_in_aid')}}
						@endif
					@endif
				@endif
			</div>

			<div class="component" data-component="financial">
				{{Form::checkbox('interested_in_aid', 'interested_in_aid', false, array('id'=>'interested_in_aid', 'class' => 'checkbox-filter filter-this'))}}
				<label for="interested_in_aid">Filter by students who are NOT interested in financial aid, grants, and scholarships</label>
			</div>

		{{Form::close()}}

	</div>

	<div class="column small-12 large-6">
		<div>
			If you would like to target students that are able to contribute financially to their college education, select the minimum amount that they might expect to contribute. These amounts are from the same list we give students to choose from on their profiles.
		</div>
	</div>
</div>