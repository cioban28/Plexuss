<style type="text/css">
	.text-center{
		color:white;
	}
</style>
@if( isset($signed_in) && $signed_in == 1 )
<div class="row hide-for-small-only close-icon-and-adv-search-msg-row make-room-for-signedin-topbar">
@else
<div class="row hide-for-small-only close-icon-and-adv-search-msg-row">
@endif
	<!-- back/close button to close side bar sections when open - start -->
	<div class="column small-12 medium-text-right frontpage-back-btn">
		<img class="tablet-up-back-btn" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/gray-x.png" alt="">
	</div>
</div>

<!-- pillars row -->
<div class="row pillars">
	<div class="column small-12 medium-4">
		<br class="show-for-small-only" />
		<div class="inner-col plexuss-student-feature">
			<div class="text-center fp-icon"><div class="fp-sprite create-prof"></div></div>
			<h5 class="small-only-text-center text-center">Create a Profile</h5>
			<div class="small-only-text-center text-center">Creating your academic profile will enable universities to find you based on your academic achievements, extracurricular activities, location and much more.</div>	
		</div>
	</div>
	<div class="column small-12 medium-4">
		<br class="show-for-small-only" />
		<div class="inner-col plexuss-student-feature">
			<div class="text-center fp-icon"><div class="fp-sprite chat-w-col"></div></div>
			<h5 class="small-only-text-center text-center">Chat with Colleges</h5>
			<div class="small-only-text-center text-center">Use our powerful search and recommendation engine to interact with a variety of colleges. Regardless of high school level, it is never too early or too late to begin using Plexuss.</div>	
		</div>
	</div>
	<div class="column small-12 medium-4">
		<br class="show-for-small-only" />
		<div class="inner-col plexuss-student-feature">
			<div class="text-center fp-icon"><div class="fp-sprite get-rec"></div></div>
			<h5 class="small-only-text-center text-center">Get Recruited</h5>
			<div class="small-only-text-center text-center">Select all colleges you are interested in attending. Shortly after, you will be contacted by the college representative on our network.</div>	
		</div>
	</div>
</div>

<!-- video row -->
<div class="row plex-vids">
	<div class="column small-6 medium-3">
		<div class="text-center">How Plexuss<br class="hide-for-large-up" /> Works</div>
		<div>
          	<img src="/images/pages/recruit-video.jpg" width="363" height="201" alt="Recruitment video" title="" />
          	<div class="play-layer is-lightboxable" data-type="video" data-thumb="/images/pages/recruit-video.jpg" data-link="//www.youtube-nocookie.com/embed/cISL9w0c8fo?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0">
          		<div class="play is-lightboxable">
          			<div class="playbtn is-lightboxable"></div>
	            </div>
	        </div>
		</div>
	</div>
	<div class="column small-6 medium-3">
		<div class="text-center">College Ranking<br class="hide-for-large-up" /> Explained</div>
		<div>
            <img src="/images/pages/ranking-video.jpg" width="363" height="201" alt="Ranking video" title="" />
          	<div class="play-layer is-lightboxable" data-type="video" data-thumb="/images/pages/ranking-video.jpg" data-link="//www.youtube.com/embed/O73eOnoTtPE?version=3&showinfo=0&controls=1&rel=0">
          		<div class="play is-lightboxable">
          			<div class="playbtn is-lightboxable"></div>
	            </div>
	        </div>
		</div>
	</div>
	<div class="column small-6 medium-3">
		<div class="text-center">Colleges<br class="hide-for-large-up" /> Pages</div>
		<div>
            <img src="/images/pages/college-video.jpg" width="363" height="201" alt="College video" title=""/>
          	<div class="play-layer is-lightboxable" data-type="video" data-thumb="/images/pages/college-video.jpg" data-link="//www.youtube-nocookie.com/embed/1QlApSS_4ZQ?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0">
          		<div class="play is-lightboxable">
          			<div class="playbtn is-lightboxable"></div>
	            </div>
	        </div>
		</div>
	</div>
	<div class="column small-6 medium-3">
		<div class="text-center">High School<br class="hide-for-large-up" /> Internship</div>
		<div>
	        <img src="/images/pages/internship-video.jpg" width="363" height="201" alt="Internship video" title="" />
          	<div class="play-layer is-lightboxable" data-type="video" data-thumb="/images/pages/internship-video.jpg" data-link="//www.youtube-nocookie.com/embed/DpGUyAc5OHA?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0">
          		<div class="play is-lightboxable">
          			<div class="playbtn is-lightboxable"></div>
	            </div>
	        </div>
		</div>
	</div>
</div>

<script src="/js/prod_ready/frontpage/plex_lightbox.min.js" defer></script>
