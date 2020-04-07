import initialState from './initialState';

const carousles = (state = initialState.carousles, action) => {
    switch(action.type){
        case "SET_LOAD_DATA":
            var status = Object.assign({}, state.status)
            status[action.payload] = false;
            return {...state, status: status}
        case "SET_STEP":
            var items = Object.assign({}, state.items)
            var comps = Object.assign([], state.comps)
            comps.push(action.payload)
            items[action.payload] = true
            var hasMoreItems = true;
            if (action.payload === 'feature')
                hasMoreItems = false;
            return {...state, items: items, hasMoreItems: hasMoreItems, comps: comps}
        case "GET_DATA":
            const { payload, which } = action
            var datas = Object.assign({}, state.datas)
            var start = Object.assign({}, state.start)
            var loading = Object.assign({}, state.loading)
            var status = Object.assign({}, state.status)
            status[which] = true;
            start[which] = start[which] + 1
            datas[which] = [...datas[which],  ...action.payload ];
            if (payload.length <= 0)
                loading[which] = false
            return {...state, datas: datas, isLoading: false, start: start, loading: loading, status: status}
        default:
            return { ...state } 
    }
}
export default carousles;