<style>
.toggle-on{ text-indent:10px !important;}.toggle-off{text-indent:15px !important;}
ul.inline{ margin-top:15px;}ul.inline li:first-child{ width:55px;} ul.inline li{ width:75%; word-break:break-all; padding-right:10px; font-size:13px;}
</style>
<div class='rightsidemenu'>	
		<div class="row">
        	<span class="icon-arrow account_setting fs22 mobile-fs20 pl40" style="background-position:10px -201px; margin-left:-15px;">Manage Text Notifications</span>
        </div>
	
		
       
     	<div class="bdr-b-dot mt10 mr10 show-for-small-only"></div>
        	{{ Form::open(array('action' => 'SettingController@getEmailSettinInfo', 'data-abide' , 'id'=>'form')) }}            
                <div class="row hide-for-small-only">
               		<div class="row">
                    <label class="c-black fs16 mt15 mb5">Mobile number</label>                
                    <div class="large-4 small-6 columns no-padding"><input type="text" name="mobile_number" class="pwd-txt" /></div>
                    <div class="large-8 small-5 columns no-padding pt2 pl5"><img src="/images/setting/password_icon.png" align="absmiddle"/></div>  
                	</div>
                	<label class="fs16 mb10 c-l-green">Text me when...</label>
                    <ul class="chkul">
                   
                    <li>{{ Form::checkbox('profile-chk', 'value', false);}}  <div >My profile has been viewed by a college</div></li>
                    <li>{{ Form::checkbox('recommend-chk', 'value', false);}}  <div >Plexuss has made a recommendation for me</div></li>
                    <li>{{ Form::checkbox('recruit-chk', 'value', false);}}  <div >I have been recruited by a college</div></li>
                    <li>{{ Form::checkbox('message-chk', 'value', false);}}  <div >I have a new message</div></li>
                    <li>{{ Form::checkbox('reminder-chk', 'value', false);}} <div >I have new calendar reminders</div></li>
                    <li>{{ Form::checkbox('grades-chk', 'value', false);}}  <div >I need to update my grades</div></li>
                    </ul>
                	<div align="right" class="mr40 mb10"> <input type="sumbit" value="Save Setting" class="org-btn"/></div>
                
                </div>          
        
             
             
             <div class="row show-for-small-only">
              
               			 <label class="fs16 c-black mt20 mb15">Text me when..</label>
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
                       <div class="mb20 mt20" align="center"><input type="sumbit" value="Save" class="org-btn"/></div>
           		  
            </div>
             
            {{Form::close()}}
       
</div>
<script type="text/javascript">
$('.toggles').toggles();	   
</script>