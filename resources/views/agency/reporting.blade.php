@extends('agency.master')
@section('content')
<div class='reporting-header'>
	<div class='title'>
		<h3>Reporting</h3>
	</div>

	<div class='goal-wrapper'>
		<h5>Goals</h5>
		<div class='monthly-goals'>
			<div class='goal-stats'>
				<div class='goal-value' data-type='applications'><div class='loader-spinner small'></div></div>
				<div>Completed Apps</div>
			</div>
{{-- 			<div class='goal-stats'>
				<div class='goal-value' data-type='accepted'><div class='loader-spinner small'></div></div>
				<div>Accepted</div>
			</div> --}}
			<div class='goal-stats'>
				<div class='goal-value' data-type='enrolled'><div class='loader-spinner small'></div></div>
				<div>Enrolled</div>
			</div>
		</div>
	</div>
	{{-- Used to help with flex alignment --}}
	<div class='invisibile-div'></div>
</div>

<div class='reporting-wrapper'>
	{{-- <div class='export-btn'></div> --}}
	<table>
		<tr>
			<th>Month</th>
			<th>Completed Apps</th>
			<th>Accepted</th>
			<th>Enrolled</th>
			<th>Removed</th>
			<th class='export-report-btn'><span class='export-icon'></span><span class='export-text'>EXPORT</span></th>
		</tr>
	</table>

	<div class='loader-spinner'></div>
</div>


@stop