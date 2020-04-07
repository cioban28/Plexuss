<script type="text/javascript">
//reload zurb items.
function init_foundation_custom(){
	$(document).foundation({
		abide : {
			patterns : {
				mixText: /^[a-zA-Z0-9\.,#\- ]+$/,
				bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
			},
			validators:{
				YearCheckExp: function(el, required, parent) {
					
				var MonthTo=months[$("#month_to_exp").val()];
				var MonthFrom=months[$("#month_from_exp").val()];
				var FromYear=$("#year_from_exp").val();
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
PostExperienceInfo();
});
</script>
<div class='viewmode' style='display:block;'>
<div class="row">
<div class="large-9 columns paddingleft0">
<span><img src="../images/icon-experience.png" border="0" style="cursor:pointer;" /></span>
<span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Experience +1% to your profile status</span>
</div>
<div class="large-3 columns paddingleft0">
	<a class="add-edit-link" onclick='AddEditExpForm();'>
		Add experience
		<img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" />
	</a>
	<!--<span style="vertical-align:middle;">

	</span>
	<span>
		
	</span>-->
</div>
</div>
<br />
@if(count($exp_data)>0)
	@foreach($exp_data as $key => $value)
<div class="row form_row">
<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{ $value->title}}</div>
<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">{{ $value->month_from}}. {{ $value->year_from}} - 
@if($value->currentlyworkhere=='1')
	{{ "Present" }}
@else
{{ $value->month_to." . ".$value->year_to }}
@endif
</div>
</div>
<hr style="border: solid 1px #1F1F1F;margin:10px 0;" />

<div class="row form_row">
<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{ $value->company}}<br />{{ $value->location}}</div>
<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">{{ $value->exp_type}}</div>
</div>
<br />

<!-- Bullet Container -->
<div class="row">
	<!-- Description -->
	<div class="large-12 columns label_gray_normal_14">
		<div class='row profile-item-desc'>
			<div class='small-12 column no-padding'>
				{{ $value->exp_description}}
			</div>
		</div>
		<!-- BULLET POINTS GO HERE -->
		@if( !empty( $value->bullet_points ) )
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

<!-- Edit/Remove button -->
<div class="row">
	<div class="large-12 columns" style="text-align:right;">
		<!--
		<a class="add-edit-link vert-middle-link remove-data-btn">Remove</a>
		-->
		<a data-exp-info='{{ htmlspecialchars( json_encode($value), ENT_QUOTES ) }}' class="add-edit-link vert-middle-link"  onclick="EditExperience(this);">
			Edit
			<img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" />
		</a>
		<!--<span style="padding-left:5px;vertical-align:middle;">

		</span>
		<span>
			
		</span>-->
	</div>
</div>
<br />
@endforeach

@else
	<!-- No Experience -->
	<div class="row form_row">
	<div class="large-12 columns paddingleft0">No experience added yet.</div>
	</div>
@endif
</div>

<div class='reveal-modal medium remove_before_ajax' id="AddEditExpForm" data-reveal>
{{ Form::open(array('url' => "ajax/profile/experience/" , 'method' => 'POST', 'id' => 'ExperienceInfoForm','data-abide'=>'ajax')) }}
{{ csrf_field() }}
{{ Form::hidden('expId', null ,array('id'=>'expId')) }}
{{ Form::hidden('whocansee','Public',array()) }}
{{ Form::hidden('postType', 'removeExperience', array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns paddingleft0">
	<span><img src="../images/icon-experience.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Experience</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns paddingleft0 highschoolWhotxt">Who can see this?</div>
<div class="large-2 columns paddingleft0">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>
<div class="row form_row comp_name_row">
<div class="columns small-12 large-2 model_label_txt paddingleft0">Company Name</div>
<div class="small-12 large-10 columns paddingleft0">{{ Form::text('company_name', null , array( 'placeholder' =>'', 'id' => 'company_name','required','pattern'=>'mixText'))}}
<small class="error">Please enter a company name</small>
</div>
</div>
<div class="row form_row">
<div class="columns small-12 large-2 model_label_txt paddingleft0">Title</div>
<div class="small-12 large-10 columns paddingleft0">{{ Form::text('title', null , array( 'placeholder' =>'', 'id' => 'title','required','pattern'=>'mixText'))}}
<small class="error">Please enter a title</small>
</div>
</div>
<div class="row form_row">
<div class="columns small-12 large-2 model_label_txt paddingleft0">Location</div>
<div class="small-12 large-10 columns paddingleft0">{{ Form::text('location', null , array( 'placeholder' =>'', 'id' => 'location','required','pattern'=>'mixText'))}}
<small class="error">Please enter a location</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Time Period</div>
<div class="large-10 columns paddingleft0">

<div class="row">
	<div class="large-4 columns">{{ Form::select('month_from', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('required','id'=>'month_from_exp') ) }}
    <small class="error">Please select a month</small></div>
    <div class="large-4 columns">{{ Form::text('year_from', null , array( 'placeholder' =>'Year', 'id' => 'year_from_exp','required','pattern'=>'integer','maxlength'=>'4'))}}
		<small class="error">Please enter a year</small></div>
    <div class="large-4 columns model_label_txt">{{ Form::checkbox('icurrentlyworkhere', '1', null, array( 'id' => 'icurrentlyworkhere_exp'))}}&nbsp;&nbsp;I currently work here</div>
</div>
<div class="row end_date_exp">
	<div class="large-4 columns">{{ Form::select('month_to', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('required','id'=>'month_to_exp') ) }}
			<small class="error">Please select a month</small></div>
    <div class="large-4 columns">{{ Form::text('year_to', null , array( 'placeholder' =>'Year', 'id' => 'year_to_exp','required','pattern'=>'integer','maxlength'=>'4','data-abide-validator'=>'YearCheckExp'))}}
			<small class="error">Please enter a valid year</small></div>
    <div class="large-4 columns"></div>
</div>
</div>
</div>    
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0 experience_type_label">Experience Type</div>
<div class="large-10 columns paddingleft0">{{ Form::select('exp_type', array('' => 'Choose','Intern'=>'Intern','Volunteer'=>'Volunteer', 'Contract'=>'Contract', 'Part-time'=>'Part-time','Full-time'=>'Full-time'),null,array('class'=>'select_exp_type','required') ) }}
<small class="error">Please select experience type.</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Description</div>
<div class="large-10 columns paddingleft0">{{ Form::textarea('description', null , array( 'placeholder' =>'', 'id' => 'description', 'class'=>'text_area_class'))}}
</div>
</div>

<!-- MODAL BULLET POINTS GO HERE -->
<div class='row'>
	<div id="bullets-heading" class="small-12 large-2 columns model_label_txt">Key bullet points (optional)</div>
	<!-- BULLET CONTAINER -->
	<div class='small-12 large-10 column bullets-parent'>
		<div class='row bullet-parent'>
			<!-- Bullet point form -->
			<div class='small-7 column'>
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
	<div class="small-12 large-push-2 large-10 column end">
		<div class="add_button" onclick="addBullet('', true);">add a key point</div>
	</div>
</div>

<br />

<!-- save remove cancel row -->
<div class="row saveRemoveCancel_row">

	<div class="column small-12 large-push-6 large-6">
		<div class="row">
			<div class="small-6 medium-6 large-6 column close-reveal-modal" onclick="hideRemoveButton();">
				<div class='button btn-cancel'>Cancel</div>
			</div>
		    <div class="small-6 medium-6 large-6 column">
		    	{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'experience_save_button'))}}
		    </div>
	    </div>
	</div>
	
    <!--<div class="large-3 small-12 column btn-save-continue">Save & Continue</div>-->
    <!--<div class="large-1 show-for-large-only"></div>-->
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
			rebuildBulletIndex('#ExperienceInfoForm');
		}
		doBulletValidation();
		resetBulletForm();

	}

	function resetBulletForm(){
		//re-initializes frontend validation
		$('#ExperienceInfoForm').foundation({
			abide : {
				patterns : {
					mixText: /^[a-zA-Z0-9\.,#\- ]+$/,
					bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
				},
				validators:{
					YearCheckExp: function(el, required, parent) {
						
					var MonthTo=months[$("#month_to_exp").val()];
					var MonthFrom=months[$("#month_from_exp").val()];
					var FromYear=$("#year_from_exp").val();
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
	/*
	$(document).ready(function(){
		$(document).on('open.fndtn.reveal', '[data-reveal]', function () {
			var modal = $(this);
			var current_workplace = modal.find("#icurrentlyworkhere_exp");
			var works_here = current_workplace.prop('checked');
			if(works_here){
				toggle_current_workplace(current_workplace, $('#month_to_exp'), $('#year_to_exp'), 'YearCheckExp');
			}
			console.log('current workplace? ' + works_here);
		});

	});
	 */

$("#icurrentlyworkhere_exp").click(function(){
	toggle_current_workplace( $(this), $('#month_to_exp'), $('#year_to_exp'), 'YearCheckExp', $('.end_date_exp'));
});
</script>
