<div class='topbanner'></div>
<div class='show-for-medium-up navmenu'>
	<div class='row'>
		<div class="large-centered large-12 column text-center">
			<?php
				$urlpath = Request::path();
			?>
			<ul>
				@if ( $urlpath == 'about' || $urlpath == 'team' || $urlpath == 'careers-internships' || stripos($urlpath, 'careers-internships') !== false || $urlpath == 'help' || stripos( $urlpath , 'help') !== false )
					<li class='<?php if( $urlpath == 'about'){echo 'selected';} ?>'><a href="/about">About</a></li>
					<li class='<?php if( $urlpath == 'team'){echo 'selected';} ?>'><a href="/team">Meet the team</a></li>
					<li class='<?php if( stripos($urlpath, 'careers-internships') !== false ){echo 'selected';} ?>'><a href="/careers-internships">Careers &amp; Internships</a></li>
					<li class='<?php if(stripos( $urlpath , 'help') !== false){echo 'selected';} ?>'><a href="/help">Help &amp; FAQ</a></li>
					
				@else
					<li class='<?php if( $urlpath == 'college-submission'){echo 'selected';} ?>'><a href="/solutions">Join as a College</a></li>
					<li class='<?php if( $urlpath == 'college-prep'){echo 'selected';} ?>'><a href="/solutions">Join as College Prep</a></li>
					<li class='<?php if(stripos( $urlpath , 'scholarship-submission') !== false){echo 'selected';} ?>'><a href="/scholarship-submission">Scholarship Submission</a></li>
					<li class='<?php if( $urlpath == 'contact'){echo 'selected';} ?>'><a href="/contact">Contact</a></li>
				@endif
			</ul>
		</div>
	</div>
</div>
