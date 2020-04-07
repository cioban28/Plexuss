const initialState ={
    colleges: [],
    loadSpinner: true
}
const favColleges = (state = initialState, action) => {
    switch(action.type){
        case "GET_FAV_COLLEGES":
            return { ...state, colleges: action.payloads, loadSpinner: false }
        case "DEL_FAV_COLLEGES":
            let selectedColleges = action.selected;
            let colleges = Object.assign([], state.colleges);
            selectedColleges.map(sel => {
                let index_co = colleges.findIndex((rec) => rec.college_id == sel);
                colleges.splice(index_co, 1);
            });
            return { ...state, colleges: colleges }
        default:
            return { ...state } 
    }
}

export default favColleges;