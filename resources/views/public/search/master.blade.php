<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('private.headers.header') 
</head>

<body id="{{$currentPage}}" 
      @if ($type == 'state')
        style="background: url({{$background_image}}) no-repeat center center fixed;"
      @endif
      class="@if($type == 'majors' || $type == 'college') college-majors @endif">
        @include('private.includes.topnav')
        <div class="row collapse mt10">



<?php 
    // dd(get_defined_vars());
?>      
        {{-- */$city = isset($querystring['city'])?$querystring['city']:''/* --}}
        {{-- */$state = isset($querystring['state'])?$querystring['state']:''/* --}}
        {{-- */$locale = isset($querystring['locale'])?$querystring['locale']:''/* --}}
        {{-- */$religious_affiliation = isset($querystring['religious_affiliation'])?$querystring['religious_affiliation']:''/* --}}
        
        {{-- */$miles_range_min_val = isset($querystring['miles_range_min_val'])?$querystring['miles_range_min_val']:'0'/* --}}
        {{-- */$miles_range_max_val = isset($querystring['miles_range_max_val']) && $querystring['miles_range_max_val']!=''?$querystring['miles_range_max_val']:250/* --}}
        
        {{-- */$tuition_max_val = isset($querystring['tuition_max_val']) && $querystring['tuition_max_val']!=''?$querystring['tuition_max_val']:20000/* --}}
        
        {{-- */$enrollment_min_val = isset($querystring['enrollment_min_val'])?$querystring['enrollment_min_val']:'0'/* --}}
        {{-- */$enrollment_max_val = isset($querystring['enrollment_max_val']) && $querystring['enrollment_max_val']!=''?$querystring['enrollment_max_val']:5000/* --}}
        
        {{-- */$applicants_min_val = isset($querystring['applicants_min_val'])?$querystring['applicants_min_val']:'0'/* --}}
        {{-- */$applicants_max_val = isset($querystring['applicants_max_val']) && $querystring['applicants_max_val']!=''?$querystring['applicants_max_val']:5000/* --}}
        

        <!-- if search result is majors -- have naviagtion filters for colleges -->
        <?php 
            $majors_top_filter = 'all'

        ?>
       
        	<!-- left Panel -->
            @if($type=='college' || $type=='majors' || $type=='state')           
            <div class="hide-for-small-only medium-4 large-3 columns side-bar-1" id="filter-search-div">

               
                     @if($type == 'majors' || $type == 'college' || $type == 'state')
                         <a href="/college" class="majors-back-btn">
                            <span class="majors-back-arrow">&lsaquo;</span> Back
                         </a>
                     @endif
                

                @include('private.includes.searchLeft')
            </div>
            @endif
            
            <!-- Right Side Part -->
            <div class="small-12 medium-8 large-9 columns @if($type !== 'state') @endif">

                <div class='columns small-12 text-center'>
                    <div class="center-college-nav-ranking mt30">
                    @include('private.college.collegeNav')
                    </div>
                </div>
              
                <div class="row">
                    <div class="column small-12">
                        @yield('content')
                    </div>
                </div>
               <?php  
                   // @if($type == 'majors' || $type == 'college')
                   //      <div class="filter-colleges-majors-nav">
                   //          <div class="filter-colleges-majors-type  
                   //                          @if( (isset($degree_type) && $degree_type == '-1' && $type == 'majors') ||
                   //                          (isset($querystring['degree']) && $querystring['degree'] == 'all' && $type == 'college') ||
                   //                          (!isset($degree_type) && !isset($querystring['degree'])) ) active @endif">
                   //              All
                   //          </div>
                   //          <div class="filter-colleges-majors-type 
                   //                      @if((isset($degree_type) && $degree_type == '3') || 
                   //                      (isset($querystring['degree']) && $querystring['degree'] == 'bachelors_degree' && $type == 'college'))  active @endif">
                   //              Bachelors
                   //          </div>
                   //          <div class="filter-colleges-majors-type 
                   //                      @if((isset($degree_type) && $degree_type == '4') ||
                   //                      (isset($querystring['degree']) && $querystring['degree'] == 'masters_degree'  && $type == 'college')) active @endif">
                   //              Masters
                   //          </div>
                   //      </div> 
                   //  @endif
                ?>
            </div>
            
             @if($type!='college' && $type!='majors' && $type!='state')  
                <!-- Right Panel -->   
                @if( isset($signed_in) && $signed_in == 0  )  
                <div class="hide-for-small-only medium-4 large-3 columns side-bar-1 page-right-side-bar radius-4 pt10">  
                    <div class="text-center"><p class="step-number">1</p></div>
                    <p class="right-bar-heading white">Get Started</p>
                    <p class="right-bar-para white">Wondering why your indicators are at zero?</p>
                    <p class="right-bar-para white">You need a profile for the recruitment process to begin.</p>
                    <div class="large-12 text-center"><a class="button get-started-button" href="/profile">Start your Profile</a></div>  
                </div>
                @endif
                
                <div class="large-3 columns right-side-footer">
					@include('private.includes.right_side_footer')
                </div>
                <!-- Right Part -->  
             @endif    
        </div>
      
        @include('private.footers.footer')
   
    </body>
</html>

<script type="text/javascript">
$(document).ready(function(e) {
	
	RangeSliderMax('slider-range-1','tuition_range','tuition_max_val','{{$querystring['tuition_max_val or'] or ''}}');
	RangeSlider('slider-range-0','miles_range','miles_range_min_val','miles_range_max_val','{{$querystring['miles_range_min_val'] or ''}}','{{$querystring['miles_range_max_val'] or ''}}');
	RangeSlider('slider-range-2','enrollment_range','enrollment_min_val','enrollment_max_val','{{$querystring['enrollment_min_val'] or ''}}','{{$querystring['enrollment_max_val'] or ''}}');
	RangeSlider('slider-range-3','applicants_range','applicants_min_val','applicants_max_val','{{$querystring['applicants_min_val'] or ''}}','{{$querystring['applicants_max_val'] or ''}}');
	
	AjaxSelectBox('country','getSelectBoxVal','country-select-box','{{isset($search_array['country']) ? $search_array['country'] : ''}}');

    if ("{{isset($search_array['country']) ? $search_array['country'] : ''}}" == 'US' || "{{isset($search_array['country']) ? $search_array['country'] : ''}}" == "") {
    	AjaxSelectBox('city','getSelectBoxVal','city-select-box','{{$querystring['city'] or ''}}','{{$querystring['state'] or ''}}');
    	AjaxSelectBox('state','getSelectBoxVal','state-select-box','{{isset($search_array['state']) ? $search_array['state'] : ''}}','');
    }


	AjaxSelectBox('locale','getSelectBoxVal','locale-select-box','{{$querystring['locale'] or ''}}','');
	AjaxSelectBox('religious_affiliation','getSelectBoxVal','religious-select-box','{{$querystring['religious_affiliation'] or ''}}','');

    if ("{{isset($search_array['country']) ? $search_array['country'] : ''}}") {
        CheckisUs("{{isset($search_array['country']) ? $search_array['country'] : ''}}");
    }

});

function CheckisUs(country_name){
	if(country_name !="US"){
		$("#state_div").html('<input name="state" type="text" value="" placeholder="State Name" id="state-select-box" />');
		$("#city_div").html('<input name="city" type="text" value="" placeholder="City Name" id="city-select-box" />');
	}else{
		$("#state_div").html('<select class="styled-select" id="state-select-box" name="state"><option value="0">No preference</option></select>');
		$("#state-select-box").attr('onchange','AjaxSelectBox("city","getSelectBoxVal","city-select-box","",this.value);');
		$("#city_div").html('<select class="styled-select" id="city-select-box" name="city"><option value="">No preference</option></select>');
		AjaxSelectBox('state','getSelectBoxVal','state-select-box','{{$search_array['state'] or ''}}');
	}
}
$(document).foundation();
</script>
