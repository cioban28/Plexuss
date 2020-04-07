						<!-- Quiz POLL -->
						@if (isset($quizInfo))
							@include('quizzes.quizzes')
						@endif
						<!-- END Quiz -->
						
						<!-- Right side Footer -->
						<div class="row">
							<div class="column small-12" id="right-side-footer">
								<ul id="right-footer-visible">
									<li><img src="/images/p-logo.png" alt="Logo" /></li>
									<li><a href="/help">Help •</a></li>
									<li><a href="/contact">Contact •</a></li>
									<li>
										<span id="right-footer-more" onclick="ShowHideFooterLinks(1);">
											More
											<img alt='downarrow' src="/images/arrow_down.png" />
										</span>
										<span id="right-footer-less"  onclick="ShowHideFooterLinks(2);">
											Less
											<img alt='uparrow' src="/images/arrow_up.png" />
										</span>
									</li>
								</ul>
								<div id="right-footer-reveal">
									<ul>
										<li><a href="/about">About •</a></li>
										<!-- Hiding this link because we disabled our advertising page
										<li><a href="/advertising">Advertising •</a></li>
										-->
										<li><a href="/college-submission">College Submission •</a></li>
										<li><a href="/scholarship-submission">Scholarship Submission •</a> </li>
										<li><a href="/careers-internships">Careers •</a> </li>
										<li><a href="/terms-of-service">Terms of Service •</a> </li>
										<li><a href="/privacy-policy">Privacy Policy •</a></li>
									</ul>
									<div class="fs10 clr-fff" style="padding-top:10px;">
										Plexuss © <?php echo date("Y"); ?>
										<span class="pl40">
											<a target='_blank' href="http://www.linkedin.com/company/plexuss-com">
												<img src="/images/social/linkedin_white.png" title="" alt="Plexuss LinkedIn Page">
											</a>
											<a  target='_blank' href="http://www.twitter.com/plexussupdates">
												<img src="/images/social/twitter_white.png" title="" alt="Plexuss Twitter Page" class="pl15">
											</a>
											<a href="https://www.facebook.com/pages/Plexusscom/465631496904278" target="_blank">
												<img src="/images/social/fb_white.png" alt="Plexuss Facebook Page" class="pl15">
											</a>
										</span>
									</div>
								</div>
							</div>
						</div>
						<!-- End Right side footer -->
