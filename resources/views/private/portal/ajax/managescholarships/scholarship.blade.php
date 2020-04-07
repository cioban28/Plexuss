<style>
#scholarship-table thead tr
{
	background-color:#959697!important
}

#scholarship-table thead tr th
{
	padding:0;
	line-height:0;
	font-size:11px;
	color:#fff;
	border-right:#fff solid 1px;
	height:30px;
	text-align:center
}

#scholarship-tabl table tr td
{
	padding:.522rem .625rem
}
#scholarship-table_length{ display:none}
</style>

<div id="manage-school-div">                        
    <div class="portal_header-div row pos  -rel">
    <!--  header menu in mobile view  -->           
    	<div class="show-for-small-only">

            <div class="row pt15">
            	<div class="small-12 column c-black no-padding fs14n text-center">SCHOLARSHIPS</div>
            </div>
        </div>
      <!--  header menu in mobile view  -->   
        <div class="small-12 column no-padding fs21 f-bold clr-green show-for-medium-up">SCHOLARSHIPS RECOMMENDED BY PLEXUSS</div>
        <?php /*?><div class="small-5 column no-padding show-for-medium-up">        	
            <div>
            	<input type="text" class="search-txt" placeholder="Search my scholarships..." />                
                <div class="go"></div>
            </div>
        </div><?php */?>
    </div>
    
    <div class="portal_header_nav show-for-medium-up">                      
        <ul>
            <li class="large-3">SCHOLARSHIP CENTER</li>
            <li onclick="settingPopup();"><img src="/images/nav-icons/setting-white.png"> SETTINGS</li>  
            <li><img src="/images/nav-icons/calender.png"> ADD DEADLINES</li>                         
            <li onclick="selectCheckbox('trashschool','manageschool','menu3')"><img src="/images/nav-icons/trash-small.png"> MOVE TO TRASH</li>            
        </ul>
    </div>
    
    <div class="row portal_header-mid">
            <div class="medium-3 small-3 column portal-content-left-side no-padding show-for-medium-up">
                <ul class="left-nav">
                    <li onclick="loadPortalTabs('managescholarships','menu1')" class="left-nav-li-active">
                    	<img src="/images/nav-icons/scholership.png">
                        <!--<div class="badge">10</div> -->
                        <div class="litext">SCHOLARSHIPS</div>
                        <div class="fr"><img src="/images/nav-icons/nav_selected.png"></div>
                        <div class="clear"></div>
                    </li>
                                        
                    <li onclick="loadPortalTabs('managescholarships','menu2')">
                    	<img src="/images/nav-icons/trash-big.png">
                        <!--<div class="badge">10</div>-->
                        <div class="litext">TRASH</div>
                    </li>
                </ul>
            </div>
            
            <div class="medium-9 small-12 column portal-content-right-side no-padding">
                <table cellpadding="0" cellspacing="0" border="0" class="display list-table" id="scholarship-table" width="100%">
                
                <thead>
                <tr>
                <th  width="5%" class="sorting_disabled">  <input type="checkbox" name="del_chk" id="del_chk"  class="checkall" onclick="checkall(this)"/></th>
                <th width="40%">
                    <span class="show-for-medium-up pl5">SCHOLARSHIP MATCHES</span>
                    <span class="show-for-small-only pl5">SCHOLARSHIP </span>                     
                 </th>
                <th width="15%">AMOUNT</th>
                <th width="15%" class="show-for-medium-up">DEADLINE</th>
                <th width="10%">STATUS</th>            
                </tr>
                </thead>
            
                <tbody>
                    @if(count($listdata)>0)
                    @foreach($listdata as $key=>$keyval)
                         <tr id="trrow_{{$keyval->list_id}}">
                            <td align="center" valign="middle">
                            	<input type="checkbox" name='check_group' value='<?php echo $keyval->list_id?>' class="check_group"/>
                            </td>
                            
                            <td valign="top">
                                <div class="row"> 
                                       <span class="c-blue fs14 f-bold">{{$keyval->school_name}}</span>
                                       <div class="f-bold show-for-small-only">$1,000</div>
                                        <span class="cursor" onclick="Opendiv(this,'<?php echo $keyval->list_id.'-'.$keyval->tablename?>','managescholarships','menu1');">
                                               &nbsp;Details
                                             <span id="quick-link-div-{{$keyval->id}}" class="expand-toggle-span">&nbsp;</span>
                                        </span>                           
                                 </div>
                                  
                             </td>
                            <td align="center" valign="top" class="show-for-medium-up"><span class="f-bold">$1,000</span></td>
                            <td align="center" valign="top">                                            	 
                                 <span class="c-blue fs16 f-bold">5/22/14</span>  
                                 <img src="/images/nav-icons/calender-blue.png" />                                                                                       
                            </td> 
                            <td align="center" valign="top">  
                                <a href="/applyscholarship?college_id=<?php echo $keyval->id?>" data-reveal-id="applyScholarshipModel" data-reveal-ajax="true">
                               		 <div class="apply_status">Apply</div>
                                </a>
                            </td>                                                                    
                        </tr>
                        
                        
                    @endforeach 
                    @endif
                </tbody>
                
                </table>
            </div>
            
            <div class="clearfix"></div> 
            
             <!--  footer menu in mobile view  -->            
             <div class="show-for-small-only">
                 <div class="floating-header-mobile" align="right">  
                    <ul>
                      <li onclick="loadPortalTabs('managescholarships','menu1')" class="mobile-menu-nav-active"><img src="/images/nav-icons/scholership.png"><div class="badge">10</div>  </li>
                      <li onclick="loadPortalTabs('managescholarships','menu2')" ><img src="/images/nav-icons/trash-small.png"> <div class="badge">10</div> </li>     
                    </ul>
                </div>
            </div>
            <!--  footer menu in mobile view  -->
    </div>  
</div>



 <!-- Apply Scholarship Popup  -->
<div id="applyScholarshipModel" class="reveal-modal medium" data-reveal> <!-- Apply Scholarship--> </div>
<!-- Apply Scholarship Popup  -->


<script type="text/javascript" charset="utf-8">
$('#scholarship-table').dataTable( {
		"bProcessing": false,
		"bServerSide": false,
		"responsive": true,
		"bFilter": true,
		"bInfo": false,
		"bSort": true,
		"bPaginate":true,
 	
		"aoColumnDefs": 
		 [
			{ "bSortable": false, "aTargets": [0,3,4] },
		 ],
		"iDisplayLength":10	   
	});
	
</script>

