import * as QUESTION_CONSTANTS from '../College_Application/questionConstants'

export const REVIEW_APP = {

	// Grabs ids from '../College_Application/questionConstants.js'
	additional_information: {

		health: {
			title: 'Health',
			answers: QUESTION_CONSTANTS.ADDTL_HEALTH.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label}; 
			})
		},

		ethnicity: {
			title: 'Ethnicity',
			answers: QUESTION_CONSTANTS.ADDTL_ETHNICITY.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label, options: object.options};
			})
		},

		family: {
			title: 'Family',
			answers: QUESTION_CONSTANTS.ADDTL_STUDENT.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label, options: object.options};
			})
		},

		prior_education: {
			title: 'Prior Education',
			answers: QUESTION_CONSTANTS.ADDTL_PRIOR_EDUCATION.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label};
			})
		},

		secondary_school: {
			title: 'Post Secondary School',
			answers: QUESTION_CONSTANTS.ADDTL_GRAD.map(object => {
				return {id: object.name, label: object.label, options: object.options};
			})
		},

		english_exams: {
			title: 'English Exams',
			answers: QUESTION_CONSTANTS.ADDTL_ENGLISH_EXAMS.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label, options: object.options};
			})
		},

		forms: {
			title: 'Forms',
			answers: QUESTION_CONSTANTS.ADDTL_FORMS.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label};
			})
		},

		finances: {
			title: 'Finances',
			answers: QUESTION_CONSTANTS.ADDTL_FINANCES.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label, options: object.options};
			}) 
		},

		emergency_contact: {
			title: 'Emergency Contact',
			answers: QUESTION_CONSTANTS.ADDTL_CONTACT.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label};
			})
		},

		liberty_university: {
			school_ids: [4124],
			title: 'Liberty University',
			answers: QUESTION_CONSTANTS.SPECIFIC_FOR_SCHOOL_4124.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label}; 
			}).concat( QUESTION_CONSTANTS.LIB_HOUSING.map(object => {
				return {id: object.name, label: object.label}; 
			})).concat( QUESTION_CONSTANTS.IS_CHRISTIAN_YES.map(object => {
				return {id: object.name, label: object.label}; 
			})).concat( QUESTION_CONSTANTS.IS_CHRISTIAN_NO.map(object => {
				return {id: object.name, label: object.label}; 
			}))
		},

		milwaukee_school_of_engineering: {
			school_ids: [4346],
			title: 'Milwaukee School of Engineering',
			answers: QUESTION_CONSTANTS.SPECIFIC_FOR_SCHOOL_4346.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label}; 
			})
		},

		otero_junior_college: {
			school_ids: [663],
			title: 'Otero Junior College',
			answers: QUESTION_CONSTANTS.SPECIFIC_FOR_SCHOOL_663.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label, fields: object.fields}; 
			})
		},

		peralta_community_college: {
			school_ids: [498],
			title: 'Peralta Community College',
			answers: QUESTION_CONSTANTS.SPECIFIC_FOR_SCHOOL_498.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label, options: object.options, fields: object.fields}; 
			})
		},

		university_of_arkansas: {
			school_ids: [148],
			title: 'University of Arkansas',
			answers: QUESTION_CONSTANTS.SPECIFIC_FOR_SCHOOL_148.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label, options: object.options}; 
			})
		},

		devry_university: {
			school_ids: [7726, 188842],
			title: 'Devry University',
			answers: QUESTION_CONSTANTS.SPECIFIC_FOR_AOR_2.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label, options: object.options}; 
			})
		},

		ELS: {
			title: 'English Langauge School',
			answers: QUESTION_CONSTANTS.SPECIFIC_FOR_AOR_5.map(object => {
				return {id: object.name, label: object.label, options: object.options}; 
			})
		},

		immigration: {
			title: 'Immigration',
			answers: QUESTION_CONSTANTS.ADDTL_IMMIGRATION.map(object => {
				return {id: object.name.replace('addtl__', ''), label: object.label}; 
			})
		},

		legal_record: {
			title: 'Legal Record',
			answers: QUESTION_CONSTANTS.ADDTL_DISCIPLINARY.map(object => {
				return { id: object.name.replace('addtl__', ''), label: object.label}; 
			})
		}	

	}

}