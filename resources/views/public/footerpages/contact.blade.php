@extends('public.footerpages.master')
@section('content')

<div class='row'>
	<div class='text-center small-12 column'>
		<h1 class='header1'>Contact</h1>
	</div>
</div>
<div class='row'>

	<div class='small-12 medium-12 large-6 large-centered column'>
		
		{{ Form::open(array('url' => '/contact', 'method' => 'POST', 'id' => 'footerContactForm' )) }}
		<div class='row'>
			<div class='large-12 column'>
				@if($errors->any())
					<div class="alert alert-danger">
						{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
					</div>
				@endif
			</div>
		</div>




		<div class='row'>
			<div class='large-12 column'>
				{{ Form::label(  'contactType', 'Select a department to contact?', array('class' => 'title') ) }}
			</div>
		</div>



		<div class='row'>
			<div class='large-12 column'>
				{{ Form::select( 'contactType', array(
					'' => 'Please select...',
					'partnership' => 'Partnership',
					'advertising' => 'Advertising',
					'press' => 'Press',
					'investor' => 'Investor',
					'other' => 'Other',
				), null , array('class' => '') ) }}
			</div>
		</div>







		<div class='row'>
			<div class='large-6 column'>
				{{ Form::text('fname', null, array('placeholder' => 'First Name' , 'class' => '')) }}
			</div>

			<div class='large-6 column'>
				{{ Form::text('lname', null, array('placeholder' => 'Last Name' ,'class' => '')) }}
			</div>
		</div>

		<div class='row'>
			<div class='large-12 column'>
				{{ Form::text('email', null, array('placeholder' => 'Email' ,'class' => '')) }}
			</div>
		</div>

		<div class='row'>
			<div class='large-12 column'>
				{{ Form::text('phone', null, array('placeholder' => 'Phone' ,'class' => '')) }}
			</div>
		</div>

		<div class='row'>
			<div class='large-12 column'>
				{{ Form::text('company', null, array('placeholder' => 'Company' ,'class' => '')) }}
			</div>
		</div>

		<div class='row'>
			<div class='large-12 column'>
				{{ Form::textarea('tellusmore', null, array('placeholder' => 'Tell us more' ,'class' => '')) }}
			</div>
		</div>

		<div class='row'>
			<div class='text-right small-12 large-6 large-push-6 column'>
				{{ Form::submit('Contact us', array('class'=>'button')) }}
			</div>
		</div>
		{{ Form::close() }}
	</div>

</div>
@stop
