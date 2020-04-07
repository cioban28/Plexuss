<script type="text/javascript">
//reload zurb items.
$(document).foundation();
$(function() {
PostInterestInfo();
});
function SetInterest(Row,Num)
{
	for(var i=1;i<=3;i++)
	{
		$('#'+Row+'_interest_'+i).addClass('skill-bg-'+i).removeClass('skill-bg-select-'+i);
		if(i==Num)
		{
		$('#'+Row+'_interest_'+i).addClass('skill-bg-select-'+i).removeClass('skill-bg-'+i);
		$('#interest_value_'+Row).val(Num);
		}
	}
}
function AddInterests()
{	
	var total=$("#count_interest").val();
	var Num=parseInt(parseInt(total)+1);
	var newRow='<div class="row row-padding" id="Interest_Row_'+Num+'"><div class="large-5 columns"><input type="text" name="interest_'+Num+'" id="interest_'+Num+'" value="" required="required" /><small class="error">Please enter your interest.</small><input type="hidden" name="interest_deleted_'+Num+'" id="interest_deleted_'+Num+'" value="0"/><input type="hidden" name="interest_id_'+Num+'" id="interest_id_'+Num+'" value="0"/><small class="error">Please enter your interest.</small></div><div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;"><div class="row collapse"><div class="large-4 medium-4 small-4 columns skill-bg-1" id="'+Num+'_interest_1" onclick="SetInterest('+Num+',1)">Beginner</div><div class="large-4 medium-4 small-4 columns skill-bg-2" id="'+Num+'_interest_2" onclick="SetInterest('+Num+',2)">Intermediate</div><div class="large-4 medium-4 small-4 columns skill-bg-3" id="'+Num+'_interest_3" onclick="SetInterest('+Num+',3)">Advanced</div></div><input type="hidden" name="interest_value_'+Num+'" id="interest_value_'+Num+'" value="" required="required"/><small class="error">Please Select Level</small></div><div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveInterests('+Num+')" /></div></div>';
	$("#InterestsRow" ).append(newRow);
	$("#count_interest").val(Num);
}
function RemoveInterests(Num)
{
$("#interest_"+Num).removeAttr('required');
$("#interest_value_"+Num).removeAttr('required');
document.getElementById('Interest_Row_'+Num).style.display="none";
document.getElementById('interest_deleted_'+Num).value="1";
}

</script>
<div class='viewmode' style='display:block;'>

<div class="row">
<div class="large-9 columns">
	<span><img src="../images/i-interests.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Interests</span>
</div>
<div class="large-3 columns">	
    <span style="vertical-align:middle;"><a class="add-edit-link"  onclick='AddEditInterestForm()'>Add/edit interests <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a></span>
    
</div>
</div>
<br />
<?php $counter=1; ?>
@if(count($interests_data)<=0)
<div class="row row-padding">
<div class="large-12 columns">No interests added.</div>
</div>
@else

	<?php $width="33%";$text="Beginner";$class="skill-beginner-bg"; ?>
	@foreach($interests_data as $key=>$value)
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

<div class='reveal-modal medium remove_before_ajax' id="AddEditInterestForm" data-reveal>
{{ Form::open(array('url' => "ajax/profile/interests/" , 'method' => 'POST', 'id' => 'InterestInfoForm','data-abide'=>'ajax')) }}
{{ Form::hidden('whocansee','Public',array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns">
	<span><img src="../images/i-interests.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Interests</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns">Who can see this?</div>
<div class="large-2 columns">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>


<div class="row row-padding">
<div class="large-5 small-12 columns greay-13-bold">Interest</div>
<div class="large-7 small-12 columns greay-13-bold">Passion Level</div>
</div>

<div id="InterestsRow">
@if(count($interests_data)<=0)
<div class="row row-padding" id="Interest_Row_1">
	<div class="large-5 columns">
		<input type="text" name="interest_1" id="interest_1" value="" required="required" />
		<input type="hidden" name="interest_deleted_1" id="interest_deleted_1" value="0"/>
		<input type="hidden" name="interest_id_1" id="interest_id_1" value="0"/>
		<small class="error">Please enter your interest.</small>
    </div>
    <div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;">
    	<div class="row collapse">            
            <div class="large-4 medium-4 small-4 columns skill-bg-1" id="1_interest_1" onclick="SetInterest(1,1)">Beginner</div>
            <div class="large-4 medium-4 small-4 columns skill-bg-2" id="1_interest_2" onclick="SetInterest(1,2)">Intermediate</div>
            <div class="large-4 medium-4 small-4 columns skill-bg-3" id="1_interest_3" onclick="SetInterest(1,3)">Advanced</div>
        </div>
        <input type="hidden" name="interest_value_1" id="interest_value_1" value="" required="required" />
        <small class="error">Please Select Level</small>
    </div>    
    <div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveInterests(1)" /></div>
</div>
@else
	@foreach($interests_data as $key=>$value)
    	@if($value->name_value=='1')
		<?php $width="33%";$text="Beginner"; ?>
        @endif
		@if($value->name_value=='2')
        <?php $width="66%";$text="Intermediate"; ?>
        @endif
		@if($value->name_value=='3')
        <?php $width="100%";$text="Advanced"; ?>
        @endif
<div class="row row-padding" id="Interest_Row_{{$counter}}">
	<div class="large-5 columns">
		<input type="text" name="interest_{{$counter}}" id="interest_{{$counter}}" value="{{$value->name}}" required="required" />
		<input type="hidden" name="interest_deleted_{{$counter}}" id="interest_deleted_{{$counter}}" value="0"/>
		<input type="hidden" name="interest_id_{{$counter}}" id="interest_id_{{$counter}}" value="{{$value->id}}"/>
		<small class="error">Please enter your interest.</small>
    </div>
    <div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;">
    	<div class="row collapse">            
            <div class="large-4 medium-4 small-4 columns skill-bg-1" id="{{$counter}}_interest_1" onclick="SetInterest('{{$counter}}',1)">Beginner</div>
            <div class="large-4 medium-4 small-4 columns skill-bg-2" id="{{$counter}}_interest_2" onclick="SetInterest('{{$counter}}',2)">Intermediate</div>
            <div class="large-4 medium-4 small-4 columns skill-bg-3" id="{{$counter}}_interest_3" onclick="SetInterest('{{$counter}}',3)">Advanced</div>
        </div>
        <input type="hidden" name="interest_value_{{$counter}}" id="interest_value_{{$counter}}" value="{{{$value->name_value or ""}}}" required="required" />
        <small class="error">Please Select Level</small>
    </div>    
    <div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveInterests('{{$counter}}')" /></div>
</div>
<script language="javascript">
SetInterest('{{$counter}}','{{$value->name_value}}')
</script>
<?php $counter++; ?>
	@endforeach
	<?php $counter=$counter-1;?>
@endif
</div>

<div class="row row-padding">
	<div class="large-12 columns"><div class="add_button" onclick="AddInterests();"><input type="hidden" name="count_interest" id="count_interest" value="{{$counter}}"/>add an interest</div></div>
</div>

<div class="row">
	<div class="small-6 column close-reveal-modal">
		<div class='button btn-cancel'>
			Cancel
		</div>
	</div>
    <div class="small-6 column">{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'interests-save-button'))}}</div>
    <!--<div class="large-3 small-12 column btn-save-continue">Save & Continue</div>-->
    <div class="large-1 show-for-large-only"></div>
    <div class="large-1 show-for-large-only"></div>
</div>
{{ Form::close() }}
</div>
