@extends('public.footerpages.master')
@section('content')
<div class='row'>
	<div class='text-center large-12 column'>
		<h1 class='header1'>Join as a College</h1>
	</div>
	<div class="text-center large-12 column membership">
  		$0 Membership Cost
	</div>
</div>



<div class='row'>
	<div class='large-12 column '>
		<div class='row'>

			<!-- right column area -->
			<div class='small-12 medium-6 medium-push-6 column collegeSubRightbar'>

				<div class='row'>
                    <div class='small-12 small-text-left medium-10 medium-centered column'>
                        <div class="benefit-info">Make connections with college-bound students across the globe. Discover how Plexuss can contribute to your student recruitment efforts.</div>
                    </div>
                </div>

                <div class='row'>
                    <div class='column small-12 small-text-left medium-10 medium-centered'>
                        <div class='title'>Benefit to Colleges</div>
                    </div>
                </div>

                <div class='row'>
                    <div class='small-12 small-text-left medium-10 medium-centered column'>
                        <div class='title'>Diversify</div>
                        <div class="benefit-info">Gain access to students globally - engage with them through live chat and text messaging.</div>
                    </div>
                </div>

                <div class='row'>
                    <div class='small-12 small-text-left medium-10 medium-centered column'>
                        <div class='title'>Optimize</div>
                        <div class="benefit-info">Discover higher quality students with the use of our machine-learning powered recommendation engine.</div>
                    </div>
                </div>

                <div class='row'>
                    <div class='small-12 small-text-left medium-10 medium-centered column'>
                        <div class='title'>Save Time</div>
                        <div class="benefit-info">Only recruit students who match your school's requirements by specifying your targeting filters.</div>
                    </div>
                </div>

                <div class='row'>
                    <div class='small-12 small-text-left medium-10 medium-centered column'>
                        <div class='title'>Reduce Cost</div>
                        <div class="benefit-info">Use our Forever Free plan or choose a flexible payment option to get more of what you want.</div>
                    </div>
                </div>

                <div class="row">
                	<div class="small-12 small-text-left medium-10 medium-centered column">
                		<div class="benefit-info">Complete the form to the left to begin.</div>
                	</div>
                </div>

                <div class="row">
                	<div class="small-12 small-text-left medium-10 medium-centered column">
                		<div class="title">
                			Plexuss represents students from <br/>
                		154 Countries  |  50 States  |  36,194 High Schools
                		</div>
                	</div>
                </div>

			</div>


			<!-- form area -->
			<div class='small-12 medium-6 medium-pull-6 column formarea'>
				<!-- <button id="autofill-button" onclick="window.location.href = '/linkedin';">
					<span class="logo">IN</span>
					<span class="button-text">
					AutoFill with <strong>LinkedIn</strong>
					</span>
				</button> -->

				{{ Form::open(array('url' => '/college-submission/thankyou', 'method' => 'POST', 'id' => 'linkedin_form')) }}

				<div class='row'>
					<div class='medium-10 medium-centered column'>
						@if($errors->any())
							<div class="alert alert-danger">
								{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
							</div>
						@endif
					</div>
				</div>
				<div class='row'>
					<div class='medium-10 medium-centered column'>
						{{ Form::text('company', null, array('placeholder' => 'College Name' , 'class' => '', 'id' => 'college-name-rep')) }}
					</div>
				</div>

				<div class='row'>
					<div class='medium-10 medium-centered column'>
						{{ Form::text('fname', null, array('placeholder' => 'First Name' ,'class' => '', 'id' => 'fname-rep')) }}
					</div>
				</div>

				<div class='row'>
					<div class='medium-10 medium-centered column'>
						{{ Form::text('lname', null, array('placeholder' => 'Last Name' ,'class' => '', 'id' => 'lname-rep')) }}
					</div>
				</div>

				<div class='row'>
					<div class='medium-10 medium-centered column'>
						{{ Form::text('title', null, array('placeholder' => 'Title' ,'class' => '', 'id' => 'title-rep')) }}
					</div>
				</div>

				<div class='row'>
					<div class='medium-10 medium-centered column'>
						{{ Form::text('email', null, array('placeholder' => 'Email' ,'class' => '', 'id' => 'email-rep')) }}
					</div>
				</div>

				<div class='row'>
					<div class='medium-10 medium-centered column'>
						{{ Form::text('phone', null, array('placeholder' => 'Phone' ,'class' => '', 'id' => 'phone-rep')) }}
					</div>
				</div>
				<div class='row'>
					<div class='medium-10 medium-centered column'>
						{{ Form::textarea('notes', null, array('placeholder' => 'Notes:' ,'class' => 'name', 'id' => 'notes-rep')) }}
					</div>
				</div>

				<div class='row'>
					<div class='small-12 medium-6 large-6 medium-centered large-uncentered large-text-left large-offset-1 column'>
						{{ Form::submit('Request Access', array('class'=>'button')) }}
					</div>
				</div>
				{{ Form::close() }}
			</div>







		</div>

		<div class="row">
			<div class="column small-12 small-text-left medium-12 medium-centered college-testimonials-title">College Testimonials</div>
			<div class="column small-12 text-left">
				<ul>
					<li class="college-testimonials-item">
						<div class="row">
							<div class="column small-1 show-for-medium-up college-comments" data-source="humboldt-state-university"></div>
							<div class="column small-9 end" style="margin-left: 30px;">
								<div class="comments-text">" Plexuss is incredibly innovative - taking feedback from universities and continually improving their product.  Plexuss allows universities to connect with a huge pool of prospective students from around the world that universities can target based on academic level, financial ability and degree program.  Cost-effective prospective student generation is what universities are looking for as international markets change, and Plexuss provides this. "</div>
								<div class="rep-name"><span>Emily Kirsch</span> - International Marketing and Recruitment Coordinator</div>
								<div class="college-name">Humboldt State University</div>
							</div>
						</div>
					</li>

					<li class="college-testimonials-item">
						<div class="row">
							<div class="column small-1 show-for-medium-up college-comments" data-source="university-of-illinois-at-chicago"></div>
							<div class="column small-9 end" style="margin-left: 30px;">
								<div class="comments-text">" Plexuss is what we have been looking for in a college search engine, and it has already proven to be ahead of the curve. It allows us to engage directly with students at the very early stages of recruitment on an innovative and unique platform. No other student search service on the market offers such a wide diversity of quality prospective students and communication tools. "</div>
								<div class="rep-name"><span>Richard O’Rourke,</span> Associate Director Office of Admissions
Recruitment &amp; Outreach</div>
								<div class="college-name">The University of Illinois at Chicago</div>
							</div>
						</div>
					</li>

				</ul>
			</div>
		</div>
	</div>
</div>



@stop
