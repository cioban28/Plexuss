@extends('admin.master')
@section('content')
<!-- COLLEGE CONTENT BEGIN -->
<div class='row'>
	<div class='small-12 column no-padding' id='admin_college_content'>
		<!-- OPEN FORM -->
		{{
		Form::open(
			array(
				'url' => 
				isset($college->id) ?
					'/admin/edit/college/' . $college->id . '/stats' :
					'/admin/edit/college/new/stats',
				'method' => 'POST',
				'data-abide',
				'files'=> true
			)
		) 
		}}
		<!-- COLLEGE NAME -->
		<div class='row'>
			<div class='small-2 column'>
			{{
			Form::label(
				'school_name',
				'College Name',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-10 column'>
			{{
			Form::text(
				'school_name',
				isset($college->school_name) ? $college->school_name : null,
				array(
					'id' => 'school_name'
				)
			)
			}}
			</div>
		</div>

		<!-- ADDRESS -->
		<div class='row'>
			<div class='small-2 column'>
			{{
			Form::label(
				'address',
				'Address',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-10 column'>
			{{
			Form::text(
				'address',
				isset($college->address) ? $college->address : null,
				array(
					'id' => 'address'
				)
			)
			}}
			</div>
		</div>

		<! -- PHONE/TOUR/BROCHURE SECTION -->
		<div class='row'>
			<div class='small-7 column'>
				<!-- PHONE -->
				<div class='row'>
					<div class='small-4 column'>
					{{
					Form::label(
						'general_phone',
						'Phone',
						array(
							'class' => 'addNewsLabel'
						)
					)
					}}
					</div>
					<div class='small-8 column'>
					{{
					Form::text(
						'general_phone',
						isset($college->general_phone) ? $college->general_phone : null,
						array(
							'id' => 'phone'
						)
					)
					}}
					</div>
				</div>

				<!-- COLLEGE TOUR URL -->
				<div class='row'>
					<div class='small-4 column'>
					{{
					Form::label(
						'tour_url',
						'College Tour URL',
						array(
							'class' => 'addNewsLabel'
						)
					)
					}}
					</div>
					<div class='small-8 column'>
					{{
					Form::text(
						'tour_url',
						null,
						array(
							'id' => 'tour_url'
						)
					)
					}}
					</div>
				</div>

				<!-- UPLOAD BROCHURE -->
				<div class='row'>
					<div class='small-4 column'>
					{{
					Form::label(
						'brochure',
						'Upload Brochure',
						array(
							'class' => 'addNewsLabel'
						)
					)
					}}
					</div>
					<div class='small-8 column'>
					{{
					Form::file(
						'brochure',
						array(
							'id' => 'brochure'
						)
					)
					}}
					</div>
				</div>
			</div>
			<!-- END PHONE/TOUR/BROCHURE SECTION -->

			<!-- BEGIN LOGO SECTION -->
			<div class='small-5 column no-padding'>
				<div class='row'>
					<div class='small-6 column'>
					{{
					Form::label(
						'logo_url',
						'College Logo',
						array(
							'class' => 'addNewsLabel'
						)
					)
					}}
					{{
					Form::file(
						'logo_url',
						array(
							'id' => 'logo'
						)
					)
					}}
					</div>
					<div class='small-6 column'>
						@if(isset($college->logo_url))
							<img id='admin_school_img' src="{{ $college->logo_prefix . $college->logo_url }}"/>
						@else
							<img id='admin_school_img' src="/images/no_photo.jpg"/>
						@endif
					</div>
				</div>
			</div>
			<!-- END LOGO SECTION -->
		</div>
		<!-- END PHONE/TOUR/BROCHURE ROW -->
		
		<!-- SLUG -->
		<div class='row'>
			<div class='small-2 column'>
			{{
			Form::label(
				'slug',
				'Slug',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-10 column'>
			{{
			Form::text(
				'slug',
				isset($college->slug) ? $college->slug : null,
				array(
					'id' => 'slug'
				)
			)
			}}
			</div>
		</div>
		<!-- PAGE TITLE -->
		<div class='row'>
			<div class='small-2 column'>
			{{
			Form::label(
				'page_title',
				'Page Title',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-10 column'>
			{{
			Form::text(
				'page_title',
				isset($college->page_title) ? $college->page_title : null,
				array(
					'id' => 'page_title'
				)
			)
			}}
			</div>
		</div>
		<!-- META KEYWORDS -->
		<div class='row'>
			<div class='small-2 column'>
			{{
			Form::label(
				'meta_keywords',
				'Meta Keywords',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-10 column'>
			{{
			Form::textarea(
				'meta_keywords',
				isset($college->meta_keywords) ? $college->meta_keywords : null,
				array(
					'id' => 'meta_keywords',
					'rows' => '4'
				)
			)
			}}
			</div>
		</div>
		<!-- META DESCRIPTION -->
		<div class='row'>
			<div class='small-2 column'>
			{{
			Form::label(
				'meta_description',
				'Meta Description',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-10 column'>
			{{
			Form::textarea(
				'meta_description',
				isset($college->meta_description) ? $college->meta_description : null,
				array(
					'id' => 'meta_description',
					'rows' => '4'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>

<!-- SLIDESHOW SECTION -->
<div class='row'>
	<div class='small-12 column admin_section_separator'>
		<span class='addNewsLabel'>
			Slideshow
		</span>
	</div>
</div>
<div class='row'>
	<div class='small-2 column'>
	{{
	Form::file(
		'slideshow',
		array(
			'id' => 'slideshow'
		)
	)
	}}
	</div>
	<!-- SLIDESHOW IMAGE BOXES -->
	<div class='small-10 column admin_section_container'>
		<div class='row'>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box end'>
			</div>
		</div>
		<div class='row'>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box'>
			</div>
			<div class='small-1 column admin_ul_slideshow_box end'>
			</div>
		</div>
	</div>
</div>
<!-- END SLIDESHOW SECTION -->

<!-- TOP BOX SECTION -->
<div class='row'>
	<div class='small-12 column admin_section_separator'>
		<span class='addNewsLabel'>
			Top Box
		</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column admin_section_container'>
		<!-- ADMISSIONS DEADLINE -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'deadline',
				'Admissions Deadline',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'deadline',
				isset($college->deadline) ? $college->deadline : null,
				array(
					'id' => 'admissions_deadline'
				)
			)
			}}
			</div>
		</div>
		<!-- ACCEPTANCE RATE -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'percent_admitted',
				'Acceptance Rate',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'percent_admitted',
				isset($college->percent_admitted) ? $college->percent_admitted : null,
				array(
					'id' => 'acceptance_rate'
				)
			)
			}}
			</div>
		</div>
		<!-- STUDENT BODY SIZE TOTAL -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'student_body_total',
				'Student Body Size Total',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'student_body_total',
				isset($college->student_body_total) ? $college->student_body_total : null,
				array(
					'id' => 'student_body_total'
				)
			)
			}}
			</div>
		</div>
		<!-- STUDENT BODY SIZE UNDERGRAD -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'undergrad_enroll_1112',
				'Student Body Size Undergrad',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'undergrad_enroll_1112',
				isset($college->undergrad_enroll_1112) ? $college->undergrad_enroll_1112 : null,
				array(
					'id' => 'student_body_undergrad'
				)
			)
			}}
			</div>
		</div>
		<!-- TUITION IN STATE -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'tuition_avg_in_state_ftug',
				'Tuition In State',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'tuition_avg_in_state_ftug',
				isset($college->tuition_avg_in_state_ftug) ? $college->tuition_avg_in_state_ftug : null,
				array(
					'id' => 'student_body_undergrad'
				)
			)
			}}
			</div>
		</div>
		<!-- TUITION OUT OF STATE -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'tuition_avg_out_state_ftug',
				'Tuition Out of State',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'tuition_avg_out_state_ftug',
				isset($college->tuition_avg_out_state_ftug) ? $college->tuition_avg_out_state_ftug : null,
				array(
					'id' => 'student_body_undergrad'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- END TOP BOX SECTION -->

<!-- PINS SECTION -->
<div class='row'>
	<div class='small-12 column admin_section_separator'>
		<span class='addNewsLabel'>
			Pins
		</span>
	</div>
</div>
<!-- GRAD RATE -->
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>Graduation Rate</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'graduation_rate_4_year',
				'Four-Year Graduation Rate',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'graduation_rate_4_year',
				isset($college->graduation_rate_4_year) ? $college->graduation_rate_4_year : null,
				array(
					'id' => 'grad_rate'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- GENERAL INFO SECTION -->
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>General Information</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<!-- TYPE -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'school_sector',
				'Type',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'school_sector',
				isset($college->school_sector) ? $college->school_sector : null,
				array(
					'id' => 'type'
				)
			)
			}}
			</div>
		</div>

		<!-- CAMPUS SETTING -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'locale',
				'Campus Setting',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'locale',
				isset($college->locale) ? $college->locale : null,
				array(
					'id' => 'campus_setting'
				)
			)
			}}
			</div>
		</div>

		<!-- CAMPUS HOUSING -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'campus_housing',
				'Campus Housing',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'campus_housing',
				isset($college->campus_housing) ? $college->campus_housing : null,
				array(
					'id' => 'campus_housing'
				)
			)
			}}
			</div>
		</div>
		<!-- RELIGOUS AFFILIATION -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'religious_affiliation',
				'Religious Affiliation',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'religious_affiliation',
				isset($college->religious_affiliation) ? $college->religious_affiliation : null,
				array(
					'id' => 'religous affiliation'
				)
			)
			}}
			</div>
		</div>
		<!-- ACADEMIC CALENDAR -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'calendar_system',
				'Academic Calendar',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'calendar_system',
				isset($college->calendar_system) ? $college->calendar_system : null,
				array(
					'id' => 'academic_calendar'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- GENERAL LINKS -->
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>General Links</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<!-- WEBSITE -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'school_url',
				'Website',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'school_url',
				isset($college->school_url) ? $college->school_url : null,
				array(
					'id' => 'website_url'
				)
			)
			}}
			</div>
		</div>
		<!-- ADMISSIONS -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'admission_url',
				'Admissions',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'admission_url',
				isset($college->admission_url) ? $college->admission_url : null,
				array(
					'id' => 'admissions'
				)
			)
			}}
			</div>
		</div>
		<!-- APPLY ONLINE -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'application_url',
				'Apply Online',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'application_url',
				isset($college->application_url) ? $college->application_url : null,
				array(
					'id' => 'apply_online_url'
				)
			)
			}}
			</div>
		</div>
		<!-- FINANCIAL AID -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'financial_aid_url',
				'Financial Aid',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'financial_aid_url',
				isset($college->financial_aid_url) ? $college->financial_aid_url : null,
				array(
					'id' => 'financial_aid_url'
				)
			)
			}}
			</div>
		</div>
		<!-- NET PRICE CALCULATOR -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'calculator_url',
				'Net Price Calculator',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'calculator_url',
				isset($college->calculator_url) ? $college->calculator_url : null,
				array(
					'id' => 'net_price_calc_url'
				)
			)
			}}
			</div>
		</div>
		<!-- MISSION STATEMENT -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'mission_url',
				'Mission Statement',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'mission_url',
				isset($college->mission_url) ? $college->mission_url : null,
				array(
					'id' => 'mission_statement_url'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- SAT SCORE SECTION -->
<!-- SAT 25TH PERCENTILE SCORE SECTION -->
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>SAT Score</span>
	</div>
</div>
<!-- SAT READ 25 -->
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_read_25',
				'25th Percentile SAT Reading',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_read_25',
					isset($college->sat_read_25) ? $college->sat_read_25 : null,
				array(
					'id' => 'sat_read_25'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- SAT WRITE 25 -->
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_write_25',
				'25th Percentile SAT Writing',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_write_25',
					isset($college->sat_write_25) ?  $college->sat_write_25 : null,
				array(
					'id' => 'sat_write_25'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- SAT MATH 25 -->
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_math_25',
				'25th Percentile SAT Math',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_math_25',
					isset($college->sat_math_25) ?  $college->sat_math_25 : null,
				array(
					'id' => 'sat_math_25'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- SAT 75TH PERCENTILE SCORES -->
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>SAT Score</span>
	</div>
</div>
<!-- SAT READ 75 -->
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_read_75',
				'75th Percentile SAT Reading',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_read_75',
				isset($college->sat_read_75) ? $college->sat_read_75 : null,
				array(
					'id' => 'sat_read_75'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- SAT WRITE 75 -->
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_write_75',
				'75th Percentile SAT Writing',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_write_75',
				isset($college->sat_write_75) ?  $college->sat_write_75 : null,
				array(
					'id' => 'sat_write_75'
				)
			)
			}}
			</div>
		</div>
	</div>
</div> <!-- SAT MATH 75 -->
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_math_75',
				'75th Percentile SAT Math',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_math_75',
				isset($college->sat_math_75) ?  $college->sat_math_75 : null,
				array(
					'id' => 'sat_math_75'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- END SAT SCORES SECTION -->
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>Submitting SAT Scores</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_percent',
				'% Submitting SAT Scores',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_percent',
				isset($college->sat_percent) ? $college->sat_percent : null,
				array(
					'id' => 'sat_percent'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>ACT Score</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'act_composite_25',
				'25th Percentile ACT',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'act_composite_25',
				isset($college->act_composite_25) ? $college->act_composite_25 : null,
				array(
					'id' => 'act_composite_25'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>ACT Score</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'act_composite_75',
				'75th Percentile ACT',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'act_composite_75',
				isset($college->act_composite_75) ? $college->act_composite_75 : null,
				array(
					'id' => 'act_composite_75'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>Submitting ACT Scores</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'act_percent',
				'% Submitting ACT Scores',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'act_percent',
				isset($college->act_percent) ? $college->act_percent : null,
				array(
					'id' => 'act_percent'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>College Total Endowment</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'total_endow',
				'Total Endowment',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'total_endow',
				isset($college->public_endowment_end_fy_12) ? $college->public_endowment_end_fy_12 : (isset($college->private_endowment_end_fy_12) ? $college->private_endowment_end_fy_12 : null),
				array(
					'id' => 'total_endow',
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>Student to Faculty Ratio</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'student_faculty_ratio',
				'Student to Faculty Ratio',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'student_faculty_ratio',
				isset($college->student_faculty_ratio) ? $college->student_faculty_ratio : null,
				array(
					'id' => 'student_faculty_ratio'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<!-- ACCREDITATIONS SECTION -->
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>Accreditations</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<!-- AGENCY -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'accred_agency',
				'Agency',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'accred_agency',
				isset($college->accred_agency) ? $college->accred_agency : null,
				array(
					'id' => 'accred_agency'
				)
			)
			}}
			</div>
		</div>
		<!-- PERIOD OF ACCREDITATION -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'accred_period',
				'Period of Accreditation',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'accred_period',
				isset($college->accred_period) ? $college->accred_period : null,
				array(
					'id' => 'accred_period'
				)
			)
			}}
			</div>
		</div>
		<!-- STATUS -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'accred_status',
				'Status',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'accred_status',
				isset($college->accred_status) ? $college->accred_status : null,
				array(
					'id' => 'accred_status'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>Awards Offered</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-6 column'>
			{{
			Form::label(
				'bachelors_degree',
				"Bachelor's Degree",
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::checkbox(
					'bachelors_degree',
					true,
					isset($college->bachelors_degree) &&
					$college->bachelors_degree == 'Yes' ? true 
					: false,
					array(
						'id' => 'bachelors_degree'
					)
				)
				}}
			</div>
		</div>
		<!-- MASTER'S DEGREE -->
		<div class='row'>
			<div class='small-6 column'>
			{{
			Form::label(
				'masters_degree',
				"Master's Degree",
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::checkbox(
					'masters_degree',
					true,
					isset($college->masters_degree) &&
					$college->masters_degree == 'Yes' ? true
					: false,
					array(
						'id' => 'masters_degree'
					)
				)
				}}
			</div>
		</div>
		<!-- POST MASTER'S CERTIFICATE -->
		<div class='row'>
			<div class='small-6 column'>
			{{
			Form::label(
				'post_masters_degree',
				"Post Master's Certificate",
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::checkbox(
					'post_masters_degree',
					true,
					isset($college->post_masters_degree) &&
					$college->post_masters_degree == 'Yes' ? true
					: false,
					array(
						'id' => 'post_masters_degree'
					)
				)
				}}
			</div>
		</div>
		<!-- DOCTOR'S DEGREE RESEARCH/SCHOLARSHIP -->
		<div class='row'>
			<div class='small-6 column'>
			{{
			Form::label(
				'doctors_degree_research',
				"Doctor's Degree - Research/Scholarship",
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::checkbox(
					'doctors_degree_research',
					true,
					isset($college->doctors_degree_research) &&
					$college->doctors_degree_research == 'Yes' ? true
					: false,
					array(
						'id' => 'doctors_degree_research'
					)
				)
				}}
			</div>
		</div>
		<!-- DOCTOR'S DEGREE PROFESSIONAL PRACTICE -->
		<div class='row'>
			<div class='small-6 column'>
			{{
			Form::label(
				'doctors_degree_professional',
				"Doctor's Degree - Professional Practice",
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::checkbox(
					'doctors_degree_professional',
					true,
					isset($college->doctors_degree_professional) &&
					$college->doctors_degree_professional == 'Yes' ? true
					: false,
					array(
						'id' => 'doctors_degree_professional'
					)
				)
				}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>ROTC</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<! -- AIR FORCE -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'rotc_air',
				'Air Force',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::checkbox(
					'rotc_air',
					true,
					isset($college->rotc_air) &&
					$college->rotc_air == 'Yes' ? true
					: false,
					array(
						'id' => 'rotc_air'
					)
				)
				}}
			</div>
		</div>
		<!-- ARMY -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'rotc_army',
				'Army',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::checkbox(
					'rotc_army',
					true,
					isset($college->rotc_army) &&
					$college->rotc_army == 'Yes' ? true
					: false,
					array(
						'id' => 'rotc_army'
					)
				)
				}}
			</div>
		</div>
		<!-- NAVY -->
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'rotc_navy',
				'Navy',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::checkbox(
					'rotc_navy',
					true,
					isset($college->rotc_navy) &&
					$college->rotc_navy == 'Yes' ? true
					: false,
					array(
						'id' => 'rotc_navy'
					)
				)
				}}
			</div>
		</div>
	</div>
</div>
<!-- END PINS SECTION -->
<!-- COLLEGE CONTENT END -->

<!-- QA/LIVE MARKS -->
<div class='row'>
	<div class='small-12 column'>
		<!-- QA -->
		<div class='row'>
			<div class='small-2 small-offset-9 column'>
				{{
				Form::label(
					'qa',
					'QA Approved',
					array(
						'class' => 'addNewsLabel'
					)
				)
				}}
			</div>
			<div class='small-1 small-offset-11 column'>
				{{
				Form::checkbox(
					'qa',
					true,
					false,
					array(
						'id' => 'qa'
					)
				)
				}}
			</div>
		</div>
		<!-- LIVE -->
		<div class='row'>
			<div class='small-2 small-offset-9 column'>
				{{
				Form::label(
					'live',
					'Ready for Live',
					array(
						'class' => 'addNewsLabel'
					)
				)
				}}
			</div>
			<div class='small-1 small-offset-11 column'>
				{{
				Form::checkbox(
					'live',
					true,
					false,
					array(
						'id' => 'qa'
					)
				)
				}}
			</div>
		</div>

	</div>
</div>
				<!-- PUBLISH/SAVE DRAFT/SUBMIT SECTION -->
                <div class='row'>
                    <div class='large-10 column right'>
						<div class='row'>
							<!-- UNPUBLISH BUTTON -->
							<div class='small-2 column no-padding text-right'>
							@if(isset($news_article['id']))
								@if($news_article['live_status'])
								<div class='underlineButton' id='underlineButtonUnpub' href="#">
									Unpublish
								</div>
								<div class='row' id='underlineButtonUnpubConf'>
									<div class='small-12 column'>
										<a class='underlineButtonConf' href="">
											Really?
										</a>
									</div>
								</div>
								@else
									&nbsp
								@endif
							@endif
							</div>

							<!-- DELETE BUTTON -->
							<div class='small-2 column no-padding text-right'>
							@if(isset($news_article['id']))
								<div class='underlineButton' id='underlineButtonDelete' href="#">
									Delete
								</div>
								<div class='row' id='underlineButtonDeleteConf'>
									<div class='small-12 column'>
										<a class='underlineButtonConf'  href="/admin/delete/news/{{ $news_article['id'] }}">
											Really?
										</a>
									</div>
								</div>
							@else
								&nbsp
							@endif
							</div>


							<!-- PREVIEW BUTTON -->
							<div class='small-4 column no-padding'>
							@if(isset($news_article['id']))
									<a class='button expand' href="/news/article/{{ $news_article['id'] }}">
										Preview
									</a>
							@else
								&nbsp
							@endif
							</div>

							<!-- SAVE/PUBLISH BUTTON -->
							<div class='small-4 column no-padding'>
								{{ Form::submit('Save Draft', array('class'=>'button expand', 'id' => 'formSubmit'))}}
							</div>
						</div>
                    </div>
	
				{{ Form::close() }}
                  <div class="clearfix"></div>
				</div>
@stop
