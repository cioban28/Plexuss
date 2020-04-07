// Intl_Resources/constants.js

export const RESOURCES = [
	{
		title: 'Application Checklist',
		icon: 'list',
		route: '/international-resources/application-checklist',
		content: 'There are many different elements to keep track of when completing an admissions application. Some of these will vary from college to college, but many will remain consistent regardless of where you are applying. Plexuss has put together a general checklist—along with definitions of key components—to help ease the often-stressful application process. '
	},
	{
		title: 'Finding the Right School',
		icon: 'find',
		route: '/international-resources/finding-schools',
		content: 'When you are looking for schools to apply to in the United States, it can be difficult to decide where to apply, especially when you cannot easily visit. Plexuss has a list of factors you can consider before choosing which colleges to apply to, as well as a handy college comparison tool to assist you in deciding between schools. '
	},

	{
		title: 'Scholarships and Financial Aid',
		icon: 'aid',
		route: '/international-resources/aid',
		content: 'Financing your education plays a huge role in the American college system. Schools in the US are quite expensive, and many people cannot afford to pay all of heir tuition out of pocket. Fortunately, there are some options for international students to help you cover your school expenses. We have outlined the most helpful and prominent scholarship and financial aid information. '
	},

	{
		title: 'English Proficiency Test Preparation',
		icon: 'prep',
		route: '/international-resources/prep',
		content: 'In order to qualify for college in the United States, you will most likely be required to take English Proficiency Tests, such as the TOEFL and the IELTS, to name two of the most well-known exams. We have put together information for you about each exam, including tips on how to do well on them, and where you can take the exams in your native country. '
	},

	{
		title: 'Working in the United States as a Student',
		icon: 'work',
		route: '/international-resources/working-in-us',
		content: 'The laws for working in the United States as an international student are very specific. It is of the utmost importance that you follow the process for having a job while in school, otherwise your visa will be terminated and you will have to leave the United States immediately. Plexuss has put together the most crucial information that you will need to know about working in the US. '
	},

	{
		title: 'Student Visa and Immigration Center',
		icon: 'visa',
		route: '/international-resources/student-visa',
		content: 'Before you can come to the US and after you are accepted to a university, you will need to apply for your student visa. There is a lot that goes into this process, including completing certain forms, scheduling an interview, and paying a fee. You will want to make sure you have a valid passport before beginning the student visa process. '
	},
];

export const APP_CHECKLIST = [
	{
		title: 'Personal Info',
		list: [
			{content: 'You will need to include your name, email address, phone number, the name of your current school, standardized test scores, citizenship information, and more.'},
		],
	},
	{
		title: 'Extracurriculars',
		list: [
			{content: 'You will be able to include information on what activities you take part in, such as sports, clubs, theatre, music, any jobs or volunteer work, and more.'},
		],
	},
	{
		title: 'Honors',
		list: [
			{content: 'List any honors you have received during the course of your most recent schooling.'},
		],
	},
	{
		title: 'Disciplinary Infractions',
		list: [
			{content: 'Explain any suspensions or expulsions you have received during the course of your most recent schooling.'},
		],
	},
	{
		title: 'Personal Statement/Essay',
		list: [
			{content: 'The personal statement is a type of essay where you can share qualitative information about yourself, such as something that defines who you are as an individual that might help you to stand out to the admissions office.'},
		],
	},
	{
		title: 'Application fee (if there is one)',
		list: [
			{content: 'Most universities in the US will charge a small fee in order to submit your application. You will usually need to pay for this fee after you have filled out the application and before you can press “Submit.” If you cannot afford the application fees, talk to the university’s financial aid office to see if they may assist you.'},
			// {content: 'See how you can save on application fees with Plexuss Premium!', button: {func: "openUpgrade" , text: "See how you can save on application fees with Plexuss Premium!"}},
			// {content: 'See how you can save on application fees with Plexuss Premium!', button: {func: "upgradeOrVisit" , text: "See how you can save on application fees with Plexuss Premium!"}},

		],
	},
];

export const TESTING_CHECKLIST = [
	{
		title: 'Proof of English Proficiency',
		list: [
			{
				title: 'All international applicants whose native language is not English will need to submit proof of English proficiency in one of the following ways. Keep in mind that not every university will accept all of these methods – you will need to find out which are accepted by the universities you apply to:',
				list: [
					{content: 'TOEFL iBT score - the minimum score you need will vary by university', add_or: true},
					{content: 'IELTS score - the minimum score you need will vary by university', add_or: true},
					{content: 'SAT Evidence-Based Reading/Writing score - the minimum score you need will vary by university', add_or: true},
					{content: 'Successful completion of ELS Intensive English Language Program-Level 112'},
				],
			},
			{
				title: 'You will need to send your scores to universities through the exam company’s website before the application due date. Each university that you apply to will have different “school codes” on their application page that you will need to enter in order to send it from the exam company to the university. Keep in mind that it may take some days to send the scores, so it is best if you get it done as soon as possible.',
			}
		],
	}
];

export const SUBMITTED_BY_YOU = [
	{title: "School's Application", list: APP_CHECKLIST},
	{title: "Standardized Testing", list: TESTING_CHECKLIST},
];

export const CHECKLIST_DETAILS = [
	'Take note of each university’s application deadline. You will want to keep a calendar with the deadlines for every university you apply to, as not every university will have the same deadline.',
	'Set a schedule to ensure that you are giving yourself enough time to complete all of your applications in a timely manner. Be sure to keep in mind that some components of your application will take more time, such as standardized test scores and anything submitted by a teacher or guidance counselor.',
];

export const SUBMITTED_BY_PROF = [
	{
		title: "Official High School/Secondary School Transcript",
		list: [
			{title: 'Your transcript is an official record of your work in high school/secondary school that shows the courses you have taken and the grades or marks that you received for each course. You will need to submit the original document in your native language, as well as an officially translated version into English.'},
		],
	},
	{
		title: "Admission Recommendation Letter",
		list: [
			{title: 'This is a letter written by one or two teachers of your choosing that provides insight into your academic prowess. It is best to pick teachers who know you well, so that they can adequately describe your academic intellect as well as who you are as a person.'}
		],
	},
];

export const WORK_DETAILS = [
	{
		title: '',
		list:[
			{
				title: 'Curricular Practical Training (CPT)',
				list: [{title: 'Any type of required internship offered by employers in connection with the student’s institution'}],
			},
			{title: 'Optional Practical Training (OPT)'},
			{title: 'Science, Technology, Engineering, and Mathematics (STEM) Optional Practical Training Extension (OPT)'},
			{title: 'Any off-campus employment must be related to area of study and authorized prior to starting any work by the Designated School Official'}
		]
	}
];

export const IELTS = [
	{
		title: 'IELTS Preparation',
		list:[
			{
				title: 'Take practice exams',
			},
			{
				title: 'Know the sections:',
				list: [
					{title: 'Listening'},
					{title: 'Reading'},
					{title: 'Writing'},
					{title: 'Speaking'},
				],
			},
			{
				title: 'Use Test-Prep books to prepare for IELTS',
				list: [
					{title: 'IELTS is more handwritten-based'},
				],
			},
		]
	}
];

export const TOEFL = [
	{
		title: 'TOEFL Preparation',
		list:[
			{
				title: 'Take practice exams',
			},
			{
				title: 'Know the sections:',
				list: [
					{title: 'Speaking'},
					{title: 'Reading'},
					{title: 'Listening'},
					{title: 'Writing'},
				],
			},
			{
				title: 'Use Test-Prep books to prepare for TOEFL',
				list: [
					{title: 'TOEFL is more computer-based'},
				],
			},
		]
	}
];
