<?php
	$collegeData = $college_data;
?>

<!--///// social buttons div of holding \\\\\-->
<div id="share_div_of_holding"
	data-share_params='{
		"page_title":"{{ $collegeData->page_title }}",
		"image_prefix":"{{ $collegeData->share_image_path }}",
		"image_name":"{{ $collegeData->share_image }}"
	}'
></div>
<!--\\\\\ social buttons div of holding /////-->

<div class='row' style="border: solid 0px #ff0000;">
    <div class='column small-12'>
		<div style="display:block">
			<div class="tuition-first-content">
		    	<div class="large-12 columns">
					<div class="large-5 columns college-rank-divide coll-tuition-cost-side">
						<div class="row value-first-contentLeftHead">
							<div class='small-12 column'>
								WHAT WILL IT COST?
							</div>
						</div>
						@if(isset($collegeData->custom_tuition))
						@foreach($collegeData->custom_tuition as $k)
						<div class='row'>
							<div class='small-12 column cross-color-platform'>
								{{$k->cct_title or ''}}
							</div>
						</div>
						<div class='row'>
							<div class="small-12 column value-money-first">
								{{$k->cct_currency or ''}}{{ number_format($k->cct_amount)}}
							</div>
						</div>
						@if(isset($k->cct_sub_title))
						<div class='row'>
							<div class='small-12 column'style="font-size: 0.4em;color: #fff;font-style: italic;margin-bottom: 3.2em;
							">
								{{$k->cct_sub_title or ''}}
							</div>
						</div>
						@endif
						@endforeach
						@else
						<div class='row'>
							<div class='small-12 column cross-color-platform'>
								In State Tuition
							</div>
						</div>
						<div class='row'>
							<div class="small-12 column value-money-first">
								@if (isset($collegeData->tuition_avg_in_state_ftug))
		                        	${{ number_format($collegeData->tuition_avg_in_state_ftug )}}
		                    	@else
		                        	N/A
		                        @endif
							</div>
						</div>
						<div class='row'>
							<div class="small-12 column cross-color-platform">
								In State Full Expense <!-- <a href="#" class="instate-details-tuition">&nbsp;&nbsp;(See details)</a> -->
							</div>
						</div>
						<div class='row'>
							<div class="small-12 column value-money-first">
								@if (isset($collegeData->total_inexpenses))
		                        	${{ number_format($collegeData->total_inexpenses )}}
		                    	@else
		                        	N/A
		                        @endif
							</div>
						</div>
						<div class='row'>
							<div class="small-12 column cross-color-platform">
								Out of State Tuition
							</div>
						</div>
						<div class='row'>
							<div class="small-12 column value-money-first">
								@if (isset($collegeData->tuition_avg_out_state_ftug))
		                        	${{ number_format($collegeData->tuition_avg_out_state_ftug )}}
		                    	@else
		                        	N/A
		                        @endif
							</div>
						</div>
						<div class='row'>
							<div class="small-12 column cross-color-platform">
								Out of State Full Expense <!-- <a href="#" class="instate-details-tuition">&nbsp;&nbsp;(See details)</a> -->
							</div>
						</div>
						<div class='row'>
							<div class="small-12 column value-money-first">
								@if (isset($collegeData->total_outexpenses))
		                        	${{ number_format($collegeData->total_outexpenses )}}
		                    	@else
		                        	N/A
		                        @endif
							</div>
						</div>
						@endif
					</div>

					@if( isset($collegeData->youtube_tuition_videos) && count($collegeData->youtube_tuition_videos) > 0 )
					<div class="large-7 column yt-vid-tuition">
                        @foreach( $collegeData->youtube_tuition_videos as $vid )
                        <iframe width="100%" height="280" src="https://www.youtube.com/embed/{{$vid['video_id']}}" style="border:none;" allowfullscreen></iframe>
                        @endforeach
                    </div>
					@else
		            <div class="large-7 columns col-tempImg">
		            	<img src="/images/colleges/stats-top-content.jpg" alt="">
		            </div>
		            @endif
		        </div>
		    </div>
		    <div class="mt10 row" >
				<!-- tuition on-campus -->
				<div class="custom-one-col-box column medium-4 col-tuition-campus-box-left" id="tution-on-campus">
					<div class="tuition-boxes">
					    <div class="tuition-head-img" style="background-image:url(/images/colleges/on-campus-box-img.png);background-size:100%;background-repeat:no-repeat">
					        <div class="impact-title"></div>
					        <div class="tuition-campus-title">ON CAMPUS</div>
					        <div class="title-head-icon"><img src="/images/colleges/on-campus-box-icon.png" alt=""/> </div>
					    </div>
					    <div class="tuition-content">
					        <div class="expenses-header" style="color:#168F3A">IN STATE</div>
					        <div class="large-12 columns tution-inner-content">
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Tuition</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->tuition_avg_in_state_ftug) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->books_supplies_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Room &amp; Board: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->room_board_on_campus_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Other: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->other_expenses_on_campus_1213) }}</div>
					            </div>
					        </div>
					    </div>
					    <div class="tuition-total-expense row" style="color:#168F3A">
					    	<div class="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
					    	<div class="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">${{ number_format($collegeData->total_inexpenses) }}</div>
					    </div>
					    <div class="tuition-content">
					        <div class="expenses-header" style="color:#005977">OUT OF STATE</div>
					        <div class="large-12 columns tution-inner-content">
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Tuition</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->tuition_avg_out_state_ftug) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->books_supplies_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Room &amp; Board: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->room_board_on_campus_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Other: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->other_expenses_on_campus_1213) }}</div>
					            </div>
					        </div>
					    </div>
					    <div class="tuition-total-expense row" style="color:#005977">
					    	<div class="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
					    	<div class="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">${{ number_format($collegeData->total_outexpenses) }}</div>
					    </div>
					</div>
				</div>
				<!-- tuition off-campus -->
		        <div class="custom-one-col-box column medium-4" id="tution-off-campus">
					<div class="tuition-boxes">
					    <div class="tuition-head-img" style="background-image:url(/images/colleges/off-campus-box-img.png);background-size:100%;background-repeat:no-repeat">
					        <div class="impact-title"></div>
					        <div class="tuition-campus-title">OFF CAMPUS</div>
					        <div class="title-head-icon"><img src="/images/colleges/off-campus-box-icon.png" alt=""/> </div>
					    </div>
					    <div class="tuition-content">
					        <div class="expenses-header" style="color:#1DB151">IN STATE</div>
					        <div class="large-12 columns tution-inner-content">
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Tuition</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->tuition_avg_in_state_ftug) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->books_supplies_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Room &amp; Board: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->room_board_off_campus_nofam_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Other: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->other_expenses_off_campus_nofam_1213) }}</div>
					            </div>
					        </div>
					    </div>
					    <div class="tuition-total-expense row" style="color:#1DB151">
					    	<div class="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
					    	<div class="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">${{ number_format($collegeData->total_off_inexpenses) }}</div>
					    </div>
					    <div class="tuition-content">
					        <div class="expenses-header" style="color:#04A5AD">OUT OF STATE</div>
					        <div class="large-12 columns tution-inner-content">
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Tuition</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->tuition_avg_out_state_ftug) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->books_supplies_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Room &amp; Board: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->room_board_off_campus_nofam_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Other: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->other_expenses_off_campus_nofam_1213) }}</div>
					            </div>
					        </div>
					    </div>
					    <div class="tuition-total-expense row" style="color:#04A5AD">
					    	<div class="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
					    	<div class="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">${{ number_format($collegeData->total_off_outexpenses) }}</div>
					    </div>
					</div>
		        </div>
		        <!-- tuition home-campus -->
		        <div class="custom-one-col-box column medium-4 col-tuition-campus-box-right" id="tution-home-campus">
					<div class="tuition-boxes">
					    <div class="tuition-head-img" style="background-image:url(/images/colleges/stay-home-box-img.png);background-size:100%;background-repeat:no-repeat">
					        <div class="impact-title"></div>
					        <div class="tuition-campus-title">STAY HOME</div>
					        <div class="title-head-icon"><img src="/images/colleges/stay-home-box-icon.png" alt=""/> </div>
					    </div>
					    <div class="tuition-content">
					        <div class="expenses-header" style="color:#A0DB39">IN STATE</div>
					        <div class="large-12 columns tution-inner-content">
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Tuition</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->tuition_avg_in_state_ftug) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->books_supplies_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Room &amp; Board: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->room_board_off_campus_nofam_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Other: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->other_expenses_off_campus_yesfam_1213) }}</div>
					            </div>
					        </div>
					    </div>
					    <div class="tuition-total-expense row" style="color:#A0DB39">
					    	<div class="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
					    	<div class="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">${{ number_format($collegeData->total_home_inexpenses) }}</div>
					    </div>
					    <div class="tuition-content">
					        <div class="expenses-header" style="color:#05CED3">OUT OF STATE</div>
					        <div class="large-12 columns tution-inner-content">
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Tuition</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->tuition_avg_out_state_ftug) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->books_supplies_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Room &amp; Board: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->room_board_off_campus_nofam_1213) }}</div>
					            </div>
					            <div class="row">
					                <div class="large-6 small-6 columns no-padding">Other: </div>
					                <div class="large-6 small-6 columns no-padding text-center">${{ number_format($collegeData->other_expenses_off_campus_nofam_1213) }}</div>
					            </div>
					        </div>
					    </div>
					    <div class="tuition-total-expense row" style="color:#05CED3">
					    	<div class="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
					    	<div class="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">${{ number_format($collegeData->total_home_outexpenses) }}</div>
					    </div>
					</div>
		        </div>
		    </div>
		</div>
	</div>
</div>
