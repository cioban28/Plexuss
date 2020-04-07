<style>
input[type=text]
{
	-moz-border-radius:1px ;	
	-webkit-border-radius:1px ;
	border-radius:1px;
	height:28px;	
	font-size:11px;
}
</style>
<div class="row">
<div class="row fs30 f-normal mb10">
		Apply to scholarship
    </div>
    
    <div class="row" style="border-bottom:solid 1px #797979; padding-bottom:5px;">
    	<div class="small-5 column no-padding c-blue f-bold fs13">Akash Kuruvilla Memorial Scholarship</div>
        <div class="small-4 column c-black f-bold fs14">Amount: $1,000</div>
        <div class="small-3 column c-black f-bold fs14">Deadline: 6/29/14</div>
    </div>

<div class="row">
    <div class="fs12 c79 mt10 mb10">Make sure the following information from your profile is correct, and we will submit it as your application.</div>
	{{ Form::open(array('url' => '#', 'method' => 'post', 'data-abide' => 'ajax')) }}
    
    <div class="mt20 mb20">   
        <div class="row">         
            <div class="small-2 column no-padding c79 f-bold fs12">
				{{ Form::label('fname', 'First Name', array('class' => 'c79 f-bold fs12')) }}
            </div>
        
        	<div class="small-10 column no-padding">
				{{ Form::text('fname', null, array('id' => 'fname', 'placeholder' => 'First name', 'required pattern' => 'name')) }}
				<small class="error">Enter your first name</small>
            </div>
        </div> 
        <div class="row">         
            <div class="small-2 column no-padding c79 f-bold fs12">
				{{ Form::label('lname', 'Last Name', array('class' => 'c79 f-bold fs12')) }}
            </div>
        
        	<div class="small-10 column no-padding">
				{{ Form::text('lname', null, array('id' => 'lname', 'placeholder' => 'Last name', 'required pattern' => 'name')) }}
				<small class="error">Enter your last name</small>
            </div>
        </div>
        
        
        <div class="row">         
            <div class="small-2 column no-padding c79 f-bold fs12">
				{{ Form::label('address', 'Address', array('class' => 'c79 f-bold fs12')) }}
            </div>
        
        	<div class="small-10 column no-padding">
				{{ Form::text('address', null, array('id' => 'address', 'placeholder' => 'address', 'required pattern' => 'address')) }}
				<small class="error">Enter your address</small>
            </div>
        </div>
        
        <div class="row">         
            <div class="small-2 column no-padding c79 f-bold fs12">
				{{ Form::label('state', 'State', array('class' => 'c79 f-bold fs12')) }}
            </div>
        
        	<div class="small-10 column no-padding">
                <div class="row">
					<div class="small-2 column no-padding">
						{{ Form::text('state', null, array('id' => 'state', 'placeholder' => 'State', 'required pattern' => 'state', 'maxlength' => '2')) }}
						<small class="error">Enter a state</small>
					</div>
					<div class="small-2 column c79 f-bold fs12">
						{{ Form::label('zip', 'Zip', array('class' => 'c79 f-bold fs12')) }}
					</div>
					<div class="small-3 column">
						{{ Form::text('zip', null, array('id' => 'zip', 'placeholder' => 'Zip', 'required pattern' => 'zip', 'maxlength' => '5')) }}
						<small class="error">Enter a valid zip</small>
					</div>
                    <div class="small-5 column"></div>
                </div>
               
            </div>
        </div>
        
        <div class="row">         
            <div class="small-2 column no-padding c79 f-bold fs12">
				 {{ Form::label('email', 'email', array('class' => 'c79 f-bold fs12')) }}
            </div>
        
        	<div class="small-10 column no-padding">
				{{ Form::text('email', null, array('placeholder' => 'email', 'required pattern' => 'email')) }}
				<small class="error">Enter a valid email</small>
            </div>
        </div>
        
        <div class="row">         
            <div class="small-2 column no-padding c79 f-bold fs12">
				 {{ Form::label('phone', 'Phone Number', array('class' => 'c79 f-bold fs12')) }}
            </div>
        
        	<div class="small-10 column no-padding">
				{{ Form::text('phone', null, array('placeholder' => 'phone', 'required pattern' => 'phone', 'maxlength' => '16')) }}
				<small class="error">Enter a valid phone number</small>
            </div>
        </div>
        
        
        <div class="row">         
            <div class="small-6 column no-padding c79 f-bold fs12">
             	<div class="row">
                	<div class="small-4 column no-padding">
						{{ Form::label('month', 'Birth Date', array('class' => 'c79 f-bold fs12')) }}
                    </div>
                    
                    <div class="small-8 column no-padding">
                    	<div class="row">
							<div class="small-4 column no-padding">
								{{ Form::text('month', null, array('placeholder' => 'MM', 'required pattern' => 'month', 'maxlength' => '2')) }}
								<small class="error">Month (MM)</small>
							</div>
							<div class="small-4 column">
								{{ Form::text('day', null, array('placeholder' => 'DD', 'required pattern' => 'day', 'maxlength' => '2')) }}
								<small class="error">Day (DD)</small>
							</div>
							<div class="small-4 column">
								{{ Form::text('year', null, array('placeholder' => 'YYYY', 'required pattern' => 'year', 'maxlength' => '4')) }}
								<small class="error">Year (YYYY)</small>
							</div>
                        </div>
                    </div>
                    
                </div>
            </div>
        
        	 <div class="small-6 column c79 f-bold fs12">
                <div class="row">
                	<div class="small-5 column">
						{{ Form::label('religion', 'Religion', array('class' => 'c79 f-bold fs12')) }}
                    </div>
                    
                    <div class="small-7 column no-padding">                    	
                        {{ Form::select('religion', array('' => 'No preference'), null, array('class'=> 'styled-select', 'required')) }}
						<small class="error">Religion is required</small>
                    </div>
                    
                </div> 
            </div>
        </div>
        
        <div class="row">         
            <div class="small-6 column no-padding c79 f-bold fs12">
             	<div class="row">
                	<div class="small-4 column no-padding">
						{{ Form::label('gender', 'Gender', array('class' => 'c79 f-bold fs12')) }}
                    </div>
                    
                    <div class="small-8 column no-padding">                    	
                        {{ Form::select('gender',array('' => 'No preference'),null,array('class'=> 'styled-select', 'required')) }}                       
						<small class="error">Gender is required</small>
                    </div>
                    
                </div>
            </div>
        
        	 <div class="small-6 column c79 f-bold fs12">
                <div class="row">
                	<div class="small-5 column no-padding">
						{{ Form::label('married', 'Marital Status', array('class' => 'c79 f-bold fs12')) }}
                    </div>
                    
                    <div class="small-7 column no-padding">                    
                        {{ Form::select('married',array('' => 'No preference'),null,array('class'=> 'styled-select', 'required')) }}                       
						<small class="error">Marital status is required</small>
                    </div>
                    
                </div> 
            </div>
        </div>
        
        <div class="row pt10">         
            <div class="small-6 column no-padding c79 f-bold fs12">
             	<div class="row">
                	<div class="small-4 column no-padding">
						{{ Form::label('ethnicity', 'Ethnicity', array('class' => 'c79 f-bold fs12')) }}
                    </div>
                    
                    <div class="small-8 column no-padding">                    	
                        	{{ Form::select('ethnicity',array('' => 'No preference'),null,array('class'=> 'styled-select', 'required')) }}                       
						<small class="error">Ethnicity is required</small>
                    </div>
                    
                </div>
            </div>
        
        	 <div class="small-6 column c79 f-bold fs12">
                <div class="row">
                	<div class="small-5 column no-padding">
						{{ Form::label('children', 'Do you have children?', array('class' => 'c79 f-bold fs12')) }}
                    </div>
                    
                    <div class="small-7 column no-padding">                    	
                        	{{ Form::select('children', array('' => 'No preference'),null,array('class'=> 'styled-select', 'required')) }}                       
							<small class="error">Please select an option</small>
                    </div>
                    
                </div> 
            </div>
        </div>
        
      
      	<div class="pt10">
        The following information from your Plexuss Profile will be sent. <br>
		Please make sure these sections are accurate before applying.
        </div>
        
        <div class="c-98d fs14 txt-deco-under f-bold mt10">GRADES <span class="pl20">ACHIEVEMENTS</span></div>        
        
        <div class="row pt10">  
             <label>Essay (if required)<span class="pl20 c-98d fs14 txt-deco-under f-bold">+ attach a document</span></label>            
        
        	<div class="small-12 column no-padding mt10">
                <textarea name="comment">Use this space to differentiate yourself and tell a story about you. </textarea>
            </div>
        </div>
        
         <div class="row mt10">  
            <label>Transcripts<span class="pl20 c-98d fs14 txt-deco-under f-bold">+ add a transcript</span></label>         
        
        	<div class="small-12 column no-padding c-black fs12 f-bold pt10" style="border-top:solid 1px #797979; padding-bottom:5px;">
               You haven’t added a transcript
            </div>
        </div>
      	
                          
    </div>
   
    <div class="mb20 mt20">
    <div class="c79 fs14" align="right" onClick="$('#applyScholarshipModel').foundation('reveal', 'close');">Cancel<input type="sumbit" value="Apply" class="org-btn ml10"/></div>    
	{{ Form::close() }}
    </div>
    
    </div>  
</div>

<a class="close-reveal-modal c-black">&#215;</a>
<script type="text/javascript">
$(document).foundation({
		abide: {
			patterns: {
				name: /^([a-zA-Z\'\- ])+$/,
				address: /^([0-9a-zA-Z\.,\- ])+$/,
				state: /^([a-zA-Z]){2}$/,
				zip: /^\d{5}(-\d{4})?$/,
				phone: /^1?\-?\(?([0-9]){3}\)?([\.\-])?([0-9]){3}([\.\-])?([0-9]){4}$/,
				month: /^([1-9]|[1][0-2])$/,
				day: /^([1-9]|[1-2][0-9]|[3][0-1])$/,
				year: /^([1][8][8-9][0-9]|[1][9][0-9][0-9]|[2][0][0-1][0-9])$/
			}
		}
	});
</script>
