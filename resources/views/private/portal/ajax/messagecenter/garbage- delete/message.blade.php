<div id="manage-school-div">                        
    <div class="portal_header-div row pos-rel">
    <!---------------------------------------- header menu in mobile view ---------------------------------------->           
    	<div class="show-for-small-only">
            <div class="row pt15">
            	<div class="small-12 column c-black no-padding fs14" style="margin-left:-5px;">MESSAGING CENTER</div>              
            </div>
        </div>
      <!---------------------------------------- header menu in mobile view ---------------------------------------->   
        <div class="small-12 column no-padding fs21 f-bold clr-green show-for-medium-up">MESSAGING CENTER <span class="f-normal fs16 pl20">Engage with colleges</span></div>        
    </div>
    
    <div class="portal_header_nav show-for-medium-up">                      
        <ul>
            <li class="large-3">MESSAGE CENTER</li>
            <li onclick="WriteMsg('msgright');"><span class="fs18 f-bold">+</span> WRITE NEW MESSAGE</li>            
            <li onclick="settingPopup();"><img src="/images/nav-icons/setting-white.png"> SETTINGS</li>  
                       
           
        </ul>
    </div>
    
    <div class="row portal_header-mid">
            <div class="medium-3 small-3 column portal-content-left-side no-padding show-for-medium-up">
                <ul class="left-nav">
                    <li onclick="loadPortalTabs('messagecenter','menu1')" class="left-nav-li-active">
                    	<img src="/images/nav-icons/nav-email-hover.png">
                        <!--<div class="badge">10</div> -->
                        <div class="litext">MESSAGES</div>
                        <div class="fr"><img src="/images/nav-icons/nav_selected.png"></div>
                        <div class="clear"></div>
                    </li>
                                        
                    <li onclick="loadPortalTabs('messagecenter','menu2')">
                    	<img src="/images/nav-icons/trash-big.png">
                        <!--<div class="badge">10</div>-->
                        <div class="litext">TRASH</div>
                    </li>
                </ul>
            </div>
            
            <div class="medium-9 small-12 column portal-content-right-side no-padding">
                <div class="row">
                    <div class="small-12 medium-6 column no-padding msgleft border-right-79">
                        <div class="msg-heading-div show-for-medium-up">MESSAGE THREADS</div>
                        
                        
                        
                        <div class="pos-rel pl10 mt10">
                        <input type="text" class="search-txt" placeholder="Search Messages..." />                
                        <div class="go-black"></div>
                        </div>
                            
                        <div class="pl10">
                            <input type="checkbox" name="school_msg" /><span class="c79 f-bold fs12">&nbsp;&nbsp;Only show messages with schools</span>
                        </div>
                        
                        <div class="show-for-small-only write-msg-mobile" onclick="WriteMsg('msg-list');"><span class="fs14">+</span> WRITE NEW MESSAGE</div>
                        
                     
                    
                        <div class="msg-list" id="msg-list">
                       		<div class="show-for-small-only">
                           		 <div align="center" class="msgloader pt40 d-none"><img src="/images/AjaxLoader.gif"></div>
                            </div>
                        
                           <!------------------------------------ In Desktop ------------------------------------->
                            <div class="row msg-content-left show-for-medium-up" style="border-bottom:#f5f5f5 solid 1px;" onclick="messageThread('msgright',0);">
                                <div class="small-2 column no-padding">
                                <img src="/images/collge_logo2.png" class="msg-img">
                                </div>
                                
                                <div class="small-8 column no-padding">
                                <div class="c-blue fs14 f-bold">Coach George, Dad<br />
                                <span class="c79 fs12 f-normal">                      	                        
                                    Let’s set up a campus tour...                                        
                                </span>
                                </div> 
                                </div>
                                
                           		 <div class="small-2 column no-padding c-black fs12">6/12/14</div>
                            </div>
                            <!------------------------------------ In Desktop ------------------------------------->
                            
                            <!------------------------------------ In Mobile ------------------------------------->
                            <div class="row msg-content-left show-for-small-only" style="border-bottom:#f5f5f5 solid 1px;" onclick="messageThread('msg-list',0);">
                                <div class="small-2 column no-padding">
                                <img src="/images/collge_logo2.png" class="msg-img">
                                </div>
                                
                                <div class="small-8 column no-padding">
                                <div class="c-blue fs14 f-bold">Coach George, Dad<br />
                                <span class="c79 fs12 f-normal">                      	                        
                                    Let’s set up a campus tour...                                        
                                </span>
                                </div> 
                                </div>
                                
                           		 <div class="small-2 column no-padding c-black fs12">6/12/14</div>
                            </div>
                            <!------------------------------------ In Mobile ------------------------------------->
                                                                           	
                        </div>
                    </div>
                
                    <div class="small-12 medium-6 column no-padding msgright show-for-medium-up" id="msgright">
                     	 <div align="center" class="msgloader d-none pt40 show-for-medium-up"><img src="/images/AjaxLoader.gif"></div>
                         <!------------------------------------ load content ------------------------------------->
                    </div>
                </div>      
            </div>
            
            <div class="clearfix"></div> 
             <!---------------------------------------- footer menu in mobile view ---------------------------------------->            
             <div class="show-for-small-only">
                 <div class="floating-header-mobile" align="right">  
                    <ul>
                        <li onclick="loadPortalTabs('messagecenter','menu1')" class="mobile-menu-nav-active"><img src="/images/nav-icons/nav-email-hover.png"><div class="badge">10</div>  </li>
                    	<li onclick="loadPortalTabs('messagecenter','menu2')" ><img src="/images/nav-icons/trash-small.png"> <div class="badge">10</div> </li>                 
                    </ul>
                </div>
            </div>
            <!---------------------------------------- footer menu in mobile view ----------------------------------------> 
                                 
    </div>  
</div>


<script type="text/javascript">
$(document).ready(function(e) {
  messageThread('msgright',0);  
});
</script>
