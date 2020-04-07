<!-- entire sales top nav - start -->
<?php 
	// dd($currentPage);
	// dd(get_defined_vars());
	$active_dash = '';
	$active_clients = '';
	$active_pickACollege = '';
	$active_billing = '';
	$active_applicationOrder = '';
	$active_agencyReporting = '';
	$active_scholarships = '';
    $active_pixelTesting = '';
    $active_email_reporting = '';

	switch ($currentPage) {
		case 'sales':
			$active_dash = 'active';
			break;
		case 'sales-pick-a-college':
			$active_pickACollege = 'active';
			break;
		case 'sales-application-order':
			$active_applicationOrder = 'active';
			break;
		case 'sales-clientReporting':
			$active_clients = 'active';
			break;
		case 'sales-billing':
			$active_billing = 'active';
			break;
		case 'sales-agency-reporting':
			$active_agencyReporting = 'active';
			break;
		case 'sales-scholarships':
			$active_scholarships = 'active';
			break;
        case 'sales-pixel-tracking-test':
            $active_pixelTesting = 'active';
            break;
        case 'sales-email-reporting':
            $active_email_reporting = 'active';
            break;
	}
?>

<nav id="sales-top-bar" class="top-bar" data-topbar role="navigation">
  <section class="top-bar-section sales-topbar-section">

    <!-- Right Nav Section -->
    <ul class="right">
		<li class="{{$active_dash}}"><a href="/sales">Dashboard</a></li>
		<li class="{{$active_pickACollege}}"><a href="/sales/pickACollege">Pick a College</a></li>
		<li class="{{$active_applicationOrder}}"><a href="/sales/application-order">Application Order</a></li>
			<li class="{{$active_agencyReporting}}"><a href="/sales/agency-reporting">Agency Reporting</a></li>
		<li class="{{$active_clients}}"><a href="/sales/clients">Client Reporting</a></li>
        <li class="{{$active_scholarships}}"><a href="/sales/scholarships">Scholarships</a></li>
		<li class="{{$active_pixelTesting}}"><a href="/sales/pixelTrackingTest">Pixel Testing</a></li>
		<li class="{{$active_email_reporting}}"><a href="/sales/email-reporting">Email Reporting</a></li>

    </ul>

    <!-- Left Nav Section -->
    <ul class="left hide-for-small-only" style="position:absolute;">
    	<li>
    		<span><a href="/"><img src="/images/plexuss_logo.png" alt="Plexuss.com"></a></span>
			<span>Sales Central Control</span>
    	</li>
    </ul>

  </section>
</nav>

<!-- entire sales top nav - end -->

