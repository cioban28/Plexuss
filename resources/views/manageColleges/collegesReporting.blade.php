@extends('manageColleges.master')


@section('content')
<?php 

	$column_headers = ['Login As', 'Rep', 'Name of School', 'Last Login', 'Inquiries', 'Recommendations', 'Total Pending', 'Pending Approved by Students', '% pending approved by students', 'Total Handshakes'];
	// dd($clients_arr);

?>

	
	<!-- filter row - start -->
	<div class="row aor-filter-container">
	</div>
	<!-- filter row - end -->




	<!-- excel like doc - start -->
	<div class="row collapse aor-dataSheet-container">
		<div class="column small-12">
			
			<table id="aor_dataTable" class="display" cellspacing="0" width="100%">
			    <thead>
			        <tr>
			        	<th></th>
			        	@foreach($column_headers as $column)
			            	<th>{{$column}}</th>
			            @endforeach
			        </tr>
			    </thead>
			    <tbody>
			    	<!-- rows -->
			    	<?php $cnt=1; ?>
			        @foreach($clients_arr as $key)

			        	<tr class="@if(isset($key['is_client']) && $key['is_client']) is-client @endif">
			        		<td>
			        			{{$cnt}}
			        		</td><!-- Login As -->	
			        		<td>
			        			<a href="{{$key['login_as'] or ''}}">
			        				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png" alt="login as">
			        			</a>
			        		</td><!-- Rep -->
			        		<td>
			        			{{$key['name'] or ''}}
			        		</td><!-- Org Name -->
			        		<td>
			        			{{$key['org_name'] or ''}}
			        		</td><!-- Last Logged in -->
			        		<td>
			        			{{$key['last_logged_in'] or ''}}
			        		</td><!-- Inquiries -->
			        		<td>
			        			{{$key['num_of_inquiries'] or ''}}
			        		</td><!-- Recommendations -->
			        		<td>
			        			{{$key['num_of_recommendations'] or ''}}
			        		</td><!-- Total Pending -->
			        		<td>
			        			{{$key['total_num_of_pending_from_all_sources'] or ''}}
			        		</td><!-- Pending Approved by Students --> 
			        		<td>
			        			{{$key['total_num_of_approved_by_pending'] or ''}}
			        		</td><!-- Percentage of Pending Approved by Students -->
			        		<td>
			        			{{$key['percent_of_approved_by_pending'] or ''}}
			        		</td><!-- Total HandShakes -->
			        		<td>
			        			{{$key['num_of_total_approved'] or ''}}
			        		</td>
			        		
			        	</tr>
			        <?php $cnt++; ?>	
			        @endforeach
			    </tbody>
			</table>

		</div>
	</div>
	<!-- excel like doc - end -->


@stop