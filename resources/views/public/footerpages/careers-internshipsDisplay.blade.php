@extends('public.footerpages.master')
@section('content')
<div class='row'>
	<div class='text-center small-12 column'>
		<h1 class='header1'>{{$career[0]->job_title}}</h1>
	</div>
</div>

<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-10 medium-6 column leftjobcol'>
				<div class='row'>
					<div class='small-10 small-centered column'>
						{!! $career[0]->post !!}
					</div>
				</div>
			</div>
			<div class='small-10  medium-6 column'>
				{{Form::open(array('url' => '/careers-internships/'. $career[0]->id , 'method' => 'POST'))}}
				{{Form::hidden('position', $career[0]->job_type)}}
				@if (Session::has('refererUrl'))
					{{Form::hidden('referer', Session::get('refererUrl') )}}
				@endif
				@if (Session::has('trackingParams'))
					@foreach (Session::get('trackingParams') as $key => $element)
						{{Form::hidden($key, $element)}}
					@endforeach
				@endif
				<div class='row'>
					<div class='small-10 small-centered column'>
						@if($errors->any())
							<div class="alert alert-danger">
								{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
							</div>
						@endif
					</div>
				</div>
				<div class='row'>
					<div class='small-10 small-centered column'>
						{{Form::text('fname', null, array('class' => 'name', 'placeholder' => 'First Name'))}}
					</div>
				</div>
				<div class='row'>
					<div class='small-10 small-centered column'>
						{{Form::text('lname', null, array('class' => 'name', 'placeholder' => 'Last Name'))}}
					</div>
				</div>
				<div class='row'>
					<div class='small-10 small-centered column'>
						{{Form::text('email', null, array('class' => 'name', 'placeholder' => 'Email Address'))}}
					</div>
				</div>
				<div class='row'>
					<div class='small-10 small-centered column'>
						{{Form::text('phone', null, array('class' => 'name', 'placeholder' => 'Phone'))}}
					</div>
				</div>
				<div class='row'>
					<div class='small-10 small-centered column'>
						{{Form::text('zip', null, array('class' => 'name', 'placeholder' => 'Zip Code'))}}
					</div>
				</div>
				@if ($career[0]->job_type == 'internships')
					<div class='row'>
						<div class='small-10 small-centered column'>
							{{Form::text('schoolname', null, array('class' => 'name', 'placeholder' => 'High School Name'))}}
						</div>
					</div>
					<div class='row'>
						<div class='small-10 small-centered column'>
							{{Form::text('gradelevel', null, array('class' => 'name', 'placeholder' => 'Grade Level'))}}
						</div>
					</div>
					<div class='row'>
						<div class='small-10 small-centered column'>
							{{Form::text('counselorname', null, array('class' => 'name', 'placeholder' => 'Counselor\'s Name ( optional )'))}}
						</div>
					</div>
					<div class='row'>
						<div class='small-10 small-centered column'>
							{{Form::text('gpa', null, array('class' => 'name', 'placeholder' => 'GPA ( optional )'))}}
						</div>
					</div>
				@endif
				<div class="row">
					<div class="text-right small-10 small-centered columns">
						{{Form::submit('Submit!', array('class' => 'button'))}}
					</div>
				</div>
				{{Form::close()}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='text-center small-12 column'>
		<div class='careerFooter'><a href="/careers-internships">Back to Current Openings</a></div>
	</div>
</div>
@stop
