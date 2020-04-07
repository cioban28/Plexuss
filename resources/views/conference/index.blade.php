@extends('conference.master')

@section('content')
	
	<div class="row conference-main-container">
		<div class="column small-12 medium-10 large-6 small-centered">
			<div class="row top-message">
				2016 Conferences
			</div>
			<div class="subtitle">
				Come visit Plexuss on the road, meet us at one of these conferences. RSVP a time below:
			</div>
		</div>
		<div class="column small-12 medium-10 large-6 small-centered conference-detail">
			@foreach($conferences as $cfn)
			<div class= "row">
				<div class="column small-2 date">
					{{$cfn['date'] or ''}}
				</div>
				<div class="column small-7 name">
					{{$cfn['name'] or ''}} - {{$cfn['location'] or ''}}
				</div>
				<div class="column small-2 booth left">
					{{$cfn['booth_num'] or ''}}
				</div>
				<div class="column small-1 rsvp">
					<a href="http://sinashayesteh.youcanbook.me/" target="_blank">RSVP</a>
				</div>
			</div>
			@endforeach
		</div>
	</div>

@stop