@include('private.headers.header')
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
  <div class="fs30 f-normal mb10"> Add Events from a College</div>
  
  <div class="fs12 c79 pt10">if you are really interested in a school, you can add all of that school’s <br />
    events and deadlines to your calendar.</div>
  
 	<div class="mt20 mb20">
    {{ Form::text('zipcode','', array('placeholder' => 'Zip Code','class'=>'advansed-search-txt','id'=>'zipcodesearch')) }}
    
    <div class="fs12 c79 pt10 pb10 pl10">OR</div>
    
    {{ Form::select('event_type',array('0' => 'No Reminder'),'',array()) }} 
    
  </div>
  <div class="mb20 mt20">
    <div class="c79 fs14" align="right" onClick="$('#event-add-modal').foundation('reveal', 'close');">Cancle     
      <input type="sumbit" value="Add" class="org-btn ml10" style="width:20%"/>
    </div>
  </div>
</div>
@include('private.footers.iframefooter')
