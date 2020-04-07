<div class="row">
	<div class="show-for-small-only p10" onClick="loadPortalTabs('messagecenter','menu1')">Go Back</div>

    <div class="p10">
        <input type="text" name="sender_name" class="sender-txt" placeholder="To: Name(s) or email(s)..." />  
    </div>
    
    <div class="msg-conversation-list">
        <?php /*?><div class="row msg-content-right pt15 pb10 pl5" style="border-bottom:#ffffff solid 1px;">
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
        
        </div>   <?php */?>
    </div>
    
    <div class="write-msg-div" style="margin:10px;">
    <textarea name="write-msg" class="write-msg">Write a message...</textarea>
    <div class="row" style="border-top:solid 1px #f5f5f5">
        <div class="small-4 column no-padding c79 fs12 pt10" style="border-right:solid 1px #f5f5f5">
            <img src="/images/nav-icons/attach.png">	Add files 
            <input type="file" id="file" />
            <!--  <span class="text">Nothing selected</span>  -->                                                              
        </div>
    
        <div class="small-8 column no-padding mr10 pos-rel" align="right">
            <input type="button" class="org-btn reply" value="Send"/>
        </div>
    </div>
    </div>
</div>