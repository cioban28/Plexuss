<script type="text/javascript">
//reload zurb items.
$(document).foundation();
$(function() {
PostSkillInfo();
});
function SetSkill(Row,Num)
{
	for(var i=1;i<=3;i++)
	{
		$('#'+Row+'_skill_'+i).addClass('skill-bg-'+i).removeClass('skill-bg-select-'+i);
		if(i==Num)
		{
		$('#'+Row+'_skill_'+i).addClass('skill-bg-select-'+i).removeClass('skill-bg-'+i);
		$('#skill_value_'+Row).val(Num);
		}
	}
}
function AddSkills()
{	
	var total=$("#count_skills").val();
	var Num=parseInt(parseInt(total)+1);
	var newRow='<div class="row row-padding" id="Skill_Row_'+Num+'"><div class="large-5 columns"><input type="text" name="skill_'+Num+'" id="skill_'+Num+'" value="" required="required" /><small class="error">Please enter your skill.</small><input type="hidden" name="skill_deleted_'+Num+'" id="skill_deleted_'+Num+'" value="0"/><input type="hidden" name="skill_id_'+Num+'" id="skill_id_'+Num+'" value="0"/></div><div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;"><div class="row collapse"><div class="large-4 medium-4 small-4 columns skill-bg-1" id="'+Num+'_skill_1" onclick="SetSkill('+Num+',1)">Beginner</div><div class="large-4 medium-4 small-4 columns skill-bg-2" id="'+Num+'_skill_2" onclick="SetSkill('+Num+',2)">Intermediate</div><div class="large-4 medium-4 small-4 columns skill-bg-3" id="'+Num+'_skill_3" onclick="SetSkill('+Num+',3)">Advanced</div></div><input type="hidden" name="skill_value_'+Num+'" id="skill_value_'+Num+'" value="" required="required"/><small class="error">Please Select Level</small></div><div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveSkills('+Num+')" /></div></div>';
	$("#SkillsRow" ).append(newRow);
	$("#count_skills").val(Num);
		/*
		$(document).foundation('reflow');
		$(document).foundation();
		$(document).foundation('abide','events');
		 */
}
function RemoveSkills(Num)
{
$("#skill_"+Num).removeAttr('required');
$("#skill_value_"+Num).removeAttr('required');
document.getElementById('Skill_Row_'+Num).style.display="none";
document.getElementById('skill_deleted_'+Num).value="1";
}

</script>
<div class='viewmode' style='display:block;'>

<div class="row">
<div class="large-9 columns">
	<span><img src="../images/icon-skill.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Skills +1% to your profile status</span>
</div>
<div class="large-3 columns">	
    <span style="vertical-align:middle;"><a class="add-edit-link"  onclick='AddEditSkillForm()'>Add/edit skills <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a></span>
    
</div>
</div>
<br />
<?php $counter=1; ?>
@if(count($skills_data)<=0)
<div class="row row-padding">
<div class="large-12 columns">No skills added.</div>
</div>
@else

	<?php $width="33%";$text="Beginner";$class="skill-beginner-bg"; ?>
	@foreach($skills_data as $key=>$value)
		@if($value->name_value=='1')
        <?php $width="33%";$text="Beginner";$class="skill-beginner-bg";?>
        @endif
		@if($value->name_value=='2')
        <?php $width="66%";$text="Intermediate";$class="skill-intermediate-bg";?>
        @endif
		@if($value->name_value=='3')
        <?php $width="100%";$text="Advanced";$class="skill-advance-bg"; ?>
        @endif
<div class="row row-padding">
	<div class="large-3 columns black-label-12">{{$value->name}}</div>
	<div class="large-9 columns skill-bg-white paddingleftright0">
	<div class="{{$class}} paddingleft0" style="width:{{$width}};">{{$text}}</div>
</div>
</div>
	@endforeach
@endif
</div>

<div class='reveal-modal medium remove_before_ajax' id="AddEditSkillForm" data-reveal>
{{ Form::open(array('url' => "ajax/profile/skills/" , 'method' => 'POST', 'id' => 'SkillInfoForm','data-abide'=>'ajax')) }}
{{ csrf_field() }}
{{ Form::hidden('whocansee','Public',array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns">
	<span><img src="../images/icon-skill.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Skills</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns highschoolWhotxt">Who can see this?</div>
<div class="large-2 columns">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>

<div class="row row-padding">
<div class="large-5 small-12 columns greay-13-bold">Skill</div>
<div class="large-7 small-12 columns greay-13-bold">Proficiency</div>
</div>

<div id="SkillsRow">
@if(count($skills_data)<=0)
<div class="row row-padding" id="Skill_Row_1">
	<div class="large-5 columns">
		<input type="text" name="skill_1" id="skill_1" value="" required="required" />
		<small class="error">Please enter your skill.</small>
		<input type="hidden" name="skill_deleted_1" id="skill_deleted_1" value="0"/>
		<input type="hidden" name="skill_id_1" id="skill_id_1" value="0"/>
	</div>
    <div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;">
		<div class="row collapse">
			<div class="large-4 medium-4 small-4 columns skill-bg-1" id="1_skill_1" onclick="SetSkill(1,1)">Beginner</div>
			<div class="large-4 medium-4 small-4 columns skill-bg-2" id="1_skill_2" onclick="SetSkill(1,2)">Intermediate</div>
			<div class="large-4 medium-4 small-4 columns skill-bg-3" id="1_skill_3" onclick="SetSkill(1,3)">Advanced</div>
		</div>
        <input type="hidden" name="skill_value_1" id="skill_value_1" value="" required="required" />
        <small class="error">Please Select Level</small>
    </div>    
    <div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveSkills(1)"/></div>
</div>
@else
	@foreach($skills_data as $key=>$value)
    	@if($value->name_value=='1')
		<?php $width="33%";$text="Beginner"; ?>
        @endif
		@if($value->name_value=='2')
        <?php $width="66%";$text="Intermediate"; ?>
        @endif
		@if($value->name_value=='3')
        <?php $width="100%";$text="Advanced"; ?>
        @endif
<div class="row row-padding" id="Skill_Row_{{$counter}}">
	<div class="large-5 columns">
		<input type="text" name="skill_{{$counter}}" id="skill_{{$counter}}" value="{{$value->name}}" required="required" />
		<small class="error">Please enter your skill.</small>
		<input type="hidden" name="skill_deleted_{{$counter}}" id="skill_deleted_{{$counter}}" value="0"/>
		<input type="hidden" name="skill_id_{{$counter}}" id="skill_id_{{$counter}}" value="{{$value->id}}"/>
	</div>
    <div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;">
		<div class="row collapse">
			<div class="large-4 medium-4 small-4 columns skill-bg-1" id="{{$counter}}_skill_1" onclick="SetSkill('{{$counter}}',1)">Beginner</div>
			<div class="large-4 medium-4 small-4 columns skill-bg-2" id="{{$counter}}_skill_2" onclick="SetSkill('{{$counter}}',2)">Intermediate</div>
			<div class="large-4 medium-4 small-4 columns skill-bg-3" id="{{$counter}}_skill_3" onclick="SetSkill('{{$counter}}',3)">Advanced</div>
		</div>
        <input type="hidden" name="skill_value_{{$counter}}" id="skill_value_{{$counter}}" value="{{{ $value->name_value or ""}}}"  required="required" />
        <small class="error">Please Select Level</small>
    </div>    
    <div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveSkills('{{$counter}}')"></div>
</div>
<script language="javascript">
SetSkill('{{$counter}}','{{$value->name_value}}')
</script>
<?php $counter++; ?>
	@endforeach
	<?php $counter=$counter-1;?>
@endif
</div>

<div class="row row-padding">
	<div class="large-12 columns"><div class="add_button" onclick="AddSkills();"><input type="hidden" name="count_skills" id="count_skills" value="{{$counter}}"/>add a skill</div></div>
</div>

<div class="row">
	<div class="small-6 column close-reveal-modal">
		<div class='button btn-cancel'>
			Cancel
		</div>
	</div>
    <div class="small-6 column">{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'skills-save-button'))}}</div>
    <!--<div class="large-3 small-12 column btn-save-continue">Save & Continue</div>-->

</div>
{{ Form::close() }}
</div>
