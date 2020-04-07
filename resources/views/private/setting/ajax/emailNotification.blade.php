<style>
.toggle-on{ text-indent:10px !important;}.toggle-off{text-indent:15px !important;}
</style>
<div class='rightsidemenu'>
		<div class="row">
        	<span class="icon-arrow account_setting fs22 mobile-fs20 pl40" style="background-position:10px -110px; margin-left:-15px;">Manage Email Notifications</span>
        </div>
        
        <div class="bdr-b-dot mt10 mr10 show-for-small-only"></div>
       
        <div class="row" >
        	{{ Form::open(array('action' => 'SettingController@getEmailSettinInfo', 'data-abide' , 'id'=>'form')) }}
           <div class="row show-for-large-only">
           		
           		<ul class="chkul">               		
                    <li><label class="fs16 c-black mt20 mb15">Email me notifications on..</label></li>
                    <li>{{ Form::checkbox('profile-chk', 'value', false);}}  <div >My profile has been viewed by a college</div></li>
                    <li>{{ Form::checkbox('recommend-chk', 'value', false);}}  <div >Plexuss has made a recommendation for me
</div></li>
                    <li>{{ Form::checkbox('recruit-chk', 'value', false);}}  <div >I have been recruited by a college</div></li>
                    <li>{{ Form::checkbox('message-chk', 'value', false);}}  <div >I have a new message</div></li>
                    <li>{{ Form::checkbox('reminder-chk', 'value', false);}} <div >I have new calendar reminders</div></li>
                    <li>{{ Form::checkbox('grades-chk', 'value', false);}}  <div >I need to update my grades</div></li>
                    
                    <li> <label class="fs16 c-black mt10 mb10">Email me updates on....</label></li>
                    <li>{{ Form::checkbox('news-chk', 'value', false);}}  <div >News about Plexuss updates</div></li>
                    <li>{{ Form::checkbox('tips-chk', 'value', false);}}<div >Tips on getting more out of Plexuss</div></li> 
                </ul>
              
               <div align="right" class="mr40 mb20"> <input type="sumbit" value="Save Setting" class="org-btn"/></div>
           </div>
            
            <div class="row show-for-small-only">
                <div class="row">
               			 <label class="fs16 c-black mt20 mb15">Email me notifications on..</label>
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>My profile has been viewed by a college</li>
                            </ul> 
                            
                            <ul class="inline">  
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>Plexuss has made a recommendation for me</li>
                            </ul>
                          
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>I have been recruited by a college</li>
                            </ul>
                           
                            <ul class="inline">                             
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>I have a new message</li>
                            </ul>
                            
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>I have new calendar reminders</li>
                            </ul>
                            
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>I need to update my grades</li>
                            </ul>
                            
                            <label class="fs16 c-black mt20 mb15">Email me updates on....</label>
                            
                             <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>News about Plexuss updates</li>
                            </ul>
                            
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>Tips on getting more out of Plexuss</li>
                            </ul>
                            
                       <div class="mb20 mt20" align="center"><input type="sumbit" value="Save" class="org-btn"/></div>
           		  </div> 
            </div>                 
            {{Form::close()}}
        </div>
</div>
    
<script type="text/javascript">
$('.toggles').toggles();	   
</script>