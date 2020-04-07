<div class="filter-crumbs-container">
  <ul class="inline-list filter-crumb-list">
    <li>
      <div class="clearfix">
        <div class="left section">{{$section}}: </div>
			 @if(isset($filters))
        	 @foreach($filters as $filter)
				 @if( isset($filter) && !empty($filter[$section]) )
					 @foreach( $filter[$section] as $date )
						<div class="left tag" data-tag-belongsto="{{$section}}" data-tag-val="{{$date}}" data-tag-component="{{$section}}" data-elem="{{$section}}_filter"><span class=""></span>{{$date}}<span class="remove">x</span></div>
					 @endforeach
				@endif
			@endforeach
			@endif
	 	</div>
    </li>
  </ul>
</div>
<div class="row filter-by-startDateTerm-container filter-page-section" data-section="startDateTerm">
  <div class="column small-12 large-6">
    <div class="component" data-component="startDateTerm">
      <label for="startDateTerm_filter">You can select multiple options, just click to add.</label>
	  {{Form::select('startDateTerm', $dates, null, array('id'=>'startDateTerm_filter', 'class' => 'select-filter filter-this')) }}	
	   	@if(isset($filters))
	 	@foreach($filters as $filter)
      	@if( isset($filter) && !empty($filter['startDateTerm']) )
      		@foreach( $filter['startDateTerm'] as $date )
      			{{Form::hidden('date_crumbs', $date)}}
      		@endforeach
      	@endif
	  @endforeach 
	  @endif</div>
    </div>
  <div class="column small-12 large-6">
    <div> Each student on Plexuss tell us when they intend to start school. Select the term(s) you want students you're targeting to apply for. </div>
  </div>
</div>
