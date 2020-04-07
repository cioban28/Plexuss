// Profile.js - actions

import $ from 'jquery'
import axios from 'axios'
import moment from 'moment'
import selectn from 'selectn'
import find from 'lodash/find'
import findIndex from 'lodash/findIndex'
import findKey from 'lodash/findKey'
import {APP_ROUTES} from './../components/SIC/constants'
const _ = {
	find: find,
	findIndex: findIndex,
	findKey: findKey,
}
export const sortCols = (listObj) => {
	var list = listObj.list;
	
	return {
		type: '_PROFILE:SORT',
		payload: {list}
	}
}

//keep track of fields changed -- for analytics
export const changedFields = (fields) => {

	let humanReadible = fields.replace(/[_]/g, " ");

	return {
		type: '_PROFILE:CHANGED_FIELDS',
		payload: humanReadible
	}	
}

//keep track of fields changed -- for analytics
export const clearChangedFields = () => {
	return{
		type: '_PORFILE:CLEAR_CHANGED_FIELDS',
		payload: {changedFields: []}
	}
}

export const updateProfile = (data = {}) => {

	return {
		type: '_PROFILE:UPDATE_DATA',
		payload: data
	}	
}

export const updateFileUploads = (data) => {
	return {
		type: '_PROFILE:UPDATE_FILE_UPLOADS',
		payload: { data }
	}
}

export const updateReqAppRoutes = (routes) => {
	return {
		type: '_PROFILE:SET_REQ_APP_ROUTES',
		payload: routes  
	}	
}

export const savePublicProfileSettings = (data) => {
    return axios({
        method: 'post',
        url: '/social/save-public-profile-settings',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, //{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
        data: data,
    })
    .then(res => {
        if(res.statusText == "OK"){
            console.log(res);
            //let newData = { user_id: data.profile_id }
            //getProfileData(newData);
        }
    })
    .catch(error => {
        console.log(error);
    })
}

export const getLikedColleges = (user_id = '') => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { getLikedCollegesPending: true, }
        });

        axios({
            url: '/ajax/getLikedColleges/' + user_id,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { getLikedCollegesPending: false, likedColleges: response.data }
            });
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { getLikedCollegesPending: false, likedColleges: [] }
            });
        });
    }
}

export const saveLikedColleges = (data, callback) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { saveLikedCollegesPending: true, }
        });

        axios({
            url: '/ajax/profile/saveLikedCollegesSection',
            method: 'POST',
            data: { ...data },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { saveLikedCollegesPending: false, getLikedCollegesPending: false, likedColleges: response.data.liked_colleges }
            });

            callback();

        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { saveLikedCollegesPending: false }
            });

            callback();
        });
    }
}

export const getProfileEducation = (user_id = '') => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { getProfileEducationPending: true, }
        });

        axios({
            url: '/ajax/getEducation/' + user_id,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_PROFILE_EDUCATION',
                payload: { getProfileEducationPending: false, education: response.data }
            });
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { getProfileEducationPending: false, education: [] }
            });
        });
    }
}

export const saveProfileEducation = (data, callback) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { saveProfileEducationPending: true, }
        });

        axios({
            url: '/ajax/profile/saveEducation',
            method: 'POST',
            data: { ...data },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_PROFILE_EDUCATION',
                payload: { education: response.data.education }
            });
            dispatch({
                type: 'UPDATE_USER_SCHOOL_NAMES',
                payload: {
                    user_school_names: response.data.user_school_names,
                }
            });
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { saveProfileEducationPending: false }
            });

            callback();

        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { saveProfileEducationPending: false }
            });

            callback();
        });
    }
}

export const getProfileClaimToFame = (user_id = '') => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { getProfileClaimToFamePending: true, }
        });

        axios({
            url: '/ajax/getProfileClaimToFame/' + user_id,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_CLAIM_TO_FAME_SECTION',
                payload: { getProfileClaimToFamePending: false, response: response.data }
            });
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { getProfileClaimToFamePending: false }
            });
        });
    }
}

export const saveClaimToFameSection = (data, callback) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { saveClaimToFameSectionPending: true, }
        });

        axios({
            url: '/ajax/profile/saveClaimToFameSection',
            method: 'POST',
            data: { ...data },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:SAVED_CLAIM_TO_FAME_SECTION',
                payload: { saveClaimToFameSectionPending: false, data: data }
            });

            callback();

        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { saveClaimToFameSectionPending: false }
            });

            callback();
        });
    }
}

export const getSkillsAndEndorsements = (user_id = '') => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { getSkillsAndEndorsementsPending: true, }
        });

        axios({
            url: '/ajax/getSkillsAndEndorsements/' + user_id,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_SKILLS_AND_ENDORSEMENTS_SECTION',
                payload: { getSkillsAndEndorsementsPending: false, response: response.data }
            });
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { getSkillsAndEndorsementsPending: false }
            });
        });
    }
}

export const saveSkillsAndEndorsements = (data, callback) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { saveSkillsAndEndorsementsSectionPending: true, }
        });

        axios({
            url: '/ajax/profile/saveSkillsAndEndorsements',
            method: 'POST',
            data: { ...data },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_SKILLS_AND_ENDORSEMENTS_SECTION',
                payload: { getSkillsAndEndorsementsPending: false, response: response.data.skills }
            });
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { saveSkillsAndEndorsementsSectionPending: false }
            });

            //callback();

        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { saveSkillsAndEndorsementsSectionPending: false }
            });

            callback();
        });
    }
}

export const getProjectsAndPublications = (user_id = '') => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { getProjectsAndPublicationsPending: true, }
        });

        axios({
            url: '/ajax/getProjectsAndPublications/' + user_id,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { getProjectsAndPublicationsPending: false, projectsAndPublications: response.data }
            });
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { getProjectsAndPublicationsPending: false, projectsAndPublications: [] }
            });
        });
    }
}

export const insertPublicProfilePublication = ({ title, url, callback}) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { addPublicationPending: true, }
        });

        axios({
            url: '/ajax/insertPublicProfilePublication',
            method: 'POST',
            data: { title, url },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_PUBLICATIONS',
                payload: { addPublicationPending: false, newPublication: response.data }
            });

            callback && callback();
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { addPublicationPending: false, addPublicationStatus: response.data }
            });

            callback && callback();
        })
    }
}

export const removePublicProfilePublication = (publication_id) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { removePublicationPending: true, }
        });

        axios({
            url: '/ajax/removePublicProfilePublication',
            method: 'POST',
            data: { publication_id },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:REMOVE_PUBLICATION',
                payload: { publication_id, removePublicationPending: false, removePublicationStatus: response.data }
            });

        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { removePublicationPending: false, removePublicationStatus: response.data }
            });

        })
    }
}

export const removeLikedCollege = (college) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { removeLikedCollegePending: true, }
        });

        axios({
            url: '/ajax/removeLikedCollege',
            method: 'POST',
            data: { likes_tally_id: college.likes_tally_id },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_LIKED_COLLEGES',
                payload: { removeLikedCollegePending: false, college, removeLikedCollegeStatus: response.data }
            });
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_LIKED_COLLEGES',
                payload: { removeLikedCollegePending: false, college, removeLikedCollegeStatus: response.data }
            });
        });
    }
}

export const resetSaved = () => {
	return {
		type: '_PROFILE:RESET_SAVED',
		payload: {
			coming_from: '',
			save_error: null,
			save_success: null,
			save_pending: false,
			unqualified_modal: null,
			confirmation_sent_error:false,
			confirmation_sent_success: false,
			verify_confirmation_code_success: false,
		}
	}	
}

export const pageChanged = (page) => {
	return {
		type: '_PROFILE:CHANGED_FIELDS',
		payload: {currentPage: page}
	}
}

export const getGPAGradingScales = (country_id) => {
	return (dispatch) => {
		dispatch({
			type: '_PROFILE:INIT_GRADING_SCALES',
			payload: { 
				grading_scales_pending: true,
				country_grading_scales: null,
				gpa_applicant_scale: null,
			},
		});

		axios({
			url: '/ajax/getGPAGradingScales/' + country_id,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
		})
		.then((response) => {
			dispatch({
				type: '_PROFILE:INIT_GRADING_SCALES',
				payload: {
					country_grading_scales: response.data,
					grading_scales_pending: false,
				}
			});
		});
	}
}

export const convertToUnitedStatesGPA = (gch_id, old_value, conversion_type) => {
	return (dispatch, getState) => {
		dispatch({
			type: '_PROFILE:INIT_GRADING_SCALES',
			payload: { 
				grading_conversion_pending: true,
			},
		});

		axios({
			url: '/ajax/convertToUnitedStatesGPA/' + gch_id + '/' + old_value + '/' + conversion_type,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
		})
		.then((response) => {
			// Force US GPA to have a absolute minimum at 0.1
			const new_gpa = response.data > 0.1 ? response.data : 0.1,
				{ _profile } = getState();

			// Verify that the user's value is the same as what is being converted before dispatching.
			// This is because the front end is converting on the fly (on change) when a new valid value is entered
			if (old_value == _profile.gpa_applicant_value) {
				dispatch({
					type: '_PROFILE:VALIDATE_PAGE',
					payload: {
						gpa: new_gpa.toFixed(2),
						grading_conversion_pending: false
					}
				});
			}
		})
		.catch((response) => {
			dispatch({
				type: '_PROFILE:INIT_GRADING_SCALES',
				payload: {
					grading_conversion_pending: false
				}
			});
		});
	}
}

// user_id is only set when super_user is trying to getProfileData of another user
export const getProfileData = (user_id = '') => {
	const scholarshipFlow = window.location.href.includes('?isScholarship') ? true : false;

	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:INIT_PROFILE_PENDING',
	 		payload: {init_profile_pending: true},
	 	});

		axios.get('/ajax/getProfileData/'+user_id)
			 .then((response) => {
			 	dispatch({
					type: '_PROFILE:INIT',
					payload: {
						init_done: true,
						...response.data,
						init_pending: false,
						init_profile_pending: false,
						isScholarshipFlow: scholarshipFlow
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {
			 			init_pending: false,
			 			init_profile_pending: false,
			 		},
			 	});
			 });
	}
}

export const getProfileDataLists = () => {

    return (dispatch) => {
        dispatch({
            type: '_PROFILE:INIT_PROFILE_PENDING',
            payload: {init_profile_pending: true},
        });

        axios.get('/ajax/oneapp/getDataFor/applicationAndMycolleges')
             .then((response) => {
                dispatch({
                    type: '_PROFILE:INIT',
                    payload: {
                        init_done: true,
                        ...response.data,
                        init_pending: false,
                        init_profile_pending: false,
                    }
                });
             })
             .catch((err) => {
                dispatch({
                    type: '_PROFILE:ERR',
                    payload: {
                        init_pending: false,
                        init_profile_pending: false,
                    },
                });
             });
    }
}



//slimmed down version of getProfileData
// user_id is only set when super_user is trying to getProfileData of another user
export const getStudentProfile = (user_id = '') => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:INIT_PROFILE_PENDING',
	 		payload: {init_profile_pending: true},
	 	});

		axios.get('/ajax/getStudentProfile/'+user_id)
			 .then((response) => {


			 	dispatch({
					type: '_PROFILE:INIT',
					payload: {
						init_done: true,
						...response.data,
						init_pending: false,
						init_profile_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {
			 			init_pending: false,
			 			init_profile_pending: false,
			 		},
			 	});
			 });
	}
}



// Gets the user submitted scholarships
export const getScholarships = () => {
	return (dispatch) => {

		dispatch({
			type:"_PROFILE:INIT_SCHOLARSHIPS_PENDING",
			payload: {
				init_scholarships_pending: true
			}
		})

		axios({
			url: '/ajax/getUserSubmitScholarships',
			type: 'GET',
			// params: {user_id: uid},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
		})
		.then((response) => {
			dispatch({
				type: '_PROFILE:INIT_SCHOLARSHIPS_DONE',
				payload: {
					
					scholarshipsList: response.data,
					init_scholarships_pending: false
				}
			});
		})
		.catch((err) => {
			dispatch({
				type: '_PROFILE:ERR',
				payload: {init_scholarships_pending: false},
			});
		});
	}
}

// Gets all possible scholarships
export const getAllScholarships = () => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { getAllScholarshipsPending: true },
        });

        axios({
            url: '/ajax/getAllScholarships',
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
        .then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { allScholarships: response.data, getAllScholarshipsPending: false },
            });
        })
        .catch((err) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { allScholarships: [], getAllScholarshipsPending: false },
            })
        })
    }
}

// Gets all scholarships the user has not applied to
export const getAllScholarshipsNotApplied = () => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { getAllScholarshipsNotAppliedPending: true },
        });

        axios({
            url: '/ajax/getAllScholarshipsNotApplied',
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
        .then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { allScholarshipsNotApplied: response.data, getAllScholarshipsNotAppliedPending: false },
            });
        })
        .catch((err) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { allScholarshipsNotApplied: [], getAllScholarshipsNotAppliedPending: false },
            })
        })
    }
}

// Gets all scholarships the user has not submitted to
export const getAllScholarshipsNotSubmitted = () => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { getAllScholarshipsNotSubmittedPending: true },
        });

        axios({
            url: '/ajax/getAllScholarshipsNotSubmitted',
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
        .then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { allScholarshipsNotSubmitted: response.data, getAllScholarshipsNotSubmittedPending: false },
            });
        })
        .catch((err) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { allScholarshipsNotSubmitted: [], getAllScholarshipsNotSubmittedPending: false },
            })
        })
    }
}

export const toggleSelectScholarship = (scholarship) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:TOGGLE_SELECTED_SCHOLARSHIP',
            payload: { scholarship },
        });
    }    
}

export const toggleExamReporting = (data) => {
	return {
		type: '_PROFILE:TOGGLE_EXAM_REPORTING',
		payload: {data}
	}
}

export const getMajors = () => {
	return (dispatch) => {
		axios({
			url: '/ajax/getAllMajors',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
		})
		.then((response) => {
			dispatch({
				type: '_PROFILE:INIT_MAJORS',
				payload: {
					init_majors_done: true,
					majors_list: response.data,
					init_majors_pending: false,
				}
			});
		})
		.catch((err) => {
			dispatch({
				type: '_PROFILE:ERR',
				payload: {init_majors_pending: false},
			});
		});
	}
}


//////////////////////////////////////////////////////////////////
// auto complete for occupations field
export const searchForJobs = (term) => {
	return (dispatch) => {
		axios.post('/get_started/searchFor/career',{input: term})
		.then((response) => {

			let jobs = response.data;

		 	if(typeof response.data === "string")
		 		jobs = null;
		 	
			dispatch({
				type: '_PROFILE:SEARCH_JOBS',
				payload: {
					init_jobss_done: true,
					jobs_list: jobs,
					init_jobs_pending: false,
				}
			});
		})
		.catch((err) => {
			dispatch({
				type: '_PROFILE:ERR',
				payload: {init_jobs_pending: false},
			});
		});

	}
}

let sfm_cancel;
export const searchForMajors = (term) => {

	return (dispatch) => {
        sfm_cancel && sfm_cancel();
		axios({
            method: 'post',
            url: '/ajax/searchForMajors',
            data: {input: term},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            cancelToken: new CancelToken(cancel => sfm_cancel = cancel),
        })
		.then((response) => {

			dispatch({
				type: '_PROFILE:SEARCH_MAJORS',
				payload: {
					init_majors_done: true,
					majors_list: response.data,
					init_majors_pending: false,
				}
			});
		})
		.catch((err) => {
			dispatch({
				type: '_PROFILE:ERR',
				payload: {init_majors_pending: false},
			});
		});

	}


}

export const getSchoolAdditionalQuestions = (college) => {
	return {
		type: '_PROFILE:SCHOOL_ADDITIONAL_QUESTIONS',
		payload: { additional_questions_pending: true, college }
	}
}

export const changeCollegeAdditionalPage = (college, movement) => {
	return {
		type: '_PROFILE:CHANGE_COLLEGE_ADDITIONAL_PAGE',
		payload: { college, movement, page_update_pending: true }
	}
}

export const getAttendedSchools = () => {
	return (dispatch) => {
		axios.get('/ajax/getAttendedSchools')
			 .then((response) => {
			 	dispatch({
					type: '_PROFILE:INIT_ATTENDED_SCHOOLS',
					payload: {
						init_schools_attended_done: true,
						schools_attended_list: response.data,
						init_schools_attended_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_schools_attended_pending: false},
			 	});
			 });
	}
}

export const findSchools = (input) => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:PENDING',
	 		payload: input,
	 	});

		axios.post('/ajax/findAllSchools', input)
			 .then((response) => {

			 	let schools = response.data;

			 	if(typeof response.data === "string")
			 		schools = null;	

			 	dispatch({
					type: '_PROFILE:SEARCH_SCHOOLS',
					payload: {
						schools_searched: schools,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_schools_pending: false},
			 	});
			 });
	}
}


////////////////////////////////////////////////////////
// get school names autocomplete function
export const getSchoolNames = (term, level) => {

	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:PENDING',
	 		payload: term
	 	});


		let url = ((level === 'hs' || level === 0) ?  '/get_started/searchFor/college_hs' : '/get_started/searchFor/college_college');

		axios.post(url, term)
			 .then((res) => {
			 	let schools = res.data;

			 	if(typeof res.data === "string")
			 		schools = null;

			 	dispatch({
					type: '_PROFILE:SEARCH_SCHOOL_NAME',
					payload: {
						school_names: schools,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_schools_pending: false},
			 	});
			 });
	}
}

export const getSchoolsBasedOnSchoolType = (input) => {
	return (dispatch) => {
		axios({
				method: 'post',
				url: '/ajax/findSchools',
				data: input,
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		  	})
			 .then((response) => {
			 	dispatch({
					type: '_PROFILE:SEARCH_SCHOOLS',
					payload: {
						list_of_schools_for_schoolName: response.data,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_schools_pending: false},
			 	});
			 });
	}
}

export const searchForCollegesWithLogos = (query) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: {
                searchForCollegesWithLogosPending: true,
            }
        });

        axios({
            method: 'post',
            url: '/ajax/searchCollegesWithLogos',
            data: { query },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
         .then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: {
                    searchForCollegesWithLogosPending: false,
                    collegesWithLogos: response.data,
                }
            });
         })
         .catch((err) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: {
                    searchForCollegesWithLogosPending: false,
                    collegesWithLogos: [],
                }
            });
        });
    }
}

export const getStates = () => {
	return (dispatch) => {
		axios.get('/ajax/getAllStates')
			 .then((response) => {
			 	dispatch({
					type: '_PROFILE:INIT_STATES',
					payload: {
						init_states_done: true,
						states_list: response.data,
						init_states_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_states_pending: false},
			 	});
			 });
	}
}

export const getCountries = () => {
	return (dispatch) => {
		
		axios.get('/ajax/getCountriesWithNameId')
			 .then((response) => {
			 	dispatch({
					type: '_PROFILE:INIT_COUNTRIES',
					payload: {
						init_countries_done: true,
						countries_list: response.data,
						init_countries_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_countries_pending: false},
			 	});
			 });
	}
}

export const getLanguages = () => {
	return (dispatch) => {
		axios.get('/ajax/getAllLanguages')
			 .then((response) => {
			 	dispatch({
					type: '_PROFILE:INIT_LANGUAGES',
					payload: {
						init_languages_done: true,
						languages_list: response.data,
						init_languages_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_languages_pending: false},
			 	});
			 });
	}
}

export const verifyPhone = (phone, phone_code) => {
	return (dispatch) => {
		var pkey = _.findKey(phone, () => true),
			ckey = _.findKey(phone_code, () => true),
			full_phone = phone_code[ckey] + phone[pkey];

		dispatch({
	 		type: '_PROFILE:VERIFY_PHONE_PENDING',
	 		payload: {
	 			verify_phone_pending: true,
	 			...phone,
				...phone_code,
	 		},
	 	});

			axios({
				method: 'post',
				url: '/phone/validatePhoneNumber',
				data: {phone: full_phone},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		  	})
			.then((res) => {
			 	dispatch({
					type: '_PROFILE:PHONE_VERIFIED',
					payload: {
						verify_phone_pending: false,
						[pkey+'_error']: selectn('data.error', res),
					}
				});
			})
			.catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_countries_pending: false},
			 	});
			});
	}
}

export const getGradeConversionCountries = (name) => {
	return (dispatch) => {
		axios.post('/ajax/getGradeConversions', { name })
			 .then((response) => {
			 	dispatch({
					type: '_PROFILE:GET_GRADE_CONVERSIONS',
					payload: {
						grade_conversion_countries_list: response.data,
						init_grade_conversion_countries_done: true,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_countries_pending: false},
			 	});
			 });
	}
}



// //////////////////////////////////////////////////////////////
// // Amplitude: send event for adding college
// export const amplitudeAddCollege = (college) => {
// 	amplitude.getInstance().logEvent('college from app added', {"college": college.school_name, "college id": college.college_id});

// }


// //////////////////////////////////////////////////////////////
// // Amplitude: send event for removing college
// export const amplitudeRemoveCollege = (college) => {
// 	amplitude.getInstance().logEvent('college from app removed', {"college": college.school_name, "college id": college.college_id});

// }


///////////////////////////////////////////////////////////////////////
//send event to amplitude if first time completing a step in the OneApp
//first time -> _profile.currentPage <= step
export const AmplitudeSaveApplication = (step, currentPage, description) => {

	//structure(APP_ROUTES) in constants.js for SIC storing app steps
	//get index of step and currentPage and compare
	let index = _.findIndex(APP_ROUTES, {id: step });
	let cindex = _.findIndex(APP_ROUTES, {id: currentPage });

	if(step === 'start')
		step = 'planned start';

	if(index >= cindex)
		amplitude.getInstance().logEvent('app ' + step + ' completed', {"Step Number": index+1, 
																		"Description": description,
																		"Previous Page": document.referrer,
                                                                        "Current Page": document.location.href,});
}




///////////////////////////////////////////////////////////////////
// save Me Section
export const saveMeSection = (data, callback) => {

	return (dispatch) => {

		dispatch({
			type: '_PROFILE:SAVE_ME_START',
			payload: {
				saveMePending: true
			}
		})


		axios.post('/ajax/profile/saveMeTab', data)
		.then((res)=>{
			dispatch({
				type: '_PROFILE:SAVE_ME_DONE',
				payload: {
					...data,
					saveMePending: false
				}
			})
			callback();

			//CORRECT--- but not implemented on backend yet	
			// if(res.data.status && res.data.status === "success"){

			// 	dispatch({
			// 		type: '_PROFILE:SAVE_ME_DONE',
			// 		payload: {
			// 			...data, 
			// 			saveMePending: false
			// 		}
			// 	})

			//	callback();	
			// }
			// else{
			// 	dispatch({
			// 		type: '_PROFILE:SAVE_ME_ERR',
			// 		payload: {
			// 			saveMePending: false
			// 		}
			// 	})
			// }
		})
		.catch((err) => {

			dispatch({
				type: '_PROFILE:SAVE_ME_ERR',
				payload: {
					saveMePending: false
				}
			})
		})
	}
}



// /////////////////////////////////////////////////////////
// // save scholarships
// export const saveScholarships = (scholarshipList) => {

// 	axios({ url: '/ajax/saveCollegeApplication', 
// 			data: {scholarships: scholarshipList, page: 'scholarship-submission'},
// 			method: 'post',
// 			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
// 		})
// 		.then((res) => {

// 		})
// }

//////////////////////////////////////////////////////////////////////
// save application sections to database
export const saveApplication = (data, step, currentPage) => {

	return (dispatch, getState) => {
		dispatch({
	 		type: '_PROFILE:SAVE_PENDING',
	 		payload: {save_pending: true},
	 	});


		//will send form changes to Amplitude -- form changes stored in state
		let state = getState();
		let description = state._profile.changedFields;
		data["page"] = step
        console.log("-------data----", data);
		axios({
			  method: 'post',
			  url: '/ajax/saveCollegeApplication',
			  data: data,
			  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		  	})
			.then((res) => {

			 	var ret = res.data;
			 	var new_data = ret.new_data || {};
			 	var pay = {
					save_pending: false,
                    init_profile_pending: false,
					profile_percent: ret.profile_percent,
					save_success: ret.status === 'success',
					save_err_msg: ret.error_msg || '',
					app_last_updated: moment().format('M/D/YYYY @ h:ma'),
					changedFields: [],
					...new_data
				};

                //send save event to Amplitude
                AmplitudeSaveApplication(step, currentPage, description);

			 	dispatch({
					type: '_PROFILE:APPLICATION_SAVED',
					payload: pay 
				});

			})
			.catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:APPLICATION_SAVE_ERR',
			 		payload: {
			 			save_error: true,
			 			save_pending: false,
			 		},
			 	});
			 });
	}
}

export const addCollegeToMyCollegesList = (college) => {

	return (dispatch, getState) => {
		dispatch({
			type: '_PROFILE:ADD_COLLEGE_TO_MY_COLLEGES_LIST',
			payload: {
				college: college
			}
		})
	}	
}

export const addCollegeToMyApplicationList = (college) => {

	return (dispatch, getState) => {
		dispatch({
			type: '_PROFILE:ADD_COLLEGE_TO_MY_APPLICATION_LIST',
			payload: {
				college: college
			}
		})
	}	
}

export const removeCollegeFromMyApplicationsList = (college) => {
	return (dispatch, getState) => {
		dispatch({
			type: '_PROFILE:REMOVE_COLLEGE_FROM_MY_APPLICATIONS_LIST',
			payload: {
				college: college
			}
		})
	}
 }

export const removeCollegeFromMyCollegesList = (college) => {

    return (dispatch, getState) => {
        dispatch({
            type: '_PROFILE:REMOVE_COLLEGE_FROM_MY_COLLEGES_LIST',
            payload: {
                college: college
            }
        })
    }   
}

export const toggleAdditionalInfoColleges = (activeCollege) => {
	return (dispatch) => {
		dispatch({
			type: '_PROFILE:TOGGLE_ADDITIONAL_INFO_COLLEGE',
			payload: {...activeCollege}
		})
	}
}	

export const saveAdditionalInfoPage = (data, college, movement, step, currentPage) => {
	return (dispatch, getState) => {
		dispatch({
	 		type: '_PROFILE:SAVE_PENDING',
	 		payload: {save_pending: true},
	 	});

	 	//will send form changes to Amplitude -- form changes stored in state
		let state = getState();
		let description = state._profile.changedFields;	

		axios({
			  method: 'post',
			  url: '/ajax/saveCollegeApplication',
			  data: data,
			  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		  	})
			 .then((res) => {
			 	var ret = res.data;
			 	var result = {
					save_pending: false,
					profile_percent: ret.profile_percent,
					save_err_msg: ret.error_msg || '',
					app_last_updated: moment().format('M/D/YYYY @ h:ma')
				};

                AmplitudeSaveApplication(step, currentPage, description);

			 	dispatch({
					type: '_PROFILE:CHANGE_COLLEGE_ADDITIONAL_PAGE',
					payload: { result, college, movement } 
				});

			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:APPLICATION_SAVE_ERR',
			 		payload: {
			 			save_error: true,
			 			save_pending: false,
			 		},
			 	});
			 });
	}
}

export const saveUploadedFilesThenApplication = (transcript_form, additional_form) => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:SAVE_PENDING',
	 		payload: { upload_pending: true, save_pending: true },
	 	});

		$.ajax({
			url: '/ajax/saveUploadedFiles',
			type: 'POST',
			data: transcript_form,
			enctype: 'multipart/form-data',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			contentType: false,
	    	processData: false,
	    	success: (data) => {
	    		dispatch({
			 		type: '_PROFILE:UPDATE_TRANSCRIPTS',
			 		payload: { transcripts: data, upload_pending: false },
			 	});

				axios({
					  method: 'post',
					  url: '/ajax/saveCollegeApplication',
					  data: additional_form,
					  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				  	})
					 .then((res) => {
					 	var ret = res.data;
					 	var pay = {
							save_pending: false,
							profile_percent: ret.profile_percent,
							save_success: ret.status === 'success',
							save_err_msg: ret.error_msg || '',
							changedFields: [],
							app_last_updated: moment().format('M/D/YYYY @ h:ma')
						};

					 	dispatch({
							type: '_PROFILE:APPLICATION_SAVED',
							payload: pay 
						});
					 })
					 .catch((err) => {
					 	dispatch({
					 		type: '_PROFILE:APPLICATION_SAVE_ERR',
					 		payload: {
					 			save_error: true,
					 			save_pending: false,
					 		},
					 	});
					 });
		    }
    	});
	}
}

export const uploadProfilePicture = (form, callback) => {
    return (dispatch) => {
        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { uploadProfilePicturePending: true },
        });

        $.ajax({
            url: '/ajax/profile/uploadProfilePicture',
            type: 'POST',
            data: form,
            enctype: 'multipart/form-data',
            contentType: false,
            processData: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: (data) => {
                dispatch({
                    type: '_PROFILE:SAVED_PROFILE_PICTURE',
                    payload: { uploadProfilePicturePending: false, data: data },
				});
				dispatch({
					type: 'UPDATE_PROFILE_IMG',
					payload: data,
				})

                callback && callback();
            }
        });
    }
}

export const saveUploadedFiles = (form) => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:SAVE_PENDING',
	 		payload: { upload_pending: true },
	 	});

		$.ajax({
			url: '/ajax/saveUploadedFiles',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
	    	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	    	success: (data) => {
	    		dispatch({
			 		type: '_PROFILE:UPDATE_TRANSCRIPTS',
			 		payload: { transcripts: data, upload_pending: false },
			 	});
	    	}
    	});
	}
}



///////////////////////////////////////////////////////
// get current uploads 
export const getUploads = () => {

	return (dispatch) => {
		dispatch({
			type: '_PROFILE:GET_UPLOADS_START',
			payload: { getUploadsPending: true }
		});


		axios.get('/ajax/profile/getUserTranscript')
		.then((res) => {

			dispatch({
				type: '_PROFILE:GET_UPLOADS_DONE',
				payload: { 
					transcript: res.data,
					getUploadsPending: false }
			});
		})
		.catch((err) => {

			dispatch({
				type: '_PROFILE:GET_UPLOADS_ERR',
				payload: { getUploadsPending: false }
			});
		})
	}
}


export const uploadAFile = (form, callback) => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:SAVE_PENDING',
	 		payload: { upload_pending: true },
	 	});

		$.ajax({
			url: '/ajax/saveUploadedFiles',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
	    	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	    	success: (data) => {


	    		if(data[0].transcript_id != '')
	    			callback(data[0].doc_type);

	    		dispatch({
			 		type: '_PROFILE:UPDATE_TRANSCRIPTS',
			 		payload: { transcripts: data, upload_pending: false },
			 	});
	    	}
    	});
	}
}


//////////////////////////////////////////////////////////
// upload a file specifically with data needed for me
export const uploadAFileMePage = (form, callback) => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:SAVE_PENDING',
	 		payload: { upload_pending: true },
	 	});

		$.ajax({
			url: '/ajax/saveUploadedFiles',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
	    	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	    	success: (data) => {

	    		if(data[0].transcript_id != '')
	    			callback(data[0].doc_type);

	    		dispatch({
			 		type: '_PROFILE:UPDATE_TRANSCRIPTS_MEPAGE',
			 		payload: { transcript: data[0], upload_pending: false },
			 	});
	    	}
    	});
	}
}



export const saveApplicationWithFiles = (form, step, currentPage) => {
	return (dispatch, getState) => {
		dispatch({
	 		type: '_PROFILE:SAVE_PENDING',
	 		payload: { save_pending: true },
	 	});

		//will send form changes to Amplitude -- form changes stored in state
		let state = getState();
		let description = state._profile.changedFields;	

	 	$.ajax({
			url: '/ajax/saveCollegeApplication',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        	success: (data) => {
                
                AmplitudeSaveApplication(step, currentPage, description);

				dispatch({
					type: '_PROFILE:APPLICATION_SAVED',
					payload: {
						save_success: true,
						save_pending: false,
						profile_percent: data.profile_percent,
						changedFields: [],
						app_last_updated: moment().format('M/D/YYYY @ h:ma')
					}
				});

        	},
        	error: (err) => {
				dispatch({
			 		type: '_PROFILE:APPLICATION_SAVE_ERR',
			 		payload: {
			 			save_error: true,
			 			save_pending: false,
			 		},
			 	});
        	}
		});
	}
}

export const sendConfirmationCode = (data) => {
	return (dispatch) => {
		var phoneNum = {
			phone: data.phone || '',
			dialing_code: data.phone_code || '',
		};
		
		dispatch({
	 		type: '_PROFILE:SEND_CONF_CODE_PENDING',
	 		payload: {confirmation_sent_pending: true},
	 	});

		axios.post('/get_started/sendPhoneConfirmation', phoneNum)
			 .then((res) => {
			 	var _data = null;
			 	try {
			 		_data = JSON.parse(res.data);
			 	} catch(e) {
			 		// Makes sure the response data string is an object.
			 		let parsed_stringify = res.data.match(/{.*}/) && res.data.match(/{.*}/).join('');
			 		_data = JSON.parse(parsed_stringify);
			 	}

			 	dispatch({
					type: '_PROFILE:CONFIRMATION_CODE_SENT',
					payload: {
						confirmation_sent_pending: false,
						confirmation_sent_err_msg: _data.error_message || '',
						confirmation_sent_error: _data.response === 'failed',
						confirmation_sent_success: _data.response === 'success',
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:CONFIRMATION_CODE_SENT_ERR',
			 		payload: {
			 			confirmation_sent_error: true,
			 			confirmation_sent_pending: false,
			 		},
			 	});
			 });
	}
}

export const verifyConfirmationCode = (code) => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:VERIFY_CONF_CODE_PENDING',
	 		payload: {confirmation_sent_pending: true},
	 	});

		axios.post('/get_started/checkPhoneConfirmation', {code})
			 .then((res) => {
			 	dispatch({
					type: '_PROFILE:CONFIRMATION_CODE_VERIFIED',
					payload: {
						confirmation_sent_pending: false,
						confirmation_sent_err_msg: res.data.error_message || '',
						confirmation_sent_error: res.data.response === 'failed',
						verify_confirmation_code_success: res.data.response === 'success',
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:CONFIRMATION_CODE_SENT_ERR',
			 		payload: {
			 			confirmation_sent_error: true,
			 			confirmation_sent_pending: false,
			 		},
			 	});
			 });
	}
}


//////////////////////////////////////////////////////////////////////
// remove an upload on the me pages
export const removeUploadMePage = (transcriptID) => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:ME_REMOVE_UPLOAD_PENDING',
	 		payload: {remove_pending: true},
	 	});

		axios.post('/ajax/profile/uploadcenter/undefined', {TransId: transcriptID, postType: "transcriptremove"})
			 .then((res) => {
			 	

			 	if(res.data.msg === "Transcript deleted successfully!"){
				 	dispatch({
						type: '_PROFILE:ME_REMOVED_UPLOAD_DONE',
						payload: {
							transcriptID: transcriptID,
							remove_pending: false,
						}
					});



				}else{
					dispatch({
				 		type: '_PROFILE:ME_REMOVE_UPLOAD_ERR',
				 		payload: {
							remove_pending: false,
				 		}
				 	});
				}


			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:ME_REMOVE_UPLOAD_ERR',
			 		payload: {
						remove_pending: false,
			 		},
			 	});
			 });
	}
}


export const removeUpload = (transcript_id, transcripts) => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:REMOVE_PENDING',
	 		payload: {remove_pending: true},
	 	});

		axios.post('/college-application/removeTranscriptAttachment', {transcript_id})
			 .then((res) => {
			 	dispatch({
					type: '_PROFILE:REMOVED_UPLOAD',
					payload: {
						...transcripts,
						remove_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:REMOVE_PENDING',
			 		payload: {
						remove_pending: false,
			 		},
			 	});
			 });
	}
}

export const getCourseSubjects = () => {
	return (dispatch) => {
		axios({
			url: '/ajax/getCoursesSubjects/',
			type: 'GET',
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        })
		.then((res) => {
			dispatch({
				type: '_PROFILE:GET_COURSE_SUBJECTS',
				payload: {
					init_course_subjects_done: true,
					get_course_subjects_pending: false,
					subjects_list: res.data,
				}
			});
		})
		.catch((err) => {
			console.log('error course: ', err);
			dispatch({
				type: '_PROFILE:GET_COURSE_SUBJECTS',
				payload: {
				get_course_subjects_pending: false,
				},
			});
		});
	}
}

export const getReligions = () => {
	return (dispatch) => {
		axios({
			url: '/ajax/getAllReligionsCustom/',
			type: 'GET',
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        })
		.then((res) => {
			dispatch({
				type: '_PROFILE:GET_RELIGION_DONE',
				payload: {
					init_religion_done: true,
					get_religion_pending: false,
					religion_list: res.data,
				}
			});
		})
		.catch((err) => {
			dispatch({
				type: '_PROFILE:GET_ETHNICITIES_DONE',
				payload: {
				get_religion_pending: false,
				},
			});
		});
	}
}


export const getAllReligions = () => {
    return (dispatch) => {
        axios({
            url: '/ajax/getAllReligions/',
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_RELIGIONS',
                payload: { fetched_religions: true, religions_list: response.data }
            });
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_RELIGIONS',
                payload: { fetched_religions: false, religions_list: [] }
            });
        });
    }
}


export const getAllEthnicities = () => {
    return (dispatch) => {
        axios({
            url: '/ajax/getAllEthnicities/',
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_ETHNICITIES',
                payload: { fetched_ethnicities: true, ethnicities_list: response.data }
            });
        }).catch((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_ETHNICITIES',
                payload: { fetched_ethnicities: false, ethnicities_list: [] }
            });
        });
    }
}

// data  = { college_id, ro_id }
export const selectCollegeLearnMore = (data) => {
    // Add utm_source
    data.utm_source = 'plexuss_oneapp_cta_learnmore-blue';

    return (dispatch, getState) => {
        const state = getState();
        const collegeList = state._profile.list;
        const college = _.find(collegeList, { college_id: data.college_id });

        dispatch({
            type: '_PROFILE:UPDATE_DATA',
            payload: { selectCollegeLearnMorePending: true, },
        });

        axios({
            url: '/ajax/homepage/saveGetStartedThreeCollegesPins',
            method: 'POST',
            data: data,
        })
        .then((response) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { 
                    selectCollegeLearnMoreResponse: {...response.data, ...{college}}, 
                    selectCollegeLearnMorePending: false,
                },
            });
        })
        .catch((error) => {
            dispatch({
                type: '_PROFILE:UPDATE_DATA',
                payload: { 
                    selectCollegeLearnMoreResponse: { status: 'failed' }, 
                    selectCollegeLearnMorePending: false,
                },
            });
        });
    }
    
}

export const getClassesBasedOnSubject = (subject_id) => {
	return (dispatch) => {
		axios({
			url: '/ajax/getClassesBasedOnSubjects/'+subject_id,
			type: 'GET',
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        })
		.then((res) => {
			dispatch({
				type: '_PROFILE:GET_CLASSES_BASED_ON_SUBJECT',
				payload: {
					['course_list_for_subject_'+subject_id]: res.data,
				}
			});
		})
		.catch((err) => {
			dispatch({
				type: '_PROFILE:GET_CLASSES_PENDING',
				payload: {
				get_course_subjects_pending: false,
				},
			});
		});
	}
}

export const removeCourse = (course_table_id, current_schools) => {
	return (dispatch) => {
		axios.post('/ajax/removeCourse', {course_table_id})
			 .then((res) => {
			 	dispatch({
					type: '_PROFILE:REMOVED_COURSE',
					payload: {
						...current_schools,
						remove_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_PROFILE:REMOVE_PENDING',
			 		payload: {
						remove_pending: false,
			 		},
			 	});
			 });
	}
}


/******************************************************
* updatees a profile entry on the backend - aritrary fields
* params: data  =  object {'field': 'value', 'field2': 'value2'}  representing fields to update and the value to update it to
*
*****************************************************/
export const updateStudentProfile = (data) => {

	return (dispatch) => {

		disptch({
			type: '_PROFILE:UPDATE_PROFILE_PENDING',
			payload: {
				update_profile_pending: true,
			}
		})


		axios.post('/ajax/updateStudentProfile', data)
		.then((res) => {

			if(res.data === null){

				disptch({
					type: '_PROFILE:UPDATE_PROFILE_ERR',
					payload: {
						update_profile_pending: false,
					}
				})
				return;
			}

			dispatch({
				type: '_PROFILE:UPDATE_ROFILE_DONE',
				payload: {
					...res.data,
					update_profile_pending: false
				}
			});




		})
		.catch((err) => {

			disptch({
				type: '_PROFILE:UPDATE_PROFILE_ERR',
				payload: {
					update_profile_pending: false,
				}
			})
		})
	}
}