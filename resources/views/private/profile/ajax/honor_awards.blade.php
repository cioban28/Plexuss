<script type="text/javascript">
//reload zurb items.
$(document).foundation({
	abide : {
		patterns : {
			honor_year: /^[0-9]+$/,
			bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
		}
	}
});
$(function() {
PostHonorAwardInfo();
});
</script>
<div class='viewmode' style='display:block;'>

<div class="row">
<div class="large-9 columns paddingleft0">
	<span><img src="../images/icon-honor.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Honors & Awards +1% to your profile status</span>
</div>
<div class="large-3 columns paddingleft0">
    <span style="vertical-align:middle;"><a class="add-edit-link"  onclick='AddEditHonorAwardForm();'>Add an honor or award <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a></span>
    
</div>
</div>
<br />
@if(count($honor_data)>0)
	@foreach($honor_data as $key=>$value)
<div class='row'>
	<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{$value->title}}</div>
	<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">{{$value->month_received}}. {{$value->year_received}}</div>
</div>
<hr style="border: solid 1px #1F1F1F;margin:10px 0;" />

<div class="row form_row">
<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{$value->issuer}}</div>
<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">&nbsp;</div>
</div>
<br />

<!-- Bullet Container -->
<div class="row">
	<!-- Description -->
	<div class="large-12 columns label_gray_normal_14">
		<div class='row profile-item-desc'>
			<div class='small-12 column no-padding'>
				{{ $value->honor_description}}
			</div>
		</div>
		<!-- BULLET POINTS GO HERE -->
		@if(!empty($value->bullet_points))
			<div class='row'>
				<div class='small-12 column no-padding'>
					<ul class='bullet-ul'>
					@foreach($value->bullet_points as $bullet_key => $bullet_val)
						@if( $bullet_val->value != '' && strlen($bullet_val->value) != 0 )
							<li class='bullet-li' data-bullet_value = '{{ $bullet_val->value }}'>
								{{$bullet_val->value}}
							</li>
						@endif
					@endforeach
					</ul>
				</div>
			</div>
		@endif
	</div>
</div>
<br />

<!-- Edit button -->
<div class="row">
	<div class="large-12 columns" style="text-align:right;">
		<span style="padding-left:5px;vertical-align:middle;">
			<a data-honor-info='{{ htmlspecialchars( json_encode($value), ENT_QUOTES ) }}' class="add-edit-link"  onclick="EditHonorAward(this);">Edit <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a>
		</span>
		
	</div>
</div>
<br />
@endforeach
@else
<div class="row form_row">
<div class="large-12 columns paddingleft0">No honors or awards added.</div>
</div>
@endif
</div>

<div class='reveal-modal medium remove_before_ajax' id="AddEditHonorAwardForm" data-reveal>
{{ Form::open(array('url' => "ajax/profile/honorsAwards/" , 'method' => 'POST', 'id' => 'HonorAwardInfoForm','data-abide'=>'ajax')) }}
{{ Form::hidden('honorId', null ,array('id'=>'honorId')) }}
{{ Form::hidden('whocansee','Public',array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns paddingleft0">
	<span><img src="../images/icon-honor.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Honors & Awards</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns paddingleft0 highschoolWhotxt">Who can see this?</div>
<div class="large-2 columns paddingleft0">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Title</div>
<div class="large-10 columns paddingleft0">{{ Form::text('title', null , array( 'placeholder' =>'', 'id' => 'title','required'))}}
<small class="error">Please enter a title</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Issuer</div>
<div class="large-10 columns paddingleft0">{{ Form::text('issuer', null , array( 'placeholder' =>'', 'id' => 'issuer','required'))}}
<small class="error">Please enter an issuer</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Date Received</div>
<div class="large-10 columns paddingleft0">
<div style="float:left;" class="paddingleft0">
	{{ Form::select('month_received', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required') ) }}
	<small class="error">Please select a month</small>
</div>
	<div style="float:left;padding-left:14px;padding-top: 1px;">
		{{ Form::text('year_received', null , array( 'placeholder' =>'Year', 'id' => 'year_received', 'class'=>'year_text','required','pattern'=>'honor_year','maxlength'=>'4'))}}
		<small class="error">Please enter a year</small>
	</div>
</div>
</div>
    
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Description <br />(optional)</div>
<div class="large-10 columns paddingleft0">{{ Form::textarea('description', null , array( 'placeholder' =>'My responsibilities included...', 'id' => 'description', 'class'=>'text_area_class'))}}
</div>
</div>

<!-- begin -->
<!-- MODAL BULLET POINTS GO HERE -->
<div class='row'>
	<div id="bullets-heading" class="large-12 columns model_label_txt">Key bullet points (optional)</div>
	<!-- BULLET CONTAINER -->
	<div class='small-12 column bullets-parent'>
		<div class='row bullet-parent'>
			<!-- Bullet point form -->
			<div class='small-5 column'>
				{{ Form::text('bullets[0][value]', null, array('class' => 'bullet-input', 'pattern' => 'bullet')) }}
				<small class='error'>Invalid input</small>
			</div>
			<!-- Bullet close X -->
			<div class='small-1 column end'>
				<span class='bullet-closex'>&#10006</span>
			</div>
		</div>
	</div>
</div>

<!-- ADD BULLET BUTTON -->
<div class="row">
	<div class="small-12 column end">
		<div class="add_button" onclick="addBullet( '', true );">add a key point</div>
	</div>
</div>

<br />

<!-- Cancel/remove/save buttons -->
<div class="row saveRemoveCancel_row">
	<div class="column small-12 large-push-6 large-6">

		<div class="row">
			<div class="small-6 column close-reveal-modal" onclick="hideRemoveButton();">
				<div class='button btn-cancel'>
					Cancel
				</div>
			</div>
			<div class="small-6 column">
				{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'honors-awards-save-button'))}}
			</div>
		</div>

	</div>
</div>

{{ Form::close() }}
</div>
<script language="javascript">
	/* Adds a new bullet point that can be filled*/
	function addBullet(value, rebuild){
		if (value == undefined) {
			value = '';
		};

		var newRow = '';

		newRow += 		'<div class="row bullet-parent">';
		newRow += 			'<div class="small-5 column">';
		newRow += 				'<input type="text" name="" value="' + value + '" placeholder="" class="bullet-input" pattern="bullet"/>';
		newRow +=				'<small class="error">Invalid input</small>';
		newRow += 			'</div>';
		newRow += 			'<div class="small-1 column end">';
		newRow += 				'<span class="bullet-closex">&#10006</span>';
		newRow += 			'</div>';
		newRow += 		'</div>';

		$('.bullets-parent').append(newRow);
		if(rebuild){
			rebuildBulletIndex('#HonorAwardInfoForm');
		}
		doBulletValidation();
		resetBulletForm();

	}
	function resetBulletForm(){
		//re-initializes frontend validation
		$('#HonorAwardInfoForm').foundation({
			abide : {
				patterns : {
					honor_year: /^[0-9]+$/,
					bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
				}
			}
		});
	}
</script>
