// /College_Application/questionConstants.js

/************************************************ below are fields for additional info page */

// BR = Build Radio fields, takes the field name and index of the field you want to be checked by default, so 0 = yes, 1 = no
const vals = ['yes', 'no'];
const BR = (name, default_for_index, custom_ids) => {
	return vals.map((v, i) => {
		return {name, id: custom_ids ? custom_ids[v] : v, is_default: default_for_index === i};
	});
};

const radio_err_msg = 'Must choose yes or no';

const DEGREES = [
	{name: "Certificate", id: 1},
	{name: "Associate's", id: 2},
	{name: "Bachelor's", id: 3},
	{name: "Master's", id: 4},
	{name: "Doctorate", id: 5},
];

const PREF = [
	{name: 'Kakao Talk', id: 'kakao'},
	{name: 'Other', id: 'other'},
	{name: 'Primary Phone (Text Messaging)', id: 'text'},
	{name: 'QQ', id: 'qq'},
	{name: 'Skype', id: 'skype'},
	{name: 'WeChat', id: 'wechat'},
	{name: 'Whatsapp', id: 'whatsapp'},
];

export const ADDTL_PRE_MESSAGE =
	"These colleges require additional information in order for you to complete your application. Please select a college below to fill out the required additional information. You will not be able to submit your application to a college if you do not complete the required fields.";

// -- additional immigration
const EXPIRATIONS = [
	{name: 'visa_type', label: 'Current visa type', field_type: 'text', placeholder: 'Enter visa type'},
	{name: 'visa_expiration', label: 'Date visa expires', field_type: 'date'},
	{name: 'i94_expiration', label: 'Date I-94 expires', field_type: 'date'},
];
export const ADDTL_IMMIGRATION = [
	{name: 'passport_expiration_date', label: 'When does your passport expire?', field_type: 'date', err: 'Cannot leave expiration date empty'},
	{name: 'passport_number', label: 'What is your passport number?', field_type: 'text', placeholder: 'xxx', err: 'Cannot leave passport number empty'},
	{name: 'addtl__living_in_us', label: 'Are you currently living in the US?', field_type: 'radio', fields: BR('living_in_us'), err: radio_err_msg},
];

// -- addition contact
export const ADDTL_CONTACT = [
	{name: 'emergency_contact_name', label: 'Emergency contact name', field_type: 'text', placeholder: 'Emergency contact name', err: 'Cannot leave name empty'},
	{name: 'emergency_phone', label: 'Emergency contact number', field_type: 'phone', alternate_name: 'emergency_phone', err: 'Cannot leave phone empty'},
	{name: 'emergency_contact_address', label: 'Emergency contact address', field_type: 'text', placeholder: 'Emergency contact address', err: 'Cannot leave address empty'},
	{name: 'emergency_contact_email', label: 'Emergency contact email', field_type: 'text', placeholder: 'Emergency contact email', err: 'Cannot leave email empty'},
	{name: 'emergency_contact_relationship', label: 'Emergency contact relationship to you', field_type: 'text', placeholder: 'Emergency contact relationship', err: 'Cannot leave relationship empty'},
	{name: 'home_phone', label: 'Home phone number', field_type: 'phone', alternate_name: 'home_phone', err: 'Cannot leave home phone empty'},
	{name: 'addtl__lived_at_permanent_addr_more_than_6_months', label: 'Have you lived at your permanent address for more than 6 months?', field_type: 'radio', fields: BR('lived_at_permanent_addr_more_than_6_months'), err: radio_err_msg},
	{name: 'addtl__mailing_and_permanent_addr_same', label: 'Is your mailing address the same as your permanent address?', field_type: 'radio', fields: BR('mailing_and_permanent_addr_same'), err: radio_err_msg},
	{name: 'contact_preference', label: 'How would you prefer to be contacted?', options: PREF, field_type: 'select', placeholder: 'Please select an answer', err: 'Must choose contact preference'},
];

// Sponsor Section
const PARENT_WILL_PAY = [
	{name: 'sponsor_will_pay_fname', label: 'Parent First Name', field_type: 'text', placeholder: '', err: 'Cannot leave name empty'},
	{name: 'sponsor_will_pay_lname', label: 'Parent Last Name', field_type: 'text', placeholder: '', err: 'Cannot leave name empty'},
	{name: 'sponsor_will_pay_phone', label: 'Phone Number', alternate_name: 'sponsor_will_pay_phone', field_type: 'phone', placeholder: '', err: 'Cannot leave phone empty'},
	{name: 'sponsor_will_pay_email', label: 'Email Address', field_type: 'email', placeholder: '', err: 'Cannot leave email empty'}
]

const RELATIVE_WILL_PAY = [
	{name: 'sponsor_will_pay_relation', label: 'Relation To You', field_type: 'text', placeholder: '', err: 'Cannot leave relation empty'},
	{name: 'sponsor_will_pay_fname', label: 'Relative First Name', field_type: 'text', placeholder: '', err: 'Cannot leave name empty'},
	{name: 'sponsor_will_pay_lname', label: 'Relative Last Name', field_type: 'text', placeholder: '', err: 'Cannot leave name empty'},
	{name: 'sponsor_will_pay_phone', label: 'Phone Number', alternate_name: 'sponsor_will_pay_phone',  field_type: 'phone', placeholder: '', err: 'Cannot leave phone empty'},
	{name: 'sponsor_will_pay_email', label: 'Email Address', field_type: 'email', placeholder: '', err: 'Cannot leave email empty'}
]

const SPONSOR_WILL_PAY = [
	{name: 'sponsor_will_pay_org_name', label: 'Sponsor Organization Name', field_type: 'text', placeholder: '', err: 'Cannot leave name empty'},
	{name: 'sponsor_will_pay_contact_name', label: 'Contact Name', field_type: 'text', placeholder: '', err: 'Cannot leave name empty'},
	{name: 'sponsor_will_pay_title', label: 'Title', field_type: 'text', placeholder: '', err: 'Cannot leave phone empty'},
	{name: 'sponsor_will_pay_phone', label: 'Phone Number', alternate_name: 'sponsor_will_pay_phone', field_type: 'phone', placeholder: '', err: 'Cannot leave phone empty'},
	{name: 'sponsor_will_pay_email', label: 'Email Address', field_type: 'email', placeholder: '', err: 'Cannot leave email empty'}
]

const SPONSOR_SELECT_OPTIONS = [
	{name: 'Parent', id: 'parent'},
	{name: 'Relative', id: 'relative'},
	{name: 'Sponsor', id: 'sponsor'}
]

const SPONSOR_AGREEMENT_OPT_IN = [
	{name: 'sponsor_will_pay_optin', id: 'sponsor_agreement', label: 'I understand Plexuss or a University Representative may contact my parents, relatives, or sponsors to help facilitate my college application.'}
]

export const SPONSOR_AGREEMENT = [
	{name: 'sponsor_will_pay_agreement', label: '', fields: SPONSOR_AGREEMENT_OPT_IN, field_type: 'checkbox'}
]

export const SPONSOR_SELECT = [
	{name: 'sponsor_will_pay_option', label: 'Choose an option below', field_type: 'select', options: SPONSOR_SELECT_OPTIONS, dependents_parent: PARENT_WILL_PAY, dependents_relative: RELATIVE_WILL_PAY, dependents_sponsor: SPONSOR_WILL_PAY, err: 'Must select a program of interest'},
]
// End of Sponsor Section




// -- additional finances
export const PAY_PLAN = [
	{name: 'My parents and family will be funding my education', id: 'parent_sponsor'},
	{name: 'I will be sponsored by my government or a company', id: 'entity_sponsor'},
	{name: 'My family cannot afford to pay for my entire education, so I will be looking for other ways to fund tuition', id: 'partial_sponsor'},
	{name: "I don't know", id: 'unknown_sponsor'},
];
export const ADDTL_FINANCES = [
	{name: 'financial_plan', label: 'How do you plan to pay for education?', options: PAY_PLAN, field_type: 'select', placeholder: 'Please select an answer', err: 'Must select payment plan'},
	{name: 'addtl__have_sponsor', label: 'Will any part of your education be sponsored?', fields: BR('have_sponsor'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__have_dependents', label: 'Do you have any dependents?', fields: BR('have_dependents'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__have_currency_restrictions', label: 'Are there any currency restrictions in your home country?', fields: BR('have_currency_restrictions'), field_type: 'radio', err: radio_err_msg},
];

// -- additional english exams
export const PEARSON_EXAM = [
	{name: 'took_pearson_versant_exam_date', label: 'Date Taken or Schedule for Pearson Versant exam', field_type: 'date', err: 'Date cannot be left empty'},
	{name: 'took_pearson_versant_exam_score', label: 'Pearson Versant Total Score', placeholder: 'Enter Pearson Versant Score', field_type: 'text', err: 'Score cannot be left empty'},
];

const STUDIED_ENG = [{name: 'num_of_yrs_studied_english_after_hs', label: 'How long have you studied English after high school?', placeholder: 'Enter number of years', field_type: 'text', err: 'Must enter number of years'}];

const LANG_SCHOOL = [
	{name: 'lang_school_completed_current_level', label: 'Completed/Current Level', placeholder: 'Enter completed/completed level', field_type: 'text', err: 'Cannot leave level empty'},
	{name: 'date_attended_lang_school', label: 'Date Attended', field_type: 'date'},
];

const A_GOALS = [
	{name: 'academic_goal__improve_english', id: 'improve_english', label: 'Improve English'},
	{name: 'academic_goal__complete_associate_or_certificate', id: 'complete_associate_or_certificate', label: 'Complete an Associate Degree or Certificate Program'},
	{name: 'academic_goal__meet_transfer_reqs_for_bachelors_degree', id: 'meet_transfer_reqs_for_bachelors_degree', label: 'Meet Transfer Requirements for Bachelor’s Degree'},
	{name: 'academic_goal__prep_for_graduate_school', id: 'prep_for_graduate_school', label: 'Prepare for Graduate School (Masters/PhD)'},
];

export const ADDTL_ENGLISH_EXAMS = [
	{name: 'ielts_date', label: 'Date Taken or Schedule for IELTS exam', field_type: 'date', err: 'Date cannot be left empty'},
	{name: 'toefl_ibt_date', label: 'Date Taken or Schedule for TOEFL iBT exam', field_type: 'date', err: 'Date cannot be left empty'},
	{name: 'addtl__took_pearson_versant_exam', label: 'Have you taken the Pearson Versant exam?', field_type: 'radio', fields: BR('took_pearson_versant_exam'), dependents: PEARSON_EXAM, err: radio_err_msg},
	{name: 'addtl__planning_to_take_esl_classes', label: 'Are you planning to take ESL Classes?', field_type: 'radio', fields: BR('planning_to_take_esl_classes'), dependents: STUDIED_ENG, err: radio_err_msg},
	{name: 'addtl__have_attended_language_school', label: 'Have you attended a U.S. Language School?', field_type: 'radio', fields: BR('have_attended_language_school'), dependents: LANG_SCHOOL, err: radio_err_msg},
	{name: 'addtl__academic_goal', label: 'What is your academic goal? (select all that apply)', field_type: 'checkbox', fields: A_GOALS, err: 'Must choose at least one option'},
];

// -- additional prior education
const HS_STATUS = [
	{name: 'Complete', id: 'complete'},
	{name: 'Discontinued', id: 'discontinued'},
	{name: 'In Progress', id: 'in_progress'},
	{name: 'Incomplete', id: 'incomplete'},
];

export const ADDTL_PRIOR_EDUCATION = [
	{name: 'name_of_hs', label: 'Name of Secondary School/High School Attended', field_type: 'text', placeholder: 'Enter name here...', err: 'Cannot leave name empty'},
	{name: 'city_of_hs', label: 'City of School Attended', field_type: 'text', placeholder: 'Enter city here...', err: 'Cannot leave city empty'},
	{name: 'state_of_hs', label: 'State of School Attended', field_type: 'text', placeholder: 'Enter state here...', err: 'Cannot leave state empty'},
	{name: 'country_of_hs', label: 'Country of School Attended', field_type: 'text', placeholder: 'Select country...', err: 'Cannot leave country empty'},
	{name: 'hs_start_date', label: 'Date You Started Secondary School', field_type: 'date'},
	{name: 'hs_end_date', label: 'Date You Completed or Will Complete Secondary School', field_type: 'date'},
	{name: 'addtl__gap_in_academic_record', label: 'Are there any gaps of 6 months or more in your academic record?', field_type: 'radio', fields: BR('gap_in_academic_record'), err: radio_err_msg},
	{name: 'addtl__attended_additional_institutions', label: 'Have you attended additional educational institutions since leaving secondary school?', field_type: 'radio', fields: BR('attended_additional_institutions'), err: radio_err_msg},
	{name: 'hs_completion_status', label: 'High School Completion Status', options: HS_STATUS, field_type: 'select', placeholder: 'Select status...', err: 'Must select a status'},
	{name: 'addtl__have_graduated_from_a_university', label: 'Have you graduated from a University (Bachelor/Masters)?', field_type: 'radio', fields: BR('have_graduated_from_a_university', {yes: 'Yes, I have graduated from University: Bachelors or Masters / PhD', no: 'No, I have not graduated from University'}), err: radio_err_msg},
	{name: 'addtl__was_instruction_taught_in_english', label: 'Check if instruction was taught in English?', field_type: 'radio', fields: BR('was_instruction_taught_in_english'), err: radio_err_msg},
];

// -- additional legal
export const ADDTL_DISCIPLINARY = [
	{name: 'addtl__academic_misconduct', label: 'Have you ever been subject to disciplinary action or do you currently have a disciplinary charge pending by any educational institution for academic misconduct, such as cheating? (You do not need to disclose any academic dismissal, suspension or probation that was due to poor grades.)', fields: BR('academic_misconduct'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__behavior_misconduct', label: 'Have you ever been subject to disciplinary action or do you currently have a disciplinary charge pending by any educational institution for behavior misconduct, such as fighting? ', fields: BR('behavior_misconduct'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__criminal_offense', label: 'Have you ever been convicted of or charged with a criminal offense, or are you currently the subject of any criminal proceeding?  ', fields: BR('criminal_offense'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__academic_expulsion', label: 'Have you ever been suspended, dismissed, expelled or required to withdraw from any high school or college for academic or disciplinary reasons? (An affirmative response will not automatically prevent admission, but any omission or falsification is grounds for denial or rescission of admission, or expulsion.)', fields: BR('academic_expulsion'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__misdemeanor', label: 'Have you ever been convicted of, or pled guilty or no contest to, a felony or misdemeanor charge? (An affirmative response will not automatically prevent admission, but any omission or falsification is grounds for denial or rescission of admission, or expulsion.)', fields: BR('misdemeanor'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__disciplinary_violation', label: 'Have you ever been found responsible for a disciplinary violation at any postsecondary educational institution you have attended, whether related to academic misconduct or behavioral misconduct, that resulted in a disciplinary action? These actions could include, but are not limited to, probation, suspension, removal, dismissal, or expulsion from the institution.', fields: BR('disciplinary_violation'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__guilty_of_crime', label: 'Have you ever been adjudicated guilty or convicted of a misdemeanor, felony, or other crime? Note that you are not required to answer “yes” to this question, or provide an explanation, if the criminal adjudication or conviction has been expunged, sealed, annulled, pardoned, destroyed, erased, impounded, or otherwise ordered by a court to be kept confidential.', fields: BR('guilty_of_crime'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__have_been_dismissed_from_school_for_disciplinary_reasons', label: 'Have you ever been dismissed from school for academic or disciplinary reasons?', fields: BR('have_been_dismissed_from_school_for_disciplinary_reasons'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__have_used_drugs_last_12_months', label: 'Have you used alcohol, tobacco, or drugs in the last 12 months?', fields: BR('have_used_drugs_last_12_months'), field_type: 'radio', err: radio_err_msg},
];

// -- additional forms
export const I20DATE = [
	{name: 'i20_end_date', label: 'Enter the I-20 End Date', field_type: 'date'},
];
export const ADDTL_FORMS = [
	{name: 'addtl__i20_institution', label: 'Do you have a Form I-20 from another institution in the United States?', fields: BR('i20_institution'), field_type: 'radio', dependents: I20DATE, err: radio_err_msg},
	{name: 'addtl__i20_dependents', label: 'Do you have dependents you would like to add to your I-20?', fields: BR('i20_dependents'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__need_form_i20_for_visa', label: 'Do you need a Form I-20 to apply for a student visa? ', fields: BR('need_form_i20_for_visa'), field_type: 'radio', err: radio_err_msg},
	{name: 'addtl__have_student_visa_and_will_transfer', label: 'Are you in possession of a valid US student visa and would like to transfer from another institution?', fields: BR('have_student_visa_and_will_transfer'), field_type: 'radio', err: radio_err_msg},
];

// -- uploads
export const ADDTL_UPLOADS = [
	{name: 'addtl__uploads', label: 'Additional Uploads -- Upload Passport, etc.', field_type: 'redirect', doc_type: {other: 'other'}, err: 'Must upload passport - choose \'other\' for upload type '},
];

// -- addtional grad school
export const ADDTL_GRAD = [
	{
		name: 'addtl__post_secondary_school_type', label: 'Post-Secondary Edu Info "Type"', field_type: 'select', options: DEGREES, err: 'Must select type',
		tip: [
			'If answer is yes to "Have you attended additional educational institutions since leaving secondary school?"',
			'Secondary School Type:',
			'(High School, Vocational, Foundation Program, Post-Secondary School/University, English Language, Other)',
			'Name of School Attended',
			'City of School Attended',
			'Country of School Attended',
			'Date you started additional schooling',
			'Date you completed or will complete additional schooling',
		],
	},
	{name: 'addtl__post_secondary_name', label: 'Post-Secondary School Name', field_type: 'text', placeholder: 'Post-Secondary School Name', in_college: 1, err: 'Cannot leave name empty'},
	{name: 'addtl__post_secondary_city', label: 'Post-Secondary School City', field_type: 'text', placeholder: 'Post-Secondary School City', err: 'Cannot leave city empty'},
	{name: 'addtl__post_secondary_country', label: 'Post-Secondary School Country', field_type: 'text', placeholder: 'Post-Secondary School Country', err: 'Cannot leave country empty'},
	{name: 'addtl__post_secondary_start_date', label: 'Post-Secondary School Start Date', field_type: 'date'},
	{name: 'addtl__post_secondary_end_date', label: 'Post-Secondary School End Date', field_type: 'date'},
	{name: 'addtl__post_secondary_resume', label: 'Resume Required for Graduates', field_type: 'redirect', doc_type: {resume: 'resume'}, err: 'Must upload a resume'},
];

// -- addtional ethnicity
export const RACE = [
	{name: 'American Indian or Alaska Native', id: 'american_indian_or_alaska_native'},
	{name: 'Asian', id: 'asian'},
	{name: 'Black or African American', id: 'black_or_african_american'},
	{name: 'Native Hawaiian/Other Pacific Islander', id: 'native_hawaiian_or_other_pacific_islander'},
	{name: 'White', id: 'white'},
];

export const ADDTL_ETHNICITY = [
	{name: 'addtl__is_hispanic', label: 'Do you consider yourself to be Hispanic/Latino?', field_type: 'radio', fields: BR('is_hispanic'), err: radio_err_msg},
	{name: 'racial_category', label: 'Select one or more of the following racial categories to describe yourself', field_type: 'select', options: RACE, err: 'Must select a race'},
];

// -- additional health
const ILLNESS = [{name: 'illnesses', label: 'List conditions separated by commas', field_type: 'text', placeholder: 'condition 1, condition 2, etc', err: 'Must list condition(s) since you selected yes'}];

export const ADDTL_HEALTH = [
	{name: 'addtl__have_allergies', label: 'Does the student have any allergies?', field_type: 'radio', fields: BR('have_allergies'), err: radio_err_msg},
	{name: 'addtl__have_medical_needs', label: 'Does the student have any medical needs?', field_type: 'radio', fields: BR('have_medical_needs'), err: radio_err_msg},
	{name: 'addtl__have_dietary_restrictions', label: 'Does the student have any special dietary restrictions?', field_type: 'radio', fields: BR('have_dietary_restrictions'), err: radio_err_msg},
	{name: 'addtl__understand_health_insurance_is_required', label: 'Do you understand that Health Insurance is required?', field_type: 'radio', fields: BR('understand_health_insurance_is_required'), err: radio_err_msg},
	{name: 'addtl__have_any_of_the_following_conditions', label: 'Have you ever had any of the following conditions? Frequent headaches, hearing difficulty, rheumatism/rheumatic fever, heart disease, lung disease, digestive/stomach pain, operation/severe injuries, hernia, arthritis, frequent dizziness/fainting, epilepsy/seizures, high blood pressure, kidney disease, nervousness or other condition. If yes, please list each condition in a comma separated format.', field_type: 'radio', fields: BR('have_any_of_the_following_conditions'), dependents: ILLNESS, err: radio_err_msg},
	{name: 'addtl__have_good_physical_and_mental_health', label: 'Are you in good physical and mental health?', field_type: 'radio', fields: BR('have_good_physical_and_mental_health'), err: radio_err_msg},
];


// -- additional student
const PARENTS_DEG = [
	{name: 'Both', id: 'both'},
	{name: 'Father', id: 'father'},
	{name: 'Mother', id: 'mother'},
	{name: 'Neither', id: 'neither'},
];
const INFLUENCED = [
	{name: 'College Fair', id: 'college_fair'},
	{name: 'Friend', id: 'friend'},
	{name: 'HS Counselor', id: 'hs_counselor'},
	{name: 'Internet', id: 'internet'},
	{name: 'Overseas Advising Center', id: 'overseas_advising_center'},
	{name: 'Other', id: 'other'},
];

export const ADDTL_STUDENT = [
	{name: 'fathers_name', label: 'Father\'s name', field_type: 'text', placeholder: 'Enter father\'s name', err: 'Cannot leave name empty'},
	{name: 'fathers_job', label: 'Father\'s job', field_type: 'text', placeholder: 'Enter father\'s job', err: 'Cannot leave job empty'},
	{name: 'fathers_addr', label: 'Father\'s address', field_type: 'text', placeholder: 'Enter father\'s address', err: 'Cannot leave address empty'},
	{name: 'fathers_city', label: 'Father\'s city', field_type: 'text', placeholder: 'Enter father\'s city', err: 'Cannot leave city empty'},
	{name: 'fathers_district', label: 'Father\'s district', field_type: 'text', placeholder: 'Enter father\'s district', err: 'Cannot leave district empty'},
	{name: 'fathers_country', label: 'Father\'s country', field_type: 'text', placeholder: 'Enter father\'s country', err: 'Cannot leave country empty'},

	{name: 'mothers_name', label: 'Mother\'s name', field_type: 'text', placeholder: 'Enter mother\'s name', err: 'Cannot leave name empty'},
	{name: 'mothers_job', label: 'Mother\'s job', field_type: 'text', placeholder: 'Enter mother\'s job', err: 'Cannot leave job empty'},
	{name: 'mothers_addr', label: 'Mother\'s address', field_type: 'text', placeholder: 'Enter mother\'s address', err: 'Cannot leave address empty'},
	{name: 'mothers_city', label: 'Mother\'s city', field_type: 'text', placeholder: 'Enter mother\'s city', err: 'Cannot leave city empty'},
	{name: 'mothers_district', label: 'Mother\'s district', field_type: 'text', placeholder: 'Enter mother\'s district', err: 'Cannot leave district empty'},
	{name: 'mothers_country', label: 'Mother\'s country', field_type: 'text', placeholder: 'Enter mother\'s country', err: 'Cannot leave country emtpy'},

	{name: 'guardian_name', label: 'Guardian\'s name', field_type: 'text', placeholder: 'Enter guardian\'s name', err: 'Cannot leave name empty'},
	{name: 'guardian_job', label: 'Guardian\'s job', field_type: 'text', placeholder: 'Enter guardian\'s job', err: 'Cannot leave job empty'},
	{name: 'guardian_addr', label: 'Guardian\'s address', field_type: 'text', placeholder: 'Enter guardian\'s address', err: 'Cannot leave address empty'},
	{name: 'guardian_city', label: 'Guardian\'s city', field_type: 'text', placeholder: 'Enter guardian\'s city', err: 'Cannot leave city empty'},
	{name: 'guardian_district', label: 'Guardian\'s district', field_type: 'text', placeholder: 'Enter guardian\'s district', err: 'Cannot leave district empty'},
	{name: 'guardian_country', label: 'Guardian\'s country', field_type: 'text', placeholder: 'Enter guardian\'s country', err: 'Cannot leave country empty'},

	{name: 'parents_have_degree', label: 'Do either of your parents or guardians have a bachelor\'s degree?', field_type: 'select', options: PARENTS_DEG, err: 'Must select an option'},
	{name: 'why_did_you_apply', label: 'What influenced you to apply?', field_type: 'select', options: INFLUENCED, err: 'Must select an option'},
	{name: 'parent_guardian_email', label: 'Parent/Guardian Primary Email', field_type: 'text', placeholder: 'Enter parent/guardian email', err: 'Cannot leave email empty'},
];


// ----- questions for specific schools ------
const DEVRY_FUNDING = [
	{name: 'Financial aid', id: 'financial_aid'},
	{name: 'CNN Institutional Education Partner', id: 'cnn_institutional_education_partner'},
	{name: 'Corporate Education Partner', id: 'corporate_education_partner'},
	{name: 'Full Cash Non-International', id: 'full_cash_non_international'},
	{name: 'Full-Cash Internation', id: 'full_cash_international'},
	{name: 'Military Benefits', id: 'military_benefits'},
	{name: 'DeVry Scholarships', id: 'devry_scholarships'},
	{name: 'Other Scholarships', id: 'other_partner'},
];

// ---------------- EDDY -> aor_id = 1
export const SPECIFIC_FOR_AOR_1 = [];

// ---------------- Devry -> aor_id = 2
export const SPECIFIC_FOR_AOR_2 = [
	{name: 'devry_funding_plan', label: 'How do you plan on funding your education?', field_type: 'select', options: DEVRY_FUNDING, onlyFor: 2, err: 'Must select an option'},
	{name: 'addtl__already_attended_school_of_management_or_nursing', label: 'Have you already attended DeVry University, its Keller Graduate School of Management, Chamberlain College of Nursing?', field_type: 'radio', fields: BR('already_attended_school_of_management_or_nursing'), onlyFor: 2, err: radio_err_msg},
	{name: 'addtl__graduate_of_carrington_or_chamberlain', label: 'Are you a graduate of Carrington College, Carrington College California or Chamberlain College of Nursing?', field_type: 'radio', fields: BR('graduate_of_carrington_or_chamberlain'), onlyFor: 2, err: radio_err_msg},
	{name: 'addtl__will_study_english_prior_to_attending_devry', label: 'Are you planning to study in an intensive English language program prior to attending DeVry University?', field_type: 'radio', fields: BR('will_study_english_prior_to_attending_devry'), onlyFor: 2, err: radio_err_msg},
];

// ---------------- Shorelight -> aor_id = 3
export const SPECIFIC_FOR_AOR_3 = [];

// ---------------- Sparkroom -> aor_id = 4
export const SPECIFIC_FOR_AOR_4 = [];

// ---------------- ELS -> aor_id = 5
const PRO_INTEREST = [
	{name: 'Financial aid', id: 'financial_aid'},
	{name: 'American Explorer', id: 'american_explorer'},
	{name: 'Business English', id: 'business_english'},
	{name: 'English for Academic Purposes', id: 'english_for_academic_purposes'},
	{name: 'General English', id: 'general_english'},
	{name: 'Prep Program for TOEFL iBT (1-session)', id: 'prep_program_for_toefl_ibt_1_session'},
	{name: 'Prep Program for TOEFL iBT (2-session)', id: 'prep_program_for_toefl_ibt_2_session'},
	{name: 'Prep Program for TOEFL iBT (3-session)', id: 'prep_program_for_toefl_ibt_3_session'},
	{name: 'Semi-Intensive English', id: 'semi_intensive_english'},
];

export const SPECIFIC_FOR_AOR_5 = [
	{name: 'program_you_are_interested_in', label: 'Tell us about the program you are interested in:', field_type: 'select', options: PRO_INTEREST, err: 'Must select a program of interest'},
];

// ---------------- MSOE -> org_school_id = 4346
const EP = [
	{name: 'english_profiency_options__taken_toefl', id: 'taken_toefl', label: 'I have taken the TOEFL'},
	{name: 'english_profiency_options__taken_ielts', id: 'taken_ielts', label: 'I have taken the IELTS'},
	{name: 'english_profiency_options__attended_school_in_usa', id: 'attended_school_in_usa', label: 'I attended high school or college in the USA'},
	{name: 'english_profiency_options__english_is_native_language', id: 'english_is_native_language', label: 'English is my native language'},
	{name: 'english_profiency_options__applying_for_conditional_admission', id: 'applying_for_conditional_admission', label: 'I am applying for conditional admission and am still working towards English proficiency'},
	{name: 'english_profiency_options__other', id: 'other', label: 'Other'},
];

const ESL_PROGRAM = [
	{name: 'addtl__english_profiency_options', label: 'What is your academic goal? (select all that apply)', field_type: 'checkbox', fields: EP},
];

export const SPECIFIC_FOR_SCHOOL_4346 = [
	{name: 'addtl__applying_to_esl_program', label: 'Are you applying to the ESL program?', field_type: 'radio', fields: BR('applying_to_esl_program'), dependents: ESL_PROGRAM, err: radio_err_msg},
	{name: 'previous_college_experience', label: 'Previous College Experience (outside MSOE)', field_type: 'text', placeholder: 'Enter previous college experience', err: 'Cannot leave experience empty'},
	{name: 'how_did_you_hear_about_msoe', label: 'How did you hear about MSOE?', field_type: 'text', placeholder: 'Enter source', err: 'Cannot leave this field empty'},
];

// ---------------- Otero -> org_school_id = 663
const SUPPORTED_BY = [
	{name: 'financial_support_provided_by__student', id: 'student', label: 'Student'},
	{name: 'financial_support_provided_by__student_parents', id: 'student_parents', label: 'Student Parent(s)'},
	{name: 'financial_support_provided_by__private_sponsor', id: 'private_sponsor', label: 'Private Sponsor (friend, other relative, company)'},
	{name: 'financial_support_provided_by__govt_scholarship', id: 'govt_scholarship', label: 'Government Scholarship'},
	{name: 'financial_support_provided_by__athletic_scholarship', id: 'athletic_scholarship', label: 'OJC Athletic Scholarship'},
	{name: 'financial_support_provided_by__other', id: 'other', label: 'Other'},
];

export const SPECIFIC_FOR_SCHOOL_663 = [
	{name: 'addtl__financial_support_provided_by', label: 'Financial support for tuition, fees, insurance, books, living expenses, transportation, etc., as noted above will be provided by:', field_type: 'checkbox', fields: SUPPORTED_BY, err: 'Must choose at least one option'}
];

// --------------- Peralta -> org_school_id = 498
const SCHOOL_APPLY = [
	{name: 'Berkeley City College (Berkeley)', id: 'berkeley_city_college'},
	{name: 'College of Alameda (Alameda)', id: 'college_of_alameda'},
	{name: 'Laney College (Oakland)', id: 'laney_college'},
	{name: 'Merritt College (Oakland)', id: 'merritt_college'},
];
const ENROLL_IN = [
	{name: 'plan_to_enroll_in__esl_program', id: 'esl_program', label: 'English as a Second Language (ESL) Program'},
	{name: 'plan_to_enroll_in__academic_program', id: 'academic_program', label: 'Academic Program – Associate/Certificate/Transfer Courses'},
];
const HS_DATE = {name: 'addtl__peralta_have_graduated_on_this_date', label: '', field_type: 'date'};
const GRAD_DATE = {name: 'addtl__peralta_will_graduate_on_this_date', label: '', field_type: 'date'};
const PERALTA_HS = [
	{name: 'have_you_graduated_from_hs__have_graduated_on_this_date', id: 'have_graduated_on_this_date', label: 'Yes, I have graduated from high school on this date:', field_type: 'date', field: HS_DATE},
	{name: 'have_you_graduated_from_hs__will_graduate_on_this_date', id: 'will_graduate_on_this_date', label: 'No, I am still attending high school and will graduate on this date:', field_type: 'date', field: GRAD_DATE},
	{name: 'have_you_graduated_from_hs__no_will_not_graduate', id: 'no_will_not_graduate', label: 'No, I have not graduated from high school and will not graduate before attending the Peralta Colleges'},
];
export const SPECIFIC_FOR_SCHOOL_498 = [
	{name: 'num_of_yrs_planning_on_studying_at_pccd', label: 'How many years do you plan to study at PCCD?', field_type: 'text', placeholder: 'Enter number of years', err: 'Must enter number of years'},
	{name: 'applying_for_admission_school', label: 'I am applying for admission to (location):', field_type: 'select', options: SCHOOL_APPLY, err: 'Must select location'},
	{name: 'addtl__plan_to_enroll_in', label: 'I plan to enroll in (select all that apply):', field_type: 'checkbox', fields: ENROLL_IN, err: 'Must select at least one enroll option'},
	{name: 'addtl__understand_I_need_to_submit_medical_examination_form', label: 'I understand that I must submit proof of vaccinations or submit Medical Examination Form (http://web.peralta.edu/international/how-to-apply/documents-and-forms/)', field_type: 'radio', fields: BR('understand_I_need_to_submit_medical_examination_form'), err: radio_err_msg},
	{name: 'addtl__academic_goals_essay', label: 'Personal Essay: Please describe your academic goals, your plans are after completing this goal and why you have chosen to attend the Peralta Colleges. Give a title. 1-2 pages typed', field_type: 'redirect', doc_type: {essay: 'essay'}, err: 'Must upload an essay document'},
	{name: 'addtl__have_you_graduated_from_hs', label: 'Have you graduated from high school (secondary)?', field_type: 'checkbox', fields: PERALTA_HS, err: 'Must select one of the options'},
];

// --------------- Liberty -> org_school_id = 4124
export const LIB_HOUSING = [
	{name: 'liberty_housing_requirements__residence_hall', id: 'residence_hall', label: 'Residence Hall (Required if single and under the age of 21)'},
	{name: 'liberty_housing_requirements__off_campus', id: 'off_campus', label: 'Off campus (Required if married or age 25 and older)'},
];
export const IS_CHRISTIAN_YES = [
	{name: 'name_of_church', label: 'If yes, name the church you attend; Denomination', field_type: 'text', placeholder: 'Enter church here', err: 'This field cannot be left empty'},
];
export const IS_CHRISTIAN_NO = [
	{name: 'religious_affiliation', label: 'If no, what is your religious affiliation or background?', field_type: 'select', options: [], placeholder: 'Select religious affiliation', err: 'This field cannot be left empty.'},
];
export const SPECIFIC_FOR_SCHOOL_4124 = [
	{name: 'addtl__understand_christian_position_of_liberty', label: 'Do you understand the Christian position of Liberty University?', field_type: 'radio', fields: BR('understand_christian_position_of_liberty'), err: radio_err_msg},
	{name: 'addtl__wish_to_study_at_christian_university', label: 'Do you wish to study at a Christian university and responsibly participate in the life of the campus?', field_type: 'radio', fields: BR('wish_to_study_at_christian_university'), err: radio_err_msg},
	{name: 'addtl__liberty_housing_requirements', label: 'Housing Requirements:', field_type: 'checkbox', fields: LIB_HOUSING, err: 'Must select at least one'},
	{name: 'addtl__are_you_christian', label: 'Are you a Christian?', field_type: 'radio', fields: BR('are_you_christian'), dependents_yes: IS_CHRISTIAN_YES, dependents_no: IS_CHRISTIAN_NO, trigger: 'any', err: radio_err_msg},
	{name: 'addtl__faith_essay', label: 'Essay: Liberty University’s mission is to develop Christ-centered men and women with the values, knowledge, and skills essential to impact tomorrow’s world. Complete a 200-400 word essay answering the following question: How will your personal faith and beliefs contribute to Liberty’s mission to develop Christ-centered leaders? Include your full name, address, and date of birth. PLEASE PRINT LEGIBLY IN BLUE OR BLACK INK ONLY', field_type: 'redirect', doc_type: {essay: 'essay'}, err: 'Must upload a document of type essay.'},
	{
		name: 'addtl__intend_to_follow_code_of_conduct',
		label: 'Do you intend to follow the code of conduct in The Liberty Way, which is summarized in the tooltip?',
		field_type: 'radio',
		fields: BR('intend_to_follow_code_of_conduct'),
		err: radio_err_msg,
		tip: [
			'Liberty University is a regionally accredited and nationally acclaimed university that teaches from a biblical worldview and promotes Christian values and ethics among its students. The university welcomes international students from other faiths but desires that they be aware of Liberty’s position before they apply. Liberty students are expected to participate in the Christian activities on campus, including weekly Convocation and hall meetings. Liberty students enroll in courses designed to help them understand Christianity and to become better Christians. Students are required to participate in Christian/Community Service, devoting a certain amount of time each semester to serving others. They are also required to follow the student code of conduct known as The Liberty Way. Violations of this conduct code can result in fines and possible dismissal from school, so it is important that international students be aware of the code of conduct before submitting an application.',
			'Expected conduct of Liberty students includes, but is not limited to, the following:',
			'- Abstain from using tobacco in any form.',
			'- Abstain from drinking alcohol or using illegal drugs in any form.',
			'- Abstain from entering the residence halls of members of the opposite gender.',
			'- Abstain from inappropriately touching members of the opposite gender.',
			'- Abstain from viewing sexually explicit material or movies.',
			'- Return to assigned residence hall by required curfew each evening.',
			'- Attend each Convocation.',
			'- Wear appropriate clothing.',
			'- Refrain from using inappropriate language.',
			'- Show proper respect for students, faculty, and staff.',
			'- Respect the Christian philosophy held by Liberty, and use the time at the university to better understand the Christian faith.',
			'- Refrain from participating in any distinctively non-Christian religious rituals. Also refrain from displaying distinctively non-Christian religious material on campus.',
		],
	},

];

// --------------- University of Arkansas -> org_school_id = 148
const UOFA_WHO = [
	{name: 'Both', id: 'both'},
	{name: 'Father', id: 'father'},
	{name: 'Mother', id: 'mother'},
	{name: 'Neither', id: 'neither'},
];
const UOFA_CATEGORY = [
	{name: 'Certificate Program', id: 'certificate_program'},
	{name: 'Reciprocal Exchange Student', id: 'reciprocal_exchange_student'},
	{name: 'Visiting Student (degree seeking at another institution)', id: 'visiting_student'},
	{name: 'Non-Degree Seeking', id: 'non_degree_seeking'},
];
export const SPECIFIC_FOR_SCHOOL_148 = [
	{name: 'addtl__seeking_u_of_arkansas_degree', label: 'Do you plan on seeking a degree from the University of Arkansas?', field_type: 'radio', fields: BR('seeking_u_of_arkansas_degree'), err: radio_err_msg},
	{name: 'who_graduated_from_u_of_arkansas', label: 'If no, what special category will you be seeking as a non-degree student?', field_type: 'select', options: UOFA_CATEGORY, err: 'Must select category'},
	{name: 'addtl__previously_attended_u_of_arkansas', label: 'Have you previously attended the University of Arkansas?', field_type: 'radio', fields: BR('previously_attended_u_of_arkansas'), err: radio_err_msg},
	{name: 'addtl__are_graduating_from_hs', label: 'If no, Are you graduating from high school or beginning college for the first time?', field_type: 'radio', fields: BR('are_graduating_from_hs'), nested: 1, err: radio_err_msg},
	{name: 'addtl__will_have_fewer_than_24_transferrable_credits', label: 'If no, Prior to your enrollment at the University of Arkansas, will you have fewer than 24 transferrable college credits earned after graduating high school, including any coursework in progress?', field_type: 'radio', fields: BR('will_have_fewer_than_24_transferrable_credits'), nested: 2, err: radio_err_msg},
	{name: 'addtl__will_have_more_than_24_transferrable_credits', label: 'If no, Prior to your enrollment at the University of Arkansas, will you have more than 24 transferrable college credits earned after graduating high school?', field_type: 'radio', fields: BR('will_have_more_than_24_transferrable_credits'), nested: 3, err: radio_err_msg},
	{name: 'addtl__have_earned_undergrad_grad_pro_degree', label: 'If yes, Have you earned an undergraduate, graduate, or professional degree?', field_type: 'radio', fields: BR('have_earned_undergrad_grad_pro_degree'), nested: 4, err: radio_err_msg},
];


// this list consists of ONLY common question (shared by all schools) - do not include school-specific questions here
export const _getCommonQuestionsAsArray = q => [
	...ADDTL_IMMIGRATION,
	...ADDTL_CONTACT,
	...ADDTL_FINANCES,
	...ADDTL_ENGLISH_EXAMS,
	...ADDTL_PRIOR_EDUCATION,
	...ADDTL_DISCIPLINARY,
	...ADDTL_FORMS,
	...ADDTL_GRAD,
	...ADDTL_ETHNICITY,
	...ADDTL_HEALTH,
	...ADDTL_STUDENT,
];

// this list consists of common + specific questions
export const _getAllQuestionsAsArray = q => [
	...ADDTL_IMMIGRATION,
	...ADDTL_CONTACT,
	...ADDTL_FINANCES,
	...ADDTL_ENGLISH_EXAMS,
	...ADDTL_PRIOR_EDUCATION,
	...ADDTL_DISCIPLINARY,
	...ADDTL_FORMS,
	...ADDTL_GRAD,
	...ADDTL_ETHNICITY,
	...ADDTL_HEALTH,
	...ADDTL_STUDENT,
	...SPECIFIC_FOR_AOR_2,
	...SPECIFIC_FOR_AOR_5,
	...SPECIFIC_FOR_SCHOOL_4346,
	...SPECIFIC_FOR_SCHOOL_663,
	...SPECIFIC_FOR_SCHOOL_498,
	...SPECIFIC_FOR_SCHOOL_4124,
	...SPECIFIC_FOR_SCHOOL_148,
];

// really only used in customQuestionSequel component to sort categories
export const _getCommonQuestionsAsObject = q => {
	return {
		ADDTL_IMMIGRATION,
		ADDTL_CONTACT,
		ADDTL_FINANCES,
		ADDTL_ENGLISH_EXAMS,
		ADDTL_PRIOR_EDUCATION,
		ADDTL_DISCIPLINARY,
		ADDTL_FORMS,
		ADDTL_GRAD,
		ADDTL_ETHNICITY,
		ADDTL_HEALTH,
		ADDTL_STUDENT,
	};
};
