const initialState ={
    applications: [],
    loadSpinner: true
}
const applications = (state = initialState, action) => {
    switch(action.type){
        case "GET_APPLICATIONS":
            return { ...state, applications: action.payloads, loadSpinner: false }
        default:
            return { ...state }
    }
}

export default applications;
