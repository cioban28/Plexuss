<div id="manage-school-div"> 
	<div class="portal_header-div row pos-rel">
        <!---------------------------------------- header menu in mobile view ---------------------------------------->           
    	<div class="show-for-small-only">
        	<div class="heading-tab" style="left:165px;">MESSAGES</div>
            <div class="row pt10">
            	<div class="small-6 column c-black no-padding fs14" style="margin-left:-5px;">TRASH</div>
                <div class="small-6 column pr30" align="right">
                	
                    <div class="cursor" onClick="showMenu('expand-toggle-span','menu-nav-div');">
                         <img src="/images/nav-icons/setting-gray.png">&nbsp;<span class="c79 f-bold fs12">Actions</span>
                         <span class="expand-toggle-span">&nbsp;</span>
                     </div>
                    
                    <div class="menu-nav-div d-none" id="menu-nav-div">
                     <div class="menu-nav-div-arrow"></div>
                    	<ul class="mobile-top-nav" onclick="settingPopup();">
                            <li>
                            	<ul class="inline">
                                	<li class="pl15"><img src="/images/nav-icons/setting-white.png"></li>
                                    <li class="pl12">SETTINGS</li>
                                </ul>
                            </li> 
                            
                            <li>
                            	<ul class="inline">
                                	<li class="pl15"><img src="/images/nav-icons/calender.png"></li>
                                    <li class="pl15">ADD DEADLINES</li>
                                </ul>
                            </li> 
                            
                            <li>
                            	<ul class="inline">
                                	<li class="pl15"><img src="/images/nav-icons/trash-big.png"></li>
                                    <li class="pl15">MOVE TO TRASH</li>
                                </ul>
                            </li> 
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
      <!---------------------------------------- header menu in mobile view ----------------------------------------> 
    
        <div class="small-7 column no-padding fs21 f-bold clr-green show-for-medium-up">
        	<span class="fs21 f-bold clr-green">TRASH</span>
            <span class="fs16 f-normal clr-green pl15">Restore schools you have blocked communication with</span>
        </div>
        <div class="small-5 column no-padding show-for-medium-up">        	
            <div>
            	<input type="text" class="search-txt" placeholder="Dumpster Dive..." />                
                <div class="go"></div>
            </div>
        </div>
    </div>                       
    
    
    <div class="portal_header_nav show-for-medium-up">                      
        <ul>
            <li class="large-3">SCHOLARSHIP CENTER</li>
            <li onclick="settingPopup();"><img src="/images/nav-icons/setting-white.png"> SETTINGS</li>                       
            <li><img src="/images/nav-icons/revert_arrow_white.png"> RESTORE</li>
            <li><img src="/images/nav-icons/trash-small.png"> PERMANANTLY DELETE</li>
        </ul>
    </div>
    
    <div class="row portal_header-mid">
            <div class="medium-3 small-3 column portal-content-left-side no-padding show-for-medium-up">
            
            <ul class="left-nav">
                    <li onclick="loadPortalTabs('messagecenter','menu1')">
                    	<img src="/images/nav-icons/nav-email-hover.png">
                        <!--<div class="badge">10</div> -->
                        <div class="litext">MESSAGES</div>
                       
                    </li>
                                        
                    <li onclick="loadPortalTabs('messagecenter','menu2')" class="left-nav-li-active">
                    	<img src="/images/nav-icons/trash-big.png">
                        <!--<div class="badge">10</div>-->
                        <div class="litext">TRASH</div>
                         <div class="fr"><img src="/images/nav-icons/nav_selected.png"></div>
                        <div class="clear"></div>
                    </li>
                </ul>
            </div>
            
            <div class="medium-9 small-12 column portal-content-right-side no-padding">
            
                <table id="list-table" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="5%" valign="middle"><input type="checkbox" name="checkbox"></th>
                        <th width="65%">
                            
                            <div class="row pl5">                                                	
                                <div class="small-1 column no-padding">
                                    <div class="sort-arrow-up"></div>
                                    <div class="sort-arrow-down"></div>
                                </div>
                                 <div class="small-11 column pt5">
                                    ITEMS IN YOUR TRASH
                                 </div>                                                          
                            </div>
                            
                        </th> 
                        
                        <th width="10%">
                            <div class="row pl5">                                                	
                                <div class="small-1 column no-padding">
                                    <div class="sort-arrow-up"></div>
                                    <div class="sort-arrow-down"></div>
                                </div>
                                 <div class="small-11 column pt5">
                                    DEADLINE
                                 </div>                                                          
                            </div>
                        </th>
                        <th align="center">                           
                            <span class="pt5">RESTORE</span>
                        </th>                                                                     
                    </tr>
                </thead>
                
                <tbody>
                    <tr>
                       <td align="center" valign="top"><input type="checkbox" name="checkbox"></td>
                        
                        <td valign="top">
                            <div class="row">
                                <div class="small-2 medium-2 column no-padding show-for-medium-up">
                                    <img src="/images/collge_logo2.png" class="college_logo">
                                </div>
                                <div class="small-12 medium-5 column pl5 pos-rel">
                                   <div class="c-blue fs14 f-bold">UCLA</div> 
                                    <span class="c79 fs12 d-block">                      	                        
                                        Los Angeles, California                                        
                                    </span>
                                 </div>
                                 
                                 <div class="small-12 medium-5 column no-padding fs10 f-bold">About our Philosophy Program</div>
                                 
                             </div>                              
                         </td>
                      
                        <td align="center" valign="top">                                            	 
                             <span class="fs12 f-normal">5/22/14</span>  
                                                                                                  
                        </td> 
                        <td align="center" valign="top">  
                              <div class="c79 f-bold"><img src="/images/nav-icons/revert_arrow.png"> restore</div>                                                                
                        </td>                                                                     
                    </tr>
             
                </tbody>
                
                </table>
            </div>
            
            <div class="clearfix"></div>  
            
             <!---------------------------------------- footer menu in mobile view ---------------------------------------->            
             <div class="show-for-small-only">
                 <div class="floating-header-mobile" align="right">  
                    <ul>
                        <li onclick="loadPortalTabs('messagecenter','manageschool_menu1')"><img src="/images/nav-icons/nav-email-hover.png"><div class="badge">10</div>  </li>
                    	<li onclick="loadPortalTabs('messagecenter','manageschool_menu2')" class="mobile-menu-nav-active"><img src="/images/nav-icons/trash-small.png"> <div class="badge">10</div> </li>       
                    </ul>
                </div>
            </div>
            <!---------------------------------------- footer menu in mobile view ---------------------------------------->               
                                              
    </div>  
</div>