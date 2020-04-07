<?php 
// echo '<pre>';
// print_r($data);
// echo '</pre>';
// exit();
$profileCompletion = null;
if( isset($filters[0]) && !empty($filters[0]) ){
	$filter = $filters[0];
	if (isset($filter)) {
		if (isset($filter['profileCompletion'][0])) {
			$profileCompletion = $filter['profileCompletion'][0];
		}
	}
}
?>
<div class="row filter-by-profileCompletion-container filter-page-section" data-section="profileCompletion">

	<div class="column small-12 large-6">
	
		{{Form::open()}}
		<br />
		<div class="row component" data-component="profileCompletion">
			<div class="column small-12 medium-9">

				{{Form::label('profileCompletion_filter', 'Profile Completion:', array('class' => 'make_bold'))}}
				{{Form::select('profileCompletion', array('' => 'Select...', '10' => '10%', '20' => '20%', '30' => '30%', '40' => '40%', '50' => '50%', '60' => '60%', '70' => '70%', '80' => '80%', '90' => '90%', '100' => '100%'), $profileCompletion, array('id' => 'profileCompletion_filter', 'class' => 'select-filter filter-this isProfComp'))}}
			</div>
		</div>
		{{Form::close()}}
	</div>



	<div class="column small-12 large-6">
		
		<br />
			Select the minimum Profile Completion percentage that a student must reach to be considered a viable candidate for recruitment.
		
	</div>	

</div>