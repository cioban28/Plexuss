<?php 
	//dd($key);


?>


<!-- left col	 -->
<div id="_contactCall" class=" clearfix contact-section-container opened" data-phone="{{$key['userPhone'] or null}}" data-token="{{$token or ''}}">

<div class='contact-call-refresh-button' title='Refresh'><div>&orarr;</div><div class='contact-call-refresh-button-text'>Refresh</div></div>

<div class="contact-call-lcol">
	<div class="contact-call-number  contact-call-row1 view-mode"><span class="edit-contact-phone-btn" title='Edit Phone Number'>Edit</span> <span class='user-contact-number-span'>{{$key['userPhone'] or 'Phone number not given'}}</span></div>

    <div class="contact-call-number contact-call-row1 edit-mode hidden">
        <span class="save-contact-phone-btn @if(!isset($key['userPhone']) || $key['userPhone'] === '') disabled @endif">Save</span>
        <input class='edit-contact-phone-input' name='edit-contact-phone' value="{{$key['userPhone'] or ''}}" />
    </div>
	
	<div class="contact-call-row2 contacgt-call-info"> 

	<div class="text-right"><div id="twilLog"> 
	
	<div class="twilLog"> &nbsp; </div> 

	<div class="contact-call-duration "> &nbsp; </div>

	</div></div>
	

	</div>
	<div class="mute-btn-wrapper">
		<div class="contact-call-mute mute"></div>
		<div class="contact-mute-txt">Mute</div>	
	</div>
</div>




<!-- rightcol -->
<div class="contact-call-rcol">
	<div class="contact-call-row1">  
		<div class="call-call-btn-wrapper call">
			<div class="contact-call-call-btn call"></div>
			<span class="contact-call-text">Call</span>
		</div>
	</div>
	<div class="contact-call-row2">  
		<div class="call-record-btn-wrapper">
			<div class="contact-record-btn"></div>
			<span class="contact-record-text">Call will be recorded</span>
		</div>
	</div>
    
    @if (isset($is_plexuss) && $is_plexuss == 1)
        <div class="contact-call-row2">  
            <div class="call-transfer-call-btn-wrapper">
                <div class="contact-transfer-call-btn">
                    <div class="contact-transfer-call-text">Transfer</div>
                </div>

                <div class="contact-posting-btn">
                    <div class="contact-posting-text">Post</div>
                </div>
            </div>
        </div>
    @endif

	<div class="prev-call-wrapper">
		<div class="prev-call-title">Previously Called</div>
		<div class="prev-call-container">
			<!-- AJAX previous calls go here -->
		</div>
	</div>
</div>

</div>