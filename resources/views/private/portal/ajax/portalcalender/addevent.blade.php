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
  <div class="fs30 f-normal mb10"> Add New Event </div>
  <div class="mt20 mb20" style="margin:15px;">
    <div class="row">
      <div class="small-4 medium-2 column no-padding c79 f-bold fs12"> Event Name </div>
      <div class="small-8 medium-10 column no-padding"> {{ Form::text('event_name','', array('placeholder' => 'Event Name')) }} </div>
    </div>
    <div class="row">
      <div class="small-4 medium-2 column no-padding c79 f-bold fs12"> Start </div>
      <div class="small-8 medium-10 column no-padding">
        <div class="row">
          <div class="small-5 medium-4 column no-padding">{{ Form::text('start_date','', array('placeholder' => 'dd/mm/yyyy','class'=>'datepicker')) }}</div>
          <div class="small-5 medium-4 column">{{ Form::select('start_time',array('0' => 'Select'),'',array()) }}</div>
          <div class="small-2 medium-4 column c79 f-bold fs12">{{ Form::checkbox('all_day', 1 ,'');}} All Day</div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="small-4 medium-2 column no-padding c79 f-bold fs12"> End </div>
      <div class="small-8 medium-10 column no-padding">
        <div class="row">
          <div class="small-5 medium-4 column no-padding">{{ Form::text('end_date','', array('placeholder' => 'dd/mm/yyyy','class'=>'datepicker')) }}</div>
          <div class="small-5 medium-4 column">{{ Form::select('end_time',array('0' => 'Select'),'',array()) }}</div>
          <div class="small-2 medium-4 column"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="small-4 medium-2 column no-padding c79 f-bold fs12"> Repeat </div>
      <div class="small-8 medium-10 column no-padding"> {{ Form::text('zipcode','', array('placeholder' => 'Zip Code','class'=>'advansed-search-txt')) }} </div>
    </div>
    <div class="row">
      <div class="small-4 medium-2 column no-padding c79 f-bold fs12"> Location </div>
      <div class="small-8 medium-10 column no-padding"> {{ Form::text('zipcode','', array('placeholder' => 'Zip Code','class'=>'advansed-search-txt')) }} </div>
    </div>
    <div class="row">
      <div class="small-4 medium-2 column no-padding c79 f-bold fs12"> Note </div>
      <div class="small-8 medium-10 column no-padding"> {{ Form::textarea('notes', 'Notes', array('class'=>'Note-txtarea')) }} </div>
    </div>
    <div class="row">
      <div class="small-4 medium-2 column no-padding c79 f-bold fs12"> Reminder </div>
      <div class="small-8 medium-10 column no-padding">
        <div class="small-4 column no-padding">{{ Form::select('event_type',array('0' => 'No Reminder'),'',array()) }}</div>
        <div class="small-8 column"></div>
      </div>
    </div>
    <div class="row">
      <div class="small-4 medium-2 column no-padding c79 f-bold fs12"> Type </div>
      <div class="small-8 medium-10 column no-padding">
        <div class="small-6 medium-4 column no-padding">{{ Form::select('event_type',array('0' => 'Select'),'',array()) }}</div>
        <div class="small-6 medium-2 column"><!--{{ Form::select('event_color_type',array('0' => 'Select'),'',array()) }}-->        
            <select name="form[location]">
                <option value="#EAB648" style="background:#EAB648; padding-left: 20px;">COLLEGE EVENT</option>
                <option value="#FF6666" style="background:#FF6666; padding-left: 20px;">CUSTOM EVENT 1</option>
                <option value="#A0DB39" style="background:#A0DB39; padding-left: 20px;">CUSTOM EVENT 2</option>
                <option value="#04A6AE" style="background:#04A6AE; padding-left: 20px;">SCHOOL TOUR</option>
                <option value="#98DDEF" style="background:#98DDEF; padding-left: 20px;">DEADLINE 1</option>
                <option value="#004358" style="background:#004358; padding-left: 20px;">DEADLINE 2</option>
            </select>
        </div>
        <div class="small-12 medium-8 column"></div>
      </div>
    </div>
  </div>
  <div class="mb20 mt20">
    <div class="c79 fs14" align="right" onClick="$('#event-add-modal').foundation('reveal', 'close');">Cancle
      <a href="#" class="tiny button secondary">Remove this event</a>
      <input type="sumbit" value="Apply" class="org-btn ml10" style="width:20%"/>
    </div>
  </div>
</div>
@include('private.footers.iframefooter')
