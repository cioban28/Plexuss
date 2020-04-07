@extends('admin.master')
@section('content')
	<!-- start of entire adv filter page structure -->
	<div class="main-admin-adv-filter-container">

		<div class="row">
			<div class="column small-12 large-3 show-for-large-up">
				@if( isset($is_agency) && $is_agency == 1 )
					<div class="backToDash-btn"><a href="/agency"> < Go Back</a></div>
				@else
					<div class="backToDash-btn"><a href="/admin/dashboard"> < Go Back</a></div>
				@endif
			</div>
			<div class="column small-12 large-9 small-text-center large-text-left">
				Filter the results you receive in your student recommendations
				<br/>
				<div class="targeting-video" style="font-size: 0.7em;color: #45a7e2;text-decoration: underline;cursor: pointer;">
					Learn how targeting works by watching this video
				</div>
			</div>
		</div>



		<div class="row hide-for-large-up">
			<div class="column small-12 text-center">
				<div class="filter-page-indicator">Welcome to your advanced filter</div>
				<div class="select-filter-btn-sm">Select another filter <span class="filter-menu-arrow">&dtrif;</span></div>
				<div class="filtering-menu-sm-container">
					<!-- adv filtering side nav - start -->
					<ul class="side-nav adv-filtering-menu" data-locked="@if(isset($show_upgrade_button) && $show_upgrade_button == 1) 1 @else 0 @endif">
						<li data-filter-tab="location">
							<a href="">Location</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="startDateTerm">
							<a href="">Start Date</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="financial">
							<a href="">Financials</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="typeofschool">
							<a href="">Type of School</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="major">
							<a href="">Major</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="scores">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Scores</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="uploads">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Uploads</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="demographic">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Demographic</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="educationLevel">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Education Level</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<!--<li data-filter-tab="desiredDegree">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Desired Degree</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>-->
						<li data-filter-tab="militaryAffiliation">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Military Affiliation</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="profileCompletion">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Profile Completion</a>
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
						<li data-filter-tab="location">
							<a href="">Location</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="startDateTerm">
							<a href="">Start Date</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="financial">
							<a href="">Financials</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="typeofschool">
							<a href="">Type of School</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="major">
							<a href="">Major</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="scores">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Scores</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="uploads">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Uploads</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="demographic">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Demographic</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="educationLevel">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Education Level</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<!--<li data-filter-tab="desiredDegree">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Desired Degree</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>-->
						<li data-filter-tab="militaryAffiliation">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Military Affiliation</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
						<li data-filter-tab="profileCompletion">
							@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
							@endif
							<a href="">Profile Completion</a>
							<div class="change-icon hide">&#x02713;</div>
						</li>
					</ul>
					<!-- adv filtering side nav - end -->	
				</div>

			</div>

			<div class="column small-12 large-9">

				<div class="video-container">
				</div>

				<div class="filter-crumbs-container">
					<ul class="inline-list filter-crumb-list">
						<!-- crumb tags get injected here -->
					</ul>
				</div>

				<div class="row recomm-meter-container hidden">
					<div class="column small-12">
						<div class="recomm-meter-msg">This meter shows if you are filtering too much. More filters could result in less recommendations.</div>
						<div class="radius progress">
							<span class="meter" style="width: {{$filter_perc or 100}}%"></span>
						</div>
						<div class="recomm-meter-descrip"><span>|&nbsp;&nbsp;&nbsp;&nbsp;Fewer recommendations</span> <span>&nbsp;&nbsp;More recommendations&nbsp;&nbsp;&nbsp;&nbsp;|</span></div>
					</div>
				</div>

				<div class="adv-filtering-section-container">
					<!-- adv filter section will get ajax in here -->
					<div class="row filter-intro-container" data-equalizer>
						<div class="column small-12 medium-4">
							<div class="filter-intro-step" data-equalizer-watch>
								<div class="text-center">1</div>
								<div class="text-center">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-1-filter.png" alt="Plexuss">
								</div>
								<div>
									You receive student recommendations daily, but you're looking for certain kinds of students
								</div>
							</div>	
						</div>
						<div class="column small-12 medium-4">
							<div class="filter-intro-step" data-equalizer-watch>
								<div class="text-center">2</div>
								<div class="text-center">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-2-filter.png" alt="Plexuss">
								</div>
								<div>
									Choose what you'd like to filter by and save your changes (menu on the left)	
								</div>
							</div>	
						</div>
						<div class="column small-12 medium-4">
							<div class="filter-intro-step" data-equalizer-watch>
								<div class="text-center">3</div>
								<div class="text-center">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-3-filter.png" alt="Plexuss">
								</div>
								<div>
									Based on your filters, you will receive recommendations that may be a better fit for your school	
								</div>
							</div>	
						</div>
					</div>	
				</div>
			</div>

			<div class="column small-12 large-9 text-right reset-save-filters-col hidden" style="padding: 16px 80px 20px 20px;">
				<span class="reset-filters-btn">Reset this filter</span> <span class="save-filters-btn">Save</span>
			</div>

			<div class="column small-12 text-right">
				<a class="targeting-done-btn" href="/admin/dashboard"><div>All done, take me to my dashboard</div></a>
			</div>

		</div>

	</div>

	<!-- dialog modal -->
	<div id="filter-dialog-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="row">
			<div class="column small-10 small-offset-1 text-center dialog-msg">Save before leaving?</div>
			<div class="column small-1">
				<a class="close-reveal-modal" aria-label="Close">&#215;</a>
			</div>
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
    <!-- end of ajax loader -->

@stop