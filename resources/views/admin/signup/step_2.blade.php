<h3>Plexuss Free Services Agreement</h3>
<div class='admin-signup-terms'>
	<p>This Agreement (hereinafter, the <b>“Agreement”</b>) is made and entered into by and between The entity that has signed up on this signup process (hereinafter referred to as <b>"Institution"</b>) and Plexuss, Inc., a Delaware corporation (<b>“Plexuss”</b>).  Plexuss hereby agrees to provide Institution with use of the Plexuss platform on a limited basis per the terms set forth below.  </p>

    <ol>
    	<li><b>License Grant.</b> Plexuss hereby grants to Institution a limited, non-exclusive, revocable and non-transferable right to use the Plexuss platform for the limited purpose of student recruitment.</li>
        
        <br />
        
        <li><b>Services.</b> For the first 30 days of this Agreement, Institution will have access to Plexuss’ Premium services (“the Premium Services”.  After this initial 30-day period has expired, Institution will have access to our limited service (the “Limited Services”).</li>
        
        <br />
        
        <li><b>Term.</b> This Agreement shall be effective until either party terminates the Agreement, with or without cause, at any time.</li>
        
        <br />
        
        <li><b>Limited License Granted to Plexuss.</b> Institution hereby grants Plexuss a revocable, non-exclusive, non-transferable license to use, reproduce and display Institution trademarks, Institution copyrighted material and/or Institution images solely for the purpose of promoting Institution’s institution on the Plexuss platform.</li>
        
        <br />
        
        <li><b>Institution Covenants.</b> In exchange for the use of the Plexuss platform, Institution agrees to the following:</li>

        <ul>
            <li>Institution will at all times comply in full with the requirements of any applicable privacy and data protection laws (including where applicable, European Union Directives 95/46/EC and 2002/58/EC) to which it may be subject.  Institution agrees to maintain the privacy of students it engages on the Plexuss platform and shall not share personal data of any student it communicates with on the Plexuss platform.</li> 

            <li>Institution shall: (a) use commercially reasonable efforts to prevent unauthorized access to or use of the Plexuss platform; and (b) notify Plexuss in writing immediately of any unauthorized use of, or access to, the Plexuss platform or any user account or password thereof.</li>
        </ul>

        <li><b>Disclaimer of Warranties.</b>  THE PLEXUSS PLATFORM IS PROVIDED TO INSTITUTION ON AN “AS IS” BASIS. ALL CONDITIONS, REPRESENTATIONS AND WARRANTIES, WHETHER EXPRESS, IMPLIED, STATUTORY OR OTHERWISE, INCLUDING, WITHOUT LIMITATION, ANY IMPLIED WARRANTY OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE OR NON-INFRINGEMENT OF THIRD PARTY RIGHTS, ARE HEREBY DISCLAIMED TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW.  IN NO EVENT SHALL PLEXUSS BE LIABLE TO YOU OR ANY OTHER INDIVIDUAL OR ENTITY CONNECTED WITH YOU FOR ANY CLAIM, LOSS, OR DAMAGE OR ANY KIND OR NATURE WHATSOEVER ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THE SERVICES, ANY SERVICES FAILURE; OR ANY LOSS OF PROFITS, SALES, BUSINESS, DATA, OR OTHER DIRECT, INDIRECT, SPECIAL, INCIDENTAL, CONSEQUENTIAL, OR LOSS OR DAMAGE OF ANY KIND OR NATURE RESULTING FROM THE FOREGOING.</li>
        
        <br />
        
        <li><b>Dispute Resolution & Governing Law.</b>  In the event of any material dispute under this Agreement which cannot be settled amicably, both parties agree that such dispute shall be submitted to final and binding arbitration in Contra Costa County, California in accordance with the rules and procedures of JAMS Inc., a private mediation and arbitration facilitator.  This Agreement shall be interpreted in accordance with and governed by the law of the State of Delaware, without regard to conflict of laws principles.</li>
    </ol>
</div>
<div class='mt20'>
	{{ Form::open(array('data-abide')) }}
	<div class='row'>
		<div class='small-4 column'>
			{{ Form::text('agreement_name', null, array('id' => 'fullname', 'required', 'placeholder'=>'Enter name', 'pattern' => 'name')) }}
		</div>

		<div class='small-4 column'>
			{{ Form::text('agreement_email', $email, array('id' => 'rep_email', 'placeholder'=>'Email Address', 'required', 'pattern'=>'email')) }}
			<small class="error">*Please enter a valid email.</small>
		</div>

		<div class='small-3 column end'>
			{{ Form::text('agreement_date', null, array('class' => 'datepicker', 'required', 'placeholder'=>'MM/DD/YYYY', 'pattern' => 'date')) }}
			<small class="error">*Please enter a valid date format. MM/DD/YYYY.</small>
		</div>
	</div>
	<div class='row'>
		<div class='admin-agreement-checkbox large-12 column'>
			<input id='admin-agreement-check-step-2' class='agreement-checkbox' type='checkbox' required />
			<label for='admin-agreement-check-step-2'><b>I agree to the Plexuss <a target='_blank' href='/terms-of-service'><u>terms of service</u></a> & <a target='_blank' href='/privacy-policy'><u>privacy policy</u></a></b></label>
		</div>
	</div>
	<div class='row'>
		<div class='large-12 column'>
			<button class='admin-signup-button step-2'>Next</button>
		</div>
	</div>
	{{ Form::close() }}
</div>