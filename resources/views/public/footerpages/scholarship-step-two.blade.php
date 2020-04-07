@extends('public.footerpages.masterTemplate')
@section('content')

<div class='formbox row collapse'>
	<div class="small-12 medium-8 medium-centered column">
		<div class='row'>
			<div class="large-12 column">
				<div class='row'>
					<div class='text-center large-12 column'>
						<h1 class='header1'>Scholarship Info</h1>
					</div>
				</div>

				<div class='row'>
					<div class='large-12 large-centered text-center column'>
						@if($errors->any())
							<div class="alert alert-danger">
								{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
							</div>
						@endif
					</div>
					<br/>
				</div>

				<div class='row white-container'>
					<div class='large-centered large-12 end column '>
						<div class='row collapse'>
							{{ Form::open(array('url' => '/scholarship-info', 'method' => 'POST')) }}
							{{Form::hidden('footer_step', '2', array('id' => 'footer_step2'))}}
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
										{{ Form::text('contact', null, array('placeholder' => 'Contact Name *' ,'class' => '')) }}
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
										{{ Form::text('email', Auth::user()->email, array('placeholder' => 'Email Address *' ,'class' => '')) }}
									</div>
								</div>
								<div class='row'>
									<div class='large-12 column'>
										{{ Form::text('applicationDeadline', null, array('placeholder' => 'Application Deadline *' ,'class' => '','onfocus'=> "(this.type='date')",'onblur' => "if(this.value=='')(this.type='text')", 'min'=>$date)) }}
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
										{{ Form::text('address', Auth::user()->address, array('placeholder' => 'Address *' , 'class' => 'name')) }}
									</div>
								</div>

								<div class='row'>
									<div class='large-12 column'>
										{{ Form::text('Address2', null, array('placeholder' => 'Address line two (Optional)' ,'class' => '')) }}
									</div>
								</div>

								<div class='row'>
									<div class='large-12 column'>
										{{ Form::text('city', Auth::user()->city, array('placeholder' => 'City *' ,'class' => '')) }}
									</div>
								</div>

								<div class='row'>
									<div class='large-12 column'>
										{{ Form::text('state', Auth::user()->state, array('placeholder' => 'State *' ,'class' => '')) }}
									</div>
								</div>

								<div class='row'>
									<div class='large-12 column'>
										{{ Form::text('zip', Auth::user()->zip, array('placeholder' => 'ZIP Code *' ,'class' => '')) }}
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
							<div class='large-12 column'>
								{{ Form::submit('Next Step', array('class'=>'button')) }}
							</div>
						</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@stop
