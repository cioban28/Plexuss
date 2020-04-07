<?php 
    $collegeData = $college_data;
    //dd($collegeData->youtube_financial_videos[0]['yt_financial_vid']);
?>

<style>
.tution-inner-content {
    box-sizing: border-box;
    padding: 10px 0 20px 10px;
}
</style>

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
        	<div class="financial-panel-content">
            	<div class="large-12 columns no-padding">
                	<div class="large-5 columns college-rank-divide">
                    	<div class="row value-first-contentLeftHead">
        					<div class='column'>
        						FINANCIAL AID
        					</div>
                        </div>
                        <div class="row cross-color-platform bold-font">
        					<div class='small-12 column'>
        						GRANT OR SCHOLARSHIP AID
        					</div>
                        </div>
                        <div class="row">
                        	<div class="large-8 small-7 columns financial-label-tpanel">
                                Students who received aid:
                            </div>
                            <div class="large-4 small-5 columns value-money-financial no-padding">
                                @if (isset($collegeData->undergrad_grant_pct))
                                    {{$collegeData->undergrad_grant_pct}}%
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="row">
                        	<div class="large-8 small-7 columns financial-label-tpanel">
                                Avg. Financial aid given:
                            </div>
                            <div class="large-4 small-5 columns value-money-financial no-padding">
                                @if (isset($collegeData->undergrad_grant_avg_amt))
                                    ${{number_format($collegeData->undergrad_grant_avg_amt)}}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="row cross-color-platform bold-font">
        					<div class='small-12 column'>
        						FEDERAL STUDENT LOANS
        					</div>
                        </div>
                        
                        <div class="row">
                        	<div class="large-8 small-7 columns financial-label-tpanel">
                                Students who received aid:
                            </div>
                            <div class="large-4 small-5 columns value-money-financial no-padding">
                                @if (isset($collegeData->undergrad_loan_pct))
                                    {{$collegeData->undergrad_loan_pct}}%
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="row">
                        	<div class="large-8 small-7 columns financial-label-tpanel">
                                Avg. Financial aid given:
                            </div>
                            <div class="large-4 small-5 columns value-money-financial no-padding">
                                @if (isset($collegeData->undergrad_loan_avg_amt))
                                    ${{ number_format($collegeData->undergrad_loan_avg_amt )}}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        
                        <div class="row cross-color-platform bold-font">
        					<div class='small-12 column'>
        						Out of State Tuition
        					</div>
                        </div>
                        <div class="row">
                        	<div class="large-8 small-7 columns financial-label-tpanel">
                                Avg. Financial aid given:
                            </div>
                            <div class="large-4 small-5 columns value-money-financial no-padding">
                                @if (isset($collegeData->undergrad_aid_avg_amt))
                                    ${{ number_format($collegeData->undergrad_aid_avg_amt + $collegeData->undergrad_loan_avg_amt )}}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>

                    @if( isset($collegeData->youtube_financial_videos) && count($collegeData->youtube_financial_videos) > 0 )
                    <div class="large-7 column yt-vid-financial">
                        @foreach( $collegeData->youtube_financial_videos as $vid )
                        <iframe width="100%" height="280" src="https://www.youtube.com/embed/{{$vid['video_id']}}" style="border:none;" allowfullscreen></iframe>
                        @endforeach
                    </div>
                    @else
                    <div class="large-7 columns no-padding">
                    	<img class="coll-enroll-tempImg" src="/images/colleges/stats-top-content.jpg" alt="">
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="mt10" >
            	<div class="row">
                    <div class="custom-4 mb5">
                        <div id="avg-cost-on-campus">
                            <div class="tuition-boxes">
                                <div class="tuition-head-img" style="background-image:url(/images/colleges/on-campus-box-img.png);background-size:100%;background-repeat:no-repeat">
                                    <div class="impact-title"></div>
                                    <div class="financial-top-title">AVG COST AFTER AID</div>
                                    <div class="financial-campus-title">ON CAMPUS</div>
                                    <div class="title-head-icon"><img src="/images/colleges/on-campus-box-icon.png" alt=""/> </div>
                                </div>
                                <div class="tuition-content">
                                    <div class="expenses-header" style="color:#158E39">IN STATE</div>
                                    <div class="large-12 columns tution-inner-content fs11">
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Total Expense:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->total_inexpenses) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_grant_avg_amt) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_loan_avg_amt) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tuition-total-expense row" style="color:#168F3A">
                                    <div class="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">
                                        Out of pocket:
                                    </div>
                                    <div class="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                        @if( $collegeData->total_incamp_financial < 0 )
                                        -${{ number_format($collegeData->total_incamp_financial * -1) }}
                                        @else
                                        ${{ number_format($collegeData->total_incamp_financial) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="tuition-content">
                                    <div class="expenses-header" style="color:#005977">OUT OF STATE</div>
                                    <div class="large-12 columns tution-inner-content fs11">
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Total Expense:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->total_outexpenses) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_grant_avg_amt) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_loan_avg_amt) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tuition-total-expense row" style="color:#004358;">
                                    <div class="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">Out of pocket:</div> 
                                    <div class="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                        @if( $collegeData->total_outcamp_financial < 0 )
                                        -${{ number_format($collegeData->total_outcamp_financial * -1) }}
                                        @else
                                        ${{ number_format($collegeData->total_outcamp_financial) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custom-4 mb5">
                        <div id="avg-cost-off-campus">                    
                            <div class="tuition-boxes">
                                <div class="tuition-head-img" style="background-image:url(/images/colleges/off-campus-box-img.png);background-size:100%;background-repeat:no-repeat">
                                    <div class="impact-title"></div>
                                    <div class="financial-top-title">AVG COST AFTER AID</div>
                                    <div class="financial-campus-title">OFF CAMPUS</div>
                                    <div class="title-head-icon"><img src="/images/colleges/off-campus-box-icon.png" alt=""/> </div>
                                </div>
                                <div class="tuition-content">
                                    <div class="expenses-header" style="color:#1DB151">IN STATE</div>
                                    <div class="large-12 columns tution-inner-content fs11">
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Total Expense:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->total_off_inexpenses) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_grant_avg_amt) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_loan_avg_amt) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tuition-total-expense row" style="color:#168F3A">
                                    <div class="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">
                                        Out of pocket:
                                    </div> 
                                    <div class="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                        @if( $collegeData->total_off_outcamp_infinancial < 0 )
                                        -${{ number_format($collegeData->total_off_outcamp_infinancial * -1) }}
                                        @else
                                        ${{ number_format($collegeData->total_off_outcamp_infinancial) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="tuition-content">
                                    <div class="expenses-header" style="color:#04A5AD">OUT OF STATE</div>
                                    <div class="large-12 columns tution-inner-content fs11">
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Total Expense:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->total_off_outexpenses) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_grant_avg_amt) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_loan_avg_amt) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tuition-total-expense row" style="color:#004358">
                                    <div class="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">Out of pocket:</div>
                                    <div class="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                        @if( $collegeData->total_off_outcamp_outfinancial < 0 )
                                        -${{ number_format($collegeData->total_off_outcamp_outfinancial * -1) }}
                                        @else
                                        ${{ number_format($collegeData->total_off_outcamp_outfinancial) }}
                                        @endif
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custom-4 mb5">
                        <div id="avg-cost-stay-home">
                            <div class="tuition-boxes">
                                <div class="tuition-head-img" style="background-image:url(/images/colleges/stay-home-box-img.png);background-size:100%;background-repeat:no-repeat">
                                    <div class="impact-title"></div>
                                    <div class="financial-top-title">AVG COST AFTER AID</div>
                                    <div class="financial-campus-title">STAY HOME</div>
                                    <div class="title-head-icon"><img src="/images/colleges/stay-home-box-icon.png" alt=""/> </div>
                                </div>
                                <div class="tuition-content">
                                    <div class="expenses-header" style="color:#A0DB39">IN STATE</div>
                                    <div class="large-12 columns tution-inner-content fs11">
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Total Expense:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->total_home_inexpenses)  }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_grant_avg_amt) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_loan_avg_amt) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tuition-total-expense row" style="color:#168F3A">
                                    <div class="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">
                                        Out of pocket:
                                    </div>
                                    <div class="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                        @if( $collegeData->total_home_infinancial < 0 )
                                        -${{ number_format($collegeData->total_home_infinancial * -1) }}
                                        @else
                                        ${{ number_format($collegeData->total_home_infinancial) }}
                                        @endif
                                    </div> 
                                </div>
                                <div class="tuition-content">
                                    <div class="expenses-header" style="color:#05CED3">OUT OF STATE</div>
                                    <div class="large-12 columns tution-inner-content fs11">
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Total Expense:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->total_home_outexpenses) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_grant_avg_amt) }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                            <div class="large-4 small-6 columns no-padding text-center">${{ number_format($collegeData->undergrad_grant_avg_amt) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tuition-total-expense row" style="color:#004358">
                                    <div class="small-6 medium-6 large-7 column text-left exp-outofpocket-total-label">Out of pocket:</div> 
                                    <div class="small-6 medium-6 large-5 column total exp-outofpocket-total-fontsize">
                                        @if( $collegeData->total_home_outfinancial < 0 )
                                        -${{ number_format($collegeData->total_home_outfinancial * -1) }}
                                        @else
                                        ${{ number_format($collegeData->total_home_outfinancial) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
