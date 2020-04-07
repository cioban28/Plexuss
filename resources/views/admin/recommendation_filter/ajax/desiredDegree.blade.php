<div class="row filter-by-desiredDegree-container filter-page-section" data-section="desiredDegree">
	<div class="column small-12 large-6">

		<div class="row hide-for-large-up">
			<div class="column small-12">
				Students tell us their desired degree on their profile. You can choose which degrees you would like recommendations for.		
			</div>
		</div>
		{{Form::open()}}
			<!-- filter by degree -->
			{{form::checkbox('desireddegree', 'selectAll', false, array('id'=>'select_all_degrees', 'class' => ''))}}
			{{form::label('select_all_degrees', 'Select All')}}

			@foreach( $filters as $degree )

			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'Certificate Programs', !isset($degree['desiredDegree']['Certificate Programs']), array('id'=>'1_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'Certificate Programs', true, array('id'=>'1_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('1_filter', 'Certificate Programs')}}
				</div>
			</div>
			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'Associate\'s Degree', !isset($degree['desiredDegree']['Associate\'s Degree']), array('id'=>'2_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'Associate\'s Degree', true, array('id'=>'2_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('2_filter', 'Associate\'s Degree')}}
				</div>
			</div>
			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'Bachelor\'s Degree', !isset($degree['desiredDegree']['Bachelor\'s Degree']), array('id'=>'3_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'Bachelor\'s Degree', true, array('id'=>'3_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('3_filter', 'Bachelor\'s Degree')}}
				</div>
			</div>
			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'Master\'s Degree', !isset($degree['desiredDegree']['Master\'s Degree']), array('id'=>'4_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'Master\'s Degree', true, array('id'=>'4_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('4_filter', 'Master\'s Degree')}}
				</div>
			</div>
			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'PHD / Doctorate', !isset($degree['desiredDegree']['PHD / Doctorate']), array('id'=>'5_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'PHD / Doctorate', true, array('id'=>'5_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('5_filter', 'PHD / Doctorate')}}
				</div>
			</div>
			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'Undecided', !isset($degree['desiredDegree']['Undecided']), array('id'=>'6_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'Undecided', true, array('id'=>'6_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('6_filter', 'Undecided')}}
				</div>
			</div>
			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'Diploma', !isset($degree['desiredDegree']['Diploma']), array('id'=>'7_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'Diploma', true, array('id'=>'7_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('7_filter', 'Diploma')}}
				</div>
			</div>
			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'Other', !isset($degree['desiredDegree']['Other']), array('id'=>'8_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'Other', true, array('id'=>'8_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('8_filter', 'Other')}}
				</div>
			</div>
			<div class="row">
				<div class="column small-12">
					@if( !empty($degree) )
					{{form::checkbox('desireddegree', 'Juris Doctor', !isset($degree['desiredDegree']['Juris Doctor']), array('id'=>'9_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{form::checkbox('desireddegree', 'Juris Doctor', true, array('id'=>'9_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{form::label('9_filter', 'Juris Doctor')}}
				</div>
			</div>

			@endforeach

		{{Form::close()}}

	</div>

	<div class="column small-12 large-6 show-for-large-up">
		Students tell us their desired degree on their profile. You can choose which degrees you would like recommendations for.		
	</div>
</div>