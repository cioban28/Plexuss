<!-- entire manageColleges top nav - start -->
<?php 
	// dd($currentPage);
	$active_dash = '';
	$active_manageStudents = '';
	$active_manageColleges = '';

	switch ($currentPage) {
		case 'manageColleges':
			$active_dash = 'active';
			break;
		case 'manageStudents':
			$active_manageStudents = 'active';
			break;
		case 'manageCollegesReporting':
			$active_manageColleges = 'active';
			break;
	}
?>

<nav id="aor-top-bar" class="top-bar" data-topbar role="navigation">
  <section class="top-bar-section aor-topbar-section">

    <!-- Right Nav Section -->
    <ul class="right">
		<li class="{{$active_dash}}"><a href="../admin">Dashboard</a></li>
		<li class="{{$active_manageStudents}}"><a href="../admin/inquiries">Manage Students</a></li>
		<li class="{{$active_manageColleges}}"><a href="#">Manage Colleges</a></li>
    </ul>

    <!-- Left Nav Section -->
    <ul class="left hide-for-small-only">
    	<li>
    		<span><a href="/"><img src="/images/plexuss_logo.png" alt="Plexuss.com"></a></span>
    	</li>
    </ul>

  </section>
</nav>

<!-- entire manageColleges top nav - end -->

