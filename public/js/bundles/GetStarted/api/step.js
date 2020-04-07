import axios from 'axios';
import store from '../../stores/getStartedStore'
import { getStepStatus, getStep1Data, getStep2Data, getStep3Data, 
    getStep4Data, getStep5Data, getStep6Data, getStatesData, 
    getCountriesData, getUserName, getSchool, getGPAGradingScale, getCountry } from '../actions/step'

export const getStepStatuses = (step) => {
    return axios.get('/get_step_status/' + step)
    .then(response => {
        // console.log(response.data)
        store.dispatch(getStepStatus(response.data))
    })
    .catch(error => {
        console.log(error)
    })
}

export const getStepDatas = (step) => {
    return axios.get('/get_started/getDataFor/step' + step)
    .then(response => {
        // console.log(response.data)
        switch(step) {
            case "1":
                store.dispatch(getStep1Data(response.data))
                break;
            case "2":
                store.dispatch(getStep2Data(response.data))
                break;
            case "3":
                store.dispatch(getStep3Data(response.data))
                break;
            case "4":
                store.dispatch(getStep4Data(response.data))
                break;
            case "6":
                store.dispatch(getStep6Data(response.data))
                break;
            case "5new":
                store.dispatch(getStep5Data(response.data))
                break;
        }
    })
    .catch(error => {
        console.log(error)
    })
}

export const getDataFor = (data) => {
    return axios.get('/get_started/getDataFor/' + data)
    .then(response => {
        switch(data) {
            case "states":
                store.dispatch(getStatesData(response.data))
                break;
            case "country":
                store.dispatch(getCountriesData(response.data))
                break;
        }
    })
    .catch(error => {
        console.log(error)
    })
}

export const getUserNames = () => {
    return axios.get('/get_user_name')
    .then(response => {
        store.dispatch(getUserName(response.data))
    })
    .catch(error => {
        console.log(error)
    })
}

export const getSchools = () => {
    return axios.get('/ajax/homepage/getGetStartedThreeCollegesPins')
    .then(response => {
        store.dispatch(getSchool(response.data))
    })
    .catch(error => {
        console.log(error)
    })
}

export const getGPAGradingScales = (country_id) => {
    return axios.get('/ajax/getGPAGradingScales/' + country_id)
    .then(response => {
        store.dispatch(getGPAGradingScale(response.data))
    })
    .catch(error => {
        console.log(error)
    })
}

export const getCountries = () => {
    return axios.get('/ajax/getCountriesWithNameId')
    .then(response => {
        store.dispatch(getCountry(response.data))
    })
    .catch(error => {
        console.log(error)
    })
}