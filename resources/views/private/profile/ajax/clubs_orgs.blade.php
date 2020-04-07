<script type="text/javascript">
//reload zurb items.
function init_foundation_custom(){
	$(document).foundation({
		abide : {
			patterns : {
				club_year: /^[0-9]+$/,
				bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
			},
			validators:{
				YearCheckClub: function(el, required, parent) {
					
				var MonthTo=months[$("#month_to_club").val()];
				var MonthFrom=months[$("#month_from_club").val()];
				var FromYear=$("#year_from_club").val();
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
PostClubOrgInfo();
});
</script>
<div class='viewmode' style='display:block;'>

<div class="row">
<div class="large-9 columns paddingleft0">
	<span><img src="../images/i-clubs.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Clubs & Organizations +1% to your profile status</span>
</div>
<div class="large-3 columns paddingleft0">
    <span style="vertical-align:middle;"><a class="add-edit-link"  onclick='AddEditClubOrgForm();'>Add club or organization <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a></span>
    
</div>
</div>
<br />
@if(count($club_data)>0)
	@foreach($club_data as $key=>$value)
<div class="row form_row">
<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{$value->club_name}}</div>
<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">{{$value->month_from}} . {{$value->year_from}} - 
@if($value->currentlyworkhere=='1')
{{ "Present"}}
@else
{{ $value->month_to.". ".$value->year_to }}
@endif
</div>
</div>
<hr style="border: solid 1px #1F1F1F;margin:10px 0;" />

<div class="row form_row">
<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{$value->position}}<br />{{$value->location}}</div>
<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">&nbsp;</div>
</div>
<br />

<!-- Bullet Container -->
<div class="row">
	<!-- Description -->
	<div class="large-12 columns label_gray_normal_14">
		<div class='row profile-item-desc'>
			<div class='small-12 column no-padding'>
				{{ $value->club_description}}
			</div>
		</div>
		<!-- BULLET POINTS GO HERE -->
		@if( !empty($value->bullet_points) )
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
			<a data-club-info='{{ htmlspecialchars( json_encode($value), ENT_QUOTES ) }}' class="add-edit-link"  onclick="EditClubOrg(this);">Edit <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a>
		</span>
		
	</div>
</div>
<br />
@endforeach

@else
	<!-- No Experience -->
	<div class="row form_row">
	<div class="large-12 columns paddingleft0">No clubs or organizations added.</div>
	</div>
@endif
</div>

<div class='reveal-modal medium remove_before_ajax' id="AddEditClubOrgForm" data-reveal>
{{ Form::open(array('url' => "ajax/profile/clubOrgs/" , 'method' => 'POST', 'id' => 'ClubOrgInfoForm','data-abide'=>'ajax')) }}
{{ csrf_field() }}
{{ Form::hidden('clubId', null ,array('id'=>'clubId')) }}
{{ Form::hidden('whocansee','Public',array()) }}
{{ Form::hidden('postType', 'removeClubOrg', array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns paddingleft0">
	<span><img src="../images/i-clubs.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Clubs & Organizations</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns paddingleft0 highschoolWhotxt">Who can see this?</div>
<div class="large-2 columns paddingleft0">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Club/Org. Name</div>
<div class="large-10 columns paddingleft0">{{ Form::text('club_name', null , array( 'placeholder' =>'', 'id' => 'club_name','required'))}}
<small class="error">Please enter a club or organization name</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Position Held</div>
<div class="large-10 columns paddingleft0">{{ Form::text('position', null , array( 'placeholder' =>'', 'id' => 'position','required'))}}
<small class="error">Please enter a position you held</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Location</div>
<div class="large-10 columns paddingleft0">{{ Form::text('location', null , array( 'placeholder' =>'', 'id' => 'location','required'))}}
<small class="error">Please enter a location</small>
</div>
</div>
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Time Period</div>
<div class="large-10 columns paddingleft0">

<div class="row">
	<div class="large-4 columns">{{ Form::select('month_from', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required','id'=>'month_from_club') ) }}
    <small class="error">Please select a month</small></div>
    <div class="large-4 columns">{{ Form::text('year_from', null , array( 'placeholder' =>'Year', 'id' => 'year_from_club', 'class'=>'year_text','required','pattern'=>'club_year','maxlength'=>'4'))}}
		<small class="error">Please enter a year</small></div>
    <div class="large-4 columns model_label_txt">{{ Form::checkbox('icurrentlyworkhere', '1', null, array( 'id' => 'icurrentlyworkhere_club'))}}&nbsp;&nbsp;I currently work here</div>
</div>
<div class="row end_date_club">
	<div class="large-4 columns">{{ Form::select('month_to', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required','id'=>'month_to_club') ) }}
			<small class="error">Please select a month</small></div>
    <div class="large-4 columns">{{ Form::text('year_to', null , array( 'placeholder' =>'Year', 'id' => 'year_to_club', 'class'=>'year_text','required','pattern'=>'club_year','maxlength'=>'4','data-abide-validator'=>'YearCheckClub'))}}
			<small class="error">Please enter a valid year</small></div>
    <div class="large-4 columns"></div>
</div>
</div>
</div>
<!--<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Time Period</div>
<div class="large-10 columns paddingleft0">

<div class="row">
	<div class="large-4 columns paddingleft0">
		<div style="float:left;">
			{{ Form::select('month_from', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required') ) }}
			<small class="error">Please select a month</small>
		</div>
		<div style="float:left;padding-left:6px;padding-top: 1px;">
			{{ Form::text('year_from', null , array( 'placeholder' =>'Year', 'id' => 'year_from', 'class'=>'year_text','required','pattern'=>'club_year','maxlength'=>'4'))}}
			<small class="error">Please enter a year</small>
		</div>
	</div>
    <div class="large-1 columns model_label_txt paddingleft0" style="text-align:left;">to</div>
	<div class="large-4 columns paddingleft0">
		<div style="float:left;">
			{{ Form::select('month_to', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required','id'=>'month_to_club') ) }}
			<small class="error">Please select a month</small>
		</div>
		<div style="float:left;padding-left:6px;padding-top: 1px;">
			{{ Form::text('year_to', null , array( 'placeholder' =>'Year', 'id' => 'year_to_club', 'class'=>'year_text','required','pattern'=>'club_year','maxlength'=>'4'))}}
			<small class="error">Please enter a year</small>
		</div>
	</div>
    <div class="large-3 columns paddingleft0"><div style="float:left;padding-top: 1px;">{{ Form::checkbox('icurrentlyworkhere', '1', null, array( 'id' => 'icurrentlyworkhere_club'))}}</div>
    <div class="model_label_txt" style="float:left;vertical-align:middle;padding-left:10px;font-size:11px;">I currently work here</div></div>
</div>

    
    
</div>
</div>-->
    
<div class="row form_row">
<div class="large-2 columns model_label_txt paddingleft0">Description<br>(optional)</div>
<div class="large-10 columns paddingleft0">{{ Form::textarea('description', null , array( 'placeholder' =>'My responsibilities included...', 'id' => 'description', 'class'=>'text_area_class'))}}
</div>
</div>

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

<!-- cancel/remove/save row -->
<div class="row saveRemoveCancel_row">
	<div class="column small-12 large-push-6 large-6">

		<div class="row">
			<div class="small-6 column close-reveal-modal" onclick="hideRemoveButton();">
				<div class='button btn-cancel' >
					Cancel
				</div>
			</div>
			<div class="small-6 column">
				{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'clubs-orgs-save-button'))}}
			</div>
		</div>

	</div>
</div>

{{ Form::close() }}
</div>
<script language="javascript">
	/* Adds a new bullet point that can be filled*/
	function addBullet(value,rebuild ){
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
			rebuildBulletIndex('#ClubOrgInfoForm');
		}
		doBulletValidation();
		resetBulletForm();

	}

	function resetBulletForm(){
		//re-initializes frontend validation
		$('#ClubOrgInfoForm').foundation({
			abide : {
				patterns : {
					club_year: /^[0-9]+$/,
					bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
				},
				validators:{
					YearCheckClub: function(el, required, parent) {
						
					var MonthTo=months[$("#month_to_club").val()];
					var MonthFrom=months[$("#month_from_club").val()];
					var FromYear=$("#year_from_club").val();
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

$("#icurrentlyworkhere_club").click(function(){
	toggle_current_workplace( $(this), $('#month_to_club'), $('#year_to_club'), 'YearCheckClub', $('.end_date_club'));
});
</script>
