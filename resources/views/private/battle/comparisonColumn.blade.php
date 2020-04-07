<?php 
$logoPath = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/";
$defaultLogo = 'images/no_photo.jpg';
?>

@if(!empty($collegeData))

    @foreach($collegeData as $key =>$value)    
    
	<?php	
	$acceptance_rate='N/A'; $total_expense='N/A';$tuition_fees='N/A';$endowment='N/A';
    if(isset($value->id) && $value->id!='')
	{
		$total_expense=number_format(($value->tuition_avg_in_state_ftug)+($value->books_supplies_1213)+($value->room_board_on_campus_1213)+($value->other_expenses_on_campus_1213));    	if($value->applicants_total>0)
		{		
			$acceptance_rate=round(($value->admissions_total)/($value->applicants_total) * 100);			
			if(101 > $acceptance_rate && $acceptance_rate > 0)
			 { $acceptance_rate=number_format($acceptance_rate).'%';}
		}		
		if($value->logo_url!='' && !is_null($value->logo_url))
			{$collegelogo= $logoPath . $value->logo_url.""; }
		else
			{$collegelogo = $defaultLogo;}	
		
		if($value->tuition_avg_in_state_ftug!='' && !is_null($value->tuition_avg_in_state_ftug))
			{$tuition_fees= number_format($value->tuition_avg_in_state_ftug);}

        if($value->tuition_avg_out_state_ftug!='' && !is_null($value->tuition_avg_out_state_ftug))
            {$tuition_out_fees= number_format($value->tuition_avg_out_state_ftug);}
		
		if(isset($value->public_endowment_end_fy_12) && $value->public_endowment_end_fy_12 != 0 ){
			$endowment='$'.round($value->public_endowment_end_fy_12 * 0.000000001, 2).'<span class="fs10"> BILLION</span>';
		}else{
            $endowment='$'.round($value->private_endowment_end_fy_12 * 0.000000001, 2).'<span class="fs10"> BILLION</span>';
        }

        if ($endowment == '$0<span class="fs10"> BILLION</span>' ) {
            $endowment = 'N/A';
        }
		
	}
    ?>

    <div class="item text-center pos-rel " data-slugs='{{$value->slug or ''}}'>
		<!-- // sticky header div-->
        <div class='comapreSchooltitleArea'>
            <!-- School name will go here! -->
                @if(isset($value->id))
                <div class="row">
                    <div class="column small-12 small-text-right removeitem cursor">&#215;</div>
                </div>
                @endif
                {{$value->school_name or ''}}
        </div>
		<!-- // sticky header div-->
        
 	 	<div class="border-right-gray border-bottom-gray" data-fieldfor='college_logo'>  
        	<div class="show-for-small battle-icon">
                <img src="/images/colleges/compare/battle-black.png" title="" alt="" />
            </div>
           
            @if(isset($value->id))
               
                <div id="pos-rel-compare-log-top-containor" class="pos-rel compareLogTopContainor">

                        <div class="removeschool show-for-small">
                            <img  src="/images/colleges/compare/close.jpg" class="text-center removeitem mobile" style="margin:0" alt=""/>
                        </div>
                         
                        <div class="removeschool hide-for-small">
                            <img  id="remove-school-button-cmpr-col-hide-4small" src="/images/colleges/compare/close.jpg" class="text-center removeitem" style="margin-top:-2px;" alt=""/>
                        </div>
                       
                        <a href="/college/{{$value->slug}}">
                            <img src="{{$collegelogo}}" class="compare-school-logo" alt=""/>
                        </a>

                        <br>
                        
                        <div class="row">
                            <div class="small-12 column">
                                <a href="/college/{{$value->slug}}" class="c79">
                                    <div class="college-name">{{$value->school_name or ''}}</div>
                                </a>
                            </div>
                        </div>


                </div>
            @else
                <div id='addSchoolBoxCompareCol' class='addSchoolBox'>
                    <a class="hide-for-small desktopClickToadd" href="#" data-reveal-id="selectSchoolPopup">
                        <img id="clickToAddSchoolImg" src="/images/colleges/compare/addclick.jpg" style="vertical-align:middle;" alt="">
                    </a>

                    <a class='show-for-small' href="#" data-reveal-id="selectSchoolPopup">          
                        <img src="/images/colleges/compare/mobile-add.png" class="compare-school-logo" style="vertical-align:middle;" style="padding:0px;" alt="">
                    </a>
                    <div class="college-name"></div>
                </div>
            @endif
                

            @if(isset($value->id))


                
                @if ($signed_in === 1)
                




                    
                    @if ($value->isInUserList === 1)
                        <div class="row ">
                            <div class="column small-12">
                                <div class="row collapse recruitment-btn-pending">
                                    <!--<div class="columns show-for-large-up large-2">
                                        <img src="/images/colleges/recruitment-btn.png">
                                    </div>-->
                                    <div class="small-12 text-center columns btn-rec-title">Already on my list!</div>
                                </div>
                            </div>
                        </div>

                    @else


                        
                        <div class="row battleRecruitButton">
                            <div class="small-12 column">
                                <div data-reveal-ajax="/ajax/recruiteme/{{$value->id or ''}}" data-reveal-id="recruitmeModal" class="row collapse recruitment-btn">
                                    <div class="small-offset-1 small-9 medium-offset-2 medium-8 large-offset-1 large-9 columns btn-rec-title">Get Recruited!</div>
                                    <div class="small-2 medium-2 large-2 column battleToolTip">
                                        <span data-tooltip aria-haspopup="true" class="has-tip tip-bottom radius" title="By clicking on recruit me, you grant access for this college to communicate with you. Manage all of your communication through your Recruitment Portal"><span class="rm-tooltip-mark"><div class="question-mark">?</div></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    @endif



                @else

                    <div class="row battleRecruitButton">
                        <div class="small-12 column">
                            <a href="/signup?requestType=recruitme&collegeId={{$value->id or ''}}&utm_source=SEO&utm_medium={{$currentPage or ''}}&utm_content={{$value->id or ''}}&utm_campaign=recruitme">
                                <div class="row collapse recruitment-btn">
                                    <div class="small-offset-1 small-9 medium-offset-2 medium-8 large-offset-1 large-9 columns btn-rec-title">Get Recruited!</div>
                                    <div class="small-2 medium-2 large-2 column battleToolTip">
                                        <span data-tooltip aria-haspopup="true" class="has-tip tip-bottom radius" title="By clicking on recruit me, you grant access for this college to communicate with you. Manage all of your communication through your Recruitment Portal">
                                            <span class="rm-tooltip-mark">
                                                <div class="question-mark">?</div>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                @endif

            
            @endif


        </div>
        
        @if(isset($value->id))
            <div class="college-info">
                <div class="odd-div title-text br-white text-center" style="padding-top:15px;">
                    <p class="m-title-heading show-for-small">RANKING</p>
                    <span class="raking-div-number">{{$value->plexuss or 'N/A'}}</span> 
                </div>
                <div class="title-text br-white text-center fs15">
                    <p class="m-title-heading show-for-small">ACCEPTANCE RATE</p>              
                    <span>{{$acceptance_rate or 'N/A'}}</span>
                </div>
                 <div class="odd-div title-text br-white text-center fs15">
                    <p class="m-title-heading show-for-small">TUITION IN-STATE</p>                  
                    <span>${{$tuition_fees or 'N/A'}}</span>
                </div>
                <div class="title-text br-white text-center fs15">
                	<p class="m-title-heading show-for-small">TUITION OUT-STATE</p>                  
                    <span>${{$tuition_out_fees or 'N/A'}}</span>
                </div>
                <div class="odd-div title-text br-white text-center fs15">              
                    <p class="m-title-heading show-for-small">TOTAL EXPENSE <br /> <span class="fs10">(on campus)</span> </p>  
                    <span>${{$total_expense or 'N/A'}}</span>
                </div>
                <div class="title-text br-white text-center fs15">                
                    <p class="m-title-heading show-for-small">STUDENT BODY</p>  
                    <span>{{$value->student_body_total or 'N/A'}}</span>
                </div>
                <div class="odd-div title-text br-white text-center fs15">                
                    <p class="m-title-heading show-for-small">APPLICATION <br /> DEADLINE <br /> <span class="fs10">(undergraduate)</span> </p>  
                    <span>{{$value->deadline or 'N/A'}}</span> 
                </div>
                <div class="title-text br-white text-center fs15">                
                    <p class="m-title-heading show-for-small">APPLICATION <br /> FEE</p>
                    <span>{{$value->application_fee_undergrad or 'N/A'}}</span> 
                </div>
                <div class="odd-div br-white text-center c79 fs12 pt5">              
                    <p class="m-title-heading show-for-small">SECTOR OF <br /> INSTITUTION</p>
                    <span>{{$value->sector_of_institution or 'N/A'}}</span> 
                </div>
                <div class="title-text br-white text-center fs15">                 
                    <p class="m-title-heading show-for-small">CALENDAR <br /> SYSTEM</p>
                    <span>{{$value->calendar_system or 'N/A'}}</span> 
                </div>
                <div class="odd-div title-text br-white text-center fs15">
                    <p class="m-title-heading show-for-small">RELIGIOUS <br /> AFFILIATION</p>
                    <span>{{$value->religous_affiliation or 'N/A'}}</span> 
                </div>
                
                <div class="title-text br-white text-center fs15">
                    <p class="m-title-heading show-for-small">CAMPUS SETTING</p>
                    <span>{{$value->locale_type or 'N/A'}}</span> 
                </div>
                
                  <div class="odd-div title-text br-white text-center fs15">
                    <p class="m-title-heading show-for-small">ENDOWMENT</p>
                    <span>{!! $endowment !!}</span> 
                </div>
            </div>
        @endif
    </div>
    @endforeach   
@endif
