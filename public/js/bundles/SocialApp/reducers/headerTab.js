const initialState ={
    tab: '',
}
const headerTabs = (state = initialState, action) => {
    switch(action.type){
        case "HOME_PAGE":
            return {...state, tab:'home'}
        case "NETWORKING_PAGE":
            return { ...state, tab:'network' }
        case "COLLEGE_PAGE":
            return { ...state, tab:'colleges' }
        case "ME_PAGE":
            return { ...state, tab:'me' }
        default:
            return { ...state } 
    }
}
export default headerTabs;