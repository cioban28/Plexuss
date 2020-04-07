import initialState from "./initialState";

const steps = (state = initialState.steps, action) => {
    // console.log(action)
    switch(action.type) {
        case "SET_USER_INFO":
            let newStep1 = Object.assign({}, state.step1)
            newStep1['user_info'] = {...action.payload}
            return {...state, step1: newStep1}
        case "SET_CURRENT_STEP":
            return {...state, currentStep: action.payload}
        case "GET_STEP_STATUS":
            const { payload } = action;
            var activeStep = null, step_num = payload.steps_completed.current_step, obj=null

            //obj of step props and bool values to show if steps are done or not

            for (var i = 1; i <= 8; i++) {
                obj = {
                    name: 'Step '+i,
                    is_active: parseInt(i) === parseInt(step_num),
                    num: parseInt(i),
                    currStep: parseInt(step_num),
                    done: payload.steps_completed['step_'+i+'_complete'],
                    total_num_of_steps: 5
                };
                if (obj.is_active) activeStep = obj;
            }
            return {...state, steps_completed: payload.steps_completed, is_loading: false, active_step: activeStep}
        case "GET_STEP1_DATA":
            const data = action.payload;
            var valid = false;

            //check user type
            for(var prop in data) {
                if(data.hasOwnProperty(prop) && prop.indexOf('is_') > -1) {
                if(+data[prop]) valid = true;
                }
            }
            if (data.college_grad_year || data.hs_grad_year) valid = true;
            else valid = false;

            valid = !!(data.curent_school_id || data.school_name);
            var step1 = {}
            step1['is_load'] = true; step1['is_valid'] = valid; step1['user_info'] = data;

            return {...state, step1: step1}
        case "GET_STEP2_DATA":
            const data2 = action.payload
            var step2 = Object.assign({}, state.step2)
            step2['bdayPending'] = false
            step2['user_info'] = data2;
            if (data2.country_id === 1)
                step2['gpa_load'] = false

            if (data2.weighted_gpa) {
                step2['weighted_gpa'] = data2.weighted_gpa;
            }

            if (data2.hs_gpa) {
                step2['unweighted_gpa'] = data2.hs_gpa;
            } else if (data2.overall_gpa) {
                step2['unweighted_gpa'] = data2.overall_gpa;
            }

            if (data2.birth_date) {
                const split = data2.birth_date.split('-');

                step2['year'] = split[0];
                step2['month'] = split[1]
                step2['day'] = split[2];

            }

            return {...state, step2: step2, gpa_applicant_country: data2.country_id, country_id: data2.country_id, gpa: step2['unweighted_gpa'], page: 'gpa'}
        case "GET_STEP3_DATA":
            const data3 = action.payload
            if( data3 && _.isArray(data3) && data3.length > 0 ) 
                return {...state, step3: data3, load_step3: false}
            else return {...state, load_step3: false}
        case "GET_STEP4_DATA":
            const data4 = action.payload
            return {...state, step4: data4}
        case "GET_STEP5_DATA":
            const data5 = action.payload
            return {...state, step5: data5}
        case "GET_STEP6_DATA":
            const data6 = action.payload
            return {...state, step6: data6}
        case "GET_STATES_DATA":
            const states = action.payload
            return {...state, states: states}
        case "GET_COUNTRIES_DATA":
            const countries = action.payload
            return {...state, unique_countries: countries, country_list: countries}
        case "GET_USER_NAME":
            const username = action.payload
            return {...state, username: username.name}
        case "GET_SCHOOL":
            const cardsObj = action.payload
            const cards = [],
                ro_caps = cardsObj.caps;

            for (const key in cardsObj) {
                if (key.indexOf('tab') !== -1)
                    cards.push(cardsObj[key]);
            }

            return {...state, all_cards: cards, ro_caps: ro_caps}
        case "SET_CAPS":
            return {...state, ro_caps:action.payload}
        case "GET_GPA_GRADING_SCALE":
            var newStep2 = Object.assign({}, state.step2)
            newStep2['country_grading_scales'] = action.payload
            return {...state, step2: newStep2}
        case "SET_GPA_APPLICANT_SCALE":
            var newScale = Object.assign({}, state.step2)
            newScale['gpa_applicant_scale'] = action.payload
            return {...state, step2: newScale}
        case "GET_COUNTRY":
            var newCountry = Object.assign({}, state.step2)
            newCountry['gpa_load'] = false
            return {...state, step2: newCountry, countries_list: action.payload}
        case '_PROFILE:ERR':
		case '_PROFILE:RESET_SAVED':
		case '_PROFILE:SAVE_PENDING':
		case '_PROFILE:APPLICATION_SAVED':
		case '_PROFILE:INIT_COUNTRIES':
		case '_PROFILE:INIT_GRADING_SCALES':
		
			return {...state, ...action.payload};
		
		case '_PROFILE:CHANGED_FIELDS':
			let newState = {...state};
			

			if(typeof newState.changedFields == 'undefined')
				newState.changedFields = [];
			
			if(newState.changedFields.indexOf(action.payload) === -1)
				newState.changedFields.push(action.payload);
	
			return 	newState;

		case '_PROFILE:VALIDATE_PAGE':
			var newState = {...state, ...action.payload},
				page = newState.page;
			
			if (page)
				newState[page + '_form_done'] = !!determineCompletion(page, newState);

			return newState;

		case '_PROFILE:UPDATE_DATA':
			var newState = {...state, ...action.payload},
                page = newState.page;

            // console.log(action.payload)
            // if (newState['gpa_applicant_value_valid'])
            // {
            //     newState['gpa'] = 
            // }
            // on update, check if this page is complete or not
            newState[page+'_form_done'] = !!determineCompletion(page, newState);

			return newState;
        default:
            return {...state}
    }
}

const GPA_FIELDS = [
	{name: 'gpa', label: '', display_label: 'United States Overall GPA', step: '0.01', placeholder: '', err: 'Only values between 0.1 and 4 are accepted.', min: 0.10, max: 4.00, type: 'text'},
];

const determineCompletion = (section, state) => {
	var _s = state;
	switch( section ){
		case 'gpa': 
			GPA_FIELDS.forEach(f => validateField(f, _s));
			return _s.gpa_valid;

		default:
			 return true; // just return true for the pages that don't have require validation
	}
}

const validateField = (field, _s) => {
	var val = _s[field.name],
		name = field.name,
		valid_name = name+'_valid';
	switch(name){
		case 'gpa':
		case 'weighted_gpa':
			val = parseFloat(val);
			_s[valid_name] = (val >= field.min && val <= field.max);
			break;

		default:
			_s[valid_name] = !!val;
			break;
	}
}

export default steps;