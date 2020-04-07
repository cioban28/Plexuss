<div class="filter-crumbs-container">
  <ul class="inline-list filter-crumb-list">
    <li>
      <div class="clearfix">
        <div class="left section">{{$section}}: </div>
        	@php $amt=2; 
			 $vall = "Both";
			 @endphp
			 @if(isset($filters))
				@foreach($filters as $filter)
					@if(isset($filter['interested_school_type']))
						@foreach($filter['interested_school_type'] as $amt )
							@if($amt == 0)
								@php
								$amt = 0;
								$vall = "Campus Only";
								@endphp
							@elseif($amt == 1)
								@php $vall = "Online Only";
								$amt = 1;
								@endphp
							@endif
							
							<div class="left tag" data-tag-belongsto="{{$section}}" data-tag-val="{{$amt}}" data-tag-component="{{$section}}" data-elem="{{$amt}}_{{$section}}"><span class=""></span>{{$vall}}<span class="remove">x</span></div>
							
						@endforeach
					@endif
				@endforeach
			@endif
			
			
			
		</div>
    </li>
  </ul>
</div>
<div class="row filter-by-typeofschool-container filter-page-section" data-section="typeofschool">
	<div class="column small-12 large-6">
	
		
			<div class="component" data-component="typeofschool">
				{{Form::radio('typeofschool', 'both', true, array('id' => 'both_typeofschool', 'class' => 'radio-filter filter-all filter-this'))}}
				{{Form::label('both_typeofschool', 'Both')}}
				<br class="show-for-small-only" />

				{{Form::radio('typeofschool', 'online_only',($amt==1)? true : false, array('id' => 'online_only_typeofschool', 'class' => 'radio-filter filter-this'))}}
				{{Form::label('online_only_typeofschool', 'Online Only')}}
				<br class="show-for-small-only" />

				{{Form::radio('typeofschool', 'campus_only',($amt==0)? true : false, array('id' => 'campus_only_typeofschool', 'class' => 'radio-filter filter-this'))}}
				{{Form::label('campus_only_typeofschool', 'Campus Only')}}

				@if( isset($filters))
					@foreach($filters as $filter)
						@if(isset($filter['interested_school_type']))
							@foreach($filter['interested_school_type'] as $amt)
								{{Form::hidden('typeofschool_crumb', $amt)}}
							@endforeach
						@endif
					@endforeach
				@endif
			</div>
			

	</div>

	<div class="column small-12 large-6">
		<div>
			By default, we will recommend students who are interested in both online and on-campus education. If you'd like to limit your recommendations to only online or on-campus, select one of these options.
		</div>
	</div>
</div>
