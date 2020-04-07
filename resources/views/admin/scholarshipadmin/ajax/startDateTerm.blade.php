<div class="row filter-by-startDateTerm-container filter-page-section" data-section="startDateTerm">
	<div class="column small-12 large-6">
	
		{{Form::open()}}

			<div class="component" data-component="startDateTerm">
				<label for="startDateTerm_filter">You can select multiple options, just click to add.</label>
				{{Form::select('startDateTerm', $dates, null, array('id'=>'startDateTerm_filter', 'class' => 'select-filter filter-this')) }}	
				@if( isset($filters) && !empty($filters[0]['startDateTerm']) )
					@foreach( $filters[0]['startDateTerm'] as $date )
						{{Form::hidden('date_crumbs', $date)}}
					@endforeach
				@endif
			</div>
		

		{{Form::close()}}

	</div>

	<div class="column small-12 large-6">
		<div>
			Each student on Plexuss tell us when they intend to start school. Select the term(s) you want students you're targeting to apply for.
		</div>
	</div>
</div>