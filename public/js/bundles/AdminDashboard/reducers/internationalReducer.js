// internationalReducer.js

import { YOUTUBE_EMBED_START, YOUTUBE_EMBED_END, YOUTUBE_URL, VIMEO_EMBED, VIMEO_URL } from './../components/cms/International/constants'

var init = {
	testimonialList: [],
	program: {},
	activeProgram: '',
	department_option: 'all',
};

export default (state = init, action) => {

	switch( action.type ){

		case 'PENDING':
		case 'SAVE_INTL':
		case 'RESET_SAVED':
		case 'EDIT_PROGRAM':
		case 'NEW_TESTIMONIAL':
		case 'EDIT_HEADER_INFO':
		case 'SET_PROGRAM_HEADER':
		case 'GET_ALL_DEPTS_DONE':
        case 'PORTAL_W_MAJOR_PENDING':
		case 'GET_MAJOR_FOR_THIS_DEPT':
		case 'INIT_PORTALS_WITH_MAJORS':
		case 'SET_MAJOR_SELECTION_OPTION':
			return {...state, ...action.payload};

		case 'SAVE_INTL_DONE':
			var newState = {...state, pending: action.payload.pending, saved: action.payload.saved}; //temp
			return newState;

		case 'EDIT_TESTIMONIAL':
			var newState = {...state};

			newState.newTestimonial = {...newState.newTestimonial, ...action.payload};
			return newState;

		case 'ADD_TESTIMONIAL':
			var newState = {...state, pending: action.payload.pending, saved: action.payload.saved},
				found = null, 
				pay = action.payload,
				testimonialToAdd = null;

			// adding id that is returned upon saving
			testimonialToAdd = {...newState.newTestimonial, id: pay.response.video_testimonial_id}; //copy newTestimonial
			newState.newTestimonial = {}; //set newTestimonial w/ empty obj

			found = _.find(newState.testimonialList, {id: testimonialToAdd.id});

			if( found ){
				newState.testimonialList = newState.testimonialList.map((tes) => {
					if( tes.id === testimonialToAdd.id ) return {...tes, ...testimonialToAdd};
					return tes;
				});
			}else{
				newState.testimonialList.unshift( testimonialToAdd );
			}

			return newState;

		case 'REMOVE_TESTIMONIAL':
			var newState = {...state, pending: action.payload.pending, saved: action.payload.saved},
				found = null, 
				testimonialToRemove = null;

			testimonialToRemove = action.payload.item;

			found = _.find(newState.testimonialList, testimonialToRemove);

			if( found ){
				newState.testimonialList = _.reject(newState.testimonialList, testimonialToRemove);
			}

			return newState;

		case 'EDIT_NOTES':
			var newState = {...state},
				pay = action.payload;

			newState[pay.name] = {...newState[pay.name], content: pay.val};

			return newState;

		case 'EDIT_ALUM':
			var newState = {...state},
				pay = action.payload.item || action.payload; // if item is set, coming from remove action

			// else pay should set with an action, which will determine what is performed
			switch(pay.alum_action){
				case 'set':
					// set new alum - open empty form
					newState.new_alumni = {...pay}; 
					break;

				case 'close':
					// unset new alum - closes form
					newState.new_alumni = null; 
					break;

				case 'edit':
					// update new_alumni with new props
					newState.new_alumni = {...newState.new_alumni, ...pay};
					break;

				case 'add':
					//only on save will pending prop be passed
					newState.pending = pay.pending;
					newState.saved = pay.saved;

					var already_exist = null;
					
					// if alumni list exists, add new alum to front of new list, else create new list w/ alum in it
					let new_alum = {...newState.new_alumni, id: pay.response.alumni_id};

					// check if this alum already exists in our list of alumni
					if( new_alum ) already_exist = _.find(newState.alumni_list.slice(), {id: new_alum.id});

					if( already_exist ){
						// already exists, so just update current
						newState.alumni_list = newState.alumni_list.map((al) => {
							if( al.id === already_exist.id ) return {...already_exist, ...new_alum};
							return al;
						});
					}else{
						// does not exist yet, so add to list
						newState.alumni_list = newState.alumni_list ? [new_alum, ...newState.alumni_list] : [new_alum];
					}

					newState.new_alumni = null;
					
					break;

				case 'remove':
					newState.alumni_list = _.reject(newState.alumni_list, {id: pay.id});
					break;

				default: //if no alum_action is passed, do same as case 'edit'
					// update new_alumni with new props
					newState.new_alumni = {...newState.new_alumni, ...pay};
					break;
			}

			return newState;

		case 'EDIT_ACTIVE_REQ':
			var newState = {...state},
				pay = action.payload.item || action.payload, // if item is set, coming from remove action
				req_prop = newState.activeProgram + '_' + pay.type + '_requirements';

			// if req prop has yet to be initialized, init it
			if( !newState[req_prop] ) newState[req_prop] = {};

			switch( pay.req_action ){
				case 'set':
					// if active_req is set, un set it, else set it
					newState[req_prop].active_req = !newState[req_prop].active_req ? pay : null;
					break;

				case 'edit':
					// if req prop has yet to be initialized, init it
					if(	!newState[req_prop].active_req ) newState[req_prop].active_req = {};

					// update active req
					newState[req_prop].active_req = {...newState[req_prop].active_req, ...pay};
					break;

				case 'save':
					var save_req = {...newState[req_prop].active_req},
						already_exist = null,
						saved_id = pay.response.requirement_id;

					//only on save will pending prop be passed
					newState.pending = pay.pending;
					newState.saved = pay.saved;

					//if we already have this requirement, overwrite existing one
					if( newState[req_prop].list ) already_exist = _.find(newState[req_prop].list.slice(), {id: saved_id});

					// if we don't already have this requirement, then save as new
					// else find and update the existing one
					if( !already_exist ){
						save_req.id = saved_id;
						newState[req_prop].list = newState[req_prop].list ? [save_req, ...newState[req_prop].list] : [save_req];
					}else{
						newState[req_prop].list = newState[req_prop].list.map((requirement) => {
							if( requirement.id === already_exist.id ) return {...already_exist, ...save_req};
							return requirement;
						});
					}

					newState[req_prop].active_req = null;
					
					break;

				case 'remove':
					//only on save will pending prop be passed
					newState.pending = action.payload.pending;
					newState.saved = action.payload.saved;

					// remove req from list
					newState[req_prop].list = _.reject(newState[req_prop].list, {id: pay.id});
					break;

				case 'remove_attachment':
					//only on save will pending prop be passed
					newState.pending = action.payload.pending;
					newState.saved = action.payload.saved;

					// remove attachment from specific requirement
					newState[req_prop].list = newState[req_prop].list.map((requirement) => {
						// if requirement was found, unset attachment
						if( requirement.id === pay.id ) return {...requirement, ...pay};
						return requirement;
					});
					break;

				default: break;
			}

			return newState;

		case 'ADD_REMOVE_INTL_DEP':
			var newState = {...state},
				{ id, name } = action.payload, //should be a department id
				dept_found = null,
				active_program_dept = 'departments';

			if( !newState[active_program_dept] ) newState[active_program_dept] = [];

			// check if we already have this id in this dept list
			dept_found = _.find(newState[active_program_dept].slice(), {id: +id});

			if( dept_found ) newState[active_program_dept] = _.reject(newState[active_program_dept].slice(), {id: +id}); //remove if we already have this dept
			else{ // else add it
				newState[active_program_dept] = [action.payload, ...newState[active_program_dept]];

				// create default value for major option (all or include)
				newState['option_for_dept_majors_'+id+'_'+newState.activeProgram] = 'all'; 
			}

			return newState;

		case 'ADD_REMOVE_INTL_MAJOR':
			var newState = {...state},
				{ id, name, dept_id } = action.payload,
				selected_major_array_name = 'selected_majors_for_dept_'+dept_id,
				major_found = null;

			if( !newState[selected_major_array_name] ) newState[selected_major_array_name] = [];

			// check if this major is already in this list
			major_found = _.find(newState[selected_major_array_name].slice(), {id: +id});

			 // if found in selected list, remove it
			if( major_found ) newState[selected_major_array_name] = _.reject(newState[selected_major_array_name].slice(), {id: id});
			else newState[selected_major_array_name] = [action.payload, ...newState[selected_major_array_name]]; // else add it

			return newState;

		case 'EDIT_MAJOR|DEPT_CAMPUS_TYPE':
			var deptORmajor = action.payload,
				newState = {...state},
				list_name = '';

			// 1. figure out if major or dept
			if( deptORmajor.dept_id ) list_name = 'selected_majors_for_dept_'+deptORmajor.dept_id+'_'+newState.activeProgram;
			else list_name = 'departments';

			// 2. map through list and update item
			newState[list_name] = newState[list_name].map((item) => {
				if( item.id === deptORmajor.id ) return {...item, ...deptORmajor};
				return item;
			});

			return newState;

		case 'RESET_INTL_MAJORS':
			var newState = {...state, ..._.pick(action.payload, ['pending', 'saved'])},
				program = 'undergrad', // hard-coded right now until we decide what to do with program header
				dept_list = program + '_departments',
				dept_option = program + '_department_option',
				selected_list_name = 'selected_majors_for_dept_',
				option_name = 'option_for_dept_majors_';

			if( newState[dept_list] && newState[dept_list].length > 0 ){
				var list = newState[dept_list];

				_.each(list, (dept) => {
					let option_prop = option_name + dept.id + '_' + program,
						selected_prop = selected_list_name + dept.id + '_' + program;

					newState[option_prop] = 'all'; // reset option back to all for this dept
					newState[selected_prop] = null; // nullify selected majors list for this dept
				});

				newState[dept_option] = 'all';
				newState[dept_list].length = []; // set dept list to new empty array;
			}

			return newState;

		case 'GET_ALL_INTL_DATA':
			// if payload data is not empty, extract data, else just return state
			if( action.payload.data ){
				var { header_info, video_testimonials, admission_info,
					  scholarship_info, grade_exams, requirements, 
					  alums, additional_notes, route, define_program } = action.payload.data;

				// these are the objects that have a 'grad' and 'undergrad' prop
				// will loop through them to extract all grad and undergrad data
				var nonListObjects = [header_info, admission_info, scholarship_info, grade_exams],
					intlData = {};

				// extracting data for header_info, admission_info, scholarship_info, grade_exams
				// some of the aforementioned nonListObjects elements may be undefined, so check if it's set before adding to intlData
				_.each(nonListObjects, (d) => { 
					if(d) intlData = {...intlData, ...d.grad, ...d.undergrad, ...d.epp}; 
				});

				// extracting data for video_testimonials
				var testimonials = _extractTestimonialData(video_testimonials);	

				// extract data for requirements
				var reqObj = _extractRequirementData(requirements);

				// extract data for additional notes
				var noteObj = _extractNotesData(additional_notes);

				// build new state
				return {
					route, // routes to grad and undergrad college pages
					...state, //prev state
					...reqObj, // all requirement related data
					...noteObj, // additional notes data
					...intlData, // rest of intl data
					alumni_list: alums, // list of saved alums
					program: define_program || {}, // sets the program type
					activeProgram: _.findKey(define_program, v => v),
					testimonialList: testimonials, // testimonial data
					pending: action.payload.pending,  
					init_done: action.payload.init_done,
				};
			}

			return {
				...state,
				pending: action.payload.pending,  
				init_done: action.payload.init_done,
			};

		case 'INIT_INTL_MAJORS_DATA':
			var pay = action.payload,
				newState = {...state, ..._.omit(pay, 'data')};

			if( pay.data ){

				if( pay.data.departments === 'all' ){
					newState.department_option = pay.data.departments;

				}else if( _.isArray(pay.data.departments) ){
					var { departments, department_option } = pay.data,
						non_dept_objs = _.omit(pay.data, ['departments', 'department_option']);

					newState = {
						...newState,
						departments,
						department_option,
					};

					// add each of the non_dept_objs to state
					_.forIn(non_dept_objs, (val, key) => newState[key] = val);
				}

			}

			return newState;

		default:
			return state;
	}

}

const _extractNotesData = (additional_notes) => {
	var notes = {};

	_.forIn(additional_notes, (value, key) => notes[key+'_notes'] = value[0]);

	return notes;
}

const _extractTestimonialData = (video_testimonials) => {
	var testimonials = [];

	_.each(video_testimonials, (vid) => {
		if( vid.source === 'youtube' ){ //is youtube
			var url = YOUTUBE_URL + vid.url, //url
				embed_url = YOUTUBE_EMBED_START + vid.url + YOUTUBE_EMBED_END; //embed url
		}else if( vid.source === 'vimeo' ){ // is vimeo
			var url = VIMEO_URL + vid.url, //url
				embed_url = VIMEO_EMBED + vid.url; //embed url
		}else{
			return false;
		}

		testimonials = [{
			url,
			embed_url,
			id: vid.id,
			title: vid.title,
		}, ...testimonials];
	});

	return testimonials;
}

const _extractRequirementData = (requirements) => {
	var reqObj = {};

	_.forIn(requirements, (program_obj, key) => {
		if( !key ) return true; //if for whatever reason key is empty, move to next key

		_.forIn(program_obj, (req, rkey) => {
			var req_name = key+'_'+rkey+'_requirements';
			
			// if reqObj[req_name] has not yet been set with an obj, then set it with an empty list prop
			if( !reqObj[req_name] )reqObj[req_name] = {list: []};

			reqObj[req_name].list = [...reqObj[req_name].list, ...req];
		});
	});

	return reqObj;
}