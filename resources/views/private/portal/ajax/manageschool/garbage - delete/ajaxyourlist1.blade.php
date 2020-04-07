
<div id="manage-school-div">                        
    <div class="portal_header-div row pos-rel">
     <!---------------------------------------- header menu in mobile view ---------------------------------------->           
    	<div class="show-for-small-only">
            <div class="row pt15">
            	<div class="small-6 column c-black no-padding fs14" style="margin-left:-5px;">YOUR COLLEGE LIST</div>
                <div class="small-6 column pr30" align="right">
                	
                    <div class="cursor" onClick="expandDivContent('expand-mobile-menu','menu-nav-div');">
                    	 <img src="/images/nav-icons/setting-gray.png">&nbsp;<span class="c79 f-bold fs12">Actions</span>
                         <span class="expand-toggle-span" id="expand-mobile-menu">&nbsp;</span>
                     </div>
                    
                    <div class="menu-nav-div d-none" id="menu-nav-div">
                     <div class="menu-nav-div-arrow" style="top:-18px"></div>
                    	<ul class="mobile-top-nav">
                            <li>
                            	<ul class="inline" onclick="settingPopup();">
                                	<li class="pl15"><img src="/images/nav-icons/setting-white.png"></li>
                                    <li class="pl12">SETTINGS</li>
                                </ul>
                            </li> 
                            
                            <li>
                            	<ul class="inline" onclick="addSchoolPopup();">
                                	<li class="pl20"><span class="f-blod fs20">+</span></li>
                                    <li class="pl15">SCHOOLS</li>
                                </ul>
                            </li> 
                            
                            <li>
                            	<ul class="inline">
                                	<li class="pl15"><img src="/images/nav-icons/calender.png"></li>
                                    <li class="pl10">ADD DEADLINES</li>
                                </ul>
                            </li> 
                            
                            <li>
                            	<ul class="inline">
                                	<li class="pl15"><img src="/images/nav-icons/trash-small.png"></li>
                                    <li class="pl15" onclick="selectCheckbox('trashschool','manageschool','menu1')">MOVE TO TRASH</li>
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
    
        <div class="small-3 column no-padding fs21 f-bold clr-green show-for-medium-up">YOUR COLLEGE LIST</div>
        <div class="small-9 column no-padding fs16 f-normal clr-green pt5 show-for-medium-up">Manage the schools you have requested to be recruited by</div>
    </div>
    
    <div class="portal_header_nav show-for-medium-up">                      
        <ul>
            <li class="large-3">RECUITMENT PORTAL</li>
            <li onclick="settingPopup();"><img src="/images/nav-icons/setting-white.png"> SETTINGS</li> 
            <li onclick="addSchoolPopup();"><span class="f-blod fs18">+</span> SCHOOLS</li>  
            <li><img src="/images/nav-icons/calender.png"> ADD DEADLINES</li>                        
            <li onclick="selectCheckbox('trashschool','manageschool','menu1')"><img src="/images/nav-icons/trash-small.png"> MOVE TO TRASH</li>
            <li><img src="/images/nav-icons/compare.png"> COMPARE SCHOOLS</li>
        </ul>
    </div>
    
    <div class="row portal_header-mid">
            <div class="medium-3 small-3 column portal-content-left-side no-padding show-for-medium-up">
                <ul class="left-nav">
                    <li onclick="loadPortalTabs('manageschool','menu1')" class="left-nav-li-active">
                    	<img src="/images/nav-icons/list.png">
                        <div class="badge">10</div> 
                        <div class="litext">YOUR LIST</div>
                        <div class="fr"><img src="/images/nav-icons/nav_selected.png"></div>
                        <div class="clear"></div>
                    </li>
                    
                    <li onclick="loadPortalTabs('manageschool','menu2')">
                    	<img src="/images/nav-icons/recurit.png">                      
                        <div class="litext">SCHOOLS WANT <br>TO RECRUIT YOU</div>
                    </li>
                    <li onclick="loadPortalTabs('manageschool','menu3')">
                    	<img src="/images/nav-icons/recommended.png">                      
                        <div class="litext">RECOMMENDED <br>BY PLEXUSS</div>
                    </li>
                    <li onclick="loadPortalTabs('manageschool','menu4')">
                    	<img src="/images/nav-icons/collegeview.png">                       
                        <div class="litext">COLLEGES <br> VIEWING YOU</div>
                    </li>
                    <li onclick="loadPortalTabs('manageschool','menu5')">
                    	<img src="/images/nav-icons/trash-big.png" class="pl">                       
                        <div class="litext">TRASH</div>
                    </li>
                </ul>
            </div>
             
            <div class="medium-9 small-12 column portal-content-right-side " id="content-list-div">
            
                
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="list-table" width="100%">
           	  
            
            
            <thead>
            <tr>
            <th>  <input type="checkbox" name="del_chk" id="del_chk" onclick="return checkall(this)" /></th>
            <th width="15%">SORT</th>
            <th width="40%">SCHOOL</th>
            <th width="10%">RANK</th>
            <th>STATUS</th>
            <th>MESSAGE</th>
            </tr>
            </thead>
            
            
          	  <tbody> 
            @if(count($listdata)>0)
            @foreach($listdata as $key=>$keyval)  
            {{-- $trcolor ='' --}}
            <?php					
            if($keyval->status=='applied'){$trcolor='#EAF6FC';}
            elseif($keyval->status=='accepted'){$trcolor='#E9F7ED';}
            else{$trcolor = '#ffffff';}
            ?>            
            <tr style="background-color:<?php echo $trcolor;?>" id="trrow_{{$keyval->list_id}}">
            <td>
            <input type="checkbox" name='check_group' value='<?php echo $keyval->list_id?>' class="check_group"/>
            </td>
            <td align="left" valign="top" class="show-for-medium-up">
            <div class="row pt10">                                                	
            <div class="college-arrow-up"></div>
            <div class="college-arrow-down"></div>
            
            <div class="rank f-bold mr10">#1</div>
            <div class="clear"></div>
            </div>
            </td>
            <td>
            <div class="row">
            <div class="small-2 medium-3 column no-padding show-for-medium-up">                                 
            @if($keyval->logo_url!='')                
            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$keyval->logo_url}}" class="college_logo"/>
            @else
            <img src="images/no_photo.jpg" class="college_logo"/>
            @endif
            </div>
            <div class="small-12 medium-9 column pl5 pos-rel">
            <div class="c-blue fs14 f-bold">{{$keyval->school_name}}<div class="status_green"><!--------></div></div> 
            <span class="c79 fs12 d-block">                      	                        
                {{$keyval->city}}, {{$keyval->long_state}}  |  
                <span onClick="expandDivContent('quick-link-div-{{$keyval->id}}','quick-link-{{$keyval->id}}');" class="cursor expandDivContent">
                    quick facts 
                    <span id="quick-link-div-{{$keyval->id}}" class="expand-toggle-span">&nbsp;</span>
                 </span>
            </span>
            </div>
            </div>
            
            </td>
            <td ><div class="rankdiv">#{{$keyval->plexuss}}</div></td>
            <td class="pos-rel">  
            <span class="cursor c79 fs12" id="status_val{{$keyval->list_id}}" onClick="expandDivContent('status-span-{{$keyval->list_id}}','menu-nav-div-{{$keyval->list_id}}');" style="text-transform:capitalize">
            @if($keyval->status!='')                
                {{$keyval->status}}
            @else
                None
            @endif
            
             <span class="expand-toggle-span" id="status-span-{{$keyval->list_id}}"></span>
            </span>
            <div class="cursor c79 fs12 d-none menu-nav-div" id="menu-nav-div-{{$keyval->list_id}}">   
            <div class="menu-nav-div-arrow" style="top:-10px"></div>
            
            <ul class="mobile-top-nav pos-rel">
                <div align="center" class="msgloader pt40 d-none pos-abs" style="left:55px; top:-5px;"><img src="/images/AjaxLoader.gif"></div>
                <li class="pl15 pt5" onclick="setlistStatus('1',{{$keyval->list_id}})">APPLIED</li>
                <li class="pl15 pt5" onclick="setlistStatus('2',{{$keyval->list_id}})">ACCEPTED</li>
                <li class="pl15 pt5" onclick="setlistStatus('3',{{$keyval->list_id}})">JUST LOOKING</li>
            </ul>     
            </div>                                                        
            </td>
            <td  class="show-for-medium-up"><img src="/images/nav-icons/massage-gray.png"></td> 
            </tr>
                   
            @endforeach 
            @endif                               
            </tbody>
            </table>              
             
             
             
            </div>
            
            <div class="clearfix"></div>  
             <!---------------------------------------- footer menu in mobile view ---------------------------------------->            
             <div class="show-for-small-only" align="left">           		
                 <div class="floating-header-mobile">  
                    <ul>
                        <li onclick="loadPortalTabs('manageschool','menu1')" class="mobile-menu-nav-active"><img src="/images/nav-icons/list.png"><div class="badge">10</div>  </li>
                        <li onclick="loadPortalTabs('manageschool','menu2')"><img src="/images/nav-icons/recurit.png"> </li>
                        <li onclick="loadPortalTabs('manageschool','menu3')"><img src="/images/nav-icons/recommended.png"></li>
                        <li onclick="loadPortalTabs('manageschool','menu4')"><img src="/images/nav-icons/collegeview.png">  </li>
                        <li onclick="loadPortalTabs('manageschool','menu5')"><img src="/images/nav-icons/trash-big.png">  </li>
                    </ul>
                </div>
            </div>
            <!---------------------------------------- footer menu in mobile view ---------------------------------------->  
                                     
    </div>  
</div>


<!---------------------------------------- Add School Model ----------------------------------------> 
<div id="portalAddSchoolModel" class="reveal-modal medium radius10 mt10" data-reveal>
<!--<iframe src="/addschool" class="event-frame large-12"></iframe>-->
<div class="row pos-rel">
<div align="center" class="msgloader pt40 d-none pos-abs" style="left:350px; top:100px; z-index:1000"><img src="/images/AjaxLoader.gif"></div>
    <div class="row fs30 f-normal mb10 mt10">
		Add schools to your list
    </div>

    <div class="row">
    <div class="fs16 c79 pt10">Which college do you want to be recruited by?</div>               			
    
    <div class="mt20 mb20">   
        <div class="row mb20">         
            <div class="small-7 medium-10 column no-padding">
             {{-- */$nval = '1'/* --}}
               <form name="addschool_form" id="addschool_form">
                <table id="dataTable" width="100%" border="0" align="left" style="border:none">
                    <tr id="{{$nval}}">
                    <td width="90%">
                    	<input type="text" name="addschool[]" id="addschool_{{$nval}}" placeholder="Start typing college name" >
                    	<input type="hidden" name="college_id[]" id="college_id_{{$nval}}" value="" class="addschool-txt">                
                    </td> 
                    <td width="10%"><font style="font-size:0px">Delete</font></td>                          
                     <input type="hidden" name="rowcountvar" id="rowcountvar" value="1">             		
                    </tr>
                </table>
                </form>                
              <!-- <input type="text" name="addschool" placeholder="Start typing college name">-->
            </div>
        
        	<div class="small-5 medium-2 column pt10">
                <input type="button" class="gray-btn f-bold" value="+ another school" style="font-size:11px; font-weight:bold;" onclick="addRow('dataTable')">    
            </div>
        </div>                   
    </div>
   
    <div class="mb20 mt20">
        <div class="c79 fs14" align="right">
            <span onClick="$('#portalSettingModel').foundation('reveal', 'close');">Cancle</span> 
            <input type="button" value="Add to my list" class="org-btn ml10" onClick="addSchoolList();" style="cursor:pointer" /></div>    
        </div>
    </div>
</div>
<a class="close-reveal-modal c-black">&#215;</a>
</div>
<!---------------------------------------- Add School Model ---------------------------------------->  

<script type="text/javascript" charset="utf-8">
var table=$('#list-table').dataTable( {
		"bProcessing": false,
		"bServerSide": false,
		"responsive": true,
		"bFilter": false,
		"bInfo": false,
		"bSort": true,
		"bPaginate":true,
 	
		"aoColumnDefs": 
		 [
			{ "bSortable": false, "aTargets": [0,1,5] },
		 ],
		"iDisplayLength":20	   
	});
	
	
function format ( d ) {
// `d` is the original data object for the row
	var retrun_val = '';						
	retrun_val +=  '<tr class="subtr"><td colspan="6"><table cellpadding="5" cellspacing="0" border="0" align="center">';	
	retrun_val +=  '<tr>';
	retrun_val +=  '<td> text come here';
	retrun_val +=  '<div class="row cursor" align="center"><span class="expand-toggle-span" id="quick-link-div-1">&nbsp;</span><span class="fs10 pl20">Close</span></div>';
	retrun_val +=  '<td>';	
	retrun_val +=  '<tr>';	
	retrun_val +=  '</table></td></tr>';		
	retrun_val +=  '</div><div class="clearfix"></div></div></div></div>';		
	return retrun_val;
}

    $('.expandDivContent').click(function () {
		var parTr = $(this).parents("tr");
        
            // Open this row
			if($(parTr).next("tr").hasClass("subtr")){
				$(parTr).next("tr").remove();
				return;
			}
            $(format()).insertAfter(parTr);

        
    } );
	
</script>