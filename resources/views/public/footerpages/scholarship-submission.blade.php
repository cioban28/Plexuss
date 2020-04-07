@extends('public.footerpages.master')
@section('content')

<div class='row'>
	<div class='text-center large-12 column'>
		<h1 class='header1'>Scholarship Submission</h1>
		<div class="subheader2">
			Colleges and Institutions interested in offering scholarships may fill out this form and submit scholarships for Plexuss' review.
		</div>
	</div>
</div>

<div class='row'>
	<div class='large-10 large-centered text-center column'>
		@if($errors->any())
			<div class="alert alert-danger">
				{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
			</div>
		@endif
	</div>
	<br/>
</div>

<div class='row '>
	<div class='large-centered large-10 end column '>
		<div class='row collapse'>
			{{ Form::open(array('url' => '/scholarship-submission', 'method' => 'POST')) }}
			<div class='large-6 column'>
				
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('scholarshiptitle', null, array('placeholder' => 'Scholarship Title *' , 'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('companyName', null, array('placeholder' => 'Company Name *' , 'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('contact', null, array('placeholder' => 'Contact *' ,'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('phone', null, array('placeholder' => 'Phone *' ,'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('fax', null, array('placeholder' => 'Fax (Optional)' ,'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('email', null, array('placeholder' => 'Email Address *' ,'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('applicationDeadline', null, array('placeholder' => 'Application Deadline *' ,'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('numberofawards', null, array('placeholder' => 'Number of Awards *' ,'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('maximumAmount', null, array('placeholder' => 'Maximum Amount *' ,'class' => '')) }}
					</div>
				</div>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('websiteAddress', null, array('placeholder' => 'Website Address *' ,'class' => '')) }}
					</div>
				</div>
			</div>
			
			<div class='text-center large-6 column'>
				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('address', null, array('placeholder' => 'Address *' , 'class' => 'name')) }}
					</div>
				</div>

				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('Address2', null, array('placeholder' => 'Address line two (Optional)' ,'class' => '')) }}
					</div>
				</div>

				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('city', null, array('placeholder' => 'City *' ,'class' => '')) }}
					</div>
				</div>

				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('state', null, array('placeholder' => 'State *' ,'class' => '')) }}
					</div>
				</div>

				<div class='row'>
					<div class='large-12 column'>
						{{ Form::text('zip', null, array('placeholder' => 'ZIP Code *' ,'class' => '')) }}
					</div>
				</div>

				<div class='row'>
					<div class='large-12 column'>
						{{ Form::textarea('scholarshipDescription', null, array('placeholder' => 'Scholarship Description *' ,'class' => 'name', 'rows' => '11')) }}
					</div>
				</div>
			</div>
		</div>
		<div class='row'>
			<div class='text-right small-12 medium-6 medium-push-6 large-6 large-text-right column'>
				{{ Form::submit('Next Step', array('class'=>'button')) }}
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>


@stop
