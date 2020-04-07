<!-- eddy_ads.blade.php -->
<div id="hidden-college-slug" class="hide" data-slug="{{$college_slug or ''}}"></div>

<script type="text/javascript" src="//agrservice.educationdynamics.com/Scripts/Bundles/EddyAggregator"></script>
<script type="text/javascript">
	$(document).on('click', '#eddyListings', function(){
		$.ajax({
			url: '/adClicked',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data: {slug: $('#hidden-college-slug').data('slug')},
			type: 'POST',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
		}).done(function(data){
			// console.log(data);
		});
	});

	$('#eddyListings').eddyAd({
		placementtoken: 'ae39a601-2a7e-4229-a528-f1ab8b30f66c',
		useIframe: false,
		testmode: false,
		isWizard: false
	});
</script>