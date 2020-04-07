@extends('agency.agencyProfile.master')
@section('content')
	<div class='browse-agents-container'>
		<div class='international-agents-header'>
			<div class='header-content'>
				<img src='/images/agency/agent-icon-white.png'>
				<h3>Plexuss Regional Representatives</h3>
			</div>
		</div>
		<div class='agency-search-bar'>
			<div class='select-tabs'>
				<div class='dropdown tab' data-tab='country'>
					<span>Country<span class='tab-arrow'></span></span>
					<ul class="dropdown-content country">
                        <li value='all'>All</li>
						@foreach ($countries_list as $key => $value)
				    		<li value='{{$value}}'>{{$value}}</li>
				    	@endforeach
				  	</ul>
			  	</div>
				
				<div class='dropdown tab' data-tab='service'>
					<span>Services<span class='tab-arrow'></span></span>
					<ul class="dropdown-content services">
						@foreach ($services_list as $key => $value)
				    		<li value='{{$value}}'>{{$value}}</li>
				    	@endforeach
				  	</ul>
				</div>
			</div>
		</div>
	</div>
	<div class='search-results-container mt10'>
		<!-- Inject search results here. -->
	</div>
@stop