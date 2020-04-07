<div id="prepSchoolModal" class="prepSchoolModal reveal-modal medium" data-reveal>
	<div class='row'>
		<div class='large-centered small-12 column'>
			<h3 class='subheader'>Tell us a little about your company...</h3>
			<p class="lead">There are a few questions we need in order to confirm your business. We will contact you shortly to give you access to  your admin panel.</p>
		</div>
	</div>
	<div class="row">
		<div class="column small-12">
			{{ Form::open(array('url' => '/college-prep', 'method' => 'POST', 'id' => 'prepSchoolForm', 'data-abide'=>'ajax')) }}
				<div class='row'>
					<div class="column small-12 large-3">
						{{ Form::label('companyname', 'Company Name', array('class' => '' )); }}
					</div>
					<div class='small-12 large-9 column'>
						{{ Form::text('company', null, array('class' => '','id' =>'companyname', 'required')) }}
						<small class="error">You must enter a company name.</small>
					</div>
				</div>

				<div class="row">
					<div class="column small-12 large-3">
						{{ Form::label('companyType', 'Company Type', array('class' => '' )); }}
					</div>
					<div class="column small-12 large-9">
						{{Form::select('companyTypes', array('' => 'Select a Company Type', 'College Prep' => 'College Prep', 'International Agency' => 'International Agency', 'English Institution' => 'English Institution'), '', array('required'))}}
						<small class="error">Must select a company type</small>
					</div>
				</div>

				<div class='row'>
					<div class="column small-12 large-3">
						{{ Form::label('title', 'Your Title', array('class' => '' )); }}
					</div>
					<div class='small-12 large-9 column'>
						{{ Form::text('title', null, array('class' => '', 'id'=>'title', 'required')) }}
						<small class="error">A title is required.</small>
					</div>
				</div>
				<div class='row'>
					<div class="column small-12  large-3">
						{{ Form::label('phone', 'Phone', array('class' => '' )); }}
					</div>
					<div class='small-12 large-9 column'>
						{{ Form::text('phone', null, array('class' => '', 'id'=>'phone', 'required', 'pattern' => 'phone')) }}
						<small class="error">A phone is required.</small>
					</div>
				</div>

				<div class='collegePrep-checkarea'>
					<div class="row">
						<div class="column small-12 large-3">
							{{ Form::label('CollegeCounseling', 'Services offered',  array('class' => ' checkboxprepInline' )); }}
							<span style="font-size:12px;">(Select all the apply)</span>
						</div>
						<div class="column small-12 large-9">
							<div class="row collapse">
								<div class="small-12 large-9 column">
									{{Form::checkbox('CollegeCounseling', 'true', null ,array('id'=>'CollegeCounseling'))}}  <label for="CollegeCounseling" class="inline">College Counseling</label>
								</div>
								<div class="small-12 column">
									{{Form::checkbox('TutoringCenter', 'true', null, array('id'=>'TutoringCenter') );}} <label for="TutoringCenter" class="inline">Tutoring Center</label>
								</div>
								<div class="small-12 column">
									{{Form::checkbox('TestPreparation', 'true', null, array('id'=>'TestPreparation') );}} <label for="TestPreparation" class="inline">Test Preparation </label>
								</div>
								<div class="small-12 column">
									{{Form::checkbox('InternationalStudentAssistance', 'true', null, array('id'=>'InternationalStudentAssistance') );}} <label for="InternationalStudentAssistance" class="inline">International Student Assistance</label>
								</div>
							</div>
						</div>
					</div>
					<br/>
				</div>
				
				<div class='row'>
					<div class="column small-12 large-3">
						{{ Form::label('notes', 'Other', array('class' => '' )); }}
					</div>
					<div class='small-12 large-9 column'>
						{{ Form::textarea('notes', null, array('class' => '', 'id'=>'notes')) }}
					</div>
				</div>

				<div class='row'>
					<div class='small-12  column text-right'>
						{{ Form::submit('Submit', array('class'=>'button')); }}
					</div>
				</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

<div id="prepSchoolModalThanks" class=" reveal-modal medium" data-reveal>
	<div class='row'>
		<div class='column small-12'>
			<div class="row">
				<div class="close-reveal-modal column small-12 text-right" style="font-size: 26px; font-weight: bold; cursor: pointer;">X</div>
			</div>
			<div class='row'>
				<div class='text-center large-12 column'>
					<h1 class='header1'>We'll get back to you soon.</h1>
				</div>
			</div>
			<div class='row'>
				<div class='column text-center'>
					<img src="/images/ThankYou.jpg" alt='Thank You!'/>
				</div>
			</div>
			<div class='row'>
				<div class='small-12 text-center column'>
					<h2 class='thankyoutext'>Thank you for contacting us.<br/>Someone from Plexuss will contact you shortly</h2>
				</div>
			</div>
		</div>
	</div>

</div>