@extends('admin.master')
@section('content')
<style>
.row{
	max-width: 100%!important;
}


</style>
<link rel="stylesheet" href="/css/scholarshipsadmin.css?v=1.01" />
<div class="main-admin-adv-filter-container">
  <div class="row">
    <div class="column small-12 large-3 show-for-large-up">&nbsp;</div>
    <div class="column small-12 large-9 small-text-center large-text-left">&nbsp;</div>
  </div>
  <div class="row"> @include('admin.scholarshipcms.leftpanel')
    <div class="column small-12 large-9 small-text-center large-text-left maintabcont" id="add">
      <div class="tab"> <a class="tablinks active" onclick="openTab(event, '1')">Scholarship Information</a> <a class="tablinks" <?php if(isset($scholarship_info->id)){ ?> onclick="openTab(event, '2')" <?php }?> id="targetid">			<span class="tooltip_scholarship">Targeting
	  <?php if(!isset($scholarship_info->id)){ ?><span class="tooltiptext">You need to save the scholarship before adding targeting</span><?php }?></span>
	  </a></div>
      <br />
      <br />
      <div class="tab-content">
        <div class="tabcontent" id="1">
          <div class='row'>
            <div class='small-12 medium-12 large-12 large-centered column'> {{ Form::open(array('url' => '', 'method' => 'POST', 'id' => 'ScholarshipAdminForm' )) }}
              {{ Form::hidden('scholarship_id', isset($scholarship_info->id) ? $scholarship_info->id : 0, array('id' => 'scholarship_id')) }}
              <div class='row'>
                <div class='large-12 column'> {{ Form::textarea('description', isset($scholarship_info->description) ? $scholarship_info->description : null, array('placeholder' => 'Description' ,'class' => '')) }} <span id="description_error" class="error_msg"></span> </div>
              </div>
			  <br />
              <div class='row'>
                <div class='large-12 column'> {{ Form::text('scholarship_title', isset($scholarship_info->scholarship_title) ? $scholarship_info->scholarship_title : null, array('placeholder' => 'Scholarship Title' , 'class' => '')) }} <span id="scholarship_name_error" class="error_msg"></span> </div>
              </div>
              <div class='row'>
                <div class='large-12 column'> {{ Form::text('scholarship_subtitle', isset($scholarship_info->scholarshipsub_title) ? $scholarship_info->scholarshipsub_title : null, array('placeholder' => 'Scholarship SubTitle' , 'class' => '')) }} <span id="scholarshipsub_name_error" class="error_msg"></span> </div>
              </div>
              <div class='row'>
                <div class='large-1 column text-right'>$</div><div class='large-5 column'>{{ Form::text('amount', isset($scholarship_info->max_amount) ? $scholarship_info->max_amount : null, array('placeholder' => 'Amount' , 'class' => '' , 'onkeypress' => "return isNumberKey(event)")) }} <span id="amount_error" class="error_msg"></span> </div>
                <div class='large-6 column'> {{ Form::text('deadline', isset($scholarship_info->deadline) ? $scholarship_info->deadline : null, array('id' => 'datepicker', 'placeholder' => 'Deadline' , 'class' => '')) }} <span id="deadline_error" class="error_msg"></span> </div>
              </div>
              <div class='row'>
                <div class='text-right small-12 large-6 large-push-6 column'> {{ Form::button('Save', array('class'=>'button','id' => "scholarshipAdmBtn")) }} </div>
              </div>
              {{ Form::close() }} </div>
          </div>
        </div>
        <div class="tabcontent" id="2" style="display:none;">
          <div class="row hide-for-large-up">
            <div class="column small-12 text-center">
              <div class="filter-page-indicator">Welcome to your advanced filter</div>
              <div class="select-filter-btn-sm">Select another filter <span class="filter-menu-arrow">&dtrif;</span></div>
              <div class="filtering-menu-sm-container">
                <!-- adv filtering side nav - start -->
                <ul class="side-nav adv-filtering-menu" data-locked="@if(isset($show_upgrade_button) && $show_upgrade_button == 1) 1 @else 0 @endif">
                  <li data-filter-tab="location"> <a href="">Location</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="startDateTerm"> <a href="">Start Date</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="financial"> <a href="">Financials</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="typeofschool"> <a href="">Type of School</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="major"> <a href="">Major</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="scores"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Scores</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="uploads"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Uploads</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="demographic"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Demographic</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="educationLevel"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Education Level</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="militaryAffiliation"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Military Affiliation</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="profileCompletion"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Profile Completion</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                </ul>
                <!-- adv filtering side nav - end -->
              </div>
            </div>
          </div>
          <div class="row common-container-for-filter-sections">
            <div class="column small-12 large-3 show-for-large-up">
              <div class="adv-filtering-menu-container">
                <!-- adv filtering side nav - start -->
                <ul class="side-nav adv-filtering-menu" data-locked="@if(isset($show_upgrade_button) && $show_upgrade_button == 1) 1 @else 0 @endif">
                  <li data-filter-tab="location" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> <a href="">Location</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="startDateTerm" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> <a href="">Start Date</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="financial" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> <a href="">Financials</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="typeofschool" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> <a href="">Type of School</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="major" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> <a href="">Major</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="scores" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Scores</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="uploads" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Uploads</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="demographic" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Demographic</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="educationLevel" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Education Level</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="militaryAffiliation" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Military Affiliation</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                  <li data-filter-tab="profileCompletion" data-schol-id="{{isset($scholarship_info->id) ? $scholarship_info->id : 0}}"> @if(isset($show_upgrade_button) && $show_upgrade_button == 1) <span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span> @endif <a href="">Profile Completion</a>
                    <div class="change-icon hide">&#x02713;</div>
                  </li>
                </ul>
                <!-- adv filtering side nav - end -->
              </div>
            </div>
            <div class="column small-12 large-9">
              <div class="video-container"> </div>
              <div class="filter-crumbs-container">
                <ul class="inline-list filter-crumb-list">
                  <!-- crumb tags get injected here -->
                </ul>
              </div>
              <div class="row recomm-meter-container hidden">
                <div class="column small-12">
                  <div class="recomm-meter-msg">This meter shows if you are filtering too much. More filters could result in less recommendations.</div>
                  <div class="radius progress"> <span class="meter" style="width: {{$filter_perc or 100}}%"></span> </div>
                  <div class="recomm-meter-descrip"><span>|&nbsp;&nbsp;&nbsp;&nbsp;Fewer recommendations</span> <span>&nbsp;&nbsp;More recommendations&nbsp;&nbsp;&nbsp;&nbsp;|</span></div>
                </div>
              </div>
              <div class="adv-filtering-section-container">
                <!-- adv filter section will get ajax in here -->
                <div class="row filter-intro-container" data-equalizer>
                  <div class="column small-12 medium-4">
                    <div class="filter-intro-step" data-equalizer-watch>
                      <div class="text-center">1</div>
                      <div class="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-1-filter.png" alt="Plexuss"> </div>
                      <div> You receive student recommendations daily, but you're looking for certain kinds of students </div>
                    </div>
                  </div>
                  <div class="column small-12 medium-4">
                    <div class="filter-intro-step" data-equalizer-watch>
                      <div class="text-center">2</div>
                      <div class="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-2-filter.png" alt="Plexuss"> </div>
                      <div> Choose what you'd like to filter by and save your changes (menu on the left) </div>
                    </div>
                  </div>
                  <div class="column small-12 medium-4">
                    <div class="filter-intro-step" data-equalizer-watch>
                      <div class="text-center">3</div>
                      <div class="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-3-filter.png" alt="Plexuss"> </div>
                      <div> Based on your filters, you will receive recommendations that may be a better fit for your school </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="column small-12 large-9 text-right reset-save-filters-col hidden" style="padding: 16px 80px 20px 20px;"> <span class="reset-filters-btn">Reset this filter</span> <span class="save-filters-btn">Save</span> </div>
            <div class="column small-12 text-right"> <a class="targeting-done-btn" href="/admin/dashboard">
              <div>All done, take me to my dashboard</div>
              </a> </div>
          </div>
        </div>
      </div>
    </div>
    <div class="column small-12 large-9 small-text-center large-text-left maintabcont" id="list" style="display:none;">
      <div class="sch-table-container">
        <div class="sch-table-headers clearfix">
          <div class="sch-col sch-col-name"> Title </div>
          <div class="sch-col sch-col-due"> Amount </div>
          <div class="sch-col sch-col-add"> Deadline </div>
          <div class="sch-col sch-usd-txt"> Edit | Delete</div>
          
        </div>
        <div class="sch-table-content-box"> @foreach($scholarships as $sch)
          <div class="sch-table-result-wrapper" id="scholDiv_{{$sch->id}}">
            <div class="sch-table-result clearfix">
              <div class="sch-col sch-col-name">
                <div class="sch-name">{{$sch->scholarship_name}}</div>
                <div class="sch-provider">{{$sch->scholarshipsub_name}}</div>
              </div>
              <div class="sch-col sch-col-amount">
                <div class="sch-amount">${{number_format($sch->amount, 2)}}</div>
              </div>
              <div class="sch-col sch-col-due">
                <div class="sch-due">{{$sch->deadline}}</div>
              </div>
              <div class="sch-col sch-col-add">
                <div class="sch-due"><a href="/admin/tools/scholarshipcms/{{$sch->id}}">Edit</a> | <a href="javascript:void(0);" onclick="DeltRecord({{$sch->id}})">Delete</a></div>
              </div>
              
            </div>
          </div>
          @endforeach
          
          @if(count($scholarships) == 0)
          <div class="sch-no-results">No results found</div>
          @endif </div>
      </div>
    </div>
  </div>
</div>
<!-- dialog modal -->
<div id="filter-dialog-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="row">
    <div class="column small-10 small-offset-1 text-center dialog-msg">Save before leaving?</div>
    <div class="column small-1"> <a class="close-reveal-modal" aria-label="Close">&#215;</a> </div>
    <div class="column small-4">
      <div class="text-center save">Save</div>
    </div>
    <div class="column small-4">
      <div class="text-center discard">Discard</div>
    </div>
    <div class="column small-4">
      <div class="text-center cancel">Close</div>
    </div>
  </div>
</div>
<!-- ajax loader -->
<div class="text-center targeting-ajax-loader">
  <svg width="70" height="20">
    <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
      <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
    </rect>
    <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
      <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
    </rect>
    <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
      <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
      <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
    </rect>
  </svg>
</div>
<script>
	function isNumberKey(evt){
   		var charCode = (evt.which) ? evt.which : event.keyCode
    	if (charCode > 31 && (charCode < 48 || charCode > 57))
        	return false;
    	return true;
	}
	$(function() {
    	$( "#datepicker" ).datepicker({
      		changeMonth: true,
      		changeYear: true,
			dateFormat: 'mm/dd/yy',
			minDate : 0
    	});
  	});
  
	function openTab(evt, cName) {
		var i, tabcontent, tablinks;
		tabcontent = document.getElementsByClassName("tabcontent");
		for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		}
		tablinks = document.getElementsByClassName("tablinks");
		for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		}
		document.getElementById(cName).style.display = "block";
		evt.currentTarget.className += " active";
	}
	
	function openmainTab(evt, cName){
		var i, tabcontent, tablinks;
		tabcontent = document.getElementsByClassName("maintabcont");
		for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		}
		tablinks = document.getElementsByClassName("bck-button");
		for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		}
		document.getElementById(cName).style.display = "block";
		evt.currentTarget.className += " active";
	}
	
	 $(document).on('click', '#scholarshipAdmBtn', function(){
     	var input = $('#ScholarshipAdminForm').serialize();
		$("#scholarshipAdmBtn").html('Saving...');
		$.ajax({
            url: '/admin/tools/scholarshipcms/addScholarshipcms',
            type: 'POST',
            data: input,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            success: function(data){
               	//console.log(data);
			    $("#scholarship_name_error").html(data.scholarship_name_error);
				$("#scholarshipsub_name_error").html(data.scholarshipsub_name_error);
				$("#amount_error").html(data.amount_error);
				$("#deadline_error").html(data.deadline_error);
				$("#description_error").html(data.description_error);
				if(data.success == "success" && data.scholarship_id!=0){
					if(data.operation == "add"){
						window.location = "/admin/tools/scholarshipcms/"+data.scholarship_id;
					}
					//$('#targetid').attr('onclick','openTab(event, "2")');
				}
				$("#scholarshipAdmBtn").html('Save');
			 },
            error: function(err){
            	console.log(err);
				$("#scholarshipAdmBtn").html('Save');
            }
        });
    });
			
	function DeltRecord(schid){
		$suretdel = confirm("Are you sure to delete this record ?");
		if($suretdel == true){
			$.ajax({
            	url: '/admin/tools/scholarshipcms/delScholarshipAdmin',
            	type: 'POST',
            	data: {'id' : schid},
            	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
           	 	success: function(data){
               		if(data=="success"){
						$("#scholDiv_"+schid).remove();
					}
			 	},
            	error: function(err){
            		console.log(err);
				}
        	});
		}
	}
	
	</script> 


@stop