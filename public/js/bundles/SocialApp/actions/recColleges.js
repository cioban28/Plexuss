export function getRecCollege(payloads){
    return{
        type: "GET_REC_COLLEGES",
        payloads,
    }
}

export function deleteRecCollege(selected, single) {
    return{
        type: "DEL_REC_COLLEGES",
        selected: selected,
        single: single,
    }
}

export function stopRecCollegesInfiniteScroll(payload) {
  return {
    type: 'STOP_REC_COLLEGES_INFINITE_SCROLL',
    payload,
  }
}
