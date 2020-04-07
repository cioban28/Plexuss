const initialState ={
    colleges: [],
    loadSpinner: true
}
const recuColleges = (state = initialState, action) => {
    switch(action.type){
        case "GET_RECU_COLLEGES":
            return { ...state, colleges: action.payloads, loadSpinner: false }
        case "DEL_RECU_COLLEGES":
            let single = action.single;
            let colleges = Object.assign([], state.colleges);
            if (!single) {
                let selectedColleges = action.selected;
                selectedColleges.map(sel => {
                    let index_co = colleges.findIndex((rec) => rec.college_id == sel);
                    colleges.splice(index_co, 1);
                });
            } else {
                let index_co = colleges.findIndex((rec) => rec.college_id == single);
                colleges.splice(index_co, 1);
            }
            return { ...state, colleges: colleges }
        default:
            return { ...state } 
    }
}

export default recuColleges;