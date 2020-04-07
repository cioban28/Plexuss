<script type="text/javascript">
//reload zurb items.
$(document).foundation();
$(function() {
PostLanguageInfo();
});
function SetLanguage(Row,Num)
{
	for(var i=1;i<=3;i++)
	{
		$('#'+Row+'_language_'+i).addClass('skill-bg-'+i).removeClass('skill-bg-select-'+i);
		if(i==Num)
		{
		$('#'+Row+'_language_'+i).addClass('skill-bg-select-'+i).removeClass('skill-bg-'+i);
		$('#language_value_'+Row).val(Num);
		}
	}
}
function AddLanguage()
{	
	var total=$("#count_language").val();
	var Num=parseInt(parseInt(total)+1);
	var newRow='<div class="row row-padding" id="Language_Row_'+Num+'"><div class="large-5 columns"><input type="text" name="language_'+Num+'" id="language_'+Num+'" value="" required="required" /><small class="error">Please enter language.</small><input type="hidden" name="language_deleted_'+Num+'" id="language_deleted_'+Num+'" value="0"/><input type="hidden" name="language_id_'+Num+'" id="language_id_'+Num+'" value="0"/></div><div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;"><div class="row collapse"><div class="large-4 medium-4 small-4 columns skill-bg-1" id="'+Num+'_language_1" onclick="SetLanguage('+Num+',1)">Beginner</div><div class="large-4 medium-4 small-4 columns skill-bg-2" id="'+Num+'_language_2" onclick="SetLanguage('+Num+',2)">Intermediate</div><div class="large-4 medium-4 small-4 columns skill-bg-3" id="'+Num+'_language_3" onclick="SetLanguage('+Num+',3)">Advanced</div></div><input type="hidden" name="language_value_'+Num+'" id="language_value_'+Num+'" value="" required="required" /><small class="error">Please Select Level</small></div><div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveLanguage('+Num+')" /></div></div>';
	$("#LanguageRow" ).append(newRow);
	$("#count_language").val(Num);
}
function RemoveLanguage(Num)
{
$("#language_"+Num).removeAttr('required');
$("#language_value_"+Num).removeAttr('required');
document.getElementById('Language_Row_'+Num).style.display="none";
document.getElementById('language_deleted_'+Num).value="1";
}

</script>
<div class='viewmode' style='display:block;'>

<div class="row">
<div class="large-9 columns">
	<span><img src="../images/i-languages.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Languages +1% to your profile status</span>
</div>
<div class="large-3 columns">	
    <span style="vertical-align:middle;"><a class="add-edit-link"  onclick='AddEditLanguageForm()'>Add/edit language <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a></span>
    
</div>
</div>
<br />
<?php $counter=1; ?>
@if(count($language_data)<=0)
<div class="row row-padding">
<div class="large-12 columns">No languages added.</div>
</div>
@else
	<?php $width="33%";$text="Beginner";$class="skill-beginner-bg";?>
	@foreach($language_data as $key=>$value)	
		@if($value->name_value=='1')
        <?php $width="33%";$text="Beginner";$class="skill-beginner-bg"; ?>
        @endif
        @if($value->name_value=='2')
        <?php $width="66%";$text="Intermediate";$class="skill-intermediate-bg"; ?>
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

<div class='reveal-modal medium remove_before_ajax' id="AddEditLanguageForm" data-reveal>
{{ Form::open(array('url' => "ajax/profile/languages/" , 'method' => 'POST', 'id' => 'LanguageInfoForm','data-abide'=>'ajax')) }}
{{ Form::hidden('whocansee','Public',array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns">
	<span><img src="../images/i-languages.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Languages</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns">Who can see this?</div>
<div class="large-2 columns">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>


<div class="row row-padding">
<div class="large-5 small-12 columns greay-13-bold">Language</div>
<div class="large-7 small-12 columns greay-13-bold">Fluency</div>
</div>

<div id="LanguageRow">
@if(count($language_data)<=0)
<div class="row row-padding" id="Language_Row_1">
	<div class="large-5 columns">
		<input type="text" name="language_1" id="language_1" value="" required="required" />
		<small class="error">Please enter language.</small>
		<input type="hidden" name="language_deleted_1" id="language_deleted_1" value="0"/>
		<input type="hidden" name="language_id_1" id="language_id_1" value="0"/>
	</div>
    <div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;">
    	<div class="row collapse">            
            <div class="large-4 medium-4 small-4 columns skill-bg-1" id="1_language_1" onclick="SetLanguage(1,1)">Beginner</div>
            <div class="large-4 medium-4 small-4 columns skill-bg-2" id="1_language_2" onclick="SetLanguage(1,2)">Intermediate</div>
            <div class="large-4 medium-4 small-4 columns skill-bg-3" id="1_language_3" onclick="SetLanguage(1,3)">Advanced</div>
        </div>
        <input type="hidden" name="language_value_1" id="language_value_1" value="" required="required" />
        <small class="error">Please Select Level</small>
    </div>    
    <div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveLanguage(1)" /></div>
</div>
@else
	@foreach($language_data as $key=>$value)

		@if($value->name_value=='1')
		<?php $width="33%";$text="Beginner"; ?>
        @endif
		@if($value->name_value=='2')
        <?php $width="66%";$text="Intermediate"; ?>
        @endif
		@if($value->name_value=='3')
        <?php $width="100%";$text="Advanced"; ?>
        @endif
<div class="row row-padding" id="Language_Row_{{$counter}}">
	<div class="large-5 columns">
		<input type="text" name="language_{{$counter}}" id="language_{{$counter}}" value="{{$value->name}}" required="required" />
		<small class="error">Please enter language.</small>
		<input type="hidden" name="language_deleted_{{$counter}}" id="language_deleted_{{$counter}}" value="0"/>
		<input type="hidden" name="language_id_{{$counter}}" id="language_id_{{$counter}}" value="{{$value->id}}"/>
	</div>
    <div class="large-6 medium-11 small-11 columns" style="padding-top: 8px;">
    	<div class="row collapse">            
            <div class="large-4 medium-4 small-4 columns skill-bg-1" id="{{$counter}}_language_1" onclick="SetLanguage('{{$counter}}',1)">Beginner</div>
            <div class="large-4 medium-4 small-4 columns skill-bg-2" id="{{$counter}}_language_2" onclick="SetLanguage('{{$counter}}',2)">Intermediate</div>
            <div class="large-4 medium-4 small-4 columns skill-bg-3" id="{{$counter}}_language_3" onclick="SetLanguage('{{$counter}}',3)">Advanced</div>
        </div>
        <input type="hidden" name="language_value_{{$counter}}" id="language_value_{{$counter}}" value="{{{$value->name_value or ""}}}"  required="required" />
        <small class="error">Please Select Level</small>
    </div>    
    <div class="large-1 medium-1 small-1 columns" style="padding-top: 6px;"><img class="profile-close-x" src="/images/close-x.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="RemoveLanguage('{{$counter}}')" /></div>
</div>
<script language="javascript">
SetLanguage('{{$counter}}','{{$value->name_value}}')
</script>
<?php $counter++; ?>
	@endforeach
	<?php $counter=$counter-1;?>
@endif
</div>

<div class="row row-padding">
	<div class="large-12 columns"><div class="add_button" onclick="AddLanguage();"><input type="hidden" name="count_language" id="count_language" value="{{$counter}}"/>add a language</div></div>
</div>

<div class="row">
	<div class="small-6 column close-reveal-modal">
		<div class='button btn-cancel'>
			Cancel
		</div>
	</div>
	<div class="small-6 column">
		{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'languages-save-button'))}}
	</div>
    <!--<div class="large-3 small-12 column btn-save-continue">Save & Continue</div>-->
    <div class="large-1 show-for-large-only"></div>
</div>
{{ Form::close() }}
</div>
