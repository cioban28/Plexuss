<script type="text/javascript">
//reload zurb items.
$(document).foundation({
	abide : {
		patterns : {
			patent_year: /^[0-9]+$/,
			bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
		}
	}
});
$(function() {
PostPatentsInfo();
});
</script>
<div class='viewmode' style='display:block;'>

<div class="row">
<div class="large-9 columns paddingleft0">
	<span><img src="../images/i-patents.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Patents </span>
</div>
<div class="large-3 columns paddingleft0">
    <span style="vertical-align:middle;"><a class="add-edit-link"  onclick='addNewPatents();'>Add a patent <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a></span>
    
</div>
</div>
<br />
@if(count($patents_data)>0)
	@foreach($patents_data as $key=>$value)
<div class="row form_row">
	<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{$value->patent_title}}</div>
	<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">{{$value->issue_month}} {{$value->issue_day}}, {{$value->issue_year}}</div>
</div>
<hr style="border: solid 1px #1F1F1F;margin:10px 0;" />

<div class="row form_row">
<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{$value->patent_office}}<br />#{{$value->patent_app_number}}</div>
<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">
@if($value->patent_authority=='1')
Patent Issued
@else
Patent Pending
@endif
</div>
</div>

<!-- Bullet Container -->
<div class="row">
	<!-- Description -->
	<div class="large-12 columns label_gray_normal_14">
		<div class='row profile-item-desc'>
			<div class='small-12 column no-padding'>
				{{ $value->patent_description}}
			</div>
		</div>
		<div class='row profile-item-url'>
			<div class='small-12 column no-padding'>
				Patent URL: {{ $value->patent_url}}
			</div>
		</div>
		<!-- BULLET POINTS GO HERE -->
		@if(!empty($value->bullet_points))
			<div class='row'>
				<div class='small-12 column no-padding'>
				Other Authors:
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
			<a data-patent-info='{{ htmlspecialchars( json_encode($value), ENT_QUOTES ) }}' class="add-edit-link"  onclick="EditPatents(this);">
				Edit <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span>
			</a>
		</span>
		
	</div>
</div>
<br />
	@endforeach
@else
<div class="row form_row">
<div class="large-12 columns paddingleft0">No patents added.</div>
</div>
@endif
</div>


<div class='reveal-modal medium remove_before_ajax' id="addNewPatents" data-reveal>
{{ Form::open(array('url' => "ajax/profile/patents/" , 'method' => 'POST', 'id' => 'PatentsInfoForm','data-abide'=>'ajax')) }}
{{ Form::hidden('patentId', null ,array('id'=>'patentId')) }}
{{ Form::hidden('whocansee','Public',array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns paddingleft0">
	<span><img src="../images/i-patents.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Patents</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns paddingleft0 highschoolWhotxt">Who can see this?</div>
<div class="large-2 columns paddingleft0">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Patent Office</div>
<div class="large-4 columns paddingleft0">{{ Form::select('patent_office', $countries ,null,array('required') ) }}
<small class="error">Patent office is required.</small>
</div>
<div class="large-5 columns paddingleft0">&nbsp;</div>
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Patent Authority</div>
<div class="large-9 columns paddingleft0 headColData">{{ Form::radio('patent_authority', '1', null, array( 'id' => 'patent_authority_1','required','checked'))}}&nbsp;Patent Issued&nbsp;{{ Form::radio('patent_authority', '0', null, array( 'id' => 'patent_authority_2','required'))}}&nbsp;Patent Pending</div>
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Patent/Application number</div>
<div class="large-9 columns paddingleft0">{{ Form::text('patent_app_number', null , array( 'placeholder' =>'', 'id' => 'patent_app_number','required'))}}
<small class="error">Please enter a patent or application number</small>
</div>
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Patent Title</div>
<div class="large-9 columns paddingleft0">{{ Form::text('patent_title', null , array( 'placeholder' =>'', 'id' => 'patent_title','required'))}}
<small class="error">Please enter a title</small>
</div>
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Issue/Filing Date</div>
<div class="large-9 columns paddingleft0">
<div style="float:left;" class="paddingleft0">
	{{ Form::select('issue_month', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required') ) }}
<small class="error">Please select a month</small>
</div>
	<div style="float:left;padding-left:14px;padding-top: 1px;">
		{{ Form::text('issue_day', null , array( 'placeholder' =>'Day', 'id' => 'issue_day', 'class'=>'year_text','required','pattern'=>'patent_year','maxlength'=>'2'))}}
		<small class="error">Please enter a day</small>
	</div>
	<div style="float:left;padding-left:14px;padding-top: 1px;">
		{{ Form::text('issue_year', null , array( 'placeholder' =>'Year', 'id' => 'issue_year', 'class'=>'year_text','required','pattern'=>'patent_year','maxlength'=>'4'))}}
		<small class="error">Please enter a day</small>
	</div>
</div>
</div>

<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Patent URL</div>
<div class="large-9 columns paddingleft0">{{ Form::text('patent_url', null , array( 'placeholder' =>'', 'id' => 'patent_url','required','pattern'=>'url'))}}
<small class="error">Please enter a patent URL</small>
</div>
</div>

<!-- MODAL BULLET POINTS GO HERE -->
<div class='row'>
	<div id="bullets-heading" class="small-12 large-3 columns model_label_txt">Inventor(s)</div>
	<!-- BULLET CONTAINER -->
	<div class='small-12 large-9 column bullets-parent'>
		<div class='row bullet-parent'>
			<!-- Bullet point form -->
			<div class='small-7 column'>
				{{ Form::text('bullets[0][value]', null, array('class' => 'bullet-input')) }}
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
	<div class="large-offset-3 small-12 column end">
		<div class="add_button" onclick="addBullet('', true);">Add an inventor</div>
	</div>
</div>

<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Description <br />(optional)</div>
<div class="large-9 columns paddingleft0">{{ Form::textarea('description', null , array( 'placeholder' =>'My responsibilities included...', 'id' => 'description', 'class'=>'text_area_class'))}}
</div>
</div>

<br />
<div class="row saveRemoveCancel_row">
	<div class="column small-12 large-push-6 large-6">
		<div class="row">
			<div class="small-6 column close-reveal-modal" onclick="hideRemoveButton();">
				<div class='button btn-cancel'>
					Cancel
				</div>
			</div>
			<div class="small-6 column">
				{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'patents-save-button'))}}
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
		newRow += 			'<div class="small-7 column">';
		newRow += 				'<input type="text" name="" value="' + value + '" placeholder="" class="bullet-input" pattern="bullet"/>';
		newRow +=				'<small class="error">Invalid input</small>';
		newRow += 			'</div>';
		newRow += 			'<div class="small-1 column end">';
		newRow += 				'<span class="bullet-closex">&#10006</span>';
		newRow += 			'</div>';
		newRow += 		'</div>';

		$('.bullets-parent').append(newRow);
		if(rebuild){
			rebuildBulletIndex('#PatentsInfoForm');
		}
		doBulletValidation();
		resetBulletForm();

	}

	function resetBulletForm(){
		//re-initializes frontend validation
		$('#PatentsInfoForm').foundation({
			abide : {
				patterns : {
					patent_year: /^[0-9]+$/,
					bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
				}
			}
		});
	}

	$('#patent_url').change(function(){
		http_helper( $(this) );
	});
</script>
