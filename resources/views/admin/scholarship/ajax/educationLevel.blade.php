<div class="filter-crumbs-container">
  <ul class="inline-list filter-crumb-list">
    <li>
      <div class="clearfix">
        <div class="left section">{{$section}}: </div>
			@php $val=''; $val1 = '';$val2 = ''; @endphp
        	@if(isset($filters))
			 	@foreach( $filters as $filter)
					@if(isset($filter[$section]))
						@php $filt = $filter[$section];@endphp
			 			@foreach( $filter[$section] as $date )
			 				
			 				@if($date=="collegeUsers_filter")
								@php $val = "College"; @endphp
								@php $val1 = "College"; @endphp
							@endif
							@if($date=="hsUsers_filter")
								@php $val = "High school"; @endphp
								@php $val2 = "High school"; @endphp
							@endif
			 				<div class="left tag" data-tag-belongsto="{{$section}}" data-tag-val="{{$date}}" data-tag-component="{{$section}}" data-elem="{{$section}}_filter"><span class=""></span>{{$val}}<span class="remove">x</span></div>
      					@endforeach
					@endif
				@endforeach
			@endif
	 	</div>
    </li>
  </ul>
</div>
<div class="row filter-by-educationLevel-container filter-page-section" data-section="educationLevel">
	<div class="column small-12 large-6">
		
			<div class="hide-for-large-up">
				By default, we show you students at all education levels, but if you are interested in students who have completed some college, you can select "College" here.
			</div>

			<!-- filter by high school students -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($filters) )
					{{Form::checkbox('hs_users', 'hsUsers', $val2 == 'High school' ? true : false, array('id'=>'hsUsers_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('hs_users', true, array('id'=>'hsUsers_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('hsUsers_filter', 'High school')}}
				</div>
			</div>

			<!-- filter by college students -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($filters) )
					{{Form::checkbox('college_users', 'collegeUsers', $val1 == 'College' ? true : false, array('id'=>'collegeUsers_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('college_users', 'collegeUsers', true, array('id'=>'collegeUsers_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('collegeUsers_filter', 'College')}}
					<br />
					<small>(Students who have completed some level of college)</small>
				</div>
			</div>
		

		<div class="row collapse minMaxError">
			<div class="column small-12">
				At least ONE checkbox must be checked.
			</div>
		</div>

		

	</div>

	<div class="column small-12 large-6 show-for-large-up">
		<div>
			By default, we show you students at all education levels, but if you are interested in students who have completed some college, you can select "College" here.
		</div>
	</div>
</div>