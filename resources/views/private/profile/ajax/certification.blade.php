<script type="text/javascript">
//reload zurb items.
function init_foundation_custom(){
	$(document).foundation({
		abide : {
			patterns : {
				certi_year: /^[0-9]+$/,
				certi_name: /^[a-zA-Z0-9 ]+$/,
				bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
			},
			validators:{
				YearCheckCerti: function(el, required, parent) {
					
					var MonthTo=months[$("#month_expire_certi").val()];
					var MonthFrom=months[$("#month_received").val()];
					var FromYear=$("#year_received").val();
					var Val=el.value;
					var pat=/^\d+$/;

					if(pat.test(Val)==false){
						return false;
					}
					
					if(pat.test(Val)==false){
						return false;
					}

					if(el.value < FromYear){
						return false;

					}else if(el.value == FromYear){
						if(MonthTo < MonthFrom){
							return false;
						}
						else{
							return true;
						}
					}
					else{
						return true;
					}
				
				}
			}
		}
	});
}
init_foundation_custom();
$(function() {
PostCertificationInfo();
});
</script>

<div class='viewmode' style='display:block;'>

<div class="row">
<div class="large-9 columns paddingleft0">
	<span><img src="../images/i-certifications.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Certifications </span>
</div>
<div class="large-3 columns paddingleft0">
    <span style="vertical-align:middle;"><a class="add-edit-link"  onclick='addNewCertificates();'>Add a certificate <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a></span>
    
</div>
</div>
<br />
@if(count($certi_data)>0)
	@foreach($certi_data as $key=>$value)
<div class="row form_row">
	<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{ $value->certi_name}}</div>
	<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">{{$value->month_received}} . {{$value->year_received}}
@if($value->notexpire=='0')
{{ " - ".$value->month_expire.". ".$value->year_expire }}
@endif
</div>
</div>
<hr style="border: solid 1px #1F1F1F;margin:10px 0;" />

<div class="row form_row">
<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{ $value->certi_auth }}</div>
<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">{{ $value->certi_license}}</div>
</div>
<br />

<!-- Bullet Container -->
<div class="row">
	<!-- Description -->
	<div class="large-12 columns label_gray_normal_14">
		<div class='row profile-item-desc'>
			<div class='small-12 column no-padding'>
				{{ $value->certi_description}}
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
		<div class='row profile-item-url'>
			<div class='small-12 column no-padding'>
				Certificate URL {{ $value->certi_url}}
			</div>
		</div>
	</div>
</div>
<br />

<!-- Edit button -->
<div class="row">
	<div class="large-12 columns" style="text-align:right;">
		<span style="padding-left:5px;vertical-align:middle;">
			<a data-certi-info='{{ htmlspecialchars(json_encode($value), ENT_QUOTES) }}' class="add-edit-link"  onclick="EditCertification(this);">
				Edit <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span>
			</a>
		</span>
		
	</div>
</div>
<br />
	@endforeach
@else
	<div class="row form_row">
		<div class="large-12 columns paddingleft0">No certifications added.</div>
	</div>
@endif
</div>


<div class='reveal-modal medium remove_before_ajax' id="addNewCertificates" data-reveal>
{{ Form::open(array('url' => "ajax/profile/certifications/". null , 'method' => 'POST', 'id' => 'CertificatesInfoForm','data-abide'=>'ajax')) }}
{{ csrf_field() }}
{{ Form::hidden('certiId', null ,array('id'=>'certiId')) }}
{{ Form::hidden('whocansee','Public',array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns paddingleft0">
	<span><img src="../images/i-certifications.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Certifications</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns paddingleft0 highschoolWhotxt">Who can see this?</div>
<div class="large-2 columns paddingleft0">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Certification Name</div>
<div class="large-10 columns paddingleft0">{{ Form::text('certi_name', null , array( 'placeholder' =>'', 'id' => 'certi_name','required', 'pattern' => 'certi_name'))}}
<small class="error">Please enter a certificate name</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Authority</div>
<div class="large-10 columns paddingleft0">{{ Form::text('certi_auth', null , array( 'placeholder' =>'', 'id' => 'certi_auth','required', 'pattern' => 'certi_name'))}}
<small class="error">Please enter an issuing authority</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">License Number</div>
<div class="large-10 columns paddingleft0">{{ Form::text('certi_license', null , array( 'placeholder' =>'', 'id' => 'certi_license','required', 'pattern' => 'alpha_numeric'))}}
<small class="error">Please enter a license number</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Certification URL</div>
<div class="large-10 columns paddingleft0">{{ Form::text('certi_url', null , array( 'placeholder' =>'', 'id' => 'certi_url','required','pattern'=>'url'))}}
<small class="error">Please enter a valid URL</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Date Received</div>
<div class="large-4 columns paddingleft0">
	<div style="float:left;" class="paddingleft0">
		{{ Form::select('month_received', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required','id'=>'month_received') ) }}
		<small class="error">Please select a month</small>
	</div>
	<div style="float:left;padding-left:14px;padding-top: 1px;">
		{{ Form::text('year_received', null , array( 'placeholder' =>'Year', 'id' => 'year_received', 'class'=>'year_text','required','pattern'=>'certi_year','maxlength'=>'4'))}}
	<small class="error">Please enter a year</small>
	</div>
</div>
<div class="large-6 columns paddingleft0"><span style="float:left;padding-top: 1px;">{{ Form::checkbox('notexpire', '1', null, array( 'id' => 'notexpire'))}}</span>
    <span class="model_label_txt" style="float:left;vertical-align:middle;padding-left:10px;">This certificate does not expire</span></div>
</div>

<div class="row form_row end_date_certi">
<div class="large-2 columns model_label_txt paddingleft0">Expiration Date</div>
<div class="large-10 columns paddingleft0">
<div style="float:left;" class="paddingleft0">
	{{ Form::select('month_expire', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required','id'=>'month_expire_certi') ) }}
	<small class="error">Please select a month</small>
</div>
<div style="float:left;padding-left:14px;padding-top: 1px;">
	{{ Form::text('year_expire', null , array( 'placeholder' =>'Year', 'id' => 'year_expire_certi', 'class'=>'year_text','required','pattern'=>'certi_year','maxlength'=>'4','data-abide-validator'=>'YearCheckCerti'))}}
<small class="error">Please enter a valid year</small>
</div>
</div>
</div>
    
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Description <br />(optional)</div>
<div class="large-10 columns paddingleft0">{{ Form::textarea('description', null , array( 'placeholder' =>'My responsibilities included...', 'id' => 'description', 'class'=>'text_area_class'))}}
</div>
</div>

<!--
<div class="row form_row">
<div class="large-12 columns model_label_txt paddingleft0">Key bullet points (optional)</div>
</div>

<div id="certiBulletPointsRows">
<div class="row" id="certiBulletRow1">
<div class="large-2 columns model_label_txt paddingleft0 hide-for-small-only">&nbsp;</div>
<div class="large-7 small-9 columns paddingleft0"><input type="text" name="certi_bullet_1" id="certi_bullet_1" placeholder="" />&nbsp;<input type="hidden" name="certi_is_delete_1" id="certi_is_delete_1" value="0" /><input type="hidden" name="certi_bullet_id_1" id="certi_bullet_id_1" value="" /></div>
<div class="large-3 small-3 columns model_label_txt paddingleft0" style="padding-top: 11px;"><img src="../images/icon-close.png" border="0" style="cursor:pointer;" align="absmiddle" onclick="certiRemoveBulletPoints(1)" /></div>
</div>
</div>
-->
<!-- add key point row -->
<!--
<div class="row">
	<div class="large-2 small-12 columns model_label_txt paddingleft0">&nbsp;</div>
	<div class="large-7 small-9 columns paddingleft0">
		<div class="add_button" onclick="certiAddBulletPoints();">add a key point</div>
	</div>
	<div class="large-3 columns model_label_txt paddingleft0 hide-for-small-only">
		<input type="hidden" name="certi_count_bullet" id="certi_count_bullet" value="1" />
	</div>
</div>
-->
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
		<div class="add_button" onclick="addBullet('', true);">add a key point</div>
	</div>
</div>
<br />

<!-- cancel/save/remove row -->
<div class="row saveRemoveCancel_row">
	<div class="column small-12 large-push-6 large-6">

		<div class="row">
			<div class="small-6 column close-reveal-modal" onclick="hideRemoveButton();">
				<div class='button btn-cancel' >
					Cancel
				</div>
			</div>
			<div class="small-6 column">
				{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'certifications-save-button'))}}
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
		newRow += 				'<small class="error">Invalid input</small>';
		newRow += 			'</div>';
		newRow += 			'<div class="small-1 column end">';
		newRow += 				'<span class="bullet-closex">&#10006</span>';
		newRow += 			'</div>';
		newRow += 		'</div>';

		$('.bullets-parent').append(newRow);
		if(rebuild){
			rebuildBulletIndex('#CertificatesInfoForm');
		}
		doBulletValidation();
		resetBulletForm();

	}

	// will refactor...
	function resetBulletForm(){
		//re-initializes frontend validation
		$('#CertificatesInfoForm').foundation({
			abide : {
				patterns : {
					certi_year: /^[0-9]+$/,
					certi_name: /^[a-zA-Z0-9 ]+$/,
					bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
				},
				validators:{
					YearCheckCerti: function(el, required, parent) {
						
						var MonthTo=months[$("#month_expire_certi").val()];
						var MonthFrom=months[$("#month_received").val()];
						var FromYear=$("#year_received").val();
						var Val=el.value;
						var pat=/^\d+$/;

						if(pat.test(Val)==false){
							return false;
						}
						
						if(pat.test(Val)==false){
							return false;
						}

						if(el.value < FromYear){
							return false;

						}else if(el.value == FromYear){
							if(MonthTo < MonthFrom){
								return false;
							}
							else{
								return true;
							}
						}
						else{
							return true;
						}
					
					}
				}
			}
		});
	}

$("#notexpire").click(function(){
	toggle_current_workplace( $(this), $('#month_expire_certi'), $('#year_expire_certi'), 'YearCheckCerti', $('.end_date_certi'));
});

// Prepend http:// if not in input field on change
$('#certi_url').change(function(){
	http_helper( $(this) );
});
</script>
