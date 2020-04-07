@extends('manageColleges.master')


@section('content')
	<div class="row aor-dashboard-container">
		<div class="column small-12 medium-11 large-9 small-centered">
			
			<!-- aor dashboard header -->
			<div class="row collapse">
				<div class="column small-12 medium-10 medium-offset-2 dash-header">
					Dashboard
				</div>
			</div>

			<!-- aor indicators row - start -->
			<div class="row collapse">

				<!-- messages indicator -->
				<div class="column small-12 medium-4 medium-offset-2 dashboard-indicator indi-messages">
					<a href="#">
						<div class="row">
							<div class="column small-12 text-center indicator-label">Manage Students</div>
							<div class="column small-12 text-center indicator-pic">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/sales-messages-icon.jpg" alt="">
							</div>
							<div class="column small-12 text-center indicator-dynamic-info">&nbsp;Students</div>
						</div>
					</a>
				</div>

				<!-- client reporting indicator -->
				<div class="column small-12 medium-4 end dashboard-indicator indi-client">
					<a href="#">
						<div class="row">
							<div class="column small-12 text-center indicator-label">Manage Colleges</div>
							<div class="column small-12 text-center indicator-pic">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/sales-client-reporting-icon.jpg" alt="">
							</div>
							<div class="column small-12 text-center indicator-dynamic-info"><span>{{$num_of_colleges}}</span>&nbsp;Colleges</div>
						</div>
					</a>
				</div>

			</div>
			<!-- aor indicators row - end -->

			<!-- plexuss social accts row -->
			<div class="row">
				<div class="column small-12 dash-header plex-social-head text-center">
					Plexuss Social Accounts
				</div>
			</div>
			
			<!-- plex social accts icons row -->
			<div class="row">
				<div class="column small-12 medium-7 large-5 small-centered">
					
					<ul class="small-block-grid-3 medium-block-grid-5 text-center">
						<li>
							<a href="https://www.linkedin.com/company/plexuss-com" target="_blank">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/linkedin-icon_cc.png" alt="">
							</a>
						</li>
						<li>
							<a href="https://twitter.com/plexussupdates" target="_blank">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/twitter-icon_cc.png" alt="">
							</a>
						</li>
						<li>
							<a href="https://www.pinterest.com/plexussupdates/" target="_blank">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/pinterest-icon_cc.png" alt="">
							</a>
						</li>
						<li>
							<a href="https://www.facebook.com/pages/Plexusscom/465631496904278" target="_blank">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/fb-icon_cc.png" alt="">
							</a>
						</li>
						<li>
							<a href="http://plexussblog.tumblr.com/" target="_blank">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/tumblr-icon_cc.png" alt="">
							</a>
						</li>
					</ul>

				</div>
			</div>


		</div>
	</div>
@stop