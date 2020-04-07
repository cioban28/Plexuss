@extends('sales.master')


@section('content')
	<div class="row sales-dashboard-container">
		<div class="column small-12 medium-11 large-9 small-centered">
			
			<!-- sales dashboard header -->
			<div class="row collapse">
				<div class="column small-12 medium-10 medium-offset-2 dash-header">
					Dashboard
				</div>
			</div>

			<!-- sales indicators row - start -->
			<div class="row collapse">	

				<!-- client reporting indicator -->
				<div class="column small-12 medium-4 dashboard-indicator indi-client">
					<a href="/sales/clients">
						<div class="row">
							<div class="column small-12 text-center indicator-label">Client Reporting</div>
							<div class="column small-12 text-center indicator-pic">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/sales-client-reporting-icon.jpg" alt="">
							</div>
							<div class="column small-12 text-center indicator-dynamic-info"><span>{{$num_of_clients or 0}}</span> Clients</div>
						</div>
					</a>
				</div>

				<!-- messages indicator -->
				<div class="column small-12 medium-4 dashboard-indicator indi-messages">
					<a href="/sales/pickACollege">
						<div class="row">
							<div class="column small-12 text-center indicator-label">Pick A College</div>
							<div class="column small-12 text-center indicator-pic">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/pick_a_college_icon.png" alt="">
							</div>
							<div class="column small-12 text-center indicator-dynamic-info">&nbsp;</div>
						</div>
					</a>
				</div>

				<!-- application order indicator -->
				<div class="column small-12 medium-4 dashboard-indicator indi-application">
					<a href="/sales/application-order">
						<div class="row">
							<div class="column small-12 text-center indicator-label">Application Order</div>
							<div class="column small-12 text-center indicator-pic">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/pick_a_college_icon.png" alt="">
							</div>
							<div class="column small-12 text-center indicator-dynamic-info">&nbsp;</div>
						</div>
					</a>
				</div>

			</div>
			<!-- sales indicators row - end -->

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