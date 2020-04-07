@extends('admin.master')
@section('content')
<!-- COLLEGE CONTENT BEGIN -->
<div class='row'>
	<div class='small-12 column no-padding' id='admin_college_content'>
		<!-- OPEN FORM -->
		{{
		Form::open(
			array(
				'url' => '/admin/add/college/',
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
				null,
				array(
					'id' => 'school_name',
					'required'
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
				null,
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
						'phone',
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
						'phone',
						null,
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
						'logo',
						'College Logo',
						array(
							'class' => 'addNewsLabel'
						)
					)
					}}
					{{
					Form::file(
						'logo',
						array(
							'id' => 'logo'
						)
					)
					}}
					</div>
					<div class='small-6 column'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/San_Jose_State_University_214791.png'/>
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
				null,
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
				null,
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
				null,
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
				null,
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
				'admissions_deadline',
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
				'admissions_deadline',
				null,
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
				'acceptance_rate',
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
				'acceptance_rate',
				null,
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
				null,
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
				'student_body_undergrad',
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
				'student_body_undergrad',
				null,
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
				'tuition_in_state',
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
				'student_body_undergrad',
				null,
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
				'tuition_out_state',
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
				'student_body_undergrad',
				null,
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
				'grad_rate',
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
				'grad_rate',
				null,
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
				'type',
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
				'type',
				null,
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
				'campus_setting',
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
				'campus_setting',
				null,
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
				null,
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
				'religous_affiliation',
				'Religous Affiliation',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'religous_affiliation',
				null,
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
				'academic_calendar',
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
				'academic_calendar',
				null,
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
				'website_url',
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
				'website_url',
				null,
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
				'admissions_url',
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
				'admissions_url',
				null,
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
				'apply_online_url',
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
				'apply_online_url',
				null,
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
				null,
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
				'net_price_calc_url',
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
				'net_price_calc_url',
				null,
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
				'mission_statement_url',
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
				'mission_statement_url',
				null,
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
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>SAT Score</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_25_pct',
				'25th Percentile SAT',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_25_pct',
				null,
				array(
					'id' => 'sat_25_pct'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<span class='admin_lg_label'>SAT Score</span>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<div class='row'>
			<div class='small-4 column'>
			{{
			Form::label(
				'sat_75_pct',
				'75th Percentile SAT',
				array(
					'class' => 'addNewsLabel'
				)
			)
			}}
			</div>
			<div class='small-8 column'>
			{{
			Form::text(
				'sat_75_pct',
				null,
				array(
					'id' => 'sat_75_pct'
				)
			)
			}}
			</div>
		</div>
	</div>
</div>
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
				'submitting_sat',
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
				'submitting_sat',
				null,
				array(
					'id' => 'submitting_sat'
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
				'act_25_pct',
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
				'act_25_pct',
				null,
				array(
					'id' => 'act_25_pct'
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
				'act_75_pct',
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
				'act_75_pct',
				null,
				array(
					'id' => 'act_75_pct'
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
				'submitting_act',
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
				'submitting_act',
				null,
				array(
					'id' => 'submitting_act'
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
				null,
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
				'stu_fac_ratio',
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
				'stu_fac_ratio',
				null,
				array(
					'id' => 'stu_fac_ratio'
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
				null,
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
				'period_of_accred',
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
				'period_of_accred',
				null,
				array(
					'id' => 'period_of_accred'
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
				null,
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
				<span class='addNewsLabel'>Bachelor's Degree</span>
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'bachelors_deg_y',
				'Yes',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column'>
				{{
				Form::radio(
					'bachelors_deg',
					true,
					false,
					array(
						'id' => 'bachelors_deg_y'
					)
				)
				}}
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'bachelors_deg_n',
				'No',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::radio(
					'bachelors_deg',
					false,
					false,
					array(
						'id' => 'bachelors_deg_n'
					)
				)
				}}
			</div>
		</div>
		<!-- MASTER'S DEGREE -->
		<div class='row'>
			<div class='small-6 column'>
				<span class='addNewsLabel'>Master's Degree</span>
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'masters_deg_y',
				'Yes',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column'>
				{{
				Form::radio(
					'masters_deg',
					true,
					false,
					array(
						'id' => 'masters_deg_y'
					)
				)
				}}
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'masters_deg_n',
				'No',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::radio(
					'masters_deg',
					false,
					false,
					array(
						'id' => 'masters_deg_n'
					)
				)
				}}
			</div>
		</div>
		<!-- POST MASTER'S CERTIFICATE -->
		<div class='row'>
			<div class='small-6 column'>
				<span class='addNewsLabel'>Post Master's Certificate</span>
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'post_masters_y',
				'Yes',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column'>
				{{
				Form::radio(
					'post_masters',
					true,
					false,
					array(
						'id' => 'post_masters_y'
					)
				)
				}}
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'post_masters_n',
				'No',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::radio(
					'post_masters',
					false,
					false,
					array(
						'id' => 'post_masters_n'
					)
				)
				}}
			</div>
		</div>
		<!-- DOCTOR'S DEGREE RESEARCH/SCHOLARSHIP -->
		<div class='row'>
			<div class='small-6 column'>
				<span class='addNewsLabel'>Doctor's Degree - Research/Scholarship</span>
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'doc_deg_research_y',
				'Yes',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column'>
				{{
				Form::radio(
					'doc_deg_research',
					true,
					false,
					array(
						'id' => 'doc_deg_research_y'
					)
				)
				}}
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'doc_deg_research_n',
				'No',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::radio(
					'doc_deg_research',
					false,
					false,
					array(
						'id' => 'doc_deg_research_n'
					)
				)
				}}
			</div>
		</div>
		<!-- DOCTOR'S DEGREE PROFESSIONAL PRACTICE -->
		<div class='row'>
			<div class='small-6 column'>
				<span class='addNewsLabel'>Doctor's Degree - Professional Practice</span>
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'doc_deg_professional_y',
				'Yes',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column'>
				{{
				Form::radio(
					'doc_deg_professional',
					true,
					false,
					array(
						'id' => 'doc_deg_professional_y'
					)
				)
				}}
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'doc_deg_professional_n',
				'No',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::radio(
					'doc_deg_professional',
					false,
					false,
					array(
						'id' => 'doc_deg_professional_n'
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
				<span class='addNewsLabel'>Air Force</span>
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'air_force_y',
				'Yes',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column'>
				{{
				Form::radio(
					'air_force',
					true,
					false,
					array(
						'id' => 'air_force_y'
					)
				)
				}}
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'air_force_n',
				'No',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::radio(
					'air_force',
					false,
					false,
					array(
						'id' => 'air_force_n'
					)
				)
				}}
			</div>
		</div>
		<!-- ARMY -->
		<div class='row'>
			<div class='small-4 column'>
				<span class='addNewsLabel'>Army</span>
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'army_y',
				'Yes',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column'>
				{{
				Form::radio(
					'army',
					true,
					false,
					array(
						'id' => 'army_y'
					)
				)
				}}
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'army_n',
				'No',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::radio(
					'army',
					false,
					false,
					array(
						'id' => 'army_n'
					)
				)
				}}
			</div>
		</div>
		<!-- NAVY -->
		<div class='row'>
			<div class='small-4 column'>
				<span class='addNewsLabel'>Navy</span>
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'navy_y',
				'Yes',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column'>
				{{
				Form::radio(
					'navy',
					true,
					false,
					array(
						'id' => 'navy_y'
					)
				)
				}}
			</div>
			<div class='small-1 column'>
			{{
			Form::label(
				'navy_n',
				'No',
				array(
					'class' => 'admin_radio_label'
				)
			)
			}}
			</div>
			<div class='small-1 column end'>
				{{
				Form::radio(
					'navy',
					false,
					false,
					array(
						'id' => 'navy_n'
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
