<script src="../dropzone/dropzone.js?7"></script>
<script type="text/javascript" src="../js/jquery.form.min.js?7"></script>
<script type="text/javascript">
//reload zurb items.
function init_college_info_fndtn(){
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
	collegePostSchool();
	collegePostCourse();
});

$(document).ready(function() {   

	init_college_info_fndtn();
}); 

function showLoadingCol()
{
	var transcriptFile=document.uploadTranscriptFormCol.transcript.value;
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
			$('#UploadButtonCol1').html('<span class="headColData">Upload in Progress, Please Wait...</span>');
			$('#UploadButtonCol2').html('<span class="headColData">Upload in Progress, Please Wait...</span>');
		}
	}else{
	return false;
	}	
}
</script>
<!-- college View -->
<div class='viewmode' style='display:block;'>
	<!-- High School Area -->
    <div class="col-icon-course">&nbsp;</div>
    <h2 class="highschoolHead">College Info</h2>
    <span class="requiredtxt">* Recruiters will NEED this information in order to contact you.</span>
    <br /><br />



	<div class='row' style='padding-bottom: 15px; border-bottom: 1px solid rgb(185, 186, 187); margin-bottom: 10px;'>
		<div class='small-4 large-7 column progress-div '>My Schools</div>
		<div class='small-4 large-2 column schoolThead'>Total Courses</div>
		<div class='small-3 large-2 column schoolThead'>Total Units</div>
		<div class='small-1 large-1 column'>&nbsp;</div>
	</div>
	@if (!$collegeSchoolInfo)
		<div class='row'>
			<div class='column'>
				<div>A school can be added by clicking on add a course below</div>
			</div>
		</div>
	@endif

    @if(isset($collegeSchoolInfo))
	    @foreach ($collegeSchoolInfo as $hsSchoolInfo)
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
					<div class='edit_pencil' href="#" onclick='collegeEditSchool(this);' data-college-info='{{ htmlspecialchars(  json_encode($hsSchoolInfo), ENT_QUOTES ) }}'><img src="../images/edit_pencil.png" border="0" style="cursor:pointer;" /></div>
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
			<a class="highschoolAdd" onclick='collegeAddNewCourse(this);'> + add a new course</a>
		</div>
		<div class='large-5 column highschoolWhotxt'>
			<!--Who can see this? <a href="#" style="color:#98D0EE !important;">Only you</a>-->
		</div>
    </div>
	<hr>
	<!-- End Course Title Area -->

	@if (!$collegeSchoolYears)
		<div class='row'>
			<div class='column'>
				<div>You haven’t added any courses yet</div>
			</div>
		</div>
	@endif


	<!-- Freshman section -->
	@if(isset($collegeSchoolYears['Freshman']))
		<div class='row green_head'>
			<div class='small-3 column'><img src="../images/icon-arrow-up.png" border="0" align="absmiddle" />&nbsp;Freshman</div>
			<div class='small-9 column'></div>
		</div><br/>
		@foreach ($collegeSchoolYears['Freshman'] as $key => $semester)
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
					<?php $classLevel;?>
						@if ($course['class_level'] == 1)
                        <?php $classLevel = 'Basic'; ?>
						@elseif($course['class_level'] == 2)
                        <?php $classLevel = 'Honors'; ?>
                        @else
                        <?php $classLevel = 'AP'; ?>
                        @endif
					<div class='small-3 column'>{{$classLevel}}</div>
					<div class='small-2 column'>{{{$course['course_grade'] or '--'}}}</div>
					<div class='small-1 column edit_pencil'>
			        	<img data-college-info='{{ htmlspecialchars( json_encode($course), ENT_QUOTES) }}' src="../images/edit_pencil.png" border="0" style="cursor:pointer;" onclick='collegeEditCourse(this);'/>
			        </div>
				</div>
			@endforeach
		@endforeach
	@endif
	<!-- End Freshman section -->
<br />
<div class="clearfix"></div>
	<!-- Sophomore section -->
	@if(isset($collegeSchoolYears['Sophomore']))
		<div class='row green_head'>
			<div class='small-3 column'><img src="../images/icon-arrow-up.png" border="0" align="absmiddle" />&nbsp;Sophomore</div>
			<div class='small-9 column'></div>
		</div><br/>
		@foreach ($collegeSchoolYears['Sophomore'] as $key => $semester)
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
                    
					<?php $classLevel;?>
					@if ($course['class_level'] == 1)
                    	<?php $classLevel = 'Basic'; ?>
					@elseif($course['class_level'] == 2)
                    	<?php $classLevel = 'Honors'; ?>
                    @else
                    	<?php $classLevel = 'AP'; ?>
                    @endif
                    
					<div class='small-3 column'>{{$classLevel}}</div>
					<div class='small-2 column'>{{{$course['course_grade'] or '--'}}}</div>
					<div class='small-1 column edit_pencil'>
			        	<img data-college-info='{{ htmlspecialchars( json_encode($course), ENT_QUOTES ) }}' src="../images/edit_pencil.png" border="0" style="cursor:pointer;" onclick='collegeEditCourse(this);'/>
			        </div>
				</div>
			@endforeach
		@endforeach
	@endif
	<!-- End Sophomore section -->
<br />
<div class="clearfix"></div>
	<!-- Junior section -->
	@if(isset($collegeSchoolYears['Junior']))
		<div class='row green_head'>
			<div class='small-3 column'><img src="../images/icon-arrow-up.png" border="0" align="absmiddle" />&nbsp;Junior</div>
			<div class='small-9 column'></div>
		</div><br/>
		@foreach ($collegeSchoolYears['Junior'] as $key => $semester)
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
					<div class='small-6 large-3 column'>{{$course['subject']}}</div>
					<div class='small-6 large-3 column'>
                    @if($course['custom_class_name']!="NULL" && $course['custom_class_name']!="")					
					{{ $course['custom_class_name']}}
					@else					
					{{ $course['clName']}}
					@endif
                    </div>
					<?php $classLevel;?>
						@if ($course['class_level'] == 1)
                        <?php $classLevel = 'Basic'; ?>
						@elseif($course['class_level'] == 2)
                        <?php $classLevel = 'Honors'; ?>
                        @else
                        <?php $classLevel = 'AP'; ?>
                        @endif
					<div class='small-6 large-3 column'>{{$classLevel}}</div>
					<div class='small-5 large-2 column'>{{{$course['course_grade'] or '--'}}}</div>
					<div class='small-1 large-1 column edit_pencil'>
			        	<img data-college-info='{{ htmlspecialchars(  json_encode($course), ENT_QUOTES ) }}' src="../images/edit_pencil.png" border="0" style="cursor:pointer;" onclick='collegeEditCourse(this);'/>
			        </div>
				</div>
			@endforeach
		@endforeach
	@endif
	<!-- End Junior section -->
<br />
<div class="clearfix"></div>
	<!-- Senior section -->
	@if(isset($collegeSchoolYears['Senior']))
		<div class='row green_head'>
			<div class='small-5 large-3 column'><img src="../images/icon-arrow-up.png" border="0" align="absmiddle" />&nbsp;Senior</div>
			<div class='small-7 large-9 column'></div>
		</div><br/>
		@foreach ($collegeSchoolYears['Senior'] as $key => $semester)
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
					<?php $classLevel;?>
						@if ($course['class_level'] == 1)
                        <?php $classLevel = 'Basic'; ?>
						@elseif($course['class_level'] == 2)
                        <?php $classLevel = 'Honors'; ?>
                        @else
                        <?php $classLevel = 'AP'; ?>
                        @endif
					<div class='small-3 column'>{{$classLevel}}</div>
					<div class='small-2 column'>{{{$course['course_grade'] or '--'}}}</div>
					<div class='small-1 column edit_pencil'>
			        	<img data-college-info='{{ htmlspecialchars(  json_encode($course), ENT_QUOTES ) }}' src="../images/edit_pencil.png" border="0" style="cursor:pointer;" onclick='collegeEditCourse(this);'/>
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
			<a id='col_add_transcript' class="highschoolAdd">+ add a transcript</a>
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
		<div class='row collapse transcript_row' id="TranscriptColRow_{{$tData->id}}">
			<div class='small-12 column'>
				<div class='row'>
					<div class='small-6 column headColData' style="word-wrap:break-word;">
						{{$tData->transcript_name}}
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
<!-- End college View -->













<!-- collegeEditSchool modal -->
<div class='reveal-modal small row remove_before_ajax' id="collegeEditSchool" data-reveal>
	{{ Form::open(array('url' => "ajax/profile/collegeInfo/". NULL , 'method' => 'POST', 'id' => 'collegeInfoForm','data-abide'=>'ajax')) }}
	{{ Form::hidden('ajaxtoken', NULL , array()) }}
	{{ Form::hidden('postType', '', array()) }}
	{{ Form::hidden('originalCollegeId', null, array()) }}
	{{ Form::hidden('CollegePickedId', null , array('id'=>'CollegePickedId')) }}
	<div class='row'>
		<div class='small-12 column edit_school_heading'>
			Edit School
		</div>
	</div>
	<hr>
	<!--//////////////////// Change Colleges Attended \\\\\\\\\\\\\\\\\\\\-->
	<div class='row'>
		<div class='small-12 large-4 column'>
			{{ Form::label('col_info_change_col_attended', 'School Name', array('class' => 'edit_school_label')) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::select('col_info_change_col_attended', $schools_attended['colleges'], $user['current_college_id'], array('required')) }}
			<small class='error'>Select an option</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Change Colleges Attended ////////////////////-->
	<!--//////////////////// Change Colleges AutoComplete \\\\\\\\\\\\\\\\\\\\-->
	<div id='col_info_change_new_school_row' class='row' style='display: none;'>
		<div class='small-12 large-4 column ui-front'>
			{{ Form::label('col_info_change_new_school', 'Add School', array('class' => 'edit_school_label')) }}
		</div>
		<div id='col_info_change_new_school_container' class='small=12 large-8 column'>
			{{ Form::text('col_info_change_new_school', null, array( 'placeholder' => 'search for a school' )) }}
			<small class='error'>Search for a school or enter your own</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Change Colleges AutoComplete ////////////////////-->
	<!--//////////////////// Change College Current School \\\\\\\\\\\\\\\\\\\\-->
	<div class='row'>
		<div class="small-12 large-8 large-offset-4 columns model_label_txt">
			{{ Form::checkbox('collegeInfoSchoolCurrent', '1', null, array( 'id' => 'collegeInfoSchoolCurrent' ))}}
			{{ Form::label( 'collegeInfoSchoolCurrent', 'My Current School', array('class' => 'edit_school_label') ) }}
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Change College Current School ////////////////////-->
	<!--//////////////////// College Warning \\\\\\\\\\\\\\\\\\\\-->
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
	<!--\\\\\\\\\\\\\\\\\\\\ College Warning ////////////////////-->

    <div class='row'>
		
		<div class='small-12 large-6 column text-center'>
			<div class='button btn-cancel btn-remove-school' onclick="collegeremoveSchool(this);">Remove this school</div>
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
<!-- end collegeEditSchool modal -->














<!-- collegeEditCourse modal -->
<div class='reveal-modal small remove_before_ajax' id="collegeEditCourse" data-reveal data-default-college='{{ htmlspecialchars( json_encode($currentSchool), ENT_QUOTES ) }}' >
	<!-- We save current school info in the modal so we can allways reset it since its not in the DOM. --> 
	{{ Form::open(array('url' => "ajax/profile/collegeInfo/". NULL , 'method' => 'POST', 'id' => 'collegeInfoCourseForm','data-abide'=>'ajax')) }}
	{{ Form::hidden('postType', 'newandEditCourse',array()) }}

	@if (isset($currentSchool['id']))
		{{ Form::hidden('collegeSchoolId', $currentSchool['id'] , array('id'=>'collegeSchoolId')) }}
	@else
		{{ Form::hidden('collegeSchoolId', null , array('id'=>'collegeSchoolId')) }}

		@endif
	{{ Form::hidden('courseId', null ,array()) }}
    {{ Form::hidden('classLevel', '1',array('id' => 'classLevel')) }}
    {{ Form::hidden('classGradeSub', '0',array('id' => 'classGradeSub')) }} 
    {{ Form::hidden('CustomClassCol', '0',array('id' => 'CustomClassCol')) }} 
	<div class="col-icon-course">&nbsp;</div>
    <h2 style="float:left;">College Info</h2>
	<hr>
	
	<!-- Edit school -->
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			Add a course at:
		</div>
	</div>

	<!--//////////////////// Colleges Attended Dropdown \\\\\\\\\\\\\\\\\\\\-->
	<div class='row'>
		<div class='small-12 large-4 column'>
			{{ Form::label('col_info_col_attended', 'School Name', array('class' => 'col_info_label')) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::select('col_info_col_attended', $schools_attended['colleges'], $user['current_college_id'], array('required')) }}
		<small class="error">Select your high school, or select 'search for another'...</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Colleges Attended Dropdown ////////////////////-->
	<!--//////////////////// Colleges New School Autocomplete \\\\\\\\\\\\\\\\\\\\-->
	<div id='col_info_new_school_row' class='row' style='display: none;'>
		<div class='small-12 large-4 column'>
			{{ Form::label('col_info_new_school', 'Add a school', array('class' => 'col_info_label')) }}
		</div>
		<div id='col_info_new_school_container' class='small-12 large-8 column ui-front'>
		{{ Form::text('col_info_new_school', '', array( 'placeholder' =>'Find your school')) }}
		<small class="error">Enter your school name</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Colleges New School Autocomplete ////////////////////-->

	<!-- edit course school -->
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			What was the course?
		</div>
	</div>
    
	<!--//////////////////// COLLEGE SUBJECT DROPDOWN \\\\\\\\\\\\\\\\\\\\-->
	<div class='row'>
		<div class='small-12 large-4 column'>
			{{ Form::label( 'collegeInfoSubject', 'My Subject', array('class' => 'edit_school_label') ) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::select( 'collegeInfoSubject', $subjects, null, array('id' => 'collegeInfoSubject', 'required')) }}
			<small class='error'>Select a subject</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ COLLEGE SUBJECT DROPDOWN ////////////////////-->

	<!--//////////////////// COLLEGE CLASS DROPDOWN \\\\\\\\\\\\\\\\\\\\-->
	<div class='row'>
		<div class='small-12 large-4 column'>
			{{ Form::label('collegeInfoClassName', 'Class Name', array('class' => 'edit_school_label')) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::select('collegeInfoClassName', array(), null, array('id' => 'collegeInfoClassName', 'required')) }}
			<small class='error'>Select a class, or add your own</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ COLLEGE CLASS DROPDOWN ////////////////////-->

	<!--//////////////////// NEW CLASS TEXT BOX \\\\\\\\\\\\\\\\\\\\-->
	<div id='col_info_new_class_row' class='row'>
		<div class='small-12 large-4 column'>
			{{ Form::label('col_info_new_class', 'New Class', array('class' => 'col_info_label')) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::text('col_info_new_class', null, array('placeholder' => 'Course Name')) }}
			<small class='error'>Enter a class name</small>
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
			<div class='gradeoptsmall button tiny btn-toggler-selected' id="levelOpt1" onclick="CollegeSetClassLevel(1);">BASIC</div>
			<div class='gradeoptsmall button tiny btn-toggler' id="levelOpt2" onclick="CollegeSetClassLevel(2);">HONORS</div>
			<div class='gradeoptsmall button tiny btn-toggler' id="levelOpt3" onclick="CollegeSetClassLevel(3);">AP</div>
		</div>
	</div>


	<!-- edit class level school -->
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			How many units was this course? (optional)
		</div>
	</div>

	<div class='row'>
		<div class='small-3 column'>
			{{ Form::select('collegeInfoUnits', array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6',), null, array('required') )}}
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
			{{ Form::label('collegeInfoEducationlevel', 'Education Level', array('class' => 'edit_school_label')) }}
		</div>
	    <div class='small-12 large-8 column'>
	    	{{ Form::select('collegeInfoEducationlevel', array(
	    	'' => 'Choose',
	    	'Freshman' => 'Freshman',
	    	'Sophomore' => 'Sophomore',
	    	'Junior' => 'Junior',
	    	'Senior' => 'Senior'
	    	), null, array('required') ) }}
            <small class="error">Please choose an option</small>
          </div>
    </div>
    
    <div class="row">
		<div class='small-12 large-4 column'>
			{{ Form::label('collegeInfoSemster', 'Academic Term', array('class' => 'edit_school_label')) }}
		</div>
		<div class='small-12 large-8 column'>
			{{ Form::select(
				'collegeInfoSemster',
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
	<div class='row'>
		<div class='small-12 column edit_course_sub_heading'>
			Did you receive a grade? If so, what was it?
			<span class="changeGrade" id="ChangeGrade" onclick="CollegeResetGradeSelection();">Change Grade Type</span>
		</div>
	</div>
	<div class='row'>
	</div>
	


	<div class='row gradeGroup' id="GradeRow">
		<div class='column'>
			<div class='gradeopt button medium radius btn-toggler' id="GradeOpt1" onclick="CollegeSetGradeOpt(1,'A-F')"><span style="font-size:26px;">A-F</span><br /><span style="font-size:12px;">LETTER</span></div>
			<div class='gradeopt button medium radius btn-toggler' id="GradeOpt2" onclick="CollegeSetGradeOpt(2,'P/F')"><span style="font-size:26px;">P/F</span><br /><span style="font-size:12px;">PASS/FAIL</span></div>
			<div class='gradeopt button medium radius btn-toggler' id="GradeOpt3" onclick="CollegeSetGradeOpt(3,'0-100')"><span style="font-size:26px;">0-100</span><br /><span style="font-size:12px;">NUMBER</span></div>
			<div class='gradeopt button medium radius btn-toggler' id="GradeOpt4" onclick="CollegeSetGradeOpt(4,'W');CollegesetFP('W');"><span style="font-size:26px;">W</span><br /><span style="font-size:12px;">WITHDRAW</span></div>
			<div class='gradeopt button medium radius btn-toggler' id="GradeOpt5" onclick="CollegeSetGradeOpt(5,'In');CollegesetFP('In');"><span style="font-size:26px;">In</span><br /><span style="font-size:12px;">INCOMPLETE</span></div>
		</div>
         {{ Form::hidden('classGrade', '',array('id' => 'classGrade')) }}
         <!--<small class="error">Please Choose Grade Type.</small>-->
         
	</div>
    <div class='row gradeGroup' style="display:none;" id="SubOpt1">
		<div class='column'>
			<div class='button tiny btn-toggler1' id="GradeOptSub1" onclick="CollegeSetSubGradeOpt(1,'A+')" data-grade="A+" >A<br />+</div>
			<div class='button tiny btn-toggler1' id="GradeOptSub2" onclick="CollegeSetSubGradeOpt(2,'A')" data-grade="A" >A<br /><br /></div>
			<div class='button tiny btn-toggler1' id="GradeOptSub3" onclick="CollegeSetSubGradeOpt(3,'A-')" data-grade="A-" >A<br />-</div>
			<div class='button tiny btn-toggler1' id="GradeOptSub4" onclick="CollegeSetSubGradeOpt(4,'B+')" data-grade="B+" >B<br />+</div>
			<div class='button tiny btn-toggler1' id="GradeOptSub5" onclick="CollegeSetSubGradeOpt(5,'B')" data-grade="B" >B<br /><br /></div>
			<div class='button tiny btn-toggler1' id="GradeOptSub6" onclick="CollegeSetSubGradeOpt(6,'B-')" data-grade="B-" >B<br />-</div>
			<div class='button tiny btn-toggler1' id="GradeOptSub7" onclick="CollegeSetSubGradeOpt(7,'C+')" data-grade="C+" >C<br />+</div>
			<div class='button tiny btn-toggler1' id="GradeOptSub8" onclick="CollegeSetSubGradeOpt(8,'C')" data-grade="C" >C<br /><br /></div>
			<div class='button tiny btn-toggler1' id="GradeOptSub9" onclick="CollegeSetSubGradeOpt(9,'C-')" data-grade="C-" >C<br />-</div>
			<div class='button tiny btn-toggler1' id="GradeOptSub10" onclick="CollegeSetSubGradeOpt(10,'D+')" data-grade="D+" >D<br />+</div>
			<div class='button tiny btn-toggler1' id="GradeOptSub11" onclick="CollegeSetSubGradeOpt(11,'D')" data-grade="D" >D<br /><br /></div>
			<div class='button tiny btn-toggler1' id="GradeOptSub12" onclick="CollegeSetSubGradeOpt(12,'D-')" data-grade="D-" >D<br />-</div>
			<div class='button tiny btn-toggler1' id="GradeOptSub13" onclick="CollegeSetSubGradeOpt(13,'F')" data-grade="F" >F<br /><br /></div>
		</div>
	</div>
    
    <div class='row gradeGroup' style="display:none;" id="SubOpt2">
		<div class='column passfail-marg'>
		<img src="../images/pass.png" border="0" id="GPass" onclick="CollegesetFP('Pass')" style="cursor:pointer;" data-grade="pass" />
        <img src="../images/fail.png" border="0" id="GFail" onclick="CollegesetFP('Fail')" style="cursor:pointer;" data-grade="fail" />
		</div>
	</div>
    

    
    <div class='row gradeGroup' style="display:none;margin: 20px 0px 27px;" id="SubOpt3">
	    <div id="slider-range-max-col"></div>       
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
		<div class='small-6 column'>
			{{ Form::submit('Add', array('class'=>'button btn-Save'))}}
		</div>
	</div>

	<div class='row editCourseButtons'>
		
		<div class='small-12 large-6 column text-center'>
			<div class='button btn-cancel btn-remove-school' onclick="collegeremoveCourse();">Remove this course</div>
		</div>
		<div class="small-12 large-3 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class='small-12 large-3 column'>
			{{ Form::submit('Save', array('class'=>'button btn-Save'))}}
		</div>

		<!--
		<div class="large-offset-5 large-1 small-12 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class='large-4 column text-center'>
			<div class='button btn-cancel btn-remove-school' onclick="collegeremoveCourse();">Remove this course</div>
		</div>
		<div class='large-2 column'>{{ Form::submit('Edit', array('class'=>'button btn-Save'))}}</div>
		-->
	</div>




    {{ Form::close() }}
</div>
<!-- end collegeEditCourse modal -->

















<div class='reveal-modal small remove_before_ajax' id="UploadTranscriptCol" data-reveal>
	<div class='row'>
		<div class='small-12 column close_x'>
			<img src="/images/close-x.png" class="close-reveal-modal" style="float: right;"></img>
		</div>
	</div>
	{{ Form::open(array('url' => "ajax/profile/collegeInfo/".NULL , 'method' => 'POST', 'id' => 'uploadTranscriptFormCol','enctype'=>'multipart/form-data','name'=>'uploadTranscriptFormCol', 'data-abide' => 'ajax')) }}
	{{ Form::hidden('ajaxtoken',NULL , array()) }}
	{{ Form::hidden('postType', 'transcriptupload',array()) }}
	{{ Form::hidden('TranscriptPathCol', null , array('id'=>'TranscriptPathCol')) }}
	<div class="row">
		<div class="large-2 columns">
			<img src="/images/no-photo.png" alt="No Photo" />
		</div>
		<div class="large-10 columns">
			<div class="row">
				<div class="large-12 columns">
					{{ Form::label('transcript', 'Upload a transcript', array( 'class' => 'upload-title' )) }}
				</div>
				<div class="large-12 columns">
					{{ Form::file('transcript', array( 'required' )) }}
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

    <div class="row" id="UploadButtonCol2">
		<div class="small-6 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class='small-6  column'>
			{{ Form::submit('Save', array('class' => 'button btn-Save')) }}
		</div>
    </div>
{{ Form::close() }}
</div>

<div class='editmode4'>
	<h2>College Info</h2>
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
			<div class='cancelButton' onclick='loadProfileInfo("collegeInfo");'>Cancel</div>
		</div>
		<div class='large-2 column'><div class='savebutton'>Save</div></div>
		<div class='large-3 column'><div class='saveContinueButton'>Save &amp; continue</div></div>
	</div>
</div>

<script type="text/javascript">
	function showhideNewClassCol()
	{
		if(document.getElementById('ShowNewClassCol').style.display=="none")
		{
			$("#collegeInfoClassName").removeAttr("required");
			document.getElementById('ShowNewClassCol').style.display="block";
			$("#CustomClassCol").val('1');
		}
		else
		{
			$("#collegeInfoClassName").attr("required","required");
			document.getElementById('ShowNewClassCol').style.display="none";
			$("#CustomClassCol").val('0');
		}
	}
	//reload zurb items.
	$(document).foundation();

	/***********************************************************************
	 *==========================ADD/EDIT COURSES ==========================
	 ***********************************************************************
	 * Bind the .change event to the schools attended dropdown. This
	 * shows the autocomplete row if the dropdown's value is 'new', and hides
	 * the autocomplete if anything else. Then it adds/removes validation from
	 * the autocomplete, and re-inits foundation.
	 */
	init_autocomp_toggle( 
		'#col_info_col_attended',			// schools attended dropdown
		'#col_info_new_school_row',			// autocomplete row
		'#col_info_new_school',				// autocomplete element
		'college_info'						// profile section's custom fndtn
	);
	/* Initializes autocomplete on the edit school modal
	 */
	make_school_autocomp( 
		'#col_info_new_school',				// autocomplete element
		'#collegeSchoolId',					// hidden school id input element
		'college',							// school type
		true 								// show unverified results if true
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
		'#col_info_change_col_attended',		// schools attended dropdown
		'#col_info_change_new_school_row',		// autocomplete row
		'#col_info_change_new_school',			// autocomplete element
		'college_info'							// profile section's custom fndtn
	);
	/* Initializes autocomplete on the edit school modal
	 */
	make_school_autocomp( 
		'#col_info_change_new_school',			// autocomplete element
		'#CollegePickedId',						// hidden school id input element
		'college',								// school type
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
		'#collegeInfoSubject',			// Subject dropdown element
		'#collegeInfoClassName'			// Class dropdown element
	);

	/***********************************************************************/

	/***********************************************************************
	 * ==================== SHOW/HIDE ADD CUSTOM CLASS ====================
	 ***********************************************************************
	 * Bind .change event to the courses dropdown in the add/edit courses dropdown
	 * modal. This shows and hides the new custom course text box.
	 */
	init_add_course_toggle(
		'#collegeInfoClassName',			// name of the select element
		'#col_info_new_class_row',			// name of the row to be toggled
		'#col_info_new_class',				// text input field (for foundation)
		'college_info'						// profile section

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
		'#col_add_transcript',			// id of element to be bound
		'#UploadTranscriptCol'			// id of reveal modal element
	);
	/***********************************************************************/

	/***********************************************************************
	 *=================== BIND TRANSCRIPT SUBMIT EVENT ====================
	 ***********************************************************************
	 * Bind a .submit event which fires an AJAX call.
	 */
	bind_transcript_submit(
		'#uploadTranscriptFormCol',			// The id of the transcript form
		'#UploadTranscriptCol',				// The id of the reveal modal
		'college',							// school type
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
		'#TranscriptColRow_',				// ID prefix of transcript's row
		'college',							// schooltype
		"{{ NULL }}"			// AJAX token
	);
	/***********************************************************************/

	$( "#slider-range-max-col" ).slider({
		range: "max",
		min: 0,
		max: 100,
		value: 0,
		slide: function( event, ui ) {
			$( ".ui-slider-handle" ).html( ui.value );
			$( "#classGradeSub" ).val( ui.value );
		}
	});

	$( ".ui-slider-handle" ).html( $( "#slider-range-max-col" ).slider( "value" ) );

	function CollegeSetClassLevel(optNum)
	{
		document.getElementById('classLevel').value=optNum;
		for(var i=1;i<=3;i++)
		{
			if(i==optNum){
			document.getElementById('levelOpt'+i).className="gradeoptsmall button tiny btn-toggler-selected";	
			}
			else
			{
			document.getElementById('levelOpt'+i).className="gradeoptsmall button tiny btn-toggler";	
			}
		}
	}
	function CollegeSetGradeOpt(optNum,val)
	{		
		document.getElementById('classGrade').value=val;
		for(var i=1;i<=5;i++)
		{
			if(i==optNum)
			{
			document.getElementById('GradeOpt'+i).className="gradeopt button medium radius btn-toggler-selected";	
			}
			else
			{
			document.getElementById('GradeOpt'+i).className="gradeopt button medium radius btn-toggler";	
			}
			if(i<=3)
			{
				if(i==optNum)
				{
					document.getElementById('SubOpt'+i).style.display="block";
					document.getElementById('ChangeGrade').style.display="block";
					document.getElementById('GradeRow').style.display="none";
				}
				else
				{
					document.getElementById('SubOpt'+i).style.display="none";	
				}
			}
		}		
	}
	function CollegeSetSubGradeOpt(optNum,val){
		document.getElementById('classGradeSub').value=val;

		for(var i=1;i<=13;i++)
		{	
			if(i==optNum)
			{
			document.getElementById('GradeOptSub'+i).className="button tiny btn-toggler1-selected";	
			}
			else
			{
			document.getElementById('GradeOptSub'+i).className="button tiny btn-toggler1";	
			}
		}
	}

	function CollegesetFP(val)
	{
		document.getElementById('classGradeSub').value=val;
		if(val=='Pass')
		{
			document.getElementById('GPass').src='../images/pass_hover.png';
			document.getElementById('GFail').src='../images/fail.png';
		}
		else
		{
			document.getElementById('GFail').src='../images/fail_hover.png';
			document.getElementById('GPass').src='../images/pass.png';
		}
	}
	function CollegeResetGradeSelection()
	{
		document.getElementById('classGrade').value='';
		document.getElementById('classGradeSub').value='';
		document.getElementById('ChangeGrade').style.display="none";
		for(var i=1;i<=5;i++)
		{
			document.getElementById('GradeOpt'+i).className="gradeopt button medium radius btn-toggler";
			if(i<=3)
			{
				document.getElementById('SubOpt'+i).style.display="none";
			}
		}
		document.getElementById('GradeRow').style.display="block";
	}

</script>
