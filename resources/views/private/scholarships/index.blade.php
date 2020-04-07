<?php

// dd(get_defined_vars());

?>


<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
		<!--ssss -->
		@include('private.includes.topnav')


		<div id="_ScholarshipsPage" class="scholarships-container clearfix"
			 data-oneapp="{{$oneapp_status}}"
			 data-uid="{{$user_id}}"
			 data-fcount="{{$fincount}}">

			<!-- scholarships left side - filters -->
			<div class="sch-filter-container">
				<div class="sch-filter-title">Scholarships Filter</div>
				<div class="sch-filter-amt-title">Amount</div>

				<div id="schFilterCont" class="sch-amounts-filters">
					<div><a href="/scholarships?rangeF=0,5000" class="@if(isset($rangeF) && $rangeF == '0,5000' ) active @endif">Below 5K</a></div>
					<div><a href="/scholarships?rangeF=5000,10000" class="@if(isset($rangeF) && $rangeF == '5000,10000' ) active @endif">5K &mdash; 10K</a></div>
					<div><a href="/scholarships?rangeF=10000,-1" class="@if(isset($rangeF) && $rangeF == '10000,-1' ) active @endif">10K &amp; Up</a></div>
				</div>


				<div id="clearSchFilter"><a href="/scholarships">Clear Filter</a></div>

			</div>


			<!-- scholarships right side -->
			<div class="sch-content-container">
				@include('private.college.collegeNav')


				<div class="howto-box">
					<div class="howto-right">
						<div class="sch-next-btn @if(!isset($fincount) || $fincount < 1) disabled @endif ">Next</div>
					</div>

					<div class="howto-left">
						<div class="sch-num-box">0</div>
						<span class="sch-sel-txt">Select scholarships below and click next to apply.</span>



						<!-- <div class="sch-finish-msg @if(!isset($fincount) || $fincount < 1) hide @endif">
							Your scholarship application is incomplete.
						</div> -->


					</div>
				</div>


				<!-- scholarships table -->
				<div class="sch-table-container" data-signin={{$signed_in}} data-uid="{{$user_id}}">

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
							<div class="sch-sort-arrows"  data-col="added"><div class="sch-sort-up"></div><div class="sch-sort-down"></div></div>Add
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
							data-sid="{{$sch->id}}"
							data-name="{{$sch->scholarship_name}}"
							data-provider="{{$sch->provider_name}}"
							data-amount="{{$sch->amount}}"
							data-due="{{$sch->deadline}}"
							added='false'>
								<div class="sch-table-result clearfix">
									<div class="sch-col sch-col-name">
                                        @if (isset($sch->ro_id) && isset($sch->website) && filter_var($sch->website, FILTER_VALIDATE_URL))
                                            <a href="{{$sch->website}}" target="_blank">
                                                <div class="sch-name sch-linkout">{{$sch->scholarship_name or $sch->provider_name}}</div>
                                            </a>
                                        @else
										  <div class="sch-name">{{$sch->scholarship_name or $sch->provider_name}}</div>
                                        @endif

										<div class="sch-provider">Scholarship provided by {{$sch->provider_name or 'Anonymous'}}</div>

										<div class="sch-view-details">VIEW DETAILS</div> <div class="sch-details-arrow down"></div>
									</div>
									<div class="sch-col sch-col-amount">
                                        @if (!isset($sch->amount) || $sch->amount == 0)
                                          <div class="sch-amount">&nbsp;</div>
                                        @else
										  <div class="sch-amount">${{number_format($sch->amount, 2)}}</div>
                                        @endif
									</div>
									<div class="sch-col sch-col-due">
										<div class="sch-due">{{$sch->deadline}}</div>
									</div>
									<div class="sch-col sch-col-add">
										@if(isset($signed_in) && $signed_in != 0)
											<div class="sch-add-btn no"
												 data-state="@if(isset($sch->status)) {{$sch->status}} @else null @endif">
												 <span>+</span>
										 	</div>
										@else
											<div class="sch-add-btn-login">+</div>
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

				<div class="sch-bottom-next-cont text-right mt20">
					<div class="sch-next-btn disabled">Next</div>
				</div>

			</div>
		</div>


	@include('private.footers.footer')
	</body>

</html>
