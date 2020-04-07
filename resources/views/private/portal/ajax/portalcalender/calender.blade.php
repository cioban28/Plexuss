<style>
.menu-nav-div-arrow{border-bottom: 12px solid rgba(0, 0, 0, 1); right:130px}
</style>
<div class="row" id="Portal-Calendar">  
<div class="portal_header-div row pos-rel show-for-small-only">
    <!---------------------------------------- header menu in mobile view ---------------------------------------->  

            <div class="row pt5">
            	<div class="small-12 column c-black no-padding fs14">CALENDAR</div>              
            </div>
      <!---------------------------------------- header menu in mobile view ---------------------------------------->   
</div>

<div class="fc-header-custom row pt10">
    <div class="small-12 medium-4 column no-padding pl10 pr10">
    	 <div id="curr_month">July 2014</div>      
 		<div class="row">        
        	<div class="small-3 medium-5 large-4 column no-padding">
            		<div class="row">
                    	<div class="small-5 column no-padding prev-button">
                        </div>
                        <div class="small-7 column no-padding next-button">
                        </div>
                    </div>                     
            </div>
            <div class="small-9 medium-7 large-8 column">
                <select name="filter_event_type" class="filter_event_type">
               	<option value="">Filter event type</option>
                </select>
            </div>
        </div>
        
        </div>   
    <div class="small-4 medium-5 column show-for-medium-up pl15">
    	<ul class="cal-notify-ul">
        	<li><div class="cal-notify-div orange"></div> <span >COLLEGE EVENT</span></li>
            <li><div class="cal-notify-div blue"></div> <span>SCHOOL TOUR</span></li>
            <li><div class="cal-notify-div green"></div> <span>CUSTOM EVENT 2</span></li>
            <li><div class="cal-notify-div red"></div> <span>CUSTOM EVENT 1</span></li>
            <li><div class="cal-notify-div l-blue"></div> <span>DEADLINE 1</span></li>
            <li><div class="cal-notify-div d-blue"></div> <span>DEADLINE 2</span></li>             
        </ul>
        
        
        
    </div>  
    <div class="small-3 medium-3 column show-for-medium-up no-padding pr15" align="right">
    	<input type="button" class="cal-btn orange" value="+ New Event" data-reveal-id="event-add-modal"/><br />
        <input type="button" class="cal-btn gray" value="+ Events from a college" data-reveal-id="event-college-modal"/>
    </div>  
    </div>

<div id='calendar' class="row"><!----------- Calender Tab ----------></div>

<!---------------------------------------- footer menu in mobile view ---------------------------------------->            
 <div class="show-for-small-only">
     <div class="floating-header-mobile" align="right">  
        <ul>
         <li style="margin-top:-10px"><input type="button" class="cal-btn orange" value="+ New Event" data-reveal-id="event-add-modal"/></li>
        <li style="margin-top:-10px"><input type="button" class="cal-btn gray" value="+ Events from a college" data-reveal-id="event-college-modal"/></li>      
        </ul>
    </div>
</div>
<!---------------------------------------- footer menu in mobile view ----------------------------------------> 

<!--//Calender Model-->
<div id="event-notallowed-modal" class="reveal-modal no-padding medium" data-reveal>
    <div class="large-12 txt-center p15">
        Event Is Not Allowed To Add On Previous Date From Today
    </div>
    <a class="close-reveal-modal c-black">&#215;</a>
</div>   
 
<div id="event-add-modal" class="reveal-modal no-padding medium radius10" data-reveal>
	<!------ add event model----->
    <iframe src="/addevent" class="event-frame large-12"></iframe>
    <a class="close-reveal-modal c-black">&#215;</a>
</div>

<div id="event-college-modal" class="reveal-modal no-padding small radius10" data-reveal>
	<iframe src="/eventcollege" class="event-frame large-12"></iframe>
    <a class="close-reveal-modal c-black">&#215;</a>
</div>
<!--//Calender Model-->

