
<!--
	SHARE BUTTONS
	Before using this include, wrap it in a div and give it a a predefined class
	to mimic the styling of a set of share buttons, or create another class to
	customize a new style.
-->
<!--/////////////// SOCIAL MEDIA BUTTONS \\\\\\\\\\\\\\\-->
<!--//////////////////// NEWS PAGES \\\\\\\\\\\\\\\\\\\\-->
@if( isset( $share_buttons ) )


	@if( isset($college_data->likes_tally))
	<span class="likes"><img id="like_img" onClick='Plex.setIndividualPalgeLikeTally("news_articles", "id", "{{$hased_news_id or ''}}", this);' src="{{$is_liked_img or '/images/social/like-icon-dark-gray.png'}}"> LIKES: <span id="number_of_likes">{{$likes_tally or '0'}}</span></span>
	@endif

	@if( isset( $share_buttons['stl_text'] ) )
		<span class="stl">{{ $share_buttons['stl_text'] }}</span>
	@endif
	<!-- PRINT BUTTON HIDDEN UNTIL PRINT FORMATTING DONE
	<div class="print_me_box" onClick="window.print()">
		<span class='stl'>PRINT ME:</span>
		<span class='print_me'></span>
	</div>
	-->
	<a class='social_share share_facebook 
	@if( isset( $share_buttons["extra_classes"] ) )
		@foreach( $share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{{ json_encode( $share_buttons["params"]["facebook"] ) }}'
	></a>
	<a class='social_share share_twitter 
	@if( isset( $share_buttons["extra_classes"] ) )
		@foreach( $share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{{ json_encode( $share_buttons["params"]["twitter"] ) }}'
	></a>
	<a class='social_share share_pinterest
	@if( isset( $share_buttons["extra_classes"] ) )
		@foreach( $share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{{ json_encode( $share_buttons["params"]["pinterest"] ) }}'
        data-pin-do="buttonPin"
        data-pin-config="above"
	></a>
	<a class='social_share share_linkedin
	@if( isset( $share_buttons["extra_classes"] ) )
		@foreach( $share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{{ json_encode( $share_buttons["params"]["linkedin"] ) }}'
	></a>
<!--\\\\\\\\\\\\\\\\\\\\ NEWS PAGES ////////////////////-->
<!--//////////////////// COLLEGE PAGES \\\\\\\\\\\\\\\\\\\\-->
@elseif( isset( $college_data->share_buttons ) )

	@if( isset($college_data->likes_tally))
	<span class="likes"><img id="like_img" onClick='Plex.setIndividualPalgeLikeTally("college", "id", "{{$college_data->hased_college_id or ''}}", this);' src="{{$college_data->is_liked_img or '/images/social/like-icon-dark-gray.png'}}"> LIKES: <span id="number_of_likes">{{$college_data->likes_tally or '0'}}</span></span>
	@endif

	@if( isset( $college_data->share_buttons['stl_text'] ) )
		<span class="stl">{{ $college_data->share_buttons['stl_text'] }}</span>
	@endif
	<!-- PRINT BUTTON HIDDEN UNTIL PRINT FORMATTING DONE
	<div class="print_me_box" onClick="window.print()">
		<span class='stl'>PRINT ME:</span>
		<span class='print_me'></span>
	</div>
	-->
	<a class='social_share share_facebook 
	@if( isset( $college_data->share_buttons["extra_classes"] ) )
		@foreach( $college_data->share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{
			"platform":"facebook"
		}'
	></a>
	<a class='social_share share_twitter 
	@if( isset( $college_data->share_buttons["extra_classes"] ) )
		@foreach( $college_data->share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{
			"platform":"twitter"
		}'
	></a>
	<a class='social_share share_pinterest
	@if( isset( $college_data->share_buttons["extra_classes"] ) )
		@foreach( $college_data->share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{
			"platform":"pinterest"
		}'
	></a>
	<a class='social_share share_linkedin
	@if( isset( $college_data->share_buttons["extra_classes"] ) )
		@foreach( $college_data->share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{
			"platform":"linkedin"
		}'
	></a>
@endif
<!--\\\\\\\\\\\\\\\\\\\\ COLLEGE PAGES ////////////////////-->




<!-- \\\\\\\\\\\\\\\\ B2B BLOG NEW FEATURES PAGE ////////////////-->


@if(isset($a) && isset($a->share_buttons))


	@if( isset( $a->share_buttons['stl_text'] ) )
		<span class="stl">{{ $a->share_buttons['stl_text'] }}</span>
	@endif
	
	<a class='social_share share_facebook 
	@if( isset( $a->share_buttons["extra_classes"] ) )
		@foreach( $a->share_buttons["extra_classes"] as $class )
			{{ " ".$class }}
		@endforeach
	@endif'
		data-params='{{ json_encode( $a->share_buttons["params"]["facebook"] ) }}'
	></a>
	<a class='social_share share_twitter 
	@if( isset( $a->share_buttons["extra_classes"] ) )
		@foreach( $a->share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{{ json_encode( $a->share_buttons["params"]["twitter"] ) }}'
	></a>
	<a class='social_share share_pinterest
	@if( isset( $a->share_buttons["extra_classes"] ) )
		@foreach( $a->share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{{ json_encode( $a->share_buttons["params"]["pinterest"] ) }}'
        data-pin-do="buttonPin"
        data-pin-config="above"
	></a>
	<a class='social_share share_linkedin
	@if( isset( $a->share_buttons["extra_classes"] ) )
		@foreach( $a->share_buttons["extra_classes"] as $class )
			{{ " " . $class }}
		@endforeach
	@endif'
		data-params='{{ json_encode( $a->share_buttons["params"]["linkedin"] ) }}'
	></a>
@endif

<!-- \\\\\\\\\\\\\\\\\ end BLOG NEW FEATURES ////////////////// -->



<!--\\\\\\\\\\\\\\\ SOCIAL MEDIA BUTTONS ///////////////-->
