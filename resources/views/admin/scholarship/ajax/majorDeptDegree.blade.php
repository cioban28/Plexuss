<div class="filter-crumbs-container">
  <ul class="inline-list filter-crumb-list">
    <li>
      <div class="clearfix">
        <div class="left section">{{$section}}: </div>
        	 @if(isset($filters))
				@foreach($filters as $filter)
					 @if( isset($filter) && !empty($filter['department']) )
					 @foreach( $filter['department'] as $date )
						<div class="left tag" data-tag-belongsto="{{$section}}" data-tag-val="{{$date}}" data-tag-component="{{$section}}" data-elem="{{$section}}_filter"><span class=""></span>{{$date}}<span class="remove">x</span></div>
					@endforeach
					@endif
			@endforeach
		 @endif
	 	</div>
    </li>
  </ul>
</div>
<div class="row filter-by-major-container filter-page-section" data-section="major">

	
	<div class="column small-12 large-6">
	

		
		
			<!-- filter by specific department -->
			<div class="row contains-tags-row component" data-component="department">
				<div class="column small-12">
					<div class="hide-for-large-up">
						<p>If your school is targeting students within a specific major, select the desired majors you'd like to include or exclude. You can select more than one item. These majors are from the same list we give students to choose from on their profiles.</p>
					</div>
					<div class="make_bold">Department: </div>
					<div>Choose one option</div>
					@php $amd ="all"; @endphp
					@if(isset($filters[0]) && !empty($filters[0]))
						@foreach( $filters as $major )
							@if($major['type'] == 'include' )
								@php $amd ="include"; @endphp
							@elseif($major['type'] == 'exclude')
								@php $amd ="exclude"; @endphp
							@endif
							
							{{Form::hidden('dept_in_ex', $amd)}}
							@if( isset($major['department']) )
								@foreach( $major['department'] as $mdd )
									{{Form::hidden('mdd', $mdd)}}
								@endforeach
							@endif
						@endforeach
					@endif

					{{Form::radio('department', 'all', $amd == 'all' ? true : false, array('id' => 'all_department_filter', 'class' => 'radio-filter filter-all filter-this'))}}
					{{Form::label('all_department_filter', 'All departments')}}
					<br class="show-for-small-only" />

					{{Form::radio('department', 'include', $amd == 'include' ? true : false, array('id' => 'include_department_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('include_department_filter', 'Include')}}
					<br class="show-for-small-only" />

					{{Form::radio('department', 'exclude', $amd == 'exclude' ? true : false, array('id' => 'exclude_department_filter', 'class' => 'radio-filter filter-this'))}}
					{{Form::label('exclude_department_filter', 'Exclude')}}

					<div class="selection-row">
						<div>You can select multiple options, just click to add</div>

						<div class="select-option-error">
							<small>Must select at least one option</small>
						</div>

						<select name="specificDepartment_filter" id="specificDepartment_filter" class="select-filter filter-this">
							@foreach($departments as $key => $val) 
								@if($key == "")
									<option value="" data-department-id="{{$key}}" selected="selected" disabled="disabled">{{$val}}</option>
								@else
									<option value="{{$val}}" data-department-id="{{$key}}">{{$val}}</option>
								@endif
							@endforeach
						</select>

					</div>
					
				</div>
			</div>
			
		

	</div>

	<div class="column small-12 large-6 show-for-large-up">
		If your school is targeting students within a specific major, select the desired majors you'd like to include or exclude. You can select more than one item. These majors are from the same list we give students to choose from on their profiles.
	</div>

	<div class="column small-12 dept-list">
		
	</div>

	
</div>