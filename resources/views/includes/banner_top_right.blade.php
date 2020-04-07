<!-- banner_top_right.blade.php -->
    <?php //dd('2dadsadads3132');
    // echo "<pre>";
    // print_r($plexuss_banners['end_point']);
    // echo "</pre>";
    // exit();

    // dd(get_defined_vars());
     ?>
@if (isset($agency_ad) && (!isset($country_based_on_ip) || $country_based_on_ip !== 'US'))
    <div class="row" id='agencyRepAd'>
        <div class="columns small-12 text-center">
            <h5>Chat with a Representative</h5>

            <div class='rep-info'>
                <?php $agency_logo = isset($agency_ad['profile_img_loc']) ? $agency_ad['profile_img_loc'] : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png' ?>
                
                <div class='agency_logo'>
                    <img src='{{ $agency_logo }}' />
                </div>
                <div>
                    <div>{{ $agency_ad['fname'] }}</div>
                    <div class='star-rating'>
                        @for ($i = 1; $i <= 5; $i++)
                            @if (isset($agency_ad['review_average']) && ceil($agency_ad['review_average']) >= $i)
                                <div class='star-icon active'></div>
                            @else
                                <div class='star-icon'></div>
                            @endif
                        @endfor
                    </div>
                    <div>
                        {{ $agency_ad['location'] }}
                    </div>
                </div>

            </div>

            <div class='view-profile-btn agency-link' data-slug='{{ $agency_ad['profile_slug'] }}'>View Profile</div>

            <div class='agent-message-btn agency-link' data-slug='{{ $agency_ad['message_slug'] }}'>Message</div>
        </div>
    </div>
@endif

<div class="row" id='plexussBannerAd' style="
    margin-bottom: 1em;
    margin-left: 0.2em;
">
	<div class="columns small-12 text-center">
		<a href="{{$plexuss_banners['end_point']}}" target="_blank"> <img src="{{$plexuss_banners['url']}}"> </a>
	</div>
</div>
<div id="hidden-college-slug" class="hide" data-slug="{{$college_slug or ''}}"></div>
<div id="hidden-affiliate-company" class="hide" data-company="{{$plexuss_banners['company'] or ''}}"></div>
<div id="hidden-adcopy-id" class="hide" data-adcopyid="{{$plexuss_banners['ad_copy_id'] or ''}}"></div>