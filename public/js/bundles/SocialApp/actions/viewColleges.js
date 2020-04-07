export function getViewCollege(payloads){
    return{
        type: "GET_VIEW_COLLEGES",
        payloads,
    }
}

export function deleteViewCollege(selected, single) {
    return{
        type: "DEL_VIEW_COLLEGES",
        selected: selected,
        single: single,
    }
}