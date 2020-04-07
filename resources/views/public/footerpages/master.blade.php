<!DOCTYPE html>
<html>
<head>

	<!-- <link rel="stylesheet" href="/css/homepage.css" /> -->
	@if(isset($signed_in) && $signed_in == 1)
		@include('private.headers.header')
	@else
		@include('public.headers.header')
	@endif
	
	<!-- LinkedIn Tracking Pixel -->
	<script type="text/javascript"> _linkedin_data_partner_id = "16425"; </script><script type="text/javascript"> (function(){var s = document.getElementsByTagName("script")[0]; var b = document.createElement("script"); b.type = "text/javascript";b.async = true; b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js"; s.parentNode.insertBefore(b, s);})(); </script>

		
	<link rel="stylesheet" href="/css/gettingStartedPins.css?6"/>        
	<link rel="stylesheet" href="/css/footerPages.css?v=7.00" />
</head>

<body class="{{ $currentPage }}" id="footerpages_id_{{ $currentPage }}">
	
	<!-- Top Nav Section -->
	@include('private.includes.topnav')
	
	<!-- TOP BANNER AND FOOTER-PAGE-NAV MENU -->
	@include('public.footerpages.footerPageNaveMenu')


   	@if(isset($thank_you))
		<div class='row'>
			<div class='column small-12'>
				<div class='row'>
					<div class='text-center large-12 column'>
						<h1 class='header1'>We'll get back to you soon.</h1>
					</div>
				</div>
				<div class='row'>
					<div class='column text-center'>
						<img src="/images/ThankYou.jpg" alt='Thank You!'/>
					</div>
				</div>
				<div class='row'>
					<div class='small-12 text-center column'>
						<h2 class='thankyoutext'>Thank you for contacting us.<br/>Someone from Plexuss will contact you shortly</h2>
					</div>
				</div>
			</div>
		</div>
	@else
		<!-- EVERYTHING BETWEEN HEADER AND FOOTER GOES HERE -->
		<div class='row'>
			<div class='small-12 column'>




				<!-- We need to move this below away -->
				@if(isset($help_page) && $help_page == 1)
					<!-- HEADING, GREETING SEARCH BAR -->
					<div class='row'>
						<div class='small-12 column'>
							<div class="help-faq text-center">
								<span>
									Help & FAQ
								</span>
							</div>

							<div id='help-faq-greeting' class='text-center'>
								Hello, <span class="txt-cap">{{$username or 'Guest'}}!</span> How can we help you today?
							</div>
							<div class="small-12 collapse pt20" style="margin-left:-10px;">
							  <!--
							  SEARCH INPUT BOX
							  <div class="small-10 medium-9 medium-offset-1 column">
								<input type="text" name="askquestion"  placeholder="Search FAQ by keyword..." class="radius1 hgt32">
							  </div>				
							  <div class="small-2 medium-2 column cursor no-padding text-left">
									<div class="search-icon "></div>
							  </div> 
							  <div class="clearfix"></div>			  
							  -->
							</div>
						</div>
					</div>
					<!-- FAQ QUICK LINKS GRID -->
					<div class='row faq-grid'>
						<div class='small-12 column'>
							 <!-- Help-Grid heading -->
							 <div class='row'>
								 <div class='small-12 column text-center' id='faq-grid-heading'>
									Maybe these topics will help:
								</div>	
							 </div>
								<!-- Top Help-Grid row -->
							<div class='row'>
								<div class="small-12 medium-12 column">
									<div class='row'>

										<div class="small-12 medium-3 column">
											<a href='/help'>
												<div class='bck-button button expand'>
													Getting Started
												</div>
											</a>
										</div>

										<div class="small-12 medium-3 column">
											<a href='/help/faq/general'>
												<div class="bck-button button expand">
													General FAQ
												</div>
											</a>
										</div>

										<div class="small-12 medium-3 column">
											<a href='/help/faq/internship'>
												<div class="bck-button button expand">
													Internship FAQ
												</div>
											</a>
										</div>

										<div class="small-12 medium-3 column">
											<a href='/help/faq/job'>
												<div class="bck-button button expand">
													Jobs FAQ
												</div>
											</a>
										</div>

									</div>
								    <div class="clearfix"></div>	 
								</div>	
							</div>
							<!-- Bottom Help-Grid Row -->
							 <div class='row'>
								 <div class="small-12 medium-12 column">
									<div class='row'>
										<!-- offset the buttons to center them -->
										<div class="small-12 medium-3 column">
											<a href='/help/helpful_videos'>
												<div class='bck-button button expand'>
													Helpful Videos
												</div>
											</a>
										</div>

										<div class="small-12 medium-3 column">
											<a href='/help/faq/scholarship'>
												<div class="bck-button button expand">
													Scholarships FAQ
												</div>
											</a>
										</div>

                                        <div class="small-12 medium-3 column end">
                                            <a href='/help/faq/privacy'>
                                                <div class="bck-button button expand">
                                                    Privacy & Data Rights
                                                </div>
                                            </a>
                                        </div>

										@if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
										<div class="small-12 medium-3 column end">
											<a href='/international-resources'>
												<div class="bck-button button expand">
													International Resources
												</div>
											</a>
										</div>
										@endif

									</div>
								</div>  
							</div>
						</div>
					</div>


					<div class='row helpvid-mobile-backg'>
						<div class='small-12 column text-center' id='help-heading'>
							@yield('help_heading')
						</div>
					</div>

				@endif
				<!-- We need to move this above away -->




				<!-- CONTENT -->
				@yield('content')
			</div>
		</div>
	@endif


	@if(isset($signed_in) && $signed_in == 1)
		<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js" async></script>
		<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
	@endif

	@include('private.includes.backToTop')

	@if(isset($signed_in) && $signed_in == 1)
		@include('private.footers.footer')
	@else
		@include('public.footers.footer')
		<script src="/js/prod_ready/foundation/foundation.reveal.min.js"></script>
	@endif
	
	@include('public.includes.footer')


	<script src="/js/help.js?7"></script>



	<script src="/js/prod_ready/foundation/foundation.abide.min.js"></script>

	@if ( $currentPage == 'scholarship-submission')
		
	<script type="text/javascript">
		$(document).ready(function(){
			/*Performs an AJAX call based on the button clicked. Populates the form
			* with data returned*/
			$('.ajaxCallbutton').click(function(event) {
				var scholarshipModal = $('#scholarshipModal');
				var type = $(this).data('type');
				//get the button text to use as a title.
				var title = $(this).html();
				var active = $(this).data('active');
				scholarshipModal.foundation('reveal', 'open');
				scholarshipModal.find('h2').html(title);
				if(active == 'empty'){
					//console.log('Active is empty!');
					$.ajax({
						url: '/scholarship-submission/ajax/' + type ,
						type: 'GET',
						dataType: 'json',
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
					})
					.done(function(data) {
						populateModalFormWithAjax(data, type );
					})
					.fail(function() {
					});
				}else{
					//console.log('Active NOT empty!');
					var form = $(this).parent().find('.savedDropDown').contents().detach();
					$.each(form, function(index, val) {
						var e = $(val);
						if( e.data('eq') == 0 ){
							e.css('display', '').addClass('active');
						} else {
							e.css('display', '').removeClass('active');
						}
					});
					scholarshipModal.find('.dropdownarea').html(form);
					scholarshipModal.find('.modalFormSubmit').data('type', type);
				}
			});



			//Click Bind
			$('.modalFormSubmit').click(function(event) {
				var type = $(this).data();
				var modalbox = $('#scholarshipModal');
				var dropdownArea = modalbox.find('.dropdownarea');
				var inputs = modalbox.find('.checkboxwrapper');
				var checkedItems = 0;
				$.each(inputs , function(index, val) {
					 var n = $(val).find('input');
					 if(n.is(':checked')){
					 	$(val).css('display', 'block');
					 	checkedItems++
					 }else{
					 	$(val).css('display', 'none');
					 }
				});

				var x;
				if(checkedItems >= 0){
					x = $('.filterArea').find("[data-type='" + type['type'] + "']").data('active', 'filled').addClass('filled').parent().find('.savedDropDown');
					x.html(dropdownArea.contents());
				}else{
					x = $('.filterArea').find("[data-active='empty']").removeClass('filled');
				}
				$('#scholarshipModal').foundation('reveal', 'close').find('.dropdownarea').html('');
				$('.filterArea').masonry({
					itemSelector: '.filter-criteria'
				});
			});

			//catrgory buttons bind.
			$('#scholarshipModal').on( "click", ".formTitle div", function() {
                $("#scholarshipModal .formTitle .button.tiny").removeClass('active');
                $(this).addClass('active');
                
				var eq = $(this).index();
				$('#scholarshipModal .checkboxwrapper').removeClass('active');
				$('#scholarshipModal .checkboxwrapper[data-eq="' + eq + '"]').addClass('active');
			});
		});

		function populateModalFormWithAjax (json, type ){
			var checkboxActive = "";
			var titleActive = "";
			var len = Object.keys(json).length;
			if(len == 1){
				titleActive = 'hidden';
			}

			html = "<div class='formTitle " + titleActive + "'>";
				$.each(json, function(index, val) {
					html += "<div class='button tiny'>" + index + "</div>";
				});
			html += "</div>";

			var i = 0;
			var itemCount = 0;

			$.each( json, function(index, val) {
				if (i == 0) {
					checkboxActive = 'active';
				}else{
					checkboxActive = '';
				};

				$.each(val, function(index, val2) {
					html += "<div class='checkboxwrapper " + checkboxActive + "' data-eq='" + i + "' >";
						html += "<input id='" + type+itemCount + "' type='checkbox' value='" + val2['id'] + "' name='" + type + "[]'/>";
						html += "<label for='" + type+itemCount + "'>" + val2['value'] + "</label>";
					html += "</div>";
					itemCount++;
				});

				i++;
			});
			
			$('.dropdownarea').html(html);
			$('.modalFormSubmit').data('type', type);
		}

		/*Init Masonry*/
		$(document).ready(function(){
			$('.filterArea').masonry({
				itemSelector: '.filter-criteria'
			});
		});

	</script>

	@endif	
 	
</body>
</html>
