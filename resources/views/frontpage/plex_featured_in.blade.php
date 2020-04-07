<!-- \\\\\\\\\\\\\\\\\\\\\\\ plexuss featured in section - start /////////////////////////-->
<div class="row plex-featured-in-section outer-section" data-equalizer style="overflow: hidden;height: 300px;">
	<!-- featured in text - left side -->
	<div class="column small-7 small-centered large-uncentered large-2 small-text-center large-text-right featured-in-text-side" data-equalizer-watch>
		<i>featured in</i>
	</div>

	<!-- column of logos of company's that have featured us in some way - right side -->
	<div class="column small-12 large-10 company-featuring-plex-logo-side" data-equalizer-watch style="height:210px;">
		
		<div class="prev-plex-logo">
			&#9668;
		</div>
		
		<!-- once we start being featured more and get more company logos - each row will contain four logos -->
		<div id="plex-logo-owl-carousel" class="row owl-carousel owl-theme" style="margin-top:14px;"> <!-- owl-carousel owl-theme-->

			<!-- Microsoft Ventures article -->
			<div id="sprite-one" class="sprite">
				<a href="https://www.microsoftventures.com/blog/entry/FromDiscoverytoSelectionAnnouncingtheSeattleAcceleratorsThirdBatch%7C6148" target="_blank"></a>
			</div>
			<!-- Launch Festival 2016 -->
			<div id="sprite-two" class="sprite">
				<a href="http://www.launchfestival.com/" target="_blank"></a>
			</div>
            <!-- featured in Geek Wire article -->
			<div id="sprite-three" class="sprite">
				<a href="http://www.geekwire.com/2016/microsoft-seattle-accelerator-startups/" target="_blank"></a>
			</div>
			<!-- featured in tech.co article -->
			<div id="sprite-four" class="sprite">
				<a href="http://tech.co/41-startups-share-motivate-teams-2015-04" target="_blank"></a>
			</div>
			<!-- featured in Bethesda Magazine article -->
			<div id="sprite-five" class="sprite">
				<a target="_blank" href="http://www.bethesdamagazine.com/Bethesda-Beat/2015/Bethesda-Named-One-of-the-Best-Cities-for-College-Grads/"></a>
			</div>
			<!-- featured in Hudson Reporter article -->
			<div id="sprite-six" class="sprite">
				<a target="_blank" href="http://hudsonreporter.com/pages/full_story/push?id=26704891&content_instance=26704891&need_to_add=true"></a>
			</div>
			<!-- featured in startup beat article -->
			<div id="sprite-seven" class="sprite">
				<a target="_blank" href="http://startupbeat.com/2015/05/27/u-s-college-comparison-and-recruitment-website-plexuss-com-now-out-of-beta-testing/"></a>
			</div>

			<div id="sprite-eight" class="sprite show-for-large-only">
				<a></a>
			</div>


		</div>
		
		<div class="next-plex-logo">
			&#9658;
		</div>
		
	</div>
</div>

<script>
	$('#plex-logo-owl-carousel').owlCarousel({
        item : 3,
        itemsDesktop : [1300,3], //4 items between 1200px and 801px
        itemsDesktopSmall : [800,2], // 3 items betweem 900px and 601px
        itemsTablet: [600,1], //2 items between 600 and 0
        itemsMobile : [600, 1], // itemsMobile disabled - inherit from itemsTablet option
        pagination : false,
        loop: false,
        center: true
    });

    $(".next-plex-logo").click(function(){ 
        var current_carousel = $(this).prev('#plex-logo-owl-carousel');
        current_carousel.trigger('owl.next');
    });

    $(".prev-plex-logo").click(function(){
        var current_carousel = $(this).next('#plex-logo-owl-carousel');
        current_carousel.trigger('owl.prev');
    });
</script>
<!-- \\\\\\\\\\\\\\\\\\\\\\\ plexuss featured in section - end /////////////////////////-->