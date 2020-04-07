// constants.js

//label will be anything and name should be field name - look at collegeInternationalTab model for field names
export const HEADER_FIELDS = [
	{name: 'total_yearly_cost', label: 'Total Yearly Cost', placeholder: '10000'}, 
	{name: 'tuition', label: 'Tuition', placeholder: '10000'}, 
	{name: 'room_board', label: 'Room & Board', placeholder: '10000'}, 
	{name: 'book_supplies', label: 'Books & Supplies', placeholder: '10000'}, 
];

export const ADMISSION_TEXT_FIELDS = [
	{name: 'application_fee', label: 'Application Fee', placeholder: '40'},
	{name: 'num_of_applicants', label: '# of Applicants', placeholder: '10000'},
	{name: 'num_of_admitted', label: '# Admitted', placeholder: '10000', showPercentage: '% Admitted'},
	{name: 'num_of_admitted_enrolled', label: '# Admitted & Enrolled', placeholder: '5000', showPercentage: '% Admitted & Enrolled'},
];

export const ADMISSION_RADIO_FIELDS = [
	{name: 'application_deadline', label: 'Application Deadline'},
	{name: 'admissions_available', label: 'Conditional Admissions Available'},
];

export const SCHOLARSHIP_RADIO_FIELDS = [
	{
		name: 'scholarship_available', 
		label: 'Scholarships Available', 
		directions: "If 'No' or 'Unknown' is selected, only that information will appear and the below information will be hidden."
	},
];

export const SCHOLARSHIP_TEXT_FIELDS = [
	{name: 'scholarship_student_received_aid', label: 'Students who received aid (%)', placeholder: '50', max: 100},
	{name: 'scholarship_avg_financial_aid_given', label: 'Avg. Financial aid given', placeholder: '12000'},
	{name: 'scholarship_requirments', label: 'Scholarship Requirements', placeholder: 'Enter link to scholarship requirements here...', validateType: 'link'},
	{name: 'scholarship_gpa', label: 'GPA', placeholder: '3.5', validateType: 'gpa'},
	{name: 'scholarship_link', label: 'Link for more info', placeholder: 'Insert link for your scholarship info...', validateType: 'link'},
];

export const GPA = [
	{title: 'GPA'},
	{name: 'grade_gpa_min', label: 'Minimum', exam: 'GPA', placeholder: '3.5', validateType: 'gpa'},
	{name: 'grade_gpa_avg', label: 'Average', exam: 'GPA', placeholder: '3.5', validateType: 'gpa'},
];

export const TOEFL = [
	{title: 'TOEFL'},
	{name: 'grade_toefl_min', label: 'Minimum', exam: 'TOEFL'},
	{name: 'grade_toefl_avg', label: 'Average', exam: 'TOEFL'},
];

export const IELTS = [
	{title: 'IELTS'},
	{name: 'grade_ielts_min', label: 'Minimum', exam: 'IELTS', max: 9},
	{name: 'grade_ielts_avg', label: 'Average', exam: 'IELTS', max: 9},
];

export const SAT = [
	{title: 'SAT'},
	{name: 'grade_sat_math_min', label: 'Minimum', exam: 'SAT', sub_label: 'Math'},
	{name: 'grade_sat_math_avg', label: 'Average', exam: 'SAT'},
	{name: 'grade_sat_reading_min', label: 'Minimum', exam: 'SAT', sub_label: 'Reading'},
	{name: 'grade_sat_reading_avg', label: 'Average', exam: 'SAT'},
];

export const PSAT = [
	{title: 'PSAT'},
	{name: 'grade_psat_math_min', label: 'Minimum', exam: 'PSAT', sub_label: 'Math'},
	{name: 'grade_psat_math_avg', label: 'Average', exam: 'PSAT'},
	{name: 'grade_psat_reading_min', label: 'Minimum', exam: 'PSAT', sub_label: 'Reading'},
	{name: 'grade_psat_reading_avg', label: 'Average', exam: 'PSAT'},
];

export const GMAT = [
	{title: 'GMAT'},
	{name: 'grade_gmat_quant_min', label: 'Minimum', exam: 'GMAT', sub_label: 'Quantitative', max: 800},
	{name: 'grade_gmat_quant_avg', label: 'Average', exam: 'GMAT', max: 800},
	{name: 'grade_gmat_verbal_min', label: 'Minimum', exam: 'GMAT', sub_label: 'Verbal', max: 800},
	{name: 'grade_gmat_verbal_avg', label: 'Average', exam: 'GMAT', max: 800},
];

export const ACT = [
	{title: 'ACT'},
	{name: 'grade_act_composite_min', label: 'Minimum', exam: 'ACT', sub_label: 'Composite'},
	{name: 'grade_act_composite_avg', label: 'Average', exam: 'ACT'},
	{name: 'grade_act_math_min', label: 'Minimum', exam: 'ACT', sub_label: 'Math'},
	{name: 'grade_act_math_avg', label: 'Average', exam: 'ACT'},
	{name: 'grade_act_english_min', label: 'Minimum', exam: 'ACT', sub_label: 'English'},
	{name: 'grade_act_english_avg', label: 'Average', exam: 'ACT'},
];

export const GRE = [
	{title: 'GRE'},
	{name: 'grade_gre_writing_min', label: 'Minimum', exam: 'GRE', sub_label: 'Writing'},
	{name: 'grade_gre_writing_avg', label: 'Average', exam: 'GRE'},
	{name: 'grade_gre_verbal_min', label: 'Minimum', exam: 'GRE', sub_label: 'Verbal'},
	{name: 'grade_gre_verbal_avg', label: 'Average', exam: 'GRE'},
	{name: 'grade_gre_quant_min', label: 'Minimum', exam: 'GRE', sub_label: 'Quantitative'},
	{name: 'grade_gre_quant_avg', label: 'Average', exam: 'GRE'},
];

export const REQUIREMENTS = [
	{name: 'visa'},
	{name: 'academic'},
	{name: 'financial'},
	{name: 'other'},
];

export const ALUMNI_NAME_TEXT_FIELD = {
	name: 'alumni_name', 
	label: 'Alumni Name', 
	placeholder: 'Enter name...', 
	validateType: 'required_name'
};

export const ALUMNI_TEXT_FIELDS = [
	{name: 'location', label: 'Home City, Country', placeholder: 'Enter home city, country...', validateType: 'required_text'},
	{name: 'linkedin', label: 'LinkedIn', placeholder: 'Ex: https://www.linkedin.com/in/username', validateType: 'link'},
];

export const ALUMNI_SELECT_FIELDS = [
	{name: 'dep_id', label: ''},
	{name: 'grad_year', label: ''},
];

export const DEPT_OPTIONS = [
	{val: 'all', label: 'All Departments'},
	{val: 'include', label: 'Include'},
];

export const MAJOR_OPTIONS = [
	{val: 'all', label: 'All Majors'},
	{val: 'include', label: 'Include'},
];

export const DEGREE_TYPES = [
	{name: 'certificate', label: "Certificate", value: 1},
	{name: 'associate', label: "Associate's", value: 2},
	{name: 'bachelor', label: "Bachelor's", value: 3},
	{name: 'master', label: "Master's", value: 4},
	{name: 'doctorate', label: "Doctorate", value: 5},
	// {name: 'online', label: "Online", value: 1},
	// {name: 'campus', label: "Campus", value: 0},
];

export const DEPT_OPT = {name: 'department_option', label: 'Choose One Option'};
export const MAJOR_OPT = {name: 'major_option', label: 'Choose One Option'};

export const NOTES = [
	{name: 'title', label: 'Note title:', placeholder: 'Enter the title of this note...', validateType: 'name'},
	{name: 'description', label: 'Note description:', placeholder: 'Enter description of this note...', validateType: 'text'},
	{name: 'attachment_url', label: 'Relevant link:', placeholder: '(Optional) Relevant link/url...', validateType: 'link'},
];

export const YOUTUBE_URL = 'https://www.youtube.com/watch?v=';
export const YOUTUBE_EMBED_START = 'https://www.youtube.com/embed/';
export const YOUTUBE_EMBED_END = '?rel=0&amp;controls=0';
export const VIMEO_EMBED = 'https://player.vimeo.com/video/';
export const VIMEO_URL = 'https://vimeo.com/';

export const EDITOR_NAME = '_notes';

export const REMOVE_TESTIMONIAL_ROUTE = '/admin/tools/international/removeVideoTestimonial';
export const REMOVE_ALUMNI_ROUTE = '/admin/tools/international/removeInternationalAlumni';
export const REMOVE_REQ_ROUTE = '/admin/tools/international/removeInternationalRequirment';
export const REMOVE_REQ_ATTACHMENT_ROUTE = '/admin/tools/international/removeInternationalAttachment';

export const RESET_INTL_MAJORS = 'RESET_INTL_MAJORS';

export const PROGRAMS = [
	{id: 'epp', name: 'English Program'},
	{id: 'undergrad', name: 'Undergraduate Program'},
	{id: 'grad', name: 'Graduate Program'},
];