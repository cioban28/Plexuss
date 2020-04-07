export function setCurrentStep(payload) {
    return {
        type: 'SET_CURRENT_STEP',
        payload
    }
}

export function setUserInfo(payload) {
    return {
        type: 'SET_USER_INFO',
        payload
    }
}

export function getStepStatus(payload) {
    return {
        type: 'GET_STEP_STATUS',
        payload,
    }
}

export function getStep1Data(payload) {
    return {
        type: 'GET_STEP1_DATA',
        payload,
    }
}

export function getStep2Data(payload) {
    return {
        type: 'GET_STEP2_DATA',
        payload,
    }
}

export function getStep3Data(payload) {
    return {
        type: 'GET_STEP3_DATA',
        payload,
    }
}

export function getStep4Data(payload) {
    return {
        type: 'GET_STEP4_DATA',
        payload,
    }
}

export function getStep5Data(payload) {
    return {
        type: 'GET_STEP5_DATA',
        payload,
    }
}

export function getStep6Data(payload) {
    return {
        type: 'GET_STEP6_DATA',
        payload,
    }
}

export function getStatesData(payload) {
    return {
        type: 'GET_STATES_DATA',
        payload,
    }
}

export function getCountriesData(payload) {
    return {
        type: 'GET_COUNTRIES_DATA',
        payload,
    }
}

export function getUserName(payload) {
    return {
        type: 'GET_USER_NAME',
        payload,
    }
}

export function updateHeader(payload) {
    return {
        type: 'UPDATE_HEADER',
        payload,
    }
}

export function getSchool(payload) {
    return {
        type: 'GET_SCHOOL',
        payload,
    }
}

export function setCaps(payload) {
    return {
        type: 'SET_CAPS',
        payload,
    }
}

export function getGPAGradingScale(payload) {
    return {
        type: 'GET_GPA_GRADING_SCALE',
        payload
    }
}

export function setGPAApplicantScale(payload) {
    return {
        type: 'SET_GPA_APPLICANT_SCALE',
        payload
    }
}

export function getCountry(payload) {
    return {
        type: 'GET_COUNTRY',
        payload
    }
}
