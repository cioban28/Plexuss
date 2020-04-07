
<?php 

// dd(get_defined_vars());

?>

<div class="myScholarships" data-oneapp="{{$oneapp_status}}">
	<div class="portal-section-head clearfix show-for-medium-up">
        <div class="portalMainTitle left">Scholarships</div>
        <div class="portalSubTitle left">Apply for scholarships and receive scholarship recommendations</div>
        <div class="show-tutorial right">
            <div class="tutorial-icon"></div>
            <div>SHOW TUTORIAL</div>
        </div>
    </div>


    <?php
    	$count = 0;
    	foreach($scholarships as $i){
    		if($i->status == 'finish') $count++;
    	}
    ?>
    @if($count > 0)
    <div class="howto-box">
		<div class="howto-right">
			<div class="sch-next-btn ">Next</div>
		</div>

		<div class="howto-left">
			You have pending scholarship applications, click next to finish.
		</div>

	</div>
	@endif


	<div class="portal_header_nav clearfix show-for-medium-up" id="move-to-trash-button-div">
        <a href="/scholarships" class="left action addschools">
            <div class="p-icon add"></div>
            <div class="action-name">ADD SCHOLARSHIPS</div>
        </a>
        <div id="trash" class="left action trash" onclick="trashScholarship();">
            <div class="p-icon trash"></div>
            <div class="action-name">MOVE TO TRASH</div>
        </div>
    
    </div>

	<!-- scholarships table -->
    <div class="sch-table-container">
		<div class="sch-table-headers clearfix">
			<div class="sch-col sch-col-name">
				<div class="sch-sort-arrows" data-col="name"><div class="sch-sort-up"></div><div class="sch-sort-down"></div></div>Name
			</div>
			<div class="sch-col sch-col-amount">
				<div class="sch-sort-arrows" data-col="amount"><div class="sch-sort-up"></div><div class="sch-sort-down"></div></div>Amount
			</div>
			<div class="sch-col sch-col-due">
				<div class="sch-sort-arrows"  data-col="due"><div class="sch-sort-up"></div><div class="sch-sort-down"></div></div>Deadline
			</div>
			<div class="sch-col sch-col-add">
				<div class="sch-sort-arrows"  data-col="added"><div class="sch-sort-up"></div><div class="sch-sort-down"></div></div>
				Status
			</div>
			<div class="sch-col sch-col-usd sch-usd-dropdown-btn">
				<div class="sch-drop-down-arrow"></div>
				<span class="sch-usd-img">$</span>
				<span class="sch-usd-txt">USD</span>
				<div class="sch-usd-dropdown">
					<div class="sm-loader mt20"></div>

				</div>
			</div>

		</div>
		<div class="sch-table-content-box">
			
			@foreach($scholarships as $sch)
							<div class="sch-table-result-wrapper " 
							data-sid="{{$sch->scholarship_id}}" 
							data-name="{{$sch->scholarship_title}}" 
							data-provider="{{$sch->company_name}}" 
							data-amount="{{$sch->max_amount}}" 
							data-due="{{$sch->deadline}}" 
							added='false'>
								<div class="sch-table-result clearfix">
									<div class="sch-col sch-col-name clearfix">
										<div class="left">
											<input class="sch-checkbox" type="checkbox" />
										</div>
										<div class="left">
                                            @if (isset($sch->ro_id) && isset($sch->website) && filter_var($sch->website, FILTER_VALIDATE_URL))
                                                <a href="{{$sch->website}}" target="_blank">
                                                    <div class="sch-name sch-linkout">{{$sch->scholarship_name or $sch->company_name}}</div>
                                                </a>
                                            @else
    											<div class="sch-name">{{$sch->scholarship_title or $sch->company_name}}</div>
                                            @endif
											<div class="sch-provider">Scholarship provided by {{$sch->company_name or 'Anonymous'}}</div>

											<div class="sch-view-details">VIEW DETAILS</div> <div class="sch-details-arrow down"></div>
										</div>
										
									</div>
									<div class="sch-col sch-col-amount">
                                        @if (!isset($sch->max_amount) || $sch->max_amount == 0)
                                            <div class="sch-amount">&nbsp;</div>
                                        @else
                                            <div class="sch-amount">${{number_format($sch->max_amount, 2)}}</div>
                                        @endif
									</div>
									<div class="sch-col sch-col-due">
										<div class="sch-due">{{$sch->deadline}}</div>
									</div>
									<div class="sch-col sch-col-add">
										@if(isset($sch->status) && $sch->status == 'finish')
										<div class="sch-status-btn finish" data-state="finish">PENDING</div>
										@else
										<div class="sch-status-btn @if(isset($sch->status)) {{$sch->status}} @else null @endif " 
											 data-state="@if(isset($sch->status)) {{$sch->status}} @else null @endif">
											
											@if(isset($sch->status)) {{strtoupper($sch->status) }} @else + @endif 
									 	</div>
										@endif
										
									 	@if(isset($sch->status) && $sch->status == 'finish')
											<div class="additional-txt">Additional info needed</div>
										@endif 
									
									</div>
									<div class="sch-col sch-col-usd">
										<div class="sch-usd">USD</div>
									</div>
								</div>
								
								<div class="sch-result-details-cont"> 
									<div class='sch-desc-title sch-due-mobile'>Deadline</div>
									<div class='sch-desc  sch-due-mobile'>{{$sch->deadline}}</div>
									<div class="sch-desc-title mt20 ">Description</div>
									<div class="sch-desc">
										{{$sch->description or 'none'}}
									</div>

									<!-- div class="sch-desc-title mt20">Elegibility Requierments</div>
									<ul>
										<li>Must be undergrad student</li>
										<li>must currently attend a university</li>
									</ul -->
								</div>
							</div>
						@endforeach

						@if(count($scholarships) == 0)
							<div class="sch-no-results">No results found</div>
						@endif

		</div>
	</div><!--end table -->

       
</div>
<script>
    $(document).ready(function(){
      $("#trash").css('display','none');
    });

    $('.sch-checkbox').click(function () {
	     	var check = $('.sch-col-name').find('input[type=checkbox]:checked').length;
	     	if(check)
      	{
          $("#trash").css('display','block');
      	}
      	else{
          $("#trash").css('display','none');
      	}
    });
</script>