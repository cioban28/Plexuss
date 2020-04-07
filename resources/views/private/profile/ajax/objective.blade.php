<?php

//dd($data);

?>


<script type="text/javascript">
//reload zurb items.
$(document).foundation();
$(function() {
PostObjectiveInfo();
});
</script>

<div class='viewmode' style='display:block;'>
    <h2 class="objective-title"><div class="dis-in-Block"><span class="text-center edit-icon"><img src="../images/icon-objective.png" alt=""/>&nbsp;Objective +5% to your profile status</div></h2>
    <div class='editInfo dis-in-Block edit-manage' onclick='editObjectiveContent(this);'>edit <span class="edit-icon"><img src="../images/edit_icon.png"/></span></div>
	<!--
    <div class="large-4 small-12 privacy-settings dis-in-Block">Who can see this? <a href="#">{{ @$user['whocansee'] }}</a></div>
	-->
    <hr>
    <div class="row collapse">
        <div class='large-11 small-12 column objective-title paddingleft0'>
        @if( isset($user['objId']) && $user['objId']>=1)
        	"I would like to get a/an 
        	<span class="obj_highlighted_txt">
        		{{ $user['degreename'] }}
        	</span> 
        

        	
        	<div id="majors_list_objective">
        	studying
	        	@if( count($user['major']) == 1)
	        		<span class="obj_highlighted_txt major-item">
	        			{{ $user['major'][0] }}
	        		</span>
	        		@endif
	        	@if( count($user['major']) == 2)
	        		<span class="obj_highlighted_txt major-item">
	        			{{ $user['major'][0] }}
	        		</span>
		        	&
		        	<span class="obj_highlighted_txt major-item">
		        		{{ $user['major'][1] }}
		        	</span>
		        @endif
	        	@if( count($user['major']) > 2) 
		        	@for($i=0; $i< count($user['major'])- 2; $i++)
		        		<span class="obj_highlighted_txt major-item">
		        			{{ $user['major'][$i] }}
		        		
		        		</span>, 
		        	@endfor
		        	<span class="obj_highlighted_txt major-item">
		        		{{ $user['major'][ count($user['major'])-2] }}
		        	</span>
		        	&
		        	<span class="obj_highlighted_txt major-item">
		        		{{ $user['major'][ count($user['major'])-1] }}
	        		</span>.
	        	@endif
	        </div>
        	

        	My dream would be to one day work as a(n) <span class="obj_highlighted_txt">{{ $user['profession'] }}</span>."
        @else
            "I would like to get a/an <span class="obj_highlighted_txt">Example Degree</span> in <span class="obj_highlighted_txt">Example Major</span>. My dream career would be working in the <span class="obj_highlighted_txt">Example Profession</span> field."
        @endif
        <br /><br />
        -{{ $user['fname'] }}&nbsp;{{ $user['lname'] }}
        </div>
    </div>
</div>
<br />
<br />

<div class="row">
	
	<div class="dis-in-Block custom-objective">Personal Objective</div>
    <div class='editInfo dis-in-Block edit-manage' onclick='editObjectiveContent(this);'>
    	edit
    	<span class="edit-icon"><img src="../images/edit_icon.png" alt=""/></span>
    </div>
	
    <br /><br />
    <div class="cust-objectives">
    @if(@$user['objId']>=1 && trim(@$user['obj_text'])!="")        
        {{ @$user['obj_text'] }}
    @else        
        No custom objective added.
    @endif    	
    </div>
    <hr>
</div>

<div class='reveal-modal medium remove_before_ajax' id="editObjectiveContent" data-reveal>
{{ Form::open(array('url' => "ajax/profile/objective/" , 'method' => 'POST', 'id' => 'objectiveForm', 'data-abide'=>'ajax')) }}
{{ csrf_field() }}
{{ Form::hidden('whocansee','Public',array()) }}
	<div class="row objective-outerrow">
        <div class='column small-12 viewmode' style='display:block;'>
        	<h2><div class="obj-title dis-in-Block"><span class="text-center edit-icon"><img src="../images/icon-objective.png" alt=""/></span>&nbsp;Objective +5% to your profile status</div></h2>
            <div class="large-3 small-6 privacy-settings dis-in-Block">
                <!--{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),@$user['whocansee'],array()) }}
				<small class="error">Please choose an option</small>-->
            </div>
            <div class="sub-2-italic">You can type &ldquo;undecided&rdquo; in the fields below if you are not sure yet about your objective.</div>
            <br /><br />

            <!-- objective form -->
            <div class="row" style="line-height:40px;">
            	
            	<!-- left side of form -->
            	<div class="column large-6 medium-6 small-12">

            		<!-- row for degree -->
            		<div class="row">
		            	<div class="column large-4 medium-4 small-12 objective-label">I would like to get a/an</div>
		            	<div class="column large-8 medium-8 small-12">
							<div style="" class="validSelecto degree-type-select">
								
								{{ Form::select('objDegree',array(),@$user['degree_type'],array('class'=>'objective-input ', 'id' => 'DegreesDropDown', 'required', 'style' => 'display: inline;')) }}
								<small class="error">Please choose an option</small>
							</div>
						</div>
					</div>

					<!-- row for major -->
					<div class="row">
						<div class="column large-4 medium-4 small-12">
							<div class="objective-label">I would like to study</div>
						</div>


						<div class="column large-8 medium-8 small-12">
							<small id="max-note">You've reached the maximum number of majors.</small>
							<div class="validSelecto" id="objMajorContainer">
								{{
									Form::text(
										'objMajor',
										isset($user['major'][0]) ? $user['major'][0] : null,
										array(
											'id' => 'objMajor',
											'class' => 'objective-input',
											 'data-selected' => isset($user['major'][0]) ? $user['major'][0] : null,
											 'placeholder' => 'Type in a major...',
											 'autocomplete' => 'off'
										)
									)
								}}

								<!-- dropdown for majors , gets populated via ajax-->
								<div class="majors-list-select">
									<span class="most-pop-right">Most Popular</span>
									<div class="popular"></div>

									<div class="line"></div>
									<div class="other"></div>
								</div>
								
								<small class="error">Please choose an option</small>
								<small id="duplicate_crumb_error">You have aleady chosen this major.</small>
								<small class="majors-error">At least one major must be chosen</small>
							</div>
						</div>
					</div>

					<!-- row for work as -->
					<div class="row">
						<div class="column large-4 medium-4 small-12">
			                <div class="objective-label">My dream is to one day work as a(n) </div>
			            </div>

			            <div class="column large-8 medium-8 small-12">
							<div class="validSelecto" id="objProfessionContainer">
								{{
									Form::text(
										'objProfession',
										isset($user['profession']) ? $user['profession'] : null,
										array(
											'id' => 'objProfession',
											'class' => 'objAutocomplete objective-input',
											 'data-selected' => isset($user['profession']) ? $user['profession'] : null,
											 'placeholder' => 'Enter a profession...',
											'required'
										)
									)
								}}
								<small class="error">Please choose an option</small>
							</div>
						</div>
					</div>

				</div>

				<!-- right side of form (where majors are populated)-->
				<div class="column large-6 medium-6 small-12 majors-container">
					<div class="majors-title">My Majors:</div>
					<div class="majors-sub-title">You may choose up to four</div>
					<ul id="majors_crumb_list" class="majorCrumbList">
						<!-- major crumbs go here -->

					</ul>
					<small id="major_number_error" class="error">Only a maximun of four majors may be chosen.</small>
				</div>
            </div>
  
            <br /><br />
            
			<!-- personal objective statement-->
            <div class="row paddingleft0">
            	<div class="large-12 small-12 column paddingleft0" style='margin-bottom: 0.5em;'>Personal Objective (optional)</div>
                <div class="large-12 small-12 column paddingleft0">{{ Form::textarea('ObjPersonalObj', @$user['obj_text'],array( 'placeholder' =>'Use this space to differentiate yourself and tell a story about you.', 'id' => 'ObjPersonalObj', 'class'=>'objective_textarea'))}}</div>
            </div>
        </div>
    </div>
    <div class="row objective-outerrow">
		<div class="small-6 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
        <div class="small-6 column">{{ Form::submit('Save', array('class'=>'button btn-Save', 'name' => 'save1'))}}</div>
		<!--
        <div class="large-3 small-12 column btn-save-continue">Save &amp; Continue</div>
		-->

    </div>
{{ Form::close() }}
<?php $degree="";?>
@if(@$user['degree_type']!="")
<?php $degree=@$user['degree_type'];?>
@endif

<?php $aos="";?>
@if(@$user['degree_type']!="")
<?php $aos=@$user['aos'];?>
@endif
<?php $aoc="";?>
@if(@$user['degree_type']!="")
<?php $aoc=@$user['aoc'];?>
@endif
<script type="text/javascript">
function getDegrees(DegreeId)
{
	$.ajax({
		url: '/ajax/profile/DropDownData',
		type: 'GET',
		data: { Type:'degree',DegreeId:DegreeId},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data) {
		$("#DegreesDropDown").html(data);
	});	
}
getDegrees('{{$degree}}');

$(document).ready(function(){

// Set autocomplete options for professions autocomplete field
	$('#objProfession').autocomplete({
		source: '/getObjectiveProfessions',
		appendTo: '#objProfessionContainer',
		minLength: 3,
		select: function(event, ui){
			$(this).data('selected', ui.item.value);
		}
	});
	$('#objProfession').change(function(){
		if($(this).val() !== $(this).data('selected')){
			$(this).val('');
		}
	});
});
	
</script>
