<div id="manage-school-div">                        
    <div class="portal_header-div row pos-rel">
     <!---------------------------------------- header menu in mobile view ---------------------------------------->           
    	<div class="show-for-small-only">
        	<div class="heading-tab">SCHOOLS</div>
            <div class="row pt10">
            	<div class="small-7 column c-black no-padding fs13" style="margin-left:-5px;">SCHOOLS VIEWING YOU</div>
                <div class="small-5 column pr30" align="right">
                	
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
                                	<li class="pl15"><img src="/images/nav-icons/trash.png"></li>
                                    <li class="pl15">MOVE TO TRASH</li>
                                </ul>
                            </li> 
                            
                            <li>
                            	<ul class="inline">
                                	<li><img src="/images/nav-icons/compare.png"></li>
                                    <li>COMPARE SCHOOLS</li>
                                </ul>
                            </li> 
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
      <!---------------------------------------- header menu in mobile view ---------------------------------------->  
        <div class="small-6 column no-padding fs21 f-bold clr-green show-for-medium-up">SCHOOLS THAT VIEWED YOUR PROFILE</div>
        <div class="small-6 column no-padding fs16 f-normal clr-green pt5 show-for-medium-up">Say “yes” to add to Your List, or “no” to remove</div>
    </div>
    
    <div class="portal_header_nav show-for-medium-up">                      
        <ul>
            <li class="large-3">RECUITMENT PORTAL</li>
            <li onclick="settingPopup();"><img src="/images/nav-icons/setting-white.png"> SETTINGS</li>                       
            <li onclick="selectCheckbox('trashschool','manageschool','menu4')"><img src="/images/nav-icons/trash-small.png"> MOVE TO TRASH</li>
            <li><img src="/images/nav-icons/compare.png"> COMPARE SCHOOLS</li>
        </ul>
    </div>
    
    <div class="row portal_header-mid">
            <div class="medium-3 small-3 column portal-content-left-side no-padding show-for-medium-up">
                <ul class="left-nav">
                    <li onclick="loadPortalTabs('manageschool','menu1')">
                    	<img src="/images/nav-icons/list.png">
                        <div class="badge">10</div> 
                        <div class="litext">YOUR LIST</div>
                    </li>
                    
                    <li onclick="loadPortalTabs('manageschool','menu2')">
                    	<img src="/images/nav-icons/recurit.png"> 
                        <div class="badge">10</div>
                        <div class="litext">SCHOOLS WANT <br>TO RECRUIT YOU</div>
                    </li>
                    <li onclick="loadPortalTabs('manageschool','menu3')">
                    	<img src="/images/nav-icons/recommended.png">
                        <div class="badge">10</div>
                        <div class="litext">RECOMMENDED <br>BY PLEXUSS</div>
                    </li>
                    <li onclick="loadPortalTabs('manageschool','menu4')" class="left-nav-li-active">
                    	<img src="/images/nav-icons/collegeview.png">
                        <div class="badge">10</div>
                        <div class="litext">COLLEGES <br> VIEWING YOU</div>
                        <div class="fr"><img src="/images/nav-icons/nav_selected.png"></div>
                        <div class="clear"></div>
                    </li>
                    <li onclick="loadPortalTabs('manageschool','menu5')">
                    	<img src="/images/nav-icons/trash-big.png">
                        <div class="badge">10</div>
                        <div class="litext">TRASH</div>
                    </li>
                </ul>
            </div>
            
            <div class="medium-9 small-12 column portal-content-right-side no-padding">
            
                <table cellpadding="0" cellspacing="0" border="0" class="display list-table" id="list-table" width="100%">
                              
                <thead>
                <tr>
                <th>  <input type="checkbox" name="del_chk" id="del_chk"  class="checkall" onclick="checkall(this)"/></th>
                <th width="15%" class="show-for-medium-up">SORT</th>
                <th width="40%">SCHOOL</th>
                <th width="10%">RANK</th>
                <th><span class="show-for-medium-up pl15">WANT TO BE RECRUITED?</span> <span class="show-for-small-only pl40">RESTORE</span></th>          
                </tr>
                </thead>
                                
                <tbody class="pos-rel">
                <div align="center" class="msgloader pt40 d-none pos-abs" style="left:300px; top:100px;"><img src="/images/AjaxLoader.gif"></div>
               
                @if(count($data['listdata'])>0)
                @foreach($data['listdata'] as $key=>$keyval)                    
                   <tr id="trrow_{{$keyval->list_id}}">
                        <td align="center" valign="top">
                        	 <input type="checkbox" name='check_group' value='<?php echo $keyval->list_id.'-'.$keyval->tablename?>' class="check_group"/>
                        </td>
                        <td class="show-for-medium-up" valign="top">
                            <div class="row pt10">                                                	
                                    <div class="college-arrow-up"></div>
                                    <div class="college-arrow-down"></div>
                                    
                                    <div class="fr rank f-bold">#</div>
                                    <div class="clear"></div>
                            </div>
                        </td>
                        <td valign="top">
                            <div class="row">
                                <div class="small-2 medium-3 column no-padding show-for-medium-up">
                                     @if($keyval->logo_url!='')                
                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$keyval->logo_url}}" class="college_logo"/>
                                    @else
                                    <img src="images/no_photo.jpg" class="college_logo"/>
                                    @endif
                                </div>
                                <div class="small-12 medium-9 column pl5">
                                   <span class="c-blue fs14 f-bold"><a href="/college/{{$keyval->id}}">{{$keyval->school_name}}</a></span>
                                    <span class="c79 fs12 d-block mt10 l-hght18">                      	                        
                                        {{$keyval->city}}, {{$keyval->long_state}}  |  
                                          <span class="cursor" onclick="Opendiv(this,'<?php echo $keyval->list_id.'-'.$keyval->tablename?>','manageschool','menu4');">
                                            quick facts 
                                            <span id="quick-link-div-{{$keyval->id}}" class="expand-toggle-span">&nbsp;</span>
                                         </span>
                                    </span>
                                 </div>
                             </div>
                              
                         </td>
                        <td align="center" valign="top"><div class="rankdiv">#{{$keyval->plexuss}}</div></td>
                        <td align="center" valign="top">                                            	 
                             <div class="row">
                                <div class="small-12  medium-3 column no-padding">
                                     <a href="/portal/plexussnotification?college_id=<?php echo $keyval->id?>" data-reveal-id="PlexussNotificationPopup" data-reveal-ajax="true">
                                        <div class="yes">Yes</div>
                                     </a>
                                </div>
                                <div class="small-12  medium-9 column no-padding">  
                                    <div class="no" onclick="trashSchool('<?php echo $keyval->list_id.'-'.$keyval->tablename?>')">No</div>
                                </div>
                             </div>                                                        
                        </td>                                                                     
                    </tr>
                    
                @endforeach 
                @endif
             
                </tbody>
                
                </table>
            </div>            
            <div class="clearfix"></div> 
            
             <!---------------------------------------- footer menu in mobile view ---------------------------------------->            
             <div class="show-for-small-only">
                 <div class="floating-header-mobile">  
                    <ul>
                       <li onclick="loadPortalTabs('manageschool','menu1')"><img src="/images/nav-icons/list.png"></li>
                    	<li onclick="loadPortalTabs('manageschool','menu2')"><img src="/images/nav-icons/recurit.png"></li>
                        <li onclick="loadPortalTabs('manageschool','menu3')"><img src="/images/nav-icons/recommended.png"></li>
                        <li onclick="loadPortalTabs('manageschool','menu4')" class="mobile-menu-nav-active"><img src="/images/nav-icons/collegeview.png"><div class="badge">10</div>  </li>
                        <li onclick="loadPortalTabs('manageschool','menu5')"><img src="/images/nav-icons/trash-small.png"></li>
                    </ul>
                </div>
            </div>
            <!---------------------------------------- footer menu in mobile view ---------------------------------------->  
    </div>  
</div>

<script type="text/javascript" charset="utf-8">
$('#list-table').dataTable( {
		"bProcessing": false,
		"bServerSide": false,
		"responsive": true,
		"bFilter": false,
		"bInfo": false,
		"bSort": true,
		"bPaginate":true,
 	
		"aoColumnDefs": 
		 [
			{ "bSortable": false, "aTargets": [0,1,4] },
		 ],
		"iDisplayLength":10	   
	});
	
</script>