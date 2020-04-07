<div class="row filter-by-demographic-container filter-page-section" data-section="demographic">
	<div class="column small-12 large-6">
	
		{{Form::open(array('url' => '', 'data-abide' => 'ajax'))}}


		@if( isset($filters) && !empty($filters) )
		
				<!-- filter by age -->
				<div class="row component" data-component="Age">
					<div class="column small-3 medium-2 scores-desc">
						{{Form::label('none', 'Age:', array('class' => 'make_bold'))}}
					</div>
					<div class="column small-3 medium-2">
						{{Form::text('age', isset($filters['ageMin_filter']) ? $filters['ageMin_filter'] : null, array('id' => 'ageMin_filter', 'class' => 'text-filter filter-this age', 'placeholder' => 'Min', 'data-scores' => 'min', 'pattern' => 'age'))}}
						<small class="error">Incorrect values. Make sure Min age isn't greater than Max age.</small>
					</div>
					<div class="column small-3 medium-1 text-center scores-desc">
						to
					</div>

					<div class="column small-3 medium-2 end">
						{{Form::text('age', isset($filters['ageMax_filter']) ? $filters['ageMax_filter'] : null, array('id' => 'ageMax_filter', 'class' => 'text-filter filter-this age', 'placeholder' => 'Max', 'data-scores' => 'max', 'pattern' => 'age'))}}
						<small class="error">Incorrect values. Ex: 16 - 30.</small>
					</div>
					<div class="column small-12 hide-for-large-up demo-age-desc">
						Choose an age range for students you are interested in. Students must be at least 13 years old to create an account on Plexuss.
					</div>
				</div>
				
				<div class="row collapse minMaxError">
					<div class="column small-12">
						Invalid input(s). Check to make sure none of the MIN values are greater than the MAX values.
					</div>
				</div>

				<!-- filter by gender -->
				<div class="row contains-tags-row component" data-component="Gender">
					<div class="column small-12">
						{{Form::label('none', 'Gender', array('class' => 'make_bold'))}}

						{{Form::radio('gender', 'all',isset($filters['gender']) ? false: true, array('id' => 'all_gender_filter', 'class' => 'radio-filter filter-this gender'))}}
						{{Form::label('all_gender_filter', 'All')}}
						<br class="show-for-small-only" />

						{{Form::radio('gender', 'include_gender', isset($filters['gender']) && $filters['gender'] == 'male'  ? true: false, array('id' => 'male_only_filter', 'class' => 'radio-filter filter-this gender'))}}
						{{Form::label('male_only_filter', 'Males Only')}}
						<br class="show-for-small-only" />

						{{Form::radio('gender', 'exclude_gender', isset($filters['gender']) && $filters['gender'] == 'female'  ? true: false, array('id' => 'female_only_filter', 'class' => 'radio-filter filter-this gender'))}}
						{{Form::label('female_only_filter', 'Females Only')}}
					</div>
				</div>

				<!-- filter by ethnicity -->
				<div class="row contains-tags-row component" data-component="Ethnicity">
					<div class="column small-12">
						{{Form::label('none', 'Ethnicity', array('class' => 'make_bold'))}}
						<div class="hide-for-large-up">
							<p>By default we will recommend you all ethnicities, but you can select which ethnicities you would like to give priority to include or exclude from your daily recommendations.</p>
						</div>

						{{Form::radio('ethnicity', 'all', isset($filters['include_eth_filter_type']) ? false: true, array('id' => 'all_eth_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('all_eth_filter', 'All Ethnicities')}}
						<br class="show-for-small-only" />

						{{Form::radio('ethnicity', 'include', isset($filters['include_eth_filter_type']) && $filters['include_eth_filter_type'] == 'include'  ? true: false, array('id' => 'include_eth_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('include_eth_filter', 'Include')}}
						<br class="show-for-small-only" />

						{{Form::radio('ethnicity', 'exclude', isset($filters['include_eth_filter_type']) && $filters['include_eth_filter_type'] == 'exclude'  ? true: false, array('id' => 'exclude_eth_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('exclude_eth_filter', 'Exclude')}}

						<div class="selection-row @if(!isset($filters['include_eth_filter_type'])) hideOnLoad @endif ">
							<div>You can select multiple options, just click to add</div>

							<div class="select-option-error">
								<small>Must select at least one option</small>
							</div>

							{{Form::select('ethnicity', $ethnicities, 'asian', array('id' => 'ethnicity_filter', 'class' => 'select-filter filter-this'))}}

							<!-- filter tags list -->
							<div class="filter-tags-list">
								<!-- major filter tags-->
								@if(isset($filters['include_eth_filter']) && $filters['include_eth_filter'] > 0 )
									@foreach( $filters['include_eth_filter'] as $ethnicity )
										<span class="advFilter-tag" data-tag-id="{{$ethnicity}}" data-type-id="ethnicity">
											{{$ethnicity}} <span class="remove-tag"> x </span>
										</span>
									@endforeach
								@else
									<small>No ethnicity selected yet.</small>	
								@endif	
							</div>
						</div>

					</div>
				</div>

				<!-- filter by Religion -->
				<div class="row contains-tags-row component" data-component="Religion">
					<div class="column small-12">
						{{Form::label('none', 'Religion', array('class' => 'make_bold'))}}
						<div class="hide-for-large-up">
							<p>By default we will recommend you all religions, but you can select which religion you would like to give priority to include or exclude from your daily recommendations.</p>
						</div>

						{{Form::radio('religion', 'all', isset($filters['include_rgs_filter_type']) ? false: true, array('id' => 'all_rgs_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('all_rgs_filter', 'All Religions')}}
						<br class="show-for-small-only" />

						{{Form::radio('religion', 'include', isset($filters['include_rgs_filter_type']) && $filters['include_rgs_filter_type'] == 'include'  ? true: false, array('id' => 'include_rgs_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('include_rgs_filter', 'Include')}}
						<br class="show-for-small-only" />

						{{Form::radio('religion', 'exclude', isset($filters['include_rgs_filter_type']) && $filters['include_rgs_filter_type'] == 'exclude'  ? true: false, array('id' => 'exclude_rgs_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('exclude_rgs_filter', 'Exclude')}}

						<div class="selection-row @if(!isset($filters['include_rgs_filter_type'])) hideOnLoad @endif ">
							<div>You can select multiple options, just click to add</div>

							<div class="select-option-error">
								<small>Must select at least one option</small>
							</div>

							{{Form::select('religion', $religions, '', array('id' => 'religion_filter', 'class' => 'select-filter filter-this'))}}

							<!-- filter tags list -->
							<div class="filter-tags-list">
								<!-- major filter tags-->
								@if(isset($filters['include_rgs_filter']) && $filters['include_rgs_filter'] > 0 )
									@foreach( $filters['include_rgs_filter'] as $religion )
										<span class="advFilter-tag" data-tag-id="{{$religion}}" data-type-id="religion">
											{{$religion}} <span class="remove-tag"> x </span>
										</span>
									@endforeach
								@else
									<small>No religion selected yet.</small>	
								@endif	
							</div>
						</div>

					</div>
				</div>
		@else

			<!-- filter by age -->
			<div class="row component" data-component="Age">
				<div class="column small-3 medium-2 scores-desc">
					{{Form::label('none', 'Age:', array('class' => 'make_bold'))}}
				</div>
				<div class="column small-3 medium-2">
					{{Form::text('age', null, array('id' => 'ageMin_filter', 'class' => 'text-filter filter-this age', 'placeholder' => 'Min', 'data-scores' => 'min', 'pattern' => 'age'))}}
					<small class="error">Incorrect values. Make sure Min age isn't greater than Max age.</small>
				</div>
				<div class="column small-3 medium-1 text-center scores-desc">
					to
				</div>
				<div class="column small-3 medium-2 end">
					{{Form::text('age', null, array('id' => 'ageMax_filter', 'class' => 'text-filter filter-this age', 'placeholder' => 'Max', 'data-scores' => 'max', 'pattern' => 'age'))}}
					<small class="error">Incorrect values. Ex: 16 - 30.</small>
				</div>
				<div class="column small-12 hide-for-large-up demo-age-desc">
					Choose an age range for students you are interested in. Students must be at least 13 years old to create an account on Plexuss.
				</div>
			</div>

			<div class="row collapse minMaxError">
				<div class="column small-12">
					Invalid input(s). Check to make sure none of the MIN values are greater than the MAX values.
				</div>
			</div>

			<!-- filter by gender -->
			<div class="row contains-tags-row component" data-component="Gender">
				<div class="column small-12">
					{{Form::label('none', 'Gender', array('class' => 'make_bold'))}}

					{{Form::radio('gender', 'all', true, array('id' => 'all_gender_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('all_gender_filter', 'All')}}
					<br class="show-for-small-only" />

					{{Form::radio('gender', 'include_gender', false, array('id' => 'male_only_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('male_only_filter', 'Males Only')}}
					<br class="show-for-small-only" />

					{{Form::radio('gender', 'exclude_gender', false, array('id' => 'female_only_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('female_only_filter', 'Females Only')}}
				</div>
			</div>

			<!-- filter by ethnicity -->
			<div class="row contains-tags-row component" data-component="Ethnicty">
				<div class="column small-12">
					{{Form::label('none', 'Ethnicity', array('class' => 'make_bold'))}}
					<div class="hide-for-large-up">
						<p>By default we will recommend you all ethnicities, but you can select which ethnicities you would like to give priority to include or exclude from your daily recommendations.</p>
					</div>

					{{Form::radio('ethnicity', 'all', true, array('id' => 'all_eth_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('all_eth_filter', 'All Ethnicities')}}
					<br class="show-for-small-only" />

					{{Form::radio('ethnicity', 'include', false, array('id' => 'include_eth_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('include_eth_filter', 'Include')}}
					<br class="show-for-small-only" />

					{{Form::radio('ethnicity', 'exclude', false, array('id' => 'exclude_eth_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('exclude_eth_filter', 'Exclude')}}

					<div class="selection-row @if( empty($filter) ) hideOnLoad @endif">
						<div>You can select multiple options, just click to add</div>

						<div class="select-option-error">
							<small>Must select at least one option</small>
						</div>

						{{Form::select('ethnicity', $ethnicities, 'asian', array('id' => 'ethnicity_filter', 'class' => 'select-filter filter-this'))}}

						<!-- filter tags list -->
						<div class="filter-tags-list">
							<!-- major filter tags-->
							<small>No ethnicity selected yet.</small>	
						</div>
					</div>

				</div>
			</div>

			<!-- filter by religion -->
			<div class="row contains-tags-row component" data-component="Religion">
				<div class="column small-12">
					{{Form::label('none', 'Religion', array('class' => 'make_bold'))}}
					<div class="hide-for-large-up">
						<p>By default we will recommend you all religions, but you can select which religion you would like to give priority to include or exclude from your daily recommendations.</p>
					</div>

					{{Form::radio('religion', 'all', true, array('id' => 'all_rgs_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('all_rgs_filter', 'All Religions')}}
					<br class="show-for-small-only" />

					{{Form::radio('religion', 'include', false, array('id' => 'include_rgs_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('include_rgs_filter', 'Include')}}
					<br class="show-for-small-only" />

					{{Form::radio('religion', 'exclude', false, array('id' => 'exclude_rgs_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('exclude_rgs_filter', 'Exclude')}}

					<div class="selection-row @if( empty($filter) ) hideOnLoad @endif">
						<div>You can select multiple options, just click to add</div>

						<div class="select-option-error">
							<small>Must select at least one option</small>
						</div>

						{{Form::select('religion', $religions, '', array('id' => 'religion_filter', 'class' => 'select-filter filter-this'))}}

						<!-- filter tags list -->
						<div class="filter-tags-list">
							<!-- major filter tags-->
							<small>No religion selected yet.</small>	
						</div>
					</div>

				</div>
			</div>

		@endif

		{{Form::close()}}

	</div>

	<div class="column small-12 large-6 show-for-large-up">
		<div>
			Choose an age range for students you are interested in. Students must be at least 13 years old to create an account on Plexuss.
		</div>
		<div>
			By default we will recommend you all ethnicities, but you can select which ethnicities you would like to give priority to include or exclude from your daily recommendations.
		</div>
	</div>

</div>

<script type="text/javascript">
	$(document)
	.foundation({
		abide : {
		  patterns: {
		    age: /^([1-9]?\d|100)$/,
		  }
		}
	});
</script>