<div class="row filter-by-educationLevel-container filter-page-section" data-section="educationLevel">
	<div class="column small-12 large-6">
	
		{{Form::open()}}

		@foreach( $filters as $filters )
			<div class="hide-for-large-up">
				By default, we show you students at all education levels, but if you are interested in students who have completed some college, you can select "College" here.
			</div>

			<!-- filter by high school students -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($filters) )
					{{Form::checkbox('hs_users', 'hsUsers', $filters['educationLevel'][0] == 'hsUsers_filter' ? true : false, array('id'=>'hsUsers_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('hs_users', 'hsUsers', true, array('id'=>'hsUsers_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('hsUsers_filter', 'High school')}}
				</div>
			</div>

			<!-- filter by college students -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($filters) )
					{{Form::checkbox('college_users', 'collegeUsers', $filters['educationLevel'][0] == 'collegeUsers_filter' ? true : false, array('id'=>'collegeUsers_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('college_users', 'collegeUsers', true, array('id'=>'collegeUsers_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('collegeUsers_filter', 'College')}}
					<br />
					<small>(Students who have completed some level of college)</small>
				</div>
			</div>
		@endforeach

		<div class="row collapse minMaxError">
			<div class="column small-12">
				At least ONE checkbox must be checked.
			</div>
		</div>

		{{Form::close()}}

	</div>

	<div class="column small-12 large-6 show-for-large-up">
		<div>
			By default, we show you students at all education levels, but if you are interested in students who have completed some college, you can select "College" here.
		</div>
	</div>
</div>