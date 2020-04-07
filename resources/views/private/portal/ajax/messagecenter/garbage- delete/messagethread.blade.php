<div class="row">
	<div class="show-for-small-only p10" onClick="loadPortalTabs('messagecenter','menu1')">Go Back</div>
    
    <div class="row pt5">
        <div class="small-8 column no-padding c79 f-bold fs12 pl5">
          <div class="cursor add-span" onClick="showMenu('expand-toggle-span','add-span-div');">
            <span class="fs16">+</span> <span class="pl5">Add someone to this thread</span>
          </div>
            
            <div class="add-span-div d-none" style="left:5px; top:25px">
                <div class="menu-nav-div-arrow-left"></div>
                <div class="row">
                	<div class="small-8 cloumn">
                    <input type="text" name="add-people" placeholder="Type Names/email to add..." class="search-txt" style="width:100% !important; margin:10px !important; font-weight:normal"/>                     
                    </div>
                    <div class="small-4 cloumn">
                       <input type="button" class="org-btn reply" value="Add" style="top:8px; right:5px;"/>
                    </div>
                </div>
            </div>
            
        </div>
    
        <div class="small-4 column no-padding">
            <div class="cursor" onClick="showMenu('expand-toggle-span','menu-nav-div');">
                <img src="/images/nav-icons/setting.png">&nbsp;<span class="c79 f-bold fs12">Actions</span>
                <span class="expand-toggle-span">&nbsp;</span>
            </div>
        
            <div class="menu-nav-div d-none" id="menu-nav-div" style="right:14px; top:35px">
            <div class="menu-nav-div-arrow"></div>
                <ul class="mobile-top-nav" onclick="settingPopup();">
                <li class="pl12 pt5">Mark as unread</li>   
                <li class="pl12 pt5">Delete messages</li> 
                <li class="pl12 pt5">Report spam/abuse</li> 
                <li class="pl12 pt5">Print message thread</li>                                  
                </ul>
            </div>
        </div>                            
    </div>

    <div class="msg-conversation-list">
        <div class="row msg-content-right pt15 pb10 pl5" style="border-bottom:#ffffff solid 1px;">
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
    </div>

    <div class="write-msg-div" style="margin:10px;">
    <textarea name="write-msg" class="write-msg">Write a reply...</textarea>
        <div class="row" style="border-top:solid 1px #f5f5f5">
            <div class="small-4 column no-padding c79 fs12 pt10" style="border-right:solid 1px #f5f5f5">
            <img src="/images/nav-icons/attach.png">	Add files 
            <input type="file" id="file" />
            <!--  <span class="text">Nothing selected</span>  -->                                                              
            </div>
        
            <div class="small-8 column no-padding mr10 pos-rel" align="right">
            <input type="button" class="org-btn reply" value="Reply"/>
            </div>
        </div>
    </div>
</div>