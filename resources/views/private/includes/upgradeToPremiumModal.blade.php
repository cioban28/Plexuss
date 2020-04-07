<div class="_upgradePremiumModal ">

	<div class="modalback"></div>

	<div class="premiumModal">

		<div id="upgradeModal_closeBtn" class="close-btn">&times;</div>

		<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/plexuss-premium-icon.png" />

		<h1>Join Plexuss Premium Today</h1>
		<h2>By becoming premium you will have access to:</h2>

		<div class="darkbox">
			<div class="clearfix">
				<div class="lock-img">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/unlock-icon.png" />
				</div>
				<div class="darkbox-title mb20">
					Unlock 50 College Essays That Got Students into Top Universities such as:
				</div>
			</div>	

			<div class="school-result clearfix">
				<div class="school-img-cont">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Harvard_University.png" />
				</div>
				<div class="school-name">Harvard University</div>
			</div>
			<div class="school-result clearfix">
				<div class="school-img-cont">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Massachusetts_Institute_of_Technology.png" />
				</div>
				<div class="school-name">Massachusetts Institute of Technology</div>
			</div>


			<div class="more-uni">+ More Universities</div>
			
		</div>
		<?php 
			$link = "/checkout/premium?cameFrom=" . $_SERVER['REQUEST_URI']; 
		?>

		<a href={{$link}} class="goto-upgrade-btn">Upgrade to premium for $499</a>



	</div>


</div>