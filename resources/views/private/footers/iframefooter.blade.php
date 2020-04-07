<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
<script src="/js/vendor/modernizr.js?7"></script>
<script src="/js/jquery.knob.js?7"></script>
<script src="/js/foundation/foundation.js?7"></script>
<script src="/js/foundation/foundation.abide.js?7"></script>
<script src="/js/foundation/foundation.reveal.js?7"></script>




<!-- Set the ajaxtoken -->
<script type="text/javascript">
	Plex = {
		@if ( isset($ajaxtoken) )
			'ajaxtoken': '{{$ajaxtoken}}',
		@endif
		
		@if ( isset($showFirstTimeHomepageModal) )
			'showFirstTimeHomepageModal' : {{{$showFirstTimeHomepageModal}}},
		@endif

		@if ( isset($profile_page_lock_modal) )
			'profile_page_lock_modal' : {{{$profile_page_lock_modal}}},
		@endif
		'modalAvailable':true,
	};
</script>

@if ($currentPage == 'portal')  
 <script src="/js/toggles.js?7"></script> 
 <script src="/js/portal.js?7"></script> 
<!-- <script src="/js/jquery.dataTables.js?7"></script>  --> 
@endif

