<div class='viewmode' style='display:block;'>

<h2 class="page_head_black"><span class="score-icon"><img src="../images/icon-score.png" alt=""/></span>&nbsp;&nbsp;Scores +5% to your profile status</h2>
<span class="requiredtxt">* Recruiters will NEED this information in order to contact you.</span>
<br /><br />
<div class="show-for-small-only"><br><br><br><br /></div>

<div class="row">
<div class='large-4 column normal_head_black paddingleft0'>High School Scores</div>
<div class='large-4 column paddingleft0'><a id="reveal_hs_scores_btn" class="add-edit-link" data-score-info='{{ @json_encode($hsScore)}}' onclick='addEditHsScore(this);'>Add or edit Scores&nbsp;<span class="edit-icon"><img src="../images/edit_icon.png" alt=""/></span></a></div>
<!--
<div class='large-4 column highschoolWhotxt' style="color:#797979 !important;">Who can see this? <a href="#" style="color:#98D0EE !important;">{{{$hsScore->whocansee or "Only You" }}}</a></div>
-->
</div>	
	<hr>

	<!-- High School area -->
    <div class='row green_head'>High School GPA:</div><br />

	<div class="row label_gray_normal_14">
		<div class="small-7 columns paddingleft0">High School GPA</div>
		<div class="small-5 columns">{{{ $hsScore->hs_gpa or "N/A" }}}</div>
	</div>
	<br/>
	<div class="row label_gray_normal_14">
		<div class="small-7 columns paddingleft0">Weighted GPA</div>
		<div class="small-5 columns">{{{ $hsScore->weighted_gpa or "N/A" }}}</div>
	</div>
	<br/>
	<div class="row label_gray_normal_14">
		<div class="small-7 columns paddingleft0">Maximum possible weighted GPA at your school</div>
		<div class="small-5 columns">{{{ $hsScore->max_weighted_gpa or "N/A" }}}</div>
	</div>
	<br/>

	<div class='row green_head'>College Entrance Exams:</div><br />

	<div class="row label_gray_normal_14">
		<div class="small-7 columns paddingleft0">LSAT Total</div>
		<div class="small-5 columns">{{{ $hsScore->lsat_total or "N/A" }}}</div>
	</div>
	<br/>
	<div class="row label_gray_normal_14">
		<div class="small-7 columns paddingleft0">GMAT Total</div>
		<div class="small-5 columns">{{{ $hsScore->gmat_total or "N/A" }}}</div>
	</div>
	<br/>
	<div class="row label_gray_normal_14">
		<div class="small-7 columns paddingleft0">AP Test</div>
		<div class="small-5 columns">{{{ $hsScore->ap_overall or "N/A" }}}</div>
	</div>
	<br/>
	<div class="row label_gray_normal_14">
		<div class="small-7 columns paddingleft0">PTE Academic</div>
		<div class="small-5 columns">{{{ $hsScore->pte_total or "N/A" }}}</div>
	</div>
	<br/>
	<!-- TOEFL SCORES  -->
    <div class='row title'>TOEFL (Test of English as a Foreign Language)</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0 end">Overall:&nbsp;&nbsp;{{{ $hsScore->toefl_total or "N/A" }}}</div>
        
	</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0">Reading:&nbsp;&nbsp;{{{ $hsScore->toefl_reading or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Listening:&nbsp;&nbsp;{{{ $hsScore->toefl_listening or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Speaking:&nbsp;&nbsp;{{{ $hsScore->toefl_speaking or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Writing:&nbsp;&nbsp;{{{ $hsScore->toefl_writing or "N/A" }}}</div>
	</div><br />

	<!-- TOEFL IETLS SCORES  -->
    <div class='row title'>TOEFL iBT</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0 end">Overall:&nbsp;&nbsp;{{{ $hsScore->toefl_ibt_total or "N/A" }}}</div>
        
	</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0">Reading:&nbsp;&nbsp;{{{ $hsScore->toefl_ibt_reading or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Listening:&nbsp;&nbsp;{{{ $hsScore->toefl_ibt_listening or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Speaking:&nbsp;&nbsp;{{{ $hsScore->toefl_ibt_speaking or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Writing:&nbsp;&nbsp;{{{ $hsScore->toefl_ibt_writing or "N/A" }}}</div>
	</div><br />

	<!-- TOEFL PBT SCORES  -->
    <div class='row title'>TOEFL PBT</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0 end">Overall:&nbsp;&nbsp;{{{ $hsScore->toefl_pbt_total or "N/A" }}}</div>
        
	</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0">Reading:&nbsp;&nbsp;{{{ $hsScore->toefl_pbt_reading or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Listening:&nbsp;&nbsp;{{{ $hsScore->toefl_pbt_listening or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Structure/Written Expression:&nbsp;&nbsp;{{{ $hsScore->toefl_pbt_written or "N/A" }}}</div>
	</div><br />

	<!-- IELTS SCORES  -->
    <div class='row title'>IELTS (International English Language Testing System)</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0 end">Overall:&nbsp;&nbsp;{{{ $hsScore->ielts_total or "N/A" }}}</div>
        
	</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0">Reading:&nbsp;&nbsp;{{{ $hsScore->ielts_reading or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Listening:&nbsp;&nbsp;{{{ $hsScore->ielts_listening or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Speaking:&nbsp;&nbsp;{{{ $hsScore->ielts_speaking or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Writing:&nbsp;&nbsp;{{{ $hsScore->ielts_writing or "N/A" }}}</div>
	</div><br />
    
    <!-- ACT SCORES  -->
    <div class='row title'>ACT Scores</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0">English:&nbsp;&nbsp;{{{ $hsScore->act_english or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Math:&nbsp;&nbsp;{{{ $hsScore->act_math or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Composite:&nbsp;&nbsp;{{{ $hsScore->act_composite or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">&nbsp;</div>
	</div><br />

	<!-- GRE SCORES  -->
    <div class='row title'>GRE Scores</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0">Verbal:&nbsp;&nbsp;{{{ $hsScore->gre_verbal or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Quantitative:&nbsp;&nbsp;{{{ $hsScore->gre_quantitative or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Analytical:&nbsp;&nbsp;{{{ $hsScore->gre_analytical or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">&nbsp;</div>
	</div><br />

	<!-- PSAT SCORES  -->
    <div class='row title'>PSAT Scores</div><br />
	<div class="row label_gray_normal_14" >
		<div class="large-3 columns paddingleft0">Critical Reading:&nbsp;&nbsp;{{{ $hsScore->psat_reading or "N/A" }}}</div>
		<div class="large-3 columns paddingleft0">Reading/Writing:&nbsp;&nbsp;{{{ $hsScore->psat_reading_writing or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Math:&nbsp;&nbsp;{{{ $hsScore->psat_math or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Writing:&nbsp;&nbsp;{{{ $hsScore->psat_writing or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Total:&nbsp;&nbsp;{{{ $hsScore->psat_total or "N/A" }}}</div>
	</div><br />

	<!-- SAT SCORES  -->
    <div class='row title'>SAT Scores</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0">Critical Reading:&nbsp;&nbsp;{{{ $hsScore->sat_reading or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Math:&nbsp;&nbsp;{{{ $hsScore->sat_math or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Reading/Writing:&nbsp;&nbsp;{{{ $hsScore->sat_reading_writing or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Writing:&nbsp;&nbsp;{{{ $hsScore->sat_writing or "N/A" }}}</div>
        <div class="large-3 columns paddingleft0">Total:&nbsp;&nbsp;{{{ $hsScore->sat_total or "N/A" }}}</div>
	</div><br />
    
    <div class='row title'>GED Scores</div><br />
	<div class="row label_gray_normal_14">
		<div class="large-3 columns paddingleft0">GED Pass/Fail:&nbsp;&nbsp;{{ $hsScore->gedfp or "--"}}</div>
        <div class="large-3 columns paddingleft0">GED Score:&nbsp;&nbsp;{{{ $hsScore->ged_score or "N/A" }}}</div>
        <div class="large-6 columns paddingleft0">&nbsp;</div>
	</div><br />
    
    <div class='row title'>Other</div><br />   
        @if( isset( $hsScore->other_values ) )
        	@if(is_array($hsScore->other_values))
	            @foreach( $hsScore->other_values as $key => $scoreValue)
		            <div class="row label_gray_normal_14">
		                <div class="large-5 columns paddingleft0">{{$scoreValue->class or 'N/A'}}:&nbsp;&nbsp;{{ $scoreValue->score or 'N/A'}}</div>
		                <div class="large-7 columns paddingleft0">&nbsp;</div>
		            </div>
	            @endforeach
	        @else
	        	<div class="row label_gray_normal_14">
	                <div class="large-5 columns paddingleft0">{{$hsScore->other_exam or 'N/A'}}:&nbsp;&nbsp;{{ $hsScore->other_values or 'N/A'}}</div>
	                <div class="large-7 columns paddingleft0">&nbsp;</div>
	            </div>
	        @endif
	    @else
    		<span class="label_gray_normal_14">There are no other score updated.</span>
    	@endif
    <br/>
	<br/>
	<br/>
	<br/>
	<!-- College area -->   
    <div class="row">
<div class='large-3 column normal_head_black paddingleft0'>College Scores</div>
<div class='large-4 column'><a id="reveal_col_scores_btn" class="add-edit-link"  onclick='AddEditCollegeScoreForm();'>Add or edit Scores&nbsp;<span class="edit-icon"><img src="../images/edit_icon.png" alt=""/></span></a></div>
<!--
<div class='large-4 column highschoolWhotxt' style="color:#797979 !important;">Who can see this? <a href="#" style="color:#98D0EE !important;">Only you</a></div>
-->

</div>	
	<hr>    

	<div class='row green_head'>College GPA:</div><br />
	<div class="row label_gray_normal_14">
		<div class="small-3 columns paddingleft0">Overall GPA</div>
		<div class="small-9 columns">{{{ $hsScore->overall_gpa or "N/A" }}}</div>
	</div><br />
    
    <!--<div class="row label_gray_normal_14">
		<div class="small-3 columns paddingleft0">Major GPA</div>
		<div class="small-3 columns">score</div>
        <div class="small-6 columns">Physics</div>
	</div><br />
    
    <div class="row label_gray_normal_14">
		<div class="small-3 columns paddingleft0">Minor GPA</div>
		<div class="small-3 columns">3.0</div>
        <div class="small-6 columns">Computer Science</div>
	</div><br />-->
	
    <!--<div class='row green_head'>Graduate Exams:</div><br />
	<div class="row label_gray_normal_14">
		<div class="small-3 columns paddingleft0">GRE</div>
		<div class="small-9 columns">400</div>
	</div><br />-->
</div>







<div class='reveal-modal medium remove_before_ajax' id="addEditHsScore" data-reveal>
	{{ Form::open(array('url' => "ajax/profile/scores/" , 'method' => 'POST', 'id' => 'ScoreInfoForm','data-abide'=>'ajax')) }}
	{{ csrf_field() }}
	{{ Form::hidden('postType','highschool',array()) }}
	<div class="row">
		<div class="large-4 columns page_head_black paddingleft0">
			<span class="score-icon"><img src="../images/icon-score.png" alt=""/></span>&nbsp;Scores
		</div>
		<div class="large-8 columns">
			<span class="requiredtxt">* Recruiters will NEED this information in order to contact you.</span>
		</div>
	</div>
	<br />
	<br />

	<div class="row">
		<div class='large-4 column normal_head_black paddingleft0'>High School Scores</div>
		<div class='large-2 column paddingleft0'>&nbsp;</div>
		<!--
		<div class='large-3 column highschoolWhotxt' style="color:#797979 !important;">Who can see this?</div>
		<div class="large-3 column">@{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),$hsScore->ged_score,array() ) }}</div>
		-->
	</div>	
	
	<div class="row">
		<div class="column small-12">
			<hr>
		</div>
	</div>

	<!-- High School area -->
    <div class='row green_head'>
    	<div class="column small-12 medium-6">
    		High School GPA:
    	</div>
		<div class="column small-12 medium-6">
			<!-- grade calculator -->
			<div class="row collapse convert-international-grades-link">
				<div class="column small-12">
					<a href="http://www.foreigncredits.com/Resources/GPA-Calculator/" target="_blank">How do I convert my international GPA?</a>
				</div>
			</div>
		</div>
    </div>
    <br/>

	<div class="row">
		<div class="small-12 large-7 columns">
			{{ Form::label('hs_gpa', 'High School GPA', array('class' => 'score_label')) }}
		</div>
		<div class="small-12 large-5 columns">
			{{ Form::text('hs_gpa', isset($hsScore->hs_gpa) ? $hsScore->hs_gpa : null , array( 'placeholder' =>'0.01-4', 'id' => 'hs_gpa', 'class'=>'small_input_text','pattern'=>'gpa', 'maxlength' => '4' ))}}
			<small class="error">Range: 0.01-4.00</small>
		</div>
	</div>
	<br />
	<div class="row">
		<div class="small-12 large-7 columns">
			{{ Form::label('weighted_gpa', 'Weighted GPA', array('class' => 'score_label')) }}
		</div>
		<div class="small-12 large-5 columns">
			{{ Form::text('weighted_gpa',isset($hsScore->weighted_gpa) ? $hsScore->weighted_gpa : null , array( 'placeholder' =>'score', 'id' => 'weighted_gpa', 'class'=>'small_input_text','data-abide-validator'=>'weighted_gpa', 'maxlength' => '8' ))}}
			<small class="error">Not a valid score</small>
		</div>
	</div> 
	<br/>
	<div class="row">
		<div class="small-12 large-7 columns">
			{{ Form::label('max_weighted_gpa', 'Maximum possible weighted GPA at your school', array('class' => 'score_label scores_maxgpa_mobile')) }}
		</div>
		<div class="small-12 large-5 columns">
			{{ Form::text('max_weighted_gpa',isset($hsScore->max_weighted_gpa) ? $hsScore->max_weighted_gpa : null , array( 'placeholder' =>'score', 'id' => 'max_weighted_gpa', 'class'=>'small_input_text','pattern'=>'max_weighted_gpa', 'maxlength' => '8' ))}}
			<small class="error">Not a valid score</small>
		</div>
	</div> 
	<br/>

	<div class='row green_head'>
		<div class="column small-12">
			College Entrance Exams:
		</div>
	</div>
	<br/>

	<div class="row">
		<div class="small-12 large-7 columns">
			{{ Form::label('lsat', 'LSAT Total', array('class' => 'score_label')) }}
		</div>
		<div class="small-12 large-5 columns">
			{{ Form::text('lsat_total',isset($hsScore->lsat_total) ? $hsScore->lsat_total : null , array( 'placeholder' =>'120-180', 'id' => 'lsat_id', 'class'=>'small_input_text','pattern'=>'lsat', 'maxlength' => '3' ))}}
			<small class="error">Range: 120-180</small>
		</div>
	</div> 
	<br/>
	<div class="row">
		<div class="small-12 large-7 columns">
			{{ Form::label('gmat', 'GMAT Total', array('class' => 'score_label')) }}
		</div>
		<div class="small-12 large-5 columns">
			{{ Form::text('gmat_total',isset($hsScore->gmat_total) ? $hsScore->gmat_total : null , array( 'placeholder' =>'200-800', 'id' => 'gre_id', 'class'=>'small_input_text','pattern'=>'gmat', 'maxlength' => '3' ))}}
			<small class="error">Range: 200-800</small>
		</div>
	</div> 
	<br/>
	<div class="row">
		<div class="small-12 large-7 columns">
			{{ Form::label('ap', 'AP Test', array('class' => 'score_label')) }}
		</div>
		<div class="small-12 large-5 columns">
			{{ Form::text('ap_overall',isset($hsScore->ap_overall) ? $hsScore->ap_overall : null , array( 'placeholder' =>'1-5', 'id' => 'ap_id', 'class'=>'small_input_text','pattern'=>'ap', 'maxlength' => '1' ))}}
			<small class="error">Range: 1-5</small>
		</div>
	</div> 
	<br/>
	<div class="row">
		<div class="small-12 large-7 columns">
			{{ Form::label('pte', 'PTE Academic', array('class' => 'score_label')) }}
		</div>
		<div class="small-12 large-5 columns">
			{{ Form::text('pte_total',isset($hsScore->pte_total) ? intval($hsScore->pte_total) : null , array( 'placeholder' =>'10-90', 'id' => 'pte_id', 'class'=>'small_input_text','pattern'=>'pte', 'maxlength' => '2' ))}}
			<small class="error">Range: 10-90</small>
		</div>
	</div> 
	<br/>

    <!-- TOEFL SCORES  -->
    <div class='row title'>
    	<div class="column small-12">
    		TOEFL (Test of English as a Foreign Language)
    	</div>
    </div>
    <br/>
    <div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-3 columns end">
        	<div>Overall:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'toefl_total', isset($hsScore->toefl_total) ? $hsScore->toefl_total : null, array(  'placeholder' =>'0-90','id' => 'toefl_total','class'=>'small_input_text','pattern' => 'toefl_overall','maxlength' => '2') ) }}
				<small class="error">Range: 0-90</small>
			</div>
		</div>
	</div><br/>
	<div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-3 columns">
        	<div>Reading:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'toefl_reading', isset($hsScore->toefl_reading) ? $hsScore->toefl_reading : null, array(  'placeholder' =>'0-30','id' => 'toefl_reading','class'=>'small_input_text','pattern' => 'toefl','maxlength' => '2') ) }}
				<small class="error">Range: 0-30</small>
			</div>
		</div>
        <div class="medium-3 columns">
        	<div>Listening:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('toefl_listening', isset($hsScore->toefl_listening) ? $hsScore->toefl_listening : null, array( 'placeholder' =>'0-30','id' => 'toefl_listening','class'=>'small_input_text','pattern' => 'toefl','maxlength' => '2' ))}}
				<small class="error">Range: 0-30</small>
			</div>
        </div>
        <div class="medium-3 columns">
        	<div>Speaking:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('toefl_speaking', isset($hsScore->toefl_speaking) ? $hsScore->toefl_speaking : null, array( 'placeholder' =>'0-30','id' => 'toefl_speaking','class'=>'small_input_text','pattern' => 'toefl','maxlength' => '2' ))}}
				<small class="error">Range: 0-30</small>
			</div>
        </div>
        <div class="medium-3 columns">
        	<div>Writing:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('toefl_writing', isset($hsScore->toefl_writing) ? $hsScore->toefl_writing : null, array( 'placeholder' =>'0-30','id' => 'toefl_writing','class'=>'small_input_text','pattern' => 'toefl','maxlength' => '2' ))}}
				<small class="error">Range: 0-30</small>
			</div>
        </div>
	</div>
	<br/>

	<!-- TOEFL iBT SCORES  -->
    <div class='row title'>
    	<div class="column small-12">
    		TOEFL iBT
    	</div>
    </div>
    <br/>
    <div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-3 columns end">
        	<div>Overall:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'toefl_ibt_total', isset($hsScore->toefl_ibt_total) ? $hsScore->toefl_ibt_total : null, array(  'placeholder' =>'0-120','id' => 'toefl_ibt_total','class'=>'small_input_text','pattern' => 'toefl_ibt_overall','maxlength' => '3') ) }}
				<small class="error">Range: 0-120</small>
			</div>
		</div>
	</div><br/>
	<div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-3 columns">
        	<div>Reading:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'toefl_ibt_reading', isset($hsScore->toefl_ibt_reading) ? $hsScore->toefl_ibt_reading : null, array(  'placeholder' =>'0-30','id' => 'toefl_ibt_reading','class'=>'small_input_text','pattern' => 'toefl','maxlength' => '2') ) }}
				<small class="error">Range: 0-30</small>
			</div>
		</div>
        <div class="medium-3 columns">
        	<div>Listening:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('toefl_ibt_listening', isset($hsScore->toefl_ibt_listening) ? $hsScore->toefl_ibt_listening : null, array( 'placeholder' =>'0-30','id' => 'toefl_ibt_listening','class'=>'small_input_text','pattern' => 'toefl','maxlength' => '2' ))}}
				<small class="error">Range: 0-30</small>
			</div>
        </div>
        <div class="medium-3 columns">
        	<div>Speaking:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('toefl_ibt_speaking', isset($hsScore->toefl_ibt_speaking) ? $hsScore->toefl_ibt_speaking : null, array( 'placeholder' =>'0-30','id' => 'toefl_ibt_speaking','class'=>'small_input_text','pattern' => 'toefl','maxlength' => '2' ))}}
				<small class="error">Range: 0-30</small>
			</div>
        </div>
        <div class="medium-3 columns">
        	<div>Writing:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('toefl_ibt_writing', isset($hsScore->toefl_ibt_writing) ? $hsScore->toefl_ibt_writing : null, array( 'placeholder' =>'0-30','id' => 'toefl_ibt_writing','class'=>'small_input_text','pattern' => 'toefl','maxlength' => '2' ))}}
				<small class="error">Range: 0-30</small>
			</div>
        </div>
	</div>
	<br/>

	<!-- TOEFL PBT SCORES  -->
    <div class='row title'>
    	<div class="column small-12">
    		TOEFL PBT
    	</div>
    </div>
    <br/>
    <div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-3 columns end">
        	<div>Overall:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'toefl_pbt_total', isset($hsScore->toefl_pbt_total) ? $hsScore->toefl_pbt_total : null, array(  'placeholder' =>'310-677','id' => 'toefl_pbt_total','class'=>'small_input_text','pattern' => 'toefl_pbt_overall','maxlength' => '3') ) }}
				<small class="error">Range: 310-677</small>
			</div>
		</div>
	</div><br/>
	<div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-3 columns">
        	<div>Reading:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'toefl_pbt_reading', isset($hsScore->toefl_pbt_reading) ? $hsScore->toefl_pbt_reading : null, array(  'placeholder' =>'31-67','id' => 'toefl_pbt_reading','class'=>'small_input_text','pattern' => 'toefl_pbt_reading','maxlength' => '2') ) }}
				<small class="error">Range: 31-67</small>
			</div>
		</div>
        <div class="medium-3 columns">
        	<div>Listening:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('toefl_pbt_listening', isset($hsScore->toefl_pbt_listening) ? $hsScore->toefl_pbt_listening : null, array( 'placeholder' =>'31-68','id' => 'toefl_pbt_listening','class'=>'small_input_text','pattern' => 'toefl_pbt_listening','maxlength' => '2' ))}}
				<small class="error">Range: 31-68</small>
			</div>
        </div>
        <div class="medium-3 columns">
        	<div>Speaking:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('toefl_pbt_written', isset($hsScore->toefl_pbt_written) ? $hsScore->toefl_pbt_written : null, array( 'placeholder' =>'31-68','id' => 'toefl_pbt_written','class'=>'small_input_text','pattern' => 'toefl_pbt_listening','maxlength' => '2' ))}}
				<small class="error">Range: 31-68</small>
			</div>
        </div>
	</div>
	<br/>

	<!-- IELTS SCORES  -->
    <div class='row title'>
    	<div class="column small-12">
    		IELTS (International English Language Testing System)
    	</div>
    </div>
    <br/>
    <div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-3 columns end">
        	<div>Overall:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'ielts_total', isset($hsScore->ielts_total) ? $hsScore->ielts_total : null, array(  'placeholder' =>'0-9','id' => 'ielts_total','class'=>'small_input_text','pattern' => 'ielts','maxlength' => '3') ) }}
				<small class="error">Range: 0-9</small>
			</div>
		</div>
	</div><br/>
	<div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-3 columns">
        	<div>Reading:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'ielts_reading', isset($hsScore->ielts_reading) ? $hsScore->ielts_reading : null, array(  'placeholder' =>'0-9','id' => 'ielts_reading','class'=>'small_input_text','pattern' => 'ielts','maxlength' => '3') ) }}
				<small class="error">Range: 0-9</small>
			</div>
		</div>
        <div class="medium-3 columns">
        	<div>Listening:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('ielts_listening', isset($hsScore->ielts_listening) ? $hsScore->ielts_listening : null, array( 'placeholder' =>'0-9','id' => 'ielts_listening','class'=>'small_input_text','pattern' => 'ielts','maxlength' => '3' ))}}
				<small class="error">Range: 0-9</small>
			</div>
        </div>
        <div class="medium-3 columns">
        	<div>Speaking:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('ielts_speaking', isset($hsScore->ielts_speaking) ? $hsScore->ielts_speaking : null, array( 'placeholder' =>'0-9','id' => 'ielts_speaking','class'=>'small_input_text','pattern' => 'ielts','maxlength' => '3' ))}}
				<small class="error">Range: 0-9</small>
			</div>
        </div>
        <div class="medium-3 columns">
        	<div>Writing:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('ielts_writing', isset($hsScore->ielts_writing) ? $hsScore->ielts_writing : null, array( 'placeholder' =>'0-9','id' => 'ielts_writing','class'=>'small_input_text','pattern' => 'ielts','maxlength' => '3' ))}}
				<small class="error">Range: 0-9</small>
			</div>
        </div>
	</div>
	<br/>

    <!-- ACT SCORES  -->
    <div class='row title'>
    	<div class="column small-12">
    		ACT Scores
    	</div>
    </div>
    <br/>
	<div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-4 columns">
        	<div>English:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'act_english', isset($hsScore->act_english) ? $hsScore->act_english : null, array(  'placeholder' =>'1-36','id' => 'act_english','class'=>'small_input_text','pattern' => 'act','maxlength' => '2') ) }}
				<small class="error">Range: 1-36</small>
			</div>
		</div>
        <div class="medium-4 columns">
        	<div>Math:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('act_math', isset($hsScore->act_math) ? $hsScore->act_math : null, array( 'placeholder' =>'1-36','id' => 'act_math','class'=>'small_input_text','pattern' => 'act','maxlength' => '2' ))}}
				<small class="error">Range: 1-36</small>
			</div>
        </div>
        <div class="medium-4 columns">
        	<div>Composite:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('act_composite', isset($hsScore->act_composite) ? $hsScore->act_composite : null, array( 'placeholder' =>'1-36','id' => 'act_composite','class'=>'small_input_text','pattern' => 'act','maxlength' => '2' ))}}
				<small class="error">Range: 1-36</small>
			</div>
        </div>
        <div class="large-3 columns paddingleft0">&nbsp;</div>
	</div>
	<br/>

	<!-- PSAT SCORES  -->
    <div class='row title'>
    	<div class="column small-12">
    		PSAT Scores
    	</div>
    	<div class='toggle-checkbox'>
    		<input class='toggle-pre-exams' id='is_pre_2016_psat_id' name='is_pre_2016_psat' type='checkbox' data-type='psat' value='1' {{ isset($hsScore->is_pre_2016_psat) && $hsScore->is_pre_2016_psat ? 'checked' : null }}>
    		<label for='is_pre_2016_psat_id'>I took the PSAT before 2016</label>
    	</div> 
    </div>
    <br/>
	<div class="edit-scores-psat row label_gray_normal_14" style="line-height:30px;">
		<div class='toggled-exam pre-exam {{ isset($hsScore->is_pre_2016_psat) && $hsScore->is_pre_2016_psat ? '' : 'hide' }}'>
			<div class="large-3 medium-6 small-12 columns">
	        	<div>Critical Reading:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
					{{ Form::text('psat_reading', isset($hsScore->psat_reading) ? $hsScore->psat_reading : null ,array( 'placeholder' =>'20-80','id' => 'psat_reading','class'=>'small_input_text','data-min'=>'200','data-max'=>'800','pattern' => 'psat','maxlength' => '2' ))}}
					<small class="error">Range: 20-80</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Math:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				@if (isset($hsScore->is_pre_2016_psat) && $hsScore->is_pre_2016_psat)
					{{ Form::text('psat_math', isset($hsScore->psat_math) ? $hsScore->psat_math : null ,array( 'placeholder' =>'20-80','id' => 'psat_math','class'=>'small_input_text','data-min'=>'200','data-max'=>'800','pattern' => 'psat','maxlength' => '2' ))}}
				@else
					{{ Form::text('psat_math', isset($hsScore->psat_math) ? $hsScore->psat_math : null ,array( 'placeholder' =>'20-80','id' => 'psat_math','class'=>'small_input_text','data-min'=>'200','data-max'=>'800','pattern' => 'psat','maxlength' => '2', 'disabled' => 'disabled' ))}}
				@endif
					<small class="error">Range: 20-80</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Writing:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
					{{ Form::text('psat_writing',isset($hsScore->psat_writing) ? $hsScore->psat_writing : null ,array( 'placeholder' =>'20-80','id' => 'psat_writing','class'=>'small_input_text','data-min'=>'200','data-max'=>'800','pattern' => 'psat','maxlength' => '2' ))}}
					<small class="error">Range: 20-80</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Total:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				@if (isset($hsScore->is_pre_2016_psat) && $hsScore->is_pre_2016_psat)
					{{  Form::text( 'psat_total', isset($hsScore->psat_total) ? $hsScore->psat_total : null , array(  'placeholder' =>'60-240', 'id' => 'psat_total', 'class'=>'small_input_text', 'data-min'=>'600', 'data-max'=>'2400', 'pattern' => 'psat_total', 'maxlength' => '3' ) )}}
				@else
					{{  Form::text( 'psat_total', isset($hsScore->psat_total) ? $hsScore->psat_total : null , array(  'placeholder' =>'60-240', 'id' => 'psat_total', 'class'=>'small_input_text', 'data-min'=>'600', 'data-max'=>'2400', 'pattern' => 'psat_total', 'maxlength' => '3', 'disabled' => 'disabled' ) )}}
				@endif
					<small class="error">Range: 60-240</small>
				</div>
	        </div>
	    </div>
	    <div class="toggled-exam post-exam {{ isset($hsScore->is_pre_2016_psat) && $hsScore->is_pre_2016_psat ? 'hide' : '' }}">
	    	<div class="large-3 medium-6 small-12 columns">
	        	<div>Math:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				@if (isset($hsScore->is_pre_2016_psat) && $hsScore->is_pre_2016_psat)
					{{ Form::text('psat_math', isset($hsScore->psat_math) ? $hsScore->psat_math : null ,array( 'placeholder' =>'160-760','id' => 'post_psat_math','class'=>'small_input_text','pattern' => 'post_psat','maxlength' => '3', 'disabled' => 'disabled' ))}}
				@else
					{{ Form::text('psat_math', isset($hsScore->psat_math) ? $hsScore->psat_math : null ,array( 'placeholder' =>'160-760','id' => 'post_psat_math','class'=>'small_input_text','pattern' => 'post_psat','maxlength' => '3' ))}}
				@endif
					<small class="error">Range: 160-760</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Reading/Writing:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
					{{ Form::text('psat_reading_writing', isset($hsScore->psat_reading_writing) ? $hsScore->psat_reading_writing : null ,array( 'placeholder' =>'160-760','id' => 'post_psat_reading_writing','class'=>'small_input_text','pattern' => 'post_psat','maxlength' => '3' ))}}
					<small class="error">Range: 160-760</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Overall:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				@if (isset($hsScore->is_pre_2016_psat) && $hsScore->is_pre_2016_psat)
					{{ Form::text('psat_total', isset($hsScore->psat_total) ? $hsScore->psat_total : null ,array( 'placeholder' =>'320-1520','id' => 'post_psat_total','class'=>'small_input_text','pattern' => 'post_psat_total','maxlength' => '4', 'disabled' => 'disabled' ))}}
				@else
					{{ Form::text('psat_total', isset($hsScore->psat_total) ? $hsScore->psat_total : null ,array( 'placeholder' =>'320-1520','id' => 'post_psat_total','class'=>'small_input_text','pattern' => 'post_psat_total','maxlength' => '4' ))}}
				@endif
					<small class="error">Range: 320-1520</small>
				</div>
	        </div>
	    </div>
	</div>
	<br/>

	<!-- SAT SCORES  -->
    <div class='row title'>
    	<div class="column small-12">
    		SAT Scores
    	</div>
    	<div class='toggle-checkbox'>
    		<input class='toggle-pre-exams' id='is_pre_2016_sat_id' name='is_pre_2016_sat' type='checkbox' data-type='sat' value='1' {{ isset($hsScore->is_pre_2016_sat) && $hsScore->is_pre_2016_sat ? 'checked' : null }}>  
    		<label for='is_pre_2016_sat_id'>I took the SAT before 2016</label>
    	</div>
    </div>
    <br/>
	<div class="edit-scores-sat row label_gray_normal_14" style="line-height:30px;">
		<div class='toggled-exam pre-exam {{ isset($hsScore->is_pre_2016_sat) && $hsScore->is_pre_2016_sat ? '' : 'hide' }}'>
			<div class="large-3 medium-6 small-12 columns">
				<div>Critical Reading:&nbsp;&nbsp;</div>
				<div>
					{{ Form::text( 'sat_reading', isset($hsScore->sat_reading) ? $hsScore->sat_reading : null , array(  'placeholder' =>'200-800', 'id' => 'sat_reading', 'class'=>'small_input_text', 'data-min'=>'200', 'data-max'=>'800', 'pattern' => 'sat', 'maxlength' => '3' ) ) }}
					<small class="error">Range: 200-800</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Math:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				@if (isset($hsScore->is_pre_2016_sat) && $hsScore->is_pre_2016_sat)
					{{ Form::text( 'sat_math', isset($hsScore->sat_math) ? $hsScore->sat_math : null , array(  'placeholder' =>'200-800', 'id' => 'sat_math', 'class'=>'small_input_text', 'data-min'=>'200', 'data-max'=>'800', 'pattern' => 'sat', 'maxlength' => '3' ) ) }}
				@else
					{{ Form::text( 'sat_math', isset($hsScore->sat_math) ? $hsScore->sat_math : null , array(  'placeholder' =>'200-800', 'id' => 'sat_math', 'class'=>'small_input_text', 'data-min'=>'200', 'data-max'=>'800', 'pattern' => 'sat', 'maxlength' => '3', 'disabled' => 'disabled' ) ) }}
				@endif
					<small class="error">Range: 200-800</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Writing:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
					{{ Form::text( 'sat_writing', isset($hsScore->sat_writing) ? $hsScore->sat_writing : null , array(  'placeholder' =>'200-800', 'id' => 'sat_writing', 'class'=>'small_input_text', 'type'=>'number', 'data-min'=>'200', 'data-max'=>'800', 'pattern' => 'sat', 'maxlength' => '3'  ) ) }}
					<small class="error">Range: 200-800</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Overall:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				@if (isset($hsScore->is_pre_2016_sat) && $hsScore->is_pre_2016_sat)
					{{  Form::text( 'sat_total',  isset($hsScore->sat_total) ? $hsScore->sat_total : null , array(  'placeholder' =>'600-2400', 'id' => 'sat_total', 'class'=>'small_input_text', 'type'=>'number', 'data-min'=>'600', 'data-max'=>'2400', 'pattern' => 'sat_total', 'maxlength' => '4' ) )}}
				@else
					{{  Form::text( 'sat_total',  isset($hsScore->sat_total) ? $hsScore->sat_total : null , array(  'placeholder' =>'600-2400', 'id' => 'sat_total', 'class'=>'small_input_text', 'type'=>'number', 'data-min'=>'600', 'data-max'=>'2400', 'pattern' => 'sat_total', 'maxlength' => '4', 'disabled' => 'disabled' ) )}}
				@endif
					<small class="error">Range: 600-2400</small>
				</div>
	        </div>
	    </div>
	    <div class="toggled-exam post-exam {{ isset($hsScore->is_pre_2016_sat) && $hsScore->is_pre_2016_sat ? 'hide' : '' }}">
	    	<div class="large-3 medium-6 small-12 columns">
	        	<div>Math:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
	    		@if (isset($hsScore->is_pre_2016_sat) && $hsScore->is_pre_2016_sat)
					{{ Form::text('sat_math', isset($hsScore->sat_math) ? $hsScore->sat_math : null ,array( 'placeholder' =>'200-800','id' => 'sat_math','class'=>'small_input_text','pattern' => 'sat','maxlength' => '3', 'disabled' => 'disabled' ))}}
				@else
					{{ Form::text('sat_math', isset($hsScore->sat_math) ? $hsScore->sat_math : null ,array( 'placeholder' =>'200-800','id' => 'sat_math','class'=>'small_input_text','pattern' => 'sat','maxlength' => '3' ))}}
				@endif
					<small class="error">Range: 200-800</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Reading/Writing:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
					{{ Form::text('sat_reading_writing', isset($hsScore->sat_reading_writing) ? $hsScore->sat_reading_writing : null ,array( 'placeholder' =>'200-800','id' => 'sat_reading_writing','class'=>'small_input_text','pattern' => 'sat','maxlength' => '3' ))}}
					<small class="error">Range: 200-800</small>
				</div>
	        </div>
	        <div class="large-3 medium-6 small-12 columns">
	        	<div>Overall:&nbsp;&nbsp;</div>
				<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
	    		@if (isset($hsScore->is_pre_2016_sat) && $hsScore->is_pre_2016_sat)
					{{ Form::text('sat_total', isset($hsScore->sat_total) ? $hsScore->sat_total : null ,array( 'placeholder' =>'400-1600','id' => 'sat_total','class'=>'small_input_text','pattern' => 'post_sat_total','maxlength' => '4', 'disabled' => 'disabled' ))}}
				@else
					{{ Form::text('sat_total', isset($hsScore->sat_total) ? $hsScore->sat_total : null ,array( 'placeholder' =>'400-1600','id' => 'sat_total','class'=>'small_input_text','pattern' => 'post_sat_total','maxlength' => '4' ))}}
				@endif
					<small class="error">Range: 400-1600</small>
				</div>
	        </div>
	    </div>
	</div>
	<br/>

	<!-- GRE SCORES  -->
    <div class='row title'>
    	<div class="column small-12">
    		GRE Scores
    	</div>
    </div>
    <br/>
	<div class="row label_gray_normal_14" style="line-height:30px;">
		<div class="medium-4 columns">
        	<div>Verbal:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text( 'gre_verbal', isset($hsScore->gre_verbal) ? $hsScore->gre_verbal : null, array(  'placeholder' =>'130-170','id' => 'gre_verbal_id','class'=>'small_input_text','pattern' => 'gre','maxlength' => '3') ) }}
				<small class="error">Range: 130-170</small>
			</div>
		</div>
        <div class="medium-4 columns">
        	<div>Quantitative:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('gre_quantitative', isset($hsScore->gre_quantitative) ? $hsScore->gre_quantitative : null, array( 'placeholder' =>'130-170','id' => 'gre_quantitative_id','class'=>'small_input_text','pattern' => 'gre','maxlength' => '3' ))}}
				<small class="error">Range: 130-170</small>
			</div>
        </div>
        <div class="medium-4 columns">
        	<div>Analytical:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-6 medium-3 columns paddingleftright0">
				{{ Form::text('gre_analytical', isset($hsScore->gre_analytical) ? $hsScore->gre_analytical : null, array( 'placeholder' =>'0-6','id' => 'gre_analytical_id','class'=>'small_input_text','pattern' => 'gre_analytical','maxlength' => '1' ))}}
				<small class="error">Range: 0-6</small>
			</div>
        </div>
        <div class="large-3 columns paddingleft0">&nbsp;</div>
	</div>
	<br/>

    <div class='row title'>
    	<div class="small-12 column">
    		GED Scores
    	</div>
    </div>
    <br/>
	<div class="row label_gray_normal_14">
		<div class="large-6 columns">

        	<div style="float:left;" class="large-3 medium-3 columns paddingleftright0">GED Pass/Fail:&nbsp;&nbsp;</div>
        	{{ Form::hidden('gedfp', isset($hsScore->gedfp) ? $hsScore->gedfp : null , array('id' => 'gedInput' ))}} 

			<div class="large-4 medium-3 columns paddingleftright0 left">
				<div data-status='Pass' class='gedIcons gedpassicon {{$hsScore->gedfpPassStatus or ''}}' onclick='gedClicked(this);'></div>
			</div>
			<div class="large-4 medium-3 columns paddingleftright0 left">
				<div data-status='Fail' class='gedIcons gedfailicon {{$hsScore->gedfpFailStatus or ''}}' onclick='gedClicked(this);'></div>
			</div>
        </div>
        <div class="large-6 columns">
        	<div style="float:left;" class="large-3 medium-3 columns paddingleftright0">GED Score:&nbsp;&nbsp;</div>
			<div style="float:left;" class="large-3 medium-3 columns paddingleftright0 end">
				{{ Form::text('ged_score', isset($hsScore->ged_score) ? $hsScore->ged_score : null ,array( 'placeholder' =>'200-800','id' => 'ged_score','class'=>'small_input_text','pattern' => 'ged','maxlength' => '3' ))}}
				<small class="error">Range: 200-800</small>
			</div>
            <div style="float:left;" class="large-3 medium-3 columns paddingleftright0">&nbsp;</div>
        </div>
	</div>
	<br/>


	<!-- Other school area -->
    <div class='row title'>
    	<div class="small-12 column">
    		Other
    	</div>
    </div>
    <br/>
	<div class="row label_gray_normal_14" id="OtherScoreParent">

	    @if( isset( $hsScore->other_values ) )
	    	@if(is_array($hsScore->other_values))
			    @foreach( $hsScore->other_values as $key => $scoreValue)
				    <div class="large-12 columns otherScoreRow">
					    <div style="float:left;" class="large-5 medium-3 columns paddingleft0 scoreClassName">
					    	<input type="text" name='othervalueScore[{{$key}}][class]'  value="{{$scoreValue->class or 0}}" placeholder="Type exam name..." pattern="other_exam" />
							<small class="error">Not a valid name</small>
					    </div>
						<div style="float:left;" class="large-2 medium-3 columns paddingleftright0 scoreClassScore">
							<input type="text" name='othervalueScore[{{$key}}][score]' class="small_input_text" value="{{$scoreValue->score or 0}}" placeholder="score" pattern='other_score' maxlength='4'/>
							<small class="error">Not a valid score</small>
						</div>
						<div class="large-2 medium-3 columns paddingleftright0 scoreCloseButton closex-btn-box" style="float:left;">
							<img class="closex-img" border="0" align="absmiddle" onclick="RemoveOtherScore({{$key}})" style="cursor:pointer;" src="../images/close-x.png" />
						</div>
				    </div>
			    @endforeach
		    @else
		    	<div class="large-12 columns otherScoreRow">
				    <div style="float:left;" class="large-5 medium-3 columns paddingleft0 scoreClassName">
				    	<input type="text" name='othervalueScore[0][class]'  value="{{$hsScore->other_exam or 0}}" placeholder="Type exam name..." pattern="other_exam" />
						<small class="error">Not a valid name</small>
				    </div>
					<div style="float:left;" class="large-2 medium-3 columns paddingleftright0 scoreClassScore">
						<input type="text" name='othervalueScore[0][score]' class="small_input_text" value="{{$hsScore->other_values or 0}}" placeholder="score" pattern='other_score' maxlength='4'/>
						<small class="error">Not a valid score</small>
					</div>
					<div class="large-2 medium-3 columns paddingleftright0 scoreCloseButton closex-btn-box" style="float:left;">
						<img class="closex-img" border="0" align="absmiddle" onclick="RemoveOtherScore(0)" style="cursor:pointer;" src="../images/close-x.png" />
					</div>
			    </div>
		    @endif
	    @else
		    <div class="large-12 columns otherScoreRow">
			    <div style="float:left;" class="small-7 large-5 medium-5 paddingleft0 scoreClassName">
			    	<input type="text" name='othervalueScore[0][class]'  value="" placeholder="Exam name..." pattern="other_exam" />
						<small class="error">Not a valid name</small>
			    </div>
				<div style="float:left;" class="small-3 large-2 medium-3 columns paddingleftright0 scoreClassScore">
					<input type="text" name='othervalueScore[0][score]'  class="small_input_text" value="" placeholder="score" pattern='other_score' maxlength='4'/>
					<small class="error">Not a valid score</small>
				</div>
				<div class="small-2 small-text-center large-2 medium-3 medium-text-left columns paddingleftright0 scoreCloseButton closex-btn-box" style="float:left;">
					<img class="closex-img" border="0" align="absmiddle" onclick="RemoveOtherScore(0)" style="cursor:pointer;" src="../images/close-x.png" />
				</div>
		    </div>
	    @endif
	</div>
	<!-- End Other school area -->











    <div class="row">
	    <div class="large-12 column">
	    	<div class="add_button" onclick="AddOther();">add a score</div>
	    </div>
    </div>
    <br/>
    <br/>   
    
    <div class='row'>
		<div class="small-6 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class="small-6 column">
			{{ Form::submit('Save', array('class'=>'button btn-Save'))}}
		</div>
		<!--
        <div class="large-3 small-12 column btn-save-continue">Save &amp; Continue</div>
		-->
	</div>
{{ Form::close() }}
</div>

<div class='reveal-modal medium remove_before_ajax' id="AddEditCollegeScore" data-reveal>
	{{ Form::open(array('url' => "ajax/profile/scores/" , 'method' => 'POST', 'id' => 'CollegeScoreForm','data-abide'=>'ajax')) }}
	{{ csrf_field() }}
	{{ Form::hidden('postType','college',array()) }}
	<div class="row">
		<div class='large-4 column page_head_black'><span class="score-icon"><img src="../images/icon-score.png" alt=""/></span>&nbsp;&nbsp;Scores</div>
		<div class='large-8 column requiredtxt'>* Recruiters will NEED this information in order to contact you.</div>
	</div>

	<br/>
	<br/>

	<div class="row">
		<div class='large-3 column normal_head_black'>College Scores</div>
		<!--
		<div class='large-4 column highschoolWhotxt' style="color:#797979 !important;">Who can see this? <a href="#" style="color:#98D0EE !important;">Only you</a></div>
		-->
	</div>	
	
	
	<div class="row">
		<hr>
		<div class="column small-12 medium-6">
			<div class='green_head'>College GPA:</div>
		</div>
		<div class="column small-12 medium-6">
			<!-- grade calculator -->
			<div class="row collapse convert-international-grades-link">
				<div class="column small-12">
					<a href="http://www.foreigncredits.com/Resources/GPA-Calculator/" target="_blank">How do I convert my international GPA?</a>
				</div>
			</div>
		</div>
	</div>
	
	<br/>
	<div class="row label_gray_normal_14">
		<div class="small-3 columns paddingleft0">Overall GPA</div>
		@if(isset($hsScore->overall_gpa))
			<?php $overall_gpa = $hsScore->overall_gpa ?>
		@else
			<?php $overall_gpa = null; ?>
		@endif
		<div class="small-9 columns">{{ Form::text('overall_gpa', $overall_gpa, array( 'placeholder' =>'score', 'id' => 'overall_gpa', 'class'=>'small_input_text','pattern'=>'gpa', 'maxlength' => '4'))}}</div>
	</div>
	<br/>

	<div class='row'>
		<div class="small-6 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class="small-6 column">
			{{ Form::submit('Save', array('class'=>'button btn-Save'))}}
		</div>
		<!--
        <div class="large-3 small-12 column btn-save-continue">Save &amp; Continue</div>
		-->
	</div>
	{{ Form::close() }}
</div>

<script language="javascript">
	
	//Handle all clicks on the GED Scores Pass or fail
	function gedClicked(item){
		var item = $(item);
		var buttonType = item.data('status');

		if (!item.hasClass('active')) {
			$('.gedIcons').removeClass('active');
			item.addClass('active');
			$('#gedInput').val(buttonType);
		} else {
			$('.gedIcons').removeClass('active');
			$('#gedInput').val('');
		};
	}

	//setGEDFP(1);
	function AddOther(){	
		var newRow = '';

		newRow += ' <div class="large-12 columns otherScoreRow">';
		newRow +=		'<div style="float:left;" class="small-7 large-5 medium-5 paddingleft0 scoreClassName">';
		newRow +=			'<input type="text" name=""   value="" placeholder="Exam name..." pattern="other_exam" />';
		newRow +=			'<small class="error">Not a valid name</small>';
		newRow +=		'</div>';
		newRow +=		'<div style="float:left;" class="small-3 large-2 medium-3 columns paddingleftright0 scoreClassScore">';
		newRow +=			'<input type="text" name="" class="small_input_text" value="" placeholder="score" pattern="other_score" maxlength="4"/>';
		newRow +=				'<small class="error">Not a valid score</small>';
		newRow +=			'</div>';
		newRow +=			'<div class="small-2 small-text-center large-2 medium-3 medium-text-left columns paddingleftright0 scoreCloseButton closex-btn-box" style="float:left;">';
		newRow +=				'<img class="closex-img" border="0" align="absmiddle" onclick="" style="cursor:pointer;" src="../images/close-x.png" />';
		newRow +=			'</div>';
		newRow +=		'</div>';
		
		$("#OtherScoreParent").append(newRow);
		RebuildOtherSchoolIndex();
		resetScoreForm();
	}

	function RemoveOtherScore(num){
		//Get the count of how many rows there are save the count.
		var scoreRows = $('.otherScoreRow');
		var scoreRowCount = scoreRows.length;

		if (scoreRowCount > 1) {
			scoreRows.eq(num).remove();
		} else {
			scoreRows.eq(num).find('input').val('');
		};
		
		RebuildOtherSchoolIndex(scoreRows);
	}

	function RebuildOtherSchoolIndex(){
		//This will rebuild the index so when the users deletes a row there will be no double index numnbers.
		var scoreRows = $('.otherScoreRow');
		$.each(scoreRows, function(index, val) {
			$(val).find('.scoreClassName input').attr('name', 'othervalueScore['+ index +'][class]');
			$(val).find('.scoreClassScore input').attr('name', 'othervalueScore['+ index +'][score]');
			$(val).find('.scoreCloseButton img').attr('onclick', 'RemoveOtherScore('+ index +')');
		});
	}

	function resetScoreForm(){
		//maybe be better way but going to use this path now.
		$('#ScoreInfoForm').foundation({
			abide : {
				focus_on_invalid : true,
				patterns : {
					act: /^([1-9]|[1-2][0-9]|[3][0-6])$/,
					ged: /^([2-7]){1}([0-9]){1}0|800$/,
					gpa: /^(([0-3]){1}\.([0-9]){1,2}|4\.(0){1,2}|([0-4]){1})$/,
					max_weighted_gpa: /^(([0-9])+|([0-9])+\.([0-9]){1,2})$/,
					sat: /^([2-7][0-9][0]|[8][0][0])$/,
					psat: /^([2-7][0-9]|[8][0])$/,
					sat_total:/^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/,
					psat_total:/^([6-9][0-9]|[1][0-9][0-9]|[2][0-3][0-9]|[2][4][0])$/,
					other_exam: /^[a-zA-Z0-9\.,#\- ]+$/,
					other_score: /^[0-9\+\-.]+$/
				},
				validators:{
					weighted_gpa: function(el, required, parent){
						var val = el.value;
						var max = $('#max_weighted_gpa').val();
						if(max == ""){
							return true;
						}
						else{
							if(parseFloat(val) > parseFloat(max)){
								return false;
							}
							else{
								return true;
							}
						}
					}
				}
			}
		});


	}


	//reload zurb items.
	$(document).foundation({
		abide : {
			focus_on_invalid : true,
			patterns : {
				act: /^([1-9]|[1-2][0-9]|[3][0-6])$/,
				ged: /^([2-7]){1}([0-9]){1}0|800$/,
				gpa: /^(?!0$)(([0-3]){1}\.([0-9]){1,2}|4\.(0){1,2}|([0-4]){1})$/,
				max_weighted_gpa: /^(([0-9])+|([0-9])+\.([0-9]){1,2})$/,
				sat: /^([2-7][0-9][0]|[8][0][0])$/,
				psat: /^([2-7][0-9]|[8][0])$/,
				sat_total:/^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/,
				psat_total:/^([6-9][0-9]|[1][0-9][0-9]|[2][0-3][0-9]|[2][4][0])$/,
				post_sat_total: /^([4-9][0-9][0-9]|[1][0-5][0-9][0-9]|1600)$/,
				post_psat: /^(1[6-9][0-9]|[2-6][0-9][0-8]|[7[0-6]0)$/,
				post_psat_total: /^(3[2-9][0-9]|[4-9][0-9][0-9]|1[0-4][0-9][0-9]|15[0-2]0)$/,
				other_exam: /^[a-zA-Z0-9\.,#\- ]+$/,
				other_score: /^[0-9\+\-.]+$/,
				toefl: /^(?:\d|[1-2]\d|3[0])$/,
				toefl_overall: /^([0-9]?[0-9])$/,
				toefl_ibt_overall: /^([0-9]?[0-9]|[1][0-1][0-9]|12[0])$/,
				toefl_pbt_reading: /^(3[1-9]|[4-5][0-9]|6[0-7])$/,
				toefl_pbt_listening: /^(3[1-9]|[4-5][0-9]|6[0-8])$/,
				toefl_pbt_overall: /^(3[1-9][0-9]|[4-5][0-9][0-9]|6[0-6][0-9]|67[0-7])$/,
				ielts: /^(?!$)(([0-8]){1}\.([0-9])|9\.(0){1,2}|([0-9]){1})$/,
				ap: /^([1-5])$/,
				lsat: /^(1[2-7][0-9]|180)$/,
				gmat: /^([2-7][0-9][0-9]|800)$/,
				pte: /^([1-8][0-9]|90)$/,
				gre: /^(1[3-6][0-9]|170)$/,
				gre_analytical: /^([0-6])$/,
			},
			validators:{
				weighted_gpa: function(el, required, parent){ 
					var val = el.value;
					var max = $('#max_weighted_gpa').val();
					if(max == ""){
						return true;
					}
					else{
						if(parseFloat(val) > parseFloat(max)){
							return false;
						}
						else{
							return true;
						}
					}
				}
			}
		}
	});

	SaveHighSchoolScores();
	SaveCollegeScores();

	// Validate weighted_gpa against max_weighted if max_weighted is specified
	$(document).ready(function(){
		$('#max_weighted_gpa').change(function(){
			Foundation.libs.abide.validate($('#weighted_gpa'), {type:'text'});
		});
	});


</script>
