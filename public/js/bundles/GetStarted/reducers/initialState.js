const initialState = {
    steps: {
        active_step: null,
        is_loading: true,
        currentStep: -1,
        steps_completed: [],
        step1: {
            is_load: false,
            is_valid: false,
            user_info: [],
        },
        step2: {
            bdayPending: true,
            user_info: [],
            gpa_load: true,
            weighted_gpa: '',
            unweighted_gpa: '',
            year: '',
            month: '',
            day: '',
        },
        step3: [],
        load_step3: true,
        step4: null,
        step5: [],
        username: '',
        step6: [],
        states: [],
        unique_countries: [],
        country_list: [],
        all_cards: [[],[],[]],
        ro_caps: [],
        gpa:'',
        countries_list:[],
        country_grading_scales: [],
        gpa_applicant_country: null,
        country_id: null,
    }
};
  
export default initialState;
  