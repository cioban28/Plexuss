export function setStep(payload){
    return{
        type: "SET_STEP",
        payload,
    };
}

export function getData(payload, which) {
    return {
        type: "GET_DATA",
        payload: payload,
        which: which,
    }
}

export function setLoadData(payload) {
    return {
        type: "SET_LOAD_DATA",
        payload,
    }
}