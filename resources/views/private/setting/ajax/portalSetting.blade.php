<style>
.toggle-on{ text-indent:10px !important;}.toggle-off{text-indent:20px !important; }
</style>
<div class='rightsidemenu'>		
		<div class="row">
        <div class="row">
        	<span class="icon-arrow account_setting fs22 pl40 mobile-fs20" style="background-position:10px -307px; margin-left:-15px;">Portal Settings</span>
        </div>
        
           
            
            <div class="bdr-b-dot mt10 mr10 mb10 show-for-small-only"></div>
            
             <p class="fs14 c79 mobile-fs12 mobile-pr25 mt10">
                What kind of schools would you like to contact you?<br /> 
                You can also access and manage these settings through the <img src="/images/setting/setting_gray.png" align="absmiddle" style="margin-top:-5px;"/> settings in your Portal.
            </p>
            <p class="fs18 c-black f-400 mobile-pr25">I only want to hear from these types of schools:</p>
            
        </div>
       
        <div class="row" >
        {{ Form::open(array('action' => 'SettingController@getEmailSettinInfo', 'data-abide' , 'id'=>'form')) }}            
        <div class="row hide-for-small-only">       
               
            <div class="row ">        
                <div class="large-7 small-6 columns no-padding">
                    <div class="row">
                        <div class="large-2 small-2 columns no-padding"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="60"></div></div>
                        <div class="large-10 small-8 columns fs12 pl25">Public <span class="c79">(examples: University of Michigan)</span></div>
                    </div>	
                </div>
                <div class="large-5 small-6 columns no-padding">
                    <div class="row">
                        <div class="large-2 small-3 columns">
                            <div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="60"></div>
                        </div>
                        <div class="large-9 small-7  columns fs12 pl22">2-Year</div>
                    </div>	
                </div>                    
            </div>        
            
            <div class="row pt20">        
                <div class="large-7 small-6 columns no-padding">
                    <div class="row">
                        <div class="large-2 small-2 columns no-padding"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="60"></div></div>
                        <div class="large-10 small-8  columns fs12 pl25">Private  <span class="c79">(examples:  Columbia University)</span></div>
                    </div>	
                </div>
                <div class="large-5 small-6 columns no-padding">
                    <div class="row">
                        <div class="large-2 small-3 columns no-padding">
                            <div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="60"></div>
                        </div>
                        <div class="large-9 small-7 columns fs12 pl22">4-Year</div>
                    </div>	
                </div>                    
            </div>        
            
            <div class="row pt20">        
                <div class="large-7 small-6 columns no-padding">
                    <div class="row">
                        <div class="large-2 small-2 columns no-padding"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="60"></div></div>
                        <div class="large-10 small-8 columns fs12 pl25 v-align-mid">Non-traditional <span class="c79">(examples: Berkeley college)</span></div>
                    </div>	
                </div>
                <div class="large-5 small-6 columns no-padding">
                    <div class="row">
                        <div class="large-2 small-3 columns no-padding">
                            <div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="60"></div>
                        </div>
                        <div class="large-9 small-7 columns fs12 pl22">Only Ranked Schools &nbsp;<span><img src="/images/setting/tooltip_icon.png" align="absmiddle"/></span></div>
                    </div>	
                </div>                    
            </div>        
        
        	<div align="right" class="mr40  mt70"> <input type="sumbit" value="Save Setting" class="org-btn"/></div>
        </div>
        
        <div class="row show-for-small-only">
        
                <div class="row">               			
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>All</li>
                            </ul> 
                            
                            <ul class="inline">  
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>2- Year</li>
                            </ul>
                          
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>4-Year</li>
                            </ul>
                           
                            <ul class="inline">                             
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>Public <br /><span class="c79">(example: UCLA)</span></li>
                            </ul>
                            
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>Private <br /><span class="c79">(example: UCLA)</span></li>
                            </ul>
                            
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>For-Profit  <br /><span class="c79">(example: Berkeley college)</span></li>
                            </ul>
                            
                            
                            <ul class="inline">
                            <li class="fs14 c-black f-bold"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></li>
                            <li>Only ranked schools</li>
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
