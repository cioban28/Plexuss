<!-- ad_affiliates.blade.php -->
<div class="row" id='affiliateAds' style="
    margin-bottom: 1em;
    margin-left: 0.2em;
">
	<div class="columns small-12 text-center">
		<a href="{{$affiliate_ad['end_point']}}" target="_blank"> <img src="{{$affiliate_ad['url']}}"> </a>
	</div>
</div>
<div id="hidden-college-slug" class="hide" data-slug="{{$data['college_slug'] or ''}}"></div>
<div id="hidden-affiliate-company" class="hide" data-company="{{$affiliate_ad['company'] or ''}}"></div>
<div id="hidden-adcopy-id" class="hide" data-adcopyid="{{$affiliate_ad['ad_copy_id'] or ''}}"></div>