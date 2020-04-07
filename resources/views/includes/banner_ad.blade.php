@if( isset($signed_in) && $signed_in == 0 && ($is_mobile != true))
<div id="_banner_ad"></div>
@endif

<div id="banner_ad_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
	<div class="text-right">
		<a id="close_banner_modal" class="close-reveal-modal" aria-label="Close">&#215;</a>
	</div>

	<div id="banner_modal_container">

		<h4>Receive up to a $500 voucher. Apply up to 5 <br> Universities for FREE</h4>

		<div class="text">All you have to do is sign up on Plexuss.</div>

		<a id="ad_modal_btn" href="/signup?utm_source=SEO&utm_medium=news&utm_content=free_voucher&utm_campaign=news_modal&utm_term=">
			Sign up!
		</a>
		<a id="ad_modal_img" href="/signup?utm_source=SEO&utm_medium=news&utm_content=free_voucher_logo&utm_campaign=news_modal&utm_term=">
			<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/banner/school-logos.png" alt="">
		</a>
		
	</div>	
</div>

<script>
	// banner elems
	const ad = document.getElementById('_banner_ad');
	const ad_bar_img = document.getElementById('ad_bar_img');
	const ad_bar_text = document.getElementById('ad_bar_text');
	const ad_bar_btn = document.getElementById('ad_bar_btn');
	const ad_modal_btn = document.getElementById('ad_modal_btn');
	const ad_modal_img = document.getElementById('ad_modal_img');
  const page_slug = window.location.pathname.split('/')[1];
	// slug
	const slug = window.location.pathname.split('/').reverse()[0];

	// list of bg images
	const images = [
		'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/banner/Page+1.jpg',
		'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/banner/Page+2.jpg',
		'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/banner/Page+3.jpg',
		'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/banner/Page+4.jpg'
	];

	const utm = [
		'/signup?utm_source=SEO&utm_medium='+page_slug+'&utm_content=ice_creame&utm_campaign='+page_slug+'_side_banner&utm_term='+slug,
		'/signup?utm_source=SEO&utm_medium='+page_slug+'&utm_content=female_smiling&utm_campaign='+page_slug+'_side_banner&utm_term='+slug,
		'/signup?utm_source=SEO&utm_medium='+page_slug+'&utm_content=female_grad&utm_campaign='+page_slug+'_side_banner&utm_term='+slug,
		'/signup?utm_source=SEO&utm_medium='+page_slug+'&utm_content=afam_male&utm_campaign='+page_slug+'_side_banner&utm_term='+slug
	];

	// get random num 0 - images.length
	const rand = Math.floor(Math.random() * images.length);

	// update ad_bar/ad_modal elem href with slug
	ad_bar_img.href += slug;
	ad_bar_text.href += slug;
	ad_bar_btn.href += slug;
	ad_modal_btn.href += slug;
	ad_modal_img.href += slug;

	// add bg pic to banner
	ad.style.backgroundImage = `url(${images[rand]})`;

	// on click, nav to /signup
	ad.addEventListener('click', () => window.location.href = utm[rand]);

	// make banner ad sticky at top
	let win, $ad, adTop, pos;

	// scroll event to watch when right banner hits top of window
	document.addEventListener('scroll', () => {

		// set vars when jquery is available
		if( $ ){
			win = $(window);
			_ad = $(ad);
			_adTop = _ad.offset().top;
		}

		// if vars are set, then start checking when banner is at top of window
		if( win && _ad && _adTop ){

			// pos is only set once sticky has been added
			if( pos && win.scrollTop() < pos && ad.classList.contains('sticky') ){
				ad.classList.remove('sticky');

			}else if( win.scrollTop() >= _adTop && !ad.classList.contains('sticky') ){
				// add sticky class if ad is at or past top of window
				pos = _adTop;
				ad.classList.add('sticky');
			}

		}

	});
</script>