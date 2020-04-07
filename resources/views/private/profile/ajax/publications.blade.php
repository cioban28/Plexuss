<script type="text/javascript">
//reload zurb items.
$(document).foundation({
	abide : {
		patterns : {
			pub_year: /^[0-9]+$/,
			bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
		}
	}
});
$(function() {
PostPublicationsInfo();
});
</script>
<div class='viewmode' style='display:block;'>

<div class="row">
<div class="large-9 columns paddingleft0">
	<span><img src="../images/i-publications.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Publications </span>
</div>
<div class="large-3 columns paddingleft0">
    <span style="vertical-align:middle;"><a class="add-edit-link"  onclick='addNewPublications();'>Add a publication <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span></a></span>
    
</div>
</div>
<br />


@if(count($publication_data)>0)
	@foreach($publication_data as $key=>$value)
<div class="row form_row">
	<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{$value->title}}</div>
	<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">{{$value->pub_month}} {{$value->pub_day}}, {{$value->pub_year}}</div>
</div>
<hr style="border: solid 1px #1F1F1F;margin:10px 0;" />

<div class="row form_row">
	<div class="large-6 small-12 medium-6 columns black-head16 paddingleft0">{{$value->publication}}</div>
	<div class="large-6 small-12 medium-6 columns black-head16 right_content_set paddingleft0">&nbsp;</div>
</div>

<!-- Bullet Container -->
<div class="row">
	<!-- Description -->
	<div class="large-12 columns label_gray_normal_14">
		<div class='row profile-item-desc'>
			<div class='small-12 column no-padding'>
				{{ $value->pub_description}}
			</div>
		</div>
		<div class='row profile-item-url'>
			<div class='small-12 column no-padding'>
				{{ $value->publication_url}}
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
			<a data-pub-info='{{ htmlspecialchars( json_encode($value), ENT_QUOTES ) }}' class="add-edit-link"  onclick="EditPublications(this);">
				Edit <span><img src="../images/edit_icon.png" border="0" style="cursor:pointer;" align="texttop" /></span>
			</a>
		</span>
		
	</div>
</div>
<br />
@endforeach
@else
<div class="row form_row">
	<div class="large-12 columns paddingleft0">No publications added.</div>
</div>
@endif
</div>


<div class='reveal-modal medium remove_before_ajax' id="addNewPublications" data-reveal>
{{ Form::open(array('url' => "ajax/profile/publications/" , 'method' => 'POST', 'id' => 'PublicationsInfoForm','data-abide'=>'ajax')) }}
{{ csrf_field() }}
{{ Form::hidden('publicationId', null ,array('id'=>'publicationId')) }}
{{ Form::hidden('whocansee','Public',array()) }}
<div class="row form_row">
<div class="large-7 small-12 medium-6 columns paddingleft0">
	<span><img src="../images/i-publications.png" border="0" style="cursor:pointer;" /></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;">Publications</span>
</div>
<!--<div class="large-3 small-12 medium-6 columns paddingleft0 highschoolWhotxt">Who can see this?</div>
<div class="large-2 columns paddingleft0">
<span class="who_can_see_right">{{ Form::select('whocansee', array('Public' => 'Public','Private' => 'Private'),null,array() ) }}</span>
</div>-->
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Title</div>
<div class="large-4 columns paddingleft0">{{ Form::text('title', null , array( 'placeholder' =>'', 'id' => 'title','required'))}}
<small class="error">Please enter a title</small>
</div>
<div class="large-5 columns paddingleft0">&nbsp;</div>
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Publication</div>
<div class="large-9 columns paddingleft0">{{ Form::text('publication', null , array( 'placeholder' =>'', 'id' => 'publication','required'))}}
<small class="error">Please enter a publication</small>
</div>
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Publication URL</div>
<div class="large-9 columns paddingleft0">{{ Form::text('publication_url', null , array( 'placeholder' =>'', 'id' => 'publication_url','required','pattern'=>'url'))}}
<small class="error">Please enter a publication URL</small>
</div>
</div>
<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Publication Date</div>
<div class="large-9 columns paddingleft0">
<div style="float:left;" class="paddingleft0">
	{{ Form::select('pub_month', array('' => 'Month','Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dec'),null,array('class'=>'month_select','required') ) }}
<small class="error">Please select a month</small>
</div>
	<div style="float:left;padding-left:14px;padding-top: 1px;">
		{{ Form::text('pub_day', null , array( 'placeholder' =>'Day', 'id' => 'pub_day', 'class'=>'year_text','required','pattern'=>'pub_year','maxlength'=>'2'))}}
		<small class="error">Please enter a day</small>
	</div>
	<div style="float:left;padding-left:14px;padding-top: 1px;">
		{{ Form::text('pub_year', null , array( 'placeholder' =>'Year', 'id' => 'pub_year', 'class'=>'year_text','required','pattern'=>'pub_year','maxlength'=>'4'))}}
		<small class="error">Please enter a year</small>
	</div>
</div>
</div>

<!-- MODAL BULLET POINTS GO HERE -->
<div class='row'>
	<div id="bullets-heading" class="small-12 large-3 columns model_label_txt">Authors (optional)</div>
	<!-- BULLET CONTAINER -->
	<div class='small-12 large-9 column bullets-parent'>
		<div class='row bullet-parent'>
			<!-- Bullet point form -->
			<div class='small-7 column'>
				{{ Form::text('bullets[0][value]', null, array('class' => 'bullet-input')) }}
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
	<div class="large-offset-3 small-9 column end">
		<div class="add_button" onclick="addBullet('', true);">Add an author</div>
	</div>
</div>

<div class="row form_row">
<div class="large-3 columns model_label_txt paddingleft0">Description <br />(optional)</div>
<div class="large-9 columns paddingleft0">{{ Form::textarea('description', null , array( 'placeholder' =>'My responsibilities included...', 'id' => 'description', 'class'=>'text_area_class'))}}
</div>
</div>

<br />

<!-- cancel/remove/save row -->
<div class="row saveRemoveCancel_row">
	<div class="column small-12 large-push-6 large-6">

		<div class="row">
			<div class="small-6 column close-reveal-modal" onclick="hideRemoveButton();">
				<div class='button btn-cancel'>
					Cancel
				</div>
			</div>
			<div class="small-6 column">
				{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'publications-save-button'))}}
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
		newRow += 				'<input type="text" name="" value="' + value + '" placeholder="" class="bullet-input"/>';
		newRow += 			'</div>';
		newRow += 			'<div class="small-1 column end">';
		newRow += 				'<span class="bullet-closex">&#10006</span>';
		newRow += 			'</div>';
		newRow += 		'</div>';

		$('.bullets-parent').append(newRow);
		if(rebuild){
			rebuildBulletIndex('#PublicationsInfoForm');
		}
		doBulletValidation();
		resetBulletForm();

	}

	function resetBulletForm(){
		//re-initializes frontend validation
		$('#PublicationsInfoForm').foundation({
			abide : {
				patterns : {
					pub_year: /^[0-9]+$/,
					bullet: /^([0-9a-zA-Z\.,#\-\'\(\) ])+$/
				}
			}
		});
	}

	$('#publication_url').change(function(){
		http_helper( $(this) );
	});
</script>
