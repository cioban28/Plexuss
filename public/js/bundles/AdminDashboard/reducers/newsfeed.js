const initialState = {
  allPosts: {
    data: [],
  },
  plexussOnlyPosts: {
    data: [],
  },
  audienceTargeting: {
    location: {
      countriesFilter: {
        all: true,
        include: false,
        exclude: false,
      },
      statesFilter: {
        all: true,
        include: false,
        exclude: false,
      },
      citiesFilter: {
        all: true,
        include: false,
        exclude: false,
      },
      selectedCountry: '',
      selectedRegion: '',
      isUSSelected: false,
      selectedCountries: [],
      selectedUSStates: [],
      selectedUSCities: [],
      stateCities: [],
    },
    startDate: {
      selectedTerms: [],
    },
    financials: {
      financials: ['0', '0 - 5,000', '5,000 - 10,0000', '10,000 - 20,0000', '20,000 - 30,0000', '50,000'],
      selectedFinancialRange: '0 - 5,000',
      studentNotInterestedInAid: false,
      selectedFinancialIndex: 1,
    },
    typeOfSchool: {
      typeOfSchool: 'Campus Only',
    },
    scores: {
      GPA: {min: '', max: ''},
      SAT: {min: '', max: ''},
      ACT: {min: '', max: ''},
      TOEFL: {min: '', max: ''},
      IELTS: {min: '', max: ''},
    },
    uploads: {
      uploads: {
        transcript: true,
        financialInfo: true,
        ietls: true,
        toefl: true,
        resume: true,
        passport: true,
        essay: true,
        others: true,
      }
    },
    demographics: {
      age: { min: '', max: '' },
      shouldShowGenderSelectedFilter: false,
      genderFilter: { all: true, males_only: false, females_only: false },
      ethnicityFilter: { all: true, include: false, exclude: false },
      religionFilter: { all: true, include: false, exclude: false },
      selectedEthnicites: [],
      selectedReligions: [],
    },
    educationLevel: {
      highSchool: true,
      college: true,
    },
    militaryAffiliations: {
      selectedMilitaryAffiliations: [],
			inMilitary: '',
    },
    profileCompletion: {
      profileCompletion: '',
    },
    majors: {
      departmentFilter: {
        all: true,
        include: false,
        exclude: false,
      },
      selectedDepartments: [],
      selectedDeptName: '',
      degreeFilters: {certificateProgram: true, associates: true, bachelors: true, masters: true, doctorate: true},
    },
    resetFilter: false,
    recommendationMeter: {
      users: 0,
      shouldUpdateMeter: false,
    },
  },
  loader: {
    isLoading: false,
  },
  salesPostId: '',
};

const newsfeed = (state = initialState, action) => {
  switch(action.type){
    case 'GET_ALL_POSTS_SUCCESS':
      let allPosts = {...state.allPosts};
      allPosts = { ...action.payload, data: [ ...allPosts.data, ...action.payload.data, ] };
      return { ...state, allPosts: allPosts };

    case 'GET_PLEXUSS_ONLY_POSTS_SUCCESS':
      let plexussOnlyPosts = {...state.plexussOnlyPosts};
      plexussOnlyPosts = { ...action.payload, data: [ ...plexussOnlyPosts.data, ...action.payload.data, ] };
      return { ...state, plexussOnlyPosts: plexussOnlyPosts };

    case 'SET_RECOMMENDATION_FILTER_LOCATION':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, location: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_START_DATE_TERM':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, startDate: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_FINANCIAL':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, financials: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_TYPEOFSCHOOL':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, typeOfSchool: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_SCORES':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, scores: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_UPLOADS':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, uploads: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_DEMOGRAPHIC':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, demographics: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_EDUCATION_LEVEL':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, educationLevel: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_MILITARY_AFFILIATION':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, militaryAffiliations: { ...action.payload } } };

    case 'SET_RECOMMENDATION_FILTER_PROFILE_COMPLETION':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, profileCompletion: {...action.payload} } };

    case 'SET_RECOMMENDATION_FILTER_MAJOR_DEPT_DEGREE':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, majors: { ...action.payload } } };

    case 'RESET_RECOMMENDATION_FILTER_LOCATION':
      let location = {
        countriesFilter: {
          all: true,
          include: false,
          exclude: false,
        },
        statesFilter: {
          all: true,
          include: false,
          exclude: false,
        },
        citiesFilter: {
          all: true,
          include: false,
          exclude: false,
        },
        selectedCountry: '',
        selectedRegion: '',
        isUSSelected: false,
        selectedCountries: [],
        selectedUSStates: [],
        selectedUSCities: [],
        stateCities: [],
      };
      return { ...state, audienceTargeting: { ...state.audienceTargeting, location: location, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_START_DATE_TERM':
      let startDate = {
        selectedTerms: [],
      };
      return { ...state, audienceTargeting: { ...state.audienceTargeting, startDate: startDate, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_FINANCIAL':
      let financials = {
        financials: ['0', '0 - 5,000', '5,000 - 10,0000', '10,000 - 20,0000', '20,000 - 30,0000', '50,000'],
        selectedFinancialRange: '0 - 5,000',
        studentNotInterestedInAid: false,
        selectedFinancialIndex: 1,
      };
      return { ...state, audienceTargeting: { ...state.audienceTargeting, financials: financials, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_TYPEOFSCHOOL':
      let typeOfSchool = {
        typeOfSchool: 'Campus Only',
      }
      return { ...state, audienceTargeting: { ...state.audienceTargeting, typeOfSchool: typeOfSchool, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_SCORES':
      let scores = {
        GPA: {min: '', max: ''},
        SAT: {min: '', max: ''},
        ACT: {min: '', max: ''},
        TOEFL: {min: '', max: ''},
        IELTS: {min: '', max: ''},
      };
      return { ...state, audienceTargeting: { ...state.audienceTargeting, scores: scores, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_UPLOADS':
      let uploads = { transcript: true, financialInfo: true, ietls: true, toefl: true, resume: true, passport: true, essay: true, others: true, }
      return { ...state, audienceTargeting: { ...state.audienceTargeting, uploads: uploads, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_DEMOGRAPHIC':
      let demographics = {
        age: { min: '', max: '' },
        shouldShowGenderSelectedFilter: false,
        genderFilter: { all: true, males_only: false, females_only: false },
        ethnicityFilter: { all: true, include: false, exclude: false },
        religionFilter: { all: true, include: false, exclude: false },
        selectedEthnicites: [],
        selectedReligions: [],
      }
      return { ...state, audienceTargeting: { ...state.audienceTargeting, demographics: demographics, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_EDUCATION_LEVEL':
      let educationLevel = { highSchool: true, college: true, };
      return { ...state, audienceTargeting: { ...state.audienceTargeting, educationLevel: educationLevel, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_MILITARY_AFFILIATION':
      let militaryAffiliations = { selectedMilitaryAffiliations: [], inMilitary: '', };
      return { ...state, audienceTargeting: { ...state.audienceTargeting, militaryAffiliations: militaryAffiliations, resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_PROFILE_COMPLETION':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, profileCompletion: { profileCompletion: '' } , resetFilter: true, } };

    case 'RESET_RECOMMENDATION_FILTER_MAJOR_DEPT_DEGREE':
      let majors = {
        departmentFilter: {
          all: true,
          include: false,
          exclude: false,
        },
        selectedDepartments: [],
        selectedDeptName: '',
        degreeFilters: {certificateProgram: true, associates: true, bachelors: true, masters: true, doctorate: true},
      };
      return { ...state, audienceTargeting: { ...state.audienceTargeting, majors: majors, resetFilter: true, } };

    case 'IS_FETCHING':
      return { ...state, loader: { isLoading: action.payload } };

    case 'SET_SALES_POST_ID':
      return { ...state, salesPostId: action.payload };

    case 'SET_TARGETING_RESET_FILTER':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, resetFilter: action.payload } };

    case 'SET_USERS_FOR_RECOMMENDATION_METER':
    return { ...state, audienceTargeting: { ...state.audienceTargeting, recommendationMeter: { ...state.audienceTargeting.recommendationMeter, users: action.payload } } };

    case 'SET_SHOULD_UPDATE_METER':
      return { ...state, audienceTargeting: { ...state.audienceTargeting, recommendationMeter: { ...state.audienceTargeting.recommendationMeter, shouldUpdateMeter: action.payload } } };

    default:
      return state;
  }
};

export default newsfeed
