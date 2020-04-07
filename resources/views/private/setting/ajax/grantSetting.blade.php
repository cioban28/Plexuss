<style>
select{ height:auto; width:200px;}.close-reveal-modal{ font-size:1rem !important; color:#000000 !important; right:-60px !important;}
.toggle-on{ text-indent:10px !important;}.toggle-off{text-indent:20px !important; }
</style>
<div class='rightsidemenu'>    
              
        <div class="row">
        	<span class="icon-arrow account_setting fs22 pl40 mobile-fs20" style="background-position:10px -422px; margin-left:-15px;">Grant Access Settings</span>
                   
            <div class="bdr-b-dot mt10 mr10 mb10 show-for-small-only"></div>
            
            <p class="fs18 c79 mt15 mb0 mobile-fs15 mobile-pr15">Grant Permission to Parents or Counselors </p>
            
            <span class="fs12 c79 l-hght12 mobile-pr15 txt-justify mt15 " style="display:block">
            Call in for back up!  Share your Plexuss acount with a Parent, Counselor, or someone you trust to help you make the best options in getting you recruited. They will receive an email invitation to share your Plexuss account.
            </span>
                     
           <!-- <div align="left" class="mt30"><input type="button" value="Add Parent/Counselor" class="gray-btn" onclick='addParentCounselor(this);'/></div>-->
        
        
            <div class="large-12 parent-table mt10 hide-for-small-only">
                <div class="row" style="border-bottom:#B9BABB solid 1px;">
                    <div class="large-3 medium-3 column div-h">Parent/Counselor Name</div>
                    <div class="large-2 medium-2 column div-h">Relation</div>
                    <div class="large-3 medium-3 column div-h">Email</div>
                    <div class="large-2 medium-2 column div-h">View Only</div> 
                    <div class="large-1 medium-1 column div-h">Full Access</div>
                    <div class="large-1 medium-1 column "></div>
                </div>
                
                <div class="row" style="margin-top:10px;">
                    <div class="large-3 medium-3 column div-txt">Mom</div>
                    <div class="large-2 medium-2 column div-txt">Parent/Counselor</div>
                    <div class="large-3 medium-3 column div-txt">mom@yahoogmail.com</div>
                    <div class="large-2 medium-2 column">{{ Form::checkbox('view-chk', 'value', false);}} </div>
                    <div class="large-1 medium-1 column">{{ Form::checkbox('access-chk', 'value', false);}} </div>
                    <div class="large-1 medium-1 column"> <img src="../images/setting/edit_icon.png"><img src="../images/setting/delete_icon.png" class="pl10"></div>
                </div>    
                
                 <div class="row" align="right" style="padding-right:10px;">
                        <input type="button" value="Add Parent/Counselor" class="gray-btn" onclick='addParentCounselor(this);'/>
                        <input type="button" value="Save Setting" class="org-btn" style="padding:5px;"/>
                </div>                          
            </div>        
        </div>     
        
        
        
        <div class="large-12 parent-table mt10 show-for-small-only" style="margin:5px;">
                <div class="row" style="border-bottom:#B9BABB solid 1px;">
                    <div class="small-7 column div-h">Parent/Counselor Name</div>
                    <div class="small-5 column div-h">Relation</div>                   
                </div>
                
                <div class="row">
                    <div class="small-7 column div-txt">Mom</div>
                    <div class="small-5 column div-txt pr10">Parent/Counselor</div>                   
                </div>
                
                 <div class="row mt25" style="border-bottom:#B9BABB solid 1px;">
                    <div class="small-12 div-h">Email</div>                        
                </div>
                
                <div class="row">
                   <div class="small-12 div-txt">mom@yahoogmail.com</div>              
                </div>
                
                <div class="row mt25">
                    <div class="small-4 column div-h">View Only</div>
                    <div class="small-4 column div-h">Full Access</div>    
                    <div class="small-4 column "></div>                 
                </div>
                
                <div class="row ">
                    <div class="small-4 column"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></div>
                    <div class="small-4 column"><div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div></div>
                    <div class="small-4 column"> <div class="large-1 medium-1 column"> <img src="../images/setting/edit_icon.png"><img src="../images/setting/delete_icon.png" class="pl10"></div></div>                  
                </div>
                
                <div class="row" style="padding-bottom:20px; padding-top:20px;">
                    <div class="small-8 column">
                    	<input type="button" value="Add Parent/Counselor" class="gray-btn" onclick='addParentCounselor(this);'/>
                    </div>
                    <div class="small-4 column div-txt" style="padding-right:10px;">
                    	<input type="button" value="Save Setting" class="org-btn" style="padding:5px;"/>
                    </div>                   
                </div>
                                   
            </div>        
        </div>    
           
</div>

<div class='reveal-modal medium' id="addParentCounselor" style="z-index:1000;" data-reveal>
{{ Form::open(array('url' => "setting/grantSetting/" , 'method' => 'POST', 'id' => 'grantForm')); }}
{{ csrf_field() }}
	<div class="row">
        <div class='viewmode' style='display:block;'>
        	<span class="c-black fs16">Grant Access to Parent / Counselor</span>	        
        </div>
    </div>
    
    <div class="bdr-b-dot mt10 mr10 mb10 show-for-small-only"></div>
    
    <div class="row mt30">
    	<div class="large-4 small-6 column fs12">Name of Parent / Counselor</div>
        <div class="large-6 small-6 column no-padding">{{ Form::text('notes', null, ['class' => 'large_txt']) }}</div>
        <div class="large-2 show-for-large-only column"></div>
    </div>
    
    <div class="row ">
    	<div class="large-4 small-6 column fs12">Relation</div>
        <div class="large-6 small-6 column no-padding styled-select">{{ Form::select('notes',array('' => 'Choose', '1' => 'Father','2'=>'Mother')) }}</div>
        <div class="large-2 show-for-large-only column"></div>
    </div>
    
     <div class="row mt10">
    	<div class="large-4 small-6 column fs12">Parent / Counselor’s email</div>
        <div class="large-6 small-6 column no-padding">{{ Form::text('notes', null, ['class' => 'large_txt']) }}</div>
        <div class="large-2 show-for-large-only column"></div>
    </div>
 	
    <div class="row hide-for-small-only">
        <ul class="chkul">  
            <li>{{ Form::checkbox('profile-chk', 'value', false);}}  <div>They can view  <img src="/images/setting/black_tooltip_icon.png" align="absmiddle" data-tooltip data-options="disable_for_touch:true" class="has-tip" title="Tooltips are awesome, you should totally use them!"/>
          
</div></li>
            <li>{{ Form::checkbox('recommend-chk', 'value', false);}} <div>They can edit &nbsp;<img src="/images/setting/black_tooltip_icon.png" align="absmiddle"/></div></li>
        </ul>
    </div>
    
    
     <div class="row show-for-small-only">
     
     
        <div class="row" style="padding-bottom:10px; padding-top:10px;">
            <div class="small-3 column">
            	<div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div>
            </div>
            <div class="small-9 column fs14" style="padding-right:10px;">
            They can view  <img src="/images/setting/black_tooltip_icon.png" align="absmiddle" data-tooltip data-options="disable_for_touch:true" class="has-tip" title="Tooltips are awesome, you should totally use them!"/>
            </div>                   
        </div>
        
          <div class="row" style="padding-bottom:10px; padding-top:10px;">
            <div class="small-3 column">
            	<div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div>
            </div>
            <div class="small-9 column fs14" style="padding-right:10px;">
            They can view  <img src="/images/setting/black_tooltip_icon.png" align="absmiddle" data-tooltip data-options="disable_for_touch:true" class="has-tip" title="Tooltips are awesome, you should totally use them!"/>
            </div>                   
        </div>
        
          <div class="row" style="padding-bottom:10px; padding-top:10px;">
            <div class="small-3 column">
            	<div class="toggles toggle-light" data-toggle-on="true" data-toggle-height="20" data-toggle-width="50"></div>
            </div>
            <div class="small-9 column fs14" style="padding-right:10px;">
            They can view  <img src="/images/setting/black_tooltip_icon.png" align="absmiddle" data-tooltip data-options="disable_for_touch:true" class="has-tip" title="Tooltips are awesome, you should totally use them!"/>
            </div>                   
        </div>     
     
    </div>
    
    <div class="row">
    	<div class="large-offset-5 large-2 small-12 column close-reveal-modal hide-for-small-only column">X</div>  
    </div>
    
    <div class="row" align="right">
    	<div class="large-10 small-4 column c79 pt5" onclick="closeReveal('addParentCounselor');">Cancle</div>
        <div class="large-2 small-4 column org-btn" onclick="PostGrantInfo();" style="width:100px; padding-top:5px;">Save</div>
    </div>
    
{{ Form::close(); }}
</div>


<script type="text/javascript">
$(document).foundation();
$('.toggles').toggles();	
</script>