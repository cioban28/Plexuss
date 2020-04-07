<div class="row filter-by-typeofschool-container filter-page-section" data-section="typeofschool">
	<div class="column small-12 large-6">
	
		{{Form::open()}}

			<div class="component" data-component="typeofschool">
				{{Form::radio('typeofschool', 'both', true, array('id' => 'both_typeofschool', 'class' => 'radio-filter filter-all filter-this'))}}
				{{Form::label('both_typeofschool', 'Both')}}
				<br class="show-for-small-only" />

				{{Form::radio('typeofschool', 'online_only', false, array('id' => 'online_only_typeofschool', 'class' => 'radio-filter filter-this'))}}
				{{Form::label('online_only_typeofschool', 'Online Only')}}
				<br class="show-for-small-only" />

				{{Form::radio('typeofschool', 'campus_only', false, array('id' => 'campus_only_typeofschool', 'class' => 'radio-filter filter-this'))}}
				{{Form::label('campus_only_typeofschool', 'Campus Only')}}

				@if( isset($filters) && !empty($filters[0]['interested_school_type']) )
					@foreach( $filters[0]['interested_school_type'] as $amt )
						{{Form::hidden('typeofschool_crumb', $amt)}}
					@endforeach
				@endif
			</div>
			
		{{Form::close()}}

	</div>

	<div class="column small-12 large-6">
		<div>
			By default, we will recommend students who are interested in both online and on-campus education. If you'd like to limit your recommendations to only online or on-campus, select one of these options.
		</div>
	</div>
</div>
