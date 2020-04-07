           // Profile.js - Reducer

import _ from 'lodash'
import { APP_ROUTES } from './../components/SIC/constants'
import * as CA from './../components/College_Application/constants'
import * as Q from './../components/College_Application/questionConstants'
import {_getRequiredRoutes} from './../components/SIC/constants'
import moment from 'moment'

const EXAMS = [
	CA.ACT,
	CA.PRE_16_SAT,
	CA.POST_16_SAT,
	CA.PRE_16_PSAT,
	CA.POST_16_PSAT,
	CA.GED,
	CA.AP,
	CA.TOEFL,
	CA.iBT,
	CA.PBT,
	CA.IELTS,
	CA.PTE,
	CA.LSAT,
	CA.GMAT,
	CA.GRE,
	CA.OTHER,
];

const ALL_ADDTL = [...Q._getAllQuestionsAsArray()];

const _valueMap = {
	0: true,
	1: true,
};

var init = {
	scholarshipsList: [],
	religions_list: [],
	ethnicities_list: [],
	MyCollegeList: [],
	collegesToRemove: [],
	MyApplicationList: [],
};

export default (state = init, action) => {

	switch( action.type ){

		case '_INTL:ERR':
		case '_PROFILE:ERR':
		case '_PROFILE:SORT':
		case '_PROFILE:PENDING':
		case '_PROFILE:RESET_SAVED':
		case '_PROFILE:INIT_MAJORS':
		case '_PROFILE:INIT_STATES':
		case '_PROFILE:SAVE_PENDING':
		case '_PROFILE:INIT_LANGUAGES':
		case '_PROFILE:MAJORS_PENDING':
		case '_PROFILE:STATES_PENDING':
		case '_PROFILE:INIT_COUNTRIES':
		case '_PROFILE:SEARCH_SCHOOLS':
		case '_PROFILE:REMOVE_PENDING':
		case '_PROFILE:REMOVED_COURSE':
		case '_PROFILE:COUNTRIES_PENDING':
		case '_PROFILE:APPLICATION_SAVED':
		case '_PROFILE:GET_RELIGION_DONE':
		case '_PROFILE:GET_CLASSES_PENDING':
		case '_PROFILE:GET_COURSE_SUBJECTS':
		case '_PROFILE:INIT_PROFILE_PENDING':
		case '_PROFILE:INIT_ATTENDED_SCHOOLS':
		case '_PROFILE:GET_GRADE_CONVERSIONS':
		case '_PROFILE:CONFIRMATION_CODE_SENT':
		case '_PROFILE:SEND_CONF_CODE_PENDING':
		case '_PROFILE:VERIFY_CONF_CODE_PENDING':
		case '_PROFILE:CONFIRMATION_CODE_SENT_ERR':
		case '_PROFILE:CONFIRMATION_CODE_VERIFIED':
		case '_PROFILE:GET_CLASSES_BASED_ON_SUBJECT':
		case '_PROFILE:INIT_GRADING_SCALES':
		case '_PROFILE:CLEAR_CHANGED_FIELDS':
		case '_PROFILE:SEARCH_MAJORS':
		case '_PROFILE:SAVE_ME_START':
		case '_PROFILE:SAVE_ME_ERR':
		case '_PROFILE:SAVE_ME_DONE':
		case '_PROFILE:GET_UPLOADS_START':
		case '_PROFILE:GET_UPLOADS_ERR':
		case '_PROFILE:GET_UPLOADS_DONE':
		case '_PROFILE:ME_REMOVE_UPLOAD_PENDING':
		case '_PROFILE:ME_REMOVE_UPLOAD_ERR':
		case '_PROFILE:SEARCH_SCHOOL_NAME':
		case '_PROFILE:SEARCH_JOBS':
		case '_PROFILE:INIT_SCHOLARSHIPS_PENDING':
		case '_PROFILE:INIT_SCHOLARSHIPS_DONE':

			return {...state, ...action.payload};

		case '_PROFILE:ADD_COLLEGE_TO_MY_COLLEGES_LIST':{
			let newState = {...state}
			let college = action.payload.college
			if(!!college.id){
				college.college_id = college.id;
				college.logo_url = college.image;
				college.school_name = college.label;
			}
			let x = newState.MyCollegeList.filter( x =>  x.college_id === college.college_id)
			if(x.length < 1){
				newState.MyCollegeList.push(college)
			}
			return newState
		}

		case '_PROFILE:ADD_COLLEGE_TO_MY_APPLICATION_LIST':{
			let newState = {...state}
			let college = action.payload.college
			if(!!college.id){
				college.college_id = college.id;
				college.logo_url = college.image;
				college.school_name = college.label;
			}
			let x = newState.MyApplicationList.filter( x =>  x.college_id === college.college_id)
			if(x.length < 1){
			newState.MyApplicationList.push(college)
			}
			return newState
		}

		case '_PROFILE:REMOVE_COLLEGE_FROM_MY_COLLEGES_LIST':{
			let newState = {...state}
			if(newState.MyCollegeList.indexOf(action.payload.college) === -1){
				newState.MyCollegeList.pop(action.payload.college)
			}
			return newState
		}

		case '_PROFILE:REMOVE_COLLEGE_FROM_MY_APPLICATIONS_LIST': {
			let newState = {...state},
				index = newState.MyApplicationList.indexOf(action.payload.college);

			index >= 0 && newState.MyApplicationList.splice(index, 1)

			return newState
		}


        case '_PROFILE:TOGGLE_SELECTED_SCHOLARSHIP': {
            const newState = {...state};
            const scholarship = action.payload.scholarship;
            const scholarshipsList = _.isEmpty(newState.scholarshipsList) ? [] : newState.scholarshipsList;

            let newScholarshipsList = scholarshipsList.slice();

            if (_.isEmpty(scholarship)) {
                return newState;
            }

            const found = _.findIndex(newScholarshipsList, { id: scholarship.id });

            if (found === -1) {
                newScholarshipsList.push(scholarship);
            } else {
                newScholarshipsList = _.filter(newScholarshipsList, (sch) => sch.id !== scholarship.id);
            }

            newState['scholarshipsList'] = newScholarshipsList;

            return newState;
				}


        case '_PROFILE:UPDATE_PUBLICATIONS': {
            const newState = {...state};
            const payload = action.payload;

            const newPublication = payload.newPublication;

            const projectsAndPublications = !_.isEmpty(newState.projectsAndPublications)
                ? newState.projectsAndPublications
                : [];

            projectsAndPublications.push(newPublication);

            newState['projectsAndPublications'] = projectsAndPublications;
            newState['addPublicationPending'] = payload.addPublicationPending;
            newState['addPublicationStatus'] = payload.addPublicationStatus;

            return newState;
        }

        case '_PROFILE:SAVED_PROFILE_PICTURE': {
            const newState = {...state};
            const payload = action.payload;
            const data = payload.data;

            newState['uploadProfilePicturePending'] = payload.uploadProfilePicturePending;

            if (data.status === 'success') {
                newState['profile_img_loc'] = data.profile_img_loc;
            }

            return newState;
        }

        case '_PROFILE:UPDATE_SKILLS_AND_ENDORSEMENTS_SECTION': {
            const newState = {...state};
            const payload = action.payload;
            newState['getSkillsAndEndorsementsPending'] = payload.getSkillsAndEndorsementsPending;
            newState['skillsAndEndorsementsList'] = payload.response;

            return newState;
        }
        case '_PROFILE:UPDATE_RELIGIONS': {
        	const newState = {...state};
        	const payload = action.payload;
        	var output = [], item;
        	var input=action.payload.religions_list
        	for (var index in input) {
					    item = {};
					    item.id = index;
					    item.name = input[index];
					    output.push(item);
					}
					if ( !!output && output.length > 0 ) {
        		newState.religions_list.push(output);
        	}

        	return newState;
        }
        case '_PROFILE:UPDATE_ETHNICITIES': {
        	const newState = {...state};
        	const payload = action.payload;
					if ( !!payload.ethnicities_list && payload.ethnicities_list.length > 0 ) {
        		newState.ethnicities_list.push(payload.ethnicities_list);
        	}

        	return newState;
        }
        case '_PROFILE:UPDATE_CLAIM_TO_FAME_SECTION': {
            let newState = {...state};
            const payload = action.payload;
            const response = payload.response;

            newState = {...newState, ...response};

            newState['getProfileClaimToFamePending'] = payload.getProfileClaimToFamePending;

            if (response.claimToFameYouTubeVideoUrl) {
                newState['claimToFameVideoUrl'] = response.claimToFameYouTubeVideoUrl;
            } else if (response.claimToFameVimeoVideoUrl) {
                newState['claimToFameVideoUrl'] = response.claimToFameVimeoVideoUrl;
            }

            return newState;
        }

        case '_PROFILE:SAVED_CLAIM_TO_FAME_SECTION': {
            const newState = {...state};
            const payload = action.payload;
            const data = payload.data;

            newState['claimToFameDescription'] = data.description;
            newState['claimToFameYouTubeVideoUrl'] = data.youtube_url;
            newState['claimToFameVimeoVideoUrl'] = data.vimeo_url;
            newState['saveClaimToFameSectionPending'] = payload.saveClaimToFameSectionPending;

            if (data.youtube_url) {
                newState['claimToFameVideoUrl'] = data.youtube_url;
            } else if (data.vimeo_url) {
                newState['claimToFameVideoUrl'] = data.vimeo_url;
            }

            return newState;
        }
        case '_PROFILE:UPDATE_PROFILE_EDUCATION': {
            let newState = {...state};
            const payload = action.payload;

            newState['getProfileEducationPending'] = payload.getProfileEducationPending;
            newState['education'] = [...payload.education];

            if (payload.saveProfileEducation !== null ) {
                newState['saveProfileEducation'] = payload.saveProfileEducation;
            }

            return newState;
        }

        case '_PROFILE:REMOVE_PUBLICATION': {
            const newState = {...state};
            const payload = action.payload;
            const publication_id = payload.publication_id;

            const projectsAndPublications = !_.isEmpty(newState.projectsAndPublications)
                ? newState.projectsAndPublications
                : [];

            const newProjectsAndPublications = _.filter(projectsAndPublications, (publication) => publication.id !== publication_id);

            newState['projectsAndPublications'] = newProjectsAndPublications;
            newState['removePublicationPending'] = payload.removePublicationPending;
            newState['removePublicationStatus'] = payload.removePublicationStatus;

            return newState;
        }
        case '_PROFILE:UPDATE_LIKED_COLLEGES': {
            const newState = {...state};
            const payload = action.payload;

            const newLikedColleges = _.filter(newState.likedColleges, (college) => college.likes_tally_id !== payload.college.likes_tally_id);

            newState['likedColleges'] = newLikedColleges;
            newState['removeLikedCollegePending'] = payload.removeLikedCollegePending;
            newState['removeLikedCollegeStatus'] = payload.removeLikedCollegeStatus;

            return newState;
        }
		case '_PROFILE:ME_REMOVED_UPLOAD_DONE': {
			let newState = {...state};

			_.remove(newState.transcript, (item) =>{
				return item.id == action.payload.transcriptID;
			})

			newState.remove_pending = action.payload.remove_pending;

			return newState;

		}
		case '_PROFILE:UPDATE_TRANSCRIPTS_MEPAGE': {
			let newState = {...state};

			newState.transcript.push(action.payload.transcript);

			newState.upload_pending = action.payload.upload_pending;

			return newState;
		}
		case '_PROFILE:CHANGED_FIELDS':
			let newState = {...state};


			if(typeof newState.changedFields == 'undefined')
				newState.changedFields = [];

			if(newState.changedFields.indexOf(action.payload) === -1)
				newState.changedFields.push(action.payload);

			return 	newState;

		case '_PROFILE:REMOVED_UPLOAD':
			var newState = {...state, ...action.payload};
			var allPagesValidated = checkIfAllPagesAreCompleteBeforeSubmitting(newState);

			return {
				...newState,
				...allPagesValidated
			};

		case '_PROFILE:VALIDATE_PAGE':
			var newState = {...state, ...action.payload},
				page = newState.page;

			if (page)
				newState[page + '_form_done'] = !!determineCompletion(page, newState);

			return newState;

		case '_PROFILE:UPDATE_FILE_UPLOADS':
			var newState = {...state},
				page = newState.page,
				transcripts = action.payload.data.transcripts,
				// If it has a file size, it is new. (Not uploaded yet)
				notUploadedFiles = _.filter(transcripts, file => file.size),
				duplicates = checkForFileDuplicates(newState, notUploadedFiles),
				newFiles = _.filter(notUploadedFiles, file => duplicates.indexOf(file.name) === -1),
				noDuplicates = _.reject(transcripts, file => file.size && duplicates.indexOf(file.name) > -1),
				newFileNames = _.reduce(newFiles, (result, file) => result.concat(file.name), []);

			if (page == 'uploads') {
				newState['uploaded_file_names']
					? newState['uploaded_file_names'] = newState['uploaded_file_names'].concat(newFileNames)
					: newState['uploaded_file_names'] = newFileNames;
			}

			newState['transcripts'] = noDuplicates;
			return {...newState, is_duplicate_upload: duplicates.length != 0 };

		case '_PROFILE:TOGGLE_EXAM_REPORTING':
			var newState = {...state},
				data = action.payload.data,
				skip_score_saving = data.skip_score_saving,
				route = data.route,
				validatePages = {};

			if (skip_score_saving) {
				newState[route.id + '_form_done'] = true;
			} else {
				newState[route.id + '_form_done'] = !!determineCompletion(route.id, newState);
			}

			return {...newState, skip_score_saving};

		case '_PROFILE:INIT':
			var newState = {...state, ...action.payload},
				collegesWithCustomQuestions = _.filter(newState.applyTo_schools, college => !!college.custom_questions);

			if( newState.req_app_routes ){
				var allPagesValidated = checkIfAllPagesAreCompleteBeforeSubmitting(newState);

				newState = {
					...newState,
					...allPagesValidated,
				};
			}else{
				// on data init, validate each section to see if they're complete or not based on data
				_.each(APP_ROUTES, route => { newState[route.id+'_form_done'] = !!determineCompletion(route.id, newState) });
			}

			if (collegesWithCustomQuestions) {
				newState = { ...newState, ...initSchoolsCustomQuestions(state, collegesWithCustomQuestions)};
			}

			return newState;

		case '_PROFILE:UPDATE_DATA':
		case '_PROFILE:PHONE_VERIFIED':
		case '_PROFILE:VERIFY_PHONE_PENDING':
			var newState = {...state, ...action.payload},
				page = newState.page;
			if( newState.req_app_routes ){
				var allPagesValidated = checkIfAllPagesAreCompleteBeforeSubmitting(newState);

				if (page == 'additional_info' && newState.active_college_for_additional_questions) {
					let college = newState.active_college_for_additional_questions,
						name = college.school_name.split(/\s+/).join('_').toLowerCase(),
						additional_questions = newState[name + '_school_additional_questions'];
					if (college && additional_questions) {
						college.page_done = determineValidAdditionalInfoPage(newState, college).page_done;
						newState = determineAndIncrementLastAdditionalInfoCollege(newState, college);
					}
				}

				newState = {
					...newState,
					...allPagesValidated,
				};
			}else{
				// on update, check if this page is complete or not
				newState[page+'_form_done'] = !!determineCompletion(page, newState);

			}

			return newState;

		case '_PROFILE:UPDATE_TRANSCRIPTS':
			var newState = { ...state },
				pay = action.payload,
				page = newState.page,
				newTranscripts = null;
			if (newState.transcripts && newState.transcripts.length > 0) {
				// Filter out initial front end transcripts that weren't stored in database yet.
				newTranscripts = newState.transcripts.filter(transcript => transcript.transcript_id);
				newTranscripts = newTranscripts.concat(pay.transcripts);
				newState.transcripts = newTranscripts;
			} else {
				newState['transcripts'] = pay.transcripts;
			}

			newState[page+'_form_done'] = !!determineCompletion(page, newState);

			return { ...newState, upload_pending: pay.upload_pending };

		case '_PROFILE:PAGE_CHANGED':
			var newState = {...state, ...action.payload},
				skippedPage = null,
				page = action.payload.currentPage;

			if( newState.req_app_routes ){
				skippedPage = determineSkippedPage(page, newState);
				newState['skipped_page'] = skippedPage;
			}

			return newState;

		case '_INTL:INIT':
			var pay = action.payload,
				_list = pay.list && pay.list.slice();

			if( state.applyTo_schools && _list ){
				// setting lacking applyTo_schools school obj with full school data from priority schools response
				state.applyTo_schools = state.applyTo_schools.map((school) => {
					let found = _.find(_list, {college_id: school.college_id});
					return found ? {...school, ...found} : school;
				});
			}

			return {
				...state,
				list: _list ? _list : [],
				unqualified_modal: pay.unqualified_modal || null,
				init_priority_schools_done: pay.init_done,
				priority_schools_pending: pay.init_pending,
			};

		case '_INTL:PENDING':
			var pay = action.payload;

			return {
				...state,
				priority_schools_pending: pay.init_pending,
			};

		case '_PROFILE:SET_REQ_APP_ROUTES':
			var req_app_routes = determineNextRoutes(action.payload),
				req_upload_docs = determineRequiredUploadDocs(state.applyTo_schools || []),
				req_additional_questions = determineAdditionalQuestions(state.applyTo_schools || []);


			var newState = {
				...state,
				req_app_routes,
				req_upload_docs,
				req_additional_questions,
			};

			var allPagesValidated = checkIfAllPagesAreCompleteBeforeSubmitting(newState),
			oneApp_step = determineOneAppStep(allPagesValidated);

			return {
				...newState,
				...allPagesValidated,
				oneApp_step
			};

		case '_PROFILE:TOGGLE_ADDITIONAL_INFO_COLLEGE':
			var newState = { ...state },
				activeCollege = action.payload.active_college_for_additional_questions,
				name = activeCollege.school_name.split(/\s+/).join('_').toLowerCase();

			newState = determineAndIncrementLastAdditionalInfoCollege(newState, activeCollege);

			if (newState[name + '_school_additional_questions']) {
				newState[name + '_school_additional_questions'].page_done =
					determineValidAdditionalInfoPage(newState, activeCollege).page_done;
			}

			return { ...newState, ...action.payload };

		case '_PROFILE:SCHOOL_ADDITIONAL_QUESTIONS':
			var newState = { ...state },
				college = action.payload.college,
				page = newState.page,
				name = college.school_name.split(/\s+/).join('_').toLowerCase(),
				additional_questions = determineAdditionalQuestions([college]),
				paginated_questions = paginateAdditionalQuestions(additional_questions),
				tmpObj = null;

			if (!state[name + '_school_additional_questions']) {
				tmpObj = {
					school_name: college.school_name,
					current_page: 0,
					questions: paginated_questions,
					page_done : false
				}
			} else {
				tmpObj = {
					school_name: college.school_name,
					current_page: state[name + '_school_additional_questions'].current_page,
					questions: paginated_questions,
					page_done : determineValidAdditionalInfoPage(newState, college).page_done
				}
			}

			newState[name + '_school_additional_questions'] = tmpObj;

			newState[name + '_school_additional_questions'].page_done =
				determineValidAdditionalInfoPage(newState, college).page_done;

			newState[page + '_form_done'] = !!determineCompletion(page, newState);

			return { ...newState, additional_questions_pending: false };

		case '_PROFILE:CHANGE_COLLEGE_ADDITIONAL_PAGE':
			var newState = { ...state },
				college = action.payload.college,
				movement = action.payload.movement,
				result = action.payload.result,
				name = college.school_name.split(/\s+/).join('_').toLowerCase();

			if (movement == 'back') {
				newState[name + '_school_additional_questions'].current_page--;
			} else if (movement == 'next') {
			 	newState[name + '_school_additional_questions'].current_page++;
			} else if (movement == 'next-college') {
				let currentCollege = { ...newState.active_college_for_additional_questions };
				newState = determineAndIncrementLastAdditionalInfoCollege(newState, currentCollege, 1);
			}

			if (movement != 'next-college') {
				newState[name + '_school_additional_questions'].page_done =
					determineValidAdditionalInfoPage(newState, newState.active_college_for_additional_questions).page_done;
			}

			return { ...newState, ...result, page_update_pending: false };

		case '_USER:INIT_IMPOSTER':
			return {...state, impersonateAs_id: action.payload.impersonateAs_id};
		default:
			return state;
	}

}

// Checks the current college and determines if its the last college
const determineAndIncrementLastAdditionalInfoCollege = (newState, currentCollege, increment = 0) => {
	let collegesWithCustomQuestions =
		newState.applyTo_schools
			.filter(college => college.custom_questions) // Remove colleges that do not have custom_questions
			.sort((a, b) => Object.keys(a.custom_questions).length - Object.keys(b.custom_questions).length), // Sort by ASC order

		nextCollegeIndex = 0,
		name = null;

	_.each(collegesWithCustomQuestions, (college, index) => {
		if (college.school_name == currentCollege.school_name) {
			name = college.school_name.split(/\s+/).join('_').toLowerCase();
			while (increment == 0 || newState[name + '_additional_questions_valid']) {
				nextCollegeIndex = index + increment;
				name = collegesWithCustomQuestions[nextCollegeIndex].school_name.split(/\s+/).join('_').toLowerCase();
				if ( ( nextCollegeIndex + 1 ) == collegesWithCustomQuestions.length ) {
					newState.last_additional_info_college = 1;
					break;
				} else {
					newState.last_additional_info_college = 0;
				}

				// Only increment if increment is set. Break out otherwise.
				if (increment == 0 ) { break; }

				increment++;
			}
			return false;
		}
	});

	newState.active_college_for_additional_questions = collegesWithCustomQuestions[nextCollegeIndex];

	return newState;
}

// Paginates questions by splting them into array elements.
const paginateAdditionalQuestions = (questions) => {
	const DIVIDE_LENGTH = 7;
	let page_count = Math.ceil(questions.length / DIVIDE_LENGTH),
		paginated_questions = [];

	if (page_count === 1) { return [questions]; }

	for (let i = 0; i < page_count; i++) {
		paginated_questions.push(questions.splice(0, DIVIDE_LENGTH));
	}

	return paginated_questions;
}

const initSchoolsCustomQuestions = (state, collegesWithCustomQuestions) => {
	let all_additional_questions = {};

	_.each(collegesWithCustomQuestions, college => {
		let name = college.school_name.split(/\s+/).join('_').toLowerCase(),
			additional_questions = determineAdditionalQuestions([college]),
			paginated_questions = paginateAdditionalQuestions(additional_questions),
			tmpObj = null;

		if (!state[name + '_school_additional_questions']) {
			tmpObj = {
				school_name: college.school_name,
				current_page: 0,
				questions: paginated_questions,
				page_done : false
			}
		} else {
			tmpObj = {
				school_name: college.school_name,
				current_page: state[name + '_school_additional_questions'].current_page,
				questions: paginated_questions,
				page_done : determineValidAdditionalInfoPage(state, college).page_done
			}
		}

		all_additional_questions[name + '_school_additional_questions'] = tmpObj;
	});

	return all_additional_questions;
}

const checkForFileDuplicates = (state, newFiles) => {
	const duplicates = [];
	_.each(state.uploaded_file_names, file_name => {
		if (_.find(newFiles, ['name', file_name])) {
			duplicates.push(file_name);
		}
	});
	return duplicates;
}

const determineAdditionalQuestions = (applyTo_schools) => {
	var a_schools = [...applyTo_schools],
		req_questions = [],
		FIELDS = [...ALL_ADDTL];

	_.each(a_schools, (sc) => {
		// if school does not have custom_questions, move to next school
		if( !_.get(sc, 'custom_questions') || _.isEmpty(sc.custom_questions) ) return true;

		// 1. loop through custom_questions
		// 2. find doc in ALL_ADDTL
		// 3. add to list - don't add if already in list
		_.each(sc.custom_questions, (required, question) => {
			let _quest = _.find(FIELDS, {name: question});

			// if can't find question, skip.
			if (!_quest) { return; }

			let already_added = _.find(req_questions.slice(), {name: _quest.name}),
				label = required ? _quest.label + ' *' : _quest.label + ' (optional)'; // updating the label with * or optional

			// if _quest is found and _doc wasn't already added to req_questions, then add it
			if( _quest && !already_added ) req_questions.push({..._quest, required, label});
		});
	});

	return req_questions;
}

const determineRequiredUploadDocs = (applyTo_schools) => {
	var a_schools = [...applyTo_schools],
		req_docs = [],
		FIELDS = [...CA.UPLOAD_Q];

	_.each(a_schools, (sc) => {
		// if school does not have allowed_uploads, move to next school
		if( !_.get(sc, 'allowed_uploads.length') ) return true;

		// 1. loop through allowed_uploads
		// 2. find doc in UPLOAD_Q
		// 3. add to list - don't add if already in list
		_.each(sc.allowed_uploads, (doc) => {
			let _doc = _.find(FIELDS, {name: doc});
			let already_added = _.find(req_docs.slice(), _doc);

			// if _doc is found and _doc wasn't already added to req_docs, then add it
			if( _doc && !already_added ) req_docs.push(_doc);
		});
	});

	// if empty, just set req_docs with all possible upload types b/c all is better than none
	// plus, there are ways to get to upload page even if it's not required
	if( !req_docs.length ) req_docs = [...CA.UPLOAD_Q];

	return req_docs;
}

const determineNextRoutes = (req_routes) => {
	// start with select_colleges
	var _routes = [...req_routes],
		start = _.findIndex(_routes, r => r.id === 'colleges'), // start with select colleges page
		end = _.findIndex(_routes, r => r.id === 'submit') - 1; // end at the page right before submit

	if( end > start ){

		for(var i = start; i <= end; i++){
			// update current route's next prop with the id of the following route in _routes list
			_routes[i] = {..._routes[i], next: _routes[i+1].id};
		}

	}else{
		// if here, means there are no required pages that the selected schools are requiring, so just make colleges next route the submit page
		_routes[start] = {..._routes[start], next: 'submit'};
	}

	return _routes;
}

const determineOneAppStep = (routes) => {
	// start with select_colleges
	let REQUIRED_ROUTES = _getRequiredRoutes();
	let _routes = [];

	// filter out the routes that are not meant to be a nav item of the SIC
	//let navItems = _.filter(REQUIRED_ROUTES, (r) => !r.notNavItem);
	_.each(APP_ROUTES, (r) => {
		let section = r.id;

		if( routes[section+'_form_done'] == false )
			_routes = [..._routes, r];
	});

	// if there are any routes from notDone, grab the first one
	if( _routes.length > 0 ){
		return _routes[0].id;
	}

	return 'review';

}

const getLatestQuestionId = (name, type) => {

	switch(type){
		case 'date': return '_dateRange_'+name;
		default: return name;
	}

}

const checkIfAllPagesAreCompleteBeforeSubmitting = (state) => {
	var map = {},
		routes_with_submit_at_end = [],
		filtered_routes = [],
		submit_route = null,
		routes = state.req_app_routes || [];

	// find all completed colleges
	map['completed_colleges'] = checkAllCompletedColleges(state);

	// find submit route
	submit_route = _.find(routes.slice(), {id: 'submit'});

	// filter out submit and review routes
	filtered_routes = _.filter(routes.slice(), r => r.id !== 'submit' && r.id !== 'review');

	// copy filtered routes to new array and append submit_route to end of new array to make sure submit page is determined last
	if(typeof submit_route === 'undefined'){
		routes_with_submit_at_end = [...filtered_routes];
	}else{
		routes_with_submit_at_end = [...filtered_routes, submit_route];
	}
	// loop through new array to determineCompletion for each route
	// routes_with_submit_at_end.forEach(r => map[r.id+'_form_done'] = !!determineCompletion(r.id, state, map));

for(let i = 0; i < routes_with_submit_at_end.length; i++){
	map[routes_with_submit_at_end[i].id+'_form_done'] = !!determineCompletion(routes_with_submit_at_end[i].id, state, map);
}

	return map;
}

const checkAllCompletedColleges = (state) => {
	const completed_colleges = [],
		applyTo_schools = state.applyTo_schools || [],
		transcripts = state.transcripts || [];

	_.each(applyTo_schools, college => {

		let valid = true;

        //// We are no longer requiring additional_info or uploads to be completed!

		// name = college.school_name.split(/\s+/).join('_').toLowerCase();

		// // Check additional questions
		// if ( ( college.custom_questions && !state[name + '_additional_questions_valid'] ) ||
		// 	 ( state[name + '_additional_questions_valid'] != null && !state[name + '_additional_questions_valid'] ) )
		// 	valid = false;

		// // Check uploads
		// if (college.allowed_uploads) {
		// 	_.each(college.allowed_uploads, upload => {
		// 		let found = _.find(transcripts, t => t.transcript_type === upload || t.upload_type === upload || t.doc_type === upload);
		// 		if (!found) { valid = false; }
		// 	});
		// }

		if (valid) { completed_colleges.push(college); }
	});

	return completed_colleges;

}

// Returns path to a skipped page, else returns false
const determineSkippedPage = (page, state) => {
	const map = checkIfAllPagesAreCompleteBeforeSubmitting(state),
		  routes = state.req_app_routes || [],
		  filtered_routes = _.filter(routes.slice(), r => r.id !== 'review'),
		  currentRoute = _.find(filtered_routes, ['id', page]) || { id: -1 }; // Fake route if currentRoute was not found as required.

	let route = null;

	// If reviewing application, do not redirect.
	if (page == 'review') { return '/college-application/review'; }

	if (currentRoute && filtered_routes && filtered_routes.length > 0) {

		// Loop to find the last route that is incomplete, with all completed forms prior to that.
		for (let i = 0; i < filtered_routes.length; i++) {
			route = filtered_routes[i];
			if (!map[route.id+'_form_done'] && ( currentRoute.id !== route.id )) {
				// Considered skipped if currentRoute is after an incomplete route
				// OR if current route isn't a required route.
				if ( filtered_routes.indexOf(route) <  filtered_routes.indexOf(currentRoute) || filtered_routes.indexOf(currentRoute) == -1 )
					return route.path;
			}
		}
	}

	return false;
}

const validateField = (field, _s) => {
	var val = _s[field.name],
		name = field.name,
		valid_name = name+'_valid';

	switch(name){
		case 'married':
		case 'children':
		case 'siblings':
		case 'in_college':
		case 'is_transfer':
		case 'parents_married':
			_s[valid_name] = !!_valueMap[val];
			break;

		case 'state_id':
		case 'degree_id':
		case 'country_id':
		case 'country_of_birth':
		case 'alternate_state_id':
		case 'alternate_country_id':
			_s[valid_name] = parseInt(val) > 0;
			break;

		case 'majors_arr':
		case 'languages':
			_s[valid_name] = _.get(_s, name+'.length', 0) > 0;
			break;

		case 'num_of_yrs_in_us':
		case 'num_of_yrs_outside_us':
			_s[valid_name] = parseInt(val) >= 0;
			break;

		case 'gpa':
		case 'weighted_gpa':
			val = parseFloat(val);
			_s[valid_name] = (val >= field.min && val <= field.max);
			break;

        case 'interested_school_type':
            _s[valid_name] = !_.isNil(val);
            break;
        case 'email':
        	var re_email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			_s[valid_name] = re_email.test(String(val).toLowerCase());
        	break;
		default:
			_s[valid_name] = !!val;
			break;
	}
}

const determineCompletion = (section, state, pageCompletionsMap) => {
	var _s = state;
	switch( section ){

		case 'basic':
			// custom validation for basic field
			CA.ALL_BASICINFO_FIELDS.forEach(f => validateField(f, _s) );

			return _s.gender && _s.grad_year && _valueMap[_s.in_college] && +_s.degree_id &&
					_.get(_s, 'majors_arr.length', 0) && _s.schoolName && _valueMap[_s.is_transfer];

		case 'identity':
			if ( !!!determineCompletion('basic', _s) ) { return false; }
            const birth_date = _s.birth_date;
            const birth_valid = birth_date && moment(birth_date, 'YYYY-MM-DD').format('YYYY-MM-DD') === birth_date;

			CA.NAME.forEach(f => validateField(f, _s)); // validates this page's fields
			return _s.fname && _s.lname && _s.birth_date && birth_valid;

		case 'start':
			if ( !determineCompletion('basic', _s) ) { return false; }

			CA.ALL_START_FIELDS.forEach(f => validateField(f, _s));
			return _s.planned_start_term && _s.planned_start_yr && !_.isNil(_s.interested_school_type);

		case 'contact':
			if ( !determineCompletion('start', _s) ) { return false; }
			var address_valid = false,
				phone_valid = false;
			CA.ALL_CONTACT_FIELDS.forEach(f => validateField(f, _s));

			address_valid = _s.line1 && (_s.state_id || _s.state) && _s.country_id && _s.city && _s.zip;
			phone_valid = _s.phone && _s.phone_code && !_s.phone_error;

			// if alternate address radio val is send, validate alternate fields
			// if the default address is not yet valid, don't bother checking alternate
			if( _s.alternate_address === 'send' && address_valid ){
				address_valid = _s.alternate_line1 && (_s.alternate_state_id || _s.alternate_state) &&
									_s.alternate_country_id && _s.alternate_city;
			}

			// if alternate phone radio val is !== none, validate alternate phone
			// if the default phone is not yet valid, don't bother checking alternate
			if( _s.preferred_alternate_phone !== 'none' && phone_valid ){
				phone_valid = _s.alternate_phone && _s.alternate_phone_code && !_s.alternate_phone_error;
			}

			return !!_s.email_valid && !!phone_valid && !!address_valid;

		case 'verify':
			return true;

		case 'citizenship':
			if ( !determineCompletion('contact', _s) ) { return false; }

			CA.ALL_CITIZEN_FIELDS.forEach(f => validateField(f, _s));
			return _s.country_of_birth && _s.city_of_birth && _s.citizenship_status && _.get(_s, 'languages.length', 0) > 0 &&
					parseInt(_s.num_of_yrs_in_us) >= 0 && parseInt(_s.num_of_yrs_outside_us) >= 0;

		case 'financials':
			if ( !!!determineCompletion('citizenship', _s) ) { return false; }

			validateField(CA.FINANCIALS, _s);
			return _s.financial_firstyr_affordibility;

		case 'gpa':
			if ( !!!determineCompletion('financials', _s) ) { return false; }

			CA.GPA_FIELDS.forEach(f => validateField(f, _s));
			return _s.gpa_valid;

		case 'scores':
			// GPA must be valid inorder for scores to be valid.
			// Helps make scores not instantly validated at beginning of application.
			if ( !!!determineCompletion('gpa', _s) ) { return false; }

			// Toggled exams off, no saving will be done. Therefore, scores are 'complete'.
			if ( _s.self_report == 'no' ) { return true; }

			const skipCheck = [];
			let all_scores_validated = true;
			_s['invalid_exam_validation_modal'] = false;

			// Checks which form to skip validation on exams that have pre and post forms.
			skipCheck.push( (_s['is_pre_2016_psat']) ? CA.POST_16_PSAT : CA.PRE_16_PSAT );

			skipCheck.push( (_s['is_pre_2016_sat']) ? CA.POST_16_SAT : CA.PRE_16_SAT );
			/**/

			_.each(EXAMS, (exam) => {
				if (skipCheck.indexOf(exam) >= 0) { return; }

				// Find button information to get button name. Find from either US or international constants.
				const BTN_INFO = _.find(CA.US_BTNS, { 'fields' : exam }) || _.find(CA.INTL_BTNS, { 'fields' : exam }) ||
								 _.find(CA.US_BTNS, { 'alternateFields' : exam }) || _.find(CA.INTL_BTNS, { 'alternateFields' : exam });

				// let fields_validated = true;
				let valid_count = 0;

				let empty_count = 0;

				_s[BTN_INFO.name + '_validated'] = true;

				_.each(exam, (field) => {
					let value = _s[field.name];

					let hasLimit = !!(field.min || field.max);

					if (value !== '' && value != null && !Number.isNaN(value)) {
						if (hasLimit && value >= field.min && value <= field.max) {
							_s[field.name + '_valid'] = true;
							valid_count++;
						} else if (!hasLimit) {
							_s[field.name + '_valid'] = true;
							valid_count++;
						} else {
							_s[field.name + '_valid'] = false;
						}
					} else if (!value || value === '') {
						_s[field.name + '_valid'] = false;
						empty_count++;
					}
				});

				if (exam.length == empty_count) {
					_s[BTN_INFO.name + '_validated'] = null;
				} else if (exam.length != valid_count) {
					_s[BTN_INFO.name + '_validated'] = false;
					_s['invalid_exam_validation_modal'] = true;
					all_scores_validated = false;
				}



			});

			return all_scores_validated;

		case 'colleges':
			if ( !!!determineCompletion('scores', _s) ) { return false; }
			return _.get(_s, 'MyCollegeList.length');

		case 'applications':
			if ( !!!determineCompletion('scores', _s) ) { return false; }

			return _.get(_s, 'MyApplicationList.length');

		case 'family':
			// if ( !!!determineCompletion('colleges', _s) ) { return false; }

			CA.FAM_Q.forEach(f => validateField(f, _s));
			return !!_valueMap[_s.married] && !!_valueMap[_s.children] && !!_valueMap[_s.parents_married] && !!_valueMap[_s.siblings];

		case 'awards':
			// if ( !!!determineCompletion('family', _s) ) { return false; }

			return _.get(_s, 'my_awards.length') || ( _s.award_name && _s.award_accord && _s.award_received_month && _s.award_received_year );

		case 'clubs':
			// if ( !!!determineCompletion('awards', _s) ) { return false; }

			return _.get(_s, 'my_clubs.length') || ( _s.club_name && _s.club_role && _s.club_active_start_month &&
								_s.club_active_start_year && _s.club_active_end_month && _s.club_active_end_year );

		case 'courses':
			// if ( !!!determineCompletion('uploads', _s) ) { return false; }

			return _.get(_s, 'current_schools.length') && _.get(_s, 'current_schools[0].courses.length') && _.get(_s, 'current_schools[0].courses[0].course_id') &&
								_.get(_s, 'current_schools[0].courses[0].credits') && _.get(_s, 'current_schools[0].courses[0].designation') &&
								_.get(_s, 'current_schools[0].courses[0].edu_level') && _.get(_s, 'current_schools[0].courses[0].subject');

		case 'additional_info':
			if ( !!!determineCompletion('colleges', _s) ) { return false; }

			/* emergencyDisable */
			// ******************* uncomment the below return true statement in case something goes wrong while I'm out. *******************
			// return true;
			// ******************* uncomment the above return true statement in case something goes wrong while I'm out. *******************
			var schools_with_custom_questions = _.pickBy(_s, (value, key) => key.endsWith('_school_additional_questions')),
				filtered_school_validations = null,
				all_school_questions = null,
				name = null,
				additional_info_valid = false;

			if( !_.get(_s, 'applyTo_schools') || _.isEmpty(_s.req_additional_questions) ) return valid;
			// Considered valid if at least one school is completely filled out.
			_.each(schools_with_custom_questions, college => {
				all_school_questions = _.reduce(college.questions, (sum, array) => sum.concat(array));
				name = college.school_name.split(/\s+/).join('_').toLowerCase();
				_s[name + '_additional_questions_valid'] = validateAdditionalInfoFields(_s, all_school_questions);
			});

			filtered_school_validations = _.pickBy(_s, (value, key) => key.endsWith('_additional_questions_valid'));

			// If any school is validated, set additional_info_valid to true else do nothing
			_.each(filtered_school_validations, validated => {
				validated ? additional_info_valid = true : null
			});

			if( additional_info_valid ) _s.save_attempted = false;

			return additional_info_valid;

		case 'uploads':
			// if ( !determineCompletion('colleges', _s) ) { return false; }

			var uploads_len = _.get(_s, 'transcripts.length', 0),
				valid = false,
				found = false,
				docs_still_needed = _s.req_upload_docs || [...CA.UPLOAD_Q],
				req_docs = [...docs_still_needed];


			// only check for specific type if user has at least uploaded one, else we know that they still need to upload everything
			if( uploads_len ){
				_.each(req_docs, d => {
					// find each transcript that contains the required uploads docs
					found = _.find(_s.transcripts, t => t.transcript_type === d.name || t.upload_type === d.name || t.doc_type === d.name);

					// update docs_still_needed by removing the found doc
					if( found ) docs_still_needed = _.filter(docs_still_needed, n => n.name !== d.name);

					valid = !docs_still_needed.length; // if docs_still_needed === 0, it's valid, else invalid
				});
			}

			_s.docs_still_needed = docs_still_needed;

			return valid;

		case 'demographics':
				CA.DEMOGRAPHIC_FIELDS.forEach(f => validateField(f, _s));
				return (_s.gender_valid &&  _s.ethnicity_valid && _s.religion_valid && _s.family_income_valid);

		case 'essay':
			// if ( !determineCompletion('colleges', _s) ) { return false; }
			var count = _s.essay_content_word_count;

			if( !count && _s.essay_content ) count = _s.essay_content.split(' ').length;

			return _s.essay_content && (count >= CA.ESSAY_MIN && count <= CA.ESSAY_MAX);


		case 'declaration':
			if ( !!!determineCompletion('colleges', _s) ) { return false; }

			let decl_schools = _.filter(_s.applyTo_schools, school => _.get(school, 'declarations.length')),
				all_declarations = [],
				valid = false;

			// for each school, add declarations to all_declarations list
			_.each(decl_schools, sc => { all_declarations = [...all_declarations, ...sc.declarations] });

			// check each declaration to see if store has declaration_id and is checked(true)
			_.each(all_declarations, dec => {
				valid = !!_s['declaration_'+dec.id];
				if( !valid ) return false;
			});

			return valid;

		case 'sponsor':
			// if ( !!!determineCompletion('colleges', _s) ) { return false; }

			let sponsor_valid = true,
				field = Q.SPONSOR_SELECT[0],
				option = _s.sponsor_will_pay_option,
				optin = _s.sponsor_will_pay_optin,
				page = _s.page,
				number_of_entries = _s.sponsor_number_of_entries,
				dependents = null;

			// If not opt in, no option selected, or no number of entries: It is invalid.
			if ( !optin || !option || !number_of_entries ) { return false; }

			// Validate dependent fields depending on option selected.
			dependents = field['dependents_' + option];

			// For each entry, check that all dependents have values.
			for (let i = 0; i < number_of_entries; i++) {
				_.each(dependents, dependent => {
					if (!_s[dependent.name + '_' + i]) {
						sponsor_valid = false;
						return false;
					}
				});

				// Using twilio phone verification states to determine valid phone
				// If only check if in sponsor page
				if (page == 'sponsor' && (!_s['sponsor_will_pay_email_' + i + '_valid'] || _s['sponsor_will_pay_phone_' + i + '_error'] == null || _s.verify_phone_pending ||
				   (!_s.verify_phone_pending && _s['sponsor_will_pay_phone_' + i + '_error']))) {
					sponsor_valid = false;
				}

				if (!sponsor_valid) { break; }
			}

			return sponsor_valid;

		case 'scholarships':
		case 'scholarshipsThanks':
		case 'submit':
			var all_other_pages_valid = false,
				_routes = _s.req_app_routes || [],
				page = _s.page;

            const pageCompletionClone = {...pageCompletionsMap};

            const optional_route_ids = [
                'uploads',
                'essay',
                'additional_info',
                'demographics',
                'sponsor',
            ];

            const isScholarshipFlow = window.location.search.includes('isScholarship=true');

            // If not scholarship flow, then this section will always be valid.
            if (!isScholarshipFlow && section === 'scholarships') {
                if ( !!!determineCompletion('scores', _s) ) { return false; }
                return true;
            }

            // Delete optional routes as we do not need to check if they're complete.
            optional_route_ids.forEach(id => {
                delete pageCompletionClone[id + '_form_done'];
            });

			// search for a false value in the map and break out immediately
			_.forIn(pageCompletionClone, (v, k) => {
				all_other_pages_valid = v;

				if ((!v && page == 'submit' ) || (!v && page == 'scholarships') || (!v && page == 'scholarshipsThanks')) {
					let skipped_route = _.find(_routes, { id: k.replace('_form_done', '') });
					if (skipped_route) {
						_s['skipped_page'] = skipped_route.path;
					}
				}

				return all_other_pages_valid;
			});

			/*
				submit page is valid if:
				- terms_of_conditions is checked
				- we have priority schools
				- all other pages pass validation
			*/
			return +_s.terms_of_conditions && _s.signature && _s.init_priority_schools_done && all_other_pages_valid;

		default:
			 return true; // just return true for the pages that don't have require validation
	}
}

const validateAdditionalInfoFields = (_s, questions) => {
	let valid = false;

	// 1. loop through all questions,
	// 2. if required prop is true, and that field is empty, fail validation, else valid
	// 3. if required prop is false, it's optional, so doesn't have to be filled out
	_.each(questions, field => {
		if( !field.required ){
			valid = true;
			_s[field.name+'_valid'] = true; // manually set field valid to true so that the err msg doesn't show

		}else{

			let _name = field.name;

			// _s.latest_question = getLatestQuestionId(_name, field.field_type); // save latest additional question to be able to scroll to it

			switch( field.field_type ){

				case 'date':
				case 'text':
					valid = !!_s[field.name] && !!_s[field.name].toString().trim();
					_s[field.name+'_valid'] = valid;
					return valid; // if false, stop validation, if true, continue to next question

				case 'select':
					valid = !!_s[field.name];
					_s[field.name+'_valid'] = valid;
					return valid; // if false, stop validation, if true, continue to next question

				case 'phone':
					// phone has two validation props - [name]+'_valid' and [name]+'_error' (twilio validation)
					valid = !!_s[field.name] && !_s[field.name+'_error'];
					_s[field.name+'_valid'] = valid;
					return valid; // if false, stop validation, if true, continue to next question

				case 'redirect':
					if( _.get(_s, 'transcripts.length', 0) > 0 ){
						// 1. loop through all uploaded docs
						// 2. check if doc.transcript_type === field.doc_type
						// 3. if true, valid and break out of loops, else keep looking
						_.each(_s.transcripts, doc => {

							// transcript_type is set when doc was previously saved
							// upload_type is set when doc was uploaded during current session
							valid = !!field.doc_type[doc.transcript_type] || !!field.doc_type[doc.upload_type] || !!field.doc_type[doc.doc_type];
							_s[field.name+'_valid'] = valid;
							return !valid; // if valid, stop looking, else continue iterating
						});

					}else valid = false;
					_s[field.name+'_valid'] = valid;

					return valid;

				case 'checkbox':
					_.each(field.fields, f => {
						valid = !!_s[f.name];
						_s[field.name+'_valid'] = valid;

						return !valid; // only one checkbox needs to be checked
					});

					return valid;

				case 'radio':
					let radio_name = _name.split('__').pop();

					if( _s[radio_name] ){
						valid = true; // if this questions input is set, it's valid
						_s[_name+'_valid'] = valid;

					}else{
						valid = false;
						_s[_name+'_valid'] = valid;
						return valid;
					}

					// has dependents if field has a value based dependent or just a general dependent, i.e. dependents vs dependents_yes
					// -- right now, only radio fields have dependents --
					let has_dependents = field['dependents_'+_s[radio_name]];

					// if question has just 'dependents' prop, default trigger value is yes
					if( !has_dependents ) has_dependents = _s[radio_name] === 'yes' && field.dependents;

					// validate dependents if any
					if( has_dependents ){

						// loop through this dependent array, and check if dependent fields have a value
						_.each(has_dependents, dependent_field => {
							// invoke trim function on field - invalid if value is just spaces

							if( _.invoke(_s, dependent_field.name+'.trim') ||_.get(_s, dependent_field.name) ){
								valid = true;
								_s[dependent_field.name+'_valid'] = valid;

							}else{
								valid = false;
								_s[dependent_field.name+'_valid'] = valid;
								return false;
							}

							if( !valid ) return false; // if at the end of the first loop, valid is false, break out

						});

					}

					return valid; // if false, stop validation, if true, continue to next question

				default: return false;
			}

		}

	});

	return valid;
}


// Checks validation for single page of additional questions
const determineValidAdditionalInfoPage = (state, college) => {
	let name = college.school_name.split(/\s+/).join('_').toLowerCase(),
		additional_questions = state[name + '_school_additional_questions'],
		questions = additional_questions.questions,
		page_index = additional_questions.current_page;

	additional_questions.page_done = validateAdditionalInfoFields(state, questions[page_index]);

	return additional_questions;
}
