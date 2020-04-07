@extends('public.footerpages.master')

@section('content')
<div class='row'>
	<div class='text-center small-12 column'>
		<h1 class='header1'>Careers internships page</h1>
	</div>
</div>
<div class='row jobslistings'>
	<div class='small-12 medium-12 column'>
		<div class='row'>
			<div class='text-center small-12 column'>
				<h2>Internships</h2>
			</div>
		</div>
		<div class='row'>
			<div class='small-11 small-centered  column postingBox'>
				<div class="row">
					<div class="small-10 small-centered columns">
						@if (isset($careers['internships']))
						@foreach ($careers['internships'] as $k => $internship)
						<h3>{{$k}}</h3>
						<div class='titleCity'>Walnut Creek, Ca</div>
						<ul>
							@foreach ($internship as $intern)
							<li><a href="/careers-internships/{{$intern['id']}}"><span class='jobtitle'>{{$intern['job_title']}}</span></a></li>
							@endforeach
						</ul>
						@endforeach
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='small-12 medium-12 column'>
		<div class='row'>
			<div class='text-center small-12 column'>
				<h2>Careers</h2>
			</div>
		</div>
		<div class='row'>
			<div class='small-11 small-centered  column postingBox'>
				<div class="row">
					<div class="small-10 small-centered columns">
						@if (isset($careers['careers']))
						@foreach ($careers['careers'] as $k => $carrer)
						<h3>{{$k}}</h3>
						<div class='titleCity'>Walnut Creek, Ca</div>
						<ul>
							@foreach ($carrer as $jobs)
							<li><a href="/careers-internships/{{$jobs['id']}}"><span class='jobtitle'>{{$jobs['job_title']}}</span></a></li>
							@endforeach
						</ul>
						@endforeach
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

