// /College_Application/constants.js

/************************************************* shared */

export const MONTH_MAP = {
	1: 'jan',
	2: 'feb',
	3: 'mar',
	4: 'ap',
	5: 'may',
	6: 'jun',
	7: 'jul',
	8: 'aug',
	9: 'sep',
	10: 'oct',
	11: 'nov',
	12: 'dec',
};

export const GET_YEARS = (subFromToday = 0, addToToday = 0) => {
	let date = new Date();
	let this_yr = date.getFullYear() - subFromToday,
		max_yr = date.getFullYear() + addToToday,
		yrs = [];

	for (var i = this_yr; i <= max_yr; i++) {
		yrs = [...yrs, {id: i, name: i}];
	}

	return yrs;
};

export const GET_MONTHS = (val_type = 'number') => {
	let months = [];

	for (var i = 1; i <= 12; i++) {
		let _name = MONTH_MAP[i];
		let val = i;

		if( val_type === 'name_short' ) val = _.capitalize(_name); 
		else if( val_type === 'name_long' ) val = MONTH_MAP[i]; 

		months = [...months, {id: val, name: _name}];
	}

	return months;
};

/************************************************* below are fields for basic info page */

// select
export const EDU = [
	{name: 'High School', id: 0},
	{name: 'College', id: 1},
];
// select
export const DEGREES = [
	{name: "Certificate", id: 1},
	{name: "Associate's", id: 2},
	{name: "Bachelor's", id: 3},
	{name: "Master's", id: 4},
	{name: "Doctorate", id: 5},
];

// select
export const TRANSFER = [
	{name: 'No', id: 0},
	{name: 'Yes', id: 1},
];

export const GENDER_OPTIONS = [
    {name: 'Male', id: 'm'},
    {name: 'Female', id: 'f'},
];

export const FAMILY_INCOME_OPTIONS = [
	{name: '$0 - $30,000', id: '0-30'},
	{name: '$30,001 - $48,000', id: '30-48'},
	{name: '$48,001 - $75,000', id: '48-75'},
	{name: '$75,001 - $110,000', id: '75-110'},
	{name: '$110,001 or more', id: '110-0'},
];

// select, text
export const IN_COLLEGE = {name: 'in_college', label: 'What is your current level of education?', options: EDU, err: 'Must select level of education.'};

export const SCHOOL_NAME = {name: 'schoolName', label: 'Name of School', placeholder: 'Enter name of current school...', err: 'Must enter school name.'};

export const BASIC_SELECT_FIELDS_1 = [
    {name: 'gender', label: 'What is your gender?', options: GENDER_OPTIONS, placeholder: 'Select gender...', err: 'Must choose gender.'},
	{name: 'grad_year', label: 'Your year of graduation?', options: GET_YEARS(10, 10), placeholder: 'Select year...', err: 'Must choose a grad year.'},
	{name: 'degree_id', label: 'What degree are you applying for?', options: DEGREES, placeholder: 'Select degree...', err: 'Must choose a degree.'},
	{name: 'majors_arr', label: 'What major are you applying for?', options: [], placeholder: 'Select major...', select_multiple: true, err: 'Must choose at least one major.'},
];

export const BASIC_SELECT_FIELDS_2 = {name: 'is_transfer', label: 'Are you a transfer student?', options: TRANSFER, placeholder: 'Select...', err: 'Transfer student cannot be left empty.'};

export const ALL_BASICINFO_FIELDS = [
	IN_COLLEGE,
	SCHOOL_NAME,
	...BASIC_SELECT_FIELDS_1,
	BASIC_SELECT_FIELDS_2,
];

// text
export const NAME = [
	{name: 'fname', label: 'First Name', err: 'First Name cannot be empty'},
	{name: 'lname', label: 'Last Name', err: 'Last Name cannot be empty'},
];

// radio
export const ALTERNATE_NAME = [
	{name: 'alternate_name_used', id: 'yes'},
	{name: 'alternate_name_used', id: 'no', is_default: true},
];

// text
export const ALTERNATE_TEXT = {name: 'alternate_name', label: 'Please list the alternate names you have used before. Separate with a comma for multiple names.'};

export const BDAY = {name: 'birth_date'};

/************************************************* below are fields for Contact page */

export const TERM_OPTS = [
	{name: 'Spring', id: 'spring'},
	{name: 'Fall', id: 'fall'},
	{name: 'Summer', id: 'summer'},
];

export const SCHOOL_TYPE_OPTIONS = [
    {name: 'Campus Only', id: '0'},
    {name: 'Online Only', id: '1'},
    {name: 'Both', id: '2'},
    
];

export const START_TERM = {name: 'planned_start_term', label: 'Term', options: TERM_OPTS, placeholder: 'Select term...', err: 'Must select a start term.'};
export const START_YR = {name: 'planned_start_yr', label: 'Year', options: [], placeholder: 'Select year...', err: 'Must select a start year.'};
export const INTERESTED_SCHOOL_TYPE = {name: 'interested_school_type', label: 'I am interested in', options: SCHOOL_TYPE_OPTIONS, placeholder: 'Select one...', err: 'Must select school type' };

export const ALL_START_FIELDS = [
	START_TERM,
	START_YR,
    INTERESTED_SCHOOL_TYPE,
];

/************************************************* below are fields for Contact page */

// text
export const CONTACT = [
	{name: 'email', field_type: 'email', label: 'Email Address', err: 'Email cannot be left empty.'},
	{name: 'skype_id', label: 'Skype', placeholder: 'example.skype.id'},
];

// select, text
export const ADDRESS = [
	{name: 'line1', label: '', placeholder: 'Address Line #1', err: 'Please enter your address.'},
	{name: 'line2', label: '', placeholder: 'Address Line #2'},
	{name: 'city', label: '', placeholder: 'City', err: 'Please enter your city.'},
	{name: 'state_id', label: '', options: [], placeholder: 'Select state', err: 'Please select a state.'},
	{name: 'country_id', label: '', options: [], placeholder: 'Select country', err: 'Please select your country.'},
	{name: 'zip', label: '', placeholder: 'Zip/Postal Code', err: 'Please enter your zip code'},
];

export const ADDRESS_INTL = [
	{name: 'line1', label: '', placeholder: 'Address Line #1', err: 'Please enter your address.'},
	{name: 'line2', label: '', placeholder: 'Address Line #2'},
	{name: 'city', label: '', placeholder: 'City', err: 'Please enter your city.'},
	{name: 'state', label: '', placeholder: 'Enter state/province', err: 'Please enter your state/province.'},
	{name: 'country_id', label: '', options: [], placeholder: 'Select country', err: 'Please select your country.'},
    {name: 'zip', label: '', placeholder: 'Zip/Postal Code', err: 'Please enter your zip code'},
];

// radio
export const PREFERRED_PHONE = [
	{name: 'preferred_phone', id: 'home', label: 'Home'},
	{name: 'preferred_phone', id: 'mobile', label: 'Mobile', is_default: true},
];

// checkbox
export const TOS = {
	name: 'txt_opt_in', 
	id: 'txt_opt_in', 
	label: 'I consent to receive text messages from Plexuss and Universities on the Plexuss network.', 
	link: '/terms-of-service', 
	linkName: 'Privacy Policy',
	is_default: true,
};

// radio
export const PREFERRED_ALTERNATE_PHONE = [
	{name: 'preferred_alternate_phone', id: 'home', label: 'Home'},
	{name: 'preferred_alternate_phone', id: 'mobile', label: 'Mobile'},
	{name: 'preferred_alternate_phone', id: 'none', label: 'No Alternate Phone', is_default: true},
];

// radio
export const ALTERNATE_ADDRESS_PREFERRED = [
	{name: 'alternate_address', id: 'none', label: 'No Alternate Address', is_default: true},
	{name: 'alternate_address', id: 'send', label: 'Send Mail to Alternate Address'},
];

// select, text
export const ALTERNATE_ADDRESS = [
	{name: 'alternate_line1', label: '', placeholder: 'Address Line #1', err: 'Please enter your address.'},
	{name: 'alternate_line2', label: '', placeholder: 'Address Line #2'},
	{name: 'alternate_city', label: '', placeholder: 'City', err: 'Please enter your city.'},
	{name: 'alternate_state_id', label: '', options: [], placeholder: 'Select state...', err: 'Please select your state.'},
	{name: 'alternate_country_id', label: '', options: [], placeholder: 'Select country...', err: 'Please select your country'},
	{name: 'alternate_zip', label: '', placeholder: 'Enter zip'},
];

export const ALTERNATE_ADDRESS_INTL = [
	{name: 'alternate_line1', label: '', placeholder: 'Address Line #1', err: 'Please enter you address.'},
	{name: 'alternate_line2', label: '', placeholder: 'Address Line #2'},
	{name: 'alternate_city', label: '', placeholder: 'City', err: 'Please enter your city.'},
	{name: 'alternate_state', label: '', placeholder: 'Enter state/province', err: 'Please enter your state/province.'},
	{name: 'alternate_country_id', label: '', options: [], placeholder: 'Select country...', err: 'Please select your country.'},
];

export const ALL_CONTACT_FIELDS = [
	...CONTACT,
	...ADDRESS,
	...ADDRESS_INTL,
	...PREFERRED_PHONE,
	...ALTERNATE_ADDRESS,
	...ALTERNATE_ADDRESS_INTL,
	...PREFERRED_ALTERNATE_PHONE,
];

// text
export const V_CODE = {name: 'verification_code', label: '', placeholder: '----', type: 'number', min: 0, max: 9999, err: 'Invalid code. Must be a 4-digit code.'};

// text
export const B_MONTH = {name: 'birthday_month', placeholder: 'Month'}; 
export const B_DAY = {name: 'birthday_day', placeholder: 'Day'}; 
export const B_YR = {name: 'birthday_year', placeholder: 'Year'}; 

/************************************************ below are fields for study page */

export const STUDY_COUNTRIES = {name: 'countries_to_study_in', label: 'What other countries would you like to attend college in?', options: [], placeholder: 'Select country...', select_multiple: true};

/************************************************ below are fields for citizenship page */

// select
export const CTZEN_STATUS = [
	{name: "U.S. Citizenship", id: 1},
	{name: "U.S. Dual Citizenship", id: 2},
	{name: "U.S. Permanent Resident", id: 3},
	{name: "Other", id: 4},
];

// select
export const DUAL_CTZN = {name: 'dual_citizenship_country', label: 'Country (Dual Citizenship)', options: [], placeholder: 'Select country...'};

// select, text
export const CTZENSHIP = [
	{name: 'country_of_birth', label: 'Country of Birth', options: [], placeholder: 'Select country...', err: 'Must select country of birth.'},
	{name: 'city_of_birth', label: 'City of Birth', placeholder: 'Enter name of city', err: 'Must enter city of birth.'},
	{name: 'citizenship_status', label: 'Citizenship Status', options: CTZEN_STATUS, placeholder: 'Select status...', err: 'Must select citizenship status.'},
];

export const LANG = {name: 'languages', label: 'Language', options: [], placeholder: 'Select language...', select_multiple: true, err: 'Must select at least one language.'};

// select, text
export const CTZN_YRS = [
	{name: 'num_of_yrs_in_us', label: 'Number of years you have lived in the United States', placeholder: 'Years in US...', type: 'number', min: 0, err: 'Must enter number of years you have lived in US.'},
	{name: 'num_of_yrs_outside_us', label: 'Number of years you have lived outside of the United States', placeholder: 'Years outside US...', type: 'number', min: 0, err: 'Must enter number of years you have lived outside of US.'},
];

export const ALL_CITIZEN_FIELDS = [
	DUAL_CTZN,
	...CTZENSHIP,
	LANG,
	...CTZN_YRS,
];

/************************************************ below are fields for financials page */

// select
export const FIN_OPTS = [
	{name: '$0', id: '0.00'},
	{name: '$0 - $5,000', id: '0 - 5,000'},
	{name: '$5,000 - $10,000', id: '5,000 - 10,000'},
	{name: '$10,000 - $20,000', id: '10,000 - 20,000'},
	{name: '$20,000 - $30,000', id: '20,000 - 30,000'},
	{name: '$30,000 - $50,000', id: '30,000 - 50,000'},
	{name: '$50,000+', id: '50,000'},
];

// text
export const FINANCIALS = {name: 'financial_firstyr_affordibility', label: '', options: FIN_OPTS, placeholder: 'Select amount...', err: 'Must select your financial affordability.'};
export const AID = {name: 'interested_in_aid', label: 'I am interested in financial aid, grants, and scholarships', is_default: true}

/************************************************ below are fields for gpa page */

export const GPA_FIELDS = [
	// {name: 'weighted_gpa', label: 'Weighted GPA', placeholder: '3.21', err: 'Only values between 0.1 and 5 are accepted.', min: 0.10, max: 5.00, type: 'number'},
	{name: 'gpa', label: '', display_label: 'United States Overall GPA', step: '0.01', placeholder: '', err: 'Only values between 0.1 and 4 are accepted.', min: 0.10, max: 4.00, type: 'text'},
];

export const CONVERSION_GPA_FIELDS = [
	{name: 'gpa_applicant_country', label: '', alternative_label: 'Please select your Country', placeholder: 'Select an option', field_type: 'select', options: []},
	{name: 'gpa_applicant_scale', label: '', alternative_label: 'Select Grading Scale', placeholder: 'Select an option', field_type: 'select', options: []},
	{name: 'gpa_applicant_value', label: '', field_type: '', step: '0.01', placeholder: '', options: []},
	{name: 'gpa', label: '', field_type: 'text', step: '0.01', err: 'Only values between 0.1 and 4 are accepted.'}
];

/************************************************ below are fields for scores page */

export const REPORT = [
	{name: 'self_report', id: 'yes', label: 'Yes', is_default: true},
	{name: 'self_report', id: 'no', label: 'No'},
];

export const ACT = [
	{name: 'act_english', label: 'English: (1-36)', placeholder: '1-36', err: 'Invalid English score. Only numbers between 1-36.', type: 'number', min: 1, max: 36},
	{name: 'act_math', label: 'Math: (1-36)', placeholder: '1-36', err: 'Invalid Math score. Only numbers between 1-36.', type: 'number', min: 1, max: 36},
	{name: 'act_composite', label: 'Composite: (1-36)', placeholder: '1-36', err: 'Invalid Composite score. Only numbers between 1-36.', type: 'number', min: 1, max: 36},
];

export const PRE_16_SAT = [
	{name: 'sat_math', label: 'Math: (200-800)', placeholder: '200-800', err: 'Invalid Math score. Only numbers between 200-800.', type: 'number', min: 200, max: 800},
	{name: 'sat_reading', label: 'Reading: (200-800)', placeholder: '200-800', err: 'Invalid Reading score. Only numbers between 200-800.', type: 'number', min: 200, max: 800},
	{name: 'sat_writing', label: 'Writing: (200-800)', placeholder: '200-800', err: 'Invalid Writing score. Only numbers between 200-800.', type: 'number', min: 200, max: 800},
	{name: 'sat_total', label: 'Total: (600-2400)', placeholder: '600-2400', err: 'Invalid Total score. Only numbers between 600-2400.', type: 'number', min: 600, max: 2400},
];

export const POST_16_SAT = [
	{name: 'sat_math', label: 'Math: (200-800)', placeholder: '200-800', err: 'Invalid Math score. Only numbers between 200-800.', type: 'number', min: 200, max: 800},
	{name: 'sat_reading_writing', label: 'Reading/Writing: (200-800)', placeholder: '200-800', err: 'Invalid Reading/Writing score. Only numbers between 200-800.', type: 'number', min: 200, max: 800},
	{name: 'sat_total', label: 'Total: (400-1600)', placeholder: '400-1600', err: 'Invalid Total score. Only numbers between 400-1600.', type: 'number', min: 400, max: 1600},
];

export const PRE_16_PSAT = [
	{name: 'psat_math', label: 'Math: (20-80)', placeholder: '20-80', err: 'Invalid Math score. Only numbers between 20-80.', type: 'number', min: 20, max: 80},
	{name: 'psat_reading', label: 'Reading: 20-80', placeholder: '20-80', err: 'Invalid Reading score. Only numbers between 20-80.', type: 'number', min: 20, max: 80},
	{name: 'psat_writing', label: 'Writing: 20-80', placeholder: '20-80', err: 'Invalid Writing score. Only numbers between 20-80.', type: 'number', min: 20, max: 80},
	{name: 'psat_total', label: 'Total: (60-240)', placeholder: '60-240', err: 'Invalid Total score. Only numbers between 60-240.', type: 'number', min: 60, max: 240},
];

export const POST_16_PSAT = [
	{name: 'psat_math', label: 'Math: (160-760)', placeholder: '160-760', err: 'Invalid Math score. Only numbers between 160-760.', type: 'number', min: 160, max: 760},
	{name: 'psat_reading_writing', label: 'Reading/Writing: 160-760', placeholder: '160-760', err: 'Invalid Reading/Writing score. Only numbers between 160-760.', type: 'number', min: 160, max: 760},
	{name: 'psat_total', label: 'Total: (320-1520)', placeholder: '320-1520', err: 'Invalid Total score. Only numbers between 320-1520.', type: 'number', min: 320, max: 1520},
];

export const GED = [
	{name: 'ged_score', label: 'GED: (200-800)', placeholder: '200-800', err: 'Invalid GED score. Only numbers between 200-800.', type: 'number', min: 200, max: 800},
];

export const AP = [
	{name: 'ap_overall', label: 'AP: (1-5)', placeholder: '1-5', err: 'Invalid AP score. Only numbers between 1-5.', type: 'number', min: 1, max: 5},
];

export const TOEFL = [
	{name: 'toefl_reading', label: 'Reading: (0-68)', placeholder: '0-68', err: 'Invalid Reading score. Only numbers between 0-68.', type: 'number', min: 0, max: 68},
	{name: 'toefl_listening', label: 'Listening: (0-68)', placeholder: '0-68', err: 'Invalid Listening score. Only numbers between 0-68.', type: 'number', min: 0, max: 68},
	{name: 'toefl_speaking', label: 'Speaking: (0-30)', placeholder: '0-30', err: 'Invalid Speaking score. Only numbers between 0-30.', type: 'number', min: 0, max: 30},
	{name: 'toefl_writing', label: 'Writing: (0-30)', placeholder: '0-30', err: 'Invalid Writing score. Only numbers between 0-30.', type: 'number', min: 0, max: 30},
	{name: 'toefl_total', label: 'Total: (0-90)', placeholder: '0-90', err: 'Invalid Total score. Only numbers between 0-90.', type: 'number', min: 0, max: 90},
];

export const iBT = [
	{name: 'toefl_ibt_reading', label: 'Reading: (0-30)', placeholder: '0-30', err: 'Invalid Reading score. Only numbers between 0-30.', type: 'number', min: 0, max: 30},
	{name: 'toefl_ibt_listening', label: 'Listening: (0-30)', placeholder: '0-30', err: 'Invalid Listening score. Only numbers between 0-30.', type: 'number', min: 0, max: 30},
	{name: 'toefl_ibt_speaking', label: 'Speaking: (0-30)', placeholder: '0-30', err: 'Invalid Speaking score. Only numbers between 0-30.', type: 'number', min: 0, max: 30},
	{name: 'toefl_ibt_writing', label: 'Writing: (0-30)', placeholder: '0-30', err: 'Invalid Writing score. Only numbers between 0-30.', type: 'number', min: 0, max: 30},
	{name: 'toefl_ibt_total', label: 'Total: (0-120)', placeholder: '0-120', err: 'Invalid Total score. Only numbers between 0-120.', type: 'number', min: 0, max: 120},
];

export const PBT = [
	{name: 'toefl_pbt_reading', label: 'Reading: (31-67)', placeholder: '31-67', err: 'Invalid Reading score. Only numbers between 31-67.', type: 'number', min: 31, max: 67},
	{name: 'toefl_pbt_listening', label: 'Listening: (31-68)', placeholder: '31-68', err: 'Invalid Listening score. Only numbers between 31-68.', type: 'number', min: 31, max: 68},
	{name: 'toefl_pbt_written', label: 'Structure/Written Expression: (31-68)', placeholder: '31-68', err: 'Invalid Speaking score. Only numbers between 31-68.', type: 'number', min: 31, max: 68},
	{name: 'toefl_pbt_total', label: 'Total: (310-677)', placeholder: '310-677', err: 'Invalid Total score. Only numbers between 310-677.', type: 'number', min: 310, max: 677},
];

export const IELTS = [
	{name: 'ielts_reading', label: 'Reading: (0-9)', placeholder: '0-9', err: 'Invalid Reading score. Only numbers between 0-9.', type: 'number', min: 0, max: 9},
	{name: 'ielts_listening', label: 'Listening: (0-9)', placeholder: '0-9', err: 'Invalid Listening score. Only numbers between 0-9.', type: 'number', min: 0, max: 9},
	{name: 'ielts_speaking', label: 'Speaking: (0-9)', placeholder: '0-9', err: 'Invalid Speaking score. Only numbers between 0-9.', type: 'number', min: 0, max: 9},
	{name: 'ielts_writing', label: 'Writing: (0-9)', placeholder: '0-9', err: 'Invalid Writing score. Only numbers between 0-9.', type: 'number', min: 0, max: 9},
	{name: 'ielts_total', label: 'Total: (0-9)', placeholder: '0-9', err: 'Invalid Total score. Only numbers between 0-9.', type: 'number', min: 0, max: 9},
];

export const PTE = [
	{name: 'pte_total', label: 'PTE: (10-90)', placeholder: '10-90', err: 'Invalid PTE score. Only numbers between 10-90.', type: 'number', min: 10, max: 90},
];

export const LSAT = [
	{name: 'lsat_total', label: 'LSAT: (120-180)', placeholder: '120-180', err: 'Invalid LSAT score. Only numbers between 120-180.', type: 'number', min: 120, max: 180},
];

export const GMAT = [
	{name: 'gmat_total', label: 'GMAT: (200-800)', placeholder: '200-800', err: 'Invalid GMAT score. Only numbers between 200-800.', type: 'number', min: 200, max: 800},
];

export const GRE = [
	{name: 'gre_verbal', label: 'Verbal: (130-170)', placeholder: '130-170', err: 'Invalid GRE Verbal Reasoning score. Only numbers between 130-170.', type: 'number', min: 130, max: 170},
	{name: 'gre_quantitative', label: 'Quantitative: (130-170)', placeholder: '130-170', err: 'Invalid GRE Quantitative Reasoning score. Only numbers between 130-170.', type: 'number', min: 130, max: 170},
	{name: 'gre_analytical', label: 'Analytical: (0-6)', placeholder: '0-6', err: 'Invalid GRE Analytical Writing score. Only numbers between 0-6.', type: 'number', min: 0, max: 6},
];

export const OTHER = [
	{name: 'other_exam', label: 'Exam Name:', placeholder: 'Exam Name'},
	{name: 'other_values', label: 'Score:', placeholder: 'Score', type: 'number', err: 'You must fill out the score and exam name'},
];

export const SAT_BEFORE = {name: 'is_pre_2016_sat', id: 'yes', label: 'I took the SAT before 2016', placeholder: ''};
export const PSAT_BEFORE = {name: 'is_pre_2016_psat', id: 'yes', label: 'I took the PSAT before 2016', placeholder: ''};

export const US_BTNS = [
	{name: 'ACT', fields: ACT},
	{name: 'SAT', fields: POST_16_SAT, alternateFields: PRE_16_SAT, conditionalQuestion: SAT_BEFORE},
	{name: 'PSAT', fields: POST_16_PSAT, alternateFields: PRE_16_PSAT, conditionalQuestion: PSAT_BEFORE},
	{name: 'LSAT', fields: LSAT},
	{name: 'GMAT', fields: GMAT},
	{name: 'GRE', fields: GRE},
	{name: 'AP Test', fields: AP},
	{name: 'GED', fields: GED},
];

export const INTL_BTNS = [
	{name: 'TOEFL', fields: TOEFL},
	{name: 'TOEFL iBT', fields: iBT},
	{name: 'TOEFL PBT', fields: PBT},
	{name: 'IELTS', fields: IELTS},
	{name: 'PTE Academic', fields: PTE},
	{name: 'Other English Tests', fields: OTHER, canHaveMultiple: true},
];

/************************************************ below are fields for select colleges page */

export const UNQUALIFIED = {
	modal_1: {
		para_1: 'Thank you for completing the first portion of the application. You need to be able to pay a minimum of $5,000 dollars in order to qualify to apply to English Programs, and at least $20,000 dollars to apply to a university',
		para_2: 'If you can afford to pay $5,000(U.S.) dollars or more, click Update to change your financials.',
		para_3: 'Alternatively, you can take free courses from Harvard, MIT, and UC Berkeley by creating an edX account.',
        btn: 'Update Financials',
        links: { edX: 'https://plexuss.com/adRedirect?company=edx&utm_source=oneapp_freecoursestopuni_edxlogo_createaccount&cid=1',
                 harvard: 'https://plexuss.com/adRedirect?company=edx&utm_source=oneapp_freecoursestopuni_harvard_register&cid=1',
                 mit: 'https://plexuss.com/adRedirect?company=edx&utm_source=oneapp_freecoursestopuni_mit_register&cid=1',
                 berkeley: 'https://plexuss.com/adRedirect?company=edx&utm_source=oneapp_freecoursestopuni_ucb_register&cid=1', },
		btn_update: '/college-application/financials'
	},	
	modal_2: {
		para_1: 'Most colleges on our network require you to finance at least $15,000 (U.S. Dollars) per year of your tuition and board.  Unfortunately, you don’t qualify for any program on our network.  If you feel you can afford the sufficient amount, please update your information',
		para_2: 'You can apply to an English program.  These programs range from one month to 12 months and can increase your chances of being accepted to a university.',
		para_3: 'Click Next If you are interested to  enroll in an English program.',
		link_name: 'Update Financials',
		link: '/college-application/financials',
		btn: 'Next'
	},
	modal_3: {
		para_1: 'Looks like you haven\'t taken an English proficiency exam (TOEFL or IELTS).  If you have, it is important to update your scores.',
		para_2: 'Otherwise, we recommend that you enroll in an English program and gain entrance to over 800 universities on our network.  The English certificate is accepted by these universities as a replacement for TOEFL/IELTS.',
		para_3: 'Click Next,  If you are interested to enroll in an English program.',
		link_name: 'Update Scores',
		link: '/college-application/scores',
		btn: 'Next',
	},
};

/************************************************ below are fields for family page */

export const YESNO = [
	{name: 'No', id: 0},
	{name: 'Yes', id: 1},
];

export const FAM_Q = [
	{name: 'married', label: 'Are you married?', options: YESNO, placeholder: 'Select...', err: 'Must select yes or no.'},
	{name: 'children', label: 'Do you have children?', options: YESNO, placeholder: 'Select...', err: 'Must select yes or no.'},
	{name: 'parents_married', label: 'Are your parents married?', options: YESNO, placeholder: 'Select...', err: 'Must select yes or no.'},
	{name: 'siblings', label: 'Do you have any siblings?', options: YESNO, placeholder: 'Select...', err: 'Must select yes or no.'},
];

/************************************************ below are fields for honors and awards page */

export const HAVE_AWARDS = {
	name: 'have_awards', 
	id: 'have_awards',
	label: 'Have you received any honors or awards?',
};

export const AWARDS_Q = [
	{name: 'award_name', label: 'Name of award', placeholder: 'Enter club/organization name...'},
	{name: 'award_accord', label: 'What was it in?', placeholder: 'What was it in?'},
	{name: 'award_received_month', label: 'When did you receive it?', options: GET_MONTHS('name_short'), placeholder: 'Select month...'},
	{name: 'award_received_year', label: '', options: GET_YEARS(20), placeholder: 'Select year...'},
	{name: 'award_notes', label: 'Add any notes', placeholder: 'Notes...'},
];

/************************************************ below are fields for clubs/orgs page */

export const HAVE_CLUBS = {
	name: 'have_clubs', 
	id: 'have_clubs',
	label: 'Were you a part of any organizations or clubs? (List any athletics here as well)',
};

export const CLUBS_Q = [
	{name: 'club_name', label: 'Name of Club/Organization', placeholder: 'Enter club/organization name...'},
	{name: 'club_role', label: 'What was your role?', placeholder: 'Select...'},
	{name: 'club_active_start_month', label: 'When were you active?', options: GET_MONTHS('name_short'), placeholder: 'From Month...'},
	{name: 'club_active_start_year', label: '', options: GET_YEARS(20), placeholder: 'From Year...'},
	{name: 'club_active_end_month', label: '', options: GET_MONTHS('name_short'), placeholder: 'To Month...'},
	{name: 'club_active_end_year', label: '', options: GET_YEARS(20), placeholder: 'To Year...'},
	{name: 'club_notes', label: 'Add any notes', placeholder: 'Select...'},
];

/************************************************ below are fields for uploads page */

export const UPLOAD_Q = [
	{name: 'transcript', label: 'Transcript'},
	{name: 'financial', label: 'Financial'},
	{name: 'resume', label: 'Resume'},
	{name: 'toefl', label: 'TOEFL'},
	{name: 'ielts', label: 'IELTS'},
	{name: 'essay', label: 'Essay'},
	{name: 'passport', label: 'Passport'},
	{name: 'other', label: 'Other'},
];

export const TIPS = [
	'Bank Statement should be provided in the official Bank Letterhead only.',
	'Bank Statement must include the date the letter was issued. For your I-20, your bank statement must be less than 6/12 months old.',
	'Name of account holder. If this is someone other than the student, please state the student\'s name and relationship to the account holder.',
	'Bank Statement must signed by a bank official.',
	'Bank Statement must include the Bank Stamp.',
	'If the Bank Statement contains the amount that has been shown from multiple accounts in the same Bank, hen you need to provide all the Account Numbers.',
	'Some Universities just require last 4 digits of your Account Number, so check with your University. It\'s fine if you give your complete account number.',
];

/************************************************ below are fields for courses page */

export const SCHEDULING = [
	{name: 'quarter'},
	{name: 'semester'},
];

export const LEVEL = [
	{disabled: 'Select...'},
	{name: 'Basic'},
	{name: 'Honors'},
	{name: 'AP'},
];

export const CREDITS = [
	{disabled: 'Select...'},
	{name: '0'},
	{name: '1'},
	{name: '2'},
	{name: '3'},
	{name: '4'},
	{name: '5'},
	{name: '6'},
];

export const GRADE_LVL = [
	{disabled: 'Select...'},
	{name: 'Freshman'},
	{name: 'Sophomore'},
	{name: 'Junior'},
	{name: 'Senior'},
];

/************************************************ below are fields for essay page */

export const ESSAY_MIN = 250;
export const ESSAY_MAX = 650;

export const ESSAY_TOPICS = [
	{name: 'essay_topic', id: 'essay_1', label: 'Some students have a background, identity, interest, or talent that is so meaningful they believe their application would be incomplete without it. If this sounds like you, then please share your story.'},
	{name: 'essay_topic', id: 'essay_2', label: 'The lessons we take from failure can be fundamental to later success. Recount an incident or time when you experienced failure. How did it affect you, and what did you learn from the experience?'},
	{name: 'essay_topic', id: 'essay_3', label: 'Reflect on a time when you challenged a belief or idea. What prompted you to act? Would you make the same decision again?'},
	{name: 'essay_topic', id: 'essay_4', label: 'Describe a problem you’ve solved or a problem you’d like to solve. It can be an intellectual challenge, a research query, an ethical dilemma - anything that is of personal importance, no matter the scale. Explain its significance to you and what steps you took or could be taken to identify a solution.'},
	{name: 'essay_topic', id: 'essay_5', label: 'Discuss an accomplishment or event, formal or informal, that marked your transition from childhood to adulthood within your culture, community, or family.'},
];

/************************************************ below are fields for Demographics  page */


export const DEMOGRAPHIC_FIELDS = [
    {name: 'gender', label: 'Gender', alternative_label: 'Gender', field_type: 'select', placeholder: 'Select gender...', err: 'Must choose gender.', options: GENDER_OPTIONS},

	{name: 'ethnicity', label: 'Ethnicity', alternative_label: 'Ethnicity', err: 'Must choose ethnicity', placeholder: 'Select an option', field_type: 'select', options: []},
	{name: 'religion', label: 'Religion', alternative_label: 'Religion', err: 'Must choose religion', placeholder: 'Select an option', field_type: 'select', options: []},
	{name: 'family_income', label: 'Family income', alternative_label: 'Family income', placeholder: 'Select range', field_type: 'select', err: 'Must choose family income', options: FAMILY_INCOME_OPTIONS}


];


/************************************************ below are fields for additional info page */

/*
	**** YOU WILL FIND ADDITIONAL INFO CONSTANTS IN ./questionConstants ****
*/

/************************************************ below are fields for submit page */

export const TOC = {
	name: 'terms_of_conditions', 
	id: 'terms_of_conditions', 
	label: 'I agree to the', 
	link: '/terms-of-service', 
	linkName: 'Terms and Conditions',
	is_default: true,
};

export const SIGN = {name: 'signature', label: 'Signature', placeholder: 'Sign with Full Name'};
