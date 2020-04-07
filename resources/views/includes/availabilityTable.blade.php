
<!-- availability table -->
<?php 
	$myTime = date('g:i a');
	$myZone = date_default_timezone_get();
?>
<div class="row c-info">
		<div class="column small-12"><span class="c-info-title">Availability</span></div>
		<div class="column small-12">
			
			<div class="avail-table">

				<!-- titles -->
				<div class="row">
					<div class="column small-6">
						<span class="avail-title">Time Zone ID/ {{$key['country_name'] or 'not available'}}</span>
						<span>&#x25BE;</span>
					</div>
					<div class="column small-6">
						<span class="avail-title">Time Zone {{$key['country_code'] or '?'}}/ {{$myZone or 'unknown'}}</span>
						<span>&#x25BE;</span>
					</div>
				</div>

				<!-- table content container - under titles -->
				<div class="row">

					<!-- left side -->
					<div class="column small-6 avail-col-left">
							<div class="current-time">{{$myTime}}</div>
							<div class="days-cont">
								<div class="row">
									<div class="column small-3">Mon</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
								<div class="row">
									<div class="column small-3">Tue</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
								<div class="row">
									<div class="column small-3">Wed</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
								<div class="row">
									<div class="column small-3">Thu</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
								<div class="row">
									<div class="column small-3">Fri</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
							</div>
					</div>

					<!-- right side -->
					<div class="column small-6">
						<div class="current-time">{{$myTime}}</div>
						<div class="days-cont">
							<div class="row">
									<div class="column small-3">Mon</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
								<div class="row">
									<div class="column small-3">Tue</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
								<div class="row">
									<div class="column small-3">Wed</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
								<div class="row">
									<div class="column small-3">Thu</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
								<div class="row">
									<div class="column small-3">Fri</div>
									<div class="column small-9">
										{{ $key['mon_avail'] or '5:00am-9:00pm'}}
									</div>
								</div>
						</div>
					</div>
				</div>

			</div><!-- end avilability table -->
		</div><!-- container in place due to previous layout choioces -->
</div><!-- end of availability section -->
									
										

										


											
								