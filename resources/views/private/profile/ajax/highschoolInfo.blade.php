<script src="../dropzone/dropzone.js?7"></script>
<script type="text/javascript" src="../js/jquery.form.min.js?7"></script>
<script type="text/javascript">
//reload zurb items.
function init_hsi_fndtn(){
	$(document).foundation({
		abide : {
			patterns : {
				school_name: /^[a-zA-Z0-9\.,#\- ]+$/,
				file_types:/^[0-9a-zA-Z\\:\-_ ]+.(jpg|png|gif|bmp|doc|docx|pdf|JPG|PNG|GIF|BMP|DOC|DOCX|PDF)$/
			}
		}
	});
}
$(function() {
	hsPostSchool();
	hsPostCourse();
});

$(document).ready(function() {   

	$('#hsInfoSubject').change(function(){
		getClasses( 0 );
	});

	init_hsi_fndtn();
}); 

function showLoadingHs()
{
	var transcriptFile=document.uploadTranscriptForm.transcript.value;
	if(transcriptFile!="")
	{
		var ArrFile=transcriptFile.split(".");
		var Ext=ArrFile[parseInt(ArrFile.length)-1];
		if(Ext!='jpg' && Ext!='jpeg' && Ext!='png' && Ext!='gif' && Ext!='doc' && Ext!='docx' && Ext!='pdf' && Ext!='txt' && Ext!='odf')
		{
			alert("You are uploading a wrong file for transcript. "+Ext+" is not allowed.");
			return false;	
		}
		else
		{
			$('#UploadButtonHs1').html('<span class="headColData">Upload in Progress, Please Wait...</span>');
			$('#UploadButtonHs2').html('<span class="headColData">Upload in Progress, Please Wait...</span>');
		}
	}else{
	return false;
	}
	//alert(document.getElementById('TranscriptPath').value);	
}
</script>
<!-- hs View -->
<div class='viewmode' style='display:block;'>
	<!-- High School Area -->
    <div class="hs-icon-course">&nbsp;</div>
    <h2 class="highschoolHead">High School Info</h2>
    <span class="requiredtxt">* Recruiters will NEED this information in order to contact you.</span>
    <br /><br />



	<div class='row' style='padding-bottom: 15px; border-bottom: 1px solid rgb(185, 186, 187); margin-bottom: 10px;'>
		<div class='small-4 large-7 column progress-div '>My Schools</div>
		<div class='small-4 large-2 column schoolThead'>Total Courses</div>
		<div class='small-3 large-2 column schoolThead'>Total Units</div>
		<div class='small-1 large-1 column'>&nbsp;</div>
	</div>
	@if (!$hsSchoolInfo)
		<div class='row'>
			<div class='column'>
				<div>A school can be added by clicking on add a course below</div>
			</div>
		</div>
	@endif

    @if(isset($hsSchoolInfo))
	    @foreach ($hsSchoolInfo as $hsSchoolInfo)
	    		@if ($hsSchoolInfo['latest'])
				<?php $current = 'current'; ?>
                @else
                <?php $current = ''; ?>
                @endif






























                
		    <div class='row'>
				<div class='small-4 large-7 column school-detail left-border-{{$hsSchoolInfo['colorNum']}} {{$current}}'>{{$hsSchoolInfo['school_name']}}</div>
				<div class='small-4 large-2 column text-center'>{{$hsSchoolInfo['courseCount']}}</div>
				<div class='small-3 large-2 column text-center'>{{$hsSchoolInfo['courseUnits']}}</div>
				<div class='small-1 large-1 column' style='color:blue;'>
					<div class='edit_pencil' onclick='hsEditSchool(this);' data-hs-info='{{ htmlspecialchars( json_encode($hsSchoolInfo), ENT_QUOTES ) }}'><span class="edit-icon"><img src="../images/edit_pencil.png" alt=""/></span></div>
		        </div>
			</div>
		@endforeach
	@endif

	<br/><br/>


	<!-- Course Title Area -->
    <div class="row">
		<div class='large-3 column progress-div'>
			My Courses
		</div>
		<div class='large-4 column'>
			<a class="highschoolAdd" onclick='hsAddNewCourse(this);'>+ add a new course</a>
		</div>
		<div class='large-5 column highschoolWhotxt'>
			<!--Who can see this? <a href="#" style="color:#98D0EE !important;">Only you</a>-->
		</div>
    </div>
	<hr>
	<!-- End Course Title Area -->

	@if (!$hsSchoolYears)
		<div class='row'>
			<div class='column'>
				<div>You haven’t added any courses yet</div>
			</div>
		</div>
	@endif


	<!-- Freshman section -->
	@if(isset($hsSchoolYears['Freshman']))
		<div class='row green_head'>
			<div class='small-3 column'><span class="edit-normal"><img src="../images/icon-arrow-up.png" alt=""/></span>&nbsp;Freshman</div>
			<div class='small-9 column'>9th grade</div>
		</div><br/>
		@foreach ($hsSchoolYears['Freshman'] as $key => $semester)
			<div class='row black_sub_head'>
				<div class='small-12 column'>{{$key}}</div>
			</div><br/>
			@foreach ($semester as $key2 => $course)
					@if ($key2 % 2 == 0)
                    <?php $rowCount = 'row_second';?>
					@else
                    <?php $rowCount = 'row_first';?>
                    @endif
				<div class='row {{$rowCount}} left-border-{{$course['colorNum']}}'>
					<div class='small-3 column'>{{$course['subject']}}</div>
					<div class='small-3 column'>
                  
					@if($course['custom_class_name']!="NULL" && $course['custom_class_name']!="")					
					{{ $course['custom_class_name']}}
					@else					
					{{ $course['clName']}}
					@endif
                    </div>
					<?php $hsclassLevel; ?>
						@if ($course['class_level'] == 1)
                        <?php $hsclassLevel = 'Basic'; ?>
						@elseif($course['class_level'] == 2)
                        <?php $hsclassLevel = 'Honors'; ?>
                        @else
                        <?php $hsclassLevel = 'AP'; ?>
                        @endif
					<div class='small-3 column'>{{$hsclassLevel}}</div>
					<div class='small-2 column'>{{{$course['course_grade'] or '--'}}}</div>
					<div class='small-1 column edit_pencil'>
			        	<span class="edit-icon"><img data-hs-info='{{ htmlspecialchars( json_encode($course), ENT_QUOTES ) }}' src="../images/edit_pencil.png" onclick='hsEditCourse(this);' alt=""/></span>
			        </div>
				</div>
			@endforeach
		@endforeach
	@endif
	<!-- End Freshman section -->
<br />
<div class="clearfix"></div>
	<!-- Sophomore section -->
	@if(isset($hsSchoolYears['Sophomore']))
		<div class='row green_head'>
			<div class='small-1 column'><img class="edit-normal" src="../images/icon-arrow-up.png" alt=""/></div>
			<div class='small-3 column'>&nbsp;Sophomore</div>
			<div class='small-6 column'>10th grade</div>
		</div><br/>
		@foreach ($hsSchoolYears['Sophomore'] as $key => $semester)
			<div class='row black_sub_head'>
				<div class='small-12 column'>{{$key}}</div>
			</div><br/>
			@foreach ($semester as $key2 => $course)
                @if ($key2 % 2 == 0)
                <?php $rowCount = 'row_second';?>
                @else
                <?php $rowCount = 'row_first';?>
                @endif
				<div class='row {{$rowCount}} left-border-{{$course['colorNum']}}'>
					<div class='small-3 column'>{{$course['subject']}}</div>
					<div class='small-3 column'>
                   @if($course['custom_class_name']!="NULL" && $course['custom_class_name']!="")					
					{{ $course['custom_class_name']}}
					@else					
					{{ $course['clName']}}
					@endif
                    </div>
					<?php $hsclassLevel; ?>
						@if ($course['class_level'] == 1)
                        <?php $hsclassLevel = 'Basic'; ?>
						@elseif($course['class_level'] == 2)
                        <?php $hsclassLevel = 'Honors'; ?>
                        @else
                        <?php $hsclassLevel = 'AP'; ?>
                        @endif
					<div class='small-3 column'>{{$hsclassLevel}}</div>
					<div class='small-2 column'>{{{$course['course_grade'] or '--'}}}</div>
					<div class='small-1 column edit_pencil'>
			        	<span class="edit-icon"><img data-hs-info='{{ htmlspecialchars( json_encode($course), ENT_QUOTES ) }}' src="../images/edit_pencil.png" onclick='hsEditCourse(this);' alt=""/></span>
			        </div>
				</div>
			@endforeach
		@endforeach
	@endif
	<!-- End Sophomore section -->
<br />
<div class="clearfix"></div>
	<!-- Junior section -->
	@if(isset($hsSchoolYears['Junior']))
		<div class='row green_head'>
			<div class='small-3 column'><span class="edit-normal"><img src="../images/icon-arrow-up.png" alt=""/></span>&nbsp;Junior</div>
			<div class='small-9 column'>11th grade</div>
		</div><br/>
		@foreach ($hsSchoolYears['Junior'] as $key => $semester)
			<div class='row black_sub_head'>
				<div class='small-12 column'>{{$key}}</div>
			</div><br/>
			@foreach ($semester as $key2 => $course)
				 @if ($key2 % 2 == 0)
                <?php $rowCount = 'row_second';?>
                @else
                <?php $rowCount = 'row_first';?>
                @endif
				<div class='row {{$rowCount}} left-border-{{$course['colorNum']}}'>
					<div class='small-3 column'>{{$course['subject']}}</div>
					<div class='small-3 column'>
                     @if($course['custom_class_name']!="NULL" && $course['custom_class_name']!="")					
					{{ $course['custom_class_name']}}
					@else					
					{{ $course['clName']}}
					@endif
                    </div>
					<?php $hsclassLevel; ?>
						@if ($course['class_level'] == 1)
                        <?php $hsclassLevel = 'Basic'; ?>
						@elseif($course['class_level'] == 2)
                        <?php $hsclassLevel = 'Honors'; ?>
                        @else
                        <?php $hsclassLevel = 'AP'; ?>
                        @endif
					<div class='small-3 column'>{{$hsclassLevel}}</div>
					<div class='small-2 column'>{{{$course['course_grade'] or '--'}}}</div>
					<div class='small-1 column edit_pencil'>
			        	<span class="edit-icon"><img data-hs-info='{{ htmlspecialchars( json_encode($course), ENT_QUOTES ) }}' src="../images/edit_pencil.png" onclick='hsEditCourse(this);' alt=""/></span>
			        </div>
				</div>
			@endforeach
		@endforeach
	@endif
	<!-- End Junior section -->
<br />
<div class="clearfix"></div>
	<!-- Senior section -->
	@if(isset($hsSchoolYears['Senior']))
		<div class='row green_head'>
			<div class='small-5 large-3 column'><span class="edit-normal"><img src="../images/icon-arrow-up.png" alt=""/></span>&nbsp;Senior</div>
			<div class='small-7 large-9 column'>12th grade</div>
		</div><br/>
		@foreach ($hsSchoolYears['Senior'] as $key => $semester)
			<div class='row black_sub_head'>
				<div class='small-12 column'>{{$key}}</div>
			</div><br/>
			@foreach ($semester as $key2 => $course)
				 @if ($key2 % 2 == 0)
                <?php $rowCount = 'row_second';?>
                @else
                <?php $rowCount = 'row_first';?>
                @endif
				<div class='row {{$rowCount}} left-border-{{$course['colorNum']}}'>
					<div class='small-3 column'>{{$course['subject']}}</div>
					<div class='small-3 column'>
                     @if($course['custom_class_name']!="NULL" && $course['custom_class_name']!="")					
					{{ $course['custom_class_name']}}
					@else					
					{{ $course['clName']}}
					@endif
                    </div>
					<?php $hsclassLevel; ?>
						@if ($course['class_level'] == 1)
                        <?php $hsclassLevel = 'Basic'; ?>
						@elseif($course['class_level'] == 2)
                        <?php $hsclassLevel = 'Honors'; ?>
                        @else
                        <?php $hsclassLevel = 'AP'; ?>
                        @endif
					<div class='small-3 column'>{{$hsclassLevel}}</div>
					<div class='small-2 column'>{{{$course['course_grade'] or '--'}}}</div>
					<div class='small-1 column edit_pencil'>
			        	<span class="edit-icon"><img data-hs-info='{{ htmlspecialchars( json_encode($course), ENT_QUOTES ) }}' src="../images/edit_pencil.png" onclick='hsEditCourse(this);' alt=""/></span>
			        </div>
				</div>
			@endforeach
		@endforeach
	@endif
	<!-- End Senior section -->

	
	<br><br/><br/>
<!--
	<div class="row">
		<div class='large-3 column progress-div'>
				My Transcripts
		</div>
		<div class='large-4 column'>
			<a id='hs_add_transcript' class="highschoolAdd">
				+ add a transcript
			</a>
		</div>
		<div class='large-5 column highschoolWhotxt'>
			Who can see this? <a href="#" style="color:#98D0EE !important;">Only you</a>
		</div>
    </div>	
	<hr>
    <div class='row transcript_row'>
		<div class='small-6 column headCol'>File Name</div>
		<div class='small-4 medium-5 end column headCol'>Date uploaded</div>
    </div>
	@if(count($transcript_data)>0)
		@foreach($transcript_data as $key=>$tData)
		<div class='row collapse transcript_row' id="TranscriptHsRow_{{$tData->id}}">
			<div class='small-12 column'>
				<div class='row'>
					<div class='small-6 column headColData' style="word-wrap:break-word;">
						<a id="transcript_preview_link" data-transcript-name="{{$tData->transcript_name}}" onclick="openTranscriptPreview(this);">{{$tData->transcript_name}}</a>
					</div>
					<div class='small-4 medium-5 column headColData'>{{$tData->created_at}}</div>
					<div class='small-2 medium-1 column close_x' align="right">
						<img class='remove_transcript' data-id='{{ $tData->id }}' src="../images/close-x.png" border="0" style="cursor:pointer;"/>
					</div>
				</div>
				<div id='transcript_confirm_row_{{ $tData->id }}' class='row transcript_confirm_row'>
					<div class='small-6 large-2 large-offset-8 column'>
						<div class='button btn-cancel transcript_del_cancel' data-id='{{ $tData->id }}'>
							Cancel
						</div>
					</div>
					<div class='small-6 large-2 column'>
						<div class='button btn-Save transcript_del_confirm' data-id='{{ $tData->id }}'>
							Delete
						</div>
					</div>
				</div>
			</div>
		</div>
		@endforeach
    @else
	<div class='row' style='color:black; font-weight:bold;'>
		<div class="no-transcript">You haven’t added a transcript</div>
	</div>
	@endif
-->
	<!--\\\\\\\\\\\\\\\\\\\\ TRANSCRIPT INFO ////////////////////-->
	@if(App::environment('local'))
		<!-- This will spit out the info in $data for Dev reasons. -->
		<div class='row'>
			<div class='column'>
				<div style='color:#000; background-color:#fff;'>
				  	<?php 
				  		//Un-comment below to check the $data this ajax call is bringing in.
				  		/*
				  		echo '<pre>';
				  		print_r($data);
				  		echo '</pre>';
				  		*/
				  	?>
  				</div>
			</div>
		</div>
		@endif
</div>
<!-- End hs View -->






<!-- hsEditSchool modal -->
<div class='reveal-modal small row remove_before_ajax' id="hsEditSchool" data-reveal>
	{{ Form::open(array('url' => "ajax/profile/highschoolInfo/". NULL , 'method' => 'POST', 'id' => 'highschoolInfoForm','data-abide'=>'ajax')) }}
	{{ Form::hidden('ajaxtoken', NULL , array()) }}
	{{ Form::hidden('postType', '', array()) }}
	{{ Form::hidden('originalSchoolId', null, array('id' => 'originalSchoolId')) }}
	{{ Form::hidden('hsSchoolPickedId', null , array('id'=>'hsSchoolPickedId')) }}
	<div class='row'>
		<div class='small-12 column edit_school_heading'>
			Edit School
		</div>
	</div>
	<hr>
	<!--//////////////////// Change HS Attended \\\\\\\\\\\\\\\\\\\\-->
	<div class='row'>
		<div class='small-12 large-4 column'>
			{{ Form::label('hs_info_change_hs_attended', 'School Name', array('class' => 'edit_school_label')) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::select('hs_info_change_hs_attended', $schools_attended['high_schools'], $user['current_hs_id'], array('required')) }}
			<small class='error'>Select an option</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Change HS Attended ////////////////////-->
	<!--//////////////////// Change HS AutoComplete \\\\\\\\\\\\\\\\\\\\-->
	<div id='hs_info_change_new_school_row' class='row' style='display: none;'>
		<div class='small-12 large-4 column'>
			{{ Form::label('hs_info_change_new_school', 'Add School', array('class' => 'edit_school_label')) }}
		</div>
		<div id='hs_info_change_new_school_container' class='small=12 large-8 column ui-front'>
			{{ Form::text('hs_info_change_new_school', null, array( 'placeholder' => 'search for a school' )) }}
			<small class='error'>Search for a school or enter your own</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Change HS AutoComplete ////////////////////-->
	<!--//////////////////// Change HS Current School \\\\\\\\\\\\\\\\\\\\-->
	<div class='row'>
		<div class="small-12 large-8 large-offset-4 columns model_label_txt">
			{{ Form::checkbox('hsInfoSchoolCurrent', '1', null, array( 'id' => 'hsInfoSchoolCurrent' ))}}
			{{ Form::label( 'hsInfoSchoolCurrent', 'My Current School', array('class' => 'edit_school_label') ) }}
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Change HS Current School ////////////////////-->
	<!--//////////////////// High Schools Warning \\\\\\\\\\\\\\\\\\\\-->
	<div class='row'>
		<div class='small-12 column edit_school_merge_warning'>
			Warning! This will move all of your courses associated with this school into the one selected! Once done, you will have to manually change your courses' school to undo this action.
		</div>
	</div>
	<div class='row'>
		<div class='small-12 column deleteWarning'>
			To remove this school you must delete all courses linked to it below.
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ High Schools Warning ////////////////////-->
    <div class='row'>
		
		<div class='small-12 large-6 column text-center'>
			<div class='button btn-cancel btn-remove-school' onclick="hsremoveSchool(this);">Remove this school</div>
		</div>
		<div class="small-12 large-3 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class='small-12 large-3 column'>{{ Form::submit('Save', array('class'=>'button btn-Save'))}}</div>

		<!--
		<div class="large-offset-4 small-2 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class='small-4 large-4 column text-center'>
			<div class='button btn-cancel btn-remove-school' onclick="hsremoveSchool(this);">Remove this school</div>
		</div>
		<div class='small-1 column end'>{{ Form::submit('Save', array('class'=>'button btn-Save'))}}</div>
		-->
	</div>	
	{{ Form::close() }}
</div>
<!-- end hsEditSchool modal -->














<!-- hsEditCourse modal -->
<div class='reveal-modal small remove_before_ajax' id="hsEditCourse" data-reveal data-default-hs='{{ htmlspecialchars( json_encode($currentSchool), ENT_QUOTES ) }}' >
	<!-- We save current school info in the modal so we can allways reset it since its not in the DOM. --> 
	{{ Form::open(array('url' => "ajax/profile/highschoolInfo/". NULL , 'method' => 'POST', 'id' => 'highschoolInfoCourseForm','data-abide'=>'ajax')) }}
	{{ Form::hidden('postType', 'newandEditCourse',array()) }}

	@if (isset($currentSchool['id']))
		{{ Form::hidden('hsSchoolId', $currentSchool['id'] , array('id'=>'hsSchoolId')) }}
	@else
		{{ Form::hidden('hsSchoolId', null , array('id'=>'hsSchoolId')) }}

		@endif
	{{ Form::hidden('courseId', null ,array()) }}
    {{ Form::hidden('hsclassLevel', '1',array('id' => 'hsclassLevel')) }}
    {{ Form::hidden('hsclassGradeSub', '0',array('id' => 'hsclassGradeSub')) }} 
	<div class='row'>
		<div class='small-12 column edit_school_heading'>
			<div class="hs-icon-course">&nbsp;</div>
			High School Info
		</div>
	</div>
	<!--
    <h2 style="float:left;">High School Info</h2>
	-->
	<hr>
	
	<!-- Edit school -->
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			Add a course at:
		</div>
	</div>

		<!--//////////////////// High Schools Attended Dropdown \\\\\\\\\\\\\\\\\\\\-->
		<div class='row'>
			<div class='small-12 large-4 column'>
				{{ Form::label('hs_info_hs_attended', 'School Name', array('class' => 'hs_info_label')) }}
			</div>
			<div class='small-12 large-8 column'>
				{{ Form::select('hs_info_hs_attended', $schools_attended['high_schools'], $user['current_hs_id'], array('required')) }}
			<small class="error">Select your high school, or select 'search for another'...</small>
			</div>
		</div>
		<!--\\\\\\\\\\\\\\\\\\\\ High Schools Attended Dropdown ////////////////////-->
		<!--//////////////////// High Schools New School Autocomplete \\\\\\\\\\\\\\\\\\\\-->
		<div id='hs_info_new_school_row' class='row' style='display: none;'>
			<div class='small-12 large-4 column'>
				{{ Form::label('hs_info_new_school', 'Add a school', array('class' => 'hs_info_label')) }}
			</div>
			<div id='hs_info_new_school_container' class='small-12 large-8 column ui-front'>
			{{ Form::text('hs_info_new_school', '', array( 'placeholder' =>'Find your school'))}}
			<small class="error">Enter your school name</small>
			</div>
		</div>
		<!--\\\\\\\\\\\\\\\\\\\\ High Schools New School Autocomplete ////////////////////-->

	<!-- edit course school -->
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			What was the course?
		</div>
	</div>
    
    <div class="row">
		<div class='small-12 large-4 column model_label_txt'>
			{{ Form::label('hsInfoSubject', 'My Subject', array('class' => 'hs_info_label')) }}
		</div>
		<div class='small-12 large-8 column' id="SubjectDropDown">
			{{ Form::select('hsInfoSubject', $subjects, '', array('id' => 'hsInfoSubject', 'required' => 'required')) }}
			<small class="error">Please select a subject</small>
		</div>
		<div class='large-3 show-for-large-only'>&nbsp;</div>
    </div>
    
	<!--//////////////////// HS COURSE SELECT  \\\\\\\\\\\\\\\\\\\\-->
    <div class="row">
		<div class='small-12 large-4 column'>
			{{ Form::label('hsInfoClassName', 'Class Name', array('class' => 'hs_info_label')) }}
		</div>
		<div class='small-12 large-8 column' id="ClassDropDown">
			{{ Form::select('hsInfoClassName', array(), '', array('id' => 'hsInfoClassName', 'required' => 'required')) }}
			<small class="error">Please select a class</small>
		</div>
		<!--
		<div class='large-3 show-for-large-only'>&nbsp;</div>
		-->
    </div>
	<!--\\\\\\\\\\\\\\\\\\\\ HS COURSE SELECT  ////////////////////-->
	<!--//////////////////// NEW CLASS TEXT BOX \\\\\\\\\\\\\\\\\\\\-->
	<div id='hs_info_new_class_row' class='row'>
		<div class='small-12 large-4 column'>
			{{ Form::label('hs_info_new_class', 'New Class', array('class' => 'hs_info_label')) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::text('hs_info_new_class', null, array('placeholder' => 'Course Name')) }}
			<small class='error'>Enter a course name</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ NEW CLASS TEXT BOX ////////////////////-->

	<!-- edit class level school -->
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			What was the class level?
		</div>
	</div>
	<div class='row'>
		<div class='column'>
			<div class='gradeoptsmall button tiny btn-toggler-selected' id="hslevelOpt1" onclick="SetClassLevel(1);">BASIC</div>
			<div class='gradeoptsmall button tiny btn-toggler' id="hslevelOpt2" onclick="SetClassLevel(2);">HONORS</div>
			<div class='gradeoptsmall button tiny btn-toggler' id="hslevelOpt3" onclick="SetClassLevel(3);">AP</div>
		</div>
	</div>


	<!-- edit class level school -->
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			How many units was this course? (optional)
		</div>
	</div>
	<div class='row'>
		<div class='small-4 column'>
			{{ Form::select('hsInfoUnits', array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6',), null, array('required') )}}
			<small class="error">Please choose an option</small>
		</div>
	</div>


	<!-- edit class level school -->
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			When did you take this course?
		</div>
	</div>

	<div class="row">
		<div class='small-12 large-4 column'>
			{{ Form::label('hsInfoEducationlevel', 'Education Level', array('class' => 'hs_info_label')) }}
		</div>
	    <div class='small-12 large-8 column'>
	    	{{ Form::select('hsInfoEducationlevel', array(
	    	'' => 'Choose',
	    	'Freshman' => 'Freshman',
	    	'Sophomore' => 'Sophomore',
	    	'Junior' => 'Junior',
	    	'Senior' => 'Senior',
			), null, array('required')) }}
			<small class="error">Please choose an option</small>
		</div>
	    <div class='large-3 show-for-large-only'>&nbsp;</div>
    </div>
    
    <div class="row">
		<div class='small-12 large-4 column'>
			{{ Form::label('hsInfoSemster', 'Academic Term', array('class' => 'hs_info_label')) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::select(
				'hsInfoSemster',
				array('' => 'Choose',
					'Quarter' => array(
						'Quarter 1' => 'Quarter 1',
						'Quarter 2' => 'Quarter 2',
						'Quarter 3' => 'Quarter 3',
						'Quarter 4' => 'Quarter 4'
					),
					'Trimester' => array(
						'Trimester 1' => 'Trimester 1',
						'Trimester 2' => 'Trimester 2',
						'Trimester 3' => 'Trimester 3'
					),
					'Semester' => array(
						'Semester 1' => 'Semester 1',
						'Semester 2' => 'Semester 2'
					),
					'Year' => array(
						'Year' => 'Year'
					)
				),
				null,
				array('required') 
				) 
			}}
			<small class="error">Please choose an option</small>
		</div>
	    <div class='large-3 show-for-large-only'>&nbsp;</div>
    </div>
    







    
	<!-- edit class level school -->
	<div class='row edit_course_sub_heading profile-hs-gradeReceived-head'>
		<div class='small-12 column'>
			Did you receive a grade? If so, what was it?
			<span class="changeGrade" id="hsChangeGrade" onclick="hsResetGradeSelection();">Change Grade Type</span>
		</div>
	</div>
	<!--
	<div class='row'>
	</div>
	-->
	


	<div class='row gradeGroup' id="hsGradeRow">
		<div class='column'>
			<div class='gradeopt button medium radius btn-toggler' id="hsGradeOpt1" onclick="SetGradeOpt(1,'A-F')"><span style="font-size:26px;">A-F</span><br /><span style="font-size:12px;">LETTER</span></div>
			<div class='gradeopt button medium radius btn-toggler' id="hsGradeOpt2" onclick="SetGradeOpt(2,'P/F')"><span style="font-size:26px;">P/F</span><br /><span style="font-size:12px;">PASS/FAIL</span></div>
			<div class='gradeopt button medium radius btn-toggler' id="hsGradeOpt3" onclick="SetGradeOpt(3,'0-100')"><span style="font-size:26px;">0-100</span><br /><span style="font-size:12px;">NUMBER</span></div>
			<div class='gradeopt button medium radius btn-toggler' id="hsGradeOpt4" onclick="SetGradeOpt(4,'W');setFP('W');"><span style="font-size:26px;">W</span><br /><span style="font-size:12px;">WITHDRAW</span></div>
			<div class='gradeopt button medium radius btn-toggler' id="hsGradeOpt5" onclick="SetGradeOpt(5,'In');setFP('In');"><span style="font-size:26px;">In</span><br /><span style="font-size:12px;">INCOMPLETE</span></div>
		</div>
        {{ Form::hidden('hsclassGrade', '',array('id' => 'hsclassGrade')) }}
         <!--<small class="error">Please Choose Grade Type.</small>-->
	</div>
    <div class='row gradeGroup' style="display:none;" id="hsSubOpt1">
		<div class='column'>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub1" onclick="SetSubGradeOpt(1,'A+')" data-grade="A+" >A<br />+</div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub2" onclick="SetSubGradeOpt(2,'A')" data-grade="A" >A<br /><br /></div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub3" onclick="SetSubGradeOpt(3,'A-')" data-grade="A-" >A<br />-</div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub4" onclick="SetSubGradeOpt(4,'B+')" data-grade="B+" >B<br />+</div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub5" onclick="SetSubGradeOpt(5,'B')" data-grade="B" >B<br /><br /></div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub6" onclick="SetSubGradeOpt(6,'B-')" data-grade="B-" >B<br />-</div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub7" onclick="SetSubGradeOpt(7,'C+')" data-grade="C+" >C<br />+</div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub8" onclick="SetSubGradeOpt(8,'C')" data-grade="C" >C<br /><br /></div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub9" onclick="SetSubGradeOpt(9,'C-')" data-grade="C-" >C<br />-</div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub10" onclick="SetSubGradeOpt(10,'D+')" data-grade="D+" >D<br />+</div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub11" onclick="SetSubGradeOpt(11,'D')" data-grade="D" >D<br /><br /></div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub12" onclick="SetSubGradeOpt(12,'D-')" data-grade="D-" >D<br />-</div>
			<div class='button tiny btn-toggler1' id="hsGradeOptSub13" onclick="SetSubGradeOpt(13,'F')" data-grade="F" >F<br /><br /></div>
		</div>
	</div>
    
    <div class='row gradeGroup' style="display:none;" id="hsSubOpt2">
		<div class='column passfail-marg'>
		<span class="edit-normal"><img src="../images/pass.png" id="hsGPass" onclick="setFP('Pass')" data-grade="pass" /></span>
        <span class="edit-normal"><img src="../images/fail.png" id="hsGFail" onclick="setFP('Fail')" data-grade="fail" /></span>
		</div>
	</div>
    

    
    <div class='row gradeGroup' style="display:none;margin: 20px 0px 27px;" id="hsSubOpt3">
	    <div id="slider-range-max"></div>       
	</div>


	<!-- grade calculator -->
	<div class="row convert-international-grades-link">
		<div class="column small-12">
			<a href="http://www.foreigncredits.com/Resources/Grade-Conversion/" target="_blank">How do I convert my international grades?</a>
		</div>
	</div>
    

    <div class='row newCourseButtons'>
		<div class="small-6 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class="small-6 column">
			{{ Form::submit('Save', array('class'=>'button btn-Save'))}}
		</div>
	</div>

	<div class='row editCourseButtons'>
		
		<div class='small-12 large-6 column text-center'>
			<div class='button btn-cancel btn-remove-school' onclick="hsremoveCourse();">Remove this course</div>
		</div>
		<div class="small-12 large-3 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class='small-12 large-3 column'>{{ Form::submit('Save', array('class'=>'button btn-Save'))}}</div>
	</div>




    {{ Form::close() }}
</div>
<!-- end hsEditCourse modal -->

















<div class='reveal-modal small remove_before_ajax' id="UploadTranscript" data-reveal>
	<div class='row'>
		<div class='small-12 column close_x'>
			<img src="/images/close-x.png" class="close-reveal-modal" style="float: right;"></img>
		</div>
	</div>
	{{ Form::open(array('url' => "ajax/profile/highschoolInfo/".NULL , 'method' => 'POST', 'id' => 'uploadTranscriptForm','enctype'=>'multipart/form-data','name'=>'uploadTranscriptForm', 'data-abide' => 'ajax')) }}
	{{ Form::hidden('ajaxtoken',NULL , array()) }}
	{{ Form::hidden('postType', 'transcriptupload',array()) }}
	{{ Form::hidden('TranscriptPath', null , array('id'=>'TranscriptPath')) }}
	<div class="row">
		<div class="large-2 columns"><img src="../images/no-photo.png" alt="No Photo" /></div>
		<div class="large-10 columns">
			<div class="row">
				<div class="large-12 columns">
					{{ Form::label('transcript', 'Upload a transcript', array( 'class' => 'upload-title' )) }}
				</div>
				<div class="large-12 columns">
					{{ Form::file('transcript', array( 'required', 'pattern' => 'file_types' )) }}
					<small class='error'>Accepted formats: .jpg, png, gif, bmp, doc, docx, or pdf</small>
				</div>
			</div>
		</div>
	</div>
	<div class="row transcript-blue">
		<div class="large-12 columns">
			The transcript does NOT take the place of entering your grades.  It is used for college counsellors to verify the grades you enter.
		</div>
	</div>
    <div class="row" id="UploadButtonHs2">
		<div class="small-6 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class='small-6 column'>
			{{ Form::submit('Save', array('class' => 'button btn-Save')) }}
		</div>
    </div>
{{ Form::close() }}
</div>

<div class='editmode4'>
	<h2>High School Info</h2>
	<h3>Edit your Classes</h3>
	<hr>

	<div class='row' style='color:green;'>
		<div class='small-3 column'>Freshman</div>
		<div class='small-3 column'>9th grade</div>
		<div class='small-3 column' onclick="switchProfileBoxToEdit(this, 4);" style='color:blue;cursor:pointer;text-decoration:underline;'></div>
		<div class='small-3 column'>2011 - 2012</div>
	</div>
	<div class='row'>
		<div class='small-12 column'>
			<h4>1st Semester</h4>
		</div>
	</div>

	<div class='row'>
		<div class='small-12 column panel'>
			&lt; These are dynamic &gt;<br/><br/>
			<div class='row'>
				<div class='small-2 column'>Class Type</div>
				<div class='small-4 column'>Class Name</div>
				<div class='small-3 column'>Class Level</div>
				<div class='small-2 column'>Grade</div>
				<div class='small-1 column'></div>
			</div>
			<br/>

			<div class='row'>
				<div class='small-2 column'>{{ Form::select('hsInfoCoursesType1', array('Math'))}}</div>
				<div class='small-4 column'>{{ Form::select('hsInfoCoursesName1', array('Pre-Algebra'))}}</div>
				<div class='small-3 column'>{{ Form::select('hsInfoCoursesLevel1', array('Honors'))}}</div>
				<div class='small-2 column'>{{ Form::select('hsInfoCoursesGrade1', array('A'))}}</div>
				<div class='small-1 column'>X</div>
			</div>
			&lt; End of dynamic &gt;<br/><br/>
			<div class='row'>
				<div class="small-6 columns">
					<div class='button tiny'>add a class to this semester</div>
				</div>
			</div>

		</div>

		
	</div>

	<br/><br/>
	<div class='row'>
		<div class='large-offset-5 large-2 column'>
			<div class='cancelButton' onclick='loadProfileInfo("highschoolInfo");'>Cancel</div>
		</div>
		<div class='large-2 column'><div class='savebutton'>Save</div></div>
		<div class='large-3 column'><div class='saveContinueButton'>Save &amp; continue</div></div>
	</div>
</div>








<!-- Now in Upload Center
<div id="transcript-preview-modal" class="reveal-modal" data-reveal>
	<div class="row">
		<div class="column small-12 small-text-right">
			<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
		</div>
	</div>
	<div class="row">
		<div class="column small-12 small-text-center transcript_preview_img">
		</div>
	</div>
</div>-->









<script>
	//reload zurb items.
	//$(document).foundation();

	/***********************************************************************
	 *==========================ADD/EDIT COURSES ==========================
	 ***********************************************************************
	 * Bind the .change event to the schools attended dropdown. This
	 * shows the autocomplete row if the dropdown's value is 'new', and hides
	 * the autocomplete if anything else. Then it adds/removes validation from
	 * the autocomplete, and re-inits foundation.
	 */
	init_autocomp_toggle( 
		'#hs_info_hs_attended',			// schools attended dropdown
		'#hs_info_new_school_row',		// autocomplete row
		'#hs_info_new_school',			// autocomplete element
		'high_school_info'				// profile section's custom fndtn
	);
	/* Initializes autocomplete on the edit school modal
	 */
	make_school_autocomp( 
		'#hs_info_new_school',			// autocomplete element
		'#hsSchoolId',					// hidden school id input element
		'highschool',					// school type
		true 							// show unverified results if true
	);
	/***********************************************************************/

	/***********************************************************************
	 *========================= CHANGE/EDIT SCHOOL ========================
	 ***********************************************************************
	 * Bind the .change event to the schools attended dropdown. This
	 * shows the autocomplete row if the dropdown's value is 'new', and hides
	 * the autocomplete if anything else. Then it adds/removes validation from
	 * the autocomplete, and re-inits foundation.
	 */
	init_autocomp_toggle( 
		'#hs_info_change_hs_attended',			// schools attended dropdown
		'#hs_info_change_new_school_row',		// autocomplete row
		'#hs_info_change_new_school',			// autocomplete element
		'high_school_info'						// profile section's custom fndtn
	);
	/* Initializes autocomplete on the edit school modal
	 */
	make_school_autocomp( 
		'#hs_info_change_new_school',			// autocomplete element
		'#hsSchoolPickedId',					// hidden school id input element
		'highschool',							// school type
		true 									// show unverified results if true
	);
	/***********************************************************************/

	/***********************************************************************
	 *==================== ADD/EDIT COURSE SUBJECT BINDS===================
	 ***********************************************************************
	 * Binds a .change event to subject dropdown to trigger getClasses, which makes
	 * an ajax call and injects dropdown options into class dropdown
	 */
	init_subject_ajax(
		'#hsInfoSubject',			// Subject dropdown element
		'#hsInfoClassName'			// Class dropdown element
	);

	/***********************************************************************/

	/***********************************************************************
	 * ==================== SHOW/HIDE ADD CUSTOM CLASS ====================
	 ***********************************************************************
	 * Bind .change event to the courses dropdown in the add/edit courses dropdown
	 * modal. This shows and hides the new custom course text box.
	 */
	init_add_course_toggle(
		'#hsInfoClassName',					// name of the select element
		'#hs_info_new_class_row',			// name of the row to be toggled
		'#hs_info_new_class',				// text input field (for foundation)
		'high_school_info'					// profile section

	);
	/***********************************************************************/

	/***********************************************************************
	 *==================== UPLOAD TRANSCRIPT CLICK BIND ===================
	 ***********************************************************************
	 * Binds a .click event to the 'add a transcript' button. This is needed because
	 * the foundation reveal modal appears at the top of the page, out of view of the
	 * user. My solution is to bind the click event, then run a scrolltop and open
	 * the modal manually without a callback.
	 */
	bind_transcript_reveal(
		'#hs_add_transcript',			// id of element to be bound
		'#UploadTranscript'				// id of reveal modal element
	);
	/***********************************************************************/

	/***********************************************************************
	 *=================== BIND TRANSCRIPT SUBMIT EVENT ====================
	 ***********************************************************************
	 * Bind a .submit event which fires an AJAX call.
	 */
	bind_transcript_submit(
		'#uploadTranscriptForm',			// The id of the transcript form
		'#UploadTranscript',				// The id of the reveal modal
		'highschool',						// school type
		"{{ NULL }}"			// ajax token
	);
	/***********************************************************************/

	/***********************************************************************
	 *=============== BIND TRANSCRIPT SHOW CONFIRM CLICK EVENT =============
	 ***********************************************************************
	 * Bind a click event to the 'X' button to show the confirm-delete row
	 */
	bind_transcript_show_confirm(
		'.remove_transcript',			// Class of the element that uses the close-x image
		'#transcript_confirm_row_'		// unfinished ID prefix for a transcript row,
										// suffixed by the transcript's db ID
	);
	/***********************************************************************/

	/***********************************************************************
	 *============== BIND TRANSCRIPT HIDE CONFIRM CLICK EVENT ==============
	 ***********************************************************************
	 * Bind a click event to the cancel button to hide the confirm-delete row
	 */
	bind_transcript_hide_confirm(
		'.transcript_del_cancel',			// class of the cancel button
		'#transcript_confirm_row_'			// unifinished ID prefix for a transcript row,
											// suffixed by the transcript's db ID
	);
	/***********************************************************************/

	/***********************************************************************
	 *================= BIND TRANSCRIPT DELETE CLICK EVENT =================
	 ***********************************************************************
	 * Bind a click event to the 'X' button for remove transcripts
	 */
	bind_transcript_delete(
		'.transcript_del_confirm',			// Class of the confirm/delete button
		'#TranscriptHsRow_',				// ID prefix of transcript's row
		'highschool',						// schooltype
		"{{ NULL }}"			// AJAX token
	);
	/***********************************************************************/

	$( "#slider-range-max" ).slider({
		range: "max",
		min: 0,
		max: 100,
		value: 0,
		slide: function( event, ui ) {
			$( ".ui-slider-handle" ).html( ui.value );
			$( "#hsclassGradeSub" ).val( ui.value );
		}
	});

	$( ".ui-slider-handle" ).html( $( "#slider-range-max" ).slider( "value" ) );

	function SetClassLevel(optNum)
	{
		document.getElementById('hsclassLevel').value=optNum;
		for(var i=1;i<=3;i++)
		{
			if(i==optNum){
			document.getElementById('hslevelOpt'+i).className="gradeoptsmall button tiny btn-toggler-selected";	
			}
			else
			{
			document.getElementById('hslevelOpt'+i).className="gradeoptsmall button tiny btn-toggler";	
			}
		}
	}

	function SetGradeOpt(optNum,val)
	{
		document.getElementById('hsclassGrade').value=val;
		for(var i=1;i<=5;i++)
		{
			if(i==optNum)
			{
			document.getElementById('hsGradeOpt'+i).className="gradeopt button medium radius btn-toggler-selected";	
			}
			else
			{
			document.getElementById('hsGradeOpt'+i).className="gradeopt button medium radius btn-toggler";	
			}
			if(i<=3)
			{
				if(i==optNum)
				{
					document.getElementById('hsSubOpt'+i).style.display="block";
					document.getElementById('hsChangeGrade').style.display="block";
					document.getElementById('hsGradeRow').style.display="none";
				}
				else
				{
					document.getElementById('hsSubOpt'+i).style.display="none";	
				}
			}
		}
	}

	function SetSubGradeOpt(optNum,val){
		document.getElementById('hsclassGradeSub').value=val;

		for(var i=1;i<=13;i++)
		{	
			if(i==optNum)
			{
			document.getElementById('hsGradeOptSub'+i).className="button tiny btn-toggler1-selected";	
			}
			else
			{
			document.getElementById('hsGradeOptSub'+i).className="button tiny btn-toggler1";	
			}
		}
	}

	function setFP(val)
	{
		document.getElementById('hsclassGradeSub').value=val;
		if(val=='Pass')
		{
			document.getElementById('hsGPass').src='../images/pass_hover.png';
			document.getElementById('hsGFail').src='../images/fail.png';
		}
		else
		{
			document.getElementById('hsGFail').src='../images/fail_hover.png';
			document.getElementById('hsGPass').src='../images/pass.png';
		}
	}
	function hsResetGradeSelection()
	{
		document.getElementById('hsclassGrade').value='';
		document.getElementById('hsclassGradeSub').value='';
		document.getElementById('hsChangeGrade').style.display="none";
		for(var i=1;i<=5;i++)
		{
			document.getElementById('hsGradeOpt'+i).className="gradeopt button medium radius btn-toggler";
			if(i<=3)
			{
				document.getElementById('hsSubOpt'+i).style.display="none";
			}
		}
		document.getElementById('hsGradeRow').style.display="block";
	}
</script>
