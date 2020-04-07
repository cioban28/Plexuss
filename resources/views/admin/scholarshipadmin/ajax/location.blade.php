<?php //dd($filters); 
?>
<div class="row filter-by-location-container filter-page-section" data-section="location">
	<div class="column small-12 large-6">

		{{Form::open()}}
		@if(empty($filters[0]))

			<!-- filter intl students - show/hide -->
			{{-- <div class="row">
				<div class="column small-12">
					{{Form::checkbox('intl_students', 'id_here', true, array('id'=>'intl_filter', 'class' => 'checkbox-filter filter-this'))}}
					{{Form::label('intl_filter', 'International Students', array('class' => 'make_bold'))}}
				</div>
			</div> --}}

			<!-- filter US students - show/hide -->
			{{-- <div class="row">
				<div class="column small-12">
					{{Form::checkbox('us_students', 'id_here', true, array('id'=>'us_filter', 'class' => 'checkbox-filter filter-this'))}}
					{{Form::label('us_filter', 'USA Students', array('class' => 'make_bold'));}}
				</div>
			</div> --}}
			
			<div class="for-usa-students-only-container">
				<!-- filter by country -->
				<div class="row contains-tags-row component" data-component="country">
					<div class="column small-12">
						<div class="make_bold">Country:</div>
						<div class="hide-for-large-up">
							<p>Choose if you would like to receive students from the USA and/or International students.</p>
						</div>

						{{Form::radio('country', 'all', true, array('id' => 'all_country_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('all_country_filter', 'All')}}
						<br class="show-for-small-only" />

						{{Form::radio('country', 'include', false, array('id' => 'include_country_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('include_country_filter', 'Include')}}
						<br class="show-for-small-only" />

						{{Form::radio('country', 'exclude', false, array('id' => 'exclude_country_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('exclude_country_filter', 'Exclude')}}


						<div class="selection-row">
							<div>You can select multiple options, just click to add</div>

							<div class="select-option-error">
								<small>Must select at least one option</small>
							</div>
							
							{{Form::select('country', $countries, null, array('id'=>'country_filter', 'class' => 'select-filter filter-this')) }}

							<div class="filter-tags-list">
								<!-- country filter tags-->
								<small>No countries selected yet.</small>	
							</div>
						</div>
					</div>
				</div>

				<!-- filter by state -->
				<div class="row contains-tags-row component" data-component="state">
					<div class="column small-12">
						<div class="make_bold">State:</div>
						<div class="hide-for-large-up">
							<p>Choose if you would like to receive students from the USA and/or International students.</p>
						</div>

						{{Form::radio('state', 'all', true, array('id' => 'all_state_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('all_state_filter', 'All')}}
						<br class="show-for-small-only" />

						{{Form::radio('state', 'include', false, array('id' => 'include_state_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('include_state_filter', 'Include')}}
						<br class="show-for-small-only" />

						{{Form::radio('state', 'exclude', false, array('id' => 'exclude_state_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('exclude_state_filter', 'Exclude')}}


						<div class="selection-row">
							<div>You can select multiple options, just click to add</div>

							<div class="select-option-error">
								<small>Must select at least one option</small>
							</div>
							
							{{Form::select('state', $states, null, array('id'=>'state_filter', 'class' => 'select-filter filter-this')) }}

							<div class="filter-tags-list">
								<!-- state filter tags-->
								<small>No states selected yet.</small>	
							</div>
						</div>
					</div>
				</div>

				<!-- filter by city -->
				<div class="row contains-tags-row component" data-component="city">
					<div class="column small-12">
						<div class="make_bold">City:</div>
						<div class="hide-for-large-up">
							<p>If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.</p>
						</div>

						{{Form::radio('city', 'all', true, array('id' => 'all_city_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('all_city_filter', 'All')}}
						<br class="show-for-small-only" />

						{{Form::radio('city', 'include', false, array('id' => 'include_city_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('include_city_filter', 'Include')}}
						<br class="show-for-small-only" />

						{{Form::radio('city', 'exclude', false, array('id' => 'exclude_city_filter', 'class' => 'radio-filter filter-this'))}}
						{{Form::label('exclude_city_filter', 'Exclude')}}

						<div class="no-city-if-all-state-error hidden">
							<small>Not allowed to include or exclude cities if filtering by ALL states.</small>
						</div>

						<div class="selection-row">
							<div>You can select multiple options, just click to add</div>

							<div class="select-option-error">
								<small>Must select at least one option</small>
							</div>

							{{Form::select('city', $cities, null, array('id'=>'city_filter', 'class' => 'select-filter filter-this')) }}

							<div class="filter-tags-list">
								<!-- city filter tags-->
								<small>No cities selected yet.</small>			
							</div>
						</div>
					</div>
				</div>
			</div>

		@else
			
			@foreach( $filters as $filter )

				<!-- filter US students - show/hide -->
				{{-- @if( isset($filter['filter']) && $filter['filter'] == 'us_filter' ) --}}
				{{-- <div class="row">
					<div class="column small-12">
						{{Form::checkbox('us_students', 'id_here', $filter['type'] == 'include' ? true : false, array('id'=>'us_filter', 'class' => 'checkbox-filter filter-this'))}}
						{{Form::label('us_filter', 'USA Students', array('class' => 'make_bold'));}}
					</div>
				</div> --}}

				{{-- @elseif( isset($filter['filter']) && $filter['filter'] == 'intl_filter' ) --}}
				<!-- filter intl students - show/hide -->
				{{-- <div class="row">
					<div class="column small-12">
						{{Form::checkbox('intl_students', 'id_here', $filter['type'] == 'include' ? true : false, array('id'=>'intl_filter', 'class' => 'checkbox-filter filter-this'))}}
						{{Form::label('intl_filter', 'International Students', array('class' => 'make_bold'))}}
					</div>
				</div --}}

				@if( $filter['filter'] == 'country' )
					<?php $country_bool = true ?>
					<div class="for-usa-students-only-container">
						<!-- filter by country -->
						<div class="row contains-tags-row component" data-component="country">
							<div class="column small-12">
								<div class="make_bold">Country:</div>
								<div class="hide-for-large-up">
									<p>Choose if you would like to receive students from the USA and/or International students.</p>
								</div>

								{{Form::radio('country', 'all', $filter['type'] == 'all' ? true : false, array('id' => 'all_country_filter', 'class' => 'radio-filter filter-this'))}}
								{{Form::label('all_country_filter', 'All')}}
								<br class="show-for-small-only" />

								{{Form::radio('country', 'include', $filter['type'] == 'include' ? true : false, array('id' => 'include_country_filter', 'class' => 'radio-filter filter-this'))}}
								{{Form::label('include_country_filter', 'Include')}}
								<br class="show-for-small-only" />

								{{Form::radio('country', 'exclude', $filter['type'] == 'exclude' ? true : false, array('id' => 'exclude_country_filter', 'class' => 'radio-filter filter-this'))}}
								{{Form::label('exclude_country_filter', 'Exclude')}}


								<div class="selection-row">
									<div>You can select multiple options, just click to add</div>

									<div class="select-option-error">
										<small>Must select at least one option</small>
									</div>
									
									{{Form::select('country', $countries, null, array('id'=>'country_filter', 'class' => 'select-filter filter-this')) }}

									<div class="filter-tags-list">
										<!-- country filter tags-->
										@if( $filter['country'] > 0 )
											@foreach( $filter['country'] as $country )
												<span class="advFilter-tag" data-tag-id="{{$country}}" data-type-id="country">
													{{$country}} <span class="remove-tag"> x </span>
												</span>
											@endforeach
										@else
											<small>No countries selected yet.</small>	
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>

				@elseif( $filter['filter'] == 'state' )

						<?php $state_bool = true ?>
						<!-- filter by state -->
						<div class="row contains-tags-row component" data-component="state">
							<div class="column small-12">
								<div class="make_bold">State:</div>
								<div class="hide-for-large-up">
									<p>Choose if you would like to receive students from the USA and/or International students.</p>
								</div>

								{{Form::radio('state', 'all', $filter['type'] == 'all' ? true : false, array('id' => 'all_state_filter', 'class' => 'radio-filter filter-this'))}}
								{{Form::label('all_state_filter', 'All')}}
								<br class="show-for-small-only" />

								{{Form::radio('state', 'include', $filter['type'] == 'include' ? true : false, array('id' => 'include_state_filter', 'class' => 'radio-filter filter-this'))}}
								{{Form::label('include_state_filter', 'Include')}}
								<br class="show-for-small-only" />

								{{Form::radio('state', 'exclude', $filter['type'] == 'exclude' ? true : false, array('id' => 'exclude_state_filter', 'class' => 'radio-filter filter-this'))}}
								{{Form::label('exclude_state_filter', 'Exclude')}}

								<div class="selection-row">
									<div>You can select multiple options, just click to add</div>

									<div class="select-option-error">
										<small>Must select at least one option</small>
									</div>
									
									{{Form::select('state', $states, null, array('id'=>'state_filter', 'class' => 'select-filter filter-this')) }}

									<div class="filter-tags-list">
										<!-- state filter tags-->
										@if( $filter['state'] > 0 )
											@foreach( $filter['state'] as $state )
												<span class="advFilter-tag" data-tag-id="{{$state}}" data-type-id="state">
													{{$state}} <span class="remove-tag"> x </span>
												</span>
											@endforeach
										@else
											<small>No states selected yet.</small>	
										@endif
									</div>
								</div>
							</div>
						</div>

				@elseif( $filter['filter'] == 'city' )

						<?php $city_bool = true ?>

						@if(!isset($state_bool))
							<!-- filter by state -->
							<div class="row contains-tags-row component" data-component="state">
								<div class="column small-12">
									<div class="make_bold">State:</div>
									<div class="hide-for-large-up">
										<p>Choose if you would like to receive students from the USA and/or International students.</p>
									</div>

									{{Form::radio('state', 'all', true, array('id' => 'all_state_filter', 'class' => 'radio-filter filter-this'))}}
									{{Form::label('all_state_filter', 'All')}}
									<br class="show-for-small-only" />

									{{Form::radio('state', 'include', false, array('id' => 'include_state_filter', 'class' => 'radio-filter filter-this'))}}
									{{Form::label('include_state_filter', 'Include')}}
									<br class="show-for-small-only" />

									{{Form::radio('state', 'exclude', false, array('id' => 'exclude_state_filter', 'class' => 'radio-filter filter-this'))}}
									{{Form::label('exclude_state_filter', 'Exclude')}}


									<div class="selection-row">
										<div>You can select multiple options, just click to add</div>

										<div class="select-option-error">
											<small>Must select at least one option</small>
										</div>
										
										{{Form::select('state', $states, null, array('id'=>'state_filter', 'class' => 'select-filter filter-this')) }}

										<div class="filter-tags-list">
											<!-- state filter tags-->
											<small>No states selected yet.</small>	
										</div>
									</div>
								</div>
							</div>

					@endif
							<!-- filter by city -->
							<div class="row contains-tags-row component" data-component="city">
								<div class="column small-12">
									<div class="make_bold">City:</div>
									<div class="hide-for-large-up">
										<p>If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.</p>
									</div>

									{{Form::radio('city', 'all', $filter['type'] == 'all' ? true : false, array('id' => 'all_city_filter', 'class' => 'radio-filter filter-this'))}}
									{{Form::label('all_city_filter', 'All')}}
									<br class="show-for-small-only" />

									{{Form::radio('city', 'include', $filter['type'] == 'include' ? true : false, array('id' => 'include_city_filter', 'class' => 'radio-filter filter-this'))}}
									{{Form::label('include_city_filter', 'Include')}}
									<br class="show-for-small-only" />

									{{Form::radio('city', 'exclude', $filter['type'] == 'exclude' ? true : false, array('id' => 'exclude_city_filter', 'class' => 'radio-filter filter-this'))}}
									{{Form::label('exclude_city_filter', 'Exclude')}}

									<div class="no-city-if-all-state-error hidden">
										<small>Not allowed to include or exclude cities if filtering by ALL states.</small>
									</div>

									<div class="selection-row">
										<div>You can select multiple options, just click to add</div>

										<div class="select-option-error">
											<small>Must select at least one option</small>
										</div>

										{{Form::select('city', $cities, null, array('id'=>'city_filter', 'class' => 'select-filter filter-this')) }}

										<div class="filter-tags-list">
											<!-- city filter tags-->
											@if( $filter['city'] > 0 )
												@foreach( $filter['city'] as $city )
													<span class="advFilter-tag" data-tag-id="{{$city}}" data-type-id="city">
														{{$city}} <span class="remove-tag"> x </span>
													</span>
												@endforeach
											@else
												<small>No cities selected yet.</small>	
											@endif
										</div>
									</div>
								</div>
							</div>
						
				@endif
			@endforeach


			@if( !isset($state_bool) && !isset($city_bool) && !isset($country_bool) )
				
				<div class="for-usa-students-only-container">
					<!-- filter by country -->
					<div class="row contains-tags-row component" data-component="country">
						<div class="column small-12">
							<div class="make_bold">Country:</div>
							<div class="hide-for-large-up">
								<p>Choose if you would like to receive students from the USA and/or International students.</p>
							</div>

							{{Form::radio('country', 'all', true, array('id' => 'all_country_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('all_country_filter', 'All')}}
							<br class="show-for-small-only" />

							{{Form::radio('country', 'include', false, array('id' => 'include_country_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('include_country_filter', 'Include')}}
							<br class="show-for-small-only" />

							{{Form::radio('country', 'exclude', false, array('id' => 'exclude_country_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('exclude_country_filter', 'Exclude')}}


							<div class="selection-row">
								<div>You can select multiple options, just click to add</div>

								<div class="select-option-error">
									<small>Must select at least one option</small>
								</div>
								
								{{Form::select('country', $countries, null, array('id'=>'country_filter', 'class' => 'select-filter filter-this')) }}

								<div class="filter-tags-list">
									<!-- country filter tags-->
									<small>No countries selected yet.</small>	
								</div>
							</div>
						</div>
					</div>

					<!-- filter by state -->
					<div class="row contains-tags-row component" data-component="state">
						<div class="column small-12">
							<div class="make_bold">State:</div>
							<div class="hide-for-large-up">
								<p>Choose if you would like to receive students from the USA and/or International students.</p>
							</div>

							{{Form::radio('state', 'all', true, array('id' => 'all_state_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('all_state_filter', 'All')}}
							<br class="show-for-small-only" />

							{{Form::radio('state', 'include', false, array('id' => 'include_state_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('include_state_filter', 'Include')}}
							<br class="show-for-small-only" />

							{{Form::radio('state', 'exclude', false, array('id' => 'exclude_state_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('exclude_state_filter', 'Exclude')}}


							<div class="selection-row hideOnLoad">
								<div>You can select multiple options, just click to add</div>

								<div class="select-option-error">
									<small>Must select at least one option</small>
								</div>
								
								{{Form::select('state', $states, null, array('id'=>'state_filter', 'class' => 'select-filter filter-this')) }}

								<div class="filter-tags-list">
									<!-- state filter tags-->
									<small>No states selected yet.</small>	
								</div>
							</div>
						</div>
					</div>

					<!-- filter by city -->
					<div class="row contains-tags-row component" data-component="city">
						<div class="column small-12">
							<div class="make_bold">City:</div>
							<div class="hide-for-large-up">
								<p>If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.</p>
							</div>

							{{Form::radio('city', 'all', true, array('id' => 'all_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('all_city_filter', 'All')}}
							<br class="show-for-small-only" />

							{{Form::radio('city', 'include', false, array('id' => 'include_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('include_city_filter', 'Include')}}
							<br class="show-for-small-only" />

							{{Form::radio('city', 'exclude', false, array('id' => 'exclude_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('exclude_city_filter', 'Exclude')}}

							<div class="no-city-if-all-state-error hidden">
								<small>Not allowed to include or exclude cities if filtering by ALL states.</small>
							</div>

							<div class="selection-row hideOnLoad">
								<div>You can select multiple options, just click to add</div>

								<div class="select-option-error">
									<small>Must select at least one option</small>
								</div>

								{{Form::select('city', $cities, null, array('id'=>'city_filter', 'class' => 'select-filter filter-this')) }}

								<div class="filter-tags-list">
									<!-- city filter tags-->
									<small>No cities selected yet.</small>			
								</div>
							</div>
						</div>
					</div>
				</div>

			@elseif( !isset($state_bool) && !isset($city_bool) )
				{{-- <div class="for-usa-students-only-container"> --}}
					<!-- filter by state -->

					<div class="row contains-tags-row component" data-component="state">
						<div class="column small-12">
							<div class="make_bold">State:</div>
							<div class="hide-for-large-up">
								<p>Choose if you would like to receive students from the USA and/or International students.</p>
							</div>

							{{Form::radio('state', 'all', true, array('id' => 'all_state_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('all_state_filter', 'All')}}
							<br class="show-for-small-only" />

							{{Form::radio('state', 'include', false, array('id' => 'include_state_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('include_state_filter', 'Include')}}
							<br class="show-for-small-only" />

							{{Form::radio('state', 'exclude', false, array('id' => 'exclude_state_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('exclude_state_filter', 'Exclude')}}


							<div class="selection-row hideOnLoad">
								<div>You can select multiple options, just click to add</div>

								<div class="select-option-error">
									<small>Must select at least one option</small>
								</div>
								
								{{Form::select('state', $states, null, array('id'=>'state_filter', 'class' => 'select-filter filter-this')) }}

								<div class="filter-tags-list">
									<!-- state filter tags-->
									<small>No states selected yet.</small>	
								</div>
							</div>
						</div>
					</div>

					<!-- filter by city -->
					<div class="row contains-tags-row component" data-component="city">
						<div class="column small-12">
							<div class="make_bold">City:</div>
							<div class="hide-for-large-up">
								<p>If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.</p>
							</div>

							{{Form::radio('city', 'all', true, array('id' => 'all_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('all_city_filter', 'All')}}
							<br class="show-for-small-only" />

							{{Form::radio('city', 'include', false, array('id' => 'include_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('include_city_filter', 'Include')}}
							<br class="show-for-small-only" />

							{{Form::radio('city', 'exclude', false, array('id' => 'exclude_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('exclude_city_filter', 'Exclude')}}

							<div class="no-city-if-all-state-error hidden">
								<small>Not allowed to include or exclude cities if filtering by ALL states.</small>
							</div>

							<div class="selection-row hideOnLoad">
								<div>You can select multiple options, just click to add</div>

								<div class="select-option-error">
									<small>Must select at least one option</small>
								</div>

								{{Form::select('city', $cities, null, array('id'=>'city_filter', 'class' => 'select-filter filter-this')) }}

								<div class="filter-tags-list">
									<!-- city filter tags-->
									<small>No cities selected yet.</small>			
								</div>
							</div>
						</div>
					</div>
				{{-- </div> --}}

			@elseif(!isset($city_bool))
				<!-- filter by city -->

					<div class="row contains-tags-row component" data-component="city">
						<div class="column small-12">
							<div class="make_bold">City:</div>
							<div class="hide-for-large-up">
								<p>If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.</p>
							</div>

							{{Form::radio('city', 'all', true, array('id' => 'all_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('all_city_filter', 'All')}}
							<br class="show-for-small-only" />

							{{Form::radio('city', 'include', false, array('id' => 'include_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('include_city_filter', 'Include')}}
							<br class="show-for-small-only" />

							{{Form::radio('city', 'exclude', false, array('id' => 'exclude_city_filter', 'class' => 'radio-filter filter-this'))}}
							{{Form::label('exclude_city_filter', 'Exclude')}}

							<div class="selection-row">
								<div>You can select multiple options, just click to add</div>

								<div class="select-option-error">
									<small>Must select at least one option</small>
								</div>

								{{Form::select('city', $cities, null, array('id'=>'city_filter', 'class' => 'select-filter filter-this')) }}

								<div class="filter-tags-list">
									<!-- city filter tags-->
									<small>No cities selected yet.</small>			
								</div>
							</div>
						</div>
					</div>
			@endif
			

		@endif
		{{Form::close()}}

	</div>

	<div class="column small-12 large-6 location-filter-desc-col show-for-large-up">
		<div>
			Choose if you would like to receive students from the USA and/or International students.
		</div>

		<div>
			If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.
		</div>
	</div>
</div>