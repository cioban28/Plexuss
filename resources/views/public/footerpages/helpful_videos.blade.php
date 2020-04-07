@extends('public.footerpages.master')
@section('help_heading')
	{{$help_heading or ''}}
@stop
@section('content')

<div class="row help-vid-section">
	<div class="column small-12 small-centered">
	
		<!-- row 1 header 
	    <div class="row video-head">
			<div class="column small-12 text-center">
				<div>Helpful Videos</div>
			</div>
		</div>-->
	    <!-- row 2 - helpful videos-->
		<div class="row row-spacing">

			<div class="column small-12 medium-6 text-center help-vid-box">
				<div class="row">
					<div class="column small-10 small-centered help-vid-background">
						<div class="cursor" data-reveal-id="videobox_1">
		              		<img src="/images/pages/recruit-video.jpg" alt="" title="" />
		        		</div>
	        		</div>
        		</div>
        		<div class="row">
        			<div class="column small-10 small-centered">
        				<div class="video-heading text-left video-title">ABOUT PLEXUSS</div>
        			</div>
        		</div>
			</div>

			<div class="column small-12 medium-6 text-center help-vid-box">
				<div class="row">
					<div class="column small-10 small-centered help-vid-background">
						<div class="cursor" data-reveal-id="videobox_2">
			                <img src="/images/pages/ranking-video.jpg" alt="" title="" />
		        		</div>
	        		</div>
        		</div>
        		<div class="row" >
        			<div class="column small-10 small-centered">
        				<div class="video-heading text-left video-title">COLLEGE RANKINGS EXPLAINED</div>
        			</div>
        		</div>
			</div>

		</div>
		<!-- row 3 - helpful videos-->
		<div class="row">

			<div class="column small-12 medium-6 text-center help-vid-box">
				<div class="row">
					<div class="column small-10 small-centered help-vid-background">
						<div class="cursor" data-reveal-id="videobox_3">
			                <img src="/images/pages/college-video.jpg" alt="" title=""/>
			        	</div>
		        	</div>
	        	</div>
	        	<div class="row">
	        		<div class="column small-10 small-centered">
	        			<div class="video-heading text-left video-title">COLLEGE PAGES</div>
	        		</div>
	        	</div>
        	</div>

        	<div class="column small-12 medium-6 text-center help-vid-box">
        		<div class="row">
        			<div class="column small-10 small-centered help-vid-background">
						<div class="cursor" data-reveal-id="videobox_4">
				            <img src="/images/pages/internship-video.jpg" alt="" title="" />
			        	</div>
		        	</div>
	        	</div>
	        	<div class="row">
	        		<div class="column small-10 small-centered">
	        			<div class="video-heading text-left video-title">HIGH SCHOOL INTERNSHIP</div>
	        		</div>
	        	</div>
        	</div>

		</div>

	</div><!-- end of main column -->
</div><!-- end of main row -->

<!-- Reveal Modals begin -->
<div id="videobox_1" class="reveal-modal large helpvid_pad helpful_vid_mod_top" data-reveal="">
	<div class="row">
		<div class="column small-12 small-text-right">
			<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
		</div>
	</div>
	<div class="flex-video widescreen vimeo">
			<iframe class="video-iframe"  src="//www.youtube-nocookie.com/embed/cISL9w0c8fo?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0" frameborder="0" allowfullscreen="">
				<!---- video frame ---->
			</iframe>
	</div>
</div>

<div id="videobox_2" class="reveal-modal large helpvid_pad helpful_vid_mod_top" data-reveal="">
	<div class="row">
		<div class="column small-12 small-text-right">
			<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
		</div>
	</div>
	<div class="flex-video widescreen vimeo">
			<iframe class="video-iframe"  src="//www.youtube.com/embed/O73eOnoTtPE?version=3&showinfo=0&controls=1&rel=0" frameborder="0" allowfullscreen="">
				<!---- video frame ---->
			</iframe>
	</div>
</div>

<div id="videobox_3" class="reveal-modal large helpvid_pad helpful_vid_mod_top" data-reveal="">
	<div class="row">
		<div class="column small-12 small-text-right">
			<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
		</div>
	</div>
	<div class="flex-video widescreen vimeo">
			<iframe class="video-iframe"  src="//www.youtube-nocookie.com/embed/1QlApSS_4ZQ?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0" frameborder="0" allowfullscreen="">
				<!---- video frame ---->
			</iframe>
	</div>
</div>

<div id="videobox_4" class="reveal-modal large helpvid_pad helpful_vid_mod_top" data-reveal="">
	<div class="row">
		<div class="column small-12 small-text-right">
			<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
		</div>
	</div>
	<div class="flex-video widescreen vimeo">
			<iframe class="video-iframe"  src="//www.youtube-nocookie.com/embed/DpGUyAc5OHA?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0" frameborder="0" allowfullscreen="">
			</iframe>
	</div>
</div>
@stop
