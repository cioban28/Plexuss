const initialState ={
    colleges: [],
    loadSpinner: true,
    hasMoreColleges: true,
}
const recColleges = (state = initialState, action) => {
    switch(action.type){
        case "GET_REC_COLLEGES":
            return { ...state, colleges: [...state.colleges, ...action.payloads], loadSpinner: false }
        case "DEL_REC_COLLEGES":
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
        case 'STOP_REC_COLLEGES_INFINITE_SCROLL':
            return { ...state, hasMoreColleges: false };
        default:
            return { ...state }
    }
}

export default recColleges;
