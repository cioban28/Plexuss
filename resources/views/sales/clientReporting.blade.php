@extends('sales.master')


@section('content')
<?php 

	$is_client = 1;
	$column_headers = ['Login As', 'MSG', 'Rep', 'Department', 'Type of Customer', 'Name of School','Product', 'Date Joined', 'Last Login', 'Trigger', 'Goal', 'Engagement', 'Yesterday', 'Weekly', 'Monthly', 'Delta', 'Overall', 'Inquiries', 'Inquiries rejected', 'Inquiries accepted', 'Inqiries Idle', 'Recommendations', 'Rec Accepted/Pending', 'Rec Rejected', 'Rec Idle', 'Rec Accpted via Auto', 'Total pending from all sources', 'Pending Approved by Students', '% pending approved by students', 'Search Recruited', 'Total Approved', '% approved via inquiry', '% approved via manual rec', '% approved via auto approve rec', '% approved via search', '# of profiles viewed','Filtered Rec', 'Non-Filtered Rec', 'Yes/No/Neutral Rec', 'Total Days Chatted', '# of daily chat users', 'Total Chat Received', 'Total Chat Sent', '# of messages received', '# of messages sent', 'Total Chat/Messages', 'Avg Response Rate to Messages', '# of likes for colleges', 'Exported file', 'Uploaded Ranking'];
	// echo '<pre>';
	// print_r($clients_arr);
	// echo '</pre>';
	// exit();
	// dd($clients_arr);
?>

	
	<!-- filter row - start -->
	<div class="row sales-filter-container">
		{{Form::open()}}
		<div class="column small-12 medium-5">
			<div class="row filter-row-left-side">

				<div class="column small-12">
					{{Form::checkbox('show-clients', 'value', false, array('id'=>'show_clients_only'))}}
					{{Form::label('show_clients_only', 'Show clients only')}}
					<br class="hide-for-large-up">
					<span class="choose-columns-btn">Choose columns to hide</span>
				</div>

				<!-- hidden column selection pane -->
				<div class="column-selection-pane">
					<div class="row">
						@for( $a = 1; $a <= count($column_headers); $a++ )
						<div class="column small-12 medium-4 col-select-column @if($a == 1 || $a == 2) hide @endif">
							{{Form::checkbox('selection-checkbox-'.$a, $a, false, array('id'=>'column-'.$a, 'class'=>'col-select-chkbox'))}}
							{{Form::label('column-'.$a, $column_headers[$a-1])}}
						</div>
						@endfor
					</div>

					<div class="row">
						<div class="column small-12">
							<span class="reset-col-btn">Reset Columns</span>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="column small-12 medium-7">
			<div class="row filter-row-right-side">
				
				<div class="column small-12 medium-6 end">
					<div class="row collapse">

						<!--
						<div class="column small-4">
							{{Form::text('sales-report-from', null, array('class'=>'salesReport-date salesReport-from', 'placeholder'=>'From'))}}
						</div>

					
						<div class="column small-1 text-center">
							<div class="date-dash"> - </div>
						</div>

						
						<div class="column small-4">
							{{Form::text('sales-report-to', null, array('class'=>'salesReport-date salesReport-to', 'placeholder'=>'To'))}}
						</div>

						
						<div class="column small-2 end text-center">
							<div class="submit-date-filter-btn client-rpt">Go</div>
						</div>
						-->
					</div>
				</div>
			
			</div>
		</div>
		{{Form::close()}}
	</div>
	<!-- filter row - end -->




	<!-- excel like doc - start -->
	<div class="row collapse sales-dataSheet-container">
		<div class="column small-12">
			
			<table id="sales_dataTable" class="display" cellspacing="0" width="100%">
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
			        		</td>
			        		<td><!-- Login As -->	
			        			<a href="{{$key['login_as'] or ''}}">
			        				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png" alt="login as">
			        			</a>
			        		</td>
			        		<td class="text-center"><!-- MSG -->	
			        			<a href="{{'/sales/messages/'.$key['org_branch_id']. '/'. $key['user_id']}}">
			        				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/message-gray.png" alt="School Message Icon">
			        			</a>
			        		</td>
			        		<td><!-- Rep -->
			        			{{$key['name'] or ''}}
			        		</td>
			        		<td><!-- Department -->
			        			Dept
			        		</td>
			        		<td> <!-- Type of customer -->
			        			{{$key['customer_type'] or ''}}
			        		</td>
			        		<td><!-- Name of School -->
			        			{{$key['school_name'] or ''}}
			        		</td>
			        		<td> <!-- Product -->
			        			Product
			        		</td>
			        		<td><!-- Date Joined -->
			        			{{$key['date_joined'] or ''}}
			        		</td>
			        		<td><!-- Last Logged in -->
			        			{{$key['last_logged_in'] or ''}}
			        		</td>
			        		<td class="trigger-link" data-school-id="{{$key['org_branch_id']}}" data-triggers='{"triggers-is-set" : "{{$key["triggers"]["is_set"]}}",
                                "triggers-frequency" : "{{$key["triggers"]["frequency"]}}",
                                "triggers-emails":"{{$key["triggers"]["emails"] or ''}}",
                                "triggers-emergency-is-set" : "{{$key["triggers"]["emergency"]["is_set"]}}",
                                "triggers-emergency-perc" : "{{$key["triggers"]["emergency"]["perc"]}}"}'><!-- Trigger -->
			        			<a href="#" class="button tiny radius FormSubmit">Trigger
			        				@if(!($key["triggers"]["is_set"] == 0 && $key["triggers"]["emergency"]["is_set"] == 0))
			        					<img class="triggerSet" src="/images/admin/check-green.png"/>
			        				@else

			        				@endif
			        			</a>
			        		</td>
			        		<td class="goal-link" style="padding-top: 25px;" data-goals='{{$key["goals"] or ""}}'> <!-- Goal -->
			        			<a href="#" class="button tiny radius FormSubmit @if($key['is_goal_setup'] == 1 && $key['approved-perc-monthly'] > 0) increasing @elseif($key['is_goal_setup'] == 1) decreasing @endif">{{$key['approved-perc-monthly'] or '0'}}%</a>
			        		</td>
			        		<td class="engagement-link" style="padding-top: 25px;" data-engagement='{
			        			"daysSinceJoined" : "{{$key["overall_data"]["days_since_joining"]}}" ,
			        			"twoWeeksAgo-data-last-login-score" : "{{$key["twoWeeksAgo_data"]["last_logged_in"]["text"]}}",
			        			"twoWeeksAgo-data-num-profile-score" : "{{$key["twoWeeksAgo_data"]["num_profile_view"]["raw_value"]}}", 
			        			"twoWeeksAgo-data-num-inquiries-score" : "{{$key["twoWeeksAgo_data"]["num_of_inquiries"]["raw_value"]}}", 
			        			"twoWeeksAgo-data-inquiry-activity-score": "{{$key["twoWeeksAgo_data"]["inquiry_activity"]["raw_value"]}}", 
			        			"twoWeeksAgo-data-recommendation-activity-score" : "{{$key["twoWeeksAgo_data"]["recommendation_activity"]["raw_value"]}}", 
			        			"twoWeeksAgo-data-num-approved-score" : "{{$key["twoWeeksAgo_data"]["num_of_total_approved"]["raw_value"]}}", 
			        			"twoWeeksAgo-data-num-adv-search-approved-score" : "{{$key["twoWeeksAgo_data"]["num_of_advance_search_approved"]["raw_value"]}}", 
			        			"twoWeeksAgo-data-num-days-chatted-score" : "{{$key["twoWeeksAgo_data"]["num_of_days_chatted"]["raw_value"]}}", 
			        			"twoWeeksAgo-data-total-chat-sent-score" : "{{$key["twoWeeksAgo_data"]["total_chat_sent"]["raw_value"]}}",
			        			"twoWeeksAgo-data-total-msg-sent-score" : "{{$key["twoWeeksAgo_data"]["total_msg_sent"]["raw_value"]}}",
			        			"twoWeeksAgo-data-total-score" : "{{$key["twoWeeksAgo_data"]["total"]["score"]}}",
			        			"monthAgo-data-last-login-score" : "{{$key["monthAgo_data"]["last_logged_in"]["text"]}}",
			        			"monthAgo-data-num-profile-score" : "{{$key["monthAgo_data"]["num_profile_view"]["raw_value"]}}", 
			        			"monthAgo-data-num-inquiries-score" : "{{$key["monthAgo_data"]["num_of_inquiries"]["raw_value"]}}", 
			        			"monthAgo-data-inquiry-activity-score": "{{$key["monthAgo_data"]["inquiry_activity"]["raw_value"]}}", 
			        			"monthAgo-data-recommendation-activity-score" : "{{$key["monthAgo_data"]["recommendation_activity"]["raw_value"]}}", 
			        			"monthAgo-data-num-approved-score" : "{{$key["monthAgo_data"]["num_of_total_approved"]["raw_value"]}}", 
			        			"monthAgo-data-num-adv-search-approved-score" : "{{$key["monthAgo_data"]["num_of_advance_search_approved"]["raw_value"]}}", 
			        			"monthAgo-data-num-days-chatted-score" : "{{$key["monthAgo_data"]["num_of_days_chatted"]["raw_value"]}}", 
			        			"monthAgo-data-total-chat-sent-score" : "{{$key["monthAgo_data"]["total_chat_sent"]["raw_value"]}}",
			        			"monthAgo-data-total-msg-sent-score" : "{{$key["monthAgo_data"]["total_msg_sent"]["raw_value"]}}",
			        			"monthAgo-data-total-score" : "{{$key["monthAgo_data"]["total"]["score"]}}", 
			        			"overall-data-last-login-score" : "{{$key["overall_data"]["last_logged_in"]["text"]}}",
			        			"overall-data-num-profile-score" : "{{$key["overall_data"]["num_profile_view"]["raw_value"]}}", 
			        			"overall-data-num-inquiries-score" : "{{$key["overall_data"]["num_of_inquiries"]["raw_value"]}}", 
			        			"overall-data-inquiry-activity-score": "{{$key["overall_data"]["inquiry_activity"]["raw_value"]}}", 
			        			"overall-data-recommendation-activity-score" : "{{$key["overall_data"]["recommendation_activity"]["raw_value"]}}", 
			        			"overall-data-num-approved-score" : "{{$key["overall_data"]["num_of_total_approved"]["raw_value"]}}", 
			        			"overall-data-num-adv-search-approved-score" : "{{$key["overall_data"]["num_of_advance_search_approved"]["raw_value"]}}", 
			        			"overall-data-num-days-chatted-score" : "{{$key["overall_data"]["num_of_days_chatted"]["raw_value"]}}", 
			        			"overall-data-total-chat-sent-score" : "{{$key["overall_data"]["total_chat_sent"]["raw_value"]}}",
			        			"overall-data-total-msg-sent-score" : "{{$key["overall_data"]["total_msg_sent"]["raw_value"]}}", 
			        			"overall-data-total-score" : "{{$key["overall_data"]["total"]["score"]}}", 
			        			"overall-data-total-rank" : "{{$key["overall_data"]["total"]["rank"]}}",
			        			"twoWeeksAgo-data-last-login-grade" : "{{$key["twoWeeksAgo_data"]["last_logged_in"]["grade"]}}",
			        			"twoWeeksAgo-data-num-profile-grade" : "{{$key["twoWeeksAgo_data"]["num_profile_view"]["grade"]}}", 
			        			"twoWeeksAgo-data-num-inquiries-grade" : "{{$key["twoWeeksAgo_data"]["num_of_inquiries"]["grade"]}}", 
			        			"twoWeeksAgo-data-inquiry-activity-grade": "{{$key["twoWeeksAgo_data"]["inquiry_activity"]["grade"]}}", 
			        			"twoWeeksAgo-data-recommendation-activity-grade" : "{{$key["twoWeeksAgo_data"]["recommendation_activity"]["grade"]}}", 
			        			"twoWeeksAgo-data-num-approved-grade" : "{{$key["twoWeeksAgo_data"]["num_of_total_approved"]["grade"]}}", 
			        			"twoWeeksAgo-data-num-adv-search-approved-grade" : "{{$key["twoWeeksAgo_data"]["num_of_advance_search_approved"]["grade"]}}", 
			        			"twoWeeksAgo-data-num-days-chatted-grade" : "{{$key["twoWeeksAgo_data"]["num_of_days_chatted"]["grade"]}}", 
			        			"twoWeeksAgo-data-total-chat-sent-grade" : "{{$key["twoWeeksAgo_data"]["total_chat_sent"]["grade"]}}",
			        			"twoWeeksAgo-data-total-msg-sent-grade" : "{{$key["twoWeeksAgo_data"]["total_msg_sent"]["grade"]}}",
			        			"twoWeeksAgo-data-filter-active" : "{{$key["twoWeeksAgo_data"]["filter_active"]}}",
			        			"twoWeeksAgo-data-total-grade" : "{{$key["twoWeeksAgo_data"]["total"]["grade"]}}",
			        			"monthAgo-data-last-login-grade" : "{{$key["monthAgo_data"]["last_logged_in"]["grade"]}}",
			        			"monthAgo-data-num-profile-grade" : "{{$key["monthAgo_data"]["num_profile_view"]["grade"]}}", 
			        			"monthAgo-data-num-inquiries-grade" : "{{$key["monthAgo_data"]["num_of_inquiries"]["grade"]}}", 
			        			"monthAgo-data-inquiry-activity-grade": "{{$key["monthAgo_data"]["inquiry_activity"]["grade"]}}", 
			        			"monthAgo-data-recommendation-activity-grade" : "{{$key["monthAgo_data"]["recommendation_activity"]["grade"]}}", 
			        			"monthAgo-data-num-approved-grade" : "{{$key["monthAgo_data"]["num_of_total_approved"]["grade"]}}", 
			        			"monthAgo-data-num-adv-search-approved-grade" : "{{$key["monthAgo_data"]["num_of_advance_search_approved"]["grade"]}}", 
			        			"monthAgo-data-num-days-chatted-grade" : "{{$key["monthAgo_data"]["num_of_days_chatted"]["grade"]}}", 
			        			"monthAgo-data-total-chat-sent-grade" : "{{$key["monthAgo_data"]["total_chat_sent"]["grade"]}}",
			        			"monthAgo-data-total-msg-sent-grade" : "{{$key["monthAgo_data"]["total_msg_sent"]["grade"]}}", 
			        			"monthAgo-data-filter-active" : "{{$key["monthAgo_data"]["filter_active"]}}",
			        			"monthAgo-data-total-grade" : "{{$key["monthAgo_data"]["total"]["grade"]}}",
			        			"overall-data-last-login-grade" : "{{$key["overall_data"]["last_logged_in"]["grade"]}}",
			        			"overall-data-num-profile-grade" : "{{$key["overall_data"]["num_profile_view"]["grade"]}}", 
			        			"overall-data-num-inquiries-grade" : "{{$key["overall_data"]["num_of_inquiries"]["grade"]}}", 
			        			"overall-data-inquiry-activity-grade": "{{$key["overall_data"]["inquiry_activity"]["grade"]}}", 
			        			"overall-data-recommendation-activity-grade" : "{{$key["overall_data"]["recommendation_activity"]["grade"]}}", 
			        			"overall-data-num-approved-grade" : "{{$key["overall_data"]["num_of_total_approved"]["grade"]}}", 
			        			"overall-data-num-adv-search-approved-grade" : "{{$key["overall_data"]["num_of_advance_search_approved"]["grade"]}}", 
			        			"overall-data-num-days-chatted-grade" : "{{$key["overall_data"]["num_of_days_chatted"]["grade"]}}", 
			        			"overall-data-total-chat-sent-grade" : "{{$key["overall_data"]["total_chat_sent"]["grade"]}}",
			        			"overall-data-total-msg-sent-grade" : "{{$key["overall_data"]["total_msg_sent"]["grade"]}}", 
			        			"overall-data-filter-active" : "{{$key["overall_data"]["filter_active"]}}",
			        			"overall-data-total-grade" : "{{$key["overall_data"]["total"]["grade"]}}",
			        			"college_id" : "{{$key["this_college_id"]}}",
			        			"user_id" : "{{$key["this_user_id"]}}"
			        		}'>
			        			<a class="button tiny radius FormSubmit @if($key['overall_data']['total']['grade'] == 'A' || $key['overall_data']['total']['grade'] == 'B') increasing @elseif($key['overall_data']['total']['grade'] == 'C' || $key['overall_data']['total']['grade'] == 'D' || $key['overall_data']['total']['grade'] == 'F') decreasing @endif">
			        				{{$key["overall_data"]["total"]["grade"] or 'N/A'}}
			        			</a>
			        		</td>
			        		<td>
			        			{{$key['yesterdays_activity_grade'] or 'N/A'}}
			        		</td>
			        		<td>
			        			{{$key['last_14days_activity_grade'] or 'N/A'}}
			        		</td>
			        		<td>
			        			{{$key['last_30days_activity_grade'] or 'N/A'}}
			        		</td>
			        		<td class="delta @if($key['engagement_delta'] > 0) positive @endif">
			        			@if($key['engagement_delta'] > 0)
			        			+{{$key['engagement_delta'] or 0}}	
			        			@else
			        			{{$key['engagement_delta'] or 0}}	
			        			@endif
			        		</td>
			        		<td>
			        			{{$key['overall_activity_score'] or 0}}	
			        		</td>
			        		<td>
			        			{{$key['num_of_inquiries'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_inquiries_rejected'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_inquiries_accepted'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_inquiries_idle'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_recommendations'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_recommendations_accepted_pending'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_recommendations_rejected'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_recommendations_idle'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_advance_search_approved'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['total_num_of_pending_from_all_sources'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['total_num_of_approved_by_pending'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['percent_of_approved_by_pending'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of_advance_search'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_of__total_approved'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['percent_approved_via_inquiry'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['percent_approved_via_recommendation'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['percent_approved_via_auto_approve_recommendation'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['percent_approved_via_advance_search'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['num_profile_view'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['filtered_recommendations'] or ''}}
			        		</td>	
			        		<td>
			        			{{$key['non_filtered_recommendations'] or ''}}
			        		</td>	
			        		<td>
			        			{{$key['college_recommendation_action'] or ''}}
			        		</td>	
			        		<td>
			        			{{$key['num_of_days_chatted'] or 0}}
			        		</td>
			        		<td>
			        			{{$key['num_daily_chat'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['total_chat_received'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['total_chat_sent'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['total_msg_received'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['total_msg_sent'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['total_messages'] or ''}}
			        		</td>
			        		<td>
			        			{{$key['avg_response_rate'] or 0}}
			        		</td>
			        		<td>
			        			{{$key['num_of_likes'] or 0}}
			        		</td>
			        		<td>
			        			{{$key['export_file_cnt'] or 0}}
			        		</td>
			        		<td>
			        			{{$key['num_of_uploaded_ranking'] or 0}}
			        		</td>
			        	</tr>
			        <?php $cnt++; ?>	
			        @endforeach
			    </tbody>
			</table>

		</div>
	</div>
	<!-- excel like doc - end -->
	
	<!-- goal data modal -->
	<div id="goalDataModal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog"> 
		<div class="row" style="padding-bottom: 30px;">
			<div class="column small-12 medium-2 large-2" style="font-size: 20px;">
				Goals
			</div>
			<div class="column small-11 medium-8 large-8 text-center">
				<span id="monthly" class="select">Monthly</span> | 
				<span id="quarterly" class="unselect">Quarterly</span> | 
				<span id="annually" class="unselect">Annually</span>
			</div>
			<div class="column small-1 medium-1 large-1 clearfix right">
				<a class="close-reveal-modal" aria-label="Close">&#215;</a>
			</div>
		</div>

		<div class="row">
			<table id="monthlyGoal">
				<thead>
					<th></th>
					<th>Goal</th>
					<th>Current Progress</th>
					<th>%</th>
					<th>Delta</th>
				</thead>
				<tbody>
					<tr class="enrollments">
						<td >
							Enrollments
						</td>
						<td class="inject-enrolls-goal-monthly"> <!-- Current Goal -->
						</td>
						<td class="inject-enrolls-progress-monthly"> <!-- Current Progress -->
						</td>
						<td class="inject-enrolls-perc-monthly"> <!-- Percentage -->
						</td>
						<td class="inject-enrolls-delta-monthly"> <!-- Delta -->
						</td>
					</tr>
					<tr class="applications">
						<td>
							Applications
						</td>
						<td class="inject-apps-goal-monthly"> <!-- Current Goal -->
						</td>
						<td class="inject-apps-progress-monthly"> <!-- Current Progress -->
						</td>
						<td class="inject-apps-perc-monthly"> <!-- Percentage -->
						</td>
						<td class="inject-apps-delta-monthly"> <!-- Delta -->
						</td>
					</tr>
					<tr class="approved">
						<td>
							Approved
						</td>
						<td class="inject-approved-goal-monthly"> <!-- Current Goal -->
						</td>
						<td class="inject-approved-progress-monthly"> <!-- Current Progress -->
						</td>
						<td class="inject-approved-perc-monthly"> <!-- Percentage -->
						</td>
						<td class="inject-approved-delta-monthly"> <!-- Delta -->
						</td>
					</tr>
				</tbody>
			</table>

			<table id="quarterlyGoal" class="hide">
				<thead>
					<th></th>
					<th>Current Goal</th>
					<th>Current Progress</th>
					<th>%</th>
					<th>Delta</th>
				</thead>
				<tbody>
					<tr class="enrollments">
						<td >
							Enrollments
						</td>
						<td class="inject-enrolls-goal-quarterly"> <!-- Current Goal -->
						</td>
						<td class="inject-enrolls-progress-quarterly"> <!-- Current Progress -->
						</td>
						<td class="inject-enrolls-perc-quarterly"> <!-- Percentage -->
						</td>
						<td class="inject-enrolls-delta-quarterly"> <!-- Delta -->
						</td>
					</tr>
					<tr class="applications">
						<td>
							Applications
						</td>
						<td class="inject-apps-goal-quarterly"> <!-- Current Goal -->
						</td>
						<td class="inject-apps-progress-quarterly"> <!-- Current Progress -->
						</td>
						<td class="inject-apps-perc-quarterly"> <!-- Percentage -->
						</td>
						<td class="inject-apps-delta-quarterly"> <!-- Delta -->
						</td>
					</tr>
					<tr class="approved">
						<td>
							Approved
						</td>
						<td class="inject-approved-goal-quarterly"> <!-- Current Goal -->
						</td>
						<td class="inject-approved-progress-quarterly"> <!-- Current Progress -->
						</td>
						<td class="inject-approved-perc-quarterly"> <!-- Percentage -->
						</td>
						<td class="inject-approved-delta-quarterly"> <!-- Delta -->
						</td>
					</tr>
				</tbody>
			</table>

			<table id="annuallyGoal" class="hide">
				<thead>
					<th></th>
					<th>Current Goal</th>
					<th>Current Progress</th>
					<th>%</th>
					<th>Delta</th>
				</thead>
				<tbody>
					<tr class="enrollments">
						<td >
							Enrollments
						</td>
						<td class="inject-enrolls-goal-annually"> <!-- Current Goal -->
						</td>
						<td class="inject-enrolls-progress-annually"> <!-- Current Progress -->
						</td>
						<td class="inject-enrolls-perc-annually"> <!-- Percentage -->
						</td>
						<td class="inject-enrolls-delta-annually"> <!-- Delta -->
						</td>
					</tr>
					<tr class="applications">
						<td>
							Applications
						</td>
						<td class="inject-apps-goal-annually"> <!-- Current Goal -->
						</td>
						<td class="inject-apps-progress-annually"> <!-- Current Progress -->
						</td>
						<td class="inject-apps-perc-annually"> <!-- Percentage -->
						</td>
						<td class="inject-apps-delta-annually"> <!-- Delta -->
						</td>
					</tr>
					<tr class="approved">
						<td>
							Approved
						</td>
						<td class="inject-approved-goal-annually"> <!-- Current Goal -->
						</td>
						<td class="inject-approved-progress-annually"> <!-- Current Progress -->
						</td>
						<td class="inject-approved-perc-annually"> <!-- Percentage -->
						</td>
						<td class="inject-approved-delta-annually"> <!-- Delta -->
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<!-- trigger modal -->
	<div id="triggerModal" class="reveal-modal" data-school-id="" data-reveal aria-labelledby="" aria-hidden="true" role="dialog">
		<div class="row">
			<div class="column small-6 trigger-title">
				Triggers
			</div>
			<div class="column clearfix small-6">
				<div class="right">
					<a class="close-reveal-modal" aria-label="Close">&#215;</a>
				</div>
			</div>
		</div>

		<div class="row" style="padding-top: 20px;">
			<div class="column small-12 medium-6 email-notify">
				<div class="row">
					Frequency
				</div>
				<div class="row">
					{{Form::radio('trigger', 'daily', false, array('id' => 'f_daily', 'class'=>'daily'))}}
					{{Form::label('f_daily', 'Daily')}}
				</div>
				<div class="row">
					{{Form::radio('trigger', 'weekly', true, array('id' => 'f_weekly', 'class' => 'weekly'))}}
					{{Form::label('f_weekly', 'Weekly')}}
				</div>
				<div class="row">
					Notify Emails
				</div>
				<div class="row">
					<div class="column small-9 email-input"> 
						<input type="email" id="validEmail"></input>
					</div>
					<div class="column small-3">
						<a href="#" class="button tiny addemail" id="addEmail">+</a>
					</div>
					<div class="column small-12 errorMsg hide">
						<div style="font-size: 10px; color: rgb(255, 90, 0);">please input valid email address
						</div>
					</div>
				</div>
				<div class="row" style="padding-top: 0px; font-size: 12px; font-weight: 600;">
					Add a comma to add multiple email address.
				</div>
				<div class="column small-12" id="emailList">
					
				</div>
			</div>
			<div class="column small-12 medium-6 e-trigger-container">
				<div class="row text-center">
					<img alt="!"></img>
					Emergency Trigger
				</div>
				<div id="almArea">
					<div class="notify">
						Notify me immediately if a college goal falls below
					</div>
					<div class="e-input clearfix">
						<div class="left"><input type="number" min="0" max="100" class="threshold"></input></div>
						<div class="left text-center">%</div>
						<small class="err">Numbers 1 - 100 only please.</small>
					</div>
					<div class="text-center">
						<a class="tiny radius button set-trigger-btn">Set Emergency Trigger</a>
					</div>
				</div>
			</div>

		</div>

		<div class="row text-center">
			<div class="save-trigger">Ok</div>
		</div>
	</div>
	<!-- modal table -->
	<div id="engagementDataModal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog">

		<div class="row">
			<div class="column medium-offset-6 large-offset-7 small-10 medium-5 large-4 text-center">
				<span id="default" class="select">Default View</span> | <span id="comparison" class="unselect">Comparison View</span>
			</div>
			<div class="column clearfix small-1">
				<div class="right">
					<a class="close-reveal-modal" aria-label="Close">&#215;</a>
				</div>
			</div>
		</div>

		<div class="row" id="defaultView">
			<table>
				<thead>
					<tr>
						<th rowspan="2">Criteria</th>
						<th colspan="2">Two Weeks </th>
						<th colspan="2">Past Month</th>
						<th colspan="2">Overall</th>
					</tr>
        			<tr>	        
						<th>Score</th>
						<th>Grade</th>
						<th>Score</th>
						<th>Grade</th>
						<th>Score</th>
						<th>Grade</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The last time this college representative logged in. A day is defined as 24 hours.">Last Logged In</span></td>
						<td class="inject-twoWeeksAgo-data-last-login-score"></td>
						<td class="inject-twoWeeksAgo-data-last-login-grade"></td>
						<td class="inject-monthAgo-data-last-login-score"></td>
						<td class="inject-monthAgo-data-last-login-grade"></td>
						<td class="inject-overall-data-last-login-score"></td>
						<td class="inject-overall-data-last-login-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Total number of times this college has viewed students' profiles."># Student Profile Views</span></td>
						<td class="inject-twoWeeksAgo-data-num-profile-score"></td>
						<td class="inject-twoWeeksAgo-data-num-profile-grade"></td>
						<td class="inject-monthAgo-data-num-profile-score"></td>
						<td class="inject-monthAgo-data-num-profile-grade"></td>
						<td class="inject-overall-data-num-profile-score"></td>
						<td class="inject-overall-data-num-profile-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Number of (student-initiated) inquiries this college has received."># Inquiries</span></td>
						<td class="inject-twoWeeksAgo-data-num-inquiries-score"></td>
						<td class="inject-twoWeeksAgo-data-num-inquiries-grade"></td>
						<td class="inject-monthAgo-data-num-inquiries-score"></td>
						<td class="inject-monthAgo-data-num-inquiries-grade"></td>
						<td class="inject-overall-data-num-inquiries-score"></td>
						<td class="inject-overall-data-num-inquiries-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The percentage of inquiries this college has responded to. [(Inquiries Accepted + Inquiries Rejected) / Total Inquiries]. Defaults to 0 if Total Inquiries is 0.">Inquiry Activity</span></td>
						<td class="inject-twoWeeksAgo-data-inquiry-activity-score"></td>
						<td class="inject-twoWeeksAgo-data-inquiry-activity-grade"></td>
						<td class="inject-monthAgo-data-inquiry-activity-score"></td>
						<td class="inject-monthAgo-data-inquiry-activity-grade"></td>
						<td class="inject-overall-data-inquiry-activity-score"></td>
						<td class="inject-overall-data-inquiry-activity-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Number of recommendations this college has manually approved or rejected.">Recommendation Activity</span></td>
						<td class="inject-twoWeeksAgo-data-recommendation-activity-score"></td>
						<td class="inject-twoWeeksAgo-data-recommendation-activity-grade"></td>
						<td class="inject-monthAgo-data-recommendation-activity-score"></td>
						<td class="inject-monthAgo-data-recommendation-activity-grade"></td>
						<td class="inject-overall-data-recommendation-activity-score"></td>
						<td class="inject-overall-data-recommendation-activity-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The number of handshakes/approved students this college has had"># Handshakes</span></td>
						<td class="inject-twoWeeksAgo-data-num-approved-score"></td>
						<td class="inject-twoWeeksAgo-data-num-approved-grade"></td>
						<td class="inject-monthAgo-data-num-approved-score"></td>
						<td class="inject-monthAgo-data-num-approved-grade"></td>
						<td class="inject-overall-data-num-approved-score"></td>
						<td class="inject-overall-data-num-approved-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Number of students this college chose to recruit through advanced search."># Adv. Search Recruited</span></td>
						<td class="inject-twoWeeksAgo-data-num-adv-search-approved-score"></td>
						<td class="inject-twoWeeksAgo-data-num-adv-search-approved-grade"></td>
						<td class="inject-monthAgo-data-num-adv-search-approved-score"></td>
						<td class="inject-monthAgo-data-num-adv-search-approved-grade"></td>
						<td class="inject-overall-data-num-adv-search-approved-score"></td>
						<td class="inject-overall-data-num-adv-search-approved-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The number of days this college has logged in to chat. Logging in is counted when the college sends at least one chat message during a day."># Days Chatted</span></td>
						<td class="inject-twoWeeksAgo-data-num-days-chatted-score"></td>
						<td class="inject-twoWeeksAgo-data-num-days-chatted-grade"></td>
						<td class="inject-monthAgo-data-num-days-chatted-score"></td>
						<td class="inject-monthAgo-data-num-days-chatted-grade"></td>
						<td class="inject-overall-data-num-days-chatted-score"></td>
						<td class="inject-overall-data-num-days-chatted-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Number of chat messages this college representative has sent.">Chat Activity</span></td>
						<td class="inject-twoWeeksAgo-data-total-chat-sent-score"></td>
						<td class="inject-twoWeeksAgo-data-total-chat-sent-grade"></td>
						<td class="inject-monthAgo-data-total-chat-sent-score"></td>
						<td class="inject-monthAgo-data-total-chat-sent-grade"></td>
						<td class="inject-overall-data-total-chat-sent-score"></td>
						<td class="inject-overall-data-total-chat-sent-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The number of offline messages this college representative has sent.">Offline Message Activity</span></td>
						<td class="inject-twoWeeksAgo-data-total-msg-sent-score"></td>
						<td class="inject-twoWeeksAgo-data-total-msg-sent-grade"></td>
						<td class="inject-monthAgo-data-total-msg-sent-score"></td>
						<td class="inject-monthAgo-data-total-msg-sent-grade"></td>
						<td class="inject-overall-data-total-msg-sent-score"></td>
						<td class="inject-overall-data-total-msg-sent-grade"></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Whether the filter for recommendations is active or not.">Filter Active</span></td>
						<td class="inject-twoWeeksAgo-data-filter-active"></td>
						<td></td>
						<td class="inject-monthAgo-data-filter-active"></td>
						<td></td>
						<td class="inject-overall-data-filter-active"></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The Overall Score/Grade using the criteria displayed above.">Overall</td>
						<td class="inject-twoWeeksAgo-data-total-score"></td>
						<td class="inject-twoWeeksAgo-data-total-grade"></td>
						<td class="inject-monthAgo-data-total-score"></td>
						<td class="inject-monthAgo-data-total-grade"></td>
						<td class="inject-overall-data-total-score"></td>
						<td class="inject-overall-data-total-grade"></td>
					</tr>
					<tr>
						<td>Overall Rank</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="inject-overall-data-total-rank"></td>
						<td></td>
					</tr>
					<tr>
						<td>Days Since Joining: </td>
						<td class="inject-daysSinceJoined"></td>
						<td></td>
						<td></td>
						<td colspan="3"><a href="#" data-reveal-id="explanationModal" class="secondary button">Grading Explaination</a></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="row hide" id="comparisonView">
			<table style="width: 90%;">
				<thead>
					<tr>
						<th rowspan="3">Criteria</th>
						<th colspan="4">
							<label for="leftSelectDateRange">Select a date range</label>
							<div class="select-style">
								<select id="leftSelectDateRange" name="leftDateRange" data-school-id="" data-user-id="">
									<option value="0">Select a comparison...</option>
									<option value="1">Yesterday vs. Day Before</option>
									<option value="2">Past Week vs. Week Before</option>
									<option value="3">Past Two Weeks vs. 3-4 Weeks Before</option>
									<option value="4">This Month vs. Last Month</option>
									<option value="5">Customed DateRange</option>
								</select>
							</div>
						</th>
					</tr>
					<tr id="rightSelect" class="hide">
						<th colspan="2">
							<div>
								<div>
									Date Range Option 1: 
									{{ Form::text('date',null, array('id' => 'rightSelectDateOpt1','class'=>'dash-cal','placeholder'=>"&nbsp;&nbsp;Date(s)")) }}
								</div>
								<div class="errorMsg hide" style="color:orange; font-size:10px">Date should be choosen</div>
							</div>
						</th>
						<th colspan="2">
							<div>
								<div>
									Date Range Option 2 : 
									{{ Form::text('date',null, array('id' => 'rightSelectDateOpt2','class'=>'dash-cal','placeholder'=>"&nbsp;&nbsp;Date(s)")) }}
								</div>
								<div class="errorMsg hide" style="color:orange; font-size:10px">Date should be choosen</div>
							</div>
						</th>
					</tr>
					<tr>
						<th>Score</th>
						<th>Grade</th>
						<th>Score</th>
						<th>Grade</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div>
	</div>

	<div id="explanationModal" class="reveal-modal" data-reveal aria-hidden="true" role="dialog"> 
		<div class="row">
			<div class="column small-11 text-center grade-policy">
				Grading: 
				<span id="pastTwoWeeks" class="">Past Two Weeks</span> | 
				<span id="pastMonth" class="select">Past Month</span> | 
				<span id="overAll" class="">Overall and Custom</span>
			</div>
			<div class="column clearfix small-1 text-center">
				<div>
					<a class="close-reveal-modal" aria-label="Close" style="font-size: 20px;">&#215;</a>
				</div>
			</div>
		</div>
		<div class="row">
			<table id="pastTwoWeeksTable" class="hide">
				<thead>
					<tr>
						<th></th>
						<th>A</th>
						<th>B</th>
						<th>C</th>
						<th>D</th>
						<th>F</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class='has-tip tip-bottom row-tip' title='The last time this college representative logged in. A day is defined as 24 hours.'>Last Logged In</span></td>
						<td>Today/Less than 3 days</td>
						<td>Less than a week</td>
						<td>Less than 2 weeks</td>
						<td>Less than a month</td>
						<td>Greater than a month</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Total number of times this college has viewed students' profiles.">Profile Views</span></td>
						<td>21 and greater</td>
						<td>13 to 20</td>
						<td>5 to 12</td>
						<td>1 to 4</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Number of (student-initiated) inquiries this college has received."># Inquiries</span></td>
						<td>6 and greater</td>
						<td>4 to 5</td>
						<td>3 to 4</td>
						<td>1 to 2</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The percentage of inquiries this college has responded to. [(Inquiries Accepted + Inquiries Rejected) / Total Inquiries]. Defaults to 0 if Total Inquiries is 0.">Inquiry Activity</span></td>
						<td>100%</td>
						<td>94 - 99%</td>
						<td>86 - 93%</td>
						<td>35 - 85%</td>
						<td>0 - 35%</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of recommendations this college has manually approved or rejected.'>Recommendation Activity</span></td>
						<td>49 and greater</td>
						<td>43 to 48</td>
						<td>33 to 42</td>
						<td>8 to 32</td>
						<td>0 to 7</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The number of handshakes/approved students this college has had.'># HandShakes</span></td>
						<td>7 or greater</td>
						<td>5 to 6</td>
						<td>3 to 4</td>
						<td>1 to 2</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of students this college chose to recruit through advanced search.'># Adv. Search Recruited</span></td>
						<td>21 and greater</td>
						<td>16 to 20</td>
						<td>11 to 15</td>
						<td>6 to 10</td>
						<td>0 to 5</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of chat messages this college representative has sent.'># Days Chatted</span></td>
						<td>6 and greater</td>
						<td>4 to 5</td>
						<td>2 to 3</td>
						<td>1</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The number of offline messages this college representative has sent'>Chat Activity</span></td>
						<td>31 and greater</td>
						<td>21 to 30</td>
						<td>11 to 20</td>
						<td>1 to 10</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The Overall Score/Grade using the criteria displayed above.'>Offline Message Activity</td>
						<td>41 and greater</td>
						<td>27 to 40</td>
						<td>14 to 26</td>
						<td>1 to 13</td>
						<td>0</td>
					</tr>
				</tbody>
			</table>

			<table id="pastMonthTable" class="">
				<thead>
					<tr>
						<th></th>
						<th>A</th>
						<th>B</th>
						<th>C</th>
						<th>D</th>
						<th>F</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class='has-tip tip-bottom row-tip' title='The last time this college representative logged in. A day is defined as 24 hours.'>Last Logged In</span></td>
						<td>Today/Less than 3 days</td>
						<td>Less than a week</td>
						<td>Less than 2 weeks</td>
						<td>Less than a month</td>
						<td>Greater than a month</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Total number of times this college has viewed students' profiles.">Profile Views</span></td>
						<td>41 and greater</td>
						<td>25 to 40</td>
						<td>10 to 24</td>
						<td>2 to 8</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Number of (student-initiated) inquiries this college has received."># Inquiries</span></td>
						<td>13 and greater</td>
						<td>9 to 12</td>
						<td>5 to 8</td>
						<td>2 to 4</td>
						<td>0 to 1</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The percentage of inquiries this college has responded to. [(Inquiries Accepted + Inquiries Rejected) / Total Inquiries]. Defaults to 0 if Total Inquiries is 0.">Inquiry Activity</span></td>
						<td>100%</td>
						<td>94 - 99%</td>
						<td>86 - 93%</td>
						<td>35 - 85%</td>
						<td>0 - 35%</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of recommendations this college has manually approved or rejected.'>Recommendation Activity</span></td>
						<td>97 and greater</td>
						<td>85 to 96</td>
						<td>65 to 84</td>
						<td>15 to 64</td>
						<td>0 to 14</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The number of handshakes/approved students this college has had.'># HandShakes</span></td>
						<td>14 or greater</td>
						<td>9 to 13</td>
						<td>5 to 8</td>
						<td>2 to 4</td>
						<td>0 to 1</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of students this college chose to recruit through advanced search.'># Adv. Search Recruited</span></td>
						<td>41 and greater</td>
						<td>31 to 40</td>
						<td>21 to 30</td>
						<td>11 to 20</td>
						<td>0 to 10</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of chat messages this college representative has sent.'># Days Chatted</span></td>
						<td>11 and greater</td>
						<td>7 to 10</td>
						<td>4 to 6</td>
						<td>1 to 3</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The number of offline messages this college representative has sent'>Chat Activity</span></td>
						<td>61 and greater</td>
						<td>41 to 60</td>
						<td>21 to 40</td>
						<td>1 to 20</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The Overall Score/Grade using the criteria displayed above.'>Offline Message Activity</td>
						<td>81 and greater</td>
						<td>53 to 80</td>
						<td>27 to 52</td>
						<td>1 to 26</td>
						<td>0</td>
					</tr>
				</tbody>
			</table>

			<table id="overAllTable" class="hide">
				<thead>
					<tr>
						<th></th>
						<th>A</th>
						<th>B</th>
						<th>C</th>
						<th>D</th>
						<th>F</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class='has-tip tip-bottom row-tip' title='The last time this college representative logged in. A day is defined as 24 hours.'>Last Logged In</span></td>
						<td>Today/Less than 3 days</td>
						<td>Less than a week</td>
						<td>Less than 2 weeks</td>
						<td>Less than a month</td>
						<td>Greater than a month</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Total number of times this college has viewed students' profiles.">Profile Views</span></td>
						<td>1.34 or greater per day</td>
						<td>0.81 to 1.33 per day</td>
						<td>0.27 to 0.80 per day</td>
						<td>0.07 to 0.26 per day</td>
						<td>0 to 0.06 per day</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="Number of (student-initiated) inquiries this college has received."># Inquiries</span></td>
						<td>0.51 or greater per day</td>
						<td>0.31 to 0.5 per day</td>
						<td>0.41 to 0.3 per day</td>
						<td>0.04 to 0.13 per day</td>
						<td>0 to 0.03</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title="The percentage of inquiries this college has responded to. [(Inquiries Accepted + Inquiries Rejected) / Total Inquiries]. Defaults to 0 if Total Inquiries is 0.">Inquiry Activity</span></td>
						<td>100%</td>
						<td>94 - 99%</td>
						<td>86 - 93%</td>
						<td>35 - 85%</td>
						<td>0 - 35%</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of recommendations this college has manually approved or rejected.'>Recommendation Activity</span></td>
						<td>3.41 and greater per day</td>
						<td>3.01 to 3.40 per day</td>
						<td>2.34 to 3 per day</td>
						<td>0.51 to 2.33 per day</td>
						<td>0 to 0.5 per day</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The number of handshakes/approved students this college has had.'># HandShakes</span></td>
						<td>0.51 and greater per day</td>
						<td>0.34 to 0.50</td>
						<td>0.21 to 0.33</td>
						<td>0.06 to 0.20</td>
						<td>0 to 0.05</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of students this college chose to recruit through advanced search.'># Adv. Search Recruited</span></td>
						<td>1.44 and greater</td>
						<td>1.08 to 1.43</td>
						<td>0.72 to 1.07</td>
						<td>0.37 to 0.71</td>
						<td>0 to 0.36</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='Number of chat messages this college representative has sent.'># Days Chatted</span></td>
						<td>0.37 and greater</td>
						<td>0.23 to 0.36</td>
						<td>0.08 to 0.21</td>
						<td>0.01 to 0.07</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The number of offline messages this college representative has sent'>Chat Activity</span></td>
						<td>2.15 and greater</td>
						<td>1.44 to 2.14</td>
						<td>0.72 to 1.43</td>
						<td>0.01 to 0.71</td>
						<td>0</td>
					</tr>
					<tr>
						<td><span data-tooltip aria-haspopup="true" class="has-tip tip-bottom row-tip" title='The Overall Score/Grade using the criteria displayed above.'>Offline Message Activity</td>
						<td>2.87 and greater</td>
						<td>1.87 to 2.86</td>
						<td>0.94 to 1.86</td>
						<td>0.01 to 0.93</td>
						<td>0</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<!-- fixed footer 
	<div class="row sales-fixed-footer-row">
		<div class="column small-12">
			Hello, Captain! Welcome to Sales Central Control.
		</div>
	</div>-->

@stop