	<!--//////////////////// RIGHT SIDE ITEMS HERE \\\\\\\\\\\\\\\\\\\\-->
				<div class="column medium-3 hide-for-small-only">
					
					<div class='row'>
						
						<div class='column small-12 text-center'>
							@include('private.includes.invite_friends_right_side')
						</div>


						<div class="small-12 column">
				            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				            <ins class="adsbygoogle"
				                 style="display:inline-block;width:235px;height:280px"
				                 data-ad-client="ca-pub-8810428285316335"
				                 data-ad-slot="5024877200"></ins>
				            <script>
				            (adsbygoogle = window.adsbygoogle || []).push({});
				            </script>
				        </div>


				        <div class='column small-12 text-center'> 
							@if(!isset($hide_gs_circle_arrow))
								@include('private.includes.right_side_get_started')
							@endif
								
								<div class="page-right-side-bar side-bar-2 radius-4">
									<div class="text-center">
										<p class="step-number">1</p>
									</div>
									<p class="right-bar-heading white">Get Started</p>
									<p class="right-bar-para white">Wondering why your indicators are at zero?</p>
									<p class="right-bar-para white">You need a profile for the recruitment process to begin.</p>
									<div class="large-12 text-center">
										<a href="/profile" class="button get-started-button">Start your Profile</a>
									</div>
								</div>
							@include('private.includes.right_side_footer')
						</div>


					</div>
				</div>
				<!--\\\\\\\\\\\\\\\\\\\\ RIGHT SIDE ITEMS END  ////////////////////-->